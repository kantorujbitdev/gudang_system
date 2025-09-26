<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function () {
        // Initialize Select2
        $('.select2').select2({
            theme: 'bootstrap4',
            placeholder: 'Pilih opsi'
        });

        // Tambah barang
        $('#btn-tambah-barang').click(function () {
            var no = $('#table_barang tbody tr').length + 1;
            var html = '<tr>' +
                '<td>' + no + '</td>' +
                '<td>' +
                '<select class="form-control select-barang" name="id_barang[]" required>' +
                '<option value="">-- Pilih Barang --</option>';
            <?php foreach ($barang as $row): ?>
                html += '<option value="<?php echo $row->id_barang; ?>"><?php echo $row->nama_barang; ?> (<?php echo $row->sku; ?>)</option>';
            <?php endforeach; ?>
            html += '</select>' +
                '</td>' +
                '<td><input type="number" class="form-control" name="jumlah_dipesan[]" min="0" placeholder="0"></td>' +
                '<td><input type="number" class="form-control jumlah-diterima" name="jumlah_diterima[]" min="1" required></td>' +
                '<td><input type="text" class="form-control" name="keterangan_barang[]" placeholder="Keterangan barang"></td>' +
                '<td class="text-center"><button type="button" class="btn btn-sm btn-danger btn-hapus-barang"><i class="fas fa-trash"></i></button></td>' +
                '</tr>';

            $('#table_barang tbody').append(html);

            // Initialize Select2 for new element
            $('.select-barang').select2({
                theme: 'bootstrap4'
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
            $('input[name="id_barang[]"]').each(function () {
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