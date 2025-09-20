<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Stok_awal extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Pengaturan/Stok_awal_model', 'stok_awal');
        $this->load->model('setup/Barang_model', 'barang');
        $this->load->model('setup/Gudang_model', 'gudang');
        $this->load->helper('form');
        $this->load->library('form_validation');

        // Cek akses menu
        $this->check_menu_access('pengaturan/stok_awal');
    }

    public function index()
    {
        $this->data['title'] = 'Stok Awal';
        $this->data['stok_awal'] = $this->stok_awal->get_all();
        $this->data['can_create'] = $this->check_permission('pengaturan/stok_awal', 'create');
        $this->data['can_edit'] = $this->check_permission('pengaturan/stok_awal', 'edit');
        $this->data['can_delete'] = $this->check_permission('pengaturan/stok_awal', 'delete');

        $this->render_view('pengaturan/stok_awal/index');
    }

    public function tambah()
    {
        if (!$this->check_permission('pengaturan/stok_awal', 'create')) {
            $this->session->set_flashdata('error', 'Anda tidak memiliki izin untuk menambah stok awal!');
            return redirect('pengaturan/stok_awal');
        }

        $this->data['title'] = 'Tambah Stok Awal';
        $this->data['barang'] = $this->barang->get_all();
        $this->data['gudang'] = $this->gudang->get_all();

        $this->form_validation->set_rules('id_barang', 'Barang', 'required');
        $this->form_validation->set_rules('id_gudang', 'Gudang', 'required');
        $this->form_validation->set_rules('qty_awal', 'Jumlah Stok', 'required|numeric|greater_than_equal_to[0]');

        if ($this->form_validation->run() === FALSE) {
            return $this->render_view('pengaturan/stok_awal/form');
        }

        $id_barang = $this->input->post('id_barang');
        $id_gudang = $this->input->post('id_gudang');
        $qty_awal = $this->input->post('qty_awal');

        // Cek stok sudah ada atau belum
        if ($this->stok_awal->get_by_barang_gudang($id_barang, $id_gudang)) {
            $this->session->set_flashdata('error', 'Stok awal untuk barang & gudang ini sudah ada!');
            return redirect('pengaturan/stok_awal/tambah');
        }

        $data_insert = [
            'id_barang' => $id_barang,
            'id_gudang' => $id_gudang,
            'id_perusahaan' => $this->session->userdata('id_perusahaan'),
            'qty_awal' => $qty_awal,
            'keterangan' => $this->input->post('keterangan'),
            'created_by' => $this->session->userdata('id_user'),
            'created_at' => date('Y-m-d H:i:s')
        ];

        if ($this->stok_awal->insert($data_insert)) {
            $this->update_stok_gudang($id_barang, $id_gudang, $qty_awal);
            $this->session->set_flashdata('success', 'Stok awal berhasil ditambahkan!');
            return redirect('pengaturan/stok_awal');
        } else {
            $this->session->set_flashdata('error', 'Gagal menambahkan stok awal!');
            return redirect('pengaturan/stok_awal/tambah');
        }
    }

    public function edit($id_stok_awal)
    {
        if (!$this->check_permission('pengaturan/stok_awal', 'edit')) {
            $this->session->set_flashdata('error', 'Anda tidak memiliki izin untuk mengubah stok awal!');
            return redirect('pengaturan/stok_awal');
        }

        $this->data['title'] = 'Edit Stok Awal';
        $this->data['stok_awal'] = $this->stok_awal->get($id_stok_awal);
        $this->data['barang'] = $this->barang->get_all();
        $this->data['gudang'] = $this->gudang->get_all();

        if (!$this->data['stok_awal']) {
            show_404();
        }

        $this->form_validation->set_rules('id_barang', 'Barang', 'required');
        $this->form_validation->set_rules('id_gudang', 'Gudang', 'required');
        $this->form_validation->set_rules('qty_awal', 'Jumlah Stok', 'required|numeric|greater_than_equal_to[0]');

        if ($this->form_validation->run() === FALSE) {
            return $this->render_view('pengaturan/stok_awal/form');
        }

        $data_update = [
            'id_barang' => $this->input->post('id_barang'),
            'id_gudang' => $this->input->post('id_gudang'),
            'qty_awal' => $this->input->post('qty_awal'),
            'keterangan' => $this->input->post('keterangan')
        ];

        if ($this->stok_awal->update($id_stok_awal, $data_update)) {
            $this->update_stok_gudang(
                $this->input->post('id_barang'),
                $this->input->post('id_gudang'),
                $this->input->post('qty_awal')
            );
            $this->session->set_flashdata('success', 'Stok awal berhasil diperbarui!');
            return redirect('pengaturan/stok_awal');
        } else {
            $this->session->set_flashdata('error', 'Gagal memperbarui stok awal!');
            return redirect('pengaturan/stok_awal/edit/' . $id_stok_awal);
        }
    }

    public function hapus($id_stok_awal)
    {
        if (!$this->check_permission('pengaturan/stok_awal', 'delete')) {
            $this->session->set_flashdata('error', 'Anda tidak memiliki izin untuk menghapus stok awal!');
            return redirect('pengaturan/stok_awal');
        }

        $stok_awal = $this->stok_awal->get($id_stok_awal);
        if (!$stok_awal) {
            show_404();
        }

        if ($this->stok_awal->delete($id_stok_awal)) {
            $this->update_stok_gudang($stok_awal->id_barang, $stok_awal->id_gudang, 0);
            $this->session->set_flashdata('success', 'Stok awal berhasil dihapus!');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus stok awal!');
        }

        return redirect('pengaturan/stok_awal');
    }

    // import() biarin dulu (bisa dimodif flashdata juga kalau mau)

    private function update_stok_gudang($id_barang, $id_gudang, $jumlah)
    {
        $stok = $this->stok_awal->get_stok_gudang($id_barang, $id_gudang);

        if ($stok) {
            $data = [
                'jumlah' => $jumlah,
                'updated_at' => date('Y-m-d H:i:s')
            ];
            $this->stok_awal->update_stok_gudang($id_barang, $id_gudang, $data);
        } else {
            $data = [
                'id_perusahaan' => $this->session->userdata('id_perusahaan'),
                'id_barang' => $id_barang,
                'id_gudang' => $id_gudang,
                'jumlah' => $jumlah,
                'reserved' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            $this->stok_awal->insert_stok_gudang($data);
        }

        $log_data = [
            'id_barang' => $id_barang,
            'id_user' => $this->session->userdata('id_user'),
            'id_perusahaan' => $this->session->userdata('id_perusahaan'),
            'id_gudang' => $id_gudang,
            'jenis' => 'penyesuaian',
            'jumlah' => $jumlah,
            'sisa_stok' => $jumlah,
            'keterangan' => 'Penyesuaian stok awal',
            'tanggal' => date('Y-m-d H:i:s')
        ];
        $this->stok_awal->insert_log_stok($log_data);
    }
}
