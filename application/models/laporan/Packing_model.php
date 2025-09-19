<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Packing_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_filtered($filter)
    {
        $id_perusahaan = $this->session->userdata('id_perusahaan');

        $this->db->select('p.id_packing, p.tanggal_packing, p.tipe_referensi, p.id_referensi, p.status,
                         u.nama as user_nama, dp.id_barang, b.nama_barang, dp.jumlah');
        $this->db->from('packing p');
        $this->db->join('user u', 'p.id_user = u.id_user');
        $this->db->join('detail_packing dp', 'p.id_packing = dp.id_packing');
        $this->db->join('barang b', 'dp.id_barang = b.id_barang');
        $this->db->where('p.id_perusahaan', $id_perusahaan);

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

    public function get_packing($id_packing)
    {
        $this->db->select('p.*, u.nama as user_nama');
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
        $this->db->where('p.id_perusahaan', $id_perusahaan);

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

        $this->db->select('u.id_user, u.nama, 
                         COUNT(DISTINCT p.id_packing) as total_packing,
                         SUM(dp.jumlah) as total_barang');
        $this->db->from('packing p');
        $this->db->join('detail_packing dp', 'p.id_packing = dp.id_packing');
        $this->db->join('user u', 'p.id_user = u.id_user');
        $this->db->where('p.id_perusahaan', $id_perusahaan);

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