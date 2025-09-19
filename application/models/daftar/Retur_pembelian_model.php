<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Retur_pembelian_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_supplier()
    {
        $id_perusahaan = $this->session->userdata('id_perusahaan');
        $this->db->where('id_perusahaan', $id_perusahaan);
        $this->db->where('status_aktif', 1);
        $this->db->order_by('nama_supplier', 'ASC');
        return $this->db->get('supplier')->result();
    }

    public function get_filtered($filter)
    {
        $id_perusahaan = $this->session->userdata('id_perusahaan');

        $this->db->select('rp.*, u.nama as user_nama, p.no_pembelian, s.nama_supplier');
        $this->db->from('retur_pembelian rp');
        $this->db->join('user u', 'rp.id_user = u.id_user');
        $this->db->join('pembelian p', 'rp.id_pembelian = p.id_pembelian', 'left');
        $this->db->join('supplier s', 'rp.id_supplier = s.id_supplier');
        $this->db->where('rp.id_perusahaan', $id_perusahaan);

        if ($filter['tanggal_awal']) {
            $this->db->where('DATE(rp.tanggal_retur) >=', $filter['tanggal_awal']);
        }

        if ($filter['tanggal_akhir']) {
            $this->db->where('DATE(rp.tanggal_retur) <=', $filter['tanggal_akhir']);
        }

        if ($filter['status']) {
            $this->db->where('rp.status', $filter['status']);
        }

        if ($filter['id_supplier']) {
            $this->db->where('rp.id_supplier', $filter['id_supplier']);
        }

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
}