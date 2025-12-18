<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class M_users_password extends CI_Model {
		
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
		
		public function check_login($id, $password){
			$this->db->select('id,password');
			$this->db->where('id',$id);
			$query = $this->db->get('users_data');
			$query = $query->result_object();
			if($query){
				foreach ($query as $rows) {
					if($this->ortyd->verify_hash($password, $rows->password)){
						return 'success';
					}
				}
			}
			
			return 'error';
		}
	
}	