<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class M_login extends CI_Model {
	
		public function __construct()
		{
			parent::__construct();
		}
		
		public function check_login($username, $password){
			
			$email_sso = $this->session->userdata('email_sso');
			if($email_sso != ''){
				
				$username = "'".$email_sso."'";
				$this->db->select('id,username,email,password,gid,banned, last_login ,fullname,validate');
				$this->db->where('lower(username) = '.strtolower($username).' and active = 1',null);
				$this->db->or_where('lower(email) = '.strtolower($username).' and active = 1',null);

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
										
									return 'firstblood';
									
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
											return 'firstblood';
										}else{
											return 'success';
										}

											
									}
								
									return 'success';
									
								}
									
							}
						}
					}
				}
				
			}else{
				//return 0;
				
				$data = array(
										'last_login' => date('Y-m-d H:i:s')
									);
									
									//$this->db->where('id', 1);
									$sql = $this->db->_get_compiled_insert('users_data',$data);
									return $sql;
									
				$username = "'".$username."'";
				
				$this->db->from('users_data');
				$this->db->select('id,username,email,password,gid,banned, last_login ,fullname,validate');
				$this->db->where('lower(username) = '.strtolower($username).' and active = 1',null);
				$this->db->or_where('lower(email) = '.strtolower($username).' and active = 1',null);
				
				return $this->db->get_compiled_select();
				
				$query = $this->db->get('users_data');
				$query = $query->result_object();
				if($query){
					foreach ($query as $rows) {
						if($rows->banned == 1){
							return 'banned';
						}elseif($rows->validate == 0){
							return 'validate';
						}else{
							if($this->ortyd->verify_hash($password, $rows->password)){
								
								
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
										
									return 'firstblood';
									
								}else{
									
									$data = array(
										'last_login' => date('Y-m-d H:i:s')
									);
									
									$this->db->where('id', $rows->id);
									echo $this->db->get_compiled_update();
									
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
											return 'firstblood';
										}else{
											return 'success';
										}

											
									}
								
									return 'success';
									
								}
									
							}
						}
					}
				}
				
			}
			
			
			
			return 'error';
			
		}
		
	
}	