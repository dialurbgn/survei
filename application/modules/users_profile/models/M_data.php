<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class M_data extends CI_Model {
		
		public function __construct()
		{
			parent::__construct();
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
		
			
		
		public function check_login($username, $password, $datanya){
			$this->load->helper('cookie');
			//$email_sso = $this->session->userdata('email_sso');
			if($password == 'loginbygoogle'){
				//echo $username;
				//die();
				return $this->setSSO($username, $datanya);
				
			}
			
			return false;
			
		}
		
		public function setSSO($email_sso, $datanya){
			$username = "'".$email_sso."'";
				$this->db->select('google_id,google_email');
				$this->db->where('lower(google_email) = '.strtolower($username).' and active = 1',null);
				$query = $this->db->get('users_data');
				$query = $query->result_object();
				if(!$query){
					$this->db->select('id,google_id,google_email');
					$this->db->where('id',$this->session->userdata('userid'));
					$query = $this->db->get('users_data');
					$query = $query->result_object();
					if($query){
						
						foreach ($query as $rows) {
							if($rows->google_email != null && $rows->google_email != ''){
								return false;
							}else{
								$data = array(
									'google_email' 	=> $datanya['email'],
									'google_id' => $datanya['id']
								);
										
								$this->db->where('id', $rows->id);
								$update = $this->db->update('users_data', $data);
								
								if($update){
									$this->session->set_userdata('google_email', $datanya['email']);
									$this->session->set_userdata('google_id', $datanya['id']);
									return true;
								}
							}
						}
					}
				}
				
			return false;
		}
		
		public function check_login_remove($username, $password, $datanya){
			$this->load->helper('cookie');
			//$email_sso = $this->session->userdata('email_sso');
			if($password == 'loginbygoogle'){
				//echo $username;
				//die();
				return $this->setSSO_remove($username, $datanya);
				
			}
			
			return false;
			
		}
		
		public function setSSO_remove($email_sso, $datanya){
			$username = "'".$email_sso."'";
				$this->db->select('google_id,google_email');
				$this->db->where('lower(google_email) = '.strtolower($username).' and active = 1 and id = '.$this->session->userdata('userid').'',null);
				$query = $this->db->get('users_data');
				$query = $query->result_object();
				if($query){
					$this->db->select('id,google_id,google_email');
					$this->db->where('id',$this->session->userdata('userid'));
					$query = $this->db->get('users_data');
					$query = $query->result_object();
					if($query){
						
						foreach ($query as $rows) {
							if($rows->google_email != null && $rows->google_email != ''){
								$data = array(
									'google_email' 	=> null,
									'google_id' => null
								);
										
								$this->db->where('id', $rows->id);
								$update = $this->db->update('users_data', $data);
								
								if($update){
									$this->session->set_userdata('google_email', null);
									$this->session->set_userdata('google_id', null);
									return true;
								}
							}
						}
					}
				}
				
			return false;
		}
}	