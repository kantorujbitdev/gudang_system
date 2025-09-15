<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Menu_model extends CI_Model
{

    public function get_all_menus()
    {
        // Cek apakah tabel menu ada
        if (!$this->db->table_exists('menu')) {
            return [];
        }

        $this->db->order_by('urutan', 'ASC');
        return $this->db->get('menu')->result();
    }

    public function get_menu_by_id($id_menu)
    {
        // Cek apakah tabel menu ada
        if (!$this->db->table_exists('menu')) {
            return FALSE;
        }

        return $this->db->get_where('menu', array('id_menu' => $id_menu))->row();
    }

    public function get_parent_menus()
    {
        // Cek apakah tabel menu ada
        if (!$this->db->table_exists('menu')) {
            return [];
        }

        $this->db->where('id_parent IS NULL');
        $this->db->order_by('urutan', 'ASC');
        return $this->db->get('menu')->result();
    }

    public function get_child_menus($id_parent)
    {
        // Cek apakah tabel menu ada
        if (!$this->db->table_exists('menu')) {
            return [];
        }

        $this->db->where('id_parent', $id_parent);
        $this->db->order_by('urutan', 'ASC');
        return $this->db->get('menu')->result();
    }

    public function insert_menu($data)
    {
        // Cek apakah tabel menu ada
        if (!$this->db->table_exists('menu')) {
            return FALSE;
        }

        return $this->db->insert('menu', $data);
    }

    public function update_menu($id_menu, $data)
    {
        // Cek apakah tabel menu ada
        if (!$this->db->table_exists('menu')) {
            return FALSE;
        }

        $this->db->where('id_menu', $id_menu);
        return $this->db->update('menu', $data);
    }

    public function delete_menu($id_menu)
    {
        // Cek apakah tabel menu ada
        if (!$this->db->table_exists('menu')) {
            return FALSE;
        }

        $this->db->where('id_menu', $id_menu);
        return $this->db->delete('menu');
    }

    public function get_menu_access($id_role)
    {
        // Cek apakah tabel menu dan hak_akses_menu ada
        if (!$this->db->table_exists('menu') || !$this->db->table_exists('hak_akses_menu')) {
            return [];
        }

        $this->db->select('m.*, ham.akses');
        $this->db->from('menu m');
        $this->db->join('hak_akses_menu ham', 'm.id_menu = ham.id_menu', 'left');
        $this->db->where('ham.id_role', $id_role);
        $this->db->order_by('m.urutan', 'ASC');

        return $this->db->get()->result();
    }

    public function update_menu_access($id_role, $id_menu, $akses)
    {
        // Cek apakah tabel hak_akses_menu ada
        if (!$this->db->table_exists('hak_akses_menu')) {
            return FALSE;
        }

        $this->db->where('id_role', $id_role);
        $this->db->where('id_menu', $id_menu);

        $query = $this->db->get('hak_akses_menu');

        if ($query->num_rows() > 0) {
            $this->db->where('id_role', $id_role);
            $this->db->where('id_menu', $id_menu);
            return $this->db->update('hak_akses_menu', array('akses' => $akses));
        } else {
            return $this->db->insert('hak_akses_menu', array(
                'id_role' => $id_role,
                'id_menu' => $id_menu,
                'akses' => $akses
            ));
        }
    }
}