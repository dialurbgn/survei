<style>
[data-bs-theme=light] body:not(.app-blank) {
    //background-size: cover;
}

</style>


<!--begin::Container-->
					<div id="kt_content_container" class="d-flex flex-column-fluid align-items-start container-xxl">
						<!--begin::Post-->
						<div class="content flex-row-fluid" id="kt_content">
							<!--begin::Navbar-->
							<div class="card mb-5 mb-xl-10">
								<div class="card-body pt-9 pb-0">
									<!--begin::Details-->
									<div class="d-flex flex-wrap flex-sm-nowrap">
										<!--begin: Pic-->
										<div class="me-7 mb-4">
											<div class="symbol symbol-100px symbol-lg-160px symbol-fixed position-relative">
											
												<?php
													$fullname = $this->session->userdata('fullname');
													$this->db->select('data_gallery.path, users_data.fullname');
													$this->db->where('users_data.id',$this->session->userdata('userid'));
													$this->db->join('data_gallery', 'data_gallery.id = users_data.cover', 'left');
													$queryimage = $this->db->get('users_data');
													$queryimage = $queryimage->result_object();
													if ($queryimage) {
														$relative_path = $queryimage[0]->path;
														$full_path = FCPATH . $relative_path;
														if($relative_path){
															if (file_exists($full_path)) {
																$imagecover = base_url() . $relative_path;
															} else {
																$imagecover = base_url() . 'themes/ortyd/assets/media/avatars/blank.png';
															}
														}else{
															$imagecover = base_url() . 'themes/ortyd/assets/media/avatars/blank.png';
														}
													} else {
														$imagecover = base_url().'themes/ortyd/assets/media/avatars/blank.png';
													}
												?>

												<img src="<?php echo $imagecover; ?>" alt="image" />
												<div class="position-absolute translate-middle bottom-0 start-100 mb-6 bg-success rounded-circle border border-4 border-body h-20px w-20px"></div>

												<!-- Tombol untuk mengubah foto -->
												<a href="<?php echo base_url('users_profile/view'); ?>" class="btn btn-primary btn-sm change-photo-btn">Update Profile</a>
											</div>
										</div>

										<style>
											/* Tombol hanya muncul saat gambar di-hover */
											.symbol:hover .change-photo-btn {
												display: block; /* Menampilkan tombol saat hover */
											}

											/* Menyembunyikan tombol secara default */
											.change-photo-btn {
												display: none;
												position: absolute;
												bottom: 10px;
												left: 50%;
												transform: translateX(-50%);
												padding: 5px 10px;
												font-size: 12px;
											}

											/* Styling tombol */
											.change-photo-btn {
												background-color: #007bff;
												color: white;
												border-radius: 5px;
												text-decoration: none;
												text-align: center;
											}

											/* Efek hover pada tombol */
											.change-photo-btn:hover {
												background-color: #0056b3;
											}
										</style>


										<!--end::Pic-->
										<!--begin::Info-->
										<div class="flex-grow-1">
											<!--begin::Title-->
											<div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
												<!--begin::User-->
												<div class="d-flex flex-column">
													<!--begin::Name-->
													<div class="d-flex align-items-center mb-2">
														<a href="#" class="text-gray-900 text-hover-primary fs-2 fw-bold me-1">
															<?php echo $this->session->userdata('fullname'); ?>
														</a>
														<a href="#">
															<i class="ki-duotone ki-verify fs-1 text-primary">
																<span class="path1"></span>
																<span class="path2"></span>
															</i>
														</a>
													</div>
													<!--end::Name-->
													<!--begin::Info-->
													<div class="d-flex flex-wrap fw-semibold fs-6 mb-4 pe-2">
														<a href="#" class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2">
														<i class="ki-duotone ki-profile-circle fs-4 me-1">
															<span class="path1"></span>
															<span class="path2"></span>
															<span class="path3"></span>
														</i>
															<?php echo $this->session->userdata('username'); ?>
														</a>
														<a href="#" class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2">
														<i class="ki-duotone ki-profile-circle fs-4 me-1">
															<span class="path1"></span>
															<span class="path2"></span>
														</i><?php echo $this->ortyd->select2_getname($this->session->userdata("group_id"),"users_groups","id","name");
															?></a>
														<a href="#" class="d-flex align-items-center text-gray-400 text-hover-primary mb-2">
														<i class="ki-duotone ki-sms fs-4">
															<span class="path1"></span>
															<span class="path2"></span>
														</i><?php echo $this->session->userdata('email'); ?></a>
													</div>
													<!--end::Info-->
												</div>
												<!--end::User-->
												<!--begin::Actions-->
												<div class="d-flex my-4">
													<a href="<?php echo base_url('data_survei_pm'); ?>" class="btn btn-danger me-3">Take Down Sekarang <i class="ki-duotone ki-black-right fs-2 text-white"></i></a>
													<!--begin::Menu-->
													<div class="me-0">
														<button class="btn btn-icon btn-bg-light btn-active-color-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
															<i class="ki-solid ki-dots-horizontal fs-2x"></i>
														</button>
														<!--begin::Menu 3-->
														<div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold w-200px py-3" data-kt-menu="true">
															<!--begin::Heading-->
															<div class="menu-item px-3">
																<div class="menu-content text-muted pb-2 px-3 fs-7 text-uppercase">Laporan Anda</div>
															</div>
															<!--end::Heading-->
															<!--begin::Menu item-->
														<!--begin::Label-->
												<div class="menu-item px-3">
												<label class="form-label fw-semibold">Tipe:</label>
												<!--end::Label-->
												<!--begin::Input-->
												<div>
														<select class="form-control form-control-sm" id="filter_tipe">
														<option value="ALL" selected>ALL</option>
													</select>
												</div>
												<!--end::Input-->
												
												<label class="form-label fw-semibold">Tahun:</label>
												<!--end::Label-->
												<!--begin::Input-->
												<div>
														<select class="form-control form-control-sm" id="filter_tahun">
												<?php 	
												if(isset($_GET['tahun'])){
													$i = $_GET['tahun'];
												}else{
													$i = date('Y');
												}

												for($y=date('Y')+1;$y>=2023;$y--) { 
												?>
												<option value="<?php echo $y; ?>" <?php if($y == $i){echo 'selected';}?>><?php echo $y; ?></option>
												<?php } ?>	
													</select>
												</div>
												<!--end::Input-->
												
												<label class="form-label fw-semibold">Agency :</label>
												<!--end::Label-->
												<!--begin::Input-->
												<div>
														<select class="form-control form-control-sm" id="filter_agency">
														<option value="ALL" selected>ALL</option>
													</select>
												</div>
												<!--end::Input-->
												</div>
															<!--end::Menu item-->
														</div>
														<!--end::Menu 3-->
													</div>
													<!--end::Menu-->
												</div>
												<!--end::Actions-->
											</div>
											<!--end::Title-->
											<!--begin::Stats-->
											<div class="d-flex flex-wrap flex-stack">
												<!--begin::Wrapper-->
												<div class="d-flex flex-column flex-grow-1 pe-8">
													<!--begin::Stats-->
													<div class="d-flex flex-wrap">
														<!--begin::Stat-->
														<div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
															<!--begin::Number-->
															<a href="<?php echo base_url('data_survei_pm'); ?>"><div class="d-flex align-items-center">
																<i class="ki-duotone ki-arrow-up fs-3 text-success me-2">
																	<span class="path1"></span>
																	<span class="path2"></span>
																</i>
																
																<?php
																	$this->db->from('data_survei_pm');
																	$this->db->where('active', 1);
																	$this->db->where('status_id !=', 100);
																	$this->db->where('createdid', $this->session->userdata('userid'));
																	$counttotal = $this->db->count_all_results();
																?>
																<div id="total_pengajuan_data" class="fs-2 fw-bold" data-kt-countup="false" data-kt-countup-value="0"><?php echo $counttotal; ?></div>
															</div>
															<!--end::Number-->
															<!--begin::Label-->
															<div class="fw-semibold fs-6 text-gray-400">Total Survei</div>
															<!--end::Label-->
															</a>
														</div>
														<!--end::Stat-->
														<!--begin::Stat-->
														<div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
															<!--begin::Number-->
															<a href="<?php echo base_url('data_survei_pm'); ?>"><div class="d-flex align-items-center">
																<i class="ki-duotone ki-arrow-up fs-3 text-success me-2">
																	<span class="path1"></span>
																	<span class="path2"></span>
																</i>
																
																<?php
																	$this->db->from('data_survei_pm');
																	$this->db->where('active', 1);
																	$this->db->where_in('status_id', array(1,2));
																	$this->db->where('createdid', $this->session->userdata('userid'));
																	$counttotal = $this->db->count_all_results();
																?>
																
																<div id="total_pengajuan_invoice_data" class="fs-2 fw-bold" data-kt-countup="false" data-kt-countup-value="0"><?php echo $counttotal; ?></div>
															</div>
															<!--end::Number-->
															<!--begin::Label-->
															<div class="fw-semibold fs-6 text-gray-400">Verifikasi</div>
															</a>
															<!--end::Label-->
														</div>
														<!--end::Stat-->
													</div>
													<!--end::Stats-->
												</div>
												<!--end::Wrapper-->
												
												<div class="d-flex align-items-right w-200px w-sm-300px flex-column mt-3">
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
															<img alt="Logo" src="<?php echo base_url(); ?>themes/ortyd/assets/media/svg/brand-logos/google-icon.svg" class="h-15px me-3" />Connect Google Account</a>
															<!--end::Google link=-->
														</div>
														<!--end::Col-->
														<!--begin::Col-->
														
													</div>
													<!--end::Login options-->
													<?php } ?>
												</div>
												
											</div>
											<!--end::Stats-->
										</div>
										<!--end::Info-->
									</div>
									<!--end::Details-->
									<!--begin::Navs-->
									<ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bold">
										
										<!--begin::Nav item-->
										<li class="nav-item mt-2">
											<a class="nav-link text-active-primary ms-0 me-10 py-5 active" href="javascript:;">Overview</a>
										</li>
										<!--end::Nav item-->

									</ul>
									<!--begin::Navs-->
								</div>
							</div>
							<!--end::Navbar-->
							<!--begin::Row-->
							<div class="row g-xxl-9" style="margin: -5px;">

							</div>
							<!--end::Row-->

						</div>
						<!--end::Post-->
					</div>
					<!--end::Container-->


