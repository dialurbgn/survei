

	<?php

		$username = '';
		$password = '';
		$email = '';
		$gid = '';
		$active = 1;
		$banned = 0;
		$notelp = '';
		$fullname = '';
		$signature = '';
		$user_id_ref = '';
		
		if(isset($id)){
			if($id == 0){
				$id = '';
				$iddata = 0;
				$type = 'Buat Baru';
			}else{
				$id = $id;
				$iddata = $id;
				$type = 'Edit';
				if($datarow && $datarow != null){
					foreach ($datarow as $rows) {
						$username = $rows->username;
						$password = null;
						$email = $rows->email;
						$gid = $rows->gid;
						$active = $rows->active;
						$banned = $rows->banned;
						$notelp = $rows->notelp;
						$fullname = $rows->fullname;
						$signature = $rows->signature;
						$user_id_ref = $rows->user_id_ref;
					}
				}
			}
		}else{
			$id = '';
			$iddata = 0;
			$type = 'Buat Baru';
		}
	?>


<!--begin::Container-->
					<div id="kt_content_container" class="d-flex flex-column-fluid align-items-start container-xxl">
						<!--begin::Post-->
						<div class="content flex-row-fluid" id="kt_content">
							<!--begin::Layout-->
							<div class="d-flex flex-column flex-lg-row">
								<!--begin::Sidebar-->
								<div class="flex-column flex-lg-row-auto w-lg-250px w-xl-350px mb-10">
									<!--begin::Card-->
									<div class="card mb-5 mb-xl-8">
										<!--begin::Card body-->
										<div class="card-body">
											<!--begin::Summary-->
											<!--begin::User Info-->
											<div class="d-flex flex-center flex-column py-5">
												<!--begin::Avatar-->
												<div class="symbol symbol-100px symbol-circle mb-7">
												
												<?php
																		$fullname = $this->session->userdata('fullname');
																		$this->db->select('data_gallery.path, users_data.fullname');
																		$this->db->where('email',$this->session->userdata('email'));
																		$this->db->join('data_gallery','data_gallery.id = users_data.cover', 'left');
																		$queryimage = $this->db->get('users_data');
																		$queryimage = $queryimage->result_object();
																		if($queryimage){
																			$fullname = $queryimage[0]->fullname;
																			if($queryimage[0]->path != ''){
																				$imagecover = base_url().$queryimage[0]->path;
																			}else{
																				$imagecover = base_url().'favicon.png';
																			}
																			//echo ' ('.$query[0]->name.')';
																		}else{
																			$imagecover = base_url().'favicon.png';
																			//echo ' (no set role)';
																		}			
																	?>
																	
													<img  id="nameamsrc" src="<?php echo $imagecover; ?>" alt="image" >
												</div>
												<!--end::Avatar-->
												<!--begin::Name-->
												<a href="#" class="fs-3 text-gray-800 text-hover-primary fw-bold mb-3"><?php echo $fullname; ?></a>
												<!--end::Name-->
												<!--begin::Position-->
												<div class="mb-9">
													<!--begin::Badge-->
													<div class="badge badge-lg badge-light-primary d-inline"><?php echo $this->ortyd->select2_getname($this->session->userdata("group_id"),"users_groups","id","name"); ?></div>
													<!--begin::Badge-->
												</div>
			
											</div>
											<!--end::User Info-->
											<!--end::Summary-->
											<!--begin::Details toggle-->
											<div class="d-flex flex-stack fs-4 py-3">
												<div class="fw-bold rotate collapsible" data-bs-toggle="collapse" href="#kt_user_view_details" role="button" aria-expanded="false" aria-controls="kt_user_view_details">Details
												<span class="ms-2 rotate-180">
													<i class="ki-duotone ki-down fs-3"></i>
												</span></div>
												<span data-bs-toggle="tooltip" data-bs-trigger="hover" title="Edit customer details">
													<a href="#" class="btn btn-sm btn-light-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_update_details">Edit</a>
												</span>
											</div>
											<!--end::Details toggle-->
											<div class="separator"></div>
											<!--begin::Details content-->
											<div id="kt_user_view_details" class="collapse show">
												<div class="pb-5 fs-6">
													<!--begin::Details item-->
													<div class="fw-bold mt-5">Username</div>
													<div class="text-gray-600"><?php echo $this->session->userdata('username'); ?></div>
													<!--begin::Details item-->
													<!--begin::Details item-->
													<div class="fw-bold mt-5">Email</div>
													<div class="text-gray-600">
														<a href="#" class="text-gray-600 text-hover-primary"><?php echo $this->session->userdata('email'); ?></a>
													</div>
													<!--begin::Details item-->
													<!--begin::Details item-->
													<div class="fw-bold mt-5">No Telp</div>
													<div class="text-gray-600"><?php echo $notelp; ?></div>
													<!--begin::Details item-->
													<!--begin::Details item-->
													<div class="fw-bold mt-5">Role</div>
													<div class="text-gray-600"><?php echo $this->ortyd->select2_getname($this->session->userdata("group_id"),"users_groups","id","name"); ?></div>
													<!--begin::Details item-->
													<!--begin::Details item-->
													<div class="fw-bold mt-5">Last Login</div>
													<div class="text-gray-600"><?php echo date('d F Y'); ?></div>
													<!--begin::Details item-->
												</div>
											</div>
											<!--end::Details content-->
										</div>
										<!--end::Card body-->
									</div>
									<!--end::Card-->
									
								</div>
								<!--end::Sidebar-->
								<!--begin::Content-->
								<div class="flex-lg-row-fluid ms-lg-10">
									<!--begin:::Tabs-->
									<ul class="nav nav-custom nav-tabs nav-line-tabs nav-line-tabs-2x border-0 fs-4 fw-semibold mb-8">
										<!--begin:::Tab item-->
										<li class="nav-item">
											<a class="nav-link text-active-primary pb-4 active" data-bs-toggle="tab" href="#kt_user_view_overview_tab">Overview</a>
										</li>
										<!--end:::Tab item-->
										<!--begin:::Tab item-->
										<li class="nav-item ms-auto" style="display:none">
											<!--begin::Action menu-->
											<a href="#" class="btn btn-primary ps-7" data-kt-menu-trigger="click" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">Actions
											<i class="ki-duotone ki-down fs-2 me-0"></i></a>
											<!--begin::Menu-->
											<div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold py-4 w-250px fs-6" data-kt-menu="true">
												<!--begin::Menu item-->
												<div class="menu-item px-5">
													<div class="menu-content text-muted pb-2 px-5 fs-7 text-uppercase">Payments</div>
												</div>
												<!--end::Menu item-->
												
											</div>
											<!--end::Menu-->
											<!--end::Menu-->
										</li>
										<!--end:::Tab item-->
									</ul>
									<!--end:::Tabs-->
									<!--begin:::Tab content-->
									<div class="tab-content" id="myTabContent">
										<!--begin:::Tab pane-->
										<div class="tab-pane fade show active" id="kt_user_view_overview_tab" role="tabpanel">
											<!--begin::Card-->
											<div class="card card-flush mb-6 mb-xl-9">
												<!--begin::Card header-->
												<div class="card-header mt-6">
													<!--begin::Card title-->
													<div class="card-title flex-column">
														<h2 class="mb-1">User's Schedule</h2>
														<div class="fs-6 fw-semibold text-muted">0 upcoming meetings</div>
													</div>
													<!--end::Card title-->
													<!--begin::Card toolbar-->
													<div class="card-toolbar" style="display:none">
														<button type="button" class="btn btn-light-primary btn-sm" data-bs-toggle="modal" data-bs-target="#kt_modal_add_schedule">
														<i class="ki-duotone ki-brush fs-3">
															<span class="path1"></span>
															<span class="path2"></span>
														</i>Add Schedule</button>
													</div>
													<!--end::Card toolbar-->
												</div>
												<!--end::Card header-->
												<!--begin::Card body-->
												<div class="card-body p-9 pt-4">
													<!--begin::Dates-->
													<ul class="nav nav-pills d-flex flex-nowrap hover-scroll-x py-2">
														<!--begin::Date-->
														
														<?php 
														$start = date('Y-m-d');
														$end = date("Y-m-t");

														$this->db->where('db_date >=',$start);
														$this->db->where('db_date <=',$end);
														$query = $this->db->get('master_calendar');
														$query = $query->result_object();
														if($query){
															foreach($query as $rowcalendar){
														
														?>
														<li class="nav-item me-1">
															<a class="nav-link btn d-flex flex-column flex-center rounded-pill min-w-40px me-2 py-4 btn-active-primary <?php if($rowcalendar->day == (int)date('d')){echo ' active '; } ?>" data-bs-toggle="tab" href="#kt_schedule_day_<?php echo $rowcalendar->id; ?>">
																<span class="opacity-50 fs-7 fw-semibold"><?php echo $rowcalendar->day_name; ?></span>
																<span class="fs-6 fw-bolder"><?php echo $rowcalendar->day; ?></span>
															</a>
														</li>
														<!--end::Date-->
														
														<?php }
														}
														?>
													</ul>
													<!--end::Dates-->
													<!--begin::Tab Content-->
													<div class="tab-content">
														
														<?php 
														if($query){
															foreach($query as $rowcalendar){
														
														?>
														<!--begin::Day-->
														<div id="kt_schedule_day_<?php echo $rowcalendar->id; ?>" class="tab-pane fade <?php if($rowcalendar->day == (int)date('d')){echo ' active show '; } ?>">
														
															<!--begin::Time-->
															<div class="d-flex flex-stack position-relative mt-6">
																<!--begin::Bar-->
																<div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
																<!--end::Bar-->
																<!--begin::Info-->
																<div class="fw-semibold ms-5">
																	<!--begin::Time-->
																	<div class="fs-7 mb-1"><?php echo $rowcalendar->day_name; ?>
																	<span class="fs-7 text-muted text-uppercase">-</span></div>
																	<!--end::Time-->
																	<!--begin::Title-->
																	<a href="#" class="fs-5 fw-bold text-dark text-hover-primary mb-2">No Schedule</a>
																	<!--end::Title-->
																	<!--begin::User-->
																	<div class="fs-7 text-muted">Lead by
																	<a href="#">-</a></div>
																	<!--end::User-->
																</div>
																<!--end::Info-->
																<!--begin::Action-->
																<a style="display:none" href="#" class="btn btn-light bnt-active-light-primary btn-sm">View</a>
																<!--end::Action-->
															</div>
															<!--end::Time-->

														</div>
														<!--end::Day-->
														
															<?php }
														} ?>

													</div>
													<!--end::Tab Content-->
												</div>
												<!--end::Card body-->
											</div>
											<!--end::Card-->
										
										</div>
										<!--end:::Tab pane-->
									</div>
									<!--end:::Tab content-->
								</div>
								<!--end::Content-->
							</div>
							<!--end::Layout-->
							<!--begin::Modals-->
							<!--begin::Modal - Update user details-->
							<div class="modal fade" id="kt_modal_update_details" tabindex="-1" aria-hidden="true">
								<!--begin::Modal dialog-->
								<div class="modal-dialog modal-dialog-centered mw-650px">
									<!--begin::Modal content-->
									<div class="modal-content">
									
									<div class="modal-header" id="kt_modal_update_user_header">
												<!--begin::Modal title-->
												<h2 class="fw-bold">Update User Details</h2>
												<!--end::Modal title-->
												<!--begin::Close-->
					
												<!--end::Close-->
											</div>
											<div class="modal-body py-10 px-lg-17">
										<!--begin::Form-->
										<form id="dataForm"  method="POST" action="<?php echo $action; ?>" enctype="multipart/form-data">
							
							
							<div class="col-md-12 ">	
									
									<div class="form-group row" >
										<div class="col-lg-12 col-md-12">
										
											<div class="col-lg-12 col-md-12 form-group">
												<label>Cover/Photo</label><br>
												<div class="dropzone text-center align-items-center" id="coverupload">
													<div class="dz-default dz-message" data-dz-message>
														<h3 class="mb-0"><i class="fa fa-cloud-download"></i></h3><p>Upload Image Cover</p>
													</div>
												</div>
											</div>
											
											<div class="col-lg-12 col-md-12 form-group" style="display:none">
												<label>Role *</label>
												<select class="form-control form-control-sm" name="gid" id="gid" required>
													<?php if($gid != '') { ?>
														<option value="<?php echo $gid; ?>">
															<?php echo $this->ortyd->select2_getname($gid,'users_groups','id','name'); ?>
														</option>
													<?php } ?>
												</select>
											</div>
											
											<div class="col-lg-12 col-md-12 form-group">
												<label>Username *</label>
												 <input type="text" name="username" class="form-control form-control-sm" value="<?php echo $username; ?>" aria-label="taxt rate" required readonly /> 
												
											</div>
										
											
											<div class="col-lg-12 col-md-12 form-group" style="display:none">
												<label>Password </label>
												 <input type="password" name="password" class="form-control form-control-sm" value="<?php echo $password; ?>" aria-label="taxt rate" /> 
												
											</div>
											
											<div class="col-lg-12 col-md-12 form-group" id="user_ref">   
													<label>User Referensi</label>
													<select class="form-control form-control-sm" name="user_id_ref" id="user_id_ref">
														<?php if($user_id_ref != '') { ?>
															<option value="<?php echo $user_id_ref; ?>">
																<?php echo $this->ortyd->select2_getname($user_id_ref,'users_data','id','fullname'); ?>
															</option>
														<?php } ?>
													</select>
											</div>
											
											<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" class="csrf_token" placeholder="CSRF Invalid" required />
											
											<div class="col-lg-12 col-md-12 form-group" style="display:none">   
												<img style="width: 100%;" src="<?php echo $signature; ?>" />
											</div>
	
										</div>
										
										<div class="col-lg-12 col-md-12">
										
											
											<div class="col-lg-12 col-md-12 form-group">
												<label>Fullname *</label>
												 <input type="text" name="fullname" class="form-control form-control-sm" value="<?php echo $fullname; ?>" aria-label="taxt rate" required /> 
												
											</div>
											
												
											<div class="col-lg-12 col-md-12 form-group">
												<label>Email *</label>
												 <input type="email" name="email" class="form-control form-control-sm" value="<?php echo $email; ?>" aria-label="taxt rate" required readonly /> 
												
											</div>
											
											
											<div class="col-lg-12 col-md-12 form-group">
												<label>No. Telp *</label>
												 <input type="text" name="notelp" class="form-control form-control-sm" value="<?php echo $notelp; ?>" aria-label="taxt rate" required /> 
												
											</div>
															
											<div class="col-lg-12 col-md-12 form-group">   
												<label>SIGNATURE</label>
												 <div id="signature-padi" class="m-signature-pad" style="border: 1px solid #000;">
													<div id="anoyaro"> FILL IN SIGNATURE USING MOUSE COURSOR </div>
													<div class="m-signature-pad--body">
													  <canvas style="width:100% !Important;height:300px !Important"></canvas>
													</div>
													<div class="m-signature-pad--footer">
													  <button type="button" class="button clear" data-action="clear">BERSIHKAN</button>
													</div>
													<input type="text" name="signature" id="signature-img" value="<?php echo $signature; ?>" style="display:none"/>
												  </div>
											</div>

											<fieldset class="col-lg-12 col-md-12 form-group" style="display:none">
												<label>Banned</label>
												<div class="form-check">
													<label class="">
													<input type="radio" class="form-check-input" name="banned" value="1" <?php if($banned == 1){echo 'checked="checked"';} ?>>
															<span class="radio-icon fuse-ripple-ready"></span>
															<span>Banned</span>
													</label>
												 </div>
												<div class="form-check">
													<label class="">
													<input type="radio" class="form-check-input" name="banned" value="0" <?php if($banned == 0){echo 'checked="checked"';} ?>>
														<span class="radio-icon fuse-ripple-ready"></span>
														<span>Not Banned</span>
													</label>
												</div>
											 </fieldset>
											
											<fieldset class="col-lg-12 col-md-12 form-group" style="display:none">
												<label>Status</label>
												<div class="form-check">
													<label class="">
													<input type="radio" class="form-check-input" name="active" value="1" <?php if($active == 1){echo 'checked="checked"';} ?>>
															<span class="radio-icon fuse-ripple-ready"></span>
															<span>Active</span>
													</label>
												 </div>
												<div class="form-check">
													<label class="">
													<input type="radio" class="form-check-input" name="active" value="0" <?php if($active == 0){echo 'checked="checked"';} ?>>
														<span class="radio-icon fuse-ripple-ready"></span>
														<span>Not Active</span>
													</label>
												</div>
											 </fieldset>
											 
										</div>
										
									</div>
									
									
									
									
							</div>
			
							
							<div class="card-footer" style="text-align:right;margin-top:20px">

								<button type="button" class="btn btn-primary fuse-ripple-ready pull-right" onClick="save()">
									<i class="fa fa-save"></i> Simpan Data
								</button>
							</div>
							
					</form>
				
										</div>
										<!--end::Form-->
									</div>
								</div>
							</div>
							<!--end::Modal - Update user details-->

						</div>
						<!--end::Post-->
					</div>
					<!--end::Container-->
					




