<div class="card shadow mb-4">
    <div class="card-header py-3">
        <div class="row">
            <div class="col">
                <?php echo responsive_title_blue('Hak Akses Menu') ?>
            </div>
            <div class="col text-right">
                <a href="<?php echo site_url('pengaturan/hak_akses/role'); ?>" class="btn btn-primary btn-sm">
                    <i class="fas fa-user-tag"></i> Manajemen Role
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <?php if ($this->session->flashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $this->session->flashdata('success'); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <?php if ($this->session->flashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $this->session->flashdata('error'); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <ul class="nav nav-tabs" id="roleTabs" role="tablist">
            <?php $first = true;
            foreach ($roles as $role): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo $first ? 'active' : ''; ?>" id="role-<?php echo $role->id_role; ?>-tab"
                        data-toggle="tab" href="#role-<?php echo $role->id_role; ?>" role="tab"
                        aria-controls="role-<?php echo $role->id_role; ?>"
                        aria-selected="<?php echo $first ? 'true' : 'false'; ?>">
                        <?php echo $role->nama_role; ?>
                    </a>
                </li>
                <?php $first = false; endforeach; ?>
        </ul>

        <div class="tab-content" id="roleTabContent">
            <?php $first = true;
            foreach ($roles as $role): ?>
                <div class="tab-pane fade <?php echo $first ? 'show active' : ''; ?>"
                    id="role-<?php echo $role->id_role; ?>" role="tabpanel"
                    aria-labelledby="role-<?php echo $role->id_role; ?>-tab">
                    <?php if ($can_edit): ?>
                        <?php echo form_open('pengaturan/hak_akses/update'); ?>
                        <input type="hidden" name="id_role" value="<?php echo $role->id_role; ?>">

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="40%">Menu</th>
                                        <th width="10%">View</th>
                                        <th width="10%">Create</th>
                                        <th width="10%">Edit</th>
                                        <th width="10%">Delete</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1;
                                    $current_hak_akses = [];
                                    foreach ($hak_akses as $ha) {
                                        if ($ha->id_role == $role->id_role) {
                                            $current_hak_akses[$ha->id_menu] = $ha;
                                        }
                                    }
                                    ?>

                                    <?php foreach ($menus as $menu): ?>
                                        <tr>
                                            <td><?php echo $no++; ?></td>
                                            <td>
                                                <?php if ($menu->id_parent): ?>
                                                    &nbsp;&nbsp;&nbsp;&nbsp;
                                                <?php endif; ?>
                                                <?php echo $menu->nama_menu; ?>
                                                <input type="hidden" name="id_menu[]" value="<?php echo $menu->id_menu; ?>">
                                            </td>
                                            <td class="text-center">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input"
                                                        id="view-<?php echo $menu->id_menu; ?>-<?php echo $role->id_role; ?>"
                                                        name="can_view[]" value="<?php echo $menu->id_menu; ?>" <?php echo (isset($current_hak_akses[$menu->id_menu]) && $current_hak_akses[$menu->id_menu]->can_view) ? 'checked' : ''; ?>>
                                                    <label class="custom-control-label"
                                                        for="view-<?php echo $menu->id_menu; ?>-<?php echo $role->id_role; ?>"></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input"
                                                        id="create-<?php echo $menu->id_menu; ?>-<?php echo $role->id_role; ?>"
                                                        name="can_create[]" value="<?php echo $menu->id_menu; ?>" <?php echo (isset($current_hak_akses[$menu->id_menu]) && $current_hak_akses[$menu->id_menu]->can_create) ? 'checked' : ''; ?>>
                                                    <label class="custom-control-label"
                                                        for="create-<?php echo $menu->id_menu; ?>-<?php echo $role->id_role; ?>"></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input"
                                                        id="edit-<?php echo $menu->id_menu; ?>-<?php echo $role->id_role; ?>"
                                                        name="can_edit[]" value="<?php echo $menu->id_menu; ?>" <?php echo (isset($current_hak_akses[$menu->id_menu]) && $current_hak_akses[$menu->id_menu]->can_edit) ? 'checked' : ''; ?>>
                                                    <label class="custom-control-label"
                                                        for="edit-<?php echo $menu->id_menu; ?>-<?php echo $role->id_role; ?>"></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input"
                                                        id="delete-<?php echo $menu->id_menu; ?>-<?php echo $role->id_role; ?>"
                                                        name="can_delete[]" value="<?php echo $menu->id_menu; ?>" <?php echo (isset($current_hak_akses[$menu->id_menu]) && $current_hak_akses[$menu->id_menu]->can_delete) ? 'checked' : ''; ?>>
                                                    <label class="custom-control-label"
                                                        for="delete-<?php echo $menu->id_menu; ?>-<?php echo $role->id_role; ?>"></label>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="form-group mt-3">
                            <button type="submit" class="btn btn-primary">Simpan Hak Akses</button>
                        </div>

                        <?php echo form_close(); ?>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="40%">Menu</th>
                                        <th width="10%">View</th>
                                        <th width="10%">Create</th>
                                        <th width="10%">Edit</th>
                                        <th width="10%">Delete</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1;
                                    $current_hak_akses = [];
                                    foreach ($hak_akses as $ha) {
                                        if ($ha->id_role == $role->id_role) {
                                            $current_hak_akses[$ha->id_menu] = $ha;
                                        }
                                    }
                                    ?>

                                    <?php foreach ($menus as $menu): ?>
                                        <tr>
                                            <td><?php echo $no++; ?></td>
                                            <td>
                                                <?php if ($menu->id_parent): ?>
                                                    &nbsp;&nbsp;&nbsp;&nbsp;
                                                <?php endif; ?>
                                                <?php echo $menu->nama_menu; ?>
                                            </td>
                                            <td class="text-center">
                                                <?php echo (isset($current_hak_akses[$menu->id_menu]) && $current_hak_akses[$menu->id_menu]->can_view) ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>'; ?>
                                            </td>
                                            <td class="text-center">
                                                <?php echo (isset($current_hak_akses[$menu->id_menu]) && $current_hak_akses[$menu->id_menu]->can_create) ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>'; ?>
                                            </td>
                                            <td class="text-center">
                                                <?php echo (isset($current_hak_akses[$menu->id_menu]) && $current_hak_akses[$menu->id_menu]->can_edit) ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>'; ?>
                                            </td>
                                            <td class="text-center">
                                                <?php echo (isset($current_hak_akses[$menu->id_menu]) && $current_hak_akses[$menu->id_menu]->can_delete) ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>'; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
                <?php $first = false; endforeach; ?>
        </div>
    </div>
</div>