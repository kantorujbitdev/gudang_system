<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Retur_pembelian_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_all()
    {
        $id_perusahaan = $this->session->userdata('id_perusahaan');

        $this->db->select('rp.*, u.nama as user_nama, pb.no_penerimaan, s.nama_supplier');
        $this->db->from('retur_pembelian rp');
        $this->db->join('user u', 'rp.id_user = u.id_user');
        $this->db->join('penerimaan_barang pb', 'rp.id_penerimaan = pb.id_penerimaan', 'left');
        $this->db->join('supplier s', 'rp.id_supplier = s.id_supplier');
        $this->db->join('supplier s2', 'pb.id_supplier = s2.id_supplier', 'left');
        $this->db->where('s2.id_perusahaan', $id_perusahaan);
        $this->db->order_by('rp.created_at', 'DESC');
        return $this->db->get()->result();
    }
    public function get($id_retur)
    {
        $this->db->select('rp.*, u.nama as user_nama, pb.no_penerimaan, s.nama_supplier');
        $this->db->from('retur_pembelian rp');
        $this->db->join('user u', 'rp.id_user = u.id_user');
        $this->db->join('penerimaan_barang pb', 'rp.id_penerimaan = pb.id_penerimaan', 'left');
        $this->db->join('supplier s', 'rp.id_supplier = s.id_supplier');
        $this->db->where('rp.id_retur_beli', $id_retur); // Perbaikan di sini
        return $this->db->get()->row();
    }
    public function get_detail($id_retur)
    {
        $this->db->select('dr.*, b.nama_barang, b.satuan, g.nama_gudang');
        $this->db->from('detail_retur_pembelian dr');
        $this->db->join('barang b', 'dr.id_barang = b.id_barang');
        $this->db->join('gudang g', 'dr.id_gudang = g.id_gudang');
        $this->db->where('dr.id_retur_beli', $id_retur);
        return $this->db->get()->result();
    }
    public function get_detail_by_id($id_detail)
    {
        $this->db->select('dr.*, b.nama_barang, g.nama_gudang');
        $this->db->from('detail_retur_pembelian dr');
        $this->db->join('barang b', 'dr.id_barang = b.id_barang');
        $this->db->join('gudang g', 'dr.id_gudang = g.id_gudang');
        $this->db->where('dr.id_detail_retur_beli', $id_detail);
        return $this->db->get()->row();
    }
    public function is_penerimaan_already_returned($id_penerimaan)
    {
        $this->db->where('id_penerimaan', $id_penerimaan);
        $this->db->where_in('status', ['Approved', 'Completed']);
        return $this->db->count_all_results('retur_pembelian') > 0;
    }

    public function get_penerimaan()
    {
        $id_perusahaan = $this->session->userdata('id_perusahaan');

        // Get penerimaan yang sudah di-retur
        $this->db->select('id_penerimaan');
        $this->db->from('retur_pembelian');
        $this->db->where_in('status', ['Approved', 'Completed']);
        $returned_penerimaan = $this->db->get_compiled_select();

        $this->db->select('pb.id_penerimaan, pb.no_penerimaan, s.nama_supplier');
        $this->db->from('penerimaan_barang pb');
        $this->db->join('supplier s', 'pb.id_supplier = s.id_supplier');
        $this->db->where('s.id_perusahaan', $id_perusahaan);
        $this->db->where('pb.status', 'Completed');
        $this->db->where("pb.id_penerimaan NOT IN ($returned_penerimaan)", NULL, FALSE);
        $this->db->order_by('pb.created_at', 'DESC');
        return $this->db->get()->result();
    }

    public function get_detail_penerimaan($id_penerimaan)
    {
        $this->db->select('dp.*, b.nama_barang, b.satuan, g.nama_gudang, g.id_gudang, dp.jumlah_diterima, s.nama_supplier, s.id_supplier');
        $this->db->from('detail_penerimaan dp');
        $this->db->join('barang b', 'dp.id_barang = b.id_barang');
        $this->db->join('gudang g', 'dp.id_gudang = g.id_gudang');
        $this->db->join('penerimaan_barang pb', 'dp.id_penerimaan = pb.id_penerimaan');
        $this->db->join('supplier s', 'pb.id_supplier = s.id_supplier');
        $this->db->where('dp.id_penerimaan', $id_penerimaan);
        return $this->db->get()->result();
    }
    public function insert($data)
    {
        $this->db->insert('retur_pembelian', $data);
        return $this->db->insert_id();
    }

    public function insert_detail($data)
    {
        $this->db->insert('detail_retur_pembelian', $data);
        return $this->db->insert_id();
    }

    public function update_status($id_retur, $status)
    {
        $this->db->where('id_retur_beli', $id_retur);
        $data = [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        return $this->db->update('retur_pembelian', $data);
    }

    public function update_detail($id_detail, $data)
    {
        $this->db->where('id_detail_retur_beli', $id_detail);
        return $this->db->update('detail_retur_pembelian', $data);
    }

    public function delete($id_retur)
    {
        $this->db->where('id_retur_beli', $id_retur);
        return $this->db->delete('retur_pembelian');
    }

    public function get_stok_barang($id_gudang, $id_barang)
    {
        $this->db->where('id_gudang', $id_gudang);
        $this->db->where('id_barang', $id_barang);
        return $this->db->get('stok_gudang')->row();
    }

    public function update_stok($id_gudang, $id_barang, $jumlah)
    {
        $this->db->where('id_gudang', $id_gudang);
        $this->db->where('id_barang', $id_barang);
        $data = [
            'jumlah' => $jumlah,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        return $this->db->update('stok_gudang', $data);
    }

    public function insert_log_stok($data)
    {
        $this->db->insert('log_stok', $data);
        return $this->db->insert_id();
    }
    public function insert_log_status($data)
    {
        $this->db->insert('log_status_transaksi', $data);
        return $this->db->insert_id();
    }
}