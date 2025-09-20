<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Auth_model');
        $this->load->library('form_validation');
    }

    public function index()
    {
        redirect('auth/login');
    }

    public function login()
    {
        if ($this->session->userdata('logged_in')) {
            redirect('dashboard');
        }

        $this->form_validation->set_rules('username', 'Username', 'required|trim');
        $this->form_validation->set_rules('password', 'Password', 'required|trim');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('auth/login');
        } else {
            $username = $this->input->post('username');
            $password = $this->input->post('password');
            $remember = $this->input->post('remember');

            $user = $this->Auth_model->login($username, $password);
            $role = $this->db->get_where('role_user', ['id_role' => $user->id_role])->row();

            if ($user) {
                if ($user->aktif == 1) {
                    $session_data = array(
                        'id_user' => $user->id_user,
                        'nama' => $user->nama,
                        'username' => $user->username,
                        'id_role' => $user->id_role,
                        'nama_role' => $role ? $role->nama_role : null,
                        'id_perusahaan' => $user->id_perusahaan,
                        'nama_perusahaan' => $user->nama_perusahaan,
                        'logged_in' => TRUE
                    );

                    $this->session->set_userdata($session_data);

                    // Update last login
                    $this->Auth_model->update_last_login($user->id_user);

                    // Set remember me cookie
                    if ($remember) {
                        $token = bin2hex(random_bytes(32));
                        $this->Auth_model->set_remember_token($user->id_user, $token);
                        set_cookie('remember_token', $token, 86400 * 30); // 30 days
                    }

                    $this->session->set_flashdata('success', 'Login berhasil!');
                    redirect('dashboard');
                } else {
                    $this->session->set_flashdata('error', 'Akun Anda tidak aktif!');
                    redirect('auth/login');
                }
            } else {
                $this->session->set_flashdata('error', 'Username atau password salah!');
                redirect('auth/login');
            }
        }
    }

    public function logout()
    {
        // Clear remember token
        if ($this->input->cookie('remember_token')) {
            $token = $this->input->cookie('remember_token');
            $this->Auth_model->clear_remember_token($token);
            delete_cookie('remember_token');
        }

        $this->session->sess_destroy();
        redirect('auth/login');
    }

    public function forgot_password()
    {
        // Implementasi lupa password
        $this->load->view('auth/forgot_password');
    }
}