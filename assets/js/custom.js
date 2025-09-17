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

// Fungsi untuk mengeksekusi script setelah jQuery dan Bootstrap siap
function runAfterDependencies(callback) {
	if (window.jQuery && typeof jQuery.fn.modal === "function") {
		callback();
	} else {
		setTimeout(function () {
			runAfterDependencies(callback);
		}, 100);
	}
}

// Inisialisasi DataTables
runAfterDependencies(function () {
	if (typeof $.fn.DataTable !== "undefined") {
		$("#dataTable").DataTable({
			responsive: true,
			paging: true,
			ordering: false,
			info: true,
			scrollX: true,
			autoWidth: false,
			dom:
				'<"row mb-3"<"col-md-6 d-flex align-items-center"l><"col-md-6 d-flex justify-content-end"f>>' +
				"rt" +
				'<"row mt-3"<"col-md-6"i><"col-md-6 d-flex justify-content-end"p>>',
		});
	}
});

// Initialize Select2
$(document).ready(function () {
	$(".select2").select2({
		theme: "bootstrap4",
		placeholder: "Pilih opsi",
		allowClear: true,
	});
});

// Initialize Datepicker - only if datepicker is loaded
$(document).ready(function () {
	// Check if datepicker function exists
	if (typeof $.fn.datepicker === "function") {
		$(".datepicker").datepicker({
			format: "dd/mm/yyyy",
			autoclose: true,
			todayHighlight: true,
			language: "id",
		});
	} else {
		console.log("Datepicker library not loaded");
	}
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
