<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Retur_pembelian extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Daftar/Retur_pembelian_model', 'retur');
        $this->load->helper('form');
        $this->load->library('form_validation');

        // Cek akses menu
        $this->check_menu_access('daftar/retur_pembelian');
    }

    public function index()
    {
        $this->data['title'] = 'Daftar Retur Pembelian';
        $this->data['supplier'] = $this->retur->get_supplier();

        // Set filter default
        $filter = [
            'tanggal_awal' => date('Y-m-01'),
            'tanggal_akhir' => date('Y-m-d'),
            'status' => '',
            'id_supplier' => ''
        ];

        $this->data['filter'] = $filter;
        $this->data['retur'] = $this->retur->get_filtered($filter);

        $this->render_view('daftar/retur_pembelian/index');
    }

    public function filter()
    {
        $this->data['title'] = 'Daftar Retur Pembelian';
        $this->data['supplier'] = $this->retur->get_supplier();

        // Get filter from POST
        $filter = [
            'tanggal_awal' => $this->input->post('tanggal_awal') ?: date('Y-m-01'),
            'tanggal_akhir' => $this->input->post('tanggal_akhir') ?: date('Y-m-d'),
            'status' => $this->input->post('status'),
            'id_supplier' => $this->input->post('id_supplier')
        ];

        $this->data['filter'] = $filter;
        $this->data['retur'] = $this->retur->get_filtered($filter);

        $this->render_view('daftar/retur_pembelian/index');
    }

    public function detail($id_retur)
    {
        $this->data['title'] = 'Detail Retur Pembelian';
        $this->data['retur'] = $this->retur->get($id_retur);
        $this->data['detail'] = $this->retur->get_detail($id_retur);

        if (!$this->data['retur']) {
            show_404();
        }

        $this->render_view('daftar/retur_pembelian/detail');
    }

    public function cetak($id_retur)
    {
        $this->data['retur'] = $this->retur->get($id_retur);
        $this->data['detail'] = $this->retur->get_detail($id_retur);

        if (!$this->data['retur']) {
            show_404();
        }

        $this->load->view('daftar/retur_pembelian/cetak', $this->data);
    }

    /* ==============================
     * Tambahan CRUD dengan Flashdata
     * ============================== */

    public function tambah()
    {
        $this->data['title'] = 'Tambah Retur Pembelian';
        $this->_set_validation_rules();

        if ($this->form_validation->run() === FALSE) {
            // render ulang form jika validasi gagal
            $this->render_view('daftar/retur_pembelian/form');
        } else {
            if ($this->retur->insert($this->input->post())) {
                $this->session->set_flashdata('success', 'Data retur berhasil ditambahkan.');
                redirect('daftar/retur_pembelian');
            } else {
                $this->session->set_flashdata('error', 'Gagal menambahkan data retur.');
                redirect('daftar/retur_pembelian/tambah');
            }
        }
    }

    public function edit($id_retur)
    {
        $this->data['title'] = 'Edit Retur Pembelian';
        $this->data['retur'] = $this->retur->get($id_retur);

        if (!$this->data['retur']) {
            show_404();
        }

        $this->_set_validation_rules();

        if ($this->form_validation->run() === FALSE) {
            $this->render_view('daftar/retur_pembelian/form');
        } else {
            if ($this->retur->update($id_retur, $this->input->post())) {
                $this->session->set_flashdata('success', 'Data retur berhasil diperbarui.');
                redirect('daftar/retur_pembelian');
            } else {
                $this->session->set_flashdata('error', 'Gagal memperbarui data retur.');
                redirect('daftar/retur_pembelian/edit/' . $id_retur);
            }
        }
    }

    public function hapus($id_retur)
    {
        if ($this->retur->delete($id_retur)) {
            $this->session->set_flashdata('success', 'Data retur berhasil dihapus.');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus data retur.');
        }
        redirect('daftar/retur_pembelian');
    }

    public function aktifkan($id_retur)
    {
        if ($this->retur->update($id_retur, ['status' => 'aktif'])) {
            $this->session->set_flashdata('success', 'Data retur berhasil diaktifkan.');
        } else {
            $this->session->set_flashdata('error', 'Gagal mengaktifkan data retur.');
        }
        redirect('daftar/retur_pembelian');
    }

    public function nonaktifkan($id_retur)
    {
        if ($this->retur->update($id_retur, ['status' => 'nonaktif'])) {
            $this->session->set_flashdata('success', 'Data retur berhasil dinonaktifkan.');
        } else {
            $this->session->set_flashdata('error', 'Gagal menonaktifkan data retur.');
        }
        redirect('daftar/retur_pembelian');
    }

    /* ==============================
     * Private Method
     * ============================== */
    private function _set_validation_rules()
    {
        $this->form_validation->set_rules('id_supplier', 'Supplier', 'required');
        $this->form_validation->set_rules('tanggal', 'Tanggal Retur', 'required');
        $this->form_validation->set_rules('keterangan', 'Keterangan', 'trim');
    }
}
