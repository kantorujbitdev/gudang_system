<!-- Tambahkan di bagian head -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
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

        // Suggestion functionality
        function setupSuggestion(inputId, suggestionId, url) {
            var input = $('#' + inputId);
            var suggestion = $('#' + suggestionId);
            var timeout;

            input.on('input', function () {
                var query = $(this).val();

                clearTimeout(timeout);

                if (query.length < 1) {
                    suggestion.hide();
                    return;
                }

                timeout = setTimeout(function () {
                    $.ajax({
                        url: url,
                        type: 'GET',
                        data: { q: query },
                        dataType: 'json',
                        success: function (data) {
                            suggestion.empty();

                            if (data.length > 0) {
                                $.each(data, function (i, item) {
                                    suggestion.append('<div class="suggestion-item" data-value="' + item.text + '">' + item.text + '</div>');
                                });
                                suggestion.show();
                            } else {
                                suggestion.hide();
                            }
                        },
                        error: function () {
                            suggestion.hide();
                        }
                    });
                }, 300);
            });

            input.on('keydown', function (e) {
                var items = suggestion.find('.suggestion-item');
                var active = suggestion.find('.suggestion-item.active');

                if (e.keyCode === 40) { // Down arrow
                    e.preventDefault();
                    if (active.length === 0) {
                        items.first().addClass('active');
                    } else {
                        active.removeClass('active');
                        active.next().addClass('active');
                    }
                } else if (e.keyCode === 38) { // Up arrow
                    e.preventDefault();
                    if (active.length === 0) {
                        items.last().addClass('active');
                    } else {
                        active.removeClass('active');
                        active.prev().addClass('active');
                    }
                } else if (e.keyCode === 13) { // Enter
                    e.preventDefault();
                    if (active.length > 0) {
                        input.val(active.data('value'));
                        suggestion.hide();
                    }
                } else if (e.keyCode === 27) { // Escape
                    suggestion.hide();
                }
            });

            suggestion.on('click', '.suggestion-item', function () {
                input.val($(this).data('value'));
                suggestion.hide();
            });

            $(document).on('click', function (e) {
                if (!$(e.target).closest('#' + inputId).length && !$(e.target).closest(suggestion).length) {
                    suggestion.hide();
                }
            });
        }

        // Setup suggestions for each field
        setupSuggestion('ukuran', 'ukuran-suggestions', '<?php echo site_url('setup/barang/get_ukuran'); ?>');
        setupSuggestion('motor', 'motor-suggestions', '<?php echo site_url('setup/barang/get_motor'); ?>');
        setupSuggestion('warna', 'warna-suggestions', '<?php echo site_url('setup/barang/get_warna'); ?>');
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
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message,
                                confirmButtonColor: '#3085d6',
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: response.message,
                                confirmButtonColor: '#3085d6',
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    error: function () {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Terjadi kesalahan saat memproses data',
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'OK'
                        });
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
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan',
                        text: 'Jumlah tidak boleh nol',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    });
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
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message,
                                confirmButtonColor: '#3085d6',
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: response.message,
                                confirmButtonColor: '#3085d6',
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    error: function () {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Terjadi kesalahan saat memproses data',
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            } else {
                form[0].reportValidity();
            }
        });
    });
</script>