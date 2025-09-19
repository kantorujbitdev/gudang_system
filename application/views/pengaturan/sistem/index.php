<div class="card shadow mb-4">
    <div class="card-header py-3">
        <div class="row">
            <div class="col">
                <?php echo responsive_title_blue('Pengaturan Sistem') ?>
            </div>
            <div class="col text-right">
                <a href="<?php echo site_url('pengaturan/sistem/backup'); ?>" class="btn btn-primary btn-sm">
                    <i class="fas fa-database"></i> Backup Database
                </a>
                <a href="<?php echo site_url('pengaturan/sistem/log'); ?>" class="btn btn-info btn-sm">
                    <i class="fas fa-file-alt"></i> Log Sistem
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

        <?php echo form_open('pengaturan/sistem/update'); ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="25%">Key</th>
                        <th width="50%">Value</th>
                        <th width="20%">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;
                    foreach ($pengaturan as $row): ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td>
                                <input type="hidden" name="key[]" value="<?php echo $row->key; ?>">
                                <?php echo $row->key; ?>
                            </td>
                            <td>
                                <?php if ($can_edit): ?>
                                    <?php if ($row->key == 'app_name'): ?>
                                        <input type="text" class="form-control" name="value[]" value="<?php echo $row->value; ?>">
                                    <?php elseif ($row->key == 'app_version'): ?>
                                        <input type="text" class="form-control" name="value[]" value="<?php echo $row->value; ?>">
                                    <?php elseif ($row->key == 'app_logo'): ?>
                                        <input type="text" class="form-control" name="value[]" value="<?php echo $row->value; ?>">
                                    <?php elseif ($row->key == 'app_email'): ?>
                                        <input type="email" class="form-control" name="value[]" value="<?php echo $row->value; ?>">
                                    <?php elseif ($row->key == 'app_phone'): ?>
                                        <input type="text" class="form-control" name="value[]" value="<?php echo $row->value; ?>">
                                    <?php elseif ($row->key == 'app_address'): ?>
                                        <textarea class="form-control" name="value[]" rows="2"><?php echo $row->value; ?></textarea>
                                    <?php else: ?>
                                        <input type="text" class="form-control" name="value[]" value="<?php echo $row->value; ?>">
                                    <?php endif; ?>
                                <?php else: ?>
                                    <?php echo $row->value; ?>
                                <?php endif; ?>
                            </td>
                            <td><?php echo $row->keterangan; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if ($can_edit): ?>
            <div class="form-group mt-3">
                <button type="submit" class="btn btn-primary">Simpan Pengaturan</button>
            </div>
        <?php endif; ?>

        <?php echo form_close(); ?>
    </div>
</div>