<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pembelian_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'pembelian';
        $this->primary_key = 'id_pembelian';
        $this->fillable = array('id_perusahaan', 'no_pembelian', 'id_user', 'id_supplier', 'tanggal_pembelian', 'tanggal_estimasi', 'keterangan', 'status');
        $this->timestamps = TRUE;
        $this->soft_delete = TRUE;
    }

    public function get_with_details($id_pembelian = NULL)
    {
        $this->db->select('p.*, s.nama_supplier, u.nama as nama_user');
        $this->db->from('pembelian p');
        $this->db->join('supplier s', 'p.id_supplier = s.id_supplier', 'left');
        $this->db->join('user u', 'p.id_user = u.id_user', 'left');

        if ($id_pembelian) {
            $this->db->where('p.id_pembelian', $id_pembelian);
            $query = $this->db->get();
            return ($query && $query->num_rows() > 0) ? $query->row() : NULL;
        } else {
            $this->db->order_by('p.created_at', 'DESC');
            $query = $this->db->get();
            return ($query && $query->num_rows() > 0) ? $query->result() : [];
        }
    }

    public function get_detail_pembelian($id_pembelian)
    {
        $this->db->select('dp.*, b.nama_barang, b.sku');
        $this->db->from('detail_pembelian dp');
        $this->db->join('barang b', 'dp.id_barang = b.id_barang', 'left');
        $this->db->where('dp.id_pembelian', $id_pembelian);

        $query = $this->db->get();
        return ($query && $query->num_rows() > 0) ? $query->result() : [];
    }

    public function count_by_supplier($id_supplier)
    {
        $this->db->where('id_supplier', $id_supplier);
        $this->db->where('deleted_at IS NULL');
        return $this->db->count_all_results('pembelian');
    }

    public function get_by_supplier($id_supplier, $limit = NULL)
    {
        $this->db->where('id_supplier', $id_supplier);
        $this->db->where('deleted_at IS NULL');
        $this->db->order_by('tanggal_pembelian', 'DESC');

        if ($limit) {
            $this->db->limit($limit);
        }

        return $this->db->get('pembelian')->result();
    }

    public function update_status($id_pembelian, $status, $keterangan = NULL)
    {
        $data = array(
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        );

        if ($keterangan) {
            $data['keterangan'] = $keterangan;
        }

        $this->db->where('id_pembelian', $id_pembelian);
        return $this->db->update('pembelian', $data);
    }

    public function log_status_change($id_pembelian, $status, $id_user, $keterangan = NULL)
    {
        $data = array(
            'id_transaksi' => $id_pembelian,
            'tipe_transaksi' => 'pembelian',
            'id_user' => $id_user,
            'status' => $status,
            'keterangan' => $keterangan
        );

        return $this->db->insert('log_status_transaksi', $data);
    }

    public function get_by_status($status, $id_perusahaan = NULL)
    {
        $this->db->select('p.*, s.nama_supplier, u.nama as nama_user');
        $this->db->from('pembelian p');
        $this->db->join('supplier s', 'p.id_supplier = s.id_supplier', 'left');
        $this->db->join('user u', 'p.id_user = u.id_user', 'left');
        $this->db->where('p.status', $status);

        if ($id_perusahaan) {
            $this->db->where('p.id_perusahaan', $id_perusahaan);
        }

        $this->db->where('p.deleted_at IS NULL');
        $this->db->order_by('p.created_at', 'DESC');

        $query = $this->db->get();
        return ($query && $query->num_rows() > 0) ? $query->result() : [];
    }

    public function get_with_perusahaan($id_perusahaan = NULL)
    {
        $this->db->select('p.*, pr.nama_perusahaan, s.nama_supplier, u.nama as nama_user');
        $this->db->from('pembelian p');
        $this->db->join('perusahaan pr', 'p.id_perusahaan = pr.id_perusahaan', 'left');
        $this->db->join('supplier s', 'p.id_supplier = s.id_supplier', 'left');
        $this->db->join('user u', 'p.id_user = u.id_user', 'left');

        if ($id_perusahaan) {
            $this->db->where('p.id_perusahaan', $id_perusahaan);
        }

        $this->db->where('p.deleted_at IS NULL');
        $this->db->order_by('p.created_at', 'DESC');

        $query = $this->db->get();
        return ($query && $query->num_rows() > 0) ? $query->result() : [];
    }

}