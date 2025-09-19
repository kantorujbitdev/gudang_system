<div class="card shadow mb-4">
    <div class="card-header py-3">
        <div class="row">
            <div class="col">
                <?php echo responsive_title_blue('Daftar Retur Penjualan') ?>
            </div>
            <div class="col text-right">
                <button class="btn btn-info btn-sm" data-toggle="collapse" data-target="#filterCollapse">
                    <i class="fas fa-filter"></i> Filter
                </button>
            </div>
        </div>
    </div>
    <div class="card-body">
        <!-- Filter Form -->
        <div class="collapse <?php echo $this->input->post() ? 'show' : ''; ?>" id="filterCollapse">
            <div class="card card-body mb-4">
                <?php echo form_open('daftar/retur_penjualan/filter'); ?>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="tanggal_awal">Tanggal Awal</label>
                            <input type="date" class="form-control" id="tanggal_awal" name="tanggal_awal"
                                value="<?php echo set_value('tanggal_awal', $filter['tanggal_awal']); ?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="tanggal_akhir">Tanggal Akhir</label>
                            <input type="date" class="form-control" id="tanggal_akhir" name="tanggal_akhir"
                                value="<?php echo set_value('tanggal_akhir', $filter['tanggal_akhir']); ?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select class="form-control" id="status" name="status">
                                <option value="">Semua</option>
                                <option value="Requested" <?php echo ($filter['status'] == 'Requested') ? 'selected' : ''; ?>>Requested</option>
                                <option value="Verification" <?php echo ($filter['status'] == 'Verification') ? 'selected' : ''; ?>>Verification</option>
                                <option value="Approved" <?php echo ($filter['status'] == 'Approved') ? 'selected' : ''; ?>>Approved</option>
                                <option value="Rejected" <?php echo ($filter['status'] == 'Rejected') ? 'selected' : ''; ?>>Rejected</option>
                                <option value="Completed" <?php echo ($filter['status'] == 'Completed') ? 'selected' : ''; ?>>Completed</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="id_pelanggan">Pelanggan</label>
                            <select class="form-control" id="id_pelanggan" name="id_pelanggan">
                                <option value="">Semua</option>
                                <?php foreach ($pelanggan as $row): ?>
                                    <option value="<?php echo $row->id_pelanggan; ?>" <?php echo ($filter['id_pelanggan'] == $row->id_pelanggan) ? 'selected' : ''; ?>>
                                        <?php echo $row->nama_pelanggan; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Terapkan Filter</button>
                    <a href="<?php echo site_url('daftar/retur_penjualan'); ?>" class="btn btn-secondary">Reset</a>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>No Retur</th>
                        <th>Tanggal</th>
                        <th>No Invoice</th>
                        <th>Pelanggan</th>
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
                            <td><?php echo date('d-m-Y H:i', strtotime($row->tanggal_retur)); ?></td>
                            <td><?php echo $row->no_invoice; ?></td>
                            <td><?php echo $row->nama_pelanggan; ?></td>
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
                                <a href="<?php echo site_url('daftar/retur_penjualan/detail/' . $row->id_retur); ?>"
                                    class="btn btn-sm btn-info" title="Detail">
                                    <i class="fas fa-info-circle"></i>
                                </a>
                                <a href="<?php echo site_url('daftar/retur_penjualan/cetak/' . $row->id_retur); ?>"
                                    class="btn btn-sm btn-primary" title="Cetak" target="_blank">
                                    <i class="fas fa-print"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>