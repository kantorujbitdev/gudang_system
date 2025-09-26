<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function () {
        // Initialize Select2
        $('.select2').select2({
            theme: 'bootstrap4',
            placeholder: 'Pilih opsi'
        });

        // Initialize Select2 for barang with search
        $('.select-barang').select2({
            theme: 'bootstrap4',
            placeholder: '-- Pilih Barang --',
            allowClear: true
        });

        // Get detail pemindahan
        $('#id_pemindahan').change(function () {
            var id_pemindahan = $(this).val();

            if (id_pemindahan) {
                $.ajax({
                    url: '<?php echo site_url('aktifitas/retur_penjualan/get_detail_pemindahan'); ?>',
                    method: 'POST',
                    data: {
                        id_pemindahan: id_pemindahan
                    },
                    dataType: 'json',
                    success: function (response) {
                        // Clear table
                        $('#table_barang tbody').empty();

                        // Add rows for each item
                        if (response.length > 0) {
                            $.each(response, function (index, item) {
                                var no = index + 1;
                                var html = '<tr>' +
                                    '<td>' + no + '</td>' +
                                    '<td>' +
                                    '<input type="hidden" name="id_barang[]" value="' + item.id_barang + '">' +
                                    '<input type="text" class="form-control" value="' + item.nama_barang + ' (' + item.sku + ')" readonly>' +
                                    '</td>' +
                                    '<td>' +
                                    '<input type="hidden" name="id_gudang[]" value="' + item.id_gudang + '">' +
                                    '<input type="text" class="form-control" value="' + item.nama_gudang + '" readonly>' +
                                    '</td>' +
                                    '<td><input type="number" class="form-control" name="jumlah_retur[]" min="1" max="' + item.jumlah + '" required></td>' +
                                    '<td><input type="text" class="form-control" name="alasan_barang[]" placeholder="Alasan retur barang"></td>' +
                                    '<td class="text-center"><button type="button" class="btn btn-sm btn-danger btn-hapus-barang"><i class="fas fa-trash"></i></button></td>' +
                                    '</tr>';

                                $('#table_barang tbody').append(html);
                            });
                        } else {
                            var html = '<tr>' +
                                '<td>1</td>' +
                                '<td><input type="text" class="form-control" value="Tidak ada barang dalam pemindahan ini" readonly></td>' +
                                '<td><input type="text" class="form-control" readonly></td>' +
                                '<td><input type="number" class="form-control" readonly></td>' +
                                '<td><input type="text" class="form-control" readonly></td>' +
                                '<td class="text-center"><button type="button" class="btn btn-sm btn-danger btn-hapus-barang"><i class="fas fa-trash"></i></button></td>' +
                                '</tr>';

                            $('#table_barang tbody').append(html);
                        }
                    }
                });
            } else {
                // Clear table
                $('#table_barang tbody').empty();

                // Add empty row
                var html = '<tr>' +
                    '<td>1</td>' +
                    '<td><select class="form-control select-barang" name="id_barang[]" required><option value="">-- Pilih Barang --</option></select></td>' +
                    '<td><select class="form-control select-gudang" name="id_gudang[]" required><option value="">-- Pilih Gudang --</option><?php foreach ($gudang as $row): ?><option value="<?php echo $row->id_gudang; ?>"><?php echo $row->nama_gudang; ?></option><?php endforeach; ?></select></td>' +
                    '<td><input type="number" class="form-control" name="jumlah_retur[]" min="1" required></td>' +
                    '<td><input type="text" class="form-control" name="alasan_barang[]" placeholder="Alasan retur barang"></td>' +
                    '<td class="text-center"><button type="button" class="btn btn-sm btn-danger btn-hapus-barang"><i class="fas fa-trash"></i></button></td>' +
                    '</tr>';

                $('#table_barang tbody').append(html);

                // Initialize Select2 for new element
                $('.select-barang').select2({
                    theme: 'bootstrap4',
                    placeholder: '-- Pilih Barang --',
                    allowClear: true
                });
            }
        });

        // Tambah barang
        $('#btn-tambah-barang').click(function () {
            var no = $('#table_barang tbody tr').length + 1;
            var html = '<tr>' +
                '<td>' + no + '</td>' +
                '<td><select class="form-control select-barang" name="id_barang[]" required><option value="">-- Pilih Barang --</option><?php foreach ($barang as $row): ?><option value="<?php echo $row->id_barang; ?>"><?php echo $row->nama_barang; ?> (<?php echo $row->sku; ?>)</option><?php endforeach; ?></select></td>' +
                '<td><select class="form-control select-gudang" name="id_gudang[]" required><option value="">-- Pilih Gudang --</option><?php foreach ($gudang as $row): ?><option value="<?php echo $row->id_gudang; ?>"><?php echo $row->nama_gudang; ?></option><?php endforeach; ?></select></td>' +
                '<td><input type="number" class="form-control" name="jumlah_retur[]" min="1" required></td>' +
                '<td><input type="text" class="form-control" name="alasan_barang[]" placeholder="Alasan retur barang"></td>' +
                '<td class="text-center"><button type="button" class="btn btn-sm btn-danger btn-hapus-barang"><i class="fas fa-trash"></i></button></td>' +
                '</tr>';

            $('#table_barang tbody').append(html);

            // Initialize Select2 for new element
            $('.select-barang').select2({
                theme: 'bootstrap4',
                placeholder: '-- Pilih Barang --',
                allowClear: true
            });
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
                $('#alert-barang').removeClass('d-none');
                setTimeout(function () {
                    $('#alert-barang').addClass('d-none');
                }, 3000);
            }
        });

        // Form validation before submit
        $('.form-penerimaan').on('submit', function (e) {
            var isValid = true;

            // Check if at least one item is filled
            var hasItem = false;
            $('select[name="id_barang[]"]').each(function () {
                if ($(this).val()) {
                    hasItem = true;
                    return false;
                }
            });

            if (!hasItem) {
                e.preventDefault();
                $('#alert-barang').removeClass('d-none');
                setTimeout(function () {
                    $('#alert-barang').addClass('d-none');
                }, 3000);
                return false;
            }

            // Check all required fields
            $(this).find('input[required], select[required]').each(function () {
                if (!$(this).val()) {
                    isValid = false;
                    $(this).addClass('is-invalid');
                } else {
                    $(this).removeClass('is-invalid');
                }
            });

            if (!isValid) {
                e.preventDefault();
                alert('Harap lengkapi semua field yang wajib diisi!');
            }
        });

        // Remove invalid class on input change
        $('input, select').on('change', function () {
            if ($(this).val()) {
                $(this).removeClass('is-invalid');
            }
        });
    });
</script>