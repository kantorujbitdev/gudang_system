<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->model('setup/Perusahaan_model');
        $this->load->model('Role_user_model');
        $this->load->helper('form');
        $this->load->library('form_validation');
        $this->load->library('upload');

        // Cek akses menu
        $this->check_menu_access('setup/user');
    }

    public function index()
    {
        $this->data['title'] = 'Manajemen User';
        $this->data['user'] = $this->User_model->get_all();

        $this->render_view('setup/user/index');
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

    public function edit($id_user)
    {
        $this->data['title'] = 'Edit User';
        $this->data['user'] = $this->User_model->get($id_user);

        if (!$this->data['user']) {
            show_404();
        }

        // Cek apakah user memiliki akses untuk mengedit user ini
        $user_role = $this->session->userdata('id_role');
        if ($user_role != 1 && $this->data['user']->id_perusahaan != $this->session->userdata('id_perusahaan')) {
            $this->data['error'] = 'Anda tidak memiliki akses untuk mengedit user ini!';
            redirect('setup/user');
        }

        if ($this->session->userdata('id_role') == 1) {
            $this->data['perusahaan'] = $this->Perusahaan_model->get_active();
        }

        if ($this->input->post()) {
            $this->form_validation->set_rules('nama', 'Nama Lengkap', 'required|trim|max_length[100]');
            $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|max_length[100]');
            $this->form_validation->set_rules('telepon', 'Telepon', 'trim|max_length[20]');

            // Jika Super Admin, validasi perusahaan
            if ($this->session->userdata('id_role') == 1) {
                $this->form_validation->set_rules('id_perusahaan', 'Perusahaan', 'required');
            }

            // Validasi password hanya jika diisi
            if (!empty($this->input->post('password'))) {
                $this->form_validation->set_rules('password', 'Password', 'min_length[6]');
                $this->form_validation->set_rules('confirm_password', 'Konfirmasi Password', 'matches[password]');
            }

            if ($this->form_validation->run() == TRUE) {
                $email = $this->input->post('email');
                $id_perusahaan = $this->session->userdata('id_role') == 1 ?
                    $this->input->post('id_perusahaan') :
                    $this->session->userdata('id_perusahaan');

                // Check unique email (excluding current record)
                if (!$this->User_model->check_unique_email($email, $id_user)) {
                    $this->data['error'] = 'Email sudah digunakan!';
                    redirect('setup/user/edit/' . $id_user);
                }

                // Handle upload foto profil
                $foto_profil = $this->data['user']->foto_profil;
                if (!empty($_FILES['foto_profil']['name'])) {
                    $config['upload_path'] = './uploads/user/';
                    $config['allowed_types'] = 'jpg|jpeg|png|gif';
                    $config['max_size'] = 1024; // 1MB
                    $config['file_name'] = time() . '_' . $_FILES['foto_profil']['name'];

                    $this->upload->initialize($config);

                    if ($this->upload->do_upload('foto_profil')) {
                        $upload_data = $this->upload->data();
                        $foto_profil = $upload_data['file_name'];

                        // Hapus foto lama jika ada
                        if ($this->data['user']->foto_profil && file_exists('./uploads/user/' . $this->data['user']->foto_profil)) {
                            unlink('./uploads/user/' . $this->data['user']->foto_profil);
                        }
                    }
                }

                $data = array(
                    'nama' => $this->input->post('nama'),
                    'email' => $email,
                    'telepon' => $this->input->post('telepon'),
                    'id_perusahaan' => $id_perusahaan,
                    'foto_profil' => $foto_profil,
                    'aktif' => $this->input->post('aktif')
                );

                // Update password jika diisi
                if (!empty($this->input->post('password'))) {
                    $data['password_hash'] = password_hash($this->input->post('password'), PASSWORD_DEFAULT);
                }

                if ($this->User_model->update($id_user, $data)) {
                    $this->data['success'] = 'User berhasil diperbarui!';

                    // Redirect berdasarkan role user
                    if ($this->data['user']->id_role == 3) {
                        redirect('setup/user/sales');
                    } elseif ($this->data['user']->id_role == 4) {
                        redirect('setup/user/packing');
                    } else {
                        redirect('setup/user');
                    }
                } else {
                    $this->data['error'] = 'Gagal memperbarui user!';
                    redirect('setup/user/edit/' . $id_user);
                }
            }
        }

        $this->render_view('setup/user/form_edit');
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

    public function aktif($id)
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

        if ($this->User_model->update_status($id, 1)) {
            $this->data['success'] = 'User berhasil diaktifkan kembali';
        } else {
            $this->data['error'] = 'Gagal mengaktifkan user';
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