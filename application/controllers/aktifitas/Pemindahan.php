<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pemindahan extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('aktifitas/Pemindahan_model', 'pemindahan');
        $this->load->model('setup/Barang_model', 'barang');
        $this->load->model('setup/Gudang_model', 'gudang');
        $this->load->model('setup/Pelanggan_model', 'pelanggan');
        $this->load->model('setup/Konsumen_model', 'konsumen');
        $this->load->model('setup/Toko_konsumen_model', 'toko_konsumen');
        $this->load->helper('form');
        $this->load->library('form_validation');

        // Cek akses menu
        $this->check_menu_access('aktifitas/pemindahan');
    }

    public function get_data_by_perusahaan()
    {
        header('Content-Type: application/json');

        $id_perusahaan = $this->input->post('id_perusahaan');

        if ($id_perusahaan) {
            $data = $this->pemindahan->get_data_by_perusahaan($id_perusahaan);

            // Generate new CSRF token
            $data['csrf_token'] = $this->security->get_csrf_hash();
            $data['csrf_name'] = $this->security->get_csrf_token_name();

            echo json_encode($data);
        } else {
            echo json_encode([]);
        }
    }

    public function get_stok_barang()
    {
        header('Content-Type: application/json');

        $id_gudang = $this->input->post('id_gudang');
        $id_barang = $this->input->post('id_barang');

        if ($id_gudang && $id_barang) {
            $stok = $this->pemindahan->get_stok_barang($id_gudang, $id_barang);

            echo json_encode([
                'stok' => $stok ? $stok->jumlah : 0,
                'reserved' => $stok ? $stok->reserved : 0,
                'tersedia' => $stok ? ($stok->jumlah - $stok->reserved) : 0
            ]);
        } else {
            echo json_encode([
                'stok' => 0,
                'reserved' => 0,
                'tersedia' => 0
            ]);
        }
    }

    public function get_barang_by_gudang()
    {
        header('Content-Type: application/json');

        $id_gudang = $this->input->post('id_gudang');

        if ($id_gudang) {
            $barang = $this->pemindahan->get_barang_by_gudang_with_stock($id_gudang);
            echo json_encode($barang);
        } else {
            echo json_encode([]);
        }
    }

    public function get_alamat_pelanggan()
    {
        header('Content-Type: application/json');

        $id_pelanggan = $this->input->post('id_pelanggan');
        $alamat = $this->pemindahan->get_alamat_pelanggan($id_pelanggan);

        if ($alamat) {
            echo json_encode([
                'status' => 'success',
                'alamat' => $alamat->alamat,
                'telepon' => $alamat->telepon,
                'email' => $alamat->email
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Alamat tidak ditemukan'
            ]);
        }
    }

    public function refresh_csrf()
    {
        header('Content-Type: application/json');
        echo json_encode([
            'csrf_token' => $this->security->get_csrf_hash(),
            'csrf_name' => $this->security->get_csrf_token_name()
        ]);
    }

    public function index()
    {
        $this->data['title'] = 'Pemindahan Barang';
        $this->data['pemindahan'] = $this->pemindahan->get_all();
        $this->data['can_create'] = $this->check_permission('aktifitas/pemindahan', 'create');
        $this->data['can_edit'] = $this->check_permission('aktifitas/pemindahan', 'edit');
        $this->data['can_delete'] = $this->check_permission('aktifitas/pemindahan', 'delete');

        $this->render_view('aktifitas/pemindahan/index');
    }


    public function tambah()
    {
        $data['extra_js'] = 'aktifitas/pemindahan/pemindahan_script';
        if (!$this->check_permission('aktifitas/pemindahan', 'create')) {
            $this->session->set_flashdata('error', 'Anda tidak memiliki izin untuk membuat pemindahan barang!');
            return redirect('aktifitas/pemindahan');
        }

        $this->data['title'] = 'Tambah Pemindahan Barang';

        $user_role = $this->session->userdata('id_role');
        $id_perusahaan = $this->session->userdata('id_perusahaan');
        $id_user = $this->session->userdata('id_user');

        // Jika Super Admin, tampilkan pilihan perusahaan
        if ($user_role == 1) {
            $this->load->model('setup/Perusahaan_model', 'perusahaan');
            $this->data['perusahaan'] = $this->perusahaan->get_all();
        }

        // Filter gudang berdasarkan perusahaan
        if ($user_role == 1) {
            // Untuk Super Admin, gudang akan diisi via AJAX setelah memilih perusahaan
            $this->data['gudang'] = [];
        } else {
            $this->data['gudang'] = $this->gudang->get_by_perusahaan($id_perusahaan);
        }

        // Filter pelanggan berdasarkan role
        if ($user_role == 3) { // Sales
            $this->data['pelanggan'] = $this->pemindahan->get_pelanggan_by_sales($id_user);
        } else {
            $this->data['pelanggan'] = $this->pelanggan->get_by_perusahaan($id_perusahaan);
        }

        // Get toko konsumen
        $this->data['toko_konsumen'] = $this->toko_konsumen->get_all();

        // Filter barang berdasarkan perusahaan
        if ($user_role == 1) {
            // Untuk Super Admin, barang akan diisi via AJAX setelah memilih perusahaan
            $this->data['barang'] = [];
        } else {
            $this->data['barang'] = $this->barang->get_by_perusahaan($id_perusahaan);
        }

        // Set default tipe tujuan untuk Sales
        if ($user_role == 3) {
            $this->data['tipe_tujuan_default'] = 'konsumen';
        }

        $this->form_validation->set_rules('id_gudang_asal', 'Gudang Asal', 'required');

        // Untuk Sales, tipe tujuan otomatis konsumen
        if ($user_role != 3) {
            $this->form_validation->set_rules('tipe_tujuan', 'Tipe Tujuan', 'required');
        }

        if ($this->form_validation->run() == FALSE) {
            return $this->render_view('aktifitas/pemindahan/form', $data);
        }

        // Generate nomor transaksi berdasarkan tipe tujuan
        $tipe_tujuan = ($user_role == 3) ? 'konsumen' : $this->input->post('tipe_tujuan');
        $no_transaksi = $this->generate_no_transaksi($tipe_tujuan);

        // Tentukan perusahaan
        if ($user_role == 1) {
            $id_perusahaan = $this->input->post('id_perusahaan');
        }

        $data_insert = [
            'no_transaksi' => $no_transaksi,
            'id_perusahaan' => $id_perusahaan,
            'id_user' => $id_user,
            'id_gudang_asal' => $this->input->post('id_gudang_asal'),
            'tanggal_pemindahan' => date('Y-m-d H:i:s'), // Waktu real
            'keterangan' => $this->input->post('keterangan'),
            'status' => 'Draft'
        ];

        $data_insert['tipe_tujuan'] = $tipe_tujuan;

        // Validasi gudang asal dan tujuan
        if ($tipe_tujuan == 'gudang') {
            $id_gudang_asal = $this->input->post('id_gudang_asal');
            $id_gudang_tujuan = $this->input->post('id_gudang_tujuan');

            if ($id_gudang_asal == $id_gudang_tujuan) {
                $this->session->set_flashdata('error', 'Gudang asal dan tujuan tidak boleh sama!');
                return redirect('aktifitas/pemindahan/tambah');
            }

            // Validasi gudang tujuan harus di perusahaan yang sama
            $gudang_asal = $this->gudang->get($id_gudang_asal);
            $gudang_tujuan = $this->gudang->get($id_gudang_tujuan);

            if ($gudang_asal->id_perusahaan != $gudang_tujuan->id_perusahaan) {
                $this->session->set_flashdata('error', 'Gudang tujuan harus di perusahaan yang sama!');
                return redirect('aktifitas/pemindahan/tambah');
            }

            $data_insert['id_gudang_tujuan'] = $id_gudang_tujuan;
        } elseif ($tipe_tujuan == 'pelanggan') {
            $data_insert['id_pelanggan'] = $this->input->post('id_pelanggan');
        } elseif ($tipe_tujuan == 'konsumen') {
            // Untuk konsumen, data akan diproses per baris di detail
            $data_insert['id_konsumen'] = null; // Akan diisi nanti
        }

        $id_pemindahan = $this->pemindahan->insert($data_insert);

        if ($id_pemindahan) {
            // Simpan detail barang
            $barang_dipindahkan = json_decode($this->input->post('barang_dipindahkan'));

            if ($barang_dipindahkan) {
                foreach ($barang_dipindahkan as $barang) {
                    // Validasi stok
                    $stok = $this->pemindahan->get_stok_barang($this->input->post('id_gudang_asal'), $barang->id_barang);
                    $stok_tersedia = $stok ? ($stok->jumlah - $stok->reserved) : 0;

                    if ($barang->jumlah > $stok_tersedia) {
                        $this->session->set_flashdata('error', 'Stok barang tidak mencukupi!');
                        return redirect('aktifitas/pemindahan/tambah');
                    }

                    // Jika tipe tujuan konsumen, simpan data konsumen
                    $id_konsumen = null;
                    if ($tipe_tujuan == 'konsumen') {
                        // Buat konsumen baru
                        $data_konsumen = [
                            'nama_konsumen' => $barang->nama_konsumen,
                            'id_toko_konsumen' => $barang->id_toko_konsumen,
                            'alamat_konsumen' => $barang->alamat_konsumen,
                            'id_perusahaan' => $id_perusahaan
                        ];

                        $id_konsumen = $this->konsumen->insert($data_konsumen);

                        // Update pemindahan dengan id_konsumen (hanya untuk baris pertama)
                        if ($data_insert['id_konsumen'] === null) {
                            $data_insert['id_konsumen'] = $id_konsumen;
                            $this->pemindahan->update($id_pemindahan, $data_insert);
                        }
                    }

                    $this->pemindahan->insert_detail([
                        'id_pemindahan' => $id_pemindahan,
                        'id_barang' => $barang->id_barang,
                        'id_gudang' => $this->input->post('id_gudang_asal'),
                        'jumlah' => $barang->jumlah,
                        'id_konsumen' => $id_konsumen // Tambahkan id_konsumen di detail
                    ]);

                    // Tambah reserved saat draft
                    $this->tambah_reserved($this->input->post('id_gudang_asal'), $barang->id_barang, $barang->jumlah);
                }
            }

            // Log status transaksi
            $this->log_status_transaksi($id_pemindahan, 'pemindahan_barang', 'Draft');

            $this->session->set_flashdata('success', 'Pemindahan barang berhasil dibuat dengan nomor: ' . $no_transaksi);
            return redirect('aktifitas/pemindahan');
        }

        $this->session->set_flashdata('error', 'Gagal membuat pemindahan barang!');
        return redirect('aktifitas/pemindahan/tambah');
    }

    public function edit($id_pemindahan)
    {
        $data['extra_js'] = 'aktifitas/pemindahan/pemindahan_script';

        if (!$this->check_permission('aktifitas/pemindahan', 'edit')) {
            $this->session->set_flashdata('error', 'Anda tidak memiliki izin untuk mengubah pemindahan barang!');
            return redirect('aktifitas/pemindahan');
        }

        $this->data['title'] = 'Edit Pemindahan Barang';
        $this->data['pemindahan'] = $this->pemindahan->get($id_pemindahan);
        $this->data['detail'] = $this->pemindahan->get_detail($id_pemindahan);

        $id_perusahaan = $this->session->userdata('id_perusahaan');
        $user_role = $this->session->userdata('id_role');
        $id_user = $this->session->userdata('id_user');

        // Jika Super Admin, tampilkan pilihan perusahaan
        if ($user_role == 1) {
            $this->load->model('setup/Perusahaan_model', 'perusahaan');
            $this->data['perusahaan'] = $this->perusahaan->get_all();
        }

        // Filter gudang berdasarkan perusahaan
        if ($user_role == 1) {
            // Untuk Super Admin, gudang akan diisi via AJAX setelah memilih perusahaan
            $this->data['gudang'] = [];
        } else {
            $this->data['gudang'] = $this->gudang->get_by_perusahaan($id_perusahaan);
        }

        // Filter pelanggan berdasarkan role
        if ($user_role == 3) { // Sales
            $this->data['pelanggan'] = $this->pemindahan->get_pelanggan_by_sales($id_user);
        } else {
            $this->data['pelanggan'] = $this->pelanggan->get_by_perusahaan($id_perusahaan);
        }

        // Get toko konsumen
        $this->data['toko_konsumen'] = $this->toko_konsumen->get_all();

        // Filter barang berdasarkan perusahaan
        if ($user_role == 1) {
            // Untuk Super Admin, barang akan diisi via AJAX setelah memilih perusahaan
            $this->data['barang'] = [];
        } else {
            $this->data['barang'] = $this->barang->get_by_perusahaan($id_perusahaan);
        }

        if (!$this->data['pemindahan']) {
            show_404();
        }

        if ($this->data['pemindahan']->status != 'Draft') {
            $this->session->set_flashdata('error', 'Pemindahan dengan status ' . $this->data['pemindahan']->status . ' tidak dapat diubah!');
            return redirect('aktifitas/pemindahan');
        }

        $this->form_validation->set_rules('id_gudang_asal', 'Gudang Asal', 'required');
        $this->form_validation->set_rules('tipe_tujuan', 'Tipe Tujuan', 'required');

        if ($this->form_validation->run() == FALSE) {
            return $this->render_view('aktifitas/pemindahan/form', $data);
        }

        $data_update = [
            'id_gudang_asal' => $this->input->post('id_gudang_asal'),
            'keterangan' => $this->input->post('keterangan')
        ];

        $tipe_tujuan = $this->input->post('tipe_tujuan');
        $data_update['tipe_tujuan'] = $tipe_tujuan;

        // Jika tipe tujuan berubah, generate nomor transaksi baru
        if ($tipe_tujuan != $this->data['pemindahan']->tipe_tujuan) {
            $no_transaksi = $this->generate_no_transaksi($tipe_tujuan);
            $data_update['no_transaksi'] = $no_transaksi;
        }

        // Validasi gudang asal dan tujuan
        if ($tipe_tujuan == 'gudang') {
            $id_gudang_asal = $this->input->post('id_gudang_asal');
            $id_gudang_tujuan = $this->input->post('id_gudang_tujuan');

            if ($id_gudang_asal == $id_gudang_tujuan) {
                $this->session->set_flashdata('error', 'Gudang asal dan tujuan tidak boleh sama!');
                return redirect('aktifitas/pemindahan/edit/' . $id_pemindahan);
            }

            // Validasi gudang tujuan harus di perusahaan yang sama
            $gudang_asal = $this->gudang->get($id_gudang_asal);
            $gudang_tujuan = $this->gudang->get($id_gudang_tujuan);

            if ($gudang_asal->id_perusahaan != $gudang_tujuan->id_perusahaan) {
                $this->session->set_flashdata('error', 'Gudang tujuan harus di perusahaan yang sama!');
                return redirect('aktifitas/pemindahan/edit/' . $id_pemindahan);
            }

            $data_update['id_gudang_tujuan'] = $id_gudang_tujuan;
            $data_update['id_pelanggan'] = NULL;
            $data_update['id_konsumen'] = NULL;
        } elseif ($tipe_tujuan == 'pelanggan') {
            $data_update['id_pelanggan'] = $this->input->post('id_pelanggan');
            $data_update['id_gudang_tujuan'] = NULL;
            $data_update['id_konsumen'] = NULL;
        } elseif ($tipe_tujuan == 'konsumen') {
            // Update data konsumen jika ada
            if ($this->data['pemindahan']->id_konsumen) {
                $data_konsumen = [
                    'nama_konsumen' => $this->input->post('nama_konsumen'),
                    'id_toko_konsumen' => $this->input->post('id_toko_konsumen'),
                    'alamat_konsumen' => $this->input->post('alamat_konsumen')
                ];

                $this->konsumen->update($this->data['pemindahan']->id_konsumen, $data_konsumen);
                $data_update['id_konsumen'] = $this->data['pemindahan']->id_konsumen;
            } else {
                // Buat konsumen baru
                $data_konsumen = [
                    'nama_konsumen' => $this->input->post('nama_konsumen'),
                    'id_toko_konsumen' => $this->input->post('id_toko_konsumen'),
                    'alamat_konsumen' => $this->input->post('alamat_konsumen'),
                    'id_perusahaan' => $id_perusahaan
                ];

                $id_konsumen = $this->konsumen->insert($data_konsumen);
                $data_update['id_konsumen'] = $id_konsumen;
            }

            $data_update['id_gudang_tujuan'] = NULL;
            $data_update['id_pelanggan'] = NULL;
        }

        if ($this->pemindahan->update($id_pemindahan, $data_update)) {
            // Hapus detail lama
            $this->pemindahan->delete_detail($id_pemindahan);

            // Simpan detail barang baru
            $barang_dipindahkan = json_decode($this->input->post('barang_dipindahkan'));

            if ($barang_dipindahkan) {
                foreach ($barang_dipindahkan as $barang) {
                    // Validasi stok
                    $stok = $this->pemindahan->get_stok_barang($this->input->post('id_gudang_asal'), $barang->id_barang);
                    $stok_tersedia = $stok ? ($stok->jumlah - $stok->reserved) : 0;

                    if ($barang->jumlah > $stok_tersedia) {
                        $this->session->set_flashdata('error', 'Stok barang tidak mencukupi!');
                        return redirect('aktifitas/pemindahan/edit/' . $id_pemindahan);
                    }

                    $this->pemindahan->insert_detail([
                        'id_pemindahan' => $id_pemindahan,
                        'id_barang' => $barang->id_barang,
                        'id_gudang' => $this->input->post('id_gudang_asal'),
                        'jumlah' => $barang->jumlah
                    ]);
                }
            }

            $this->session->set_flashdata('success', 'Pemindahan barang berhasil diperbarui');
            return redirect('aktifitas/pemindahan');
        }

        $this->session->set_flashdata('error', 'Gagal memperbarui pemindahan barang!');
        return redirect('aktifitas/pemindahan/edit/' . $id_pemindahan);
    }

    public function konfirmasi($id_pemindahan, $status)
    {
        log_message('debug', '=== START konfirmasi ===');
        log_message('debug', 'Params: id_pemindahan=' . $id_pemindahan . ', status=' . $status);

        if (!$this->check_permission('aktifitas/pemindahan', 'edit')) {
            $this->session->set_flashdata('error', 'Anda tidak memiliki izin untuk mengkonfirmasi pemindahan barang!');
            return redirect('aktifitas/pemindahan');
        }

        $pemindahan = $this->pemindahan->get($id_pemindahan);
        if (!$pemindahan) {
            show_404();
        }

        $valid_status = ['Packing', 'Shipping', 'Delivered', 'Cancelled'];
        if (!in_array($status, $valid_status)) {
            $this->session->set_flashdata('error', 'Status tidak valid!');
            return redirect('aktifitas/pemindahan');
        }

        $current_status = $pemindahan->status;
        $valid_transition = false;

        switch ($current_status) {
            case 'Draft':
                $valid_transition = in_array($status, ['Packing', 'Cancelled']);
                break;
            case 'Packing':
                $valid_transition = in_array($status, ['Shipping', 'Cancelled']);
                break;
            case 'Shipping':
                $valid_transition = in_array($status, ['Delivered', 'Cancelled']);
                break;
        }

        if (!$valid_transition) {
            $this->session->set_flashdata('error', 'Perubahan status dari ' . $current_status . ' ke ' . $status . ' tidak diizinkan!');
            return redirect('aktifitas/pemindahan');
        }

        // Handle stock changes based on status transition
        if ($status == 'Packing' && $current_status == 'Draft') {
            // Untuk Konsumen dan Pelanggan
            if ($pemindahan->tipe_tujuan == 'konsumen' || $pemindahan->tipe_tujuan == 'pelanggan') {
                log_message('debug', 'Draft to Packing (konsumen/pelanggan): kurangi stok dan reserved');
                $this->kurangi_stok_dan_reserved($id_pemindahan);
            }
            // Untuk Gudang, tidak ada perubahan stok saat packing

            // Simpan data ke tabel packing
            $this->simpan_data_packing($id_pemindahan);
        }

        if ($status == 'Delivered') {
            // Untuk Gudang
            if ($pemindahan->tipe_tujuan == 'gudang' && $pemindahan->id_gudang_tujuan) {
                log_message('debug', 'Shipping to Delivered (gudang): kurangi stok gudang asal, tambah stok gudang tujuan, kurangi reserved');
                $this->proses_stok_gudang_delivered($id_pemindahan);
            }
            // Untuk Konsumen dan Pelanggan, tidak ada perubahan stok saat delivered
        }

        if ($status == 'Cancelled') {
            if ($current_status == 'Draft') {
                log_message('debug', 'Draft to Cancelled: kembalikan reserved');
                $this->kembalikan_reserved($id_pemindahan);
            } elseif ($current_status == 'Packing' || $current_status == 'Shipping') {
                log_message('debug', 'Packing/Shipping to Cancelled: kembalikan stok ke gudang asal');
                $this->kembalikan_stok($id_pemindahan);
            }
        }

        // Update status after handling stock
        log_message('debug', 'About to call update_status');
        $update_result = $this->pemindahan->update_status($id_pemindahan, $status);
        log_message('debug', 'Update status result: ' . ($update_result ? 'SUCCESS' : 'FAILED'));

        if ($update_result) {
            $this->log_status_transaksi($id_pemindahan, 'pemindahan_barang', $status);
            $this->session->set_flashdata('success', 'Status pemindahan berhasil diubah menjadi ' . $status);
        } else {
            $this->session->set_flashdata('error', 'Gagal mengubah status pemindahan barang!');
        }

        log_message('debug', '=== END konfirmasi ===');
        return redirect('aktifitas/pemindahan');
    }

    // Fungsi untuk menyimpan data ke tabel packing
    private function simpan_data_packing($id_pemindahan)
    {
        log_message('debug', '=== START simpan_data_packing for pemindahan ID: ' . $id_pemindahan . ' ===');

        $pemindahan = $this->pemindahan->get($id_pemindahan);
        $detail = $this->pemindahan->get_detail($id_pemindahan);

        // Cek apakah sudah ada data packing untuk pemindahan ini
        $this->db->where('id_referensi', $id_pemindahan);
        $this->db->where('tipe_referensi', 'pemindahan_barang');
        $existing_packing = $this->db->get('packing')->row();

        if (!$existing_packing) {
            // Buat data packing baru
            $data_packing = [
                'id_referensi' => $id_pemindahan,
                'tipe_referensi' => 'pemindahan_barang',
                'id_user' => $this->session->userdata('id_user'),
                'tanggal_packing' => date('Y-m-d H:i:s'),
                'status' => 'Packing',
                'catatan' => 'Packing otomatis dari pemindahan barang ' . $pemindahan->no_transaksi
            ];

            $id_packing = $this->pemindahan->insert_packing($data_packing);

            if ($id_packing) {
                // Simpan detail packing
                foreach ($detail as $item) {
                    $data_detail_packing = [
                        'id_packing' => $id_packing,
                        'id_barang' => $item->id_barang,
                        'jumlah' => $item->jumlah
                    ];

                    $this->pemindahan->insert_detail_packing($data_detail_packing);
                }

                // Log status transaksi untuk packing
                $this->log_status_transaksi($id_packing, 'packing', 'Packing');

                log_message('debug', 'Data packing berhasil disimpan dengan ID: ' . $id_packing);
            } else {
                log_message('debug', 'Gagal menyimpan data packing');
            }
        } else {
            log_message('debug', 'Data packing sudah ada untuk pemindahan ID: ' . $id_pemindahan);
        }

        log_message('debug', '=== END simpan_data_packing ===');
    }

    public function detail($id_pemindahan)
    {
        $this->data['title'] = 'Detail Pemindahan Barang';
        $this->data['pemindahan'] = $this->pemindahan->get($id_pemindahan);
        $this->data['detail'] = $this->pemindahan->get_detail($id_pemindahan);

        if (!$this->data['pemindahan']) {
            show_404();
        }

        $this->render_view('aktifitas/pemindahan/detail');
    }

    private function generate_no_transaksi($tipe_tujuan = null)
    {
        // Tentukan prefix berdasarkan tipe tujuan
        $prefix = '';
        switch ($tipe_tujuan) {
            case 'konsumen':
                $prefix = 'K-PMB-';
                break;
            case 'gudang':
                $prefix = 'G-PMB-';
                break;
            case 'pelanggan':
                $prefix = 'P-PMB-';
                break;
            default:
                $prefix = 'PMB-';
        }

        $date_prefix = date('ymd');
        $full_prefix = $prefix . $date_prefix;

        $this->db->like('no_transaksi', $full_prefix, 'after');
        $this->db->order_by('no_transaksi', 'DESC');
        $this->db->limit(1);
        $last = $this->db->get('pemindahan_barang')->row();

        if ($last) {
            $last_number = substr($last->no_transaksi, -5);
            $new_number = str_pad($last_number + 1, 5, '0', STR_PAD_LEFT);
        } else {
            $new_number = '00001';
        }

        return $full_prefix . "-" . $new_number;
    }

    // Fungsi untuk menambah reserved saat draft
    private function tambah_reserved($id_gudang, $id_barang, $jumlah)
    {
        log_message('debug', '=== START tambah_reserved ===');
        log_message('debug', 'Params: id_gudang=' . $id_gudang . ', id_barang=' . $id_barang . ', jumlah=' . $jumlah);

        $stok = $this->pemindahan->get_stok_barang($id_gudang, $id_barang);

        if ($stok) {
            $new_reserved = $stok->reserved + $jumlah;
            $update_result = $this->pemindahan->update_stok($id_gudang, $id_barang, $stok->jumlah, $new_reserved);
            log_message('debug', 'Update reserved result: ' . ($update_result ? 'SUCCESS' : 'FAILED'));
        }

        log_message('debug', '=== END tambah_reserved ===');
    }

    // Fungsi untuk mengurangi stok dan reserved saat packing (untuk konsumen/pelanggan)
    private function kurangi_stok_dan_reserved($id_pemindahan)
    {
        log_message('debug', '=== START kurangi_stok_dan_reserved for pemindahan ID: ' . $id_pemindahan . ' ===');

        $pemindahan = $this->pemindahan->get($id_pemindahan);
        $detail = $this->pemindahan->get_detail($id_pemindahan);

        foreach ($detail as $item) {
            $stok = $this->pemindahan->get_stok_barang($pemindahan->id_gudang_asal, $item->id_barang);

            if ($stok && $stok->jumlah >= $item->jumlah && $stok->reserved >= $item->jumlah) {
                // Kurangi stok dan reserved
                $new_stok = $stok->jumlah - $item->jumlah;
                $new_reserved = $stok->reserved - $item->jumlah;

                $update_result = $this->pemindahan->update_stok($pemindahan->id_gudang_asal, $item->id_barang, $new_stok, $new_reserved);
                log_message('debug', 'Update stok result: ' . ($update_result ? 'SUCCESS' : 'FAILED'));

                // Log stock movement
                $log_data = [
                    'id_barang' => $item->id_barang,
                    'id_user' => $this->session->userdata('id_user'),
                    'id_perusahaan' => $pemindahan->id_perusahaan,
                    'id_gudang' => $pemindahan->id_gudang_asal,
                    'jenis' => 'keluar',
                    'jumlah' => $item->jumlah,
                    'sisa_stok' => $new_stok,
                    'keterangan' => 'Pengambilan barang untuk packing ' . $pemindahan->no_transaksi,
                    'id_referensi' => $id_pemindahan,
                    'tipe_referensi' => 'pemindahan_barang'
                ];

                $this->pemindahan->insert_log_stok($log_data);
            } else {
                log_message('debug', 'Insufficient stock or reserved');
                $this->session->set_flashdata('error', 'Stok tidak mencukupi untuk barang ' . $item->nama_barang);
                return redirect('aktifitas/pemindahan');
            }
        }

        log_message('debug', '=== END kurangi_stok_dan_reserved ===');
    }

    // Fungsi untuk proses stok gudang saat delivered
    private function proses_stok_gudang_delivered($id_pemindahan)
    {
        log_message('debug', '=== START proses_stok_gudang_delivered for pemindahan ID: ' . $id_pemindahan . ' ===');

        $pemindahan = $this->pemindahan->get($id_pemindahan);
        $detail = $this->pemindahan->get_detail($id_pemindahan);

        foreach ($detail as $item) {
            // Proses gudang asal
            $stok_asal = $this->pemindahan->get_stok_barang($pemindahan->id_gudang_asal, $item->id_barang);

            if ($stok_asal && $stok_asal->jumlah >= $item->jumlah && $stok_asal->reserved >= $item->jumlah) {
                // Kurangi stok dan reserved di gudang asal
                $new_stok_asal = $stok_asal->jumlah - $item->jumlah;
                $new_reserved_asal = $stok_asal->reserved - $item->jumlah;

                $update_result_asal = $this->pemindahan->update_stok($pemindahan->id_gudang_asal, $item->id_barang, $new_stok_asal, $new_reserved_asal);
                log_message('debug', 'Update stok asal result: ' . ($update_result_asal ? 'SUCCESS' : 'FAILED'));

                // Log stock movement gudang asal
                $log_data_asal = [
                    'id_barang' => $item->id_barang,
                    'id_user' => $this->session->userdata('id_user'),
                    'id_perusahaan' => $pemindahan->id_perusahaan,
                    'id_gudang' => $pemindahan->id_gudang_asal,
                    'jenis' => 'transfer_keluar',
                    'jumlah' => $item->jumlah,
                    'sisa_stok' => $new_stok_asal,
                    'keterangan' => 'Pengiriman barang ke gudang tujuan ' . $pemindahan->no_transaksi,
                    'id_referensi' => $id_pemindahan,
                    'tipe_referensi' => 'pemindahan_barang'
                ];

                $this->pemindahan->insert_log_stok($log_data_asal);
            } else {
                log_message('debug', 'Insufficient stock or reserved in gudang asal');
                $this->session->set_flashdata('error', 'Stok tidak mencukupi untuk barang ' . $item->nama_barang);
                return redirect('aktifitas/pemindahan');
            }

            // Proses gudang tujuan
            $stok_tujuan = $this->pemindahan->get_stok_barang($pemindahan->id_gudang_tujuan, $item->id_barang);

            if ($stok_tujuan) {
                // Tambah stok di gudang tujuan
                $new_stok_tujuan = $stok_tujuan->jumlah + $item->jumlah;

                $update_result_tujuan = $this->pemindahan->update_stok($pemindahan->id_gudang_tujuan, $item->id_barang, $new_stok_tujuan, $stok_tujuan->reserved);
                log_message('debug', 'Update stok tujuan result: ' . ($update_result_tujuan ? 'SUCCESS' : 'FAILED'));

                // Log stock movement gudang tujuan
                $log_data_tujuan = [
                    'id_barang' => $item->id_barang,
                    'id_user' => $this->session->userdata('id_user'),
                    'id_perusahaan' => $pemindahan->id_perusahaan,
                    'id_gudang' => $pemindahan->id_gudang_tujuan,
                    'jenis' => 'transfer_masuk',
                    'jumlah' => $item->jumlah,
                    'sisa_stok' => $new_stok_tujuan,
                    'keterangan' => 'Penerimaan barang dari gudang asal ' . $pemindahan->no_transaksi,
                    'id_referensi' => $id_pemindahan,
                    'tipe_referensi' => 'pemindahan_barang'
                ];

                $this->pemindahan->insert_log_stok($log_data_tujuan);
            } else {
                // Create new stock record if not exists
                $new_stok_tujuan = $item->jumlah;
                log_message('debug', 'Creating new stock record with quantity: ' . $new_stok_tujuan);

                $update_result_tujuan = $this->pemindahan->update_stok($pemindahan->id_gudang_tujuan, $item->id_barang, $new_stok_tujuan, 0);
                log_message('debug', 'Insert result: ' . ($update_result_tujuan ? 'SUCCESS' : 'FAILED'));

                // Log stock movement gudang tujuan
                $log_data_tujuan = [
                    'id_barang' => $item->id_barang,
                    'id_user' => $this->session->userdata('id_user'),
                    'id_perusahaan' => $pemindahan->id_perusahaan,
                    'id_gudang' => $pemindahan->id_gudang_tujuan,
                    'jenis' => 'transfer_masuk',
                    'jumlah' => $item->jumlah,
                    'sisa_stok' => $new_stok_tujuan,
                    'keterangan' => 'Penerimaan barang dari gudang asal ' . $pemindahan->no_transaksi,
                    'id_referensi' => $id_pemindahan,
                    'tipe_referensi' => 'pemindahan_barang'
                ];

                $this->pemindahan->insert_log_stok($log_data_tujuan);
            }
        }

        log_message('debug', '=== END proses_stok_gudang_delivered ===');
    }

    // Fungsi untuk mengembalikan reserved saat cancel dari draft
    private function kembalikan_reserved($id_pemindahan)
    {
        log_message('debug', '=== START kembalikan_reserved for pemindahan ID: ' . $id_pemindahan . ' ===');

        $pemindahan = $this->pemindahan->get($id_pemindahan);
        $detail = $this->pemindahan->get_detail($id_pemindahan);

        foreach ($detail as $item) {
            $stok = $this->pemindahan->get_stok_barang($pemindahan->id_gudang_asal, $item->id_barang);

            if ($stok) {
                // Kembalikan reserved
                $new_reserved = $stok->reserved - $item->jumlah;

                $update_result = $this->pemindahan->update_stok($pemindahan->id_gudang_asal, $item->id_barang, $stok->jumlah, $new_reserved);
                log_message('debug', 'Update reserved result: ' . ($update_result ? 'SUCCESS' : 'FAILED'));
            }
        }

        log_message('debug', '=== END kembalikan_reserved ===');
    }

    // Fungsi untuk mengembalikan stok saat cancel dari packing/shipping
    private function kembalikan_stok($id_pemindahan)
    {
        log_message('debug', '=== START kembalikan_stok for pemindahan ID: ' . $id_pemindahan . ' ===');

        $pemindahan = $this->pemindahan->get($id_pemindahan);
        $detail = $this->pemindahan->get_detail($id_pemindahan);

        foreach ($detail as $item) {
            $stok = $this->pemindahan->get_stok_barang($pemindahan->id_gudang_asal, $item->id_barang);

            if ($stok) {
                // Kembalikan stok
                $new_stok = $stok->jumlah + $item->jumlah;

                $update_result = $this->pemindahan->update_stok($pemindahan->id_gudang_asal, $item->id_barang, $new_stok, $stok->reserved);
                log_message('debug', 'Update stok result: ' . ($update_result ? 'SUCCESS' : 'FAILED'));

                // Log stock movement
                $log_data = [
                    'id_barang' => $item->id_barang,
                    'id_user' => $this->session->userdata('id_user'),
                    'id_perusahaan' => $pemindahan->id_perusahaan,
                    'id_gudang' => $pemindahan->id_gudang_asal,
                    'jenis' => 'penyesuaian',
                    'jumlah' => $item->jumlah,
                    'sisa_stok' => $new_stok,
                    'keterangan' => 'Pembatalan transfer barang ' . $pemindahan->no_transaksi,
                    'id_referensi' => $id_pemindahan,
                    'tipe_referensi' => 'pemindahan_barang'
                ];

                $this->pemindahan->insert_log_stok($log_data);
            }
        }

        log_message('debug', '=== END kembalikan_stok ===');
    }

    private function log_status_transaksi($id_transaksi, $tipe_transaksi, $status)
    {
        $log_data = [
            'id_transaksi' => $id_transaksi,
            'tipe_transaksi' => $tipe_transaksi,
            'id_user' => $this->session->userdata('id_user'),
            'status' => $status,
            'tanggal' => date('Y-m-d H:i:s')
        ];

        $this->pemindahan->insert_log_status($log_data);
    }
}