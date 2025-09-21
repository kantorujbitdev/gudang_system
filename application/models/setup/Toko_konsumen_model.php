<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Toko_konsumen_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_all()
    {
        $this->db->where('status_aktif', 1);
        return $this->db->get('toko_konsumen')->result();
    }

    public function get($id_toko_konsumen)
    {
        $this->db->where('id_toko_konsumen', $id_toko_konsumen);
        $this->db->where('status_aktif', 1);
        return $this->db->get('toko_konsumen')->row();
    }
}