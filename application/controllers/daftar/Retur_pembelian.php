<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Retur_pembelian extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('auth');
        $this->load->model('retur_pembelian_model');
        $this->load->model('pembelian_model');
        $this->load->model('supplier_model');
        $this->load->model('barang_model');
        $this->load->model('setup/Gudang_model');
        $this->load->model('stok_model');
        $this->load->library('form_validation');

        if (!$this->auth->is_logged_in()) {
            redirect('auth');
        }

        if (!$this->auth->has_permission('retur/pembelian')) {
            show_error('Anda tidak memiliki akses ke halaman ini', 403);
        }
    }

    public function index()
    {
        $data['title'] = 'Retur Pembelian';
        $data['content'] = 'retur_pembelian/index';
        $data['retur'] = $this->retur_pembelian_model->get_all();

        $this->load->view('template/template', $data);
    }

    public function tambah()
    {
        $data['title'] = 'Tambah Retur Pembelian';
        $data['content'] = 'retur_pembelian/tambah';
        $data['pembelian'] = $this->pembelian_model->get_pembelian_for_retur();
        $data['supplier'] = $this->supplier_model->get_all();

        $this->form_validation->set_rules('id_pembelian', 'Pembelian', 'required');
        $this->form_validation->set_rules('id_supplier', 'Supplier', 'required');
        $this->form_validation->set_rules('tanggal_retur', 'Tanggal Retur', 'required');
        $this->form_validation->set_rules('alasan_retur', 'Alasan Retur', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('template/template', $data);
        } else {
            // Generate nomor retur
            $no_retur = 'RB-' . date('YmdHis');

            $data_retur = array(
                'no_retur_beli' => $no_retur,
                'id_pembelian' => $this->input->post('id_pembelian'),
                'id_supplier' => $this->input->post('id_supplier'),
                'id_user' => $this->session->userdata('id_user'),
                'tanggal_retur' => $this->input->post('tanggal_retur'),
                'alasan_retur' => $this->input->post('alasan_retur'),
                'status' => 'Requested'
            );

            $id_retur = $this->retur_pembelian_model->insert_retur($data_retur);

            // Insert detail retur
            $id_barang = $this->input->post('id_barang');
            $id_gudang = $this->input->post('id_gudang');
            $jumlah_retur = $this->input->post('jumlah_retur');
            $alasan_barang = $this->input->post('alasan_barang');

            for ($i = 0; $i < count($id_barang); $i++) {
                if (!empty($id_barang[$i]) && !empty($jumlah_retur[$i])) {
                    $data_detail = array(
                        'id_retur_beli' => $id_retur,
                        'id_barang' => $id_barang[$i],
                        'id_gudang' => $id_gudang[$i],
                        'jumlah_retur' => $jumlah_retur[$i],
                        'alasan_barang' => $alasan_barang[$i]
                    );

                    $this->retur_pembelian_model->insert_detail_retur($data_detail);
                }
            }

            $this->data['success'] = 'Retur pembelian berhasil ditambahkan!';
            redirect('retur/pembelian');
        }
    }

    public function edit($id)
    {
        $data['title'] = 'Edit Retur Pembelian';
        $data['content'] = 'retur_pembelian/edit';
        $data['retur'] = $this->retur_pembelian_model->get_by_id($id);
        $data['detail_retur'] = $this->retur_pembelian_model->get_detail_by_retur($id);
        $data['pembelian'] = $this->pembelian_model->get_pembelian_for_retur();
        $data['supplier'] = $this->supplier_model->get_all();

        if (!$data['retur']) {
            show_404();
        }

        $this->form_validation->set_rules('id_pembelian', 'Pembelian', 'required');
        $this->form_validation->set_rules('id_supplier', 'Supplier', 'required');
        $this->form_validation->set_rules('tanggal_retur', 'Tanggal Retur', 'required');
        $this->form_validation->set_rules('alasan_retur', 'Alasan Retur', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('template/template', $data);
        } else {
            $data_retur = array(
                'id_pembelian' => $this->input->post('id_pembelian'),
                'id_supplier' => $this->input->post('id_supplier'),
                'tanggal_retur' => $this->input->post('tanggal_retur'),
                'alasan_retur' => $this->input->post('alasan_retur')
            );

            $this->retur_pembelian_model->update_retur($id, $data_retur);

            // Delete existing detail
            $this->retur_pembelian_model->delete_detail_retur($id);

            // Insert new detail
            $id_barang = $this->input->post('id_barang');
            $id_gudang = $this->input->post('id_gudang');
            $jumlah_retur = $this->input->post('jumlah_retur');
            $alasan_barang = $this->input->post('alasan_barang');

            for ($i = 0; $i < count($id_barang); $i++) {
                if (!empty($id_barang[$i]) && !empty($jumlah_retur[$i])) {
                    $data_detail = array(
                        'id_retur_beli' => $id,
                        'id_barang' => $id_barang[$i],
                        'id_gudang' => $id_gudang[$i],
                        'jumlah_retur' => $jumlah_retur[$i],
                        'alasan_barang' => $alasan_barang[$i]
                    );

                    $this->retur_pembelian_model->insert_detail_retur($data_detail);
                }
            }

            $this->data['success'] = 'Retur pembelian berhasil diperbarui!';
            redirect('retur/pembelian');
        }
    }

    public function hapus($id)
    {
        $retur = $this->retur_pembelian_model->get_by_id($id);

        if (!$retur) {
            show_404();
        }

        $this->retur_pembelian_model->delete_retur($id);
        $this->data['success'] = 'Retur pembelian berhasil dihapus!';
        redirect('retur/pembelian');
    }

    public function detail($id)
    {
        $data['title'] = 'Detail Retur Pembelian';
        $data['content'] = 'retur_pembelian/detail';
        $data['retur'] = $this->retur_pembelian_model->get_by_id($id);
        $data['detail_retur'] = $this->retur_pembelian_model->get_detail_by_retur($id);

        if (!$data['retur']) {
            show_404();
        }

        $this->load->view('template/template', $data);
    }

    public function update_status($id, $status)
    {
        $retur = $this->retur_pembelian_model->get_by_id($id);

        if (!$retur) {
            show_404();
        }

        $data_retur = array(
            'status' => $status
        );

        $this->retur_pembelian_model->update_retur($id, $data_retur);

        // Log status change
        $data_log = array(
            'id_retur_beli' => $id,
            'id_user' => $this->session->userdata('id_user'),
            'status' => $status,
            'keterangan' => $this->input->post('keterangan')
        );

        $this->retur_pembelian_model->insert_log_status_retur($data_log);

        // Update stock if status is Approved
        if ($status == 'Approved') {
            $detail_retur = $this->retur_pembelian_model->get_detail_by_retur($id);

            foreach ($detail_retur as $detail) {
                // Check if stock exists
                $stok = $this->stok_model->get_stok_by_barang_gudang($detail->id_barang, $detail->id_gudang);

                if ($stok) {
                    // Update existing stock
                    $data_stok = array(
                        'jumlah' => $stok->jumlah - $detail->jumlah_retur
                    );
                    $this->stok_model->update_stok($stok->id_stok, $data_stok);

                    // Log stock movement
                    $data_log_stok = array(
                        'id_barang' => $detail->id_barang,
                        'id_user' => $this->session->userdata('id_user'),
                        'id_perusahaan' => $this->session->userdata('id_perusahaan'),
                        'id_gudang' => $detail->id_gudang,
                        'jenis' => 'retur_pembelian',
                        'jumlah' => $detail->jumlah_retur,
                        'sisa_stok' => $stok->jumlah - $detail->jumlah_retur,
                        'keterangan' => 'Retur pembelian ke supplier',
                        'id_referensi' => $id,
                        'tipe_referensi' => 'retur_pembelian'
                    );

                    $this->stok_model->insert_log_stok($data_log_stok);
                }
            }
        }

        $this->data['success'] = 'Status retur pembelian berhasil diperbarui!';
        redirect('retur/pembelian/detail/' . $id);
    }

    public function get_pembelian_detail()
    {
        $id_pembelian = $this->input->post('id_pembelian');
        $detail_pembelian = $this->pembelian_model->get_detail_by_pembelian($id_pembelian);

        echo json_encode($detail_pembelian);
    }
    public function get_stok_by_barang_gudang()
    {
        $id_barang = $this->input->post('id_barang');
        $id_gudang = $this->input->post('id_gudang');

        $stok = $this->stok_model->get_stok_by_barang_gudang($id_barang, $id_gudang);

        if ($stok) {
            echo json_encode(array('status' => 'success', 'stok' => $stok->jumlah));
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Stok tidak ditemukan'));
        }
    }
}