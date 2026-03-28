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

    public function get_valid_verification_token($tokenHash)
    {
        return $this->db
            ->where('token_hash', $tokenHash)
            ->where('used_at IS NULL', null, false)
            ->where('expires_at >', date('Y-m-d H:i:s'))
            ->get('email_verification_tokens')
            ->row();
    }

    public function mark_email_verified($userId)
    {
        return $this->db
            ->where('id', $userId)
            ->update('users', ['email_verified' => 1]);
    }

    public function mark_verification_token_used($tokenId)
    {
        return $this->db
            ->where('id', $tokenId)
            ->update('email_verification_tokens', [
                'used_at' => date('Y-m-d H:i:s')
            ]);
    }

    public function get_user_by_email($email)
    {
        return $this->db
            ->where('university_email', $email)
            ->where('is_active', 1)
            ->get('users')
            ->row();
    }

    public function update_last_login($userId)
    {
        return $this->db
            ->where('id', $userId)
            ->update('users', [
                'last_login_at' => date('Y-m-d H:i:s')
            ]);
    }

    public function store_password_reset_token($data)
    {
        return $this->db->insert('password_reset_tokens', $data);
    }

    public function get_valid_password_reset_token($tokenHash)
    {
        return $this->db
            ->where('token_hash', $tokenHash)
            ->where('used_at IS NULL', null, false)
            ->where('expires_at >', date('Y-m-d H:i:s'))
            ->get('password_reset_tokens')
            ->row();
    }

    public function mark_password_reset_token_used($tokenId)
    {
        return $this->db
            ->where('id', $tokenId)
            ->update('password_reset_tokens', [
                'used_at' => date('Y-m-d H:i:s')
            ]);
    }

    public function update_password($userId, $passwordHash)
    {
        return $this->db
            ->where('id', $userId)
            ->update('users', [
                'password_hash' => $passwordHash,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
    }

    

    
}