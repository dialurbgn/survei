<?php
//CONTROLLER BY HANAFI GINTING

defined('BASEPATH') OR exit('No direct script access allowed');

class Data_ticket extends MX_Controller {

		//CONFIG VARIABLE
		private $urlparent = 'data_ticket'; //NAME TABLE 
		private $identity_id = 'slug'; //IDENTITY TABLE
		private $field = 'slug'; // IDENTITY FROM NAME FOR GET ID
		private $slug_indentity = 'subjek'; //NAME FIELD 
		private $sorting = 'modified'; // SORT FOR VIEW
		private $exclude = array('berakhir','status_id','created','modified','createdid','modifiedid','id','active','slug');
		private $exclude_table = array('berakhir','status_id','created','modified','createdid','modifiedid','id','active','slug');
		//END CONFIG VARIABLE
		
		private $viewname;
		private $viewformname;
		private $viewformnamereply;
		private $tabledb;
		private $tableid;
		private $titlechilddb;
		private $headurldb;
		private $actionurl;
		private $actionurlbalasan;
		private $actionurlbalasantutup;
		private $module;
		private $modeldb;

		public function __construct()
		{
			
			
			$this->viewname = $this->urlparent.'/views/v_data';
			$this->viewformname = $this->urlparent.'/views/v_data_form';
			$this->viewformnamereply = $this->urlparent.'/views/v_data_view';
			$this->tabledb = $this->urlparent;
			$this->tableid = $this->urlparent.'.id';
			$this->titlechilddb = strtoupper($this->urlparent);
			$this->headurldb = $this->urlparent;
			$this->actionurl = $this->urlparent.'/actiondata';
			$this->actionurlbalasan = $this->urlparent.'/actiondata_ticket_balasan';
			$this->actionurlbalasantutup = $this->urlparent.'/actiondata_ticket_tutup';
			$this->module = $this->urlparent;
			$this->modeldb = 'm_data';
			
		
			$this->load->model($this->modeldb,'m_model_data');
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
			
			array_push($wherecolumn, $table.'.active');
			array_push($wheredetail, $this->input->post('active',true));

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
				
				
				$viewdata = '<div class="menu-item px-3"><a class="dropdown-item d-flex align-items-center gap-2" href="'.base_url().$this->urlparent.'/replydata/'.$identity_id.'"><i class="fa fa-eye text-info mt-1"></i> View</a></div> ';
				
				if($this->ortyd->access_check_update_data($this->module)){

					if($this->session->userdata('userid') == $rows['createdid'] || $this->session->userdata('group_id') != 3){
						$editdata = '<div class="menu-item px-3"><a class="dropdown-item d-flex align-items-center gap-2" href="'.base_url().$this->urlparent.'/editdata/'.$identity_id.'"><i class="fa fa-edit text-primary mt-1"></i> Edit</a></div> ';
					}else{
						$editdata = '';
					}
		
					$restoredata = '<div class="menu-item px-3"><a href="javascript:;" class="dropdown-item d-flex align-items-center gap-2" onClick="restoredata('.$uuid.')"><i class="fa fa-undo text-warning"></i> <span>Restore</span></a></div>';
						
				}else{
					$editdata = '';
					$restoredata = '';
				}
					
					
				if($this->ortyd->access_check_delete_data($this->module)){
					
					$deletedata = '<div class="menu-item px-3"><a href="javascript:;" class="dropdown-item d-flex align-items-center gap-2" onClick="deletedata('.$uuid.')"><i class="fa fa-trash text-danger mt-1"></i> Delete</a></div>';
						
				}else{
					if($editdata == '' && $viewdata == ''){
						$deletedata = '<div class="menu-item px-3"><a class="dropdown-item">No Action</a></div>';
					}else{
						$deletedata = '';
					}
					
				}
				
				if($rows['active'] == 1){
					$status = '<span class="label label-success">'.$activateddata[$rows['active']].'</span>';
					$action = '
				
						<a href="#" class="btn btn-sm btn-primary btn-active-light-primary btn-flex btn-center btn-sm menu-dropdown" data-kt-menu-trigger="click" data-kt-menu-placement="top-end">
							Action <i class="ki-duotone ki-down fs-5 ms-1"></i>                    
						</a>
						<!--begin::Menu-->
						<div class="menu menu-sub menu-sub-dropdown menu-sub-dropdown-dt menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
							
							<!--begin::Menu item-->
							'.$viewdata.'
							'.$editdata.'
							'.$deletedata.'
							<!--end::Menu item-->
								
						</div>
						<!--end::Menu-->
				
					';
				}else{
					$status = '<span class="label label-danger">'.$activateddata[$rows['active']].'</span>';
					$action = '
					
						<a href="#" class="btn btn-sm btn-primary btn-active-light-primary btn-flex btn-center btn-sm menu-dropdown" data-kt-menu-trigger="click" data-kt-menu-placement="top-end">
							Action <i class="ki-duotone ki-down fs-5 ms-1"></i>                    
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
		
		function get_data_view(){

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
				foreach($query_column as $rowsdata){
					$table_references = null;
					//$table_references = $this->ortyd->get_table_reference($table,$rowsdata['name']);
					if($table_references != null){
						array_push($ordernya,$table_references[0].'.'.$table_references[2]);
						array_push($searchnya,$table_references[0].'.'.$table_references[2]);
						array_push($selectnya,$table_references[0].'.'.$table_references[2]." as ".$table_references[0].'_'.$table_references[2]);
						
						$joinnya = array_search($table_references[0],$selectnya);
						if($joinnya == '' || $joinnya == null){
							array_push($jointable,$table_references[0]);
							array_push($joindetail,$table.'.'.$rowsdata['name'].' = '.$table_references[0].'.'.$table_references[1]);
							array_push($joinposition,'left');
						}

					}else{
						array_push($ordernya,$table.'.'.$rowsdata['name']);
						array_push($searchnya,$table.'.'.$rowsdata['name']);
					}
					
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
			
			
			array_push($wherecolumn, $table.'.active');
			array_push($wheredetail, $this->input->post('active',true));

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
				
				
				$viewdata = '<li class="nav-item"><a class="dropdown-item" href="'.base_url().$this->urlparent.'/replydata/'.$identity_id.'"><i class="fa fa-eye text-info mt-1"></i> View</a></li> ';
				
				if($this->ortyd->access_check_update_data($this->module)){

					$editdata = '<li class="nav-item"><a class="dropdown-item" href="'.base_url().$this->urlparent.'/editdata/'.$identity_id.'"><i class="fa fa-edit"></i> Edit</a></li> ';
									
					$restoredata = '<li class="nav-item"><a href="javascript:;" class="dropdown-item" onClick="restoredata('.$uuid.')"><i class="fa fa-trash"></i> Restore</a></li>';
						
				}else{
					$editdata = '';
					$restoredata = '';
				}
					
					
				if($this->ortyd->access_check_delete_data($this->module)){
					
					$deletedata = '<li class="nav-item"><a href="javascript:;" class="dropdown-item" onClick="deletedata('.$uuid.')"><i class="fa fa-trash text-danger mt-1"></i> Delete</a></li>';
						
				}else{
					if($editdata == '' && $viewdata == ''){
						$deletedata = '<li class="nav-item"><a class="dropdown-item">No Action</a></li>';
					}else{
						$deletedata = '';
					}
					
				}
				
				if($rows['active'] == 1){
					$status = '<span class="label label-success">'.$activateddata[$rows['active']].'</span>';
					$action = '
				
						<li class="nav-item dropdown language-select text-uppercase" style="list-style-type: none;">
							<a class="btn btn-sm btn-primary nav-link dropdown-item dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</a>
							<ul class="dropdown-menu">
							 <!--begin::Menu item-->
							'.$viewdata.'
							<!--end::Menu item-->
							</ul>
						  </li>
			  
				
					';
				}else{
					$status = '<span class="label label-danger">'.$activateddata[$rows['active']].'</span>';
					$action = '
					
						<li class="nav-item dropdown language-select text-uppercase" style="list-style-type: none;">
							<a class="btn btn-sm btn-primary nav-link dropdown-item dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</a>
							<ul class="dropdown-menu">
							 <!--begin::Menu item-->
							'.$restoredata.'
							<!--end::Menu item-->
							</ul>
						  </li>
						
					';
					
				}
				
				$row[] = $action;
				if($query_column){
					foreach($query_column as $rowsdata){
						$table_references = null;
						//$table_references = $this->ortyd->get_table_reference($table,$rowsdata['name']);
						if($table_references != null){
							$variable = $rows[$table_references[0].'_'.$table_references[2]];
							$row[] = $variable;
						}elseif($rowsdata['name'] == 'ticket_no'){
							$variable = '<a href="'.base_url('data_ticket/replydata/').$identity_id.'">'.$rows[$rowsdata['name']].'</a>';
							$row[] = $variable;
						}elseif($rowsdata['name'] == 'date' || $rowsdata['name'] == 'tanggal'){
							$variable = $this->ortyd->format_date($rows[$rowsdata['name']]);
							$row[] = $variable;
						}else{
							$variable = $rows[$rowsdata['name']];
							$row[] = $variable;
						}
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
		
		function get_data_ticket_replied(){

			$activateddata = array('Inactive','Active');
			$table = $this->tabledb;
			$column_order = array(null,'data_ticket_detail.created');
			$column_search = array('data_ticket_detail.subjek');
			$order = array('data_ticket_detail.created' => 'DESC');
			$select = 'data_ticket.*, master_ticket_kategori.name as kategori_name, master_ticket_status.name as status_name, master_ticket_status.color as status_color, users_data.fullname, data_ticket_detail.subjek as balasan, data_ticket_detail.created as balasan_tanggal, balasan_user.fullname as balasan_fullname';
			
			$jointable = array('master_ticket_kategori','master_ticket_status','users_data','data_ticket_detail','users_data balasan_user');
			$joindetail = array('master_ticket_kategori.id = data_ticket.kategori_id','master_ticket_status.id = data_ticket.status_id','users_data.id = data_ticket.createdid','data_ticket_detail.ticket_id = data_ticket.id','balasan_user.id = data_ticket_detail.createdid');
			$joinposition = array('left','left','left','inner','left');
			
			$wherecolumn = array();
			$wheredetail = array();
			
			array_push($wherecolumn, 'data_ticket_detail.active');
			array_push($wheredetail, 1);
			
			array_push($wherecolumn, 'data_ticket_detail.ticket_id');
			array_push($wheredetail,  $this->input->post('ticket_id'));
			
			$groupby = array();
		
			$list = $this->ortyd->get_datatables($table,$column_order,$column_search,$order,$select,$jointable,$joindetail,$joinposition, $wherecolumn,$wheredetail,$groupby);
			$data = array();
			$no = $_POST['start'];
			foreach ($list as $rows) {
				$no++;
				$row = array();
				$date = date_create($rows->balasan_tanggal);
				$date = date_format($date,'Y-m-d H:i:s');
				$new_time = date("Y-m-d H:i:s", strtotime('+7 hours', strtotime($date)));
				$row[] = '<div class="row"> <div class="col-lg-2 col-md-2"> <img src="'.base_url().'image/user.png" style="width:50px;"/><br><small>'.$rows->balasan_fullname.'</small><br><span style="font-size: 8px;">'.$new_time.'<br></div><div class="col-lg-10 col-md-10">'.$rows->balasan.'</span></div></div>';
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
			$data['title'] = $this->titlechilddb;
			$data['title_tipe'] = 'Buat';
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
			$data['title'] = $this->titlechilddb;
			$data['title_tipe'] = 'Edit';
			$data['id'] = $ID;
			$data['module'] = $this->module;
			$data['modeldb'] = $this->m_model_data;
			$data['exclude'] = $this->exclude;
			$data['headurl'] = $this->headurldb;
			$data['datarow'] = $this->m_model_data->get_data_byid($data['id'], $this->tabledb, $this->tableid);
			$data['action'] = base_url().$this->actionurl.'/'.$data['id'];
			$this->template->load('main',$this->viewformname, $data);
		}
		
		public function replydata($ID)
		{
			$this->ortyd->access_check_update($this->module);
			$ID = $this->ortyd->select2_getname($ID,$this->tabledb,$this->field,$this->tableid);
			$data['title'] = $this->titlechilddb;
			$data['title_tipe'] = 'View';
			$data['id'] = $ID;
			$data['module'] = $this->module;
			$data['modeldb'] = $this->m_model_data;
			$data['exclude'] = $this->exclude;
			$data['headurl'] = $this->headurldb;
			$data['datarow'] = $this->m_model_data->get_data_byid($data['id'], $this->tabledb, $this->tableid);
			$data['action'] = base_url().$this->actionurlbalasan.'/0';
			$data['linkdata'] = $this->urlparent.'/get_data_ticket_replied';
			$data['actiontutup'] = base_url().$this->actionurlbalasantutup.'/'.$data['id'];
			$this->template->load('main',$this->viewformnamereply, $data);
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
							if($rows_column["name"] == 'nilai'){
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
							array('file_id' 		=> 0),
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
							//redirect($this->headurldb.'?message=error', 'refresh');
							$result = array("csrf_hash" => $this->security->get_csrf_hash(),"status" => "error");
							echo json_encode($result);
						}else{
							$this->db->trans_commit();
							if($update){
								//$this->saveUsers($id);
								$this->savekeyword($id, strip_tags($this->input->post('subjek')));
					
								$evidence = $this->input->post('evidence');
								if($evidence != '' && $evidence != null){
									$this->ortyd->saveEvidence($id,$evidence,$this->urlparent);
								}
								//redirect($this->headurldb.'?message=success', 'refresh');
								$result = array("csrf_hash" => $this->security->get_csrf_hash(),"status" => "success");
								echo json_encode($result);
							}else{
								//redirect($this->headurldb.'?message=error', 'refresh');
								$result = array("csrf_hash" => $this->security->get_csrf_hash(),"status" => "error");
								echo json_encode($result);
							}
						}
					}else{
						//redirect($this->headurldb.'?message=error', 'refresh');
						$result = array("csrf_hash" => $this->security->get_csrf_hash(),"status" => "error");
						echo json_encode($result);
					}
					
				}else{
					$this->ortyd->access_check_insert($this->module);
					if($query_column){
						foreach($query_column as $rows_column){
							if($rows_column["name"] == 'nilai'){
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
							array('status_id' 		=> 1),
							array('file_id' 		=> 0),
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
							//redirect($this->headurldb.'?message=error', 'refresh');
							$result = array("csrf_hash" => $this->security->get_csrf_hash(),"status" => "error");
							echo json_encode($result);
						}else{
							$this->db->trans_commit();
							if($insert){
								$identity = $this->input->post($this->field,true);
								
								//$this->saveUsers($insert_id);
								$this->savekeyword($insert_id, strip_tags($this->input->post('subjek')));
								
								//$insert_id = $this->ortyd->select2_getname($identity,$this->tabledb,$this->field,$this->tableid);
								$evidence = $this->input->post('evidence');
								if($evidence != '' && $evidence != null){
									$this->ortyd->saveEvidence($insert_id,$evidence,$this->urlparent);
								}
								//redirect($this->headurldb.'?message=success', 'refresh');
								$result = array("csrf_hash" => $this->security->get_csrf_hash(),"status" => "success");
								echo json_encode($result);
							}else{
								//redirect($this->headurldb.'?message=error', 'refresh');
								$result = array("csrf_hash" => $this->security->get_csrf_hash(),"status" => "error");
								echo json_encode($result);
							}
						}
					}else{
						//redirect($this->headurldb.'?message=error', 'refresh');
						$result = array("csrf_hash" => $this->security->get_csrf_hash(),"status" => "error");
						echo json_encode($result);
					}
				}
			}else{
				//redirect($this->headurldb.'?message=error', 'refresh');
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
			$reference = $this->input->post('reference',true);
			$q = $this->input->post('q',true);
			
			if(!$q){
				$q = '';
			}
			
			echo $this->ortyd->select2custom($id,$name,$q,$table,$reference);
			
		}
		
		public function proses_upload(){
			$token = $this->input->post('token_foto');
			$user_id = $this->session->userdata('userid');
			$namefield = "userfile";
			echo $this->ortyd->proses_upload($token, $user_id, $namefield);
		}
		
		public function getcover(){
			$id =$this->input->post('id',true);
			$tableid =$this->input->post('tableid',true);
			$table =$this->urlparent;

			echo $this->ortyd->getcover($id,$tableid,$table);
		}
		
		function saveUsers($ticket_id){
			$user_id_to = $this->input->post('user_id_to');	
			if($user_id_to){
				for($y=0;$y<=count($user_id_to)-1;$y++){

					$data = array(
						'ticket_id' 		=> $ticket_id,
						'from_id' 			=> $this->session->userdata('userid'),
						'to_id' 			=> $user_id_to[$y],
						'active' 			=> $this->input->post('active',true),
						'createdid'			=> $this->session->userdata('userid'),
						'created'			=> date('Y-m-d H:i:s'),
						'modifiedid'		=> $this->session->userdata('userid'),
						'modified'			=> date('Y-m-d H:i:s')
					);

					$insert = $this->db->insert('data_ticket_user', $data);
				}
			}
		}
		
		function savekeyword($ticket_id, $subjek){
			
			$subjek = explode(" ",trim($subjek));
			
			if(count($subjek) > 0){
				for($x=0;$x<=count($subjek) - 1;$x++){
					$keyword = $subjek[$x];
					$this->db->where('lower(keyword)', strtolower($keyword));
					$this->db->where('ticket_id', $ticket_id);
					$query = $this->db->get('data_ticket_keyword');
					$query = $query->result_object();
					if(!$query && $keyword != ''){
						$data = array(
							'ticket_id' 			=> $ticket_id,
							'keyword' 			=> $keyword,
							'active' 			=> 1,
							'createdid'			=> $this->session->userdata('userid'),
							'created'			=> date('Y-m-d H:i:s'),
							'modifiedid'		=> $this->session->userdata('userid'),
							'modified'			=> date('Y-m-d H:i:s')
						);

						$insert = $this->db->insert('data_ticket_keyword', $data);
					}
				}
			}
			
		}
		
		public function actiondata_ticket_balasan($id){
		
			if($id != 0){
				$this->ortyd->access_check_update($this->module);
				$data = array(
						'ticket_id' 		=> $this->input->post('ticket_id'),
						'subjek' 			=> $this->input->post('balasan'),
						'active' 			=> 1,
						'modifiedid'		=> $this->session->userdata('userid'),
						'modified'			=> date('Y-m-d H:i:s')
				);

				$this->db->where('id', $id);
				$update = $this->db->update('data_ticket_detail', $data);
				
				if($update){
					
					$this->ortyd->access_check_update($this->module);
					$data = array(
							'status_id' 		=> 2,
							'modifiedid'		=> $this->session->userdata('userid'),
							'modified'			=> date('Y-m-d H:i:s')
					);

					$this->db->where('id', $this->input->post('ticket_id'));
					$update = $this->db->update($this->tabledb, $data);

					$result = array("csrf_hash" => $this->security->get_csrf_hash(),"message" => "success");
					echo json_encode($result);
				}else{
					$result = array("csrf_hash" => $this->security->get_csrf_hash(),"message" => "error");
					echo json_encode($result);
				}
				
			}else{
				$this->ortyd->access_check_insert($this->module);
				$data = array(
						'ticket_id' 			=> $this->input->post('ticket_id'),
						'subjek' 			=> $this->input->post('balasan'),
						'active' 			=> 1,
						'createdid'			=> $this->session->userdata('userid'),
						'created'			=> date('Y-m-d H:i:s'),
						'modifiedid'		=> $this->session->userdata('userid'),
						'modified'			=> date('Y-m-d H:i:s')
				);

				$insert = $this->db->insert('data_ticket_detail', $data);
				$insert_id = $this->db->insert_id();
				 
				if($insert){
					
					$this->ortyd->access_check_update($this->module);
					$data = array(
							'status_id' 		=> 2,
							'modifiedid'		=> $this->session->userdata('userid'),
							'modified'			=> date('Y-m-d H:i:s')
					);

					$this->db->where('id', $this->input->post('ticket_id'));
					$update = $this->db->update($this->tabledb, $data);
					
					$result = array("csrf_hash" => $this->security->get_csrf_hash(),"message" => "success");
					echo json_encode($result);
				}else{
					$result = array("csrf_hash" => $this->security->get_csrf_hash(),"message" => "error");
					echo json_encode($result);
				}
				
			}
		}
		
		public function actiondata_ticket_tutup($id){
			

			if($id != 0){
				$this->ortyd->access_check_update($this->module);
				$data = array(
						'status_id' 		=> 3,
						'berakhir' 			=> date('Y-m-d'),
						'modifiedid'		=> $this->session->userdata('userid'),
						'modified'			=> date('Y-m-d H:i:s')
				);

				$this->db->where('id', $id);
				$update = $this->db->update($this->tabledb, $data);
				
				if($update){
					redirect($this->headurldb.'?message=success', 'refresh');
				}else{
					redirect($this->headurldb.'?message=error', 'refresh');
				}
				
			}
		}
		
		
}
