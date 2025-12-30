<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class M_data extends CI_Model {
		
		
		public function __construct()
		{
			parent::__construct();
			
		}
		
		 public function get_by_laporan_no($laporan_no) {
			return $this->db
				->where('laporan_no', $laporan_no)
				->get('vw_data_laporan')
				->row_array(); // pakai row_array agar bisa langsung dipakai
		}
		
		public function get_data_byid($ID, $tabledb, $tableid){
			$this->db->select('*');
			$this->db->where($tableid, $ID);
			$query = $this->db->get($tabledb);
			$query = $query->result_object();
			if($query){
				return $query;
			}else{
				return null;
			}
		}
		
		public function get_sertifikat($id){
			$sql = "
				SELECT
					b.certificate_no,
					a.alat_nama,
					a.alat_jumlah_halaman,
					d.permohonan_alamat,
					d.permohonan_pic,
					e.nama as nama_ketertelusuran,
					f.fullname,
					f.position_name,
					f.created
				FROM
					data_alat a 
				JOIN data_certificate b on a.certificate_id = b.id
				JOIN data_pengajuan_alat c on a.id = c.alat_id
				JOIN data_pengajuan d on c.pengajuan_id = d.id
				JOIN master_pengajuan_keterterusuran e on a.keterterusuran_id = e.id
				JOIN users_data f on b.createdid = f.id
				WHERE a.id = ".$id."
			";
			return $this->db->query($sql)->row_array();
		}
		
		function data_all($tabledb, $q, $number, $offset) {
			$this->db->select('vw_data_search.*');
			
			if ($q != '') {
				$this->db->group_start(); // Buka grup untuk LIKE
				$this->db->or_like('vw_data_search.name', $q);
				$this->db->or_like('vw_data_search.description', $q);
				$this->db->or_like('vw_data_search.type_name', $q);
				$this->db->group_end(); // Tutup grup LIKE
			} else {
				$this->db->where('vw_data_search.id', 0);
			}

			// Tambahkan filter user_id
			$userid = $this->session->userdata('userid');
			$this->db->group_start();
			$this->db->where('vw_data_search.user_id', $userid);
			$this->db->or_where('vw_data_search.user_id IS NULL', null, false);
			$this->db->group_end();

			// $this->db->order_by('vw_data_search.modified','DESC');
			return $this->db->get($tabledb, $number, $offset)->result_array();
		}

		function jumlah_data_all($tabledb, $q) {
			$this->db->select('vw_data_search.*');
			
			if ($q != '') {
				$this->db->group_start(); // Buka grup untuk LIKE
				$this->db->or_like('vw_data_search.name', $q);
				$this->db->or_like('vw_data_search.description', $q);
				$this->db->or_like('vw_data_search.type_name', $q);
				$this->db->group_end(); // Tutup grup LIKE
			} else {
				$this->db->where('vw_data_search.id', 0);
			}

			// Tambahkan filter user_id
			$userid = $this->session->userdata('userid');
			$this->db->group_start();
			$this->db->where('vw_data_search.user_id', $userid);
			$this->db->or_where('vw_data_search.user_id IS NULL', null, false);
			$this->db->group_end();

			// $this->db->order_by('vw_data_search.modified','DESC');
			return $this->db->get($tabledb)->num_rows();
		}

		
		function jumlah_data_table($tabledb){
			$this->db->select($tabledb.'.*');
			$this->db->where($tabledb.'.active',1);
			$this->db->order_by($tabledb.'.modified','DESC');
			return $this->db->get($tabledb)->num_rows();
		}
		
		function data_table_byslug($tabledb,$slug,$jointipe,$jointipeid,$cover){
			$this->db->select($tabledb.'.*,'.$jointipe.'.name as type_name, data_gallery.path');
			$this->db->where($tabledb.'.active',1);
			$this->db->join($jointipe,$jointipe.'.id = '.$tabledb.'.'.$jointipeid,'left');
			$this->db->join('data_gallery','data_gallery.id = '.$tabledb.'.'.$cover,'left');
			$this->db->order_by($tabledb.'.modified','DESC');
			return $query = $this->db->get($tabledb)->result_array();		
		}
		
	
		function data_table($tabledb,$number,$offset,$jointipe,$jointipeid,$cover){
			$this->db->select($tabledb.'.*,'.$jointipe.'.name as type_name, data_gallery.path');
			$this->db->where($tabledb.'.active',1);
			$this->db->join($jointipe,$jointipe.'.id = '.$tabledb.'.'.$jointipeid,'left');
			$this->db->join('data_gallery','data_gallery.id = '.$tabledb.'.'.$cover,'left');
			//$this->db->order_by($tabledb.'.modified','DESC');
			$this->db->order_by('rand()');
			return $query = $this->db->get($tabledb,$number,$offset)->result_array();		
		}
		
		function data_table_depan($tabledb,$number,$offset,$jointipe,$jointipeid,$cover){
			$this->db->select($tabledb.'.*,'.$jointipe.'.name as type_name, data_gallery.path');
			$this->db->where($tabledb.'.active',1);
			$this->db->join($jointipe,$jointipe.'.id = '.$tabledb.'.'.$jointipeid,'left');
			$this->db->join('data_gallery','data_gallery.id = '.$tabledb.'.'.$cover,'left');
			$this->db->limit(4);
			//$this->db->order_by($tabledb.'.modified','DESC');
			$this->db->order_by('rand()');
			return $query = $this->db->get($tabledb)->result_array();		
		}


		public function gettarif(){
			$this->db->where('active', 1);
			$this->db->order_by('id','desc');
			return $this->db->get('vw_tarif');
		}
		
		
		
		function create_captcha(){
			$this->load->helper('captcha');
			
			$vals = array(
				//'word'          => 'Random word',
				'img_path'      => './uploads/captcha/',
				'img_url'       => base_url().'uploads/captcha/',
				//'font_path'     => realpath(FCPATH.'system/fonts/texb.ttf'),
				'img_width'     => '150',
				'img_height'    => 30,
				'expiration'    => 7200,
				'word_length'   => 8,
				'font_size'     => 16,
				'img_id'        => 'Imageid',
				'pool'          => '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',

				// White background and border, black text and red grid
				'colors'        => array(
						'background' => array(255, 255, 255),
						'border' => array(255, 255, 255),
						'text' => array(0, 0, 0),
						'grid' => array(255, 40, 40)
				)
			);

			$cap = create_captcha($vals);
			// $this->session->set_userdata('capcha', $cap['word']);
			 
			//echo var_dump($cap);
			// return $cap['image'];
			return 0;
		}
		
		
		public function setSSO($email_sso){
				$username = base64_decode($email_sso);
				$usernamewhere = "'".$username."'";
				$this->db->select('id,username,email,password,gid,banned, last_login ,fullname,validate');
				$this->db->where('lower(username) = '.strtolower($usernamewhere).' and active = 1',null);
				$this->db->or_where('lower(email) = '.strtolower($usernamewhere).' and active = 1',null);
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
										
									return true;
									
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
											'group_id'     	=> $rows->gid,
											'last_login'	=> date('Y-m-d H:i:s'),
											'logged_in'		=> TRUE
										);

										$this->session->set_userdata($login);
										
																		
										if($rows->last_login == ''){
											return true;
										}else{
											return true;
										}

											
									}
								
									return true;
									
								}
									
							}
						}
					}
				}else{
					$data = array(
							'username' 			=> $username,
							'fullname' 			=> $username,
							'email' 			=> $username.'@simpktn.kemendag.go.id',
							'notelp' 			=> '-',
							'password' 			=> $this->ortyd->hash($username.'2024'),
							'gid' 				=> 3,
							'active' 			=> 1,
							'user_id_ref' 		=> 1,
							'validate' 			=> 1,
							'banned' 			=> 0,
							'createdid'			=> 1,
							'created'			=> date('Y-m-d H:i:s'),
							'modifiedid'		=> 1,
							'modified'			=> date('Y-m-d H:i:s')
					);
					
					$insert = $this->db->insert('users_data', $data);
					$insert_id = $this->db->insert_id();
					
					if($insert){
						
						$login = array(
							'userid'  		=> $insert_id,
							'email'     	=> $username.'@simpktn.kemendag.go.id',
							'username'     	=> $username,
							'fullname'     	=> $username,
							'group_id'     	=> 3,
							'last_login'	=> date('Y-m-d H:i:s'),
							'logged_in'		=> TRUE
						);

						$this->session->set_userdata($login);
						return true;
					}

				}
				
			return false;
		}
		
		function is_base64($s){
			// Check if there are valid base64 characters
			if (!preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $s)) return false;

			// Decode the string in strict mode and check the results
			$decoded = base64_decode($s, true);
			if(false === $decoded) return false;

			// Encode the string again
			if(base64_encode($decoded) != $s) return false;

			return true;
		}
		
		
