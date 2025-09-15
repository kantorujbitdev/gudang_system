<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pengaturan_hak_akses extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('auth');
        $this->load->model('hak_akses_model');
        $this->load->model('role_model');
        $this->load->model('menu_model');
        $this->load->library('form_validation');

        if (!$this->auth->is_logged_in()) {
            redirect('auth');
        }

        if (!$this->auth->has_permission('pengaturan/hak_akses')) {
            show_error('Anda tidak memiliki akses ke halaman ini', 403);
        }
    }

    public function index()
    {
        $data['title'] = 'Hak Akses';
        $data['content'] = 'pengaturan/hak_akses/index';
        $data['roles'] = $this->role_model->get_all();
        $data['menus'] = $this->menu_model->get_all();
        $data['hak_akses'] = $this->hak_akses_model->get_all();

        $this->load->view('template/template', $data);
    }

    public function simpan()
    {
        $id_role = $this->input->post('id_role');
        $akses = $this->input->post('akses');

        // Delete existing hak akses for this role
        $this->hak_akses_model->delete_by_role($id_role);

        // Insert new hak akses
        foreach ($akses as $id_menu => $value) {
            $data = array(
                'id_role' => $id_role,
                'id_menu' => $id_menu,
                'akses' => 1
            );

            $this->hak_akses_model->insert($data);
        }

        $this->session->set_flashdata('success', 'Hak akses berhasil disimpan!');
        redirect('pengaturan/hak_akses');
    }
}