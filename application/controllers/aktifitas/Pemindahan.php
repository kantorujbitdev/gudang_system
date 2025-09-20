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
        $this->data['gudang'] = $this->gudang->get_all();
        $this->data['pelanggan'] = $this->pelanggan->get_all();
        $this->data['barang'] = $this->barang->get_all();

        $this->form_validation->set_rules('id_gudang_asal', 'Gudang Asal', 'required');
        $this->form_validation->set_rules('tipe_tujuan', 'Tipe Tujuan', 'required');
        $this->form_validation->set_rules('tanggal', 'Tanggal', 'required');

        if ($this->form_validation->run() == FALSE) {
            return $this->render_view('aktifitas/pemindahan/form');
        }

        // Generate nomor transfer
        $no_transfer = $this->generate_no_transfer();
        $data_insert = [
            'no_transfer' => $no_transfer,
            'id_perusahaan' => $this->session->userdata('id_perusahaan'),
            'id_gudang_asal' => $this->input->post('id_gudang_asal'),
            'id_user' => $this->session->userdata('id_user'),
            'tanggal' => $this->input->post('tanggal') . ' ' . date('H:i:s'),
            'keterangan' => $this->input->post('keterangan'),
            'status' => 'Draft'
        ];

        $tipe_tujuan = $this->input->post('tipe_tujuan');
        if ($tipe_tujuan == 'gudang') {
            $data_insert['id_gudang_tujuan'] = $this->input->post('id_gudang_tujuan');
        } elseif ($tipe_tujuan == 'pelanggan') {
            $data_insert['id_pelanggan'] = $this->input->post('id_pelanggan');
        }

        $id_transfer = $this->pemindahan->insert($data_insert);

        if ($id_transfer) {
            // Simpan detail barang
            $barang_ids = $this->input->post('id_barang');
            $jumlahs = $this->input->post('jumlah');

            foreach ($barang_ids as $key => $id_barang) {
                if ($id_barang && $jumlahs[$key] > 0) {
                    $this->pemindahan->insert_detail([
                        'id_transfer' => $id_transfer,
                        'id_barang' => $id_barang,
                        'jumlah' => $jumlahs[$key]
                    ]);
                }
            }

            $this->session->set_flashdata('success', 'Pemindahan barang berhasil dibuat dengan nomor: ' . $no_transfer);
            return redirect('aktifitas/pemindahan');
        }

        $this->session->set_flashdata('error', 'Gagal membuat pemindahan barang!');
        return redirect('aktifitas/pemindahan/tambah');
    }

    public function edit($id_transfer)
    {
        if (!$this->check_permission('aktifitas/pemindahan', 'edit')) {
            $this->session->set_flashdata('error', 'Anda tidak memiliki izin untuk mengubah pemindahan barang!');
            return redirect('aktifitas/pemindahan');
        }

        $this->data['title'] = 'Edit Pemindahan Barang';
        $this->data['pemindahan'] = $this->pemindahan->get($id_transfer);
        $this->data['detail'] = $this->pemindahan->get_detail($id_transfer);
        $this->data['gudang'] = $this->gudang->get_all();
        $this->data['pelanggan'] = $this->pelanggan->get_all();
        $this->data['barang'] = $this->barang->get_all();

        if (!$this->data['pemindahan']) {
            show_404();
        }

        if ($this->data['pemindahan']->status != 'Draft') {
            $this->session->set_flashdata('error', 'Pemindahan dengan status ' . $this->data['pemindahan']->status . ' tidak dapat diubah!');
            return redirect('aktifitas/pemindahan');
        }

        $this->form_validation->set_rules('id_gudang_asal', 'Gudang Asal', 'required');
        $this->form_validation->set_rules('tipe_tujuan', 'Tipe Tujuan', 'required');
        $this->form_validation->set_rules('tanggal', 'Tanggal', 'required');

        if ($this->form_validation->run() == FALSE) {
            return $this->render_view('aktifitas/pemindahan/form');
        }

        $data_update = [
            'id_gudang_asal' => $this->input->post('id_gudang_asal'),
            'tanggal' => $this->input->post('tanggal') . ' ' . date('H:i:s'),
            'keterangan' => $this->input->post('keterangan')
        ];

        $tipe_tujuan = $this->input->post('tipe_tujuan');
        if ($tipe_tujuan == 'gudang') {
            $data_update['id_gudang_tujuan'] = $this->input->post('id_gudang_tujuan');
            $data_update['id_pelanggan'] = NULL;
        } elseif ($tipe_tujuan == 'pelanggan') {
            $data_update['id_pelanggan'] = $this->input->post('id_pelanggan');
            $data_update['id_gudang_tujuan'] = NULL;
        }

        if ($this->pemindahan->update($id_transfer, $data_update)) {
            $this->pemindahan->delete_detail($id_transfer);

            $barang_ids = $this->input->post('id_barang');
            $jumlahs = $this->input->post('jumlah');

            foreach ($barang_ids as $key => $id_barang) {
                if ($id_barang && $jumlahs[$key] > 0) {
                    $this->pemindahan->insert_detail([
                        'id_transfer' => $id_transfer,
                        'id_barang' => $id_barang,
                        'jumlah' => $jumlahs[$key]
                    ]);
                }
            }

            $this->session->set_flashdata('success', 'Pemindahan barang berhasil diperbarui');
            return redirect('aktifitas/pemindahan');
        }

        $this->session->set_flashdata('error', 'Gagal memperbarui pemindahan barang!');
        return redirect('aktifitas/pemindahan/edit/' . $id_transfer);
    }

    public function hapus($id_transfer)
    {
        if (!$this->check_permission('aktifitas/pemindahan', 'delete')) {
            $this->session->set_flashdata('error', 'Anda tidak memiliki izin untuk menghapus pemindahan barang!');
            return redirect('aktifitas/pemindahan');
        }

        $pemindahan = $this->pemindahan->get($id_transfer);
        if (!$pemindahan) {
            show_404();
        }

        if ($pemindahan->status != 'Draft') {
            $this->session->set_flashdata('error', 'Pemindahan dengan status ' . $pemindahan->status . ' tidak dapat dihapus!');
            return redirect('aktifitas/pemindahan');
        }

        if ($this->pemindahan->delete($id_transfer)) {
            $this->session->set_flashdata('success', 'Pemindahan barang berhasil dihapus');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus pemindahan barang!');
        }
        return redirect('aktifitas/pemindahan');
    }

    public function konfirmasi($id_transfer, $status)
    {
        if (!$this->check_permission('aktifitas/pemindahan', 'edit')) {
            $this->session->set_flashdata('error', 'Anda tidak memiliki izin untuk mengkonfirmasi pemindahan barang!');
            return redirect('aktifitas/pemindahan');
        }

        $pemindahan = $this->pemindahan->get($id_transfer);
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

        if ($this->pemindahan->update_status($id_transfer, $status)) {
            if ($status == 'Shipping' && $current_status != 'Shipping') {
                $this->kurangi_stok($id_transfer);
            }
            if ($status == 'Delivered' && $pemindahan->id_gudang_tujuan) {
                $this->tambah_stok($id_transfer);
            }
            if ($status == 'Cancelled' && $current_status == 'Shipping') {
                $this->kembalikan_stok($id_transfer);
            }

            $this->log_status_transaksi($id_transfer, 'transfer_stok', $status);
            $this->session->set_flashdata('success', 'Status pemindahan berhasil diubah menjadi ' . $status);
        } else {
            $this->session->set_flashdata('error', 'Gagal mengubah status pemindahan barang!');
        }

        return redirect('aktifitas/pemindahan');
    }

    public function detail($id_transfer)
    {
        $this->data['title'] = 'Detail Pemindahan Barang';
        $this->data['pemindahan'] = $this->pemindahan->get($id_transfer);
        $this->data['detail'] = $this->pemindahan->get_detail($id_transfer);

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

    private function generate_no_transfer()
    {
        $prefix = 'TRF-' . date('ymd');
        $this->db->like('no_transfer', $prefix, 'after');
        $this->db->order_by('no_transfer', 'DESC');
        $this->db->limit(1);
        $last = $this->db->get('transfer_stok')->row();

        if ($last) {
            $last_number = substr($last->no_transfer, -4);
            $new_number = str_pad($last_number + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $new_number = '0001';
        }

        return $prefix . $new_number;
    }

    private function kurangi_stok($id_transfer)
    {
        $pemindahan = $this->pemindahan->get($id_transfer);
        $detail = $this->pemindahan->get_detail($id_transfer);

        foreach ($detail as $item) {
            // Get current stock
            $stok = $this->pemindahan->get_stok_barang($pemindahan->id_gudang_asal, $item->id_barang);

            if ($stok && $stok->jumlah >= $item->jumlah) {
                // Update stock
                $new_stok = $stok->jumlah - $item->jumlah;
                $this->pemindahan->update_stok($pemindahan->id_gudang_asal, $item->id_barang, $new_stok);

                // Log stock movement
                $log_data = [
                    'id_barang' => $item->id_barang,
                    'id_user' => $this->session->userdata('id_user'),
                    'id_perusahaan' => $this->session->userdata('id_perusahaan'),
                    'id_gudang' => $pemindahan->id_gudang_asal,
                    'jenis' => 'transfer_keluar',
                    'jumlah' => $item->jumlah,
                    'sisa_stok' => $new_stok,
                    'keterangan' => 'Pemindahan barang ' . $pemindahan->no_transfer,
                    'id_referensi' => $id_transfer,
                    'tipe_referensi' => 'transfer'
                ];
                $this->pemindahan->insert_log_stok($log_data);
            }
        }
    }

    private function tambah_stok($id_transfer)
    {
        $pemindahan = $this->pemindahan->get($id_transfer);
        $detail = $this->pemindahan->get_detail($id_transfer);

        foreach ($detail as $item) {
            // Get current stock
            $stok = $this->pemindahan->get_stok_barang($pemindahan->id_gudang_tujuan, $item->id_barang);

            if ($stok) {
                // Update stock
                $new_stok = $stok->jumlah + $item->jumlah;
                $this->pemindahan->update_stok($pemindahan->id_gudang_tujuan, $item->id_barang, $new_stok);

                // Log stock movement
                $log_data = [
                    'id_barang' => $item->id_barang,
                    'id_user' => $this->session->userdata('id_user'),
                    'id_perusahaan' => $this->session->userdata('id_perusahaan'),
                    'id_gudang' => $pemindahan->id_gudang_tujuan,
                    'jenis' => 'transfer_masuk',
                    'jumlah' => $item->jumlah,
                    'sisa_stok' => $new_stok,
                    'keterangan' => 'Penerimaan transfer barang ' . $pemindahan->no_transfer,
                    'id_referensi' => $id_transfer,
                    'tipe_referensi' => 'transfer'
                ];
                $this->pemindahan->insert_log_stok($log_data);
            }
        }
    }

    private function kembalikan_stok($id_transfer)
    {
        $pemindahan = $this->pemindahan->get($id_transfer);
        $detail = $this->pemindahan->get_detail($id_transfer);

        foreach ($detail as $item) {
            // Get current stock
            $stok = $this->pemindahan->get_stok_barang($pemindahan->id_gudang_asal, $item->id_barang);

            if ($stok) {
                // Update stock
                $new_stok = $stok->jumlah + $item->jumlah;
                $this->pemindahan->update_stok($pemindahan->id_gudang_asal, $item->id_barang, $new_stok);

                // Log stock movement
                $log_data = [
                    'id_barang' => $item->id_barang,
                    'id_user' => $this->session->userdata('id_user'),
                    'id_perusahaan' => $this->session->userdata('id_perusahaan'),
                    'id_gudang' => $pemindahan->id_gudang_asal,
                    'jenis' => 'penyesuaian',
                    'jumlah' => $item->jumlah,
                    'sisa_stok' => $new_stok,
                    'keterangan' => 'Pembatalan transfer barang ' . $pemindahan->no_transfer,
                    'id_referensi' => $id_transfer,
                    'tipe_referensi' => 'transfer'
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
            'status' => $status
        ];

        $this->pemindahan->insert_log_status($log_data);
    }
}