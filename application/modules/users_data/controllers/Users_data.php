<?php
//CONTROLLER BY HANAFI GINTING

defined('BASEPATH') OR exit('No direct script access allowed');

class Users_data extends MX_Controller {

		//CONFIG VARIABLE
		private $urlparent = 'users_data'; //NAME TABLE 
		private $identity_id = 'slug'; //IDENTITY TABLE
		private $field = 'slug'; // IDENTITY FROM NAME FOR GET ID
		private $slug_indentity = 'fullname'; //NAME FIELD 
		private $sorting = 'modified'; // SORT FOR VIEW
		private $exclude = array('lab_id','validate_admin','google_email','google_id','register_by_google','nik','timezone_id','perusahaan_id','company','workplace','status_kepegawaian','position_name','position_id','data_id','notif_id','banned','last_login','owner_id','validate','online_date','app_version','is_email_all','is_test','app_tipe','signature','color','history_id','status_id','created','modified','createdid','modifiedid','id','active','slug');
		private $exclude_table = array('lab_id','validate_admin','google_email','google_id','register_by_google','nik','timezone_id','perusahaan_id','signature','password','color','history_id','status_id','created','modified','createdid','modifiedid','id','active','slug');
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
		
		function get_data(){

			$activateddata = array('Inactive','Active');
			$table = $this->input->post('table',true);
			$searchfield = $this->input->post('select2search');
			
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
						array_push($selectnya,$table_references[0].'_'.$alias.'.'.$table_references[2]." as ".$table_references[0].'_'.$alias.'_'.$table_references[2]);
						
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
			
			if($searchfield != '' && $searchfield != null){
				$column_search = array($searchfield);
			}
			
			$order = array($table.'.'.$sorting => 'DESC');
			$selectnya = implode(",", $selectnya);
			if($selectnya != ''){
				$selectnya = ','.$selectnya;
			}
			$select = $table.'.*'.$selectnya;
			
			if( $this->input->post('active',true) == 0){
					array_push($wherecolumn, 'users_data.active');
					array_push($wheredetail, 0);
					
					array_push($wherecolumn, 'users_data.banned');
					array_push($wheredetail, 0);
					
					
			}elseif( $this->input->post('active',true) == 3){
					
					array_push($wherecolumn, 'users_data.banned');
					array_push($wheredetail, 1);
	
			}elseif( $this->input->post('active',true) == 4){
					
					array_push($wherecolumn, 'users_data.validate');
					array_push($wheredetail, 0);
					
					array_push($wherecolumn, 'users_data.active');
					array_push($wheredetail, 1);
	
			}elseif( $this->input->post('active',true) == 2){
					
					array_push($wherecolumn, 'users_data.banned');
					array_push($wheredetail, 0);
					
					array_push($wherecolumn, 'users_data.users_data.active');
					array_push($wheredetail, 1);
	
			}elseif( $this->input->post('active',true) == 5){
					
					array_push($wherecolumn, 'users_data.banned');
					array_push($wheredetail, 0);
					
					array_push($wherecolumn, 'users_data.validate');
					array_push($wheredetail, 1);
					
					array_push($wherecolumn, 'date(users_data.online_date)');
					array_push($wheredetail, date('Y-m-d'));
					
					array_push($wherecolumn, 'users_data.active');
					array_push($wheredetail, 1);
	
			}elseif( $this->input->post('active',true) == 6){
					
					array_push($wherecolumn, 'users_data.banned');
					array_push($wheredetail, 0);
					
					array_push($wherecolumn, 'users_data.validate');
					array_push($wheredetail, 1);
					
					array_push($wherecolumn, 'users_data.validate_admin');
					array_push($wheredetail, 0);

					array_push($wherecolumn, 'users_data.active');
					array_push($wheredetail, 1);
	
			}else{
					
					array_push($wherecolumn, 'users_data.active');
					array_push($wheredetail, $this->input->post('active',true));	
					
					array_push($wherecolumn, 'users_data.banned');
					array_push($wheredetail, 0);
					
			}
			
			$userid = $this->session->userdata('userid');
			$gid = $this->session->userdata('group_id');
			if($gid == 3){
				array_push($wherecolumn, 'users_data.id');
				array_push($wheredetail, $userid);
			}elseif($gid == 6){
				array_push($wherecolumn, 'users_data.gid');
				array_push($wheredetail, 3);
			}else{
				array_push($wherecolumn, $table.'.id !=');
				array_push($wheredetail, 1);
			}
			

			$groupby = array();
		
			$list = $this->ortyd->get_datatables($table,$column_order,$column_search,$order,$select,$jointable,$joindetail,$joinposition, $wherecolumn,$wheredetail,$groupby);
			$data = array();
			$no = $_POST['start'];
			foreach ($list as $rows) {
				$rows = (array) $rows;
				$no++;
				$row = array();
				//$row[] = $no;
				
				$identity_id = $rows[$this->identity_id];
				$uuid = "'". $rows[$this->identity_id]."'";
				
				if($this->ortyd->access_check_update_data($this->module)){

					$editdata = '<div class="menu-item px-3"><a class="dropdown-item d-flex align-items-center gap-2" href="'.base_url().$this->urlparent.'/editdata/'.$identity_id.'"><i class="fa fa-edit text-primary mt-1"></i> Edit</a></div> ';
					
					
									
					$restoredata = '<div class="menu-item px-3"><a href="javascript:;" class="dropdown-item d-flex align-items-center gap-2" onClick="restoredata('.$uuid.')"><i class="fa fa-undo text-warning"></i> <span>Restore</span></a></div>';
					
					if($rows['banned'] == 1){
						$unbanned = '<div class="menu-item px-3"><a href="javascript:;" class="dropdown-item" onClick="restorebanneddata('.$uuid.')"><i class="fa fa-edit"></i> Unbanned User</a></div>';
						$banneddata = '';
					}else{
						$unbanned = '';
						$banneddata = '<div class="menu-item px-3"><a href="javascript:;" class="dropdown-item" onClick="banneddata('.$uuid.')"><i class="fa fa-edit"></i> Ban User</a></div>';
					}
					
					if($rows['validate'] == 0){
						$validatenya = '<div class="menu-item px-3"><a href="javascript:;" class="dropdown-item" onClick="validatedata('.$uuid.')"><i class="fa fa-edit"></i> Validate User</a></div>';
					}else{
						$validatenya = '';
					}
					
					if($rows['validate_admin'] == 0){
						$validatenyaadmin = '<div class="menu-item px-3"><a href="javascript:;" class="dropdown-item" onClick="validatedataadmin('.$uuid.')"><i class="fa fa-edit"></i> Activate User</a></div>';
					}else{
						$validatenyaadmin = '';
					}
						
				}else{
					$editdata = '';
					$banneddata = '';
					$restoredata = '';
					$validatenya = '';
					$validatenyaadmin = '';
					$unbanned = '';
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
						<div class="menu menu-sub menu-sub-dropdown menu-sub-dropdown-dt menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
							
							<!--begin::Menu item-->
							'.$editdata.'
							'.$banneddata.'
							'.$validatenya.'
							'.$validatenyaadmin.'
							'.$unbanned.'
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
						<div class="menu menu-sub menu-sub-dropdown menu-sub-dropdown-dt menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
							
							<!--begin::Menu item-->
							'.$restoredata.'
							<!--end::Menu item-->
								
						</div>
						<!--end::Menu-->
						
					';
					
				}
				
				$row[] = $action;
				if($query_column){
					$alias = 0;
					foreach($query_column as $rowsdata){
						$table_references = null;
						$table_references = $this->ortyd->get_table_reference($table,$rowsdata['name']);
						if($table_references != null){
							$variable = $rows[$table_references[0].'_'.$alias.'_'.$table_references[2]];
							$row[] = $variable;
						}elseif($rowsdata['name'] == 'link'){
							$variable = '<a href="'.base_url($table).$identity_id.'">'.$rows[$rowsdata['name']].'</a>';
							$row[] = $variable;
						}else{
							$variable = $rows[$rowsdata['name']];
							$variable = $this->ortyd->getFormatData($table,$rowsdata['name'], $variable);
							$row[] = $variable;
						}
						
						$alias++;
					}
				}
				//$row[] = $status;

				$data[] = $row;
			}
			
	 
			$output = array(
				"draw" => $_POST['draw'],
				"recordsTotal" => $this->ortyd->count_filtered($table,$column_order,$column_search,$order,$select,$jointable,$joindetail,$joinposition, $wherecolumn,$wheredetail,$groupby),
				"recordsFiltered" => $this->ortyd->count_filtered($table,$column_order,$column_search,$order,$select,$jointable,$joindetail,$joinposition, $wherecolumn,$wheredetail,$groupby),"csrf_hash" => $this->security->get_csrf_hash(),
				"data" => $data,
			);
			
			echo json_encode($output);
		}
		
