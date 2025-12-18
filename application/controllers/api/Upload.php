<?php

require(APPPATH . '/libraries/REST_Controller.php');
require(APPPATH . '/libraries/simple_html_dom.php');


class Upload extends REST_Controller
{

	private $modeldb = 'm_api';
    function __construct()
    {
        parent::__construct();
		$this->load->model($this->modeldb);
		$this->load->model('m_api_history');
		//$this->load->helper(['jwt', 'authorization']); 
		header("Access-Control-Allow-Origin: *");
		header('Access-Control-Allow-Credentials: false');
		header('Access-Control-Allow-Headers: Origin ,Content-Type,authorization');
		header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
		header('Access-Control-Max-Age: 86400'); 
		$method = $_SERVER['REQUEST_METHOD'];
        if ($method == "OPTIONS") {
            die();
        }
    }

	
	function uploadBase64_new_post()
    {
		$file = $this->input->post('image64', true);
		$user_id = $this->input->post('user_id', true);
		$id = $this->input->post('id', true);
				
		$dir = './file/thumbnail/'.date('Y').'/'.date('m').'/'.date('d');
				
		if(!file_exists($dir)){
			mkdir($dir,0755,true);
		}

		$path = 'file/thumbnail/'.date('Y').'/'.date('m').'/'.date('d');

		if (preg_match('/^data:image\/(\w+);base64,/', $file, $type)) {
			$file = substr($file, strpos($file, ',') + 1);
			$type = strtolower($type[1]); // jpg, png, gif

			if (!in_array($type, [ 'jpg', 'jpeg', 'gif', 'png' ])) {
				$data['status'] = 'error';	
				$data['errors'] = 'Invalid Image Type';
				$this->response($data, 405);
				die();
			}

			$file = base64_decode($file);

			if ($file === false) {
				$data['status'] = 'error';	
				$data['errors'] = 'base64_decode failed';
				$this->response($data, 405);
				die();
			}
		} else {
			$data['status'] = 'error';	
			$data['errors'] = 'did not match data URI with image data';
			$this->response($data, 405);
			die();
		}
				
		$nama = date('YmdHis').$user_id.'.'.$type;
		$status = file_put_contents($path .'/'.$nama,$file);
		if($status){
			$token	=	'0.'.date('YmdHis').$user_id;
			$size	=	(int)(strlen(rtrim($file, '=')) * 3 / 4) / 1000;
					
			$data = array(
				'name'			=> $nama,
				'file_size'		=> $size * 1000,
				'token'			=> $token,
				'path'			=> $path .'/'.$nama,
				'path_server'	=> $dir .'/'.$nama,
				'file_store_format'	=> $type,
				'createdid'		=> $user_id,
				'created'		=> date('Y-m-d H:i:s'),
				'modifiedid'	=> $user_id,
				'modified'		=> date('Y-m-d H:i:s'),
				'url_server'	=> base_url()
			);
						
			$insert = $this->db->insert('data_gallery',$data);
			$insertid = $this->db->insert_id();
					
			if($insert){
						
				$this->db->where('data_gallery.id', $id);
				$querystatus = $this->db->get('data_gallery');	
				$querystatus = $querystatus->result_object();
				if($querystatus){
					$dataremove = array(
						'thumbnail_id' 	=> $insertid,
						'modifiedid'	=> $user_id,
						'modified'		=> date('Y-m-d H:i:s')
					);
														
					$this->db->where('data_gallery.id', $querystatus[0]->id);
					$updateactive = $this->db->update('data_gallery', $dataremove);
							
					$data['status'] = 'success';	
					$data['errors'] = 'Data inserted storage';
						
				}else{
					$data['status'] = 'error';	
					$data['errors'] = 'Data not insert review';
				}		
			}else{
				$data['status'] = 'error';	
				$data['errors'] = 'Data not insert storage';
			}
		}else{
			$data['status'] = 'error';	
			$data['errors'] = 'Upload Errors';
		}	
		$this->response($data, 200);
    }
	
