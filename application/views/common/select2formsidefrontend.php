<?php 
	if(!isset($readonlyselect)){
		$readonlyselect = '';
	}
	
	if(!isset($linkcustom)){
		$linkcustom = 'select2';
	}
	
	if(!isset($isadditem)){
		$isadditem = '';
	}
	
	$table_references = $this->ortyd->get_table_reference($module,$rows_column['name']);
	if($table_references != null){ 
		$table = $table_references[0];
		if($table == 'translate_table_select_option'){
			$reference = '';
			$selectnested = 'custom';
			$selectnestedfieldid = $table_references[6];
			$selectnestedrefid = 'table_select_id';
		}else{
			$reference = '';
			$selectnested = $table_references[3];
			if($selectnested == 1 || $selectnested == '1'){
				$selectnestedfieldid = $table_references[4];
				$selectnestedrefid = $table_references[5];
			}else{
				$selectnestedfieldid = '';
				$selectnestedrefid = '';
				$selectnested = 0;
			}
		}
		$selecttableid = $table_references[1];
		$selecttablename = $table_references[2];
		
	}else{
		$table = $module;
		$reference = '';
		$selecttableid = 'id';
		$selecttablename = 'id';
		$selectnested = 0;
		$selectnestedfieldid = '';
		$selectnestedrefid = '';
	}
