<div class="card shadow mb-4">
    <div class="card-header py-3">
        <div class="row">
            <div class="col">
                <?php echo responsive_title_blue('Stok Awal') ?>
            </div>
            <div class="col text-right">
                <?php if ($can_create): ?>
                    <a href="<?php echo site_url('pengaturan/stok_awal/import'); ?>" class="btn btn-success btn-sm">
                        <i class="fas fa-file-import"></i> Import dari Excel
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="card-body">
        <?php if (isset($perusahaan)): ?>
            <div class="form-group row mb-3">
                <label class="col-sm-2 col-form-label">Perusahaan</label>
                <div class="col-sm-4">
                    <select class="form-control" id="select_perusahaan" onchange="changePerusahaan()">
                        <?php foreach ($perusahaan as $p): ?>
                            <option value="<?php echo $p->id_perusahaan; ?>" <?php echo ($selected_perusahaan == $p->id_perusahaan) ? 'selected' : ''; ?>>
                                <?php echo $p->nama_perusahaan; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode Barang</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th>Gudang</th>
                        <th>Stok Awal</th>
                        <th>Stok Terkini</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;
                    if (!empty($barang)):
                        foreach ($barang as $row):
                            foreach ($row['gudang'] as $g): ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td><?php echo $row['sku']; ?></td>
                                    <td><?php echo $row['nama_barang']; ?></td>
                                    <td><?php echo $row['nama_kategori']; ?></td>
                                    <td><?php echo $g['nama_gudang']; ?></td>
                                    <td>
                                        <?php if ($g['stok_awal'] > 0): ?>
                                            <span class="badge badge-primary"><?php echo $g['stok_awal']; ?></span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary">0</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($g['stok_terkini'] > 0): ?>
                                            <span class="badge badge-success"><?php echo $g['stok_terkini']; ?></span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary">0</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($can_create && !$g['has_stok_awal']): ?>
                                            <button type="button" class="btn btn-primary btn-sm btn-tambah-stok"
                                                data-id_barang="<?php echo $row['id_barang']; ?>"
                                                data-nama_barang="<?php echo $row['nama_barang']; ?>"
                                                data-id_gudang="<?php echo $g['id_gudang']; ?>"
                                                data-nama_gudang="<?php echo $g['nama_gudang']; ?>">
                                                <i class="fas fa-plus"></i> Tambah
                                            </button>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>

                                </tr>
                            <?php endforeach;
                        endforeach;
                    else: ?>
                        <tr>
                            <td colspan="8" class="text-center">Tidak ada data barang</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Stok -->
<div class="modal fade" id="modalTambahStok" tabindex="-1" role="dialog" aria-labelledby="modalTambahStokLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTambahStokLabel">Tambah Stok Awal</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formTambahStok">
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>"
                        value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <input type="hidden" name="id_barang" id="modal_id_barang">
                    <input type="hidden" name="id_gudang" id="modal_id_gudang">
                    <?php if (isset($selected_perusahaan)): ?>
                        <input type="hidden" name="id_perusahaan" value="<?php echo $selected_perusahaan; ?>">
                    <?php endif; ?>

                    <div class="form-group">
                        <label>Barang</label>
                        <input type="text" class="form-control" id="modal_nama_barang" readonly>
                    </div>

                    <div class="form-group">
                        <label>Gudang</label>
                        <input type="text" class="form-control" id="modal_nama_gudang" readonly>
                    </div>

                    <div class="form-group">
                        <label for="modal_qty_awal">Jumlah Stok <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="modal_qty_awal" name="qty_awal" min="0" required>
                    </div>

                    <div class="form-group">
                        <label for="modal_keterangan">Keterangan</label>
                        <textarea class="form-control" id="modal_keterangan" name="keterangan" rows="2"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnSimpanStok">Simpan</button>
            </div>
        </div>
    </div>
</div>