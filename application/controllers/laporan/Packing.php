<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Packing extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('auth');
        $this->load->model('packing_model');
        $this->load->model('penjualan_model');
        $this->load->model('transfer_model');
        $this->load->library('form_validation');

        if (!$this->auth->is_logged_in()) {
            redirect('auth');
        }

        if (!$this->auth->has_permission('packing')) {
            show_error('Anda tidak memiliki akses ke halaman ini', 403);
        }
    }

    public function index()
    {
        $data['title'] = 'Packing';
        $data['content'] = 'packing/index';
        $data['packing'] = $this->packing_model->get_all();

        $this->load->view('template/template', $data);
    }

    public function tambah()
    {
        $data['title'] = 'Tambah Packing';
        $data['content'] = 'packing/tambah';
        $data['penjualan'] = $this->penjualan_model->get_penjualan_for_packing();
        $data['transfer'] = $this->transfer_model->get_transfer_for_packing();

        $this->form_validation->set_rules('tipe_referensi', 'Tipe Referensi', 'required');
        $this->form_validation->set_rules('id_referensi', 'Referensi', 'required');
        $this->form_validation->set_rules('tanggal_packing', 'Tanggal Packing', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('template/template', $data);
        } else {
            $data_packing = array(
                'id_referensi' => $this->input->post('id_referensi'),
                'tipe_referensi' => $this->input->post('tipe_referensi'),
                'id_user' => $this->session->userdata('id_user'),
                'tanggal_packing' => $this->input->post('tanggal_packing'),
                'catatan' => $this->input->post('catatan'),
                'status' => 'Draft'
            );

            $id_packing = $this->packing_model->insert_packing($data_packing);

            // Insert detail packing
            $id_barang = $this->input->post('id_barang');
            $jumlah = $this->input->post('jumlah');

            for ($i = 0; $i < count($id_barang); $i++) {
                if (!empty($id_barang[$i]) && !empty($jumlah[$i])) {
                    $data_detail = array(
                        'id_packing' => $id_packing,
                        'id_barang' => $id_barang[$i],
                        'jumlah' => $jumlah[$i]
                    );

                    $this->packing_model->insert_detail_packing($data_detail);
                }
            }

            $this->data['success'] = 'Packing berhasil ditambahkan!';
            redirect('packing');
        }
    }

    public function edit($id)
    {
        $data['title'] = 'Edit Packing';
        $data['content'] = 'packing/edit';
        $data['packing'] = $this->packing_model->get_by_id($id);
        $data['detail_packing'] = $this->packing_model->get_detail_by_packing($id);
        $data['penjualan'] = $this->penjualan_model->get_penjualan_for_packing();
        $data['transfer'] = $this->transfer_model->get_transfer_for_packing();

        if (!$data['packing']) {
            show_404();
        }

        $this->form_validation->set_rules('tipe_referensi', 'Tipe Referensi', 'required');
        $this->form_validation->set_rules('id_referensi', 'Referensi', 'required');
        $this->form_validation->set_rules('tanggal_packing', 'Tanggal Packing', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('template/template', $data);
        } else {
            $data_packing = array(
                'id_referensi' => $this->input->post('id_referensi'),
                'tipe_referensi' => $this->input->post('tipe_referensi'),
                'tanggal_packing' => $this->input->post('tanggal_packing'),
                'catatan' => $this->input->post('catatan')
            );

            $this->packing_model->update_packing($id, $data_packing);

            // Delete existing detail
            $this->packing_model->delete_detail_packing($id);

            // Insert new detail
            $id_barang = $this->input->post('id_barang');
            $jumlah = $this->input->post('jumlah');

            for ($i = 0; $i < count($id_barang); $i++) {
                if (!empty($id_barang[$i]) && !empty($jumlah[$i])) {
                    $data_detail = array(
                        'id_packing' => $id,
                        'id_barang' => $id_barang[$i],
                        'jumlah' => $jumlah[$i]
                    );

                    $this->packing_model->insert_detail_packing($data_detail);
                }
            }

            $this->data['success'] = 'Packing berhasil diperbarui!';
            redirect('packing');
        }
    }

    public function hapus($id)
    {
        $packing = $this->packing_model->get_by_id($id);

        if (!$packing) {
            show_404();
        }

        $this->packing_model->delete_packing($id);
        $this->data['success'] = 'Packing berhasil dihapus!';
        redirect('packing');
    }

    public function detail($id)
    {
        $data['title'] = 'Detail Packing';
        $data['content'] = 'packing/detail';
        $data['packing'] = $this->packing_model->get_by_id($id);
        $data['detail_packing'] = $this->packing_model->get_detail_by_packing($id);

        if (!$data['packing']) {
            show_404();
        }

        $this->load->view('template/template', $data);
    }

    public function update_status($id, $status)
    {
        $packing = $this->packing_model->get_by_id($id);

        if (!$packing) {
            show_404();
        }

        $data_packing = array(
            'status' => $status
        );

        $this->packing_model->update_packing($id, $data_packing);

        // Log status change
        $data_log = array(
            'id_transaksi' => $id,
            'tipe_transaksi' => 'packing',
            'id_user' => $this->session->userdata('id_user'),
            'status' => $status,
            'keterangan' => $this->input->post('keterangan')
        );

        $this->packing_model->insert_log_status_transaksi($data_log);

        // Update status referensi (penjualan/transfer) if status is Completed
        if ($status == 'Completed') {
            if ($packing->tipe_referensi == 'penjualan') {
                $this->penjualan_model->update_status($packing->id_referensi, 'Packing');
            } elseif ($packing->tipe_referensi == 'transfer') {
                $this->transfer_model->update_status($packing->id_referensi, 'Packing');
            }
        }

        $this->data['success'] = 'Status packing berhasil diperbarui!';
        redirect('packing/detail/' . $id);
    }

    public function get_referensi_detail()
    {
        $tipe_referensi = $this->input->post('tipe_referensi');
        $id_referensi = $this->input->post('id_referensi');

        if ($tipe_referensi == 'penjualan') {
            $detail = $this->penjualan_model->get_detail_by_penjualan($id_referensi);
        } elseif ($tipe_referensi == 'transfer') {
            $detail = $this->transfer_model->get_detail_by_transfer($id_referensi);
        }

        echo json_encode($detail);
    }
    public function get_penjualan_options()
    {
        $id_perusahaan = $this->session->userdata('id_perusahaan');

        $this->db->select('penjualan.id_penjualan, penjualan.no_invoice, pelanggan.nama_pelanggan');
        $this->db->from('penjualan');
        $this->db->join('pelanggan', 'pelanggan.id_pelanggan = penjualan.id_pelanggan');
        $this->db->where('penjualan.id_perusahaan', $id_perusahaan);
        $this->db->where('penjualan.status', 'Draft');
        $this->db->order_by('penjualan.tanggal_penjualan', 'DESC');
        $penjualan = $this->db->get()->result();

        echo json_encode($penjualan);
    }

    public function get_transfer_options()
    {
        $id_perusahaan = $this->session->userdata('id_perusahaan');

        $this->db->select('transfer_stok.id_transfer, transfer_stok.no_transfer');
        $this->db->from('transfer_stok');
        $this->db->where('transfer_stok.id_perusahaan', $id_perusahaan);
        $this->db->where('transfer_stok.status', 'Draft');
        $this->db->order_by('transfer_stok.tanggal', 'DESC');
        $transfer = $this->db->get()->result();

        echo json_encode($transfer);
    }
}