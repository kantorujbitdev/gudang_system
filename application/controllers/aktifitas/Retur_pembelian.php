<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Retur_pembelian extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->data['title'] = 'Retur Pembelian';
        $this->render_view('aktifitas/retur_pembelian/index');
    }
}