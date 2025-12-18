
<?php
	
	$exclude = $exclude;
	$query_column = $this->ortyd->getviewlistform($module, $exclude, 2);
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
	
	if($is_publish == '' || $is_publish == null ){
		$is_publish = '0';
	}
?>

<form id="form<?php echo $iddata; ?>"  method="POST" action="<?php echo $action; ?>" enctype="multipart/form-data">
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
					
					<div class="row" id="dragList_nodrag">	
					<?php
						if($query_column){
							$indentitas = 0;
							foreach($query_column as $rows_column){ 
							
								$disable = '';
								
								$width_column = $this->ortyd->width_column($module,$rows_column['name']);
								$tipe_data = $this->ortyd->getTipeData($module,$rows_column['name']);
								$label_name = $this->ortyd->translate_column($module,$rows_column['name']);
								$label_name_text = $label_name;
								if($rows_column['name']){
									$table_change = "'".$module."'";
									$table_change_id = "'".$rows_column['name']."'";
									$label_name_text_data = "'".$label_name_text."'";
									$editheader = ' <span style="cursor:pointer" onClick="changeTitle('.$table_change.','.$table_change_id.','.$label_name_text_data.')"><i class="fa fa-edit"></i></span>';
									if($this->ortyd->getAksesEditNaming() == true){
										$label_name = $label_name.$editheader;
									}else{
										$label_name = $label_name;
									}
								}
								
								
								$labelrequired = 0;
								if($rows_column['is_nullable'] == 'NO'){
									$labelrequired = 1;
									$label_name = $label_name.' <span style="color: red;">*</span>';
								}else{
									$this->db->where('master_lop_field_required.field',$rows_column['name']);
									$this->db->where('master_lop_field_required_tipe.module', $module);
									$this->db->where('master_lop_field_required.active', 1);
									$this->db->join('master_lop_field_required_tipe','master_lop_field_required.tipe_id = master_lop_field_required_tipe.id');
									$querytab = $this->db->get('master_lop_field_required');
									$querytab = $querytab->result_object();
									if($querytab){
										$label_name = $label_name.' <span style="color: red;">*</span>';
									}
								}
								
								?>
								
								
								
								
								<?php
								
								if($tipe_data == 'TEXTAREA'){ ?>
								
									
									<?php if($rows_column['name'] == 'id'){ ?>
										
										
										
									<?php }else{ ?>
									
										<div id="<?php echo $rows_column['name'].'_header'; ?>" draggable="true" class="drag-item col-lg-<?php echo $width_column; ?> py-3">
											<div class="form-group">
												<div class="row">
													<div class="col-lg-3">
														<label><?php echo $label_name; ?></label>
													</div>
													<div class="col-lg-9">
														<textarea rows="3" id="<?php echo $rows_column['name']; ?>" name="<?php echo $rows_column['name']; ?>" class="form-control form-control-sm" <?php if($rows_column['is_nullable'] == 'NO'){echo ' required';} ?> <?php echo $disable; ?> placeholder="<?php echo 'Input '.$label_name_text; ?>"><?php echo ${$rows_column['name']}; ?></textarea>
													</div>
												</div>
											</div>
										</div>
									
									<?php } ?>
							
							
							<?php }elseif($rows_column['name'] == 'content'){ ?>
								
								<div id="<?php echo $rows_column['name'].'_header'; ?>" draggable="true" class="drag-item col-lg-<?php echo $width_column; ?> py-3">
									<div class="form-group">
										<div class="row">
											<div class="col-lg-3">
												<label><?php echo $label_name; ?></label>
											</div>
											<div class="col-lg-9">
												<!-- Tambah Kolom -->
												<div class="row mb-3 align-items-center" style="display:none !important">
													<div class="col-md-auto">
														<label class="mb-0">Tambah Kolom:</label>
													</div>
													<div class="col-md-4">
														<select class="form-control" id="column-size">
															<?php for ($i = 1; $i <= 12; $i++): ?>
																<option value="<?php echo $i; ?>" <?php echo $i == 12 ? 'selected' : ''; ?>>
																	col-md-<?php echo $i; ?> (<?php echo round(($i / 12) * 100); ?>%)
																</option>
															<?php endfor; ?>
														</select>
													</div>
													<div class="col-md-auto">
														<button type="button" class="btn btn-success" id="add-column">+ Tambah Kolom</button>
													</div>
												</div>

												<!-- Tombol Toggle dan Beautify -->
												<div class="mb-2 d-flex gap-2" style="display:none !important">
													<button type="button" class="btn btn-secondary btn-sm toggle-code">
														<i class="fas fa-code"></i> Tampilkan Kode
													</button>
													<button type="button" class="btn btn-info btn-sm beautify-html" style="display: none;">
														<i class="fas fa-magic"></i> Beautify HTML
													</button>
												</div>

												<!-- Editor -->
												<div class="code-editor" style="display: none;">
													<textarea id="html-editor" rows="10"><?php echo ${$rows_column['name']}; ?></textarea>

													<div class="mt-3">
														<label>Preview:</label>
														<div id="html-preview" class="border p-3" style="background: #f9f9f9; min-height: 100px;"></div>
													</div>
												</div>

												<!-- Kolom Dinamis -->
												<div class="row" id="editor-row"></div>

												<!-- Hidden Textarea untuk kirim data -->
												<textarea rows="30" id="<?php echo $rows_column['name']; ?>" name="<?php echo $rows_column['name']; ?>" class="form-control d-none"
													<?php if ($rows_column['is_nullable'] == 'NO') echo 'required'; ?>
													<?php echo $disable; ?>
													placeholder="<?php echo 'Input '.$label_name_text; ?>"><?php echo ${$rows_column['name']}; ?></textarea>
											</div>
										</div>
									</div>
								</div>

								<!-- Script -->
								<script>

										const toggleBtn = document.querySelector(".toggle-code");
										const beautifyBtn = document.querySelector(".beautify-html");
										const codeEditorContainer = document.querySelector(".code-editor");
										const preview = document.getElementById("html-preview");
										const textarea = document.getElementById("html-editor");
										const hiddenTextarea = document.getElementById("<?php echo $rows_column['name']; ?>");

										const editor = CodeMirror.fromTextArea(textarea, {
											mode: "htmlmixed",
											lineNumbers: true,
											lineWrapping: true,
											theme: "default"
										});

										function updatePreview() {
											const html = editor.getValue();
											preview.innerHTML = '<div class="row">'+html+'</div>';
											textarea.value = html;           // update textarea editor (jika kamu pakai textarea terpisah)
											hiddenTextarea.value = html;
										}


									document.addEventListener("DOMContentLoaded", function () {

										editor.on("change", updatePreview);

										toggleBtn.addEventListener("click", function () {
											if (codeEditorContainer.style.display === "none") {
												codeEditorContainer.style.display = "block";
												beautifyBtn.style.display = "inline-block";
												toggleBtn.innerHTML = '<i class="fas fa-code"></i> Sembunyikan Kode';
												editor.refresh();
												updatePreview();
												$('#editor-row').hide();
												
											} else {
												updatePreview();
												codeEditorContainer.style.display = "none";
												beautifyBtn.style.display = "none";
												toggleBtn.innerHTML = '<i class="fas fa-code"></i> Tampilkan Kode';
												$('#editor-row').show();
											}
											
											updatePreview();          // update div preview
											generateEditorContent();  // update #content hidden textarea
											renderEditorBlocks(hiddenTextarea.value)
										});

										beautifyBtn.addEventListener("click", function () {
											const beautified = html_beautify(editor.getValue(), {
												indent_size: 2,
												space_in_empty_paren: true
											});
											editor.setValue(beautified);
											
										});
									});
								</script>


							
							<?php }elseif($tipe_data == 'TEXTEDITOR'){ ?>
								
									
									<?php if($rows_column['name'] == 'id'){ ?>
										
										
										
									<?php }else{ ?>
									
										<div id="<?php echo $rows_column['name'].'_header'; ?>" draggable="true" class="drag-item col-lg-<?php echo $width_column; ?> py-3">
											<div class="form-group">
												<div class="row">
													<div class="col-lg-3">
														<label><?php echo $label_name; ?></label>
													</div>
													<div class="col-lg-9">
														<textarea rows="3" name="<?php echo $rows_column['name']; ?>" class="form-control form-control-sm summernote" <?php if($rows_column['is_nullable'] == 'NO'){echo ' required';} ?> <?php echo $disable; ?> placeholder="<?php echo 'Input '.$label_name_text; ?>"><?php echo ${$rows_column['name']}; ?></textarea>
													</div>
												</div>
											</div>
										</div>
									
									<?php } ?>
							
							
							<?php }elseif($tipe_data == 'DATE' || $rows_column['name'] == 'date' || $rows_column['name'] == 'tanggal'){ ?>
								
									<div id="<?php echo $rows_column['name'].'_header'; ?>" draggable="true" class="drag-item col-lg-<?php echo $width_column; ?> py-3">
										<div class="form-group">
											<div class="row">
												<div class="col-lg-3">
													<label><?php echo $label_name; ?></label>
												</div>
												<div class="col-lg-9">
													<input id="<?php echo $rows_column['name']; ?>" type="text" name="<?php echo $rows_column['name']; ?>" class="form-control form-control-sm datetime" value="<?php echo ${$rows_column['name']}; ?>"<?php if($rows_column['is_nullable'] == 'NO'){echo ' required ';} ?> <?php echo $disable; ?> readonly='true' placeholder="<?php echo 'Input '.$label_name_text; ?>"/> 
												</div>
											</div>
										</div>
									</div>
									
							<?php }elseif($tipe_data == 'DATETIME'){ ?>
								
									<div id="<?php echo $rows_column['name'].'_header'; ?>" draggable="true" class="drag-item col-lg-<?php echo $width_column; ?> py-3">
										<div class="form-group">
											<div class="row">
												<div class="col-lg-3">
													<label><?php echo $label_name; ?></label>
												</div>
												<div class="col-lg-9">
													<input id="<?php echo $rows_column['name']; ?>" type="text" name="<?php echo $rows_column['name']; ?>" class="form-control form-control-sm datepickertime" value="<?php echo ${$rows_column['name']}; ?>"<?php if($rows_column['is_nullable'] == 'NO'){echo ' required ';} ?> <?php echo $disable; ?> readonly='true' placeholder="<?php echo 'Input '.$label_name_text; ?>"/> 
												</div>
											</div>
										</div>
									</div>
								
							<?php }elseif($rows_column['name'] == 'email' || $rows_column['name'] == 'perusahaan_email'){ ?>
								
									<div id="<?php echo $rows_column['name'].'_header'; ?>" draggable="true" class="drag-item col-lg-<?php echo $width_column; ?> py-3">
										<div class="form-group">
											<div class="row">
												<div class="col-lg-3">
													<label><?php echo $label_name; ?></label>
												</div>
												<div class="col-lg-9">
													<input id="<?php echo $rows_column['name']; ?>" type="email" name="<?php echo $rows_column['name']; ?>" class="form-control form-control-sm" value="<?php echo ${$rows_column['name']}; ?>"<?php if($rows_column['is_nullable'] == 'NO'){echo ' required ';} ?> <?php echo $disable; ?> placeholder="<?php echo 'Input '.$label_name_text; ?>" /> 
												</div>
											</div>
										</div>
									</div>
								
							<?php }elseif($tipe_data == 'NUMBER' || $rows_column['name'] == 'nomor' || $rows_column['name'] == 'perusahaan_hp'){ ?>
								
									<div id="<?php echo $rows_column['name'].'_header'; ?>" draggable="true" class="drag-item col-lg-<?php echo $width_column; ?> py-3">
										<div class="form-group">
											<div class="row">
												<div class="col-lg-3">
													<label><?php echo $label_name; ?></label>
												</div>
												<div class="col-lg-9">
													<input id="<?php echo $rows_column['name']; ?>" type="number" name="<?php echo $rows_column['name']; ?>" class="form-control form-control-sm" value="<?php echo ${$rows_column['name']}; ?>"<?php if($rows_column['is_nullable'] == 'NO'){echo ' required ';} ?> <?php echo $disable; ?> placeholder="<?php echo 'Input '.$label_name_text; ?>" />
												</div>
											</div>
										</div>
									</div>
									
							<?php }elseif($tipe_data == 'CURRENCY' || $rows_column['name'] == 'nilai'){ ?>
								
									<div id="<?php echo $rows_column['name'].'_header'; ?>" draggable="true" class="drag-item col-lg-<?php echo $width_column; ?> py-3">
										<div class="form-group">
											<div class="row">
												<div class="col-lg-3">
													<label><?php echo $label_name; ?></label>
												</div>
												<div class="col-lg-9">
													<input id="<?php echo $rows_column['name']; ?>" type="text" name="<?php echo $rows_column['name']; ?>" class="form-control form-control-sm numeric-rp" value="<?php echo ${$rows_column['name']}; ?>"<?php if($rows_column['is_nullable'] == 'NO'){echo ' required ';} ?> <?php echo $disable; ?> placeholder="<?php echo 'Input '.$label_name_text; ?>" />
												</div>
											</div>
										</div>
									</div>
								
							<?php }else{ ?>
							
							
									<?php if($tipe_data == 'FILE' || $rows_column['name'] == 'file_id'){ ?>
								
										<?php 
											include(APPPATH."views/common/uploadformside.php");
										?>
								
									<?php }elseif($tipe_data == 'SELECT'){ ?>
										
									<?php 
										include(APPPATH."views/common/select2formside.php");
									?>
									
									<?php }elseif($rows_column['name'] == 'urutan'){ ?>
			
										<div id="<?php echo $rows_column['name'].'_header'; ?>" draggable="true" class="drag-item col-lg-<?php echo $width_column; ?> py-3">
											<div class="form-group">
												<div class="row">
													<div class="col-lg-3">
														<label><?php echo $label_name; ?></label>
													</div>
													<div class="col-lg-9">
														<input type="number" id="<?php echo $rows_column['name']; ?>" name="<?php echo $rows_column['name']; ?>" class="form-control form-control-sm" value="<?php echo ${$rows_column['name']}; ?>" <?php if($rows_column['is_nullable'] == 'NO'){echo ' required';} ?> <?php echo $disable; ?> placeholder="<?php echo 'Input '.$label_name_text; ?>" min="1" /> 
													</div>
												</div>
											</div>
										</div>
									
									
									<?php }else{ ?>
								
										<?php if($rows_column['name'] == 'content_old'){ ?>
										<div id="<?php echo $rows_column['name'].'_header'; ?>" draggable="true" class="drag-item col-lg-<?php echo $width_column; ?> py-3">
											<div class="form-group">
												<div class="row">
													<div class="col-lg-3">
														<label><?php echo $label_name; ?></label>
													</div>
													<div class="col-lg-9">
														<input type="text" name="<?php echo $rows_column['name']; ?>" id="<?php echo $rows_column['name']; ?>" class="form-control form-control-sm" value="" <?php if($rows_column['is_nullable'] == 'NO'){echo ' required';} ?> <?php echo $disable; ?> placeholder="<?php echo 'Input '.$label_name_text; ?>" /> 
													</div>
												</div>
											</div>
										</div>
										<?php }else{ ?>
										<div id="<?php echo $rows_column['name'].'_header'; ?>" draggable="true" class="drag-item col-lg-<?php echo $width_column; ?> py-3">
											<div class="form-group">
												<div class="row">
													<div class="col-lg-3">
														<label><?php echo $label_name; ?></label>
													</div>
													<div class="col-lg-9">
														<input type="text" name="<?php echo $rows_column['name']; ?>" id="<?php echo $rows_column['name']; ?>" class="form-control form-control-sm" value="<?php echo ${$rows_column['name']}; ?>" <?php if($rows_column['is_nullable'] == 'NO'){echo ' required';} ?> <?php echo $disable; ?> placeholder="<?php echo 'Input '.$label_name_text; ?>" /> 
													</div>
												</div>
											</div>
										</div>
										<?php } ?>
									
									<?php } ?>

							<?php }
								$indentitas++;
							}
						} ?>
						
						

						<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" class="csrf_token" placeholder="CSRF Invalid" required />
						
						<div class="card-footer py-3" id="btn-aksi-action">
							<div class="d-flex justify-content-end gap-3">
								<button type="button" onclick="handleSaveDraft('#form<?= $iddata; ?>', '<?= $action_draft; ?>', '<?= $iddata; ?>','','draft')" class="btn btn-light-primary">
									<i class="fa fa-save me-2"></i> Simpan
								</button>
								<button type="button" id="kt_docs_formvalidation_text_submit" class="btn btn-primary">
									<i class="fa fa-paper-plane me-2"></i> Simpan & Keluar
								</button>
								<?php if($iddata){ ?>
								<button type="button" onclick="handleSaveDraft('#form<?= $iddata; ?>', '<?= $action_draft; ?>', '<?= $iddata; ?>','','preview')" class="btn btn-light-primary">
									<i class="fa fa-save me-2"></i>  Simpan & Preview
								</button>
								<?php } ?>
							</div>
						</div>
							
						</div>	
					
				</div>
									</div>
								</div>
						</div>
		</div>
