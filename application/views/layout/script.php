<!-- Bootstrap core JavaScript-->
<script src="<?php echo base_url('assets/vendor/jquery/jquery.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/vendor/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>

<!-- Core plugin JavaScript-->
<script src="<?php echo base_url('assets/vendor/jquery-easing/jquery.easing.min.js'); ?>"></script>

<!-- Custom scripts -->
<script src="<?php echo base_url('assets/js/sb-admin-2.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/custom.js'); ?>"></script>

<!-- Page level plugins -->
<script src="<?php echo base_url('assets/vendor/datatables/js/jquery.dataTables.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/vendor/datatables/css/dataTables.bootstrap4.min.js'); ?>"></script>

<!-- Page level custom scripts -->
<script>
    // Close responsive menu
    $(document).click(function (e) {
        if (!$(e.target).is('.navbar-nav, .navbar-nav *')) {
            $('.navbar-collapse').collapse('hide');
        }
    });

    // Auto hide alerts
    setTimeout(function () {
        $(".alert").fadeOut('slow');
    }, 5000);
</script>
</body>

</html>