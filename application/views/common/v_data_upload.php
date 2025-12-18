
<script>

	
var bar1;
let dialogload = null;
function progresbar(){
	
	dialogload = bootbox.dialog({
		//closeButton: false,
        title: 'Ektrak Data Excel ...',
        message: '<div id="myItem1" class="ldBar label-center" data-value="0" data-preset="circle"></div><div id="listobj_upload"></div>'
    });

	dialogload.find('.bootbox-close-button').hide();
	
	dialogload.on( "hide" , function() {
		table.draw();
	});
	
	/* construct manually */
	bar1 = new ldBar("#myItem1");
	bar1.set(1);
}

function uploadbatch(){
	var uploadHtml = "<div>" +
		"<label class='upload-area' style='width:100%;text-align:left;' for='fupload'> Pilih File (Excel) </br>" +
		'<input id="file_manual" id="file_manual" type="file" class="form-control file_manual" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" placeholder="Choose File" >' +
		"<i class='fa fa-cloud-upload fa-3x'></i>" +
		"<br />" +
		"</label>" +
		"<br />" +
		"<br />" +
		'<a target="_blank" class="form-control btn btn-simpan btn-primary" href="<?php echo base_url('uploads/format/format_data.xlsx'); ?>"><i class="ti ti-download"></i> Download Contoh Format</a>' +
		"<br />" +
		"<span style='margin-left:5px !important;' id='fileList'></span>" +
		"</div><div class='clearfix'></div>";

		bootbox.dialog({
			message: uploadHtml,
			title: 'Impor data',
			buttons: {
				success: {
					label: 'Impor data',
						className: "btn btn-create btn-primary",
							callback: function () {
								importdatabatch($('#file_manual'))
						}
				}
			},
			onEscape: function() {
				table.draw();
			},
		});
};

function upload(){
	var uploadHtml = "<div>" +
		"<label class='upload-area' style='width:100%;text-align:left;' for='fupload'> Pilih File (Excel) </br>" +
		'<input id="file_manual" id="file_manual" type="file" class="form-control file_manual" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" placeholder="Choose File" >' +
		"<i class='fa fa-cloud-upload fa-3x'></i>" +
		"<br />" +
		"</label>" +
		"<br />" +
		"<br />" +
		'<a target="_blank" class="form-control btn btn-simpan btn-primary" href="<?php echo base_url('uploads/format/format_data.xlsx'); ?>"><i class="ti ti-download"></i> Download Contoh Format</a>' +
		"<br />" +
		"<span style='margin-left:5px !important;' id='fileList'></span>" +
		"</div><div class='clearfix'></div>";

		bootbox.dialog({
			message: uploadHtml,
			title: 'Impor data',
			buttons: {
				success: {
					label: 'Impor data',
						className: "btn btn-create btn-primary",
							callback: function () {
								importdata($('#file_manual'))
						}
				}
			},
			onEscape: function() {
				table.draw();
			},
		});
};
					
var loadingimportbatch;
function importdatabatch(filenya) {
											
	var y;
	var x=0;
	var message_data = true;
	var boxdelete = bootbox.confirm({
		title: "Konfirmasi",
		message: "Apakah anda yakin akan mengimpor data ?",
		buttons: {
			cancel: {
				label: '<i class="ti ti-close"></i> Tidak'
			},
			confirm: {
				label: '<i class="ti ti-upload"></i> Iya Yakin'
			}
		},
		callback: function (result) {
			if(result==true){
				loadingimport = bootbox.dialog({
					title: 'Upload Data Excel...',
					message: '<p><i class="fa fa-spin fa-spinner"></i> Loading...</p>',
					className: 'loadingdialog',
					closeButton: false
				});
				
				var data;

				data = new FormData();
				data.append('file', filenya[0].files[0]);
				data.append('<?php echo $this->security->get_csrf_token_name(); ?>', csrfHash || $('#csrf_token').val());

				try {								
					$.ajax({
						url: '<?php echo base_url($headurl.'/importwithprogressbatchtindaklanjut'); ?>',
						data: data,
						processData: false,
						type: 'POST',
						contentType: false,
						error: function (request, status, error) {
							loadingimport.find('.bootbox-body').html('Error !, Data format error');
							setTimeout(function(){
								loadingimport.modal('hide'); 
							}, 2000);
						},
						success: function (data) {		
							//console.log(data)
							
							var n = data.search("Line: ");
							if(n <= 0 && (data != null && data != '')){
								if(data){
									var obj = JSON.parse(data);
									updateCsrfToken(obj.csrf_hash)
									if(obj.message == 'success'){
										var result = obj.data
										var totalx = obj.total
										loadingimport.find('.bootbox-body').html('success, Data uploaded');
										setTimeout(function(){
											loadingimport.modal('hide'); 
										}, 2000);
									}else{
										loadingimport.find('.bootbox-body').html('Error 1 !, Data format error');
										setTimeout(function(){
											loadingimport.modal('hide'); 
										}, 2000);
									}
								}else{
									loadingimport.find('.bootbox-body').html('Error 2 !, Data format error');
									setTimeout(function(){
										loadingimport.modal('hide'); 
									}, 2000);
								}						
							}else{
								loadingimport.find('.bootbox-body').html('Error 3 !, Data format error');
								setTimeout(function(){
									loadingimport.modal('hide'); 
								}, 2000);
							}
															
						}
					});
					
				}catch(e) {
					loadingimport.find('.bootbox-body').html('Error 4 !, Data format error');
					setTimeout(function(){
						loadingimport.modal('hide'); 
					}, 2000);
				}
				//e.preventDefault();
			}
		}										
	})
};

