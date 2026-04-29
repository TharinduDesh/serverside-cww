<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Developer extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Api_key_model');
        $this->require_developer();
    }

    private function require_developer()
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

        $role = $this->session->userdata('role');

        if (!in_array($role, ['developer', 'admin'], true)) {
            show_error('Forbidden: Developer access required.', 403);
            exit;
        }
    }

    public function index()
    {
        $userId = $this->session->userdata('user_id');

        $data['title'] = 'Developer API Keys';
        $data['api_keys'] = $this->Api_key_model->get_api_keys_by_user_id($userId);
        $data['usage_logs'] = $this->Api_key_model->get_usage_logs_by_user_id($userId);

        $this->load->view('developer/index', $data);
    }

    public function generate_key()
    {
        $userId = $this->session->userdata('user_id');

        $this->form_validation->set_rules('key_name', 'Key Name', 'required|trim|max_length[100]');
        $this->form_validation->set_rules('scope', 'Scope', 'required|trim');

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('error_message', validation_errors());
            redirect('developer');
            return;
        }

        $keyName = trim($this->input->post('key_name', TRUE));
        $scope = trim($this->input->post('scope', TRUE));


        $allowedScopes = [
            'read:alumni,read:analytics',
            'read:alumni_of_day',
            'read:alumni',
            'read:analytics',
            'read:donations',
            'full'
        ];

        if (!in_array($scope, $allowedScopes, true)) {
            $this->session->set_flashdata('error_message', 'Invalid API key scope selected.');
            redirect('developer');
            return;
        }

        try {
            $rawKey = bin2hex(random_bytes(32));
        } catch (Exception $e) {
            $this->session->set_flashdata('error_message', 'Unable to generate secure API key.');
            redirect('developer');
            return;
        }

        $keyHash = hash('sha256', $rawKey);
        $keyPrefix = substr($rawKey, 0, 12);

        $created = $this->Api_key_model->create_api_key([
            'user_id' => $userId,
            'key_name' => $keyName,
            'api_key_hash' => $keyHash,
            'key_prefix' => $keyPrefix,
            'scope' => $scope,
            'is_active' => 1
        ]);

        if (!$created) {
            $this->session->set_flashdata('error_message', 'Failed to create API key. Please try again.');
            redirect('developer');
            return;
        }

        $this->session->set_flashdata('success_message', 'API key generated successfully. Copy it now; it will not be shown again.');
        $this->session->set_flashdata('generated_api_key', $rawKey);

        redirect('developer');
    }

    public function revoke_key($id)
    {
        // Only allow POST requests 
        if ($this->input->method(TRUE) !== 'POST') {
            show_error('Method Not Allowed', 405);
        }

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