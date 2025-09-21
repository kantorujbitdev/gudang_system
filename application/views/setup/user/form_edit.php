<div class="form-group text-left mt-4">
    <?php
    $back_url = 'setup/user';
    if (isset($user)) {
        if ($user->id_role == 3)
            $back_url = 'setup/user/sales';
        elseif ($user->id_role == 4)
            $back_url = 'setup/user/packing';
        elseif ($user->id_role == 5)
            $back_url = 'setup/user/retur';
    }
    echo back_button($back_url);
    ?>
</div>

<div class="card shadow mb-4">
    <div class="card-header bg-primary text-white d-flex align-items-center">
        <?php echo responsive_title('Edit User') ?>
    </div>
    <div class="card-body px-4 py-4">
        <?php echo form_open('setup/user/edit/' . $user->id_user); ?>

        <div class="form-group">
            <?php echo form_label('Nama Lengkap <span class="text-danger">*</span>', 'nama'); ?>
            <?php echo form_input(array(
                'name' => 'nama',
                'id' => 'nama',
                'class' => 'form-control',
                'value' => set_value('nama', $user->nama),
                'required' => '',
                'maxlength' => '100'
            )); ?>
            <?php echo form_error('nama', '<small class="text-danger">', '</small>'); ?>
        </div>

        <div class="form-group">
            <?php echo form_label('Username <span class="text-danger">*</span>', 'username'); ?>
            <?php echo form_input(array(
                'name' => 'username',
                'id' => 'username',
                'class' => 'form-control',
                'value' => set_value('username', $user->username),
                'required' => '',
                'maxlength' => '50'
            )); ?>
            <?php echo form_error('username', '<small class="text-danger">', '</small>'); ?>
        </div>

        <div class="form-group">
            <?php echo form_label('Email <span class="text-danger">*</span>', 'email'); ?>
            <?php echo form_input(array(
                'name' => 'email',
                'id' => 'email',
                'class' => 'form-control',
                'value' => set_value('email', $user->email),
                'required' => '',
                'maxlength' => '100'
            )); ?>
            <?php echo form_error('email', '<small class="text-danger">', '</small>'); ?>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <?php echo form_label('Password', 'password'); ?>
                    <?php echo form_password(array(
                        'name' => 'password',
                        'id' => 'password',
                        'class' => 'form-control',
                        'placeholder' => 'Kosongkan jika tidak ingin mengubah password'
                    )); ?>
                    <?php echo form_error('password', '<small class="text-danger">', '</small>'); ?>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <?php echo form_label('Konfirmasi Password', 'confirm_password'); ?>
                    <?php echo form_password(array(
                        'name' => 'confirm_password',
                        'id' => 'confirm_password',
                        'class' => 'form-control',
                        'placeholder' => 'Kosongkan jika tidak ingin mengubah password'
                    )); ?>
                    <?php echo form_error('confirm_password', '<small class="text-danger">', '</small>'); ?>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <?php echo form_label('Telepon', 'telepon'); ?>
                    <?php echo form_input(array(
                        'name' => 'telepon',
                        'id' => 'telepon',
                        'class' => 'form-control',
                        'value' => set_value('telepon', $user->telepon),
                        'maxlength' => '20'
                    )); ?>
                    <?php echo form_error('telepon', '<small class="text-danger">', '</small>'); ?>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <?php echo form_label('Role <span class="text-danger">*</span>', 'id_role'); ?>
                    <?php
                    $role_options = array();
                    if ($this->session->userdata('id_role') == 1) { // Super Admin
                        $role_options = array(
                            2 => 'Admin Perusahaan',
                            3 => 'Sales Online',
                            4 => 'Admin Packing',
                            5 => 'Admin Return'
                        );
                    } else { // Admin Perusahaan
                        $role_options = array(
                            3 => 'Sales Online',
                            4 => 'Admin Packing',
                            5 => 'Admin Return'
                        );
                    }

                    echo form_dropdown('id_role', $role_options, set_value('id_role', $user->id_role), 'class="form-control" id="id_role" required');
                    ?>
                    <?php echo form_error('id_role', '<small class="text-danger">', '</small>'); ?>
                </div>
            </div>
        </div>

        <?php if ($this->session->userdata('id_role') == 1): // Super Admin ?>
            <div class="form-group">
                <?php echo form_label('Perusahaan <span class="text-danger">*</span>', 'id_perusahaan'); ?>
                <?php
                $perusahaan_options = array('' => 'Pilih Perusahaan');
                foreach ($perusahaan as $p) {
                    $perusahaan_options[$p->id_perusahaan] = $p->nama_perusahaan;
                }
                echo form_dropdown('id_perusahaan', $perusahaan_options, set_value('id_perusahaan', $user->id_perusahaan), 'class="form-control" id="id_perusahaan" required');
                ?>
                <?php echo form_error('id_perusahaan', '<small class="text-danger">', '</small>'); ?>
            </div>
        <?php else: ?>
            <div class="form-group">
                <?php echo form_label('Perusahaan', 'id_perusahaan'); ?>
                <?php echo form_input(array(
                    'name' => 'nama_perusahaan',
                    'id' => 'nama_perusahaan',
                    'class' => 'form-control',
                    'value' => $user->nama_perusahaan,
                    'readonly' => 'true'
                )); ?>
                <?php echo form_hidden('id_perusahaan', $user->id_perusahaan); ?>
            </div>
        <?php endif; ?>

        <div class="form-group">
            <?php echo form_submit('submit', 'Simpan', array('class' => 'btn btn-primary')); ?>
            <a href="<?php echo site_url($back_url); ?>" class="btn btn-secondary">Batal</a>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>