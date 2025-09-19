<div class="card shadow mb-4">
    <div class="card-header py-3">
        <div class="row">
            <div class="col">
                <?php echo responsive_title_blue('Approval Flow') ?>
            </div>
            <div class="col text-right">
                <a href="<?php echo site_url('pengaturan/approval/diagram'); ?>" class="btn btn-info btn-sm">
                    <i class="fas fa-project-diagram"></i> Lihat Diagram
                </a>
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
                        <th>Tipe Transaksi</th>
                        <th>Status Dari</th>
                        <th>Status Ke</th>
                        <th>Role</th>
                        <th>Urutan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;
                    foreach ($approval_flows as $row): ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo ucfirst(str_replace('_', ' ', $row->tipe_transaksi)); ?></td>
                            <td><?php echo $row->status_dari; ?></td>
                            <td><?php echo $row->status_ke; ?></td>
                            <td><?php echo $row->nama_role; ?></td>
                            <td><?php echo $row->urutan; ?></td>
                            <td>
                                <?php if ($can_edit): ?>
                                    <a href="<?php echo site_url('pengaturan/approval/edit/' . $row->tipe_transaksi); ?>"
                                        class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
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