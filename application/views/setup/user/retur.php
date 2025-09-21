<div class="card shadow mb-4">
    <div class="card-header py-3">
        <div class="row">
            <div class="col">
                <?php echo responsive_title_blue('Manajemen Admin Retur') ?>
            </div>
            <div class="col text-right">
                <?php if ($can_create): ?>
                    <a href="<?php echo site_url('setup/user/tambah/5') ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Tambah Admin Retur
                    </a>
                <?php endif; ?>
                <a href="<?php echo site_url('setup/user') ?>" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Kembali
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
                        <th>Nama</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Telepon</th>
                        <th>Perusahaan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;
                    if (!empty($users)):
                        foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo $user->nama; ?></td>
                                <td><?php echo $user->username; ?></td>
                                <td><?php echo $user->email ?: '-'; ?></td>
                                <td><?php echo $user->telepon ?: '-'; ?></td>
                                <td><?php echo $user->nama_perusahaan ?: '-'; ?></td>
                                <td>
                                    <?php if ($user->aktif == 1): ?>
                                        <span class="badge badge-success">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Tidak Aktif</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($can_edit): ?>
                                        <a href="<?php echo site_url('setup/user/edit/' . $user->id_user); ?>"
                                            class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    <?php endif; ?>

                                    <?php if ($user->aktif == 1): ?>
                                        <a href="<?php echo site_url('setup/user/nonaktif/' . $user->id_user) ?>"
                                            class="btn btn-sm btn-danger"
                                            onclick="return confirm('Apakah Anda yakin ingin menonaktifkan user ini?')"
                                            title="Nonaktifkan">
                                            <i class="fas fa-user-slash"></i>
                                        </a>
                                    <?php else: ?>
                                        <a href="<?php echo site_url('setup/user/aktif/' . $user->id_user) ?>"
                                            class="btn btn-sm btn-success"
                                            onclick="return confirm('Apakah Anda yakin ingin mengaktifkan user ini?')"
                                            title="Aktifkan">
                                            <i class="fas fa-user-check"></i>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach;
                    else: ?>
                        <tr>
                            <td colspan="8" class="text-center">Tidak ada data admin retur</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>