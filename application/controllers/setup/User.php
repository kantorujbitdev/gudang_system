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
    public function profile()
    {
        $id_user = $this->session->userdata('id_user');
        $user = $this->user->get($id_user);

        if (!$user) {
            show_404();
        }

        $this->data['title'] = 'Profil Saya';
        $this->data['user'] = $user;

        // Validasi form
        $this->form_validation->set_rules('nama', 'Nama Lengkap', 'required');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');

        // Kalau password diisi → validasi tambahan
        if ($this->input->post('password')) {
            $this->form_validation->set_rules('password', 'Password', 'min_length[6]');
            $this->form_validation->set_rules('confirm_password', 'Konfirmasi Password', 'matches[password]');
        }

        if ($this->form_validation->run() === FALSE) {
            return $this->render_view('setup/user/profile');
        }

        // Data update
        $data_update = [
            'nama' => $this->input->post('nama'),
            'email' => $this->input->post('email'),
            'telepon' => $this->input->post('telepon'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($this->input->post('password')) {
            $data_update['password_hash'] = password_hash($this->input->post('password'), PASSWORD_DEFAULT);
        }

        if ($this->user->update($id_user, $data_update)) {
            $this->session->set_flashdata('success', 'Profil berhasil diperbarui');
        } else {
            $this->session->set_flashdata('error', 'Gagal memperbarui profil');
        }

        return redirect('setup/user/profile');
    }
    public function change_password()
    {
        $this->data['title'] = 'Ubah Password';

        // Aturan validasi
        $this->form_validation->set_rules('current_password', 'Password Saat Ini', 'required|callback_check_current_password');
        $this->form_validation->set_rules('new_password', 'Password Baru', 'required|min_length[6]');
        $this->form_validation->set_rules('confirm_password', 'Konfirmasi Password Baru', 'required|matches[new_password]');

        if ($this->form_validation->run() == FALSE) {
            return $this->render_view('setup/user/change_password');
        }

        // Jika validasi sukses
        $id_user = $this->session->userdata('id_user');
        $data_update = [
            'password_hash' => password_hash($this->input->post('new_password'), PASSWORD_DEFAULT),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($this->user->update($id_user, $data_update)) {
            $this->session->set_flashdata('success', 'Password berhasil diubah');
        } else {
            $this->session->set_flashdata('error', 'Gagal mengubah password');
        }

        return redirect('dashboard');
    }

    // Callback untuk validasi password saat ini
    public function check_current_password($current_password)
    {
        $id_user = $this->session->userdata('id_user');
        $user = $this->user->get($id_user);

        if ($user && password_verify($current_password, $user->password_hash)) {
            return TRUE;
        }

        $this->form_validation->set_message('check_current_password', 'Password saat ini salah');
        return FALSE;
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

        // Redirect berdasarkan role yang ditambahkan
        $role_redirect = $this->input->post('id_role');
        if ($role_redirect == 3) {
            return redirect('setup/user/sales');
        } elseif ($role_redirect == 4) {
            return redirect('setup/user/packing');
        } elseif ($role_redirect == 5) {
            return redirect('setup/user/retur');
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

        // Redirect berdasarkan role yang diedit
        $role_redirect = $this->input->post('id_role');
        if ($role_redirect == 3) {
            return redirect('setup/user/sales');
        } elseif ($role_redirect == 4) {
            return redirect('setup/user/packing');
        } elseif ($role_redirect == 5) {
            return redirect('setup/user/retur');
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

        // Redirect berdasarkan role user yang dinonaktifkan
        if ($user->id_role == 3) {
            return redirect('setup/user/sales');
        } elseif ($user->id_role == 4) {
            return redirect('setup/user/packing');
        } elseif ($user->id_role == 5) {
            return redirect('setup/user/retur');
        }
        return redirect('setup/user');
    }

    public function sales()
    {
        $this->data['title'] = 'Manajemen Sales';

        // Hanya Super Admin dan Admin Perusahaan yang bisa akses
        if ($this->session->userdata('id_role') != 1 && $this->session->userdata('id_role') != 2) {
            show_404();
        }

        // Ambil data user dengan role Sales (id_role = 3)
        $this->data['users'] = $this->user->get_sales();
        $this->data['can_create'] = $this->check_permission('setup/user/sales', 'create');
        $this->data['can_edit'] = $this->check_permission('setup/user/sales', 'edit');
        $this->data['can_delete'] = $this->check_permission('setup/user/sales', 'delete');

        $this->render_view('setup/user/sales');
    }

    public function packing()
    {
        $this->data['title'] = 'Manajemen Admin Packing';

        // Hanya Super Admin dan Admin Perusahaan yang bisa akses
        if ($this->session->userdata('id_role') != 1 && $this->session->userdata('id_role') != 2) {
            show_404();
        }

        // Ambil data user dengan role Admin Packing (id_role = 4)
        $this->data['users'] = $this->user->get_packing();
        $this->data['can_create'] = $this->check_permission('setup/user/packing', 'create');
        $this->data['can_edit'] = $this->check_permission('setup/user/packing', 'edit');
        $this->data['can_delete'] = $this->check_permission('setup/user/packing', 'delete');

        $this->render_view('setup/user/packing');
    }

    public function retur()
    {
        $this->data['title'] = 'Manajemen Admin Retur';

        // Hanya Super Admin dan Admin Perusahaan yang bisa akses
        if ($this->session->userdata('id_role') != 1 && $this->session->userdata('id_role') != 2) {
            show_404();
        }

        // Ambil data user dengan role Admin Retur (id_role = 5)
        $this->data['users'] = $this->user->get_retur();
        $this->data['can_create'] = $this->check_permission('setup/user/retur', 'create');
        $this->data['can_edit'] = $this->check_permission('setup/user/retur', 'edit');
        $this->data['can_delete'] = $this->check_permission('setup/user/retur', 'delete');

        $this->render_view('setup/user/retur');
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

        // Perusahaan wajib diisi kecuali untuk Super Admin
        $user_role = $this->session->userdata('id_role');
        if ($user_role != 1) { // Bukan Super Admin
            $this->form_validation->set_rules('id_perusahaan', 'Perusahaan', 'required');
        }
    }

    // Callback untuk validasi username
    public function check_username($username, $id_user)
    {
        if ($this->user->check_unique_username($username, $id_user)) {
            return TRUE;
        } else {
            $this->form_validation->set_message('check_username', '{field} sudah digunakan');
            return FALSE;
        }
    }

    // Callback untuk validasi email
    public function check_email($email, $id_user)
    {
        if ($this->user->check_unique_email($email, $id_user)) {
            return TRUE;
        } else {
            $this->form_validation->set_message('check_email', '{field} sudah digunakan');
            return FALSE;
        }
    }
}