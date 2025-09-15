<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Retur_penjualan extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->data['title'] = 'Retur Penjualan';
        $this->render_view('aktifitas/retur_penjualan/index');
    }
}