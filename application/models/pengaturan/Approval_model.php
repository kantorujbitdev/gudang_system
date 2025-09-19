<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Approval_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_all_approval_flows()
    {
        $this->db->select('af.*, r.nama_role');
        $this->db->from('approval_flow af');
        $this->db->join('role_user r', 'af.id_role = r.id_role');
        $this->db->where('af.status_aktif', 1);
        $this->db->order_by('af.tipe_transaksi, af.urutan');
        return $this->db->get()->result();
    }

    public function get_approval_flow_by_tipe($tipe_transaksi)
    {
        $this->db->select('af.*, r.nama_role');
        $this->db->from('approval_flow af');
        $this->db->join('role_user r', 'af.id_role = r.id_role');
        $this->db->where('af.tipe_transaksi', $tipe_transaksi);
        $this->db->where('af.status_aktif', 1);
        $this->db->order_by('af.urutan');
        return $this->db->get()->result();
    }

    public function get_all_roles()
    {
        $this->db->where('status_aktif', 1);
        $this->db->order_by('nama_role', 'ASC');
        return $this->db->get('role_user')->result();
    }

    public function insert($data)
    {
        $this->db->insert('approval_flow', $data);
        return $this->db->insert_id();
    }

    public function delete_by_tipe($tipe_transaksi)
    {
        $this->db->where('tipe_transaksi', $tipe_transaksi);
        return $this->db->delete('approval_flow');
    }
}