</div>
</form>

<script>
let colIndex = 0;
function addEditorColumn() {
    const colSize = $('#column-size').val() || 12;
    const colId = `col-${Date.now().toString(36)}-${Math.random().toString(36).substring(2, 8)}`;

    const html = `
        <div class="col-md-${colSize} resizable-col mb-3" data-cols="md-${colSize}">
            <div class="editor-block" data-id="${colId}">
                <div class="block-toolbar d-flex justify-content-end align-items-center mb-2">
                    <span class="drag-handle me-auto" title="Drag to move block" style="cursor: move;">
                        <i class="fas fa-grip-lines"></i>
                    </span>
                    <button type="button" class="btn btn-sm btn-secondary collapse-toggle me-1" data-bs-toggle="collapse" data-bs-target="#collapse-${colId}">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-info block-settings me-1">
                        <i class="fas fa-cog"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-danger remove-block">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div id="collapse-${colId}" class="collapse show">
                    <textarea class="summernote" name="block[]"></textarea>
                </div>
            </div>
        </div>
    `;

    const lastRow = $('#editor-row .col-baris').last();
    if (lastRow.length) {
        lastRow.append(html);
    } else {
        const newRow = $('<div class="col-baris row mb-3"></div>');
        newRow.append(html);
        $('#editor-row').append(newRow);
    }

    addsummernote('summernote');
    makeResizable();

    setTimeout(() => {
        $('.collapse-toggle').off('click').on('click', function () {
            const icon = $(this).find('i');
            icon.toggleClass('fa-chevron-down fa-chevron-up');
            $($(this).data('bs-target')).collapse('toggle');
        });

        $('.block-settings').off('click').on('click', function () {
            const block = $(this).closest('.editor-block');
            showBlockSettings(block);
        });

        generateEditorContent();
    }, 100);
}

