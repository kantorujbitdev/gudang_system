<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Custom file input label
        $('.custom-file-input').on('change', function () {
            var fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').html(fileName);
        });

        // Function to load kategori
        function loadKategori(id_perusahaan, selected_kategori = '') {
            // Get CSRF token
            var csrf_token = $('input[name="<?php echo $this->security->get_csrf_token_name(); ?>"]').val();

            $.ajax({
                url: "<?php echo site_url('setup/barang/get_kategori_by_perusahaan'); ?>",
                method: "POST",
                data: {
                    id_perusahaan: id_perusahaan,
                    <?php echo $this->security->get_csrf_token_name(); ?>: csrf_token
                },
                dataType: "json",
                success: function (response) {
                    $('#id_kategori').empty();

                    if (response.length > 0) {
                        $('#id_kategori').append('<option value="">-- Pilih Kategori --</option>');
                        $.each(response, function (index, item) {
                            var selected = (item.id_kategori == selected_kategori) ? 'selected' : '';
                            $('#id_kategori').append('<option value="' + item.id_kategori + '" ' + selected + '>' + item.nama_kategori + '</option>');
                        });
                    } else {
                        $('#id_kategori').append('<option value="">-- Tidak ada kategori --</option>');
                    }
                },
                error: function (xhr, status, error) {
                    console.log(xhr.responseText);
                    $('#id_kategori').empty();
                    $('#id_kategori').append('<option value="">-- Error loading kategori --</option>');
                }
            });
        }

        // Load kategori when perusahaan changes
        $('#id_perusahaan').change(function () {
            var id_perusahaan = $(this).val();
            var selected_kategori = '';
            loadKategori(id_perusahaan, selected_kategori);
        });

        // Initial load if in edit mode or for Super Admin
        <?php if (isset($barang) && $this->session->userdata('id_role') == 1): ?>
            var initial_id_perusahaan = $('#id_perusahaan').val();
            var initial_selected_kategori = '<?php echo isset($barang) ? $barang->id_kategori : ''; ?>';
            if (initial_id_perusahaan) {
                loadKategori(initial_id_perusahaan, initial_selected_kategori);
            }
        <?php elseif ($this->session->userdata('id_role') == 1 && isset($selected_perusahaan)): ?>
            var initial_id_perusahaan = '<?php echo $selected_perusahaan; ?>';
            var initial_selected_kategori = '';
            if (initial_id_perusahaan) {
                loadKategori(initial_id_perusahaan, initial_selected_kategori);
            }
        <?php endif; ?>
    });


    $(document).ready(function () {
        // Tambah stok
        $('#submitTambahStok').click(function () {
            var form = $('#tambahStokForm');
            if (form[0].checkValidity()) {
                $.ajax({
                    url: "<?php echo site_url('setup/barang/tambah_stok'); ?>",
                    type: "POST",
                    data: form.serialize(),
                    dataType: "json",
                    success: function (response) {
                        if (response.status === 'success') {
                            $('#tambahStokModal').modal('hide');
                            alert(response.message);
                            location.reload();
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function () {
                        alert('Terjadi kesalahan saat memproses data');
                    }
                });
            } else {
                form[0].reportValidity();
            }
        });

        // Edit stok
        $('.edit-stok').click(function () {
            var id_barang = $(this).data('id_barang');
            var id_gudang = $(this).data('id_gudang');
            var jumlah = $(this).data('jumlah');
            var nama_gudang = $(this).data('nama_gudang');

            $('#edit_id_barang').val(id_barang);
            $('#edit_id_gudang').val(id_gudang);
            $('#edit_nama_gudang').val(nama_gudang);
            $('#edit_jumlah_sekarang').val(jumlah);
            $('#edit_jumlah').val('');

            $('#editStokModal').modal('show');
        });

        $('#submitEditStok').click(function () {
            var form = $('#editStokForm');
            var jumlah = parseInt($('#edit_jumlah').val());
            var id_barang = $('#edit_id_barang').val();
            var id_gudang = $('#edit_id_gudang').val();

            if (form[0].checkValidity()) {
                if (jumlah === 0) {
                    alert('Jumlah tidak boleh nol');
                    return;
                }

                var url = jumlah > 0 ?
                    "<?php echo site_url('setup/barang/tambah_stok'); ?>" :
                    "<?php echo site_url('setup/barang/kurangi_stok'); ?>";

                $.ajax({
                    url: url,
                    type: "POST",
                    data: {
                        id_barang: id_barang,
                        id_gudang: id_gudang,
                        jumlah: Math.abs(jumlah)
                    },
                    dataType: "json",
                    success: function (response) {
                        if (response.status === 'success') {
                            $('#editStokModal').modal('hide');
                            alert(response.message);
                            location.reload();
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function () {
                        alert('Terjadi kesalahan saat memproses data');
                    }
                });
            } else {
                form[0].reportValidity();
            }
        });
    });
</script>