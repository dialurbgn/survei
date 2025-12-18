<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Telegram Library untuk CodeIgniter 3
 * 
 * Library untuk mengirim pesan ke Telegram Bot API
 * 
 * @author Your Name
 * @version 1.0
 */
class Telegram {
    
    private $ci;
    private $bot_token;
    private $api_url;
    
    public function __construct($config = array()) {
        $this->ci =& get_instance();
        
        // Load konfigurasi dari config file atau parameter
        if (!empty($config['bot_token'])) {
            $this->bot_token = $config['bot_token'];
        } else {
            // Coba load dari config file
            $this->ci->load->config('telegram', TRUE);
            $telegram_config = $this->ci->config->item('telegram');
            $this->bot_token = telegram_token;
        }
        
        if (empty($this->bot_token)) {
            show_error('Telegram Bot Token tidak ditemukan. Pastikan sudah dikonfigurasi.');
        }
        
        $this->api_url = "https://api.telegram.org/bot" . $this->bot_token . "/";
    }
    
    /**
     * Kirim pesan teks sederhana
     * 
     * @param string $chat_id ID chat atau username channel
     * @param string $message Pesan yang akan dikirim
     * @param array $options Opsi tambahan (parse_mode, disable_web_page_preview, dll)
     * @return array Response dari Telegram API
     */
    public function send_message($chat_id, $message, $options = array()) {
        $data = array(
            'chat_id' => $chat_id,
            'text' => $message
        );
        
        // Merge dengan opsi tambahan
        $data = array_merge($data, $options);
        
        return $this->make_request('sendMessage', $data);
    }
    
    /**
     * Kirim foto
     * 
     * @param string $chat_id ID chat atau username channel
     * @param string $photo URL foto atau file path
     * @param string $caption Caption foto (opsional)
     * @param array $options Opsi tambahan
     * @return array Response dari Telegram API
     */
    public function send_photo($chat_id, $photo, $caption = '', $options = array()) {
        $data = array(
            'chat_id' => $chat_id,
            'photo' => $photo
        );
        
        if (!empty($caption)) {
            $data['caption'] = $caption;
        }
        
        $data = array_merge($data, $options);
        
        return $this->make_request('sendPhoto', $data);
    }
    
    /**
     * Kirim dokumen
     * 
     * @param string $chat_id ID chat atau username channel
     * @param string $document URL dokumen atau file path
     * @param string $caption Caption dokumen (opsional)
     * @param array $options Opsi tambahan
     * @return array Response dari Telegram API
     */
    public function send_document($chat_id, $document, $caption = '', $options = array()) {
        $data = array(
            'chat_id' => $chat_id,
            'document' => $document
        );
        
        if (!empty($caption)) {
            $data['caption'] = $caption;
        }
        
        $data = array_merge($data, $options);
        
        return $this->make_request('sendDocument', $data);
    }
    
    /**
     * Kirim pesan dengan inline keyboard
     * 
     * @param string $chat_id ID chat
     * @param string $message Pesan
     * @param array $keyboard Array inline keyboard
     * @param array $options Opsi tambahan
     * @return array Response dari Telegram API
     */
    public function send_inline_keyboard($chat_id, $message, $keyboard, $options = array()) {
        $data = array(
            'chat_id' => $chat_id,
            'text' => $message,
            'reply_markup' => json_encode(array(
                'inline_keyboard' => $keyboard
            ))
        );
        
        $data = array_merge($data, $options);
        
        return $this->make_request('sendMessage', $data);
    }
    
    /**
     * Edit pesan
     * 
     * @param string $chat_id ID chat
     * @param int $message_id ID pesan yang akan diedit
     * @param string $new_text Teks baru
     * @param array $options Opsi tambahan
     * @return array Response dari Telegram API
     */
    public function edit_message($chat_id, $message_id, $new_text, $options = array()) {
        $data = array(
            'chat_id' => $chat_id,
            'message_id' => $message_id,
            'text' => $new_text
        );
        
        $data = array_merge($data, $options);
        
        return $this->make_request('editMessageText', $data);
    }
    
