<div class="card shadow mb-4">
    <div class="card-header py-3">
        <div class="row">
            <div class="col">
                <h6 class="m-0 font-weight-bold text-primary">Detail Penerimaan Barang:
                    <?php echo $penerimaan->no_penerimaan; ?>
                </h6>
            </div>
            <div class="col text-right">
                <a href="<?php echo site_url('aktifitas/penerimaan'); ?>" class="btn btn-secondary btn-sm">
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
                        <th width="30%">No Penerimaan</th>
                        <td><?php echo $penerimaan->no_penerimaan; ?></td>
                    </tr>
                    <tr>
                        <th>Tanggal</th>
                        <td><?php echo date('d-m-Y H:i:s', strtotime($penerimaan->tanggal_penerimaan)); ?></td>
                    </tr>
                    <tr>
                        <th>Supplier</th>
                        <td><?php echo $penerimaan->nama_supplier; ?></td>
                    </tr>
                    <tr>
                        <th>Gudang</th>
                        <td><?php echo $penerimaan->nama_gudang; ?></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-sm">
                    <tr>
                        <th width="30%">User</th>
                        <td><?php echo $penerimaan->user_nama; ?></td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            <?php
                            $status_class = '';
                            switch ($penerimaan->status) {
                                case 'Draft':
                                    $status_class = 'badge-secondary';
                                    break;
                                case 'Received':
                                    $status_class = 'badge-info';
                                    break;
                                case 'Completed':
                                    $status_class = 'badge-success';
                                    break;
                                case 'Cancelled':
                                    $status_class = 'badge-danger';
                                    break;
                            }
                            ?>
                            <span class="badge <?php echo $status_class; ?>"><?php echo $penerimaan->status; ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th>Keterangan</th>
                        <td><?php echo $penerimaan->keterangan ?: '-'; ?></td>
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
                        <th>Jumlah Dipesan</th>
                        <th>Jumlah Diterima</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;
                    foreach ($detail as $row): ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo $row->nama_barang; ?></td>
                            <td><?php echo $row->satuan; ?></td>
                            <td><?php echo $row->jumlah_dipesan ?: '-'; ?></td>
                            <td><?php echo $row->jumlah_diterima; ?></td>
                            <td><?php echo $row->keterangan ?: '-'; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>