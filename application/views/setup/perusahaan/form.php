<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h4 class="m-0 font-weight-bold text-primary"><?php echo isset($perusahaan) ? 'Edit' : 'Tambah'; ?> Perusahaan</h4>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h1 class="h5 mb-0 text-gray-800">Form Perusahaan</h1>
    </div>
    <div class="card-body">
        <?php echo form_open(isset($perusahaan) ? 'setup/perusahaan/edit/' . $perusahaan->id_perusahaan : 'setup/perusahaan/tambah'); ?>
        <div class="form-group">
            <?php echo form_label('Nama Perusahaan <span class="text-danger">*</span>', 'nama_perusahaan'); ?>
            <?php echo form_input(array(
                'name' => 'nama_perusahaan',
                'id' => 'nama_perusahaan',
                'class' => 'form-control',
                'value' => set_value('nama_perusahaan', isset($perusahaan) ? $perusahaan->nama_perusahaan : ''),
                'required' => '',
                'maxlength' => '255'
            )); ?>
            <?php echo form_error('nama_perusahaan', '<small class="text-danger">', '</small>'); ?>
        </div>

        <div class="form-group">
            <?php echo form_label('Alamat', 'alamat'); ?>
            <?php echo form_textarea(array(
                'name' => 'alamat',
                'id' => 'alamat',
                'class' => 'form-control',
                'rows' => 3,
                'value' => set_value('alamat', isset($perusahaan) ? $perusahaan->alamat : '')
            )); ?>
            <?php echo form_error('alamat', '<small class="text-danger">', '</small>'); ?>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <?php echo form_label('Telepon', 'telepon'); ?>
                    <?php echo form_input(array(
                        'name' => 'telepon',
                        'id' => 'telepon',
                        'class' => 'form-control',
                        'value' => set_value('telepon', isset($perusahaan) ? $perusahaan->telepon : ''),
                        'maxlength' => '20'
                    )); ?>
                    <?php echo form_error('telepon', '<small class="text-danger">', '</small>'); ?>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <?php echo form_label('Email', 'email'); ?>
                    <?php echo form_input(array(
                        'name' => 'email',
                        'id' => 'email',
                        'class' => 'form-control',
                        'value' => set_value('email', isset($perusahaan) ? $perusahaan->email : ''),
                        'maxlength' => '100'
                    )); ?>
                    <?php echo form_error('email', '<small class="text-danger">', '</small>'); ?>
                </div>
            </div>
        </div>

        <?php if (isset($perusahaan)): ?>
            <div class="form-group">
                <?php echo form_label('Status', 'status_aktif'); ?>
                <div class="form-check">
                    <?php echo form_radio(array(
                        'name' => 'status_aktif',
                        'id' => 'status_aktif_1',
                        'value' => 1,
                        'checked' => set_value('status_aktif', $perusahaan->status_aktif) == 1 ? TRUE : FALSE
                    )); ?>
                    <?php echo form_label('Aktif', 'status_aktif_1', array('class' => 'form-check-label')); ?>
                </div>
                <div class="form-check">
                    <?php echo form_radio(array(
                        'name' => 'status_aktif',
                        'id' => 'status_aktif_0',
                        'value' => 0,
                        'checked' => set_value('status_aktif', $perusahaan->status_aktif) == 0 ? TRUE : FALSE
                    )); ?>
                    <?php echo form_label('Tidak Aktif', 'status_aktif_0', array('class' => 'form-check-label')); ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="form-group">
            <?php echo form_submit('submit', 'Simpan', array('class' => 'btn btn-primary')); ?>
            <a href="<?php echo site_url('setup/perusahaan'); ?>" class="btn btn-secondary">Batal</a>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>