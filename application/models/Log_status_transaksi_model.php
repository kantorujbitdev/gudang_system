<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Log_status_transaksi_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'log_status_transaksi';
        $this->primary_key = 'id_log';
        $this->fillable = array('id_transaksi', 'tipe_transaksi', 'id_user', 'status', 'tanggal', 'keterangan');
        $this->timestamps = FALSE;
    }

    public function insert_log($data)
    {
        return $this->insert($data);
    }

    public function get_by_transaksi($id_transaksi, $tipe_transaksi)
    {
        $this->db->select('lst.*, u.nama as nama_user');
        $this->db->from('log_status_transaksi lst');
        $this->db->join('user u', 'lst.id_user = u.id_user', 'left');
        $this->db->where('lst.id_transaksi', $id_transaksi);
        $this->db->where('lst.tipe_transaksi', $tipe_transaksi);
        $this->db->order_by('lst.tanggal', 'ASC');

        $query = $this->db->get();
        return ($query && $query->num_rows() > 0) ? $query->result() : [];
    }
}