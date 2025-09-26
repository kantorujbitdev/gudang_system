<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary"><?php echo $title; ?></h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="id_pemindahan">Pemindahan Barang <span class="text-danger">*</span></label>
                    <select class="form-control select2" id="id_pemindahan" name="id_pemindahan" required>
                        <option value="">-- Pilih Pemindahan Barang --</option>
                        <?php foreach ($pemindahan as $row): ?>
                            <option value="<?php echo $row->id_pemindahan; ?>"><?php echo $row->no_transaksi; ?> -
                                <?php echo $row->nama_penerima; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php echo form_error('id_pemindahan', '<small class="text-danger">', '</small>'); ?>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="alasan_retur">Alasan Retur <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="alasan_retur" name="alasan_retur" rows="3"
                        placeholder="Masukkan alasan retur" required><?php echo set_value('alasan_retur'); ?></textarea>
                    <?php echo form_error('alasan_retur', '<small class="text-danger">', '</small>'); ?>
                </div>
            </div>
        </div>

        <hr>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">Daftar Barang</h6>
        </div>

        <div class="table-responsive mb-3">
            <table class="table table-bordered table-striped" id="table_barang">
                <thead class="thead-dark">
                    <tr>
                        <th width="5%">No</th>
                        <th width="35%">Barang</th>
                        <th width="15%">Gudang</th>
                        <th width="15%">Jumlah Dikirim</th>
                        <th width="15%">Jumlah Retur</th>
                        <th width="20%">Alasan</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="6" class="text-center">
                            <em>Silakan pilih pemindahan barang terlebih dahulu</em>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="form-group text-right">
            <a href="<?php echo site_url('aktifitas/retur_penjualan'); ?>" class="btn btn-secondary">
                <i class="fas fa-times"></i> Batal
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Simpan
            </button>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>