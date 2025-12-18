
<?php
	
	$exclude = $exclude;
	$query_column = $this->ortyd->query_column($module, $exclude);
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
	
	$created = $this->ortyd->select2_getname($iddata,'vw_ticket','id','created');
	$created = date_create($created);
	$created = date_format($created,'d F Y H:i:s');
	$ticket_no = $this->ortyd->select2_getname($iddata,'vw_ticket','id','ticket_no');
	$pelapor = $this->ortyd->select2_getname($iddata,'vw_ticket','id','fullname');
?>

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
						
						
							<div class="row">	
						<?php
							if($query_column){
								$indentitas = 0;
								foreach($query_column as $rows_column){ 
								
									$disable = ' readonly="readonly" ';
									$editheader = '';
									$width_column = $this->ortyd->width_column($module,$rows_column['name']);
									$label_name = $this->ortyd->translate_column($module,$rows_column['name']);
									$label_name_text = $label_name;
									if($rows_column['name']){
										$table_change = "'".$module."'";
										$table_change_id = "'".$rows_column['name']."'";
										//$editheader = ' <span style="cursor:pointer" onClick="changeTitle('.$table_change.','.$table_change_id.')"><i class="fa fa-edit"></i></span>';
										if($this->ortyd->getAksesEditNaming() == true){
											$label_name = $label_name.$editheader;
										}else{
											$label_name = $label_name;
										}
									}
									if($rows_column['is_nullable'] == 'NO'){
										$label_name = $label_name.'';
									} 
									
									if($rows_column['type'] == 'text' || $rows_column['type'] == 'longtext'){ ?>
									
										
										<?php if($rows_column['name'] == 'id'){ ?>
											
											
											
										<?php }else{ ?>
										
											<?php if($rows_column['name'] == 'keterangan'){ ?>
											
											<div class="col-lg-<?php echo $width_column; ?> py-3">
												<div class="form-group">
													<label><?php echo $label_name; ?></label>
													<div class="ticket-font-view"><?php echo ${$rows_column['name']}; ?></div>
												</div>
											</div>

											<?php }else{ ?>
										
											<div class="col-lg-<?php echo $width_column; ?> py-3">
												<div class="form-group">
													<label><?php echo $label_name; ?></label>
													<div class="ticket-font-view"><?php echo ${$rows_column['name']}; ?></div>
												</div>
											</div>
											
											<?php } ?>
										
										<?php } ?>
								
								
								<?php }elseif($rows_column['name'] == 'date'){ ?>
									
										<div class="col-lg-<?php echo $width_column; ?> py-3">
											<div class="form-group">
												<label><?php echo $label_name; ?></label>
												<div class="ticket-font-view"><?php echo ${$rows_column['name']}; ?></div>
											</div>
										</div>
										
								<?php }elseif($rows_column['name'] == 'tanggal'){ ?>
								
										<div class="col-lg-4 py-3">
											<div class="form-group">
												<label><?php echo 'Nomor Tiket'; ?></label>
												<div class="ticket-font-view"><?php echo $ticket_no; ?></div>
											</div>
										</div>
										
										<div class="col-lg-8 py-3">
											<div class="form-group">
												<label><?php echo 'Pelapor'; ?></label>
												<div class="ticket-font-view"><?php echo $pelapor; ?></div>
											</div>
										</div>
									
										<div class="col-lg-<?php echo $width_column; ?> py-3">
											<div class="form-group">
												<label><?php echo $label_name; ?></label>
												<div class="ticket-font-view"><?php echo $created; ?></div>
											</div>
										</div>
									
								<?php }elseif($rows_column['name'] == 'email'){ ?>
									
										<div class="col-lg-<?php echo $width_column; ?> py-3">
											<div class="form-group">
												<label><?php echo $label_name; ?></label>
												<div class="ticket-font-view"><?php echo ${$rows_column['name']}; ?></div>
											</div>
										</div>
									
								<?php }elseif($rows_column['name'] == 'nomor'){ ?>
									
										<div class="col-lg-<?php echo $width_column; ?> py-3">
											<div class="form-group">
												<label><?php echo $label_name; ?></label>
												<div class="ticket-font-view"><?php echo ${$rows_column['name']}; ?></div>
											</div>
										</div>
									
								<?php }else{ ?>
								
								
										<?php if($rows_column['name'] == 'kategori_id' || $rows_column['name'] == 'kategori_id_sub'){ ?>
											
										<?php 
												$table_references = $this->ortyd->get_table_reference($module,$rows_column['name']);
												if($table_references != null){ 
													$table = $table_references[0];
													$reference = '';
													$selecttableid = $table_references[1];
													$selecttablename = $table_references[2];
												}else{
													$table = $module;
													$reference = '';
													$selecttableid = 'id';
													$selecttablename = 'name';
												}
												
												${$rows_column['name']} = $this->ortyd->select2_getname(${$rows_column['name']},$table,'id',$selecttablename);
										?>
										
												<div class="col-lg-<?php echo $width_column; ?> py-3">
													<div class="form-group">
														<label><?php echo $label_name; ?></label>
														<div class="ticket-font-view"><?php echo ${$rows_column['name']}; ?></div> 
													</div>
												</div>
										
										<?php }elseif($rows_column['name'] == 'urutan'){ ?>
				
											<div class="col-lg-<?php echo $width_column; ?> py-3">
												<div class="form-group">
													<label><?php echo $label_name; ?></label>
													<div class="ticket-font-view"><?php echo ${$rows_column['name']}; ?></div> 
												</div>
											</div>
										
										
										<?php }elseif($rows_column['name'] == 'file_id'){ ?>
									
											<?php 
												include(APPPATH."views/common/uploadformtable.php");
											?>
									
										<?php }else{ ?>
									
											<div class="col-lg-<?php echo $width_column; ?> py-3">
												<div class="form-group">
													<label><?php echo $label_name; ?></label>
													<div class="ticket-font-view"><?php echo ${$rows_column['name']}; ?></div> 
												</div>
											</div>
										
										<?php } ?>

								<?php }
									$indentitas++;
								}
							} ?>

							
							<div class="card-footer col-lg-12 py-3" id="btn-cancel-submit">
	
							</div>
								
							</div>	
							
							<div class="row">
							
								<div class="col-lg-12">
									<label class="label ticket-font-view">Tanggapi Disini</label>
									<form id="form<?php echo $iddata; ?>"  method="POST" action="<?php echo $action; ?>" enctype="multipart/form-data">
									
										<div class="form-group" style="display:none">
											<input name="ticket_id" value="<?php echo $id; ?>" />
										</div>
										
										<div class="form-group">
											<textarea class="form-control form-control-sm" id="balasan" name="balasan" aria-describedby="balasan" rows="5" required></textarea>
										</div>
										
										<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" class="csrf_token" placeholder="CSRF Invalid" required />
										
										<div class="form-group" style="margin-top:10px">
											<button type="submit" class="btn btn-success fuse-ripple-ready pull-right">
												<i class="fa fa-save"></i> Kirim balasan
											</button>
										</div>
									
									</form>
								</div>
								
								<div class="col-lg-12">
									<div class="table-responsive">
										<table id="datatablesskp" class="table table-striped ">
											<thead>
												<tr>
													<th>Tanggapan</th>
												</tr>
											 </thead>
											 <tbody>
											 </tbody>
										</table>
									</div>
								</div>
								
								<div class="col-lg-12">
									<form id="formtutup<?php echo $iddata; ?>"  method="POST" action="<?php echo $actiontutup; ?>" enctype="multipart/form-data">
									
										<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" class="csrf_token" placeholder="CSRF Invalid" required />

										<div class="form-group" style="text-align:center">
											<button type="submit" class="btn btn-danger fuse-ripple-ready pull-center" >
												<i class="fa fa-times"></i> Tutup Percakapan
											</button>
										</div>
									
									</form>
								</div>
							
							</div>
						
						
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
$( document ).ready(function() {
	
		setTimeout(function() {
			$('.dropzone-panel').hide();
			$('.dropzone-toolbar').hide();
			$('.dropzone-text-muted').hide();
		}, 500);
		getReplied()
			
		$("#form<?php echo $iddata; ?>").submit(function(){

            $.ajax({
              url:$(this).attr("action"),
              data:$(this).serialize(),
              type:$(this).attr("method"),
              dataType: 'formdata',
              beforeSend: function() {
                $("#balasan").attr("disabled",true);
                $("button").attr("disabled",true);
              },
              complete:function() {
                $("#balasan").attr("disabled",false);
				$("#balasan").html('');
				$("#balasan").val('');
                $("button").attr("disabled",false);	
				table.draw();
              },
              success:function(hasil) {
                var txt = $("#balasan");
                if(txt.val().trim().length < 1) {
                  alert("Balasan masih kosong");
                }else{
                   txt.val('');
				   txt.html('');
                }
              }
            })
            return false;
          });
		
})


