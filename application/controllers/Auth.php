<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper(['url', 'form']);
        $this->load->library('form_validation');
        $this->load->model('User_model');
        $this->load->library('auth'); // ⬅️ ini wajib
    }


    public function index()
    {
        if ($this->auth->is_logged_in()) {
            redirect('dashboard');
        }

        $this->load->view('auth/login');
    }

    public function login()
    {
        if ($this->auth->is_logged_in()) {
            redirect('dashboard');
        }

        $this->form_validation->set_rules('username', 'Username', 'required');
        $this->form_validation->set_rules('password', 'Password', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('auth/login');
        } else {
            $username = $this->input->post('username');
            $password = $this->input->post('password');

            if ($this->auth->login($username, $password)) {
                redirect('dashboard');
            } else {
                $this->session->set_flashdata('error', 'Username atau password salah!');
                redirect('auth');
            }
        }
    }

    public function logout()
    {
        $this->auth->logout();
        redirect('auth');
    }
}