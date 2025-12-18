
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

<form id="form<?php echo $iddata; ?>"  method="POST" action="<?php echo $action; ?>" enctype="multipart/form-data">
<div id="kt_content_container" class="d-flex flex-column-fluid align-items-start container-xxl">
	<!--begin::Post-->
	<div class="content flex-row-fluid" id="kt_content">				
		<?php include(APPPATH."views/navbar_header_form.php"); ?>			
		<!--begin::Row-->
		<div class="row gx-6 gx-xl-9">
			<!--begin::Col-->
			<div class="col-lg-12">
				<!--begin::Summary-->
				<div class="card card-custom gutter-b example example-compact">
				<div class="card-body">
					
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
									$label_name = $label_name.' <span style="color: red;">*</span>';
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
									
										<div id="<?php echo $rows_column['name'].'_header'; ?>" draggable="true" class="drag-item col-lg-<?php echo $width_column; ?> py-3">
											<div class="form-group">
												<div class="row">
													<div class="col-lg-3">
														<label><?php echo $label_name; ?></label>
													</div>
													<div class="col-lg-9">
														<textarea rows="3" name="<?php echo $rows_column['name']; ?>" class="form-control form-control-sm" <?php if($rows_column['is_nullable'] == 'NO'){echo ' required';} ?> <?php echo $disable; ?> placeholder="<?php echo 'Input '.$label_name_text; ?>"><?php echo ${$rows_column['name']}; ?></textarea>
													</div>
												</div>
											</div>
										</div>
									
									<?php } ?>
							
							
							<?php }elseif($tipe_data == 'PASSWORD' || $rows_column['name'] == 'password'){ ?>
								
									<div id="<?php echo $rows_column['name'].'_header'; ?>" draggable="true" class="drag-item col-lg-<?php echo $width_column; ?> py-3">
										<div class="form-group">
											<div class="row">
												<div class="col-lg-3">
													<label><?php echo $label_name; ?></label>
												</div>
												<div class="col-lg-9">
													<input id="<?php echo $rows_column['name']; ?>" type="password" name="<?php echo $rows_column['name']; ?>" class="form-control form-control-sm" value="<?php echo ${$rows_column['name']}; ?>"<?php if($rows_column['is_nullable'] == 'NO'){echo ' required ';} ?> <?php echo $disable; ?> placeholder="<?php echo 'Input '.$label_name_text; ?>"/> 
												</div>
											</div>
										</div>
									</div>
									
							<?php }elseif($tipe_data == 'DATE' || $rows_column['name'] == 'date' || $rows_column['name'] == 'tanggal'){ ?>
								
									<div id="<?php echo $rows_column['name'].'_header'; ?>" draggable="true" class="drag-item col-lg-<?php echo $width_column; ?> py-3">
										<div class="form-group">
											<div class="row">
												<div class="col-lg-3">
													<label><?php echo $label_name; ?></label>
												</div>
												<div class="col-lg-9">
													<input id="<?php echo $rows_column['name']; ?>" type="text" name="<?php echo $rows_column['name']; ?>" class="form-control form-control-sm datetime" value="<?php echo ${$rows_column['name']}; ?>"<?php if($rows_column['is_nullable'] == 'NO'){echo ' required ';} ?> <?php echo $disable; ?> readonly='true' placeholder="<?php echo 'Input '.$label_name_text; ?>"/> 
												</div>
											</div>
										</div>
									</div>
									
							<?php }elseif($tipe_data == 'DATETIME'){ ?>
								
									<div id="<?php echo $rows_column['name'].'_header'; ?>" draggable="true" class="drag-item col-lg-<?php echo $width_column; ?> py-3">
										<div class="form-group">
											<div class="row">
												<div class="col-lg-3">
													<label><?php echo $label_name; ?></label>
												</div>
												<div class="col-lg-9">
													<input id="<?php echo $rows_column['name']; ?>" type="text" name="<?php echo $rows_column['name']; ?>" class="form-control form-control-sm datepickertime" value="<?php echo ${$rows_column['name']}; ?>"<?php if($rows_column['is_nullable'] == 'NO'){echo ' required ';} ?> <?php echo $disable; ?> readonly='true' placeholder="<?php echo 'Input '.$label_name_text; ?>"/> 
												</div>
											</div>
										</div>
									</div>
								
							<?php }elseif($rows_column['name'] == 'email' || $rows_column['name'] == 'perusahaan_email'){ ?>
								
									<div id="<?php echo $rows_column['name'].'_header'; ?>" draggable="true" class="drag-item col-lg-<?php echo $width_column; ?> py-3">
										<div class="form-group">
											<div class="row">
												<div class="col-lg-3">
													<label><?php echo $label_name; ?></label>
												</div>
												<div class="col-lg-9">
													<input id="<?php echo $rows_column['name']; ?>" type="email" name="<?php echo $rows_column['name']; ?>" class="form-control form-control-sm" value="<?php echo ${$rows_column['name']}; ?>"<?php if($rows_column['is_nullable'] == 'NO'){echo ' required ';} ?> <?php echo $disable; ?> placeholder="<?php echo 'Input '.$label_name_text; ?>" /> 
												</div>
											</div>
										</div>
									</div>
								
							<?php }elseif($tipe_data == 'NUMBER' || $rows_column['name'] == 'nomor' || $rows_column['name'] == 'perusahaan_hp'){ ?>
								
									<div id="<?php echo $rows_column['name'].'_header'; ?>" draggable="true" class="drag-item col-lg-<?php echo $width_column; ?> py-3">
										<div class="form-group">
											<div class="row">
												<div class="col-lg-3">
													<label><?php echo $label_name; ?></label>
												</div>
												<div class="col-lg-9">
													<input id="<?php echo $rows_column['name']; ?>" type="number" name="<?php echo $rows_column['name']; ?>" class="form-control form-control-sm" value="<?php echo ${$rows_column['name']}; ?>"<?php if($rows_column['is_nullable'] == 'NO'){echo ' required ';} ?> <?php echo $disable; ?> placeholder="<?php echo 'Input '.$label_name_text; ?>" />
												</div>
											</div>
										</div>
									</div>
									
							<?php }elseif($tipe_data == 'CURRENCY' || $rows_column['name'] == 'nilai'){ ?>
								
									<div id="<?php echo $rows_column['name'].'_header'; ?>" draggable="true" class="drag-item col-lg-<?php echo $width_column; ?> py-3">
										<div class="form-group">
											<div class="row">
												<div class="col-lg-3">
													<label><?php echo $label_name; ?></label>
												</div>
												<div class="col-lg-9">
													<input id="<?php echo $rows_column['name']; ?>" type="text" name="<?php echo $rows_column['name']; ?>" class="form-control form-control-sm numeric-rp" value="<?php echo ${$rows_column['name']}; ?>"<?php if($rows_column['is_nullable'] == 'NO'){echo ' required ';} ?> <?php echo $disable; ?> placeholder="<?php echo 'Input '.$label_name_text; ?>" />
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
										include(APPPATH."views/common/select2formside.php");
									?>
									
									<?php }elseif($rows_column['name'] == 'urutan'){ ?>
			
										<div id="<?php echo $rows_column['name'].'_header'; ?>" draggable="true" class="drag-item col-lg-<?php echo $width_column; ?> py-3">
											<div class="form-group">
												<div class="row">
													<div class="col-lg-3">
														<label><?php echo $label_name; ?></label>
													</div>
													<div class="col-lg-9">
														<input type="number" id="<?php echo $rows_column['name']; ?>" name="<?php echo $rows_column['name']; ?>" class="form-control form-control-sm" value="<?php echo ${$rows_column['name']}; ?>" <?php if($rows_column['is_nullable'] == 'NO'){echo ' required';} ?> <?php echo $disable; ?> placeholder="<?php echo 'Input '.$label_name_text; ?>" min="1" /> 
													</div>
												</div>
											</div>
										</div>
									
									
									<?php }else{ ?>
								
										<div id="<?php echo $rows_column['name'].'_header'; ?>" draggable="true" class="drag-item col-lg-<?php echo $width_column; ?> py-3">
											<div class="form-group">
												<div class="row">
													<div class="col-lg-3">
														<label><?php echo $label_name; ?></label>
													</div>
													<div class="col-lg-9">
														<input type="text" name="<?php echo $rows_column['name']; ?>" id="<?php echo $rows_column['name']; ?>" class="form-control form-control-sm" value="<?php echo ${$rows_column['name']}; ?>" <?php if($rows_column['is_nullable'] == 'NO'){echo ' required';} ?> <?php echo $disable; ?> placeholder="<?php echo 'Input '.$label_name_text; ?>" /> 
													</div>
												</div>
											</div>
										</div>
									
									<?php } ?>

							<?php }
								$indentitas++;
							}
						} ?>
						
						<div class="col-lg-12 py-3" style="">
							<div class="form-group">
								<div class="row">
									<div class="col-lg-12">
										<div id="role_table_0"></div>
									</div>
									<div class="col-lg-12">
										<button class="btn btn-sm btn-primary btn-aksi-data" onClick="addRowRole(<?php echo $iddata; ?>,1)" type="button">Tambah Role</button>
									</div>
								</div>
							</div>
						</div>

						<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" class="csrf_token" placeholder="CSRF Invalid" required />
						
						<div class="card-footer col-lg-12 py-3" id="btn-aksi-action">
						
							<div class="row">
								<div class="col-lg-12" style="padding:0;padding-right: 5px;">
									<button style="margin-left:10px" type="button" id="kt_docs_formvalidation_text_submit" class="btn btn-primary pull-right">
										<i class="fa fa-save"></i> Simpan
									</button>
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
			
			<?php 
					$this->db->where('master_lop_field_required_tipe.module', $module);
					$this->db->where('master_lop_field_required.active', 1);
					$this->db->join('master_lop_field_required_tipe','master_lop_field_required.tipe_id = master_lop_field_required_tipe.id');
					$querytab = $this->db->get('master_lop_field_required');
					$querytab = $querytab->result_object();
					if($querytab){
						foreach($querytab as $rowstab){
				?>
											
						$('#<?php echo $rowstab->field; ?>').prop('required',true);
											
				<?php
						}
					}
				
			?>
			
			Swal.fire({
			   icon: "info",
			  title: "Submit Data",
			  html: 'Apakah anda yakin akan menyimpan data ? <p></p><span style="color:red">Pastikan semua data yang ada sesuai.</span>',
			  showDenyButton: false,
			  showCancelButton: true,
			  confirmButtonText: "Iya, Saya Setuju",
			  cancelButtonText: "Tidak, Sesuaikan Inputan",
			  cancelButtonColor: "#ff0000",
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
						
						if(forminput.elements[i].attributes['name'].nodeValue == 'password'){
							<?php if($typedata != 'Edit'){ ?>
								console.log(forminput.elements[i].attributes)
								datanya = forminput.elements[i].attributes['placeholder'].nodeValue;
								datanya = datanya.replaceAll(/<\/[^>]+(>|$)/g, "");
								requiredattrdata.push(stripHtml(datanya) + '<br>')
								requiredattr = 1;
							<?php } ?>
						}else{
							console.log(forminput.elements[i].attributes)
							datanya = forminput.elements[i].attributes['placeholder'].nodeValue;
							datanya = datanya.replaceAll(/<\/[^>]+(>|$)/g, "");
							requiredattrdata.push(stripHtml(datanya) + '<br>')
							requiredattr = 1;
						}
						
					}
				}
				

					y = $('#password').val();
					console.log(y.search(/[a-z]/))
					<?php if($typedata != 'Edit'){ ?>
					
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
					
				// VALIDASI EMAIL
				var emailInput = document.getElementById('email');
				if (emailInput) {
					console.log(emailInput)
					var email = emailInput.value.trim();
					if(email != '' && email != '-'){
						
						var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

						if (email !== '' && !emailPattern.test(email)) {
							message = "Format Email tidak valid.";
							requiredattrdata.push(stripHtml(message) + '<br>');
							requiredattr = 1;
						}
					}
					
				} else {
					console.warn("Elemen email tidak ditemukan!");
				}

				 // Validasi nomor telepon
				var phone = $('#notelp').val();
				if (phone) {
					var phoneRegex = /^[0-9]{10,12}$/; // Validasi 10-12 digit angka
					if (!phoneRegex.test(phone)) {
						message = "Nomor telepon tidak valid. Harus 10-12 digit angka.";
						requiredattrdata.push(stripHtml(message) + '<br>');
						requiredattr = 1;
					}
				}else {
					console.warn("Elemen email tidak ditemukan!");
				}

				if(requiredattr == 0){
					$.post('<?php echo $action; ?>', $('#form<?php echo $iddata; ?>').serialize(),function (data) {
					console.log(data)
					updateCsrfToken(data.csrf_hash)
						if(data.status == "success"){
							
							Swal.fire({
								text: "Data berhasil disimpan!",
								icon: "success",
								buttonsStyling: false,
								confirmButtonText: "Ok, got it!",
								customClass: {
									confirmButton: "btn btn-primary"
								}
							});
							
							loadingclose()
							
							setTimeout(() => {
								window.location.href = '<?php echo base_url($headurl); ?>'; //Will take you to Google.
							}, 2000);
							
						}else{
							
							Swal.fire({
								text: "Data tidak berhasil disimpan!, "+ data.error,
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

									loadingclose();
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
		
		nomorrole[1] = 0;
		getRoleRequired(numpo, <?php echo $iddata ?>, <?php echo $iddata ?>);
		
		// Add event listeners for drag and drop events
		<?php if($this->ortyd->getaccessdrag() == true){ ?>
		dragList.addEventListener('dragstart', handleDragStart);
		dragList.addEventListener('dragover', handleDragOver);
		dragList.addEventListener('drop', handleDrop);
	<?php } ?>
	});
	
		
		
var numpo = 1;	
var table_rolepo = [];
var nomorrole = [];
function getRoleRequired(number, approval_id, approval_data_id){

	$.post('<?php echo base_url($headurl.'/getDataRoleRequired'); ?>',{
			user_id : approval_data_id,
			<?php echo $this->security->get_csrf_token_name(); ?> : csrfHash 
	},function (data) {	
			if(data != 'null'){
				$('#input-konfirmasi-nya').val('')
				var obj = jQuery.parseJSON(data);
				updateCsrfToken(obj.csrf_hash)
				var table_detail_role_body;
				if(obj.status == 'success'){
					var table_detail_role = 
					'<div class="div-detail-table"><span style="font-size: 16px;font-family: sora-font-regular;">Additional Role</span></div>'+
					'<div class="table-responship">'+
						'<table class="table table-striped align-middle table-row-dashed fs-6 gy-5" id="table_rolepo_tbl_'+number+'">' +
							'<thead>'+
								'<th width="250">Role</th>'+
								'<th width="20">Action</th>'+
							'</thead>'+
							'<tbody id="table_body_role">'+
								
							'</tbody>'+
						'</table>'+
					'</div>';
					

					$('#role_table_0').html(table_detail_role);
					$('#table_body_role').append(table_detail_role_body);
					table_rolepo[number] = $('#table_rolepo_tbl_'+number).DataTable({})
					
					
					setTimeout(function(){
						$.each(obj.data, function(i, item) {
							console.log(item)
							addRowRole(approval_id, number, i, item)
						})
					}, 1000);

				}else{
					
					var table_detail_role = 
					'<div class="div-detail-table"><span style="font-size: 16px;font-family: sora-font-regular;">Additional Role</span></div>'+
					'<div class="table-responship">'+
						'<table class="table table-striped align-middle table-row-dashed fs-6 gy-5" id="table_rolepo_tbl_'+number+'">' +
							'<thead>'+
								'<th width="250">Role</th>'+
								'<th width="20">Action</th>'+
							'</thead>'+
							'<tbody id="table_body_role">'+
								
							'</tbody>'+
						'</table>'+
					'</div>';
					
				}
				
				$('#role_table_0').html(table_detail_role);
				$('#table_body_role').append(table_detail_role_body);
				table_rolepo[number] = $('#table_rolepo_tbl_'+number).DataTable({
							responsive:true,
							"drawCallback": function( settings ) {
								json = settings.aoData
								$.each(json, function(i, item) {
									console.log(item.idx)
									setTimeout(function(){
										select2option(nomorrole[number],'approval_role_role_id_'+nomorrole[number],'Role','users_groups','id','name',null,null);
									}, 1000);
								
								})
							},
							"lengthMenu": [[-1], ['All']],
							"columnDefs": [{
								"targets": [0,0],
								"orderable": false,
								"width": "200"
							}],
						})
					
			}
	})
}


function addRowRole(approval_data_id = null, number = null, i = null, item = null){
	
	table_rolepo[number].row.add([
			'<input id="approval_role_id_'+number+'_'+nomorrole[number]+'" name="approval_role_id['+approval_data_id+'][]" style="display:none" class="form-control form-control-sm" type="text"  value="" readonly /><select name="approval_role_role_id['+approval_data_id+'][]" placeholder="Isi Product Name"  class="form-control form-control-sm" id="approval_role_role_id_'+number+'_'+nomorrole[number]+'" required></select>',
			'<button type="button" class="btn btn-sm btn-danger btn-aksi-data" onClick="removeRowRole('+nomorrole[number]+','+number+')">X</button>'
		]).node().id = 'nomorrole_'+number+'_'+nomorrole[number]
		
		table_rolepo[number].draw(false);
	
	select2option(nomorrole[number],'approval_role_role_id_'+number+'_'+nomorrole[number],'Role','users_groups','id','name',null,null);
	
	if(item == null){
	
	}else{
		$('#approval_role_id_'+number+'_'+nomorrole[number]).val(item.id);
		
		var newOptionRole = new Option(item.role_name, item.role_id, true, true);
        $('#approval_role_role_id_'+number+'_'+nomorrole[number]).append(newOptionRole).trigger('change');
		
	}
	
	nomorrole[number] = nomorrole[number] + 1;
}

function removeRowRole(nomorrole_index, number){
	table_rolepo[number].row("#nomorrole_"+number+'_'+nomorrole_index).remove().draw();
	//$('#nomor_'+nomor).remove();
}

function getformatrp(div){
	new AutoNumeric.multiple(div, 
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

function getautocomplete(div, table){
	$(div).typeahead({
		   source:  function (query, process) {
		   return $.get('<?php echo base_url().$module."/getItem"; ?>', { 
				query: query,
				table: table,
				<?php echo $this->security->get_csrf_token_name(); ?> : csrfHash 
		}, function (data) {
				  console.log(data);
				   data = $.parseJSON(data);
				   return process(data);
			   });
		   }
	});
}

function select2option(nomor,id,name,table,tableid,tablename,reference_id,reference){
		console.log('TEST')
		$("#"+id).select2({	
			width : '100%',
			multiple: false,
			ajax: {
				type: "POST",
				url: "<?php echo base_url($headurl.'/select2'); ?>",
				dataType: 'json',
				delay: 250,
				data: function (params) {
					return {
						q: params.term, // search term
						table:table,
						id:tableid,
						name:tablename,
						reference_id:reference_id,
						reference:reference,
						page: params.page,
						<?php echo $this->security->get_csrf_token_name(); ?> : csrfHash 
					};
				},
				processResults: function (data, params) {
					updateCsrfToken(data.csrf_hash)
					params.page = params.page || 1;
					return {
						results: $.map(data.items, function (item) {
							return {
								id: item.id,
								text: item.name
							}
						}),
						pagination: {
							more: (params.page * 30) < data.total_count
						}
					};
				},
				cache: true
			},
			placeholder: 'Pilih '+name
		}).on("select2:select", function(e) { 
		
			
		})
}
	
	
	
	
</script>

