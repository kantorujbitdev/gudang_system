<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pelanggan_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'pelanggan';
        $this->primary_key = 'id_pelanggan';
        $this->fillable = array('id_perusahaan', 'nama_pelanggan', 'alamat', 'telepon', 'email', 'tipe_pelanggan', 'status_aktif');
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
        $this->db->order_by('nama_pelanggan', 'ASC');
        return $this->db->get($this->table)->result();
    }

    public function get_with_perusahaan()
    {
        $this->db->select('p.*, pr.nama_perusahaan');
        $this->db->from('pelanggan p');
        $this->db->join('perusahaan pr', 'p.id_perusahaan = pr.id_perusahaan', 'left');
        $this->db->order_by('p.created_at', 'DESC');

        $query = $this->db->get();

        return ($query && $query->num_rows() > 0)
            ? $query->result()
            : [];
    }

    public function get_detail($id_pelanggan)
    {
        $this->db->select('p.*, pr.nama_perusahaan');
        $this->db->from('pelanggan p');
        $this->db->join('perusahaan pr', 'p.id_perusahaan = pr.id_perusahaan', 'left');
        $this->db->where('p.id_pelanggan', $id_pelanggan);

        $query = $this->db->get();

        return ($query && $query->num_rows() > 0)
            ? $query->row()
            : null;
    }

    public function update_status($id, $status)
    {
        $this->db->where('id_pelanggan', $id);
        $data = [
            'status_aktif' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        return $this->db->update('pelanggan', $data);
    }

    public function get($id_pelanggan)
    {
        $user_role = $this->session->userdata('id_role');
        $user_perusahaan = $this->session->userdata('id_perusahaan');

        $this->db->select('p.*, pr.nama_perusahaan');
        $this->db->from('pelanggan p');
        $this->db->join('perusahaan pr', 'p.id_perusahaan = pr.id_perusahaan', 'left');
        $this->db->where('p.id_pelanggan', $id_pelanggan);

        // Filter berdasarkan role user
        if ($user_role != 1) { // Bukan Super Admin
            $this->db->where('p.id_perusahaan', $user_perusahaan);
        }

        $this->db->where('p.deleted_at IS NULL');
        return $this->db->get()->row();
    }

    public function check_unique_name($nama_pelanggan, $id_perusahaan, $id_pelanggan = NULL)
    {
        $this->db->where('nama_pelanggan', $nama_pelanggan);
        $this->db->where('id_perusahaan', $id_perusahaan);
        $this->db->where('deleted_at IS NULL');
        if ($id_pelanggan) {
            $this->db->where('id_pelanggan !=', $id_pelanggan);
        }
        return $this->db->get($this->table)->num_rows() == 0;
    }
    public function get_transaksi_by_pelanggan($id_pelanggan, $limit = 10)
    {
        $this->db->select('pb.id_pemindahan, pb.no_transaksi, pb.tanggal_pemindahan as tanggal, pb.status, 
                     COUNT(dpb.id_barang) as total_item');
        $this->db->from('pemindahan_barang pb');
        $this->db->join('detail_pemindahan_barang dpb', 'pb.id_pemindahan = dpb.id_pemindahan');
        $this->db->where('pb.id_pelanggan', $id_pelanggan);
        $this->db->where('pb.deleted_at IS NULL');
        $this->db->group_by('pb.id_pemindahan, pb.no_transaksi, pb.tanggal_pemindahan, pb.status');
        $this->db->order_by('pb.tanggal_pemindahan', 'DESC');
        $this->db->limit($limit);

        return $this->db->get()->result();
    }
    public function get_by_perusahaan($id_perusahaan)
    {
        $this->db->where('id_perusahaan', $id_perusahaan);
        $this->db->where('deleted_at IS NULL');
        $this->db->where('status_aktif', 1);
        $this->db->order_by('nama_pelanggan', 'ASC');
        return $this->db->get($this->table)->result();
    }

    public function get_distributors($id_perusahaan = NULL)
    {
        if ($id_perusahaan) {
            $this->db->where('id_perusahaan', $id_perusahaan);
        }

        $this->db->where('tipe_pelanggan', 'distributor');
        $this->db->where('deleted_at IS NULL');
        $this->db->where('status_aktif', 1);
        $this->db->order_by('nama_pelanggan', 'ASC');
        return $this->db->get($this->table)->result();
    }

    public function get_konsumens($id_perusahaan = NULL)
    {
        if ($id_perusahaan) {
            $this->db->where('id_perusahaan', $id_perusahaan);
        }

        $this->db->where('tipe_pelanggan', 'konsumen');
        $this->db->where('deleted_at IS NULL');
        $this->db->where('status_aktif', 1);
        $this->db->order_by('nama_pelanggan', 'ASC');
        return $this->db->get($this->table)->result();
    }
}