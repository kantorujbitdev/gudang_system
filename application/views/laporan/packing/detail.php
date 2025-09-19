<div class="card shadow mb-4">
    <div class="card-header py-3">
        <div class="row">
            <div class="col">
                <h6 class="m-0 font-weight-bold text-primary">Detail Packing: <?php echo $packing->id_packing; ?></h6>
            </div>
            <div class="col text-right">
                <a href="<?php echo site_url('laporan/packing'); ?>" class="btn btn-secondary btn-sm">
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
                        <th width="30%">No Packing</th>
                        <td><?php echo $packing->id_packing; ?></td>
                    </tr>
                    <tr>
                        <th>Tanggal</th>
                        <td><?php echo date('d-m-Y H:i', strtotime($packing->tanggal_packing)); ?></td>
                    </tr>
                    <tr>
                        <th>User</th>
                        <td><?php echo $packing->user_nama; ?></td>
                    </tr>
                    <tr>
                        <th>Referensi</th>
                        <td><?php echo $packing->tipe_referensi . ' #' . $packing->id_referensi; ?></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-sm">
                    <tr>
                        <th width="30%">Status</th>
                        <td>
                            <?php
                            $status_class = '';
                            switch ($packing->status) {
                                case 'Draft':
                                    $status_class = 'badge-secondary';
                                    break;
                                case 'Packing':
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
                            <span class="badge <?php echo $status_class; ?>"><?php echo $packing->status; ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th>Catatan</th>
                        <td><?php echo $packing->catatan ?: '-'; ?></td>
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
                        <th>Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;
                    $total = 0;
                    foreach ($detail as $row): ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo $row->nama_barang; ?></td>
                            <td><?php echo $row->satuan; ?></td>
                            <td><?php echo $row->jumlah; ?></td>
                        </tr>
                        <?php $total += $row->jumlah; ?>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="3" class="text-right font-weight-bold">Total</td>
                        <td class="font-weight-bold"><?php echo $total; ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>