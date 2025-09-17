<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Kategori_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'kategori';
        $this->primary_key = 'id_kategori';
        $this->fillable = array('id_perusahaan', 'nama_kategori', 'deskripsi', 'status_aktif', 'deleted_at');
        $this->protected = array('id_kategori');
        $this->timestamps = FALSE;
    }

    public function get_all()
    {
        $user_role = $this->session->userdata('id_role');
        $user_perusahaan = $this->session->userdata('id_perusahaan');

        $this->db->select('k.*, p.nama_perusahaan, COUNT(b.id_barang) as total_barang');
        $this->db->from('kategori k');
        $this->db->join('perusahaan p', 'k.id_perusahaan = p.id_perusahaan', 'left');
        $this->db->join('barang b', 'k.id_kategori = b.id_kategori AND b.deleted_at IS NULL', 'left');

        // Filter berdasarkan role user
        if ($user_role != 1) { // Bukan Super Admin
            $this->db->where('k.id_perusahaan', $user_perusahaan);
        }

        $this->db->where('k.deleted_at IS NULL');
        $this->db->group_by('k.id_kategori');
        $this->db->order_by('k.nama_kategori', 'ASC');
        return $this->db->get()->result();
    }

    public function get($id_kategori)
    {
        $user_role = $this->session->userdata('id_role');
        $user_perusahaan = $this->session->userdata('id_perusahaan');

        $this->db->select('k.*, p.nama_perusahaan');
        $this->db->from('kategori k');
        $this->db->join('perusahaan p', 'k.id_perusahaan = p.id_perusahaan', 'left');
        $this->db->where('k.id_kategori', $id_kategori);

        // Filter berdasarkan role user
        if ($user_role != 1) { // Bukan Super Admin
            $this->db->where('k.id_perusahaan', $user_perusahaan);
        }

        $this->db->where('k.deleted_at IS NULL');
        return $this->db->get()->row();
    }

    public function check_unique_name($nama_kategori, $id_perusahaan, $id_kategori = NULL)
    {
        $this->db->where('nama_kategori', $nama_kategori);
        $this->db->where('id_perusahaan', $id_perusahaan);
        $this->db->where('deleted_at IS NULL');
        if ($id_kategori) {
            $this->db->where('id_kategori !=', $id_kategori);
        }
        return $this->db->get($this->table)->num_rows() == 0;
    }

    public function get_barang_by_kategori($id_kategori)
    {
        $user_role = $this->session->userdata('id_role');
        $user_perusahaan = $this->session->userdata('id_perusahaan');

        $this->db->select('b.*, sg.jumlah as stok');
        $this->db->from('barang b');
        $this->db->join('stok_gudang sg', 'b.id_barang = sg.id_barang', 'left');
        $this->db->where('b.id_kategori', $id_kategori);
        $this->db->where('b.deleted_at IS NULL');
        $this->db->where('b.status_aktif', 1);

        // Filter berdasarkan role user
        if ($user_role != 1) { // Bukan Super Admin
            $this->db->where('b.id_perusahaan', $user_perusahaan);
        }

        return $this->db->get()->result();
    }

    public function get_by_perusahaan($id_perusahaan)
    {
        $this->db->where('id_perusahaan', $id_perusahaan);
        $this->db->where('deleted_at IS NULL');
        $this->db->where('status_aktif', 1);
        $this->db->order_by('nama_kategori', 'ASC');
        return $this->db->get($this->table)->result();
    }
}