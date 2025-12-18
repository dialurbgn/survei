<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class M_data extends CI_Model {
		
		public function __construct()
		{
			parent::__construct();
		}

		    // Function to save a message to the database
		public function save_message($data) {
			$this->db->insert('chat_messages', $data);
			return $this->db->insert_id();  // Return the last inserted ID
		}

		// Function to get chat history between two users
		public function get_chat_history($user1, $user2) {
			$this->db->select('*');
			$this->db->from('chat_messages');
			$this->db->where("(from_user = '$user1' AND to_user = '$user2') OR (from_user = '$user2' AND to_user = '$user1')");
			$this->db->order_by('timestamp', 'ASC');
			return $this->db->get()->result();
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
		
		public function updatestatusread($id){
			$this->db->select('*');
			$this->db->where('id',$id);
			$this->db->where('is_read',0);
			$query = $this->db->get('data_inbox');
			$query = $query->result_object();
			if($query){
				$data = array(
						'is_read' 			=> 1,
						'modifiedid'		=> $this->session->userdata('userid'),
						'modified'			=> date('Y-m-d H:i:s')
				);
				
				$this->db->where('id', $id);
				$update = $this->db->update('data_inbox', $data);
				if($update){
					return true;
				}
			}
			
			return false;
		}
}	