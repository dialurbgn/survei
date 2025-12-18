<?php
//CONTROLLER BY HANAFI GINTING

defined('BASEPATH') OR exit('No direct script access allowed');

class Data_survei_pm_detail extends MX_Controller {

		//CONFIG VARIABLE
		private $urlparent = 'data_survei_pm_detail'; //NAME TABLE 
		private $identity_id = 'slug'; //IDENTITY TABLE
		private $field = 'slug'; // IDENTITY FROM NAME FOR GET ID
		private $slug_indentity = 'name'; //NAME FIELD 
		private $sorting = 'modified'; // SORT FOR VIEW
		private $exclude = array('color','history_id','status_id','created','modified','createdid','modifiedid','id','active','slug');
		private $exclude_table = array('color','history_id','status_id','created','modified','createdid','modifiedid','id','active','slug');
		//END CONFIG VARIABLE
		
		private $viewname;
		private $viewformname;
		private $tabledb;
		private $tableid;
		private $titlechilddb;
		private $headurldb;
		private $actionurl;
		private $module;
		private $modeldb;
		private $approval_id;
		private $tableview;
		private $modeedit;

		public function __construct()
		{
			
			
			$this->viewname = $this->urlparent.'/views/v_data';
			$this->viewformname = $this->urlparent.'/views/v_data_form';
			$this->tabledb = $this->urlparent;
			$this->tableid = $this->urlparent.'.id';
			$this->titlechilddb = strtoupper($this->urlparent);
			$this->headurldb = $this->urlparent;
			$this->actionurl = $this->urlparent.'/actiondata';
			$this->module = $this->urlparent;
			$this->modeldb = 'm_data';
			$this->approval_id = 0;
			$this->tableview = $this->tabledb;
			$this->modeedit = 'popup'; //popup or normal
			
			$this->load->model($this->modeldb,'m_model_data');
			$this->load->model('dashboard/m_dashboard','m_model_master');
			$this->titlechilddb = $this->ortyd->getmodulename($this->module);
			
			$this->ortyd->session_check();
			$this->ortyd->access_check($this->module);
		}
		
		public function index()
		{
			$data['title'] = $this->titlechilddb;
			$data['module'] = $this->module;
			$data['tabledb'] = $this->tableview;
			$data['identity_id'] = $this->identity_id;
			$data['slug_indentity'] = $this->slug_indentity;
			$data['exclude_table'] = $this->exclude_table;
			$data['headurl'] = $this->headurldb;
			$data['linkdata'] = $this->urlparent.'/get_data';
			$data['linkcreate'] = $this->urlparent.'/createdata';
			$this->template->load('main',$this->viewname, $data);
		}
		
