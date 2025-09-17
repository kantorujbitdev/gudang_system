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