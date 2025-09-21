<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary"><?php echo $title; ?></h6>
    </div>
    <div class="card-body">
        <?php echo form_open(current_url()); ?>
        <div class="row">
            <?php if ($this->session->userdata('id_role') == 1): // Super Admin ?>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="id_perusahaan">Perusahaan <span class="text-danger">*</span></label>
                        <select class="form-control" id="id_perusahaan" name="id_perusahaan" required>
                            <option value="">-- Pilih Perusahaan --</option>
                            <?php foreach ($perusahaan as $row): ?>
                                <option value="<?php echo $row->id_perusahaan; ?>" <?php echo (isset($pemindahan) && $pemindahan->id_perusahaan == $row->id_perusahaan) ? 'selected' : ''; ?>>
                                    <?php echo $row->nama_perusahaan; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php echo form_error('id_perusahaan', '<small class="text-danger">', '</small>'); ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="tanggal_pemindahan">Tanggal Pemindahan</label>
                    <input type="text" class="form-control" id="tanggal_pemindahan" name="tanggal_pemindahan"
                        value="<?php echo date('d-m-Y H:i:s'); ?>" readonly>
                    <small class="text-muted">Waktu real, tidak dapat diubah</small>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="id_gudang_asal">Gudang Asal <span class="text-danger">*</span></label>
                    <select class="form-control" id="id_gudang_asal" name="id_gudang_asal" required>
                        <option value="">-- Pilih Gudang --</option>
                        <?php foreach ($gudang as $row): ?>
                            <option value="<?php echo $row->id_gudang; ?>" <?php echo (isset($pemindahan) && $pemindahan->id_gudang_asal == $row->id_gudang) ? 'selected' : ''; ?>>
                                <?php echo $row->nama_gudang; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php echo form_error('id_gudang_asal', '<small class="text-danger">', '</small>'); ?>
                </div>

                <div class="form-group">
                    <label for="tipe_tujuan">Tipe Tujuan <span class="text-danger">*</span></label>
                    <select class="form-control" id="tipe_tujuan" name="tipe_tujuan" required>
                        <option value="">-- Pilih Tipe --</option>
                        <option value="gudang" <?php echo (isset($pemindahan) && $pemindahan->tipe_tujuan == 'gudang') ? 'selected' : ''; ?>>Gudang</option>
                        <option value="pelanggan" <?php echo (isset($pemindahan) && $pemindahan->tipe_tujuan == 'pelanggan') ? 'selected' : ''; ?>>Pelanggan</option>
                        <option value="konsumen" <?php echo (isset($pemindahan) && $pemindahan->tipe_tujuan == 'konsumen') ? 'selected' : ''; ?>>Konsumen</option>
                    </select>
                    <?php echo form_error('tipe_tujuan', '<small class="text-danger">', '</small>'); ?>
                </div>

                <div class="form-group" id="gudang_tujuan_field"
                    style="display: <?php echo (isset($pemindahan) && $pemindahan->tipe_tujuan == 'gudang') ? 'block' : 'none'; ?>;">
                    <label for="id_gudang_tujuan">Gudang Tujuan</label>
                    <select class="form-control" id="id_gudang_tujuan" name="id_gudang_tujuan">
                        <option value="">-- Pilih Gudang --</option>
                        <?php foreach ($gudang as $row): ?>
                            <option value="<?php echo $row->id_gudang; ?>" <?php echo (isset($pemindahan) && $pemindahan->id_gudang_tujuan == $row->id_gudang) ? 'selected' : ''; ?>>
                                <?php echo $row->nama_gudang; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group" id="pelanggan_field"
                    style="display: <?php echo (isset($pemindahan) && $pemindahan->tipe_tujuan == 'pelanggan') ? 'block' : 'none'; ?>;">
                    <label for="id_pelanggan">Pelanggan</label>
                    <select class="form-control" id="id_pelanggan" name="id_pelanggan">
                        <option value="">-- Pilih Pelanggan --</option>
                        <?php foreach ($pelanggan as $row): ?>
                            <option value="<?php echo $row->id_pelanggan; ?>" <?php echo (isset($pemindahan) && $pemindahan->id_pelanggan == $row->id_pelanggan) ? 'selected' : ''; ?>>
                                <?php echo $row->nama_pelanggan; ?> (<?php echo $row->tipe_pelanggan; ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group" id="konsumen_field"
                    style="display: <?php echo (isset($pemindahan) && $pemindahan->tipe_tujuan == 'konsumen') ? 'block' : 'none'; ?>;">
                    <label for="id_alamat_konsumen">Alamat Pengiriman</label>
                    <select class="form-control" id="id_alamat_konsumen" name="id_alamat_konsumen">
                        <option value="">-- Pilih Alamat --</option>
                        <?php foreach ($alamat_konsumen as $row): ?>
                            <option value="<?php echo $row->id_alamat; ?>" <?php echo (isset($pemindahan) && $pemindahan->id_alamat_konsumen == $row->id_alamat) ? 'selected' : ''; ?>>
                                <?php echo $row->alamat_lengkap; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <a href="javascript:void(0)" id="btn-tambah-alamat" class="btn btn-sm btn-secondary mt-2">
                        <i class="fas fa-plus"></i> Tambah Alamat Baru
                    </a>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="keterangan">Keterangan</label>
                    <textarea class="form-control" id="keterangan" name="keterangan"
                        rows="3"><?php echo set_value('keterangan', isset($pemindahan) ? $pemindahan->keterangan : ''); ?></textarea>
                </div>
            </div>
        </div>

        <hr>

        <h6>Daftar Barang</h6>
        <div class="table-responsive mb-3">
            <table class="table table-bordered" id="table_barang">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="40%">Barang</th>
                        <th width="15%">Stok Tersedia</th>
                        <th width="15%">Jumlah</th>
                        <th width="10%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($detail)): ?>
                        <?php foreach ($detail as $key => $item): ?>
                            <tr>
                                <td><?php echo $key + 1; ?></td>
                                <td>
                                    <select class="form-control select-barang" name="id_barang[]" required>
                                        <option value="">-- Pilih Barang --</option>
                                        <?php foreach ($barang as $row): ?>
                                            <option value="<?php echo $row->id_barang; ?>" <?php echo ($item->id_barang == $row->id_barang) ? 'selected' : ''; ?>>
                                                <?php echo $row->nama_barang; ?> (<?php echo $row->sku; ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" class="form-control stok-tersedia" readonly>
                                </td>
                                <td>
                                    <input type="number" class="form-control" name="jumlah[]"
                                        value="<?php echo $item->jumlah; ?>" min="1" required>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-danger btn-hapus-barang"><i
                                            class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td>1</td>
                            <td>
                                <select class="form-control select-barang" name="id_barang[]" required>
                                    <option value="">-- Pilih Barang --</option>
                                    <?php foreach ($barang as $row): ?>
                                        <option value="<?php echo $row->id_barang; ?>"><?php echo $row->nama_barang; ?>
                                            (<?php echo $row->sku; ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td>
                                <input type="text" class="form-control stok-tersedia" readonly>
                            </td>
                            <td>
                                <input type="number" class="form-control" name="jumlah[]" min="1" required>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-danger btn-hapus-barang"><i
                                        class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="form-group">
            <button type="button" class="btn btn-secondary" id="btn-tambah-barang"><i class="fas fa-plus"></i> Tambah
                Barang</button>
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="<?php echo site_url('aktifitas/pemindahan'); ?>" class="btn btn-secondary">Batal</a>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>

<!-- Modal Tambah Alamat -->
<div class="modal fade" id="modalTambahAlamat" tabindex="-1" role="dialog" aria-labelledby="modalTambahAlamatLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTambahAlamatLabel">Tambah Alamat Baru</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form-tambah-alamat">
                    <div class="form-group">
                        <label for="alamat_lengkap">Alamat Lengkap <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="alamat_lengkap" name="alamat_lengkap" rows="3"
                            required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="keterangan_alamat">Keterangan</label>
                        <input type="text" class="form-control" id="keterangan_alamat" name="keterangan_alamat">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btn-simpan-alamat">Simpan</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        // Untuk Super Admin, ambil data berdasarkan perusahaan
        <?php if ($this->session->userdata('id_role') == 1): ?>
            $('#id_perusahaan').change(function () {
                var id_perusahaan = $(this).val();

                if (id_perusahaan) {
                    $.ajax({
                        url: '<?php echo site_url('aktifitas/pemindahan/get_data_by_perusahaan'); ?>',
                        method: 'POST',
                        data: {
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
                            var gudangTujuanHtml = '<option value="">-- Pilih Gudang --</option>';
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
                            var barangHtml = '';
                            $.each(response.barang, function (i, item) {
                                barangHtml += '<option value="' + item.id_barang + '">' + item.nama_barang + ' (' + item.sku + ')</option>';
                            });

                            // Update semua select barang di tabel
                            $('.select-barang').each(function () {
                                var currentValue = $(this).val();
                                $(this).html('<option value="">-- Pilih Barang --</option>' + barangHtml);
                                $(this).val(currentValue);
                            });
                        }
                    });
                } else {
                    // Reset semua field
                    $('#id_gudang_asal').html('<option value="">-- Pilih Gudang --</option>');
                    $('#id_gudang_tujuan').html('<option value="">-- Pilih Gudang --</option>');
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
            if (tipe == 'gudang') {
                $('#gudang_tujuan_field').show();
                $('#pelanggan_field').hide();
                $('#konsumen_field').hide();
                $('#id_gudang_tujuan').attr('required', true);
                $('#id_pelanggan').removeAttr('required');
                $('#id_alamat_konsumen').removeAttr('required');
            } else if (tipe == 'pelanggan') {
                $('#gudang_tujuan_field').hide();
                $('#pelanggan_field').show();
                $('#konsumen_field').hide();
                $('#id_gudang_tujuan').removeAttr('required');
                $('#id_pelanggan').attr('required', true);
                $('#id_alamat_konsumen').removeAttr('required');
            } else if (tipe == 'konsumen') {
                $('#gudang_tujuan_field').hide();
                $('#pelanggan_field').hide();
                $('#konsumen_field').show();
                $('#id_gudang_tujuan').removeAttr('required');
                $('#id_pelanggan').removeAttr('required');
                $('#id_alamat_konsumen').attr('required', true);
            } else {
                $('#gudang_tujuan_field').hide();
                $('#pelanggan_field').hide();
                $('#konsumen_field').hide();
                $('#id_gudang_tujuan').removeAttr('required');
                $('#id_pelanggan').removeAttr('required');
                $('#id_alamat_konsumen').removeAttr('required');
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
            var html = '<tr>' +
                '<td>' + no + '</td>' +
                '<td>' +
                '<select class="form-control select-barang" name="id_barang[]" required>' +
                '<option value="">-- Pilih Barang --</option>';

            // Ambil opsi barang dari baris pertama
            var firstRowOptions = $('#table_barang tbody tr:first .select-barang').html();
            if (firstRowOptions) {
                html += firstRowOptions;
            } else {
                // Jika tidak ada baris pertama, gunakan opsi default
                html += '<?php foreach ($barang as $row): ?>' +
                        '<option value="<?php echo $row->id_barang; ?>"><?php echo $row->nama_barang; ?> (<?php echo $row->sku; ?>)</option>' +
                        '<?php endforeach; ?>';
            }

            html += '</select>' +
                '</td>' +
                '<td><input type="text" class="form-control stok-tersedia" readonly></td>' +
                '<td><input type="number" class="form-control" name="jumlah[]" min="1" required></td>' +
                '<td><button type="button" class="btn btn-sm btn-danger btn-hapus-barang"><i class="fas fa-trash"></i></button></td>' +
                '</tr>';

            $('#table_barang tbody').append(html);
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
    });
</script>