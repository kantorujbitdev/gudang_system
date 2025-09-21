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
        $this->load->helper('form');
        $this->load->library('form_validation');

        // Cek akses menu
        $this->check_menu_access('aktifitas/pemindahan');
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

        // Get alamat konsumen
        $this->data['alamat_konsumen'] = $this->pemindahan->get_alamat_konsumen($id_user);

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
            return $this->render_view('aktifitas/pemindahan/form');
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

        if ($tipe_tujuan == 'gudang') {
            $data_insert['id_gudang_tujuan'] = $this->input->post('id_gudang_tujuan');
        } elseif ($tipe_tujuan == 'pelanggan') {
            $data_insert['id_pelanggan'] = $this->input->post('id_pelanggan');
        } elseif ($tipe_tujuan == 'konsumen') {
            $data_insert['id_alamat_konsumen'] = $this->input->post('id_alamat_konsumen');
        }

        $id_pemindahan = $this->pemindahan->insert($data_insert);

        if ($id_pemindahan) {
            // Simpan detail barang
            $barang_ids = $this->input->post('id_barang');
            $jumlahs = $this->input->post('jumlah');

            foreach ($barang_ids as $key => $id_barang) {
                if ($id_barang && $jumlahs[$key] > 0) {
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

    public function edit($id_pemindahan)
    {
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

        // Get alamat konsumen
        $this->data['alamat_konsumen'] = $this->pemindahan->get_alamat_konsumen($id_user);

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
            return $this->render_view('aktifitas/pemindahan/form');
        }

        $data_update = [
            'id_gudang_asal' => $this->input->post('id_gudang_asal'),
            'keterangan' => $this->input->post('keterangan')
        ];

        $tipe_tujuan = $this->input->post('tipe_tujuan');
        $data_update['tipe_tujuan'] = $tipe_tujuan;

        if ($tipe_tujuan == 'gudang') {
            $data_update['id_gudang_tujuan'] = $this->input->post('id_gudang_tujuan');
            $data_update['id_pelanggan'] = NULL;
            $data_update['id_alamat_konsumen'] = NULL;
        } elseif ($tipe_tujuan == 'pelanggan') {
            $data_update['id_pelanggan'] = $this->input->post('id_pelanggan');
            $data_update['id_gudang_tujuan'] = NULL;
            $data_update['id_alamat_konsumen'] = NULL;
        } elseif ($tipe_tujuan == 'konsumen') {
            $data_update['id_alamat_konsumen'] = $this->input->post('id_alamat_konsumen');
            $data_update['id_gudang_tujuan'] = NULL;
            $data_update['id_pelanggan'] = NULL;
        }

        if ($this->pemindahan->update($id_pemindahan, $data_update)) {
            $this->pemindahan->delete_detail($id_pemindahan);

            $barang_ids = $this->input->post('id_barang');
            $jumlahs = $this->input->post('jumlah');

            foreach ($barang_ids as $key => $id_barang) {
                if ($id_barang && $jumlahs[$key] > 0) {
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
            if ($status == 'Delivered' && $pemindahan->id_gudang_tujuan) {
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
        $id_gudang = $this->input->post('id_gudang');
        $id_barang = $this->input->post('id_barang');

        $stok = $this->pemindahan->get_stok_barang($id_gudang, $id_barang);

        echo json_encode([
            'stok' => $stok ? $stok->jumlah : 0,
            'reserved' => $stok ? $stok->reserved : 0,
            'tersedia' => $stok ? ($stok->jumlah - $stok->reserved) : 0
        ]);
    }

    public function get_data_by_perusahaan()
    {
        $id_perusahaan = $this->input->post('id_perusahaan');
        $data = $this->pemindahan->get_data_by_perusahaan($id_perusahaan);
        echo json_encode($data);
    }

    public function simpan_alamat()
    {
        $this->load->library('form_validation');

        $this->form_validation->set_rules('alamat_lengkap', 'Alamat Lengkap', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode([
                'status' => 'error',
                'message' => validation_errors()
            ]);
            return;
        }

        $data = [
            'id_user' => $this->session->userdata('id_user'),
            'alamat_lengkap' => $this->input->post('alamat_lengkap'),
            'keterangan' => $this->input->post('keterangan_alamat'),
            'status_aktif' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $id_alamat = $this->pemindahan->insert_alamat_konsumen($data);

        if ($id_alamat) {
            echo json_encode([
                'status' => 'success',
                'id_alamat' => $id_alamat
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Gagal menyimpan alamat'
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