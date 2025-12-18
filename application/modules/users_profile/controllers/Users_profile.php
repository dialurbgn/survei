<?php
//CONTROLLER BY HANAFI GINTING

defined('BASEPATH') OR exit('No direct script access allowed');

class Users_profile extends MX_Controller {

		//CONFIG VARIABLE
		private $urlparent = 'users_profile'; //NAME TABLE 
		private $identity_id = 'slug'; //IDENTITY TABLE
		private $field = 'slug'; // IDENTITY FROM NAME FOR GET ID
		private $slug_indentity = 'fullname'; //NAME FIELD 
		private $sorting = 'modified'; // SORT FOR VIEW
		private $exclude = array('company','workplace','status_kepegawaian','position_name','position_id','perusahaan_id','data_id','notif_id','banned','last_login','owner_id','validate','online_date','app_version','is_email_all','is_test','app_tipe','signature','color','history_id','status_id','created','modified','createdid','modifiedid','id','active','slug');
		private $exclude_table = array('signature','password','color','history_id','status_id','created','modified','createdid','modifiedid','id','active','slug');
		//END CONFIG VARIABLE
		
		private $viewname;
		private $viewformname;
		private $tabledb;
		private $tableid;
		private $titlechilddb;
		private $headurldb;
		private $actionurl;
		private $module;
		private $modeldb;

		public function __construct()
		{
			
			
			$this->viewname = $this->urlparent.'/views/v_data';
			$this->viewformname = $this->urlparent.'/views/v_data_form';
			$this->tabledb = 'users_data';
			$this->tableid = 'users_data'.'.id';
			$this->titlechilddb = strtoupper($this->urlparent);
			$this->headurldb = $this->urlparent;
			$this->actionurl = $this->urlparent.'/actiondata';
			$this->module = $this->urlparent;
			$this->modeldb = 'm_data';
			
			$this->load->model($this->modeldb,'m_model_data');
			$this->load->model('dashboard/m_dashboard','m_model_master');
			$this->titlechilddb = $this->ortyd->getmodulename($this->module);
			
			$this->ortyd->session_check();
			$this->ortyd->access_check($this->module);
		}
		
		public function swicth($role_id){
			
			$this->db->select('users_data_groups.*, users_groups.name as role_name');
			$this->db->where('users_data_groups.user_id',$this->session->userdata('userid'));
			$this->db->where('users_data_groups.role_id',$role_id);
			$this->db->where('users_data_groups.active',1);
			$this->db->join('users_groups','users_data_groups.role_id = users_groups.id');
			$querycompany = $this->db->get('users_data_groups');
			$querycompany = $querycompany->result_object();
			if($querycompany){
				$this->session->set_userdata('group_id', $role_id);
				redirect('dashboard', 'refresh');							
			}else{
				redirect('dashboard?message=noaccess', 'refresh');
			}
			
		}
		
