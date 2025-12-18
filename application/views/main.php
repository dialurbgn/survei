<?php
	$currency = 'IDR';
?>
<!DOCTYPE html>
<!--
Author: Keenthemes
Product Name: Metronic
Product Version: 8.2.0
Purchase: https://1.envato.market/EA4JP
Website: http://www.keenthemes.com
Contact: support@keenthemes.com
Follow: www.twitter.com/keenthemes
Dribbble: www.dribbble.com/keenthemes
Like: www.facebook.com/keenthemes
License: For each use you must have a valid license purchased only from above link in order to legally use the theme for your project.
-->
<html lang="en">
	<!--begin::Head-->
	<head><base href=""/>
		<title>
			<?php
				$themes_data_id = $this->ortyd->select2_getname($this->session->userdata('userid'),'users_data','id','themes_id');
				if($themes_data_id == '-'){
					$themes_data_id = 1;
				}
				$sidebar_id = $this->ortyd->select2_getname($this->session->userdata('userid'),'users_data','id','sidebar');
				if($sidebar_id == '-'){
					$sidebar_id = 0;
				}
				if(isset($title)){ 
					echo $title;
				}else{ 
					echo 'Dashboard';
				}; 
			?> | <?php echo title; ?>
		</title> 
		
		<style>
		
		[data-bs-theme=light] body:not(.app-blank) {
			background-image: url(<?php echo base_url().$this->ortyd->getthemes($themes_data_id); ?>) !important;
		}
		

		.menu-atas {
			//display:none !important;
		}
		

		
		#kt_body {
			//padding-top:30px;
		}
		
		

		</style>

		
		<meta charset="utf-8" />
		<meta name="insight-app-sec-validation" content="3aa047b9-2e0c-405d-986f-cbf0123bf56d">
		<meta name="description" content="<?php echo subtitle; ?>" />
		<meta name="keywords" content="<?php echo title; ?>" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<meta property="og:locale" content="en_US" />
		<meta property="og:type" content="website" />
		<meta property="og:title" content="<?php echo title; ?>" />
		<meta property="og:url" content="<?php echo base_url(); ?>" />
		<meta property="og:site_name" content="<?php echo title; ?>" />
		<!-- Di dalam <head> -->
		<link rel="manifest" href="<?php echo base_url(); ?>manifest.json">
		<meta name="theme-color" content="#000000">
		<link rel="canonical" href="<?php echo base_url(); ?>" />
		<link rel="shortcut icon" href="<?php echo base_url(); ?>favicon.png" />
		<!--begin::Fonts(mandatory for all pages)-->
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
		<!--end::Fonts-->
		<!--begin::Vendor Stylesheets(used for this page only)-->
		<link href="<?php echo base_url(); ?>themes/ortyd/assets/plugins/custom/fullcalendar/fullcalendar.bundle.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo base_url(); ?>themes/ortyd/assets/plugins/custom/datatables/datatables.bundle.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo base_url(); ?>themes/ortyd/assets/vendors/select2/css/select2.min.css" type="text/css" rel="stylesheet">
		<!--end::Vendor Stylesheets-->
		<!--begin::Global Stylesheets Bundle(mandatory for all pages)-->
		<link href="<?php echo base_url(); ?>themes/ortyd/assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo base_url(); ?>themes/ortyd/assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
		<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css" rel="stylesheet">
		<link href="<?php echo base_url(); ?>themes/ortyd/assets/vendors/sweetalert2-main/sweetalert2.min.css" rel="stylesheet">
		
		<link href="<?php echo base_url(); ?>themes/ortyd/assets/vendors/star-rating/dist/star-rating.css" rel="stylesheet">
		
		<link href="<?php echo base_url(); ?>themes/ortyd/assets/css/style.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo base_url(); ?>themes/ortyd/assets/css/style-dark.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo base_url(); ?>themes/ortyd/assets/css/style-system.css" rel="stylesheet" type="text/css" />
		<!--end::Global Stylesheets Bundle-->
		<script>// Frame-busting to prevent site from being loaded within a frame without permission (click-jacking) if (window.top != window.self) { window.top.location.replace(window.self.location.href); }</script>
		
		<script src="<?php echo base_url(); ?>themes/ortyd/assets/vendors/jquery/jquery-3.2.1.min.js"></script>
		
		<!--begin::Javascript-->
		
		
		<?php
		if (ENVIRONMENT == 'production') {
			?>
			<script>
				// Nonaktifkan semua console methods
				(function() {
					var noop = function() {};
					if (typeof window.console === "object") {
						var methods = ["log", "warn", "error", "info", "debug", "trace", "group", "groupCollapsed", "groupEnd", "assert"];
						for (var i = 0; i < methods.length; i++) {
							window.console[methods[i]] = noop;
						}
					}
				})();
			</script>
			<?php
		}
		?>

		

		
		<script>var hostUrl = "<?php echo base_url(); ?>themes/ortyd/assets/";</script>
		<!--begin::Global Javascript Bundle(mandatory for all pages)-->
		<script src="<?php echo base_url(); ?>themes/ortyd/assets/plugins/global/plugins.bundle.js"></script>
		<script src="<?php echo base_url(); ?>themes/ortyd/assets/js/scripts.bundle.js"></script>
		<script type="text/javascript" src="<?php echo base_url(); ?>themes/ortyd/assets/vendors/sweetalert2-main/sweetalert2.all.min.js"></script> 
		<script type="text/javascript" src="<?php echo base_url(); ?>themes/ortyd/assets/js/custom/bootbox.min.js" type="text/javascript"></script> 
		<script type="text/javascript" src="<?php echo base_url(); ?>themes/ortyd/assets/vendors/select2/js/select2.min.js" ></script> 
		<script type="text/javascript" src="<?php echo base_url(); ?>themes/ortyd/assets/js/custom/signature_pad.js" type="text/javascript"></script> 
		
		<script src="<?php echo base_url(); ?>themes/ortyd/assets/vendors/ckeditor/ckeditor.js"></script>
		<script src="<?php echo base_url(); ?>themes/ortyd/assets/vendors/fusionchart/fusioncharts.js" type="text/javascript" ></script>
		<script src="<?php echo base_url(); ?>themes/ortyd/assets/vendors/fusionchart/fusioncharts.jqueryplugin.js" type="text/javascript" ></script>
		<script src="<?php echo base_url(); ?>themes/ortyd/assets/vendors/fusionchart/themes/fusioncharts.theme.fusion.js" type="text/javascript" ></script>
		<script src="<?php echo base_url(); ?>themes/ortyd/assets/vendors/star-rating/dist/star-rating.js" type="text/javascript" ></script>
		
		<link href="<?php echo base_url(); ?>themes/ortyd/assets/vendors/progress-bar/loading-bar.min.css" rel="stylesheet">
		<script src="<?php echo base_url(); ?>themes/ortyd/assets/vendors/progress-bar/loading-bar.min.js" type="text/javascript"></script> 
		<script src="<?php echo base_url(); ?>themes/ortyd/assets/js/custom/dinamic_filter.js"></script>
		<script src="<?php echo base_url(); ?>themes/ortyd/assets/js/custom/jquery.selectlistactions.js"></script>
		<!-- Google tag (gtag.js) -->

		<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDLBSpFTZJg6RZLa3y3FvwH7psBGkWvuVM" type="text/javascript"></script>
		
		<script>
			let csrfName = '<?= $this->security->get_csrf_token_name(); ?>';
			let csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
			// Prefilter untuk menyisipkan CSRF token ke semua request POST
			$.ajaxPrefilter(function(options, originalOptions, jqXHR) {
				if (options.type.toUpperCase() === 'POST') {
					//csrfName = $('#csrf_token').attr('name');
					//csrfHash = $('#csrf_token').val();

					if (typeof originalOptions.data === 'string') {
						// Tambahkan CSRF token ke string query jika belum ada
						if (!originalOptions.data.includes(csrfName + '=')) {
							originalOptions.data += '&' + csrfName + '=' + csrfHash;
						}
					} else if (typeof originalOptions.data === 'object') {
						// Tambahkan jika belum ada
						if (!(csrfName in originalOptions.data)) {
							originalOptions.data[csrfName] = csrfHash;
						}
					}
				}
			});

			// Penanganan error CSRF / server error
			$(document).ajaxError(function(event, jqxhr) {
				if (jqxhr.status === 403 || jqxhr.status === 500) {
					// Request CSRF token baru
					$.get('<?php echo base_url('request_csrf_token'); ?>', function(data) {
						if (data && data.csrf_hash) {
							updateCsrfToken(data.csrf_hash);
						}
					});
				}
			});
			</script>
			
		<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
		<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
		<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
		
		<!-- Tambahkan di <head> -->
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/codemirror.min.css">
		<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/codemirror.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/htmlmixed/htmlmixed.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/js-beautify/1.14.7/beautify-html.min.js"></script>

		<script src="<?php echo base_url(); ?>sw-register.js"></script>
		
		<?php include_once('analytics.php'); ?>
	</head>
	<!--end::Head-->
	<!--begin::Body-->
	<body id="kt_body" class="header-fixed header-tablet-and-mobile-fixed toolbar-enabled">
	
		<input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>" id="csrf_token" />
		<!--begin::Theme mode setup on page load-->
		<script>var defaultThemeMode = "light"; var themeMode; if ( document.documentElement ) { if ( document.documentElement.hasAttribute("data-bs-theme-mode")) { themeMode = document.documentElement.getAttribute("data-bs-theme-mode"); } else { if ( localStorage.getItem("data-bs-theme") !== null ) { themeMode = localStorage.getItem("data-bs-theme"); } else { themeMode = defaultThemeMode; } } if (themeMode === "system") { themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light"; } document.documentElement.setAttribute("data-bs-theme", themeMode); }</script>
		<!--end::Theme mode setup on page load-->
		<!--begin::Main-->
		<!--begin::Root-->
		<div class="d-flex flex-column flex-root">
			<!--begin::Page-->

			<div class="page d-flex flex-row flex-column-fluid">

				<?php 
				if($sidebar_id == 1){
					require_once('sidebar.php'); 
				}
				?>

				<!--begin::Wrapper-->
				<div class="wrapper d-flex flex-column flex-row-fluid" id="kt_wrapper">
					<!--begin::Header-->
					<div id="kt_header" class="header align-items-stretch mb-5 mb-lg-10" data-kt-sticky="true" data-kt-sticky-name="header" data-kt-sticky-offset="{default: '200px', lg: '300px'}">
						<!--begin::Container-->
						<div class="container-xxl d-flex align-items-center">
							<!--begin::Heaeder menu toggle-->
							<div class="d-flex topbar align-items-center d-lg-none ms-n2 me-3" title="Show aside menu">
								<div class="btn btn-icon btn-active-light-primary btn-custom w-30px h-30px w-md-40px h-md-40px" id="kt_header_menu_mobile_toggle">
									<i class="ki-duotone ki-abstract-14 fs-1">
										<span class="path1"></span>
										<span class="path2"></span>
									</i>
								</div>
							</div>
							<!--end::Heaeder menu toggle-->
							<!--begin::Header Logo-->
							<div class="header-logo me-5 me-md-10 flex-grow-1 flex-lg-grow-0" id="header_logo">
								<a href="<?php echo base_url('dashboard'); ?>">
									<img alt="Logo" src="<?php echo base_url(); ?>logo-badan.png" class="logo-default h-40px"  style="" />
									<img alt="Logo" src="<?php echo base_url(); ?>logo-badan.png" class="logo-sticky h-40px" style="" />
								</a>
							</div>
							<!--end::Header Logo-->
							<!--begin::Wrapper-->
							<div class="d-flex align-items-stretch justify-content-between flex-lg-grow-1">
								
								<?php require_once('navbar.php'); ?>
								
							</div>
							<!--end::Wrapper-->
						</div>
						<!--end::Container-->
					</div>
					<!--end::Header-->
					
					<!--Begin::Content-->
						<?php require_once($template_contents.'.php'); ?>
					<!--End::Content-->
					
					<!--begin::Footer-->
					<div class="footer py-4 d-flex flex-lg-column" id="kt_footer">
						<!--begin::Container-->
						<div class="container-xxl d-flex flex-column flex-md-row align-items-center justify-content-between">
							<!--begin::Copyright-->
							<div class="text-dark order-2 order-md-1">
								<span class="text-muted fw-semibold me-1"><?php echo date('Y'); ?>&copy;</span>
								<a href="#" target="_blank" class="text-gray-800 text-hover-primary"><?php echo title; ?></a>
							</div>
							<!--end::Copyright-->

						</div>
						<!--end::Container-->
					</div>
					<!--end::Footer-->
				</div>
				<!--end::Wrapper-->
			</div>
			<!--end::Page-->
		</div>
		<!--end::Root-->

		<!--end::Main-->
		<!--begin::Scrolltop-->
		<div id="kt_scrolltop" class="scrolltop" data-kt-scrolltop="true">
			<i class="ki-duotone ki-arrow-up">
				<span class="path1"></span>
				<span class="path2"></span>
			</i>
		</div>
		<!--end::Scrolltop-->
		
		
		<!--end::Global Javascript Bundle-->
		<!--begin::Vendors Javascript(used for this page only)-->

		
		<script src="<?php echo base_url(); ?>themes/ortyd/assets/js/custom/uploads.js"></script>
		
		<script src="<?php echo base_url(); ?>themes/ortyd/assets/plugins/custom/fullcalendar/fullcalendar.bundle.js"></script>
		<script src="https://cdn.amcharts.com/lib/5/index.js"></script>
		<script src="https://cdn.amcharts.com/lib/5/xy.js"></script>
		<script src="https://cdn.amcharts.com/lib/5/percent.js"></script>
		<script src="https://cdn.amcharts.com/lib/5/radar.js"></script>
		<script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script>
		<script src="https://cdn.amcharts.com/lib/5/map.js"></script>
		<script src="https://cdn.amcharts.com/lib/5/geodata/worldLow.js"></script>
		<script src="https://cdn.amcharts.com/lib/5/geodata/continentsLow.js"></script>
		<script src="https://cdn.amcharts.com/lib/5/geodata/usaLow.js"></script>
		<script src="https://cdn.amcharts.com/lib/5/geodata/worldTimeZonesLow.js"></script>
		<script src="https://cdn.amcharts.com/lib/5/geodata/worldTimeZoneAreasLow.js"></script>
		<script src="<?php echo base_url(); ?>themes/ortyd/assets/plugins/custom/datatables/datatables.bundle.js"></script>
		<!--end::Vendors Javascript-->
		<!--begin::Custom Javascript(used for this page only)-->
		<script src="<?php echo base_url(); ?>themes/ortyd/assets/js/widgets.bundle.js"></script>
		<script src="<?php echo base_url(); ?>themes/ortyd/assets/js/custom/widgets.js"></script>
		<script type="text/javascript" src="<?php echo base_url(); ?>themes/ortyd/assets/vendors/bootstrap-typehead/bootstrap3-typeahead.min.js" ></script> 
		<script src="<?php echo base_url(); ?>themes/ortyd/assets/vendors/autoNumeric-next/src/autoNumeric.min.js" type="text/javascript"></script> 
		<script src="<?php echo base_url(); ?>themes/ortyd/assets/vendors/qr-code/dist/qr-code.js" type="text/javascript"></script> 
		
		<script>
			$( document ).ready(function() {
				csrfName = '<?= $this->security->get_csrf_token_name(); ?>';
				csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
				KTMenu.createInstances();
			});
		</script>
		
		
		
		<!-- Summernote -->
		
		<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.js"></script>

<!-- 
		<link href="<?php echo base_url(); ?>themes/ortyd/assets/vendors/summernote/summernote-lite.min.css" rel="stylesheet">
		<script src="<?php echo base_url(); ?>themes/ortyd/assets/vendors/summernote/summernote-lite.min.js" type="text/javascript"></script> 
		-->
		
		<script>
		  $('.summernote').summernote({
			height: 300,
			callbacks: {
			  onImageUpload: function(files) {
				sendFile(files[0]);
			  }
			}
		  });

		 function sendFile(file) {
    var data = new FormData();
    
    // Ambil user_id dari session PHP
    var user_id = '<?php echo $this->session->userdata("user_id"); ?>'; // Ambil user_id dari session
    
    // Ambil nama file yang diupload
    var file_name = file.name; // Mengambil nama file yang diupload
    
    // Tambahkan data yang diperlukan
    data.append("userfile", file); // File yang diunggah
    data.append("user_id", user_id); // ID pengguna yang mengunggah
    data.append("file_name", file_name); // Nama file yang diunggah
    data.append("token_foto", csrfHash); // CSRF hash
    
    // Tambahkan CSRF token sesuai nama yang digunakan oleh CodeIgniter
    data.append("<?php echo $this->security->get_csrf_token_name(); ?>", 
                '<?php echo $this->security->get_csrf_hash(); ?>');

    // Kirim melalui AJAX
    $.ajax({
        url: "<?php echo fileserver_url.'proses_upload'; ?>",
        data: data,
        cache: false,
        contentType: false,
        processData: false,
        type: "POST",
        success: function(response) {
            // Mengecek apakah upload berhasil
			var jsonResponse = JSON.parse(response);
            if (jsonResponse.message === "success") {
                // Gunakan path dari respons untuk menambahkan gambar ke dalam Summernote
                $('.summernote').summernote("insertImage", jsonResponse.path);
            } else {
                alert("Upload gagal: " + jsonResponse.message);
            }
        },
        error: function(data) {
            alert("Upload gagal");
        }
    });
}


		</script>


		<script>
			
				$( document ).ready(function() {
					is_online()
					$(window).on('scroll', function () {
						let bg = $('body').css('background-image');
						let match = bg.match(/url\(["']?(.*?)["']?\)/);

						if (match && match[1]) {
							let imageUrl = match[1];

							let img = new Image();
							img.crossOrigin = "anonymous";
							img.src = imageUrl;

							img.onload = function () {
								const { darkest, brightest } = getDarkestAndBrightestColor(img);
								const gradient = `linear-gradient(to bottom right, rgb(${darkest}) 50%, rgb(${brightest}) 100%)`;
								const isSticky = $('body').attr('data-kt-sticky-header') === 'on';

								if (isSticky) {
									$('#kt_header').attr("style", "background: " + gradient + " !important;");
									$('#kt_header .menu-sub').attr("style", "background: " + gradient + " !important;");
									$('#kt_header .menu-item.here>.menu-link').attr("style", "background: " + gradient + " !important;");
									$('#kt_header .menu-title').attr("style", "color: #fff !important;");
								} else {
									$('#kt_header').removeAttr("style");
									$('#kt_header .menu-title').css('color', '');
									$('#kt_header .menu-sub').css("background", "#FFF");
									$('#kt_header .menu-item.here>.menu-link').css("background", "rgba(255,255,255,.1)");
								}
							};
						}
					});

									
					document.querySelectorAll('input[readonly], textarea[readonly], select[readonly], .readonly-mode .select2-selection .select2-selection__rendered').forEach(function(el) {
						el.removeAttribute('placeholder');
					});

					if (window.location === window.parent.location) {
					<?php
							$this->db->select('data_absensi.*, date(tanggal) as datenya');
							$this->db->where('user_id', $this->session->userdata('userid'));
							$this->db->where('type', 'Website');
							$this->db->order_by('date(tanggal)','DESC');
							$this->db->limit(1);
							$queryabsen = $this->db->get('data_absensi');
							$queryabsen = $queryabsen->result_object();
							if($queryabsen){
								$last_absen = $queryabsen[0]->datenya;
							}else{
								$date = date('Y-m-d');
								$date = strtotime($date);
								$date = strtotime("-1 day", $date);
								$last_absen = date('Y-m-d', $date);
							}
							
							if($last_absen != date('Y-m-d')){
								
					?>
					
							<?php if($this->session->userdata("position_name") != null && $this->session->userdata("position_name") != ''){
								
								$position_name = $this->session->userdata("position_name");
								
							}else{
								
								$position_name =  $this->ortyd->select2_getname($this->session->userdata("group_id"),"users_groups","id","name"); 
							}
							
							?>
							
							Swal.fire({
							  title: `<div style="font-size:1.8em;color:#2c3e50;font-weight:bold">Selamat Datang Kembali<br><span style="color:#e74c3c">${<?php echo json_encode($this->session->userdata("fullname")); ?>}</span></div>`,
							  html: `<style>
									   .custom-swal-container .swal2-popup {
										 border-radius: 12px !important;
										 box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15) !important;
									   }
									   .custom-swal-confirm-btn {
										 font-size: 1.1em !important;
										 padding: 10px 25px !important;
										 border-radius: 8px !important;
										 box-shadow: 0 4px 8px rgba(52, 152, 219, 0.3) !important;
										 transition: all 0.3s ease !important;
									   }
									   .custom-swal-confirm-btn:hover {
										 transform: translateY(-2px) !important;
										 box-shadow: 0 6px 12px rgba(52, 152, 219, 0.4) !important;
									   }
									 </style>
									 <div style="background:#f8f9fa;padding:20px;border-radius:10px;border-left:4px solid #3498db;margin-top:15px">
									   <div style="display:flex;align-items:center;gap:15px">
										 <i class="ti ti-user-check" style="font-size:2em;color:#3498db"></i>
										 <div>
										   <p style="margin:0;font-size:1.1em;color:#2c3e50">Anda login sebagai</p>
										   <p style="margin:0;font-size:1.3em;font-weight:bold;color:#e74c3c">${<?php echo json_encode($position_name); ?>}</p>
										 </div>
									   </div>
									 </div>`,
							  imageUrl: '<?php echo base_url('logo.jpg'); ?>',
							  imageWidth: 160,
							  imageHeight: 50,
							  imageAlt: '<?php echo title; ?>',
							  allowOutsideClick: false,
							  background: '#ffffff',
							  backdrop: `
								rgba(0,0,123,0.4)
								url("<?php echo base_url('assets/img/nyan-cat.gif'); ?>")
								left top
								no-repeat
							  `,
							  confirmButtonText: '<i class="ti ti-angle-double-right"></i> Memulai Aplikasi Sekarang',
							  confirmButtonColor: '#3498db',
							  buttonsStyling: true,
							  showClass: {
								popup: 'animate__animated animate__fadeInDown animate__faster'
							  },
							  hideClass: {
								popup: 'animate__animated animate__fadeOutUp animate__faster'
							  },
							  customClass: {
								container: 'custom-swal-container',
								confirmButton: 'custom-swal-confirm-btn'
							  }
							}).then((result) => {
							  /* Read more about isConfirmed, isDenied below */
							 if (result.isConfirmed) {
									kirimAbsen(); // Pertama kali kirim
								}

							});
						
					
					<?php } ?>
					
						}
						
						function kirimAbsen() {
		$.post('<?php echo base_url('dashboard/saveAbsen'); ?>', {
			user_id: '<?php echo $this->session->userdata('userid'); ?>',
			<?php echo $this->security->get_csrf_token_name(); ?>: csrfHash
		}, function (data) {
			var obj = jQuery.parseJSON(data);
			updateCsrfToken(obj.csrf_hash);

			if (obj.message == 'success') {
				jumlah = obj.data;

				Swal.fire({
					title: '<i class="fas fa-gift" style="color: #f8bb86; font-size: 3em"></i> Terima Kasih!',
					html: '<div style="background-color: #f0f8ff; padding: 20px; border-radius: 10px; border-left: 5px solid #4CAF50">' +
						'<p style="font-size: 1.2em; margin-bottom: 10px">Terima kasih telah melakukan daily login pada <?php echo title; ?>!</p>' +
						'<div style="display: flex; align-items: center; margin-top: 15px">' +
						'<i class="fas fa-star" style="color: #FFD700; margin-right: 10px"></i>' +
						'<span>Selamat beraktifitas teman !</span>' +
						'</div></div>',
					icon: 'success',
					confirmButtonText: '<i class="fas fa-thumbs-up"></i> Mengerti',
					confirmButtonColor: '#4CAF50',
					background: '#fff9e6',
					timer: 5000,
					timerProgressBar: true,
					showClass: {
						popup: 'animate__animated animate__bounceIn'
					},
					hideClass: {
						popup: 'animate__animated animate__fadeOut'
					}
				});
			}
		}).fail(function (jqxhr, status, error) {
			console.error("Request failed: " + error);

			// Jika 403, ambil token CSRF baru
			if (jqxhr.status === 403) {
				$.get('<?php echo base_url('request_csrf_token'); ?>', function (data) {
					csrfHash = data.csrf_hash;
					updateCsrfToken(csrfHash);

					// Tampilkan ulang konfirmasi SweetAlert untuk retry
					showRetryDialog();
				});
			} else {
				// Untuk error lainnya
				showRetryDialog();
			}
		});
	}

	function showRetryDialog() {
		Swal.fire({
			title: "Gagal Absen!",
			text: "Terjadi kesalahan saat mengirim data. Coba lagi?",
			icon: "error",
			showCancelButton: true,
			confirmButtonText: "Ya, coba lagi",
			cancelButtonText: "Batal",
			confirmButtonColor: "#d33",
			cancelButtonColor: "#aaa",
			customClass: {
				confirmButton: "btn btn-danger",
				cancelButton: "btn btn-secondary"
			},
			didOpen: () => {
				$('.swal2-container').css('z-index', 99999);
			}
		}).then((retryResult) => {
			if (retryResult.isConfirmed) {
				kirimAbsen(); // Retry kirim
			}
		});
	}
					
					<?php if(($this->session->userdata('tipe_data') != '' && $this->session->userdata('tipe_data') != null)){ ?>
					
						<?php if($this->session->userdata('tipe_data') == 'Inbound') { ?>
							$('#nota_kebutuhan_count').addClass('displayNone');
							$('#menu_id_24').hide()
						<?php }elseif($this->session->userdata('tipe_data') == 'Outbound') { ?>
							$('#justi_kebutuhan_count').addClass('displayNone');
							$('#menu_id_25').hide()
						<?php } ?>
						
					<?php } ?>
					
					if ($(".numeric-rp")[0]){
			
						new AutoNumeric.multiple('.numeric-rp', 
							{ 
								currencySymbol : '<?php echo $currency; ?>. ',
								unformatOnSubmit: true,
								allowDecimalPadding: false,
								watchExternalChanges: true,
												digitGroupSeparator: '.',
												decimalCharacter : ',',	
							}
						);
					
					}
					
					$(".datetime").daterangepicker({
							singleDatePicker: true,
							showDropdowns: true,
							minYear: 1901,
							maxYear: parseInt(moment().format("YYYY"),12),
							locale: {
							  format: 'YYYY-MM-DD'
							}
						}, function(start, end, label) {
							
						}
					);
					
					$(".datepickertime").daterangepicker({
						singleDatePicker: true,
						timePicker: true,
						showDropdowns: true,
						timePicker24Hour:true,
						opens: 'auto',
						drops: 'auto',
						parentEl: ".swal2-popup",
						minYear: 1901,
						maxYear: parseInt(moment().format("YYYY"),12),
						locale: {
							format: 'YYYY-MM-DD HH:mm:00'
						}
					}, function(start, end, label) {
									
					});
			
					setTimeout(function(){
						
						
						const deg = Math.floor(Math.random() *360);
				  
						const gradient = "linear-gradient(" + deg + "deg, " + "#" + createHex() + ", " + "#" + createHex() +")";
						//document.getElementById("header-color").style.background = gradient;
						//document.getElementById("left-sidebar").style.background = gradient;
						
						
					}, 100);
					
					setTimeout(function(){
						
						is_online()
						
					}, 60000);
					

					
					//getcountingmenu()
					
				});
				
				function is_online() {
				try {
					$.ajax({
						url: '<?php echo base_url('dashboard/isonline'); ?>',
						method: 'POST',
						dataType: 'json',
						data: {
							
						},
						success: function(response) {
							if (response) {
								if (response.message === 'success') {
									//if (response.csrf_hash) {
										//updateCsrfToken(response.csrf_hash);
									//}
									// initializeTheme();
									// setupThemeToggle();
								} else if (response.message === 'notlogin') {
									window.location.reload();
								}
							} else {
								console.error("is_online error");
								//window.location.reload();
							}
						},
						error: function(xhr, status, error) {
							console.error("is_online error:", error);
							//window.location.reload();
						}
					});
				} catch (error) {
					console.error("Caught error in is_online:", error);
					//window.location.reload();
				}
			}


				
				function getcountingmenu(){
					$.post('<?php echo base_url('dashboard/getcount'); ?>',{
						<?php echo $this->security->get_csrf_token_name(); ?> : csrfHash 
					}, function (data) {
						var obj = jQuery.parseJSON(data);
						if(obj.message == 'success'){
							jumlah = obj.data
							$('#menu_data_perusahaan_register').html(jumlah.total_mitra);
							$('#menu_child_Pra_Registrasi').html(jumlah.total_mitra_pra);
							$('#menu_child_Input_Registrasi').html(jumlah.total_mitra_input);
							$('#menu_child_Vendor').html(jumlah.total_mitra_verified);
							$('#menu_data_nota_kebutuhan').html(jumlah.total_nota_kebutuhan);
							$('#menu_data_justifikasi_kebutuhan').html(jumlah.total_justifikasi_kebutuhan);
							$('#menu_data_spph').html(jumlah.total_spph);
							$('#menu_data_spk').html(jumlah.total_spk);
							$('#menu_data_bast').html(jumlah.total_bast);
							$('#menu_data_invoice').html(jumlah.total_invoice);
						}
					})
					.fail(function(jqxhr, status, error) {
									console.error("Request failed: " + error);
									
									// Menangani jika statusnya 403 dan mengambil token CSRF baru
									if (jqxhr.status === 403) {
										$.get('<?php echo base_url('request_csrf_token'); ?>', function(data) {
											csrfHash = data.csrf_hash;
											updateCsrfToken(csrfHash); // Perbarui token CSRF
											// Lakukan retry atau aksi lainnya
										});
									}

									Swal.fire({
										text: "Terjadi kesalahan saat mengirim data!",
										icon: "error",
										buttonsStyling: false,
										confirmButtonText: "Coba Lagi",
										customClass: {
											confirmButton: "btn btn-danger"
										},
										didOpen: () => {
											$('.swal2-container').css('z-index', 99999); // Ensures the alert is in front
										}
									});

									//loadingclose();
								});
				}
				
				function createHex() {
				  var hexCode1 = "";
				  var hexValues1 = "0123456789abcdef";
				  
				  for ( var i = 0; i < 6; i++ ) {
					hexCode1 += hexValues1.charAt(Math.floor(Math.random() * hexValues1.length));
				  }
				  return hexCode1;
				}
	  
		</script>
		
		
		<canvas id="pdf-canvas_data" width="400" style="display:none"></canvas>
		<script>

		var __PDF_DOC_GEN,
			__CURRENT_PAGE_GEN,
			__TOTAL_PAGES_GEN,
			__PAGE_RENDERING_IN_PROGRESS_GEN = 0,
			__CANVAS_GEN = $('#pdf-canvas_data').get(0),
			__CANVAS_GEN_CTX_GEN = __CANVAS_GEN.getContext('2d');

		function showPDF_GEN(div,pdf_url,id,type = null) {
			PDFJS.getDocument({ url: pdf_url }).then(function(pdf_doc) {
				__PDF_DOC_GEN = pdf_doc;
				__TOTAL_PAGES_GEN = __PDF_DOC_GEN.numPages;
				// Show the first page
				var user_id = '<?php echo $this->session->userdata('userid'); ?>'
				showPage_gen(1, user_id, id,div,type);
			}).catch(function(error) {

				alert(error.message);
			});;
		}

		function showPage_gen(page_no, user_id, id,div,type = null) {
			__PAGE_RENDERING_IN_PROGRESS_GEN = 1;
			__CURRENT_PAGE_GEN = page_no;
			// Fetch the page
			__PDF_DOC_GEN.getPage(page_no).then(function(page) {
				// As the canvas is of a fixed width we need to set the scale of the viewport accordingly
				var scale_required = __CANVAS_GEN.width / page.getViewport(1).width;

				// Get viewport of the page at required scale
				var viewport = page.getViewport(scale_required);

				// Set canvas height
				__CANVAS_GEN.height = viewport.height;

				var renderContext = {
					canvasContext: __CANVAS_GEN_CTX_GEN,
					viewport: viewport
				};
				
				// Render the page contents in the canvas
				page.render(renderContext).then(function() {
					__PAGE_RENDERING_IN_PROGRESS_GEN = 0;
					var img = __CANVAS_GEN.toDataURL("image/png");
					//console.log(img);
					
					$.post("<?php echo fileserver_url.'uploadBase64_new'; ?>",{image64 : img, user_id : user_id, id: id, <?php echo $this->security->get_csrf_token_name(); ?> : csrfHash }, function (response) {
						//var obj = $.parseJSON(response)
						var obj = response;
						console.log(obj)
						if(type == 'renderclass'){
							$("#" + div + ' .dz-preview .dz-image_'+ id +' img').attr("src", obj.url_server + obj.path);
						}else{
							$("#" + div + ' .dz-preview .dz-image img').attr("src", obj.url_server + obj.path);
						}
						
						
						//console.log(response);
					})
				});
			});
		}

		</script>
		
		
		<script>
		var loadingupload
		function loadingopen(){
			loadingupload = bootbox.dialog({
				title: 'Uploading Data ...',
				message: '<p><i class="fa fa-spin fa-spinner"></i> Loading...</p>',
				className: 'loadingdialog',
				closeButton: false
			});
		}
		
		function loadingclose(){
			//loadingupload.find('.bootbox-body').html('Upload');
			setTimeout(function(){
				loadingupload.modal('hide'); 
			}, 2000);
		}
		
		
		
		</script>
		
		
		<script>
		var loadingprogress
		function loadingopenprog(){
			loadingprogress = bootbox.dialog({
				title: 'Loading ...',
				message: '<p><i class="fa fa-spin fa-spinner"></i> Loading...</p>',
				className: 'loadingdialog',
				closeButton: false
			});
		}
		
		function loadingcloseprog(){
			//loadingupload.find('.bootbox-body').html('Upload');
			setTimeout(function(){
				loadingprogress.modal('hide'); 
			}, 2000);
		}
		
		
		
		</script>
		
		<?php require_once('common/changeheader.php'); ?>
		
		<script>

		function stripHtml(html)
		{
		   let tmp = document.createElement("DIV");
		   tmp.innerHTML = html;
		   return tmp.textContent || tmp.innerText || "";
		}

		    

   
	let draggedItem = null;
	let targetItemData = null;
    // Drag start event handler
    function handleDragStart(event) {
      draggedItem = event.target;
      event.dataTransfer.effectAllowed = 'move';
      event.dataTransfer.setData('text/html', draggedItem.innerHTML);
      event.target.style.opacity = '0.5';
    }

    // Drag over event handler
    function handleDragOver(event) {
      event.preventDefault();
      event.dataTransfer.dropEffect = 'move';
      targetItem = event.target;
      if (targetItem !== draggedItem && targetItem.classList.contains('drag-item')) {
        const boundingRect = targetItem.getBoundingClientRect();
        const offset = boundingRect.y + (boundingRect.height / 2);
		//console.log(targetItem.id)
		//console.log(targetItem.classList.contains('drag-item'))
        if (targetItem.classList.contains('drag-item')) {
		  targetItemData = event;
          targetItem.style.borderBottom = 'solid 2px red';
          targetItem.style.borderTop = '';
        } else {
          targetItem.style.borderTop = 'solid 2px #000';
          targetItem.style.borderBottom = '';
        }
      }
    }

    // Drop event handler
    function handleDrop(event) {
		//console.log(event.target);
     // event.preventDefault();
		targetItem = targetItemData.target;
	  //console.log(targetItem.id)
	  //console.log(draggedItem.id)
	  
	  if (targetItem !== draggedItem && targetItem.classList.contains('drag-item')) {
        if (event.clientY > targetItem.getBoundingClientRect().top + (targetItem.offsetHeight / 2)) {
           targetItem.parentNode.insertBefore(draggedItem, targetItem.nextSibling);
		   //swap(draggedItem, targetItem.nextSibling);
        } else {
		   targetItem.parentNode.insertBefore(draggedItem, targetItem);
           //swap(draggedItem, targetItem);
        }
      }
	  
      targetItem.style.borderTop = '';
      targetItem.style.borderBottom = '';
      draggedItem.style.opacity = '';
      draggedItem = null;
	  getAlldiv()
    }
	
	function swap(node1, node2) {
		const afterNode2 = node2.nextElementSibling;
		const parent = node2.parentNode;
		node1.replaceWith(node2);
		parent.insertBefore(node1, afterNode2);
	}
	
	function getAlldiv(){
		 //console.log($('div'));  
    // [<div id="outer"><div id="inner"></div></div>], [<div id="inner"></div>]

			 var datacolumnnya = [];
			 var images = $('#dragList').find("draggable");
			 console.log(images.prevObject[0].children.length);
			 var data = images.prevObject[0].children;
			 for(x=0;x<=data.length;x++){
				 
				try {
					if (typeof data[x].draggable !== "undefined") {
						if(data[x].draggable == true){
							text = data[x].id;
							$("#" + text).css("border","none");
							$("#" + text).css("border-color","none");
							$("#" + text).css("opacity","1");
							text = text.replace("_header", "");
							console.log(text)
							datacolumnnya[x] = text;
						}
					}
				} catch (err) {
				   break;
				}

				
				
			 }
			 
			const myArray = datacolumnnya.toString();
            var myJsonString = JSON.stringify(myArray);
            console.log(myArray)
			savingTableViewOrderForm('<?php echo $module; ?>','<?php echo $module; ?>', datacolumnnya, myArray)
			// 0: <div id="outer"><div id="inner"></div></div>
			// 1: <div id="inner"></div>
	}

	function readonly_select(objs, action) {
		if (action === true) {
			// Menambahkan elemen disabled-select
			objs.prepend('<div class="disabled-select"></div>');

			// Jika ada class selection, tambahkan class lain (misalnya 'readonly-mode')
			if (objs.has('.selection').length) {
				objs.find('.selection').addClass('readonly-mode');
			}
		} else {
			// Menghapus elemen disabled-select
			$(".disabled-select", objs).remove();

			// Menghapus class 'readonly-mode' jika ada
			if (objs.has('.selection').length) {
				objs.find('.selection').removeClass('readonly-mode');
			}
		}
	}
	
	function updateCsrfToken(newToken) {
		// Update semua elemen input yang class-nya 'csrf'
		csrfHash = newToken;
		$('#csrf_token').val(csrfHash);
		$('.csrf_token').val(csrfHash);
	}
	
	function getDarkestAndBrightestColor(imgElement) {
		const canvas = document.createElement('canvas');
		const ctx = canvas.getContext('2d');

		canvas.width = imgElement.naturalWidth;
		canvas.height = imgElement.naturalHeight;
		ctx.drawImage(imgElement, 0, 0);

		const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height).data;

		let minBrightness = 255 * 3;
		let maxBrightness = 0;
		let darkestColor = '';
		let brightestColor = '';

		const MIN_BRIGHTNESS_THRESHOLD = 60; // Hindari warna terlalu gelap/hitam

		for (let i = 0; i < imageData.length; i += 4) {
			const r = imageData[i];
			const g = imageData[i + 1];
			const b = imageData[i + 2];

			const brightness = r + g + b;

			// Cek untuk warna terang
			if (brightness > maxBrightness) {
				maxBrightness = brightness;
				brightestColor = `${r},${g},${b}`;
			}

			// Ambil warna tergelap yang masih di atas threshold
			if (brightness < minBrightness && brightness > MIN_BRIGHTNESS_THRESHOLD) {
				minBrightness = brightness;
				darkestColor = `${r},${g},${b}`;
			}
		}

		// Jika tidak ada warna tergelap yang lolos filter, fallback ke abu-abu tua
		if (!darkestColor) {
			darkestColor = '60,60,60';
		}

		return {
			darkest: darkestColor,
			brightest: brightestColor
		};
	}


		</script>
		
		<?php //include_once('chat.php'); ?>


		<!--end::Custom Javascript-->
		<!--end::Javascript-->
		

		
		<style>
		    #installBtn {
      position: fixed;
      bottom: 20px;
      left: 20px;
      background-color: #007bff;
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 50%;
      font-size: 16px;
      cursor: pointer;
      z-index: 9999;
    }

    #installBtn:hover {
      background-color: #0056b3;
    }
		</style>
		 <button id="installBtn" style="display: none;">Install Aplikasi</button>

		 <script>
		  let deferredPrompt;
		  const addBtn = document.getElementById('installBtn');

		  // Sembunyikan button install PWA dulu
		  if (addBtn) addBtn.style.display = 'none';

		  // Event listener untuk sebelum aplikasi diinstall
		  window.addEventListener('beforeinstallprompt', (e) => {
			e.preventDefault();
			deferredPrompt = e;

			// Tampilkan tombol install
			if (addBtn) addBtn.style.display = 'block';

			// Menangani klik pada tombol install
			addBtn.addEventListener('click', () => {
			  // Tampilkan SweetAlert2 untuk konfirmasi install
			  Swal.fire({
				title: 'Install Aplikasi?',
				text: "Apakah Anda ingin menambahkan aplikasi ini ke layar utama?",
				icon: 'question',
				showCancelButton: true,
				confirmButtonText: 'Install',
				cancelButtonText: 'Nanti Saja'
			  }).then((result) => {
				if (result.isConfirmed && deferredPrompt) {
				  // Tampilkan prompt install jika pengguna mengonfirmasi
				  deferredPrompt.prompt();
				  deferredPrompt.userChoice.then((choiceResult) => {
					if (choiceResult.outcome === 'accepted') {
					  console.log('User accepted the install prompt');
					  // Menampilkan SweetAlert2 dengan pesan sukses
					  Swal.fire('Terinstal!', 'Aplikasi berhasil ditambahkan ke layar utama.', 'success');
					  // Sembunyikan tombol setelah berhasil terinstal
					  if (addBtn) addBtn.style.display = 'none';
					} else {
					  console.log('User dismissed the install prompt');
					  // Menampilkan SweetAlert2 dengan pesan info
					  Swal.fire('Dibatalkan', 'Aplikasi tidak jadi diinstall.', 'info');
					}
					deferredPrompt = null; // Reset deferredPrompt setelah selesai
				  });
				}
			  });
			});
		  });

		  // Cek jika aplikasi sudah terinstal
		  window.addEventListener('appinstalled', (event) => {
			console.log('Aplikasi berhasil diinstal');
			// Sembunyikan tombol setelah aplikasi terinstal
			if (addBtn) addBtn.style.display = 'none';
		  });
		</script>


	
		<script>
		  if ('serviceWorker' in navigator) {
			navigator.serviceWorker.register('<?php echo base_url(); ?>service-worker.js')
			  .then(reg => console.log('Service Worker registered', reg))
			  .catch(err => console.error('Service Worker registration failed', err));
		  }
		  
		</script>
		

	</body>
	<!--end::Body-->
</html>