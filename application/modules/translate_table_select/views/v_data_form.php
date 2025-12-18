
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

						<div class="col-lg-12 py-3">
							<div class="form-group">
								<div class="row">
									<div class="col-lg-12">
										<div id="option_table"></div>
									</div>
									<div class="col-lg-12">
										<button class="btn btn-sm btn-primary" onClick="addRowoption()" type="button">Tambah Option Data</button>
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
						console.log(forminput.elements[i].attributes)
						datanya = forminput.elements[i].attributes['placeholder'].nodeValue;
						datanya = datanya.replaceAll(/<\/[^>]+(>|$)/g, "");
						requiredattrdata.push(stripHtml(datanya) + '<br>')
						requiredattr = 1;
					}
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
								text: "Data tidak berhasil disimpan!",
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
		// Add event listeners for drag and drop events
		<?php if($this->ortyd->getaccessdrag() == true){ ?>
		dragList.addEventListener('dragstart', handleDragStart);
		dragList.addEventListener('dragover', handleDragOver);
		dragList.addEventListener('drop', handleDrop);
	<?php } ?>
	
		getOption()
		
	});
	
	
	var table_option;
	var nomor=1;
	function getOption(){

		$.post('<?php echo base_url($headurl.'/getDataOption'); ?>',{
				data_id : '<?php echo $iddata; ?>',
				<?php echo $this->security->get_csrf_token_name(); ?> : csrfHash 
		},function (data) {	
				if(data != 'null'){
					$('#input-konfirmasi-nya').val('')
					var obj = jQuery.parseJSON(data);
					updateCsrfToken(obj.csrf_hash)
					var table_detail_option_body;
					if(obj.status == 'success'){
						var table_detail_option = 
						'<div class="div-detail-table">Option</div>'+
						'<div class="table-responship">'+
							'<table class="table table-striped align-middle table-row-dashed fs-6 gy-5" id="table_option_tbl">' +
								'<thead>'+
									'<th>Value</th>'+
									'<th>Action</th>'+
								'</thead>'+
								'<tbody id="table_body_option">'+
									
								'</tbody>'+
							'</table>'+
						'</div>';
						
						
						$.each(obj.data, function(i, item) {
							table_detail_option_body = table_detail_option_body+
								'<tr id="nomor_'+nomor+'">'+
									'<td><input name="tbl_id[]" style="display:none" class="form-control form-control-sm" type="text" value="'+item.id+'" /><input class="form-control form-control-sm"  name="tbl_option_name[]" type="text" value="'+item.option_value+'" /></td>' +
									'<td><button class="btn btn-sm btn-danger" onClick="removeRowoption('+nomor+')">X</button></td>' +
								'</tr>';
							nomor = nomor + 1;
						})
						
						
						$('#option_table').html(table_detail_option);
						$('#table_body_option').append(table_detail_option_body);
						table_option = $('#table_option_tbl').DataTable({})

					}else{
						
						var table_detail_option = 
						'<div class="div-detail-table">Option</div>'+
						'<div class="table-responship">'+
							'<table class="table table-striped align-middle table-row-dashed fs-6 gy-5" id="table_option_tbl">' +
								'<thead>'+
									'<th>Value</th>'+
									'<th>Action</th>'+
								'</thead>'+
								'<tbody id="table_body_option">'+
									
								'</tbody>'+
							'</table>'+
						'</div>';
						
					}
					
					$('#option_table').html(table_detail_option);
					$('#table_body_option').append(table_detail_option_body);
					table_option = $('#table_option_tbl').DataTable({})
						
				}
		})
	}

	function addRowoption(){
		table_option.row.add([
			'<input name="tbl_id[]" style="display:none" class="form-control form-control-sm" type="text"  value="0"/><input placeholder="Isi Value" class="form-control form-control-sm" type="text" name="tbl_option_name[]" value="" required/>',
			'<button class="btn btn-sm btn-danger" onClick="removeRowoption('+nomor+')">X</button>'
		]).node().id = 'nomor_'+nomor;
		
		table_option.draw(false);
		nomor = nomor + 1;
	}

	function removeRowoption(nomor){
		table_option.row("#nomor_"+nomor).remove().draw();
		//$('#nomor_'+nomor).remove();
	}

</script>

