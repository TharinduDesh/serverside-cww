<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Analytics_model extends CI_Model
{
    /**
     * Returns the main dashboard summary counts.
     */
    public function get_summary()
    {
        $summary = [];

        $summary['total_alumni'] = $this->db
            ->where('email_verified', 1)
            ->where('is_active', 1)
            ->count_all_results('users');

        $summary['total_programmes'] = $this->db
            ->select('programme')
            ->from('degrees')
            ->where('programme IS NOT NULL', null, false)
            ->where('programme !=', '')
            ->group_by('programme')
            ->get()
            ->num_rows();

        $summary['total_industry_sectors'] = $this->db
            ->select('industry_sector')
            ->from('employment_history')
            ->where('industry_sector IS NOT NULL', null, false)
            ->where('industry_sector !=', '')
            ->group_by('industry_sector')
            ->get()
            ->num_rows();

        $summary['total_certifications'] = $this->db
            ->count_all_results('certifications');

        $summary['total_professional_courses'] = $this->db
            ->count_all_results('professional_courses');

        return $summary;
    }

    /**
     * Chart 1: Alumni count by programme.
     */
    public function get_alumni_by_programme()
    {
        return $this->db
            ->select('COALESCE(NULLIF(programme, ""), "Not Specified") AS label, COUNT(*) AS total', false)
            ->from('degrees')
            ->group_by('label')
            ->order_by('total', 'DESC')
            ->get()
            ->result_array();
    }

    /**
     * Chart 2: Alumni count by graduation year.
     * Uses completion_date from degrees table.
     */
    public function get_alumni_by_graduation_year()
    {
        return $this->db
            ->select('YEAR(completion_date) AS label, COUNT(*) AS total', false)
            ->from('degrees')
            ->where('completion_date IS NOT NULL', null, false)
            ->group_by('YEAR(completion_date)')
            ->order_by('label', 'ASC')
            ->get()
            ->result_array();
    }

    /**
     * Chart 3: Employment distribution by industry sector.
     */
    public function get_employment_by_industry_sector()
    {
        return $this->db
            ->select('COALESCE(NULLIF(industry_sector, ""), "Not Specified") AS label, COUNT(*) AS total', false)
            ->from('employment_history')
            ->group_by('label')
            ->order_by('total', 'DESC')
            ->get()
            ->result_array();
    }

    /**
     * Chart 4: Most common job titles.
     */
    public function get_common_job_titles($limit = 10)
    {
        return $this->db
            ->select('job_title AS label, COUNT(*) AS total')
            ->from('employment_history')
            ->where('job_title IS NOT NULL', null, false)
            ->where('job_title !=', '')
            ->group_by('job_title')
            ->order_by('total', 'DESC')
            ->limit($limit)
            ->get()
            ->result_array();
    }

    /**
     * Chart 5: Most common certifications.
     */
    public function get_top_certifications($limit = 10)
    {
        return $this->db
            ->select('certification_name AS label, COUNT(*) AS total')
            ->from('certifications')
            ->where('certification_name IS NOT NULL', null, false)
            ->where('certification_name !=', '')
            ->group_by('certification_name')
            ->order_by('total', 'DESC')
            ->limit($limit)
            ->get()
            ->result_array();
    }

    /**
     * Chart 6: Most common professional courses.
     */
    public function get_top_professional_courses($limit = 10)
    {
        return $this->db
            ->select('course_name AS label, COUNT(*) AS total')
            ->from('professional_courses')
            ->where('course_name IS NOT NULL', null, false)
            ->where('course_name !=', '')
            ->group_by('course_name')
            ->order_by('total', 'DESC')
            ->limit($limit)
            ->get()
            ->result_array();
    }

    /**
     * Chart 7: Top employers.
     */
    public function get_top_employers($limit = 10)
    {
        return $this->db
            ->select('company_name AS label, COUNT(*) AS total')
            ->from('employment_history')
            ->where('company_name IS NOT NULL', null, false)
            ->where('company_name !=', '')
            ->group_by('company_name')
            ->order_by('total', 'DESC')
            ->limit($limit)
            ->get()
            ->result_array();
    }

    /**
     * Chart 8: Alumni employment location distribution.
     */
    public function get_geographic_distribution()
    {
        return $this->db
            ->select('COALESCE(NULLIF(location, ""), "Not Specified") AS label, COUNT(*) AS total', false)
            ->from('employment_history')
            ->group_by('label')
            ->order_by('total', 'DESC')
            ->get()
            ->result_array();
    }

    /**
     * Skills gap style insight.
     * This combines certifications and professional courses as post-graduation development signals.
     */
    public function get_skills_gap_insights($limit = 10)
    {
        $certifications = $this->db
            ->select('certification_name AS skill_name, COUNT(*) AS total')
            ->from('certifications')
            ->where('certification_name IS NOT NULL', null, false)
            ->where('certification_name !=', '')
            ->group_by('certification_name')
            ->get_compiled_select();

        $courses = $this->db
            ->select('course_name AS skill_name, COUNT(*) AS total')
            ->from('professional_courses')
            ->where('course_name IS NOT NULL', null, false)
            ->where('course_name !=', '')
            ->group_by('course_name')
            ->get_compiled_select();

        $query = $this->db->query("
            SELECT 
                skill_name AS label,
                SUM(total) AS total
            FROM (
                {$certifications}
                UNION ALL
                {$courses}
            ) AS combined_skills
            GROUP BY skill_name
            ORDER BY total DESC
            LIMIT ?
        ", [$limit]);

        $results = $query->result_array();

        $totalAlumni = max((int) $this->db
            ->where('email_verified', 1)
            ->where('is_active', 1)
            ->count_all_results('users'), 1);

        foreach ($results as &$row) {
            $percentage = round(((int) $row['total'] / $totalAlumni) * 100, 2);
            $row['percentage'] = $percentage;
            $row['level'] = $this->classify_gap_level($percentage);
        }

        return $results;
    }

    /**
     * Returns alumni list using optional filters.
     */
    public function get_filtered_alumni($filters = [])
    {
        $this->db
            ->select('
                users.id,
                users.university_email,
                users.first_name,
                users.last_name,
                CONCAT(users.first_name, " ", users.last_name) AS full_name,
                profiles.headline,
                degrees.degree_name,
                degrees.programme,
                degrees.completion_date,
                employment_history.company_name,
                employment_history.job_title,
                employment_history.industry_sector,
                employment_history.location
            ', false)
            ->from('users')
            ->join('profiles', 'profiles.user_id = users.id', 'left')
            ->join('degrees', 'degrees.user_id = users.id', 'left')
            ->join('employment_history', 'employment_history.user_id = users.id', 'left')
            ->where('users.email_verified', 1)
            ->where('users.is_active', 1);

        if (!empty($filters['programme'])) {
            $this->db->where('degrees.programme', $filters['programme']);
        }

        if (!empty($filters['graduation_year'])) {
            $this->db->where('YEAR(degrees.completion_date) =', (int) $filters['graduation_year'], false);
        }

        if (!empty($filters['industry_sector'])) {
            $this->db->where('employment_history.industry_sector', $filters['industry_sector']);
        }

        if (!empty($filters['search'])) {
            $this->db->group_start()
                ->like('users.first_name', $filters['search'])
                ->or_like('users.last_name', $filters['search'])
                ->or_like('users.university_email', $filters['search'])
                ->or_like('employment_history.company_name', $filters['search'])
                ->or_like('employment_history.job_title', $filters['search'])
                ->group_end();
        }

        return $this->db
            ->group_by('users.id')
            ->order_by('users.first_name', 'ASC')
            ->order_by('users.last_name', 'ASC')
            ->get()
            ->result_array();
    }

    /**
     * Dropdown data for alumni filter page.
     */
    public function get_filter_options()
    {
        return [
            'programmes' => $this->get_distinct_programmes(),
            'graduation_years' => $this->get_distinct_graduation_years(),
            'industry_sectors' => $this->get_distinct_industry_sectors()
        ];
    }

    /**
     * Returns distinct programmes for filter dropdown.
     */
    public function get_distinct_programmes()
    {
        return $this->db
            ->select('programme')
            ->from('degrees')
            ->where('programme IS NOT NULL', null, false)
            ->where('programme !=', '')
            ->group_by('programme')
            ->order_by('programme', 'ASC')
            ->get()
            ->result_array();
    }

    /**
     * Returns distinct graduation years for filter dropdown.
     */
    public function get_distinct_graduation_years()
    {
        return $this->db
            ->select('YEAR(completion_date) AS graduation_year', false)
            ->from('degrees')
            ->where('completion_date IS NOT NULL', null, false)
            ->group_by('YEAR(completion_date)')
            ->order_by('graduation_year', 'DESC')
            ->get()
            ->result_array();
    }

    /**
     * Returns distinct industry sectors for filter dropdown.
     */
    public function get_distinct_industry_sectors()
    {
        return $this->db
            ->select('industry_sector')
            ->from('employment_history')
            ->where('industry_sector IS NOT NULL', null, false)
            ->where('industry_sector !=', '')
            ->group_by('industry_sector')
            ->order_by('industry_sector', 'ASC')
            ->get()
            ->result_array();
    }

    /**
     * Classifies post-graduation skills/courses into insight levels.
     */
    private function classify_gap_level($percentage)
    {
        if ($percentage >= 60) {
            return 'Critical Gap';
        }

        if ($percentage >= 30) {
            return 'Significant Gap';
        }

        if ($percentage >= 10) {
            return 'Emerging Gap';
        }

        return 'Low Signal';
    }
}