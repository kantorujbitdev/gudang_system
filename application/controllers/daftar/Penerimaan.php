<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Penerimaan extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Daftar/Penerimaan_model', 'penerimaan');
        $this->load->helper('form');
        $this->load->library('form_validation');

        // Cek akses menu
        $this->check_menu_access('daftar/penerimaan');
    }

    public function index()
    {
        $this->data['title'] = 'Daftar Penerimaan Barang';
        $this->data['gudang'] = $this->penerimaan->get_gudang();
        $this->data['supplier'] = $this->penerimaan->get_supplier();

        // Set filter default
        $filter = [
            'tanggal_awal' => date('Y-m-01'),
            'tanggal_akhir' => date('Y-m-d'),
            'status' => '',
            'id_gudang' => '',
            'id_supplier' => ''
        ];

        $this->data['filter'] = $filter;
        $this->data['penerimaan'] = $this->penerimaan->get_filtered($filter);

        $this->render_view('daftar/penerimaan/index');
    }

    public function filter()
    {
        $this->data['title'] = 'Daftar Penerimaan Barang';
        $this->data['gudang'] = $this->penerimaan->get_gudang();
        $this->data['supplier'] = $this->penerimaan->get_supplier();

        // Get filter from POST
        $filter = [
            'tanggal_awal' => $this->input->post('tanggal_awal') ?: date('Y-m-01'),
            'tanggal_akhir' => $this->input->post('tanggal_akhir') ?: date('Y-m-d'),
            'status' => $this->input->post('status'),
            'id_gudang' => $this->input->post('id_gudang'),
            'id_supplier' => $this->input->post('id_supplier')
        ];

        $this->data['filter'] = $filter;
        $this->data['penerimaan'] = $this->penerimaan->get_filtered($filter);

        $this->render_view('daftar/penerimaan/index');
    }

    public function detail($id_penerimaan)
    {
        $this->data['title'] = 'Detail Penerimaan Barang';
        $this->data['penerimaan'] = $this->penerimaan->get($id_penerimaan);
        $this->data['detail'] = $this->penerimaan->get_detail($id_penerimaan);

        if (!$this->data['penerimaan']) {
            show_404();
        }

        $this->render_view('daftar/penerimaan/detail');
    }

    public function cetak($id_penerimaan)
    {
        $this->data['penerimaan'] = $this->penerimaan->get($id_penerimaan);
        $this->data['detail'] = $this->penerimaan->get_detail($id_penerimaan);

        if (!$this->data['penerimaan']) {
            show_404();
        }

        $this->load->view('daftar/penerimaan/cetak', $this->data);
    }
}