		/**
		 * Enhanced get_data method with filter support
		 */
		public function get_data() {
			$activateddata = ['Inactive', 'Active'];
			$gid = $this->session->userdata('group_id');
			// Amankan dan validasi input
			$table = $this->security->xss_clean($this->input->post('table', true) ?? '');
			$searchfield = $this->security->xss_clean($this->input->post('select2search', true) ?? '');
			$activeStatus = $this->security->xss_clean($this->input->post('active', true)  ?? '');
			$start = (int) ($_POST['start'] ?? 0);
			$draw = (int) ($_POST['draw'] ?? 1);
			
			// Get filters from frontend
			$filters = $this->input->post('filters', true) ?? [];
			$filters = $this->security->xss_clean($filters);

			if (empty($table)) {
				echo json_encode([
					'draw' => $draw,
					'recordsTotal' => 0,
					'recordsFiltered' => 0,
					'csrf_hash' => $this->security->get_csrf_hash(),
					'data' => [],
					'message' => 'Table parameter is required.'
				]);
				return;
			}

			$sorting = $this->sorting;
			$selectnya = [];
			$jointable = [];
			$joindetail = [];
			$joinposition = [];
			$wherecolumn = [];
			$wheredetail = [];

			$exclude = $this->exclude_table;
			$query_column = $this->ortyd->getviewlistcontrol($table, $this->module, $exclude);

			if ($query_column) {
				$ordernya = [null];
				$searchnya = [];
				$alias = 0;

				foreach ($query_column as $rowsdata) {
					$table_references = $this->ortyd->get_table_reference($table, $rowsdata['name']);

					if ($table_references) {
						$join_alias = $table_references[0] . '_' . $alias;
						$column_alias = $join_alias . '.' . $table_references[2];
						$as_alias = $join_alias . '_' . $table_references[2];

						$ordernya[] = $column_alias;
						$searchnya[] = $column_alias;
						$selectnya[] = "$column_alias as $as_alias";

						if (!in_array("{$table_references[0]} as {$join_alias}", $jointable)) {
							$jointable[] = "{$table_references[0]} as {$join_alias}";
							$joindetail[] = "{$table}.{$rowsdata['name']} = {$join_alias}.{$table_references[1]}";
							$joinposition[] = 'left';
						}
					} else {
						$col = "{$table}.`{$rowsdata['name']}`";
						$ordernya[] = $col;
						$searchnya[] = $col;
					}

					$alias++;
				}

				$ordernya[] = null;
				$column_order = $ordernya;
				$column_search = $searchnya;
			} else {
				$column_order = [null];
				$column_search = [null];
			}

			// Jika search field spesifik digunakan
			if (!empty($searchfield)) {
				$column_search = [$searchfield];
			}

			$order = [$table . '.' . $sorting => 'DESC'];
			$selectnya = implode(',', $selectnya);
			$select = $table . '.*' . ($selectnya ? ',' . $selectnya : '');

			$wherecolumn[] = $table . '.active';
			$wheredetail[] = $activeStatus;
			
			if($gid == 3){
				$wherecolumn[] = $table . '.createdid';
				$wheredetail[] = $userid;
			}

			// Add dynamic filters to where conditions
			if (!empty($filters) && is_array($filters)) {
				foreach ($filters as $column => $filterData) {
					$column = $this->security->xss_clean($column);
					
					if (!is_array($filterData)) {
						continue;
					}
					
					foreach ($filterData as $type => $value) {
						$value = $this->security->xss_clean($value);
						
						if (empty($value)) {
							continue;
						}
						
						$columnName = $table . '.' . $column;
						
						switch ($type) {
							case 'text':
								$wherecolumn[] = $columnName . ' LIKE';
								$wheredetail[] = '%' . $value . '%';
								break;
								
							case 'select':
								$wherecolumn[] = $columnName;
								$wheredetail[] = $value;
								break;
								
							case 'date_start':
								$wherecolumn[] = $columnName . ' >=';
								$wheredetail[] = $value;
								break;
								
							case 'date_end':
								$wherecolumn[] = $columnName . ' <=';
								$wheredetail[] = $value . ' 23:59:59';
								break;
								
							case 'number_min':
								$wherecolumn[] = $columnName . ' >=';
								$wheredetail[] = (float) $value;
								break;
								
							case 'number_max':
								$wherecolumn[] = $columnName . ' <=';
								$wheredetail[] = (float) $value;
								break;
						}
					}
				}
			}

			$groupby = [];

			$list = $this->ortyd->get_datatables($table, $column_order, $column_search, $order, $select, $jointable, $joindetail, $joinposition, $wherecolumn, $wheredetail, $groupby);
			$data = [];
			$no = $start;

			foreach ($list as $rows) {
				$rows = (array) $rows;
				$no++;
				$row = [];

				$identity_id = $rows[$this->identity_id];
				$uuid = "'" . addslashes($identity_id ?? '') . "'";

				// Edit, Restore, Delete Access
				$editdata = '';
				$restoredata = '';
				$deletedata = '';

				if ($this->ortyd->access_check_update_data($this->module)) {
					
					if($this->modeedit == 'popup'){
						$mode = "'edit'";
						$editdata = '<div class="menu-item px-3"><a href="javascript:;" class="dropdown-item d-flex align-items-center gap-2" onClick="openPopupForm('.$mode.','.$uuid.')"><i class="fa fa-edit text-primary mt-1"></i> Edit</a></div>';
					}else{
						$editdata = '<div class="menu-item px-3"><a class="dropdown-item d-flex align-items-center gap-2" href="' . base_url() . $this->urlparent . '/editdata/' . $identity_id . '"><i class="fa fa-edit text-primary mt-1"></i> Edit</a></div>';
					}
					
					$restoredata = '<div class="menu-item px-3"><a href="javascript:;" class="dropdown-item d-flex align-items-center gap-2" onClick="restoredata(' . $uuid . ')"><i class="fa fa-undo text-warning"></i> Restore</a></div>';
				}

				if ($this->ortyd->access_check_delete_data($this->module)) {
					$deletedata = '<div class="menu-item px-3"><a href="javascript:;" class="dropdown-item d-flex align-items-center gap-2" onClick="deletedata(' . $uuid . ')"><i class="fa fa-trash text-danger mt-1"></i> Delete</a></div>';
				}
				

				if ($this->ortyd->access_check_delete_data($this->module)) {
					$deletedata = '<div class="menu-item px-3"><a href="javascript:;" class="dropdown-item d-flex align-items-center gap-2" onClick="deletedata(' . $uuid . ')"><i class="fa fa-trash text-danger mt-1"></i> Delete</a></div>';
				}

				$status_label = $rows['active'] == 1 ? 'success' : 'danger';
				$status = '<span class="badge badge-light-' . $status_label . '">' . $activateddata[$rows['active']] . '</span>';

				$action_menu = $rows['active'] == 1 ? ($editdata . $deletedata) : $restoredata;

				$action = '
					<a href="#" class="btn btn-sm btn-primary btn-active-light-primary btn-flex btn-center btn-sm menu-dropdown" data-kt-menu-trigger="click" data-kt-menu-placement="top-end">...</a>
					<div class="menu menu-sub menu-sub-dropdown menu-sub-dropdown-dt menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
						' . $action_menu . '
					</div>
				';
				
				$row[] = '<div class="d-flex align-items-center gap-2">
								'.$action.'
						</div>';


				// Isi kolom data
				if($query_column){
					$alias = 0;
					foreach($query_column as $rowsdata){
						$table_references = null;
						$table_references = $this->ortyd->get_table_reference($table,$rowsdata['name']);
						if ($table_references) {
							$row[] = $rows[$table_references[0] . '_' . $alias . '_' . $table_references[2]] ?? '';
						} elseif ($rowsdata['name'] == 'link') {
							$row[] = '<a href="' . base_url($table) . $identity_id . '">' . htmlspecialchars($rows[$rowsdata['name']] ?? '') . '</a>';
						} else {
							$value = $rows[$rowsdata['name']] ?? '';
							$row[] = $this->ortyd->getFormatData($table, $rowsdata['name'], $value);
						}
						$alias++;
					}
				}

				$data[] = $row;
			}

			// Output JSON aman
			echo json_encode([
				'draw' => $draw,
				'recordsTotal' => $this->ortyd->count_filtered($table, $column_order, $column_search, $order, $select, $jointable, $joindetail, $joinposition, $wherecolumn, $wheredetail, $groupby),
				'recordsFiltered' => $this->ortyd->count_filtered($table, $column_order, $column_search, $order, $select, $jointable, $joindetail, $joinposition, $wherecolumn, $wheredetail, $groupby),
				'csrf_hash' => $this->security->get_csrf_hash(),
				'data' => $data,
			]);
		}
		
