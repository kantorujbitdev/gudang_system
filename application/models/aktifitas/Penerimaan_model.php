<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Penerimaan_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_all()
    {
        $id_perusahaan = $this->session->userdata('id_perusahaan');

        $this->db->select('pb.*, u.nama as user_nama, g.nama_gudang, s.nama_supplier');
        $this->db->from('penerimaan_barang pb');
        $this->db->join('user u', 'pb.id_user = u.id_user');
        $this->db->join('gudang g', 'pb.id_gudang = g.id_gudang');
        $this->db->join('supplier s', 'pb.id_supplier = s.id_supplier');
        $this->db->where('pb.id_perusahaan', $id_perusahaan);
        $this->db->order_by('pb.created_at', 'DESC');
        return $this->db->get()->result();
    }

    public function get($id_penerimaan)
    {
        $this->db->select('pb.*, u.nama as user_nama, g.nama_gudang, s.nama_supplier');
        $this->db->from('penerimaan_barang pb');
        $this->db->join('user u', 'pb.id_user = u.id_user');
        $this->db->join('gudang g', 'pb.id_gudang = g.id_gudang');
        $this->db->join('supplier s', 'pb.id_supplier = s.id_supplier');
        $this->db->where('pb.id_penerimaan', $id_penerimaan);
        return $this->db->get()->row();
    }

    public function get_detail($id_penerimaan)
    {
        $this->db->select('dp.*, b.nama_barang, b.satuan');
        $this->db->from('detail_penerimaan dp');
        $this->db->join('barang b', 'dp.id_barang = b.id_barang');
        $this->db->where('dp.id_penerimaan', $id_penerimaan);
        return $this->db->get()->result();
    }

    public function insert($data)
    {
        $this->db->insert('penerimaan_barang', $data);
        return $this->db->insert_id();
    }

    public function insert_detail($data)
    {
        $this->db->insert('detail_penerimaan', $data);
        return $this->db->insert_id();
    }

    public function update($id_penerimaan, $data)
    {
        $this->db->where('id_penerimaan', $id_penerimaan);
        return $this->db->update('penerimaan_barang', $data);
    }

    public function update_status($id_penerimaan, $status)
    {
        $this->db->where('id_penerimaan', $id_penerimaan);
        $data = [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        return $this->db->update('penerimaan_barang', $data);
    }

    public function delete($id_penerimaan)
    {
        $this->db->where('id_penerimaan', $id_penerimaan);
        return $this->db->delete('penerimaan_barang');
    }

    public function delete_detail($id_penerimaan)
    {
        $this->db->where('id_penerimaan', $id_penerimaan);
        return $this->db->delete('detail_penerimaan');
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