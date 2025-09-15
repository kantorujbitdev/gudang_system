<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Menu
{
    protected $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->model('Menu_model');
    }

    public function get_menu()
    {
        $id_role = $this->CI->session->userdata('id_role');

        // Cek apakah tabel menu dan hak_akses_menu ada
        if (!$this->CI->db->table_exists('menu') || !$this->CI->db->table_exists('hak_akses_menu')) {
            return [];
        }

        // Get parent menus
        $this->CI->db->select('m.*');
        $this->CI->db->from('menu m');
        $this->CI->db->join('hak_akses_menu ham', 'm.id_menu = ham.id_menu');
        $this->CI->db->where('ham.id_role', $id_role);
        $this->CI->db->where('ham.akses', 1);
        $this->CI->db->where('m.id_parent IS NULL');
        $this->CI->db->where('m.status_aktif', 1);
        $this->CI->db->order_by('m.urutan', 'ASC');

        $parent_menus = $this->CI->db->get()->result();

        $menu = array();

        foreach ($parent_menus as $parent) {
            // Get child menus
            $this->CI->db->select('m.*');
            $this->CI->db->from('menu m');
            $this->CI->db->join('hak_akses_menu ham', 'm.id_menu = ham.id_menu');
            $this->CI->db->where('ham.id_role', $id_role);
            $this->CI->db->where('ham.akses', 1);
            $this->CI->db->where('m.id_parent', $parent->id_menu);
            $this->CI->db->where('m.status_aktif', 1);
            $this->CI->db->order_by('m.urutan', 'ASC');

            $child_menus = $this->CI->db->get()->result();

            $menu_item = array(
                'id_menu' => $parent->id_menu,
                'nama_menu' => $parent->nama_menu,
                'url' => $parent->url,
                'icon' => $parent->icon,
                'children' => $child_menus
            );

            $menu[] = $menu_item;
        }

        return $menu;
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