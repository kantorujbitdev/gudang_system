<div class="card shadow mb-4">
    <div class="card-header py-3">
        <div class="row">
            <div class="col">
                <?php echo responsive_title_blue('Log Sistem') ?>
            </div>
            <div class="col text-right">
                <button class="btn btn-danger btn-sm"
                    onclick="return confirm('Apakah Anda yakin ingin membersihkan semua log?')">
                    <i class="fas fa-trash"></i> Bersihkan Semua Log
                </button>
            </div>
        </div>
    </div>
    <div class="card-body">
        <?php if ($this->session->flashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $this->session->flashdata('success'); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <?php if ($this->session->flashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $this->session->flashdata('error'); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama File</th>
                        <th>Ukuran</th>
                        <th>Tanggal Modifikasi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;
                    foreach ($log_files as $file): ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo $file['name']; ?></td>
                            <td><?php echo number_format($file['size'] / 1024, 2); ?> KB</td>
                            <td><?php echo date('d-m-Y H:i:s', $file['modified']); ?></td>
                            <td>
                                <a href="<?php echo site_url('pengaturan/sistem/view_log/' . $file['name']); ?>"
                                    class="btn btn-sm btn-info" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="<?php echo base_url('application/logs/' . $file['name']); ?>"
                                    class="btn btn-sm btn-primary" title="Download" download>
                                    <i class="fas fa-download"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('.btn-danger').click(function () {
            if (confirm('Apakah Anda yakin ingin membersihkan semua log?')) {
                window.location.href = '<?php echo site_url('pengaturan/sistem/clear_log'); ?>';
            }
        });
    });
</script>