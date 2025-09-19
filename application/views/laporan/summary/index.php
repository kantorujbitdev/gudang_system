<div class="card shadow mb-4">
    <div class="card-header py-3">
        <div class="row">
            <div class="col">
                <?php echo responsive_title_blue('Ringkasan Stok') ?>
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
                <?php echo form_open('laporan/summary/filter'); ?>
                <div class="row">
                    <div class="col-md-4">
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
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="id_gudang">Gudang</label>
                            <select class="form-control" id="id_gudang" name="id_gudang">
                                <option value="">Semua Gudang</option>
                                <?php foreach ($gudang as $row): ?>
                                    <option value="<?php echo $row->id_gudang; ?>" <?php echo ($filter['id_gudang'] == $row->id_gudang) ? 'selected' : ''; ?>>
                                        <?php echo $row->nama_gudang; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Terapkan Filter</button>
                    <a href="<?php echo site_url('laporan/summary'); ?>" class="btn btn-secondary">Reset</a>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>

        <!-- Summary -->
        <div class="collapse <?php echo $this->input->post() ? 'show' : ''; ?>" id="summaryCollapse">
            <div class="card card-body mb-4">
                <div class="row">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h5 class="card-title">Total Barang</h5>
                                <h3 class="card-text"><?php echo $this->summary->get_summary($filter)->total_barang; ?>
                                </h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h5 class="card-title">Total Stok</h5>
                                <h3 class="card-text"><?php echo $this->summary->get_summary($filter)->total_stok; ?>
                                </h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h5 class="card-title">Stok Tersedia</h5>
                                <h3 class="card-text">
                                    <?php echo $this->summary->get_summary($filter)->total_tersedia; ?>
                                </h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <h5 class="card-title">Stok Reserved</h5>
                                <h3 class="card-text">
                                    <?php echo $this->summary->get_summary($filter)->total_reserved; ?>
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-12">
                        <h6>Status Stok</h6>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="card bg-danger text-white">
                                    <div class="card-body">
                                        <h5 class="card-title">Stok Habis</h5>
                                        <h3 class="card-text">
                                            <?php echo $this->summary->get_summary($filter)->stok_habis; ?>
                                        </h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body">
                                        <h5 class="card-title">Stok Rendah</h5>
                                        <h3 class="card-text">
                                            <?php echo $this->summary->get_summary($filter)->stok_rendah; ?>
                                        </h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body">
                                        <h5 class="card-title">Stok Sedang</h5>
                                        <h3 class="card-text">
                                            <?php echo $this->summary->get_summary($filter)->stok_sedang; ?>
                                        </h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body">
                                        <h5 class="card-title">Stok Cukup</h5>
                                        <h3 class="card-text">
                                            <?php echo $this->summary->get_summary($filter)->stok_cukup; ?>
                                        </h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-6">
                        <h6>Stok per Barang</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Barang</th>
                                        <th>Total Stok</th>
                                        <th>Reserved</th>
                                        <th>Tersedia</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1;
                                    foreach ($this->summary->get_stok_by_barang($filter) as $row): ?>
                                        <tr>
                                            <td><?php echo $no++; ?></td>
                                            <td><?php echo $row->nama_barang; ?></td>
                                            <td><?php echo $row->total_stok; ?></td>
                                            <td><?php echo $row->total_reserved; ?></td>
                                            <td><?php echo $row->total_tersedia; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6>Stok per Gudang</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Gudang</th>
                                        <th>Total Barang</th>
                                        <th>Total Stok</th>
                                        <th>Reserved</th>
                                        <th>Tersedia</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1;
                                    foreach ($this->summary->get_stok_by_gudang($filter) as $row): ?>
                                        <tr>
                                            <td><?php echo $no++; ?></td>
                                            <td><?php echo $row->nama_gudang; ?></td>
                                            <td><?php echo $row->total_barang; ?></td>
                                            <td><?php echo $row->total_stok; ?></td>
                                            <td><?php echo $row->total_reserved; ?></td>
                                            <td><?php echo $row->total_tersedia; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <?php $stok_kritis = $this->summary->get_stok_kritis($filter); ?>
                <?php if (count($stok_kritis) > 0): ?>
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="alert alert-danger">
                                <h5><i class="fas fa-exclamation-triangle"></i> Stok Kritis</h5>
                                <p>Barang berikut ini memiliki stok rendah dan perlu segera diisi ulang:</p>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Barang</th>
                                                <th>SKU</th>
                                                <th>Gudang</th>
                                                <th>Stok Tersedia</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $no = 1;
                                            foreach ($stok_kritis as $row): ?>
                                                <tr>
                                                    <td><?php echo $no++; ?></td>
                                                    <td><?php echo $row->nama_barang; ?></td>
                                                    <td><?php echo $row->sku; ?></td>
                                                    <td><?php echo $row->nama_gudang; ?></td>
                                                    <td><?php echo $row->stok_tersedia; ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Export -->
        <div class="collapse <?php echo $this->input->post() ? 'show' : ''; ?>" id="exportCollapse">
            <div class="card card-body mb-4">
                <?php echo form_open('laporan/summary/export'); ?>
                <input type="hidden" name="id_barang" value="<?php echo $filter['id_barang']; ?>">
                <input type="hidden" name="id_gudang" value="<?php echo $filter['id_gudang']; ?>">

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
                        <th>Barang</th>
                        <th>SKU</th>
                        <th>Gudang</th>
                        <th>Stok Tersedia</th>
                        <th>Reserved</th>
                        <th>Total Stok</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;
                    foreach ($stok as $row): ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo $row->nama_barang; ?></td>
                            <td><?php echo $row->sku; ?></td>
                            <td><?php echo $row->nama_gudang; ?></td>
                            <td>
                                <?php
                                $stok_class = '';
                                if ($row->stok_tersedia <= 0) {
                                    $stok_class = 'badge-danger';
                                } elseif ($row->stok_tersedia <= 10) {
                                    $stok_class = 'badge-warning';
                                } elseif ($row->stok_tersedia <= 50) {
                                    $stok_class = 'badge-info';
                                } else {
                                    $stok_class = 'badge-success';
                                }
                                ?>
                                <span class="badge <?php echo $stok_class; ?>"><?php echo $row->stok_tersedia; ?></span>
                            </td>
                            <td><?php echo $row->reserved; ?></td>
                            <td><?php echo $row->jumlah; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>