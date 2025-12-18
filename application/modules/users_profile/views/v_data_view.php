
	<style>
		/* Styling untuk container Upload */
.upload-container {
    text-align: center;
    margin: 30px 0;
}

.dropzone {
    background-color: #f7f7f7;
    border: 2px dashed #ddd;
    border-radius: 8px;
    padding: 30px;
    display: inline-block;
    width: 100%;
    max-width: 400px;
    margin: 0 auto;
    cursor: pointer;
    transition: background-color 0.3s ease, border-color 0.3s ease;
}

.dropzone:hover {
    background-color: #f0f0f0;
    border-color: #007bff;
}

.dz-message {
    font-size: 16px;
    color: #666;
}

.dz-message h5 {
    font-weight: 600;
    color: #333;
    margin: 10px 0;
}

.dz-message p {
    font-size: 14px;
    color: #888;
}

.upload-icon {
    font-size: 50px;
    color: #007bff;
    margin-bottom: 20px;
}

.dz-preview {
    margin: 10px 0;
    display: flex;
    justify-content: center;
}

.dz-preview .dz-image {
    border-radius: 8px;
}

.dz-remove {
    background-color: #ff3860;
    border: none;
    color: white;
    font-size: 14px;
    padding: 5px 10px;
    border-radius: 4px;
    cursor: pointer;
    margin-top: 10px;
    transition: background-color 0.3s ease;
}

.dz-remove:hover {
    background-color: #e72b4f;
}

.dz-error-mark {
    font-size: 20px;
    color: #ff3860;
}

.dz-success-mark {
    font-size: 20px;
    color: #28a745;
}

@media (min-width: 768px) {
	#floating-save-button {
		position: fixed;
		top: 150px;
		right: 50px;
		z-index: 1;
		background: white;
		padding: 10px;
		border-radius: 10px;
		box-shadow: 0 2px 6px rgba(0,0,0,0.15);
	}
}

	</style>
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
						$themes_id = $rows->themes_id;
						$sidebar = $rows->sidebar;
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
																		$this->db->where('users_data.id',$this->session->userdata('userid'));
																		$this->db->join('data_gallery','data_gallery.id = users_data.cover', 'left');
																		$queryimage = $this->db->get('users_data');
																		$queryimage = $queryimage->result_object();
																		if($queryimage){
																			$relative_path = $queryimage[0]->path;
																			$full_path = FCPATH . $relative_path;

																			if (file_exists($full_path)) {
																				$imagecover = base_url() . $relative_path;
																			} else {
																				$imagecover = base_url() . 'themes/ortyd/assets/media/avatars/blank.png';
																			}
																		}else{
																			$imagecover = base_url().'themes/ortyd/assets/media/avatars/blank.png';
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
												
												<?php $logged_in = $this->session->userdata('google_id');
												if ( $logged_in != null && $logged_in != '') { ?>
												
												<!--begin::Login options-->
												<div class="row g-3 mb-9">
													<!--begin::Col-->
													<div class="col-md-12">
														<!--begin::Google link=-->
														<a href="<?php echo $googlelink; ?>" class="btn btn-flex btn-outline btn-text-gray-700 btn-active-color-primary bg-state-light flex-center text-nowrap w-100">
														<img alt="Logo" src="<?php echo base_url(); ?>themes/ortyd/assets/media/svg/brand-logos/google-icon.svg" class="h-15px me-3" />Remove Google Connect</a>
														<!--end::Google link=-->
													</div>
													<!--end::Col-->
													<!--begin::Col-->
													
												</div>
												<!--end::Login options-->
													
												<?php }else{ ?>
												<!--begin::Login options-->
												<div class="row g-3 mb-9">
													<!--begin::Col-->
													<div class="col-md-12">
														<!--begin::Google link=-->
														<a href="<?php echo $googlelink; ?>" class="btn btn-flex btn-outline btn-text-gray-700 btn-active-color-primary bg-state-light flex-center text-nowrap w-100">
														<img alt="Logo" src="<?php echo base_url(); ?>themes/ortyd/assets/media/svg/brand-logos/google-icon.svg" class="h-15px me-3" />Connect Google</a>
														<!--end::Google link=-->
													</div>
													<!--end::Col-->
													<!--begin::Col-->
													
												</div>
												<!--end::Login options-->
												<?php } ?>
			
											</div>
											<!--end::User Info-->
											<!--end::Summary-->
											<!--begin::Details toggle-->
											<div class="d-flex flex-stack fs-4 py-3">
												<div class="fw-bold rotate collapsible" data-bs-toggle="collapse" href="#kt_user_view_details" role="button" aria-expanded="false" aria-controls="kt_user_view_details">Details
												<span class="ms-2 rotate-180">
													<i class="ki-duotone ki-down fs-3"></i>
												</span></div>

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
														<a href="mailto:<?php echo $this->session->userdata('email'); ?>" class="text-gray-600 text-hover-primary"><?php echo $this->session->userdata('email'); ?></a>
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
													<?php if ( $logged_in != null && $logged_in != '') { ?>
													<div class="fw-bold mt-5">Google Account</div>
													<div class="text-gray-600">
														<a href="mailto:<?php echo $this->session->userdata('google_email'); ?>" class="text-gray-600 text-hover-primary"><?php echo $this->session->userdata('google_email'); ?></a>
													</div>
													<?php } ?>
													<div class="fw-bold mt-5">Signature</div>
													<div class="text-gray-600">
														<img style="width: 100%;" src="<?php echo 'data:image/png;base64,'.$signature; ?>" />
													</div>
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
											<a class="nav-link text-active-primary pb-4 active" data-bs-toggle="tab" href="#kt_user_edit_overview_tab">Overview</a>
										</li>
										<li class="nav-item">
											<a class="nav-link text-primary pb-4 " data-bs-toggle="tab" href="#kt_user_view_overview_tab">Schedule</a>
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
											<div class="tab-pane fade show active" id="kt_user_edit_overview_tab" role="tabpanel">
											<!--begin::Form-->
											
												<div class="card card-flush mb-6 mb-xl-9">
												
												
													<!--begin::Card header-->
													<div class="card-header mt-6">
														<!--begin::Card title-->
														<div class="card-title flex-column">
															<h2 class="mb-1">User's Update</h2>
														</div>
														<!--end::Card title-->

													</div>
												
												<div class="card-body p-9 pt-4">
													<form id="dataForm"  method="POST" action="<?php echo $action; ?>" enctype="multipart/form-data">
							
							
											<div class="col-md-12 ">	
													
													<div class="form-group row" >
														<div class="col-lg-12 col-md-12">
															
															<div class="col-lg-12 col-md-12 form-group">
																<label>Cover/Photo</label><br>
																<div id="coverupload" class="dropzone">
																	<!-- Dropzone area -->
																</div>
															</div>



															
															<div class="col-lg-12 col-md-12 form-group mt-5" style="display:none">
																<label>Role *</label>
																<select class="form-control form-control-sm input-readonly" name="gid" id="gid" required readonly>
																	<?php if($gid != '') { ?>
																		<option value="<?php echo $gid; ?>">
																			<?php echo $this->ortyd->select2_getname($gid,'users_groups','id','name'); ?>
																		</option>
																	<?php } ?>
																</select>
															</div>
															
															<div class="col-lg-12 col-md-12 form-group mt-5">
																<label>Username <span style="color: red;">*</span></label>
																 <input type="text" name="username" class="form-control form-control-sm input-readonly" value="<?php echo $username; ?>" aria-label="taxt rate" required readonly /> 
																
															</div>
															
															<div class="col-lg-12 col-md-12 form-group mt-5">
																<label>Email <span style="color: red;">*</span></label>
																 <input type="email" name="email" class="form-control form-control-sm input-readonly" value="<?php echo $email; ?>" aria-label="taxt rate" required readonly /> 
																
															</div>
															
														
															
															<div class="col-lg-12 col-md-12 form-group mt-5" style="display:none">
																<label>Password </label>
																 <input type="password" name="password" class="form-control form-control-sm" value="<?php echo $password; ?>" aria-label="taxt rate" /> 
																
															</div>

															<div class="col-lg-12 col-md-12 form-group mt-5" id="user_ref" style="display:none">   
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
														
															
															<div class="col-lg-12 col-md-12 form-group mt-5">
																<label>Fullname <span style="color: red;">*</span></label>
																 <input type="text" name="fullname" class="form-control form-control-sm" value="<?php echo $fullname; ?>" aria-label="taxt rate" required /> 
																
															</div>
															
															<div class="col-lg-12 col-md-12 form-group mt-5">
																<label>No. Telp <span style="color: red;">*</span></label>
																 <input type="number" name="notelp" class="form-control form-control-sm" value="<?php echo $notelp; ?>" aria-label="taxt rate" required /> 
																
															</div>
															
															<div class="col-lg-12 col-md-12 form-group mt-5">
																<label>Themes</label>
																<select class="form-control form-control-sm" name="themes_id" id="themes_id" >
																	<?php if($themes_id != '') { ?>
																		<option value="<?php echo $themes_id; ?>">
																			<?php echo $this->ortyd->select2_getname($themes_id,'master_themes','id','name'); ?>
																		</option>
																	<?php } ?>
																</select>
															</div>
															
															<div class="col-lg-12 col-md-12 form-group mt-5">
																<label>Sidebar Menu</label>
																<select class="form-control form-control-sm" name="sidebar" id="sidebar" >
																	<?php if($themes_id != '') { ?>
																		<option value="<?php echo $sidebar; ?>">
																			<?php echo $this->ortyd->select2_getname($sidebar,'master_status_option','id','name'); ?>
																		</option>
																	<?php } ?>
																</select>
															</div>
																			
															<style>
																.signature-wrapper {
																	border: 1px solid #ddd;
																	border-radius: 10px;
																	padding: 15px;
																	background: #f9f9f9;
																	margin-bottom: 20px;
																}

																.signature-wrapper h5 {
																	font-weight: 600;
																	margin-bottom: 10px;
																}

																.m-signature-pad--body {
																	background-color: #fff;
																	border: 1px dashed #ccc;
																	border-radius: 6px;
																	padding: 10px;
																}

																#canvas {
																	width: 100% !important;
																	height: 250px !important;
																	border-radius: 4px;
																	cursor: crosshair;
																}

																.m-signature-pad--footer {
																	text-align: right;
																	margin-top: 10px;
																}

																.m-signature-pad--footer .clear {
																	background: #e74c3c;
																	border: none;
																	color: #fff;
																	padding: 6px 12px;
																	border-radius: 4px;
																	font-size: 14px;
																	cursor: pointer;
																}

																.m-signature-pad--footer .clear:hover {
																	background: #c0392b;
																}
															</style>

															<div class="col-lg-12 col-md-12 form-group signature-wrapper" style="margin-top:20px">
																<h5>SIGNATURE</h5>
																<div id="signature-padi" class="m-signature-pad">
																	<div class="m-signature-pad--body">
																		<canvas id="canvas"></canvas>
																	</div>
																	<div class="m-signature-pad--footer">
																		<button type="button" class="button btn btn-sm  clear" data-action="clear">BERSIHKAN</button>
																		<button type="button" class="button btn btn-sm btn-primary" onClick="saveTTD()">SIMPAN TTD</button>
																	</div>
																	<input type="hidden" name="signature" id="signature-img" value="<?php echo $signature; ?>" />
																</div>
															</div>

															<div class="col-lg-12 col-md-12 form-group signature-wrapper">
																<h5>Atau Upload File Tanda Tangan</h5>
																<input type="file" name="ttd_file" id="fileInput" class="form-control form-control-sm" accept="image/png" />
																<textarea style="display:none" id="base64Output" rows="10" cols="80" readonly></textarea>
																<hr>
																<button type="button" class="button btn btn-sm btn-primary" onClick="saveTTD()">SIMPAN TTD</button>
															</div>

															
															<script>
																const fileInput = document.getElementById('fileInput');
																const base64Output = document.getElementById('base64Output');

																fileInput.addEventListener('change', function () {
																  const file = this.files[0];
																  
																  if (!file) {
																	base64Output.value = '';
																	return;
																  }

																  // Validasi apakah file adalah PNG
																  if (file.type !== 'image/png') {
																	alert('Hanya file PNG yang diperbolehkan!');
																	this.value = '';
																	base64Output.value = '';
																	return;
																  }

																  const reader = new FileReader();
																  reader.onload = function () {
																	base64Output.value = reader.result;
																  };
																  reader.readAsDataURL(file); // ini akan menghasilkan base64 dengan prefix data:image/png;base64,...
																});
															  </script>

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
							
											<div id="floating-save-button">
	<button type="button" class="btn btn-primary btn-sm" onClick="save()">
		<i class="fa fa-save"></i> Simpan Data
	</button>
