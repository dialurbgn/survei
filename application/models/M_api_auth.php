<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


class M_api_auth extends CI_Model
{
	
	public $link_pins;
	public function __construct()
	{
		parent::__construct();
		//$this->link_pins = LINK_LDAP;
		//$this->load->helper(['jwt', 'authorization']);
	}

	function checkToken(){
		$headers = $this->input->request_headers();
		$response = array('status' => 'error', 'message' => 'Token Error 1');
		
		if (isset($headers['Authorization'])) {
			//print_r($headers['Authorization']);
			$decodedToken = $this->authorization_token->validateToken($headers['Authorization']);
			
			//print_r($decodedToken);
			
			if ($decodedToken['status']) {
				$response = array('status' => 'success', 'data' => $decodedToken);
				//$this->response($response, 200);
			}else{
				$response = array('status' => 'error', 'message' => 'Auth Error', 'errors' => $decodedToken['message']);
				//$this->response($response, 401);
			}
			
		}else{
			$response = array('status' => 'error', 'message' => 'Auth Error');
			//$this->response($response, 401);
		}
		
		return $response;
	}

	function getLogin($username, $password, $json_contents)
	{

		if ($username != '' && $password != '') {
			
			$loginya = null;
			if($loginya != null){
					
					
			}else{
				
				$this->db->select('users_data.id,users_data.username,users_data.fullname,users_data.email,users_data.password,users_data.gid,users_data.banned, users_data.last_login, users_groups.name as role,users_data.app_tipe,users_data.app_version, users_data.notif_id');
				$this->db->where("lower(users_data.username) = '" . strtolower($username) . "' and users_data.active = 1 and users_data.banned = 0", null);
				$this->db->or_where("lower(users_data.email) = '" . strtolower($username) . "' and users_data.active = 1 and users_data.banned = 0", null);
				$this->db->join('users_groups', 'users_groups.id = users_data.gid');
				$query = $this->db->get('users_data');
				$query = $query->result_object();
				if ($query) {
					foreach ($query as $rows) {
						
						if ($this->ortyd->verify_hash($password, $rows->password)) {
							$datalast = array(
								'last_login' => date('Y-m-d H:i:s')
							);

							$this->db->where('id', $rows->id);
							$update = $this->db->update('users_data', $datalast);

							if ($update) {
								
								$query = $this->getProfile($rows->username);
		
								$x=0;
								if($query['status'] == 'success'){
									$data['data'] = $query['data'];
									$data['status'] = 'success';
								}else{
									$data['status'] = 'error';
									$data['errors'] = 'Password Error';
								}
								
							}
						} else {
							$data['status'] = 'error';
							$data['errors'] = 'Password Error';
						}
					}
				} else {
					$data['status'] = 'error';
					$data['errors'] = 'Username or Password Error';
				}
			
				
			}
				
			
		} else {
			$data['status'] = 'error';
			$data['errors'] = 'Field not empty';
		}

		return $data;
	}
	
