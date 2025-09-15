<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Stok_model extends CI_Model
{

    public function get_total_barang()
    {
        $filter = get_company_filter();

        $this->db->where('aktif', 1);
        if ($filter) {
            $this->db->where($filter);
        }

        return $this->db->count_all_results('barang');
    }

    public function get_total_gudang()
    {
        $filter = get_company_filter();

        $this->db->where('status_aktif', 1);
        if ($filter) {
            $this->db->where($filter);
        }

        return $this->db->count_all_results('gudang');
    }

    public function get_stok_menipis()
    {
        $filter = get_company_filter();

        $this->db->select('b.nama_barang, g.nama_gudang, sg.jumlah, 10 as min_stok');
        $this->db->from('stok_gudang sg');
        $this->db->join('barang b', 'sg.id_barang = b.id_barang');
        $this->db->join('gudang g', 'sg.id_gudang = g.id_gudang');
        $this->db->where('sg.jumlah <=', 10);
        $this->db->where('b.aktif', 1);
        $this->db->where('g.status_aktif', 1);

        if ($filter) {
            $this->db->where($filter);
        }

        return $this->db->get()->result();
    }

    public function get_transaksi_hari_ini()
    {
        $filter = get_company_filter();

        // Cek apakah tabel log_stok ada
        if (!$this->db->table_exists('log_stok')) {
            return 0;
        }

        $this->db->where('DATE(tanggal)', date('Y-m-d'));
        if ($filter) {
            $this->db->where($filter);
        }

        return $this->db->count_all_results('log_stok');
    }

    public function get_chart_stok()
    {
        $filter = get_company_filter();

        $this->db->select('g.nama_gudang, SUM(sg.jumlah) as total_stok');
        $this->db->from('stok_gudang sg');
        $this->db->join('gudang g', 'sg.id_gudang = g.id_gudang');
        $this->db->join('barang b', 'sg.id_barang = b.id_barang');
        $this->db->where('b.aktif', 1);
        $this->db->where('g.status_aktif', 1);

        if ($filter) {
            $this->db->where($filter);
        }

        $this->db->group_by('g.id_gudang');

        return $this->db->get()->result();
    }

    public function get_chart_transaksi()
    {
        $filter = get_company_filter();

        // Cek apakah tabel log_stok ada
        if (!$this->db->table_exists('log_stok')) {
            return [
                'masuk' => 0,
                'keluar' => 0,
                'transfer' => 0
            ];
        }

        $this->db->select('jenis, COUNT(*) as total');
        $this->db->from('log_stok');
        $this->db->where('MONTH(tanggal)', date('m'));
        $this->db->where('YEAR(tanggal)', date('Y'));
        $this->db->where_in('jenis', ['masuk', 'keluar', 'transfer_masuk']);

        if ($filter) {
            $this->db->where($filter);
        }

        $this->db->group_by('jenis');

        $result = $this->db->get()->result();

        $chart_data = [
            'masuk' => 0,
            'keluar' => 0,
            'transfer' => 0
        ];

        foreach ($result as $row) {
            if ($row->jenis == 'masuk') {
                $chart_data['masuk'] = $row->total;
            } elseif ($row->jenis == 'keluar') {
                $chart_data['keluar'] = $row->total;
            } elseif ($row->jenis == 'transfer_masuk') {
                $chart_data['transfer'] = $row->total;
            }
        }

        return $chart_data;
    }
}