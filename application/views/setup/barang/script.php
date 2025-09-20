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
</script>