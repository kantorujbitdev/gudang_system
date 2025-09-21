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

    // Perbaikan masalah CSRF token
    public function get_data_by_perusahaan()
    {
        // Set header untuk JSON response
        header('Content-Type: application/json');

        // Cek CSRF token
        $csrf_token_name = $this->security->get_csrf_token_name();
        $csrf_hash = $this->security->get_csrf_hash();

        // Ambil token dari POST
        $posted_token = $this->input->post($csrf_token_name);

        // Validasi token
        if ($posted_token !== $csrf_hash) {
            echo json_encode(['error' => 'Invalid CSRF token']);
            return;
        }

        $id_perusahaan = $this->input->post('id_perusahaan');

        if ($id_perusahaan) {
            $data = $this->pemindahan->get_data_by_perusahaan($id_perusahaan);

            // Update CSRF token untuk response
            $new_token = $this->security->get_csrf_hash();
            $data['csrf_token'] = $new_token;
            $data['csrf_name'] = $csrf_token_name;

            echo json_encode($data);
        } else {
            echo json_encode([]);
        }
    }

    public function get_barang_by_gudang()
    {
        header('Content-Type: application/json');

        // Cek CSRF token
        $csrf_token_name = $this->security->get_csrf_token_name();
        $posted_token = $this->input->post($csrf_token_name);

        if ($posted_token !== $this->security->get_csrf_hash()) {
            echo json_encode(['error' => 'Invalid CSRF token']);
            return;
        }

        $id_gudang = $this->input->post('id_gudang');

        if ($id_gudang) {
            $barang = $this->pemindahan->get_barang_by_gudang_with_stock($id_gudang);
            echo json_encode($barang);
        } else {
            echo json_encode([]);
        }
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

        $this->form_validation->set_rules('id_gudang_asal', 'Gudang Asal', 'required');
        $this->form_validation->set_rules('tipe_tujuan', 'Tipe Tujuan', 'required');

        if ($this->form_validation->run() == FALSE) {
            return $this->render_view('aktifitas/pemindahan/form', $data);
        }

        // Generate nomor transaksi
        $no_transaksi = $this->generate_no_transaksi();

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

        $tipe_tujuan = $this->input->post('tipe_tujuan');
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
            // Buat konsumen baru
            $data_konsumen = [
                'nama_konsumen' => $this->input->post('nama_konsumen'),
                'id_toko_konsumen' => $this->input->post('id_toko_konsumen'),
                'alamat_konsumen' => $this->input->post('alamat_konsumen'),
                'id_perusahaan' => $id_perusahaan
            ];

            $id_konsumen = $this->konsumen->insert($data_konsumen);
            $data_insert['id_konsumen'] = $id_konsumen;
        }

        $id_pemindahan = $this->pemindahan->insert($data_insert);

        if ($id_pemindahan) {
            // Simpan detail barang
            $barang_ids = $this->input->post('id_barang');
            $jumlahs = $this->input->post('jumlah');

            // Cek duplikasi barang
            $unique_barang = array_unique($barang_ids);
            if (count($barang_ids) != count($unique_barang)) {
                $this->session->set_flashdata('error', 'Tidak boleh ada barang yang duplikat dalam satu transaksi!');
                return redirect('aktifitas/pemindahan/tambah');
            }

            foreach ($barang_ids as $key => $id_barang) {
                if ($id_barang && $jumlahs[$key] > 0) {
                    // Validasi stok
                    $stok = $this->pemindahan->get_stok_barang($this->input->post('id_gudang_asal'), $id_barang);
                    $stok_tersedia = $stok ? ($stok->jumlah - $stok->reserved) : 0;

                    if ($jumlahs[$key] > $stok_tersedia) {
                        $this->session->set_flashdata('error', 'Stok barang tidak mencukupi!');
                        return redirect('aktifitas/pemindahan/tambah');
                    }

                    $this->pemindahan->insert_detail([
                        'id_pemindahan' => $id_pemindahan,
                        'id_barang' => $id_barang,
                        'id_gudang' => $this->input->post('id_gudang_asal'),
                        'jumlah' => $jumlahs[$key]
                    ]);
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
    // Add a method to refresh CSRF token
    public function refresh_csrf()
    {
        header('Content-Type: application/json');
        echo json_encode([
            'csrf_token' => $this->security->get_csrf_hash(),
            'csrf_name' => $this->security->get_csrf_token_name()
        ]);
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
            $this->pemindahan->delete_detail($id_pemindahan);

            $barang_ids = $this->input->post('id_barang');
            $jumlahs = $this->input->post('jumlah');

            // Cek duplikasi barang
            $unique_barang = array_unique($barang_ids);
            if (count($barang_ids) != count($unique_barang)) {
                $this->session->set_flashdata('error', 'Tidak boleh ada barang yang duplikat dalam satu transaksi!');
                return redirect('aktifitas/pemindahan/edit/' . $id_pemindahan);
            }

            foreach ($barang_ids as $key => $id_barang) {
                if ($id_barang && $jumlahs[$key] > 0) {
                    // Validasi stok
                    $stok = $this->pemindahan->get_stok_barang($this->input->post('id_gudang_asal'), $id_barang);
                    $stok_tersedia = $stok ? ($stok->jumlah - $stok->reserved) : 0;

                    if ($jumlahs[$key] > $stok_tersedia) {
                        $this->session->set_flashdata('error', 'Stok barang tidak mencukupi!');
                        return redirect('aktifitas/pemindahan/edit/' . $id_pemindahan);
                    }

                    $this->pemindahan->insert_detail([
                        'id_pemindahan' => $id_pemindahan,
                        'id_barang' => $id_barang,
                        'id_gudang' => $this->input->post('id_gudang_asal'),
                        'jumlah' => $jumlahs[$key]
                    ]);
                }
            }

            $this->session->set_flashdata('success', 'Pemindahan barang berhasil diperbarui');
            return redirect('aktifitas/pemindahan');
        }

        $this->session->set_flashdata('error', 'Gagal memperbarui pemindahan barang!');
        return redirect('aktifitas/pemindahan/edit/' . $id_pemindahan);
    }

    public function hapus($id_pemindahan)
    {
        if (!$this->check_permission('aktifitas/pemindahan', 'delete')) {
            $this->session->set_flashdata('error', 'Anda tidak memiliki izin untuk menghapus pemindahan barang!');
            return redirect('aktifitas/pemindahan');
        }

        $pemindahan = $this->pemindahan->get($id_pemindahan);
        if (!$pemindahan) {
            show_404();
        }

        if ($pemindahan->status != 'Draft') {
            $this->session->set_flashdata('error', 'Pemindahan dengan status ' . $pemindahan->status . ' tidak dapat dihapus!');
            return redirect('aktifitas/pemindahan');
        }

        if ($this->pemindahan->delete($id_pemindahan)) {
            $this->session->set_flashdata('success', 'Pemindahan barang berhasil dihapus');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus pemindahan barang!');
        }
        return redirect('aktifitas/pemindahan');
    }

    public function konfirmasi($id_pemindahan, $status)
    {
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

        if ($this->pemindahan->update_status($id_pemindahan, $status)) {
            if ($status == 'Shipping' && $current_status != 'Shipping') {
                $this->kurangi_stok($id_pemindahan);
            }
            if ($status == 'Delivered' && $pemindahan->tipe_tujuan == 'gudang' && $pemindahan->id_gudang_tujuan) {
                $this->tambah_stok($id_pemindahan);
            }
            if ($status == 'Cancelled' && $current_status == 'Shipping') {
                $this->kembalikan_stok($id_pemindahan);
            }

            $this->log_status_transaksi($id_pemindahan, 'pemindahan_barang', $status);
            $this->session->set_flashdata('success', 'Status pemindahan berhasil diubah menjadi ' . $status);
        } else {
            $this->session->set_flashdata('error', 'Gagal mengubah status pemindahan barang!');
        }

        return redirect('aktifitas/pemindahan');
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

    public function get_stok_barang()
    {
        header('Content-Type: application/json');
        $id_gudang = $this->input->post('id_gudang');
        $id_barang = $this->input->post('id_barang');

        $stok = $this->pemindahan->get_stok_barang($id_gudang, $id_barang);

        echo json_encode([
            'stok' => $stok ? $stok->jumlah : 0,
            'reserved' => $stok ? $stok->reserved : 0,
            'tersedia' => $stok ? ($stok->jumlah - $stok->reserved) : 0
        ]);
    }

    public function get_alamat_pelanggan()
    {
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

    private function generate_no_transaksi()
    {
        $prefix = 'PMB-' . date('ymd');
        $this->db->like('no_transaksi', $prefix, 'after');
        $this->db->order_by('no_transaksi', 'DESC');
        $this->db->limit(1);
        $last = $this->db->get('pemindahan_barang')->row();

        if ($last) {
            $last_number = substr($last->no_transaksi, -4);
            $new_number = str_pad($last_number + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $new_number = '0001';
        }

        return $prefix . $new_number;
    }

    private function kurangi_stok($id_pemindahan)
    {
        $pemindahan = $this->pemindahan->get($id_pemindahan);
        $detail = $this->pemindahan->get_detail($id_pemindahan);

        foreach ($detail as $item) {
            // Get current stock
            $stok = $this->pemindahan->get_stok_barang($pemindahan->id_gudang_asal, $item->id_barang);

            if ($stok && $stok->jumlah >= $item->jumlah) {
                // Update stock
                $new_stok = $stok->jumlah - $item->jumlah;
                $this->pemindahan->update_stok($pemindahan->id_gudang_asal, $item->id_barang, $new_stok, $stok->reserved);

                // Log stock movement
                $log_data = [
                    'id_barang' => $item->id_barang,
                    'id_user' => $this->session->userdata('id_user'),
                    'id_perusahaan' => $this->session->userdata('id_perusahaan'),
                    'id_gudang' => $pemindahan->id_gudang_asal,
                    'jenis' => 'keluar',
                    'jumlah' => $item->jumlah,
                    'sisa_stok' => $new_stok,
                    'keterangan' => 'Pemindahan barang ' . $pemindahan->no_transaksi,
                    'id_referensi' => $id_pemindahan,
                    'tipe_referensi' => 'pemindahan_barang'
                ];
                $this->pemindahan->insert_log_stok($log_data);
            }
        }
    }

    private function tambah_stok($id_pemindahan)
    {
        $pemindahan = $this->pemindahan->get($id_pemindahan);
        $detail = $this->pemindahan->get_detail($id_pemindahan);

        foreach ($detail as $item) {
            // Get current stock
            $stok = $this->pemindahan->get_stok_barang($pemindahan->id_gudang_tujuan, $item->id_barang);

            if ($stok) {
                // Update stock
                $new_stok = $stok->jumlah + $item->jumlah;
                $this->pemindahan->update_stok($pemindahan->id_gudang_tujuan, $item->id_barang, $new_stok, $stok->reserved);

                // Log stock movement
                $log_data = [
                    'id_barang' => $item->id_barang,
                    'id_user' => $this->session->userdata('id_user'),
                    'id_perusahaan' => $this->session->userdata('id_perusahaan'),
                    'id_gudang' => $pemindahan->id_gudang_tujuan,
                    'jenis' => 'transfer_masuk',
                    'jumlah' => $item->jumlah,
                    'sisa_stok' => $new_stok,
                    'keterangan' => 'Penerimaan transfer barang ' . $pemindahan->no_transaksi,
                    'id_referensi' => $id_pemindahan,
                    'tipe_referensi' => 'pemindahan_barang'
                ];
                $this->pemindahan->insert_log_stok($log_data);
            } else {
                // Create new stock record if not exists
                $new_stok_data = [
                    'id_perusahaan' => $pemindahan->id_perusahaan,
                    'id_gudang' => $pemindahan->id_gudang_tujuan,
                    'id_barang' => $item->id_barang,
                    'jumlah' => $item->jumlah,
                    'reserved' => 0
                ];
                $this->db->insert('stok_gudang', $new_stok_data);

                // Log stock movement
                $log_data = [
                    'id_barang' => $item->id_barang,
                    'id_user' => $this->session->userdata('id_user'),
                    'id_perusahaan' => $this->session->userdata('id_perusahaan'),
                    'id_gudang' => $pemindahan->id_gudang_tujuan,
                    'jenis' => 'transfer_masuk',
                    'jumlah' => $item->jumlah,
                    'sisa_stok' => $item->jumlah,
                    'keterangan' => 'Penerimaan transfer barang ' . $pemindahan->no_transaksi,
                    'id_referensi' => $id_pemindahan,
                    'tipe_referensi' => 'pemindahan_barang'
                ];
                $this->pemindahan->insert_log_stok($log_data);
            }
        }
    }

    private function kembalikan_stok($id_pemindahan)
    {
        $pemindahan = $this->pemindahan->get($id_pemindahan);
        $detail = $this->pemindahan->get_detail($id_pemindahan);

        foreach ($detail as $item) {
            // Get current stock
            $stok = $this->pemindahan->get_stok_barang($pemindahan->id_gudang_asal, $item->id_barang);

            if ($stok) {
                // Update stock
                $new_stok = $stok->jumlah + $item->jumlah;
                $this->pemindahan->update_stok($pemindahan->id_gudang_asal, $item->id_barang, $new_stok, $stok->reserved);

                // Log stock movement
                $log_data = [
                    'id_barang' => $item->id_barang,
                    'id_user' => $this->session->userdata('id_user'),
                    'id_perusahaan' => $this->session->userdata('id_perusahaan'),
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