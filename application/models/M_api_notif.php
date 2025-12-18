<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


class M_api_notif extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
	}
	

	function setInboxData($data_array){
		
		$tipe_id = $data_array['tipe_id'];
		
		if($tipe_id == 1){ //Pengajuan Baru
			$jenis_user_id = 1;
			$this->getUsers($jenis_user_id, $data_array);
		}elseif($tipe_id == 2){ //Pengajuan
			$jenis_user_id = 2;
			$this->getUsers($jenis_user_id, $data_array);
		}elseif($tipe_id == 3){ //Pengajuan
			$jenis_user_id = 3;
			$this->getUsers($jenis_user_id, $data_array);
		}elseif($tipe_id == 4){ //Pengajuan
			$jenis_user_id = 4;
			$this->getUsers($jenis_user_id, $data_array);
		}elseif($tipe_id == 5){ //Pengajuan
			$jenis_user_id = 5;
			$this->getUsers($jenis_user_id, $data_array);
		}elseif($tipe_id == 6){ //Pengajuan
			$jenis_user_id = 6;
			$this->getUsers($jenis_user_id, $data_array);
		}elseif($tipe_id == 7){ //TANDA TERIMA
			$jenis_user_id = 7;
			$this->getUsers($jenis_user_id, $data_array);
		}elseif($tipe_id == 9){ //DRAFT SPK
			$jenis_user_id = 9;
			$this->getUsers($jenis_user_id, $data_array);
		}elseif($tipe_id == 10){ //VALIDASI SPK
			$jenis_user_id = 10;
			$this->getUsers($jenis_user_id, $data_array);
		}elseif($tipe_id == 11){ //VALIDASI SPK
			$jenis_user_id = 11;
			$this->getUsers($jenis_user_id, $data_array);
		}elseif($tipe_id == 12){ //VALIDASI SPK
			$jenis_user_id = 12;
			$this->getUsers($jenis_user_id, $data_array);
		}elseif($tipe_id == 13){ //VALIDASI SPK
			$jenis_user_id = 13;
			$this->getUsers($jenis_user_id, $data_array);
		}elseif($tipe_id == 14){ //VALIDASI SPK
			$jenis_user_id = 14;
			$this->getUsers($jenis_user_id, $data_array);
		}elseif($tipe_id == 15){ //VALIDASI SPK
			$jenis_user_id = 15;
			$this->getUsers($jenis_user_id, $data_array);
		}
	}
	
	function getUsers($jenis_user_id, $data_array){
		
		if($jenis_user_id == 1){ //Pengajuan Baru
			$pengajuan_id = $data_array['data_id'];
			$pengajuan = $this->getDetailPengajuan($pengajuan_id);
			$gid = array(6); //ROLE PELAYANAN
			$this->db->select('users_data.*');
			$this->db->where_in('users_data.gid', $gid);
			$query = $this->db->get('users_data');
			$query = $query->result_array();
			if($query){
				foreach($query as $rows){
					
					$from_id = $pengajuan['user_id'];
					$to_id = $rows['id'];
					
					$category_id = 1; //Pengajuan
					$subject = 'Submit Pengajuan '.$pengajuan['permohonan_no'].' baru oleh '.$pengajuan['user_fullname'];
					$message = 'Dear '.$rows['fullname'].'<br><br>
					
								Pada tanggal '.$pengajuan['tanggal_input'].', User '.$pengajuan['user_fullname'].' melakukan submit pengajuan dengan nomor pengajuan : ['.$pengajuan['permohonan_no'].'] perusahaan '.$pengajuan['permohonan_nama'].' dan pic ['.$pengajuan['permohonan_pic'].'] .<br>
								Silakan lakukan pengecekan pada link berikut.<br><br>
								
								<a href="'.$pengajuan['link'].'">Klik Disini untuk detail Pengajuan</a><br><br>
								
								Terima kasih<br><br>
								Regards,<br>
								Kalibrasi SIMPKTN System';
								
					$data_id = $pengajuan_id;
					$is_wa = 0;
					$this->ortyd->setInbox($from_id, $to_id, $category_id, $subject, $message, $data_id,$is_wa);
				}
			}
		}elseif($jenis_user_id == 2){ //Pengajuan Baru
			$pengajuan_id = $data_array['data_id'];
			$pengajuan = $this->getDetailPengajuan($pengajuan_id);
			$gid = array(4); //ROLE PELAYANAN
			$this->db->select('users_data.*');
			$this->db->where_in('users_data.gid', $gid);
			$query = $this->db->get('users_data');
			$query = $query->result_array();
			if($query){
				foreach($query as $rows){
					
					$from_id = $pengajuan['user_update_id'];
					$to_id = $rows['id'];
					
					$category_id = 1; //Pengajuan
					$subject = 'Menunggu Verifikasi Pengajuan '.$pengajuan['permohonan_no'].' oleh Kepala Balai';
					$message = 'Dear '.$rows['fullname'].'<br><br>
					
								Pada tanggal '.$pengajuan['tanggal_input'].', Petugas Pelayanan '.$pengajuan['user_update'].' melakukan pengiriman pengajuan kepada anda dengan nomor pengajuan : ['.$pengajuan['permohonan_no'].'] perusahaan '.$pengajuan['permohonan_nama'].' dan pic ['.$pengajuan['permohonan_pic'].'] untuk dilakukan verifikasi.<br>
								Silakan lakukan verifikasi pada link berikut.<br><br>
								
								<a href="'.$pengajuan['link'].'">Klik Disini untuk detail Pengajuan</a><br><br>
								
								Terima kasih<br><br>
								Regards,<br>
								Kalibrasi SIMPKTN System';
								
					$data_id = $pengajuan_id;
					$is_wa = 0;
					$this->ortyd->setInbox($from_id, $to_id, $category_id, $subject, $message, $data_id,$is_wa);
				}
			}
		}elseif($jenis_user_id == 3){ //Pengajuan Baru
			$pengajuan_id = $data_array['data_id'];
			$pengajuan = $this->getDetailPengajuan($pengajuan_id);
			//$gid = array(4); //ROLE PELAYANAN
			$this->db->select('users_data.*');
			$this->db->where_in('users_data.id', $pengajuan['user_id']);
			$query = $this->db->get('users_data');
			$query = $query->result_array();
			if($query){
				foreach($query as $rows){
					
					$from_id = $pengajuan['user_update_id'];
					$to_id = $rows['id'];
					
					$category_id = 1; //Pengajuan
					$subject = 'Menunggu Konfirmasi Pengajuan '.$pengajuan['permohonan_no'].' oleh '.$pengajuan['user_fullname'].' ';
					$message = 'Dear '.$rows['fullname'].'<br><br>
					
								Pada tanggal '.$pengajuan['tanggal_input'].', Pelayanan kalibrasi melakukan pengiriman Konfirmasi pengajuan kepada anda dengan nomor pengajuan : ['.$pengajuan['permohonan_no'].'] perusahaan '.$pengajuan['permohonan_nama'].' dan pic ['.$pengajuan['permohonan_pic'].'] untuk dilakukan konfirmasi pengajuan.<br>
								Silakan lakukan konfirmasi pengajuan anda pada link berikut.<br><br>
								
								<a href="'.$pengajuan['link'].'">Klik Disini untuk detail Pengajuan</a><br><br>
								
								Terima kasih<br><br>
								Regards,<br>
								Kalibrasi SIMPKTN System';
								
					$data_id = $pengajuan_id;
					$is_wa = 0;
					$this->ortyd->setInbox($from_id, $to_id, $category_id, $subject, $message, $data_id,$is_wa);
				}
			}
		}elseif($jenis_user_id == 4){ //Pengajuan Baru
			$pengajuan_id = $data_array['data_id'];
			$pengajuan = $this->getDetailPengajuan($pengajuan_id);
			$gid = array(6); //ROLE PELAYANAN
			$this->db->select('users_data.*');
			$this->db->where_in('users_data.gid', $gid);
			$query = $this->db->get('users_data');
			$query = $query->result_array();
			if($query){
				foreach($query as $rows){
					
					$from_id = $pengajuan['user_id'];
					$to_id = $rows['id'];
					
					$category_id = 1; //Pengajuan
					$subject = 'Konfirmasi Pengajuan '.$pengajuan['permohonan_no'].' oleh '.$pengajuan['user_fullname'];
					$message = 'Dear '.$rows['fullname'].'<br><br>
					
								Pada tanggal '.$pengajuan['tanggal_input'].', User '.$pengajuan['user_fullname'].' melakukan konfirmasi pengajuan dengan nomor pengajuan : ['.$pengajuan['permohonan_no'].'] perusahaan '.$pengajuan['permohonan_nama'].' dan pic ['.$pengajuan['permohonan_pic'].'] .<br>
								Silakan lakukan pengecekan pada link berikut.<br><br>
								
								<a href="'.$pengajuan['link'].'">Klik Disini untuk detail Pengajuan</a><br><br>
								
								Terima kasih<br><br>
								Regards,<br>
								Kalibrasi SIMPKTN System';
								
					$data_id = $pengajuan_id;
					$is_wa = 0;
					$this->ortyd->setInbox($from_id, $to_id, $category_id, $subject, $message, $data_id,$is_wa);
				}
			}
		}elseif($jenis_user_id == 5){ //Pengajuan Baru
			$pengajuan_id = $data_array['data_id'];
			$pengajuan = $this->getDetailPengajuan($pengajuan_id);
			//$gid = array(4); //ROLE PELAYANAN
			$this->db->select('users_data.*');
			$this->db->where_in('users_data.id', $pengajuan['user_id']);
			$query = $this->db->get('users_data');
			$query = $query->result_array();
			if($query){
				foreach($query as $rows){
					
					$from_id = $pengajuan['user_update_id'];
					$to_id = $rows['id'];
					
					$category_id = 1; //Pengajuan
					$subject = 'Revisi Pengajuan '.$pengajuan['permohonan_no'].' oleh '.$pengajuan['user_fullname'].' ';
					$message = 'Dear '.$rows['fullname'].'<br><br>
					
								Pada tanggal '.$pengajuan['tanggal_input'].', Pelayanan kalibrasi melakukan pengiriman revisi pengajuan kepada anda dengan nomor pengajuan : ['.$pengajuan['permohonan_no'].'] perusahaan '.$pengajuan['permohonan_nama'].' dan pic ['.$pengajuan['permohonan_pic'].'] untuk dilakukan revisi pengajuan.<br>
								Silakan lakukan revisi pengajuan anda pada link berikut.<br><br>
								
								<a href="'.$pengajuan['link'].'">Klik Disini untuk detail Pengajuan</a><br><br>
								
								Terima kasih<br><br>
								Regards,<br>
								Kalibrasi SIMPKTN System';
								
					$data_id = $pengajuan_id;
					$is_wa = 0;
					$this->ortyd->setInbox($from_id, $to_id, $category_id, $subject, $message, $data_id,$is_wa);
				}
			}
		}elseif($jenis_user_id == 7){ //Pengajuan Baru
			$pengajuan_id = $data_array['data_id'];
			$arraydata = array(
				'tipe' => 1,
				'tanda_terima_id' => $pengajuan_id
			);
			$pengajuan = $this->getDetailPengajuan($pengajuan_id, $arraydata);
			//$gid = array(4); //ROLE PELAYANAN
			$this->db->select('users_data.*');
			$this->db->where_in('users_data.id', $pengajuan['user_id']);
			$query = $this->db->get('users_data');
			$query = $query->result_array();
			if($query){
				foreach($query as $rows){
					
					$from_id = $pengajuan['tanda_terima_user_update_id'];
					$to_id = $rows['id'];
					
					$category_id = 2; //Pengajuan
					$subject = 'Tanda Terima Alat Pengajuan '.$pengajuan['permohonan_no'];
					$message = 'Dear '.$rows['fullname'].'<br><br>
					
								Pada tanggal '.$pengajuan['tanda_terima_tanggal'].', Pelayanan kalibrasi melakukan penerimaan alat pengajuan anda dengan nomor pengajuan : ['.$pengajuan['permohonan_no'].'] perusahaan '.$pengajuan['permohonan_nama'].' dan pic ['.$pengajuan['permohonan_pic'].'] .<br>
								Silakan lakukan pengecekan pengajuan anda pada link berikut.<br><br>
								
								<a href="'.$pengajuan['link'].'">Klik Disini untuk detail Pengajuan</a><br><br>
								
								Terima kasih<br><br>
								Regards,<br>
								Kalibrasi SIMPKTN System';
								
					$data_id = $pengajuan_id;
					$is_wa = 0;
					$this->ortyd->setInbox($from_id, $to_id, $category_id, $subject, $message, $data_id,$is_wa);
				}
			}
		}elseif($jenis_user_id == 10){ //SPK Kepala
			$pengajuan_id = $data_array['data_id'];
			$arraydata = array(
				'tipe' => 2,
				'spk_id' => $pengajuan_id
			);
			$pengajuan = $this->getDetailPengajuan($pengajuan_id, $arraydata);
			$gid = array(4); //ROLE Kepala balai
			$this->db->select('users_data.*');
			$this->db->where_in('users_data.gid', $gid);
			$query = $this->db->get('users_data');
			$query = $query->result_array();
			if($query){
				foreach($query as $rows){
					
					$from_id = $pengajuan['spk_user_update_id'];
					$to_id = $rows['id'];
					
					$category_id = 3; //Pengajuan
					$subject = 'Menunggu Verifikasi Draft SPK Pengajuan '.$pengajuan['permohonan_no'].' oleh Kepala Balai';
					$message = 'Dear '.$rows['fullname'].'<br><br>
					
								Pada tanggal '.$pengajuan['spk_tanggal_input'].', Petugas Pelayanan '.$pengajuan['user_update'].' melakukan pengiriman SPK Draft kepada anda dengan nomor pengajuan : ['.$pengajuan['permohonan_no'].'] perusahaan '.$pengajuan['permohonan_nama'].' dan pic ['.$pengajuan['permohonan_pic'].'] untuk dilakukan verifikasi.<br>
								Silakan lakukan verifikasi pada link berikut.<br><br>
								
								<a href="'.$pengajuan['spk_link'].'">Klik Disini untuk detail SPK</a><br><br>
								
								Terima kasih<br><br>
								Regards,<br>
								Kalibrasi SIMPKTN System';
								
					$data_id = $pengajuan_id;
					$is_wa = 0;
					$this->ortyd->setInbox($from_id, $to_id, $category_id, $subject, $message, $data_id,$is_wa);
				}
			}
		}elseif($jenis_user_id == 11){ //SPK Terbit
			$pengajuan_id = $data_array['data_id'];
			$arraydata = array(
				'tipe' => 2,
				'spk_id' => $pengajuan_id
			);
			$pengajuan = $this->getDetailPengajuan($pengajuan_id, $arraydata);
			$gid = array(6); //ROLE Pelayanan
			$this->db->select('users_data.*');
			$this->db->where_in('users_data.gid', $gid);
			$query = $this->db->get('users_data');
			$query = $query->result_array();
			if($query){
				foreach($query as $rows){
					
					$from_id = $pengajuan['spk_user_update_id'];
					$to_id = $rows['id'];
					
					$category_id = 3; //Pengajuan
					$subject = 'SPK Pengajuan '.$pengajuan['permohonan_no'].' telah terbit dengan nomor SPK '.$pengajuan['spk_no'];
					$message = 'Dear '.$rows['fullname'].'<br><br>
					
								Pada tanggal '.$pengajuan['spk_tanggal_input'].', Kepalai Balai melakukan verifikasi SPK Draft dengan nomor pengajuan : ['.$pengajuan['permohonan_no'].'] perusahaan '.$pengajuan['permohonan_nama'].' dan pic ['.$pengajuan['permohonan_pic'].'] SPK telah terbit dengan nomor SPK ['.$pengajuan['spk_no'].'].<br>
								Silakan lakukan pengecekan pada link berikut.<br><br>
								
								<a href="'.$pengajuan['spk_link'].'">Klik Disini untuk detail SPK</a><br><br>
								
								Terima kasih<br><br>
								Regards,<br>
								Kalibrasi SIMPKTN System';
								
					$data_id = $pengajuan_id;
					$is_wa = 0;
					$this->ortyd->setInbox($from_id, $to_id, $category_id, $subject, $message, $data_id,$is_wa);
				}
			}
			
			
			$pengajuan_disposisi_spk_id_array = [];
			$this->db->select('data_pengajuan_spk_disposisi.disposisi_id');
			$this->db->where('data_pengajuan_spk_disposisi.pengajuan_spk_id',$pengajuan['spk_id']);
			$this->db->where('data_pengajuan_spk_disposisi.active',1);
			$this->db->group_by('data_pengajuan_spk_disposisi.disposisi_id');
			//$this->db->order_by();
			$querybesaran = $this->db->get('data_pengajuan_spk_disposisi');
			$querybesaran = $querybesaran->result_object();
			if($querybesaran){
				foreach($querybesaran as $rowsbesaran){
					array_push($pengajuan_disposisi_spk_id_array, $rowsbesaran->disposisi_id);
				}
			}
			
			$this->db->select('users_data.*');
			if(count($pengajuan_disposisi_spk_id_array) > 0){
				$this->db->where_in('users_data.id', $pengajuan_disposisi_spk_id_array);
			}else{
				$this->db->where('users_data.id', 0);
			}
			$query = $this->db->get('users_data');
			$query = $query->result_array();
			if($query){
				foreach($query as $rows){
					
					$from_id = $pengajuan['spk_user_update_id'];
					$to_id = $rows['id'];
					
					$category_id = 3; //Pengajuan
					$subject = 'SPK Pengajuan '.$pengajuan['permohonan_no'].' untuk memulai kalibrasi';
					$message = 'Dear '.$rows['fullname'].'<br><br>
					
								Pada tanggal '.$pengajuan['spk_tanggal_input'].', Kepalai Balai melakukan verifikasi SPK Draft dengan nomor pengajuan : ['.$pengajuan['permohonan_no'].'] perusahaan '.$pengajuan['permohonan_nama'].' dan pic ['.$pengajuan['permohonan_pic'].'] SPK telah terbit dengan nomor SPK ['.$pengajuan['spk_no'].'] silahkan melakukan kalibrasi.<br>
								Silakan lakukan pengecekan pada link berikut.<br><br>
								
								<a href="'.$pengajuan['spk_link'].'">Klik Disini untuk detail SPK</a><br><br>
								
								Terima kasih<br><br>
								Regards,<br>
								Kalibrasi SIMPKTN System';
								
					$data_id = $pengajuan_id;
					$is_wa = 0;
					$this->ortyd->setInbox($from_id, $to_id, $category_id, $subject, $message, $data_id,$is_wa);
				}
			}
			
		}elseif($jenis_user_id == 12){ //Input Invoice
			$pengajuan_id = $data_array['data_id'];
			$arraydata = array(
				'tipe' => 2,
				'spk_id' => $pengajuan_id
			);
			$pengajuan = $this->getDetailPengajuan($pengajuan_id, $arraydata);
			$gid = array(8); //ROLE Bendahara
			$this->db->select('users_data.*');
			$this->db->where_in('users_data.gid', $gid);
			$query = $this->db->get('users_data');
			$query = $query->result_array();
			if($query){
				foreach($query as $rows){
					
					$from_id = $pengajuan['spk_user_update_id'];
					$to_id = $rows['id'];
					
					$category_id = 4; //Pengajuan
					$subject = 'Kalibrasi SPK '.$pengajuan['spk_no'].' Telah Selesai';
					$message = 'Dear '.$rows['fullname'].'<br><br>
					
								Pada tanggal '.$pengajuan['spk_tanggal_input'].', Petugas telah selesai melakukan Kalibrasi dengan nomor pengajuan : ['.$pengajuan['permohonan_no'].'] perusahaan '.$pengajuan['permohonan_nama'].' dan pic ['.$pengajuan['permohonan_pic'].'], SPK dengan nomor ['.$pengajuan['spk_no'].'].<br>
								Silakan lakukan pembuatan invoice pada link berikut.<br><br>
								
								<a href="'.$pengajuan['spk_link'].'">Klik Disini untuk detail SPK</a><br><br>
								
								Terima kasih<br><br>
								Regards,<br>
								Kalibrasi SIMPKTN System';
								
					$data_id = $pengajuan_id;
					$is_wa = 0;
					$this->ortyd->setInbox($from_id, $to_id, $category_id, $subject, $message, $data_id,$is_wa);
				}
			}
			
		}elseif($jenis_user_id == 122){ //Verifikasi Kepala Balai
			$pengajuan_id = $data_array['data_id'];
			$arraydata = array(
				'tipe' => 2,
				'spk_id' => $pengajuan_id
			);
			$pengajuan = $this->getDetailPengajuan($pengajuan_id, $arraydata);
			$gid = array(4); //ROLE Kepala balai
			$this->db->select('users_data.*');
			$this->db->where_in('users_data.gid', $gid);
			$query = $this->db->get('users_data');
			$query = $query->result_array();
			if($query){
				foreach($query as $rows){
					
					$from_id = $pengajuan['spk_user_update_id'];
					$to_id = $rows['id'];
					
					$category_id = 3; //Pengajuan
					$subject = 'Menunggu Verifikasi Kalibrasi SPK '.$pengajuan['spk_no'].' oleh Kepala Balai';
					$message = 'Dear '.$rows['fullname'].'<br><br>
					
								Pada tanggal '.$pengajuan['spk_tanggal_input'].', Petugas telah selesai melakukan Kalibrasi dengan nomor pengajuan : ['.$pengajuan['permohonan_no'].'] perusahaan '.$pengajuan['permohonan_nama'].' dan pic ['.$pengajuan['permohonan_pic'].'], SPK dengan nomor ['.$pengajuan['spk_no'].'].<br>
								Silakan lakukan validasi pada link berikut.<br><br>
								
								<a href="'.$pengajuan['spk_link'].'">Klik Disini untuk detail SPK</a><br><br>
								
								Terima kasih<br><br>
								Regards,<br>
								Kalibrasi SIMPKTN System';
								
					$data_id = $pengajuan_id;
					$is_wa = 0;
					$this->ortyd->setInbox($from_id, $to_id, $category_id, $subject, $message, $data_id,$is_wa);
				}
			}
			
		}elseif($jenis_user_id == 13){ //Input Invoice
			$pengajuan_id = $data_array['data_id'];
			$arraydata = array(
				'tipe' => 3,
				'spk_id' => $pengajuan_id
			);
			$pengajuan = $this->getDetailPengajuan($pengajuan_id, $arraydata);
			$gid = array(7); //verificator
			$this->db->select('users_data.*');
			$this->db->where_in('users_data.gid', $gid);
			$this->db->where('users_data.id !=', $pengajuan['spk_disposisi_id']);
			$query = $this->db->get('users_data');
			$query = $query->result_array();
			if($query){
				foreach($query as $rows){
					
					$from_id = $pengajuan['spk_user_update_id'];
					$to_id = $rows['id'];
					
					$category_id = 5; //Pengajuan
					$subject = 'Menunggu Verifikasi Kalibrasi SPK '.$pengajuan['spk_no'].' dengan Nama Alat '.$pengajuan['alat_nama'];
					$message = 'Dear '.$rows['fullname'].'<br><br>
					
								Pada tanggal '.$pengajuan['alat_tanggal_input'].', Petugas Lab ['.$pengajuan['spk_disposisi_name'].'] telah selesai melakukan Kalibrasi dengan nomor pengajuan : ['.$pengajuan['permohonan_no'].'] perusahaan '.$pengajuan['permohonan_nama'].' dan pic ['.$pengajuan['permohonan_pic'].'], SPK dengan nomor ['.$pengajuan['spk_no'].'].<br>
								Silakan lakukan verifikasi pada link berikut.<br><br>
								
								<a href="'.$pengajuan['alat_link'].'">Klik Disini untuk detail SPK</a><br><br>
								
								Terima kasih<br><br>
								Regards,<br>
								Kalibrasi SIMPKTN System';
								
					$data_id = $pengajuan_id;
					$is_wa = 0;
					$this->ortyd->setInbox($from_id, $to_id, $category_id, $subject, $message, $data_id,$is_wa);
				}
			}
			
		}elseif($jenis_user_id == 14){ //Verifikasi
			$pengajuan_id = $data_array['data_id'];
			$arraydata = array(
				'tipe' => 3,
				'spk_id' => $pengajuan_id
			);
			$pengajuan = $this->getDetailPengajuan($pengajuan_id, $arraydata);
			$gid = array(9); //verificator
			$this->db->select('users_data.*');
			$this->db->where_in('users_data.gid', $gid);
			$this->db->where('users_data.id !=', $pengajuan['spk_disposisi_id']);
			$query = $this->db->get('users_data');
			$query = $query->result_array();
			if($query){
				foreach($query as $rows){
					
					$from_id = $pengajuan['spk_user_update_id'];
					$to_id = $rows['id'];
					
					$category_id = 5; //Pengajuan
					$subject = 'Menunggu Verifikasi Kalibrasi SPK '.$pengajuan['spk_no'].' dengan Nama Alat '.$pengajuan['alat_nama'];
					$message = 'Dear '.$rows['fullname'].'<br><br>
					
								Pada tanggal '.$pengajuan['alat_tanggal_input'].', Petugas Verifikasi Pertama telah selesai melakukan Verifikasi Kalibrasi dengan nomor pengajuan : ['.$pengajuan['permohonan_no'].'] perusahaan '.$pengajuan['permohonan_nama'].' dan pic ['.$pengajuan['permohonan_pic'].'], SPK dengan nomor ['.$pengajuan['spk_no'].'].<br>
								Silakan lakukan verifikasi pada link berikut.<br><br>
								
								<a href="'.$pengajuan['alat_link'].'">Klik Disini untuk detail SPK</a><br><br>
								
								Terima kasih<br><br>
								Regards,<br>
								Kalibrasi SIMPKTN System';
								
					$data_id = $pengajuan_id;
					$is_wa = 0;
					$this->ortyd->setInbox($from_id, $to_id, $category_id, $subject, $message, $data_id,$is_wa);
				}
			}
			
		}elseif($jenis_user_id == 15){ //Input Invoice
			$pengajuan_id = $data_array['data_id'];
			$arraydata = array(
				'tipe' => 3,
				'spk_id' => $pengajuan_id
			);
			$pengajuan = $this->getDetailPengajuan($pengajuan_id, $arraydata);
			$gid = array(7); //verificator
			$this->db->select('users_data.*');
			$this->db->where_in('users_data.gid', $gid);
			$this->db->where_not_in('users_data.id', array($pengajuan['spk_disposisi_id'], $pengajuan['alat_update_id']));
			$query = $this->db->get('users_data');
			$query = $query->result_array();
			if($query){
				foreach($query as $rows){
					
					$from_id = $pengajuan['spk_user_update_id'];
					$to_id = $rows['id'];
					
					$category_id = 5; //Pengajuan
					$subject = 'Menunggu Verifikasi Kalibrasi SPK '.$pengajuan['spk_no'].' dengan Nama Alat '.$pengajuan['alat_nama'];
					$message = 'Dear '.$rows['fullname'].'<br><br>
					
								Pada tanggal '.$pengajuan['alat_tanggal_input'].', Petugas Lab ['.$pengajuan['spk_disposisi_name'].'] telah selesai melakukan Kalibrasi dengan nomor pengajuan : ['.$pengajuan['permohonan_no'].'] perusahaan '.$pengajuan['permohonan_nama'].' dan pic ['.$pengajuan['permohonan_pic'].'], SPK dengan nomor ['.$pengajuan['spk_no'].'].<br>
								Silakan lakukan verifikasi pada link berikut.<br><br>
								
								<a href="'.$pengajuan['alat_link'].'">Klik Disini untuk detail SPK</a><br><br>
								
								Terima kasih<br><br>
								Regards,<br>
								Kalibrasi SIMPKTN System';
								
					$data_id = $pengajuan_id;
					$is_wa = 0;
					$this->ortyd->setInbox($from_id, $to_id, $category_id, $subject, $message, $data_id,$is_wa);
				}
			}
		}	
	}
	
	function getDetailPengajuan($pengajuan_id, $arraydata = null){
		
		$data = [];
		
		if($arraydata != null){
			if($arraydata['tipe'] == 1){
				$tanda_terima = $this->getDetailTandaTerima($arraydata['tanda_terima_id']);
				$pengajuan_id = $tanda_terima['tanda_terima_pengajuan_id'];
				$data['tanda_terima_no'] = $tanda_terima['tanda_terima_no'];
				$data['tanda_terima_tanggal'] = $tanda_terima['tanda_terima_tanggal'];
				$data['tanda_terima_user_update_id'] = $tanda_terima['user_update_id'];
			}elseif($arraydata['tipe'] == 2){
				$spk = $this->getDetailSPK($arraydata['spk_id']);
				$pengajuan_id = $spk['spk_pengajuan_id'];
				$data['spk_id'] = $spk['spk_id'];
				$data['spk_no'] = $spk['spk_no'];
				$data['spk_tanggal'] = $spk['spk_tanggal'];
				$data['spk_user_update_id'] = $spk['user_update_id'];
				$data['spk_tanggal_input'] = $spk['tanggal_input'];
				$data['spk_link'] = $spk['link'];
				$data['spk_disposisi_id'] = $spk['spk_disposisi_id'];
			}elseif($arraydata['tipe'] == 3){
				$alat = $this->getDetailAlat($arraydata['spk_id']);
				$pengajuan_id = $alat['spk_pengajuan_id'];
				$data['alat_id'] = $alat['alat_id'];
				$data['alat_nama'] = $alat['alat_nama'];
				$data['alat_tanggal_input'] = $alat['alat_tanggal_input'];
				$data['alat_link'] = $alat['alat_link'];
				$data['alat_update_id'] = $alat['alat_update_id'];
				$data['spk_id'] = $alat['spk_id'];
				$data['spk_no'] = $alat['spk_no'];
				$data['spk_tanggal'] = $alat['spk_tanggal'];
				$data['spk_user_update_id'] = $alat['user_update_id'];
				$data['spk_tanggal_input'] = $alat['tanggal_input'];
				$data['spk_link'] = $alat['link'];
				$data['spk_disposisi_id'] = $alat['spk_disposisi_id'];
				$data['spk_disposisi_name'] = $alat['spk_disposisi_name'];
			}
		}
		
		$this->db->select('vw_data_pengajuan.*');
		$this->db->where('vw_data_pengajuan.id', $pengajuan_id);
		$query = $this->db->get('vw_data_pengajuan');
		$query = $query->result_array();
		if($query){
			foreach ($query as $rows){
				$data['id'] = $rows['id'];
				$data['permohonan_no'] = $rows['permohonan_no'];
				$data['permohonan_nama'] = $rows['permohonan_nama'];
				$data['permohonan_pic'] = $rows['permohonan_pic'];
				$data['user_id'] = $rows['user_id'];
				$data['user_fullname'] = $rows['permohonan_dibuat_oleh'];
				$data['user_update_id'] = $rows['modifiedid'];
				$data['user_update'] = $rows['permohonan_diubah_oleh'];
				$data['tanggal_input'] = $this->ortyd->format_date($rows['modified']);
				$data['link'] = base_url().'data_pengajuan/editdata/'.$rows['slug'];
				$data['modifiedid'] = $rows['modifiedid'];
			}
		}else{
			$data = null;
		}
		
		return $data;
	}
	
	
	function getDetailTandaTerima($tanda_terima_id){
		$data = [];
		$this->db->select('data_pengajuan_tanda_terima.*');
		$this->db->where('data_pengajuan_tanda_terima.id', $tanda_terima_id);
		$query = $this->db->get('data_pengajuan_tanda_terima');
		$query = $query->result_array();
		if($query){
			foreach ($query as $rows){
				$data['tanda_terima_id'] = $rows['id'];
				$data['tanda_terima_pengajuan_id'] = $rows['pengajuan_id'];
				$data['tanda_terima_no'] = $rows['tanda_terima_no'];
				$data['tanda_terima_tanggal'] = $this->ortyd->format_date($rows['modified']);
				$data['user_update_id'] = $rows['modifiedid'];
			}
		}else{
			$data = null;
		}
		
		return $data;
	}
	
	function getDetailSPK($spk_id){
		$data = [];
		$this->db->select('data_pengajuan_spk.*');
		$this->db->where('data_pengajuan_spk.id', $spk_id);
		$query = $this->db->get('data_pengajuan_spk');
		$query = $query->result_array();
		if($query){
			foreach ($query as $rows){
				$data['spk_id'] = $rows['id'];
				$data['spk_pengajuan_id'] = $rows['pengajuan_id'];
				$data['spk_no'] = $rows['spk_no'];
				$data['spk_draft_no'] = $rows['spk_draft_no'];
				$data['spk_tanggal'] = $this->ortyd->format_date($rows['spk_tanggal']);
				$data['tanggal_input'] = $this->ortyd->format_date($rows['modified']);
				$data['link'] = base_url().'data_pengajuan_spk/editdata/'.$rows['slug'];
				$data['user_update_id'] = $rows['modifiedid'];
				$data['spk_disposisi_id'] = $rows['disposisi_id'];
			}
		}else{
			$data = null;
		}
		
		return $data;
	}
	
	function getDetailAlat($alat_id){
		$data = [];
		$this->db->select('data_pengajuan_spk.*, data_pengajuan_alat.id as alat_id, data_alat.modified as alat_modified, data_alat.slug as alat_slug, data_alat.alat_nama, petugaslab.fullname as disposisi_name, data_alat.modifiedid as alat_modifiedid');
		$this->db->where('data_alat.id', $alat_id);
		$this->db->join('data_pengajuan_alat','data_pengajuan_alat.spk_id = data_pengajuan_spk.id');
		$this->db->join('users_data petugaslab','petugaslab.id = data_pengajuan_alat.disposisi_id');
		$this->db->join('data_alat','data_alat.id = data_pengajuan_alat.alat_id');
		$query = $this->db->get('data_pengajuan_spk');
		$query = $query->result_array();
		if($query){
			foreach ($query as $rows){
				$data['alat_id'] = $rows['alat_id'];
				$data['alat_nama'] = $rows['alat_nama'];
				$data['alat_tanggal_input'] = $this->ortyd->format_date($rows['alat_modified']);
				$data['alat_link'] = base_url().'data_alat/editdata/'.$rows['alat_slug'];
				$data['alat_update_id'] = $rows['alat_modifiedid'];
				$data['spk_id'] = $rows['id'];
				$data['spk_pengajuan_id'] = $rows['pengajuan_id'];
				$data['spk_no'] = $rows['spk_no'];
				$data['spk_draft_no'] = $rows['spk_draft_no'];
				$data['spk_tanggal'] = $this->ortyd->format_date($rows['spk_tanggal']);
				$data['tanggal_input'] = $this->ortyd->format_date($rows['modified']);
				$data['link'] = base_url().'data_pengajuan_spk/editdata/'.$rows['slug'];
				$data['user_update_id'] = $rows['modifiedid'];
				$data['spk_disposisi_id'] = $rows['disposisi_id'];
				$data['spk_disposisi_name'] = $rows['disposisi_name'];
			}
		}else{
			$data = null;
		}
		
		return $data;
	}
	
	
	function setInboxLOP($status_id, $data_array){
			
		if($status_id == 2){ //NEW LOP
			$gid = array(4,6);
			$this->db->select('users_data.id as user_id');
			$this->db->where_in('users_data.gid', $gid);
			$query = $this->db->get('users_data');
			$query = $query->result_object();
			if($query){
				foreach($query as $rows){
					$from_id = $data_array['from_id'];
					$to_id = $rows->user_id;
					$category_id = 1;
						//$subject = $data_array['subject'];
						//$message = $data_array['message'];
						
					$subject = 'Input LOP baru dimasukkan oleh AM';
					$message = 'Dear Manager SBU<br><br>
								Pada tanggal xx, AM melakukan input LOP baru dengan<br>
								status NEW CUSTOMER.<br>
								Silakan lakukan pengecekan.<br><br>
								Terima kasih<br><br>
								Regards,<br>
								MAST System';
								
								
					$data_id = $data_array['data_id'];
					$is_wa = 0;
					$this->ortyd->setInbox($from_id, $to_id, $category_id, $subject, $message, $data_id,$is_wa);
				}
			}	
				
				$this->db->select('users_data.id as user_id');
				$this->db->where_in('users_data.id', $data_array['from_id']);
				$query = $this->db->get('users_data');
				$query = $query->result_object();
				if($query){
					foreach($query as $rows){
						$from_id = $data_array['from_id'];
						$to_id = $from_id;
						$category_id = 1;
						//$subject = $data_array['subject'];
						//$message = $data_array['message'];
						
						$subject = 'Input LOP baru';
						$message = 'Dear AM<br><br>
								Pada tanggal xx, LOP Anda telah update ke F0<br>
								status NEW CUSTOMER.<br>
								Terima kasih<br><br>
								Regards,<br>
								MAST System';
								
								
						$data_id = $data_array['data_id'];
						$is_wa = 0;
						$this->ortyd->setInbox($from_id, $to_id, $category_id, $subject, $message, $data_id,$is_wa);
					}
				}
				
			}elseif($status_id == 3){ //NEW LOP
				$gid = array(4,6);
				$this->db->select('users_data.id as user_id');
				$this->db->where_in('users_data.gid', $gid);
				$query = $this->db->get('users_data');
				$query = $query->result_object();
				if($query){
					foreach($query as $rows){
						$from_id = $data_array['from_id'];
						$to_id = $rows->user_id;
						$category_id = 2;
						$subject = $data_array['subject'];
						$message = $data_array['message'];
						
						$subject = 'LOP status F0 naik status menjadi F1';
						$message = 'Dear Manager SBU<br><br>
								LOP dengan nomor xx sudah naik status menjadi F1<br>
								Silakan lakukan pengecekan.<br><br>
								Terima kasih<br><br>
								Regards,<br>
								MAST System';
								
						$data_id = $data_array['data_id'];
						$is_wa = 0;
						$this->ortyd->setInbox($from_id, $to_id, $category_id, $subject, $message, $data_id,$is_wa);
					}
				}	
				
				$this->db->select('users_data.id as user_id');
				$this->db->where_in('users_data.id', $data_array['from_id']);
				$query = $this->db->get('users_data');
				$query = $query->result_object();
				if($query){
					foreach($query as $rows){
						$from_id = $data_array['from_id'];
						$to_id = $from_id;
						$category_id = 1;
						//$subject = $data_array['subject'];
						//$message = $data_array['message'];
						
						$subject = 'Input LOP baru';
						$message = 'Dear AM<br><br>
								Pada tanggal xx, LOP Anda telah update ke F1<br>
								status NEW CUSTOMER.<br>
								Terima kasih<br><br>
								Regards,<br>
								MAST System';
								
								
						$data_id = $data_array['data_id'];
						$is_wa = 0;
						$this->ortyd->setInbox($from_id, $to_id, $category_id, $subject, $message, $data_id,$is_wa);
					}
				}
				
			}elseif($status_id == 4){ //NEW LOP
				$gid = array(4,6);
				$this->db->select('users_data.id as user_id');
				$this->db->where_in('users_data.gid', $gid);
				$query = $this->db->get('users_data');
				$query = $query->result_object();
				if($query){
					foreach($query as $rows){
						$from_id = $data_array['from_id'];
						$to_id = $rows->user_id;
						$category_id = 3;
						$subject = $data_array['subject'];
						$message = $data_array['message'];
						
						$subject = 'LOP status F0 naik status menjadi F2';
						$message = 'Dear Manager SBU<br><br>
								LOP dengan nomor xx sudah naik status menjadi F2<br>
								Silakan lakukan pengecekan.<br><br>
								Terima kasih<br><br>
								Regards,<br>
								MAST System';
								
						$data_id = $data_array['data_id'];
						$is_wa = 0;
						$this->ortyd->setInbox($from_id, $to_id, $category_id, $subject, $message, $data_id,$is_wa);
					}
				}

				$gid = array(4);
				$this->db->select('users_data.id as user_id');
				$this->db->where_in('users_data.gid', $gid);
				$query = $this->db->get('users_data');
				$query = $query->result_object();
				if($query){
					foreach($query as $rows){
						$from_id = $data_array['from_id'];
						$to_id = $rows->user_id;
						$category_id = 3;
						$subject = $data_array['subject'];
						$message = $data_array['message'];
						
						$subject = 'LOP status F0 naik status menjadi F2';
						$message = 'Dear Manager SBU<br><br>
								LOP dengan nomor xx sudah naik status menjadi F2<br>
								Silakan lakukan pengecekan dan segera persiapan proses<br>
								dan dokumen untuk Self Assesment.<br>
								Terima kasih<br><br>
								Regards,<br>
								MAST System';
								
						$data_id = $data_array['data_id'];
						$is_wa = 0;
						$this->ortyd->setInbox($from_id, $to_id, $category_id, $subject, $message, $data_id,$is_wa);
					}
				}
				
				
				$this->db->select('users_data.id as user_id');
				$this->db->where_in('users_data.id', $data_array['from_id']);
				$query = $this->db->get('users_data');
				$query = $query->result_object();
				if($query){
					foreach($query as $rows){
						$from_id = $data_array['from_id'];
						$to_id = $from_id;
						$category_id = 1;
						//$subject = $data_array['subject'];
						//$message = $data_array['message'];
						
						$subject = 'Input LOP baru';
						$message = 'Dear AM<br><br>
								Pada tanggal xx, LOP Anda telah update ke F2<br>
								status NEW CUSTOMER.<br>
								Terima kasih<br><br>
								Regards,<br>
								MAST System';
								
								
						$data_id = $data_array['data_id'];
						$is_wa = 0;
						$this->ortyd->setInbox($from_id, $to_id, $category_id, $subject, $message, $data_id,$is_wa);
					}
				}
				
			}elseif($status_id == 5){ //NEW LOP
				$gid = array(5);
				$this->db->select('users_data.id as user_id');
				$this->db->where_in('users_data.gid', $gid);
				$query = $this->db->get('users_data');
				$query = $query->result_object();
				if($query){
					foreach($query as $rows){
						$from_id = $data_array['from_id'];
						$to_id = $rows->user_id;
						$category_id = 4;
						$subject = $data_array['subject'];
						$message = $data_array['message'];
						$data_id = $data_array['data_id'];
						$is_wa = 0;
						$this->ortyd->setInbox($from_id, $to_id, $category_id, $subject, $message, $data_id,$is_wa);
					}
				}
				
			}elseif($status_id == 6){ //NEW LOP
				$gid = array(5);
				$this->db->select('users_data.id as user_id');
				$this->db->where_in('users_data.gid', $gid);
				$query = $this->db->get('users_data');
				$query = $query->result_object();
				if($query){
					foreach($query as $rows){
						$from_id = $data_array['from_id'];
						$to_id = $rows->user_id;
						$category_id = 5;
						$subject = $data_array['subject'];
						$message = $data_array['message'];
						$data_id = $data_array['data_id'];
						$is_wa = 0;
						$this->ortyd->setInbox($from_id, $to_id, $category_id, $subject, $message, $data_id,$is_wa);
					}
				}
				
			}elseif($status_id == 7){ //NEW LOP
				$gid = array(8);
				$this->db->select('users_data.id as user_id');
				$this->db->where_in('users_data.gid', $gid);
				$query = $this->db->get('users_data');
				$query = $query->result_object();
				if($query){
					foreach($query as $rows){
						$from_id = $data_array['from_id'];
						$to_id = $rows->user_id;
						$category_id = 6;
						$subject = $data_array['subject'];
						$message = $data_array['message'];
						
						$subject = 'Mohon review dokumen yang disiapkan PIC Presales';
						$message = 'Dear Manager SBU<br><br>
							Presales sudah mempersiapkan dokumen untuk LOP xx.<br>
							Mohon lakukan proses review.<br><br>
							Terima kasih';
								
						$data_id = $data_array['data_id'];
						$is_wa = 0;
						$this->ortyd->setInbox($from_id, $to_id, $category_id, $subject, $message, $data_id,$is_wa);
					}
				}
				
			}elseif($status_id == 8){ //NEW LOP
				$gid = array(4);
				$this->db->select('users_data.id as user_id');
				$this->db->where_in('users_data.gid', $gid);
				$query = $this->db->get('users_data');
				$query = $query->result_object();
				if($query){
					foreach($query as $rows){
						$from_id = $data_array['from_id'];
						$to_id = $rows->user_id;
						$category_id = 7;
						$subject = $data_array['subject'];
						$message = $data_array['message'];
						$data_id = $data_array['data_id'];
						$is_wa = 0;
						$this->ortyd->setInbox($from_id, $to_id, $category_id, $subject, $message, $data_id,$is_wa);
					}
				}
				
			}elseif($status_id == 9){ //NEW LOP
				$gid = array(5);
				$this->db->select('users_data.id as user_id');
				$this->db->where_in('users_data.gid', $gid);
				$query = $this->db->get('users_data');
				$query = $query->result_object();
				if($query){
					foreach($query as $rows){
						$from_id = $data_array['from_id'];
						$to_id = $rows->user_id;
						$category_id = 8;
						$subject = $data_array['subject'];
						$message = $data_array['message'];
						$data_id = $data_array['data_id'];
						$is_wa = 0;
						$this->ortyd->setInbox($from_id, $to_id, $category_id, $subject, $message, $data_id,$is_wa);
					}
				}
				
			}elseif($status_id == 13){ //NEW LOP
				$gid = array(9);
				$this->db->select('users_data.id as user_id');
				$this->db->where_in('users_data.gid', $gid);
				$query = $this->db->get('users_data');
				$query = $query->result_object();
				if($query){
					foreach($query as $rows){
						$from_id = $data_array['from_id'];
						$to_id = $rows->user_id;
						$category_id = 9;
						$subject = $data_array['subject'];
						$message = $data_array['message'];
						$data_id = $data_array['data_id'];
						$is_wa = 0;
						$this->ortyd->setInbox($from_id, $to_id, $category_id, $subject, $message, $data_id,$is_wa);
					}
				}
				
			}elseif($status_id == 14){ //NEW LOP
				$gid = array(9);
				$this->db->select('users_data.id as user_id');
				$this->db->where_in('users_data.gid', $gid);
				$query = $this->db->get('users_data');
				$query = $query->result_object();
				if($query){
					foreach($query as $rows){
						$from_id = $data_array['from_id'];
						$to_id = $rows->user_id;
						$category_id = 9;
						$subject = $data_array['subject'];
						$message = $data_array['message'];
						$data_id = $data_array['data_id'];
						$is_wa = 0;
						$this->ortyd->setInbox($from_id, $to_id, $category_id, $subject, $message, $data_id,$is_wa);
					}
				}
				
			}elseif($status_id == 15){ //NEW LOP
				$gid = array(1,2,3,4,5,6,7,8,9,10);
				$this->db->select('users_data.id as user_id');
				$this->db->where_in('users_data.gid', $gid);
				$query = $this->db->get('users_data');
				$query = $query->result_object();
				if($query){
					foreach($query as $rows){
						$from_id = $data_array['from_id'];
						$to_id = $rows->user_id;
						$category_id = 12;
						$subject = $data_array['subject'];
						$message = $data_array['message'];
						$data_id = $data_array['data_id'];
						$is_wa = 0;
						$this->ortyd->setInbox($from_id, $to_id, $category_id, $subject, $message, $data_id,$is_wa);
					}
				}	
			}elseif($status_id == 16){ //NEW LOP
				$gid = array(1,2,3,4,5,6,7,8,9,10);
				$this->db->select('users_data.id as user_id');
				$this->db->where_in('users_data.gid', $gid);
				$query = $this->db->get('users_data');
				$query = $query->result_object();
				if($query){
					foreach($query as $rows){
						$from_id = $data_array['from_id'];
						$to_id = $rows->user_id;
						$category_id = 13;
						$subject = $data_array['subject'];
						$message = $data_array['message'];
						$data_id = $data_array['data_id'];
						$is_wa = 0;
						$this->ortyd->setInbox($from_id, $to_id, $category_id, $subject, $message, $data_id,$is_wa);
					}
				}	
			}elseif($status_id == 17){ //NEW LOP
				$gid = array(1,2,3,4,5,6,7,8,9,10);
				$this->db->select('users_data.id as user_id');
				$this->db->where_in('users_data.gid', $gid);
				$query = $this->db->get('users_data');
				$query = $query->result_object();
				if($query){
					foreach($query as $rows){
						$from_id = $data_array['from_id'];
						$to_id = $rows->user_id;
						$category_id = 14;
						$subject = $data_array['subject'];
						$message = $data_array['message'];
						$data_id = $data_array['data_id'];
						$is_wa = 0;
						$this->ortyd->setInbox($from_id, $to_id, $category_id, $subject, $message, $data_id,$is_wa);
					}
				}	
			}
		}
		
	function kirimemail(){

		$this->db->select('data_inbox.*');
		$this->db->where('data_inbox.is_email', 1);
		$this->db->where('data_inbox.email_date is null', null);
		$query = $this->db->get('data_inbox');
		$query = $query->result_object();
		if($query){
			foreach($query as $rows){
				$this->db->select('*');
				$this->db->where_in('users_data.id', $rows->to_id);
				$queryuser = $this->db->get('users_data');
				$queryuser = $queryuser->result_object();
				if($queryuser){
					foreach($queryuser as $rowsuser){
						$email = $rowsuser->email;
						$fullname = $rowsuser->fullname;
						$subject = $rows->subject;
						$message = $rows->message;
						$attachment = null;
						$sending = $this->ortyd->sendEmail($email, $fullname, $subject, $message, $attachment);
						if($sending){
							$data = array(	
								'email_date'	=> date('Y-m-d H:i:s')
							);
							
							$this->db->where('id',$rows->id);				
							$insert = $this->db->update('data_inbox',$data);
						}
					}		
				}
			}		
		}
		
		return true;
	}
	
	function sendMessage($content, $heading, $userID){
			$app_id_onesignal = 'addb86dd-20af-4bda-b713-97b35b464400';
			$this->db->select('notif_id');
			$this->db->where('id',$userID);
			$result = $this->db->get('users_data');
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
		
}
