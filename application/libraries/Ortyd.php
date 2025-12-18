<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use NcJoes\OfficeConverter\OfficeConverter;
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfReader;
use Spatie\Browsershot\Browsershot;
use mikehaertl\wkhtmlto\Pdf;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;

use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Document\Properties;

#[\AllowDynamicProperties]
class Ortyd {

	const ALLOWED_ALGOS = ['default' => PASSWORD_DEFAULT, 'bcrypt' => PASSWORD_BCRYPT];
	protected $_cost = 10;
	protected $_algo = PASSWORD_DEFAULT;
	public $link_api = api_url.'api/';
	public $linkgateway_dev = api_url.'api/';
	
	public function __construct() {
		$this->CI =& get_instance();
		$this->CI->load->helper('shorten_encryption');
		$this->CI->load->library('rabbitmq');
		$this->CI->load->model('m_api_notif');
		//$this->CI->load->model('m_api_history');
		//if(function_exists('mcrypt_encrypt') === false){
			//throw new Exception('The Password library requires the Mcrypt extension.');
		//}
	}
	
	public function setMenu($module){
		
		$CI =& get_instance();
		$gid = $CI->session->userdata('group_id');
		$menu_id = 0;
		
		
		if($gid == 1 || $gid == 2 || $gid == 11 || $module == 'data_bantuan' || $module == 'users_profile' || $module == 'users_password' || $module == 'dashboard' || $module == 'data_inbox' || $module == 'data_ticket'){
			$CI->db->select('master_menu.*');
			$CI->db->where('master_menu.module', $module);
			$query = $CI->db->get('master_menu');
			$query = $query->result_object();
			if(!$query){
				$datadetail = array(
					'name' 				=> $module,
					'description' 		=> $module,
					'module' 			=> $module,
					'url' 				=> $module,
					'slug' 				=> $this->sanitize($module,'master_menu'),
					'icon'				=> 'fa fa-database',
					'parent_id' 		=> 6,
					'sort' 				=> 99,
					'show' 				=> 1,
					'createdid'			=> 1,
					'created'			=> date('Y-m-d H:i:s'),
					'modifiedid'		=> 1,
					'modified'			=> date('Y-m-d H:i:s'),
					'active'			=> 1
				);
				
				$insert = $CI->db->insert('master_menu', $datadetail);	
				$insertid = $CI->db->insert_id();
				if($insert){
					$CI->db->select('users_groups_access.*');
					$CI->db->where('users_groups_access.gid', $gid);
					$CI->db->where('users_groups_access.menu_id', $insertid);
					$queryaccess = $CI->db->get('users_groups_access');
					$queryaccess = $queryaccess->result_object();
					if(!$queryaccess){
						$datadetail = array(
							'gid' 				=> $gid,
							'menu_id' 			=> $insertid,
							'view' 				=> 1,
							'update' 			=> 1,
							'delete'			=> 1,
							'insert' 			=> 1,
							'createdid'			=> 1,
							'created'			=> date('Y-m-d H:i:s'),
							'modifiedid'		=> 1,
							'modified'			=> date('Y-m-d H:i:s'),
							'active'			=> 1
						);
						
						$insert = $CI->db->insert('users_groups_access', $datadetail);	
						$insertid = $CI->db->insert_id();
					}else{
						$datadetail = array(
							'gid' 				=> $query[0]->gid,
							'menu_id' 			=> $query[0]->menu_id,
							'view' 				=> 1,
							'update' 			=> 1,
							'delete'			=> 1,
							'insert' 			=> 1,
							'createdid'			=> 1,
							'created'			=> date('Y-m-d H:i:s'),
							'modifiedid'		=> 1,
							'modified'			=> date('Y-m-d H:i:s'),
							'active'			=> 1
						);
						
						$CI->db->where('users_groups_access.gid', $gid);
						$CI->db->where('users_groups_access.menu_id', $insertid);
						$insert = $CI->db->update('users_groups_access', $datadetail);	
						$insertid = $query[0]->id;
					}
				}
			}else{
				$CI->db->select('users_groups_access.*');
				$CI->db->where('users_groups_access.gid', $gid);
				$CI->db->where('users_groups_access.menu_id', $query[0]->id);
				$queryaccess = $CI->db->get('users_groups_access');
				$queryaccess = $queryaccess->result_object();
				if(!$queryaccess){
						$datadetail = array(
							'gid' 				=> $gid,
							'menu_id' 			=> $query[0]->id,
							'view' 				=> 1,
							'update' 			=> 1,
							'delete'			=> 1,
							'insert' 			=> 1,
							'createdid'			=> 1,
							'created'			=> date('Y-m-d H:i:s'),
							'modifiedid'		=> 1,
							'modified'			=> date('Y-m-d H:i:s'),
							'active'			=> 1
						);
						
						$insert = $CI->db->insert('users_groups_access', $datadetail);	
						$insertid = $CI->db->insert_id();
				}else{
					$datadetail = array(
						'gid' 				=> $queryaccess[0]->gid,
						'menu_id' 			=> $queryaccess[0]->menu_id,
						'view' 				=> 1,
						'update' 			=> 1,
						'delete'			=> 1,
						'insert' 			=> 1,
						'createdid'			=> 1,
						'created'			=> date('Y-m-d H:i:s'),
						'modifiedid'		=> 1,
						'modified'			=> date('Y-m-d H:i:s'),
						'active'			=> 1
					);
						
					$CI->db->where('users_groups_access.id', $queryaccess[0]->id);
					$insert = $CI->db->update('users_groups_access', $datadetail);	
					$insertid = $queryaccess[0]->id;
				}
			}
		}else{
			
			
			$CI->db->select('master_menu.*');
			$CI->db->where('master_menu.module', $module);
			$querymenu = $CI->db->get('master_menu');
			$querymenu = $querymenu->result_object();
			if($querymenu){
				$menu_id = $querymenu[0]->id;	
			}
			
			$CI->db->select('users_groups_role.*');
			$CI->db->where('users_groups_role.parent_id', $menu_id);
			$querymenu = $CI->db->get('users_groups_role');
			$querymenu = $querymenu->result_object();
			if($querymenu){
				$menu_id = $querymenu[0]->group_id;	
			}
			
			$CI->db->select('users_groups_access.*');
			$CI->db->where('users_groups_access.menu_id', $menu_id);
			$CI->db->where('users_groups_access.gid', $gid);
			$queryacc = $CI->db->get('users_groups_access');
			$queryacc = $queryacc->result_object();
			if($queryacc){
				$CI->db->select('users_groups_role.*');
				$CI->db->where('users_groups_role.group_id', $menu_id);
				$queryrole = $CI->db->get('users_groups_role');
				$queryrole = $queryrole->result_object();
				if($queryrole){
					foreach($queryrole as $rolemenu){
						
						$CI->db->select('master_menu.*');
						$CI->db->where('master_menu.id', $rolemenu->parent_id);
						$query = $CI->db->get('master_menu');
						$query = $query->result_object();
						if($query){
							$CI->db->select('users_groups_access.*');
							$CI->db->where('users_groups_access.gid', $gid);
							$CI->db->where('users_groups_access.menu_id', $rolemenu->parent_id);
							$queryaccess = $CI->db->get('users_groups_access');
							$queryaccess = $queryaccess->result_object();
							if(!$queryaccess){
									$datadetail = array(
										'gid' 				=> $gid,
										'menu_id' 			=> $rolemenu->parent_id,
										'view' 				=> $queryacc[0]->view,
										'update' 			=> $queryacc[0]->update,
										'delete'			=> $queryacc[0]->delete,
										'insert' 			=> $queryacc[0]->insert,
										'createdid'			=> 1,
										'created'			=> date('Y-m-d H:i:s'),
										'modifiedid'		=> 1,
										'modified'			=> date('Y-m-d H:i:s'),
										'active'			=> 1
									);
									
									$insert = $CI->db->insert('users_groups_access', $datadetail);	
									$insertid = $CI->db->insert_id();
							}else{
								$datadetail = array(
									'gid' 				=> $queryaccess[0]->gid,
									'menu_id' 			=> $rolemenu->parent_id,
									'view' 				=> $queryacc[0]->view,
									'update' 			=> $queryacc[0]->update,
									'delete'			=> $queryacc[0]->delete,
									'insert' 			=> $queryacc[0]->insert,
									'created'			=> date('Y-m-d H:i:s'),
									'modifiedid'		=> 1,
									'modified'			=> date('Y-m-d H:i:s'),
									'active'			=> 1
								);
									
								$CI->db->where('users_groups_access.id', $queryaccess[0]->id);
								$insert = $CI->db->update('users_groups_access', $datadetail);	
								$insertid = $queryaccess[0]->id;
							}
						}
					}
				}
			}
		}
	}
	
	function ip_address() 
    {
		
		$this->CI->load->helper('cookie');
		$cookieData = $this->CI->input->cookie("ortyd_session_data_kadi");
		//echo $cookieData;

		return $cookieData;

    }
	
	public function getHistoryAkses($type = 1, $id_data = null){
		
		$CI =& get_instance();
		$userid = $CI->session->userdata('userid');
		if($userid == ''){
			$userid = 0;
		}
		
		$currentURL = current_url(); //http://myhost/main

		if(isset($_SERVER['QUERY_STRING'])){
			$params   = $_SERVER['QUERY_STRING']; //my_id=1,3
			$url = $currentURL . '?' . $params; 
		}else{
			$url = $currentURL;
		}
		
		$ip = $this->ip_address();
		
		if($ip != '' && $ip != null){
			$datenow = "'".date('Y-m-d')."'";
			$CI->db->select('data_history.*');
			$CI->db->where('ip', $ip);
			$CI->db->where('link', $url);
			$CI->db->where('CAST(date AS date) = '.$datenow,null );
			$CI->db->order_by('modified','DESC');
			$query = $CI->db->get('data_history');
			$query = $query->result_object();
			if($query){
				if($query[0]->visit_count == ''){
					$visit_count = 1;
				}else{
					$visit_count = (int)$query[0]->visit_count + 1;
				}
				
				$datadetail = array(
					'modifiedid'		=> $userid,
					'modified'			=> date('Y-m-d H:i:s'),
					'last_access'		=> date('Y-m-d H:i:s'),
					'visit_count'		=> $visit_count
				);
				
				$CI->db->where('ip', $ip);
				$CI->db->where('link', $url);
				$CI->db->where('CAST(date AS date) = '.$datenow,null );
				$insert = $CI->db->update('data_history', $datadetail);	
			
				return false;
			}
			
			
			//$ip = $_SERVER['REMOTE_ADDR'];
			//$details = json_decode(file_get_contents("http://ipinfo.io/{$ip}/json"));
			//if($details){
				//$city = $details->city;
				//$country = $details->country;
			//}else{
				//$city =null;
				//$country = null;
			//}
			
			$city =null;
			$country = null;
			

			$datadetail = array(
				'ip' 				=> $ip,
				'link' 				=> $url,
				'type' 				=> $type,
				'active' 			=> 1,
				'createdid'			=> $userid,
				'date' 				=> date('Y-m-d H:i:s'),
				'created'			=> date('Y-m-d H:i:s'),
				'modifiedid'		=> $userid,
				'modified'			=> date('Y-m-d H:i:s'),
				'last_access'		=> date('Y-m-d H:i:s'),
				'visit_count'		=> 1,
				'country'			=> $country,
				'city'				=> $city,
				'id_data'			=> $id_data
			);
			

													
			$insert = $CI->db->insert('data_history', $datadetail);	
		}

		return false;
	}
	
	
	function translate_google($text){
		
		return $text;
		die();
		
		$this->CI->load->helper('google_translate');
		if($this->CI->session->userdata('lang') != ''){
			if($this->CI->session->userdata('lang') != 'id'){
				$source = 'id';
				$target = $this->CI->session->userdata('lang');
				
				$databasetranlate = $this->getTranslate($text,$source,$target);
				if($databasetranlate != false){
					return $databasetranlate;
				}else{
					$translate = google_translate($text,$target,$source);
					return $this->saveTranslate($text,$source,$target,$translate);
				}
				
			}else{
				return $text;
			}
		}else{
			return $text;
		}
	}	
	
	
		
	function bacaHTML($url){
			 // inisialisasi CURL
			 $data = curl_init();
			 // setting CURL
			 curl_setopt($data, CURLOPT_RETURNTRANSFER, 1);
			 curl_setopt($data, CURLOPT_URL, $url);
			 // menjalankan CURL untuk membaca isi file
			 $hasil = curl_exec($data);
			 curl_close($data);
			 return $hasil;
		}


	public function getMeta($meta_id){
		
		$tablename = 'meta_value';
		$tableid = 'meta_id';
		$id= $meta_id;
		$table = 'master_setting';
		
		$CI =& get_instance();
		$tablenameas = $tablename.' as name';
		$CI->db->select($tablenameas);
		$CI->db->where($tableid, $id);
		$CI->db->order_by('modified','DESC');
		$query = $CI->db->get($table);
		$query = $query->result_object();
		if($query){
			return $query[0]->name;
		}else{
			return '-';
		}
	}
	
	public function getMeta_cover($meta_id){
		
		$tablename = 'cover';
		$tableid = 'meta_id';
		$id= $meta_id;
		$table = 'master_setting';
		
		$CI =& get_instance();
		$tablenameas = $tablename.' as name';
		$CI->db->select($tablenameas);
		$CI->db->where($tableid, $id);
		$CI->db->order_by('modified','DESC');
		$query = $CI->db->get($table);
		$query = $query->result_object();
		if($query){
			return $query[0]->name;
		}else{
			return '-';
		}
	}
	
