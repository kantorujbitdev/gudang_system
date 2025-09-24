<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Gudang_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'gudang';
        $this->primary_key = 'id_gudang';
        $this->fillable = array('id_perusahaan', 'nama_gudang', 'alamat', 'telepon', 'created_by', 'status_aktif');
        $this->timestamps = TRUE;

    }

    public function get_all()
    {
        $this->db->order_by('created_at', 'DESC');
        return $this->db->get($this->table)->result();
    }

    public function get_active()
    {
        $this->db->where('status_aktif', 1);
        $this->db->order_by('created_at', 'DESC');
        return $this->db->get($this->table)->result();
    }

    public function get_with_perusahaan()
    {
        $this->db->select('g.*, p.nama_perusahaan');
        $this->db->from('gudang g');
        $this->db->join('perusahaan p', 'g.id_perusahaan = p.id_perusahaan', 'left');
        $this->db->order_by('g.created_at', 'DESC');

        $query = $this->db->get();

        return ($query && $query->num_rows() > 0)
            ? $query->result()
            : [];
    }

    public function get_detail($id_gudang)
    {
        $this->db->select('g.*, p.nama_perusahaan, u.nama as created_by_name');
        $this->db->from('gudang g');
        $this->db->join('perusahaan p', 'g.id_perusahaan = p.id_perusahaan', 'left');
        $this->db->join('user u', 'g.created_by = u.id_user', 'left');
        $this->db->where('g.id_gudang', $id_gudang);

        $query = $this->db->get();

        return ($query && $query->num_rows() > 0)
            ? $query->row()
            : null;
    }

    public function get_by_perusahaan($id_perusahaan)
    {
        $this->db->where('id_perusahaan', $id_perusahaan);
        $this->db->where('status_aktif', 1);
        $this->db->order_by('nama_gudang', 'ASC');
        return $this->db->get($this->table)->result();
    }

    public function update_status($id, $status)
    {
        $this->db->where('id_gudang', $id);
        $data = [
            'status_aktif' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        return $this->db->update('gudang', $data);
    }

    public function check_unique_name($nama_gudang, $id_perusahaan, $id_gudang = NULL)
    {
        $this->db->where('nama_gudang', $nama_gudang);
        $this->db->where('id_perusahaan', $id_perusahaan);
        if ($id_gudang) {
            $this->db->where('id_gudang !=', $id_gudang);
        }
        return $this->db->get($this->table)->num_rows() == 0;
    }
}