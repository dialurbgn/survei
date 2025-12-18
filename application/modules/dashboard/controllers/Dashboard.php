<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MX_Controller {
	//CONFIG VARIABLE
		private $urlparent = 'dashboard'; //NAME TABLE 
		private $identity_id = 'slug'; //IDENTITY TABLE
		private $field = 'slug'; // IDENTITY FROM NAME FOR GET ID
		private $slug_indentity = 'name'; //NAME FIELD 
		private $sorting = 'modified'; // SORT FOR VIEW
		private $exclude = array('color','history_id','status_id','created','modified','createdid','modifiedid','id','active','slug');
		private $exclude_table = array('color','history_id','status_id','created','modified','createdid','modifiedid','id','active','slug');
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
		}
		
		public function search_meta_like() {
			$keyword = $this->input->post('keyword', true);

			if($keyword){
				$this->db->like('meta_value', $keyword);
			}
			$this->db->where('active',1);
			$query = $this->db->get('translate');
			$result = $query->result_array();

			echo json_encode([
				'csrf_hash' => $this->security->get_csrf_hash(),
				'status' => 'success',
				'data' => $result
			]);
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


		
		public function index()
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
			$this->template->load('main',$this->viewname, $data);
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
		
		
		function getcount(){
			
			$totalmitra = 0;
			$totalmitrapra = 0;
			$totalmitrainput = 0;
			$totalmitraverified = 0;
			$totalnotakebutuhan = 0;
			$totaljustifikasikebutuhan = 0;
			$totalspph = 0;
			$totalspk = 0;
			$totalbast = 0;
			$totalinvoice = 0;
			
			$this->db->select('count(vw_data_perusahaan.id) as jumlah');
			$this->db->where_in('vw_data_perusahaan.status_id',array(1,2,7));
			$query = $this->db->get('vw_data_perusahaan');
			$query = $query->result_object();
			if($query){
				$totalmitra = $query[0]->jumlah;
			}
			
			$this->db->select('count(vw_data_perusahaan.id) as jumlah');
			$this->db->where_in('vw_data_perusahaan.status_id',array(1));
			$query = $this->db->get('vw_data_perusahaan');
			$query = $query->result_object();
			if($query){
				$totalmitrapra = $query[0]->jumlah;
			}
			
			$this->db->select('count(vw_data_perusahaan.id) as jumlah');
			$this->db->where_in('vw_data_perusahaan.status_id',array(2,3,4,5,6));
			$query = $this->db->get('vw_data_perusahaan');
			$query = $query->result_object();
			if($query){
				$totalmitrainput = $query[0]->jumlah;
			}
			
			$this->db->select('count(vw_data_perusahaan.id) as jumlah');
			$this->db->where_in('vw_data_perusahaan.status_id',array(7));
			$query = $this->db->get('vw_data_perusahaan');
			$query = $query->result_object();
			if($query){
				$totalmitraverified = $query[0]->jumlah;
			}
			
			$this->db->select('count(vw_data_nota_kebutuhan.id) as jumlah');
			if( $this->session->userdata('group_id') != 1 && $this->session->userdata('group_id') != 2 && $this->session->userdata('group_id') != 4){
				$this->db->where('vw_data_nota_kebutuhan.createdid',$this->session->userdata('userid'));
			}
			$this->db->where('vw_data_nota_kebutuhan.spph_id is null',null);
			$query = $this->db->get('vw_data_nota_kebutuhan');
			$query = $query->result_object();
			if($query){
				$totalnotakebutuhan = $query[0]->jumlah;
			}
			
			$this->db->select('count(vw_data_justifikasi_kebutuhan.id) as jumlah');
			if( $this->session->userdata('group_id') != 1 && $this->session->userdata('group_id') != 2 && $this->session->userdata('group_id') != 4){
				$this->db->where('vw_data_justifikasi_kebutuhan.createdid',$this->session->userdata('userid'));
			}
			$this->db->where('vw_data_justifikasi_kebutuhan.spph_id is null',null);
			$query = $this->db->get('vw_data_justifikasi_kebutuhan');
			$query = $query->result_object();
			if($query){
				$totaljustifikasikebutuhan = $query[0]->jumlah;
			}
			
			$this->db->select('count(vw_data_spph.id) as jumlah');
			$this->db->where_in('vw_data_spph.status_id',array(0,1));
			if(($this->session->userdata('tipe_data') != '' && $this->session->userdata('tipe_data') != null)){
				$this->db->where('vw_data_spph.tipe_spph',$this->session->userdata('tipe_data'));
			} 
			$query = $this->db->get('vw_data_spph');
			$query = $query->result_object();
			if($query){
				$totalspph = $query[0]->jumlah;
			}
			
			$this->db->select('count(vw_data_spph.id) as jumlah');
			$this->db->where('vw_data_spph.spk_id is not null',null);
			if(($this->session->userdata('tipe_data') != '' && $this->session->userdata('tipe_data') != null)){
				$this->db->where('vw_data_spph.tipe_spph',$this->session->userdata('tipe_data'));
			}
			$this->db->where_in('vw_data_spph.status_id',array(6,7));
			$query = $this->db->get('vw_data_spph');
			$query = $query->result_object();
			if($query){
				$totalspk = $query[0]->jumlah;
			}
			
			$this->db->select('count(vw_data_bast.bast_id) as jumlah');
			$this->db->where('vw_data_bast.bast_nilai is not null',null);
			$this->db->where('vw_data_bast.kondisi_id !=',1);
			if(($this->session->userdata('tipe_data') != '' && $this->session->userdata('tipe_data') != null)){
				$this->db->where('vw_data_bast.tipe_spph',$this->session->userdata('tipe_data'));
			} 
			$query = $this->db->get('vw_data_bast');
			$query = $query->result_object();
			if($query){
				$totalbast = $query[0]->jumlah;
			}
			
			$this->db->select('count(vw_data_invoice.invoice_id) as jumlah');
			$this->db->where('vw_data_invoice.invoice_id is not null',null);
			//$this->db->where('vw_data_invoice.status_id',4);
			if(($this->session->userdata('tipe_data') != '' && $this->session->userdata('tipe_data') != null)){
				$this->db->where('vw_data_invoice.tipe_spph',$this->session->userdata('tipe_data'));
			} 
			$query = $this->db->get('vw_data_invoice');
			$query = $query->result_object();
			if($query){
				$totalinvoice = $query[0]->jumlah;
			}
			
			$datanya = array(
				'total_mitra' => $totalmitra,
				'total_mitra_pra' => $totalmitrapra,
				'total_mitra_input' => $totalmitrainput,
				'total_mitra_verified' => $totalmitraverified,
				'total_nota_kebutuhan' => $totalnotakebutuhan,
				'total_justifikasi_kebutuhan' => $totaljustifikasikebutuhan,
				'total_spph' => $totalspph,
				'total_spk' => $totalspk,
				'total_bast' => $totalbast,
				'total_invoice' => $totalinvoice
			);
			
			$result = array("csrf_hash" => $this->security->get_csrf_hash(),"message" => "success", "data" => $datanya);
			
			echo json_encode($result);
		}
		
		
		function uploadBase64_new()
		{
			echo $this->m_dashboard->uploadBase64_new();
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
    
    // Deteksi database driver
    $is_postgre = ($this->db->dbdriver == 'postgre' || $this->db->dbdriver == 'postgre');

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

    // Query dengan deteksi database
    if($is_postgre){
        // PostgreSQL
        $this->db->where('meta_id', $meta_id);
        $this->db->where('meta_table', $meta_table);
        $query = $this->db->get('translate');
        $query = $query->result_object();
    }else{
        // MySQL
        $this->db->where('meta_id', $meta_id);
        $this->db->where('meta_table', $meta_table);
        $query = $this->db->get('translate');
        $query = $query->result_object();
    }
    
    if(!$query){
        
        $datacmcode = array(
            'meta_size'         => 12,
            'meta_tipe'         => 'TEXT',
            'meta_required'     => $required,
            'meta_value'        => $meta_value,
            'meta_table'        => $meta_table,
            'meta_table_ref'                => '',
            'meta_table_id_ref_value'       => '',
            'meta_table_name_ref_value'     => '',
            'meta_table_id_ref'     => '',
            'meta_table_name_ref'   => '',
            'meta_id'           => $meta_id,
            'meta_only_name'    => 1,
            'meta_nested'       => 0,
            'meta_nested_field_id'      => '',
            'meta_nested_field_name'    => '',
            'meta_nested_ref_id'        => '',
            'meta_nested_ref_id_value'  => '',
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
        
        // Handle null values untuk PostgreSQL
        if($is_postgre){
            // PostgreSQL mengembalikan null sebagai NULL, bukan string 'null'
            $meta_nested = ($query[0]->meta_nested === null || $query[0]->meta_nested === '' || $query[0]->meta_nested === 'null') ? 0 : $query[0]->meta_nested;
            
            if($query[0]->meta_nested_field_id === null || $query[0]->meta_nested_field_id === '' || $query[0]->meta_nested_field_id === 'null'){
                $meta_nested_field_name = '';
            }else{
                $meta_nested_field_name = $this->ortyd->translate_column($meta_table,$query[0]->meta_nested_field_id);
            }
        }else{
            // MySQL
            if($query[0]->meta_nested == null || $query[0]->meta_nested == '' || $query[0]->meta_nested == 'null'){
                $query[0]->meta_nested = 0;
            }
            
            if($query[0]->meta_nested_field_id == null || $query[0]->meta_nested_field_id == '' || $query[0]->meta_nested_field_id == 'null'){
                $meta_nested_field_name = '';
            }else{
                $meta_nested_field_name = $this->ortyd->translate_column($meta_table,$query[0]->meta_nested_field_id);
            }
            
            $meta_nested = $query[0]->meta_nested;
        }
        
        if($required == 1){
            
        }else{
            $required = (int)$query[0]->meta_required;
        }
        
        // Handle empty string vs null untuk PostgreSQL
        $meta_table_ref = ($is_postgre && $query[0]->meta_table_ref === null) ? '' : $query[0]->meta_table_ref;
        $meta_table_id_ref = ($is_postgre && $query[0]->meta_table_id_ref === null) ? '' : $query[0]->meta_table_id_ref;
        $meta_table_name_ref = ($is_postgre && $query[0]->meta_table_name_ref === null) ? '' : $query[0]->meta_table_name_ref;
        $meta_nested_field_id = ($is_postgre && $query[0]->meta_nested_field_id === null) ? '' : $query[0]->meta_nested_field_id;
        $meta_nested_ref_id = ($is_postgre && $query[0]->meta_nested_ref_id === null) ? '' : $query[0]->meta_nested_ref_id;
        
        $datacmcode = array(
            'meta_size'     => $query[0]->meta_size,
            'meta_tipe'     => $query[0]->meta_tipe,
            'meta_required'     => $required,
            'meta_value'    => $query[0]->meta_value,
            'meta_table'    => $query[0]->meta_table,
            'meta_table_ref'        => $meta_table_ref,
            'meta_table_id_ref'     => $meta_table_id_ref,
            'meta_table_name_ref'   => $meta_table_name_ref,
            'meta_table_name_ref_value'     => ($meta_table_ref != '') ? $this->ortyd->translate_column($meta_table_ref, $meta_table_name_ref) : '',
            'meta_table_id_ref_value'   => ($meta_table_ref != '') ? $this->ortyd->translate_column($meta_table_ref, $meta_table_id_ref) : '',
            'meta_id'       => $query[0]->meta_id,
            'meta_only_name'    =>  $query[0]->meta_only_name,
            'meta_nested'   => $meta_nested,
            'meta_nested_field_id'  => $meta_nested_field_id,
            'meta_nested_field_name'    => $meta_nested_field_name,
            'meta_nested_ref_id'    => $meta_nested_ref_id,
            'meta_nested_ref_id_value'  => ($meta_table_ref != '' && $meta_nested_ref_id != '') ? $this->ortyd->translate_column($meta_table_ref, $meta_nested_ref_id) : '',
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
		
		public function getColumn(){
			echo $this->m_dashboard_popup->getColumn();
		}
		
		public function getColumnDetail(){
			echo $this->m_dashboard_popup->getColumnDetail();
		}
		
		
		
		public function projectbysales(){
			
			$tahun = $this->input->post('tahun',true);
			$tipe = $this->input->post('project_tipe',true);
			$tipenya = $tipe.' '.$tahun;
			
			$datalabel = [];
			$dataisi = [];
			$datavalue = [];
			
			$this->db->select('*');
			$querybulan = $this->db->get('master_bulan');
			$querybulan = $querybulan->result_object();
			if($querybulan){
				foreach($querybulan as $rowsbulan){
					$datalabelnya = [
						"label" => $rowsbulan->code
					];
					
					array_push($datalabel,$datalabelnya);
				}
			}
			
			if($tipe == 'SALES'){
				$querytipe = array('Menunggu Validasi','Menunggu Take Down','Menunggu Verifikasi','Selesai');
			}else{
				$querytipe = array('Menunggu Validasi','Menunggu Take Down','Menunggu Verifikasi','Selesai');
			}
			
			if($querytipe){
				foreach($querytipe as $rowsdata){
					
					if($rowsdata == 'Menunggu Validasi'){
						$color = '#5E686D';
					}elseif($rowsdata == 'Menunggu Take Down'){
						$color = '#FF8000';
					}elseif($rowsdata == 'Menunggu Verifikasi'){
						$color = '#F3C623';
					}elseif($rowsdata == 'Selesai'){
						$color = '#64c2a6';
					}else{
						$color = '#000000';
					}
					
					$datavalue = [];
					$this->db->select('*');
					$querybulan = $this->db->get('master_bulan');
					$querybulan = $querybulan->result_object();
					if($querybulan){
						foreach($querybulan as $rowsbulan){
							$total_report = 0;
							if($rowsdata == 'Menunggu Validasi'){
								$dash='0';
								$this->db->select('count(vw_data_laporan_patrolisiber.laporan_no) as total_report, count(vw_data_laporan_patrolisiber.laporan_no) as total_data');
								$this->db->where('tahun', $tahun);
								$this->db->where('bulan',$rowsbulan->id);
								$this->db->where_in('role_id',array(4));
								$this->db->where_in('status_id',array(1,2));
								
								if($this->session->userdata('group_id') == 3){
									$this->db->where('ppmse_id',$this->session->userdata('ppmse_id'));
								}

								$query = $this->db->get('vw_data_laporan_patrolisiber');
								$query = $query->result_object();
								if($query){
									$values = $query[0]->total_data;
									$total_report = $query[0]->total_report;
								}else{
									$values = 0;
								}
							}elseif($rowsdata == 'Menunggu Take Down'){
								$dash='0';
								$this->db->select('count(vw_data_laporan_patrolisiber.laporan_no) as total_report, count(vw_data_laporan_patrolisiber.laporan_no) as total_data');
								$this->db->where('tahun', $tahun);
								$this->db->where('bulan',$rowsbulan->id);
								$this->db->where_in('role_id',array(3));
								$this->db->where_in('status_id',array(1,2));
								
								if($this->session->userdata('group_id') == 3){
									$this->db->where('ppmse_id',$this->session->userdata('ppmse_id'));
								}

								$query = $this->db->get('vw_data_laporan_patrolisiber');
								$query = $query->result_object();
								if($query){
									$values = $query[0]->total_data;
									$total_report = $query[0]->total_report;
								}else{
									$values = 0;
								}
							}elseif($rowsdata == 'Menunggu Verifikasi'){
								$dash='0';
								$this->db->select('count(vw_data_laporan_patrolisiber.laporan_no) as total_report, count(vw_data_laporan_patrolisiber.laporan_no) as total_data');
								$this->db->where('tahun', $tahun);
								$this->db->where('bulan',$rowsbulan->id);
								$this->db->where_in('role_id',array(5));
								$this->db->where_in('status_id',array(1,2));
								
								if($this->session->userdata('group_id') == 3){
									$this->db->where('ppmse_id',$this->session->userdata('ppmse_id'));
								}

								$query = $this->db->get('vw_data_laporan_patrolisiber');
								$query = $query->result_object();
								if($query){
									$values = $query[0]->total_data;
									$total_report = $query[0]->total_report;
								}else{
									$values = 0;
								}
							}else{
								$dash='0';
								$this->db->select('count(vw_data_laporan_patrolisiber.laporan_no) as total_report, count(vw_data_laporan_patrolisiber.laporan_no) as total_data');
								$this->db->where('tahun', $tahun);
								$this->db->where('bulan',$rowsbulan->id);
								$this->db->where_in('status_id',array(3));
								
								if($this->session->userdata('group_id') == 3){
									$this->db->where('ppmse_id',$this->session->userdata('ppmse_id'));
								}

								$query = $this->db->get('vw_data_laporan_patrolisiber');
								$query = $query->result_object();
								if($query){
									$values = $query[0]->total_data;
									$total_report = $query[0]->total_report;
								}else{
									$values = 0;
								}

							}
							
							$datalabelnya = [
								"dashed" => $dash,
								"allowDrag" => "0",
								"value" => (float)$values,
								"displayValue" => $rowsdata." | ".$total_report." Report", 								
							];
							
							array_push($datavalue,$datalabelnya);
						}
					}
			
					
					$dataisinya = [
						"seriesname" => $rowsdata,
						"color"=> $color,
						"anchorBgColor"=> $color,
						"allowDrag"=> "0",
						"data" => $datavalue 
					];

							
					array_push($dataisi,$dataisinya);
				}
			}else{
				$datalabelnya = [
					"label" => ''
				];
							
				$dataisinya = [
					"seriesname" => "Report Pengawasan",
					"color"=> "#feae65",
					"anchorBgColor"=> "#2d87bb",
					"allowDrag"=> "0",
					"data" => $datavalue 
				];
								
				array_push($dataisi,$dataisinya);
			}

			$jayParsedAry = [
			   "message" => "success", 
			   "data" => $datalabel,
			   "data5" => $dataisi, 
			   "total" => "149.57 M",
			   "csrf_hash" => $this->security->get_csrf_hash()
			]; 
			
			echo json_encode($jayParsedAry);
	
		}
		
	
	function projectbychannel(){
    $tahun = $this->input->post('tahun', true);
    $tipe = $this->input->post('project_tipe', true);
    $tipenya = $tipe . ' ' . $tahun;

    $datalabel = [];
    $dataisi   = [];

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

    // Loop bulan → ambil total laporan (tanpa status)
    $datavalue = [];
    foreach ($querybulan as $rowsbulan) {
        $this->db->select('COUNT(vw_data_laporan_daftar_hitam.laporan_no) as total_report');
        $this->db->where('vw_data_laporan_daftar_hitam.tahun', $tahun);
        $this->db->where('vw_data_laporan_daftar_hitam.bulan', $rowsbulan->id);

        $querydata = $this->db->get('vw_data_laporan_daftar_hitam')->row();
        $values = $querydata ? (int)$querydata->total_report : 0;

        $datalabelnya = [
            "displayValue" => $this->ortyd->custom_number_format((float)$values) . " Report",
            "value" => $values
        ];
        array_push($datavalue, $datalabelnya);
    }

    // Satu series saja (tanpa status)
    $dataisinya = [
        "seriesname" => "Laporan Daftar Hitam",
        "color" => "#000000",
        "data" => $datavalue
    ];
    array_push($dataisi, $dataisinya);

    $jayParsedAry = [
        "message" => "success",
        "data" => $datalabel,
        "data5" => $dataisi,
        "total" => "149.57 M",
        "csrf_hash" => $this->security->get_csrf_hash()
    ];
    echo json_encode($jayParsedAry);
}


	function projectbyfunnel() {
	
		
		echo '{
    "message": "success",
    "data": [
        {
            "label": "-",
            "displayValue": "Approval Pengajuan",
            "color": "#A02334",
            "value": 60
        },
        {
            "label": "Submit",
            "displayValue": "Submit | 956 Pengajuan",
            "color": "#A91D3A",
            "value": 50
        },
        {
            "label": "SPK",
            "displayValue": "SPK | 641 Pengajuan",
            "color": "#C73659",
            "value": 40
        },
        {
            "label": "Disposisi",
            "displayValue": "Disposisi | 536 Pengajuan",
            "color": "#D10363",
            "value": 30
        },
        {
            "label": "Pengukuran",
            "displayValue": "Pengukuran | 371 Pengajuan",
            "color": "#FF9A00",
            "value": 20
        },
        {
            "label": "Verifikasi",
            "displayValue": "Verifikasi | 316 Pengajuan",
            "color": "#FFBF00",
            "value": 10
        },
        {
            "label": "Selesai",
            "displayValue": "Selesai | 297 Pengajuan",
            "color": "#808836",
            "value": 1
        }
    ],
    "total": "1.37 rb"
}';

	}
	
	
	function projectbyam() {
		
		$tahun = $this->input->post('tahun',true);
		$tipe = $this->input->post('project_tipe',true);
		$tipenya = $tipe.' '.$tahun;
		
		$datalabel = [];
		$dataisi = [];
		$this->db->select('master_ppmse.name as ppmse_name, count(vw_data_laporan_patrolisiber.laporan_no) as total_report, count(vw_data_laporan_patrolisiber.laporan_no) as total_data');
		$this->db->where('tahun', $tahun);
		$this->db->join('master_ppmse','master_ppmse.id = vw_data_laporan_patrolisiber.ppmse_id');
		if($this->session->userdata('group_id') == 3){
			$this->db->where('vw_data_laporan_patrolisiber.ppmse_id',$this->session->userdata('ppmse_id'));
		}
		$this->db->group_by('master_ppmse.name,vw_data_laporan_patrolisiber.tahun');
		$this->db->order_by('total_data','DESC');
		$this->db->limit(10);
		$query = $this->db->get('vw_data_laporan_patrolisiber');
		$query = $query->result_object();
			//echo $this->db->last_query();

		if($query){
			foreach($query as $rowsdata){
					
					$datalabelnya = [
						"label" => ucwords(strtolower($rowsdata->ppmse_name ?? ''))
					];
					
					$dataisinya = [
						"displayValue" => $rowsdata->ppmse_name.' | '.$this->ortyd->custom_number_format((float)$rowsdata->total_data)." | ".$rowsdata->total_report.' Report',
						"value" => (float)$rowsdata->total_data
					];
					
					array_push($datalabel,$datalabelnya);
					array_push($dataisi,$dataisinya);
			}
		}else{
				$datalabelnya = [
					"label" => ''
				];
				
				$dataisinya = [
					"displayValue"  => '',
					"value"  => 0
				];
					
			array_push($datalabel,$datalabelnya);
			array_push($dataisi,$dataisinya);
		}
			
		$jayParsedAry = [
		   "message" => "success", 
		   "data" => $datalabel,
		   "data5" => [
				[
					"seriesname" => "", 
					"color" => "#FABC3F", 
					"data" => $dataisi
				] 
			], 
		   "total" => "149.57 M",
			"csrf_hash" => $this->security->get_csrf_hash() 
		]; 
		
		echo json_encode($jayParsedAry);

			
	}
	

function projectbyubis() {
    
    $tahun = $this->input->post('tahun',true);
    $tipe = $this->input->post('project_tipe',true);
    $tipenya = $tipe.' '.$tahun;
    
    $datafunnel = array();
    
    $group_id = $this->session->userdata('group_id');
    $unit_id = $this->session->userdata('unit_id');
    
    // Ambil data jenis pelanggaran dari master
    $this->db->select('id, name as nama_pelanggaran');
    $this->db->where('active', 1); // Hanya yang aktif
    $this->db->order_by('nama_pelanggaran', 'ASC');
    $query_master = $this->db->get('master_jenis_pelanggaran');
    $master_pelanggaran = $query_master->result_object();
    
    // Warna untuk funnel chart
    $funnel_colors = array('#FF8000','#5E686D','#F3C623','#64c2a6','#2E8B57','#4682B4','#DC143C','#9932CC','#FF1493','#00CED1');
    
    $x = 0;
    foreach ($master_pelanggaran as $pelanggaran) {
        // Query untuk menghitung laporan yang mengandung jenis pelanggaran ini
        $this->db->select('count(vw_data_laporan_pengawasan.laporan_no) as total_report, count(vw_data_laporan_pengawasan.laporan_no) as total_data');
        
        // Menggunakan FIND_IN_SET atau LIKE untuk mencari dalam field yang dipisahkan koma
        $this->db->where("(FIND_IN_SET('{$pelanggaran->nama_pelanggaran}', REPLACE(jenis_pelanggaran, ' ', '')) > 0 
                          OR jenis_pelanggaran LIKE '%{$pelanggaran->nama_pelanggaran}%')", NULL, FALSE);
       
        
        // Filter berdasarkan tahun
        //$this->db->where('tahun', $tahun);
        
        // Filter hanya yang dipublikasi (status_id = 4)
        //$this->db->where('status_id', 4);
        
        $query = $this->db->get('vw_data_laporan_pengawasan');
        $result = $query->row();
        
        // Hanya tambahkan ke funnel jika ada data
        if($result && $result->total_data > 0) {
            $datafunnelnya = [
                "label" => $pelanggaran->nama_pelanggaran, 
                "displayValue" => $pelanggaran->nama_pelanggaran." ".$this->ortyd->custom_number_format((float)$result->total_data)." Report", 
                "color" => isset($funnel_colors[$x]) ? $funnel_colors[$x] : $funnel_colors[$x % count($funnel_colors)],
                "value" => (float)$result->total_data
            ];
            
            array_push($datafunnel, $datafunnelnya);
            $x++;
        }
    }
    
    // Jika tidak ada data sama sekali, tambahkan default
    if(empty($datafunnel)) {
        $datafunnelnya = [
            "label" => "Tidak Ada Data", 
            "displayValue" => "Tidak Ada Data 0 Report", 
            "color" => "#cccccc",
            "value" => 0
        ];
        array_push($datafunnel, $datafunnelnya);
    }
    
    // Hitung total keseluruhan
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

	function projectbyportfolio() {
		
		
			echo '{
    "message": "success",
    "data": [
        {
            "label": "Jan"
        },
        {
            "label": "Feb"
        },
        {
            "label": "Mar"
        },
        {
            "label": "Apr"
        },
        {
            "label": "May"
        },
        {
            "label": "Jun"
        },
        {
            "label": "Jul"
        },
        {
            "label": "Aug"
        },
        {
            "label": "Sep"
        },
        {
            "label": "Oct"
        },
        {
            "label": "Nov"
        },
        {
            "label": "Dec"
        }
    ],
    "data5": [
        {
            "seriesname": "CPE INTEGRATOR",
            "color": "#feae65",
            "data": [
                {
                    "displayValue": "CPE INTEGRATOR | 0 | 0",
                    "value": null
                },
                {
                    "displayValue": "CPE INTEGRATOR | 0 | 0",
                    "value": null
                },
                {
                    "displayValue": "CPE INTEGRATOR | 0 | 0",
                    "value": null
                },
                {
                    "displayValue": "CPE INTEGRATOR | 0 | 0",
                    "value": null
                },
                {
                    "displayValue": "CPE INTEGRATOR | 0 | 0",
                    "value": null
                },
                {
                    "displayValue": "CPE INTEGRATOR | 2.74 M | 3",
                    "value": "2735619400"
                },
                {
                    "displayValue": "CPE INTEGRATOR | 270.00 jt | 2",
                    "value": "270000000"
                },
                {
                    "displayValue": "CPE INTEGRATOR | 43.16 M | 8",
                    "value": "43158975933"
                },
                {
                    "displayValue": "CPE INTEGRATOR | 4.65 M | 6",
                    "value": "4650000000"
                },
                {
                    "displayValue": "CPE INTEGRATOR | 17.68 M | 3",
                    "value": "17675908030"
                },
                {
                    "displayValue": "CPE INTEGRATOR | 0 | 0",
                    "value": null
                },
                {
                    "displayValue": "CPE INTEGRATOR | 0 | 0",
                    "value": null
                }
            ]
        },
        {
            "seriesname": "PROFESSIONAL SERVICES",
            "color": "#f66d44",
            "data": [
                {
                    "displayValue": "PROFESSIONAL SERVICES | 6.01 M | 2",
                    "value": "6012500000"
                },
                {
                    "displayValue": "PROFESSIONAL SERVICES | 119.10 jt | 2",
                    "value": "119100000"
                },
                {
                    "displayValue": "PROFESSIONAL SERVICES | 270.33 jt | 1",
                    "value": "270325569"
                },
                {
                    "displayValue": "PROFESSIONAL SERVICES | 414.70 jt | 3",
                    "value": "414695000"
                },
                {
                    "displayValue": "PROFESSIONAL SERVICES | 0 | 0",
                    "value": null
                },
                {
                    "displayValue": "PROFESSIONAL SERVICES | 407.74 jt | 1",
                    "value": "407738738"
                },
                {
                    "displayValue": "PROFESSIONAL SERVICES | 0 | 0",
                    "value": null
                },
                {
                    "displayValue": "PROFESSIONAL SERVICES | 10.00 M | 1",
                    "value": "10000000000"
                },
                {
                    "displayValue": "PROFESSIONAL SERVICES | 2.57 M | 3",
                    "value": "2565000000"
                },
                {
                    "displayValue": "PROFESSIONAL SERVICES | 0 | 0",
                    "value": null
                },
                {
                    "displayValue": "PROFESSIONAL SERVICES | 0 | 0",
                    "value": null
                },
                {
                    "displayValue": "PROFESSIONAL SERVICES | 4.00 M | 1",
                    "value": "4000000000"
                }
            ]
        },
        {
            "seriesname": "CPE Services",
            "color": "#e6f69d",
            "data": [
                {
                    "displayValue": "CPE Services | 91.08 M | 18",
                    "value": "91084553765"
                },
                {
                    "displayValue": "CPE Services | 111.40 M | 16",
                    "value": "111399631679"
                },
                {
                    "displayValue": "CPE Services | 302.82 M | 56",
                    "value": "302818773234"
                },
                {
                    "displayValue": "CPE Services | 519.11 M | 74",
                    "value": "519110342878"
                },
                {
                    "displayValue": "CPE Services | 75.75 M | 46",
                    "value": "75754897750"
                },
                {
                    "displayValue": "CPE Services | 59.88 M | 56",
                    "value": "59879240954"
                },
                {
                    "displayValue": "CPE Services | 172.44 M | 56",
                    "value": "172443790158"
                },
                {
                    "displayValue": "CPE Services | 247.49 M | 95",
                    "value": "247489988639"
                },
                {
                    "displayValue": "CPE Services | 483.05 M | 87",
                    "value": "483054223550"
                },
                {
                    "displayValue": "CPE Services | 472.11 M | 202",
                    "value": "472111229395"
                },
                {
                    "displayValue": "CPE Services | 222.79 M | 177",
                    "value": "222785689934"
                },
                {
                    "displayValue": "CPE Services | 33.30 M | 5",
                    "value": "33301500000"
                }
            ]
        },
        {
            "seriesname": "IoT Solutions",
            "color": "#64c2a6",
            "data": [
                {
                    "displayValue": "IoT Solutions | 1.40 M | 2",
                    "value": "1400000000"
                },
                {
                    "displayValue": "IoT Solutions | 250.00 jt | 1",
                    "value": "250000000"
                },
                {
                    "displayValue": "IoT Solutions | 0 | 0",
                    "value": null
                },
                {
                    "displayValue": "IoT Solutions | 1.00 M | 1",
                    "value": "1000000000"
                },
                {
                    "displayValue": "IoT Solutions | 0 | 0",
                    "value": null
                },
                {
                    "displayValue": "IoT Solutions | 80.00 jt | 1",
                    "value": "80000000"
                },
                {
                    "displayValue": "IoT Solutions | 0 | 0",
                    "value": null
                },
                {
                    "displayValue": "IoT Solutions | 52.70 jt | 1",
                    "value": "52700000"
                },
                {
                    "displayValue": "IoT Solutions | 150.20 jt | 3",
                    "value": "150200000"
                },
                {
                    "displayValue": "IoT Solutions | 0 | 0",
                    "value": null
                },
                {
                    "displayValue": "IoT Solutions | 0 | 0",
                    "value": null
                },
                {
                    "displayValue": "IoT Solutions | 0 | 0",
                    "value": null
                }
            ]
        },
        {
            "seriesname": "Seat Management",
            "color": "#ff01111",
            "data": [
                {
                    "displayValue": "Seat Management | 0 | 0",
                    "value": null
                },
                {
                    "displayValue": "Seat Management | 0 | 0",
                    "value": null
                },
                {
                    "displayValue": "Seat Management | 26.80 jt | 1",
                    "value": "26801802"
                },
                {
                    "displayValue": "Seat Management | 1.79 M | 1",
                    "value": "1785000000"
                },
                {
                    "displayValue": "Seat Management | 8.23 M | 8",
                    "value": "8229661350"
                },
                {
                    "displayValue": "Seat Management | 4.96 M | 7",
                    "value": "4957078738"
                },
                {
                    "displayValue": "Seat Management | 2.16 M | 8",
                    "value": "2156370270"
                },
                {
                    "displayValue": "Seat Management | 4.61 M | 8",
                    "value": "4608199600"
                },
                {
                    "displayValue": "Seat Management | 2.75 M | 4",
                    "value": "2745000000"
                },
                {
                    "displayValue": "Seat Management | 195.00 jt | 1",
                    "value": "195000000"
                },
                {
                    "displayValue": "Seat Management | 0 | 0",
                    "value": null
                },
                {
                    "displayValue": "Seat Management | 0 | 0",
                    "value": null
                }
            ]
        }
    ],
    "total": "149.57 M"
}';
	}
	
	function projectbyrecuring() {
		
		
			echo '{
    "message": "success",
    "data": [
        {
            "label": "Jan"
        },
        {
            "label": "Feb"
        },
        {
            "label": "Mar"
        },
        {
            "label": "Apr"
        },
        {
            "label": "May"
        },
        {
            "label": "Jun"
        },
        {
            "label": "Jul"
        },
        {
            "label": "Aug"
        },
        {
            "label": "Sep"
        },
        {
            "label": "Oct"
        },
        {
            "label": "Nov"
        },
        {
            "label": "Dec"
        }
    ],
    "data5": [
        {
            "seriesname": "OTC",
            "color": null,
            "data": [
                {
                    "displayValue": "OTC | 400.00 jt | 1",
                    "value": "400000000"
                },
                {
                    "displayValue": "OTC | 0 | 0",
                    "value": null
                },
                {
                    "displayValue": "OTC | 0 | 0",
                    "value": null
                },
                {
                    "displayValue": "OTC | 2.80 M | 2",
                    "value": "2804010420"
                },
                {
                    "displayValue": "OTC | 0 | 0",
                    "value": null
                },
                {
                    "displayValue": "OTC | 487.74 jt | 2",
                    "value": "487738738"
                },
                {
                    "displayValue": "OTC | 273.48 jt | 2",
                    "value": "273477477"
                },
                {
                    "displayValue": "OTC | 13.40 M | 9",
                    "value": "13398480000"
                },
                {
                    "displayValue": "OTC | 125.85 M | 45",
                    "value": "125848296665"
                },
                {
                    "displayValue": "OTC | 58.93 M | 34",
                    "value": "58926783294"
                },
                {
                    "displayValue": "OTC | 23.91 M | 19",
                    "value": "23910775100"
                },
                {
                    "displayValue": "OTC | 29.00 M | 3",
                    "value": "29001500000"
                }
            ]
        },
        {
            "seriesname": "Recurring/Termin 1 Thn",
            "color": null,
            "data": [
                {
                    "displayValue": "Recurring/Termin 1 Thn | 98.10 M | 21",
                    "value": "98097053765"
                },
                {
                    "displayValue": "Recurring/Termin 1 Thn | 61.26 M | 18",
                    "value": "61258389000"
                },
                {
                    "displayValue": "Recurring/Termin 1 Thn | 2.76 M | 12",
                    "value": "2764566164"
                },
                {
                    "displayValue": "Recurring/Termin 1 Thn | 166.01 M | 16",
                    "value": "166013305991"
                },
                {
                    "displayValue": "Recurring/Termin 1 Thn | 63.98 M | 33",
                    "value": "63979095625"
                },
                {
                    "displayValue": "Recurring/Termin 1 Thn | 26.85 M | 30",
                    "value": "26846330325"
                },
                {
                    "displayValue": "Recurring/Termin 1 Thn | 70.23 M | 32",
                    "value": "70225616586"
                },
                {
                    "displayValue": "Recurring/Termin 1 Thn | 73.50 M | 31",
                    "value": "73499608933"
                },
                {
                    "displayValue": "Recurring/Termin 1 Thn | 98.92 M | 19",
                    "value": "98916622207"
                },
                {
                    "displayValue": "Recurring/Termin 1 Thn | 78.24 M | 18",
                    "value": "78240908030"
                },
                {
                    "displayValue": "Recurring/Termin 1 Thn | 8.24 M | 9",
                    "value": "8235180000"
                },
                {
                    "displayValue": "Recurring/Termin 1 Thn | 8.30 M | 3",
                    "value": "8300000000"
                }
            ]
        },
        {
            "seriesname": "Recurring/Termin 2 Thn",
            "color": null,
            "data": [
                {
                    "displayValue": "Recurring/Termin 2 Thn | 0 | 0",
                    "value": null
                },
                {
                    "displayValue": "Recurring/Termin 2 Thn | 0 | 0",
                    "value": null
                },
                {
                    "displayValue": "Recurring/Termin 2 Thn | 0 | 0",
                    "value": null
                },
                {
                    "displayValue": "Recurring/Termin 2 Thn | 0 | 0",
                    "value": null
                },
                {
                    "displayValue": "Recurring/Termin 2 Thn | 0 | 0",
                    "value": null
                },
                {
                    "displayValue": "Recurring/Termin 2 Thn | 0 | 0",
                    "value": null
                },
                {
                    "displayValue": "Recurring/Termin 2 Thn | 0 | 0",
                    "value": null
                },
                {
                    "displayValue": "Recurring/Termin 2 Thn | 0 | 0",
                    "value": null
                },
                {
                    "displayValue": "Recurring/Termin 2 Thn | 0 | 0",
                    "value": null
                },
                {
                    "displayValue": "Recurring/Termin 2 Thn | 346.22 jt | 1",
                    "value": "346215000"
                },
                {
                    "displayValue": "Recurring/Termin 2 Thn | 5.00 M | 1",
                    "value": "5000000000"
                },
                {
                    "displayValue": "Recurring/Termin 2 Thn | 0 | 0",
                    "value": null
                }
            ]
        },
        {
            "seriesname": "Recurring/Termin 3 Thn",
            "color": null,
            "data": [
                {
                    "displayValue": "Recurring/Termin 3 Thn | 0 | 0",
                    "value": null
                },
                {
                    "displayValue": "Recurring/Termin 3 Thn | 0 | 0",
                    "value": null
                },
                {
                    "displayValue": "Recurring/Termin 3 Thn | 0 | 0",
                    "value": null
                },
                {
                    "displayValue": "Recurring/Termin 3 Thn | 0 | 0",
                    "value": null
                },
                {
                    "displayValue": "Recurring/Termin 3 Thn | 0 | 0",
                    "value": null
                },
                {
                    "displayValue": "Recurring/Termin 3 Thn | 628.50 jt | 2",
                    "value": "628500000"
                },
                {
                    "displayValue": "Recurring/Termin 3 Thn | 0 | 0",
                    "value": null
                },
                {
                    "displayValue": "Recurring/Termin 3 Thn | 0 | 0",
                    "value": null
                },
                {
                    "displayValue": "Recurring/Termin 3 Thn | 0 | 0",
                    "value": null
                },
                {
                    "displayValue": "Recurring/Termin 3 Thn | 27.52 M | 4",
                    "value": "27516440000"
                },
                {
                    "displayValue": "Recurring/Termin 3 Thn | 12.68 M | 5",
                    "value": "12678279200"
                },
                {
                    "displayValue": "Recurring/Termin 3 Thn | 0 | 0",
                    "value": null
                }
            ]
        }
    ],
    "total": "149.57 M"
}';
	}
		
	function projectbysustain() {
		    $tahun = $this->input->post('tahun', true);
    $tipe = $this->input->post('project_tipe', true);
    $tipenya = $tipe . ' ' . $tahun;

    $datalabel = [];
    $dataisi   = [];

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

    // Loop bulan → ambil total laporan (tanpa status)
    $datavalue = [];
    foreach ($querybulan as $rowsbulan) {
        $this->db->select('COUNT(vw_data_laporan_daftar_prioritas.laporan_no) as total_report');
        $this->db->where('vw_data_laporan_daftar_prioritas.tahun', $tahun);
        $this->db->where('vw_data_laporan_daftar_prioritas.bulan', $rowsbulan->id);

        $querydata = $this->db->get('vw_data_laporan_daftar_prioritas')->row();
        $values = $querydata ? (int)$querydata->total_report : 0;

        $datalabelnya = [
            "displayValue" => $this->ortyd->custom_number_format((float)$values) . " Report",
            "value" => $values
        ];
        array_push($datavalue, $datalabelnya);
    }

    // Satu series saja (tanpa status)
    $dataisinya = [
        "seriesname" => "Laporan Prioritas Pengawasan",
        "color" => "#008000",
        "data" => $datavalue
    ];
    array_push($dataisi, $dataisinya);

    $jayParsedAry = [
        "message" => "success",
        "data" => $datalabel,
        "data5" => $dataisi,
        "total" => "149.57 M",
        "csrf_hash" => $this->security->get_csrf_hash()
    ];
    echo json_encode($jayParsedAry);
	
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
					 ->where('active', 1)
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
