<div class="card shadow mb-4">
    <div class="card-header py-3">
        <div class="row">
            <div class="col">
                <?php echo responsive_title_blue('Laporan Sales') ?>
            </div>
            <div class="col text-right">
                <button class="btn btn-info btn-sm" data-toggle="collapse" data-target="#filterCollapse">
                    <i class="fas fa-filter"></i> Filter
                </button>
                <button class="btn btn-success btn-sm" data-toggle="collapse" data-target="#summaryCollapse">
                    <i class="fas fa-chart-pie"></i> Summary
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
                <?php echo form_open('laporan/sales/filter'); ?>
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
                            <label for="id_barang">Barang</label>
                            <select class="form-control" id="id_barang" name="id_barang">
                                <option value="">Semua Barang</option>
                                <?php foreach ($barang as $row): ?>
                                    <option value="<?php echo $row->id_barang; ?>" <?php echo ($filter['id_barang'] == $row->id_barang) ? 'selected' : ''; ?>>
                                        <?php echo $row->nama_barang; ?>
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
                                <option value="Shipping" <?php echo (isset($filter['status']) && $filter['status'] == 'Shipping') ? 'selected' : ''; ?>>Shipping</option>
                                <option value="Delivered" <?php echo (isset($filter['status']) && $filter['status'] == 'Delivered') ? 'selected' : ''; ?>>Delivered</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="id_user">User</label>
                            <select class="form-control" id="id_user" name="id_user">
                                <option value="">Semua User</option>
                                <?php
                                $current_role = '';
                                foreach ($users as $row):
                                    if ($current_role != $row->nama_role) {
                                        if ($current_role != '')
                                            echo '</optgroup>';
                                        echo '<optgroup label="' . $row->nama_role . '">';
                                        $current_role = $row->nama_role;
                                    }
                                    ?>
                                    <option value="<?php echo $row->id_user; ?>" <?php echo ($filter['id_user'] == $row->id_user) ? 'selected' : ''; ?>>
                                        <?php echo $row->nama; ?>
                                    </option>
                                <?php endforeach;
                                if ($current_role != '')
                                    echo '</optgroup>';
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary">Terapkan Filter</button>
                                <a href="<?php echo site_url('laporan/sales'); ?>" class="btn btn-secondary">Reset</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>

        <!-- Summary -->
        <div class="collapse <?php echo $this->input->post() ? 'show' : ''; ?>" id="summaryCollapse">
            <div class="card card-body mb-4">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h5 class="card-title">Total Transaksi</h5>
                                <h3 class="card-text">
                                    <?php echo $this->sales->get_summary($filter)->total_transaksi ?: 0; ?>
                                </h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h5 class="card-title">Total Barang</h5>
                                <h3 class="card-text">
                                    <?php echo $this->sales->get_summary($filter)->total_barang ?: 0; ?>
                                </h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h5 class="card-title">Rata-rata</h5>
                                <h3 class="card-text">
                                    <?php echo number_format(($this->sales->get_summary($filter)->total_barang ?: 0) / ($this->sales->get_summary($filter)->total_transaksi ?: 1), 0, ',', '.'); ?>
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-6">
                        <h6>Top 5 Tujuan</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tujuan</th>
                                        <th>Transaksi</th>
                                        <th>Total Barang</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1;
                                    $top_pelanggan = array_slice($this->sales->get_sales_by_pelanggan($filter), 0, 5); ?>
                                    <?php foreach ($top_pelanggan as $row): ?>
                                        <tr>
                                            <td><?php echo $no++; ?></td>
                                            <td><?php echo $row->nama_tujuan; ?></td>
                                            <td><?php echo $row->total_transaksi; ?></td>
                                            <td><?php echo $row->total_barang; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6>Top 5 Barang</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Barang</th>
                                        <th>Jumlah</th>
                                        <th>Satuan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1;
                                    $top_barang = array_slice($this->sales->get_sales_by_barang($filter), 0, 5); ?>
                                    <?php foreach ($top_barang as $row): ?>
                                        <tr>
                                            <td><?php echo $no++; ?></td>
                                            <td><?php echo $row->nama_barang; ?></td>
                                            <td><?php echo $row->total_jumlah; ?></td>
                                            <td><?php echo $row->satuan; ?></td>
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
                <?php echo form_open('laporan/sales/export'); ?>
                <input type="hidden" name="tanggal_awal" value="<?php echo $filter['tanggal_awal']; ?>">
                <input type="hidden" name="tanggal_akhir" value="<?php echo $filter['tanggal_akhir']; ?>">
                <input type="hidden" name="id_barang" value="<?php echo $filter['id_barang']; ?>">
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
                        <th>No Transaksi</th>
                        <th>Tanggal</th>
                        <th>Tujuan</th>
                        <th>Barang</th>
                        <th>Jumlah</th>
                        <th>Satuan</th>
                        <th>Status</th>
                        <th>User</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;
                    foreach ($sales as $row): ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo $row->no_transaksi; ?></td>
                            <td><?php echo date('d-m-Y H:i:s', strtotime($row->tanggal_pemindahan)); ?></td>
                            <td><?php echo $row->nama_tujuan; ?></td>
                            <td><?php echo $row->nama_barang; ?></td>
                            <td><?php echo $row->jumlah; ?></td>
                            <td><?php echo $row->satuan; ?></td>
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
                                <span class="badge <?php echo $status_class; ?>"><?php echo $row->status; ?></span>
                            </td>
                            <td><?php echo $row->nama_user; ?></td>
                            <td>
                                <a href="<?php echo site_url('laporan/sales/detail/' . $row->id_pemindahan); ?>"
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