		public function view()
		{
			$ID = $this->session->userdata('userid');
			$this->ortyd->access_check_update($this->module);
			
			$logged_in = $this->session->userdata('google_id');
			if ( $logged_in != null && $logged_in != '') {
				
				$google_client = new Google_Client();
			
				$google_client->setClientId(google_id); //Define your ClientID
				
				$google_client->setClientSecret(google_secret); //Define your Client Secret Key
				
				$google_client->setRedirectUri(base_url('users_profile/google_remove')); //Define your Redirect Uri
				
				$google_client->addScope('email');
				
				$google_client->addScope('profile');
				
				$linkgoogle = $google_client->createAuthUrl();
				
				$provider = new \League\OAuth2\Client\Provider\Facebook([
					'clientId'          => facebook_id,
					'clientSecret'      => facebook_secret,
					'redirectUri'       => base_url('login/facebook'),
					'graphApiVersion'   => 'v2.10',
				]);
				
				$authUrl = $provider->getAuthorizationUrl([
					'scope' => ['email'],
				]);

			}else{
				//$aktivasi = $this->ortyd->generateAktivasi();
				//$data['generatelink'] = $aktivasi;
				
				$google_client = new Google_Client();
			
				$google_client->setClientId(google_id); //Define your ClientID
				
				$google_client->setClientSecret(google_secret); //Define your Client Secret Key
				
				$google_client->setRedirectUri(base_url('users_profile/google')); //Define your Redirect Uri
				
				$google_client->addScope('email');
				
				$google_client->addScope('profile');
				
				$linkgoogle = $google_client->createAuthUrl();
				
				$provider = new \League\OAuth2\Client\Provider\Facebook([
					'clientId'          => facebook_id,
					'clientSecret'      => facebook_secret,
					'redirectUri'       => base_url('login/facebook'),
					'graphApiVersion'   => 'v2.10',
				]);
				
				$authUrl = $provider->getAuthorizationUrl([
					'scope' => ['email'],
				]);

			
				if(isset($_GET['email'])){
					$email_sso = $_GET['email'];
				}else{
					$email_sso = $this->session->userdata('email_sso');
				}
				if($email_sso != ''){
					//$email_sso = $this->session->userdata('email_sso');
					$username = $email_sso;
					$password = $email_sso;
					$logindata = $this->m_model_data->check_login($username, $password);
					//echo $logindata;
					//die();
					if ( $logindata == 'success' || $logindata == 'validate' || $logindata == 'firstblood') {
						$userid = 3;
						$logged_in = $this->session->userdata('logged_in');
						if ( $userid != null && $logged_in == TRUE) {
							redirect('dashboard?message=success', 'refresh');
						}
					}
				}
			}
			
			$data['title'] = 'Edit '.$this->titlechilddb;
			$data['googlelink'] = $linkgoogle;
			$data['id'] = $ID;
			$data['headurl'] = $this->headurldb;
			$data['module'] = $this->module;
			$data['modeldb'] = $this->m_model_data;
			$data['linkdata'] = $this->urlparent.'/get_data_users_data_pasar';
			$data['datarow'] = $this->m_model_data->get_data_byid($data['id'], $this->tabledb, $this->tableid);
			$data['action'] = base_url().$this->urlparent.'/actioneditusers_data'.'/'.$data['id'];
			$this->template->load('main',$this->urlparent.'/views/v_data_view', $data);
		}
		
		function google()
		{

			$logged_in = $this->session->userdata('google_id');
			if ( $logged_in != null && $logged_in != '') {
				$linkgoogle = '#';
				redirect('users_profile/view?message=errorgoogle', 'refresh');
			}else{
				
				$google_client = new Google_Client();
				
				$google_client->setClientId(google_id); //Define your ClientID
				
				$google_client->setClientSecret(google_secret); //Define your Client Secret Key
				
				$google_client->setRedirectUri(base_url('users_profile/google')); //Define your Redirect Uri
				
				$google_client->addScope('email');
				
				$google_client->addScope('profile');

				if(isset($_GET["code"]))
				{
					$token = $google_client->fetchAccessTokenWithAuthCode($_GET["code"]);
			
					if(!isset($token["error"]))
					{
						$google_client->setAccessToken($token['access_token']);
						
						$this->session->set_userdata('access_token', $token['access_token']);
						
						$google_service = new Google_Service_Oauth2($google_client);
						
						$data = $google_service->userinfo->get();
						$username = $data['email'];
						$logindata = $this->m_model_data->check_login($username, 'loginbygoogle',$data);
						
						if($logindata == true){
							redirect('users_profile/view?message=successgoogle', 'refresh');
						}else{
							redirect('users_profile/view?message=errorgoogle1', 'refresh');
						}
					
					}else{
						redirect('users_profile/view?message=errorgoogle2', 'refresh');
					}
				}else{
					redirect('users_profile/view?message=errorgoogle3', 'refresh');
				}
			
			}
		}
		
