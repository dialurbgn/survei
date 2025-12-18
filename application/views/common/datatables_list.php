<?php 
if(!isset($importdata)){ $importdata = false; } 
if(!isset($headurl)){ $headurl = '#'; }
if(!isset($advancesearch)){ $advancesearch = false; } 
if(!isset($columnforid)){ $columnforid = 0; } 
?>

<script>

 $(document).ready(function() {
	 
	 
	 // Initialize Dynamic Filters
	<?php if($advancesearch == true){ ?>
    const dynamicFilters = new DynamicFilters({
		url: '<?php echo base_url('dashboard/get_select_options_filter'); ?>', // Dari PHP
        columns: filterColumns, // Dari PHP
        tableId: 'datatablesskp',
		csrfHash: csrfHash || $('#csrf_token').val(),
		table: '<?php echo $tabledb; ?>',
        ajaxUrl: '<?php echo base_url($linkdata); ?>',
        csrfTokenName: '<?php echo $this->security->get_csrf_token_name(); ?>'
    });

	<?php } ?>
	
	 let retryCount = 0;
	const maxRetries = 3;

       getHeader('<?php echo $module; ?>')
		table = $('#datatablesskp').DataTable({
                          colReorder: true,
						  initComplete: function() {
							  <?php if($advancesearch == true){ ?>
								// Set reference to table in dynamicFilters
								dynamicFilters.table = table;
							  <?php } ?>
							},
                          "drawCallback": function(settings) {
                            KTMenu.createInstances();
                            $("#header_data_total").html($('#dt_total').html());
							console.log(settings); // <-- cek isinya
							<?php if($advancesearch == true){ ?>
							 updateFilterSummary();
							  <?php } ?>
                          },
                          "rowCallback": function(row, data) {
							console.log(data[<?php echo $columnforid+1; ?>])
							console.log(row)
                            var key = 0;
                            for (const theArray in data) {
                              if (key == 0) {
                                $('td:eq(' + key + ')', row).addClass('dtkenolbtn');
                              }
                              if (key == 1) {
                                $('td:eq(' + key + ')', row).addClass('dtkenol');
                                break
                              }
                              key = key + 1
                            }
							
							  
                          },
                          "responsive": false,
                          "scrollY": false,
                          "scrollX": true,
                          "scrollCollapse": true,
                          "processing": true,
                          "dom": '<"row"<"col-md-5 text-left"B><"col-md-7 text-right"f>>rt<"row"<"col-md-1"l><"col-md-5 text-right"i><"col-md-6"p>>',
                          "buttons": [{
                            className: 'btn-copy',
                            text: '<i class = "fas fa-copy" ></i> Salin',
                            extend: 'copy'
                          }, {
                            className: 'btn-excel',
                            text: '<i class = "far fa-file-excel" ></i> Unduh Excel',
                            extend: 'excel',
                            action: newExportAction,
							exportOptions: {
							 columns: function (idx, data, node) {
								 if (idx == 0) {
									 return false;
								 }
								 return true;
							  },
						    },
                          }
						  ,
						  <?php if($this->ortyd->access_check_insert_data($module) && $importdata == true) { 
							if($this->session->userdata('group_id') == 10000) { ?>
										{
											text: '<i class="ti-upload"></i> Import Data',
											action: function ( e, dt, node, config ) {
												upload();
											}
										},
										<?php 
								}
							} ?>
							],
                          "oLanguage": {
                            "sProcessing": "Mengambil Data ...",
                            "oPaginate": {
                              "sFirst": "<<",
                              "sPrevious": "<",
                              "sNext": ">",
                              "sLast": ">>"
                            },
                            "sSearch": '<i class = "fa fa-search" ></i>',
                            "sSearchPlaceholder": 'Cari ...',
                            "sInfo": 'Menampilkan _START_ sampai _END_ dari <span id = "dt_total" style = "display: contents;" > _TOTAL_</span>', 
							<?php
                            if ($this->ortyd->access_check_insert_data($module) && $this ->session->userdata("group_id") != 3) {
                            ?>
								"sEmptyTable" : 'Tidak ada data yang tersedia', 
							<?php
                            } else { ?>
								"sEmptyTable" : 'Tidak ada data yang tersedia', 
							<?php
                            } ?>
                            //"sLengthMenu": "Menampilkan _MENU_ Entri",
                            "sInfoEmpty" : ""
                          },
                          "fixedColumns": {
                            leftColumns: 2,
                          },
                          "sPaginationType": "full_numbers",
                          "lengthMenu": [
                            [10, 50, 100, 500, 1000, -1],
                            [10, 50, 100, 500, 1000, 'All']
                          ],
                          "processing": true,
                          "serverSide": true,
                          "order": [],
                          "ajax": {
                            "url": "<?php echo base_url($linkdata); ?>",
                            "type" : "POST",
                            "data": function(d) {
                              d.active = type;
                              d.table = '<?php echo $tabledb; ?>';
							  d.select2search = $("#select2search").val();
							  <?php if($advancesearch == true){ ?>
							  d.filters = dynamicFilters.getFiltersForAjax();
							  <?php } ?>
                              d.<?php echo $this->security->get_csrf_token_name(); ?> = csrfHash || $('#csrf_token').val();
                            },
							dataSrc: function(json) {
								console.log(json); // <-- cek isinya
								if (json.csrf_hash) {
									 updateCsrfToken(json.csrf_hash)
								}
								return json.data;
							},
							error: function(xhr, error, thrown) {
								console.warn("AJAX Error:", error);
								console.error("AJAX Error:", error);

								
										 if (retryCount < maxRetries) {
											retryCount++;
											console.log(`Retrying... (${retryCount}/${maxRetries})`);
											setTimeout(() => {
												table.draw(false); // reload data tanpa reset pagination
											}, 1000); // tunggu 1 detik sebelum retry
										} else {
											retryCount = 0; // reset retry counter
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
												 table.draw(); // reload data tanpa reset pagination
												}
											  });
										}
										
							}
                          },
                          "columnDefs": [{
                            "targets": [0, 0],
                            "orderable": false,
                            "width": "10px"
                          }],
                        }).on('column-reorder', function(e, settings, details) {
                            var datacolumnnya = [];
                            //console.log(settings.aoHeader[0])
                            var headernya = settings.aoHeader[0]
                            for (var x = 0; x<= headernya.length - 2; x++) {
                              if (typeof(headernya[x].cell.nextElementSibling.attributes) != "undefined") {
                                dataaa = headernya[x].cell.nextElementSibling.attributes;
                                if (typeof(dataaa[0]) != "undefined") {
                                  if (typeof(dataaa[0]) != null) {
                                    result = dataaa[0].nodeValue;
                                    datacolumnnya[x] = result;
                                  }
                                }
                              }
                            }
                            const myArray = datacolumnnya.toString();
                            var myJsonString = JSON.stringify(myArray);
                            console.log(myArray)
                            //console.log(details)
                            savingTableViewOrder('<?php echo $module; ?>','<?php echo $tablenya; ?>', datacolumnnya, myArray)
                            });
							
							 // Handle individual row selection
                      $('#datatablesskp tbody').on('change', '.row-checkbox', function() {
                          var id = $(this).val();
                          var row = $(this).closest('tr');
                          
                          if ($(this).prop('checked')) {
                              if (selectedRows.indexOf(id) === -1) {
                                  selectedRows.push(id);
                                  row.addClass('selected');
                              }
                          } else {
                              var index = selectedRows.indexOf(id);
                              if (index !== -1) {
                                  selectedRows.splice(index, 1);
                                  row.removeClass('selected');
                              }
                              $('#select-all').prop('checked', false);
                          }
                          
                          updateSelectionUI();
                      });
                      
                      // Handle select all
                      $('#select-all').on('change', function() {
                          var isChecked = $(this).prop('checked');
                          
                          if (isChecked) {
                              selectAllRows();
                          } else {
                              clearAllSelections();
                          }
                      });
							
							$('<label class="pull-right" style="margin-left:10px;width:200px">' +
									'<select class="form-control form-control-sm" id="select2search" name="search_field">'+
										'<option value="<?php echo $searchid; ?>" selected ><?php echo $searchname; ?></option>'+
											'</select>' + 
									'</label>').appendTo("#datatablesskp_wrapper .dataTables_filter");

							$(".dataTables_filter label").addClass("pull-right");

							$("#select2search").select2({	
									width : '100%',
									//dropdownParent: $('.swal2-container'),
									ajax: {
										type: "POST",
										url: "<?php echo base_url('dashboard/getnamingfieldcontrol'); ?>",
										dataType: 'json',
										delay: 250,
										data: function (params) {
											return {
												q: params.term, // search term
												table:'<?php echo $tabledb; ?>',
												exclude:'<?php echo json_encode($exclude); ?>',
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
									placeholder: 'Pilih Tipe Pencarian'
								}).on("select2:select", function(e) { 
									table.draw();
								})
								
								 // Selection management functions
                  function selectAllRows() {
                      selectedRows = [];
                      $('#datatablesskp tbody .row-checkbox').each(function() {
                          var id = $(this).val();
                          selectedRows.push(id);
                          $(this).prop('checked', true);
                          $(this).closest('tr').addClass('selected');
                      });
                      updateSelectionUI();
                  }
                  
              
                 
                  
								
								// Show filter summary modal
    function showFilterSummary() {
        const filterInfo = dynamicFilters.exportFilters();
        
        let summaryHtml = '<div class="filter-summary-content">';
        
        if (filterInfo.count === 0) {
            summaryHtml += '<p class="text-muted">Tidak ada filter yang aktif</p>';
        } else {
            summaryHtml += `<p><strong>${filterInfo.count} filter aktif:</strong></p><ul class="list-unstyled">`;
            
            Object.keys(filterInfo.summary).forEach(label => {
                const filterData = filterInfo.summary[label];
                summaryHtml += `<li class="mb-2">`;
                summaryHtml += `<strong>${label}:</strong> `;
                
                const values = Object.values(filterData).filter(v => v);
                summaryHtml += values.join(', ');
                summaryHtml += `</li>`;
            });
            
            summaryHtml += '</ul>';
        }
        
        summaryHtml += '</div>';
        
        Swal.fire({
            title: 'Ringkasan Filter',
            html: summaryHtml,
            width: '500px',
            showCloseButton: true,
            showConfirmButton: false,
            customClass: {
                container: 'filter-summary-modal'
            }
        });
    }

    // Update filter context based on active/inactive status
    function updateFilterContext(activeStatus) {
        const contextLabel = activeStatus == 1 ? 'Data Aktif' : 'Data Terhapus';
        $('.filter-title').html(`<i class="fas fa-filter"></i> Filter ${contextLabel}`);
    }

    // Update filter summary in real-time
    function updateFilterSummary() {
        const filterInfo = dynamicFilters.exportFilters();
        
        // Update record info with filter context
        const info = table.page.info();
        let infoText = `Menampilkan ${info.start + 1} sampai ${info.end} dari ${info.recordsTotal} data`;
        
        if (filterInfo.count > 0) {
            infoText += ` (${filterInfo.count} filter aktif)`;
        }
        
        $('.dataTables_info').html(infoText);
    }

    // LOKASI: Function ini ada di dalam file DataTables Integration
    // Dipanggil di drawCallback DataTables untuk update info setiap kali tabel di-reload

    // Advanced search integration
    function setupAdvancedSearch() {
        // Add advanced search toggle
        const advancedSearchBtn = $(`
            <button type="button" class="btn btn-outline-secondary btn-sm ms-2" id="advanced-search-toggle">
                <i class="fas fa-search-plus"></i> Pencarian Lanjutan
            </button>
        `);
        
        $('.dataTables_filter').append(advancedSearchBtn);
        
        $('#advanced-search-toggle').click(function () {
			const container = $('#filter-container');
			const isVisible = container.is(':visible');

			if (!isVisible) {
				container.slideDown(); // langsung buka
			} else {
				container.slideUp(); // jika mau tutup juga
			}

			$('html, body').animate({
				scrollTop: $('#dynamic-filters').offset().top - 100
			}, 500);
		});
    }


	<?php if($advancesearch == true){ ?>
	 // Initialize advanced search
	
    setupAdvancedSearch();
	
	$('#filter-container').slideUp(0)
	   
    // Global functions for external access
    window.dynamicFilters = dynamicFilters;
    window.showFilterSummary = showFilterSummary;

	<?php } ?>
	

								
                        });
						
                      var oldExportAction = function(self, e, dt, button, config) {
                        if (button[0].className.indexOf('buttons-excel') >= 0) {
                          if ($.fn.dataTable.ext.buttons.excelHtml5.available(dt, config)) {
                            $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt, button, config);
                          } else {
                            $.fn.dataTable.ext.buttons.excelFlash.action.call(self, e, dt, button, config);
                          }
                        } else if (button[0].className.indexOf('buttons-print') >= 0) {
                          $.fn.dataTable.ext.buttons.print.action(e, dt, button, config);
                        }
                      };
                      var newExportAction = function(e, dt, button, config) {
                        var self = this;
                        var oldStart = dt.settings()[0]._iDisplayStart;
                        dt.one('preXhr', function(e, s, data) {
                          // Just this once, load all data from the server...
                          data.start = 0;
                          data.length = 2147483647;
                          dt.one('preDraw', function(e, settings) {
                            // Call the original action function 
                            oldExportAction(self, e, dt, button, config);
                            dt.one('preXhr', function(e, s, data) {
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

                      function get_data(data, dis) {
                        $('.nav-link').removeClass("active");
                        $('#' + dis + ' a').addClass("active");
                        type = data;
                        table.draw();
                      }
					  
					      function clearAllSelections() {
                      selectedRows = [];
                      $('#datatablesskp tbody .row-checkbox').prop('checked', false);
                      $('#datatablesskp tbody tr').removeClass('selected');
                      $('#select-all').prop('checked', false);
                      updateSelectionUI();
                  }
				  
				   
                  function updateSelectionUI() {
                      var count = selectedRows.length;
                      
                      if (count > 0) {
                          $('#batch-selection-info').show();
                          $('#selected-count').text(count);
                          $('#btn-batch-verify').show();
                      } else {
                          $('#batch-selection-info').hide();
                          $('#btn-batch-verify').hide();
                      }
                  }
</script>

<?php 
			   $viewdata_upload = array(
					'headurl' => $headurl,
			   );
			   
			   $this->load->view('common/v_data_upload', $viewdata_upload); ?>