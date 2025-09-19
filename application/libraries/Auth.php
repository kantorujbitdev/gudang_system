<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth
{
    protected $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->model('Auth_model');
        $this->CI->load->library('session');
    }

    public function check_auth()
    {
        // Exception untuk halaman login dan forgot password
        $exception = array('auth/login', 'auth/forgot_password', 'auth/logout');

        if (in_array(uri_string(), $exception)) {
            return;
        }

        if (!$this->CI->session->userdata('logged_in')) {
            redirect('auth/login');
        }

        // Check remember me
        if ($this->CI->input->cookie('remember_token')) {
            $token = $this->CI->input->cookie('remember_token');
            $user = $this->CI->Auth_model->get_user_by_token($token);

            if ($user) {
                $session_data = array(
                    'id_user' => $user->id_user,
                    'nama' => $user->nama,
                    'username' => $user->username,
                    'id_role' => $user->id_role,
                    'id_perusahaan' => $user->id_perusahaan,
                    'nama_perusahaan' => $user->nama_perusahaan,
                    'nama_role' => $user->nama_role,
                    'logged_in' => TRUE
                );

                $this->CI->session->set_userdata($session_data);
            } else {
                delete_cookie('remember_token');
            }
        }
    }

    public function is_super_admin()
    {
        return $this->CI->session->userdata('id_role') == 1;
    }

    public function is_admin()
    {
        return $this->CI->session->userdata('id_role') == 2;
    }

    public function is_sales()
    {
        return $this->CI->session->userdata('id_role') == 3;
    }

    public function is_packing()
    {
        return $this->CI->session->userdata('id_role') == 4;
    }

    public function is_return()
    {
        return $this->CI->session->userdata('id_role') == 5;
    }

    public function has_permission($menu_url, $permission = 'view')
    {
        $id_role = $this->CI->session->userdata('id_role');

        // Super Admin memiliki akses penuh
        if ($id_role == 1) {
            return TRUE;
        }

        $this->CI->load->model('Menu_model');
        $perm = $this->CI->Menu_model->get_permission($id_role, $menu_url);

        switch ($permission) {
            case 'view':
                return $perm->can_view == 1;
            case 'create':
                return $perm->can_create == 1;
            case 'edit':
                return $perm->can_edit == 1;
            case 'delete':
                return $perm->can_delete == 1;
            default:
                return FALSE;
        }
    }
}