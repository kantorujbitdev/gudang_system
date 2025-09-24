<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pemindahan_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }
    public function get_data_by_perusahaan($id_perusahaan)
    {
        $data = [];

        // Get gudang aktif saja
        $this->db->where('id_perusahaan', $id_perusahaan);
        $this->db->where('status_aktif', 1);
        $this->db->where('deleted_at IS NULL');
        $gudang = $this->db->get('gudang')->result();
        $data['gudang'] = $gudang;

        // Get pelanggan aktif saja
        $this->db->where('id_perusahaan', $id_perusahaan);
        $this->db->where('status_aktif', 1);
        $this->db->where('deleted_at IS NULL');
        $pelanggan = $this->db->get('pelanggan')->result();
        $data['pelanggan'] = $pelanggan;

        // Get barang yang ada stoknya di gudang perusahaan tersebut
        $this->db->select('b.*, sg.id_gudang, sg.jumlah, sg.reserved');
        $this->db->from('barang b');
        $this->db->join('stok_gudang sg', 'b.id_barang = sg.id_barang AND sg.id_gudang IN (SELECT id_gudang FROM gudang WHERE id_perusahaan = ' . $id_perusahaan . ')', 'inner');
        $this->db->where('b.id_perusahaan', $id_perusahaan);
        $this->db->where('b.status_aktif', 1);
        $this->db->where('b.deleted_at IS NULL');
        $this->db->where('sg.jumlah >', 0);
        $this->db->group_by('b.id_barang');
        $this->db->order_by('b.nama_barang', 'ASC');
        $barang = $this->db->get()->result();
        $data['barang'] = $barang;

        return $data;
    }
    public function get_all_stok()
    {
        $this->db->select('sg.*');
        $this->db->from('stok_gudang sg');
        $this->db->join('barang b', 'sg.id_barang = b.id_barang');
        $this->db->join('gudang g', 'sg.id_gudang = g.id_gudang');
        $this->db->where('b.status_aktif', 1);
        $this->db->where('b.deleted_at', NULL);
        $this->db->where('g.status_aktif', 1);
        $this->db->where('g.deleted_at', NULL);
        return $this->db->get()->result();
    }

    public function get_stok_by_perusahaan($id_perusahaan)
    {
        $this->db->select('sg.*');
        $this->db->from('stok_gudang sg');
        $this->db->join('barang b', 'sg.id_barang = b.id_barang');
        $this->db->join('gudang g', 'sg.id_gudang = g.id_gudang');
        $this->db->where('b.id_perusahaan', $id_perusahaan);
        $this->db->where('b.status_aktif', 1);
        $this->db->where('b.deleted_at', NULL);
        $this->db->where('g.status_aktif', 1);
        $this->db->where('g.deleted_at', NULL);
        return $this->db->get()->result();
    }
    public function get_barang_by_gudang_with_stock($id_gudang)
    {
        $this->db->select('b.*, sg.jumlah, sg.reserved');
        $this->db->from('barang b');
        $this->db->join('stok_gudang sg', 'b.id_barang = sg.id_barang AND sg.id_gudang = ' . $id_gudang, 'inner');
        $this->db->where('b.status_aktif', 1);
        $this->db->where('b.deleted_at', NULL);
        $this->db->where('sg.jumlah >', 0);
        $this->db->order_by('b.nama_barang', 'ASC');
        return $this->db->get()->result();
    }


    public function get_barang_by_perusahaan_with_stock($id_perusahaan)
    {
        $this->db->select('b.*, sg.jumlah, sg.reserved');
        $this->db->from('barang b');
        $this->db->join('stok_gudang sg', 'b.id_barang = sg.id_barang', 'inner');
        $this->db->where('b.id_perusahaan', $id_perusahaan);
        $this->db->where('b.status_aktif', 1);
        $this->db->where('b.deleted_at', NULL);
        $this->db->where('sg.jumlah >', 0);
        $this->db->order_by('b.nama_barang', 'ASC');
        return $this->db->get()->result();
    }
    public function get_all()
    {
        $user_role = $this->session->userdata('id_role');
        $id_perusahaan = $this->session->userdata('id_perusahaan');
        $id_user = $this->session->userdata('id_user');

        $this->db->select('pb.*, u.nama as user_nama, ga.nama_gudang as gudang_asal, gt.nama_gudang as gudang_tujuan, p.nama_pelanggan, k.nama_konsumen, tk.nama_toko_konsumen');
        $this->db->from('pemindahan_barang pb');
        $this->db->join('user u', 'pb.id_user = u.id_user');
        $this->db->join('gudang ga', 'pb.id_gudang_asal = ga.id_gudang');
        $this->db->join('gudang gt', 'pb.id_gudang_tujuan = gt.id_gudang', 'left');
        $this->db->join('pelanggan p', 'pb.id_pelanggan = p.id_pelanggan', 'left');
        $this->db->join('konsumen k', 'pb.id_konsumen = k.id_konsumen', 'left');
        $this->db->join('toko_konsumen tk', 'k.id_toko_konsumen = tk.id_toko_konsumen', 'left');

        // Filter berdasarkan role user
        if ($user_role != 1) { // Bukan Super Admin
            $this->db->where('pb.id_perusahaan', $id_perusahaan);

            // Jika role Sales, hanya bisa lihat pemindahan ke pelanggan miliknya
            if ($user_role == 3) {
                $this->db->where('pb.tipe_tujuan', 'pelanggan');
                $this->db->where('p.id_sales', $id_user);
            }
        }

        $this->db->where('pb.deleted_at', NULL);
        $this->db->order_by('pb.created_at', 'DESC');

        return $this->db->get()->result();
    }

    public function get($id_pemindahan)
    {
        $this->db->select('pb.*, u.nama as user_nama, ga.nama_gudang as gudang_asal, gt.nama_gudang as gudang_tujuan, p.nama_pelanggan, k.nama_konsumen, k.alamat_konsumen, tk.nama_toko_konsumen');
        $this->db->from('pemindahan_barang pb');
        $this->db->join('user u', 'pb.id_user = u.id_user');
        $this->db->join('gudang ga', 'pb.id_gudang_asal = ga.id_gudang');
        $this->db->join('gudang gt', 'pb.id_gudang_tujuan = gt.id_gudang', 'left');
        $this->db->join('pelanggan p', 'pb.id_pelanggan = p.id_pelanggan', 'left');
        $this->db->join('konsumen k', 'pb.id_konsumen = k.id_konsumen', 'left');
        $this->db->join('toko_konsumen tk', 'k.id_toko_konsumen = tk.id_toko_konsumen', 'left');
        $this->db->where('pb.id_pemindahan', $id_pemindahan);
        $this->db->where('pb.deleted_at', NULL);

        return $this->db->get()->row();
    }

    public function get_detail($id_pemindahan)
    {
        $this->db->select('dpb.*, b.nama_barang, b.satuan, b.sku');
        $this->db->from('detail_pemindahan_barang dpb');
        $this->db->join('barang b', 'dpb.id_barang = b.id_barang');
        $this->db->where('dpb.id_pemindahan', $id_pemindahan);

        return $this->db->get()->result();
    }

    public function insert($data)
    {
        $this->db->insert('pemindahan_barang', $data);
        return $this->db->insert_id();
    }

    public function insert_detail($data)
    {
        $this->db->insert('detail_pemindahan_barang', $data);
        return $this->db->insert_id();
    }

    public function update($id_pemindahan, $data)
    {
        $this->db->where('id_pemindahan', $id_pemindahan);
        return $this->db->update('pemindahan_barang', $data);
    }

    public function update_status($id_pemindahan, $status)
    {
        $this->db->where('id_pemindahan', $id_pemindahan);
        $data = [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        return $this->db->update('pemindahan_barang', $data);
    }

    public function delete_detail($id_pemindahan)
    {
        $this->db->where('id_pemindahan', $id_pemindahan);
        return $this->db->delete('detail_pemindahan_barang');
    }

    public function get_stok_barang($id_gudang, $id_barang)
    {
        $this->db->where('id_gudang', $id_gudang);
        $this->db->where('id_barang', $id_barang);
        return $this->db->get('stok_gudang')->row();
    }

    public function update_stok($id_gudang, $id_barang, $jumlah, $reserved = 0)
    {
        $this->db->where('id_gudang', $id_gudang);
        $this->db->where('id_barang', $id_barang);
        $data = [
            'jumlah' => $jumlah,
            'reserved' => $reserved,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $result = $this->db->update('stok_gudang', $data);

        // Debug log
        log_message('debug', 'Update stok: ' . $this->db->last_query());
        log_message('debug', 'Update result: ' . ($result ? 'Success' : 'Failed'));

        return $result;
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

    public function get_pelanggan_by_sales($id_sales)
    {
        $this->db->where('id_sales', $id_sales);
        $this->db->where('status_aktif', 1);
        $this->db->where('deleted_at', NULL);
        return $this->db->get('pelanggan')->result();
    }
    public function get_alamat_pelanggan($id_pelanggan)
    {
        $this->db->select('p.alamat, p.telepon, p.email');
        $this->db->from('pelanggan p');
        $this->db->where('p.id_pelanggan', $id_pelanggan);
        $this->db->where('p.status_aktif', 1);
        $this->db->where('p.deleted_at', NULL);
        return $this->db->get()->row();
    }
}