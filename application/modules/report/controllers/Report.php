<?php
//CONTROLLER BY HANAFI GINTING

defined('BASEPATH') OR exit('No direct script access allowed');

class Report extends MX_Controller {

		//CONFIG VARIABLE
		private $urlparent = 'report'; //NAME TABLE 
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

		public function __construct()
		{
			
			
			$this->viewname = $this->urlparent.'/views/v_data';
			$this->viewformname = $this->urlparent.'/views/v_data_form';
			$this->tabledb = 'vw_report_data_pengajuan';
			$this->tableid = 'vw_report_data_pengajuan.id';
			$this->titlechilddb = strtoupper($this->urlparent);
			$this->headurldb = $this->urlparent;
			$this->actionurl = $this->urlparent.'/actiondata';
			$this->module = $this->urlparent;
			$this->modeldb = 'm_data';
			
			$this->load->model($this->modeldb,'m_model_data');
			$this->load->model('dashboard/m_dashboard','m_model_master');
			$this->titlechilddb = $this->ortyd->getmodulename($this->module);
			
			$this->ortyd->session_check();
			$this->ortyd->access_check($this->module);
		}
		
		public function index()
		{
			$tipe = 1;
			
			$this->titlechilddb = 'Laporan Pengajuan';
			
			if(isset($_GET['tipe'])){
				if($_GET['tipe'] == 2){
					$this->tabledb = 'vw_data_survei_prov';
					$this->titlechilddb = 'Daftar Survei Per Provinsi';
				}else{
					$this->tabledb = 'vw_data_survei';
					$this->titlechilddb = 'Daftar Survei';
				}
			}else{
				$this->tabledb = 'vw_data_survei';
				$this->titlechilddb = 'Daftar Survei';
			}
			$data['title'] = $this->titlechilddb;
			$data['module'] = $this->module;
			$data['tabledb'] = $this->tabledb;
			$data['identity_id'] = $this->identity_id;
			$data['slug_indentity'] = $this->slug_indentity;
			$data['exclude_table'] = $this->exclude_table;
			$data['headurl'] = $this->headurldb;
			$data['linkdata'] = $this->urlparent.'/get_data';
			$data['linkcreate'] = $this->urlparent.'/createdata';
			$this->template->load('main',$this->viewname, $data);
		}
		
				
		public function get_data() {
			$activateddata = ['Inactive', 'Active'];
			// Amankan dan validasi input
			$table = $this->security->xss_clean($this->input->post('table', true) ?? '');
			$searchfield = $this->security->xss_clean($this->input->post('select2search', true) ?? '');
			$activeStatus = $this->security->xss_clean($this->input->post('active', true)  ?? '');
			$start = (int) ($_POST['start'] ?? 0);
			$draw = (int) ($_POST['draw'] ?? 1);

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
			
			// Get filters from frontend
			$filters = $this->input->post('filters', true) ?? [];
			$filters = $this->security->xss_clean($filters);

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

			//$wherecolumn[] = $table . '.active';
			//$wheredetail[] = $activeStatus;
			
			if($this->input->post('active',true) == 1){
				array_push($wherecolumn, $table.'.active');
				array_push($wheredetail, 1);
			}else{
				array_push($wherecolumn, $table.'.active');
				array_push($wheredetail, 0);
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

				$editdata = '<div class="menu-item px-3"><a class="dropdown-item d-flex align-items-center gap-2" href="' . base_url() . $this->urlparent . '/editdata/' . $identity_id . '"><i class="fa fa-eye text-primary mt-1"></i> View Detail</a></div>';
				if ($this->ortyd->access_check_update_data($this->module)) {
					$editdata = '';
					
					if($this->session->userdata('group_id') == 1 || $this->session->userdata('group_id') == 2){
						//$restoredata = '<div class="menu-item px-3"><a href="javascript:;" class="dropdown-item" onClick="restoredata('.$uuid.')"><i class="fa fa-trash"></i> Restore</a></div>';
					}else{
						$restoredata = '';
					}
				}

				if ($this->ortyd->access_check_delete_data($this->module)) {
					if($this->session->userdata('group_id') == 1 || $this->session->userdata('group_id') == 2){
						//$deletedata = '<div class="menu-item px-3"><a href="javascript:;" class="dropdown-item" onClick="deletedata('.$uuid.')"><i class="fa fa-trash"></i> Delete</a></div>';
					}else{
						$deletedata = '';
					}
					
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
				
				if($this->session->userdata('group_id') == 3){
					$row[] = '';
				}else{
					$row[] = $action;
				}
			
				

				// Isi kolom data
				if ($query_column) {
					$alias = 0;
					foreach ($query_column as $rowsdata) {
						$table_references = $this->ortyd->get_table_reference($table, $rowsdata['name']);

						if ($table_references) {
							$row[] = $rows[$table_references[0] . '_' . $alias . '_' . $table_references[2]] ?? '';
						} elseif ($rowsdata['name'] == 'link') {
							$row[] = '<a href="' . base_url($table) . $identity_id . '">' . htmlspecialchars($rows[$rowsdata['name']] ?? '') . '</a>';
						} elseif ($rowsdata['name'] == 'parameters') {
							$variable = $rows[$rowsdata['name']];
							if (!empty($variable)) {
								// pecah berdasarkan " || "
								$items = explode(' || ', $variable);
								$list = '<ul style="padding-left:15px; margin:0;">';
								foreach ($items as $item) {
									// pecah per detail " | "
									$parts = explode(' | ', $item);
									$detail = [];
									foreach ($parts as $p) {
										// kalau ada ":" → bold label
										if (strpos($p, ':') !== false) {
											[$label, $val] = explode(':', $p, 2);
											$detail[] = '<b>' . trim($label) . ':</b> ' . trim($val);
										} else {
											$detail[] = $p;
										}
									}
									$list .= '<li>' . implode(' | ', $detail) . '</li>';
								}
								$list .= '</ul>';
								$variable = $list;
							}
							$row[] = $variable;
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

		public function removedata() {
			$tabledb = 'data_laporan';
			
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
				$this->db->where($fieldKey, $id)->update($tabledb, $data);
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
			$tabledb = 'data_laporan';
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
				$this->db->where($fieldKey, $id)->update($tabledb, $data);
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
			$reference    = $this->security->xss_clean($this->input->post('reference', true)) ?? null;
			$reference_id = $this->security->xss_clean($this->input->post('reference_id', true)) ?? null;
			$q            = $this->security->xss_clean($this->input->post('q', true)) ?? '';

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
			echo $this->m_model_master->getcover($this->urlparent);
		}
		
		public function deleteFile(){
			$this->ortyd->access_check_update($this->module);
			echo $this->m_model_master->deleteFile();
		}
		
		
}
