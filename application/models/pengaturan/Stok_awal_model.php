<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Stok_awal_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_all()
    {
        $id_perusahaan = $this->session->userdata('id_perusahaan');

        $this->db->select('sa.*, b.nama_barang, b.sku, g.nama_gudang, u.nama as created_by');
        $this->db->from('stok_awal sa');
        $this->db->join('barang b', 'sa.id_barang = b.id_barang');
        $this->db->join('gudang g', 'sa.id_gudang = g.id_gudang');
        $this->db->join('user u', 'sa.created_by = u.id_user');
        $this->db->where('sa.id_perusahaan', $id_perusahaan);
        $this->db->order_by('sa.created_at', 'DESC');
        return $this->db->get()->result();
    }

    public function get($id_stok_awal)
    {
        $this->db->select('sa.*, b.nama_barang, b.sku, g.nama_gudang');
        $this->db->from('stok_awal sa');
        $this->db->join('barang b', 'sa.id_barang = b.id_barang');
        $this->db->join('gudang g', 'sa.id_gudang = g.id_gudang');
        $this->db->where('sa.id_stok_awal', $id_stok_awal);
        return $this->db->get()->row();
    }

    public function get_by_barang_gudang($id_barang, $id_gudang)
    {
        $this->db->where('id_barang', $id_barang);
        $this->db->where('id_gudang', $id_gudang);
        $this->db->where('id_perusahaan', $this->session->userdata('id_perusahaan'));
        return $this->db->get('stok_awal')->row();
    }

    public function insert($data)
    {
        $this->db->insert('stok_awal', $data);
        return $this->db->insert_id();
    }

    public function update($id_stok_awal, $data)
    {
        $this->db->where('id_stok_awal', $id_stok_awal);
        return $this->db->update('stok_awal', $data);
    }

    public function delete($id_stok_awal)
    {
        $this->db->where('id_stok_awal', $id_stok_awal);
        return $this->db->delete('stok_awal');
    }

    public function get_stok_gudang($id_barang, $id_gudang)
    {
        $this->db->where('id_barang', $id_barang);
        $this->db->where('id_gudang', $id_gudang);
        return $this->db->get('stok_gudang')->row();
    }

    public function update_stok_gudang($id_barang, $id_gudang, $data)
    {
        $this->db->where('id_barang', $id_barang);
        $this->db->where('id_gudang', $id_gudang);
        return $this->db->update('stok_gudang', $data);
    }

    public function insert_stok_gudang($data)
    {
        $this->db->insert('stok_gudang', $data);
        return $this->db->insert_id();
    }

    public function insert_log_stok($data)
    {
        $this->db->insert('log_stok', $data);
        return $this->db->insert_id();
    }
}