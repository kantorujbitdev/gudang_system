<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Penerimaan extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('auth');
        $this->load->model('penerimaan_model');
        $this->load->model('pembelian_model');
        $this->load->model('barang_model');
        $this->load->model('gudang_model');
        $this->load->model('stok_model');
        $this->load->library('form_validation');

        if (!$this->auth->is_logged_in()) {
            redirect('auth');
        }

        if (!$this->auth->has_permission('penerimaan')) {
            show_error('Anda tidak memiliki akses ke halaman ini', 403);
        }
    }

    public function index()
    {
        $data['title'] = 'Penerimaan Barang';
        $data['content'] = 'penerimaan/index';
        $data['penerimaan'] = $this->penerimaan_model->get_all();

        $this->load->view('template/template', $data);
    }

    public function tambah()
    {
        $data['title'] = 'Tambah Penerimaan Barang';
        $data['content'] = 'penerimaan/tambah';
        $data['pembelian'] = $this->pembelian_model->get_pembelian_for_penerimaan();
        $data['gudang'] = $this->gudang_model->get_all();

        $this->form_validation->set_rules('id_pembelian', 'Pembelian', 'required');
        $this->form_validation->set_rules('id_gudang', 'Gudang', 'required');
        $this->form_validation->set_rules('tanggal_penerimaan', 'Tanggal Penerimaan', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('template/template', $data);
        } else {
            // Generate nomor penerimaan
            $no_penerimaan = 'RCV-' . date('YmdHis');

            $data_penerimaan = array(
                'no_penerimaan' => $no_penerimaan,
                'id_pembelian' => $this->input->post('id_pembelian'),
                'id_user' => $this->session->userdata('id_user'),
                'id_gudang' => $this->input->post('id_gudang'),
                'tanggal_penerimaan' => $this->input->post('tanggal_penerimaan'),
                'keterangan' => $this->input->post('keterangan'),
                'status' => 'Draft'
            );

            $id_penerimaan = $this->penerimaan_model->insert_penerimaan($data_penerimaan);

            // Insert detail penerimaan
            $id_barang = $this->input->post('id_barang');
            $jumlah_diterima = $this->input->post('jumlah_diterima');
            $jumlah_dipesan = $this->input->post('jumlah_dipesan');

            for ($i = 0; $i < count($id_barang); $i++) {
                if (!empty($id_barang[$i]) && !empty($jumlah_diterima[$i])) {
                    $data_detail = array(
                        'id_penerimaan' => $id_penerimaan,
                        'id_barang' => $id_barang[$i],
                        'jumlah_diterima' => $jumlah_diterima[$i],
                        'jumlah_dipesan' => $jumlah_dipesan[$i],
                        'keterangan' => $this->input->post('keterangan_barang')[$i]
                    );

                    $this->penerimaan_model->insert_detail_penerimaan($data_detail);
                }
            }

            $this->session->set_flashdata('success', 'Penerimaan barang berhasil ditambahkan!');
            redirect('penerimaan');
        }
    }

    public function edit($id)
    {
        $data['title'] = 'Edit Penerimaan Barang';
        $data['content'] = 'penerimaan/edit';
        $data['penerimaan'] = $this->penerimaan_model->get_by_id($id);
        $data['detail_penerimaan'] = $this->penerimaan_model->get_detail_by_penerimaan($id);
        $data['pembelian'] = $this->pembelian_model->get_pembelian_for_penerimaan();
        $data['gudang'] = $this->gudang_model->get_all();

        if (!$data['penerimaan']) {
            show_404();
        }

        $this->form_validation->set_rules('id_pembelian', 'Pembelian', 'required');
        $this->form_validation->set_rules('id_gudang', 'Gudang', 'required');
        $this->form_validation->set_rules('tanggal_penerimaan', 'Tanggal Penerimaan', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('template/template', $data);
        } else {
            $data_penerimaan = array(
                'id_pembelian' => $this->input->post('id_pembelian'),
                'id_gudang' => $this->input->post('id_gudang'),
                'tanggal_penerimaan' => $this->input->post('tanggal_penerimaan'),
                'keterangan' => $this->input->post('keterangan')
            );

            $this->penerimaan_model->update_penerimaan($id, $data_penerimaan);

            // Delete existing detail
            $this->penerimaan_model->delete_detail_penerimaan($id);

            // Insert new detail
            $id_barang = $this->input->post('id_barang');
            $jumlah_diterima = $this->input->post('jumlah_diterima');
            $jumlah_dipesan = $this->input->post('jumlah_dipesan');

            for ($i = 0; $i < count($id_barang); $i++) {
                if (!empty($id_barang[$i]) && !empty($jumlah_diterima[$i])) {
                    $data_detail = array(
                        'id_penerimaan' => $id,
                        'id_barang' => $id_barang[$i],
                        'jumlah_diterima' => $jumlah_diterima[$i],
                        'jumlah_dipesan' => $jumlah_dipesan[$i],
                        'keterangan' => $this->input->post('keterangan_barang')[$i]
                    );

                    $this->penerimaan_model->insert_detail_penerimaan($data_detail);
                }
            }

            $this->session->set_flashdata('success', 'Penerimaan barang berhasil diperbarui!');
            redirect('penerimaan');
        }
    }

    public function hapus($id)
    {
        $penerimaan = $this->penerimaan_model->get_by_id($id);

        if (!$penerimaan) {
            show_404();
        }

        $this->penerimaan_model->delete_penerimaan($id);
        $this->session->set_flashdata('success', 'Penerimaan barang berhasil dihapus!');
        redirect('penerimaan');
    }

    public function detail($id)
    {
        $data['title'] = 'Detail Penerimaan Barang';
        $data['content'] = 'penerimaan/detail';
        $data['penerimaan'] = $this->penerimaan_model->get_by_id($id);
        $data['detail_penerimaan'] = $this->penerimaan_model->get_detail_by_penerimaan($id);

        if (!$data['penerimaan']) {
            show_404();
        }

        $this->load->view('template/template', $data);
    }

    public function update_status($id, $status)
    {
        $penerimaan = $this->penerimaan_model->get_by_id($id);

        if (!$penerimaan) {
            show_404();
        }

        $data_penerimaan = array(
            'status' => $status
        );

        $this->penerimaan_model->update_penerimaan($id, $data_penerimaan);

        // Log status change
        $data_log = array(
            'id_penerimaan' => $id,
            'id_user' => $this->session->userdata('id_user'),
            'status' => $status,
            'keterangan' => $this->input->post('keterangan')
        );

        $this->penerimaan_model->insert_log_status_penerimaan($data_log);

        // Update stock if status is Received or Completed
        if ($status == 'Received' || $status == 'Completed') {
            $detail_penerimaan = $this->penerimaan_model->get_detail_by_penerimaan($id);

            foreach ($detail_penerimaan as $detail) {
                // Check if stock exists
                $stok = $this->stok_model->get_stok_by_barang_gudang($detail->id_barang, $penerimaan->id_gudang);

                if ($stok) {
                    // Update existing stock
                    $data_stok = array(
                        'jumlah' => $stok->jumlah + $detail->jumlah_diterima
                    );
                    $this->stok_model->update_stok($stok->id_stok, $data_stok);
                } else {
                    // Insert new stock
                    $data_stok = array(
                        'id_perusahaan' => $this->session->userdata('id_perusahaan'),
                        'id_gudang' => $penerimaan->id_gudang,
                        'id_barang' => $detail->id_barang,
                        'jumlah' => $detail->jumlah_diterima
                    );
                    $this->stok_model->insert_stok($data_stok);
                }

                // Log stock movement
                $data_log_stok = array(
                    'id_barang' => $detail->id_barang,
                    'id_user' => $this->session->userdata('id_user'),
                    'id_perusahaan' => $this->session->userdata('id_perusahaan'),
                    'id_gudang' => $penerimaan->id_gudang,
                    'jenis' => 'masuk',
                    'jumlah' => $detail->jumlah_diterima,
                    'sisa_stok' => $stok ? ($stok->jumlah + $detail->jumlah_diterima) : $detail->jumlah_diterima,
                    'keterangan' => 'Penerimaan barang dari pembelian',
                    'id_referensi' => $id,
                    'tipe_referensi' => 'penerimaan'
                );

                $this->stok_model->insert_log_stok($data_log_stok);
            }
        }

        $this->session->set_flashdata('success', 'Status penerimaan barang berhasil diperbarui!');
        redirect('penerimaan/detail/' . $id);
    }

    public function get_pembelian_detail()
    {
        $id_pembelian = $this->input->post('id_pembelian');
        $detail_pembelian = $this->pembelian_model->get_detail_by_pembelian($id_pembelian);

        echo json_encode($detail_pembelian);
    }
}