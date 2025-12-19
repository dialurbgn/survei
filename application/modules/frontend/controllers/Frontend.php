<?php
//CONTROLLER BY HANAFI GINTING

defined('BASEPATH') OR exit('No direct script access allowed');

class Frontend extends MX_Controller {

		//CONFIG VARIABLE
		private $urlparent = 'frontend'; //NAME TABLE 
		private $identity_id = 'slug'; //IDENTITY TABLE
		private $field = 'slug'; // IDENTITY FROM NAME FOR GET ID
		private $slug_indentity = 'name'; //NAME FIELD 
		private $sorting = 'modified'; // SORT FOR VIEW
		private $exclude = array('status_id','created','modified','createdid','modifiedid','id','active','slug');
		private $exclude_table = array('status_id','created','modified','createdid','modifiedid','id','active','slug');
		private $site_key = site_key; // change this to yours
		private $secret_key = site_secret; // change this to yours
		//END CONFIG VARIABLE
		
		private $viewname;
		private $viewformname;
		private $viewformnamekualifikasi;
		private $tabledb;
		private $tableid;
		private $titlechilddb;
		private $headurldb;
		private $actionurl;
		private $actionurl_register;
		private $actionurl_pengaduan;
		private $module;
		private $modeldb;

		public function __construct()
		{
			$this->viewname = $this->urlparent.'/views/v_data';
			$this->viewformname = $this->urlparent.'/views/v_data_form';
			$this->viewformnamekualifikasi = $this->urlparent.'/views/v_data_form_validasi';
			$this->tabledb = $this->urlparent;
			$this->tableid = $this->urlparent.'.id';
			$this->titlechilddb = strtoupper($this->urlparent);
			$this->headurldb = $this->urlparent;
			$this->actionurl = $this->urlparent.'/actiondata';
			$this->actionurl_register = $this->urlparent.'/actiondata_register';
			$this->actionurl_pengaduan = $this->urlparent.'/actiondata_pengaduan';
			$this->module = $this->urlparent;
			$this->modeldb = 'm_data';
			
			$this->load->library('pagination');
			$this->load->model($this->modeldb,'m_model_data');
			$this->load->model('dashboard/m_dashboard','m_model_master');
			$this->titlechilddb = $this->ortyd->getmodulename($this->module);
			
						
			$logged_in = $this->session->userdata('logged_in');
			if(isset($_GET['id'])){
				if($_GET['id'] != ''){
					if ($logged_in == TRUE) {
						//redirect('dashboard?message=success', 'refresh');
					}else{
						$username = $_GET['id'];
						$checkbase64 = $this->m_model_data->is_base64($username);
						if($checkbase64 == true){
							$login = $this->m_model_data->setSSO($username);
							if($login == true){
								redirect(base_url(), 'refresh');
							}
						}
						
					}
				}else{
					if ($logged_in == TRUE) {
					
					}else{
						//redirect('https://simpktn.kemendag.go.id/index.php/internal', 'refresh');
					}
				}
			}else{
				if ($logged_in == TRUE) {
					
				}else{
					//redirect('https://simpktn.kemendag.go.id/index.php/internal', 'refresh');
				}
			}
			
			//$this->ortyd->session_check();
			//$this->ortyd->access_check($this->module);
		}
		
		public function index()
		{
			$data['title'] = "Home";
			$data['site_key'] = $this->site_key;
			$data['og_tipe'] = 'Content '.' - '.title;
			$data['og_title'] = $data['title'].' - '.title;
			$data['og_url'] = base_url();
			$data['meta_description'] = $data['title'].' - '.title;
			$data['headurl'] = 'frontend';
			$data['module'] = $this->module;
			$this->template->load('frontend',$this->viewname, $data);
		}
		
		public function register()
		{
			
			redirect('login', 'refresh');
			return;
			die();
			
			$data['site_key'] = $this->site_key;
			$data['secret_key'] = $this->secret_key;
			
			if($this->session->userdata('userid')){
				redirect('dashboard?message=success', 'refresh');
			}
			
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

			$data['googlelink'] = $linkgoogle;
			
			$data['title'] = 'Registrasi Pengguna';
			$data['id'] = null;
			$data['module'] = 'users_data';
			$data['modeldb'] = $this->m_model_data;
			$data['exclude'] = array('validate_admin','themes_id','sidebar','lab_id','google_email','google_id','register_by_google','nik','timezone_id','perusahaan_id','cover','user_id_ref','unit_id','gid','company','workplace','status_kepegawaian','position_name','position_id','data_id','notif_id','banned','last_login','owner_id','validate','online_date','app_version','is_email_all','is_test','app_tipe','signature','color','history_id','status_id','created','modified','createdid','modifiedid','id','active','slug');
			$data['data'] = null;
			$data['headurl'] = $this->headurldb;
			$data['action'] = base_url().$this->actionurl.'/0';
			$this->template->load('frontend',$this->urlparent.'/views/v_register', $data);
		}

		public function pengaduan()
		{
			$data['site_key'] = $this->site_key;
			$data['secret_key'] = $this->secret_key;
			$data['title'] = 'Layanan Pengaduan';
			$data['id'] = null;
			$data['module'] = 'data_pengaduan';
			$data['modeldb'] = $this->m_model_data;
			$data['exclude'] = array('date','color','history_id','status_id','created','modified','createdid','modifiedid','id','active','slug');
			$data['data'] = null;
			$data['headurl'] = $this->headurldb;
			$data['action'] = base_url().$this->actionurl_pengaduan.'/0';
			$this->template->load('frontend',$this->urlparent.'/views/v_pengaduan', $data);
		}
		

		public function proses_upload(){
			header('X-Robots-Tag: noindex, nofollow', true);
			echo $this->m_model_master->proses_upload();
		}
		
		public function getcover(){
			header('X-Robots-Tag: noindex, nofollow', true);
			echo $this->m_model_master->getcover($this->urlparent);
		}
		
		public function deleteFile(){
			header('X-Robots-Tag: noindex, nofollow', true);
			$this->ortyd->access_check_update($this->module);
			echo $this->m_model_master->deleteFile();
		}
		
		
		
