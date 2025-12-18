<?php
//CONTROLLER BY HANAFI GINTING

defined('BASEPATH') OR exit('No direct script access allowed');

class Data_gallery extends MX_Controller {

		//CONFIG VARIABLE
		private $urlparent = 'data_gallery'; //NAME TABLE 
		private $identity_id = 'id'; //IDENTITY TABLE
		private $field = 'id'; // IDENTITY FROM NAME FOR GET ID
		private $slug_indentity = 'name'; //NAME FIELD 
		private $sorting = 'modified'; // SORT FOR VIEW
		private $exclude = array('token','path','path_server','file_size','created','modified','createdid','modifiedid','active','url_server','file_store_is','file_store_type','file_store_id','file_store_format','thumbnail_id','table','tableid','tabledataid','is_temp','latitude','longitude');
		private $exclude_table = array('token','path','path_server','file_size','created','modified','createdid','modifiedid','active','url_server','file_store_is','file_store_type','file_store_id','file_store_format','thumbnail_id','table','tableid','tabledataid','is_temp','latitude','longitude');
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
			
			$this->load->helper('shorten_encryption');
			$this->load->helper('download');
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
			
		}
		
		public function index()
		{
			$this->ortyd->access_check($this->module);
			$data['title'] = $this->titlechilddb;
			$data['module'] = $this->module;
			$data['tabledb'] = $this->tabledb;
			$data['identity_id'] = $this->identity_id;
			$data['slug_indentity'] = $this->slug_indentity;
			$data['exclude_table'] = $this->exclude_table;
			$data['headurl'] = $this->headurldb;
			$data['linkdata'] = $this->urlparent.'/get_data';
			$data['linkcreate'] = '#';
			$this->template->load('main',$this->viewname, $data);
		}
		
		function get_data(){
			$this->ortyd->access_check($this->module);
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
			
			//array_push($wherecolumn, $table.'.active');
			//array_push($wheredetail, $this->input->post('active',true));

			$groupby = array();
		
			$list = $this->ortyd->get_datatables($table,$column_order,$column_search,$order,$select,$jointable,$joindetail,$joinposition, $wherecolumn,$wheredetail,$groupby);
			$data = array();
			$no = $_POST['start'];
			foreach ($list as $rows) {
				$rows = (array) $rows;
				$no++;
				$row = array();
				//$row[] = $no;
				$link = $rows['url_server'].'data_gallery/viewdokumen?path='.$rows['path'].'&tipe='.$rows['file_store_format'].'&token='.$rows['token'];
				$encodedlink = encrypt_short($link);
				
				$identity_id = $rows[$this->identity_id];
				$uuid = "'". $rows[$this->identity_id]."'";
				
				$viewdata = '<div target="_blank" class="menu-item px-3"><a class="dropdown-item" href="'.base_url().'dokumenview/'.$encodedlink.'"><i class="fa fa-eye text-info mt-1"></i> View Gallery</a></div> ';
				
				if($this->ortyd->access_check_update_data($this->module)){

					$editdata = '';
									
					$restoredata = '';
						
				}else{
					$editdata = '';
					$restoredata = '';
				}
					
					
				if($this->ortyd->access_check_delete_data($this->module)){
					
					$deletedata = '';
						
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
							'.$viewdata.'
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
							'.$viewdata.'
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
						}elseif($rowsdata['name'] == 'name'){
							$variable = '<a target="_blank" href="'.base_url().'dokumenview/'.$encodedlink.'">'.$rows[$rowsdata['name']].'</a>';
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
		
		public function viewdata($linkdata)
		{
			$decoded = decrypt_short($linkdata);
			$parts = parse_url($decoded); // Pisahkan bagian URL
			parse_str($parts['query'], $queryParams); // Ambil parameter dari query
			
			$path = $queryParams['path'];   // myfile.xlsx
			$tipe   = $queryParams['tipe'];     // 123
			$token   = $queryParams['token'];     // 123

			$this->db->where('path',$path);
			$this->db->where('token',$token);
			$this->db->where('file_store_format',$tipe);
			$query = $this->db->get($this->tabledb);
			$query = $query->result_object();
			if($query){
				
				if($query[0]->file_store_format == 'xlsx' || $query[0]->file_store_format == 'xls' || $query[0]->file_store_format == 'pdf' || $query[0]->file_store_format == 'jpg' || $query[0]->file_store_format == 'png' || $query[0]->file_store_format == 'jpeg'){
					$data['title'] = $query[0]->name;
					$data['id'] = $query[0]->id;
					$data['module'] = $this->module;
					$data['modeldb'] = $this->m_model_data;
					$data['exclude'] = array('id');
					$data['headurl'] = $this->headurldb;
					$data['datarow'] = $this->m_model_data->get_data_byid($data['id'], $this->tabledb, $this->tableid);
					$data['action'] = base_url().$this->actionurl.'/'.$data['id'];
					$this->template->load('main',$this->viewformname, $data);
				}else{
					if($query[0]->path != null){
						$path = FCPATH . $query[0]->path; // Path lengkap ke file
						$data = file_get_contents($path); // Baca isi file
						$name = $query[0]->name.'.'.$query[0]->file_store_format; // Nama file yang akan di-download user
						force_download($name, $data);
					}else{
						redirect('404','refresh');
					}
				}
				
			}else{
				redirect('404','refresh');
			}
			
		}

		
}
