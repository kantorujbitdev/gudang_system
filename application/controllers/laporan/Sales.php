<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pembelian extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('auth');
        $this->load->model('pembelian_model');
        $this->load->model('supplier_model');
        $this->load->model('barang_model');
        $this->load->model('setup/Gudang_model');
        $this->load->library('form_validation');

        if (!$this->auth->is_logged_in()) {
            redirect('auth');
        }

        if (!$this->auth->has_permission('pembelian')) {
            show_error('Anda tidak memiliki akses ke halaman ini', 403);
        }
    }

    public function index()
    {
        $data['title'] = 'Pembelian';
        $data['content'] = 'pembelian/index';
        $data['pembelian'] = $this->pembelian_model->get_all();

        $this->load->view('template/template', $data);
    }

    public function tambah()
    {
        $data['title'] = 'Tambah Pembelian';
        $data['content'] = 'pembelian/tambah';
        $data['supplier'] = $this->supplier_model->get_all();
        $data['barang'] = $this->barang_model->get_all();
        $data['gudang'] = $this->gudang_model->get_all();

        $this->form_validation->set_rules('id_supplier', 'Supplier', 'required');
        $this->form_validation->set_rules('tanggal_pembelian', 'Tanggal Pembelian', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('template/template', $data);
        } else {
            // Generate nomor pembelian
            $no_pembelian = 'PO-' . date('YmdHis');

            $data_pembelian = array(
                'id_perusahaan' => $this->session->userdata('id_perusahaan'),
                'no_pembelian' => $no_pembelian,
                'id_user' => $this->session->userdata('id_user'),
                'id_supplier' => $this->input->post('id_supplier'),
                'tanggal_pembelian' => $this->input->post('tanggal_pembelian'),
                'tanggal_estimasi' => $this->input->post('tanggal_estimasi'),
                'keterangan' => $this->input->post('keterangan'),
                'status' => 'Draft'
            );

            $id_pembelian = $this->pembelian_model->insert_pembelian($data_pembelian);

            // Insert detail pembelian
            $id_barang = $this->input->post('id_barang');
            $jumlah = $this->input->post('jumlah');
            $harga_beli = $this->input->post('harga_beli');

            for ($i = 0; $i < count($id_barang); $i++) {
                if (!empty($id_barang[$i]) && !empty($jumlah[$i])) {
                    $data_detail = array(
                        'id_pembelian' => $id_pembelian,
                        'id_barang' => $id_barang[$i],
                        'jumlah' => $jumlah[$i],
                        'harga_beli' => $harga_beli[$i]
                    );

                    $this->pembelian_model->insert_detail_pembelian($data_detail);
                }
            }

            $this->session->set_flashdata('success', 'Pembelian berhasil ditambahkan!');
            redirect('pembelian');
        }
    }

    public function edit($id)
    {
        $data['title'] = 'Edit Pembelian';
        $data['content'] = 'pembelian/edit';
        $data['pembelian'] = $this->pembelian_model->get_by_id($id);
        $data['detail_pembelian'] = $this->pembelian_model->get_detail_by_pembelian($id);
        $data['supplier'] = $this->supplier_model->get_all();
        $data['barang'] = $this->barang_model->get_all();
        $data['gudang'] = $this->gudang_model->get_all();

        if (!$data['pembelian']) {
            show_404();
        }

        $this->form_validation->set_rules('id_supplier', 'Supplier', 'required');
        $this->form_validation->set_rules('tanggal_pembelian', 'Tanggal Pembelian', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('template/template', $data);
        } else {
            $data_pembelian = array(
                'id_supplier' => $this->input->post('id_supplier'),
                'tanggal_pembelian' => $this->input->post('tanggal_pembelian'),
                'tanggal_estimasi' => $this->input->post('tanggal_estimasi'),
                'keterangan' => $this->input->post('keterangan')
            );

            $this->pembelian_model->update_pembelian($id, $data_pembelian);

            // Delete existing detail
            $this->pembelian_model->delete_detail_pembelian($id);

            // Insert new detail
            $id_barang = $this->input->post('id_barang');
            $jumlah = $this->input->post('jumlah');
            $harga_beli = $this->input->post('harga_beli');

            for ($i = 0; $i < count($id_barang); $i++) {
                if (!empty($id_barang[$i]) && !empty($jumlah[$i])) {
                    $data_detail = array(
                        'id_pembelian' => $id,
                        'id_barang' => $id_barang[$i],
                        'jumlah' => $jumlah[$i],
                        'harga_beli' => $harga_beli[$i]
                    );

                    $this->pembelian_model->insert_detail_pembelian($data_detail);
                }
            }

            $this->session->set_flashdata('success', 'Pembelian berhasil diperbarui!');
            redirect('pembelian');
        }
    }

    public function hapus($id)
    {
        $pembelian = $this->pembelian_model->get_by_id($id);

        if (!$pembelian) {
            show_404();
        }

        $this->pembelian_model->delete_pembelian($id);
        $this->session->set_flashdata('success', 'Pembelian berhasil dihapus!');
        redirect('pembelian');
    }

    public function detail($id)
    {
        $data['title'] = 'Detail Pembelian';
        $data['content'] = 'pembelian/detail';
        $data['pembelian'] = $this->pembelian_model->get_by_id($id);
        $data['detail_pembelian'] = $this->pembelian_model->get_detail_by_pembelian($id);

        if (!$data['pembelian']) {
            show_404();
        }

        $this->load->view('template/template', $data);
    }

    public function update_status($id, $status)
    {
        $pembelian = $this->pembelian_model->get_by_id($id);

        if (!$pembelian) {
            show_404();
        }

        $data_pembelian = array(
            'status' => $status
        );

        $this->pembelian_model->update_pembelian($id, $data_pembelian);

        // Log status change
        $data_log = array(
            'id_pembelian' => $id,
            'id_user' => $this->session->userdata('id_user'),
            'status' => $status,
            'keterangan' => $this->input->post('keterangan')
        );

        $this->pembelian_model->insert_log_status_pembelian($data_log);

        $this->session->set_flashdata('success', 'Status pembelian berhasil diperbarui!');
        redirect('pembelian/detail/' . $id);
    }
}