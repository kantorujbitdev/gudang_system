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

    public function stok($id_barang)
    {
        $this->data['title'] = 'Manajemen Stok Barang';
        $this->data['barang'] = $this->Barang_model->get($id_barang);

        if (!$this->data['barang']) {
            show_404();
        }

        $this->load->model('setup/Gudang_model');
        $this->data['gudang'] = $this->Gudang_model->get_by_perusahaan($this->data['barang']->id_perusahaan);
        $this->data['stok'] = $this->Barang_model->get_stok_by_barang($id_barang);
        $data['extra_js'] = 'setup/barang/script';
        $this->render_view('setup/barang/stok', $data);
    }

    public function tambah_stok()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $id_barang = $this->input->post('id_barang');
        $id_gudang = $this->input->post('id_gudang');
        $jumlah = $this->input->post('jumlah');

        // Validasi
        if (!$id_barang || !$id_gudang || !$jumlah || $jumlah <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'Data tidak valid']);
            return;
        }

        // Cek barang
        $barang = $this->Barang_model->get($id_barang);
        if (!$barang) {
            echo json_encode(['status' => 'error', 'message' => 'Barang tidak ditemukan']);
            return;
        }

        // Cek gudang
        $this->load->model('Gudang_model');
        $gudang = $this->Gudang_model->get($id_gudang);
        if (!$gudang) {
            echo json_encode(['status' => 'error', 'message' => 'Gudang tidak ditemukan']);
            return;
        }

        // Cek apakah stok sudah ada
        $this->load->model('Stok_gudang_model');
        $stok_exists = $this->Stok_gudang_model->get_by_barang_gudang($id_barang, $id_gudang);

        if ($stok_exists) {
            // Update stok yang sudah ada
            $data_update = [
                'jumlah' => $stok_exists->jumlah + $jumlah
            ];

            if ($this->Stok_gudang_model->update($stok_exists->id_stok, $data_update)) {
                // Log stok
                $this->load->model('Log_stok_model');
                $log_data = [
                    'id_barang' => $id_barang,
                    'id_user' => $this->session->userdata('id_user'),
                    'id_perusahaan' => $barang->id_perusahaan,
                    'id_gudang' => $id_gudang,
                    'jenis' => 'masuk',
                    'jumlah' => $jumlah,
                    'sisa_stok' => $stok_exists->jumlah + $jumlah,
                    'keterangan' => 'Penambahan stok manual',
                    'id_referensi' => null,
                    'tipe_referensi' => 'penyesuaian'
                ];
                $this->Log_stok_model->insert($log_data);

                echo json_encode(['status' => 'success', 'message' => 'Stok berhasil ditambahkan']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Gagal menambahkan stok']);
            }
        } else {
            // Buat stok baru
            $data_insert = [
                'id_perusahaan' => $barang->id_perusahaan,
                'id_barang' => $id_barang,
                'id_gudang' => $id_gudang,
                'jumlah' => $jumlah,
                'reserved' => 0
            ];

            if ($this->Stok_gudang_model->insert($data_insert)) {
                // Log stok
                $this->load->model('Log_stok_model');
                $log_data = [
                    'id_barang' => $id_barang,
                    'id_user' => $this->session->userdata('id_user'),
                    'id_perusahaan' => $barang->id_perusahaan,
                    'id_gudang' => $id_gudang,
                    'jenis' => 'masuk',
                    'jumlah' => $jumlah,
                    'sisa_stok' => $jumlah,
                    'keterangan' => 'Penambahan stok manual',
                    'id_referensi' => null,
                    'tipe_referensi' => 'penyesuaian'
                ];
                $this->Log_stok_model->insert($log_data);

                echo json_encode(['status' => 'success', 'message' => 'Stok berhasil ditambahkan']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Gagal menambahkan stok']);
            }
        }
    }

    public function kurangi_stok()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $id_barang = $this->input->post('id_barang');
        $id_gudang = $this->input->post('id_gudang');
        $jumlah = $this->input->post('jumlah');

        // Validasi
        if (!$id_barang || !$id_gudang || !$jumlah || $jumlah <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'Data tidak valid']);
            return;
        }

        // Cek barang
        $barang = $this->Barang_model->get($id_barang);
        if (!$barang) {
            echo json_encode(['status' => 'error', 'message' => 'Barang tidak ditemukan']);
            return;
        }

        // Cek gudang
        $this->load->model('Gudang_model');
        $gudang = $this->Gudang_model->get($id_gudang);
        if (!$gudang) {
            echo json_encode(['status' => 'error', 'message' => 'Gudang tidak ditemukan']);
            return;
        }

        // Cek stok
        $this->load->model('Stok_gudang_model');
        $stok_exists = $this->Stok_gudang_model->get_by_barang_gudang($id_barang, $id_gudang);

        if (!$stok_exists) {
            echo json_encode(['status' => 'error', 'message' => 'Stok tidak ditemukan']);
            return;
        }

        // Cek apakah stok mencukupi
        if ($stok_exists->jumlah < $jumlah) {
            echo json_encode(['status' => 'error', 'message' => 'Stok tidak mencukupi']);
            return;
        }

        // Update stok
        $data_update = [
            'jumlah' => $stok_exists->jumlah - $jumlah
        ];

        if ($this->Stok_gudang_model->update($stok_exists->id_stok, $data_update)) {
            // Log stok
            $this->load->model('Log_stok_model');
            $log_data = [
                'id_barang' => $id_barang,
                'id_user' => $this->session->userdata('id_user'),
                'id_perusahaan' => $barang->id_perusahaan,
                'id_gudang' => $id_gudang,
                'jenis' => 'keluar',
                'jumlah' => $jumlah,
                'sisa_stok' => $stok_exists->jumlah - $jumlah,
                'keterangan' => 'Pengurangan stok manual',
                'id_referensi' => null,
                'tipe_referensi' => 'penyesuaian'
            ];
            $this->Log_stok_model->insert($log_data);

            echo json_encode(['status' => 'success', 'message' => 'Stok berhasil dikurangi']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal mengurangi stok']);
        }
    }

    public function index()
    {
        $this->data['title'] = 'Manajemen Barang';
        $this->data['barang'] = $this->Barang_model->get_barang_with_stok();

        $this->render_view('setup/barang/index');
    }

    public function tambah()
    {
        $this->data['title'] = 'Tambah Barang';

        if ($this->session->userdata('id_role') == 1) {
            $this->data['perusahaan'] = $this->Perusahaan_model->get_active();
            if (!empty($this->data['perusahaan'])) {
                $first_perusahaan = $this->data['perusahaan'][0];
                $this->data['kategori'] = $this->Kategori_model->get_by_perusahaan($first_perusahaan->id_perusahaan);
                $this->data['selected_perusahaan'] = $first_perusahaan->id_perusahaan;
            } else {
                $this->data['kategori'] = [];
                $this->data['selected_perusahaan'] = '';
            }
        } else {
            $this->data['kategori'] = $this->Kategori_model->get_by_perusahaan($this->session->userdata('id_perusahaan'));
        }

        if ($this->input->post()) {
            $this->_set_validation_rules();

            if ($this->form_validation->run() == TRUE) {
                $nama_barang = $this->input->post('nama_barang');
                $sku = $this->input->post('sku');
                $id_perusahaan = $this->session->userdata('id_role') == 1 ?
                    $this->input->post('id_perusahaan') :
                    $this->session->userdata('id_perusahaan');

                // Cek SKU unik
                if (!$this->Barang_model->check_unique_sku($sku, $id_perusahaan)) {
                    $this->session->set_flashdata('error', 'SKU sudah ada di perusahaan yang sama!');
                    redirect('setup/barang/tambah');
                }

                // Upload gambar
                $gambar = $this->_upload_gambar();

                $data = [
                    'nama_barang' => $nama_barang,
                    'sku' => $sku,
                    'id_perusahaan' => $id_perusahaan,
                    'id_kategori' => $this->input->post('id_kategori'),
                    'satuan' => $this->input->post('satuan'),
                    'deskripsi' => $this->input->post('deskripsi'),
                    'gambar' => $gambar,
                    'status_aktif' => 1
                ];

                if ($this->Barang_model->insert($data)) {
                    $this->session->set_flashdata('success', 'Barang berhasil ditambahkan!');
                    redirect('setup/barang');
                } else {
                    $this->session->set_flashdata('error', 'Gagal menambahkan barang!');
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
            $this->_set_validation_rules(true);

            if ($this->form_validation->run() == TRUE) {
                $nama_barang = $this->input->post('nama_barang');
                $sku = $this->input->post('sku');
                $id_perusahaan = $this->session->userdata('id_role') == 1 ?
                    $this->input->post('id_perusahaan') :
                    $this->session->userdata('id_perusahaan');

                if (!$this->Barang_model->check_unique_sku($sku, $id_perusahaan, $id_barang)) {
                    $this->session->set_flashdata('error', 'SKU sudah ada di perusahaan yang sama!');
                    redirect('setup/barang/edit/' . $id_barang);
                }

                // Upload gambar
                $gambar = $this->_upload_gambar($this->data['barang']->gambar);

                $data = [
                    'nama_barang' => $nama_barang,
                    'sku' => $sku,
                    'id_perusahaan' => $id_perusahaan,
                    'id_kategori' => $this->input->post('id_kategori'),
                    'satuan' => $this->input->post('satuan'),
                    'deskripsi' => $this->input->post('deskripsi'),
                    'gambar' => $gambar,
                    'status_aktif' => $this->input->post('status_aktif')
                ];

                if ($this->Barang_model->update($id_barang, $data)) {
                    $this->session->set_flashdata('success', 'Barang berhasil diperbarui!');
                    redirect('setup/barang');
                } else {
                    $this->session->set_flashdata('error', 'Gagal memperbarui barang!');
                    redirect('setup/barang/edit/' . $id_barang);
                }
            }
        }

        $this->render_view('setup/barang/form');
    }

    public function nonaktif($id)
    {
        if ($this->Barang_model->update($id, ['status_aktif' => 0])) {
            $this->session->set_flashdata('success', 'Barang berhasil dinonaktifkan');
        } else {
            $this->session->set_flashdata('error', 'Gagal menonaktifkan barang');
        }
        redirect('setup/barang');
    }

    public function aktif($id)
    {
        if ($this->Barang_model->update($id, ['status_aktif' => 1])) {
            $this->session->set_flashdata('success', 'Barang berhasil diaktifkan kembali');
        } else {
            $this->session->set_flashdata('error', 'Gagal mengaktifkan barang');
        }
        redirect('setup/barang');
    }

    public function hapus($id_barang)
    {
        $barang = $this->Barang_model->get($id_barang);

        if (!$barang) {
            show_404();
        }

        $stok_count = $this->Stok_gudang_model->get_total_stok($id_barang);

        if ($stok_count > 0) {
            $this->session->set_flashdata('error', 'Barang tidak dapat dihapus karena masih memiliki stok terkait!');
            redirect('setup/barang');
        }

        if ($barang->gambar && file_exists('./uploads/barang/' . $barang->gambar)) {
            unlink('./uploads/barang/' . $barang->gambar);
        }

        if ($this->Barang_model->delete($id_barang)) {
            $this->session->set_flashdata('success', 'Barang berhasil dihapus!');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus barang!');
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

        $this->data['stok'] = $this->Barang_model->get_stok_by_barang($id_barang);
        $this->data['total_stok'] = $this->Barang_model->get_total_stok($id_barang);

        $this->render_view('setup/barang/detail');
    }

    public function get_kategori_by_perusahaan()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $id_perusahaan = $this->input->post('id_perusahaan');

        if (!$id_perusahaan) {
            echo json_encode([]);
            return;
        }

        $kategori = $this->Kategori_model->get_by_perusahaan_id($id_perusahaan);
        echo json_encode($kategori);
    }

    /** ============================
     *  Private Helpers
     *  ============================
     */
    private function _set_validation_rules($is_edit = false)
    {
        $this->form_validation->set_rules('nama_barang', 'Nama Barang', 'required|trim|max_length[100]');
        $this->form_validation->set_rules('sku', 'SKU', 'required|trim|max_length[50]');
        $this->form_validation->set_rules('id_kategori', 'Kategori', 'required');
        $this->form_validation->set_rules('satuan', 'Satuan', 'required|trim|max_length[20]');
        $this->form_validation->set_rules('deskripsi', 'Deskripsi', 'trim');

        if ($is_edit) {
            $this->form_validation->set_rules('status_aktif', 'Status', 'required|in_list[0,1]');
        }

        if ($this->session->userdata('id_role') == 1) {
            $this->form_validation->set_rules('id_perusahaan', 'Perusahaan', 'required');
        }
    }

    private function _upload_gambar($old_file = null)
    {
        $gambar = $old_file;

        if (!empty($_FILES['gambar']['name'])) {
            $config['upload_path'] = './uploads/barang/';
            $config['allowed_types'] = 'jpg|jpeg|png|gif';
            $config['max_size'] = 2048; // 2MB
            $config['file_name'] = time() . '_' . $_FILES['gambar']['name'];

            $this->upload->initialize($config);

            if ($this->upload->do_upload('gambar')) {
                $upload_data = $this->upload->data();
                $gambar = $upload_data['file_name'];

                if ($old_file && file_exists('./uploads/barang/' . $old_file)) {
                    unlink('./uploads/barang/' . $old_file);
                }
            } else {
                $this->session->set_flashdata('error', $this->upload->display_errors());
                redirect(current_url());
            }
        }

        return $gambar;
    }
}