/**
 * POK Detail Model Methods
 * Add these methods to M_data.php model
 */

/**
 * Get POK detail list with location names
 */
public function get_pok_detail_list($survei_pm_detail_id, $master_kelompok_id) {
    $this->db->select('
        data_survei_pm_detail_list.*,
        wil_provinsi.wil_nama as provinsi_name,
        wil_kabkota.wil_nama as kabkota_name,
        wil_kecamatan.wil_nama as kecamatan_name,
        wil_kelurahan.wil_nama as kelurahan_name
    ');
    $this->db->where('data_survei_pm_detail_list.survei_pm_detail_id', $survei_pm_detail_id);
    $this->db->where('data_survei_pm_detail_list.master_kelompok_id', $master_kelompok_id);
    $this->db->where('data_survei_pm_detail_list.active', 1);
    
    // Join wilayah
    $this->db->join('data_wilayah as wil_provinsi', 
                    'wil_provinsi.id = data_survei_pm_detail_list.provinsi_id', 'left');
    $this->db->join('data_wilayah as wil_kabkota', 
                    'wil_kabkota.id = data_survei_pm_detail_list.kabkota_id', 'left');
    $this->db->join('data_wilayah as wil_kecamatan', 
                    'wil_kecamatan.id = data_survei_pm_detail_list.kecamatan_id', 'left');
    $this->db->join('data_wilayah as wil_kelurahan', 
                    'wil_kelurahan.id = data_survei_pm_detail_list.kelurahandes_id', 'left');
    
    $this->db->order_by('data_survei_pm_detail_list.created', 'DESC');
    
    $query = $this->db->get('data_survei_pm_detail_list');
    return $query->result_array();
}

/**
 * Get total penerima for specific POK
 */
public function get_pok_total($survei_pm_detail_id, $master_kelompok_id) {
    $this->db->select_sum('jumlah_total');
    $this->db->where('survei_pm_detail_id', $survei_pm_detail_id);
    $this->db->where('master_kelompok_id', $master_kelompok_id);
    $this->db->where('active', 1);
    
    $result = $this->db->get('data_survei_pm_detail_list')->row();
    return $result->jumlah_total ?? 0;
}

/**
 * Get single POK detail by ID
 */
public function get_pok_detail_by_id($id) {
    $this->db->where('id', $id);
    $this->db->where('active', 1);
    
    $query = $this->db->get('data_survei_pm_detail_list');
    return $query->row_array();
}

/**
 * Save POK detail (insert or update)
 */
public function save_pok_detail($data, $id = null) {
    if ($id && $id != '0') {
        // Update
        $this->db->where('id', $id);
        return $this->db->update('data_survei_pm_detail_list', $data);
    } else {
        // Insert
        return $this->db->insert('data_survei_pm_detail_list', $data);
    }
}

/**
 * Delete POK detail (soft delete)
 */
public function delete_pok_detail($id, $userid) {
    $data = [
        'active' => 0,
        'modifiedid' => $userid,
        'modified' => date('Y-m-d H:i:s')
    ];
    
    $this->db->where('id', $id);
    return $this->db->update('data_survei_pm_detail_list', $data);
}

/**
 * Update POK total in master detail table
 */
public function update_pok_total_in_master($survei_pm_detail_id, $pok_field, $total) {
    $this->db->where('id', $survei_pm_detail_id);
    return $this->db->update('data_survei_pm_detail', [
        $pok_field => $total,
        'modified' => date('Y-m-d H:i:s')
    ]);
}

/**
 * Get master kelompok by urutan (POK number)
 */
public function get_master_kelompok_by_urutan($urutan) {
    $this->db->where('urutan', $urutan);
    $this->db->where('active', 1);
    
    $query = $this->db->get('master_kelompok');
    return $query->row_array();
}

/**
 * Get master kelompok by ID
 */
public function get_master_kelompok_by_id($id) {
    $this->db->where('id', $id);
    $this->db->where('active', 1);
    
    $query = $this->db->get('master_kelompok');
    return $query->row_array();
}

/**
 * Get all active master kelompok
 */
public function get_all_master_kelompok() {
    $this->db->where('active', 1);
    $this->db->order_by('urutan', 'ASC');
    
    $query = $this->db->get('master_kelompok');
    return $query->result_array();
}

/**
 * Get wilayah name by ID
 */
public function get_wilayah_name($id) {
    if (!$id) return null;
    
    $this->db->select('wil_nama');
    $this->db->where('id', $id);
    $result = $this->db->get('data_wilayah')->row();
    
    return $result ? $result->wil_nama : null;
}

/**
 * Get survei detail ID by survei PM ID
 */
public function get_survei_detail_id($survei_pm_id) {
    $this->db->select('id');
    $this->db->where('survei_pm_pm_id', $survei_pm_id);
    $this->db->where('active', 1);
    
    $result = $this->db->get('data_survei_pm_detail')->row();
    return $result ? $result->id : null;
}

/**
 * Get POK detail statistics
 */
public function get_pok_detail_stats($survei_pm_detail_id) {
    $this->db->select('
        master_kelompok.id,
        master_kelompok.kode_kelompok,
        master_kelompok.nama_kelompok,
        master_kelompok.urutan,
        COUNT(data_survei_pm_detail_list.id) as total_units,
        SUM(data_survei_pm_detail_list.jumlah_total) as total_penerima
    ');
    $this->db->from('master_kelompok');
    $this->db->join('data_survei_pm_detail_list', 
                    'data_survei_pm_detail_list.master_kelompok_id = master_kelompok.id ' .
                    'AND data_survei_pm_detail_list.survei_pm_detail_id = ' . (int)$survei_pm_detail_id . ' ' .
                    'AND data_survei_pm_detail_list.active = 1', 
                    'left');
    $this->db->where('master_kelompok.active', 1);
    $this->db->group_by('master_kelompok.id, master_kelompok.kode_kelompok, master_kelompok.nama_kelompok, master_kelompok.urutan');
    $this->db->order_by('master_kelompok.urutan', 'ASC');
    
    $query = $this->db->get();
    return $query->result_array();
}

/**
 * Check if POK has details
 */
public function pok_has_details($survei_pm_detail_id, $master_kelompok_id) {
    $this->db->where('survei_pm_detail_id', $survei_pm_detail_id);
    $this->db->where('master_kelompok_id', $master_kelompok_id);
    $this->db->where('active', 1);
    
    return $this->db->count_all_results('data_survei_pm_detail_list') > 0;
}

}	