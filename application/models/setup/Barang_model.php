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
        $this->db->order_by('created_at', 'DESC');
        return $this->db->get($this->table)->result();
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
    public function get_by_kategori($id_kategori)
    {
        $this->db->select('b.*, g.nama_gudang');
        $this->db->from('barang b');
        $this->db->join('gudang g', 'b.id_gudang = g.id_gudang', 'left');
        $this->db->where('b.id_kategori', $id_kategori);
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