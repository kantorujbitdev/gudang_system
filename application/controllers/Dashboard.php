<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('auth');
        $this->load->model('dashboard_model');

        if (!$this->auth->is_logged_in()) {
            redirect('auth');
        }
    }

    public function index()
    {
        $data['title'] = 'Dashboard';
        $data['content'] = 'dashboard/index';
        $data['total_barang'] = $this->dashboard_model->get_total_barang();
        $data['total_stok'] = $this->dashboard_model->get_total_stok();
        $data['total_penjualan'] = $this->dashboard_model->get_total_penjualan();
        $data['total_pembelian'] = $this->dashboard_model->get_total_pembelian();
        $data['recent_transactions'] = $this->dashboard_model->get_recent_transactions();
        $data['low_stock_items'] = $this->dashboard_model->get_low_stock_items();

        $this->load->view('template/template', $data);
    }
}