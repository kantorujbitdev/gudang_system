<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pelanggan extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->data['title'] = 'Pelanggan';
        $this->render_view('setup/pelanggan/index');
    }
}