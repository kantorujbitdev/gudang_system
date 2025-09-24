<div class="card shadow mb-4">
    <div class="card-header py-3">
        <div class="row">
            <div class="col">
                <?php echo responsive_title_blue('Daftar Barang') ?>
            </div>
            <div class="col text-right">
                <a href="<?php echo site_url('setup/barang/tambah') ?>" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Tambah Barang
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Gambar</th>
                        <th>SKU</th>
                        <th>Kode Barang</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th>Ukuran</th>
                        <th>Motor</th>
                        <th>Warna</th>
                        <?php if ($this->session->userdata('id_role') == 1): ?>
                            <th>Perusahaan</th>
                        <?php endif; ?>
                        <th>Total Stok</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;
                    foreach ($barang as $row): ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td>
                                <?php if ($row->gambar): ?>
                                    <img src="<?php echo base_url('uploads/barang/' . $row->gambar); ?>" class="img-thumbnail"
                                        style="max-width: 50px;" alt="Gambar Barang">
                                <?php else: ?>
                                    <span class="text-muted">Tidak ada</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo $row->sku; ?></td>
                            <td><?php echo $row->kode_barang ?: '-'; ?></td>
                            <td><?php echo $row->nama_barang; ?></td>
                            <td><?php echo $row->nama_kategori ?: '-'; ?></td>
                            <td><?php echo $row->ukuran ?: '-'; ?></td>
                            <td><?php echo $row->motor ?: '-'; ?></td>
                            <td><?php echo $row->warna ?: '-'; ?></td>
                            <?php if ($this->session->userdata('id_role') == 1): ?>
                                <td><?php echo $row->nama_perusahaan; ?></td>
                            <?php endif; ?>

                            <td>
                                <?php
                                $total_stok = isset($row->total_stok) ? $row->total_stok : 0;
                                $total_reserved = isset($row->total_reserved) ? $row->total_reserved : 0;
                                $stok_tersedia = $total_stok - $total_reserved;

                                // Tentukan warna badge stok
                                if ($stok_tersedia <= 0) {
                                    $badge_class = 'danger'; // habis
                                } elseif ($stok_tersedia <= 5) {
                                    $badge_class = 'warning'; // sedikit
                                } else {
                                    $badge_class = 'success'; // cukup
                                }
                                ?>
                                <span class="badge badge-<?php echo $badge_class; ?> p-2">
                                    <?php echo $stok_tersedia . ' ' . $row->satuan; ?>
                                </span>
                                <?php if ($total_reserved > 0): ?>
                                    <span class="badge badge-secondary">
                                        Reserved: <?php echo $total_reserved; ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($row->status_aktif == 1): ?>
                                    <span class="badge badge-success">Aktif</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Tidak Aktif</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?php echo site_url('setup/barang/detail/' . $row->id_barang); ?>"
                                    class="btn btn-sm btn-info" title="Detail">
                                    <i class="fas fa-info-circle"></i>
                                </a>
                                <a href="<?php echo site_url('setup/barang/stok/' . $row->id_barang); ?>"
                                    class="btn btn-sm btn-primary" title="Kelola Stok">
                                    <i class="fas fa-boxes"></i>
                                </a>
                                <a href="<?php echo site_url('setup/barang/edit/' . $row->id_barang); ?>"
                                    class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <?php if ($row->status_aktif == '1'): ?>
                                    <a href="<?php echo site_url('setup/barang/nonaktif/' . $row->id_barang) ?>"
                                        class="btn btn-sm btn-danger"
                                        onclick="return confirm('Apakah Anda yakin ingin menonaktifkan barang ini?')">
                                        <i class="fas fa-minus-square"></i>
                                    </a>
                                <?php else: ?>
                                    <a href="<?php echo site_url('setup/barang/aktif/' . $row->id_barang) ?>"
                                        class="btn btn-sm btn-success"
                                        onclick="return confirm('Apakah Anda yakin ingin mengaktifkan kembali barang ini?')">
                                        <i class="fas fa-check-square"></i>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>