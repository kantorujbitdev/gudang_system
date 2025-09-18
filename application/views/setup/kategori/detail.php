<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h4 class="m-0 font-weight-bold text-primary">Detail Kategori</h4>
    <div>
        <a href="<?php echo site_url('setup/kategori/edit/' . $kategori->id_kategori); ?>"
            class="d-none d-sm-inline-block btn btn-sm btn-warning shadow-sm">
            <i class="fas fa-edit fa-sm text-white-50"></i> Edit
        </a>
        <a href="<?php echo site_url('setup/kategori'); ?>"
            class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm ml-2">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h1 class="h5 mb-0 text-gray-800">Informasi Kategori</h1>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr>
                        <td width="30%"><strong>Nama Kategori</strong></td>
                        <td><?php echo $kategori->nama_kategori; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Perusahaan</strong></td>
                        <td><?php echo $kategori->nama_perusahaan; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Deskripsi</strong></td>
                        <td><?php echo $kategori->deskripsi ?: '-'; ?></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr>
                        <td width="30%"><strong>Status</strong></td>
                        <td>
                            <?php if ($kategori->status_aktif == 1): ?>
                                <span class="badge badge-success">Aktif</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Tidak Aktif</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>ID Kategori</strong></td>
                        <td><?php echo $kategori->id_kategori; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Dibuat Pada</strong></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($kategori->created_at)); ?></td>
                    </tr>
                    <?php if ($kategori->updated_at): ?>
                        <tr>
                            <td><strong>Diperbarui Pada</strong></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($kategori->updated_at)); ?></td>
                        </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Daftar Barang dalam Kategori -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h1 class="h5 mb-0 text-gray-800">Daftar Barang dalam Kategori (10 Terbaru)</h1>
    </div>
    <div class="card-body">
        <?php if (!empty($barang)): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode Barang</th>
                            <th>Nama Barang</th>
                            <th>Gudang</th>
                            <th>Stok</th>
                            <th>Harga Jual</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;
                        $barang_limit = array_slice($barang, 0, 10);
                        foreach ($barang_limit as $b): ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo $b->sku; ?></td>
                                <td><?php echo $b->nama_barang; ?></td>
                                <td><?php echo $b->nama_gudang; ?></td>
                                <td><?php echo $b->stok ?: 0; ?></td>
                                <td><?php echo number_format($b->harga_jual, 0, ',', '.'); ?></td>
                                <td>
                                    <?php if ($b->aktif == 1): ?>
                                        <span class="badge badge-success">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Tidak Aktif</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php if (count($barang) > 10): ?>
                <div class="text-center mt-2">
                    <a href="<?php echo site_url('setup/barang'); ?>" class="btn btn-sm btn-outline-primary">
                        Lihat Semua Barang
                    </a>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Belum ada barang dalam kategori ini.
            </div>
        <?php endif; ?>
    </div>
</div>