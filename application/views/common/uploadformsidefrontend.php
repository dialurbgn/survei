

<div id="<?php echo $rows_column['name'].'_header'; ?>" draggable="true" class="drag-item col-lg-<?php echo $width_column; ?> py-3">
	<?php 
	$generate_btn = 0;
	if(isset($generate_dok)){
		if($generate_dok == 1){ 
			$generate_btn = 1;
			$generate_dok = 0;
		}
	}
	?>
	<div  id="<?php echo $rows_column['name'].'_up'; ?>"></div>
</div>
										
<script>

$( document ).ready(function() {
	
	var generate_btn = "<?php echo $generate_btn; ?>";
	var table = "<?php echo $module; ?>";
	var tableid = "<?php echo $rows_column['name']; ?>";
	
	<?php if(isset($_GET['file'])){
		if($_GET['file']==0){ 
	?>
		var idnyaheader = "0";
	<?php
	}else{
	?>
		var idnyaheader = "<?php echo $iddata; ?>";
	<?php } 
	
	}else{
	?>
		var idnyaheader = "<?php echo $iddata; ?>";
		
	<?php } ?>
	var idnya = "<?php echo ${$rows_column['name']}; ?>";
	var div = "<?php echo $rows_column['name'].'_up'; ?>"
	var uplabel = "<?php echo strip_tags($label_name); ?>";
	var requiredform = '<?php if($rows_column['is_nullable'] == 'NO'){echo ' required ';} ?>'
	if(requiredform == ''){
		requiredform = null
	}
	uploadFormJS(div,uplabel, "<?php echo fileserver_url.'proses_upload_datafront_x24440'; ?>", 3, 2,1,csrfHash,tableid, data_id = null, requiredform, generate_btn)

	getCover(div, idnyaheader, tableid, uplabel, requiredform, idnya)
});


