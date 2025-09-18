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

    public function get_stok($id_barang, $id_gudang)
    {
        $this->db->where('id_barang', $id_barang);
        $this->db->where('id_gudang', $id_gudang);
        $query = $this->db->get($this->table);

        if ($query && $query->num_rows() > 0) {
            return $query->row();
        }

        return NULL;
    }

    public function update_stok($id_barang, $id_gudang, $jumlah, $reserved = NULL)
    {
        $stok = $this->get_stok($id_barang, $id_gudang);

        if ($stok) {
            $data = array(
                'jumlah' => $jumlah,
                'updated_at' => date('Y-m-d H:i:s')
            );

            if ($reserved !== NULL) {
                $data['reserved'] = $reserved;
            }

            $this->db->where('id_stok', $stok->id_stok);
            return $this->db->update($this->table, $data);
        } else {
            // Jika stok tidak ada, buat baru
            $data = array(
                'id_perusahaan' => $this->session->userdata('id_perusahaan'),
                'id_gudang' => $id_gudang,
                'id_barang' => $id_barang,
                'jumlah' => $jumlah,
                'reserved' => $reserved !== NULL ? $reserved : 0
            );

            return $this->insert($data);
        }
    }

    public function tambah_stok($id_barang, $id_gudang, $jumlah)
    {
        $stok = $this->get_stok($id_barang, $id_gudang);

        if ($stok) {
            $new_jumlah = $stok->jumlah + $jumlah;
            return $this->update_stok($id_barang, $id_gudang, $new_jumlah);
        } else {
            $data = array(
                'id_perusahaan' => $this->session->userdata('id_perusahaan'),
                'id_gudang' => $id_gudang,
                'id_barang' => $id_barang,
                'jumlah' => $jumlah,
                'reserved' => 0
            );

            return $this->insert($data);
        }
    }

    public function kurangi_stok($id_barang, $id_gudang, $jumlah)
    {
        $stok = $this->get_stok($id_barang, $id_gudang);

        if ($stok && $stok->jumlah >= $jumlah) {
            $new_jumlah = $stok->jumlah - $jumlah;
            return $this->update_stok($id_barang, $id_gudang, $new_jumlah);
        }

        return FALSE;
    }

    public function get_by_gudang($id_gudang)
    {
        $this->db->select('sg.*, b.nama_barang, b.sku, k.nama_kategori');
        $this->db->from('stok_gudang sg');
        $this->db->join('barang b', 'sg.id_barang = b.id_barang', 'left');
        $this->db->join('kategori k', 'b.id_kategori = k.id_kategori', 'left');
        $this->db->where('sg.id_gudang', $id_gudang);
        $this->db->where('b.status_aktif', 1);
        $this->db->order_by('b.nama_barang', 'ASC');

        $query = $this->db->get();
        return ($query && $query->num_rows() > 0) ? $query->result() : [];
    }

    public function get_by_barang($id_barang)
    {
        $this->db->select('sg.*, g.nama_gudang');
        $this->db->from('stok_gudang sg');
        $this->db->join('gudang g', 'sg.id_gudang = g.id_gudang', 'left');
        $this->db->where('sg.id_barang', $id_barang);
        $this->db->where('g.status_aktif', 1);
        $this->db->order_by('g.nama_gudang', 'ASC');

        $query = $this->db->get();
        return ($query && $query->num_rows() > 0) ? $query->result() : [];
    }

    public function get_by_perusahaan($id_perusahaan)
    {
        $this->db->select('sg.*, b.nama_barang, b.sku, g.nama_gudang');
        $this->db->from('stok_gudang sg');
        $this->db->join('barang b', 'sg.id_barang = b.id_barang', 'left');
        $this->db->join('gudang g', 'sg.id_gudang = g.id_gudang', 'left');
        $this->db->where('sg.id_perusahaan', $id_perusahaan);
        $this->db->where('b.status_aktif', 1);
        $this->db->where('g.status_aktif', 1);
        $this->db->order_by('b.nama_barang', 'ASC');

        $query = $this->db->get();
        return ($query && $query->num_rows() > 0) ? $query->result() : [];
    }

    public function get_stok_minimum($id_perusahaan, $min_stok = 10)
    {
        $this->db->select('sg.*, b.nama_barang, b.sku, g.nama_gudang');
        $this->db->from('stok_gudang sg');
        $this->db->join('barang b', 'sg.id_barang = b.id_barang', 'left');
        $this->db->join('gudang g', 'sg.id_gudang = g.id_gudang', 'left');
        $this->db->where('sg.id_perusahaan', $id_perusahaan);
        $this->db->where('sg.jumlah <=', $min_stok);
        $this->db->where('b.status_aktif', 1);
        $this->db->where('g.status_aktif', 1);
        $this->db->order_by('sg.jumlah', 'ASC');

        $query = $this->db->get();
        return ($query && $query->num_rows() > 0) ? $query->result() : [];
    }
}