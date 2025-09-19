<div class="card shadow mb-4">
    <div class="card-header py-3">
        <div class="row">
            <div class="col">
                <h6 class="m-0 font-weight-bold text-primary"><?php echo $title; ?></h6>
            </div>
            <div class="col text-right">
                <a href="<?php echo site_url('pengaturan/stok_awal'); ?>" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $success_message; ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $error; ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <?php if (isset($error_messages)): ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong>Detail Error:</strong>
                <ul>
                    <?php foreach ($error_messages as $msg): ?>
                        <li><?php echo $msg; ?></li>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <div class="card card-info mb-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-info">Format File Import</h6>
            </div>
            <div class="card-body">
                <p>File import harus dalam format Excel (.xlsx, .xls) atau CSV dengan struktur sebagai berikut:</p>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Column A</th>
                                <th>Column B</th>
                                <th>Column C</th>
                                <th>Column D (Opsional)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>SKU Barang</td>
                                <td>Nama Gudang</td>
                                <td>Jumlah Stok</td>
                                <td>Keterangan</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <p class="mt-2"><strong>Catatan:</strong></p>
                <ul>
                    <li>Baris pertama adalah header dan tidak akan diproses</li>
                    <li>Pastikan SKU Barang dan Nama Gudang sudah ada di sistem</li>
                    <li>Stok awal untuk kombinasi barang dan gudang yang sudah ada akan dilewati</li>
                </ul>
            </div>
        </div>

        <?php echo form_open_multipart('pengaturan/stok_awal/import'); ?>
        <div class="form-group">
            <label for="file_import">File Import <span class="text-danger">*</span></label>
            <input type="file" class="form-control" id="file_import" name="file_import" accept=".xlsx,.xls,.csv"
                required>
            <?php echo form_error('file_import', '<small class="text-danger">', '</small>'); ?>
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-upload"></i> Upload dan Proses
            </button>
            <a href="<?php echo site_url('pengaturan/stok_awal'); ?>" class="btn btn-secondary">Batal</a>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>