<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api_key_model extends CI_Model
{
    public function create_api_key($data)
    {
        $this->db->insert('api_keys', $data);
        return $this->db->insert_id();
    }

    public function get_api_keys_by_user_id($userId)
    {
        return $this->db
            ->where('user_id', $userId)
            ->order_by('created_at', 'DESC')
            ->get('api_keys')
            ->result();
    }

    public function get_api_key_by_id($id, $userId)
    {
        return $this->db
            ->where('id', $id)
            ->where('user_id', $userId)
            ->get('api_keys')
            ->row();
    }

    public function revoke_api_key($id, $userId)
    {
        return $this->db
            ->where('id', $id)
            ->where('user_id', $userId)
            ->update('api_keys', [
                'is_active' => 0,
                'revoked_at' => date('Y-m-d H:i:s')
            ]);
    }

    public function find_active_api_key_by_hash($hash)
    {
        return $this->db
            ->where('api_key_hash', $hash)
            ->where('is_active', 1)
            ->get('api_keys')
            ->row();
    }

    public function update_last_used($apiKeyId)
    {
        return $this->db
            ->where('id', $apiKeyId)
            ->update('api_keys', [
                'last_used_at' => date('Y-m-d H:i:s')
            ]);
    }

    public function log_api_usage($data)
    {
        return $this->db->insert('api_usage_logs', $data);
    }

    public function get_usage_logs_by_user_id($userId)
    {
        return $this->db
            ->select('api_usage_logs.*, api_keys.key_name')
            ->from('api_usage_logs')
            ->join('api_keys', 'api_keys.id = api_usage_logs.api_key_id')
            ->where('api_keys.user_id', $userId)
            ->order_by('api_usage_logs.accessed_at', 'DESC')
            ->get()
            ->result();
    }
}