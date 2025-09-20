<div class="card shadow mb-4">
    <div class="card-header py-3">
        <div class="row">
            <div class="col">
                <?php echo responsive_title_blue('Stok Awal') ?>
            </div>
            <div class="col text-right">
                <?php if ($can_create): ?>
                    <a href="<?php echo site_url('pengaturan/stok_awal/tambah'); ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Tambah Stok Awal
                    </a>
                    <a href="<?php echo site_url('pengaturan/stok_awal/import'); ?>" class="btn btn-success btn-sm">
                        <i class="fas fa-file-import"></i> Import dari Excel
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Barang</th>
                        <th>Gudang</th>
                        <th>Qty Awal</th>
                        <th>Dibuat Oleh</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;
                    if (!empty($stok_awal)):
                        foreach ($stok_awal as $row): ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo $row->nama_barang; ?> (<?php echo $row->sku; ?>)</td>
                                <td><?php echo $row->nama_gudang; ?></td>
                                <td><?php echo $row->qty_awal; ?></td>
                                <td><?php echo $row->created_by; ?></td>
                                <td><?php echo date('d-m-Y H:i', strtotime($row->created_at)); ?></td>
                                <td>
                                    <?php if ($can_edit): ?>
                                        <a href="<?php echo site_url('pengaturan/stok_awal/edit/' . $row->id_stok_awal); ?>"
                                            class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    <?php endif; ?>
                                    <?php if ($can_delete): ?>
                                        <a href="<?php echo site_url('pengaturan/stok_awal/hapus/' . $row->id_stok_awal); ?>"
                                            class="btn btn-sm btn-danger" title="Hapus"
                                            onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach;
                    else: ?>
                        <tr>
                            <td colspan="7" class="text-center">Tidak ada data stok awal</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>