// Delete block
$(document).on('click', '.remove-block', function () {
    $(this).closest('[class*="col-md-"]').remove();
	generateEditorContent()
});

// Drag and drop
new Sortable(document.getElementById('editor-row'), {
    animation: 150,
    handle: '.editor-block',
    draggable: '.col-md-3, .col-md-4, .col-md-6, .col-md-12'
});

function makeResizable() {
    $('.resizable-col').resizable({
        handles: 'e',
        minWidth: 100,
        resize: function (event, ui) {
            const parentWidth = $(this).parent().width();
            const colSize = Math.min(12, Math.max(1, Math.round((ui.size.width / parentWidth) * 12)));
            const newClass = `col-md-${colSize}`;

            // Hapus class col-md-* sebelumnya, lalu tambahkan yang baru
            $(this)
                .removeClass(function (index, className) {
                    return (className.match(/col-md-\d+/g) || []).join(' ');
                })
                .addClass(newClass)
                .attr('data-cols', newClass);
        }
    });
}

function renderEditorBlocks(content) {
    if (!content){
		addEditorColumn();
		return;
	}

    $('#editor-row').html(''); // Clear editor

    const tempWrapper = $('<div>').html(content);

	if (tempWrapper.find('.col-baris').length > 0) {
		// Process each row
		tempWrapper.find('.col-baris').each(function () {
			const row = $(this);
			// Get original row class and style
			const originalRowClass = row.attr('class') || '';
			const originalRowStyle = row.attr('style') || '';

			// Buat ulang row dengan semua class dan style aslinya
			const rowHtml = $('<div>')
				.addClass(originalRowClass)
				.attr('style', originalRowStyle);

			// Toolbar row dengan icon drag handle
			const rowToolbar = `
				<div class="row-toolbar d-flex justify-content-between align-items-center mb-2">
					<button type="button" class="btn btn-sm btn-primary row-settings me-1">
						<i class="fas fa-cog"></i> Row Settings
					</button>
				</div>`;
			rowHtml.append(rowToolbar);

			// Process each block in the row
			row.find('.block-box').each(function (index) {
				const col = $(this);
				const originalClass = col.attr('class') || '';
				const boxCol = col.find('.box-col');

				const colClassMatch = originalClass.match(/\bcol-(xs|sm|md|lg|xl|xxl)-\d{1,2}\b/);
				const colClass = colClassMatch ? colClassMatch[0] : 'col-md-12';

				const colId = col.attr('data-id') || 'block-' +  Date.now().toString(36)+'-'+Math.random().toString(36).substring(2, 8) + '-' + index;
				const innerHtml = boxCol.html()?.trim() || '';

				const boxColStyles = boxCol.attr('style') || '';
				const boxColClasses = boxCol.attr('class')?.replace('box-col', '').trim() || '';

				const blockHtml = `
					<div class="${colClass} resizable-col" data-cols="${colClass.replace('col-', '')}">
						<div class="editor-block ${boxColClasses}" data-id="${colId}" style="${boxColStyles}">
							<div class="block-toolbar d-flex justify-content-end align-items-center mb-1" >
								<span class="drag-handle me-auto" title="Drag to move block" style="cursor: move;">
									<i class="fas fa-grip-lines"></i>
								</span>
								<button type="button" class="btn btn-sm btn-secondary collapse-toggle me-1" data-bs-toggle="collapse" data-bs-target="#collapse-${colId}">
									<i class="fas fa-chevron-down"></i>
								</button>
								<button type="button" class="btn btn-sm btn-info block-settings me-1">
									<i class="fas fa-cog"></i>
								</button>
								<button type="button" class="btn btn-sm btn-danger remove-block">
									<i class="fas fa-times"></i>
								</button>
							</div>
							<div id="collapse-${colId}" class=""> <!-- default collapsed hidden -->
								<textarea class="summernote" name="block[]">${innerHtml}</textarea>
							</div>
						</div>
					</div>`;

				rowHtml.append(blockHtml);
			});

			$('#editor-row').append(rowHtml);
		});

		// Handle case where content doesn't have col-baris wrapper
		if (tempWrapper.find('.col-baris').length === 0) {
			tempWrapper.find('.block-box').each(function (index) {
				const col = $(this);
				const originalClass = col.attr('class') || '';
				const boxCol = col.find('.box-col');

				const colClassMatch = originalClass.match(/\bcol-(xs|sm|md|lg|xl|xxl)-\d{1,2}\b/);
				const colClass = colClassMatch ? colClassMatch[0] : 'col-md-12';

				const colId = col.attr('data-id') || 'block-' + Date.now().toString(36)+'-'+Math.random().toString(36).substring(2, 8) + '-' + index;
				const innerHtml = boxCol.html()?.trim() || '';

				const boxColStyles = boxCol.attr('style') || '';
				const boxColClasses = boxCol.attr('class')?.replace('box-col', '').trim() || '';

				const blockHtml = `
					<div class="${colClass} resizable-col" data-cols="${colClass.replace('col-', '')}">
						<div class="editor-block ${boxColClasses}" data-id="${colId}" style="${boxColStyles}">
							<div class="block-toolbar d-flex justify-content-end align-items-center mb-1" >
								<span class="drag-handle me-auto" title="Drag to move block" style="cursor: move;">
									<i class="fas fa-grip-lines"></i>
								</span>
								<button type="button" class="btn btn-sm btn-secondary collapse-toggle me-1" data-bs-toggle="collapse" data-bs-target="#collapse-${colId}">
									<i class="fas fa-chevron-down"></i>
								</button>
								<button type="button" class="btn btn-sm btn-info block-settings me-1">
									<i class="fas fa-cog"></i>
								</button>
								<button type="button" class="btn btn-sm btn-danger remove-block">
									<i class="fas fa-times"></i>
								</button>
							</div>
							<div id="collapse-${colId}" class=""> <!-- default collapsed hidden -->
								<textarea class="summernote" name="block[]">${innerHtml}</textarea>
							</div>
						</div>
					</div>`;

				$('#editor-row').append(blockHtml);
			});
		}

		setTimeout(function () {
			addsummernote('summernote');
			makeResizable();

			// Inisialisasi sortable untuk drag & drop
		   // setTimeout(function () {
				//$('#editor-row .resizable-col').sortable({
					//items: '> .editor-block',
					//handle: '.drag-handle',
					//connectWith: '#editor-row .resizable-col',
					//placeholder: 'sortable-placeholder',
					//cancel: '.note-editor, .note-editor *', // Ini wajib
					//forcePlaceholderSize: true,
					//tolerance: 'pointer',
					//start: function (event, ui) {
						//ui.placeholder.height(ui.item.height());
					//},
					//update: function (event, ui) {
						//generateEditorContent();
					//}
				//}).disableSelection();
			//}, 300); // tunggu render summernote
			

			// Toggle collapse icon dan collapse show/hide
			$('.collapse-toggle').on('click', function () {
				const icon = $(this).find('i');
				icon.toggleClass('fa-chevron-down fa-chevron-up');

				const target = $($(this).data('bs-target'));
				target.collapse('toggle');
			});

			// Initialize settings popup for block
			$('.block-settings').on('click', function () {
				const block = $(this).closest('.editor-block');
				if (!block.css('background-color') || block.css('background-color') === 'rgba(0, 0, 0, 0)') {
					block.css('background-color', '#ffffff');
				}
				showBlockSettings(block);
			});

			// Initialize settings popup for row
			$('.row-settings').on('click', function () {
				const row = $(this).closest('.col-baris');
				showRowSettings(row);
			});

		}, 100);
	} else {
    // jika .col-baris tidak ada
		addEditorColumn();
	}

}


    function extractStyleValue(styleStr, prop) {
		const match = styleStr.match(new RegExp(prop + '\\s*:\\s*([^;]+)', 'i'));
		return match ? match[1].trim() : '';
	}

