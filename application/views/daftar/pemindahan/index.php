<div class="card shadow mb-4">
    <div class="card-header py-3">
        <div class="row">
            <div class="col">
                <?php echo responsive_title_blue('Daftar Pemindahan Barang') ?>
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
                <?php echo form_open('daftar/pemindahan/filter'); ?>
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
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select class="form-control" id="status" name="status">
                                <option value="">Semua</option>
                                <option value="Draft" <?php echo ($filter['status'] == 'Draft') ? 'selected' : ''; ?>>
                                    Draft</option>
                                <option value="Packing" <?php echo ($filter['status'] == 'Packing') ? 'selected' : ''; ?>>
                                    Packing</option>
                                <option value="Shipping" <?php echo ($filter['status'] == 'Shipping') ? 'selected' : ''; ?>>Shipping</option>
                                <option value="Delivered" <?php echo ($filter['status'] == 'Delivered') ? 'selected' : ''; ?>>Delivered</option>
                                <option value="Cancelled" <?php echo ($filter['status'] == 'Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="id_gudang">Gudang</label>
                            <select class="form-control" id="id_gudang" name="id_gudang">
                                <option value="">Semua</option>
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
                    <a href="<?php echo site_url('daftar/pemindahan'); ?>" class="btn btn-secondary">Reset</a>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>No Transfer</th>
                        <th>Tanggal</th>
                        <th>Asal</th>
                        <th>Tujuan</th>
                        <th>User</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;
                    foreach ($pemindahan as $row): ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo $row->no_transfer; ?></td>
                            <td><?php echo date('d-m-Y H:i', strtotime($row->tanggal)); ?></td>
                            <td><?php echo $row->gudang_asal; ?></td>
                            <td>
                                <?php if ($row->id_gudang_tujuan): ?>
                                    <?php echo $row->gudang_tujuan; ?>
                                <?php elseif ($row->id_pelanggan): ?>
                                    <?php echo $row->nama_pelanggan; ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td><?php echo $row->user_nama; ?></td>
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
                            <td>
                                <a href="<?php echo site_url('daftar/pemindahan/detail/' . $row->id_transfer); ?>"
                                    class="btn btn-sm btn-info" title="Detail">
                                    <i class="fas fa-info-circle"></i>
                                </a>
                                <a href="<?php echo site_url('daftar/pemindahan/cetak/' . $row->id_transfer); ?>"
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