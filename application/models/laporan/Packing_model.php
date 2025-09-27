<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Packing_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }
    public function get_packing_efficiency($filter)
    {
        $id_perusahaan = $this->session->userdata('id_perusahaan');

        $this->db->select('p.id_packing, p.tanggal_packing, pb.tanggal_pemindahan as tanggal_transaksi,
                         TIMESTAMPDIFF(HOUR, pb.tanggal_pemindahan, p.tanggal_packing) as waktu_packing,
                         p.status, u.nama as user_nama', FALSE);
        $this->db->from('packing p');
        $this->db->join('user u', 'p.id_user = u.id_user');
        $this->db->join('pemindahan_barang pb', 'p.id_referensi = pb.id_pemindahan AND p.tipe_referensi = "pemindahan_barang"', 'left');
        $this->db->where('pb.id_perusahaan', $id_perusahaan);

        if ($filter['tanggal_awal']) {
            $this->db->where('DATE(p.tanggal_packing) >=', $filter['tanggal_awal']);
        }

        if ($filter['tanggal_akhir']) {
            $this->db->where('DATE(p.tanggal_packing) <=', $filter['tanggal_akhir']);
        }

        if ($filter['id_user']) {
            $this->db->where('p.id_user', $filter['id_user']);
        }

        if ($filter['status']) {
            $this->db->where('p.status', $filter['status']);
        }

        $this->db->order_by('p.tanggal_packing', 'DESC');
        return $this->db->get()->result();
    }

    public function get_packing_by_period($filter)
    {
        $id_perusahaan = $this->session->userdata('id_perusahaan');

        $this->db->select('DATE(p.tanggal_packing) as tanggal, 
                         COUNT(DISTINCT p.id_packing) as total_packing,
                         SUM(dp.jumlah) as total_barang', FALSE);
        $this->db->from('packing p');
        $this->db->join('detail_packing dp', 'p.id_packing = dp.id_packing');
        $this->db->join('pemindahan_barang pb', 'p.id_referensi = pb.id_pemindahan AND p.tipe_referensi = "pemindahan_barang"', 'left');
        $this->db->where('pb.id_perusahaan', $id_perusahaan);

        if ($filter['tanggal_awal']) {
            $this->db->where('DATE(p.tanggal_packing) >=', $filter['tanggal_awal']);
        }

        if ($filter['tanggal_akhir']) {
            $this->db->where('DATE(p.tanggal_packing) <=', $filter['tanggal_akhir']);
        }

        if ($filter['id_user']) {
            $this->db->where('p.id_user', $filter['id_user']);
        }

        if ($filter['status']) {
            $this->db->where('p.status', $filter['status']);
        }

        $this->db->group_by('DATE(p.tanggal_packing)');
        $this->db->order_by('tanggal', 'ASC');
        return $this->db->get()->result();
    }

    public function get_average_packing_time($filter)
    {
        $id_perusahaan = $this->session->userdata('id_perusahaan');

        $this->db->select('AVG(TIMESTAMPDIFF(HOUR, pb.tanggal_pemindahan, p.tanggal_packing)) as rata_rata_waktu', FALSE);
        $this->db->from('packing p');
        $this->db->join('pemindahan_barang pb', 'p.id_referensi = pb.id_pemindahan AND p.tipe_referensi = "pemindahan_barang"', 'left');
        $this->db->where('pb.id_perusahaan', $id_perusahaan);

        if ($filter['tanggal_awal']) {
            $this->db->where('DATE(p.tanggal_packing) >=', $filter['tanggal_awal']);
        }

        if ($filter['tanggal_akhir']) {
            $this->db->where('DATE(p.tanggal_packing) <=', $filter['tanggal_akhir']);
        }

        if ($filter['id_user']) {
            $this->db->where('p.id_user', $filter['id_user']);
        }

        if ($filter['status']) {
            $this->db->where('p.status', $filter['status']);
        }

        return $this->db->get()->row();
    }
    public function get_filtered($filter)
    {
        $user_role = $this->session->userdata('id_role');
        $id_user = $this->session->userdata('id_user');
        $id_perusahaan = $this->session->userdata('id_perusahaan');

        $this->db->select('p.*, u.nama as user_nama, pb.no_transaksi, pb.tanggal_pemindahan, 
                      b.nama_barang, dpb.jumlah, b.satuan');
        $this->db->from('packing p');
        $this->db->join('user u', 'p.id_user = u.id_user');
        $this->db->join('detail_packing dpb', 'p.id_packing = dpb.id_packing');
        $this->db->join('barang b', 'dpb.id_barang = b.id_barang');
        $this->db->join('pemindahan_barang pb', 'p.id_referensi = pb.id_pemindahan AND p.tipe_referensi = "pemindahan_barang"', 'left');

        // Filter berdasarkan role
        if ($user_role == 4) { // Admin Packing hanya bisa lihat data sendiri
            $this->db->where('p.id_user', $id_user);
        } else { // Super Admin dan Admin Perusahaan bisa lihat semua data
            $this->db->where('pb.id_perusahaan', $id_perusahaan);
        }

        // Filter berdasarkan tanggal
        if (!empty($filter['tanggal_awal']) && !empty($filter['tanggal_akhir'])) {
            $this->db->where('DATE(p.tanggal_packing) >=', $filter['tanggal_awal']);
            $this->db->where('DATE(p.tanggal_packing) <=', $filter['tanggal_akhir']);
        }

        // Filter berdasarkan user
        if (!empty($filter['id_user']) && $user_role != 4) { // Untuk non-Admin Packing
            $this->db->where('p.id_user', $filter['id_user']);
        }

        // Filter berdasarkan status
        if (!empty($filter['status'])) {
            $this->db->where('p.status', $filter['status']);
        }

        $this->db->order_by('p.tanggal_packing', 'DESC');

        return $this->db->get()->result();
    }

    public function get_packing($id_packing)
    {
        // Menggunakan select manual untuk menghindari masalah backtick
        $this->db->select('p.*, u.nama as user_nama', FALSE);
        $this->db->from('packing p');
        $this->db->join('user u', 'p.id_user = u.id_user');
        $this->db->where('p.id_packing', $id_packing);
        return $this->db->get()->row();
    }

    public function get_detail_packing($id_packing)
    {
        $this->db->select('dp.*, b.nama_barang, b.satuan');
        $this->db->from('detail_packing dp');
        $this->db->join('barang b', 'dp.id_barang = b.id_barang');
        $this->db->where('dp.id_packing', $id_packing);
        return $this->db->get()->result();
    }

    public function get_summary($filter)
    {
        $id_perusahaan = $this->session->userdata('id_perusahaan');

        $this->db->select('COUNT(DISTINCT p.id_packing) as total_packing, 
                         SUM(dp.jumlah) as total_barang');
        $this->db->from('packing p');
        $this->db->join('detail_packing dp', 'p.id_packing = dp.id_packing');

        // Join dengan tabel referensi untuk mendapatkan id_perusahaan
        if ($this->db->table_exists('pemindahan_barang')) {
            $this->db->join('pemindahan_barang pb', 'p.id_referensi = pb.id_pemindahan AND p.tipe_referensi = "pemindahan_barang"', 'left');
            $this->db->where('pb.id_perusahaan', $id_perusahaan);
        }

        if ($filter['tanggal_awal']) {
            $this->db->where('DATE(p.tanggal_packing) >=', $filter['tanggal_awal']);
        }

        if ($filter['tanggal_akhir']) {
            $this->db->where('DATE(p.tanggal_packing) <=', $filter['tanggal_akhir']);
        }

        if ($filter['id_user']) {
            $this->db->where('p.id_user', $filter['id_user']);
        }

        if ($filter['status']) {
            $this->db->where('p.status', $filter['status']);
        }

        return $this->db->get()->row();
    }

    public function get_packing_by_user($filter)
    {
        $id_perusahaan = $this->session->userdata('id_perusahaan');

        // Menggunakan select manual untuk menghindari masalah backtick
        $this->db->select('u.id_user, u.nama, 
                         COUNT(DISTINCT p.id_packing) as total_packing,
                         SUM(dp.jumlah) as total_barang', FALSE);
        $this->db->from('packing p');
        $this->db->join('detail_packing dp', 'p.id_packing = dp.id_packing');
        $this->db->join('user u', 'p.id_user = u.id_user');

        // Join dengan tabel referensi untuk mendapatkan id_perusahaan
        if ($this->db->table_exists('pemindahan_barang')) {
            $this->db->join('pemindahan_barang pb', 'p.id_referensi = pb.id_pemindahan AND p.tipe_referensi = "pemindahan_barang"', 'left');
            $this->db->where('pb.id_perusahaan', $id_perusahaan);
        }

        if ($filter['tanggal_awal']) {
            $this->db->where('DATE(p.tanggal_packing) >=', $filter['tanggal_awal']);
        }

        if ($filter['tanggal_akhir']) {
            $this->db->where('DATE(p.tanggal_packing) <=', $filter['tanggal_akhir']);
        }

        if ($filter['status']) {
            $this->db->where('p.status', $filter['status']);
        }

        $this->db->group_by('u.id_user, u.nama');
        $this->db->order_by('total_packing', 'DESC');
        return $this->db->get()->result();
    }
}