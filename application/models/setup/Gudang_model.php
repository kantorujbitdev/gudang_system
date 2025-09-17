<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Gudang_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'gudang';
        $this->primary_key = 'id_gudang';
        $this->fillable = array('id_perusahaan', 'nama_gudang', 'alamat', 'telepon', 'status_aktif');
    }

    public function get_by_perusahaan($id_perusahaan)
    {
        $this->db->where('id_perusahaan', $id_perusahaan);
        $this->db->where('status_aktif', 1);
        $this->db->order_by('nama_gudang', 'ASC');
        return $this->db->get($this->table)->result();
    }
}