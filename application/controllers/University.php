<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class University extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('Analytics_model');
        $this->load->model('Api_key_model');
        $this->load->helper(['url', 'form']);

        $this->require_login();
    }

    /**
     * Restricts university analytics pages to logged-in users.
     * Developer/admin users are the best fit for this dashboard.
     */
    private function require_login()
    {
        if (!$this->session->userdata('user_id')) {
            redirect('login');
            return;
        }
    }

    /**
     * Main University Analytics Dashboard.
     */
    public function dashboard()
    {
        $data['title'] = 'University Analytics Dashboard';
        $data['summary'] = $this->Analytics_model->get_summary();
        $data['skills_gap'] = $this->Analytics_model->get_skills_gap_insights(5);

        $this->load->view('university/dashboard', $data);
    }

    /**
     * Graphs and trends page.
     */
    public function graphs()
    {
        $data['title'] = 'Analytics Graphs & Trends';

        $this->load->view('university/graphs', $data);
    }

    /**
     * Alumni explorer page with filters.
     */
    public function alumni()
    {
        $filters = [
            'programme' => trim($this->input->get('programme', TRUE)),
            'graduation_year' => trim($this->input->get('graduation_year', TRUE)),
            'industry_sector' => trim($this->input->get('industry_sector', TRUE)),
            'search' => trim($this->input->get('search', TRUE))
        ];

        $data['title'] = 'View Alumni';
        $data['filters'] = $filters;
        $data['filter_options'] = $this->Analytics_model->get_filter_options();
        $data['alumni'] = $this->Analytics_model->get_filtered_alumni($filters);

        $this->load->view('university/alumni', $data);
    }

    /**
     * Reports and export page.
     */
    public function reports()
    {
        $data['title'] = 'Analytics Reports';
        $data['summary'] = $this->Analytics_model->get_summary();
        $data['skills_gap'] = $this->Analytics_model->get_skills_gap_insights(10);

        $this->load->view('university/reports', $data);
    }
}