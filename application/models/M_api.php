<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class M_api extends CI_Model {
		
		public function __construct()
		{
			parent::__construct();
			$this->load->helper(['jwt', 'authorization']); 
		}
		
		function _clean_special($string) {
		   $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
		   $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.

		   return preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
		}
		

}	