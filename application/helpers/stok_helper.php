<?php
defined('BASEPATH') or exit('No direct script access allowed');

if (!function_exists('calculate_available_stock')) {
    function calculate_available_stock($jumlah, $reserved)
    {
        return $jumlah - $reserved;
    }
}

if (!function_exists('format_stok_status')) {
    function format_stok_status($stok, $min_stok = 10)
    {
        if ($stok <= 0) {
            return '<span class="badge badge-danger">Habis</span>';
        } elseif ($stok <= $min_stok) {
            return '<span class="badge badge-warning">Menipis</span>';
        } else {
            return '<span class="badge badge-success">Tersedia</span>';
        }
    }
}