    /**
     * Hapus pesan
     * 
     * @param string $chat_id ID chat
     * @param int $message_id ID pesan yang akan dihapus
     * @return array Response dari Telegram API
     */
    public function delete_message($chat_id, $message_id) {
        $data = array(
            'chat_id' => $chat_id,
            'message_id' => $message_id
        );
        
        return $this->make_request('deleteMessage', $data);
    }
    
    /**
     * Get info bot
     * 
     * @return array Response dari Telegram API
     */
    public function get_me() {
        return $this->make_request('getMe', array());
    }
    
    /**
     * Get updates (webhook/polling)
     * 
     * @param array $options Opsi untuk getUpdates
     * @return array Response dari Telegram API
     */
    public function get_updates($options = array()) {
        return $this->make_request('getUpdates', $options);
    }
    
    /**
     * Set webhook
     * 
     * @param string $webhook_url URL webhook
     * @param array $options Opsi tambahan
     * @return array Response dari Telegram API
     */
    public function set_webhook($webhook_url, $options = array()) {
        $data = array(
            'url' => $webhook_url
        );
        
        $data = array_merge($data, $options);
        
        return $this->make_request('setWebhook', $data);
    }
    
    /**
     * Buat request ke Telegram API
     * 
     * @param string $method Method API yang akan dipanggil
     * @param array $data Data yang akan dikirim
     * @return array Response dari API
     */
    private function make_request($method, $data) {
        $url = $this->api_url . $method;
        
        // Gunakan cURL untuk request
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        // Decode JSON response
        $result = json_decode($response, true);
        
        // Tambahkan info HTTP code
        if (is_array($result)) {
            $result['http_code'] = $http_code;
        } else {
            $result = array(
                'ok' => false,
                'error_code' => $http_code,
                'description' => 'Invalid response from Telegram API',
                'http_code' => $http_code,
                'raw_response' => $response
            );
        }
        
        return $result;
    }
    
    /**
     * Parse incoming webhook data
     * 
     * @return array|false Parsed webhook data atau false jika gagal
     */
    public function get_webhook_data() {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        
        return $data ? $data : false;
    }
    
    /**
     * Extract message info dari webhook data
     * 
     * @param array $webhook_data Data dari webhook
     * @return array|false Message info atau false jika tidak ada
     */
    public function extract_message($webhook_data) {
        if (isset($webhook_data['message'])) {
            return array(
                'message_id' => $webhook_data['message']['message_id'],
                'from' => $webhook_data['message']['from'],
                'chat' => $webhook_data['message']['chat'],
                'date' => $webhook_data['message']['date'],
                'text' => isset($webhook_data['message']['text']) ? $webhook_data['message']['text'] : '',
                'type' => $this->get_message_type($webhook_data['message'])
            );
        }
        
        return false;
    }
    
    /**
     * Tentukan tipe pesan
     * 
     * @param array $message Message data
     * @return string Tipe pesan
     */
    private function get_message_type($message) {
        if (isset($message['photo'])) return 'photo';
        if (isset($message['document'])) return 'document';
        if (isset($message['video'])) return 'video';
        if (isset($message['audio'])) return 'audio';
        if (isset($message['voice'])) return 'voice';
        if (isset($message['sticker'])) return 'sticker';
        if (isset($message['location'])) return 'location';
        if (isset($message['contact'])) return 'contact';
        
        return 'text';
    }
    
    /**
     * Format pesan dengan HTML
     * 
     * @param string $text Teks pesan
     * @return string Formatted text
     */
    public function format_html($text) {
        return array('parse_mode' => 'HTML', 'text' => $text);
    }
    
    /**
     * Format pesan dengan Markdown
     * 
     * @param string $text Teks pesan
     * @return string Formatted text
     */
    public function format_markdown($text) {
        return array('parse_mode' => 'Markdown', 'text' => $text);
    }
}

/* End of file Telegram.php */
/* Location: ./application/libraries/Telegram.php */