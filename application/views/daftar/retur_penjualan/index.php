<div class="card shadow mb-4">
    <div class="card-header py-3">
        <div class="row">
            <div class="col">
                <?php echo responsive_title_blue('Retur Penjualan') ?>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>No Retur</th>
                        <th>Tanggal</th>
                        <th>No. Transaksi</th>
                        <th>Penerima</th>
                        <th>User</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;
                    foreach ($retur as $row): ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo $row->no_retur; ?></td>
                            <td><?php echo date('d-m-Y H:i:s', strtotime($row->tanggal_retur)); ?></td>
                            <td><?php echo $row->no_transaksi; ?></td>
                            <td><?php echo $row->nama_penerima; ?></td>
                            <td><?php echo $row->user_nama; ?></td>
                            <td>
                                <?php
                                $status_class = '';
                                switch ($row->status) {
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
                                <span class="badge <?php echo $status_class; ?>"><?php echo $row->status; ?></span>
                            </td>
                            <td>
                                <a href="<?php echo site_url('aktifitas/retur_penjualan/detail/' . $row->id_retur); ?>"
                                    class="btn btn-sm btn-info" title="Detail">
                                    <i class="fas fa-info-circle"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>