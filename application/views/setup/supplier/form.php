<div class="form-group text-left mt-4">
    <?php echo back_button('setup/supplier'); ?>
</div>
<div class="card shadow mb-4">
    <div class="card-header bg-primary text-white d-flex align-items-center">
        <?php echo responsive_title(isset($supplier) ? 'Edit Supplier' : 'Tambah Supplier') ?>
    </div>
    <div class="card-body px-4 py-4">
        <?php echo form_open(isset($supplier) ? 'setup/supplier/edit/' . $supplier->id_supplier : 'setup/supplier/tambah'); ?>
        <div class="form-group">
            <?php echo form_label('Nama Supplier <span class="text-danger">*</span>', 'nama_supplier'); ?>
            <?php echo form_input(array(
                'name' => 'nama_supplier',
                'id' => 'nama_supplier',
                'class' => 'form-control',
                'value' => set_value('nama_supplier', isset($supplier) ? $supplier->nama_supplier : ''),
                'required' => '',
                'maxlength' => '100'
            )); ?>
            <?php echo form_error('nama_supplier', '<small class="text-danger">', '</small>'); ?>
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
                    set_value('id_perusahaan', isset($supplier) ? $supplier->id_perusahaan : ''),
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
                'value' => set_value('alamat', isset($supplier) ? $supplier->alamat : '')
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
                        'value' => set_value('telepon', isset($supplier) ? $supplier->telepon : ''),
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
                        'value' => set_value('email', isset($supplier) ? $supplier->email : ''),
                        'maxlength' => '100'
                    )); ?>
                    <?php echo form_error('email', '<small class="text-danger">', '</small>'); ?>
                </div>
            </div>
        </div>

        <?php if (isset($supplier)): ?>
            <div class="form-group">
                <?php echo form_label('Status', 'status_aktif'); ?>
                <div class="form-check">
                    <?php echo form_radio(array(
                        'name' => 'status_aktif',
                        'id' => 'status_aktif_1',
                        'value' => 1,
                        'checked' => set_value('status_aktif', $supplier->status_aktif) == 1 ? TRUE : FALSE
                    )); ?>
                    <?php echo form_label('Aktif', 'status_aktif_1', array('class' => 'form-check-label')); ?>
                </div>
                <div class="form-check">
                    <?php echo form_radio(array(
                        'name' => 'status_aktif',
                        'id' => 'status_aktif_0',
                        'value' => 0,
                        'checked' => set_value('status_aktif', $supplier->status_aktif) == 0 ? TRUE : FALSE
                    )); ?>
                    <?php echo form_label('Tidak Aktif', 'status_aktif_0', array('class' => 'form-check-label')); ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="form-group">
            <?php echo form_submit('submit', 'Simpan', array('class' => 'btn btn-primary')); ?>
            <a href="<?php echo site_url('setup/supplier'); ?>" class="btn btn-secondary">Batal</a>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>