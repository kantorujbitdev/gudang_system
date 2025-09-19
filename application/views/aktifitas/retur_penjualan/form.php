<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary"><?php echo $title; ?></h6>
    </div>
    <div class="card-body">
        <?php echo form_open(current_url()); ?>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="id_pembelian">Pembelian <span class="text-danger">*</span></label>
                    <select class="form-control" id="id_pembelian" name="id_pembelian">
                        <option value="">-- Pilih Pembelian --</option>
                        <?php foreach ($pembelian as $row): ?>
                            <option value="<?php echo $row->id_pembelian; ?>"><?php echo $row->no_pembelian; ?> -
                                <?php echo $row->nama_supplier; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php echo form_error('id_pembelian', '<small class="text-danger">', '</small>'); ?>
                </div>

                <div class="form-group">
                    <label for="id_supplier">Supplier <span class="text-danger">*</span></label>
                    <select class="form-control" id="id_supplier" name="id_supplier" required>
                        <option value="">-- Pilih Supplier --</option>
                        <?php foreach ($supplier as $row): ?>
                            <option value="<?php echo $row->id_supplier; ?>"><?php echo $row->nama_supplier; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php echo form_error('id_supplier', '<small class="text-danger">', '</small>'); ?>
                </div>

                <div class="form-group">
                    <label for="tanggal_retur">Tanggal Retur <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="tanggal_retur" name="tanggal_retur"
                        value="<?php echo set_value('tanggal_retur', date('Y-m-d')); ?>" required>
                    <?php echo form_error('tanggal_retur', '<small class="text-danger">', '</small>'); ?>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="alasan_retur">Alasan Retur <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="alasan_retur" name="alasan_retur" rows="3"
                        required><?php echo set_value('alasan_retur'); ?></textarea>
                    <?php echo form_error('alasan_retur', '<small class="text-danger">', '</small>'); ?>
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
                        <th width="15%">Gudang</th>
                        <th width="15%">Jumlah Retur</th>
                        <th width="20%">Alasan</th>
                        <th width="5%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
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
                            <select class="form-control select-gudang" name="id_gudang[]" required>
                                <option value="">-- Pilih Gudang --</option>
                                <?php foreach ($gudang as $row): ?>
                                    <option value="<?php echo $row->id_gudang; ?>"><?php echo $row->nama_gudang; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td>
                            <input type="number" class="form-control" name="jumlah_retur[]" min="1" required>
                        </td>
                        <td>
                            <input type="text" class="form-control" name="alasan_barang[]">
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-danger btn-hapus-barang"><i
                                    class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="form-group">
            <button type="button" class="btn btn-secondary" id="btn-tambah-barang"><i class="fas fa-plus"></i> Tambah
                Barang</button>
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="<?php echo site_url('aktifitas/retur_pembelian'); ?>" class="btn btn-secondary">Batal</a>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>

<script>
    $(document).ready(function () {
        // Tambah barang
        $('#btn-tambah-barang').click(function () {
            var no = $('#table_barang tbody tr').length + 1;
            var html = '<tr>' +
                '<td>' + no + '</td>' +
                '<td><select class="form-control select-barang" name="id_barang[]" required><option value="">-- Pilih Barang --</option><?php foreach ($barang as $row): ?><option value="<?php echo $row->id_barang; ?>"><?php echo $row->nama_barang; ?> (<?php echo $row->sku; ?>)</option><?php endforeach; ?></select></td>' +
                '<td><select class="form-control select-gudang" name="id_gudang[]" required><option value="">-- Pilih Gudang --</option><?php foreach ($gudang as $row): ?><option value="<?php echo $row->id_gudang; ?>"><?php echo $row->nama_gudang; ?></option><?php endforeach; ?></select></td>' +
                '<td><input type="number" class="form-control" name="jumlah_retur[]" min="1" required></td>' +
                '<td><input type="text" class="form-control" name="alasan_barang[]"></td>' +
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
    });
</script>