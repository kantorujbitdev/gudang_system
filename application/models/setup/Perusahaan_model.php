<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Perusahaan_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'perusahaan';
        $this->primary_key = 'id_perusahaan';
        $this->fillable = array('nama_perusahaan', 'alamat', 'telepon', 'email', 'status_aktif');
        $this->timestamps = TRUE;
        $this->soft_delete = TRUE;
    }

    public function get_all()
    {
        $this->db->order_by('created_at', 'DESC');
        return $this->db->get($this->table)->result();
    }
    public function update_status($id, $status)
    {
        $this->db->where('id_perusahaan', $id);
        $data = [
            'status_aktif' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        return $this->db->update('perusahaan', $data);
    }
    public function get_active()
    {
        $this->db->where('status_aktif', 1);
        $this->db->order_by('created_at', 'DESC');
        return $this->db->get($this->table)->result();
    }

    public function get_with_stats()
    {
        $this->db->select('p.*,
                       COUNT(DISTINCT u.id_user) as total_user,
                       COUNT(DISTINCT g.id_gudang) as total_gudang,
                       COUNT(DISTINCT b.id_barang) as total_barang', false);
        $this->db->from('perusahaan p');
        $this->db->join('user u', 'p.id_perusahaan = u.id_perusahaan', 'left');
        $this->db->join('gudang g', 'p.id_perusahaan = g.id_perusahaan', 'left');
        $this->db->join('barang b', 'p.id_perusahaan = b.id_perusahaan', 'left');
        $this->db->group_by('p.id_perusahaan');
        $this->db->order_by('p.created_at', 'DESC');

        $query = $this->db->get();

        return ($query && $query->num_rows() > 0)
            ? $query->result()
            : [];
    }

    public function check_unique_name($nama_perusahaan, $id_perusahaan = NULL)
    {
        $this->db->where('nama_perusahaan', $nama_perusahaan);
        if ($id_perusahaan) {
            $this->db->where('id_perusahaan !=', $id_perusahaan);
        }
        return $this->db->get($this->table)->num_rows() == 0;
    }
}