var loadingimport;
function importdata(filenya) {
											
	var y;
	var x=0;
	var message_data = true;
	var boxdelete = bootbox.confirm({
		title: "Konfirmasi",
		message: "Apakah anda yakin akan mengimpor data ?",
		buttons: {
			cancel: {
				label: '<i class="ti ti-close"></i> Tidak'
			},
			confirm: {
				label: '<i class="ti ti-upload"></i> Iya Yakin'
			}
		},
		callback: function (result) {
			if(result==true){
				loadingimport = bootbox.dialog({
					title: 'Upload Data Excel...',
					message: '<p><i class="fa fa-spin fa-spinner"></i> Loading...</p>',
					className: 'loadingdialog',
					closeButton: false
				});
				
				var data;

				data = new FormData();
				data.append('file', filenya[0].files[0]);
				data.append('<?php echo $this->security->get_csrf_token_name(); ?>', csrfHash || $('#csrf_token').val());

				try {								
					$.ajax({
						url: '<?php echo base_url($headurl.'/importwithprogress'); ?>',
						data: data,
						processData: false,
						type: 'POST',
						contentType: false,
						error: function (request, status, error) {
							loadingimport.find('.bootbox-body').html('Error !, Data format error');
							setTimeout(function(){
								loadingimport.modal('hide'); 
							}, 2000);
						},
						success: function (data) {		
							//console.log(data)
							
							var n = data.search("Line: ");
							if(n <= 0 && (data != null && data != '')){
								if(data){
									var obj = JSON.parse(data);
									updateCsrfToken(obj.csrf_hash)
									if(obj.message == 'success'){
										var result = obj.data
										var totalx = obj.total
										setTimeout(function(){
											loadingimport.modal('hide'); 
											progresbar()
											savingData(result,x,totalx)
										}, 1000);

									}else{
										loadingimport.find('.bootbox-body').html('Error 1 !, Data format error');
										setTimeout(function(){
											loadingimport.modal('hide'); 
										}, 2000);
									}
								}else{
									loadingimport.find('.bootbox-body').html('Error 2 !, Data format error');
									setTimeout(function(){
										loadingimport.modal('hide'); 
									}, 2000);
								}						
							}else{
								loadingimport.find('.bootbox-body').html('Error 3 !, Data format error');
								setTimeout(function(){
									loadingimport.modal('hide'); 
								}, 2000);
							}
															
						}
					});
					
				}catch(e) {
					loadingimport.find('.bootbox-body').html('Error 4 !, Data format error');
					setTimeout(function(){
						loadingimport.modal('hide'); 
					}, 2000);
				}
				//e.preventDefault();
			}
		}										
	})
};

var tmp_result = null;
var tmp_x = null;
var tmp_totalx = null;
function savingData(result,x,totalx){
	
	tmp_result = result;
	tmp_x = x;
	tmp_totalx = totalx;
	
	if(x==0){
		y = 1
	}else{
		y = (x/totalx) * 100
	}
	
	y = Math.round(y)

	var datax = x;
	
	if(typeof result[datax] == "undefined" || result[datax] == null){
		setTimeout(function() {
			bar1.set(100);
			dialogload.find('.bootbox-close-button').show();
			table.draw();
			//dialogload.find('.bootbox-body').html('success');
			//dialogload.modal('hide'); 
		}, 1);
	}else{
			$.post('<?php echo base_url($headurl.'/saveDB'); ?>',{ 
				data : JSON.stringify(result[datax]),
				<?php echo $this->security->get_csrf_token_name(); ?> : csrfHash || $('#csrf_token').val()
			}, function (data) {
				 updateCsrfToken(data.csrf_hash)
				if(data.message == "success"){
					//console.log(x)
					bar1.set(y);
					
					laporan_no = data.laporan_no
					
					if(data.status == "Replace"){
						$('#listobj_upload').prepend('<div style="color:#FFC000">'+x+'. '+ laporan_no + ' - (' + data.status + ')</div>')
					}else{
						$('#listobj_upload').prepend('<div style="color:#013220">'+x+'. '+ laporan_no + ' - (' + data.status + ')</div>')
					}
					
					//$('#listobj_upload').prepend('<div>'+x+'. '+ laporan_no +' - '+nama_mitra+ ' - ' + nama_pekerjaan + ' - (' + data.message + ')</div>')

					if(y == 100 && dialogload != null && x == (totalx) ){
						setTimeout(function() {
							dialogload.find('.bootbox-close-button').show();
							//dialogload.find('.bootbox-body').html('success');
							//dialogload.modal('hide'); 
						}, 100);
					}else{	
						x = x + 1;
						savingData(result,x,totalx)
					}
				}else{
					
					laporan_no = data.laporan_no
					$('#listobj_upload').prepend('<div style="color:#FF0000">Line : '+x+'. '+ laporan_no +' - (' + data.message + ')</div>')
					
					bar1.set(y);
					
					if(y == 100 && dialogload != null && x == (totalx) ){
						setTimeout(function() {
							dialogload.find('.bootbox-close-button').show();
							//dialogload.find('.bootbox-body').html('success');
							//dialogload.modal('hide'); 
						}, 100);
					}else{		
						x = x + 1;
						savingData(result,x,totalx)
					}	
																									
				}

				return true;
			}, 'json')
			.fail(function(xhr, status, error) {
				savingData(tmp_result,tmp_x,tmp_totalx)
			});
	}
					
	
											
}

</script>