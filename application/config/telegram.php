<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Telegram Bot Configuration
|--------------------------------------------------------------------------
|
| Konfigurasi untuk Telegram Bot API
| 
| Cara mendapatkan Bot Token:
| 1. Chat dengan @BotFather di Telegram
| 2. Ketik /newbot dan ikuti instruksi
| 3. Copy token yang diberikan
|
| Cara mendapatkan Chat ID:
| 1. Kirim pesan ke bot Anda
| 2. Buka: https://api.telegram.org/bot8203623395:AAGGedsLTgV6jrg70LpByxT7aQr4PaHfAj0/getUpdates
| 3. Lihat "chat":{"id": CHAT_ID}
|
*/

$config['telegram'] = array(
    // Bot Token dari @BotFather
    'bot_token' => telegram_token,
    
    // Default Chat ID (opsional)
    'default_chat_id' => '338325624',
    
    // Timeout untuk cURL request (detik)
    'timeout' => 30,
    
    // Parse mode default ('HTML', 'Markdown', atau '')
    'default_parse_mode' => 'HTML'
);

/* End of file telegram.php */
/* Location: ./application/config/telegram.php */