<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Profile extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Profile_model');
        $this->load->library('upload');
    }


    private function require_login()
    {
        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
            exit;
        }

        $lastActivity = $this->session->userdata('last_activity');
        $timeoutSeconds = 7200;

        if ($lastActivity && (time() - $lastActivity > $timeoutSeconds)) {
            $this->session->sess_destroy();
            redirect('auth/login');
            exit;
        }

        $this->session->set_userdata('last_activity', time());
    }

    public function index()
    {
        $this->require_login();

        $userId = $this->session->userdata('user_id');

        $data['title'] = 'My Profile';
        $data['profile'] = $this->Profile_model->get_profile_by_user_id($userId);
        $data['degrees'] = $this->Profile_model->get_degrees_by_user_id($userId);
        $data['certifications'] = $this->Profile_model->get_certifications_by_user_id($userId);
        $data['licences'] = $this->Profile_model->get_licences_by_user_id($userId);
        $data['courses'] = $this->Profile_model->get_courses_by_user_id($userId);
        $data['employment_history'] = $this->Profile_model->get_employment_history_by_user_id($userId);

        $this->load->view('profile/index', $data);
    }

    public function save()
    {
        $this->require_login();

        $userId = $this->session->userdata('user_id');
        $existingProfile = $this->Profile_model->get_profile_by_user_id($userId);

        $this->form_validation->set_rules('headline', 'Headline', 'trim|max_length[255]');
        $this->form_validation->set_rules('biography', 'Biography', 'required|trim|max_length[3000]');
        $this->form_validation->set_rules(
            'linkedin_url',
            'LinkedIn URL',
            'trim|max_length[255]|callback_valid_linkedin_url'
        );
        if ($this->form_validation->run() === FALSE) {
            $data['title'] = 'My Profile';
            $data['profile'] = $existingProfile;
            $this->load->view('profile/index', $data);
            return;
        }

        $profileImage = $existingProfile ? $existingProfile->profile_image : null;

        if (!empty($_FILES['profile_image']['name'])) {
            $uploadPath = FCPATH . 'uploads/profile_images/';

            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, TRUE);
            }

            $config = [
                'upload_path' => $uploadPath,
                'allowed_types' => 'jpg|jpeg|png',
                'max_size' => 2048,
                'max_width' => 3000,
                'max_height' => 3000,
                'encrypt_name' => TRUE,
                'remove_spaces' => TRUE
            ];

            $this->upload->initialize($config);

            if (!$this->upload->do_upload('profile_image')) {
                $data['title'] = 'My Profile';
                $data['profile'] = $existingProfile;
                $data['error_message'] = $this->upload->display_errors('', '');
                $this->load->view('profile/index', $data);
                return;
            }

            $uploadData = $this->upload->data();

            $allowedMimeTypes = ['image/jpeg', 'image/png'];
            if (!in_array($uploadData['file_type'], $allowedMimeTypes, TRUE)) {
                @unlink($uploadData['full_path']);
                $data['title'] = 'My Profile';
                $data['profile'] = $existingProfile;
                $data['error_message'] = 'Invalid image type uploaded.';
                $this->load->view('profile/index', $data);
                return;
            }

            $newProfileImage = 'uploads/profile_images/' . $uploadData['file_name'];

            // Delete old image if it exists

            if (
                $existingProfile &&
                !empty($existingProfile->profile_image) &&
                file_exists(FCPATH . $existingProfile->profile_image)
            ) {
                @unlink(FCPATH . $existingProfile->profile_image);
            }

            $profileImage = $newProfileImage;
        }

        $profileData = [
            'user_id' => $userId,
            'headline' => trim($this->input->post('headline', TRUE)),
            'biography' => trim($this->input->post('biography', TRUE)),
            'linkedin_url' => trim($this->input->post('linkedin_url', TRUE)),
            'profile_image' => $profileImage
        ];

        if ($existingProfile) {
            unset($profileData['user_id']);
            $this->Profile_model->update_profile($userId, $profileData);
        } else {
            $this->Profile_model->create_profile($profileData);
        }

        $this->session->set_flashdata('success_message', 'Profile saved successfully.');
        redirect('profile');
    }

    private function is_valid_url($url)
    {
        $url = trim($url);

        if ($url === '') {
            return TRUE;
        }

        return filter_var($url, FILTER_VALIDATE_URL) !== FALSE;
    }
    // Linkedin URL validation 
    public function valid_linkedin_url($url)
    {
        $url = trim($url);

        if ($url === '') {
            return TRUE;
        }

        if (!$this->is_valid_url($url)) {
            $this->form_validation->set_message(
                'valid_linkedin_url',
                'Please enter a valid LinkedIn URL.'
            );
            return FALSE;
        }

        $parts = parse_url($url);

        if (
            empty($parts['scheme']) ||
            !in_array(strtolower($parts['scheme']), ['http', 'https'], TRUE) ||
            empty($parts['host'])
        ) {
            $this->form_validation->set_message(
                'valid_linkedin_url',
                'Please enter a valid LinkedIn URL.'
            );
            return FALSE;
        }

        $host = strtolower($parts['host']);
        $allowedHosts = ['linkedin.com', 'www.linkedin.com', 'lk.linkedin.com'];

        if (!in_array($host, $allowedHosts, TRUE)) {
            $this->form_validation->set_message(
                'valid_linkedin_url',
                'Please enter a valid LinkedIn profile URL.'
            );
            return FALSE;
        }

        return TRUE;
    }
    // Degree URL validation
    public function valid_degree_url($url)
    {
        if (!$this->is_valid_url($url)) {
            $this->form_validation->set_message('valid_degree_url', 'Please enter a valid degree URL.');
            return FALSE;
        }

        return TRUE;
    }

    public function add_degree()
    {
        $this->require_login();

        $userId = $this->session->userdata('user_id');

        $this->form_validation->set_rules('degree_name', 'Degree Name', 'required|trim|max_length[255]');
        $this->form_validation->set_rules('institution_name', 'Institution Name', 'required|trim|max_length[255]');
        $this->form_validation->set_rules('degree_url', 'Degree URL', 'trim|max_length[255]|callback_valid_degree_url');
        $this->form_validation->set_rules('completion_date', 'Completion Date', 'trim');

        if ($this->form_validation->run() === FALSE) {
            $data['title'] = 'My Profile';
            $data['profile'] = $this->Profile_model->get_profile_by_user_id($userId);
            $data['degrees'] = $this->Profile_model->get_degrees_by_user_id($userId);
            $data['degree_error'] = validation_errors();
            $this->load->view('profile/index', $data);
            return;
        }

        $degreeData = [
            'user_id' => $userId,
            'degree_name' => trim($this->input->post('degree_name', TRUE)),
            'institution_name' => trim($this->input->post('institution_name', TRUE)),
            'degree_url' => trim($this->input->post('degree_url', TRUE)),
            'completion_date' => $this->input->post('completion_date', TRUE) ?: null
        ];

        $this->Profile_model->create_degree($degreeData);

        $this->session->set_flashdata('success_message', 'Degree added successfully.');
        redirect('profile');
    }

    public function edit_degree($id)
    {
        $this->require_login();

        $userId = $this->session->userdata('user_id');
        $degree = $this->Profile_model->get_degree_by_id($id, $userId);

        if (!$degree) {
            show_404();
        }

        $this->form_validation->set_rules('degree_name', 'Degree Name', 'required|trim|max_length[255]');
        $this->form_validation->set_rules('institution_name', 'Institution Name', 'required|trim|max_length[255]');
        $this->form_validation->set_rules('degree_url', 'Degree URL', 'trim|max_length[255]|callback_valid_degree_url');
        $this->form_validation->set_rules('completion_date', 'Completion Date', 'trim');

        if ($this->form_validation->run() === FALSE) {
            $data['title'] = 'Edit Degree';
            $data['degree'] = $degree;
            $this->load->view('profile/edit_degree', $data);
            return;
        }

        $degreeData = [
            'degree_name' => trim($this->input->post('degree_name', TRUE)),
            'institution_name' => trim($this->input->post('institution_name', TRUE)),
            'degree_url' => trim($this->input->post('degree_url', TRUE)),
            'completion_date' => $this->input->post('completion_date', TRUE) ?: null
        ];

        $this->Profile_model->update_degree($id, $userId, $degreeData);

        $this->session->set_flashdata('success_message', 'Degree updated successfully.');
        redirect('profile');
    }

    public function delete_degree($id)
    {
        $this->require_login();

        $userId = $this->session->userdata('user_id');
        $degree = $this->Profile_model->get_degree_by_id($id, $userId);

        if (!$degree) {
            show_404();
        }

        $this->Profile_model->delete_degree($id, $userId);

        $this->session->set_flashdata('success_message', 'Degree deleted successfully.');
        redirect('profile');
    }

    // Certficate-related methods

    public function valid_certification_url($url)
    {
        if (!$this->is_valid_url($url)) {
            $this->form_validation->set_message('valid_certification_url', 'Please enter a valid certification URL.');
            return FALSE;
        }

        return TRUE;
    }


    public function add_certification()
    {
        $this->require_login();

        $userId = $this->session->userdata('user_id');

        $this->form_validation->set_rules('certification_name', 'Certification Name', 'required|trim|max_length[255]');
        $this->form_validation->set_rules('issuing_organization', 'Issuing Organization', 'required|trim|max_length[255]');
        $this->form_validation->set_rules('certification_url', 'Certification URL', 'trim|max_length[255]|callback_valid_certification_url');
        $this->form_validation->set_rules('certification_completion_date', 'Completion Date', 'trim');

        if ($this->form_validation->run() === FALSE) {
            $data['title'] = 'My Profile';
            $data['profile'] = $this->Profile_model->get_profile_by_user_id($userId);
            $data['degrees'] = $this->Profile_model->get_degrees_by_user_id($userId);
            $data['certifications'] = $this->Profile_model->get_certifications_by_user_id($userId);
            $data['certification_error'] = validation_errors();
            $this->load->view('profile/index', $data);
            return;
        }

        $certificationData = [
            'user_id' => $userId,
            'certification_name' => trim($this->input->post('certification_name', TRUE)),
            'issuing_organization' => trim($this->input->post('issuing_organization', TRUE)),
            'certification_url' => trim($this->input->post('certification_url', TRUE)),
            'completion_date' => $this->input->post('certification_completion_date', TRUE) ?: null
        ];

        $this->Profile_model->create_certification($certificationData);

        $this->session->set_flashdata('success_message', 'Certification added successfully.');
        redirect('profile');
    }

    public function edit_certification($id)
    {
        $this->require_login();

        $userId = $this->session->userdata('user_id');
        $certification = $this->Profile_model->get_certification_by_id($id, $userId);

        if (!$certification) {
            show_404();
        }

        $this->form_validation->set_rules('certification_name', 'Certification Name', 'required|trim|max_length[255]');
        $this->form_validation->set_rules('issuing_organization', 'Issuing Organization', 'required|trim|max_length[255]');
        $this->form_validation->set_rules('certification_url', 'Certification URL', 'trim|max_length[255]|callback_valid_certification_url');
        $this->form_validation->set_rules('completion_date', 'Completion Date', 'trim');

        if ($this->form_validation->run() === FALSE) {
            $data['title'] = 'Edit Certification';
            $data['certification'] = $certification;
            $this->load->view('profile/edit_certification', $data);
            return;
        }

        $certificationData = [
            'certification_name' => trim($this->input->post('certification_name', TRUE)),
            'issuing_organization' => trim($this->input->post('issuing_organization', TRUE)),
            'certification_url' => trim($this->input->post('certification_url', TRUE)),
            'completion_date' => $this->input->post('completion_date', TRUE) ?: null
        ];

        $this->Profile_model->update_certification($id, $userId, $certificationData);

        $this->session->set_flashdata('success_message', 'Certification updated successfully.');
        redirect('profile');
    }

    public function delete_certification($id)
    {
        $this->require_login();

        $userId = $this->session->userdata('user_id');
        $certification = $this->Profile_model->get_certification_by_id($id, $userId);

        if (!$certification) {
            show_404();
        }

        $this->Profile_model->delete_certification($id, $userId);

        $this->session->set_flashdata('success_message', 'Certification deleted successfully.');
        redirect('profile');
    }


    // Licence-related methods

    public function valid_licence_url($url)
    {
        if (!$this->is_valid_url($url)) {
            $this->form_validation->set_message('valid_licence_url', 'Please enter a valid licence URL.');
            return FALSE;
        }

        return TRUE;
    }


    public function add_licence()
    {
        $this->require_login();

        $userId = $this->session->userdata('user_id');

        $this->form_validation->set_rules('licence_name', 'Licence Name', 'required|trim|max_length[255]');
        $this->form_validation->set_rules('issuing_body', 'Issuing Body', 'required|trim|max_length[255]');
        $this->form_validation->set_rules('licence_url', 'Licence URL', 'trim|max_length[255]|callback_valid_licence_url');
        $this->form_validation->set_rules('licence_completion_date', 'Completion Date', 'trim');

        if ($this->form_validation->run() === FALSE) {
            $data['title'] = 'My Profile';
            $data['profile'] = $this->Profile_model->get_profile_by_user_id($userId);
            $data['degrees'] = $this->Profile_model->get_degrees_by_user_id($userId);
            $data['certifications'] = $this->Profile_model->get_certifications_by_user_id($userId);
            $data['licences'] = $this->Profile_model->get_licences_by_user_id($userId);
            $data['licence_error'] = validation_errors();
            $this->load->view('profile/index', $data);
            return;
        }

        $licenceData = [
            'user_id' => $userId,
            'licence_name' => trim($this->input->post('licence_name', TRUE)),
            'issuing_body' => trim($this->input->post('issuing_body', TRUE)),
            'licence_url' => trim($this->input->post('licence_url', TRUE)),
            'completion_date' => $this->input->post('licence_completion_date', TRUE) ?: null
        ];

        $this->Profile_model->create_licence($licenceData);

        $this->session->set_flashdata('success_message', 'Licence added successfully.');
        redirect('profile');
    }

    public function edit_licence($id)
    {
        $this->require_login();

        $userId = $this->session->userdata('user_id');
        $licence = $this->Profile_model->get_licence_by_id($id, $userId);

        if (!$licence) {
            show_404();
        }

        $this->form_validation->set_rules('licence_name', 'Licence Name', 'required|trim|max_length[255]');
        $this->form_validation->set_rules('issuing_body', 'Issuing Body', 'required|trim|max_length[255]');
        $this->form_validation->set_rules('licence_url', 'Licence URL', 'trim|max_length[255]|callback_valid_licence_url');
        $this->form_validation->set_rules('completion_date', 'Completion Date', 'trim');

        if ($this->form_validation->run() === FALSE) {
            $data['title'] = 'Edit Licence';
            $data['licence'] = $licence;
            $this->load->view('profile/edit_licence', $data);
            return;
        }

        $licenceData = [
            'licence_name' => trim($this->input->post('licence_name', TRUE)),
            'issuing_body' => trim($this->input->post('issuing_body', TRUE)),
            'licence_url' => trim($this->input->post('licence_url', TRUE)),
            'completion_date' => $this->input->post('completion_date', TRUE) ?: null
        ];

        $this->Profile_model->update_licence($id, $userId, $licenceData);

        $this->session->set_flashdata('success_message', 'Licence updated successfully.');
        redirect('profile');
    }

    public function delete_licence($id)
    {
        $this->require_login();

        $userId = $this->session->userdata('user_id');
        $licence = $this->Profile_model->get_licence_by_id($id, $userId);

        if (!$licence) {
            show_404();
        }

        $this->Profile_model->delete_licence($id, $userId);

        $this->session->set_flashdata('success_message', 'Licence deleted successfully.');
        redirect('profile');
    }

    // Professional cources related methods 

    public function valid_course_url($url)
    {
        if (!$this->is_valid_url($url)) {
            $this->form_validation->set_message('valid_course_url', 'Please enter a valid course URL.');
            return FALSE;
        }

        return TRUE;
    }

    public function add_course()
    {
        $this->require_login();

        $userId = $this->session->userdata('user_id');

        $this->form_validation->set_rules('course_name', 'Course Name', 'required|trim|max_length[255]');
        $this->form_validation->set_rules('provider_name', 'Provider Name', 'required|trim|max_length[255]');
        $this->form_validation->set_rules('course_url', 'Course URL', 'trim|max_length[255]|callback_valid_course_url');
        $this->form_validation->set_rules('course_completion_date', 'Completion Date', 'trim');

        if ($this->form_validation->run() === FALSE) {
            $data['title'] = 'My Profile';
            $data['profile'] = $this->Profile_model->get_profile_by_user_id($userId);
            $data['degrees'] = $this->Profile_model->get_degrees_by_user_id($userId);
            $data['certifications'] = $this->Profile_model->get_certifications_by_user_id($userId);
            $data['licences'] = $this->Profile_model->get_licences_by_user_id($userId);
            $data['courses'] = $this->Profile_model->get_courses_by_user_id($userId);
            $data['course_error'] = validation_errors();
            $this->load->view('profile/index', $data);
            return;
        }

        $courseData = [
            'user_id' => $userId,
            'course_name' => trim($this->input->post('course_name', TRUE)),
            'provider_name' => trim($this->input->post('provider_name', TRUE)),
            'course_url' => trim($this->input->post('course_url', TRUE)),
            'completion_date' => $this->input->post('course_completion_date', TRUE) ?: null
        ];

        $this->Profile_model->create_course($courseData);

        $this->session->set_flashdata('success_message', 'Professional course added successfully.');
        redirect('profile');
    }

    public function edit_course($id)
    {
        $this->require_login();

        $userId = $this->session->userdata('user_id');
        $course = $this->Profile_model->get_course_by_id($id, $userId);

        if (!$course) {
            show_404();
        }

        $this->form_validation->set_rules('course_name', 'Course Name', 'required|trim|max_length[255]');
        $this->form_validation->set_rules('provider_name', 'Provider Name', 'required|trim|max_length[255]');
        $this->form_validation->set_rules('course_url', 'Course URL', 'trim|max_length[255]|callback_valid_course_url');
        $this->form_validation->set_rules('completion_date', 'Completion Date', 'trim');

        if ($this->form_validation->run() === FALSE) {
            $data['title'] = 'Edit Professional Course';
            $data['course'] = $course;
            $this->load->view('profile/edit_course', $data);
            return;
        }

        $courseData = [
            'course_name' => trim($this->input->post('course_name', TRUE)),
            'provider_name' => trim($this->input->post('provider_name', TRUE)),
            'course_url' => trim($this->input->post('course_url', TRUE)),
            'completion_date' => $this->input->post('completion_date', TRUE) ?: null
        ];

        $this->Profile_model->update_course($id, $userId, $courseData);

        $this->session->set_flashdata('success_message', 'Professional course updated successfully.');
        redirect('profile');
    }

    public function delete_course($id)
    {
        $this->require_login();

        $userId = $this->session->userdata('user_id');
        $course = $this->Profile_model->get_course_by_id($id, $userId);

        if (!$course) {
            show_404();
        }

        $this->Profile_model->delete_course($id, $userId);

        $this->session->set_flashdata('success_message', 'Professional course deleted successfully.');
        redirect('profile');
    }

    // Employment history related methods

    private function validate_employment_dates($startDate, $endDate, $isCurrent)
    {
        if (empty($startDate)) {
            return 'Start date is required.';
        }

        if (!$this->is_valid_date($startDate)) {
            return 'Start date must be a valid date.';
        }

        if (!empty($endDate) && !$this->is_valid_date($endDate)) {
            return 'End date must be a valid date.';
        }

        if (!$isCurrent && empty($endDate)) {
            return 'End date is required unless this is your current job.';
        }

        if ($isCurrent && !empty($endDate)) {
            return 'Current jobs should not have an end date.';
        }

        if (!empty($endDate) && strtotime($endDate) < strtotime($startDate)) {
            return 'End date cannot be earlier than start date.';
        }

        return null;
    }

    public function add_employment()
    {
        $this->require_login();

        $userId = $this->session->userdata('user_id');

        $this->form_validation->set_rules('company_name', 'Company Name', 'required|trim|max_length[255]');
        $this->form_validation->set_rules('job_title', 'Job Title', 'required|trim|max_length[255]');
        $this->form_validation->set_rules('start_date', 'Start Date', 'required|trim');
        $this->form_validation->set_rules('end_date', 'End Date', 'trim');
        $this->form_validation->set_rules('description', 'Description', 'trim|max_length[3000]');

        $isCurrent = $this->input->post('is_current') ? 1 : 0;
        $startDate = $this->input->post('start_date', TRUE);
        $endDate = $this->input->post('end_date', TRUE);

        $dateError = $this->validate_employment_dates($startDate, $endDate, $isCurrent);

        if ($this->form_validation->run() === FALSE || $dateError !== null) {
            $data['title'] = 'My Profile';
            $data['profile'] = $this->Profile_model->get_profile_by_user_id($userId);
            $data['degrees'] = $this->Profile_model->get_degrees_by_user_id($userId);
            $data['certifications'] = $this->Profile_model->get_certifications_by_user_id($userId);
            $data['licences'] = $this->Profile_model->get_licences_by_user_id($userId);
            $data['courses'] = $this->Profile_model->get_courses_by_user_id($userId);
            $data['employment_history'] = $this->Profile_model->get_employment_history_by_user_id($userId);
            $data['employment_error'] = validation_errors() . (!empty($dateError) ? '<p>' . $dateError . '</p>' : '');
            $this->load->view('profile/index', $data);
            return;
        }

        $employmentData = [
            'user_id' => $userId,
            'company_name' => trim($this->input->post('company_name', TRUE)),
            'job_title' => trim($this->input->post('job_title', TRUE)),
            'start_date' => $startDate,
            'end_date' => $isCurrent ? null : ($endDate ?: null),
            'is_current' => $isCurrent,
            'description' => trim($this->input->post('description', TRUE))
        ];

        $this->Profile_model->create_employment($employmentData);

        $this->session->set_flashdata('success_message', 'Employment history added successfully.');
        redirect('profile');
    }

    public function edit_employment($id)
    {
        $this->require_login();

        $userId = $this->session->userdata('user_id');
        $employment = $this->Profile_model->get_employment_by_id($id, $userId);

        if (!$employment) {
            show_404();
        }

        $this->form_validation->set_rules('company_name', 'Company Name', 'required|trim|max_length[255]');
        $this->form_validation->set_rules('job_title', 'Job Title', 'required|trim|max_length[255]');
        $this->form_validation->set_rules('start_date', 'Start Date', 'required|trim');
        $this->form_validation->set_rules('end_date', 'End Date', 'trim');
        $this->form_validation->set_rules('description', 'Description', 'trim|max_length[3000]');

        $isCurrent = $this->input->post('is_current') ? 1 : 0;
        $startDate = $this->input->post('start_date', TRUE);
        $endDate = $this->input->post('end_date', TRUE);

        $dateError = $this->validate_employment_dates($startDate, $endDate, $isCurrent);

        if ($this->form_validation->run() === FALSE || $dateError !== null) {
            $data['title'] = 'Edit Employment';
            $data['employment'] = $employment;
            $data['employment_error'] = validation_errors() . (!empty($dateError) ? '<p>' . $dateError . '</p>' : '');
            $this->load->view('profile/edit_employment', $data);
            return;
        }

        $employmentData = [
            'company_name' => trim($this->input->post('company_name', TRUE)),
            'job_title' => trim($this->input->post('job_title', TRUE)),
            'start_date' => $startDate,
            'end_date' => $isCurrent ? null : ($endDate ?: null),
            'is_current' => $isCurrent,
            'description' => trim($this->input->post('description', TRUE))
        ];

        $this->Profile_model->update_employment($id, $userId, $employmentData);

        $this->session->set_flashdata('success_message', 'Employment history updated successfully.');
        redirect('profile');
    }

    public function delete_employment($id)
    {
        $this->require_login();

        $userId = $this->session->userdata('user_id');
        $employment = $this->Profile_model->get_employment_by_id($id, $userId);

        if (!$employment) {
            show_404();
        }

        $this->Profile_model->delete_employment($id, $userId);

        $this->session->set_flashdata('success_message', 'Employment history deleted successfully.');
        redirect('profile');
    }

}