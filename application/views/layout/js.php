<script>
    $(document).ready(function () {
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

        // Inisialisasi DataTables
        if ($.fn.DataTable) {
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

        // Dropdown mobile
        if ($(window).width() < 992) {
            $('.dropdown-menu a.dropdown-toggle').on('click', function (e) {
                if (!$(this).next().hasClass('show')) {
                    $(this).parents('.dropdown-menu').first().find('.show').removeClass("show");
                }
                var $subMenu = $(this).next(".dropdown-menu");
                $subMenu.toggleClass('show');

                $(this).parents('li.nav-item.dropdown.show').on('hidden.bs.dropdown', function () {
                    $('.dropdown-submenu .show').removeClass("show");
                });

                return false;
            });
        }

        $(window).resize(function () {
            if ($(window).width() >= 992) {
                $('.dropdown-menu').removeClass('show');
            }
        });
    });
</script>