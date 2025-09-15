// Custom JavaScript untuk aplikasi

// Fungsi untuk format currency
function formatCurrency(amount) {
	return new Intl.NumberFormat("id-ID", {
		style: "currency",
		currency: "IDR",
		minimumFractionDigits: 0,
	}).format(amount);
}

// Fungsi untuk format tanggal
function formatDate(date) {
	const options = { year: "numeric", month: "long", day: "numeric" };
	return new Date(date).toLocaleDateString("id-ID", options);
}

// Fungsi untuk format datetime
function formatDateTime(date) {
	const options = {
		year: "numeric",
		month: "long",
		day: "numeric",
		hour: "2-digit",
		minute: "2-digit",
	};
	return new Date(date).toLocaleDateString("id-ID", options);
}

// Auto format input currency
$(document).on("input", ".currency-input", function () {
	let value = $(this).val().replace(/\D/g, "");
	$(this).val(formatCurrency(value));
});

// Auto format input number
$(document).on("input", ".number-input", function () {
	let value = $(this).val().replace(/\D/g, "");
	$(this).val(value);
});

// Initialize tooltips
$(function () {
	$('[data-toggle="tooltip"]').tooltip();
});

// Initialize popovers
$(function () {
	$('[data-toggle="popover"]').popover();
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

	$(".datetimepicker").datetimepicker({
		format: "dd/mm/yyyy hh:ii",
		autoclose: true,
		todayHighlight: true,
		language: "id",
	});
});

// Confirm delete
$(document).on("click", ".btn-delete", function (e) {
	e.preventDefault();
	const url = $(this).attr("href");
	const message =
		$(this).data("confirm") || "Apakah Anda yakin ingin menghapus data ini?";

	confirmAction(message, function () {
		window.location.href = url;
	});
});

// Submit form with loading
$(document).on("submit", "form", function () {
	const form = $(this);
	if (form.valid()) {
		showLoading();
	}
});

// Close alert automatically
setTimeout(function () {
	$(".alert").fadeOut("slow");
}, 5000);

// Print function
function printDiv(divName) {
	const printContents = document.getElementById(divName).innerHTML;
	const originalContents = document.body.innerHTML;

	document.body.innerHTML = printContents;
	window.print();
	document.body.innerHTML = originalContents;
}

// Export to Excel
function exportToExcel(tableId, filename) {
	const dataType = "application/vnd.ms-excel";
	const tableSelect = document.getElementById(tableId);
	const tableHTML = tableSelect.outerHTML.replace(/ /g, "%20");

	// Specify file name
	filename = filename ? filename + ".xls" : "export_data.xls";

	// Create download link element
	const downloadLink = document.createElement("a");

	document.body.appendChild(downloadLink);

	if (navigator.msSaveOrOpenBlob) {
		const blob = new Blob(["\ufeff", tableHTML], {
			type: dataType,
		});
		navigator.msSaveOrOpenBlob(blob, filename);
	} else {
		// Create a link to the file
		downloadLink.href = "data:" + dataType + ", " + tableHTML;

		// Setting the file name
		downloadLink.download = filename;

		// Triggering the function
		downloadLink.click();
	}
}

// Auto resize textarea
$(document).ready(function () {
	$("textarea")
		.each(function () {
			this.setAttribute(
				"style",
				"height:" + this.scrollHeight + "px;overflow-y:hidden;"
			);
		})
		.on("input", function () {
			this.style.height = "auto";
			this.style.height = this.scrollHeight + "px";
		});
});

// Copy to clipboard
function copyToClipboard(text) {
	const dummy = document.createElement("textarea");
	document.body.appendChild(dummy);
	dummy.value = text;
	dummy.select();
	document.execCommand("copy");
	document.body.removeChild(dummy);

	// Show notification
	alert("Teks telah disalin ke clipboard!");
}
