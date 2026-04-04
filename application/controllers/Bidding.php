<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bidding extends CI_Controller
{
    private $is_testing_mode = false;
    public function __construct()
    {
        parent::__construct();

        $this->is_testing_mode =
            isset($_ENV['BIDDING_TEST_MODE']) &&
            $_ENV['BIDDING_TEST_MODE'] === 'true';

        $this->load->model('Bidding_model');
        $this->load->model('Profile_model');
        $this->load->model('User_model');
        $this->load->library('email');
    }

    private function require_login()
    {
        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
            exit;
        }

        $lastActivity = $this->session->userdata('last_activity');
        $timeoutSeconds = 7200;

        if ($lastActivity && (time() - $lastActivity > $timeoutSeconds)) {
            $this->session->sess_destroy();
            redirect('auth/login');
            exit;
        }

        $this->session->set_userdata('last_activity', time());
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

    private function get_default_feature_date()
    {
        return date('Y-m-d', strtotime('+1 day'));
    }

    private function is_valid_date($date)
    {
        $parsed = DateTime::createFromFormat('Y-m-d', $date);
        return $parsed && $parsed->format('Y-m-d') === $date;
    }

    private function refresh_blind_bid_statuses($featureDate)
    {
        $previousWinningBid = $this->Bidding_model->get_current_winning_bid_for_feature_date($featureDate);
        $highestBid = $this->Bidding_model->get_highest_bid_for_feature_date($featureDate);

        if (!$highestBid) {
            return;
        }

        $this->Bidding_model->update_all_bid_statuses_for_feature_date($featureDate, $highestBid->id);

        if (
            $previousWinningBid &&
            (int) $previousWinningBid->id !== (int) $highestBid->id
        ) {
            $outbidUser = $this->Bidding_model->get_bid_with_user_details($previousWinningBid->id);

            if ($outbidUser) {
                $sent = $this->send_outbid_notification($outbidUser, $featureDate);

                if (!$sent) {
                    log_message('error', 'Failed to send outbid email for bid ID ' . $previousWinningBid->id);
                }
            }
        }
    }

    public function index()
    {
        $this->require_login();

        $data['is_testing_mode'] = $this->is_testing_mode;

        $userId = $this->session->userdata('user_id');
        $featureDate = $this->input->get('feature_date', TRUE);

        if (empty($featureDate)) {
            $featureDate = $this->get_default_feature_date();
        }

        if (!$this->is_valid_date($featureDate)) {
            $featureDate = $this->get_default_feature_date();
        }

        $profile = $this->Profile_model->get_profile_by_user_id($userId);
        $currentBid = $this->Bidding_model->get_bid_by_user_and_feature_date($userId, $featureDate);
        $monthlyWins = $this->Bidding_model->count_monthly_featured_wins($userId, $featureDate);
        $remainingSlots = max(0, 3 - $monthlyWins);

        $data['title'] = 'Blind Bidding';
        $data['profile'] = $profile;
        $data['feature_date'] = $featureDate;
        $data['current_bid'] = $currentBid;
        $data['monthly_wins'] = $monthlyWins;
        $data['remaining_slots'] = $remainingSlots;
        $data['bid_history'] = $this->Bidding_model->get_user_bids($userId);

        $this->load->view('bidding/index', $data);
    }

    public function place_bid()
    {
        $this->require_login();

        $userId = $this->session->userdata('user_id');
        $profile = $this->Profile_model->get_profile_by_user_id($userId);

        if (!$profile) {
            $this->session->set_flashdata('error_message', 'You must complete your profile before placing a bid.');
            redirect('profile');
            return;
        }

        $this->form_validation->set_rules('feature_date', 'Feature Date', 'required|trim');
        $this->form_validation->set_rules('bid_amount', 'Bid Amount', 'required|trim|decimal|greater_than[0]');

        $featureDate = $this->input->post('feature_date', TRUE);
        $bidAmount = $this->input->post('bid_amount', TRUE);

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('error_message', validation_errors());
            redirect('bidding?feature_date=' . urlencode($featureDate ?: $this->get_default_feature_date()));
            return;
        }

        if (!$this->is_valid_date($featureDate)) {
            $this->session->set_flashdata('error_message', 'Please select a valid feature date.');
            redirect('bidding');
            return;
        }

        if ($featureDate < date('Y-m-d')) {
            $this->session->set_flashdata('error_message', 'You cannot place a bid for a past date.');
            redirect('bidding');
            return;
        }

        $monthlyWins = $this->Bidding_model->count_monthly_featured_wins($userId, $featureDate);
        if ($monthlyWins >= 3) {
            $this->session->set_flashdata('error_message', 'You have reached your monthly featured limit for this month.');
            redirect('bidding?feature_date=' . urlencode($featureDate));
            return;
        }

        $existingBid = $this->Bidding_model->get_bid_by_user_and_feature_date($userId, $featureDate);

        if ($existingBid) {
            if ((float) $bidAmount <= (float) $existingBid->bid_amount) {
                $this->session->set_flashdata('error_message', 'Bid updates must increase your previous amount.');
                redirect('bidding?feature_date=' . urlencode($featureDate));
                return;
            }

            $this->Bidding_model->update_bid($existingBid->id, $userId, [
                'bid_amount' => $bidAmount,
                'status' => 'pending'
            ]);
        } else {
            $this->Bidding_model->create_bid([
                'user_id' => $userId,
                'feature_date' => $featureDate,
                'bid_amount' => $bidAmount,
                'status' => 'pending'
            ]);
        }

        $this->refresh_blind_bid_statuses($featureDate);

        $updatedBid = $this->Bidding_model->get_bid_by_user_and_feature_date($userId, $featureDate);

        if ($updatedBid && $updatedBid->status === 'winning') {
            $this->session->set_flashdata('success_message', 'Bid saved successfully. You are currently winning for this date.');
        } else {
            $this->session->set_flashdata('success_message', 'Bid saved successfully. You are currently outbid for this date.');
        }

        redirect('bidding?feature_date=' . urlencode($featureDate));
    }

    public function run_winner_selection($featureDate = null)
    {
        $this->require_login();

        if (empty($featureDate)) {
            $featureDate = $this->get_default_feature_date();
        }

        if (!$this->is_valid_date($featureDate)) {
            show_error('Invalid feature date.', 400);
        }

        if ($this->Bidding_model->featured_record_exists($featureDate)) {
            echo 'Winner already selected for ' . html_escape($featureDate) . '.';
            return;
        }

        $bids = $this->Bidding_model->get_bids_for_feature_date_ordered($featureDate);

        if (empty($bids)) {
            echo 'No bids available for ' . html_escape($featureDate) . '.';
            return;
        }

        $winningBid = null;

        foreach ($bids as $bid) {
            $monthlyWins = $this->Bidding_model->count_monthly_featured_wins($bid->user_id, $featureDate);

            if ($monthlyWins < 3) {
                $winningBid = $bid;
                break;
            }
        }

        if (!$winningBid) {
            echo 'No eligible bidder found for ' . html_escape($featureDate) . '.';
            return;
        }

        $this->db->trans_start();

        $this->Bidding_model->create_featured_alumnus([
            'user_id' => $winningBid->user_id,
            'bid_id' => $winningBid->id,
            'feature_date' => $featureDate,
            'winning_bid_amount' => $winningBid->bid_amount
        ]);

        $this->Bidding_model->mark_final_bid_results($featureDate, $winningBid->id);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            show_error('Failed to finalize winner selection.', 500);
        }

        $bidders = $this->Bidding_model->get_bids_with_user_details_for_feature_date($featureDate);

        foreach ($bidders as $bidder) {
            if ((int) $bidder->id === (int) $winningBid->id) {
                $this->send_winner_notification($bidder, $featureDate);
            } else {
                $this->send_loser_notification($bidder, $featureDate);
            }
        }

        echo 'Winner selected and email notifications sent for ' . html_escape($featureDate) . '.';
    }

    public function featured_today()
    {
        $featureDate = date('Y-m-d');
        $featured = $this->Bidding_model->get_featured_alumnus_for_date($featureDate);

        $data['title'] = 'Featured Alumnus Today';
        $data['feature_date'] = $featureDate;
        $data['featured'] = $featured;

        $this->load->view('bidding/featured_today', $data);
    }

    // Send email notifications to all bidders 
    private function send_winner_notification($winner, $featureDate)
    {
        $subject = 'You won the Alumni of the Day bidding';

        $message = '
            <h2>Congratulations!</h2>
            <p>Dear ' . html_escape($winner->first_name . ' ' . $winner->last_name) . ',</p>
            <p>Your bid has won for the feature date <strong>' . html_escape($featureDate) . '</strong>.</p>
            <p>Your profile has been selected as the featured Alumni of the Day.</p>
            <p>Thank you for participating.</p>
        ';

        return $this->send_email_message($winner->university_email, $subject, $message);
    }

    private function send_loser_notification($bidder, $featureDate)
    {
        $subject = 'Bidding result for Alumni of the Day';

        $message = '
            <h2>Bidding Result</h2>
            <p>Dear ' . html_escape($bidder->first_name . ' ' . $bidder->last_name) . ',</p>
            <p>Your bid for the feature date <strong>' . html_escape($featureDate) . '</strong> was not selected.</p>
            <p>You can continue participating in future bidding rounds.</p>
            <p>Thank you for your interest.</p>
        ';

        return $this->send_email_message($bidder->university_email, $subject, $message);
    }

    // Admin function to reset all bids and featured records for a specific date (for testing purposes)
    public function run_winner_selection_post()
    {
        $this->require_login();

        $featureDate = $this->input->post('feature_date', TRUE);

        if (empty($featureDate) || !$this->is_valid_date($featureDate)) {
            $this->session->set_flashdata('error_message', 'Invalid feature date for winner selection.');
            redirect('bidding');
            return;
        }

        redirect('bidding/run_winner_selection/' . $featureDate);
    }

    // End admin functions

    // Live outbid mail send
    private function send_outbid_notification($bidder, $featureDate)
    {
        $subject = 'You have been outbid';

        $message = '
            <h2>You have been outbid</h2>
            <p>Dear ' . html_escape($bidder->first_name . ' ' . $bidder->last_name) . ',</p>
            <p>Your bid for the feature date <strong>' . html_escape($featureDate) . '</strong> is no longer the highest eligible bid.</p>
            <p>You may return to the platform and increase your bid if you still want to compete for that date.</p>
            <p>The current highest amount remains hidden to preserve blind bidding.</p>
        ';

        return $this->send_email_message($bidder->university_email, $subject, $message);
    }
}