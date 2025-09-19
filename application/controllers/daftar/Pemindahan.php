<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pemindahan extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Daftar/Pemindahan_model', 'pemindahan');
        $this->load->helper('form');
        $this->load->library('form_validation');

        // Cek akses menu
        $this->check_menu_access('daftar/pemindahan');
    }

    public function index()
    {
        $this->data['title'] = 'Daftar Pemindahan Barang';
        $this->data['gudang'] = $this->pemindahan->get_gudang();
        $this->data['pelanggan'] = $this->pemindahan->get_pelanggan();

        // Set filter default
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

        // Get filter from POST
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
            show_404();
        }

        $this->render_view('daftar/pemindahan/detail');
    }

    public function cetak($id_transfer)
    {
        $this->data['pemindahan'] = $this->pemindahan->get($id_transfer);
        $this->data['detail'] = $this->pemindahan->get_detail($id_transfer);

        if (!$this->data['pemindahan']) {
            show_404();
        }

        $this->load->view('daftar/pemindahan/cetak', $this->data);
    }
}