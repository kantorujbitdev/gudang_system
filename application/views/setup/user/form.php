<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary"><?php echo $title; ?></h6>
    </div>
    <div class="card-body">
        <?php echo form_open(current_url()); ?>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="nama">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="nama" name="nama"
                        value="<?php echo set_value('nama'); ?>" required>
                    <?php echo form_error('nama', '<small class="text-danger">', '</small>'); ?>
                </div>

                <div class="form-group">
                    <label for="username">Username <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="username" name="username"
                        value="<?php echo set_value('username'); ?>" required>
                    <?php echo form_error('username', '<small class="text-danger">', '</small>'); ?>
                </div>

                <div class="form-group">
                    <label for="email">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" id="email" name="email"
                        value="<?php echo set_value('email'); ?>" required>
                    <?php echo form_error('email', '<small class="text-danger">', '</small>'); ?>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="id_role">Role <span class="text-danger">*</span></label>
                    <select class="form-control" id="id_role" name="id_role" required>
                        <option value="">-- Pilih Role --</option>
                        <?php
                        $roles = array(
                            3 => 'Sales Online',
                            4 => 'Admin Packing'
                        );
                        foreach ($roles as $id => $nama):
                            ?>
                            <option value="<?php echo $id; ?>" <?php echo ($role == $id) ? 'selected' : ''; ?>>
                                <?php echo $nama; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php echo form_error('id_role', '<small class="text-danger">', '</small>'); ?>
                </div>

                <div class="form-group">
                    <label for="id_perusahaan">Perusahaan <span class="text-danger">*</span></label>
                    <select class="form-control" id="id_perusahaan" name="id_perusahaan" required>
                        <option value="">-- Pilih Perusahaan --</option>
                        <?php foreach ($perusahaan as $row): ?>
                            <option value="<?php echo $row->id_perusahaan; ?>"><?php echo $row->nama_perusahaan; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php echo form_error('id_perusahaan', '<small class="text-danger">', '</small>'); ?>
                </div>

                <div class="form-group">
                    <label for="telepon">Telepon</label>
                    <input type="text" class="form-control" id="telepon" name="telepon"
                        value="<?php echo set_value('telepon'); ?>">
                    <?php echo form_error('telepon', '<small class="text-danger">', '</small>'); ?>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="password">Password <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" id="password" name="password" required>
                    <?php echo form_error('password', '<small class="text-danger">', '</small>'); ?>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="confirm_password">Konfirmasi Password <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    <?php echo form_error('confirm_password', '<small class="text-danger">', '</small>'); ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="<?php echo site_url('setup/user'); ?>" class="btn btn-secondary">Batal</a>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>