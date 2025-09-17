<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h4 class="m-0 font-weight-bold text-primary">Detail Kategori Barang</h4>
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
                        <td><strong>Deskripsi</strong></td>
                        <td><?php echo $kategori->deskripsi ?: '-'; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Status</strong></td>
                        <td>
                            <?php if ($kategori->status_aktif == 1): ?>
                                <span class="badge badge-success">Aktif</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Tidak Aktif</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Dibuat Pada</strong></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($kategori->created_at)); ?></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr>
                        <td width="30%"><strong>Total Barang</strong></td>
                        <td><?php echo count($barang); ?></td>
                    </tr>
                    <tr>
                        <td><strong>ID Kategori</strong></td>
                        <td><?php echo $kategori->id_kategori; ?></td>
                    </tr>
                    <?php if ($this->session->userdata('id_role') == 1): ?>
                        <tr>
                            <td><strong>ID Perusahaan</strong></td>
                            <td><?php echo $kategori->id_perusahaan; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Nama Perusahaan</strong></td>
                            <td><?php echo $kategori->nama_perusahaan ?? '-'; ?></td>
                        </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($barang)): ?>
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h1 class="h5 mb-0 text-gray-800">Daftar Barang dalam Kategori Ini</h1>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode Barang</th>
                            <th>Nama Barang</th>
                            <th>Harga Jual</th>
                            <th>Stok</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;
                        foreach ($barang as $item): ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo $item->sku; ?></td>
                                <td><?php echo $item->nama_barang; ?></td>
                                <td><?php echo number_format($item->harga_jual, 0, ',', '.'); ?></td>
                                <td>
                                    <?php
                                    // Get stock for this item
                                    $this->db->select('SUM(jumlah) as total_stok');
                                    $this->db->where('id_barang', $item->id_barang);
                                    $stok_result = $this->db->get('stok_gudang')->row();
                                    $total_stok = $stok_result ? $stok_result->total_stok : 0;
                                    echo $total_stok;
                                    ?>
                                </td>
                                <td>
                                    <?php if ($item->aktif == 1): ?>
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
        </div>
    </div>
<?php else: ?>
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Tidak ada barang dalam kategori ini.
            </div>
        </div>
    </div>
<?php endif; ?>