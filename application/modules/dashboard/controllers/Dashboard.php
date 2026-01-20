<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MX_Controller {
	//CONFIG VARIABLE
		private $urlparent = 'dashboard';
		private $identity_id = 'slug';
		private $field = 'slug';
		private $slug_indentity = 'name';
		private $sorting = 'modified';
		private $exclude = array('color','history_id','status_id','created','modified','createdid','modifiedid','id','active','slug');
		private $exclude_table = array('color','history_id','status_id','created','modified','createdid','modifiedid','id','active','slug');
		
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
			$this->viewname = $this->urlparent.'/views/v_dashboard';
			$this->viewformname = $this->urlparent.'/views/v_data_form';
			$this->tabledb = $this->urlparent;
			$this->tableid = $this->urlparent.'.id';
			$this->titlechilddb = strtoupper($this->urlparent);
			$this->headurldb = $this->urlparent;
			$this->actionurl = $this->urlparent.'/actiondata';
			$this->module = $this->urlparent;
			$this->modeldb = 'm_dashboard';

			$this->load->model('m_dashboard_popup');
			$this->load->model($this->modeldb,'m_model_data');
			$this->titlechilddb = $this->ortyd->getmodulename($this->module);
			
			$this->ortyd->session_check();
			$this->ortyd->access_check($this->module);
			
			$gid = $this->session->userdata('group_id');
			if($gid == 3){
				redirect('survei', 'refresh');			
			}
			
		}
		
		public function index()
		{
			$logged_in = $this->session->userdata('google_id');
			if ( $logged_in != null && $logged_in != '') {
				
				$google_client = new Google_Client();
			
				$google_client->setClientId(google_id);
				$google_client->setClientSecret(google_secret);
				$google_client->setRedirectUri(base_url('users_profile/google_remove'));
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
				
				$google_client = new Google_Client();
			
				$google_client->setClientId(google_id);
				$google_client->setClientSecret(google_secret);
				$google_client->setRedirectUri(base_url('users_profile/google'));
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
					$username = $email_sso;
					$password = $email_sso;
					$logindata = $this->m_model_data->check_login($username, $password);
					if ( $logindata == 'success' || $logindata == 'validate' || $logindata == 'firstblood') {
						$userid = 3;
						$logged_in = $this->session->userdata('logged_in');
						if ( $userid != null && $logged_in == TRUE) {
							redirect('dashboard?message=success', 'refresh');
						}
					}
				}
			}
			
			$data['googlelink'] = $linkgoogle;
			$data['title'] = $this->titlechilddb;
			$data['module'] = $this->module;
			$data['tabledb'] = $this->tabledb;
			$data['identity_id'] = $this->identity_id;
			$data['exclude_table'] = $this->exclude_table;
			$data['headurl'] = $this->headurldb;
			$data['linkdata'] = $this->urlparent.'/get_data';
			$data['linkcreate'] = $this->urlparent.'/createdata';
			$this->template->load('main',$this->viewname, $data);
		}
		
		// FUNGSI UNTUK DASHBOARD SURVEI
		function getcount(){
			
			$tahun = $this->input->post('tahun',true);
			$provinsi = $this->input->post('provinsi',true);
			$kabkota = $this->input->post('kabkota',true);
			$kelompok = $this->input->post('kelompok',true);
			
			// Total Survei
			$sql = "SELECT count(data_survei_pm.id) as jumlah 
					FROM data_survei_pm 
					WHERE data_survei_pm.active = 1";
			
			if($tahun && $tahun != 'ALL'){
				$sql .= " AND EXTRACT(YEAR FROM data_survei_pm.created) = ".$this->db->escape($tahun);
			}
			
			$query = $this->db->query($sql);
			$result = $query->result_object();
			$totalsurvei = $result ? $result[0]->jumlah : 0;
			
			// Total Provinsi
			$sql = "SELECT count(DISTINCT SUBSTRING(m_set_wil_administratif.wil_prov_kode, 1, 2)) as jumlah
					FROM data_survei_pm
					INNER JOIN data_survei_pm_detail ON data_survei_pm_detail.survei_pm_pm_id = data_survei_pm.id
					INNER JOIN data_survei_pm_detail_list ON data_survei_pm_detail_list.survei_pm_detail_id = data_survei_pm_detail.id
					INNER JOIN m_set_wil_administratif ON m_set_wil_administratif.wil_id = data_survei_pm_detail_list.wil_id
					WHERE data_survei_pm.active = 1";
			
			if($tahun && $tahun != 'ALL'){
				$sql .= " AND EXTRACT(YEAR FROM data_survei_pm.created) = ".$this->db->escape($tahun);
			}
			
			$query = $this->db->query($sql);
			$result = $query->result_object();
			$totalprovinsi = $result ? $result[0]->jumlah : 0;
			
			// Total Kab/Kota
			$sql = "SELECT count(DISTINCT m_set_wil_administratif.wil_kab_kode) as jumlah
					FROM data_survei_pm
					INNER JOIN data_survei_pm_detail ON data_survei_pm_detail.survei_pm_pm_id = data_survei_pm.id
					INNER JOIN data_survei_pm_detail_list ON data_survei_pm_detail_list.survei_pm_detail_id = data_survei_pm_detail.id
					INNER JOIN m_set_wil_administratif ON m_set_wil_administratif.wil_id = data_survei_pm_detail_list.wil_id
					WHERE data_survei_pm.active = 1";
			
			if($tahun && $tahun != 'ALL'){
				$sql .= " AND EXTRACT(YEAR FROM data_survei_pm.created) = ".$this->db->escape($tahun);
			}
			
			if($provinsi && $provinsi != 'ALL'){
				$sql .= " AND SUBSTRING(m_set_wil_administratif.wil_prov_kode, 1, 2) = ".$this->db->escape($provinsi);
			}
			
			$query = $this->db->query($sql);
			$result = $query->result_object();
			$totalkabkota = $result ? $result[0]->jumlah : 0;
			
			// Total Kelompok
			$sql = "SELECT count(DISTINCT data_survei_pm_detail_list.master_kelompok_id) as jumlah
					FROM data_survei_pm
					INNER JOIN data_survei_pm_detail ON data_survei_pm_detail.survei_pm_pm_id = data_survei_pm.id
					INNER JOIN data_survei_pm_detail_list ON data_survei_pm_detail_list.survei_pm_detail_id = data_survei_pm_detail.id
					INNER JOIN m_set_wil_administratif ON m_set_wil_administratif.wil_id = data_survei_pm_detail_list.wil_id
					WHERE data_survei_pm.active = 1";
			
			if($tahun && $tahun != 'ALL'){
				$sql .= " AND EXTRACT(YEAR FROM data_survei_pm.created) = ".$this->db->escape($tahun);
			}
			
			if($provinsi && $provinsi != 'ALL'){
				$sql .= " AND SUBSTRING(m_set_wil_administratif.wil_prov_kode, 1, 2) = ".$this->db->escape($provinsi);
			}
			
			if($kabkota && $kabkota != 'ALL'){
				$sql .= " AND m_set_wil_administratif.wil_kab_kode = ".$this->db->escape($kabkota);
			}
			
			$query = $this->db->query($sql);
			$result = $query->result_object();
			$totalkelompok = $result ? $result[0]->jumlah : 0;
			
			// Total Surveyor (orang yang melakukan survei)
			$sql = "SELECT count(DISTINCT data_survei_pm.createdid) as jumlah
					FROM data_survei_pm
					INNER JOIN data_survei_pm_detail ON data_survei_pm_detail.survei_pm_pm_id = data_survei_pm.id
					INNER JOIN data_survei_pm_detail_list ON data_survei_pm_detail_list.survei_pm_detail_id = data_survei_pm_detail.id
					INNER JOIN m_set_wil_administratif ON m_set_wil_administratif.wil_id = data_survei_pm_detail_list.wil_id
					WHERE data_survei_pm.active = 1";
			
			if($tahun && $tahun != 'ALL'){
				$sql .= " AND EXTRACT(YEAR FROM data_survei_pm.created) = ".$this->db->escape($tahun);
			}
			
			if($provinsi && $provinsi != 'ALL'){
				$sql .= " AND SUBSTRING(m_set_wil_administratif.wil_prov_kode, 1, 2) = ".$this->db->escape($provinsi);
			}
			
			if($kabkota && $kabkota != 'ALL'){
				$sql .= " AND m_set_wil_administratif.wil_kab_kode = ".$this->db->escape($kabkota);
			}
			
			if($kelompok && $kelompok != 'ALL'){
				$sql .= " AND data_survei_pm_detail_list.master_kelompok_id = ".$this->db->escape($kelompok);
			}
			
			$query = $this->db->query($sql);
			$result = $query->result_object();
			$totalsurveyor = $result ? $result[0]->jumlah : 0;
			
			// Total Semua (jumlah_total dari seluruh unit)
			$sql = "SELECT COALESCE(SUM(data_survei_pm_detail_list.jumlah_total),0) as jumlah
					FROM data_survei_pm
					INNER JOIN data_survei_pm_detail 
						ON data_survei_pm_detail.survei_pm_pm_id = data_survei_pm.id
					INNER JOIN data_survei_pm_detail_list 
						ON data_survei_pm_detail_list.survei_pm_detail_id = data_survei_pm_detail.id
					INNER JOIN m_set_wil_administratif 
						ON m_set_wil_administratif.wil_id = data_survei_pm_detail_list.wil_id
					WHERE data_survei_pm.active = 1";

			if($tahun && $tahun != 'ALL'){
				$sql .= " AND EXTRACT(YEAR FROM data_survei_pm.created) = ".$this->db->escape($tahun);
			}

			if($provinsi && $provinsi != 'ALL'){
				$sql .= " AND SUBSTRING(m_set_wil_administratif.wil_prov_kode, 1, 2) = ".$this->db->escape($provinsi);
			}

			if($kabkota && $kabkota != 'ALL'){
				$sql .= " AND m_set_wil_administratif.wil_kab_kode = ".$this->db->escape($kabkota);
			}

			if($kelompok && $kelompok != 'ALL'){
				$sql .= " AND data_survei_pm_detail_list.master_kelompok_id = ".$this->db->escape($kelompok);
			}

			$query = $this->db->query($sql);
			$result = $query->result_object();
			$total_semua = $result ? $result[0]->jumlah : 0;

			
			$datanya = array(
				'total_survei' => $this->m_model_data->format_angka_singkat($totalsurvei),
				'total_provinsi' => $this->m_model_data->format_angka_singkat($totalprovinsi),
				'total_kabkota' => $this->m_model_data->format_angka_singkat($totalkabkota),
				'total_kelompok' => $this->m_model_data->format_angka_singkat($totalkelompok),
				'total_surveyor' => $this->m_model_data->format_angka_singkat($totalsurveyor),
				'total_semua' => $this->m_model_data->format_angka_singkat($total_semua)
			);
			
			$result = array("csrf_hash" => $this->security->get_csrf_hash(),"message" => "success", "data" => $datanya);
			
			echo json_encode($result);
		}
		
		// CHART FUNCTIONS UNTUK SURVEI
		public function survei_by_provinsi(){
			
			$tahun = $this->input->post('tahun',true);
			$provinsi = $this->input->post('provinsi',true);
			$kabkota = $this->input->post('kabkota',true);
			$kelompok = $this->input->post('kelompok',true);
			
			$dataisi = [];
			
			// Get top 10 provinsi berdasarkan jumlah survei
			$sql = "SELECT SUBSTRING(m_set_wil_administratif.wil_prov_kode, 1, 2) as prov_code, 
						   MAX(m_set_wil_administratif.wil_prov_nama) as wil_prov_nama, 
						   count(DISTINCT data_survei_pm.id) as total_survei,
						   SUM(data_survei_pm_detail_list.jumlah_total) as total_semua
					FROM data_survei_pm
					INNER JOIN data_survei_pm_detail ON data_survei_pm_detail.survei_pm_pm_id = data_survei_pm.id
					INNER JOIN data_survei_pm_detail_list ON data_survei_pm_detail_list.survei_pm_detail_id = data_survei_pm_detail.id
					INNER JOIN m_set_wil_administratif ON m_set_wil_administratif.wil_id = data_survei_pm_detail_list.wil_id
					WHERE data_survei_pm.active = 1 
					AND m_set_wil_administratif.wil_prov_nama IS NOT NULL
					AND m_set_wil_administratif.wil_prov_nama != ''";
			
			if($tahun && $tahun != 'ALL'){
				$sql .= " AND EXTRACT(YEAR FROM data_survei_pm.created) = ".$this->db->escape($tahun);
			}
			
			if($provinsi && $provinsi != 'ALL'){
				$sql .= " AND SUBSTRING(m_set_wil_administratif.wil_prov_kode, 1, 2) = ".$this->db->escape($provinsi);
			}
			
			$sql .= " GROUP BY SUBSTRING(m_set_wil_administratif.wil_prov_kode, 1, 2)
					  ORDER BY total_semua DESC";
			
			$query = $this->db->query($sql);
			$result = $query->result_object();
			
			if($result && count($result) > 0){
				foreach($result as $rowsdata){
					
					$dataisinya = [
						"label" => ucwords(strtolower($rowsdata->wil_prov_nama ?? 'Unknown')),
						"displayValue" => $this->ortyd->custom_number_format((float)$rowsdata->total_semua),
						"value" => (float)$rowsdata->total_semua,
						"link" => "JavaScript:drillDownProvinsi('".$rowsdata->prov_code."','".$rowsdata->wil_prov_nama."')"
					];
					
					array_push($dataisi,$dataisinya);
				}
			}else{
				$dataisinya = [
					"label" => 'Tidak Ada Data',
					"displayValue"  => 'Tidak Ada Data',
					"value"  => 0
				];
				array_push($dataisi,$dataisinya);
			}
				
			$jayParsedAry = [
			   "message" => "success", 
			   "data" => $dataisi,
			   "total" => count($result),
				"csrf_hash" => $this->security->get_csrf_hash() 
			]; 
			
			echo json_encode($jayParsedAry);
		}
		
		public function survei_by_kabkota(){
			
			$tahun = $this->input->post('tahun',true);
			$provinsi = $this->input->post('provinsi',true);
			$kabkota = $this->input->post('kabkota',true);
			$kelompok = $this->input->post('kelompok',true);
			
			$dataisi = [];
			
			// Get top 10 kab/kota berdasarkan jumlah survei
			$sql = "SELECT m_set_wil_administratif.wil_kab_kode, 
						   MAX(m_set_wil_administratif.wil_kab_nama) as wil_kab_nama, 
						   count(DISTINCT data_survei_pm.id) as total_survei,
						   SUM(data_survei_pm_detail_list.jumlah_total) as total_semua
					FROM data_survei_pm
					INNER JOIN data_survei_pm_detail ON data_survei_pm_detail.survei_pm_pm_id = data_survei_pm.id
					INNER JOIN data_survei_pm_detail_list ON data_survei_pm_detail_list.survei_pm_detail_id = data_survei_pm_detail.id
					INNER JOIN m_set_wil_administratif ON m_set_wil_administratif.wil_id = data_survei_pm_detail_list.wil_id
					WHERE data_survei_pm.active = 1
					AND m_set_wil_administratif.wil_kab_nama IS NOT NULL
					AND m_set_wil_administratif.wil_kab_nama != ''";
			
			if($tahun && $tahun != 'ALL'){
				$sql .= " AND EXTRACT(YEAR FROM data_survei_pm.created) = ".$this->db->escape($tahun);
			}
			
			if($provinsi && $provinsi != 'ALL'){
				$sql .= " AND SUBSTRING(m_set_wil_administratif.wil_prov_kode, 1, 2) = ".$this->db->escape($provinsi);
			}
			
			if($kabkota && $kabkota != 'ALL'){
				$sql .= " AND m_set_wil_administratif.wil_kab_kode = ".$this->db->escape($kabkota);
			}
			
			$sql .= " GROUP BY m_set_wil_administratif.wil_kab_kode
					  ORDER BY total_semua DESC
					  LIMIT 10";
			
			$query = $this->db->query($sql);
			$result = $query->result_object();
			
			if($result && count($result) > 0){
				foreach($result as $rowsdata){
					
					$dataisinya = [
						"label" => ucwords(strtolower($rowsdata->wil_kab_nama ?? 'Unknown')),
						"displayValue" => $this->ortyd->custom_number_format((float)$rowsdata->total_semua),
						"value" => (float)$rowsdata->total_semua,
						"link" => "JavaScript:drillDownKabkota('".$rowsdata->wil_kab_kode."','".$rowsdata->wil_kab_nama."')"
					];
					
					array_push($dataisi,$dataisinya);
				}
			}else{
				$dataisinya = [
					"label" => 'Tidak Ada Data',
					"displayValue"  => 'Tidak Ada Data',
					"value"  => 0
				];
				array_push($dataisi,$dataisinya);
			}
				
			$jayParsedAry = [
			   "message" => "success", 
			   "data" => $dataisi,
			   "total" => count($result),
				"csrf_hash" => $this->security->get_csrf_hash() 
			]; 
			
			echo json_encode($jayParsedAry);
		}
		
		public function survei_by_kelompok(){
			
			$tahun = $this->input->post('tahun',true);
			$provinsi = $this->input->post('provinsi',true);
			$kabkota = $this->input->post('kabkota',true);
			$kelompok = $this->input->post('kelompok',true);
			
			$datafunnel = array();
			
			// Get all kelompok
			$this->db->select('id, nama_kelompok');
			$this->db->where('active', 1);
			$this->db->order_by('urutan', 'ASC');
			$query_master = $this->db->get('master_kelompok');
			$master_kelompok = $query_master->result_object();
			
			$funnel_colors = array('#667eea','#764ba2','#f093fb','#f5576c','#43e97b','#38f9d7','#fa709a','#fee140','#30cfd0','#330867');
			
			$x = 0;
			foreach ($master_kelompok as $kel) {
				// Query untuk menghitung survei per kelompok
				$sql = "SELECT count(DISTINCT data_survei_pm.id) as total_survei,
						SUM(data_survei_pm_detail_list.jumlah_total) as total_semua
						FROM data_survei_pm
						INNER JOIN data_survei_pm_detail ON data_survei_pm_detail.survei_pm_pm_id = data_survei_pm.id
						INNER JOIN data_survei_pm_detail_list ON data_survei_pm_detail_list.survei_pm_detail_id = data_survei_pm_detail.id
						INNER JOIN m_set_wil_administratif ON m_set_wil_administratif.wil_id = data_survei_pm_detail_list.wil_id
						WHERE data_survei_pm.active = 1
						AND data_survei_pm_detail_list.master_kelompok_id = ".$this->db->escape($kel->id);
				
				if($tahun && $tahun != 'ALL'){
					$sql .= " AND EXTRACT(YEAR FROM data_survei_pm.created) = ".$this->db->escape($tahun);
				}
				
				if($provinsi && $provinsi != 'ALL'){
					$sql .= " AND SUBSTRING(m_set_wil_administratif.wil_prov_kode, 1, 2) = ".$this->db->escape($provinsi);
				}
				
				if($kabkota && $kabkota != 'ALL'){
					$sql .= " AND m_set_wil_administratif.wil_kab_kode = ".$this->db->escape($kabkota);
				}
				
				$query = $this->db->query($sql);
				$result = $query->row();
				
				if($result && $result->total_semua > 0) {
					$datafunnelnya = [
						"label" => $kel->nama_kelompok, 
						"displayValue" => $this->ortyd->custom_number_format((float)$result->total_semua), 
						"color" => isset($funnel_colors[$x]) ? $funnel_colors[$x] : $funnel_colors[$x % count($funnel_colors)],
						"value" => (float)$result->total_semua,
						"link" => "JavaScript:drillDownKelompok('".$kel->id."','".$kel->nama_kelompok."')"
					];
					
					array_push($datafunnel, $datafunnelnya);
					$x++;
				}
			}
			
			if(empty($datafunnel)) {
				$datafunnelnya = [
					"label" => "Tidak Ada Data", 
					"displayValue" => "Tidak Ada Data 0", 
					"color" => "#cccccc",
					"value" => 0
				];
				array_push($datafunnel, $datafunnelnya);
			}
			
			$total_keseluruhan = array_sum(array_column($datafunnel, 'value'));
			$total_formatted = $this->ortyd->custom_number_format($total_keseluruhan);
			
			$jayParsedAry = [
				 "message" => "success", 
				 "data" => $datafunnel,
				 "total" => $total_formatted,
				 "csrf_hash" => $this->security->get_csrf_hash() 
			];
			
			echo json_encode($jayParsedAry);
		}
		
		public function survei_timeline(){
			
			$tahun = $this->input->post('tahun',true);
			$provinsi = $this->input->post('provinsi',true);
			$kabkota = $this->input->post('kabkota',true);
			$kelompok = $this->input->post('kelompok',true);
			
			if(!$tahun || $tahun == 'ALL'){
				$tahun = date('Y');
			}
			
			$datalabel = [];
			$datavalue = [];
			
			// Label bulan
			$this->db->select('id, name');
			$this->db->order_by('id', 'ASC');
			$querybulan = $this->db->get('master_bulan')->result_object();

			if ($querybulan) {
				foreach ($querybulan as $rowsbulan) {
					$datalabelnya = [
						"label" => $rowsbulan->name
					];
					array_push($datalabel, $datalabelnya);
				}
			}

			// Loop bulan untuk ambil total survei
			foreach ($querybulan as $rowsbulan) {
				$sql = "SELECT COUNT(DISTINCT data_survei_pm.id) as total_survei,
						SUM(data_survei_pm_detail_list.jumlah_total) as total_semua
						FROM data_survei_pm
						INNER JOIN data_survei_pm_detail ON data_survei_pm_detail.survei_pm_pm_id = data_survei_pm.id
						INNER JOIN data_survei_pm_detail_list ON data_survei_pm_detail_list.survei_pm_detail_id = data_survei_pm_detail.id
						INNER JOIN m_set_wil_administratif ON m_set_wil_administratif.wil_id = data_survei_pm_detail_list.wil_id
						WHERE data_survei_pm.active = 1
						AND EXTRACT(YEAR FROM data_survei_pm.created) = ".$this->db->escape($tahun)."
						AND EXTRACT(MONTH FROM data_survei_pm.created) = ".$this->db->escape($rowsbulan->id);
				
				if($provinsi && $provinsi != 'ALL'){
					$sql .= " AND SUBSTRING(m_set_wil_administratif.wil_prov_kode, 1, 2) = ".$this->db->escape($provinsi);
				}
				
				if($kabkota && $kabkota != 'ALL'){
					$sql .= " AND m_set_wil_administratif.wil_kab_kode = ".$this->db->escape($kabkota);
				}
				
				if($kelompok && $kelompok != 'ALL'){
					$sql .= " AND data_survei_pm_detail_list.master_kelompok_id = ".$this->db->escape($kelompok);
				}

				$querydata = $this->db->query($sql);
				$querydata = $querydata->row();
				$values = $querydata ? (int)$querydata->total_semua : 0;

				$datalabelnya = [
					"displayValue" => $this->ortyd->custom_number_format((float)$values),
					"value" => $values
				];
				array_push($datavalue, $datalabelnya);
			}

			// Satu series saja
			$dataisi = [
				[
					"seriesname" => "Survei per Bulan",
					"color" => "#667eea",
					"data" => $datavalue,
					"link" => "JavaScript:drillDownAll()"
				]
			];

			$jayParsedAry = [
				"message" => "success",
				"data" => $datalabel,
				"data5" => $dataisi,
				"total" => array_sum(array_column($datavalue, 'value')),
				"csrf_hash" => $this->security->get_csrf_hash()
			];
			echo json_encode($jayParsedAry);
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
		
		public function getColumn(){
			echo $this->m_dashboard_popup->getColumn();
		}
		
		public function getColumnDetail(){
			echo $this->m_dashboard_popup->getColumnDetail();
		}
		
		// FUNGSI DRILL DOWN DETAIL SURVEI - UNTUK POPUP DATATABLES
		public function detail_survei_by_provinsi(){
			
			$provinsi_code = $this->input->post('provinsi_code',true);
			$tahun = $this->input->post('tahun',true);
			
			// Return success untuk trigger popup DataTables
			$jayParsedAry = [
			   "message" => "success",
			   "provinsi_code" => $provinsi_code,
			   "tahun" => $tahun,
			   "csrf_hash" => $this->security->get_csrf_hash() 
			]; 
			
			echo json_encode($jayParsedAry);
		}
		
		public function detail_survei_by_kabkota(){
			
			$kabkota_code = $this->input->post('kabkota_code',true);
			$tahun = $this->input->post('tahun',true);
			
			// Return success untuk trigger popup DataTables
			$jayParsedAry = [
			   "message" => "success",
			   "kabkota_code" => $kabkota_code,
			   "tahun" => $tahun,
			   "csrf_hash" => $this->security->get_csrf_hash() 
			]; 
			
			echo json_encode($jayParsedAry);
		}
		
		public function detail_survei_by_kelompok(){
			
			$kelompok_id = $this->input->post('kelompok_id',true);
			$tahun = $this->input->post('tahun',true);
			
			// Return success untuk trigger popup DataTables
			$jayParsedAry = [
			   "message" => "success",
			   "kelompok_id" => $kelompok_id,
			   "tahun" => $tahun,
			   "csrf_hash" => $this->security->get_csrf_hash() 
			]; 
			
			echo json_encode($jayParsedAry);
		}
		
		
		public function get_table_columns()
		{
			$table = $this->input->post('table');

			$fields = $this->db->field_data($table);
			$inputs = [];

			foreach ($fields as $field) {
				// Lewati field ID auto increment
				if ($field->primary_key == 1 && $field->type == 'int') continue;

				$inputs[] = [
					'name' => $field->name,
					'type' => $field->type,
					'max_length' => $field->max_length
				];
			}

			echo json_encode([
				'success' => true,
				'inputs' => $inputs,
				'csrf_hash' => $this->security->get_csrf_hash()
			]);
		}

		
		public function add_item_ajax()
		{
			$table = $this->input->post('table', TRUE);
			$userid = (int) $this->session->userdata('userid');

			// Whitelist nama tabel yang diizinkan
			if (!preg_match('/^master_[a-zA-Z0-9_]+$/', $table)) {
				echo json_encode([
					'success' => false,
					'message' => 'Tabel tidak diizinkan.',
					'csrf_hash' => $this->security->get_csrf_hash()
				]);
				return;
			}

			$fields = $this->db->field_data($table);
			$excluded_fields = ['color','created', 'createdid', 'modified', 'modifiedid', 'active', 'slug'];

			$data = [];
			foreach ($fields as $field) {
				if (in_array($field->name, $excluded_fields)) continue;
				if ($field->primary_key == 1 && $field->type == 'int') continue;

				// Sanitasi input (gunakan TRUE agar auto XSS filter diaktifkan)
				$data[$field->name] = $this->input->post($field->name, TRUE);
			}

			// Validasi duplikat untuk field name
			if (isset($data['name'])) {
				$this->db->where('LOWER(name)', strtolower($data['name']));
				$exists = $this->db->get($table)->row();

				if ($exists) {
					echo json_encode([
						'success' => false,
						'message' => 'Nama sudah ada.',
						'csrf_hash' => $this->security->get_csrf_hash()
					]);
					return;
				}
			}

			// Tambahan metadata
			$data['active'] = 1;
			$data['createdid'] = $userid;
			$data['created'] = date('Y-m-d H:i:s');
			$data['modifiedid'] = $userid;
			$data['modified'] = date('Y-m-d H:i:s');

			// Insert ke database
			if ($this->db->insert($table, $data)) {
				echo json_encode([
					'success' => true,
					'id' => $this->db->insert_id(),
					'name' => $data['name'] ?? '',
					'csrf_hash' => $this->security->get_csrf_hash()
				]);
			} else {
				echo json_encode([
					'success' => false,
					'message' => 'Gagal menyimpan data.',
					'csrf_hash' => $this->security->get_csrf_hash()
				]);
			}
		}

public function menu()
		{
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
			
			$data['googlelink'] = $linkgoogle;
			$data['title'] = $this->titlechilddb;
			$data['module'] = $this->module;
			$data['tabledb'] = $this->tabledb;
			$data['identity_id'] = $this->identity_id;
			$data['exclude_table'] = $this->exclude_table;
			$data['headurl'] = $this->headurldb;
			$data['linkdata'] = $this->urlparent.'/get_data';
			$data['linkcreate'] = $this->urlparent.'/createdata';
			$this->template->load('main',$this->urlparent.'/views/v_menu', $data);
		}
		
			
		function action_tipe(){
			$input = $this->input->post('input',true);
			$this->session->set_userdata('tipe_data', $input);

			$result = array("csrf_hash" => $this->security->get_csrf_hash(),"message" => "success");
			echo json_encode($result);
		}
		
		function setminiside(){
			if($this->session->userdata('hassidelarge') == 1){
				$klik = 0;
				$this->session->set_userdata('hassidelarge', 0);
			}else{
				$klik = 1;
				$this->session->set_userdata('hassidelarge', 1);
			}
			
			$result = array("csrf_hash" => $this->security->get_csrf_hash(),"message" => "success", "data" => $klik);
			echo json_encode($result);
		}
		
		function isonline(){
			
			$userid = $this->session->userdata('userid');
			$logged_in = $this->session->userdata('logged_in');
			if ( !$userid && $logged_in != TRUE) {
				$result = array("message" => "notlogin");
				echo json_encode($result);
			}else{
				$data = array(
					'online_date' => date('Y-m-d H:i:s')
				);
									
				$this->db->where('id', $this->session->userdata('userid'));
				$update = $this->db->update('users_data', $data);
				if($update){
					$result = array("message" => "success", "data" => $update);
					echo json_encode($result);
				}else{
					$result = array("message" => "error");
					echo json_encode($result);
				}
			}
		}
		
		function uploadBase64_new()
		{
			echo $this->m_dashboard->uploadBase64_new();
		}
		
		public function getnamingfield(){
			$meta_table = $this->input->post('table',true);
			$q = $this->input->post('q',true);
			$exclude = array();
			$columnya =  $this->ortyd->query_column($meta_table, $exclude, null, null, $q);
			if($columnya){
				$i=0;
				foreach ($columnya as $rows){
					$data[$i]['id'] = $rows['id'];
					$data[$i]['name']= $this->ortyd->translate_column($meta_table,$rows['id']);
					$i++;
				}
				$data = array('csrf_hash' =>$this->security->get_csrf_hash(),'items' => $data);
			}else{
				$data = array('csrf_hash' =>$this->security->get_csrf_hash(),'items' => array());
			}
			
			echo json_encode($data);
			
		}
		
		public function getnamingfieldcontrol(){
			$meta_table = $this->input->post('table',true);
			$q = $this->input->post('q',true);
			$exclude = $this->input->post('exclude',true);
			if($exclude != null && $exclude != ''){
				$exclude = json_decode($exclude);
			}else{
				$exclude = null;
			}
			
			if(!$q){
				$q = '';
			}
		
			//$exclude = array();
			
			$query_column = $this->ortyd->getviewlistcontrol($meta_table, $meta_table, $exclude, $q);
			if($query_column){
				$searchnyaid = array();
				$searchnya = array();
				$alias = 0;
				foreach($query_column as $rowsdata){
					array_push($searchnyaid,$this->ortyd->translate_column($meta_table,$rowsdata['name']));
					
					$table_references = null;
					$table_references = $this->ortyd->get_table_reference($meta_table,$rowsdata['name']);
					
					if($table_references != null){
						array_push($searchnya,$table_references[0].'_'.$alias.'.'.$table_references[2]);
					}else{
						array_push($searchnya,$meta_table.'.'."`".$rowsdata['name']."`");
					}
					
					$alias++;
				}
				$column_search = $searchnya;
			}else{
				$column_search = array(null);
			}
			
			if ($column_search != null) {
				$i = 0;
				//$q = $this->input->post('q', true); // Ambil keyword pencarian dari POST

				$data = [];
				
				foreach ($column_search as $rows) {
					$name = $searchnyaid[$i];
					// Tentukan id dan name untuk setiap kolom pencarian
					if ($q !== '') {
						// Pencarian case-insensitive dengan stripos
						if (stripos($name ?? '', $q) !== false) {
							$data[$i]['id'] = $rows;
							$data[$i]['name'] = $searchnyaid[$i];
						}
						
					} else {
						$data[$i]['id'] = $rows;
						$data[$i]['name'] = $searchnyaid[$i];
						// Jika tidak ada pencarian, masukkan semua data
					
					}
					
					$i++;
				}
				
				// Kembalikan hasil dengan csrf_hash dan data pencarian
				$data = array(
					'csrf_hash' => $this->security->get_csrf_hash(),
					'items' => $data
				);
			} else {
				// Jika $column_search kosong, kembalikan data kosong
				$data = array(
					'csrf_hash' => $this->security->get_csrf_hash(),
					'items' => array()
				);
			}
			
			echo json_encode($data);
			
		}
		
		public function getnaming(){
			

			$meta_table = $this->input->post('table_change',true);
			$meta_id = $this->input->post('table_change_id',true);
			$meta_value = $this->input->post('value',true);
			$exclude = array();
			
			$tablenya =  $this->ortyd->query_table_list();
			$columnya =  $this->ortyd->query_column($meta_table, $exclude);
			$datacolumn = [];
			if($columnya){
				foreach($columnya as $rowsc){
					array_push($datacolumn, array(
							"id" => $rowsc['id'],
							"name" => $this->ortyd->translate_column($meta_table,$rowsc['id'])
						)
					);
				}
			}
			
			$required = 0;
			$is_nullable = $this->m_model_data->is_column_nullable($meta_table, $meta_id);

			if ($is_nullable === true) {
				
			} elseif ($is_nullable === false) {
				$required = 1;
			} else {
				
			}

			$this->db->where('meta_id',$meta_id);
			$this->db->where('meta_table',$meta_table);
			$query = $this->db->get('translate');
			$query = $query->result_object();
			if(!$query){
				
				$datacmcode = array(
					'meta_size' 	=> 12,
					'meta_tipe' 	=> 'TEXT',
					'meta_required' => $required,
					'meta_value' 	=> $meta_value,
					'meta_table' 	=> $meta_table,
					'meta_table_ref' 		=> '',
					'meta_table_id_ref_value' 		=> '',
					'meta_table_name_ref_value' 	=> '',
					'meta_table_id_ref' 	=> '',
					'meta_table_name_ref' 	=> '',
					'meta_id' 		=> $meta_id,
					'meta_only_name' 	=> 1,
					'meta_nested' 	=> 0,
					'meta_nested_field_id' 	=> '',
					'meta_nested_field_name' 	=> '',
					'meta_nested_ref_id' 	=> '',
					'meta_nested_ref_id_value' 	=> '',
					'meta_table_list' => $tablenya,
					'meta_column' => $datacolumn
				);

				if($datacmcode){
					$result = array("csrf_hash" => $this->security->get_csrf_hash(),"status" => "success", "data" => $datacmcode);
					echo json_encode($result);
				}else{
					$result = array("csrf_hash" => $this->security->get_csrf_hash(),"status" => "error");
					echo json_encode($result);
				}
			
			}else{
				
				if($query[0]->meta_nested == null || $query[0]->meta_nested == '' || $query[0]->meta_nested == 'null'){
					$query[0]->meta_nested = 0;
				}
				
				if($query[0]->meta_nested_field_id == null || $query[0]->meta_nested_field_id == '' || $query[0]->meta_nested_field_id == 'null'){
					$meta_nested_field_name = '';
				}else{
					$meta_nested_field_name = $this->ortyd->translate_column($meta_table,$query[0]->meta_nested_field_id);
				}
				
				if($required == 1){
					
				}else{
					$required = (int)$query[0]->meta_required;
				}
				
				$datacmcode = array(
					'meta_size' 	=> $query[0]->meta_size,
					'meta_tipe' 	=> $query[0]->meta_tipe,
					'meta_required' 	=> $required,
					'meta_value' 	=> $query[0]->meta_value,
					'meta_table' 	=> $query[0]->meta_table,
					'meta_table_ref' 		=> $query[0]->meta_table_ref,
					'meta_table_id_ref' 	=> $query[0]->meta_table_id_ref,
					'meta_table_name_ref' 	=> $query[0]->meta_table_name_ref,
					'meta_table_name_ref_value' 		=> $this->ortyd->translate_column($query[0]->meta_table_ref,$query[0]->meta_table_name_ref),
					'meta_table_id_ref_value' 	=> $this->ortyd->translate_column($query[0]->meta_table_ref,$query[0]->meta_table_id_ref),
					'meta_id' 		=> $query[0]->meta_id,
					'meta_only_name' 	=>  $query[0]->meta_only_name,
					'meta_nested' 	=> $query[0]->meta_nested,
					'meta_nested_field_id' 	=> $query[0]->meta_nested_field_id,
					'meta_nested_field_name' 	=> $meta_nested_field_name,
					'meta_nested_ref_id' 	=> $query[0]->meta_nested_ref_id,
					'meta_nested_ref_id_value' 	=> $this->ortyd->translate_column($query[0]->meta_table_ref,$query[0]->meta_nested_ref_id),
					'meta_table_list' => $tablenya,
					'meta_column' => $datacolumn
				);

				if($datacmcode){
					$result = array("csrf_hash" => $this->security->get_csrf_hash(), "status" => "success", "data" => $datacmcode);
					echo json_encode($result);
				}else{
					$result = array("csrf_hash" => $this->security->get_csrf_hash(), "status" => "error");
					echo json_encode($result);
				}
				
			}
				
		}
		
		public function updatenaming(){
			
			$only = $this->input->post('only',true) ?? 1;
			$required_data = $this->input->post('required',true) ?? 0;
			$size = $this->input->post('size',true);
			$meta_value = $this->input->post('value',true);
			$meta_tipe = $this->input->post('tipe',true);
			$meta_table = $this->input->post('table_change',true);
			$meta_id = $this->input->post('table_change_id',true);
			$meta_table_ref = $this->input->post('table_ref',true) ?? null;
			$meta_table_id_ref = $this->input->post('table_id_ref',true) ?? null;
			$meta_table_name_ref = $this->input->post('table_name_ref',true) ?? null;
			$meta_nested = $this->input->post('nested',true) ?? 0;
			$meta_nested_field_id = $this->input->post('nested_field_id',true) ?? null;
			$meta_nested_ref_id = $this->input->post('nested_ref_id',true) ?? null;
			
			$this->db->where('meta_id',$meta_id);
			$this->db->where('meta_table',$meta_table);
			$query = $this->db->get('translate');
			$query = $query->result_object();
			if(!$query){
				
				$datacmcode = array(
					'meta_size' 	=> $size,
					'meta_required' => $required_data,
					'meta_tipe' 	=> $meta_tipe,
					'meta_value' 	=> $meta_value,
					'meta_table' 	=> $meta_table,
					'meta_table_ref' 		=> $meta_table_ref,
					'meta_table_id_ref' 	=> $meta_table_id_ref,
					'meta_table_name_ref' 	=> $meta_table_name_ref,
					'meta_nested' 			=> $meta_nested,
					'meta_nested_field_id' 	=> $meta_nested_field_id,
					'meta_nested_ref_id' 	=> $meta_nested_ref_id,
					'meta_only_name' 	=> $only,
					'meta_id' 		=> $meta_id,
					'created' 		=> date('Y-m-d H:i:s'),
					'createdid' 	=> $this->session->userdata('userid'),
					'modified' 		=> date('Y-m-d H:i:s'),
					'modifiedid' 	=> $this->session->userdata('userid'),
					'active' 		=> 1
				);
									
				$updatecmcode = $this->db->insert('translate', $datacmcode);
				if($updatecmcode){
					$required = $this->m_model_data->updateRequiredField($meta_table, $meta_id, $required_data);
					$result = array("csrf_hash" => $this->security->get_csrf_hash(),"status" => "success");
					echo json_encode($result);
				}else{
					$result = array("csrf_hash" => $this->security->get_csrf_hash(),"status" => "error");
					echo json_encode($result);
				}
			
			}else{
				
				if($meta_tipe == 'SELECT'){
					
					if($only == '1'){
						$datacmcode = array(
							'meta_size' 	=> $size,
							'meta_required' => $required_data,
							'meta_value' 	=> $meta_value,
							'meta_table' 	=> $meta_table,
							'meta_id' 		=> $meta_id,
							'meta_only_name' 	=> $only,
							'modified' 		=> date('Y-m-d H:i:s'),
							'modifiedid' 	=> $this->session->userdata('userid'),
							'active' 		=> 1
						);
					}else{
						
						if($meta_nested == '1'){
							$datacmcode = array(
								'meta_size' 	=> $size,
								'meta_required' => $required_data,
								'meta_tipe' 	=> $meta_tipe,
								'meta_value' 	=> $meta_value,
								'meta_table' 	=> $meta_table,
								'meta_table_ref' 		=> $meta_table_ref,
								'meta_table_id_ref' 	=> $meta_table_id_ref,
								'meta_table_name_ref' 	=> $meta_table_name_ref,
								'meta_nested' 			=> $meta_nested,
								'meta_nested_field_id' 	=> $meta_nested_field_id,
								'meta_nested_ref_id' 	=> $meta_nested_ref_id,
								'meta_only_name' 	=> $only,
								'meta_id' 		=> $meta_id,
								'modified' 		=> date('Y-m-d H:i:s'),
								'modifiedid' 	=> $this->session->userdata('userid'),
								'active' 		=> 1
							);
						}else{
							$datacmcode = array(
								'meta_size' 	=> $size,
								'meta_required' => $required_data,
								'meta_tipe' 	=> $meta_tipe,
								'meta_value' 	=> $meta_value,
								'meta_table' 	=> $meta_table,
								'meta_table_ref' 		=> $meta_table_ref,
								'meta_table_id_ref' 	=> $meta_table_id_ref,
								'meta_table_name_ref' 	=> $meta_table_name_ref,
								'meta_nested' 			=> $meta_nested,
								'meta_only_name' 	=> $only,
								'meta_id' 		=> $meta_id,
								'modified' 		=> date('Y-m-d H:i:s'),
								'modifiedid' 	=> $this->session->userdata('userid'),
								'active' 		=> 1
							);
						}
						
					}
					
				}else{
					
					if($only == '1'){
						$datacmcode = array(
							'meta_size' 	=> $size,
							'meta_required' => $required_data,
							'meta_value' 	=> $meta_value,
							'meta_table' 	=> $meta_table,
							'meta_id' 		=> $meta_id,
							'meta_only_name' 	=> $only,
							'modified' 		=> date('Y-m-d H:i:s'),
							'modifiedid' 	=> $this->session->userdata('userid'),
							'active' 		=> 1
						);
					}else{
						$datacmcode = array(
							'meta_size' 	=> $size,
							'meta_required' => $required_data,
							'meta_tipe' 	=> $meta_tipe,
							'meta_value' 	=> $meta_value,
							'meta_table' 	=> $meta_table,
							'meta_id' 		=> $meta_id,
							'meta_only_name' 	=> $only,
							'modified' 		=> date('Y-m-d H:i:s'),
							'modifiedid' 	=> $this->session->userdata('userid'),
							'active' 		=> 1
						);
					}
					
					
				}
				

				$this->db->where('id',$query[0]->id);				
				$updatecmcode = $this->db->update('translate', $datacmcode);
				if($updatecmcode){
					$required = $this->m_model_data->updateRequiredField($meta_table, $meta_id, $required_data);
					$result = array("csrf_hash" => $this->security->get_csrf_hash(),"status" => "success");
					echo json_encode($result);
				}else{
					$result = array("csrf_hash" => $this->security->get_csrf_hash(),"status" => "error");
					echo json_encode($result);
				}
			}
				
		}
		
		
		public function updateview(){
			//die();
			
			$modulview = $this->input->post('modulview',true);
			$tabelview = $this->input->post('tabelview',true);
			$dataview = json_encode($this->input->post('dataview'));
			
			$this->db->where('module',$modulview);
			$this->db->where('table',$tabelview);
			$query = $this->db->get('translate_view');
			$query = $query->result_object();
			if(!$query){
				$datacmcode = array(
					'module' 		=> $modulview,
					'table' 		=> $tabelview,
					'data' 			=> $dataview,
					'created' 		=> date('Y-m-d H:i:s'),
					'createdid' 	=> $this->session->userdata('userid'),
					'modified' 		=> date('Y-m-d H:i:s'),
					'modifiedid' 	=> $this->session->userdata('userid'),
					'active' 		=> 1
				);
									
				$updatecmcode = $this->db->insert('translate_view', $datacmcode);
				if($updatecmcode){
					
					$this->db->where('module',$modulview);
					$this->db->where('table',$tabelview);
					$this->db->where('user_id',$this->session->userdata('userid'));
					$query = $this->db->get('translate_view_user');
					$query = $query->result_object();
					if($query){
						$datacmcode = array(
							'module' 		=> $modulview,
							'table' 		=> $tabelview,
							'data' 			=> $dataview,
							'user_id' 		=> $this->session->userdata('userid'),
							'modified' 		=> date('Y-m-d H:i:s'),
							'modifiedid' 	=> $this->session->userdata('userid')
						);
						
						$this->db->where('id',$query[0]->id);						
						$updatecmcode = $this->db->update('translate_view_user', $datacmcode);
					}else{
						$datacmcode = array(
							'module' 		=> $modulview,
							'table' 		=> $tabelview,
							'data' 			=> $dataview,
							'user_id' 		=> $this->session->userdata('userid'),
							'created' 		=> date('Y-m-d H:i:s'),
							'createdid' 	=> $this->session->userdata('userid'),
							'modified' 		=> date('Y-m-d H:i:s'),
							'modifiedid' 	=> $this->session->userdata('userid'),
							'active' 		=> 1
						);
						
						$updatecmcode = $this->db->insert('translate_view_user', $datacmcode);
					}
			
					$result = array("csrf_hash" => $this->security->get_csrf_hash(),"status" => "success");
					echo json_encode($result);
				}else{
					$result = array("csrf_hash" => $this->security->get_csrf_hash(),"status" => "error");
					echo json_encode($result);
				}
			
			}else{
				
				$datacmcode = array(
					'module' 		=> $modulview,
					'table' 		=> $tabelview,
					'data' 			=> $dataview,
					'modified' 		=> date('Y-m-d H:i:s'),
					'modifiedid' 	=> $this->session->userdata('userid'),
					'active' 		=> 1
				);
				

				$this->db->where('id',$query[0]->id);				
				$updatecmcode = $this->db->update('translate_view', $datacmcode);
				if($updatecmcode){
					
					$this->db->where('module',$modulview);
					$this->db->where('table',$tabelview);
					$this->db->where('user_id',$this->session->userdata('userid'));
					$query = $this->db->get('translate_view_user');
					$query = $query->result_object();
					if($query){
						$datacmcode = array(
							'module' 		=> $modulview,
							'table' 		=> $tabelview,
							'data' 			=> $dataview,
							'user_id' 		=> $this->session->userdata('userid'),
							'modified' 		=> date('Y-m-d H:i:s'),
							'modifiedid' 	=> $this->session->userdata('userid')
						);
						
						$this->db->where('id',$query[0]->id);							
						$updatecmcode = $this->db->update('translate_view_user', $datacmcode);
					}else{
						$datacmcode = array(
							'module' 		=> $modulview,
							'table' 		=> $tabelview,
							'data' 			=> $dataview,
							'user_id' 		=> $this->session->userdata('userid'),
							'created' 		=> date('Y-m-d H:i:s'),
							'createdid' 	=> $this->session->userdata('userid'),
							'modified' 		=> date('Y-m-d H:i:s'),
							'modifiedid' 	=> $this->session->userdata('userid'),
							'active' 		=> 1
						);

						$updatecmcode = $this->db->insert('translate_view_user', $datacmcode);
					}
					
					$result = array("csrf_hash" => $this->security->get_csrf_hash(),"status" => "success");
					echo json_encode($result);
				}else{
					$result = array("csrf_hash" => $this->security->get_csrf_hash(),"status" => "error");
					echo json_encode($result);
				}
			}
		}
		
		
		public function saveAbsen(){
			//die();
			
			$user_id = $this->input->post('user_id',true);

			$this->db->where('user_id',$user_id);
			$this->db->where('type', 'Website');
			$this->db->where('tanggal',date('Y-m-d'));
			$query = $this->db->get('data_absensi');
			$query = $query->result_object();
			if(!$query){
				$datacmcode = array(
					'user_id' 			=> $user_id,
					'latitude' 			=> null,
					'longitude' 		=> null,
					'type' 				=> 'Website',
					'tanggal' 			=> date('Y-m-d'),
					'slug' 				=> $user_id.date('YmdHis').rand(1000,9999),
					'active'			=> 1,
					'createdid'			=> $user_id,
					'modifiedid'		=> $user_id,
					'created'			=> date('Y-m-d H:i:s'),
					'modified'			=> date('Y-m-d H:i:s')
				);
									
				$updatecmcode = $this->db->insert('data_absensi', $datacmcode);
				if($updatecmcode){
					$result = array("csrf_hash" => $this->security->get_csrf_hash(),"status" => "success");
					echo json_encode($result);
				}else{
					$result = array("csrf_hash" => $this->security->get_csrf_hash(),"status" => "error");
					echo json_encode($result);
				}
			
			}else{
				
				$result = array("csrf_hash" => $this->security->get_csrf_hash(),"status" => "error");
				echo json_encode($result);
				
			}
		}
		
		public function updatevieworder(){
			//die();
			
			$modulview = $this->input->post('modulview',true);
			$tabelview = $this->input->post('tabelview',true);
			$tableorder = $this->input->post('tableorder',true);
			$dataview = json_encode($this->input->post('dataview'));
			
			$this->db->where('module',$modulview);
			$this->db->where('table',$tabelview);
			$query = $this->db->get('translate_view');
			$query = $query->result_object();
			if(!$query){
				$datacmcode = array(
					'module' 		=> $modulview,
					'table' 		=> $tabelview,
					'data' 			=> $dataview,
					'data_order' 	=> $tableorder,
					'created' 		=> date('Y-m-d H:i:s'),
					'createdid' 	=> $this->session->userdata('userid'),
					'modified' 		=> date('Y-m-d H:i:s'),
					'modifiedid' 	=> $this->session->userdata('userid'),
					'active' 		=> 1
				);
									
				$updatecmcode = $this->db->insert('translate_view', $datacmcode);
				if($updatecmcode){
					
					$this->db->where('module',$modulview);
					$this->db->where('table',$tabelview);
					$this->db->where('user_id',$this->session->userdata('userid'));
					$query = $this->db->get('translate_view_user');
					$query = $query->result_object();
					if(!$query){
						$datacmcode = array(
							'module' 		=> $modulview,
							'table' 		=> $tabelview,
							'data' 			=> $dataview,
							'data_order' 	=> $tableorder,
							'user_id' 		=> $this->session->userdata('userid'),
							'created' 		=> date('Y-m-d H:i:s'),
							'createdid' 	=> $this->session->userdata('userid'),
							'modified' 		=> date('Y-m-d H:i:s'),
							'modifiedid' 	=> $this->session->userdata('userid'),
							'active' 		=> 1
						);
											
						$updatecmcode = $this->db->insert('translate_view_user', $datacmcode);
					}else{
						$datacmcode = array(
							'module' 		=> $modulview,
							'table' 		=> $tabelview,
							'data' 			=> $dataview,
							'data_order' 	=> $tableorder,
							'user_id' 		=> $this->session->userdata('userid'),
							'modified' 		=> date('Y-m-d H:i:s'),
							'modifiedid' 	=> $this->session->userdata('userid'),
							'active' 		=> 1
						);
						
						$this->db->where('id',$query[0]->id);						
						$updatecmcode = $this->db->update('translate_view_user', $datacmcode);
					}
			
					$result = array("csrf_hash" => $this->security->get_csrf_hash(),"status" => "success");
					echo json_encode($result);
				}else{
					$result = array("csrf_hash" => $this->security->get_csrf_hash(),"status" => "error");
					echo json_encode($result);
				}
			
			}else{
				
				$datacmcode = array(
					'module' 		=> $modulview,
					'table' 		=> $tabelview,
					'data' 			=> $dataview,
					'data_order' 	=> $tableorder,
					'modified' 		=> date('Y-m-d H:i:s'),
					'modifiedid' 	=> $this->session->userdata('userid'),
					'active' 		=> 1
				);
				

				$this->db->where('id',$query[0]->id);				
				$updatecmcode = $this->db->update('translate_view', $datacmcode);
				if($updatecmcode){
					
					$this->db->where('module',$modulview);
					$this->db->where('table',$tabelview);
					$this->db->where('user_id',$this->session->userdata('userid'));
					$query = $this->db->get('translate_view_user');
					$query = $query->result_object();
					if(!$query){
						$datacmcode = array(
							'module' 		=> $modulview,
							'table' 		=> $tabelview,
							'data' 			=> $dataview,
							'data_order' 	=> $tableorder,
							'user_id' 		=> $this->session->userdata('userid'),
							'created' 		=> date('Y-m-d H:i:s'),
							'createdid' 	=> $this->session->userdata('userid'),
							'modified' 		=> date('Y-m-d H:i:s'),
							'modifiedid' 	=> $this->session->userdata('userid'),
							'active' 		=> 1
						);
											
						$updatecmcode = $this->db->insert('translate_view_user', $datacmcode);
					}else{
						$datacmcode = array(
							'module' 		=> $modulview,
							'table' 		=> $tabelview,
							'data' 			=> $dataview,
							'data_order' 	=> $tableorder,
							'user_id' 		=> $this->session->userdata('userid'),
							'modified' 		=> date('Y-m-d H:i:s'),
							'modifiedid' 	=> $this->session->userdata('userid'),
							'active' 		=> 1
						);
									
						$this->db->where('id',$query[0]->id);
						$updatecmcode = $this->db->update('translate_view_user', $datacmcode);
					}
					
					$result = array("csrf_hash" => $this->security->get_csrf_hash(),"status" => "success");
					echo json_encode($result);
				}else{
					$result = array("csrf_hash" => $this->security->get_csrf_hash(),"status" => "error");
					echo json_encode($result);
				}
			}
		}
		
		public function updatevieworderform(){
			//die();
			
			$modulview = $this->input->post('modulview',true);
			$tabelview = $this->input->post('tabelview',true);
			$tableorder = $this->input->post('tableorder',true);
			$dataview = json_encode($this->input->post('dataview'));
			
			$this->db->where('module',$modulview);
			$this->db->where('table',$tabelview);
			$query = $this->db->get('translate_view');
			$query = $query->result_object();
			if(!$query){
				$datacmcode = array(
					'module' 		=> $modulview,
					'table' 		=> $tabelview,
					'data_order_form' 	=> $tableorder,
					'created' 		=> date('Y-m-d H:i:s'),
					'createdid' 	=> $this->session->userdata('userid'),
					'modified' 		=> date('Y-m-d H:i:s'),
					'modifiedid' 	=> $this->session->userdata('userid'),
					'active' 		=> 1
				);
									
				$updatecmcode = $this->db->insert('translate_view', $datacmcode);
				if($updatecmcode){
					
					$this->db->where('module',$modulview);
					$this->db->where('table',$tabelview);
					$this->db->where('user_id',$this->session->userdata('userid'));
					$query = $this->db->get('translate_view_user');
					$query = $query->result_object();
					if(!$query){
						
						$datacmcode = array(
							'module' 		=> $modulview,
							'table' 		=> $tabelview,
							'data_order_form' 	=> $tableorder,
							'user_id' 		=> $this->session->userdata('userid'),
							'created' 		=> date('Y-m-d H:i:s'),
							'createdid' 	=> $this->session->userdata('userid'),
							'modified' 		=> date('Y-m-d H:i:s'),
							'modifiedid' 	=> $this->session->userdata('userid'),
							'active' 		=> 1
						);
											
						$updatecmcode = $this->db->insert('translate_view_user', $datacmcode);
					}else{
						$datacmcode = array(
							'module' 		=> $modulview,
							'table' 		=> $tabelview,
							'data_order_form' 	=> $tableorder,
							//'user_id' 		=> $this->session->userdata('userid'),
							'modified' 		=> date('Y-m-d H:i:s'),
							'modifiedid' 	=> $this->session->userdata('userid'),
							'active' 		=> 1
						);
						
						$this->db->where('module',$modulview);
						$this->db->where('table',$tabelview);						
						$updatecmcode = $this->db->update('translate_view_user', $datacmcode);
					}
			
					$result = array("csrf_hash" => $this->security->get_csrf_hash(),"status" => "success");
					echo json_encode($result);
				}else{
					$result = array("csrf_hash" => $this->security->get_csrf_hash(),"status" => "error");
					echo json_encode($result);
				}
			
			}else{
				
				$datacmcode = array(
					'module' 		=> $modulview,
					'table' 		=> $tabelview,
					'data_order_form' 	=> $tableorder,
					'modified' 		=> date('Y-m-d H:i:s'),
					'modifiedid' 	=> $this->session->userdata('userid'),
					'active' 		=> 1
				);
				

				$this->db->where('id',$query[0]->id);				
				$updatecmcode = $this->db->update('translate_view', $datacmcode);
				if($updatecmcode){
					
					$this->db->where('module',$modulview);
					$this->db->where('table',$tabelview);
					$this->db->where('user_id',$this->session->userdata('userid'));
					$query = $this->db->get('translate_view_user');
					$query = $query->result_object();
					if(!$query){
						
						$datacmcode = array(
							'module' 		=> $modulview,
							'table' 		=> $tabelview,
							'data_order_form' 	=> $tableorder,
							'user_id' 		=> $this->session->userdata('userid'),
							'created' 		=> date('Y-m-d H:i:s'),
							'createdid' 	=> $this->session->userdata('userid'),
							'modified' 		=> date('Y-m-d H:i:s'),
							'modifiedid' 	=> $this->session->userdata('userid'),
							'active' 		=> 1
						);
											
						$updatecmcode = $this->db->insert('translate_view_user', $datacmcode);
					}else{
						$datacmcode = array(
							'module' 		=> $modulview,
							'table' 		=> $tabelview,
							'data_order_form' 	=> $tableorder,
							//'user_id' 		=> $this->session->userdata('userid'),
							'modified' 		=> date('Y-m-d H:i:s'),
							'modifiedid' 	=> $this->session->userdata('userid'),
							'active' 		=> 1
						);
						
						$this->db->where('module',$modulview);
						$this->db->where('table',$tabelview);							
						$updatecmcode = $this->db->update('translate_view_user', $datacmcode);
					}
					
					$result = array("csrf_hash" => $this->security->get_csrf_hash(),"status" => "success");
					echo json_encode($result);
				}else{
					$result = array("csrf_hash" => $this->security->get_csrf_hash(),"status" => "error");
					echo json_encode($result);
				}
			}
		}
		
		public function getheader(){
			
			$module = $this->input->post('id',true);
			
			if($module == 'data_pengajuan_spk_SPK'){
				$datanya = array(
					'name' 				=> 'SPK',
					'description' 		=> 'SPK',
					'icon' 				=> '<i class="fa fa-database"></i>'
				);
				
				$result = array("message" => "success","data"=> $datanya);
				echo json_encode($result);
				
			}elseif($module == 'data_pengajuan_spk_SPKDRAFT'){
				$datanya = array(
					'name' 				=> 'DRAFT SPK',
					'description' 		=> 'DRAFT SPK',
					'icon' 				=> '<i class="fa fa-database"></i>'
				);
				
				$result = array("message" => "success","data"=> $datanya);
				echo json_encode($result);
					
			}elseif($module == 'master_menu'){
				$datanya = array(
					'name' 				=> 'Menu',
					'description' 		=> 'Menu',
					'icon' 				=> '<i class="fa fa-list"></i>'
				);
				
				$result = array("message" => "success","data"=> $datanya);
				echo json_encode($result);
					
			}else{
			
				$datanya = array();
				$this->db->where('master_menu.module',$module);
				$query = $this->db->get('master_menu');
				$query = $query->result_object();
				if($query){

					foreach($query as $rows){
						
						$datanya = array(
							'name' 				=> $rows->name,
							'description' 		=> $rows->description,
							'icon' 				=> '<i class="'.$rows->icon.'"></i>'
						);
					}
					$result = array("message" => "success","data"=> $datanya);
					echo json_encode($result);
				}else{
					$result = array("message" => "error");
					echo json_encode($result);
				}
			
			}
				
		}
		
		public function get_select_options_filter()
		{
			$table = $this->security->xss_clean($this->input->post('table', true));
			$columnid = $this->security->xss_clean($this->input->post('columnid', true));
			$columnname = $this->security->xss_clean($this->input->post('columnname', true));

			if (empty($table) || empty($columnid) || empty($columnname)) {
				echo json_encode([
					'options' => [],
					'csrf_hash' => $this->security->get_csrf_hash()
				]);
				return;
			}

			$this->db->select("$columnid, $columnname")
					 ->distinct()
					 ->where("$columnname IS NOT NULL")
					 ->where("$columnname !=", "")
					 //->where('active', 1)
					 ->order_by($columnid, 'ASC');

			$query = $this->db->get($table);
			$results = $query->result_array();

			$options = [];
			foreach ($results as $row) {
				if (!empty($row[$columnid])) {
					$options[] = [
						'value' => $row[$columnid],
						'text' => $row[$columnname]
					];
				}
			}

			echo json_encode([
				'options' => $options,
				'csrf_hash' => $this->security->get_csrf_hash()
			]);
		}


		
		
}