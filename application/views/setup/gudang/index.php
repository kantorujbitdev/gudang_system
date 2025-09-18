<div class="card shadow mb-4">
    <div class="card-header py-3">
        <div class="row">
            <div class="col">
                <?php echo responsive_title_blue('Daftar Gudang') ?>
            </div>
            <div class="col text-right">
                <a href="<?php echo site_url('setup/gudang/tambah') ?>" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Tambah Gudang
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
                        <th>Nama Gudang</th>
                        <th>Perusahaan</th>
                        <th>Alamat</th>
                        <th>Telepon</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;
                    foreach ($gudang as $row): ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo $row->nama_gudang; ?></td>
                            <td><?php echo $row->nama_perusahaan; ?></td>
                            <td><?php echo $row->alamat ?: '-'; ?></td>
                            <td><?php echo $row->telepon ?: '-'; ?></td>
                            <td>
                                <?php if ($row->status_aktif == 1): ?>
                                    <span class="badge badge-success">Aktif</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Tidak Aktif</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?php echo site_url('setup/gudang/detail/' . $row->id_gudang); ?>"
                                    class="btn btn-sm btn-info" title="Detail">
                                    <i class="fas fa-info-circle"></i> Detail
                                </a>
                                <a href="<?php echo site_url('setup/gudang/edit/' . $row->id_gudang); ?>"
                                    class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i> Edit
                                </a>

                                <?php if ($row->status_aktif == '1'): ?>
                                    <a href="<?php echo site_url('setup/gudang/nonaktif/' . $row->id_gudang) ?>"
                                        class="btn btn-sm btn-danger"
                                        onclick="return confirm('Apakah Anda yakin ingin menonaktifkan gudang ini?')">
                                        <i class="fas fa-minus-square"></i> Nonaktif
                                    </a>
                                <?php else: ?>
                                    <a href="<?php echo site_url('setup/gudang/aktif/' . $row->id_gudang) ?>"
                                        class="btn btn-sm btn-success"
                                        onclick="return confirm('Apakah Anda yakin ingin mengaktifkan kembali gudang ini?')">
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