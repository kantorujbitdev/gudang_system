<div class="card shadow mb-4">
    <div class="card-header py-3">
        <div class="row">
            <div class="col">
                <h6 class="m-0 font-weight-bold text-primary"><?php echo $title; ?></h6>
            </div>
            <div class="col text-right">
                <a href="<?php echo site_url('pengaturan/sistem/log'); ?>" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <a href="<?php echo base_url('application/logs/' . $filename); ?>" class="btn btn-primary btn-sm"
                    download>
                    <i class="fas fa-download"></i> Download
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="card bg-light">
            <div class="card-body">
                <pre class="log-content"><?php echo htmlspecialchars($log_content); ?></pre>
            </div>
        </div>
    </div>
</div>

<style>
    .log-content {
        max-height: 500px;
        overflow-y: auto;
        font-family: monospace;
        font-size: 12px;
        white-space: pre-wrap;
        word-wrap: break-word;
    }
</style>