<div class="form-group text-left mt-4">
    <?php echo back_button('setup/user/sales'); ?>
</div>
<div class="card shadow mb-4">
    <div class="card-header bg-primary text-white d-flex align-items-center">
        <?php echo responsive_title('Tambah Sales') ?>
    </div>
    <div class="card-body px-4 py-4">
        <?php echo form_open_multipart('setup/user/tambah_sales'); ?>
        <?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>

        <div class="form-group">
            <?php echo form_label('Nama Lengkap <span class="text-danger">*</span>', 'nama'); ?>
            <?php echo form_input(array(
                'name' => 'nama',
                'id' => 'nama',
                'class' => 'form-control',
                'value' => set_value('nama'),
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
                'value' => set_value('username'),
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
                'type' => 'email',
                'value' => set_value('email'),
                'required' => '',
                'maxlength' => '100'
            )); ?>
            <?php echo form_error('email', '<small class="text-danger">', '</small>'); ?>
        </div>

        <div class="form-group">
            <?php echo form_label('Telepon', 'telepon'); ?>
            <?php echo form_input(array(
                'name' => 'telepon',
                'id' => 'telepon',
                'class' => 'form-control',
                'value' => set_value('telepon'),
                'maxlength' => '20'
            )); ?>
            <?php echo form_error('telepon', '<small class="text-danger">', '</small>'); ?>
        </div>

        <?php if ($this->session->userdata('id_role') == 1): ?>
            <div class="form-group">
                <?php echo form_label('Perusahaan <span class="text-danger">*</span>', 'id_perusahaan'); ?>
                <?php
                $options = array();
                foreach ($perusahaan as $p) {
                    $options[$p->id_perusahaan] = $p->nama_perusahaan;
                }

                echo form_dropdown(
                    'id_perusahaan',
                    $options,
                    set_value('id_perusahaan'),
                    array('class' => 'form-control', 'required' => '')
                );
                ?>
                <?php echo form_error('id_perusahaan', '<small class="text-danger">', '</small>'); ?>
            </div>
        <?php endif; ?>

        <div class="form-group">
            <?php echo form_label('Password <span class="text-danger">*</span>', 'password'); ?>
            <?php echo form_password(array(
                'name' => 'password',
                'id' => 'password',
                'class' => 'form-control',
                'required' => '',
                'minlength' => '6'
            )); ?>
            <?php echo form_error('password', '<small class="text-danger">', '</small>'); ?>
        </div>

        <div class="form-group">
            <?php echo form_label('Konfirmasi Password <span class="text-danger">*</span>', 'confirm_password'); ?>
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
            <?php echo form_label('Foto Profil', 'foto_profil'); ?>
            <div class="custom-file">
                <?php echo form_upload(array(
                    'name' => 'foto_profil',
                    'id' => 'foto_profil',
                    'class' => 'custom-file-input',
                    'accept' => 'image/*'
                )); ?>
                <label class="custom-file-label" for="foto_profil">Pilih foto</label>
            </div>
            <?php echo form_error('foto_profil', '<small class="text-danger">', '</small>'); ?>
        </div>

        <div class="form-group">
            <?php echo form_submit('submit', 'Simpan', array('class' => 'btn btn-primary')); ?>
            <a href="<?php echo site_url('setup/user/sales'); ?>" class="btn btn-secondary">Batal</a>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>

<script>
    $(document).ready(function () {
        // Custom file input label
        $('.custom-file-input').on('change', function () {
            var fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').html(fileName);
        });
    });
</script>