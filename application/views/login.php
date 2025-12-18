<?php
	$currency = 'Rp';
	if(!isset($site_key)){
		$site_key = '6Le6VJgqAAAAAK8yrTjdjcwQ-Q1_BjME--MoPL6f';
	}
?>
<!DOCTYPE html>
<html lang="en">
	<!--begin::Head-->
	<head>
		<title>Login | <?php echo title; ?></title>
		<meta name="insight-app-sec-validation" content="3aa047b9-2e0c-405d-986f-cbf0123bf56d">
		<meta charset="utf-8" />
		<meta name="description" content="<?php echo subtitle; ?>" />
		<meta name="keywords" content="<?php echo title; ?>" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<meta property="og:locale" content="en_US" />
		<meta property="og:type" content="website" />
		<meta property="og:title" content="<?php echo title; ?>" />
		<meta property="og:url" content="<?php echo base_url(); ?>" />
		<meta property="og:site_name" content="<?php echo title; ?>" />
		<link rel="canonical" href="<?php echo base_url(); ?>" />
		<link rel="shortcut icon" href="<?php echo base_url(); ?>favicon.png" />
		<!-- Di dalam <head> -->
		<link rel="manifest" href="<?php echo base_url(); ?>manifest.json">
		<meta name="theme-color" content="#000000">
		<!--begin::Fonts(mandatory for all pages)-->
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
		<!--end::Fonts-->
		<!--begin::Global Stylesheets Bundle(mandatory for all pages)-->
		<link href="<?php echo base_url(); ?>themes/ortyd/assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo base_url(); ?>themes/ortyd/assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo base_url(); ?>themes/ortyd/assets/css/style-login.css" rel="stylesheet" type="text/css" />
		<!--end::Global Stylesheets Bundle-->
		<script>// Frame-busting to prevent site from being loaded within a frame without permission (click-jacking) if (window.top != window.self) { window.top.location.replace(window.self.location.href); }</script>
		
		<?php $themes_data_id = rand(1, 3); ?>
		<style>
		body {
			background-image: url('<?php echo base_url() . $this->ortyd->getthemes($themes_data_id); ?>') !important;
		}
		
		/* Particle.js styling */
		#particles-js {
			position: fixed;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			z-index: -1;
			pointer-events: none;
		}
		
		/* Ensure content is above particles */
		.content-wrapper {
			position: relative;
			z-index: 1;
		}
		</style>

		
		<script src="<?php echo base_url(); ?>themes/ortyd/assets/vendors/jquery/jquery-3.2.1.min.js"></script>
		<script type="text/javascript" src="<?php echo base_url(); ?>themes/ortyd/assets/vendors/bootstrap-notify/bootstrap-notify.min.js"></script> 
		<script src="<?php echo base_url(); ?>themes/ortyd/assets/vendors/autoNumeric-next/src/autoNumeric.min.js" type="text/javascript"></script> 

		<script src='https://www.google.com/recaptcha/api.js'></script>
		
		<!-- Particle.js CDN -->
		<script src="https://cdnjs.cloudflare.com/ajax/libs/particles.js/2.0.0/particles.min.js"></script>
		
		<script>
			var csrfName;
			var csrfHash;
			$( document ).ready(function() {
				csrfName = $('#csrf_token').attr('name'); // Nama token
				csrfHash = $('#csrf_token').val();        // Nilai token
				
				$.ajaxPrefilter(function(options, originalOptions, jqXHR) {
					if (options.type.toUpperCase() === 'POST') {
						csrfName = $('#csrf_token').attr('name');
						csrfHash = $('#csrf_token').val();  // Ambil CSRF token terbaru
						$('.csrf_token').val(csrfHash);
						if (typeof originalOptions.data === 'string') {
							originalOptions.data += '&' + csrfName + '=' + csrfHash;
						} else if (typeof originalOptions.data === 'object') {
							originalOptions.data[csrfName] = csrfHash;
						}
					}
				});
			});
			
		</script>
		
		<script type="text/javascript">
			$.ajaxPrefilter(function(options, originalOptions, jqXHR) {
				if (options.type.toUpperCase() === 'POST') {
					csrfName = $('#csrf_token').attr('name');
					csrfHash = $('#csrf_token').val();  // Ambil CSRF token terbaru
					if (typeof originalOptions.data === 'string') {
						originalOptions.data += '&' + csrfName + '=' + csrfHash;
					} else if (typeof originalOptions.data === 'object') {
						originalOptions.data[csrfName] = csrfHash;
					}
				}
			});
			
			$(document).ajaxError(function(event, jqxhr) {
			  if (jqxhr.status === 403) {
				// Ambil token baru (bisa dari endpoint khusus)
				$.get('<?php echo base_url('request_csrf_token'); ?>', function(data) {
				  csrfHash = data.csrf_hash;
				  updateCsrfToken(csrfHash)
				});
			  }
			});
			</script>
			
			<script src="<?php echo base_url(); ?>sw-register.js"></script>
			
			<?php include_once('analytics.php'); ?>
		
	</head>
	<!--end::Head-->
	<!--begin::Body-->
	<body id="kt_body" class="auth-bg bgi-size-cover bgi-attachment-fixed bgi-position-center bgi-no-repeat">
		<!-- Particles.js container -->
		<div id="particles-js"></div>
		
		<input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>" id="csrf_token" />
		<!--begin::Theme mode setup on page load-->
		<script>var defaultThemeMode = "light"; var themeMode; if ( document.documentElement ) { if ( document.documentElement.hasAttribute("data-bs-theme-mode")) { themeMode = document.documentElement.getAttribute("data-bs-theme-mode"); } else { if ( localStorage.getItem("data-bs-theme") !== null ) { themeMode = localStorage.getItem("data-bs-theme"); } else { themeMode = defaultThemeMode; } } if (themeMode === "system") { themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light"; } document.documentElement.setAttribute("data-bs-theme", themeMode); }</script>
		<!--end::Theme mode setup on page load-->
		<!--begin::Main-->
		<div class="content-wrapper">
			<?php require_once($template_contents.'.php'); ?>
		</div>
		<!--end::Main-->
		<!--begin::Javascript-->
		<script>var hostUrl = "<?php echo base_url(); ?>themes/ortyd/assets/";</script>
		<!--begin::Global Javascript Bundle(mandatory for all pages)-->
		<script src="<?php echo base_url(); ?>themes/ortyd/assets/plugins/global/plugins.bundle.js"></script>
		<script src="<?php echo base_url(); ?>themes/ortyd/assets/js/scripts.bundle.js"></script>
		<!--end::Global Javascript Bundle-->
		<!--begin::Custom Javascript(used for this page only)-->
		
		<!--end::Custom Javascript-->
		<script>
		
		function stripHtml(html)
		{
		   let tmp = document.createElement("DIV");
		   tmp.innerHTML = html;
		   return tmp.textContent || tmp.innerText || "";
		}
		
		function updateCsrfToken(newToken) {
			// Update semua elemen input yang class-nya 'csrf'
			$('#csrf_token').val(newToken);
			$('.csrf_token').val(newToken);
		}
		
		</script>
		
		<!-- Particle.js Configuration -->
		<script>
		particlesJS('particles-js', {
			"particles": {
				"number": {
					"value": 80,
					"density": {
						"enable": true,
						"value_area": 800
					}
				},
				"color": {
					"value": "#ffffff"
				},
				"shape": {
					"type": "circle",
					"stroke": {
						"width": 0,
						"color": "#000000"
					},
					"polygon": {
						"nb_sides": 5
					}
				},
				"opacity": {
					"value": 0.5,
					"random": false,
					"anim": {
						"enable": false,
						"speed": 1,
						"opacity_min": 0.1,
						"sync": false
					}
				},
				"size": {
					"value": 3,
					"random": true,
					"anim": {
						"enable": false,
						"speed": 40,
						"size_min": 0.1,
						"sync": false
					}
				},
				"line_linked": {
					"enable": true,
					"distance": 150,
					"color": "#ffffff",
					"opacity": 0.4,
					"width": 1
				},
				"move": {
					"enable": true,
					"speed": 6,
					"direction": "none",
					"random": false,
					"straight": false,
					"out_mode": "out",
					"bounce": false,
					"attract": {
						"enable": false,
						"rotateX": 600,
						"rotateY": 1200
					}
				}
			},
			"interactivity": {
				"detect_on": "canvas",
				"events": {
					"onhover": {
						"enable": true,
						"mode": "repulse"
					},
					"onclick": {
						"enable": true,
						"mode": "push"
					},
					"resize": true
				},
				"modes": {
					"grab": {
						"distance": 400,
						"line_linked": {
							"opacity": 1
						}
					},
					"bubble": {
						"distance": 400,
						"size": 40,
						"duration": 2,
						"opacity": 8,
						"speed": 3
					},
					"repulse": {
						"distance": 200,
						"duration": 0.4
					},
					"push": {
						"particles_nb": 4
					},
					"remove": {
						"particles_nb": 2
					}
				}
			},
			"retina_detect": true
		});
		</script>
		
		<script>
		  if ('serviceWorker' in navigator) {
			navigator.serviceWorker.register('<?php echo base_url(); ?>service-worker.js')
			  .then(reg => console.log('Service Worker registered', reg))
			  .catch(err => console.error('Service Worker registration failed', err));
		  }
		</script>
		<!--end::Javascript-->
	</body>
	<!--end::Body-->
</html>