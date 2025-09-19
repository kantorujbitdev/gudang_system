<div class="card shadow mb-4">
    <div class="card-header py-3">
        <div class="row">
            <div class="col">
                <?php echo responsive_title_blue('Manajemen Admin Packing') ?>
            </div>
            <div class="col text-right">
                <a href="<?php echo site_url('setup/user/tambah_packing'); ?>" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Tambah Admin Packing
                </a>
                <a href="<?php echo site_url('setup/user'); ?>" class="btn btn-secondary btn-sm ml-2">
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
                        <th>Foto</th>
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
                    foreach ($user as $row): ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td>
                                <?php if ($row->foto_profil): ?>
                                    <img src="<?php echo base_url('uploads/user/' . $row->foto_profil); ?>"
                                        class="img-thumbnail" style="max-width: 50px;" alt="Foto Profil">
                                <?php else: ?>
                                    <img src="<?php echo base_url('assets/images/user-default.png'); ?>" class="img-thumbnail"
                                        style="max-width: 50px;" alt="Foto Default">
                                <?php endif; ?>
                            </td>
                            <td><?php echo $row->nama; ?></td>
                            <td><?php echo $row->username; ?></td>
                            <td><?php echo $row->email; ?></td>
                            <td><?php echo $row->telepon ?: '-'; ?></td>
                            <td><?php echo $row->nama_perusahaan; ?></td>
                            <td>
                                <?php if ($row->aktif == 1): ?>
                                    <span class="badge badge-success">Aktif</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Tidak Aktif</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?php echo site_url('setup/user/detail/' . $row->id_user); ?>"
                                    class="btn btn-sm btn-info" title="Detail">
                                    <i class="fas fa-info-circle"></i>
                                </a>
                                <a href="<?php echo site_url('setup/user/edit/' . $row->id_user); ?>"
                                    class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="<?php echo site_url('setup/user/reset_password/' . $row->id_user); ?>"
                                    class="btn btn-sm btn-secondary" title="Reset Password">
                                    <i class="fas fa-key"></i>
                                </a>

                                <?php if ($row->aktif == '1'): ?>
                                    <a href="<?php echo site_url('setup/user/nonaktif/' . $row->id_user) ?>"
                                        class="btn btn-sm btn-danger"
                                        onclick="return confirm('Apakah Anda yakin ingin menonaktifkan user ini?')">
                                        <i class="fas fa-user-slash"></i>
                                    </a>
                                <?php else: ?>
                                    <a href="<?php echo site_url('setup/user/aktif/' . $row->id_user) ?>"
                                        class="btn btn-sm btn-success"
                                        onclick="return confirm('Apakah Anda yakin ingin mengaktifkan user ini?')">
                                        <i class="fas fa-user-check"></i>
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