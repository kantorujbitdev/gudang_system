<div class="card shadow mb-4">
    <div class="card-header py-3">
        <div class="row">
            <div class="col">
                <?php echo responsive_title_blue('Laporan Packing') ?>
            </div>
            <div class="col text-right">
                <button class="btn btn-info btn-sm" data-toggle="collapse" data-target="#filterCollapse">
                    <i class="fas fa-filter"></i> Filter
                </button>
                <button class="btn btn-success btn-sm" data-toggle="collapse" data-target="#summaryCollapse">
                    <i class="fas fa-chart-pie"></i> Dashboard
                </button>
                <button class="btn btn-primary btn-sm" data-toggle="collapse" data-target="#exportCollapse">
                    <i class="fas fa-file-excel"></i> Export
                </button>
            </div>
        </div>
    </div>
    <div class="card-body">
        <!-- Filter Form -->
        <div class="collapse <?php echo $this->input->post() ? 'show' : ''; ?>" id="filterCollapse">
            <div class="card card-body mb-4">
                <?php echo form_open('laporan/packing/filter'); ?>
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
                            <label for="id_user">Admin Packing</label>
                            <select class="form-control select2" id="id_user" name="id_user">
                                <option value="">Semua User</option>
                                <?php foreach ($user as $row): ?>
                                    <option value="<?php echo $row->id_user; ?>" <?php echo ($filter['id_user'] == $row->id_user) ? 'selected' : ''; ?>><?php echo $row->nama; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select class="form-control" id="status" name="status">
                                <option value="">Semua Status</option>
                                <option value="Draft" <?php echo (isset($filter['status']) && $filter['status'] == 'Draft') ? 'selected' : ''; ?>>Draft</option>
                                <option value="Packing" <?php echo (isset($filter['status']) && $filter['status'] == 'Packing') ? 'selected' : ''; ?>>Packing</option>
                                <option value="Completed" <?php echo (isset($filter['status']) && $filter['status'] == 'Completed') ? 'selected' : ''; ?>>Completed</option>
                                <option value="Cancelled" <?php echo (isset($filter['status']) && $filter['status'] == 'Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Terapkan Filter</button>
                    <a href="<?php echo site_url('laporan/packing'); ?>" class="btn btn-secondary">Reset</a>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>

        <!-- Dashboard -->
        <div class="collapse <?php echo $this->input->post() ? 'show' : ''; ?>" id="summaryCollapse">
            <div class="card card-body mb-4">
                <div class="row">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h5 class="card-title">Total Packing</h5>
                                <h3 class="card-text">
                                    <?php echo $summary->total_packing ?: 0; ?>
                                </h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h5 class="card-title">Total Barang</h5>
                                <h3 class="card-text">
                                    <?php echo $summary->total_barang ?: 0; ?>
                                </h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h5 class="card-title">Rata-rata/Packing</h5>
                                <h3 class="card-text">
                                    <?php echo $summary->total_packing > 0 ? round($summary->total_barang / $summary->total_packing, 1) : 0; ?>
                                </h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <h5 class="card-title">Rata-rata Waktu</h5>
                                <h3 class="card-text">
                                    <?php echo round($avg_packing_time->rata_rata_waktu ?: 0, 1); ?> jam
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-6">
                        <h6>Grafik Packing Periode</h6>
                        <div class="table-responsive">
                            <canvas id="packingChart" height="200"></canvas>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6>Performance Admin Packing</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Admin Packing</th>
                                        <th>Total Packing</th>
                                        <th>Total Barang</th>
                                        <th>Rata-rata</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; ?>
                                    <?php foreach ($packing_by_user as $row): ?>
                                        <tr>
                                            <td><?php echo $no++; ?></td>
                                            <td><?php echo $row->nama; ?></td>
                                            <td><?php echo $row->total_packing; ?></td>
                                            <td><?php echo $row->total_barang; ?></td>
                                            <td><?php echo $row->total_packing > 0 ? round($row->total_barang / $row->total_packing, 1) : 0; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-12">
                        <h6>Efisiensi Waktu Packing</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>No Packing</th>
                                        <th>Tanggal Packing</th>
                                        <th>Admin Packing</th>
                                        <th>Referensi</th>
                                        <th>Waktu Packing (jam)</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; ?>
                                    <?php foreach (array_slice($packing_efficiency, 0, 10) as $row): ?>
                                        <tr>
                                            <td><?php echo $no++; ?></td>
                                            <td>#<?php echo $row->id_packing; ?></td>
                                            <td><?php echo date('d-m-Y H:i', strtotime($row->tanggal_packing)); ?></td>
                                            <td><?php echo $row->user_nama; ?></td>
                                            <td><?php echo 'Pemindahan #' . $row->id_referensi; ?></td>
                                            <td><?php echo $row->waktu_packing; ?></td>
                                            <td>
                                                <?php
                                                $status_class = '';
                                                switch ($row->status) {
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
                                                <span
                                                    class="badge <?php echo $status_class; ?>"><?php echo $row->status; ?></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Export -->
        <div class="collapse <?php echo $this->input->post() ? 'show' : ''; ?>" id="exportCollapse">
            <div class="card card-body mb-4">
                <?php echo form_open('laporan/packing/export'); ?>
                <input type="hidden" name="tanggal_awal" value="<?php echo $filter['tanggal_awal']; ?>">
                <input type="hidden" name="tanggal_akhir" value="<?php echo $filter['tanggal_akhir']; ?>">
                <input type="hidden" name="id_user" value="<?php echo $filter['id_user']; ?>">
                <input type="hidden" name="status" value="<?php echo $filter['status']; ?>">

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Format Export</label>
                            <select class="form-control" name="format">
                                <option value="excel">Excel (.xlsx)</option>
                                <option value="pdf">PDF</option>
                                <option value="csv">CSV</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-download"></i> Download Laporan
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>No Packing</th>
                        <th>Tanggal</th>
                        <th>User</th>
                        <th>Referensi</th>
                        <th>Barang</th>
                        <th>Jumlah</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;
                    foreach ($packing as $row): ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td>#<?php echo $row->id_packing; ?></td>
                            <td><?php echo date('d-m-Y H:i:s', strtotime($row->tanggal_packing)); ?></td>
                            <td><?php echo $row->user_nama; ?></td>
                            <td><?php echo $row->tipe_referensi . ' #' . $row->id_referensi; ?></td>
                            <td><?php echo $row->nama_barang; ?></td>
                            <td><?php echo $row->jumlah; ?></td>
                            <td>
                                <?php
                                $status_class = '';
                                switch ($row->status) {
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
                                <span class="badge <?php echo $status_class; ?>"><?php echo $row->status; ?></span>
                            </td>
                            <td>
                                <a href="<?php echo site_url('laporan/packing/detail/' . $row->id_packing); ?>"
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