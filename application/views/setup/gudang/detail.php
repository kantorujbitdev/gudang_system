<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h4 class="m-0 font-weight-bold text-primary">Detail Gudang</h4>
    <div>
        <a href="<?php echo site_url('setup/gudang/edit/' . $gudang->id_gudang); ?>"
            class="d-none d-sm-inline-block btn btn-sm btn-warning shadow-sm">
            <i class="fas fa-edit fa-sm text-white-50"></i> Edit
        </a>
        <a href="<?php echo site_url('setup/gudang'); ?>"
            class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm ml-2">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h1 class="h5 mb-0 text-gray-800">Informasi Gudang</h1>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr>
                        <td width="30%"><strong>Nama Gudang</strong></td>
                        <td><?php echo $gudang->nama_gudang; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Alamat</strong></td>
                        <td><?php echo $gudang->alamat ?: '-'; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Telepon</strong></td>
                        <td><?php echo $gudang->telepon ?: '-'; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Status</strong></td>
                        <td>
                            <?php if ($gudang->status_aktif == 1): ?>
                                <span class="badge badge-success">Aktif</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Tidak Aktif</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr>
                        <td width="30%"><strong>ID Gudang</strong></td>
                        <td><?php echo $gudang->id_gudang; ?></td>
                    </tr>
                    <tr>
                        <td><strong>ID Perusahaan</strong></td>
                        <td><?php echo $gudang->id_perusahaan; ?></td>
                    </tr>
                    <?php if ($this->session->userdata('id_role') == 1): ?>
                        <tr>
                            <td><strong>Nama Perusahaan</strong></td>
                            <td><?php echo $gudang->nama_perusahaan ?? '-'; ?></td>
                        </tr>
                    <?php endif; ?>
                    <tr>
                        <td><strong>Dibuat Pada</strong></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($gudang->created_at)); ?></td>
                    </tr>
                    <?php if ($gudang->updated_at): ?>
                        <tr>
                            <td><strong>Diperbarui Pada</strong></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($gudang->updated_at)); ?></td>
                        </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Daftar Stok di Gudang -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h1 class="h5 mb-0 text-gray-800">Daftar Stok di Gudang Ini</h1>
    </div>
    <div class="card-body">
        <?php if (!empty($stok)): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode Barang</th>
                            <th>Nama Barang</th>
                            <th>Kategori</th>
                            <th>Jumlah Stok</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;
                        foreach ($stok as $item): ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo $item->sku; ?></td>
                                <td><?php echo $item->nama_barang; ?></td>
                                <td><?php echo $item->nama_kategori; ?></td>
                                <td>
                                    <?php if ($item->jumlah > 0): ?>
                                        <span class="badge badge-success"><?php echo $item->jumlah; ?></span>
                                    <?php else: ?>
                                        <span class="badge badge-danger"><?php echo $item->jumlah; ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($item->jumlah > 10): ?>
                                        <span class="badge badge-success">Cukup</span>
                                    <?php elseif ($item->jumlah > 0): ?>
                                        <span class="badge badge-warning">Menipis</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Habis</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Tidak ada stok di gudang ini.
            </div>
        <?php endif; ?>
    </div>
</div>