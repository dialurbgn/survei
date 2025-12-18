<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Setting {

	public function getMetaValue($meta_id) {
		$CI =& get_instance();
		$CI->db->select('meta_value');
		$CI->db->where('meta_id',$meta_id);
		$CI->db->limit(1);
		$query = $CI->db->get('master_setting');
		$query = $query->result_object();
		if($query){
			return $query[0]->meta_value;
		}
		
		return null;

	}

}
