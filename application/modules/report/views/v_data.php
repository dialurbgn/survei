<div id="kt_content_container" class="d-flex flex-column-fluid align-items-start container-xxl">
 <!--begin::Post-->
 <div class="content flex-row-fluid" id="kt_content"> 
	<?php
	 $title = $title;
	 $total_rows = 0;
	 $tablenya = $tabledb;
	 $exclude = $exclude_table;
	 $advancesearch = true;
	 $columnforid = 0;
	 include APPPATH . "views/navbar_header.php";
	?>
   <!--begin::Row-->
   <div class="row gx-6 gx-xl-9">
     <!--begin::Col-->
     <div class="col-lg-12">
       <!--begin::Summary-->
       <div class="card card-custom gutter-b example example-compact">
         <div class="row">
           <div class="col-sm-12">
             <div class="card-header tabbable" style="padding:0;display:none" id="ul_menu_header">
                <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bold" role="tablist" style="margin:0;margin-left:30px">
				   <li class="nav-item mt-2" id="nav-1" onClick="get_data(1,'nav-1');">
                   <a class="nav-link active " data-toggle="tab" href="javascript:;" role="tab">
                     <i class="far fa-list-alt"></i> &nbsp;Semua Data <?php echo $title; ?></a>
                 </li>
                 <li class="nav-item mt-2" id="nav-2" onClick="get_data(0,'nav-2');">
                   <a class="nav-link" data-toggle="tab" href="javascript:;" role="tab">
                     <i class="far fa-trash-alt"></i> &nbsp;Terhapus</a>
                 </li>
               </ul>
             </div>
			 
			 <?php if($advancesearch == true){ ?>
             <!-- Dynamic Filters Section -->
             <div class="card-body" style="padding-bottom: 0;">
               <div class="row mb-4" id="dynamic-filters">
                 <div class="col-12">
                   <h6 class="mb-3">
                     <i class="fas fa-filter"></i> Filter Data
                     <button type="button" class="btn btn-sm btn-light ms-2" id="toggle-filters">
                       <i class="fas fa-chevron-down"></i>
                     </button>
                   </h6>
                   <div id="filter-container" style="display: none;">
                     <div class="row" id="filter-row">
                       <!-- Filters akan diisi secara dinamis -->
                     </div>
                     <div class="row mt-3">
                       <div class="col-12">
                         <button type="button" class="btn btn-primary btn-sm" id="apply-filters">
                           <i class="fas fa-search"></i> Terapkan Filter
                         </button>
                         <button type="button" class="btn btn-secondary btn-sm ms-2" id="clear-filters">
                           <i class="fas fa-times"></i> Bersihkan Filter
                         </button>
                       </div>
                     </div>
                   </div>
                 </div>
               </div>
             </div>
			<?php } ?>
			
             <div class="card-body card-table">
               <div class="table-responsive table-custom">
                 <table id="datatablesskp" class="table table-striped align-middle table-row-dashed fs-6 gy-5">
                   <thead>
                     <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                       <th></th> 
						<?php
						$total_rows = 0;
						$exclude = $exclude_table;
						$query_column = $this->ortyd->getviewlistcontrol(
							$tablenya,
							$module,
							$exclude
						);
						
						$searchid = $tablenya.'.'.$slug_indentity;
						$label_name = $this->ortyd->translate_column(
							$tablenya,
							$searchid
						);
						$searchname = $label_name;
						
						// Store column info for filter generation
						$filter_columns = array();
				
						//print_r($query_column);
						if ($query_column) {
							$x = 1;
							foreach ($query_column as $rows_column) {
								$label_name = $this->ortyd->translate_column(
									$tablenya,
									$rows_column["name"]
								);
								
								if($x == 1){
									$searchid = $this->ortyd->translate_column_table(
										$tablenya,
										$rows_column["name"],
										$exclude
									);
									$searchname = $label_name;
								}
								
								if (
									$rows_column["name"] != "active"
								) {
									
									// Store column info for filters
									$tableref = $this->ortyd->getRefDataTable($tablenya, $rows_column["name"]);
									$filter_columns[] = array(
										'name' => $rows_column["name"],
										'label' => $label_name,
										'table' => $tableref[0],
										'tableid' => $tableref[1],
										'tablename' => $tableref[2],
										'columnname' => $tableref[3],
										'type' => $this->ortyd->getTipeData($tablenya, $rows_column["name"])
									);
									
									$table_change = "'" . $tablenya . "'";
									$table_change_id = "'" . $rows_column["name"] . "'";
									$label_name_text = "'".$label_name."'";
									$editheader = '<span style="cursor:pointer" onClick="changeTitle('.$table_change .",".$table_change_id .",".$label_name_text.')"><i class="fa fa-edit"></i></span>';
									if ($this->ortyd->getAksesEditNaming() == true) {
										echo '<th data-id="'.$rows_column["name"].'">' .
											$label_name.
											$editheader.
											"</th>";
									} else {
										echo '<th data-id="'.$rows_column["name"] .'">'.
											$label_name .
											"</th>";
									}
								}
								$x++;
							}
							$total_rows = $x;
						}
						?>
                       <!--<th>Status</th><th></th> -->
                     </tr>
                   </thead>
                   <tbody></tbody>
                 </table>
               </div>
			   
			    <?php 
			   $viewdata = array(
					'module' => $module,
					'linkdata' => $linkdata,
					'tabledb' => $tabledb,
					'exclude' => $exclude,
					'tablenya' => $tablenya,
					'searchid' => $searchid,
					'searchname' => $searchname,
					'headurl' => $headurl,
					'advancesearch' => $advancesearch,
					'columnforid' => $columnforid,
					'importdata' => false
			   );
			   
			   $this->load->view('common/datatables_list', $viewdata); ?>
			   
			   
               <script type="text/javascript">
			   
					      // Column information for filters
					   var filterColumns = <?php echo json_encode($filter_columns); ?>;
					   var appliedFilters = {};
					   
					   // Batch selection variables
					   var selectedRows = [];
					   var isSelectAll = false;      
					   
			        $( document ).ready(function() {
						$('#btn-buat-data').hide();
						
						setTimeout(function() {
							$('#header_data_menu').html('<?php echo $title; ?>');
						}, 1000); // 3000 ms = 3 detik

					});
				  
					<?php
                      if (isset($_GET["message"])) {
                        if ($_GET["message"] == "success") {
                          ?>Swal.fire({
                            icon: 'success',
                            title: 'Berhasil ...',
                            text: 'Menyimpan data berhasil',
                          })<?php
                        } else {
                          ?>Swal.fire({
                            icon: 'error',
                            title: 'Kesalahan ...',
                            text: 'Menyimpan data error',
                          })<?php
                        }
                      } ?>
					  
				
				
                  var table;
                  var type = 1;

                  function closeMenu() {
                    KTMenu.createInstances();
                    var menuElement = document.querySelector("#kt_menu_data");
                    var menu = KTMenu.getInstance(menuElement);
                    var item = document.querySelector("#kt_menu_data_item");
                    menu.hide(item);
                  }

				  function deletedata(id) {
                        var boxdelete = bootbox.confirm({
                            title: "Confirm Action",
                            message: "Do you want to delete this data ?",
                            buttons: {
                              cancel: {
                                label: '<i class = "fa fa-times" ></i> Cancel'
                              },
                              confirm: {
                                label: '<i class = "fa fa-check" ></i> Confirm'
                              }
                            },
                            callback: function(result) {
                              if (result == true) {
                                $.post('<?php echo base_url($headurl.
                                    "/removedata"); ?>',{ id : id, <?php echo $this->security->get_csrf_token_name(); ?> : csrfHash  }, function (data) {
                                   updateCsrfToken(data.csrf_hash)
								  if (data.message == "success") {
                                    table.draw(false);
                                    boxdelete.modal('hide');
                                    Swal.fire({
                                      icon: 'success',
                                      title: 'Berhasil ...',
                                      text: 'Hapus data berhasil',
                                    })
                                  } else {
                                    table.draw(false);
                                    boxdelete.modal('hide');
                                    Swal.fire({
                                      icon: 'error',
                                      title: 'Kesalahan ...',
                                      text: 'Hapus data error',
                                    })
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

								});
                            }
                          }
                        });
                    }

                    function restoredata(id) {
                      var boxdelete = bootbox.confirm({
                          title: "Confirm Action",
                          message: "Do you want to restore this data ?",
                          buttons: {
                            cancel: {
                              label: '<i class = "fa fa-times" ></i> Cancel'
                            },
                            confirm: {
                              label: '<i class = "fa fa-check" ></i> Confirm'
                            }
                          },
                          callback: function(result) {
                            if (result == true) {
                              $.post('<?php echo base_url($headurl.
                                  "/restoredata"); ?>',{ id : id, <?php echo $this->security->get_csrf_token_name(); ?> : csrfHash  }, function (data) {
                                 updateCsrfToken(data.csrf_hash)
								if (data.message == "success") {
                                  table.draw(false);
                                  boxdelete.modal('hide');
                                  Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil ...',
                                    text: 'Mengembalikan data berhasil',
                                  })
                                } else {
                                  table.draw(false);
                                  boxdelete.modal('hide');
                                  Swal.fire({
                                    icon: 'error',
                                    title: 'Kesalahan ...',
                                    text: 'Mengembalikan data error',
                                  })
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
								});
                          }
                        }
                      });
                  }
               </script>
             </div>
           </div>
         </div>
       </div>
     </div>
   </div>
 </div>
</div>