<script>
    $(document).ready(function () {
        // Handle tombol tambah stok
        $('.btn-tambah-stok').click(function () {
            var id_barang = $(this).data('id_barang');
            var nama_barang = $(this).data('nama_barang');
            var id_gudang = $(this).data('id_gudang');
            var nama_gudang = $(this).data('nama_gudang');

            $('#modal_id_barang').val(id_barang);
            $('#modal_nama_barang').val(nama_barang);
            $('#modal_id_gudang').val(id_gudang);
            $('#modal_nama_gudang').val(nama_gudang);
            $('#modal_qty_awal').val('');
            $('#modal_keterangan').val('');

            // Reset validation state
            $('#formTambahStok').removeClass('was-validated');

            $('#modalTambahStok').modal('show');
        });

        // Handle simpan stok
        $('#btnSimpanStok').click(function () {
            var form = $('#formTambahStok');

            if (form[0].checkValidity() === false) {
                event.preventDefault();
                event.stopPropagation();
                form.addClass('was-validated');
                return;
            }

            // Disable tombol untuk mencegah double click
            $('#btnSimpanStok').prop('disabled', true);

            $.ajax({
                url: '<?php echo site_url('pengaturan/stok_awal/ajax_tambah_stok'); ?>',
                type: 'POST',
                data: form.serialize(),
                dataType: 'json',
                success: function (response) {
                    if (response.status === 'success') {
                        // Tutup modal dan reload halaman untuk menampilkan flashdata
                        $('#modalTambahStok').modal('hide');
                        window.location.reload();
                    } else {
                        // Tampilkan pesan error
                        alert(response.message);
                        $('#btnSimpanStok').prop('disabled', false);
                    }
                },
                error: function (xhr, status, error) {
                    alert('Terjadi kesalahan saat menyimpan data: ' + error);
                    $('#btnSimpanStok').prop('disabled', false);
                }
            });
        });

        // Reset modal ketika ditutup
        $('#modalTambahStok').on('hidden.bs.modal', function () {
            $('#formTambahStok')[0].reset();
            $('#formTambahStok').removeClass('was-validated');
            $('#btnSimpanStok').prop('disabled', false);
        });
    });

    <?php if (isset($perusahaan)): ?>
        function changePerusahaan() {
            var id_perusahaan = $('#select_perusahaan').val();
            window.location.href = '<?php echo site_url('pengaturan/stok_awal'); ?>?id_perusahaan=' + id_perusahaan;
        }
    <?php endif; ?>
</script>