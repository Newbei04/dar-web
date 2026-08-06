<?php
// Start output buffering
ob_start();

// Load page from template
require_once "navbar.php";

?>
<!DOCTYPE html>
<html lang="en">

<head>
	<!--Title-->
	<title>DAR Web</title>

	<!-- Meta -->
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="author" content="DexignZone">
	<meta name="robots" content="index, follow">

	<meta name="keywords" content="Mophy, Payment Admin Dashboard, Bootstrap Template, FrontEnd, Web Application, Payment Management, Responsive Design, User Experience, Customizable, Modern UI, Dashboard Template, Admin Panel, Bootstrap 4, HTML5, CSS3, JavaScript, Finance, Payment Gateway, Admin Template, UI Kit, SASS, SCSS, CRM, Analytics, Responsive Dashboard">

	<meta name="description" content="Explore the power of Mophy – a sleek and feature-rich Payment Admin Dashboard Bootstrap Template with a seamlessly integrated FrontEnd.">

	<meta property="og:title" content="Mophy - Payment Admin Dashboard Bootstrap Template + FrontEnd | DexignZone">
	<meta property="og:description" content="Explore the power of Mophy – a sleek and feature-rich Payment Admin Dashboard Bootstrap Template with a seamlessly integrated FrontEnd.">
	<meta property="og:image" content="https://mophy.dexignzone.com/xhtml/social-image.png">

	<meta name="format-detection" content="telephone=no">

	<meta name="twitter:title" content="Mophy - Payment Admin Dashboard Bootstrap Template + FrontEnd | DexignZone">
	<meta name="twitter:description" content="Explore the power of Mophy – a sleek and feature-rich Payment Admin Dashboard Bootstrap Template with a seamlessly integrated FrontEnd.">
	<meta name="twitter:image" content="https://mophy.dexignzone.com/xhtml/social-image.png">
	<meta name="twitter:card" content="summary_large_image">

	<!-- MOBILE SPECIFIC -->
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- Favicon icon -->
	<link rel="icon" type="image/png" sizes="16x16" href="<?= $baseURL ?>assets/images/dar.png">

	<link href="<?= $baseURL ?>assets/vendor/jqvmap/css/jqvmap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="<?= $baseURL ?>assets/vendor/chartist/css/chartist.min.css">
	<link href="<?= $baseURL ?>assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css" rel="stylesheet">
	<link href="<?= $baseURL ?>assets/vendor/owl-carousel/owl.carousel.css" rel="stylesheet">
	<link href="<?= $baseURL ?>assets/vendor/bootstrap-datepicker-master/css/bootstrap-datepicker.min.css" rel="stylesheet">
	<link rel="stylesheet" href="<?= $baseURL ?>assets/vendor/select2/css/select2.min.css">
	<link rel="stylesheet" href="<?= $baseURL ?>assets/vendor/sweetalert2/dist/sweetalert2.min.css">
	<link class="main-css" href="<?= $baseURL ?>assets/css/style.css" rel="stylesheet">

	<!-- Custom styles -->
	<?php yieldSection('css') ?>

</head>

