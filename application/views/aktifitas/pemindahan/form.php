<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary"><?php echo $title; ?></h6>
    </div>
    <div class="card-body">
        <?php echo form_open(current_url()); ?>
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
                        <option value="gudang" <?php echo (isset($pemindahan) && $pemindahan->id_gudang_tujuan) ? 'selected' : ''; ?>>Gudang</option>
                        <option value="pelanggan" <?php echo (isset($pemindahan) && $pemindahan->id_pelanggan) ? 'selected' : ''; ?>>Pelanggan</option>
                    </select>
                    <?php echo form_error('tipe_tujuan', '<small class="text-danger">', '</small>'); ?>
                </div>

                <div class="form-group" id="gudang_tujuan_field"
                    style="display: <?php echo (isset($pemindahan) && $pemindahan->id_gudang_tujuan) ? 'block' : 'none'; ?>;">
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
                    style="display: <?php echo (isset($pemindahan) && $pemindahan->id_pelanggan) ? 'block' : 'none'; ?>;">
                    <label for="id_pelanggan">Pelanggan</label>
                    <select class="form-control" id="id_pelanggan" name="id_pelanggan">
                        <option value="">-- Pilih Pelanggan --</option>
                        <?php foreach ($pelanggan as $row): ?>
                            <option value="<?php echo $row->id_pelanggan; ?>" <?php echo (isset($pemindahan) && $pemindahan->id_pelanggan == $row->id_pelanggan) ? 'selected' : ''; ?>>
                                <?php echo $row->nama_pelanggan; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="tanggal">Tanggal <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="tanggal" name="tanggal"
                        value="<?php echo set_value('tanggal', isset($pemindahan) ? date('Y-m-d', strtotime($pemindahan->tanggal)) : date('Y-m-d')); ?>"
                        required>
                    <?php echo form_error('tanggal', '<small class="text-danger">', '</small>'); ?>
                </div>

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

<script>
    $(document).ready(function () {
        // Toggle tipe tujuan
        $('#tipe_tujuan').change(function () {
            var tipe = $(this).val();
            if (tipe == 'gudang') {
                $('#gudang_tujuan_field').show();
                $('#pelanggan_field').hide();
                $('#id_gudang_tujuan').attr('required', true);
                $('#id_pelanggan').removeAttr('required');
            } else if (tipe == 'pelanggan') {
                $('#gudang_tujuan_field').hide();
                $('#pelanggan_field').show();
                $('#id_gudang_tujuan').removeAttr('required');
                $('#id_pelanggan').attr('required', true);
            } else {
                $('#gudang_tujuan_field').hide();
                $('#pelanggan_field').hide();
                $('#id_gudang_tujuan').removeAttr('required');
                $('#id_pelanggan').removeAttr('required');
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
            <?php foreach ($barang as $row): ?>
                html += '<option value="<?php echo $row->id_barang; ?>"><?php echo $row->nama_barang; ?> (<?php echo $row->sku; ?>)</option>';
            <?php endforeach; ?>
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

        // Trigger change on page load for existing rows
        $('.select-barang').trigger('change');
    });
</script>