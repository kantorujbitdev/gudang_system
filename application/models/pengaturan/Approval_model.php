<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Approval_flow_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'approval_flow';
        $this->primary_key = 'id_approval';
        $this->fillable = array('tipe_transaksi', 'status_dari', 'status_ke', 'id_role', 'urutan', 'status_aktif');
        $this->timestamps = FALSE;
    }

    public function get_by_tipe_transaksi($tipe_transaksi)
    {
        $this->db->where('tipe_transaksi', $tipe_transaksi);
        $this->db->where('status_aktif', 1);
        $this->db->order_by('urutan', 'ASC');

        $query = $this->db->get($this->table);
        return ($query && $query->num_rows() > 0) ? $query->result() : [];
    }

    public function get_next_approval($tipe_transaksi, $current_status)
    {
        $this->db->where('tipe_transaksi', $tipe_transaksi);
        $this->db->where('status_dari', $current_status);
        $this->db->where('status_aktif', 1);
        $this->db->order_by('urutan', 'ASC');

        $query = $this->db->get($this->table);
        return ($query && $query->num_rows() > 0) ? $query->row() : NULL;
    }

    public function can_change_status($tipe_transaksi, $current_status, $new_status, $id_role)
    {
        $this->db->where('tipe_transaksi', $tipe_transaksi);
        $this->db->where('status_dari', $current_status);
        $this->db->where('status_ke', $new_status);
        $this->db->where('id_role', $id_role);
        $this->db->where('status_aktif', 1);

        return $this->db->count_all_results($this->table) > 0;
    }
}