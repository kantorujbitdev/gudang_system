<div class="card shadow mb-4">
    <div class="card-header py-3">
        <div class="row">
            <div class="col">
                <h6 class="m-0 font-weight-bold text-primary">Verifikasi Retur Pembelian:
                    <?php echo $retur->no_retur_beli; ?>
                </h6>
            </div>
            <div class="col text-right">
                <a href="<?php echo site_url('aktifitas/retur_pembelian'); ?>" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <?php echo form_open(current_url()); ?>
        <div class="row mb-3">
            <div class="col-md-6">
                <table class="table table-sm">
                    <tr>
                        <th width="30%">No Retur</th>
                        <td><?php echo $retur->no_retur_beli; ?></td>
                    </tr>
                    <tr>
                        <th>Tanggal</th>
                        <td><?php echo date('d-m-Y H:i', strtotime($retur->tanggal_retur)); ?></td>
                    </tr>
                    <tr>
                        <th>No Pembelian</th>
                        <td><?php echo $retur->no_pembelian ?: '-'; ?></td>
                    </tr>
                    <tr>
                        <th>Supplier</th>
                        <td><?php echo $retur->nama_supplier; ?></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-sm">
                    <tr>
                        <th width="30%">User</th>
                        <td><?php echo $retur->user_nama; ?></td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            <?php
                            $status_class = '';
                            switch ($retur->status) {
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
                            <span class="badge <?php echo $status_class; ?>"><?php echo $retur->status; ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th>Alasan Retur</th>
                        <td><?php echo $retur->alasan_retur; ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <hr>

        <h6>Daftar Barang</h6>
        <div class="table-responsive mb-3">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="30%">Barang</th>
                        <th width="15%">Gudang</th>
                        <th width="15%">Jumlah Retur</th>
                        <th width="15%">Jumlah Disetujui</th>
                        <th width="15%">Alasan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;
                    foreach ($detail as $row): ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo $row->nama_barang; ?></td>
                            <td><?php echo $row->nama_gudang; ?></td>
                            <td><?php echo $row->jumlah_retur; ?></td>
                            <td>
                                <input type="hidden" name="id_detail_retur[]"
                                    value="<?php echo $row->id_detail_retur_beli; ?>">
                                <input type="number" class="form-control" name="jumlah_disetujui[]"
                                    value="<?php echo $row->jumlah_disetujui; ?>" min="0"
                                    max="<?php echo $row->jumlah_retur; ?>">
                            </td>
                            <td><?php echo $row->alasan_barang ?: '-'; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="form-group">
            <label for="status">Status Verifikasi <span class="text-danger">*</span></label>
            <select class="form-control" id="status" name="status" required>
                <option value="">-- Pilih Status --</option>
                <option value="Approved">Approved</option>
                <option value="Rejected">Rejected</option>
            </select>
            <?php echo form_error('status', '<small class="text-danger">', '</small>'); ?>
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="<?php echo site_url('aktifitas/retur_pembelian'); ?>" class="btn btn-secondary">Batal</a>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>