<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pemindahan_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_gudang()
    {
        $id_perusahaan = $this->session->userdata('id_perusahaan');
        $this->db->where('id_perusahaan', $id_perusahaan);
        $this->db->where('status_aktif', 1);
        $this->db->order_by('nama_gudang', 'ASC');
        return $this->db->get('gudang')->result();
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

        $this->db->select('ts.*, u.nama as user_nama, ga.nama_gudang as gudang_asal, gt.nama_gudang as gudang_tujuan, p.nama_pelanggan');
        $this->db->from('transfer_stok ts');
        $this->db->join('user u', 'ts.id_user = u.id_user');
        $this->db->join('gudang ga', 'ts.id_gudang_asal = ga.id_gudang');
        $this->db->join('gudang gt', 'ts.id_gudang_tujuan = gt.id_gudang', 'left');
        $this->db->join('pelanggan p', 'ts.id_pelanggan = p.id_pelanggan', 'left');
        $this->db->where('ts.id_perusahaan', $id_perusahaan);

        if ($filter['tanggal_awal']) {
            $this->db->where('DATE(ts.tanggal) >=', $filter['tanggal_awal']);
        }

        if ($filter['tanggal_akhir']) {
            $this->db->where('DATE(ts.tanggal) <=', $filter['tanggal_akhir']);
        }

        if ($filter['status']) {
            $this->db->where('ts.status', $filter['status']);
        }

        if ($filter['id_gudang']) {
            $this->db->where('ts.id_gudang_asal', $filter['id_gudang']);
        }

        if ($filter['id_pelanggan']) {
            $this->db->where('ts.id_pelanggan', $filter['id_pelanggan']);
        }

        $this->db->order_by('ts.created_at', 'DESC');
        return $this->db->get()->result();
    }

    public function get($id_transfer)
    {
        $this->db->select('ts.*, u.nama as user_nama, ga.nama_gudang as gudang_asal, gt.nama_gudang as gudang_tujuan, p.nama_pelanggan');
        $this->db->from('transfer_stok ts');
        $this->db->join('user u', 'ts.id_user = u.id_user');
        $this->db->join('gudang ga', 'ts.id_gudang_asal = ga.id_gudang');
        $this->db->join('gudang gt', 'ts.id_gudang_tujuan = gt.id_gudang', 'left');
        $this->db->join('pelanggan p', 'ts.id_pelanggan = p.id_pelanggan', 'left');
        $this->db->where('ts.id_transfer', $id_transfer);
        return $this->db->get()->row();
    }

    public function get_detail($id_transfer)
    {
        $this->db->select('dts.*, b.nama_barang, b.satuan');
        $this->db->from('detail_transfer_stok dts');
        $this->db->join('barang b', 'dts.id_barang = b.id_barang');
        $this->db->where('dts.id_transfer', $id_transfer);
        return $this->db->get()->result();
    }
}