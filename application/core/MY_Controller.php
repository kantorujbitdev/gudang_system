<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{
    protected $data = array();

    public function __construct()
    {
        parent::__construct();
        // Load library dan helper
        $this->load->library(array('session', 'form_validation'));
        $this->load->helper(array('url', 'form', 'security', 'company_filter', 'menu'));
        // Load custom library
        $this->load->library('Auth');
        $this->load->library('Menu');
        // Load model untuk menu
        $this->load->model('Menu_model');

        // Check autentikasi (kecuali untuk halaman auth)
        $current_controller = $this->router->fetch_class();
        if ($current_controller != 'auth') {
            $this->auth->check_auth();
        }

        // Set data global
        $this->set_global_data();
    }

    private function set_global_data()
    {
        // Data user
        $this->data['user'] = $this->session->all_userdata();

        // Title default
        $this->data['title'] = 'Dashboard';
    }

    protected function render_view($view, $data = NULL)
    {
        if (is_null($data)) {
            $data = $this->data;
        } else {
            $data = array_merge($this->data, $data);
        }
        $this->load->view('layout/header', $data);
        $this->load->view('layout/wording', $data);
        $this->load->view($view, $data);
        $this->load->view('layout/footer', $data);
    }

    protected function json_response($data, $status = 200)
    {
        return $this->output
            ->set_content_type('application/json')
            ->set_status_header($status)
            ->set_output(json_encode($data));
    }

    protected function check_access($allowed_roles = array())
    {
        $user_role = $this->session->userdata('id_role');

        if (!in_array($user_role, $allowed_roles)) {
            $this->session->set_flashdata('error', 'Anda tidak memiliki akses ke halaman ini!');
            redirect('dashboard');
        }
    }

    protected function check_menu_access($menu_url)
    {
        $user_role = $this->session->userdata('id_role');

        if (!$this->Menu_model->check_access($user_role, $menu_url)) {
            $this->session->set_flashdata('error', 'Anda tidak memiliki akses ke menu ini!');
            redirect('dashboard');
        }
    }
}