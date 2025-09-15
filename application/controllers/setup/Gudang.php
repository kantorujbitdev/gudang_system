<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Gudang extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('auth');
        $this->load->model('gudang_model');
        $this->load->library('form_validation');

        if (!$this->auth->is_logged_in()) {
            redirect('auth');
        }

        if (!$this->auth->has_permission('gudang')) {
            show_error('Anda tidak memiliki akses ke halaman ini', 403);
        }
    }

    public function index()
    {
        $data['title'] = 'Gudang';
        $data['content'] = 'gudang/index';
        $data['gudang'] = $this->gudang_model->get_all();

        $this->load->view('template/template', $data);
    }

    public function tambah()
    {
        $data['title'] = 'Tambah Gudang';
        $data['content'] = 'gudang/tambah';

        $this->form_validation->set_rules('nama_gudang', 'Nama Gudang', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('template/template', $data);
        } else {
            $data_gudang = array(
                'id_perusahaan' => $this->session->userdata('id_perusahaan'),
                'nama_gudang' => $this->input->post('nama_gudang'),
                'alamat' => $this->input->post('alamat'),
                'telepon' => $this->input->post('telepon'),
                'created_by' => $this->session->userdata('id_user'),
                'status_aktif' => 1
            );

            $this->gudang_model->insert($data_gudang);
            $this->session->set_flashdata('success', 'Gudang berhasil ditambahkan!');
            redirect('gudang');
        }
    }

    public function edit($id)
    {
        $data['title'] = 'Edit Gudang';
        $data['content'] = 'gudang/edit';
        $data['gudang'] = $this->gudang_model->get_by_id($id);

        if (!$data['gudang']) {
            show_404();
        }

        $this->form_validation->set_rules('nama_gudang', 'Nama Gudang', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('template/template', $data);
        } else {
            $data_gudang = array(
                'nama_gudang' => $this->input->post('nama_gudang'),
                'alamat' => $this->input->post('alamat'),
                'telepon' => $this->input->post('telepon'),
                'status_aktif' => $this->input->post('status_aktif')
            );

            $this->gudang_model->update($id, $data_gudang);
            $this->session->set_flashdata('success', 'Gudang berhasil diperbarui!');
            redirect('gudang');
        }
    }

    public function hapus($id)
    {
        $gudang = $this->gudang_model->get_by_id($id);

        if (!$gudang) {
            show_404();
        }

        $this->gudang_model->delete($id);
        $this->session->set_flashdata('success', 'Gudang berhasil dihapus!');
        redirect('gudang');
    }
}