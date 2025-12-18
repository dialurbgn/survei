<?php 

	if(!isset($selecttableid)){
		$selecttableid = '';
	}
	
	if(!isset($selecttablename)){
		$selecttablename = '';
	}
	
	if(!isset($readonlyselect)){
		$readonlyselect = '';
	}
	
	if(!isset($linkcustom)){
		$linkcustom = 'select2';
	}
	
	if(!isset($isadditem)){
		$isadditem = '';
	}
	
	if(!isset($selecttable)){
		$selecttable = '';
	}else{
		$table = $selecttable;
	}
	
	// Check if this is popup mode
	$is_popup = isset($is_popup) && $is_popup === true;
	$field_id_suffix = $is_popup ? '_popup' : '';
	$container_class = $is_popup ? 'popup-form-container' : '';
	
	// Multiple select configuration
	$is_multiple = isset($is_multiple) && $is_multiple === true;
	$multiple_table = isset($multiple_table) ? $multiple_table : '';
	$multiple_foreign_key = isset($multiple_foreign_key) ? $multiple_foreign_key : '';
	$multiple_reference_key = isset($multiple_reference_key) ? $multiple_reference_key : '';
	
	$table_references = $this->ortyd->get_table_reference($module,$rows_column['name']);
	if($table_references != null){ 
	
		if($selecttable == ''){
			$table = $table_references[0];
		}
		
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
		if($selecttableid == ''){
			$selecttableid = $table_references[1];
		}
		
		if($selecttableid == ''){
			$selecttablename = $table_references[2];
		}
		
		
		
	}else{
		
		if($selecttable == ''){
			$table = $module;
		}
		
		$reference = '';
		
		if($selecttableid == ''){
			$selecttableid = 'id';
		}
		
		if($selecttableid == ''){
			$selecttablename = 'id';
		}
		
		
		
		$selectnested = 0;
		$selectnestedfieldid = '';
		$selectnestedrefid = '';
	}
	
	// Adjust layout for popup
	$form_group_class = $is_popup ? 'form-group mb-3' : 'form-group';
	$row_class = $is_popup ? 'row' : 'row';
	$label_col = $is_popup ? 'col-lg-3' : 'col-lg-3';
	$input_col = $is_popup ? 'col-lg-9' : 'col-lg-9';
	$select_id = $rows_column['name'] . $field_id_suffix;
	
	// Get existing selected values for multiple select
	$existing_values = [];
	if($is_multiple && $multiple_table && isset($id) && $id > 0) {
		$this->db->select($multiple_reference_key);
		$this->db->where($multiple_foreign_key, $id);
		$this->db->where('active', 1);
		$query_existing = $this->db->get($multiple_table);
		if($query_existing->num_rows() > 0) {
			foreach($query_existing->result() as $row) {
				$existing_values[] = $row->{$multiple_reference_key};
			}
		}
	}
