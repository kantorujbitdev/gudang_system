<div class="card shadow mb-4">
    <div class="card-header py-3">
        <div class="row">
            <div class="col">
                <?php echo responsive_title_blue('Diagram Approval Flow') ?>
            </div>
            <div class="col text-right">
                <a href="<?php echo site_url('pengaturan/approval'); ?>" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <?php
            $tipe_transaksi = '';
            $flows_by_tipe = [];

            foreach ($approval_flows as $flow) {
                if ($flow->tipe_transaksi != $tipe_transaksi) {
                    $tipe_transaksi = $flow->tipe_transaksi;
                    $flows_by_tipe[$tipe_transaksi] = [];
                }
                $flows_by_tipe[$tipe_transaksi][] = $flow;
            }

            foreach ($flows_by_tipe as $tipe => $flows):
                ?>
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h6 class="m-0 font-weight-bold"><?php echo ucfirst(str_replace('_', ' ', $tipe)); ?></h6>
                        </div>
                        <div class="card-body">
                            <div class="flow-diagram">
                                <?php
                                $status_colors = [
                                    'Draft' => 'secondary',
                                    'Packing' => 'info',
                                    'Shipping' => 'warning',
                                    'Delivered' => 'success',
                                    'Cancelled' => 'danger',
                                    'Received' => 'info',
                                    'Completed' => 'success',
                                    'Requested' => 'secondary',
                                    'Verification' => 'info',
                                    'Approved' => 'warning',
                                    'Rejected' => 'danger'
                                ];

                                $current_status = 'Draft';
                                echo '<div class="flow-status text-center mb-2">';
                                echo '<div class="badge badge-' . $status_colors[$current_status] . ' p-2">' . $current_status . '</div>';
                                echo '</div>';

                                foreach ($flows as $flow) {
                                    echo '<div class="flow-arrow text-center mb-2">';
                                    echo '<i class="fas fa-arrow-down"></i>';
                                    echo '<div class="small">' . $flow->nama_role . '</div>';
                                    echo '</div>';

                                    echo '<div class="flow-status text-center mb-2">';
                                    echo '<div class="badge badge-' . $status_colors[$flow->status_ke] . ' p-2">' . $flow->status_ke . '</div>';
                                    echo '</div>';

                                    $current_status = $flow->status_ke;
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<style>
    .flow-diagram {
        padding: 20px;
    }

    .flow-status {
        margin: 10px 0;
    }

    .flow-status .badge {
        display: inline-block;
        min-width: 120px;
    }

    .flow-arrow {
        margin: 5px 0;
        height: 50px;
        position: relative;
    }

    .flow-arrow i {
        font-size: 24px;
        color: #6c757d;
    }

    .flow-arrow .small {
        font-size: 12px;
        margin-top: 5px;
    }
</style>