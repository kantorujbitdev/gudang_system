<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Transfer extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('auth');
        $this->load->model('transfer_model');
        $this->load->model('barang_model');
        $this->load->model('setup/Gudang_model');
        $this->load->model('stok_model');
        $this->load->library('form_validation');

        if (!$this->auth->is_logged_in()) {
            redirect('auth');
        }

        if (!$this->auth->has_permission('transfer')) {
            show_error('Anda tidak memiliki akses ke halaman ini', 403);
        }
    }

    public function index()
    {
        $data['title'] = 'Transfer Stok';
        $data['content'] = 'transfer/index';
        $data['transfer'] = $this->transfer_model->get_all();

        $this->load->view('template/template', $data);
    }

    public function tambah()
    {
        $data['title'] = 'Tambah Transfer Stok';
        $data['content'] = 'transfer/tambah';
        $data['gudang'] = $this->gudang_model->get_all();
        $data['barang'] = $this->barang_model->get_all();

        $this->form_validation->set_rules('id_gudang_asal', 'Gudang Asal', 'required');
        $this->form_validation->set_rules('id_gudang_tujuan', 'Gudang Tujuan', 'required');
        $this->form_validation->set_rules('tanggal', 'Tanggal', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('template/template', $data);
        } else {
            // Generate nomor transfer
            $no_transfer = 'TRF-' . date('YmdHis');

            $data_transfer = array(
                'id_perusahaan' => $this->session->userdata('id_perusahaan'),
                'no_transfer' => $no_transfer,
                'id_gudang_asal' => $this->input->post('id_gudang_asal'),
                'id_gudang_tujuan' => $this->input->post('id_gudang_tujuan'),
                'id_user' => $this->session->userdata('id_user'),
                'tanggal' => $this->input->post('tanggal'),
                'keterangan' => $this->input->post('keterangan'),
                'status' => 'Draft'
            );

            $id_transfer = $this->transfer_model->insert_transfer($data_transfer);

            // Insert detail transfer
            $id_barang = $this->input->post('id_barang');
            $jumlah = $this->input->post('jumlah');

            for ($i = 0; $i < count($id_barang); $i++) {
                if (!empty($id_barang[$i]) && !empty($jumlah[$i])) {
                    $data_detail = array(
                        'id_transfer' => $id_transfer,
                        'id_barang' => $id_barang[$i],
                        'jumlah' => $jumlah[$i]
                    );

                    $this->transfer_model->insert_detail_transfer($data_detail);
                }
            }

            $this->session->set_flashdata('success', 'Transfer stok berhasil ditambahkan!');
            redirect('transfer');
        }
    }

    public function edit($id)
    {
        $data['title'] = 'Edit Transfer Stok';
        $data['content'] = 'transfer/edit';
        $data['transfer'] = $this->transfer_model->get_by_id($id);
        $data['detail_transfer'] = $this->transfer_model->get_detail_by_transfer($id);
        $data['gudang'] = $this->gudang_model->get_all();
        $data['barang'] = $this->barang_model->get_all();

        if (!$data['transfer']) {
            show_404();
        }

        $this->form_validation->set_rules('id_gudang_asal', 'Gudang Asal', 'required');
        $this->form_validation->set_rules('id_gudang_tujuan', 'Gudang Tujuan', 'required');
        $this->form_validation->set_rules('tanggal', 'Tanggal', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('template/template', $data);
        } else {
            $data_transfer = array(
                'id_gudang_asal' => $this->input->post('id_gudang_asal'),
                'id_gudang_tujuan' => $this->input->post('id_gudang_tujuan'),
                'tanggal' => $this->input->post('tanggal'),
                'keterangan' => $this->input->post('keterangan')
            );

            $this->transfer_model->update_transfer($id, $data_transfer);

            // Delete existing detail
            $this->transfer_model->delete_detail_transfer($id);

            // Insert new detail
            $id_barang = $this->input->post('id_barang');
            $jumlah = $this->input->post('jumlah');

            for ($i = 0; $i < count($id_barang); $i++) {
                if (!empty($id_barang[$i]) && !empty($jumlah[$i])) {
                    $data_detail = array(
                        'id_transfer' => $id,
                        'id_barang' => $id_barang[$i],
                        'jumlah' => $jumlah[$i]
                    );

                    $this->transfer_model->insert_detail_transfer($data_detail);
                }
            }

            $this->session->set_flashdata('success', 'Transfer stok berhasil diperbarui!');
            redirect('transfer');
        }
    }

    public function hapus($id)
    {
        $transfer = $this->transfer_model->get_by_id($id);

        if (!$transfer) {
            show_404();
        }

        $this->transfer_model->delete_transfer($id);
        $this->session->set_flashdata('success', 'Transfer stok berhasil dihapus!');
        redirect('transfer');
    }

    public function detail($id)
    {
        $data['title'] = 'Detail Transfer Stok';
        $data['content'] = 'transfer/detail';
        $data['transfer'] = $this->transfer_model->get_by_id($id);
        $data['detail_transfer'] = $this->transfer_model->get_detail_by_transfer($id);

        if (!$data['transfer']) {
            show_404();
        }

        $this->load->view('template/template', $data);
    }

    public function update_status($id, $status)
    {
        $transfer = $this->transfer_model->get_by_id($id);

        if (!$transfer) {
            show_404();
        }

        $data_transfer = array(
            'status' => $status
        );

        $this->transfer_model->update_transfer($id, $data_transfer);

        // Log status change
        $data_log = array(
            'id_transaksi' => $id,
            'tipe_transaksi' => 'transfer_stok',
            'id_user' => $this->session->userdata('id_user'),
            'status' => $status,
            'keterangan' => $this->input->post('keterangan')
        );

        $this->transfer_model->insert_log_status_transaksi($data_log);

        // Update stock if status is Shipping or Delivered
        if ($status == 'Shipping' || $status == 'Delivered') {
            $detail_transfer = $this->transfer_model->get_detail_by_transfer($id);

            foreach ($detail_transfer as $detail) {
                // Check if stock exists in gudang asal
                $stok_asal = $this->stok_model->get_stok_by_barang_gudang($detail->id_barang, $transfer->id_gudang_asal);

                if ($stok_asal) {
                    // Update stock gudang asal
                    $data_stok_asal = array(
                        'jumlah' => $stok_asal->jumlah - $detail->jumlah
                    );
                    $this->stok_model->update_stok($stok_asal->id_stok, $data_stok_asal);

                    // Log stock movement gudang asal
                    $data_log_stok_asal = array(
                        'id_barang' => $detail->id_barang,
                        'id_user' => $this->session->userdata('id_user'),
                        'id_perusahaan' => $this->session->userdata('id_perusahaan'),
                        'id_gudang' => $transfer->id_gudang_asal,
                        'jenis' => 'transfer_keluar',
                        'jumlah' => $detail->jumlah,
                        'sisa_stok' => $stok_asal->jumlah - $detail->jumlah,
                        'keterangan' => 'Transfer stok ke gudang tujuan',
                        'id_referensi' => $id,
                        'tipe_referensi' => 'transfer'
                    );

                    $this->stok_model->insert_log_stok($data_log_stok_asal);
                }

                // Check if stock exists in gudang tujuan
                $stok_tujuan = $this->stok_model->get_stok_by_barang_gudang($detail->id_barang, $transfer->id_gudang_tujuan);

                if ($stok_tujuan) {
                    // Update existing stock gudang tujuan
                    $data_stok_tujuan = array(
                        'jumlah' => $stok_tujuan->jumlah + $detail->jumlah
                    );
                    $this->stok_model->update_stok($stok_tujuan->id_stok, $data_stok_tujuan);
                } else {
                    // Insert new stock gudang tujuan
                    $data_stok_tujuan = array(
                        'id_perusahaan' => $this->session->userdata('id_perusahaan'),
                        'id_gudang' => $transfer->id_gudang_tujuan,
                        'id_barang' => $detail->id_barang,
                        'jumlah' => $detail->jumlah
                    );
                    $this->stok_model->insert_stok($data_stok_tujuan);
                    $stok_tujuan = (object) array('jumlah' => 0);
                }

                // Log stock movement gudang tujuan
                $data_log_stok_tujuan = array(
                    'id_barang' => $detail->id_barang,
                    'id_user' => $this->session->userdata('id_user'),
                    'id_perusahaan' => $this->session->userdata('id_perusahaan'),
                    'id_gudang' => $transfer->id_gudang_tujuan,
                    'jenis' => 'transfer_masuk',
                    'jumlah' => $detail->jumlah,
                    'sisa_stok' => $stok_tujuan->jumlah + $detail->jumlah,
                    'keterangan' => 'Transfer stok dari gudang asal',
                    'id_referensi' => $id,
                    'tipe_referensi' => 'transfer'
                );

                $this->stok_model->insert_log_stok($data_log_stok_tujuan);
            }
        }

        $this->session->set_flashdata('success', 'Status transfer stok berhasil diperbarui!');
        redirect('transfer/detail/' . $id);
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