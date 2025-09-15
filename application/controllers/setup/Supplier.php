<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Supplier extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->data['title'] = 'Supplier';
        $this->render_view('setup/supplier/index');
    }
}