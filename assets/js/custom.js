// Custom JavaScript untuk aplikasi
// Fungsi untuk format currency
function formatCurrency(amount) {
	return new Intl.NumberFormat("id-ID", {
		style: "currency",
		currency: "IDR",
		minimumFractionDigits: 0,
	}).format(amount);
}

// Auto format input currency
$(document).on("input", ".currency-input", function () {
	let value = $(this).val().replace(/\D/g, "");
	$(this).val(formatCurrency(value));
});

// Initialize DataTables
$(document).ready(function () {
	$(".datatable").DataTable({
		responsive: true,
		pageLength: 10,
		language: {
			url: "//cdn.datatables.net/plug-ins/1.10.21/i18n/Indonesian.json",
		},
	});
});

// Initialize Select2
$(document).ready(function () {
	$(".select2").select2({
		theme: "bootstrap4",
		placeholder: "Pilih opsi",
		allowClear: true,
	});
});

// Initialize Datepicker
$(document).ready(function () {
	$(".datepicker").datepicker({
		format: "dd/mm/yyyy",
		autoclose: true,
		todayHighlight: true,
		language: "id",
	});
});

// Close responsive menu
$(document).click(function (e) {
	if (!$(e.target).is(".navbar-nav, .navbar-nav *")) {
		$(".navbar-collapse").collapse("hide");
	}
});

// Auto hide alerts
setTimeout(function () {
	$(".alert").fadeOut("slow");
}, 5000);

// Handle sidebar toggle on mobile
$(document).ready(function () {
	// Check if window width is less than 768px
	if ($(window).width() < 768) {
		// Hide sidebar by default on mobile
		$("body").addClass("sidebar-toggled");
		$(".sidebar").addClass("toggled");
	}

	// Handle window resize
	$(window).resize(function () {
		if ($(window).width() < 768) {
			// Hide sidebar by default on mobile
			$("body").addClass("sidebar-toggled");
			$(".sidebar").addClass("toggled");
		} else {
			// Show sidebar on desktop
			$("body").removeClass("sidebar-toggled");
			$(".sidebar").removeClass("toggled");
		}
	});
});
