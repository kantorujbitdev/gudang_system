<div class="card shadow mb-4">
    <div class="card-header py-3">
        <div class="row">
            <div class="col">
                <h6 class="m-0 font-weight-bold text-primary">Informasi Stok: <?php echo $barang->nama_barang; ?></h6>
            </div>
            <div class="col text-right">
                <a href="<?php echo site_url('setup/barang'); ?>" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-6">
                <table class="table table-sm">
                    <tr>
                        <th width="30%">SKU</th>
                        <td><?php echo $barang->sku; ?></td>
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
                        <th>Status</th>
                        <td>
                            <?php if ($barang->status_aktif == 1): ?>
                                <span class="badge badge-success">Aktif</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Tidak Aktif</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <hr>

        <h6 class="mb-3">Stok per Gudang</h6>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Gudang</th>
                        <th>Stok Tersedia</th>
                        <th>Reserved</th>
                        <th>Total Stok</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;
                    foreach ($stok as $row): ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo $row->nama_gudang; ?></td>
                            <td><?php echo $row->jumlah - $row->reserved; ?></td>
                            <td><?php echo $row->reserved; ?></td>
                            <td><?php echo $row->jumlah; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>