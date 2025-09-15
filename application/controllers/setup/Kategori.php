<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Kategori extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('setup/Kategori_model');
        $this->load->helper('form');
    }

    public function index()
    {
        $this->data['title'] = 'Kategori Barang';
        $this->data['kategori'] = $this->Kategori_model->get_all();
        $this->render_view('setup/kategori/index');
    }

    public function tambah()
    {
        $this->data['title'] = 'Tambah Kategori Barang';

        if ($this->input->post()) {
            $this->form_validation->set_rules('nama_kategori', 'Nama Kategori', 'required|trim');
            $this->form_validation->set_rules('deskripsi', 'Deskripsi', 'trim');

            if ($this->form_validation->run() == TRUE) {
                $data = array(
                    'id_perusahaan' => $this->session->userdata('id_perusahaan'),
                    'nama_kategori' => $this->input->post('nama_kategori'),
                    'deskripsi' => $this->input->post('deskripsi'),
                    'status_aktif' => 1
                );

                if ($this->Kategori_model->insert($data)) {
                    $this->session->set_flashdata('success', 'Kategori barang berhasil ditambahkan!');
                    redirect('setup/kategori');
                } else {
                    $this->session->set_flashdata('error', 'Gagal menambahkan kategori barang!');
                }
            }
        }

        $this->render_view('setup/kategori/form');
    }

    public function edit($id_kategori)
    {
        $this->data['title'] = 'Edit Kategori Barang';
        $this->data['kategori'] = $this->Kategori_model->get($id_kategori);

        if (!$this->data['kategori']) {
            show_404();
        }

        if ($this->input->post()) {
            $this->form_validation->set_rules('nama_kategori', 'Nama Kategori', 'required|trim');
            $this->form_validation->set_rules('deskripsi', 'Deskripsi', 'trim');

            if ($this->form_validation->run() == TRUE) {
                $data = array(
                    'nama_kategori' => $this->input->post('nama_kategori'),
                    'deskripsi' => $this->input->post('deskripsi')
                );

                if ($this->Kategori_model->update($id_kategori, $data)) {
                    $this->session->set_flashdata('success', 'Kategori barang berhasil diperbarui!');
                    redirect('setup/kategori');
                } else {
                    $this->session->set_flashdata('error', 'Gagal memperbarui kategori barang!');
                }
            }
        }

        $this->render_view('setup/kategori/form');
    }

    public function hapus($id_kategori)
    {
        $kategori = $this->Kategori_model->get($id_kategori);

        if (!$kategori) {
            show_404();
        }

        if ($this->Kategori_model->delete($id_kategori)) {
            $this->session->set_flashdata('success', 'Kategori barang berhasil dihapus!');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus kategori barang!');
        }

        redirect('setup/kategori');
    }
}