var table
function getReplied(){
	table = $('#datatablesskp').DataTable({ 
		"initComplete": function(settings, json) {
			//console.log(json.recordsTotal);
			$('#totalreplied').html(json.recordsTotal);
		},
		"responsive": true,
		"bFilter": false,
		"lengthChange": false,
		"info":     false,
		"ordering" : false,
		"dom"	: '<"row"<"col-md-6 text-left"l><"col-md-3 text-right"f><"col-md-3 text-right"B>>rt<"row"<"col-md-6"i><"col-md-6"p>>',
		"buttons" : [ 'copy', 'excel'],
		"oLanguage" : {
			"sProcessing": "<div class='load1 load-wrapper'><div class='loader'>Getting Data ...</div></div>",
				"oPaginate" : {
					"sFtickett": "<<",
					"sPrevious": "<",
					"sNext": ">", 
					"sLast": ">>" 
				}
		},
		"sPaginationType": "full_numbers",
		"lengthMenu": [[5, 10, 25, 50, 100, 500, 1000,-1], [5, 10, 25, 50, 100, 500, 1000,'All']],
		"processing": true,
		"serverSide": true,
		"order": [],
		"ajax": {
			"url": "<?php echo base_url($linkdata); ?>",
			"type": "POST",
			"data": function ( d ) {
				d.ticket_id = <?php echo $iddata; ?>;
				d[csrfName] = csrfHash;
			},
							dataSrc: function(json) {
								console.log(json); // <-- cek isinya
								if (json.csrf_hash) {
									 updateCsrfToken(json.csrf_hash)
									csrfHash = json.csrf_hash;
								}
								return json.data;
							},
																error: function(xhr, error, thrown) {
																	console.warn("AJAX Error:", error);

																	 console.error("AJAX Error:", error);

																	  Swal.fire({
																		icon: 'error',
																		title: 'Gagal Memuat Data!',
																		text: 'Terjadi kesalahan saat mengambil data dari server.',
																		footer: '<a href="<?php base_url('data_ticket'); ?>">Cek koneksi atau hubungi admin.</a>',
																		confirmButtonText: 'Coba Lagi',
																		customClass: {
																		  popup: 'swal-custom-zindex'
																		},
																		didOpen: () => {
																		  // Tambahkan langsung z-index ke elemen swal jika perlu
																		  $('.swal2-container').css('z-index', 99999);
																		}
																	  }).then((result) => {
																		if (result.isConfirmed) {
																		  table.draw(); // reload DataTable jika user klik "Coba Lagi"
																		}
																	  });
																  }
		},
		"columnDefs": [
			{ 
				"targets": [ 0,0 ],
				"orderable": false,
			},
		],
	});
								
	table.buttons().destroy();
}
</script>