function parseStyleString(styleString) {
    const styleObj = {};
    styleString.split(';').forEach(rule => {
        const [prop, value] = rule.split(':');
        if (prop && value) {
            styleObj[prop.trim()] = value.trim();
        }
    });
    return styleObj;
}

function buildStyleString(styleObj) {
    return Object.entries(styleObj).map(([key, val]) => `${key}: ${val}`).join('; ');
}

function showRowSettings(row) {
    const currentStyles = row.attr('style') || '';
    const currentClasses = row.attr('class') || '';
    const computedStyle = row[0] ? window.getComputedStyle(row[0]) : null;

    let currentBgColor = computedStyle?.backgroundColor || '';

	if (currentBgColor === 'rgba(0, 0, 0, 0)' || currentBgColor === 'transparent') {
		currentBgColor = ''; // tidak ada background color
	}
	
	 // Background Color input
    // Convert currentBgColor rgb to hex if possible, else fallback #ffffff
    function rgbToHex(rgb) {
        if (!rgb || rgb === '') return '#ffffff';
        const result = rgb.match(/^rgba?\((\d+),\s*(\d+),\s*(\d+)/i);
        if (!result) return rgb;
        const r = parseInt(result[1]).toString(16).padStart(2, '0');
        const g = parseInt(result[2]).toString(16).padStart(2, '0');
        const b = parseInt(result[3]).toString(16).padStart(2, '0');
        return '#' + r + g + b;
    }

    currentBgColor = rgbToHex(currentBgColor);
	
    const styleStr = currentStyles;

    const currentPadding = {
        top: computedStyle?.getPropertyValue('padding-top') || '',
        right: computedStyle?.getPropertyValue('padding-right') || '',
        bottom: computedStyle?.getPropertyValue('padding-bottom') || '',
        left: computedStyle?.getPropertyValue('padding-left') || ''
    };

    const currentMargin = {
        top: computedStyle?.getPropertyValue('margin-top') || '',
        right: computedStyle?.getPropertyValue('margin-right') || '',
        bottom: computedStyle?.getPropertyValue('margin-bottom') || '',
        left: computedStyle?.getPropertyValue('margin-left') || ''
    };

    const modalHtml = `
    <div class="modal fade" id="rowSettingsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title">Row Settings</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Appearance</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">Additional Classes</label>
                                        <input type="text" class="form-control" id="rowClasses" 
                                            value="${currentClasses.replace('col-baris', '').trim()}" 
                                            placeholder="e.g., custom-row">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Background Color</label>
                                        <div class="input-group">
                                            <input type="color" class="form-control form-control-color" 
                                                id="rowBgColor" value="${currentBgColor || '#ffffff'}">
                                            <button class="btn btn-outline-secondary" type="button" id="clearRowBgColor">
                                                Clear
                                            </button>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Inline Styles</label>
                                        <textarea class="form-control" id="rowStyles" rows="3" 
                                            placeholder="Custom CSS styles">${styleStr}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-light"><h6 class="mb-0">Padding</h6></div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-6 col-sm-3"><label class="form-label small">Top</label><input type="text" class="form-control" id="rowPaddingTop" value="${currentPadding.top}" placeholder="0px"></div>
                                        <div class="col-6 col-sm-3"><label class="form-label small">Right</label><input type="text" class="form-control" id="rowPaddingRight" value="${currentPadding.right}" placeholder="0px"></div>
                                        <div class="col-6 col-sm-3"><label class="form-label small">Bottom</label><input type="text" class="form-control" id="rowPaddingBottom" value="${currentPadding.bottom}" placeholder="0px"></div>
                                        <div class="col-6 col-sm-3"><label class="form-label small">Left</label><input type="text" class="form-control" id="rowPaddingLeft" value="${currentPadding.left}" placeholder="0px"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="card mb-4">
                                <div class="card-header bg-light"><h6 class="mb-0">Margin</h6></div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-6 col-sm-3"><label class="form-label small">Top</label><input type="text" class="form-control" id="rowMarginTop" value="${currentMargin.top}" placeholder="0px"></div>
                                        <div class="col-6 col-sm-3"><label class="form-label small">Right</label><input type="text" class="form-control" id="rowMarginRight" value="${currentMargin.right}" placeholder="0px"></div>
                                        <div class="col-6 col-sm-3"><label class="form-label small">Bottom</label><input type="text" class="form-control" id="rowMarginBottom" value="${currentMargin.bottom}" placeholder="0px"></div>
                                        <div class="col-6 col-sm-3"><label class="form-label small">Left</label><input type="text" class="form-control" id="rowMarginLeft" value="${currentMargin.left}" placeholder="0px"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveRowSettings">Save Changes</button>
                </div>
            </div>
        </div>
    </div>`;

    $('body').append(modalHtml);

    const modal = new bootstrap.Modal(document.getElementById('rowSettingsModal'));
    modal.show();

    $('#clearRowBgColor').on('click', function () {
        $('#rowBgColor').val('#ffffff').trigger('change');
    });

    $('#saveRowSettings').on('click', function () {
        // Ambil style dari textarea
        const styleObj = parseStyleString($('#rowStyles').val().trim());

        // Update background color
        const bgColor = $('#rowBgColor').val();
        if (bgColor && bgColor !== '#ffffff') {
            styleObj['background-color'] = bgColor;
        } else {
            delete styleObj['background-color'];
        }

        // Update padding
        styleObj['padding-top'] = $('#rowPaddingTop').val() || '0';
        styleObj['padding-right'] = $('#rowPaddingRight').val() || '0';
        styleObj['padding-bottom'] = $('#rowPaddingBottom').val() || '0';
        styleObj['padding-left'] = $('#rowPaddingLeft').val() || '0';

        // Update margin
        styleObj['margin-top'] = $('#rowMarginTop').val() || '0';
        styleObj['margin-right'] = $('#rowMarginRight').val() || '0';
        styleObj['margin-bottom'] = $('#rowMarginBottom').val() || '0';
        styleObj['margin-left'] = $('#rowMarginLeft').val() || '0';

        // Simpan class
        const newClasses = 'col-baris ' + $('#rowClasses').val().trim();
        row.attr('class', newClasses);

        // Simpan kembali ke atribut style
        const newStyle = buildStyleString(styleObj);
        row.attr('style', newStyle);

        generateEditorContent();
        modal.hide();
    });

    $('#rowSettingsModal').on('hidden.bs.modal', function () {
        generateEditorContent();
        $(this).remove();
    });
}

function generateEditorContent() {
    let finalHtml = '';

    $('#editor-row .col-baris').each(function () {
        const rowWrapper = $(this);

        // Ambil semua kelas dan style asli dari .col-baris
        const originalClass = rowWrapper.attr('class') || '';
        const originalStyle = rowWrapper.attr('style') || '';

        let rowHtml = '';
        let rowTotal = 0;

        rowWrapper.find('.summernote').each(function () {
            const blockWrapper = $(this).closest('.resizable-col');
            const blockElement = $(this).closest('.editor-block');

            // Dapatkan kelas col, contoh col-md-6
            const rawColSize = blockWrapper.attr('data-cols');
            let colClass = 'col-md-12';

            if (typeof rawColSize === 'string') {
                if (/^(xs|sm|md|lg|xl|xxl)-\d{1,2}$/.test(rawColSize)) {
                    colClass = `col-${rawColSize}`;
                } else if (/^col-(xs|sm|md|lg|xl|xxl)-\d{1,2}$/.test(rawColSize)) {
                    colClass = rawColSize;
                }
            }

            const colSizeMatch = colClass.match(/-(\d{1,2})$/);
            const colSize = colSizeMatch ? parseInt(colSizeMatch[1], 10) : 12;

            const htmlContent = $(this).summernote('code');
            const blockId = blockElement.attr('data-id') || '';
            const blockClasses = blockElement.attr('class')?.replace('editor-block', '').trim() || '';
            const blockStyles = blockElement.attr('style') || '';

            const blockBoxHtml = `
                <div class="block-box ${colClass}" data-id="${blockId}">
                    <div class="box-col ${blockClasses}" style="${blockStyles}">
                        ${htmlContent}
                    </div>
                </div>`;

            if (rowTotal + colSize > 12) {
                if (rowHtml) {
                    // Gunakan kelas dan style asli dari .col-baris
                    finalHtml += `<div class="${originalClass}" style="${originalStyle}">${rowHtml}</div>`;
                }
                rowHtml = blockBoxHtml;
                rowTotal = colSize;
            } else {
                rowHtml += blockBoxHtml;
                rowTotal += colSize;
            }
        });

        if (rowHtml) {
            finalHtml += `<div class="${originalClass}" style="${originalStyle}">${rowHtml}</div>`;
        }
    });

    // Kalau tidak ada .col-baris, fallback ke .summernote langsung
    if (finalHtml === '' && $('#editor-row .summernote').length > 0) {
        let currentRowHtml = '';
        let currentRowTotal = 0;

        $('#editor-row .summernote').each(function () {
            const blockWrapper = $(this).closest('.resizable-col');
            const blockElement = $(this).closest('.editor-block');

            const rawColSize = blockWrapper.attr('data-cols');
            let colClass = 'col-md-12';

            if (typeof rawColSize === 'string') {
                if (/^(xs|sm|md|lg|xl|xxl)-\d{1,2}$/.test(rawColSize)) {
                    colClass = `col-${rawColSize}`;
                } else if (/^col-(xs|sm|md|lg|xl|xxl)-\d{1,2}$/.test(rawColSize)) {
                    colClass = rawColSize;
                }
            }

            const colSizeMatch = colClass.match(/-(\d{1,2})$/);
            const colSize = colSizeMatch ? parseInt(colSizeMatch[1], 10) : 12;

            const htmlContent = $(this).summernote('code');
            const blockId = blockElement.attr('data-id') || '';
            const blockClasses = blockElement.attr('class')?.replace('editor-block', '').trim() || '';
            const blockStyles = blockElement.attr('style') || '';

            const blockBoxHtml = `
                <div class="block-box ${colClass}" data-id="${blockId}">
                    <div class="box-col ${blockClasses}" style="${blockStyles}">
                        ${htmlContent}
                    </div>
                </div>`;

            if (currentRowTotal + colSize > 12) {
                finalHtml += `<div class="col-baris row">${currentRowHtml}</div>`;
                currentRowHtml = blockBoxHtml;
                currentRowTotal = colSize;
            } else {
                currentRowHtml += blockBoxHtml;
                currentRowTotal += colSize;
            }
        });

        if (currentRowHtml) {
            finalHtml += `<div class="col-baris row">${currentRowHtml}</div>`;
        }
    }

    $('#content').val(finalHtml);
    $('#html-editor').val(finalHtml);

    if (editor && typeof editor.setValue === 'function') {
        editor.setValue(finalHtml);
    }

    return finalHtml;
}

function showBlockSettings(block) {
    // Fungsi bantu untuk parse style inline menjadi object
    function parseStyleString(styleString) {
        const styleObj = {};
        if (!styleString) return styleObj;
        const styles = styleString.split(';');
        for (let i = 0; i < styles.length; i++) {
            const s = styles[i].trim();
            if (!s) continue;
            const [prop, val] = s.split(':');
            if (prop && val) {
                styleObj[prop.trim()] = val.trim();
            }
        }
        return styleObj;
    }

    // Fungsi bantu untuk build style string dari object
    function buildStyleString(styleObj) {
        let styleStr = '';
        for (const prop in styleObj) {
            if (styleObj.hasOwnProperty(prop)) {
                styleStr += prop + ': ' + styleObj[prop] + '; ';
            }
        }
        return styleStr.trim();
    }

    // Ambil current style dan kelas
    const currentStyleString = block.attr('style') || '';
    const currentClasses = block.attr('class') || '';

    // Parse style ke object
    let styleObj = parseStyleString(currentStyleString);

    // Ambil computed background color (transparan jadi empty)
    const computedStyle = block[0] ? window.getComputedStyle(block[0]) : null;
    let currentBgColor = '';
    if (computedStyle) {
        currentBgColor = computedStyle.backgroundColor === 'rgba(0, 0, 0, 0)' ? '' : computedStyle.backgroundColor;
    }

    // Buat nilai padding dan margin default dari styleObj, fallback '0px'
    const paddingProps = ['padding-top', 'padding-right', 'padding-bottom', 'padding-left'];
    const marginProps = ['margin-top', 'margin-right', 'margin-bottom', 'margin-left'];

    const currentPadding = {};
    const currentMargin = {};

    paddingProps.forEach(function(prop) {
        currentPadding[prop] = styleObj[prop] || '0px';
    });
    marginProps.forEach(function(prop) {
        currentMargin[prop] = styleObj[prop] || '0px';
    });

    // Ambil kelas tambahan tanpa 'editor-block'
    var extraClasses = currentClasses.replace(/(^|\s)editor-block(\s|$)/g, ' ').trim();
	//var extraClasses = $('#blockClasses').val().trim();
	block.removeClass().addClass('editor-block');
	if (extraClasses.length > 0) {
		var extraClassesArray = extraClasses.split(/\s+/);
		extraClassesArray.forEach(function(c) {
			if (c) block.addClass(c);
		});
	}

    // Modal HTML (string biasa, concat)
    var modalHtml = '';
    modalHtml += '<div class="modal fade" id="blockSettingsModal" tabindex="-1" aria-hidden="true">';
    modalHtml +=   '<div class="modal-dialog modal-lg">';
    modalHtml +=     '<div class="modal-content">';
    modalHtml +=       '<div class="modal-header bg-light">';
    modalHtml +=         '<h5 class="modal-title">Block Settings</h5>';
    modalHtml +=         '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>';
    modalHtml +=       '</div>';
    modalHtml +=       '<div class="modal-body">';
    modalHtml +=         '<div class="row">';
    
    // Left Column
    modalHtml +=           '<div class="col-md-6">';
    modalHtml +=             '<div class="card mb-4">';
    modalHtml +=               '<div class="card-header bg-light"><h6 class="mb-0">Appearance</h6></div>';
    modalHtml +=               '<div class="card-body">';
    modalHtml +=                 '<div class="mb-3">';
    modalHtml +=                   '<label class="form-label">Additional Classes</label>';
    modalHtml +=                   '<input type="text" class="form-control" id="blockClasses" value="' + extraClasses + '" placeholder="e.g., my-class another-class">';
    modalHtml +=                 '</div>';

    // Background Color input
    // Convert currentBgColor rgb to hex if possible, else fallback #ffffff
    function rgbToHex(rgb) {
        if (!rgb || rgb === '') return '#ffffff';
        const result = rgb.match(/^rgba?\((\d+),\s*(\d+),\s*(\d+)/i);
        if (!result) return rgb;
        const r = parseInt(result[1]).toString(16).padStart(2, '0');
        const g = parseInt(result[2]).toString(16).padStart(2, '0');
        const b = parseInt(result[3]).toString(16).padStart(2, '0');
        return '#' + r + g + b;
    }

    var bgColorHex = rgbToHex(currentBgColor);

    modalHtml +=                 '<div class="mb-3">';
    modalHtml +=                   '<label class="form-label">Background Color</label>';
    modalHtml +=                   '<div class="input-group">';
    modalHtml +=                     '<input type="color" class="form-control form-control-color" id="blockBgColor" value="' + bgColorHex + '">';
    modalHtml +=                     '<button class="btn btn-outline-secondary" type="button" id="clearBgColor">Clear</button>';
    modalHtml +=                   '</div>';
    modalHtml +=                 '</div>';

    // Inline styles textarea, show style string without padding, margin, background-color (karena diatur terpisah)
    var filteredStyleObj = {...styleObj};
    paddingProps.forEach(p => delete filteredStyleObj[p]);
    marginProps.forEach(m => delete filteredStyleObj[m]);
    delete filteredStyleObj['background-color'];
    var filteredStyleString = buildStyleString(filteredStyleObj);

    modalHtml +=                 '<div class="mb-3">';
    modalHtml +=                   '<label class="form-label">Inline Styles</label>';
    modalHtml +=                   '<textarea class="form-control" id="blockStyles" rows="3" placeholder="Custom CSS styles">' + filteredStyleString + '</textarea>';
    modalHtml +=                 '</div>';

    modalHtml +=               '</div>';
    modalHtml +=             '</div>';
    modalHtml +=           '</div>';

    // Right Column
    modalHtml +=           '<div class="col-md-6">';
    
    // Padding Card
    modalHtml +=             '<div class="card mb-4">';
    modalHtml +=               '<div class="card-header bg-light"><h6 class="mb-0">Padding</h6></div>';
    modalHtml +=               '<div class="card-body">';
    modalHtml +=                 '<div class="row g-3">';
    paddingProps.forEach(function(p, i) {
        var label = p.split('-')[1]; // top, right, bottom, left
        modalHtml += '<div class="col-6 col-sm-3">';
        modalHtml += '<label class="form-label small text-muted">' + label.charAt(0).toUpperCase() + label.slice(1) + '</label>';
        modalHtml += '<input type="text" class="form-control" id="padding' + label.charAt(0).toUpperCase() + label.slice(1) + '" value="' + currentPadding[p] + '" placeholder="0px">';
        modalHtml += '</div>';
    });
    modalHtml +=                 '</div>';
    modalHtml +=               '</div>';
    modalHtml +=             '</div>';

    // Margin Card
    modalHtml +=             '<div class="card mb-4">';
    modalHtml +=               '<div class="card-header bg-light"><h6 class="mb-0">Margin</h6></div>';
    modalHtml +=               '<div class="card-body">';
    modalHtml +=                 '<div class="row g-3">';
    marginProps.forEach(function(m, i) {
        var label = m.split('-')[1]; // top, right, bottom, left
        modalHtml += '<div class="col-6 col-sm-3">';
        modalHtml += '<label class="form-label small text-muted">' + label.charAt(0).toUpperCase() + label.slice(1) + '</label>';
        modalHtml += '<input type="text" class="form-control" id="margin' + label.charAt(0).toUpperCase() + label.slice(1) + '" value="' + currentMargin[m] + '" placeholder="0px">';
        modalHtml += '</div>';
    });
    modalHtml +=                 '</div>';
    modalHtml +=               '</div>';
    modalHtml +=             '</div>';

    modalHtml +=           '</div>'; // end right col

    modalHtml +=         '</div>'; // end row
    modalHtml +=       '</div>'; // end modal-body
    modalHtml +=       '<div class="modal-footer bg-light">';
    modalHtml +=         '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>';
    modalHtml +=         '<button type="button" class="btn btn-primary" id="saveBlockSettings">Save Changes</button>';
    modalHtml +=       '</div>';
    modalHtml +=     '</div>';
    modalHtml +=   '</div>';
    modalHtml += '</div>';

    // Append modal to body
    $('body').append(modalHtml);

    // Show modal
    var modalEl = document.getElementById('blockSettingsModal');
    var modal = new bootstrap.Modal(modalEl);
    modal.show();

    // Clear background color button
    $('#clearBgColor').on('click', function () {
        $('#blockBgColor').val('#ffffff').trigger('change');
    });

    // Save button handler
    $('#saveBlockSettings').on('click', function () {
        // Update classes, selalu sertakan 'editor-block'
        var newClasses = 'editor-block ' + $('#blockClasses').val().trim();
        block.attr('class', newClasses.trim());

        // Ambil inline styles dari textarea, parsing jadi object dulu
        var inlineStyleString = $('#blockStyles').val();
        var inlineStyleObj = parseStyleString(inlineStyleString);

        // Update padding dan margin di inlineStyleObj
        paddingProps.forEach(function (p) {
            var inputId = 'padding' + p.split('-')[1].charAt(0).toUpperCase() + p.split('-')[1].slice(1);
            var val = $('#' + inputId).val();
            if (val && val.trim() !== '') {
                inlineStyleObj[p] = val.trim();
            } else {
                delete inlineStyleObj[p];
            }
        });

        marginProps.forEach(function (m) {
            var inputId = 'margin' + m.split('-')[1].charAt(0).toUpperCase() + m.split('-')[1].slice(1);
            var val = $('#' + inputId).val();
            if (val && val.trim() !== '') {
                inlineStyleObj[m] = val.trim();
            } else {
                delete inlineStyleObj[m];
            }
        });

        // Update background color di inlineStyleObj
        var bgColor = $('#blockBgColor').val();
        if (bgColor && bgColor !== '#ffffff') {
            inlineStyleObj['background-color'] = bgColor;
        } else {
            delete inlineStyleObj['background-color'];
        }

        // Build style string kembali dan set ke block
        var newStyleString = buildStyleString(inlineStyleObj);
        if (newStyleString) {
            block.attr('style', newStyleString);
        } else {
            block.removeAttr('style');
        }

        // Panggil fungsi generateEditorContent jika ada (sesuaikan)
        if (typeof generateEditorContent === 'function') {
            generateEditorContent();
        }

        modal.hide();
    });

    // Bersihkan modal dari DOM saat sudah ditutup
    $(modalEl).on('hidden.bs.modal', function () {
        if (typeof generateEditorContent === 'function') {
            generateEditorContent();
        }
        $(modalEl).remove();
    });
}

</script>

<script>




$(document).ready(function () {
	
	
	
	$('#add-column').on('click', function () {
		addEditorColumn();
	});


	 // Jika ada konten yang diambil dari controller, render ke dalam blok
   const content = `<?= isset($content) ? addslashes($content ?? '') : ''; ?>`;
   renderEditorBlocks(content)

	setTimeout(function () {
				$('.block-toolbar .block-settings').each(function () {
					this.style.setProperty('display', 'none', 'important');
				});
				
				$('.block-toolbar .remove-block').each(function () {
					this.style.setProperty('display', 'none', 'important');
				});
				
				$('.row-toolbar').each(function () {
					this.style.setProperty('display', 'none', 'important');
				});
			},100)
	


})
  // Fungsi untuk merender blok dari blocksData dalam div.row
</script>

<script>
	$( document ).ready(function() {
	

		// Submit button handler
		var forminput = document.getElementById('form<?php echo $iddata; ?>');
		const submitButton = document.getElementById('kt_docs_formvalidation_text_submit');
		submitButton.addEventListener('click', function (e) {
			
			<?php 
					$this->db->where('master_lop_field_required_tipe.module', $module);
					$this->db->where('master_lop_field_required.active', 1);
					$this->db->join('master_lop_field_required_tipe','master_lop_field_required.tipe_id = master_lop_field_required_tipe.id');
					$querytab = $this->db->get('master_lop_field_required');
					$querytab = $querytab->result_object();
					if($querytab){
						foreach($querytab as $rowstab){
				?>
											
						$('#<?php echo $rowstab->field; ?>').prop('required',true);
											
				<?php
						}
					}
				
			?>
			
			Swal.fire({
			  icon: "info",
			  title: "Submit Data",
			  html: 'Apakah anda yakin akan menyimpan data ? <p></p><span style="color:red">Pastikan semua data yang ada sesuai.</span>',
			  showDenyButton: false,
			  showCancelButton: true,
			  confirmButtonText: "Iya, Saya Setuju",
			  cancelButtonText: "Tidak, Sesuaikan Inputan",
			  cancelButtonColor: "#ff0000",
			}).then((result) => {
			  /* Read more about isConfirmed, isDenied below */
			  if (result.isConfirmed) {
				
				loadingopen()
			
				// Prevent default button action
				e.preventDefault();
				var requiredattr = 0;
				var requiredattrdata = [];
				var datanya;
				for(var i=0; i < forminput.elements.length; i++){
					if(forminput.elements[i].value === '' && forminput.elements[i].hasAttribute('required')){
						console.log(forminput.elements[i].attributes)
						datanya = forminput.elements[i].attributes['placeholder'].nodeValue;
						datanya = datanya.replaceAll(/<\/[^>]+(>|$)/g, "");
						requiredattrdata.push(stripHtml(datanya) + '<br>')
						requiredattr = 1;
					}
				}

				if(requiredattr == 0){
					$.post('<?php echo $action; ?>', $('#form<?php echo $iddata; ?>').serialize(),function (data) {
					console.log(data)
					updateCsrfToken(data.csrf_hash)
						if(data.status == "success"){
							
							Swal.fire({
								text: "Data berhasil disimpan!",
								icon: "success",
								buttonsStyling: false,
								confirmButtonText: "Ok, got it!",
								customClass: {
									confirmButton: "btn btn-primary"
								}
							});
							
							loadingclose()
							
							setTimeout(() => {
								window.location.href = '<?php echo base_url($headurl); ?>'; //Will take you to Google.
							}, 2000);
							
						}else{
							
							Swal.fire({
								text: "Data tidak berhasil disimpan!",
								icon: "error",
								buttonsStyling: false,
								confirmButtonText: "Tutup",
								customClass: {
									confirmButton: "btn btn-primary"
								}
							});

							loadingclose()
							
						}
					}, 'json')
					.fail(function(jqxhr, status, error) {
									console.error("Request failed: " + error);
									
									// Menangani jika statusnya 403 dan mengambil token CSRF baru
									if (jqxhr.status === 403) {
										$.get('<?php echo base_url('request_csrf_token'); ?>', function(data) {
											csrfHash = data.csrf_hash;
											updateCsrfToken(csrfHash); // Perbarui token CSRF
											// Lakukan retry atau aksi lainnya
										});
									}

									Swal.fire({
										text: "Terjadi kesalahan saat mengirim data!",
										icon: "error",
										buttonsStyling: false,
										confirmButtonText: "Coba Lagi",
										customClass: {
											confirmButton: "btn btn-danger"
										},
										didOpen: () => {
											$('.swal2-container').css('z-index', 99999); // Ensures the alert is in front
										}
									});

									loadingclose();
								});
				}else{
					
					console.log(requiredattrdata.toString())
					datanya = requiredattrdata.toString().replaceAll(",","");
					Swal.fire({
						html: "Masih ada data belum terisi:<br>" +datanya,
						icon: "error",
						buttonsStyling: false,
						confirmButtonText: "Lanjutkan Pengisian",
						customClass: {
							confirmButton: "btn btn-primary"
						}
					});
					
					loadingclose()
				}
			
			  } else if (result.isDenied) {
				Swal.fire("Changes are not saved", "", "info");
			  }
			});
			
			
			
			
		})

	

	})
	
	
function handleSaveDraft(formSelector, actionUrl, iddata, slugRefresh = '', type = 'draft') {
	const formElement = document.querySelector(formSelector);

	// Aktifkan field required dari PHP (generate inline script-nya secara dinamis)
	<?php 
		$this->db->where('master_lop_field_required_tipe.module', $module);
		$this->db->where('master_lop_field_required.active', 1);
		$this->db->join('master_lop_field_required_tipe','master_lop_field_required.tipe_id = master_lop_field_required_tipe.id');
		$querytab = $this->db->get('master_lop_field_required');
		$querytab = $querytab->result_object();
		if($querytab){
			foreach($querytab as $rowstab){
	?>
		$('#<?php echo $rowstab->field; ?>').prop('required', true);
	<?php
			}
		}
	?>

	Swal.fire({
		icon: "info",
		title: "Submit Data",
		html: 'Apakah anda yakin akan menyimpan data ? <p></p><span style="color:red">Pastikan semua data yang ada sesuai.</span>',
		showDenyButton: false,
		showCancelButton: true,
		confirmButtonText: "Iya, Saya Setuju",
		cancelButtonText: "Tidak, Sesuaikan Inputan",
		cancelButtonColor: "#ff0000",
	}).then((result) => {
		if (result.isConfirmed) {

			loadingopen();

			let requiredattr = 0;
			let requiredattrdata = [];

			for (let i = 0; i < formElement.elements.length; i++) {
				if (formElement.elements[i].value === '' && formElement.elements[i].hasAttribute('required')) {
					let datanya = formElement.elements[i].getAttribute('placeholder') || formElement.elements[i].name;
					datanya = datanya.replaceAll(/<\/[^>]+(>|$)/g, "");
					requiredattrdata.push(stripHtml(datanya) + '<br>');
					requiredattr = 1;
				}
			}

			if (requiredattr === 0) {
				$.post(actionUrl, $(formSelector).serialize(), function (data) {
					console.log(data);
					updateCsrfToken(data.csrf_hash);

					if (data.status === "success") {
						Swal.fire({
							text: "Data berhasil disimpan!",
							icon: "success",
							buttonsStyling: false,
							confirmButtonText: "Ok, got it!",
							customClass: {
								confirmButton: "btn btn-primary"
							}
						});

						loadingclose();

						if (slugRefresh !== '' && data.slug_refresh !== '') {
							setTimeout(() => {
								window.location.href = data.slug_refresh;
							}, 1000);
						}
						
						if (data.preview_url != '' && type != 'draft') {
							window.open(data.preview_url, '_blank');
						}

					} else {
						Swal.fire({
							text: "Data tidak berhasil disimpan!",
							icon: "error",
							buttonsStyling: false,
							confirmButtonText: "Tutup",
							customClass: {
								confirmButton: "btn btn-primary"
							}
						});
						loadingclose();
					}
				}, 'json').fail(function (jqxhr, status, error) {
					console.error("Request failed: " + error);

					if (jqxhr.status === 403) {
						$.get('<?php echo base_url('request_csrf_token'); ?>', function (data) {
							csrfHash = data.csrf_hash;
							updateCsrfToken(csrfHash);
						});
					}

					Swal.fire({
						text: "Terjadi kesalahan saat mengirim data!",
						icon: "error",
						buttonsStyling: false,
						confirmButtonText: "Coba Lagi",
						customClass: {
							confirmButton: "btn btn-danger"
						},
						didOpen: () => {
							$('.swal2-container').css('z-index', 99999);
						}
					});
					loadingclose();
				});
			} else {
				let datanya = requiredattrdata.join("");
				Swal.fire({
					html: "Masih ada data belum terisi:<br>" + datanya,
					icon: "error",
					buttonsStyling: false,
					confirmButtonText: "Lanjutkan Pengisian",
					customClass: {
						confirmButton: "btn btn-primary"
					}
				});
				loadingclose();
			}
		}
	});
}



	$( document ).ready(function() {
		
		
	
		// Add event listeners for drag and drop events
		<?php if($this->ortyd->getaccessdrag() == true){ ?>
		//dragList.addEventListener('dragstart', handleDragStart);
		//dragList.addEventListener('dragover', handleDragOver);
		//dragList.addEventListener('drop', handleDrop);
	<?php } ?>
	});
	function addsummernote(classnya) {
		
		 if (!document.getElementById('summernote-fix-style')) {
        const style = document.createElement('style');
        style.id = 'summernote-fix-style';
        style.innerHTML = `
        .note-editor.fullscreen .note-editable {
            background-color: #fff !important;
            color: #000 !important;
            z-index: 1050;
        }`;
        document.head.appendChild(style);
    }
	
    $('.' + classnya).each(function(index) {
        // Buat ID unik jika belum ada
        if (!$(this).attr('id')) {
            $(this).attr('id', 'summernote-' + Date.now() + '-' + index);
        }
		

        const editorId = $(this).attr('id');

        $('#' + editorId).summernote({
            height: 200,
            placeholder: 'Masukkan konten di sini...',
            tabsize: 2,
            toolbar: [
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['font', ['strikethrough', 'superscript', 'subscript']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview']],
                ['custom', ['slider', 'htmlblock']]
            ],
			callbacks: {
				onChange: function(contents, $editable) {
					// Setiap ada perubahan di summernote
					console.log('Perubahan:', contents);

					// Jika kamu ingin update kode HTML/preview:
					generateEditorContent(); // <-- panggil fungsi kamu untuk generate HTML & update textarea hidden
				}
			},
            buttons: {
               slider: function() {
				const button = $('<button type="button" class="note-btn btn-codeview note-codeview-keep" title="Insert Slider"><i class="note-icon-picture"></i></button>');
				button.on('click', function() {
					Swal.fire({
						title: 'Pilih Slider dan Class',
						html: `
							<select id="sliderSelect" class="form-control" multiple="multiple" style="width:100%; margin-bottom: 1em;"></select>
							<label for="classSelect" style="display:block; margin-bottom:0.5em;">Pilih Slider</label>
							<select id="classSelect" class="form-control" style="width:100%;">
								<option value="slider-normal" selected>slider-normal</option>
								<option value="slider-fullpage">slider-fullpage</option>
							</select>
						`,
						preConfirm: function() {
							const selected = $('#sliderSelect').val();
							const selectedClass = $('#classSelect').val();
							if (!selected || selected.length === 0) {
								Swal.showValidationMessage('Pilih slider terlebih dahulu.');
								return false;
							}
							if (!selectedClass) {
								Swal.showValidationMessage('Pilih class slider.');
								return false;
							}
							const shortcode = `[slider ids="${selected.join(',')}" class="${selectedClass}"]`;
							$('#' + editorId).summernote('insertText', shortcode);
						},
						showCancelButton: true,
						confirmButtonText: 'Insert Shortcode',
						cancelButtonText: 'Tutup',
						didOpen: function() {
							$('#sliderSelect').select2({
								placeholder: "Pilih beberapa slider",
								allowClear: true,
								dropdownParent: $('.swal2-container'),
								ajax: {
									type: "POST",
									url: "<?php echo base_url($headurl.'/select2'); ?>",
									dataType: 'json',
									delay: 250,
									data: function (params) {
										return {
											q: params.term,
											table: '<?php echo 'data_slider'; ?>',
											id: '<?php echo 'id'; ?>',
											name: '<?php echo 'title'; ?>',
											page: params.page,
											<?php echo $this->security->get_csrf_token_name(); ?> : csrfHash 
										};
									},
									processResults: function (data, params) {
										updateCsrfToken(data.csrf_hash)
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
								}
							});
						}
					});
				});
				return button[0];
			},
                htmlblock: function () {
                    const button = $('<button type="button" class="note-btn btn-codeview note-codeview-keep" title="Insert HTML Block"><i class="note-icon-table"></i></button>');
                    button.on('click', function () {
                        Swal.fire({
                            title: 'Masukkan Konten HTML',
                            input: 'textarea',
                            inputPlaceholder: 'Tulis konten HTML di sini...',
                            showCancelButton: true,
                            confirmButtonText: 'Sisipkan Blok',
                            cancelButtonText: 'Batal',
                            preConfirm: (innerContent) => {
                                const blockHtml = `
                                    <div class="row">
                                        <div class="col-lg-12">
                                            ${innerContent || '<!-- Konten di sini -->'}
                                        </div>
                                    </div>`;
                                $('#' + editorId).summernote('pasteHTML', blockHtml);
                            }
                        });
                    });
                    return button[0];
                }
            }
        });
    });
}

	
</script>

