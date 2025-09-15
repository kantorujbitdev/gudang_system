<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Penerimaan extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->data['title'] = 'Penerimaan Barang';
        $this->render_view('aktifitas/penerimaan/index');
    }
}