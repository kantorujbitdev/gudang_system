<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary"><?php echo $title; ?></h6>
    </div>
    <div class="card-body">
        <?php echo form_open(current_url(), array('id' => 'form-pemindahan')); ?>
        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>"
            value="<?php echo $this->security->get_csrf_hash(); ?>">

        <div class="row">
            <?php if ($this->session->userdata('id_role') == 1): // Super Admin ?>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="id_perusahaan">Perusahaan <span class="text-danger">*</span></label>
                        <select class="form-control" id="id_perusahaan" name="id_perusahaan" required>
                            <option value="">-- Pilih Perusahaan --</option>
                            <?php foreach ($perusahaan as $row): ?>
                                <option value="<?php echo $row->id_perusahaan; ?>" <?php echo (isset($pemindahan) && $pemindahan->id_perusahaan == $row->id_perusahaan) ? 'selected' : ''; ?>>
                                    <?php echo $row->nama_perusahaan; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php echo form_error('id_perusahaan', '<small class="text-danger">', '</small>'); ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="tanggal_pemindahan">Tanggal Pemindahan</label>
                    <input type="text" class="form-control" id="tanggal_pemindahan" name="tanggal_pemindahan"
                        value="<?php echo date('d-m-Y H:i:s'); ?>" readonly>
                    <small class="text-muted">Waktu real, tidak dapat diubah</small>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="id_gudang_asal">Gudang Asal <span class="text-danger">*</span></label>
                    <select class="form-control" id="id_gudang_asal" name="id_gudang_asal" required>
                        <option value="">-- Pilih Gudang --</option>
                        <?php foreach ($gudang as $row): ?>
                            <option value="<?php echo $row->id_gudang; ?>" <?php echo (isset($pemindahan) && $pemindahan->id_gudang_asal == $row->id_gudang) ? 'selected' : ''; ?>>
                                <?php echo $row->nama_gudang; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php echo form_error('id_gudang_asal', '<small class="text-danger">', '</small>'); ?>
                </div>

                <div class="form-group">
                    <label for="tipe_tujuan">Tipe Tujuan <span class="text-danger">*</span></label>
                    <select class="form-control" id="tipe_tujuan" name="tipe_tujuan" required>
                        <option value="">-- Pilih Tipe --</option>
                        <option value="gudang" <?php echo (isset($pemindahan) && $pemindahan->tipe_tujuan == 'gudang') ? 'selected' : ''; ?>>Gudang</option>
                        <option value="pelanggan" <?php echo (isset($pemindahan) && $pemindahan->tipe_tujuan == 'pelanggan') ? 'selected' : ''; ?>>Pelanggan</option>
                        <option value="konsumen" <?php echo (isset($pemindahan) && $pemindahan->tipe_tujuan == 'konsumen') ? 'selected' : ''; ?>>Konsumen</option>
                    </select>
                    <?php echo form_error('tipe_tujuan', '<small class="text-danger">', '</small>'); ?>
                </div>

                <!-- Lokasi Tujuan -->
                <div class="form-group">
                    <label>Lokasi Tujuan</label>

                    <!-- Gudang Tujuan -->
                    <div id="gudang_tujuan_field" style="display: none;">
                        <label for="id_gudang_tujuan">Gudang Tujuan</label>
                        <select class="form-control" id="id_gudang_tujuan" name="id_gudang_tujuan">
                            <option value="">-- Pilih Gudang Tujuan --</option>
                            <?php foreach ($gudang as $row): ?>
                                <option value="<?php echo $row->id_gudang; ?>" <?php echo (isset($pemindahan) && $pemindahan->id_gudang_tujuan == $row->id_gudang) ? 'selected' : ''; ?>>
                                    <?php echo $row->nama_gudang; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Pelanggan -->
                    <div id="pelanggan_field" style="display: none;">
                        <label for="id_pelanggan">Pelanggan</label>
                        <select class="form-control" id="id_pelanggan" name="id_pelanggan">
                            <option value="">-- Pilih Pelanggan --</option>
                            <?php foreach ($pelanggan as $row): ?>
                                <option value="<?php echo $row->id_pelanggan; ?>" <?php echo (isset($pemindahan) && $pemindahan->id_pelanggan == $row->id_pelanggan) ? 'selected' : ''; ?>>
                                    <?php echo $row->nama_pelanggan; ?> (<?php echo $row->tipe_pelanggan; ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <div id="alamat_pelanggan" class="mt-2" style="display: none;">
                            <div class="card card-body bg-light">
                                <h6>Alamat Pelanggan:</h6>
                                <p id="alamat_text"></p>
                                <p><strong>Telepon:</strong> <span id="telepon_text"></span></p>
                                <p><strong>Email:</strong> <span id="email_text"></span></p>
                            </div>
                        </div>
                    </div>

                    <!-- Konsumen -->
                    <div id="konsumen_field" style="display: none;">
                        <label for="nama_konsumen">Nama Konsumen</label>
                        <input type="text" class="form-control" id="nama_konsumen" name="nama_konsumen"
                            value="<?php echo set_value('nama_konsumen', isset($pemindahan) ? $pemindahan->nama_konsumen : ''); ?>">

                        <label for="toko_konsumen">Toko</label>
                        <input type="text" class="form-control" id="toko_konsumen" name="toko_konsumen"
                            value="<?php echo set_value('toko_konsumen', isset($pemindahan) ? $pemindahan->toko_konsumen : ''); ?>">

                        <label for="alamat_konsumen">Alamat Konsumen</label>
                        <textarea class="form-control" id="alamat_konsumen" name="alamat_konsumen"
                            rows="3"><?php echo set_value('alamat_konsumen', isset($pemindahan) ? $pemindahan->alamat_konsumen : ''); ?></textarea>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="keterangan">Keterangan</label>
                    <textarea class="form-control" id="keterangan" name="keterangan"
                        rows="3"><?php echo set_value('keterangan', isset($pemindahan) ? $pemindahan->keterangan : ''); ?></textarea>
                </div>
            </div>
        </div>

        <hr>

        <h6>Daftar Barang</h6>
        <div class="table-responsive mb-3">
            <table class="table table-bordered" id="table_barang">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="40%">Barang</th>
                        <th width="15%">Stok Tersedia</th>
                        <th width="15%">Jumlah</th>
                        <th width="10%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($detail)): ?>
                        <?php foreach ($detail as $key => $item): ?>
                            <tr>
                                <td><?php echo $key + 1; ?></td>
                                <td>
                                    <select class="form-control select-barang" name="id_barang[]" required>
                                        <option value="">-- Pilih Barang --</option>
                                        <?php foreach ($barang as $row): ?>
                                            <option value="<?php echo $row->id_barang; ?>" <?php echo ($item->id_barang == $row->id_barang) ? 'selected' : ''; ?>>
                                                <?php echo $row->nama_barang; ?> (<?php echo $row->sku; ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" class="form-control stok-tersedia" readonly>
                                </td>
                                <td>
                                    <input type="number" class="form-control" name="jumlah[]"
                                        value="<?php echo $item->jumlah; ?>" min="1" required>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-danger btn-hapus-barang"><i
                                            class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td>1</td>
                            <td>
                                <select class="form-control select-barang" name="id_barang[]" required>
                                    <option value="">-- Pilih Barang --</option>
                                    <?php foreach ($barang as $row): ?>
                                        <option value="<?php echo $row->id_barang; ?>"><?php echo $row->nama_barang; ?>
                                            (<?php echo $row->sku; ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td>
                                <input type="text" class="form-control stok-tersedia" readonly>
                            </td>
                            <td>
                                <input type="number" class="form-control" name="jumlah[]" min="1" required>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-danger btn-hapus-barang"><i
                                        class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="form-group">
            <button type="button" class="btn btn-secondary" id="btn-tambah-barang"><i class="fas fa-plus"></i> Tambah
                Barang</button>
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="<?php echo site_url('aktifitas/pemindahan'); ?>" class="btn btn-secondary">Batal</a>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>

<!-- Modal Tambah Alamat -->
<div class="modal fade" id="modalTambahAlamat" tabindex="-1" role="dialog" aria-labelledby="modalTambahAlamatLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTambahAlamatLabel">Tambah Alamat Baru</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form-tambah-alamat">
                    <div class="form-group">
                        <label for="alamat_lengkap">Alamat Lengkap <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="alamat_lengkap" name="alamat_lengkap" rows="3"
                            required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="keterangan_alamat">Keterangan</label>
                        <input type="text" class="form-control" id="keterangan_alamat" name="keterangan_alamat">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btn-simpan-alamat">Simpan</button>
            </div>
        </div>
    </div>
</div>