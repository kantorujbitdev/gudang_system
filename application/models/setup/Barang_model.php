<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Barang_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'barang';
        $this->primary_key = 'id_barang';
        $this->fillable = array('id_perusahaan', 'id_kategori', 'nama_barang', 'gambar', 'sku', 'deskripsi', 'satuan', 'harga_jual', 'harga_beli_terakhir', 'status_aktif');
        $this->soft_delete = TRUE;
    }

    public function get_all()
    {
        $user_role = $this->session->userdata('id_role');
        $user_perusahaan = $this->session->userdata('id_perusahaan');

        $this->db->select('b.*, k.nama_kategori, p.nama_perusahaan');
        $this->db->from('barang b');
        $this->db->join('kategori k', 'b.id_kategori = k.id_kategori', 'left');
        $this->db->join('perusahaan p', 'b.id_perusahaan = p.id_perusahaan', 'left');
        $this->db->where('b.deleted_at IS NULL');

        // Filter berdasarkan role user
        if ($user_role != 1) { // Bukan Super Admin
            $this->db->where('b.id_perusahaan', $user_perusahaan);
        }

        $this->db->order_by('b.created_at', 'DESC');
        return $this->db->get()->result();
    }

    public function get_active()
    {
        $user_role = $this->session->userdata('id_role');
        $user_perusahaan = $this->session->userdata('id_perusahaan');

        $this->db->select('b.*, k.nama_kategori, p.nama_perusahaan');
        $this->db->from('barang b');
        $this->db->join('kategori k', 'b.id_kategori = k.id_kategori', 'left');
        $this->db->join('perusahaan p', 'b.id_perusahaan = p.id_perusahaan', 'left');
        $this->db->where('b.status_aktif', 1);
        $this->db->where('b.deleted_at IS NULL');

        // Filter berdasarkan role user
        if ($user_role != 1) { // Bukan Super Admin
            $this->db->where('b.id_perusahaan', $user_perusahaan);
        }

        $this->db->order_by('b.nama_barang', 'ASC');
        return $this->db->get()->result();
    }

    public function get($id_barang)
    {
        $user_role = $this->session->userdata('id_role');
        $user_perusahaan = $this->session->userdata('id_perusahaan');

        $this->db->select('b.*, k.nama_kategori, p.nama_perusahaan');
        $this->db->from('barang b');
        $this->db->join('kategori k', 'b.id_kategori = k.id_kategori', 'left');
        $this->db->join('perusahaan p', 'b.id_perusahaan = p.id_perusahaan', 'left');
        $this->db->where('b.id_barang', $id_barang);
        $this->db->where('b.deleted_at IS NULL');

        // Filter berdasarkan role user
        if ($user_role != 1) { // Bukan Super Admin
            $this->db->where('b.id_perusahaan', $user_perusahaan);
        }

        return $this->db->get()->row();
    }

    public function get_by_kategori($id_kategori)
    {
        $user_role = $this->session->userdata('id_role');
        $user_perusahaan = $this->session->userdata('id_perusahaan');

        $this->db->select('b.*, k.nama_kategori, p.nama_perusahaan');
        $this->db->from('barang b');
        $this->db->join('kategori k', 'b.id_kategori = k.id_kategori', 'left');
        $this->db->join('perusahaan p', 'b.id_perusahaan = p.id_perusahaan', 'left');
        $this->db->where('b.id_kategori', $id_kategori);
        $this->db->where('b.status_aktif', 1);
        $this->db->where('b.deleted_at IS NULL');

        // Filter berdasarkan role user
        if ($user_role != 1) { // Bukan Super Admin
            $this->db->where('b.id_perusahaan', $user_perusahaan);
        }

        $this->db->order_by('b.nama_barang', 'ASC');
        return $this->db->get()->result();
    }

    public function check_unique_sku($sku, $id_perusahaan, $id_barang = NULL)
    {
        $this->db->where('sku', $sku);
        $this->db->where('id_perusahaan', $id_perusahaan);
        $this->db->where('deleted_at IS NULL');
        if ($id_barang) {
            $this->db->where('id_barang !=', $id_barang);
        }
        return $this->db->get($this->table)->num_rows() == 0;
    }

    public function get_stok_by_barang($id_barang)
    {
        $this->db->select('sg.*, g.nama_gudang');
        $this->db->from('stok_gudang sg');
        $this->db->join('gudang g', 'sg.id_gudang = g.id_gudang', 'left');
        $this->db->where('sg.id_barang', $id_barang);
        $this->db->where('g.status_aktif', 1);
        $this->db->order_by('g.nama_gudang', 'ASC');

        $query = $this->db->get();
        return ($query && $query->num_rows() > 0) ? $query->result() : [];
    }

    public function get_total_stok($id_barang)
    {
        $this->db->select('SUM(jumlah) as total_stok');
        $this->db->from('stok_gudang');
        $this->db->where('id_barang', $id_barang);

        $query = $this->db->get();
        $result = $query->row();

        return $result ? $result->total_stok : 0;
    }

    public function get_by_gudang($id_gudang)
    {
        $this->db->select('b.*, k.nama_kategori, sg.jumlah, sg.reserved');
        $this->db->from('barang b');
        $this->db->join('stok_gudang sg', 'b.id_barang = sg.id_barang', 'inner');
        $this->db->join('kategori k', 'b.id_kategori = k.id_kategori', 'left');
        $this->db->where('sg.id_gudang', $id_gudang);
        $this->db->where('b.status_aktif', 1);
        $this->db->order_by('b.created_at', 'DESC');

        $query = $this->db->get();

        return ($query && $query->num_rows() > 0)
            ? $query->result()
            : [];
    }

    public function get_by_perusahaan($id_perusahaan)
    {
        $this->db->select('b.*, k.nama_kategori');
        $this->db->from('barang b');
        $this->db->join('kategori k', 'b.id_kategori = k.id_kategori');
        $this->db->where('b.id_perusahaan', $id_perusahaan);
        $this->db->where('b.deleted_at IS NULL');
        $this->db->where('b.status_aktif', 1);
        $this->db->order_by('b.created_at', 'DESC');
        return $this->db->get()->result();
    }

}