		public function createdata()
		{
			$this->ortyd->access_check_insert($this->module);
			$is_popup = $this->input->get('popup') == '1';
			
			$data['title'] = 'Buat '.$this->titlechilddb;
			$data['id'] = null;
			$data['tableview'] = $this->tableview;
			$data['module'] = $this->module;
			$data['modeldb'] = $this->m_model_data;
			$data['exclude'] = $this->exclude;
			$data['data'] = null;
			$data['headurl'] = $this->headurldb;
			$data['action'] = base_url().$this->actionurl.'/0';
			$data['is_popup'] = $is_popup;
			
			if ($is_popup) {
				// Return only the form content for popup
				echo $this->load->view($this->viewformname, $data, true);
			} else {
				// Normal page load
				$this->template->load('main',$this->viewformname, $data);
			}
		}
		
		public function editdata($ID)
		{
			$this->ortyd->access_check_update($this->module);
			$is_popup = $this->input->get('popup') == '1';
			
			$ID = $this->ortyd->select2_getname($ID,$this->tabledb,$this->field,$this->tableid);
			$data['title'] = 'Edit '.$this->titlechilddb;
			$data['id'] = $ID;
			$data['tableview'] = $this->tableview;
			$data['module'] = $this->module;
			$data['modeldb'] = $this->m_model_data;
			$data['exclude'] = $this->exclude;
			$data['headurl'] = $this->headurldb;
			$data['datarow'] = $this->m_model_data->get_data_byid($data['id'], $this->tabledb, $this->tableid);
			$data['action'] = base_url().$this->actionurl.'/'.$data['id'];
			$data['is_popup'] = $is_popup;
			
			if ($is_popup) {
				// Return only the form content for popup
				echo $this->load->view($this->viewformname, $data, true);
			} else {
				// Normal page load
				$this->template->load('main',$this->viewformname, $data);
			}
		}
		
