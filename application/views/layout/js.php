<!-- Core plugin JavaScript-->
<script src="<?php echo base_url('/assets/js/jquery.easing.min.js'); ?>"></script>

<!-- SB Admin 2 -->
<script src="<?php echo base_url('/assets/js/sb-admin-2.min.js'); ?>"></script>

<!-- DataTables JS -->
<script src="<?php echo base_url('/assets/js/jquery.dataTables.min.js'); ?>"></script>
<script src="<?php echo base_url('/assets/js/dataTables.bootstrap4.min.js'); ?>"></script>

<!-- Page level custom scripts -->
<script>
    // Show notification
    function showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        notification.style.top = '20px';
        notification.style.right = '20px';
        notification.style.zIndex = '9999';
        notification.style.minWidth = '250px';
        notification.innerHTML = `
            ${message}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        `;
        document.body.appendChild(notification);
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => {
                if (document.body.contains(notification)) {
                    document.body.removeChild(notification);
                }
            }, 150);
        }, 3000);
    }

    // Fungsi untuk mengeksekusi script setelah jQuery dan Bootstrap siap
    function runAfterDependencies(callback) {
        if (window.jQuery && typeof jQuery.fn.modal === 'function') {
            callback();
        } else {
            setTimeout(function () {
                runAfterDependencies(callback);
            }, 100);
        }
    }

    // Inisialisasi DataTables
    runAfterDependencies(function () {
        if (typeof $.fn.DataTable !== 'undefined') {
            $('#dataTable').DataTable({
                responsive: true,
                paging: true,
                ordering: false,
                info: true,
                scrollX: true,
                autoWidth: false,
                dom: '<"row mb-3"<"col-md-6 d-flex align-items-center"l><"col-md-6 d-flex justify-content-end"f>>' +
                    'rt' +
                    '<"row mt-3"<"col-md-6"i><"col-md-6 d-flex justify-content-end"p>>',
            });
        }
    });
    $(document).ready(function () {
        // Handle dropdown menus in mobile view
        if ($(window).width() < 992) {
            $('.dropdown-menu a.dropdown-toggle').on('click', function (e) {
                if (!$(this).next().hasClass('show')) {
                    $(this).parents('.dropdown-menu').first().find('.show').removeClass("show");
                }
                var $subMenu = $(this).next(".dropdown-menu");
                $subMenu.toggleClass('show');

                $(this).parents('li.nav-item.dropdown.show').on('hidden.bs.dropdown', function (e) {
                    $('.dropdown-submenu .show').removeClass("show");
                });

                return false;
            });
        }

        // Handle window resize
        $(window).resize(function () {
            if ($(window).width() >= 992) {
                $('.dropdown-menu').removeClass('show');
            }
        });
    });

</script>