<script>


function save(){
	
	var dataURL = canvas.toDataURL();

	if(dataURL == 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAnAAAACWCAYAAABekbUVAAAHwUlEQVR4Xu3WQQ0AAAwCseHf9HRc0ikgZQ92jgABAgQIECBAICWwVFphCRAgQIAAAQIEzoDzBAQIECBAgACBmIABFytMXAIECBAgQICAAecHCBAgQIAAAQIxAQMuVpi4BAgQIECAAAEDzg8QIECAAAECBGICBlysMHEJECBAgAABAgacHyBAgAABAgQIxAQMuFhh4hIgQIAAAQIEDDg/QIAAAQIECBCICRhwscLEJUCAAAECBAgYcH6AAAECBAgQIBATMOBihYlLgAABAgQIEDDg/AABAgQIECBAICZgwMUKE5cAAQIECBAgYMD5AQIECBAgQIBATMCAixUmLgECBAgQIEDAgPMDBAgQIECAAIGYgAEXK0xcAgQIECBAgIAB5wcIECBAgAABAjEBAy5WmLgECBAgQIAAAQPODxAgQIAAAQIEYgIGXKwwcQkQIECAAAECBpwfIECAAAECBAjEBAy4WGHiEiBAgAABAgQMOD9AgAABAgQIEIgJGHCxwsQlQIAAAQIECBhwfoAAAQIECBAgEBMw4GKFiUuAAAECBAgQMOD8AAECBAgQIEAgJmDAxQoTlwABAgQIECBgwPkBAgQIECBAgEBMwICLFSYuAQIECBAgQMCA8wMECBAgQIAAgZiAARcrTFwCBAgQIECAgAHnBwgQIECAAAECMQEDLlaYuAQIECBAgAABA84PECBAgAABAgRiAgZcrDBxCRAgQIAAAQIGnB8gQIAAAQIECMQEDLhYYeISIECAAAECBAw4P0CAAAECBAgQiAkYcLHCxCVAgAABAgQIGHB+gAABAgQIECAQEzDgYoWJS4AAAQIECBAw4PwAAQIECBAgQCAmYMDFChOXAAECBAgQIGDA+QECBAgQIECAQEzAgIsVJi4BAgQIECBAwIDzAwQIECBAgACBmIABFytMXAIECBAgQICAAecHCBAgQIAAAQIxAQMuVpi4BAgQIECAAAEDzg8QIECAAAECBGICBlysMHEJECBAgAABAgacHyBAgAABAgQIxAQMuFhh4hIgQIAAAQIEDDg/QIAAAQIECBCICRhwscLEJUCAAAECBAgYcH6AAAECBAgQIBATMOBihYlLgAABAgQIEDDg/AABAgQIECBAICZgwMUKE5cAAQIECBAgYMD5AQIECBAgQIBATMCAixUmLgECBAgQIEDAgPMDBAgQIECAAIGYgAEXK0xcAgQIECBAgIAB5wcIECBAgAABAjEBAy5WmLgECBAgQIAAAQPODxAgQIAAAQIEYgIGXKwwcQkQIECAAAECBpwfIECAAAECBAjEBAy4WGHiEiBAgAABAgQMOD9AgAABAgQIEIgJGHCxwsQlQIAAAQIECBhwfoAAAQIECBAgEBMw4GKFiUuAAAECBAgQMOD8AAECBAgQIEAgJmDAxQoTlwABAgQIECBgwPkBAgQIECBAgEBMwICLFSYuAQIECBAgQMCA8wMECBAgQIAAgZiAARcrTFwCBAgQIECAgAHnBwgQIECAAAECMQEDLlaYuAQIECBAgAABA84PECBAgAABAgRiAgZcrDBxCRAgQIAAAQIGnB8gQIAAAQIECMQEDLhYYeISIECAAAECBAw4P0CAAAECBAgQiAkYcLHCxCVAgAABAgQIGHB+gAABAgQIECAQEzDgYoWJS4AAAQIECBAw4PwAAQIECBAgQCAmYMDFChOXAAECBAgQIGDA+QECBAgQIECAQEzAgIsVJi4BAgQIECBAwIDzAwQIECBAgACBmIABFytMXAIECBAgQICAAecHCBAgQIAAAQIxAQMuVpi4BAgQIECAAAEDzg8QIECAAAECBGICBlysMHEJECBAgAABAgacHyBAgAABAgQIxAQMuFhh4hIgQIAAAQIEDDg/QIAAAQIECBCICRhwscLEJUCAAAECBAgYcH6AAAECBAgQIBATMOBihYlLgAABAgQIEDDg/AABAgQIECBAICZgwMUKE5cAAQIECBAgYMD5AQIECBAgQIBATMCAixUmLgECBAgQIEDAgPMDBAgQIECAAIGYgAEXK0xcAgQIECBAgIAB5wcIECBAgAABAjEBAy5WmLgECBAgQIAAAQPODxAgQIAAAQIEYgIGXKwwcQkQIECAAAECBpwfIECAAAECBAjEBAy4WGHiEiBAgAABAgQMOD9AgAABAgQIEIgJGHCxwsQlQIAAAQIECBhwfoAAAQIECBAgEBMw4GKFiUuAAAECBAgQMOD8AAECBAgQIEAgJmDAxQoTlwABAgQIECBgwPkBAgQIECBAgEBMwICLFSYuAQIECBAgQMCA8wMECBAgQIAAgZiAARcrTFwCBAgQIECAgAHnBwgQIECAAAECMQEDLlaYuAQIECBAgAABA84PECBAgAABAgRiAgZcrDBxCRAgQIAAAQIGnB8gQIAAAQIECMQEDLhYYeISIECAAAECBAw4P0CAAAECBAgQiAkYcLHCxCVAgAABAgQIGHB+gAABAgQIECAQEzDgYoWJS4AAAQIECBAw4PwAAQIECBAgQCAmYMDFChOXAAECBAgQIGDA+QECBAgQIECAQEzAgIsVJi4BAgQIECBAwIDzAwQIECBAgACBmIABFytMXAIECBAgQICAAecHCBAgQIAAAQIxAQMuVpi4BAgQIECAAIEHT64AlxItUlEAAAAASUVORK5CYII='){
		//$('#signature-img').val('');
	}else{
		$('#signature-img').val(dataURL);
	}
	
	
	$.ajax({
	  type: 'POST',
	  url: $("#dataForm").attr("action"),
	  data: $("#dataForm").serialize(), 
	  //or your custom data either as object {foo: "bar", ...} or foo=bar&...
	  success: function(response) { 
		var obj = JSON.parse(response);
		if(obj.message == 'success'){
			window.location.href = "<?php echo base_url('users_data/view?message=success');?>"
		}else{
			window.location.href = "<?php echo base_url('users_data/view?message=error');?>"
		}

	  },
	});
}

