<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Barang extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('setup/Barang_model');
        $this->load->model('setup/Kategori_model');
    }

    public function index()
    {
        $this->data['title'] = 'Barang';
        $this->data['barang'] = $this->Barang_model->get_all();

        $this->render_view('setup/barang/index');
    }

    public function tambah()
    {
        $this->data['title'] = 'Tambah Barang';
        $this->data['kategori'] = $this->Kategori_model->get_all();

        if ($this->input->post()) {
            $data = array(
                'id_perusahaan' => $this->session->userdata('id_perusahaan'),
                'id_kategori' => $this->input->post('id_kategori'),
                'nama_barang' => $this->input->post('nama_barang'),
                'sku' => $this->input->post('sku'),
                'deskripsi' => $this->input->post('deskripsi'),
                'satuan' => $this->input->post('satuan'),
                'harga_jual' => $this->input->post('harga_jual'),
                'harga_beli_terakhir' => $this->input->post('harga_beli_terakhir'),
                'status_aktif' => 1
            );

            if ($this->Barang_model->insert($data)) {
                $this->session->set_flashdata('success', 'Barang berhasil ditambahkan!');
                redirect('setup/barang');
            } else {
                $this->session->set_flashdata('error', 'Gagal menambahkan barang!');
            }
        }

        $this->render_view('setup/barang/form');
    }

    public function edit($id_barang)
    {
        $this->data['title'] = 'Edit Barang';
        $this->data['barang'] = $this->Barang_model->get($id_barang);
        $this->data['kategori'] = $this->Kategori_model->get_all();

        if (!$this->data['barang']) {
            show_404();
        }

        if ($this->input->post()) {
            $data = array(
                'id_kategori' => $this->input->post('id_kategori'),
                'nama_barang' => $this->input->post('nama_barang'),
                'sku' => $this->input->post('sku'),
                'deskripsi' => $this->input->post('deskripsi'),
                'satuan' => $this->input->post('satuan'),
                'harga_jual' => $this->input->post('harga_jual'),
                'harga_beli_terakhir' => $this->input->post('harga_beli_terakhir')
            );

            if ($this->Barang_model->update($id_barang, $data)) {
                $this->session->set_flashdata('success', 'Barang berhasil diperbarui!');
                redirect('setup/barang');
            } else {
                $this->session->set_flashdata('error', 'Gagal memperbarui barang!');
            }
        }

        $this->render_view('setup/barang/form');
    }

    public function hapus($id_barang)
    {
        $barang = $this->Barang_model->get($id_barang);

        if (!$barang) {
            show_404();
        }

        if ($this->Barang_model->delete($id_barang)) {
            $this->session->set_flashdata('success', 'Barang berhasil dihapus!');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus barang!');
        }

        redirect('setup/barang');
    }
}