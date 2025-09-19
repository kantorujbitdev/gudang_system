<div class="card shadow mb-4">
    <div class="card-header py-3">
        <div class="row">
            <div class="col">
                <?php echo responsive_title_blue('Laporan Mutasi Barang') ?>
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
                <?php echo form_open('laporan/mutasi/filter'); ?>
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="tanggal_awal">Tanggal Awal</label>
                            <input type="date" class="form-control" id="tanggal_awal" name="tanggal_awal"
                                value="<?php echo set_value('tanggal_awal', $filter['tanggal_awal']); ?>">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="tanggal_akhir">Tanggal Akhir</label>
                            <input type="date" class="form-control" id="tanggal_akhir" name="tanggal_akhir"
                                value="<?php echo set_value('tanggal_akhir', $filter['tanggal_akhir']); ?>">
                        </div>
                    </div>
                    <div class="col-md-2">
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
                    <div class="col-md-2">
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
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="jenis">Jenis</label>
                            <select class="form-control" id="jenis" name="jenis">
                                <option value="">Semua Jenis</option>
                                <option value="masuk" <?php echo ($filter['jenis'] == 'masuk') ? 'selected' : ''; ?>>Masuk
                                </option>
                                <option value="keluar" <?php echo ($filter['jenis'] == 'keluar') ? 'selected' : ''; ?>>
                                    Keluar</option>
                                <option value="retur_penjualan" <?php echo ($filter['jenis'] == 'retur_penjualan') ? 'selected' : ''; ?>>Retur Penjualan</option>
                                <option value="retur_pembelian" <?php echo ($filter['jenis'] == 'retur_pembelian') ? 'selected' : ''; ?>>Retur Pembelian</option>
                                <option value="transfer_masuk" <?php echo ($filter['jenis'] == 'transfer_masuk') ? 'selected' : ''; ?>>Transfer Masuk</option>
                                <option value="transfer_keluar" <?php echo ($filter['jenis'] == 'transfer_keluar') ? 'selected' : ''; ?>>Transfer Keluar</option>
                                <option value="penyesuaian" <?php echo ($filter['jenis'] == 'penyesuaian') ? 'selected' : ''; ?>>Penyesuaian</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Terapkan Filter</button>
                    <a href="<?php echo site_url('laporan/mutasi'); ?>" class="btn btn-secondary">Reset</a>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>

        <!-- Summary -->
        <div class="collapse <?php echo $this->input->post() ? 'show' : ''; ?>" id="summaryCollapse">
            <div class="card card-body mb-4">
                <div class="row">
                    <div class="col-md-2">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h5 class="card-title">Masuk</h5>
                                <h3 class="card-text">
                                    <?php echo $this->mutasi->get_summary($filter)->total_masuk ?: 0; ?>
                                </h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card bg-danger text-white">
                            <div class="card-body">
                                <h5 class="card-title">Keluar</h5>
                                <h3 class="card-text">
                                    <?php echo $this->mutasi->get_summary($filter)->total_keluar ?: 0; ?>
                                </h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h5 class="card-title">Retur Penjualan</h5>
                                <h3 class="card-text">
                                    <?php echo $this->mutasi->get_summary($filter)->total_retur_penjualan ?: 0; ?>
                                </h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <h5 class="card-title">Retur Pembelian</h5>
                                <h3 class="card-text">
                                    <?php echo $this->mutasi->get_summary($filter)->total_retur_pembelian ?: 0; ?>
                                </h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h5 class="card-title">Transfer Masuk</h5>
                                <h3 class="card-text">
                                    <?php echo $this->mutasi->get_summary($filter)->total_transfer_masuk ?: 0; ?>
                                </h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card bg-secondary text-white">
                            <div class="card-body">
                                <h5 class="card-title">Transfer Keluar</h5>
                                <h3 class="card-text">
                                    <?php echo $this->mutasi->get_summary($filter)->total_transfer_keluar ?: 0; ?>
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-6">
                        <h6>Mutasi per Barang</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Barang</th>
                                        <th>Masuk</th>
                                        <th>Keluar</th>
                                        <th>Netto</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1;
                                    foreach ($this->mutasi->get_mutasi_by_barang($filter) as $row): ?>
                                        <?php
                                        $total_masuk = $row->total_masuk + $row->total_retur_penjualan + $row->total_transfer_masuk;
                                        $total_keluar = $row->total_keluar + $row->total_retur_pembelian + $row->total_transfer_keluar;
                                        $netto = $total_masuk - $total_keluar;
                                        ?>
                                        <tr>
                                            <td><?php echo $no++; ?></td>
                                            <td><?php echo $row->nama_barang; ?></td>
                                            <td><?php echo $total_masuk; ?></td>
                                            <td><?php echo $total_keluar; ?></td>
                                            <td><?php echo $netto; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6>Mutasi per Gudang</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Gudang</th>
                                        <th>Masuk</th>
                                        <th>Keluar</th>
                                        <th>Netto</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1;
                                    foreach ($this->mutasi->get_mutasi_by_gudang($filter) as $row): ?>
                                        <?php
                                        $total_masuk = $row->total_masuk + $row->total_retur_penjualan + $row->total_transfer_masuk;
                                        $total_keluar = $row->total_keluar + $row->total_retur_pembelian + $row->total_transfer_keluar;
                                        $netto = $total_masuk - $total_keluar;
                                        ?>
                                        <tr>
                                            <td><?php echo $no++; ?></td>
                                            <td><?php echo $row->nama_gudang; ?></td>
                                            <td><?php echo $total_masuk; ?></td>
                                            <td><?php echo $total_keluar; ?></td>
                                            <td><?php echo $netto; ?></td>
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
                <?php echo form_open('laporan/mutasi/export'); ?>
                <input type="hidden" name="tanggal_awal" value="<?php echo $filter['tanggal_awal']; ?>">
                <input type="hidden" name="tanggal_akhir" value="<?php echo $filter['tanggal_akhir']; ?>">
                <input type="hidden" name="id_barang" value="<?php echo $filter['id_barang']; ?>">
                <input type="hidden" name="id_gudang" value="<?php echo $filter['id_gudang']; ?>">
                <input type="hidden" name="jenis" value="<?php echo $filter['jenis']; ?>">

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
                        <th>Tanggal</th>
                        <th>Barang</th>
                        <th>Gudang</th>
                        <th>Jenis</th>
                        <th>Jumlah</th>
                        <th>Sisa Stok</th>
                        <th>User</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;
                    foreach ($mutasi as $row): ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo date('d-m-Y H:i', strtotime($row->tanggal)); ?></td>
                            <td><?php echo $row->nama_barang; ?></td>
                            <td><?php echo $row->nama_gudang; ?></td>
                            <td>
                                <?php
                                $jenis_class = '';
                                switch ($row->jenis) {
                                    case 'masuk':
                                        $jenis_class = 'badge-success';
                                        break;
                                    case 'keluar':
                                        $jenis_class = 'badge-danger';
                                        break;
                                    case 'retur_penjualan':
                                        $jenis_class = 'badge-info';
                                        break;
                                    case 'retur_pembelian':
                                        $jenis_class = 'badge-warning';
                                        break;
                                    case 'transfer_masuk':
                                        $jenis_class = 'badge-primary';
                                        break;
                                    case 'transfer_keluar':
                                        $jenis_class = 'badge-secondary';
                                        break;
                                    case 'penyesuaian':
                                        $jenis_class = 'badge-dark';
                                        break;
                                }
                                ?>
                                <span class="badge <?php echo $jenis_class; ?>"><?php echo $row->jenis; ?></span>
                            </td>
                            <td><?php echo $row->jumlah; ?></td>
                            <td><?php echo $row->sisa_stok; ?></td>
                            <td><?php echo $row->user_nama ?: '-'; ?></td>
                            <td><?php echo $row->keterangan ?: '-'; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>