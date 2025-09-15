<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Kategori extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('auth');
        $this->load->model('kategori_model');
        $this->load->library('form_validation');

        if (!$this->auth->is_logged_in()) {
            redirect('auth');
        }

        if (!$this->auth->has_permission('kategori')) {
            show_error('Anda tidak memiliki akses ke halaman ini', 403);
        }
    }

    public function index()
    {
        $data['title'] = 'Kategori';
        $data['content'] = 'kategori/index';
        $data['kategori'] = $this->kategori_model->get_all();

        $this->load->view('template/template', $data);
    }

    public function tambah()
    {
        $data['title'] = 'Tambah Kategori';
        $data['content'] = 'kategori/tambah';

        $this->form_validation->set_rules('nama_kategori', 'Nama Kategori', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('template/template', $data);
        } else {
            $data_kategori = array(
                'id_perusahaan' => $this->session->userdata('id_perusahaan'),
                'nama_kategori' => $this->input->post('nama_kategori'),
                'deskripsi' => $this->input->post('deskripsi'),
                'status_aktif' => 1
            );

            $this->kategori_model->insert($data_kategori);
            $this->session->set_flashdata('success', 'Kategori berhasil ditambahkan!');
            redirect('kategori');
        }
    }

    public function edit($id)
    {
        $data['title'] = 'Edit Kategori';
        $data['content'] = 'kategori/edit';
        $data['kategori'] = $this->kategori_model->get_by_id($id);

        if (!$data['kategori']) {
            show_404();
        }

        $this->form_validation->set_rules('nama_kategori', 'Nama Kategori', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('template/template', $data);
        } else {
            $data_kategori = array(
                'nama_kategori' => $this->input->post('nama_kategori'),
                'deskripsi' => $this->input->post('deskripsi'),
                'status_aktif' => $this->input->post('status_aktif')
            );

            $this->kategori_model->update($id, $data_kategori);
            $this->session->set_flashdata('success', 'Kategori berhasil diperbarui!');
            redirect('kategori');
        }
    }

    public function hapus($id)
    {
        $kategori = $this->kategori_model->get_by_id($id);

        if (!$kategori) {
            show_404();
        }

        $this->kategori_model->delete($id);
        $this->session->set_flashdata('success', 'Kategori berhasil dihapus!');
        redirect('kategori');
    }
}