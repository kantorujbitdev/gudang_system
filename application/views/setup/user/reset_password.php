<div class="form-group text-left mt-4">
    <?php echo back_button('setup/user'); ?>
</div>
<div class="card shadow mb-4">
    <div class="card-header bg-primary text-white d-flex align-items-center">
        <?php echo responsive_title('Reset Password') ?>
    </div>
    <div class="card-body px-4 py-4">
        <?php echo form_open('setup/user/reset_password/' . $user->id_user); ?>
        <?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>

        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> Anda akan mereset password untuk user
            <strong><?php echo $user->nama; ?></strong> (<?php echo $user->username; ?>)
        </div>

        <div class="form-group">
            <?php echo form_label('Password Baru <span class="text-danger">*</span>', 'new_password'); ?>
            <?php echo form_password(array(
                'name' => 'new_password',
                'id' => 'new_password',
                'class' => 'form-control',
                'required' => '',
                'minlength' => '6'
            )); ?>
            <?php echo form_error('new_password', '<small class="text-danger">', '</small>'); ?>
        </div>

        <div class="form-group">
            <?php echo form_label('Konfirmasi Password Baru <span class="text-danger">*</span>', 'confirm_password'); ?>
            <?php echo form_password(array(
                'name' => 'confirm_password',
                'id' => 'confirm_password',
                'class' => 'form-control',
                'required' => '',
                'minlength' => '6'
            )); ?>
            <?php echo form_error('confirm_password', '<small class="text-danger">', '</small>'); ?>
        </div>

        <div class="form-group">
            <?php echo form_submit('submit', 'Reset Password', array('class' => 'btn btn-primary')); ?>
            <a href="<?php echo site_url('setup/user'); ?>" class="btn btn-secondary">Batal</a>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>