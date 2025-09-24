<div class="card shadow mb-4">
    <div class="card-header py-3">
        <div class="row">
            <div class="col">
                <?php echo responsive_title_blue('Daftar Kategori') ?>
            </div>
            <div class="col text-right">
                <a href="<?php echo site_url('setup/kategori/tambah') ?>" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Tambah Kategori
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
                        <th>Nama Kategori</th>
                        <th>Perusahaan</th>
                        <th>Deskripsi</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;
                    foreach ($kategori as $row): ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo $row->nama_kategori; ?></td>
                            <td><?php echo $row->nama_perusahaan; ?></td>
                            <td><?php echo $row->deskripsi ?: '-'; ?></td>
                            <td>
                                    <?php if ($row->status_aktif == 1): ?>
                                    <span class="badge badge-success">Aktif</span>
                                    <?php else: ?>
                                    <span class="badge badge-danger">Tidak Aktif</span>
                                    <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?php echo site_url('setup/kategori/detail/' . $row->id_kategori); ?>"
                                    class="btn btn-sm btn-info" title="Detail">
                                    <i class="fas fa-info-circle"></i>
                                </a>
                                <a href="<?php echo site_url('setup/kategori/edit/' . $row->id_kategori); ?>"
                                    class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>

                                    <?php if ($row->status_aktif == '1'): ?>
                                    <a href="<?php echo site_url('setup/kategori/nonaktif/' . $row->id_kategori) ?>"
                                        class="btn btn-sm btn-danger"
                                        onclick="return confirm('Apakah Anda yakin ingin menonaktifkan kategori ini?')">
                                        <i class="fas fa-minus-square"></i>
                                    </a>
                                    <?php else: ?>
                                    <a href="<?php echo site_url('setup/kategori/aktif/' . $row->id_kategori) ?>"
                                        class="btn btn-sm btn-success"
                                        onclick="return confirm('Apakah Anda yakin ingin mengaktifkan kembali kategori ini?')">
                                        <i class="fas fa-check-square"></i>
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