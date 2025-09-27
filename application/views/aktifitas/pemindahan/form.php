<?php
// Load data master JavaScript
echo isset($data_master_js) ? $data_master_js : '';
?>
<script src="<?php echo base_url('assets/js/data_master.js'); ?>"></script>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary"><?php echo $title; ?></h6>
    </div>
    <div class="card-body">
        <?php echo form_open(current_url(), array('id' => 'form-pemindahan')); ?>

        <!-- Hidden field for tanggal pemindahan -->
        <input type="hidden" id="tanggal_pemindahan" name="tanggal_pemindahan"
            value="<?php echo date('Y-m-d H:i:s'); ?>">

        <!-- Informasi Transaksi -->
        <?php if ($this->session->userdata('id_role') == 1): // Super Admin ?>
            <div class="row mb-4">
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
                </div>
            </div>
        <?php else: ?>
            <div class="row mb-4">
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
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <div class="form-group">
                            <label for="tipe_tujuan">Tipe Tujuan <span class="text-danger">*</span></label>
                            <select class="form-control" id="tipe_tujuan" name="tipe_tujuan" required>
                                <option value="konsumen" <?php echo (!isset($pemindahan) || $pemindahan->tipe_tujuan == 'konsumen') ? 'selected' : ''; ?>>Konsumen</option>
                                <option value="pelanggan" <?php echo (isset($pemindahan) && $pemindahan->tipe_tujuan == 'pelanggan') ? 'selected' : ''; ?>>Pelanggan</option>
                                <option value="gudang" <?php echo (isset($pemindahan) && $pemindahan->tipe_tujuan == 'gudang') ? 'selected' : ''; ?>>Gudang</option>
                            </select>
                            <?php echo form_error('tipe_tujuan', '<small class="text-danger">', '</small>'); ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Barang Dipindahkan -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Barang Dipindahkan</h6>
                <div>
                    <span class="badge badge-success mr-2" id="jumlah-barang-dipindahkan">0 barang</span>
                    <button type="button" class="btn btn-sm btn-primary" id="btn-tambah-barang-modal"
                        data-toggle="modal" data-target="#modalBarang">
                        <i class="fas fa-plus"></i> Tambah Barang
                    </button>
                </div>
            </div>
            <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover compact small"
                        id="table_barang_dipindahkan">
                        <thead>
                            <tr>
                                <th width="4%">No</th>
                                <th width="20%">Nama Barang</th>
                                <th width="10%">Kode Barang</th>
                                <th width="10%">SKU</th>
                                <th width="8%">Ukuran</th>
                                <th width="15%">Motor</th>
                                <th width="10%">Warna</th>
                                <th width="8%">Jumlah</th>
                                <th width="6%">Satuan</th>
                                <th width="9%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (isset($detail)): ?>
                                <?php foreach ($detail as $key => $item): ?>
                                    <tr>
                                        <td><?php echo $key + 1; ?></td>
                                        <td><?php echo $item->nama_barang; ?></td>
                                        <td><?php echo $item->kode_barang ?: '-'; ?></td>
                                        <td><?php echo $item->sku; ?></td>
                                        <td><?php echo $item->ukuran ?: '-'; ?></td>
                                        <td><?php echo $item->motor ?: '-'; ?></td>
                                        <td><?php echo $item->warna ?: '-'; ?></td>
                                        <td>
                                            <input type="number" class="form-control form-control-sm jumlah-barang"
                                                name="jumlah[]" value="<?php echo $item->jumlah; ?>" min="1" required
                                                data-id_barang="<?php echo $item->id_barang; ?>">
                                        </td>
                                        <td><?php echo $item->satuan; ?></td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-danger btn-hapus-barang"
                                                data-id_barang="<?php echo $item->id_barang; ?>">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Informasi Transaksi -->
        <div class="row mb-4">
            <div class="col-md-6">
                <!-- Gudang Tujuan -->
                <div id="gudang_tujuan_field" style="display: none;">
                    <div class="form-group">
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
                </div>

                <!-- Pelanggan -->
                <div id="pelanggan_field" style="display: none;">
                    <div class="form-group">
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
                </div>

                <!-- Konsumen -->
                <div id="konsumen_field" style="display: none;">
                    <div class="form-group">
                        <label for="id_toko_konsumen">Toko</label>
                        <select class="form-control" id="id_toko_konsumen" name="id_toko_konsumen">
                            <option value="">-- Pilih Toko --</option>
                            <?php foreach ($toko_konsumen as $row): ?>
                                <option value="<?php echo $row->id_toko_konsumen; ?>" <?php echo (isset($pemindahan) && $pemindahan->id_konsumen && $pemindahan->id_toko_konsumen == $row->id_toko_konsumen) ? 'selected' : ''; ?>>
                                    <?php echo $row->nama_toko_konsumen; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="nama_konsumen">Nama Konsumen</label>
                        <input type="text" class="form-control" id="nama_konsumen" name="nama_konsumen"
                            value="<?php echo set_value('nama_konsumen', isset($pemindahan) && $pemindahan->id_konsumen ? $pemindahan->nama_konsumen : ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="alamat_konsumen">Alamat Konsumen</label>
                        <textarea class="form-control" id="alamat_konsumen" name="alamat_konsumen"
                            rows="3"><?php echo set_value('alamat_konsumen', isset($pemindahan) && $pemindahan->id_konsumen ? $pemindahan->alamat_konsumen : ''); ?></textarea>
                    </div>
                </div>
            </div>
            <div class="col-md-6">

                <div class="form-group">
                    <label for="keterangan">Keterangan</label>
                    <textarea class="form-control" id="keterangan" name="keterangan"
                        rows="2"><?php echo set_value('keterangan', isset($pemindahan) ? $pemindahan->keterangan : ''); ?></textarea>
                </div>
            </div>
        </div>

        <input type="hidden" id="barang_dipindahkan" name="barang_dipindahkan" value="">
        <div class="form-group text-right mt-4">
            <a href="<?php echo site_url('aktifitas/pemindahan'); ?>" class="btn btn-secondary">
                <i class="fas fa-times"></i> Batal
            </a>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Simpan
            </button>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>

<!-- Modal untuk Pilih Barang -->
<div class="modal fade" id="modalBarang" tabindex="-1" role="dialog" aria-labelledby="modalBarangLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalBarangLabel">Pilih Barang</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <div class="input-group">
                        <input type="text" class="form-control" id="cari_barang_modal"
                            placeholder="Ketik nama barang, SKU, atau motor">
                        <div class="input-group-append">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover compact small" id="table_barang_modal">
                        <thead>
                            <tr>
                                <th width="4%">No</th>
                                <th width="20%">Nama Barang</th>
                                <th width="10%">Kode Barang</th>
                                <th width="10%">SKU</th>
                                <th width="8%">Ukuran</th>
                                <th width="15%">Motor</th>
                                <th width="10%">Warna</th>
                                <th width="8%">Stok</th>
                                <th width="6%">Satuan</th>
                                <th width="9%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data akan diisi via JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>