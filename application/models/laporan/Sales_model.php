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
        $id_user = $this->session->userdata('id_user');
        $user_role = $this->session->userdata('id_role');

        // Menggunakan select manual untuk menghindari masalah backtick
        $this->db->select('pb.id_pemindahan, pb.no_transaksi, pb.tanggal_pemindahan, 
                         CASE 
                            WHEN pb.tipe_tujuan = "pelanggan" THEN pl.nama_pelanggan
                            WHEN pb.tipe_tujuan = "konsumen" THEN k.nama_konsumen
                            ELSE "-"
                         END as nama_tujuan,
                         dpb.id_barang, b.nama_barang, dpb.jumlah, b.satuan, pb.status, u.nama as nama_user', FALSE);
        $this->db->from('pemindahan_barang pb');
        $this->db->join('detail_pemindahan_barang dpb', 'pb.id_pemindahan = dpb.id_pemindahan');
        $this->db->join('barang b', 'dpb.id_barang = b.id_barang');
        $this->db->join('gudang g', 'dpb.id_gudang = g.id_gudang');
        $this->db->join('perusahaan p', 'pb.id_perusahaan = p.id_perusahaan');
        $this->db->join('user u', 'pb.id_user = u.id_user');
        $this->db->join('pelanggan pl', 'pb.id_pelanggan = pl.id_pelanggan', 'left');
        $this->db->join('konsumen k', 'pb.id_konsumen = k.id_konsumen', 'left');
        $this->db->where('pb.id_perusahaan', $id_perusahaan);
        // Hanya menampilkan data pemindahan barang ke konsumen
        $this->db->where('pb.tipe_tujuan', 'konsumen');

        // Filter status - hanya menampilkan Shipping dan Delivered
        if (isset($filter['status']) && !empty($filter['status'])) {
            $this->db->where('pb.status', $filter['status']);
        } else {
            // Default: hanya menampilkan Shipping dan Delivered
            $this->db->where_in('pb.status', ['Shipping', 'Delivered']);
        }

        // Jika role adalah Sales, hanya tampilkan data miliknya sendiri
        if ($user_role == 3) { // 3 adalah id_role untuk Sales Online
            $this->db->where('pb.id_user', $id_user);
        }

        if ($filter['tanggal_awal']) {
            $this->db->where('DATE(pb.tanggal_pemindahan) >=', $filter['tanggal_awal']);
        }

        if ($filter['tanggal_akhir']) {
            $this->db->where('DATE(pb.tanggal_pemindahan) <=', $filter['tanggal_akhir']);
        }

        if ($filter['id_barang']) {
            $this->db->where('dpb.id_barang', $filter['id_barang']);
        }

        if ($filter['id_user']) {
            $this->db->where('pb.id_user', $filter['id_user']);
        }

        $this->db->order_by('pb.tanggal_pemindahan', 'DESC');
        return $this->db->get()->result();
    }

    public function get_pemindahan($id_pemindahan)
    {
        // Menggunakan select manual untuk menghindari masalah backtick
        $this->db->select('pb.*, u.nama as user_nama, 
                         CASE 
                            WHEN pb.tipe_tujuan = "pelanggan" THEN pl.nama_pelanggan
                            WHEN pb.tipe_tujuan = "konsumen" THEN k.nama_konsumen
                            ELSE "-"
                         END as nama_tujuan,
                         CASE 
                            WHEN pb.tipe_tujuan = "pelanggan" THEN pl.alamat
                            WHEN pb.tipe_tujuan = "konsumen" THEN k.alamat_konsumen
                            ELSE "-"
                         END as alamat_tujuan,
                         CASE 
                            WHEN pb.tipe_tujuan = "pelanggan" THEN pl.telepon
                            WHEN pb.tipe_tujuan = "konsumen" THEN "-"
                            ELSE "-"
                         END as telepon_tujuan,
                         g.nama_gudang as gudang_asal', FALSE);
        $this->db->from('pemindahan_barang pb');
        $this->db->join('user u', 'pb.id_user = u.id_user');
        $this->db->join('gudang g', 'pb.id_gudang_asal = g.id_gudang');
        $this->db->join('pelanggan pl', 'pb.id_pelanggan = pl.id_pelanggan', 'left');
        $this->db->join('konsumen k', 'pb.id_konsumen = k.id_konsumen', 'left');
        $this->db->where('pb.id_pemindahan', $id_pemindahan);
        return $this->db->get()->row();
    }

    public function get_detail_pemindahan($id_pemindahan)
    {
        $this->db->select('dpb.*, b.nama_barang, b.satuan, g.nama_gudang');
        $this->db->from('detail_pemindahan_barang dpb');
        $this->db->join('barang b', 'dpb.id_barang = b.id_barang');
        $this->db->join('gudang g', 'dpb.id_gudang = g.id_gudang');
        $this->db->where('dpb.id_pemindahan', $id_pemindahan);
        return $this->db->get()->result();
    }

    public function get_summary($filter)
    {
        $id_perusahaan = $this->session->userdata('id_perusahaan');
        $id_user = $this->session->userdata('id_user');
        $user_role = $this->session->userdata('id_role');

        $this->db->select('COUNT(DISTINCT pb.id_pemindahan) as total_transaksi, 
                         SUM(dpb.jumlah) as total_barang');
        $this->db->from('pemindahan_barang pb');
        $this->db->join('detail_pemindahan_barang dpb', 'pb.id_pemindahan = dpb.id_pemindahan');
        $this->db->where('pb.id_perusahaan', $id_perusahaan);
        // Hanya menampilkan data pemindahan barang ke konsumen
        $this->db->where('pb.tipe_tujuan', 'konsumen');

        // Filter status - hanya menampilkan Shipping dan Delivered
        if (isset($filter['status']) && !empty($filter['status'])) {
            $this->db->where('pb.status', $filter['status']);
        } else {
            // Default: hanya menampilkan Shipping dan Delivered
            $this->db->where_in('pb.status', ['Shipping', 'Delivered']);
        }

        // Jika role adalah Sales, hanya tampilkan data miliknya sendiri
        if ($user_role == 3) { // 3 adalah id_role untuk Sales Online
            $this->db->where('pb.id_user', $id_user);
        }

        if ($filter['tanggal_awal']) {
            $this->db->where('DATE(pb.tanggal_pemindahan) >=', $filter['tanggal_awal']);
        }

        if ($filter['tanggal_akhir']) {
            $this->db->where('DATE(pb.tanggal_pemindahan) <=', $filter['tanggal_akhir']);
        }

        if ($filter['id_barang']) {
            $this->db->where('dpb.id_barang', $filter['id_barang']);
        }

        if ($filter['id_user']) {
            $this->db->where('pb.id_user', $filter['id_user']);
        }

        return $this->db->get()->row();
    }

    public function get_sales_by_pelanggan($filter)
    {
        $id_perusahaan = $this->session->userdata('id_perusahaan');
        $id_user = $this->session->userdata('id_user');
        $user_role = $this->session->userdata('id_role');

        // Menggunakan select manual untuk menghindari masalah backtick
        $this->db->select('CASE 
                            WHEN pb.tipe_tujuan = "pelanggan" THEN pl.id_pelanggan
                            WHEN pb.tipe_tujuan = "konsumen" THEN k.id_konsumen
                            ELSE 0
                         END as id_tujuan,
                         CASE 
                            WHEN pb.tipe_tujuan = "pelanggan" THEN pl.nama_pelanggan
                            WHEN pb.tipe_tujuan = "konsumen" THEN k.nama_konsumen
                            ELSE "-"
                         END as nama_tujuan,
                         COUNT(DISTINCT pb.id_pemindahan) as total_transaksi,
                         SUM(dpb.jumlah) as total_barang', FALSE);
        $this->db->from('pemindahan_barang pb');
        $this->db->join('detail_pemindahan_barang dpb', 'pb.id_pemindahan = dpb.id_pemindahan');
        $this->db->join('pelanggan pl', 'pb.id_pelanggan = pl.id_pelanggan', 'left');
        $this->db->join('konsumen k', 'pb.id_konsumen = k.id_konsumen', 'left');
        $this->db->where('pb.id_perusahaan', $id_perusahaan);
        // Hanya menampilkan data pemindahan barang ke konsumen
        $this->db->where('pb.tipe_tujuan', 'konsumen');

        // Filter status - hanya menampilkan Shipping dan Delivered
        if (isset($filter['status']) && !empty($filter['status'])) {
            $this->db->where('pb.status', $filter['status']);
        } else {
            // Default: hanya menampilkan Shipping dan Delivered
            $this->db->where_in('pb.status', ['Shipping', 'Delivered']);
        }

        // Jika role adalah Sales, hanya tampilkan data miliknya sendiri
        if ($user_role == 3) { // 3 adalah id_role untuk Sales Online
            $this->db->where('pb.id_user', $id_user);
        }

        if ($filter['tanggal_awal']) {
            $this->db->where('DATE(pb.tanggal_pemindahan) >=', $filter['tanggal_awal']);
        }

        if ($filter['tanggal_akhir']) {
            $this->db->where('DATE(pb.tanggal_pemindahan) <=', $filter['tanggal_akhir']);
        }

        if ($filter['id_barang']) {
            $this->db->where('dpb.id_barang', $filter['id_barang']);
        }

        if ($filter['id_user']) {
            $this->db->where('pb.id_user', $filter['id_user']);
        }

        $this->db->group_by('id_tujuan, nama_tujuan');
        $this->db->order_by('total_barang', 'DESC');
        return $this->db->get()->result();
    }

    public function get_sales_by_barang($filter)
    {
        $id_perusahaan = $this->session->userdata('id_perusahaan');
        $id_user = $this->session->userdata('id_user');
        $user_role = $this->session->userdata('id_role');

        $this->db->select('b.id_barang, b.nama_barang, b.satuan,
                         SUM(dpb.jumlah) as total_jumlah');
        $this->db->from('pemindahan_barang pb');
        $this->db->join('detail_pemindahan_barang dpb', 'pb.id_pemindahan = dpb.id_pemindahan');
        $this->db->join('barang b', 'dpb.id_barang = b.id_barang');
        $this->db->where('pb.id_perusahaan', $id_perusahaan);
        // Hanya menampilkan data pemindahan barang ke konsumen
        $this->db->where('pb.tipe_tujuan', 'konsumen');

        // Filter status - hanya menampilkan Shipping dan Delivered
        if (isset($filter['status']) && !empty($filter['status'])) {
            $this->db->where('pb.status', $filter['status']);
        } else {
            // Default: hanya menampilkan Shipping dan Delivered
            $this->db->where_in('pb.status', ['Shipping', 'Delivered']);
        }

        // Jika role adalah Sales, hanya tampilkan data miliknya sendiri
        if ($user_role == 3) { // 3 adalah id_role untuk Sales Online
            $this->db->where('pb.id_user', $id_user);
        }

        if ($filter['tanggal_awal']) {
            $this->db->where('DATE(pb.tanggal_pemindahan) >=', $filter['tanggal_awal']);
        }

        if ($filter['tanggal_akhir']) {
            $this->db->where('DATE(pb.tanggal_pemindahan) <=', $filter['tanggal_akhir']);
        }

        if ($filter['id_user']) {
            $this->db->where('pb.id_user', $filter['id_user']);
        }

        $this->db->group_by('b.id_barang, b.nama_barang, b.satuan');
        $this->db->order_by('total_jumlah', 'DESC');
        return $this->db->get()->result();
    }

    public function get_users_by_company($id_perusahaan)
    {
        $this->db->select('u.id_user, u.nama, r.nama_role');
        $this->db->from('user u');
        $this->db->join('role_user r', 'u.id_role = r.id_role');
        $this->db->where('u.id_perusahaan', $id_perusahaan);
        $this->db->where('u.aktif', 1);
        $this->db->where_in('u.id_role', array(2, 3)); // Admin Perusahaan dan Sales Online
        $this->db->order_by('r.nama_role, u.nama');
        return $this->db->get()->result();
    }
}