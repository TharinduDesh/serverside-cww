<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api_docs extends CI_Controller
{
    public function index()
    {
        $this->load->view('api_docs/index');
    }

    public function spec()
    {
        $this->output
            ->set_content_type('application/json', 'utf-8')
            ->set_output($this->load->view('api_docs/openapi.json', [], TRUE));
    }
}