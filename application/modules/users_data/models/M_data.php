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
		
		// Helper function to get or create master data ID
		public function getMasterId($table, $field, $value, $name_field = 'name', $default = 1)
		{
			if (empty($value)) {
				return null;
			}
			
			// Check if record exists
			$row = $this->db->select('id')
				->where($field, $value)
				->get($table)
				->row();
			
			if ($row) {
				return $row->id;
			}
			
			// If not exists, create new record
			
			$slug = $this->ortyd->sanitize($value, $table);
			
			$insert_data = array(
				$field => $value,
				//$name_field => $value, // Use value as name if different field
				'description' => 'Auto-created from import: ' . $value,
				'slug' => $slug,
				'created' => date('Y-m-d H:i:s'),
				'modified' => date('Y-m-d H:i:s'),
				'createdid' => $this->session->userdata('userid') ?: 1,
				'modifiedid' => $this->session->userdata('userid') ?: 1,
				'active' => 1
			);
			
			// If field and name_field are the same, don't duplicate
			//if ($field === $name_field) {
				//unset($insert_data[$name_field]);
			//}
			
			$this->db->insert($table, $insert_data);
			return $this->db->insert_id();
		}
}	