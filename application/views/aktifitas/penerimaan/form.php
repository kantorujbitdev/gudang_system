<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary"><?php echo $title; ?></h6>
    </div>
    <div class="card-body">
        <?php echo form_open(current_url()); ?>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="id_supplier">Supplier <span class="text-danger">*</span></label>
                    <select class="form-control" id="id_supplier" name="id_supplier" required>
                        <option value="">-- Pilih Supplier --</option>
                        <?php foreach ($supplier as $row): ?>
                            <option value="<?php echo $row->id_supplier; ?>" <?php echo (isset($penerimaan) && $penerimaan->id_supplier == $row->id_supplier) ? 'selected' : ''; ?>>
                                <?php echo $row->nama_supplier; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php echo form_error('id_supplier', '<small class="text-danger">', '</small>'); ?>
                </div>

                <div class="form-group">
                    <label for="id_gudang">Gudang <span class="text-danger">*</span></label>
                    <select class="form-control" id="id_gudang" name="id_gudang" required>
                        <option value="">-- Pilih Gudang --</option>
                        <?php foreach ($gudang as $row): ?>
                            <option value="<?php echo $row->id_gudang; ?>" <?php echo (isset($penerimaan) && $penerimaan->id_gudang == $row->id_gudang) ? 'selected' : ''; ?>>
                                <?php echo $row->nama_gudang; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php echo form_error('id_gudang', '<small class="text-danger">', '</small>'); ?>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="tanggal_penerimaan">Tanggal Penerimaan <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="tanggal_penerimaan" name="tanggal_penerimaan"
                        value="<?php echo set_value('tanggal_penerimaan', isset($penerimaan) ? date('Y-m-d', strtotime($penerimaan->tanggal_penerimaan)) : date('Y-m-d')); ?>"
                        required>
                    <?php echo form_error('tanggal_penerimaan', '<small class="text-danger">', '</small>'); ?>
                </div>

                <div class="form-group">
                    <label for="keterangan">Keterangan</label>
                    <textarea class="form-control" id="keterangan" name="keterangan"
                        rows="3"><?php echo set_value('keterangan', isset($penerimaan) ? $penerimaan->keterangan : ''); ?></textarea>
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
                        <th width="35%">Barang</th>
                        <th width="15%">Jumlah Dipesan</th>
                        <th width="15%">Jumlah Diterima</th>
                        <th width="20%">Keterangan</th>
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
                                    <input type="number" class="form-control" name="jumlah_dipesan[]"
                                        value="<?php echo $item->jumlah_dipesan; ?>" min="0">
                                </td>
                                <td>
                                    <input type="number" class="form-control" name="jumlah_diterima[]"
                                        value="<?php echo $item->jumlah_diterima; ?>" min="1" required>
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="keterangan_barang[]"
                                        value="<?php echo $item->keterangan; ?>">
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
                                <input type="number" class="form-control" name="jumlah_dipesan[]" min="0">
                            </td>
                            <td>
                                <input type="number" class="form-control" name="jumlah_diterima[]" min="1" required>
                            </td>
                            <td>
                                <input type="text" class="form-control" name="keterangan_barang[]">
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
            <a href="<?php echo site_url('aktifitas/penerimaan'); ?>" class="btn btn-secondary">Batal</a>
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
                '<td>' +
                '<select class="form-control select-barang" name="id_barang[]" required>' +
                '<option value="">-- Pilih Barang --</option>';
            <?php foreach ($barang as $row): ?>
                html += '<option value="<?php echo $row->id_barang; ?>"><?php echo $row->nama_barang; ?> (<?php echo $row->sku; ?>)</option>';
            <?php endforeach; ?>
            html += '</select>' +
                '</td>' +
                '<td><input type="number" class="form-control" name="jumlah_dipesan[]" min="0"></td>' +
                '<td><input type="number" class="form-control" name="jumlah_diterima[]" min="1" required></td>' +
                '<td><input type="text" class="form-control" name="keterangan_barang[]"></td>' +
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