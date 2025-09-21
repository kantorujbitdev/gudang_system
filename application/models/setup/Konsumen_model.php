<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Konsumen_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_all()
    {
        $user_role = $this->session->userdata('id_role');
        $id_perusahaan = $this->session->userdata('id_perusahaan');

        $this->db->select('k.*, tk.nama_toko_konsumen');
        $this->db->from('konsumen k');
        $this->db->join('toko_konsumen tk', 'k.id_toko_konsumen = tk.id_toko_konsumen');

        // Filter berdasarkan role user
        if ($user_role != 1) { // Bukan Super Admin
            $this->db->where('k.id_perusahaan', $id_perusahaan);
        }

        $this->db->where('k.status_aktif', 1);
        $this->db->order_by('k.nama_konsumen', 'ASC');

        return $this->db->get()->result();
    }

    public function get($id_konsumen)
    {
        $this->db->select('k.*, tk.nama_toko_konsumen');
        $this->db->from('konsumen k');
        $this->db->join('toko_konsumen tk', 'k.id_toko_konsumen = tk.id_toko_konsumen');
        $this->db->where('k.id_konsumen', $id_konsumen);
        $this->db->where('k.status_aktif', 1);

        return $this->db->get()->row();
    }

    public function get_by_perusahaan($id_perusahaan)
    {
        $this->db->select('k.*, tk.nama_toko_konsumen');
        $this->db->from('konsumen k');
        $this->db->join('toko_konsumen tk', 'k.id_toko_konsumen = tk.id_toko_konsumen');
        $this->db->where('k.id_perusahaan', $id_perusahaan);
        $this->db->where('k.status_aktif', 1);
        $this->db->order_by('k.nama_konsumen', 'ASC');

        return $this->db->get()->result();
    }

    public function insert($data)
    {
        $this->db->insert('konsumen', $data);
        return $this->db->insert_id();
    }

    public function update($id_konsumen, $data)
    {
        $this->db->where('id_konsumen', $id_konsumen);
        return $this->db->update('konsumen', $data);
    }
}