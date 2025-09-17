<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Perusahaan extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('setup/Perusahaan_model');
        $this->load->helper('form');
        $this->load->library('form_validation');

        // Debug: Cek role user
        $user_role = $this->session->userdata('id_role');
        log_message('debug', 'User Role: ' . $user_role);

        // Cek akses menu
        $this->check_menu_access('setup/perusahaan');
    }

    public function index()
    {
        $this->data['title'] = 'Manajemen Perusahaan';
        $this->data['perusahaan'] = $this->Perusahaan_model->get_with_stats();

        // Debug: Cek menu yang diakses
        log_message('debug', 'Accessing Perusahaan index');

        $this->render_view('setup/perusahaan/index');
    }

    public function tambah()
    {
        $this->data['title'] = 'Tambah Perusahaan';

        if ($this->input->post()) {
            $this->form_validation->set_rules('nama_perusahaan', 'Nama Perusahaan', 'required|trim|max_length[255]');
            $this->form_validation->set_rules('alamat', 'Alamat', 'trim');
            $this->form_validation->set_rules('telepon', 'Telepon', 'trim|max_length[20]');
            $this->form_validation->set_rules('email', 'Email', 'trim|valid_email|max_length[100]');

            if ($this->form_validation->run() == TRUE) {
                $nama_perusahaan = $this->input->post('nama_perusahaan');

                // Check unique name
                if (!$this->Perusahaan_model->check_unique_name($nama_perusahaan)) {
                    $this->session->set_flashdata('error', 'Nama perusahaan sudah ada!');
                    $this->render_view('setup/perusahaan/form');
                    return;
                }

                $data = array(
                    'nama_perusahaan' => $nama_perusahaan,
                    'alamat' => $this->input->post('alamat'),
                    'telepon' => $this->input->post('telepon'),
                    'email' => $this->input->post('email'),
                    'status_aktif' => 1
                );

                if ($this->Perusahaan_model->insert($data)) {
                    $this->session->set_flashdata('success', 'Perusahaan berhasil ditambahkan!');
                    redirect('setup/perusahaan');
                } else {
                    $this->session->set_flashdata('error', 'Gagal menambahkan perusahaan!');
                }
            }
        }

        $this->render_view('setup/perusahaan/form');
    }

    public function edit($id_perusahaan)
    {
        $this->data['title'] = 'Edit Perusahaan';
        $this->data['perusahaan'] = $this->Perusahaan_model->get($id_perusahaan);

        if (!$this->data['perusahaan']) {
            show_404();
        }

        if ($this->input->post()) {
            $this->form_validation->set_rules('nama_perusahaan', 'Nama Perusahaan', 'required|trim|max_length[255]');
            $this->form_validation->set_rules('alamat', 'Alamat', 'trim');
            $this->form_validation->set_rules('telepon', 'Telepon', 'trim|max_length[20]');
            $this->form_validation->set_rules('email', 'Email', 'trim|valid_email|max_length[100]');
            $this->form_validation->set_rules('status_aktif', 'Status', 'required|in_list[0,1]');

            if ($this->form_validation->run() == TRUE) {
                $nama_perusahaan = $this->input->post('nama_perusahaan');

                // Check unique name (excluding current record)
                if (!$this->Perusahaan_model->check_unique_name($nama_perusahaan, $id_perusahaan)) {
                    $this->session->set_flashdata('error', 'Nama perusahaan sudah ada!');
                    $this->render_view('setup/perusahaan/form');
                    return;
                }

                $data = array(
                    'nama_perusahaan' => $nama_perusahaan,
                    'alamat' => $this->input->post('alamat'),
                    'telepon' => $this->input->post('telepon'),
                    'email' => $this->input->post('email'),
                    'status_aktif' => $this->input->post('status_aktif')
                );

                if ($this->Perusahaan_model->update($id_perusahaan, $data)) {
                    $this->session->set_flashdata('success', 'Perusahaan berhasil diperbarui!');
                    redirect('setup/perusahaan');
                } else {
                    $this->session->set_flashdata('error', 'Gagal memperbarui perusahaan!');
                }
            }
        }

        $this->render_view('setup/perusahaan/form');
    }

    public function hapus($id_perusahaan)
    {
        $perusahaan = $this->Perusahaan_model->get($id_perusahaan);

        if (!$perusahaan) {
            show_404();
        }

        // Check if company has related data
        $has_related_data = FALSE;

        // Check users
        $this->db->where('id_perusahaan', $id_perusahaan);
        if ($this->db->count_all_results('user') > 0) {
            $has_related_data = TRUE;
        }

        // Check gudang
        $this->db->where('id_perusahaan', $id_perusahaan);
        if ($this->db->count_all_results('gudang') > 0) {
            $has_related_data = TRUE;
        }

        // Check barang
        $this->db->where('id_perusahaan', $id_perusahaan);
        if ($this->db->count_all_results('barang') > 0) {
            $has_related_data = TRUE;
        }

        if ($has_related_data) {
            $this->session->set_flashdata('error', 'Perusahaan tidak dapat dihapus karena masih memiliki data terkait (user, gudang, atau barang)!');
            redirect('setup/perusahaan');
        }

        if ($this->Perusahaan_model->delete($id_perusahaan)) {
            $this->session->set_flashdata('success', 'Perusahaan berhasil dihapus!');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus perusahaan!');
        }

        redirect('setup/perusahaan');
    }

    public function detail($id_perusahaan)
    {
        $this->data['title'] = 'Detail Perusahaan';
        $this->data['perusahaan'] = $this->Perusahaan_model->get($id_perusahaan);

        if (!$this->data['perusahaan']) {
            show_404();
        }

        // Get related data
        $this->load->model(['setup/Gudang_model', 'setup/User_model', 'setup/Barang_model']);

        $this->data['gudang'] = $this->Gudang_model->get_by_perusahaan($id_perusahaan);
        $this->data['users'] = $this->User_model->get_by_perusahaan($id_perusahaan);
        $this->data['barang'] = $this->Barang_model->get_by_perusahaan($id_perusahaan);

        $this->render_view('setup/perusahaan/detail');
    }
}