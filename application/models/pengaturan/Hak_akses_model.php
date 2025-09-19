<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Hak_akses_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_all_roles()
    {
        $this->db->order_by('nama_role', 'ASC');
        return $this->db->get('role_user')->result();
    }

    public function get_role($id_role)
    {
        $this->db->where('id_role', $id_role);
        return $this->db->get('role_user')->row();
    }

    public function get_all_hak_akses()
    {
        $this->db->select('ham.*, m.nama_menu, r.nama_role');
        $this->db->from('hak_akses_menu ham');
        $this->db->join('menu m', 'ham.id_menu = m.id_menu');
        $this->db->join('role_user r', 'ham.id_role = r.id_role');
        $this->db->order_by('r.nama_role, m.urutan');
        return $this->db->get()->result();
    }

    public function get_hak_akses_by_role($id_role)
    {
        $this->db->where('id_role', $id_role);
        return $this->db->get('hak_akses_menu')->result();
    }

    public function insert($data)
    {
        $this->db->insert('hak_akses_menu', $data);
        return $this->db->insert_id();
    }

    public function delete_by_role($id_role)
    {
        $this->db->where('id_role', $id_role);
        return $this->db->delete('hak_akses_menu');
    }

    public function insert_role($data)
    {
        $this->db->insert('role_user', $data);
        return $this->db->insert_id();
    }

    public function update_role($id_role, $data)
    {
        $this->db->where('id_role', $id_role);
        return $this->db->update('role_user', $data);
    }

    public function delete_role($id_role)
    {
        $this->db->where('id_role', $id_role);
        return $this->db->delete('role_user');
    }

    public function count_users_by_role($id_role)
    {
        $this->db->where('id_role', $id_role);
        $this->db->where('aktif', 1);
        return $this->db->count_all_results('user');
    }
}