?>
	<div draggable="true" class="drag-item col-lg-<?php echo $width_column; ?>" id="<?php echo $rows_column['name'].'_header'; ?>">				
		<div class="form-group">
			<div class="row">
				<div class="col-lg-12">
					<label><?php echo $label_name; ?></label>
				</div>
				<div class="col-lg-12">
				
					<select class="form-control form-control-sm <?php if($rows_column['is_nullable'] == 'NO'){echo ' required ';} ?>" name="<?php echo $rows_column['name']; ?>" id="<?php echo $rows_column['name']; ?>" <?php if($rows_column['is_nullable'] == 'NO'){echo ' required';} ?> <?php echo $disable; ?>  placeholder="<?php echo 'Input '.$label_name_text; ?>">
						<?php if($selecttablename != 'name') { ?>
							<option value="<?php echo ${$rows_column['name']}; ?>"><?php echo $this->ortyd->select2_getname(${$rows_column['name']},$table,'id',$selecttablename); ?></option>
						<?php }elseif(${$rows_column['name']} != '') { ?>
							<option value="<?php echo ${$rows_column['name']}; ?>"><?php echo $this->ortyd->select2_getname(${$rows_column['name']},$table,'id','name'); ?></option>
						<?php } ?>
					</select>
																			
					<script>
					
						

						$( document ).ready(function() {
							
							$select = $("#"+'<?php echo $rows_column['name']; ?>').select2({	
								width: '100%',		
								<?php if($readonlyselect == ''){ ?>
								allowClear: true,
								<?php } ?>
								ajax: {
									type: "POST",
									url: "<?php echo base_url($headurl.'/'.$linkcustom); ?>",
									dataType: 'json',
									delay: 250,
									data: function (params) {
										return {
											q: params.term, // search term
											<?php if($readonlyselect != ''){ ?>
											table: '<?php echo 'master_readonly'; ?>',
											<?php }else{ ?>
											table: '<?php echo $table; ?>',
											<?php } ?>
											<?php if($selectnested == 'custom'){ ?>
												reference_id: '<?php echo $selectnestedrefid; ?>', // search term
												reference: '<?php echo $selectnestedfieldid ?? 0; ?>', // search term
											<?php }elseif($selectnested != 0){ ?>
												reference_id: '<?php echo $selectnestedrefid; ?>', // search term
												reference: $("#<?php echo $selectnestedfieldid; ?>").val() || "0", // search term
											<?php } ?>
											id:'<?php echo $selecttableid; ?>',
											name:'<?php echo $selecttablename; ?>',
											page: params.page,
											<?php echo $this->security->get_csrf_token_name(); ?> : csrfHash 
										};
									},
									processResults: function (data, params) {
										updateCsrfToken(data.csrf_hash);
										params.page = params.page || 1;
										
										let results = $.map(data.items, function (item) {
											return {
												id: item.id,
												text: item.name
											};
										});

										<?php if($isadditem != ''){ ?>
											// Tambahkan opsi "Tambah baru"
											results.push({
												id: '__addnew__',
												text: '➕ Tambah data baru'
											});
										<?php } ?>
										

										return {
											results: results,
											pagination: {
												more: (params.page * 30) < data.total_count
											}
										};
									},
									cache: true
								},
								placeholder: 'Pilih ' + '<?php echo $label_name_text; ?>'
							}).on("select2:select", function(e) { 
								if(e.params.data.id === '__addnew__') {
									// Buka modal atau redirect ke form input baru
									$('#<?php echo $rows_column['name']; ?>').val(null).trigger('change'); // Reset dulu

									// Contoh: tampilkan modal
									if (typeof showAddNewModal === 'function') {
										showAddNewModal('<?php echo $table; ?>', '<?php echo $selecttablename; ?>', function(newItem) {
											let newOption = new Option(newItem.text, newItem.id, true, true);
											$('#<?php echo $rows_column['name']; ?>').append(newOption).trigger('change');
										});
									} else {
										alert('Fitur tambah data belum diimplementasikan.');
									}
								}
							})
							
							
							
							<?php if($readonlyselect != ''){ ?>
								$select.data('select2').$container.addClass('select2_<?php echo $rows_column['name']; ?>');
								readonly_select($(".select2_<?php echo $rows_column['name']; ?>"), true);
							<?php } ?>
					
						})
						
						
						function showAddNewModal(table, column, callback) {
							$.post('<?php echo base_url("dashboard/get_table_columns"); ?>', {
								table: table,
								<?php echo $this->security->get_csrf_token_name(); ?>: csrfHash
							}, function(res) {
								updateCsrfToken(res.csrf_hash);

								if (!res.success || !res.inputs) {
									Swal.fire('Error', 'Gagal mengambil struktur tabel', 'error');
									return;
								}

								let htmlInputs = '';
								// Tambahkan list field yang ingin dikecualikan dari input
								const excludedFields = ['color','created', 'createdid', 'modified', 'modifiedid', 'active', 'slug'];

								res.inputs.forEach(col => {
									if (excludedFields.includes(col.name)) return; // skip kolom yang dikecualikan

									const inputType = (col.type === 'text' || col.max_length > 255) ? 'textarea' : 'input';
									if (inputType === 'input') {
										htmlInputs += `<input id="swal-input-${col.name}" class="swal2-input" placeholder="${col.name}">`;
									} else {
										htmlInputs += `<textarea id="swal-input-${col.name}" class="swal2-textarea" placeholder="${col.name}"></textarea>`;
									}
								});
								Swal.fire({
									title: 'Tambah Data Baru',
									html: htmlInputs,
									focusConfirm: false,
									showCancelButton: true,
									confirmButtonText: 'Simpan',
									cancelButtonText: 'Batal',
									preConfirm: () => {
										const values = {};
										let errorMsg = '';

										// Loop semua kolom yang dikirim dari server
										res.inputs.forEach(col => {
											if (excludedFields.includes(col.name)) return;

											const el = document.getElementById('swal-input-' + col.name);
											if (!el) return;

											const val = el.value.trim();
											if (!val && col.notnull == 1) {
												errorMsg = `${col.name} harus diisi`;
											}
											values[col.name] = val;
										});

										if (errorMsg) {
											Swal.showValidationMessage(errorMsg);
											return false;
										}

										return values;
									}
								}).then((result) => {
									if (result.isConfirmed) {
										const dataToSend = {
											...result.value,
											table : table,
											<?php echo $this->security->get_csrf_token_name(); ?>: csrfHash
										};

										$.post('<?php echo base_url("dashboard/add_item_ajax"); ?>', dataToSend, function(response) {
											updateCsrfToken(response.csrf_hash);

											if (response.success) {
												callback({ id: response.id, text: response.name });
												Swal.fire('Berhasil!', 'Data berhasil ditambahkan.', 'success');
											} else {
												Swal.fire('Gagal!', response.message || 'Data gagal disimpan.', 'error');
											}
										}, 'json');
									}
								});
							}, 'json');
						}


					</script>
		
				</div>
			</div>
			
		</div>
	</div>