		public function select2() {
			header('X-Robots-Tag: noindex, nofollow', true);
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
		
		public function tracking() {
			header('X-Robots-Tag: noindex, nofollow', true);
			//print_r(date('Y-m-d H:i:s'));die();
			//$this->menu($p = 'home');
			
			$data['title'] = "Traking Dokumen";
			$data['og_tipe'] = 'Content '.' - '.title;
			$data['og_title'] = $data['title'].' - '.title;
			$data['og_url'] = base_url();
			$data['meta_description'] = $data['title'].' - '.title;
			$data['headurl'] = $this->urlparent;
			$data['module'] = $this->module;
			$this->template->load('frontend','frontend/views/v_tracking', $data);
		}
		
		public function post()
		{
			if($this->input->get('type',true) != null){
				if($this->input->get('type',true) == 'preview'){
					$last = $this->uri->total_segments();
					$slug = $this->uri->segment($last);
					$content = $this->ortyd->getDetailPostbySlugPreview($slug);
					if($content != null){
						$this->output->set_header("X-Robots-Tag: noindex, nofollow", true);
						$data['content_web'] = $this->shortcode->parse($content[0]['content']);
						$data['title'] = $content[0]['name'];
						$data['ditulis'] = $content[0]['ditulis'];
						$data['tanggal'] = $this->ortyd->format_date($content[0]['date']);
						$data['sumber'] = $content[0]['sumber'];
						$data['og_tipe'] = 'Page '.' - '.title;
						$data['og_title'] = $data['title'].' - '.title;
						$data['og_url'] = base_url($slug);
						$data['meta_description'] = $content[0]['name'].' - '.substr($this->ortyd->_clean_input_data($content[0]['description']),0,300);
						$this->template->load('frontend','frontend/views/v_post', $data);
					}else{
						redirect('404','refresh');
					}
				}else{
					redirect('404','refresh');
				}
			}else{
				$last = $this->uri->total_segments();
				$slug = $this->uri->segment($last);
				$content = $this->ortyd->getDetailPostbySlug($slug);
				if($content != null){
					$data['content_web'] = $this->shortcode->parse($content[0]['content']);
					$data['title'] = $content[0]['name'];
					$data['ditulis'] = $content[0]['ditulis'];
					$data['tanggal'] = $this->ortyd->format_date($content[0]['date']);
					$data['sumber'] = $content[0]['sumber'];
					$data['og_tipe'] = 'Page '.' - '.title;
					$data['og_title'] = $data['title'].' - '.title;
					$data['og_url'] = base_url($slug);
					$data['meta_description'] = $content[0]['name'].' - '.substr($this->ortyd->_clean_input_data($content[0]['description']),0,300);
					$this->template->load('frontend','frontend/views/v_post', $data);
				}else{
					redirect('404','refresh');
				}
			}
		}
		
		public function page()
		{
			
			if($this->input->get('type') != null){
				if($this->input->get('type',true) == 'preview'){
					$last = $this->uri->total_segments();
					$slug = $this->uri->segment($last);
					$content = $this->ortyd->getDetailPagebySlugPreview($slug);
					if($content != null){
						$this->output->set_header("X-Robots-Tag: noindex, nofollow", true);
						$data['content_web'] = $this->shortcode->parse($content[0]['content']);
						$data['title'] = $content[0]['name'];
						$data['og_tipe'] = 'Page '.' - '.title;
						$data['og_title'] = $data['title'].' - '.title;
						$data['og_url'] = base_url($slug);
						$data['meta_description'] = $content[0]['name'].' - '.substr($this->ortyd->_clean_input_data($content[0]['description']),0,300);
						$this->template->load('frontend','frontend/views/v_page', $data);
					}else{
						redirect('404','refresh');
					}
				}
			}else{
				$last = $this->uri->total_segments();
				$slug = $this->uri->segment($last);
				$content = $this->ortyd->getDetailPagebySlug($slug);
				if($content != null){
					$data['content_web'] = $this->shortcode->parse($content[0]['content']);
					$data['title'] = $content[0]['name'];
					$data['og_tipe'] = 'Page '.' - '.title;
					$data['og_title'] = $data['title'].' - '.title;
					$data['og_url'] = base_url($slug);
					$data['meta_description'] = $content[0]['name'].' - '.substr($this->ortyd->_clean_input_data($content[0]['description']),0,300);
					$this->template->load('frontend','frontend/views/v_page', $data);
				}else{
					redirect('404','refresh');
				}
			}
			
		}

		public function publikasi(){
			$data = [
				'title' => 'Hasil Pengawasan',
				'meta_description' => 'Hasil Pengawasan - ' . title,
				'tablenya' => 'vw_data_laporan_pengawasan',
				'slug_indentity' => 'slug',
				'module' => 'vw_data_laporan_pengawasan',
				'identity_id' => 'slug',
				'exclude_table' => [
					'id','createdid','created','modifiedid','modified','active','slug',
					'status_id','laporan_no','status_last_id','user_id','role_id','role_name','level_id'
				],
				'headurl' => $this->urlparent,
				'linkdata' => $this->urlparent.'/get_data_daftar_pengawasan'
			];
			
			$this->template->load('frontend','frontend/views/v_publikasi', $data);
		}
		
		public function get_data_daftar_pengawasan()
		{
			header('X-Robots-Tag: noindex, nofollow', true);

			$table = 'vw_data_laporan_pengawasan';
			$sorting = 'modified';
			$exclude = [
				'id','createdid','created','modifiedid','modified','active','slug',
				'status_id','laporan_no','status_last_id','user_id','role_id','role_name','level_id'
			];

			$query_column = $this->ortyd->getviewlistcontrol($table, $this->module, $exclude);
			
			if ($query_column) {
				$ordernya = [null];
				$searchnya = [];
				$selectnya = [];
				$jointable = [];
				$joindetail = [];
				$joinposition = [];
				
				$alias = 0;
				foreach ($query_column as $rowsdata) {
					$table_references = $this->ortyd->get_table_reference($table, $rowsdata['name']);
					
					if ($table_references != null) {
						array_push($ordernya, $table_references[0].'_'.$alias.'.'.$table_references[2]);
						array_push($searchnya, $table_references[0].'_'.$alias.'.'.$table_references[2]);
						array_push($selectnya, $table_references[0].'_'.$alias.'.'.$table_references[2]." as ".$table_references[0].'_'.$alias.'_'.$table_references[2]);
						
						$joinnya = array_search($table_references[0], $selectnya);
						if ($joinnya === false || $joinnya === null) {
							array_push($jointable, $table_references[0].' as '.$table_references[0].'_'.$alias);
							array_push($joindetail, $table.'.'.$rowsdata['name'].' = '.$table_references[0].'_'.$alias.'.'.$table_references[1]);
							array_push($joinposition, 'left');
						}
					} else {
						array_push($ordernya, $table.'.`'.$rowsdata['name'].'`');
						array_push($searchnya, $table.'.`'.$rowsdata['name'].'`');
					}
					$alias++;
				}
				
				array_push($ordernya, null);
				$column_order = $ordernya;
				$column_search = $searchnya;
			} else {
				$column_order = [null];
				$column_search = [null];
				$selectnya = [];
			}
			
			$order = [$table.'.'.$sorting => 'DESC'];
			$selectnya_str = implode(",", $selectnya);
			if ($selectnya_str != '') {
				$selectnya_str = ','.$selectnya_str;
			}
			$select = $table.'.*'.$selectnya_str;
			
			$wherecolumn = [$table.'.active'];
			$wheredetail = [$this->input->post('active')];
			$groupby = [];
		
			$list = $this->ortyd->get_datatables($table, $column_order, $column_search, $order, $select, $jointable, $joindetail, $joinposition, $wherecolumn, $wheredetail, $groupby);
			
			$data = [];
			$no = $_POST['start'];
			
			foreach ($list as $rows) {
				$rows = (array) $rows;
				$no++;
				$row = [$no];
				
				if ($query_column) {
					$alias = 0;
					foreach ($query_column as $rowsdata) {
						$table_references = $this->ortyd->get_table_reference($table, $rowsdata['name']);
						if ($table_references != null) {
							$variable = $rows[$table_references[0].'_'.$alias.'_'.$table_references[2]];
							$row[] = $variable;
						} else {
							$variable = $rows[$rowsdata['name']];
							$variable = $this->ortyd->getFormatData($table, $rowsdata['name'], $variable);
							$row[] = $variable;
						}
						$alias++;
					}
				}
				
				$data[] = $row;
			}
	 
			$output = [
				"draw" => $_POST['draw'],
				"recordsTotal" => $this->ortyd->count_filtered($table, $column_order, $column_search, $order, $select, $jointable, $joindetail, $joinposition, $wherecolumn, $wheredetail, $groupby),
				"recordsFiltered" => $this->ortyd->count_filtered($table, $column_order, $column_search, $order, $select, $jointable, $joindetail, $joinposition, $wherecolumn, $wheredetail, $groupby),
				"csrf_hash" => $this->security->get_csrf_hash(),
				"data" => $data,
			];
			
			echo json_encode($output);
		}
		
		public function contact()
		{
			$data['title'] = "Kontak ".title;
			$data['site_key'] = $this->site_key;
			$data['secret_key'] = $this->secret_key;
			$data['meta_description'] = $data['title'];
			$data['og_tipe'] = 'Kontak '.' - '.title;
			$data['og_title'] = $data['title'];
			$data['og_url'] = base_url('kontak');
			//$data['capcha'] = $this->m_model_data->create_captcha();
			$data['headurl'] = 'frontend';
			$data['module'] = $this->module;
			$this->template->load('frontend','frontend/views/v_contact', $data);
		}
		
		public function saveMessage()
		{
			header('X-Robots-Tag: noindex, nofollow', true);
			$this->load->helper('captcha');
			$this->load->library('form_validation');

			// Ambil input dengan filter XSS
			$name = trim($this->input->post('name', TRUE));
			$email = trim($this->input->post('email', TRUE));
			$subject = trim($this->input->post('subject', TRUE));
			$messageContent = trim($this->input->post('message', TRUE)); // hindari konflik nama variabel
			//$captchaInput = trim($this->input->post('capcha', TRUE));

			// Validasi input
			$this->form_validation->set_rules('name', 'Nama', 'required|max_length[100]');
			$this->form_validation->set_rules('email', 'Email', 'required|valid_email|max_length[100]');
			$this->form_validation->set_rules('subject', 'Subjek', 'required|max_length[150]');
			$this->form_validation->set_rules('message', 'Pesan', 'required|max_length[1000]');
			//$this->form_validation->set_rules('capcha', 'Captcha', 'required');

			if ($this->form_validation->run() === FALSE) {
				echo json_encode([
					'message' => 'errors',
					'errors' => validation_errors()
				]);
				return;
			}

			// Validasi CAPTCHA
			if (!$this->captcha_validation()) {
				echo json_encode([
					'message' => 'error',
					'errors' => 'Captcha tidak valid atau belum diisi.'
				]);
				return;
			}

			// Mulai transaksi database
			$this->db->trans_begin();

			$userId = $this->session->userdata('userid') ?: 0;
			$slug = $this->ortyd->sanitize($subject,'data_message');
			
			$dataInsert = [
				'name'        => $name,
				'email'       => $email,
				'subject'     => $subject,
				'message'     => $messageContent,
				'slug'      => $slug,
				'active'      => 1,
				'date'        => date('Y-m-d H:i:s'),
				'createdid'   => $userId,
				'created'     => date('Y-m-d H:i:s'),
				'modifiedid'  => $userId,
				'modified'    => date('Y-m-d H:i:s'),
			];

			$insert = $this->db->insert('data_message', $dataInsert);

			$this->db->trans_complete();

			if ($this->db->trans_status() === FALSE || !$insert) {
				echo json_encode([
					'message' => 'errors',
					'errors' => 'Terjadi kesalahan saat mengirim pesan.'
				]);
			} else {
				echo json_encode(['message' => 'success']);
			}
		}

	
		public function faq()
		{
			$data['title'] = 'FAQ';
			$data['meta_description'] = 'Tanya Jawab Pertanyaan Seputar SIMPKTN';
			$this->template->load('frontend','frontend/views/v_faq', $data);
		}
		
		public function get_detail_product()
		{
			header('X-Robots-Tag: noindex, nofollow', true);
			$kode = $this->input->post('kode');
			$data = $this->m_model_data->get_by_laporan_no($kode); // Misalnya ini ambil 1 data

			echo json_encode([
				'success' => $data ? true : false,
				'data' => $data,
				'csrf_hash' => $this->security->get_csrf_hash(),
			]);
		}


		public function search()
		{
			$data['title'] = 'Pencarian';
			$data['meta_description'] = 'Cari Data';

			// Ambil dan sanitasi input
			$q = $this->input->get('q', true); // XSS Filter sudah aktif karena true
			$q = trim($q); // Hilangkan spasi di depan dan belakang
			$q = substr($q, 0, 100); // Batasi panjang (misalnya max 100 karakter)
			$q = $this->ortyd->_clean_input_data($q); // Custom sanitasi tambahan (pastikan fungsi ini aman)

			// Validasi jika kosong
			if (empty($q)) {
				$data['datarows'] = [];
				$this->template->load('frontend', 'frontend/views/v_search', $data);
				return;
			}

			// Hitung total data
			$jumlah_data = $this->m_model_data->jumlah_data_all('vw_data_search', $q);

			$segment = 2;
			$jml_per_page = 20;

			// Konfigurasi paginasi
			$config = $this->ortyd->getConfigPagging('search', $jumlah_data, $jml_per_page, $segment, 0);
			$from = (int) $this->uri->segment($segment);
			$this->pagination->initialize($config);

			// Ambil data hasil pencarian
			$data['datarows'] = $this->m_model_data->data_all('vw_data_search', $q, $config['per_page'], $from);

			// Load view
			$this->template->load('frontend', 'frontend/views/v_search', $data);
		}

		
		public function download()
		{
			header('X-Robots-Tag: noindex, nofollow', true);
			$last = $this->uri->total_segments();
			$slug_name = $this->uri->segment($last);
			$slug = $this->ortyd->select2_getname($slug_name,'master_download_type','slug','id');
			
			if($slug != '-'){
				$data['type'] = $slug;
				$slug_name = $this->ortyd->select2_getname($slug_name,'master_download_type','slug','name');
				$data['title'] = 'Download - '.$slug_name;
				$data['meta_description'] = $data['title'].' - '.title;
			}else{
				$data['type'] = 'All';
				$data['title'] = 'Semua Download';
				$data['meta_description'] = $data['title'].' - '.title;
			}
			
			//$data['meta_description'] = 'Berkas KADI';
			$data['table_berkas'] = 'data_download';
			$data['identity_id'] = 'slug';
			$data['exclude_table'] = array('created','modified','createdid','modifiedid','id','active','slug','description','is_frontend');
			$data['headurl'] = $this->urlparent;
			$data['linkdata'] = $this->urlparent.'/get_data_download';
			$this->template->load('frontend','frontend/views/v_download', $data);
		}
	
		function get_data_download(){
			header('X-Robots-Tag: noindex, nofollow', true);
			$type = $this->input->post('type');
			
			$activateddata = array('Inactive','Active');
			$table = 'data_download';
			$sorting = 'modified';
			
			$exclude = array('created','modified','createdid','modifiedid','id','active','slug','description','is_frontend');
			$query_column = $this->ortyd->query_column($table, $exclude);
			if($query_column){
				$ordernya = array(null);
				$searchnya = array();
				foreach($query_column as $rowsdata){
					array_push($ordernya,$table.'.'.$rowsdata['name']);
					array_push($searchnya,$table.'.'.$rowsdata['name']);
				}
				$column_order = $ordernya;
				$column_search = $searchnya;
			}else{
				$column_order = array(null);
				$column_search = array(null);
			}
			
			$order = array($table.'.'.$sorting => 'DESC');
			$select = $table.'.*,master_download_type.name as type_name, data_gallery.path';
			
			array_push($column_order,'master_download_type.name');
			array_push($column_search,'master_download_type.name');
			
			$jointable = array('master_download_type','data_gallery');
			$joindetail = array('master_download_type.id = data_download.type','data_gallery.id = data_download.file_id');
			$joinposition = array('left','left');
			
			$wherecolumn = array();
			$wheredetail = array();
			
			array_push($wherecolumn, $table.'.active');
			array_push($wheredetail, 1);
			
			if($type != 'All'){
				array_push($wherecolumn, $table.'.type');
				array_push($wheredetail, $type);
			}

			$groupby = array();
		
			$list = $this->ortyd->get_datatables($table,$column_order,$column_search,$order,$select,$jointable,$joindetail,$joinposition, $wherecolumn,$wheredetail,$groupby);
			$data = array();
			$no = $_POST['start'];
			foreach ($list as $rows) {
				$rows = (array) $rows;
				$no++;
				$row = array();
				$row[] = $no;
				
				if($query_column){
					foreach($query_column as $rowsdata){
						if($rowsdata['name'] != 'slug' && $rowsdata['name'] != 'active'){
							$variable = $rows[$rowsdata['name']];
							if($rowsdata['name'] == 'type'){
								$row[] = $rows['type_name'];
							}elseif($rowsdata['name'] == 'cover'){
								if(trim($rows[$rowsdata['name']] ?? '') != ''){
									$row[] = '<img width="200" src="'.base_url().$rows['path'].'" />';
								}else{
									$row[] = '<img width="200" src="'.base_url().'themes/ortyd/assets/images/noimage.png" />';
								}
							}elseif($rowsdata['name'] == 'file_id'){
								if(trim($rows[$rowsdata['name']] ?? '') != ''){
									$thumbnail_id = $this->ortyd->select2_getname($rows[$rowsdata['name']],'data_gallery','id','thumbnail_id');
									$path = $this->ortyd->select2_getname($thumbnail_id,'data_gallery','id','path');
									if($path != ''){
										$row[] = '<a class="btn btn-primary" style="width:100%" href="'.base_url().'unduh/'.$rows['file_id'].'"><i class="fa fa-download"></i> Unduh</a>';
									}else{
										$row[] = '';
									}
								}else{
									$row[] = '';
								}
							}else{
								$row[] = $variable;
							}
						}
					}
				}
				
				$data[] = $row;
			}
			
	 
			$output = array(
				"draw" => $_POST['draw'],
				"recordsTotal" => $this->ortyd->count_filtered($table,$column_order,$column_search,$order,$select,$jointable,$joindetail,$joinposition, $wherecolumn,$wheredetail,$groupby),
				"recordsFiltered" => $this->ortyd->count_filtered($table,$column_order,$column_search,$order,$select,$jointable,$joindetail,$joinposition, $wherecolumn,$wheredetail,$groupby),"csrf_hash" => $this->security->get_csrf_hash(),
				"data" => $data,
			);
			
			echo json_encode($output);
		}
		
		function get_data_publikasi(){
			header('X-Robots-Tag: noindex, nofollow', true);
			$type = $this->input->post('type');
			
			$activateddata = array('Inactive','Active');
			$table = 'vw_data_laporan_pengawasan';
			$sorting = 'modified';
			$selectnya = array();
			$jointable = array();
			$joindetail = array();
			$joinposition = array();
			$wherecolumn = array();
			$wheredetail = array();
			
			$exclude = array(
				'id',
				'created',
				'createdid',
				'modified',
				'modifiedid',
				'active',
				'slug',
				'status_id',
				'laporan_no',
				'unit_id',
				'user_id',
				'area_provinsi_id',
				'area_kota_id',
				'status_last_id',
				'tanggal_laporan',
				'tanggal_inspeksi',
				'is_migration',
				'area_provinsi_name',
				'area_kota_name',
				//'lokasi',
				//'product_name',
				//'product_code',
				//'product_description',
				//'product_batch',
				'product_hs_id',
				'product_kategori_id',
				//'product_merk',
				//'product_model',
				'product_tipe_id',
				'slug_product',
				'regulasi_nomor_pendaftaran',
				'regulasi_peraturan',
				'regulasi_standar',
				'regulasi_bukti',
				'is_product_palsu',
				'slug_regulasi',
				//'resiko_deskripsi',
				'resiko_ikhtisar_hasil_uji',
				'resiko_informasi_kejadian',
				'resiko_kesesuaian_standar',
				'resiko_kategori_id',
				'slug_resiko',
				'produsen_nama',
				'produsen_telp',
				'produsen_alamat',
				'produsen_email',
				'produsen_personil',
				'slug_ketelusuran',
				'petugas_name',
				//'tindak_lanjut',
				//'gambar'
			);

			$query_column = $this->ortyd->getviewlistcontrol($table, $this->module, $exclude);
			if($query_column){
				$ordernya = array(null);
				$searchnya = array();
				$alias = 0;
				foreach($query_column as $rowsdata){
					$table_references = null;
					$table_references = $this->ortyd->get_table_reference($table,$rowsdata['name']);
					
					if($table_references != null){
						array_push($ordernya,$table_references[0].'_'.$alias.'.'.$table_references[2]);
						array_push($searchnya,$table_references[0].'_'.$alias.'.'.$table_references[2]);
						array_push($selectnya,$table_references[0].'_'.$alias.'.'.$table_references[2]." as ".$table_references[0].'_'.$alias.'_'.$table_references[2]);
						
						$joinnya = array_search($table_references[0],$selectnya);
						if($joinnya == '' || $joinnya == null){
							array_push($jointable,$table_references[0].' as '.$table_references[0].'_'.$alias);
							array_push($joindetail,$table.'.'.$rowsdata['name'].' = '.$table_references[0].'_'.$alias.'.'.$table_references[1]);
							array_push($joinposition,'left');
						}

					}else{
						array_push($ordernya,$table.'.'."`".$rowsdata['name']."`");
						array_push($searchnya,$table.'.'."`".$rowsdata['name']."`");
					}
					
					$alias++;
				}
				array_push($ordernya,null);
				$column_order = $ordernya;
				$column_search = $searchnya;
			}else{
				$column_order = array(null);
				$column_search = array(null);
			}
			
			$order = array($table.'.'.$sorting => 'DESC');
			$selectnya = implode(",", $selectnya);
			if($selectnya != ''){
				$selectnya = ','.$selectnya;
			}
			$select = $table.'.*'.$selectnya;
			
			//array_push($wherecolumn, $table.'.status_id');
			//array_push($wheredetail, 4);
			
			 // CSRF dan POST data
			$searchColumn = $this->input->post('searchColumn');
			$searchValue = $this->input->post('searchValue');
			
			if (!empty($searchColumn) && !empty($searchValue)) {
				array_push($wherecolumn, $table.'.'.$searchColumn.'|like');
				array_push($wheredetail, $searchValue);
			}

			$groupby = array();
		
			$list = $this->ortyd->get_datatables($table,$column_order,$column_search,$order,$select,$jointable,$joindetail,$joinposition, $wherecolumn,$wheredetail,$groupby);
			$data = array();
			$no = $_POST['start'];
			foreach ($list as $rows) {
				$rows = (array) $rows;
		
				$data[] = [
					'kode' => $rows['slug'] ?? '',
					'image_product' => !empty($rows['gambar']) ? $rows['gambar'] : base_url('themes/ortyd/assets/media/avatars/no_image.jpg'),
					'nama_produk' => $rows['product_name'] ?? '-',
					'nomor_publikasi' => $rows['laporan_no'] ?? '-',
					'nomor_laporan' => $rows['laporan_no'] ?? '-',
					'risiko' => $rows['resiko_deskripsi'] ?? '-',
					'lokasi' => $rows['lokasi'] ?? '-',
					'tindak_lanjut' => $rows['tindak_lanjut'] ?? '-',
				];
			}
			
	 
			$output = array(
				"draw" => $_POST['draw'],
				"recordsTotal" => $this->ortyd->count_filtered($table,$column_order,$column_search,$order,$select,$jointable,$joindetail,$joinposition, $wherecolumn,$wheredetail,$groupby),
				"recordsFiltered" => $this->ortyd->count_filtered($table,$column_order,$column_search,$order,$select,$jointable,$joindetail,$joinposition, $wherecolumn,$wheredetail,$groupby),"csrf_hash" => $this->security->get_csrf_hash(),
				"data" => $data,
			);
			
			echo json_encode($output);
		}
		
		
		function unduh(){
			header('X-Robots-Tag: noindex, nofollow', true);
			$last = $this->uri->total_segments();
			$file_id = $this->uri->segment($last);
			$this->load->helper('download');
			$this->db->where('id', $file_id);
			$query = $this->db->get('data_gallery');
			
			if ($query->num_rows() == 0) {
			   return false;
			}

			$path = '';
			$file = '';

			foreach ($query->result_array() as $result) {

				$path .= FCPATH . 'uploads/'; 

				// This gives the stored file name
				// This is folder 201702
				// Looks like 201702/post_1486965530_jeJNHKWXPMrwRpGBYxczIfTbaqhLnDVO.php

				$stored_file_name = $result['path_server']; 

				// Out puts just example "config.php"
				$original = $result['name']; 
				
				//$this->ortyd->getHistoryAkses(2, $file_id);

			}

			force_download($original,file_get_contents($stored_file_name));
	}
		
	function get_data_view(){
			header('X-Robots-Tag: noindex, nofollow', true);
			$activateddata = array('Inactive','Active');
			$table = $this->input->post('table',true);
			$sorting = 'created';
			$selectnya = array();
			$jointable = array();
			$joindetail = array();
			$joinposition = array();
			$wherecolumn = array();
			$wheredetail = array();
			
			
			$exclude = array('berakhir','status_id','created','modified','createdid','modifiedid','id','active','slug');
			$query_column = $this->ortyd->getviewlistcontrol($table,'data_ticket', $exclude);
			if($query_column){
				$ordernya = array(null);
				$searchnya = array();
				foreach($query_column as $rowsdata){
					$table_references = null;
					//$table_references = $this->ortyd->get_table_reference($table,$rowsdata['name']);
					if($table_references != null){
						array_push($ordernya,$table_references[0].'.'.$table_references[2]);
						array_push($searchnya,$table_references[0].'.'.$table_references[2]);
						array_push($selectnya,$table_references[0].'.'.$table_references[2]." as ".$table_references[0].'_'.$table_references[2]);
						
						$joinnya = array_search($table_references[0],$selectnya);
						if($joinnya == '' || $joinnya == null){
							array_push($jointable,$table_references[0]);
							array_push($joindetail,$table.'.'.$rowsdata['name'].' = '.$table_references[0].'.'.$table_references[1]);
							array_push($joinposition,'left');
						}

					}else{
						array_push($ordernya,$table.'.'.$rowsdata['name']);
						array_push($searchnya,$table.'.'.$rowsdata['name']);
					}
					
				}
				array_push($ordernya,null);
				$column_order = $ordernya;
				$column_search = $searchnya;
			}else{
				$column_order = array(null);
				$column_search = array(null);
			}
			
			$order = array($table.'.'.$sorting => 'DESC');
			$selectnya = implode(",", $selectnya);
			if($selectnya != ''){
				$selectnya = ','.$selectnya;
			}
			$select = $table.'.*'.$selectnya;
			
			
			array_push($wherecolumn, $table.'.active');
			array_push($wheredetail, $this->input->post('active',true));

			$groupby = array();
		
			$list = $this->ortyd->get_datatables($table,$column_order,$column_search,$order,$select,$jointable,$joindetail,$joinposition, $wherecolumn,$wheredetail,$groupby);
			$data = array();
			$no = $_POST['start'];
			foreach ($list as $rows) {
				$rows = (array) $rows;
				$no++;
				$row = array();
				//$row[] = $no;
				
				$identity_id = $rows['slug'];
				$uuid = "'". $rows['slug']."'";
				
				
				$viewdata = '<li class="nav-item"><a class="dropdown-item" href="'.base_url_site_CMS.'data_ticket'.'/replydata/'.$identity_id.'"><i class="fa fa-eye text-info mt-1"></i> View</a></li> ';
				$editdata = '';
				$restoredata = '';
				$deletedata = '';	
				
				if($rows['active'] == 1){
					$status = '<span class="label label-success">'.$activateddata[$rows['active']].'</span>';
					$action = '
				
						<li class="nav-item dropdown language-select text-uppercase" style="list-style-type: none;">
							<a class="btn btn-sm btn-primary nav-link dropdown-item dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</a>
							<ul class="dropdown-menu">
							 <!--begin::Menu item-->
							'.$viewdata.'
							<!--end::Menu item-->
							</ul>
						  </li>
			  
				
					';
				}else{
					$status = '<span class="label label-danger">'.$activateddata[$rows['active']].'</span>';
					$action = '
					
						<li class="nav-item dropdown language-select text-uppercase" style="list-style-type: none;">
							<a class="btn btn-sm btn-primary nav-link dropdown-item dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</a>
							<ul class="dropdown-menu">
							 <!--begin::Menu item-->
							'.$restoredata.'
							<!--end::Menu item-->
							</ul>
						  </li>
						
					';
					
				}
				
				//$row[] = $action;
				if($query_column){
					foreach($query_column as $rowsdata){
						$table_references = null;
						//$table_references = $this->ortyd->get_table_reference($table,$rowsdata['name']);
						if($table_references != null){
							$variable = $rows[$table_references[0].'_'.$table_references[2]];
							$row[] = $variable;
						}elseif($rowsdata['name'] == 'ticket_no'){
							$variable = '<a href="'.base_url('data_ticket/replydata/').$identity_id.'">'.$rows[$rowsdata['name']].'</a>';
							$row[] = $variable;
						}elseif($rowsdata['name'] == 'date' || $rowsdata['name'] == 'tanggal'){
							$variable = $this->ortyd->format_date($rows[$rowsdata['name']]);
							$row[] = $variable;
						}else{
							$variable = $rows[$rowsdata['name']];
							$row[] = $variable;
						}
					}
				}
				//$row[] = $status;

				$data[] = $row;
			}
			
	 
			$output = array(
				"draw" => $_POST['draw'],
				"recordsTotal" => $this->ortyd->count_filtered($table,$column_order,$column_search,$order,$select,$jointable,$joindetail,$joinposition, $wherecolumn,$wheredetail,$groupby),
				"recordsFiltered" => $this->ortyd->count_filtered($table,$column_order,$column_search,$order,$select,$jointable,$joindetail,$joinposition, $wherecolumn,$wheredetail,$groupby),"csrf_hash" => $this->security->get_csrf_hash(),
				"data" => $data,
			);
			
			echo json_encode($output);
	}
	
	function captcha_validation()
		{
			header('X-Robots-Tag: noindex, nofollow', true);
			$secret_key = $this->secret_key; // change this to yours
			$url = 'https://www.google.com/recaptcha/api/siteverify?secret=' . $secret_key . '&response='.$_POST['g-recaptcha-response'];
			$response = @file_get_contents($url);
			$data = json_decode($response, true);
			if($data['success'])
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		
public function survei_pm()
{
    // Cek apakah user sudah login
    $logged_in = $this->session->userdata('logged_in');
    $userid = $this->session->userdata('userid');
    
    $data['title'] = 'Form Survei Penerima Manfaat';
    $data['site_key'] = cloudflare_turnstile_site_key;
    $data['secret_key'] = cloudflare_turnstile_secret_key;
    $data['module'] = 'data_survei_pm';
    $data['module_detail'] = 'data_survei_pm_detail';
    $data['modeldb'] = $this->m_model_data;
    $data['exclude'] = array('survei_pm_id','color','history_id','status_id','created','modified','createdid','modifiedid','id','active','slug');
    $data['exclude_detail'] = array('survei_pm_pm_id','color','history_id','status_id','created','modified','createdid','modifiedid','id','active','slug');
    $data['headurl'] = 'frontend';
    $data['og_tipe'] = 'Form Survei PM - '.title;
    $data['og_title'] = $data['title'].' - '.title;
    $data['og_url'] = base_url('survei');
    $data['meta_description'] = $data['title'].' - '.title;
    $data['action'] = base_url().'frontend/actiondata_survei_pm';
    
    // Setup Google OAuth
    $google_client = new Google_Client();
    $google_client->setClientId(google_id);
    $google_client->setClientSecret(google_secret);
    $google_client->setRedirectUri(base_url('frontend/google_callback_survei'));
    $google_client->addScope('email');
    $google_client->addScope('profile');
    $linkgoogle = $google_client->createAuthUrl();
    
    $data['googlelink'] = $linkgoogle;
    $data['is_logged_in'] = false;
    $data['user_data'] = null;
    
    if ($logged_in && $userid) {
        // User sudah login
        $data['is_logged_in'] = true;
        
        // Ambil data user yang sedang login
        $this->db->where('id', $userid);
        $this->db->where('active', 1);
        $user_data = $this->db->get('users_data')->row();
        
        if ($user_data) {
            $data['user_data'] = $user_data;
            
            // Cek apakah user ini sudah pernah isi survei
            $this->db->where('createdid', $userid);
            $this->db->where('active', 1);
            $existing_survey = $this->db->get('data_survei_pm')->row();
            
            if ($existing_survey) {
                // Mode UPDATE - load data existing
                $data['id'] = $existing_survey->id;
                $data['typedata'] = 'Edit';
                $data['datarow'] = $this->m_model_data->get_data_byid($existing_survey->id, 'data_survei_pm', 'data_survei_pm.id');
                
                // Get detail data
                $this->db->where('survei_pm_pm_id', $existing_survey->id);
                $this->db->where('active', 1);
                $data['datarow_detail'] = $this->db->get('data_survei_pm_detail')->result_object();
            } else {
                // Mode INSERT - form baru
                $data['id'] = null;
                $data['typedata'] = 'Buat';
                $data['datarow'] = null;
                $data['datarow_detail'] = null;
            }
        }
    } else {
        // User belum login - form kosong
        $data['id'] = null;
        $data['typedata'] = 'Buat';
        $data['datarow'] = null;
        $data['datarow_detail'] = null;
    }
    
    $this->template->load('frontend', 'frontend/views/v_survei_pm_form', $data);
}

// Callback setelah login Google untuk survei
public function google_callback_survei()
{
    $google_client = new Google_Client();
    $google_client->setClientId(google_id);
    $google_client->setClientSecret(google_secret);
    $google_client->setRedirectUri(base_url('frontend/google_callback_survei'));
    $google_client->addScope('email');
    $google_client->addScope('profile');
    
    if (isset($_GET['code'])) {
        $token = $google_client->fetchAccessTokenWithAuthCode($_GET['code']);
        
        if (!isset($token['error'])) {
            $google_client->setAccessToken($token['access_token']);
            
            $google_service = new Google_Service_Oauth2($google_client);
            $data_google = $google_service->userinfo->get();
            
            $email = $data_google['email'];
            $name = $data_google['name'];
            $google_id = $data_google['id'];
            
            // Cek apakah user sudah terdaftar
            $this->db->where('email', $email);
            $this->db->or_where('google_email', $email);
            $this->db->where('active', 1);
            $user = $this->db->get('users_data')->row();
            
            if ($user) {
                // User sudah ada, login
                $this->auto_login_user($user->id, $user->username, $user->gid);
            } else {
                // User baru, daftarkan otomatis
                $username = $this->generate_unique_username($name);
                $timestamp = date('Y-m-d H:i:s');
                
                $dataUser = [
                    'fullname' => $name,
                    'username' => $username,
                    'password' => $this->ortyd->hash(bin2hex(random_bytes(16))), // Random password
                    'email' => $email,
                    'google_email' => $email,
                    'google_id' => $google_id,
                    'gid' => 3,
                    'active' => 1,
                    'banned' => 0,
                    'validate' => 1,
                    'validate_admin' => 1,
                    'register_by_google' => 1,
                    'last_login' => $timestamp,
                    'created' => $timestamp,
                    'modified' => $timestamp,
                    'createdid' => 0,
                    'modifiedid' => 0,
                    'slug' => $this->ortyd->sanitize($username, 'users_data'),
                    'timezone_id' => 1,
                    'themes_id' => 1,
                    'sidebar' => 0,
                    'is_test' => 0
                ];
                
                $this->db->insert('users_data', $dataUser);
                $user_id = $this->db->insert_id();
                
                // Auto login
                $this->auto_login_user($user_id, $username, 3);
            }
            
            // Redirect kembali ke form survei
            redirect('survei', 'refresh');
        } else {
            // Error dari Google
            redirect('survei?error=google_auth_failed', 'refresh');
        }
    } else {
        redirect('survei', 'refresh');
    }
}

private function auto_login_user($user_id, $username, $gid)
{
    $session_data = [
        'userid' => $user_id,
        'username' => $username,
        'group_id' => $gid,
        'logged_in' => TRUE,
        'login_time' => date('Y-m-d H:i:s')
    ];
    
    $this->session->set_userdata($session_data);
    
    // Update last login
    $this->db->where('id', $user_id);
    $this->db->update('users_data', [
        'last_login' => date('Y-m-d H:i:s'),
        'online_date' => date('Y-m-d H:i:s')
    ]);
}

private function generate_unique_username($fullname)
{
    $base_username = strtolower(str_replace(' ', '', $fullname));
    $base_username = preg_replace('/[^a-z0-9]/', '', $base_username);
    $base_username = substr($base_username, 0, 20);
    
    $username = $base_username;
    $counter = 1;
    
    while (true) {
        $this->db->where('username', $username);
        $exists = $this->db->get('users_data')->num_rows();
        
        if ($exists == 0) {
            break;
        }
        
        $username = $base_username . $counter;
        $counter++;
    }
    
    return $username;
}

private function validate_cloudflare_turnstile()
{
    $token = $this->input->post('cf-turnstile-response');
    
    if (empty($token)) {
        return false;
    }
    
    $secret_key = cloudflare_turnstile_secret_key;
    $ip = $this->input->ip_address();
    
    $data = [
        'secret' => $secret_key,
        'response' => $token,
        'remoteip' => $ip
    ];
    
    $ch = curl_init('https://challenges.cloudflare.com/turnstile/v0/siteverify');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    $result = json_decode($response, true);
    
    return isset($result['success']) && $result['success'] === true;
}


function getDataExist()
{
    $autoLogin = $this->check_autologin_survei_pm();

    if ($autoLogin['status'] === true) {
        echo json_encode([
            'status' => 'success'
        ]);
        return;
    } elseif (isset($autoLogin['error'])) {
        echo json_encode([
            'status' => 'error',
            'error'  => $autoLogin['error']
        ]);
        return;
    }
}


private function check_autologin_survei_pm()
{
    $this->load->helper('security');

    // Ambil & sanitasi POST
    $nama  = $this->security->xss_clean($this->input->post('survei_pm_nama', true));
    $nip   = $this->security->xss_clean($this->input->post('survei_pm_nip', true));
    $email = $this->security->xss_clean($this->input->post('survei_pm_email', true));
    $tlp   = $this->security->xss_clean($this->input->post('survei_pm_tlp', true));
    $kec   = $this->security->xss_clean($this->input->post('survei_pm_wil_id', true));

    // Validasi wajib
    if (!$nama || !$nip || !$email || !$tlp || !$kec) {
        return [
            'status' => false,
            'error'  => 'Data identitas belum lengkap'
        ];
    }

    // Validasi email
    if (!filter_var($email ?? '', FILTER_VALIDATE_EMAIL)) {
        return [
            'status' => false,
            'error'  => 'Format email tidak valid'
        ];
    }
	
	$this->db->where('survei_pm_email', $email);
	$this->db->where('survei_pm_nama', $nama);
	$this->db->where('survei_pm_nip', $nip);
	$this->db->where('survei_pm_tlp', $tlp);
	$this->db->where('survei_pm_wil_id', $kec);
	$this->db->where('active', 1);

	$user = $this->db->get('data_survei_pm')->row();

	if (!$user) {
		return [
			'status' => false,
			'is_new_user' => true
		];
	}

    // Cari user berdasarkan email
    $this->db->where('email', $email);
    $this->db->where('active', 1);
    $user = $this->db->get('users_data')->row();

    if (!$user) {
        return [
            'status' => false,
            'is_new_user' => true
        ];
    }

    /**
     * Validasi tambahan
     * Cocokkan nama & no telp (aman, fleksibel)
     */
    if (
        trim(strtolower($user->fullname)) !== trim(strtolower($nama)) ||
        trim($user->notelp) !== trim($tlp)
    ) {
        return [
            'status' => false,
            'error'  => 'Data tidak sesuai dengan akun terdaftar'
        ];
    }

    // Jika belum login atau beda user → auto login
    if (
        !$this->session->userdata('logged_in') ||
        $this->session->userdata('userid') != $user->id
    ) {
        $this->auto_login_user($user->id, $user->username, $user->gid);
    }

    return [
        'status'   => true,
        'user_id'  => $user->id,
        'user'     => $user
    ];
}

public function actiondata_survei_pm() 
{
    header('X-Robots-Tag: noindex, nofollow', true);
    
    // CEK LOGIN DULU
    $logged_in = $this->session->userdata('logged_in');
    $userid = $this->session->userdata('userid');
    
    if (!$logged_in || !$userid) {
        echo json_encode([
            "status" => "error", 
            "error" => "Anda harus login terlebih dahulu untuk mengisi survei.",
            "redirect" => base_url('survei-pm')
        ]);
        return;
    }
    
    $this->load->library(['form_validation']);
    $this->load->helper(['security', 'validation']);
    
    // Validasi Cloudflare Turnstile
    if (!$this->validate_cloudflare_turnstile()) {
        echo json_encode([
            "status" => "error", 
            "error" => "Verifikasi keamanan gagal. Silakan coba lagi."
        ]);
        return;
    }
    
    // Ambil dan sanitasi data dengan pengecekan null
    $survei_pm_nama = $this->input->post('survei_pm_nama', true) ?? '';
    $survei_pm_nip = $this->input->post('survei_pm_nip', true) ?? '';
    $survei_pm_email = $this->input->post('survei_pm_email', true) ?? '';
    $survei_pm_tlp = $this->input->post('survei_pm_tlp', true) ?? '';
    $survei_pm_wil_id = $this->input->post('survei_pm_wil_id', true) ?? '';
    
    // Trim whitespace
    $survei_pm_nama = trim($survei_pm_nama);
    $survei_pm_nip = trim($survei_pm_nip);
    $survei_pm_email = trim($survei_pm_email);
    $survei_pm_tlp = trim($survei_pm_tlp);
    $survei_pm_wil_id = trim($survei_pm_wil_id);
    
    // Validasi 5 field wajib
    if (empty($survei_pm_nama) || empty($survei_pm_nip) || empty($survei_pm_email) || 
        empty($survei_pm_tlp) || empty($survei_pm_wil_id)) {
        echo json_encode([
            "status" => "error", 
            "error" => "Data Nama, NIP, Email, No Telepon, dan Kecamatan harus diisi."
        ]);
        return;
    }
    
    // Validasi email format - Gunakan helper function
    if (!is_valid_email($survei_pm_email)) {
        echo json_encode([
            "status" => "error", 
            "error" => "Format email tidak valid."
        ]);
        return;
    }
    
    $data = [];
    $module = 'data_survei_pm';
    $module_detail = 'data_survei_pm_detail';
    $exclude = array('survei_pm_id','color','history_id','status_id','created','modified','createdid','modifiedid','id','active','slug');
    $exclude_detail = array('survei_pm_pm_id','color','history_id','status_id','created','modified','createdid','modifiedid','id','active','slug');
    
    // Get columns
    $query_column = $this->ortyd->query_column($module, $exclude);
    if (!$query_column) {
        echo json_encode(["status" => "error", "error" => "Query Column Error"]);
        return;
    }
    
    $query_column_detail = $this->ortyd->query_column($module_detail, $exclude_detail);
    if (!$query_column_detail) {
        echo json_encode(["status" => "error", "error" => "Query Column Detail Error"]);
        return;
    }
    
    $timestamp = date('Y-m-d H:i:s');
    $logged_in = $this->session->userdata('logged_in');
    $userid = $this->session->userdata('userid');
    
    // ======== STEP 1: CEK/BUAT USER DI users_data ========
    
    // Cek apakah user dengan email ini sudah ada
    $this->db->where('email', $survei_pm_email);
    $this->db->where('active', 1);
    $existing_user = $this->db->get('users_data')->row();
    
    if ($existing_user) {
        // User sudah terdaftar
        $user_id = $existing_user->id;
        
        // Auto login jika belum login
        if (!$logged_in || $userid != $user_id) {
            $this->auto_login_user($user_id, $existing_user->username, $existing_user->gid);
            $userid = $user_id;
        }
        
        // Cek apakah user ini sudah pernah isi survei
        $this->db->where('createdid', $user_id);
        $this->db->where('active', 1);
        $existing_survey = $this->db->get($module)->row();
        
        if ($existing_survey) {
            // Mode UPDATE
            $isUpdate = true;
            $survei_pm_id = $existing_survey->id;
        } else {
            // Mode INSERT (user lama tapi belum pernah isi survei)
            $isUpdate = false;
        }
        
    } else {
        // User belum terdaftar, buat user baru (AUTO REGISTER)
        $username = $this->generate_unique_username($survei_pm_nama);
        $default_password = 'survei123'; // Password default
        
        $dataUser = [
            'fullname' => $survei_pm_nama,
            'username' => $username,
            'password' => $this->ortyd->hash($default_password),
            'email' => $survei_pm_email,
			'google_email' => $survei_pm_email,
            'notelp' => $survei_pm_tlp,
            'gid' => 3, // Group ID user biasa
            'active' => 1,
            'banned' => 0,
            'validate' => 1,
            'last_login' => $timestamp,
            'created' => $timestamp,
            'modified' => $timestamp,
            'createdid' => 0,
            'modifiedid' => 0,
            'slug' => $this->ortyd->sanitize($username, 'users_data'),
            'timezone_id' => 1,
            'register_by_google' => 0,
            'is_test' => 0,
            'themes_id' => 1,
            'sidebar' => 0,
            'validate_admin' => 1
        ];
        
        $this->db->trans_begin();
        
        $insert_user = $this->db->insert('users_data', $dataUser);
        
        if (!$insert_user || $this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo json_encode([
                "status" => "error",
                "error" => "Gagal melakukan registrasi otomatis."
            ]);
            return;
        }
        
        $user_id = $this->db->insert_id();
        $this->db->trans_commit();
        
        // Auto login user baru
        $this->auto_login_user($user_id, $username, 3);
        $userid = $user_id;
        
        // Mode INSERT (user baru)
        $isUpdate = false;
    }
    
    // ======== STEP 2: SIMPAN DATA SURVEI ========
    
    // Process master data
    foreach ($query_column as $column) {
        $name = $column['name'];
        $type = $this->ortyd->getTipeData($module, $name);
        $input = $this->input->post($name, true);
        $input = $this->security->xss_clean($input ?? '');
        
        if ($type == 'CURRENCY' || $name == 'nilai') {
            $input = $this->ortyd->unformatrp($input);
        }
        
        $data[$name] = ($input === '') ? null : $input;
    }
    
    $data['modifiedid'] = $userid;
    $data['modified'] = $timestamp;
    $data['active'] = 1;
    $data['status_id'] = 1;
    
    if (!$isUpdate) {
        $data['createdid'] = $userid;
        $data['created'] = $timestamp;
        $data['slug'] = $this->ortyd->sanitize($survei_pm_nama . '-' . time(), $module);
    }
    
    // Start transaction untuk survei
    $this->db->trans_begin();
    
    // Insert or Update Master
    if ($isUpdate) {
        $this->db->where('id', $survei_pm_id);
        $this->db->where('createdid', $userid);
        $success = $this->db->update($module, $data);
    } else {
        $success = $this->db->insert($module, $data);
        $survei_pm_id = $this->db->insert_id();
    }
    
    if (!$success) {
        $this->db->trans_rollback();
        echo json_encode([
            "status" => "error", 
            "error" => "Gagal menyimpan data survei"
        ]);
        return;
    }
    
    // Process detail data
    $data_detail = [];
    foreach ($query_column_detail as $column) {
        $name = $column['name'];
        $type = $this->ortyd->getTipeData($module_detail, $name);
        $input = $this->input->post($name, true);
        $input = $this->security->xss_clean($input ?? '');
        
        if ($type == 'CURRENCY' || $type == 'NUMBER') {
            $input = ($input === '' || $input === null) ? 0 : (int)$input;
        }
        
        $data_detail[$name] = $input;
    }
    
    $data_detail['survei_pm_pm_id'] = $survei_pm_id;
    $data_detail['modifiedid'] = $userid;
    $data_detail['modified'] = $timestamp;
    $data_detail['active'] = 1;
    $data_detail['status_id'] = 1;
    
    // Check if detail exists
    if ($isUpdate) {
        $this->db->where('survei_pm_pm_id', $survei_pm_id);
        $this->db->where('active', 1);
        $existing_detail = $this->db->get($module_detail)->row();
        
        if ($existing_detail) {
            $this->db->where('survei_pm_pm_id', $survei_pm_id);
            $this->db->where('active', 1);
            $success_detail = $this->db->update($module_detail, $data_detail);
        } else {
            $data_detail['createdid'] = $userid;
            $data_detail['created'] = $timestamp;
            $data_detail['slug'] = $this->ortyd->sanitize('detail-' . $survei_pm_id, $module_detail);
            $success_detail = $this->db->insert($module_detail, $data_detail);
        }
    } else {
        $data_detail['createdid'] = $userid;
        $data_detail['created'] = $timestamp;
        $data_detail['slug'] = $this->ortyd->sanitize('detail-' . $survei_pm_id, $module_detail);
        $success_detail = $this->db->insert($module_detail, $data_detail);
    }
    
    // Check transaction
    if ($this->db->trans_status() === FALSE || !$success_detail) {
        $this->db->trans_rollback();
        echo json_encode([
            "status" => "error", 
            "error" => "Gagal menyimpan data detail"
        ]);
        return;
    }
    
    $this->db->trans_commit();
    
    // Save evidence
    //$this->saveEvidence($survei_pm_id, $module);
    
    $message = $isUpdate ? 
        "Data survei berhasil diupdate" : 
        "Data survei berhasil disimpan. Anda telah terdaftar di sistem.";
    
    echo json_encode([
        "status" => "success",
        "message" => $message,
        "is_new_user" => !$existing_user,
        "is_update" => $isUpdate,
        "csrf_hash" => $this->security->get_csrf_hash()
    ]);
}

public function select2_kecamatan() {
    header('X-Robots-Tag: noindex, nofollow', true);
    header('Content-Type: application/json');
    
    $table = $this->input->post('table', true);
    $id = $this->input->post('id', true);
    $name = $this->input->post('name', true);
    $q = $this->input->post('q', true);
    $page = $this->input->post('page', true) ? (int)$this->input->post('page', true) : 1;
    
    // Pagination settings
    $per_page = 30;
    $offset = ($page - 1) * $per_page;
    
    // Filter parameters (opsional untuk filter provinsi/kota)
    $provinsi_id = $this->input->post('provinsi_id', true);
    $kota_id = $this->input->post('kota_id', true);
    
    if (!$q) {
        $q = '';
    }
    
    // Clean search query
    $q = trim($q);
    
    // Build select query
    $this->db->select($table.'.'.$id.' as id, '.$table.'.'.$name.' as name');
    
    // Add keyword field if exists
    if ($this->db->field_exists('wil_keyword', $table)) {
        $this->db->select($table.'.wil_keyword', false);
    }
    
    // Filter by provinsi if provided
    if (!empty($provinsi_id) && $provinsi_id != '0') {
        if ($this->db->field_exists('wil_provinsi_id', $table)) {
            $this->db->where($table.'.wil_provinsi_id', $provinsi_id);
        }
    }
    
    // Filter by kota if provided
    if (!empty($kota_id) && $kota_id != '0') {
        if ($this->db->field_exists('wil_kota_id', $table)) {
            $this->db->where($table.'.wil_kota_id', $kota_id);
        }
    }
    
    // Search filter
    if (!empty($q)) {
        $this->db->group_start();
        $this->db->like($table.'.'.$name, $q, 'both');
        
        // Search by keyword if field exists
        if ($this->db->field_exists('wil_keyword', $table)) {
            $this->db->or_like($table.'.wil_keyword', $q, 'both');
        }
        
        $this->db->group_end();
    }
    
    // Only active records
    if ($this->db->field_exists('active', $table)) {
        $this->db->where($table.'.active', 1);
    }
    
	$this->db->where($table.'.wil_level', 3);
    // Count total for pagination
    $total_query = clone $this->db;
    $total_count = $total_query->count_all_results($table, false);
    
    // Apply pagination and ordering
    $this->db->order_by($table.'.'.$name, 'ASC');
    $this->db->limit($per_page, $offset);
    
    $query = $this->db->get($table);
    $results = $query->result_array();
    
    $data = [];
    
    if ($results) {
        foreach ($results as $row) {
            $data[] = [
                'id' => (int)$row['id'],
                'name' => $row['name']
            ];
        }
    }
    
    // Prepare response in format yang sesuai dengan select2formsidefront.php
    $response = [
        'items' => $data,
        'total_count' => $total_count,
        'csrf_hash' => $this->security->get_csrf_hash()
    ];
    
    echo json_encode($response);
}
		
		
}
