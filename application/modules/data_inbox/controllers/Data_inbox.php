<?php
//CONTROLLER BY HANAFI GINTING

defined('BASEPATH') OR exit('No direct script access allowed');

class Data_inbox extends MX_Controller {

		//CONFIG VARIABLE
		private $urlparent = 'data_inbox'; //NAME TABLE 
		private $identity_id = 'slug'; //IDENTITY TABLE
		private $field = 'slug'; // IDENTITY FROM NAME FOR GET ID
		private $slug_indentity = 'field'; //NAME FIELD 
		private $sorting = 'modified'; // SORT FOR VIEW
		private $exclude = array('is_read','is_wa','is_email','email_date','note','data_id','history_id','status_id','created','modified','createdid','modifiedid','id','active','slug');
		private $exclude_table = array('is_read','is_wa','is_email','email_date','note','data_id','history_id','status_id','created','modified','createdid','modifiedid','id','active','slug');
		//END CONFIG VARIABLE
		
		private $viewname;
		private $viewnamedetail;
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
			$this->viewnamedetail = $this->urlparent.'/views/v_data_view';
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
			$data['slug_indentity'] = $this->slug_indentity;
			$data['identity_id'] = $this->identity_id;
			$data['exclude_table'] = $this->exclude_table;
			$data['headurl'] = $this->headurldb;
			$data['linkdata'] = $this->urlparent.'/get_data';
			$data['linkcreate'] = $this->urlparent.'/createdata';
			$this->template->load('main',$this->viewname, $data);
		}
		
		public function chat() {
			$data['title'] = 'Private Chat';
			$data['module'] = $this->module;
			$data['tabledb'] = $this->tabledb;
			$data['identity_id'] = $this->identity_id;
			$data['exclude_table'] = $this->exclude_table;
			$data['headurl'] = $this->headurldb;
			$data['linkdata'] = $this->urlparent.'/get_data';
			$data['linkcreate'] = $this->urlparent.'/createdata';
			$this->template->load('main',$this->urlparent.'/views/v_data_chat', $data);
		}
		
		public function getUserList() {
			// Ambil data id dan fullname dari tabel users_data
			$this->db->select('id, username, fullname as name');
			$this->db->where('active', 1);
			$this->db->where('id !=', $this->session->userdata('userid'));

			// Mengambil waktu 10 menit ke belakang
			$ten_minutes_ago = date('Y-m-d H:i:s', strtotime('-30 minutes'));
			$this->db->where('online_date >=', $ten_minutes_ago); // Menampilkan data dengan online_date lebih dari atau sama dengan 10 menit yang lalu

			$this->db->from('users_data');
			$query = $this->db->get();

			// Periksa apakah ada hasil
			if ($query->num_rows() > 0) {
				$users = $query->result_array(); // Mengembalikan hasil dalam bentuk array
			} else {
				$users = []; // Jika tidak ada data
			}
			
			echo json_encode(['users' => $users]);
		}
		
		function get_data(){

			$activateddata = array('Inactive','Active');
			$table = $this->input->post('table',true);
			$sorting = $this->sorting;
			$selectnya = array();
			$jointable = array();
			$joindetail = array();
			$joinposition = array();
			$wherecolumn = array();
			$wheredetail = array();
			
			$exclude = $this->exclude_table;
			$query_column = $this->ortyd->getviewlistcontrol($table, $this->module, $exclude);
			if($query_column){
				$ordernya = array(null);
				$searchnya = array();
				$alias = 0;
				foreach($query_column as $rowsdata){
					$table_references = null;
					$table_references = $this->ortyd->get_table_reference($table,$rowsdata['name']);
					
					if($table_references != null){
						array_push($ordernya,$table_references[0].'_'.$alias.'.'.$table_references[2]);
						array_push($searchnya,$table_references[0].'_'.$alias.'.'.$table_references[2]);
						array_push($selectnya,$table_references[0].'_'.$alias.'.'.$table_references[2]." as ".$table_references[0].'_'.$table_references[2]);
						
						$joinnya = array_search($table_references[0],$selectnya);
						if($joinnya == '' || $joinnya == null){
							array_push($jointable,$table_references[0].' as '.$table_references[0].'_'.$alias);
							array_push($joindetail,$table.'.'.$rowsdata['name'].' = '.$table_references[0].'_'.$alias.'.'.$table_references[1]);
							array_push($joinposition,'left');
						}

					}else{
						array_push($ordernya,$table.'.'."`".$rowsdata['name']."`");
						array_push($searchnya,$table.'.'."`".$rowsdata['name']."`");
					}
					
					$alias++;
				}
				array_push($ordernya,null);
				$column_order = $ordernya;
				$column_search = $searchnya;
			}else{
				$column_order = array(null);
				$column_search = array(null);
			}
			
			$order = array($table.'.'.$sorting => 'DESC');
			$selectnya = implode(",", $selectnya);
			if($selectnya != ''){
				$selectnya = ','.$selectnya;
			}
			$select = $table.'.*'.$selectnya;
			
			if( $this->input->post('active',true) == 3){
				
				array_push($wherecolumn, $table.'.is_read');
				array_push($wheredetail, 0);
				
					array_push($wherecolumn, $table.'.active');
				array_push($wheredetail, 1);
			
			}elseif( $this->input->post('active',true) == 4){
				
				array_push($wherecolumn, $table.'.is_read');
				array_push($wheredetail, 1);
				
					array_push($wherecolumn, $table.'.active');
				array_push($wheredetail, 1);
			
			}else{
				array_push($wherecolumn, $table.'.active');
				array_push($wheredetail, $this->input->post('active',true));
			}
			
			array_push($wherecolumn, $table.'.to_id');
			array_push($wheredetail, $this->session->userdata('userid'));

			$groupby = array();
		
			$list = $this->ortyd->get_datatables($table,$column_order,$column_search,$order,$select,$jointable,$joindetail,$joinposition, $wherecolumn,$wheredetail,$groupby);
			$data = array();
			$no = $this->input->post('start',true);
			foreach ($list as $rows) {
				$rows = (array) $rows;
				$no++;
				$row = array();
				//$row[] = $no;
				
				$identity_id = $rows[$this->identity_id];
				$uuid = "'". $rows[$this->identity_id]."'";
				
				if($this->ortyd->access_check_update_data($this->module)){

					$editdata = '<div class="menu-item px-3"><a class="dropdown-item d-flex align-items-center gap-2" href="'.base_url().$this->urlparent.'/viewdata/'.$identity_id.'"><i class="fa fa-eye text-info mt-1"></i> View Detail</a></div> ';
									
					$restoredata = '<div class="menu-item px-3"><a href="javascript:;" class="dropdown-item d-flex align-items-center gap-2" onClick="restoredata('.$uuid.')"><i class="fa fa-undo text-warning"></i> <span>Restore</span></a></div>';
						
				}else{
					$editdata = '';
					$restoredata = '';
				}
					
					
				if($this->ortyd->access_check_delete_data($this->module)){
					
					$deletedata = '<div class="menu-item px-3"><a href="javascript:;" class="dropdown-item d-flex align-items-center gap-2" onClick="deletedata('.$uuid.')"><i class="fa fa-trash text-danger mt-1"></i> Delete</a></div>';
						
				}else{
					if($editdata == ''){
						$deletedata = '';
					}else{
						$deletedata = '';
					}
					
				}
				
				if($rows['active'] == 1){
					$status = '<span class="badge badge-light-success">'.$activateddata[$rows['active']].'</span>';
					$action = '
				
						<a href="#" class="btn btn-sm btn-primary btn-active-light-primary btn-flex btn-center btn-sm menu-dropdown" data-kt-menu-trigger="click" data-kt-menu-placement="top-end">
							...                    
						</a>
						<!--begin::Menu-->
						<div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
							
							<!--begin::Menu item-->
							'.$editdata.'
							'.$deletedata.'
							<!--end::Menu item-->
								
						</div>
						<!--end::Menu-->
				
					';
				}else{
					$status = '<span class="badge badge-light-danger">'.$activateddata[$rows['active']].'</span>';
					$action = '
					
						<a href="#" class="btn btn-sm btn-primary btn-active-light-primary btn-flex btn-center btn-sm menu-dropdown" data-kt-menu-trigger="click" data-kt-menu-placement="top-end">
							...                    
						</a>
						<!--begin::Menu-->
						<div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
							
							<!--begin::Menu item-->
							'.$restoredata.'
							<!--end::Menu item-->
								
						</div>
						<!--end::Menu-->
						
					';
					
				}
				
				$row[] = $action;
				if($query_column){
					foreach($query_column as $rowsdata){
						$table_references = null;
						$table_references = $this->ortyd->get_table_reference($table,$rowsdata['name']);
						if($table_references != null){
							$variable = $rows[$table_references[0].'_'.$table_references[2]];
							$row[] = $variable;
						}elseif($rowsdata['name'] == 'link'){
							$variable = '<a href="'.base_url($table).$identity_id.'">'.$rows[$rowsdata['name']].'</a>';
							$row[] = $variable;
						}else{
							$variable = $rows[$rowsdata['name']];
							$variable = $this->ortyd->getFormatData($table,$rowsdata['name'], $variable);
							$row[] = $variable;
						}
					}
				}
				//$row[] = $status;

				$data[] = $row;
			}
			
	 
			$output = array(
				"draw" => $this->input->post('draw',true),
				"recordsTotal" => $this->ortyd->count_filtered($table,$column_order,$column_search,$order,$select,$jointable,$joindetail,$joinposition, $wherecolumn,$wheredetail,$groupby),
				"recordsFiltered" => $this->ortyd->count_filtered($table,$column_order,$column_search,$order,$select,$jointable,$joindetail,$joinposition, $wherecolumn,$wheredetail,$groupby),"csrf_hash" => $this->security->get_csrf_hash(),
				"data" => $data,
			);
			
			echo json_encode($output);
		}
		
		public function createdata()
		{
			$this->ortyd->access_check_insert($this->module);
			$data['title'] = 'Buat '.$this->titlechilddb;
			$data['id'] = null;
			$data['module'] = $this->module;
			$data['modeldb'] = $this->m_model_data;
			$data['exclude'] = $this->exclude;
			$data['data'] = null;
			$data['headurl'] = $this->headurldb;
			$data['action'] = base_url().$this->actionurl.'/0';
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
			$data['action'] = base_url().$this->actionurl.'/'.$data['id'];
			$this->template->load('main',$this->viewformname, $data);
		}
		
		public function viewdata($ID)
		{
			$this->ortyd->access_check_update($this->module);
			$to_id = $this->ortyd->select2_getname($ID,$this->tabledb,$this->field,'to_id');
			$ID = $this->ortyd->select2_getname($ID,$this->tabledb,$this->field,$this->tableid);
			$data['title'] = 'View '.$this->titlechilddb;
			$data['id'] = $ID;
			if($to_id == $this->session->userdata('userid')){
				$read = $this->m_model_data->updatestatusread($ID);	
			}

			$data['module'] = $this->module;
			$data['modeldb'] = $this->m_model_data;
			$data['exclude'] = $this->exclude;
			$data['headurl'] = $this->headurldb;
			$data['datarow'] = $this->m_model_data->get_data_byid($data['id'], $this->tabledb, $this->tableid);
			$data['action'] = base_url().$this->actionurl.'/'.$data['id'];
			$this->template->load('main',$this->viewnamedetail, $data);
		}
		
		public function actiondata($id){
			
			$data = array();
			$exclude = $this->exclude;
			$query_column = $this->ortyd->query_column($this->tabledb, $exclude);
			if($query_column){
				if($id != '0'){
					$this->ortyd->access_check_update($this->module);
					if($query_column){
						foreach($query_column as $rows_column){
							$tipe_data = $this->ortyd->getTipeData($this->module,$rows_column['name']);
							if($tipe_data == 'CURRENCY' || $rows_column["name"] == 'nilai'){
								$dataisi = $this->input->post($rows_column["name"],true);
								$dataisi = $this->ortyd->unformatrp($dataisi);
								if($dataisi == ''){
									$dataisi = null;
								}
								$data_array = array($rows_column["name"] => $dataisi);
								$data = array_merge($data,$data_array);
							}else{
								$dataisi = $this->input->post($rows_column["name"],true);
								if($dataisi == ''){
									$dataisi = null;
								}
								$data_array = array($rows_column["name"] => $dataisi);
								$data = array_merge($data,$data_array);
							}
						}

						$data = array_merge($data,
							array('active' 			=> 1),
							array('modifiedid'		=> $this->session->userdata('userid')),
							array('modified'		=> date('Y-m-d H:i:s'))
						);
						
						//BEGIN ADDITIONAL
						//$string = $this->input->post('name',true);
						//$slug = $this->ortyd->sanitize($string,$this->tabledb);
						//$data = array_merge($data,
							//array('slug' 	=> $slug)
						//);
						//END ADDITIONAL
						
					}
				
					
					if(count($data) > 1){
						
						$this->db->trans_begin();
						
						$this->db->where($this->tableid, $id);
						$update = $this->db->update($this->tabledb, $data);
						
						$this->db->trans_complete();
						if ($this->db->trans_status() === FALSE){
							$this->db->trans_rollback();
							$result = array("csrf_hash" => $this->security->get_csrf_hash(),"status" => "error");
							echo json_encode($result);
						}else{
							$this->db->trans_commit();
							if($update){
								$this->saveEvidence($id, $this->urlparent);
								$result = array("csrf_hash" => $this->security->get_csrf_hash(),"status" => "success");
								echo json_encode($result);
							}else{
								$result = array("csrf_hash" => $this->security->get_csrf_hash(),"status" => "error");
								echo json_encode($result);
							}
						}
					}else{
						$result = array("csrf_hash" => $this->security->get_csrf_hash(),"status" => "error");
						echo json_encode($result);
					}
					
				}else{
					$this->ortyd->access_check_insert($this->module);
					if($query_column){
						foreach($query_column as $rows_column){
							$tipe_data = $this->ortyd->getTipeData($this->module,$rows_column['name']);
							if($tipe_data == 'CURRENCY' || $rows_column["name"] == 'nilai'){
								$dataisi = $this->input->post($rows_column["name"],true);
								$dataisi = $this->ortyd->unformatrp($dataisi);
								if($dataisi == ''){
									$dataisi = null;
								}
								$data_array = array($rows_column["name"] => $dataisi);
								$data = array_merge($data,$data_array);
							}else{
								$dataisi = $this->input->post($rows_column["name"],true);
								if($dataisi == ''){
									$dataisi = null;
								}
								$data_array = array($rows_column["name"] => $dataisi);
								$data = array_merge($data,$data_array);
							}
						}

						$data = array_merge($data,
							array('active' 			=> 1),
							array('createdid'		=> $this->session->userdata('userid')),
							array('created'			=> date('Y-m-d H:i:s')),
							array('modifiedid'		=> $this->session->userdata('userid')),
							array('modified'		=> date('Y-m-d H:i:s'))
						);
						
						//BEGIN ADDITIONAL
						$string = $this->input->post($this->slug_indentity,true);
						$slug = $this->ortyd->sanitize($string,$this->tabledb);
						$data = array_merge($data,
							array('slug' 	=> $slug)
						);
						//END ADDITIONAL
					}

					if(count($data) > 1){
						$this->db->trans_begin();
						
						$insert = $this->db->insert($this->tabledb, $data);
						$insert_id = $this->db->insert_id();
						
						$this->db->trans_complete();
						if ($this->db->trans_status() === FALSE){
							$this->db->trans_rollback();
							$result = array("csrf_hash" => $this->security->get_csrf_hash(),"status" => "error");
							echo json_encode($result);
						}else{
							$this->db->trans_commit();
							if($insert){
								$identity = $this->input->post($this->field,true);
								$this->saveEvidence($insert_id, $this->urlparent);
								$result = array("csrf_hash" => $this->security->get_csrf_hash(),"status" => "success");
								echo json_encode($result);
							}else{
								$result = array("csrf_hash" => $this->security->get_csrf_hash(),"status" => "error");
								echo json_encode($result);
							}
						}
					}else{
						$result = array("csrf_hash" => $this->security->get_csrf_hash(),"status" => "error");
						echo json_encode($result);
					}
				}
			}else{
				$result = array("csrf_hash" => $this->security->get_csrf_hash(),"status" => "error");
				echo json_encode($result);
			}
		}
		
		public function removedata(){
			$this->ortyd->access_check_delete($this->module);
			
			if($this->field == 'slug'){
				$id = $this->input->post('id',true);	
				$this->db->where($this->field,$id);
			}else{
				$id = $this->input->post('id',true);	
				$this->db->where($this->tableid,$id);
			}
			
			$this->db->where('active',1);
			$query = $this->db->get($this->tabledb);
			$query = $query->result_object();
			if($query){
				
				$this->db->trans_begin();
				
				$dataremove = array(
					'active' 			=> 0,
					'modifiedid'		=> $this->session->userdata('userid'),
					'modified'			=> date('Y-m-d H:i:s')
				);
										
				if($this->field == 'slug'){
					$this->db->where('slug', $id);
				}else{
					$this->db->where('id', $id);
				}
				
				$updateactive = $this->db->update($this->tabledb, $dataremove);
				
				$this->db->trans_complete();
				if ($this->db->trans_status() === FALSE){
					$this->db->trans_rollback();
					$result = array("csrf_hash" => $this->security->get_csrf_hash(),"message" => "error");
					echo json_encode($result);
				}else{
					if($updateactive){
						$result = array("csrf_hash" => $this->security->get_csrf_hash(),"message" => "success");
						echo json_encode($result);
					}else{
						$result = array("csrf_hash" => $this->security->get_csrf_hash(),"message" => "error");
						echo json_encode($result);
					}
				}
			}else{
				$result = array("csrf_hash" => $this->security->get_csrf_hash(),"message" => "error");
				echo json_encode($result);
			}

		}
		
		public function restoredata(){
			$this->ortyd->access_check_update($this->module);
			
			if($this->field == 'slug'){
				$id = $this->input->post('id',true);	
				$this->db->where($this->field,$id);
			}else{
				$id = $this->input->post('id',true);	
				$this->db->where($this->tableid,$id);
			}
			
			$this->db->where('active',0);
			$query = $this->db->get($this->tabledb);
			$query = $query->result_object();
			if($query){
				
				$this->db->trans_begin();
				
				$dataremove = array(
					'active' 			=> 1,
					'modifiedid'		=> $this->session->userdata('userid'),
					'modified'			=> date('Y-m-d H:i:s')
				);
										
				if($this->field == 'slug'){
					$this->db->where('slug', $id);
				}else{
					$this->db->where('id', $id);
				}
				
				$updateactive = $this->db->update($this->tabledb, $dataremove);
				
				$this->db->trans_complete();
				if ($this->db->trans_status() === FALSE){
					$this->db->trans_rollback();
					$result = array("csrf_hash" => $this->security->get_csrf_hash(),"message" => "error");
					echo json_encode($result);
				}else{
					if($updateactive){
						$result = array("csrf_hash" => $this->security->get_csrf_hash(),"message" => "success");
						echo json_encode($result);
					}else{
						$result = array("csrf_hash" => $this->security->get_csrf_hash(),"message" => "error");
						echo json_encode($result);
					}
				}
			}else{
				$result = array("csrf_hash" => $this->security->get_csrf_hash(),"message" => "error");
				echo json_encode($result);
			}

		}
		
		
		public function select2() {
			
			$table = $this->input->post('table',true);
			$id = $this->input->post('id',true);
			$name = $this->input->post('name',true);
			$reference = $this->input->post('reference',true) ?? null;
			$reference_id = $this->input->post('reference_id',true) ?? null;
			$q = $this->input->post('q',true);
			
			if(!$q){
				$q = '';
			}
		
			echo $this->ortyd->select2custom($id,$name,$q,$table,$reference,$reference_id);
			
		}
		
		
		public function savechat() {
			$response = [];

			// Ambil isi konten chat
			$content = $this->input->post('content', TRUE); // TRUE untuk XSS filter
			$userId = $this->session->userdata('userid');

			// Pastikan user ID tersedia
			if (!$userId) {
				$response = [
					'status' => 'error',
					'message' => 'User ID tidak ditemukan.',
					'csrf_hash' => $this->security->get_csrf_hash()
				];
				$this->output
					->set_content_type('application/json')
					->set_output(json_encode($response));
				return;
			}

			// Simpan file chat
			$filepath = FCPATH . 'logs/chatdata-' . $userId . '.txt';
			$saved = file_put_contents($filepath, $content);

			$response = [
				'status' => $saved !== false ? 'success' : 'error',
				'message' => $saved !== false ? 'Chat saved successfully.' : 'Failed to save chat.'
			];

			// Output JSON response
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode($response));
		}


		public function loadchat() {
			$file = FCPATH . 'logs/chatdata-' . $this->session->userdata('userid') . '.txt';
			$content = file_exists($file) ? file_get_contents($file) : '';

			$response = [
				'chat_html' => $content
			];

			$this->output
				->set_content_type('application/json')
				->set_output(json_encode($response));
		}
		
		public function select2useronline() {
			
			
			$filePath = FCPATH.'logs/user_list.txt'; // Path to your user list file
			if (!file_exists($filePath)) {
				$data = array('csrf_hash' =>$this->security->get_csrf_hash(),'items' => array());
			}else{
				
				// Read file and split it into lines
				$fileContent = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
				$connectedUsers = [];

				foreach ($fileContent as $line) {
					$user = trim($line);
					if (!empty($user)) {
						$connectedUsers[] = $user;
					}
				}
		
				$table = $this->input->post('table', true);
				$id = $this->input->post('id', true);
				$name = $this->input->post('name', true);
				$sbu_id = $this->input->post('sbu_id');
				$reference = $this->input->post('reference', true);
				$q = $this->input->post('q', true);

				if ($q == '' || !$q) {
					$q = '';
				}

				$selectnya = $id.' as id,'.$name.' as name, online_date';
				$this->db->select($selectnya);
				$this->db->like($name, $q);
				$this->db->where('active', 1);
				if($this->session->userdata('group_id') == 3){
					$this->db->where_in('gid', array(1,6));
				}
				$this->db->where('id !=', $this->session->userdata('userid'));
				$this->db->where_in('username', $connectedUsers);
				// Mengambil waktu 30 menit ke belakang
				$ten_minutes_ago = date('Y-m-d H:i:s', strtotime('-30 minutes'));
				$this->db->where('online_date >=', $ten_minutes_ago);

				$this->db->order_by($name, 'ASC');

				$query = $this->db->get($table);
				$query = $query->result_array();
				if ($query) {
					$i = 0;
					foreach ($query as $rows) {
						$data[$i]['id'] = $rows['id'];
						$data[$i]['name'] = $rows['name'];

						// Menambahkan ikon hijau jika online
						$online_icon = (strtotime($rows['online_date']) >= strtotime($ten_minutes_ago)) ? '<i class="fas fa-circle" style="color: green;"></i> ' : '';
						$data[$i]['name'] = $online_icon . $rows['name']; // Menambahkan ikon ke nama

						$i++;
					}
					$data = array('csrf_hash' =>$this->security->get_csrf_hash(),'items' => $data);
				} else {
					$data = array('csrf_hash' =>$this->security->get_csrf_hash(),'items' => array());
				}
			
			}

			echo json_encode($data);
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
