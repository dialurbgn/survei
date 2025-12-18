<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notfound_404 extends MX_Controller {

		public function __construct()
		{

		}
	
		public function index()
		{
			$this->load->view('v_notfound_404');
		}
		
}
