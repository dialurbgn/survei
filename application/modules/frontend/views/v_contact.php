<section class="section border-0 lazyload my-0" data-bg-src="" style="background-position: 50% 100%; ">
					<div class="container">
						<div class="row justify-content-md-end py-3">
						
						<div class="col-md-6">
		<div class="container">
					<div class="row">
						<div class="col">
							<div class="row py-2">
							  
								
								<div class="col-md-12">
									<p class="mb-0"><strong class="text-dark text-4">Alamat</strong></p>
									<p class="mb-0"><a href="<?php echo base_url('kontak').'#mapid'; ?>" class=""><?php echo $this->ortyd->getMeta('company_address'); ?></a></p>
								</div>
								<div class="col-md-12">
									<p class="mb-0"><strong class="text-dark text-4">Jam Kerja</strong></p>
									<p class="mb-0"><?php echo $this->ortyd->getMeta('company_bussines_hour'); ?></p>
								</div>
								<div class="col-md-12">
									<p class="mb-0"><strong class="text-dark text-4">Telepon</strong></p>
									<p class="mb-3"><a href="tel:<?php echo $this->ortyd->getMeta('company_notelp'); ?>" class=""><?php echo $this->ortyd->getMeta('company_notelp'); ?></a></p>
									
								</div>
								<div class="col-md-12">
									<p class="mb-0"><strong class="text-dark text-4">Fax</strong></p>
									<p class="mb-3"><a href="tel:<?php echo $this->ortyd->getMeta('company_fax'); ?>" class=""><?php echo $this->ortyd->getMeta('company_fax'); ?></a></p>
									
								</div>
								<div class="col-md-12">
									<p class="mb-0"><strong class="text-dark text-4">Whatsapp</strong></p>
									<p class="mb-3"><a href="tel:<?php echo $this->ortyd->getMeta('company_whatsapp'); ?>" class=""><?php echo $this->ortyd->getMeta('company_whatsapp'); ?></a></p>
									
								</div>
								<div class="col-md-12">
									<p class="mb-0"><strong class="text-dark text-4">Email/Surel</strong></p>
									<p class="mb-0"><a href="mailto:<?php echo $this->ortyd->getMeta('company_email'); ?>"><?php echo $this->ortyd->getMeta('company_email'); ?></a></p>
								</div>

							</div>

						</div>
					</div>
				</div>
		</div>
		
							<div class="col-md-6">
								<div class="appear-animation" data-appear-animation="blurIn" data-appear-animation-delay="0">
									<h2 class="mb-0 font-weight-bold">Kirim Pesan</h2>
									<div class="divider divider-primary divider-small mt-2 mb-4">
										<hr class="my-0 me-auto">
									</div>
								</div>
								<div class="appear-animation" data-appear-animation="fadeIn" data-appear-animation-delay="400">
									<form class="contact-form form-style-2" id="formContact" action="#" method="POST">
										<div class="row pb-2 mb-1">
	
										  
											<div class="form-group col-lg-6">
												<input type="text" value="" placeholder="Nama Lengkap" data-msg-required="Silahkan Isi Nama Lengkap." maxlength="100" class="form-control text-3 h-auto py-2" name="name" required>
											</div>
											<div class="form-group col-lg-6">
												<input type="email" value="" placeholder="Alamat Surel" data-msg-required="Silahkan Isi Alamat Surel." data-msg-email="Silahkan Masukan alamat surel yang benar." maxlength="100" class="form-control text-3 h-auto py-2" name="email" required>
											</div>
										</div>
										
										<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
										
										<div class="row pb-2 mb-1">
											<div class="form-group col">
												<input type="text" value="" placeholder="Perihal" data-msg-required="Silahkan Isi Prihal." maxlength="100" class="form-control text-3 h-auto py-2" name="subject" required>
											</div>
										</div>
										<div class="row pb-2 mb-1">
											<div class="form-group col">
												<textarea maxlength="5000" placeholder="Pesan" data-msg-required="Silahkan Isi Pesan." rows="8" class="form-control text-3 h-auto py-2" name="message" required></textarea>
											</div>
										</div>
										
										<!-- <div class="row pb-2 mb-1">
											<div class="form-group col-lg-4">
												<?php //echo $capcha;?>
											</div>
											<div class="form-group col-lg-8">
												<input type="text" value="" placeholder="Input Capcha" class="form-control text-3 h-auto py-2" name="capcha" required>
											</div>
										</div> -->
										
										<div class="fv-row mb-3">
											<div class="g-recaptcha" data-sitekey="<?php echo $site_key; ?>"></div>
											<span class="text-danger"><?php echo form_error('g-recaptcha-response'); ?></span>
										</div>
										
										<div class="row">
											<div class="form-group col">
												<input type="submit" value="Kirimkan Pesan" class="btn btn-primary btn-modern text-uppercase font-weight-bold text-2 py-3 btn-px-4" data-loading-text="Loading..." onClick="saveMessage()">
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</section>
				
				
			<script>
			
			function saveMessage() {
				// Ambil nilai input email
				var email = $("input[name='email']").val();

				// Validasi format email menggunakan regex
				var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;

				// Cek apakah email valid
				if (!emailPattern.test(email)) {
					Swal.fire({
						title: '<strong>Oops...</strong>',
						icon: 'error',
						html: 'Silahkan masukkan alamat surel yang valid.'
					});
					return; // Hentikan fungsi jika email tidak valid
				}

				// Cek apakah reCAPTCHA terisi
				var recaptchaResponse = grecaptcha.getResponse();
				if (recaptchaResponse.length === 0) {
					// Jika kosong, tampilkan peringatan dan hentikan pengiriman formulir
					Swal.fire({
						title: '<strong>Oops...</strong>',
						icon: 'error',
						html: 'Silahkan centang kotak reCAPTCHA untuk melanjutkan.'
					});
					return; // Hentikan fungsi jika CAPTCHA tidak valid
				}

				// Ambil data dari form
				var datastring = $("#formContact").serialize();

				// Ambil tombol kirim
				var submitButton = $("#submitButton");

				// Nonaktifkan tombol kirim dan ubah teks tombol menjadi "Loading..."
				submitButton.prop('disabled', true).val('Loading...');

				// Kirim data form ke server menggunakan AJAX
				$.post('<?php echo base_url('frontend/saveMessage'); ?>', datastring, function (data) {
					if (data != 'null') {
						var obj = jQuery.parseJSON(data);
						updateCsrfToken(obj.csrf_hash); // Update CSRF token

						// Cek apakah pesan berhasil dikirim
						if (obj.message == 'success') {
							Swal.fire({
								title: '<strong>Pesan Terkirim</strong>',
								icon: 'info',
								html: 'Terima Kasih Telah Mengirim Pesan ke Kami'
							});

							// Redirect setelah beberapa detik
							setTimeout(function() {
								window.location.href = "<?php echo base_url(); ?>";
							}, 10000);
						} else {
							// Jika ada kesalahan
							Swal.fire({
								title: '<strong>Oops...</strong>',
								icon: 'error',
								html: 'Ada yang bermasalah!, ' + obj.errors
							});
						}
					}
				}).fail(function() {
					// Jika terjadi kesalahan pada request AJAX
					Swal.fire({
						title: '<strong>Oops...</strong>',
						icon: 'error',
						html: 'Terjadi kesalahan saat mengirim pesan. Silakan coba lagi.'
					});
				}).always(function() {
					// Aktifkan kembali tombol kirim dan kembalikan teks tombol
					submitButton.prop('disabled', false).val('Kirimkan Pesan');
				});
			}





			
			</script>
			
			<div class="">
	<div class="row" id="mapid">
		<div class="col-md-12">
			<?php echo $this->ortyd->getMeta('company_map'); ?>
		</div>
		

		


	</div>
</div>
