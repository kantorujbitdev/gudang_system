<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Sistem_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_all_pengaturan()
    {
        $this->db->order_by('key', 'ASC');
        return $this->db->get('pengaturan_sistem')->result();
    }

    public function get_pengaturan($key)
    {
        $this->db->where('key', $key);
        return $this->db->get('pengaturan_sistem')->row();
    }

    public function update_pengaturan($key, $data)
    {
        $this->db->where('key', $key);
        return $this->db->update('pengaturan_sistem', $data);
    }

    public function insert_pengaturan($data)
    {
        $this->db->insert('pengaturan_sistem', $data);
        return $this->db->insert_id();
    }
}