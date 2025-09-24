<script>
    $(document).ready(function () {
        // For Super Admin, get data based on company
        <?php if ($this->session->userdata('id_role') == 1): ?>
            $('#id_perusahaan').change(function () {
                var id_perusahaan = $(this).val();

                if (id_perusahaan) {
                    $('#id_gudang_asal').html('<option value="">Loading...</option>');

                    $.ajax({
                        url: '<?php echo site_url('aktifitas/pemindahan/get_data_by_perusahaan'); ?>',
                        method: 'POST',
                        data: {
                            id_perusahaan: id_perusahaan,
                            <?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>'
                        },
                        dataType: 'json',
                        success: function (response) {
                            // Update gudang asal
                            var gudangAsalHtml = '<option value="">-- Pilih Gudang --</option>';
                            $.each(response.gudang, function (i, item) {
                                gudangAsalHtml += '<option value="' + item.id_gudang + '">' + item.nama_gudang + '</option>';
                            });
                            $('#id_gudang_asal').html(gudangAsalHtml);

                            // Update gudang tujuan
                            var gudangTujuanHtml = '<option value="">-- Pilih Gudang Tujuan --</option>';
                            $.each(response.gudang, function (i, item) {
                                gudangTujuanHtml += '<option value="' + item.id_gudang + '">' + item.nama_gudang + '</option>';
                            });
                            $('#id_gudang_tujuan').html(gudangTujuanHtml);

                            // Update pelanggan
                            var pelangganHtml = '<option value="">-- Pilih Pelanggan --</option>';
                            $.each(response.pelanggan, function (i, item) {
                                pelangganHtml += '<option value="' + item.id_pelanggan + '">' + item.nama_pelanggan + ' (' + item.tipe_pelanggan + ')</option>';
                            });
                            $('#id_pelanggan').html(pelangganHtml);

                            // Reset barang tables
                            $('#table_barang_tersedia tbody').html('<tr><td colspan="10" class="text-center">Pilih gudang terlebih dahulu</td></tr>');
                            $('#table_barang_dipindahkan tbody').html('');
                            updateBarangDipindahkan();
                            updateJumlahBarang();
                        },
                        error: function () {
                            alert('Terjadi kesalahan saat mengambil data perusahaan');
                            $('#id_gudang_asal').html('<option value="">-- Pilih Gudang --</option>');
                        }
                    });
                } else {
                    // Reset all fields
                    $('#id_gudang_asal').html('<option value="">-- Pilih Gudang --</option>');
                    $('#id_gudang_tujuan').html('<option value="">-- Pilih Gudang Tujuan --</option>');
                    $('#id_pelanggan').html('<option value="">-- Pilih Pelanggan --</option>');
                    $('#table_barang_tersedia tbody').html('<tr><td colspan="10" class="text-center">Pilih gudang terlebih dahulu</td></tr>');
                    $('#table_barang_dipindahkan tbody').html('');
                    updateBarangDipindahkan();
                    updateJumlahBarang();
                }
            });

            // Trigger change if there's initial value
            if ($('#id_perusahaan').val()) {
                $('#id_perusahaan').trigger('change');
            }
        <?php endif; ?>

        // Toggle tipe tujuan
        $('#tipe_tujuan').change(function () {
            var tipe = $(this).val();

            // Hide all fields
            $('#gudang_tujuan_field').hide();
            $('#pelanggan_field').hide();
            $('#konsumen_field').hide();

            // Remove required from all
            $('#id_gudang_tujuan').removeAttr('required');
            $('#id_pelanggan').removeAttr('required');
            $('#nama_konsumen').removeAttr('required');
            $('#id_toko_konsumen').removeAttr('required');
            $('#alamat_konsumen').removeAttr('required');

            if (tipe == 'gudang') {
                $('#gudang_tujuan_field').show();
                $('#id_gudang_tujuan').attr('required', true);
            } else if (tipe == 'pelanggan') {
                $('#pelanggan_field').show();
                $('#id_pelanggan').attr('required', true);
            } else if (tipe == 'konsumen') {
                $('#konsumen_field').show();
                $('#nama_konsumen').attr('required', true);
                $('#id_toko_konsumen').attr('required', true);
                $('#alamat_konsumen').attr('required', true);
            }
        });

        // Get alamat pelanggan
        $('#id_pelanggan').change(function () {
            var id_pelanggan = $(this).val();

            if (id_pelanggan) {
                $.ajax({
                    url: '<?php echo site_url('aktifitas/pemindahan/get_alamat_pelanggan'); ?>',
                    method: 'POST',
                    data: {
                        id_pelanggan: id_pelanggan,
                        <?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>'
                    },
                    dataType: 'json',
                    success: function (response) {
                        if (response.status == 'success') {
                            $('#alamat_text').text(response.alamat || '-');
                            $('#telepon_text').text(response.telepon || '-');
                            $('#email_text').text(response.email || '-');
                            $('#alamat_pelanggan').show();
                        } else {
                            $('#alamat_pelanggan').hide();
                        }
                    }
                });
            } else {
                $('#alamat_pelanggan').hide();
            }
        });

        // Get barang by gudang
        $('#id_gudang_asal').change(function () {
            var id_gudang = $(this).val();

            if (id_gudang) {
                $('#table_barang_tersedia tbody').html('<tr><td colspan="10" class="text-center">Loading...</td></tr>');

                $.ajax({
                    url: '<?php echo site_url('aktifitas/pemindahan/get_barang_by_gudang'); ?>',
                    method: 'POST',
                    data: {
                        id_gudang: id_gudang,
                        <?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>'
                    },
                    dataType: 'json',
                    success: function (response) {
                        var html = '';
                        if (response.length > 0) {
                            $.each(response, function (i, item) {
                                html += '<tr>' +
                                    '<td>' + (i + 1) + '</td>' +
                                    '<td>' + item.nama_barang + '</td>' +
                                    '<td>' + (item.kode_barang || '-') + '</td>' +
                                    '<td>' + item.sku + '</td>' +
                                    '<td>' + (item.ukuran || '-') + '</td>' +
                                    '<td>' + (item.motor || '-') + '</td>' +
                                    '<td>' + (item.warna || '-') + '</td>' +
                                    '<td>' + (item.jumlah - item.reserved) + '</td>' +
                                    '<td>' + (item.satuan || '-') + '</td>' +
                                    '<td><button type="button" class="btn btn-sm btn-primary btn-tambah-barang" ' +
                                    'data-id_barang="' + item.id_barang + '" data-nama_barang="' + item.nama_barang + '" ' +
                                    'data-kode_barang="' + (item.kode_barang || '') + '" data-sku="' + item.sku + '" ' +
                                    'data-ukuran="' + (item.ukuran || '') + '" data-motor="' + (item.motor || '') + '" ' +
                                    'data-warna="' + (item.warna || '') + '" data-satuan="' + (item.satuan || '') + '" ' +
                                    'data-stok="' + (item.jumlah - item.reserved) + '">' +
                                    '<i class="fas fa-plus"></i></button></td>' +
                                    '</tr>';
                            });
                        } else {
                            html = '<tr><td colspan="10" class="text-center">Tidak ada barang tersedia</td></tr>';
                        }

                        $('#table_barang_tersedia tbody').html(html);
                        updateJumlahBarang();
                    },
                    error: function () {
                        $('#table_barang_tersedia tbody').html('<tr><td colspan="10" class="text-center">Error loading data</td></tr>');
                    }
                });
            } else {
                $('#table_barang_tersedia tbody').html('<tr><td colspan="10" class="text-center">Pilih gudang terlebih dahulu</td></tr>');
                updateJumlahBarang();
            }
        });

        // Search barang
        $('#cari_barang').keyup(function () {
            var keyword = $(this).val().toLowerCase();
            var visibleCount = 0;

            $('#table_barang_tersedia tbody tr').each(function () {
                var nama_barang = $(this).find('td:eq(1)').text().toLowerCase();
                var sku = $(this).find('td:eq(3)').text().toLowerCase();
                var kode_barang = $(this).find('td:eq(2)').text().toLowerCase();
                var motor = $(this).find('td:eq(5)').text().toLowerCase();

                if (nama_barang.indexOf(keyword) !== -1 || sku.indexOf(keyword) !== -1 || kode_barang.indexOf(keyword) !== -1 || motor.indexOf(keyword) !== -1) {
                    $(this).show();
                    visibleCount++;
                } else {
                    $(this).hide();
                }
            });

            // Update jumlah barang tersedia yang terlihat
            $('#jumlah-barang-tersedia').text(visibleCount + ' barang');
        });

        // Add barang to dipindahkan list
        $(document).on('click', '.btn-tambah-barang', function () {
            var id_barang = $(this).data('id_barang');
            var nama_barang = $(this).data('nama_barang');
            var kode_barang = $(this).data('kode_barang');
            var sku = $(this).data('sku');
            var ukuran = $(this).data('ukuran');
            var motor = $(this).data('motor');
            var warna = $(this).data('warna');
            var satuan = $(this).data('satuan');
            var stok = $(this).data('stok');

            // Check if barang already added
            var exists = false;
            $('#table_barang_dipindahkan tbody tr').each(function () {
                if ($(this).find('input.jumlah-barang').data('id_barang') == id_barang) {
                    exists = true;
                    return false;
                }
            });

            if (exists) {
                alert('Barang sudah ditambahkan!');
                return;
            }

            var no = $('#table_barang_dipindahkan tbody tr').length + 1;
            var html = '<tr>' +
                '<td>' + no + '</td>' +
                '<td>' + nama_barang + '</td>' +
                '<td>' + kode_barang + '</td>' +
                '<td>' + sku + '</td>' +
                '<td>' + ukuran + '</td>' +
                '<td>' + motor + '</td>' +
                '<td>' + warna + '</td>' +
                '<td><input type="number" class="form-control form-control-sm jumlah-barang" name="jumlah[]" min="1" max="' + stok + '" value="1" data-id_barang="' + id_barang + '"></td>' +
                '<td>' + satuan + '</td>' +
                '<td><button type="button" class="btn btn-sm btn-danger btn-hapus-barang" data-id_barang="' + id_barang + '"><i class="fas fa-trash"></i></button></td>' +
                '</tr>';

            $('#table_barang_dipindahkan tbody').append(html);

            // Update hidden field and count
            updateBarangDipindahkan();
            updateJumlahBarang();
        });

        // Remove barang from dipindahkan list
        $(document).on('click', '.btn-hapus-barang', function () {
            $(this).closest('tr').remove();

            // Renumber rows
            $('#table_barang_dipindahkan tbody tr').each(function (index) {
                $(this).find('td:first').text(index + 1);
            });

            // Update hidden field and count
            updateBarangDipindahkan();
            updateJumlahBarang();
        });

        // Update hidden field for barang dipindahkan
        function updateBarangDipindahkan() {
            var barang = [];

            $('#table_barang_dipindahkan tbody tr').each(function () {
                var id_barang = $(this).find('input.jumlah-barang').data('id_barang');
                var jumlah = $(this).find('input.jumlah-barang').val();

                if (id_barang && jumlah) {
                    barang.push({
                        id_barang: id_barang,
                        jumlah: jumlah
                    });
                }
            });

            $('#barang_dipindahkan').val(JSON.stringify(barang));
        }

        // Update jumlah barang badge
        function updateJumlahBarang() {
            var tersediaCount = $('#table_barang_tersedia tbody tr:visible').length;
            var dipindahkanCount = $('#table_barang_dipindahkan tbody tr').length;

            $('#jumlah-barang-tersedia').text(tersediaCount + ' barang');
            $('#jumlah-barang-dipindahkan').text(dipindahkanCount + ' barang');
        }

        // Form validation before submit
        $('#form-pemindahan').submit(function (e) {
            // Update hidden field
            updateBarangDipindahkan();

            var barang = JSON.parse($('#barang_dipindahkan').val());

            if (barang.length === 0) {
                e.preventDefault();
                alert('Pilih minimal 1 barang untuk dipindahkan!');
                return false;
            }

            // Validate each item
            for (var i = 0; i < barang.length; i++) {
                if (barang[i].jumlah <= 0) {
                    e.preventDefault();
                    alert('Jumlah barang harus lebih dari 0!');
                    return false;
                }
            }
        });

        // Trigger change on page load
        if ($('#id_gudang_asal').val()) {
            $('#id_gudang_asal').trigger('change');
        }

        // Trigger tipe tujuan change on page load
        $('#tipe_tujuan').trigger('change');

        // Trigger pelanggan change on page load if exists
        if ($('#id_pelanggan').val()) {
            $('#id_pelanggan').trigger('change');
        }

        // Initialize jumlah barang badges
        updateJumlahBarang();
    });
</script>