function uploadFormJS(div, label, url, file_max_upload, file_max_size,user_id,csrf,tableid,data_id = null, requiredform = null, generate_btn = null){
	
	
	
	if(generate_btn == 1){
		var tableidid = "'"+tableid+"'";
		var generate_btn_html = '<button type="button"  class="btn btn-sm btn-danger me-2" onClick="generate('+tableidid+')"> Generate</button>';
		var stylebtnhide = ' style="display:none" '
	}else{
		var tableidid = "'"+tableid+"'";
		var generate_btn_html = '';
		var stylebtnhide = '';
	}
	
	var labelmodule = '<?php echo $module; ?>';
	var labelmodule = "'"+labelmodule+"'";
	var labeltext = "'"+label+"'";;
	<?php if($this->ortyd->getAksesEditNaming() == true){ ?>
	var onClick = 'onClick="changeTitle('+labelmodule+','+tableidid+','+labeltext+')"';
	var changelabel = '<span style="cursor:pointer" '+onClick+' ><i class="fa fa-edit"></i></span>';
	<?php }else{ ?>
	var onClick = '';
	var changelabel = '';
	<?php } ?>
	var html = '<!--begin::Input group-->'+
				'<div class="form-group row">'+
					'<!--begin::Label-->'+
						'<label class="col-lg-3 text-lg-right">'+ label + changelabel + '</label>'+
							'<!--end::Label-->'+
							'<!--begin::Col-->'+
							'<div class="col-lg-9">'+
								'<!--begin::Dropzone-->'+
								'<div class="dropzone dropzone-queue mb-2" id="'+div+'_dropzone">'+
									'<!--begin::Controls-->'+
									'<div class="dropzone-panel mb-lg-0 mb-2">'+
										generate_btn_html + 
										'<a '+stylebtnhide+' id="generate_'+tableid+'" class="dropzone-select btn btn-sm btn-primary me-2">Unggah </a>'+
										
										'<a class="dropzone-upload btn btn-sm btn-light-primary me-2" style="display:none">Unggah Semua</a>'+
										'<a class="dropzone-remove-all btn btn-sm btn-light-primary" style="display:none">Hapus Semua</a>'+
										'<span class="dropzone-text-muted form-text text-muted">Maximal file '+file_max_size+'MB dan Maximal Jumlah file '+file_max_upload+'.</span>'+
									'</div>'+
									'<!--end::Controls-->'+
										'<!--begin::Items-->'+
										'<div class="dropzone-customs dropzone-items wm-200px">'+
											'<div class="dropzone-item" style="display:none">'+
												'<!--begin::File-->'+
												'<div class="dropzone-file">'+
													'<a target="_blank" class="dropzone-filename dropzone-link" title="some_image_file_name.jpg">'+
														'<span data-dz-name class="dropzone-filename-title">some_image_file_name.jpg</span>'+
														'<strong>(<span data-dz-size>340kb</span>)</strong>'+
													'</a>'+

													'<div class="dropzone-error" data-dz-errormessage></div>'+
												'</div>'+
												'<!--end::File-->'+

												'<!--begin::Progress-->'+
												'<div class="dropzone-progress">'+
													'<div class="progress">'+
														'<div class="progress-bar bg-primary" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" data-dz-uploadprogress> '+
														'</div>'+
													'</div>'+
												'</div>'+
												'<!--end::Progress-->'+

												'<!--begin::Toolbar-->'+
												'<div class="dropzone-toolbar" >'+
													'<span class="dropzone-start"><i class="bi bi-play-fill fs-3"></i></span>'+
													'<span class="dropzone-cancel" data-dz-remove style="display: none;"><i class="bi bi-x fs-3"></i></span>'+
													'<span class="dropzone-delete" data-dz-remove><i class="bi bi-x fs-1"></i></span>'+
												'</div>'+
												'<!--end::Toolbar-->'+
										'</div>'+
								'</div>'+
								'<!--end::Items-->'+
								
								'<!--begin::Hint-->'+
								
								'<!--end::Hint-->'+
							
							'</div>'+
							
							
							
							'<!--end::Dropzone-->'+

							
						'</div>'+
					'<!--end::Col-->'+
				'</div>'+
			'<!--end::Input group-->';
	
	var e = document.createElement('div');
	e.innerHTML = html;
	console.log(div);
	const elmnt = document.getElementById(div);
	elmnt.appendChild(e);
	
	// set the dropzone container id
	//const id = "#kt_dropzonejs_example_2";
	const id = "#" + div + "_dropzone";
	const dropzone = document.querySelector(id);

	// set the preview element template
	var previewNode = dropzone.querySelector(".dropzone-item");
	previewNode.id = "";
	var previewTemplate = previewNode.parentNode.innerHTML;
	previewNode.parentNode.removeChild(previewNode);

	var myDropzone = new Dropzone(id, { // Make the whole body a dropzone
		url: url, // Set the url for your upload script location
		paramName:"userfile",
		<?php if(isset($acceptedFiles)){ ?>
			<?php if($acceptedFiles != null){ ?>
			acceptedFiles: "<?php echo $acceptedFiles; ?>",
			<?php }else{ ?>
			acceptedFiles: "image/*,.pdf,.xlsx,.xls",
			<?php } ?>
		<?php }else{ ?>
			acceptedFiles: "image/*,.pdf,.xlsx,.xls",
		<?php } ?>
		parallelUploads: file_max_upload,
		previewTemplate: previewTemplate,
		maxFilesize: file_max_size, // Max filesize in MB
		//autoQueue: false, // Make sure the files aren't queued until manually added
		previewsContainer: id + " .dropzone-items", // Define the container to display the previews
		clickable: id + " .dropzone-select", // Define the element that should be used as click trigger to select files.
		init: function() {
			this.on('error', function (file, response, xhr) {
				let message = "Terjadi kesalahan saat mengunggah file.";
				let statusCode = xhr?.status || null;

				try {
					// Jika response JSON (biasanya dari server kita)
					if (typeof response === "string") {
						response = JSON.parse(response);
					}
					updateCsrfToken(response.csrf_hash);
					message = response.message || message;
				} catch (e) {
					// Jika bukan JSON (misalnya HTML error page)
					if (statusCode === 403) {
						message = "Akses ditolak (403 Forbidden).";
					} else if (statusCode === 500) {
						message = "Kesalahan server (500 Internal Server Error).";
					} else if (typeof response === "string" && response.includes("403")) {
						message = "Akses tidak diizinkan.";
					}
				}

				Swal.fire({
					icon: 'error',
					title: 'Upload Gagal',
					text: message,
				});
				console.error("Upload Error:", response);
				
				$(id + ' .dropzone-items .dropzone-item:last-child').remove();
			});
			this.on("success", function(file, response) {
				var obj = JSON.parse(response);
				updateCsrfToken(obj.csrf_hash)
				if (obj.message === 'success') {
					console.log(obj);
					$(id + ' .dropzone-items .dropzone-item:last-child .dropzone-file .dropzone-filename')
						.append('<input type="text" style="display:none" class="form-control form-control-sm" placeholder="' + label + '" name="evidence[' + tableid + ']" value="' + obj.id + '" />')
						.attr("title", obj.name)
						.attr("href", obj.path);

					$(id + ' .dropzone-items .dropzone-item:last-child .dropzone-file .dropzone-filename .dropzone-filename-title')
						.html(obj.name);

					$('#eviden_data_' + tableid + '_0').remove();
				} else {
					// Remove item yang gagal di-upload
					$(id + ' .dropzone-items .dropzone-item:last-child').remove();

					// Tampilkan pesan menggunakan SweetAlert
					Swal.fire({
						icon: 'error',
						title: 'Upload Gagal',
						text: obj.message || 'Terjadi kesalahan saat mengunggah file.',
						confirmButtonText: 'OK'
					});
				}
			
				
			})
		}
	});

	myDropzone.on("addedfile", function (file) {
		// Hookup the start button
		file.previewElement.querySelector(id + " .dropzone-start").onclick = function () { myDropzone.enqueueFile(file); };
		const dropzoneItems = dropzone.querySelectorAll('.dropzone-item');
		dropzoneItems.forEach(dropzoneItem => {
			dropzoneItem.style.display = '';
		});
		dropzone.querySelector('.dropzone-upload').style.display = "inline-block";
		dropzone.querySelector('.dropzone-remove-all').style.display = "inline-block";
	});

	// Update the total progress bar
	myDropzone.on("totaluploadprogress", function (progress) {
		const progressBars = dropzone.querySelectorAll('.progress-bar');
		progressBars.forEach(progressBar => {
			progressBar.style.width = progress + "%";
		});
	});

	myDropzone.on("sending", function(a,b,c) {
		// Show the total progress bar when upload starts
		const progressBars = dropzone.querySelectorAll('.progress-bar');
		progressBars.forEach(progressBar => {
			progressBar.style.opacity = "1";
		});
		// And disable the start button
		a.previewElement.querySelector(id + " .dropzone-start").setAttribute("disabled", "disabled");
		<?php if(isset($pathdir)){ ?>
			<?php if($pathdir != '' && $pathdir != null){ ?>
				c.append("pathdir", '<?php echo $pathdir; ?>');
			<?php } ?>
		<?php } ?>
		<?php if(isset($tipedir)){ ?>
			<?php if($tipedir != '' && $tipedir != null){ ?>
				c.append("tipedir", '<?php echo $tipedir; ?>');
			<?php } ?>
		<?php } ?>
		a.token=Math.random();
		c.append("token_foto",a.token); //Menmpersiapkan token untuk masing masing foto
		c.append("user_id", user_id);
		c.append("file_name", label);
		c.append("pathdir", 'uploads');
		c.append("tipedir", 'pengaduan');
		c.append("<?php echo $this->security->get_csrf_token_name(); ?>", csrfHash);
	});

	// Hide the total progress bar when nothing's uploading anymore
	myDropzone.on("complete", function (progress) {
		const progressBars = dropzone.querySelectorAll('.dz-complete');

		setTimeout(function () {
			progressBars.forEach(progressBar => {
				progressBar.querySelector('.progress-bar').style.opacity = "0";
				progressBar.querySelector('.progress').style.opacity = "0";
				progressBar.querySelector('.dropzone-start').style.opacity = "0";
			});
		}, 300);
	});

	// Setup the buttons for all transfers
	dropzone.querySelector(".dropzone-upload").addEventListener('click', function () {
		myDropzone.enqueueFiles(myDropzone.getFilesWithStatus(Dropzone.ADDED));
	});

	// Setup the button for remove all files
	dropzone.querySelector(".dropzone-remove-all").addEventListener('click', function () {
		dropzone.querySelector('.dropzone-upload').style.display = "none";
		dropzone.querySelector('.dropzone-remove-all').style.display = "none";
		myDropzone.removeAllFiles(true);
	});

		// On all files completed upload
	myDropzone.on("queuecomplete", function (progress) {
		const uploadIcons = dropzone.querySelectorAll('.dropzone-upload');
		uploadIcons.forEach(uploadIcon => {
			uploadIcon.style.display = "none";
		});
	});

		// On all files removed
	myDropzone.on("removedfile", function (file) {
		if (myDropzone.files.length < 1) {
			dropzone.querySelector('.dropzone-upload').style.display = "none";
			dropzone.querySelector('.dropzone-remove-all').style.display = "none";
		}
	});
}


