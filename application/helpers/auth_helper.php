<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Helper untuk Authentication Login via Token
 */
if (!function_exists('auth_login')) {
    function auth_login($token, $login_via = 'Web Browser', $tipe_id = 1, $otp = null)
    {
        $CI =& get_instance();

        if (empty($token)) {
            return 'invalid_token';
        }

        // Cari token di database
        $CI->db->select('ut.*, ud.id as user_id, ud.username, ud.email, ud.fullname, ud.position_name, ud.gid, ud.unit_id, ud.google_email, ud.google_id, ud.banned, ud.active');
        $CI->db->from('users_remember_tokens ut');
        $CI->db->join('users_data ud', 'ut.user_id = ud.id');
		if($tipe_id == 2){
			
			if (empty($otp)) {
				return 'invalid_otp';
			}
		
			$CI->db->where('ut.otp', $otp);
		}
		
        $CI->db->where('ut.token', $token);
        $CI->db->where('ut.expires_at >', date('Y-m-d H:i:s'));
        $CI->db->where('ud.active', 1);
        $query  = $CI->db->get();
        $result = $query->row();

        if (!$result) {
            return 'token_expired';
        }

        // Cek apakah user di-banned
        if ($result->banned == 1) {
            mark_token_as_used($result->token);
            return 'banned';
        }

        // Tandai token sudah digunakan
        mark_token_as_used($result->token);

        // Regenerate session
        $CI->session->sess_regenerate(TRUE);

        // Update last login
        $data = [
            'last_login' => date('Y-m-d H:i:s'),
            'validate'   => 1
        ];
        $CI->db->where('id', $result->user_id);
        $CI->db->update('users_data', $data);

        // Set session login
        $login = [
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
            'login_via'     => $login_via
        ];
        $CI->session->set_userdata($login);

        // Set cookie CSRF
        $domain = $_SERVER['HTTP_HOST'];
        $parts  = explode('.', $domain);
        $domain = implode('.', array_slice($parts, count($parts) - 2));

        $my_cookie = [
            'name'   => 'csrf_cookie_pins_filemanager',
            'value'  => sha1('csrf_cookie_pins_filemanager' . date('Y-m-d H:i:s')),
            'expire' => 3000,
            'domain' => $domain
        ];
        $CI->input->set_cookie($my_cookie);

        // Kirim notifikasi inbox
        send_inbox_notif($result->user_id, 100, ucfirst($login_via));

        return 'success';
    }
}

/**
 * Tandai token sudah digunakan
 */
if (!function_exists('mark_token_as_used')) {
    function mark_token_as_used($token)
    {
		return false;
		die();
        $CI =& get_instance();
        $data = [
            //'is_used' => 1,
            'expires_at' => date('Y-m-d H:i:s')
        ];
        $CI->db->where('token', $token);
        $CI->db->update('users_remember_tokens', $data);
    }
}

/**
 * Kirim notifikasi inbox setelah login
 */
if (!function_exists('send_inbox_notif')) {
    function send_inbox_notif($to_id, $type_id = 100, $type_login = 'Web Browser')
    {
        $CI =& get_instance();

        $from_id  = 1;
		
        // Format sesuai kebutuhan (misalnya pakai format_indonesian_datetime custom)
        if (function_exists('format_indonesian_datetime')) {
            $login_time = format_indonesian_datetime();
        } else {
			// Clone ke WIB (Asia/Jakarta)
			// Buat DateTime dari UTC sekarang
			$dtUtc = new DateTime('now', new DateTimeZone('UTC'));
			$dtWib = clone $dtUtc;
			$dtWib->setTimezone(new DateTimeZone('Asia/Jakarta'));
            $login_time = $dtWib->format('d-m-Y H:i:s');
        }

        $username = $CI->ortyd->select2_getname($to_id, 'users_data', 'id', 'fullname') ?? 'User';

        $subject = "Login Berhasil - {$username} melalui {$type_login}";
        $message = "Halo {$username},\nAnda berhasil login pada {$login_time} menggunakan {$type_login}.\n"
                 . "Jika ini bukan Anda segera ganti password atau hubungi admin.";

        $status   = 1;
        $priority = 0;

        return $CI->ortyd->setInbox($from_id, $to_id, $type_id, $subject, $message, $status, $priority);
    }
}



