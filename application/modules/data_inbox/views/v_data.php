<div id="kt_content_container" class="d-flex flex-column-fluid align-items-start container-xxl">
 <!--begin::Post-->
 <div class="content flex-row-fluid" id="kt_content"> 
	<?php
	 $total_rows = 0;
	 $tablenya = $tabledb;
	 $exclude = $exclude_table;
	 include APPPATH . "views/navbar_header.php";
	?>
   <!--begin::Row-->
   <div class="row gx-6 gx-xl-9">
     <!--begin::Col-->
     <div class="col-lg-6 col-xl-3">
        
<!--begin::Contact group wrapper-->
<div class="card card-flush">
    <!--begin::Card header-->
    <div class="card-header pt-7" id="kt_chat_contacts_header">
        <!--begin::Card title-->
        <div class="card-title">
            <h2>Filter</h2>
        </div>
        <!--end::Card title-->
    </div>
    <!--end::Card header-->


    <!--begin::Card body-->
    <div class="card-body pt-5">
        <!--begin::Contact groups-->
        <div class="d-flex flex-column gap-5">
                            <!--begin::Contact group-->
                <div class="d-flex flex-stack">
                    <a href="javascript:;" id="nav-1" onClick="get_data(1,'nav-1');" class="nav-link fs-6 fw-bold text-gray-800 text-hover-primary text-active-primary">Semua Inbox</a>
                    <div class="badge badge-light-primary" id="header_data_total_side"></div>
                </div>
                <!--begin::Contact group-->
                            <!--begin::Contact group-->
                <div class="d-flex flex-stack">
                    <a href="javascript:;" id="nav-3" onClick="get_data(3,'nav-3');" class="nav-link fs-6 fw-bold text-gray-800 text-active-primary text-hover-primary active ">Belum Dibaca</a>
                     <!-- <div class="badge badge-light-primary">9</div> -->
                </div>
                <!--begin::Contact group-->
                            <!--begin::Contact group-->
                <div class="d-flex flex-stack">
                    <a href="javascript:;" id="nav-4" onClick="get_data(4,'nav-4');" class="nav-link fs-6 fw-bold text-gray-800 text-active-primary text-hover-primary ">Terbaca</a>
                     <!-- <div class="badge badge-light-primary">9</div> -->
                </div>
                <!--begin::Contact group-->
                            <!--begin::Contact group-->
                <div class="d-flex flex-stack">
                    <a href="javascript:;" id="nav-2" onClick="get_data(2,'nav-2');" class="nav-link fs-6 fw-bold text-gray-800 text-active-primary text-hover-primary ">Hapus</a>
                     <!-- <div class="badge badge-light-primary">9</div> -->
                </div>
                <!--begin::Contact group-->
                    </div>
        <!--end::Contact groups-->

        <?php
                            if ($this->ortyd->access_check_insert_data($module) && $this ->session->userdata("group_id") != 3) {
                            ?>
        <!--begin::Separator-->
        <div class="separator my-7" style="display:none"></div>
        <!--begin::Separator-->
        
        <!--begin::Add new contact-->
        <a href="#" class="btn btn-primary w-100" style="display:none">
            <i class="ki-duotone ki-badge fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>           Pesan Baru
        </a>
        
                            <?php } ?>
        <!--end::Add new contact-->
    </div>
    <!--end::Card body-->
</div>
<!--end::Contact group wrapper-->    </div>

     <div class="col-lg-9">
       <!--begin::Summary-->
       <div class="card card-custom gutter-b example example-compact">
         <div class="row">
           <div class="col-sm-12">

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
									$rows_column["name"] != $identity_id &&
									$rows_column["name"] != "active"
								) {
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
					'searchname' => $searchname
			   );
			   
			   $this->load->view('common/datatables_list', $viewdata); ?>
			   
               
               <script type="text/javascript">
                  var table;
                  var type = 3;

                  function closeMenu() {
                    KTMenu.createInstances();
                    var menuElement = document.querySelector("#kt_menu_data");
                    var menu = KTMenu.getInstance(menuElement);
                    var item = document.querySelector("#kt_menu_data_item");
                    menu.hide(item);
                  }
                  
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
                                    table.draw();
                                    boxdelete.modal('hide');
                                    Swal.fire({
                                      icon: 'success',
                                      title: 'Berhasil ...',
                                      text: 'Hapus data berhasil',
                                    })
                                  } else {
                                    table.draw();
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

									//loadingclose();
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
                                  table.draw();
                                  boxdelete.modal('hide');
                                  Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil ...',
                                    text: 'Mengembalikan data berhasil',
                                  })
                                } else {
                                  table.draw();
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

									//loadingclose();
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