?>
	<div id="<?php echo $rows_column['name'].'_header'.$field_id_suffix; ?>" draggable="<?php echo $is_popup ? 'false' : 'true'; ?>" class="<?php echo $is_popup ? '' : 'drag-item'; ?> col-lg-<?php echo $width_column; ?> py-3">				
		<div class="<?php echo $form_group_class; ?>">
			<div class="<?php echo $row_class; ?>">
				<div class="<?php echo $label_col; ?>">
					<label><?php echo $label_name; ?></label>
					<?php if($is_multiple): ?>
						<small class="text-muted d-block">Bisa pilih lebih dari satu</small>
					<?php endif; ?>
				</div>
				<div class="<?php echo $input_col; ?>">
				
					<select class="form-control form-control-sm <?php echo $is_popup ? 'select2-popup' : ''; ?> <?php if($rows_column['is_nullable'] == 'NO'){echo ' required ';} ?>" 
						name="<?php echo $rows_column['name'] . ($is_multiple ? '[]' : ''); ?>" 
						id="<?php echo $select_id; ?>" 
						<?php if($is_multiple): ?>multiple="multiple"<?php endif; ?>
						<?php if($rows_column['is_nullable'] == 'NO'){echo ' required';} ?> 
						<?php echo $disable; ?>  
						placeholder="<?php echo 'Input '.$label_name_text; ?>">
						
						<?php if(!$is_multiple): ?>
							<?php if($selecttablename != 'name') { ?>
								<option value="<?php echo ${$rows_column['name']}; ?>"><?php echo $this->ortyd->select2_getname(${$rows_column['name']},$table,'id',$selecttablename); ?></option>
							<?php }elseif(${$rows_column['name']} != '') { ?>
								<option value="<?php echo ${$rows_column['name']}; ?>"><?php echo $this->ortyd->select2_getname(${$rows_column['name']},$table,'id','name'); ?></option>
							<?php } ?>
						<?php else: ?>
							<?php if(!empty($existing_values)): ?>
								<?php foreach($existing_values as $val): ?>
									<option value="<?php echo $val; ?>" selected><?php echo $this->ortyd->select2_getname($val,$table,'id',$selecttablename); ?></option>
								<?php endforeach; ?>
							<?php endif; ?>
						<?php endif; ?>
					</select>
					
					<?php if($is_multiple): ?>
						<!-- Hidden input to store multiple values as JSON -->
						<input type="hidden" name="<?php echo $rows_column['name']; ?>_multiple_data" id="<?php echo $select_id; ?>_data" value="<?php echo htmlspecialchars(json_encode($existing_values)); ?>" >
					<?php endif; ?>
																			
					<script>
					<?php if ($is_popup): ?>
					// Popup mode initialization - will be called after modal is shown
					$(document).on('shown.bs.modal', '#popupFormModal', function() {
						initializeSelect2Popup_<?php echo $rows_column['name']; ?>();
					});
					
					function initializeSelect2Popup_<?php echo $rows_column['name']; ?>() {
					<?php else: ?>
					$(document).ready(function() {
					<?php endif; ?>
						
						var select2Config = {
							width: '100%',
							<?php if($readonlyselect == ''): ?>
							allowClear: true,
							<?php endif; ?>
							<?php if($is_multiple): ?>
							multiple: true,
							maximumSelectionLength: 20, // Limit maximum selections
							<?php endif; ?>
							<?php if ($is_popup): ?>
							dropdownParent: $('#popupFormModal'),
							<?php endif; ?>
							ajax: {
								type: "POST",
								url: "<?php echo base_url($headurl.'/'.$linkcustom); ?>",
								dataType: 'json',
								delay: 250,
								data: function (params) {
									return {
										q: params.term, // search term
										<?php if($readonlyselect != ''): ?>
										table: '<?php echo 'master_readonly'; ?>',
										<?php else: ?>
										table: '<?php echo $table; ?>',
										<?php endif; ?>
										<?php if($selectnested == 'custom'): ?>
											reference_id: '<?php echo $selectnestedrefid; ?>', // search term
											reference: '<?php echo $selectnestedfieldid ?? 0; ?>', // search term
										<?php elseif($selectnested != 0): ?>
											reference_id: '<?php echo $selectnestedrefid; ?>', // search term
											reference: $("#<?php echo $selectnestedfieldid . $field_id_suffix; ?>").val() || "0", // search term
										<?php endif; ?>
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

									<?php if($isadditem != '' && $readonlyselect == ''): ?>
										// Tambahkan opsi "Tambah baru" hanya jika tidak readonly
										results.push({
											id: '__addnew__',
											text: '➕ Tambah data baru'
										});
									<?php endif; ?>
									

									return {
										results: results,
										pagination: {
											more: (params.page * 30) < data.total_count
										}
									};
								},
								cache: true
							},
							placeholder: 'Pilih ' + '<?php echo $label_name_text; ?>' + '<?php echo $is_multiple ? " (bisa lebih dari satu)" : ""; ?>'
						};
						
						$select = $("#<?php echo $select_id; ?>").select2(select2Config)
						<?php if($is_multiple): ?>
						.on("select2:select select2:unselect", function(e) {
							// Update hidden input when selection changes
							var selectedValues = $(this).val() || [];
							$("#<?php echo $select_id; ?>_data").val(JSON.stringify(selectedValues));
						})
						<?php endif; ?>
						.on("select2:select", function(e) { 
							if(e.params.data.id === '__addnew__') {
								// Buka modal atau redirect ke form input baru
								<?php if($is_multiple): ?>
								// For multiple select, remove the __addnew__ option after click
								var currentValues = $('#<?php echo $select_id; ?>').val() || [];
								currentValues = currentValues.filter(val => val !== '__addnew__');
								$('#<?php echo $select_id; ?>').val(currentValues).trigger('change');
								<?php else: ?>
								$('#<?php echo $select_id; ?>').val(null).trigger('change'); // Reset dulu
								<?php endif; ?>

								// Contoh: tampilkan modal
								if (typeof showAddNewModal === 'function') {
									showAddNewModal('<?php echo $table; ?>', '<?php echo $selecttablename; ?>', function(newItem) {
										let newOption = new Option(newItem.text, newItem.id, true, true);
										$('#<?php echo $select_id; ?>').append(newOption).trigger('change');
										<?php if($is_multiple): ?>
										// Update hidden input
										var selectedValues = $('#<?php echo $select_id; ?>').val() || [];
										$("#<?php echo $select_id; ?>_data").val(JSON.stringify(selectedValues));
										<?php endif; ?>
									});
								} else {
									alert('Fitur tambah data belum diimplementasikan.');
								}
							}
						});
						
						<?php if($readonlyselect != ''): ?>
							$select.data('select2').$container.addClass('select2_<?php echo $rows_column['name'] . $field_id_suffix; ?>');
							readonly_select($(".select2_<?php echo $rows_column['name'] . $field_id_suffix; ?>"), true);
							
							<?php if($is_multiple): ?>
							// Disable removing items for readonly multiple select
							$select.on('select2:unselecting', function(e) {
								e.preventDefault();
								return false;
							});
							
							// Hide remove buttons for readonly multiple select
							$(".select2_<?php echo $rows_column['name'] . $field_id_suffix; ?>").find('.select2-selection__choice__remove').hide();
							
							// Additional styling for readonly multiple select
							$(".select2_<?php echo $rows_column['name'] . $field_id_suffix; ?>").find('.select2-selection__choice').css({
								'background-color': '#e9ecef',
								'border-color': '#ced4da',
								'color': '#6c757d'
							});
							<?php endif; ?>
						<?php endif; ?>
				
					<?php if ($is_popup): ?>
					}
					<?php else: ?>
					});
					<?php endif; ?>
					
					function showAddNewModal<?php echo $field_id_suffix; ?>(table, column, callback) {
						// Pastikan modal baru memiliki z-index yang lebih tinggi
						var originalZIndex = $('#popupFormModal').css('z-index');
						
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
								<?php if ($is_popup): ?>
								// Pastikan SweetAlert muncul di atas popup modal
								backdrop: false,
								allowOutsideClick: false,
								didOpen: () => {
									$('.swal2-container').css('z-index', parseInt(originalZIndex) + 1000);
								},
								<?php endif; ?>
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
	
	<?php if ($is_popup): ?>
	<style>
	/* Fix select2 dropdown z-index untuk popup */
	.select2-container--default .select2-dropdown {
		z-index: 9999 !important;
	}
	
	.select2-container--default .select2-dropdown--below {
		z-index: 9999 !important;
	}
	
	.select2-container--default .select2-dropdown--above {
		z-index: 9999 !important;
	}
	
	/* Pastikan dropdown muncul di dalam modal */
	#popupFormModal .select2-container {
		width: 100% !important;
	}
	
	#popupFormModal .select2-selection {
		border-color: #ced4da;
		border-radius: 0.375rem;
	}
	
	#popupFormModal .select2-selection:focus {
		border-color: #86b7fe;
		box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
	}
	
	/* Fix untuk dropdown yang terpotong */
	.modal-body {
		overflow: visible;
	}
	
	/* Override z-index untuk SweetAlert di dalam popup */
	.swal2-container {
		z-index: 99999 !important;
	}
	
	/* Multiple select styling */
	.select2-container--default .select2-selection--multiple {
		min-height: 38px;
		border-radius: 0.375rem;
	}
	
	.select2-container--default .select2-selection--multiple .select2-selection__choice {
		background-color: #0d6efd;
		border-color: #0d6efd;
		color: white;
		border-radius: 0.25rem;
		margin: 2px;
	}
	
	.select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
		color: white;
		margin-right: 5px;
	}
	
	.select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
		color: #ff6b6b;
	}
	
	/* Readonly multiple select styling */
	.select2-container--readonly .select2-selection--multiple .select2-selection__choice {
		background-color: #e9ecef !important;
		border-color: #ced4da !important;
		color: #6c757d !important;
	}
	
	.select2-container--readonly .select2-selection--multiple .select2-selection__choice__remove {
		display: none !important;
	}
	</style>
	<?php endif; ?>