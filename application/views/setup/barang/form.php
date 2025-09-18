<div class="form-group text-left mt-4">
    <?php echo back_button('setup/barang'); ?>
</div>
<div class="card shadow mb-4">
    <div class="card-header bg-primary text-white d-flex align-items-center">
        <?php echo responsive_title(isset($barang) ? 'Edit Barang' : 'Tambah Barang') ?>
    </div>
    <div class="card-body px-4 py-4">
        <?php echo form_open_multipart(isset($barang) ? 'setup/barang/edit/' . $barang->id_barang : 'setup/barang/tambah'); ?>
        <div class="form-group">
            <?php echo form_label('Nama Barang <span class="text-danger">*</span>', 'nama_barang'); ?>
            <?php echo form_input(array(
                'name' => 'nama_barang',
                'id' => 'nama_barang',
                'class' => 'form-control',
                'value' => set_value('nama_barang', isset($barang) ? $barang->nama_barang : ''),
                'required' => '',
                'maxlength' => '100'
            )); ?>
            <?php echo form_error('nama_barang', '<small class="text-danger">', '</small>'); ?>
        </div>

        <div class="form-group">
            <?php echo form_label('SKU <span class="text-danger">*</span>', 'sku'); ?>
            <?php echo form_input(array(
                'name' => 'sku',
                'id' => 'sku',
                'class' => 'form-control',
                'value' => set_value('sku', isset($barang) ? $barang->sku : ''),
                'required' => '',
                'maxlength' => '50'
            )); ?>
            <?php echo form_error('sku', '<small class="text-danger">', '</small>'); ?>
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
                    set_value('id_perusahaan', isset($barang) ? $barang->id_perusahaan : ''),
                    array('class' => 'form-control', 'required' => '')
                );
                ?>
                <?php echo form_error('id_perusahaan', '<small class="text-danger">', '</small>'); ?>
            </div>
        <?php endif; ?>

        <div class="form-group">
            <?php echo form_label('Kategori <span class="text-danger">*</span>', 'id_kategori'); ?>
            <?php
            $options = array();
            foreach ($kategori as $k) {
                $options[$k->id_kategori] = $k->nama_kategori;
            }

            echo form_dropdown(
                'id_kategori',
                $options,
                set_value('id_kategori', isset($barang) ? $barang->id_kategori : ''),
                array('class' => 'form-control', 'required' => '')
            );
            ?>
            <?php echo form_error('id_kategori', '<small class="text-danger">', '</small>'); ?>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <?php echo form_label('Satuan <span class="text-danger">*</span>', 'satuan'); ?>
                    <?php echo form_input(array(
                        'name' => 'satuan',
                        'id' => 'satuan',
                        'class' => 'form-control',
                        'value' => set_value('satuan', isset($barang) ? $barang->satuan : ''),
                        'required' => '',
                        'maxlength' => '20'
                    )); ?>
                    <?php echo form_error('satuan', '<small class="text-danger">', '</small>'); ?>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <?php echo form_label('Harga Jual <span class="text-danger">*</span>', 'harga_jual'); ?>
                    <?php echo form_input(array(
                        'name' => 'harga_jual',
                        'id' => 'harga_jual',
                        'class' => 'form-control',
                        'type' => 'number',
                        'min' => '0',
                        'step' => '100',
                        'value' => set_value('harga_jual', isset($barang) ? $barang->harga_jual : ''),
                        'required' => ''
                    )); ?>
                    <?php echo form_error('harga_jual', '<small class="text-danger">', '</small>'); ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <?php echo form_label('Harga Beli Terakhir', 'harga_beli_terakhir'); ?>
            <?php echo form_input(array(
                'name' => 'harga_beli_terakhir',
                'id' => 'harga_beli_terakhir',
                'class' => 'form-control',
                'type' => 'number',
                'min' => '0',
                'step' => '100',
                'value' => set_value('harga_beli_terakhir', isset($barang) ? $barang->harga_beli_terakhir : '')
            )); ?>
            <?php echo form_error('harga_beli_terakhir', '<small class="text-danger">', '</small>'); ?>
        </div>

        <div class="form-group">
            <?php echo form_label('Gambar', 'gambar'); ?>
            <div class="custom-file">
                <?php echo form_upload(array(
                    'name' => 'gambar',
                    'id' => 'gambar',
                    'class' => 'custom-file-input',
                    'accept' => 'image/*'
                )); ?>
                <label class="custom-file-label" for="gambar">Pilih gambar</label>
            </div>
            <?php if (isset($barang) && $barang->gambar): ?>
                <div class="mt-2">
                    <img src="<?php echo base_url('uploads/barang/' . $barang->gambar); ?>" class="img-thumbnail"
                        style="max-width: 150px;" alt="Gambar Barang">
                    <br>
                    <small class="text-muted">Gambar saat ini</small>
                </div>
            <?php endif; ?>
            <?php echo form_error('gambar', '<small class="text-danger">', '</small>'); ?>
        </div>

        <div class="form-group">
            <?php echo form_label('Deskripsi', 'deskripsi'); ?>
            <?php echo form_textarea(array(
                'name' => 'deskripsi',
                'id' => 'deskripsi',
                'class' => 'form-control',
                'rows' => 3,
                'value' => set_value('deskripsi', isset($barang) ? $barang->deskripsi : '')
            )); ?>
            <?php echo form_error('deskripsi', '<small class="text-danger">', '</small>'); ?>
        </div>

        <?php if (isset($barang)): ?>
            <div class="form-group">
                <?php echo form_label('Status', 'status_aktif'); ?>
                <div class="form-check">
                    <?php echo form_radio(array(
                        'name' => 'status_aktif',
                        'id' => 'status_aktif_1',
                        'value' => 1,
                        'checked' => set_value('status_aktif', $barang->status_aktif) == 1 ? TRUE : FALSE
                    )); ?>
                    <?php echo form_label('Aktif', 'status_aktif_1', array('class' => 'form-check-label')); ?>
                </div>
                <div class="form-check">
                    <?php echo form_radio(array(
                        'name' => 'status_aktif',
                        'id' => 'status_aktif_0',
                        'value' => 0,
                        'checked' => set_value('status_aktif', $barang->status_aktif) == 0 ? TRUE : FALSE
                    )); ?>
                    <?php echo form_label('Tidak Aktif', 'status_aktif_0', array('class' => 'form-check-label')); ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="form-group">
            <?php echo form_submit('submit', 'Simpan', array('class' => 'btn btn-primary')); ?>
            <a href="<?php echo site_url('setup/barang'); ?>" class="btn btn-secondary">Batal</a>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>

<script>
    // Custom file input label
    document.querySelector('.custom-file-input').addEventListener('change', function (e) {
        var fileName = document.getElementById("gambar").files[0].name;
        var nextSibling = e.target.nextElementSibling;
        nextSibling.innerText = fileName;
    });
</script>