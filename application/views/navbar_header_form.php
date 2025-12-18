<!--begin::Navbar-->
							<div class="card mb-6 mb-xl-9" id="header-form">
								<div class="card-body pt-9 pb-0">
									<!--begin::Details-->
									<div class="d-flex flex-wrap flex-sm-nowrap mb-6 no-margin">
										<!--begin::Image-->
										
											<div class="d-flex flex-center flex-shrink-0 bg-light rounded w-100px h-100px w-lg-50px h-lg-50px me-7 mb-4">
											<img class="mw-50px mw-lg-75px" src="<?php echo base_url(); ?>themes/ortyd/assets/media/svg/files/folder-document.svg" alt="image" />
										</div>
										
										<!--end::Image-->
										<!--begin::Wrapper-->
										<div class="flex-grow-1">
											<!--begin::Head-->
											<div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
												<!--begin::Details-->
												<div class="d-flex flex-column">
													<!--begin::Status-->
													<div class="d-flex align-items-center mb-1">
														<a href="javascript:;" class="text-gray-800 text-hover-primary fs-2 fw-bold me-3" id="header_data_menu"><?php echo $title; ?></a>
														<?php if(isset($status_name)){ ?>
														<span class="badge badge-light-success me-auto" id="header_data_description"><?php echo $status_name; ?></span>
														<?php } ?>
														
													
													</div>
													<?php if(isset($title_desc)){ ?>
													<div class="d-flex align-items-center mb-1" >
														 <span class="fs-8"> <?php echo $title_desc; ?></span>
													</div>
													<?php } ?>
													<!--end::Status-->
													<!--begin::Description
													<div class="d-flex flex-wrap fw-semibold mb-4 fs-5 text-gray-400" id="header_data_description"></div>
													end::Description-->
												</div>
												
												
												<div class="d-flex my-4" id="btn-aksi-submit">
												
												</div>
												<!--end::Details-->
											</div>
											<!--end::Head-->

										</div>
										
										<!--end::Wrapper-->
									</div>
									<!--end::Details-->
									<div class="separator"></div>
									<!--begin::Nav-->
									<div style="overflow: auto">
										<ul style="width:max-content" id="header_data_list_menu" class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bold">
										
										</ul>
									</div>
									<!--end::Nav-->
								</div>
							</div>
							<!--end::Navbar-->
							
							<script>
							
							$( document ).ready(function() {
								$("#btn-aksi-submit").html($('#btn-aksi-action').html());
								$('#btn-aksi-action').remove();
							})


							</script>
