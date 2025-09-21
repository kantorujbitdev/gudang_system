<div class="card shadow mb-4">
    <div class="card-header py-3">
        <div class="row">
            <div class="col">
                <?php echo responsive_title_blue('Manajemen User') ?>
            </div>
            <div class="col text-right">
                <?php if ($can_create): ?>
                    <div class="dropdown d-inline-block">
                        <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-plus"></i> Tambah User
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <?php if ($this->session->userdata('id_role') == 1): ?>
                                <a class="dropdown-item" href="<?php echo site_url('setup/user/tambah/2') ?>">Admin
                                    Perusahaan</a>
                            <?php endif; ?>
                            <a class="dropdown-item" href="<?php echo site_url('setup/user/tambah/3') ?>">Sales Online</a>
                            <a class="dropdown-item" href="<?php echo site_url('setup/user/tambah/4') ?>">Admin Packing</a>
                            <a class="dropdown-item" href="<?php echo site_url('setup/user/tambah/5') ?>">Admin Retur</a>
                        </div>
                    </div>
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
                        <th>Nama</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Perusahaan</th>
                        <th>Email</th>
                        <th>Telepon</th>
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
                                <td><?php echo $user->nama_role; ?></td>
                                <td><?php echo $user->nama_perusahaan ?: '-'; ?></td>
                                <td><?php echo $user->email ?: '-'; ?></td>
                                <td><?php echo $user->telepon ?: '-'; ?></td>
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
                            <td colspan="9" class="text-center">Tidak ada data user</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>