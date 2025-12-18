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
}	