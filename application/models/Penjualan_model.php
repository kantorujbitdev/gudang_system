<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Penjualan_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'penjualan';
        $this->primary_key = 'id_penjualan';
        $this->fillable = array('id_perusahaan', 'no_invoice', 'id_user', 'id_pelanggan', 'tanggal_penjualan', 'keterangan', 'status');
        $this->timestamps = TRUE;
        $this->soft_delete = TRUE;
    }

    public function get_with_details($id_penjualan = NULL)
    {
        $this->db->select('p.*, pl.nama_pelanggan, u.nama as nama_user');
        $this->db->from('penjualan p');
        $this->db->join('pelanggan pl', 'p.id_pelanggan = pl.id_pelanggan', 'left');
        $this->db->join('user u', 'p.id_user = u.id_user', 'left');

        if ($id_penjualan) {
            $this->db->where('p.id_penjualan', $id_penjualan);
            $query = $this->db->get();
            return ($query && $query->num_rows() > 0) ? $query->row() : NULL;
        } else {
            $this->db->order_by('p.created_at', 'DESC');
            $query = $this->db->get();
            return ($query && $query->num_rows() > 0) ? $query->result() : [];
        }
    }

    public function get_detail_penjualan($id_penjualan)
    {
        $this->db->select('dp.*, b.nama_barang, b.sku, g.nama_gudang');
        $this->db->from('detail_penjualan dp');
        $this->db->join('barang b', 'dp.id_barang = b.id_barang', 'left');
        $this->db->join('gudang g', 'dp.id_gudang = g.id_gudang', 'left');
        $this->db->where('dp.id_penjualan', $id_penjualan);

        $query = $this->db->get();
        return ($query && $query->num_rows() > 0) ? $query->result() : [];
    }

    public function count_by_pelanggan($id_pelanggan)
    {
        $this->db->where('id_pelanggan', $id_pelanggan);
        $this->db->where('deleted_at IS NULL');
        return $this->db->count_all_results('penjualan');
    }

    public function get_by_pelanggan($id_pelanggan, $limit = NULL)
    {
        $this->db->where('id_pelanggan', $id_pelanggan);
        $this->db->where('deleted_at IS NULL');
        $this->db->order_by('tanggal_penjualan', 'DESC');

        if ($limit) {
            $this->db->limit($limit);
        }

        return $this->db->get('penjualan')->result();
    }

    public function update_status($id_penjualan, $status, $keterangan = NULL)
    {
        $data = array(
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        );

        if ($keterangan) {
            $data['keterangan'] = $keterangan;
        }

        $this->db->where('id_penjualan', $id_penjualan);
        return $this->db->update('penjualan', $data);
    }

    public function log_status_change($id_penjualan, $status, $id_user, $keterangan = NULL)
    {
        $data = array(
            'id_transaksi' => $id_penjualan,
            'tipe_transaksi' => 'penjualan',
            'id_user' => $id_user,
            'status' => $status,
            'keterangan' => $keterangan
        );

        return $this->db->insert('log_status_transaksi', $data);
    }

    public function get_by_status($status, $id_perusahaan = NULL)
    {
        $this->db->select('p.*, pl.nama_pelanggan, u.nama as nama_user');
        $this->db->from('penjualan p');
        $this->db->join('pelanggan pl', 'p.id_pelanggan = pl.id_pelanggan', 'left');
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
        $this->db->select('p.*, pr.nama_perusahaan, pl.nama_pelanggan, u.nama as nama_user');
        $this->db->from('penjualan p');
        $this->db->join('perusahaan pr', 'p.id_perusahaan = pr.id_perusahaan', 'left');
        $this->db->join('pelanggan pl', 'p.id_pelanggan = pl.id_pelanggan', 'left');
        $this->db->join('user u', 'p.id_user = u.id_user', 'left');

        if ($id_perusahaan) {
            $this->db->where('p.id_perusahaan', $id_perusahaan);
        }

        $this->db->where('p.deleted_at IS NULL');
        $this->db->order_by('p.created_at', 'DESC');

        $query = $this->db->get();
        return ($query && $query->num_rows() > 0) ? $query->result() : [];
    }

    public function get_by_user($id_user, $limit = NULL)
    {
        $this->db->select('p.*, pl.nama_pelanggan');
        $this->db->from('penjualan p');
        $this->db->join('pelanggan pl', 'p.id_pelanggan = pl.id_pelanggan', 'left');
        $this->db->where('p.id_user', $id_user);
        $this->db->where('p.deleted_at IS NULL');
        $this->db->order_by('p.created_at', 'DESC');

        if ($limit) {
            $this->db->limit($limit);
        }

        $query = $this->db->get();
        return ($query && $query->num_rows() > 0) ? $query->result() : [];
    }

    public function get_total_penjualan($id_perusahaan = NULL, $start_date = NULL, $end_date = NULL)
    {
        $this->db->select('SUM(dp.harga_jual * dp.jumlah) as total');
        $this->db->from('penjualan p');
        $this->db->join('detail_penjualan dp', 'p.id_penjualan = dp.id_penjualan', 'inner');

        if ($id_perusahaan) {
            $this->db->where('p.id_perusahaan', $id_perusahaan);
        }

        if ($start_date) {
            $this->db->where('DATE(p.tanggal_penjualan) >=', $start_date);
        }

        if ($end_date) {
            $this->db->where('DATE(p.tanggal_penjualan) <=', $end_date);
        }

        $this->db->where('p.deleted_at IS NULL');
        $this->db->where('p.status !=', 'Cancelled');

        $query = $this->db->get();
        $result = $query->row();

        return $result ? $result->total : 0;
    }
}