<script>
    $(document).ready(function () {
        // Ambil CSRF token
        var csrf_token = $('meta[name="csrf-token"]').attr('content');
        if (!csrf_token) {
            csrf_token = $('input[name="<?php echo $this->security->get_csrf_token_name(); ?>"]').val();
        }

        // Untuk Super Admin, ambil data berdasarkan perusahaan
        <?php if ($this->session->userdata('id_role') == 1): ?>
            $('#id_perusahaan').change(function () {
                var id_perusahaan = $(this).val();

                if (id_perusahaan) {
                    $.ajax({
                        url: '<?php echo site_url('aktifitas/pemindahan/get_data_by_perusahaan'); ?>',
                        method: 'POST',
                        data: {
                            <?php echo $this->security->get_csrf_token_name(); ?>: csrf_token,
                            id_perusahaan: id_perusahaan
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

                            // Update barang
                            var barangHtml = '<option value="">-- Pilih Barang --</option>';
                            $.each(response.barang, function (i, item) {
                                barangHtml += '<option value="' + item.id_barang + '">' + item.nama_barang + ' (' + item.sku + ')</option>';
                            });

                            // Update semua select barang di tabel
                            $('.select-barang').each(function () {
                                var currentValue = $(this).val();
                                $(this).html(barangHtml);
                                $(this).val(currentValue);
                            });
                        }
                    });
                } else {
                    // Reset semua field
                    $('#id_gudang_asal').html('<option value="">-- Pilih Gudang --</option>');
                    $('#id_gudang_tujuan').html('<option value="">-- Pilih Gudang Tujuan --</option>');
                    $('#id_pelanggan').html('<option value="">-- Pilih Pelanggan --</option>');
                    $('.select-barang').html('<option value="">-- Pilih Barang --</option>');
                }
            });

            // Trigger change jika ada nilai awal
            if ($('#id_perusahaan').val()) {
                $('#id_perusahaan').trigger('change');
            }
        <?php endif; ?>

        // Toggle tipe tujuan
        $('#tipe_tujuan').change(function () {
            var tipe = $(this).val();

            // Sembunyikan semua field
            $('#gudang_tujuan_field').hide();
            $('#pelanggan_field').hide();
            $('#konsumen_field').hide();

            // Hapus required semua
            $('#id_gudang_tujuan').removeAttr('required');
            $('#id_pelanggan').removeAttr('required');
            $('#nama_konsumen').removeAttr('required');
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
                        <?php echo $this->security->get_csrf_token_name(); ?>: csrf_token,
                        id_pelanggan: id_pelanggan
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

        // Get stok barang
        $(document).on('change', '.select-barang', function () {
            var id_gudang = $('#id_gudang_asal').val();
            var id_barang = $(this).val();
            var row = $(this).closest('tr');

            if (id_gudang && id_barang) {
                $.ajax({
                    url: '<?php echo site_url('aktifitas/pemindahan/get_stok_barang'); ?>',
                    method: 'POST',
                    data: {
                        <?php echo $this->security->get_csrf_token_name(); ?>: csrf_token,
                        id_gudang: id_gudang,
                        id_barang: id_barang
                    },
                    dataType: 'json',
                    success: function (response) {
                        row.find('.stok-tersedia').val(response.tersedia);
                    }
                });
            } else {
                row.find('.stok-tersedia').val('');
            }
        });

        // Tambah barang
        $('#btn-tambah-barang').click(function () {
            var no = $('#table_barang tbody tr').length + 1;
            var barangOptions = $('.select-barang:first').html();

            if (!barangOptions) {
                barangOptions = '<option value="">-- Pilih Barang --</option>';
                <?php foreach ($barang as $row): ?>
                    barangOptions += '<option value="<?php echo $row->id_barang; ?>"><?php echo $row->nama_barang; ?> (<?php echo $row->sku; ?>)</option>';
                <?php endforeach; ?>
            }

            var html = '<tr>' +
                '<td>' + no + '</td>' +
                '<td><select class="form-control select-barang" name="id_barang[]" required>' + barangOptions + '</select></td>' +
                '<td><input type="text" class="form-control stok-tersedia" readonly></td>' +
                '<td><input type="number" class="form-control" name="jumlah[]" min="1" required></td>' +
                '<td><button type="button" class="btn btn-sm btn-danger btn-hapus-barang"><i class="fas fa-trash"></i></button></td>' +
                '</tr>';

            $('#table_barang tbody').append(html);

            // Trigger change event untuk select-barang yang baru
            $('#table_barang tbody tr:last .select-barang').trigger('change');
        });

        // Hapus barang
        $(document).on('click', '.btn-hapus-barang', function () {
            if ($('#table_barang tbody tr').length > 1) {
                $(this).closest('tr').remove();

                // Renumber rows
                $('#table_barang tbody tr').each(function (index) {
                    $(this).find('td:first').text(index + 1);
                });
            } else {
                alert('Minimal harus ada 1 barang!');
            }
        });

        // Modal tambah alamat
        $('#btn-tambah-alamat').click(function () {
            $('#modalTambahAlamat').modal('show');
        });

        $('#btn-simpan-alamat').click(function () {
            var alamat_lengkap = $('#alamat_lengkap').val();
            var keterangan = $('#keterangan_alamat').val();

            if (!alamat_lengkap) {
                alert('Alamat lengkap harus diisi!');
                return;
            }

            $.ajax({
                url: '<?php echo site_url('aktifitas/pemindahan/simpan_alamat'); ?>',
                method: 'POST',
                data: {
                    <?php echo $this->security->get_csrf_token_name(); ?>: csrf_token,
                    alamat_lengkap: alamat_lengkap,
                    keterangan_alamat: keterangan
                },
                dataType: 'json',
                success: function (response) {
                    if (response.status == 'success') {
                        // Tambahkan opsi alamat baru
                        var newOption = '<option value="' + response.id_alamat + '" selected>' + alamat_lengkap + '</option>';
                        $('#id_alamat_konsumen').append(newOption);

                        // Reset form
                        $('#form-tambah-alamat')[0].reset();

                        // Tutup modal
                        $('#modalTambahAlamat').modal('hide');

                        // Tampilkan pesan sukses
                        alert('Alamat berhasil ditambahkan!');
                    } else {
                        alert('Gagal menambahkan alamat: ' + response.message);
                    }
                },
                error: function () {
                    alert('Terjadi kesalahan saat menambahkan alamat!');
                }
            });
        });

        // Trigger change on page load for existing rows
        $('.select-barang').trigger('change');

        // Trigger tipe tujuan change on page load
        $('#tipe_tujuan').trigger('change');

        // Trigger pelanggan change on page load if exists
        if ($('#id_pelanggan').val()) {
            $('#id_pelanggan').trigger('change');
        }
    });
</script>