<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Stok_gudang_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'stok_gudang';
        $this->primary_key = 'id_stok';
        $this->fillable = array('id_perusahaan', 'id_gudang', 'id_barang', 'jumlah', 'reserved');
        $this->timestamps = TRUE;
    }

    public function get_stok_by_gudang($id_gudang)
    {
        $this->db->select('sg.*, b.nama_barang, b.sku, b.harga_jual, k.nama_kategori');
        $this->db->from('stok_gudang sg');
        $this->db->join('barang b', 'sg.id_barang = b.id_barang', 'left');
        $this->db->join('kategori k', 'b.id_kategori = k.id_kategori', 'left');
        $this->db->where('sg.id_gudang', $id_gudang);
        $this->db->where('b.status_aktif', 1);
        $this->db->order_by('b.nama_barang', 'ASC');

        $query = $this->db->get();

        return ($query && $query->num_rows() > 0)
            ? $query->result()
            : [];
    }

    public function get_stok_by_barang($id_barang)
    {
        $this->db->select('sg.*, g.nama_gudang');
        $this->db->from('stok_gudang sg');
        $this->db->join('gudang g', 'sg.id_gudang = g.id_gudang', 'left');
        $this->db->where('sg.id_barang', $id_barang);
        $this->db->where('g.status_aktif', 1);

        $query = $this->db->get();

        return ($query && $query->num_rows() > 0)
            ? $query->result()
            : [];
    }

    public function update_stok($id_gudang, $id_barang, $jumlah, $reserved = 0)
    {
        $this->db->where('id_gudang', $id_gudang);
        $this->db->where('id_barang', $id_barang);

        $data = array(
            'jumlah' => $jumlah,
            'reserved' => $reserved,
            'updated_at' => date('Y-m-d H:i:s')
        );

        return $this->db->update($this->table, $data);
    }

    public function add_stok($id_perusahaan, $id_gudang, $id_barang, $jumlah = 0, $reserved = 0)
    {
        // Cek apakah stok sudah ada
        $this->db->where('id_gudang', $id_gudang);
        $this->db->where('id_barang', $id_barang);
        $query = $this->db->get($this->table);

        if ($query->num_rows() > 0) {
            // Update stok yang sudah ada
            $stok = $query->row();
            $new_jumlah = $stok->jumlah + $jumlah;
            $new_reserved = $stok->reserved + $reserved;

            return $this->update_stok($id_gudang, $id_barang, $new_jumlah, $new_reserved);
        } else {
            // Insert stok baru
            $data = array(
                'id_perusahaan' => $id_perusahaan,
                'id_gudang' => $id_gudang,
                'id_barang' => $id_barang,
                'jumlah' => $jumlah,
                'reserved' => $reserved
            );

            return $this->insert($data);
        }
    }

    public function get_total_stok_by_barang($id_barang)
    {
        $this->db->select('SUM(jumlah) as total_stok, SUM(reserved) as total_reserved');
        $this->db->from($this->table);
        $this->db->where('id_barang', $id_barang);

        $query = $this->db->get();

        if ($query && $query->num_rows() > 0) {
            $result = $query->row();
            return array(
                'total_stok' => $result->total_stok ?: 0,
                'total_reserved' => $result->total_reserved ?: 0
            );
        }

        return array(
            'total_stok' => 0,
            'total_reserved' => 0
        );
    }
}