</div>
											
									</form>
				
												</div>
											</div>
										</div>
									
									
									
										<div class="tab-pane fade " id="kt_user_view_overview_tab" role="tabpanel">
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
							<!--begin::Modal - Update user details--

						</div>
						<!--end::Post-->
					</div>
					<!--end::Container-->
					

			</div>



<script>
document.addEventListener('DOMContentLoaded', function () {
    const urlParams = new URLSearchParams(window.location.search);
    const message = urlParams.get('message');

    if (message === 'errorgoogle2' || message === 'errorgoogle3') {
        Swal.fire({
            icon: 'error',
            title: 'Gagal Terkoneksi ke Google',
            text: 'Terjadi kesalahan saat menghubungkan dengan layanan Google. Silakan coba lagi nanti.',
        });
    }else if (message === 'errorgoogle1') {
        Swal.fire({
            icon: 'error',
            title: 'Email telah digunakan oleh user lain atau email tidak terdaftar',
            text: 'Terjadi kesalahan saat menghubungkan dengan layanan Google. Silakan coba lagi nanti.',
        });
    }
});
</script>

<script>



function save(){
	$.ajax({
		type: 'POST',
		url: $("#dataForm").attr("action"),
		data: $("#dataForm").serialize(), 
		success: function(response) { 
			var obj = JSON.parse(response);
			if(obj.message == 'success'){
				// Menampilkan SweetAlert sebelum reload
				Swal.fire({
					title: 'Berhasil!',
					text: 'Data berhasil disimpan.',
					icon: 'success',
					confirmButtonText: 'OK'
				}).then(function() {
					// Setelah tombol OK di klik, halaman akan reload
					window.location.href = "<?php echo base_url('users_profile/view?message=success');?>";
				});
			} else {
				// Menampilkan SweetAlert untuk error
				Swal.fire({
					title: 'Gagal!',
					text: 'Terjadi kesalahan saat menyimpan data.',
					icon: 'error',
					confirmButtonText: 'Coba Lagi'
				}).then(function() {
					// Setelah tombol OK di klik, halaman akan reload
					window.location.href = "<?php echo base_url('users_profile/view?message=error');?>";
				});
			}
		},
	});

}



