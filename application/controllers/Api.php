<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Api_key_model');
        $this->load->model('Bidding_model');
        $this->load->model('Profile_model');

        header('Content-Type: application/json');
    }

    private function respond($data, $statusCode = 200)
    {
        $this->output
            ->set_status_header($statusCode)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES))
            ->_display();

        exit;
    }

    private function get_authorization_header()
    {
        $header = null;

        if ($this->input->server('HTTP_AUTHORIZATION')) {
            $header = $this->input->server('HTTP_AUTHORIZATION');
        } elseif ($this->input->server('REDIRECT_HTTP_AUTHORIZATION')) {
            $header = $this->input->server('REDIRECT_HTTP_AUTHORIZATION');
        } elseif (function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
            if (isset($headers['Authorization'])) {
                $header = $headers['Authorization'];
            } elseif (isset($headers['authorization'])) {
                $header = $headers['authorization'];
            }
        }

        return $header;
    }

    private function get_bearer_token()
    {
        $header = $this->get_authorization_header();

        if (!$header) {
            return null;
        }

        if (preg_match('/Bearer\s(\S+)/', $header, $matches)) {
            return $matches[1];
        }

        return null;
    }

    private function authenticate_api_key()
    {
        $rawToken = $this->get_bearer_token();

        if (!$rawToken) {
            $this->respond([
                'status' => 'error',
                'message' => 'Missing Bearer token.'
            ], 401);
        }

        $tokenHash = hash('sha256', $rawToken);
        $apiKey = $this->Api_key_model->find_active_api_key_by_hash($tokenHash);

        if (!$apiKey) {
            $this->respond([
                'status' => 'error',
                'message' => 'Invalid or revoked API key.'
            ], 401);
        }

        $this->Api_key_model->update_last_used($apiKey->id);

        $this->Api_key_model->log_api_usage([
            'api_key_id' => $apiKey->id,
            'endpoint' => current_url(),
            'method' => $this->input->method(TRUE),
            'ip_address' => $this->input->ip_address(),
            'user_agent' => $this->input->user_agent()
        ]);

        return $apiKey;
    }

    public function featured_today()
    {
        $featureDate = date('Y-m-d');
        $featured = $this->Bidding_model->get_featured_alumnus_for_date($featureDate);

        if (!$featured) {
            $this->respond([
                'status' => 'success',
                'message' => 'No featured alumnus selected for today yet.',
                'data' => null
            ]);
        }

        $profile = $this->Profile_model->get_profile_by_user_id($featured->user_id);

        $this->respond([
            'status' => 'success',
            'data' => [
                'feature_date' => $featureDate,
                'user' => [
                    'id' => (int) $featured->user_id,
                    'first_name' => $featured->first_name,
                    'last_name' => $featured->last_name
                ],
                'profile' => [
                    'headline' => $profile ? $profile->headline : null,
                    'biography' => $profile ? $profile->biography : null,
                    'linkedin_url' => $profile ? $profile->linkedin_url : null,
                    'profile_image_url' => ($profile && !empty($profile->profile_image))
                        ? base_url($profile->profile_image)
                        : null
                ]
            ]
        ]);
    }

    public function featured_today_secure()
    {
        $this->authenticate_api_key();

        $featureDate = date('Y-m-d');
        $featured = $this->Bidding_model->get_featured_alumnus_for_date($featureDate);

        if (!$featured) {
            $this->respond([
                'status' => 'success',
                'message' => 'No featured alumnus selected for today yet.',
                'data' => null
            ]);
        }

        $profile = $this->Profile_model->get_profile_by_user_id($featured->user_id);

        $this->respond([
            'status' => 'success',
            'data' => [
                'feature_date' => $featureDate,
                'user' => [
                    'id' => (int) $featured->user_id,
                    'first_name' => $featured->first_name,
                    'last_name' => $featured->last_name
                ],
                'profile' => [
                    'headline' => $profile ? $profile->headline : null,
                    'biography' => $profile ? $profile->biography : null,
                    'linkedin_url' => $profile ? $profile->linkedin_url : null,
                    'profile_image_url' => ($profile && !empty($profile->profile_image))
                        ? base_url($profile->profile_image)
                        : null
                ]
            ]
        ]);
    }
}