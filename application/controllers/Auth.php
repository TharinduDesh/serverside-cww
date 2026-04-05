<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller
{
    private $allowed_domains = ['westminster.ac.uk', 'gmail.com', 'eastminster.ac.uk'];

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->library('email');
    }

    private function send_email_message($to, $subject, $message)
    {
        $config = [
            'protocol' => 'smtp',
            'smtp_host' => $_ENV['SMTP_HOST'] ?? 'smtp.gmail.com',
            'smtp_port' => (int) ($_ENV['SMTP_PORT'] ?? 465),
            'smtp_user' => $_ENV['SMTP_USER'] ?? '',
            'smtp_pass' => $_ENV['SMTP_PASS'] ?? '',
            'smtp_crypto' => 'ssl',
            'mailtype' => 'html',
            'charset' => 'utf-8',
            'wordwrap' => TRUE,
            'newline' => "\r\n",
            'crlf' => "\r\n"
        ];

        $this->email->initialize($config);

        $this->email->from(
            $_ENV['SMTP_FROM_EMAIL'] ?? '',
            $_ENV['SMTP_FROM_NAME'] ?? 'App'
        );

        $this->email->to($to);
        $this->email->subject($subject);
        $this->email->message($message);

        return $this->email->send();
    }

    public function register()
    {
        $data['title'] = 'Register';

        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('first_name', 'First Name', 'required|trim|min_length[2]|max_length[100]');
            $this->form_validation->set_rules('last_name', 'Last Name', 'required|trim|min_length[2]|max_length[100]');
            $this->form_validation->set_rules('email', 'University Email', 'required|trim|valid_email|callback_email_domain_check|callback_email_not_exists');
            $this->form_validation->set_rules(
                'password',
                'Password',
                'required|trim|min_length[8]|max_length[64]|callback_strong_password_check'
            );
            $this->form_validation->set_rules(
                'confirm_password',
                'Confirm Password',
                'required|trim|matches[password]'
            );

            if ($this->form_validation->run() === TRUE) {
                $email = strtolower(trim($this->input->post('email', TRUE)));

                $userData = [
                    'first_name' => trim($this->input->post('first_name', TRUE)),
                    'last_name' => trim($this->input->post('last_name', TRUE)),
                    'university_email' => $email,
                    'password_hash' => password_hash($this->input->post('password'), PASSWORD_BCRYPT),
                    'role' => 'alumnus',
                    'email_verified' => 0,
                    'is_active' => 1
                ];

                $user_id = $this->User_model->create_user($userData);

                $rawToken = bin2hex(random_bytes(32));
                $tokenHash = hash('sha256', $rawToken);

                $this->User_model->store_verification_token([
                    'user_id' => $user_id,
                    'token_hash' => $tokenHash,
                    'expires_at' => date('Y-m-d H:i:s', strtotime('+1 day'))
                ]);

                $verifyLink = site_url('auth/verify?token=' . $rawToken);

                $message = '
                    <h2>Verify Your Email</h2>
                    <p>Thank you for registering for the Alumni Influencer Platform.</p>
                    <p>Please click the link below to verify your account:</p>
                    <p><a href="' . $verifyLink . '">Verify Email</a></p>
                    <p>This link will expire in 24 hours.</p>
                ';

                $emailSent = $this->send_email_message(
                    $email,
                    'Verify your Alumni Influencer account',
                    $message
                );

                if ($emailSent) {
                    $data['success_message'] = 'Registration successful. A verification email has been sent to your university email address.';
                } else {
                    $data['error_message'] = 'Registration succeeded, but the verification email could not be sent.';
                    // For local debugging only:
                    // $data['error_message'] .= '<br><pre>' . $this->email->print_debugger() . '</pre>';
                }

                $this->load->view('auth/register', $data);
                return;
            }
        }

        $this->load->view('auth/register', $data);
    }

    public function email_domain_check($email)
    {
        $email = strtolower(trim($email));
        $parts = explode('@', $email);

        if (count($parts) !== 2 || !in_array($parts[1], $this->allowed_domains, TRUE)) {
            $this->form_validation->set_message(
                'email_domain_check',
                'You must register using an allowed email address.'
            );
            return FALSE;
        }

        return TRUE;
    }

    public function email_not_exists($email)
    {
        $email = strtolower(trim($email));

        if ($this->User_model->email_exists($email)) {
            $this->form_validation->set_message(
                'email_not_exists',
                'This email is already registered.'
            );
            return FALSE;
        }

        return TRUE;
    }

    public function verify()
    {
        $rawToken = $this->input->get('token', TRUE);

        if (!$rawToken) {
            show_error('Invalid verification link.', 400);
        }

        $tokenHash = hash('sha256', $rawToken);
        $tokenRow = $this->User_model->get_valid_verification_token($tokenHash);

        if (!$tokenRow) {
            show_error('Verification link is invalid, expired, or already used.', 400);
        }

        $this->User_model->mark_email_verified($tokenRow->user_id);
        $this->User_model->mark_verification_token_used($tokenRow->id);

        $this->session->set_flashdata('success_message', 'Email verified successfully. You can now log in.');
        redirect('auth/login');
    }

    public function strong_password_check($password)
    {
        $hasUpper = preg_match('/[A-Z]/', $password);
        $hasLower = preg_match('/[a-z]/', $password);
        $hasNumber = preg_match('/[0-9]/', $password);
        $hasSpecial = preg_match('/[\W_]/', $password);

        if (!$hasUpper || !$hasLower || !$hasNumber || !$hasSpecial) {
            $this->form_validation->set_message(
                'strong_password_check',
                'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.'
            );
            return FALSE;
        }

        return TRUE;
    }

    public function login()
    {
        $data['title'] = 'Login';

        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email');
            $this->form_validation->set_rules('password', 'Password', 'required|trim');

            if ($this->form_validation->run() === TRUE) {
                $email = strtolower(trim($this->input->post('email', TRUE)));
                $password = $this->input->post('password');

                $user = $this->User_model->get_user_by_email($email);

                if (!$user) {
                    $data['error_message'] = 'Invalid email or password.';
                    $this->load->view('auth/login', $data);
                    return;
                }

                if ((int) $user->email_verified !== 1) {
                    $data['error_message'] = 'Please verify your email before logging in.';
                    $this->load->view('auth/login', $data);
                    return;
                }

                // Check if account is locked due to failed login attempts

                if (!empty($user->locked_until) && strtotime($user->locked_until) > time()) {
                    $data['error_message'] = 'Your account is temporarily locked due to repeated failed login attempts. Please try again later.';
                    $this->load->view('auth/login', $data);
                    return;
                }

                if (!password_verify($password, $user->password_hash)) {
                    $data['error_message'] = 'Invalid email or password.';
                    $this->load->view('auth/login', $data);
                    return;
                }

                $sessionData = [
                    'user_id' => $user->id,
                    'user_email' => $user->university_email,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'role' => $user->role,
                    'logged_in' => TRUE,
                    'last_activity' => time()
                ];

                $this->session->set_userdata($sessionData);
                $this->session->sess_regenerate(TRUE);

                $this->User_model->update_last_login($user->id);

                redirect('auth/dashboard');
                return;
            }
        }

        $this->load->view('auth/login', $data);
    }

    public function dashboard()
    {
        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
            return;
        }

        $this->check_session_timeout();

        echo '<h1>Welcome, ' . html_escape($this->session->userdata('first_name')) . '</h1>';
        echo '<p>You are logged in.</p>';
        echo '<p><a href="' . site_url('profile') . '">Manage Profile</a></p>';
        echo '<p><a href="' . site_url('bidding') . '">Blind Bidding</a></p>';
        if (in_array($this->session->userdata('role'), ['developer', 'admin'], true)) {
            echo '<p><a href="' . site_url('developer') . '">Developer API Keys</a></p>';
        }
        echo '<p><a href="' . site_url('auth/logout') . '">Logout</a></p>';
    }

    public function logout()
    {
        $this->session->sess_destroy();
        redirect('auth/login');
    }

    private function check_session_timeout()
    {
        $lastActivity = $this->session->userdata('last_activity');
        $timeoutSeconds = 7200;

        if ($lastActivity && (time() - $lastActivity > $timeoutSeconds)) {
            $this->session->sess_destroy();
            redirect('auth/login');
            exit;
        }

        $this->session->set_userdata('last_activity', time());
    }

    public function forgot_password()
    {
        $data['title'] = 'Forgot Password';

        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('email', 'University Email', 'required|trim|valid_email');

            if ($this->form_validation->run() === TRUE) {
                $email = strtolower(trim($this->input->post('email', TRUE)));
                $user = $this->User_model->get_user_by_email($email);

                if ($user) {
                    $rawToken = bin2hex(random_bytes(32));
                    $tokenHash = hash('sha256', $rawToken);

                    $this->User_model->store_password_reset_token([
                        'user_id' => $user->id,
                        'token_hash' => $tokenHash,
                        'expires_at' => date('Y-m-d H:i:s', strtotime('+1 hour'))
                    ]);

                    $resetLink = site_url('auth/reset_password?token=' . $rawToken);

                    $message = '
                        <h2>Password Reset Request</h2>
                        <p>You requested to reset your password.</p>
                        <p>Click the link below to continue:</p>
                        <p><a href="' . $resetLink . '">Reset Password</a></p>
                        <p>This link will expire in 1 hour.</p>
                    ';

                    $this->send_email_message(
                        $email,
                        'Reset your Alumni Influencer password',
                        $message
                    );
                }

                $data['success_message'] = 'If that email exists, a password reset link has been sent.';
            }
        }

        $this->load->view('auth/forgot_password', $data);
    }

    public function reset_password()
    {
        $data['title'] = 'Reset Password';

        $rawToken = $this->input->get('token', TRUE);

        if (empty($rawToken)) {
            show_error('Invalid reset link.', 400);
        }

        $tokenHash = hash('sha256', $rawToken);
        $tokenRow = $this->User_model->get_valid_password_reset_token($tokenHash);

        if (!$tokenRow) {
            show_error('Reset link is invalid, expired, or already used.', 400);
        }

        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules(
                'password',
                'Password',
                'required|trim|min_length[8]|max_length[64]|callback_strong_password_check'
            );
            $this->form_validation->set_rules(
                'confirm_password',
                'Confirm Password',
                'required|trim|matches[password]'
            );

            if ($this->form_validation->run() === TRUE) {
                $passwordHash = password_hash($this->input->post('password'), PASSWORD_BCRYPT);

                $this->User_model->update_password($tokenRow->user_id, $passwordHash);
                $this->User_model->mark_password_reset_token_used($tokenRow->id);

                $this->session->set_flashdata('success_message', 'Password reset successful. You can now log in.');
                redirect('auth/login');
                return;
            }
        }

        $data['token'] = $rawToken;
        $this->load->view('auth/reset_password', $data);
    }

}