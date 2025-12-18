<div class="col-md-12" style="display:none">
		<div class="card-group">
								<div class="card">
									<div class="card-header">
										
									</div>
									<div class="card-body card-boxnya">
									
										
													
										<div class="table-responsive table-custom " id="example-datatable100">
										
											<div class="col-lg-12" id="Awareness">
													
													<div class="row">
														<!-- Daily Rate -->
														<div class="col-lg-6">
															<div class="dashboard-card" id="awarewebcolor">
																<h4 style="color: inherit;">Visitor Web</h4>
																<h2 style="color: inherit; padding-top: 0px;"><span style="font-size: inherit;" id="awareweb">0%</span></h2>
															
															</div>
														</div>
														
														<!-- Activity Rate -->
														<div class="col-lg-6">
															<div class="dashboard-card" id="utilwebcolor">
																<h4 style="color: inherit;">Visitor Mobile</h4>
																<h2 style="color: inherit;padding-top: 0px;"><span style="font-size: inherit;"  id="utilweb">0%</span></h2>
																
															</div>
														</div>
													</div>
												
												
										</div>
										
											<div id="chart-containerall-popup" class="grafix-isi" style="height:300px">
														<div class="mengunduh">
																	 <div class="loading loading07">
																		<span data-text="L">L</span>
																		<span data-text="O">O</span>
																		<span data-text="A">A</span>
																		<span data-text="D">D</span>
																		<span data-text="I">I</span>
																		<span data-text="N">N</span>
																		<span data-text="G">G</span>
																	  </div>
																</div>
													</div>
													
											<table class="table table-striped " id="datatablecapex" style="    width: 100% !important;">
												<thead>
													<tr id="tableheaddercapex">
														<th>No</th>
													</tr>
												</thead>
											</table>
										</div>
									</div>
								</div>
							</div>
</div>


<script type="text/javascript">

