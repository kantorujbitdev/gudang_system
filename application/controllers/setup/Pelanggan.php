<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pelanggan extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('auth');
        $this->load->model('pelanggan_model');
        $this->load->library('form_validation');

        if (!$this->auth->is_logged_in()) {
            redirect('auth');
        }

        if (!$this->auth->has_permission('pelanggan')) {
            show_error('Anda tidak memiliki akses ke halaman ini', 403);
        }
    }

    public function index()
    {
        $data['title'] = 'Pelanggan';
        $data['content'] = 'pelanggan/index';
        $data['pelanggan'] = $this->pelanggan_model->get_all();

        $this->load->view('template/template', $data);
    }

    public function tambah()
    {
        $data['title'] = 'Tambah Pelanggan';
        $data['content'] = 'pelanggan/tambah';

        $this->form_validation->set_rules('nama_pelanggan', 'Nama Pelanggan', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('template/template', $data);
        } else {
            $data_pelanggan = array(
                'id_perusahaan' => $this->session->userdata('id_perusahaan'),
                'nama_pelanggan' => $this->input->post('nama_pelanggan'),
                'alamat' => $this->input->post('alamat'),
                'telepon' => $this->input->post('telepon'),
                'email' => $this->input->post('email'),
                'tipe_pelanggan' => $this->input->post('tipe_pelanggan'),
                'status_aktif' => 1
            );

            $this->pelanggan_model->insert($data_pelanggan);
            $this->session->set_flashdata('success', 'Pelanggan berhasil ditambahkan!');
            redirect('pelanggan');
        }
    }

    public function edit($id)
    {
        $data['title'] = 'Edit Pelanggan';
        $data['content'] = 'pelanggan/edit';
        $data['pelanggan'] = $this->pelanggan_model->get_by_id($id);

        if (!$data['pelanggan']) {
            show_404();
        }

        $this->form_validation->set_rules('nama_pelanggan', 'Nama Pelanggan', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('template/template', $data);
        } else {
            $data_pelanggan = array(
                'nama_pelanggan' => $this->input->post('nama_pelanggan'),
                'alamat' => $this->input->post('alamat'),
                'telepon' => $this->input->post('telepon'),
                'email' => $this->input->post('email'),
                'tipe_pelanggan' => $this->input->post('tipe_pelanggan'),
                'status_aktif' => $this->input->post('status_aktif')
            );

            $this->pelanggan_model->update($id, $data_pelanggan);
            $this->session->set_flashdata('success', 'Pelanggan berhasil diperbarui!');
            redirect('pelanggan');
        }
    }

    public function hapus($id)
    {
        $pelanggan = $this->pelanggan_model->get_by_id($id);

        if (!$pelanggan) {
            show_404();
        }

        $this->pelanggan_model->delete($id);
        $this->session->set_flashdata('success', 'Pelanggan berhasil dihapus!');
        redirect('pelanggan');
    }
}