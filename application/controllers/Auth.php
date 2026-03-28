<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller
{
    private $allowed_domain = 'westminster.ac.uk';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
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
                    'first_name'       => trim($this->input->post('first_name', TRUE)),
                    'last_name'        => trim($this->input->post('last_name', TRUE)),
                    'university_email' => $email,
                    'password_hash'    => password_hash($this->input->post('password'), PASSWORD_BCRYPT),
                    'role'             => 'alumnus',
                    'email_verified'   => 0,
                    'is_active'        => 1
                ];

                $user_id = $this->User_model->create_user($userData);

                $rawToken = bin2hex(random_bytes(32));
                $tokenHash = hash('sha256', $rawToken);

                $this->User_model->store_verification_token([
                    'user_id'    => $user_id,
                    'token_hash' => $tokenHash,
                    'expires_at' => date('Y-m-d H:i:s', strtotime('+1 day'))
                ]);

                // Temporary for testing before real email sending
                $data['success_message'] = 'Registration successful. Verify your email using this test link: '
                    . site_url('auth/verify?token=' . $rawToken);

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

        if (count($parts) !== 2 || $parts[1] !== $this->allowed_domain) {
            $this->form_validation->set_message(
                'email_domain_check',
                'You must register using a valid university email address.'
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

        echo 'Email verified successfully. You can now log in.';
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

                if ((int)$user->email_verified !== 1) {
                    $data['error_message'] = 'Please verify your email before logging in.';
                    $this->load->view('auth/login', $data);
                    return;
                }

                if (!password_verify($password, $user->password_hash)) {
                    $data['error_message'] = 'Invalid email or password.';
                    $this->load->view('auth/login', $data);
                    return;
                }

                $sessionData = [
                    'user_id'         => $user->id,
                    'user_email'      => $user->university_email,
                    'first_name'      => $user->first_name,
                    'last_name'       => $user->last_name,
                    'role'            => $user->role,
                    'logged_in'       => TRUE,
                    'last_activity'   => time()
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
        $timeoutSeconds = 7200; // 2 hours

        if ($lastActivity && (time() - $lastActivity > $timeoutSeconds)) {
            $this->session->sess_destroy();
            redirect('auth/login');
            exit;
        }

        $this->session->set_userdata('last_activity', time());
    }
}