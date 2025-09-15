<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Debug extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function index()
    {
        echo "<h1>Debug Database Tables</h1>";

        // Cek tabel penjualan
        echo "<h2>penjualan</h2>";
        $query = $this->db->get('penjualan');
        echo "<p>Total records: " . $query->num_rows() . "</p>";
        if ($query->num_rows() > 0) {
            echo "<table border='1'>";
            echo "<tr>";
            foreach ($query->list_fields() as $field) {
                echo "<th>$field</th>";
            }
            echo "</tr>";
            foreach ($query->result() as $row) {
                echo "<tr>";
                foreach ($query->list_fields() as $field) {
                    echo "<td>" . $row->$field . "</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
        }

        // Cek tabel retur_penjualan
        echo "<h2>retur_penjualan</h2>";
        $query = $this->db->get('retur_penjualan');
        echo "<p>Total records: " . $query->num_rows() . "</p>";
        if ($query->num_rows() > 0) {
            echo "<table border='1'>";
            echo "<tr>";
            foreach ($query->list_fields() as $field) {
                echo "<th>$field</th>";
            }
            echo "</tr>";
            foreach ($query->result() as $row) {
                echo "<tr>";
                foreach ($query->list_fields() as $field) {
                    echo "<td>" . $row->$field . "</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
        }
    }

    public function test_retur_query()
    {
        // Test query retur dengan filter
        $id_perusahaan = $this->input->get('id_perusahaan');

        $this->db->select('retur_penjualan.*, penjualan.no_invoice, penjualan.id_perusahaan, pelanggan.nama_pelanggan, user.nama as created_by');
        $this->db->from('retur_penjualan');
        $this->db->join('penjualan', 'penjualan.id_penjualan = retur_penjualan.id_penjualan', 'left');
        $this->db->join('pelanggan', 'pelanggan.id_pelanggan = penjualan.id_pelanggan', 'left');
        $this->db->join('user', 'user.id_user = retur_penjualan.id_user', 'left');

        if ($id_perusahaan) {
            $this->db->where('penjualan.id_perusahaan', $id_perusahaan);
        }

        $this->db->order_by('retur_penjualan.tanggal_retur', 'DESC');

        // Tampilkan query
        echo "<h2>Query yang dihasilkan:</h2>";
        echo "<pre>" . $this->db->get_compiled_select() . "</pre>";

        // Eksekusi query
        $query = $this->db->get();

        if ($query === FALSE) {
            echo "<h2>Error:</h2>";
            $error = $this->db->error();
            echo "<pre>" . print_r($error, true) . "</pre>";
        } else {
            echo "<h2>Hasil Query:</h2>";
            echo "<p>Total records: " . $query->num_rows() . "</p>";
            if ($query->num_rows() > 0) {
                echo "<table border='1'>";
                echo "<tr>";
                foreach ($query->list_fields() as $field) {
                    echo "<th>$field</th>";
                }
                echo "</tr>";
                foreach ($query->result() as $row) {
                    echo "<tr>";
                    foreach ($query->list_fields() as $field) {
                        echo "<td>" . $row->$field . "</td>";
                    }
                    echo "</tr>";
                }
                echo "</table>";
            }
        }
    }

    public function test_penjualan_query()
    {
        // Test query penjualan dengan filter
        $id_perusahaan = $this->input->get('id_perusahaan');

        $this->db->select('penjualan.*, pelanggan.nama_pelanggan, user.nama as created_by');
        $this->db->from('penjualan');
        $this->db->join('pelanggan', 'pelanggan.id_pelanggan = penjualan.id_pelanggan', 'left');
        $this->db->join('user', 'user.id_user = penjualan.id_user', 'left');

        if ($id_perusahaan) {
            $this->db->where('penjualan.id_perusahaan', $id_perusahaan);
        }

        $this->db->order_by('penjualan.tanggal_penjualan', 'DESC');

        // Tampilkan query
        echo "<h2>Query yang dihasilkan:</h2>";
        echo "<pre>" . $this->db->get_compiled_select() . "</pre>";

        // Eksekusi query
        $query = $this->db->get();

        if ($query === FALSE) {
            echo "<h2>Error:</h2>";
            $error = $this->db->error();
            echo "<pre>" . print_r($error, true) . "</pre>";
        } else {
            echo "<h2>Hasil Query:</h2>";
            echo "<p>Total records: " . $query->num_rows() . "</p>";
            if ($query->num_rows() > 0) {
                echo "<table border='1'>";
                echo "<tr>";
                foreach ($query->list_fields() as $field) {
                    echo "<th>$field</th>";
                }
                echo "</tr>";
                foreach ($query->result() as $row) {
                    echo "<tr>";
                    foreach ($query->list_fields() as $field) {
                        echo "<td>" . $row->$field . "</td>";
                    }
                    echo "</tr>";
                }
                echo "</table>";
            }
        }
    }
}