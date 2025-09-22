<?php
defined('BASEPATH') or exit('No direct script access allowed');

if (!function_exists('generate_data_master_js')) {
    function generate_data_master_js($data)
    {
        $output = "<script>\n";
        $output .= "var dataMaster = {\n";

        // Generate perusahaan data
        $output .= "  perusahaan: " . json_encode($data['perusahaan']) . ",\n";

        // Generate gudang data
        $gudang_by_perusahaan = [];
        foreach ($data['gudang'] as $gudang) {
            if (!isset($gudang_by_perusahaan[$gudang->id_perusahaan])) {
                $gudang_by_perusahaan[$gudang->id_perusahaan] = [];
            }
            $gudang_by_perusahaan[$gudang->id_perusahaan][] = $gudang;
        }
        $output .= "  gudang: " . json_encode($gudang_by_perusahaan) . ",\n";

        // Generate barang data
        $barang_by_perusahaan = [];
        foreach ($data['barang'] as $barang) {
            if (!isset($barang_by_perusahaan[$barang->id_perusahaan])) {
                $barang_by_perusahaan[$barang->id_perusahaan] = [];
            }
            $barang_by_perusahaan[$barang->id_perusahaan][] = $barang;
        }
        $output .= "  barang: " . json_encode($barang_by_perusahaan) . ",\n";

        // Generate pelanggan data
        $pelanggan_by_perusahaan = [];
        foreach ($data['pelanggan'] as $pelanggan) {
            if (!isset($pelanggan_by_perusahaan[$pelanggan->id_perusahaan])) {
                $pelanggan_by_perusahaan[$pelanggan->id_perusahaan] = [];
            }
            $pelanggan_by_perusahaan[$pelanggan->id_perusahaan][] = $pelanggan;
        }
        $output .= "  pelanggan: " . json_encode($pelanggan_by_perusahaan) . ",\n";

        // Generate toko konsumen data
        $output .= "  toko_konsumen: " . json_encode($data['toko_konsumen']) . ",\n";

        // Generate stok data
        $stok_by_gudang = [];
        foreach ($data['stok'] as $stok) {
            if (!isset($stok_by_gudang[$stok->id_gudang])) {
                $stok_by_gudang[$stok->id_gudang] = [];
            }
            $stok_by_gudang[$stok->id_gudang][$stok->id_barang] = $stok;
        }
        $output .= "  stok: " . json_encode($stok_by_gudang) . "\n";

        $output .= "};\n";
        $output .= "</script>\n";

        return $output;
    }
}