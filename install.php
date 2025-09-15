<?php
// Cek apakah file diakses langsung
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

// Load database configuration
require_once 'application/config/database.php';

// Connect to database
$conn = mysqli_connect($db['default']['hostname'], $db['default']['username'], $db['default']['password'], $db['default']['database']);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Insert default user if not exists
$sql = "SELECT COUNT(*) as count FROM user WHERE username = 'admin'";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

if ($row['count'] == 0) {
    // Insert default user
    $password = password_hash('password', PASSWORD_DEFAULT);
    $sql = "INSERT INTO user (nama, username, password_hash, id_role, id_perusahaan, aktif, created_at) 
            VALUES ('Administrator', 'admin', '$password', 1, 1, 1, NOW())";

    if (mysqli_query($conn, $sql)) {
        echo "Default user created successfully!<br>";
    } else {
        echo "Error creating default user: " . mysqli_error($conn) . "<br>";
    }
}

// Insert default company if not exists
$sql = "SELECT COUNT(*) as count FROM perusahaan WHERE nama_perusahaan = 'Demo Company'";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

if ($row['count'] == 0) {
    $sql = "INSERT INTO perusahaan (nama_perusahaan, status_aktif, created_at) 
            VALUES ('Demo Company', 1, NOW())";

    if (mysqli_query($conn, $sql)) {
        echo "Default company created successfully!<br>";
    } else {
        echo "Error creating default company: " . mysqli_error($conn) . "<br>";
    }
}

// Insert default warehouse if not exists
$sql = "SELECT COUNT(*) as count FROM gudang WHERE nama_gudang = 'Gudang Utama'";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

if ($row['count'] == 0) {
    $sql = "INSERT INTO gudang (id_perusahaan, nama_gudang, status_aktif, created_at) 
            VALUES (1, 'Gudang Utama', 1, NOW())";

    if (mysqli_query($conn, $sql)) {
        echo "Default warehouse created successfully!<br>";
    } else {
        echo "Error creating default warehouse: " . mysqli_error($conn) . "<br>";
    }
}

// Insert default category if not exists
$sql = "SELECT COUNT(*) as count FROM kategori WHERE nama_kategori = 'Umum'";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

if ($row['count'] == 0) {
    $sql = "INSERT INTO kategori (id_perusahaan, nama_kategori, status_aktif, created_at) 
            VALUES (1, 'Umum', 1, NOW())";

    if (mysqli_query($conn, $sql)) {
        echo "Default category created successfully!<br>";
    } else {
        echo "Error creating default category: " . mysqli_error($conn) . "<br>";
    }
}

// Insert default product if not exists
$sql = "SELECT COUNT(*) as count FROM barang WHERE nama_barang = 'Contoh Produk'";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

if ($row['count'] == 0) {
    $sql = "INSERT INTO barang (id_perusahaan, id_kategori, nama_barang, sku, satuan, harga_jual, aktif, created_at) 
            VALUES (1, 1, 'Contoh Produk', 'PROD001', 'pcs', 10000, 1, NOW())";

    if (mysqli_query($conn, $sql)) {
        echo "Default product created successfully!<br>";
    } else {
        echo "Error creating default product: " . mysqli_error($conn) . "<br>";
    }
}

// Insert default stock if not exists
$sql = "SELECT COUNT(*) as count FROM stok_gudang WHERE id_barang = 1 AND id_gudang = 1";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

if ($row['count'] == 0) {
    $sql = "INSERT INTO stok_gudang (id_perusahaan, id_barang, id_gudang, jumlah, reserved, created_at) 
            VALUES (1, 1, 1, 100, 0, NOW())";

    if (mysqli_query($conn, $sql)) {
        echo "Default stock created successfully!<br>";
    } else {
        echo "Error creating default stock: " . mysqli_error($conn) . "<br>";
    }
}

echo "Installation completed!<br>";
echo "You can now login with username: admin and password: password<br>";

// Close connection
mysqli_close($conn);
?>