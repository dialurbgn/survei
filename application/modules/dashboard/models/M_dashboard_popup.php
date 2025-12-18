<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class M_dashboard_popup extends CI_Model {
	
		private $data_exclude = [
			
		];
		
		public function __construct()
		{
			parent::__construct();
		}
		
		
		public function getColumn(){
			$column = array();
			$filter = $this->input->post('filter',true);
			$id =  $this->input->post('id',true);
			$categories = $this->input->post('categories',true);
			$dataset = $this->input->post('dataset',true);
			$columndata = 1;
			
			if($id || !$id){
				
				if($id == 1 || $id == 9){
					$table = 'vw_data_laporan_patrolisiber';
					$exclude = $this->data_exclude;
				}elseif($id == 10){
					$table = 'vw_data_laporan_pengawasan';
					$exclude = $this->data_exclude;
				}elseif($id == 5){
					$table = 'vw_data_laporan_daftar_hitam';
					$exclude = $this->data_exclude;
				}elseif($id == 6){
					$table = 'vw_data_laporan_daftar_prioritas';
					$exclude = $this->data_exclude;
				}else{
					$table = 'vw_data_laporan_patrolisiber';
					$exclude = $this->data_exclude;
				}
				
				$columndata == 1;
				
				if($columndata == 1){
					
						$query_column = $this->ortyd->query_column_include_nosort($table, $exclude);
						//return $this->db->last_query();
						if($query_column){
							array_push($column, array("title" =>'No', "className" => "alignleft"));
							foreach($query_column as $rows){
								$label_name = $this->ortyd->translate_column($table,$rows['name']);	
								array_push($column, array("title" => $label_name, "className" => "alignleft"));
							}
						}
					
				}
				
				
				
			}
			
			$output = array(
				"status" => 'success',
				"column" => $column,
				"csrf_hash" => $this->security->get_csrf_hash()
			);
			
			return json_encode($output);
		}
		
		public function getColumnDetail(){
			//$filter = $this->input->post('filter',true);
			$id =  $this->input->post('id',true);
			$categories = $this->input->post('categories',true);
			$dataset = $this->input->post('dataset',true);
			$tahun = $this->input->post('tahun',true);
			$project_tipe = $this->input->post('project_tipe',true);
			$tipenya = $project_tipe.' '.$tahun;
			
			$filter = array(
				"tahun" => $tahun,
				"project_tipe" => $project_tipe,
				"tipenya" => $tipenya
			);

			if($id || !$id){
				
				if($id == 1 || $id == 9){
					$table = 'vw_data_laporan_patrolisiber';
					$exclude = $this->data_exclude;
					$sorting = 'created';
					$data = 1;
					
					return $this->getdetail($id, $table, $exclude, $sorting , $data, $categories, $dataset,$filter);
				}elseif($id == 10){
					$table = 'vw_data_laporan_pengawasan';
					$exclude = $this->data_exclude;
					$sorting = 'created';
					$data = 1;
					
					return $this->getdetail($id, $table, $exclude, $sorting , $data, $categories, $dataset,$filter);
				}elseif($id == 5){
					$table = 'vw_data_laporan_daftar_hitam';
					$exclude = $this->data_exclude;
					$sorting = 'created';
					$data = 1;
					
					return $this->getdetail($id, $table, $exclude, $sorting , $data, $categories, $dataset,$filter);
				}elseif($id == 6){
					$table = 'vw_data_laporan_daftar_prioritas';
					$exclude = $this->data_exclude;
					$sorting = 'created';
					$data = 1;
					
					return $this->getdetail($id, $table, $exclude, $sorting , $data, $categories, $dataset,$filter);
				}else{
					$table = 'vw_data_laporan_patrolisiber';
					$exclude = $this->data_exclude;
					$sorting = 'created';
					$data = 1;
					
					return $this->getdetail($id, $table, $exclude, $sorting , $data, $categories, $dataset,$filter);
				}
				
				
				
			}
			
		}
		
		
		function getdetail($id, $table, $exclude, $sorting , $data, $categories, $dataset,$filter){

			if($id == 0){
				

			}else{
				
				$query_column = $this->ortyd->query_column_include_nosort($table, $exclude);
				if($query_column){
					$ordernya = array(null);
					$searchnya = array();
					foreach($query_column as $rowsdata){
						array_push($ordernya,$rowsdata['name']);
						array_push($searchnya,$rowsdata['name']);
					}
					$column_order = $ordernya;
					$column_search = $searchnya;
				}else{
					$column_order = array(null);
					$column_search = array(null);
				}
				
				$order = array($table.'.'.$sorting => 'DESC');
				$select = $table.'.*';
				
				$jointable = array();
				$joindetail = array();
				$joinposition = array();
				
				$wherecolumn = array();
				$wheredetail = array();
				
				if($this->session->userdata('group_id') == 3){
					array_push($wherecolumn, $table.'.ppmse_id');
					array_push($wheredetail, $this->session->userdata('ppmse_id'));
				}

				if($id == 1){

					if($categories != '0' && $categories != ''){
						$bulan_code = $this->ortyd->select2_getname($categories,'master_bulan','code','id');
						array_push($wherecolumn, $table.'.bulan');
						array_push($wheredetail, $bulan_code);
					
						//array_push($wherecolumn, $table.'.bulan_won');
						//array_push($wheredetail, $categories);
						
					}else{
						
					}
					
					if($dataset != '0' && $dataset != ''){
						//array_push($wherecolumn, 'lower('.$table.'.status)');
						//array_push($wheredetail, strtolower($dataset));
					}

				}
				
				array_push($wherecolumn, $table.'.tahun');
				array_push($wheredetail, $filter['tahun']);

				$groupby = array();
			
				$list = $this->ortyd->get_datatables($table,$column_order,$column_search,$order,$select,$jointable,$joindetail,$joinposition, $wherecolumn,$wheredetail,$groupby);
				$data = array();
				$no = $_POST['start'];
				//echo $this->db->last_query();
				foreach ($list as $rows) {
					$rows = (array) $rows;
					$no++;
					$row = array();
					$row[] = $no;
					if($query_column){
						foreach($query_column as $rowsdata){
							if($rowsdata['name'] == 'lop_nilai' || $rowsdata['name'] == 'nilai' || $rowsdata['name'] == 'total'){
								$variable = $this->ortyd->rupiahnonkoma($rows[$rowsdata['name']]);
								$row[] = $variable;
							}elseif($rowsdata['name']){
								$variable = $rows[$rowsdata['name']];
								$row[] = $variable;
							}else{
								$variable = $rows[$rowsdata['name']];
								$row[] = $variable;
							}
							
						}
					}

					$data[] = $row;
				}
				
		 
				$output = array(
					"draw" => $_POST['draw'],
					"recordsTotal" => $this->ortyd->count_filtered($table,$column_order,$column_search,$order,$select,$jointable,$joindetail,$joinposition, $wherecolumn,$wheredetail,$groupby),
					"recordsFiltered" => $this->ortyd->count_filtered($table,$column_order,$column_search,$order,$select,$jointable,$joindetail,$joinposition, $wherecolumn,$wheredetail,$groupby),
					"data" => $data,
					"csrf_hash" => $this->security->get_csrf_hash()
				);
				
				echo json_encode($output);
				
			}
			
		}
		
	
}	