		public function actiondata($id) {
			$data = [];
			$exclude = $this->exclude;
			$query_column = $this->ortyd->query_column($this->tabledb, $exclude);

			if (!$query_column) {
				return json_encode([
					"csrf_hash" => $this->security->get_csrf_hash(),
					"status"    => "error"
				]);
			}

			$isUpdate = ($id != '0');
			$isUpdate ? $this->ortyd->access_check_update($this->module) : $this->ortyd->access_check_insert($this->module);

			foreach ($query_column as $column) {
				$name = $column['name'];
				$type = $this->ortyd->getTipeData($this->module, $name);

				$input = $this->input->post($name, true);
				$input = $this->security->xss_clean($input ?? ''); // XSS protection

				if ($type == 'CURRENCY' || $name == 'nilai') {
					$input = $this->ortyd->unformatrp($input);
				}

				$data[$name] = ($input === '') ? null : $input;
			}

			$timestamp = date('Y-m-d H:i:s');
			$userId = $this->session->userdata('userid');
			$data['modifiedid'] = $userId;
			$data['modified'] = $timestamp;
			$data['active'] = 1;

			if (!$isUpdate) {
				$data['createdid'] = $userId;
				$data['created'] = $timestamp;

				// Tambahan slug hanya saat insert
				$slugSource = $this->input->post($this->slug_indentity, true);
				$slugSource = $this->security->xss_clean($slugSource ?? '');
				$data['slug'] = $this->ortyd->sanitize($slugSource, $this->tabledb);
			}

			if (count($data) <= 1) {
				return json_encode([
					"csrf_hash" => $this->security->get_csrf_hash(),
					"status"    => "error"
				]);
			}

			$this->db->trans_begin();

			if ($isUpdate) {
				$this->db->where($this->tableid, $id);
				$success = $this->db->update($this->tabledb, $data);
				$saveId = $id;
			} else {
				$success = $this->db->insert($this->tabledb, $data);
				$saveId = $this->db->insert_id();
			}

			if ($this->db->trans_status() === FALSE || !$success) {
				$this->db->trans_rollback();
				return $this->jsonError();
			}

			$this->db->trans_commit();
			$this->saveEvidence($saveId, $this->urlparent);

			echo json_encode([
				"csrf_hash" => $this->security->get_csrf_hash(),
				"status"    => "success"
			]);
		}
		
		public function removedata() {
			$this->ortyd->access_check_delete($this->module);

			$id = $this->security->xss_clean($this->input->post('id', true) ?? '');
			if (empty($id)) {
				echo json_encode([
					"csrf_hash" => $this->security->get_csrf_hash(),
					"message"   => "error"
				]);
				return;
			}

			$fieldKey = ($this->field == 'slug') ? $this->field : $this->tableid;

			$this->db->where($fieldKey, $id)->where('active', 1);
			$query = $this->db->get($this->tabledb)->result();

			if ($query) {
				$this->db->trans_start();
				$data = [
					'active'     => 0,
					'modifiedid' => $this->session->userdata('userid'),
					'modified'   => date('Y-m-d H:i:s')
				];
				$this->db->where($fieldKey, $id)->update($this->tabledb, $data);
				$this->db->trans_complete();

				if ($this->db->trans_status() === FALSE) {
					echo json_encode([
						"csrf_hash" => $this->security->get_csrf_hash(),
						"message"   => "error"
					]);
				} else {
					echo json_encode([
						"csrf_hash" => $this->security->get_csrf_hash(),
						"message"   => "success"
					]);
				}
			} else {
				echo json_encode([
					"csrf_hash" => $this->security->get_csrf_hash(),
					"message"   => "error"
				]);
			}
		}

