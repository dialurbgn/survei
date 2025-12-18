<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rabbit extends MX_Controller {
	 private $queue;
	public function __construct()
	{
		parent::__construct();
		$this->load->library('rabbitmq');
		$this->load->config('rabbitmq');
		$this->load->library('telegram');
		$this->queue = $this->config->item('rabbitmq_queue');
	}

	public function index()
	{
		echo "RabbitMQ Worker Ready.";
	}

	public function send()
	{
		$pesan = $this->ortyd->setInbox(1, 1, 1, 'Testing Email', 'Testing Email', 1, 0);
		$this->ortyd->generateInvoice(18);
		echo "Pesan berhasil dikirim ke antrian! " . $pesan;
	}
	
	public function setwebhook()
	{
		//https://api.telegram.org/bot7815329896:AAG3FhDMaFWv7xIKvwpfwSLwEvfn_eUnKLc/deleteWebhook
		//https://bk-simpktn.kemendag.go.id/rabbit/setwebhook
		$this->load->library('telegram');
		$url = base_url('rabbit/webhook');
		$result = $this->telegram->set_webhook($url);

		echo '<pre>';
		print_r($result);
		echo '</pre>';
	}
		
	public function webhook()
	{
		// Ambil raw input JSON dari Telegram
		$update = $this->telegram->get_webhook_data();

		if (!$update) {
			http_response_code(200);
			exit();
		}

		$message = $this->telegram->extract_message($update);
		$chat_id = $message['chat']['id'] ?? null;
		$text    = trim($message['text'] ?? '');

		if (!$chat_id) {
			http_response_code(200);
			exit();
		}

		$reply = '';
		$keyboard = [
			[
				['text' => 'Register Akun', 'url' => base_url('register')],
				['text' => 'Manual Update Profil', 'url' => base_url('users_profile/view')]
			]
		];

		if (stripos($text, '/start') === 0) {
			// Pisahkan perintah dan argumen
			$parts = explode(' ', $text, 2);
			$arg = isset($parts[1]) ? trim($parts[1]) : null;

			if ($arg === 'checkid') {
				// Perintah khusus untuk cek Chat ID
				$reply = "ℹ️ Chat ID kamu saat ini adalah: <code>{$chat_id}</code>";
				$keyboard = null; // Tidak perlu tombol
			}elseif ($arg === 'login') {
				// 🚀 AUTO LOGIN - Langsung jalankan perintah /login
				$login_response = $this->_handleLoginCommand($chat_id);
				$reply = $login_response['message'];
				$keyboard = $login_response['keyboard'] ?? null;
			}elseif ($arg) {
				// Anggap argumen adalah email
				$email = $arg;
				$this->db->where('email', $email);
				$this->db->update('users_data', ['telegram_chat_id' => $chat_id]);

				if ($this->db->affected_rows() > 0) {
					$reply = "✅ Chat ID kamu berhasil disimpan!\n\n" .
							 "Email: <b>{$email}</b>\nChat ID: <code>{$chat_id}</code>\n\n" .
							 "ℹ️ Sekarang anda sudah dapat menerima notifikasi lewat Telegram.\n\n".
							 "📋 <b>Perintah yang tersedia:</b>\n" .
							 "<code>/start email@example.com</code> → untuk register chat_id\n" .
							 "<code>/start checkid</code> → untuk melihat chat_id kamu saat ini\n" .
							 "<code>/login</code> → untuk mendapatkan link login\n\n" .
							 "📌 <b>Solusi:</b> Silahkan masuk ke menu Profil Anda lalu update Chat ID Telegram secara manual.";
					$keyboard = null; // Tidak perlu tombol
				} else {
					// Error message dulu, baru guide di bawahnya
					$reply = "⚠️ Email <b>{$email}</b> tidak ditemukan di sistem atau email sudah terdaftar dengan Chat ID lain atau Telegram ID anda belum teregister di sistem kami. Silahkan Register Terlebih Dahulu.\n\n" .
							 "─────────────────────────\n\n" .
							 "📋 <b>Perintah yang tersedia:</b>\n" .
							 "<code>/start email@example.com</code> → untuk register chat_id\n" .
							 "<code>/start checkid</code> → untuk melihat chat_id kamu saat ini\n" .
							 "<code>/login</code> → untuk mendapatkan link login\n\n" .
							 "📌 <b>Solusi:</b> Silahkan masuk ke menu Profil Anda lalu update Chat ID Telegram secara manual.";
							 
				}
			} else {
				// /start tanpa argumen → tampilkan instruksi lama + Chat ID
				$first_name = $message['from']['first_name'] ?? '';
				$reply = "Halo <b>{$first_name}</b>!\n\n" .
						 "ℹ️ Chat ID kamu saat ini adalah: <code>{$chat_id}</code>\n\n" .
						 "Gunakan perintah:\n" .
						 "<code>/start email@example.com</code> → untuk register chat_id\n" .
						 "<code>/start checkid</code> → untuk melihat chat_id kamu saat ini\n" .
						 "<code>/login</code> → untuk mendapatkan link login\n\n" .
						 "📌 Jika email tidak ditemukan atau email sudah terdaftar dengan Chat ID lain, silahkan masuk ke menu Profil Anda lalu update Chat ID Telegram secara manual. Apabila belum punya akun silahkan register terlebih dahulu.";
			}
		} elseif (stripos($text, '/login') === 0 || strtolower(trim($text)) === 'login') {
			// Handle perintah login
			$login_response = $this->_handleLoginCommand($chat_id);
			$reply = $login_response['message'];
			$keyboard = $login_response['keyboard'] ?? null;
		} else {
			// Semua input lain → tampilkan instruksi seperti /start
			$first_name = $message['from']['first_name'] ?? '';
			$reply = "Halo <b>{$first_name}</b>!\n\n" .
					 "ℹ️ Chat ID kamu saat ini adalah: <code>{$chat_id}</code>\n\n" .
					 "Gunakan perintah:\n" .
					 "<code>/start email@example.com</code> → untuk register chat_id\n" .
					 "<code>/start checkid</code> → untuk melihat chat_id kamu saat ini\n" .
					 "<code>/login</code> → untuk mendapatkan link login\n\n" .
					 "📌 Jika email tidak ditemukan atau email sudah terdaftar dengan Chat ID lain, silahkan masuk ke menu Profil Anda lalu update Chat ID Telegram secara manual. Apabila belum punya akun silahkan register terlebih dahulu.";
		}

		// Kirim balasan ke Telegram
		if ($keyboard) {
			$this->telegram->send_inline_keyboard($chat_id, $reply, $keyboard, ['parse_mode' => 'HTML']);
		} else {
			$this->telegram->send_message($chat_id, $reply, ['parse_mode' => 'HTML']);
		}

		// WAJIB: selalu balas 200 OK agar Telegram tidak retry
		http_response_code(200);
		exit();
	}
	
	/**
	 * Handle perintah login dari Telegram
	 */
	private function _handleLoginCommand($chat_id)
	{
		// Cek apakah chat_id sudah terdaftar di database
		$this->db->select('id, email, username, fullname');
		$this->db->where('telegram_chat_id', $chat_id);
		$this->db->where('active', 1);
		$this->db->where('banned', 0);
		$query = $this->db->get('users_data');
		$users = $query->result(); // Gunakan result() bukan row()
		
		if (!$users || count($users) == 0) {
			// Chat ID belum terdaftar
			return [
				'message' => "❌ Chat ID Anda belum terdaftar di sistem!\n\n" .
							"📝 Silahkan daftarkan chat ID terlebih dahulu dengan perintah:\n" .
							"<code>/start email@anda.com</code>\n\n" .
							"Atau update manual melalui menu profil di website. Apabila belum punya akun silahkan register terlebih dahulu.",
				'keyboard' => [
					[
						['text' => 'Register Akun', 'url' => base_url('register')],
						['text' => 'Manual Update Profil', 'url' => base_url('users_profile/view')]
					]
				]
			];
		}
		
		// Jika hanya 1 user, langsung proses
		if (count($users) == 1) {
			$user = $users[0];
			
			// Generate token login untuk Telegram
			$login_token = $this->_generateTelegramLoginToken($user->id, $chat_id);
			
			if (!$login_token) {
				return [
					'message' => "❌ Gagal membuat token login. Silahkan coba lagi nanti.",
					'keyboard' => null
				];
			}
			
			// Buat URL login
			$login_url = base_url()."login?telegram=" . $login_token;
			
			return [
				'message' => "🔐 <b>Login via Telegram</b>\n\n" .
							"👤 User: <b>{$user->fullname}</b>\n" .
							"📧 Email: <code>{$user->email}</code>\n\n" .
							"🔗 Klik tombol di bawah untuk login:\n\n" .
							"⚠️ <i>Token berlaku selama 10 menit</i>",
				'keyboard' => [
					[
						['text' => '🚀 Login Sekarang', 'url' => $login_url]
					]
				]
			];
		}
		
		// Jika lebih dari 1 user, tampilkan pilihan
		$message = "🔐 <b>Pilih Akun untuk Login</b>\n\n" .
				   "📱 Chat ID ini terdaftar untuk beberapa akun:\n\n";
		
		$keyboard = [];
		$counter = 1;
		
		foreach ($users as $user) {
			$message .= "{$counter}. <b>{$user->fullname}</b>\n";
			$message .= "   📧 <code>{$user->email}</code>\n\n";
			
			// Generate token untuk setiap user
			$login_token = $this->_generateTelegramLoginToken($user->id, $chat_id);
			
			if ($login_token) {
				$login_url = base_url()."login?telegram=" . $login_token;
				$keyboard[] = [
					['text' => "🚀 Login sebagai {$user->fullname}", 'url' => $login_url]
				];
			}
			
			$counter++;
		}
		
		$message .= "⚠️ <i>Semua token berlaku selama 10 menit</i>";
		
		// Tambahkan tombol manual update jika diperlukan
		$keyboard[] = [
			['text' => '⚙️ Manual Update Profil', 'url' => base_url('users_profile/view')]
		];
		
		return [
			'message' => $message,
			'keyboard' => $keyboard
		];
	}

	/**
	 * Generate token login untuk Telegram
	 */
	private function _generateTelegramLoginToken($user_id, $chat_id)
	{
		// Generate random token
		$token = bin2hex(random_bytes(32));
		$expires_at = date('Y-m-d H:i:s', strtotime('+10 minutes'));

		// Hapus token lama untuk user ini (jika ada)
		$this->db->where('user_id', $user_id);
		//$this->db->where('type', 'telegram_login');
		$this->db->delete('users_remember_tokens');

		// Insert token baru
		$data = [
			'user_id' => $user_id,
			'token' => $token,
			//'type' => 'telegram_login',
			//'chat_id' => $chat_id,
			'expires_at' => $expires_at,
			'created_at' => date('Y-m-d H:i:s'),
			//'is_used' => 0
		];

		$insert = $this->db->insert('users_remember_tokens', $data);
		if($insert){
			return $insert ? $token : false;
		}
		
		return null;
		
	}

	public function process()
	{
		log_message('info', 'RabbitMQ Worker Dimulai');
		$this->rabbitmq->consume(function ($data) {
			$payload = is_string($data) ? json_decode($data, true) : $data;
			
			if($payload == null || $payload == '' || $payload == 'null' || $payload == '-'){
				return false;
			}
			
			log_message('info', 'Pesan diterima: ' . json_encode($payload));
			
			$tipe = $payload['tipe'] ?? 1;
			$start_time = microtime(true);
			
			// Insert log awal ke database
			$log_id = $this->_insertRabbitLog($payload, $tipe, 'received');
			
			try {
				// Update status menjadi processing
				$this->_updateRabbitLog($log_id, 'processing');
				
				switch ($tipe) {
					case 1:
						$this->_processEmail($payload);
						break;
						
					case 2:
						$this->_processEmail($payload);
						break;
					case 4:
						$this->_processInvoice($payload);
						break;
					case 5:
						$this->_processPdfGeneration($payload);
						break;
					case 6:
						$this->_processTelegram($payload);
						break;
					default:
						log_message('info', 'Tipe pesan tidak dikenali: ' . json_encode($payload));
						$this->_updateRabbitLog($log_id, 'failed', 'Tipe pesan tidak dikenali: ' . $tipe);
						return;
				}
				
				// Hitung waktu pemrosesan
				$processing_time = microtime(true) - $start_time;
				
				// Update status menjadi success
				$this->_updateRabbitLog($log_id, 'success', null, $processing_time);
				
			} catch (\Exception $e) {
				$processing_time = microtime(true) - $start_time;
				$error_message = 'Error saat memproses pesan: ' . $e->getMessage();
				
				log_message('error', $error_message);
				
				// Update status menjadi failed dan increment retry count
				$this->_updateRabbitLog($log_id, 'failed', $error_message, $processing_time, true);
				
				// Publish ulang ke queue
				$this->rabbitmq->publish($payload);
			}
		});
	}

	/**
	 * Insert log baru ke tabel rabbit_log
	 */
	private function _insertRabbitLog($payload, $message_type, $status = 'received')
	{
		$data = [
			'queue_name' => $this->queue ?? 'ci_queue',
			'message_type' => $message_type,
			'payload' => json_encode($payload),
			'status' => $status,
			'created_at' => date('Y-m-d H:i:s')
		];
		
		$this->db->insert('rabbit_log', $data);
		return $this->db->insert_id();
	}

	/**
	 * Update log di tabel rabbit_log
	 */
	private function _updateRabbitLog($log_id, $status, $error_message = null, $processing_time = null, $increment_retry = false)
	{
		$data = [
			'status' => $status,
			'updated_at' => date('Y-m-d H:i:s')
		];
		
		if ($error_message !== null) {
			$data['error_message'] = $error_message;
		}
		
		if ($processing_time !== null) {
			$data['processing_time'] = round($processing_time, 4);
		}
		
		if ($status === 'success') {
			$data['processed_at'] = date('Y-m-d H:i:s');
		}
		
		if ($increment_retry) {
			$this->db->set('retry_count', 'retry_count + 1', FALSE);
		}
		
		$this->db->where('id', $log_id);
		$this->db->update('rabbit_log', $data);
	}

	/**
	 * Mendapatkan statistik log RabbitMQ
	 */
	public function getRabbitLogStats($date_from = null, $date_to = null)
	{
		if ($date_from) {
			$this->db->where('created_at >=', $date_from);
		}
		if ($date_to) {
			$this->db->where('created_at <=', $date_to);
		}
		
		$this->db->select('
			status,
			message_type,
			COUNT(*) as total,
			AVG(processing_time) as avg_processing_time,
			MAX(processing_time) as max_processing_time,
			SUM(retry_count) as total_retries
		');
		
		$this->db->group_by(['status', 'message_type']);
		$query = $this->db->get('rabbit_log');
		
		return $query->result_array();
	}

	private function _processEmail($payload)
	{
		$email      = $payload['email'] ?? null;
		$fullname   = $payload['fullname'] ?? null;
		$subject    = $payload['subject'] ?? null;
		$message    = $payload['message'] ?? null;
		$attachment = $payload['attachment'] ?? null;
		$inbox_id   = $payload['inbox_id'] ?? null;

		if (!$email || !$subject || !$message) {
			log_message('error', 'Data email tidak lengkap: ' . json_encode($payload));
			return;
		}

		$success = $this->ortyd->sendEmail($email, $fullname, $subject, $message, $attachment);
		if ($success) {
			$dataUpdate = [
				'email_date' => date('Y-m-d H:i:s'),
				'modifiedid' => 1,
				'modified'   => date('Y-m-d H:i:s')
			];
			$this->db->where('data_inbox.id', $inbox_id);
			if ($this->db->update('data_inbox', $dataUpdate)) {
				log_message('info', 'Email berhasil dikirim dan DB ter-update.');
			}
		} else {
			log_message('error', 'Email gagal dikirim, melakukan requeue.');
			$this->rabbitmq->publish($payload);
		}
	}
	
	private function _processTelegram($payload)
	{
		$email      = $payload['email'] ?? null;
		$fullname   = $payload['fullname'] ?? null;
		$subject    = $payload['subject'] ?? null;
		$message    = $payload['message'] ?? null;
		$attachment = $payload['attachment'] ?? null;
		$inbox_id   = $payload['inbox_id'] ?? null;

		$chat_id_data = $this->ortyd->select2_getname($email,'users_data','email','telegram_chat_id');

		if (!$email || !$subject || !$message || $chat_id_data == null || $chat_id_data == '-' || $chat_id_data == '' || !$chat_id_data) {
			log_message('error', 'Data Telegram tidak lengkap: ' . json_encode($payload));
			return;
		}

		// Konversi HTML ke format Telegram
		$message = str_ireplace(['<br>', '<br/>', '<br />'], "\n", $message);

		// Hanya izinkan tag HTML yang didukung Telegram
		$allowed_tags = '<b><i><u><s><a><code><pre>';
		$message = strip_tags($message, $allowed_tags);

		// Kirim ke Telegram dengan parse_mode HTML
		$success = $this->ortyd->send_telegram($chat_id_data, $message, 'HTML');

		if ($success['status'] == 'success') {
			$dataUpdate = [
				//'email_date' => date('Y-m-d H:i:s'),
				'modifiedid' => 1,
				'modified'   => date('Y-m-d H:i:s')
			];
			$this->db->where('data_inbox.id', $inbox_id);
			if ($this->db->update('data_inbox', $dataUpdate)) {
				log_message('info', 'Telegram berhasil dikirim dan DB ter-update.');
			}
		} else {
			log_message('error', 'Telegram gagal dikirim, melakukan requeue.');
			$this->rabbitmq->publish($payload);
		}
	}


	private function _processInvoice($payload)
	{
		$invoice_id = $payload['invoice_id'] ?? null;
		if (!$invoice_id) {
			log_message('error', 'invoice_id tidak tersedia dalam payload.');
			return;
		}

		$this->ortyd->setInbox(1, 1, 1, 'Testing Email ' . $invoice_id, 'Testing Email', 1, 0);
		$this->ortyd->generateInvoice($invoice_id);
		log_message('info', 'Invoice berhasil dibuat.');
	}

	private function _processPdfGeneration($payload)
	{
		$requiredFields = ['html', 'ext', 'nama', 'name', 'tipe_nama', 'paper', 'orientation', 'stream', 'output'];
		foreach ($requiredFields as $field) {
			if (!isset($payload[$field])) {
				log_message('error', "Field PDF `$field` tidak tersedia: " . json_encode($payload));
				return;
			}
		}

		$linkdata = $this->ortyd->savePDFData(
			$payload['html'],
			$payload['ext'],
			$payload['nama'],
			$payload['name'],
			$payload['tipe_nama'],
			$payload['paper'],
			$payload['orientation'],
			$payload['stream'],
			$payload['output']
		);

		if ($linkdata === false) {
			log_message('error', 'Gagal Membuat PDF: ' . json_encode($payload));
		} else {
			log_message('info', 'PDF berhasil dibuat.');
		}
	}
}
