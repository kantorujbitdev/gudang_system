<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Dashboard extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(['Stok_model', 'Auth_model']);
    }
    public function index()
    {
        $this->data['title'] = 'Dashboard';
        // Get data dashboard
        $this->data['total_barang'] = $this->Stok_model->get_total_barang();
        $this->data['total_gudang'] = $this->Stok_model->get_total_gudang();
        $this->data['stok_menipis'] = $this->Stok_model->get_stok_menipis();
        $this->data['transaksi_hari_ini'] = $this->Stok_model->get_transaksi_hari_ini();
        // Get chart data
        $this->data['chart_stok'] = $this->Stok_model->get_chart_stok();
        $this->data['chart_transaksi'] = $this->Stok_model->get_chart_transaksi();

        // Add page title for header
        $this->data['page_title'] = 'Dashboard';

        $this->render_view('dashboard/index');
    }
}