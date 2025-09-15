$(document).ready(function () {
	// Tunggu Bootstrap dimuat
	setTimeout(function () {
		// Handle sidebar toggle
		$("#sidebarToggle, #sidebarToggleTop").on("click", function (e) {
			e.preventDefault();
			$("body").toggleClass("sidebar-toggled");
			$(".sidebar").toggleClass("toggled");
			if ($(".sidebar").hasClass("toggled")) {
				$(".sidebar .collapse").collapse("hide");
			} else {
				// If sidebar is being opened, close the top menu on mobile
				if ($(window).width() < 768) {
					$("#topMenu").collapse("hide");
				}
			}
		});

		// Handle dropdown toggle clicks in sidebar
		$(".sidebar .dropdown-toggle").on("click", function (e) {
			e.preventDefault();
			e.stopPropagation();
			var $this = $(this);
			var $dropdown = $this.next(".dropdown-menu");

			// Close other dropdowns
			$(".sidebar .dropdown-menu").not($dropdown).removeClass("show");
			$(".sidebar .dropdown-toggle").not($this).removeClass("show");

			// Toggle current dropdown
			$dropdown.toggleClass("show");
			$this.toggleClass("show");
		});

		// Handle top menu toggle
		$(".navbar-toggler").on("click", function (e) {
			// If sidebar is open on mobile, close it when opening top menu
			if ($(window).width() < 768 && $(".sidebar").hasClass("toggled")) {
				$("body").removeClass("sidebar-toggled");
				$(".sidebar").removeClass("toggled");
			}
		});

		// Close dropdowns when clicking outside
		$(document).on("click", function (e) {
			// Close sidebar dropdowns
			if (!$(e.target).closest(".sidebar").length) {
				$(".sidebar .dropdown-menu").removeClass("show");
				$(".sidebar .dropdown-toggle").removeClass("show");
			}

			// Close top menu dropdowns
			if (!$(e.target).closest(".navbar").length) {
				$(".navbar .dropdown-menu").removeClass("show");
				$(".navbar .dropdown-toggle").removeClass("show");
			}
		});

		// Close sidebar when clicking outside on mobile
		$(document).on("click", function (e) {
			if ($(window).width() < 768) {
				if (
					!$(e.target).closest(".sidebar").length &&
					!$(e.target).closest("#sidebarToggle, #sidebarToggleTop").length &&
					$(".sidebar").hasClass("toggled")
				) {
					$("body").removeClass("sidebar-toggled");
					$(".sidebar").removeClass("toggled");
				}
			}
		});

		// Handle window resize
		$(window).resize(function () {
			if ($(window).width() >= 768) {
				// Reset sidebar state on desktop
				$("body").removeClass("sidebar-toggled");
				$(".sidebar").removeClass("toggled");
			} else {
				// Hide sidebar by default on mobile
				$("body").addClass("sidebar-toggled");
				$(".sidebar").addClass("toggled");
			}
		});

		// Initialize sidebar state based on screen size
		if ($(window).width() < 768) {
			// Hide sidebar by default on mobile
			$("body").addClass("sidebar-toggled");
			$(".sidebar").addClass("toggled");
		}

		console.log("Menu script loaded successfully");
	}, 100);
});
