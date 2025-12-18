<?php
//CONTROLLER BY HANAFI GINTING

defined('BASEPATH') OR exit('No direct script access allowed');

class Data_message extends MX_Controller {

		//CONFIG VARIABLE
		private $urlparent = 'data_message'; //NAME TABLE 
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
			$this->tabledb = $this->urlparent;
			$this->tableid = $this->urlparent.'.id';
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
					$editdata = '<div class="menu-item px-3"><a class="dropdown-item d-flex align-items-center gap-2" href="' . base_url() . $this->urlparent . '/editdata/' . $identity_id . '"><i class="fa fa-edit text-primary mt-1"></i> Edit</a></div>';
					$restoredata = '<div class="menu-item px-3"><a href="javascript:;" class="dropdown-item d-flex align-items-center gap-2" onClick="restoredata(' . $uuid . ')"><i class="fa fa-undo text-warning"></i> Restore</a></div>';
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
				$row[] = $action;

				// Isi kolom data
				if ($query_column) {
					$alias = 0;
					foreach ($query_column as $rowsdata) {
						$table_references = $this->ortyd->get_table_reference($table, $rowsdata['name']);

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
			redirect('404','refresh');
			$this->ortyd->access_check_insert($this->module);
			$data['title'] = 'Buat '.$this->titlechilddb;;
			$data['id'] = null;
			$data['module'] = $this->module;
			$data['modeldb'] = $this->m_model_data;
			$data['exclude'] = $this->exclude;
			$data['data'] = null;
			$data['headurl'] = $this->headurldb;
			//$data['action'] = base_url().$this->actionurl.'/0';
			$data['action'] = '#';
			$this->template->load('main',$this->viewformname, $data);
		}
		
		public function editdata($ID)
		{
			$this->ortyd->access_check_update($this->module);
			$ID = $this->ortyd->select2_getname($ID,$this->tabledb,$this->field,$this->tableid);
			$data['title'] = 'View '.$this->titlechilddb;
			$data['id'] = $ID;
			$data['module'] = $this->module;
			$data['modeldb'] = $this->m_model_data;
			$data['exclude'] = $this->exclude;
			$data['headurl'] = $this->headurldb;
			$data['datarow'] = $this->m_model_data->get_data_byid($data['id'], $this->tabledb, $this->tableid);
			//$data['action'] = base_url().$this->actionurl.'/'.$data['id'];
			$data['action'] = '#';
			$this->template->load('main',$this->viewformname, $data);
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
