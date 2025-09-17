<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'user';
        $this->primary_key = 'id_user';
        $this->fillable = array('nama', 'username', 'password_hash', 'id_role', 'id_perusahaan', 'email', 'telepon', 'status_aktif');
        $this->protected = array('password_hash');
    }

    public function get_by_perusahaan($id_perusahaan)
    {
        $this->db->select('u.*, r.nama_role');
        $this->db->from('user u');
        $this->db->join('role_user r', 'u.id_role = r.id_role');
        $this->db->where('u.id_perusahaan', $id_perusahaan);
        $this->db->where('u.aktif', 1);
        $this->db->order_by('u.nama', 'ASC');
        return $this->db->get()->result();
    }
}