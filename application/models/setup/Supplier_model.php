<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Supplier_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'supplier';
        $this->primary_key = 'id_supplier';
        $this->fillable = array('id_perusahaan', 'nama_supplier', 'alamat', 'telepon', 'email', 'status_aktif');
        $this->timestamps = TRUE;
        $this->soft_delete = TRUE;
    }

    public function get_all()
    {
        $this->db->order_by('created_at', 'DESC');
        return $this->db->get($this->table)->result();
    }

    public function get_active()
    {
        $this->db->where('status_aktif', 1);
        $this->db->order_by('nama_supplier', 'ASC');
        return $this->db->get($this->table)->result();
    }

    public function get_with_perusahaan()
    {
        $this->db->select('s.*, p.nama_perusahaan');
        $this->db->from('supplier s');
        $this->db->join('perusahaan p', 's.id_perusahaan = p.id_perusahaan', 'left');
        $this->db->order_by('s.created_at', 'DESC');

        $query = $this->db->get();

        return ($query && $query->num_rows() > 0)
            ? $query->result()
            : [];
    }

    public function get_detail($id_supplier)
    {
        $this->db->select('s.*, p.nama_perusahaan');
        $this->db->from('supplier s');
        $this->db->join('perusahaan p', 's.id_perusahaan = p.id_perusahaan', 'left');
        $this->db->where('s.id_supplier', $id_supplier);

        $query = $this->db->get();

        return ($query && $query->num_rows() > 0)
            ? $query->row()
            : null;
    }

    public function update_status($id, $status)
    {
        $this->db->where('id_supplier', $id);
        $data = [
            'status_aktif' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        return $this->db->update('supplier', $data);
    }

    public function get($id_supplier)
    {
        $user_role = $this->session->userdata('id_role');
        $user_perusahaan = $this->session->userdata('id_perusahaan');

        $this->db->select('s.*, p.nama_perusahaan');
        $this->db->from('supplier s');
        $this->db->join('perusahaan p', 's.id_perusahaan = p.id_perusahaan', 'left');
        $this->db->where('s.id_supplier', $id_supplier);

        // Filter berdasarkan role user
        if ($user_role != 1) { // Bukan Super Admin
            $this->db->where('s.id_perusahaan', $user_perusahaan);
        }

        $this->db->where('s.deleted_at IS NULL');
        return $this->db->get()->row();
    }

    public function check_unique_name($nama_supplier, $id_perusahaan, $id_supplier = NULL)
    {
        $this->db->where('nama_supplier', $nama_supplier);
        $this->db->where('id_perusahaan', $id_perusahaan);
        $this->db->where('deleted_at IS NULL');
        if ($id_supplier) {
            $this->db->where('id_supplier !=', $id_supplier);
        }
        return $this->db->get($this->table)->num_rows() == 0;
    }

    public function get_by_perusahaan($id_perusahaan)
    {
        $this->db->where('id_perusahaan', $id_perusahaan);
        $this->db->where('deleted_at IS NULL');
        $this->db->where('status_aktif', 1);
        $this->db->order_by('nama_supplier', 'ASC');
        return $this->db->get($this->table)->result();
    }
}