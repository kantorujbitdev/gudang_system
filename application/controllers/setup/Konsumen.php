<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Konsumen extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('setup/Konsumen_model', 'konsumen');
        $this->load->model('setup/Toko_konsumen_model', 'toko_konsumen');
        $this->load->helper('form');
        $this->load->library('form_validation');

        // Cek akses menu
        $this->check_menu_access('setup/konsumen');
    }

    public function get_detail_ajax()
    {
        header('Content-Type: application/json');

        // Cek CSRF token
        $csrf_token_name = $this->security->get_csrf_token_name();
        $posted_token = $this->input->post($csrf_token_name);

        if ($posted_token !== $this->security->get_csrf_hash()) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid CSRF token']);
            return;
        }

        $id_konsumen = $this->input->post('id_konsumen');

        if ($id_konsumen) {
            $konsumen = $this->konsumen->get($id_konsumen);

            if ($konsumen) {
                echo json_encode([
                    'status' => 'success',
                    'alamat_konsumen' => $konsumen->alamat_konsumen
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Konsumen tidak ditemukan'
                ]);
            }
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'ID konsumen tidak valid'
            ]);
        }
    }
}