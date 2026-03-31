<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Profile_model extends CI_Model
{
    public function get_profile_by_user_id($userId)
    {
        return $this->db
            ->where('user_id', $userId)
            ->get('profiles')
            ->row();
    }

    public function create_profile($data)
    {
        return $this->db->insert('profiles', $data);
    }

    public function update_profile($userId, $data)
    {
        return $this->db
            ->where('user_id', $userId)
            ->update('profiles', $data);
    }

    // Degree-related methods

    public function get_degrees_by_user_id($userId)
    {
        return $this->db
            ->where('user_id', $userId)
            ->order_by('completion_date', 'DESC')
            ->get('degrees')
            ->result();
    }

    public function get_degree_by_id($id, $userId)
    {
        return $this->db
            ->where('id', $id)
            ->where('user_id', $userId)
            ->get('degrees')
            ->row();
    }

    public function create_degree($data)
    {
        return $this->db->insert('degrees', $data);
    }

    public function update_degree($id, $userId, $data)
    {
        return $this->db
            ->where('id', $id)
            ->where('user_id', $userId)
            ->update('degrees', $data);
    }

    public function delete_degree($id, $userId)
    {
        return $this->db
            ->where('id', $id)
            ->where('user_id', $userId)
            ->delete('degrees');
    }

    // Certification-related methods

    public function get_certifications_by_user_id($userId)
    {
        return $this->db
            ->where('user_id', $userId)
            ->order_by('completion_date', 'DESC')
            ->get('certifications')
            ->result();
    }

    public function get_certification_by_id($id, $userId)
    {
        return $this->db
            ->where('id', $id)
            ->where('user_id', $userId)
            ->get('certifications')
            ->row();
    }

    public function create_certification($data)
    {
        return $this->db->insert('certifications', $data);
    }

    public function update_certification($id, $userId, $data)
    {
        return $this->db
            ->where('id', $id)
            ->where('user_id', $userId)
            ->update('certifications', $data);
    }

    public function delete_certification($id, $userId)
    {
        return $this->db
            ->where('id', $id)
            ->where('user_id', $userId)
            ->delete('certifications');
    }

    // License-related methods

    public function get_licences_by_user_id($userId)
    {
        return $this->db
            ->where('user_id', $userId)
            ->order_by('completion_date', 'DESC')
            ->get('licences')
            ->result();
    }

    public function get_licence_by_id($id, $userId)
    {
        return $this->db
            ->where('id', $id)
            ->where('user_id', $userId)
            ->get('licences')
            ->row();
    }

    public function create_licence($data)
    {
        return $this->db->insert('licences', $data);
    }

    public function update_licence($id, $userId, $data)
    {
        return $this->db
            ->where('id', $id)
            ->where('user_id', $userId)
            ->update('licences', $data);
    }

    public function delete_licence($id, $userId)
    {
        return $this->db
            ->where('id', $id)
            ->where('user_id', $userId)
            ->delete('licences');
    }

    // Professional Cources-related methods

    public function get_courses_by_user_id($userId)
    {
        return $this->db
            ->where('user_id', $userId)
            ->order_by('completion_date', 'DESC')
            ->get('professional_courses')
            ->result();
    }

    public function get_course_by_id($id, $userId)
    {
        return $this->db
            ->where('id', $id)
            ->where('user_id', $userId)
            ->get('professional_courses')
            ->row();
    }

    public function create_course($data)
    {
        return $this->db->insert('professional_courses', $data);
    }

    public function update_course($id, $userId, $data)
    {
        return $this->db
            ->where('id', $id)
            ->where('user_id', $userId)
            ->update('professional_courses', $data);
    }

    public function delete_course($id, $userId)
    {
        return $this->db
            ->where('id', $id)
            ->where('user_id', $userId)
            ->delete('professional_courses');
    }

    // Employment history-related methods

    public function get_employment_history_by_user_id($userId)
    {
        return $this->db
            ->where('user_id', $userId)
            ->order_by('is_current', 'DESC')
            ->order_by('start_date', 'DESC')
            ->get('employment_history')
            ->result();
    }

    public function get_employment_by_id($id, $userId)
    {
        return $this->db
            ->where('id', $id)
            ->where('user_id', $userId)
            ->get('employment_history')
            ->row();
    }

    public function create_employment($data)
    {
        return $this->db->insert('employment_history', $data);
    }

    public function update_employment($id, $userId, $data)
    {
        return $this->db
            ->where('id', $id)
            ->where('user_id', $userId)
            ->update('employment_history', $data);
    }

    public function delete_employment($id, $userId)
    {
        return $this->db
            ->where('id', $id)
            ->where('user_id', $userId)
            ->delete('employment_history');
    }

}

