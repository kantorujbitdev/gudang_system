<div class="form-group text-left mt-4">
    <?php echo back_button('setup/kategori'); ?>
</div>
<div class="card shadow mb-4">
    <div class="card-header bg-primary text-white d-flex align-items-center">
        <?php echo responsive_title(isset($kategori) ? 'Edit Kategori' : 'Tambah Kategori') ?>
    </div>
    <div class="card-body px-4 py-4">
        <?php echo form_open(isset($kategori) ? 'setup/kategori/edit/' . $kategori->id_kategori : 'setup/kategori/tambah'); ?>
        <div class="form-group">
            <?php echo form_label('Nama Kategori <span class="text-danger">*</span>', 'nama_kategori'); ?>
            <?php echo form_input(array(
                'name' => 'nama_kategori',
                'id' => 'nama_kategori',
                'class' => 'form-control',
                'value' => set_value('nama_kategori', isset($kategori) ? $kategori->nama_kategori : ''),
                'required' => '',
                'maxlength' => '50'
            )); ?>
            <?php echo form_error('nama_kategori', '<small class="text-danger">', '</small>'); ?>
        </div>

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
                set_value('id_perusahaan', isset($kategori) ? $kategori->id_perusahaan : ''),
                array('class' => 'form-control', 'required' => '')
            );
            ?>
            <?php echo form_error('id_perusahaan', '<small class="text-danger">', '</small>'); ?>
        </div>

        <div class="form-group">
            <?php echo form_label('Deskripsi', 'deskripsi'); ?>
            <?php echo form_textarea(array(
                'name' => 'deskripsi',
                'id' => 'deskripsi',
                'class' => 'form-control',
                'rows' => 3,
                'value' => set_value('deskripsi', isset($kategori) ? $kategori->deskripsi : '')
            )); ?>
            <?php echo form_error('deskripsi', '<small class="text-danger">', '</small>'); ?>
        </div>

        <?php if (isset($kategori)): ?>
            <div class="form-group">
                <?php echo form_label('Status', 'status_aktif'); ?>
                <div class="form-check">
                    <?php echo form_radio(array(
                        'name' => 'status_aktif',
                        'id' => 'status_aktif_1',
                        'value' => 1,
                        'checked' => set_value('status_aktif', $kategori->status_aktif) == 1 ? TRUE : FALSE
                    )); ?>
                    <?php echo form_label('Aktif', 'status_aktif_1', array('class' => 'form-check-label')); ?>
                </div>
                <div class="form-check">
                    <?php echo form_radio(array(
                        'name' => 'status_aktif',
                        'id' => 'status_aktif_0',
                        'value' => 0,
                        'checked' => set_value('status_aktif', $kategori->status_aktif) == 0 ? TRUE : FALSE
                    )); ?>
                    <?php echo form_label('Tidak Aktif', 'status_aktif_0', array('class' => 'form-check-label')); ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="form-group">
            <?php echo form_submit('submit', 'Simpan', array('class' => 'btn btn-primary')); ?>
            <a href="<?php echo site_url('setup/kategori'); ?>" class="btn btn-secondary">Batal</a>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>