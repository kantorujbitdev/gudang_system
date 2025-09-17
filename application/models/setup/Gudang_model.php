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
        $this->timestamps = TRUE;
    }

    public function get_all()
    {
        $user_role = $this->session->userdata('id_role');
        $user_perusahaan = $this->session->userdata('id_perusahaan');

        $this->db->select('g.*, p.nama_perusahaan');
        $this->db->from('gudang g');
        $this->db->join('perusahaan p', 'g.id_perusahaan = p.id_perusahaan', 'left');

        // Filter berdasarkan role user
        if ($user_role != 1) { // Bukan Super Admin
            $this->db->where('g.id_perusahaan', $user_perusahaan);
        }

        $this->db->order_by('g.nama_gudang', 'ASC');
        return $this->db->get()->result();
    }

    public function get_by_perusahaan($id_perusahaan)
    {
        $this->db->where('id_perusahaan', $id_perusahaan);
        $this->db->where('status_aktif', 1);
        $this->db->order_by('nama_gudang', 'ASC');
        return $this->db->get($this->table)->result();
    }

    public function get_with_stok($id_perusahaan = NULL)
    {
        $this->db->select('g.*, p.nama_perusahaan, COUNT(sg.id_stok) as total_items, SUM(sg.jumlah) as total_stok');
        $this->db->from('gudang g');
        $this->db->join('perusahaan p', 'g.id_perusahaan = p.id_perusahaan', 'left');
        $this->db->join('stok_gudang sg', 'g.id_gudang = sg.id_gudang', 'left');

        if ($id_perusahaan) {
            $this->db->where('g.id_perusahaan', $id_perusahaan);
        }

        $this->db->group_by('g.id_gudang');
        $this->db->order_by('g.nama_gudang', 'ASC');
        return $this->db->get()->result();
    }
}