	public function proses_upload_post()
	{
		// Autentikasi user wajib
		if (!$this->session->userdata('logged_in')) {
			show_error('Unauthorized access', 403);
			return;
		}
		
		// Ambil input dengan sanitasi
		$pathdir = $this->security->xss_clean($this->input->post('pathdir', true) ?? '');
		$tipedir = $this->security->xss_clean($this->input->post('tipedir', true) ?? '');


		if($pathdir != '' && $pathdir != null){
			// Validasi format pathdir
			if (empty($pathdir) || preg_match('/\.\.|[^a-zA-Z0-9_\-\/]/', $pathdir)) {
				show_error('Invalid path format', 400);
				return;
			}

			// Gabungkan dengan FCPATH dan validasi eksistensi path
			$base_path = realpath(FCPATH);
			$target_path = realpath(FCPATH.$pathdir);

			if (!$target_path || strpos($target_path, $base_path) !== 0 || !is_dir($target_path)) {
				show_error('Directory does not exist or invalid access', 404);
				return;
			}

			// Jalankan proses upload
			$result = $this->ortyd->proses_upload_dok($pathdir, $tipedir);
			echo $result;
		}else{
			$result = $this->ortyd->proses_upload_dok();
			echo $result;
		}
	}
	
	public function proses_upload_datafront_x24440_post()
	{
		// Autentikasi user wajib
		//if (!$this->session->userdata('logged_in')) {
			//show_error('Unauthorized access', 403);
			//return;
		//}
		
		// Ambil input dengan sanitasi
		$pathdir = $this->security->xss_clean($this->input->post('pathdir', true) ?? '');
		$tipedir = $this->security->xss_clean($this->input->post('tipedir', true) ?? '');


		if($pathdir != '' && $pathdir != null){
			// Validasi format pathdir
			if (empty($pathdir) || preg_match('/\.\.|[^a-zA-Z0-9_\-\/]/', $pathdir)) {
				show_error('Invalid path format', 400);
				return;
			}

			// Gabungkan dengan FCPATH dan validasi eksistensi path
			$base_path = realpath(FCPATH);
			$target_path = realpath(FCPATH.$pathdir);

			if (!$target_path || strpos($target_path, $base_path) !== 0 || !is_dir($target_path)) {
				show_error('Directory does not exist or invalid access', 404);
				return;
			}

			// Jalankan proses upload
			$result = $this->ortyd->proses_upload_dok($pathdir, $tipedir);
			echo $result;
		}else{
			$result = $this->ortyd->proses_upload_dok();
			echo $result;
		}
	}


	
	public function remove_file_post()
	{
		// Autentikasi wajib (ganti dengan sistem loginmu)
		if (!$this->session->userdata('logged_in')) {
			$this->output
				->set_status_header(401)
				->set_output(json_encode([
					'message' => 'error',
					'errors' => 'Unauthorized request'
				]));
			return;
		}

		// Validasi input POST
		$this->load->library('form_validation');
		$this->form_validation->set_rules('user_id', 'User ID', 'required|integer');
		$this->form_validation->set_rules('token', 'Token', 'required|alpha_numeric|min_length[8]|max_length[64]');

		if ($this->form_validation->run() == FALSE) {
			$this->output
				->set_status_header(400)
				->set_output(json_encode([
					'message' => 'error',
					'errors' => validation_errors()
				]));
			return;
		}

		$user_id = (int) $this->security->xss_clean($this->input->post('user_id'));
		$token   = $this->security->xss_clean($this->input->post('token'));

		// Validasi bahwa user_id adalah milik session aktif
		if ((int)$this->session->userdata('user_id') !== $user_id) {
			$this->output
				->set_status_header(403)
				->set_output(json_encode([
					'message' => 'error',
					'errors' => 'Invalid session or user mismatch'
				]));
			return;
		}

		// Cari data file berdasarkan token
		$file = $this->db->get_where('data_gallery', ['token' => $token]);

		if ($file->num_rows() > 0) {
			$hasil = $file->row();

			// Pastikan user pemilik file
			if ((int)$hasil->user_id !== $user_id) {
				$this->output
					->set_status_header(403)
					->set_output(json_encode([
						'message' => 'error',
						'errors' => 'You are not authorized to delete this file.'
					]));
				return;
			}

			// Soft delete file
			$dataremove = [
				'active'     => 0,
				'modifiedid' => $user_id,
				'modified'   => date('Y-m-d H:i:s')
			];

			$this->db->where('token', $token);
			$updateactive = $this->db->update('data_gallery', $dataremove);

			if ($updateactive) {
				// (Optional) Logging
				log_message('info', "User $user_id removed file with token $token");

				$this->output
					->set_content_type('application/json')
					->set_output(json_encode(['message' => 'success']));
			} else {
				$this->output
					->set_status_header(500)
					->set_output(json_encode([
						'message' => 'error',
						'errors' => 'Database update failed'
					]));
			}
		} else {
			$this->output
				->set_status_header(404)
				->set_output(json_encode([
					'message' => 'error',
					'errors' => 'File not found'
				]));
		}
	}


	

}