<?php
$route['default_controller'] = 'auth';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

// Custom routes
$route['login'] = 'auth/login';
$route['logout'] = 'auth/logout';
$route['dashboard'] = 'dashboard';

// Routes untuk setup
$route['setup/kategori'] = 'setup/kategori';
$route['setup/barang'] = 'setup/barang';
$route['setup/gudang'] = 'setup/gudang';
$route['setup/pelanggan'] = 'setup/pelanggan';
$route['setup/supplier'] = 'setup/supplier';
$route['setup/user'] = 'setup/user';

// Routes untuk aktifitas
$route['aktifitas/pemindahan'] = 'aktifitas/pemindahan';
$route['aktifitas/penerimaan'] = 'aktifitas/penerimaan';
$route['aktifitas/retur_penjualan'] = 'aktifitas/retur_penjualan';
$route['aktifitas/retur_pembelian'] = 'aktifitas/retur_pembelian';

// Routes untuk daftar
$route['daftar/pemindahan'] = 'daftar/pemindahan';
$route['daftar/penerimaan'] = 'daftar/penerimaan';
$route['daftar/retur_penjualan'] = 'daftar/retur_penjualan';
$route['daftar/retur_pembelian'] = 'daftar/retur_pembelian';

// Routes untuk laporan
$route['laporan/sales'] = 'laporan/sales';
$route['laporan/packing'] = 'laporan/packing';
$route['laporan/mutasi'] = 'laporan/mutasi';
$route['laporan/summary'] = 'laporan/summary';

// Routes untuk pengaturan
$route['pengaturan/stok_awal'] = 'pengaturan/stok_awal';
$route['pengaturan/hak_akses'] = 'pengaturan/hak_akses';
$route['pengaturan/approval'] = 'pengaturan/approval';
$route['pengaturan/sistem'] = 'pengaturan/sistem';