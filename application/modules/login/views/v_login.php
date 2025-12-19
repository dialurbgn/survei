<style>
		.field-icon {
			     float: right;
				margin-left: -50px;
				margin-top:-47px;
				position: relative;
				z-index: 2;
				padding: 25px;
			}
			
			.splide__arrows {
		display: none;
	}
	
	::placeholder {
  color: #000 !important;
  opacity: 1; /* Firefox */
}

::-ms-input-placeholder { /* Edge 12 -18 */
  color: #000  !important;
}
			
			

	</style>
	
	<?php 
		$iddata = 1;
	?>
<!--begin::Root-->
		<div class="d-flex flex-column flex-root">
			<!--begin::Page bg image-->
			<!--end::Page bg image-->
			<!--begin::Authentication - Sign-in -->
			<div class="d-flex flex-column flex-column-fluid flex-lg-row">
				<!--begin::Aside-->
				<div class="d-flex flex-center w-lg-50 pt-15 pt-lg-0 px-10">
					<!--begin::Aside-->
					<div class="d-flex flex-center flex-lg-start flex-column" style="    text-align: center;
    background: #fff;
    padding: 30px;
    border-radius: 20px;
    opacity: 0.7;">
						<!--begin::Logo-->
						<a href="#" class="mb-7" style="width:100%;text-align:center">
							<img alt="Logo" style="max-width:300px" src="<?php echo base_url(); ?>logo-dark.png" />
						</a>
						<!--end::Logo-->
						<!--begin::Title-->
						<div style="
							font-size: 30px;
							text-align: center;
							width: 100%;
							color: #333 !important; /* abu gelap, lebih nyaman dari #000 */
							font-weight: bold !important;
							font-family: 'Segoe UI', 'Roboto', 'Helvetica Neue', sans-serif;
							letter-spacing: 1px;
							line-height: 1.4;
							margin: 0;
							text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.15); /* bayangan halus */
							background-color: #fff; /* jika perlu background div juga putih */
						">
						</div>

						<!--end::Title-->
					</div>
					<!--begin::Aside-->
				</div>
				<!--begin::Aside-->
				<!--begin::Body-->
				<div class="d-flex flex-column-fluid flex-lg-row-auto justify-content-center justify-content-lg-end p-12 p-lg-5">
					<!--begin::Card-->
					<div class="bg-body d-flex flex-column align-items-stretch flex-center rounded-4 w-md-600px p-20" style="opacity: 0.9;background-color: #ffffff  !important;
    color: #fff;">
						<!--begin::Wrapper-->
						<div class="d-flex flex-center flex-column flex-column-fluid px-lg-5 pb-5 pb-lg-5">
							<!--begin::Form-->
							<form class="form w-100" novalidate="novalidate" id="form<?php echo $iddata; ?>" data-kt-redirect-url="" action="<?php echo $action; ?>" method="POST">
								<!--begin::Heading-->
								<div class="text-center mb-11">
									<!--begin::Title-->
									<h1 class="text-black fw-bolder mb-3">
										<?php echo subtitle; ?>
									</h1>
									<!--end::Title-->
									<!--begin::Subtitle-->
									<div class="text-black fw-semibold fs-6">Login Akun Anda Sekarang !</div>
									<!--end::Subtitle=-->
								</div>
								
								<!--begin::Login options-->
								<div class="row g-3 mb-9">
									<!--begin::Col-->
									<div class="col-md-12">
										<!--begin::Google link=-->
										<a href="<?php echo $googlelink; ?>" class="btn btn-flex  btn-color-black btn-outline btn-text-black-700 btn-active-color-primary bg-state-light flex-center text-nowrap w-100">
										<img alt="Logo" src="<?php echo base_url(); ?>themes/ortyd/assets/media/svg/brand-logos/google-icon.svg" class="h-15px me-3" />Sign in with Google</a>
										<!--end::Google link=-->
									</div>
									<!--end::Col-->
									<!--begin::Col-->
									
								</div>
								<!--end::Login options-->
								
									<!--begin::Col-->
								<div class="col-md-12">
									<!--begin::Telegram link=-->
									<a href="<?php echo telegram_link; ?>" target="_blank" class="btn btn-flex btn-color-black btn-outline btn-text-black-700 btn-active-color-primary bg-state-light flex-center text-nowrap w-100">
									<img alt="Telegram" src="<?php echo base_url(); ?>themes/ortyd/assets/media/svg/brand-logos/telegram-icon.svg" class="h-15px me-3" />Login with Telegram</a>
									<!--end::Telegram link=-->
								</div>
								<!--end::Col-->
								
								<!--begin::Separator-->
								<div class="separator separator-content my-14">
									<span class="w-125px text-black fw-semibold fs-7">Or with email</span>
								</div>
								
								<!--begin::Heading-->
								<!--begin::Input group=-->
								<div class="fv-row mb-8 text-black">
									<!--begin::Email-->
									<input type="text" placeholder="Masukan Alamat Email/Username Terdaftar" name="username" autocomplete="off" class="form-control form-control-sm bg-transparent text-black" required />
									<!--end::Email-->
								</div>
								<!--end::Input group=-->
								<div class="fv-row mb-3 text-black">
									<!--begin::Password-->
									<input id="password" type="password" placeholder="Masukan Password Terdaftar" name="password" autocomplete="off" class="form-control form-control-sm bg-transparent text-black" required />
									<span toggle="#password-field" class="fa fa-fw fa-eye field-icon toggle-password"></span>
									
									<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" class="csrf_token" placeholder="CSRF Invalid" required />
									<!--end::Password-->
								</div>
								
								
								
								<div class="fv-row mb-3">
									<div class="g-recaptcha" data-sitekey="<?php echo $site_key; ?>"></div>
									<span class="text-danger"><?php echo form_error('g-recaptcha-response'); ?></span>
								</div>

								<!--end::Input group=-->
								<!--begin::Wrapper-->
								<div style="" class="d-flex flex-stack flex-wrap gap-3 fs-base fw-semibold mb-8">
									<div></div>
									<!--begin::Link-->
									<a href="<?php echo base_url('forgot'); ?>" class="link-primary text-black">Lupa Password ?</a>
									<!--end::Link-->
								</div>
								<!--end::Wrapper-->
								<!--begin::Submit button-->
								
								<div class="form-check mb-3 text-black">
								  <input class="form-check-input" type="checkbox" name="remember_me" id="remember_me">
								  <label class="form-check-label text-black" for="remember_me">
									Ingat saya
								  </label>
								</div>
								
								
			
								<div class="d-grid mb-2">
									<button type="button" id="kt_docs_formvalidation_text_submit" class="btn btn-primary">
										<!--begin::Indicator label-->
										<span class="indicator-label">Masuk dan Akses Dashboard Anda</span>
										<!--end::Indicator label-->
										<!--begin::Indicator progress-->
										<span class="indicator-progress">Menunggu ...
										<span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
										<!--end::Indicator progress-->
									</button>
								</div>
								<div class="d-grid mb-2">
									<a class="btn btn-danger" href="<?php echo base_url(); ?>">
										Kembali Ke Halaman Utama
									</a>
								</div>
								<!-- Links for forgot and sign-up -->
								<div class="mt-4 text-center" style="display:none">
									<a href="<?php echo base_url('register'); ?>" class="text-black fs-15 text-decoration-none">Belum punya akun ? buat disini</a>
								</div>
								<!--end::Submit button-->
								<!--begin::Sign up-->

								<!--end::Sign up-->
							</form>
							<!--end::Form-->
						</div>
						<!--end::Wrapper-->
					</div>
					<!--end::Card-->
				</div>
				<!--end::Body-->
			</div>
			<!--end::Authentication - Sign-in-->
		</div>
		<!--end::Root-->


	
	<script>
	
	// By default do not allow form submission.
	var allow_submit = false

	function captcha_filled () {
		/*
		 * This is called when Google get's the recaptcha response and approves it.
		 * Setting allow_submit = true will let the form POST as normal.
		 * */

		allow_submit = true
	}

	function captcha_expired () {
		/*
		 * This is called when Google determines too much time has passed and expires the approval.
		 * Setting allow_submit = false will prevent the form from being submitted.
		 * */

		allow_submit = false
	}


	function check_captcha_filled (e) {
		console.log('captcha-verified')
		/*
		 * This will be called when the form is submitted.
		 * If Google determines the captcha is OK, it will have
		 * called captcha_filled which sets allow_submit = true
		 * If the captcha has not been filled out, allow_submit
		 * will still be false.
		 * We check allow_submit and prevent the form from being submitted
		 * if the value of allow_submit is false.
		 * */

		// If captcha_filled has not been called, allow_submit will be false.
		// In this case, we want to prevent the form from being submitted.
		if (!allow_submit) {
			// This call prevents the form submission.
			// e.preventDefault()

			// This alert is temporary - you should replace it with whatever you want
			// to do if the captcha has not been filled out.
			alert('ERROR: Please verify you are human by filling out the captcha')

			return false
		}
		captcha_expired()
		return true
	}
	
	$( document ).ready(function() {
		
		window.addEventListener('load', () => {
		  const $recaptcha = document.querySelector('#g-recaptcha-response');
		  if ($recaptcha) {
			$recaptcha.setAttribute('required', 'required');
			$recaptcha.setAttribute('placeholder', 'Capcha Salah atau tidak sesuai');
		  }
		})

		window.setTimeout( function() {
		  window.location.reload();
		}, 500000);
		
		
		var forminput = document.getElementById('form<?php echo $iddata; ?>');
const submitButton = document.getElementById('kt_docs_formvalidation_text_submit');

submitButton.addEventListener('click', function (e) {

    Swal.fire({
        icon: "info",
        title: "Login <?php echo title; ?>",
        html: 'Apakah anda yakin akan login ke aplikasi ? <p></p><span style="color:red">Pastikan semua data login anda tidak diberikan ke orang lain, jaga akses anda. Terima kasih</span>',
        showDenyButton: false,
        showCancelButton: true,
        confirmButtonText: "Iya, Saya Setuju",
        cancelButtonText: "Tidak, Batalkan",
        cancelButtonColor: "#ff0000",
    }).then((result) => {
        if (result.isConfirmed) {

            // Prevent default button action
            e.preventDefault();
            var requiredattr = 0;
            var requiredattrdata = [];
            var datanya;
            for (var i = 0; i < forminput.elements.length; i++) {
                if (forminput.elements[i].value === '' && forminput.elements[i].hasAttribute('required')) {
                    datanya = forminput.elements[i].attributes['placeholder'].nodeValue;
                    datanya = datanya.replaceAll(/<\/[^>]+(>|$)/g, "");
                    requiredattrdata.push(stripHtml(datanya) + '<br>');
                    requiredattr = 1;
                }
            }

            if (requiredattr == 0) {
                // Disable tombol dan ubah teks menjadi loading
                submitButton.disabled = true;
                let originalText = submitButton.innerHTML;
                submitButton.innerHTML = 'Loading...';

                $.post('<?php echo $action; ?>', $('#form<?php echo $iddata; ?>').serialize(), function (data) {
                    console.log(data.status);
                    updateCsrfToken(data.csrf_hash);

                    // Kembalikan tombol ke keadaan semula
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalText;

                    if (data.status == "success") {

                        if (data.message == 'success') {

                            let timerInterval;
                            Swal.fire({
                                title: "Login",
                                html: "Login Berhasil. Menunggu Mengarahkan ...",
                                timer: 2000,
                                timerProgressBar: true,
                                didOpen: () => {
                                    Swal.showLoading();
                                    const timer = Swal.getPopup().querySelector("b");
                                    timerInterval = setInterval(() => {
                                        timer.textContent = `${Swal.getTimerLeft()}`;
                                    }, 100);
                                },
                                willClose: () => {
                                    clearInterval(timerInterval);
                                }
                            }).then((result) => {
                                if (result.dismiss === Swal.DismissReason.timer) {
                                    window.location.href = '<?php echo base_url('dashboard'); ?>';
                                }
                            });

                        } else if (data.message == 'firstblood') {
                            let timerInterval;
                            Swal.fire({
                                title: "Login",
                                html: "Login Berhasil. Menunggu Mengarahkan ...",
                                timer: 2000,
                                timerProgressBar: true,
                                didOpen: () => {
                                    Swal.showLoading();
                                    const timer = Swal.getPopup().querySelector("b");
                                    timerInterval = setInterval(() => {
                                        timer.textContent = `${Swal.getTimerLeft()}`;
                                    }, 100);
                                },
                                willClose: () => {
                                    clearInterval(timerInterval);
                                }
                            }).then((result) => {
                                if (result.dismiss === Swal.DismissReason.timer) {
                                    window.location.href = '<?php echo base_url('users_password?message=change'); ?>';
                                }
                            });
                        } else {
                            let timerInterval;
                            Swal.fire({
                                title: "Login",
                                html: "Login Berhasil. Menunggu Mengarahkan ...",
                                timer: 2000,
                                timerProgressBar: true,
                                didOpen: () => {
                                    Swal.showLoading();
                                    const timer = Swal.getPopup().querySelector("b");
                                    timerInterval = setInterval(() => {
                                        timer.textContent = `${Swal.getTimerLeft()}`;
                                    }, 100);
                                },
                                willClose: () => {
                                    clearInterval(timerInterval);
                                }
                            }).then((result) => {
                                if (result.dismiss === Swal.DismissReason.timer) {
                                    window.location.href = '<?php echo base_url('dashboard'); ?>';
                                }
                            });
                        }

                    } else {

                        Swal.fire({
                            text: "Login Gagal",
                            html: data.errors,
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "Tutup",
                            customClass: {
                                confirmButton: "btn btn-primary"
                            }
                        });

                        grecaptcha.reset();

                    }
                }, 'json')
                    .fail(function (jqxhr, status, error) {
                        console.error("Request failed: " + error);

                        // Kembalikan tombol ke keadaan semula
                        submitButton.disabled = false;
                        submitButton.innerHTML = originalText;

                        if (jqxhr.status === 403) {
                            $.get('<?php echo base_url('request_csrf_token'); ?>', function (data) {
                                csrfHash = data.csrf_hash;
                                updateCsrfToken(csrfHash);
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
                                $('.swal2-container').css('z-index', 99999);
                            }
                        });

                    });
            } else {

                datanya = requiredattrdata.toString().replaceAll(",", "");
                Swal.fire({
                    html: "Masih ada data belum terisi:<br>" + datanya,
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: "Lanjutkan Pengisian",
                    customClass: {
                        confirmButton: "btn btn-primary"
                    }
                });

            }

        } else if (result.isDenied) {
            Swal.fire("Changes are not saved", "", "info");
        }
    });

});

		$('#wrapper').height($(window).height());
		$('.card').height($(window).height() - 20);
		$('.card-body').height($(window).height() - 20);

		$(".toggle-password").click(function() {

		  $(this).toggleClass("fa-eye fa-eye-slash");
		  var input = $('#password');
		  if (input.attr("type") == "password") {
			input.attr("type", "text");
		  } else {
			input.attr("type", "password");
		  }
		});
		
	});

	function popuppem(){
		Swal.fire({
		  title: 'Pemberitahuan !',
		  text: 'Untuk mendapatkan username dan password atau mereset password dapat melalui administrator - INAMS',
		  imageUrl: '<?php echo base_url('logo.jpg'); ?>',
		  imageWidth: 400,
		  imageHeight: 200,
		  imageAlt: 'INAMS - BTPNS',
		})
	}
		
	
	
	 $(function() {
		 
      $(".preloader").fadeOut();
        });
        $(function() {
            $('[data-toggle="tooltip"]').tooltip()
        });
        // ============================================================== 
        // Login and Recover Password 
        // ============================================================== 
        $('#to-recover').on("click", function() {
            $("#loginform").slideUp();
            $("#recoverform").fadeIn();
        });
		
         "use strict";
         $('input[type="checkbox"]').on('change', function(){
                $(this).parent().toggleClass("active")
                $(this).closest(".media").toggleClass("active");
            }); 
        $(window).on("load", function(){
            /* loading screen */
            $(".loader_wrapper").fadeOut("slow");
        });
		
		<?php 
		
		if(isset($_GET['message'])){
			if($_GET['message'] == 'error'){
		?>
		
		$( document ).ready(function() {
							Swal.fire({
									title: "Login Gagal",
									text: 'Username atau password Salah',
									icon: "error",
									buttonsStyling: false,
									confirmButtonText: "Tutup",
									customClass: {
										confirmButton: "btn btn-primary"
									}
							});
		})
						
		<?php
			}elseif($_GET['message'] == 'errordata'){
		?>

			$( document ).ready(function() {
							Swal.fire({
									title: "Login Gagal",
									text: 'Login error, google account tidak terdaftar',
									icon: "error",
									buttonsStyling: false,
									confirmButtonText: "Tutup",
									customClass: {
										confirmButton: "btn btn-primary"
									}
							});
			})
						
		<?php
			}elseif($_GET['message'] == 'banned'){
		?>

			$( document ).ready(function() {
							Swal.fire({
									title: "Login Gagal",
									text: 'Username/Email anda di blokir oleh sistem, silahkan hubungin administrator',
									icon: "error",
									buttonsStyling: false,
									confirmButtonText: "Tutup",
									customClass: {
										confirmButton: "btn btn-primary"
									}
							});
			})
			
		<?php
			}elseif($_GET['message'] == 'error_capcha'){
		?>
			$( document ).ready(function() {
							Swal.fire({
									title: "Login Gagal",
									text: 'Capcha Salah atau tidak sesuai',
									icon: "error",
									buttonsStyling: false,
									confirmButtonText: "Tutup",
									customClass: {
										confirmButton: "btn btn-primary"
									}
							});
			}) 
		<?php
			}elseif($_GET['message'] == 'createerrors'){
		?>

			$( document ).ready(function() {
							Swal.fire({
									title: "Login Gagal",
									text: 'User tidak berhasil di buat',
									icon: "error",
									buttonsStyling: false,
									confirmButtonText: "Tutup",
									customClass: {
										confirmButton: "btn btn-primary"
									}
							});
			}) 
			
		<?php
			}elseif($_GET['message'] == 'validate'){
		?>
			$( document ).ready(function() {
							Swal.fire({
									title: "Login Gagal",
									text: 'Username/Email belum di aktivasi, silahkan lakukan aktivasi terlebih dahulu',
									icon: "error",
									buttonsStyling: false,
									confirmButtonText: "Tutup",
									customClass: {
										confirmButton: "btn btn-primary"
									}
							});
			}) 
			
		<?php
			}elseif($_GET['message'] == 'validate_admin'){
		?>
			$( document ).ready(function() {
							Swal.fire({
									title: "Login Gagal",
									text: 'Username/Email belum di aktivasi oleh admin, silahkan menunggu atau menghubungi admin telebih dahulu',
									icon: "error",
									buttonsStyling: false,
									confirmButtonText: "Tutup",
									customClass: {
										confirmButton: "btn btn-primary"
									}
							});
			}) 
			
		<?php
			}elseif($_GET['message'] == 'success' || $_GET['message'] == 'googlelogin'){
		?>
		
				$( document ).ready(function() {
			let timerInterval;
								Swal.fire({
								  title: "Login",
								  html: "Login Berhasil. Menunggu Mengarahkan ...",
								  timer: 2000,
								  timerProgressBar: true,
								  didOpen: () => {
									Swal.showLoading();
									const timer = Swal.getPopup().querySelector("b");
									timerInterval = setInterval(() => {
									  timer.textContent = `${Swal.getTimerLeft()}`;
									}, 100);
								  },
								  willClose: () => {
									clearInterval(timerInterval);
								  }
								}).then((result) => {
								  /* Read more about handling dismissals below */
								  if (result.dismiss === Swal.DismissReason.timer) {
									window.location.href = '<?php echo base_url('dashboard'); ?>';
								  }
								});
			}) 
			
		<?php
			}
		}
	?>


	function onClick(e) {
	    e.preventDefault();
	    grecaptcha.enterprise.ready(async () => {
	      const token = await grecaptcha.enterprise.execute('6LdxzEgqAAAAAOEYybsSwjwB0Sq0z1UrDbiVzZTD', {action: 'LOGIN'});
	    });
	  }
	
					
    </script>
	