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
        // Check permission
        if (!$this->check_permission('setup/user/sales', 'create')) {
            $this->session->set_flashdata('error', 'Anda tidak memiliki izin untuk menambah user!');
            redirect('setup/user');
        }

        $this->data['title'] = 'Tambah User';
        $this->data['role'] = $role;
        $this->data['perusahaan'] = $this->perusahaan->get_active();
        $this->data['gudang'] = $this->gudang->get_all();

        // Default role
        if (!$role) {
            $role = 3; // Default to Sales
        }

        $this->form_validation->set_rules('nama', 'Nama Lengkap', 'required|trim|max_length[100]');
        $this->form_validation->set_rules('username', 'Username', 'required|trim|is_unique[user.username]|min_length[5]|max_length[50]');
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|is_unique[user.email]');
        $this->form_validation->set_rules('telepon', 'Telepon', 'trim|max_length[20]');
        $this->form_validation->set_rules('id_role', 'Role', 'required');
        $this->form_validation->set_rules('id_perusahaan', 'Perusahaan', 'required');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
        $this->form_validation->set_rules('confirm_password', 'Konfirmasi Password', 'required|matches[password]');

        if ($this->form_validation->run() == FALSE) {
            $this->render_view('setup/user/form');
        } else {
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
                redirect('setup/user');
            } else {
                $this->session->set_flashdata('error', 'Gagal menambahkan user!');
                $this->render_view('setup/user/form');
            }
        }
    }

    public function edit($id_user)
    {
        // Check permission
        if (!$this->check_permission('setup/user/sales', 'edit')) {
            $this->session->set_flashdata('error', 'Anda tidak memiliki izin untuk mengubah data user!');
            redirect('setup/user');
        }

        $this->data['title'] = 'Edit User';
        $this->data['user'] = $this->user->get($id_user);
        $this->data['perusahaan'] = $this->perusahaan->get_active();
        $this->data['gudang'] = $this->gudang->get_all();

        if (!$this->data['user']) {
            show_404();
        }

        $this->form_validation->set_rules('nama', 'Nama Lengkap', 'required|trim|max_length[100]');
        $this->form_validation->set_rules('username', 'Username', 'required|trim|callback_check_username[' . $id_user . ']|min_length[5]|max_length[50]');
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|callback_check_email[' . $id_user . ']');
        $this->form_validation->set_rules('telepon', 'Telepon', 'trim|max_length[20]');
        $this->form_validation->set_rules('id_role', 'Role', 'required');
        $this->form_validation->set_rules('id_perusahaan', 'Perusahaan', 'required');

        if ($this->input->post('password')) {
            $this->form_validation->set_rules('password', 'Password', 'min_length[6]');
            $this->form_validation->set_rules('confirm_password', 'Konfirmasi Password', 'matches[password]');
        }

        if ($this->form_validation->run() == FALSE) {
            $this->render_view('setup/user/form_edit');
        } else {
            $data_update = [
                'nama' => $this->input->post('nama'),
                'username' => $this->input->post('username'),
                'email' => $this->input->post('email'),
                'telepon' => $this->input->post('telepon'),
                'id_role' => $this->input->post('id_role'),
                'id_perusahaan' => $this->input->post('id_perusahaan'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Update password if provided
            if ($this->input->post('password')) {
                $data_update['password_hash'] = password_hash($this->input->post('password'), PASSWORD_DEFAULT);
            }

            if ($this->user->update($id_user, $data_update)) {
                $this->session->set_flashdata('success', 'User berhasil diperbarui');
                redirect('setup/user');
            } else {
                $this->session->set_flashdata('error', 'Gagal memperbarui user!');
                $this->render_view('setup/user/form_edit');
            }
        }
    }

    public function aktif($id_user)
    {
        if ($this->user->update_status($id_user, 1)) {
            $this->session->set_flashdata('success', 'User berhasil diaktifkan');
        } else {
            $this->session->set_flashdata('error', 'Gagal mengaktifkan user');
        }
        redirect('setup/user');
    }

    public function profile()
    {
        $this->data['title'] = 'Profil Saya';
        $id_user = $this->session->userdata('id_user');
        $this->data['user'] = $this->user->get($id_user);

        if (!$this->data['user']) {
            show_404();
        }

        $this->form_validation->set_rules('nama', 'Nama Lengkap', 'required|trim|max_length[100]');
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|callback_check_email[' . $id_user . ']');
        $this->form_validation->set_rules('telepon', 'Telepon', 'trim|max_length[20]');

        if ($this->input->post('password')) {
            $this->form_validation->set_rules('password', 'Password', 'min_length[6]');
            $this->form_validation->set_rules('confirm_password', 'Konfirmasi Password', 'matches[password]');
        }

        if ($this->form_validation->run() == FALSE) {
            $this->render_view('setup/user/profile');
        } else {
            $data_update = [
                'nama' => $this->input->post('nama'),
                'email' => $this->input->post('email'),
                'telepon' => $this->input->post('telepon'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Update password if provided
            if ($this->input->post('password')) {
                $data_update['password_hash'] = password_hash($this->input->post('password'), PASSWORD_DEFAULT);
            }

            if ($this->user->update($id_user, $data_update)) {
                // Update session data
                $this->session->set_userdata('nama', $data_update['nama']);

                $this->session->set_flashdata('success', 'Profil berhasil diperbarui');
                redirect('setup/user/profile');
            } else {
                $this->session->set_flashdata('error', 'Gagal memperbarui profil!');
                $this->render_view('setup/user/profile');
            }
        }
    }

    public function change_password()
    {
        $this->data['title'] = 'Ubah Password';
        $id_user = $this->session->userdata('id_user');
        $this->data['user'] = $this->user->get($id_user);

        if (!$this->data['user']) {
            show_404();
        }

        $this->form_validation->set_rules('current_password', 'Password Saat Ini', 'required|callback_check_current_password');
        $this->form_validation->set_rules('new_password', 'Password Baru', 'required|min_length[6]');
        $this->form_validation->set_rules('confirm_password', 'Konfirmasi Password Baru', 'required|matches[new_password]');

        if ($this->form_validation->run() == FALSE) {
            $this->render_view('setup/user/change_password');
        } else {
            $data_update = [
                'password_hash' => password_hash($this->input->post('new_password'), PASSWORD_DEFAULT),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            if ($this->user->update($id_user, $data_update)) {
                $this->session->set_flashdata('success', 'Password berhasil diubah');
                redirect('setup/user/change_password');
            } else {
                $this->session->set_flashdata('error', 'Gagal mengubah password!');
                $this->render_view('setup/user/change_password');
            }
        }
    }

    public function check_username($username, $id_user)
    {
        $user = $this->user->get_by_username($username);

        if ($user && $user->id_user != $id_user) {
            $this->form_validation->set_message('check_username', 'Username sudah digunakan oleh user lain!');
            return FALSE;
        }

        return TRUE;
    }

    public function check_email($email, $id_user)
    {
        $user = $this->user->get_by_email($email);

        if ($user && $user->id_user != $id_user) {
            $this->form_validation->set_message('check_email', 'Email sudah digunakan oleh user lain!');
            return FALSE;
        }

        return TRUE;
    }

    public function check_current_password($password)
    {
        $id_user = $this->session->userdata('id_user');
        $user = $this->user->get($id_user);

        if (!password_verify($password, $user->password_hash)) {
            $this->form_validation->set_message('check_current_password', 'Password saat ini tidak sesuai!');
            return FALSE;
        }

        return TRUE;
    }

    public function sales()
    {
        $this->data['title'] = 'Manajemen Sales';
        $id_perusahaan = $this->session->userdata('id_role') == 1 ? NULL : $this->session->userdata('id_perusahaan');
        $this->data['user'] = $this->User_model->get_sales($id_perusahaan);

        $this->render_view('setup/user/sales');
    }

    public function packing()
    {
        $this->data['title'] = 'Manajemen Admin Packing';
        $id_perusahaan = $this->session->userdata('id_role') == 1 ? NULL : $this->session->userdata('id_perusahaan');
        $this->data['user'] = $this->User_model->get_packing($id_perusahaan);

        $this->render_view('setup/user/packing');
    }

    public function tambah_sales()
    {
        $this->data['title'] = 'Tambah Sales';
        $this->data['role'] = 3; // Sales role ID

        if ($this->session->userdata('id_role') == 1) {
            $this->data['perusahaan'] = $this->Perusahaan_model->get_active();
        }

        if ($this->input->post()) {
            $this->form_validation->set_rules('nama', 'Nama Lengkap', 'required|trim|max_length[100]');
            $this->form_validation->set_rules('username', 'Username', 'required|trim|min_length[5]|max_length[50]|alpha_numeric');
            $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|max_length[100]');
            $this->form_validation->set_rules('telepon', 'Telepon', 'trim|max_length[20]');
            $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
            $this->form_validation->set_rules('confirm_password', 'Konfirmasi Password', 'required|matches[password]');

            // Jika Super Admin, validasi perusahaan
            if ($this->session->userdata('id_role') == 1) {
                $this->form_validation->set_rules('id_perusahaan', 'Perusahaan', 'required');
            }

            if ($this->form_validation->run() == TRUE) {
                $username = $this->input->post('username');
                $email = $this->input->post('email');
                $id_perusahaan = $this->session->userdata('id_role') == 1 ?
                    $this->input->post('id_perusahaan') :
                    $this->session->userdata('id_perusahaan');

                // Check unique username
                if (!$this->User_model->check_unique_username($username)) {
                    $this->data['error'] = 'Username sudah digunakan!';
                    redirect('setup/user/tambah_sales');
                }

                // Check unique email
                if (!$this->User_model->check_unique_email($email)) {
                    $this->data['error'] = 'Email sudah digunakan!';
                    redirect('setup/user/tambah_sales');
                }

                // Handle upload foto profil
                $foto_profil = '';
                if (!empty($_FILES['foto_profil']['name'])) {
                    $config['upload_path'] = './uploads/user/';
                    $config['allowed_types'] = 'jpg|jpeg|png|gif';
                    $config['max_size'] = 1024; // 1MB
                    $config['file_name'] = time() . '_' . $_FILES['foto_profil']['name'];

                    $this->upload->initialize($config);

                    if ($this->upload->do_upload('foto_profil')) {
                        $upload_data = $this->upload->data();
                        $foto_profil = $upload_data['file_name'];
                    }
                }

                $data = array(
                    'nama' => $this->input->post('nama'),
                    'username' => $username,
                    'password_hash' => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
                    'email' => $email,
                    'telepon' => $this->input->post('telepon'),
                    'id_role' => 3, // Sales role
                    'id_perusahaan' => $id_perusahaan,
                    'created_by' => $this->session->userdata('id_user'),
                    'foto_profil' => $foto_profil,
                    'aktif' => 1
                );

                if ($this->User_model->insert($data)) {
                    $this->data['success'] = 'Sales berhasil ditambahkan!';
                    redirect('setup/user/sales');
                } else {
                    $this->data['error'] = 'Gagal menambahkan sales!';
                    redirect('setup/user/tambah_sales');
                }
            }
        }

        $this->render_view('setup/user/form_sales');
    }

    public function tambah_packing()
    {
        $this->data['title'] = 'Tambah Admin Packing';
        $this->data['role'] = 4; // Admin Packing role ID

        if ($this->session->userdata('id_role') == 1) {
            $this->data['perusahaan'] = $this->Perusahaan_model->get_active();
        }

        if ($this->input->post()) {
            $this->form_validation->set_rules('nama', 'Nama Lengkap', 'required|trim|max_length[100]');
            $this->form_validation->set_rules('username', 'Username', 'required|trim|min_length[5]|max_length[50]|alpha_numeric');
            $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|max_length[100]');
            $this->form_validation->set_rules('telepon', 'Telepon', 'trim|max_length[20]');
            $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
            $this->form_validation->set_rules('confirm_password', 'Konfirmasi Password', 'required|matches[password]');

            // Jika Super Admin, validasi perusahaan
            if ($this->session->userdata('id_role') == 1) {
                $this->form_validation->set_rules('id_perusahaan', 'Perusahaan', 'required');
            }

            if ($this->form_validation->run() == TRUE) {
                $username = $this->input->post('username');
                $email = $this->input->post('email');
                $id_perusahaan = $this->session->userdata('id_role') == 1 ?
                    $this->input->post('id_perusahaan') :
                    $this->session->userdata('id_perusahaan');

                // Check unique username
                if (!$this->User_model->check_unique_username($username)) {
                    $this->data['error'] = 'Username sudah digunakan!';
                    redirect('setup/user/tambah_packing');
                }

                // Check unique email
                if (!$this->User_model->check_unique_email($email)) {
                    $this->data['error'] = 'Email sudah digunakan!';
                    redirect('setup/user/tambah_packing');
                }

                // Handle upload foto profil
                $foto_profil = '';
                if (!empty($_FILES['foto_profil']['name'])) {
                    $config['upload_path'] = './uploads/user/';
                    $config['allowed_types'] = 'jpg|jpeg|png|gif';
                    $config['max_size'] = 1024; // 1MB
                    $config['file_name'] = time() . '_' . $_FILES['foto_profil']['name'];

                    $this->upload->initialize($config);

                    if ($this->upload->do_upload('foto_profil')) {
                        $upload_data = $this->upload->data();
                        $foto_profil = $upload_data['file_name'];
                    }
                }

                $data = array(
                    'nama' => $this->input->post('nama'),
                    'username' => $username,
                    'password_hash' => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
                    'email' => $email,
                    'telepon' => $this->input->post('telepon'),
                    'id_role' => 4, // Admin Packing role
                    'id_perusahaan' => $id_perusahaan,
                    'created_by' => $this->session->userdata('id_user'),
                    'foto_profil' => $foto_profil,
                    'aktif' => 1
                );

                if ($this->User_model->insert($data)) {
                    $this->data['success'] = 'Admin Packing berhasil ditambahkan!';
                    redirect('setup/user/packing');
                } else {
                    $this->data['error'] = 'Gagal menambahkan admin packing!';
                    redirect('setup/user/tambah_packing');
                }
            }
        }

        $this->render_view('setup/user/form_packing');
    }

    public function reset_password($id_user)
    {
        $this->data['title'] = 'Reset Password';
        $this->data['user'] = $this->User_model->get($id_user);

        if (!$this->data['user']) {
            show_404();
        }

        // Cek apakah user memiliki akses untuk reset password user ini
        $user_role = $this->session->userdata('id_role');
        if ($user_role != 1 && $this->data['user']->id_perusahaan != $this->session->userdata('id_perusahaan')) {
            $this->data['error'] = 'Anda tidak memiliki akses untuk mereset password user ini!';
            redirect('setup/user');
        }

        if ($this->input->post()) {
            $this->form_validation->set_rules('new_password', 'Password Baru', 'required|min_length[6]');
            $this->form_validation->set_rules('confirm_password', 'Konfirmasi Password', 'required|matches[new_password]');

            if ($this->form_validation->run() == TRUE) {
                $new_password = $this->input->post('new_password');

                if ($this->User_model->reset_password($id_user, $new_password)) {
                    $this->data['success'] = 'Password berhasil direset!';

                    // Redirect berdasarkan role user
                    if ($this->data['user']->id_role == 3) {
                        redirect('setup/user/sales');
                    } elseif ($this->data['user']->id_role == 4) {
                        redirect('setup/user/packing');
                    } else {
                        redirect('setup/user');
                    }
                } else {
                    $this->data['error'] = 'Gagal mereset password!';
                    redirect('setup/user/reset_password/' . $id_user);
                }
            }
        }

        $this->render_view('setup/user/reset_password');
    }

    public function nonaktif($id)
    {
        $user = $this->User_model->get($id);

        if (!$user) {
            show_404();
        }

        // Cek apakah user memiliki akses untuk mengubah status user ini
        $user_role = $this->session->userdata('id_role');
        if ($user_role != 1 && $user->id_perusahaan != $this->session->userdata('id_perusahaan')) {
            $this->data['error'] = 'Anda tidak memiliki akses untuk mengubah status user ini!';
            redirect('setup/user');
        }

        if ($this->User_model->update_status($id, 0)) {
            $this->data['success'] = 'User berhasil dinonaktifkan';
        } else {
            $this->data['error'] = 'Gagal menonaktifkan user';
        }

        // Redirect berdasarkan role user
        if ($user->id_role == 3) {
            redirect('setup/user/sales');
        } elseif ($user->id_role == 4) {
            redirect('setup/user/packing');
        } else {
            redirect('setup/user');
        }
    }


    public function detail($id_user)
    {
        $this->data['title'] = 'Detail User';
        $this->data['user'] = $this->User_model->get($id_user);

        if (!$this->data['user']) {
            show_404();
        }

        // Cek apakah user memiliki akses untuk melihat detail user ini
        $user_role = $this->session->userdata('id_role');
        if ($user_role != 1 && $this->data['user']->id_perusahaan != $this->session->userdata('id_perusahaan')) {
            $this->data['error'] = 'Anda tidak memiliki akses untuk melihat detail user ini!';
            redirect('setup/user');
        }

        $this->render_view('setup/user/detail');
    }
}