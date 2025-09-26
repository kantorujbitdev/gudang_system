<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Retur_penjualan_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_all()
    {
        $id_perusahaan = $this->session->userdata('id_perusahaan');

        $this->db->select('rp.*, u.nama as user_nama, pb.no_transaksi');
        $this->db->select('IF(pb.tipe_tujuan = "pelanggan", pl.nama_pelanggan, IF(pb.tipe_tujuan = "konsumen", k.nama_konsumen, "-")) as nama_penerima', FALSE);
        $this->db->from('retur_penjualan rp');
        $this->db->join('user u', 'rp.id_user = u.id_user');
        $this->db->join('pemindahan_barang pb', 'rp.id_pemindahan = pb.id_pemindahan');
        $this->db->join('pelanggan pl', 'pb.id_pelanggan = pl.id_pelanggan', 'left');
        $this->db->join('konsumen k', 'pb.id_konsumen = k.id_konsumen', 'left');
        $this->db->where('pb.id_perusahaan', $id_perusahaan);
        $this->db->order_by('rp.created_at', 'DESC');
        return $this->db->get()->result();
    }

    public function get($id_retur)
    {
        $this->db->select('rp.*, u.nama as user_nama, pb.no_transaksi, pb.tipe_tujuan');
        $this->db->select('IF(pb.tipe_tujuan = "pelanggan", pl.nama_pelanggan, IF(pb.tipe_tujuan = "konsumen", k.nama_konsumen, "-")) as nama_penerima', FALSE);
        $this->db->from('retur_penjualan rp');
        $this->db->join('user u', 'rp.id_user = u.id_user');
        $this->db->join('pemindahan_barang pb', 'rp.id_pemindahan = pb.id_pemindahan');
        $this->db->join('pelanggan pl', 'pb.id_pelanggan = pl.id_pelanggan', 'left');
        $this->db->join('konsumen k', 'pb.id_konsumen = k.id_konsumen', 'left');
        $this->db->where('rp.id_retur', $id_retur);
        return $this->db->get()->row();
    }

    public function get_detail($id_retur)
    {
        $this->db->select('dr.*, b.nama_barang, b.satuan, g.nama_gudang');
        $this->db->from('detail_retur_penjualan dr');
        $this->db->join('barang b', 'dr.id_barang = b.id_barang');
        $this->db->join('gudang g', 'dr.id_gudang = g.id_gudang');
        $this->db->where('dr.id_retur', $id_retur);
        return $this->db->get()->result();
    }

    public function get_pemindahan()
    {
        $id_perusahaan = $this->session->userdata('id_perusahaan');

        $this->db->select('pb.id_pemindahan, pb.no_transaksi');
        $this->db->select('IF(pb.tipe_tujuan = "pelanggan", pl.nama_pelanggan, IF(pb.tipe_tujuan = "konsumen", k.nama_konsumen, "-")) as nama_penerima', FALSE);
        $this->db->from('pemindahan_barang pb');
        $this->db->join('pelanggan pl', 'pb.id_pelanggan = pl.id_pelanggan', 'left');
        $this->db->join('konsumen k', 'pb.id_konsumen = k.id_konsumen', 'left');
        $this->db->where('pb.id_perusahaan', $id_perusahaan);
        $this->db->where('pb.status', 'Delivered');
        $this->db->order_by('pb.created_at', 'DESC');
        return $this->db->get()->result();
    }

    public function get_detail_pemindahan($id_pemindahan)
    {
        $this->db->select('dp.*, b.nama_barang, b.satuan, g.nama_gudang, g.id_gudang');
        $this->db->from('detail_pemindahan_barang dp');
        $this->db->join('barang b', 'dp.id_barang = b.id_barang');
        $this->db->join('gudang g', 'dp.id_gudang = g.id_gudang');
        $this->db->where('dp.id_pemindahan', $id_pemindahan);
        return $this->db->get()->result();
    }

    public function insert($data)
    {
        $this->db->insert('retur_penjualan', $data);
        return $this->db->insert_id();
    }

    public function insert_detail($data)
    {
        $this->db->insert('detail_retur_penjualan', $data);
        return $this->db->insert_id();
    }

    public function update_status($id_retur, $status)
    {
        $this->db->where('id_retur', $id_retur);
        $data = [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        return $this->db->update('retur_penjualan', $data);
    }

    public function update_detail($id_detail, $data)
    {
        $this->db->where('id_detail_retur', $id_detail);
        return $this->db->update('detail_retur_penjualan', $data);
    }

    public function delete($id_retur)
    {
        $this->db->where('id_retur', $id_retur);
        return $this->db->delete('retur_penjualan');
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