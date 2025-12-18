
<?php
	
	$exclude = $exclude;
	$query_column = $this->ortyd->getviewlistform($module, $exclude, 2);
	//print_r($query_column);
	
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
						
						<?php if($iddata != 0){?>
							<div class="row style-select">
							   <div class="row">

								<div class="col-lg-9">
								
								<div class="subject-info-box-1">
								  <label>Available Menu</label>
								  <select multiple class="form-control" id="lstBox1">
								    <?php 
								  
										$menu_gid = [];
										$this->db->select('master_menu.id, master_menu.name');
										$this->db->where("(parent_id is null or parent_id = '')",null);
										//$this->db->where("master_menu.show",1);
										if($iddata !=0){
											$this->db->where("users_groups_access.gid",$iddata);
											$this->db->where("users_groups_access.view",1);
											$this->db->join('users_groups_access','users_groups_access.menu_id = master_menu.id','left');
										}else{
											$this->db->where("master_menu.id",0);
										}
										
										//$this->db->where('show',1);
										$this->db->order_by('sort','asc');
										$this->db->group_by('master_menu.id, master_menu.name, master_menu.id, master_menu.parent_id');
										$queryheadmenu = $this->db->get('master_menu');
										$queryheadmenu = $queryheadmenu->result_object();
										if($queryheadmenu){
											foreach($queryheadmenu as $rowsmenu){
												array_push($menu_gid, $rowsmenu->id);	
											}
										}
			
										$this->db->select('master_menu.id, master_menu.name, master_menu.show');
										$this->db->where("(parent_id is null or parent_id = '')",null);
										//$this->db->where("master_menu.show",1);
										//$this->db->where('show',1);
										if(count($menu_gid) > 0){
											$this->db->where_not_in("master_menu.id",$menu_gid);
										}
										$this->db->order_by('sort','asc');
										$this->db->group_by('master_menu.id, master_menu.name, master_menu.id, master_menu.parent_id, master_menu.show');
										$queryheadmenu = $this->db->get('master_menu');
										$queryheadmenu = $queryheadmenu->result_object();
										
										if(!$queryheadmenu){
											$this->db->select('master_menu.id, master_menu.name, master_menu.show');
											$this->db->where("(parent_id is null or parent_id = '')",null);
											//$this->db->where("master_menu.show",1);
											$this->db->order_by('sort','asc');
											$this->db->group_by('master_menu.id, master_menu.name, master_menu.id, master_menu.parent_id, master_menu.show');
											$queryheadmenu = $this->db->get('master_menu');
											$queryheadmenu = $queryheadmenu->result_object();
											
										}
										
										if($queryheadmenu){
											foreach($queryheadmenu as $rowsmenu) {
											$show='';	
												if($rowsmenu->show == 0){
													$show="(Not Showing)";
													
													?>
													<option style="color:red" value="<?php echo $rowsmenu->id; ?>"><?php echo $rowsmenu->name.' '.$show; ?></option>
										<?php
										
												}else{
													?>
													<option value="<?php echo $rowsmenu->id; ?>"><?php echo $rowsmenu->name.' '.$show; ?></option>
										<?php
												}
											}
										}
									?>
								  </select>
								</div>

								<div class="subject-info-arrows text-center">
								  <br />
								  <br />
								  <input class="btn btn-sm btn-primary" type='button' id='btnAllRight' value='>>' class="btn btn-default" />
								  <br />
								  <input class="btn btn-sm btn-primary"  type='button' id='btnRight' value='>' class="btn btn-default" />
								  <br />
								  <input class="btn btn-sm btn-danger"  type='button' id='btnLeft' value='<' class="btn btn-default" />
								  <br />
								  <input class="btn btn-sm btn-danger"  type='button' id='btnAllLeft' value='<<' class="btn btn-default" />
								</div>

								<div class="subject-info-box-2">
								  <label>Menu You Have Selected</label>
								  <select multiple class="form-control" id="lstBox2">
									 <?php 
								  
										$this->db->select('master_menu.id, master_menu.name, master_menu.show');
										$this->db->where("(parent_id is null or parent_id = '')",null);
										//$this->db->where("master_menu.show",1);
										if($iddata !=0){
											$this->db->where("users_groups_access.gid",$iddata);
											$this->db->where("users_groups_access.view",1);
											$this->db->join('users_groups_access','users_groups_access.menu_id = master_menu.id','left');
										}else{
											$this->db->where("master_menu.id",0);
										}
										
										//$this->db->where('show',1);
										$this->db->order_by('sort','asc');
										$this->db->group_by('master_menu.id, master_menu.name, master_menu.id, master_menu.parent_id, master_menu.show');
										$queryheadmenu = $this->db->get('master_menu');
										$queryheadmenu = $queryheadmenu->result_object();
										if($queryheadmenu){
											foreach($queryheadmenu as $rowsmenu) {
												$show='';	
												if($rowsmenu->show == 0){
													$show="(Not Showing)";
													
													?>
													<option style="color:red" value="<?php echo $rowsmenu->id; ?>"><?php echo $rowsmenu->name.' '.$show; ?></option>
										<?php
										
												}else{
													?>
													<option value="<?php echo $rowsmenu->id; ?>"><?php echo $rowsmenu->name.' '.$show; ?></option>
										<?php
												}
											}
										}
									?>
								  </select>
								</div>

								<div class="clearfix"></div>
							  </div>
							  
							  	<div class="col-lg-3">
									<div class="subject-info-box-1">
										<label>Permision</label>
										<div id="lstBox2_permision" class="form-control">

										</div>
									</div>
							    </div>
							
							
							   </div>
							  </div>
						
						
							<div class="row style-select" style="margin-top:30px">
							
							 <div class="row">
							 
							  <div class="col-md-9">
								<div class="subject-info-box-1">
								  <label>Available Child Menu</label>
								  <select multiple class="form-control" id="lstBox1_child">

								  </select>
								</div>

								<div class="subject-info-arrows text-center">
								  <br />
								  <br />
								  <input class="btn btn-sm btn-primary"  type='button' id='btnAllRight_child' value='>>' class="btn btn-default" />
								  <br />
								  <input class="btn btn-sm btn-primary"  type='button' id='btnRight_child' value='>' class="btn btn-default" />
								  <br />
								  <input class="btn btn-sm btn-danger"  type='button' id='btnLeft_child' value='<' class="btn btn-default" />
								  <br />
								  <input class="btn btn-sm btn-danger"  type='button' id='btnAllLeft_child' value='<<' class="btn btn-default" />
								</div>

								<div class="subject-info-box-2">
								  <label>Menu Child You Have Selected</label>
								  <select multiple class="form-control" id="lstBox2_child">

								  </select>
								</div>

								<div class="clearfix"></div>
							  </div>
							
								<div class="col-lg-3">
									<div class="subject-info-box-1">
										<label>Permision</label>
										<div id="lstBox2_child_permision" class="form-control">

										</div>
									</div>
							    </div>
							</div>
							</div>

						<?php } ?>

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
	
	<?php if($iddata != 0){?>
		$('#lstBox1').click(function(e) {
			parent_id = $('#lstBox1').val();
			getMenuChild(parent_id,1)
			e.preventDefault();
		});
		  
		$('#lstBox2').click(function(e) {
			parent_id = $('#lstBox2').val();
			getMenuChild(parent_id,2)
			e.preventDefault();
		});
		
		
		$('#lstBox2_child').click(function(e) {
			parent_id = $('#lstBox2_child').val();
			getPermisionChild(parent_id,2)
			e.preventDefault();
		});
		
		$('#lstBox1_child').click(function(e) {
			$("#lstBox2_child_permision").empty();
			e.preventDefault();
		});
		  
	    $('#btnRight').click(function(e) {
			$('select').moveToListAndDelete('#lstBox1', '#lstBox2');
			saveMenu('#lstBox2',1)
			e.preventDefault();
		  });

		  $('#btnAllRight').click(function(e) {
			$('select').moveAllToListAndDelete('#lstBox1', '#lstBox2');
			saveMenu('#lstBox2',1)
			e.preventDefault();
		  });

		  $('#btnLeft').click(function(e) {
			$('select').moveToListAndDelete('#lstBox2', '#lstBox1');
			saveMenu('#lstBox2',1)
			e.preventDefault();
		  });

		  $('#btnAllLeft').click(function(e) {
			$('select').moveAllToListAndDelete('#lstBox2', '#lstBox1');
			saveMenu('#lstBox2',1)
			e.preventDefault();
		  });
		  
		  
		   $('#btnRight_child').click(function(e) {
			$('select').moveToListAndDelete('#lstBox1_child', '#lstBox2_child');
			saveMenu('#lstBox2_child',2)
			e.preventDefault();
		  });

		  $('#btnAllRight_child').click(function(e) {
			$('select').moveAllToListAndDelete('#lstBox1_child', '#lstBox2_child');
			saveMenu('#lstBox2_child',2)
			e.preventDefault();
		  });

		  $('#btnLeft_child').click(function(e) {
			$('select').moveToListAndDelete('#lstBox2_child', '#lstBox1_child');
			saveMenu('#lstBox2_child',2)
			e.preventDefault();
		  });

		  $('#btnAllLeft_child').click(function(e) {
			$('select').moveAllToListAndDelete('#lstBox2_child', '#lstBox1_child');
			saveMenu('#lstBox2_child',2)
			e.preventDefault();
		  });
	<?php } ?>
	});
	
	<?php if($iddata != 0){?>
	var parent_id_tmp = null;
	function getMenuChild(parent_id,type){
		parent_id_tmp = null;
		$("#lstBox1_child").empty();
		$("#lstBox2_child").empty();
		$("#lstBox2_permision").empty();
		$("#lstBox2_child_permision").empty();
		parent_id_tmp = parent_id;
		$.post('<?php echo base_url($headurl."/getMenuChild"); ?>',{ 
			parent_id : parent_id, 
			gid : <?php echo $iddata; ?>, 
			<?php echo $this->security->get_csrf_token_name(); ?> : csrfHash  
		}, function (data) {
			 updateCsrfToken(data.csrf_hash)
            if (data.message == "success") {
				
				
				if((data.menu_access != null && data.menu_access != '' && data.menu_access != 'null') ){
					menu_accessnya = data.menu_access
					if(menu_accessnya.view == "1"){
						$("#lstBox2_permision").append('<div class="menu-item px-3"><div class="menu-content fs-6 text-dark fw-bold px-0 py-0"><input type="checkbox" onClick="saveAccessPermission(this, <?php echo $iddata; ?>)" class="form-check-input checkbox_permision" id="checkbox_permision_view"  value="1" checked="checked" readonly> View</div></div>');
					}else{
						$("#lstBox2_permision").append('<div class="menu-item px-3"><div class="menu-content fs-6 text-dark fw-bold px-0 py-0"><input type="checkbox" onClick="saveAccessPermission(this, <?php echo $iddata; ?>)" class="form-check-input checkbox_permision" id="checkbox_permision_view" value="0"> View</div></div>');
					}
					
					if(menu_accessnya.insert == "1"){
						$("#lstBox2_permision").append('<div class="menu-item px-3"><div class="menu-content fs-6 text-dark fw-bold px-0 py-0"><input type="checkbox" onClick="saveAccessPermission(this, <?php echo $iddata; ?>)"  class="form-check-input checkbox_permision" id="checkbox_permision_insert" value="1" checked="checked"> Insert</div></div>');
					}else{
						$("#lstBox2_permision").append('<div class="menu-item px-3"><div class="menu-content fs-6 text-dark fw-bold px-0 py-0"><input type="checkbox" onClick="saveAccessPermission(this, <?php echo $iddata; ?>)"  class="form-check-input checkbox_permision" id="checkbox_permision_insert" value="0"> Insert</div></div>');
					}
					
					if(menu_accessnya.update == "1"){
						$("#lstBox2_permision").append('<div class="menu-item px-3"><div class="menu-content fs-6 text-dark fw-bold px-0 py-0"><input type="checkbox" onClick="saveAccessPermission(this, <?php echo $iddata; ?>)"  class="form-check-input checkbox_permision" id="checkbox_permision_update" value="1" checked="checked"> Update</div></div>');
					}else{
						$("#lstBox2_permision").append('<div class="menu-item px-3"><div class="menu-content fs-6 text-dark fw-bold px-0 py-0"><input type="checkbox" onClick="saveAccessPermission(this, <?php echo $iddata; ?>)"  class="form-check-input checkbox_permision" id="checkbox_permision_update" value="0"> Update</div></div>');
					}
					
					if(menu_accessnya.delete == "1"){
						$("#lstBox2_permision").append('<div class="menu-item px-3"><div class="menu-content fs-6 text-dark fw-bold px-0 py-0"><input type="checkbox" onClick="saveAccessPermission(this, <?php echo $iddata; ?>)"  class="form-check-input checkbox_permision" id="checkbox_permision_delete" value="1" checked="checked"> Delete</div></div>');
					}else{
						$("#lstBox2_permision").append('<div class="menu-item px-3"><div class="menu-content fs-6 text-dark fw-bold px-0 py-0"><input type="checkbox" onClick="saveAccessPermission(this, <?php echo $iddata; ?>)"  class="form-check-input checkbox_permision" id="checkbox_permision_delete" value="0"> Delete</div></div>');
					}
				}
					
					
				if((data.menu_kiri != null && data.menu_kiri != '' && data.menu_kiri != 'null') || (data.menu_kanan != null && data.menu_kanan != '' && data.menu_kanan != 'null')){
					
					menu_kiri_nya = data.menu_kiri
					$.each(menu_kiri_nya, function(i, item) {
						//alert(menunya[i].id);
						 $("#lstBox1_child").append('<option value="'+ menu_kiri_nya[i].id +'">' + menu_kiri_nya[i].name + '</option>');
					});

					menu_kanan_nya = data.menu_kanan
					$.each(menu_kanan_nya, function(i, item) {
						//alert(menunya[i].id);
						 $("#lstBox2_child").append('<option value="'+ menu_kanan_nya[i].id +'">' + menu_kanan_nya[i].name + '</option>');
					});   
				}
                                
            }
        }, 'json');
	}
	
	function saveMenu(select,tipe){
		
		menunya = [];
		$(select +" option").each(function()
			{
				menunya.push($(this).val())
			}
		);

		$.post('<?php echo base_url($headurl."/saveAccess/".$iddata); ?>',{ 
			menu_id : menunya,
			tipe : tipe, 
			parent_id : parent_id_tmp,
			<?php echo $this->security->get_csrf_token_name(); ?> : csrfHash  
		}, function (data) {
			 updateCsrfToken(data.csrf_hash)
            if (data.message == "success") {
				console.log('as');         
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

									//loadingclose();
								});
	}
	
	function saveAccessPermission(div, gid){
		
		if($('#checkbox_permision_view').is(":checked")){
			checkbox_permision_view = 1
		}else{
			checkbox_permision_view = 0
		}
		
		if($('#checkbox_permision_insert').is(":checked")){
			checkbox_permision_insert = 1
		}else{
			checkbox_permision_insert = 0
		}
		
		if($('#checkbox_permision_update').is(":checked")){
			checkbox_permision_update = 1
		}else{
			checkbox_permision_update = 0
		}
		
		if($('#checkbox_permision_delete').is(":checked")){
			checkbox_permision_delete = 1
		}else{
			checkbox_permision_delete = 0
		}
		
		$.post('<?php echo base_url($headurl."/saveAccessPermission/".$iddata); ?>',{ 
			menu_id : parent_id_tmp,
			gid : gid,
			view : checkbox_permision_view,
			insert : checkbox_permision_insert,
			update : checkbox_permision_update,
			delete : checkbox_permision_delete,
			<?php echo $this->security->get_csrf_token_name(); ?> : csrfHash  
		}, function (data) {
			 updateCsrfToken(data.csrf_hash)
            if (data.message == "success") {
				console.log('as');         
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

									//loadingclose();
								});
	}
	
	
	var parent_child_id_tmp = null;
	function getPermisionChild(parent_id,type){
		parent_child_id_tmp = null;
		$("#lstBox2_child_permision").empty();
		parent_child_id_tmp = parent_id;
		$.post('<?php echo base_url($headurl."/getPermissionChild"); ?>',{ 
			parent_id : parent_id, 
			gid : <?php echo $iddata; ?>, 
			<?php echo $this->security->get_csrf_token_name(); ?> : csrfHash  
		}, function (data) {
			 updateCsrfToken(data.csrf_hash)
            if (data.message == "success") {
				
				if((data.menu_access != null && data.menu_access != '' && data.menu_access != 'null') ){
					menu_accessnya = data.menu_access
					if(menu_accessnya.view == "1"){
						$("#lstBox2_child_permision").append('<div class="menu-item px-3"><div class="menu-content fs-6 text-dark fw-bold px-0 py-0"><input type="checkbox" onClick="saveAccessChildPermission(this, <?php echo $iddata; ?>)" class="form-check-input checkbox_permision" id="checkbox_permision_view_child"  value="1" checked="checked" readonly> View</div></div>');
					}else{
						$("#lstBox2_child_permision").append('<div class="menu-item px-3"><div class="menu-content fs-6 text-dark fw-bold px-0 py-0"><input type="checkbox" onClick="saveAccessChildPermission(this, <?php echo $iddata; ?>)" class="form-check-input checkbox_permision" id="checkbox_permision_view_child" value="0"> View</div></div>');
					}
					
					if(menu_accessnya.insert == "1"){
						$("#lstBox2_child_permision").append('<div class="menu-item px-3"><div class="menu-content fs-6 text-dark fw-bold px-0 py-0"><input type="checkbox" onClick="saveAccessChildPermission(this, <?php echo $iddata; ?>)"  class="form-check-input checkbox_permision" id="checkbox_permision_insert_child" value="1" checked="checked"> Insert</div></div>');
					}else{
						$("#lstBox2_child_permision").append('<div class="menu-item px-3"><div class="menu-content fs-6 text-dark fw-bold px-0 py-0"><input type="checkbox" onClick="saveAccessChildPermission(this, <?php echo $iddata; ?>)"  class="form-check-input checkbox_permision" id="checkbox_permision_insert_child" value="0"> Insert</div></div>');
					}
					
					if(menu_accessnya.update == "1"){
						$("#lstBox2_child_permision").append('<div class="menu-item px-3"><div class="menu-content fs-6 text-dark fw-bold px-0 py-0"><input type="checkbox" onClick="saveAccessChildPermission(this, <?php echo $iddata; ?>)"  class="form-check-input checkbox_permision" id="checkbox_permision_update_child" value="1" checked="checked"> Update</div></div>');
					}else{
						$("#lstBox2_child_permision").append('<div class="menu-item px-3"><div class="menu-content fs-6 text-dark fw-bold px-0 py-0"><input type="checkbox" onClick="saveAccessChildPermission(this, <?php echo $iddata; ?>)"  class="form-check-input checkbox_permision" id="checkbox_permision_update_child" value="0"> Update</div></div>');
					}
					
					if(menu_accessnya.delete == "1"){
						$("#lstBox2_child_permision").append('<div class="menu-item px-3"><div class="menu-content fs-6 text-dark fw-bold px-0 py-0"><input type="checkbox" onClick="saveAccessChildPermission(this, <?php echo $iddata; ?>)"  class="form-check-input checkbox_permision" id="checkbox_permision_delete_child" value="1" checked="checked"> Delete</div></div>');
					}else{
						$("#lstBox2_child_permision").append('<div class="menu-item px-3"><div class="menu-content fs-6 text-dark fw-bold px-0 py-0"><input type="checkbox" onClick="saveAccessChildPermission(this, <?php echo $iddata; ?>)"  class="form-check-input checkbox_permision" id="checkbox_permision_delete_child" value="0"> Delete</div></div>');
					}
				}
                                
            }
        }, 'json');
	}
	
	
	function saveAccessChildPermission(div, gid){
		
		if($('#checkbox_permision_view_child').is(":checked")){
			checkbox_permision_view = 1
		}else{
			checkbox_permision_view = 0
		}
		
		if($('#checkbox_permision_insert_child').is(":checked")){
			checkbox_permision_insert = 1
		}else{
			checkbox_permision_insert = 0
		}
		
		if($('#checkbox_permision_update_child').is(":checked")){
			checkbox_permision_update = 1
		}else{
			checkbox_permision_update = 0
		}
		
		if($('#checkbox_permision_delete_child').is(":checked")){
			checkbox_permision_delete = 1
		}else{
			checkbox_permision_delete = 0
		}
		
		$.post('<?php echo base_url($headurl."/saveAccessPermission/".$iddata); ?>',{ 
			menu_id : parent_child_id_tmp,
			gid : gid,
			view : checkbox_permision_view,
			insert : checkbox_permision_insert,
			update : checkbox_permision_update,
			delete : checkbox_permision_delete,
			<?php echo $this->security->get_csrf_token_name(); ?> : csrfHash  
		}, function (data) {
			 updateCsrfToken(data.csrf_hash)
            if (data.message == "success") {
				console.log('as');         
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

									//loadingclose();
								});
	}
	
	
	<?php } ?>
	
</script>

