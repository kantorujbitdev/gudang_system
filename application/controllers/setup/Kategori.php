<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Kategori extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('setup/Kategori_model');
        $this->load->model('setup/Perusahaan_model');
        $this->load->helper('form');
        $this->load->library('form_validation');

        // Cek akses menu
        $this->check_menu_access('setup/kategori');
    }

    public function index()
    {
        $this->data['title'] = 'Manajemen Kategori';
        $this->data['kategori'] = $this->Kategori_model->get_with_perusahaan();

        $this->render_view('setup/kategori/index');
    }

    public function tambah()
    {
        $this->data['title'] = 'Tambah Kategori';
        $this->data['perusahaan'] = $this->Perusahaan_model->get_active();

        if ($this->input->post()) {
            $this->form_validation->set_rules('nama_kategori', 'Nama Kategori', 'required|trim|max_length[50]');
            $this->form_validation->set_rules('id_perusahaan', 'Perusahaan', 'required');
            $this->form_validation->set_rules('deskripsi', 'Deskripsi', 'trim');

            if ($this->form_validation->run() == TRUE) {
                $nama_kategori = $this->input->post('nama_kategori');
                $id_perusahaan = $this->input->post('id_perusahaan');

                // Check unique name per company
                if (!$this->Kategori_model->check_unique_name($nama_kategori, $id_perusahaan)) {
                    $this->data['error'] = 'Nama kategori sudah ada di perusahaan yang sama!';
                    $this->render_view('setup/kategori/form');
                    return;
                }

                $data = array(
                    'nama_kategori' => $nama_kategori,
                    'id_perusahaan' => $id_perusahaan,
                    'deskripsi' => $this->input->post('deskripsi'),
                    'status_aktif' => 1
                );

                if ($this->Kategori_model->insert($data)) {
                    $this->data['success'] = 'Kategori berhasil ditambahkan!';
                    redirect('setup/kategori');
                } else {
                    $this->data['error'] = 'Gagal menambahkan kategori!';
                }
            }
        }

        $this->render_view('setup/kategori/form');
    }

    public function nonaktif($id)
    {
        if ($this->Kategori_model->update_status($id, 0)) {
            $this->data['success'] = 'Kategori berhasil dinonaktifkan';
        } else {
            $this->data['error'] = 'Gagal menonaktifkan kategori';
        }
        redirect('setup/kategori');
    }

    public function aktif($id)
    {
        if ($this->Kategori_model->update_status($id, 1)) {
            $this->data['success'] = 'Kategori berhasil diaktifkan kembali';
        } else {
            $this->data['error'] = 'Gagal mengaktifkan kategori';
        }
        redirect('setup/kategori');
    }

    public function edit($id_kategori)
    {
        $this->data['title'] = 'Edit Kategori Barang';
        $this->data['kategori'] = $this->Kategori_model->get($id_kategori);

        if (!$this->data['kategori']) {
            show_404();
        }

        // Jika Super Admin, tampilkan pilihan perusahaan
        if ($this->session->userdata('id_role') == 1) {
            $this->load->model('setup/Perusahaan_model');
            $this->data['perusahaan'] = $this->Perusahaan_model->get_all();
        }

        if ($this->input->post()) {
            $this->form_validation->set_rules('nama_kategori', 'Nama Kategori', 'required|trim|max_length[50]');
            $this->form_validation->set_rules('deskripsi', 'Deskripsi', 'trim|max_length[255]');
            $this->form_validation->set_rules('status_aktif', 'Status', 'required|in_list[0,1]');

            // Jika Super Admin, validasi perusahaan
            if ($this->session->userdata('id_role') == 1) {
                $this->form_validation->set_rules('id_perusahaan', 'Perusahaan', 'required');
            }

            if ($this->form_validation->run() == TRUE) {
                $nama_kategori = $this->input->post('nama_kategori');
                $id_perusahaan = $this->session->userdata('id_role') == 1 ?
                    $this->input->post('id_perusahaan') :
                    $this->session->userdata('id_perusahaan');

                // Check unique name (excluding current record)
                if (!$this->Kategori_model->check_unique_name($nama_kategori, $id_perusahaan, $id_kategori)) {
                    $this->data['error'] = 'Nama kategori sudah ada untuk perusahaan ini!';
                    $this->render_view('setup/kategori/form');
                    return;
                }

                $data = array(
                    'nama_kategori' => $nama_kategori,
                    'deskripsi' => $this->input->post('deskripsi'),
                    'status_aktif' => $this->input->post('status_aktif')
                );

                // Jika Super Admin, update perusahaan
                if ($this->session->userdata('id_role') == 1) {
                    $data['id_perusahaan'] = $id_perusahaan;
                }

                if ($this->Kategori_model->update($id_kategori, $data)) {
                    $this->data['success'] = 'Kategori barang berhasil diperbarui!';
                    redirect('setup/kategori');
                } else {
                    $this->data['error'] = 'Gagal memperbarui kategori barang!';
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

        // Check if category has related products
        $this->db->where('id_kategori', $id_kategori);
        $this->db->where('deleted_at IS NULL');
        $product_count = $this->db->count_all_results('barang');

        if ($product_count > 0) {
            $this->data['error'] = 'Kategori tidak dapat dihapus karena masih memiliki produk terkait!';
            redirect('setup/kategori');
        }

        if ($this->Kategori_model->delete($id_kategori)) {
            $this->data['success'] = 'Kategori barang berhasil dihapus!';
        } else {
            $this->data['error'] = 'Gagal menghapus kategori barang!';
        }

        redirect('setup/kategori');
    }

    public function detail($id_kategori)
    {
        $this->data['title'] = 'Detail Kategori Barang';
        $this->data['kategori'] = $this->Kategori_model->get($id_kategori);

        if (!$this->data['kategori']) {
            show_404();
        }

        // Get related products
        $this->data['barang'] = $this->Kategori_model->get_barang_by_kategori($id_kategori);

        $this->render_view('setup/kategori/detail');
    }
}