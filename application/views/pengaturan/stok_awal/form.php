<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary"><?php echo $title; ?></h6>
    </div>
    <div class="card-body">
        <?php echo form_open(current_url()); ?>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="id_barang">Barang <span class="text-danger">*</span></label>
                    <select class="form-control select2" id="id_barang" name="id_barang" required>
                        <option value="">-- Pilih Barang --</option>
                        <?php foreach ($barang as $row): ?>
                            <option value="<?php echo $row->id_barang; ?>" <?php echo (isset($stok_awal) && $stok_awal->id_barang == $row->id_barang) ? 'selected' : ''; ?>>
                                <?php echo $row->nama_barang; ?> (<?php echo $row->sku; ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php echo form_error('id_barang', '<small class="text-danger">', '</small>'); ?>
                </div>

                <div class="form-group">
                    <label for="id_gudang">Gudang <span class="text-danger">*</span></label>
                    <select class="form-control select2" id="id_gudang" name="id_gudang" required>
                        <option value="">-- Pilih Gudang --</option>
                        <?php foreach ($gudang as $row): ?>
                            <option value="<?php echo $row->id_gudang; ?>" <?php echo (isset($stok_awal) && $stok_awal->id_gudang == $row->id_gudang) ? 'selected' : ''; ?>>
                                <?php echo $row->nama_gudang; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php echo form_error('id_gudang', '<small class="text-danger">', '</small>'); ?>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="qty_awal">Jumlah Stok <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="qty_awal" name="qty_awal"
                        value="<?php echo set_value('qty_awal', isset($stok_awal) ? $stok_awal->qty_awal : ''); ?>"
                        min="0" required>
                    <?php echo form_error('qty_awal', '<small class="text-danger">', '</small>'); ?>
                </div>

                <div class="form-group">
                    <label for="keterangan">Keterangan</label>
                    <textarea class="form-control" id="keterangan" name="keterangan"
                        rows="3"><?php echo set_value('keterangan', isset($stok_awal) ? $stok_awal->keterangan : ''); ?></textarea>
                </div>
            </div>
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Simpan
            </button>
            <a href="<?php echo site_url('pengaturan/stok_awal'); ?>" class="btn btn-secondary">
                <i class="fas fa-times"></i> Batal
            </a>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>

<!-- Load Select2 CSS & JS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function () {
        $('.select2').select2({
            theme: 'bootstrap4',
            placeholder: 'Pilih opsi'
        });
    });
</script>