if (!function_exists('format_indonesian_datetime')) {
    /**
     * Format tanggal & waktu ke format Indonesia
     * Contoh: "Selasa, 10 September 2025 13:45 WIB"
     *
     * @param string|null $datetime (default: sekarang UTC)
     * @return string
     */
    function format_indonesian_datetime($datetime = null)
    {
        $days   = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
        $months = [
            1 => 'Januari','Februari','Maret','April','Mei','Juni',
            'Juli','Agustus','September','Oktober','November','Desember'
        ];

        // Buat object DateTime UTC atau dari input
        $dt = $datetime
            ? new DateTime($datetime, new DateTimeZone('UTC'))
            : new DateTime('now', new DateTimeZone('UTC'));

        // Ubah ke WIB (Asia/Jakarta)
        $dt->setTimezone(new DateTimeZone('Asia/Jakarta'));

        $day_name   = $days[(int)$dt->format('w')];
        $day_number = $dt->format('j');
        $month_name = $months[(int)$dt->format('n')];
        $year       = $dt->format('Y');
        $time       = $dt->format('H:i');

        return "{$day_name}, {$day_number} {$month_name} {$year} {$time} WIB";
    }
}



if (!function_exists('generate_wa_login_token')) {
    /**
     * Generate token login untuk WhatsApp
     *
     * @param int         $user_id
     * @param string      $wa_id        Nomor WhatsApp (misalnya 628123xxx)
     * @param string|int  $expired      Durasi expired token. Bisa string relatif ('+10 minutes', '+1 hour')
     *                                  atau integer detik (contoh: 600 untuk 10 menit)
     * @return string|false
     */
	 
	 //$this->load->helper('auth');

	// Token expired default 10 menit
	//$token = generate_wa_login_token($user_id, $wa_id);

	// Token expired 1 jam
	//$token = generate_wa_login_token($user_id, $wa_id, '+1 hour');

	// Token expired 5 menit (pakai detik)
	//$token = generate_wa_login_token($user_id, $wa_id, 300);

	function generate_wa_login_token($user_id, $wa_id, $expired = '+10 minutes', $tipe_id = 1, $otp = null)
	{
		$CI =& get_instance();

		// Prefix token khusus WhatsApp
		$prefix = 'wa_';

		// Generate token random + hash dari wa_id
		$random  = bin2hex(random_bytes(16)); // 32 karakter random
		$waHash  = substr(hash('sha256', $wa_id), 0, 16); // hash pendek dari nomor WA
		$token   = $prefix . $random . $waHash;

		// Hitung waktu expired
		if (is_numeric($expired)) {
			$expires_at = date('Y-m-d H:i:s', time() + (int)$expired);
		} else {
			$expires_at = date('Y-m-d H:i:s', strtotime($expired));
		}

		// Cek apakah token sudah ada untuk user ini
		$CI->db->select('id');
		$CI->db->where('user_id', $user_id);
		$CI->db->where('expires_at >', date('Y-m-d H:i:s'));
		$exists = $CI->db->get('users_remember_tokens')->row();

		// Default data
		$data = [
			'user_id'    => $user_id,
			'token'      => $token,
			'expires_at' => $expires_at,
			'created_at' => date('Y-m-d H:i:s'),
			'tipe_id'    => $tipe_id,
		];

		// Jika tipe OTP (2), buat OTP random 6 digit jika belum ada
		if ($tipe_id == 2) {
			if (empty($otp)) {
				$otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT); 
			}
			$data['otp'] = $otp;
		}

		if ($exists) {
			// Update token lama
			$CI->db->where('id', $exists->id);
			$update = $CI->db->update('users_remember_tokens', $data);
			return $update ? ['token' => $token, 'otp' => $otp] : false;
		} else {
			// Insert token baru
			$insert = $CI->db->insert('users_remember_tokens', $data);
			return $insert ? ['token' => $token, 'otp' => $otp] : false;
		}
	}


}
