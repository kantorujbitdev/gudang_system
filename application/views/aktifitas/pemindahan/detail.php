<div class="card shadow mb-4">
    <div class="card-header py-3">
        <div class="row">
            <div class="col">
                <h6 class="m-0 font-weight-bold text-primary">Detail Pemindahan Barang:
                    <?php echo $pemindahan->no_transaksi; ?>
                </h6>
            </div>
            <div class="col text-right">
                <a href="<?php echo site_url('aktifitas/pemindahan'); ?>" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-6">
                <table class="table table-sm">
                    <tr>
                        <th width="30%">No Transaksi</th>
                        <td><?php echo $pemindahan->no_transaksi; ?></td>
                    </tr>
                    <tr>
                        <th>Tanggal</th>
                        <td><?php echo date('d-m-Y H:i', strtotime($pemindahan->tanggal_pemindahan)); ?></td>
                    </tr>
                    <tr>
                        <th>Gudang Asal</th>
                        <td><?php echo $pemindahan->gudang_asal; ?></td>
                    </tr>
                    <tr>
                        <th>Tipe Tujuan</th>
                        <td>
                            <?php
                            $tipe_class = '';
                            switch ($pemindahan->tipe_tujuan) {
                                case 'gudang':
                                    $tipe_class = 'badge-info';
                                    break;
                                case 'pelanggan':
                                    $tipe_class = 'badge-warning';
                                    break;
                                case 'konsumen':
                                    $tipe_class = 'badge-success';
                                    break;
                            }
                            ?>
                            <span
                                class="badge <?php echo $tipe_class; ?>"><?php echo ucfirst($pemindahan->tipe_tujuan); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th>Tujuan</th>
                        <td>
                            <?php if ($pemindahan->id_gudang_tujuan): ?>
                                <div class="mb-1">
                                    <span class="badge badge-info">Gudang</span>
                                </div>
                                <div><?php echo $pemindahan->gudang_tujuan; ?></div>
                            <?php elseif ($pemindahan->id_pelanggan): ?>
                                <div class="mb-1">
                                    <span class="badge badge-warning">Pelanggan</span>
                                </div>
                                <div><?php echo $pemindahan->nama_pelanggan; ?></div>
                            <?php elseif ($pemindahan->id_konsumen): ?>
                                <div class="mb-1">
                                    <span class="badge badge-success">Konsumen</span>
                                </div>
                                <div><strong><?php echo $pemindahan->nama_konsumen; ?></strong></div>
                                <?php if ($pemindahan->nama_toko_konsumen): ?>
                                    <div class="text-muted small">Toko: <?php echo $pemindahan->nama_toko_konsumen; ?></div>
                                <?php endif; ?>
                                <?php if ($pemindahan->alamat_konsumen): ?>
                                    <div class="mt-1">
                                        <small><?php echo nl2br(htmlspecialchars($pemindahan->alamat_konsumen)); ?></small>
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-sm">
                    <tr>
                        <th width="30%">User</th>
                        <td><?php echo $pemindahan->user_nama; ?></td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            <?php
                            $status_class = '';
                            switch ($pemindahan->status) {
                                case 'Draft':
                                    $status_class = 'badge-secondary';
                                    break;
                                case 'Packing':
                                    $status_class = 'badge-info';
                                    break;
                                case 'Shipping':
                                    $status_class = 'badge-warning';
                                    break;
                                case 'Delivered':
                                    $status_class = 'badge-success';
                                    break;
                                case 'Cancelled':
                                    $status_class = 'badge-danger';
                                    break;
                            }
                            ?>
                            <span class="badge <?php echo $status_class; ?>"><?php echo $pemindahan->status; ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th>Keterangan</th>
                        <td><?php echo $pemindahan->keterangan ?: '-'; ?></td>
                    </tr>
                    <?php if ($pemindahan->id_konsumen && $pemindahan->alamat_konsumen): ?>
                        <tr>
                            <th>Alamat Lengkap</th>
                            <td><?php echo nl2br(htmlspecialchars($pemindahan->alamat_konsumen)); ?></td>
                        </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>

        <hr>

        <h6>Daftar Barang</h6>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="15%">Kode Barang</th>
                        <th width="45%">Nama Barang</th>
                        <th width="10%">Satuan</th>
                        <th width="15%">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;
                    foreach ($detail as $row): ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo $row->sku; ?></td>
                            <td><?php echo $row->nama_barang; ?></td>
                            <td><?php echo $row->satuan; ?></td>
                            <td><?php echo $row->jumlah; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>