	function getProfile($username, $app_tipe = null, $app_version = null)
	{
		if ($username != '') {
			
			if($app_tipe != null && $app_version != null){
				$datalast = array(
					'last_login' => date('Y-m-d H:i:s'),
					'online_date' => date('Y-m-d H:i:s')
				);

				$this->db->where('username', $username);
				$update = $this->db->update('users_data', $datalast);
			}else{
				$datalast = array(
					'last_login' => date('Y-m-d H:i:s'),
					'online_date' => date('Y-m-d H:i:s'),
					//'app_tipe' => $app_tipe,
					//'app_version' => $app_version
				);

				$this->db->where('username', $username);
				$update = $this->db->update('users_data', $datalast);
			}
							
			$this->db->select('data_gallery.path, data_gallery.url_server, users_data.id,users_data.username,users_data.fullname,users_data.email,users_data.gid,users_data.banned, users_data.last_login, users_groups.name as role,users_data.notelp,users_data.app_tipe,users_data.app_version, users_data.notif_id');
			$this->db->where("lower(users_data.username) = '" . strtolower($username) . "' and users_data.active = 1", null);
			$this->db->join('users_groups', 'users_groups.id = users_data.gid');
			$this->db->join('data_gallery', 'data_gallery.id = users_data.cover','left');
			$query = $this->db->get('users_data');
			$query = $query->result_object();
			if ($query) {
				foreach ($query as $rows) {
					//$datalast = array(
						//'last_login' => date('Y-m-d H:i:s')
					//);

					//$this->db->where('id', $rows->id);
					//$update = $this->db->update('users_data', $datalast);

					$update = 1;
					if ($update) {
						$x=0;
						foreach($query as $rows){
							$data['data']['id'] = $rows->id;
							$data['data']['username'] = $rows->username;
							$data['data']['fullname'] = $rows->fullname;
							$data['data']['email'] = $rows->email;
							$data['data']['notelp'] = $rows->notelp;
							$data['data']['group_id'] = $rows->gid;
							$data['data']['group'] = $this->ortyd->getMaster('users_groups', $rows->gid);
							$data['data']['app_tipe'] = $rows->app_tipe;
							$data['data']['app_version'] = $rows->app_version;
							$data['data']['last_login'] = $rows->last_login;
							$data['data']['notif_id'] = $rows->notif_id;
							
							$url = $rows->url_server.$rows->path;
							$handle = curl_init($url);
							curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);

							/* Get the HTML or whatever is linked in $url. */
							$response = curl_exec($handle);

							/* Check for 404 (file not found). */
							$httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
							if($httpCode != 200) {
								$data['data']['cover'] = base_url_site.'/themes/ortyd/assets/media/avatars/blank.png';
							}else{
								$data['data']['cover'] = $rows->url_server.$rows->path;
							}

							curl_close($handle);
							
							$this->db->select('data_absensi.*, date(tanggal) as datenya');
							$this->db->where('user_id', $rows->id);
							$this->db->where('type', 'Mobile');
							$this->db->order_by('date(tanggal)','DESC');
							$this->db->limit(1);
							$queryabsen = $this->db->get('data_absensi');
							$queryabsen = $queryabsen->result_object();
							if($queryabsen){
								$data['data']['last_absen'] = $queryabsen[0]->datenya;
							}else{
								$date = date('Y-m-d');
								$date = strtotime($date);
								$date = strtotime("-1 day", $date);
								$data['data']['last_absen'] = date('Y-m-d', $date);
							}
							
							
								if($app_tipe != null && $app_version != null){
									if($app_tipe == 'Android'){
										$this->db->where('name', 'Android');
									}elseif($app_tipe == 'iOS'){
										$this->db->where('name', 'Ios');
									}else{
										$this->db->where('name', 'Website');
									}
								}
								$this->db->where('active', 1);
								$queryapp = $this->db->get('master_app_version');
								$queryapp = $queryapp->result_object();
								if($queryapp){
									$y=0;
									foreach($queryapp as $rowsapp){
										$data['data']['app_latest']['id'] = $rowsapp->id;
										$data['data']['app_latest']['name'] = $rowsapp->name;
										
										if($rows->app_version != '' && $rows->app_version != null){
											if(strlen($rowsapp->version) == 3){
												$newnameapp = $rowsapp->version.'0';
											}else{
												$newnameapp = $rowsapp->version;
											}
											$versionnew = (int)str_replace('.','',$newnameapp);
											$versionnya = (int)str_replace('.','',$rows->app_version);
											if($versionnya >= $versionnew){
												$data['data']['app_latest']['version'] = $rows->app_version;
											}else{
												$data['data']['app_latest']['version'] = $rowsapp->version;
											}
										}else{
											$data['data']['app_latest']['version'] = $rowsapp->version;
										}
										$data['data']['app_latest']['file_id'] = $rowsapp->file_id;
										$data['data']['app_latest']['file'] = $this->ortyd->getMaster('data_gallery', $rowsapp->file_id);
										$y++;
									}
								}else{
									$data['data']['app_latest'] = null;
								}
								
							$x++;
						}
						$data['status'] = 'success';
					}
				}
			} else {
				$data['status'] = 'error';
				$data['errors'] = 'Username Error';
			}
		} else {
			$data['status'] = 'error';
			$data['errors'] = 'Field not empty';
		}

		return $data;
	}
	
	public function setSSO($email_sso){
			$username = "'".$email_sso."'";
				$this->db->select('users_data.id,users_data.username,users_data.fullname,users_data.email,users_data.password,users_data.gid,users_data.banned, users_data.last_login, users_groups.name as role,users_data.app_tipe,users_data.app_version, users_data.notif_id');
				$this->db->where("lower(users_data.username) = '" . strtolower($username) . "' and users_data.active = 1 and users_data.gid = 3 and users_data.banned = 0", null);
				$this->db->or_where("lower(users_data.email) = '" . strtolower($username) . "' and users_data.active = 1 and users_data.gid = 3 and users_data.banned = 0", null);
				$this->db->join('users_groups', 'users_groups.id = users_data.gid');
				$query = $this->db->get('users_data');
				$query = $query->result_object();
				if($query){
					foreach ($query as $rows) {
						if($rows->banned == 1){
							return 'banned';
						}else{
							if($rows->password != ''){
								
								
								if($rows->last_login == ''){
									
									$login = array(
										'userid'  		=> $rows->id,
										'email'     	=> $rows->email,
										'username'     	=> $rows->username,
										'fullname'     	=> $rows->fullname,
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
	
	
	public function get_by_email($email)
    {
        $this->db->where('email', $email);
        $query = $this->db->get('users_data'); // Mengambil data dari tabel users_data
        return $query->row_array(); // Mengembalikan satu baris data jika ditemukan
    }

    // Fungsi untuk menyimpan data pengguna baru
    public function insert($data)
    {
        $this->db->insert('users_data', $data); // Menyimpan data pengguna baru ke dalam tabel users_data
        return $this->db->insert_id(); // Mengembalikan ID pengguna yang baru disimpan
    }

    // Fungsi untuk memperbarui informasi pengguna berdasarkan email (misalnya login terakhir)
    public function update_user($email, $data)
    {
        $this->db->where('email', $email);
        $this->db->update('users_data', $data); // Mengupdate data pengguna berdasarkan email
        return $this->db->affected_rows(); // Mengembalikan jumlah baris yang terpengaruh
    }


}
