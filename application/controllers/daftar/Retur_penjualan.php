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

        // Set filter default
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
        $this->data['title'] = 'Daftar Retur Penjualan';
        $this->data['pelanggan'] = $this->retur->get_pelanggan();

        // Get filter from POST
        $filter = [
            'tanggal_awal' => $this->input->post('tanggal_awal') ?: date('Y-m-01'),
            'tanggal_akhir' => $this->input->post('tanggal_akhir') ?: date('Y-m-d'),
            'status' => $this->input->post('status'),
            'id_pelanggan' => $this->input->post('id_pelanggan')
        ];

        $this->data['filter'] = $filter;
        $this->data['retur'] = $this->retur->get_filtered($filter);

        $this->render_view('daftar/retur_penjualan/index');
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
}