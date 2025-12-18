<!--begin::Navbar-->
							<div class="card mb-6 mb-xl-9">
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
														<a href="javascript:;" class="text-gray-800 text-hover-primary fs-2 fw-bold me-3" id="header_data_menu">-</a>
														<span style="display:none" class="badge badge-light-success me-auto" id="header_data_description">-</span>
														
														
													
													</div>
													<div class="d-flex align-items-center mb-1">
														 <span class="fs-8"> Total </span>
														 <span class="fs-8" id="header_data_total" style="padding-left: 3px;padding-right: 3px;"></span> 
														 <span class="fs-8"> Data </span>
													</div>
													<!--end::Status-->
													<!--begin::Description
													<div class="d-flex flex-wrap fw-semibold mb-4 fs-5 text-gray-400" id="header_data_description"></div>
													end::Description-->
												</div>
												
												
												
												<div class="d-flex">
													<?php if($this->ortyd->access_check_insert_data($module) && $module != 'data_inbox') { ?>
													
														<?php if(($module == 'data_laporan' && $this->ortyd->getMasterRole('simpan_laporan', 0) == false)) { ?>
														
														<?php }else{ ?>
														<a style="margin-right:10px;" href="<?php echo $linkcreate; ?>" class="btn btn-danger " id="btn-buat-data"> 
															<?php if(isset($module)){
																if($module == 'data_laporan'){ 
																	$labeling = 'Buat laporan';
																}elseif($module == ''){ 
																	$labeling = 'Buat Data';
																}else{
																	$labeling = 'Buat Data';
																}
															}else{
																$labeling = 'Buat Data';
															}
															?>
															<i class="far fa-edit"></i> <?php echo $labeling; ?>
														</a>
														
														<?php } ?>
														
													<?php } ?>
													<!--begin::Menu-->
													<div class="me-0">
														
														<?php if($this->ortyd->getaccessfilter() == true){ ?>
														<button class="btn btn-sm btn-bg-light btn-active-color-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
															<i class="ki-duotone ki-filter fs-5 me-1">
										<span class="path1"></span>
										<span class="path2"></span>
									</i>Filter Tabel <i class="ki-solid ki-dots-horizontal fs-2x"></i>
														</button>
														<?php } ?>
														<!--begin::Menu 3-->
														<div  style="max-height:250px;overflow: auto !important;" class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold w-200px py-3" data-kt-menu="true">
														
															<div class="menu-item px-3" style="max-height:250px;overflow: auto !important;"> 
																<?php echo $this->ortyd->getviewlistcheck($tablenya, $exclude,$module); ?>
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
		id : id,
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

$( document ).ready(function() {
		$('#checkall_check').click(function(event) {  
            if(this.checked) { 
                $('.checkone_check').each(function() { 
                    this.checked = true;     
                });
            }else{
                $('.checkone_check').each(function() {
                    this.checked = false; 
                });        
            }
        });

});
		


</script>
