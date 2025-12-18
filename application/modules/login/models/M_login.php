<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class M_login extends CI_Model {
	
		private $urlparent = 'users_data'; //NAME TABLE 
		public $link;
		
		public function __construct()
		{
			parent::__construct();
			$this->link = api_url."api/";
		}
		
		public function store_remember_token($user_id, $token, $expiry)
		{
			// Bersihkan token lama untuk user ini
			$this->db->delete('users_remember_tokens', ['user_id' => $user_id]);

			$data = [
				'user_id'     => $user_id,
				'token'       => $token,
				'expires_at'  => date('Y-m-d H:i:s', $expiry),
				'created_at'  => date('Y-m-d H:i:s')
			];
			$this->db->insert('users_remember_tokens', $data);
		}
		
		public function get_data_byid($ID, $tabledb, $tableid){
			$this->db->select('*');
			$this->db->where($tableid, $ID);
			$query = $this->db->get($tabledb);
			$query = $query->result_object();
			if($query){
				return $query[0];
			}else{
				return null;
			}
		}
		
		function getToken($token) {
			// Kirim token sebagai parameter di URL
			$url = $this->link . 'auth/cekToken?token=' . urlencode($token);

			$ch = curl_init($url);

			curl_setopt_array($ch, [
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_TIMEOUT => 50,
				CURLOPT_SSL_VERIFYPEER => false,
				CURLOPT_CUSTOMREQUEST => 'GET',
				CURLOPT_HTTPHEADER => [
					'Accept: application/json'
				]
			]);

			$response = curl_exec($ch);
			$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);

			if ($response) {
				$data = json_decode($response);
				return $data;
			}

			return null;
		}

		
		function getLogin($username, $password) {
			$url = $this->link . 'auth/getLogin';
			
			$payload = json_encode([
				'username' => $username,
				'password' => $password
			]);

			$ch = curl_init($url);

			curl_setopt_array($ch, [
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_TIMEOUT => 50,
				CURLOPT_SSL_VERIFYPEER => false,
				CURLOPT_POST => true,
				CURLOPT_POSTFIELDS => $payload,
				CURLOPT_HTTPHEADER => [
					'Content-Type: application/json',
					'Content-Length: ' . strlen($payload)
				]
			]);

			$response = curl_exec($ch);
			$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);
			//print_r($response);
			//die();
			if ($response) {
				return $response;
			}

			return null;
		}

		
		
		public function forgot_password($email){
			$email = $this->db->escape($email);
			$this->db->select('users_data.email, users_data.id,users_data.fullname');
			$this->db->where('lower(users_data.email) = '.strtolower($email).'',null);
			$query = $this->db->get('users_data');
			$query = $query->result_object();
			if($query){
				
				$data = array(
					'active' 			=> 0,
					'modifiedid'		=> 0,
					'modified'			=> date('Y-m-d H:i:s')
				);
				
				$this->db->where('user_id',$query[0]->id);
				$insert = $this->db->update('users_token', $data);
				
				$token = $encodedlink = encrypt_short($query[0]->id.$email.date('YmdHis'));;
				
				$data = array(
					'user_id' 			=> $query[0]->id,
					'token' 			=> $token,
					'slug' 				=> $token,
					'expired'			=> date('Y-m-d H:i:s', strtotime('+1 hour')),
					'active' 			=> 1,
					'createdid'			=> 0,
					'created'			=> date('Y-m-d H:i:s'),
					'modifiedid'		=> 0,
					'modified'			=> date('Y-m-d H:i:s')
				);
									
				$insert = $this->db->insert('users_token', $data);
				if($insert){
					$name = $query[0]->fullname;
					$email = $query[0]->email;
					$data_id = $query[0]->id;
					$message = 'Dear '.$name.' <br><br>
					Anda melakukan permintaan reset password, berikut link untuk mengganti password anda : <br><br>

					<a style="color:#81BFDA" href="'.base_url().'reset/'.$token.'">'.base_url().'reset/'.$token.'</a><br><br>

					Demikian disampaikan, atas perhatian dan kerjasamanya diucapkan terima kasih<br><br>';
					
					$link = base_url().'reset/'.$token;
					$this->sendemail($name, $email, $message, $link, $data_id);
					
					return 'success';
				}else{
					return 'errors';
				}
				
				
			}else{
				return 'emailnotvalid';
			}
		}
		
		public function sendemail($name, $email, $message, $link, $data_id){
			$email = $email;
			$fullname = $name;
			$data_encode = base64_encode($link);
			$subject = 'Permintaan Reset Password pada '.title;
			$attachment = null;
			$sendinbox = $this->ortyd->setInbox(1, $data_id, 2, $subject, $message, $data_id ,0, 2);
			//$sendmail = $this->ortyd->sendEmail($email, $fullname, $subject, $message, $attachment);
			return $sendinbox;
		}
		
		
		public function check_login($username, $password){
			$this->load->helper('cookie');
			//$email_sso = $this->session->userdata('email_sso');
			if($password == 'loginbygoogle'){
				//echo $username;
				//die();
				return $this->setSSOGoogle($username);
				
			}elseif($password == 'tokenauth'){
				//echo $username;
				//die();
					$params = array(
						'token' => $username
					);

					$data = $this->curl->simple_get($this->link.'api/auth/cekToken', $params, array(
						//CURLOPT_HTTPHEADER => array('Authorization: Bearer '.$token),
						CURLOPT_TIMEOUT => 50000,
						CURLOPT_SSL_VERIFYPEER => false
					));

					$info = $this->curl->info;
					$rowcode = $info['http_code'];
					//echo 'asa'.$this->link.'api/auth/cekToken';
					//die();
					if($data) {
						$data = json_decode($data);
						
						
						// Periksa status response
						if(isset($data->status) && $data->status == 'success') {
							$user_data = $data->data->data;
							//print_r($user_data->data);
							// Dapatkan token baru dari response
							$new_token = $username;
							
							// Format data user yang diperlukan
							$user_info = array(
								'id' => $user_data->id,
								'username' => $user_data->username,
								'fullname' => $user_data->fullname,
								'email' => $user_data->email,
								'notelp' => $user_data->notelp,
								//'ppmse_id' 	=> $user_data->ppmse_id,
								'group_id' => $user_data->group_id,
								'group_name' => $user_data->group->name,
								'last_login' => $user_data->last_login,
								'cover' => $user_data->cover,
								'token' => $new_token // Simpan token baru
							);
							
							// Simpan data user ke session atau proses login
							return $this->setSSOToken($user_data->email);
						} else {
							// Handle error response
							return array(
								'status' => 'error',
								'message' => 'Login failed',
								'http_code' => $rowcode
							);
						}
					} else {
						// Handle curl error
						return array(
							'status' => 'error',
							'message' => 'No data received',
							'http_code' => $rowcode
						);
					}
				
			}else{
				
				if(sso_status == TRUE){
					//$loginya = $this->getLogin($username, $password); //AKTIFKAN SSO
					$loginya = null;
				}else{
					$loginya = null;
				}
				
				//return $loginya;
				
				if($loginya != null){
					
					if($loginya) {
						$data = json_decode($loginya);
						
						// Periksa status response
						if(isset($data->status) && $data->status == 'success') {
							$user_data = $data->data;
							
							// Dapatkan token baru dari response
							$new_token = $data->token;
							
							// Format data user yang diperlukan
							$user_info = array(
								'id' => $user_data->id,
								'username' => $user_data->username,
								'fullname' => $user_data->fullname,
								'email' => $user_data->email,
								'notelp' => $user_data->notelp,
								//'ppmse_id' 	=> $user_data->ppmse_id,
								'group_id' => $user_data->group_id,
								'group_name' => $user_data->group->name,
								'last_login' => $user_data->last_login,
								'cover' => $user_data->cover,
								'token' => $new_token // Simpan token baru
							);
							
							// Simpan data user ke session atau proses login
							return $this->setSSO($user_data->username);
						} else {
							// Handle error response
							return array(
								'status' => 'error',
								'message' => 'Login failed',
								'http_code' => $rowcode
							);
						}
					} else {
						// Handle curl error
						return array(
							'status' => 'error',
							'message' => 'No data received',
							'http_code' => $rowcode
						);
					}
				
				}else{
					
					
					$domain = $_SERVER['HTTP_HOST'];
					$parts = explode('.', $domain);
					$domain = implode('.', array_slice($parts, count($parts)-2));
										
					$username = $this->db->escape($username);
					//$username = "'".$username."'";
					$this->db->select('id,username,email,password,gid,banned, last_login ,fullname,validate,unit_id,position_name,google_email,google_id,lab_id,ppmse_id,validate_admin');
					$this->db->where('lower(username) = '.strtolower($username).' and active = 1',null);
					$this->db->or_where('lower(email) = '.strtolower($username).' and active = 1',null);
					$query = $this->db->get('users_data');
					$query = $query->result_object();
					if($query){
						foreach ($query as $rows) {
							if($rows->banned == 1){
								return 'banned';
							}elseif($rows->validate == 0){
								return 'validate';
							}elseif($rows->validate_admin == 0){
								return 'validate_admin';
							}else{
								if($this->ortyd->verify_hash($password, $rows->password)){
									
									//notif
									$this->send_inbox_notif($rows->id,100,'SSO By Google');
								
									if($rows->last_login == ''){
										
										$login = array(
											'userid'  		=> $rows->id,
											'email'     	=> $rows->email,
											'username'     	=> $rows->username,
											'fullname'     	=> $rows->fullname,
											'position_name' => $rows->position_name,
											'ppmse_id' 	=> $rows->ppmse_id,
											'position_name' => $rows->position_name,
											'group_id'     	=> $rows->gid,
											'google_email'     	=> $rows->google_email,
											'google_id'     	=> $rows->google_id,
											'lab_id'     	=> $rows->lab_id,
											'unit_id'  => $rows->unit_id,
											'upload_image_file_manager'   => true,
											'last_login'	=> date('Y-m-d H:i:s'),
											'logged_in'		=> TRUE
										);

										$this->session->set_userdata($login);
										
										
										
										$my_cookie= array(
											'name'   => 'csrf_cookie_pins_filemanager',
											'value'  => sha1('csrf_cookie_pins_filemanager'.date('Y-m-d H:i:s')),                            
											'expire' => '3000',
											'domain' => $domain
										);						   
										$this->input->set_cookie($my_cookie);
											
										return 'firstblood';
										
									}else{
										
										$data = array(
											'last_login' => date('Y-m-d H:i:s')
										);
										
										$this->db->where('id', $rows->id);
										$update = $this->db->update('users_data', $data);
										
										if($update){
											

											$login = array(
												'userid'  		=> $rows->id,
												'email'     	=> $rows->email,
												'username'     	=> $rows->username,
												'fullname'     	=> $rows->fullname,
												'position_name' => $rows->position_name,
												'ppmse_id' 	=> $rows->ppmse_id,
												'unit_id'  => $rows->unit_id,
												'group_id'     	=> $rows->gid,
												'google_email'     	=> $rows->google_email,
												'google_id'     	=> $rows->google_id,
												'lab_id'     	=> $rows->lab_id,
												'upload_image_file_manager'   => true,
												'last_login'	=> date('Y-m-d H:i:s'),
												'logged_in'		=> TRUE
											);

											$this->session->set_userdata($login);
											
											$my_cookie= array(
												'name'   => 'csrf_cookie_pins_filemanager',
												'value'  => sha1('csrf_cookie_pins_filemanager'.date('Y-m-d H:i:s')),                            
												'expire' => '3000', 
												'domain' => $domain
											);						   
											$this->input->set_cookie($my_cookie); 
																			
											if($rows->last_login == ''){
												return 'firstblood';
											}else{
												return 'success';
											}

												
										}
									
										return 'success';
										
									}
										
								}
							}
						}
					}
				
				}
				
				
				
			}
			
			
			
			return 'error';
			
		}
		
		public function setSSO($email_sso){
				//$username = "'".$email_sso."'";
				$username = $this->db->escape($email_sso);
				//$username = "'".$username."'";
				$this->db->select('id,username,email,password,gid,banned, last_login ,fullname,validate,unit_id,position_name,google_email,google_id,lab_id,ppmse_id,validate_admin');
				$this->db->where('lower(username) = '.strtolower($username).' and active = 1',null);
				$this->db->or_where('lower(email) = '.strtolower($username).' and active = 1',null);
				$query = $this->db->get('users_data');
				$query = $query->result_object();
				if($query){
					foreach ($query as $rows) {
						if($rows->banned == 1){
							return 'banned';
						}elseif($rows->validate_admin == 0){
							return 'validate_admin';
						}else{
							if($rows->password != ''){
								
								//notif
								$this->send_inbox_notif($rows->id,100,'SSO By Google');
								
								if($rows->last_login == ''){
									
									$login = array(
										'userid'  		=> $rows->id,
										'email'     	=> $rows->email,
										'username'     	=> $rows->username,
										'fullname'     	=> $rows->fullname,
										'position_name' => $rows->position_name,
										'ppmse_id' 	=> $rows->ppmse_id,
										'google_email'     	=> $rows->google_email,
										'google_id'     	=> $rows->google_id,
										'lab_id'     	=> $rows->lab_id,
										'unit_id'  => $rows->unit_id,
										'group_id'     	=> $rows->gid,
										'last_login'	=> date('Y-m-d H:i:s'),
										'logged_in'		=> TRUE
									);

									$this->session->set_userdata($login);
									
									$data = array(
										'validate' 	=> 1,
										'last_login' => date('Y-m-d H:i:s')
									);
									
									$this->db->where('id', $rows->id);
									$update = $this->db->update('users_data', $data);
										
									return 'firstblood';
									
								}else{
									
									$data = array(
										'validate' 	=> 1,
										'last_login' => date('Y-m-d H:i:s')
									);
									
									$this->db->where('id', $rows->id);
									$update = $this->db->update('users_data', $data);
									
									if($update){
										

										$login = array(
											'userid'  		=> $rows->id,
											'email'     	=> $rows->email,
											'username'     	=> $rows->username,
											'fullname'     	=> $rows->fullname,
											'position_name' => $rows->position_name,
											'ppmse_id' 	=> $rows->ppmse_id,
											'google_email'     	=> $rows->google_email,
											'google_id'     	=> $rows->google_id,
											'lab_id'     	=> $rows->lab_id,
											'unit_id'  => $rows->unit_id,
											'group_id'     	=> $rows->gid,
											'last_login'	=> date('Y-m-d H:i:s'),
											'logged_in'		=> TRUE
										);

										$this->session->set_userdata($login);
										
																		
										if($rows->last_login == ''){
											return 'firstblood';
										}else{
											return 'success';
										}

											
									}
								
									return 'success';
									
								}
									
							}
						}
					}
				}
				
			return 'error';
		}

		
		public function setSSOGoogle($email_sso){
				//$username = "'".$email_sso."'";
				$username = $this->db->escape($email_sso);
				//$username = "'".$username."'";
				$this->db->select('id,username,email,password,gid,banned, last_login ,fullname,validate,unit_id,position_name,google_email,google_id,lab_id,ppmse_id,validate_admin');
				$this->db->where('lower(google_email) = '.strtolower($username).' and active = 1',null);
				$query = $this->db->get('users_data');
				$query = $query->result_object();
				if($query){
					foreach ($query as $rows) {
						if($rows->banned == 1){
							return 'banned';
						}elseif($rows->validate_admin == 0){
							return 'validate_admin';
						}else{
							if($rows->password != ''){
								
								//notif
								$this->send_inbox_notif($rows->id,100,'SSO By Google');
								
								if($rows->last_login == ''){
									
									$login = array(
										'userid'  		=> $rows->id,
										'email'     	=> $rows->email,
										'username'     	=> $rows->username,
										'fullname'     	=> $rows->fullname,
										'ppmse_id' 	=> $rows->ppmse_id,
										'position_name' => $rows->position_name,
										'google_email'     	=> $rows->google_email,
										'google_id'     	=> $rows->google_id,
										'lab_id'     	=> $rows->lab_id,
										'unit_id'  => $rows->unit_id,
										'group_id'     	=> $rows->gid,
										'last_login'	=> date('Y-m-d H:i:s'),
										'logged_in'		=> TRUE
									);

									$this->session->set_userdata($login);
									
									$data = array(
										'validate' 	=> 1,
										'last_login' => date('Y-m-d H:i:s')
									);
									
									$this->db->where('id', $rows->id);
									$update = $this->db->update('users_data', $data);
										
									return 'firstblood';
									
								}else{
									
									$data = array(
										'validate' 	=> 1,
										'last_login' => date('Y-m-d H:i:s')
									);
									
									$this->db->where('id', $rows->id);
									$update = $this->db->update('users_data', $data);
									
									if($update){
										

										$login = array(
											'userid'  		=> $rows->id,
											'email'     	=> $rows->email,
											'username'     	=> $rows->username,
											'ppmse_id' 	=> $rows->ppmse_id,
											'fullname'     	=> $rows->fullname,
											'position_name' => $rows->position_name,
											'google_email'     	=> $rows->google_email,
											'google_id'     	=> $rows->google_id,
											'lab_id'     	=> $rows->lab_id,
											'unit_id'  => $rows->unit_id,
											'group_id'     	=> $rows->gid,
											'last_login'	=> date('Y-m-d H:i:s'),
											'logged_in'		=> TRUE
										);

										$this->session->set_userdata($login);
										
																		
										if($rows->last_login == ''){
											return 'firstblood';
										}else{
											return 'success';
										}

											
									}
								
									return 'success';
									
								}
									
							}
						}
					}
				}
				
			return 'error';
		}
		
			
		/**
		 * Tambahkan method ini ke dalam class M_login
		 */

		/**
		 * Proses login via token Telegram
		 * @param string $token Token yang diterima dari URL
		 * @return string Status login
		 */
		public function telegram_login($token)
		{
			if (empty($token)) {
				return 'invalid_token';
			}

			// Cari token di database
			$this->db->select('ut.*, ud.id, ud.username, ud.email, ud.fullname, ud.position_name, ud.gid, ud.unit_id, ud.google_email, ud.google_id, ud.banned, ud.active');
			$this->db->from('users_remember_tokens ut');
			$this->db->join('users_data ud', 'ut.user_id = ud.id');
			$this->db->where('ut.token', $token);
			//$this->db->where('ut.type', 'telegram_login');
			//$this->db->where('ut.is_used', 0);
			$this->db->where('ut.expires_at >', date('Y-m-d H:i:s'));
			$this->db->where('ud.active', 1);
			$query = $this->db->get();
			
			$result = $query->row();
			
			if (!$result) {
				return 'token_expired';
			}

			// Cek apakah user di-banned
			if ($result->banned == 1) {
				// Tandai token sebagai used meskipun user di-banned
				$this->_markTokenAsUsed($result->token);
				return 'banned';
			}

			// Tandai token sebagai sudah digunakan
			$this->_markTokenAsUsed($result->token);

			// Regenerate session untuk keamanan
			$this->session->sess_regenerate(TRUE);

			// Update last login
			$data = array(
				'last_login' => date('Y-m-d H:i:s'),
				'validate' => 1
			);
			
			$this->db->where('id', $result->user_id);
			$this->db->update('users_data', $data);

			// Set session login
			$login = array(
				'userid'        => $result->user_id,
				'email'         => $result->email,
				'username'      => $result->username,
				'fullname'      => $result->fullname,
				'position_name' => $result->position_name,
				'google_email'  => $result->google_email,
				'google_id'     => $result->google_id,
				'unit_id'       => $result->unit_id,
				'group_id'      => $result->gid,
				'upload_image_file_manager' => true,
				'last_login'    => date('Y-m-d H:i:s'),
				'logged_in'     => TRUE,
				'login_via'     => 'telegram'
			);

			$this->session->set_userdata($login);

			// Set cookie CSRF
			$domain = $_SERVER['HTTP_HOST'];
			$parts = explode('.', $domain);
			$domain = implode('.', array_slice($parts, count($parts)-2));

			$my_cookie = array(
				'name'   => 'csrf_cookie_pins_filemanager',
				'value'  => sha1('csrf_cookie_pins_filemanager'.date('Y-m-d H:i:s')),                            
				'expire' => '3000',
				'domain' => $domain
			);
			$this->input->set_cookie($my_cookie);

			// Kirim notifikasi login via Telegram
			//$this->send_telegram_login_notification($result->user_id, $result->chat_id);
			

			// Kirim notifikasi inbox
			$this->send_inbox_notif($result->user_id, 100, 'Telegram');

			return 'success';
		}

		/**
		 * Tandai token sebagai sudah digunakan
		 */
		private function _markTokenAsUsed($token)
		{
			return false;
			die();
			
			$data = array(
				//'is_used' => 1,
				//'expires_at' => date('Y-m-d H:i:s')
			);
			
			$this->db->where('token', $token);
			$this->db->update('users_remember_tokens', $data);
		}

		/**
		 * Kirim notifikasi login berhasil ke Telegram
		 */
		public function send_telegram_login_notification($user_id, $chat_id)
		{
			// Set timezone WIB
			date_default_timezone_set('Asia/Jakarta');

			// Ambil informasi user
			$user = $this->get_data_byid($user_id, 'users_data', 'id');
			if (!$user) return false;

			// Ambil waktu login sekarang dalam format friendly
			$login_time = strftime('%A, %d %b %Y, %H:%M WIB');
			$ip_address = $this->input->ip_address();
			$user_agent = $this->input->user_agent();

			// Pesan notifikasi
			$message = "🔐 <b>Login Berhasil via Telegram</b>\n\n" .
					  "👤 User: <b>{$user->fullname}</b>\n" .
					  "📧 Email: <code>{$user->email}</code>\n" .
					  "🕒 Waktu: {$login_time}\n" .
					  "🌐 IP Address: <code>{$ip_address}</code>\n\n" .
					  "⚠️ Jika ini bukan Anda, segera hubungi admin!";

			// Kirim ke Telegram (gunakan library telegram yang sudah ada)
			$this->load->library('telegram');
			$result = $this->telegram->send_message($chat_id, $message, ['parse_mode' => 'HTML']);

			return $result;
		}

		/**
		 * Cleanup token yang sudah expired
		 * Method ini bisa dipanggil secara berkala via cron job
		 */
		public function cleanup_expired_tokens()
		{
			$this->db->where('expires_at <', date('Y-m-d H:i:s'));
			$this->db->where('type', 'telegram_login');
			$deleted = $this->db->delete('users_remember_tokens');
			
			return $this->db->affected_rows();
		}

		/**
		 * Validasi token tanpa menggunakannya
		 * Berguna untuk preview atau validasi awal
		 */
		public function validate_telegram_token($token)
		{
			if (empty($token)) {
				return ['valid' => false, 'message' => 'Token kosong'];
			}

			$this->db->select('ut.*, ud.fullname, ud.email');
			$this->db->from('users_remember_tokens ut');
			$this->db->join('users_data ud', 'ut.user_id = ud.id');
			$this->db->where('ut.token', $token);
			//$this->db->where('ut.type', 'telegram_login');
			//$this->db->where('ut.is_used', 0);
			$this->db->where('ud.active', 1);
			$query = $this->db->get();
			
			$result = $query->row();
			
			if (!$result) {
				return ['valid' => false, 'message' => 'Token tidak ditemukan atau tidak valid'];
			}

			// Cek expired
			if (strtotime($result->expires_at) < time()) {
				return ['valid' => false, 'message' => 'Token sudah expired'];
			}

			// Cek banned
			if ($result->banned == 1) {
				return ['valid' => false, 'message' => 'User di-banned'];
			}

			$remaining_time = strtotime($result->expires_at) - time();
			$remaining_minutes = ceil($remaining_time / 60);

			return [
				'valid' => true, 
				'message' => 'Token valid',
				'user' => [
					'fullname' => $result->fullname,
					'email' => $result->email
				],
				'remaining_minutes' => $remaining_minutes
			];
		}
		
		public function send_inbox_notif($to_id, $type_id = 100, $type_login = 'Web Browser')
		{
			// ID pengirim sistem
			$from_id = 1;

			// Ambil waktu login sekarang (UTC → WIB)
			$login_time = format_indonesian_datetime(); // otomatis WIB

			// Ambil nama user
			$username = $this->ortyd->select2_getname($to_id, 'users_data', 'id', 'fullname') ?? 'User';

			// Atur subject dan message notifikasi login
			$subject = "Login Berhasil - {$username} melalui {$type_login}";
			$message = "Halo {$username},\nAnda berhasil login pada {$login_time} menggunakan {$type_login}.\n"
					 . "Jika ini bukan Anda segera ganti password atau hubungi admin.";

			$status   = 1; // pesan baru/unread
			$priority = 0; // prioritas normal

			// Kirim pesan ke inbox Telegram/ORTYD
			return $this->ortyd->setInbox($from_id, $to_id, $type_id, $subject, $message, $status, $priority);
		}
		
}	