		public function createdata()
		{
			$this->ortyd->access_check_insert($this->module);
			$data['title'] = 'Buat '.$this->titlechilddb;;
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
			$userid = $this->session->userdata('userid');
			$gid = $this->session->userdata('group_id');
			if($gid == 3){
				$username = $this->ortyd->select2_getname($ID,$this->tabledb,'slug','id');
				if($username != $userid){
					redirect('dashboard?message=noaccess', 'refresh');
				}
			}elseif($gid == 6){
				$giddata = $this->ortyd->select2_getname($ID,$this->tabledb,'slug','gid');
				if($giddata != 3){
					redirect('dashboard?message=noaccess', 'refresh');
				}
			}else{
				//array_push($wherecolumn, $table.'.id !=');
				//array_push($wheredetail, 1);
			}
			
			$this->ortyd->access_check_update($this->module);
			$ID = $this->ortyd->select2_getname($ID,$this->tabledb,$this->field,$this->tableid);
			$data['title'] = 'Edit '.$this->titlechilddb;
			$data['id'] = $ID;
			$data['module'] = $this->module;
			$data['modeldb'] = $this->m_model_data;
			$data['exclude'] = $this->exclude;
			$data['headurl'] = $this->headurldb;
			$data['datarow'] = $this->m_model_data->get_data_byid($data['id'], $this->tabledb, $this->tableid);
			$data['action'] = base_url().$this->actionurl.'/'.$data['id'];
			$this->template->load('main',$this->viewformname, $data);
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
							}elseif($rows_column["name"] == 'password'){
								$dataisi = $this->input->post($rows_column["name"],true);
								if($dataisi != null && $dataisi != ''){
									$dataisi = $this->ortyd->hash($dataisi);
									if($dataisi == ''){
										$dataisi = null;
									}
									$data_array = array($rows_column["name"] => $dataisi);
									$data = array_merge($data,$data_array);
								}
							}elseif($rows_column["name"] == 'cover'){
								$dataisi = $this->input->post($rows_column["name"],true);
								if($dataisi != null && $dataisi != ''){
									$dataisi = $this->ortyd->hash($dataisi);
									if($dataisi == ''){
										$dataisi = null;
									}
									$data_array = array($rows_column["name"] => $dataisi);
									$data = array_merge($data,$data_array);
								}
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
								$this->saveRoleApproval($id,1);
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
					
					
					$emaildata = $this->input->post('email', true);
					$email_exists = $this->db->where('email', $emaildata)->get($this->tabledb)->row();

					if ($email_exists) {
						$result = array("csrf_hash" => $this->security->get_csrf_hash(), "status" => "error", "error" => 'Email already exists');
						echo json_encode($result);
						die();
					}

					$username = $this->ortyd->select2_getname($this->input->post('username'),$this->tabledb,'username','id');
					if($username != '-'){
						$result = array("csrf_hash" => $this->security->get_csrf_hash(),"status" => "error", "error" => 'Username Exist');
						echo json_encode($result);
						die();
					}
					
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
							}elseif($rows_column["name"] == 'password'){
								$dataisi = $this->input->post($rows_column["name"],true);
								if($dataisi != null && $dataisi != ''){
									$dataisi = $this->ortyd->hash($dataisi);
									if($dataisi == ''){
										$dataisi = null;
									}
									$data_array = array($rows_column["name"] => $dataisi);
									$data = array_merge($data,$data_array);
								}
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
							array('banned' 			=> 0),
							array('validate' 		=> 1),
							array('validate_admin' 	=> 1),
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
								$this->saveRoleApproval($insert_id,1);
								$result = array("csrf_hash" => $this->security->get_csrf_hash(),"status" => "success");
								echo json_encode($result);
							}else{
								$result = array("csrf_hash" => $this->security->get_csrf_hash(),"status" => "error", "error" => 'Data Not Save');
								echo json_encode($result);
							}
						}
					}else{
						$result = array("csrf_hash" => $this->security->get_csrf_hash(),"status" => "error", "error" => 'Data Not Save');
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
			
			$userid = $this->session->userdata('userid');
			$gid = $this->session->userdata('group_id');
			if($gid == 3){
				$reference = 3;
				$reference_id = 'id';
			}elseif($gid == 6){
				$reference = 3;
				$reference_id = 'id';
			}else{
				
			}
		
			echo $this->ortyd->select2custom($id,$name,$q,$table,$reference,$reference_id);
			
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
		
		public function banneddata(){
			$this->ortyd->access_check_update($this->module);
			
			if($this->field == 'slug'){
				$id = $this->input->post('id',true);	
				$this->db->where($this->field,$id);
			}else{
				$id = $this->input->post('id',true);	
				$this->db->where($this->tableid,$id);
			}
			

			$this->db->where('banned',0);
			$query = $this->db->get($this->tabledb);
			$query = $query->result_object();
			if($query){
				
				$dataremove = array(
					'banned' 		=> 1,
					'modifiedid'		=> $this->session->userdata('userid'),
					'modified'			=> date('Y-m-d H:i:s')
				);
										
				if($this->field == 'slug'){
					$this->db->where('slug', $id);
				}else{
					$this->db->where('id', $id);
				}
				$updateactive = $this->db->update($this->tabledb, $dataremove);
				
				if($updateactive){
					$result = array("csrf_hash" => $this->security->get_csrf_hash(),"message" => "success");
					echo json_encode($result);
				}else{
					$result = array("csrf_hash" => $this->security->get_csrf_hash(),"message" => "error");
					echo json_encode($result);
				}
			
			}else{
				$result = array("csrf_hash" => $this->security->get_csrf_hash(),"message" => "error");
				echo json_encode($result);
			}

		}
		
		
		public function validatedata(){
			$this->ortyd->access_check_update($this->module);
			
			if($this->field == 'slug'){
				$id = $this->input->post('id',true);	
				$this->db->where($this->field,$id);
			}else{
				$id = $this->input->post('id',true);	
				$this->db->where($this->tableid,$id);
			}

			$this->db->where('validate',0);
			$query = $this->db->get($this->tabledb);
			$query = $query->result_object();
			if($query){
				
				$dataremove = array(
					'validate' 			=> 1,
					'modifiedid'		=> $this->session->userdata('userid'),
					'modified'			=> date('Y-m-d H:i:s')
				);
										
				if($this->field == 'slug'){
					$this->db->where('slug', $id);
				}else{
					$this->db->where('id', $id);
				}
				$updateactive = $this->db->update($this->tabledb, $dataremove);
				
				if($updateactive){
					$result = array("csrf_hash" => $this->security->get_csrf_hash(),"message" => "success");
					echo json_encode($result);
				}else{
					$result = array("csrf_hash" => $this->security->get_csrf_hash(),"message" => "error");
					echo json_encode($result);
				}
			
			}else{
				$result = array("csrf_hash" => $this->security->get_csrf_hash(),"message" => "error");
				echo json_encode($result);
			}

		}
		
		public function validatedataadmin(){
			$this->ortyd->access_check_update($this->module);
			
			if($this->field == 'slug'){
				$id = $this->input->post('id',true);	
				$this->db->where($this->field,$id);
			}else{
				$id = $this->input->post('id',true);	
				$this->db->where($this->tableid,$id);
			}

			$this->db->where('validate',1);
			$this->db->where('validate_admin',0);
			$query = $this->db->get($this->tabledb);
			$query = $query->result_object();
			if($query){
				
				$dataremove = array(
					'validate_admin' 	=> 1,
					'modifiedid'		=> $this->session->userdata('userid'),
					'modified'			=> date('Y-m-d H:i:s')
				);
										
				if($this->field == 'slug'){
					$this->db->where('slug', $id);
				}else{
					$this->db->where('id', $id);
				}
				$updateactive = $this->db->update($this->tabledb, $dataremove);
				
				if($updateactive){
					$result = array("csrf_hash" => $this->security->get_csrf_hash(),"message" => "success");
					echo json_encode($result);
				}else{
					$result = array("csrf_hash" => $this->security->get_csrf_hash(),"message" => "error");
					echo json_encode($result);
				}
			
			}else{
				$result = array("csrf_hash" => $this->security->get_csrf_hash(),"message" => "error");
				echo json_encode($result);
			}

		}
		
		public function restorebanneddata(){
			$this->ortyd->access_check_update($this->module);
			
			if($this->field == 'slug'){
				$id = $this->input->post('id',true);	
				$this->db->where($this->field,$id);
			}else{
				$id = $this->input->post('id',true);	
				$this->db->where($this->tableid,$id);
			}
			
			$this->db->where('banned',1);
			$query = $this->db->get($this->tabledb);
			$query = $query->result_object();
			if($query){
				
				$dataremove = array(
					'banned' 			=> 0,
					'modifiedid'		=> $this->session->userdata('userid'),
					'modified'			=> date('Y-m-d H:i:s')
				);
										
				if($this->field == 'slug'){
					$this->db->where('slug', $id);
				}else{
					$this->db->where('id', $id);
				}
				$updateactive = $this->db->update($this->tabledb, $dataremove);
				
				if($updateactive){
					$result = array("csrf_hash" => $this->security->get_csrf_hash(),"message" => "success");
					echo json_encode($result);
				}else{
					$result = array("csrf_hash" => $this->security->get_csrf_hash(),"message" => "error");
					echo json_encode($result);
				}
			
			}else{
				$result = array("csrf_hash" => $this->security->get_csrf_hash(),"message" => "error");
				echo json_encode($result);
			}

		}
		
		
		public function getDataRoleRequired(){
			$user_id = $this->input->post('user_id');
			
			
			$this->db->select('users_data_groups.*, users_groups.name as role_name');
			$this->db->where('users_data_groups.user_id',$user_id);
			$this->db->where('users_data_groups.active',1);
			$this->db->where('users_groups.active',1);
			$this->db->join('users_groups','users_groups.id = users_data_groups.role_id','left');
			$query = $this->db->get('users_data_groups');
			$query = $query->result_object();
			if($query){
				$result = array("csrf_hash" => $this->security->get_csrf_hash(),"status" => "success", "data" => $query);
				echo json_encode($result);
			}else{
				$result = array("csrf_hash" => $this->security->get_csrf_hash(),"status" => "error");
				echo json_encode($result);
			}
		}
		
		function saveRoleApproval($approval_id,$keydata = 0){
			if($approval_id != '' && $approval_id != null){
				
				$keydata = $approval_id;
				//$tenggat_sph = $this->input->post('tenggat_sph');
				$tbl_approval_role_id = $this->input->post('approval_role_id')[$keydata] ?? null;
				$tbl_role_id = $this->input->post('approval_role_role_id')[$keydata] ?? null;

				if($tbl_role_id != null && $tbl_role_id != ''){
					
					if(count($tbl_role_id) > 0){
				
						$datadetail = array(
							'active' 			=> 0,
							'modifiedid'		=> $this->session->userdata('userid'),
							'modified'			=> date('Y-m-d H:i:s')
						);
										
						$this->db->where('users_data_groups.user_id', $approval_id);
						$update = $this->db->update('users_data_groups', $datadetail);
						if($update){
							foreach( $tbl_role_id as $key => $n ) {
								
								$slugnya = $approval_id.'-'.$tbl_role_id[$key];
								
								if($tbl_approval_role_id[$key] != '' && $tbl_approval_role_id[$key] != '0' && $tbl_approval_role_id[$key] != 0){
									$this->db->where('users_data_groups.id', $tbl_approval_role_id[$key]);
									$query = $this->db->get('users_data_groups');
									$query = $query->result_object();
									if(!$query){
									
										$datadetail = array(
											'user_id' 				=> $approval_id,
											'role_id' 				=> $tbl_role_id[$key],
											'active' 				=> 1,
											'createdid'				=> $this->session->userdata('userid'),
											'created'				=> date('Y-m-d H:i:s'),
											'modifiedid'			=> $this->session->userdata('userid'),
											'modified'				=> date('Y-m-d H:i:s')
										);
										
										$string = $slugnya;
										$slug = $this->ortyd->sanitize($string,'users_data_groups');
										$datadetail = array_merge($datadetail,
											array('slug' 	=> $slug)
										);
												
										$insert = $this->db->insert('users_data_groups', $datadetail);	
										$insert_id = $this->db->insert_id();
										
									}else{
										$datadetail = array(
											'user_id' 				=> $approval_id,
											'role_id' 				=> $tbl_role_id[$key],
											'active' 				=> 1,
											'modifiedid'			=> $this->session->userdata('userid'),
											'modified'				=> date('Y-m-d H:i:s')
										);
												
										$this->db->where('users_data_groups.id', $query[0]->id);
										$update = $this->db->update('users_data_groups', $datadetail);
										
									}
								}else{
									
									$datadetail = array(
										'user_id' 				=> $approval_id,
										'role_id' 				=> $tbl_role_id[$key],
										'active' 				=> 1,
										'createdid'				=> $this->session->userdata('userid'),
										'created'				=> date('Y-m-d H:i:s'),
										'modifiedid'			=> $this->session->userdata('userid'),
										'modified'				=> date('Y-m-d H:i:s')
									);
										
									$string = $slugnya;
									$slug = $this->ortyd->sanitize($string,'users_data_groups');
									$datadetail = array_merge($datadetail,
										array('slug' 	=> $slug)
									);
												
									$insert = $this->db->insert('users_data_groups', $datadetail);	
									$insert_id = $this->db->insert_id();
										
								}
							}
						}
					}
				}
			}
			
			$this->saveRoleAwal($approval_id);
		}
		
		function saveRoleAwal($user_id){
			
			$this->db->where('users_data.id', $user_id);
			$query = $this->db->get('users_data');
			$query = $query->result_object();
			if($query){
				$this->db->where('users_data_groups.user_id', $query[0]->id);
				$this->db->where('users_data_groups.role_id', $query[0]->gid);
									$querygid = $this->db->get('users_data_groups');
									$querygid = $querygid->result_object();
									if(!$querygid){
									
										$slugnya = $query[0]->id.'-'.$query[0]->gid;
										
										$datadetail = array(
											'user_id' 				=> $query[0]->id,
											'role_id' 				=> $query[0]->gid,
											'active' 				=> 1,
											'createdid'				=> $this->session->userdata('userid'),
											'created'				=> date('Y-m-d H:i:s'),
											'modifiedid'			=> $this->session->userdata('userid'),
											'modified'				=> date('Y-m-d H:i:s')
										);
										
										$string = $slugnya;
										$slug = $this->ortyd->sanitize($string,'users_data_groups');
										$datadetail = array_merge($datadetail,
											array('slug' 	=> $slug)
										);
												
										$insert = $this->db->insert('users_data_groups', $datadetail);	
										$insert_id = $this->db->insert_id();
										
									}else{
										$datadetail = array(
											'user_id' 				=> $query[0]->id,
											'role_id' 				=> $query[0]->gid,
											'active' 				=> 1,
											'modifiedid'			=> $this->session->userdata('userid'),
											'modified'				=> date('Y-m-d H:i:s')
										);
												
										$this->db->where('users_data_groups.id', $querygid[0]->id);
										$update = $this->db->update('users_data_groups', $datadetail);
										
									}
			}
			
		}
		
		// Enhanced createusernewimport function
		function createusernewimport($email, $fullname = null, $username = null, $password = null, $notelp = null, $nik = null, $ppmse_id = null, $gid = null)
		{
			// Set default values if parameters not provided
			$fullname = $fullname ?: $email;
			$notelp   = $notelp ?: null;
			$password = $password ?: $this->generateRandomString();
			$gid      = $gid ?: 3;
			
			// Check if email already exists
			$this->db->where('email', $email);
			$user = $this->db->get('users_data')->row();
			if ($user) {
				// If email exists, return user id
				return $user->id;
			}
			
			// If username not provided, extract from email (before @)
			if (!$username) {
				$username = strstr($email, '@', true);
			}
			
			// Ensure unique username, add number suffix if exists
			$original_username = $username;
			$i = 1;
			while (true) {
				$this->db->where('username', $username);
				$exists = $this->db->get('users_data')->row();
				if (!$exists) {
					break; // username is unique, stop loop
				}
				$username = $original_username . $i;
				$i++;
			}
			
			$slug = $this->ortyd->sanitize($username, 'users_data');
			
			$data = [
				'username'      => $username,
				'fullname'      => $fullname,
				'password'      => is_string($password) && strlen($password) > 50 ? $password : $this->ortyd->hash($password),
				'email'         => $email,
				'notelp'        => $notelp,
				'nik'           => $nik,
				'ppmse_id'   	=> $ppmse_id,
				'slug'          => $slug,
				'gid'           => $gid,
				'active'        => 1,
				'validate_admin' => 1,
				'user_id_ref'   => 1,
				'banned'        => 0,
				'validate'      => 1,
				'cover'         => null,
				'createdid'     => $this->session->userdata('userid') ?: 1,
				'created'       => date('Y-m-d H:i:s'),
				'modifiedid'    => $this->session->userdata('userid') ?: 1,
				'modified'      => date('Y-m-d H:i:s')
			];
			
			if ($this->db->insert('users_data', $data)) {
				return $this->db->insert_id(); // return user_id from insert
			}
			
			return null;
		}

		// Function untuk parsing data user dari string
		function parseUserDataFromString($userString)
		{
			if (empty($userString)) {
				return null;
			}
			
			// Split by space, but handle fullname with multiple words
			$parts = explode(' ', trim($userString));
			
			if (count($parts) < 6) {
				return null; // Invalid format
			}
			
			// Parse from the end since the format is more predictable there
			$gid = array_pop($parts); // Last element: gid
			$nik_notelp = array_pop($parts); // Second last: nik/notelp
			$email = array_pop($parts); // Third last: email
			$password = array_pop($parts); // Fourth last: password (hash)
			$username = array_pop($parts); // Fifth last: username
			
			// The rest is fullname
			$fullname = implode(' ', $parts);
			
			return array(
				'fullname' => $fullname,
				'username' => $username,
				'password' => $password,
				'email' => $email,
				'notelp' => $nik_notelp,
				'nik' => $nik_notelp,
				'ppmse_name' => null,
				'gid' => $gid
			);
		}

		// Main import function for users
		function importwithprogress()
		{
			//error_reporting(0);
			//ini_set('display_errors', 0);  

			$this->load->library('excel');
			$importdata = 1;
			$datanyaarray = array();
			
			if (isset($_FILES["file"]["name"])) {
				if ($importdata) {
					$path = $_FILES["file"]["tmp_name"];
					$object = PHPExcel_IOFactory::load($path);
					$detail = array();
					$x = 0;
					$sheet = 1;

					$sheetCount = $object->getSheetCount();
					if ($sheetCount) {
						foreach ($object->getWorksheetIterator() as $worksheet) {
							if ($sheet == 1) {
								$highestRow = $worksheet->getHighestRow();
								$highestColumn = $worksheet->getHighestColumn();
								$x = 0;

								// Starting from row 2 (assuming row 1 = headers, row 2+ = data)
								for ($row = 2; $row <= $highestRow; $row++) {
									try {
										// Method 1: If data is in separate columns
										// fullname username password email notelp nik ppmse_name gid
										$fullname = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
										$username = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
										$password = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
										$email = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
										$notelp = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
										$nik = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
										$ppmse_name = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
										$gid = $worksheet->getCellByColumnAndRow(7, $row)->getValue();
	
										$ppmse_id = $this->m_model_data->getMasterId('master_ppmse', 'name', $ppmse_name);
										// Method 2: If data is in one string (uncomment this section if needed)
										/*
										$user_string = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
										$parsed_data = $this->parseUserDataFromString($user_string);
										
										if ($parsed_data) {
											$fullname = $parsed_data['fullname'];
											$username = $parsed_data['username'];
											$password = $parsed_data['password'];
											$email = $parsed_data['email'];
											$notelp = $parsed_data['notelp'];
											$nik = $parsed_data['nik'];
											$ppmse_name = $parsed_data['ppmse_name'];
											$gid = $parsed_data['gid'];
										}
										*/

										// Skip empty rows
										if (empty($email) || empty($username)) {
											continue;
										}

										// Create user dengan data lengkap
										$user_id = $this->createusernewimport(
											$email,
											$fullname,
											$username,
											$password,
											$notelp,
											$nik,
											$ppmse_id,
											$gid
										);

										$data = array(
											'fullname' => $fullname,
											'username' => $username,
											'password' => $password,
											'email' => $email,
											'notelp' => $notelp,
											'nik' => $nik,
											'ppmse_id' => $ppmse_id,
											'gid' => $gid,
											'user_id' => $user_id,
											'row_number' => $row,
											'message' => $user_id ? 'success' : 'error'
										);

										array_push($datanyaarray, $data);

									} catch (Exception $e) {
										$data = array(
											'fullname' => $fullname ?? 'Unknown',
											'email' => $email ?? 'Unknown Email',
											'error_message' => $e->getMessage(),
											'row_number' => $row,
											'message' => 'error'
										);

										array_push($datanyaarray, $data);
									}
								}

								break;
							} else {
								$sheet++;
							}
						}

						$totalrow = count($datanyaarray);

						$result = array(
							"csrf_hash" => $this->security->get_csrf_hash(),
							"message" => "success",
							"data" => $datanyaarray,
							"total" => $totalrow
						);
						echo json_encode($result);
					} else {
						$result = array(
							"csrf_hash" => $this->security->get_csrf_hash(),
							"message" => "error",
							'errors' => 'Sheet Not Found',
							'sheet' => $sheetCount
						);
						echo json_encode($result);
					}
				} else {
					$result = array(
						"csrf_hash" => $this->security->get_csrf_hash(),
						"message" => "error",
						'errors' => 'File Not Uploads'
					);
					echo json_encode($result);
				}
			} else {
				$result = array(
					"csrf_hash" => $this->security->get_csrf_hash(),
					"message" => "error",
					'errors' => 'Wrong Format or Data'
				);
				echo json_encode($result);
			}
		}

		// Save user to database
		function saveDB()
		{
			$data = $this->input->post('data');
			$data = json_decode($data, true);
			
			$result = array(
				"csrf_hash" => $this->security->get_csrf_hash(),
				"message"   => "error",
				'errors'    => 'Not Save',
				'data'      => $data
			);
			
			try {
				// Extract required fields for user
				$email = $data['email'] ?? null;
				$username = $data['username'] ?? null;
				$fullname = $data['fullname'] ?? null;
				
				if($email == '' || $email == null || $username == '' || $username == null){
					$result['errors'] = 'Email and Username are required';
					$result['csrf_hash'] = $this->security->get_csrf_hash();
					echo json_encode($result);
					return;
				}
				
				// Check if user already exists
				$this->db->where('email', $email);
				$existing_user = $this->db->get('users_data')->row();
				
				if ($existing_user) {
					// Update existing user
					$this->db->where('id', $existing_user->id);
					
					$update_data = array(
						'fullname' => $data['fullname'] ?? $existing_user->fullname,
						'username' => $data['username'] ?? $existing_user->username,
						'password' => $data['password'] ?? $existing_user->password,
						'notelp' => $data['notelp'] ?? $existing_user->notelp,
						'nik' => $data['nik'] ?? $existing_user->nik,
						'ppmse_id' => $data['ppmse_id'] ?? $existing_user->ppmse_id,
						'gid' => $data['gid'] ?? $existing_user->gid,
						'validate_admin' => 1,
						'active' => isset($data['active']) ? $data['active'] : $existing_user->active,
						'modifiedid' => $this->session->userdata('userid') ?: 1,
						'modified' => date('Y-m-d H:i:s')
					);
					
					$this->db->update('users_data', $update_data);
					$user_id = $existing_user->id;
					$operation = 'Update';
					
				} else {
					// Create new user
					$user_id = $this->createusernewimport(
						$data['email'],
						$data['fullname'],
						$data['username'],
						$data['password'],
						$data['notelp'],
						$data['nik'],
						$data['ppmse_id'],
						$data['gid']
					);
					$operation = 'Insert';
				}
				
				// Check for database errors
				if ($this->db->error()['code'] != 0) {
					$db_error = $this->db->error();
					$result['errors'] = "DB Error [{$db_error['code']}] {$db_error['message']}";
					$result['csrf_hash'] = $this->security->get_csrf_hash();
					echo json_encode($result);
					return;
				}
				
				// Verify the operation
				$this->db->where('users_data.id', $user_id);
				$query = $this->db->get('users_data');
				
				if (!$query) {
					$db_error = $this->db->error();
					$result['csrf_hash'] = $this->security->get_csrf_hash();
					$result['errors'] = "DB Error [{$db_error['code']}] {$db_error['message']}";
					echo json_encode($result);
					return;
				}
				
				$rows = $query->result_object();
				if ($rows) {
					$result = array(
						"csrf_hash" => $this->security->get_csrf_hash(),
						"message"   => "success",
						'status'    => $operation,
						'user_id'   => $user_id,
						'rows'      => $rows,
						'data'      => $data
					);
				} else {
					$result['errors'] = 'User not found after database operation';
					$result['rows'] = $rows;
				}
				
			} catch (Exception $e) {
				$result['errors'] = $e->getMessage();
			}
			
			$result['csrf_hash'] = $this->security->get_csrf_hash();
			echo json_encode($result);
		}
		
}
