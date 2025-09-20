<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Retur_penjualan extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Daftar/Retur_penjualan_model', 'retur');
        $this->load->helper('form');
        $this->load->library('form_validation');

        // Cek akses menu
        $this->check_menu_access('daftar/retur_penjualan');
    }

    public function index()
    {
        $this->data['title'] = 'Daftar Retur Penjualan';
        $this->data['pelanggan'] = $this->retur->get_pelanggan();

        $filter = [
            'tanggal_awal' => date('Y-m-01'),
            'tanggal_akhir' => date('Y-m-d'),
            'status' => '',
            'id_pelanggan' => ''
        ];

        $this->data['filter'] = $filter;
        $this->data['retur'] = $this->retur->get_filtered($filter);

        $this->render_view('daftar/retur_penjualan/index');
    }

    public function filter()
    {
        $this->form_validation->set_rules('tanggal_awal', 'Tanggal Awal', 'required');
        $this->form_validation->set_rules('tanggal_akhir', 'Tanggal Akhir', 'required');

        if ($this->form_validation->run() === FALSE) {
            // validasi gagal → render ulang
            $this->data['title'] = 'Daftar Retur Penjualan';
            $this->data['pelanggan'] = $this->retur->get_pelanggan();
            $this->data['filter'] = $this->input->post();
            $this->data['retur'] = [];

            $this->render_view('daftar/retur_penjualan/index');
        } else {
            // validasi sukses → ambil data
            $filter = [
                'tanggal_awal' => $this->input->post('tanggal_awal'),
                'tanggal_akhir' => $this->input->post('tanggal_akhir'),
                'status' => $this->input->post('status'),
                'id_pelanggan' => $this->input->post('id_pelanggan')
            ];

            $this->data['title'] = 'Daftar Retur Penjualan';
            $this->data['pelanggan'] = $this->retur->get_pelanggan();
            $this->data['filter'] = $filter;
            $this->data['retur'] = $this->retur->get_filtered($filter);

            $this->render_view('daftar/retur_penjualan/index');
        }
    }

    public function detail($id_retur)
    {
        $this->data['title'] = 'Detail Retur Penjualan';
        $this->data['retur'] = $this->retur->get($id_retur);
        $this->data['detail'] = $this->retur->get_detail($id_retur);

        if (!$this->data['retur']) {
            show_404();
        }

        $this->render_view('daftar/retur_penjualan/detail');
    }

    public function cetak($id_retur)
    {
        $this->data['retur'] = $this->retur->get($id_retur);
        $this->data['detail'] = $this->retur->get_detail($id_retur);

        if (!$this->data['retur']) {
            show_404();
        }

        $this->load->view('daftar/retur_penjualan/cetak', $this->data);
    }

    // =========================
    // Tambahan aksi CRUD
    // =========================
    public function tambah()
    {
        $this->form_validation->set_rules('id_pelanggan', 'Pelanggan', 'required');
        $this->form_validation->set_rules('tanggal_retur', 'Tanggal Retur', 'required');

        if ($this->form_validation->run() === FALSE) {
            $this->data['title'] = 'Tambah Retur Penjualan';
            $this->data['pelanggan'] = $this->retur->get_pelanggan();

            $this->render_view('daftar/retur_penjualan/form');
        } else {
            $data = [
                'id_pelanggan' => $this->input->post('id_pelanggan'),
                'tanggal_retur' => $this->input->post('tanggal_retur'),
                'status' => 'draft',
                'created_at' => date('Y-m-d H:i:s')
            ];

            if ($this->retur->insert($data)) {
                $this->session->set_flashdata('success', 'Retur berhasil ditambahkan.');
                redirect('daftar/retur_penjualan');
            } else {
                $this->session->set_flashdata('error', 'Gagal menambahkan retur.');
                redirect('daftar/retur_penjualan/tambah');
            }
        }
    }

    public function edit($id_retur)
    {
        $this->form_validation->set_rules('tanggal_retur', 'Tanggal Retur', 'required');

        if ($this->form_validation->run() === FALSE) {
            $this->data['title'] = 'Edit Retur Penjualan';
            $this->data['pelanggan'] = $this->retur->get_pelanggan();
            $this->data['retur'] = $this->retur->get($id_retur);

            if (!$this->data['retur']) {
                show_404();
            }

            $this->render_view('daftar/retur_penjualan/form');
        } else {
            $data = [
                'tanggal_retur' => $this->input->post('tanggal_retur'),
                'status' => $this->input->post('status')
            ];

            if ($this->retur->update($id_retur, $data)) {
                $this->session->set_flashdata('success', 'Retur berhasil diperbarui.');
                redirect('daftar/retur_penjualan');
            } else {
                $this->session->set_flashdata('error', 'Gagal memperbarui retur.');
                redirect('daftar/retur_penjualan/edit/' . $id_retur);
            }
        }
    }

    public function hapus($id_retur)
    {
        if ($this->retur->delete($id_retur)) {
            $this->session->set_flashdata('success', 'Retur berhasil dihapus.');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus retur.');
        }
        redirect('daftar/retur_penjualan');
    }
}
