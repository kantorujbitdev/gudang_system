<script>
    $(document).ready(function () {
        // Get CSRF token and name from the form
        var csrf_token = $('input[name="<?php echo $this->security->get_csrf_token_name(); ?>"]').val();
        var csrf_name = '<?php echo $this->security->get_csrf_token_name(); ?>';

        // Function to update CSRF token in all forms
        function updateCSRFToken(new_token) {
            csrf_token = new_token;
            $('input[name="' + csrf_name + '"]').val(new_token);
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

                            // Update all select barang in table
                            $('.select-barang').each(function () {
                                var currentValue = $(this).val();
                                $(this).html(barangHtml);
                                $(this).val(currentValue);
                            });

                            // Update CSRF token
                            if (response.csrf_token) {
                                updateCSRFToken(response.csrf_token);
                            }
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
                    $('.select-barang').html('<option value="">-- Pilih Barang --</option>');
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
                row.find('.stok-tersedia').val('Loading...');

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
                        checkDuplicateItems();
                    },
                    error: function () {
                        row.find('.stok-tersedia').val('Error');
                    }
                });
            } else {
                row.find('.stok-tersedia').val('');
            }
        });

        // Function to check duplicate items
        function checkDuplicateItems() {
            var selectedItems = [];
            $('.select-barang').each(function () {
                var val = $(this).val();
                if (val) {
                    selectedItems.push(val);
                }
            });

            $('.select-barang').each(function () {
                var currentValue = $(this).val();
                $(this).find('option').each(function () {
                    var optionValue = $(this).val();
                    if (optionValue && optionValue != currentValue) {
                        if (selectedItems.indexOf(optionValue) > -1) {
                            $(this).attr('disabled', true);
                        } else {
                            $(this).removeAttr('disabled');
                        }
                    } else {
                        $(this).removeAttr('disabled');
                    }
                });
            });
        }

        // Add item
        $('#btn-tambah-barang').click(function () {
            var no = $('#table_barang tbody tr').length + 1;

            // Get item options from first row or initial data
            var itemOptions = '';
            if ($('.select-barang').length > 0) {
                itemOptions = $('.select-barang:first').html();
            } else {
                itemOptions = '<option value="">-- Pilih Barang --</option>';
                <?php foreach ($barang as $row): ?>
                    itemOptions += '<option value="<?php echo $row->id_barang; ?>"><?php echo $row->nama_barang; ?> (<?php echo $row->sku; ?>)</option>';
                <?php endforeach; ?>
            }

            var html = '<tr>' +
                '<td>' + no + '</td>' +
                '<td><select class="form-control select-barang" name="id_barang[]" required>' + itemOptions + '</select></td>' +
                '<td><input type="text" class="form-control stok-tersedia" readonly></td>' +
                '<td><input type="number" class="form-control" name="jumlah[]" min="1" required></td>' +
                '<td><button type="button" class="btn btn-sm btn-danger btn-hapus-barang"><i class="fas fa-trash"></i></button></td>' +
                '</tr>';

            $('#table_barang tbody').append(html);

            // Trigger change event for new select-barang
            var newRow = $('#table_barang tbody tr:last');
            newRow.find('.select-barang').trigger('change');

            // Check duplicate items
            checkDuplicateItems();
        });

        // Delete item
        $(document).on('click', '.btn-hapus-barang', function () {
            if ($('#table_barang tbody tr').length > 1) {
                $(this).closest('tr').remove();

                // Renumber rows
                $('#table_barang tbody tr').each(function (index) {
                    $(this).find('td:first').text(index + 1);
                });

                // Check duplicate items
                checkDuplicateItems();
            } else {
                alert('Minimal harus ada 1 barang!');
            }
        });

        // Form validation before submit
        $('#form-pemindahan').submit(function (e) {
            var valid = true;

            // Check for duplicate items
            var selectedItems = [];
            $('.select-barang').each(function () {
                var val = $(this).val();
                if (val) {
                    if (selectedItems.indexOf(val) > -1) {
                        valid = false;
                    }
                    selectedItems.push(val);
                }
            });

            if (!valid) {
                e.preventDefault();
                alert('Tidak boleh ada barang yang duplikat dalam satu transaksi!');
                return false;
            }

            // Check stock for each item
            $('.select-barang').each(function () {
                var row = $(this).closest('tr');
                var id_barang = $(this).val();
                var jumlah = parseInt(row.find('input[name="jumlah[]"]').val());
                var stokTersedia = parseInt(row.find('.stok-tersedia').val());

                if (id_barang && jumlah > stokTersedia) {
                    valid = false;
                }
            });

            if (!valid) {
                e.preventDefault();
                alert('Jumlah barang melebihi stok yang tersedia!');
                return false;
            }
        });

        // Trigger change on page load for existing rows
        $('.select-barang').each(function () {
            var row = $(this).closest('tr');
            var id_gudang = $('#id_gudang_asal').val();
            var id_barang = $(this).val();

            if (id_gudang && id_barang) {
                // Load stock for existing rows
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
            }
        });

        // Trigger tipe tujuan change on page load
        $('#tipe_tujuan').trigger('change');

        // Trigger pelanggan change on page load if exists
        if ($('#id_pelanggan').val()) {
            $('#id_pelanggan').trigger('change');
        }
    });
</script>