function saveTTD(){
	
	if($('#base64Output').val() != ''){
		var dataURL = $('#base64Output').val();
	}else{
		var dataURL = canvas.toDataURL();
	}
	

	console.log(dataURL)
	
	if(dataURL == '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Tlt7bGXuOmf1jupsY8S/UL1rZaIXKAECBAgQIECAAIEVElCIWKHFEioBAgQIECBAgACBXRT4qep91QWr71fDcU3rdL2xutqY0N9Vl1qn5ORCgAABAgQIECBAYJkEFCKWaTXEQoAAAQIECBAgQGC5BF5bXWcMaeidMPRQWIfrDrPm1I+qjhyTGXZFfHEdEpMDAQIECBAgQIAAgWUUUIhYxlUREwECBAgQIECAAIHlELjMrBDxoTGUV1Q3WY6wdhTFxas/q849jvK/qqfsaEQPEyBAgAABAgQIECBwQAGFCC8IAQIECBAgQIAAAQIHEvhAddnxhnX498OTqruO+Xy0GnpDfNMrQIAAAQIECBAgQIDA4gTW4R8Si9MxMgECBAgQIECAAAECD6nuOzL8VjV8kL+q162r524I/lerV65qMuImQIAAAQIECBAgsCoCChGrslLiJECAAAECBAgQILA3Ales3jVO/cHqcnsTxo5nPWf19urs40gPrh6w41ENQIAAAQIECBAgQIDAQQUUIg5K5AYCBAgQIECAAAECkxd4y+xD/KuOCueqPreCIkOPixuPcX+qulL11RXMQ8gECBAgQIAAAQIEVk5AIWLllkzABAgQIECAAAECBHZd4KbVk2fHMh1dPbS6365HsLMJb1a9ZBzi5OreYz47G9XTBAgQIECAAAECBAhsSUAhYktMbiJAgAABAgQIECAwaYFzbNgFMeyGGHZFrMp1ePWdDcG+qbr6qgQvTgIECBAgQIAAAQLrIKAQsQ6rKAcCBAgQIECAAAECixfY2LT6mOp9i59yLjO8t7rCONLXqjPPZVSDECBAgAABAgQIECCwZQGFiC1TuZEAAQIECBAgQIDApAWOne2EeG511uo51W1XQOP3qj/eEOevVS9agbiFSIAAAQIECBAgQGCtBBQi1mo5JUOAAAECBAgQIEBgYQKnqYYdBUdU36rOU520sNl2PvClq9dWPzMO9RfVdXY+rBEIECBAgAABAgQIENiugELEdsXcT4AAAQIECBAgQGC6Arced0UMArfZ8PdlExmKJS+urj0GNjSovvhsF8c/L1ug4iFAgAABAgQIECAwBQGFiCmsshwJECBAgAABAgQIzEfgAtWfVReq/ma24+Ay8xl27qPcpTp+w6hDAeX5c5/FgAQIECBAgAABAgQIbElAIWJLTG4iQIAAAQIE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IECAAAECBAgQ2JbAs6sbVLeuXrvJk8dtaEj90eqi2xrdzQQIECBAgAABAgQILL2AQsTSL5EACRAgQIAAAQIECKy8wPHVLcdixGs2ZPO46nc3fD30kXj4ymcrAQIECBAgQIAAAQIE/ouAQoQXggABAgQIECBAgACBRQv8Q3Wx8Wim4YimU68vVmcdv7hb9eRFB2J8AgQIECBAgAABAgR2X0AhYvfNzUiAAAECBAgQIEBgagJXnjWjvsusV8TzqtdVR1TPrY6dNbE+XfXtaugV8c9Tg5EvAQIECBAgQIAAgSkIKERMYZXlSIAAAQIECBAgQGDvBV5dXW8WxgPHhtQvHkM6qTrD3ocnAgIECBAgQIAAAQIEFiWgELEoWeMSIECAAAECBAgQILBR4NRCxNOq01a/Pv7wTdXVUREgQIAAAQIECBAgsL4CChHru7YyI0CAAAECBAgQILBMAj9ZXa16SvUzY2BPrY6rvrNMgYqFAAECBAgQIECAAIH5CihEzNfTaAQIECBAgAABAgQI7F/gjWMx4tQ7rlW9HhgBAgQIECBAgAABAustoBCx3usrOwIECBAgQIAAAQLLInCj6pVjMN+rHlQ9elmCEwcBAgQIECBAgAABAosTUIhYnK2RCRAgQIAAAQIECBD4T4F7Vo8YvzyxutOGwgQnAgQIECBAgAABAgTWWEAhYo0XV2oECBAgQIAAAQIElkTgT6rfGmM5uTpd9dbql5YkPmEQIECAAAECBAgQILBAAYWIBeIamgABAgQIECBAgACBzll9doPD9avbV8+zI8LbQYAAAQIECBAgQGAaAgoR01hnWRIgQIAAAQIECBDYC4HDqltWj69OW323On113erZ1VOq4cgmFwECBAgQIECAAAECayygELHGiys1AgQIECBAgAABAnss8GfVDcYYXlDde7Yb4gvVg6v7Ve+sfnGPYzQ9AQIECBAgQIAAAQILFlCIWDCw4QkQIECAAAECBAhMWOBj1YXG/B9dHTf+/czV3aszjbsl/n7CRlInQIAAAQIECBAgsPYCChFrv8QSJECAAAECBAgQILDrApep3lYdPs78ruo3q09uiOSB1QOq91RX3PUITUiAAAECBAgQIECAwK4JKETsGrWJCBAgQIAAAQIECExG4EHV/Tdke3R14j7Z37AajmsaChFDz4h/nYyORAkQIECAAAECBAhMTEAhYmILLl0CBAgQIECAAAECCxZ4cXXzDXP8Y3WB/cw59IkY+kX8aXXjBcdleAIECBAgQIAAAQIE9khAIWKP4E1LgAABAgQIECBAYA0Fjpj1fjh5Q17DkUzXr07aT673rR6iELGGb4KUCBAgQIAAAQIECGwQUIjwOhAgQIAAAQIECBAgMA+BS419IU63YbChD8RwTNOBrqdVdxibVz9uHoEYgwABAgQIECBAgACB5RJQiFiu9RANAQIECBAgQIAAgVUVeHh1rw3BDwWG+1TfOEhCTx8bWb90nyOdVtVB3AQIECBAgAABAgQI7COgEOGVIECAAAECBAgQIEBgpwKPqu6xzyDnrj67hYHPVQ3FiK9Wj68+uIVn3EKAAAECBAgQIECAwAoJKESs0GIJlQABAgQIECBAgMCSCgx9IYb+EKdev1s9YRuxvrk6tvqT6ne28ZxbCRAgQIAAAQIECBBYAQGFiBVYJCESIECAAAECBAgQWGKBT1QX2BDf8PWFthnvjaunVh+YNbb+7erT23ze7QQIECBAgAABAgQILLGAQsQSL47QCBAgQIAAAQIECCy5wK9Ur9sQ49eqG1XvPoS4P1edo9rubopDmMojBAgQIECAAAECBAjspoBCxG5qm4sAAQIECBAgQIDA+gjcrHrJPumcUN35EFMcnntK9YJZg+u7Vd8+xHE8RoAAAQIECBAgQIDAkgkoRCzZggiHAAECBAgQIECAwAoIHFM9ubrUhljfVd2i+uIO4j9lfPYa1Rt3MI5HCRAgQIAAAQIECBBYIgGFiCVaDKEQIECAAAECBAgQWBGBV1fX2yfWW1fP32H8j63uXj2iuvcOx/I4AQIECBAgQIAAAQJLIqAQsSQLIQwCBAgQIECAAAECKyLwoOr++8T6vOou1fd3mMOVqneOY5xxdkTTN3Y4nscJECBAgAABAgQIEFgCAYWIJVgEIRAgQIAAAQIECBBYEYGLV2+vjtwn3vNX/zSHHE47Nr++cvWEsXH1HIY1BAECBAgQIECAAAECeymgELGX+uYmQIAAAQIECBAgsFoCX5v1hhh2Kmy8hqbVL5tjGr9V/Un1j9UF5jiuoQgQIECAAAECBAgQ2CMBhYg9gjctAQIECBAgQIAAgRUTeE113X1i/mR1wTnncVT1htnOiMtVj6nuMefxDUeAAAECBAgQIECAwC4LKETsMrjpCBAgQIAAAQIECKygwP2qB28S95mrYZfEvK9nVLevhqbYvzYrRnxv3hMYjwABAgQIECBAgACB3RNQiNg9azMRIECAAAECBAgQWEWBs1Zf3CTwoVDwrAUm9J3ZjojDx0LEixY4j6EJECBAgAABAgQIEFiwgELEgoENT4AAAQIECBAgQGDFBU6ujtgnh49UF1twXn9a3bD6u9nOiEsteC7DEyBAgAABAgQIECCwQAGFiAXiGpoAAQIECBAgQIDAigsMTaOH5tH7XsMuiS8tOLeh+PA34xyLOgJqwSkYngABAgQIECBAgACBQUAhwntAgAABAgQIECBAgMBmAmGarMYAACAASURBVHesTtjkB8P3n75LZM+rblW9ubraLs1pGgIECBAgQIAAAQIE5iygEDFnUMMRIECAAAECBAgQWAOBC1Uf2ySPz1bn3sX87lY9sfpKdY3qH3ZxblMRIECAAAECBAgQIDAnAYWIOUEahgABAgQIECBAgMAaCbytuvIm+QxFiKEYsZvXcDzTcEzTQ6r77+bE5iJAgAABAgQIECBAYD4CChHzcTQKAQIECBAgQIAAgXURuO/4of+++dx5P0c1LTrvO1VPHSfZi0LIovMzPgECBAgQIECAAIG1F1CIWPslliABAgQIECBAgACBLQvcpTp+k7tPnPVpuFL1iS2PNL8bj6hOHod7cXXL+Q1tJAIECBAgQIAAAQIEdkNAIWI3lM1BgAABAgQIECBAYPkFzlF9bj9h3qF6xh6mcJvq2dW3qqF/xdAzwkWAAAECBAgQIECAwIoIKESsyEIJkwABAgQIECBAgMCCBT5SXWSTOd5UXX3Bcx9s+COrd1YXrR432yFx94M94OcECBAgQIAAAQIECCyPgELE8qyFSAgQIECAAAECBAjslcDTqmHXw77XSdXFqi/tVWAb5n3Q2Kz6i9Ux1fCniwABAgQIECBAgACBFRBQiFiBRRIiAQIECBAgQIAAgQUK3Kh6fnXaTeYY+jEMfRmW4drYK+Kx1R8sQ1BiIECAAAECBAgQIEDg4AIKEQc3cgcBAgQIECBAgACBdRUY+kK8uTrvJgm+qrrhkiV+3+oh1feqw5csNuEQIECAAAECBAgQILAfAYUIrwYBAgQIECBAgACB6Qq8shp2ROx7DU2hh+LEiUtGc7bqrbMdHOfRK2LJVkY4BAgQIECAAAECBA4goBDh9SBAgAABAgQIECAwTYG7VMfvJ/VrVa9fUpZnVbetvjErSly5+tiSxiksAgQIECBAgAABAgRGAYUIrwIBAgQIECBAgACB6QlcvnrfftJ++qw59R2XnOSUMb7nVb+x5LEKjwABAgQIECBAgMDkBRQiJv8KACBAgAABAgQIEJiYwNHVO6sLbpL3x6tjqm8vucndqieOMR5WfX/J4xUeAQIECBAgQIAAgUkLKERMevklT4AAAQIECBAgMEGBF1a33E/eV63etgImp6/eXl109t8rqpusQMxCJECAAAECBAgQIDBZAYWIyS69xAkQIECAAAECBCYocO3qz/eT90Or+62QyUOq+1bfqW5cvWmFYhcqAQIECBAgQIAAgUkJKERMarklS4AAAQIECBAgMGGBs1fD0Uun3cRgKE5cdwVtPlpduHp2dbsVjF/IBAgQIECAAAECBCYhoBAxiWWWJAECBAgQIECAwMQFDq8+uJ++EJ+aHXM0HMn0xRU0GoonrxnjvnT1tyuYg5AJECBAgAABAgQIrL2AQsTaL7EECRAgQIAAAQIECPSg6v77cbhW9foVNjp1V8SXqrOucB5CJ0CAAAECBAgQILC2AgoRa7u0EiNAgAABAgQIECDwQ4ErVe/cj8VQgBgKEat8bex7cf0NOyRWOSexEyBAgAABAgQIEFgrAYWItVpOyRAgQIAAAQIECBD4LwLDDoEPVWfZxOWvq1+svr8GZs+pfmMsuAw5uQgQIECAAAECBAgQWCIBhYglWgyhECBAgAABAgQIEJizwJ9Uv7XJmF8fixCfmPN8ezXcFatXVUePR1A9ZK8CMS8BAgQIECBAgAABAj8qoBDhrSBAgAABAgQIECCwngJ3q564n9SGfhHr9mH9cMzUNWdHTX2guln1ufVcVlkRIECAAAECBAgQWD0BhYjVWzMREyBAgAABAgQIEDiYwAWrN1Tn2OTG11bXO9gAK/jz0852f5xYnaa6c3XCCuYgZAIECBAgQIAAAQJrKaAQsZbLKikCBAgQIECAAIGJC7y8+tVNDD427hr4wpr6PLi635jbJau/X9M8pUWAAAECBAgQIEBgpQQUIlZquQRLgAABAgQIECBA4KACwwfxwwfym103rYYixbpeP1V9a0xuOJrqyeuaqLwIECBAgAABAgQIrJKAQsQqrZZYCRAgQIAAAQIECBxY4KerT1ZHbHLbI6t7TgDwxtUrxjzPX/3TBHKWIgECBAgQIECAAIGlFlCIWOrlERwBAgQIECBAgACBLQscPhYhfmaTJ/62unb15S2Ptro3nmmW6wers1d/Vf3y6qYicgIECBAgQIAAAQLrIaAQsR7rKAsCBAgQIECAAAECw46H4/bDcGz1lgkR3ah65ZjvsEvkKxPKXaoECBAgQIAAAQIElk5AIWLplkRABAgQIECAAAECBLYtcIfqaft56u7V47Y94mo/cHT1suqq1UnVGVY7HdETIECAAAECBAgQWG0BhYjVXj/REyBAgAABAgQIEBiOYnpKdb1NKD5bnXuiRDerXjLmPvTM+O5EHaRNgAABAgQIECBAYM8FFCL2fAkEQIAAAQIECBAgQGBHAu+vLrfJCJ+pLl99Y0ejr+7Dp60eXw27Rb5TnW51UxE5AQIECBAgQIAAgdUWUIhY7fUTPQECBAgQIECAwLQFhkLD+/ZDcNvqOdPm6VrVX4wGV5kd1fT2iXtInwABAgQIECBAgMCeCChE7Am7SQkQIECAAAECBAjsWOBK1Tv3M8ofVI/d8QzrMcBvV08YUzlT9fX1SEsWBAgQIECAAAECBFZHQCFiddZKpAQIECBAgAABAgQ2Cjyjuv0mJF+uLlV9FdcPBS5c/XV1mupW1Qu4ECBAgAABAgQIECCwuwIKEbvrbTYCBAgQIECAAAEC8xB4YXXL/Qw09EIYeiK4/lPgOtVrxy8vVH0CDgECBAgQIECAAAECuyegELF71mYiQIAAAQIECBAgMA+Bo6rP7af58uvrh30RXP9V4GfH4sNh1T2qxwAiQIAAAQIECBAgQGD3BBQids/aTAQIECBAgAABAgR2KnCu6oPVGfYZ6PvVcFTTvarh764fFbhI9ZHx21ev3gSJAAECBAgQIECAAIHdEVCI2B1nsxAgQIAAAQIECBCYh8ADqwfsZ6BLV387j0nWdIxhJ8lJY253q568pnlKiwABAgQIECBAgMDSCShELN2SCIgAAQIECBAgQIDApgLDb/E/rTrHPj/90qwAcf3qQ9wOKvDTs94a/zg7munwWTPvu1RPPegTbiBAgAABAgQIECBAYMcCChE7JjQAAQIECBAgQIAAgYULnGn2wflX9zPLX1c/t/AI1meCL88KEGep7lodvz5pyYQAAQIECBAgQIDA8gooRCzv2oiMAAECBAgQIECAwKkCN6tesgnHsBviptW7UW1ZYOiz8RfV2atHVA/b8pNuJECAAAECBAgQIEDgkAQUIg6JzUMECBAgQIAAAQIEdk3g16oXbDLbydUlqs/tWiTrM9EbZ8Wbq1WvmzX/vvb6pCUTAgQIECBAgAABAsspoBCxnOsiKgIECBAgQIAAAQKnCrymuu4mHG+ofgXTIQlcZmxWfXT1nOqhhzSKhwgQIECAAAECBAgQ2JKAQsSWmNxEgAABAgQIECBAYE8ETtnPrMNv9F9jTyJan0nvveFYpp+s/nV9UpMJAQIECBAgQIAAgeUSUIhYrvUQDQECBAgQIECAAIFB4Kjql6uX7Ydj6HPgSKadvSvD0UxD342hz8ZNqk/sbDhPEyBAgAABAgQIECCwPwGFCO8GAQIECBAgQIAAgeUTOK565H7CGppTv3z5Ql7JiA6rvlMN/y66cvWOlcxC0AQIECBAgAABAgSWXEAhYskXSHgECBAgQIAAAQKTE3hgdYfqZzbJ/LXV9SYnsriEz119ehz+5tVLFzeVkQkQIECAAAECBAhMV0AhYrprL3MCBAgQIECAAIHlE7h69Zf7CesL1bNmPxsKFa75CdyoukT1verPq4/Nb2gjESBAgAABAgQIECAwCChEeA8IECBAgAABAgQILIfAVWbHA731AKH8VvWk5Qh17aIY3Af/vxp7c6xdghIiQIAAAQIECBAgsJcCChF7qW9uAgQIECBAgAABAv8p8KHqMvsBOb66K6yFCTxm1ivi98e+HPdc2CwGJkCAAAECBAgQIDBRAYWIiS68tAkQIECAAAECBJZK4ITqjgeI6LrjsUFLFfSaBTMYD0dffaW62JrlJh0CBAgQIECAAAECeyqgELGn/CYnQIAAAQIECBAg8MOeDw84gMODZj/TF2LxL8rtqmeO01y4+vjipzQDAQIECBAgQIAAgWkIKERMY51lSYAAAQIECBAgsJwCV6veqAixFItz1urx1aXGP59XnbwUkQmCAAECBAgQIECAwIoLKESs+AIKnwABAgQIECBAYKUFTjlI9FedNVF+20pnuFrBn6H6xhjy3aonr1b4oiVAgAABAgQIECCwnAIKEcu5LqIiQIAAAQIECBBYf4GXVTc5QJp3qp62/gxLl+GnqvNUv1s9YemiExABAgQIECBAgACBFRRQiFjBRRMyAQIECBAgQIDAygscrC/Eq6obrnyWq5vAF6qfrR5T3WN10xA5AQIECBAgQIAAgeUQUIhYjnUQBQECBAgQIECAwHQELlC9qzp6PykPRzHduho+DHftjcBfV5cej8UajsdyESBAgAABAgQIECCwAwGFiB3geZQAAQIECBAgQIDAIQgcbDfELaqXHMK4HpmfwGmrd1SXqV5a3Xx+QxuJAAECBAgQIECAwPQEFCKmt+YyJkCAAAECBAgQ2DuBO1YnHGD6oSfE0BvCtfcCr62uU72/+rXq03sfkggIECBAgAABAgQIrKaAQsRqrpuoCRAgQIAAAQIEVlPgebMPtG+1n9CHI5kcA7Q863pk9e7qwtWbqqsvT2giIUCAAAECBAgQILBaAgoRq7VeoiVAgAABAgQIEFhdgWEnxLAjYn/XUIQYihGu5RF4WXWT6l9m/92hev3yhCYSAgQIECBAgAABAqsjoBCxOmslUgIECBAgQIAAgdUVGH6rfigynHE/KbyquuHqprfWkX+4uujYM+LKa52p5AgQIECAAAECBAgsSEAhYkGwhiVAgAABAgQIECAwCpxuthPiS9Vh+xH56KwPwa9UXyC2lAIPre5TfbO636yY9OSljFJQBAgQIECAAAECBJZYQCFiiRdHaAQIECBAgAABAmshMPSEGHpD7O+6a3X8WmS6vkkMxaJhV8v7qhtUX13fVGVGgAABAgQIECBAYP4CChHzNzUiAQIECBAgQIAAgVMFzjYWIa6yH5KXVzfFtfQCw5FMb6hOUz22+oOlj1iABAgQIECAAAECBJZIQCFiiRZDKAQIECBAgAABAmsn8IHqsgfISoPq1Vnyd1ZXGo/QGopHw+4IFwECBAgQIECAAAECWxBQiNgCklsIECBAgAABAgQIHILAA6sHHOC5v5x9mH3NQxjXI3sjcIbqG+PUb66uX31/b0IxKwECBAgQIECAAIHVElCIWK31Ei0BAgQIECBAgMBqCNyueuYBQv1EdaHVSEWUGwTuXz1o/PqXq7+iQ4AAAQIECBAgQIDAwQUUIg5u5A4CBAgQIECAAAEC2xE4ffW66vIHeOjRs9+mP247g7p3KQTOUw1HNP109a//r717Dbb2rO86/h2dYj2k04ooxKAURxHo1Ek4VKWtSXHKWKCHaRNLejBpMAlEBlEmsWPHJNUphA4UKQFJ2oRCm2BSW22DzUwPScdMqS2BvEAdUAoKBB20Fmyhjjq613SDDw97r72f51l77+u+1me9aWHf67p//89/vUj661p3tdr1Z4ZIJgQBAgQIECBAgACBgQUUEQMvRzQCBAgQIECAAIFFChz0k0z/c/ehx4scTuheUr1p1+Ft1d9gQoAAAQIECBAgQIDAegFFhE8IAQIECBAgQIAAgc0JPK26p3r6miMf2vlJn6/Z3C2ddAICq+d7fP3ufb9h59svP3cCGdySAAECBAgQIECAwGIEFBGLWZWgBAgQIECAAAECCxC4tnrzATlvqF6zgFlE3F/gKdXqgdUXVI9Uz68eBUaAAAECBAgQIECAwN4CigifDAIECBAgQIAAAQKbE/iZ6oUHHHdJ9eDmbumkExJY/TzT6meaVq/V/37dCeVwWwIECBAgQIAAAQLDCygihl+RgAQIECBAgAABAgsS+FD1pAPyPrH66IJmEnVvgd+/Uz58ovqy3T+/rHojLAIECBAgQIAAAQIEvlBAEeFTQYAAAQIECBAgQGAzAl9SffKAo1Z//9LN3M4pAwhcWL1nN8fD1XdU7x8glwgECBAgQIAAAQIEhhJQRAy1DmEIECBAgAABAgQWLPC86v4D8r+7etaCZxT9CwWur27Z/a9vqm6GRIAAAQIECBAgQIDA5wsoInwiCBAgQIAAAQIECGxGYPV/hL7xgKPurS7bzO2cMojA6qeZ/n312N08V1V3DJJNDAIECBAgQIAAAQJDCCgihliDEAQIECBAgAABAhMIfKD6swfM8eLqRyeY1QifL3DRzkPKVz/NtHr9SnVp9SgkAgQIECBAgAABAgR+T0AR4ZNAgAABAgQIECBA4NwFztt9PsRB/3x90N/PPYkTTkrgB6rv3b35G6qXn1QQ9yVAgAABAgQIECAwmoB/ERptI/IQIECAAAECBAgsUeDbq7sPCP6/qscscTiZDyXw+Orjp1z5D6p/eKh3uogAAQIECBAgQIDA5AKKiMkXbDwCBAgQIECAAIFjEfh71asOcacLq0cOcZ1Llinw9Op9u9H/XfVX/UTTMhcpNQECBAgQIECAwGYFFBGb9XQaAQIECBAgQIDAdgp8d/Vjhxj9j1efOMR1LlmuwO3V6lkgq9eHqicvdxTJCRAgQIAAAQIECGxGQBGxGUenECBAgAABAgQIbLfAS6tbD0FwXfWmQ1znkmUL/PfqS3dH+MvVu5Y9jvQECBAgQIAAAQIEzk1AEXFuft5NgAABAgQIECBAYCXwgupnD0Hxwuq+Q1znkmUL/M3q+6vVcyNWr4urX172SNITIECAAAECBAgQOHsBRcTZ23knAQIECBAgQIAAgc8KPOGQzwI4/7QHGhOcV+DN1bW7491c/WD1O/OOazICBAgQIECAAAEC+wsoInw6CBAgQIAAAQIECGxG4C07ZcTVa466baeEuGYzt3LKAgT+dPXD1epbMKvXO3e/ObOA6CISIECAAAECBAgQ2KyAImKznk4jQIAAAQIECBDYboGbqhv3IFj9f8Sv/ua1XQIX7RQRD++O/OHqadVntovAtAQIECBAgAABAgRKEeFTQIAAAQIECBAgQGCzAn+9+p7q2dWvVXdU/3Szt3DaggRWDzJflVCPq367+kvV+xaUX1QCBAgQIECAAAEC5yygiDhnQgcQIECAAAECBAgQIEBgrcCD1V/ZvWL1IOsf4UWAAAECBAgQIEBgmwQUEdu0bbMSIECAAAECBAgQIHASAqvnRNy6e+Mvrt628+2IV55EEPckQIAAAQIECBAgcBICioiTUHdPAgQIECBAgAABAgS2TeBJ1YdOGfqC6mPbhmBeAgQIECBAgACB7RRQRGzn3k1NgAABAgQIECBAgMDxCjym+kT1Jbu3Xf1E013Vp483hrsRIECAAAECBAgQOH4BRcTxm7sjAQIECBAgQIAAAQLbKfDE6tXV5bvjr4qI79hOClMTIECAAAECBAhsk4AiYpu2bVYCBAgQIECAAAECBE5a4OXV63dDvKd6xkkHcn8CBAgQIECAAAECRy2giDhqYecTIECAAAECBAgQIEDg/ws8ofrx6ut2/6tP7pYRH4REgAABAgQIECBAYFYBRcSsmzUXAQIECBAgQIAAAQIjC3y0+pO7AVc/1/T91WdGDiwbAQIECBAgQIAAgbMVUEScrZz3ESBAgAABAgQIECBA4OwFrqpeVT1u94jbq6vP/jjvJECAAAECBAgQIDCugCJi3N1IRoAAAQIECBAgQIDA3AJ/v/pHuyP+l+rF1X1zj2w6AgQIECBAgACBbRRQRGzj1s1MgAABAgQIECBAgMAIAl9e/Vj1Nbthfrd6bPXpEcLJQIAAAQIECBAgQGBTAoqITUk6hwABAgQIECBAgAABAmcu8MTqP53yttU3I55SrR5i7UWAAAECBAgQIEBgCgFFxBRrNAQBAgQIECBAgAABAgsW+K7qLdUf3J3hoerbqlUp4UWAAAECBAgQIEBg8QKKiMWv0AAECBAgQIAAAQIECEwg8JPVt54yx13VddVvTTCbEQgQIECAAAECBLZcQBGx5R8A4xMgQIAAAQIECBAgMIzAvbvfhPhsoFdX31f9n2ESCkKAAAECBAgQIEDgLAQUEWeB5i0ECBAgQIAAAQIECBA4AoHzqh+vvvGUs19Rvf4I7uVIAgQIECBAgAABAscmoIg4Nmo3IkCAAAECBAgQIECAwIECf766vfrq3StX34a4snr7ge90AQECBAgQIECAAIFBBRQRgy5GLAIECBAgQIAAAQIEtlbgudUPV0/dFfi/1eqbEf94a0UMToAAAQIECBAgsGgBRcSi1yc8AQIECBAgQIAAAQKTClxV3Vb9vt35frN6SXXPpPMaiwABAgQIECBAYGIBRcTEyzUaAQIECBAgQIAAAQKLFlg9K+JfnDLBh6sXV7+46KmEJ0CAAAECBAgQ2DoBRcTWrdzABAgQIECAAAECBAgsSOCW6vpT8v636k9Uq2dHeBEgQIAAAQIECBBYhIAiYhFrEpIAAQIECBAgQIAAgS0VeFr1hmr13IjPvh6tVg+1/h9bamJsAgQIECBAgACBhQkoIha2MHEJECBAgAABAgQIENg6ga+sfrp68imTv7J6vW9GbN1nwcAECBAgQIAAgUUKKCIWuTahCRAgQIAAAQIECBDYMoG/UD1y2sw3VK/ZMgfjEiBAgAABAgQILFBAEbHApYlMgAABAgQIECBAgMBWClxbvXanfPhDu9N/oHp5df9WahiaAAECBAgQIEBgMQKKiMWsSlACBAgQIECAAAECBAj0b6unnuLwmeqx1ep/ehEgQIAAAQIECBAYUkARMeRahCJAgAABAgQIECBAgMCeAl9c/WT1/FP++ubqVdVHmBEgQIAAAQIECBAYUUARMeJWZCJAgAABAgQIECBAgMD+At9a3VU95pRLbq3+FjQCBAgQIECAAAECIwooIkbcikwECBAgQIAAAQIECBBYL/AXq3edcsnHq/OhESBAgAABAgQIEBhRQBEx4lZkIkCAAAECBAgQIECAwHqB1bch/uvOw6vPO+2y1X/+bXgECBAgQIAAAQIERhJQRIy0DVkIECBAgAABAgQIECBweIHHVz9TPeuUt9xdXX74I1xJgAABAgQIECBA4OgFFBFHb+wOBAgQIECAAAECBAgQOCqBa6vVw6o/+/rX1f07/+Gmo7qhcwkQIECAAAECBAicqYAi4kzFXE+AAAECBAgQIECAAIGxBF5WveG0SP5db6wdSUOAAAECBAgQ2GoB/3C61es3PAECBAgQIECAAAECEwisngvxqdPmuK26ZoLZjECAAAECBAgQIDCBgCJigiUagQABAgQIECBAgACBrRd4Y3XdaQrftPsMia3HAUCAAAECBAgQIHCyAoqIk/V3dwIECBAgQIAAAQIECGxK4FerrzrlsF+pnrOpw51DgAABAgQIECBA4GwFFBFnK+d9BAgQIECAAAECBAgQGEvgiurO0yJdVt07VkxpCBAgQIAAAQIEtk1AEbFtGzcvAQIECBAgQIAAAQIzC9xTXXrKgKsSYlVGeBEgQIAAAQIECBA4MQFFxInRuzEBAgQIECBAgAABAgQ2LvDXqn952qnPrB7e+J0cSIAAAQIECBAgQOCQAoqIQ0K5jAABAgQIECBAgAABAgsQ+APV6tkQF52S1bciFrA4EQkQIECAAAECMwsoImbertkIECBAgAABAgQIENhGgeurW04b/Gurf7WNGGYmQIAAAQIECBA4eQFFxMnvQAICBAgQIECAAAECBAhsUuBPVf/xtAN/fuebEl+/yZs4iwABAgQIECBAgMBhBRQRh5VyHQECBAgQIECAAAECBJYj8KbqJafFXT20evUzTV4ECBAgQIAAAQIEjlVAEXGs3G5GgAABAgQIECBAgACBYxH46j1+iumd1QuO5e5uQoAAAQIECBAgQOAUAUWEjwMBAgQIECBAgAABAgTmFLivev5po11ZvXXOcU1FgAABAgQIECAwqoAiYtTNyEWAAAECBAgQIECAAIFzE7i8+ok9jrig+ti5He3dBAgQIECAAAECBA4voIg4vJUrCRAgQIAAAQIECBAgsDSBm6obTwv9y9XFSxtEXgIECBAgQIAAgeUKKCKWuzvJCRAgQIAAAQIECBAgcJDAU6tfqM4/7cJ/Vn3bQW/2dwIECBAgQIAAAQKbEFBEbELRGQQIECBAgAABAgQIEBhX4Aeq790j3jOrh8eNLRkBAgQIECBAgMAsAoqIWTZpDgIECBAgQIAAAQIECOwt8EXVB3YeUv2kPf58WXUvOAIECBAgQIAAAQJHKaCIOEpdZxMgQIAAAQIECBAgQGAMgddVr9gjyqqEeFt13xgxpSBAgAABAgQIEJhRQBEx41bNRIAAAQIECBAgQIAAgS8UeE710B4wD1aXACNAgAABAgQIECBwVAKKiKOSdS4BAgQIECBAgAABAgTGE3h39Yw9Yj1SXTheXIkIECBAgAABAgRmEFBEzLBFMxAgQIAAAQIECBAgQOBwAqsS4vZ9SofVg6tXD7D2IkCAAAECBAgQILBRAUXERjkdRoAAAQIECBAgQIAAgeEFrqju3CflR6vXV68dfgoBCRAgQIAAAQIEFiOgiFjMqgQlQIAAAQIECBAgQIDAxgQurh7Y57TfqN6+87ebNnY3BxEgQIAAAQIECGy1gCJiq9dveAIECBAgQIAAAQIEtljg0uqefeZ/tHpldfcW+xidAAECBAgQIEBgQwKKiA1BOoYAAQIECBAgQIAAAQILEziv+rvVVdUFe2T/f7cSpgAAGZtJREFUWPW11eobEl4ECBAgQIAAAQIEzlpAEXHWdN5IgAABAgQIECBAgACBKQRWZcQN1eP2mOaRfR5sPcXghiBAgAABAgQIEDgeAUXE8Ti7CwECBAgQIECAAAECBEYWWD0P4prq8XuE/DvVD40cXjYCBAgQIECAAIGxBRQRY+9HOgIECBAgQIAAAQIECByXwOuqV+xzsxdV7ziuIO5DgAABAgQIECAwl4AiYq59moYAAQIECBAgQIAAAQLnIrD6ZsSNexzwyd1vS/zuuRzuvQQIECBAgAABAtspoIjYzr2bmgABAgQIECBAgAABAnsJ/LHq/dUf3eOPl1X3YiNAgAABAgQIECBwpgKKiDMVcz0BAgQIECBAgAABAgTmFnhO9dAeI/6H6quq35x7fNMRIECAAAECBAhsWkARsWlR5xEgQIAAAQIECBAgQGDZAl9U/Wp10R5jfMNOGfFzyx5PegIECBAgQIAAgeMWUEQct7j7ESBAgAABAgQIECBAYHyB51X37xHzkurB8eNLSIAAAQIECBAgMJKAImKkbchCgAABAgQIECBAgACBMQReWt26R5S7q8vHiCgFAQIECBAgQIDAUgQUEUvZlJwECBAgQIAAAQIECBA4PoEXVD+7x+3eu89PNh1fMnciQIAAAQIECBBYnIAiYnErE5gAAQIECBAgQIAAAQJHLnBTdeM+d/HzTEfO7wYECBAgQIAAgbkEFBFz7dM0BAgQIECAAAECBAgQ2ITApdU9+xz0keorqk9t4kbOIECAAAECBAgQmF9AETH/jk1IgAABAgQIECBAgACBMxW4sHrPmjf5VsSZirqeAAECBAgQILDFAoqILV6+0QkQIECAAAECBAgQILCPwJOrD67R+Z3qfN+K8PkhQIAAAQIECBA4jIAi4jBKriFAgAABAgQIECBAgMD2CTxUPWfN2G+trtw+FhMTIECAAAECBAicqYAi4kzFXE+AAAECBAgQIECAAIHtEXhf9fQ1496887fVg629CBAgQIAAAQIECOwroIjw4SBAgAABAgQIECBAgACB/QReV73iAJ4frK5HSIAAAQIECBAgQGA/AUWEzwYBAgQIECBAgAABAgQI7CdwQfWRA3g+XF1evQsjAQIECBAgQIAAgb0EFBE+FwQIECBAgAABAgQIECCwTuDi6oEDiP5N9RUYCRAgQIAAAQIECCgifAYIECBAgAABAgQIECBA4GwE7t/5xsPzDnjjn6l+42wO9x4CBAgQIECAAIG5BXwjYu79mo4AAQIECBAgQIAAAQKbEnh1dcOaw66ubt/UzZxDgAABAgQIECAwj4AiYp5dmoQAAQIECBAgQIAAAQJHLXBf9fx9bvLR6ik7D67+9FGHcD4BAgQIECBAgMCyBBQRy9qXtAQIECBAgAABAgQIEDhJgSurO9YEuKh670kGdG8CBAgQIECAAIHxBBQR4+1EIgIECBAgQIAAAQIECIwq8OTqg2vCXVPdNmp4uQgQIECAAAECBE5GQBFxMu7uSoAAAQIECBAgQIAAgaUKPFBdvE/4VQmxKiO8CBAgQIAAAQIECHxOQBHhw0CAAAECBAgQIECAAAECZyJwZ3XFPm/459W3nMlhriVAgAABAgQIEJhfQBEx/45NSIAAAQIECBAgQIAAgU0KXFrds8+Bb6qu2+TNnEWAAAECBAgQILB8AUXE8ndoAgIECBAgQIAAAQIECBynwOrbEKtvRez1emu1eqC1FwECBAgQIECAAIHPCSgifBgIECBAgAABAgQIECBA4EwEXlrdus8b7q0uO5PDXEuAAAECBAgQIDC/gCJi/h2bkAABAgQIECBAgAABApsUWPeMiDdWL9vkzZxFgAABAgQIECCwfAFFxPJ3aAICBAgQIECAAAECBAgcp8AD1cX73PCV1WuPM4x7ESBAgAABAgQIjC+giBh/RxISIECAAAECBAgQIEBgJIH3V39un0AvrO4bKawsBAgQIECAAAECJy+giDj5HUhAgAABAgQIECBAgACBJQl8audbD+ftE/ii6r1LGkZWAgQIECBAgACBoxdQRBy9sTsQIECAAAECBAgQIEBgFoE/XP3n6o/sMdD/rs6vPjHLsOYgQIAAAQIECBDYjIAiYjOOTiFAgAABAgQIECBAgMA2CDyr+rU1gz67+vVtgDAjAQIECBAgQIDA4QUUEYe3ciUBAgQIECBAgAABAgS2XeAJ1aNrEFbfiPj4tiOZnwABAgQIECBA4PMFFBE+EQQIECBAgAABAgQIECBwWIEnVR9ac/GXVx8+7GGuI0CAAAECBAgQ2A4BRcR27NmUBAgQIECAAAECBAgQ2ITAM6p3rznomdXDm7iRMwgQIECAAAECBOYRUETMs0uTECBAgAABAgQIECBA4KgFLq4eWHOTS6oHjzqE8wkQIECAAAECBJYloIhY1r6kJUCAAAECBAgQIECAwEkKfE/1o2sCXFXdcZIB3ZsAAQIECBAgQGA8AUXEeDuRiAABAgQIECBAgAABAqMKXF/dsibcDdVrRg0vFwECBAgQIECAwMkIKCJOxt1dCRAgQIAAAQIECBAgsESBF1V3rQl+eXX3EgeTmQABAgQIECBA4OgEFBFHZ+tkAgQIECBAgAABAgQIzCZwRXXnmqGurN4629DmIUCAAAECBAgQODcBRcS5+Xk3AQIECBAgQIAAAQIEtkng6uotawa+prptm0DMSoAAAQIECBAgcLCAIuJgI1cQIECAAAECBAgQIECAwO8J3FTduAbj5t1reBEgQIAAAQIECBD4nIAiwoeBAAECBAgQIECAAAECBA4r8E+q1bce9nutvi1x7WEPcx0BAgQIECBAgMB2CCgitmPPpiRAgAABAgQIECBAgMAmBH6x+ro1B/1S9dxN3MgZBAgQIECAAAEC8wgoIubZpUkIECBAgAABAgQIECBw1AJ+mumohZ1PgAABAgQIEJhQQBEx4VKNRIAAAQIECBAgQIAAgSMS+Obqp9ec/RPVdx7RvR1LgAABAgQIECCwUAFFxEIXJzYBAgQIECBAgAABAgROQOBvVz+05r4PVpecQC63JECAAAECBAgQGFhAETHwckQjQIAAAQIECBAgQIDAYAJXVHeuyfRb1ZcNllkcAgQIECBAgACBExZQRJzwAtyeAAECBAgQIECAAAECCxJ4UXXXmryPVBcuaB5RCRAgQIAAAQIEjkFAEXEMyG5BgAABAgQIECBAgACBSQQurh44YBb/njnJso1BgAABAgQIENiUgH9A3JSkcwgQIECAAAECBAgQIDC/wNXVW9aM+UvVc+dnMCEBAgQIECBAgMCZCCgizkTLtQQIECBAgAABAgQIENhugbdV37WG4B3V6uebvAgQIECAAAECBAh8TkAR4cNAgAABAgQIECBAgAABAocR+Jbqpw648Lurtx/mMNcQIECAAAECBAhsj4AiYnt2bVICBAgQIECAAAECBAici8D11S0HHPDs6tfP5SbeS4AAAQIECBAgMJ+AImK+nZqIAAECBAgQIECAAAECRyFwZXXHmoPfWb3gKG7sTAIECBAgQIAAgWULKCKWvT/pCRAgQIAAAQIECBAgcFwCq4dQ/8Kam92887ebjiuM+xAgQIAAAQIECCxHQBGxnF1JSoAAAQIECBAgQIAAgZMWWBUNN+4R4sHqkpMO5/4ECBAgQIAAAQJjCigixtyLVAQIECBAgAABAgQIEBhV4NLq+3YeXP2V1furd+wE9U2IUbclFwECBAgQIEBgAAFFxABLEIEAAQIECBAgQIAAAQIECBAgQIAAAQIECMwqoIiYdbPmIkCAAAECBAgQIECAAAECBAgQIECAAAECAwgoIgZYgggECBAgQIAAAQIECBAgQIAAAQIECBAgQGBWAUXErJs1FwECBAgQIECAAAECBAgQIECAAAECBAgQGEBAETHAEkQgQIAAAQIECBAgQIAAAQIECBAgQIAAAQKzCigiZt2suQgQIECAAAECBAgQIECAAAECBAgQIECAwAACiogBliACAQIECBAgQIAAAQIECBAgQIAAAQIECBCYVUARMetmzUWAAAECBAgQIECAAAECBAgQIECAAAECBAYQUEQMsAQRCBAgQIAAAQIECBAgQIAAAQIECBAgQIDArAKKiFk3ay4CBAgQIECAAAECBAgQIECAAAECBAgQIDCAgCJigCWIQIAAAQIECBAgQIAAAQIECBAgQIAAAQIEZhVQRMy6WXMRIECAAAECBAgQIECAAAECBAgQIECAAIEBBBQRAyxBBAIECBAgQIAAAQIECBAgQIAAAQIECBAgMKuAImLWzZqLAAECBAgQIECAAAECBAgQIECAAAECBAgMIKCIGGAJIhAgQIAAAQIECBAgQIAAAQIECBAgQIAAgVkFFBGzbtZcBAgQIECAAAECBAgQIECAAAECBAgQIEBgAAFFxABLEIEAAQIECBAgQIAAAQIECBAgQIAAAQIECMwqoIiYdbPmIkCAAAECBAgQIECAAAECBAgQIECAAAECAwgoIgZYgggECBAgQIAAAQIECBAgQIAAAQIECBAgQGBWAUXErJs1FwECBAgQIECAAAECBAgQIECAAAECBAgQGEBAETHAEkQgQIAAAQIECBAgQIAAAQIECBAgQIAAAQKzCigiZt2suQgQIECAAAECBAgQIECAAAECBAgQIECAwAACiogBliACAQIECBAgQIAAAQIECBAgQIAAAQIECBCYVUARMetmzUWAAAECBAgQIECAAAECBAgQIECAAAECBAYQUEQMsAQRCBAgQIAAAQIECBAgQIAAAQIECBAgQIDArAKKiFk3ay4CBAgQIECAAAECBAgQIECAAAECBAgQIDCAgCJigCWIQIAAAQIECBAgQIAAAQIECBAgQIAAAQIEZhVQRMy6WXMRIECAAAECBAgQIECAAAECBAgQIECAAIEBBBQRAyxBBAIECBAgQIAAAQIECBAgQIAAAQIECBAgMKuAImLWzZqLAAECBAgQIECAAAECBAgQIECAAAECBAgMIKCIGGAJIhAgQIAAAQIECBAgQIAAAQIECBAgQIAAgVkFFBGzbtZcBAgQIECAAAECBAgQIECAAAECBAgQIEBgAAFFxABLEIEAAQIECBAgQIAAAQIECBAgQIAAAQIECMwqoIiYdbPmIkCAAAECBAgQIECAAAECBAgQIECAAAECAwgoIgZYgggECBAgQIAAAQIECBAgQIAAAQIECBAgQGBWAUXErJs1FwECBAgQIECAAAECBAgQIECAAAECBAgQGEBAETHAEkQgQIAAAQIECBAgQIAAAQIECBAgQIAAAQKzCigiZt2suQgQIECAAAECBAgQIECAAAECBAgQIECAwAACiogBliACAQIECBAgQIAAAQIECBAgQIAAAQIECBCYVUARMetmzUWAAAECBAgQIECAAAECBAgQIECAAAECBAYQUEQMsAQRCBAgQIAAAQIECBAgQIAAAQIECBAgQIDArAKKiFk3ay4CBAgQIECAAAECBAgQIECAAAECBAgQIDCAgCJigCWIQIAAAQIECBAgQIAAAQIECBAgQIAAAQIEZhVQRMy6WXMRIECAAAECBAgQIECAAAECBAgQIECAAIEBBBQRAyxBBAIECBAgQIAAAQIECBAgQIAAAQIECBAgMKuAImLWzZqLAAECBAgQIECAAAECBAgQIECAAAECBAgMIKCIGGAJIhAgQIAAAQIECBAgQIAAAQIECBAgQIAAgVkFFBGzbtZcBAgQIECAAAECBAgQIECAAAECBAgQIEBgAAFFxABLEIEAAQIECBAgQIAAAQIECBAgQIAAAQIECMwqoIiYdbPmIkCAAAECBAgQIECAAAECBAgQIECAAAECAwgoIgZYgggECBAgQIAAAQIECBAgQIAAAQIECBAgQGBWAUXErJs1FwECBAgQIECAAAECBAgQIECAAAECBAgQGEBAETHAEkQgQIAAAQIECBAgQIAAAQIECBAgQIAAAQKzCigiZt2suQgQIECAAAECBAgQIECAAAECBAgQIECAwAACiogBliACAQIECBAgQIAAAQIECBAgQIAAAQIECBCYVUARMetmzUWAAAECBAgQIECAAAECBAgQIECAAAECBAYQUEQMsAQRCBAgQIAAAQIECBAgQIAAAQIECBAgQIDArAKKiFk3ay4CBAgQIECAAAECBAgQIECAAAECBAgQIDCAgCJigCWIQIAAAQIECBAgQIAAAQIECBAgQIAAAQIEZhVQRMy6WXMRIECAAAECBAgQIECAAAECBAgQIECAAIEBBBQRAyxBBAIECBAgQIAAAQIECBAgQIAAAQIECBAgMKuAImLWzZqLAAECBAgQIECAAAECBAgQIECAAAECBAgMIKCIGGAJIhAgQIAAAQIECBAgQIAAAQIECBAgQIAAgVkFFBGzbtZcBAgQIECAAAECBAgQIECAAAECBAgQIEBgAAFFxABLEIEAAQIECBAgQIAAAQIECBAgQIAAAQIECMwqoIiYdbPmIkCAAAECBAgQIECAAAECBAgQIECAAAECAwgoIgZYgggECBAgQIAAAQIECBAgQIAAAQIECBAgQGBWAUXErJs1FwECBAgQIECAAAECBAgQIECAAAECBAgQGEBAETHAEkQgQIAAAQIECBAgQIAAAQIECBAgQIAAAQKzCigiZt2suQgQIECAAAECBAgQIECAAAECBAgQIECAwAACiogBliACAQIECBAgQIAAAQIECBAgQIAAAQIECBCYVUARMetmzUWAAAECBAgQIECAAAECBAgQIECAAAECBAYQUEQMsAQRCBAgQIAAAQIECBAgQIAAAQIECBAgQIDArAKKiFk3ay4CBAgQIECAAAECBAgQIECAAAECBAgQIDCAgCJigCWIQIAAAQIECBAgQIAAAQIECBAgQIAAAQIEZhVQRMy6WXMRIECAAAECBAgQIECAAAECBAgQIECAAIEBBBQRAyxBBAIECBAgQIAAAQIECBAgQIAAAQIECBAgMKuAImLWzZqLAAECBAgQIECAAAECBAgQIECAAAECBAgMIKCIGGAJIhAgQIAAAQIECBAgQIAAAQIECBAgQIAAgVkFFBGzbtZcBAgQIECAAAECBAgQIECAAAECBAgQIEBgAAFFxABLEIEAAQIECBAgQIAAAQIECBAgQIAAAQIECMwqoIiYdbPmIkCAAAECBAgQIECAAAECBAgQIECAAAECAwgoIgZYgggECBAgQIAAAQIECBAgQIAAAQIECBAgQGBWAUXErJs1FwECBAgQIECAAAECBAgQIECAAAECBAgQGEBAETHAEkQgQIAAAQIECBAgQIAAAQIECBAgQIAAAQKzCigiZt2suQgQIECAAAECBAgQIECAAAECBAgQIECAwAACiogBliACAQIECBAgQIAAAQIECBAgQIAAAQIECBCYVUARMetmzUWAAAECBAgQIECAAAECBAgQIECAAAECBAYQUEQMsAQRCBAgQIAAAQIECBAgQIAAAQIECBAgQIDArAKKiFk3ay4CBAgQIECAAAECBAgQIECAAAECBAgQIDCAgCJigCWIQIAAAQIECBAgQIAAAQIECBAgQIAAAQIEZhVQRMy6WXMRIECAAAECBAgQIECAAAECBAgQIECAAIEBBBQRAyxBBAIECBAgQIAAAQIECBAgQIAAAQIECBAgMKuAImLWzZqLAAECBAgQIECAAAECBAgQIECAAAECBAgMIKCIGGAJIhAgQIAAAQIECBAgQIAAAQIECBAgQIAAgVkFFBGzbtZcBAgQIECAAAECBAgQIECAAAECBAgQIEBgAAFFxABLEIEAAQIECBAgQIAAAQIECBAgQIAAAQIECMwqoIiYdbPmIkCAAAECBAgQIECAAAECBAgQIECAAAECAwgoIgZYgggECBAgQIAAAQIECBAgQIAAAQIECBAgQGBWAUXErJs1FwECBAgQIECAAAECBAgQIECAAAECBAgQGEBAETHAEkQgQIAAAQIECBAgQIAAAQIECBAgQIAAAQKzCigiZt2suQgQIECAAAECBAgQIECAAAECBAgQIECAwAACiogBliACAQIECBAgQIAAAQIECBAgQIAAAQIECBCYVUARMetmzUWAAAECBAgQIECAAAECBAgQIECAAAECBAYQUEQMsAQRCBAgQIAAAQIECBAgQIAAAQIECBAgQIDArAKKiFk3ay4CBAgQIECAAAECBAgQIECAAAECBAgQIDCAgCJigCWIQIAAAQIECBAgQIAAAQIECBAgQIAAAQIEZhVQRMy6WXMRIECAAAECBAgQIECAAAECBAgQIECAAIEBBBQRAyxBBAIECBAgQIAAAQIECBAgQIAAAQIECBAgMKuAImLWzZqLAAECBAgQIECAAAECBAgQIECAAAECBAgMIKCIGGAJIhAgQIAAAQIECBAgQIAAAQIECBAgQIAAgVkFFBGzbtZcBAgQIECAAAECBAgQIECAAAECBAgQIEBgAAFFxABLEIEAAQIECBAgQIAAAQIECBAgQIAAAQIECMwqoIiYdbPmIkCAAAECBAgQIECAAAECBAgQIECAAAECAwgoIgZYgggECBAgQIAAAQIECBAgQIAAAQIECBAgQGBWAUXErJs1FwECBAgQIECAAAECBAgQIECAAAECBAgQGEBAETHAEkQgQIAAAQIECBAgQIAAAQIECBAgQIAAAQKzCigiZt2suQgQIECAAAECBAgQIECAAAECBAgQIECAwAAC/w+N5GXC586AewAAAABJRU5ErkJggg=='){
		//$('#signature-img').val('');
		//dataURL = dataURL.replace("data:image/png;base64,", "");
	}else{
		dataURL = dataURL.replace("data:image/png;base64,", "");
		$('#signature-img').val(dataURL);
	}
	
	
	$.ajax({
		type: 'POST',
		url: '<?php echo base_url('users_profile/actionedituserssignature_data/'.$iddata); ?>',
		data: $("#dataForm").serialize(), 
		success: function(response) { 
			var obj = JSON.parse(response);
			if(obj.message == 'success'){
				// Menampilkan SweetAlert sebelum reload
				Swal.fire({
					title: 'Berhasil!',
					text: 'Data berhasil disimpan.',
					icon: 'success',
					confirmButtonText: 'OK'
				}).then(function() {
					// Setelah tombol OK di klik, halaman akan reload
					window.location.href = "<?php echo base_url('users_profile/view?message=success');?>";
				});
			} else {
				// Menampilkan SweetAlert untuk error
				Swal.fire({
					title: 'Gagal!',
					text: 'Terjadi kesalahan saat menyimpan data.',
					icon: 'error',
					confirmButtonText: 'Coba Lagi'
				}).then(function() {
					// Setelah tombol OK di klik, halaman akan reload
					window.location.href = "<?php echo base_url('users_profile/view?message=error');?>";
				});
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
	console.log('asa')
    // When zoomed out to less than 100%, for some very strange reason,
    // some browsers report devicePixelRatio as less than 1
    // and only part of the canvas is cleared then.
    var ratio =  Math.max(window.devicePixelRatio || 1, 1);
    canvas.width = canvas.offsetWidth * ratio;
    canvas.height = canvas.offsetHeight * ratio;
    canvas.getContext("2d").scale(ratio, ratio);
}


	$(document).ready(function() {
		
			 signaturePad = new SignaturePad(canvas);

			resizeCanvas();
			
			clearButton.addEventListener("click", function (event) {
			 console.log('aawsassa')
				signaturePad.clear();
			});
			
		$('#area_provinsi_id').prop('disabled', true);
		$('#area_kota_id').prop('disabled', true);
		$('#gid').prop('disabled', true);
		
		$("#themes_id").select2({
				allowClear: true,
				width: '100%',	
				ajax: {
					type: "POST",
					url: "<?php echo base_url($headurl.'/select2'); ?>",
							dataType: 'json',
							delay: 250,
							data: function (params) {
							  return {
								q: params.term, // search term
								table: '<?php echo 'master_themes'; ?>',
								id:'<?php echo 'id'; ?>',
								name:'<?php echo 'name'; ?>',
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
						  placeholder: 'Search for a Themes'
				})
				
		$("#sidebar").select2({
				allowClear: true,
				width: '100%',	
				ajax: {
					type: "POST",
					url: "<?php echo base_url($headurl.'/select2'); ?>",
							dataType: 'json',
							delay: 250,
							data: function (params) {
							  return {
								q: params.term, // search term
								table: '<?php echo 'master_status_option'; ?>',
								id:'<?php echo 'id'; ?>',
								name:'<?php echo 'name'; ?>',
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
						  placeholder: 'Search for a Sidebar'
				})

		$("#area_provinsi_id").select2({	
		ajax: {
			type: "POST",
			url: "<?php echo base_url($headurl.'/select2_area_provinsi'); ?>",
			dataType: 'json',
			delay: 250,
			data: function (params) {
				return {
					q: params.term, // search term
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
						  placeholder: 'Search for a Role'
				})
				
	});
	
	Dropzone.autoDiscover = false;
	var limit = 0;
    var foto_upload_cover = new Dropzone("#coverupload", {
    url: "<?php echo base_url($headurl.'/proses_upload') ?>",
    maxFiles: 1,
    maxFilesize: 1000, // Maksimum ukuran file 1MB
    method: "post",
    acceptedFiles: "image/*", // Hanya menerima file gambar
    createImageThumbnails: true,
    paramName: "userfile",
    dictInvalidFileType: "Hanya file gambar yang diperbolehkan.",
    addRemoveLinks: true,
    thumbnailWidth: "250",
    thumbnailHeight: "250",
    dictDefaultMessage: "Seret & Letakkan foto Anda di sini atau klik untuk memilih", // Pesan default
    dictRemoveFile: "Hapus foto", // Teks untuk tombol hapus
    dictCancelUpload: "Batal upload", // Teks untuk tombol batal upload
    dictFileTooBig: "File terlalu besar. Maksimum ukuran: 1MB", // Pesan ketika file terlalu besar
    init: function () {
		this.on('error', function(file, response) {
				var obj = JSON.parse(response);
				updateCsrfToken(obj.csrf_hash);
				alert(response);
		});
        this.on("maxfilesexceeded", function (file, response) { 
			var obj = JSON.parse(response);
			updateCsrfToken(obj.csrf_hash);
			limit = 1; 
		});
        this.on("success", function (file, response) {
			var obj = JSON.parse(response);
			updateCsrfToken(obj.csrf_hash);
            if (limit == 0) {
                var obj = $.parseJSON(response);
                $('#dataForm').append('<input style="display:none" type="text" id="cover' + obj.id + '" class="form-control form-control-sm" name="cover" value="' + obj.id + '" />');
				save()
            }
        });
    }
});

// Event saat upload dimulai
foto_upload_cover.on("sending", function (a, b, c) {
    a.token = Math.random();
    c.append("token_foto", a.token); // Menambahkan token untuk tiap foto
    c.append("user_id", <?php echo $this->session->userdata('userid'); ?>);
    c.append("file_name", 'Photo Profile <?php echo $this->session->userdata('fullname'); ?>');
    c.append("<?php echo $this->security->get_csrf_token_name(); ?>", csrfHash );
});

// Event saat foto dihapus
foto_upload_cover.on("removedfile", function (a) {
    bootbox.confirm({
        message: "Apakah Anda yakin ingin menghapus foto ini secara permanen?",
        buttons: {
            confirm: {
                label: 'Ya',
                className: 'btn-success'
            },
            cancel: {
                label: 'Tidak',
                className: 'btn-danger'
            }
        },
        callback: function (result) {
            if (result == true) {
                var token = a.token;
                $.ajax({
                    type: "post",
                    data: {
                        token: token,
                        id: <?php echo $iddata; ?>,
                        <?php echo $this->security->get_csrf_token_name(); ?> : csrfHash 
                    },
                    url: "<?php echo base_url($headurl.'/remove_foto') ?>",
                    cache: false,
                    dataType: 'json',
                    success: function (data) { document.getElementById("cover" + data.id).remove(); },
                    error: function () { console.log("Error"); }
                });
            } else {
                $.post('<?php echo base_url($headurl.'/getcover'); ?>', {
                    id: <?php echo $iddata; ?>,
                    <?php echo $this->security->get_csrf_token_name(); ?> : csrfHash 
                }, function (data) {
                    if (data != 'null') {
                        var obj = jQuery.parseJSON(data);
						updateCsrfToken(obj.csrf_hash)
                        if (obj.message == 'success') {
                            for (var key in obj) {
                                var mockFile = { name: obj[key].name, size: obj[key].size, token: obj[key].token };
                                foto_upload_cover.options.addedfile.call(foto_upload_cover, mockFile);
                                foto_upload_cover.options.thumbnail.call(foto_upload_cover, mockFile, "<?php echo base_url(); ?>" + obj[key].path);
                                foto_upload_cover.emit("complete", mockFile);
                            }
                        }
                    }
                });
            }
        }
    });
});


	
</script>