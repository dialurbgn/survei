<!--begin::Navbar-->
							<div class="card mb-6 mb-xl-9">
								<div class="card-body pt-9 pb-0">
									<!--begin::Details-->
									<div class="d-flex flex-wrap flex-sm-nowrap mb-6 no-margin">
										<!--begin::Image-->
										
										<!--end::Image-->
										<!--begin::Wrapper-->
										<div class="flex-grow-1">
											<!--begin::Head-->
											<div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
												<!--begin::Details-->
												
												<div class="d-flex">
												
													<!--begin::Menu-->
													<div class="me-0">
														
														<?php if($this->session->userdata('group_id') == 1 || $this->session->userdata('group_id') == 2){ ?>
														<button class="btn btn-sm btn-bg-light btn-active-color-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
															Filter Tabel <i class="ki-solid ki-dots-horizontal fs-2x"></i>
														</button>
														<?php } ?>
														<!--begin::Menu 3-->
														<div  style="max-height:250px;overflow: auto !important;" class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold w-200px py-3" data-kt-menu="true">
															<?php
																if(!isset($tablenya)){
																	$tablenya_data = $tablenya_doc;
																	$exclude_data = $exclude_doc;
																	$module_data = $module_doc;
																}else{
																	$tablenya_data = $tablenya;
																	$exclude_data = $exclude;
																	$module_data = $module;
																}
															?>
															<div class="menu-item px-3" style="max-height:250px;overflow: auto !important;"> 
																<?php echo $this->ortyd->getviewlistcheck($tablenya_data, $exclude_data,$module_data); ?>
															</div>
															
															<!--begin::Heading-->
															
															<!--end::Menu item-->
														</div>
														<!--end::Menu 3-->
													</div>
													<!--end::Menu-->
												</div>
												<!--end::Details-->
											</div>
											<!--end::Head-->
											<!--begin::Info-->
											<div class="d-flex flex-wrap justify-content-start">
												<!--begin::Stats-->
												
												
												
												<!--end::Stats-->
												<!--begin::Users-->
												<div class="symbol-group symbol-hover mb-3" id="header_perusahaan_mitra">
													<!--begin::User-->
													
													<!--end::User-->
	
												</div>
												<!--end::Users-->
											</div>
											
											
											<!--end::Info-->
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

function getHeader(id){
	$.post('<?php echo base_url('dashboard/getheader'); ?>',{ 
		id : id
	}, function (data) {
		var obj = data
		//updateCsrfToken(data.csrf_hash)
		if(obj.message == "success"){
			var datanya = obj.data;
			console.log(datanya)
			$("#header_data_list_menu").html($('#ul_menu_header').html());
			$("#header_data_menu").html(datanya.name);
			//$("#header_data_status").html(datanya.status_name);
			$("#header_data_description").html(datanya.description);
			//$("#header_data_total").html($('#dt_total').html());
			//$("#header_perusahaan_hp").html(datanya.perusahaan_hp);
			
			//$("#header_perusahaan_jenis").html(datanya.perusahaan_jenis_name);


		}else{
			
		}
	}, 'json')
}


</script>
