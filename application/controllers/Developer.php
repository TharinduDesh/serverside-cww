<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Developer extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Api_key_model');
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

    public function index()
    {
        $this->require_login();

        $userId = $this->session->userdata('user_id');

        $data['title'] = 'Developer API Keys';
        $data['api_keys'] = $this->Api_key_model->get_api_keys_by_user_id($userId);
        $data['usage_logs'] = $this->Api_key_model->get_usage_logs_by_user_id($userId);

        $this->load->view('developer/index', $data);
    }

    public function generate_key()
    {
        $this->require_login();

        $userId = $this->session->userdata('user_id');

        $this->form_validation->set_rules('key_name', 'Key Name', 'required|trim|max_length[100]');

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('error_message', validation_errors());
            redirect('developer');
            return;
        }

        $keyName = trim($this->input->post('key_name', TRUE));

        $rawKey = bin2hex(random_bytes(32));
        $keyHash = hash('sha256', $rawKey);

        $this->Api_key_model->create_api_key([
            'user_id' => $userId,
            'key_name' => $keyName,
            'api_key_hash' => $keyHash,
            'is_active' => 1
        ]);

        $this->session->set_flashdata('success_message', 'API key generated successfully. Copy it now; it will not be shown again.');
        $this->session->set_flashdata('generated_api_key', $rawKey);

        redirect('developer');
    }

    public function revoke_key($id)
    {
        $this->require_login();

        $userId = $this->session->userdata('user_id');
        $apiKey = $this->Api_key_model->get_api_key_by_id($id, $userId);

        if (!$apiKey) {
            show_404();
        }

        $this->Api_key_model->revoke_api_key($id, $userId);

        $this->session->set_flashdata('success_message', 'API key revoked successfully.');
        redirect('developer');
    }
}