function getCover(div, id, tableid, uplabel, requiredform = null, iddata = null) {
    const idnya = "#" + div + "_dropzone";
    const divnya = "'" + idnya + "'";

    $.post('<?php echo base_url($headurl.'/getcover'); ?>', {
        id: id,
        tableid: tableid,
        <?php echo $this->security->get_csrf_token_name(); ?>: csrfHash
    }, function (data) {
        var obj = jQuery.parseJSON(data);
        updateCsrfToken(obj.csrf_hash);

        if (obj.message === 'success') {
            var data = obj.data;

            for (var key in data) {
                var file = data[key];
                var ext = file.name.split('.').pop().toLowerCase();

                var previewContent = "";

                if (ext === 'jpg' || ext === 'jpeg' || ext === 'png' || ext === 'gif' || ext === 'webp') {
                    previewContent = 
                        "<a href=\"" + file.path + "\" target=\"_blank\" class=\"dropzone-link\" title=\"" + file.name + "\">" +
                            "<img src=\"" + file.path + "\" alt=\"" + file.name + "\" style=\"max-width: 100px; max-height: 100px; border:1px solid #ccc; margin-right:10px;\">" +
                        "</a>";
                } else {
                    previewContent = 
                        "<a href=\"" + file.path + "\" target=\"_blank\" class=\"dropzone-filename dropzone-link\" title=\"" + file.name + "\">" +
                            "<span data-dz-name class=\"dropzone-filename-title\"><i class=\"fa fa-download\"></i> " + file.name + "</span>" +
                            "<strong>(<span data-dz-size>" + file.size + "kb</span>)</strong>" +
                        "</a>";
                }

                var html = 
                    "<div class=\"dropzone-item\" id=\"eviden_data_" + file.evidence_id + "\">" +
                        "<div class=\"dropzone-file\">" +
                            previewContent +
                            "<input type=\"text\" style=\"display:none\" class=\"form-control form-control-sm\" name=\"evidence[" + tableid + "]\" placeholder=\"" + uplabel + "\" value=\"" + file.id + "\" " + (requiredform ?? "") + " />" +
                            "<div class=\"dropzone-error\" data-dz-errormessage></div>" +
                        "</div>" +
                        "<div class=\"dropzone-toolbar\">" +
                            "<span class=\"dropzone-start\" style=\"display: none;\"><i class=\"bi bi-play-fill fs-3\"></i></span>" +
                            "<span class=\"dropzone-cancel\" data-dz-remove style=\"display: none;\"><i class=\"bi bi-x fs-3\"></i></span>" +
                            "<span class=\"dropzone-delete\" onClick=\"deletefile(" + divnya + ", " + file.evidence_id + ")\"><i class=\"bi bi-x fs-1\"></i></span>" +
                        "</div>" +
                    "</div>";

                $(idnya + ' .dropzone-items').append(html);
            }
        } else {
            if (requiredform != null) {
                var html = 
                    "<div class=\"dropzone-item\" id=\"eviden_data_" + tableid + "_0\" style=\"display:none\">" +
                        "<div class=\"dropzone-file\">" +
                            "<a href=\"#\" target=\"_blank\" class=\"dropzone-filename dropzone-link\" title=\"#\">" +
                                "<span data-dz-name class=\"dropzone-filename-title\">#</span>" +
                                "<strong>(<span data-dz-size>0kb</span>)</strong>" +
                            "</a>" +
                            "<input type=\"text\" style=\"display:none\" class=\"form-control form-control-sm\" name=\"evidence[" + tableid + "]\" placeholder=\"" + uplabel + "\" value=\"\" " + requiredform + " />" +
                            "<div class=\"dropzone-error\" data-dz-errormessage></div>" +
                        "</div>" +
                    "</div>";

                $(idnya + ' .dropzone-items').append(html);
            }
        }
    });
}

