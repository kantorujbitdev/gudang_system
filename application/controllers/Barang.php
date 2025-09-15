<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Barang extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('auth');
        $this->load->model('barang_model');
        $this->load->model('kategori_model');
        $this->load->library('form_validation');

        if (!$this->auth->is_logged_in()) {
            redirect('auth');
        }

        if (!$this->auth->has_permission('barang')) {
            show_error('Anda tidak memiliki akses ke halaman ini', 403);
        }
    }

    public function index()
    {
        $data['title'] = 'Barang';
        $data['content'] = 'barang/index';
        $data['barang'] = $this->barang_model->get_all();

        $this->load->view('template/template', $data);
    }

    public function tambah()
    {
        $data['title'] = 'Tambah Barang';
        $data['content'] = 'barang/tambah';
        $data['kategori'] = $this->kategori_model->get_all();

        $this->form_validation->set_rules('nama_barang', 'Nama Barang', 'required');
        $this->form_validation->set_rules('sku', 'SKU', 'required|is_unique[barang.sku]');
        $this->form_validation->set_rules('id_kategori', 'Kategori', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('template/template', $data);
        } else {
            // Handle upload gambar
            $gambar = '';
            if (!empty($_FILES['gambar']['name'])) {
                $config['upload_path'] = './uploads/barang/';
                $config['allowed_types'] = 'gif|jpg|png|jpeg';
                $config['max_size'] = 2048;
                $config['encrypt_name'] = TRUE;

                $this->load->library('upload', $config);

                if ($this->upload->do_upload('gambar')) {
                    $upload_data = $this->upload->data();
                    $gambar = $upload_data['file_name'];
                }
            }

            $data_barang = array(
                'id_perusahaan' => $this->session->userdata('id_perusahaan'),
                'id_kategori' => $this->input->post('id_kategori'),
                'nama_barang' => $this->input->post('nama_barang'),
                'sku' => $this->input->post('sku'),
                'deskripsi' => $this->input->post('deskripsi'),
                'satuan' => $this->input->post('satuan'),
                'harga_jual' => $this->input->post('harga_jual'),
                'harga_beli_terakhir' => $this->input->post('harga_beli_terakhir'),
                'gambar' => $gambar,
                'aktif' => 1
            );

            $this->barang_model->insert($data_barang);
            $this->session->set_flashdata('success', 'Barang berhasil ditambahkan!');
            redirect('barang');
        }
    }

    public function edit($id)
    {
        $data['title'] = 'Edit Barang';
        $data['content'] = 'barang/edit';
        $data['barang'] = $this->barang_model->get_by_id($id);
        $data['kategori'] = $this->kategori_model->get_all();

        if (!$data['barang']) {
            show_404();
        }

        $this->form_validation->set_rules('nama_barang', 'Nama Barang', 'required');
        $this->form_validation->set_rules('sku', 'SKU', 'required|callback_check_sku[' . $id . ']');
        $this->form_validation->set_rules('id_kategori', 'Kategori', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('template/template', $data);
        } else {
            // Handle upload gambar
            $gambar = $data['barang']->gambar;
            if (!empty($_FILES['gambar']['name'])) {
                $config['upload_path'] = './uploads/barang/';
                $config['allowed_types'] = 'gif|jpg|png|jpeg';
                $config['max_size'] = 2048;
                $config['encrypt_name'] = TRUE;

                $this->load->library('upload', $config);

                if ($this->upload->do_upload('gambar')) {
                    // Hapus gambar lama
                    if ($data['barang']->gambar && file_exists('./uploads/barang/' . $data['barang']->gambar)) {
                        unlink('./uploads/barang/' . $data['barang']->gambar);
                    }

                    $upload_data = $this->upload->data();
                    $gambar = $upload_data['file_name'];
                }
            }

            $data_barang = array(
                'id_kategori' => $this->input->post('id_kategori'),
                'nama_barang' => $this->input->post('nama_barang'),
                'sku' => $this->input->post('sku'),
                'deskripsi' => $this->input->post('deskripsi'),
                'satuan' => $this->input->post('satuan'),
                'harga_jual' => $this->input->post('harga_jual'),
                'harga_beli_terakhir' => $this->input->post('harga_beli_terakhir'),
                'gambar' => $gambar,
                'aktif' => $this->input->post('aktif')
            );

            $this->barang_model->update($id, $data_barang);
            $this->session->set_flashdata('success', 'Barang berhasil diperbarui!');
            redirect('barang');
        }
    }

    public function hapus($id)
    {
        $barang = $this->barang_model->get_by_id($id);

        if (!$barang) {
            show_404();
        }

        // Hapus gambar
        if ($barang->gambar && file_exists('./uploads/barang/' . $barang->gambar)) {
            unlink('./uploads/barang/' . $barang->gambar);
        }

        $this->barang_model->delete($id);
        $this->session->set_flashdata('success', 'Barang berhasil dihapus!');
        redirect('barang');
    }

    public function check_sku($sku, $id)
    {
        $this->db->where('sku', $sku);
        $this->db->where('id_barang !=', $id);
        $this->db->where('id_perusahaan', $this->session->userdata('id_perusahaan'));
        $query = $this->db->get('barang');

        if ($query->num_rows() > 0) {
            $this->form_validation->set_message('check_sku', 'SKU sudah digunakan');
            return FALSE;
        } else {
            return TRUE;
        }
    }
}