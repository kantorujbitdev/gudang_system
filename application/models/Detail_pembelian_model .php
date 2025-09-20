<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Detail_pembelian_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'detail_pembelian';
        $this->primary_key = 'id_detail';
        $this->fillable = array('id_pembelian', 'id_barang', 'jumlah');
        $this->timestamps = TRUE;
    }

    public function get_by_pembelian($id_pembelian)
    {
        $this->db->select('dp.*, b.nama_barang, b.sku');
        $this->db->from('detail_pembelian dp');
        $this->db->join('barang b', 'dp.id_barang = b.id_barang', 'left');
        $this->db->where('dp.id_pembelian', $id_pembelian);

        $query = $this->db->get();
        return ($query && $query->num_rows() > 0) ? $query->result() : [];
    }

    public function insert_batch($data)
    {
        return $this->db->insert_batch($this->table, $data);
    }

    public function delete_by_pembelian($id_pembelian)
    {
        $this->db->where('id_pembelian', $id_pembelian);
        return $this->db->delete($this->table);
    }
}