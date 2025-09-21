<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Stok_awal_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }
    public function get_barang_with_stok($id_perusahaan)
    {
        // Ambil semua barang aktif per perusahaan
        $this->db->select('b.id_barang, b.nama_barang, b.sku, k.nama_kategori');
        $this->db->from('barang b');
        $this->db->join('kategori k', 'b.id_kategori = k.id_kategori', 'left');
        $this->db->where('b.id_perusahaan', $id_perusahaan);
        $this->db->where('b.status_aktif', 1);
        $this->db->order_by('b.nama_barang', 'ASC');
        $barang = $this->db->get()->result();

        // Ambil semua gudang aktif per perusahaan
        $this->db->select('id_gudang, nama_gudang');
        $this->db->where('id_perusahaan', $id_perusahaan);
        $this->db->where('status_aktif', 1);
        $gudang = $this->db->get('gudang')->result();

        $result = array();

        foreach ($barang as $b) {
            $row = array(
                'id_barang' => $b->id_barang,
                'nama_barang' => $b->nama_barang,
                'sku' => $b->sku,
                'nama_kategori' => $b->nama_kategori,
                'gudang' => array()
            );

            foreach ($gudang as $g) {
                // Cek stok awal
                $stok_awal = $this->db->get_where('stok_awal', array(
                    'id_barang' => $b->id_barang,
                    'id_gudang' => $g->id_gudang,
                    'id_perusahaan' => $id_perusahaan
                ))->row();

                // Cek stok terkini
                $stok_terkini = $this->db->get_where('stok_gudang', array(
                    'id_barang' => $b->id_barang,
                    'id_gudang' => $g->id_gudang,
                    'id_perusahaan' => $id_perusahaan
                ))->row();

                $row['gudang'][] = array(
                    'id_gudang' => $g->id_gudang,
                    'nama_gudang' => $g->nama_gudang,
                    'stok_awal' => $stok_awal ? $stok_awal->qty_awal : 0,
                    'stok_terkini' => $stok_terkini ? $stok_terkini->jumlah : 0,
                    'has_stok_awal' => $stok_awal ? true : false
                );
            }

            $result[] = $row;
        }

        return $result;
    }
    public function get_all()
    {
        $id_perusahaan = $this->session->userdata('id_perusahaan');

        $this->db->select('sa.*, b.nama_barang, b.sku, g.nama_gudang, u.nama as created_by');
        $this->db->from('stok_awal sa');
        $this->db->join('barang b', 'sa.id_barang = b.id_barang');
        $this->db->join('gudang g', 'sa.id_gudang = g.id_gudang');
        $this->db->join('user u', 'sa.created_by = u.id_user');
        $this->db->where('sa.id_perusahaan', $id_perusahaan);
        $this->db->order_by('sa.created_at', 'DESC');
        return $this->db->get()->result();
    }

    public function get($id_stok_awal)
    {
        $this->db->select('sa.*, b.nama_barang, b.sku, g.nama_gudang');
        $this->db->from('stok_awal sa');
        $this->db->join('barang b', 'sa.id_barang = b.id_barang');
        $this->db->join('gudang g', 'sa.id_gudang = g.id_gudang');
        $this->db->where('sa.id_stok_awal', $id_stok_awal);
        return $this->db->get()->row();
    }

    public function get_by_barang_gudang($id_barang, $id_gudang)
    {
        $this->db->where('id_barang', $id_barang);
        $this->db->where('id_gudang', $id_gudang);
        $this->db->where('id_perusahaan', $this->session->userdata('id_perusahaan'));
        return $this->db->get('stok_awal')->row();
    }

    public function insert($data)
    {
        $this->db->insert('stok_awal', $data);
        return $this->db->insert_id();
    }

    public function update($id_stok_awal, $data)
    {
        $this->db->where('id_stok_awal', $id_stok_awal);
        return $this->db->update('stok_awal', $data);
    }

    public function delete($id_stok_awal)
    {
        $this->db->where('id_stok_awal', $id_stok_awal);
        return $this->db->delete('stok_awal');
    }
    public function get_stok_awal_by_barang_gudang($id_barang, $id_gudang, $id_perusahaan = null)
    {
        if ($id_perusahaan === null) {
            $id_perusahaan = $this->session->userdata('id_perusahaan');
        }

        $this->db->where('id_barang', $id_barang);
        $this->db->where('id_gudang', $id_gudang);
        $this->db->where('id_perusahaan', $id_perusahaan);
        return $this->db->get('stok_awal')->row();
    }
    public function get_stok_gudang($id_barang, $id_gudang, $id_perusahaan = null)
    {
        if ($id_perusahaan === null) {
            $id_perusahaan = $this->session->userdata('id_perusahaan');
        }

        $this->db->where('id_barang', $id_barang);
        $this->db->where('id_gudang', $id_gudang);
        $this->db->where('id_perusahaan', $id_perusahaan);
        return $this->db->get('stok_gudang')->row();
    }
    private function update_stok_gudang($id_barang, $id_gudang, $jumlah, $id_perusahaan = null)
    {
        if ($id_perusahaan === null) {
            $id_perusahaan = $this->session->userdata('id_perusahaan');
        }

        $stok = $this->stok_awal->get_stok_gudang($id_barang, $id_gudang, $id_perusahaan);

        if ($stok) {
            // Update stok yang ada
            $data = [
                'jumlah' => $jumlah,
                'updated_at' => date('Y-m-d H:i:s')
            ];
            $this->stok_awal->update_stok_gudang($id_barang, $id_gudang, $data);
        } else {
            // Insert stok baru
            $data = [
                'id_perusahaan' => $id_perusahaan,
                'id_barang' => $id_barang,
                'id_gudang' => $id_gudang,
                'jumlah' => $jumlah,
                'reserved' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            $this->stok_awal->insert_stok_gudang($data);
        }

        // Ambil stok terbaru untuk log
        $stok_terbaru = $this->stok_awal->get_stok_gudang($id_barang, $id_gudang, $id_perusahaan);

        $log_data = [
            'id_barang' => $id_barang,
            'id_user' => $this->session->userdata('id_user'),
            'id_perusahaan' => $id_perusahaan,
            'id_gudang' => $id_gudang,
            'jenis' => 'penyesuaian',
            'jumlah' => $jumlah,
            'sisa_stok' => $stok_terbaru ? $stok_terbaru->jumlah : 0,
            'keterangan' => 'Penyesuaian stok awal',
            'tanggal' => date('Y-m-d H:i:s'),
            'id_referensi' => null,
            'tipe_referensi' => 'penyesuaian'
        ];
        $this->stok_awal->insert_log_stok($log_data);
    }
    public function insert_stok_gudang($data)
    {
        $this->db->insert('stok_gudang', $data);
        return $this->db->insert_id();
    }

    public function insert_log_stok($data)
    {
        $this->db->insert('log_stok', $data);
        return $this->db->insert_id();
    }

    // Metode baru untuk mendapatkan barang berdasarkan perusahaan
    public function get_barang_by_perusahaan($id_perusahaan)
    {
        $this->db->select('id_barang, nama_barang, sku');
        $this->db->where('id_perusahaan', $id_perusahaan);
        $this->db->where('status_aktif', 1);
        return $this->db->get('barang')->result();
    }

    // Metode baru untuk mendapatkan gudang berdasarkan perusahaan
    public function get_gudang_by_perusahaan($id_perusahaan)
    {
        $this->db->select('id_gudang, nama_gudang');
        $this->db->where('id_perusahaan', $id_perusahaan);
        $this->db->where('status_aktif', 1);
        return $this->db->get('gudang')->result();
    }

    // Metode baru untuk mendapatkan stok awal berdasarkan perusahaan
    public function get_stok_awal_by_perusahaan($id_perusahaan)
    {
        $this->db->select('sa.*, b.nama_barang, b.sku, g.nama_gudang');
        $this->db->from('stok_awal sa');
        $this->db->join('barang b', 'sa.id_barang = b.id_barang');
        $this->db->join('gudang g', 'sa.id_gudang = g.id_gudang');
        $this->db->where('sa.id_perusahaan', $id_perusahaan);
        $this->db->order_by('sa.created_at', 'DESC');
        return $this->db->get()->result();
    }
}