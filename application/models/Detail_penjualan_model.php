<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Detail_penjualan_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'detail_penjualan';
        $this->primary_key = 'id_detail';
        $this->fillable = array('id_penjualan', 'id_barang', 'id_gudang', 'jumlah');
        $this->timestamps = TRUE;
    }

    public function get_by_penjualan($id_penjualan)
    {
        $this->db->select('dp.*, b.nama_barang, b.sku, g.nama_gudang');
        $this->db->from('detail_penjualan dp');
        $this->db->join('barang b', 'dp.id_barang = b.id_barang', 'left');
        $this->db->join('gudang g', 'dp.id_gudang = g.id_gudang', 'left');
        $this->db->where('dp.id_penjualan', $id_penjualan);

        $query = $this->db->get();
        return ($query && $query->num_rows() > 0) ? $query->result() : [];
    }

    public function insert_batch($data)
    {
        return $this->db->insert_batch($this->table, $data);
    }

    public function delete_by_penjualan($id_penjualan)
    {
        $this->db->where('id_penjualan', $id_penjualan);
        return $this->db->delete($this->table);
    }
}