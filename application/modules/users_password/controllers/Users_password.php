<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users_password extends MX_Controller {

		private $urlparent = 'users_password';
		private $viewformname = 'users_password/views/v_users_password_form';
		private $tabledb = 'users_data';
		private $tableid = 'users_data.id';
		private $titlechilddb = 'Change Password';
		private $headurldb = 'users_password';
		private $actionurl = 'users_password/actionusers_password';
		private $module = 'users_password';
		private $modeldb = 'm_users_password';
		
		public function __construct()
		{
			$this->ortyd->session_check();
			$this->ortyd->access_check($this->module);
			$this->load->model($this->modeldb);
			$this->titlechilddb = $this->ortyd->getmodulename($this->module);
		}
		
		public function index()
		{
			$data['title'] = $this->titlechilddb;
			$data['module'] = $this->module;
			$data['headurl'] = '';
			$data['action'] = base_url().$this->actionurl;
			$this->template->load('main',$this->viewformname, $data);
			
		}
	
		public function actionusers_password(){
			
			$id = $this->session->userdata('userid');
			if($id != 0 && $id != null){
				
				$passwordlama = $this->input->post('passwordlama');
				$passwordkonfirmasi = $this->input->post('konfirmasi');
				$passworddata = $this->input->post('password');
				
				//$cekpasslama = $this->m_users_password->check_login($id, $passwordlama);
				
				$cekpasslama = 'success';
				if($cekpasslama != 'success'){
					$message = array(
							"status" => 'error',
							"errors" => 'Password Lama Salah'
						);
									
						echo json_encode($message);
				}else{
					if($passwordkonfirmasi != $passworddata){
						//redirect($this->headurldb.'?message=passerrornosame', 'refresh');
						//die();
						$message = array(
							"status" => 'error',
							"errors" => 'Password Lama Salah'
						);
									
						echo json_encode($message);
					}else{
							$data = array(
									'last_login' 		=> date('Y-m-d H:i:s'),
									'modifiedid'		=> $this->session->userdata('userid'),
									'modified'			=> date('Y-m-d H:i:s')
							);
							
							if($passworddata != ''){
								$datapassword = array(
									'password' => $this->ortyd->hash($this->input->post('password'))
								);
								
								$data = array_merge($data,$datapassword);
							}
							
							$this->db->where('id', $id);
							$update = $this->db->update($this->tabledb, $data);
							
							if($update){
								//redirect('dashboard?message=success', 'refresh');
								//die();
								$message = array(
									"status" => 'success',
									"errors" => '-'
								);
											
								echo json_encode($message);
							}else{
								//redirect($this->headurldb.'?message=error', 'refresh');
								//die();
								$message = array(
									"status" => 'error',
									"errors" => 'Password Lama Salah'
								);
									
								echo json_encode($message);
							}
				
					}
				}
			
				//redirect($this->headurldb.'?message=error', 'refresh');
			}else{
				$message = array(
					"status" => 'error',
					"errors" => 'Password Lama Salah'
				);
									
				echo json_encode($message);
			}
		}
		
}
