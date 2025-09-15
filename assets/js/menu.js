$(document).ready(function () {
	// Tunggu Bootstrap dimuat
	setTimeout(function () {
		// Handle dropdown toggle clicks
		$(".dropdown-toggle").on("click", function (e) {
			e.preventDefault();
			e.stopPropagation();

			var $this = $(this);
			var $dropdown = $this.next(".dropdown-menu");

			// Close other dropdowns
			$(".dropdown-menu").not($dropdown).removeClass("show");
			$(".dropdown-toggle").not($this).removeClass("show");

			// Toggle current dropdown
			$dropdown.toggleClass("show");
			$this.toggleClass("show");
		});

		// Close dropdowns when clicking outside
		$(document).on("click", function (e) {
			if (!$(e.target).closest(".dropdown").length) {
				$(".dropdown-menu").removeClass("show");
				$(".dropdown-toggle").removeClass("show");
			}
		});

		// Handle dropdown item clicks - biarkan link berfungsi normal
		$(".dropdown-item").on("click", function (e) {
			// Jangan cegah default behavior untuk link
			// Biarkan link berfungsi normal
		});

		console.log("Menu script loaded successfully");
	}, 100);
});