function uploadFormJSView(div, label, url, file_max_upload, file_max_size,user_id,csrf,tableid,data_id = null, requiredform = null){
	

	var html = '<!--begin::Input group-->'+
				'<div class="form-group row">'+
					'<!--begin::Label-->'+
						'<label class="col-lg-3 col-form-label text-lg-right">'+ label +'</label>'+
							'<!--end::Label-->'+
							'<!--begin::Col-->'+
							'<div class="col-lg-9">'+
								'<!--begin::Dropzone-->'+
								'<div class="dropzone dropzone-queue mb-2" id="'+div+'_dropzone">'+
									'<!--begin::Controls-->'+
									'<div class="dropzone-panel mb-lg-0 mb-2" style="display:none">'+
										'<a class="dropzone-select btn btn-sm btn-primary me-2">Unggah </a>'+
										'<a class="dropzone-upload btn btn-sm btn-light-primary me-2" style="display:none">Unggah Semua</a>'+
										'<a class="dropzone-remove-all btn btn-sm btn-light-primary" style="display:none">Hapus Semua</a>'+
										'<span class="dropzone-text-muted form-text text-muted">Maximal file '+file_max_size+'MB dan Maximal Jumlah file '+file_max_upload+'.</span>'+
									'</div>'+
									'<!--end::Controls-->'+
										'<!--begin::Items-->'+
										'<div class="dropzone-customs dropzone-items wm-200px">'+
											'<div class="dropzone-item" style="display:none">'+
												'<!--begin::File-->'+
												'<div class="dropzone-file">'+
													'<a target="_blank" class="dropzone-filename dropzone-link" title="some_image_file_name.jpg">'+
														'<span data-dz-name class="dropzone-filename-title">some_image_file_name.jpg</span>'+
														'<strong>(<span data-dz-size>340kb</span>)</strong>'+
													'</a>'+

													'<div class="dropzone-error" data-dz-errormessage></div>'+
												'</div>'+
												'<!--end::File-->'+

												'<!--begin::Progress-->'+
												'<div class="dropzone-progress">'+
													'<div class="progress">'+
														'<div class="progress-bar bg-primary" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" data-dz-uploadprogress> '+
														'</div>'+
													'</div>'+
												'</div>'+
												'<!--end::Progress-->'+

												'<!--begin::Toolbar-->'+
												
												'<!--end::Toolbar-->'+
										'</div>'+
								'</div>'+
								'<!--end::Items-->'+
								
								'<!--begin::Hint-->'+
								
								'<!--end::Hint-->'+
							
							'</div>'+
							
							
							
							'<!--end::Dropzone-->'+

							
						'</div>'+
					'<!--end::Col-->'+
				'</div>'+
			'<!--end::Input group-->';
	
	var e = document.createElement('div');
	e.innerHTML = html;
	console.log(div);
	const elmnt = document.getElementById(div);
	elmnt.appendChild(e);
	
	// set the dropzone container id
	//const id = "#kt_dropzonejs_example_2";
	const id = "#" + div + "_dropzone";
	const dropzone = document.querySelector(id);

	// set the preview element template
	var previewNode = dropzone.querySelector(".dropzone-item");
	previewNode.id = "";
	var previewTemplate = previewNode.parentNode.innerHTML;
	previewNode.parentNode.removeChild(previewNode);

	var myDropzone = new Dropzone(id, { // Make the whole body a dropzone
		url: url, // Set the url for your upload script location
		paramName:"userfile",
		parallelUploads: file_max_upload,
		previewTemplate: previewTemplate,
		maxFilesize: file_max_size, // Max filesize in MB
		//autoQueue: false, // Make sure the files aren't queued until manually added
		previewsContainer: id + " .dropzone-items", // Define the container to display the previews
		clickable: id + " .dropzone-select", // Define the element that should be used as click trigger to select files.
		init: function() {
			this.on('error', function (file, response, xhr) {
				let message = "Terjadi kesalahan saat mengunggah file.";
				let statusCode = xhr?.status || null;

				try {
					// Jika response JSON (biasanya dari server kita)
					if (typeof response === "string") {
						response = JSON.parse(response);
					}
					updateCsrfToken(response.csrf_hash);
					message = response.message || message;
				} catch (e) {
					// Jika bukan JSON (misalnya HTML error page)
					if (statusCode === 403) {
						message = "Akses ditolak (403 Forbidden).";
					} else if (statusCode === 500) {
						message = "Kesalahan server (500 Internal Server Error).";
					} else if (typeof response === "string" && response.includes("403")) {
						message = "Akses tidak diizinkan.";
					}
				}

				Swal.fire({
					icon: 'error',
					title: 'Upload Gagal',
					text: message,
				});
				console.error("Upload Error:", response);
			});
			this.on("success", function(file, response) {
				var obj = JSON.parse(response);
				updateCsrfToken(obj.csrf_hash);
				console.log(obj);
				
				if (obj.message === 'success') {
					console.log(obj);
					$(id + ' .dropzone-items .dropzone-item:last-child .dropzone-file .dropzone-filename')
						.append('<input type="text" style="display:none" class="form-control form-control-sm" placeholder="' + label + '" name="evidence[' + tableid + ']" value="' + obj.id + '" />')
						.attr("title", obj.name)
						.attr("href", obj.link);

					$(id + ' .dropzone-items .dropzone-item:last-child .dropzone-file .dropzone-filename .dropzone-filename-title')
						.html(obj.name);

					$('#eviden_data_' + tableid + '_0').remove();
				} else {
					// Remove item yang gagal di-upload
					$(id + ' .dropzone-items .dropzone-item:last-child').remove();

					// Tampilkan pesan menggunakan SweetAlert
					Swal.fire({
						icon: 'error',
						title: 'Upload Gagal',
						text: obj.message || 'Terjadi kesalahan saat mengunggah file.',
						confirmButtonText: 'OK'
					});
				}
				
				
			})
		}
	});

	myDropzone.on("addedfile", function (file) {
		// Hookup the start button
		file.previewElement.querySelector(id + " .dropzone-start").onclick = function () { myDropzone.enqueueFile(file); };
		const dropzoneItems = dropzone.querySelectorAll('.dropzone-item');
		dropzoneItems.forEach(dropzoneItem => {
			dropzoneItem.style.display = '';
		});
		dropzone.querySelector('.dropzone-upload').style.display = "inline-block";
		dropzone.querySelector('.dropzone-remove-all').style.display = "inline-block";
	});

	// Update the total progress bar
	myDropzone.on("totaluploadprogress", function (progress) {
		const progressBars = dropzone.querySelectorAll('.progress-bar');
		progressBars.forEach(progressBar => {
			progressBar.style.width = progress + "%";
		});
	});

	myDropzone.on("sending", function(a,b,c) {
		// Show the total progress bar when upload starts
		const progressBars = dropzone.querySelectorAll('.progress-bar');
		progressBars.forEach(progressBar => {
			progressBar.style.opacity = "1";
		});
		// And disable the start button
		a.previewElement.querySelector(id + " .dropzone-start").setAttribute("disabled", "disabled");
		a.token=Math.random();
		c.append("token_foto",a.token); //Menmpersiapkan token untuk masing masing foto
		c.append("user_id", user_id);
		c.append("file_name", label);
		c.append("<?php echo $this->security->get_csrf_token_name(); ?>", csrfHash);
	});

	// Hide the total progress bar when nothing's uploading anymore
	myDropzone.on("complete", function (progress) {
		const progressBars = dropzone.querySelectorAll('.dz-complete');

		setTimeout(function () {
			progressBars.forEach(progressBar => {
				progressBar.querySelector('.progress-bar').style.opacity = "0";
				progressBar.querySelector('.progress').style.opacity = "0";
				progressBar.querySelector('.dropzone-start').style.opacity = "0";
			});
		}, 300);
	});

	// Setup the buttons for all transfers
	dropzone.querySelector(".dropzone-upload").addEventListener('click', function () {
		myDropzone.enqueueFiles(myDropzone.getFilesWithStatus(Dropzone.ADDED));
	});

	// Setup the button for remove all files
	dropzone.querySelector(".dropzone-remove-all").addEventListener('click', function () {
		dropzone.querySelector('.dropzone-upload').style.display = "none";
		dropzone.querySelector('.dropzone-remove-all').style.display = "none";
		myDropzone.removeAllFiles(true);
	});

		// On all files completed upload
	myDropzone.on("queuecomplete", function (progress) {
		const uploadIcons = dropzone.querySelectorAll('.dropzone-upload');
		uploadIcons.forEach(uploadIcon => {
			uploadIcon.style.display = "none";
		});
	});

		// On all files removed
	myDropzone.on("removedfile", function (file) {
		if (myDropzone.files.length < 1) {
			dropzone.querySelector('.dropzone-upload').style.display = "none";
			dropzone.querySelector('.dropzone-remove-all').style.display = "none";
		}
	});
}

