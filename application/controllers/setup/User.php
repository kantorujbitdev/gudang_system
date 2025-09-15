<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->data['title'] = 'User Management';
        $this->render_view('setup/user/index');
    }
}