		function google_remove()
		{

			$logged_in = $this->session->userdata('google_id');
			if ( $logged_in != null && $logged_in != '') {
				
				$google_client = new Google_Client();
				
				$google_client->setClientId(google_id); //Define your ClientID
				
				$google_client->setClientSecret(google_secret); //Define your Client Secret Key
				
				$google_client->setRedirectUri(base_url('users_profile/google_remove')); //Define your Redirect Uri
				
				$google_client->addScope('email');
				
				$google_client->addScope('profile');

				if(isset($_GET["code"]))
				{
					$token = $google_client->fetchAccessTokenWithAuthCode($_GET["code"]);
			
					if(!isset($token["error"]))
					{
						$google_client->setAccessToken($token['access_token']);
						
						$this->session->set_userdata('access_token', $token['access_token']);
						
						$google_service = new Google_Service_Oauth2($google_client);
						
						$data = $google_service->userinfo->get();
						$username = $data['email'];
						$logindata = $this->m_model_data->check_login_remove($username, 'loginbygoogle',$data);
						
						if($logindata == true){
							redirect('users_profile/view?message=successgoogle', 'refresh');
						}else{
							redirect('users_profile/view?message=errorgoogle', 'refresh');
						}
					
					}else{
						redirect('users_profile/view?message=errorgoogle', 'refresh');
					}
				}else{
					redirect('users_profile/view?message=errorgoogle', 'refresh');
				}
			
			
				
				//redirect('dashboard?message=success', 'refresh');
			}else{
				$linkgoogle = '#';
				redirect('users_profile/view?message=errorgoogle', 'refresh');
			}
		}
	
		public function actioneditusers_data($id)
		{
			// Cek metode POST
			if ($this->input->method() !== 'post') {
				show_error('Invalid request method', 405);
			}

			// Proteksi akses
			if ((int)$id === 0 || !is_numeric($id)) {
				echo json_encode(["message" => "invalid_id"]);
				return;
			}

			$this->ortyd->access_check_update($this->module);

			// CSRF token check (jika CSRF aktif di config)
			//if ($this->security->get_csrf_token_name() && !$this->input->post($this->security->get_csrf_token_name())) {
			   // echo json_encode(["message" => "csrf_invalid"]);
				//return;
			//}

			// Validasi dan sanitasi input
			$this->load->library('form_validation');

			$this->form_validation->set_rules('username', 'Username', 'required|trim');
			$this->form_validation->set_rules('fullname', 'Fullname', 'required|trim');
			$this->form_validation->set_rules('email', 'Email', 'required|valid_email|trim');
			$this->form_validation->set_rules('notelp', 'No Telp', 'trim');
			$this->form_validation->set_rules('active', 'Active', 'numeric');
			$this->form_validation->set_rules('banned', 'Banned', 'numeric');
			$this->form_validation->set_rules('password', 'Password', 'min_length[6]|max_length[255]');
			$this->form_validation->set_rules('cover', 'Cover', 'trim');
			$this->form_validation->set_rules('signature', 'Signature', 'trim');
			$this->form_validation->set_rules('themes_id', 'Themes', 'trim');
			$this->form_validation->set_rules('sidebar', 'Sidebar', 'trim');

			if ($this->form_validation->run() === FALSE) {
				echo json_encode(['message' => 'validation_error', 'errors' => validation_errors()]);
				return;
			}

			$user_id_ref = $this->input->post('user_id_ref') ?: null;

			$data = [
				'username'    => htmlspecialchars($this->input->post('username', TRUE), ENT_QUOTES, 'UTF-8'),
				'fullname'    => htmlspecialchars($this->input->post('fullname', TRUE), ENT_QUOTES, 'UTF-8'),
				'email'       => strtolower($this->input->post('email', TRUE)),
				'notelp'      => htmlspecialchars($this->input->post('notelp', TRUE), ENT_QUOTES, 'UTF-8'),
				'user_id_ref' => $user_id_ref,
				'active'      => (int)$this->input->post('active', TRUE),
				'banned'      => (int)$this->input->post('banned', TRUE),
				'modifiedid'  => (int)$this->session->userdata('userid'),
				'modified'    => date('Y-m-d H:i:s')
			];

			// Tambah password jika diisi
			$password = $this->input->post('password');
			if (!empty($password)) {
				$data['password'] = $this->ortyd->hash($password);
			}
			
			$themes_id = $this->input->post('themes_id', TRUE);
			if (!empty($themes_id)) {
				$data['themes_id'] = $themes_id;
			}
			
			$sidebar = $this->input->post('sidebar', TRUE);
			if (!empty($sidebar)) {
				$data['sidebar'] = $sidebar;
			}else{
				$data['sidebar'] = 0;
			}

			// Tambah cover jika diisi
			$cover = $this->input->post('cover', TRUE);
			if (!empty($cover)) {
				$data['cover'] = $cover;
			}

			// Tambah signature jika diisi
			//$signature = $this->input->post('signature', TRUE);
			//if (!empty($signature)) {
				// Signature mungkin mengandung HTML, escape khusus
				//$data['signature'] = strip_tags($signature, '<img><p><br><b><i><u>');
			//}

			// Update database
			$this->db->where('id', (int)$id);
			$update = $this->db->update($this->tabledb, $data);
			
			if($update){
				if (!empty($cover)) {
						$this->db->where('data_dokumen.table', $this->tabledb);
						$this->db->where('data_dokumen.tableid','cover');
						$this->db->where('data_dokumen.file_id',$cover);
						$queryev = $this->db->get('data_dokumen');
						$queryev = $queryev->result_object();
						if(!$queryev){
							$datadetail_ev = array(
								'table' 				=> $this->tabledb,
								'tableid' 				=> 'cover',
								'file_id' 				=> $cover,
								'data_id' 				=> $id,
								'active' 				=> 1,
								'createdid'				=> $this->session->userdata('userid'),
								'created'				=> date('Y-m-d H:i:s'),
								'modifiedid'			=> $this->session->userdata('userid'),
								'modified'				=> date('Y-m-d H:i:s')
							);
														
							$insert_ev = $this->db->insert('data_dokumen', $datadetail_ev);
						}else{
							$datadetail_ev = array(
								'table' 				=> $this->tabledb,
								'tableid' 				=> 'cover',
								'file_id' 				=> $cover,
								'data_id' 				=> $id,
								'active' 				=> 1,
								'modifiedid'			=> $this->session->userdata('userid'),
								'modified'				=> date('Y-m-d H:i:s')
							);
															
							$this->db->where('data_dokumen.id', $queryev[0]->id);
							$update = $this->db->update('data_dokumen', $datadetail_ev);
						}
				}
			}

			echo json_encode([
				'message' => $update ? 'success' : 'error',
				'id'      => $id,
				'type'    => 'update'
			]);
		}
		
