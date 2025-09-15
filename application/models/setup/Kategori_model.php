<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Kategori_model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->table = 'kategori';
        $this->primary_key = 'id_kategori';
        $this->fillable = array('id_perusahaan', 'nama_kategori', 'deskripsi', 'status_aktif');
    }

    public function get_all()
    {
        $filter = get_company_filter();

        $this->db->select('k.*, COUNT(b.id_barang) as total_barang');
        $this->db->from('kategori k');
        $this->db->join('barang b', 'k.id_kategori = b.id_kategori', 'left');

        if ($filter) {
            $this->db->where($filter);
        }

        $this->db->group_by('k.id_kategori');
        $this->db->order_by('k.nama_kategori', 'ASC');

        return $this->db->get()->result();
    }

    public function get($id_kategori)
    {
        $filter = get_company_filter();

        $this->db->where('id_kategori', $id_kategori);

        if ($filter) {
            $this->db->where($filter);
        }

        return $this->db->get($this->table)->row();
    }
}