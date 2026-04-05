<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api_docs extends CI_Controller
{
    public function index()
    {
        $data['title'] = 'API Documentation';
        $data['spec_url'] = site_url('api-spec.json');

        $this->load->view('api_docs/index', $data);
    }

    public function spec()
    {
        $json = $this->load->view('api_docs/openapi.json', [], TRUE);

        $this->output
            ->set_content_type('application/json', 'utf-8')
            ->set_output($json);
    }
}