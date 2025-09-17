<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h4 class="m-0 font-weight-bold text-primary"><?php echo isset($kategori) ? 'Edit' : 'Tambah'; ?> Kategori Barang
    </h4>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h1 class="h5 mb-0 text-gray-800">Form Kategori Barang</h1>
    </div>
    <div class="card-body">
        <?php echo form_open(isset($kategori) ? 'setup/kategori/edit/' . $kategori->id_kategori : 'setup/kategori/tambah'); ?>
        <div class="form-group">
            <?php echo form_label('Nama Kategori', 'nama_kategori'); ?>
            <?php echo form_input(array(
                'name' => 'nama_kategori',
                'id' => 'nama_kategori',
                'class' => 'form-control',
                'value' => set_value('nama_kategori', isset($kategori) ? $kategori->nama_kategori : ''),
                'required' => ''
            )); ?>
            <?php echo form_error('nama_kategori', '<small class="text-danger">', '</small>'); ?>
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

        <div class="form-group">
            <?php echo form_submit('submit', 'Simpan', array('class' => 'btn btn-primary')); ?>
            <a href="<?php echo site_url('setup/kategori'); ?>" class="btn btn-secondary">Batal</a>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>