function convertToArrayOfObjects(data) {
    var keys = data.shift(),
        i = 0, k = 0,
        obj = null,
        output = [];

    for (i = 0; i < data.length; i++) {
        obj = {};

        for (k = 0; k < keys.length; k++) {
            obj[keys[k]] = data[i][k];
        }

        output.push(obj);
    }

    return output;
}

						var table100 = null;
						function viewPopData(type, filter, namenya = null){
							
							var container = $('#example-datatable100').clone();
							container.find('#datatablecapex').attr('id', 'datatablecapex_new');
							container.find('#tableheaddercapex').attr('id', 'tableheaddercapex_new');
							container.find('#chart-containerall-popup').attr('id', 'chart-containerall-popup_new');
							
							container.find('#Awareness').attr('id', 'Awareness_new');
							container.find('#awareweb').attr('id', 'awareweb_new');
							container.find('#utilweb').attr('id', 'utilweb_new');
							container.find('#awarewebcolor').attr('id', 'awarewebcolor_new');
							container.find('#utilwebcolor').attr('id', 'utilwebcolor_new');
							
							filter['type'] = namenya;
							var range = filter['range']
							var regional = filter['regional']
							var area = filter['area']
							var vendor = filter['vendor']
							var enginner = filter['enginner']
							//var namenya = filter['namenya']
							var data_array = [];
								
		
							box4 = bootbox.dialog({
								size: "large",
								show: true,
								backdrop: true,
								message: container.html(),
								title: "Data"
							});
							
											  
							box4.on("shown.bs.modal", function() {
								
							
								getData(type, filter)
								
								if(type == 106){
									$.post('<?php echo base_url('dasbor/grafik'); ?>',{
										type_id : 111,
										type : namenya,
										range : range, 
										regional : regional, 
										area : area, 
										vendor : vendor,
										enginner: enginner,
										<?php echo $this->security->get_csrf_token_name(); ?> : csrfHash ,
									}, function (data) {
								
										obj = JSON.parse(data);
										updateCsrfToken(obj.csrf_hash)
										if(obj.message == "success"){ 
											
											if(namenya == 'Utilization'){
												$('#Awareness_new').hide();
											}else{
												$('#Awareness_new').show();
												$('#awareweb_new').html(obj.awareweb[1]);
												$('#utilweb_new').html(obj.awareweb[2]);
												$('#awarewebcolor_new').css('background-color', obj.awarewebcolor[1]);
												$('#awarewebcolor_new').css('color', obj.awarewebfont[1]);
												$('#utilwebcolor_new').css('background-color', obj.awarewebcolor[2]);
												$('#utilwebcolor_new').css('color', obj.awarewebfont[2]);
											}
											
					
											if(namenya == 'Utilization'){
												var caption = "Utilization";
											}else{
												var caption = "Visitor User";
											}
											
											var subCaption = "Number of";
											var type_id_nya = obj.type_id
											data_array[type_id_nya] = [];
											data_array[type_id_nya]["titleChart"]  = null
											data_array[type_id_nya]["subtitleChart"]  = null
											data_array[type_id_nya]["objData"]  = null
											data_array[type_id_nya]["idData"]  = null
											data_array[type_id_nya]["categoryLabel"]  = null
											data_array[type_id_nya]["datasetName"]  = null
											data_array[type_id_nya]["myChart"]  = null
											data_array[type_id_nya]["popclik"]  = null
											data_array[type_id_nya]["filter"]  = null	
														
											var myChart = new FusionCharts({
												type: "msspline",
												renderAt: "chart-containerall-popup_new",
												width: "100%",
												height: "100%",
												dataFormat: "json",
												containerBackgroundOpacity: '0',
												dataSource: {
													chart: {
														"chartLeftMargin": "20",
														"chartTopMargin": "0",
														"chartRightMargin": "0",
														"chartBottomMargin": "0",
														captionPosition: "left",
														xAxisValueBgColor: '#ffffff',
														xAxisValueBgAlpha: 0,
														"caption": caption,
														"captionFontColor": "#93c6e7",
														"subCaption": subCaption,
														"subcaptionFontSize": subCaption,
														"captionFont": "Arial",
														'canvasBgAlpha':0,
														"bgColor": "#DDDDDD",
														"bgAlpha": "0",
														// "bgImage": "http://upload.wikimedia.org/wikipedia/commons/7/79/Misc_fruit.jpg",
														//Background image transparency 
														//"bgImageAlpha": "25",
														//"bgImageDisplayMode": "stretch",
														"captionFontSize": "14",
														plottooltext: "<b>$displayValue</b>",
														"showValues": "0",
														"showlegend": "1",
														"showpercentvalues": "1",
														"legendposition": "bottom",
														"usedataplotcolorforlabels": "1",
														"theme": "fusion",
														"showLabels": "1",
														"animateClockwise": "1",
														"pieRadius": "70",
														"placeValuesInside": "0",
														"numberScaleValue": "1000,1000,1000",
														"numberScaleUnit": " rb, jt, M",
														"decimalSeparator": ",",
														"thousandSeparator": ".",
														valueFontColor : "#666666",
														"valueFontSize" : "11",
														legendItemFontSize:"8",
														 labelFontSize:"8",
														 "baseFontSize": "8",
														"labelDisplay": "rotate",
														"slantLabel": "1"
													},
													categories: [
															{
															  category: obj.data
															}
														 ],
														 dataset: obj.data5
												}
											}).render();
												  
										}
									})
								}else{
									$('#Awareness_new').hide();
									$('#chart-containerall-popup_new').hide();
									
								}
								
								
							})
							
						}
						
						var loading = null;
						function getData(type, filter = null){
								
								var range = filter['range']
								var regional = filter['regional']
								var area = filter['area']
								var vendor = filter['vendor']
								var enginner = filter['enginner']
								var type_nya = filter['type']
	
								if(table100 != null){
									table100.destroy();
									table100 = new $('#datatablecapex').DataTable();
									table100.destroy();
									$('#datatablecapex_new').empty();
									
									//$('#datatablecapex_new').dataTable().fnClearTable();
									//$('#datatablecapex_new').dataTable().fnDestroy();
									
								}else{
									//table = new $('#datatablecapex').DataTable();
									//table.destroy();
								}
								
								
								
								$.post('<?php echo base_url($headurl.'/getColumn'); ?>',
								{ 
									id : type,
									range : range, 
									regional : regional, 
									area : area, 
									vendor : vendor,
									enginner: enginner,
									type: type_nya,
									<?php echo $this->security->get_csrf_token_name(); ?> : csrfHash 
								}, 
								function (data) {
								////console.log(data); 
									var obj = JSON.parse(data);
									updateCsrfToken(obj.csrf_hash)
									datacolumn = obj.column;
									$('#tableheaddercapex_new').empty();
									$('#tableheaddercapex_new .addth').remove();
									$('#tableheaddercapex_new tbody').remove();
									
									if(obj.status == 'success'){
										setTimeout(function(){
											jQuery.each(datacolumn, function(i) {
												$('#tableheaddercapex_new').append('<th class="addth"></th>');
											})
										}, 500);
										
										
									$.fn.dataTable.ext.errMode = 'none';

									table100 = new $('#datatablecapex_new').DataTable({
															destroy: true,
															retrieve: true,
															"rowCallback": function( row, data ) {
																var key = 0;
																for (const theArray in data) {
																	if(key == 0){
																		$('td:eq('+ key +')', row).addClass('dtkenolbtn');
																	} if(key == 1){
																		$('td:eq('+ key +')', row).addClass('dtkenol');

																		break
																	}
																	key = key +1
																}

															},
															"initComplete": function(settings, json) {
																
															},
															"preDrawCallback": function( settings ) {
			
																
															},
															"drawCallback": function( settings ) {
				
															},		
															"responsive": false,
															"scrollY":false,
															"scrollX":true,
															"scrollCollapse":true,
															"bPaginate": true,
															"bSort": true, 
															"bInfo": false,
															"bFilter": true,
															"dom"	: '<"row"<"col-md-6 text-left"l><"col-md-3 text-right"f><"col-md-3 text-right"B>>rt<"row"<"col-md-6"i><"col-md-6"p>>',
															"buttons" : [ 'copy', 
															{
																extend: 'excel',
																action: newExportAction
															}],
															"oLanguage" : {
																"sProcessing": "<div class='load1 load-wrapper'><div class='loader'>Getting Data ...</div></div>",
																 "oPaginate" : {
																	"sFirst": "<<",
																	"sPrevious": "<",
																	"sNext": ">", 
																	"sLast": ">>" 
																},
																"sSearch": '<i class="fa fa-search"></i>',
																"sSearchPlaceholder": 'Cari ...'
															},
															"fixedColumns":   {
																leftColumns: 2,
															},
															"sPaginationType": "full_numbers",
															"lengthMenu": [[5, 10, 25, 50, 100, 500, 1000,-1], [5, 10, 25, 50, 100, 500, 1000,'All']],
															"processing": true,
															"serverSide": true,
															"order": [],
															"ajax": {
																"url": "<?php echo base_url($headurl.'/getColumnDetail'); ?>",
																"type": "POST",
																"data": function ( d ) {
																	d.id = type;
																	d.range = range; 
																	d.regional = regional; 
																	d.area = area;
																	d.vendor = vendor;
																	d.enginner= enginner;
																	d.type= type_nya;
																	d.<?= $this->security->get_csrf_token_name(); ?> = csrfHash 
																},
																dataSrc: function(json) {
																	console.log(json); // <-- cek isinya
																	if (json.csrf_hash) {
																		updateCsrfToken(json.csrf_hash)
																	}
																	return json.data;
																}
															},
															"columns" : datacolumn,
															"columnDefs": [
																{  
																	"targets": 0,
																	"width": "10px",
																	"orderable": false 
																},
																{  
																	"targets": '_all',
																	"width": "10px",
																	"orderable": false 
																}
															 ],
										})		
										
									
									
									}
									
								//table.buttons().remove();
								})
							
							
							
													
								oldExportAction = function (self, e, dt, button, config) {
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

								newExportAction = function (e, dt, button, config) {
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
													
							}	
						
</script>