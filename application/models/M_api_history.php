<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class M_api_history extends CI_Model {
		
		public $link_pins = "https://api.pins.co.id/";
		
		public function __construct()
		{
			parent::__construct();
		}
		
		function getToken($link){
		
			$params = array(
				'username' => 'fauzi.hanif',
				'password' => 'Aussie@2025'
			);
			
			$data = $this->curl->simple_post($link.'auth/token/request',$params,array(
							CURLOPT_TIMEOUT => 50000,
							CURLOPT_SSL_VERIFYPEER => false
						)
					);
					
			$info = $this->curl->info;
			$rowcode = $info['http_code'];
			//die();
					
			if($data){
				$data = json_decode($data);
				$rowdata = $data->data;
				$rowcode = $rowcode;
				return $rowdata->access_token;
			}
			
			return null;
			
		}
		
		public function update_db_user_info($imgdata) {
		   $imgdata = file_get_contents($imgdata['full_path']);//get the content of the 
		   return $imgdata;
	   } 
   
		function saveBAUT($project_mitra_id,$no,$tanggal,$file,$detail_file){
		
			try {
				//$file = $this->update_db_user_info($file);
				$token = $this->getToken($this->link_pins.'api/');
				
				//$file_name_with_full_path = realpath($file);
				$file_name_with_full_path = new CurlFile($file, 'pdf', 'file.pdf');
				
				$params = array(
					'project_mitra_id' => $project_mitra_id,
					'no' => $no,
					'tanggal' => $tanggal,
					'file' => $file_name_with_full_path
				);
				
				$data = $this->curl->simple_post($this->link_pins.'api/procurement/baut',$params,array(
								CURLOPT_HTTPHEADER => array('Authorization:Bearer '.$token.'','Content-Type:multipart/form-data'),
								CURLOPT_TIMEOUT => 50000,
								CURLOPT_SSL_VERIFYPEER => false,
								CURLOPT_POSTFIELDS => $params,
								CURLOPT_POST => 1,
								CURLOPT_RETURNTRANSFER => true,
							)
						);
						
				$info = $this->curl->info;
				$rowcode = $info['http_code'];
				
				//return $info;
				//die();
						
				if($data){
					$data = json_decode($data);
					$rowdata = $data->data;
					$rowcode = $rowcode;
					return $rowdata->id;
				}
				
				return 0;
			
			} catch (Exception $e) {
				return false;
			}
			
		}
	
		
		public function setMasterIO($io_id, $dataio){
			
			if($dataio != null){
					$format = array(
						'id'				=> ($dataio->id != '' ? $dataio->id : null),
						'internal_order'	=> ($dataio->internal_order != '' ? $dataio->internal_order : null),
						'deskripsi_order'	=> ($dataio->deskripsi_order != '' ? $dataio->deskripsi_order : null),
						'created_at'		=> ($dataio->created_at != '' ? $dataio->created_at : null),
						'updated_at'		=> ($dataio->updated_at != '' ? $dataio->updated_at : null),
						'io_format'			=> ($dataio->io_format != '' ? $dataio->io_format : null)
					);
					
					$this->db->where('id',$format['id']);
					$query = $this->db->get('master_io');
					$query = $query->result_object();
					if(!$query){
						$datarow = array(  
								"id" 					=> ($format['id'] != '' ? $format['id'] : null),
								"internal_order" 		=> ($format['internal_order'] != '' ? $format['internal_order'] : null),
								"deskripsi_order" 		=> ($format['deskripsi_order'] != '' ? $format['deskripsi_order'] : null),
								"io_format" 			=> ($format['io_format'] != '' ? $format['io_format'] : null),
								'active'				=> 1,
								'createdid'				=> 1,
								'created'				=> ($format['created_at'] != '' ? $format['created_at'] : null),
								'modifiedid'			=> 1,
								'modified'				=> ($format['updated_at'] != '' ? $format['updated_at'] : null)
						);
						
						$string = $format['io_format'];
						$slug = $this->ortyd->sanitize($string,'master_io');
						$datarow = array_merge($datarow,
							array('slug' 	=> $slug)
						);
						
						$insert = $this->db->insert('master_io', $datarow);
					}else{
						$datarow = array(  
								"id" 					=> ($format['id'] != '' ? $format['id'] : null),
								"internal_order" 		=> ($format['internal_order'] != '' ? $format['internal_order'] : null),
								"deskripsi_order" 		=> ($format['deskripsi_order'] != '' ? $format['deskripsi_order'] : null),
								"io_format" 			=> ($format['io_format'] != '' ? $format['io_format'] : null),
								'active'				=> 1,
								'createdid'				=> 1,
								'created'				=> ($format['created_at'] != '' ? $format['created_at'] : null),
								'modifiedid'			=> 1,
								'modified'				=> ($format['updated_at'] != '' ? $format['updated_at'] : null)
						);
						
						$string = $format['io_format'];
						$slug = $this->ortyd->sanitize($string,'master_io');
						//$datarow = array_merge($datarow,
							//array('slug' 	=> $slug)
						//);
						
						$this->db->where('id',$format['id']);
						$update = $this->db->update('master_io', $datarow);
					}
			
			}
			
			
			return true;
		}
		
		
		public function setCustomer($customer_id, $dataio){
			
			if($dataio != null){
					$format = array(
						'id'				=> ($dataio->id != '' ? $dataio->id : null),
						'deskripsi_customer'=> ($dataio->deskripsi_customer != '' ? $dataio->deskripsi_customer : null),
						'akun_customer'		=> ($dataio->akun_customer != '' ? $dataio->akun_customer : null),
						'alamat'			=> ($dataio->alamat != '' ? $dataio->alamat : null),
						'email'				=> ($dataio->email != '' ? $dataio->email : null),
						'no_tlpn'			=> ($dataio->no_tlpn != '' ? $dataio->no_tlpn : null),
						'fax'				=> ($dataio->fax != '' ? $dataio->fax : null),
						'customer_group_id'	=> ($dataio->customer_group_id != '' ? $dataio->customer_group_id : null),
						'channel'			=> ($dataio->channel != '' ? $dataio->channel : null),
						'website'			=> ($dataio->website != '' ? $dataio->website : null),
						'created_at'		=> ($dataio->created_at != '' ? $dataio->created_at : null),
						'updated_at'		=> ($dataio->updated_at != '' ? $dataio->updated_at : null),
						'deleted_at'		=> ($dataio->deleted_at != '' ? $dataio->deleted_at : null)
					);
					
					if($format['customer_group_id'] == 1){
						$is_telkom = 1;
					}else{
						$is_telkom = 0;
					}
					
					$this->db->where('id',$format['id']);
					$query = $this->db->get('master_customer');
					$query = $query->result_object();
					if(!$query){
						$datarow = array(  
							'id'				=> ($format['id'] != '' ? $format['id'] : null),
							'name'				=> ($format['deskripsi_customer'] != '' ? $format['deskripsi_customer'] : null),
							'description'		=> ($format['deskripsi_customer'] != '' ? $format['deskripsi_customer'] : null),
							'akun_customer'		=> ($format['akun_customer'] != '' ? $format['akun_customer'] : null),
							'alamat'			=> ($format['alamat'] != '' ? $format['alamat'] : null),
							'email'				=> ($format['email'] != '' ? $format['email'] : null),
							'no_tlpn'			=> ($format['no_tlpn'] != '' ? $format['no_tlpn'] : null),
							'fax'				=> ($format['fax'] != '' ? $format['fax'] : null),
							'customer_group_id'	=> ($format['customer_group_id'] != '' ? $format['customer_group_id'] : null),
							'channel'			=> ($format['channel'] != '' ? $format['channel'] : null),
							'website'			=> ($format['website'] != '' ? $format['website'] : null),
							'is_telkom'			=> $is_telkom,
							'active'			=> 1,
							'createdid'			=> 1,
							'created'			=> ($format['created_at'] != '' ? $format['created_at'] : null),
							'modifiedid'		=> 1,
							'modified'			=> ($format['updated_at'] != '' ? $format['updated_at'] : null)
						);
						
						$string = $format['deskripsi_customer'];
						$slug = $this->ortyd->sanitize($string,'master_customer');
						$datarow = array_merge($datarow,
							array('slug' 	=> $slug)
						);
						
						$insert = $this->db->insert('master_customer', $datarow);
					}else{
						$datarow = array(  
							'id'				=> ($format['id'] != '' ? $format['id'] : null),
							'name'				=> ($format['deskripsi_customer'] != '' ? $format['deskripsi_customer'] : null),
							'description'		=> ($format['deskripsi_customer'] != '' ? $format['deskripsi_customer'] : null),
							'akun_customer'		=> ($format['akun_customer'] != '' ? $format['akun_customer'] : null),
							'alamat'			=> ($format['alamat'] != '' ? $format['alamat'] : null),
							'email'				=> ($format['email'] != '' ? $format['email'] : null),
							'no_tlpn'			=> ($format['no_tlpn'] != '' ? $format['no_tlpn'] : null),
							'fax'				=> ($format['fax'] != '' ? $format['fax'] : null),
							'customer_group_id'	=> ($format['customer_group_id'] != '' ? $format['customer_group_id'] : null),
							'channel'			=> ($format['channel'] != '' ? $format['channel'] : null),
							'website'			=> ($format['website'] != '' ? $format['website'] : null),
							'is_telkom'			=> $is_telkom,
							'active'			=> 1,
							'createdid'			=> 1,
							'created'			=> ($format['created_at'] != '' ? $format['created_at'] : null),
							'modifiedid'		=> 1,
							'modified'			=> ($format['updated_at'] != '' ? $format['updated_at'] : null)
						);
						
						$string = $format['deskripsi_customer'];
						$slug = $this->ortyd->sanitize($string,'master_customer');
						//$datarow = array_merge($datarow,
							//array('slug' 	=> $slug)
						//);
						
						$this->db->where('id',$format['id']);
						$update = $this->db->update('master_customer', $datarow);
					}
			
			}
			
			
			return true;
		}
		
		
		public function setEndCustomer($end_customer_id, $dataio){
			
			if($dataio != null){
					$format = array(
						'id'				=> ($dataio->id != '' ? $dataio->id : null),
						'nama'=> ($dataio->nama != '' ? $dataio->nama : null),
						'nomor'		=> ($dataio->nomor != '' ? $dataio->akun_customer : null),
						'alamat'			=> ($dataio->alamat != '' ? $dataio->alamat : null),
						'email'				=> ($dataio->email != '' ? $dataio->email : null),
						'no_tlpn'			=> ($dataio->no_tlpn != '' ? $dataio->no_tlpn : null),
						'fax'				=> ($dataio->fax != '' ? $dataio->fax : null),
						'website'			=> ($dataio->website != '' ? $dataio->website : null),
						'created_at'		=> ($dataio->created_at != '' ? $dataio->created_at : null),
						'updated_at'		=> ($dataio->updated_at != '' ? $dataio->updated_at : null),
						'deleted_at'		=> ($dataio->deleted_at != '' ? $dataio->deleted_at : null)
					);
					
					$this->db->where('id',$format['id']);
					$query = $this->db->get('master_end_customer');
					$query = $query->result_object();
					if(!$query){
						$datarow = array(  
							'id'				=> ($format['id'] != '' ? $format['id'] : null),
							'name'				=> ($format['nama'] != '' ? $format['nama'] : null),
							'description'		=> ($format['nama'] != '' ? $format['nama'] : null),
							'nomor'		=> ($format['nomor'] != '' ? $format['nomor'] : null),
							'alamat'			=> ($format['alamat'] != '' ? $format['alamat'] : null),
							'email'				=> ($format['email'] != '' ? $format['email'] : null),
							'no_tlpn'			=> ($format['no_tlpn'] != '' ? $format['no_tlpn'] : null),
							'fax'				=> ($format['fax'] != '' ? $format['fax'] : null),
							'website'			=> ($format['website'] != '' ? $format['website'] : null),
							'active'			=> 1,
							'createdid'			=> 1,
							'created'			=> ($format['created_at'] != '' ? $format['created_at'] : null),
							'modifiedid'		=> 1,
							'modified'			=> ($format['updated_at'] != '' ? $format['updated_at'] : null)
						);
						
						$string = $format['nama'];
						$slug = $this->ortyd->sanitize($string,'master_end_customer');
						$datarow = array_merge($datarow,
							array('slug' 	=> $slug)
						);
						
						$insert = $this->db->insert('master_end_customer', $datarow);
					}else{
						$datarow = array(  
							'id'				=> ($format['id'] != '' ? $format['id'] : null),
							'name'				=> ($format['nama'] != '' ? $format['nama'] : null),
							'description'		=> ($format['nama'] != '' ? $format['nama'] : null),
							'nomor'		=> ($format['nomor'] != '' ? $format['nomor'] : null),
							'alamat'			=> ($format['alamat'] != '' ? $format['alamat'] : null),
							'email'				=> ($format['email'] != '' ? $format['email'] : null),
							'no_tlpn'			=> ($format['no_tlpn'] != '' ? $format['no_tlpn'] : null),
							'fax'				=> ($format['fax'] != '' ? $format['fax'] : null),
							'website'			=> ($format['website'] != '' ? $format['website'] : null),
							'active'			=> 1,
							'createdid'			=> 1,
							'created'			=> ($format['created_at'] != '' ? $format['created_at'] : null),
							'modifiedid'		=> 1,
							'modified'			=> ($format['updated_at'] != '' ? $format['updated_at'] : null)
						);
						
						$string = $format['nama'];
						$slug = $this->ortyd->sanitize($string,'master_end_customer');
						//$datarow = array_merge($datarow,
							//array('slug' 	=> $slug)
						//);
						
						$this->db->where('id',$format['id']);
						$update = $this->db->update('master_end_customer', $datarow);
					}
			
			}
			
			
			return true;
		}
		
		public function setPicCustomer($project_id, $dataio){
			
			if($dataio != null){
					$format = array(
						'id'				=> ($dataio->id != '' ? $dataio->id : null),
						'customer_id'		=> ($dataio->customer_id != '' ? $dataio->customer_id : null),
						'nama'				=> ($dataio->nama != '' ? $dataio->nama : null),
						'telpon'			=> ($dataio->telpon != '' ? $dataio->telpon : null),
						'email'				=> ($dataio->email != '' ? $dataio->email : null),
						'created_at'		=> ($dataio->created_at != '' ? $dataio->created_at : null),
						'updated_at'		=> ($dataio->updated_at != '' ? $dataio->updated_at : null),
					);
					
					$this->db->where('id',$format['id']);
					$query = $this->db->get('master_pic_customer');
					$query = $query->result_object();
					if(!$query){
						$datarow = array(  
							'id'				=> ($format['id'] != '' ? $format['id'] : null),
							'customer_id'		=> ($format['customer_id'] != '' ? $format['customer_id'] : null),
							'nama'				=> ($format['nama'] != '' ? $format['nama'] : null),
							'email'				=> ($format['email'] != '' ? $format['email'] : null),
							'telpon'			=> ($format['telpon'] != '' ? $format['telpon'] : null),
							'active'			=> 1,
							'createdid'			=> 1,
							'created'			=> ($format['created_at'] != '' ? $format['created_at'] : null),
							'modifiedid'		=> 1,
							'modified'			=> ($format['updated_at'] != '' ? $format['updated_at'] : null)
						);
						
						$string = $format['nama'];
						$slug = $this->ortyd->sanitize($string,'master_pic_customer');
						$datarow = array_merge($datarow,
							array('slug' 	=> $slug)
						);
						
						$insert = $this->db->insert('master_pic_customer', $datarow);
						$insert_id = $this->db->insert_id();
						 
						if($insert){
							
							$this->db->where('project_id',$project_id);
							$this->db->where('customer_pic_id',$format['id']);
							$query = $this->db->get('data_project_customer_pic');
							$query = $query->result_object();
							if(!$query){
								$datarow = array(  
									'project_id'		=> $project_id,
									'customer_pic_id'	=> $insert_id,
									'active'			=> 1,
									'createdid'			=> 1,
									'created'			=> ($format['created_at'] != '' ? $format['created_at'] : null),
									'modifiedid'		=> 1,
									'modified'			=> ($format['updated_at'] != '' ? $format['updated_at'] : null)
								);
								
								$insert = $this->db->insert('data_project_customer_pic', $datarow);
							}else{
								$datarow = array(  
									'project_id'		=> $project_id,
									'customer_pic_id'	=> $insert_id,
									'active'			=> 1,
									'modifiedid'		=> 1,
									'modified'			=> ($format['updated_at'] != '' ? $format['updated_at'] : null)
								);
								
								$this->db->where('id',$query[0]->id);
								$update = $this->db->update('data_project_customer_pic', $datarow);
						
							}
						}
					}else{
						$datarow = array(  
							'id'				=> ($format['id'] != '' ? $format['id'] : null),
							'customer_id'		=> ($format['customer_id'] != '' ? $format['customer_id'] : null),
							'nama'				=> ($format['nama'] != '' ? $format['nama'] : null),
							'email'				=> ($format['email'] != '' ? $format['email'] : null),
							'telpon'			=> ($format['telpon'] != '' ? $format['telpon'] : null),
							'active'			=> 1,
							'createdid'			=> 1,
							'created'			=> ($format['created_at'] != '' ? $format['created_at'] : null),
							'modifiedid'		=> 1,
							'modified'			=> ($format['updated_at'] != '' ? $format['updated_at'] : null)
						);
						
						$string = $format['nama'];
						$slug = $this->ortyd->sanitize($string,'master_pic_customer');
						//$datarow = array_merge($datarow,
							//array('slug' 	=> $slug)
						//);
						
						$this->db->where('id',$format['id']);
						$update = $this->db->update('master_pic_customer', $datarow);
						
						if($update){
							
							$this->db->where('project_id',$project_id);
							$this->db->where('customer_pic_id',$format['id']);
							$query = $this->db->get('data_project_customer_pic');
							$query = $query->result_object();
							if(!$query){
								$datarow = array(  
									'project_id'		=> $project_id,
									'customer_pic_id'	=> $format['id'],
									'active'			=> 1,
									'createdid'			=> 1,
									'created'			=> ($format['created_at'] != '' ? $format['created_at'] : null),
									'modifiedid'		=> 1,
									'modified'			=> ($format['updated_at'] != '' ? $format['updated_at'] : null)
								);
								
								$insert = $this->db->insert('data_project_customer_pic', $datarow);
							}else{
								$datarow = array(  
									'project_id'		=> $project_id,
									'customer_pic_id'	=> $format['id'],
									'active'			=> 1,
									'modifiedid'		=> 1,
									'modified'			=> ($format['updated_at'] != '' ? $format['updated_at'] : null)
								);
								
								$this->db->where('id',$query[0]->id);
								$update = $this->db->update('data_project_customer_pic', $datarow);
						
							}
						}
						
					}
			
			}
			
			
			return true;
		}
		
		public function setPicendCustomer($project_id, $dataio){
			
			if($dataio != null){
					$format = array(
						'id'				=> ($dataio->id != '' ? $dataio->id : null),
						'end_customer_id'		=> ($dataio->end_customer_id != '' ? $dataio->end_customer_id : null),
						'nama'				=> ($dataio->nama != '' ? $dataio->nama : null),
						'telpon'			=> ($dataio->telpon != '' ? $dataio->telpon : null),
						'email'				=> ($dataio->email != '' ? $dataio->email : null),
						'created_at'		=> ($dataio->created_at != '' ? $dataio->created_at : null),
						'updated_at'		=> ($dataio->updated_at != '' ? $dataio->updated_at : null),
					);
					
					$this->db->where('id',$format['id']);
					$query = $this->db->get('master_pic_end_customer');
					$query = $query->result_object();
					if(!$query){
						$datarow = array(  
							'id'				=> ($format['id'] != '' ? $format['id'] : null),
							'end_customer_id'		=> ($format['end_customer_id'] != '' ? $format['end_customer_id'] : null),
							'nama'				=> ($format['nama'] != '' ? $format['nama'] : null),
							'email'				=> ($format['email'] != '' ? $format['email'] : null),
							'telpon'			=> ($format['telpon'] != '' ? $format['telpon'] : null),
							'active'			=> 1,
							'createdid'			=> 1,
							'created'			=> ($format['created_at'] != '' ? $format['created_at'] : null),
							'modifiedid'		=> 1,
							'modified'			=> ($format['updated_at'] != '' ? $format['updated_at'] : null)
						);
						
						$string = $format['nama'];
						$slug = $this->ortyd->sanitize($string,'master_pic_end_customer');
						$datarow = array_merge($datarow,
							array('slug' 	=> $slug)
						);
						
						$insert = $this->db->insert('master_pic_end_customer', $datarow);
						
						if($insert){
							
							$this->db->where('project_id',$project_id);
							$this->db->where('end_customer_pic_id',$format['id']);
							$query = $this->db->get('data_project_customer_end_pic');
							$query = $query->result_object();
							if(!$query){
								$datarow = array(  
									'project_id'		=> $project_id,
									'end_customer_pic_id'	=> $format['id'],
									'active'			=> 1,
									'createdid'			=> 1,
									'created'			=> ($format['created_at'] != '' ? $format['created_at'] : null),
									'modifiedid'		=> 1,
									'modified'			=> ($format['updated_at'] != '' ? $format['updated_at'] : null)
								);
								
								$insert = $this->db->insert('data_project_customer_end_pic', $datarow);
							}else{
								$datarow = array(  
									'project_id'		=> $project_id,
									'end_customer_pic_id'	=> $format['id'],
									'active'			=> 1,
									'modifiedid'		=> 1,
									'modified'			=> ($format['updated_at'] != '' ? $format['updated_at'] : null)
								);
								
								$this->db->where('id',$query[0]->id);
								$update = $this->db->update('data_project_customer_end_pic', $datarow);
						
							}
						}
						
					}else{
						$datarow = array(  
							'id'				=> ($format['id'] != '' ? $format['id'] : null),
							'end_customer_id'		=> ($format['end_customer_id'] != '' ? $format['end_customer_id'] : null),
							'nama'				=> ($format['nama'] != '' ? $format['nama'] : null),
							'email'				=> ($format['email'] != '' ? $format['email'] : null),
							'telpon'			=> ($format['telpon'] != '' ? $format['telpon'] : null),
							'active'			=> 1,
							'createdid'			=> 1,
							'created'			=> ($format['created_at'] != '' ? $format['created_at'] : null),
							'modifiedid'		=> 1,
							'modified'			=> ($format['updated_at'] != '' ? $format['updated_at'] : null)
						);
						
						$string = $format['nama'];
						$slug = $this->ortyd->sanitize($string,'master_pic_end_customer');
						//$datarow = array_merge($datarow,
							//array('slug' 	=> $slug)
						//);
						
						$this->db->where('id',$format['id']);
						$update = $this->db->update('master_pic_end_customer', $datarow);
						
						if($update){
							
							$this->db->where('project_id',$project_id);
							$this->db->where('end_customer_pic_id',$format['id']);
							$query = $this->db->get('data_project_customer_end_pic');
							$query = $query->result_object();
							if(!$query){
								$datarow = array(  
									'project_id'		=> $project_id,
									'end_customer_pic_id'	=> $format['id'],
									'active'			=> 1,
									'createdid'			=> 1,
									'created'			=> ($format['created_at'] != '' ? $format['created_at'] : null),
									'modifiedid'		=> 1,
									'modified'			=> ($format['updated_at'] != '' ? $format['updated_at'] : null)
								);
								
								$insert = $this->db->insert('data_project_customer_end_pic', $datarow);
							}else{
								$datarow = array(  
									'project_id'		=> $project_id,
									'end_customer_pic_id'	=> $format['id'],
									'active'			=> 1,
									'modifiedid'		=> 1,
									'modified'			=> ($format['updated_at'] != '' ? $format['updated_at'] : null)
								);
								
								$this->db->where('id',$query[0]->id);
								$update = $this->db->update('data_project_customer_end_pic', $datarow);
						
							}
						}
						
					}
			
			}
			
			
			return true;
		}
		
		public function setUnit($unit_id, $dataio){
			
			if($dataio != null){
					$format = array(
						'id'				=> ($dataio->id != '' ? $dataio->id : null),
						'name'				=> ($dataio->name != '' ? $dataio->name : null),
						'direktorat_id'		=> ($dataio->direktorat_id != '' ? $dataio->direktorat_id : null),
						'cost_center'		=> ($dataio->cost_center != '' ? $dataio->cost_center : null),
						'created_at'		=> ($dataio->created_at != '' ? $dataio->created_at : null),
						'updated_at'		=> ($dataio->updated_at != '' ? $dataio->updated_at : null),
						'deleted_at'		=> ($dataio->deleted_at != '' ? $dataio->deleted_at : null)
					);
					
					$this->db->where('id',$format['id']);
					$query = $this->db->get('master_unit');
					$query = $query->result_object();
					if(!$query){
						$datarow = array(  
							'id'				=> ($format['id'] != '' ? $format['id'] : null),
							'name'				=> ($format['name'] != '' ? $format['name'] : null),
							'description'		=> ($format['name'] != '' ? $format['name'] : null),
							'direktorat_id'		=> ($format['direktorat_id'] != '' ? $format['direktorat_id'] : null),
							'cost_center'		=> ($format['cost_center'] != '' ? $format['cost_center'] : null),
							'active'			=> 1,
							'createdid'			=> 1,
							'created'			=> ($format['created_at'] != '' ? $format['created_at'] : null),
							'modifiedid'		=> 1,
							'modified'			=> ($format['updated_at'] != '' ? $format['updated_at'] : null)
						);
						
						$string = $format['name'];
						$slug = $this->ortyd->sanitize($string,'master_unit');
						$datarow = array_merge($datarow,
							array('slug' 	=> $slug)
						);
						
						$insert = $this->db->insert('master_unit', $datarow);
					}else{
						$datarow = array(  
							'id'				=> ($format['id'] != '' ? $format['id'] : null),
							'name'				=> ($format['name'] != '' ? $format['name'] : null),
							'description'		=> ($format['name'] != '' ? $format['name'] : null),
							'direktorat_id'		=> ($format['direktorat_id'] != '' ? $format['direktorat_id'] : null),
							'cost_center'		=> ($format['cost_center'] != '' ? $format['cost_center'] : null),
							'active'			=> 1,
							'createdid'			=> 1,
							'created'			=> ($format['created_at'] != '' ? $format['created_at'] : null),
							'modifiedid'		=> 1,
							'modified'			=> ($format['updated_at'] != '' ? $format['updated_at'] : null)
						);
						
						$string = $format['name'];
						$slug = $this->ortyd->sanitize($string,'master_unit');
						//$datarow = array_merge($datarow,
							//array('slug' 	=> $slug)
						//);
						
						$this->db->where('id',$format['id']);
						$update = $this->db->update('master_unit', $datarow);
					}
			
			}
			
			
			return true;
		}
		
		public function setPortfolio($portfolio_id, $dataio){
			
			if($dataio != null){
					$format = array(
						'id'				=> ($dataio->id != '' ? $dataio->id : null),
						'name'				=> ($dataio->name != '' ? $dataio->name : null),
						'created_at'		=> ($dataio->created_at != '' ? $dataio->created_at : null),
						'updated_at'		=> ($dataio->updated_at != '' ? $dataio->updated_at : null),
						'deleted_at'		=> ($dataio->deleted_at != '' ? $dataio->deleted_at : null)
					);
					
					$this->db->where('id',$format['id']);
					$query = $this->db->get('master_portfolio');
					$query = $query->result_object();
					if(!$query){
						$datarow = array(  
							'id'				=> ($format['id'] != '' ? $format['id'] : null),
							'name'				=> ($format['name'] != '' ? $format['name'] : null),
							'description'		=> ($format['name'] != '' ? $format['name'] : null),
							'active'			=> 1,
							'createdid'			=> 1,
							'created'			=> ($format['created_at'] != '' ? $format['created_at'] : null),
							'modifiedid'		=> 1,
							'modified'			=> ($format['updated_at'] != '' ? $format['updated_at'] : null)
						);
						
						$string = $format['name'];
						$slug = $this->ortyd->sanitize($string,'master_portfolio');
						$datarow = array_merge($datarow,
							array('slug' 	=> $slug)
						);
						
						$insert = $this->db->insert('master_portfolio', $datarow);
					}else{
						$datarow = array(  
							'id'				=> ($format['id'] != '' ? $format['id'] : null),
							'name'				=> ($format['name'] != '' ? $format['name'] : null),
							'description'		=> ($format['name'] != '' ? $format['name'] : null),
							'active'			=> 1,
							'createdid'			=> 1,
							'created'			=> ($format['created_at'] != '' ? $format['created_at'] : null),
							'modifiedid'		=> 1,
							'modified'			=> ($format['updated_at'] != '' ? $format['updated_at'] : null)
						);
						
						$string = $format['name'];
						$slug = $this->ortyd->sanitize($string,'master_portfolio');
						//$datarow = array_merge($datarow,
							//array('slug' 	=> $slug)
						//);
						
						$this->db->where('id',$format['id']);
						$update = $this->db->update('master_portfolio', $datarow);
					}
			
			}
			
			
			return true;
		}
		
		
		public function setStatus($status_id, $dataio){
			
			if($dataio != null){
					$format = array(
						'id'				=> ($dataio->id != '' ? $dataio->id : null),
						'name'				=> ($dataio->name != '' ? $dataio->name : null),
						'created_at'		=> ($dataio->created_at != '' ? $dataio->created_at : null),
						'updated_at'		=> ($dataio->updated_at != '' ? $dataio->updated_at : null),
						'deleted_at'		=> ($dataio->deleted_at != '' ? $dataio->deleted_at : null)
					);
					
					$this->db->where('id',$format['id']);
					$query = $this->db->get('master_status_crm');
					$query = $query->result_object();
					if(!$query){
						$datarow = array(  
							'id'				=> ($format['id'] != '' ? $format['id'] : null),
							'name'				=> ($format['name'] != '' ? $format['name'] : null),
							'description'		=> ($format['name'] != '' ? $format['name'] : null),
							'active'			=> 1,
							'createdid'			=> 1,
							'created'			=> ($format['created_at'] != '' ? $format['created_at'] : null),
							'modifiedid'		=> 1,
							'modified'			=> ($format['updated_at'] != '' ? $format['updated_at'] : null)
						);
						
						$string = $format['name'];
						$slug = $this->ortyd->sanitize($string,'master_status_crm');
						$datarow = array_merge($datarow,
							array('slug' 	=> $slug)
						);
						
						$insert = $this->db->insert('master_status_crm', $datarow);
					}else{
						$datarow = array(  
							'id'				=> ($format['id'] != '' ? $format['id'] : null),
							'name'				=> ($format['name'] != '' ? $format['name'] : null),
							'description'		=> ($format['name'] != '' ? $format['name'] : null),
							'active'			=> 1,
							'createdid'			=> 1,
							'created'			=> ($format['created_at'] != '' ? $format['created_at'] : null),
							'modifiedid'		=> 1,
							'modified'			=> ($format['updated_at'] != '' ? $format['updated_at'] : null)
						);
						
						$string = $format['name'];
						$slug = $this->ortyd->sanitize($string,'master_status_crm');
						//$datarow = array_merge($datarow,
							//array('slug' 	=> $slug)
						//);
						
						$this->db->where('id',$format['id']);
						$update = $this->db->update('master_status_crm', $datarow);
					}
			
			}
			
			
			return true;
		}
		
		public function setTop($top_id, $dataio){
			
			if($dataio != null){
					$format = array(
						'id'				=> ($dataio->id != '' ? $dataio->id : null),
						'name'				=> ($dataio->nama != '' ? $dataio->nama : null),
						'created_at'		=> ($dataio->created_at != '' ? $dataio->created_at : null),
						'updated_at'		=> ($dataio->updated_at != '' ? $dataio->updated_at : null),
						'deleted_at'		=> ($dataio->deleted_at != '' ? $dataio->deleted_at : null)
					);
					
					$this->db->where('id',$format['id']);
					$query = $this->db->get('master_top');
					$query = $query->result_object();
					if(!$query){
						$datarow = array(  
							'id'				=> ($format['id'] != '' ? $format['id'] : null),
							'name'				=> ($format['name'] != '' ? $format['name'] : null),
							'description'		=> ($format['name'] != '' ? $format['name'] : null),
							'active'			=> 1,
							'createdid'			=> 1,
							'created'			=> ($format['created_at'] != '' ? $format['created_at'] : null),
							'modifiedid'		=> 1,
							'modified'			=> ($format['updated_at'] != '' ? $format['updated_at'] : null)
						);
						
						$string = $format['name'];
						$slug = $this->ortyd->sanitize($string,'master_top');
						$datarow = array_merge($datarow,
							array('slug' 	=> $slug)
						);
						
						$insert = $this->db->insert('master_top', $datarow);
					}else{
						$datarow = array(  
							'id'				=> ($format['id'] != '' ? $format['id'] : null),
							'name'				=> ($format['name'] != '' ? $format['name'] : null),
							'description'		=> ($format['name'] != '' ? $format['name'] : null),
							'active'			=> 1,
							'createdid'			=> 1,
							'created'			=> ($format['created_at'] != '' ? $format['created_at'] : null),
							'modifiedid'		=> 1,
							'modified'			=> ($format['updated_at'] != '' ? $format['updated_at'] : null)
						);
						
						$string = $format['name'];
						$slug = $this->ortyd->sanitize($string,'master_top');
						//$datarow = array_merge($datarow,
							//array('slug' 	=> $slug)
						//);
						
						$this->db->where('id',$format['id']);
						$update = $this->db->update('master_top', $datarow);
					}
			
			}
			
			
			return true;
		}
		
		public function setSegment($segment_id, $dataio){
			
			if($dataio != null){
					$format = array(
						'id'				=> ($dataio->id != '' ? $dataio->id : null),
						'name'				=> ($dataio->name != '' ? $dataio->name : null),
						'channel'			=> ($dataio->channel != '' ? $dataio->channel : null),
						'customer_id'		=> ($dataio->customer_id != '' ? $dataio->customer_id : null),
						'created_at'		=> ($dataio->created_at != '' ? $dataio->created_at : null),
						'updated_at'		=> ($dataio->updated_at != '' ? $dataio->updated_at : null),
						'deleted_at'		=> ($dataio->deleted_at != '' ? $dataio->deleted_at : null)
					);
					
					$this->db->where('id',$format['id']);
					$query = $this->db->get('master_segment');
					$query = $query->result_object();
					if(!$query){
						$datarow = array(  
							'id'				=> ($format['id'] != '' ? $format['id'] : null),
							'name'				=> ($format['name'] != '' ? $format['name'] : null),
							'description'		=> ($format['name'] != '' ? $format['name'] : null),
							'channel'			=> ($format['channel'] != '' ? $format['channel'] : null),
							'customer_id'		=> ($format['customer_id'] != '' ? $format['customer_id'] : null),
							'active'			=> 1,
							'createdid'			=> 1,
							'created'			=> ($format['created_at'] != '' ? $format['created_at'] : null),
							'modifiedid'		=> 1,
							'modified'			=> ($format['updated_at'] != '' ? $format['updated_at'] : null)
						);
						
						$string = $format['name'];
						$slug = $this->ortyd->sanitize($string,'master_segment');
						$datarow = array_merge($datarow,
							array('slug' 	=> $slug)
						);
						
						$insert = $this->db->insert('master_segment', $datarow);
					}else{
						$datarow = array(  
							'id'				=> ($format['id'] != '' ? $format['id'] : null),
							'name'				=> ($format['name'] != '' ? $format['name'] : null),
							'description'		=> ($format['name'] != '' ? $format['name'] : null),
							'channel'			=> ($format['channel'] != '' ? $format['channel'] : null),
							'customer_id'		=> ($format['customer_id'] != '' ? $format['customer_id'] : null),
							'active'			=> 1,
							'createdid'			=> 1,
							'created'			=> ($format['created_at'] != '' ? $format['created_at'] : null),
							'modifiedid'		=> 1,
							'modified'			=> ($format['updated_at'] != '' ? $format['updated_at'] : null)
						);
						
						$string = $format['name'];
						$slug = $this->ortyd->sanitize($string,'master_segment');
						//$datarow = array_merge($datarow,
							//array('slug' 	=> $slug)
						//);
						
						$this->db->where('id',$format['id']);
						$update = $this->db->update('master_segment', $datarow);
					}
			
			}
			
			
			return true;
		}
		
		
		
		
		
		public function setKbli($kbli_id, $dataio){
			
			if($dataio != null){
					$format = array(
						'id'				=> ($dataio->id != '' ? $dataio->id : null),
						'name'				=> ($dataio->judul != '' ? $dataio->judul : null),
						'kode'				=> ($dataio->kode != '' ? $dataio->kode : null),
						'lokasi_usaha'		=> ($dataio->lokasi_usaha != '' ? $dataio->lokasi_usaha : null),
						'klasifikasi_risiko'=> ($dataio->klasifikasi_risiko != '' ? $dataio->klasifikasi_risiko : null),
						'jenis_perizinan'	=> ($dataio->jenis_perizinan != '' ? $dataio->jenis_perizinan : null),
						'legalitas_perizinan'=> ($dataio->legalitas_perizinan != '' ? $dataio->legalitas_perizinan : null),
						'created_at'		=> ($dataio->created_at != '' ? $dataio->created_at : null),
						'updated_at'		=> ($dataio->updated_at != '' ? $dataio->updated_at : null),
						'deleted_at'		=> ($dataio->deleted_at != '' ? $dataio->deleted_at : null)
					);
					
					$this->db->where('id',$format['id']);
					$query = $this->db->get('master_kbli');
					$query = $query->result_object();
					if(!$query){
						$datarow = array(  
							'id'				=> ($format['id'] != '' ? $format['id'] : null),
							'name'				=> ($format['name'] != '' ? $format['name'] : null),
							'description'		=> ($format['name'] != '' ? $format['name'] : null),
							'kode'			=> ($format['kode'] != '' ? $format['kode'] : null),
							'lokasi_usaha'		=> ($format['lokasi_usaha'] != '' ? $format['lokasi_usaha'] : null),
							'klasifikasi_risiko'		=> ($format['klasifikasi_risiko'] != '' ? $format['klasifikasi_risiko'] : null),
							'jenis_perizinan'		=> ($format['jenis_perizinan'] != '' ? $format['jenis_perizinan'] : null),
							'legalitas_perizinan'		=> ($format['legalitas_perizinan'] != '' ? $format['legalitas_perizinan'] : null),
							'active'			=> 1,
							'createdid'			=> 1,
							'created'			=> ($format['created_at'] != '' ? $format['created_at'] : null),
							'modifiedid'		=> 1,
							'modified'			=> ($format['updated_at'] != '' ? $format['updated_at'] : null)
						);
						
						$string = $format['name'];
						$slug = $this->ortyd->sanitize($string,'master_kbli');
						$datarow = array_merge($datarow,
							array('slug' 	=> $slug)
						);
						
						$insert = $this->db->insert('master_kbli', $datarow);
					}else{
						$datarow = array(  
							'id'				=> ($format['id'] != '' ? $format['id'] : null),
							'name'				=> ($format['name'] != '' ? $format['name'] : null),
							'description'		=> ($format['name'] != '' ? $format['name'] : null),
							'kode'			=> ($format['kode'] != '' ? $format['kode'] : null),
							'lokasi_usaha'		=> ($format['lokasi_usaha'] != '' ? $format['lokasi_usaha'] : null),
							'klasifikasi_risiko'		=> ($format['klasifikasi_risiko'] != '' ? $format['klasifikasi_risiko'] : null),
							'jenis_perizinan'		=> ($format['jenis_perizinan'] != '' ? $format['jenis_perizinan'] : null),
							'legalitas_perizinan'		=> ($format['legalitas_perizinan'] != '' ? $format['legalitas_perizinan'] : null),
							'active'			=> 1,
							'createdid'			=> 1,
							'created'			=> ($format['created_at'] != '' ? $format['created_at'] : null),
							'modifiedid'		=> 1,
							'modified'			=> ($format['updated_at'] != '' ? $format['updated_at'] : null)
						);
						
						$string = $format['name'];
						$slug = $this->ortyd->sanitize($string,'master_kbli');
						//$datarow = array_merge($datarow,
							//array('slug' 	=> $slug)
						//);
						
						$this->db->where('id',$format['id']);
						$update = $this->db->update('master_kbli', $datarow);
					}
			
			}
			
			
			return true;
		}
		
		
		
		public function setFunnel($funnel_id, $dataio){
			
			if($dataio != null){
					$format = array(
						'id'				=> ($dataio->id != '' ? $dataio->id : null),
						'name'				=> ($dataio->name != '' ? $dataio->name : null),
						'deskripsi'			=> ($dataio->deskripsi != '' ? $dataio->deskripsi : null),
						'level'				=> ($dataio->level != '' ? $dataio->level : null),
						'percentage'		=> ($dataio->percentage != '' ? $dataio->percentage : null),
						'created_at'		=> ($dataio->created_at != '' ? $dataio->created_at : null),
						'updated_at'		=> ($dataio->updated_at != '' ? $dataio->updated_at : null),
						'deleted_at'		=> ($dataio->deleted_at != '' ? $dataio->deleted_at : null)
					);
					
					$this->db->where('id',$format['id']);
					$query = $this->db->get('master_funnel');
					$query = $query->result_object();
					if(!$query){
						$datarow = array(  
							'id'				=> ($format['id'] != '' ? $format['id'] : null),
							'name'				=> ($format['name'] != '' ? $format['name'] : null),
							'description'		=> ($format['deskripsi'] != '' ? $format['deskripsi'] : null),
							'level'				=> ($format['level'] != '' ? $format['level'] : null),
							'percentage'		=> ($format['percentage'] != '' ? $format['percentage'] : null),
							'active'			=> 1,
							'createdid'			=> 1,
							'created'			=> ($format['created_at'] != '' ? $format['created_at'] : null),
							'modifiedid'		=> 1,
							'modified'			=> ($format['updated_at'] != '' ? $format['updated_at'] : null)
						);
						
						$string = $format['name'];
						$slug = $this->ortyd->sanitize($string,'master_funnel');
						$datarow = array_merge($datarow,
							array('slug' 	=> $slug)
						);
						
						$insert = $this->db->insert('master_funnel', $datarow);
					}else{
						$datarow = array(  
							'id'				=> ($format['id'] != '' ? $format['id'] : null),
							'name'				=> ($format['name'] != '' ? $format['name'] : null),
							'description'		=> ($format['deskripsi'] != '' ? $format['deskripsi'] : null),
							'level'				=> ($format['level'] != '' ? $format['level'] : null),
							'percentage'		=> ($format['percentage'] != '' ? $format['percentage'] : null),
							'active'			=> 1,
							'createdid'			=> 1,
							'created'			=> ($format['created_at'] != '' ? $format['created_at'] : null),
							'modifiedid'		=> 1,
							'modified'			=> ($format['updated_at'] != '' ? $format['updated_at'] : null)
						);
						
						$string = $format['name'];
						$slug = $this->ortyd->sanitize($string,'master_funnel');
						//$datarow = array_merge($datarow,
							//array('slug' 	=> $slug)
						//);
						
						$this->db->where('id',$format['id']);
						$update = $this->db->update('master_funnel', $datarow);
					}
			
			}
			
			
			return true;
		}
		
		public function setAM($am_id, $dataio){
			
			if($dataio != null){
					$format = array(
						'id'					=> ($dataio->id != '' ? $dataio->id : null),
						'name'					=> ($dataio->name != '' ? $dataio->name : null),
						'username'				=> ($dataio->username != '' ? $dataio->username : null),
						'email'					=> ($dataio->email != '' ? $dataio->email : null),
						'nik'					=> ($dataio->nik != '' ? $dataio->nik : null),
						'company'				=> ($dataio->company != '' ? $dataio->company : null),
						'workplace'				=> ($dataio->workplace != '' ? $dataio->workplace : null),
						'status_kepegawaian'	=> ($dataio->status_kepegawaian != '' ? $dataio->status_kepegawaian : null),
						'phone'					=> ($dataio->phone != '' ? $dataio->phone : null)
					);
					
					$this->db->where('email',$format['email']);
					$query = $this->db->get('users_data');
					$query = $query->result_object();
					if(!$query){
						$datarow = array(  
							'data_id'			=> ($format['id'] != '' ? $format['id'] : null),
							'fullname'			=> ($format['name'] != '' ? $format['name'] : null),
							'username'			=> ($format['username'] != '' ? $format['username'] : null),
							'password'			=> 'created',
							'email'				=> ($format['email'] != '' ? $format['email'] : null),
							'notelp'			=> ($format['phone'] != '' ? $format['phone'] : null),
							'gid'				=> 3,
							'validate'			=> 0,
							'nik'				=> ($format['nik'] != '' ? $format['nik'] : null),
							'company'			=> ($format['company'] != '' ? $format['company'] : null),
							'workplace'			=> ($format['workplace'] != '' ? $format['workplace'] : null),
							'status_kepegawaian'=> ($format['status_kepegawaian'] != '' ? $format['status_kepegawaian'] : null),
							'banned'			=> 0,
							'active'			=> 1,
							'createdid'			=> 1,
							'created'			=> date('Y-m-d H:i:s'),
							'modifiedid'		=> 1,
							'modified'			=> date('Y-m-d H:i:s'),
						);
						
						$insert = $this->db->insert('users_data', $datarow);
					}else{
						$datarow = array(  
							'data_id'			=> ($format['id'] != '' ? $format['id'] : null),
							'fullname'			=> ($format['name'] != '' ? $format['name'] : null),
							'notelp'			=> ($format['phone'] != '' ? $format['phone'] : null),
							'nik'				=> ($format['nik'] != '' ? $format['nik'] : null),
							'company'			=> ($format['company'] != '' ? $format['company'] : null),
							'workplace'			=> ($format['workplace'] != '' ? $format['workplace'] : null),
							'status_kepegawaian'=> ($format['status_kepegawaian'] != '' ? $format['status_kepegawaian'] : null),
							'active'			=> 1,
							'modifiedid'		=> 1,
							'modified'			=> date('Y-m-d H:i:s'),
						);
						
						$this->db->where('email',$format['email']);
						$update = $this->db->update('users_data', $datarow);
					}
			
			}
			
			
			return true;
		}
		
		public function setMitras($project_id, $dataio){
			
			
			$project_detail = $dataio->project ?? '';
			if($project_detail != ''){
				$project_detail_mitras = $project_detail->mitras ?? '';
				
				if($project_detail_mitras != ''){
					
					foreach ($project_detail_mitras as $rowsmitras){

						$format = array(
							'id'				=> (($rowsmitras->detail_mitra->id ?? '') != '' ? (($rowsmitras->detail_mitra->id ?? '') ?? '') : null),
							'ref_id'			=> (($rowsmitras->id ?? '') != '' ? (($rowsmitras->id ?? '') ?? '') : null),
							'name'				=> (($rowsmitras->detail_mitra->nama_vendor ?? '') != '' ? (($rowsmitras->detail_mitra->nama_vendor ?? '') ?? '') : null),
							'deskripsi_pekerjaan'				=> (($rowsmitras->deskripsi_pekerjaan ?? '') != '' ? (($rowsmitras->deskripsi_pekerjaan ?? '') ?? '') : null),
							'description'		=> (($rowsmitras->detail_mitra->deskripsi_vendor ?? '') != '' ? ($rowsmitras->detail_mitra->deskripsi_vendor ?? '') : null),
							'no_telp'			=> (($rowsmitras->detail_mitra->no_tlpn ?? '') != '' ? ($rowsmitras->detail_mitra->no_tlpn ?? '') : null),
							'pic'				=> (($rowsmitras->detail_mitra->pic ?? '') != '' ? ($rowsmitras->detail_mitra->pic ?? '') : null),
							'direktur'				=> (($rowsmitras->detail_mitra->direktur ?? '') != '' ? ($rowsmitras->detail_mitra->direktur ?? '') : null),
							'email'				=> (($rowsmitras->detail_mitra->email ?? '') != '' ? ($rowsmitras->detail_mitra->email ?? '') : null),
							'nilai_pekerjaan'				=> (($rowsmitras->nilai_pekerjaan ?? '') != '' ? ($rowsmitras->nilai_pekerjaan ?? '') : null),
							'tanggal_mulai'				=> (($rowsmitras->start_jangka_waktu_pekerjaan ?? '') != '' ? ($rowsmitras->start_jangka_waktu_pekerjaan ?? '') : null),
							'tanggal_selesai'				=> (($rowsmitras->end_jangka_waktu_pekerjaan ?? '') != '' ? ($rowsmitras->end_jangka_waktu_pekerjaan ?? '') : null),
							'sow'				=> (($rowsmitras->sow ?? '') != '' ? ($rowsmitras->sow ?? '') : null),
							'alamat'				=> (($rowsmitras->detail_mitra->alamat ?? '') != '' ? ($rowsmitras->detail_mitra->alamat ?? '') : null),
							'created_at'		=> (($rowsmitras->detail_mitra->created_at ?? '') != '' ? ($rowsmitras->detail_mitra->created_at ?? '') : null),
							'updated_at'		=> (($rowsmitras->detail_mitra->updated_at ?? '') != '' ? ($rowsmitras->detail_mitra->updated_at ?? '') : null),
						);
						
						
						$this->db->where('id',$format['id']);
						$query = $this->db->get('master_mitra');
						$query = $query->result_object();
						if(!$query){
							
							$datarow = array(  
								'id'				=> ($format['id'] != '' ? $format['id'] : null),
								'name'				=> ($format['name'] != '' ? $format['name'] : null),
								'description'		=> ($format['description'] != '' ? $format['description'] : null),
								'no_telp'			=> ($format['no_telp'] != '' ? $format['no_telp'] : null),
								'pic'				=> ($format['pic'] != '' ? $format['pic'] : null),
								'direktur'			=> ($format['direktur'] != '' ? $format['direktur'] : null),
								'email'				=> ($format['email'] != '' ? $format['email'] : null),
								'alamat'				=> ($format['alamat'] != '' ? $format['alamat'] : null),
								'active'			=> 1,
								'createdid'			=> 1,
								'created'			=> date('Y-m-d H:i:s'),
								'modifiedid'		=> 1,
								'modified'			=> date('Y-m-d H:i:s')
							);
							
							$string = $format['name'];
							$slug = $this->ortyd->sanitize($string,'master_mitra');
							$datarow = array_merge($datarow,
								array('slug' 	=> $slug)
							);
							
							$insert = $this->db->insert('master_mitra', $datarow);
							$insert_id = $this->db->insert_id();
							
							if($insert){
								$this->setProjectMitra($project_id, $format);
							}
							
						}else{
							$datarow = array(  
								'id'				=> ($format['id'] != '' ? $format['id'] : null),
								'name'				=> ($format['name'] != '' ? $format['name'] : null),
								'description'		=> ($format['description'] != '' ? $format['description'] : null),
								'no_telp'			=> ($format['no_telp'] != '' ? $format['no_telp'] : null),
								'pic'				=> ($format['pic'] != '' ? $format['pic'] : null),
								'direktur'			=> ($format['direktur'] != '' ? $format['direktur'] : null),
								'email'				=> ($format['email'] != '' ? $format['email'] : null),
								'alamat'				=> ($format['alamat'] != '' ? $format['alamat'] : null),
								'active'			=> 1,
								'createdid'			=> 1,
								'created'			=> date('Y-m-d H:i:s'),
								'modifiedid'		=> 1,
								'modified'			=> date('Y-m-d H:i:s')
							);
							
							$string = $format['name'];
							$slug = $this->ortyd->sanitize($string,'master_mitra');
							//$datarow = array_merge($datarow,
								//array('slug' 	=> $slug)
							//);
							
							$this->db->where('id',$query[0]->id);
							$update = $this->db->update('master_mitra', $datarow);
							
							if($update){
								$this->setProjectMitra($project_id, $format);
							}
							
						}
					}

				}
				
			}
			
		}
		
		public function setProjectMitra($project_id, $format){
			
			
			
			$this->db->where('project_id',$project_id);
			$this->db->where('mitra_id',$format['id']);
			$this->db->where('ref_id',$format['ref_id']);
			$query = $this->db->get('data_project_mitra');
			$query = $query->result_object();
			if(!$query){
						
				$datarow = array(  
					'project_id'		=> ($project_id != '' ? $project_id : null),
					'mitra_id'			=> ($format['id'] != '' ? $format['id'] : null),
					'ref_id'			=> ($format['ref_id'] != '' ? $format['ref_id'] : null),
					'nilai_pekerjaan'	=> ($format['nilai_pekerjaan'] != '' ? $format['nilai_pekerjaan'] : null),
					'tanggal_mulai'		=> ($format['tanggal_mulai'] != '' ? $format['tanggal_mulai'] : null),
					'tanggal_selesai'	=> ($format['tanggal_selesai'] != '' ? $format['tanggal_selesai'] : null),
					'deskripsi_pekerjaan'	=> ($format['deskripsi_pekerjaan'] != '' ? $format['deskripsi_pekerjaan'] : null),
					'sow'				=> ($format['sow'] != '' ? $format['sow'] : null),
					'active'			=> 1,
					'createdid'			=> 1,
					'created'			=> date('Y-m-d H:i:s'),
					'modifiedid'		=> 1,
					'modified'			=> date('Y-m-d H:i:s')
				);
							
				$string = 'mitra-'.$project_id.'-'.$format['id'];
				$slug = $this->ortyd->sanitize($string,'data_project_mitra');
					$datarow = array_merge($datarow,
					array('slug' 	=> $slug)
				);
							
				$insert = $this->db->insert('data_project_mitra', $datarow);
				$insert_id = $this->db->insert_id();
				
				if($insert){
					$this->setSOW($insert_id,$format['sow']);
				}
							
			}else{
				
				$nilai_pekerjaan = ($format['nilai_pekerjaan'] != '' ? $format['nilai_pekerjaan'] : null);
				//echo 'test'.$nilai_pekerjaan.' '.$format['ref_id'].'<br>';
				
				$datarow = array(  
					'project_id'		=> ($project_id != '' ? $project_id : null),
					'mitra_id'			=> ($format['id'] != '' ? $format['id'] : null),
					'ref_id'			=> ($format['ref_id'] != '' ? $format['ref_id'] : null),
					'deskripsi_pekerjaan'	=> ($format['deskripsi_pekerjaan'] != '' ? $format['deskripsi_pekerjaan'] : null),
					'sow'				=> ($format['sow'] != '' ? $format['sow'] : null),
					'nilai_pekerjaan'	=> $nilai_pekerjaan,
					'tanggal_mulai'		=> ($format['tanggal_mulai'] != '' ? $format['tanggal_mulai'] : null),
					'tanggal_selesai'	=> ($format['tanggal_selesai'] != '' ? $format['tanggal_selesai'] : null),
					'active'			=> 1,
					'modifiedid'		=> 1,
					'modified'			=> date('Y-m-d H:i:s')
				);
				
		
				$string = 'mitra-'.$project_id.'-'.$format['id'];
				$slug = $this->ortyd->sanitize($string,'data_project_mitra');
					//$datarow = array_merge($datarow,
					//array('slug' 	=> $slug)
				//);
				
				
				$this->db->where('ref_id',$format['ref_id']);
				$this->db->where('project_id',$project_id);
				$this->db->where('mitra_id',$format['id']);
				$update = $this->db->update('data_project_mitra', $datarow);
				if($update){
					$this->setSOW($query[0]->id, $format['sow']);
				}
			}
						
		}
		
		public function setSOW($project_mitra_id, $string){
			if($string != ''){
				$tags = explode('-',$string);
				foreach($tags as $key) {
					if($key != ''){
						$this->db->where('project_mitra_id',$project_mitra_id);
						$this->db->where('sow',$key);
						$query = $this->db->get('data_project_mitra_sow');
						$query = $query->result_object();
						if(!$query){
							$datarow = array(  
								'project_mitra_id'	=> $project_mitra_id,
								'sow'				=> $key,
								'active'			=> 1,
								'createdid'			=> 1,
								'created'			=> date('Y-m-d H:i:s'),
								'modifiedid'		=> 1,
								'modified'			=> date('Y-m-d H:i:s')
							);
							
							$string = 'sow-'.$project_mitra_id.'-'.$key;
							$slug = $this->ortyd->sanitize($string,'data_project_mitra_sow');
							$datarow = array_merge($datarow,
								array('slug' 	=> $slug)
							);
							
							$insert = $this->db->insert('data_project_mitra_sow', $datarow);
							
						}else{
							$datarow = array(  
								'project_mitra_id'	=> $project_mitra_id,
								'sow'				=> $key,
								'active'			=> 1,
								'createdid'			=> 1,
								'created'			=> date('Y-m-d H:i:s'),
								'modifiedid'		=> 1,
								'modified'			=> date('Y-m-d H:i:s')
							);
							
							$this->db->where('id',$query[0]->id);
							$update = $this->db->update('data_project_mitra_sow', $datarow);
						}
					}
					  
				}
			}
		}
		
		
		public function setDP($dataio, $is_update){
			//echo 'test';
			if($dataio != null){
					$format = array(
						'project_id'		=> ($dataio['project_id'] != '' ? $dataio['project_id'] : null),
						'nilai'				=> ($dataio['nilai'] != '' ?$dataio['nilai'] : null)
					);
					
					$this->db->where('project_id',$format['project_id']);
					$query = $this->db->get('data_project_uang_muka');
					$query = $query->result_object();
					if(!$query){
						$datarow = array(  
							'project_id'		=> ($format['project_id'] != '' ? $format['project_id'] : null),
							'nilai'				=> ($format['nilai'] != '' ? $format['nilai'] : null),
							'status_id'			=> 1,
							'active'			=> 1,
							'createdid'			=> 1,
							'created'			=> date('Y-m-d H:i:s'),
							'modifiedid'		=> 1,
							'modified'			=> date('Y-m-d H:i:s')
						);
						
						$string = 'DP-'.$format['project_id'];
						$slug = $this->ortyd->sanitize($string,'data_project_uang_muka');
						$datarow = array_merge($datarow,
							array('slug' 	=> $slug)
						);
						
						$insert = $this->db->insert('data_project_uang_muka', $datarow);
						$insert_id = $this->db->insert_id();
						
						if($insert){
							if($is_update == 0){
								$this->ortyd->saveInbox(5, 1, $insert_id, 'Uang Muka/DP',0);
							}
						}
					}else{
						$datarow = array(  
							'project_id'		=> ($format['project_id'] != '' ? $format['project_id'] : null),
							'nilai'				=> ($format['nilai'] != '' ? $format['nilai'] : null),
							'active'			=> 1,
							'createdid'			=> 1,
							'created'			=> date('Y-m-d H:i:s'),
							'modifiedid'		=> 1,
							'modified'			=> date('Y-m-d H:i:s')
						);
						
						$string = 'DP-'.$format['project_id'];
						$slug = $this->ortyd->sanitize($string,'data_project_uang_muka');
						//$datarow = array_merge($datarow,
							//array('slug' 	=> $slug)
						//);
						
						$this->db->where('id',$query[0]->id);
						$update = $this->db->update('data_project_uang_muka', $datarow);
						if($update){
							if($is_update == 0){
								$this->ortyd->saveInbox(5, 1, $query[0]->id, 'Uang Muka/DP',0);
							}
						}
					}
			
			}
			
			
			return true;
		}
		
		
		public function setWO($project_id, $dataio, $dataproject, $is_project, $is_update){
			
			$datarow = array(  
				'active'=> 0
			);
			
			$this->db->where('project_id',$project_id);
			$update = $this->db->update('data_project_wo', $datarow);
			
			if($dataio != null){
				foreach($dataio as $rowsdata){
					
					$format = array(
						'ref_id'			=> ($rowsdata->id != '' ? $rowsdata->id : null),
						'dokumen_sp_url'	=> ($rowsdata->dokumen_sp_url != '' ? $rowsdata->dokumen_sp_url : null),
						'ao_sid_url'	=> ($rowsdata->ao_sid_url != '' ? $rowsdata->ao_sid_url : null),
						'wo_no'				=> ($rowsdata->no_surat_pesanan != '' ? $rowsdata->no_surat_pesanan : null),
						'tanggal_wo'		=> ($rowsdata->tanggal != '' ? $rowsdata->tanggal : null),
						'masa_layanan'		=> ($rowsdata->jangka_waktu_pelaksanaan != '' ? $rowsdata->jangka_waktu_pelaksanaan : null),
						'keterangan'		=> ($rowsdata->judul_surat_pesanan != '' ? $rowsdata->judul_surat_pesanan : null),
						'nilai_wo'			=> ($rowsdata->nilai_surat_pesanan != '' ? $rowsdata->nilai_surat_pesanan : null),
						'jml_bast'			=> ($rowsdata->jumlah_termin != '' ? $rowsdata->jumlah_termin : null),
						'created_at'		=> ($rowsdata->created_at != '' ? $rowsdata->created_at : null),
						'updated_at'		=> ($rowsdata->updated_at != '' ? $rowsdata->updated_at : null),
						'deleted_at'		=> ($rowsdata->deleted_at != '' ? $rowsdata->deleted_at : null),
						'no_kl'			=> ($dataproject->project->no_kl != '' ? $dataproject->project->no_kl : null),
						'nilai_kl'			=> ($dataproject->project->nilai_kl != '' ? $dataproject->project->nilai_kl : null),
						'file_kl'			=> ($dataproject->project->file_kl != '' ? $dataproject->project->file_kl : null)
					);
					
					
					if($format['nilai_kl'] != null){
						$nilai_wo = $format['nilai_kl'];
					}else{
						$nilai_wo = $format['nilai_wo'];
					}
					
					if($format['no_kl'] != null){
						$no_kl = $format['no_kl'];
					}else{
						$no_kl = $format['wo_no'];
					}
					
					$this->db->where('ref_id',$format['ref_id']);
					$query = $this->db->get('data_project_wo');
					$query = $query->result_object();
					if(!$query){
						$datarow = array(  
							'ref_id'			=> ($format['ref_id'] != '' ? $format['ref_id'] : null),
							'project_id'		=> ($project_id != '' ? $project_id : null),
							'wo_no'				=> ($no_kl != '' ? $no_kl : null),
							'tanggal_wo'		=> ($format['tanggal_wo'] != '' ? $format['tanggal_wo'] : null),
							'masa_layanan'		=> ($format['masa_layanan'] != '' ? $format['masa_layanan'] : null),
							'masa_layanan_aktif'=> ($format['masa_layanan'] != '' ? $format['masa_layanan'] : null),
							'start_aktif		'=> ($dataproject->start_layanan != '' ? $dataproject->start_layanan : null),
							'end_aktif			'=> ($dataproject->end_layanan != '' ? $dataproject->end_layanan : null),
							'nilai_layanan'		=> ($format['nilai_wo'] != '' ? $format['nilai_wo'] : null),
							'keterangan'		=> ($format['keterangan'] != '' ? $format['keterangan'] : null),
							'nilai_wo'			=> ($nilai_wo != '' ? $nilai_wo : null),
							'jml_bast'			=> ($format['jml_bast'] != '' ? $format['jml_bast'] : null),
							'active'			=> 1,
							'createdid'			=> 1,
							'created'			=> ($format['created_at'] != '' ? $format['created_at'] : null),
							'modifiedid'		=> 1,
							'modified'			=> ($format['updated_at'] != '' ? $format['updated_at'] : null)
						);
						
						$string = $format['wo_no'];
						$slug = $this->ortyd->sanitize($string,'data_project_wo');
						$datarow = array_merge($datarow,
							array('slug' 	=> $slug)
						);
						
						$insert = $this->db->insert('data_project_wo', $datarow);
						$insertid = $this->db->insert_id();
						
						if($insert){
							$wo_id = $insertid;
							$project_mitra_id = null;
							$type = 15;
							if($format['file_kl'] != null){
								$path = $format['file_kl'];
							}else{
								$path = $format['dokumen_sp_url'];
							}
							
																
							$this->saveDoc($project_id, $wo_id, $project_mitra_id, $type, $path,null);
							
							$path_ao_id = $format['ao_sid_url'];
							$this->import($path_ao_id,$wo_id);
						}
						
					}else{
						$datarow = array(  
							'ref_id'			=> ($format['ref_id'] != '' ? $format['ref_id'] : null),
							'project_id'		=> ($project_id != '' ? $project_id : null),
							'wo_no'				=> ($no_kl != '' ? $no_kl : null),
							'tanggal_wo'		=> ($format['tanggal_wo'] != '' ? $format['tanggal_wo'] : null),
							'masa_layanan'		=> ($format['masa_layanan'] != '' ? $format['masa_layanan'] : null),
							'masa_layanan_aktif'=> ($format['masa_layanan'] != '' ? $format['masa_layanan'] : null),
							'start_aktif		'=> ($dataproject->start_layanan != '' ? $dataproject->start_layanan : null),
							'end_aktif			'=> ($dataproject->end_layanan != '' ? $dataproject->end_layanan : null),
							'nilai_layanan'		=> ($format['nilai_wo'] != '' ? $format['nilai_wo'] : null),
							'keterangan'		=> ($format['keterangan'] != '' ? $format['keterangan'] : null),
							'nilai_wo'			=> ($nilai_wo != '' ? $nilai_wo : null),
							'jml_bast'			=> ($format['jml_bast'] != '' ? $format['jml_bast'] : null),
							'active'			=> 1,
							'createdid'			=> 1,
							'created'			=> ($format['created_at'] != '' ? $format['created_at'] : null),
							'modifiedid'		=> 1,
							'modified'			=> ($format['updated_at'] != '' ? $format['updated_at'] : null)
						);
						
						$string = $no_kl;
						$slug = $this->ortyd->sanitize($string,'data_project_wo');
						//$datarow = array_merge($datarow,
							//array('slug' 	=> $slug)
						//);
						
						$this->db->where('ref_id',$format['ref_id']);
						$update = $this->db->update('data_project_wo', $datarow);
						
						if($update){
							$wo_id = $query[0]->id;
							$project_mitra_id = null;
							$type = 15;
							
							if($format['file_kl'] != null){
								$path = $format['file_kl'];
							}else{
								$path = $format['dokumen_sp_url'];
							}
																
							$this->saveDoc($project_id, $wo_id, $project_mitra_id, $type, $path,null);

							
							$path_ao_id = $format['ao_sid_url'];
							$this->import($path_ao_id,$wo_id);
						}
						
					}
					
				}
				
				
				if($is_project == 1){	
					if($dataproject->tanggal_won != ''){
											
						if($dataproject->is_perpanjangan == 1){
							if($dataproject->jenis_perpanjangan == 'murni'){
								if($is_update == 0){
									$this->ortyd->saveInbox(1, 1, $dataproject->id,'Project Perpanjangan Murni',0);
								}
							}else{
								if($is_update == 0){
									$this->ortyd->saveInbox(1, 1, $dataproject->id,'Project Perpanjangan dan Pasang Baru',0);
								}
							}
						}else{
							if($is_update == 0){
								$this->ortyd->saveInbox(1, 1, $dataproject->id,'Project Pasang Baru',0);
							}
						}
											
					}
				}
					
			
			}else{
				
				$project_detail = $dataproject->project ?? '';
				$project_jasbis = $dataproject->jasbis ?? '';
				$project_dokumen_kl = $dataproject->dokumen_kl ?? '';
				
					
				if($project_detail != ''){
					
					if(($project_detail->tanggal_mulai ?? '') != '' && ($project_detail->tanggal_akhir ?? '') != ''){
						$from = ($project_detail->tanggal_mulai ?? '');
						$to = ($project_detail->tanggal_akhir ?? '');
						if($from != '' && $to != ''){
							$masa_layanan = $this->diffMonth($from, $to);
						}else{
							$masa_layanan = null;
						}
						
					}else{
						$masa_layanan = null;
					}
					

					$format = array(
						'ref_id'			=> null,
						'wo_no'				=> (($project_detail->no_kl ?? '') != '' ? ($project_detail->no_kl ?? '') : ($dataproject->no_kl ?? '-')),
						'tanggal_wo'		=> (($project_detail->tanggal_kl ?? '') != '' ? ($project_detail->tanggal_kl ?? '') : null),
						'masa_layanan'		=> $masa_layanan,
						'keterangan'		=> (($project_detail->no_kl ?? '') != '' ? ($project_detail->no_kl ?? '') : null),
						'nilai_wo'			=> (($project_detail->nilai_kl ?? '') != '' ? ($project_detail->nilai_kl ?? '') : null),
						'jml_bast'			=> (($project_detail->jumlah_top ?? '') != '' ? ($project_detail->jumlah_top ?? '') : null),
						'created_at'		=> (($project_detail->created_at ?? '') != '' ? ($project_detail->created_at ?? '') : null),
						'updated_at'		=> (($project_detail->updated_at ?? '') != '' ? ($project_detail->updated_at ?? '') : null),
						'path_wo'			=> (($project_dokumen_kl->path ?? '') != '' ? ($project_dokumen_kl->path ?? '') : null),
						'no_kl'			=> ($dataproject->project->no_kl != '' ? $dataproject->project->no_kl : null),
						'nilai_kl'			=> ($dataproject->project->nilai_kl != '' ? $dataproject->project->nilai_kl : null),
						'file_kl'			=> ($dataproject->project->file_kl != '' ? $dataproject->project->file_kl : null)
					);
					
					$this->db->where('project_id',$project_id);
					$this->db->where('wo_no',$format['wo_no']);
					$query = $this->db->get('data_project_wo');
					$query = $query->result_object();
					if(!$query){
						$datarow = array(  
							'ref_id'			=> null,
							'project_id'		=> ($project_id != '' ? $project_id : null),
							'wo_no'				=> ($format['wo_no'] != '' ? $format['wo_no'] : null),
							'tanggal_wo'		=> ($format['tanggal_wo'] != '' ? $format['tanggal_wo'] : null),
							'masa_layanan'		=> ($format['masa_layanan'] != '' ? $format['masa_layanan'] : null),
							'masa_layanan_aktif'=> ($format['masa_layanan'] != '' ? $format['masa_layanan'] : null),
							'start_aktif		'=> ($dataproject->start_layanan != '' ? $dataproject->start_layanan : null),
							'end_aktif			'=> ($dataproject->end_layanan != '' ? $dataproject->end_layanan : null),
							'nilai_layanan'		=> ($format['nilai_wo'] != '' ? $format['nilai_wo'] : null),
							'keterangan'		=> ($format['keterangan'] != '' ? $format['keterangan'] : null),
							'nilai_wo'			=> ($format['nilai_wo'] != '' ? $format['nilai_wo'] : null),
							'jml_bast'			=> ($format['jml_bast'] != '' ? $format['jml_bast'] : null),
							'active'			=> 1,
							'createdid'			=> 1,
							'created'			=> ($format['created_at'] != '' ? $format['created_at'] : null),
							'modifiedid'		=> 1,
							'modified'			=> ($format['updated_at'] != '' ? $format['updated_at'] : null)
						);
						
						$string = $format['wo_no'];
						$slug = $this->ortyd->sanitize($string,'data_project_wo');
						$datarow = array_merge($datarow,
							array('slug' 	=> $slug)
						);
						
						$insert = $this->db->insert('data_project_wo', $datarow);
						$insert_id = $this->db->insert_id();
						
						if($insert){
							
							$wo_id = $insert_id;
							$project_mitra_id = null;
							$type = 15;
							
							if($format['file_kl'] != null){
								$path = $format['file_kl'];
							}else{
								$path = $format['path_wo'];
							}
																
							$this->saveDoc($project_id, $wo_id, $project_mitra_id,$type, $path,null);
							
							if($project_jasbis != ''){
								if(($project_jasbis->ao_sid_url ?? '') != ''){
									$wo_id = $insert_id;
									$path_ao_id = ($project_jasbis->ao_sid_url ?? '');
									$this->import($path_ao_id,$wo_id);
								}
							}

						}
						
					}else{
						$datarow = array(  
							'ref_id'			=> null,
							'project_id'		=> ($project_id != '' ? $project_id : null),
							'wo_no'				=> ($format['wo_no'] != '' ? $format['wo_no'] : null),
							'tanggal_wo'		=> ($format['tanggal_wo'] != '' ? $format['tanggal_wo'] : null),
							'masa_layanan'		=> ($format['masa_layanan'] != '' ? $format['masa_layanan'] : null),
							'masa_layanan_aktif'=> ($format['masa_layanan'] != '' ? $format['masa_layanan'] : null),
							'start_aktif		'=> ($dataproject->start_layanan != '' ? $dataproject->start_layanan : null),
							'end_aktif			'=> ($dataproject->end_layanan != '' ? $dataproject->end_layanan : null),
							'nilai_layanan'		=> ($format['nilai_wo'] != '' ? $format['nilai_wo'] : null),
							'keterangan'		=> ($format['keterangan'] != '' ? $format['keterangan'] : null),
							'nilai_wo'			=> ($format['nilai_wo'] != '' ? $format['nilai_wo'] : null),
							'jml_bast'			=> ($format['jml_bast'] != '' ? $format['jml_bast'] : null),
							'active'			=> 1,
							'createdid'			=> 1,
							'created'			=> ($format['created_at'] != '' ? $format['created_at'] : null),
							'modifiedid'		=> 1,
							'modified'			=> ($format['updated_at'] != '' ? $format['updated_at'] : null)
						);
						
						$string = $format['wo_no'];
						$slug = $this->ortyd->sanitize($string,'data_project_wo');
						//$datarow = array_merge($datarow,
							//array('slug' 	=> $slug)
						//);
						
						$this->db->where('project_id',$project_id);
						$this->db->where('wo_no',$format['wo_no']);
						$update = $this->db->update('data_project_wo', $datarow);
						
						if($update){
							
							$wo_id = $query[0]->id;
							$project_mitra_id = null;
							$type = 15;
							if($format['file_kl'] != null){
								$path = $format['file_kl'];
							}else{
								$path = $format['path_wo'];
							}
							
							$this->saveDoc($project_id, $wo_id, $project_mitra_id,$type, $path,null);
							
							if($project_jasbis != ''){
								if(($project_jasbis->ao_sid_url ?? '') != ''){
									$wo_id = $query[0]->id;
									$path_ao_id = ($project_jasbis->ao_sid_url ?? '');
									$this->import($path_ao_id,$wo_id);
								}
							}
				
							
						}
					}
					
					if($is_project == 1){	
						if($dataproject->tanggal_won != ''){
												
							if($dataproject->is_perpanjangan == 1){
								if($dataproject->jenis_perpanjangan == 'murni'){
									if($is_update == 0){
										$this->ortyd->saveInbox(1, 1, $dataproject->id,'Project Perpanjangan Murni',0);
									}
								}else{
									if($is_update == 0){
										$this->ortyd->saveInbox(1, 1, $dataproject->id,'Project Perpanjangan dan Pasang Baru',0);
									}
								}
							}else{
								if($is_update == 0){
									$this->ortyd->saveInbox(1, 1, $dataproject->id,'Project Pasang Baru',0);
								}
							}
												
						}
					}
						
				}else{
					
					return false;
					
					if(($dataproject->start_layanan ?? '') != '' && ($dataproject->end_layanan ?? '') != ''){
						$from = ($dataproject->start_layanan ?? '');
						$to = ($dataproject->end_layanan ?? '');
						if($from != '' && $to != ''){
							$masa_layanan = $this->diffMonth($from, $to);
						}else{
							$masa_layanan = null;
						}
						
					}else{
						$masa_layanan = null;
					}
					

					$format = array(
						'ref_id'			=> null,
						'wo_no'				=> (($dataproject->no_kl ?? '') != '' ? ($dataproject->no_kl ?? '-') : ($dataproject->no_kl ?? '-') ),
						'tanggal_wo'		=> (($dataproject->start_layanan ?? '') != '' ? ($dataproject->start_layanan ?? '') : null),
						'masa_layanan'		=> $masa_layanan,
						'keterangan'		=> (($dataproject->no_kl ?? '') != '' ? ($dataproject->no_kl ?? '') : null),
						'nilai_wo'			=> (($dataproject->nilai_kl ?? '') != '' ? ($dataproject->nilai_kl ?? '') : null),
						'jml_bast'			=> 0,
						'created_at'		=> (($dataproject->created_at ?? '') != '' ? ($dataproject->created_at ?? '') : null),
						'updated_at'		=> (($dataproject->updated_at ?? '') != '' ? ($dataproject->updated_at ?? '') : null),
					);
					
					$this->db->where('project_id',$project_id);
					$this->db->where('wo_no',$format['wo_no']);
					$query = $this->db->get('data_project_wo');
					$query = $query->result_object();
					if(!$query){
						$datarow = array(  
							'ref_id'			=> null,
							'project_id'		=> ($project_id != '' ? $project_id : null),
							'wo_no'				=> ($format['wo_no'] != '' ? $format['wo_no'] : null),
							'tanggal_wo'		=> ($format['tanggal_wo'] != '' ? $format['tanggal_wo'] : null),
							'masa_layanan'		=> ($format['masa_layanan'] != '' ? $format['masa_layanan'] : null),
							'keterangan'		=> ($format['keterangan'] != '' ? $format['keterangan'] : null),
							'nilai_wo'			=> ($format['nilai_wo'] != '' ? $format['nilai_wo'] : null),
							'jml_bast'			=> ($format['jml_bast'] != '' ? $format['jml_bast'] : null),
							'active'			=> 1,
							'createdid'			=> 1,
							'created'			=> ($format['created_at'] != '' ? $format['created_at'] : null),
							'modifiedid'		=> 1,
							'modified'			=> ($format['updated_at'] != '' ? $format['updated_at'] : null)
						);
						
						$string = $format['wo_no'];
						$slug = $this->ortyd->sanitize($string,'data_project_wo');
						$datarow = array_merge($datarow,
							array('slug' 	=> $slug)
						);
						
						$insert = $this->db->insert('data_project_wo', $datarow);
					}else{
						$datarow = array(  
							'ref_id'			=> null,
							'project_id'		=> ($project_id != '' ? $project_id : null),
							'wo_no'				=> ($format['wo_no'] != '' ? $format['wo_no'] : null),
							'tanggal_wo'		=> ($format['tanggal_wo'] != '' ? $format['tanggal_wo'] : null),
							'masa_layanan'		=> ($format['masa_layanan'] != '' ? $format['masa_layanan'] : null),
							'keterangan'		=> ($format['keterangan'] != '' ? $format['keterangan'] : null),
							'nilai_wo'			=> ($format['nilai_wo'] != '' ? $format['nilai_wo'] : null),
							'jml_bast'			=> ($format['jml_bast'] != '' ? $format['jml_bast'] : null),
							'active'			=> 1,
							'createdid'			=> 1,
							'created'			=> ($format['created_at'] != '' ? $format['created_at'] : null),
							'modifiedid'		=> 1,
							'modified'			=> ($format['updated_at'] != '' ? $format['updated_at'] : null)
						);
						
						$string = $format['wo_no'];
						$slug = $this->ortyd->sanitize($string,'data_project_wo');
						//$datarow = array_merge($datarow,
							//array('slug' 	=> $slug)
						//);
						
						$this->db->where('project_id',$project_id);
						$this->db->where('wo_no',$format['wo_no']);
						$update = $this->db->update('data_project_wo', $datarow);
					}
					
				}
					
					
					
			}
			
			
			return true;
		}
		
		function diffMonth($from, $to) {

			$fromYear = date("Y", strtotime($from));
			$fromMonth = date("m", strtotime($from));
			$toYear = date("Y", strtotime($to));
			$toMonth = date("m", strtotime($to));
			if ($fromYear == $toYear) {
				return ($toMonth-$fromMonth)+1;
			} else {
				return (12-$fromMonth)+1+$toMonth;
			}

		}
		
		public function setrunning($id_api, $month, $year, $user_id){
			$this->db->select('id, status');
			$this->db->where('id_api',$id_api);
			$this->db->where('month',$month);
			$this->db->where('year',$year);
			$query = $this->db->get('api_header');
			$query = $query->result_object();
			if($query){
				if($query[0]->status == 0){
					$this->updatestatus($query[0]->id, $id_api, $month, $year, $query[0]->status, null, null,null,null, $user_id);
					return 1;
				}else{
					return 0;
				}
			}else{
				$this->updatestatus(null, $id_api, $month, $year, 2, null, null,null,null, $user_id);
				return 1;
			}
			
			return 0;
		}
		
		
		public function setstop($id_api, $month, $year, $status_code, $user_id){
			$this->db->select('id, status, running_date, page, row');
			$this->db->where('id_api',$id_api);
			$this->db->where('month',$month);
			$this->db->where('year',$year);
			$this->db->where('status',1);
			$query = $this->db->get('api_header');
			$query = $query->result_object();
			if($query){
				$this->updatestatus($query[0]->id, $id_api, $month, $year, $query[0]->status, $status_code, $query[0]->running_date, $query[0]->page, $query[0]->row, $user_id);
				return 1;
			}
			
			return 0;
		}
		
		public function updatepage($id_api, $month, $year, $page, $row){
			$this->db->select('id, status, running_date, page, row');
			$this->db->where('id_api',$id_api);
			$this->db->where('month',$month);
			$this->db->where('year',$year);
			$this->db->where('status',1);
			$query = $this->db->get('api_header');
			$query = $query->result_object();
			if($query){
				$page = (int)$page;
				$row = (int)$query[0]->row + (int)$row;
				$this->updatepagerow($query[0]->id, $page, $row);
				return 1;
			}
			
			return 0;
		}
		
		function updatepagerow($id, $page, $row){
			$datarow = array(  
					"page" 			=> $page,
					"row"			=> $row,
					'modifiedid'	=> 1,
					'modified'		=> date('Y-m-d H:i:s')
			);
				
			$this->db->where('id',$id);
			$update = $this->db->update('api_header', $datarow);
		}
		
		
		function updatestatus($id, $id_api, $month, $year, $status, $status_code, $running_date, $page, $row, $user_id){
			if($status == 0){
				
				$datarow = array(  
					"id_api" 			=> $id_api,
					"month"				=> $month,
					"year" 				=> $year,
					"page"				=> null,
					"row"				=> null,
					"status"			=> 1,
					"status_code"		=> $status_code,
					"running_date"		=> date('Y-m-d H:i:s'),
					"stop_date"			=> null,
					'createdid'			=> $user_id,
					'created'			=> date('Y-m-d H:i:s'),
					'modifiedid'		=> $user_id,
					'modified'			=> date('Y-m-d H:i:s')
				);
				
				$this->db->where('id',$id);
				$update = $this->db->update('api_header', $datarow);
				if($update){
					$api_header_id = $id;
					$page = null;
					$row = null;
					$running_date = date('Y-m-d H:i:s');
					$stop_date = null;
					$this->inserthistory($api_header_id, $page, $row, 1, $status_code, $running_date, $stop_date, $user_id);
				}
				return $id;
				
			}elseif($status == 1){
				
				$datarow = array(  
					"id_api" 			=> $id_api,
					"month"				=> $month,
					"year" 				=> $year,
					"page" 				=> $page,
					"row" 				=> $row,
					"status"			=> 0,
					"status_code"		=> $status_code,
					//"running_date"		=> date('Y-m-d H:i:s'),
					"stop_date"			=> date('Y-m-d H:i:s'),
					'createdid'			=> $user_id,
					'created'			=> date('Y-m-d H:i:s'),
					'modifiedid'		=> $user_id,
					'modified'			=> date('Y-m-d H:i:s')
				);
				
				$this->db->where('id',$id);
				$update = $this->db->update('api_header', $datarow);
				if($update){
					$api_header_id = $id;
					$page = $page;
					$row = $row;
					$running_date = $running_date;
					$stop_date = date('Y-m-d H:i:s');
					$this->inserthistory($api_header_id, $page, $row, 0, $status_code, $running_date, $stop_date, $user_id);
				}
				return $id;
				
			}else{
				$datarow = array(  
					"id_api" 			=> $id_api,
					"month"				=> $month,
					"year" 				=> $year,
					"page"				=> null,
					"row"				=> null,
					"status"			=> 1,
					"status_code"		=> null,
					"running_date"		=> date('Y-m-d H:i:s'),
					"stop_date"			=> null,
					'createdid'			=> $user_id,
					'created'			=> date('Y-m-d H:i:s'),
					'modifiedid'		=> $user_id,
					'modified'			=> date('Y-m-d H:i:s')
				);
				
				$insert = $this->db->insert('api_header', $datarow);
				$insert_id = $this->db->insert_id();
				
				if($insert){
					$api_header_id = $insert_id;
					$page = null;
					$row = null;
					$running_date = date('Y-m-d H:i:s');
					$stop_date = null;
					$this->inserthistory($api_header_id, $page, $row, 1, null, $running_date, $stop_date, $user_id);
				}
				
				return $insert_id;	
			}
			
			return false;
		}
		
		
		function inserthistory($api_header_id, $page, $row, $status, $status_code, $running_date, $stop_date, $user_id){
		
			$datarow = array(  
				"api_header_id" 	=> $api_header_id,
				"page"				=> $page,
				"row"				=> $row,
				"status"			=> $status,
				"status_code"		=> $status_code,
				"running_date"		=> $running_date,
				"stop_date"			=> $stop_date,
				'createdid'			=> $user_id,
				'created'			=> date('Y-m-d H:i:s'),
				'modifiedid'		=> $user_id,
				'modified'			=> date('Y-m-d H:i:s')
			);
				
			$insert = $this->db->insert('api_history', $datarow);
			$insert_id = $this->db->insert_id();
			return $insert_id;	

		}
		
		function saveDoc($project_id, $wo_id, $project_mitra_id, $type, $file_url_ex, $nomor){
			
			$file_id = $this->setGallery($file_url_ex);
			
			//if($file_id != null){
				
				$this->db->select('id');
				$this->db->where('project_id',$project_id);
				if($project_mitra_id != null){
					$this->db->where('project_mitra_id',$project_mitra_id);
				}
				if($wo_id != null){
					$this->db->where('wo_id',$wo_id);
				}
				$this->db->where('dokumen_id',$type);
				$query = $this->db->get('data_project_dokumen');
				$query = $query->result_object();
				if(!$query){
						
					$data = array(
						'project_id'		=> $project_id,
						'wo_id'				=> $wo_id,
						'project_mitra_id'	=> $project_mitra_id,
						'dokumen_id'		=> $type,
						'file_id'			=> $file_id,
						'nomor'			=> $nomor,
						'createdid'		=> 1,
						'created'		=> date('Y-m-d H:i:s'),
						'modifiedid'	=> 1,
						'modified'		=> date('Y-m-d H:i:s')
					);
								
					$this->db->insert('data_project_dokumen',$data);
					$insertid = $this->db->insert_id();
					
				}else{
						
					$data = array(
						'project_id'		=> $project_id,
						'wo_id'				=> $wo_id,
						'project_mitra_id'	=> $project_mitra_id,
						'dokumen_id'		=> $type,
						'file_id'			=> $file_id,
						'nomor'			=> $nomor,
						'modifiedid'	=> 1,
						'modified'		=> date('Y-m-d H:i:s')
					);
						
					$this->db->where('id',$query[0]->id);
					$this->db->update('data_project_dokumen',$data);
					$insertid = $query[0]->id;
						
				}
			
			//}
		}
		
		function setGallery($file_url_ex){
			
			if($file_url_ex != null && $file_url_ex != ''){
				
				$strArray = explode('/',$file_url_ex);
			
				if(count($strArray) > 0){
					
					$file_name = end($strArray);
					$file_namenya = explode('.',$file_name);
					$ext = end($file_namenya);
					$file_url = $this->link_pins;
					$path = str_replace($file_url,'',$file_url_ex);
					$size = 1;
					$token = '11.'.date('YmdHis').rand(1,1000);
					
					$this->db->select('id');
					$this->db->where('path',$path);
					$query = $this->db->get('data_gallery');
					$query = $query->result_object();
					if(!$query){
						
						$data = array(
							'name'			=> $file_name,
							'file_size'		=> $size * 1000,
							'token'			=> $token,
							'path'			=> $path,
							'path_server'	=> './'.$path,
							'createdid'		=> 1,
							'created'		=> date('Y-m-d H:i:s'),
							'modifiedid'	=> 1,
							'modified'		=> date('Y-m-d H:i:s'),
							'file_store_format'	=> $ext,
							'url_server'	=> $file_url
						);
								
						$this->db->insert('data_gallery',$data);
						$insertid = $this->db->insert_id();
					
					}else{
						
						$data = array(
							'name'			=> $file_name,
							'file_size'		=> $size * 1000,
							'token'			=> $token,
							'path'			=> $path,
							'path_server'	=> './'.$path,
							'modifiedid'	=> 1,
							'modified'		=> date('Y-m-d H:i:s'),
							'file_store_format'	=> $ext,
							'url_server'	=> $file_url
						);
						
						$this->db->where('path',$path);
						$this->db->update('data_gallery',$data);
						$insertid = $query[0]->id;
						
					}
					
					return $insertid;
				}
			
			}
			
			
			return null;
			
		}
		
		
		function import($path, $wo_id){
			
			try {
				
			$this->load->library('excel');
			
			$importdata = 1;
			if($path != null && $path != ''){
				
				if (file_exists('./file/filename.xlsx')) {
					unlink('./file/filename.xlsx');
				}
				
				file_put_contents('./file/filename.xlsx',
				  file_get_contents($path)
				);
		
				if($importdata){
					$path = $path;
					$object = PHPExcel_IOFactory::load("filename.xlsx");
					$detail = array();
					$x=0;
					$sheet = 1;
					
					$sheetCount = $object->getSheetCount();
					if($sheetCount){
						foreach($object->getWorksheetIterator() as $worksheet)
						{
							if($sheet == 1){
								//print_r($worksheet);
								//die();
								$highestRow = $worksheet->getHighestRow();
								$highestColumn = $worksheet->getHighestColumn();
								$x=0;
								for($row=2; $row<=$highestRow; $row++)
									{
										
										$project_io = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
										$wo_no = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
										
										//$wo_id = $this->ortyd->select2_getname($wo_no,'data_project_wo','wo_no','id');
										
										$name = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
										$description = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
										$ao = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
										$sid = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
										$snid = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
										$pic = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
										$notelp = $worksheet->getCellByColumnAndRow(7, $row)->getValue();
										$lokasi = $worksheet->getCellByColumnAndRow(8, $row)->getValue();
										$batch = $worksheet->getCellByColumnAndRow(9, $row)->getValue();
										$owner = $worksheet->getCellByColumnAndRow(10, $row)->getValue();
										$keterangan = $worksheet->getCellByColumnAndRow(11, $row)->getValue();
										$string = $name;
										$slug = $this->ortyd->sanitize($string,'master_perangkat');
										
										
										$this->db->where('ao',$ao);
										$query = $this->db->get('master_perangkat');
										$query = $query->result_object();
										if(!$query){
											
											$data = array(
												'name' 				=> $name,
												'description' 		=> $description,
												'ao' 				=> $ao,
												'sid' 				=> $sid,
												'snid' 				=> $snid,
												'pic' 				=> $pic,
												'notelp' 			=> $notelp,
												'lokasi' 			=> $lokasi,
												'batch' 			=> $batch,
												'owner' 			=> $owner,
												'keterangan' 		=> $keterangan,
												'slug' 				=> $slug,
												'active' 			=> 1,
												'createdid'			=> $this->session->userdata('userid'),
												'created'			=> date('Y-m-d H:i:s'),
												'modifiedid'		=> $this->session->userdata('userid'),
												'modified'			=> date('Y-m-d H:i:s')
											);
											
											$insert = $this->db->insert('master_perangkat', $data);
											$insert_id = $this->db->insert_id();
											
											if($insert){
												
												$this->db->where('wo_id',$wo_id);
												$this->db->where('perangkat_id',$insert_id);
												$query = $this->db->get('data_project_perangkat');
												$query = $query->result_object();
												if(!$query){
													
													$data = array(
														'wo_id' 			=> $wo_id,
														'perangkat_id' 		=> $insert_id,
														'slug' 				=> 'perangkat-'.$wo_id.'-'.$insert_id,
														'active' 			=> 1,
														'createdid'			=> $this->session->userdata('userid'),
														'created'			=> date('Y-m-d H:i:s'),
														'modifiedid'		=> $this->session->userdata('userid'),
														'modified'			=> date('Y-m-d H:i:s')
													);
													
													$insert = $this->db->insert('data_project_perangkat', $data);
													$insert_id = $this->db->insert_id();
													
												}else{
													
													$data = array(
														'wo_id' 			=> $wo_id,
														'perangkat_id' 		=> $insert_id,
														'active' 			=> 1,
														'modifiedid'		=> $this->session->userdata('userid'),
														'modified'			=> date('Y-m-d H:i:s')
													);
													
													$this->db->where('wo_id',$wo_id);
													$this->db->where('perangkat_id',$insert_id);
													$update = $this->db->update('data_project_perangkat', $data);
													$insert_id = $query[0]->id;
													
												}
												
											}
											
										}else{
											
											
											$data = array(
												'name' 				=> $name,
												'description' 		=> $description,
												'ao' 				=> $ao,
												'sid' 				=> $sid,
												'snid' 				=> $snid,
												'pic' 				=> $pic,
												'notelp' 			=> $notelp,
												'lokasi' 			=> $lokasi,
												'batch' 			=> $batch,
												'owner' 			=> $owner,
												'keterangan' 		=> $keterangan,
												'slug' 				=> $slug,
												'active' 			=> 1,
												'createdid'			=> $this->session->userdata('userid'),
												'created'			=> date('Y-m-d H:i:s'),
												'modifiedid'		=> $this->session->userdata('userid'),
												'modified'			=> date('Y-m-d H:i:s')
											);
											
											$this->db->where('ao',$ao);
											$update = $this->db->update('master_perangkat', $data);
											$insert_id = $query[0]->id;
											
											if($update){
												
												$this->db->where('wo_id',$wo_id);
												$this->db->where('perangkat_id',$insert_id);
												$query = $this->db->get('data_project_perangkat');
												$query = $query->result_object();
												if(!$query){
													
													$data = array(
														'wo_id' 			=> $wo_id,
														'perangkat_id' 		=> $insert_id,
														'slug' 				=> 'perangkat-'.$wo_id.'-'.$insert_id,
														'active' 			=> 1,
														'createdid'			=> $this->session->userdata('userid'),
														'created'			=> date('Y-m-d H:i:s'),
														'modifiedid'		=> $this->session->userdata('userid'),
														'modified'			=> date('Y-m-d H:i:s')
													);
													
													$insert = $this->db->insert('data_project_perangkat', $data);
													$insert_id = $this->db->insert_id();
													
												}else{
													
													$data = array(
														'wo_id' 			=> $wo_id,
														'perangkat_id' 		=> $insert_id,
														'active' 			=> 1,
														'modifiedid'		=> $this->session->userdata('userid'),
														'modified'			=> date('Y-m-d H:i:s')
													);
													
													$this->db->where('wo_id',$wo_id);
													$this->db->where('perangkat_id',$insert_id);
													$update = $this->db->update('data_project_perangkat', $data);
													$insert_id = $query[0]->id;
													
												}
												
											}
											
										}
										
									}
									
								break;
							}else{
								$sheet++;
							}
							
						}
						
						$result = array("message" => "success");
						return json_encode($result);
					
					}else{
						$result = array("message" => "error", 'errors' => 'Sheet Not Found','sheet' => $sheetCount);
						return json_encode($result);
					}
				}else{
					$result = array("message" => "error", 'errors' => 'File Not Uploads');
					return json_encode($result);
				}
		  }else{
			  $result = array("message" => "error", 'errors' => 'Wrong Format or Data');
			  return json_encode($result);
		  }
		  
		  } catch (Exception $e) {
			  return false;
		  }
		  
	}
		
}	