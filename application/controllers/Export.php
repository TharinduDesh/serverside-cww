<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Export extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('Analytics_model');
        $this->load->helper(['url']);

        $this->require_login();
    }

    /**
     * Export pages should only be available to logged-in users.
     */
    private function require_login()
    {
        if (!$this->session->userdata('user_id')) {
            redirect('login');
            return;
        }
    }

    /**
     * Downloads filtered alumni records as a CSV file.
     *
     * URL example:
     * /index.php/export/alumni-csv?programme=Computer%20Science&graduation_year=2025&industry_sector=IT
     */
    public function alumni_csv()
    {
        $filters = [
            'programme' => trim($this->input->get('programme', TRUE)),
            'graduation_year' => trim($this->input->get('graduation_year', TRUE)),
            'industry_sector' => trim($this->input->get('industry_sector', TRUE)),
            'search' => trim($this->input->get('search', TRUE))
        ];

        $alumni = $this->Analytics_model->get_filtered_alumni($filters);

        $filename = 'filtered_alumni_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');

        // CSV header row
        fputcsv($output, [
            'Alumni ID',
            'Full Name',
            'University Email',
            'Headline',
            'Degree',
            'Programme',
            'Graduation Date',
            'Company',
            'Job Title',
            'Industry Sector',
            'Location'
        ]);

        foreach ($alumni as $person) {
            fputcsv($output, [
                $person['id'] ?? '',
                $person['full_name'] ?? '',
                $person['university_email'] ?? '',
                $person['headline'] ?? '',
                $person['degree_name'] ?? '',
                $person['programme'] ?? '',
                $person['completion_date'] ?? '',
                $person['company_name'] ?? '',
                $person['job_title'] ?? '',
                $person['industry_sector'] ?? '',
                $person['location'] ?? ''
            ]);
        }

        fclose($output);
        exit;
    }

    /**
     * Downloads dashboard summary and skills-gap insights as a CSV file.
     */
    public function analytics_summary_csv()
    {
        $summary = $this->Analytics_model->get_summary();
        $skillsGap = $this->Analytics_model->get_skills_gap_insights(10);

        $filename = 'analytics_summary_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');

        fputcsv($output, ['University Analytics Summary']);
        fputcsv($output, ['Generated At', date('Y-m-d H:i:s')]);
        fputcsv($output, []);

        fputcsv($output, ['Metric', 'Value']);
        fputcsv($output, ['Total Active Alumni', $summary['total_alumni'] ?? 0]);
        fputcsv($output, ['Total Programmes', $summary['total_programmes'] ?? 0]);
        fputcsv($output, ['Total Industry Sectors', $summary['total_industry_sectors'] ?? 0]);
        fputcsv($output, ['Total Certifications', $summary['total_certifications'] ?? 0]);
        fputcsv($output, ['Total Professional Courses', $summary['total_professional_courses'] ?? 0]);

        fputcsv($output, []);
        fputcsv($output, ['Skills Gap Signals']);
        fputcsv($output, ['Skill / Course', 'Total Records', 'Percentage', 'Gap Level']);

        foreach ($skillsGap as $gap) {
            fputcsv($output, [
                $gap['label'] ?? '',
                $gap['total'] ?? 0,
                isset($gap['percentage']) ? $gap['percentage'] . '%' : '',
                $gap['level'] ?? ''
            ]);
        }

        fclose($output);
        exit;
    }
}