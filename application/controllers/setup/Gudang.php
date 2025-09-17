<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Gudang extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('setup/Gudang_model');
        $this->load->model('setup/Perusahaan_model');
        $this->load->helper('form');
        $this->load->library('form_validation');

        // Cek akses menu
        $this->check_menu_access('setup/gudang');
    }

    public function index()
    {
        $this->data['title'] = 'Manajemen Gudang';
        $this->data['gudang'] = $this->Gudang_model->get_with_perusahaan();

        $this->render_view('setup/gudang/index');
    }

    public function tambah()
    {
        $this->data['title'] = 'Tambah Gudang';
        $this->data['perusahaan'] = $this->Perusahaan_model->get_active();

        if ($this->input->post()) {
            $this->form_validation->set_rules('nama_gudang', 'Nama Gudang', 'required|trim|max_length[100]');
            $this->form_validation->set_rules('id_perusahaan', 'Perusahaan', 'required');
            $this->form_validation->set_rules('alamat', 'Alamat', 'trim');
            $this->form_validation->set_rules('telepon', 'Telepon', 'trim|max_length[20]');

            if ($this->form_validation->run() == TRUE) {
                $nama_gudang = $this->input->post('nama_gudang');
                $id_perusahaan = $this->input->post('id_perusahaan');

                // Check unique name per company
                if (!$this->Gudang_model->check_unique_name($nama_gudang, $id_perusahaan)) {
                    $this->session->set_flashdata('error', 'Nama gudang sudah ada di perusahaan yang sama!');
                    $this->render_view('setup/gudang/form');
                    return;
                }

                $data = array(
                    'nama_gudang' => $nama_gudang,
                    'id_perusahaan' => $id_perusahaan,
                    'alamat' => $this->input->post('alamat'),
                    'telepon' => $this->input->post('telepon'),
                    'created_by' => $this->session->userdata('id_user'),
                    'status_aktif' => 1
                );

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
        $this->data['perusahaan'] = $this->Perusahaan_model->get_active();

        if (!$this->data['gudang']) {
            show_404();
        }

        if ($this->input->post()) {
            $this->form_validation->set_rules('nama_gudang', 'Nama Gudang', 'required|trim|max_length[100]');
            $this->form_validation->set_rules('id_perusahaan', 'Perusahaan', 'required');
            $this->form_validation->set_rules('alamat', 'Alamat', 'trim');
            $this->form_validation->set_rules('telepon', 'Telepon', 'trim|max_length[20]');
            $this->form_validation->set_rules('status_aktif', 'Status', 'required|in_list[0,1]');

            if ($this->form_validation->run() == TRUE) {
                $nama_gudang = $this->input->post('nama_gudang');
                $id_perusahaan = $this->input->post('id_perusahaan');

                // Check unique name per company (excluding current record)
                if (!$this->Gudang_model->check_unique_name($nama_gudang, $id_perusahaan, $id_gudang)) {
                    $this->session->set_flashdata('error', 'Nama gudang sudah ada di perusahaan yang sama!');
                    $this->render_view('setup/gudang/form');
                    return;
                }

                $data = array(
                    'nama_gudang' => $nama_gudang,
                    'id_perusahaan' => $id_perusahaan,
                    'alamat' => $this->input->post('alamat'),
                    'telepon' => $this->input->post('telepon'),
                    'status_aktif' => $this->input->post('status_aktif')
                );

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

    public function nonaktif($id)
    {
        if ($this->Gudang_model->update_status($id, 0)) {
            $this->session->set_flashdata('success', 'Gudang berhasil dinonaktifkan');
        } else {
            $this->session->set_flashdata('error', 'Gagal menonaktifkan gudang');
        }
        redirect('setup/gudang');
    }

    public function aktif($id)
    {
        if ($this->Gudang_model->update_status($id, 1)) {
            $this->session->set_flashdata('success', 'Gudang berhasil diaktifkan kembali');
        } else {
            $this->session->set_flashdata('error', 'Gagal mengaktifkan gudang');
        }
        redirect('setup/gudang');
    }

    public function detail($id_gudang)
    {
        $this->data['title'] = 'Detail Gudang';
        $this->data['gudang'] = $this->Gudang_model->get_detail($id_gudang);

        if (!$this->data['gudang']) {
            show_404();
        }

        // Get related data
        $this->load->model('setup/Barang_model');
        $this->data['barang'] = $this->Barang_model->get_by_gudang($id_gudang);

        $this->render_view('setup/gudang/detail');
    }
}