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
	
	//$status_id = $this->ortyd->select2_getname($iddata,$module,'id','status_id');
	$status_id = 0;
	$createdid  = $this->ortyd->select2_getname($iddata,$module,'id','createdid');
	if($status_id == '' || $status_id == '0' || $status_id == 0 || $status_id == null || $status_id == '-'){
		$status_id = 0;
	}
	
	// Check if this is popup mode
	$is_popup = isset($is_popup) && $is_popup === true;
	$container_class = $is_popup ? 'popup-form-container' : '';
	$form_id_suffix = $is_popup ? '_popup' : '';
	
	$editdatanya = 0;
	if(isset($_GET['edit'])){
		if($_GET['edit'] == 'true'){
			$editdatanya = 1;
			$action = $action.'?edit=true';
		}
	}
?>

<?php if (!$is_popup): ?>
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
<?php endif; ?>

<?php if ($is_popup): ?>
<div class="<?php echo $container_class; ?>">
	<form id="form<?php echo $iddata . $form_id_suffix; ?>" method="POST" action="<?php echo $action; ?>" enctype="multipart/form-data">
<?php endif; ?>

					<div class="row" id="dragList<?php echo $is_popup ? 'Popup' : ''; ?>">	
				<?php
					if($query_column){
						$indentitas = 0;
						foreach($query_column as $rows_column){ 
						
								$readonlyselect = '';
								$css_hidden = '';
								$disable = '';
								
								if($status_id != 0 && $status_id != 99){
									$readonlyselect = true;
									$disable = ' readonly ';
									$css_hidden = ' hidden_css ';
								}
								
								if($status_id == 99){ 
									if($this->session->userdata('userid') == $createdid || $this->session->userdata('group_id') == 1){
										
									}else{
										$readonlyselect = true;
										$disable = ' readonly ';
										$css_hidden = ' hidden_css ';
									}
								}
								
								if($editdatanya == 1){
									$disable = '';
									$readonlyselect = '';
								}
							
							$viewtable = $module;
							if(isset($tableview)){
								if($tableview != '' && $tableview != $module){
									$viewtable = $tableview;
								}
								
							}
							
							$width_column = $this->ortyd->width_column($viewtable,$rows_column['name']);
							$tipe_data = $this->ortyd->getTipeData($viewtable,$rows_column['name']);
							$label_name = $this->ortyd->translate_column($viewtable,$rows_column['name']);
							$label_name_text = $label_name;
							if($rows_column['name']){
								$table_change = "'".$viewtable."'";
								$table_change_id = "'".$rows_column['name']."'";
								$label_name_text_data = "'".$label_name_text."'";
								$editheader = ' <span style="cursor:pointer" onClick="changeTitle('.$table_change.','.$table_change_id.','.$label_name_text_data.')"><i class="fa fa-edit"></i></span>';
								if($this->ortyd->getAksesEditNaming() == true && !$is_popup){
									$label_name = $label_name.$editheader;
								}else{
									$label_name = $label_name;
								}
							}
							
							$labelrequired = 0;
							if($rows_column['is_nullable'] == 'NO'){
								$labelrequired = 1;
								$label_name = $label_name.' <span style="color: red;">*</span>';
							}else{
								$this->db->where('master_lop_field_required.field',$rows_column['name']);
								$this->db->where('master_lop_field_required_tipe.module', $viewtable);
								$this->db->where('master_lop_field_required.active', 1);
								$this->db->join('master_lop_field_required_tipe','master_lop_field_required.tipe_id = master_lop_field_required_tipe.id');
								$querytab = $this->db->get('master_lop_field_required');
								$querytab = $querytab->result_object();
								if($querytab){
									$label_name = $label_name.' <span style="color: red;">*</span>';
								}
							}
							
							// Adjust layout for popup
							$form_group_class = $is_popup ? 'form-group mb-3' : 'form-group';
							$row_class = $is_popup ? 'row' : 'row';
							$label_col = $is_popup ? 'col-lg-3' : 'col-lg-3';
							$input_col = $is_popup ? 'col-lg-9' : 'col-lg-9';
							$field_id_suffix = $is_popup ? '_popup' : '';
							
							?>
							
							<?php
							
							if($tipe_data == 'TEXTAREA'){ ?>
							
								<?php if($rows_column['name'] == 'id'){ ?>
									
								<?php }else{ ?>
								
									<div id="<?php echo $rows_column['name'].'_header'.$field_id_suffix; ?>" draggable="<?php echo $is_popup ? 'false' : 'true'; ?>" class="<?php echo $is_popup ? '' : 'drag-item'; ?> col-lg-<?php echo $width_column; ?> py-3">
										<div class="<?php echo $form_group_class; ?>">
											<div class="<?php echo $row_class; ?>">
												<div class="<?php echo $label_col; ?>">
													<label><?php echo $label_name; ?></label>
												</div>
												<div class="<?php echo $input_col; ?>">
													<textarea rows="3" name="<?php echo $rows_column['name']; ?>" id="<?php echo $rows_column['name'].$field_id_suffix; ?>" class="form-control form-control-sm" <?php if($rows_column['is_nullable'] == 'NO'){echo ' required';} ?> <?php echo $disable; ?> placeholder="<?php echo 'Input '.$label_name_text; ?>"><?php echo ${$rows_column['name']}; ?></textarea>
												</div>
											</div>
										</div>
									</div>
								
								<?php } ?>
						
						
						<?php }elseif($tipe_data == 'TEXTEDITOR'){ ?>
							
								<?php if($rows_column['name'] == 'id'){ ?>
									
								<?php }else{ ?>
								
									<div id="<?php echo $rows_column['name'].'_header'.$field_id_suffix; ?>" draggable="<?php echo $is_popup ? 'false' : 'true'; ?>" class="<?php echo $is_popup ? '' : 'drag-item'; ?> col-lg-<?php echo $width_column; ?> py-3">
										<div class="<?php echo $form_group_class; ?>">
											<div class="<?php echo $row_class; ?>">
												<div class="<?php echo $label_col; ?>">
													<label><?php echo $label_name; ?></label>
												</div>
												<div class="<?php echo $input_col; ?>">
													<textarea rows="3" name="<?php echo $rows_column['name']; ?>" id="<?php echo $rows_column['name'].$field_id_suffix; ?>" class="form-control form-control-sm summernote" <?php if($rows_column['is_nullable'] == 'NO'){echo ' required';} ?> <?php echo $disable; ?> placeholder="<?php echo 'Input '.$label_name_text; ?>"><?php echo ${$rows_column['name']}; ?></textarea>
												</div>
											</div>
										</div>
									</div>
								
								<?php } ?>
						
						
						<?php }elseif($tipe_data == 'DATE' || $rows_column['name'] == 'date' || $rows_column['name'] == 'tanggal'){ ?>
							
								<div id="<?php echo $rows_column['name'].'_header'.$field_id_suffix; ?>" draggable="<?php echo $is_popup ? 'false' : 'true'; ?>" class="<?php echo $is_popup ? '' : 'drag-item'; ?> col-lg-<?php echo $width_column; ?> py-3">
									<div class="<?php echo $form_group_class; ?>">
										<div class="<?php echo $row_class; ?>">
											<div class="<?php echo $label_col; ?>">
												<label><?php echo $label_name; ?></label>
											</div>
											<div class="<?php echo $input_col; ?>">
												<input id="<?php echo $rows_column['name'].$field_id_suffix; ?>" type="text" name="<?php echo $rows_column['name']; ?>" class="form-control form-control-sm datetime" value="<?php echo ${$rows_column['name']}; ?>"<?php if($rows_column['is_nullable'] == 'NO'){echo ' required ';} ?> <?php echo $disable; ?> readonly='true' placeholder="<?php echo 'Input '.$label_name_text; ?>"/> 
											</div>
										</div>
									</div>
								</div>
								
						<?php }elseif($tipe_data == 'DATETIME'){ ?>
							
								<div id="<?php echo $rows_column['name'].'_header'.$field_id_suffix; ?>" draggable="<?php echo $is_popup ? 'false' : 'true'; ?>" class="<?php echo $is_popup ? '' : 'drag-item'; ?> col-lg-<?php echo $width_column; ?> py-3">
									<div class="<?php echo $form_group_class; ?>">
										<div class="<?php echo $row_class; ?>">
											<div class="<?php echo $label_col; ?>">
												<label><?php echo $label_name; ?></label>
											</div>
											<div class="<?php echo $input_col; ?>">
												<input id="<?php echo $rows_column['name'].$field_id_suffix; ?>" type="text" name="<?php echo $rows_column['name']; ?>" class="form-control form-control-sm datepickertime" value="<?php echo ${$rows_column['name']}; ?>"<?php if($rows_column['is_nullable'] == 'NO'){echo ' required ';} ?> <?php echo $disable; ?> readonly='true' placeholder="<?php echo 'Input '.$label_name_text; ?>"/> 
											</div>
										</div>
									</div>
								</div>
							
						<?php }elseif($rows_column['name'] == 'email'){ ?>
							
								<div id="<?php echo $rows_column['name'].'_header'.$field_id_suffix; ?>" draggable="<?php echo $is_popup ? 'false' : 'true'; ?>" class="<?php echo $is_popup ? '' : 'drag-item'; ?> col-lg-<?php echo $width_column; ?> py-3">
									<div class="<?php echo $form_group_class; ?>">
										<div class="<?php echo $row_class; ?>">
											<div class="<?php echo $label_col; ?>">
												<label><?php echo $label_name; ?></label>
											</div>
											<div class="<?php echo $input_col; ?>">
												<input id="<?php echo $rows_column['name'].$field_id_suffix; ?>" type="email" name="<?php echo $rows_column['name']; ?>" class="form-control form-control-sm" value="<?php echo ${$rows_column['name']}; ?>"<?php if($rows_column['is_nullable'] == 'NO'){echo ' required ';} ?> <?php echo $disable; ?> placeholder="<?php echo 'Input '.$label_name_text; ?>" /> 
											</div>
										</div>
									</div>
								</div>
							
						<?php }elseif($tipe_data == 'NUMBER'){ ?>
							
								<div id="<?php echo $rows_column['name'].'_header'.$field_id_suffix; ?>" draggable="<?php echo $is_popup ? 'false' : 'true'; ?>" class="<?php echo $is_popup ? '' : 'drag-item'; ?> col-lg-<?php echo $width_column; ?> py-3">
									<div class="<?php echo $form_group_class; ?>">
										<div class="<?php echo $row_class; ?>">
											<div class="<?php echo $label_col; ?>">
												<label><?php echo $label_name; ?></label>
											</div>
											<div class="<?php echo $input_col; ?>">
												<input id="<?php echo $rows_column['name'].$field_id_suffix; ?>" type="number" name="<?php echo $rows_column['name']; ?>" class="form-control form-control-sm" value="<?php echo ${$rows_column['name']}; ?>"<?php if($rows_column['is_nullable'] == 'NO'){echo ' required ';} ?> <?php echo $disable; ?> placeholder="<?php echo 'Input '.$label_name_text; ?>" />
											</div>
										</div>
									</div>
								</div>
								
						<?php }elseif($tipe_data == 'CURRENCY'){ ?>
							
								<div id="<?php echo $rows_column['name'].'_header'.$field_id_suffix; ?>" draggable="<?php echo $is_popup ? 'false' : 'true'; ?>" class="<?php echo $is_popup ? '' : 'drag-item'; ?> col-lg-<?php echo $width_column; ?> py-3">
									<div class="<?php echo $form_group_class; ?>">
										<div class="<?php echo $row_class; ?>">
											<div class="<?php echo $label_col; ?>">
												<label><?php echo $label_name; ?></label>
											</div>
											<div class="<?php echo $input_col; ?>">
												<input id="<?php echo $rows_column['name'].$field_id_suffix; ?>" type="text" name="<?php echo $rows_column['name']; ?>" class="form-control form-control-sm numeric-rp" value="<?php echo ${$rows_column['name']}; ?>"<?php if($rows_column['is_nullable'] == 'NO'){echo ' required ';} ?> <?php echo $disable; ?> placeholder="<?php echo 'Input '.$label_name_text; ?>" />
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
		
									<div id="<?php echo $rows_column['name'].'_header'.$field_id_suffix; ?>" draggable="<?php echo $is_popup ? 'false' : 'true'; ?>" class="<?php echo $is_popup ? '' : 'drag-item'; ?> col-lg-<?php echo $width_column; ?> py-3">
										<div class="<?php echo $form_group_class; ?>">
											<div class="<?php echo $row_class; ?>">
												<div class="<?php echo $label_col; ?>">
													<label><?php echo $label_name; ?></label>
												</div>
												<div class="<?php echo $input_col; ?>">
													<input type="number" id="<?php echo $rows_column['name'].$field_id_suffix; ?>" name="<?php echo $rows_column['name']; ?>" class="form-control form-control-sm" value="<?php echo ${$rows_column['name']}; ?>" <?php if($rows_column['is_nullable'] == 'NO'){echo ' required';} ?> <?php echo $disable; ?> placeholder="<?php echo 'Input '.$label_name_text; ?>" min="1" /> 
												</div>
											</div>
										</div>
									</div>
								
								
								<?php }else{ ?>
							
									<div id="<?php echo $rows_column['name'].'_header'.$field_id_suffix; ?>" draggable="<?php echo $is_popup ? 'false' : 'true'; ?>" class="<?php echo $is_popup ? '' : 'drag-item'; ?> col-lg-<?php echo $width_column; ?> py-3">
										<div class="<?php echo $form_group_class; ?>">
											<div class="<?php echo $row_class; ?>">
												<div class="<?php echo $label_col; ?>">
													<label><?php echo $label_name; ?></label>
												</div>
												<div class="<?php echo $input_col; ?>">
													<input type="text" name="<?php echo $rows_column['name']; ?>" id="<?php echo $rows_column['name'].$field_id_suffix; ?>" class="form-control form-control-sm" value="<?php echo ${$rows_column['name']}; ?>" <?php if($rows_column['is_nullable'] == 'NO'){echo ' required';} ?> <?php echo $disable; ?> placeholder="<?php echo 'Input '.$label_name_text; ?>" /> 
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
					
					<?php if (!$is_popup): ?>
					<div class="card-footer col-lg-12 py-3" id="btn-aksi-action">
					
						<div class="row">
											
							<?php if($status_id == 0 || $status_id == 100 || $status_id == 99){ ?> 
							
								<?php if($this->ortyd->access_check_insert($module)){ 
								
									if($status_id == 99){ 

										if($this->session->userdata('userid') == $createdid || $this->session->userdata('group_id') == 1){ ?>
											<div class="col-lg-12" style="padding:0;padding-right: 5px;">
												<button style="margin-left:10px;width:100%" type="button" id="kt_docs_formvalidation_text_submit" class="btn btn-primary pull-right">
														<i class="fa fa-save"></i> Simpan Data
												</button>
											</div>
										<?php } ?>
									
									<?php }else{ ?>
									
										<div class="col-lg-12" style="padding:0;padding-right: 5px;">
												<button style="margin-left:10px;width:100%" type="button" id="kt_docs_formvalidation_text_submit" class="btn btn-primary pull-right">
														<i class="fa fa-save"></i> Simpan Data
												</button>
											</div>
											
									<?php } ?>
									
								<?php } ?>
								
							<?php }elseif($status_id == 1 || $status_id == 2){ ?> 
							
								<?php 
									$approvaldata = $this->ortyd->getApproval(1, $iddata);
									
									if($approvaldata != false){ 
										if($approvaldata['group_id'] == $this->session->userdata('group_id') || $this->session->userdata('group_id') == 1){ ?>
									
										<div class="col-lg-12" style="padding:0;padding-right: 5px;">
											<button style="margin-left:10px;width:100%" type="button" id="kt_docs_formvalidation_text_submit" class="btn btn-primary pull-right">
													<i class="fa fa-save"></i> <?php echo $approvaldata['name']; ?>
											</div>
									<?php } 
									}
								?>
								
							<?php }else{ ?>
							
								<?php if($this->ortyd->access_check_insert($module)){  ?>
									<div class="col-lg-12" style="padding:0;padding-right: 5px;<?php if($editdatanya == 1){  echo ''; }else{ echo 'display:none';}?>">
										<button style="margin-left:10px;width:100%" type="button" id="kt_docs_formvalidation_text_submit" class="btn btn-primary pull-right">
												<i class="fa fa-save"></i> Simpan Data
										</button>
									</div>
								<?php } ?>

							<?php } ?>
							
						</div>

					</div>
					<?php endif; ?>
					
					<?php if ($is_popup): ?>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">
							<i class="fas fa-times"></i> Batal
						</button>
						<button type="button" class="btn btn-primary" id="kt_docs_formvalidation_text_submit<?php echo $form_id_suffix; ?>">
							<i class="fas fa-save"></i> Simpan & Kirim Data
						</button>
					</div>
					<?php endif; ?>
						
					</div>	

<?php if ($is_popup): ?>
	</form>
</div>
<?php endif; ?>

<?php if (!$is_popup): ?>
				</div>
							</div>
						</div>
				</div>
		</div>
</div>
</form>
<?php endif; ?>

<div class="col-xl-12" style="display:none">
	<div id="box2tableKonfirmasi">
	
		<div class="form-group" id="kualifikasi_form">
			<label class="lbl-comment">
				Validasi 
			</label>
			
			<select class="form-control form-control-sm" id="select-konfirmasi">
				<option value="1">Lanjutkan</option>
				<option value="0">Revisi</option>
			</select>
			
		</div>
		
		<div class="form-group">
			<label class="lbl-comment">
				Isi Catatan anda
			</label>
			
			<textarea rows="3" name="input-comment" id="input-konfirmasi" class="form-control form-control-sm"></textarea>									
		</div>
		<div class="form-group">
			<button type="button" id="btn-konfirmasi" class="btn btn-create" style="float: none;margin-right: 10px;">Submit</button>				
		</div>
	</div>
</div>

<div class="col-xl-12" style="display:none">
	<div id="box2tableKonfirmasiReject">
		<div class="form-group">
			<label class="lbl-comment">
				Isi Catatan anda
			</label>
			
			<textarea rows="3" name="input-comment" id="input-konfirmasi" class="form-control form-control-sm"></textarea>									
		</div>
		<div class="form-group">
			<button type="button" id="btn-konfirmasi" class="btn btn-create" style="float: none;margin-right: 10px;">Submit</button>				
		</div>
	</div>
</div>

<script>
	
	$( document ).ready(function() {
		
		<?php if($editdatanya == 1){ ?>
			setTimeout(function(){ $('.hidden_css').hide(); }, 3000);
		<?php } ?>
		
		<?php if ($is_popup): ?>
		// Popup form specific initialization
		initializePopupForm();
		<?php else: ?>
		// Regular form initialization
		initializeRegularForm();
		<?php endif; ?>
		
	});
	
	<?php if ($is_popup): ?>
	function initializePopupForm() {
		// Handle popup form submission
		$('#kt_docs_formvalidation_text_submit<?php echo $form_id_suffix; ?>').on('click', function(e) {
			e.preventDefault();
			handlePopupFormSubmission();
		});
	}
	
	function handlePopupFormSubmission() {
		
		// Required field validation
		<?php 
			$this->db->where('master_lop_field_required_tipe.module', $module);
			$this->db->where('master_lop_field_required.active', 1);
			$this->db->join('master_lop_field_required_tipe','master_lop_field_required.tipe_id = master_lop_field_required_tipe.id');
			$querytab = $this->db->get('master_lop_field_required');
			$querytab = $querytab->result_object();
			if($querytab){
				foreach($querytab as $rowstab){
		?>
			$('#<?php echo $rowstab->field; ?>_popup').prop('required',true);
		<?php
				}
			}
		?>
		
		var forminput = document.getElementById('form<?php echo $iddata . $form_id_suffix; ?>');
		
		// Form validation
		var requiredattr = 0;
		var requiredattrdata = [];
		var datanya;
		
		for(var i=0; i < forminput.elements.length; i++){
			if(forminput.elements[i].value === '' && forminput.elements[i].hasAttribute('required')){
				datanya = forminput.elements[i].attributes['placeholder'].nodeValue;
				datanya = datanya.replaceAll(/<\/[^>]+(>|$)/g, "");
				requiredattrdata.push(stripHtml(datanya) + '<br>')
				requiredattr = 1;
			}
		}
		
		if(requiredattr == 0){
			// Show confirmation dialog
			Swal.fire({
				icon: "info",
				title: "<?php echo ($iddata > 0) ? 'Update' : 'Submit'; ?> Data",
				html: 'Apakah anda yakin akan <?php echo ($iddata > 0) ? 'mengupdate' : 'menyimpan'; ?> data ini<?php echo ($iddata == 0) ? ' dan mengirimkan ke verifikator' : ''; ?>?',
				showCancelButton: true,
				confirmButtonText: "Ya, <?php echo ($iddata > 0) ? 'Update' : 'Simpan'; ?>",
				cancelButtonText: "Batal",
				cancelButtonColor: "#d33"
			}).then((result) => {
				if (result.isConfirmed) {
					submitPopupData();
				}
			});
		} else {
			datanya = requiredattrdata.toString().replaceAll(",","");
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
	}
	
	function submitPopupData() {
		var formData = new FormData(document.getElementById('form<?php echo $iddata . $form_id_suffix; ?>'));
		
		// Show loading
		Swal.fire({
			title: 'Menyimpan...',
			text: 'Sedang memproses data',
			allowOutsideClick: false,
			didOpen: () => {
				Swal.showLoading();
			}
		});
		
		$.ajax({
			url: '<?php echo $action; ?>',
			type: 'POST',
			data: formData,
			processData: false,
			contentType: false,
			dataType: 'json',
			success: function(response) {
				if(response.status == "success"){
					Swal.fire({
						icon: 'success',
						title: 'Berhasil!',
						text: 'Data berhasil <?php echo ($iddata > 0) ? 'diupdate' : 'disimpan dan dikirim'; ?>',
						timer: 2000,
						showConfirmButton: false
					}).then(() => {
						$('#popupFormModal').modal('hide');
						// Refresh datatable
						if (typeof table !== 'undefined') {
							table.ajax.reload();
						}
					});
				} else {
					Swal.fire({
						icon: 'error',
						title: 'Error!',
						text: 'Data tidak berhasil disimpan: ' + (response.errors || 'Unknown error'),
						confirmButtonText: 'OK'
					});
				}
				
				// Update CSRF token
				if (response.csrf_hash) {
					updateCsrfToken(response.csrf_hash);
				}
			},
			error: function(jqxhr, status, error) {
				console.error("Request failed: " + error);
				
				Swal.fire({
					icon: 'error',
					title: 'Error!',
					text: 'Terjadi kesalahan saat menyimpan data',
					confirmButtonText: 'OK'
				});
			}
		});
	}
	
	<?php else: ?>
	function initializeRegularForm() {
		// Handle regular form submission
		const submitButton = document.getElementById('kt_docs_formvalidation_text_submit');
		if (submitButton) {
			submitButton.addEventListener('click', function (e) {
				handleRegularFormSubmission(e);
			});
		}
	}
	
	function handleRegularFormSubmission(e) {
		// Required field validation
		<?php 
			$this->db->where('master_lop_field_required_tipe.module', $viewtable);
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
		
		<?php if($status_id == 0 || $status_id == 99 || $status_id == 100){ ?> 
		Swal.fire({
			icon: "info",
			title: "Submit Data",
			html: 'Apakah anda yakin akan menyimpan data ini dan mengirimkan ke verifikator ? <p></p><span style="color:red">Pastikan semua data yang ada sesuai.</span>',
			showDenyButton: false,
			showCancelButton: true,
			confirmButtonText: "Iya, Saya Setuju",
			cancelButtonText: "Tidak, Sesuaikan Inputan",
			cancelButtonColor: "#ff0000",
		}).then((result) => {
			if (result.isConfirmed) {
				loadingopen();
				e.preventDefault();
				submitRegularForm();
			}
		});
		<?php }else{ ?> 
		e.preventDefault();
		submitRegularForm();
		<?php } ?> 
	}
	
	function submitRegularForm() {
		var forminput = document.getElementById('form<?php echo $iddata; ?>');
		var requiredattr = 0;
		var requiredattrdata = [];
		var datanya;
		
		for(var i=0; i < forminput.elements.length; i++){
			if(forminput.elements[i].value === '' && forminput.elements[i].hasAttribute('required')){
				datanya = forminput.elements[i].attributes['placeholder'].nodeValue;
				datanya = datanya.replaceAll(/<\/[^>]+(>|$)/g, "");
				requiredattrdata.push(stripHtml(datanya) + '<br>')
				requiredattr = 1;
			}
		}
		
		totalrequired = $('#total_required').val();
		if(totalrequired > 0){
			message = "Perhatikan Tanda Merah pada setiap bagian input, masih ada data yang belum terisi ";
			datanya = message;
			datanya = datanya.replaceAll(/<\/[^>]+(>|$)/g, "");
			requiredattrdata.push(stripHtml(datanya) + '<br>')
			requiredattr = 1;
		}

		if(requiredattr == 0){
			$.post('<?php echo $action; ?>', $('#form<?php echo $iddata; ?>').serialize(),function (data) {
				updateCsrfToken(data.csrf_hash)
				if(data.status == "success"){
					<?php if($status_id == 3 || $status_id == 4 || $status_id == 5){ ?> 
						popupboxpublikasi('<?php echo $iddata; ?>');
					<?php }elseif($status_id == 0 || $status_id == 99 || $status_id == 100){ ?> 
						Swal.fire({
							text: "Data berhasil disimpan! dan dikirimkan",
							icon: "success",
							buttonsStyling: false,
							confirmButtonText: "Ok, got it!",
							customClass: {
								confirmButton: "btn btn-primary"
							}
						});
						
						loadingclose();
						
						setTimeout(() => {
							window.location.href = '<?php echo base_url($headurl); ?>';
						}, 100);
					<?php }else{ ?> 
						popupboxkonfirmasi('<?php echo $iddata; ?>');
					<?php } ?> 
				}else{
					Swal.fire({
						text: "Data tidak berhasil disimpan!, " + data.errors,
						icon: "error",
						buttonsStyling: false,
						confirmButtonText: "Tutup",
						customClass: {
							confirmButton: "btn btn-primary"
						}
					});
					loadingclose();
				}
			}, 'json')
			.fail(function(jqxhr, status, error) {
				console.error("Request failed: " + error);
				
				if (jqxhr.status === 403) {
					$.get('<?php echo base_url('request_csrf_token'); ?>', function(data) {
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
					}
				});
			});
		}else{
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
			loadingclose();
		}
	}
	<?php endif; ?>
	
	// Common functions
	function stripHtml(html) {
		var tmp = document.createElement("DIV");
		tmp.innerHTML = html;
		return tmp.textContent || tmp.innerText || "";
	}
	
	<?php if (!$is_popup): ?>
	// Drag and drop functionality (only for regular form)
	$( document ).ready(function() {
		<?php if($this->ortyd->getaccessdrag() == true){ ?>
		var dragList = document.getElementById('dragList');
		if (dragList) {
			dragList.addEventListener('dragstart', handleDragStart);
			dragList.addEventListener('dragover', handleDragOver);
			dragList.addEventListener('drop', handleDrop);
		}
		<?php } ?>
	});
	<?php endif; ?>
	
	// Confirmation functions (existing code)
	var box1;
	function popupboxkonfirmasi(id){
		if(id != '0' && id != null && id != ''){
			var id_data = id
			var container = $('#box2tableKonfirmasi').clone();
			container.find('#select-konfirmasi').attr('id', 'select-konfirmasi-nya');
			container.find('#input-konfirmasi').attr('id', 'input-konfirmasi-nya');
			container.find('#btn-konfirmasi').attr('id', 'btn-konfirmasi-nya');
			container.find('#kualifikasi_form').attr('id', 'kualifikasi_form-nya');
			
			box1 = bootbox.dialog({
					size: "large",
					show: true,
					backdrop: true,
					message: container.html(),
					title: '<i class="ti ti-info-alt"></i> Konfirmasi',
					subTitle: ''
			})
			
			box1.on("shown.bs.modal", function() {
				<?php if($status_id == 0){ ?>
					$('#kualifikasi_form-nya').hide();
				<?php }else{ ?>
					$('#kualifikasi_form-nya').show();
				<?php } ?>
				document.getElementById('btn-konfirmasi-nya').innerHTML = '<i class="ti ti-shift-right"></i> Submit';
				$('#input-konfirmasi-nya').prop("disabled",false);
				
				$("#btn-konfirmasi-nya").click(function() {
					if ($('#input-konfirmasi-nya').val() == '' || $('#input-konfirmasi-nya').val() == null) {
						Swal.fire('Warning', 'Isi Catatan Anda', 'warning');
					} else if ($('#select-konfirmasi-nya').val() == '' || $('#select-konfirmasi-nya').val() == null) {
						Swal.fire('Warning', 'Pilih Kualifikasi', 'warning');
					} else {
						Swal.fire({
							title: 'Konfirmasi',
							text: 'Apakah Anda yakin ingin submit data ini?',
							icon: 'question',
							showCancelButton: true,
							confirmButtonColor: '#3085d6',
							cancelButtonColor: '#d33',
							confirmButtonText: 'Ya, Submit!',
							cancelButtonText: 'Batal'
						}).then((result) => {
							if (result.isConfirmed) {
								box1.modal('hide');
								saveKonfirmasi(id, 
									$('#select-konfirmasi-nya').val(), 
									$('#input-konfirmasi-nya').val()
								);
							}
						});
					}
				});
			});
		}
	}

	function saveKonfirmasi(id, select, input){
		loadingopenprog()
		$.post('<?php echo base_url($headurl.'/actiondata_konfirmasi'); ?>',{
				input : input,
				select : select,
				id : id,
				<?php echo $this->security->get_csrf_token_name(); ?> : csrfHash 
		},function (data) {	
				if(data != 'null'){
					$('#input-konfirmasi-nya').val('')
					var obj = jQuery.parseJSON(data);
					updateCsrfToken(obj.csrf_hash)
					if(obj.status == 'success'){
						if(obj.status_id == 99){
							Swal.fire({
								position: 'center',
								icon: 'success',
								title: 'Data berhasil dikembalikan',
								showConfirmButton: false,
								timer: 1500
							})
						}else{
							Swal.fire({
								position: 'center',
								icon: 'success',
								title: 'Data berhasil dikirim',
								showConfirmButton: false,
								timer: 1500
							})
						}
						
						setTimeout(function(){
							window.location.href = '<?php echo base_url($headurl); ?>';
						}, 100);
					}else{
						Swal.fire({
							icon: 'error',
							title: 'Kesalahan...',
							text: 'Ada sesuatu yang salah!,' + obj.errors,
						})
						loadingcloseprog()
					}
				}
		})
	}

</script>

<?php if ($is_popup): ?>
<style>
.popup-form-container .form-group {
    margin-bottom: 1rem;
}

.popup-form-container .modal-body {
    padding: 1.5rem;
}

.popup-form-container .select2-container {
    width: 100% !important;
}

.popup-form-container .select2-dropdown {
    z-index: 9999;
}

.popup-form-container .note-editor {
    border: 1px solid #ced4da;
    border-radius: 0.375rem;
}

.popup-form-container .datetime,
.popup-form-container .datepickertime {
    background-color: #f8f9fa;
}

.popup-form-container label {
    font-weight: 500;
    color: #495057;
}

.popup-form-container .form-control:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

.popup-form-container .btn-primary {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.popup-form-container .btn-primary:hover {
    background-color: #0b5ed7;
    border-color: #0a58ca;
}

.popup-form-container .modal-footer {
    border-top: 1px solid #dee2e6;
    padding: 1rem 1.5rem;
}

@media (max-width: 768px) {
    .popup-form-container .col-lg-4,
    .popup-form-container .col-lg-8 {
        flex: 0 0 100%;
        max-width: 100%;
    }
    
    .popup-form-container .col-lg-4 {
        margin-bottom: 0.5rem;
    }
}
</style>
<?php endif; ?>