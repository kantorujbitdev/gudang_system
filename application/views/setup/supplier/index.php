<div class="card shadow mb-4">
    <div class="card-header py-3">
        <div class="row">
            <div class="col">
                <?php echo responsive_title_blue('Daftar Supplier') ?>
            </div>
            <div class="col text-right">
                <a href="<?php echo site_url('setup/supplier/tambah') ?>" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Tambah Supplier
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Supplier</th>
                        <th>Perusahaan</th>
                        <th>Telepon</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;
                    foreach ($supplier as $row): ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo $row->nama_supplier; ?></td>
                            <td><?php echo $row->nama_perusahaan; ?></td>
                            <td><?php echo $row->telepon ?: '-'; ?></td>
                            <td><?php echo $row->email ?: '-'; ?></td>
                            <td>
                                <?php if ($row->status_aktif == 1): ?>
                                    <span class="badge badge-success">Aktif</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Tidak Aktif</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?php echo site_url('setup/supplier/detail/' . $row->id_supplier); ?>"
                                    class="btn btn-sm btn-info" title="Detail">
                                    <i class="fas fa-info-circle"></i> Detail
                                </a>
                                <a href="<?php echo site_url('setup/supplier/edit/' . $row->id_supplier); ?>"
                                    class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i> Edit
                                </a>

                                <?php if ($row->status_aktif == '1'): ?>
                                    <a href="<?php echo site_url('setup/supplier/nonaktif/' . $row->id_supplier) ?>"
                                        class="btn btn-sm btn-danger"
                                        onclick="return confirm('Apakah Anda yakin ingin menonaktifkan supplier ini?')">
                                        <i class="fas fa-minus-square"></i> Nonaktif
                                    </a>
                                <?php else: ?>
                                    <a href="<?php echo site_url('setup/supplier/aktif/' . $row->id_supplier) ?>"
                                        class="btn btn-sm btn-success"
                                        onclick="return confirm('Apakah Anda yakin ingin mengaktifkan kembali supplier ini?')">
                                        <i class="fas fa-check-square"></i> Aktif
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