		public function actionedituserssignature_data($id)
		{
			// Cek metode POST
			if ($this->input->method() !== 'post') {
				show_error('Invalid request method', 405);
			}

			// Proteksi akses
			if ((int)$id === 0 || !is_numeric($id)) {
				echo json_encode(["message" => "invalid_id"]);
				return;
			}

			$this->ortyd->access_check_update($this->module);

			// CSRF token check (jika CSRF aktif di config)
			//if ($this->security->get_csrf_token_name() && !$this->input->post($this->security->get_csrf_token_name())) {
			   // echo json_encode(["message" => "csrf_invalid"]);
				//return;
			//}

			// Validasi dan sanitasi input
			$this->load->library('form_validation');

			$this->form_validation->set_rules('username', 'Username', 'required|trim');
			$this->form_validation->set_rules('fullname', 'Fullname', 'required|trim');
			$this->form_validation->set_rules('email', 'Email', 'required|valid_email|trim');
			$this->form_validation->set_rules('notelp', 'No Telp', 'trim');
			$this->form_validation->set_rules('active', 'Active', 'numeric');
			$this->form_validation->set_rules('banned', 'Banned', 'numeric');
			$this->form_validation->set_rules('password', 'Password', 'min_length[6]|max_length[255]');
			$this->form_validation->set_rules('cover', 'Cover', 'trim');
			$this->form_validation->set_rules('signature', 'Signature', 'trim');

			if ($this->form_validation->run() === FALSE) {
				echo json_encode(['message' => 'validation_error', 'errors' => validation_errors()]);
				return;
			}

			$user_id_ref = $this->input->post('user_id_ref') ?: null;

			$data = [
				'username'    => htmlspecialchars($this->input->post('username', TRUE), ENT_QUOTES, 'UTF-8'),
				'fullname'    => htmlspecialchars($this->input->post('fullname', TRUE), ENT_QUOTES, 'UTF-8'),
				'email'       => strtolower($this->input->post('email', TRUE)),
				'notelp'      => htmlspecialchars($this->input->post('notelp', TRUE), ENT_QUOTES, 'UTF-8'),
				'user_id_ref' => $user_id_ref,
				'active'      => (int)$this->input->post('active', TRUE),
				'banned'      => (int)$this->input->post('banned', TRUE),
				'modifiedid'  => (int)$this->session->userdata('userid'),
				'modified'    => date('Y-m-d H:i:s')
			];

			// Tambah password jika diisi
			$password = $this->input->post('password');
			if (!empty($password)) {
				$data['password'] = $this->ortyd->hash($password);
			}

			// Tambah cover jika diisi
			$cover = $this->input->post('cover', TRUE);
			if (!empty($cover)) {
				$data['cover'] = $cover;
			}

			// Tambah signature jika diisi
			$signature = $this->input->post('signature', TRUE);
			if (!empty($signature)) {
				// Signature mungkin mengandung HTML, escape khusus
				$data['signature'] = strip_tags($signature, '<img><p><br><b><i><u>');
			}

			// Update database
			$this->db->where('id', (int)$id);
			$update = $this->db->update($this->tabledb, $data);
			
			if($update){
				if (!empty($cover)) {
						$this->db->where('data_dokumen.table', $this->tabledb);
						$this->db->where('data_dokumen.tableid','cover');
						$this->db->where('data_dokumen.file_id',$cover);
						$queryev = $this->db->get('data_dokumen');
						$queryev = $queryev->result_object();
						if(!$queryev){
							$datadetail_ev = array(
								'table' 				=> $this->tabledb,
								'tableid' 				=> 'cover',
								'file_id' 				=> $cover,
								'data_id' 				=> $id,
								'active' 				=> 1,
								'createdid'				=> $this->session->userdata('userid'),
								'created'				=> date('Y-m-d H:i:s'),
								'modifiedid'			=> $this->session->userdata('userid'),
								'modified'				=> date('Y-m-d H:i:s')
							);
														
							$insert_ev = $this->db->insert('data_dokumen', $datadetail_ev);
						}else{
							$datadetail_ev = array(
								'table' 				=> $this->tabledb,
								'tableid' 				=> 'cover',
								'file_id' 				=> $cover,
								'data_id' 				=> $id,
								'active' 				=> 1,
								'modifiedid'			=> $this->session->userdata('userid'),
								'modified'				=> date('Y-m-d H:i:s')
							);
															
							$this->db->where('data_dokumen.id', $queryev[0]->id);
							$update = $this->db->update('data_dokumen', $datadetail_ev);
						}
				}
			}

			echo json_encode([
				'message' => $update ? 'success' : 'error',
				'id'      => $id,
				'type'    => 'update'
			]);
		}

