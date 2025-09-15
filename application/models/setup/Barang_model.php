<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Barang_model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->table = 'barang';
        $this->primary_key = 'id_barang';
        $this->fillable = array('id_perusahaan', 'id_kategori', 'nama_barang', 'gambar', 'sku', 'deskripsi', 'satuan', 'harga_jual', 'harga_beli_terakhir', 'aktif');
    }

    public function get_all()
    {
        $filter = get_company_filter();

        $this->db->select('b.*, k.nama_kategori, 
                           (SELECT SUM(jumlah) FROM stok_gudang WHERE id_barang = b.id_barang) as total_stok');
        $this->db->from('barang b');
        $this->db->join('kategori k', 'b.id_kategori = k.id_kategori', 'left');

        if ($filter) {
            $this->db->where($filter);
        }

        $this->db->order_by('b.nama_barang', 'ASC');

        return $this->db->get()->result();
    }

    public function get($id_barang)
    {
        $filter = get_company_filter();

        $this->db->where('id_barang', $id_barang);

        if ($filter) {
            $this->db->where($filter);
        }

        return $this->db->get($this->table)->row();
    }
}