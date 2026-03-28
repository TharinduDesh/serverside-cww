<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Testdb extends CI_Controller
{
    public function index()
    {
        if ($this->db->conn_id) {
            echo 'Database connected successfully.';
        } else {
            echo 'Database connection failed.';
        }
    }
}