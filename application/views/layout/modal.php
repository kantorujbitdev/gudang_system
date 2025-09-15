<!-- Modal untuk konfirmasi -->
<div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="confirmMessage">Apakah Anda yakin ingin melanjutkan?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="confirmButton">Ya</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk loading -->
<div class="modal fade" id="loadingModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static"
    data-keyboard="false">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-body text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <p class="mt-2">Memproses...</p>
            </div>
        </div>
    </div>
</div>

<script>
    // Fungsi konfirmasi
    function confirmAction(message, callback) {
        $('#confirmMessage').text(message);
        $('#confirmModal').modal('show');

        $('#confirmButton').off('click').on('click', function () {
            $('#confirmModal').modal('hide');
            if (callback) callback();
        });
    }

    // Fungsi loading
    function showLoading() {
        $('#loadingModal').modal('show');
    }

    function hideLoading() {
        $('#loadingModal').modal('hide');
    }
</script>