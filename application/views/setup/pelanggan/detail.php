<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h4 class="m-0 font-weight-bold text-primary">Detail Pelanggan</h4>
    <div>
        <?php if ($this->auth->has_permission('setup/pelanggan', 'edit')): ?>
            <a href="<?php echo site_url('setup/pelanggan/edit/' . $pelanggan->id_pelanggan); ?>"
                class="btn btn-sm btn-warning" title="Edit">
                <i class="fas fa-edit"></i> Edit
            </a>
        <?php endif; ?>
        <a href="<?php echo site_url('setup/pelanggan'); ?>"
            class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm ml-2">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h1 class="h5 mb-0 text-gray-800">Informasi Pelanggan</h1>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr>
                        <td width="30%"><strong>Nama Pelanggan</strong></td>
                        <td><?php echo $pelanggan->nama_pelanggan; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Perusahaan</strong></td>
                        <td><?php echo $pelanggan->nama_perusahaan; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Alamat</strong></td>
                        <td><?php echo $pelanggan->alamat ?: '-'; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Telepon</strong></td>
                        <td><?php echo $pelanggan->telepon ?: '-'; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Email</strong></td>
                        <td><?php echo $pelanggan->email ?: '-'; ?></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr>
                        <td width="30%"><strong>Tipe Pelanggan</strong></td>
                        <td>
                            <?php if ($pelanggan->tipe_pelanggan == 'distributor'): ?>
                                <span class="badge badge-info">Distributor</span>
                            <?php else: ?>
                                <span class="badge badge-secondary">Konsumen</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Status</strong></td>
                        <td>
                            <?php if ($pelanggan->status_aktif == 1): ?>
                                <span class="badge badge-success">Aktif</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Tidak Aktif</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>ID Pelanggan</strong></td>
                        <td><?php echo $pelanggan->id_pelanggan; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Dibuat Pada</strong></td>
                        <td><?php echo date('d/m/Y H:i:s', strtotime($pelanggan->created_at)); ?></td>
                    </tr>
                    <?php if ($pelanggan->updated_at): ?>
                        <tr>
                            <td><strong>Diperbarui Pada</strong></td>
                            <td><?php echo date('d/m/Y H:i:s', strtotime($pelanggan->updated_at)); ?></td>
                        </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Daftar Transaksi Pelanggan -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h1 class="h5 mb-0 text-gray-800">Riwayat Transaksi (10 Terbaru)</h1>
    </div>
    <div class="card-body">
        <?php if (!empty($transaksi)): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>No. Transaksi</th>
                            <th>Tanggal</th>
                            <th>Gudang Asal</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;
                        foreach ($transaksi as $t): ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo $t->no_transaksi ?: '-'; ?></td>
                                <td><?php echo date('d/m/Y H:i:s', strtotime($t->tanggal_pemindahan)); ?></td>
                                <td><?php echo $t->gudang_asal ?: '-'; ?></td>
                                <td>
                                    <?php
                                    $status_class = '';
                                    switch ($t->status) {
                                        case 'Draft':
                                            $status_class = 'badge-secondary';
                                            break;
                                        case 'Packing':
                                            $status_class = 'badge-info';
                                            break;
                                        case 'Shipping':
                                            $status_class = 'badge-primary';
                                            break;
                                        case 'Delivered':
                                            $status_class = 'badge-success';
                                            break;
                                        case 'Cancelled':
                                            $status_class = 'badge-danger';
                                            break;
                                        default:
                                            $status_class = 'badge-secondary';
                                    }
                                    ?>
                                    <span class="badge <?php echo $status_class; ?>"><?php echo $t->status; ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php if (count($transaksi) >= 10): ?>
                <div class="text-center mt-2">
                    <a href="<?php echo site_url('daftar/pemindahan?pelanggan=' . $pelanggan->id_pelanggan); ?>"
                        class="btn btn-sm btn-outline-primary">
                        Lihat Semua Transaksi
                    </a>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Belum ada transaksi untuk pelanggan ini.
            </div>
        <?php endif; ?>
    </div>
</div>