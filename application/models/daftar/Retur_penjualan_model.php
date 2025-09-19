<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Retur_penjualan_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_pelanggan()
    {
        $id_perusahaan = $this->session->userdata('id_perusahaan');
        $this->db->where('id_perusahaan', $id_perusahaan);
        $this->db->where('status_aktif', 1);
        $this->db->order_by('nama_pelanggan', 'ASC');
        return $this->db->get('pelanggan')->result();
    }

    public function get_filtered($filter)
    {
        $id_perusahaan = $this->session->userdata('id_perusahaan');

        $this->db->select('rp.*, u.nama as user_nama, p.no_invoice, pl.nama_pelanggan');
        $this->db->from('retur_penjualan rp');
        $this->db->join('user u', 'rp.id_user = u.id_user');
        $this->db->join('penjualan p', 'rp.id_penjualan = p.id_penjualan');
        $this->db->join('pelanggan pl', 'p.id_pelanggan = pl.id_pelanggan');
        $this->db->where('p.id_perusahaan', $id_perusahaan);

        if ($filter['tanggal_awal']) {
            $this->db->where('DATE(rp.tanggal_retur) >=', $filter['tanggal_awal']);
        }

        if ($filter['tanggal_akhir']) {
            $this->db->where('DATE(rp.tanggal_retur) <=', $filter['tanggal_akhir']);
        }

        if ($filter['status']) {
            $this->db->where('rp.status', $filter['status']);
        }

        if ($filter['id_pelanggan']) {
            $this->db->where('p.id_pelanggan', $filter['id_pelanggan']);
        }

        $this->db->order_by('rp.created_at', 'DESC');
        return $this->db->get()->result();
    }

    public function get($id_retur)
    {
        $this->db->select('rp.*, u.nama as user_nama, p.no_invoice, pl.nama_pelanggan');
        $this->db->from('retur_penjualan rp');
        $this->db->join('user u', 'rp.id_user = u.id_user');
        $this->db->join('penjualan p', 'rp.id_penjualan = p.id_penjualan');
        $this->db->join('pelanggan pl', 'p.id_pelanggan = pl.id_pelanggan');
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
}