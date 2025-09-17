<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Gudang extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('setup/Gudang_model');
        $this->load->helper('form');
        $this->load->library('form_validation');

        // Cek akses berdasarkan role
        $user_role = $this->session->userdata('id_role');
        // Cek akses menu
        $this->check_menu_access('setup/gudang');
    }

    public function index()
    {
        $this->data['title'] = 'Manajemen Gudang';
        $this->data['gudang'] = $this->Gudang_model->get_with_stok();
        $this->render_view('setup/gudang/index');
    }

    public function tambah()
    {
        $this->data['title'] = 'Tambah Gudang';

        // Jika Super Admin, tampilkan pilihan perusahaan
        if ($this->session->userdata('id_role') == 1) {
            $this->load->model('setup/Perusahaan_model');
            $this->data['perusahaan'] = $this->Perusahaan_model->get_active();
        }

        if ($this->input->post()) {
            $this->form_validation->set_rules('nama_gudang', 'Nama Gudang', 'required|trim|max_length[100]');
            $this->form_validation->set_rules('alamat', 'Alamat', 'trim');
            $this->form_validation->set_rules('telepon', 'Telepon', 'trim|max_length[20]');

            // Jika Super Admin, validasi perusahaan
            if ($this->session->userdata('id_role') == 1) {
                $this->form_validation->set_rules('id_perusahaan', 'Perusahaan', 'required');
            }

            if ($this->form_validation->run() == TRUE) {
                $data = array(
                    'nama_gudang' => $this->input->post('nama_gudang'),
                    'alamat' => $this->input->post('alamat'),
                    'telepon' => $this->input->post('telepon'),
                    'status_aktif' => 1
                );

                // Jika Super Admin, set perusahaan
                if ($this->session->userdata('id_role') == 1) {
                    $data['id_perusahaan'] = $this->input->post('id_perusahaan');
                } else {
                    $data['id_perusahaan'] = $this->session->userdata('id_perusahaan');
                }

                if ($this->Gudang_model->insert($data)) {
                    $this->session->set_flashdata('success', 'Gudang berhasil ditambahkan!');
                    redirect('setup/gudang');
                } else {
                    $this->session->set_flashdata('error', 'Gagal menambahkan gudang!');
                }
            }
        }

        $this->render_view('setup/gudang/form');
    }

    public function edit($id_gudang)
    {
        $this->data['title'] = 'Edit Gudang';
        $this->data['gudang'] = $this->Gudang_model->get($id_gudang);

        if (!$this->data['gudang']) {
            show_404();
        }

        // Jika Super Admin, tampilkan pilihan perusahaan
        if ($this->session->userdata('id_role') == 1) {
            $this->load->model('setup/Perusahaan_model');
            $this->data['perusahaan'] = $this->Perusahaan_model->get_active();
        }

        if ($this->input->post()) {
            $this->form_validation->set_rules('nama_gudang', 'Nama Gudang', 'required|trim|max_length[100]');
            $this->form_validation->set_rules('alamat', 'Alamat', 'trim');
            $this->form_validation->set_rules('telepon', 'Telepon', 'trim|max_length[20]');
            $this->form_validation->set_rules('status_aktif', 'Status', 'required|in_list[0,1]');

            // Jika Super Admin, validasi perusahaan
            if ($this->session->userdata('id_role') == 1) {
                $this->form_validation->set_rules('id_perusahaan', 'Perusahaan', 'required');
            }

            if ($this->form_validation->run() == TRUE) {
                $data = array(
                    'nama_gudang' => $this->input->post('nama_gudang'),
                    'alamat' => $this->input->post('alamat'),
                    'telepon' => $this->input->post('telepon'),
                    'status_aktif' => $this->input->post('status_aktif')
                );

                // Jika Super Admin, update perusahaan
                if ($this->session->userdata('id_role') == 1) {
                    $data['id_perusahaan'] = $this->input->post('id_perusahaan');
                }

                if ($this->Gudang_model->update($id_gudang, $data)) {
                    $this->session->set_flashdata('success', 'Gudang berhasil diperbarui!');
                    redirect('setup/gudang');
                } else {
                    $this->session->set_flashdata('error', 'Gagal memperbarui gudang!');
                }
            }
        }

        $this->render_view('setup/gudang/form');
    }

    public function hapus($id_gudang)
    {
        $gudang = $this->Gudang_model->get($id_gudang);

        if (!$gudang) {
            show_404();
        }

        // Check if gudang has related stock
        $this->db->where('id_gudang', $id_gudang);
        if ($this->db->count_all_results('stok_gudang') > 0) {
            $this->session->set_flashdata('error', 'Gudang tidak dapat dihapus karena masih memiliki stok terkait!');
            redirect('setup/gudang');
        }

        if ($this->Gudang_model->delete($id_gudang)) {
            $this->session->set_flashdata('success', 'Gudang berhasil dihapus!');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus gudang!');
        }

        redirect('setup/gudang');
    }

    public function detail($id_gudang)
    {
        $this->data['title'] = 'Detail Gudang';
        $this->data['gudang'] = $this->Gudang_model->get($id_gudang);

        if (!$this->data['gudang']) {
            show_404();
        }

        // Get stock in this warehouse
        $this->load->model('Stok_model');
        $this->data['stok'] = $this->Stok_model->get_by_gudang($id_gudang);

        $this->render_view('setup/gudang/detail');
    }
}