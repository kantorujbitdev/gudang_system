<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pemindahan extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->data['title'] = 'Pemindahan Barang';
        $this->render_view('aktifitas/pemindahan/index');
    }
}