		public function select2() {
			
			$table = $this->input->post('table',true);
			$id = $this->input->post('id',true);
			$name = $this->input->post('name',true);
			$reference = $this->input->post('reference',true) ?? null;
			$reference_id = $this->input->post('reference_id',true) ?? null;
			$q = $this->input->post('q',true);
			
			if(!$q){
				$q = '';
			}
		
			echo $this->ortyd->select2custom($id,$name,$q,$table,$reference,$reference_id);
			
		}
		
		public function saveEvidence($data_id, $urlparent){
			return $this->m_model_master->saveEvidence($data_id, $urlparent);
		}
		
		public function proses_upload(){
			echo $this->m_model_master->proses_upload();
		}
		
		public function getcover(){
			echo $this->m_model_master->getcover('users_data');
		}
		
		public function deleteFile(){
			$this->ortyd->access_check_update($this->module);
			echo $this->m_model_master->deleteFile();
		}
		
		
		// Fungsi untuk menyimpan tema ke session
		public function set_theme($theme = 'light')
		{
			// Simpan tema ke session
			$theme = $this->input->post('theme', true) ?? 'light';
			$this->session->set_userdata('theme', $theme);
			echo json_encode(['status' => 'success', 'theme' => $theme]);
		}

		// Fungsi untuk mendapatkan tema dari session
		public function get_theme()
		{
			// Ambil tema dari session
			$theme = $this->session->userdata('theme');
			if (!$theme) {
				// Jika belum ada, default ke 'light'
				$theme = 'light';
			}
			echo json_encode(['theme' => $theme]);
		}
		
}
