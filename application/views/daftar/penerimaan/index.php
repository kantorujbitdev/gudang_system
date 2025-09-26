<div class="card shadow mb-4">
    <div class="card-header py-3">
        <div class="row">
            <div class="col">
                <?php echo responsive_title_blue('Penerimaan Barang') ?>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>No Penerimaan</th>
                        <th>Tanggal</th>
                        <th>Supplier</th>
                        <th>Gudang</th>
                        <th>User</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;
                    foreach ($penerimaan as $row): ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo $row->no_penerimaan; ?></td>
                            <td><?php echo date('d-m-Y H:i:s', strtotime($row->tanggal_penerimaan)); ?></td>
                            <td><?php echo $row->nama_supplier; ?></td>
                            <td><?php echo $row->nama_gudang; ?></td>
                            <td><?php echo $row->user_nama; ?></td>
                            <td>
                                <?php
                                $status_class = '';
                                switch ($row->status) {
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
                                <span class="badge <?php echo $status_class; ?>"><?php echo $row->status; ?></span>
                            </td>
                            <td>
                                <a href="<?php echo site_url('daftar/penerimaan/detail/' . $row->id_penerimaan); ?>"
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