		public function restoredata() {
			$this->ortyd->access_check_update($this->module);

			$id = $this->security->xss_clean($this->input->post('id', true) ?? '');
			if (empty($id)) {
				echo json_encode([
					"csrf_hash" => $this->security->get_csrf_hash(),
					"message"   => "error"
				]);
				return;
			}

			$fieldKey = ($this->field == 'slug') ? $this->field : $this->tableid;

			$this->db->where($fieldKey, $id)->where('active', 0);
			$query = $this->db->get($this->tabledb)->result();

			if ($query) {
				$this->db->trans_start();
				$data = [
					'active'     => 1,
					'modifiedid' => $this->session->userdata('userid'),
					'modified'   => date('Y-m-d H:i:s')
				];
				$this->db->where($fieldKey, $id)->update($this->tabledb, $data);
				$this->db->trans_complete();

				if ($this->db->trans_status() === FALSE) {
					echo json_encode([
						"csrf_hash" => $this->security->get_csrf_hash(),
						"message"   => "error"
					]);
				} else {
					echo json_encode([
						"csrf_hash" => $this->security->get_csrf_hash(),
						"message"   => "success"
					]);
				}
			} else {
				echo json_encode([
					"csrf_hash" => $this->security->get_csrf_hash(),
					"message"   => "error"
				]);
			}
		}

		public function select2() {
			$table        = $this->security->xss_clean($this->input->post('table', true));
			$id           = $this->security->xss_clean($this->input->post('id', true));
			$name         = $this->security->xss_clean($this->input->post('name', true));
			$reference    = $this->security->xss_clean($this->input->post('reference', true) ?? '') ?? null;
			$reference_id = $this->security->xss_clean($this->input->post('reference_id', true) ?? '') ?? null;
			$q            = $this->security->xss_clean($this->input->post('q', true) ?? '') ?? '';

			// Validasi wajib: hindari query tanpa nama kolom atau tabel
			if (empty($table) || empty($id) || empty($name)) {
				echo json_encode([
					'results' => [],
					'message' => 'Parameter tidak lengkap',
					'csrf_hash' => $this->security->get_csrf_hash()
				]);
				return;
			}

			// Eksekusi jika valid
			$results = $this->ortyd->select2custom($id, $name, $q, $table, $reference, $reference_id);

			// Pastikan hasil dalam bentuk JSON yang aman
			header('Content-Type: application/json');
			echo $results;
		}
		
		
		public function saveEvidence($data_id, $urlparent){
			return $this->m_model_master->saveEvidence($data_id, $urlparent);
		}
		
		public function proses_upload(){
			echo $this->m_model_master->proses_upload();
		}
		
		public function getcover(){
			$table = $this->input->post('table') ?? $this->urlparent;
			echo $this->m_model_master->getcover($table);
		}
		
		public function deleteFile(){
			$this->ortyd->access_check_update($this->module);
			echo $this->m_model_master->deleteFile();
		}
		
		/**
		 * Get popup form using the same v_data_form.php file
		 */
		public function get_popup_form() {
			$id = $this->security->xss_clean($this->input->post('id', true));
			$mode = $this->security->xss_clean($this->input->post('mode', true)); // 'create' or 'edit'
			
			if ($mode == 'edit') {
				$this->ortyd->access_check_update($this->module);
				$id = $this->ortyd->select2_getname($id, $this->tabledb, $this->field, $this->tableid);
			} else {
				$this->ortyd->access_check_insert($this->module);
				$id = 0;
			}
			
			$data['title'] = ($mode == 'edit') ? 'Edit '.$this->titlechilddb : 'Buat '.$this->titlechilddb;
			$data['id'] = $id;
			$data['mode'] = $mode;
			$data['tableview'] = $this->tableview;
			$data['module'] = $this->module;
			$data['module'] = $this->module;
			$data['modeldb'] = $this->m_model_data;
			$data['exclude'] = $this->exclude;
			$data['headurl'] = $this->headurldb;
			$data['action'] = base_url().$this->actionurl.'/'.$id;
			$data['is_popup'] = true; // Flag untuk popup mode
			
			if ($mode == 'edit' && $id > 0) {
				$data['datarow'] = $this->m_model_data->get_data_byid($id, $this->tabledb, $this->tableid);
			} else {
				$data['datarow'] = null;
			}
			
			// Load form view yang sama dengan tambahan wrapper popup
			$formHtml = $this->load->view($this->urlparent.'/v_data_form', $data, true);
			
			// Wrap form dalam modal structure
			$modalHtml = $this->wrapFormInModal($formHtml, $data['title'], $id);
			
			echo json_encode([
				'status' => 'success',
				'html' => $modalHtml,
				'csrf_hash' => $this->security->get_csrf_hash()
			]);
		}

