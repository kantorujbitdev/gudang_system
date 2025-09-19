<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h4 class="m-0 font-weight-bold text-primary">Detail User</h4>
    <div>
        <a href="<?php echo site_url('setup/user/edit/' . $user->id_user); ?>"
            class="d-none d-sm-inline-block btn btn-sm btn-warning shadow-sm">
            <i class="fas fa-edit fa-sm text-white-50"></i> Edit
        </a>
        <a href="<?php echo site_url('setup/user/reset_password/' . $user->id_user); ?>"
            class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm ml-2">
            <i class="fas fa-key fa-sm text-white-50"></i> Reset Password
        </a>
        <a href="<?php echo site_url('setup/user'); ?>"
            class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm ml-2">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h1 class="h5 mb-0 text-gray-800">Informasi User</h1>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4 text-center">
                <?php if ($user->foto_profil): ?>
                    <img src="<?php echo base_url('uploads/user/' . $user->foto_profil); ?>"
                        class="img-thumbnail rounded-circle mb-3" style="max-width: 150px;" alt="Foto Profil">
                <?php else: ?>
                    <img src="<?php echo base_url('assets/images/user-default.png'); ?>"
                        class="img-thumbnail rounded-circle mb-3" style="max-width: 150px;" alt="Foto Default">
                <?php endif; ?>
                <h5><?php echo $user->nama; ?></h5>
                <p class="text-muted">@<?php echo $user->username; ?></p>
            </div>
            <div class="col-md-8">
                <table class="table table-borderless">
                    <tr>
                        <td width="30%"><strong>Nama Lengkap</strong></td>
                        <td><?php echo $user->nama; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Username</strong></td>
                        <td><?php echo $user->username; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Email</strong></td>
                        <td><?php echo $user->email; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Telepon</strong></td>
                        <td><?php echo $user->telepon ?: '-'; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Role</strong></td>
                        <td>
                            <?php if ($user->id_role == 3): ?>
                                <span class="badge badge-info">Sales</span>
                            <?php elseif ($user->id_role == 4): ?>
                                <span class="badge badge-warning">Admin Packing</span>
                            <?php else: ?>
                                <span class="badge badge-secondary"><?php echo $user->nama_role; ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Perusahaan</strong></td>
                        <td><?php echo $user->nama_perusahaan ?: '-'; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Status</strong></td>
                        <td>
                            <?php if ($user->aktif == 1): ?>
                                <span class="badge badge-success">Aktif</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Tidak Aktif</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>ID User</strong></td>
                        <td><?php echo $user->id_user; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Dibuat Pada</strong></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($user->created_at)); ?></td>
                    </tr>
                    <?php if ($user->last_login): ?>
                        <tr>
                            <td><strong>Login Terakhir</strong></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($user->last_login)); ?></td>
                        </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>
</div>