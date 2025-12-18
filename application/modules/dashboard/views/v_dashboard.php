<style>
	.container-fluid {
		background: #fff !important;
		padding: 0px 15px !important;
	}
	
	.card-group .card {
		border-right: none;
	}
	
	.modal-lg {
		width: 100%;
		//margin: 0;
		max-width: 90% !important;
	}
	

	
	.act-btn{
      background:#f66d44;
      display: block;
      width: 50px;
      height: 50px;
      line-height: 50px;
      text-align: center;
      color: white;
      font-size: 30px;
      font-weight: bold;
      border-radius: 50%;
      -webkit-border-radius: 50%;
      text-decoration: none;
      transition: ease all 0.3s;
      position: fixed;
      left: 30px;
      bottom:30px;
    }
	.act-btn:hover{background: #a5c1dc}

</style>
					
<div id="kt_content_container" class="d-flex flex-column-fluid align-items-start container-xxl">
	<div class="content flex-row-fluid" id="kt_content">
		<div class="">
			<div class="col-sm-12" style="padding: 0px;">
				<div class="card-body" style="padding: 5px;">
				
					<div role="tabpanel" class="tab-pane fade in active show" id="performace">


						<?php 
						
						if($this->session->userdata('group_id') == 3){
							include('v_dashboard_user.php'); 
						}else{
							include('v_dashboard_performance.php'); 
						}
			
						?>
					 </div>
							
					
					<script type="text/javascript">
					
						var table;
						var type = 1;
						
						var tahun = $("#filter_tahun").val();
						var project_tipe = $("#filter_tipe").val();

						$(document).ready(function() {
							
							
								$("#filter_button").click(function(){
									tahun = $("#filter_tahun").val();
									project_tipe = $("#filter_tipe").val();
									getChart(project_tipe, tahun)
								});
								
								$("#filter_tahun").select2({
								//dropdownParent: $('#swal2-html-container'),
									width: '100%',		
									placeholder: 'Pilih Tahun'
								}).on("select2:select", function(e) { 
									tahun = $("#filter_tahun").val();
									project_tipe = $("#filter_tipe").val();
									getChart(project_tipe, tahun)
								}).on("select2:unselect", function(e) { 
									tahun = $("#filter_tahun").val();
									project_tipe = $("#filter_tipe").val();
									getChart(project_tipe, tahun)
								})
								
								$("#filter_tipe").select2({
								//dropdownParent: $('#swal2-html-container'),
									width: '100%',		
									placeholder: 'Pilih Tipe'
								}).on("select2:select", function(e) { 
									tahun = $("#filter_tahun").val();
									project_tipe = $("#filter_tipe").val();
									getChart(project_tipe, tahun)
								}).on("select2:unselect", function(e) { 
									tahun = $("#filter_tahun").val();
									project_tipe = $("#filter_tipe").val();
									getChart(project_tipe, tahun)
								})
								
						
							<?php
							if (isset($_GET['message'])) {
								
								$this->db->select('data_absensi.*, date(tanggal) as datenya');
								$this->db->where('user_id', $this->session->userdata('userid'));
								$this->db->where('type', 'Website');
								$this->db->order_by('date(tanggal)','DESC');
								$this->db->limit(1);
								$queryabsen = $this->db->get('data_absensi');
								$queryabsen = $queryabsen->result_object();
								if($queryabsen){
									$last_absen = $queryabsen[0]->datenya;
								}else{
									$date = date('Y-m-d');
									$date = strtotime($date);
									$date = strtotime("-1 day", $date);
									$last_absen = date('Y-m-d', $date);
								}
							
								if ($_GET['message'] == 'success' && $last_absen != date('Y-m-d')) {
									?>
									
									
									<?php 
									$querynotif = false;
									if($querynotif){ 
										$messagenotif = '<div style="font-size:14px"><br>Ada mendapat pesan baru <br><a style="color:red" href="'.base_url('data_inbox').'"><i class="ti-eye"></i> Lihat Pesan </a></div>';
										$messagenotificon = '<div style="width: 40px;position: absolute;right: 50px;top: 25px;"><a href="'.base_url('data_inbox').'"><i class="ti-email"></i><div class="notify" style="top: -25px;right: -3px;"> <span class="heartbit"></span> <span class="point"></span> </div></div>';
									}else{
										$messagenotif = '';
										$messagenotificon = '';
									}?>
									
									<?php if($this->session->userdata("position_name") != null && $this->session->userdata("position_name") != ''){
								
										$position_name = $this->session->userdata("position_name");
										
									}else{
										
										$position_name =  $this->ortyd->select2_getname($this->session->userdata("group_id"),"users_groups","id","name"); 
									}
									
									?>
									
									Swal.fire({
									  title: 'Selamat Datang Kembali <br> <?php echo $this->session->userdata("fullname").' '.$messagenotificon; ?>',
									  text: '',
									  html:
										'Anda login sebagai <?php echo $position_name.' '.$messagenotif; ?>',
									  imageUrl: '<?php echo base_url('logo.jpg'); ?>',
									  imageWidth: 160,
									  imageHeight: 50,
									  allowOutsideClick: false,
									  imageAlt: '<?php echo title;?>',
									  confirmButtonText:'<i class="ti ti-angle-double-right"></i> Memulai Aplikasi Sekarang',
									})

									<?php
								}
								elseif($_GET['message'] == 'noaccess') {
									?>
									Swal.fire({
									   icon: "error",
									  title: 'Access Denied',
									  text: 'Anda Tidak Punya Akses ke Halaman tersebut',
									  allowOutsideClick: false,
									  imageAlt: '<?php echo title;?>',
									  confirmButtonText:'<i class="ti ti-angle-double-right"></i> Oke',
									})

									<?php
								}
							} ?>
							
							
							setTimeout(function(){
								getChart(project_tipe, tahun)
							}, 1000);
							
							
						})
						
						
						
						function getChart(project_tipe, tahun){
							
							<?php 
						
						if($this->session->userdata('group_id') == 3){ ?>
							get_dashboard_performance(project_tipe, tahun);
						<?php }else{ ?>
							get_dashboard_performance(project_tipe, tahun);
						<?php }
			
						?>
							
							
							
						}
						</script>
										
				</div>
			</div>
		</div>
	</div>
</div>

<a href="#" id="id-menunya" class="act-btn" style="display:none">
  +
</a>

<?php $this->load->view('dashboard_popup'); ?>




<div class="col-xl-12" style="display:none">
  
	<div  id="box1table">
    <p class="text-muted mb-4 font-14" id="header-sub-title" style="display:none"></p>

                                <!-- Nav tabs -->
        <ul class="nav nav-pills" role="tablist">
              <li class="nav-item waves-effect waves-light">
                    <a class="nav-link active" data-toggle="tab" href="#home-11" id="viewdatadetail" role="tab">Overview</a>
              </li>
			   <li class="nav-item waves-effect waves-light">
                      <a class="nav-link" data-toggle="tab" href="#home-33" role="tab" id="viewdatatablegr">Grafik Level 2</a>
              </li>
              <li class="nav-item waves-effect waves-light">
                      <a class="nav-link" data-toggle="tab" href="#home-22" role="tab" id="viewdatatable">Data</a>
              </li>
                                   
        </ul>
                                <div class="tab-content">
                                    <div class="tab-pane active " id="home-1" role="tabpanel">
                                      <div class="container-fluid" style="padding-left:0 !important;padding-right:0 !important">
                                         <div class="card" style="margin:0">
                                           <div class="card-body"  style="padding:0">
                                               <div class="row">
											   
											     
												
                                                  <div class="col-xl-12">
                                                       <div id="<?php echo 'chartsfront'; ?>" style="display: block;"></div>
                                                  </div>
                                           
                                                   
                                                </div>
                                              </div>
                                            </div>  
                                          </div>
                                        </div>
									<div class="tab-pane p-3" id="home-3" role="tabpanel">
										<div class="card" style="margin:0">
                                           <div class="card-body"  style="padding:0">
                                               <div class="row">
												 <div class="col-lg-12">
													<div class="table-responsive" style="padding-bottom:20px;" id="chartdetail"></div>
												</div>
												</div>
											</div>
										</div>
									</div>
                                    <div class="tab-pane p-3" id="home-2" role="tabpanel">
                                    <div class="container-fluid" style="padding-left:0 !important;padding-right:0 !important">
                                         <div class="card" style="margin:0">
                                           <div class="card-body"  style="padding:0">
                                               <div class="row">
												 <div class="col-lg-6" id="filter_category">
													<div class="card-body" style="padding:0">
														<label>Category </label>
														<select class=" form-control" id="datasource_categories" tabindex="-1" aria-hidden="true">
															
														</select>
													</div>
													
												</div>
												<div class="col-lg-6" id="filter_dataset">
													<div class="card-body" style="padding:0">
														<label>Dataset </label>
														<select class=" form-control" id="datasource_dataset" tabindex="-1" aria-hidden="true">
															
														</select>
													</div>
													
												</div>
                                                 <div class="col-xl-12" id="headernya" style="    display: block;
    width: 100%;
    overflow-x: auto;">
														
														<table class="table " id="datatablett" style="width:initial !important">
															<thead>
																<tr id="tableheadder">
																	<th>Loading ........</th>
																</tr>
															</thead>
														</table>
                                                </div>
                                              </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                </div>
		
	</div>
</div>
</div>

<script>


let height = window.innerHeight;
let heighnya = height - 110;
let heightbag2 = heighnya/2;

let heighnya2 = height - 60;
let heightbag3 = heighnya2/3;

let heighnya1 = height - 60;
let heightbag1 = heighnya1/1.5;
let atas = 30;
let bawah = 60;



							<?php 
						
						if($this->session->userdata('group_id') == 3){ ?>
							
						<?php }else{ ?>
							//document.getElementById("chart-containerformance").style.height = heightbag2 + atas + "px";
//document.getElementById("chart-containerformance-4").style.height = heightbag2 + atas + "px";
//document.getElementById("chart-containerformance-5").style.height = heightbag2 + atas + "px";
//document.getElementById("chart-containerformance-7").style.height = heightbag2 + atas + "px";

//document.getElementById("chart-containerformance-1").style.height = heightbag2 - bawah + "px";
//document.getElementById("chart-containerformance-2").style.height = heightbag2 - bawah + "px";
						<?php }
									
						?>


$('#header-color').hide();
$('#header-menu').show();

var ismenunya = 0;

$( document ).ready(function() {

	
});


	
var objData = null;
var idData = null;
var pop = 1;
var box1;
var titleChart = '';
var subtitleChart = '';
function popupbox1(titleChartShow,subtitleChartShow,myChart,popclik,objDataShow,idDataShow,categoryLabel,datasetName){
	
	console.log(objDataShow);
	
	if(table == null || table == ''){
		table = new $('#newtables').DataTable();
	}
	
	
	if(popclik == 0){
		
	}else{
		pop = 0;
		var container = $('#box1table').clone();
		container.find('#chartsfront').attr('id', 'chartsfront2');
		container.find('#header-sub-title').attr('id', 'header-sub-title2');
		container.find('#home-1').attr('id', 'home-11');
		container.find('#home-2').attr('id', 'home-22');
		container.find('#home-3').attr('id', 'home-33');
		container.find('#headernya').attr('id', 'headernya_pop');
		container.find('#viewdatadetail').attr('id', 'viewdatadetailpop');
		container.find('#viewdatatable').attr('id', 'viewdatatablepop');
		container.find('#viewdatatablegr').attr('id', 'viewdatatablegrpop');
		container.find('#datatablett').attr('id', 'newtables');
		
		$("#newtables_wrapper").css('display','block !important');
		$("#newtables_wrapper").css('width','100% !important');
		$("#newtables_wrapper").css('overflow-x','auto !important');
		
		container.find('#tableheadder').attr('id', 'newtableheadder');
		container.find('#totalnilai').attr('id', 'totalnilaidata');
		container.find('#datasource_categories').attr('id', 'datasource_categories_popup');
		container.find('#datasource_dataset').attr('id', 'datasource_dataset_popup');
		container.find('#chartdetail').attr('id', 'chartdetail_popup');
		container.find('#filter_category').attr('id', 'filter_category_popup');
		container.find('#filter_dataset').attr('id', 'filter_dataset_popup');
		container.find('#display_target').attr('id', 'display_target_pop_data');
		container.find('#target_pop').attr('id', 'target_pop_data');
		container.find('#ach_pop').attr('id', 'ach_pop_data');
		container.find('#kpi_pop').attr('id', 'kpi_pop_data');
		container.find('#growth_pop').attr('id', 'growth_pop_pop');
		
		var cloned_chart;
		cloned_chart = myChart.clone({width: "100%",height: "400"});
		
		box1 = bootbox.dialog({
			size: "large",
			show: true,
			backdrop: true,
			message: container.html(),
			title: titleChartShow + ' <span style="font-size:12px">(' + subtitleChartShow + ')</span>',
			subTitle:''
		});
								
    $('#header-sub-title2').html(subtitleChartShow);	
	
	if(objDataShow != ''){
		$('#totalnilaidata').html(objDataShow.total);	
	}
    
		box1.on("shown.bs.modal", function() {
			box1.attr("id", "mybootboxdashboard");
			//console.log(myChart);
			
			////console.log(myChart.args.type); //"pie2d"
			
			$('#datasource_categories_popup').select2({dropdownParent: $('#mybootboxdashboard'),'width':'100%'}).on("select2:select", function(e) { 
				table.destroy();
				getTables(idDataShow,tahun,project_tipe)		
			});
			
			$('#datasource_dataset_popup').select2({dropdownParent: $('#mybootboxdashboard'),'width':'100%'}).on("select2:select", function(e) { 
				table.destroy();
				getTables(idDataShow,tahun,project_tipe)		
			});
			
			var newOption = new Option('All', 0, true, true);
			$('#datasource_categories_popup').append(newOption).trigger('change');
				
			var newOption = new Option('All', 0, true, true);
			$('#datasource_dataset_popup').append(newOption).trigger('change');
			
			if(myChart.args.type == 'funnel' ||  myChart.args.type == 'doughnut2d' || myChart.args.type == 'pie2d' || myChart.args.type == 'bar2d' || myChart.args.type == 'funnel' || myChart.args.type == 'column2d'){
				if (typeof myChart.args.dataSource.data !== 'undefined') {
					var obj1 = myChart.args.dataSource.data;
					////console.log.log(obj1);
					if(obj1){
						for (var key in obj1) {
							////console.log.log(obj1[key].label);
							var newOption = new Option(obj1[key].label, obj1[key].label, false, false);
							$('#datasource_categories_popup').append(newOption).trigger('change');
						}
					}
				}
			
			}else{
				if (typeof myChart.args.dataSource.categories !== 'undefined') {
					var obj1 = myChart.args.dataSource.categories[0].category;
					////console.log.log(obj1);
					if(obj1){
						for (var key in obj1) {
							////console.log.log(obj1[key].label);
							var newOption = new Option(obj1[key].label, obj1[key].label, false, false);
							$('#datasource_categories_popup').append(newOption).trigger('change');
						}
					}
				}

				
				if (typeof myChart.args.dataSource.dataset !== 'undefined') {
					var obj2 = myChart.args.dataSource.dataset;
					if(obj2){
						for (var key in obj2) {
							var newOption = new Option(obj2[key].seriesname, obj2[key].seriesname, false, false);
							$('#datasource_dataset_popup').append(newOption).trigger('change');
						}
					}
				}
			
			}
			
			var legendint
			if(idDataShow == 2){
				legendint = '0'
			}else{
				legendint = '1'
			}

			var legendPosition
			if(idDataShow == 444){
				legendPosition = "right"
			}else{
				legendPosition = "top-right"
			}
			
			cloned_chart.setChartAttribute({
				"showValues": "1",
				"showLegend": legendint,
				"legendPosition": legendPosition,
				"legendIconScale": "1",
				"showLabels": "1",
				"animation": "1",
				"exportEnabled": "1",
				"showTooltip": "1",
				"showHoverEffect": "1",
				"showYAxisValues": "0",
				"caption": '',
				"subCaption": '',
				"captionalignment":"left",
				"captionFontBold":"1",
				"xAxisName": "",
				"pieRadius": "100",
				"yAxisName": "",
				"showXAxisLine": "0",
				"showYAxisLine": "0",
				"numDivLines": "0",
				"enableSlicing": "0",
				"enableRotation": "0",
				"labelFontSize":"14",
				"legendItemFontSize":"14",
				"valueFontSize" : "12",
			  });
			  
			
			 
		
			 if(idDataShow == 444){
				$('#home-11').hide();
				$('#home-22').hide();
				$('#home-33').show();
				
				$("#viewdatatablegrpop" ).addClass("active");
				$("#viewdatadetailpop" ).removeClass("active");
				$("#viewdatatablepop" ).removeClass("active");
				
				getGrafikLevel2(idDataShow,tahun,project_tipe)
			 }else{
				 
				 $("#viewdatatablegrpop" ).hide();
				 if(categoryLabel != ''){
					 console.log(myChart.args.type)
				  if(myChart.args.type == 'funnel' || myChart.args.type == 'doughnut2d' || myChart.args.type == 'pie2d' || myChart.args.type == 'bar2d' || myChart.args.type == 'funnel'){
					  if(idDataShow == 444){
						  $("#datasource_categories_popup").val(0).trigger('change');
					  }else{
						  if (typeof myChart.args.dataSource.data !== 'undefined') {
							$("#datasource_categories_popup").val(categoryLabel).trigger('change');
						  }
					  }
						
					}else{
						if (typeof myChart.args.dataSource.categories !== 'undefined') {
							$("#datasource_categories_popup").val(categoryLabel).trigger('change');
						}
						
						
						if (typeof myChart.args.dataSource.dataset !== 'undefined') {
							$("#datasource_dataset_popup").val(datasetName).trigger('change');
						}
					}
					
					
				  
					$('#home-11').hide();
					$('#home-22').show();
					$('#home-33').hide();
					$("#viewdatatablepop" ).addClass("active");
					$("#viewdatadetailpop" ).removeClass("active");
					$("#viewdatatablegrpop" ).removeClass("active");
					
					if(idDataShow == 444){
						//$('#filter_category_popup').hide();
						//$('#filter_dataset_popup').hide();
				    }
			   
					table.destroy();
					getTables(idDataShow,tahun,project_tipe)
					
				}
			 }
			  
			  
			  cloned_chart.addEventListener('dataPlotClick', function(eventObj, dataObj) {
				    //console.log.log(dataObj)
					
					
					if(myChart.args.type == 'funnel' || myChart.args.type == 'doughnut2d' ||  myChart.args.type == 'pie2d' || myChart.args.type == 'bar2d' || myChart.args.type == 'funnel'){
						if (typeof myChart.args.dataSource.data !== 'undefined') {
							$("#datasource_categories_popup").val(dataObj.categoryLabel).trigger('change');
						}
					}else{
						if (typeof myChart.args.dataSource.categories !== 'undefined') {
							$("#datasource_categories_popup").val(dataObj.categoryLabel).trigger('change');
						}
						
						
						if (typeof myChart.args.dataSource.dataset !== 'undefined') {
							$("#datasource_dataset_popup").val(dataObj.datasetName).trigger('change');
						}
					}
					
					
				  
					$('#home-11').hide();
					$('#home-22').show();
					$('#home-33').hide();
					$("#viewdatatablepop" ).addClass("active");
					$("#viewdatadetailpop" ).removeClass("active");
					$("#viewdatatablegrpop" ).removeClass("active");
					table.destroy();
					getTables(idDataShow,tahun,project_tipe)
			  });
			  
			  
			  
			$("#viewdatadetailpop").click(function () {
				$('#home-11').show();
				$('#home-22').hide();
				$('#home-33').hide();
			});
			

			$("#viewdatatablepop").click(function () {
				
				var child3 = $('#headernya_pop').find("#newtables");
				if(child3.length == 0) {
					
					var childdraf = $('#headernya_pop').find("#chartdetail_popup");
					if(childdraf.length > 0) {
						$("#chartdetail_popup").remove();
					}
					
					$('#headernya_pop').append('<div class="table-responsive" style="padding-bottom:20px;" id="chartdetail_popup"></div>' +
															'<table class="table " id="newtables">' +
																'	<thead>' +
																'		<tr id="newtableheadder">' + 
																	'		<th>Loading ........</th> ' +
																	'	</tr> ' +
																	 ' </thead> ' +
																'</table>')
				}else{
					//table.destroy();
				}
		
		
				$('#home-11').hide();
				$('#home-22').show();
				$('#home-33').hide();
				table.destroy();
				getTables(idDataShow,tahun,project_tipe)
			});
			
			$("#viewdatatablegrpop").click(function () {
				$('#home-11').hide();
				$('#home-22').hide();
				$('#home-33').show();
			});
			
			  
			cloned_chart.render('chartsfront2');
			
			
			
		})
		
		box1.on("hide.bs.modal", function() {
			pop = 1;
			//table.destroy();
		})
	}
						
}



var table;
var datacolumn;
function getTables(id,tahun,project_tipe){
	//table = new $('#newtables').DataTable();
	
	
	console.log(table);
	if(table != null){
		//$('#newtables').empty();						
		//$('#newtables').dataTable().fnClearTable();
		//$('#newtables').dataTable().fnDestroy();
	}
	
	$(".loading").css('display','block');
	$("#newtables").css('width','initial !important');
	$("#newtables tbody").empty();
	
	$.post('<?php echo base_url($headurl.'/getColumn'); ?>',
	{ 
		id : id,
		tahun : tahun,
		project_tipe : project_tipe,
		categories : $('#datasource_categories_popup').val(),
		dataset : $('#datasource_dataset_popup').val(),
		<?= $this->security->get_csrf_token_name(); ?> : csrfHash  
	}, 
	function (data) {
			//////console.log.log(data); 
			var obj = JSON.parse(data);
			updateCsrfToken(obj.csrf_hash)
			datacolumn = obj.column;
			$('#newtableheadder').empty();
			$('#newtableheadder .addth').remove();
			$('#newtableheadder tbody').remove();
			jQuery.each(datacolumn, function(i) {
				$('#newtableheadder').append('<th class="addth"></th>');
			})

				
			table = new $('#newtables').DataTable({ 
										"drawCallback": function( settings ) {
											var api = this.api();
											
										},
										"responsive": false,
										 "ordering": false,
										"dom"	: '<"row"<"col-md-6 text-left"l><"col-md-3 text-right"f><"col-md-3 text-right"B>>rt<"row"<"col-md-6"i><"col-md-6"p>>',
										"bPaginate": true,
										"bInfo": false,
										"bFilter": true,
										"buttons" : [ 'copy', 
										{
											extend: 'excel',
											action: newExportAction
										}],
										"oLanguage" : {
											"sProcessing": "<div class='load1 load-wrapper'><div class='loader'>Loading...</div></div>",
											 "oPaginate" : {
												"sFirst": "<<",
												"sPrevious": "<",
												"sNext": ">", 
												"sLast": ">>" 
											}
										},
										"fixedColumns":   {
											leftColumns: 1,
											rightColumns: 1
										},
										"sPaginationType": "full_numbers",
										"lengthMenu": [[10, 25, 50, 100, 500, 1000, -1], [10, 25, 50, 100, 500, 1000,'All']],
										"processing": true,
										"serverSide": true,
										"order": [],
										"ajax": {
											"url": "<?php echo base_url($headurl.'/getColumnDetail'); ?>",
											"type": "POST",
											"data": function ( d ) {
												d.id = id
												d.tahun = tahun
												d.project_tipe = project_tipe
												d.categories = $('#datasource_categories_popup').val()
												d.dataset = $('#datasource_dataset_popup').val()
												d.<?= $this->security->get_csrf_token_name(); ?> = csrfHash 
											},
																dataSrc: function(json) {
																	console.log(json); // <-- cek isinya
																	if (json.csrf_hash) {
																		updateCsrfToken(json.csrf_hash)
																		csrfHash = json.csrf_hash;
																	}
																	return json.data;
																},
										error: function(xhr, error, thrown) {
											console.warn("AJAX Error:", error);

											 console.error("AJAX Error:", error);

											  Swal.fire({
												icon: 'error',
												title: 'Gagal Memuat Data!',
												text: 'Terjadi kesalahan saat mengambil data dari server.',
												footer: '<a href="<?php base_url('data_ticket'); ?>">Cek koneksi atau hubungi admin.</a>',
												confirmButtonText: 'Coba Lagi',
												customClass: {
												  popup: 'swal-custom-zindex'
												},
												didOpen: () => {
												  // Tambahkan langsung z-index ke elemen swal jika perlu
												  $('.swal2-container').css('z-index', 99999);
												}
											  }).then((result) => {
												if (result.isConfirmed) {
												  table.draw(); // reload DataTable jika user klik "Coba Lagi"
												}
											  });
										  }
										},
										"columns" : datacolumn,
										"columnDefs": [
											{ "width": "30%", "targets": -1 }
										 ],
								})
								

								
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


function getGrafikLevel2(id,tahun,project_tipe){
	if(id == 444){
				 $.post('<?php echo base_url($headurl.'/projectbyam'); ?>',{ tahun : tahun, <?= $this->security->get_csrf_token_name(); ?> : csrfHash  }, function (data) {
						obj = JSON.parse(data);
						updateCsrfToken(obj.csrf_hash)
						if(obj.message == "success"){ 
						
							var caption = "Sales Funnel";
							var subCaption = "Nominal Rp.";
							
							
					  var myChart2 = new FusionCharts({
						type: "mscolumn2d",
						renderAt: "chartdetail_popup",
						width: "100%",
						height: "400",
						dataFormat: "json",
						dataSource: {
						 chart: {
							caption: caption,
							 "subCaption": subCaption,
							"captionFont": "Arial",
									"captionFontSize": "14",
						plottooltext: "<b>$percentValue</b> $label",
							  "showValues": "1",
							  showlegend: "1",
							  showpercentvalues: "1",
							  legendposition: "bottom",
							  usedataplotcolorforlabels: "1",
							  theme: "fusion",
							  showLabels: "1",
							  labelFontSize:"12",
							   legendItemFontSize:"12",
							  animateClockwise: "1",
							  "pieRadius": "40",
						  "placeValuesInside": "0",
						  "numberScaleValue": "1000,1000,1000",
						  "numberScaleUnit": " rb, jt, M",
						  "decimalSeparator": ",",
						  "thousandSeparator": ".",
						  valueFontSize : "12",
						  "rotateValues": "1",
						  },
						  categories: [
							{
							  category: [
								{
								  label: "ENT <br> Conv Rasio :  11% <br> Lose : 40 M | 10 Project <br> Cancel : 20 M | 12 Project"
								},
								{
								  label: "GPMB <br> Conv Rasio :  41% <br> Lose : 40 M | 10 Project <br> Cancel : 20 M | 12 Project"
								},
								{
								  label: "TSB <br> Conv Rasio :  42% <br> Lose : 40 M | 10 Project <br> Cancel : 20 M | 12 Project"
								},
								{
								  label: "E-COMM & REG <br> Conv Rasio : 8% <br> Lose : 40 M | 10 Project <br> Cancel : 20 M | 12 Project"
								}
							  ]
							}
						  ],
						  dataset: [
							{
							  seriesname: "F0",
							  color : "#feae65",
							  data: [
								{
								  value: "2150",
								   "displayValue": "2150 M",
								},
								{
								  value: "1268",
								  "displayValue": "1268 M",
								},
								{
								  value: "850",
								  "displayValue": "850 M",
								},
								{
								  value: "551",
								  "displayValue": "850 M",
								}
							  ]
								},{
							  seriesname: "F1",
							  color : "#e6f69d",
							  data: [
								{
								  value: "1632",
								   "displayValue": "1632 M",
								},
								{
								  value: "1264",
								  "displayValue": "1264 M",
								},
								{
								  value: "850",
								  "displayValue": "850 M",
								},
								{
								  value: "550",
								  "displayValue": "850 M",
								}
							  ]
							},{
							  seriesname: "F2",
							  color : "#f66d44",
							  data: [
								{
								  value: "1275",
								   "displayValue": "1275 M",
								},
								{
								  value: "1221",
								  "displayValue": "1221 M",
								},
								{
								  value: "505",
								  "displayValue": "505 M",
								},
								{
								  value: "478",
								  "displayValue": "850 M",
								}
							  ]
							},{
							  seriesname: "F3",
							  color : "#64c2a6",
							  data: [
								{
								  value: "415",
								   "displayValue": "415 M",
								},
								{
								  value: "401",
								  "displayValue": "401 M",
								},
								{
								  value: "438",
								  "displayValue": "438 M",
								},
								{
								  value: "320",
								  "displayValue": "320 M",
								}
							  ]
							},{
							  seriesname: "F4",
							  color : "#baeafe",
							  data: [
								{
								  value: "48",
								   "displayValue": "48 M",
								},
								{
								  value: "182",
								  "displayValue": "182 M",
								},
								{
								  value: "209",
								  "displayValue": "209 M",
								},
								{
								  value: "33",
								  "displayValue": "33 M",
								}
							  ]
							},{
							  seriesname: "F5",
							  color : "#def1ff",
							  data: [
								{
								  value: "47",
								   "displayValue": "47 M",
								},
								{
								  value: "162",
								  "displayValue": "162 M",
								},
								{
								  value: "182",
								  "displayValue": "182 M",
								},
								{
								  value: "24",
								  "displayValue": "24 M",
								}
							  ]
							}
						  ]
						},
							 events: {
									dataPlotClick: function (eventObj, dataObj) {
										
										$('#datasource_categories_popup').empty().trigger("change");
										$('#datasource_dataset_popup').empty().trigger("change");


										if(myChart2.args.type == 'funnel' ||  myChart2.args.type == 'doughnut2d' || myChart2.args.type == 'pie2d' || myChart2.args.type == 'bar2d' || myChart2.args.type == 'funnel'){
				if (typeof myChart2.args.dataSource.data !== 'undefined') {
					var obj1 = myChart2.args.dataSource.data;
					////console.log.log(obj1);
					if(obj1){
						for (var key in obj1) {
							////console.log.log(obj1[key].label);
							
							let text = obj1[key].label;
							const myArray = text.split("<br>");

							var newOption = new Option(myArray[0], myArray[0], false, false);
							$('#datasource_categories_popup').append(newOption).trigger('change');
						}
					}
				}
			
			}else{
				if (typeof myChart2.args.dataSource.categories !== 'undefined') {
					var obj1 = myChart2.args.dataSource.categories[0].category;
					////console.log.log(obj1);
					if(obj1){
						for (var key in obj1) {
							////console.log.log(obj1[key].label);
							let text = obj1[key].label;
							const myArray = text.split("<br>");

							var newOption = new Option(myArray[0], myArray[0], false, false);
							$('#datasource_categories_popup').append(newOption).trigger('change');
						}
					}
				}

				
				if (typeof myChart2.args.dataSource.dataset !== 'undefined') {
					var obj2 = myChart2.args.dataSource.dataset;
					if(obj2){
						for (var key in obj2) {
							var newOption = new Option(obj2[key].seriesname, obj2[key].seriesname, false, false);
							$('#datasource_dataset_popup').append(newOption).trigger('change');
						}
					}
				}
			
			}
										
										if(myChart2.args.type == 'doughnut2d' || myChart2.args.type == 'pie2d' || myChart2.args.type == 'bar2d' || myChart2.args.type == 'funnel'){
											if (typeof myChart2.args.dataSource.data !== 'undefined') {
												
												let text = dataObj.categoryLabel;
											const myArray = text.split("<br>");
							
												$("#datasource_categories_popup").val(myArray).trigger('change');
											}
										}else{
											if (typeof myChart2.args.dataSource.categories !== 'undefined') {
												let text = dataObj.categoryLabel;
											const myArray = text.split("<br>");
							
												$("#datasource_categories_popup").val(myArray).trigger('change');
											}
											
											
											if (typeof myChart2.args.dataSource.dataset !== 'undefined') {
												$("#datasource_dataset_popup").val(dataObj.datasetName).trigger('change');
											}
										}
					
										var child3 = $('#headernya_pop').find("#newtables");
										if(child3.length == 0) {
											
											var childdraf = $('#headernya_pop').find("#chartdetail_popup");
											if(childdraf.length > 0) {
												$("#chartdetail_popup").remove();
											}
											
											$('#headernya_pop').append('<div class="table-responsive" style="padding-bottom:20px;" id="chartdetail_popup"></div>' +
																					'<table class="table " id="newtables">' +
																						'	<thead>' +
																						'		<tr id="newtableheadder">' + 
																							'		<th>Loading ........</th> ' +
																							'	</tr> ' +
																							 ' </thead> ' +
																						'</table>')
										}else{
											//table.destroy();
										}
								
								
										$('#home-11').hide();
										$('#home-22').show();
										$('#home-33').hide();
										table.destroy();
										getTables(3,tahun,project_tipe)
									}
							  }
					  }).render();
					  $(".loading").css('display','none');
					
				}else{
					$(".loading").css('display','none');
				}
					})
				
	}else{
		$(".loading").css('display','none');
	}
}

</script>
