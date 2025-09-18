<div class="form-group text-left mt-4">
    <?php echo back_button('setup/pelanggan'); ?>
</div>
<div class="card shadow mb-4">
    <div class="card-header bg-primary text-white d-flex align-items-center">
        <?php echo responsive_title(isset($pelanggan) ? 'Edit Pelanggan' : 'Tambah Pelanggan') ?>
    </div>
    <div class="card-body px-4 py-4">
        <?php echo form_open(isset($pelanggan) ? 'setup/pelanggan/edit/' . $pelanggan->id_pelanggan : 'setup/pelanggan/tambah'); ?>
        <div class="form-group">
            <?php echo form_label('Nama Pelanggan <span class="text-danger">*</span>', 'nama_pelanggan'); ?>
            <?php echo form_input(array(
                'name' => 'nama_pelanggan',
                'id' => 'nama_pelanggan',
                'class' => 'form-control',
                'value' => set_value('nama_pelanggan', isset($pelanggan) ? $pelanggan->nama_pelanggan : ''),
                'required' => '',
                'maxlength' => '100'
            )); ?>
            <?php echo form_error('nama_pelanggan', '<small class="text-danger">', '</small>'); ?>
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
                    set_value('id_perusahaan', isset($pelanggan) ? $pelanggan->id_perusahaan : ''),
                    array('class' => 'form-control', 'required' => '')
                );
                ?>
                <?php echo form_error('id_perusahaan', '<small class="text-danger">', '</small>'); ?>
            </div>
        <?php endif; ?>

        <div class="form-group">
            <?php echo form_label('Alamat', 'alamat'); ?>
            <?php echo form_textarea(array(
                'name' => 'alamat',
                'id' => 'alamat',
                'class' => 'form-control',
                'rows' => 3,
                'value' => set_value('alamat', isset($pelanggan) ? $pelanggan->alamat : '')
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
                        'value' => set_value('telepon', isset($pelanggan) ? $pelanggan->telepon : ''),
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
                        'value' => set_value('email', isset($pelanggan) ? $pelanggan->email : ''),
                        'maxlength' => '100'
                    )); ?>
                    <?php echo form_error('email', '<small class="text-danger">', '</small>'); ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <?php echo form_label('Tipe Pelanggan <span class="text-danger">*</span>', 'tipe_pelanggan'); ?>
            <div class="form-check">
                <?php echo form_radio(array(
                    'name' => 'tipe_pelanggan',
                    'id' => 'tipe_pelanggan_distributor',
                    'value' => 'distributor',
                    'checked' => set_value('tipe_pelanggan', isset($pelanggan) ? $pelanggan->tipe_pelanggan : 'konsumen') == 'distributor' ? TRUE : FALSE
                )); ?>
                <?php echo form_label('Distributor', 'tipe_pelanggan_distributor', array('class' => 'form-check-label')); ?>
            </div>
            <div class="form-check">
                <?php echo form_radio(array(
                    'name' => 'tipe_pelanggan',
                    'id' => 'tipe_pelanggan_konsumen',
                    'value' => 'konsumen',
                    'checked' => set_value('tipe_pelanggan', isset($pelanggan) ? $pelanggan->tipe_pelanggan : 'konsumen') == 'konsumen' ? TRUE : FALSE
                )); ?>
                <?php echo form_label('Konsumen', 'tipe_pelanggan_konsumen', array('class' => 'form-check-label')); ?>
            </div>
            <?php echo form_error('tipe_pelanggan', '<small class="text-danger">', '</small>'); ?>
        </div>

        <?php if (isset($pelanggan)): ?>
            <div class="form-group">
                <?php echo form_label('Status', 'status_aktif'); ?>
                <div class="form-check">
                    <?php echo form_radio(array(
                        'name' => 'status_aktif',
                        'id' => 'status_aktif_1',
                        'value' => 1,
                        'checked' => set_value('status_aktif', $pelanggan->status_aktif) == 1 ? TRUE : FALSE
                    )); ?>
                    <?php echo form_label('Aktif', 'status_aktif_1', array('class' => 'form-check-label')); ?>
                </div>
                <div class="form-check">
                    <?php echo form_radio(array(
                        'name' => 'status_aktif',
                        'id' => 'status_aktif_0',
                        'value' => 0,
                        'checked' => set_value('status_aktif', $pelanggan->status_aktif) == 0 ? TRUE : FALSE
                    )); ?>
                    <?php echo form_label('Tidak Aktif', 'status_aktif_0', array('class' => 'form-check-label')); ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="form-group">
            <?php echo form_submit('submit', 'Simpan', array('class' => 'btn btn-primary')); ?>
            <a href="<?php echo site_url('setup/pelanggan'); ?>" class="btn btn-secondary">Batal</a>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>