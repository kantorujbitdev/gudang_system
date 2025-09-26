<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function () {
        // Initialize Select2
        $('.select2').select2({
            theme: 'bootstrap4',
            placeholder: 'Pilih opsi'
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
                                    '<input type="hidden" name="id_gudang[]" value="' + item.id_gudang + '">' +
                                    '<input type="text" class="form-control" value="' + item.nama_barang + ' (' + item.sku + ')" readonly>' +
                                    '</td>' +
                                    '<td>' +
                                    '<input type="text" class="form-control" value="' + item.nama_gudang + '" readonly>' +
                                    '</td>' +
                                    '<td>' +
                                    '<input type="text" class="form-control" value="' + item.jumlah + ' ' + item.satuan + '" readonly>' +
                                    '</td>' +
                                    '<td><input type="number" class="form-control" name="jumlah_retur[]" min="1" max="' + item.jumlah + '" required placeholder="Jumlah retur"></td>' +
                                    '<td><input type="text" class="form-control" name="alasan_barang[]" placeholder="Alasan retur barang"></td>' +
                                    '</tr>';

                                $('#table_barang tbody').append(html);
                            });
                        } else {
                            var html = '<tr>' +
                                '<td colspan="6" class="text-center">' +
                                '<em>Tidak ada barang dalam pemindahan ini</em>' +
                                '</td>' +
                                '</tr>';

                            $('#table_barang tbody').append(html);
                        }
                    }
                });
            } else {
                // Clear table
                $('#table_barang tbody').empty();

                var html = '<tr>' +
                    '<td colspan="6" class="text-center">' +
                    '<em>Silakan pilih pemindahan barang terlebih dahulu</em>' +
                    '</td>' +
                    '</tr>';

                $('#table_barang tbody').append(html);
            }
        });

        // Form validation before submit
        $('form').on('submit', function (e) {
            var isValid = true;

            // Check if pemindahan is selected
            if (!$('#id_pemindahan').val()) {
                e.preventDefault();
                alert('Harap pilih pemindahan barang terlebih dahulu!');
                return false;
            }

            // Check if at least one item is filled
            var hasItem = false;
            $('input[name="jumlah_retur[]"]').each(function () {
                if ($(this).val() > 0) {
                    hasItem = true;
                    return false;
                }
            });

            if (!hasItem) {
                e.preventDefault();
                alert('Harap isi minimal 1 barang untuk diretur!');
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