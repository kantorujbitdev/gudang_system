<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Retur_pembelian extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('daftar/Retur_pembelian_model', 'retur');
        $this->load->model('setup/Barang_model', 'barang');
        $this->load->model('setup/Gudang_model', 'gudang');
        $this->load->model('setup/Supplier_model', 'supplier');
        $this->load->helper('form');
        $this->load->library('form_validation');

        // Cek akses menu
        $this->check_menu_access('daftar/retur_pembelian');
    }

    public function index()
    {
        $this->data['title'] = 'Retur Pembelian';
        $this->data['retur'] = $this->retur->get_all();
        $this->data['can_create'] = $this->check_permission('daftar/retur_pembelian', 'create');
        $this->data['can_edit'] = $this->check_permission('daftar/retur_pembelian', 'edit');
        $this->data['can_delete'] = $this->check_permission('daftar/retur_pembelian', 'delete');

        $this->render_view('daftar/retur_pembelian/index');
    }



    public function tambah()
    {
        // Check permission
        if (!$this->check_permission('daftar/retur_pembelian', 'create')) {
            $this->session->set_flashdata('error', 'Anda tidak memiliki izin untuk membuat retur pembelian!');
            return redirect('daftar/retur_pembelian');
        }

        $this->data['title'] = 'Tambah Retur Pembelian';
        $this->data['penerimaan'] = $this->retur->get_penerimaan();
        $this->data['supplier'] = $this->supplier->get_by_perusahaan($this->session->userdata('id_perusahaan'));
        $this->data['gudang'] = $this->gudang->get_by_perusahaan($this->session->userdata('id_perusahaan'));
        $this->data['barang'] = $this->barang->get_by_perusahaan($this->session->userdata('id_perusahaan'));
        $this->data['extra_js'] = 'daftar/retur_pembelian/script';

        $this->form_validation->set_rules('id_penerimaan', 'Penerimaan Barang', 'required');
        $this->form_validation->set_rules('alasan_retur', 'Alasan Retur', 'required');

        if ($this->form_validation->run() == FALSE) {
            return $this->render_view('daftar/retur_pembelian/form', $this->data);
        }

        // Check if penerimaan already returned
        $id_penerimaan = $this->input->post('id_penerimaan');
        if ($this->retur->is_penerimaan_already_returned($id_penerimaan)) {
            $this->session->set_flashdata('error', 'Penerimaan barang ini sudah pernah di-retur!');
            return redirect('daftar/retur_pembelian/tambah');
        }

        // Generate nomor retur
        $no_retur = $this->generate_no_retur();

        $data_insert = [
            'no_retur_beli' => $no_retur,
            'id_penerimaan' => $id_penerimaan,
            'id_user' => $this->session->userdata('id_user'),
            'id_supplier' => $this->input->post('id_supplier'),
            'tanggal_retur' => date('Y-m-d H:i:s'),
            'alasan_retur' => $this->input->post('alasan_retur'),
            'status' => 'Requested'
        ];

        $id_retur = $this->retur->insert($data_insert);

        if ($id_retur) {
            // Simpan detail barang
            $barang_ids = $this->input->post('id_barang');
            $id_gudangs = $this->input->post('id_gudang');
            $jumlah_returs = $this->input->post('jumlah_retur');
            $alasan_barangs = $this->input->post('alasan_barang');

            foreach ($barang_ids as $key => $id_barang) {
                if ($id_barang && $jumlah_returs[$key] > 0) {
                    $detail_data = [
                        'id_retur_beli' => $id_retur,
                        'id_barang' => $id_barang,
                        'id_gudang' => $id_gudangs[$key],
                        'jumlah_retur' => $jumlah_returs[$key],
                        'jumlah_disetujui' => 0,
                        'alasan_barang' => $alasan_barangs[$key]
                    ];
                    $this->retur->insert_detail($detail_data);
                }
            }

            $this->session->set_flashdata('success', 'Retur pembelian berhasil dibuat dengan nomor: ' . $no_retur);
            return redirect('daftar/retur_pembelian');
        } else {
            $this->session->set_flashdata('error', 'Gagal membuat retur pembelian!');
            return redirect('daftar/retur_pembelian/tambah');
        }
    }
    public function verifikasi($id_retur)
    {
        // Check permission
        if (!$this->check_permission('daftar/retur_pembelian', 'edit')) {
            $this->session->set_flashdata('error', 'Anda tidak memiliki izin untuk verifikasi retur pembelian!');
            return redirect('daftar/retur_pembelian');
        }

        $this->data['title'] = 'Verifikasi Retur Pembelian';
        $this->data['retur'] = $this->retur->get($id_retur);
        $this->data['detail'] = $this->retur->get_detail($id_retur);

        if (!$this->data['retur']) {
            show_404();
        }

        if ($this->data['retur']->status != 'Requested') {
            $this->session->set_flashdata('error', 'Retur pembelian dengan status ' . $this->data['retur']->status . ' tidak dapat diverifikasi!');
            return redirect('daftar/retur_pembelian');
        }

        $this->form_validation->set_rules('status', 'Status', 'required');

        if ($this->form_validation->run() == FALSE) {
            return $this->render_view('daftar/retur_pembelian/verifikasi', $this->data);
        }

        $status = $this->input->post('status');

        if ($status == 'Approved') {
            // Validasi stok sebelum approve
            $detail = $this->retur->get_detail($id_retur);
            $stok_cukup = true;

            foreach ($detail as $item) {
                // Check stock
                $stok = $this->retur->get_stok_barang($item->id_gudang, $item->id_barang);

                if (!$stok || $stok->jumlah < $item->jumlah_retur) {
                    $stok_cukup = false;
                    $this->session->set_flashdata('error', 'Stok tidak mencukup untuk barang: ' . $item->nama_barang . '. Stok tersedia: ' . ($stok ? $stok->jumlah : 0) . ', Diminta: ' . $item->jumlah_retur);
                    return redirect('daftar/retur_pembelian/verifikasi/' . $id_retur);
                }
            }

            if ($stok_cukup) {
                // Update status retur
                if ($this->retur->update_status($id_retur, $status)) {
                    // Update detail retur: set jumlah_disetujui = jumlah_retur
                    foreach ($detail as $item) {
                        $detail_data = [
                            'jumlah_disetujui' => $item->jumlah_retur
                        ];
                        $this->retur->update_detail($item->id_detail_retur_beli, $detail_data);
                    }

                    // Kurangi stok
                    $this->kurangi_stok($id_retur);

                    // Log status
                    $this->log_status_transaksi($id_retur, 'retur_pembelian', $status);

                    $this->session->set_flashdata('success', 'Retur pembelian berhasil dilanjutkan! Barang akan dikembalikan ke supplier.');
                    return redirect('daftar/retur_pembelian');
                }
            }
        } else {
            // Jika ditolak
            if ($this->retur->update_status($id_retur, $status)) {
                $this->log_status_transaksi($id_retur, 'retur_pembelian', $status);
                $this->session->set_flashdata('success', 'Retur pembelian berhasil dibatalkan.');
                return redirect('daftar/retur_pembelian');
            }
        }

        $this->session->set_flashdata('error', 'Gagal verifikasi retur pembelian!');
        return redirect('daftar/retur_pembelian/verifikasi/' . $id_retur);
    }
    public function hapus($id_retur)
    {
        // Check permission
        if (!$this->check_permission('daftar/retur_pembelian', 'delete')) {
            $this->session->set_flashdata('error', 'Anda tidak memiliki izin untuk menghapus retur pembelian!');
            return redirect('daftar/retur_pembelian');
        }

        $retur = $this->retur->get($id_retur);

        if (!$retur) {
            show_404();
        }

        if ($retur->status != 'Requested') {
            $this->session->set_flashdata('error', 'Retur pembelian dengan status ' . $retur->status . ' tidak dapat dihapus!');
            return redirect('daftar/retur_pembelian');
        }

        if ($this->retur->delete($id_retur)) {
            $this->session->set_flashdata('success', 'Retur pembelian berhasil dihapus');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus retur pembelian!');
        }

        return redirect('daftar/retur_pembelian');
    }

    public function detail($id_retur)
    {
        $this->data['title'] = 'Detail Retur Pembelian';
        $this->data['retur'] = $this->retur->get($id_retur);
        $this->data['detail'] = $this->retur->get_detail($id_retur);

        if (!$this->data['retur']) {
            show_404();
        }

        $this->render_view('daftar/retur_pembelian/detail');
    }

    public function get_detail_penerimaan()
    {
        $id_penerimaan = $this->input->post('id_penerimaan');
        $detail = $this->retur->get_detail_penerimaan($id_penerimaan);
        echo json_encode($detail);
    }

    private function generate_no_retur()
    {
        $prefix = 'RB-' . date('ymd');
        $this->db->like('no_retur_beli', $prefix, 'after');
        $this->db->order_by('no_retur_beli', 'DESC');
        $this->db->limit(1);
        $last = $this->db->get('retur_pembelian')->row();

        if ($last) {
            $last_number = substr($last->no_retur_beli, -4);
            $new_number = str_pad($last_number + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $new_number = '0001';
        }

        return $prefix . $new_number;
    }

    private function kurangi_stok($id_retur)
    {
        $retur = $this->retur->get($id_retur);
        $detail = $this->retur->get_detail($id_retur);

        foreach ($detail as $item) {
            // Get current stock
            $stok = $this->retur->get_stok_barang($item->id_gudang, $item->id_barang);

            if ($stok && $stok->jumlah >= $item->jumlah_retur) {
                // Update stock
                $new_stok = $stok->jumlah - $item->jumlah_retur;
                $this->retur->update_stok($item->id_gudang, $item->id_barang, $new_stok);

                // Log stock movement
                $log_data = [
                    'id_barang' => $item->id_barang,
                    'id_user' => $this->session->userdata('id_user'),
                    'id_perusahaan' => $this->session->userdata('id_perusahaan'),
                    'id_gudang' => $item->id_gudang,
                    'jenis' => 'retur_pembelian',
                    'jumlah' => $item->jumlah_retur,
                    'sisa_stok' => $new_stok,
                    'keterangan' => 'Retur pembelian ke supplier: ' . $retur->no_retur_beli,
                    'id_referensi' => $id_retur,
                    'tipe_referensi' => 'retur_pembelian',
                    'tanggal' => date('Y-m-d H:i:s')
                ];
                $this->retur->insert_log_stok($log_data);
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

        $this->retur->insert_log_status($log_data);
    }
}