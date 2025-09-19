<div class="card shadow mb-4">
    <div class="card-header py-3">
        <div class="row">
            <div class="col">
                <?php echo responsive_title_blue('Manajemen User') ?>
            </div>
            <div class="col text-right">
                <?php if ($can_create): ?>
                    <div class="dropdown d-inline">
                        <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-plus"></i> Tambah User
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="<?php echo site_url('setup/user/tambah/3'); ?>">
                                <i class="fas fa-user-tag"></i> Sales Online
                            </a>
                            <a class="dropdown-item" href="<?php echo site_url('setup/user/tambah/4'); ?>">
                                <i class="fas fa-user-box"></i> Admin Packing
                            </a>
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
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;
                    foreach ($users as $row): ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo $row->nama; ?></td>
                            <td><?php echo $row->username; ?></td>
                            <td>
                                <?php
                                $role_class = '';
                                switch ($row->id_role) {
                                    case 1:
                                        $role_class = 'badge-danger';
                                        break;
                                    case 2:
                                        $role_class = 'badge-primary';
                                        break;
                                    case 3:
                                        $role_class = 'badge-success';
                                        break;
                                    case 4:
                                        $role_class = 'badge-info';
                                        break;
                                    case 5:
                                        $role_class = 'badge-warning';
                                        break;
                                }
                                ?>
                                <span class="badge <?php echo $role_class; ?>"><?php echo $row->nama_role; ?></span>
                            </td>
                            <td><?php echo $row->nama_perusahaan ?: '-'; ?></td>
                            <td><?php echo $row->email; ?></td>
                            <td>
                                <?php if ($row->aktif == 1): ?>
                                    <span class="badge badge-success">Aktif</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Tidak Aktif</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($can_edit): ?>
                                    <a href="<?php echo site_url('setup/user/edit/' . $row->id_user); ?>"
                                        class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                <?php endif; ?>

                                <?php if ($row->id_user != $this->session->userdata('id_user')): ?>
                                    <?php if ($row->aktif == 1): ?>
                                        <?php if ($can_edit): ?>
                                            <a href="<?php echo site_url('setup/user/nonaktif/' . $row->id_user); ?>"
                                                class="btn btn-sm btn-danger" title="Nonaktifkan"
                                                onclick="return confirm('Apakah Anda yakin ingin menonaktifkan user ini?')">
                                                <i class="fas fa-user-slash"></i>
                                            </a>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <?php if ($can_edit): ?>
                                            <a href="<?php echo site_url('setup/user/aktif/' . $row->id_user); ?>"
                                                class="btn btn-sm btn-success" title="Aktifkan"
                                                onclick="return confirm('Apakah Anda yakin ingin mengaktifkan user ini?')">
                                                <i class="fas fa-user-check"></i>
                                            </a>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <?php if ($can_delete): ?>
                                        <a href="<?php echo site_url('setup/user/hapus/' . $row->id_user); ?>"
                                            class="btn btn-sm btn-danger" title="Hapus"
                                            onclick="return confirm('Apakah Anda yakin ingin menghapus user ini?')">
                                            <i class="fas fa-trash"></i>
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