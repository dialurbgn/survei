		<script>

		function changeTitle(table_change, table_change_id, labelname = null){
			
			
			$.post('<?php echo base_url('dashboard/getnaming'); ?>',{
						value : labelname,
						table_change : table_change,
						table_change_id :table_change_id,
						<?php echo $this->security->get_csrf_token_name(); ?> : csrfHash 
				},function (data) {	
						if(data != 'null'){
							var obj = jQuery.parseJSON(data);
							updateCsrfToken(obj.csrf_hash)
							if(obj.status == 'success'){
								
								console.log(obj.data.meta_only_name)
								
								meta_size = obj.data.meta_size
								meta_only_name = obj.data.meta_only_name
								meta_tipe = obj.data.meta_tipe
								meta_required = obj.data.meta_required
								meta_table_ref = obj.data.meta_table_ref
								meta_table_id_ref = obj.data.meta_table_id_ref
								meta_table_name_ref = obj.data.meta_table_name_ref
								meta_nested = obj.data.meta_nested
								meta_nested_field_id = obj.data.meta_nested_field_id
								meta_nested_field_name = obj.data.meta_nested_field_name
								meta_nested_field_custom_id = obj.data.meta_nested_field_custom_id
								meta_nested_field_custom_name = obj.data.meta_nested_field_custom_name
								meta_nested_ref_id = obj.data.meta_nested_ref_id
								meta_column = obj.data.meta_column
								meta_table_list =  obj.data.meta_table_list
								
								meta_table_id_ref_value = obj.data.meta_table_id_ref_value
								meta_table_name_ref_value = obj.data.meta_table_name_ref_value
								meta_nested_ref_id_value = obj.data.meta_nested_ref_id_value
								table_nested_custom_tipe = 0;
								
								if(meta_table_ref != null && meta_table_ref != 'null' && meta_table_ref != ''){
									select_table_list_option = '<option value="'+meta_table_ref+'" selected">'+meta_table_ref+'</option>'
									if(meta_table_ref == 'translate_table_select_option'){
										table_nested_custom_tipe = 1
									}
								}else{
									select_table_list_option = '';
								}
								$.each(meta_table_list, function(i, obj) {
								  select_table_list_option = select_table_list_option + '<option value="'+obj.id+'">'+obj.name+'</option>'
								  //alert(obj.name);
								});
								
								if(meta_size == 6){
									meta_size_option = '<option value="6" selected>MID</option>';
								}else if(meta_size == 12){
									meta_size_option = '<option value="12" selected>FULL</option>';
								}else if(meta_size == 3){
									meta_size_option = '<option value="3" selected>1/4</option>';
								}else if(meta_size == 4){
									meta_size_option = '<option value="4" selected>1/3</option>';
								}else{
									meta_size_option = '<option value="12" selected>FULL</option>';
								}
								
								if(meta_only_name == 1){
									meta_only_name_selected = ' selected ';
									meta_only_name_selected_no = '  ';
								}else{
									meta_only_name_selected = ' ';
									meta_only_name_selected_no = ' selected ';
								}
								
								if(meta_tipe == "SELECT"){
									meta_tipe_selected = 1;
								}else{
									meta_tipe_selected = 0;
								}
								
								if(meta_required == 0){
									meta_required_selected = ' ';
									meta_required_selected_no = ' selected ';
								}else{
									meta_required_selected = ' selected ';
									meta_required_selected_no = ' ';
								}
								
								if(meta_tipe == '' || meta_tipe == null){
									meta_tipe_value = ' ';
								}else{
									meta_tipe_value =  '<option value="'+ meta_tipe +'" selected>'+ meta_tipe +'</option>'
								}
								
								if(meta_nested == 1 && meta_tipe == "SELECT"){
									meta_nested_selected = ' selected ';
									meta_nested_selected_no = '  ';
								}else{
									meta_nested_selected = ' ';
									meta_nested_selected_no = ' selected ';
								}
								
								if(meta_nested_field_id != null && meta_nested_field_id != 'null' && meta_nested_field_id != ''){
									select_nested_option = '<option value="'+meta_nested_field_id+'" selected>'+meta_nested_field_name+'</option>'
								}else{
									select_nested_option = '';
								}
								
								if(meta_nested_field_custom_id != null && meta_nested_field_custom_id != 'null' && meta_nested_field_custom_id != ''){
									select_nested_custom_option = '<option value="'+meta_nested_field_custom_id+' selected">'+meta_nested_field_custom_name+'</option>'
								}else{
									select_nested_custom_option = '';
								}
								
								$.each(meta_column, function(i, obj) {
								  select_nested_option = select_nested_option + '<option value="'+obj.id+'">'+obj.name+'</option>'
								  //alert(obj.name);
								});
								
								html='<div class="form-group">'+
                               '<label>Cari Nama yg Mirip</label>'+
                               '<select id="select_mirip_value" class="form-control form-control-sm" style="width:100%"></select>'+
                           '</div>'+
									'<div class="col-lg-16" style="text-align:left;margin-top:30px">'+
										'<div class="form-group">'+
											'<label>NAMA</label>'+
											'<input type="text" class="form-control form-control-sm" id="value" placeholder="Value" value="'+labelname+'" />'+
										'</div>'+
										'<div class="form-group">'+
											'<label>REQUIRED</label>'+
											'<select class="form-control form-control-sm" id="requiredd" >'+
												'<option value="1" '+ meta_required_selected +'>YES</option>'+
												'<option value="0" '+ meta_required_selected_no +'>NO</option>'+
											'</select>'+
										'</div>'+
										'<div class="form-group">'+
												'<label>SIZE</label>'+
												'<select class="form-control form-control-sm" id="size" >'+
												    meta_size_option +
													'<option value="12">FULL</option>'+
													'<option value="6">MID</option>'+
													'<option value="3">1/4</option>'+
													'<option value="4">1/3</option>'+
												'</select>'+
											'</div>'+
										'<div class="form-group">'+
											'<label>ONLY NAME</label>'+
											'<select class="form-control form-control-sm" id="only" >'+
												'<option value="1" '+ meta_only_name_selected +'>YES</option>'+
												'<option value="0" '+ meta_only_name_selected_no +'>NO</option>'+
											'</select>'+
										'</div>'+
										'<div style="display:none" id="form_detail_tablenya">'+
											'<div class="form-group">'+
												'<label>TIPE</label>'+
												'<select class="form-control form-control-sm" id="tipe" >'+
													meta_tipe_value +
													'<option value="TEXT">TEXT</option>'+
													'<option value="PASSWORD">PASSWORD</option>'+
													'<option value="TEXTAREA">TEXTAREA</option>'+
													'<option value="TEXTEDITOR">TEXTEDITOR</option>'+
													'<option value="NUMBER">NUMBER</option>'+
													'<option value="DATE">DATE</option>'+
													'<option value="DATEONLYWITHZONE">DATE TIMEZONE</option>'+
													'<option value="DATETIME">DATETIME</option>'+
													'<option value="DATETIMEZONE">DATETIME TIMEZONE</option>'+
													'<option value="CURRENCY">CURRENCY</option>'+
													'<option value="SELECT">SELECT</option>'+
													'<option value="FILE">FILE</option>'+
													'<option value="LINK">LINK</option>'+
												'</select>'+
											'</div>'+
											'<div id="table_ref_form" style="display:none">'+
												'<div class="form-group">'+
													'<label>TABLE REF</label>'+
													'<select class="form-control form-control-sm" id="table_ref" >'+
														select_table_list_option +
													'</select>'+
												'</div>'+
												'<div class="form-group">'+
													'<label>TABLE ID</label>'+
													'<select class="form-control form-control-sm" id="table_id_ref" >'+
														'<option value="'+ meta_table_id_ref +'" selected>'+ meta_table_id_ref_value +'</option>'+
													'</select>'+
												'</div>'+
												'<div class="form-group">'+
													'<label>TABLE NAME</label>'+
													'<select class="form-control form-control-sm" id="table_name_ref" >'+
														'<option value="'+ meta_table_name_ref +'" selected>'+ meta_table_name_ref_value +'</option>'+
													'</select>'+
												'</div>'+
												'<div id="table_nested_custom" style="display:none">'+
													'<div class="form-group">'+
														'<label>Option Ref</label>'+
														'<select class="form-control form-control-sm" id="table_nested_custom_id" >'+
															select_nested_custom_option +
														'</select>'+
													'</div>'+
												'</div>'+
												'<div class="form-group">'+
													'<label>Nested</label>'+
													'<select class="form-control form-control-sm" id="nested" >'+
														'<option value="1" '+ meta_nested_selected +'>YES</option>'+
														'<option value="0" '+ meta_nested_selected_no +'>NO</option>'+
													'</select>'+
												'</div>'+
												'<div id="table_nested_form" style="display:none">'+
													'<div class="form-group">'+
														'<label>FIELD REF</label>'+
														'<select class="form-control form-control-sm" id="meta_nested_field_id" >'+
															select_nested_option +
														'</select>'+
													'</div>'+
													'<div class="form-group">'+
														'<label>REF ID</label>'+
														'<select class="form-control form-control-sm" id="nested_ref" >'+
															'<option value="'+ meta_nested_ref_id +'" selected>'+ meta_nested_ref_id_value +'</option>'+
														'</select>'+
													'</div>'+
												'</div>'+
											
											'</div>'+
										'</div>'+
									'</div>';
						
							
						Swal.fire({
							title: 'Update Naming ?',
							icon:'question',
							html:html,
							inputAttributes: {
							  id: "popupformedit",
							},
							heightAuto: false,	
							showCloseButton: true,
							showCancelButton: true,
							showDenyButton: false,
							confirmButtonColor: '#3085d6',
							cancelButtonColor: '#d33',
							confirmButtonText: '<i class="fa fa-edit"></i> Update',
							denyButtonText: '<i class="fa fa-undo"></i> Kembalikan',
							focusConfirm: false,
							showClass: {
								popup: 'animate__animated animate__fadeInDown'
							},
							hideClass: {
								popup: 'animate__animated animate__fadeOutUp'
							},
							preConfirm: () => {
								const size = Swal.getPopup().querySelector('#size').value
								const value = Swal.getPopup().querySelector('#value').value
								const only = Swal.getPopup().querySelector('#only').value
								const tipe = Swal.getPopup().querySelector('#tipe').value
								const requiredd = Swal.getPopup().querySelector('#requiredd').value
								const table_ref = Swal.getPopup().querySelector('#table_ref').value
								const table_id_ref = Swal.getPopup().querySelector('#table_id_ref').value
								const table_name_ref = Swal.getPopup().querySelector('#table_name_ref').value
								const meta_nested = Swal.getPopup().querySelector('#nested').value
								const meta_nested_field_id = Swal.getPopup().querySelector('#meta_nested_field_id').value
								const meta_nested_ref_id = Swal.getPopup().querySelector('#nested_ref').value
								const meta_nested_field_custom_id = Swal.getPopup().querySelector('#table_nested_custom_id').value
								
								
								if (!value) {
									Swal.showValidationMessage('Isi Naming')
								}else if (!tipe) {
									Swal.showValidationMessage('Isi Tipe')
								}else if (!only) {
									Swal.showValidationMessage('Isi Tipe Ubah')
								}else if (!requiredd) {
									Swal.showValidationMessage('Isi Required')
								}

								return { value: value,tipe: tipe,table_ref: table_ref,table_id_ref: table_id_ref,table_name_ref: table_name_ref,only: only,size: size,meta_nested: meta_nested,meta_nested_field_id: meta_nested_field_id,meta_nested_ref_id: meta_nested_ref_id, meta_nested_field_custom_id:meta_nested_field_custom_id, requiredd:requiredd}
							},
							willOpen: () => {

// Inisialisasi Select2 dengan append
$('#select_mirip_value').select2({
    dropdownParent: $('.swal2-container'),
    placeholder: 'Cari Meta Mirip...',
    minimumInputLength: 1,
    ajax: {
        url: '<?php echo base_url("dashboard/search_meta_like"); ?>',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: function(params){
            return {
                table: $('#table_ref').val() || meta_table,
                keyword: params.term,
                <?php echo $this->security->get_csrf_token_name(); ?>: csrfHash
            };
        },
        processResults: function(res){
            updateCsrfToken(res.csrf_hash);
            return {
                results: $.map(res.data, function(item){
                    return {
                        id: item.meta_id,
                        text: item.meta_value+ ' [' +item.meta_table+']',
                        data: item
                    };
                })
            };
        },
        cache: true
    }
});
// Event append saat pilih meta mirip
$('#select_mirip_value').on('select2:select', function(e){
    let item = e.params.data.data;

  // Fungsi global appendSelect
function appendSelect(selector, value, text){
    if(!value || value === 'null' || value === null) return; // skip jika null
    let $select = $(selector);
    let existing = $select.find("option[value='"+value+"']");
    if(existing.length === 0){
        // Append new option dan select
        let newOption = new Option(text, value, true, true);
        $select.append(newOption).trigger('change');
    } else {
        // Jika sudah ada, cukup select
        $select.val(value).trigger('change');
    }
}


    if(!item) return; // safety check

    // Append meta mirip ke semua Select2
    appendSelect('#table_ref', item.meta_table_ref, item.meta_table_ref);
    appendSelect('#table_id_ref', item.meta_table_id_ref, item.meta_table_id_ref);
    appendSelect('#table_name_ref', item.meta_table_name_ref, item.meta_table_name_ref);
    appendSelect('#meta_nested_field_id', item.meta_nested_field_id, item.meta_nested_field_id);
    appendSelect('#nested_ref', item.meta_nested_ref_id, item.meta_nested_ref_id);
    appendSelect('#table_nested_custom_id', item.meta_nested_field_custom_id, item.meta_nested_field_custom_name || item.meta_nested_field_custom_id);

    // Update field biasa
	$('#value').val(item.meta_value);
    $('#tipe').val(item.meta_tipe).trigger('change');
    $('#size').val(item.meta_size);
    $('#only').val(item.meta_only_name).trigger('change');
    $('#requiredd').val(item.meta_required);
    $('#nested').val(item.meta_nested).trigger('change');

});


								$('#only').on('change',function(){
									if($('#only').val() == "0"){
										$('#form_detail_tablenya').show();
									}else{
										$('#form_detail_tablenya').hide();
									}
									
								})
								
								$('#tipe').on('change',function(){
									if($('#tipe').val() == "SELECT"){
										$('#table_ref_form').show();
									}else{
										$('#table_ref_form').hide();
									}
									
								})
								
								$('#nested').on('change',function(){
									if($('#nested').val() == "1"){
										$('#table_nested_form').show();
									}else{
										$('#table_nested_form').hide();
									}
								})
								
								if(meta_only_name == 0){
									$('#form_detail_tablenya').show();
								}else{
									$('#form_detail_tablenya').hide();
								}
								
								if(meta_tipe_selected == 1){
									$('#table_ref_form').show();
								}else{
									$('#table_ref_form').hide();
								}
								
								if(table_nested_custom_tipe == 1){
									$('#table_nested_custom').show();
								}else{
									$('#table_nested_custom').hide();
								}
								
								if(meta_nested_field_custom_id == 'translate_table_select_option'){
									$('#table_nested_custom').show();
								}else{
									$('#table_nested_custom').hide();
								}
								
								if(meta_nested == 1){
									$('#table_nested_form').show();
								}else{
									$('#table_nested_form').hide();
								}
								
								$('#meta_nested_field_id').select2({
									 dropdownParent: $('.swal2-container')
								});
								
								$('#table_ref').select2({
									 dropdownParent: $('.swal2-container')
								}).on("select2:select", function(e) { 
									if($('#table_ref').val() == 'translate_table_select_option'){
										table_nested_custom_tipe = 1
										$('#table_nested_custom').show();
									}else{
										table_nested_custom_tipe = 0
										$('#table_nested_custom').hide();
									}
								})
								
								$("#table_nested_custom_id").select2({	
									width : '100%',
									dropdownParent: $('.swal2-container'),
									ajax: {
										type: "POST",
										url: "<?php echo base_url('dashboard/select2'); ?>",
										dataType: 'json',
										delay: 250,
										data: function (params) {
											return {
												q: params.term, // search term
												table:'translate_table_select',
												id:'id',
												name:'name',
												page: params.page,
												<?php echo $this->security->get_csrf_token_name(); ?> : csrfHash 
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
									placeholder: 'Pilih Table Field ID'
								}).on("select2:select", function(e) { 
									
								})
								
								$("#table_id_ref").select2({	
									width : '100%',
									dropdownParent: $('.swal2-container'),
									ajax: {
										type: "POST",
										url: "<?php echo base_url('dashboard/getnamingfield'); ?>",
										dataType: 'json',
										delay: 250,
										data: function (params) {
											return {
												q: params.term, // search term
												table:$('#table_ref').val(),
												page: params.page,
												<?php echo $this->security->get_csrf_token_name(); ?> : csrfHash 
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
									placeholder: 'Pilih Table Field ID'
								}).on("select2:select", function(e) { 
									
								})
								
								$("#table_name_ref").select2({	
									width : '100%',
									dropdownParent: $('.swal2-container'),
									ajax: {
										type: "POST",
										url: "<?php echo base_url('dashboard/getnamingfield'); ?>",
										dataType: 'json',
										delay: 250,
										data: function (params) {
											return {
												q: params.term, // search term
												table:$('#table_ref').val(),
												page: params.page,
												<?php echo $this->security->get_csrf_token_name(); ?> : csrfHash 
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
									placeholder: 'Pilih Table Field Name'
								}).on("select2:select", function(e) { 
									
								})
								
								$("#nested_ref").select2({	
									width : '100%',
									dropdownParent: $('.swal2-container'),
									ajax: {
										type: "POST",
										url: "<?php echo base_url('dashboard/getnamingfield'); ?>",
										dataType: 'json',
										delay: 250,
										data: function (params) {
											return {
												q: params.term, // search term
												table:$('#table_ref').val(),
												page: params.page,
												<?php echo $this->security->get_csrf_token_name(); ?> : csrfHash 
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
									placeholder: 'Pilih Table Field Name'
								}).on("select2:select", function(e) { 
									
								})
								

							}
						}).then((result) => {

							console.log(result)
							if (result.isConfirmed) {
								var value = result.value.value
								var tipe = result.value.tipe
								var requiredd = result.value.requiredd
								var table_ref = result.value.table_ref
								var table_id_ref = result.value.table_id_ref
								var table_name_ref = result.value.table_name_ref
								var only = result.value.only
								var size = result.value.size
								var nested = result.value.meta_nested
								var nested_field_id = result.value.meta_nested_field_id
								var nested_ref_id = result.value.meta_nested_ref_id
								var meta_nested_field_custom_id = result.value.meta_nested_field_custom_id
								
								var save = saveKonfirmasinaming(table_change, table_change_id, value,tipe,table_ref,table_id_ref,table_name_ref,only,size,nested,nested_field_id,nested_ref_id,meta_nested_field_custom_id,requiredd);
								
							  }
									
						})
			
										
							}else{
								
								Swal.fire({
									icon: 'error',
									title: 'Kesalahan...',
									text: 'Ada sesuatu yang salah!',
								})
								
							}
						}
				})
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

									//loadingclose();
								});
				
					
		}

		function saveKonfirmasinaming(table_change, table_change_id, value,tipe,table_ref,table_id_ref,table_name_ref,only,size,nested,nested_field_id,nested_ref_id,meta_nested_field_custom_id,requiredd){
			
			if(value == '' || value == null){
				Swal.fire({
					icon: 'error',
					title: 'Oops...',
					text: 'Isi naming',
					footer: ''
				})
			}else if(requiredd == '' || requiredd == null){
				Swal.fire({
					icon: 'error',
					title: 'Oops...',
					text: 'Isi Required',
					footer: ''
				})
			}else if(tipe == '' || tipe == null){
				Swal.fire({
					icon: 'error',
					title: 'Oops...',
					text: 'Isi Tipe',
					footer: ''
				})
			}else if(table_change == '' || table_change == null){
				Swal.fire({
					icon: 'error',
					title: 'Oops...',
					text: 'Isi Tabel',
					footer: ''
				})
			}else if(table_change_id == '' || table_change_id == null){
				Swal.fire({
					icon: 'error',
					title: 'Oops...',
					text: 'Isi Table ID',
					footer: ''
				})
			}else if(only == '' || only == null){
				Swal.fire({
					icon: 'error',
					title: 'Oops...',
					text: 'Isi TIPE ID',
					footer: ''
				})
			}else{
			
				$.post('<?php echo base_url('dashboard/updatenaming'); ?>',{
						size : size,
						value : value,
						required : requiredd,
						tipe : tipe,
						only : only,
						table_ref : table_ref,
						table_id_ref : table_id_ref,
						table_name_ref : table_name_ref,
						table_change : table_change,
						table_change_id :table_change_id,
						nested : nested,
						nested_field_id : nested_field_id,
						nested_ref_id : nested_ref_id,
						meta_nested_field_custom_id : meta_nested_field_custom_id,
						<?php echo $this->security->get_csrf_token_name(); ?> : csrfHash 
				},function (data) {	
						if(data != 'null'){
							var obj = jQuery.parseJSON(data);
							updateCsrfToken(obj.csrfHash); // Perbarui token CSRF
							if(obj.status == 'success'){
								
								let timerInterval
								Swal.fire({
								title: 'Saving Data',
									html: 'Loading <b></b> milliseconds.',
									timer: 100,
									timerProgressBar: true,
									didOpen: () => {
										Swal.showLoading()
										const b = Swal.getHtmlContainer().querySelector('b')
										timerInterval = setInterval(() => {
											b.textContent = Swal.getTimerLeft()
										}, 100)
									},
									willClose: () => {
										clearInterval(timerInterval)
									}
								}).then((result) => {
									/* Read more about handling dismissals below */
									if (result.dismiss === Swal.DismissReason.timer) {
										
										location.reload();

										
										//window.location.href = '<?php echo base_url('data_proposal/editdata/')?>'+obj.slug;
									}
								})
										
							}else{
								
								Swal.fire({
									icon: 'error',
									title: 'Kesalahan...',
									text: 'Ada sesuatu yang salah!',
								})
								
							}
						}
				}).fail(function(jqxhr, status, error) {
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

									//loadingclose();
								});
			
			}
		}


		function savingTableView(modulview, tabelview){
			
			var array = []
			var checkboxes = document.getElementsByName("checkbox_table[]");

			for (var i = 0; i < checkboxes.length; i++) {
				if ( checkboxes[i].checked ) {
					array.push(checkboxes[i].value)
				}
			}
			
			if(modulview == '' || modulview == null){
				Swal.fire({
					icon: 'error',
					title: 'Oops...',
					text: 'Isi modulview',
					footer: ''
				})
			}else if(tabelview == '' || tabelview == null){
				Swal.fire({
					icon: 'error',
					title: 'Oops...',
					text: 'Isi tabelview',
					footer: ''
				})
			}else{
			
				$.post('<?php echo base_url('dashboard/updateview'); ?>',{
						modulview : modulview,
						tabelview : tabelview,
						dataview :array,
						<?php echo $this->security->get_csrf_token_name(); ?> : csrfHash 
				},function (data) {	
						if(data != 'null'){
							var obj = jQuery.parseJSON(data);
							updateCsrfToken(obj.csrfHash); // Perbarui token CSRF
							if(obj.status == 'success'){
								
								let timerInterval
								Swal.fire({
								title: 'Saving Data',
									html: 'Loading <b></b> milliseconds.',
									timer: 100,
									timerProgressBar: true,
									didOpen: () => {
										Swal.showLoading()
										const b = Swal.getHtmlContainer().querySelector('b')
										timerInterval = setInterval(() => {
											b.textContent = Swal.getTimerLeft()
										}, 100)
									},
									willClose: () => {
										clearInterval(timerInterval)
									}
								}).then((result) => {
									/* Read more about handling dismissals below */
									if (result.dismiss === Swal.DismissReason.timer) {
										
										location.reload();

										
										//window.location.href = '<?php echo base_url('data_proposal/editdata/')?>'+obj.slug;
									}
								})
										
							}else{
								
								Swal.fire({
									icon: 'error',
									title: 'Kesalahan...',
									text: 'Ada sesuatu yang salah!',
								})
								
							}
						}
				})
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

									//loadingclose();
								});
			
			}
		}
		
		function savingTableViewOrder(modulview, tabelview, array, tableorder){
			
			if(modulview == '' || modulview == null){
				Swal.fire({
					icon: 'error',
					title: 'Oops...',
					text: 'Isi modulview',
					footer: ''
				})
			}else if(tabelview == '' || tabelview == null){
				Swal.fire({
					icon: 'error',
					title: 'Oops...',
					text: 'Isi tabelview',
					footer: ''
				})
			}else if(tableorder == '' || tableorder == null){
				Swal.fire({
					icon: 'error',
					title: 'Oops...',
					text: 'Isi tableorder',
					footer: ''
				})
			}else{
			
				$.post('<?php echo base_url('dashboard/updatevieworder'); ?>',{
						modulview : modulview,
						tabelview : tabelview,
						tableorder : tableorder,
						dataview :array,
						<?php echo $this->security->get_csrf_token_name(); ?> : csrfHash 
				},function (data) {	
						if(data != 'null'){
							var obj = jQuery.parseJSON(data);
							updateCsrfToken(obj.csrf_hash); // Perbarui token CSRF
							if(obj.status == 'success'){
								
								let timerInterval
								Swal.fire({
								title: 'Saving Data',
									html: 'Loading <b></b> milliseconds.',
									timer: 100,
									timerProgressBar: true,
									didOpen: () => {
										Swal.showLoading()
										const b = Swal.getHtmlContainer().querySelector('b')
										timerInterval = setInterval(() => {
											b.textContent = Swal.getTimerLeft()
										}, 100)
									},
									willClose: () => {
										clearInterval(timerInterval)
									}
								}).then((result) => {
									/* Read more about handling dismissals below */
									if (result.dismiss === Swal.DismissReason.timer) {
										
										//location.reload();

										
										//window.location.href = '<?php echo base_url('data_proposal/editdata/')?>'+obj.slug;
									}
								})
										
							}else{
								
								Swal.fire({
									icon: 'error',
									title: 'Kesalahan...',
									text: 'Ada sesuatu yang salah!',
								})
								
							}
						}
				}).fail(function(jqxhr, status, error) {
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

									//loadingclose();
								});
			
			}
		}
		
		function savingTableViewOrderForm(modulview, tabelview, array, tableorder){
			
			if(modulview == '' || modulview == null){
				Swal.fire({
					icon: 'error',
					title: 'Oops...',
					text: 'Isi modulview',
					footer: ''
				})
			}else if(tabelview == '' || tabelview == null){
				Swal.fire({
					icon: 'error',
					title: 'Oops...',
					text: 'Isi tabelview',
					footer: ''
				})
			}else if(tableorder == '' || tableorder == null){
				Swal.fire({
					icon: 'error',
					title: 'Oops...',
					text: 'Isi tableorder',
					footer: ''
				})
			}else{
			
				$.post('<?php echo base_url('dashboard/updatevieworderform'); ?>',{
						modulview : modulview,
						tabelview : tabelview,
						tableorder : tableorder,
						dataview :array,
						<?php echo $this->security->get_csrf_token_name(); ?> : csrfHash 
				},function (data) {	
						if(data != 'null'){
							var obj = jQuery.parseJSON(data);
							//csrfHash = obj.csrf_hash;
							updateCsrfToken(obj.csrf_hash); // Perbarui token CSRF
							if(obj.status == 'success'){
								
								let timerInterval
								Swal.fire({
								title: 'Saving Data',
									html: 'Loading <b></b> milliseconds.',
									timer: 100,
									timerProgressBar: true,
									didOpen: () => {
										Swal.showLoading()
										const b = Swal.getHtmlContainer().querySelector('b')
										timerInterval = setInterval(() => {
											b.textContent = Swal.getTimerLeft()
										}, 100)
									},
									willClose: () => {
										clearInterval(timerInterval)
									}
								}).then((result) => {
									/* Read more about handling dismissals below */
									if (result.dismiss === Swal.DismissReason.timer) {
										
										//location.reload();

										
										//window.location.href = '<?php echo base_url('data_proposal/editdata/')?>'+obj.slug;
									}
								})
										
							}else{
								
								Swal.fire({
									icon: 'error',
									title: 'Kesalahan...',
									text: 'Ada sesuatu yang salah!',
								})
								
							}
						}
				}).fail(function(jqxhr, status, error) {
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

									//loadingclose();
								});
			
			}
		}
		
		
	</script>