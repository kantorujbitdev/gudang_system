<div class="card shadow mb-4">
    <div class="card-header py-3">
        <div class="row">
            <div class="col">
                <h6 class="m-0 font-weight-bold text-primary"><?php echo $title; ?></h6>
            </div>
            <div class="col text-right">
                <a href="<?php echo site_url('pengaturan/approval'); ?>" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <?php echo form_open('pengaturan/approval/update'); ?>
        <input type="hidden" name="tipe_transaksi" value="<?php echo $tipe_transaksi; ?>">

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="20%">Status Dari</th>
                        <th width="20%">Status Ke</th>
                        <th width="25%">Role</th>
                        <th width="10%">Urutan</th>
                        <th width="10%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    $status_options = [
                        'Draft' => 'Draft',
                        'Packing' => 'Packing',
                        'Shipping' => 'Shipping',
                        'Delivered' => 'Delivered',
                        'Cancelled' => 'Cancelled',
                        'Received' => 'Received',
                        'Completed' => 'Completed',
                        'Requested' => 'Requested',
                        'Verification' => 'Verification',
                        'Approved' => 'Approved',
                        'Rejected' => 'Rejected'
                    ];

                    // Get existing flows
                    $existing_flows = [];
                    foreach ($approval_flows as $flow) {
                        $existing_flows[$flow->status_dari][$flow->status_ke] = $flow;
                    }

                    foreach ($status_options as $status_dari): ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td>
                                <strong><?php echo $status_dari; ?></strong>
                                <input type="hidden" name="status_dari[]" value="<?php echo $status_dari; ?>">
                            </td>
                            <td>
                                <select class="form-control" name="status_ke[]">
                                    <option value="">-- Pilih Status --</option>
                                    <?php foreach ($status_options as $status_ke): ?>
                                        <?php if ($status_ke != $status_dari): ?>
                                            <option value="<?php echo $status_ke; ?>" <?php echo (isset($existing_flows[$status_dari][$status_ke]) ? 'selected' : ''); ?>>
                                                <?php echo $status_ke; ?></option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td>
                                <select class="form-control" name="id_role[]">
                                    <option value="">-- Pilih Role --</option>
                                    <?php foreach ($roles as $role): ?>
                                        <option value="<?php echo $role->id_role; ?>" <?php echo (isset($existing_flows[$status_dari]) && isset($existing_flows[$status_dari][$status_ke]) && $existing_flows[$status_dari][$status_ke]->id_role == $role->id_role) ? 'selected' : ''; ?>><?php echo $role->nama_role; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td>
                                <input type="number" class="form-control" name="urutan[]"
                                    value="<?php echo (isset($existing_flows[$status_dari]) && isset($existing_flows[$status_dari][$status_ke]) ? $existing_flows[$status_dari][$status_ke]->urutan : ''); ?>"
                                    min="0">
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-danger btn-remove-row">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="form-group mt-3">
            <button type="button" class="btn btn-secondary" id="btn-add-row">
                <i class="fas fa-plus"></i> Tambah Baris
            </button>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </div>

        <?php echo form_close(); ?>
    </div>
</div>

<script>
    $(document).ready(function () {
        // Add row
        $('#btn-add-row').click(function () {
            var lastRow = $('tbody tr:last');
            var newRow = lastRow.clone();

            // Reset values
            newRow.find('select').val('');
            newRow.find('input[type="number"]').val('');

            // Update row number
            var no = parseInt(lastRow.find('td:first').text()) + 1;
            newRow.find('td:first').text(no);

            $('tbody').append(newRow);
        });

        // Remove row
        $(document).on('click', '.btn-remove-row', function () {
            if ($('tbody tr').length > 1) {
                $(this).closest('tr').remove();

                // Renumber rows
                $('tbody tr').each(function (index) {
                    $(this).find('td:first').text(index + 1);
                });
            } else {
                alert('Minimal harus ada 1 baris!');
            }
        });
    });
</script>