<!--begin::Navbar-->
							<div class="card mb-6 mb-xl-9">
								<div class="card-body pt-9 pb-0">
									<!--begin::Details-->
									<div class="row no-margin">
										
										<div class="col-lg-12">
											<!--begin::Head-->
											<div class="row" style="    margin-bottom: 5px;">
												<!--begin::Details-->
												
												<!--begin::Wrapper-->
										
												<div class="col-lg-9">
												
												
													<!--begin::Status-->
													<div style="font-size: 10px;" class="text-gray-400" id="header_laporan_status"></div>
													<div class="d-flex align-items-center mb-1">
														<a href="javascript:;" class="text-gray-800 text-hover-primary fs-2 fw-bold me-3" id="header_laporan_no">-</a>
														<span class="badge me-auto" id="header_laporan_nama">-</span>
													</div>
													
													<!--end::Status-->
													<!--begin::Description-->
													<span class="symbol-group symbol-hover mb-3" id="header_total_required">
													<!--begin::User-->
														<input type="number" id="total_required" style="display:none" />
													<!--end::User-->
	
													</span>
													
													<!--end::Description-->
													
													
												
												</div>
												

												<div class="col-lg-3" id="btn-aksi-submit">
												
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
									<ul id="header_laporan_menu" class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bold">
									
									</ul>
									<!--end::Nav-->
								</div>
							</div>
							<!--end::Navbar-->

<?php 
$tipeinput = 'edit';
if(isset($_GET['tipe'])){
	$tipeinput = $_GET['tipe'];
}
?>							
<script>
							
$( document ).ready(function() {
	$('#btn-aksi-action').hide();
	$('#btn-aksi-submit').html($('#btn-aksi-action').html())
	$('#btn-aksi-action').html('')
})

function getHeaderLaporan(id,tipe,iddata = null, jenis = null){
	$.post('<?php echo base_url('data_laporan/getheaderlaporan'); ?>',{ 
		laporan_id : id, 
		tipe : tipe, 
		jenis : jenis, 
		iddata : iddata,
		tipeinput : '<?php echo $tipeinput; ?>',
		<?php echo $this->security->get_csrf_token_name(); ?> : "<?php echo $this->security->get_csrf_hash(); ?>" 
	}, function (data) {
		var obj = data
		updateCsrfToken(obj.csrf_hash)
		if(obj.message == "success"){
			var datanya = obj.data;
			console.log(datanya)
			$("#header_laporan_menu").html(datanya.laporan_menu);
			$("#header_laporan_no").html(datanya.laporan_no);
			$("#header_laporan_nama").html(datanya.laporan_nama);
			$("#header_laporan_status").html(datanya.laporan_status);
			$("#total_required").val(datanya.laporan_required);
		}else{
			
		}
	}, 'json');
}

var linkdata = '#';
function onLink(link){
	linkdata = link
	<?php if($laporan_status_id == 0 || $laporan_status_id == 100 || $laporan_status_id == 99){ ?>
		$('#kt_docs_formvalidation_text_draft').click();
	<?php }else{ ?>
	setTimeout(function() { // Setelah klik, kasih delay kecil supaya klik kejadian dulu
		window.location.href = linkdata; // Redirect ke link tujuan
	}, 100); // 300 ms (0.3 detik) delay cukup
	<?php } ?>
}


</script>
