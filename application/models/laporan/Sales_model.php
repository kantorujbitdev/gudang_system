<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Sales_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_filtered($filter)
    {
        $id_perusahaan = $this->session->userdata('id_perusahaan');

        $this->db->select('p.id_penjualan, p.no_invoice, p.tanggal_penjualan, pl.nama_pelanggan, 
                         dp.id_barang, b.nama_barang, dp.jumlah, dp.harga_jual, 
                         (dp.jumlah * dp.harga_jual) as total, p.status');
        $this->db->from('penjualan p');
        $this->db->join('detail_penjualan dp', 'p.id_penjualan = dp.id_penjualan');
        $this->db->join('barang b', 'dp.id_barang = b.id_barang');
        $this->db->join('pelanggan pl', 'p.id_pelanggan = pl.id_pelanggan');
        $this->db->where('p.id_perusahaan', $id_perusahaan);

        if ($filter['tanggal_awal']) {
            $this->db->where('DATE(p.tanggal_penjualan) >=', $filter['tanggal_awal']);
        }

        if ($filter['tanggal_akhir']) {
            $this->db->where('DATE(p.tanggal_penjualan) <=', $filter['tanggal_akhir']);
        }

        if ($filter['id_pelanggan']) {
            $this->db->where('p.id_pelanggan', $filter['id_pelanggan']);
        }

        if ($filter['id_barang']) {
            $this->db->where('dp.id_barang', $filter['id_barang']);
        }

        $this->db->order_by('p.tanggal_penjualan', 'DESC');
        return $this->db->get()->result();
    }

    public function get_penjualan($id_penjualan)
    {
        $this->db->select('p.*, u.nama as user_nama, pl.nama_pelanggan, pl.alamat, pl.telepon');
        $this->db->from('penjualan p');
        $this->db->join('user u', 'p.id_user = u.id_user');
        $this->db->join('pelanggan pl', 'p.id_pelanggan = pl.id_pelanggan');
        $this->db->where('p.id_penjualan', $id_penjualan);
        return $this->db->get()->row();
    }

    public function get_detail_penjualan($id_penjualan)
    {
        $this->db->select('dp.*, b.nama_barang, b.satuan, g.nama_gudang');
        $this->db->from('detail_penjualan dp');
        $this->db->join('barang b', 'dp.id_barang = b.id_barang');
        $this->db->join('gudang g', 'dp.id_gudang = g.id_gudang');
        $this->db->where('dp.id_penjualan', $id_penjualan);
        return $this->db->get()->result();
    }

    public function get_summary($filter)
    {
        $id_perusahaan = $this->session->userdata('id_perusahaan');

        $this->db->select('COUNT(DISTINCT p.id_penjualan) as total_transaksi, 
                         SUM(dp.jumlah * dp.harga_jual) as total_penjualan');
        $this->db->from('penjualan p');
        $this->db->join('detail_penjualan dp', 'p.id_penjualan = dp.id_penjualan');
        $this->db->where('p.id_perusahaan', $id_perusahaan);

        if ($filter['tanggal_awal']) {
            $this->db->where('DATE(p.tanggal_penjualan) >=', $filter['tanggal_awal']);
        }

        if ($filter['tanggal_akhir']) {
            $this->db->where('DATE(p.tanggal_penjualan) <=', $filter['tanggal_akhir']);
        }

        if ($filter['id_pelanggan']) {
            $this->db->where('p.id_pelanggan', $filter['id_pelanggan']);
        }

        if ($filter['id_barang']) {
            $this->db->where('dp.id_barang', $filter['id_barang']);
        }

        return $this->db->get()->row();
    }

    public function get_sales_by_pelanggan($filter)
    {
        $id_perusahaan = $this->session->userdata('id_perusahaan');

        $this->db->select('pl.id_pelanggan, pl.nama_pelanggan, 
                         COUNT(DISTINCT p.id_penjualan) as total_transaksi,
                         SUM(dp.jumlah * dp.harga_jual) as total_penjualan');
        $this->db->from('penjualan p');
        $this->db->join('detail_penjualan dp', 'p.id_penjualan = dp.id_penjualan');
        $this->db->join('pelanggan pl', 'p.id_pelanggan = pl.id_pelanggan');
        $this->db->where('p.id_perusahaan', $id_perusahaan);

        if ($filter['tanggal_awal']) {
            $this->db->where('DATE(p.tanggal_penjualan) >=', $filter['tanggal_awal']);
        }

        if ($filter['tanggal_akhir']) {
            $this->db->where('DATE(p.tanggal_penjualan) <=', $filter['tanggal_akhir']);
        }

        if ($filter['id_barang']) {
            $this->db->where('dp.id_barang', $filter['id_barang']);
        }

        $this->db->group_by('pl.id_pelanggan, pl.nama_pelanggan');
        $this->db->order_by('total_penjualan', 'DESC');
        return $this->db->get()->result();
    }

    public function get_sales_by_barang($filter)
    {
        $id_perusahaan = $this->session->userdata('id_perusahaan');

        $this->db->select('b.id_barang, b.nama_barang, b.satuan,
                         SUM(dp.jumlah) as total_jumlah,
                         SUM(dp.jumlah * dp.harga_jual) as total_penjualan');
        $this->db->from('penjualan p');
        $this->db->join('detail_penjualan dp', 'p.id_penjualan = dp.id_penjualan');
        $this->db->join('barang b', 'dp.id_barang = b.id_barang');
        $this->db->where('p.id_perusahaan', $id_perusahaan);

        if ($filter['tanggal_awal']) {
            $this->db->where('DATE(p.tanggal_penjualan) >=', $filter['tanggal_awal']);
        }

        if ($filter['tanggal_akhir']) {
            $this->db->where('DATE(p.tanggal_penjualan) <=', $filter['tanggal_akhir']);
        }

        if ($filter['id_pelanggan']) {
            $this->db->where('p.id_pelanggan', $filter['id_pelanggan']);
        }

        $this->db->group_by('b.id_barang, b.nama_barang, b.satuan');
        $this->db->order_by('total_penjualan', 'DESC');
        return $this->db->get()->result();
    }
}