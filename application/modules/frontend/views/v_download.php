<section class="page-header page-header-modern bg-color-quaternary page-header-lg border-0 m-0">
	<div class="container position-relative z-index-2">
		<div class="row text-center text-md-start py-3">
			<div class="col-md-8 order-2 order-md-1 align-self-center p-static">
				<h1 class="font-weight-bold text-color-dark text-10 mb-0">
					<?php echo $title; ?>
				</h1>
			</div>
			<div class="col-md-4 order-1 order-md-2 align-self-center">
				<ul class="breadcrumb breadcrumb-dark font-weight-bold d-block text-md-end text-4 mb-0">
					<li>
						<a href="<?php echo base_url(); ?>" class="text-decoration-none text-dark">Beranda</a>
					</li>
					<li class="text-upeercase active text-color-primary">
						<?php echo $title; ?>
					</li>
				</ul>
			</div>
		</div>
	</div>
</section>

<div class="container py-5 my-3">
	<div class="row">
	
		<?php include('v_post_sidebar_dua.php'); ?>
		
		<div class="col-lg-9 mb-4 mb-lg-0">
		
			<div class="table-responsive">
						<table id="datatablesskp" class="table table-striped ">
							<thead>
								<tr>
									<th>No.</th>
								<?php
									$total_rows = 0;
									$exclude = $exclude_table;
									$query_column = $this->ortyd->query_column($table_berkas, $exclude);
									if($query_column){
										$x=1;
										foreach($query_column as $rows_column){
											if($rows_column['name'] == 'file_id'){
												echo '<th>Berkas</th>';
											}elseif($rows_column['name'] == 'type'){
												echo '<th>Tipe</th>';
											}elseif($rows_column['name'] == 'name'){
												echo '<th>Nama</th>';
											}elseif($rows_column['name'] == 'date'){
												echo '<th>Tanggal</th>';
											}elseif($rows_column['name'] != $identity_id && $rows_column['name'] != 'active'){
												echo '<th>'.strtoupper($rows_column['name']).'</th>';
											}
											$x++;
										}
										$total_rows = $x;
									}
								?>
								</tr>
							 </thead>
							 <tbody>
							 </tbody>
						</table>
					</div>
					<script type="text/javascript">
						var table;
						var type = 1;
						$(document).ready(function() {
							
							table = $('#datatablesskp').DataTable({
										
										"responsive": false,
										"dom"	: '<"row"<"col-md-6 text-left"l><"col-md-6 text-right"f><"col-md-2 text-right"B>>rt<"row"<"col-md-6"i><"col-md-6"p>>',
										"buttons" : [ 'copy', 
										{
											extend: 'excel',
											action: newExportAction
										}],
										"oLanguage" : {
											"sProcessing": "<div class='load1 load-wrapper'><div class='loader'>Mengambil Data ...</div></div>",
											 "oPaginate" : {
												"sFirst": "<<",
												"sPrevious": "<",
												"sNext": ">", 
												"sLast": ">>" 
											},
											"sInfo": "Menampilkan _START_ sampai _END_ dari _TOTAL_",
											"sEmptyTable": "Tidak ada data yang tersedia",
											"sLengthMenu": "Menampilkan _MENU_ Entri",
											"sSearch": "Cari",
											"sInfoEmpty": ""
										},
										"sPaginationType": "full_numbers",
										"lengthMenu": [[5, 10, 25, 50, 100, 500, 1000,-1], [5, 10, 25, 50, 100, 500, 1000,'Semua']],
										"processing": true,
										"serverSide": true,
										"order": [],
										"ajax": {
											"url": "<?php echo base_url($linkdata); ?>",
											"type": "POST",
											"data": function ( d ) {
												d.active = type;
												d.csrf_ortyd_simpktn_siswaspk_name = "<?php echo $this->security->get_csrf_hash(); ?>";
												d.type = '<?php echo $type; ?>';
											}
										},
										"columnDefs": [
												{ 
													"targets": [ 0,0 ],
													"orderable": false,
												},
												{ 
													"targets": [ 0,4 ],
													"orderable": false,
													"className": "text-center"
												}
										],
								});
								
								table.buttons().remove()
												 
						});
						
						var oldExportAction = function (self, e, dt, button, config) {
							if (button[0].className.indexOf('buttons-excel') >= 0) {
								if ($.fn.dataTable.ext.buttons.excelHtml5.available(dt, config)) {
									$.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt, button, config);
								}
								else {
									$.fn.dataTable.ext.buttons.excelFlash.action.call(self, e, dt, button, config);
								}
							} else if (button[0].className.indexOf('buttons-print') >= 0) {
								$.fn.dataTable.ext.buttons.print.action(e, dt, button, config);
							}
						};

						var newExportAction = function (e, dt, button, config) {
							var self = this;
							var oldStart = dt.settings()[0]._iDisplayStart;

							dt.one('preXhr', function (e, s, data) {
								// Just this once, load all data from the server...
								data.start = 0;
								data.length = 2147483647;

								dt.one('preDraw', function (e, settings) {
									// Call the original action function 
									oldExportAction(self, e, dt, button, config);

									dt.one('preXhr', function (e, s, data) {
										// DataTables thinks the first item displayed is index 0, but we're not drawing that.
										// Set the property to what it was before exporting.
										settings._iDisplayStart = oldStart;
										data.start = oldStart;
									});

									// Reload the grid with the original page. Otherwise, API functions like table.cell(this) don't work properly.
									setTimeout(dt.ajax.reload, 0);

									// Prevent rendering of the full data to the DOM
									return false;
								});
							});

							// Requery the server with the new one-time export settings
							dt.ajax.reload();
						};
						
						function get_data(data){
							type = data;
							table.draw();
						}
						
						</script>
							
		</div>
		
		

	</div>
</div>