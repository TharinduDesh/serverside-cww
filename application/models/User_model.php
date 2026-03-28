<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model
{
    public function email_exists($email)
    {
        return $this->db
            ->where('university_email', $email)
            ->count_all_results('users') > 0;
    }

    public function create_user($data)
    {
        $this->db->insert('users', $data);
        return $this->db->insert_id();
    }

    public function store_verification_token($data)
    {
        return $this->db->insert('email_verification_tokens', $data);
    }
}