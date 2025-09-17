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
                <?php echo responsive_title_blue('Daftar Perusahaan') ?>
            </div>
            <div class="col text-right">
                <a href="<?php echo site_url('setup/perusahaan/tambah') ?>" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus">
                    </i>
                    Tambah Perusahaan
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
                        <th>Nama Perusahaan</th>
                        <th>Alamat</th>
                        <th>Telepon</th>
                        <th>Email</th>
                        <th>Total User</th>
                        <th>Total Gudang</th>
                        <th>Total Barang</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;
                    foreach ($perusahaan as $row): ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo $row->nama_perusahaan; ?></td>
                            <td><?php echo $row->alamat ?: '-'; ?></td>
                            <td><?php echo $row->telepon ?: '-'; ?></td>
                            <td><?php echo $row->email ?: '-'; ?></td>
                            <td><?php echo $row->total_user; ?></td>
                            <td><?php echo $row->total_gudang; ?></td>
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
                                    <a href="<?php echo site_url('setup/perusahaan/detail/' . $row->id_perusahaan); ?>"
                                        class="btn btn-sm btn-info" title="Detail">
                                        <i class="fas fa-info-circle"></i>
                                    </a>
                                    <a href="<?php echo site_url('setup/perusahaan/edit/' . $row->id_perusahaan); ?>"
                                        class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="<?php echo site_url('setup/perusahaan/hapus/' . $row->id_perusahaan); ?>"
                                        class="btn btn-sm btn-danger" title="Hapus"
                                        onclick="return confirm('Apakah Anda yakin ingin menghapus perusahaan ini?');">
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