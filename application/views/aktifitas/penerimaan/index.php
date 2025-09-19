<div class="card shadow mb-4">
    <div class="card-header py-3">
        <div class="row">
            <div class="col">
                <?php echo responsive_title_blue('Penerimaan Barang') ?>
            </div>
            <?php if ($can_create): ?>
                <div class="col text-right">
                    <a href="<?php echo site_url('aktifitas/penerimaan/tambah'); ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Tambah Penerimaan
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
                        <th>No Penerimaan</th>
                        <th>Tanggal</th>
                        <th>Supplier</th>
                        <th>Gudang</th>
                        <th>User</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;
                    foreach ($penerimaan as $row): ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo $row->no_penerimaan; ?></td>
                            <td><?php echo date('d-m-Y H:i', strtotime($row->tanggal_penerimaan)); ?></td>
                            <td><?php echo $row->nama_supplier; ?></td>
                            <td><?php echo $row->nama_gudang; ?></td>
                            <td><?php echo $row->user_nama; ?></td>
                            <td>
                                <?php
                                $status_class = '';
                                switch ($row->status) {
                                    case 'Draft':
                                        $status_class = 'badge-secondary';
                                        break;
                                    case 'Received':
                                        $status_class = 'badge-info';
                                        break;
                                    case 'Completed':
                                        $status_class = 'badge-success';
                                        break;
                                    case 'Cancelled':
                                        $status_class = 'badge-danger';
                                        break;
                                }
                                ?>
                                <span class="badge <?php echo $status_class; ?>"><?php echo $row->status; ?></span>
                            </td>
                            <td>
                                <a href="<?php echo site_url('aktifitas/penerimaan/detail/' . $row->id_penerimaan); ?>"
                                    class="btn btn-sm btn-info" title="Detail">
                                    <i class="fas fa-info-circle"></i>
                                </a>
                                <?php if ($row->status == 'Draft' && $can_edit): ?>
                                    <a href="<?php echo site_url('aktifitas/penerimaan/edit/' . $row->id_penerimaan); ?>"
                                        class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                <?php endif; ?>
                                <?php if ($row->status == 'Draft' && $can_delete): ?>
                                    <a href="<?php echo site_url('aktifitas/penerimaan/hapus/' . $row->id_penerimaan); ?>"
                                        class="btn btn-sm btn-danger" title="Hapus"
                                        onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                <?php endif; ?>
                                <?php if ($can_edit): ?>
                                    <?php if ($row->status == 'Draft'): ?>
                                        <a href="<?php echo site_url('aktifitas/penerimaan/konfirmasi/' . $row->id_penerimaan . '/Received'); ?>"
                                            class="btn btn-sm btn-primary" title="Proses Penerimaan"
                                            onclick="return confirm('Apakah Anda yakin ingin memproses penerimaan?')">
                                            <i class="fas fa-clipboard-check"></i> Received
                                        </a>
                                    <?php elseif ($row->status == 'Received'): ?>
                                        <a href="<?php echo site_url('aktifitas/penerimaan/konfirmasi/' . $row->id_penerimaan . '/Completed'); ?>"
                                            class="btn btn-sm btn-success" title="Selesaikan Penerimaan"
                                            onclick="return confirm('Apakah Anda yakin ingin menyelesaikan penerimaan?')">
                                            <i class="fas fa-check"></i> Completed
                                        </a>
                                    <?php endif; ?>

                                    <?php if ($row->status != 'Completed' && $row->status != 'Cancelled'): ?>
                                        <a href="<?php echo site_url('aktifitas/penerimaan/konfirmasi/' . $row->id_penerimaan . '/Cancelled'); ?>"
                                            class="btn btn-sm btn-danger" title="Batalkan"
                                            onclick="return confirm('Apakah Anda yakin ingin membatalkan penerimaan ini?')">
                                            <i class="fas fa-times"></i> Batal
                                        </a>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>