var wrapper = document.getElementById("signature-padi"),
    clearButton = wrapper.querySelector("[data-action=clear]"),
    saveButton = wrapper.querySelector("[data-action=save]"),
    canvas = wrapper.querySelector("canvas"),
    signaturePad;

// Adjust canvas coordinate space taking into account pixel ratio,
// to make it look crisp on mobile devices.
// This also causes canvas to be cleared.
function resizeCanvas() {
    // When zoomed out to less than 100%, for some very strange reason,
    // some browsers report devicePixelRatio as less than 1
    // and only part of the canvas is cleared then.
    var ratio =  Math.max(window.devicePixelRatio || 1, 1);
    canvas.width = canvas.offsetWidth * ratio;
    canvas.height = canvas.offsetHeight * ratio;
    canvas.getContext("2d").scale(ratio, ratio);
}

window.onresize = resizeCanvas;
resizeCanvas();

signaturePad = new SignaturePad(canvas);

clearButton.addEventListener("click", function (event) {
    signaturePad.clear();
});



	$(document).ready(function() {
		
		$('#area_provinsi_id').prop('disabled', true);
		$('#area_kota_id').prop('disabled', true);
		$('#gid').prop('disabled', true);
		

		$("#area_provinsi_id").select2({	
		ajax: {
			type: "POST",
			url: "<?php echo base_url($headurl.'/select2_area_provinsi'); ?>",
			dataType: 'json',
			delay: 250,
			data: function (params) {
				return {
					q: params.term, // search term
					page: params.page
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
		placeholder: 'Search for a Provinsi'
	}).on("select2:select", function(e) { 
		$('#area_kota_id').val(0).trigger('change');
	})
	
	$("#area_kota_id").select2({	
		ajax: {
			type: "POST",
			url: "<?php echo base_url($headurl.'/select2_area_kota'); ?>",
			dataType: 'json',
			delay: 250,
			data: function (params) {
				return {
					q: params.term, // search term
					provinsi_id: $('#area_provinsi_id').val(),
					page: params.page
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
		placeholder: 'Search for a Kab/Kota'
	}).on("select2:select", function(e) { 

	})
	
	$("#user_id_ref").select2({	
		width : '100%',
		multiple: false,
		ajax: {
			type: "POST",
			url: "<?php echo base_url($headurl.'/select2_user_ref'); ?>",
			dataType: 'json',
			delay: 250,
			data: function (params) {
				return {
					q: params.term, // search term
					kota_id: $('#area_kota_id').val(),
					page: params.page
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
		placeholder: 'Search for a User Referensi'
	}).on("select2:select", function(e) { 
		
	})
		
			$("#gid").select2({
			
				ajax: {
					type: "POST",
					url: "<?php echo base_url($headurl.'/select2_gid'); ?>",
							dataType: 'json',
							delay: 250,
							data: function (params) {
							  return {
								q: params.term, // search term
								page: params.page
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
						  placeholder: 'Search for a Role'
				})
				
	});
	
	
	Dropzone.autoDiscover = false;
	var limit = 0;
					
	var foto_upload_cover= new Dropzone("#coverupload",{
			url: "<?php echo base_url($headurl.'/proses_upload') ?>",
			maxFiles: 1,
			maxFilesize: 1000,
			method:"post",
			acceptedFiles:"image/*",
			createImageThumbnails: true,
			paramName:"userfile",
			dictInvalidFileType:"Type file ini tidak dizinkan",
			addRemoveLinks:true,
			thumbnailWidth:"250",
            thumbnailHeight:"250",
			init: function() {
				this.on("maxfilesexceeded", function(file){
					limit = 1;
				});
				this.on("success", function(file, response) {
					if(limit == 0){
						var obj = $.parseJSON(response)
						//console.log(obj.message);
						$('#dataForm').append('<input style="display:none" type="text" id="cover' + obj.id +'" class="form-control form-control-sm" name="cover" value="' + obj.id +'" />');
					}			
				})
			}
	});
	
	//Event ketika Memulai mengupload
	foto_upload_cover.on("sending",function(a,b,c){
		a.token=Math.random();
		c.append("token_foto",a.token); //Menmpersiapkan token untuk masing masing foto
	});
	
	//Event ketika foto dihapus
	foto_upload_cover.on("removedfile",function(a){
		
		bootbox.confirm({
			message: "Are you sure delete permanently this cover ? ",
			buttons: {
				confirm: {
					label: 'Yes',
					className: 'btn-success'
				},
				cancel: {
					label: 'No',
					className: 'btn-danger'
				}
			},
			callback: function (result) {
				
				if(result == true){
					var token=a.token;	
					$.ajax({
						type:"post",
						data:{token:token,id:<?php echo $iddata; ?>},
						url:"<?php echo base_url($headurl.'/remove_foto') ?>",
						cache:false,
						dataType: 'json',
						success: function(data){
							//console.log(data);
							document.getElementById("cover" + data.id).remove();
						},
						error: function(){
							console.log("Error");
						}
					});
				}else{
					
					
					$.post('<?php echo base_url($headurl.'/getcover'); ?>',{id : <?php echo $iddata; ?>}, function (data) {
						if(data != 'null'){
							var obj = jQuery.parseJSON(data);
							updateCsrfToken(obj.csrf_hash)
							console.log(obj[0].name);
							for (var key in obj) {
								var mockFile = { name: obj[key].name, size: obj[key].size, token: obj[key].token };
								foto_upload_cover.options.addedfile.call(foto_upload_cover, mockFile);
								foto_upload_cover.options.thumbnail.call(foto_upload_cover, mockFile, "<?php echo base_url(); ?>" + obj[key].path);
								foto_upload_cover.emit("complete", mockFile);
							}
						}
					});
	
				}
				
		
			}
		});


	});
	
	
	
	$.post('<?php echo base_url($headurl.'/getcover'); ?>',{id : <?php echo $iddata; ?>, <?php echo $this->security->get_csrf_token_name(); ?> : csrfHash  }, function (data) {
						
		if(data != 'null'){
			var obj = jQuery.parseJSON(data);
			updateCsrfToken(obj.csrf_hash)
			console.log(obj[0].name);
			for (var key in obj) {
				var mockFile = { name: obj[key].name, size: obj[key].size, token: obj[key].token };
				foto_upload_cover.options.addedfile.call(foto_upload_cover, mockFile);
				foto_upload_cover.options.thumbnail.call(foto_upload_cover, mockFile, "<?php echo base_url(); ?>" + obj[key].path);
				foto_upload_cover.emit("complete", mockFile);
			}
		}
	});
	
</script>