function uploadFormJSCustom(div, div_text, label, url, file_max_upload, file_max_size,user_id,csrf,tableid,data_id = null, requiredform = null){
	

	var html = '<!--begin::Input group-->'+
				'<div class="form-group row">'+
					'<!--begin::Label-->'+
						
							'<!--end::Label-->'+
							'<!--begin::Col-->'+
							'<div class="col-lg-12">'+
								'<!--begin::Dropzone-->'+
								'<div class="dropzone dropzone-queue mb-2" id="'+div+'_dropzone">'+
									'<!--begin::Controls-->'+
									'<div class="dropzone-panel mb-lg-0 mb-2">'+
										'<a class="dropzone-select btn btn-sm btn-primary me-2">Unggah </a>'+
										'<a class="dropzone-upload btn btn-sm btn-light-primary me-2" style="display:none">Unggah Semua</a>'+
										'<a class="dropzone-remove-all btn btn-sm btn-light-primary" style="display:none">Hapus Semua</a>'+
									'</div>'+
									'<!--end::Controls-->'+
										'<!--begin::Items-->'+
										'<div class="dropzone-customs dropzone-items wm-200px">'+
											'<div class="dropzone-item" style="display:none">'+
												'<!--begin::File-->'+
												'<div class="dropzone-file">'+
													'<a target="_blank" class="dropzone-filename dropzone-link" title="some_image_file_name.jpg">'+
														'<span data-dz-name class="dropzone-filename-title"><i class="fa fa-download"></i> some_image_file_name.jpg</span>'+
														'<strong>(<span data-dz-size>340kb</span>)</strong>'+
													'</a>'+

													'<div class="dropzone-error" data-dz-errormessage></div>'+
												'</div>'+
												'<!--end::File-->'+

												'<!--begin::Progress-->'+
												'<div class="dropzone-progress">'+
													'<div class="progress">'+
														'<div class="progress-bar bg-primary" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" data-dz-uploadprogress> '+
														'</div>'+
													'</div>'+
												'</div>'+
												'<!--end::Progress-->'+

												'<!--begin::Toolbar-->'+
												'<div class="dropzone-toolbar">'+
													'<span class="dropzone-start"><i class="bi bi-play-fill fs-3"></i></span>'+
													'<span class="dropzone-cancel" data-dz-remove style="display: none;"><i class="bi bi-x fs-3"></i></span>'+
													'<span class="dropzone-delete" data-dz-remove><i class="bi bi-x fs-1"></i></span>'+
												'</div>'+
												'<!--end::Toolbar-->'+
										'</div>'+
								'</div>'+
								'<!--end::Items-->'+
							'</div>'+
							'<!--end::Dropzone-->'+

							'<!--begin::Hint-->'+
							'<span class="dropzone-text-muted form-text text-muted">Maximal file '+file_max_size+'MB dan Maximal Jumlah file '+file_max_upload+'.</span>'+
							'<!--end::Hint-->'+
						'</div>'+
					'<!--end::Col-->'+
				'</div>'+
			'<!--end::Input group-->';
	
	var e = document.createElement('div');
	e.innerHTML = html;
	console.log(div);
	const elmnt = document.getElementById(div);
	elmnt.appendChild(e);
	
	// set the dropzone container id
	//const id = "#kt_dropzonejs_example_2";
	const id = "#" + div + "_dropzone";
	const dropzone = document.querySelector(id);

	// set the preview element template
	var previewNode = dropzone.querySelector(".dropzone-item");
	previewNode.id = "";
	var previewTemplate = previewNode.parentNode.innerHTML;
	previewNode.parentNode.removeChild(previewNode);

	var myDropzone = new Dropzone(id, { // Make the whole body a dropzone
		url: url, // Set the url for your upload script location
		paramName:"userfile",
		parallelUploads: file_max_upload,
		previewTemplate: previewTemplate,
		maxFilesize: file_max_size, // Max filesize in MB
		//autoQueue: false, // Make sure the files aren't queued until manually added
		previewsContainer: id + " .dropzone-items", // Define the container to display the previews
		clickable: id + " .dropzone-select", // Define the element that should be used as click trigger to select files.
		init: function() {
			this.on('error', function (file, response, xhr) {
				let message = "Terjadi kesalahan saat mengunggah file.";
				let statusCode = xhr?.status || null;

				try {
					// Jika response JSON (biasanya dari server kita)
					if (typeof response === "string") {
						response = JSON.parse(response);
					}
					updateCsrfToken(response.csrf_hash);
					message = response.message || message;
				} catch (e) {
					// Jika bukan JSON (misalnya HTML error page)
					if (statusCode === 403) {
						message = "Akses ditolak (403 Forbidden).";
					} else if (statusCode === 500) {
						message = "Kesalahan server (500 Internal Server Error).";
					} else if (typeof response === "string" && response.includes("403")) {
						message = "Akses tidak diizinkan.";
					}
				}

				Swal.fire({
					icon: 'error',
					title: 'Upload Gagal',
					text: message,
				});
				console.error("Upload Error:", response);
			});
			this.on("success", function(file, response) {
				var obj = JSON.parse(response);
				updateCsrfToken(obj.csrf_hash);
				
				if(obj.message == 'success'){
					console.log(obj);
					$('#'+div_text).val(obj.id);
					
					$(id +' .dropzone-items .dropzone-item:last-child .dropzone-file .dropzone-filename').attr("title", obj.name);
					
					$(id +' .dropzone-items .dropzone-item:last-child .dropzone-file .dropzone-filename').attr("href", obj.link);
					
					$(id +' .dropzone-items .dropzone-item:last-child .dropzone-file .dropzone-filename .dropzone-filename-title').html( '<i class="fa fa-download"></i> '+obj.name);
				}else{
					alert(response);
				}
				
				
			})
		}
	});

	myDropzone.on("addedfile", function (file) {
		// Hookup the start button
		file.previewElement.querySelector(id + " .dropzone-start").onclick = function () { myDropzone.enqueueFile(file); };
		const dropzoneItems = dropzone.querySelectorAll('.dropzone-item');
		dropzoneItems.forEach(dropzoneItem => {
			dropzoneItem.style.display = '';
		});
		dropzone.querySelector('.dropzone-upload').style.display = "inline-block";
		dropzone.querySelector('.dropzone-remove-all').style.display = "inline-block";
	});

	// Update the total progress bar
	myDropzone.on("totaluploadprogress", function (progress) {
		const progressBars = dropzone.querySelectorAll('.progress-bar');
		progressBars.forEach(progressBar => {
			progressBar.style.width = progress + "%";
		});
	});

	myDropzone.on("sending", function(a,b,c) {
		// Show the total progress bar when upload starts
		const progressBars = dropzone.querySelectorAll('.progress-bar');
		progressBars.forEach(progressBar => {
			progressBar.style.opacity = "1";
		});
		// And disable the start button
		a.previewElement.querySelector(id + " .dropzone-start").setAttribute("disabled", "disabled");
		a.token=Math.random();
		c.append("token_foto",a.token); //Menmpersiapkan token untuk masing masing foto
		c.append("user_id", user_id);
		c.append("file_name", label);
		c.append("<?php echo $this->security->get_csrf_token_name(); ?>", csrfHash);
	});

	// Hide the total progress bar when nothing's uploading anymore
	myDropzone.on("complete", function (progress) {
		const progressBars = dropzone.querySelectorAll('.dz-complete');

		setTimeout(function () {
			progressBars.forEach(progressBar => {
				progressBar.querySelector('.progress-bar').style.opacity = "0";
				progressBar.querySelector('.progress').style.opacity = "0";
				progressBar.querySelector('.dropzone-start').style.opacity = "0";
			});
		}, 300);
	});

	// Setup the buttons for all transfers
	dropzone.querySelector(".dropzone-upload").addEventListener('click', function () {
		myDropzone.enqueueFiles(myDropzone.getFilesWithStatus(Dropzone.ADDED));
	});

	// Setup the button for remove all files
	dropzone.querySelector(".dropzone-remove-all").addEventListener('click', function () {
		dropzone.querySelector('.dropzone-upload').style.display = "none";
		dropzone.querySelector('.dropzone-remove-all').style.display = "none";
		myDropzone.removeAllFiles(true);
	});

		// On all files completed upload
	myDropzone.on("queuecomplete", function (progress) {
		const uploadIcons = dropzone.querySelectorAll('.dropzone-upload');
		uploadIcons.forEach(uploadIcon => {
			uploadIcon.style.display = "none";
		});
	});

		// On all files removed
	myDropzone.on("removedfile", function (file) {
		if (myDropzone.files.length < 1) {
			dropzone.querySelector('.dropzone-upload').style.display = "none";
			dropzone.querySelector('.dropzone-remove-all').style.display = "none";
		}
	});
}

function deletefile(div, id){
		$(div +' #eviden_data_' + id).remove();
}
	
function validateDropzoneRequired(div_id, placeholder) {
	var dropzone = Dropzone.forElement('#' + div_id + '_dropzone');
	if (dropzone.getAcceptedFiles().length === 0) {
		return placeholder; // stop form submission
	}
	return true;
}

</script>
