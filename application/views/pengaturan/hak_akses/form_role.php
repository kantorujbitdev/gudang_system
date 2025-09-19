<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary"><?php echo $title; ?></h6>
    </div>
    <div class="card-body">
        <?php echo form_open(current_url()); ?>
        <div class="form-group">
            <label for="nama_role">Nama Role <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="nama_role" name="nama_role"
                value="<?php echo set_value('nama_role', isset($role) ? $role->nama_role : ''); ?>" required>
            <?php echo form_error('nama_role', '<small class="text-danger">', '</small>'); ?>
        </div>

        <div class="form-group">
            <label for="deskripsi">Deskripsi</label>
            <textarea class="form-control" id="deskripsi" name="deskripsi"
                rows="3"><?php echo set_value('deskripsi', isset($role) ? $role->deskripsi : ''); ?></textarea>
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="<?php echo site_url('pengaturan/hak_akses/role'); ?>" class="btn btn-secondary">Batal</a>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>