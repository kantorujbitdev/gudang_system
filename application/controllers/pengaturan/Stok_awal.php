<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pengaturan_stok_awal extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('auth');
        $this->load->model('stok_awal_model');
        $this->load->model('barang_model');
        $this->load->model('setup/Gudang_model');
        $this->load->library('form_validation');

        if (!$this->auth->is_logged_in()) {
            redirect('auth');
        }

        if (!$this->auth->has_permission('pengaturan/stok_awal')) {
            show_error('Anda tidak memiliki akses ke halaman ini', 403);
        }
    }

    public function index()
    {
        $data['title'] = 'Stok Awal';
        $data['content'] = 'pengaturan/stok_awal/index';
        $data['stok_awal'] = $this->stok_awal_model->get_all();

        $this->load->view('template/template', $data);
    }

    public function tambah()
    {
        $data['title'] = 'Tambah Stok Awal';
        $data['content'] = 'pengaturan/stok_awal/tambah';
        $data['barang'] = $this->barang_model->get_all();
        $data['gudang'] = $this->gudang_model->get_all();

        $this->form_validation->set_rules('id_barang', 'Barang', 'required');
        $this->form_validation->set_rules('id_gudang', 'Gudang', 'required');
        $this->form_validation->set_rules('qty_awal', 'Qty Awal', 'required|numeric');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('template/template', $data);
        } else {
            $data_stok_awal = array(
                'id_barang' => $this->input->post('id_barang'),
                'id_gudang' => $this->input->post('id_gudang'),
                'id_perusahaan' => $this->session->userdata('id_perusahaan'),
                'qty_awal' => $this->input->post('qty_awal'),
                'keterangan' => $this->input->post('keterangan'),
                'created_by' => $this->session->userdata('id_user')
            );

            $this->stok_awal_model->insert($data_stok_awal);
            $this->session->set_flashdata('success', 'Stok awal berhasil ditambahkan!');
            redirect('pengaturan/stok_awal');
        }
    }

    public function edit($id)
    {
        $data['title'] = 'Edit Stok Awal';
        $data['content'] = 'pengaturan/stok_awal/edit';
        $data['stok_awal'] = $this->stok_awal_model->get_by_id($id);
        $data['barang'] = $this->barang_model->get_all();
        $data['gudang'] = $this->gudang_model->get_all();

        if (!$data['stok_awal']) {
            show_404();
        }

        $this->form_validation->set_rules('id_barang', 'Barang', 'required');
        $this->form_validation->set_rules('id_gudang', 'Gudang', 'required');
        $this->form_validation->set_rules('qty_awal', 'Qty Awal', 'required|numeric');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('template/template', $data);
        } else {
            $data_stok_awal = array(
                'id_barang' => $this->input->post('id_barang'),
                'id_gudang' => $this->input->post('id_gudang'),
                'qty_awal' => $this->input->post('qty_awal'),
                'keterangan' => $this->input->post('keterangan')
            );

            $this->stok_awal_model->update($id, $data_stok_awal);
            $this->session->set_flashdata('success', 'Stok awal berhasil diperbarui!');
            redirect('pengaturan/stok_awal');
        }
    }

    public function hapus($id)
    {
        $stok_awal = $this->stok_awal_model->get_by_id($id);

        if (!$stok_awal) {
            show_404();
        }

        $this->stok_awal_model->delete($id);
        $this->session->set_flashdata('success', 'Stok awal berhasil dihapus!');
        redirect('pengaturan/stok_awal');
    }

    public function proses()
    {
        $stok_awal = $this->stok_awal_model->get_all();

        foreach ($stok_awal as $row) {
            // Check if stock exists
            $stok = $this->stok_awal_model->get_stok_by_barang_gudang($row->id_barang, $row->id_gudang);

            if ($stok) {
                // Update existing stock
                $data_stok = array(
                    'jumlah' => $stok->jumlah + $row->qty_awal
                );
                $this->stok_awal_model->update_stok($stok->id_stok, $data_stok);
            } else {
                // Insert new stock
                $data_stok = array(
                    'id_perusahaan' => $row->id_perusahaan,
                    'id_gudang' => $row->id_gudang,
                    'id_barang' => $row->id_barang,
                    'jumlah' => $row->qty_awal
                );
                $this->stok_awal_model->insert_stok($data_stok);
            }

            // Log stock movement
            $data_log_stok = array(
                'id_barang' => $row->id_barang,
                'id_user' => $this->session->userdata('id_user'),
                'id_perusahaan' => $row->id_perusahaan,
                'id_gudang' => $row->id_gudang,
                'jenis' => 'masuk',
                'jumlah' => $row->qty_awal,
                'sisa_stok' => $stok ? ($stok->jumlah + $row->qty_awal) : $row->qty_awal,
                'keterangan' => 'Stok awal',
                'id_referensi' => $row->id_stok_awal,
                'tipe_referensi' => 'penyesuaian'
            );

            $this->stok_awal_model->insert_log_stok($data_log_stok);
        }

        $this->session->set_flashdata('success', 'Stok awal berhasil diproses!');
        redirect('pengaturan/stok_awal');
    }
}