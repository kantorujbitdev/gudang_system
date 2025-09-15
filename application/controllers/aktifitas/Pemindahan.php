<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Penjualan extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('auth');
        $this->load->model('penjualan_model');
        $this->load->model('pelanggan_model');
        $this->load->model('barang_model');
        $this->load->model('gudang_model');
        $this->load->model('stok_model');
        $this->load->library('form_validation');

        if (!$this->auth->is_logged_in()) {
            redirect('auth');
        }

        if (!$this->auth->has_permission('penjualan')) {
            show_error('Anda tidak memiliki akses ke halaman ini', 403);
        }
    }

    public function index()
    {
        $data['title'] = 'Penjualan';
        $data['content'] = 'penjualan/index';
        $data['penjualan'] = $this->penjualan_model->get_all();

        $this->load->view('template/template', $data);
    }

    public function tambah()
    {
        $data['title'] = 'Tambah Penjualan';
        $data['content'] = 'penjualan/tambah';
        $data['pelanggan'] = $this->pelanggan_model->get_all();
        $data['barang'] = $this->barang_model->get_all();
        $data['gudang'] = $this->gudang_model->get_all();

        $this->form_validation->set_rules('id_pelanggan', 'Pelanggan', 'required');
        $this->form_validation->set_rules('tanggal_penjualan', 'Tanggal Penjualan', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('template/template', $data);
        } else {
            // Generate nomor invoice
            $no_invoice = 'INV-' . date('YmdHis');

            $data_penjualan = array(
                'id_perusahaan' => $this->session->userdata('id_perusahaan'),
                'no_invoice' => $no_invoice,
                'id_user' => $this->session->userdata('id_user'),
                'id_pelanggan' => $this->input->post('id_pelanggan'),
                'tanggal_penjualan' => $this->input->post('tanggal_penjualan'),
                'keterangan' => $this->input->post('keterangan'),
                'status' => 'Draft'
            );

            $id_penjualan = $this->penjualan_model->insert_penjualan($data_penjualan);

            // Insert detail penjualan
            $id_barang = $this->input->post('id_barang');
            $id_gudang = $this->input->post('id_gudang');
            $jumlah = $this->input->post('jumlah');
            $harga_jual = $this->input->post('harga_jual');

            for ($i = 0; $i < count($id_barang); $i++) {
                if (!empty($id_barang[$i]) && !empty($jumlah[$i])) {
                    $data_detail = array(
                        'id_penjualan' => $id_penjualan,
                        'id_barang' => $id_barang[$i],
                        'id_gudang' => $id_gudang[$i],
                        'jumlah' => $jumlah[$i],
                        'harga_jual' => $harga_jual[$i]
                    );

                    $this->penjualan_model->insert_detail_penjualan($data_detail);
                }
            }

            $this->session->set_flashdata('success', 'Penjualan berhasil ditambahkan!');
            redirect('penjualan');
        }
    }

    public function edit($id)
    {
        $data['title'] = 'Edit Penjualan';
        $data['content'] = 'penjualan/edit';
        $data['penjualan'] = $this->penjualan_model->get_by_id($id);
        $data['detail_penjualan'] = $this->penjualan_model->get_detail_by_penjualan($id);
        $data['pelanggan'] = $this->pelanggan_model->get_all();
        $data['barang'] = $this->barang_model->get_all();
        $data['gudang'] = $this->gudang_model->get_all();

        if (!$data['penjualan']) {
            show_404();
        }

        $this->form_validation->set_rules('id_pelanggan', 'Pelanggan', 'required');
        $this->form_validation->set_rules('tanggal_penjualan', 'Tanggal Penjualan', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('template/template', $data);
        } else {
            $data_penjualan = array(
                'id_pelanggan' => $this->input->post('id_pelanggan'),
                'tanggal_penjualan' => $this->input->post('tanggal_penjualan'),
                'keterangan' => $this->input->post('keterangan')
            );

            $this->penjualan_model->update_penjualan($id, $data_penjualan);

            // Delete existing detail
            $this->penjualan_model->delete_detail_penjualan($id);

            // Insert new detail
            $id_barang = $this->input->post('id_barang');
            $id_gudang = $this->input->post('id_gudang');
            $jumlah = $this->input->post('jumlah');
            $harga_jual = $this->input->post('harga_jual');

            for ($i = 0; $i < count($id_barang); $i++) {
                if (!empty($id_barang[$i]) && !empty($jumlah[$i])) {
                    $data_detail = array(
                        'id_penjualan' => $id,
                        'id_barang' => $id_barang[$i],
                        'id_gudang' => $id_gudang[$i],
                        'jumlah' => $jumlah[$i],
                        'harga_jual' => $harga_jual[$i]
                    );

                    $this->penjualan_model->insert_detail_penjualan($data_detail);
                }
            }

            $this->session->set_flashdata('success', 'Penjualan berhasil diperbarui!');
            redirect('penjualan');
        }
    }

    public function hapus($id)
    {
        $penjualan = $this->penjualan_model->get_by_id($id);

        if (!$penjualan) {
            show_404();
        }

        $this->penjualan_model->delete_penjualan($id);
        $this->session->set_flashdata('success', 'Penjualan berhasil dihapus!');
        redirect('penjualan');
    }

    public function detail($id)
    {
        $data['title'] = 'Detail Penjualan';
        $data['content'] = 'penjualan/detail';
        $data['penjualan'] = $this->penjualan_model->get_by_id($id);
        $data['detail_penjualan'] = $this->penjualan_model->get_detail_by_penjualan($id);

        if (!$data['penjualan']) {
            show_404();
        }

        $this->load->view('template/template', $data);
    }

    public function update_status($id, $status)
    {
        $penjualan = $this->penjualan_model->get_by_id($id);

        if (!$penjualan) {
            show_404();
        }

        $data_penjualan = array(
            'status' => $status
        );

        $this->penjualan_model->update_penjualan($id, $data_penjualan);

        // Log status change
        $data_log = array(
            'id_penjualan' => $id,
            'id_user' => $this->session->userdata('id_user'),
            'status' => $status,
            'keterangan' => $this->input->post('keterangan')
        );

        $this->penjualan_model->insert_log_status_penjualan($data_log);

        // Update stock if status is Shipping or Delivered
        if ($status == 'Shipping' || $status == 'Delivered') {
            $detail_penjualan = $this->penjualan_model->get_detail_by_penjualan($id);

            foreach ($detail_penjualan as $detail) {
                // Check if stock exists
                $stok = $this->stok_model->get_stok_by_barang_gudang($detail->id_barang, $detail->id_gudang);

                if ($stok) {
                    // Update existing stock
                    $data_stok = array(
                        'jumlah' => $stok->jumlah - $detail->jumlah
                    );
                    $this->stok_model->update_stok($stok->id_stok, $data_stok);

                    // Log stock movement
                    $data_log_stok = array(
                        'id_barang' => $detail->id_barang,
                        'id_user' => $this->session->userdata('id_user'),
                        'id_perusahaan' => $this->session->userdata('id_perusahaan'),
                        'id_gudang' => $detail->id_gudang,
                        'jenis' => 'keluar',
                        'jumlah' => $detail->jumlah,
                        'sisa_stok' => $stok->jumlah - $detail->jumlah,
                        'keterangan' => 'Penjualan ke pelanggan',
                        'id_referensi' => $id,
                        'tipe_referensi' => 'penjualan'
                    );

                    $this->stok_model->insert_log_stok($data_log_stok);
                }
            }
        }

        $this->session->set_flashdata('success', 'Status penjualan berhasil diperbarui!');
        redirect('penjualan/detail/' . $id);
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