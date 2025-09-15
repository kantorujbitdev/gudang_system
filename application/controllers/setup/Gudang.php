<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Gudang extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('setup/Gudang_model');
    }

    public function index()
    {
        $this->data['title'] = 'Gudang';
        $this->data['gudang'] = $this->Gudang_model->get_all();

        $this->render_view('setup/gudang/index');
    }
}