<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Barang extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('setup/Barang_model');
        $this->load->model('setup/Kategori_model');
        $this->load->model('setup/Perusahaan_model');
        $this->load->model('Stok_gudang_model');
        $this->load->helper('form');
        $this->load->library('form_validation');
        $this->load->library('upload');

        // Cek akses menu
        $this->check_menu_access('setup/barang');
    }

    public function index()
    {
        $this->data['title'] = 'Manajemen Barang';
        $this->data['barang'] = $this->Barang_model->get_all();

        $this->render_view('setup/barang/index');
    }

    public function tambah()
    {
        $this->data['title'] = 'Tambah Barang';
        $this->data['kategori'] = $this->Kategori_model->get_by_perusahaan($this->session->userdata('id_perusahaan'));

        if ($this->session->userdata('id_role') == 1) {
            $this->data['perusahaan'] = $this->Perusahaan_model->get_active();
        }

        if ($this->input->post()) {
            $this->form_validation->set_rules('nama_barang', 'Nama Barang', 'required|trim|max_length[100]');
            $this->form_validation->set_rules('sku', 'SKU', 'required|trim|max_length[50]');
            $this->form_validation->set_rules('id_kategori', 'Kategori', 'required');
            $this->form_validation->set_rules('satuan', 'Satuan', 'required|trim|max_length[20]');
            $this->form_validation->set_rules('harga_jual', 'Harga Jual', 'required|numeric|greater_than_equal_to[0]');
            $this->form_validation->set_rules('deskripsi', 'Deskripsi', 'trim');

            // Jika Super Admin, validasi perusahaan
            if ($this->session->userdata('id_role') == 1) {
                $this->form_validation->set_rules('id_perusahaan', 'Perusahaan', 'required');
            }

            if ($this->form_validation->run() == TRUE) {
                $nama_barang = $this->input->post('nama_barang');
                $sku = $this->input->post('sku');
                $id_perusahaan = $this->session->userdata('id_role') == 1 ?
                    $this->input->post('id_perusahaan') :
                    $this->session->userdata('id_perusahaan');

                // Check unique SKU per company
                if (!$this->Barang_model->check_unique_sku($sku, $id_perusahaan)) {
                    $this->data['error'] = 'SKU sudah ada di perusahaan yang sama!';
                    redirect('setup/barang/tambah');
                }

                // Handle upload gambar
                $gambar = '';
                if (!empty($_FILES['gambar']['name'])) {
                    $config['upload_path'] = './uploads/barang/';
                    $config['allowed_types'] = 'jpg|jpeg|png|gif';
                    $config['max_size'] = 2048; // 2MB
                    $config['file_name'] = time() . '_' . $_FILES['gambar']['name'];

                    $this->upload->initialize($config);

                    if ($this->upload->do_upload('gambar')) {
                        $upload_data = $this->upload->data();
                        $gambar = $upload_data['file_name'];
                    } else {
                        $this->data['error'] = $this->upload->display_errors();
                        redirect('setup/barang/tambah');
                    }
                }

                $data = array(
                    'nama_barang' => $nama_barang,
                    'sku' => $sku,
                    'id_perusahaan' => $id_perusahaan,
                    'id_kategori' => $this->input->post('id_kategori'),
                    'satuan' => $this->input->post('satuan'),
                    'harga_jual' => $this->input->post('harga_jual'),
                    'harga_beli_terakhir' => $this->input->post('harga_beli_terakhir') ?: NULL,
                    'deskripsi' => $this->input->post('deskripsi'),
                    'gambar' => $gambar,
                    'status_aktif' => 1
                );

                if ($this->Barang_model->insert($data)) {
                    $this->data['success'] = 'Barang berhasil ditambahkan!';
                    redirect('setup/barang');
                } else {
                    $this->data['error'] = 'Gagal menambahkan barang!';
                    redirect('setup/barang/tambah');
                }
            }
        }

        $this->render_view('setup/barang/form');
    }

    public function edit($id_barang)
    {
        $this->data['title'] = 'Edit Barang';
        $this->data['barang'] = $this->Barang_model->get($id_barang);

        if (!$this->data['barang']) {
            show_404();
        }

        $this->data['kategori'] = $this->Kategori_model->get_by_perusahaan($this->data['barang']->id_perusahaan);

        if ($this->session->userdata('id_role') == 1) {
            $this->data['perusahaan'] = $this->Perusahaan_model->get_active();
        }

        if ($this->input->post()) {
            $this->form_validation->set_rules('nama_barang', 'Nama Barang', 'required|trim|max_length[100]');
            $this->form_validation->set_rules('sku', 'SKU', 'required|trim|max_length[50]');
            $this->form_validation->set_rules('id_kategori', 'Kategori', 'required');
            $this->form_validation->set_rules('satuan', 'Satuan', 'required|trim|max_length[20]');
            $this->form_validation->set_rules('harga_jual', 'Harga Jual', 'required|numeric|greater_than_equal_to[0]');
            $this->form_validation->set_rules('deskripsi', 'Deskripsi', 'trim');
            $this->form_validation->set_rules('status_aktif', 'Status', 'required|in_list[0,1]');

            // Jika Super Admin, validasi perusahaan
            if ($this->session->userdata('id_role') == 1) {
                $this->form_validation->set_rules('id_perusahaan', 'Perusahaan', 'required');
            }

            if ($this->form_validation->run() == TRUE) {
                $nama_barang = $this->input->post('nama_barang');
                $sku = $this->input->post('sku');
                $id_perusahaan = $this->session->userdata('id_role') == 1 ?
                    $this->input->post('id_perusahaan') :
                    $this->session->userdata('id_perusahaan');

                // Check unique SKU (excluding current record)
                if (!$this->Barang_model->check_unique_sku($sku, $id_perusahaan, $id_barang)) {
                    $this->data['error'] = 'SKU sudah ada di perusahaan yang sama!';
                    redirect('setup/barang/edit/' . $id_barang);
                }

                // Handle upload gambar
                $gambar = $this->data['barang']->gambar;
                if (!empty($_FILES['gambar']['name'])) {
                    $config['upload_path'] = './uploads/barang/';
                    $config['allowed_types'] = 'jpg|jpeg|png|gif';
                    $config['max_size'] = 2048; // 2MB
                    $config['file_name'] = time() . '_' . $_FILES['gambar']['name'];

                    $this->upload->initialize($config);

                    if ($this->upload->do_upload('gambar')) {
                        $upload_data = $this->upload->data();
                        $gambar = $upload_data['file_name'];

                        // Hapus gambar lama jika ada
                        if ($this->data['barang']->gambar && file_exists('./uploads/barang/' . $this->data['barang']->gambar)) {
                            unlink('./uploads/barang/' . $this->data['barang']->gambar);
                        }
                    } else {
                        $this->data['error'] = $this->upload->display_errors();
                        redirect('setup/barang/edit/' . $id_barang);
                    }
                }

                $data = array(
                    'nama_barang' => $nama_barang,
                    'sku' => $sku,
                    'id_perusahaan' => $id_perusahaan,
                    'id_kategori' => $this->input->post('id_kategori'),
                    'satuan' => $this->input->post('satuan'),
                    'harga_jual' => $this->input->post('harga_jual'),
                    'harga_beli_terakhir' => $this->input->post('harga_beli_terakhir') ?: NULL,
                    'deskripsi' => $this->input->post('deskripsi'),
                    'gambar' => $gambar,
                    'status_aktif' => $this->input->post('status_aktif')
                );

                if ($this->Barang_model->update($id_barang, $data)) {
                    $this->data['success'] = 'Barang berhasil diperbarui!';
                    redirect('setup/barang');
                } else {
                    $this->data['error'] = 'Gagal memperbarui barang!';
                    redirect('setup/barang/edit/' . $id_barang);
                }
            }
        }

        $this->render_view('setup/barang/form');
    }

    public function nonaktif($id)
    {
        if ($this->Barang_model->update($id, array('status_aktif' => 0))) {
            $this->data['success'] = 'Barang berhasil dinonaktifkan';
        } else {
            $this->data['error'] = 'Gagal menonaktifkan barang';
        }
        redirect('setup/barang');
    }

    public function aktif($id)
    {
        if ($this->Barang_model->update($id, array('status_aktif' => 1))) {
            $this->data['success'] = 'Barang berhasil diaktifkan kembali';
        } else {
            $this->data['error'] = 'Gagal mengaktifkan barang';
        }
        redirect('setup/barang');
    }

    public function hapus($id_barang)
    {
        $barang = $this->Barang_model->get($id_barang);

        if (!$barang) {
            show_404();
        }

        // Check if product has related transactions
        $this->load->model('Stok_gudang_model');
        $stok_count = $this->Stok_gudang_model->get_total_stok($id_barang);

        if ($stok_count > 0) {
            $this->data['error'] = 'Barang tidak dapat dihapus karena masih memiliki stok terkait!';
            redirect('setup/barang');
        }

        // Hapus gambar jika ada
        if ($barang->gambar && file_exists('./uploads/barang/' . $barang->gambar)) {
            unlink('./uploads/barang/' . $barang->gambar);
        }

        if ($this->Barang_model->delete($id_barang)) {
            $this->data['success'] = 'Barang berhasil dihapus!';
        } else {
            $this->data['error'] = 'Gagal menghapus barang!';
        }

        redirect('setup/barang');
    }

    public function detail($id_barang)
    {
        $this->data['title'] = 'Detail Barang';
        $this->data['barang'] = $this->Barang_model->get($id_barang);

        if (!$this->data['barang']) {
            show_404();
        }

        // Get related data
        $this->data['stok'] = $this->Barang_model->get_stok_by_barang($id_barang);
        $this->data['total_stok'] = $this->Barang_model->get_total_stok($id_barang);

        $this->render_view('setup/barang/detail');
    }

    public function get_kategori_by_perusahaan()
    {
        // Check if this is an AJAX request
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        // Validate CSRF token
        if (!$this->security->csrf_verify()) {
            echo json_encode([]);
            return;
        }

        $id_perusahaan = $this->input->post('id_perusahaan');

        if (!$id_perusahaan) {
            echo json_encode([]);
            return;
        }

        $kategori = $this->Kategori_model->get_by_perusahaan_id($id_perusahaan);
        echo json_encode($kategori);
    }
}