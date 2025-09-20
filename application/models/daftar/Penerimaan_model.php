<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Penerimaan_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_gudang()
    {
        $id_perusahaan = $this->session->userdata('id_perusahaan');
        $user_role = $this->session->userdata('id_role');
        if ($user_role != 1) {
            // Bukan Super Admin
            $this->db->where('id_perusahaan', $id_perusahaan);
        }
        $this->db->where('status_aktif', 1);
        $this->db->order_by('nama_gudang', 'ASC');
        return $this->db->get('gudang')->result();
    }

    public function get_supplier()
    {

        $id_perusahaan = $this->session->userdata('id_perusahaan');
        // Filter berdasarkan role user
        $user_role = $this->session->userdata('id_role');
        if ($user_role != 1) { // Bukan Super Admin
            $this->db->where('id_perusahaan', $id_perusahaan);
        }
        $this->db->where('status_aktif', 1);
        $this->db->order_by('nama_supplier', 'ASC');
        return $this->db->get('supplier')->result();
    }

    public function get_filtered($filter)
    {
        $id_perusahaan = $this->session->userdata('id_perusahaan');

        $this->db->select('pb.*, u.nama as user_nama, g.nama_gudang, s.nama_supplier');
        $this->db->from('penerimaan_barang pb');
        $this->db->join('user u', 'pb.id_user = u.id_user');
        $this->db->join('gudang g', 'pb.id_gudang = g.id_gudang');
        $this->db->join('supplier s', 'pb.id_supplier = s.id_supplier');
        // Filter berdasarkan role user
        $user_role = $this->session->userdata('id_role');
        if ($user_role != 1) { // Bukan Super Admin
            $this->db->where('pb.id_perusahaan', $id_perusahaan);
        }

        if ($filter['tanggal_awal']) {
            $this->db->where('DATE(pb.tanggal_penerimaan) >=', $filter['tanggal_awal']);
        }

        if ($filter['tanggal_akhir']) {
            $this->db->where('DATE(pb.tanggal_penerimaan) <=', $filter['tanggal_akhir']);
        }

        if ($filter['status']) {
            $this->db->where('pb.status', $filter['status']);
        }

        if ($filter['id_gudang']) {
            $this->db->where('pb.id_gudang', $filter['id_gudang']);
        }

        if ($filter['id_supplier']) {
            $this->db->where('pb.id_supplier', $filter['id_supplier']);
        }

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
}