<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('auth');
        $this->load->model('user_model');
        $this->load->model('role_model');
        $this->load->model('gudang_model');
        $this->load->library('form_validation');

        if (!$this->auth->is_logged_in()) {
            redirect('auth');
        }
    }

    public function sales()
    {
        if (!$this->auth->has_permission('user/sales')) {
            show_error('Anda tidak memiliki akses ke halaman ini', 403);
        }

        $data['title'] = 'User Sales';
        $data['content'] = 'user/sales';
        $data['users'] = $this->user_model->get_by_role(3); // Role Sales Online

        $this->load->view('template/template', $data);
    }

    public function tambah_sales()
    {
        if (!$this->auth->has_permission('user/sales')) {
            show_error('Anda tidak memiliki akses ke halaman ini', 403);
        }

        $data['title'] = 'Tambah User Sales';
        $data['content'] = 'user/tambah_sales';
        $data['perusahaan'] = $this->user_model->get_perusahaan();

        $this->form_validation->set_rules('nama', 'Nama', 'required');
        $this->form_validation->set_rules('username', 'Username', 'required|is_unique[user.username]');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
        $this->form_validation->set_rules('password_confirm', 'Konfirmasi Password', 'required|matches[password]');
        $this->form_validation->set_rules('id_perusahaan', 'Perusahaan', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('template/template', $data);
        } else {
            $data_user = array(
                'nama' => $this->input->post('nama'),
                'username' => $this->input->post('username'),
                'password_hash' => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
                'id_role' => 3, // Role Sales Online
                'id_perusahaan' => $this->input->post('id_perusahaan'),
                'created_by' => $this->session->userdata('id_user'),
                'aktif' => 1
            );

            $this->user_model->insert($data_user);
            $this->session->set_flashdata('success', 'User Sales berhasil ditambahkan!');
            redirect('user/sales');
        }
    }

    public function edit_sales($id)
    {
        if (!$this->auth->has_permission('user/sales')) {
            show_error('Anda tidak memiliki akses ke halaman ini', 403);
        }

        $data['title'] = 'Edit User Sales';
        $data['content'] = 'user/edit_sales';
        $data['user'] = $this->user_model->get_by_id($id);
        $data['perusahaan'] = $this->user_model->get_perusahaan();

        if (!$data['user'] || $data['user']->id_role != 3) {
            show_404();
        }

        $this->form_validation->set_rules('nama', 'Nama', 'required');
        $this->form_validation->set_rules('username', 'Username', 'required|callback_check_username[' . $id . ']');
        $this->form_validation->set_rules('id_perusahaan', 'Perusahaan', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('template/template', $data);
        } else {
            $data_user = array(
                'nama' => $this->input->post('nama'),
                'username' => $this->input->post('username'),
                'id_perusahaan' => $this->input->post('id_perusahaan'),
                'aktif' => $this->input->post('aktif')
            );

            // Update password if provided
            if ($this->input->post('password')) {
                $data_user['password_hash'] = password_hash($this->input->post('password'), PASSWORD_DEFAULT);
            }

            $this->user_model->update($id, $data_user);
            $this->session->set_flashdata('success', 'User Sales berhasil diperbarui!');
            redirect('user/sales');
        }
    }

    public function packing()
    {
        if (!$this->auth->has_permission('user/packing')) {
            show_error('Anda tidak memiliki akses ke halaman ini', 403);
        }

        $data['title'] = 'User Admin Packing';
        $data['content'] = 'user/packing';
        $data['users'] = $this->user_model->get_by_role(4); // Role Admin Packing

        $this->load->view('template/template', $data);
    }

    public function tambah_packing()
    {
        if (!$this->auth->has_permission('user/packing')) {
            show_error('Anda tidak memiliki akses ke halaman ini', 403);
        }

        $data['title'] = 'Tambah User Admin Packing';
        $data['content'] = 'user/tambah_packing';
        $data['perusahaan'] = $this->user_model->get_perusahaan();
        $data['gudang'] = $this->gudang_model->get_all();

        $this->form_validation->set_rules('nama', 'Nama', 'required');
        $this->form_validation->set_rules('username', 'Username', 'required|is_unique[user.username]');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
        $this->form_validation->set_rules('password_confirm', 'Konfirmasi Password', 'required|matches[password]');
        $this->form_validation->set_rules('id_perusahaan', 'Perusahaan', 'required');
        $this->form_validation->set_rules('id_gudang', 'Gudang', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('template/template', $data);
        } else {
            $data_user = array(
                'nama' => $this->input->post('nama'),
                'username' => $this->input->post('username'),
                'password_hash' => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
                'id_role' => 4, // Role Admin Packing
                'id_perusahaan' => $this->input->post('id_perusahaan'),
                'id_gudang' => $this->input->post('id_gudang'),
                'created_by' => $this->session->userdata('id_user'),
                'aktif' => 1
            );

            $this->user_model->insert($data_user);
            $this->session->set_flashdata('success', 'User Admin Packing berhasil ditambahkan!');
            redirect('user/packing');
        }
    }

    public function edit_packing($id)
    {
        if (!$this->auth->has_permission('user/packing')) {
            show_error('Anda tidak memiliki akses ke halaman ini', 403);
        }

        $data['title'] = 'Edit User Admin Packing';
        $data['content'] = 'user/edit_packing';
        $data['user'] = $this->user_model->get_by_id($id);
        $data['perusahaan'] = $this->user_model->get_perusahaan();
        $data['gudang'] = $this->gudang_model->get_all();

        if (!$data['user'] || $data['user']->id_role != 4) {
            show_404();
        }

        $this->form_validation->set_rules('nama', 'Nama', 'required');
        $this->form_validation->set_rules('username', 'Username', 'required|callback_check_username[' . $id . ']');
        $this->form_validation->set_rules('id_perusahaan', 'Perusahaan', 'required');
        $this->form_validation->set_rules('id_gudang', 'Gudang', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('template/template', $data);
        } else {
            $data_user = array(
                'nama' => $this->input->post('nama'),
                'username' => $this->input->post('username'),
                'id_perusahaan' => $this->input->post('id_perusahaan'),
                'id_gudang' => $this->input->post('id_gudang'),
                'aktif' => $this->input->post('aktif')
            );

            // Update password if provided
            if ($this->input->post('password')) {
                $data_user['password_hash'] = password_hash($this->input->post('password'), PASSWORD_DEFAULT);
            }

            $this->user_model->update($id, $data_user);
            $this->session->set_flashdata('success', 'User Admin Packing berhasil diperbarui!');
            redirect('user/packing');
        }
    }

    public function check_username($username, $id)
    {
        $this->db->where('username', $username);
        $this->db->where('id_user !=', $id);
        $query = $this->db->get('user');

        if ($query->num_rows() > 0) {
            $this->form_validation->set_message('check_username', 'Username sudah digunakan');
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public function get_gudang_by_perusahaan()
    {
        $id_perusahaan = $this->input->post('id_perusahaan');

        $this->db->where('id_perusahaan', $id_perusahaan);
        $this->db->where('status_aktif', 1);
        $this->db->order_by('nama_gudang', 'ASC');
        $gudang = $this->db->get('gudang')->result();

        echo json_encode($gudang);
    }
}