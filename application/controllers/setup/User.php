<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('setup/User_model', 'user');
        $this->load->model('setup/Perusahaan_model', 'perusahaan');
        $this->load->model('setup/Gudang_model', 'gudang');
        $this->load->helper('form');
        $this->load->library('form_validation');

        // Cek akses menu
        $this->check_menu_access('setup/user/sales');
    }

    public function index()
    {
        $this->data['title'] = 'Manajemen User';
        $this->data['users'] = $this->user->get_all();
        $this->data['can_create'] = $this->check_permission('setup/user/sales', 'create');
        $this->data['can_edit'] = $this->check_permission('setup/user/sales', 'edit');
        $this->data['can_delete'] = $this->check_permission('setup/user/sales', 'delete');

        $this->render_view('setup/user/index');
    }

    public function tambah($role = null)
    {
        if (!$this->check_permission('setup/user/sales', 'create')) {
            $this->session->set_flashdata('error', 'Anda tidak memiliki izin untuk menambah user!');
            return redirect('setup/user');
        }

        $this->data['title'] = 'Tambah User';
        $this->data['role'] = $role ?? 3; // default Sales
        $this->data['perusahaan'] = $this->perusahaan->get_active();
        $this->data['gudang'] = $this->gudang->get_all();

        $this->_set_validation_rules();

        if ($this->form_validation->run() == FALSE) {
            return $this->render_view('setup/user/form');
        }

        $data_insert = [
            'nama' => $this->input->post('nama'),
            'username' => $this->input->post('username'),
            'email' => $this->input->post('email'),
            'telepon' => $this->input->post('telepon'),
            'id_role' => $this->input->post('id_role'),
            'id_perusahaan' => $this->input->post('id_perusahaan'),
            'password_hash' => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
            'created_by' => $this->session->userdata('id_user'),
            'aktif' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ];

        if ($this->user->insert($data_insert)) {
            $this->session->set_flashdata('success', 'User berhasil ditambahkan');
        } else {
            $this->session->set_flashdata('error', 'Gagal menambahkan user!');
        }
        return redirect('setup/user');
    }

    public function edit($id_user)
    {
        if (!$this->check_permission('setup/user/sales', 'edit')) {
            $this->session->set_flashdata('error', 'Anda tidak memiliki izin untuk mengubah user!');
            return redirect('setup/user');
        }

        $this->data['title'] = 'Edit User';
        $this->data['user'] = $this->user->get($id_user);
        $this->data['perusahaan'] = $this->perusahaan->get_active();
        $this->data['gudang'] = $this->gudang->get_all();

        if (!$this->data['user']) {
            show_404();
        }

        $this->_set_validation_rules($id_user);

        if ($this->form_validation->run() == FALSE) {
            return $this->render_view('setup/user/form_edit');
        }

        $data_update = [
            'nama' => $this->input->post('nama'),
            'username' => $this->input->post('username'),
            'email' => $this->input->post('email'),
            'telepon' => $this->input->post('telepon'),
            'id_role' => $this->input->post('id_role'),
            'id_perusahaan' => $this->input->post('id_perusahaan'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($this->input->post('password')) {
            $data_update['password_hash'] = password_hash($this->input->post('password'), PASSWORD_DEFAULT);
        }

        if ($this->user->update($id_user, $data_update)) {
            $this->session->set_flashdata('success', 'User berhasil diperbarui');
        } else {
            $this->session->set_flashdata('error', 'Gagal memperbarui user!');
        }
        return redirect('setup/user');
    }

    public function aktif($id_user)
    {
        if ($this->user->update_status($id_user, 1)) {
            $this->session->set_flashdata('success', 'User berhasil diaktifkan');
        } else {
            $this->session->set_flashdata('error', 'Gagal mengaktifkan user');
        }
        return redirect('setup/user');
    }

    public function nonaktif($id_user)
    {
        $user = $this->user->get($id_user);
        if (!$user) {
            show_404();
        }

        if ($this->user->update_status($id_user, 0)) {
            $this->session->set_flashdata('success', 'User berhasil dinonaktifkan');
        } else {
            $this->session->set_flashdata('error', 'Gagal menonaktifkan user');
        }

        if ($user->id_role == 3) {
            return redirect('setup/user/sales');
        } elseif ($user->id_role == 4) {
            return redirect('setup/user/packing');
        }
        return redirect('setup/user');
    }

    /** ====================
     * Helper Rules
     * ==================== */
    private function _set_validation_rules($id_user = null)
    {
        $this->form_validation->set_rules('nama', 'Nama Lengkap', 'required|trim|max_length[100]');
        if ($id_user) {
            $this->form_validation->set_rules('username', 'Username', 'required|trim|callback_check_username[' . $id_user . ']|min_length[5]|max_length[50]');
            $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|callback_check_email[' . $id_user . ']');
        } else {
            $this->form_validation->set_rules('username', 'Username', 'required|trim|is_unique[user.username]|min_length[5]|max_length[50]');
            $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|is_unique[user.email]');
            $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
            $this->form_validation->set_rules('confirm_password', 'Konfirmasi Password', 'required|matches[password]');
        }
        $this->form_validation->set_rules('telepon', 'Telepon', 'trim|max_length[20]');
        $this->form_validation->set_rules('id_role', 'Role', 'required');
        $this->form_validation->set_rules('id_perusahaan', 'Perusahaan', 'required');
    }
}
