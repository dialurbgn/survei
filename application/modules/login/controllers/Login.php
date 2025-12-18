<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends MX_Controller {

		//CONFIG VARIABLE
		private $urlparent = 'login'; //NAME TABLE 
		private $identity_id = 'id'; //IDENTITY TABLE
		private $field = 'id'; // IDENTITY FROM NAME FOR GET ID
		private $slug_indentity = 'username'; //NAME FIELD 
		private $sorting = 'modified'; // SORT FOR VIEW
		private $exclude = array('created','modified','createdid','modifiedid','id','active','slug');
		private $exclude_table = array('created','modified','createdid','modifiedid','id','active','slug');
		private $site_key = site_key; // change this to yours
		private $secret_key = site_secret; // change this to yours
		//END CONFIG VARIABLE
		
		private $viewname;
		private $viewformname;
		private $viewformnameregister;
		private $tabledb;
		private $tableid;
		private $titlechilddb;
		private $headurldb;
		private $actionurl;
		private $actionurl_register;
		private $module;
		private $modeldb;
		
		
		public function __construct()
		{
			$this->tabledb = 'users_data';
			$this->tableid = $this->tabledb.'.id';
			$this->headurldb = $this->urlparent;
			$this->load->model('m_login','m_model_data');
			$this->load->helper('cookie');

			$telegram_token = $this->input->get('telegram');
			$wa_token = $this->input->get('wa');

			// Jika token ada, proses login
			if ($telegram_token) {
				$this->telegram();
				return;
			}
			
			//$this->ortyd->session_check();
			
			if($wa_token){
				$token = $wa_token;
				//echo $token.'asas';
				if ($token && !$this->session->userdata('logged_in')) {
					//$hashed = hash('sha256', $token);
					$this->db->where('token', $token);
					$this->db->where('tipe_id', 2);
					$this->db->where('expires_at >=', date('Y-m-d H:i:s'));
					$record = $this->db->get('users_remember_tokens')->row();

					
					if ($record) {
						// Ambil user dari ID
						$user = $this->m_model_data->get_data_byid($record->user_id,'users_data','id');
						
						//notif
						$this->m_model_data->send_inbox_notif($user->id,100);

						// Set session seperti biasa
						$login = array(
							'userid'  		=> $user->id,
							'email'     	=> $user->email,
							'username'     	=> $user->username,
							'fullname'     	=> $user->fullname,
							'position_name' => $user->position_name,
							'group_id'     	=> $user->gid,
							'google_email'  => $user->google_email,
							'google_id'     => $user->google_id,
							'unit_id'       => $user->unit_id,
							'upload_image_file_manager' => true,
							'last_login'	=> date('Y-m-d H:i:s'),
							'logged_in'		=> TRUE
						);
						
						//print_r($login );
						//die();
						$this->session->set_userdata($login);
					}
				}
			}else{
				$token = get_cookie('remember_token');
				//echo $token.'asas';
				if ($token && !$this->session->userdata('logged_in')) {
					//$hashed = hash('sha256', $token);
					$this->db->where('token', $token);
					$this->db->where('tipe_id', 1);
					$this->db->where('expires_at >=', date('Y-m-d H:i:s'));
					$record = $this->db->get('users_remember_tokens')->row();

					
					if ($record) {
						// Ambil user dari ID
						$user = $this->m_model_data->get_data_byid($record->user_id,'users_data','id');
						
						//notif
						$this->m_model_data->send_inbox_notif($user->id,100);

						// Set session seperti biasa
						$login = array(
							'userid'  		=> $user->id,
							'email'     	=> $user->email,
							'username'     	=> $user->username,
							'fullname'     	=> $user->fullname,
							'position_name' => $user->position_name,
							'group_id'     	=> $user->gid,
							'google_email'  => $user->google_email,
							'google_id'     => $user->google_id,
							'unit_id'       => $user->unit_id,
							'upload_image_file_manager' => true,
							'last_login'	=> date('Y-m-d H:i:s'),
							'logged_in'		=> TRUE
						);
						
						//print_r($login );
						//die();
						$this->session->set_userdata($login);
					}
				}
			}

		}
		
		function telegram()
		{
			// Ambil parameter telegram dari GET
			$telegram_token = $this->input->get('telegram');

			// Jika token ada, proses login
			if ($telegram_token) {
				$logindata = $this->m_model_data->telegram_login($telegram_token);

				// Redirect berdasarkan status login
				switch ($logindata) {
					case 'banned':
						redirect('login?message=banned', 'refresh');
						break;
					case 'success':
						redirect('login?message=telegram', 'refresh');
						break;
					case 'validate':
						redirect('login?message=validate', 'refresh');
						break;
					case 'firstblood':
						redirect('users_password?message=change', 'refresh');
						break;
					default:
						redirect('login?message=errordata', 'refresh');
						break;
				}
			} else {
				// Jika tidak ada token, langsung redirect error
				redirect('login?message=errordata', 'refresh');
			}
		}
	
		
		public function refresh_csrf_token()
		{
			//if (!$this->session->userdata('logged_in')) {
				//show_error('Unauthorized', 401);
			//}

			header('X-Robots-Tag: noindex, nofollow', true);

			$this->output
				->set_content_type('application/json')
				->set_output(json_encode([
					'csrf_hash' => $this->security->get_csrf_hash()
				]));
		}
	
		function captcha_validation()
		{
			$secret_key = $this->secret_key;

			if (empty($_POST['g-recaptcha-response'])) {
				return false;
			}

			$captcha_response = $_POST['g-recaptcha-response'];

			$url = 'https://www.google.com/recaptcha/api/siteverify';

			$data = [
				'secret' => $secret_key,
				'response' => $captcha_response,
				'remoteip' => $_SERVER['REMOTE_ADDR'] // opsional tapi direkomendasikan
			];

			$ch = curl_init();

			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_TIMEOUT, 5); // timeout max 5 detik
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); // verifikasi SSL

			$response = curl_exec($ch);
			$curl_error = curl_error($ch);

			curl_close($ch);

			if ($response === false) {
				// Log error kalau perlu
				log_message('error', 'reCAPTCHA request failed: ' . $curl_error);
				return false;
			}

			$result = json_decode($response, true);

			return isset($result['success']) && $result['success'] === true;
		}
		
	
		public function index()
		{
			$userid = 3;
			$linkgoogle = null;
			$logged_in = $this->session->userdata('logged_in');
			if ( $userid != null && $logged_in == TRUE) {
				redirect('dashboard?message=success', 'refresh');
			}else{
				//$aktivasi = $this->ortyd->generateAktivasi();
				//$data['generatelink'] = $aktivasi;
				
				$google_client = new Google_Client();
			
				$google_client->setClientId(google_id); //Define your ClientID
				
				$google_client->setClientSecret(google_secret); //Define your Client Secret Key
				
				$google_client->setRedirectUri(base_url('login/google')); //Define your Redirect Uri
				
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

			}
			
			$data['title'] = 'Login';
			$data['googlelink'] = $linkgoogle;
			$data['facebooklink'] = $authUrl;
			$data['site_key'] = $this->site_key;
			$data['secret_key'] = $this->secret_key;
			$data['action'] = base_url().'login/submit';
			$this->template->load('login','login/views/v_login', $data);
		}
		
		public function term()
		{
			
			$data['title'] = 'Term & Condition '.title;
			$data['action'] = base_url().'login/submit';
			$this->template->load('login','login/views/v_term', $data);
		}
		
		public function forgetpassword()
		{
			$data['title'] = 'Forgot Password';
			$data['site_key'] = $this->site_key;
			$data['secret_key'] = $this->secret_key;
			$data['action'] = base_url().'login/submit_forgetpassword';
			$this->template->load('login','login/views/v_login_forget', $data);
		}
		
		public function resetpassword($token)
		{
			// Validasi Captcha terlebih dahulu
			// Sanitize the token to avoid any malicious inputs
			$token = trim($this->security->xss_clean($token));

			// Optional token format validation (commented)
			// if (!preg_match('/^[a-zA-Z0-9]{32}$/', $token)) {
			//     show_error('Invalid token format', 400);
			//     return;
			// }

			// Check the database for the token and validate its properties
			$this->db->select('users_token.token, users_token.user_id');
			$this->db->where('users_token.token', $token);
			$this->db->where('users_token.active', 1);
			$this->db->where('users_token.expired >=', date('Y-m-d H:i:s'));
			$query = $this->db->get('users_token');
			$query_result = $query->result_object();

			if ($query_result) {
				// Token is valid, proceed with the reset password process
				$data['title'] = 'Reset Password';
				$data['user_id'] = $query_result[0]->user_id;
				$data['site_key'] = $this->site_key;
				$data['secret_key'] = $this->secret_key;
				$data['action'] = base_url() . 'login/submit_resetpassword/' . $token;

				// Load the reset password view
				$this->template->load('login', 'login/views/v_login_reset', $data);
			} else {
				// Invalid token or expired, deactivate token and redirect to 404
				$data = array(
					'active' => 0,
					'modifiedid' => 0,
					'modified' => date('Y-m-d H:i:s')
				);

				$this->db->where('token', $token);
				$this->db->update('users_token', $data);

				redirect('404', 'refresh');
			}
		}


		
		function google()
		{
			header('X-Robots-Tag: noindex, nofollow', true);
			$google_client = new Google_Client();
			
			$google_client->setClientId(google_id); //Define your ClientID
			
			$google_client->setClientSecret(google_secret); //Define your Client Secret Key
			
			$google_client->setRedirectUri(base_url('login/google')); //Define your Redirect Uri
			
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
					$logindata = $this->m_model_data->check_login($username, 'loginbygoogle');
					
					if($logindata == 'banned'){
						redirect('login?message=banned', 'refresh');
					}elseif($logindata == 'success'){
						redirect('login?message=googlelogin', 'refresh');
					}elseif($logindata == 'validate'){
						redirect('login?message=validate', 'refresh');
					}elseif($logindata == 'validate_admin'){
						redirect('login?message=validate_admin', 'refresh');
					}elseif($logindata == 'firstblood'){
						redirect('users_password?message=change', 'refresh');
					}else{
						
						redirect('login?message=errordata', 'refresh');
						die();
						
						$datausers = array(
							'fullname'  => $data['given_name'].' '.$data['family_name'],
							'username'  => $data['email'],
							'password'  => $this->ortyd->hash($data['given_name'].' '.$data['family_name'].' '.date('YmdHis')),
							'email'  	=> $data['email'],
							'register_by_google'  => 1,
							'last_login' => date('Y-m-d H:i:s'),
							'google_id'  => $data['id'],
							'google_email'  => $data['email']
						);
						
						$datausers = array_merge($datausers,
							array('gid' 			=> 3),
							array('active' 			=> 1),
							array('banned' 			=> 0),
							array('validate' 		=> 1),
							array('validate_admin' 	=> 0),
							array('createdid'		=> 0),
							array('created'			=> date('Y-m-d H:i:s')),
							array('modifiedid'		=> 0),
							array('modified'		=> date('Y-m-d H:i:s'))
						);
						
						$string = $data['given_name'];
						$slug = $this->ortyd->sanitize($string,'users_data');
						$datausers = array_merge($datausers,
							array('slug' 	=> $slug)
						);
						
						$insert = $this->db->insert('users_data', $datausers);
						$insert_id = $this->db->insert_id();
						
						if($insert){
							$username = $data['email'];
							$logindata = $this->m_model_data->check_login($username, 'loginbygoogle');
							
							if($logindata == 'banned'){
								redirect('login?message=banned', 'refresh');
							}elseif($logindata == 'success'){
								redirect('login?message=googlelogin', 'refresh');
							}elseif($logindata == 'validate'){
								redirect('login?message=validate', 'refresh');
							}elseif($logindata == 'firstblood'){
								redirect('login?message=googlelogin', 'refresh');
							}else{
								redirect('login?message=errordata', 'refresh');
							}
						}else{
							redirect('login?message=errordata', 'refresh');
						}
					}
				
				}else{
					redirect('login?message=errordata', 'refresh');
				}
			}else{
				redirect('login?message=errordata', 'refresh');
			}
		}
		
	
		function facebook()
		{
			header('X-Robots-Tag: noindex, nofollow', true);
			$provider = new \League\OAuth2\Client\Provider\Facebook([
				'clientId'          => facebook_id,
				'clientSecret'      => facebook_secret,
				'redirectUri'       => base_url('login/facebook'),
				'graphApiVersion'   => 'v2.10',
			]);

			if(isset($_GET["code"]))
			{
				$token = $provider->getAccessToken('authorization_code', [
					'code' => $_GET['code']
				]);
				
				try {
					
					if($token)
					{
						$data = $provider->getResourceOwner($token);
						$username = $data->getEmail();
						$logindata = $this->m_model_data->check_login($username, 'loginbygoogle');
						
						if($logindata == 'banned'){
							redirect('login?message=banned', 'refresh');
						}elseif($logindata == 'success'){
							redirect('dashboard?message=success', 'refresh');
						}elseif($logindata == 'validate'){
							redirect('login?message=validate', 'refresh');
						}elseif($logindata == 'validate_admin'){
							redirect('login?message=validate_admin', 'refresh');
						}elseif($logindata == 'firstblood'){
							redirect('users_password?message=change', 'refresh');
						}else{
							
							$datausers = array(
								'fullname'  => $data->getName(),
								'username'  => $data->getEmail(),
								'password'  => $this->ortyd->hash($data->getName().' '.date('YmdHis')),
								'email'  	=> $data->getEmail(),
								'register_by_google'  => 2,
								'last_login' => date('Y-m-d H:i:s'),
								'google_id'  => $data->getId()
							);
							
							$datausers = array_merge($datausers,
								array('gid' 			=> 3),
								array('active' 			=> 1),
								array('banned' 			=> 0),
								array('validate' 		=> 1),
								array('createdid'		=> 0),
								array('created'			=> date('Y-m-d H:i:s')),
								array('modifiedid'		=> 0),
								array('modified'		=> date('Y-m-d H:i:s'))
							);
							
							$string = $data->getName();
							$slug = $this->ortyd->sanitize($string,'users_data');
							$datausers = array_merge($datausers,
								array('slug' 	=> $slug)
							);
							
							$insert = $this->db->insert('users_data', $datausers);
							$insert_id = $this->db->insert_id();
							
							if($insert){
								$username = $data->getEmail();
								$logindata = $this->m_model_data->check_login($username, 'loginbygoogle');
								
								if($logindata == 'banned'){
									redirect('login?message=banned', 'refresh');
								}elseif($logindata == 'success'){
									redirect('dashboard?message=success', 'refresh');
								}elseif($logindata == 'validate'){
									redirect('login?message=validate', 'refresh');
								}elseif($logindata == 'validate_admin'){
									redirect('login?message=validate_admin', 'refresh');
								}elseif($logindata == 'firstblood'){
									redirect('users_password?message=change', 'refresh');
								}else{
									redirect('login?message=error', 'refresh');
								}
							}else{
								redirect('login?message=errordata', 'refresh');
							}
						}
					
					}else{
						
					}
				
				} catch (Exception $e) {
					redirect('login?message=errordata', 'refresh');
				}
				
			}else{
				redirect('login?message=errordata', 'refresh');
			}
		}
		
	
		public function submit()
		{
			header('X-Robots-Tag: noindex, nofollow', true);
			$capcha = $this->captcha_validation();
			//$capcha = true;
			if($capcha == true){
				$username = trim($this->security->xss_clean($this->input->post('username')));
				$password = trim($this->security->xss_clean($this->input->post('password')));
				

				if ($this->security->xss_clean($username)){
					$username = $this->ortyd->_clean_input_data($username);
					//$username = $this->ortyd->_clean_special($username);
				}
				
				if ($this->security->xss_clean($password)){
					$password = $this->ortyd->_clean_input_data($password);
					//$password = $this->ortyd->_clean_special($password);
				}
				
				$logindata = $this->m_model_data->check_login($username, $password);
				$remember = $this->input->post('remember_me', true); // checkbox
				//print_r($logindata);
				//die();
				
				if($logindata == 'banned'){
					//redirect('login?message=banned', 'refresh');
					$message = $result = array("csrf_hash" => $this->security->get_csrf_hash(),'status' => 'error','errors' => 'Username/Email anda di blokir oleh sistem, silahkan hubungin administrator');
				}elseif($logindata === 'success' || $logindata === 'firstblood'){
					$message = $result = array("csrf_hash" => $this->security->get_csrf_hash(),'status' => 'success','message' => 'success');
					if($logindata == 'firstblood'){
						//redirect('users_password?message=change', 'refresh');
						$message = $result = array("csrf_hash" => $this->security->get_csrf_hash(),'status' => 'success','message' => 'firstblood');
					}
					//redirect('dashboard?message=success', 'refresh');
					
					
					if ($remember) {
						$token = bin2hex(random_bytes(32)); // generate 64-char token
						$user_id = $this->session->userdata('userid'); // Pastikan session diset di check_login()
						$expiry = time() + (86400 * 30); // 30 hari

						// Simpan ke database (buat fungsi di model)
						$this->m_model_data->store_remember_token($user_id, $token, $expiry);

						// Simpan cookie
						set_cookie('remember_token', $token, 86400 * 30); // 30 hari
					}

					//die();

				}elseif($logindata == 'validate'){
					//redirect('login?message=validate', 'refresh');
					$message = $result = array("csrf_hash" => $this->security->get_csrf_hash(),'status' => 'error','errors' => 'Username/Email belum di aktivasi, silahkan lakukan aktivasi terlebih dahulu');
				}elseif($logindata == 'validate_admin'){
					$message = $result = array("csrf_hash" => $this->security->get_csrf_hash(),'status' => 'error','errors' => 'Username/Email belum di aktivasi oleh admin INAMS');
				}else{
					//redirect('login?message=error', 'refresh');
					$message = $result = array("csrf_hash" => $this->security->get_csrf_hash(),'status' => 'error','errors' => 'Username atau password Salah');
				}
			}else{
				//redirect('login?message=error_capcha', 'refresh');
				$message = $result = array("csrf_hash" => $this->security->get_csrf_hash(),'status' => 'error','errors' => 'Capcha tidak sesuai');
			}
			
			echo json_encode($message);
		}
		
			
		public function submit_forgetpassword()
		{
			header('X-Robots-Tag: noindex, nofollow', true);
			$capcha = $this->captcha_validation();
			//$capcha = true;
			if($capcha == true){
				$email =  trim($this->security->xss_clean($this->input->post('email')));
				
				if ($this->security->xss_clean($email)){
					//$email = $this->ortyd->_clean_input_data($email);
					//$username = $this->ortyd->_clean_special($username);
				}
				
				$logindata = $this->m_model_data->forgot_password($email);
				//print_r($logindata);
				//die();
				
				if($logindata == 'emailnotvalid'){
					//redirect('login?message=banned', 'refresh');
					$message = $result = array("csrf_hash" => $this->security->get_csrf_hash(),'status' => 'error','errors' => 'Email anda tidak terdaftar pada sistem');
				}elseif($logindata == 'success'){
					//redirect('dashboard?message=success', 'refresh');
					$message = $result = array("csrf_hash" => $this->security->get_csrf_hash(),'status' => 'success','message' => 'Silahkan cek email anda untuk melanjutkan proses reset password');
				}elseif($logindata == 'errors'){
					//redirect('login?message=validate', 'refresh');
					$message = $result = array("csrf_hash" => $this->security->get_csrf_hash(),'status' => 'error','errors' => 'Reset Password bermasalah, silahkan coba lagi beberapa saat');
				}else{
					//redirect('login?message=error', 'refresh');
					$message = $result = array("csrf_hash" => $this->security->get_csrf_hash(),'status' => 'error','errors' => 'Reset Password bermasalah, silahkan coba lagi beberapa saat');
				}
			}else{
				//redirect('login?message=error_capcha', 'refresh');
				$message = $result = array("csrf_hash" => $this->security->get_csrf_hash(),'status' => 'error','errors' => 'Capcha tidak sesuai');
			}
			
			echo json_encode($message);
		}
		
		public function submit_resetpassword($token)
		{
			header('X-Robots-Tag: noindex, nofollow', true);
			$capcha = $this->captcha_validation();
			//$capcha = true;
			if($capcha == true){
				$passwordkonfirmasi =  trim($this->security->xss_clean($this->input->post('konfirmasi')));
				$passworddata =  trim($this->security->xss_clean($this->input->post('password')));

				if ($this->security->xss_clean($passwordkonfirmasi)){
					//$passwordkonfirmasi = $this->ortyd->_clean_input_data($passwordkonfirmasi);
					//$username = $this->ortyd->_clean_special($username);
				}
				
				if ($this->security->xss_clean($passworddata)){
					//$passworddata = $this->ortyd->_clean_input_data($passworddata);
					//$username = $this->ortyd->_clean_special($username);
				}
				
				
				$this->db->select('users_token.token, users_token.user_id');
				$this->db->where('users_token.token', $token);
				$this->db->where('users_token.active', 1);
				$this->db->where('users_token.expired >=', date('Y-m-d H:i:s'));
				$query = $this->db->get('users_token');
				$query = $query->result_object();
				if($query){
					$id = $query[0]->user_id;
				}else{
					$message = array(
						"status" => 'error',
						"errors" => 'Token expired'
					);
				}

				if($id != 0 && $id != null){
					
					
					
					//$cekpasslama = $this->m_users_password->check_login($id, $passwordlama);
					
					$cekpasslama = 'success';
					if($cekpasslama != 'success'){
						$message = array(
							"status" => 'error',
							"errors" => 'Password Lama Salah'
						);
					}else{
						if($passwordkonfirmasi != $passworddata){
							//redirect($this->headurldb.'?message=passerrornosame', 'refresh');
							//die();
							$message = array(
								"status" => 'error',
								"errors" => 'Password Tidak Sama'
							);
						}else{
								$data = array(
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
								$update = $this->db->update('users_data', $data);
								
								if($update){
									//redirect('dashboard?message=success', 'refresh');
									//die();
									
									$data = array(
										'active' 			=> 0,
										'modifiedid'		=> 0,
										'modified'			=> date('Y-m-d H:i:s')
									);
									
									$this->db->where('token',$token);
									$insert = $this->db->update('users_token', $data);
						
									$message = array(
										"status" => 'success',
										"errors" => '-'
									);
								}else{
									//redirect($this->headurldb.'?message=error', 'refresh');
									//die();
									$message = array(
										"status" => 'error',
										"errors" => 'Password Salah'
									);
								}
					
						}
					}
				
					//redirect($this->headurldb.'?message=error', 'refresh');
				}else{
					$message = array(
						"status" => 'error',
						"errors" => 'Password Salah'
					);
				}
				

			}else{
				//redirect('login?message=error_capcha', 'refresh');
				$message = $result = array("csrf_hash" => $this->security->get_csrf_hash(),'status' => 'error','errors' => 'Capcha tidak sesuai');
			}
			
			echo json_encode($message);
		}
		
		public function logout()
		{
			//$this->load->helper('cookie');
			header('X-Robots-Tag: noindex, nofollow', true);

			// Ambil domain utama (fallback jika localhost)
			$domain = $_SERVER['HTTP_HOST'];
			$parts = explode('.', $domain);
			$domain = count($parts) >= 2 ? implode('.', array_slice($parts, -2)) : '';

			// Hapus semua cookie secara umum (jika ada)
			// Tidak menggunakan foreach, hanya yang umum terpakai
			setcookie(session_name(), '', time() - 3600, '/');
			setcookie(csrf_token, '', time() - 3600, '/', $domain);
			setcookie(csrf_token, '', time() - 3600, '/'); // fallback root
			delete_cookie(csrf_token, $domain, '/');

			// Ambil domain dari base_url
			$parsed_url = parse_url(base_url());
			$domain = isset($parsed_url['host']) ? $parsed_url['host'] : '';

			// Hapus cookie remember_token
			delete_cookie('remember_token', $domain, '/');
			setcookie('remember_token', '', time() - 3600, '/', $domain);
			setcookie('remember_token', '', time() - 3600, '/'); // fallback root domain

			// Hapus semua data session
			$this->session->sess_destroy();

			// Bersihkan array $_COOKIE untuk menghindari penggunaan setelah logout
			$_COOKIE = [];

			// Redirect ke halaman login
			redirect(base_url('login'), 'refresh');
		}


		public function delete_old_chat() {
			header('X-Robots-Tag: noindex, nofollow', true);
			// Direktori tempat file chat disimpan
			$chatDirectory = FCPATH . 'logs/'; // Ganti dengan direktori yang sesuai

			// Ambil semua file dalam direktori
			$files = glob($chatDirectory . 'chatdata-*.txt');
			
			// Waktu saat ini
			$currentTime = time();

			foreach ($files as $file) {
				// Cek waktu modifikasi file
				if (file_exists($file) && $currentTime - filemtime($file) > 7 * 24 * 60 * 60) {
					// Hapus file jika sudah lebih dari 7 hari
					unlink($file);
				}
			}
			
			redirect('404','refresh');
			
		}	

		public function manifest()
		{
			header('Content-Type: application/json');
			$baseUrl = base_url();
			echo json_encode([
				"name" => title,
				"short_name" => "BK-SIMPKTN",
				"start_url" => $baseUrl,
				"display" => "standalone",
				"background_color" => "#ffffff",
				"theme_color" => "#000000",
				"orientation" => "portrait",
				"icons" => [
					[
						"src" => $baseUrl . "themes/pwa/logo-192.png",
						"sizes" => "192x192",
						"type" => "image/png"
					],
					[
						"src" => $baseUrl . "themes/pwa/logo-512.png",
						"sizes" => "512x512",
						"type" => "image/png"
					]
				]
			]);
		}
		
		public function sitemap()
		{
			header("Content-Type: application/xml; charset=utf-8");

			// URL statis
			$urls = [
				[
					'loc' => base_url(),
					'lastmod' => date('Y-m-d'),
					'priority' => '1.00'
				],
				[
					'loc' => base_url('kontak'),
					'lastmod' => '2025-05-08',
					'priority' => '0.95'
				],
				[
					'loc' => base_url('pengaduan'),
					'lastmod' => '2025-05-08',
					'priority' => '1.00'
				],
				[
					'loc' => base_url('publikasi'),
					'lastmod' => '2025-05-08',
					'priority' => '1.00'
				],
				[
					'loc' => base_url('faq'),
					'lastmod' => '2025-05-08',
					'priority' => '0.80'
				]
			];

			// Ambil data_article terbaru (limit 5)
			$this->db->select('data_article.id, data_article.slug, data_article.modified, master_article_jenis.slug as slug_jenis');
			$this->db->where('data_article.active', 1);
			$this->db->where('data_article.is_publish', 1);
			$this->db->join('master_article_jenis', 'master_article_jenis.id = data_article.jenis_id', 'left');
			$this->db->order_by('data_article.modified DESC'); // gunakan modified jika ada, bisa juga 'date'
			//$this->db->limit(5);
			$articles = $this->db->get('data_article')->result();

			foreach ($articles as $article) {
				$urls[] = [
					'loc' => base_url($article->slug_jenis.'/' . $article->slug),
					'lastmod' => date('Y-m-d', strtotime($article->modified)),
					'priority' => '0.80'
				];
			}

			// Ambil data_page terbaru (limit 5)
			$this->db->select('data_page.id, data_page.slug, data_page.modified');
			$this->db->where('data_page.active', 1);
			$this->db->where('data_page.jenis_id', 1);
			$this->db->where('data_page.is_publish', 1);
			$this->db->join('master_page_jenis', 'master_page_jenis.id = data_page.jenis_id', 'left');
			$this->db->order_by('data_page.modified DESC'); // atau 'tanggal DESC'
			//$this->db->limit(5);
			$pages = $this->db->get('data_page')->result();

			foreach ($pages as $page) {
				$urls[] = [
					'loc' => base_url($page->slug),
					'lastmod' => date('Y-m-d', strtotime($page->modified)),
					'priority' => '0.90'
				];
			}

			$data['urls'] = $urls;

			// Load view sitemap (XML)
			$this->load->view('sitemap', $data);
		}


}
