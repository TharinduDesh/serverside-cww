<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Analytics_api extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('Analytics_model');
        $this->load->model('Api_key_model');

        header('Content-Type: application/json');
    }

    /**
     * Sends a JSON response and stops further execution.
     */
    private function respond($data, $statusCode = 200)
    {
        $this->output
            ->set_status_header($statusCode)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES))
            ->_display();

        exit;
    }

    /**
     * Reads the Authorization header from different server environments.
     */
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

    /**
     * Extracts the Bearer token from Authorization header.
     */
    private function get_bearer_token()
    {
        $header = $this->get_authorization_header();

        if (!$header) {
            return null;
        }

        if (preg_match('/Bearer\s+(\S+)/i', $header, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Checks whether the API key has the required permission scope.
     *
     * Supports both:
     * - old CW1 style scopes: read, read_stats, full
     * - new CW2 style scopes: read:alumni, read:analytics, read:alumni_of_day
     */
    private function api_key_has_scope($apiKey, $requiredScope)
    {
        if (!$apiKey || empty($apiKey->scope)) {
            return false;
        }

        $scope = trim($apiKey->scope);

        /*
         * Backward compatibility for existing CW1 keys.
         * If your old developer key has "full", it can access analytics.
         */
        if ($scope === 'full') {
            return true;
        }

        if ($scope === $requiredScope) {
            return true;
        }

        /*
         * Allows comma-separated scopes:
         * Example: read:alumni,read:analytics
         */
        $scopes = array_map('trim', explode(',', $scope));

        return in_array($requiredScope, $scopes);
    }

    /**
     * Authenticates the API key and checks its required permission scope.
     */
    private function authenticate_api_key($requiredScope)
    {
        $rawToken = $this->get_bearer_token();

        if (!$rawToken) {
            $this->respond([
                'status' => 'error',
                'message' => 'Missing Bearer token.'
            ], 401);
        }

        $tokenHash = hash('sha256', $rawToken);
        $apiKey = $this->Api_key_model->find_valid_api_key_by_hash($tokenHash);

        if (!$apiKey) {
            $this->respond([
                'status' => 'error',
                'message' => 'Invalid, expired, or revoked API key.'
            ], 401);
        }

        if (!$this->api_key_has_scope($apiKey, $requiredScope)) {
            $this->log_usage($apiKey->id, 403);

            $this->respond([
                'status' => 'error',
                'message' => 'Forbidden: API key does not have the required scope.',
                'required_scope' => $requiredScope
            ], 403);
        }

        $this->Api_key_model->update_last_used($apiKey->id);

        return $apiKey;
    }

    /**
     * Stores API usage details for monitoring and coursework evidence.
     */
    private function log_usage($apiKeyId, $statusCode = 200)
    {
        $this->Api_key_model->log_api_usage([
            'api_key_id' => $apiKeyId,
            'endpoint' => current_url(),
            'method' => $this->input->method(TRUE),
            'ip_address' => $this->input->ip_address(),
            'user_agent' => $this->input->user_agent(),
            'status_code' => $statusCode
        ]);
    }

    /**
     * Allows only GET requests for analytics endpoints.
     */
    private function require_get()
    {
        if ($this->input->method(TRUE) !== 'GET') {
            $this->respond([
                'status' => 'error',
                'message' => 'Method not allowed.'
            ], 405);
        }
    }

    /**
     * GET /api/analytics/summary
     */
    public function summary()
    {
        $this->require_get();

        $apiKey = $this->authenticate_api_key('read:analytics');
        $data = $this->Analytics_model->get_summary();

        $this->log_usage($apiKey->id, 200);

        $this->respond([
            'status' => 'success',
            'data' => $data
        ]);
    }

    /**
     * GET /api/analytics/programmes
     */
    public function programmes()
    {
        $this->require_get();

        $apiKey = $this->authenticate_api_key('read:analytics');
        $data = $this->Analytics_model->get_alumni_by_programme();

        $this->log_usage($apiKey->id, 200);

        $this->respond([
            'status' => 'success',
            'data' => $data
        ]);
    }

    /**
     * GET /api/analytics/graduation-years
     */
    public function graduation_years()
    {
        $this->require_get();

        $apiKey = $this->authenticate_api_key('read:analytics');
        $data = $this->Analytics_model->get_alumni_by_graduation_year();

        $this->log_usage($apiKey->id, 200);

        $this->respond([
            'status' => 'success',
            'data' => $data
        ]);
    }

    /**
     * GET /api/analytics/industry-sectors
     */
    public function industry_sectors()
    {
        $this->require_get();

        $apiKey = $this->authenticate_api_key('read:analytics');
        $data = $this->Analytics_model->get_employment_by_industry_sector();

        $this->log_usage($apiKey->id, 200);

        $this->respond([
            'status' => 'success',
            'data' => $data
        ]);
    }

    /**
     * GET /api/analytics/job-titles
     */
    public function job_titles()
    {
        $this->require_get();

        $apiKey = $this->authenticate_api_key('read:analytics');
        $data = $this->Analytics_model->get_common_job_titles(10);

        $this->log_usage($apiKey->id, 200);

        $this->respond([
            'status' => 'success',
            'data' => $data
        ]);
    }

    /**
     * GET /api/analytics/certifications
     */
    public function certifications()
    {
        $this->require_get();

        $apiKey = $this->authenticate_api_key('read:analytics');
        $data = $this->Analytics_model->get_top_certifications(10);

        $this->log_usage($apiKey->id, 200);

        $this->respond([
            'status' => 'success',
            'data' => $data
        ]);
    }

    /**
     * GET /api/analytics/courses
     */
    public function courses()
    {
        $this->require_get();

        $apiKey = $this->authenticate_api_key('read:analytics');
        $data = $this->Analytics_model->get_top_professional_courses(10);

        $this->log_usage($apiKey->id, 200);

        $this->respond([
            'status' => 'success',
            'data' => $data
        ]);
    }

    /**
     * GET /api/analytics/top-employers
     */
    public function top_employers()
    {
        $this->require_get();

        $apiKey = $this->authenticate_api_key('read:analytics');
        $data = $this->Analytics_model->get_top_employers(10);

        $this->log_usage($apiKey->id, 200);

        $this->respond([
            'status' => 'success',
            'data' => $data
        ]);
    }

    /**
     * GET /api/analytics/geography
     */
    public function geography()
    {
        $this->require_get();

        $apiKey = $this->authenticate_api_key('read:analytics');
        $data = $this->Analytics_model->get_geographic_distribution();

        $this->log_usage($apiKey->id, 200);

        $this->respond([
            'status' => 'success',
            'data' => $data
        ]);
    }

    /**
     * GET /api/analytics/skills-gap
     */
    public function skills_gap()
    {
        $this->require_get();

        $apiKey = $this->authenticate_api_key('read:analytics');
        $data = $this->Analytics_model->get_skills_gap_insights(10);

        $this->log_usage($apiKey->id, 200);

        $this->respond([
            'status' => 'success',
            'data' => $data
        ]);
    }

    /**
     * GET /api/analytics/filter-options
     */
    public function filter_options()
    {
        $this->require_get();

        $apiKey = $this->authenticate_api_key('read:alumni');
        $data = $this->Analytics_model->get_filter_options();

        $this->log_usage($apiKey->id, 200);

        $this->respond([
            'status' => 'success',
            'data' => $data
        ]);
    }

    /**
     * GET /api/analytics/alumni
     *
     * Optional query parameters:
     * - programme
     * - graduation_year
     * - industry_sector
     * - search
     */
    public function alumni()
    {
        $this->require_get();

        $apiKey = $this->authenticate_api_key('read:alumni');

        $filters = [
            'programme' => trim($this->input->get('programme', TRUE)),
            'graduation_year' => trim($this->input->get('graduation_year', TRUE)),
            'industry_sector' => trim($this->input->get('industry_sector', TRUE)),
            'search' => trim($this->input->get('search', TRUE))
        ];

        $data = $this->Analytics_model->get_filtered_alumni($filters);

        $this->log_usage($apiKey->id, 200);

        $this->respond([
            'status' => 'success',
            'filters' => $filters,
            'count' => count($data),
            'data' => $data
        ]);
    }
}