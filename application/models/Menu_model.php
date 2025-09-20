<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Menu_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_menu_tree($id_role = NULL)
    {
        // Query dasar
        $this->db->select('m.*');

        if ($id_role == 1) {
            // Super Admin -> ambil semua menu
            $this->db->from('menu m');
        } else {
            // Role lain -> join dengan hak akses
            $this->db->from('menu m');
            $this->db->join('hak_akses_menu h', 'h.id_menu = m.id_menu AND h.id_role = ' . $id_role);
            $this->db->where('h.can_view', 1);
        }

        $this->db->where('m.status_aktif', 1);
        $this->db->order_by('m.urutan', 'ASC');

        $query = $this->db->get();
        $menus = $query->result();

        // Buat map id_menu -> menu
        $menu_map = array();
        foreach ($menus as $menu) {
            $menu->children = array(); // inisialisasi
            $menu_map[$menu->id_menu] = $menu;
        }

        // Susun tree
        $menu_tree = array();
        foreach ($menus as $menu) {
            if (!empty($menu->id_parent) && isset($menu_map[$menu->id_parent])) {
                $menu_map[$menu->id_parent]->children[] = $menu;
            } else {
                $menu_tree[] = $menu; // root menu
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

        $this->db->select('ham.can_view');
        $this->db->from('hak_akses_menu ham');
        $this->db->join('menu m', 'ham.id_menu = m.id_menu');
        $this->db->where('ham.id_role', $id_role);
        $this->db->where('m.url', $menu_url);

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->row()->can_view == 1;
        }

        return FALSE;
    }

    public function get_permission($id_role, $menu_url)
    {
        // Super Admin memiliki akses penuh
        if ($id_role == 1) {
            return (object) [
                'can_view' => 1,
                'can_create' => 1,
                'can_edit' => 1,
                'can_delete' => 1
            ];
        }

        $this->db->select('ham.can_view, ham.can_create, ham.can_edit, ham.can_delete');
        $this->db->from('hak_akses_menu ham');
        $this->db->join('menu m', 'ham.id_menu = m.id_menu');
        $this->db->where('ham.id_role', $id_role);
        $this->db->where('m.url', $menu_url);

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->row();
        }

        return (object) [
            'can_view' => 0,
            'can_create' => 0,
            'can_edit' => 0,
            'can_delete' => 0
        ];
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