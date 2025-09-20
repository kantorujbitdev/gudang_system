<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Retur_penjualan extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Aktifitas/Retur_penjualan_model', 'retur');
        $this->load->model('setup/Barang_model', 'barang');
        $this->load->model('setup/Gudang_model', 'gudang');
        $this->load->model('setup/Pelanggan_model', 'pelanggan');
        $this->load->helper(['form', 'url']);
        $this->load->library('form_validation');

        // Cek akses menu
        $this->check_menu_access('aktifitas/retur_penjualan');
    }

    public function index()
    {
        $this->data['title'] = 'Retur Penjualan';
        $this->data['retur'] = $this->retur->get_all();
        $this->data['can_create'] = $this->check_permission('aktifitas/retur_penjualan', 'create');
        $this->data['can_edit'] = $this->check_permission('aktifitas/retur_penjualan', 'edit');
        $this->data['can_delete'] = $this->check_permission('aktifitas/retur_penjualan', 'delete');

        $this->render_view('aktifitas/retur_penjualan/index');
    }

    public function tambah()
    {
        if (!$this->check_permission('aktifitas/retur_penjualan', 'create')) {
            $this->session->set_flashdata('error', 'Anda tidak memiliki izin untuk membuat retur penjualan!');
            return redirect('aktifitas/retur_penjualan');
        }

        $this->data['title'] = 'Tambah Retur Penjualan';
        $this->data['penjualan'] = $this->retur->get_penjualan();
        $this->data['gudang'] = $this->gudang->get_all();
        $this->data['barang'] = $this->barang->get_all();

        $this->form_validation->set_rules('id_penjualan', 'Penjualan', 'required');
        $this->form_validation->set_rules('tanggal_retur', 'Tanggal Retur', 'required');
        $this->form_validation->set_rules('alasan_retur', 'Alasan Retur', 'required');

        if ($this->form_validation->run() === FALSE) {
            return $this->render_view('aktifitas/retur_penjualan/form');
        }

        $no_retur = $this->generate_no_retur();
        $data_insert = [
            'no_retur' => $no_retur,
            'id_penjualan' => $this->input->post('id_penjualan'),
            'id_user' => $this->session->userdata('id_user'),
            'tanggal_retur' => $this->input->post('tanggal_retur') . ' ' . date('H:i:s'),
            'alasan_retur' => $this->input->post('alasan_retur'),
            'status' => 'Requested'
        ];

        $id_retur = $this->retur->insert($data_insert);

        if ($id_retur) {
            $barang_ids = $this->input->post('id_barang');
            $id_gudangs = $this->input->post('id_gudang');
            $jumlah_returs = $this->input->post('jumlah_retur');
            $alasan_barangs = $this->input->post('alasan_barang');

            foreach ($barang_ids as $key => $id_barang) {
                if ($id_barang && $jumlah_returs[$key] > 0) {
                    $detail_data = [
                        'id_retur' => $id_retur,
                        'id_barang' => $id_barang,
                        'id_gudang' => $id_gudangs[$key],
                        'jumlah_retur' => $jumlah_returs[$key],
                        'jumlah_disetujui' => 0,
                        'alasan_barang' => $alasan_barangs[$key]
                    ];
                    $this->retur->insert_detail($detail_data);
                }
            }

            $this->session->set_flashdata('success', 'Retur penjualan berhasil dibuat dengan nomor: ' . $no_retur);
        } else {
            $this->session->set_flashdata('error', 'Gagal membuat retur penjualan!');
        }
        redirect('aktifitas/retur_penjualan');
    }

    public function verifikasi($id_retur)
    {
        if (!$this->check_permission('aktifitas/retur_penjualan', 'edit')) {
            $this->session->set_flashdata('error', 'Anda tidak memiliki izin untuk verifikasi retur penjualan!');
            return redirect('aktifitas/retur_penjualan');
        }

        $this->data['title'] = 'Verifikasi Retur Penjualan';
        $this->data['retur'] = $this->retur->get($id_retur);
        $this->data['detail'] = $this->retur->get_detail($id_retur);
        $this->data['gudang'] = $this->gudang->get_all();

        if (!$this->data['retur']) {
            show_404();
        }

        if ($this->data['retur']->status != 'Requested') {
            $this->session->set_flashdata('error', 'Retur penjualan dengan status ' . $this->data['retur']->status . ' tidak dapat diverifikasi!');
            return redirect('aktifitas/retur_penjualan');
        }

        $this->form_validation->set_rules('status', 'Status', 'required');

        if ($this->form_validation->run() === FALSE) {
            return $this->render_view('aktifitas/retur_penjualan/verifikasi');
        }

        $status = $this->input->post('status');
        if ($this->retur->update_status($id_retur, $status)) {
            $detail_ids = $this->input->post('id_detail_retur');
            $jumlah_disetujuis = $this->input->post('jumlah_disetujui');

            foreach ($detail_ids as $key => $id_detail) {
                $this->retur->update_detail($id_detail, [
                    'jumlah_disetujui' => $jumlah_disetujuis[$key]
                ]);
            }

            if ($status == 'Approved') {
                $this->tambah_stok($id_retur);
            }

            $this->log_status_transaksi($id_retur, 'retur_penjualan', $status);
            $this->session->set_flashdata('success', 'Verifikasi retur penjualan berhasil, status: ' . $status);
        } else {
            $this->session->set_flashdata('error', 'Gagal verifikasi retur penjualan!');
        }
        redirect('aktifitas/retur_penjualan');
    }

    public function hapus($id_retur)
    {
        if (!$this->check_permission('aktifitas/retur_penjualan', 'delete')) {
            $this->session->set_flashdata('error', 'Anda tidak memiliki izin untuk menghapus retur penjualan!');
            return redirect('aktifitas/retur_penjualan');
        }

        $retur = $this->retur->get($id_retur);
        if (!$retur) {
            show_404();
        }

        if ($retur->status != 'Requested') {
            $this->session->set_flashdata('error', 'Retur penjualan dengan status ' . $retur->status . ' tidak dapat dihapus!');
            return redirect('aktifitas/retur_penjualan');
        }

        if ($this->retur->delete($id_retur)) {
            $this->session->set_flashdata('success', 'Retur penjualan berhasil dihapus.');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus retur penjualan!');
        }
        redirect('aktifitas/retur_penjualan');
    }

    public function detail($id_retur)
    {
        $this->data['title'] = 'Detail Retur Penjualan';
        $this->data['retur'] = $this->retur->get($id_retur);
        $this->data['detail'] = $this->retur->get_detail($id_retur);

        if (!$this->data['retur']) {
            show_404();
        }

        $this->render_view('aktifitas/retur_penjualan/detail');
    }

    public function get_detail_penjualan()
    {
        $id_penjualan = $this->input->post('id_penjualan');
        $detail = $this->retur->get_detail_penjualan($id_penjualan);
        echo json_encode($detail);
    }

    private function generate_no_retur()
    {
        $prefix = 'RJ-' . date('ymd');
        $this->db->like('no_retur', $prefix, 'after');
        $this->db->order_by('no_retur', 'DESC');
        $this->db->limit(1);
        $last = $this->db->get('retur_penjualan')->row();

        if ($last) {
            $last_number = substr($last->no_retur, -4);
            $new_number = str_pad($last_number + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $new_number = '0001';
        }

        return $prefix . $new_number;
    }

    private function tambah_stok($id_retur)
    {
        $retur = $this->retur->get($id_retur);
        $detail = $this->retur->get_detail($id_retur);

        foreach ($detail as $item) {
            if ($item->jumlah_disetujui > 0) {
                $stok = $this->retur->get_stok_barang($item->id_gudang, $item->id_barang);

                if ($stok) {
                    $new_stok = $stok->jumlah + $item->jumlah_disetujui;
                    $this->retur->update_stok($item->id_gudang, $item->id_barang, $new_stok);

                    $log_data = [
                        'id_barang' => $item->id_barang,
                        'id_user' => $this->session->userdata('id_user'),
                        'id_perusahaan' => $this->session->userdata('id_perusahaan'),
                        'id_gudang' => $item->id_gudang,
                        'jenis' => 'retur_penjualan',
                        'jumlah' => $item->jumlah_disetujui,
                        'sisa_stok' => $new_stok,
                        'keterangan' => 'Retur penjualan ' . $retur->no_retur,
                        'id_referensi' => $id_retur,
                        'tipe_referensi' => 'retur_penjualan'
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
