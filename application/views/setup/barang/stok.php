<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h4 class="m-0 font-weight-bold text-primary">Manajemen Stok: <?php echo $barang->nama_barang; ?></h4>
    <div>
        <a href="<?php echo site_url('setup/barang'); ?>"
            class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h1 class="h5 mb-0 text-gray-800">Informasi Barang</h1>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-sm">
                    <tr>
                        <th width="30%">SKU</th>
                        <td><?php echo $barang->sku; ?></td>
                    </tr>
                    <tr>
                        <th>Nama Barang</th>
                        <td><?php echo $barang->nama_barang; ?></td>
                    </tr>
                    <tr>
                        <th>Kategori</th>
                        <td><?php echo $barang->nama_kategori ?: '-'; ?></td>
                    </tr>
                    <tr>
                        <th>Satuan</th>
                        <td><?php echo $barang->satuan; ?></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-sm">
                    <tr>
                        <th width="30%">Status</th>
                        <td>
                            <?php if ($barang->status_aktif == 1): ?>
                                <span class="badge badge-success">Aktif</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Tidak Aktif</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Total Stok</th>
                        <td>
                            <?php
                            $total_stok = 0;
                            $total_reserved = 0;
                            foreach ($stok as $s) {
                                $total_stok += $s->jumlah;
                                $total_reserved += $s->reserved;
                            }
                            $stok_tersedia = $total_stok - $total_reserved;
                            ?>
                            <span class="badge badge-info"><?php echo $stok_tersedia; ?>
                                <?php echo $barang->satuan; ?></span>
                            <?php if ($total_reserved > 0): ?>
                                <small class="text-muted">(<?php echo $total_reserved; ?> reserved)</small>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h1 class="h5 mb-0 text-gray-800">Stok per Gudang</h1>
        <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#tambahStokModal">
            <i class="fas fa-plus"></i> Tambah Stok
        </button>
    </div>
    <div class="card-body">
        <?php if (!empty($stok)): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Gudang</th>
                            <th>Stok Tersedia</th>
                            <th>Reserved</th>
                            <th>Total Stok</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;
                        foreach ($stok as $s): ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo $s->nama_gudang; ?></td>
                                <td><?php echo $s->jumlah - $s->reserved; ?></td>
                                <td><?php echo $s->reserved; ?></td>
                                <td><?php echo $s->jumlah; ?></td>
                                <td>
                                    <button class="btn btn-sm btn-warning edit-stok"
                                        data-id_barang="<?php echo $barang->id_barang; ?>"
                                        data-id_gudang="<?php echo $s->id_gudang; ?>" data-jumlah="<?php echo $s->jumlah; ?>"
                                        data-nama_gudang="<?php echo $s->nama_gudang; ?>">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Barang ini belum memiliki stok di gudang mana pun.
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Tambah Stok -->
<div class="modal fade" id="tambahStokModal" tabindex="-1" role="dialog" aria-labelledby="tambahStokModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tambahStokModalLabel">Tambah Stok Barang</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="tambahStokForm">
                    <input type="hidden" name="id_barang" value="<?php echo $barang->id_barang; ?>">
                    <div class="form-group">
                        <label for="id_gudang">Gudang</label>
                        <select name="id_gudang" id="id_gudang" class="form-control" required>
                            <option value="">-- Pilih Gudang --</option>
                            <?php foreach ($gudang as $g): ?>
                                <option value="<?php echo $g->id_gudang; ?>"><?php echo $g->nama_gudang; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="jumlah">Jumlah</label>
                        <input type="number" name="jumlah" id="jumlah" class="form-control" min="1" required>
                        <small class="form-text text-muted">Masukkan jumlah stok yang akan ditambahkan</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" id="submitTambahStok" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Stok -->
<div class="modal fade" id="editStokModal" tabindex="-1" role="dialog" aria-labelledby="editStokModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editStokModalLabel">Edit Stok Barang</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editStokForm">
                    <input type="hidden" name="id_barang" id="edit_id_barang">
                    <input type="hidden" name="id_gudang" id="edit_id_gudang">
                    <div class="form-group">
                        <label>Gudang</label>
                        <input type="text" id="edit_nama_gudang" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label for="edit_jumlah">Jumlah Saat Ini</label>
                        <input type="number" id="edit_jumlah_sekarang" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label for="edit_jumlah">Jumlah</label>
                        <input type="number" name="jumlah" id="edit_jumlah" class="form-control" min="1" required>
                        <small class="form-text text-muted">Masukkan jumlah stok yang akan ditambahkan (positif) atau
                            dikurangi (negatif)</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" id="submitEditStok" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</div>