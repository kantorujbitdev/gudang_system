<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Log_stok_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'log_stok';
        $this->primary_key = 'id_log';
        $this->fillable = array('id_barang', 'id_user', 'id_perusahaan', 'id_gudang', 'jenis', 'jumlah', 'sisa_stok', 'keterangan', 'tanggal', 'id_referensi', 'tipe_referensi');
        $this->timestamps = FALSE;
    }

    public function insert_log($data)
    {
        return $this->insert($data);
    }

    public function get_by_barang($id_barang, $id_gudang = NULL)
    {
        $this->db->select('ls.*, b.nama_barang, g.nama_gudang, u.nama as nama_user');
        $this->db->from('log_stok ls');
        $this->db->join('barang b', 'ls.id_barang = b.id_barang', 'left');
        $this->db->join('gudang g', 'ls.id_gudang = g.id_gudang', 'left');
        $this->db->join('user u', 'ls.id_user = u.id_user', 'left');
        $this->db->where('ls.id_barang', $id_barang);

        if ($id_gudang) {
            $this->db->where('ls.id_gudang', $id_gudang);
        }

        $this->db->order_by('ls.tanggal', 'DESC');

        $query = $this->db->get();
        return ($query && $query->num_rows() > 0) ? $query->result() : [];
    }

    public function get_by_perusahaan($id_perusahaan, $limit = 50)
    {
        $this->db->select('ls.*, b.nama_barang, g.nama_gudang, u.nama as nama_user');
        $this->db->from('log_stok ls');
        $this->db->join('barang b', 'ls.id_barang = b.id_barang', 'left');
        $this->db->join('gudang g', 'ls.id_gudang = g.id_gudang', 'left');
        $this->db->join('user u', 'ls.id_user = u.id_user', 'left');
        $this->db->where('ls.id_perusahaan', $id_perusahaan);
        $this->db->order_by('ls.tanggal', 'DESC');
        $this->db->limit($limit);

        $query = $this->db->get();
        return ($query && $query->num_rows() > 0) ? $query->result() : [];
    }

    public function get_by_referensi($id_referensi, $tipe_referensi)
    {
        $this->db->select('ls.*, b.nama_barang, g.nama_gudang');
        $this->db->from('log_stok ls');
        $this->db->join('barang b', 'ls.id_barang = b.id_barang', 'left');
        $this->db->join('gudang g', 'ls.id_gudang = g.id_gudang', 'left');
        $this->db->where('ls.id_referensi', $id_referensi);
        $this->db->where('ls.tipe_referensi', $tipe_referensi);

        $query = $this->db->get();
        return ($query && $query->num_rows() > 0) ? $query->result() : [];
    }

    public function get_mutasi_barang($id_perusahaan, $start_date = NULL, $end_date = NULL)
    {
        $this->db->select('ls.*, b.nama_barang, b.sku, g.nama_gudang as gudang_asal, 
                          gt.nama_gudang as gudang_tujuan, u.nama as nama_user');
        $this->db->from('log_stok ls');
        $this->db->join('barang b', 'ls.id_barang = b.id_barang', 'left');
        $this->db->join('gudang g', 'ls.id_gudang = g.id_gudang', 'left');
        $this->db->join('user u', 'ls.id_user = u.id_user', 'left');
        $this->db->join('gudang gt', 'ls.id_gudang_tujuan = gt.id_gudang', 'left', TRUE);
        $this->db->where('ls.id_perusahaan', $id_perusahaan);

        if ($start_date) {
            $this->db->where('DATE(ls.tanggal) >=', $start_date);
        }

        if ($end_date) {
            $this->db->where('DATE(ls.tanggal) <=', $end_date);
        }

        $this->db->order_by('ls.tanggal', 'DESC');

        $query = $this->db->get();
        return ($query && $query->num_rows() > 0) ? $query->result() : [];
    }
}