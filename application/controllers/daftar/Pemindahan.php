<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pemindahan extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Daftar/Pemindahan_model', 'pemindahan');
        $this->load->helper(['form', 'url']);
        $this->load->library('form_validation');

        // Cek akses menu
        $this->check_menu_access('daftar/pemindahan');
    }

    public function index()
    {
        $this->data['title'] = 'Daftar Pemindahan Barang';
        $this->data['gudang'] = $this->pemindahan->get_gudang();
        $this->data['pelanggan'] = $this->pemindahan->get_pelanggan();

        // Default filter
        $filter = [
            'tanggal_awal' => date('Y-m-01'),
            'tanggal_akhir' => date('Y-m-d'),
            'status' => '',
            'id_gudang' => '',
            'id_pelanggan' => ''
        ];

        $this->data['filter'] = $filter;
        $this->data['pemindahan'] = $this->pemindahan->get_filtered($filter);

        $this->render_view('daftar/pemindahan/index');
    }

    public function filter()
    {
        $this->data['title'] = 'Daftar Pemindahan Barang';
        $this->data['gudang'] = $this->pemindahan->get_gudang();
        $this->data['pelanggan'] = $this->pemindahan->get_pelanggan();

        // Ambil filter dari POST
        $filter = [
            'tanggal_awal' => $this->input->post('tanggal_awal') ?: date('Y-m-01'),
            'tanggal_akhir' => $this->input->post('tanggal_akhir') ?: date('Y-m-d'),
            'status' => $this->input->post('status'),
            'id_gudang' => $this->input->post('id_gudang'),
            'id_pelanggan' => $this->input->post('id_pelanggan')
        ];

        $this->data['filter'] = $filter;
        $this->data['pemindahan'] = $this->pemindahan->get_filtered($filter);

        $this->render_view('daftar/pemindahan/index');
    }

    public function detail($id_transfer)
    {
        $this->data['title'] = 'Detail Pemindahan Barang';
        $this->data['pemindahan'] = $this->pemindahan->get($id_transfer);
        $this->data['detail'] = $this->pemindahan->get_detail($id_transfer);

        if (!$this->data['pemindahan']) {
            $this->session->set_flashdata('error', 'Data pemindahan tidak ditemukan.');
            redirect('daftar/pemindahan');
        }

        $this->render_view('daftar/pemindahan/detail');
    }

    public function cetak($id_transfer)
    {
        $this->data['pemindahan'] = $this->pemindahan->get($id_transfer);
        $this->data['detail'] = $this->pemindahan->get_detail($id_transfer);

        if (!$this->data['pemindahan']) {
            $this->session->set_flashdata('error', 'Data pemindahan tidak ditemukan.');
            redirect('daftar/pemindahan');
        }

        $this->load->view('daftar/pemindahan/cetak', $this->data);
    }

    // ==================== Tambah ====================
    public function tambah()
    {
        $this->data['title'] = 'Tambah Pemindahan Barang';
        $this->_set_validation_rules();

        if ($this->form_validation->run() === FALSE) {
            // Kalau gagal validasi, tetap di form
            $this->data['gudang'] = $this->pemindahan->get_gudang();
            $this->data['pelanggan'] = $this->pemindahan->get_pelanggan();
            return $this->render_view('daftar/pemindahan/form');
        }

        if ($this->pemindahan->insert($this->input->post())) {
            $this->session->set_flashdata('success', 'Data pemindahan berhasil ditambahkan.');
        } else {
            $this->session->set_flashdata('error', 'Gagal menambahkan data pemindahan.');
        }
        redirect('daftar/pemindahan');
    }

    // ==================== Edit ====================
    public function edit($id_transfer)
    {
        $this->data['title'] = 'Edit Pemindahan Barang';
        $this->_set_validation_rules();

        if ($this->form_validation->run() === FALSE) {
            $this->data['pemindahan'] = $this->pemindahan->get($id_transfer);
            if (!$this->data['pemindahan']) {
                $this->session->set_flashdata('error', 'Data pemindahan tidak ditemukan.');
                redirect('daftar/pemindahan');
            }
            $this->data['gudang'] = $this->pemindahan->get_gudang();
            $this->data['pelanggan'] = $this->pemindahan->get_pelanggan();
            return $this->render_view('daftar/pemindahan/form');
        }

        if ($this->pemindahan->update($id_transfer, $this->input->post())) {
            $this->session->set_flashdata('success', 'Data pemindahan berhasil diperbarui.');
        } else {
            $this->session->set_flashdata('error', 'Gagal memperbarui data pemindahan.');
        }
        redirect('daftar/pemindahan');
    }

    // ==================== Hapus ====================
    public function hapus($id_transfer)
    {
        if ($this->pemindahan->delete($id_transfer)) {
            $this->session->set_flashdata('success', 'Data pemindahan berhasil dihapus.');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus data pemindahan.');
        }
        redirect('daftar/pemindahan');
    }

    // ==================== Aktifkan ====================
    public function aktifkan($id_transfer)
    {
        if ($this->pemindahan->set_status($id_transfer, 1)) {
            $this->session->set_flashdata('success', 'Pemindahan berhasil diaktifkan.');
        } else {
            $this->session->set_flashdata('error', 'Gagal mengaktifkan pemindahan.');
        }
        redirect('daftar/pemindahan');
    }

    // ==================== Nonaktifkan ====================
    public function nonaktifkan($id_transfer)
    {
        if ($this->pemindahan->set_status($id_transfer, 0)) {
            $this->session->set_flashdata('success', 'Pemindahan berhasil dinonaktifkan.');
        } else {
            $this->session->set_flashdata('error', 'Gagal menonaktifkan pemindahan.');
        }
        redirect('daftar/pemindahan');
    }

    // ==================== Validation Rules ====================
    private function _set_validation_rules()
    {
        $this->form_validation->set_rules('tanggal', 'Tanggal', 'required');
        $this->form_validation->set_rules('id_gudang', 'Gudang', 'required');
        $this->form_validation->set_rules('id_pelanggan', 'Pelanggan', 'required');
        // Tambahkan rules lain sesuai kebutuhan
    }
}