	public function query_column_include($table, $exclude_new) {
		
		
		if(dbconnection == 'postgre'){
			
			$exclude = array();
			if(count($exclude_new) > 0){
				$exclude = array_merge($exclude, $exclude_new);
			}
			
			$exclude_column = '';
			if(count($exclude) > 0){
				foreach ($exclude as $value) {
					$value = "'".$value."'";
					if($exclude_column != ''){
						$exclude_column = $exclude_column.','.$value;
					}else{
						$exclude_column = $value;
					}
				}
				$exclude_column = '('.$exclude_column.')';
				$wherein = ' AND cols.column_name in '.$exclude_column.' ';
			}else{
				$wherein = ' ';
			}

			$CI =& get_instance();
			$query_column = $CI->db->query("SELECT
					cols.column_name as name, 
					cols.column_name as id, 
					cols.character_octet_length as numbernya, 
					cols.data_type as type, cols.is_nullable as is_nullable,
					cols.numeric_precision as isnumber,
					(
						SELECT
							pg_catalog.col_description(c.oid, cols.ordinal_position::int)
						FROM
							pg_catalog.pg_class c
						WHERE
							c.oid = (SELECT (cols.table_name)::regclass::oid)
							AND c.relname = cols.table_name
					) AS column_comment
				FROM
					INFORMATION_SCHEMA.COLUMNS cols
				WHERE
					cols.TABLE_SCHEMA = 'public' 
					AND cols.TABLE_NAME = '".$table."'
					".$wherein."
				ORDER BY (
					SELECT
						pg_catalog.col_description(c.oid, cols.ordinal_position::int)
					FROM
						pg_catalog.pg_class c
					WHERE
						c.oid = (SELECT (cols.table_name)::regclass::oid)
						AND c.relname = cols.table_name
				) ASC
			");
			$query_column = $query_column->result_array();
			if(count($query_column) > 0){
				return $query_column;
			}else{
				return null;
			}
		
		}else{
			
			$exclude = array();
			if(count($exclude_new) > 0){
				$exclude = array_merge($exclude, $exclude_new);
			}
			
			$exclude_column = '';
			if(count($exclude) > 0){
				foreach ($exclude as $value) {
					$value = "'".$value."'";
					if($exclude_column != ''){
						$exclude_column = $exclude_column.','.$value;
					}else{
						$exclude_column = $value;
					}
				}
				$exclude_column = '('.$exclude_column.')';
				$wherein = ' AND column_name in '.$exclude_column.' ';
			}else{
				$wherein = ' ';
			}

			$CI =& get_instance();
			$query_column = $CI->db->query("SELECT
					column_name as name, column_name as id, character_octet_length as numbernya, data_type as type, is_nullable as is_nullable,numeric_precision as isnumber
				FROM
					INFORMATION_SCHEMA.COLUMNS 
				WHERE
					TABLE_SCHEMA = '".$CI->db->database."' 
					AND TABLE_NAME = '".$table."'
					".$wherein."
				ORDER BY column_comment * 1 ASC
			");
			$query_column = $query_column->result_array();
			if(count($query_column) > 0){
				return $query_column;
			}else{
				return null;
			}
		
		}
		
		
		
	}
	
	public function query_column_filter($table, $exclude_new, $ordernya = null) {
		
		
		if(dbconnection == 'postgre'){
			$exclude = array();
			if(count($exclude_new) > 0){
				$exclude = array_merge($exclude, $exclude_new);
			}
			
			$exclude_column = '';
			if(count($exclude) > 0){
				foreach ($exclude as $value) {
					$value = "'".$value."'";
					if($exclude_column != ''){
						$exclude_column = $exclude_column.','.$value;
					}else{
						$exclude_column = $value;
					}
				}
				$exclude_column = '('.$exclude_column.')';
				$wherein = ' AND cols.column_name in '.$exclude_column.' ';
			}else{
				$wherein = ' ';
			}
			
			if($ordernya == null){
				$orderdata = " ORDER BY (
					SELECT
						pg_catalog.col_description(c.oid, cols.ordinal_position::int)
					FROM
						pg_catalog.pg_class c
					WHERE
						c.oid = (SELECT (cols.table_name)::regclass::oid)
						AND c.relname = cols.table_name
				) ASC ";
			}else{
				$ordernya_list = '';
				$tags = explode(',',$ordernya);
				$xx=0;
				
				$stringwhen = " ";
				foreach($tags as $key) {
					if($xx==0){
						$ordernya_list = "'".$key."'";  
						$stringwhen = " WHEN cols.column_name='".$key."' THEN ".$xx."";
					}else{
						$ordernya_list = $ordernya_list.','."'".$key."'";
						$stringwhen = $stringwhen." WHEN cols.column_name='".$key."' THEN ".$xx."";						
					}
					
					$xx++;
				}

				$orderdata = " ORDER BY CASE " .$stringwhen. " ELSE ".$xx." END ASC ";
			}

			$CI =& get_instance();
			$query_column = $CI->db->query("SELECT
					cols.column_name as name, 
					cols.column_name as id, 
					cols.character_octet_length as numbernya, 
					cols.data_type as type, cols.is_nullable as is_nullable,
					cols.numeric_precision as isnumber,
					(
						SELECT
							pg_catalog.col_description(c.oid, cols.ordinal_position::int)
						FROM
							pg_catalog.pg_class c
						WHERE
							c.oid = (SELECT (cols.table_name)::regclass::oid)
							AND c.relname = cols.table_name
					) AS column_comment
				FROM
					INFORMATION_SCHEMA.COLUMNS cols
				WHERE
					cols.TABLE_SCHEMA = 'public' 
					AND cols.TABLE_NAME = '".$table."'
					".$wherein."
					".$orderdata."
			");
			
			$query_column = $query_column->result_array();
			if(count($query_column) > 0){
				return $query_column;
			}else{
				return null;
			}
		}else{
			
			$exclude = array();
			if(count($exclude_new) > 0){
				$exclude = array_merge($exclude, $exclude_new);
			}
			
			$exclude_column = '';
			if(count($exclude) > 0){
				foreach ($exclude as $value) {
					$value = "'".$value."'";
					if($exclude_column != ''){
						$exclude_column = $exclude_column.','.$value;
					}else{
						$exclude_column = $value;
					}
				}
				$exclude_column = '('.$exclude_column.')';
				$wherein = ' AND column_name in '.$exclude_column.' ';
			}else{
				$wherein = ' ';
			}
			
			if($ordernya == null){
				$orderdata = " order by ordinal_position ";
			}else{
				$ordernya_list = '';
				$tags = explode(',',$ordernya);
				$xx=0;
				
				$stringwhen = " ";
				foreach($tags as $key) {
					if($xx==0){
						$ordernya_list = "'".$key."'";  
						$stringwhen = " WHEN '".$key."' THEN ".$xx."";
					}else{
						$ordernya_list = $ordernya_list.','."'".$key."'";
						$stringwhen = $stringwhen." WHEN '".$key."' THEN ".$xx."";						
					}
					
					$xx++;
				}

				$orderdata = " ORDER BY CASE column_name " .$stringwhen. " ELSE ".$xx." END ASC, column_name ASC ";
			}

			$CI =& get_instance();
			$query_column = $CI->db->query("SELECT
					column_name as name, column_name as id, character_octet_length as numbernya, data_type as type, is_nullable as is_nullable,numeric_precision as isnumber
				FROM
					INFORMATION_SCHEMA.COLUMNS 
				WHERE
					TABLE_SCHEMA = '".$CI->db->database."' 
					AND TABLE_NAME = '".$table."'
					".$wherein."
					".$orderdata."
			");
			$query_column = $query_column->result_array();
			if(count($query_column) > 0){
				return $query_column;
			}else{
				return null;
			}
		
		}
		
		
		
		
	}
	
	public function query_column_include_nosort($table, $exclude_new) {
		
		
		if(dbconnection == 'postgre'){
			$exclude = array();
			if(count($exclude_new) > 0){
				$exclude = array_merge($exclude, $exclude_new);
			}
			
			$exclude_column = '';
			if(count($exclude) > 0){
				foreach ($exclude as $value) {
					$value = "'".$value."'";
					if($exclude_column != ''){
						$exclude_column = $exclude_column.','.$value;
					}else{
						$exclude_column = $value;
					}
				}
				$exclude_column = '('.$exclude_column.')';
				$wherein = ' AND cols.column_name in '.$exclude_column.' ';
			}else{
				$wherein = ' ';
			}

			$CI =& get_instance();
			$query_column = $CI->db->query("SELECT
					cols.column_name as name, 
					cols.column_name as id, 
					cols.character_octet_length as numbernya, 
					cols.data_type as type, cols.is_nullable as is_nullable,
					cols.numeric_precision as isnumber,
					(
						SELECT
							pg_catalog.col_description(c.oid, cols.ordinal_position::int)
						FROM
							pg_catalog.pg_class c
						WHERE
							c.oid = (SELECT (cols.table_name)::regclass::oid)
							AND c.relname = cols.table_name
					) AS column_comment
				FROM
					INFORMATION_SCHEMA.COLUMNS cols
				WHERE
					cols.TABLE_SCHEMA = 'public' 
					AND cols.TABLE_NAME = '".$table."'
					".$wherein."
			");
			$query_column = $query_column->result_array();
			if(count($query_column) > 0){
				return $query_column;
			}else{
				return null;
			}
		}else{
			
			$exclude = array();
			if(count($exclude_new) > 0){
				$exclude = array_merge($exclude, $exclude_new);
			}
			
			$exclude_column = '';
			if(count($exclude) > 0){
				foreach ($exclude as $value) {
					$value = "'".$value."'";
					if($exclude_column != ''){
						$exclude_column = $exclude_column.','.$value;
					}else{
						$exclude_column = $value;
					}
				}
				$exclude_column = '('.$exclude_column.')';
				$wherein = ' AND column_name in '.$exclude_column.' ';
			}else{
				$wherein = ' ';
			}

			$CI =& get_instance();
			$query_column = $CI->db->query("SELECT
					column_name as name, column_name as id, character_octet_length as numbernya, data_type as type, is_nullable as is_nullable,numeric_precision as isnumber
				FROM
					INFORMATION_SCHEMA.COLUMNS 
				WHERE
					TABLE_SCHEMA = '".$CI->db->database."' 
					AND TABLE_NAME = '".$table."'
					".$wherein."
					order by ordinal_position
			");
			$query_column = $query_column->result_array();
			if(count($query_column) > 0){
				return $query_column;
			}else{
				return null;
			}
		
		}
		
		
		
	}
	
		public function getviewlistform($module, $exclude, $tipe_form = 1){
		
			$user_id = $this->CI->session->userdata('userid');
			$this->CI->db->select('translate_view_user.*');
			$this->CI->db->where('translate_view_user.table',$module);
			$this->CI->db->where('translate_view_user.module',$module);
			$this->CI->db->where('translate_view_user.user_id',$user_id);
			$this->CI->db->where('translate_view_user.active',1);
			$this->CI->db->order_by('translate_view_user.modified','DESC');
			$this->CI->db->limit(1);
			$queryexclude = $this->CI->db->get('translate_view_user');
			$queryexclude = $queryexclude->result_object();
			if($queryexclude){
				//
			}else{
				$this->CI->db->select('translate_view.*');
				$this->CI->db->where('translate_view.table',$module);
				$this->CI->db->where('translate_view.module',$module);
				$this->CI->db->where('translate_view.active',1);
				$this->CI->db->order_by('translate_view.modified','DESC');
				$this->CI->db->limit(1);
				$queryexclude = $this->CI->db->get('translate_view');
				$queryexclude = $queryexclude->result_object();
			}
			
			if($tipe_form == 2){
				$this->CI->db->select('translate_view.*');
				$this->CI->db->where('translate_view.table',$module);
				$this->CI->db->where('translate_view.module',$module);
				$this->CI->db->where('translate_view.active',1);
				$this->CI->db->order_by('translate_view.modified','DESC');
				$this->CI->db->limit(1);
				$queryexclude = $this->CI->db->get('translate_view');
				$queryexclude = $queryexclude->result_object();
			}
		
			
			if($queryexclude){
				$query_column = $this->query_column($module, $exclude, $queryexclude[0]->data_order_form, $queryexclude[0]->data_order_form);
			}else{
				$query_column = $this->query_column($module, $exclude, null, null);
			}
			
			return $query_column;
	}
	
	
	public function query_table_list() {
    
    $column = [];
    $CI =& get_instance();
    
    // Deteksi database driver
    $db_driver = $CI->db->dbdriver;
    
    if($db_driver == 'postgre' || $db_driver == 'postgresql'){
        // PostgreSQL
        $query_column = $CI->db->query("
            SELECT 
                tablename as id, 
                tablename as name 
            FROM pg_catalog.pg_tables 
            WHERE schemaname = 'public'
            ORDER BY 
                CASE 
                    WHEN tablename != 'translate_table_select_option' THEN 1
                    ELSE 2 
                END ASC, 
                tablename ASC
        ");
        $query_column = $query_column->result_array();
        
        if(count($query_column) > 0){
            foreach($query_column as $rows){
                if($rows['name'] == 'translate_table_select_option'){
                    array_push($column, array(
                            "id" => $rows['id'],
                            "name" => 'Custom Option'
                        )
                    );
                }else{
                    array_push($column, array(
                            "id" => $rows['id'],
                            "name" => $rows['name']
                        )
                    );
                }
            }
            return $column;
        }else{
            return null;
        }
        
    }else{
        // MySQL
        $query_column = $CI->db->query("
            SELECT 
                table_name as id, 
                table_name as name
            FROM information_schema.tables
            WHERE TABLE_SCHEMA = '".$CI->db->database."'
            ORDER BY 
                CASE 
                    WHEN table_name != 'translate_table_select_option' THEN 1
                    ELSE 2 
                END ASC, 
                table_name ASC
        ");
        $query_column = $query_column->result_array();
        
        if(count($query_column) > 0){
            foreach($query_column as $rows){
                if($rows['name'] == 'translate_table_select_option'){
                    array_push($column, array(
                            "id" => $rows['id'],
                            "name" => 'Custom Option'
                        )
                    );
                }else{
                    array_push($column, array(
                            "id" => $rows['id'],
                            "name" => $rows['name']
                        )
                    );
                }
            }
            return $column;
        }else{
            return null;
        }
        
    }
    
}
	
	public function query_column($table, $exclude_new, $ordernya = null, $orderformnya = null, $q = null) {
		
		
		if(dbconnection == 'postgre'){
			
			$exclude = array();
			if(count($exclude_new) > 0){
				$exclude = array_merge($exclude, $exclude_new);
			}
			
			$exclude_column = '';
			if(count($exclude) > 0){
				foreach ($exclude as $value) {
					$value = "'".$value."'";
					if($exclude_column != ''){
						$exclude_column = $exclude_column.','.$value;
					}else{
						$exclude_column = $value;
					}
				}
				$exclude_column = '('.$exclude_column.')';
				if($q != null){
					$wherein = " AND (cols.column_name ~* '".$q."' AND cols.column_name not in ".$exclude_column." ) ";
				}else{
					$wherein = ' AND cols.column_name not in '.$exclude_column.' ';
				}
				
			}else{
				if($q != null){
					$wherein = " AND (cols.column_name ~* '".$q."') ";
				}else{
					$wherein = ' ';
				}
				
			}
			
			if($ordernya == null){
				$orderdata = " ORDER BY (
					SELECT
						pg_catalog.col_description(c.oid, cols.ordinal_position::int)
					FROM
						pg_catalog.pg_class c
					WHERE
						c.oid = (SELECT (cols.table_name)::regclass::oid)
						AND c.relname = cols.table_name
				) ASC ";
			}else{
				$ordernya_list = '';
				$tags = explode(',',$ordernya);
				$xx=0;
				
				$stringwhen = " ";
				foreach($tags as $key) {
					if($xx==0){
						$ordernya_list = "'".$key."'";  
						$stringwhen = " WHEN cols.column_name='".$key."' THEN ".$xx."";
					}else{
						$ordernya_list = $ordernya_list.','."'".$key."'";
						$stringwhen = $stringwhen." WHEN cols.column_name='".$key."' THEN ".$xx."";						
					}
					
					$xx++;
				}

				$orderdata = " ORDER BY CASE " .$stringwhen. " ELSE ".$xx." END ASC ";
			}

			$CI =& get_instance();
			$query_column = $CI->db->query("SELECT
					cols.column_name as name, 
					cols.column_name as id, 
					cols.character_octet_length as numbernya, 
					cols.data_type as type, cols.is_nullable as is_nullable,
					cols.numeric_precision as isnumber,
					(
						SELECT
							pg_catalog.col_description(c.oid, cols.ordinal_position::int)
						FROM
							pg_catalog.pg_class c
						WHERE
							c.oid = (SELECT (cols.table_name)::regclass::oid)
							AND c.relname = cols.table_name
					) AS column_comment
				FROM
					INFORMATION_SCHEMA.COLUMNS cols
				WHERE
					cols.TABLE_SCHEMA = 'public' 
					AND cols.TABLE_NAME = '".$table."'
					".$wherein."
					".$orderdata."
			");
			$query_column = $query_column->result_array();
			if(count($query_column) > 0){
				return $query_column;
			}else{
				return null;
			}
			
		}else{
			
			$exclude = array();
			if(count($exclude_new) > 0){
				$exclude = array_merge($exclude, $exclude_new);
			}
			
			$exclude_column = '';
			if(count($exclude) > 0){
				foreach ($exclude as $value) {
					$value = "'".$value."'";
					if($exclude_column != ''){
						$exclude_column = $exclude_column.','.$value;
					}else{
						$exclude_column = $value;
					}
				}
				$exclude_column = '('.$exclude_column.')';
				if($q != null){
					$wherein = " AND (column_name like '%".$q."%' AND column_name not in ".$exclude_column." ) ";
				}else{
					$wherein = ' AND column_name not in '.$exclude_column.' ';
				}
				
			}else{
				if($q != null){
					$wherein = " AND (column_name like '%".$q."%') ";
				}else{
					$wherein = ' ';
				}
				
			}
			
			if($ordernya == null){
				
				$orderdata = " ORDER BY column_comment * 1 ASC ";
				
			}else{
				$ordernya_list = '';
				$tags = explode(',',$ordernya);
				$xx=0;
				
				$stringwhen = " ";
				foreach($tags as $key) {
					if($xx==0){
						$ordernya_list = "'".$key."'";  
						$stringwhen = " WHEN '".$key."' THEN ".$xx."";
					}else{
						$ordernya_list = $ordernya_list.','."'".$key."'";
						$stringwhen = $stringwhen." WHEN '".$key."' THEN ".$xx."";						
					}
					
					$xx++;
				}

				$orderdata = " ORDER BY CASE column_name " .$stringwhen. " ELSE ".$xx." END ASC, column_name ASC ";
			}

			$CI =& get_instance();
			$query_column = $CI->db->query("SELECT
					column_name as name, column_name as id, character_octet_length as numbernya, data_type as type, is_nullable as is_nullable,numeric_precision as isnumber
				FROM
					INFORMATION_SCHEMA.COLUMNS 
				WHERE
					TABLE_SCHEMA = '".$CI->db->database."' 
					AND TABLE_NAME = '".$table."'
					".$wherein."
					".$orderdata."
			");
			$query_column = $query_column->result_array();
			if(count($query_column) > 0){
				return $query_column;
			}else{
				return null;
			}
			
		}
		
		
		
		
	}
	
	
	public function query_column_nosort($table, $exclude_new, $ordernya = null) {
		
		
		if(dbconnection == 'postgre'){
			
			$exclude = array();
			if(count($exclude_new) > 0){
				$exclude = array_merge($exclude, $exclude_new);
			}
			
			$exclude_column = '';
			if(count($exclude) > 0){
				foreach ($exclude as $value) {
					$value = "'".$value."'";
					if($exclude_column != ''){
						$exclude_column = $exclude_column.','.$value;
					}else{
						$exclude_column = $value;
					}
				}
				$exclude_column = '('.$exclude_column.')';
				$wherein = ' AND cols.column_name not in '.$exclude_column.' ';
			}else{
				$wherein = ' ';
			}
			
			if($ordernya == null){
				$orderdata = " ORDER BY (
					SELECT
						pg_catalog.col_description(c.oid, cols.ordinal_position::int)
					FROM
						pg_catalog.pg_class c
					WHERE
						c.oid = (SELECT (cols.table_name)::regclass::oid)
						AND c.relname = cols.table_name
				) ASC ";
			}else{
				$ordernya_list = '';
				$tags = explode(',',$ordernya);
				$xx=0;
				
				$stringwhen = " ";
				foreach($tags as $key) {
					if($xx==0){
						$ordernya_list = "'".$key."'";  
						$stringwhen = " WHEN cols.column_name='".$key."' THEN ".$xx."";
					}else{
						$ordernya_list = $ordernya_list.','."'".$key."'";
						$stringwhen = $stringwhen." WHEN cols.column_name='".$key."' THEN ".$xx."";						
					}
					
					$xx++;
				}

				$orderdata = " ORDER BY CASE " .$stringwhen. " ELSE ".$xx." END ASC ";
			}

			$CI =& get_instance();
			$query_column = $CI->db->query("SELECT
					cols.column_name as name, 
					cols.column_name as id, 
					cols.character_octet_length as numbernya, 
					cols.data_type as type, cols.is_nullable as is_nullable,
					cols.numeric_precision as isnumber,
					(
						SELECT
							pg_catalog.col_description(c.oid, cols.ordinal_position::int)
						FROM
							pg_catalog.pg_class c
						WHERE
							c.oid = (SELECT (cols.table_name)::regclass::oid)
							AND c.relname = cols.table_name
					) AS column_comment
				FROM
					INFORMATION_SCHEMA.COLUMNS cols
				WHERE
					cols.TABLE_SCHEMA = 'public' 
					AND cols.TABLE_NAME = '".$table."'
					".$wherein."
					".$orderdata."
			");
			$query_column = $query_column->result_array();
			if(count($query_column) > 0){
				return $query_column;
			}else{
				return null;
			}
		
		
		}else{
			
			$exclude = array();
			if(count($exclude_new) > 0){
				$exclude = array_merge($exclude, $exclude_new);
			}
			
			$exclude_column = '';
			if(count($exclude) > 0){
				foreach ($exclude as $value) {
					$value = "'".$value."'";
					if($exclude_column != ''){
						$exclude_column = $exclude_column.','.$value;
					}else{
						$exclude_column = $value;
					}
				}
				$exclude_column = '('.$exclude_column.')';
				$wherein = ' AND column_name not in '.$exclude_column.' ';
			}else{
				$wherein = ' ';
			}
			
			if($ordernya == null){
				$orderdata = " order by ordinal_position ";
			}else{
				$ordernya_list = '';
				$tags = explode(',',$ordernya);
				$xx=0;
				
				$stringwhen = " ";
				foreach($tags as $key) {
					if($xx==0){
						$ordernya_list = "'".$key."'";  
						$stringwhen = " WHEN '".$key."' THEN ".$xx."";
					}else{
						$ordernya_list = $ordernya_list.','."'".$key."'";
						$stringwhen = $stringwhen." WHEN '".$key."' THEN ".$xx."";						
					}
					
					$xx++;
				}

				$orderdata = " ORDER BY CASE column_name " .$stringwhen. " ELSE ".$xx." END ASC, column_name ASC ";
			}

			$CI =& get_instance();
			$query_column = $CI->db->query("SELECT
					column_name as name, column_name as id, character_octet_length as numbernya, data_type as type, is_nullable as is_nullable,numeric_precision as isnumber
				FROM
					INFORMATION_SCHEMA.COLUMNS 
				WHERE
					TABLE_SCHEMA = '".$CI->db->database."' 
					AND TABLE_NAME = '".$table."'
					".$wherein."
					".$orderdata."
			");
			$query_column = $query_column->result_array();
			if(count($query_column) > 0){
				return $query_column;
			}else{
				return null;
			}
			
		
		}
		
		
	}
	
	
	
	public function width_column($table,$column_name){
		
		
		if(dbconnection == 'postgre'){
				
			$refdata = $this->getRefDataWidth($table,$column_name);
			if($refdata != null){
				return $refdata;
			}
			
			$CI =& get_instance();
			$query_column = $CI->db->query("SELECT
					cols.column_name as name, 
					cols.column_name as id, 
					cols.character_octet_length as numbernya, 
					cols.data_type as type, cols.is_nullable as is_nullable,
					cols.numeric_precision as isnumber,
					(
						SELECT
							pg_catalog.col_description(c.oid, cols.ordinal_position::int)
						FROM
							pg_catalog.pg_class c
						WHERE
							c.oid = (SELECT (cols.table_name)::regclass::oid)
							AND c.relname = cols.table_name
					) AS column_comment
				FROM
					INFORMATION_SCHEMA.COLUMNS cols
				WHERE
					cols.COLUMN_NAME = '".$column_name."' 
					AND cols.TABLE_SCHEMA = 'public'
					AND cols.TABLE_NAME = '".$table."'
				ORDER BY (
					SELECT
						pg_catalog.col_description(c.oid, cols.ordinal_position::int)
					FROM
						pg_catalog.pg_class c
					WHERE
						c.oid = (SELECT (cols.table_name)::regclass::oid)
						AND c.relname = cols.table_name
				) ASC
			");
			
			$query_column = $query_column->result_array();
			if(count($query_column) > 0){
				if(isset($query_column[0]['column_comment'])){
					$label = $query_column[0]['column_comment'];
					$label = explode('|',$label);
					if(count($label) > 0){
						if(isset($label[2])){
							$column_name = $label[2];
						}else{
							$column_name = '12';
						}
					}else{
						$column_name = '12';
					}
				}else{
					$column_name = '12';
				}
				
			}
			
			return $column_name;
		
		}else{
				
			$refdata = $this->getRefDataWidth($table,$column_name);
			if($refdata != null){
				return $refdata;
			}
				
			$CI =& get_instance();
			$query_column = $CI->db->query("SELECT
					column_name as name, column_name as id, character_octet_length as numbernya, data_type as type, is_nullable as is_nullable,numeric_precision as isnumber,column_comment  as column_comment
				FROM
					INFORMATION_SCHEMA.COLUMNS 
				WHERE
					 COLUMN_NAME = '".$column_name."' 
					AND TABLE_SCHEMA = '".$CI->db->database."' 
					AND TABLE_NAME = '".$table."'
				ORDER BY column_comment * 1 ASC
			");
			$query_column = $query_column->result_array();
			if(count($query_column) > 0){
				if(isset($query_column[0]['column_comment'])){
					$label = $query_column[0]['column_comment'];
					$label = explode('|',$label);
					if(count($label) > 0){
						if(isset($label[2])){
							if(is_numeric($label[2]) == true){
								$column_name = $label[2];
							}else{
								$column_name = '12';
							}
						}else{
							$column_name = '12';
						}
					}else{
						$column_name = '12';
					}
				}else{
					$column_name = '12';
				}
				
			}
			
			return $column_name;
		
		}
		
	}
	
	public function get_table_reference($table,$column_name){
		
		if(dbconnection == 'postgre'){
			
			
			$refdata = $this->getRefData($table,$column_name);
			if($refdata != null){
				return $refdata;
			}
			
			$column_ori = $column_name;
			$CI =& get_instance();
			$query_column = $CI->db->query("SELECT
					cols.column_name as name, 
					cols.column_name as id, 
					cols.character_octet_length as numbernya, 
					cols.data_type as type, cols.is_nullable as is_nullable,
					cols.numeric_precision as isnumber,
					(
						SELECT
							pg_catalog.col_description(c.oid, cols.ordinal_position::int)
						FROM
							pg_catalog.pg_class c
						WHERE
							c.oid = (SELECT (cols.table_name)::regclass::oid)
							AND c.relname = cols.table_name
					) AS column_comment
				FROM
					INFORMATION_SCHEMA.COLUMNS cols
				WHERE
					cols.COLUMN_NAME = '".$column_name."' 
					AND cols.TABLE_SCHEMA = 'public'
					AND cols.TABLE_NAME = '".$table."'
				ORDER BY (
					SELECT
						pg_catalog.col_description(c.oid, cols.ordinal_position::int)
					FROM
						pg_catalog.pg_class c
					WHERE
						c.oid = (SELECT (cols.table_name)::regclass::oid)
						AND c.relname = cols.table_name
				) ASC
			");
			
			$query_column = $query_column->result_array();
			if(count($query_column) > 0){
				if(isset($query_column[0]['column_comment'])){
					$label = $query_column[0]['column_comment'];
					$label = explode('|',$label);
					if(count($label) > 0){
						if(isset($label[3])){
							$tabel_ref = $label[3];
							if(isset($label[4])){
								$table_id = $label[4];
							}else{
								$table_id = 'id';
							}
							if(isset($label[5])){
								$table_name = $label[5];
							}else{
								$table_name = 'name';
							}
							return array($tabel_ref,$table_id,$table_name,0,null,null);
						}
					}
				}
			}
			
			$refdata = $this->getRefData($table,$column_name);
			if($refdata != null){
				return $refdata;
			}
			
			return null;
			
		}else{
			
			$refdata = $this->getRefData($table,$column_name);
			if($refdata != null){
				return $refdata;
			}
			
			$column_ori = $column_name;
			$CI =& get_instance();
			$query_column = $CI->db->query("SELECT
					column_name as name, column_name as id, character_octet_length as numbernya, data_type as type, is_nullable as is_nullable,numeric_precision as isnumber,column_comment  as column_comment
				FROM
					INFORMATION_SCHEMA.COLUMNS 
				WHERE
					 COLUMN_NAME = '".$column_name."' 
					AND TABLE_SCHEMA = '".$CI->db->database."' 
					AND TABLE_NAME = '".$table."'
				ORDER BY column_comment * 1 ASC
			");
			$query_column = $query_column->result_array();
			if(count($query_column) > 0){
				if(isset($query_column[0]['column_comment'])){
					$label = $query_column[0]['column_comment'];
					$label = explode('|',$label);
					if(count($label) > 0){
						if(isset($label[3])){
							$tabel_ref = $label[3];
							if(isset($label[4])){
								$table_id = $label[4];
							}else{
								$table_id = 'id';
							}
							if(isset($label[5])){
								$table_name = $label[5];
							}else{
								$table_name = 'name';
							}
							return array($tabel_ref,$table_id,$table_name,0,null,null);
						}
					}
				}
			}
			
			
			
			return null;
		
			
		}
		
	}
	
		
	public function translate_column_table($table,$column_name,$exclude){
		

			$query_column = $this->getviewlistcontrol($table, $table, $exclude);
			if($query_column){
				$ordernya = array(null);
				$searchnya = array();
				$alias = 0;
				foreach($query_column as $rowsdata){
					$table_references = null;
					$table_references = $this->get_table_reference($table,$rowsdata['name']);
					
					if($table_references != null){
						
						if($rowsdata['name'] == $column_name){
							return $table_references[0].'_'.$alias.'.'.$table_references[2];
						}
						

					}
					
					$alias++;
				}
			}
			
			return $table.'.'.$column_name;
		
	}
	
	
	public function translate_column($table,$column_name){
		
		
		if(dbconnection == 'postgre'){
			
			$this->CI->db->select('translate.*');
			$this->CI->db->where('translate.meta_id',$column_name);
			$this->CI->db->where('translate.meta_table',$table);
			$this->CI->db->where('translate.active',1);
			$this->CI->db->order_by('translate.modified','DESC');
			$this->CI->db->limit(1);
			$query = $this->CI->db->get('translate');
			$query = $query->result_object();
			if($query){
				return $query[0]->meta_value;
			}
			
			$CI =& get_instance();
			$query_column = $CI->db->query("SELECT
					cols.column_name as name, 
					cols.column_name as id, 
					cols.character_octet_length as numbernya, 
					cols.data_type as type, cols.is_nullable as is_nullable,
					cols.numeric_precision as isnumber,
					(
						SELECT
							pg_catalog.col_description(c.oid, cols.ordinal_position::int)
						FROM
							pg_catalog.pg_class c
						WHERE
							c.oid = (SELECT (cols.table_name)::regclass::oid)
							AND c.relname = cols.table_name
					) AS column_comment
				FROM
					INFORMATION_SCHEMA.COLUMNS cols
				WHERE
					cols.COLUMN_NAME = '".$column_name."' 
					AND cols.TABLE_SCHEMA = 'public'
					AND cols.TABLE_NAME = '".$table."'
				ORDER BY (
					SELECT
						pg_catalog.col_description(c.oid, cols.ordinal_position::int)
					FROM
						pg_catalog.pg_class c
					WHERE
						c.oid = (SELECT (cols.table_name)::regclass::oid)
						AND c.relname = cols.table_name
				) ASC
			");
			
			$query_column = $query_column->result_array();
			if(count($query_column) > 0){
				if(isset($query_column[0]['column_comment'])){
					$label = $query_column[0]['column_comment'];
					$label = explode('|',$label);
					if(count($label) > 0){
						if(isset($label[1])){
							$column_name = $label[1];
						}
					}
				}
				
			}
			
			return $column_name;
			
		}else{
			
			$this->CI->db->select('translate.*');
			$this->CI->db->where('translate.meta_id',$column_name);
			$this->CI->db->where('translate.meta_table',$table);
			$this->CI->db->where('translate.active',1);
			$this->CI->db->order_by('translate.modified','DESC');
			$this->CI->db->limit(1);
			$query = $this->CI->db->get('translate');
			$query = $query->result_object();
			if($query){
				return $query[0]->meta_value;
			}
			
			$column_ori = $column_name;
			$CI =& get_instance();
			$query_column = $CI->db->query("SELECT
					column_name as name, column_name as id, character_octet_length as numbernya, data_type as type, is_nullable as is_nullable,numeric_precision as isnumber,column_comment  as column_comment
				FROM
					INFORMATION_SCHEMA.COLUMNS 
				WHERE
					 COLUMN_NAME = '".$column_name."' 
					AND TABLE_SCHEMA = '".$CI->db->database."' 
					AND TABLE_NAME = '".$table."'
				ORDER BY column_comment * 1 ASC
			");
			$query_column = $query_column->result_array();
			if(count($query_column) > 0){
				if(isset($query_column[0]['column_comment'])){
					$label = $query_column[0]['column_comment'];
					$label = explode('|',$label);
					if(count($label) > 0){
						if(isset($label[1])){
							$column_name = $label[1];
						}
					}
				}
			}
			
			if($column_name == $column_ori){
				return $this->translate_column_view($table,$column_name);
			}
			
			return $column_name;
		
		}
		
	}
	
	
	public function translate_column_view($table,$column_name){
		
		$this->CI->db->select('translate.*');
		$this->CI->db->where('translate.meta_id',$column_name);
		$this->CI->db->where('translate.meta_table',$table);
		$this->CI->db->where('translate.active',1);
		$this->CI->db->order_by('translate.modified','DESC');
		$this->CI->db->limit(1);
		$query = $this->CI->db->get('translate');
		$query = $query->result_object();
		if($query){
			return $query[0]->meta_value;
		}
		
		if($column_name == 'jml_bast_selesai'){
			return "Jumlah BAST Selesai";
		}elseif($column_name == 'handover_type'){
			return "Tipe Handover";
		}elseif($column_name == 'file'){
			return "Dokumen";
		}elseif($column_name == 'invoice_type'){
			return "Dokumen Tipe";
		}elseif($column_name == 'status_invoice_name'){
			return "Status Invoice";
		}elseif($column_name == 'deskripsi_progres'){
			return "Deskripsi Progres";
		}
		
		$CI =& get_instance();
		$query_column = $CI->db->query("SELECT
				column_name as name, column_name as id, character_octet_length as numbernya, data_type as type, is_nullable as is_nullable,numeric_precision as isnumber,column_comment  as column_comment
			FROM
				INFORMATION_SCHEMA.COLUMNS 
			WHERE
				 COLUMN_NAME = '".$column_name."' 
				AND TABLE_SCHEMA = '".$CI->db->database."' 
				 AND column_comment != ''
			ORDER BY column_comment * 1 ASC
		");
		$query_column = $query_column->result_array();
		if(count($query_column) > 0){
			if(isset($query_column[0]['column_comment'])){
				$label = $query_column[0]['column_comment'];
				$label = explode('|',$label);
				if(count($label) > 0){
					if(isset($label[1])){
						$column_name = $label[1];
					}
				}
			}
			
		}
		
		return $column_name;
	}
	
	public function getDetailPagebySlug($slug){
		$this->CI->db->select('data_page.*,master_page_jenis.name as type_name');
		$this->CI->db->where('data_page.slug',$slug);
		$this->CI->db->where('data_page.jenis_id',1);
		$this->CI->db->where('data_page.active',1);
		$this->CI->db->where('data_page.is_publish',1);
		$this->CI->db->join('master_page_jenis','master_page_jenis.id = data_page.jenis_id','left');
		$this->CI->db->order_by('data_page.modified','DESC');
		$this->CI->db->limit(1);
		$query = $this->CI->db->get('data_page');
		$query = $query->result_array();
		if(count($query) > 0){
			return $query;
		}
		
		return null;
	}
	
	public function getDetailPostbySlug($slug){
		$this->CI->db->select('data_article.*,master_article_jenis.name as type_name');
		$this->CI->db->where('data_article.slug',$slug);
		$this->CI->db->where('data_article.active',1);
		$this->CI->db->where('data_article.is_publish',1);
		$this->CI->db->join('master_article_jenis','master_article_jenis.id = data_article.jenis_id','left');
		$this->CI->db->order_by('data_article.modified','DESC');
		$this->CI->db->limit(1);
		$query = $this->CI->db->get('data_article');
		$query = $query->result_array();
		if(count($query) > 0){
			return $query;
		}
		
		return null;
	}
	
	public function getDetailPagebySlugPreview($slug){
		$this->CI->db->select('data_page.*,master_page_jenis.name as type_name');
		$this->CI->db->where('data_page.slug',$slug);
		$this->CI->db->where('data_page.active',1);
		$this->CI->db->where('data_page.is_publish',0);
		$this->CI->db->join('master_page_jenis','master_page_jenis.id = data_page.jenis_id','left');
		$this->CI->db->order_by('data_page.modified','DESC');
		$this->CI->db->limit(1);
		$query = $this->CI->db->get('data_page');
		$query = $query->result_array();
		if(count($query) > 0){
			return $query;
		}
		
		return null;
	}
	
	public function getDetailPostbySlugPreview($slug){
		$this->CI->db->select('data_article.*,master_article_jenis.name as type_name');
		$this->CI->db->where('data_article.slug',$slug);
		$this->CI->db->where('data_article.active',1);
		$this->CI->db->where('data_article.is_publish',0);
		$this->CI->db->join('master_article_jenis','master_article_jenis.id = data_article.jenis_id','left');
		$this->CI->db->order_by('data_article.modified','DESC');
		$this->CI->db->limit(1);
		$query = $this->CI->db->get('data_article');
		$query = $query->result_array();
		if(count($query) > 0){
			return $query;
		}
		
		return null;
	}
	
	public function getPageFrontend(){
		$this->CI->db->select('data_page.*,master_page_jenis.name as type_name');
		$this->CI->db->where('data_page.jenis_id',2);
		$this->CI->db->where('data_page.active',1);
		$this->CI->db->where('data_page.is_publish',1);
		$this->CI->db->join('master_page_jenis','master_page_jenis.id = data_page.jenis_id','left');
		$this->CI->db->order_by('data_page.modified','DESC');
		$this->CI->db->limit(1);
		$query = $this->CI->db->get('data_page');
		$query = $query->result_array();
		if(count($query) > 0){
			return $query;
		}
		
		return null;
	}
	
	public function security_access($id, $table, $table_id) {
		
		$CI =& get_instance();
		
		$userid = $CI->session->userdata('userid');
		$gid = $CI->session->userdata('group_id');
		
		if($gid == 3){
			$area_kota_id = $this->select2_getname($userid,'users_data','id','area_kota_id');
			
			$CI->db->select('area_kota_id');
			$CI->db->where($table_id,$id);
			$CI->db->limit(1);
			$query = $CI->db->get($table);
			$query = $query->result_object();
			if($query){
				if($query[0]->area_kota_id == $area_kota_id){
					//redirect('dashboard?message=noaccess', 'refresh');
				}else{
					redirect('dashboard?message=noaccess', 'refresh');
				}
			}
		}

	}
	
	function sendMessage($content, $heading, $userID){
			$app_id_onesignal = 'addb86dd-20af-4bda-b713-97b35b464400';
			$this->CI->db->select('notif_id');
			$this->CI->db->where('id',$userID);
			$result = $this->CI->db->get('users_data');
			$result = $result->result_object();
			if($result){
				 if($result[0]->notif_id != ''){
					 
					$heading = array(
					   "en" => $heading
					);

					$content = array(
						"en" => $content
					);
					
					$fields = array(
						'app_id' => $app_id_onesignal,
						//'android_channel_id' => '2f24550a-5358-43af-9e81-ebb4ff455690',
						'include_aliases' => array(
							"external_id" => array($result[0]->notif_id)
						),
						"target_channel" => "push",
						'data' => array("foo" => "bar"),
						'contents' => $content,
						'priority' => 10,
						'headings' => $heading,
					);
					
					$fields = json_encode($fields);
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
					curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
															   'Authorization: Basic YTVjMTljODUtOWMxNi00ODliLWE5ZjgtMWRhZDhlYTE0MzFk'));
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
					curl_setopt($ch, CURLOPT_HEADER, FALSE);
					curl_setopt($ch, CURLOPT_POST, TRUE);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

					$response = curl_exec($ch);
					curl_close($ch);
					return array("status" => 1,"message" => json_encode($response), "notif_id" => array($result[0]->notif_id));
				 }else{
					return array("status" => 0,"message" => 'Notif ID NUll', "notif_id" => array($result[0]->notif_id));
				 }
			}else{
				return array("status" => 0,"message" => 'User tidak ditemukan', "notif_id" => '');
			}
			
			
		}
	
	
	//SESSION CHECK
	
	function sendGCM($to_id,$title,$message) {
			$notif_id = $this->select2_getname($to_id,"users_data","id","notif_id");
			
			if($notif_id == ''){
				return false;
			}
			
			$SERVER_KEY = 'AAAAOoITJHs:APA91bHfDd55eoHMHpKiWsz_5ulY7WEbzfaeTh0zQP392l449FoGaSai5z9oBnM2z8HkPeQOZJ2AbuZCPirHEyit5Q0yr5wgCihth18l585LZJC5YRyhvdbXRMB-5g4KqTH8NqkVLpEe';
			
			$DEVICE_REG_TOKEN=$notif_id;
			
				$databosy = [
					'click_action' => base_url(),
					'url' =>  base_url(),
					'icon' => base_url('logo-text.png')
				  ];
			  
			  $msg = [
				'title' => $title,
				'body' => $message,
				'icon' => base_url('logo-text.png'),
				'click_action' => base_url(),
				'data' => $databosy
			  ];

			  $fields = [
				'to' => $DEVICE_REG_TOKEN,
				'notification' => $msg
			  ];
  
			
			$fields = json_encode($fields);
			
			$ch = curl_init();

			curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

			$headers = array();
			$headers[] = 'Authorization: key='.$SERVER_KEY;
			$headers[] = 'Content-Type: application/json';
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

			$result = curl_exec($ch);
			if (curl_errno($ch)) {
				echo 'Error:' . curl_error($ch);
			}
			curl_close($ch);
			return $result;
		}
		
	public function session_check() {
		$CI =& get_instance();
		$CI->load->library('curl');
		$userid = $CI->session->userdata('userid');
		$logged_in = $CI->session->userdata('logged_in');
		if ( !$userid && $logged_in != TRUE) {
			redirect('login', 'refresh');
		}

	}
	
	public function access_check($module) {
		$CI =& get_instance();
		$group_id = $CI->session->userdata('group_id');
		$this->setMenu($module);
		
		$CI->db->where('gid',$group_id);
		$CI->db->where('module',$module);
		$CI->db->where('view',1);
		$CI->db->join('master_menu','master_menu.id=users_groups_access.menu_id');
		$query = $CI->db->get('users_groups_access');
		$query = $query->result_object();
		if(!$query){
			if($module == 'dashboard'){
				#
			}else{
				redirect('dashboard?message=noaccess', 'refresh');
			}
			
		}
		
		return true;

	}
	
	public function parentmodule($module) {
		$CI =& get_instance();
		$CI->db->where('module',$module);
		$query = $CI->db->get('master_menu');
		$query = $query->result_object();
		if($query){
			return $query[0]->parent_id;
		}
		
		return null;

	}
	
	public function getmodulename($module) {
		$CI =& get_instance();
		$CI->db->where('module',$module);
		$query = $CI->db->get('master_menu');
		$query = $query->result_object();
		if($query){
			return $query[0]->name;
		}
		
		return null;

	}
	
	public function access_check_insert($module) {
		$CI =& get_instance();
		$group_id = $CI->session->userdata('group_id');
		
		$CI->db->where('gid',$group_id);
		$CI->db->where('module',$module);
		$CI->db->where('insert',1);
		$CI->db->join('master_menu','master_menu.id=users_groups_access.menu_id');
		$query = $CI->db->get('users_groups_access');
		$query = $query->result_object();
		if(!$query){
			if($module == 'dashboard'){
				#
			}else{
				redirect('dashboard?message=noaccess', 'refresh');
			}
			
		}
		
		return true;

	}
	
	public function getIconMenu($module) {
		$CI =& get_instance();
		$CI->db->where('module',$module);
		$CI->db->limit(1);
		$query = $CI->db->get('master_menu');
		$query = $query->result_object();
		if($query){
			return $query[0]->icon;
		}
		
		return 'fa fa-home';

	}
	
	public function access_check_view($module) {
		$CI =& get_instance();
		$group_id = $CI->session->userdata('group_id');
		
		$CI->db->where('gid',$group_id);
		$CI->db->where('module',$module);
		$CI->db->where('view',1);
		$CI->db->join('master_menu','master_menu.id=users_groups_access.menu_id');
		$query = $CI->db->get('users_groups_access');
		$query = $query->result_object();
		if(!$query){
			if($module == 'dashboard'){
				#
			}else{
				redirect('dashboard?message=noaccess', 'refresh');
			}
			
		}
		
		return true;

	}
	
	public function access_check_update($module) {
		$CI =& get_instance();
		$group_id = $CI->session->userdata('group_id');
		
		$CI->db->where('gid',$group_id);
		$CI->db->where('module',$module);
		$CI->db->where('update',1);
		$CI->db->join('master_menu','master_menu.id=users_groups_access.menu_id');
		$query = $CI->db->get('users_groups_access');
		$query = $query->result_object();
		if(!$query){
			if($module == 'dashboard'){
				#
			}else{
				redirect('dashboard?message=noaccess', 'refresh');
			}
			
		}
		
		return true;

	}
	
	public function access_check_delete($module) {
		$CI =& get_instance();
		$group_id = $CI->session->userdata('group_id');
		
		$CI->db->where('gid',$group_id);
		$CI->db->where('module',$module);
		$CI->db->where('delete',1);
		$CI->db->join('master_menu','master_menu.id=users_groups_access.menu_id');
		$query = $CI->db->get('users_groups_access');
		$query = $query->result_object();
		if(!$query){
			if($module == 'dashboard'){
				#
			}else{
				redirect('dashboard?message=noaccess', 'refresh');
			}
			
		}
		
		return true;

	}
	
	public function access_check_insert_data($module) {
		$CI =& get_instance();
		$group_id = $CI->session->userdata('group_id');
		
		$CI->db->where('gid',$group_id);
		$CI->db->where('module',$module);
		$CI->db->where('insert',1);
		$CI->db->join('master_menu','master_menu.id=users_groups_access.menu_id');
		$query = $CI->db->get('users_groups_access');
		$query = $query->result_object();
		if(!$query){
			return false;
		}
		
		return true;

	}
	
	public function access_check_view_data($module) {
		$CI =& get_instance();
		$group_id = $CI->session->userdata('group_id');
		
		$CI->db->where('gid',$group_id);
		$CI->db->where('module',$module);
		$CI->db->where('view',1);
		$CI->db->join('master_menu','master_menu.id=users_groups_access.menu_id');
		$query = $CI->db->get('users_groups_access');
		$query = $query->result_object();
		if(!$query){
			return false;
		}
		
		return true;

	}
	
	public function access_check_update_data($module) {
		$CI =& get_instance();
		$group_id = $CI->session->userdata('group_id');
		
		$CI->db->where('gid',$group_id);
		$CI->db->where('module',$module);
		$CI->db->where('update',1);
		$CI->db->join('master_menu','master_menu.id=users_groups_access.menu_id');
		$query = $CI->db->get('users_groups_access');
		$query = $query->result_object();
		if(!$query){
			return false;
		}
		
		return true;

	}

	public function access_check_delete_data($module) {
		$CI =& get_instance();
		$group_id = $CI->session->userdata('group_id');
		
		$CI->db->where('gid',$group_id);
		$CI->db->where('module',$module);
		$CI->db->where('delete',1);
		$CI->db->join('master_menu','master_menu.id=users_groups_access.menu_id');
		$query = $CI->db->get('users_groups_access');
		$query = $query->result_object();
		if(!$query){
			return false;
		}
		
		return true;

	}
	
	public function get_access_id(){
		$CI =& get_instance();
		return $CI->session->userdata('group_id');
	}
	
	//SELECT2 DATA
	
	public function select2_data_all($table,$tableid,$tablename,$q) {
		$CI =& get_instance();
		$tableidas = $tableid.' as id';
		$tablenameas = $tablename.' as name';
		$column = $tableidas.','.$tablenameas;
		$CI->db->select($column);
		$query = $CI->db->get($table);
		$query = $query->result_array();
		$data = array('items' => $query);
		return json_encode($data);
	}
	
	public function select2_data_filter_all($table,$tableid,$tablename,$q, $filter, $filterdata) {
		$CI =& get_instance();
		$tableidas = $tableid.' as id';
		$tablenameas = $tablename.' as name';
		$column = $tableidas.','.$tablenameas;
		$CI->db->select($column);
		$CI->db->where($filter, $filterdata);
		$query = $CI->db->get($table);
		$query = $query->result_array();
		$data = array('items' => $query);
		return json_encode($data);
	}
	
	public function select2_data_filter($table,$tableid,$tablename, $q, $filter, $filterdata) {
		$CI =& get_instance();
		$tableidas = $tableid.' as id';
		$tablenameas = $tablename.' as name';
		$column = $tableidas.','.$tablenameas;
		$CI->db->select($column);
		$CI->db->like($tablename, $q);
		$CI->db->where($filter, $filterdata);
		$query = $CI->db->get($table);
		$query = $query->result_array();
		$data = array('items' => $query);
		return json_encode($data);
	}

	public function select2_data($table,$tableid,$tablename,$q) {
		$CI =& get_instance();
		$tableidas = $tableid.' as id';
		$tablenameas = $tablename.' as name';
		$column = $tableidas.','.$tablenameas;
		$CI->db->select($column);
		$CI->db->like($tablename, $q);
		$query = $CI->db->get($table);
		$query = $query->result_array();
		$data = array('items' => $query);
		return json_encode($data);
	}
	
	public function select2_getname_all($id,$table,$tableid){
		$CI =& get_instance();
		//$tablenameas = $tablename.' as name';
		$CI->db->select('*');
		$CI->db->where($tableid, $id);
		$CI->db->order_by('modified','DESC');
		$query = $CI->db->get($table);
		$query = $query->result_object();
		if($query){
			return $query[0];
		}else{
			return null;
		}
	}
	
	public function select2_getname($id, $table, $tableid, $tablename)
{
    if (empty($tablename) || empty($id) || empty($table)) {
        return '-';
    }

    $CI =& get_instance();

    $CI->db->select($tablename . ' AS name');

    if (!empty($tableid)) {
        $CI->db->where($tableid, $id);
    }

    // ✅ CEK dulu apakah kolom "modified" ada
    if ($CI->db->field_exists('modified', $table)) {
        $CI->db->order_by('modified', 'DESC');
    }

    $query = $CI->db->get($table);

    // ✅ cegah fatal error
    if ($query === false) {
        log_message('error', 'Query failed: ' . $CI->db->last_query());
        return '-';
    }

    $result = $query->result();

    return (!empty($result)) ? $result[0]->name : '-';
}

	
	public function select2_getname_menu($id,$table,$tableid,$tablename,$notnullid){
		$CI =& get_instance();
		$tablenameas = $tablename.' as name';
		$CI->db->select($tablenameas);
		$CI->db->where($tableid, $id);
		$CI->db->where($notnullid.' !=', '00000000-0000-0000-0000-000000000000');
		$CI->db->order_by('modified','DESC');
		$query = $CI->db->get($table);
		$query = $query->result_object();
		if($query){
			return $query[0]->name;
		}else{
			return '-';
		}
	}

	public function tgl_indo($tanggal){
		$bulan = array (
			'Januari',
			'Februari',
			'Maret',
			'April',
			'Mei',
			'Juni',
			'Juli',
			'Agustus',
			'September',
			'Oktober',
			'November',
			'Desember'
		);
		$pecahkan = explode('-', $tanggal);
		
		// variabel pecahkan 0 = tahun
		// variabel pecahkan 1 = bulan
		// variabel pecahkan 2 = tanggal
	 
		return $pecahkan[2] . ' ' . $bulan[ (int)$pecahkan[1] ] . ' ' . $pecahkan[0];
	}

	public function months_array($id)
	{
		$months_array=array(
			1 => 'Januari',
			2 => 'Februari',
			3 => 'Maret',
			4 => 'April',
			5 => 'Mei',
			6 => 'Juni',
			7 => 'Juli',
			8 => 'Agustus',
			9 => 'September',
			10 => 'Oktober',
			11 => 'Nopember',
			12 => 'Desember'
		);
	
			return $months_array[$id];
	}
	
	
	//DATATABLES DATA

	public function _get_datatables_query($table,$column_order,$column_search,$order,$select,$jointable,$joindetail,$joinposition,$wheretable,$wherecolumn,$groupby)
    {
		$CI =& get_instance();
		$CI->db->select($select);
		if(count($jointable) > 0){
			for($i = 0;$i < count($jointable);$i++){
				$CI->db->join($jointable[$i], $joindetail[$i], $joinposition[$i]);
			}
		}
        $CI->db->from($table);
		
		if(count($wheretable) > 0){
			for($i = 0;$i < count($wheretable);$i++){
				
				$explode = explode("|",$wheretable[$i]);
				if(count($explode) > 1){
					if($explode[1] == 'in'){
						$CI->db->where_in($explode[0], $wherecolumn[$i]);
					}elseif($explode[1] == 'like'){
						$CI->db->like($explode[0], $wherecolumn[$i]);
					}elseif($explode[1] == 'notin'){
						$CI->db->where_not_in($explode[0], $wherecolumn[$i]);
					}elseif($explode[1] == 'or'){
						$CI->db->or_where($wheretable[$i], $wherecolumn[$i]);
					}else{
						$CI->db->where($wheretable[$i], $wherecolumn[$i]);
					}
				}else{
					$CI->db->where($wheretable[$i], $wherecolumn[$i]);
				}
						
				
			}
		}
 
        $i = 0;
     
        foreach ($column_search as $item)
        {
			if($_POST['search']['value'])
            {
                if($i===0)
                {
                    $CI->db->group_start();
                    $CI->db->like($item, $_POST['search']['value']);
                }
                else
                {
                    $CI->db->or_like($item, $_POST['search']['value']);
                }
 
                if(count($column_search) - 1 == $i)
                    $CI->db->group_end();
            }
            $i++;
        }
         
        if(isset($_POST['order']))
        {
            $CI->db->order_by($column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } 
        else if(isset($order))
        {
            $order = $order;
			if(count($order) > 0){
				foreach($order as $rows => $key){
					$CI->db->order_by($rows,$key);
				}
			}
            
        }
		
		if(count($groupby) > 0){
			$CI->db->group_by($groupby); 
		}
		
		 
		 
    }
 
    public function get_datatables($table,$column_order,$column_search,$order,$select,$jointable,$joindetail,$joinposition,$wheretable,$wherecolumn,$groupby)
    {
		$CI =& get_instance();
        $this->_get_datatables_query($table,$column_order,$column_search,$order,$select,$jointable,$joindetail,$joinposition,$wheretable,$wherecolumn,$groupby);
		
		
        if($_POST['length'] != -1)
        $CI->db->limit($_POST['length'], $_POST['start']);
        $query = $CI->db->get();
		//echo $CI->db->last_query();
        return $query->result();
    }
 
    public function count_filtered($table,$column_order,$column_search,$order,$select,$jointable,$joindetail,$joinposition,$wheretable,$wherecolumn,$groupby)
    {
		$CI =& get_instance();
        $this->_get_datatables_query($table,$column_order,$column_search,$order,$select,$jointable,$joindetail,$joinposition,$wheretable,$wherecolumn,$groupby);
        $query = $CI->db->get();
        return $query->num_rows();
    }
 
    public function count_all($table)
    {
		$CI =& get_instance();
        $CI->db->from($table);
        return $CI->db->count_all_results();
    }
	
	//PASSWORD ENCRYPT

	public function hash($password){
		return password_hash($password, $this->_algo, ['cost' => $this->_cost]);
	}

	public function set_cost($cost = 10){
		$this->_cost = $cost;
		return $this;
	}

	public function set_algo($algo = 'default'){
		if(!in_array($algo, array_keys(self::ALLOWED_ALGOS))){
			throw new Exception($algo ." is not allowed algo.");
		}
		$this->_algo = self::ALLOWED_ALGOS[$algo];
		return $this;
	}

	public function verify_hash($password, $hash){
		return password_verify($password, $hash);
	}
	
	
	public function upload_image_do($target_path, $file, $namefield){
		$CI =& get_instance();
		if($file){
			$CI->load->library('upload');
			$config['upload_path'] = $target_path;
			$config['allowed_types'] = 'gif|jpg|png|jpeg';
			$CI->upload->initialize($config);
			if($CI->upload->do_upload($namefield)){
				$file_data = $CI->upload->data();
				return $file_data['file_name'];
			}else{
				return null;
			}
		}
		 
	}
	
	function _clean_input_data($str)
	{
		$string = str_replace("&nbsp;", " ", $str);
		$string = str_replace("&ndash;", " ", $string);
		$string = htmlspecialchars(trim($string) ?? '');
		$string = html_entity_decode($string) ;
		$string = preg_replace('/<[^<|>]+?>/', ' ', htmlspecialchars_decode($string));
		$string = htmlentities($string, ENT_QUOTES, "UTF-8");
		return $string;
		
	}
	
	function _clean_special($string) {
	   $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
	   $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.

	   return preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
	}
	
	function rupiah($angka){
	
		$hasil_rupiah = "Rp " . number_format($angka,2,',','.');
		return $hasil_rupiah;
	 
	}
	
	function rupiahnonkoma($angka){
		if($angka == 0){
			$hasil_rupiah = " - ";
		}else{
			$hasil_rupiah = "" . number_format($angka,0,',','.');
		}
		
		return $hasil_rupiah;
	 
	}
	
	function rupiahnonkomanonrp($angka){
	
		$hasil_rupiah = number_format($angka,0,',','.');
		return $hasil_rupiah;
	 
	}
	
	function rupiahsingkat($n, $presisi=1) {
		if ($n < 900) {
			$format_angka = number_format($n, $presisi);
			$simbol = '';
		} else if ($n < 900000) {
			$format_angka = number_format($n / 1000, $presisi);
			$simbol = ' rb';
		} else if ($n < 900000000) {
			$format_angka = number_format($n / 1000000, $presisi);
			$simbol = ' jt';
		} else if ($n < 900000000000) {
			$format_angka = number_format($n / 1000000000, $presisi);
			$simbol = ' M';
		} else {
			$format_angka = number_format($n / 1000000000000, $presisi);
			$simbol = ' T';
		}
	 
		if ( $presisi > 0 ) {
			$pisah = '.' . str_repeat( '0', $presisi );
			$format_angka = str_replace( $pisah, '', $format_angka );
		}
		
		return $format_angka . $simbol;
	}
	
	function sanitizenormal($string,$table){
		
		if($string == null){
			$string = '';
		}
		$CI =& get_instance();
		// sanitize string, remove Latin chars like 'ç ' and add - instead of white-space
		// based on: http://stackoverflow.com/a/2103815/2275490
		$str= strtolower(trim(preg_replace('~[^0-9a-z]+~i', '-', html_entity_decode(preg_replace('~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i', '$1', htmlentities($string, ENT_QUOTES, 'UTF-8')), ENT_QUOTES, 'UTF-8')), '-'));
		// check how often header exists in table "posts"
		// add counted suffix to pretty_url if already exists
		//$str=encrypt_short($str);
		$x=1;
		$y=1;
		$strold = $str;
		while($x == 1) {
			//echo $str.'<br>';
			$CI->db->select('count(*) as total');
			$CI->db->where('lower(slug)', strtolower($str));
			$query = $CI->db->get($table);
			$query = $query->result_array();
			if(count($query)>0) {
				if($query[0]['total'] == 0){
					$x=2;
					//$str=$strold.'-'.$y;
					break;
				}else{
					$y++;
					$str=$strold.'-'.$y; // allways returns the latest number for same slug
					//echo $str;
					//$x=3;
				}
			}else{
				$x=2;
			}
		}

		return $str;                    
	}
	
	function sanitize($string,$table){
		
		if($string == null){
			$string = '';
		}
		$CI =& get_instance();
		// sanitize string, remove Latin chars like 'ç ' and add - instead of white-space
		// based on: http://stackoverflow.com/a/2103815/2275490
		$str= strtolower(trim(preg_replace('~[^0-9a-z]+~i', '-', html_entity_decode(preg_replace('~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i', '$1', htmlentities($string, ENT_QUOTES, 'UTF-8')), ENT_QUOTES, 'UTF-8')), '-'));
		// check how often header exists in table "posts"
		// add counted suffix to pretty_url if already exists
		$str=encrypt_short($str);
		$x=1;
		$y=1;
		$strold = $str;
		while($x == 1) {
			//echo $str.'<br>';
			$CI->db->select('count(*) as total');
			$CI->db->where('lower(slug)', strtolower($str));
			$query = $CI->db->get($table);
			$query = $query->result_array();
			if(count($query)>0) {
				if($query[0]['total'] == 0){
					$x=2;
					//$str=$strold.'-'.$y;
					break;
				}else{
					$y++;
					$str=$strold.'-'.$y; // allways returns the latest number for same slug
					//echo $str;
					//$x=3;
				}
			}else{
				$x=2;
			}
		}

		return $str;                    
	}
	
	function getsanitizefieldid($string,$table){
		
		if($string == null){
			$string = '';
		}
		$CI =& get_instance();
		// sanitize string, remove Latin chars like 'ç ' and add - instead of white-space
		// based on: http://stackoverflow.com/a/2103815/2275490
		$str= strtolower(trim(preg_replace('~[^0-9a-z]+~i', '-', html_entity_decode(preg_replace('~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i', '$1', htmlentities($string, ENT_QUOTES, 'UTF-8')), ENT_QUOTES, 'UTF-8')), '-'));
		// check how often header exists in table "posts"
		// add counted suffix to pretty_url if already exists
		$str=encrypt_short($str);
		$x=1;
		$y=1;
		$strold = $str;
		while($x == 1) {
			//echo $str.'<br>';
			$CI->db->select('count(*) as total');
			$CI->db->where('lower(field_detail_id)', strtolower($str));
			$query = $CI->db->get($table);
			$query = $query->result_array();
			//echo $CI->db->last_query();;
			if(count($query)>0) {
				if($query[0]['total'] == 0){
					$x=2;
					//$str=$strold.'-'.$y;
					break;
				}else{
					$y++;
					$str=$strold.'-'.$y; // allways returns the latest number for same slug
					//echo $str;
					//$x=3;
				}
			}else{
				$x=2;
			}
		}

		return $str;                    
	}
	
	function hari_ini($date){
		$date = date_create($date);
		$hari = date_format($date,"D");
	 
		switch($hari){
			case 'Sun':
				$hari_ini = "Minggu";
			break;
	 
			case 'Mon':			
				$hari_ini = "Senin";
			break;
	 
			case 'Tue':
				$hari_ini = "Selasa";
			break;
	 
			case 'Wed':
				$hari_ini = "Rabu";
			break;
	 
			case 'Thu':
				$hari_ini = "Kamis";
			break;
	 
			case 'Fri':
				$hari_ini = "Jumat";
			break;
	 
			case 'Sat':
				$hari_ini = "Sabtu";
			break;
			
			default:
				$hari_ini = "Tidak di ketahui";		
			break;
		}
 
	return $hari_ini.', '.date_format($date,"d F Y");
 
	}
	
	function getConfigPagging($url, $jumlah_data, $per_page, $segment, $uri_segment){
			
			$config['reuse_query_string'] = TRUE;
			$config['base_url'] = base_url().$url;
			$config['total_rows'] = $jumlah_data;
			$config['per_page'] = $per_page;
			$config["uri_segment"] = $uri_segment;  // uri parameter
			$choice = $config["total_rows"] / $config["per_page"];
			$config["num_links"] = floor($choice);
			// Membuat Style pagination untuk BootStrap v4
			$config['first_link']       = 'First';
			$config['last_link']        = 'Last';
			$config['next_link']        = '>';
			$config['prev_link']        = '<';
			$config['full_tag_open']    = '<div class="pagging text-center"><ul class="custom-pagination-style-1 pagination pagination-rounded pagination-md justify-content-center">';
			$config['full_tag_close']   = '</ul></div>';
			$config['num_tag_open']     = '<li class="page-item"><span class="page-link">';
			$config['num_tag_close']    = '</span></li>';
			$config['cur_tag_open']     = '<li class="page-item active"><span class="page-link">';
			$config['cur_tag_close']    = '<span class="sr-only">(current)</span></span></li>';
			$config['next_tag_open']    = '<li class="page-item"><span class="page-link">';
			$config['next_tagl_close']  = '<span aria-hidden="true">&raquo;</span></span></li>';
			$config['prev_tag_open']    = '<li class="page-item"><span class="page-link">';
			$config['prev_tagl_close']  = '</span>Next</li>';
			$config['first_tag_open']   = '<li class="page-item"><span class="page-link">';
			$config['first_tagl_close'] = '</span></li>';
			$config['last_tag_open']    = '<li class="page-item"><span class="page-link">';
			$config['last_tagl_close']  = '</span></li>';
			
			return $config;
	}
	
	function buildTree($parentId) {
		$branch = array();
		
		$this->CI->db->select('master_menu_frontend.*');
		$this->CI->db->where("master_menu_frontend.parent",$parentId);
		$this->CI->db->where('master_menu_frontend.active',1);
		$this->CI->db->order_by('master_menu_frontend.sort','ASC');
		$query = $this->CI->db->get('master_menu_frontend');
		$query = $query->result_array();
		if($query){
			foreach ($query as $elements) {
				if ($elements['parent'] == $parentId) {
					$children = $this->buildTree($elements['id']);
					if ($children) {
						$elements['children'] = $children;
					}else{
						$elements['children'] = null;
					}
					$branch[] = $elements;
				}
			}
		}
		


		return $branch;
	}
 
	
	function penyebut($nilai) {
		$nilai = abs(intval($nilai));
		$huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
		$temp = "";
		if ($nilai < 12) {
			$temp = " ". $huruf[$nilai];
		} else if ($nilai <20) {
			$temp = $this->penyebut($nilai - 10). " belas";
		} else if ($nilai < 100) {
			$temp = $this->penyebut($nilai/10)." puluh". $this->penyebut($nilai % 10);
		} else if ($nilai < 200) {
			$temp = " seratus" . $this->penyebut($nilai - 100);
		} else if ($nilai < 1000) {
			$temp = $this->penyebut($nilai/100) . " ratus" . $this->penyebut($nilai % 100);
		} else if ($nilai < 2000) {
			$temp = " seribu" . $this->penyebut($nilai - 1000);
		} else if ($nilai < 1000000) {
			$temp = $this->penyebut($nilai/1000) . " ribu" . $this->penyebut($nilai % 1000);
		} else if ($nilai < 1000000000) {
			$temp = $this->penyebut($nilai/1000000) . " juta" . $this->penyebut($nilai % 1000000);
		} else if ($nilai < 1000000000000) {
			$temp = $this->penyebut($nilai/1000000000) . " milyar" . $this->penyebut(fmod($nilai,1000000000));
		} else if ($nilai < 1000000000000000) {
			$temp = $this->penyebut($nilai/1000000000000) . " trilyun" . $this->penyebut(fmod($nilai,1000000000000));
		}     
		return $temp;
	}
 
	function terbilang($nilai) {
		if($nilai<0) {
			$hasil = "minus ". trim($this->penyebut($nilai));
			return $hasil.' rupiah';
		}elseif($nilai==0) {
			$hasil = '';
			return '-';
		} else {
			$hasil = trim($this->penyebut($nilai));
			return $hasil.' rupiah';
		}     		
		
	}
	
	function replacetitikkoma($nilai) {
		$pos = strrpos($nilai, ".");
		if ($pos === false) { // note: three equal signs
			$nilai = number_format((float)$nilai, 0, '.', '');
		}else{
			$nilai = number_format((float)$nilai, 3, '.', '');
			$nilai = str_replace('.',',',$nilai);
		}
		
		return $nilai;
		
	}
	
	
	function weeks($month, $year){
			$firstday = date("w", mktime(0, 0, 0, $month, 1, $year)); 
			$lastday = date("t", mktime(0, 0, 0, $month, 1, $year)); 
			$count_weeks = 1 + ceil(($lastday-7+$firstday)/7);
			return $count_weeks;
		}
		
		function weeks_in_month($date) {
			$custom_date = strtotime( date('Y-m-d', strtotime($date)) ); 
			$week_start = date('Y-m-d', strtotime('this week last sunday', $custom_date));
			$week_end = date('Y-m-d', strtotime('this week next monday', $custom_date));
			
			$month = date("m",strtotime($week_end));
		
			return array(
					'month' => $month,
					'week_start' => $week_start,
					'week_end' => $week_end,
			);
		}
		
		function getDatefrommonth($datenya, $bulan_nya, $minggu_ke){
			$date_array = array();
			$month = $bulan_nya;
			while($month == $bulan_nya){
				$dateawal = $datenya;
				$weeknya2 = $this->weeks_in_month($datenya);
				$datenya = $weeknya2['week_end'];
				$month = $weeknya2['month'];
				$datanya = array(
					'start' => $dateawal,
					'end' => $datenya,
				);
				array_push($date_array, $datanya);
			}
			
			if(count($date_array) > 0){
				if(isset($date_array[$minggu_ke])){
					return $date_array[$minggu_ke];
				}else{
					return null;
				}
			}else{
				return null;
			}
			
		}
		
		function proses_upload_dok($pathdir = null, $tipedir = null){
			$user_id = $this->CI->input->post('user_id', true);
			$file_name = $this->CI->input->post('file_name', true);
			
			try {
				
				if($pathdir == null){
					// Tentukan direktori penyimpanan file
					$dir = './file/'.date('Y').'/'.date('m').'/file/'.date('Y').'/'.date('m').'/'.date('d');
					$path = 'file/'.date('Y').'/'.date('m').'/file/'.date('Y').'/'.date('m').'/'.date('d');
				}else{
					if($tipedir != null){
						$dir = './'.$pathdir.'/'.$tipedir.'/'.date('Y').'/'.date('m').'/file/'.date('Y').'/'.date('m').'/'.date('d');
						$path = $pathdir.'/'.$tipedir.'/'.date('Y').'/'.date('m').'/file/'.date('Y').'/'.date('m').'/'.date('d');
					}else{
						$tipedir = 'custom';
						$dir = './'.$pathdir.'/'.$tipedir.'/'.date('Y').'/'.date('m').'/file/'.date('Y').'/'.date('m').'/'.date('d');
						$path = $pathdir.'/'.$tipedir.'/'.date('Y').'/'.date('m').'/file/'.date('Y').'/'.date('m').'/'.date('d');
					}
				}
				
				
				if(!file_exists($dir)){
					mkdir($dir, 0755, true);
				}

				// Path untuk file yang diupload
				
				$config['upload_path'] = $dir;

				// Tentukan nama file yang aman
				if($file_name != '' && $file_name != null){
					$namereplace = $_FILES["userfile"]['name'];
					$dname = explode(".", $namereplace);
					$ext = end($dname);
					//$namereplace = $this->_clean_special($file_name);
					$namereplace = str_replace('.'.$ext, '', $namereplace);
					$namereplace = $this->_clean_special($namereplace);
				} else {
					$namereplace = $_FILES["userfile"]['name'];
					$dname = explode(".", $namereplace);
					$ext = end($dname);
					$namereplace = str_replace('.'.$ext, '', $namereplace);
					$namereplace = $this->_clean_special($namereplace);
				}
				
				// Batasi panjang nama maksimal 50 karakter
				$namereplace = substr($namereplace, 0, 30);

				// Tambahkan hash unik (contoh: berdasarkan waktu saat ini)
				$hash = substr(md5(time() . rand()), 0, 8); // hash pendek 8 karakter

				// Gabungkan nama + hash + ekstensi
				$namereplace = $namereplace . '-' . $hash;
				
				$config['file_name'] = $namereplace . '.' . $ext;
				$config['allowed_types'] = 'pdf|xls|xlsx|dwg|dxf|dwf|zip|rar|png|jpg|jpeg|gif|docx|doc'; // Tentukan jenis file yang diperbolehkan
				
				// Maksimal ukuran file 10 MB
				$config['max_size'] = 50240;  // ukuran dalam KB (10MB)
				
				$this->CI->load->library('upload', $config);
				
				// Validasi MIME Type menggunakan finfo_file
				$file_info = finfo_open(FILEINFO_MIME_TYPE);
				$mime_type = finfo_file($file_info, $_FILES['userfile']['tmp_name']);
				finfo_close($file_info);

				$allowed_mime_types = [
					'application/pdf',
					'image/png',
					'image/jpeg',
					'image/gif',
					//'application/zip',
					//'application/x-rar-compressed',
					'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // .xlsx
					'application/vnd.ms-excel', // .xls
					'application/msword', // .doc
					'application/vnd.openxmlformats-officedocument.wordprocessingml.document' // .docx
				];
				
				if (!in_array($mime_type, $allowed_mime_types)) {
					$result = array("csrf_hash" => $this->CI->security->get_csrf_hash(),"message" => "Invalid file type.", "m" => "Invalid file type.");
					return json_encode($result);
				}

				// Cek apakah file upload valid
				if($this->CI->upload->do_upload('userfile')) {
					// Mendapatkan data file setelah diupload
					$token = $this->CI->input->post('token_foto', true);
					$nama = $this->CI->upload->data('file_name', true);
					//$nama = $namereplace;
					$size = $this->CI->upload->data('file_size', true);
					
					// Memastikan file gambar memiliki dimensi yang benar
					$image_info = getimagesize($_FILES['userfile']['tmp_name']);
					if ($image_info) {
						// Cek jika dimensi file gambar terlalu besar
						if ($image_info[0] > 5000 || $image_info[1] > 5000) {
							$result = array("csrf_hash" => $this->CI->security->get_csrf_hash(),"message" => "Image dimensions too large.", "m" => "Image dimensions too large.");
							return json_encode($result);
						}
					}

					$data = array(
						'name' => $nama,
						'file_size' => $size * 1000,
						'token' => $token,
						'path' => $path . '/' . $nama,
						'path_server' => $dir . '/' . $nama,
						'createdid' => $user_id,
						'created' => date('Y-m-d H:i:s'),
						'modifiedid' => $user_id,
						'modified' => date('Y-m-d H:i:s'),
						'file_store_format' => $ext,
						'url_server' => base_url()
					);
					
					// Simpan data ke database
					$insert = $this->CI->db->insert('data_gallery', $data);
					$insertid = $this->CI->db->insert_id();
					
					if ($insert) {
						// Buat link untuk mengakses dokumen
						$link = base_url().'data_gallery/viewdokumen?path='.$path .'/'.$nama.'&tipe='.$ext.'&token='.$token;
						$encodedlink = encrypt_short($link);
						
						// Kembalikan hasil dalam format JSON
						$result = array(
							"message" => "success",
							"csrf_hash" => $this->CI->security->get_csrf_hash(),
							'id' => $insertid,
							'name' => $nama,
							'path' => base_url().$path .'/'.$nama,
							'link' => base_url().'dokumenview/'.$encodedlink,
							"extention" => strtolower($ext)
						);
						return json_encode($result);
					} else {
						$result = array("csrf_hash" => $this->CI->security->get_csrf_hash(),"message" => "Data not insert storage", "m" => "Data not insert storage");
						return json_encode($result);
					}
				} else {
					// Jika gagal upload, tampilkan error
					$result = array("csrf_hash" => $this->CI->security->get_csrf_hash(),"message" => "Proses upload gagal ".$this->CI->upload->display_errors(), "m" => $this->CI->upload->display_errors());
					return json_encode($result);
				}
			} catch (Error $e) {
				$result = array(
					"csrf_hash" => $this->CI->security->get_csrf_hash(),
					"message" => "Proses upload gagal. ".$e->getMessage(),
					"error" => $e->getMessage(),
					"id" => null
				);
				return json_encode($result);
			} catch (Exception $e) {
				$result = array(
					"csrf_hash" => $this->CI->security->get_csrf_hash(),
					"message" => "Proses upload gagal. ". $e->getMessage(),
					"error" => $e->getMessage(),
					"id" => null
				);
				return json_encode($result);
			}
		}
		
		
		function proses_upload($pathdir = null, $tipedir = null){
			
			return $this->proses_upload_dok($pathdir, $tipedir);

		}
		
		function select2custom($id,$name,$q,$table,$reference = null,$reference_id = null){
    
    if($reference == '' || $reference == null || $reference == '-'){
        $reference = null;
    }
    
    if($reference_id == '' || $reference_id == null || $reference_id == '-'){
        $reference_id = null;
    }
    
    // Deteksi database driver
    $is_postgre = ($this->CI->db->dbdriver == 'postgre' || $this->CI->db->dbdriver == 'postgre');
    
    if($table == 'master_product_hs'){
        $selectnya = $id.' as id,'.$name.' as name, name as namenya, code';
    }elseif($table == 'master_kbli'){
        $selectnya = $id.' as id,'.$name.' as name, name as namenya, code';
    }else{
        $selectnya = $id.' as id,'.$name.' as name';
    }
    
    $this->CI->db->select($selectnya);
    
    if($table == 'users_data'){
        if($reference != null && $reference_id != null){
            if($reference_id == 'disposisi'){
                $this->CI->db->where_in('gid',array(5,7,9));
            }else{
                $this->CI->db->where($reference_id,$reference);
            }
        }else{
            if($reference != null && $reference_id != null){
                $this->CI->db->where($reference_id,$reference);
            }
        }
    }else{
        
        if($table == 'master_unit'){
            $group_id = $this->CI->session->userdata('group_id');
            $unit_id = $this->CI->session->userdata('unit_id');
            if($group_id == 3){
                if($unit_id != null && $unit_id != ''){
                    $this->CI->db->where('id',$this->CI->session->userdata('unit_id'));
                }else{
                    $this->CI->db->where('id',0);
                }
            }elseif($group_id == 5){
                if($unit_id != null && $unit_id != ''){
                    $this->CI->db->where('id',$this->CI->session->userdata('unit_id'));
                }else{
                    $this->CI->db->where('id',0);
                }
            }
            
            if($reference != null && $reference_id != null){
                $this->CI->db->where($reference_id,$reference);
            }
        }elseif($table == 'master_area_provinsi'){
            $group_id = $this->CI->session->userdata('group_id');
            $area_provinsi_id = $this->CI->session->userdata('area_provinsi_id');
            if($group_id == 3){
                if($area_provinsi_id != null && $area_provinsi_id != ''){
                    $this->CI->db->where('id',$this->CI->session->userdata('area_provinsi_id'));
                }else{
                    $this->CI->db->where('id',0);
                }
            }elseif($group_id == 5){
                if($area_provinsi_id != null && $area_provinsi_id != ''){
                    $this->CI->db->where('id',$this->CI->session->userdata('area_provinsi_id'));
                }else{
                    $this->CI->db->where('id',0);
                }
            }
            
            if($reference != null && $reference_id != null){
                $this->CI->db->where($reference_id,$reference);
            }
        }elseif($table == 'master_area_kota'){
            if($reference == 39){
                //$this->CI->db->where_in('gid',array(5,7,9));
            }else{
                $this->CI->db->where($reference_id,$reference);
            }
        }elseif($table == 'users_groups'){
            $this->CI->db->where('id !=',1);
            
            if($reference != null && $reference_id != null){
                $this->CI->db->where($reference_id,$reference);
            }
            
        }else{
            if($reference != null && $reference_id != null){
                $this->CI->db->where($reference_id,$reference);
            }
        }
    }
    
    // Cek apakah kolom 'active' ada di tabel - Menggunakan field_exists() CodeIgniter
    if($this->CI->db->field_exists('active', $table)){
        $this->CI->db->where('active', 1);
    }

    // Handle LIKE query based on database driver
    if ($table == 'master_product_hs' || $table == 'master_kbli') {
        if($is_postgre){
            // PostgreSQL: gunakan ILIKE untuk case-insensitive search
            $escaped_q = $this->CI->db->escape_like_str($q);
            $this->CI->db->group_start();
            $this->CI->db->where("(code ILIKE '%{$escaped_q}%' OR name ILIKE '%{$escaped_q}%')");
            $this->CI->db->group_end();
        }else{
            // MySQL: gunakan LIKE biasa
            $this->CI->db->group_start();
            $this->CI->db->like('code', $q);
            $this->CI->db->or_like('name', $q);
            $this->CI->db->group_end();
        }
    } else {
        if($is_postgre){
            // PostgreSQL: gunakan ILIKE untuk case-insensitive search
            $escaped_q = $this->CI->db->escape_like_str($q);
            $this->CI->db->where("{$name} ILIKE '%{$escaped_q}%'");
        }else{
            // MySQL: gunakan LIKE biasa
            $this->CI->db->like($name, $q);
        }
    }
    
    $this->CI->db->limit(20);
    $query = $this->CI->db->get($table);
    $query = $query->result_array();
    
    if($query){
        $i=0;
        foreach ($query as $rows){
            $data[$i]['id'] = $rows['id'];
            
            if($table != 'master_product_hs' && $table != 'master_kbli'){
                $data[$i]['name']= $this->getFormatData($table,$name, $rows['name']);
            }else{
                $data[$i]['name']= $rows['code'].' - '.$rows['name'];
            }
            
            $i++;
        }
        $data = array('csrf_hash' =>$this->CI->security->get_csrf_hash(),'items' => $data);
    }else{
        $data = array('csrf_hash' =>$this->CI->security->get_csrf_hash(),'items' => array());
    }
    
    return json_encode($data);
}
		
		function date_formatnya($date,$format){
			if($date == null || $date == ''){
				return null;
			}
			
			if($format == null){
				$format = "d F Y";
			}
			$date= date_create($date);
			return date_format($date,$format);
		}
		
		function setInbox($from_id, $to_id, $category_id, $subject, $message, $data_id ,$is_wa, $tipe = 1){
			
			if($from_id && $to_id && $subject && $message){
				$data = array(
					'from_id'		=> $from_id,
					'to_id'			=> $to_id,
					'category_id'	=> $category_id,
					'subject'		=> $subject,
					'message'		=> $message,
					'data_id'		=> $data_id,
					//'note'			=> $note,
					'is_read'		=> 0,
					'is_email'		=> 1,
					'createdid'		=> $from_id,
					'created'		=> date('Y-m-d H:i:s'),
					'modifiedid'	=> $from_id,
					'modified'		=> date('Y-m-d H:i:s'),
				);
						
				$insert = $this->CI->db->insert('data_inbox',$data);
				$insertid = $this->CI->db->insert_id();	
				
				if($insert){
					$inbox_id = $insertid;
					//$pengirim = $this->select2_getname($from_id,'users_data','id','notelp');
					//$penerima = $this->select2_getname($to_id,'users_data','id','notelp');
					//$desc_project = $this->select2_getname($no_io,'vw_data_project','project_io','desc_project');
					//$comment = $subject;
					///$this->setWA($inbox_id, $pengirim, $penerima, $no_io, $no_kontrak, $no_bast, //$desc_project, $comment);
					$slug = $from_id.$to_id.'-'.date('YmdHis').rand(1000,9999).'-inbox';
					$data = array(
						'slug'			=> $slug,
						'modifiedid'	=> $from_id,
						'modified'		=> date('Y-m-d H:i:s')
					);
					
					$this->CI->db->where('data_inbox.id',$insertid);					
					$insert = $this->CI->db->update('data_inbox',$data);
					
					if($insert){
						//Kirim Email
						$this->CI->db->select('users_data.*');
						$this->CI->db->where('users_data.id',$to_id);
						$datauser = $this->CI->db->get('users_data');
						$datauser = $datauser->result_object();
						if($datauser){
							$this->CI->rabbitmq->publish([
								'tipe' => $tipe,
								'email' => $datauser[0]->email,
								'fullname' 	=> $datauser[0]->fullname,
								'subject' 	=> $subject,
								'message' 	=> $message,
								'attachment' => null,
								'inbox_id' => $inbox_id
							]);
							
							//Kirim telegram
							$this->CI->rabbitmq->publish([
								'tipe' => 6,
								'email' => $datauser[0]->email,
								'fullname' 	=> $datauser[0]->fullname,
								'subject' 	=> $subject,
								'message' 	=> $message,
								'attachment' => null,
								'inbox_id' => $inbox_id
							]);
						}

						//$this->sendMessage($subject, 'Input Data', $to_id);
					}
				
				}
				
				return $insertid;
			}
			
			return null;
		}
		
		function setWA($inbox_id, $pengirim, $penerima, $no_io, $no_kontrak, $no_bast, $desc_project, $comment){
			
			return false;
		}
		
		
		function getAksesEditNaming(){
			
			$CI =& get_instance();

			$user_id = $CI->session->userdata('userid');
			$gid = $this->select2_getname($user_id,'users_data','id','gid');
				 
			if($gid == 1 || $gid == 2){
				return true;
			}
			
			return false;
		}
		
		public function getaccessdrag(){
			$group_id = $this->CI->session->userdata('group_id');
			if($group_id == 1 || $group_id == 2){
				return true;
			}
			
			return false;
		}
		
		public function getaccessfilter(){
			
			return true;
			
			$group_id = $this->CI->session->userdata('group_id');
			if($gid == 1 || $gid == 2){
				return true;
			}
			
			return false;
		}
		
	
		
	public function getviewlistcontrol($table, $module, $exclude, $q = ''){
		
			$user_id = $this->CI->session->userdata('userid');
			$this->CI->db->select('translate_view_user.*');
			$this->CI->db->where('translate_view_user.table',$table);
			$this->CI->db->where('translate_view_user.table',$table);
			$this->CI->db->where('translate_view_user.module',$module);
			$this->CI->db->where('translate_view_user.active',1);
			$this->CI->db->where('translate_view_user.user_id',$user_id);
			$this->CI->db->order_by('translate_view_user.modified','DESC');
			$this->CI->db->limit(1);
			$queryexclude = $this->CI->db->get('translate_view_user');
			$queryexclude = $queryexclude->result_object();
			if($queryexclude){
				//
			}else{
				$this->CI->db->select('translate_view.*');
				$this->CI->db->where('translate_view.table',$table);
				$this->CI->db->where('translate_view.module',$module);
				$this->CI->db->where('translate_view.active',1);
				$this->CI->db->order_by('translate_view.modified','DESC');
				$this->CI->db->limit(1);
				$queryexclude = $this->CI->db->get('translate_view');
				$queryexclude = $queryexclude->result_object();
			}
			
			
			if($queryexclude){
				if($queryexclude[0]->data != 'null' && $queryexclude[0]->data != '' && $queryexclude[0]->data != null){
					$exclude = json_decode($queryexclude[0]->data);
					if($queryexclude[0]->data_order != null && $queryexclude[0]->data_order != ''){
						$query_column = $this->query_column_filter($table, $exclude, $queryexclude[0]->data_order);
					}else{
						$query_column = $this->query_column_filter($table, $exclude, null);
					}
					
				}else{
					if ($this->str_conten($table, 'vw_')) { 
						if($queryexclude[0]->data_order != null && $queryexclude[0]->data_order != ''){
							$query_column = $this->query_column_nosort($table, $exclude, $queryexclude[0]->data_order);
						}else{
							$query_column = $this->query_column_nosort($table, $exclude, null);
						}
						
					}else{
						if($queryexclude[0]->data_order != null && $queryexclude[0]->data_order != ''){
							$query_column = $this->query_column($table, $exclude, $queryexclude[0]->data_order);
						}else{
							$query_column = $this->query_column($table, $exclude, null);
						}
						
					}
					
				}
			}else{
				if ($this->str_conten($table, 'vw_')) { 
					$query_column = $this->query_column_nosort($table, $exclude, null);
				}else{
					$query_column = $this->query_column($table, $exclude, null);
				}
					
			}
			
			return $query_column;
	}
	
	public function getRefDataTable($tabel,$table_id){
		$this->CI->db->select('translate.*');
		$this->CI->db->where('translate.meta_id',$table_id);
		$this->CI->db->where('translate.meta_table',$tabel);
		$this->CI->db->where('translate.active',1);
		$queryexclude = $this->CI->db->get('translate');
		$queryexclude = $queryexclude->result_object();
		if($queryexclude){
			if($queryexclude[0]->meta_tipe == 'SELECT'){
				return array($queryexclude[0]->meta_table_ref,
								$queryexclude[0]->meta_table_id_ref,
								$queryexclude[0]->meta_table_name_ref,
								$queryexclude[0]->meta_value
				);
			}else{
				return array($tabel,$table_id,$table_id,$table_id);
			}
		}
		
		return array($tabel,$table_id,$table_id,$table_id);
	}
	
	public function getTipeData($tabel,$table_id){
		$this->CI->db->select('translate.*');
		$this->CI->db->where('translate.meta_id',$table_id);
		$this->CI->db->where('translate.meta_table',$tabel);
		$this->CI->db->where('translate.active',1);
		$queryexclude = $this->CI->db->get('translate');
		$queryexclude = $queryexclude->result_object();
		if($queryexclude){
			return $queryexclude[0]->meta_tipe;
		}
		
		return null;
	}
	
	public function getFormatData($tabel,$table_id, $value){
		$text = $value;
		$this->CI->db->select('translate.*');
		$this->CI->db->where('translate.meta_id',$table_id);
		$this->CI->db->where('translate.meta_table',$tabel);
		$this->CI->db->where('translate.active',1);
		$queryexclude = $this->CI->db->get('translate');
		$queryexclude = $queryexclude->result_object();
		if($queryexclude){
			
			
			if($queryexclude[0]->meta_tipe == 'DATE'){
				$datenya = $this->validateDate($value, $format = 'Y-m-d H:i:s');
				//return $datenya;
				if($datenya){
					$text = $this->format_date($value);
				}else{
					$datenya = $this->validateDate($value, $format = 'Y-m-d');
					if($datenya){
						$text = $this->format_date($value);
					}
				}
			}elseif($queryexclude[0]->meta_tipe == 'DATETIME'){
				$datenya = $this->validateDate($value, $format = 'Y-m-d H:i:s');
				if($datenya){
					$text = $this->format_datetime($value);
				}
			}elseif($queryexclude[0]->meta_tipe == 'DATETIMEZONE'){
				$datenya = $this->validateDate($value, $format = 'Y-m-d H:i:s');
				if($datenya){
					$range = $this->getTimezoneRange();
					$text = $this->format_datetimezone($value, $range);
				}
			}elseif($queryexclude[0]->meta_tipe == 'CURRENCY'){
				$datenya = $this->validateAngka($value);
				if($datenya){
					$text = $this->rupiahnonkoma($value);
				}
			}elseif($queryexclude[0]->meta_tipe == 'FILE'){
				$variable = $value;
				if($variable != '' && $variable != null && $variable != '-'){
					$file = $this->getcoverdata($variable);
					if($file['message'] == 'success'){
						$link = $file['data'][0]['link'];
						$short = substr($link, 0, 30) . (strlen($link) > 30 ? '...' : '');
						$text = '<div class="d-flex align-items-center gap-2"><a target="_balnk" href="'.$file['data'][0]['link'].'" class="text-primary link-preview-trigger" data-url="'.$file['data'][0]['link'].'" data-title="Preview: '.$file['data'][0]['link'].'">
										<i class="fas fa-link"></i> 
										'.$short.' 
									</a><a href="'.$file['data'][0]['link'].'" target="_blank" class="text-muted" title="Buka di tab baru">
										<i class="fas fa-external-link-alt"></i>
									</a></div>';
					}else{
						$text = '-';
					}
				}
			}
		}
		
		return $text;
	}
	
	function getcoverdata($id){
			$fieldnya = array();
			//$dwt_id =$this->input->post('id');
			//$id =$this->input->post('id');
			//$tableid =$this->input->post('tableid');
			
			$this->CI->db->select('data_gallery.*');
			$this->CI->db->where('data_gallery.id',$id);
			$querystatus = $this->CI->db->get('data_gallery');
			$querystatus = $querystatus->result_object();
			//print_r($this->CI->db->last_query());
			if($querystatus){
				foreach ($querystatus as $rows) {
					
					$link = base_url().'data_gallery/viewdokumen?path='.$rows->path.'&tipe='.$rows->file_store_format.'&token='.$rows->token;
					$encodedlink = encrypt_short($link);
					
					$datanya = array(
						'id' => $rows->id,
						'name' => $rows->name, 	
						'path' => $rows->url_server.$rows->path,
						'size' => $rows->file_size/1000, 		
						'link' => base_url().'dokumenview/'.$encodedlink,						
						"extention" =>  $rows->file_store_format, 
					);
					
					array_push($fieldnya, $datanya);
				}
				
				$result = array("csrf_hash" => $this->CI->security->get_csrf_hash(),"message" => "success",'data' => $fieldnya);
				return $result;
			}else{
				$result = array("csrf_hash" => $this->CI->security->get_csrf_hash(),"message" => "error");
				return $result;
			}
			
		}
		
	function getthemes($id){
			$fieldnya = array();
			//$dwt_id =$this->input->post('id');
			//$id =$this->input->post('id');
			//$tableid =$this->input->post('tableid');
			
			$this->CI->db->select('data_gallery.*');
			$this->CI->db->where('master_themes.id',$id);
			$this->CI->db->where('master_themes.active',1);
			$this->CI->db->join('master_themes','master_themes.file_id = data_gallery.id');
			$querystatus = $this->CI->db->get('data_gallery');
			$querystatus = $querystatus->result_object();
			//print_r($this->CI->db->last_query());
			if($querystatus){
				if(file_exists(FCPATH. $querystatus[0]->path)){
					return $querystatus[0]->path;
				}
			}
			
			if($this->CI->session->userdata('group_id') != 3){
				return 'themes/ortyd/assets/media/patterns/header-bg.jpg';
			}else{
				return 'themes/ortyd/assets/media/patterns/header-bg-3.jpg';
			}
			
		}
	
	public function getRefData($tabel,$table_id){
		$this->CI->db->select('translate.*');
		$this->CI->db->where('translate.meta_id',$table_id);
		$this->CI->db->where('translate.meta_table',$tabel);
		$this->CI->db->where('translate.active',1);
		$queryexclude = $this->CI->db->get('translate');
		$queryexclude = $queryexclude->result_object();
		if($queryexclude){
			if($queryexclude[0]->meta_tipe == 'SELECT'){
				if($queryexclude[0]->meta_table_ref != null && $queryexclude[0]->meta_table_id_ref != null && $queryexclude[0]->meta_table_name_ref != null && $queryexclude[0]->meta_table_ref != '' && $queryexclude[0]->meta_table_id_ref != '' && $queryexclude[0]->meta_table_name_ref != ''){
					$tabel_ref = $queryexclude[0]->meta_table_ref;
					$table_id = $queryexclude[0]->meta_table_id_ref;
					$table_name = $queryexclude[0]->meta_table_name_ref;
					$nested = $queryexclude[0]->meta_nested;
					$nested_field_id = $queryexclude[0]->meta_nested_field_id;
					$nested_ref_id = $queryexclude[0]->meta_nested_ref_id;
					$nested_field_custom_id = $queryexclude[0]->meta_nested_field_custom_id;
					return array($tabel_ref,$table_id,$table_name,$nested,$nested_field_id,$nested_ref_id,$nested_field_custom_id);
				}
			}
		}
		
		return null;
	}
	
	public function getRefDataWidth($tabel,$table_id){
		$this->CI->db->select('translate.*');
		$this->CI->db->where('translate.meta_id',$table_id);
		$this->CI->db->where('translate.meta_table',$tabel);
		$this->CI->db->where('translate.active',1);
		$queryexclude = $this->CI->db->get('translate');
		$queryexclude = $queryexclude->result_object();
		if($queryexclude){
			if($queryexclude[0]->meta_size != '' && $queryexclude[0]->meta_size != null){
				return $queryexclude[0]->meta_size;
			}
		}
		
		return null;
	}
	
	function validateAngka($value){
		if(preg_match('/^\d+$/',$value ?? '')) {
			return true;
		} 
		
		return false;
	}
	
	function validateDate($date, $format = 'Y-m-d')
	{
		$d = DateTime::createFromFormat($format, $date ?? '');
		// The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
		return $d && $d->format($format) === $date;
	}
	
	public function getviewlistcheck($tablenya, $exclude,$module){
		
		$modulview = "'".$module."'";
		$tabelview = "'".$tablenya."'";
												
		$li ='<div style="background: #d62e2e;border-radius: 10px;margin-top: 5px;" class="menu-item px-3"><div class="menu-content fs-6 text-dark fw-bold px-2 py-2" style="color:#fff !important">Daftar Kolom</div></div>';
		
		$ordernya = null;
		
		$user_id = $this->CI->session->userdata('userid');
		$this->CI->db->select('translate_view_user.*');
		$this->CI->db->where('translate_view_user.table',$tablenya);
		$this->CI->db->where('translate_view_user.module',$module);
		$this->CI->db->where('translate_view_user.user_id',$user_id);
		$this->CI->db->where('translate_view_user.active',1);
		$this->CI->db->order_by('translate_view_user.modified','DESC');
		$this->CI->db->limit(1);
		$queryexclude = $this->CI->db->get('translate_view_user');
		$queryexclude = $queryexclude->result_object();
		if($queryexclude){
			//
		}else{
			$this->CI->db->select('translate_view.*');
			$this->CI->db->where('translate_view.table',$tablenya);
			$this->CI->db->where('translate_view.module',$module);
			$this->CI->db->where('translate_view.active',1);
			$this->CI->db->order_by('translate_view.modified','DESC');
			$this->CI->db->limit(1);
			$queryexclude = $this->CI->db->get('translate_view');
			$queryexclude = $queryexclude->result_object();
		}
		
		if($queryexclude){
			if($queryexclude[0]->data_order != null && $queryexclude[0]->data_order != ''){
				$ordernya = $queryexclude[0]->data_order;
			}
		}
			
		if ($this->str_conten($tablenya, 'vw_')) { 
			//$query_column = $this->getviewlistcontrol($tablenya, $module, $exclude);
			$exclude = array();
			$query_column = $this->query_column($tablenya, $exclude, $ordernya);
		}else{
			//$query_column = $this->getviewlistcontrol($tablenya, $module, $exclude);
			$exclude = array();
			$query_column = $this->query_column($tablenya, $exclude, $ordernya);
		}
		
		$ordernya_list = [];		
		if($ordernya != null){
			$tags = explode(',',$ordernya);
			$xx=0;
			foreach($tags as $key) {
				$label_name = $this->translate_column($tablenya,$key);
				array_push($ordernya_list, array(
					'labelname' => $label_name,
					'labelid' => $key
					)
				);
			}
		}
				
				
												$datalist=[];
												if($query_column){
													$x=1;
													$li = $li.'<div class="menu-item px-3" style="margin-top:10px;    border-bottom: 1px solid #e2e5e7;"><div class="menu-content fs-6 text-dark fw-bold px-0 py-0"><input type="checkbox" class="checkall_check form-check-input" id="checkall_check"> Check All</div></div>';
													foreach($query_column as $rows_column){
														
														$datasame = 0;
														if(count($ordernya_list) >0){
															foreach($ordernya_list as $rowslist) {
																if($rows_column['name'] == $rowslist['labelid']){
																	$datasame = 1;
																	break;
																}
															}
														}
														
														if($datasame == 0){
															$label_name = $this->translate_column($tablenya,$rows_column['name']);
															array_push($datalist, array(
																	'labelname' => $label_name,
																	'labelid' => $rows_column['name']
																)
															);
														}

														$x++;
													}
													
													if(count($ordernya_list) >0 && count($datalist) > 0){
														//usort($ordernya_list, array($this,'compareByName'));
														usort($datalist, array($this,'compareByName'));
														$datalist = array_merge($ordernya_list, $datalist);
													}else{
														usort($datalist, array($this,'compareByName'));
													}
													
													if(count($datalist) >0){
														//usort($datalist, array($this,'compareByName'));
														foreach($datalist as $rowsnya){
															if($rowsnya['labelid'] && $rowsnya['labelname'] != 'active'){
																if($this->checkarrayview($tabelview, $modulview,$rowsnya['labelid']) == true){
																	$li = $li.'<div class="menu-item px-3"><div class="menu-content fs-6 text-dark fw-bold px-0 py-0"><input type="checkbox" class="form-check-input checkone_check" name="checkbox_table[]" value="'.$rowsnya['labelid'].'" checked="checked"> '.$rowsnya['labelname'].'</div></div>';
																}else{
																	$li = $li.'<div class="menu-item px-3"><div class="menu-content fs-6 text-dark fw-bold px-0 py-0"><input type="checkbox" class="form-check-input checkone_check" name="checkbox_table[]" value="'.$rowsnya['labelid'].'"> '.$rowsnya['labelname'].'</div></div>';
																}
																
															}
														}
													}
													
												}
												
		$li = $li.'<div class="menu-item px-3"><button onClick="savingTableView('.$modulview.','.$tabelview.')" type="button" class="btn btn-danger btn-view-list"><i class="fa fa-save"></i> Simpan</button></div>';
												
		return $li;
	}
	
	function compareByName($a, $b) {
	  return strcmp($a["labelname"], $b["labelname"]);
	}

	public function checkarrayview($tablenya, $module, $data_id){
		
		$tablenya = str_replace("'",'',$tablenya);
		$module = str_replace("'",'',$module);
		
		$user_id = $this->CI->session->userdata('userid');
		$this->CI->db->select('translate_view_user.*');
		$this->CI->db->where('translate_view_user.table',$tablenya);
		$this->CI->db->where('translate_view_user.module',$module);
		$this->CI->db->where('translate_view_user.user_id',$user_id);
		$this->CI->db->where('translate_view_user.active',1);
		$this->CI->db->order_by('translate_view_user.modified','DESC');
		$this->CI->db->limit(1);
		$queryexclude = $this->CI->db->get('translate_view_user');
		$queryexclude = $queryexclude->result_object();
		if($queryexclude){
			//
		}else{
			$this->CI->db->select('translate_view.*');
			$this->CI->db->where('translate_view.table',$tablenya);
			$this->CI->db->where('translate_view.module',$module);
			$this->CI->db->where('translate_view.active',1);
			$this->CI->db->order_by('translate_view.modified','DESC');
			$this->CI->db->limit(1);
			$queryexclude = $this->CI->db->get('translate_view');
			$queryexclude = $queryexclude->result_object();
		}
		
		
		if($queryexclude){
			if($queryexclude[0]->data != 'null' && $queryexclude[0]->data != '' && $queryexclude[0]->data != null){
				$exclude = json_decode($queryexclude[0]->data, true);
				if (in_array($data_id, $exclude)){
					return true;
				}
			}	
		}

		return false;
	}	
	
	function commentNumber($data_id, $field_id, $type_id){
		$CI =& get_instance();
		$CI->db->select('count(data_dwt_detail_comment.id) as total');
		$CI->db->where('data_dwt_detail_comment.field_id', $field_id);
		$CI->db->where('data_dwt_detail_comment.detail_id', $data_id);
		$CI->db->where('data_dwt_detail_comment.type_id', $type_id);
		$CI->db->where('data_dwt_detail_comment.status_id', 1);
		$query = $CI->db->get('data_dwt_detail_comment');
		$query = $query->result_object();
		if($query){
			return $query[0]->total;
		}else{
			return 0;
		}
	}
	
	function getQuery($id){
		$CI =& get_instance();
		$CI->db->select('vw_query.query');
		$CI->db->where('vw_query.id', $id);
		$query = $CI->db->get('vw_query');
		$query = $query->result_object();
		if($query){
			return $query[0]->query;
		}else{
			return null;
		}
	}
	
	function selectQuery($id){
		
		$select = $this->getQuery($id);
		
		if($select != null){
			$CI =& get_instance();
			$query = $CI->db->query($select);
			$query = $query->result_object();
			if($query){
				return $query;
			}else{
				return $CI->db->error();
			}
		}
		
	}
	
	function getdatabyname($name,$table){
		$user_id = $this->CI->session->userdata('userid');
		
		$this->CI->db->where('lower(name)',strtolower($name));
		$query = $this->CI->db->get($table);
		$query = $query->result_object();
		if(!$query){
			$datarow = array(  
				'name'				=> $name,
				'description'		=> $name,
				'active'			=> 1,
				'createdid'			=> $user_id,
				'created'			=> date('Y-m-d H:i:s'),
				'modifiedid'		=> $user_id,
				'modified'			=> date('Y-m-d H:i:s'),
			);
								
			$insert = $this->CI->db->insert($table, $datarow);
			$insert_id = $this->CI->db->insert_id();
			if($insert){
				return $insert_id;
			}
		}else{
			return $query[0]->id;
		}
			
		return 0;
	}
	
	function format_date($date){
		$date=date_create($date ?? '');
		return date_format($date,"d M, Y");
	}
	
	function format_hanyadate($date){
		$date=date_create($date ?? '');
		return date_format($date,"Y-m-d");
	}
	
	function format_datetime($datetime){
		$datetime=date_create($datetime ?? '');
		return date_format($datetime,"d M, Y H:i:s");
	}

	function str_conten($haystack, $needle){
		if (!function_exists('str_contains')) {
			/**
			 * Check if substring is contained in string
			 *
			 * @param $haystack
			 * @param $needle
			 *
			 * @return bool
			 */
			return strpos($haystack ?? '', $needle ?? '');
		}else{
			return str_contains($haystack ?? '', $needle ?? '');
		}
	}
	
	public function sendEmail($email, $fullname, $subject, $message, $attachment){
		
		 //$config = [
				//'mailtype'  => 'html',
				//'charset'   => 'utf-8',
				//'protocol'  => 'smtp',
				//'smtp_host' => 'smtp.hostinger.com',
				//'smtp_user' => 'no-reply@nktdev.online',  // Email gmail
				//'smtp_pass'   => 'ASKmppkbppp#123',  // Password gmail
				//'smtp_crypto' => 'ssl',
				//'starttls' => TRUE,
				//'smtp_port'   => 465,
				//'crlf'    => "\r\n",
				//'newline' => "\r\n"
			//];
			
		//$config = [
				//'mailtype'  => 'html',
				//'charset'   => 'utf-8',
				//'protocol'  => 'smtp',
				//'smtp_host' => 'smtp.kemendag.go.id',
				//'smtp_user' => 'no_reply-kalibrasi@kemendag.go.id',  // Email gmail
				//'smtp_pass'   => 'ASKmppkbppp#123',  // Password gmail
				//'smtp_crypto' => 'ssl',
				//'starttls' => TRUE,
				//'smtp_port'   => 25,
				//'crlf'    => "\r\n",
				//'newline' => "\r\n"
		//];
			
		error_reporting(0);
		try {
			$config = [
				'mailtype'  => 'html',
				'charset'   => 'utf-8',
				'protocol'  => 'smtp',
				'smtp_host' => 'smtp.kemendag.go.id',
				'smtp_user' => 'inams@kemendag.go.id',  // Email gmail
				'smtp_pass'   => '#205Depdag',  // Password gmail
				'smtp_crypto' => 'security',
				'starttls' => TRUE,
				'smtp_port'   => 25,
				'crlf'    => "\r\n",
				'newline' => "\r\n"
			];

			// Load library email dan konfigurasinya
			$this->CI->load->library('email', $config);

			// Email dan nama pengirim
			$this->CI->email->from('inams@kemendag.go.id', 'inams');

			// Email penerima
			$this->CI->email->to($email); // Ganti dengan email tujuan

			// Lampiran email, isi dengan url/path file
			//$this->email->attach('https://masrud.com/content/images/20181215150137-codeigniter-smtp-gmail.png');

			// Subject email
			$this->CI->email->subject($subject);

			// Isi email
			$htmlnya = $this->formatemail($subject, $message);
			$this->CI->email->message($htmlnya);

			if($attachment != null && $attachment != ''){
				$attched_file= base_url().$attachment;
				$this->CI->email->attach($attched_file);
			}
			

			//$this->email->print_debugger();
			
			// Tampilkan pesan sukses atau error
			if ($this->CI->email->send()) {
				$this->CI->email->clear(TRUE);
				return true;
			} else {
				//$this->CI->email->clear(TRUE);
				//return 0;
				return $this->CI->email->print_debugger();
			}
			
		}catch(Exception $e) {
			return false;
		}
		
		
	}
	
	function unformatrp($nilai){
		
		if($nilai == null){
			return null;
		}
		
		$this->CI->db->where('active',1);
		$query = $this->CI->db->get('master_currency');
		$query = $query->result_object();
		if($query){
			foreach($query as $rows){
				$nilai = str_replace($rows->code.'. ','',$nilai);
			}
		}
		
		$nilai = str_replace('.','',$nilai);
		$nilai = str_replace(',','.',$nilai);
		
		return $nilai;
	}
	
	public function convertimgtobase64($path){
			$path=$path;
			$type = pathinfo($path, PATHINFO_EXTENSION);
			$data = file_get_contents($path);
			$base64=base64_encode($data);
			$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
			return $base64;
			//echo '<img src="'.$base64.'" />';
	}
	
		
	function converting_generate($text, $data, $tipe_id){
			
			if($tipe_id == 1){
				
				$dwt_alamat = $this->select2_getname($data['nama_mitra'],'data_dwt_register','dwt_nama','dwt_alamat');
				
				$data['alamat_mitra'] = $dwt_alamat;
				
				$converstring = str_replace('[[nomor_spph]]',$data['nomor_spph'],$text);
				$converstring = str_replace('[[tanggal_spph]]',$data['tanggal_spph'],$converstring);
				$converstring = str_replace('[[nama_mitra]]',$data['nama_mitra'],$converstring);
				$converstring = str_replace('[[alamat_mitra]]',$data['alamat_mitra'],$converstring);
				$converstring = str_replace('[[nama_pekerjaan]]',$data['nama_pekerjaan'],$converstring);
				$converstring = str_replace('[[mekanisme_pembayaran]]',$data['mekanisme_pembayaran'],$converstring);
				$converstring = str_replace('[[nama_pic_drafter]]',$data['nama_pic_drafter'],$converstring);
				$converstring = str_replace('[[email_pic_drafter]]',$data['email_pic_drafter'],$converstring);
				$converstring = str_replace('[[hari_submit]]',$data['hari_submit'],$converstring);
				$converstring = str_replace('[[tanggal_submit]]',$data['tanggal_submit'],$converstring);
				$converstring = str_replace('[[jam_submit]]',$data['jam_submit'],$converstring);
				$converstring = str_replace('[[jangka_waktu]]',$data['jangka_waktu'],$converstring);
				$converstring = str_replace('[[nama_vp]]',$data['nama_vp'],$converstring);
				$converstring = str_replace('[[jabatan_vp]]',$data['jabatan_vp'],$converstring);
			}elseif($tipe_id == 2){
				$converstring = str_replace('[[nomor_spph]]',$data['nomor_spph'],$text);
				$converstring = str_replace('[[tanggal_spph]]',$data['tanggal_spph'],$converstring);
				$converstring = str_replace('[[nama_mitra]]',$data['nama_mitra'],$converstring);
				$converstring = str_replace('[[alamat_mitra]]',$data['alamat_mitra'],$converstring);
				$converstring = str_replace('[[nama_pekerjaan]]',$data['nama_pekerjaan'],$converstring);
				$converstring = str_replace('[[sph_nomor]]',$data['sph_nomor'],$converstring);
				$converstring = str_replace('[[sph_tanggal]]',$data['sph_tanggal'],$converstring);
				$converstring = str_replace('[[sph_nilai]]',$data['sph_nilai'],$converstring);
			}elseif($tipe_id == 3){
				$converstring = str_replace('[[nomor_spph]]',$data['nomor_spph'],$text);
				$converstring = str_replace('[[tanggal_spph]]',$data['tanggal_spph'],$converstring);
				$converstring = str_replace('[[nama_mitra]]',$data['nama_mitra'],$converstring);
				$converstring = str_replace('[[alamat_mitra]]',$data['alamat_mitra'],$converstring);
				$converstring = str_replace('[[nama_pekerjaan]]',$data['nama_pekerjaan'],$converstring);
				$converstring = str_replace('[[mekanisme_pembayaran]]',$data['mekanisme_pembayaran'],$converstring);
				$converstring = str_replace('[[nama_pic_drafter]]',$data['nama_pic_drafter'],$converstring);
				$converstring = str_replace('[[email_pic_drafter]]',$data['email_pic_drafter'],$converstring);
				$converstring = str_replace('[[hari_submit]]',$data['hari_submit'],$converstring);
				$converstring = str_replace('[[tanggal_submit]]',$data['tanggal_submit'],$converstring);
				$converstring = str_replace('[[jam_submit]]',$data['jam_submit'],$converstring);
				$converstring = str_replace('[[bulan_submit]]',$data['bulan_submit'],$converstring);
				$converstring = str_replace('[[tahun_submit]]',$data['tahun_submit'],$converstring);
				$converstring = str_replace('[[target_pengadaan]]',$data['target_pengadaan'],$converstring);
				$converstring = str_replace('[[pekerjaan_mulai]]',$data['pekerjaan_mulai'],$converstring);
				$converstring = str_replace('[[pekerjaan_selesai]]',$data['pekerjaan_selesai'],$converstring);
				$converstring = str_replace('[[lokasi_pekerjaan]]',$data['lokasi'],$converstring);
				$converstring = str_replace('[[ruang_lingkup]]',$data['ruang_lingkup'],$converstring);
				$converstring = str_replace('[[nama_vp]]',$data['nama_vp'],$converstring);
				$converstring = str_replace('[[jabatan_vp]]',$data['jabatan_vp'],$converstring);
				$converstring = str_replace('[[sph_nomor]]',$data['sph_nomor'],$converstring);
				$converstring = str_replace('[[sph_tanggal]]',$data['sph_tanggal'],$converstring);
				$converstring = str_replace('[[sph_nilai]]',$data['sph_nilai'],$converstring);
				$converstring = str_replace('[[nilai_kesepakatan]]',$data['nilai_kesepakatan'],$converstring);
				$converstring = str_replace('[[syarat_penagihan]]',$data['syarat_penagihan'],$converstring);
				$converstring = str_replace('[[dok_akhir]]',$data['dok_akhir'],$converstring);
			}elseif($tipe_id == 4 || $tipe_id == 5 || $tipe_id == 6){
				$converstring = str_replace('[[nama_mitra]]',$data['nama_mitra'],$text);
				$converstring = str_replace('[[alamat_mitra]]',$data['alamat_mitra'],$converstring);
				$converstring = str_replace('[[nama_pekerjaan]]',$data['nama_pekerjaan'],$converstring);
				$converstring = str_replace('[[bakn_tanggal]]',$data['bakn_tanggal'],$converstring);
				$converstring = str_replace('[[direktur_mitra]]',$data['direktur_mitra'],$converstring);
				$converstring = str_replace('[[jabatan_direktur_mitra]]',$data['jabatan_direktur_mitra'],$converstring);
				$converstring = str_replace('[[npwp]]',$data['npwp'],$converstring);
				$converstring = str_replace('[[nilai_kesepakatan]]',$data['nilai_kesepakatan'],$converstring);
			}elseif($tipe_id == 7){
				$converstring = str_replace('[[nomor_spph]]',$data['nomor_spph'],$text);
				$converstring = str_replace('[[tanggal_spph]]',$data['tanggal_spph'],$converstring);
				$converstring = str_replace('[[nama_mitra]]',$data['nama_mitra'],$converstring);
				$converstring = str_replace('[[alamat_mitra]]',$data['alamat_mitra'],$converstring);
				$converstring = str_replace('[[nama_pekerjaan]]',$data['nama_pekerjaan'],$converstring);
				$converstring = str_replace('[[mekanisme_pembayaran]]',$data['mekanisme_pembayaran'],$converstring);
				$converstring = str_replace('[[nama_pic_drafter]]',$data['nama_pic_drafter'],$converstring);
				$converstring = str_replace('[[email_pic_drafter]]',$data['email_pic_drafter'],$converstring);
				$converstring = str_replace('[[hari_submit]]',$data['hari_submit'],$converstring);
				$converstring = str_replace('[[tanggal_submit]]',$data['tanggal_submit'],$converstring);
				$converstring = str_replace('[[jam_submit]]',$data['jam_submit'],$converstring);
				$converstring = str_replace('[[bulan_submit]]',$data['bulan_submit'],$converstring);
				$converstring = str_replace('[[tahun_submit]]',$data['tahun_submit'],$converstring);
				$converstring = str_replace('[[target_pengadaan]]',$data['target_pengadaan'],$converstring);
				$converstring = str_replace('[[pekerjaan_mulai]]',$data['pekerjaan_mulai'],$converstring);
				$converstring = str_replace('[[pekerjaan_selesai]]',$data['pekerjaan_selesai'],$converstring);
				$converstring = str_replace('[[lokasi_pekerjaan]]',$data['lokasi'],$converstring);
				$converstring = str_replace('[[ruang_lingkup]]',$data['ruang_lingkup'],$converstring);
				$converstring = str_replace('[[nama_vp]]',$data['nama_vp'],$converstring);
				$converstring = str_replace('[[jabatan_vp]]',$data['jabatan_vp'],$converstring);
				$converstring = str_replace('[[sph_nomor]]',$data['sph_nomor'],$converstring);
				$converstring = str_replace('[[sph_tanggal]]',$data['sph_tanggal'],$converstring);
				$converstring = str_replace('[[sph_nilai]]',$data['sph_nilai'],$converstring);
				$converstring = str_replace('[[nilai_kesepakatan]]',$data['nilai_kesepakatan'],$converstring);
				$converstring = str_replace('[[nomorrekening]]',$data['nomorrekening'],$converstring);
				$converstring = str_replace('[[namarekening]]',$data['namarekening'],$converstring);
				$converstring = str_replace('[[bankrekening]]',$data['bankrekening'],$converstring);
				$converstring = str_replace('[[spk_nomor]]',$data['spk_nomor'],$converstring);
				$converstring = str_replace('[[spk_tanggal]]',$data['spk_tanggal'],$converstring);
				$converstring = str_replace('[[bakn_tanggal]]',$data['bakn_tanggal'],$converstring);
				$converstring = str_replace('[[syarat_penagihan]]',$data['syarat_penagihan'],$converstring);
				$converstring = str_replace('[[dok_akhir]]',$data['dok_akhir'],$converstring);
			}elseif($tipe_id == 8){
				
				
				
				$converstring = str_replace('[[nama_mitra]]',$data['nama_mitra'],$text);
				$converstring = str_replace('[[alamat_mitra]]',$data['alamat_mitra'],$converstring);
				$converstring = str_replace('[[nama_pekerjaan]]',$data['nama_pekerjaan'],$converstring);
				
				$converstring = str_replace('[[nama_pic_drafter]]',$data['nama_pic_drafter'],$converstring);
				$converstring = str_replace('[[email_pic_drafter]]',$data['email_pic_drafter'],$converstring);
				$converstring = str_replace('[[hari_submit]]',$data['hari_submit'],$converstring);
				$converstring = str_replace('[[tanggal_submit]]',$data['tanggal_submit'],$converstring);
				$converstring = str_replace('[[jam_submit]]',$data['jam_submit'],$converstring);
				$converstring = str_replace('[[bulan_submit]]',$data['bulan_submit'],$converstring);
				$converstring = str_replace('[[tahun_submit]]',$data['tahun_submit'],$converstring);
				
				$converstring = str_replace('[[nama_vp]]',$data['nama_vp'],$converstring);
				$converstring = str_replace('[[jabatan_vp]]',$data['jabatan_vp'],$converstring);
				$converstring = str_replace('[[dok_akhir]]',$data['dok_akhir'],$converstring);
				$converstring = str_replace('[[dok_akhir_nomor]]',$data['dok_akhir_nomor'],$converstring);
				$converstring = str_replace('[[dok_akhir_tanggal]]',$data['dok_akhir_tanggal'],$converstring);
				$converstring = str_replace('[[nilai_pekerjaan]]',$data['nilai_pekerjaan'],$converstring);
				$converstring = str_replace('[[nilai_progres]]',$data['nilai_progres'],$converstring);
				$converstring = str_replace('[[baut_tanggal]]',$data['baut_tanggal'],$converstring);
				
				
			}elseif($tipe_id == 9 || $tipe_id == 10){
				
				
				
				$converstring = str_replace('[[nama_mitra]]',$data['nama_mitra'],$text);
				$converstring = str_replace('[[alamat_mitra]]',$data['alamat_mitra'],$converstring);
				$converstring = str_replace('[[nama_pekerjaan]]',$data['nama_pekerjaan'],$converstring);
				
				$converstring = str_replace('[[nama_pic_drafter]]',$data['nama_pic_drafter'],$converstring);
				$converstring = str_replace('[[email_pic_drafter]]',$data['email_pic_drafter'],$converstring);
				$converstring = str_replace('[[hari_submit]]',$data['hari_submit'],$converstring);
				$converstring = str_replace('[[tanggal_submit]]',$data['tanggal_submit'],$converstring);
				$converstring = str_replace('[[jam_submit]]',$data['jam_submit'],$converstring);
				$converstring = str_replace('[[bulan_submit]]',$data['bulan_submit'],$converstring);
				$converstring = str_replace('[[tahun_submit]]',$data['tahun_submit'],$converstring);
				
				$converstring = str_replace('[[nama_vp]]',$data['nama_vp'],$converstring);
				$converstring = str_replace('[[jabatan_vp]]',$data['jabatan_vp'],$converstring);
				$converstring = str_replace('[[dok_akhir]]',$data['dok_akhir'],$converstring);
				$converstring = str_replace('[[dok_akhir_nomor]]',$data['dok_akhir_nomor'],$converstring);
				$converstring = str_replace('[[dok_akhir_tanggal]]',$data['dok_akhir_tanggal'],$converstring);
				$converstring = str_replace('[[nilai_pekerjaan]]',$data['nilai_pekerjaan'],$converstring);
				$converstring = str_replace('[[nilai_tagihan]]',$data['nilai_tagihan'],$converstring);
				$converstring = str_replace('[[kota]]',$data['kota'],$converstring);
				$converstring = str_replace('[[mekanisme_pembayaran]]',$data['mekanisme_pembayaran'],$converstring);
				$converstring = str_replace('[[tanggal_kwintansi]]',$data['tanggal_kwintansi'],$converstring);
				$converstring = str_replace('[[nomorrekening]]',$data['nomorrekening'],$converstring);
				$converstring = str_replace('[[namarekening]]',$data['namarekening'],$converstring);
				$converstring = str_replace('[[bankrekening]]',$data['bankrekening'],$converstring);
				
			}else{
				$converstring = $text;
			}
			
			
			return $converstring;
		}
		
		public function saveFormat(){
			$data_id = $this->CI->input->post('id');
			$dwt_id = $this->CI->input->post('dwt_id');	
			$id_nota_justi = $this->CI->input->post('id_nota_justi');	
			$tipe_id = $this->CI->input->post('tipe_id');
			
			$format = $this->CI->input->post('format');
			
			
			$this->CI->db->where('dwt_id',$dwt_id);
			$this->CI->db->where('data_id',$data_id);
			$this->CI->db->where('id_nota_justi',$id_nota_justi);
			$this->CI->db->where('tipe_id',$tipe_id);
			//$this->CI->db->where('active',1);
			$querygenerate = $this->CI->db->get('data_generate');
			$querygenerate = $querygenerate->result_object();
			if(!$querygenerate){
				$data = array(	
					'data_id'		=> $data_id,
					'dwt_id'	=> $dwt_id,
					'id_nota_justi'	=> $id_nota_justi,
					'tipe_id'		=> $tipe_id,
					'description'	=> $format,
					'active'		=> 1,
					'createdid'		=> $this->CI->session->userdata('userid'),
					'created'		=> date('Y-m-d H:i:s'),
					'modifiedid'	=> $this->CI->session->userdata('userid'),
					'modified'		=> date('Y-m-d H:i:s'),
				);
															
				$insert = $this->CI->db->insert('data_generate',$data);
				$insert_id = $this->CI->db->insert_id();
				if($insert){
					$result = array("csrf_hash" => $this->CI->security->get_csrf_hash(),"message" => "success",'id' => $insert_id);
					return $result;
				}else{
					$result = array("csrf_hash" => $this->CI->security->get_csrf_hash(),"message" => "error");
					return $result;
				}
			}else{
				
				$data = array(	
					'description'	=> $format,
					'active'		=> 1,
					'modifiedid'	=> $this->CI->session->userdata('userid'),
					'modified'		=> date('Y-m-d H:i:s'),
				);
				
				$this->CI->db->where('id',$querygenerate[0]->id);				
				$insert = $this->CI->db->update('data_generate',$data);
				$insert_id = $querygenerate[0]->id;
				if($insert){
					$result = array("csrf_hash" => $this->CI->security->get_csrf_hash(),"message" => "success",'id' => $insert_id);
					return $result;
				}else{
					$result = array("csrf_hash" => $this->CI->security->get_csrf_hash(),"message" => "error");
					return $result;
				}
				
			}
					
			
		}
		
		public function format_download($id){
			//$this->CI->load->helper('dompdf', 'file');
			$this->CI->load->library('pdfgenerator');
			
			$this->CI->db->where('id',$id);
			$querygenerate = $this->CI->db->get('data_generate');
			$querygenerate = $querygenerate->result_object();
			if($querygenerate){
				
				if($querygenerate[0]->tipe_id == 0){
					
						$this->CI->db->where('id',$id);
						$this->CI->db->where('active',1);
						$query = $this->CI->db->get('data_generate');
						$query = $query->result_object();
						if($query){
							$dwt_name = $this->select2_getname($query[0]->dwt_id,'data_dwt_register','id','slug');
							$data['title'] = 'GENERATE | STAR PINS';
							//$data['format'] = $query[0]->description;
							
							$data['description'] =$query[0]->description;
							$doc = new DOMDocument();
							$doc->loadHTML($data['description']);
							$tags = $doc->getElementsByTagName('img');
							foreach ($tags as $tag) {
								$old_src = $tag->getAttribute('src');
								$path=base_url().$old_src;
								$new_src_url = $this->convertimgtobase64($path);
								$tag->setAttribute('src', $new_src_url);
								$tag->setAttribute('data-src', $old_src);
							}
							$data['format'] = $doc->saveHTML();
							$data['name_file'] = 'GENERATE';
					
							return $data;
							
						}else{
							return null;
						}
						
				}else{
					
					$this->CI->db->where('id',$querygenerate[0]->id_nota_justi);
					if($querygenerate[0]->tipe_id == 1){
						$queryjustnot = $this->CI->db->get('data_nota_kebutuhan');
					}elseif($querygenerate[0]->tipe_id == 2){
						$queryjustnot = $this->CI->db->get('data_justifikasi_kebutuhan');
					}
					$queryjustnot = $queryjustnot->result_object();
					if($queryjustnot){
						
						$this->CI->db->where('id',$id);
						$this->CI->db->where('active',1);
						$query = $this->CI->db->get('data_generate');
						$query = $query->result_object();
						if($query){
							$dwt_name = $this->select2_getname($query[0]->dwt_id,'data_dwt_register','id','slug');
							$data['title'] = 'SPPH Pekerjaan '.$queryjustnot[0]->name.' untuk '.$dwt_name.' | STAR PINS';
							//$data['format'] = $query[0]->description;
							
							$data['description'] =$query[0]->description;
							$doc = new DOMDocument();
							$doc->loadHTML($data['description']);
							$tags = $doc->getElementsByTagName('img');
							foreach ($tags as $tag) {
								$old_src = $tag->getAttribute('src');
								$path=base_url().$old_src;
								$new_src_url = $this->convertimgtobase64($path);
								$tag->setAttribute('src', $new_src_url);
								$tag->setAttribute('data-src', $old_src);
							}
							$data['format'] = $doc->saveHTML();
							$data['name_file'] = 'Draft_SPPH_'.$dwt_name.'_'.$queryjustnot[0]->slug.'';
					
							return $data;
							
						}else{
							return null;
						}
						
					}
				
				}
				
			}
			
			
			return null;
			
		}
		
		function custom_number_format($n, $precision = 2) {
			if ($n < 1000) {
				// Anything less than a million
				$n_format = number_format($n);
			} else if ($n < 1000000) {
				// Anything less than a billion
				$n_format = number_format($n/ 1000, $precision) . ' rb';
			} else if ($n < 1000000000) {
				// Anything less than a billion
				$n_format = number_format($n/ 1000000, $precision) . ' jt';
			} else {
				// At least a billion
				$n_format = number_format($n/ 1000000000, $precision) . ' M';
			}

			return $n_format;
		}
		
			
		function getMasterRole($tipe, $iddata){
			$gid = $this->CI->session->userdata('group_id');
			
			if($gid == 1 || $gid == 2){
				return true;
			}
			
			if($tipe == 'simpan_laporan'){
				if($gid == 1 || $gid == 2 || $gid == 3){
					return true;
				}
			}elseif($tipe == 'validasi_laporan'){
				if($gid == 1 || $gid == 2 || $gid == 4){
					return true;
				}
			}elseif($tipe == 'publikasi_laporan'){
				if($gid == 1 || $gid == 2){
					return true;
				}
			}
				
				
			return false;
		}
		
		function getRangeDate($date1, $date2, $totaldaynya = 7){
			
			$totalday = 0;
			$date_range = array();
			$this->CI->db->where('db_date >=', $date1);
			$this->CI->db->where('db_date <=', $date2);
			$this->CI->db->where('weekend_flag', 'f');
			$this->CI->db->where('holiday_flag', 'f');
			$this->CI->db->order_by('db_date','ASC');
			$this->CI->db->group_by('db_date');
			$querybulan = $this->CI->db->get('master_calendar');
			$querybulan = $querybulan->result_object();
			if($querybulan){
				foreach($querybulan as $rowsbulan){
					$totalday = $totalday + 1; 
				}
			}
			
			$totaldayinclude = $totaldaynya - $totalday;
			$totaldayinclude = $totaldaynya + $totaldayinclude;
			if($totaldayinclude == 11){
				$totaldayinclude = $totaldayinclude;
			}else{
				$totaldayinclude = $totaldayinclude;
			}
			
			
			$datenew1 = $date2; //date from database 
			$str2 = date('Y-m-d', strtotime('-'.$totaldayinclude.' days', strtotime($datenew1))); 
			$date1 = $str2;
			
			$this->CI->db->where('db_date >=', $date1);
			$this->CI->db->where('db_date <=', $date2);
			$this->CI->db->where('weekend_flag', 'f');
			$this->CI->db->where('holiday_flag', 'f');
			$this->CI->db->order_by('db_date','ASC');
			$this->CI->db->group_by('db_date');
			$querybulan = $this->CI->db->get('master_calendar');
			$querybulan = $querybulan->result_object();
			if($querybulan){
				foreach($querybulan as $rowsbulan){
					array_push($date_range, $rowsbulan->db_date);
				}
			}
			
			return array($date1, $date2, $totaldayinclude, $totaldaynya, $date_range);	
			
		}
		
		function checkdaterange($date1, $date2){
			
			$datenew = $this->getRangeDate($date1, $date2);
			$date1 = $datenew[0];
			$date2 = $datenew[1];
			$totaldaynya = $datenew[3];
			$date_range = $datenew[4];
					
			$totalday = 0;
			$this->CI->db->where('db_date >=', $date1);
			$this->CI->db->where('db_date <=', $date2);
			$this->CI->db->where('weekend_flag', 'f');
			$this->CI->db->where('holiday_flag', 'f');
			$this->CI->db->order_by('db_date','ASC');
			$this->CI->db->group_by('db_date');
			$querybulan = $this->CI->db->get('master_calendar');
			$querybulan = $querybulan->result_object();
			if($querybulan){
				foreach($querybulan as $rowsbulan){
					$totalday = $totalday + 1; 
				}
			}
			
			if($totalday < 7) {
				//$this->checkdaterange($date1, $date2);
			}
			
			return array($date1, $date2, $date_range);	
		}
		
		
		function geocode($lat, $lon){

		   $details_url="https://maps.google.com/maps/api/geocode/json?latlng=".$lat.",".$lon."&key=AIzaSyB1anZ_H2UxTJ8Xl8eeofQXrocg7UJDoAw";

		   $ch = curl_init();
		   curl_setopt($ch, CURLOPT_URL, $details_url);
		   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		   $geoloc = json_decode(curl_exec($ch), true);

		   return $geoloc;

		}
		
		function geolinkmap($lat, $lon){

		   $details_url="https://www.google.com/maps/place/".$lat.','.$lon;

		   return $details_url;

		}
		
		function updateScoring($tipe_id, $status_id, $description, $data_id, $user_id){
		$CI =& get_instance();
		
		$id_scoring = 0;
		$nilai = 0;
		$name = '';
		if($tipe_id == 1){
			$id_scoring = 1;
		}elseif($tipe_id == 2){
			$id_scoring = 4;
			$name = $this->select2_getname($data_id,'data_activity','id','name');
		}elseif($tipe_id == 4){
			$id_scoring = 9;
			$name = $this->select2_getname($data_id,'master_customer','id','name');
		}elseif($tipe_id == 5){
			
			$name = $this->select2_getname($data_id,'data_lop','id','lop_no');
			$is_sa = $this->select2_getname($data_id,'data_lop','id','is_sa');
			$is_rpa = $this->select2_getname($data_id,'data_lop','id','is_rpa');
			$is_radirtas = $this->select2_getname($data_id,'data_lop','id','is_radirtas');
			
			if($status_id == 2){
				$id_scoring = 10;
			}elseif($status_id == 3){
				$id_scoring = 12;
			}elseif($status_id == 4){
				$id_scoring = 14;
			}elseif($is_sa == 1 && $is_rpa == 0 && $is_radirtas == 0 && $status_id == 14){
				$id_scoring = 15;
			}elseif($is_sa == 1 && $is_rpa == 0 && $is_radirtas == 1 && $status_id == 11){
				$id_scoring = 15;
			}elseif($is_sa == 1 && $is_rpa == 1 && $is_radirtas == 1 && $status_id == 10){
				$id_scoring = 15;
			}elseif($is_sa == 1 && $is_rpa == 1 && $is_radirtas == 0 && $status_id == 14){
				$id_scoring = 16;
			}elseif($is_sa == 1 && $is_rpa == 1 && $is_radirtas == 1 && $status_id == 11){
				$id_scoring = 16;
			}elseif($status_id == 16){
				$id_scoring = 18;
			}elseif($status_id == 17){
				$id_scoring = 20;
			}
		}
		
	
		if($id_scoring != 0){
			$CI->db->select('data_score.*');
			if($tipe_id == 4){
				
			}else{
				$CI->db->where('user_id', $user_id);
			}
			$CI->db->where('data_id', $data_id);
			$CI->db->where('status_id', $status_id);
			$CI->db->where('type_id', $tipe_id);
			//$CI->db->where('description', $description);
			$CI->db->order_by('modified','DESC');
			$query = $CI->db->get('data_score');
			$query = $query->result_object();
			if(!$query){
				
				$CI->db->select('master_score.nilai, master_score.name');
				$CI->db->where('id', $id_scoring);
				$queryms = $CI->db->get('master_score');
				$queryms = $queryms->result_object();
				if($queryms){
					$nilai = $queryms[0]->nilai;
					if($nilai == null || $nilai == ''){
						$nilai = 0;
					}
					if($name == ''){
						$description = $queryms[0]->name;
					}else{
						$description = $queryms[0]->name.' '.$name;
					}
					
				}

				$datadetail = array(
					'user_id'			=> $user_id,
					'type_id'			=> $tipe_id,
					'data_id'			=> $data_id,
					'description'		=> $description,
					'status_id'			=> $status_id,
					'nilai'				=> $nilai,
					'createdid'			=> $user_id,
					'created'			=> date('Y-m-d H:i:s'),
					'modifiedid'		=> $user_id,
					'modified'			=> date('Y-m-d H:i:s'),
					'active'			=> 1,
				);
				
				$slug = $this->sanitize($description,'data_score');
				$datadetail = array_merge($datadetail,
					array('slug' 	=> $slug)
				);
				
				$insert = $CI->db->insert('data_score', $datadetail);
				$insertid = $this->CI->db->insert_id();
				
				return $insertid;
			}
		}
		
		return null;
			
	}
	
	function recursive_copy($module_name, $module_target_name, $source, $dest)
	{
		$file_exist = 0;
		$new_controller = basename($module_name) . '.php';
		$tmp_folder = FCPATH . 'uploads/modules/';

		// Normalisasi path dan validasi agar tidak ada traversal path
		$source = realpath($source);
		$dest = realpath(dirname($dest)) . DIRECTORY_SEPARATOR . basename($dest);

		if ($source === false || strpos($source, realpath(FCPATH)) !== 0) {
			log_message('error', 'Invalid source path.');
			return 1;
		}

		if (strpos($dest, realpath(FCPATH)) !== 0) {
			log_message('error', 'Invalid destination path.');
			return 1;
		}

		if (is_dir($source)) {
			if (!is_dir($dest)) {
				mkdir($dest, 0777, true);
			}

			$dir_items = array_diff(scandir($source), ['..', '.']);
			foreach ($dir_items as $v) {
				if (preg_match('/[<>:"\/\\|?*\x00]/', $v)) {
					continue; // Skip dangerous filename
				}

				$vnew = ($v === $new_controller) ? basename($module_target_name) . '.php' : $v;
				$source_path = $source . DIRECTORY_SEPARATOR . $v;
				$dest_path = $dest . DIRECTORY_SEPARATOR . $vnew;

				if (!file_exists($dest_path)) {
					$this->recursive_copy($module_name, $module_target_name, $source_path, $dest_path);

					if ($v === $new_controller) {
						if (!is_dir($tmp_folder)) {
							mkdir($tmp_folder, 0775, true);
						}

						$tmp_file_path = $tmp_folder . basename($vnew);
						$this->recursive_copy($module_name, $module_target_name, $source_path, $tmp_file_path);

						if (file_exists($tmp_file_path)) {
							$contents = file_get_contents($tmp_file_path);
							$count = 0;

							$contents = str_replace($module_name, $module_target_name, $contents, $count);
							$contents = str_replace(strtolower($module_name), strtolower($module_target_name), $contents, $count);

							if ($count > 0) {
								file_put_contents($tmp_file_path, $contents);
								$this->recursive_copy($module_name, $module_target_name, $tmp_file_path, $dest_path);
							}

							unlink($tmp_file_path);
						}
					}
				} else {
					$file_exist = 1;
				}
			}
		} elseif (is_file($source)) {
			if (!copy($source, $dest)) {
				log_message('error', "Failed to copy $source to $dest");
				$file_exist = 1;
			}
		}

		$controller_path = './application/modules/' . strtolower(basename($module_name)) . '/controllers/' . basename($new_controller);
		if (file_exists($controller_path)) {
			$file_exist = 1;
		}

		if (is_dir($tmp_folder)) {
			//$this->delete_directory($tmp_folder);
		}

		return $file_exist;
	}

	// Delete directory recursively
	private function delete_directory($dir)
	{
		if (!is_dir($dir)) return;
		foreach (scandir($dir) as $item) {
			if ($item === '.' || $item === '..') continue;
			$path = $dir . DIRECTORY_SEPARATOR . $item;
			is_dir($path) ? $this->delete_directory($path) : unlink($path);
		}
		rmdir($dir);
	}

		
		function generateLopNo()
	{
			// FORMAT SMA/TAHUN SEKARANG/0001
			// EX : SMA/2020/0001
			$tahun = date("Y");
			$this->CI->db->select('SUBSTR(lop_no, 4, 4) as tahun, RIGHT(lop_no,4) as lop_no', false);
			$this->CI->db->where('lop_no !=','DRAFT');
			$this->CI->db->like('lop_no','LOP');
			$this->CI->db->order_by("lop_no", "DESC");
			$this->CI->db->limit(1);
			$query = $this->CI->db->get('data_lop');
			if($query->num_rows() <> 0)
			{
				$data	= $query->row(); // ambil satu baris data
				$tahun	= $data->tahun;
				if($tahun == date('Y')){
					$kodeLOP  = intval($data->lop_no) + 1; // tambah 1
				}else{
					$kodeLOP  = 1;
				}
			}else{
				$kodeLOP  = 1; // isi dengan 1
			}

			$lastKode = str_pad($kodeLOP, 4, "0", STR_PAD_LEFT);
			$LOP      = "LOP";
			$newKode  = $LOP."".$tahun."".$lastKode;

			return $newKode;  // return kode baru

	}
	
	
	function formatemail($subject, $message){
		$html = '<!--
			* This email was built using Tabular.
			* For more information, visit https://tabular.email
			-->
			<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
			<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office" lang="en">
			  <head>
				<title></title>
				<meta charset="UTF-8" />
				<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
				<!--[if !mso]>-->
				<meta http-equiv="X-UA-Compatible" content="IE=edge" />
				<!--
					<![endif]-->
				<meta name="x-apple-disable-message-reformatting" content="" />
				<meta content="target-densitydpi=device-dpi" name="viewport" />
				<meta content="true" name="HandheldFriendly" />
				<meta content="width=device-width" name="viewport" />
				<meta name="format-detection" content="telephone=no, date=no, address=no, email=no, url=no" />
				<style type="text/css">
				  table {
					border-collapse: separate;
					table-layout: fixed;
					mso-table-lspace: 0pt;
					mso-table-rspace: 0pt
				  }

				  table td {
					border-collapse: collapse
				  }

				  .ExternalClass {
					width: 100%
				  }

				  .ExternalClass,
				  .ExternalClass p,
				  .ExternalClass span,
				  .ExternalClass font,
				  .ExternalClass td,
				  .ExternalClass div {
					line-height: 100%
				  }
				  
				  .btn-email {
					  display:block;
					  margin:0;
					  Margin:0;
					  font-family:"Albert Sans,BlinkMacSystemFont,Segoe UI,Helvetica Neue,Arial,sans-serif";
					  line-height:44px;
					  font-weight:800;
					  font-style:normal;
					  font-size:12px;
					  text-decoration:none;
					  text-transform:uppercase;
					  letter-spacing:2.4px;
					  direction:ltr;
					  color:#FFFFFF !important;
					  text-align:center;
					  mso-line-height-rule:
					  exactly;
					  mso-text-raise:10px;
					   background: #5a92cd;
					  padding: 10px;
					  border-radius: 10px;
				  }

				  body,
				  a,
				  li,
				  p,
				  h1,
				  h2,
				  h3 {
					-ms-text-size-adjust: 100%;
					-webkit-text-size-adjust: 100%;
				  }

				  html {
					-webkit-text-size-adjust: none !important
				  }

				  body,
				  #innerTable {
					-webkit-font-smoothing: antialiased;
					-moz-osx-font-smoothing: grayscale
				  }

				  #innerTable img+div {
					display: none;
					display: none !important
				  }

				  img {
					Margin: 0;
					padding: 0;
					-ms-interpolation-mode: bicubic
				  }

				  h1,
				  h2,
				  h3,
				  p,
				  a {
					line-height: inherit;
					overflow-wrap: normal;
					white-space: normal;
					word-break: break-word
				  }

				  a {
					text-decoration: none
				  }

				  h1,
				  h2,
				  h3,
				  p {
					min-width: 100% !important;
					width: 100% !important;
					max-width: 100% !important;
					display: inline-block !important;
					border: 0;
					padding: 0;
					margin: 0
				  }

				  a[x-apple-data-detectors] {
					color: inherit !important;
					text-decoration: none !important;
					font-size: inherit !important;
					font-family: inherit !important;
					font-weight: inherit !important;
					line-height: inherit !important
				  }

				  u+#body a {
					color: inherit;
					text-decoration: none;
					font-size: inherit;
					font-family: inherit;
					font-weight: inherit;
					line-height: inherit;
				  }

				  a[href^="mailto"],
				  a[href^="tel"],
				  a[href^="sms"] {
					color: inherit;
					text-decoration: none
				  }
				</style>
				<style type="text/css">
				  @media (min-width: 481px) {
					.hd {
					  display: none !important
					}
				  }
				</style>
				<style type="text/css">
				  @media (max-width: 480px) {
					.hm {
					  display: none !important
					}
				  }
				</style>
				<style type="text/css">
				  @media (max-width: 480px) {
					.t50 {
					  padding: 0 0 22px !important
					}

					.t12,
					.t41,
					.t51,
					.t65 {
					  width: 480px !important
					}

					.t36,
					.t46,
					.t60,
					.t7 {
					  text-align: center !important
					}

					.t35,
					.t45,
					.t59,
					.t6 {
					  vertical-align: top !important;
					  width: 600px !important
					}

					.t4 {
					  border-top-left-radius: 0 !important;
					  border-top-right-radius: 0 !important;
					  padding: 20px 30px !important
					}

					.t33 {
					  border-bottom-right-radius: 0 !important;
					  border-bottom-left-radius: 0 !important;
					  padding: 30px !important
					}

					.t67 {
					  mso-line-height-alt: 20px !important;
					  line-height: 20px !important
					}

					.t55 {
					  width: 380px !important
					}

					.t2 {
					  width: 44px !important
					}

					.t21,
					.t31 {
					  width: 420px !important
					}
				  }
				</style>
				<!--[if !mso]>-->
				<link href="https://fonts.googleapis.com/css2?family=Albert+Sans:wght@500;800&amp;display=swap" rel="stylesheet" type="text/css" />
				<!--
					<![endif]-->
				<!--[if mso]>
					<xml>
						<o:OfficeDocumentSettings>
							<o:AllowPNG/>
							<o:PixelsPerInch>96</o:PixelsPerInch>
						</o:OfficeDocumentSettings>
					</xml>
					<![endif]-->
			  </head>
			  <body id="body" class="t70" style="min-width:100%;Margin:0px;padding:0px;background-color:#E0E0E0;">
				<div class="t69" style="background-color:#E0E0E0;">
				  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" align="center">
					<tr>
					  <td class="t68" style="font-size:0;line-height:0;mso-line-height-rule:exactly;background-color:#E0E0E0;background-image:url(undefined);" valign="top" align="center">
						<!--[if mso]>
									<v:background
										xmlns:v="urn:schemas-microsoft-com:vml" fill="true" stroke="false">
										<v:fill color="#E0E0E0"/>
									</v:background>
									<![endif]-->
						<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" align="center" id="innerTable">
						  <tr>
							<td align="center">
							  <table class="t52" role="presentation" cellpadding="0" cellspacing="0" style="Margin-left:auto;Margin-right:auto;">
								<tr>
								  <!--[if mso]>
														<td width="566" class="t51" style="background-image:url(undefined);width:566px;">
															<![endif]-->
								  <!--[if !mso]>-->
								  <td class="t51" style="background-image:url(undefined);width:566px;">
									<!--
																<![endif]-->
									<table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="width:100%">
									  <tr>
										<td class="t50" style="padding:50px 10px 31px 10px;">
										  <div class="t49" style="width:100%;text-align:center;">
											<div class="t48" style="display:inline-block;">
											  <table class="t47" role="presentation" cellpadding="0" cellspacing="0" align="center" valign="top">
												<tr class="t46">
												  <td></td>
												  <td class="t45" width="546" valign="top">
													<table role="presentation" width="100%" cellpadding="0" cellspacing="0" class="t44" style="width:100%;">
													  <tr>
														<td class="t43" style="background-color:transparent;background-image:url(undefined);">
														  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="width:100% !important;">
															<tr>
															  <td align="center">
																<table class="t13" role="presentation" cellpadding="0" cellspacing="0" style="Margin-left:auto;Margin-right:auto;">
																  <tr>
																	<!--[if mso]>
																		<td width="546" class="t12" style="background-image:url(undefined);width:546px;">
																																	<![endif]-->
																	<!--[if !mso]>-->
																	<td class="t12" style="background-image:url(undefined);width:546px;">
																	  <!--
																																		<![endif]-->
																	  <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="width:100%">
																		<tr>
																		  <td class="t11">
																			<div class="t10" style="width:100%;text-align:center;">
																			  <div class="t9" style="display:inline-block;">
																				<table class="t8" role="presentation" cellpadding="0" cellspacing="0" align="center" valign="top">
																				  <tr class="t7">
																					<td></td>
																					<td class="t6" width="546" valign="top">
																					  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" class="t5" style="width:100%;">
																						<tr>
																						  <td class="t4" style="overflow:hidden;background-color:#274879;background-image:url(undefined);padding:49px 50px 42px 50px;border-radius:18px 18px 0 0;">
																							<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="width:100% !important;">
																							  <tr>
																								<td align="left">
																								  <table class="t3" role="presentation" cellpadding="0" cellspacing="0" style="Margin-right:auto;">
																									<tr>
																									  <!--[if mso]>
																																																		<td width="85" class="t2" style="background-image:url(undefined);width:85px;">
																																																			<![endif]-->
																									  <!--[if !mso]>-->
																									  <td class="t2" style="background-image:url(undefined);width:85px;">
																										<!--
																																																				<![endif]-->
																										<table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="width:100%">
																										  <tr>
																											<td class="t1">
																											  <div style="font-size:0px;">
																												<img class="t0" style="display:block;border:0;height:auto;width:100%;Margin:0;max-width:100%;" width="120" alt="" src="https://www.kemendag.go.id/assets/imgs/theme/logo.svg" />
																											  </div>
																											</td>
																										  </tr>
																										</table>
																									  </td>
																									</tr>
																								  </table>
																								</td>
																							  </tr>
																							</table>
																						  </td>
																						</tr>
																					  </table>
																					</td>
																					<td></td>
																				  </tr>
																				</table>
																			  </div>
																			</div>
																		  </td>
																		</tr>
																	  </table>
																	</td>
																  </tr>
																</table>
															  </td>
															</tr>
															<tr>
															  <td align="center">
																<table class="t42" role="presentation" cellpadding="0" cellspacing="0" style="Margin-left:auto;Margin-right:auto;">
																  <tr>
																	<!--[if mso]>
																																		<td width="546" class="t41" style="background-image:url(undefined);width:546px;">
																																			<![endif]-->
																	<!--[if !mso]>-->
																	<td class="t41" style="background-image:url(undefined);width:546px;">
																	  <!--
																																				<![endif]-->
																	  <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="width:100%">
																		<tr>
																		  <td class="t40">
																			<div class="t39" style="width:100%;text-align:center;">
																			  <div class="t38" style="display:inline-block;">
																				<table class="t37" role="presentation" cellpadding="0" cellspacing="0" align="center" valign="top">
																				  <tr class="t36">
																					<td></td>
																					<td class="t35" width="546" valign="top">
																					  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" class="t34" style="width:100%;">
																						<tr>
																						  <td class="t33" style="overflow:hidden;background-color:#F8F8F8;background-image:url(undefined);padding:40px 50px 40px 50px;border-radius:0 0 18px 18px;">
																							<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="width:100% !important;">
																							  <tr>
																								<td align="left">
																								  <table class="t17" role="presentation" cellpadding="0" cellspacing="0" style="Margin-right:auto;">
																									<tr>
																									  <!--[if mso]>
																																																				<td width="381" class="t16" style="background-image:url(undefined);width:381px;">
																																																					<![endif]-->
																									  <!--[if !mso]>-->
																									  <td class="t16" style="background-image:url(undefined);width:381px;">
																										<!--
																																																						<![endif]-->
																										<table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="width:100%">
																										  <tr>
																											<td class="t15">
																											  <h1 class="t14" style="margin:0;Margin:0;font-family:Albert Sans,BlinkMacSystemFont,Segoe UI,Helvetica Neue,Arial,sans-serif;line-height:41px;font-weight:800;font-style:normal;font-size:30px;text-decoration:none;text-transform:none;letter-spacing:-1.56px;direction:ltr;color:#191919;text-align:left;mso-line-height-rule:exactly;mso-text-raise:3px;">'.$subject.'</h1>
																											</td>
																										  </tr>
																										</table>
																									  </td>
																									</tr>
																								  </table>
																								</td>
																							  </tr>
																							  <tr>
																								<td>
																								  <div class="t18" style="mso-line-height-rule:exactly;mso-line-height-alt:25px;line-height:25px;font-size:1px;display:block;">&nbsp;&nbsp;</div>
																								</td>
																							  </tr>
																							  <tr>
																								<td align="left">
																								  <table class="t22" role="presentation" cellpadding="0" cellspacing="0" style="Margin-right:auto;">
																									<tr>
																									  <!--[if mso]>
																																																					<td width="446" class="t21" style="background-image:url(undefined);width:446px;">
																																																						<![endif]-->
																									  <!--[if !mso]>-->
																									  <td class="t21" style="background-image:url(undefined);width:446px;">
																										<!--
																																																							<![endif]-->
																										<table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="width:100%">
																										  <tr>
																											<td class="t20">
																											  <p class="t19" style="margin:0;Margin:0;font-family:Albert Sans,BlinkMacSystemFont,Segoe UI,Helvetica Neue,Arial,sans-serif;line-height:22px;font-weight:500;font-style:normal;font-size:14px;text-decoration:none;text-transform:none;letter-spacing:-0.56px;direction:ltr;color:#333333;text-align:left;mso-line-height-rule:exactly;mso-text-raise:2px;">'.$message.'</p>
																											</td>
																										  </tr>
																										</table>
																									  </td>
																									</tr>
																								  </table>
																								</td>
																							  </tr>
																							  <tr>
																								<td>
																								  <div class="t23" style="mso-line-height-rule:exactly;mso-line-height-alt:15px;line-height:15px;font-size:1px;display:block;">&nbsp;&nbsp;</div>
																								</td>
																							  </tr>
																							 
																							  <tr>
																								<td>
																								  <div class="t28" style="mso-line-height-rule:exactly;mso-line-height-alt:15px;line-height:15px;font-size:1px;display:block;">&nbsp;&nbsp;</div>
																								</td>
																							  </tr>
																							  <tr>
																								<td align="left">
																								  <table class="t32" role="presentation" cellpadding="0" cellspacing="0" style="Margin-right:auto;">
																									<tr>
																									  <!--[if mso]>
																																																							<td width="446" class="t31" style="background-image:url(undefined);width:446px;">
																																																								<![endif]-->
																									  <!--[if !mso]>-->
																									  <td class="t31" style="background-image:url(undefined);width:446px;">
																										<!--
																																																									<![endif]-->
																										<table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="width:100%">
																										  <tr>
																											<td class="t30">
																											  <p class="t29" style="margin:0;Margin:0;font-family:Albert Sans,BlinkMacSystemFont,Segoe UI,Helvetica Neue,Arial,sans-serif;line-height:22px;font-weight:500;font-style:normal;font-size:14px;text-decoration:none;text-transform:none;letter-spacing:-0.56px;direction:ltr;color:#333333;text-align:left;mso-line-height-rule:exactly;mso-text-raise:2px;">Jika ada pertanyaan dan butuh bantuan lain silahkan kontak administrator.</p>
																											</td>
																										  </tr>
																										</table>
																									  </td>
																									</tr>
																								  </table>
																								</td>
																							  </tr>
																							</table>
																						  </td>
																						</tr>
																					  </table>
																					</td>
																					<td></td>
																				  </tr>
																				</table>
																			  </div>
																			</div>
																		  </td>
																		</tr>
																	  </table>
																	</td>
																  </tr>
																</table>
															  </td>
															</tr>
														  </table>
														</td>
													  </tr>
													</table>
												  </td>
												  <td></td>
												</tr>
											  </table>
											</div>
										  </div>
										</td>
									  </tr>
									</table>
								  </td>
								</tr>
							  </table>
							</td>
						  </tr>
						  <tr>
							<td align="center">
							  <table class="t66" role="presentation" cellpadding="0" cellspacing="0" style="Margin-left:auto;Margin-right:auto;">
								<tr>
								  <!--[if mso]>
																						<td width="600" class="t65" style="background-image:url(undefined);width:600px;">
																							<![endif]-->
								  <!--[if !mso]>-->
								  <td class="t65" style="background-image:url(undefined);width:600px;">
									<!--
																								<![endif]-->
									<table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="width:100%">
									  <tr>
										<td class="t64">
										  <div class="t63" style="width:100%;text-align:center;">
											<div class="t62" style="display:inline-block;">
											  <table class="t61" role="presentation" cellpadding="0" cellspacing="0" align="center" valign="top">
												<tr class="t60">
												  <td></td>
												  <td class="t59" width="600" valign="top">
													<table role="presentation" width="100%" cellpadding="0" cellspacing="0" class="t58" style="width:100%;">
													  <tr>
														<td class="t57" style="background-image:url(undefined);padding:0 50px 0 50px;">
														  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="width:100% !important;">
															<tr>
															  <td align="center">
																<table class="t56" role="presentation" cellpadding="0" cellspacing="0" style="Margin-left:auto;Margin-right:auto;">
																  <tr>
																	<!--[if mso]>
																																								<td width="420" class="t55" style="background-image:url(undefined);width:420px;">
																																									<![endif]-->
																	<!--[if !mso]>-->
																	<td class="t55" style="background-image:url(undefined);width:420px;">
																	  <!--
																																										<![endif]-->
																	  <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="width:100%">
																		<tr>
																		  <td class="t54">
																			<p class="t53" style="margin:0;Margin:0;font-family:Albert Sans,BlinkMacSystemFont,Segoe UI,Helvetica Neue,Arial,sans-serif;line-height:22px;font-weight:500;font-style:normal;font-size:12px;text-decoration:none;text-transform:none;direction:ltr;color:#888888;text-align:center;mso-line-height-rule:exactly;mso-text-raise:3px;">© '.date('Y').'PMSE By SIMPKTN. All Rights Reserved <br />
																			</p>
																		  </td>
																		</tr>
																	  </table>
																	</td>
																  </tr>
																</table>
															  </td>
															</tr>
														  </table>
														</td>
													  </tr>
													</table>
												  </td>
												  <td></td>
												</tr>
											  </table>
											</div>
										  </div>
										</td>
									  </tr>
									</table>
								  </td>
								</tr>
							  </table>
							</td>
						  </tr>
						  <tr>
							<td>
							  <div class="t67" style="mso-line-height-rule:exactly;mso-line-height-alt:50px;line-height:50px;font-size:1px;display:block;">&nbsp;&nbsp;</div>
							</td>
						  </tr>
						</table>
					  </td>
					</tr>
				  </table>
				</div>
				<div class="gmail-fix" style="display: none; white-space: nowrap; font: 15px courier; line-height: 0;">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</div>
			  </body>
			</html>';
			
			return $html;
	}
	
	
	function getTokenEpayment($username, $password)
{
    $url = $this->link_api . 'auth/getLogin';

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
            'Content-Length: ' . strlen($payload),
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Safari/537.36',
            'Referer: https://epayment-simpktn.kemendag.go.id/',
            'Origin: https://epayment-simpktn.kemendag.go.id'
        ]
    ]);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        $error_msg = curl_error($ch);
    }

    curl_close($ch);

    if ($response) {
        $data = json_decode($response);
        if (isset($data->status) && $data->status == 'success') {
            return array($http_code, $data->token);
        }
    }

    // Jika error cURL terjadi, bisa return juga untuk debug
    if (isset($error_msg)) {
        return array($http_code, null, 'cURL Error: ' . $error_msg);
    }

    // Return response mentah untuk keperluan debugging jika perlu
    return array($http_code, null, $response);
}

	
	function getBilling($id, $user_web_id){
		$headerdata = array();
		$detaildata = array();
		$errornya_simpony = '';
		$message = array("status" => 'error',"errors" => 'Data Tidak Ditemukan');
		
		$this->CI->db->select('data_pengajuan_invoice.*');
		$this->CI->db->where('data_pengajuan_invoice.id',$id);
		$this->CI->db->where('data_pengajuan_invoice.active',1);
		$queryinvoice = $this->CI->db->get('data_pengajuan_invoice');
		$queryinvoice = $queryinvoice->result_object();
		if($queryinvoice){
			
			$pengajuan_id = $queryinvoice[0]->pengajuan_id;
			$permohonan_perusahaan_id = $this->select2_getname($pengajuan_id,'data_pengajuan','id','permohonan_perusahaan_id');
			$nama_wajib_bayar = $queryinvoice[0]->invoice_nama;
			$invoice_alamat = $queryinvoice[0]->invoice_nama;
			$invoice_npwp = $queryinvoice[0]->invoice_npwp ?? '999999999999999';
			$invoice_npwp = preg_replace('/\D/', '', $invoice_npwp);
			
			
			$kode_billing = $queryinvoice[0]->kode_billing;
			
			if($kode_billing != null && $kode_billing != ''){
				
				$data_simpony = array(
					'invoice_no' => $queryinvoice[0]->invoice_no,
					'tanggal' => $queryinvoice[0]->tanggal,
					'trx_id_simponi' => $queryinvoice[0]->trx_id_simponi,
					'kode_billing' => $queryinvoice[0]->kode_billing,
					'tgl_jam_billing' => $queryinvoice[0]->tgl_jam_billing,
					'tgl_jam_expired_billing' => $queryinvoice[0]->tgl_jam_expired_billing
				);
				
				$message = array(
					"status" => 'success',
					"message" => 'Sukses',
					"data" => $data_simpony,
					"json"=>null,
					"errornya_simpony" => $errornya_simpony
				);
				
			}else{
				
				$iddata = $queryinvoice[0]->id;
				$invoice_no = $queryinvoice[0]->invoice_no;
				$invoice_tanggal = $queryinvoice[0]->tanggal;
				$trx_id_kl = $queryinvoice[0]->invoice_no;
				$user_id = '59303';
				$password = 'P@ssw0rdSIMPKTN';
				$expired_date = $queryinvoice[0]->expired;
				$kode_kl = '090';
				$kode_eselon_1 = '09';
				$kode_satker = '647931';
				$jenis_pnbp = 'F';
				$kode_mata_uang = '1';
				$kode_lokasi_satker = '01';
				$kode_kabkot_satker = '55';
				$total_nominal_billing = (float)$queryinvoice[0]->total;
				//$nama_wajib_bayar = $nama_wajib_bayar;
				$kode_satker_pemungut = '647931';
				$npwp = $invoice_npwp ?? '999999999999999';
				$nik = '9999999999999999';
				
				$this->CI->db->select('data_pengajuan_invoice_detail.*');
				$this->CI->db->where('data_pengajuan_invoice_detail.invoice_id',$iddata);
				$this->CI->db->where('data_pengajuan_invoice_detail.active',1);
				$queryinvoicedetail = $this->CI->db->get('data_pengajuan_invoice_detail');
				$queryinvoicedetail = $queryinvoicedetail->result_object();
				if($queryinvoicedetail){
					foreach($queryinvoicedetail as $rowsdata){
						
						//$nama_wajib_bayar = $nama_wajib_bayar;
						$kode_tarif_simponi = $rowsdata->kode_tarif_simponi;
						$kode_pp_simponi = $rowsdata->kode_pp_simponi;
						$kode_akun = $rowsdata->kode_akun;
						$nominal_tarif_pnbp = (float)$rowsdata->nominal;
						$volume = (float)$rowsdata->qty;
						$satuan = $rowsdata->tarif_uom;
						$total_nominal_per_record = (float)$rowsdata->nominal;
						$keterangan = $rowsdata->keterangan;
						
						$data_detail = array(
							"nama_wajib_bayar"=> $nama_wajib_bayar,
							"kode_tarif_simponi"=> $kode_tarif_simponi,
							"kode_pp_simponi"=> $kode_pp_simponi,
							"kode_akun"=> $kode_akun,
							"nominal_tarif_pnbp"=> $nominal_tarif_pnbp,
							"volume"=> $volume,
							"satuan"=> $satuan,
							"total_nominal_per_record"=> $total_nominal_per_record,
							"kode_lokasi_satker"=> $kode_lokasi_satker,
							"kode_kabkot_satker"=> $kode_kabkot_satker,
							"keterangan"=> $keterangan
						);
						
						array_push($detaildata,$data_detail); 
					}
				}
				
				$headerdata = array(
					"trx_id_kl"=> $trx_id_kl,
					"user_id"=> $user_id,
					"password"=> $password,
					"invoice_no"=> $invoice_no,
					"invoice_tanggal"=> $invoice_tanggal,
					"kode_kl"=> $kode_kl,
					"expired_date"=> $expired_date,
					"kode_eselon_1"=> $kode_eselon_1,
					"kode_satker"=> $kode_satker,
					"jenis_pnbp"=> $jenis_pnbp,
					"kode_mata_uang"=> $kode_mata_uang,
					"total_nominal_billing"=> $total_nominal_billing,
					"nama_wajib_bayar"=> $nama_wajib_bayar,
					"kode_satker_pemungut"=> $kode_satker_pemungut,
					"npwp"=> $npwp,
					"nik"=> $nik,
					'detail' => $detaildata
				);
				
				
				
				$token_data = $this->getTokenEpayment(payment_user, payment_password);
				$token = $token_data[1];
				$json_contents = $headerdata;
				
				if($token != null){
					
					$authorization = "Authorization: Bearer ".$token; // Prepare the authorisation token

					$header = array(
						$authorization,
						'Content-type: application/json'
					);
					
					$curl = curl_init();
					curl_setopt($curl, CURLOPT_URL, $this->linkgateway_dev.'transaction/getBilling');
					curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
					//curl_setopt($curl, CURLOPT_ENCODING, '');
					curl_setopt($curl, CURLOPT_MAXREDIRS, 10);
					curl_setopt($curl, CURLOPT_TIMEOUT, 300);
					curl_setopt($curl, CURLOPT_VERBOSE, true);
					curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
					curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
					curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
					curl_setopt($curl, CURLOPT_POST, 1);
					curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
					curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($json_contents));
					curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
					$response = curl_exec($curl);
					$info = curl_getinfo($curl);
					//print_r($info);
					
					curl_close($curl);
					$result = $response;
					//print_r ($result);
					//die();
					
					$array_respon = json_decode($result, true);
					//print_r($array_respon['data'][0]['billing']);
					//die();
					
					if (is_array($array_respon) && count($array_respon) > 0) {
						
						if (
							isset($array_respon['data'][0]['billing']['errors'])
						) {
							$errorMessage = $array_respon['data'][0]['billing']['errors'];
							$errornya_simpony = $errorMessage;
						} else {
							$errornya_simpony = "Tidak ada error.";
						}
			
						if($array_respon['status'] == 'success'){
							
							if($array_respon['data'][0]['billing']['status'] == 'success'){
								$data_simpony = array(
									'invoice_no' => $invoice_no,
									'tanggal' => $invoice_tanggal,
									'trx_id_simponi' => $array_respon['data'][0]['billing']['data']['trx_id_simponi'],
									'kode_billing' => $array_respon['data'][0]['billing']['data']['kode_billing'],
									'tgl_jam_billing' => $array_respon['data'][0]['billing']['data']['tgl_jam_billing'],
									'tgl_jam_expired_billing' => $array_respon['data'][0]['billing']['data']['tgl_jam_expired_billing'],
									'errors_simpony' => $errornya_simpony,
									'modifiedid'	=> $user_web_id,
									'modified'		=> date('Y-m-d H:i:s')
								);
								
								$this->CI->db->where('data_pengajuan_invoice.id', $iddata);
								$insert = $this->CI->db->update('data_pengajuan_invoice', $data_simpony);	
								if($insert){
									
									$data_simpony_respon = array(
										'invoice_no' => $invoice_no,
										'tanggal' => $invoice_tanggal,
										'trx_id_simponi' => $array_respon['data'][0]['billing']['data']['trx_id_simponi'],
										'kode_billing' => $array_respon['data'][0]['billing']['data']['kode_billing'],
										'tgl_jam_billing' => $array_respon['data'][0]['billing']['data']['tgl_jam_billing'],
										'tgl_jam_expired_billing' => $array_respon['data'][0]['billing']['data']['tgl_jam_expired_billing'],
									);
								
									$message = array(
										"status" => 'success',
										"message" => $array_respon['status'],
										"data" => $data_simpony_respon,
										"json"=>$json_contents,
										"errornya_simpony" => $errornya_simpony
									);
								}else{
									$message = array(
										"status" => 'error',
										"errors" => 'Data tidak dapat di simpan',
										'simpony_respon' => $array_respon, 
										"json"=>$json_contents,
										"errornya_simpony" => $errornya_simpony
									);
								}
							}else{
								
								$data_simpony = array(
									'errors_simpony' => $errornya_simpony,
									'modifiedid'	=> $user_web_id,
									'modified'		=> date('Y-m-d H:i:s')
								);
								
								$this->CI->db->where('data_pengajuan_invoice.id', $iddata);
								$insert = $this->CI->db->update('data_pengajuan_invoice', $data_simpony);	
							
								$data_simpony_respon = array(
									'invoice_no' => $invoice_no,
									'tanggal' => $invoice_tanggal,
									'trx_id_simponi' => null,
									'kode_billing' => null,
									'tgl_jam_billing' => null,
									'tgl_jam_expired_billing' => null,
								);
								
								$message = array(
									"status" => 'error',
									"errors" => $array_respon['data'][0]['billing']['status'],
									"message" => $array_respon['status'],
									"data" => $data_simpony_respon,
									'simpony_respon' => $array_respon, 
									"json"=>$json_contents,
									"errornya_simpony" => $errornya_simpony
								);
							}
							
							
						}else{
							
							$data_simpony = array(
								'invoice_no' 	=> $invoice_no,
								'tanggal' 		=> $invoice_tanggal,
								'errors_simpony' => $errornya_simpony,
								'modifiedid'	=> $user_web_id,
								'modified'		=> date('Y-m-d H:i:s')
							);
							
							$this->CI->db->where('data_pengajuan_invoice.id', $iddata);
							$insert = $this->CI->db->update('data_pengajuan_invoice', $data_simpony);	
							if($insert){
								
								$message = array(
									"status" => 'error',
									"errors" => $array_respon['status'],
									'simpony_respon' => $array_respon, 
									"json"=>$json_contents,
									"errornya_simpony" => $errornya_simpony
								);
								
							}else{
								$message = array(
									"status" => 'error',
									"errors" => 'Data tidak dapat di simpan',
									'simpony_respon' => $array_respon, 
									"json"=>$json_contents,
									"errornya_simpony" => $errornya_simpony
								);
							}
							
							$message = array(
								"status" => 'error',
								"errors" => $array_respon['status'],
								'simpony_respon' => $array_respon, 
								"json"=>$json_contents,
								"errornya_simpony" => $errornya_simpony
							);
						}
					}
				}else{
					$message = array(
						"status" => 'error',
						"errors" => 'Token Errors, Code : '.$token_data[0],
						"json"=>$json_contents,
						"errornya_simpony" => $errornya_simpony
					);
				}
			}
		}
		
		return $message;
		
	}
	
	
	
	function getPembayaran($id, $user_web_id){
		$headerdata = array();
		$detaildata = array();
		$message = array("status" => 'error',"errors" => 'Data Tidak Ditemukan');
		
		$this->CI->db->select('data_pengajuan_invoice.*');
		$this->CI->db->where('data_pengajuan_invoice.id',$id);
		$this->CI->db->where('data_pengajuan_invoice.active',1);
		$queryinvoice = $this->CI->db->get('data_pengajuan_invoice');
		$queryinvoice = $queryinvoice->result_object();
		if($queryinvoice){
			
			$pengajuan_id = $queryinvoice[0]->pengajuan_id;
			$permohonan_perusahaan_id = $this->select2_getname($pengajuan_id,'data_pengajuan','id','permohonan_perusahaan_id');
			$nama_wajib_bayar = $queryinvoice[0]->invoice_nama;
			
			
			$kode_billing = $queryinvoice[0]->kode_billing;
			
			if($kode_billing != null && $kode_billing != ''){
				
				$data_simpony = array(
					'invoice_no' => $queryinvoice[0]->invoice_no,
					'tanggal' => $queryinvoice[0]->tanggal,
					'trx_id_simponi' => $queryinvoice[0]->trx_id_simponi,
					'kode_billing' => $queryinvoice[0]->kode_billing,
					'tgl_jam_billing' => $queryinvoice[0]->tgl_jam_billing,
					'tgl_jam_expired_billing' => $queryinvoice[0]->tgl_jam_expired_billing
				);
				
				$message = array(
					"status" => 'success',
					"message" => 'Sukses',
					"data" => $data_simpony
				);
				
			}else{
				
				$iddata = $queryinvoice[0]->id;
				$invoice_no = $queryinvoice[0]->invoice_no;
				$invoice_tanggal = $queryinvoice[0]->tanggal;
				$trx_id_kl = $queryinvoice[0]->invoice_no;
				$user_id = '59303';
				$password = 'P@ssw0rdSIMPKTN';
				$expired_date = $queryinvoice[0]->expired;
				$kode_kl = '090';
				$kode_eselon_1 = '09';
				$kode_satker = '647931';
				$jenis_pnbp = 'F';
				$kode_mata_uang = '1';
				$total_nominal_billing = (float)$queryinvoice[0]->total;
				//$nama_wajib_bayar = $nama_wajib_bayar;
				$kode_satker_pemungut = '647931';
				$npwp = '999999999999999';
				$nik = '3204403107910001';
				
				$this->CI->db->select('data_pengajuan_invoice_detail.*');
				$this->CI->db->where('data_pengajuan_invoice_detail.invoice_id',$iddata);
				$this->CI->db->where('data_pengajuan_invoice_detail.active',1);
				$queryinvoicedetail = $this->CI->db->get('data_pengajuan_invoice_detail');
				$queryinvoicedetail = $queryinvoicedetail->result_object();
				if($queryinvoicedetail){
					foreach($queryinvoicedetail as $rowsdata){
						
						//$nama_wajib_bayar = $nama_wajib_bayar;
						$kode_tarif_simponi = $rowsdata->kode_tarif_simponi;
						$kode_pp_simponi = $rowsdata->kode_pp_simponi;
						$kode_akun = $rowsdata->kode_akun;
						$nominal_tarif_pnbp = (float)$rowsdata->nominal;
						$volume = (float)$rowsdata->qty;
						$satuan = $rowsdata->tarif_uom;
						$total_nominal_per_record = (float)$rowsdata->nominal;
						$kode_lokasi_satker = '01';
						$kode_kabkot_satker = '55';
						
						$data_detail = array(
							"nama_wajib_bayar"=> $nama_wajib_bayar,
							"kode_tarif_simponi"=> $kode_tarif_simponi,
							"kode_pp_simponi"=> $kode_pp_simponi,
							"kode_akun"=> $kode_akun,
							"nominal_tarif_pnbp"=> $nominal_tarif_pnbp,
							"volume"=> $volume,
							"satuan"=> $satuan,
							"total_nominal_per_record"=> $total_nominal_per_record,
							"kode_lokasi_satker"=> $kode_lokasi_satker,
							"kode_kabkot_satker"=> $kode_kabkot_satker
						);
						
						array_push($detaildata,$data_detail); 
					}
				}
				
				$headerdata = array(
					"trx_id_kl"=> $trx_id_kl,
					"user_id"=> $user_id,
					"password"=> $password,
					"invoice_no"=> $invoice_no,
					"invoice_tanggal"=> $invoice_tanggal,
					"kode_kl"=> $kode_kl,
					"expired_date"=> $expired_date,
					"kode_eselon_1"=> $kode_eselon_1,
					"kode_satker"=> $kode_satker,
					"jenis_pnbp"=> $jenis_pnbp,
					"kode_mata_uang"=> $kode_mata_uang,
					"total_nominal_billing"=> $total_nominal_billing,
					"nama_wajib_bayar"=> $nama_wajib_bayar,
					"kode_satker_pemungut"=> $kode_satker_pemungut,
					"npwp"=> $npwp,
					"nik"=> $nik,
					'detail' => $detaildata
				);
				
				
				
				$token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6IjEiLCJ1c2VybmFtZSI6InNhZG1pbiIsImZ1bGxuYW1lIjoiU3VwZXJhZG1pbiIsImVtYWlsIjoic2FkbWluQGtlbWVuZGFnLmdvLmlkIiwibm90ZWxwIjoiMDgxMjgxODAzNzQ2IiwiZ3JvdXBfaWQiOiIxIiwiZ3JvdXAiOnsiaWQiOiIxIiwibmFtZSI6IlN1cGVyYWRtaW4iLCJkZXNjcmlwdGlvbiI6IlN1cGVyYWRtaW4iLCJsZXZlbCI6bnVsbH0sImxhc3RfbG9naW4iOiIyMDI1LTAzLTI2IDAyOjAxOjM4IiwiY292ZXIiOiIiLCJBUElfVElNRSI6MTc0Mjk1NDQ5OH0.2l7FUayarHxmCkh8iWJWCx-kKBBgIsew0SVQmBQvgl4';
				
				$json_contents = $headerdata;
				$authorization = "Authorization: Bearer ".$token; // Prepare the authorisation token
				 
				$header = array(
					$authorization,
					'Content-type: application/json'
				);
				
				$curl = curl_init();
				curl_setopt($curl, CURLOPT_URL, $this->linkgateway_dev.'transaction/getBilling');
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				//curl_setopt($curl, CURLOPT_ENCODING, '');
				curl_setopt($curl, CURLOPT_MAXREDIRS, 10);
				curl_setopt($curl, CURLOPT_TIMEOUT, 300);
				curl_setopt($curl, CURLOPT_VERBOSE, true);
				curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
				curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
				curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
				curl_setopt($curl, CURLOPT_POST, 1);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
				curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($json_contents));
				curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
				$response = curl_exec($curl);
				$info = curl_getinfo($curl);
				//print_r($info);
				
				curl_close($curl);
				$result = $response;
				//print_r ($result);
				//die();
				
				$array_respon = json_decode($result, true);
				//print_r($array_respon['data'][0]['billing']);
				//die();
				if(count($array_respon) > 0){
					if($array_respon['status'] == 'success'){
						
						if($array_respon['data'][0]['billing']['status'] == 'success'){
							$data_simpony = array(
								'invoice_no' => $invoice_no,
								'tanggal' => $invoice_tanggal,
								'trx_id_simponi' => $array_respon['data'][0]['billing']['data']['trx_id_simponi'],
								'kode_billing' => $array_respon['data'][0]['billing']['data']['kode_billing'],
								'tgl_jam_billing' => $array_respon['data'][0]['billing']['data']['tgl_jam_billing'],
								'tgl_jam_expired_billing' => $array_respon['data'][0]['billing']['data']['tgl_jam_expired_billing'],
								'modifiedid'	=> $user_web_id,
								'modified'		=> date('Y-m-d H:i:s')
							);
							
							$this->CI->db->where('data_pengajuan_invoice.id', $iddata);
							$insert = $this->CI->db->update('data_pengajuan_invoice', $data_simpony);	
							if($insert){
								
								$data_simpony_respon = array(
									'invoice_no' => $invoice_no,
									'tanggal' => $invoice_tanggal,
									'trx_id_simponi' => $array_respon['data'][0]['billing']['data']['trx_id_simponi'],
									'kode_billing' => $array_respon['data'][0]['billing']['data']['kode_billing'],
									'tgl_jam_billing' => $array_respon['data'][0]['billing']['data']['tgl_jam_billing'],
									'tgl_jam_expired_billing' => $array_respon['data'][0]['billing']['data']['tgl_jam_expired_billing'],
								);
							
								$message = array(
									"status" => 'success',
									"message" => $array_respon['status'],
									"data" => $data_simpony_respon
								);
							}else{
								$message = array(
									"status" => 'error',
									"errors" => 'Data tidak dapat di simpan'
								);
							}
						}else{
							$data_simpony_respon = array(
								'invoice_no' => $invoice_no,
								'tanggal' => $invoice_tanggal,
								'trx_id_simponi' => null,
								'kode_billing' => null,
								'tgl_jam_billing' => null,
								'tgl_jam_expired_billing' => null,
							);
							
							$message = array(
								"status" => 'error',
								"errors" => $array_respon['data'][0]['billing']['status'],
								"message" => $array_respon['status'],
								"data" => $data_simpony_respon
							);
						}
						
						
					}else{
						
						$data_simpony = array(
							'invoice_no' 	=> $invoice_no,
							'tanggal' 		=> $invoice_tanggal,
							'modifiedid'	=> $user_web_id,
							'modified'		=> date('Y-m-d H:i:s')
						);
						
						$this->CI->db->where('data_pengajuan_invoice.id', $iddata);
						$insert = $this->CI->db->update('data_pengajuan_invoice', $data_simpony);	
						if($insert){
							
							$message = array(
								"status" => 'error',
								"errors" => $array_respon['status'],
								"json"=>$json_contents
							);
							
						}else{
							$message = array(
								"status" => 'error',
								"errors" => 'Data tidak dapat di simpan',
								"json"=>$json_contents
							);
						}
						
						$message = array(
							"status" => 'error',
							"errors" => $array_respon['status'],
							"json"=>$json_contents
						);
					}
				}
			
			}
		}
		
		return $message;
		
	}
	

		function generateNomorInvoice()
		{
				// FORMAT SM.07.00/XXXX/PKTN.3.2/SPK/Bulan/Tahun
				// EX : SMA/2020/0001
				$tahun = date("Y");
				$this->CI->db->select('RIGHT(invoice_no,4) as tahun, SUBSTRING(invoice_no,10,4) as invoice_no');
				$this->CI->db->where('invoice_no !=','DRAFT');
				$this->CI->db->order_by("invoice_no", "DESC");
				$this->CI->db->limit(1);
				$query = $this->CI->db->get('data_pengajuan_invoice');
				if($query->num_rows() <> 0)
				{
					$data	= $query->row(); // ambil satu baris data
					$tahun	= $data->tahun;
					if($tahun == date('Y')){
						$kodeLOP  = intval($data->invoice_no) + 1; // tambah 1
					}else{
						$tahun	= date('Y');
						$kodeLOP  = 1;
					}
				}else{
					$kodeLOP  = 1; // isi dengan 1
				}

				$lastKode = str_pad($kodeLOP, 4, "0", STR_PAD_LEFT);
				$LOP      = "PKTN.3.2/INV";
				$newKode  = 'SM.07.00/'.$lastKode.'/'.$LOP."/".date('m').'/'.$tahun;
				
				//2696//11/2024

				return $newKode;  // return kode baru

		}
		
		
		function generateNomortandaterima()
		{
				// FORMAT SM.07.00/XXXX/PKTN.3.2/SPK/Bulan/Tahun
				// EX : SMA/2020/0001
				$tahun = date("Y");
				$this->CI->db->select('RIGHT(tanda_terima_no,4) as tahun, SUBSTRING(tanda_terima_no,10,4) as tanda_terima_no');
				$this->CI->db->where('tanda_terima_no !=','DRAFT');
				$this->CI->db->order_by("tanda_terima_no", "DESC");
				$this->CI->db->limit(1);
				$query = $this->CI->db->get('data_pengajuan_tanda_terima');
				if($query->num_rows() <> 0)
				{
					$data	= $query->row(); // ambil satu baris data
					$tahun	= $data->tahun;
					if($tahun == date('Y')){
						$kodeLOP  = intval($data->tanda_terima_no) + 1; // tambah 1
					}else{
						$tahun	= date('Y');
						$kodeLOP  = 1;
					}
				}else{
					$kodeLOP  = 1; // isi dengan 1
				}

				$lastKode = str_pad($kodeLOP, 4, "0", STR_PAD_LEFT);
				$LOP      = "PKTN.3.2/TA";
				$newKode  = 'SM.07.00/'.$lastKode.'/'.$LOP."/".date('m').'/'.$tahun;
				
				//2696//11/2024

				return $newKode;  // return kode baru

		}
	
			
		public function convert($excelPath, $pdfPath)
		{
			// Path ke file Excel
			//$excelPath = FCPATH . 'uploads/sample.xlsx';
			$excelPath = FCPATH .$excelPath;
			// Load spreadsheet
			$spreadsheet = IOFactory::load($excelPath);
			// Set active sheet ke sheet ke-3 (index 2)
			$spreadsheet->setActiveSheetIndex(3);
			

			// Set PDF renderer (pakai Mpdf)
			\PhpOffice\PhpSpreadsheet\IOFactory::registerWriter('Pdf', Mpdf::class);

			// Buat Writer PDF
			$writer = IOFactory::createWriter($spreadsheet, 'Pdf');

			// Simpan sebagai file PDF (opsional bisa ke browser langsung)
			//$pdfPath = FCPATH . 'uploads/output.pdf';
			$pdfPath = FCPATH .$pdfPath;
			
			// Hanya render active sheet
			$writer->setSheetIndex(3); // ini penting: hanya sheet ke-3 yang dikonversi

			$writer->save($pdfPath);

			// Tampilkan di browser
			//header('Content-Type: application/pdf');
			//header('Content-Disposition: inline; filename="output.pdf"');
			//readfile($pdfPath);
		}


		
		public function convertpdflinuxold($excelFile, $pdfFile, $path, $filename, $cert_id)
		{
			try {
				// Pastikan file ada
				if (!file_exists($excelFile)) {
					throw new Exception("File Excel tidak ditemukan.");
				}

				// Load workbook asli
				$reader = IOFactory::createReaderForFile($excelFile);
				$reader->setLoadAllSheets(); // load semua dulu
				$spreadsheet = $reader->load($excelFile);

				// Cari sheet dengan prefix 'print_'
				$found = false;
				foreach ($spreadsheet->getSheetNames() as $index => $name) {
					if (strpos($name, 'print_') === 0) {
						$targetSheet = $spreadsheet->getSheet($index);
						$found = true;
						break;
					}
				}

				if (!$found) {
					throw new Exception("Worksheet dengan prefix 'print_' tidak ditemukan.");
				}

				// Salin ke workbook baru
				$newSpreadsheet = new Spreadsheet();
				$newSpreadsheet->removeSheetByIndex(0); // hapus sheet default
				$clonedSheet = clone $targetSheet;
				$newSpreadsheet->addSheet($clonedSheet);
				$newSpreadsheet->setActiveSheetIndex(0);

				// Simpan sementara ke file Excel baru
				$tempExcel = sys_get_temp_dir() . '/' . uniqid('sheet_', true) . '.xlsx';
				$writer = IOFactory::createWriter($newSpreadsheet, 'Xlsx');
				$writer->save($tempExcel);

				// Konversi ke PDF dengan LibreOffice
				$outputDir = dirname($pdfFile);
				$command = 'libreoffice --headless --convert-to pdf --outdir ' . escapeshellarg($outputDir) . ' ' . escapeshellarg($tempExcel);
				exec($command, $output, $returnVar);

				if ($returnVar !== 0) {
					throw new Exception("Gagal mengonversi file Excel ke PDF.");
				}

				$convertedPdf = $outputDir . '/' . pathinfo($tempExcel, PATHINFO_FILENAME) . '.pdf';
				if (!file_exists($convertedPdf)) {
					throw new Exception("File PDF hasil konversi tidak ditemukan.");
				}

				// Rename jika perlu
				if ($convertedPdf !== $pdfFile) {
					rename($convertedPdf, $pdfFile);
				}

				// Hapus file sementara
				unlink($tempExcel);

			} catch (Exception $e) {
				log_message('error', 'Convert PDF Error: ' . $e->getMessage());
				return $path;
			}

			// Simpan ke gallery (kode lanjutan tidak berubah)
			$tipe_nama = $filename;
			$ext = 'pdf';
			$name = $tipe_nama . '.' . $ext;
			$size = 1;
			$token = '2.' . rand(100000, 999999) . date('YmdHis');
			$dir = './' . $path;

			$savegallery = $this->saveGallery($name, $ext, $size, $token, $path, $dir, $this->CI->session->userdata('userid'));

			if ($savegallery['message'] == 'success') {
				$doc_id = $savegallery['id'];
				if ($doc_id) {
					$datadetail = array(
						'page2' => $doc_id,
						'modifiedid' => $this->CI->session->userdata('userid'),
						'modified' => date('Y-m-d H:i:s')
					);

					$this->CI->db->where('data_certificate.id', $cert_id);
					$update = $this->CI->db->update('data_certificate', $datadetail);
					if ($update) {
						return $savegallery['path_save'];
					}
				}
				return $path;
			}

			return $path;
		}
		
	public function convertpdflinux($path, $excelfile, $galleryname, $filename, $cert_id)
{
    $dir = './' . $path;
    $this->createSubdirectories($dir, ['', '/all/', '/page1/', '/page2/', '/convert/']);

    if (PHP_OS === 'WINNT') {
        return $this->convertpdfwindows(
            $this->sanitizePath(FCPATH . $excelfile),
            $this->sanitizePath(FCPATH . $path . '/page2/' . $filename . '.pdf'),
            $path . '/page2/' . $filename . '.pdf',
            $filename . '.pdf',
            $cert_id
        );
    }

    $inputFile = FCPATH . $excelfile;
    $tempExcel = FCPATH . $path . '/convert/temp_print_sheet.xlsx';
    $finalPDF = FCPATH . $path . '/page2/' . $filename . '.pdf';

    if (!$this->extractPrintableSheet($inputFile, $tempExcel)) {
        return null;
    }

    if (!$this->convertExcelToPdf($tempExcel, $filename, $finalPDF)) {
        return null;
    }

    return $this->saveToGalleryAndUpdateCert($finalPDF, $filename, $cert_id);
}

private function createSubdirectories($basePath, array $subdirs)
{
    foreach ($subdirs as $subdir) {
        $fullPath = $basePath . $subdir;
        if (!file_exists($fullPath)) {
            mkdir($fullPath, 0755, true);
        }
    }
}

private function sanitizePath($path)
{
    return str_replace('/', DIRECTORY_SEPARATOR, $path);
}

private function extractPrintableSheet($inputFile, $outputExcel)
{
    $spreadsheet = IOFactory::load($inputFile);
    $processedSheet = false;  // Flag untuk mengecek apakah ada sheet yang diproses

    // Loop untuk setiap sheet di dalam spreadsheet
    foreach ($spreadsheet->getSheetNames() as $i => $sheetName) {
        // Jika sheet tidak memiliki prefix 'print_', lewati sheet ini
        if (strpos($sheetName, 'print_') !== 0) {
            continue;  // Sheet ini tidak akan diproses dan dilanjutkan ke sheet berikutnya
        }

        $sourceSheet = $spreadsheet->getSheet($i);
        $printArea = $sourceSheet->getPageSetup()->getPrintArea();

        if ($printArea) {
            // Jika ada area print yang ditentukan, ambil koordinatnya
            [$startCell, $endCell] = explode(':', $printArea);
            [$startCol, $startRow] = Coordinate::coordinateFromString($startCell);
            [$endCol, $endRow] = Coordinate::coordinateFromString($endCell);

            $startColIndex = Coordinate::columnIndexFromString($startCol);
            $endColIndex = Coordinate::columnIndexFromString($endCol);
        } else {
            // Ambil area data jika tidak ada area print yang ditentukan
            $startColIndex = 1;
            $endColIndex = Coordinate::columnIndexFromString($sourceSheet->getHighestDataColumn());
            $startRow = 1;
            $endRow = $sourceSheet->getHighestDataRow();
        }

        // Buat salinan sheet dan atur judul
        $copiedSheet = new Spreadsheet();
        $targetSheet = $copiedSheet->getActiveSheet();
        $targetSheet->setTitle($sourceSheet->getTitle());

        // Salin pengaturan halaman dan data
        $this->copyPageSetup($sourceSheet, $targetSheet);
        $this->copyStylesAndData($sourceSheet, $targetSheet, $startColIndex, $endColIndex, $startRow, $endRow);

        // Atur area cetak
        $startColLetter = Coordinate::stringFromColumnIndex($startColIndex);
        $endColLetter = Coordinate::stringFromColumnIndex($endColIndex);
        $targetSheet->getPageSetup()->setPrintArea("{$startColLetter}{$startRow}:{$endColLetter}{$endRow}");

        // Tulis hasil ke file Excel output
        $writer = new Xlsx($copiedSheet);
        $writer->save($outputExcel);

        $processedSheet = true; // Menandakan bahwa setidaknya ada satu sheet yang diproses
    }

    // Jika tidak ada sheet yang diproses, log dan kembalikan false
    if (!$processedSheet) {
        log_message('error', 'Tidak ada sheet dengan prefix "print_" ditemukan di file: ' . $inputFile);
        return false;
    }

    return true; // Semua sheet yang sesuai telah diproses
}


private function copyStylesAndData($source, $target, $startColIndex, $endColIndex, $startRow, $endRow)
{
    // Menyalin lebar kolom
    for ($col = $startColIndex; $col <= $endColIndex; $col++) {
        $columnLetter = Coordinate::stringFromColumnIndex($col);
        $sourceColumnDimension = $source->getColumnDimension($columnLetter);
        $targetColumnDimension = $target->getColumnDimension($columnLetter);

        // Menyalin lebar kolom jika ada
        if ($sourceColumnDimension->getWidth()) {
            $targetColumnDimension->setWidth($sourceColumnDimension->getWidth());
        }
    }

    // Menyalin tinggi baris
    for ($row = $startRow; $row <= $endRow; $row++) {
        $sourceRowDimension = $source->getRowDimension($row);
        $targetRowDimension = $target->getRowDimension($row);

        // Menyalin tinggi baris jika ada
        if ($sourceRowDimension->getRowHeight()) {
            $targetRowDimension->setRowHeight($sourceRowDimension->getRowHeight());
        }
    }

    // Menyalin isi dan gaya untuk setiap sel
    for ($row = $startRow; $row <= $endRow; $row++) {
        for ($col = $startColIndex; $col <= $endColIndex; $col++) {
            $coord = Coordinate::stringFromColumnIndex($col) . $row;

            // Menyalin nilai sel (dan formula jika ada)
            $cell = $source->getCell($coord);
            if ($cell->getDataType() == \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_FORMULA) {
                $target->setCellValue($coord, $cell->getCalculatedValue());
            } else {
                $target->setCellValueExplicit($coord, $cell->getValue(), $cell->getDataType());
            }

            // Menyalin style sel
            $style = $source->getStyle($coord);
            $this->copyCellStyle($style, $target->getStyle($coord));
        }
    }

    // Menyalin merged cells
    $this->copyMergedCells($source, $target, $startColIndex, $endColIndex, $startRow, $endRow);
}
private function copyMergedCells($source, $target, $startColIndex, $endColIndex, $startRow, $endRow)
{
    $mergedCells = $source->getMergeCells();

    foreach ($mergedCells as $range) {
        [$startCell, $endCell] = explode(":", $range);

        [$startCol, $startRowCoord] = Coordinate::coordinateFromString($startCell);
        [$endCol, $endRowCoord] = Coordinate::coordinateFromString($endCell);

        $startColIndexCoord = Coordinate::columnIndexFromString($startCol);
        $endColIndexCoord = Coordinate::columnIndexFromString($endCol);

        // Bandingkan apakah merged cell ini masih dalam range yang disalin
        if (
            $startColIndexCoord >= $startColIndex && $endColIndexCoord <= $endColIndex &&
            $startRowCoord >= $startRow && $endRowCoord <= $endRow
        ) {
            $target->mergeCells("{$startCol}{$startRowCoord}:{$endCol}{$endRowCoord}");
        }
    }
}

private function copyCellStyle($sourceStyle, $targetStyle)
{
    // Menyalin warna font
    if ($sourceStyle->getFont()->getColor()) {
        $targetStyle->getFont()->getColor()->setARGB($sourceStyle->getFont()->getColor()->getARGB());
    }

    // Menyalin ukuran font
    if ($sourceStyle->getFont()->getSize()) {
        $targetStyle->getFont()->setSize($sourceStyle->getFont()->getSize());
    }

    // Menyalin gaya font (tebal, miring, garis bawah, dll)
    if ($sourceStyle->getFont()->getBold()) {
        $targetStyle->getFont()->setBold(true);
    }
    if ($sourceStyle->getFont()->getItalic()) {
        $targetStyle->getFont()->setItalic(true);
    }

    // Menyalin warna latar belakang
    if ($sourceStyle->getFill()->getStartColor()) {
        $targetStyle->getFill()->getStartColor()->setARGB($sourceStyle->getFill()->getStartColor()->getARGB());
    }

    // Menyalin batas (border)
    foreach (['top', 'right', 'bottom', 'left'] as $borderDirection) {
        if ($sourceStyle->getBorders()->{"get" . ucfirst($borderDirection)}()) {
            $targetStyle->getBorders()->{"get" . ucfirst($borderDirection)}()
                ->setBorderStyle($sourceStyle->getBorders()->{"get" . ucfirst($borderDirection)}()->getBorderStyle());
        }
    }

    // Menyalin alignment (perataan teks)
    $targetStyle->getAlignment()->setHorizontal($sourceStyle->getAlignment()->getHorizontal());
    $targetStyle->getAlignment()->setVertical($sourceStyle->getAlignment()->getVertical());

    // Menyalin padding kiri dan kanan (indentasi)
    $targetStyle->getAlignment()->setIndent($sourceStyle->getAlignment()->getIndent());

    // Menyalin format angka (jika ada)
    if ($sourceStyle->getNumberFormat()->getFormatCode()) {
        $targetStyle->getNumberFormat()->setFormatCode($sourceStyle->getNumberFormat()->getFormatCode());
    }
}

private function copyPageSetup($source, $target)
{
    $pageSetup = $source->getPageSetup();
    $target->getPageSetup()->setPaperSize($pageSetup->getPaperSize());
    $target->getPageSetup()->setOrientation($pageSetup->getOrientation());

    $pageMargins = $source->getPageMargins();
    $target->getPageMargins()->setTop($pageMargins->getTop());
    $target->getPageMargins()->setBottom($pageMargins->getBottom());
    $target->getPageMargins()->setLeft($pageMargins->getLeft());
    $target->getPageMargins()->setRight($pageMargins->getRight());
    $target->getPageMargins()->setHeader($pageMargins->getHeader());
    $target->getPageMargins()->setFooter($pageMargins->getFooter());
}

private function convertExcelToPdf($excelFile, $filename, $pdfPath)
{
    try {
        $converter = new OfficeConverter($excelFile);
        $converter->convertTo($filename . '.pdf');

        $generatedPdf = dirname($excelFile) . '/' . $filename . '.pdf';

        if (!file_exists($generatedPdf)) {
            return false;
        }

        rename($generatedPdf, $pdfPath);
        return true;
    } catch (Exception $e) {
        log_message('error', 'PDF conversion failed: ' . $e->getMessage());
        return false;
    }
}

private function saveToGalleryAndUpdateCert($filePath, $filename, $cert_id)
{
    $ext = 'pdf';
    $name = $filename . '.' . $ext;
    $size = filesize($filePath);
    $token = '2.' . rand(100000, 999999) . date('YmdHis');

    $savegallery = $this->saveGallery($name, $ext, $size, $token, $filePath, './' . $filePath, $this->CI->session->userdata('userid'));

    if ($savegallery['message'] === 'success') {
        $doc_id = $savegallery['id'];
        if ($doc_id) {
            $this->CI->db->where('data_certificate.id', $cert_id);
            $this->CI->db->update('data_certificate', [
                'page2' => $doc_id,
                'modifiedid' => $this->CI->session->userdata('userid'),
                'modified' => date('Y-m-d H:i:s')
            ]);
            return $savegallery['path_save'];
        }
    }

    return $filePath;
}


		public function convertpdflinux1($path, $excelfile, $galleryname, $filename, $cert_id)
		{
			$dir = './' . $path;

			// Buat direktori jika belum ada
			foreach (['', '/all/', '/page1/', '/page2/', '/convert/'] as $subdir) {
				if (!file_exists($dir . $subdir)) {
					mkdir($dir . $subdir, 0755, true);
				}
			}

			if (PHP_OS == 'WINNT') {
				$inputFile = FCPATH . $excelfile;
				$inputFile = str_replace('/', '\\', $inputFile);
				$outputfile = FCPATH . $path . '/page2/' . $filename . '.pdf';
				$outputfile = str_replace('/', '\\', $outputfile);
				$path = $path . '/page2/' . $filename . '.pdf';
				return $this->convertpdfwindows($inputFile, $outputfile, $path, $filename . '.pdf', $cert_id);
			} else {
				$inputFile = FCPATH . $excelfile;
				$tempExcel = FCPATH . $path . '/convert/temp_print_sheet.xlsx';
				$finalPDF = FCPATH . $path . '/page2/' . $filename . '.pdf';

				// Load Excel dan cari sheet 'print_'
				$spreadsheet = IOFactory::load($inputFile);
				$found = false;

				foreach ($spreadsheet->getSheetNames() as $i => $sheetName) {
					if (strpos($sheetName, 'print_') === 0) {
						$sourceSheet = $spreadsheet->getSheet($i);
						$newSpreadsheet = new Spreadsheet();
						$newSpreadsheet->removeSheetByIndex(0); // Hapus default sheet
						$copiedSheet = $newSpreadsheet->createSheet();
						$copiedSheet->setTitle($sourceSheet->getTitle());

						// Salin isi cell (value dan formula)
						$highestRow = $sourceSheet->getHighestRow();
						$highestColumn = $sourceSheet->getHighestColumn();
						$highestColIndex = Coordinate::columnIndexFromString($highestColumn);

						for ($row = 1; $row <= $highestRow; $row++) {
							for ($col = 1; $col <= $highestColIndex; $col++) {
								$cellCoordinate = Coordinate::stringFromColumnIndex($col) . $row;
								$cell = $sourceSheet->getCell($cellCoordinate);
								$newCell = $copiedSheet->getCell($cellCoordinate);

								$newCell->setValue($cell->getCalculatedValue());

								// Salin style (opsional tapi disarankan)
								// Menjadi:
								// Menyalin gaya per sel
// Menyalin font style
$font = $sourceSheet->getStyle($cellCoordinate)->getFont();
$copiedSheet->getStyle($cellCoordinate)->getFont()->setName($font->getName())
    ->setSize($font->getSize())
    ->setBold($font->getBold())
    ->setItalic($font->getItalic())
    ->setUnderline($font->getUnderline());

// Menyalin warna font, kita buat objek Color terlebih dahulu
$color = $font->getColor();
$copiedSheet->getStyle($cellCoordinate)->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color($color->getRGB()));

// Menyalin border style
$border = $sourceSheet->getStyle($cellCoordinate)->getBorders();
$copiedSheet->getStyle($cellCoordinate)->getBorders()->getTop()->setBorderStyle($border->getTop()->getBorderStyle());
$copiedSheet->getStyle($cellCoordinate)->getBorders()->getBottom()->setBorderStyle($border->getBottom()->getBorderStyle());
$copiedSheet->getStyle($cellCoordinate)->getBorders()->getLeft()->setBorderStyle($border->getLeft()->getBorderStyle());
$copiedSheet->getStyle($cellCoordinate)->getBorders()->getRight()->setBorderStyle($border->getRight()->getBorderStyle());

// Menyalin fill color (warna latar belakang)
// Membuat objek Color untuk startColor dan endColor
$fill = $sourceSheet->getStyle($cellCoordinate)->getFill();
$copiedSheet->getStyle($cellCoordinate)->getFill()->setFillType($fill->getFillType())
    ->setStartColor(new \PhpOffice\PhpSpreadsheet\Style\Color($fill->getStartColor()->getRGB()))
    ->setEndColor(new \PhpOffice\PhpSpreadsheet\Style\Color($fill->getEndColor()->getRGB()));

// Menyalin alignment (penyusunan teks)
$alignment = $sourceSheet->getStyle($cellCoordinate)->getAlignment();
$copiedSheet->getStyle($cellCoordinate)->getAlignment()->setHorizontal($alignment->getHorizontal())
    ->setVertical($alignment->getVertical())
    ->setWrapText($alignment->getWrapText());


							}
						}

						// Tetapkan sheet hasil sebagai aktif
						$newSpreadsheet->removeSheetByIndex(0); // Hapus kosong
						$newSpreadsheet->addSheet($copiedSheet);
						$newSpreadsheet->setActiveSheetIndex(0);

						$writer = new Xlsx($newSpreadsheet);
						$writer->save($tempExcel);
						$found = true;
						break;
					}
				}

				if (!$found) {
					return null; // Sheet dengan prefix print_ tidak ditemukan
				}

				// Konversi ke PDF
				$converter = new OfficeConverter($tempExcel);
				$converter->convertTo($filename . '.pdf'); // Output di folder yang sama

				// Pindahkan hasil ke folder page2/
				$convertedPdf = dirname($tempExcel) . '/' . $filename . '.pdf';
				if (!file_exists($convertedPdf)) {
					return null;
				}
				rename($convertedPdf, $finalPDF);

				// Simpan ke galeri
				$ext = 'pdf';
				$name = $filename . '.' . $ext;
				$size = 1;
				$token = '2.' . rand(100000, 999999) . date('YmdHis');
				$savegallery = $this->saveGallery($name, $ext, $size, $token, $finalPDF, './' . $finalPDF, $this->CI->session->userdata('userid'));

				if ($savegallery['message'] == 'success') {
					$doc_id = $savegallery['id'];
					if ($doc_id) {
						$datadetail = array(
							'page2' => $doc_id,
							'modifiedid' => $this->CI->session->userdata('userid'),
							'modified' => date('Y-m-d H:i:s')
						);

						$this->CI->db->where('data_certificate.id', $cert_id);
						$update = $this->CI->db->update('data_certificate', $datadetail);
						if ($update) {
							return $savegallery['path_save'];
						}
					}
					return $finalPDF;
				}

				return $finalPDF;
			}
		}

		
		public function releaseCOMObject(&$comObj) {
			if ($comObj) {
				try {
					//$comObj->Release(); // optional, kadang COM support ini
				} catch (Exception $e) {
					// skip error
				}
				$comObj = null;
			}
			gc_collect_cycles(); // paksa PHP bersihin memory
		}

		public function convertpdfwindows($excelFile, $pdfFile, $path, $filename, $cert_id)
		{
			try {
				$excel = new COM("Excel.Application") or die("Tidak bisa mengakses Excel");
				$excel->Visible = false;
				$excel->DisplayAlerts = false;

				$workbook = $excel->Workbooks->Open($excelFile, null, true);

				$targetWorksheet = null;
				// Iterasi worksheet
				foreach ($workbook->Worksheets as $sheet) {
					$name = $sheet->Name;
					if (strpos($name, 'print_') === 0) {
						$targetWorksheet = $sheet;
						break;
					}
				}

				if (!$targetWorksheet) {
					throw new Exception("Worksheet dengan prefix 'print_' tidak ditemukan.");
				}

				// Export worksheet yang ditemukan
				$targetWorksheet->ExportAsFixedFormat(0, $pdfFile);

				$workbook->Close(false);
				$excel->Quit();

				// Release COM
				$this->releaseCOMObject($targetWorksheet);
				$this->releaseCOMObject($workbook);
				$this->releaseCOMObject($excel);
				gc_collect_cycles();

			} catch (Exception $e) {
				if (isset($targetWorksheet)) $this->releaseCOMObject($targetWorksheet);
				if (isset($workbook)) {
					$workbook->Close(false);
					$this->releaseCOMObject($workbook);
				}
				if (isset($excel)) {
					$excel->Quit();
					$this->releaseCOMObject($excel);
				}
				gc_collect_cycles();

				// throw new Exception("Gagal konversi Excel ke PDF: " . $e->getMessage());
			}

			// Simpan ke gallery (lanjutan kode tidak diubah)
			$tipe_nama = $filename;
			$ext = 'pdf';
			$name = $tipe_nama.'.'.$ext;
			$size = 1;
			$token = '2.' . rand(100000, 999999) . date('YmdHis');
			$dir = './' . $path;

			$savegallery = $this->saveGallery($name, $ext, $size, $token, $path, $dir, $this->CI->session->userdata('userid'));

			if ($savegallery['message'] == 'success') {
				$doc_id = $savegallery['id'];
				if ($doc_id) {
					$datadetail = array(
						'page2' => $doc_id,
						'modifiedid' => $this->CI->session->userdata('userid'),
						'modified' => date('Y-m-d H:i:s')
					);

					$this->CI->db->where('data_certificate.id', $cert_id);
					$update = $this->CI->db->update('data_certificate', $datadetail);
					if ($update) {
						return $savegallery['path_save'];
					}
				}
				return $path;
			}

			return $path;
		}


		
		public function getCert($page1, $certificate_no)
		{
			
			
			$pdfFiles = [$page1]; // Add your file names here
			$certificate_file = $this->mergePDF($certificate_no,$pdfFiles, 1);
			
			return $certificate_file;
			
			$this->CI->db->select('data_certificate.id, data_certificate.certificate_no, data_gallery.path, data_gallery.name as filename');
			$this->CI->db->where('data_certificate.certificate_no',$certificate_no);
			$this->CI->db->where('data_certificate.active',1);
			$this->CI->db->join('data_alat','data_alat.certificate_id = data_certificate.id');
			$this->CI->db->join('data_gallery','data_gallery.id = data_alat.alat_file_kalibrasi_verifikator_dua'); //ganti file disini
			$this->CI->db->limit(1);
			$querycer = $this->CI->db->get('data_certificate');
			$querycer = $querycer->result_object();
			if($querycer){
				$path = 'file/'.date('Y').'/certificate';
				$page2 = $this->convertpdflinux($path,$querycer[0]->path, $querycer[0]->filename, $querycer[0]->certificate_no, $querycer[0]->id);

				if($page2 != null){
					$pdfFiles = [$page1, $page2]; // Add your file names here
					$certificate_file = $this->mergePDF($certificate_no,$pdfFiles, $querycer[0]->id);
					$dataid = array(
						'certificate_file' 	=> $certificate_file,
						'modifiedid'	=> $this->CI->session->userdata('userid'),
						'modified'		=> date('Y-m-d H:i:s')
					);
																
					$this->CI->db->where('id', $querycer[0]->id);
					$update = $this->CI->db->update('data_certificate', $dataid);
					if($update){
						return $certificate_file;
					}
				}

			}
			
			return null;
		}
		
		function savePDF($html, $outputfile){
			Browsershot::html($html)->waitUntilNetworkIdle()
			  ->setOption('no-sandbox', true)
			  ->savePdf(FCPATH .$outputfile);
		}


		function mergePDF($filename, $arraypdf, $cert_id) {
			$pdf = new Fpdi();
			$pdf->SetAutoPageBreak(false);
			$isFirstPage = true; // Flag untuk mengecek halaman pertama
			// $headerImagePath = FCPATH . 'header_backup.png';
			// $headerImagePathdua = FCPATH . 'header_backup.png';
			$watermarkImagePath = FCPATH . 'themes/ortyd/assets/media/format/sertifikat1bg.jpg';
			$headerHeight = 0;
			
			$footerText1 = "";
			$footerText2 = "";
			$footerText3 = "";
			$footerText4 = "";
			$footerBoxText = "This sertificate issued by Directorate General of Consumer Protection and Trade Compliance’s electronic services system. No Signature or seal is required";

			if (count($arraypdf) === 0) return null;

			$bottomMargin = 12; // Jarak dari bawah halaman
			$x = 0;
			$y=0;
			foreach ($arraypdf as $file) {
				if (!file_exists($file)) continue;

				try {
					$pageCount = $pdf->setSourceFile($file);
				} catch (Exception $e) {
					continue;
				}

				for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
					try {
						$templateId = $pdf->importPage($pageNo);
						if ($x == 0) {
							$size = $pdf->getTemplateSize($templateId);
							$x++;
						}
					} catch (Exception $e) {
						continue;
					}

					$size['width'] = 450;
					$size['height'] = 450;

					$pdf->AddPage('L', [$size['width'], $size['height']]);

					// Watermark
					$watermarkWidth = $size['width'] / 1.2;
					$watermarkHeight = 0;
					$watermarkX = ($size['width'] - $watermarkWidth) / 2;
					$watermarkY = ($size['height'] - ($size['height'] / 2)) / 3.5;
					// $pdf->Image($watermarkImagePath, $watermarkX, $watermarkY, $watermarkWidth, $watermarkHeight, '', '', '', false, 100, '', false, false, 0);

					// Header hanya pada halaman pertama
					if ($isFirstPage) {
						// $pdf->Image($headerImagePath, 0, 0, $size['width']);
						$isFirstPage = false; // Set flag ke false setelah halaman pertama
					}else{
						// $pdf->Image($headerImagePathdua, 0, 0, $size['width']);
					}
					// Header

					// Template Konten PDF
					if($y == 0){
						$contentY = $headerHeight - 25;
						$pdf->useTemplate($templateId, 0, $contentY, $size['width'], $size['height']); // dipotong supaya ga numpuk footer
					}else{
						$contentY = $headerHeight;
						$pdf->useTemplate($templateId, 0, $contentY, $size['width'], $size['height']); // dipotong supaya ga numpuk footer
					}
					
					// Footer Texts
					$pdf->SetFont('Times', '', 9);
					$pdf->SetTextColor(0, 0, 0); // warna hitam normal
					$pdf->SetY($size['height'] - $bottomMargin); 
					$pdf->SetFont('Times', 'BI', 9);
					$pdf->Cell(0, 3, $footerText1, 0, 1, 'L');
					$pdf->SetFont('Times', 'I', 9);
					$pdf->Cell(0, 3, $footerText3, 0, 1, 'L');
					$pdf->SetFont('Times', 'BI', 9);
					$pdf->Cell(0, 3, $footerText2, 0, 1, 'L');
					$pdf->SetFont('Times', 'I', 9);
					$pdf->Cell(0, 3, $footerText4, 0, 1, 'L');

					// Kotak Kuning + Teks
					//$boxHeight = 12;
					//$boxY = $size['height'] - $bottomMargin - $boxHeight + 12;
					//$pdf->SetFillColor(245, 200, 100); // warna kuning
					//$pdf->Rect(0, $boxY, $size['width'], $boxHeight, 'F');

					//$pdf->SetFont('Times', '', 10);
					//$pdf->SetTextColor(80, 50, 0); // warna coklat gelap

					// Geser teks lebih ke bawah di dalam kotak kuning
					//$pdf->SetY($boxY + 10 - 9);  // <<-- dari 5 diubah ke 10 supaya lebih ke bawah
					//$pdf->MultiCell(0, 5, $footerBoxText, 0, 'C');
					$y++;
				}
			}

			// Simpan PDF
			$outputPath = FCPATH . 'file/certificate/all/' . $filename . '.pdf';
			if (PHP_OS == 'WINNT') {
				$outputPath = str_replace('/', '\\', $outputPath);
			}

			$outputDir = dirname($outputPath);
			if (!is_dir($outputDir)) {
				mkdir($outputDir, 0755, true);
			}

			$pdf->Output('F', $outputPath);

			// Save ke gallery
			$tipe_nama = $filename;
			$ext = 'pdf';
			$name = $tipe_nama.'.'.$ext;
			$size = 1;
			$token = '2.' . rand(100000, 999999) . date('YmdHis');
			$path = 'file/certificate/all/' . $filename . '.pdf';
			$dir = './' . $path;

			$savegallery = $this->saveGallery($name, $ext, $size, $token, $path, $dir, $this->CI->session->userdata('userid'));
			if ($savegallery['message'] == 'success') {
				$doc_id = $savegallery['id'];
				if ($doc_id) {
					
					return $savegallery['link'];
					
					$datadetail = array(
						'certificate' => $doc_id,
						'modifiedid' => $this->CI->session->userdata('userid'),
						'modified' => date('Y-m-d H:i:s')
					);

					$this->CI->db->where('data_certificate.id', $cert_id);
					$update = $this->CI->db->update('data_certificate', $datadetail);
					if ($update) {
						return $savegallery['link'];
					}
				}
			}

			return false;
		}
		
		
		function replacePage1FileWithHeaderAndFooter($file, $filename,$cert_id) {
			$pdf = new Fpdi();
			$pdf->SetAutoPageBreak(false);

			// Paths to header and footer images/text
			$headerImagePath = FCPATH . 'header.png';
			$headerImagePathdua = FCPATH . 'header-dua.png';
			$watermarkImagePath = FCPATH . 'themes/ortyd/assets/media/format/sertifikat1.jpg';
			$headerHeight = 35;
			$footerHeight = 20; // Perkiraan tinggi area footer

			$footerText1 = "Sertifikat ini diterbitkan melalui sistem pelayanan secara elektronik pada Direktorat Jenderal Perlindungan Konsumen dan Tertib Niaga Kementerian Perdagangan yang tidak membutuhkan cap dan tanda tangan basah.";
			$footerText2 = "";
			$footerBoxText = "This sertificate issued by Directorate General of Consumer Protection and Trade Compliance’s electronic services system. No Signature or seal is required";
			$footerBoxHeight = 12;
			$isFirstPage = true; // Flag untuk mengecek halaman pertama
			
			$bottomMargin = 15; // Jarak dari bawah halaman
			$x = 0;
			
			if (!file_exists(FCPATH.$file)) return null;

			try {
				$pageCount = $pdf->setSourceFile(FCPATH.$file); // Get the number of pages
				if ($pageCount < 1) {
					return null; // No pages to process
				}
			} catch (Exception $e) {
				return null;
			}

			try {
				$templateId = $pdf->importPage(1); // Import hanya halaman pertama
				$size = $pdf->getTemplateSize($templateId);
			} catch (Exception $e) {
				return null;
			}

			// Add page with the same size as the template
			$pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);

			// Watermark
					$watermarkWidth = $size['width'] / 1.2;
					$watermarkHeight = 0;
					$watermarkX = ($size['width'] - $watermarkWidth) / 2;
					$watermarkY = ($size['height'] - ($size['height'] / 2)) / 3.5;
					$pdf->Image($watermarkImagePath, $watermarkX, $watermarkY, $watermarkWidth, $watermarkHeight, '', '', '', false, 100, '', false, false, 0);



					// Header hanya pada halaman pertama
					if ($isFirstPage) {
						$pdf->Image($headerImagePath, 0, 0, $size['width']);
						$isFirstPage = false; // Set flag ke false setelah halaman pertama
					}else{
						$pdf->Image($headerImagePathdua, 0, 0, $size['width']);
					}
					// Header
					

					// Template Konten PDF
					$pdf->useTemplate($templateId, 0, $headerHeight, $size['width'], $size['height'] - $headerHeight - $bottomMargin - 20); // dipotong supaya ga numpuk footer

					// Footer Texts
					$pdf->SetFont('Times', '', 10);
					$pdf->SetTextColor(0, 0, 0); // warna hitam normal
					$pdf->SetY($size['height'] - $bottomMargin - 10); 
					$pdf->Cell(0, 5, $footerText1, 0, 1, 'C');
					//$pdf->SetFont('Times', 'I', 10);
					//$pdf->Cell(0, 5, $footerText2, 0, 1, 'C');

					// Kotak Kuning + Teks
					$boxHeight = 12;
					$boxY = $size['height'] - $bottomMargin - $boxHeight + 12;
					$pdf->SetFillColor(245, 200, 100); // warna kuning
					$pdf->Rect(0, $boxY, $size['width'], $boxHeight, 'F');

					$pdf->SetFont('Times', '', 10);
					$pdf->SetTextColor(80, 50, 0); // warna coklat gelap

					// Geser teks lebih ke bawah di dalam kotak kuning
					$pdf->SetY($boxY + 10 - 9);  // <<-- dari 5 diubah ke 10 supaya lebih ke bawah
					$pdf->MultiCell(0, 5, $footerBoxText, 0, 'C');

			// Save the new PDF (akan menimpa file asli)
			
			$tipe_nama = $filename;
			$ext = 'pdf';
			$name = $tipe_nama;
			$size = 1;
			$token = '2.' . rand(100000, 999999) . date('YmdHis');
			$path = 'file/'.date('Y').'/certificate/page1/print/' . $name;
			
			$outputPath = $path;
			if (PHP_OS == 'WINNT') {
				$outputPath = str_replace('/', '\\', $outputPath);
			}

			$outputDir = dirname($outputPath);
			if (!is_dir($outputDir)) {
				mkdir($outputDir, 0755, true);
			}

			$pdf->Output('F', $outputPath);
			
			
			// Save ke gallery
			
			
			$dir = './' . $path;

			$savegallery = $this->saveGallery($name, $ext, $size, $token, $path, $dir, 1);
			if ($savegallery['message'] == 'success') {
				$doc_id = $savegallery['id'];
				if ($doc_id) {
					$datadetail = array(
						'page1print' => $doc_id,
						'modifiedid' => $this->CI->session->userdata('userid'),
						'modified' => date('Y-m-d H:i:s')
					);

					$this->CI->db->where('data_certificate.id', $cert_id);
					$update = $this->CI->db->update('data_certificate', $datadetail);
					if ($update) {
						return $doc_id;
					}
				}
			}
			
			return null;
		}

		function saveGallery($nama,$ext,$size,$token,$path,$dir,$user_id){
			
			
			$this->CI->db->select('data_gallery.*');
			$this->CI->db->where('data_gallery.path',$path);
			$queryexclude = $this->CI->db->get('data_gallery');
			$queryexclude = $queryexclude->result_object();
			if(!$queryexclude){
				$data = array(
					'name'			=> $nama,
					'file_size'		=> $size * 1000,
					'token'			=> $token,
					'path'			=> $path,
					'path_server'	=> $dir,
					'createdid'		=> $user_id,
					'created'		=> date('Y-m-d H:i:s'),
					'modifiedid'	=> $user_id,
					'modified'		=> date('Y-m-d H:i:s'),
					'file_store_format'	=> $ext,
					'url_server'	=> base_url()
				);
								
				$insert = $this->CI->db->insert('data_gallery',$data);
				$insertid = $this->CI->db->insert_id();
				
				if($insert){
					$link = base_url().'data_gallery/viewdokumen?path='.$path.'&tipe='.$ext.'&token='.$token;
					$encodedlink = encrypt_short($link);
					
					$result = array(
						"message" => "success",
						"csrf_hash" => $this->CI->security->get_csrf_hash(),
						'id' => $insertid,
						'name' => $nama, 	
						'path' => base_url().$path, 	
						'path_save' => $path,
						'link' => base_url().'dokumenview/'.$encodedlink,
						"extention" => strtolower($ext)
					);
					
					return $result;
				}else{
					$data['status'] = 'error';	
					$data['errors'] = 'Data not insert storage';
					$result = array("csrf_hash" => $this->CI->security->get_csrf_hash(),"message" => "error", "m" => $data['errors']);
					return $result;
				}
			}else{
				
				$data = array(
					'name'			=> $nama,
					'modifiedid'	=> $user_id,
					'modified'		=> date('Y-m-d H:i:s')
				);
				
				$this->CI->db->where('data_gallery.id',$queryexclude[0]->id);
				$insert = $this->CI->db->update('data_gallery',$data);
				$insertid = $queryexclude[0]->id;
				
				$link = base_url().'data_gallery/viewdokumen?path='.$queryexclude[0]->path.'&tipe='.$queryexclude[0]->file_store_format.'&token='.$queryexclude[0]->token;
				$encodedlink = encrypt_short($link);
					
				$result = array(
					"message" => "success",
					"csrf_hash" => $this->CI->security->get_csrf_hash(),
					'id' => $queryexclude[0]->id,
					'name' => $queryexclude[0]->name,	
					'path' => base_url().$queryexclude[0]->path, 
					'path_save' => $queryexclude[0]->path, 
					'link' => base_url().'dokumenview/'.$encodedlink,
					"extention" => strtolower($queryexclude[0]->file_store_format)
				);
					
				return $result;
			}
			
			
					
		}
		
		
	
	function getMaster($table, $id)
	{
		
		if ($table != '' && $id != '') {
			
			$exclude_new = array('created','modified','createdid','modifiedid','active','slug','color');
			$select = array();
			$query_column = $this->query_column($table, $exclude_new);
			if($query_column){
				foreach($query_column as $rowsdata){
					array_push($select,$table.'.'."`".$rowsdata['name']."`");
				}
			}
			
			$selectnya = implode(",", $select);
			if($selectnya != ''){
				$selectnya = ','.$selectnya;
			}
			$selectdata = $selectnya;
			
			

			if($table == 'users_data'){
				$this->CI->db->select('users_data.id, users_data.fullname, users_data.username, users_data.email');
			}else{
				$this->CI->db->select($selectdata);
			}
			
			$this->CI->db->where($table.'.id', $id);
			$query = $this->CI->db->get($table);
			$query = $query->result_object();
			if ($query) {
				$data = $query[0];
			} else {
				$data = null;
			}
		} else {
			$data = null;
		}

		return $data;
	}
	
	
	function getMasterRefFile($table, $tableid, $id, $type = 1)
	{
		$datanya = array();
		if ($table != '' && $id != '') {
			
			$exclude_new = array('created','modified','createdid','modifiedid','active','slug','color');
			$select = array();
			$selectfield = array();
			$query_column = $this->query_column($table, $exclude_new);
			if($query_column){
				foreach($query_column as $rowsdata){
					array_push($select,$table.'.'."`".$rowsdata['name']."`");
					array_push($selectfield,$rowsdata['name']);
				}
			}
			
			$selectnya = implode(",", $select);
			if($selectnya != ''){
				$selectnya = ','.$selectnya;
			}
			$selectdata = $selectnya;

			if($table == 'users_data'){
				$this->CI->db->select('users_data.id, users_data.fullname, users_data.username, users_data.email');
			}else{
				$this->CI->db->select($selectdata);
			}
			
			$this->CI->db->where($table.'.'.$tableid, $id);
			$query = $this->CI->db->get($table);
			$query = $query->result_array();
			if ($query) {
				$x=0;
				foreach($query as $rows){
					foreach($selectfield as $rowstable){
						$datanya[$x][$rowstable] = $rows[$rowstable];
						if($rowstable == 'file_id'){
							$datanya[$x]['file'] = $this->getMaster('data_gallery', $rows['file_id']);
						}
					}
					$x++;
				}
				
				if($type == 2){
					$data = $datanya;
				}else{
					$data = $datanya[0];
				}
				
			} else {
				$data = null;
			}
		} else {
			$data = null;
		}

		return $data;
	}
	
	function getMasterRef($table, $tableid, $id, $type = 1)
	{
		if ($table != '' && $id != '') {
			
			$exclude_new = array('created','modified','createdid','modifiedid','active','slug','color');
			$select = array();
			$query_column = $this->query_column($table, $exclude_new);
			if($query_column){
				foreach($query_column as $rowsdata){
					array_push($select,$table.'.'."`".$rowsdata['name']."`");
				}
			}
			
			$selectnya = implode(",", $select);
			if($selectnya != ''){
				$selectnya = ','.$selectnya;
			}
			$selectdata = $selectnya;

			if($table == 'users_data'){
				$this->CI->db->select('users_data.id, users_data.fullname, users_data.username, users_data.email');
			}else{
				$this->CI->db->select($selectdata);
			}
			
			$this->CI->db->where($table.'.'.$tableid, $id);
			$query = $this->CI->db->get($table);
			$query = $query->result_object();
			if ($query) {
				if($type == 2){
					$data = $query;
				}else{
					$data = $query[0];
				}
				
			} else {
				$data = null;
			}
		} else {
			$data = null;
		}

		return $data;
	}
	
	function getMasterRefArray($table, $tableid, $id, $type = 1)
	{
		if ($table != '' && $id != '') {
			
			$exclude_new = array('created','modified','createdid','modifiedid','active','slug','color');
			$select = array();
			$query_column = $this->query_column($table, $exclude_new);
			if($query_column){
				foreach($query_column as $rowsdata){
					array_push($select,$table.'.'."`".$rowsdata['name']."`");
				}
			}
			
			$selectnya = implode(",", $select);
			if($selectnya != ''){
				$selectnya = ','.$selectnya;
			}
			$selectdata = $selectnya;

			if($table == 'users_data'){
				$this->CI->db->select('users_data.id, users_data.fullname, users_data.username, users_data.email');
			}else{
				$this->CI->db->select($selectdata);
			}
			
			$this->CI->db->where($table.'.'.$tableid, $id);
			$query = $this->CI->db->get($table);
			$query = $query->result_object();
			if ($query) {
				if($type == 2){
					$data = $query;
				}else{
					$data = $query[0];
				}
				
			} else {
				$data = array();
			}
		} else {
			$data = array();
		}

		return $data;
	}
	
	
	public function generateInvoice($invoice_id){
			$totalinvoice = 0;
			//$invoice_no = null;

			$array_spk = [];
			$this->CI->db->select('data_pengajuan_invoice_spk.spk_id');
			$this->CI->db->where('data_pengajuan_invoice_spk.invoice_id',$invoice_id);
			$this->CI->db->where('data_pengajuan_invoice_spk.active', 1);
			$query = $this->CI->db->get('data_pengajuan_invoice_spk');
			$query = $query->result_object();
			if($query){
				foreach($query as $rowsspk){
					array_push($array_spk, $rowsspk->spk_id);
				}
			}
			
			if(count($array_spk) == 0){
				return false;
			}
			
			$this->CI->db->select('sum(data_pengajuan_tarif.tarif_total) as total');
			$this->CI->db->where_in('data_pengajuan_alat.spk_id',$array_spk);
			$this->CI->db->where('data_pengajuan_tarif.active', 1);
			$this->CI->db->where('data_pengajuan_alat.status_id', 9);
			$this->CI->db->join('data_pengajuan_tarif','data_pengajuan_tarif.pengajuan_alat_id = data_pengajuan_alat.id');
			$query = $this->CI->db->get('data_pengajuan_alat');
			$query = $query->result_object();
			if($query){
				$totalinvoice = $query[0]->total;
			}
			
			
			$this->CI->db->where('data_pengajuan_invoice.id', $invoice_id);
			$querydata = $this->CI->db->get('data_pengajuan_invoice');
			$querydata = $querydata->result_object();
			if($querydata){
				
				$date = date('Y-m-d');
				$date = strtotime($date);
				$date = strtotime("+7 day", $date);
				$date = date('Y-m-d H:i:s', $date);
				
				$datadetail = array(
					//'pengajuan_id' 			=> $pengajuan_id,
					'total' 				=> $totalinvoice,
					'expired' 				=> $date,
					'status_id' 			=> 1,
					'active' 				=> 1,
					'modifiedid'			=> $this->CI->session->userdata('userid'),
					'modified'				=> date('Y-m-d H:i:s')
				);
												
				$this->CI->db->where('data_pengajuan_invoice.id', $querydata[0]->id);
				$update = $this->CI->db->update('data_pengajuan_invoice', $datadetail);
				$insert_id = $querydata[0]->id;
				if($update){
					$this->CI->db->select('data_pengajuan_tarif.tarif_id, 
										data_pengajuan_tarif.tarif_satuan,
										data_pengajuan_tarif.tarif_uom, 
										data_pengajuan_tarif.kode_tarif_simponi, 
										data_pengajuan_tarif.kode_pp_simponi, 
										data_pengajuan_tarif.kode_akun, 
										sum(data_pengajuan_tarif.tarif_qty) as tarif_qty, 
										sum(data_pengajuan_tarif.tarif_total) as tarif_total
					');
					$this->CI->db->where_in('data_pengajuan_alat.spk_id',$array_spk);
					$this->CI->db->where('data_pengajuan_tarif.active', 1);
					$this->CI->db->where('data_pengajuan_alat.status_id', 9);
					$this->CI->db->join('data_pengajuan_tarif','data_pengajuan_tarif.pengajuan_alat_id = data_pengajuan_alat.id');
					$this->CI->db->group_by('data_pengajuan_tarif.tarif_id,
							data_pengajuan_tarif.tarif_satuan,
							data_pengajuan_tarif.tarif_uom, 
							data_pengajuan_tarif.kode_tarif_simponi, 
							data_pengajuan_tarif.kode_pp_simponi, 
							data_pengajuan_tarif.kode_akun
					');
					$querydata = $this->CI->db->get('data_pengajuan_alat');
					$querydata = $querydata->result_object();
					if($querydata){
						foreach($querydata as $rows){
							$this->CI->db->where('data_pengajuan_invoice_detail.invoice_id', $insert_id);
							$this->CI->db->where('data_pengajuan_invoice_detail.tarif_id', $rows->tarif_id);
							$queryinvoice= $this->CI->db->get('data_pengajuan_invoice_detail');
							$queryinvoice = $queryinvoice->result_object();
							if(!$queryinvoice){
								$slugnya = $invoice_id.'-'.$insert_id.'-'.$rows->tarif_id.'-'.date('Y-m-d');

								$datadetail = array(
									'invoice_id' 			=> $insert_id,
									'tarif_id' 				=> $rows->tarif_id,
									//'alat_id' 				=> $rows->id,
									'nominal' 				=> $rows->tarif_satuan,
									'qty' 					=> $rows->tarif_qty,
									'total' 				=> $rows->tarif_total,
									'tarif_uom' 			=> $rows->tarif_uom,
									'kode_tarif_simponi' 	=> $rows->kode_tarif_simponi,
									'kode_pp_simponi' 		=> $rows->kode_pp_simponi,
									'kode_akun' 			=> $rows->kode_akun,
									'keterangan' 			=> 'Created Invoice',
									'status_id' 			=> 1,
									'active' 				=> 1,
									'createdid'				=> $this->CI->session->userdata('userid'),
									'created'				=> date('Y-m-d H:i:s'),
									'modifiedid'			=> $this->CI->session->userdata('userid'),
									'modified'				=> date('Y-m-d H:i:s')
								);
														
								$string = $slugnya;
								$slug = $this->CI->ortyd->sanitize($string,'data_pengajuan_invoice_detail');
								$datadetail = array_merge($datadetail,
									array('slug' 	=> $slug)
								);
																
								$insert = $this->CI->db->insert('data_pengajuan_invoice_detail', $datadetail);	
								//$insert_id = $this->CI->db->insert_id();
					
							}else{
								$datadetail = array(
									'invoice_id' 			=> $insert_id,
									'tarif_id' 				=> $rows->tarif_id,
									//'alat_id' 				=> $rows->id,
									'nominal' 				=> $rows->tarif_satuan,
									'qty' 					=> $rows->tarif_qty,
									'total' 				=> $rows->tarif_total,
									'tarif_uom' 			=> $rows->tarif_uom,
									'kode_tarif_simponi' 	=> $rows->kode_tarif_simponi,
									'kode_pp_simponi' 		=> $rows->kode_pp_simponi,
									'kode_akun' 			=> $rows->kode_akun,
									'keterangan' 			=> 'Update Invoice',
									'active' 				=> 1,
									'modifiedid'			=> $this->CI->session->userdata('userid'),
									'modified'				=> date('Y-m-d H:i:s')
								);
																
								$this->CI->db->where('data_pengajuan_invoice_detail.id', $queryinvoice[0]->id);
								$update = $this->CI->db->update('data_pengajuan_invoice_detail', $datadetail);						
							}
						}
					}
					
					
					$this->CI->db->select('data_pengajuan_alat.id as pengajuan_alat_id, data_pengajuan_alat.tarif');
					$this->CI->db->where_in('data_pengajuan_alat.spk_id',$array_spk);
					$this->CI->db->where('data_pengajuan_alat.status_id', 9);
					$querydata = $this->CI->db->get('data_pengajuan_alat');
					$querydata = $querydata->result_object();
					if($querydata){
						foreach($querydata as $rows){
							$this->CI->db->where('data_pengajuan_invoice_detail_alat.invoice_id', $insert_id);
							$this->CI->db->where('data_pengajuan_invoice_detail_alat.pengajuan_alat_id', $rows->pengajuan_alat_id);
							$queryinvoice= $this->CI->db->get('data_pengajuan_invoice_detail_alat');
							$queryinvoice = $queryinvoice->result_object();
							if(!$queryinvoice){
								$slugnya = $invoice_id.'-'.$insert_id.'-'.$rows->pengajuan_alat_id.'-'.date('Y-m-d');

								$datadetail = array(
									'invoice_id' 			=> $insert_id,
									'pengajuan_alat_id' 	=> $rows->pengajuan_alat_id,
									'total' 				=> $rows->tarif,
									'active' 				=> 1,
									'createdid'				=> $this->CI->session->userdata('userid'),
									'created'				=> date('Y-m-d H:i:s'),
									'modifiedid'			=> $this->CI->session->userdata('userid'),
									'modified'				=> date('Y-m-d H:i:s')
								);
														
								$string = $slugnya;
								$slug = $this->CI->ortyd->sanitize($string,'data_pengajuan_invoice_detail_alat');
								$datadetail = array_merge($datadetail,
									array('slug' 	=> $slug)
								);
																
								$insert = $this->CI->db->insert('data_pengajuan_invoice_detail_alat', $datadetail);	
								//$insert_id = $this->CI->db->insert_id();
					
							}else{
								$datadetail = array(
									'invoice_id' 			=> $insert_id,
									'pengajuan_alat_id' 	=> $rows->pengajuan_alat_id,
									'total' 				=> $rows->tarif,
									'active' 				=> 1,
									'modifiedid'			=> $this->CI->session->userdata('userid'),
									'modified'				=> date('Y-m-d H:i:s')
								);
																
								$this->CI->db->where('data_pengajuan_invoice_detail_alat.id', $queryinvoice[0]->id);
								$update = $this->CI->db->update('data_pengajuan_invoice_detail_alat', $datadetail);						
							}
						}
					}
					
				}
			}

			$this->CI->db->select('data_pengajuan_invoice_detail.*');
			$this->CI->db->where('data_pengajuan_invoice.id',$invoice_id);
			$this->CI->db->where('data_pengajuan_invoice_detail.active', 1);
			$this->CI->db->join('data_pengajuan_invoice','data_pengajuan_invoice.id = data_pengajuan_invoice_detail.invoice_id');
			$query = $this->CI->db->get('data_pengajuan_invoice_detail');
			$query = $query->result_object();
			if($query){
				
				$this->CI->db->select('data_pengajuan_alat.id');
				$this->CI->db->where_in('data_pengajuan_alat.spk_id',$array_spk);
				$this->CI->db->where('data_pengajuan_alat.active', 1);
				$query = $this->CI->db->get('data_pengajuan_alat');
				$query = $query->result_object();
				if($query){
					foreach($query as $rows){
						$this->updateAlatbyInvoice($rows->id);
					}
				}
				
				$this->CI->db->select('data_pengajuan_invoice_spk.spk_id');
				$this->CI->db->where('data_pengajuan_invoice_spk.invoice_id',$invoice_id);
				$this->CI->db->where('data_pengajuan_invoice_spk.active', 1);
				$query = $this->CI->db->get('data_pengajuan_invoice_spk');
				$query = $query->result_object();
				if($query){
					foreach($query as $rows){
						$status_id = 8;
						$catatan = 'SPK Update to Invoice';
						$status_last_id = 7;
						$this->updateStatusSPK($rows->spk_id, $status_id, $catatan, $status_last_id);
					}
				}
				
				$billing = $this->getBilling($insert_id, $this->CI->session->userdata('userid'));
			
				$result = array("csrf_hash" => $this->CI->security->get_csrf_hash(),"status" => "success", "data" => $query, "billing" => $billing);
				return $result;
			}else{
				$result = array("csrf_hash" => $this->CI->security->get_csrf_hash(),"status" => "error");
				return $result;
			}
				
		}
		
		function updateStatusSPK($spk_id, $status_id, $catatan, $status_last_id){
			
			$dataid = array(
				'status_id' 		=> $status_id,
				'status_last_id' 	=> $status_last_id,
				'modifiedid'		=> $this->CI->session->userdata('userid'),
				'modified'			=> date('Y-m-d H:i:s')
			);
											
			$this->CI->db->where('id', $spk_id);
			$update = $this->CI->db->update('data_pengajuan_spk', $dataid);
					
			$data_array = array(
				"status_id" => $status_id,
				"catatan" => $catatan,
				"status_last_id" => $status_last_id
			);
						
			$history_id = $this->saveHistorySPK($spk_id, $data_array);
			return $history_id;
		}
		
		
		function saveHistorySPK($spk_id, $data_array){
			
			$details = $this->CI->db->where("id", $spk_id)->get('data_pengajuan_spk');
			
			foreach ($details->result_array() as $row) {
				unset($row['id']);
				
				$row = array_merge($row,
					array('spk_id' 	=> $spk_id)
				);
						
				$insertdata = $this->CI->db->insert('data_pengajuan_spk_history_data',$row);
				$last_id = $this->CI->db->insert_id();
				
				if($insertdata){
					$dataid = array(
						'spk_id' 	=> $spk_id ?? 0,
						'tanggal'		=> date('Y-m-d H:i:s'),
						'status_id'		=> $row['status_id'] ?? 0,
						'keterangan'	=> $data_array['catatan'] ?? 'Tidak Ada Catatan',
						'history_id'	=> $last_id,
						'active' 		=> 1,
						'createdid'		=> $this->CI->session->userdata('userid'),
						'created'		=> date('Y-m-d H:i:s'),
						'modifiedid'	=> $this->CI->session->userdata('userid'),
						'modified'		=> date('Y-m-d H:i:s')
					);
					
					//$this->CI->db->where('id', $spk_id);
					$insert = $this->CI->db->insert('data_pengajuan_spk_history_status', $dataid);
					$insert_id = $this->CI->db->insert_id();
					if($insert){
						return $insert_id;
					}
					
					
				}

			}
			
			return false;
		}
		
		
		function updateAlatbyInvoice($id){
			$this->CI->db->where('data_pengajuan_alat.id',$id);
			$this->CI->db->where('data_pengajuan_alat.active',1);
			$this->CI->db->where('data_pengajuan_alat.status_id',7);
			$query = $this->CI->db->get('data_pengajuan_alat');
			$query = $query->result_object();
			if($query){
				foreach($query as $rows){
					$dataremove = array(
						'status_id' 	=> 8,
						'modifiedid'	=> $this->CI->session->userdata('userid'),
						'modified'		=> date('Y-m-d H:i:s')
					);
											
					$this->db->where('data_pengajuan_alat.id', $rows->id);
					$updateactive = $this->CI->db->update('data_pengajuan_alat', $dataremove);
					if($updateactive){
						$data_array = array(
							"status_id" => 8,
							"catatan" => 'Update Alat to Invoice',
							"status_last_id" => 7
						);
						$this->saveHistoryPengajuanAlat($rows->id, $data_array);
					}
				}
			}
		}
		
		function saveHistoryPengajuanAlat($pengajuan_alat_id, $data_array){
			
			$details = $this->CI->db->where("id", $pengajuan_alat_id)->get('data_pengajuan_alat');
			
			foreach ($details->result_array() as $row) {
				unset($row['id']);
				
				$row = array_merge($row,
					array('pengajuan_alat_id' 	=> $pengajuan_alat_id)
				);
						
				$insertdata = $this->CI->db->insert('data_pengajuan_alat_history_data',$row);
				$last_id = $this->CI->db->insert_id();
				
				if($insertdata){
					$dataid = array(
						'pengajuan_alat_id' 	=> $pengajuan_alat_id ?? 0,
						'tanggal'		=> date('Y-m-d H:i:s'),
						'status_id'		=> $row['status_id'] ?? 0,
						'keterangan'	=> $data_array['catatan'] ?? 'Tidak Ada Catatan',
						'history_id'	=> $last_id,
						'active' 		=> 1,
						'createdid'		=> $this->CI->session->userdata('userid'),
						'created'		=> date('Y-m-d H:i:s'),
						'modifiedid'	=> $this->CI->session->userdata('userid'),
						'modified'		=> date('Y-m-d H:i:s')
					);
					
					//$this->CI->db->where('id', $spk_id);
					$insert = $this->CI->db->insert('data_pengajuan_alat_history_status', $dataid);
					$insert_id = $this->CI->db->insert_id();
					if($insert){
						return $insert_id;
					}
					
					
				}

			}
			
			return false;
		}
	
	
				
	function generateApproval($approval_id, $data_id, $data_id_nya = null){
		$x=0;
		$user_id = $this->CI->session->userdata('userid');
		
		$this->CI->db->select('data_approval.*');
		$this->CI->db->where('data_approval.id',$approval_id);
		$this->CI->db->where('data_approval.active',1);
		$this->CI->db->order_by("data_approval.created", "DESC");
		$this->CI->db->limit(1);
		$query = $this->CI->db->get('data_approval');
		$query = $query->result_object();
		if($query){
			foreach($query as $rows){
				$this->CI->db->select('data_approval_role.*');
				$this->CI->db->where('data_approval_role.approval_id',$rows->id);
				$this->CI->db->where('data_approval_role.active',1);
				$queryrole = $this->CI->db->get('data_approval_role');
				$queryrole = $queryrole->result_object();
				if($queryrole){
					foreach($queryrole as $rowsrole){
						
						$this->CI->db->select('data_approval_data.*');
						$this->CI->db->where('data_approval_data.approval_role_id',$rowsrole->id);
						$this->CI->db->where('data_approval_data.data_id',$data_id);
						$this->CI->db->where('data_approval_data.active',1);
						$querydata = $this->CI->db->get('data_approval_data');
						$querydata = $querydata->result_object();
						if(!$querydata){
							$datadetail = array(
								'approval_id'		=> $rows->id,
								'approval_role_id'	=> $rowsrole->id,
								'level'				=> $rowsrole->level,
								'data_id'			=> $data_id,
								'approval_group_id'	=> $rowsrole->role_id,
								'approval_date'		=> null,
								'approval_user_id'	=> null,
								'createdid'			=> $user_id,
								'created'			=> date('Y-m-d H:i:s'),
								'modifiedid'		=> $user_id,
								'modified'			=> date('Y-m-d H:i:s'),
								'active'			=> 1,
							);
							
							$slugstring = date('YmdHis').'-'.$rows->id.'-'.$rowsrole->id.'-'.$data_id;
							$slug = $this->sanitize($slugstring,'data_approval_data');
							$datadetail = array_merge($datadetail,
								array('slug' 	=> $slug)
							);
							
							$insert = $this->CI->db->insert('data_approval_data', $datadetail);
							$insertid = $this->CI->db->insert_id();
							if($insert){
								if($data_id_nya != null && $x==0){
									$data_array = array(
										"tipe_id" => 111,
										"data_id" => $data_id_nya,
										"user_id" => $rowsrole->role_id
									);
									
									$this->CI->m_api_notif->setInboxData($data_array);
								}
							}
							$x++;
						}else{
							$datadetail = array(
								'approval_id'		=> $rows->id,
								'approval_role_id'	=> $rowsrole->id,
								'level'				=> $rowsrole->level,
								'approval_group_id'	=> $rowsrole->role_id,
								'data_id'			=> $data_id,
								'approval_date'		=> null,
								'approval_user_id'	=> null,
								'modifiedid'		=> $user_id,
								'modified'			=> date('Y-m-d H:i:s'),
								'active'			=> 1,
							);

							$this->CI->db->where('data_approval_data.id',$querydata[0]->id);
							$insert = $this->CI->db->update('data_approval_data', $datadetail);
							$insertid = $querydata[0]->id;
						}
					}
				}
			}
		}
	}
	
	function getApproval($approval_id, $data_id, $insertid = null, $data_id_nya = null){
		$this->CI->db->select('data_approval_data.*, data_approval_role.name as role_name');
		$this->CI->db->where('data_approval_data.approval_id',$approval_id);
		$this->CI->db->where('data_approval_data.data_id',$data_id);
		$this->CI->db->where('data_approval_data.approval_date is null',null);
		$this->CI->db->where('data_approval_data.active',1);
		$this->CI->db->join('data_approval_role','data_approval_data.approval_role_id = data_approval_role.id');
		$this->CI->db->order_by("data_approval_data.level", "ASC");
		$this->CI->db->limit(1);
		$query = $this->CI->db->get('data_approval_data');
		$query = $query->result_object();
		if($query){
			foreach($query as $rows){
				
				if($data_id_nya != null){
					$data_array = array(
						"tipe_id" => 111,
						"data_id" => $data_id_nya,
						"user_id" => $rows->approval_group_id
					);
					
					$this->CI->m_api_notif->setInboxData($data_array);
				}
				
				
				return array(
					'id' 		=> $rows->id,
					'name' 		=> 'Verifikasi '.$this->select2_getname($rows->approval_group_id,'users_groups','id','name'),
					'level' 	=> $rows->level,
					'role_name' => $rows->role_name,
					'group_id' 	=> $rows->approval_group_id,
					'last_id' 	=> $insertid
				);
			}
		}
		
		return false;
	}
	
	function saveApproval($approval_id, $data_id, $data_id_nya = null){
		
		$user_id = $this->CI->session->userdata('userid');
		
		$approvaldata = $this->getApproval($approval_id, $data_id);
		if($approvaldata != false){
			$approval_data_id = $approvaldata['id'];
			
			$datadetail = array(
				'approval_date'		=> date('Y-m-d H:i:s'),
				'approval_user_id'	=> $user_id,
				'modifiedid'		=> $user_id,
				'modified'			=> date('Y-m-d H:i:s'),
				'active'			=> 1,
			);

			$this->CI->db->where('data_approval_data.id',$approval_data_id);
			$insert = $this->CI->db->update('data_approval_data', $datadetail);
			$insertid = $approval_data_id;
			if($insert){
				$approvaldata = $this->getApproval($approval_id, $data_id, $insertid, $data_id_nya);
				return $approvaldata;
				
			}
		}
		
		return false;
	}
	
	public function send_telegram($chat_id_data, $message_data, $parse_mode = 'HTML') {
         
        //https://api.telegram.org/bot8203623395:AAGGedsLTgV6jrg70LpByxT7aQr4PaHfAj0/getUpdates
         
        $telegram_config = $this->CI->config->item('telegram');
        $chat_id = $chat_id_data ?? '338325624';
        
        $message = $message_data ?? 'Halo! Ini pesan dari SIMPKTNPMSE_BOT';
		
		$options = array(
            'parse_mode' => $parse_mode
        );
        
        $result = $this->CI->telegram->send_message($chat_id, $message, $options);
        
        if ($result['ok']) {
            return array("status"=> "success","message"=>"Pesan berhasil dikirim!");
        } else {
             return array("status"=> "error", "message"=> "Gagal mengirim pesan: " . $result['description']);
        }
    }

    function tanggal_indo($tanggal, $cetak_hari = false)
		{
			$hari = array ( 1 =>    'Senin',
						'Selasa',
						'Rabu',
						'Kamis',
						'Jumat',
						'Sabtu',
						'Minggu'
					);
					
			$bulan = array (1 =>   'Januari',
						'Februari',
						'Maret',
						'April',
						'Mei',
						'Juni',
						'Juli',
						'Agustus',
						'September',
						'Oktober',
						'November',
						'Desember'
					);
			$split 	  = explode('-', $tanggal);
			$tgl_indo = $split[2] . ' ' . $bulan[ (int)$split[1] ] . ' ' . $split[0];
			
			if ($cetak_hari) {
				$num = date('N', strtotime($tanggal));
				return $hari[$num] . ', ' . $tgl_indo;
			}
			return $tgl_indo;
		} 
	
	
		
}
