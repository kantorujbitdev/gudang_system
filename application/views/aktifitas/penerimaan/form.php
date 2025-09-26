<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary"><?php echo $title; ?></h6>
    </div>
    <div class="card-body">
        <?php echo form_open(current_url(), array('class' => 'form-penerimaan')); ?>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="id_supplier">Supplier <span class="text-danger">*</span></label>
                    <select class="form-control select2" id="id_supplier" name="id_supplier" required>
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
                    <select class="form-control select2" id="id_gudang" name="id_gudang" required>
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
                <!-- Hidden field for tanggal penerimaan -->
                <input type="hidden" id="tanggal_penerimaan" name="tanggal_penerimaan"
                    value="<?php echo date('Y-m-d H:i:s'); ?>">

                <div class="form-group">
                    <label for="keterangan">Keterangan</label>
                    <textarea class="form-control" id="keterangan" name="keterangan" rows="3"
                        placeholder="Masukkan keterangan tambahan jika ada"><?php echo set_value('keterangan', isset($penerimaan) ? $penerimaan->keterangan : ''); ?></textarea>
                </div>
            </div>
        </div>

        <hr>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">Daftar Barang</h6>
            <button type="button" class="btn btn-sm btn-success" id="btn-tambah-barang">
                <i class="fas fa-plus"></i> Tambah Barang
            </button>
        </div>

        <div class="table-responsive mb-3">
            <table class="table table-bordered table-striped" id="table_barang">
                <thead class="thead-dark">
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
                                        value="<?php echo $item->jumlah_dipesan; ?>" min="0" placeholder="0">
                                </td>
                                <td>
                                    <input type="number" class="form-control jumlah-diterima" name="jumlah_diterima[]"
                                        value="<?php echo $item->jumlah_diterima; ?>" min="1" required>
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="keterangan_barang[]"
                                        value="<?php echo $item->keterangan; ?>" placeholder="Keterangan barang">
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-danger btn-hapus-barang">
                                        <i class="fas fa-trash"></i>
                                    </button>
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
                                <input type="number" class="form-control" name="jumlah_dipesan[]" min="0" placeholder="0">
                            </td>
                            <td>
                                <input type="number" class="form-control jumlah-diterima" name="jumlah_diterima[]" min="1"
                                    required>
                            </td>
                            <td>
                                <input type="text" class="form-control" name="keterangan_barang[]"
                                    placeholder="Keterangan barang">
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-danger btn-hapus-barang">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="alert alert-info d-none" id="alert-barang">
            <i class="fas fa-info-circle"></i> Minimal harus ada 1 barang yang diterima!
        </div>

        <div class="form-group text-right">
            <a href="<?php echo site_url('aktifitas/penerimaan'); ?>" class="btn btn-secondary">
                <i class="fas fa-times"></i> Batal
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Simpan
            </button>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>

<!-- Modal untuk info stok -->
<div class="modal fade" id="modalStok" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Informasi Stok Barang</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Gudang</th>
                                <th>Stok Tersedia</th>
                                <th>Satuan</th>
                            </tr>
                        </thead>
                        <tbody id="info-stok-body">
                            <!-- Data akan diisi via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>