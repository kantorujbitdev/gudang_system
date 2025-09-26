<div class="card shadow mb-4">
    <div class="card-header py-3">
        <div class="row">
            <div class="col">
                <?php echo responsive_title_blue('Retur Pembelian') ?>
            </div>
            <?php if ($this->auth->has_permission('aktifitas/retur_pembelian', 'create')): ?>
                <div class="col text-right">
                    <a href="<?php echo site_url('aktifitas/retur_pembelian/tambah'); ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Tambah Retur
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>No Retur</th>
                        <th>Tanggal</th>
                        <th>Supplier</th>
                        <th>User</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;
                    foreach ($retur as $row): ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo $row->no_retur_beli; ?></td>
                            <td><?php echo date('d-m-Y H:i:s', strtotime($row->tanggal_retur)); ?></td>
                            <td><?php echo $row->nama_supplier; ?></td>
                            <td><?php echo $row->user_nama; ?></td>
                            <td>
                                <?php
                                $status_class = '';
                                switch ($row->status) {
                                    case 'Requested':
                                        $status_class = 'badge-secondary';
                                        break;
                                    case 'Verification':
                                        $status_class = 'badge-info';
                                        break;
                                    case 'Approved':
                                        $status_class = 'badge-warning';
                                        break;
                                    case 'Rejected':
                                        $status_class = 'badge-danger';
                                        break;
                                    case 'Completed':
                                        $status_class = 'badge-success';
                                        break;
                                }
                                ?>
                                <span class="badge <?php echo $status_class; ?>"><?php echo $row->status; ?></span>
                            </td>
                            <td>
                                <a href="<?php echo site_url('aktifitas/retur_pembelian/detail/' . $row->id_retur_beli); ?>"
                                    class="btn btn-sm btn-info" title="Detail">
                                    <i class="fas fa-info-circle"></i>
                                </a>
                                <?php if ($row->status == 'Requested' && $this->auth->has_permission('aktifitas/retur_pembelian', 'edit')): ?>
                                    <a href="<?php echo site_url('aktifitas/retur_pembelian/verifikasi/' . $row->id_retur_beli); ?>"
                                        class="btn btn-sm btn-warning" title="Verifikasi">
                                        <i class="fas fa-check-circle"></i> Verifikasi
                                    </a>
                                <?php endif; ?>
                                <?php if ($row->status == 'Requested' && $this->auth->has_permission('aktifitas/retur_pembelian', 'delete')): ?>
                                    <a href="<?php echo site_url('aktifitas/retur_pembelian/hapus/' . $row->id_retur_beli); ?>"
                                        class="btn btn-sm btn-danger" title="Hapus"
                                        onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>