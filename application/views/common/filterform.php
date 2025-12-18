						<div class="row">
						<div class="col-lg-6">				
							<div class="form-group">
								<label>Kategori</label>
								<select class="form-control form-control-sm" id="filter_kategori"></select>
							</div>
						</div>
						
					
						
						<div class="col-lg-2">				
							<div class="form-group">
								<label>Bulan</label>
								<select class="form-control form-control-sm" id="filter_bulan"></select>
							</div>
						</div>
						
						<div class="col-lg-2">				
							<div class="form-group">
								<label>Tahun</label>
								<select class="form-control form-control-sm" id="filter_tahun"></select>
							</div>
						</div>
						
						<div class="col-lg-2">				
							<div class="form-group">
								<label>Minggu Ke</label>
								<select class="form-control form-control-sm" id="filter_minggu_ke"></select>
							</div>
						</div>
						
						<div class="col-lg-12" id="filter_institusi_header">				
							<div class="form-group">
								<label>Institusi</label>
								<select class="form-control form-control-sm" id="filter_institusi"></select>
							</div>
						</div>
						
						<div class="col-lg-6" id="filter_provinsi_header">				
							<div class="form-group">
								<label>Provinsi</label>
								<select class="form-control form-control-sm" id="filter_provinsi"></select>
							</div>
						</div>
						
						<div class="col-lg-6" id="filter_kota_header">				
							<div class="form-group">
								<label>Kota</label>
								<select class="form-control form-control-sm" id="filter_kota"></select>
							</div>
						</div>
						
						<div class="col-lg-3" id="filter_kecamatan_header">				
							<div class="form-group">
								<label>Kecamatan</label>
								<select class="form-control form-control-sm" id="filter_kecamatan"></select>
							</div>
						</div>
						
						<div class="col-lg-3" id="filter_desa_header">				
							<div class="form-group">
								<label>Desa</label>
								<select class="form-control form-control-sm" id="filter_desa"></select>
							</div>
						</div>
						
						<div class="col-lg-12" id="filter_lumbung_header">				
							<div class="form-group">
								<label>Lumbung</label>
								<select class="form-control form-control-sm" id="filter_lumbung"></select>
							</div>
						</div>
						
						
					</div>
					
					
					<script type="text/javascript">
						$(document).ready(function() {
							
							$('#filter_institusi_header').hide();
							$('#filter_provinsi_header').hide();
							$('#filter_kota_header').hide();
							$('#filter_kecamatan_header').hide();
							$('#filter_desa_header').hide();
							$('#filter_lumbung_header').hide();
							
							$("#filter_kategori").select2({	
								allowClear: true,
								width: '100%',		
								ajax: {
									type: "POST",
									url: "<?php echo base_url($headurl.'/select2'); ?>",
									dataType: 'json',
									y: 250,
									data: function (params) {
										return {
											q: params.term, // search term
											table: 'master_kategori',
											reference: '', // search term
											id:'id',
											name:'name',
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
								placeholder: 'Pilih Kategori'
							}).on("select2:select", function(e) { 
								
								setColumn()
								table.draw();
															
							}).on("select2:clearing", function(e) { 
								
								setColumn()
								table.draw();
															
							})
							
							
							$("#filter_minggu_ke").select2({
								allowClear: true,
								width: '100%',		
								ajax: {
									type: "POST",
									url: "<?php echo base_url($headurl.'/select2'); ?>",
									dataType: 'json',
									y: 250,
									data: function (params) {
										return {
											q: params.term, // search term
											table: 'master_minggu_ke',
											reference: '', // search term
											id:'id',
											name:'name',
											month: $('#filter_bulan').val(),
											year: $('#filter_tahun').val(),
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
								placeholder: 'Pilih Minggu Ke'
							}).on("select2:select", function(e) { 
								table.draw();								
							}).on("select2:clearing", function(e) { 
								table.draw();								
							})
							
							$("#filter_bulan").select2({
								allowClear: true,
								width: '100%',		
								ajax: {
									type: "POST",
									url: "<?php echo base_url($headurl.'/select2'); ?>",
									dataType: 'json',
									y: 250,
									data: function (params) {
										return {
											q: params.term, // search term
											table: 'master_bulan',
											reference: '', // search term
											id:'id',
											name:'name',
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
								placeholder: 'Pilih Bulan'
							}).on("select2:select", function(e) { 
								table.draw();									
							}).on("select2:clearing", function(e) { 
								table.draw();								
							})
							
							$("#filter_tahun").select2({	
								allowClear: true,
								width: '100%',		
								ajax: {
									type: "POST",
									url: "<?php echo base_url($headurl.'/select2'); ?>",
									dataType: 'json',
									y: 250,
									data: function (params) {
										return {
											q: params.term, // search term
											table: 'master_tahun',
											reference: '', // search term
											id:'id',
											name:'name',
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
								placeholder: 'Pilih Tahun'
							}).on("select2:select", function(e) { 
								table.draw();									
							}).on("select2:clearing", function(e) { 
								table.draw();								
							})
							
							$("#filter_institusi").select2({
								allowClear: true,
								width: '100%',		
								ajax: {
									type: "POST",
									url: "<?php echo base_url($headurl.'/select2'); ?>",
									dataType: 'json',
									y: 250,
									data: function (params) {
										return {
											q: params.term, // search term
											table: 'master_institusi',
											reference: '', // search term
											id:'id',
											name:'name',
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
								placeholder: 'Pilih Institusi'
							}).on("select2:select", function(e) { 
								table.draw();									
							}).on("select2:clearing", function(e) { 
								table.draw();								
							})
							
							$("#filter_provinsi").select2({
								allowClear: true,
								width: '100%',		
								ajax: {
									type: "POST",
									url: "<?php echo base_url($headurl.'/select2'); ?>",
									dataType: 'json',
									y: 250,
									data: function (params) {
										return {
											q: params.term, // search term
											table: 'master_area_provinsi',
											reference: '', // search term
											id:'id',
											name:'name',
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
								placeholder: 'Pilih Provinsi'
							}).on("select2:select", function(e) { 
								$('#filter_kota').val(0).trigger('change');
								$('#filter_kecamatan').val(0).trigger('change');
								$('#filter_desa').val(0).trigger('change');
								$('#filter_lumbung').val(0).trigger('change');
								table.draw();								
							}).on("select2:clearing", function(e) { 
								$('#filter_kota').val(0).trigger('change');
								$('#filter_kecamatan').val(0).trigger('change');
								$('#filter_desa').val(0).trigger('change');
								$('#filter_lumbung').val(0).trigger('change');
								table.draw();								
							})
							
							$("#filter_kota").select2({
								allowClear: true,
								width: '100%',		
								ajax: {
									type: "POST",
									url: "<?php echo base_url($headurl.'/select2'); ?>",
									dataType: 'json',
									y: 250,
									data: function (params) {
										return {
											q: params.term, // search term
											table: 'master_area_kota',
											reference: $("#filter_provinsi").val(), // search term
											id:'id',
											name:'name',
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
								placeholder: 'Pilih Kota'
							}).on("select2:select", function(e) { 
								$('#filter_kecamatan').val(0).trigger('change');
								$('#filter_desa').val(0).trigger('change');
								$('#filter_lumbung').val(0).trigger('change');
								table.draw();									
							}).on("select2:clearing", function(e) {
								$('#filter_kecamatan').val(0).trigger('change');
								$('#filter_desa').val(0).trigger('change');	
								$('#filter_lumbung').val(0).trigger('change');
								table.draw();								
							})
							
							$("#filter_kecamatan").select2({
								allowClear: true,
								width: '100%',		
								ajax: {
									type: "POST",
									url: "<?php echo base_url($headurl.'/select2'); ?>",
									dataType: 'json',
									y: 250,
									data: function (params) {
										return {
											q: params.term, // search term
											table: 'master_area_kecamatan',
											reference: $("#filter_kota").val(), // search term
											id:'id',
											name:'name',
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
								placeholder: 'Pilih Kecamatan'
							}).on("select2:select", function(e) { 
								$('#filter_desa').val(0).trigger('change');
								$('#filter_lumbung').val(0).trigger('change');
								table.draw();									
							}).on("select2:clearing", function(e) { 
								$('#filter_desa').val(0).trigger('change');	
								$('#filter_lumbung').val(0).trigger('change');
								table.draw();								
							})
							
							$("#filter_desa").select2({
								allowClear: true,
								width: '100%',		
								ajax: {
									type: "POST",
									url: "<?php echo base_url($headurl.'/select2'); ?>",
									dataType: 'json',
									y: 250,
									data: function (params) {
										return {
											q: params.term, // search term
											table: 'master_area_desa',
											reference: $("#filter_kecamatan").val(), // search term
											id:'id',
											name:'name',
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
								placeholder: 'Pilih Desa'
							}).on("select2:select", function(e) { 
								$('#filter_lumbung').val(0).trigger('change');
								table.draw();									
							}).on("select2:clearing", function(e) {
								$('#filter_lumbung').val(0).trigger('change');
								table.draw();								
							})
							
							$("#filter_lumbung").select2({
								allowClear: true,
								width: '100%',		
								ajax: {
									type: "POST",
									url: "<?php echo base_url($headurl.'/select2'); ?>",
									dataType: 'json',
									y: 250,
									data: function (params) {
										return {
											q: params.term, // search term
											table: 'master_lokasi_pemantauan',
											reference: '', // search term
											id:'id',
											name:'jenis_pengelola',
											area_provinsi_id: $('#filter_provinsi').val(),
											area_kota_id: $('#filter_kota').val(),
											area_kecamatan_id: $('#filter_kecamatan').val(),
											area_desa_id: $('#filter_desa').val(),
											lokasi_pemantauan_id: $('#filter_lumbung').val(),
											kategori_id: $('#filter_kategori').val(),
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
								placeholder: 'Pilih Lumbung'
							}).on("select2:select", function(e) { 
								table.draw();									
							}).on("select2:clearing", function(e) { 
								table.draw();								
							})
							
						})
							
							function setColumn(){
							if($('#filter_kategori').val() == '6'){
									$('#filter_provinsi_header').show();
									$('#filter_kota_header').hide();
									$('#filter_kecamatan_header').hide();
									$('#filter_desa_header').hide();
									$('#filter_lumbung_header').hide();
									$('#filter_institusi_header').show();
									
									$( "#filter_provinsi_header" ).removeClass( "col-lg-6" );
									$( "#filter_provinsi_header" ).addClass( "col-lg-12" );
									
									$( "#filter_provinsi_header" ).removeClass( "col-lg-3" );
									$( "#filter_provinsi_header" ).addClass( "col-lg-12" );
		
								}else if($('#filter_kategori').val() == '5'){
									$('#filter_institusi_header').hide();
									$('#filter_provinsi_header').show();
									$('#filter_kota_header').hide();
									$('#filter_kecamatan_header').hide();
									$('#filter_desa_header').hide();
									$('#filter_lumbung_header').hide();
									 
									$( "#filter_provinsi_header" ).removeClass( "col-lg-6" );
									$( "#filter_provinsi_header" ).addClass( "col-lg-12" );


								}else if($('#filter_kategori').val() == '4'){
									$('#filter_institusi_header').hide();
									$('#filter_provinsi_header').show();
									$('#filter_kota_header').show();
									$('#filter_kecamatan_header').hide();
									$('#filter_desa_header').hide();
									$('#filter_lumbung_header').hide();
									
									$( "#filter_provinsi_header" ).removeClass( "col-lg-12" );
									$( "#filter_provinsi_header" ).addClass( "col-lg-6" );
									
									$( "#filter_kota_header" ).removeClass( "col-lg-3" );
									$( "#filter_kota_header" ).addClass( "col-lg-6" );
									
								}else if($('#filter_kategori').val() == '2'){
									$('#filter_institusi_header').hide();
									$('#filter_provinsi_header').show();
									$('#filter_kota_header').show();
									$('#filter_kecamatan_header').show();
									$('#filter_desa_header').show();
									$('#filter_lumbung_header').show();
									
									$( "#filter_provinsi_header" ).removeClass( "col-lg-12" );
									$( "#filter_provinsi_header" ).addClass( "col-lg-6" );
									
									$( "#filter_provinsi_header" ).removeClass( "col-lg-6" );
									$( "#filter_provinsi_header" ).addClass( "col-lg-3" );
									
									$( "#filter_kota_header" ).removeClass( "col-lg-6" );
									$( "#filter_kota_header" ).addClass( "col-lg-3" );
									
								}else{
									$('#filter_institusi_header').hide();
									$('#filter_provinsi_header').hide();
									$('#filter_kota_header').hide();
									$('#filter_kecamatan_header').hide();
									$('#filter_desa_header').hide();
									$('#filter_lumbung_header').hide();
									
									$( "#filter_provinsi_header" ).removeClass( "col-lg-12" );
									$( "#filter_provinsi_header" ).addClass( "col-lg-6" );
									
								}
															
								$('#filter_institusi').val(0).trigger('change');
								$('#filter_provinsi').val(0).trigger('change');
								$('#filter_kota').val(0).trigger('change');
								$('#filter_kecamatan').val(0).trigger('change');
								$('#filter_desa').val(0).trigger('change');
								$('#filter_lumbung').val(0).trigger('change');
						}
						</script>