<body>

	<!--*******************
        Preloader start
    ********************-->
	<div id="preloader">
		<div class="sk-three-bounce">
			<div class="sk-child sk-bounce1"></div>
			<div class="sk-child sk-bounce2"></div>
			<div class="sk-child sk-bounce3"></div>
		</div>
	</div>
	<!--*******************
        Preloader end
    ********************-->

	<!--**********************************
        Main wrapper start
    ***********************************-->
	<div id="main-wrapper">

		<?= $_SESSION["IS_LOGIN"] ? yieldSection('navbar') : '' ?>
		<!--**********************************
            Content body start
        ***********************************-->
		<div class="content-body default-height">
			<?= yieldSection('content') ?>
		</div>
		<!--**********************************
            Content body end
        ***********************************-->

		<!--**********************************
            Footer start
        ***********************************-->
		<div class="footer">
			<div class="copyright">
				<p>Copyright © Designed &amp; Developed by <a href="http://dexignzone.com/" target="_blank">DexignZone</a> <span class="current-year">2024</span></p>
			</div>
		</div>
		<!--**********************************
            Footer end
        ***********************************-->

	</div>
	<!--**********************************
        Main wrapper end
    ***********************************-->

	<!--**********************************
        Scripts
    ***********************************-->
	<!-- Required vendors -->
	<script src="<?= $baseURL ?>assets/vendor/global/global.min.js"></script>
	<script>
		// Bootstrap 5 has no jQuery .modal() plugin; patch it so existing
		// $('#id').modal('show'|'hide'|'toggle') calls work and ESC closes modals.
		(function($) {
			if (!$ || !window.bootstrap || $.fn.modal) return;
			$.fn.modal = function(action) {
				return this.each(function() {
					var modal = bootstrap.Modal.getOrCreateInstance(this);
					if (action === 'show' || action === 'hide' || action === 'toggle') {
						modal[action]();
					}
				});
			};
		})(window.jQuery);
	</script>
	<script src="<?= $baseURL ?>assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js"></script>
	<script src="<?= $baseURL ?>assets/vendor/chart-js/chart.bundle.min.js"></script>
	<script src="<?= $baseURL ?>assets/vendor/owl-carousel/owl.carousel.js"></script>
	<!-- Chart piety plugin files -->
	<script src="<?= $baseURL ?>assets/vendor/peity/jquery.peity.min.js"></script>
	<!-- Apex Chart -->
	<script src="<?= $baseURL ?>assets/vendor/apexchart/apexchart.js"></script>
	<!-- Dashboard 1 -->
	<script src="<?= $baseURL ?>assets/js/dashboard/dashboard-1.js"></script>
	<script src="<?= $baseURL ?>assets/js/custom.min.js"></script>
	<script src="<?= $baseURL ?>assets/js/deznav-init.js"></script>
	<script src="<?= $baseURL ?>assets/vendor/sweetalert2/dist/sweetalert2.min.js"></script>
	<script>
		document.title = "<?= isset($title) ? $ENV['APP_NAME'] . ' - ' . htmlspecialchars($title) : $ENV['APP_NAME'] ?>";

		function showLoader(title = "Processing...", html = "Please wait while we are processing your request!") {
			Swal.fire({
				title: title,
				html: html,
				// imageUrl: "<?= $baseURL ?>assets/images/preloader.gif",
				showConfirmButton: false,
				allowOutsideClick: false,
				allowEscapeKey: false,
			});
		}

		function closeLoader() {
			Swal.close();
		}

		function carouselReview() {
			jQuery('.testimonial-one').owlCarousel({
				loop: true,
				margin: 10,
				autoplay: true,
				nav: false,
				center: true,
				rtl: true,
				dots: false,
				navText: ['<i class="fas fa-caret-left"></i>', '<i class="fas fa-caret-right"></i>'],
				responsive: {
					0: {
						items: 2
					},
					400: {
						items: 3
					},
					700: {
						items: 5
					},
					991: {
						items: 6
					},
					1200: {
						items: 4
					},
					1600: {
						items: 5
					}
				}
			})
		}

		jQuery(window).on('load', function() {
			setTimeout(function() {
				carouselReview();
			}, 1000);
		});

		jQuery(document).ready(function() {
			setTimeout(function() {
				dezSettingsOptions.version = 'light';
				new dezSettings(dezSettingsOptions);
				setCookie('version', 'light');
			}, 1500)
		});
	</script>

	<!-- Page-specific scripts -->
	<?= yieldSection('scripts-dashboard') ?>
	<?= yieldSection('scripts') ?>

</body>

</html>

<?php
// End output buffering and flush the content to the output
ob_end_flush();
?>