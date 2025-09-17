<?php if ($this->session->flashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo $this->session->flashdata('success'); ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<?php if ($this->session->flashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php echo $this->session->flashdata('error'); ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <div class="row">
            <div class="col">
                <?php echo responsive_title_blue('Daftar Kategori') ?>
            </div>
            <div class="col text-right">
                <a href="<?php echo site_url('setup/kategori/tambah') ?>" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus">
                    </i>
                    Tambah Kategori
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Kategori</th>
                        <th>Deskripsi</th>
                        <?php if ($this->session->userdata('id_role') == 1): ?>
                            <th>Perusahaan</th>
                        <?php endif; ?>
                        <th>Total Barang</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;
                    foreach ($kategori as $row): ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo $row->nama_kategori; ?></td>
                            <td><?php echo $row->deskripsi ?: '-'; ?></td>
                            <?php if ($this->session->userdata('id_role') == 1): ?>
                                <td><?php echo $row->nama_perusahaan ?? '-'; ?></td>
                            <?php endif; ?>
                            <td><?php echo $row->total_barang; ?></td>
                            <td>
                                <?php if ($row->status_aktif == 1): ?>
                                    <span class="badge badge-success">Aktif</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Tidak Aktif</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="<?php echo site_url('setup/kategori/detail/' . $row->id_kategori); ?>"
                                        class="btn btn-sm btn-info" title="Detail">
                                        <i class="fas fa-info-circle"></i>
                                    </a>
                                    <a href="<?php echo site_url('setup/kategori/edit/' . $row->id_kategori); ?>"
                                        class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="<?php echo site_url('setup/kategori/hapus/' . $row->id_kategori); ?>"
                                        class="btn btn-sm btn-danger" title="Hapus"
                                        onclick="return confirm('Apakah Anda yakin ingin menghapus kategori ini?');">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>