<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h4 class="m-0 font-weight-bold text-primary">Barang</h1>
    <a href="<?php echo site_url('setup/barang/tambah'); ?>"
        class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
        <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Barang
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h3 class="h5 mb-0 text-gray-800"Daftar Barang</h4>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th>Satuan</th>
                        <th>Harga Jual</th>
                        <th>Stok</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;
                    foreach ($barang as $row): ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td><?php echo $row->sku; ?></td>
                                    <td><?php echo $row->nama_barang; ?></td>
                                    <td><?php echo $row->nama_kategori; ?></td>
                                    <td><?php echo $row->satuan; ?></td>
                                    <td><?php echo number_format($row->harga_jual, 0, ',', '.'); ?></td>
                                    <td><?php echo $row->total_stok ?: 0; ?></td>
                                    <td>
                                        <?php if ($row->aktif == 1): ?>
                                                    <span class="badge badge-success">Aktif</span>
                                        <?php else: ?>
                                                    <span class="badge badge-danger">Tidak Aktif</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?php echo site_url('setup/barang/edit/' . $row->id_barang); ?>"
                                            class="btn btn-sm btn-info">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?php echo site_url('setup/barang/hapus/' . $row->id_barang); ?>"
                                            class="btn btn-sm btn-danger"
                                            onclick="return confirm('Apakah Anda yakin ingin menghapus barang ini?');">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>