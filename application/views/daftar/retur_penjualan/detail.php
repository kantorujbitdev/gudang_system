<div class="card shadow mb-4">
    <div class="card-header py-3">
        <div class="row">
            <div class="col">
                <h6 class="m-0 font-weight-bold text-primary">Detail Retur Penjualan: <?php echo $retur->no_retur; ?>
                </h6>
            </div>
            <div class="col text-right">
                <a href="<?php echo site_url('daftar/retur_penjualan'); ?>" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <a href="<?php echo site_url('daftar/retur_penjualan/cetak/' . $retur->id_retur); ?>"
                    class="btn btn-primary btn-sm" target="_blank">
                    <i class="fas fa-print"></i> Cetak
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-6">
                <table class="table table-sm">
                    <tr>
                        <th width="30%">No Retur</th>
                        <td><?php echo $retur->no_retur; ?></td>
                    </tr>
                    <tr>
                        <th>Tanggal</th>
                        <td><?php echo date('d-m-Y H:i', strtotime($retur->tanggal_retur)); ?></td>
                    </tr>
                    <tr>
                        <th>No Invoice</th>
                        <td><?php echo $retur->no_invoice; ?></td>
                    </tr>
                    <tr>
                        <th>Pelanggan</th>
                        <td><?php echo $retur->nama_pelanggan; ?></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-sm">
                    <tr>
                        <th width="30%">User</th>
                        <td><?php echo $retur->user_nama; ?></td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            <?php
                            $status_class = '';
                            switch ($retur->status) {
                                case 'Requested':
                                    $status_class = 'badge-secondary';
                                    break;
                                case 'Verification':
                                    $status_class = 'badge-info';
                                    break;
                                case 'Approved':
                                    $status_class = 'badge-warning';
                                    break;
                                case 'Rejected':
                                    $status_class = 'badge-danger';
                                    break;
                                case 'Completed':
                                    $status_class = 'badge-success';
                                    break;
                            }
                            ?>
                            <span class="badge <?php echo $status_class; ?>"><?php echo $retur->status; ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th>Alasan Retur</th>
                        <td><?php echo $retur->alasan_retur; ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <hr>

        <h6>Daftar Barang</h6>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Barang</th>
                        <th>Satuan</th>
                        <th>Gudang</th>
                        <th>Jumlah Retur</th>
                        <th>Jumlah Disetujui</th>
                        <th>Alasan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;
                    foreach ($detail as $row): ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo $row->nama_barang; ?></td>
                            <td><?php echo $row->satuan; ?></td>
                            <td><?php echo $row->nama_gudang; ?></td>
                            <td><?php echo $row->jumlah_retur; ?></td>
                            <td><?php echo $row->jumlah_disetujui; ?></td>
                            <td><?php echo $row->alasan_barang ?: '-'; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>