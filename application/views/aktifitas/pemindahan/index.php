<div class="card shadow mb-4">
    <div class="card-header py-3">
        <div class="row">
            <div class="col">
                <?php echo responsive_title_blue('Pemindahan Barang') ?>
            </div>
            <?php if ($can_create): ?>
                <div class="col text-right">
                    <a href="<?php echo site_url('aktifitas/pemindahan/tambah'); ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Tambah Pemindahan
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>No Transaksi</th>
                        <th>Tanggal</th>
                        <th>Asal</th>
                        <th>Tipe Tujuan</th>
                        <th>Lokasi Tujuan</th>
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
                            <td><?php echo $row->no_transaksi; ?></td>
                            <td><?php echo date('d-m-Y H:i', strtotime($row->tanggal_pemindahan)); ?></td>
                            <td><?php echo $row->gudang_asal; ?></td>
                            <td><?php echo ucfirst($row->tipe_tujuan); ?></td>
                            <td>
                                <?php if ($row->id_gudang_tujuan): ?>
                                    <?php echo $row->gudang_tujuan; ?>
                                <?php elseif ($row->id_pelanggan): ?>
                                    <?php echo $row->nama_pelanggan; ?>
                                <?php elseif ($row->id_alamat_konsumen): ?>
                                    <?php echo substr($row->alamat_lengkap, 0, 30) . '...'; ?>
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
                                <a href="<?php echo site_url('aktifitas/pemindahan/detail/' . $row->id_pemindahan); ?>"
                                    class="btn btn-sm btn-info" title="Detail">
                                    <i class="fas fa-info-circle"></i>
                                </a>
                                <?php if ($row->status == 'Draft' && $can_edit): ?>
                                    <a href="<?php echo site_url('aktifitas/pemindahan/edit/' . $row->id_pemindahan); ?>"
                                        class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                <?php endif; ?>
                                <?php if ($row->status == 'Draft' && $can_delete): ?>
                                    <a href="<?php echo site_url('aktifitas/pemindahan/hapus/' . $row->id_pemindahan); ?>"
                                        class="btn btn-sm btn-danger" title="Hapus"
                                        onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                <?php endif; ?>
                                <?php if ($can_edit): ?>
                                    <?php if ($row->status == 'Draft'): ?>
                                        <a href="<?php echo site_url('aktifitas/pemindahan/konfirmasi/' . $row->id_pemindahan . '/Packing'); ?>"
                                            class="btn btn-sm btn-primary" title="Proses Packing"
                                            onclick="return confirm('Apakah Anda yakin ingin memproses packing?')">
                                            <i class="fas fa-box"></i> Packing
                                        </a>
                                    <?php elseif ($row->status == 'Packing'): ?>
                                        <a href="<?php echo site_url('aktifitas/pemindahan/konfirmasi/' . $row->id_pemindahan . '/Shipping'); ?>"
                                            class="btn btn-sm btn-warning" title="Proses Shipping"
                                            onclick="return confirm('Apakah Anda yakin ingin memproses shipping?')">
                                            <i class="fas fa-truck"></i> Shipping
                                        </a>
                                    <?php elseif ($row->status == 'Shipping'): ?>
                                        <a href="<?php echo site_url('aktifitas/pemindahan/konfirmasi/' . $row->id_pemindahan . '/Delivered'); ?>"
                                            class="btn btn-sm btn-success" title="Konfirmasi Diterima"
                                            onclick="return confirm('Apakah Anda yakin ingin mengkonfirmasi barang diterima?')">
                                            <i class="fas fa-check"></i> Delivered
                                        </a>
                                    <?php endif; ?>

                                    <?php if ($row->status != 'Delivered' && $row->status != 'Cancelled'): ?>
                                        <a href="<?php echo site_url('aktifitas/pemindahan/konfirmasi/' . $row->id_pemindahan . '/Cancelled'); ?>"
                                            class="btn btn-sm btn-danger" title="Batalkan"
                                            onclick="return confirm('Apakah Anda yakin ingin membatalkan pemindahan ini?')">
                                            <i class="fas fa-times"></i> Batal
                                        </a>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>