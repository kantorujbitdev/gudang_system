<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Menu
{
    protected $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
    }

    public function get_menu($id_role = NULL)
    {
        // Jika Super Admin, ambil semua menu
        if ($id_role == 1) {
            $this->CI->db->where('status_aktif', 1);
        } else {
            $this->CI->db->select('m.*');
            $this->CI->db->from('menu m');
            $this->CI->db->join('hak_akses_menu ham', 'm.id_menu = ham.id_menu');
            $this->CI->db->where('ham.id_role', $id_role);
            $this->CI->db->where('ham.akses', 1);
            $this->CI->db->where('m.status_aktif', 1);
        }
        $query = $this->CI->db->get('menu');
        $menus = $query->result();

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

    public function get_menu_flat($id_role = NULL)
    {
        // Untuk keperluan debugging
        if ($id_role == 1) {
            $this->CI->db->where('status_aktif', 1);
        } else {
            $this->CI->db->select('m.*');
            $this->CI->db->from('menu m');
            $this->CI->db->join('hak_akses_menu ham', 'm.id_menu = ham.id_menu');
            $this->CI->db->where('ham.id_role', $id_role);
            $this->CI->db->where('ham.akses', 1);
            $this->CI->db->where('m.status_aktif', 1);
        }
        $this->CI->db->order_by('urutan', 'ASC');
        return $this->CI->db->get('menu')->result();
    }

    public function get_breadcrumb()
    {
        $uri = $this->CI->uri->segment_array();
        $breadcrumb = array();

        // Add Home
        $breadcrumb[] = array(
            'title' => 'Home',
            'url' => site_url('dashboard')
        );

        // Cek apakah tabel menu ada
        if (!$this->CI->db->table_exists('menu')) {
            return $breadcrumb;
        }

        // Build breadcrumb from URI
        $path = '';
        foreach ($uri as $segment) {
            $path .= $segment . '/';

            // Get menu title
            $this->CI->db->select('nama_menu');
            $this->CI->db->from('menu');
            $this->CI->db->where('url', rtrim($path, '/'));

            $query = $this->CI->db->get();

            if ($query->num_rows() > 0) {
                $breadcrumb[] = array(
                    'title' => $query->row()->nama_menu,
                    'url' => site_url(rtrim($path, '/'))
                );
            }
        }

        return $breadcrumb;
    }
}