<!-- Tambahkan di bagian head -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script>
    $(document).ready(function () {
        // Fungsi untuk toggle kolom konsumen
        function toggleKonsumenColumns(show) {
            if (show) {
                $('.konsumen-column').show();
            } else {
                $('.konsumen-column').hide();
            }
        }

        // Inisialisasi kolom konsumen berdasarkan tipe tujuan awal
        var initialTipeTujuan = $('#tipe_tujuan').val();
        if (initialTipeTujuan === 'konsumen' || <?php echo $this->session->userdata('id_role') == 3 ? 'true' : 'false'; ?>) {
            toggleKonsumenColumns(true);
        } else {
            toggleKonsumenColumns(false);
        }

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
                            $('#table_barang_modal tbody').html('<tr><td colspan="11" class="text-center">Pilih gudang terlebih dahulu</td></tr>');
                            $('#table_barang_dipindahkan tbody').html('');
                            updateBarangDipindahkan();
                            updateJumlahBarang();
                        },
                        error: function () {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Terjadi kesalahan saat mengambil data perusahaan',
                                confirmButtonColor: '#3085d6',
                                confirmButtonText: 'OK'
                            });
                            $('#id_gudang_asal').html('<option value="">-- Pilih Gudang --</option>');
                        }
                    });
                } else {
                    // Reset all fields
                    $('#id_gudang_asal').html('<option value="">-- Pilih Gudang --</option>');
                    $('#id_gudang_tujuan').html('<option value="">-- Pilih Gudang Tujuan --</option>');
                    $('#id_pelanggan').html('<option value="">-- Pilih Pelanggan --</option>');
                    $('#table_barang_modal tbody').html('<tr><td colspan="11" class="text-center">Pilih gudang terlebih dahulu</td></tr>');
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

            // Remove required from all
            $('#id_gudang_tujuan').removeAttr('required');
            $('#id_pelanggan').removeAttr('required');

            if (tipe == 'gudang') {
                $('#gudang_tujuan_field').show();
                $('#id_gudang_tujuan').attr('required', true);
                toggleKonsumenColumns(false);
            } else if (tipe == 'pelanggan') {
                $('#pelanggan_field').show();
                $('#id_pelanggan').attr('required', true);
                toggleKonsumenColumns(false);
            } else if (tipe == 'konsumen') {
                toggleKonsumenColumns(true);
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

        // Validasi sebelum membuka modal barang
        $('#btn-tambah-barang-modal').click(function (e) {
            var id_gudang = $('#id_gudang_asal').val();

            if (!id_gudang) {
                e.preventDefault();
                e.stopPropagation();

                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Silakan pilih gudang asal terlebih dahulu!',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                });

                $('#id_gudang_asal').focus();
                return false;
            }

            // Reset modal state
            $('#table_barang_modal tbody').find('input[type="checkbox"]').prop('checked', false);
            $('#table_barang_modal tbody').find('input[type="number"]').val(1);
            $('#select-all').prop('checked', false);
            $('#info-selected').addClass('d-none');
        });

        // Get barang by gudang untuk modal
        $('#id_gudang_asal').change(function () {
            var id_gudang = $(this).val();

            if (id_gudang) {
                $('#table_barang_modal tbody').html('<tr><td colspan="11" class="text-center">Loading...</td></tr>');

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
                                    '<td>' + item.nama_barang + '</td>' +
                                    '<td>' + (item.kode_barang || '-') + '</td>' +
                                    '<td>' + (item.motor || '-') + '</td>' +
                                    '<td>' + (item.warna || '-') + '</td>' +
                                    '<td>' + (item.jumlah - item.reserved) + '</td>' +
                                    '<td>' + (item.satuan || '-') + '</td>' +
                                    '<td><input type="number" class="form-control form-control-sm jumlah-barang-modal" ' +
                                    'min="1" max="' + (item.jumlah - item.reserved) + '" value="1" ' +
                                    'data-id_barang="' + item.id_barang + '"></td>' +
                                    '<td>' +
                                    '<div class="custom-control custom-checkbox">' +
                                    '<input type="checkbox" class="custom-control-input checkbox-barang" ' +
                                    'id="barang-' + item.id_barang + '" ' +
                                    'data-id_barang="' + item.id_barang + '" ' +
                                    'data-nama_barang="' + item.nama_barang + '" ' +
                                    'data-kode_barang="' + (item.kode_barang || '') + '" ' +
                                    'data-motor="' + (item.motor || '') + '" ' +
                                    'data-warna="' + (item.warna || '') + '" ' +
                                    'data-satuan="' + (item.satuan || '') + '" ' +
                                    'data-stok="' + (item.jumlah - item.reserved) + '">' +
                                    '<label class="custom-control-label" for="barang-' + item.id_barang + '"></label>' +
                                    '</div>' +
                                    '</td>' +
                                    '</tr>';
                            });
                        } else {
                            html = '<tr><td colspan="11" class="text-center">Tidak ada barang tersedia</td></tr>';
                        }

                        $('#table_barang_modal tbody').html(html);
                    },
                    error: function () {
                        $('#table_barang_modal tbody').html('<tr><td colspan="11" class="text-center">Error loading data</td></tr>');
                    }
                });
            } else {
                $('#table_barang_modal tbody').html('<tr><td colspan="11" class="text-center">Pilih gudang terlebih dahulu</td></tr>');
            }
        });

        // Search barang di modal
        $('#cari_barang_modal').keyup(function () {
            var keyword = $(this).val().toLowerCase();

            $('#table_barang_modal tbody tr').each(function () {
                var nama_barang = $(this).find('td:eq(0)').text().toLowerCase();
                var kode_barang = $(this).find('td:eq(1)').text().toLowerCase();
                var motor = $(this).find('td:eq(2)').text().toLowerCase();

                if (nama_barang.indexOf(keyword) !== -1 || kode_barang.indexOf(keyword) !== -1 || motor.indexOf(keyword) !== -1) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });

        // Select all checkbox
        $('#select-all').change(function () {
            $('.checkbox-barang').prop('checked', $(this).prop('checked'));
            updateSelectedCount();
        });

        // Individual checkbox change
        $(document).on('change', '.checkbox-barang', function () {
            updateSelectedCount();

            // Check if all checkboxes are checked
            var allChecked = $('.checkbox-barang:not(:checked)').length === 0;
            $('#select-all').prop('checked', allChecked);
        });

        // Update selected count
        function updateSelectedCount() {
            var selectedCount = $('.checkbox-barang:checked').length;
            $('#selected-count').text(selectedCount);

            if (selectedCount > 0) {
                $('#info-selected').removeClass('d-none');
            } else {
                $('#info-selected').addClass('d-none');
            }
        }

        // Add selected barang to dipindahan list
        $('#btn-pilih-semua').click(function () {
            var selectedBarang = [];
            var isKonsumen = $('#tipe_tujuan').val() === 'konsumen' || <?php echo $this->session->userdata('id_role') == 3 ? 'true' : 'false'; ?>;

            $('.checkbox-barang:checked').each(function () {
                var checkbox = $(this);
                var row = checkbox.closest('tr');
                var jumlahInput = row.find('.jumlah-barang-modal');
                var jumlah = parseInt(jumlahInput.val()) || 1;
                var maxStok = parseInt(checkbox.data('stok'));

                if (jumlah > maxStok) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan',
                        text: 'Jumlah melebihi stok tersedia untuk ' + checkbox.data('nama_barang'),
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                selectedBarang.push({
                    id_barang: checkbox.data('id_barang'),
                    nama_barang: checkbox.data('nama_barang'),
                    kode_barang: checkbox.data('kode_barang'),
                    motor: checkbox.data('motor'),
                    warna: checkbox.data('warna'),
                    satuan: checkbox.data('satuan'),
                    jumlah: jumlah
                });
            });

            if (selectedBarang.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Silakan pilih minimal 1 barang!',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                });
                return;
            }

            // Add selected barang to table
            $.each(selectedBarang, function (i, barang) {
                // Check if barang already added
                var exists = false;
                $('#table_barang_dipindahkan tbody tr').each(function () {
                    if ($(this).find('input.jumlah-barang').data('id_barang') == barang.id_barang) {
                        exists = true;
                        return false;
                    }
                });

                if (!exists) {
                    var no = $('#table_barang_dipindahkan tbody tr').length + 1;

                    var html = '<tr>' +
                        '<td>' + no + '</td>' +
                        '<td>' + barang.nama_barang + '</td>' +
                        '<td>' + barang.motor + '</td>' +
                        '<td>' + barang.warna + '</td>' +
                        '<td><input type="number" class="form-control form-control-sm jumlah-barang" name="jumlah[]" min="1" value="' + barang.jumlah + '" data-id_barang="' + barang.id_barang + '"></td>' +
                        '<td>' + barang.satuan + '</td>';

                    // Tambahkan kolom konsumen jika diperlukan
                    if (isKonsumen) {
                        html += '<td class="konsumen-column">' +
                            '<select class="form-control form-control-sm toko-konsumen" name="toko_konsumen[]" required>' +
                            '<option value="">-- Pilih Toko --</option>';
                        <?php foreach ($toko_konsumen as $toko): ?>
                            html += '<option value="<?php echo $toko->id_toko_konsumen; ?>"><?php echo $toko->nama_toko_konsumen; ?></option>';
                        <?php endforeach; ?>
                        html += '</select>' +
                            '</td>' +
                            '<td class="konsumen-column">' +
                            '<input type="text" class="form-control form-control-sm nama-konsumen" name="nama_konsumen[]" placeholder="Nama konsumen" required>' +
                            '</td>' +
                            '<td class="konsumen-column">' + '<textarea class="form-control form-control-sm mt-1 alamat-konsumen" name="alamat_konsumen[]" rows="2" placeholder="Alamat konsumen" required></textarea>' +
                            '</td>';
                    }

                    html += '<td>' +
                        '<button type="button" class="btn btn-sm btn-danger btn-hapus-barang" data-id_barang="' + barang.id_barang + '"><i class="fas fa-trash"></i></button>' +
                        '</td>' +
                        '</tr>';

                    $('#table_barang_dipindahkan tbody').append(html);
                }
            });

            // Update hidden field and count
            updateBarangDipindahkan();
            updateJumlahBarang();

            // Close modal
            $('#modalBarang').modal('hide');

            // Show success message
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: selectedBarang.length + ' barang berhasil ditambahkan',
                timer: 2000,
                showConfirmButton: false
            });
        });

        // Remove barang from dipindahan list
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
            var isKonsumen = $('#tipe_tujuan').val() === 'konsumen' || <?php echo $this->session->userdata('id_role') == 3 ? 'true' : 'false'; ?>;

            $('#table_barang_dipindahkan tbody tr').each(function (index) {
                var id_barang = $(this).find('input.jumlah-barang').data('id_barang');
                var jumlah = $(this).find('input.jumlah-barang').val();

                var barang_data = {
                    id_barang: id_barang,
                    jumlah: jumlah
                };

                if (isKonsumen) {
                    barang_data.id_toko_konsumen = $(this).find('select.toko-konsumen').val();
                    barang_data.nama_konsumen = $(this).find('input.nama-konsumen').val();
                    barang_data.alamat_konsumen = $(this).find('textarea.alamat-konsumen').val();
                }

                if (id_barang && jumlah) {
                    barang.push(barang_data);
                }
            });

            $('#barang_dipindahkan').val(JSON.stringify(barang));
        }

        // Update jumlah barang badge
        function updateJumlahBarang() {
            var dipindahkanCount = $('#table_barang_dipindahkan tbody tr').length;
            $('#jumlah-barang-dipindahkan').text(dipindahkanCount + ' barang');
        }

        // Form validation before submit
        $('#form-pemindahan').submit(function (e) {
            // Update hidden field
            updateBarangDipindahkan();

            var barang = JSON.parse($('#barang_dipindahkan').val());
            var isKonsumen = $('#tipe_tujuan').val() === 'konsumen' || <?php echo $this->session->userdata('id_role') == 3 ? 'true' : 'false'; ?>;

            if (barang.length === 0) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Pilih minimal 1 barang untuk dipindahkan!',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                });
                return false;
            }

            // Validate each item
            for (var i = 0; i < barang.length; i++) {
                if (barang[i].jumlah <= 0) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan',
                        text: 'Jumlah barang harus lebih dari 0!',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    });
                    return false;
                }

                // Validate konsumen data if needed
                if (isKonsumen) {
                    if (!barang[i].id_toko_konsumen || !barang[i].nama_konsumen || !barang[i].alamat_konsumen) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'warning',
                            title: 'Peringatan',
                            text: 'Lengkapi data toko dan konsumen untuk semua barang!',
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'OK'
                        });
                        return false;
                    }
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