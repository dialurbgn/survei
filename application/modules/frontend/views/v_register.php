
<?php
	
	$exclude = $exclude;
	$query_column = $this->ortyd->getviewlistform($module, $exclude, 2);
	if($query_column){
		foreach($query_column as $rows_column){
			if($rows_column['name'] == 'active'){
				${$rows_column['name']} = 1;
			}else{
				${$rows_column['name']} = null;
			}
		}
		$tanggal = date('Y-m-d');
		if(isset($id)){
			if($id == '0'){
				$id = '';
				$iddata = 0;
				$typedata = 'Buat';
			}else{
				$id = $id;
				$iddata = $id;
				$typedata = 'Edit';
				if($datarow && $datarow != null){
					foreach($query_column as $rows_column){
						foreach ($datarow as $rows) {
							${$rows_column['name']} = $rows->{$rows_column['name']};
						}
					}
				}
			}
		}else{
			$id = '';
			$iddata = 0;
			$typedata = 'Buat';
		}
	}else{
		$newURL = base_url($module);
		header('Location: '.$newURL);
	}
	
	$password = null;
?>

<form id="form<?php echo $iddata; ?>"  method="POST" action="<?php echo $action; ?>" enctype="multipart/form-data" style="margin:20px">
	<div id="kt_content_container" class="d-flex flex-column-fluid align-items-start container-xxl">
		<div class="content flex-row-fluid" id="kt_content">
			<br>
			<div class="row gx-6 gx-xl-9">
				<div class="col-lg-6">
					<div class="" id="owl-slider-home-register">
							
							<?php
							$this->db->select('data_popup.*,data_gallery.path');
							$this->db->where('data_popup.active',1);
							$this->db->join('data_gallery','data_gallery.id = data_popup.cover','left');
							$this->db->order_by('rand()','ASC');
							$this->db->limit(1);
							$query = $this->db->get('data_popup');
							$query = $query->result_array();
							if(count($query) > 0){
								foreach($query as $rows){
							?>
								<div class="item" >
									<img src="<?php echo base_url().$rows['path']; ?>" alt="<?php echo $rows['title']; ?>" style="width:100%;max-width: 600px;">
								</div>
							
								<?php }
							}
							?>
								
								
							</div>
				</div>
				<div class="col-lg-6">
					<!--begin::Summary-->
					<div class="card card-custom gutter-b example example-compact">
						<div class="card-body">
							<div class="form-group">
								<h2 class="label" style="font-size: 20px !important;font-weight: 400;border-bottom: 1px solid #206a8b;">
									Register akun anda sekarang 
								</h2>
								
								<!--begin::Login options-->
								<div class="row g-3 mb-9">
									<!--begin::Col-->
									<div class="col-md-12">
									
									<a href="<?php echo base_url('login'); ?>" class="text-dark fs-15 text-decoration-none">Anda sudah punya akun ? silahkan login disini </a>
										<!--begin::Google link=-->
										<a style="    border: 1px solid #ddd;
    margin-top: 20px;
    margin-bottom: 20px;" href="<?php echo $googlelink; ?>" class="btn btn-flex btn-outline btn-text-gray-700 btn-active-color-primary bg-state-light flex-center text-nowrap w-100">
										<img alt="Logo" src="<?php echo base_url(); ?>themes/ortyd/assets/media/svg/brand-logos/google-icon.svg" class="h-15px me-3" />Register with Google</a>
										<!--end::Google link=-->
									</div>
									<!--end::Col-->
								</div>
								
								<!--begin::Separator-->
								<div class="separator separator-content my-14 text-center">
									<span class="w-125px text-gray-500 fw-semibold fs-7">Or with form</span>
								</div>
								<!--end::Separator-->
								
							</div>
							
								<div class="row" id="dragList">	
							<?php
								if($query_column){
									$indentitas = 0;
									foreach($query_column as $rows_column){ 
									
										$disable = '';
										
										$width_column = $this->ortyd->width_column($module,$rows_column['name']);
										$tipe_data = $this->ortyd->getTipeData($module,$rows_column['name']);
										$label_name = $this->ortyd->translate_column($module,$rows_column['name']);
										$label_name_text = $label_name;
										if($rows_column['name']){
											$table_change = "'".$module."'";
											$table_change_id = "'".$rows_column['name']."'";
											$label_name_text_data = "'".$label_name_text."'";
											$editheader = ' <span style="cursor:pointer" onClick="changeTitle('.$table_change.','.$table_change_id.','.$label_name_text_data.')"><i class="fa fa-edit"></i></span>';
											if($this->ortyd->getAksesEditNaming() == true){
												$label_name = $label_name.$editheader;
											}else{
												$label_name = $label_name;
											}
										}
										if($rows_column['is_nullable'] == 'NO'){
											$label_name = $label_name.' *';
										}else{
											$this->db->where('master_lop_field_required.field',$rows_column['name']);
											$this->db->where('master_lop_field_required_tipe.module', $module);
											$this->db->where('master_lop_field_required.active', 1);
											$this->db->join('master_lop_field_required_tipe','master_lop_field_required.tipe_id = master_lop_field_required_tipe.id');
											$querytab = $this->db->get('master_lop_field_required');
											$querytab = $querytab->result_object();
											if($querytab){
												$label_name = $label_name.' <span style="color: red;">*</span>';
											}
										} 
										
										?>
										
										
										
										
										<?php
										
										if($tipe_data == 'TEXTAREA'){ ?>
										
											
											<?php if($rows_column['name'] == 'id'){ ?>
												
												
												
											<?php }else{ ?>
											
												<div id="<?php echo $rows_column['name'].'_header'; ?>" draggable="true" class="drag-item col-lg-<?php echo $width_column; ?>">
													<div class="form-group">
														<div class="row">
															<div class="col-lg-3">
																<label><?php echo $label_name; ?></label>
															</div>
															<div class="col-lg-9">
																<textarea id="<?php echo $rows_column['name']; ?>" rows="3" name="<?php echo $rows_column['name']; ?>" class="form-control" <?php if($rows_column['is_nullable'] == 'NO'){echo ' required';} ?> <?php echo $disable; ?> placeholder="<?php echo 'Input '.$label_name_text; ?>"><?php echo ${$rows_column['name']}; ?></textarea>
															</div>
														</div>
													</div>
												</div>
											
											<?php } ?>
									
									
									<?php }elseif($tipe_data == 'PASSWORD' || $rows_column['name'] == 'password'){ ?>
										
											<div id="<?php echo $rows_column['name'].'_header'; ?>" draggable="true" class="drag-item col-lg-<?php echo $width_column; ?>">
												<div class="form-group">
													<div class="row">
														<div class="col-lg-12">
															<label class="label"><?php echo $label_name; ?></label>
														</div>
														<div class="col-lg-12">
															<input id="<?php echo $rows_column['name']; ?>" type="password" name="<?php echo $rows_column['name']; ?>" class="form-control" value="<?php echo ${$rows_column['name']}; ?>"<?php if($rows_column['is_nullable'] == 'NO'){echo ' required ';} ?> <?php echo $disable; ?> placeholder="<?php echo 'Input '.$label_name_text; ?>"/> 
														</div>
													</div>
												</div>
											</div>
											
											<div id="<?php echo $rows_column['name'].'_header_duplicate'; ?>" draggable="true" class="drag-item col-lg-<?php echo $width_column; ?>">
												<div class="form-group">
													<div class="row">
														<div class="col-lg-12">
															<label class="label"><?php echo 'Password Konfirmasi *'; ?></label>
														</div>
														<div class="col-lg-12">
															<input id="<?php echo $rows_column['name'].'_duplicate'; ?>" type="password" class="form-control" value="<?php echo ${$rows_column['name']}; ?>" required <?php echo $disable; ?> placeholder="<?php echo 'Input '.'Password Konfirmasi'; ?>"/> 
														</div>
													</div>
												</div>
											</div>
											
									<?php }elseif($tipe_data == 'DATE' || $rows_column['name'] == 'date' || $rows_column['name'] == 'tanggal'){ ?>
										
											<div id="<?php echo $rows_column['name'].'_header'; ?>" draggable="true" class="drag-item col-lg-<?php echo $width_column; ?>">
												<div class="form-group">
													<div class="row">
														<div class="col-lg-3">
															<label style=""><?php echo $label_name; ?></label>
														</div>
														<div class="col-lg-9">
															<input id="<?php echo $rows_column['name']; ?>" type="text" name="<?php echo $rows_column['name']; ?>" class="form-control datetime" value="<?php echo ${$rows_column['name']}; ?>"<?php if($rows_column['is_nullable'] == 'NO'){echo ' required ';} ?> <?php echo $disable; ?> readonly='true' placeholder="<?php echo 'Input '.$label_name_text; ?>"/> 
														</div>
													</div>
												</div>
											</div>
											
									<?php }elseif($tipe_data == 'DATETIME'){ ?>
										
											<div id="<?php echo $rows_column['name'].'_header'; ?>" draggable="true" class="drag-item col-lg-<?php echo $width_column; ?>">
												<div class="form-group">
													<div class="row">
														<div class="col-lg-3">
															<label><?php echo $label_name; ?></label>
														</div>
														<div class="col-lg-9">
															<input id="<?php echo $rows_column['name']; ?>" type="text" name="<?php echo $rows_column['name']; ?>" class="form-control datepickertime" value="<?php echo ${$rows_column['name']}; ?>"<?php if($rows_column['is_nullable'] == 'NO'){echo ' required ';} ?> <?php echo $disable; ?> readonly='true' placeholder="<?php echo 'Input '.$label_name_text; ?>"/> 
														</div>
													</div>
												</div>
											</div>
										
									<?php }elseif($rows_column['name'] == 'email' || $rows_column['name'] == 'perusahaan_email'){ ?>
										
											<div id="<?php echo $rows_column['name'].'_header'; ?>" draggable="true" class="drag-item col-lg-<?php echo $width_column; ?> ">
												<div class="form-group">
													<div class="row">
														<div class="col-lg-20">
															<label class="label"><?php echo $label_name; ?></label>
														</div>
														<div class="col-lg-20">
															<input id="<?php echo $rows_column['name']; ?>" type="email" name="<?php echo $rows_column['name']; ?>" class="form-control" value="<?php echo ${$rows_column['name']}; ?>"<?php if($rows_column['is_nullable'] == 'NO'){echo ' required ';} ?> <?php echo $disable; ?> placeholder="<?php echo 'Input '.$label_name_text; ?>" /> 
														</div>
													</div>
												</div>
											</div>
										
									<?php }elseif($tipe_data == 'NUMBER' || $rows_column['name'] == 'nomor' || $rows_column['name'] == 'perusahaan_hp'){ ?>
										
											<div id="<?php echo $rows_column['name'].'_header'; ?>" draggable="true" class="drag-item col-lg-<?php echo $width_column; ?>">
												<div class="form-group">
													<div class="row">
														<div class="col-lg-3">
															<label><?php echo $label_name; ?></label>
														</div>
														<div class="col-lg-9">
															<input id="<?php echo $rows_column['name']; ?>" type="number" name="<?php echo $rows_column['name']; ?>" class="form-control" value="<?php echo ${$rows_column['name']}; ?>"<?php if($rows_column['is_nullable'] == 'NO'){echo ' required ';} ?> <?php echo $disable; ?> placeholder="<?php echo 'Input '.$label_name_text; ?>" />
														</div>
													</div>
												</div>
											</div>
											
									<?php }elseif($tipe_data == 'CURRENCY' || $rows_column['name'] == 'nilai'){ ?>
										
											<div id="<?php echo $rows_column['name'].'_header'; ?>" draggable="true" class="drag-item col-lg-<?php echo $width_column; ?>">
												<div class="form-group">
													<div class="row">
														<div class="col-lg-3">
															<label><?php echo $label_name; ?></label>
														</div>
														<div class="col-lg-9">
															<input id="<?php echo $rows_column['name']; ?>" type="text" name="<?php echo $rows_column['name']; ?>" class="form-control numeric-rp" value="<?php echo ${$rows_column['name']}; ?>"<?php if($rows_column['is_nullable'] == 'NO'){echo ' required ';} ?> <?php echo $disable; ?> placeholder="<?php echo 'Input '.$label_name_text; ?>" />
														</div>
													</div>
												</div>
											</div>
										
									<?php }else{ ?>
									
									
											<?php if($tipe_data == 'FILE' || $rows_column['name'] == 'file_id'){ ?>
										
												<?php 
													include(APPPATH."views/common/uploadformside.php");
												?>
										
											<?php }elseif($tipe_data == 'SELECT'){ ?>
												
											<?php 
												include(APPPATH."views/common/select2formsidefrontend.php");
											?>
											
											<?php }elseif($rows_column['name'] == 'urutan'){ ?>
					
												<div id="<?php echo $rows_column['name'].'_header'; ?>" draggable="true" class="drag-item col-lg-<?php echo $width_column; ?> py-3">
													<div class="form-group">
														<div class="row">
															<div class="col-lg-3">
																<label><?php echo $label_name; ?></label>
															</div>
															<div class="col-lg-9">
																<input type="number" id="<?php echo $rows_column['name']; ?>" name="<?php echo $rows_column['name']; ?>" class="form-control" value="<?php echo ${$rows_column['name']}; ?>" <?php if($rows_column['is_nullable'] == 'NO'){echo ' required';} ?> <?php echo $disable; ?> placeholder="<?php echo 'Input '.$label_name_text; ?>" min="1" /> 
															</div>
														</div>
													</div>
												</div>
											
											
											<?php }else{ ?>
										
												<div id="<?php echo $rows_column['name'].'_header'; ?>" draggable="true" class="drag-item col-lg-<?php echo $width_column; ?>">
													<div class="form-group">
														<div class="row">
															<div class="col-lg-12">
																<label class="label"><?php echo $label_name; ?></label>
															</div>
															<div class="col-lg-12">
																<input type="text" name="<?php echo $rows_column['name']; ?>" id="<?php echo $rows_column['name']; ?>" class="form-control" value="<?php echo ${$rows_column['name']}; ?>" <?php if($rows_column['is_nullable'] == 'NO'){echo ' required';} ?> <?php echo $disable; ?> placeholder="<?php echo 'Input '.$label_name_text; ?>" /> 
															</div>
														</div>
													</div>
												</div>
											
											<?php } ?>

									<?php }
										$indentitas++;
									}
								} ?>

								<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" class="csrf_token" placeholder="CSRF Invalid" required />
								
								<div class="fv-row mb-3">
									<div class="g-recaptcha" data-sitekey="<?php echo $site_key; ?>"></div>
									<span class="text-danger"><?php echo form_error('g-recaptcha-response'); ?></span>
								</div>
								
								<div class="col-lg-12 py-3">
								
									<div class="row text-center">
										<div class="col-lg-12" style="padding:0;padding-right: 5px;">
											<button class="btn btn-info" type="button" id="kt_docs_formvalidation_text_submit">Register Akun Anda</button>
											 <a class="btn btn-danger" href="<?php echo base_url(); ?>" ><i class="fa fa-undo"></i> Kembali Ke Beranda</a>
											<!-- <button style="margin-left:10px" type="button" id="kt_docs_formvalidation_text_submit" class="btn btn-primary pull-right">
												<i class="fa fa-save"></i> Register
											</button> -->
										</div>
									</div>

								</div>
									
								</div>	
							
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</form>
<script>
	
	$( document ).ready(function() {
		
		// Submit button handler
		var forminput = document.getElementById('form<?php echo $iddata; ?>');
		const submitButton = document.getElementById('kt_docs_formvalidation_text_submit');
		submitButton.addEventListener('click', function (e) {
			
			Swal.fire({
			  icon: "info",
			  title: "Regitrasi Akun",
			  html: 'Apakah anda yakin dan setuju akan membuat akun pada website '.title.' ini ? <p></p><span style="color:red">Pastikan semua data yang ada input sesuai dengan data yang sebenarnya.</span>',
			  showDenyButton: false,
			  showCancelButton: true,
			  confirmButtonText: "Iya, Saya Setuju",
			  cancelButtonText: "Tidak, sesuaikan inputan",
			  cancelButtonColor: "#ff0000",
			  //denyButtonText: `Don't save`
			}).then((result) => {
			  /* Read more about isConfirmed, isDenied below */
			  if (result.isConfirmed) {
				
				loadingopen()
			
				// Prevent default button action
				e.preventDefault();
				var requiredattr = 0;
				var requiredattrdata = [];
				var datanya;
				for(var i=0; i < forminput.elements.length; i++){
					if(forminput.elements[i].value === '' && forminput.elements[i].hasAttribute('required')){
						
							console.log(forminput.elements[i].attributes)
							datanya = forminput.elements[i].attributes['placeholder'].nodeValue;
							datanya = datanya.replaceAll(/<\/[^>]+(>|$)/g, "");
							requiredattrdata.push(stripHtml(datanya) + '<br>')
							requiredattr = 1;
						
					}
				}
				
					yy = $('#password_duplicate').val();
					y = $('#password').val();
					console.log(y.search(/[a-z]/))
					<?php if($typedata != 'Edit'){ ?>
					
						if (y != yy) {
						  message = "password dan Password Konfirmasi tidak sama";
						  datanya = message;
						  datanya = datanya.replaceAll(/<\/[^>]+(>|$)/g, "");
						  requiredattrdata.push(stripHtml(datanya) + '<br>')
						  requiredattr = 1;
						}
						if (y.length < 8) {
						  message = "Password Harus minimal 8 Karakter ";
						  datanya = message;
						  datanya = datanya.replaceAll(/<\/[^>]+(>|$)/g, "");
						  requiredattrdata.push(stripHtml(datanya) + '<br>')
						  requiredattr = 1;
						}
						if (y.search(/[a-z]/) == -1) {
						  message = "Password Membutuhkan minimal 1 Hurup Kecil";
						  datanya = message;
						  datanya = datanya.replaceAll(/<\/[^>]+(>|$)/g, "");
						  requiredattrdata.push(stripHtml(datanya) + '<br>')
						  requiredattr = 1;
						}
						if (y.search(/[A-Z]/) == -1) {
						  message = "Password Membutuhkan minimal 1 Hurup Kapital";
						  datanya = message;
						  datanya = datanya.replaceAll(/<\/[^>]+(>|$)/g, "");
						  requiredattrdata.push(stripHtml(datanya) + '<br>')
						  requiredattr = 1;
						}
						if (y.search (/[0-9]/) == -1) {
						  message = "Password Membutuhkan minimal 1 Angka";
						  datanya = message;
						  datanya = datanya.replaceAll(/<\/[^>]+(>|$)/g, "");
						  requiredattrdata.push(stripHtml(datanya) + '<br>')
						  requiredattr = 1;
						}

					<?php } ?>
				
				
				 // Validasi nomor telepon
				var phone = $('#notelp').val();
				var phoneRegex = /^[0-9]{10,12}$/; // Validasi 10-12 digit angka
				if (!phoneRegex.test(phone)) {
					message = "Nomor telepon tidak valid. Harus 10-12 digit angka.";
					requiredattrdata.push(stripHtml(message) + '<br>');
					requiredattr = 1;
				}
		
				// CEK CAPTCHA
				var recaptchaResponse = grecaptcha.getResponse();
				if (recaptchaResponse.length == 0) {
					message = "Silakan verifikasi captcha terlebih dahulu.";
					requiredattrdata.push(stripHtml(message) + '<br>');
					requiredattr = 1;
				}

				// VALIDASI EMAIL
				var emailInput = document.getElementById('email');
				if (emailInput) {
					console.log(emailInput)
					var email = emailInput.value.trim();
					var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

					if (email !== '' && !emailPattern.test(email)) {
						message = "Format Email tidak valid.";
						requiredattrdata.push(stripHtml(message) + '<br>');
						requiredattr = 1;
					}
				} else {
					console.warn("Elemen email tidak ditemukan!");
				}

				if(requiredattr == 0){
					$.post('<?php echo $action; ?>', $('#form<?php echo $iddata; ?>').serialize(),function (data) {
					console.log(data)
					updateCsrfToken(data.csrf_hash)
						if(data.status == "success"){
							loadingclose()
							Swal.fire({
								text: "Register berhasil, Silahkan lakukan verifikasi akun pada email anda !",
								icon: "success",
								buttonsStyling: false,
								confirmButtonText: "Ok, got it!",
								customClass: {
									confirmButton: "btn btn-primary"
								}
							}).then((result) => {
						  /* Read more about isConfirmed, isDenied below */
								if (result.isConfirmed) {
									setTimeout(() => {
										
										window.location.href = '<?php echo base_url($headurl); ?>'; //Will take you to Google.
									}, 1000);
								}
							})
										
							
							
						
							
						}else{
							
							Swal.fire({
								text: "Register tidak berhasil!, "+ data.error,
								icon: "error",
								buttonsStyling: false,
								confirmButtonText: "Tutup",
								customClass: {
									confirmButton: "btn btn-primary"
								}
							});

							loadingclose()
							
						}
					}, 'json')
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

								
								});
								
				}else{
					
					console.log(requiredattrdata.toString())
					datanya = requiredattrdata.toString().replaceAll(",","");
					Swal.fire({
						html: "Masih ada data belum terisi:<br>" +datanya,
						icon: "error",
						buttonsStyling: false,
						confirmButtonText: "Lanjutkan Pengisian",
						customClass: {
							confirmButton: "btn btn-primary"
						}
					});
					
					loadingclose()
				}
			
			  } else if (result.isDenied) {
				Swal.fire("Changes are not saved", "", "info");
			  }
			});
			
			
			
			
		})

	})
	
	$( document ).ready(function() {
		// Add event listeners for drag and drop events
		<?php if($this->ortyd->getaccessdrag() == true){ ?>
		dragList.addEventListener('dragstart', handleDragStart);
		dragList.addEventListener('dragover', handleDragOver);
		dragList.addEventListener('drop', handleDrop);
	<?php } ?>

					
	});
	
</script>

