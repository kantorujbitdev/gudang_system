<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Hak_akses extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Pengaturan/Hak_akses_model', 'hak_akses');
        $this->load->model('Menu_model');
        $this->load->helper('form');
        $this->load->library('form_validation');

        // Cek akses menu
        $this->check_menu_access('pengaturan/hak_akses');
    }

    public function index()
    {
        $this->data['title'] = 'Hak Akses Menu';
        $this->data['roles'] = $this->hak_akses->get_all_roles();
        $this->data['menus'] = $this->Menu_model->get_menu_tree();
        $this->data['hak_akses'] = $this->hak_akses->get_all_hak_akses();
        $this->data['can_edit'] = $this->check_permission('pengaturan/hak_akses', 'edit');

        $this->render_view('pengaturan/hak_akses/index');
    }

    public function update()
    {
        // Check permission
        if (!$this->check_permission('pengaturan/hak_akses', 'edit')) {
            $this->data['error'] = 'Anda tidak memiliki izin untuk mengubah hak akses!';
            redirect('pengaturan/hak_akses');
        }

        if ($this->input->post()) {
            $id_role = $this->input->post('id_role');
            $id_menus = $this->input->post('id_menu');
            $can_view = $this->input->post('can_view');
            $can_create = $this->input->post('can_create');
            $can_edit = $this->input->post('can_edit');
            $can_delete = $this->input->post('can_delete');

            // Delete existing hak akses for this role
            $this->hak_akses->delete_by_role($id_role);

            // Insert new hak akses
            foreach ($id_menus as $key => $id_menu) {
                $data = [
                    'id_role' => $id_role,
                    'id_menu' => $id_menu,
                    'can_view' => isset($can_view[$key]) ? 1 : 0,
                    'can_create' => isset($can_create[$key]) ? 1 : 0,
                    'can_edit' => isset($can_edit[$key]) ? 1 : 0,
                    'can_delete' => isset($can_delete[$key]) ? 1 : 0
                ];

                $this->hak_akses->insert($data);
            }

            $this->data['success'] = 'Hak akses berhasil diperbarui';
            redirect('pengaturan/hak_akses');
        }
    }

    public function role()
    {
        // Check permission
        if (!$this->check_permission('pengaturan/hak_akses', 'edit')) {
            $this->data['error'] = 'Anda tidak memiliki izin untuk mengelola role!';
            redirect('pengaturan/hak_akses');
        }

        $this->data['title'] = 'Manajemen Role';
        $this->data['roles'] = $this->hak_akses->get_all_roles();

        $this->render_view('pengaturan/hak_akses/role');
    }

    public function tambah_role()
    {
        // Check permission
        if (!$this->check_permission('pengaturan/hak_akses', 'edit')) {
            $this->data['error'] = 'Anda tidak memiliki izin untuk menambah role!';
            redirect('pengaturan/hak_akses/role');
        }

        $this->data['title'] = 'Tambah Role';

        $this->form_validation->set_rules('nama_role', 'Nama Role', 'required|trim|max_length[50]');
        $this->form_validation->set_rules('deskripsi', 'Deskripsi', 'trim');

        if ($this->form_validation->run() == FALSE) {
            $this->render_view('pengaturan/hak_akses/form_role');
        } else {
            $data_insert = [
                'nama_role' => $this->input->post('nama_role'),
                'deskripsi' => $this->input->post('deskripsi')
            ];

            if ($this->hak_akses->insert_role($data_insert)) {
                $this->data['success'] = 'Role berhasil ditambahkan';
                redirect('pengaturan/hak_akses/role');
            } else {
                $this->data['error'] = 'Gagal menambahkan role!';
                $this->render_view('pengaturan/hak_akses/form_role');
            }
        }
    }

    public function edit_role($id_role)
    {
        // Check permission
        if (!$this->check_permission('pengaturan/hak_akses', 'edit')) {
            $this->data['error'] = 'Anda tidak memiliki izin untuk mengubah role!';
            redirect('pengaturan/hak_akses/role');
        }

        $this->data['title'] = 'Edit Role';
        $this->data['role'] = $this->hak_akses->get_role($id_role);

        if (!$this->data['role']) {
            show_404();
        }

        $this->form_validation->set_rules('nama_role', 'Nama Role', 'required|trim|max_length[50]');
        $this->form_validation->set_rules('deskripsi', 'Deskripsi', 'trim');

        if ($this->form_validation->run() == FALSE) {
            $this->render_view('pengaturan/hak_akses/form_role');
        } else {
            $data_update = [
                'nama_role' => $this->input->post('nama_role'),
                'deskripsi' => $this->input->post('deskripsi')
            ];

            if ($this->hak_akses->update_role($id_role, $data_update)) {
                $this->data['success'] = 'Role berhasil diperbarui';
                redirect('pengaturan/hak_akses/role');
            } else {
                $this->data['error'] = 'Gagal memperbarui role!';
                $this->render_view('pengaturan/hak_akses/form_role');
            }
        }
    }

    public function hapus_role($id_role)
    {
        // Check permission
        if (!$this->check_permission('pengaturan/hak_akses', 'edit')) {
            $this->data['error'] = 'Anda tidak memiliki izin untuk menghapus role!';
            redirect('pengaturan/hak_akses/role');
        }

        // Prevent deleting default roles
        if ($id_role <= 5) {
            $this->data['error'] = 'Role default tidak dapat dihapus!';
            redirect('pengaturan/hak_akses/role');
        }

        // Check if role is used by users
        $users_count = $this->hak_akses->count_users_by_role($id_role);
        if ($users_count > 0) {
            $this->data['error'] = 'Role ini masih digunakan oleh ' . $users_count . ' user!';
            redirect('pengaturan/hak_akses/role');
        }

        if ($this->hak_akses->delete_role($id_role)) {
            // Delete hak akses for this role
            $this->hak_akses->delete_by_role($id_role);

            $this->data['success'] = 'Role berhasil dihapus';
        } else {
            $this->data['error'] = 'Gagal menghapus role!';
        }
        redirect('pengaturan/hak_akses/role');
    }
}