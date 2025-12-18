<?php
require(APPPATH . '/libraries/REST_Controller.php');
require(APPPATH . '/libraries/simple_html_dom.php');

class Auth extends REST_Controller
{
    private $modeldb = 'm_api_auth';

    function __construct()
    {
        parent::__construct();

        header("Access-Control-Allow-Origin: *");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Allow-Headers: Origin, Content-Type, Authorization');
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        header('Access-Control-Max-Age: 86400');

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            die();
        }

        $this->load->model($this->modeldb);
    }

	// Fungsi untuk menyimpan data login SSO
    public function addUser_post()
	{
		// Ambil data dari request POST
		$json_contents = json_decode(file_get_contents('php://input'), true);

		// Validasi data yang diterima
		$email_sso = $json_contents['email'] ?? '';
		$fullname = $json_contents['fullname'] ?? '';
		$username = $json_contents['username'] ?? '';
		
		if (empty($email_sso) || empty($username)) {
			return $this->response([
				'status' => 'error',
				'message' => 'Email dan Username wajib diisi'
			], 400);
		}

		// Periksa apakah pengguna sudah ada di database berdasarkan email
		$existing_user = $this->m_api_auth->get_by_email($email_sso);

		if (!$existing_user) {
			// Pengguna baru, insert ke database
			$data = [
				'fullname'        => $fullname,
				'username'        => $username,
				'email'           => $email_sso,
				'password'        => '', // Password dapat disesuaikan atau dibiarkan kosong jika tidak digunakan
				'gid'             => 3, // Asumsi grup default, sesuaikan jika perlu
				'created'         => date('Y-m-d H:i:s'),
				'modified'        => date('Y-m-d H:i:s'),
				'active'          => 1, // Status aktif
				'banned'          => 0, // Status tidak dibanned
				'validate'         => 1,
			];

			// Simpan data pengguna baru
			$this->db->insert('users_data', $data);
			$user_id = $this->db->insert_id();

			// Jika data berhasil disimpan, lakukan login atau operasional lain
			// Misalnya mengirimkan respon sukses dengan data pengguna
			return $this->response([
				'status' => 'success',
				'message' => 'Pengguna baru berhasil ditambahkan',
				'data' => $data
			], 200);
		} else {
			// Pengguna sudah ada, lakukan update jika diperlukan
			return $this->response([
				'status' => 'error',
				'message' => 'Pengguna dengan email ini sudah ada'
			], 400);
		}
	}


    public function getLogin_post()
    {
        header("Content-type:application/json");
        $json = file_get_contents('php://input');
        $json_contents = json_decode($json, true);

        $username = $json_contents['username'] ?? '';
        $password = $json_contents['password'] ?? '';

        if (!empty($username) && !empty($password)) {
            $data = $this->m_api_auth->getLogin($username, $password, $json_contents);
            if ($data['status'] !== 'error') {
                $token_data = $data['data'];
                $token = $this->authorization_token->generateToken($token_data);
                $data['token'] = $token;
                return $this->response($data, 200);
            }
            return $this->response(['status' => 'error', 'message' => 'Login Error'], 401);
        }
        return $this->response(['status' => 'error', 'message' => 'Invalid input'], 401);
    }

    public function getProfile_post()
    {
        header("Content-type:application/json");
        $json = file_get_contents('php://input');
        $json_contents = json_decode($json, true);
        $headers = $this->input->request_headers();

        $app_tipe = $json_contents['app_tipe'] ?? null;
        $app_version = $json_contents['app_version'] ?? null;

        if (!isset($headers['Authorization'])) {
            return $this->response(['status' => 'error', 'message' => 'Token Error'], 400);
        }

        $decodedToken = $this->authorization_token->validateToken($headers['Authorization']);

        if (!$decodedToken['status']) {
            return $this->response(['status' => 'error', 'message' => $decodedToken['message']], 401);
        }

        $username = $decodedToken['data']->username ?? '';

        if (!empty($username)) {
            $data = $this->m_api_auth->getProfile($username, $app_tipe, $app_version);
            if ($data['status'] !== 'error') {
                return $this->response($data, 200);
            }
            return $this->response(['status' => 'error', 'message' => 'Profile Error'], 401);
        }

        return $this->response(['status' => 'error', 'message' => 'Invalid username'], 401);
    }

    public function logout_post()
    {
        header("Content-type:application/json");
        $headers = $this->input->request_headers();

        if (!isset($headers['Authorization'])) {
            return $this->response(['status' => 'error', 'message' => 'Token Error'], 400);
        }

        $decodedToken = $this->authorization_token->validateToken($headers['Authorization']);

        if (!$decodedToken['status']) {
            return $this->response(['status' => 'error', 'message' => $decodedToken['message']], 401);
        }

        $username = $decodedToken['data']->username ?? '';

        if (!empty($username)) {
            $this->db->where('username', $username);
            $this->db->update('users_data', ['notif_id' => 'logout']);

            $data = $this->m_api_auth->getProfile($username);
            if ($data['status'] !== 'error') {
                return $this->response($data, 200);
            }

            return $this->response(['status' => 'error', 'message' => 'Logout error'], 401);
        }

        return $this->response(['status' => 'error', 'message' => 'Invalid username'], 401);
    }

	public function logincus_post()
	{
		header("Content-type:application/json");
		
		$headers = $this->input->request_headers();

		if (!isset($headers['Authorization'])) {
			return $this->response([
				'status' => 'error',
				'message' => 'Access token is missing'
			], 400);
		}

		$decodedToken = $this->authorization_token->validateToken($headers['Authorization']);

		if (!$decodedToken['status']) {
			return $this->response([
				'status' => 'error',
				'message' => $decodedToken['message']
			], 401);
		}

		$userInfo = (array) $decodedToken['data'];

		if (!isset($userInfo['email'])) {
			return $this->response([
				'status' => 'error',
				'message' => 'Invalid token: email not found'
			], 401);
		}

		// Cari atau buat customer
		$customer = $this->m_api_auth->get_by_email($userInfo['email']);

		if (!$customer) {
			$data = [
				'name'           => $userInfo['name'] ?? 'Guest',
				'email'          => $userInfo['email'],
				'avatar'         => $userInfo['avatar'] ?? null,
				'provider_id'    => $userInfo['id'] ?? null,
				'provider_name'  => $userInfo['provider'] ?? 'jwt',
				'created_at'     => date('Y-m-d H:i:s'),
				'updated_at'     => date('Y-m-d H:i:s'),
			];
			$customer_id = $this->m_api_auth->insert($data);
			$customer = $this->m_api_auth->get($customer_id);
		}

		// Simpan ke session jika perlu
		$this->session->set_userdata('customer', $customer);

		return $this->response([
			'status' => 'success',
			'customer' => $customer
		], 200);
	}


}
