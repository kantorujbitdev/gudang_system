<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Summary_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_filtered($filter)
    {
        $id_perusahaan = $this->session->userdata('id_perusahaan');

        $this->db->select('sg.*, b.nama_barang, b.sku, g.nama_gudang, 
                         (sg.jumlah - sg.reserved) as stok_tersedia');
        $this->db->from('stok_gudang sg');
        $this->db->join('barang b', 'sg.id_barang = b.id_barang');
        $this->db->join('gudang g', 'sg.id_gudang = g.id_gudang');
        $this->db->where('sg.id_perusahaan', $id_perusahaan);

        if ($filter['id_barang']) {
            $this->db->where('sg.id_barang', $filter['id_barang']);
        }

        if ($filter['id_gudang']) {
            $this->db->where('sg.id_gudang', $filter['id_gudang']);
        }

        $this->db->order_by('g.nama_gudang', 'ASC');
        $this->db->order_by('b.nama_barang', 'ASC');
        return $this->db->get()->result();
    }

    public function get_summary($filter)
    {
        $id_perusahaan = $this->session->userdata('id_perusahaan');

        $this->db->select('
            COUNT(DISTINCT sg.id_barang) as total_barang,
            COUNT(DISTINCT sg.id_gudang) as total_gudang,
            SUM(sg.jumlah) as total_stok,
            SUM(sg.reserved) as total_reserved,
            SUM(sg.jumlah - sg.reserved) as total_tersedia,
            SUM(CASE WHEN sg.jumlah = 0 THEN 1 ELSE 0 END) as stok_habis,
            SUM(CASE WHEN sg.jumlah > 0 AND sg.jumlah <= 10 THEN 1 ELSE 0 END) as stok_rendah,
            SUM(CASE WHEN sg.jumlah > 10 AND sg.jumlah <= 50 THEN 1 ELSE 0 END) as stok_sedang,
            SUM(CASE WHEN sg.jumlah > 50 THEN 1 ELSE 0 END) as stok_cukup
        ');
        $this->db->from('stok_gudang sg');
        $this->db->join('barang b', 'sg.id_barang = b.id_barang');
        $this->db->join('gudang g', 'sg.id_gudang = g.id_gudang');
        $this->db->where('sg.id_perusahaan', $id_perusahaan);

        if ($filter['id_barang']) {
            $this->db->where('sg.id_barang', $filter['id_barang']);
        }

        if ($filter['id_gudang']) {
            $this->db->where('sg.id_gudang', $filter['id_gudang']);
        }

        return $this->db->get()->row();
    }

    public function get_stok_by_barang($filter)
    {
        $id_perusahaan = $this->session->userdata('id_perusahaan');

        $this->db->select('b.id_barang, b.nama_barang, b.satuan,
                         SUM(sg.jumlah) as total_stok,
                         SUM(sg.reserved) as total_reserved,
                         SUM(sg.jumlah - sg.reserved) as total_tersedia');
        $this->db->from('stok_gudang sg');
        $this->db->join('barang b', 'sg.id_barang = b.id_barang');
        $this->db->where('sg.id_perusahaan', $id_perusahaan);

        if ($filter['id_gudang']) {
            $this->db->where('sg.id_gudang', $filter['id_gudang']);
        }

        $this->db->group_by('b.id_barang, b.nama_barang, b.satuan');
        $this->db->order_by('b.nama_barang', 'ASC');
        return $this->db->get()->result();
    }

    public function get_stok_by_gudang($filter)
    {
        $id_perusahaan = $this->session->userdata('id_perusahaan');

        $this->db->select('g.id_gudang, g.nama_gudang,
                         COUNT(DISTINCT sg.id_barang) as total_barang,
                         SUM(sg.jumlah) as total_stok,
                         SUM(sg.reserved) as total_reserved,
                         SUM(sg.jumlah - sg.reserved) as total_tersedia');
        $this->db->from('stok_gudang sg');
        $this->db->join('gudang g', 'sg.id_gudang = g.id_gudang');
        $this->db->where('sg.id_perusahaan', $id_perusahaan);

        if ($filter['id_barang']) {
            $this->db->where('sg.id_barang', $filter['id_barang']);
        }

        $this->db->group_by('g.id_gudang, g.nama_gudang');
        $this->db->order_by('g.nama_gudang', 'ASC');
        return $this->db->get()->result();
    }

    public function get_stok_kritis($filter)
    {
        $id_perusahaan = $this->session->userdata('id_perusahaan');

        $this->db->select('sg.*, b.nama_barang, b.sku, g.nama_gudang, 
                         (sg.jumlah - sg.reserved) as stok_tersedia');
        $this->db->from('stok_gudang sg');
        $this->db->join('barang b', 'sg.id_barang = b.id_barang');
        $this->db->join('gudang g', 'sg.id_gudang = g.id_gudang');
        $this->db->where('sg.id_perusahaan', $id_perusahaan);
        $this->db->where('sg.jumlah <=', 10); // Stok kritis jika <= 10

        if ($filter['id_barang']) {
            $this->db->where('sg.id_barang', $filter['id_barang']);
        }

        if ($filter['id_gudang']) {
            $this->db->where('sg.id_gudang', $filter['id_gudang']);
        }

        $this->db->order_by('sg.jumlah', 'ASC');
        return $this->db->get()->result();
    }
}