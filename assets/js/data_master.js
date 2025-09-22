// Fungsi untuk mendapatkan data berdasarkan perusahaan
function getDataByPerusahaan(id_perusahaan) {
	return {
		gudang: dataMaster.gudang[id_perusahaan] || [],
		barang: dataMaster.barang[id_perusahaan] || [],
		pelanggan: dataMaster.pelanggan[id_perusahaan] || [],
	};
}

// Fungsi untuk mendapatkan stok barang
function getStokBarang(id_gudang, id_barang) {
	if (dataMaster.stok[id_gudang] && dataMaster.stok[id_gudang][id_barang]) {
		return dataMaster.stok[id_gudang][id_barang];
	}
	return null;
}

// Fungsi untuk mendapatkan barang berdasarkan gudang
function getBarangByGudang(id_gudang) {
	var result = [];
	var stokGudang = dataMaster.stok[id_gudang] || {};

	// Cari perusahaan dari gudang
	var id_perusahaan = null;
	for (var pid in dataMaster.gudang) {
		var gudangs = dataMaster.gudang[pid];
		for (var i = 0; i < gudangs.length; i++) {
			if (gudangs[i].id_gudang == id_gudang) {
				id_perusahaan = pid;
				break;
			}
		}
		if (id_perusahaan) break;
	}

	if (id_perusahaan && dataMaster.barang[id_perusahaan]) {
		var barangList = dataMaster.barang[id_perusahaan];
		for (var i = 0; i < barangList.length; i++) {
			var barang = barangList[i];
			var stok = stokGudang[barang.id_barang];
			if (stok && stok.jumlah > 0) {
				result.push({
					id_barang: barang.id_barang,
					nama_barang: barang.nama_barang,
					sku: barang.sku,
					jumlah: stok.jumlah,
					reserved: stok.reserved,
				});
			}
		}
	}

	return result;
}

// Fungsi untuk mendapatkan alamat pelanggan
function getAlamatPelanggan(id_pelanggan) {
	for (var pid in dataMaster.pelanggan) {
		var pelangganList = dataMaster.pelanggan[pid];
		for (var i = 0; i < pelangganList.length; i++) {
			if (pelangganList[i].id_pelanggan == id_pelanggan) {
				return {
					alamat: pelangganList[i].alamat,
					telepon: pelangganList[i].telepon,
					email: pelangganList[i].email,
				};
			}
		}
	}
	return null;
}
