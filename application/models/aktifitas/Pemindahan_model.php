<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pemindahan_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_all()
    {
        $user_role = $this->session->userdata('id_role');

        $id_perusahaan = $this->session->userdata('id_perusahaan');
        $this->db->select('ts.*, u.nama as user_nama, ga.nama_gudang as gudang_asal, gt.nama_gudang as gudang_tujuan');
        $this->db->from('transfer_stok ts');
        $this->db->join('user u', 'ts.id_user = u.id_user');
        $this->db->join('gudang ga', 'ts.id_gudang_asal = ga.id_gudang');
        $this->db->join('gudang gt', 'ts.id_gudang_tujuan = gt.id_gudang', 'left');
        // Filter berdasarkan role user
        if ($user_role != 1) { // Bukan Super Admin
            $this->db->where('pb.id_perusahaan', $id_perusahaan);
        }
        return $this->db->get()->result();
    }

    public function get($id_transfer)
    {
        $this->db->select('ts.*, u.nama as user_nama, ga.nama_gudang as gudang_asal, gt.nama_gudang as gudang_tujuan, p.nama_pelanggan');
        $this->db->from('transfer_stok ts');
        $this->db->join('user u', 'ts.id_user = u.id_user');
        $this->db->join('gudang ga', 'ts.id_gudang_asal = ga.id_gudang');
        $this->db->join('gudang gt', 'ts.id_gudang_tujuan = gt.id_gudang', 'left');
        $this->db->join('pelanggan p', 'ts.id_pelanggan = p.id_pelanggan', 'left');
        $this->db->where('ts.id_transfer', $id_transfer);
        return $this->db->get()->row();
    }

    public function get_detail($id_transfer)
    {
        $this->db->select('dts.*, b.nama_barang, b.satuan');
        $this->db->from('detail_transfer_stok dts');
        $this->db->join('barang b', 'dts.id_barang = b.id_barang');
        $this->db->where('dts.id_transfer', $id_transfer);
        return $this->db->get()->result();
    }

    public function insert($data)
    {
        $this->db->insert('transfer_stok', $data);
        return $this->db->insert_id();
    }

    public function insert_detail($data)
    {
        $this->db->insert('detail_transfer_stok', $data);
        return $this->db->insert_id();
    }

    public function update($id_transfer, $data)
    {
        $this->db->where('id_transfer', $id_transfer);
        return $this->db->update('transfer_stok', $data);
    }

    public function update_status($id_transfer, $status)
    {
        $this->db->where('id_transfer', $id_transfer);
        $data = [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        return $this->db->update('transfer_stok', $data);
    }

    public function delete($id_transfer)
    {
        $this->db->where('id_transfer', $id_transfer);
        return $this->db->delete('transfer_stok');
    }

    public function delete_detail($id_transfer)
    {
        $this->db->where('id_transfer', $id_transfer);
        return $this->db->delete('detail_transfer_stok');
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