<?php
$this->output
     ->set_header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0")
     ->set_header("Cache-Control: post-check=0, pre-check=0", false)
     ->set_header("Pragma: no-cache")
     ->set_header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<title>
			<?php 
				if(isset($title)){ 
					echo $title;
				}else{ 
					echo 'HOME';
				}; 
			?> | <?php echo title; ?>
		</title> 
		<meta name="insight-app-sec-validation" content="3aa047b9-2e0c-405d-986f-cbf0123bf56d">
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<!-- Di dalam <head> -->
		<link rel="manifest" href="<?php echo base_url(); ?>manifest.json">
		<meta name="theme-color" content="#000000">
		<?php 
			if(isset($meta_description)){ 
				if($meta_description != '' && $meta_description != null){
					echo '<meta name="description" content="'.$meta_description.'" />';
				}else{
					echo '<meta name="keywords" content="'.title.' - '.subtitle.'" />';
				}
				
			}else{ 
				echo '<meta name="keywords" content="'.title.' - '.subtitle.'" />';
			}; 
		 ?>
		 
		 <meta property="og:locale" content="en_US" />
		 
		 <?php 
			if(isset($og_tipe)){ 
				if($og_tipe != '' && $og_tipe != null){
					echo '<meta property="og:type" content="'.$og_tipe.'" />';
				}else{
					echo '<meta property="og:type" content="Content - '.title.'" />';
				}
				
			}else{ 
				echo '<meta property="og:type" content="Content - '.title.'" />';
			}; 
		 ?>
		 
		 <?php 
			if(isset($og_title)){ 
				if($og_title != '' && $og_title != null){
					echo '<meta property="og:title" content="'.$og_title.'" />';
				}else{
					echo '<meta property="og:title" content="'.title.'" />';
				}
				
			}else{ 
				echo '<meta property="og:title" content="'.title.'" />';
			}; 
		 ?>
		
		<?php 
			if(isset($og_url)){ 
				if($og_url != '' && $og_url != null){
					echo '<meta property="og:url" content="'.$og_url.'" />';
				}else{
					echo '<meta property="og:url" content="'.base_url_site.'" />';
				}
				
			}else{ 
				echo '<meta property="og:url" content="'.base_url_site.'" />';
			}; 
		 ?>
		
		<meta property="og:site_name" content="<?php echo title; ?>" />
		
		<?php
			if(isset($_GET['type'])){
				if($this->ortyd->_clean_input_data($_GET['type'] == 'preview')){
					echo '<meta name="robots" content="noindex">';
					echo '<meta name="googlebot" content="noindex">';
				}
			}
		?>

		 <!-- PRELOADER STYLES - Critical, harus di-load pertama -->
    <style>
        /* PRELOADER STYLES */
        #preloader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 99999;
            transition: opacity 0.5s ease-out, visibility 0.5s ease-out;
        }

        #preloader.fade-out {
            opacity: 0;
            visibility: hidden;
        }

        .preloader-content {
            text-align: center;
            color: white;
        }

        .preloader-logo {
            width: 120px;
            height: 120px;
            margin: 0 auto 30px;
            animation: logoFloat 2s ease-in-out infinite;
        }

        .preloader-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            filter: brightness(0) invert(1);
        }

        /* LOADING ANIMATIONS */
        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 3px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
            margin: 20px auto;
        }

        .loading-dots {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 20px 0;
        }

        .loading-dots .dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background-color: white;
            margin: 0 5px;
            animation: dotBounce 1.4s ease-in-out infinite both;
        }

        .loading-dots .dot:nth-child(1) { animation-delay: -0.32s; }
        .loading-dots .dot:nth-child(2) { animation-delay: -0.16s; }
        .loading-dots .dot:nth-child(3) { animation-delay: 0s; }

        .loading-text {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 18px;
            font-weight: 300;
            margin-top: 20px;
            opacity: 0.9;
        }

        .loading-progress {
            width: 200px;
            height: 4px;
            background-color: rgba(255,255,255,0.3);
            border-radius: 2px;
            margin: 20px auto;
            overflow: hidden;
        }

        .loading-progress-bar {
            width: 0%;
            height: 100%;
            background: linear-gradient(90deg, #fff, rgba(255,255,255,0.8));
            border-radius: 2px;
            transition: width 0.3s ease;
        }

        /* KEYFRAME ANIMATIONS */
        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        @keyframes logoFloat {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        @keyframes dotBounce {
            0%, 80%, 100% { 
                transform: scale(0.8);
                opacity: 0.5;
            }
            40% { 
                transform: scale(1.2);
                opacity: 1;
            }
        }

        @keyframes slideInFromTop {
            0% {
                transform: translateY(-100px);
                opacity: 0;
            }
            100% {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* CONTENT STYLES - Untuk mencegah FOUC */
        .main-content {
            opacity: 0;
            animation: fadeIn 0.5s ease-in-out 0.5s forwards;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            .preloader-logo {
                width: 80px;
                height: 80px;
            }
            
            .loading-text {
                font-size: 16px;
            }
            
            .loading-progress {
                width: 150px;
            }
        }
    </style>
	
	
	  <!-- PRELOAD CRITICAL RESOURCES -->
    <link rel="preload" href="<?php echo base_url(); ?>themes/ortyd_frontend/css/theme.css?v=<?=time()?>" as="style">
    <link rel="preload" href="<?php echo base_url(); ?>themes/ortyd_frontend/vendor/jquery/jquery.min.js" as="script">
    <link rel="preload" href="<?php echo base_url(); ?>favicon.png" as="image">
	
		<!-- Favicon -->
		<link rel="shortcut icon" href="<?php echo base_url(); ?>favicon.png" type="image/x-icon" />
		<link rel="apple-touch-icon" href="img/apple-touch-icon.png">


		<!-- Web Fonts  -->
		<link id="googleFonts" href="https://fonts.googleapis.com/css?family=Merriweather:300,400,700,900%7CPoppins:200,300,400,500,600,700,800&display=swap" rel="stylesheet" type="text/css">

		<!-- Vendor CSS -->
		<link rel="stylesheet" href="<?php echo base_url(); ?>themes/ortyd_frontend/vendor/bootstrap/css/bootstrap.min.css">
		<link rel="stylesheet" href="<?php echo base_url(); ?>themes/ortyd_frontend/vendor/fontawesome-free/css/all.min.css">
		<link rel="stylesheet" href="<?php echo base_url(); ?>themes/ortyd_frontend/vendor/animate/animate.compat.css">
		<link rel="stylesheet" href="<?php echo base_url(); ?>themes/ortyd_frontend/vendor/simple-line-icons/css/simple-line-icons.min.css">
		<link rel="stylesheet" href="<?php echo base_url(); ?>themes/ortyd_frontend/vendor/owl.carousel/assets/owl.carousel.min.css">
		<link rel="stylesheet" href="<?php echo base_url(); ?>themes/ortyd_frontend/vendor/owl.carousel/assets/owl.theme.default.min.css">
		<link rel="stylesheet" href="<?php echo base_url(); ?>themes/ortyd_frontend/vendor/magnific-popup/magnific-popup.min.css">
		
		<link href="<?php echo base_url(); ?>themes/ortyd/assets/plugins/custom/datatables/datatables.bundle.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo base_url(); ?>themes/ortyd/assets/vendors/sweetalert2-main/sweetalert2.min.css" rel="stylesheet">

		<!-- Theme CSS -->
		<link rel="stylesheet" href="<?php echo base_url(); ?>themes/ortyd_frontend/css/theme.css?v=<?=time()?>">
		<link rel="stylesheet" href="<?php echo base_url(); ?>themes/ortyd_frontend/css/theme-elements.css">
		<link rel="stylesheet" href="<?php echo base_url(); ?>themes/ortyd_frontend/css/theme-blog.css">
		<link rel="stylesheet" href="<?php echo base_url(); ?>themes/ortyd_frontend/css/theme-shop.css">

		<!-- Revolution Slider CSS -->
		<link rel="stylesheet" href="<?php echo base_url(); ?>themes/ortyd_frontend/vendor/rs-plugin/css/settings.css">
		<link rel="stylesheet" href="<?php echo base_url(); ?>themes/ortyd_frontend/vendor/rs-plugin/css/layers.css">
		<link rel="stylesheet" href="<?php echo base_url(); ?>themes/ortyd_frontend/vendor/rs-plugin/css/navigation.css">

		<!-- Demo CSS -->
		<link rel="stylesheet" href="<?php echo base_url(); ?>themes/ortyd_frontend/css/demos/demo-law-firm.css">

		<!-- Skin CSS -->
		<link id="skinCSS" rel="stylesheet" href="<?php echo base_url(); ?>themes/ortyd_frontend/css/skins/skin-law-firm.css">

		<link href="<?php echo base_url(); ?>themes/ortyd_frontend/vendor/select2/css/select2.min.css" type="text/css" rel="stylesheet">
		
		<!-- Theme Custom CSS -->
		<link rel="stylesheet" href="<?php echo base_url(); ?>themes/ortyd_frontend/css/custom.css">

		<script src="<?php echo base_url(); ?>themes/ortyd_frontend/vendor/jquery/jquery.min.js"></script>
		
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
		
		<!-- Head Libs -->
		<script src="<?php echo base_url(); ?>themes/ortyd_frontend/vendor/modernizr/modernizr.min.js"></script>
		
		<script type="text/javascript" src="<?php echo base_url(); ?>themes/ortyd/assets/vendors/sweetalert2-main/sweetalert2.all.min.js"></script> 
		
		<script type="text/javascript" src="<?php echo base_url(); ?>themes/ortyd_frontend/vendor/select2/js/select2.min.js" ></script> 
		<script type="text/javascript" src="<?php echo base_url(); ?>themes/ortyd/assets/js/custom/bootbox.min.js" type="text/javascript"></script> 
		
	
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
			

		
		<?php include_once('analytics.php'); ?>
		
		
		<script src="<?php echo base_url(); ?>themes/ortyd_frontend/js/lazy-loading.js"></script>

</head>
<body>


<!-- PRELOADER -->
    <div id="preloader" style="display:none">
        <div class="preloader-content">
            <div class="preloader-logo">
                <img src="<?php echo base_url(); ?>logo-dark.png" alt="Loading..." id="preloader-logo">
            </div>
            
            <!-- LOADING SPINNER -->
            <div class="loading-spinner"></div>
            
            <!-- LOADING DOTS -->
            <div class="loading-dots">
                <div class="dot"></div>
                <div class="dot"></div>
                <div class="dot"></div>
            </div>
            
            <!-- LOADING TEXT -->
            <div class="loading-text" id="loading-text">Memuat halaman...</div>
            
            <!-- PROGRESS BAR -->
            <div class="loading-progress">
                <div class="loading-progress-bar" id="progress-bar"></div>
            </div>
        </div>
    </div>
	
	<div class="body">
		<input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>" id="csrf_token" />

<header id="header" data-plugin-options="{'stickyEnabled': true, 'stickyEnableOnBoxed': true, 'stickyEnableOnMobile': false, 'stickyStartAt': 107, 'stickySetTop': '-107px', 'stickyChangeLogo': true}">
    <div class="header-body border-color-primary border-top-0 box-shadow-none">
        <div class="header-container container z-index-2">
            <div class="header-row">
                <div class="header-column">
                    <div class="header-row">
                        <div class="header-logo">
                            <a href="<?php echo base_url(); ?>">
                               <?php
								$logoPath = $this->ortyd->select2_getname(
									$this->ortyd->getMeta_cover('company_header_logo'),
									"data_gallery",
									"id",
									"path"
								);

								// Cek jika path kosong atau file tidak ditemukan di FCPATH
								if ($logoPath == '' ||  empty($logoPath) || !file_exists(FCPATH . $logoPath)) {
									$logoPath = 'logo-dark.png'; // Sesuaikan path logo default
								}
								?>

								<img alt="BGN" height="75" src="<?php echo base_url($logoPath); ?>">
                            </a>
                        </div>
                    </div>
                </div>
                <div class="header-column justify-content-end">
                    <div class="header-row h-100">
                        <ul class="header-extra-info d-flex h-100 align-items-center">
                           

                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="header-nav-bar">
            <div class="container">
                <div class="header-row">
                    <div class="header-column">
                        <div class="header-row">
                            <div class="header-column">
                                <?php
                                $mainMenus = $this->db->select('master_menu_frontend.*')
                                    ->where(['parent' => '0', 'active' => 1])
                                    ->order_by('sort', 'ASC')
                                    ->get('master_menu_frontend')
                                    ->result_array();

                                function isActive($link) {
                                    $currentUrl = current_url();
                                    return (strpos($currentUrl, $link) !== false) ? ' active' : '';
                                }

                                function buildMenuItem($row, $tree, $ortyd) {
                                    $link = $row['direct_link'] == 1 ? $row['link'] : base_url() . $row['link'];
                                    $hasChildren = count($tree) > 0;
                                    $target = $row['target_blank'] == 1 ? ' target="_blank"' : '';
                                    $activeClass = isActive($row['link']);
                                    $icon = $row['icon'] ? '<i class="' . $row['icon'] . ' me-1"></i> ' : '';

                                    if ($row['slug'] == 'download') {
                                        return buildDownloadMenu($row, $ortyd);
                                    }

                                    $html = '<li class="dropdown' . ($hasChildren ? ' dropdown-reverse' : '') . '">';
                                    $html .= '<a class="dropdown-item' . ($hasChildren ? ' dropdown-toggle' : '') . $activeClass . '"' . $target . ' href="' . ($hasChildren ? '#' : $link) . '">' . $icon . $ortyd->translate_google($row['name']) . '</a>';

                                    if ($hasChildren) {
                                        $html .= '<ul class="dropdown-menu sm-nowrap">';
                                        foreach ($tree as $child) {
                                            $subTree = $ortyd->buildTree($child['id']);
                                            $html .= buildSubMenuItem($child, $subTree, $ortyd);
                                        }
                                        $html .= '</ul>';
                                    }
                                    $html .= '</li>';
                                    return $html;
                                }

                                function buildSubMenuItem($row, $tree, $ortyd) {
                                    $link = $row['direct_link'] == 1 ? $row['link'] : base_url() . $row['link'];
                                    $target = $row['target_blank'] == 1 ? ' target="_blank"' : '';
                                    $name = $ortyd->translate_google($row['name']);
                                    $icon = $row['icon'] ? '<i class="' . $row['icon'] . ' me-1"></i> ' : '';

                                    if (count($tree) > 0) {
                                        $html = '<li class="dropdown-submenu">';
                                        $html .= '<a class="dropdown-item"' . $target . ' href="' . $link . '">' . $icon . $name . '</a>';
                                        $html .= '<ul class="dropdown-menu sm-nowrap">';
                                        foreach ($tree as $child) {
                                            $html .= buildSubMenuItem($child, $ortyd->buildTree($child['id']), $ortyd);
                                        }
                                        $html .= '</ul></li>';
                                        return $html;
                                    }

                                    return '<li class="nav-item"><a class="dropdown-item"' . $target . ' href="' . $link . '">' . $icon . $name . '</a></li>';
                                }

                                function buildDownloadMenu($row, $ortyd) {
                                    $icon = $row['icon'] ? '<i class="' . $row['icon'] . ' me-1"></i> ' : '';
                                    $html = '<li class="dropdown dropdown-reverse">';
                                    $html .= '<a class="dropdown-item dropdown-toggle" href="#">' . $icon . $ortyd->translate_google($row['name']) . '</a>';
                                    $html .= '<ul class="dropdown-menu">';
                                    $html .= '<li><a class="dropdown-item" href="' . base_url() . 'download">' . $ortyd->translate_google('Semua Berkas') . '</a></li>';

                                    $types = get_instance()->db->where('active', 1)->order_by('sort', 'ASC')->get('master_download_type')->result_array();
                                    foreach ($types as $type) {
                                        $html .= '<li><a class="dropdown-item" href="' . base_url() . 'download/' . $type['slug'] . '">' . $ortyd->translate_google($type['name']) . '</a></li>';
                                    }

                                    $html .= '</ul></li>';
                                    return $html;
                                }
                                ?>

                              <div class="header-nav justify-content-start header-nav-line header-nav-bottom-line header-nav-bottom-line-effect-1">
									<div class="header-nav-main header-nav-main-square header-nav-main-dropdown-no-borders header-nav-main-effect-2 header-nav-main-sub-effect-1">
										<nav class="collapse">
											<ul class="nav nav-pills" id="mainNav">
												<?php foreach ($mainMenus as $row): ?>
													<?php $tree = $this->ortyd->buildTree($row['id']); ?>
													<?= buildMenuItem($row, $tree, $this->ortyd); ?>
												<?php endforeach; ?>

											</ul>
										</nav>
									</div>
									
								
								</div>

								

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <button class="btn header-btn-collapse-nav" data-bs-toggle="collapse" data-bs-target=".header-nav-main nav">
        <i class="fas fa-bars"></i>
    </button>
</header>

			
			<div role="main" class="main">
			
				<?php require_once($template_contents.'.php'); ?>
				
			</div>
			
			<footer id="footer" class="border-top-0 mt-0" style="background:#606365">
				<div class="container py-4">
					<div class="row py-5">
						<div class="col-md-6 mb-4 mb-lg-0">
							<a href="<?php echo base_url(); ?>" class="logo pe-0 pe-lg-3 pb-4">

								 <?php
									$logoPath = $this->ortyd->select2_getname(
										$this->ortyd->getMeta_cover('company_header_footer_logo'),
										"data_gallery",
										"id",
										"path"
									);

									// Cek jika path kosong atau file tidak ditemukan di FCPATH
									if ($logoPath == '' ||  empty($logoPath) || !file_exists(FCPATH . $logoPath)) {
										 $logoPath = 'logo-badan.png'; // Sesuaikan path logo default
									}
									?>
									
									<img style="width: 100%;max-width: 400px;border-radius: 10px;" alt="BGN" width="" height="" src="<?php echo base_url($logoPath); ?>">
								
								
							</a>
							<p class="pt-3 mb-2" style="color:#fff">
							
							<?php echo $this->ortyd->getMeta('company_footer_text'); ?>
							
							</p>

						</div>
						
						<div class="col-md-6">
							
							<h5 class="text-4-5 text-transform-none custom-font-primary mb-3"><?php echo $this->ortyd->translate_google('Jam Layanan'); ?></h5>
							<strong style="color:#fff !important" class="custom-footer-strong-1"><?php echo $this->ortyd->getMeta('company_bussines_hour'); ?></strong>
							<br>
							<h5 class="text-4-5 text-transform-none custom-font-primary mb-3"><?php echo $this->ortyd->translate_google('Ikuti Kami di Media Sosial'); ?></h5>

							<ul class="custom-social-icons-style-1 social-icons social-icons-clean">
								<li class="social-icons-instagram">
									<a href="<?php echo $this->ortyd->getMeta('company_instagram'); ?>" class="no-footer-css" target="_blank" title="Instagram"><i style="color:#fff !important"  class="text-primary fab fa-instagram"></i></a>
								</li>
								<li class="social-icons-twitter mx-4">
									<a href="<?php echo $this->ortyd->getMeta('company_twitter'); ?>" class="no-footer-css" target="_blank" title="Twitter"><i style="color:#fff !important"  class="text-primary fab fa-twitter"></i></a>
								</li>
								<li class="social-icons-facebook mx-2">
									<a href="<?php echo $this->ortyd->getMeta('company_facebook'); ?>" class="no-footer-css" target="_blank" title="Facebook"><i style="color:#fff !important"  class="text-primary fab fa-facebook-f"></i></a>
								</li>
								<li class="social-icons-linkedin mx-4">
									<a href="<?php echo $this->ortyd->getMeta('company_linked_in'); ?>" class="no-footer-css" target="_blank" title="Linked in"><i style="color:#fff !important"  class="text-primary fab fa-linkedin"></i></a>
								</li>
								<li class="social-icons-youtube">
									<a href="<?php echo $this->ortyd->getMeta('company_youtube'); ?>" class="no-footer-css" target="_blank" title="Youtube"><i style="color:#fff !important"  class="text-primary fab fa-youtube"></i></a>
								</li>
							</ul>

						</div>
					
					
					</div>
				</div>
				<div class="footer-copyright footer-copyright-style-2">
					<div class="container py-2">
						<div class="row py-4">
							<div class="col d-flex align-items-center justify-content-center">
								<p style="color:#fff">© <?php echo $this->ortyd->translate_google('Hak Cipta'); ?> 2025 - <?php echo $this->ortyd->translate_google('Badan Gizi Nasional'); ?>.</p>
							</div>
						</div>
					</div>
				</div>
			</footer>

	</div>
	
	<!-- Vendor -->
		
				<script src='https://www.google.com/recaptcha/api.js'></script>
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css">
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css">

		<script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
		
		<!-- Dropzone CSS -->
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.css" />

		<!-- Dropzone JS -->
		<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js"></script>

		
		<script src="<?php echo base_url(); ?>sw-register.js"></script>
		
		<script src="<?php echo base_url(); ?>themes/ortyd_frontend/vendor/jquery.appear/jquery.appear.min.js"></script>
		<script src="<?php echo base_url(); ?>themes/ortyd_frontend/vendor/jquery.easing/jquery.easing.min.js"></script>
		<script src="<?php echo base_url(); ?>themes/ortyd_frontend/vendor/jquery.cookie/jquery.cookie.min.js"></script>
		<script src="<?php echo base_url(); ?>themes/ortyd_frontend/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
		<script src="<?php echo base_url(); ?>themes/ortyd_frontend/vendor/jquery.validation/jquery.validate.min.js"></script>
		<script src="<?php echo base_url(); ?>themes/ortyd_frontend/vendor/jquery.easy-pie-chart/jquery.easypiechart.min.js"></script>
		<script src="<?php echo base_url(); ?>themes/ortyd_frontend/vendor/jquery.gmap/jquery.gmap.min.js"></script>
		<script src="<?php echo base_url(); ?>themes/ortyd_frontend/vendor/lazysizes/lazysizes.min.js"></script>
		<script src="<?php echo base_url(); ?>themes/ortyd_frontend/vendor/isotope/jquery.isotope.min.js"></script>
		<script src="<?php echo base_url(); ?>themes/ortyd_frontend/vendor/owl.carousel/owl.carousel.min.js"></script>
		<script src="<?php echo base_url(); ?>themes/ortyd_frontend/vendor/magnific-popup/jquery.magnific-popup.min.js"></script>
		<script src="<?php echo base_url(); ?>themes/ortyd_frontend/vendor/vide/jquery.vide.min.js"></script>
		<script src="<?php echo base_url(); ?>themes/ortyd_frontend/vendor/vivus/vivus.min.js"></script>

		<!-- Theme Base, Components and Settings -->
		<script src="<?php echo base_url(); ?>themes/ortyd_frontend/js/theme.js"></script>

		<!-- Revolution Slider Scripts -->
		<script src="<?php echo base_url(); ?>themes/ortyd_frontend/vendor/rs-plugin/js/jquery.themepunch.tools.min.js"></script>
		<script src="<?php echo base_url(); ?>themes/ortyd_frontend/vendor/rs-plugin/js/jquery.themepunch.revolution.min.js"></script>

		<!-- Current Page Vendor and Views -->
		<script src="<?php echo base_url(); ?>themes/ortyd_frontend/js/views/view.contact.js"></script>

		<!-- Demo -->
		<script src="<?php echo base_url(); ?>themes/ortyd_frontend/js/demos/demo-law-firm.js"></script>

		<!-- Theme Custom -->
		<script src="<?php echo base_url(); ?>themes/ortyd_frontend/js/custom.js"></script>

		<!-- Theme Initialization Files -->
		<script src="<?php echo base_url(); ?>themes/ortyd_frontend/js/theme.init.js"></script>
		
		<script src="<?php echo base_url(); ?>themes/ortyd/assets/plugins/custom/datatables/datatables.bundle.js"></script>
		
		
		<?php //echo $this->ortyd->getHistoryAkses(); ?>
		

<?php require_once('common/changeheader.php'); ?>


<!-- LOADING SCRIPTS -->
    <script>
        // PRELOADER CONTROLLER
        class PreloaderController {
            constructor() {
                this.preloader = document.getElementById('preloader');
                this.mainContent = document.getElementById('main-content');
                this.progressBar = document.getElementById('progress-bar');
                this.loadingText = document.getElementById('loading-text');
                
                this.loadingSteps = [
                    { text: 'Memuat halaman...', progress: 10 },
                    { text: 'Menghubungkan database...', progress: 30 },
                    { text: 'Memuat assets...', progress: 50 },
                    { text: 'Memuat konten...', progress: 70 },
                    { text: 'Menyelesaikan...', progress: 90 },
                    { text: 'Selesai!', progress: 100 }
                ];
                
                this.currentStep = 0;
                this.resourcesLoaded = 0;
                this.totalResources = 0;
                
                this.init();
            }
            
            init() {
                // Mulai animasi loading
                this.startLoading();
                
                // Monitor resource loading
                this.monitorResources();
                
                // Set minimum loading time
                this.minLoadTime = 2000; // 2 detik minimum
                this.startTime = Date.now();
            }
            
            startLoading() {
                this.updateStep();
                
                // Update step setiap 500ms
                this.stepInterval = setInterval(() => {
                    if (this.currentStep < this.loadingSteps.length - 1) {
                        this.currentStep++;
                        this.updateStep();
                    }
                }, 500);
            }
            
            updateStep() {
                const step = this.loadingSteps[this.currentStep];
                this.loadingText.textContent = step.text;
                this.progressBar.style.width = step.progress + '%';
            }
            
            monitorResources() {
                // Hitung total resources yang perlu dimuat
                const images = document.querySelectorAll('img');
                const links = document.querySelectorAll('link[rel="stylesheet"]');
                const scripts = document.querySelectorAll('script[src]');
                
                this.totalResources = images.length + links.length + scripts.length;
                
                // Monitor image loading
                images.forEach(img => {
                    if (img.complete) {
                        this.resourceLoaded();
                    } else {
                        img.addEventListener('load', () => this.resourceLoaded());
                        img.addEventListener('error', () => this.resourceLoaded());
                    }
                });
                
                // Monitor CSS loading
                links.forEach(link => {
                    link.addEventListener('load', () => this.resourceLoaded());
                    link.addEventListener('error', () => this.resourceLoaded());
                });
                
                // Monitor script loading
                scripts.forEach(script => {
                    script.addEventListener('load', () => this.resourceLoaded());
                    script.addEventListener('error', () => this.resourceLoaded());
                });
                
                // Fallback untuk DOM ready
                if (document.readyState === 'complete') {
                    setTimeout(() => this.checkComplete(), 100);
                } else {
                    window.addEventListener('load', () => this.checkComplete());
                }
            }
            
            resourceLoaded() {
                this.resourcesLoaded++;
                
                // Update progress berdasarkan resource yang dimuat
                const resourceProgress = (this.resourcesLoaded / this.totalResources) * 100;
                const currentProgress = Math.max(
                    this.loadingSteps[this.currentStep].progress,
                    resourceProgress
                );
                
                this.progressBar.style.width = currentProgress + '%';
                
                // Check jika semua resource sudah dimuat
                if (this.resourcesLoaded >= this.totalResources) {
                    this.checkComplete();
                }
            }
            
            checkComplete() {
                const elapsedTime = Date.now() - this.startTime;
                const remainingTime = Math.max(0, this.minLoadTime - elapsedTime);
                
            }
            
            hidePreloader() {
                // Clear interval
                if (this.stepInterval) {
                    clearInterval(this.stepInterval);
                }
                
                // Update ke step terakhir
                this.currentStep = this.loadingSteps.length - 1;
                this.updateStep();
                
                // Fade out preloader
                setTimeout(() => {
                    this.preloader.classList.add('fade-out');
                    
                    // Show main content
                    setTimeout(() => {
                        this.preloader.style.display = 'none';
                        this.mainContent.classList.remove('hidden');
                        
                        // Show contact info dengan animasi
                        const contactInfo = document.querySelector('.header-contact');
                        if (contactInfo) {
                            setTimeout(() => {
                                contactInfo.style.display = 'block';
                                contactInfo.style.animation = 'slideInFromTop 0.5s ease-out';
                            }, 300);
                        }
                        
                        // Trigger custom event
                        window.dispatchEvent(new Event('preloaderComplete'));
                        
                    }, 500);
                }, 300);
            }
        }
        
        // Initialize preloader
        document.addEventListener('DOMContentLoaded', function() {
            new PreloaderController();
        });
        
        // CSRF Setup
        window.csrfData = {
            name: '<?= $this->security->get_csrf_token_name(); ?>',
            hash: '<?= $this->security->get_csrf_hash(); ?>'
        };
        
        // Mobile menu toggle
        document.addEventListener('preloaderComplete', function() {
            const toggle = document.querySelector('.mobile-menu-toggle');
            const navMenu = document.querySelector('.nav-menu');
            
            // Show mobile toggle on small screens
            function checkMobile() {
                if (window.innerWidth <= 768) {
                    toggle.style.display = 'block';
                    navMenu.style.display = 'none';
                } else {
                    toggle.style.display = 'none';
                    navMenu.style.display = 'flex';
                }
            }
            
            checkMobile();
            window.addEventListener('resize', checkMobile);
            
            // Toggle functionality
            toggle.addEventListener('click', function() {
                const isVisible = navMenu.style.display === 'flex';
                navMenu.style.display = isVisible ? 'none' : 'flex';
                navMenu.style.flexDirection = 'column';
                navMenu.style.position = 'absolute';
                navMenu.style.top = '100%';
                navMenu.style.left = '0';
                navMenu.style.right = '0';
                navMenu.style.background = '#f8f9fa';
                navMenu.style.padding = '20px';
                navMenu.style.boxShadow = '0 2px 10px rgba(0,0,0,0.1)';
            });
        });
    </script>
	
<script>
	 var baseurl = "<?php echo base_url(); ?>";
		
		function stripHtml(html)
		{
		   let tmp = document.createElement("DIV");
		   tmp.innerHTML = html;
		   return tmp.textContent || tmp.innerText || "";
		}
		

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
		
		function updateCsrfToken(newToken) {
			csrfHash = newToken;
			// Update semua elemen input yang class-nya 'csrf'
			$('#csrf_token').val(csrfHash);
			$('.csrf_token').val(csrfHash);
		}
		
		
    </script>
	
	<script>
		  if ('serviceWorker' in navigator) {
			navigator.serviceWorker.register('<?php echo base_url(); ?>service-worker.js')
			  .then(reg => console.log('Service Worker registered', reg))
			  .catch(err => console.error('Service Worker registration failed', err));
		  }
	</script>
	
	</div>
	
		
<script>
document.addEventListener("DOMContentLoaded", function () {
    const targetElements = document.querySelectorAll(".owl-item.position-relative.active.fadeIn.animated");
    const defaultElements = document.querySelectorAll(".bg-color-quaternary, .bg-quaternary");

    // Warna gradien utama
    const baseGradient = ['rgb(161, 196, 253)', 'rgb(194, 233, 251)', 'rgb(212, 241, 254)'];

    // Variasi arah gradien
    const directions = [0, 45, 90, 135, 180, 225, 270, 315];

    function applyGradient(elements) {
        const direction = directions[Math.floor(Math.random() * directions.length)];
        const gradientString = `linear-gradient(${direction}deg, ${baseGradient.join(', ')})`;

        elements.forEach(el => {
            el.style.setProperty('background', gradientString, 'important');
        });
    }

    function updateBackground() {
        if (targetElements.length > 0) {
            applyGradient(targetElements);
        } else if (defaultElements.length > 0) {
            applyGradient(defaultElements);
        }
    }

    updateBackground();
    setInterval(updateBackground, 30000);
	
	document.querySelectorAll('.col-baris.container-div').forEach(function (colBaris) {
        // Cek apakah sudah ada container di dalamnya agar tidak double wrap
        const hasInnerContainer = Array.from(colBaris.children).some(child => child.classList.contains('container'));
        if (hasInnerContainer) return;

		
        const wrapper = document.createElement('div');
        wrapper.className = 'container';

        // Pindahkan semua isi ke dalam wrapper
        while (colBaris.firstChild) {
            wrapper.appendChild(colBaris.firstChild);
			wrapper.classList.add('row');
        }

        colBaris.appendChild(wrapper);
    });
	
	document.querySelectorAll('.bg-overlay-bs').forEach(function (colBaris) {
        // Cek agar tidak duplikat
        if (!colBaris.querySelector(':scope > .curve-svg')) {
            const curve = document.createElement('div');
            curve.className = 'curve-svg';
            curve.innerHTML = `
                <svg viewBox="0 0 1440 100" preserveAspectRatio="none">
                    <path d="M0,0 C480,100 960,0 1440,100 L1440,0 L0,0 Z" fill="#fff"></path>
                </svg>
            `;
            // Sisipkan sebagai elemen pertama
            colBaris.insertBefore(curve, colBaris.firstElementChild);
        }
    });
	
	
	
});



</script>



<style>
/* Banner cookie */
#cookie-banner {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: #333;
    color: #fff;
    padding: 15px;
    text-align: center;
    display: none;
    z-index: 9999;
    font-family: Arial, sans-serif;
}
#cookie-banner button {
    background: #f0c040;
    color: #000;
    border: none;
    padding: 8px 15px;
    margin-left: 10px;
    cursor: pointer;
    font-weight: bold;
}
</style>

<div id="cookie-banner">
    This website uses cookies to ensure you get the best experience.
    <button id="accept-cookies">Accept</button>
</div>

<script>
$(document).ready(function() {
    // Fungsi untuk mendapatkan cookie
    function getCookie(name) {
        const value = "; " + document.cookie;
        const parts = value.split("; " + name + "=");
        if (parts.length === 2) return parts.pop().split(";").shift();
        return null;
    }

    // Fungsi untuk membuat cookie
    function setCookie(name, value, days) {
        const d = new Date();
        d.setTime(d.getTime() + (days*24*60*60*1000));
        document.cookie = name + "=" + value + "; expires=" + d.toUTCString() + "; path=/";
    }

    // Cek apakah cookies di browser diizinkan
    function areCookiesEnabled() {
        try {
            document.cookie = "testcookie=1";
            const enabled = document.cookie.indexOf("testcookie") !== -1;
            // Hapus cookie percobaan
            document.cookie = "testcookie=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
            return enabled;
        } catch (e) {
            return false;
        }
    }

    // Tampilkan banner jika cookie permission belum ada
    if (!getCookie('cookie_permission')) {
        $('#cookie-banner').fadeIn();
    }

    // Klik tombol Accept
    $('#accept-cookies').click(function() {
        if (areCookiesEnabled()) {
            setCookie('cookie_permission', 'granted', 30);
            $('#cookie-banner').fadeOut();
            alert('Thank you! Cookies are now enabled.');
        } else {
            alert('Cookies are blocked in your browser. Please enable cookies to continue.');
        }
    });
});
</script>

</body>
</html>