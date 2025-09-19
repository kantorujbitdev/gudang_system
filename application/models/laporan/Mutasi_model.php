<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Mutasi_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_filtered($filter)
    {
        $id_perusahaan = $this->session->userdata('id_perusahaan');

        $this->db->select('ls.*, b.nama_barang, g.nama_gudang, u.nama as user_nama');
        $this->db->from('log_stok ls');
        $this->db->join('barang b', 'ls.id_barang = b.id_barang');
        $this->db->join('gudang g', 'ls.id_gudang = g.id_gudang');
        $this->db->join('user u', 'ls.id_user = u.id_user', 'left');
        $this->db->where('ls.id_perusahaan', $id_perusahaan);

        if ($filter['tanggal_awal']) {
            $this->db->where('DATE(ls.tanggal) >=', $filter['tanggal_awal']);
        }

        if ($filter['tanggal_akhir']) {
            $this->db->where('DATE(ls.tanggal) <=', $filter['tanggal_akhir']);
        }

        if ($filter['id_barang']) {
            $this->db->where('ls.id_barang', $filter['id_barang']);
        }

        if ($filter['id_gudang']) {
            $this->db->where('ls.id_gudang', $filter['id_gudang']);
        }

        if ($filter['jenis']) {
            $this->db->where('ls.jenis', $filter['jenis']);
        }

        $this->db->order_by('ls.tanggal', 'DESC');
        return $this->db->get()->result();
    }

    public function get_summary($filter)
    {
        $id_perusahaan = $this->session->userdata('id_perusahaan');

        $this->db->select('
            SUM(CASE WHEN ls.jenis = "masuk" THEN ls.jumlah ELSE 0 END) as total_masuk,
            SUM(CASE WHEN ls.jenis = "keluar" THEN ls.jumlah ELSE 0 END) as total_keluar,
            SUM(CASE WHEN ls.jenis = "retur_penjualan" THEN ls.jumlah ELSE 0 END) as total_retur_penjualan,
            SUM(CASE WHEN ls.jenis = "retur_pembelian" THEN ls.jumlah ELSE 0 END) as total_retur_pembelian,
            SUM(CASE WHEN ls.jenis = "transfer_masuk" THEN ls.jumlah ELSE 0 END) as total_transfer_masuk,
            SUM(CASE WHEN ls.jenis = "transfer_keluar" THEN ls.jumlah ELSE 0 END) as total_transfer_keluar,
            SUM(CASE WHEN ls.jenis = "penyesuaian" THEN ls.jumlah ELSE 0 END) as total_penyesuaian
        ');
        $this->db->from('log_stok ls');
        $this->db->where('ls.id_perusahaan', $id_perusahaan);

        if ($filter['tanggal_awal']) {
            $this->db->where('DATE(ls.tanggal) >=', $filter['tanggal_awal']);
        }

        if ($filter['tanggal_akhir']) {
            $this->db->where('DATE(ls.tanggal) <=', $filter['tanggal_akhir']);
        }

        if ($filter['id_barang']) {
            $this->db->where('ls.id_barang', $filter['id_barang']);
        }

        if ($filter['id_gudang']) {
            $this->db->where('ls.id_gudang', $filter['id_gudang']);
        }

        return $this->db->get()->row();
    }

    public function get_mutasi_by_barang($filter)
    {
        $id_perusahaan = $this->session->userdata('id_perusahaan');

        $this->db->select('b.id_barang, b.nama_barang, b.satuan,
            SUM(CASE WHEN ls.jenis = "masuk" THEN ls.jumlah ELSE 0 END) as total_masuk,
            SUM(CASE WHEN ls.jenis = "keluar" THEN ls.jumlah ELSE 0 END) as total_keluar,
            SUM(CASE WHEN ls.jenis = "retur_penjualan" THEN ls.jumlah ELSE 0 END) as total_retur_penjualan,
            SUM(CASE WHEN ls.jenis = "retur_pembelian" THEN ls.jumlah ELSE 0 END) as total_retur_pembelian,
            SUM(CASE WHEN ls.jenis = "transfer_masuk" THEN ls.jumlah ELSE 0 END) as total_transfer_masuk,
            SUM(CASE WHEN ls.jenis = "transfer_keluar" THEN ls.jumlah ELSE 0 END) as total_transfer_keluar
        ');
        $this->db->from('log_stok ls');
        $this->db->join('barang b', 'ls.id_barang = b.id_barang');
        $this->db->where('ls.id_perusahaan', $id_perusahaan);

        if ($filter['tanggal_awal']) {
            $this->db->where('DATE(ls.tanggal) >=', $filter['tanggal_awal']);
        }

        if ($filter['tanggal_akhir']) {
            $this->db->where('DATE(ls.tanggal) <=', $filter['tanggal_akhir']);
        }

        if ($filter['id_gudang']) {
            $this->db->where('ls.id_gudang', $filter['id_gudang']);
        }

        if ($filter['jenis']) {
            $this->db->where('ls.jenis', $filter['jenis']);
        }

        $this->db->group_by('b.id_barang, b.nama_barang, b.satuan');
        $this->db->order_by('b.nama_barang', 'ASC');
        return $this->db->get()->result();
    }

    public function get_mutasi_by_gudang($filter)
    {
        $id_perusahaan = $this->session->userdata('id_perusahaan');

        $this->db->select('g.id_gudang, g.nama_gudang,
            SUM(CASE WHEN ls.jenis = "masuk" THEN ls.jumlah ELSE 0 END) as total_masuk,
            SUM(CASE WHEN ls.jenis = "keluar" THEN ls.jumlah ELSE 0 END) as total_keluar,
            SUM(CASE WHEN ls.jenis = "retur_penjualan" THEN ls.jumlah ELSE 0 END) as total_retur_penjualan,
            SUM(CASE WHEN ls.jenis = "retur_pembelian" THEN ls.jumlah ELSE 0 END) as total_retur_pembelian,
            SUM(CASE WHEN ls.jenis = "transfer_masuk" THEN ls.jumlah ELSE 0 END) as total_transfer_masuk,
            SUM(CASE WHEN ls.jenis = "transfer_keluar" THEN ls.jumlah ELSE 0 END) as total_transfer_keluar
        ');
        $this->db->from('log_stok ls');
        $this->db->join('gudang g', 'ls.id_gudang = g.id_gudang');
        $this->db->where('ls.id_perusahaan', $id_perusahaan);

        if ($filter['tanggal_awal']) {
            $this->db->where('DATE(ls.tanggal) >=', $filter['tanggal_awal']);
        }

        if ($filter['tanggal_akhir']) {
            $this->db->where('DATE(ls.tanggal) <=', $filter['tanggal_akhir']);
        }

        if ($filter['id_barang']) {
            $this->db->where('ls.id_barang', $filter['id_barang']);
        }

        if ($filter['jenis']) {
            $this->db->where('ls.jenis', $filter['jenis']);
        }

        $this->db->group_by('g.id_gudang, g.nama_gudang');
        $this->db->order_by('g.nama_gudang', 'ASC');
        return $this->db->get()->result();
    }
}