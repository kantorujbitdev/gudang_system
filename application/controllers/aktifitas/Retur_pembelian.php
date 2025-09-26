<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Retur_pembelian extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('aktifitas/Retur_pembelian_model', 'retur');
        $this->load->model('setup/Barang_model', 'barang');
        $this->load->model('setup/Gudang_model', 'gudang');
        $this->load->model('setup/Supplier_model', 'supplier');
        $this->load->helper('form');
        $this->load->library('form_validation');

        // Cek akses menu
        $this->check_menu_access('aktifitas/retur_pembelian');
    }

    public function index()
    {
        $this->data['title'] = 'Retur Pembelian';
        $this->data['retur'] = $this->retur->get_all();
        $this->data['can_create'] = $this->check_permission('aktifitas/retur_pembelian', 'create');
        $this->data['can_edit'] = $this->check_permission('aktifitas/retur_pembelian', 'edit');
        $this->data['can_delete'] = $this->check_permission('aktifitas/retur_pembelian', 'delete');

        $this->render_view('aktifitas/retur_pembelian/index');
    }

    public function tambah()
    {
        // Check permission
        if (!$this->check_permission('aktifitas/retur_pembelian', 'create')) {
            $this->session->set_flashdata('error', 'Anda tidak memiliki izin untuk membuat retur pembelian!');
            return redirect('aktifitas/retur_pembelian');
        }

        $this->data['title'] = 'Tambah Retur Pembelian';
        $this->data['pembelian'] = $this->retur->get_pembelian();
        $this->data['supplier'] = $this->supplier->get_all();
        $this->data['gudang'] = $this->gudang->get_all();
        $this->data['barang'] = $this->barang->get_all();

        $this->form_validation->set_rules('id_supplier', 'Supplier', 'required');
        $this->form_validation->set_rules('tanggal_retur', 'Tanggal Retur', 'required');
        $this->form_validation->set_rules('alasan_retur', 'Alasan Retur', 'required');

        if ($this->form_validation->run() == FALSE) {
            return $this->render_view('aktifitas/retur_pembelian/form');
        }

        // Generate nomor retur
        $no_retur = $this->generate_no_retur();

        $data_insert = [
            'no_retur_beli' => $no_retur,
            'id_pembelian' => $this->input->post('id_pembelian'),
            'id_user' => $this->session->userdata('id_user'),
            'id_supplier' => $this->input->post('id_supplier'),
            'tanggal_retur' => $this->input->post('tanggal_retur') . ' ' . date('H:i:s'),
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
            return redirect('aktifitas/retur_pembelian');
        } else {
            $this->session->set_flashdata('error', 'Gagal membuat retur pembelian!');
            return redirect('aktifitas/retur_pembelian/tambah');
        }
    }

    public function verifikasi($id_retur)
    {
        // Check permission
        if (!$this->check_permission('aktifitas/retur_pembelian', 'edit')) {
            $this->session->set_flashdata('error', 'Anda tidak memiliki izin untuk verifikasi retur pembelian!');
            return redirect('aktifitas/retur_pembelian');
        }

        $this->data['title'] = 'Verifikasi Retur Pembelian';
        $this->data['retur'] = $this->retur->get($id_retur);
        $this->data['detail'] = $this->retur->get_detail($id_retur);

        if (!$this->data['retur']) {
            show_404();
        }

        if ($this->data['retur']->status != 'Requested') {
            $this->session->set_flashdata('error', 'Retur pembelian dengan status ' . $this->data['retur']->status . ' tidak dapat diverifikasi!');
            return redirect('aktifitas/retur_pembelian');
        }

        $this->form_validation->set_rules('status', 'Status', 'required');

        if ($this->form_validation->run() == FALSE) {
            return $this->render_view('aktifitas/retur_pembelian/verifikasi');
        }

        $status = $this->input->post('status');

        if ($this->retur->update_status($id_retur, $status)) {
            $detail_ids = $this->input->post('id_detail_retur');
            $jumlah_disetujuis = $this->input->post('jumlah_disetujui');

            foreach ($detail_ids as $key => $id_detail) {
                $detail_data = [
                    'jumlah_disetujui' => $jumlah_disetujuis[$key]
                ];
                $this->retur->update_detail($id_detail, $detail_data);
            }

            if ($status == 'Approved') {
                $this->kurangi_stok($id_retur);
            }

            $this->log_status_transaksi($id_retur, 'retur_pembelian', $status);

            $this->session->set_flashdata('success', 'Verifikasi retur pembelian berhasil, status: ' . $status);
            return redirect('aktifitas/retur_pembelian');
        } else {
            $this->session->set_flashdata('error', 'Gagal verifikasi retur pembelian!');
            return redirect('aktifitas/retur_pembelian/verifikasi/' . $id_retur);
        }
    }

    public function hapus($id_retur)
    {
        // Check permission
        if (!$this->check_permission('aktifitas/retur_pembelian', 'delete')) {
            $this->session->set_flashdata('error', 'Anda tidak memiliki izin untuk menghapus retur pembelian!');
            return redirect('aktifitas/retur_pembelian');
        }

        $retur = $this->retur->get($id_retur);

        if (!$retur) {
            show_404();
        }

        if ($retur->status != 'Requested') {
            $this->session->set_flashdata('error', 'Retur pembelian dengan status ' . $retur->status . ' tidak dapat dihapus!');
            return redirect('aktifitas/retur_pembelian');
        }

        if ($this->retur->delete($id_retur)) {
            $this->session->set_flashdata('success', 'Retur pembelian berhasil dihapus');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus retur pembelian!');
        }

        return redirect('aktifitas/retur_pembelian');
    }

    public function detail($id_retur)
    {
        $this->data['title'] = 'Detail Retur Pembelian';
        $this->data['retur'] = $this->retur->get($id_retur);
        $this->data['detail'] = $this->retur->get_detail($id_retur);

        if (!$this->data['retur']) {
            show_404();
        }

        $this->render_view('aktifitas/retur_pembelian/detail');
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
            if ($item->jumlah_disetujui > 0) {
                $stok = $this->retur->get_stok_barang($item->id_gudang, $item->id_barang);

                if ($stok && $stok->jumlah >= $item->jumlah_disetujui) {
                    $new_stok = $stok->jumlah - $item->jumlah_disetujui;
                    $this->retur->update_stok($item->id_gudang, $item->id_barang, $new_stok);

                    $log_data = [
                        'id_barang' => $item->id_barang,
                        'id_user' => $this->session->userdata('id_user'),
                        'id_perusahaan' => $this->session->userdata('id_perusahaan'),
                        'id_gudang' => $item->id_gudang,
                        'jenis' => 'retur_pembelian',
                        'jumlah' => $item->jumlah_disetujui,
                        'sisa_stok' => $new_stok,
                        'keterangan' => 'Retur pembelian ' . $retur->no_retur_beli,
                        'id_referensi' => $id_retur,
                        'tipe_referensi' => 'retur_pembelian'
                    ];
                    $this->retur->insert_log_stok($log_data);
                }
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

        $this->retur->insert_log_status($log_data);
    }
}
