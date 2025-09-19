<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Penerimaan extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Aktifitas/Penerimaan_model', 'penerimaan');
        $this->load->model('setup/Barang_model', 'barang');
        $this->load->model('setup/Gudang_model', 'gudang');
        $this->load->model('setup/Supplier_model', 'supplier');
        $this->load->helper('form');
        $this->load->library('form_validation');

        // Cek akses menu
        $this->check_menu_access('aktifitas/penerimaan');
    }

    public function index()
    {
        $this->data['title'] = 'Penerimaan Barang';
        $this->data['penerimaan'] = $this->penerimaan->get_all();
        $this->data['can_create'] = $this->check_permission('aktifitas/penerimaan', 'create');
        $this->data['can_edit'] = $this->check_permission('aktifitas/penerimaan', 'edit');
        $this->data['can_delete'] = $this->check_permission('aktifitas/penerimaan', 'delete');

        $this->render_view('aktifitas/penerimaan/index');
    }

    public function tambah()
    {
        // Check permission
        if (!$this->check_permission('aktifitas/penerimaan', 'create')) {
            $this->data['error'] = 'Anda tidak memiliki izin untuk membuat penerimaan barang!';
            $this->render_view('aktifitas/penerimaan');
        }

        $this->data['title'] = 'Tambah Penerimaan Barang';
        $this->data['gudang'] = $this->gudang->get_all();
        $this->data['supplier'] = $this->supplier->get_all();
        $this->data['barang'] = $this->barang->get_all();

        $this->form_validation->set_rules('id_gudang', 'Gudang', 'required');
        $this->form_validation->set_rules('id_supplier', 'Supplier', 'required');
        $this->form_validation->set_rules('tanggal_penerimaan', 'Tanggal Penerimaan', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->render_view('aktifitas/penerimaan/form');
        } else {
            // Generate nomor penerimaan
            $no_penerimaan = $this->generate_no_penerimaan();

            $data_insert = [
                'no_penerimaan' => $no_penerimaan,
                'id_user' => $this->session->userdata('id_user'),
                'id_gudang' => $this->input->post('id_gudang'),
                'id_supplier' => $this->input->post('id_supplier'),
                'tanggal_penerimaan' => $this->input->post('tanggal_penerimaan') . ' ' . date('H:i:s'),
                'keterangan' => $this->input->post('keterangan'),
                'status' => 'Draft'
            ];

            $id_penerimaan = $this->penerimaan->insert($data_insert);

            if ($id_penerimaan) {
                // Simpan detail barang
                $barang_ids = $this->input->post('id_barang');
                $jumlah_dipesans = $this->input->post('jumlah_dipesan');
                $jumlah_diterimas = $this->input->post('jumlah_diterima');
                $keterangans = $this->input->post('keterangan_barang');

                foreach ($barang_ids as $key => $id_barang) {
                    if ($id_barang && $jumlah_diterimas[$key] > 0) {
                        $detail_data = [
                            'id_penerimaan' => $id_penerimaan,
                            'id_barang' => $id_barang,
                            'jumlah_dipesan' => $jumlah_dipesans[$key] ?: 0,
                            'jumlah_diterima' => $jumlah_diterimas[$key],
                            'id_gudang' => $this->input->post('id_gudang'),
                            'keterangan' => $keterangans[$key]
                        ];
                        $this->penerimaan->insert_detail($detail_data);
                    }
                }

                $this->data['success'] = 'Penerimaan barang berhasil dibuat dengan nomor: ' . $no_penerimaan;
                $this->render_view('aktifitas/penerimaan');
            } else {
                $this->data['error'] = 'Gagal membuat penerimaan barang!';
                $this->render_view('aktifitas/penerimaan/form');
            }
        }
    }

    public function edit($id_penerimaan)
    {
        // Check permission
        if (!$this->check_permission('aktifitas/penerimaan', 'edit')) {
            $this->data['error'] = 'Anda tidak memiliki izin untuk mengubah penerimaan barang!';
            $this->render_view('aktifitas/penerimaan');
        }

        $this->data['title'] = 'Edit Penerimaan Barang';
        $this->data['penerimaan'] = $this->penerimaan->get($id_penerimaan);
        $this->data['detail'] = $this->penerimaan->get_detail($id_penerimaan);
        $this->data['gudang'] = $this->gudang->get_all();
        $this->data['supplier'] = $this->supplier->get_all();
        $this->data['barang'] = $this->barang->get_all();

        if (!$this->data['penerimaan']) {
            show_404();
        }

        // Cek status, hanya draft yang bisa diedit
        if ($this->data['penerimaan']->status != 'Draft') {
            $this->data['error'] = 'Penerimaan barang dengan status ' . $this->data['penerimaan']->status . ' tidak dapat diubah!';
            $this->render_view('aktifitas/penerimaan');
        }

        $this->form_validation->set_rules('id_gudang', 'Gudang', 'required');
        $this->form_validation->set_rules('id_supplier', 'Supplier', 'required');
        $this->form_validation->set_rules('tanggal_penerimaan', 'Tanggal Penerimaan', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->render_view('aktifitas/penerimaan/form');
        } else {
            $data_update = [
                'id_gudang' => $this->input->post('id_gudang'),
                'id_supplier' => $this->input->post('id_supplier'),
                'tanggal_penerimaan' => $this->input->post('tanggal_penerimaan') . ' ' . date('H:i:s'),
                'keterangan' => $this->input->post('keterangan')
            ];

            if ($this->penerimaan->update($id_penerimaan, $data_update)) {
                // Hapus detail lama
                $this->penerimaan->delete_detail($id_penerimaan);

                // Simpan detail baru
                $barang_ids = $this->input->post('id_barang');
                $jumlah_dipesans = $this->input->post('jumlah_dipesan');
                $jumlah_diterimas = $this->input->post('jumlah_diterima');
                $keterangans = $this->input->post('keterangan_barang');

                foreach ($barang_ids as $key => $id_barang) {
                    if ($id_barang && $jumlah_diterimas[$key] > 0) {
                        $detail_data = [
                            'id_penerimaan' => $id_penerimaan,
                            'id_barang' => $id_barang,
                            'jumlah_dipesan' => $jumlah_dipesans[$key] ?: 0,
                            'jumlah_diterima' => $jumlah_diterimas[$key],
                            'id_gudang' => $this->input->post('id_gudang'),
                            'keterangan' => $keterangans[$key]
                        ];
                        $this->penerimaan->insert_detail($detail_data);
                    }
                }

                $this->data['success'] = 'Penerimaan barang berhasil diperbarui';
                $this->render_view('aktifitas/penerimaan');
            } else {
                $this->data['error'] = 'Gagal memperbarui penerimaan barang!';
                $this->render_view('aktifitas/penerimaan/form');
            }
        }
    }

    public function hapus($id_penerimaan)
    {
        // Check permission
        if (!$this->check_permission('aktifitas/penerimaan', 'delete')) {
            $this->data['error'] = 'Anda tidak memiliki izin untuk menghapus penerimaan barang!';
            $this->render_view('aktifitas/penerimaan');
        }

        $penerimaan = $this->penerimaan->get($id_penerimaan);

        if (!$penerimaan) {
            show_404();
        }

        // Cek status, hanya draft yang bisa dihapus
        if ($penerimaan->status != 'Draft') {
            $this->data['error'] = 'Penerimaan barang dengan status ' . $penerimaan->status . ' tidak dapat dihapus!';
            $this->render_view('aktifitas/penerimaan');
        }

        if ($this->penerimaan->delete($id_penerimaan)) {
            $this->data['success'] = 'Penerimaan barang berhasil dihapus';
        } else {
            $this->data['error'] = 'Gagal menghapus penerimaan barang!';
        }
        $this->render_view('aktifitas/penerimaan');
    }

    public function konfirmasi($id_penerimaan, $status)
    {
        // Check permission
        if (!$this->check_permission('aktifitas/penerimaan', 'edit')) {
            $this->data['error'] = 'Anda tidak memiliki izin untuk mengkonfirmasi penerimaan barang!';
            $this->render_view('aktifitas/penerimaan');
        }

        $penerimaan = $this->penerimaan->get($id_penerimaan);

        if (!$penerimaan) {
            show_404();
        }

        // Validasi perubahan status
        $valid_status = ['Received', 'Completed', 'Cancelled'];
        if (!in_array($status, $valid_status)) {
            $this->data['error'] = 'Status tidak valid!';
            $this->render_view('aktifitas/penerimaan');
        }

        // Cek alur status
        $current_status = $penerimaan->status;
        $valid_transition = false;

        switch ($current_status) {
            case 'Draft':
                if (in_array($status, ['Received', 'Cancelled'])) {
                    $valid_transition = true;
                }
                break;
            case 'Received':
                if (in_array($status, ['Completed', 'Cancelled'])) {
                    $valid_transition = true;
                }
                break;
        }

        if (!$valid_transition) {
            $this->data['error'] = 'Perubahan status dari ' . $current_status . ' ke ' . $status . ' tidak diizinkan!';
            $this->render_view('aktifitas/penerimaan');
        }

        // Update status
        if ($this->penerimaan->update_status($id_penerimaan, $status)) {
            // Jika status received, tambah stok
            if ($status == 'Received' && $current_status != 'Received') {
                $this->tambah_stok($id_penerimaan);
            }

            // Jika status cancelled, kurangi stok jika sudah received
            if ($status == 'Cancelled' && $current_status == 'Received') {
                $this->kurangi_stok($id_penerimaan);
            }

            // Log status transaksi
            $this->log_status_transaksi($id_penerimaan, 'penerimaan', $status);

            $this->data['success'] = 'Status penerimaan barang berhasil diubah menjadi ' . $status;
        } else {
            $this->data['error'] = 'Gagal mengubah status penerimaan barang!';
        }

        $this->render_view('aktifitas/penerimaan');
    }

    public function detail($id_penerimaan)
    {
        $this->data['title'] = 'Detail Penerimaan Barang';
        $this->data['penerimaan'] = $this->penerimaan->get($id_penerimaan);
        $this->data['detail'] = $this->penerimaan->get_detail($id_penerimaan);

        if (!$this->data['penerimaan']) {
            show_404();
        }

        $this->render_view('aktifitas/penerimaan/detail');
    }

    private function generate_no_penerimaan()
    {
        $prefix = 'PBR-' . date('ymd');
        $this->db->like('no_penerimaan', $prefix, 'after');
        $this->db->order_by('no_penerimaan', 'DESC');
        $this->db->limit(1);
        $last = $this->db->get('penerimaan_barang')->row();

        if ($last) {
            $last_number = substr($last->no_penerimaan, -4);
            $new_number = str_pad($last_number + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $new_number = '0001';
        }

        return $prefix . $new_number;
    }

    private function tambah_stok($id_penerimaan)
    {
        $penerimaan = $this->penerimaan->get($id_penerimaan);
        $detail = $this->penerimaan->get_detail($id_penerimaan);

        foreach ($detail as $item) {
            // Get current stock
            $stok = $this->penerimaan->get_stok_barang($penerimaan->id_gudang, $item->id_barang);

            if ($stok) {
                // Update stock
                $new_stok = $stok->jumlah + $item->jumlah_diterima;
                $this->penerimaan->update_stok($penerimaan->id_gudang, $item->id_barang, $new_stok);

                // Log stock movement
                $log_data = [
                    'id_barang' => $item->id_barang,
                    'id_user' => $this->session->userdata('id_user'),
                    'id_perusahaan' => $this->session->userdata('id_perusahaan'),
                    'id_gudang' => $penerimaan->id_gudang,
                    'jenis' => 'masuk',
                    'jumlah' => $item->jumlah_diterima,
                    'sisa_stok' => $new_stok,
                    'keterangan' => 'Penerimaan barang ' . $penerimaan->no_penerimaan,
                    'id_referensi' => $id_penerimaan,
                    'tipe_referensi' => 'penerimaan'
                ];
                $this->penerimaan->insert_log_stok($log_data);
            }
        }
    }

    private function kurangi_stok($id_penerimaan)
    {
        $penerimaan = $this->penerimaan->get($id_penerimaan);
        $detail = $this->penerimaan->get_detail($id_penerimaan);

        foreach ($detail as $item) {
            // Get current stock
            $stok = $this->penerimaan->get_stok_barang($penerimaan->id_gudang, $item->id_barang);

            if ($stok && $stok->jumlah >= $item->jumlah_diterima) {
                // Update stock
                $new_stok = $stok->jumlah - $item->jumlah_diterima;
                $this->penerimaan->update_stok($penerimaan->id_gudang, $item->id_barang, $new_stok);

                // Log stock movement
                $log_data = [
                    'id_barang' => $item->id_barang,
                    'id_user' => $this->session->userdata('id_user'),
                    'id_perusahaan' => $this->session->userdata('id_perusahaan'),
                    'id_gudang' => $penerimaan->id_gudang,
                    'jenis' => 'penyesuaian',
                    'jumlah' => $item->jumlah_diterima,
                    'sisa_stok' => $new_stok,
                    'keterangan' => 'Pembatalan penerimaan barang ' . $penerimaan->no_penerimaan,
                    'id_referensi' => $id_penerimaan,
                    'tipe_referensi' => 'penerimaan'
                ];
                $this->penerimaan->insert_log_stok($log_data);
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

        $this->penerimaan->insert_log_status($log_data);
    }
}