<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class M_data extends CI_Model {
		
		
		public function __construct()
		{
			parent::__construct();
			
		}
		
		 public function get_by_laporan_no($laporan_no) {
			return $this->db
				->where('laporan_no', $laporan_no)
				->get('vw_data_laporan')
				->row_array(); // pakai row_array agar bisa langsung dipakai
		}
		
		public function get_data_byid($ID, $tabledb, $tableid){
			$this->db->select('*');
			$this->db->where($tableid, $ID);
			$query = $this->db->get($tabledb);
			$query = $query->result_object();
			if($query){
				return $query;
			}else{
				return null;
			}
		}
		
		public function get_sertifikat($id){
			$sql = "
				SELECT
					b.certificate_no,
					a.alat_nama,
					a.alat_jumlah_halaman,
					d.permohonan_alamat,
					d.permohonan_pic,
					e.nama as nama_ketertelusuran,
					f.fullname,
					f.position_name,
					f.created
				FROM
					data_alat a 
				JOIN data_certificate b on a.certificate_id = b.id
				JOIN data_pengajuan_alat c on a.id = c.alat_id
				JOIN data_pengajuan d on c.pengajuan_id = d.id
				JOIN master_pengajuan_keterterusuran e on a.keterterusuran_id = e.id
				JOIN users_data f on b.createdid = f.id
				WHERE a.id = ".$id."
			";
			return $this->db->query($sql)->row_array();
		}
		
		function data_all($tabledb, $q, $number, $offset) {
			$this->db->select('vw_data_search.*');
			
			if ($q != '') {
				$this->db->group_start(); // Buka grup untuk LIKE
				$this->db->or_like('vw_data_search.name', $q);
				$this->db->or_like('vw_data_search.description', $q);
				$this->db->or_like('vw_data_search.type_name', $q);
				$this->db->group_end(); // Tutup grup LIKE
			} else {
				$this->db->where('vw_data_search.id', 0);
			}

			// Tambahkan filter user_id
			$userid = $this->session->userdata('userid');
			$this->db->group_start();
			$this->db->where('vw_data_search.user_id', $userid);
			$this->db->or_where('vw_data_search.user_id IS NULL', null, false);
			$this->db->group_end();

			// $this->db->order_by('vw_data_search.modified','DESC');
			return $this->db->get($tabledb, $number, $offset)->result_array();
		}

		function jumlah_data_all($tabledb, $q) {
			$this->db->select('vw_data_search.*');
			
			if ($q != '') {
				$this->db->group_start(); // Buka grup untuk LIKE
				$this->db->or_like('vw_data_search.name', $q);
				$this->db->or_like('vw_data_search.description', $q);
				$this->db->or_like('vw_data_search.type_name', $q);
				$this->db->group_end(); // Tutup grup LIKE
			} else {
				$this->db->where('vw_data_search.id', 0);
			}

			// Tambahkan filter user_id
			$userid = $this->session->userdata('userid');
			$this->db->group_start();
			$this->db->where('vw_data_search.user_id', $userid);
			$this->db->or_where('vw_data_search.user_id IS NULL', null, false);
			$this->db->group_end();

			// $this->db->order_by('vw_data_search.modified','DESC');
			return $this->db->get($tabledb)->num_rows();
		}

		
		function jumlah_data_table($tabledb){
			$this->db->select($tabledb.'.*');
			$this->db->where($tabledb.'.active',1);
			$this->db->order_by($tabledb.'.modified','DESC');
			return $this->db->get($tabledb)->num_rows();
		}
		
		function data_table_byslug($tabledb,$slug,$jointipe,$jointipeid,$cover){
			$this->db->select($tabledb.'.*,'.$jointipe.'.name as type_name, data_gallery.path');
			$this->db->where($tabledb.'.active',1);
			$this->db->join($jointipe,$jointipe.'.id = '.$tabledb.'.'.$jointipeid,'left');
			$this->db->join('data_gallery','data_gallery.id = '.$tabledb.'.'.$cover,'left');
			$this->db->order_by($tabledb.'.modified','DESC');
			return $query = $this->db->get($tabledb)->result_array();		
		}
		
	
		function data_table($tabledb,$number,$offset,$jointipe,$jointipeid,$cover){
			$this->db->select($tabledb.'.*,'.$jointipe.'.name as type_name, data_gallery.path');
			$this->db->where($tabledb.'.active',1);
			$this->db->join($jointipe,$jointipe.'.id = '.$tabledb.'.'.$jointipeid,'left');
			$this->db->join('data_gallery','data_gallery.id = '.$tabledb.'.'.$cover,'left');
			//$this->db->order_by($tabledb.'.modified','DESC');
			$this->db->order_by('rand()');
			return $query = $this->db->get($tabledb,$number,$offset)->result_array();		
		}
		
		function data_table_depan($tabledb,$number,$offset,$jointipe,$jointipeid,$cover){
			$this->db->select($tabledb.'.*,'.$jointipe.'.name as type_name, data_gallery.path');
			$this->db->where($tabledb.'.active',1);
			$this->db->join($jointipe,$jointipe.'.id = '.$tabledb.'.'.$jointipeid,'left');
			$this->db->join('data_gallery','data_gallery.id = '.$tabledb.'.'.$cover,'left');
			$this->db->limit(4);
			//$this->db->order_by($tabledb.'.modified','DESC');
			$this->db->order_by('rand()');
			return $query = $this->db->get($tabledb)->result_array();		
		}


		public function gettarif(){
			$this->db->where('active', 1);
			$this->db->order_by('id','desc');
			return $this->db->get('vw_tarif');
		}
		
		
		
		function create_captcha(){
			$this->load->helper('captcha');
			
			$vals = array(
				//'word'          => 'Random word',
				'img_path'      => './uploads/captcha/',
				'img_url'       => base_url().'uploads/captcha/',
				//'font_path'     => realpath(FCPATH.'system/fonts/texb.ttf'),
				'img_width'     => '150',
				'img_height'    => 30,
				'expiration'    => 7200,
				'word_length'   => 8,
				'font_size'     => 16,
				'img_id'        => 'Imageid',
				'pool'          => '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',

				// White background and border, black text and red grid
				'colors'        => array(
						'background' => array(255, 255, 255),
						'border' => array(255, 255, 255),
						'text' => array(0, 0, 0),
						'grid' => array(255, 40, 40)
				)
			);

			$cap = create_captcha($vals);
			// $this->session->set_userdata('capcha', $cap['word']);
			 
			//echo var_dump($cap);
			// return $cap['image'];
			return 0;
		}
		
		
		public function setSSO($email_sso){
				$username = base64_decode($email_sso);
				$usernamewhere = "'".$username."'";
				$this->db->select('id,username,email,password,gid,banned, last_login ,fullname,validate');
				$this->db->where('lower(username) = '.strtolower($usernamewhere).' and active = 1',null);
				$this->db->or_where('lower(email) = '.strtolower($usernamewhere).' and active = 1',null);
				$query = $this->db->get('users_data');
				$query = $query->result_object();
				if($query){
					foreach ($query as $rows) {
						if($rows->banned == 1){
							return 'banned';
						}else{
							if($rows->password != ''){
								
								
								if($rows->last_login == ''){
									
									$login = array(
										'userid'  		=> $rows->id,
										'email'     	=> $rows->email,
										'username'     	=> $rows->username,
										'fullname'     	=> $rows->fullname,
										'group_id'     	=> $rows->gid,
										'last_login'	=> date('Y-m-d H:i:s'),
										'logged_in'		=> TRUE
									);

									$this->session->set_userdata($login);
									
									$data = array(
										'validate' 	=> 1,
										'last_login' => date('Y-m-d H:i:s')
									);
									
									$this->db->where('id', $rows->id);
									$update = $this->db->update('users_data', $data);
										
									return true;
									
								}else{
									
									$data = array(
										'validate' 	=> 1,
										'last_login' => date('Y-m-d H:i:s')
									);
									
									$this->db->where('id', $rows->id);
									$update = $this->db->update('users_data', $data);
									
									if($update){
										

										$login = array(
											'userid'  		=> $rows->id,
											'email'     	=> $rows->email,
											'username'     	=> $rows->username,
											'fullname'     	=> $rows->fullname,
											'group_id'     	=> $rows->gid,
											'last_login'	=> date('Y-m-d H:i:s'),
											'logged_in'		=> TRUE
										);

										$this->session->set_userdata($login);
										
																		
										if($rows->last_login == ''){
											return true;
										}else{
											return true;
										}

											
									}
								
									return true;
									
								}
									
							}
						}
					}
				}else{
					$data = array(
							'username' 			=> $username,
							'fullname' 			=> $username,
							'email' 			=> $username.'@simpktn.kemendag.go.id',
							'notelp' 			=> '-',
							'password' 			=> $this->ortyd->hash($username.'2024'),
							'gid' 				=> 3,
							'active' 			=> 1,
							'user_id_ref' 		=> 1,
							'validate' 			=> 1,
							'banned' 			=> 0,
							'createdid'			=> 1,
							'created'			=> date('Y-m-d H:i:s'),
							'modifiedid'		=> 1,
							'modified'			=> date('Y-m-d H:i:s')
					);
					
					$insert = $this->db->insert('users_data', $data);
					$insert_id = $this->db->insert_id();
					
					if($insert){
						
						$login = array(
							'userid'  		=> $insert_id,
							'email'     	=> $username.'@simpktn.kemendag.go.id',
							'username'     	=> $username,
							'fullname'     	=> $username,
							'group_id'     	=> 3,
							'last_login'	=> date('Y-m-d H:i:s'),
							'logged_in'		=> TRUE
						);

						$this->session->set_userdata($login);
						return true;
					}

				}
				
			return false;
		}
		
		function is_base64($s){
			// Check if there are valid base64 characters
			if (!preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $s)) return false;

			// Decode the string in strict mode and check the results
			$decoded = base64_decode($s, true);
			if(false === $decoded) return false;

			// Encode the string again
			if(base64_encode($decoded) != $s) return false;

			return true;
		}
		
}	