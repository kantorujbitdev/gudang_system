<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h4 class="m-0 font-weight-bold text-primary">Detail Barang</h4>
    <div>
        <a href="<?php echo site_url('setup/barang/edit/' . $barang->id_barang); ?>"
            class="d-none d-sm-inline-block btn btn-sm btn-warning shadow-sm">
            <i class="fas fa-edit fa-sm text-white-50"></i> Edit
        </a>
        <a href="<?php echo site_url('setup/barang'); ?>"
            class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm ml-2">
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
            <div class="col-md-8">
                <table class="table table-borderless">
                    <tr>
                        <td width="30%"><strong>SKU</strong></td>
                        <td><?php echo $barang->sku; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Kode Barang</strong></td>
                        <td><?php echo $barang->kode_barang ?: '-'; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Nama Barang</strong></td>
                        <td><?php echo $barang->nama_barang; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Ukuran</strong></td>
                        <td><?php echo $barang->ukuran ?: '-'; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Motor</strong></td>
                        <td><?php echo $barang->motor ?: '-'; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Warna</strong></td>
                        <td><?php echo $barang->warna ?: '-'; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Kategori</strong></td>
                        <td><?php echo $barang->nama_kategori ?: '-'; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Perusahaan</strong></td>
                        <td><?php echo $barang->nama_perusahaan; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Deskripsi</strong></td>
                        <td><?php echo $barang->deskripsi ?: '-'; ?></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-4">
                <div class="text-center">
                    <?php if ($barang->gambar): ?>
                        <img src="<?php echo base_url('uploads/barang/' . $barang->gambar); ?>" class="img-fluid rounded"
                            style="max-width: 200px;" alt="Gambar Barang">
                    <?php else: ?>
                        <div class="bg-light rounded p-4">
                            <i class="fas fa-image fa-3x text-gray-400"></i>
                            <p class="mt-2 text-gray-500">Tidak ada gambar</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr>
                        <td width="30%"><strong>Satuan</strong></td>
                        <td><?php echo $barang->satuan; ?></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr>
                        <td width="30%"><strong>Status</strong></td>
                        <td>
                            <?php if ($barang->status_aktif == 1): ?>
                                <span class="badge badge-success">Aktif</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Tidak Aktif</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>ID Barang</strong></td>
                        <td><?php echo $barang->id_barang; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Total Stok</strong></td>
                        <td>
                            <span class="badge badge-info"><?php echo $total_stok; ?>
                                <?php echo $barang->satuan; ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Dibuat Pada</strong></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($barang->created_at)); ?></td>
                    </tr>
                    <?php if ($barang->updated_at): ?>
                        <tr>
                            <td><strong>Diperbarui Pada</strong></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($barang->updated_at)); ?></td>
                        </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Stok per Gudang -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h1 class="h5 mb-0 text-gray-800">Stok per Gudang</h1>
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
                            <th>Total</th>
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