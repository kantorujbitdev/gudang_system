<div class="card shadow mb-4">
    <div class="card-header py-3">
        <div class="row">
            <div class="col">
                <?php echo responsive_title_blue('Daftar Penerimaan Barang') ?>
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
                <?php echo form_open('daftar/penerimaan/filter'); ?>
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
                                <option value="Received" <?php echo ($filter['status'] == 'Received') ? 'selected' : ''; ?>>Received</option>
                                <option value="Completed" <?php echo ($filter['status'] == 'Completed') ? 'selected' : ''; ?>>Completed</option>
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
                            <label for="id_supplier">Supplier</label>
                            <select class="form-control" id="id_supplier" name="id_supplier">
                                <option value="">Semua</option>
                                <?php foreach ($supplier as $row): ?>
                                    <option value="<?php echo $row->id_supplier; ?>" <?php echo ($filter['id_supplier'] == $row->id_supplier) ? 'selected' : ''; ?>>
                                        <?php echo $row->nama_supplier; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Terapkan Filter</button>
                    <a href="<?php echo site_url('daftar/penerimaan'); ?>" class="btn btn-secondary">Reset</a>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>

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
                            <td><?php echo date('d-m-Y H:i', strtotime($row->tanggal_penerimaan)); ?></td>
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
                                <a href="<?php echo site_url('daftar/penerimaan/cetak/' . $row->id_penerimaan); ?>"
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