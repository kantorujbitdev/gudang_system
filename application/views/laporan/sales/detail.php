<div class="card shadow mb-4">
    <div class="card-header py-3">
        <div class="row">
            <div class="col">
                <h6 class="m-0 font-weight-bold text-primary">Detail Penjualan: <?php echo $penjualan->no_invoice; ?>
                </h6>
            </div>
            <div class="col text-right">
                <a href="<?php echo site_url('laporan/sales'); ?>" class="btn btn-secondary btn-sm">
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
                        <th width="30%">No Invoice</th>
                        <td><?php echo $penjualan->no_invoice; ?></td>
                    </tr>
                    <tr>
                        <th>Tanggal</th>
                        <td><?php echo date('d-m-Y H:i', strtotime($penjualan->tanggal_penjualan)); ?></td>
                    </tr>
                    <tr>
                        <th>Pelanggan</th>
                        <td><?php echo $penjualan->nama_pelanggan; ?></td>
                    </tr>
                    <tr>
                        <th>Alamat</th>
                        <td><?php echo $penjualan->alamat ?: '-'; ?></td>
                    </tr>
                    <tr>
                        <th>Telepon</th>
                        <td><?php echo $penjualan->telepon ?: '-'; ?></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-sm">
                    <tr>
                        <th width="30%">User</th>
                        <td><?php echo $penjualan->user_nama; ?></td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            <?php
                            $status_class = '';
                            switch ($penjualan->status) {
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
                            <span class="badge <?php echo $status_class; ?>"><?php echo $penjualan->status; ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th>Keterangan</th>
                        <td><?php echo $penjualan->keterangan ?: '-'; ?></td>
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
                        <th>Jumlah</th>
                        <th>Total</th>
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
                            <td><?php echo $row->jumlah; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>