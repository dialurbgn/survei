<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class M_dashboard extends CI_Model {
	
		public function __construct()
		{
			parent::__construct();
		}
		
		
public function is_column_nullable($table, $column)
{
    $db_driver = $this->db->dbdriver;
    
    if($db_driver == 'postgre' || $db_driver == 'postgresql'){
        // PostgreSQL
        $this->db->select('is_nullable');
        $this->db->from('information_schema.columns');
        $this->db->where('table_schema', 'public');
        $this->db->where('table_name', $table);
        $this->db->where('column_name', $column);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            $row = $query->row();
            return $row->is_nullable === 'YES'; // TRUE jika NULL diperbolehkan
        }
        return null; // kolom tidak ditemukan
        
    }else{
        // MySQL
        $database = $this->db->database;
        $this->db->select('IS_NULLABLE');
        $this->db->from('INFORMATION_SCHEMA.COLUMNS');
        $this->db->where('TABLE_SCHEMA', $database);
        $this->db->where('TABLE_NAME', $table);
        $this->db->where('COLUMN_NAME', $column);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            $row = $query->row();
            return $row->IS_NULLABLE === 'YES'; // TRUE jika NULL diperbolehkan
        }
        return null; // kolom tidak ditemukan
    }
}
		
		function updateRequiredField($module, $column, $required){
			
			$tipe_id = $this->updateRequiredFieldmaster($module);
			
			$this->db->where('field',$column);
			$this->db->where('tipe_id',$tipe_id);
			$query = $this->db->get('master_lop_field_required');
			$query = $query->result_object();
			if(!$query){
				
				$string = $column.'-'.$module;
				$slug = $this->ortyd->sanitize($string,'master_lop_field_required');
				
				$datacmcode = array(
					'field' 		=> $column,
					'tipe_id' 		=> $tipe_id,
					'slug' 			=> $slug,
					'created' 		=> date('Y-m-d H:i:s'),
					'createdid' 	=> $this->session->userdata('userid'),
					'modified' 		=> date('Y-m-d H:i:s'),
					'modifiedid' 	=> $this->session->userdata('userid'),
					'active' 		=> $required
				);
									
				$updatecmcode = $this->db->insert('master_lop_field_required', $datacmcode);
				if($updatecmcode){
					return true;
				}
			
			}else{
				
				$datacmcode = array(
					'modified' 		=> date('Y-m-d H:i:s'),
					'modifiedid' 	=> $this->session->userdata('userid'),
					'active' 		=> $required
				);
							
				$this->db->where('id',$query[0]->id);
				$updatecmcode = $this->db->update('master_lop_field_required', $datacmcode);
				if($updatecmcode){
					return true;
				}
				
			}
			
			return false;
		}
		
		function updateRequiredFieldmaster($module){
			$this->db->where('module',$module);
			$query = $this->db->get('master_lop_field_required_tipe');
			$query = $query->result_object();
			if(!$query){
				
				$string = $module.date('Ymd');
				$slug = $this->ortyd->sanitize($string,'master_lop_field_required_tipe');
				
				$datacmcode = array(
					'name'		 	=> $module,
					'module'		=> $module,
					'slug'			=> $slug,
					'created' 		=> date('Y-m-d H:i:s'),
					'createdid' 	=> $this->session->userdata('userid'),
					'modified' 		=> date('Y-m-d H:i:s'),
					'modifiedid' 	=> $this->session->userdata('userid'),
					'active' 		=> 1
				);
									
				$insert = $this->db->insert('master_lop_field_required_tipe', $datacmcode);
				$insertid = $this->db->insert_id();
				if($insert){
					return $insertid;
				}
			
			}else{
				return $query[0]->id;
			}
			
			return null;
		}

		function uploadBase64_new()
		{

					$file = $this->input->post('image64');
					$user_id = $this->input->post('user_id',true);
					$id = $this->input->post('id',true);
					
					$dir = './file/thumbnail/'.date('Y').'/'.date('m').'/'.date('d');
					
					if(!file_exists($dir)){
					  mkdir($dir,0755,true);
					}

					$path = 'file/thumbnail/'.date('Y').'/'.date('m').'/'.date('d');

					if (preg_match('/^data:image\/(\w+);base64,/', $file, $type)) {
						$file = substr($file, strpos($file, ',') + 1);
						$type = strtolower($type[1]); // jpg, png, gif

						if (!in_array($type, [ 'jpg', 'jpeg', 'gif', 'png' ])) {
							$data['status'] = 'error';	
							$data['errors'] = 'Invalid Image Type';
							return $json_encode($data);
							die();
						}

						$file = base64_decode($file);

						if ($file === false) {
							$data['status'] = 'error';	
							$data['errors'] = 'base64_decode failed';
							return $json_encode($data);
							die();
						}
					} else {
						$data['status'] = 'error';	
						$data['errors'] = 'did not match data URI with image data';
						return $json_encode($data);
						die();
					}
					
					$nama = date('YmdHis').$user_id.'.'.$type;
					$status = file_put_contents($path .'/'.$nama,$file);
					if($status){
						$token	=	'0.'.date('YmdHis').$user_id;
						$size	=	(int)(strlen(rtrim($file, '=')) * 3 / 4) / 1000;
						
						$data = array(
							'name'			=> $nama,
							'file_size'		=> $size * 1000,
							'token'			=> $token,
							'path'			=> $path .'/'.$nama,
							'path_server'	=> $dir .'/'.$nama,
							'createdid'		=> $user_id,
							'created'		=> date('Y-m-d H:i:s'),
							'modifiedid'	=> $user_id,
							'modified'		=> date('Y-m-d H:i:s'),
							'url_server'	=> base_url()
						);
							
						$insert = $this->db->insert('data_gallery',$data);
						$insertid = $this->db->insert_id();
						
						if($insert){
							
							$this->db->where('data_gallery.id', $id);
							$querystatus = $this->db->get('data_gallery');	
							$querystatus = $querystatus->result_object();
							if($querystatus){
								$dataremove = array(
									'thumbnail_id' 	=> $insertid,
									'modifiedid'	=> $user_id,
									'modified'		=> date('Y-m-d H:i:s')
								);
															
								$this->db->where('data_gallery.id', $querystatus[0]->id);
								$updateactive = $this->db->update('data_gallery', $dataremove);
								
								$data['status'] = 'success';	
								$data['errors'] = '-';
								$data['id'] = $insertid;
							
							}else{
								$data['status'] = 'error';	
								$data['errors'] = 'Data not insert review';
							}
							
							
						}else{
							$data['status'] = 'error';	
							$data['errors'] = 'Data not insert storage';
						}
					}else{
						$data['status'] = 'error';	
						$data['errors'] = 'Upload Errors';
					}
				
			return json_encode($data);
		}
		
		
		public function proses_upload(){
			
			return $this->ortyd->proses_upload_dok();

		}
		
		
		function getcover($urlparent){
			
			$this->load->helper('shorten_encryption');
			
			$fieldnya = array();
			//$perusahaan_id =$this->input->post('id',true);
			$id =$this->input->post('id',true);
			$tableid =$this->input->post('tableid',true);
			$table =$urlparent;

			$this->db->select('data_gallery.*, data_dokumen.id as evidence_id');
			$this->db->where('data_dokumen.table', $table);
			$this->db->where('data_dokumen.tableid', $tableid);
			$this->db->where('data_dokumen.data_id', $id);
			$this->db->where('data_dokumen.active',1);
			$this->db->join('data_gallery','data_dokumen.file_id = data_gallery.id');
			$this->db->join('data_gallery thumb','thumb.id = data_gallery.thumbnail_id','left');
			$querystatus = $this->db->get('data_dokumen');
			$querystatus = $querystatus->result_object();
			//print_r($this->db->last_query());
			if($querystatus){
				foreach ($querystatus as $rows) {
					
					//$row[] = $no;
					$link = base_url().'data_gallery/viewdokumen?path='.$rows->path.'&tipe='.$rows->file_store_format.'&token='.$rows->token;
					$encodedlink = encrypt_short($link);
						
					$datanya = array(
						'id' => $rows->id,
						'evidence_id' => $rows->evidence_id,
						'name' => $rows->name, 	
						'path' => $rows->url_server.$rows->path,
						'size' => $rows->file_size/1000, 		
						'link' => base_url().'dokumenview/'.$encodedlink,						
						"extention" =>  $rows->file_store_format, 
						"last_query"=>$this->db->last_query()
					);
					
					array_push($fieldnya, $datanya);
				}
				
				$result = array("csrf_hash" => $this->security->get_csrf_hash(),"message" => "success",'data' => $fieldnya);
				return json_encode($result);
			}else{
				$result = array("csrf_hash" => $this->security->get_csrf_hash(),"message" => "error","last_query"=>$this->db->last_query());
				return json_encode($result);
			}
			
		}
		
		public function deleteFile(){
			//$this->ortyd->access_check_update($this->module);
			
			$id =$this->input->post('id',true);
			$tableid =$this->input->post('tableid',true);
			$table =$this->urlparent;
			
			
			$this->db->where('data_dokumen.table', $table);
			$this->db->where('data_dokumen.tableid', $tableid);
			$this->db->where('data_dokumen.data_id', $id);
			$this->db->where('active',1);
			$query = $this->db->get('data_dokumen');
			$query = $query->result_object();
			if($query){
				
				$this->db->trans_begin();
				
				$dataremove = array(
					'active' 			=> 0,
					'modifiedid'		=> $this->session->userdata('userid'),
					'modified'			=> date('Y-m-d H:i:s')
				);

				$this->db->where('id', $id);
				$updateactive = $this->db->update('data_dokumen', $dataremove);
				
				$this->db->trans_complete();
				if ($this->db->trans_status() === FALSE){
					$this->db->trans_rollback();
					$result = array("csrf_hash" => $this->security->get_csrf_hash(),"message" => "error");
					return json_encode($result);
				}else{
					if($updateactive){
						$result = array("csrf_hash" => $this->security->get_csrf_hash(),"message" => "success");
						return json_encode($result);
					}else{
						$result = array("csrf_hash" => $this->security->get_csrf_hash(),"message" => "error");
						return json_encode($result);
					}
				}
			}else{
				$result = array("csrf_hash" => $this->security->get_csrf_hash(),"message" => "error");
				return json_encode($result);
			}

		}
		
		
		public function saveEvidence($data_id, $urlparent) {

			$evidence = $this->input->post('evidence');
			$table = $urlparent;

			// Nonaktifkan dulu semua file sebelumnya
			$this->db->where('table', $table);
			$this->db->where('data_id', $data_id);
			$this->db->update('data_dokumen', ['active' => 0]);

			if (!empty($evidence)) {
				$evidence_detail = $evidence;
				foreach ($evidence_detail as $key_ev => $n_ev) {
					if (!empty($n_ev)) {
						$datadetail_ev = array(
							$key_ev         => $n_ev,
							'active'        => 1,
							'modifiedid'    => $this->session->userdata('userid')  ?? 0,
							'modified'      => date('Y-m-d H:i:s')
						);

						$this->db->where('id', $data_id);
						$this->db->update($table, $datadetail_ev);

						$this->db->where('data_dokumen.table', $table);
						$this->db->where('data_dokumen.tableid', $key_ev);
						$this->db->where('data_dokumen.file_id', $n_ev);
						$queryev = $this->db->get('data_dokumen')->result();

						$datadetail_ev = array(
							'table'         => $table,
							'tableid'       => $key_ev,
							'file_id'       => $n_ev,
							'data_id'       => $data_id,
							'active'        => 1,
							'modifiedid'    => $this->session->userdata('userid') ?? 0,
							'modified'      => date('Y-m-d H:i:s')
						);

						if (!$queryev) {
							$datadetail_ev['createdid'] = $this->session->userdata('userid')  ?? 0;
							$datadetail_ev['created']   = date('Y-m-d H:i:s');
							$this->db->insert('data_dokumen', $datadetail_ev);
						} else {
							$this->db->where('id', $queryev[0]->id);
							$this->db->update('data_dokumen', $datadetail_ev);
						}
					}
				}
			}
		}

		
		
		function getcoverdata($id){
			$fieldnya = array();
			//$perusahaan_id =$this->input->post('id',true);
			//$id =$this->input->post('id',true);
			//$tableid =$this->input->post('tableid',true);
			
			$this->db->select('data_gallery.*');
			$this->db->where('data_gallery.id',$id);
			$querystatus = $this->db->get('data_gallery');
			$querystatus = $querystatus->result_object();
			//print_r($this->db->last_query());
			if($querystatus){
				foreach ($querystatus as $rows) {
					$datanya = array(
						'id' => $rows->id,
						'name' => $rows->name, 	
						'path' => $rows->url_server.$rows->path,
						'size' => $rows->file_size/1000, 							
						"extention" =>  $rows->file_store_format, 
					);
					
					array_push($fieldnya, $datanya);
				}
				
				$result = array("csrf_hash" => $this->security->get_csrf_hash(),"message" => "success",'data' => $fieldnya);
				return $result;
			}else{
				$result = array("csrf_hash" => $this->security->get_csrf_hash(),"message" => "error");
				return $result;
			}
			
		}
		
}	