		/**
		 * Wrap form HTML in modal structure
		 */
		private function wrapFormInModal($formHtml, $title, $id) {
			return '
			<div class="modal fade" id="popupFormModal" tabindex="-1" role="dialog" aria-labelledby="popupFormModalLabel" aria-hidden="true" data-backdrop="true" data-keyboard="true">
				<div class="modal-dialog modal-xl" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title" id="popupFormModalLabel">
								<i class="fas fa-' . ($id > 0 ? 'edit' : 'plus') . '"></i> ' . $title . '
							</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true" style="color: #fff;padding: 10px;border-radius: 10px;">X</span>
							</button>
						</div>
						<div class="modal-body popup-form-container" style="max-height: 70vh; overflow-y: auto;">
							' . $formHtml . '
						</div>
					</div>
				</div>
			</div>
			
			<script>
			$(document).ready(function() {
				// Initialize popup modal events
				initializePopupModalEvents();
				
				// Initialize form components after modal is shown
				$("#popupFormModal").on("shown.bs.modal", function() {
					console.log("Modal shown, initializing components...");
					initializePopupComponents();
				});
				
				// Cleanup when modal is hidden
				$("#popupFormModal").on("hidden.bs.modal", function() {
					console.log("Modal hidden, cleaning up...");
					cleanupPopupComponents();
				});
			});
			
			function overrideFormSubmission() {
						// Find the submit button and override its click event
						$(".popup-form-container").find("button[id*=\'kt_docs_formvalidation_text_submit\']").off("click").on("click", function(e) {
							e.preventDefault();
							submitPopupForm();
						});
					}
					
					function submitPopupForm() {
						var form = $(".popup-form-container form");
						var formData = new FormData(form[0]);
						
						// Validation
						var requiredFields = form.find("[required]");
						var isValid = true;
						var errorMessages = [];
						
						requiredFields.each(function() {
							if (!$(this).val()) {
								isValid = false;
								var label = $(this).closest(".form-group, .col-lg-6, .col-lg-12").find("label").text().replace("*", "").trim();
								errorMessages.push(label);
							}
						});
						
						if (!isValid) {
							Swal.fire({
								icon: "warning",
								title: "Data Belum Lengkap",
								html: "Harap lengkapi field berikut:<br>" + errorMessages.join("<br>"),
								confirmButtonText: "OK"
							});
							return;
						}
						
						// Show loading
						Swal.fire({
							title: "Menyimpan...",
							text: "Sedang memproses data",
							allowOutsideClick: false,
							didOpen: () => {
								Swal.showLoading();
							}
						});
						
						$.ajax({
							url: form.attr("action"),
							type: "POST",
							data: formData,
							processData: false,
							contentType: false,
							dataType: "json",
							success: function(response) {
								if (response.status == "success") {
									Swal.fire({
										icon: "success",
										title: "Berhasil!",
										text: "Data berhasil disimpan dan dikirim",
										timer: 2000,
										showConfirmButton: false
									}).then(() => {
										$("#popupFormModal").modal("hide");
										// Refresh datatable
										if (typeof table !== "undefined") {
											table.ajax.reload();
										}
									});
								} else {
									Swal.fire({
										icon: "error",
										title: "Error!",
										text: "Data tidak berhasil disimpan: " + (response.errors || "Unknown error"),
										confirmButtonText: "OK"
									});
								}
								
								// Update CSRF token
								if (response.csrf_hash) {
									updateCsrfToken(response.csrf_hash);
								}
							},
							error: function(jqxhr, status, error) {
								console.error("Request failed: " + error);
								
								Swal.fire({
									icon: "error",
									title: "Error!",
									text: "Terjadi kesalahan saat menyimpan data",
									confirmButtonText: "OK"
								});
							}
						});
					}
			
			function initializePopupModalEvents() {
				// Multiple ways to close modal
				
				// 1. Close button in header
				$(document).on("click", "#popupFormModal .close", function() {
					console.log("Header close button clicked");
					closePopupModal();
				});
				
				// 2. Cancel button in footer
				$(document).on("click", "#closePopupBtn", function() {
					console.log("Cancel button clicked");
					closePopupModal();
				});
				
				// 3. Submit button
				$(document).on("click", "#submitPopupFormBtn", function() {
					console.log("Submit button clicked");
					overrideFormSubmission();
				});
				
				// 4. Escape key
				$(document).on("keydown", function(e) {
					if (e.keyCode === 27 && $("#popupFormModal").is(":visible")) {
						console.log("Escape key pressed");
						closePopupModal();
					}
				});
				
				// 5. Click outside modal (backdrop)
				$(document).on("click", "#popupFormModal", function(e) {
					if (e.target === this) {
						console.log("Backdrop clicked");
						closePopupModal();
					}
				});
				
				// 6. Handle modal backdrop click with Bootstrap way
				$("#popupFormModal").on("click", "[data-dismiss=\"modal\"]", function() {
					console.log("Bootstrap dismiss clicked");
					closePopupModal();
				});
			}
			
			function closePopupModal() {
				console.log("Closing popup modal...");
				
				// Check if there are unsaved changes
				if (hasUnsavedChanges()) {
					Swal.fire({
						title: "Ada perubahan yang belum disimpan",
						text: "Apakah Anda yakin ingin menutup form ini?",
						icon: "warning",
						showCancelButton: true,
						confirmButtonColor: "#d33",
						cancelButtonColor: "#3085d6",
						confirmButtonText: "Ya, tutup",
						cancelButtonText: "Batal"
					}).then((result) => {
						if (result.isConfirmed) {
							forceCloseModal();
						}
					});
				} else {
					forceCloseModal();
				}
			}
			
			function forceCloseModal() {
				console.log("Force closing modal...");
				
				// Cleanup components first
				cleanupPopupComponents();
				
				// Hide modal with Bootstrap method
				$("#popupFormModal").modal("hide");
				
				// If Bootstrap method fails, force remove
				setTimeout(function() {
					if ($("#popupFormModal").length > 0) {
						console.log("Bootstrap hide failed, force removing...");
						$("body").removeClass("modal-open");
						$(".modal-backdrop").remove();
						$("#popupFormModal").remove();
					}
				}, 500);
			}
			
			function hasUnsavedChanges() {
				// Check if form has been modified
				var hasChanges = false;
				$("#popupFormModal form input, #popupFormModal form textarea, #popupFormModal form select").each(function() {
					if ($(this).is(":text, textarea") && $(this).val().trim() !== "") {
						hasChanges = true;
						return false;
					}
					if ($(this).is("select") && $(this).val() !== null && $(this).val() !== "") {
						hasChanges = true;
						return false;
					}
				});
				return hasChanges;
			}
			
			function cleanupPopupComponents() {
				console.log("Cleaning up popup components...");
				
				// Destroy Select2 instances
				$("#popupFormModal .select2-hidden-accessible").each(function() {
					try {
						$(this).select2("destroy");
						console.log("Select2 destroyed for:", $(this).attr("name"));
					} catch (e) {
						console.warn("Failed to destroy Select2:", e);
					}
				});
				
				// Destroy Summernote instances
				$("#popupFormModal .note-editor").each(function() {
					try {
						$(this).summernote("destroy");
						console.log("Summernote destroyed");
					} catch (e) {
						console.warn("Failed to destroy Summernote:", e);
					}
				});
				
				// Destroy Datepicker instances
				$("#popupFormModal .datepicker").each(function() {
					try {
						$(this).datepicker("destroy");
						console.log("Datepicker destroyed");
					} catch (e) {
						console.warn("Failed to destroy Datepicker:", e);
					}
				});
				
				// Remove any event listeners
				$(document).off("keydown.popupModal");
				$(document).off("click.popupModal");
			}
			
			function initializePopupComponents() {
				// Initialize Select2 dengan delay untuk memastikan modal sudah fully rendered
				setTimeout(function() {
					initializePopupSelect2();
				}, 100);
				
				// Initialize other components
				initializePopupSummernote();
				initializePopupDatepickers();
				initializePopupCurrency();
				
				console.log("All popup components initialized");
			}
			
			function initializePopupSelect2() {
				console.log("Initializing Select2 for popup...");
				
				$("#popupFormModal .select2-popup").each(function() {
					var $select = $(this);
					var fieldName = $select.attr("name");
					
					// Skip if already initialized
					if ($select.hasClass("select2-hidden-accessible")) {
						console.log("Select2 already initialized for:", fieldName);
						return;
					}
					
					try {
						var config = {
							dropdownParent: $("#popupFormModal"),
							width: "100%",
							placeholder: "Pilih...",
							allowClear: true,
							escapeMarkup: function(markup) { return markup; }
						};
						
						$select.select2(config);
						console.log("Select2 initialized for:", fieldName);
						
						// Fix z-index
						$select.on("select2:open", function() {
							$(".select2-dropdown").css("z-index", 99999);
						});
						
					} catch (e) {
						console.error("Failed to initialize Select2 for " + fieldName + ":", e);
					}
				});
			}
			
			function initializePopupSummernote() {
				$("#popupFormModal .summernote").each(function() {
					if (!$(this).hasClass("note-editor")) {
						$(this).summernote({
							height: 150,
							toolbar: [
								["style", ["style"]],
								["font", ["bold", "italic", "underline", "clear"]],
								["para", ["ul", "ol", "paragraph"]],
								["table", ["table"]],
								["insert", ["link"]],
								["view", ["fullscreen", "codeview"]]
							]
						});
					}
				});
			}
			
			function initializePopupDatepickers() {
				$("#popupFormModal .datetime").each(function() {
					if (!$(this).hasClass("hasDatepicker")) {
						$(this).daterangepicker({
									singleDatePicker: true,
									showDropdowns: true,
									minYear: 1901,
									maxYear: parseInt(moment().format("YYYY"),12),
									locale: {
									  format: "YYYY-MM-DD"
									}
								}, function(start, end, label) {
									
								}
							);
					}
				});
				
				$("#popupFormModal .datepickertime").each(function() {
					if (!$(this).data("DateTimePicker")) {
						$(this).daterangepicker({
								singleDatePicker: true,
								timePicker: true,
								showDropdowns: true,
								timePicker24Hour:true,
								opens: "auto",
								drops: "auto",
								parentEl: ".swal2-popup",
								minYear: 1901,
								maxYear: parseInt(moment().format("YYYY"),12),
								locale: {
									format: "YYYY-MM-DD HH:mm:00"
								}
							}, function(start, end, label) {
											
							});
					}
				});
			}
			
			function initializePopupCurrency() {
				$("#popupFormModal .numeric-rp").off("input.currency").on("input.currency", function() {
					var value = $(this).val().replace(/[^0-9]/g, "");
					$(this).val(formatRupiah(value));
				});
			}
			

			
			function submitPopupFormCustom() {
				// Custom form submission logic here if needed
				console.log("Custom popup form submission");
				// Implementation will be added based on your form structure
			}
			
			function formatRupiah(angka) {
				var number_string = angka.toString();
				var split = number_string.split(",");
				var sisa = split[0].length % 3;
				var rupiah = split[0].substr(0, sisa);
				var ribuan = split[0].substr(sisa).match(/\\d{3}/gi);
				
				if (ribuan) {
					separator = sisa ? "." : "";
					rupiah += separator + ribuan.join(".");
				}
				
				rupiah = split[1] != undefined ? rupiah + "," + split[1] : rupiah;
				return "Rp " + rupiah;
			}
			</script>
			
			<style>

			</style>';
		}
		
		
}
