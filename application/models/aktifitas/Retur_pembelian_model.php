<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Retur_pembelian_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_all()
    {
        $id_perusahaan = $this->session->userdata('id_perusahaan');

        $this->db->select('rp.*, u.nama as user_nama, p.no_pembelian, s.nama_supplier');
        $this->db->from('retur_pembelian rp');
        $this->db->join('user u', 'rp.id_user = u.id_user');
        $this->db->join('pembelian p', 'rp.id_pembelian = p.id_pembelian', 'left');
        $this->db->join('supplier s', 'rp.id_supplier = s.id_supplier');
        $this->db->where('rp.id_perusahaan', $id_perusahaan);
        $this->db->order_by('rp.created_at', 'DESC');
        return $this->db->get()->result();
    }

    public function get($id_retur)
    {
        $this->db->select('rp.*, u.nama as user_nama, p.no_pembelian, s.nama_supplier');
        $this->db->from('retur_pembelian rp');
        $this->db->join('user u', 'rp.id_user = u.id_user');
        $this->db->join('pembelian p', 'rp.id_pembelian = p.id_pembelian', 'left');
        $this->db->join('supplier s', 'rp.id_supplier = s.id_supplier');
        $this->db->where('rp.id_retur_beli', $id_retur);
        return $this->db->get()->row();
    }

    public function get_detail($id_retur)
    {
        $this->db->select('dr.*, b.nama_barang, b.satuan, g.nama_gudang');
        $this->db->from('detail_retur_pembelian dr');
        $this->db->join('barang b', 'dr.id_barang = b.id_barang');
        $this->db->join('gudang g', 'dr.id_gudang = g.id_gudang');
        $this->db->where('dr.id_retur_beli', $id_retur);
        return $this->db->get()->result();
    }

    public function get_pembelian()
    {
        $id_perusahaan = $this->session->userdata('id_perusahaan');

        $this->db->select('p.id_pembelian, p.no_pembelian, s.nama_supplier');
        $this->db->from('pembelian p');
        $this->db->join('supplier s', 'p.id_supplier = s.id_supplier');
        $this->db->where('p.id_perusahaan', $id_perusahaan);
        $this->db->where('p.status', 'Completed');
        $this->db->order_by('p.created_at', 'DESC');
        return $this->db->get()->result();
    }

    public function insert($data)
    {
        $this->db->insert('retur_pembelian', $data);
        return $this->db->insert_id();
    }

    public function insert_detail($data)
    {
        $this->db->insert('detail_retur_pembelian', $data);
        return $this->db->insert_id();
    }

    public function update_status($id_retur, $status)
    {
        $this->db->where('id_retur_beli', $id_retur);
        $data = [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        return $this->db->update('retur_pembelian', $data);
    }

    public function update_detail($id_detail, $data)
    {
        $this->db->where('id_detail_retur_beli', $id_detail);
        return $this->db->update('detail_retur_pembelian', $data);
    }

    public function delete($id_retur)
    {
        $this->db->where('id_retur_beli', $id_retur);
        return $this->db->delete('retur_pembelian');
    }

    public function get_stok_barang($id_gudang, $id_barang)
    {
        $this->db->where('id_gudang', $id_gudang);
        $this->db->where('id_barang', $id_barang);
        return $this->db->get('stok_gudang')->row();
    }

    public function update_stok($id_gudang, $id_barang, $jumlah)
    {
        $this->db->where('id_gudang', $id_gudang);
        $this->db->where('id_barang', $id_barang);
        $data = [
            'jumlah' => $jumlah,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        return $this->db->update('stok_gudang', $data);
    }

    public function insert_log_stok($data)
    {
        $this->db->insert('log_stok', $data);
        return $this->db->insert_id();
    }

    public function insert_log_status($data)
    {
        $this->db->insert('log_status_transaksi', $data);
        return $this->db->insert_id();
    }
}