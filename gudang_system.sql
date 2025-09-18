-- Tabel Perusahaan (tabel utama)
CREATE TABLE `perusahaan` (
  `id_perusahaan` int(11) NOT NULL AUTO_INCREMENT,
  `nama_perusahaan` varchar(255) NOT NULL,
  `alamat` varchar(255) DEFAULT NULL,
  `telepon` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `status_aktif` tinyint(1) DEFAULT 1,
  `deleted_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_perusahaan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel Role User
CREATE TABLE `role_user` (
  `id_role` int(11) NOT NULL AUTO_INCREMENT,
  `nama_role` varchar(50) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  PRIMARY KEY (`id_role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel Menu
CREATE TABLE `menu` (
  `id_menu` int(11) NOT NULL AUTO_INCREMENT,
  `nama_menu` varchar(50) NOT NULL,
  `url` varchar(100) DEFAULT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `urutan` int(11) DEFAULT 0,
  `id_parent` int(11) DEFAULT NULL,
  `status_aktif` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id_menu`),
  KEY `id_parent` (`id_parent`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel User
CREATE TABLE `user` (
  `id_user` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `id_role` int(11) NOT NULL,
  `id_perusahaan` int(11) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `telepon` varchar(20) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `aktif` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `foto_profil` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `username` (`username`),
  KEY `id_role` (`id_role`),
  KEY `id_perusahaan` (`id_perusahaan`),
  KEY `user_ibfk_3` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel Hak Akses Menu
CREATE TABLE `hak_akses_menu` (
  `id_hak_akses` int(11) NOT NULL AUTO_INCREMENT,
  `id_role` int(11) NOT NULL,
  `id_menu` int(11) NOT NULL,
  `akses` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id_hak_akses`),
  UNIQUE KEY `uniq_role_menu` (`id_role`,`id_menu`),
  KEY `hak_akses_menu_ibfk_2` (`id_menu`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel Kategori
CREATE TABLE `kategori` (
  `id_kategori` int(11) NOT NULL AUTO_INCREMENT,
  `id_perusahaan` int(11) NOT NULL,
  `nama_kategori` varchar(50) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `status_aktif` tinyint(1) DEFAULT 1,
  `deleted_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id_kategori`),
  UNIQUE KEY `uniq_kategori_perusahaan` (`id_perusahaan`,`nama_kategori`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel Gudang
CREATE TABLE `gudang` (
  `id_gudang` int(11) NOT NULL AUTO_INCREMENT,
  `id_perusahaan` int(11) NOT NULL,
  `nama_gudang` varchar(100) NOT NULL,
  `alamat` text DEFAULT NULL,
  `telepon` varchar(20) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `status_aktif` tinyint(1) DEFAULT 1,
  `deleted_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_gudang`),
  KEY `id_perusahaan` (`id_perusahaan`),
  KEY `gudang_ibfk_2` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel Pelanggan
CREATE TABLE `pelanggan` (
  `id_pelanggan` int(11) NOT NULL AUTO_INCREMENT,
  `id_perusahaan` int(11) NOT NULL,
  `nama_pelanggan` varchar(100) NOT NULL,
  `alamat` text DEFAULT NULL,
  `telepon` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `tipe_pelanggan` enum('distributor','konsumen') DEFAULT 'konsumen',
  `status_aktif` tinyint(1) DEFAULT 1,
  `deleted_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id_pelanggan`),
  KEY `pelanggan_ibfk_1` (`id_perusahaan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel Supplier
CREATE TABLE `supplier` (
  `id_supplier` int(11) NOT NULL AUTO_INCREMENT,
  `id_perusahaan` int(11) NOT NULL,
  `nama_supplier` varchar(100) NOT NULL,
  `alamat` text DEFAULT NULL,
  `telepon` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `kontak_person` varchar(100) DEFAULT NULL,
  `status_aktif` tinyint(1) DEFAULT 1,
  `deleted_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id_supplier`),
  KEY `supplier_ibfk_1` (`id_perusahaan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel Barang
CREATE TABLE `barang` (
  `id_barang` int(11) NOT NULL AUTO_INCREMENT,
  `id_perusahaan` int(11) NOT NULL,
  `id_kategori` int(11) DEFAULT NULL,
  `nama_barang` varchar(100) NOT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `sku` varchar(50) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `satuan` varchar(20) DEFAULT NULL,
  `harga_jual` decimal(10,2) DEFAULT NULL,
  `harga_beli_terakhir` decimal(10,2) DEFAULT NULL,
  `status_aktif` tinyint(1) DEFAULT 1,
  `deleted_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_barang`),
  UNIQUE KEY `uniq_sku_perusahaan` (`id_perusahaan`,`sku`),
  KEY `id_kategori` (`id_kategori`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel Approval Flow
CREATE TABLE `approval_flow` (
  `id_approval` int(11) NOT NULL AUTO_INCREMENT,
  `tipe_transaksi` enum('penjualan','pembelian','penerimaan','retur_penjualan','retur_pembelian','transfer_stok') NOT NULL,
  `status_dari` varchar(20) NOT NULL,
  `status_ke` varchar(20) NOT NULL,
  `id_role` int(11) NOT NULL,
  `urutan` int(11) NOT NULL,
  `status_aktif` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id_approval`),
  KEY `tipe_transaksi` (`tipe_transaksi`),
  KEY `id_role` (`id_role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel Stok Gudang
CREATE TABLE `stok_gudang` (
  `id_stok` int(11) NOT NULL AUTO_INCREMENT,
  `id_perusahaan` int(11) NOT NULL,
  `id_gudang` int(11) NOT NULL,
  `id_barang` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL DEFAULT 0,
  `reserved` int(11) NOT NULL DEFAULT 0 COMMENT 'Jumlah stok yang dipesan tapi belum dikirim',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_stok`),
  UNIQUE KEY `uniq_barang_gudang` (`id_barang`,`id_gudang`),
  KEY `stok_gudang_ibfk_1` (`id_perusahaan`),
  KEY `stok_gudang_ibfk_2` (`id_gudang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel Pengaturan Sistem
CREATE TABLE `pengaturan_sistem` (
  `id_pengaturan` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(50) NOT NULL,
  `value` text DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  PRIMARY KEY (`id_pengaturan`),
  UNIQUE KEY `uniq_key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel Transaksi Utama
CREATE TABLE `penjualan` (
  `id_penjualan` int(11) NOT NULL AUTO_INCREMENT,
  `id_perusahaan` int(11) NOT NULL,
  `no_invoice` varchar(50) DEFAULT NULL,
  `id_user` int(11) NOT NULL,
  `id_pelanggan` int(11) DEFAULT NULL,
  `tanggal_penjualan` datetime NOT NULL,
  `keterangan` text DEFAULT NULL,
  `status` enum('Draft','Packing','Shipping','Delivered','Cancelled') DEFAULT 'Draft',
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id_penjualan`),
  KEY `id_perusahaan` (`id_perusahaan`),
  KEY `penjualan_ibfk_2` (`id_user`),
  KEY `penjualan_ibfk_3` (`id_pelanggan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `pembelian` (
  `id_pembelian` int(11) NOT NULL AUTO_INCREMENT,
  `id_perusahaan` int(11) NOT NULL,
  `no_pembelian` varchar(50) DEFAULT NULL,
  `id_user` int(11) NOT NULL,
  `id_supplier` int(11) DEFAULT NULL,
  `tanggal_pembelian` datetime NOT NULL,
  `tanggal_estimasi` datetime DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `status` enum('Draft','Received','Completed','Cancelled') DEFAULT 'Draft',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_pembelian`),
  KEY `id_perusahaan` (`id_perusahaan`),
  KEY `pembelian_ibfk_2` (`id_user`),
  KEY `pembelian_ibfk_3` (`id_supplier`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `penerimaan_barang` (
  `id_penerimaan` int(11) NOT NULL AUTO_INCREMENT,
  `no_penerimaan` varchar(50) DEFAULT NULL,
  `id_pembelian` int(11) DEFAULT NULL,
  `id_user` int(11) NOT NULL,
  `id_gudang` int(11) NOT NULL,
  `tanggal_penerimaan` datetime NOT NULL,
  `keterangan` text DEFAULT NULL,
  `status` enum('Draft','Received','Completed','Cancelled') DEFAULT 'Draft',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_penerimaan`),
  KEY `id_pembelian` (`id_pembelian`),
  KEY `id_user` (`id_user`),
  KEY `id_gudang` (`id_gudang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `transfer_stok` (
  `id_transfer` int(11) NOT NULL AUTO_INCREMENT,
  `no_transfer` varchar(50) DEFAULT NULL,
  `id_perusahaan` int(11) NOT NULL,
  `id_gudang_asal` int(11) NOT NULL,
  `id_gudang_tujuan` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `tanggal` datetime NOT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  `status` enum('Draft','Packing','Shipping','Delivered','Cancelled') DEFAULT 'Draft',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_transfer`),
  KEY `id_perusahaan` (`id_perusahaan`),
  KEY `id_gudang_asal` (`id_gudang_asal`),
  KEY `id_gudang_tujuan` (`id_gudang_tujuan`),
  KEY `id_user` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `packing` (
  `id_packing` int(11) NOT NULL AUTO_INCREMENT,
  `id_referensi` int(11) NOT NULL COMMENT 'ID dari transaksi terkait (penjualan/transfer)',
  `tipe_referensi` enum('penjualan','transfer') NOT NULL,
  `id_user` int(11) NOT NULL,
  `tanggal_packing` datetime NOT NULL,
  `status` enum('Draft','Packing','Completed','Cancelled') DEFAULT 'Draft',
  `catatan` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_packing`),
  KEY `id_referensi` (`id_referensi`),
  KEY `id_user` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `retur_penjualan` (
  `id_retur` int(11) NOT NULL AUTO_INCREMENT,
  `no_retur` varchar(50) DEFAULT NULL,
  `id_penjualan` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `tanggal_retur` datetime NOT NULL,
  `alasan_retur` text NOT NULL,
  `status` enum('Requested','Verification','Approved','Rejected','Completed') DEFAULT 'Requested',
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id_retur`),
  KEY `id_penjualan` (`id_penjualan`),
  KEY `id_user` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `retur_pembelian` (
  `id_retur_beli` int(11) NOT NULL AUTO_INCREMENT,
  `no_retur_beli` varchar(50) DEFAULT NULL,
  `id_pembelian` int(11) DEFAULT NULL,
  `id_user` int(11) NOT NULL,
  `id_supplier` int(11) NOT NULL,
  `tanggal_retur` datetime NOT NULL,
  `alasan_retur` text NOT NULL,
  `status` enum('Requested','Verification','Approved','Rejected','Completed') DEFAULT 'Requested',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_retur_beli`),
  KEY `id_pembelian` (`id_pembelian`),
  KEY `id_user` (`id_user`),
  KEY `id_supplier` (`id_supplier`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel Detail Transaksi
CREATE TABLE `detail_penjualan` (
  `id_detail` int(11) NOT NULL AUTO_INCREMENT,
  `id_penjualan` int(11) NOT NULL,
  `id_barang` int(11) NOT NULL,
  `id_gudang` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `harga_jual` decimal(10,2) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id_detail`),
  KEY `fk_penjualan_detail` (`id_penjualan`),
  KEY `fk_barang_penjualan` (`id_barang`),
  KEY `fk_gudang_penjualan` (`id_gudang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `detail_pembelian` (
  `id_detail` int(11) NOT NULL AUTO_INCREMENT,
  `id_pembelian` int(11) NOT NULL,
  `id_barang` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `harga_beli` decimal(10,2) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id_detail`),
  KEY `id_pembelian` (`id_pembelian`),
  KEY `id_barang` (`id_barang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `detail_penerimaan` (
  `id_detail` int(11) NOT NULL AUTO_INCREMENT,
  `id_penerimaan` int(11) NOT NULL,
  `id_barang` int(11) NOT NULL,
  `jumlah_diterima` int(11) NOT NULL,
  `jumlah_dipesan` int(11) DEFAULT NULL,
  `id_gudang` int(11) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id_detail`),
  KEY `id_penerimaan` (`id_penerimaan`),
  KEY `id_barang` (`id_barang`),
  KEY `id_gudang` (`id_gudang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `detail_transfer_stok` (
  `id_detail` int(11) NOT NULL AUTO_INCREMENT,
  `id_transfer` int(11) NOT NULL,
  `id_barang` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id_detail`),
  KEY `id_transfer` (`id_transfer`),
  KEY `id_barang` (`id_barang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `detail_packing` (
  `id_detail` int(11) NOT NULL AUTO_INCREMENT,
  `id_packing` int(11) NOT NULL,
  `id_barang` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id_detail`),
  KEY `id_packing` (`id_packing`),
  KEY `id_barang` (`id_barang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `detail_retur_penjualan` (
  `id_detail_retur` int(11) NOT NULL AUTO_INCREMENT,
  `id_retur` int(11) NOT NULL,
  `id_barang` int(11) NOT NULL,
  `id_gudang` int(11) NOT NULL,
  `jumlah_retur` int(11) NOT NULL,
  `jumlah_disetujui` int(11) DEFAULT 0 COMMENT 'Jumlah yang disetujui untuk retur',
  `alasan_barang` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id_detail_retur`),
  KEY `fk_retur` (`id_retur`),
  KEY `fk_barang` (`id_barang`),
  KEY `fk_gudang` (`id_gudang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `detail_retur_pembelian` (
  `id_detail_retur_beli` int(11) NOT NULL AUTO_INCREMENT,
  `id_retur_beli` int(11) NOT NULL,
  `id_barang` int(11) NOT NULL,
  `id_gudang` int(11) NOT NULL,
  `jumlah_retur` int(11) NOT NULL,
  `jumlah_disetujui` int(11) DEFAULT 0 COMMENT 'Jumlah yang disetujui untuk retur',
  `alasan_barang` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id_detail_retur_beli`),
  KEY `id_retur_beli` (`id_retur_beli`),
  KEY `id_barang` (`id_barang`),
  KEY `id_gudang` (`id_gudang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel Log dan Pendukung
CREATE TABLE `log_stok` (
  `id_log` int(11) NOT NULL AUTO_INCREMENT,
  `id_barang` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `id_perusahaan` int(11) NOT NULL,
  `id_gudang` int(11) NOT NULL,
  `jenis` enum('masuk','keluar','retur_penjualan','retur_pembelian','transfer_keluar','transfer_masuk','penyesuaian') NOT NULL,
  `jumlah` int(11) NOT NULL,
  `sisa_stok` int(11) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `tanggal` datetime DEFAULT current_timestamp(),
  `id_referensi` int(11) DEFAULT NULL COMMENT 'ID transaksi terkait (penjualan/penerimaan/retur/transfer)',
  `tipe_referensi` enum('penjualan','penerimaan','retur_penjualan','retur_pembelian','transfer','penyesuaian') DEFAULT NULL,
  PRIMARY KEY (`id_log`),
  KEY `idx_barang` (`id_barang`),
  KEY `idx_gudang` (`id_gudang`),
  KEY `idx_perusahaan` (`id_perusahaan`),
  KEY `idx_referensi` (`id_referensi`),
  KEY `idx_tipe_referensi` (`tipe_referensi`),
  KEY `log_stok_ibfk_2` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `log_status_transaksi` (
  `id_log` int(11) NOT NULL AUTO_INCREMENT,
  `id_transaksi` int(11) NOT NULL,
  `tipe_transaksi` enum('penjualan','pembelian','penerimaan','retur_penjualan','retur_pembelian','transfer_stok','packing') NOT NULL,
  `id_user` int(11) NOT NULL,
  `status` varchar(20) NOT NULL,
  `tanggal` datetime NOT NULL DEFAULT current_timestamp(),
  `keterangan` text DEFAULT NULL,
  PRIMARY KEY (`id_log`),
  KEY `id_transaksi` (`id_transaksi`),
  KEY `id_user` (`id_user`),
  KEY `tipe_transaksi` (`tipe_transaksi`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `stok_awal` (
  `id_stok_awal` int(11) NOT NULL AUTO_INCREMENT,
  `id_barang` int(11) NOT NULL,
  `id_gudang` int(11) NOT NULL,
  `id_perusahaan` int(11) NOT NULL,
  `qty_awal` int(11) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL,
  PRIMARY KEY (`id_stok_awal`),
  KEY `fk_stok_awal_barang` (`id_barang`),
  KEY `fk_stok_awal_gudang` (`id_gudang`),
  KEY `fk_stok_awal_perusahaan` (`id_perusahaan`),
  KEY `stok_awal_ibfk_4` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- View
CREATE VIEW `v_stok_realtime` AS 
SELECT 
  `sg`.`id_barang` AS `id_barang`, 
  `sg`.`id_gudang` AS `id_gudang`, 
  `b`.`nama_barang` AS `nama_barang`, 
  `g`.`nama_gudang` AS `nama_gudang`, 
  `sg`.`jumlah` AS `jumlah`, 
  `sg`.`reserved` AS `reserved`, 
  `sg`.`jumlah` - `sg`.`reserved` AS `stok_tersedia`, 
  `b`.`satuan` AS `satuan`, 
  `p`.`nama_perusahaan` AS `nama_perusahaan` 
FROM 
  (((`stok_gudang` `sg` 
  JOIN `barang` `b` ON(`sg`.`id_barang` = `b`.`id_barang`)) 
  JOIN `gudang` `g` ON(`sg`.`id_gudang` = `g`.`id_gudang`)) 
  JOIN `perusahaan` `p` ON(`sg`.`id_perusahaan` = `p`.`id_perusahaan`)) 
WHERE 
  `b`.`status_aktif` = 1 AND 
  `g`.`status_aktif` = 1 AND 
  `p`.`status_aktif` = 1;

-- Menambahkan Foreign Key Constraints
ALTER TABLE `user`
  ADD CONSTRAINT `user_ibfk_1` FOREIGN KEY (`id_role`) REFERENCES `role_user` (`id_role`),
  ADD CONSTRAINT `user_ibfk_2` FOREIGN KEY (`id_perusahaan`) REFERENCES `perusahaan` (`id_perusahaan`),
  ADD CONSTRAINT `user_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `user` (`id_user`);

ALTER TABLE `hak_akses_menu`
  ADD CONSTRAINT `hak_akses_menu_ibfk_1` FOREIGN KEY (`id_role`) REFERENCES `role_user` (`id_role`),
  ADD CONSTRAINT `hak_akses_menu_ibfk_2` FOREIGN KEY (`id_menu`) REFERENCES `menu` (`id_menu`);

ALTER TABLE `menu`
  ADD CONSTRAINT `menu_ibfk_1` FOREIGN KEY (`id_parent`) REFERENCES `menu` (`id_menu`);

ALTER TABLE `kategori`
  ADD CONSTRAINT `kategori_ibfk_1` FOREIGN KEY (`id_perusahaan`) REFERENCES `perusahaan` (`id_perusahaan`);

ALTER TABLE `gudang`
  ADD CONSTRAINT `gudang_ibfk_1` FOREIGN KEY (`id_perusahaan`) REFERENCES `perusahaan` (`id_perusahaan`),
  ADD CONSTRAINT `gudang_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `user` (`id_user`);

ALTER TABLE `pelanggan`
  ADD CONSTRAINT `pelanggan_ibfk_1` FOREIGN KEY (`id_perusahaan`) REFERENCES `perusahaan` (`id_perusahaan`);

ALTER TABLE `supplier`
  ADD CONSTRAINT `supplier_ibfk_1` FOREIGN KEY (`id_perusahaan`) REFERENCES `perusahaan` (`id_perusahaan`);

ALTER TABLE `barang`
  ADD CONSTRAINT `barang_ibfk_1` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`),
  ADD CONSTRAINT `barang_ibfk_2` FOREIGN KEY (`id_perusahaan`) REFERENCES `perusahaan` (`id_perusahaan`);

ALTER TABLE `approval_flow`
  ADD CONSTRAINT `approval_flow_ibfk_1` FOREIGN KEY (`id_role`) REFERENCES `role_user` (`id_role`);

ALTER TABLE `stok_gudang`
  ADD CONSTRAINT `stok_gudang_ibfk_1` FOREIGN KEY (`id_perusahaan`) REFERENCES `perusahaan` (`id_perusahaan`),
  ADD CONSTRAINT `stok_gudang_ibfk_2` FOREIGN KEY (`id_gudang`) REFERENCES `gudang` (`id_gudang`),
  ADD CONSTRAINT `stok_gudang_ibfk_3` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id_barang`);

ALTER TABLE `penjualan`
  ADD CONSTRAINT `penjualan_ibfk_1` FOREIGN KEY (`id_perusahaan`) REFERENCES `perusahaan` (`id_perusahaan`),
  ADD CONSTRAINT `penjualan_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`),
  ADD CONSTRAINT `penjualan_ibfk_3` FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan` (`id_pelanggan`);

ALTER TABLE `pembelian`
  ADD CONSTRAINT `pembelian_ibfk_1` FOREIGN KEY (`id_perusahaan`) REFERENCES `perusahaan` (`id_perusahaan`),
  ADD CONSTRAINT `pembelian_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`),
  ADD CONSTRAINT `pembelian_ibfk_3` FOREIGN KEY (`id_supplier`) REFERENCES `supplier` (`id_supplier`);

ALTER TABLE `penerimaan_barang`
  ADD CONSTRAINT `penerimaan_barang_ibfk_1` FOREIGN KEY (`id_pembelian`) REFERENCES `pembelian` (`id_pembelian`),
  ADD CONSTRAINT `penerimaan_barang_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`),
  ADD CONSTRAINT `penerimaan_barang_ibfk_3` FOREIGN KEY (`id_gudang`) REFERENCES `gudang` (`id_gudang`);

ALTER TABLE `transfer_stok`
  ADD CONSTRAINT `transfer_stok_ibfk_1` FOREIGN KEY (`id_perusahaan`) REFERENCES `perusahaan` (`id_perusahaan`),
  ADD CONSTRAINT `transfer_stok_ibfk_2` FOREIGN KEY (`id_gudang_asal`) REFERENCES `gudang` (`id_gudang`),
  ADD CONSTRAINT `transfer_stok_ibfk_3` FOREIGN KEY (`id_gudang_tujuan`) REFERENCES `gudang` (`id_gudang`),
  ADD CONSTRAINT `transfer_stok_ibfk_4` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`);

ALTER TABLE `packing`
  ADD CONSTRAINT `packing_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`);

ALTER TABLE `retur_penjualan`
  ADD CONSTRAINT `retur_penjualan_ibfk_1` FOREIGN KEY (`id_penjualan`) REFERENCES `penjualan` (`id_penjualan`),
  ADD CONSTRAINT `retur_penjualan_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`);

ALTER TABLE `retur_pembelian`
  ADD CONSTRAINT `retur_pembelian_ibfk_1` FOREIGN KEY (`id_pembelian`) REFERENCES `pembelian` (`id_pembelian`),
  ADD CONSTRAINT `retur_pembelian_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`),
  ADD CONSTRAINT `retur_pembelian_ibfk_3` FOREIGN KEY (`id_supplier`) REFERENCES `supplier` (`id_supplier`);

ALTER TABLE `detail_penjualan`
  ADD CONSTRAINT `detail_penjualan_ibfk_1` FOREIGN KEY (`id_penjualan`) REFERENCES `penjualan` (`id_penjualan`),
  ADD CONSTRAINT `detail_penjualan_ibfk_2` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id_barang`),
  ADD CONSTRAINT `detail_penjualan_ibfk_3` FOREIGN KEY (`id_gudang`) REFERENCES `gudang` (`id_gudang`);

ALTER TABLE `detail_pembelian`
  ADD CONSTRAINT `detail_pembelian_ibfk_1` FOREIGN KEY (`id_pembelian`) REFERENCES `pembelian` (`id_pembelian`),
  ADD CONSTRAINT `detail_pembelian_ibfk_2` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id_barang`);

ALTER TABLE `detail_penerimaan`
  ADD CONSTRAINT `detail_penerimaan_ibfk_1` FOREIGN KEY (`id_penerimaan`) REFERENCES `penerimaan_barang` (`id_penerimaan`),
  ADD CONSTRAINT `detail_penerimaan_ibfk_2` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id_barang`),
  ADD CONSTRAINT `detail_penerimaan_ibfk_3` FOREIGN KEY (`id_gudang`) REFERENCES `gudang` (`id_gudang`);

ALTER TABLE `detail_transfer_stok`
  ADD CONSTRAINT `detail_transfer_stok_ibfk_1` FOREIGN KEY (`id_transfer`) REFERENCES `transfer_stok` (`id_transfer`),
  ADD CONSTRAINT `detail_transfer_stok_ibfk_2` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id_barang`);

ALTER TABLE `detail_packing`
  ADD CONSTRAINT `detail_packing_ibfk_1` FOREIGN KEY (`id_packing`) REFERENCES `packing` (`id_packing`),
  ADD CONSTRAINT `detail_packing_ibfk_2` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id_barang`);

ALTER TABLE `detail_retur_penjualan`
  ADD CONSTRAINT `detail_retur_penjualan_ibfk_1` FOREIGN KEY (`id_retur`) REFERENCES `retur_penjualan` (`id_retur`),
  ADD CONSTRAINT `detail_retur_penjualan_ibfk_2` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id_barang`),
  ADD CONSTRAINT `detail_retur_penjualan_ibfk_3` FOREIGN KEY (`id_gudang`) REFERENCES `gudang` (`id_gudang`);

ALTER TABLE `detail_retur_pembelian`
  ADD CONSTRAINT `detail_retur_pembelian_ibfk_1` FOREIGN KEY (`id_retur_beli`) REFERENCES `retur_pembelian` (`id_retur_beli`),
  ADD CONSTRAINT `detail_retur_pembelian_ibfk_2` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id_barang`),
  ADD CONSTRAINT `detail_retur_pembelian_ibfk_3` FOREIGN KEY (`id_gudang`) REFERENCES `gudang` (`id_gudang`);

ALTER TABLE `log_stok`
  ADD CONSTRAINT `log_stok_ibfk_1` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id_barang`),
  ADD CONSTRAINT `log_stok_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`),
  ADD CONSTRAINT `log_stok_ibfk_3` FOREIGN KEY (`id_perusahaan`) REFERENCES `perusahaan` (`id_perusahaan`),
  ADD CONSTRAINT `log_stok_ibfk_4` FOREIGN KEY (`id_gudang`) REFERENCES `gudang` (`id_gudang`);

ALTER TABLE `log_status_transaksi`
  ADD CONSTRAINT `log_status_transaksi_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`);

ALTER TABLE `stok_awal`
  ADD CONSTRAINT `stok_awal_ibfk_1` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id_barang`),
  ADD CONSTRAINT `stok_awal_ibfk_2` FOREIGN KEY (`id_gudang`) REFERENCES `gudang` (`id_gudang`),
  ADD CONSTRAINT `stok_awal_ibfk_3` FOREIGN KEY (`id_perusahaan`) REFERENCES `perusahaan` (`id_perusahaan`),
  ADD CONSTRAINT `stok_awal_ibfk_4` FOREIGN KEY (`created_by`) REFERENCES `user` (`id_user`);

-- Insert Data
INSERT INTO `perusahaan` (`id_perusahaan`, `nama_perusahaan`, `alamat`, `telepon`, `email`, `status_aktif`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'PT. Maju Bersama', 'Jl. Sudirman No. 10, Jakarta', '021-12345678', '', 1, NULL, '2025-09-15 22:53:59', '2025-09-17 12:09:24'),
(6, 'asdas', 'asdas', 'asdas', '', 0, NULL, '2025-09-17 17:55:05', '2025-09-17 18:39:25'),
(8, 'rwrwrw', 'werw', '', '', 1, NULL, '2025-09-17 18:36:54', '2025-09-17 18:36:54');

INSERT INTO `role_user` (`id_role`, `nama_role`, `deskripsi`) VALUES
(1, 'Super Admin', 'Mengelola semua perusahaan'),
(2, 'Admin Perusahaan', 'Mengelola seluruh data dan transaksi dalam lingkup perusahaannya'),
(3, 'Sales Online', 'Melakukan input penjualan ke konsumen, tidak menyimpan stok'),
(4, 'Admin Packing', 'Menangani proses packing, pengiriman, dan konfirmasi barang sampai'),
(5, 'Admin Return', 'Memproses verifikasi dan approval retur barang');

INSERT INTO `menu` (`id_menu`, `nama_menu`, `url`, `icon`, `urutan`, `id_parent`, `status_aktif`) VALUES
(1, 'Dashboard', 'dashboard', 'fas fa-tachometer-alt', 1, NULL, 1),
(2, 'Setup', '#', 'fas fa-database', 2, NULL, 1),
(3, 'Kategori Barang', 'setup/kategori', 'fas fa-tags', 2, 2, 1),
(4, 'Barang', 'setup/barang', 'fas fa-box', 3, 2, 1),
(5, 'Gudang', 'setup/gudang', 'fas fa-warehouse', 4, 2, 1),
(6, 'Pelanggan', 'setup/pelanggan', 'fas fa-users', 5, 2, 1),
(7, 'Supplier', 'setup/supplier', 'fas fa-truck', 6, 2, 1),
(8, 'User Management', '#', 'fas fa-user-cog', 7, 2, 1),
(9, 'Sales', 'setup/user/sales', 'fas fa-user-tag', 1, 8, 1),
(10, 'Admin Packing', 'setup/user/packing', 'fas fa-user-box', 2, 8, 1),
(11, 'Aktifitas', '#', 'fas fa-exchange-alt', 3, NULL, 1),
(12, 'Pemindahan Barang', 'aktifitas/pemindahan', 'fas fa-truck-loading', 1, 11, 1),
(13, 'Penerimaan Barang', 'aktifitas/penerimaan', 'fas fa-clipboard-check', 2, 11, 1),
(14, 'Retur Penjualan', 'aktifitas/retur_penjualan', 'fas fa-undo-alt', 3, 11, 1),
(15, 'Retur Pembelian', 'aktifitas/retur_pembelian', 'fas fa-undo', 4, 11, 1),
(16, 'Daftar', '#', 'fas fa-list', 4, NULL, 1),
(17, 'Pemindahan Barang', 'daftar/pemindahan', 'fas fa-truck', 1, 16, 1),
(18, 'Penerimaan Barang', 'daftar/penerimaan', 'fas fa-clipboard-list', 2, 16, 1),
(19, 'Retur Penjualan', 'daftar/retur_penjualan', 'fas fa-undo-alt', 3, 16, 1),
(20, 'Retur Pembelian', 'daftar/retur_pembelian', 'fas fa-undo', 4, 16, 1),
(21, 'Laporan', '#', 'fas fa-chart-bar', 5, NULL, 1),
(22, 'Sales', 'laporan/sales', 'fas fa-chart-line', 1, 21, 1),
(23, 'Packing', 'laporan/packing', 'fas fa-boxes', 2, 21, 1),
(24, 'Mutasi Barang', 'laporan/mutasi', 'fas fa-exchange-alt', 3, 21, 1),
(25, 'Ringkasan Stok', 'laporan/summary', 'fas fa-file-invoice-dollar', 4, 21, 1),
(26, 'Pengaturan', '#', 'fas fa-cog', 6, NULL, 1),
(27, 'Stok Awal', 'pengaturan/stok_awal', 'fas fa-dolly-flatbed', 1, 26, 1),
(28, 'Hak Akses', 'pengaturan/hak_akses', 'fas fa-user-shield', 2, 26, 1),
(29, 'Approval Flow', 'pengaturan/approval', 'fas fa-tasks', 3, 26, 1),
(30, 'Pengaturan Sistem', 'pengaturan/sistem', 'fas fa-sliders-h', 4, 26, 1),
(31, 'Perusahaan', 'setup/perusahaan', 'fas fa-building', 1, 2, 1);

INSERT INTO `user` (`id_user`, `nama`, `username`, `password_hash`, `id_role`, `id_perusahaan`, `email`, `telepon`, `created_by`, `aktif`, `created_at`, `updated_at`, `last_login`, `foto_profil`) VALUES
(1, 'Super Admin', 'admin', '$2y$10$Jk0IyD8hFSY6CbpX5MJtD.3GlVjw.g9hAVM/rymsjmza2cnTl02aq', 1, NULL, 'admin@example.com', '081234567890', NULL, 1, '2025-09-16 00:13:33', NULL, '2025-09-17 18:03:18', NULL),
(2, 'Admin Perusahaan', 'adminperusahaan', '$2y$10$Jk0IyD8hFSY6CbpX5MJtD.3GlVjw.g9hAVM/rymsjmza2cnTl02aq', 2, 1, 'admin@perusahaan.com', '081234567891', NULL, 1, '2025-09-16 00:13:33', NULL, '2025-09-17 12:41:16', NULL),
(3, 'Sales Online', 'sales', '$2y$10$Jk0IyD8hFSY6CbpX5MJtD.3GlVjw.g9hAVM/rymsjmza2cnTl02aq', 3, 1, 'sales@perusahaan.com', '081234567892', NULL, 1, '2025-09-16 00:13:33', NULL, NULL, NULL),
(4, 'Admin Packing', 'packing', '$2y$10$Jk0IyD8hFSY6CbpX5MJtD.3GlVjw.g9hAVM/rymsjmza2cnTl02aq', 4, 1, 'packing@perusahaan.com', '081234567893', NULL, 1, '2025-09-16 00:13:33', NULL, NULL, NULL),
(5, 'Admin Return', 'return', '$2y$10$Jk0IyD8hFSY6CbpX5MJtD.3GlVjw.g9hAVM/rymsjmza2cnTl02aq', 5, 1, 'return@perusahaan.com', '081234567894', NULL, 1, '2025-09-16 00:13:33', NULL, NULL, NULL);

INSERT INTO `hak_akses_menu` (`id_hak_akses`, `id_role`, `id_menu`, `akses`) VALUES
(1, 1, 1, 1),
(2, 1, 2, 1),
(3, 1, 3, 1),
(4, 1, 4, 1),
(5, 1, 5, 1),
(6, 1, 6, 1),
(7, 1, 7, 1),
(8, 1, 8, 1),
(9, 1, 9, 1),
(10, 1, 10, 1),
(11, 1, 11, 1),
(12, 1, 12, 1),
(13, 1, 13, 1),
(14, 1, 14, 1),
(15, 1, 15, 1),
(16, 1, 16, 1),
(17, 1, 17, 1),
(18, 1, 18, 1),
(19, 1, 19, 1),
(20, 1, 20, 1),
(21, 1, 21, 1),
(22, 1, 22, 1),
(23, 1, 23, 1),
(24, 1, 24, 1),
(25, 1, 25, 1),
(26, 1, 26, 1),
(27, 1, 27, 1),
(28, 1, 28, 1),
(29, 1, 29, 1),
(30, 1, 30, 1),
(32, 2, 1, 1),
(33, 2, 2, 1),
(34, 2, 3, 1),
(35, 2, 4, 1),
(36, 2, 5, 1),
(37, 2, 6, 1),
(38, 2, 7, 1),
(39, 2, 8, 1),
(40, 2, 9, 1),
(41, 2, 10, 1),
(42, 2, 11, 1),
(43, 2, 12, 1),
(44, 2, 13, 1),
(45, 2, 14, 1),
(46, 2, 15, 1),
(47, 2, 16, 1),
(48, 2, 17, 1),
(49, 2, 18, 1),
(50, 2, 19, 1),
(51, 2, 20, 1),
(52, 2, 21, 1),
(53, 2, 22, 1),
(54, 2, 23, 1),
(55, 2, 24, 1),
(56, 2, 25, 1),
(57, 2, 26, 1),
(58, 2, 27, 1),
(59, 2, 28, 1),
(60, 2, 29, 1),
(61, 2, 30, 1),
(63, 3, 1, 1),
(64, 3, 2, 1),
(65, 3, 4, 1),
(66, 3, 6, 1),
(67, 3, 11, 1),
(68, 3, 13, 1),
(69, 3, 14, 1),
(70, 3, 15, 1),
(71, 3, 16, 1),
(72, 3, 18, 1),
(73, 3, 19, 1),
(74, 3, 20, 1),
(75, 3, 21, 1),
(76, 3, 22, 1),
(77, 4, 1, 1),
(78, 4, 11, 1),
(79, 4, 12, 1),
(80, 4, 13, 1),
(81, 4, 16, 1),
(82, 4, 17, 1),
(83, 4, 18, 1),
(84, 4, 21, 1),
(85, 4, 23, 1),
(86, 5, 1, 1),
(87, 5, 11, 1),
(88, 5, 14, 1),
(89, 5, 15, 1),
(90, 5, 16, 1),
(91, 5, 19, 1),
(92, 5, 20, 1),
(93, 1, 31, 1),
(94, 2, 31, 1);

INSERT INTO `gudang` (`id_gudang`, `id_perusahaan`, `nama_gudang`, `alamat`, `telepon`, `created_by`, `status_aktif`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'Gudang Utama', 'Alamat Gudang Utama', '02112345679', NULL, 1, NULL, '2025-09-16 00:12:08', NULL),
(2, 1, 'Gudang Jakarta Timur', '124fhgbknlm,', '', 1, 1, NULL, '2025-09-17 19:01:45', '2025-09-17 19:18:25');

INSERT INTO `kategori` (`id_kategori`, `id_perusahaan`, `nama_kategori`, `deskripsi`, `status_aktif`, `deleted_at`, `created_at`) VALUES
(1, 1, 'asdas', 'asdnaskjd', 1, NULL, '2025-09-18 00:28:37');

INSERT INTO `approval_flow` (`id_approval`, `tipe_transaksi`, `status_dari`, `status_ke`, `id_role`, `urutan`, `status_aktif`) VALUES
(1, 'penjualan', 'Draft', 'Packing', 4, 1, 1),
(2, 'penjualan', 'Packing', 'Shipping', 4, 2, 1),
(3, 'penjualan', 'Shipping', 'Delivered', 4, 3, 1),
(4, 'penjualan', 'Draft', 'Cancelled', 4, 4, 1),
(5, 'penjualan', 'Packing', 'Cancelled', 4, 5, 1),
(6, 'penjualan', 'Shipping', 'Cancelled', 4, 6, 1),
(7, 'transfer_stok', 'Draft', 'Packing', 4, 1, 1),
(8, 'transfer_stok', 'Packing', 'Shipping', 4, 2, 1),
(9, 'transfer_stok', 'Shipping', 'Delivered', 4, 3, 1),
(10, 'transfer_stok', 'Draft', 'Cancelled', 4, 4, 1),
(11, 'transfer_stok', 'Packing', 'Cancelled', 4, 5, 1),
(12, 'transfer_stok', 'Shipping', 'Cancelled', 4, 6, 1),
(13, 'pembelian', 'Draft', 'Received', 2, 1, 1),
(14, 'pembelian', 'Received', 'Completed', 2, 2, 1),
(15, 'pembelian', 'Draft', 'Cancelled', 2, 3, 1),
(16, 'pembelian', 'Received', 'Cancelled', 2, 4, 1),
(17, 'penerimaan', 'Draft', 'Received', 2, 1, 1),
(18, 'penerimaan', 'Received', 'Completed', 2, 2, 1),
(19, 'penerimaan', 'Draft', 'Cancelled', 2, 3, 1),
(20, 'penerimaan', 'Received', 'Cancelled', 2, 4, 1),
(21, 'retur_penjualan', 'Requested', 'Verification', 5, 1, 1),
(22, 'retur_penjualan', 'Verification', 'Approved', 5, 2, 1),
(23, 'retur_penjualan', 'Verification', 'Rejected', 5, 3, 1),
(24, 'retur_penjualan', 'Approved', 'Completed', 5, 4, 1),
(25, 'retur_pembelian', 'Requested', 'Verification', 5, 1, 1),
(26, 'retur_pembelian', 'Verification', 'Approved', 5, 2, 1),
(27, 'retur_pembelian', 'Verification', 'Rejected', 5, 3, 1),
(28, 'retur_pembelian', 'Approved', 'Completed', 5, 4, 1);