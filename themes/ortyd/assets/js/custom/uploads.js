function uploadDropZone(div, label, url, file_max_upload, file_max_size,user_id,csrf,data_id = null, requiredform = null){
	
	if(requiredform != '' && requiredform != null){
		var bintang = ' *'
	}else{
		var bintang = ''
	}
	
	var html = '<!--begin::Input group-->'+
				'<div class="form-group row">'+
					'<!--begin::Label-->'+
						'<label class="col-lg-3">'+ label + bintang + '</label>'+
							'<!--end::Label-->'+
							'<!--begin::Col-->'+
							'<div class="col-lg-9">'+
								'<!--begin::Dropzone-->'+
								'<div class="dropzone dropzone-queue mb-2" id="'+div+'_dropzone">'+
									'<!--begin::Controls-->'+
									'<div class="dropzone-panel mb-lg-0 mb-2">'+
										'<a class="dropzone-select btn btn-sm btn-primary me-2">Unggah Dokumen</a>'+
										'<a class="dropzone-upload btn btn-sm btn-light-primary me-2" style="display:none">Unggah Semua</a>'+
										'<a class="dropzone-remove-all btn btn-sm btn-light-primary" style="display:none">Hapus Semua</a>'+
									'</div>'+
									'<!--end::Controls-->'+
										'<!--begin::Items-->'+
										'<div class="dropzone-items wm-200px">'+
											'<div class="dropzone-item file-dropzone" style="display:none">'+
												'<!--begin::File-->'+
												'<div class="dropzone-file">'+
													'<a target="_blank" class="dropzone-filename" title="some_image_file_name.jpg">'+
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
	previewNode.id = 0;
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
			this.on('error', function(file, response) {
				alert(response);
			})
			this.on("success", function(file, response) {
				var obj = JSON.parse(response);
				
				if(obj.message == 'success'){
					console.log(obj);
					$(id +' .dropzone-items .dropzone-item:last-child .dropzone-file .dropzone-filename').append('<input type="text" style="display:none" class="form-control" placeholder="'+label+'" name="evidence['+data_id+'][]" value="' + obj.id +'" />');
					
					$(id +' .dropzone-items .dropzone-item:last-child .dropzone-file .dropzone-filename').attr("title", obj.name);
					
					$(id +' .dropzone-items .dropzone-item:last-child .dropzone-file .dropzone-filename .dropzone-filename-title').html( obj.name);
					
					$(id +' .dropzone-items .dropzone-item:last-child .dropzone-file .dropzone-filename').attr("href", obj.path);
					
					$('#eviden_data_'+data_id+'_0').remove();
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
		c.append("csrf_ortyd_vms_name", csrf);
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
