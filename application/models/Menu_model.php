<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Menu_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_menu_flat($id_role = NULL)
    {
        $this->db->select('m.*');
        $this->db->from('menu m');

        // Jika bukan Super Admin, join dengan hak_akses_menu
        if ($id_role != 1) {
            $this->db->join('hak_akses_menu ham', 'm.id_menu = ham.id_menu AND ham.id_role = ' . $id_role . ' AND ham.akses = 1', 'inner');
        }

        $this->db->where('m.status_aktif', 1);
        $this->db->order_by('m.urutan', 'ASC');

        $query = $this->db->get();

        // Debug: log query
        log_message('debug', 'Menu Query: ' . $this->db->last_query());

        return $query->result();
    }

    public function get_menu_tree($id_role = NULL)
    {
        $menus = $this->get_menu_flat($id_role);

        // Build menu tree
        $menu_tree = array();
        foreach ($menus as $menu) {
            if ($menu->id_parent == NULL) {
                $menu_tree[$menu->id_menu] = $menu;
                $menu_tree[$menu->id_menu]->children = array();
            }
        }

        foreach ($menus as $menu) {
            if ($menu->id_parent != NULL && isset($menu_tree[$menu->id_parent])) {
                $menu_tree[$menu->id_parent]->children[] = $menu;
            }
        }

        return $menu_tree;
    }

    public function check_access($id_role, $menu_url)
    {
        // Super Admin memiliki akses ke semua menu
        if ($id_role == 1) {
            return TRUE;
        }

        $this->db->select('m.id_menu');
        $this->db->from('menu m');
        $this->db->join('hak_akses_menu ham', 'm.id_menu = ham.id_menu');
        $this->db->where('ham.id_role', $id_role);
        $this->db->where('ham.akses', 1);
        $this->db->where('m.url', $menu_url);
        $this->db->where('m.status_aktif', 1);

        $query = $this->db->get();
        return $query->num_rows() > 0;
    }

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