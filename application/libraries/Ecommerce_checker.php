<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ecommerce_checker {
    
    private $CI;
    private $timeout = 30;
    private $max_redirects = 5;
    private $debug_mode = false;
    
    // Konfigurasi platform yang didukung
    private $platforms = [
        'shopee' => [
            'domains' => ['shopee.co.id', 'www.shopee.co.id'],
            'name' => 'Shopee',
            'type' => 'ecommerce'
        ],
        'tokopedia' => [
            'domains' => ['tokopedia.com', 'www.tokopedia.com', 'ta.tokopedia.com'],
            'name' => 'Tokopedia',
            'type' => 'ecommerce'
        ],
        'gofood' => [
            'domains' => ['gofood.co.id', 'www.gofood.co.id', 'gojek.com/gofood'],
            'name' => 'GoFood',
            'type' => 'food_delivery'
        ],
        'lazada' => [
            'domains' => ['lazada.co.id', 'www.lazada.co.id'],
            'name' => 'Lazada',
            'type' => 'ecommerce'
        ],
        'blibli' => [
            'domains' => ['blibli.com', 'www.blibli.com'],
            'name' => 'Blibli',
            'type' => 'ecommerce'
        ],
        'grabfood' => [
            'domains' => ['food.grab.com', 'www.food.grab.com', 'grab.com/id/food'],
            'name' => 'GrabFood',
            'type' => 'food_delivery'
        ],
        'bukalapak' => [
            'domains' => ['bukalapak.com', 'www.bukalapak.com'],
            'name' => 'Bukalapak',
            'type' => 'ecommerce'
        ],
        'simulasiapp' => [
            'domains' => ['simulasiapp.com', 'www.simulasiapp.com'],
            'name' => 'SimulasiApp',
            'type' => 'application'
        ],
        'tst_pmse' => [
            'domains' => ['tst-pmse.com', 'www.tst-pmse.com', 'pmse.tst.go.id'],
            'name' => 'TST PMSE',
            'type' => 'government'
        ],
        'property99' => [
            'domains' => ['99.co', 'www.99.co'],
            'name' => '99.co',
            'type' => 'property'
        ],
        'ujicoba' => [
            'domains' => ['ujicoba.com', 'www.ujicoba.com'],
            'name' => 'UjiCoba',
            'type' => 'testing'
        ],
        'tiktok' => [
            'domains' => ['tiktok.com', 'www.tiktok.com'],
            'name' => 'TikTok',
            'type' => 'social_media'
        ],
        'shopee_food' => [
            'domains' => ['food.shopee.co.id', 'www.food.shopee.co.id'],
            'name' => 'ShopeeFood',
            'type' => 'food_delivery'
        ]
    ];
    
    public function __construct() {
        $this->CI =& get_instance();
    }
    
    /**
     * Set debug mode
     */
    public function set_debug_mode($debug = true) {
        $this->debug_mode = $debug;
    }
    
    /**
     * Cek status halaman dari berbagai platform
     */
    public function check_page_status($url) {
        // Validasi URL
        if (!$this->is_valid_url($url)) {
            return $this->create_error_response('URL tidak valid', $url);
        }
        
        // Deteksi platform
        $platform = $this->detect_platform($url);
        if (!$platform) {
            return $this->create_error_response('Platform tidak didukung', $url);
        }
        
        // Fetch konten halaman
        $result = $this->fetch_page_content($url);
        if (!$result['success']) {
            return $result;
        }
        
        // Analisis konten berdasarkan platform
        $analysis = $this->analyze_content_by_platform($platform, $result['content'], $result['http_code'], $result);
        
        $response = [
            'success' => true,
            'url' => $url,
            'final_url' => isset($result['final_url']) ? $result['final_url'] : $result['url'],
            'platform' => $this->platforms[$platform]['name'],
            'platform_type' => $this->platforms[$platform]['type'],
            'http_code' => $result['http_code'],
            'is_not_found' => $analysis['is_not_found'],
            'status' => $analysis['status'],
            'message' => $analysis['message'],
            'details' => $analysis['details'],
            'checked_at' => date('Y-m-d H:i:s')
        ];
        
        // Tambahkan info debug jika diperlukan
        if ($this->debug_mode) {
            $response['debug'] = [
                'content_length' => strlen($result['content']),
                'content_sample' => substr(strip_tags($result['content']), 0, 500),
                'headers' => isset($result['headers']) ? $result['headers'] : null,
                'redirects' => isset($result['redirects']) ? $result['redirects'] : 0,
                'has_html_structure' => stripos($result['content'], '<html') !== false,
                'has_title_tag' => stripos($result['content'], '<title') !== false
            ];
        }
        
        return $response;
    }
    
    /**
     * Deteksi platform berdasarkan URL
     */
    private function detect_platform($url) {
        $parsed_url = parse_url($url);
        if (!isset($parsed_url['host'])) {
            return false;
        }
        
        $host = strtolower($parsed_url['host']);
        
        foreach ($this->platforms as $platform_key => $platform_config) {
            foreach ($platform_config['domains'] as $domain) {
                if ($host === $domain || strpos($host, $domain) !== false) {
                    return $platform_key;
                }
            }
        }
        
        return false;
    }
    
    /**
     * Validasi URL
     */
    private function is_valid_url($url) {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }
    
    /**
     * Ambil konten halaman
     */
    private function fetch_page_content($url) {
        
        $platform = $this->detect_platform($url);
    
        // Gunakan setting khusus untuk Blibli
        if ($platform === 'blibli') {
            return $this->fetch_blibli_content($url);
        }
    
        $ch = curl_init();
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => $this->max_redirects,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_USERAGENT => $this->get_random_user_agent(),
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HEADER => false,
            CURLOPT_NOBODY => false,
            CURLOPT_ENCODING => 'gzip, deflate',
            CURLOPT_HTTPHEADER => [
                'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
                'Accept-Language: id-ID,id;q=0.9,en-US;q=0.8,en;q=0.7',
                'Accept-Encoding: gzip, deflate, br',
                'Connection: keep-alive',
                'Upgrade-Insecure-Requests: 1',
                'Cache-Control: no-cache',
                'Pragma: no-cache',
                'Sec-Fetch-Dest: document',
                'Sec-Fetch-Mode: navigate',
                'Sec-Fetch-Site: none',
                'Sec-Ch-Ua: "Not_A Brand";v="8", "Chromium";v="120", "Google Chrome";v="120"',
                'Sec-Ch-Ua-Mobile: ?0',
                'Sec-Ch-Ua-Platform: "Windows"'
            ]
        ]);
        
        $content = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        $final_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        $redirect_count = curl_getinfo($ch, CURLINFO_REDIRECT_COUNT);
        
        curl_close($ch);
        
        if ($content === false || !empty($error)) {
            return [
                'success' => false,
                'message' => 'Gagal mengambil konten halaman: ' . $error,
                'http_code' => $http_code,
                'error' => $error
            ];
        }
        
        return [
            'success' => true,
            'content' => $content,
            'http_code' => $http_code,
            'final_url' => $final_url,
            'redirects' => $redirect_count
        ];
    }
    
    
    private function fetch_blibli_content($url) {
        $ch = curl_init();
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => $this->max_redirects,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_USERAGENT => $this->get_random_user_agent(),
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HEADER => false,
            CURLOPT_NOBODY => false,
            // PERBAIKAN: Hapus CURLOPT_ENCODING atau set ke string kosong
            CURLOPT_ENCODING => '', // Biarkan kosong agar curl menentukan sendiri
            CURLOPT_HTTPHEADER => [
                'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
                'Accept-Language: id-ID,id;q=0.9,en-US;q=0.8,en;q=0.7',
                // PERBAIKAN: Ubah Accept-Encoding menjadi lebih sederhana
                'Accept-Encoding: gzip, deflate',
                'Connection: keep-alive',
                'Upgrade-Insecure-Requests: 1',
                'Cache-Control: no-cache',
                'Pragma: no-cache',
                'Sec-Fetch-Dest: document',
                'Sec-Fetch-Mode: navigate',
                'Sec-Fetch-Site: none',
                'Sec-Ch-Ua: "Not_A Brand";v="8", "Chromium";v="120", "Google Chrome";v="120"',
                'Sec-Ch-Ua-Mobile: ?0',
                'Sec-Ch-Ua-Platform: "Windows"'
            ]
        ]);
        
        $content = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        $final_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        $redirect_count = curl_getinfo($ch, CURLINFO_REDIRECT_COUNT);
        
        curl_close($ch);
        
        if ($content === false || !empty($error)) {
            return [
                'success' => false,
                'message' => 'Gagal mengambil konten halaman: ' . $error,
                'http_code' => $http_code,
                'error' => $error
            ];
        }
        
        return [
            'success' => true,
            'content' => $content,
            'http_code' => $http_code,
            'final_url' => $final_url,
            'redirects' => $redirect_count
        ];
    }

    /**
     * Analisis konten berdasarkan platform
     */
    private function analyze_content_by_platform($platform, $content, $http_code, $fetch_result = []) {
        switch ($platform) {
            case 'shopee':
                return $this->analyze_shopee($content, $http_code, $fetch_result);
            case 'tokopedia':
                return $this->analyze_tokopedia($content, $http_code, $fetch_result);
            case 'gofood':
                return $this->analyze_gofood($content, $http_code, $fetch_result);
            case 'lazada':
                return $this->analyze_lazada($content, $http_code, $fetch_result);
            case 'blibli':
                return $this->analyze_blibli($content, $http_code, $fetch_result);
            case 'grabfood':
                return $this->analyze_grabfood($content, $http_code, $fetch_result);
            case 'bukalapak':
                return $this->analyze_bukalapak($content, $http_code, $fetch_result);
            case 'simulasiapp':
                return $this->analyze_simulasiapp($content, $http_code, $fetch_result);
            case 'tst_pmse':
                return $this->analyze_tst_pmse($content, $http_code, $fetch_result);
            case 'property99':
                return $this->analyze_property99($content, $http_code, $fetch_result);
            case 'ujicoba':
                return $this->analyze_ujicoba($content, $http_code, $fetch_result);
            case 'tiktok':
                return $this->analyze_tiktok($content, $http_code, $fetch_result);
            case 'shopee_food':
                return $this->analyze_shopee_food($content, $http_code, $fetch_result);
            default:
                return $this->analyze_generic($content, $http_code, $fetch_result);
        }
    }
    
    /**
     * Analisis Shopee dengan deteksi yang lebih baik
     */
    private function analyze_shopee($content, $http_code, $fetch_result = []) {
        // Deteksi prioritas: halaman dengan HTML structure tapi tanpa title tag
        $has_html = stripos($content, '<html') !== false;
        $has_title_tag = stripos($content, '<title') !== false;
        $has_js_config = stripos($content, 'window.__PAGE_ID__') !== false;
        
        if ($has_html && !$has_title_tag && $has_js_config) {
            return [
                'is_not_found' => true,
                'status' => 'not_found',
                'message' => 'Produk tidak ditemukan - halaman tanpa title',
                'details' => 'Halaman Shopee dimuat dengan HTML structure tapi tanpa title tag'
            ];
        }
        
        // Cek redirect ke halaman error atau homepage
        if (isset($fetch_result['final_url']) && $fetch_result['redirects'] > 0) {
            $final_url = $fetch_result['final_url'];
            if (strpos($final_url, '/product/') === false && 
                (strpos($final_url, 'shopee.co.id/?') !== false || 
                 strpos($final_url, 'shopee.co.id/error') !== false ||
                 $final_url === 'https://shopee.co.id/' ||
                 $final_url === 'https://www.shopee.co.id/')) {
                return [
                    'is_not_found' => true,
                    'status' => 'redirect_to_home',
                    'message' => 'Produk tidak ditemukan - redirect ke homepage',
                    'details' => 'Redirect dari produk ke: ' . $final_url
                ];
            }
        }
        
        // Cek meta tag is404
        if (preg_match('/<meta[^>]*name=["\']is404["\'][^>]*content=["\']true["\'][^>]*>/i', $content)) {
            return [
                'is_not_found' => true,
                'status' => 'not_found',
                'message' => 'Produk tidak ditemukan - meta is404 true',
                'details' => 'Meta tag is404 dengan value true ditemukan'
            ];
        }
        
        // Cek robots noindex
        if (preg_match('/<meta[^>]*name=["\']robots["\'][^>]*content=["\'][^"\']*noindex[^"\']*["\'][^>]*>/i', $content)) {
            return [
                'is_not_found' => true,
                'status' => 'not_found',
                'message' => 'Produk tidak ditemukan - robots noindex',
                'details' => 'Meta robots noindex ditemukan'
            ];
        }
        
        // Cek class product-not-exist
        if (stripos($content, 'product-not-exist') !== false) {
            return [
                'is_not_found' => true,
                'status' => 'not_found',
                'message' => 'Produk tidak ditemukan - class product-not-exist',
                'details' => 'CSS class product-not-exist ditemukan'
            ];
        }
        
        return $this->generic_analysis($content, $http_code, $this->get_not_found_indicators('shopee'), $this->get_valid_indicators('shopee'));
    }
    
    /**
     * Analisis Tokopedia
     */
    private function analyze_tokopedia($content, $http_code, $fetch_result = []) {
        // Cek HTML structure dan title
        $has_html = stripos($content, '<html') !== false;
        $has_title_tag = stripos($content, '<title') !== false;
        
        if ($has_html && !$has_title_tag) {
            return [
                'is_not_found' => true,
                'status' => 'not_found',
                'message' => 'Produk tidak ditemukan - halaman tanpa title',
                'details' => 'Halaman Tokopedia dimuat tanpa title tag'
            ];
        }
        
        // Cek redirect ke halaman error atau homepage
        if (isset($fetch_result['final_url']) && $fetch_result['redirects'] > 0) {
            $final_url = $fetch_result['final_url'];
            if (strpos($final_url, '/p/') === false && 
                (strpos($final_url, 'tokopedia.com/?') !== false || 
                 strpos($final_url, 'tokopedia.com/error') !== false ||
                 $final_url === 'https://www.tokopedia.com/' ||
                 $final_url === 'https://tokopedia.com/')) {
                return [
                    'is_not_found' => true,
                    'status' => 'redirect_to_home',
                    'message' => 'Produk tidak ditemukan - redirect ke homepage',
                    'details' => 'Redirect dari produk ke: ' . $final_url
                ];
            }
        }
        
        // Cek pesan error spesifik Tokopedia
        $tokopedia_error_messages = [
            'Waduh, tujuanmu nggak ada!',
            'Waduh, tujuanmu nggak ada',
            'tujuanmu nggak ada',
            'Mungkin kamu salah jalan atau alamat',
            'Ayo balik sebelum gelap'
        ];
        
        foreach ($tokopedia_error_messages as $error_msg) {
            if (stripos($content, $error_msg) !== false) {
                return [
                    'is_not_found' => true,
                    'status' => 'not_found',
                    'message' => 'Produk tidak ditemukan - halaman error Tokopedia',
                    'details' => 'Ditemukan pesan error: "' . $error_msg . '"'
                ];
            }
        }
        
        // Cek JavaScript status codes
        $js_patterns = [
            '/"statusCode"\s*:\s*(\d+)/',
            '/"status_code"\s*:\s*(\d+)/',
            '/statusCode\s*:\s*(\d+)/'
        ];
        
        foreach ($js_patterns as $pattern) {
            if (preg_match($pattern, $content, $matches)) {
                $js_status_code = (int)$matches[1];
                if ($js_status_code === 410 || $js_status_code === 404) {
                    return [
                        'is_not_found' => true,
                        'status' => $js_status_code === 410 ? 'gone' : 'not_found',
                        'message' => 'Produk tidak ditemukan (' . $js_status_code . ')',
                        'details' => 'JavaScript statusCode: ' . $js_status_code
                    ];
                }
            }
        }
        
        return $this->generic_analysis($content, $http_code, $this->get_not_found_indicators('tokopedia'), $this->get_valid_indicators('tokopedia'));
    }
    
    /**
     * Analisis GoFood
     */
    private function analyze_gofood($content, $http_code, $fetch_result = []) {
        $has_html = stripos($content, '<html') !== false;
        $has_title_tag = stripos($content, '<title') !== false;
        
        if ($has_html && !$has_title_tag) {
            return [
                'is_not_found' => true,
                'status' => 'not_found',
                'message' => 'Restoran/menu tidak ditemukan - halaman tanpa title',
                'details' => 'Halaman GoFood dimuat tanpa title tag'
            ];
        }
        
        return $this->generic_analysis($content, $http_code, $this->get_not_found_indicators('gofood'), $this->get_valid_indicators('gofood'));
    }
    
    /**
     * Analisis Lazada
     */
    private function analyze_lazada($content, $http_code, $fetch_result = []) {
        $has_html = stripos($content, '<html') !== false;
        $has_title_tag = stripos($content, '<title') !== false;
        
        if ($has_html && !$has_title_tag) {
            return [
                'is_not_found' => true,
                'status' => 'not_found',
                'message' => 'Produk tidak ditemukan - halaman tanpa title',
                'details' => 'Halaman Lazada dimuat tanpa title tag'
            ];
        }
        
        return $this->generic_analysis($content, $http_code, $this->get_not_found_indicators('lazada'), $this->get_valid_indicators('lazada'));
    }
    
    /**
     * Analisis Blibli
     */
    private function analyze_blibli($content, $http_code, $fetch_result = []) {
    // Handle HTTP 403 khusus untuk Blibli
    if ($http_code == 403) {
        return [
            'is_not_found' => true,
            'status' => 'access_denied',
            'message' => 'Produk tidak dapat diakses - akses ditolak',
            'details' => 'Blibli mengembalikan HTTP 403 Forbidden'
        ];
    }
    
    // Lanjutkan dengan analisis normal...
    $has_html = stripos($content, '<html') !== false;
    $has_title_tag = stripos($content, '<title') !== false;
    
    if ($has_html && !$has_title_tag) {
        return [
            'is_not_found' => true,
            'status' => 'not_found',
            'message' => 'Produk tidak ditemukan - halaman tanpa title',
            'details' => 'Halaman Blibli dimuat tanpa title tag'
        ];
    }
    
    return $this->generic_analysis($content, $http_code, $this->get_not_found_indicators('blibli'), $this->get_valid_indicators('blibli'));
}
    
    /**
     * Analisis GrabFood
     */
    private function analyze_grabfood($content, $http_code, $fetch_result = []) {
        $has_html = stripos($content, '<html') !== false;
        $has_title_tag = stripos($content, '<title') !== false;
        
        if ($has_html && !$has_title_tag) {
            return [
                'is_not_found' => true,
                'status' => 'not_found',
                'message' => 'Restoran/menu tidak ditemukan - halaman tanpa title',
                'details' => 'Halaman GrabFood dimuat tanpa title tag'
            ];
        }
        
        return $this->generic_analysis($content, $http_code, $this->get_not_found_indicators('grabfood'), $this->get_valid_indicators('grabfood'));
    }
    
    /**
     * Analisis Bukalapak
     */
    private function analyze_bukalapak($content, $http_code, $fetch_result = []) {
        $has_html = stripos($content, '<html') !== false;
        $has_title_tag = stripos($content, '<title') !== false;
        
        if ($has_html && !$has_title_tag) {
            return [
                'is_not_found' => true,
                'status' => 'not_found',
                'message' => 'Produk tidak ditemukan - halaman tanpa title',
                'details' => 'Halaman Bukalapak dimuat tanpa title tag'
            ];
        }
        
        return $this->generic_analysis($content, $http_code, $this->get_not_found_indicators('bukalapak'), $this->get_valid_indicators('bukalapak'));
    }
    
    /**
     * Analisis SimulasiApp
     */
    private function analyze_simulasiapp($content, $http_code, $fetch_result = []) {
        $has_html = stripos($content, '<html') !== false;
        $has_title_tag = stripos($content, '<title') !== false;
        
        if ($has_html && !$has_title_tag) {
            return [
                'is_not_found' => true,
                'status' => 'not_found',
                'message' => 'Halaman tidak ditemukan - halaman tanpa title',
                'details' => 'Halaman SimulasiApp dimuat tanpa title tag'
            ];
        }
        
        return $this->generic_analysis($content, $http_code, $this->get_not_found_indicators('simulasiapp'), $this->get_valid_indicators('simulasiapp'));
    }
    
    /**
     * Analisis TST PMSE
     */
    private function analyze_tst_pmse($content, $http_code, $fetch_result = []) {
        $has_html = stripos($content, '<html') !== false;
        $has_title_tag = stripos($content, '<title') !== false;
        
        if ($has_html && !$has_title_tag) {
            return [
                'is_not_found' => true,
                'status' => 'not_found',
                'message' => 'Halaman tidak ditemukan - halaman tanpa title',
                'details' => 'Halaman TST PMSE dimuat tanpa title tag'
            ];
        }
        
        return $this->generic_analysis($content, $http_code, $this->get_not_found_indicators('tst_pmse'), $this->get_valid_indicators('tst_pmse'));
    }
    
    /**
     * Analisis 99.co
     */
    private function analyze_property99($content, $http_code, $fetch_result = []) {
        $has_html = stripos($content, '<html') !== false;
        $has_title_tag = stripos($content, '<title') !== false;
        
        if ($has_html && !$has_title_tag) {
            return [
                'is_not_found' => true,
                'status' => 'not_found',
                'message' => 'Properti tidak ditemukan - halaman tanpa title',
                'details' => 'Halaman 99.co dimuat tanpa title tag'
            ];
        }
        
        return $this->generic_analysis($content, $http_code, $this->get_not_found_indicators('property99'), $this->get_valid_indicators('property99'));
    }
    
    /**
     * Analisis UjiCoba
     */
    private function analyze_ujicoba($content, $http_code, $fetch_result = []) {
        $has_html = stripos($content, '<html') !== false;
        $has_title_tag = stripos($content, '<title') !== false;
        
        if ($has_html && !$has_title_tag) {
            return [
                'is_not_found' => true,
                'status' => 'not_found',
                'message' => 'Halaman tidak ditemukan - halaman tanpa title',
                'details' => 'Halaman UjiCoba dimuat tanpa title tag'
            ];
        }
        
        return $this->generic_analysis($content, $http_code, $this->get_not_found_indicators('ujicoba'), $this->get_valid_indicators('ujicoba'));
    }
    
    /**
     * Analisis TikTok
     */
    private function analyze_tiktok($content, $http_code, $fetch_result = []) {
        $has_html = stripos($content, '<html') !== false;
        $has_title_tag = stripos($content, '<title') !== false;
        
        if ($has_html && !$has_title_tag) {
            return [
                'is_not_found' => true,
                'status' => 'not_found',
                'message' => 'Video/profil tidak ditemukan - halaman tanpa title',
                'details' => 'Halaman TikTok dimuat tanpa title tag'
            ];
        }
        
        // Cek TikTok specific error patterns
        if (stripos($content, 'video is not available') !== false ||
            stripos($content, 'This video is currently unavailable') !== false ||
            stripos($content, 'Sorry, this video is not available') !== false) {
            return [
                'is_not_found' => true,
                'status' => 'not_found',
                'message' => 'Video TikTok tidak tersedia',
                'details' => 'Video tidak dapat diakses atau telah dihapus'
            ];
        }
        
        return $this->generic_analysis($content, $http_code, $this->get_not_found_indicators('tiktok'), $this->get_valid_indicators('tiktok'));
    }
    
    /**
     * Analisis ShopeeFood
     */
    private function analyze_shopee_food($content, $http_code, $fetch_result = []) {
        $has_html = stripos($content, '<html') !== false;
        $has_title_tag = stripos($content, '<title') !== false;
        $has_js_config = stripos($content, 'window.__PAGE_ID__') !== false;
        
        if ($has_html && !$has_title_tag && $has_js_config) {
            return [
                'is_not_found' => true,
                'status' => 'not_found',
                'message' => 'Restoran/menu tidak ditemukan - halaman tanpa title',
                'details' => 'Halaman ShopeeFood dimuat dengan HTML structure tapi tanpa title tag'
            ];
        }
        
        return $this->generic_analysis($content, $http_code, $this->get_not_found_indicators('shopee_food'), $this->get_valid_indicators('shopee_food'));
    }
    
    /**
     * Analisis generic untuk platform yang tidak spesifik
     */
    private function analyze_generic($content, $http_code, $fetch_result = []) {
        $has_html = stripos($content, '<html') !== false;
        $has_title_tag = stripos($content, '<title') !== false;
        
        if ($has_html && !$has_title_tag) {
            return [
                'is_not_found' => true,
                'status' => 'not_found',
                'message' => 'Halaman tidak ditemukan - halaman tanpa title',
                'details' => 'Halaman dimuat tanpa title tag'
            ];
        }
        
        $not_found_indicators = [
            'Page not found',
            'Halaman tidak ditemukan',
            'error-404',
            'Not Found',
            '404'
        ];
        
        $valid_indicators = [
            'Add to Cart',
            'Tambah ke Keranjang',
            'Buy Now',
            'Beli Sekarang'
        ];
        
        return $this->generic_analysis($content, $http_code, $not_found_indicators, $valid_indicators);
    }
    
    /**
     * Get not found indicators by platform
     */
    private function get_not_found_indicators($platform) {
        switch ($platform) {
            case 'shopee':
                return [
                    'product-not-exist',
                    'Produk tidak ada',
                    'Produk tidak tersedia',
                    'product-not-exist__content',
                    'product-not-exist__text',
                    'is404" content="true"',
                    'robots" content="noindex"',
                    'Page not found',
                    'Halaman tidak ditemukan',
                    'Ups! Halaman yang kamu cari tidak ada',
                    'error-page'
                ];
            case 'tokopedia':
                return [
                    'Halaman tidak ditemukan',
                    'Page not found',
                    'Produk tidak ditemukan',
                    'Toko tidak ditemukan',
                    'error-404',
                    'Waduh, tujuanmu nggak ada!',
                    'Waduh, tujuanmu nggak ada',
                    'tujuanmu nggak ada',
                    'Mungkin kamu salah jalan atau alamat',
                    'Ayo balik sebelum gelap',
                    '"statusCode":410',
                    '"statusCode":404',
                    'error-illustration',
                    'error-not-found',
                    'Ups! Halaman yang kamu cari tidak ditemukan',
                    'Produk yang Anda cari tidak tersedia'
                ];
            case 'gofood':
                return [
                    'Restoran tidak ditemukan',
                    'Restaurant not found',
                    'error-404',
                    'Halaman tidak ditemukan',
                    'Menu tidak tersedia',
                    'Restoran tutup',
                    'Restaurant is closed',
                    'Tidak ada restoran di area ini',
                    'No restaurants available'
                ];
            case 'lazada':
                return [
                    'Page not found',
                    'Halaman tidak ditemukan',
                    'Product not found',
                    'error-404',
                    'Produk tidak ditemukan',
                    'Item tidak tersedia',
                    'Product is no longer available',
                    'Produk sudah tidak tersedia'
                ];
            case 'blibli':
                return [
                    'Halaman tidak ditemukan',
                    'Page not found',
                    'Produk tidak tersedia',
                    'error-404',
                    'Product not found',
                    'Item tidak ditemukan',
                    'Ups! Halaman yang kamu cari tidak ditemukan',
                    'Produk sudah tidak dijual'
                ];
            case 'grabfood':
                return [
                    'Restaurant not found',
                    'Restoran tidak ditemukan',
                    'error-404',
                    'Page not found',
                    'Merchant not available',
                    'Restoran tutup',
                    'Restaurant is closed',
                    'No delivery available'
                ];
            case 'bukalapak':
                return [
                    'Halaman tidak ditemukan',
                    'Page not found',
                    'Produk tidak tersedia',
                    'error-404',
                    'Product not found',
                    'Ups! Halaman yang kamu cari tidak ditemukan',
                    'Produk sudah habis',
                    'Item tidak tersedia'
                ];
            case 'simulasiapp':
                return [
                    'Page not found',
                    'Halaman tidak ditemukan',
                    'error-404',
                    'Not Found',
                    'Simulasi tidak ditemukan',
                    'App tidak tersedia',
                    'Service unavailable'
                ];
            case 'tst_pmse':
                return [
                    'Page not found',
                    'Halaman tidak ditemukan',
                    'error-404',
                    'Not Found',
                    'Service not available',
                    'Layanan tidak tersedia',
                    'Access denied',
                    'Akses ditolak'
                ];
            case 'property99':
                return [
                    'Property not found',
                    'Properti tidak ditemukan',
                    'error-404',
                    'Page not found',
                    'Listing not available',
                    'Properti sudah tidak tersedia',
                    'Property is no longer available'
                ];
            case 'ujicoba':
                return [
                    'Page not found',
                    'Halaman tidak ditemukan',
                    'error-404',
                    'Not Found',
                    'Test not found',
                    'Ujicoba tidak tersedia',
                    'Service unavailable'
                ];
            case 'tiktok':
                return [
                    'Video not available',
                    'This video is currently unavailable',
                    'Sorry, this video is not available',
                    'Page not found',
                    'User not found',
                    'Content not available',
                    'Video has been deleted',
                    'Account not found'
                ];
            case 'shopee_food':
                return [
                    'Restaurant not found',
                    'Restoran tidak ditemukan',
                    'error-404',
                    'Halaman tidak ditemukan',
                    'Menu tidak tersedia',
                    'Merchant not available',
                    'Restoran tutup',
                    'Restaurant is closed'
                ];
            default:
                return [
                    'Page not found',
                    'Halaman tidak ditemukan',
                    'error-404',
                    'Not Found',
                    '404'
                ];
        }
    }
    
    /**
     * Get valid indicators by platform
     */
    private function get_valid_indicators($platform) {
        switch ($platform) {
            case 'shopee':
                return [
                    'Tambah ke Keranjang',
                    'Beli Sekarang',
                    'product-detail',
                    'shopee-button-solid',
                    'product-name',
                    'Add to Cart',
                    'Buy Now',
                    'pdp-product-detail'
                ];
            case 'tokopedia':
                return [
                    'data-testid="btnAddToCart"',
                    'Beli Sekarang',
                    'Masukkan Keranjang',
                    'Deskripsi Produk',
                    'product-detail',
                    'pdp-product',
                    'Tambah ke Wishlist',
                    'Tambah ke Keranjang',
                    'add-to-cart'
                ];
            case 'gofood':
                return [
                    'Tambah ke Keranjang',
                    'Order Now',
                    'Pesan Sekarang',
                    'restaurant-detail',
                    'menu-item',
                    'order-button',
                    'restaurant-name',
                    'Add to Cart'
                ];
            case 'lazada':
                return [
                    'Add to Cart',
                    'Buy Now',
                    'pdp-product-title',
                    'add-to-cart-buy-now-btn',
                    'product-detail',
                    'Tambah ke Keranjang',
                    'Beli Sekarang'
                ];
            case 'blibli':
                return [
                    'Tambah ke Keranjang',
                    'Beli Sekarang',
                    'product-detail',
                    'add-to-cart',
                    'product-title',
                    'Add to Cart',
                    'Buy Now'
                ];
            case 'grabfood':
                return [
                    'Add to Basket',
                    'Order Now',
                    'restaurant-detail',
                    'menu-item',
                    'add-to-cart',
                    'restaurant-name',
                    'Tambah ke Keranjang'
                ];
            case 'bukalapak':
                return [
                    'Tambah ke Keranjang',
                    'Beli Langsung',
                    'product-detail',
                    'c-btn-product',
                    'product-name',
                    'Add to Cart',
                    'Buy Now'
                ];
            case 'simulasiapp':
                return [
                    'Mulai Simulasi',
                    'Start Simulation',
                    'app-content',
                    'simulation-start',
                    'main-content',
                    'app-interface'
                ];
            case 'tst_pmse':
                return [
                    'Login',
                    'Dashboard',
                    'main-content',
                    'service-menu',
                    'user-dashboard',
                    'form-input'
                ];
            case 'property99':
                return [
                    'Hubungi Agen',
                    'Contact Agent',
                    'property-detail',
                    'listing-detail',
                    'property-info',
                    'View Details',
                    'Lihat Detail'
                ];
            case 'ujicoba':
                return [
                    'Mulai Test',
                    'Start Test',
                    'test-content',
                    'main-content',
                    'quiz-start',
                    'test-interface'
                ];
            case 'tiktok':
                return [
                    'video-player',
                    'video-content',
                    'user-profile',
                    'video-info',
                    'player-container',
                    'tiktok-player'
                ];
            case 'shopee_food':
                return [
                    'Tambah ke Keranjang',
                    'Order Now',
                    'restaurant-detail',
                    'menu-item',
                    'order-button',
                    'restaurant-name',
                    'Add to Cart'
                ];
            default:
                return [
                    'Add to Cart',
                    'Tambah ke Keranjang',
                    'Buy Now',
                    'Beli Sekarang',
                    'main-content',
                    'content'
                ];
        }
    }
    
    /**
     * Analisis generic berdasarkan indikator
     */
    private function generic_analysis($content, $http_code, $not_found_indicators, $valid_indicators) {
        // Cek HTTP status code
        if ($http_code == 404) {
            return [
                'is_not_found' => true,
                'status' => 'not_found',
                'message' => 'Halaman tidak ditemukan (HTTP 404)',
                'details' => 'HTTP response code 404'
            ];
        }
        
        if ($http_code == 410) {
            return [
                'is_not_found' => true,
                'status' => 'gone',
                'message' => 'Halaman sudah tidak tersedia (HTTP 410)',
                'details' => 'HTTP response code 410 - Gone'
            ];
        }
        
        // Cek server error
        if ($http_code >= 500) {
            return [
                'is_not_found' => true,
                'status' => 'server_error',
                'message' => 'Server error',
                'details' => 'HTTP response code ' . $http_code
            ];
        }
        
        // Cek client error lainnya
        if ($http_code >= 400 && $http_code < 500) {
            return [
                'is_not_found' => true,
                'status' => 'client_error',
                'message' => 'Client error',
                'details' => 'HTTP response code ' . $http_code
            ];
        }
        
        // Cek indikator not found dalam konten
        $found_not_found_indicators = [];
        foreach ($not_found_indicators as $indicator) {
            if (stripos($content, $indicator) !== false) {
                $found_not_found_indicators[] = $indicator;
            }
        }
        
        if (!empty($found_not_found_indicators)) {
            return [
                'is_not_found' => true,
                'status' => 'not_found',
                'message' => 'Halaman tidak ditemukan',
                'details' => 'Ditemukan indikator: ' . implode(', ', $found_not_found_indicators)
            ];
        }
        
        // Cek konten valid
        $found_valid_indicators = [];
        foreach ($valid_indicators as $indicator) {
            if (stripos($content, $indicator) !== false) {
                $found_valid_indicators[] = $indicator;
            }
        }
        
        if (!empty($found_valid_indicators)) {
            return [
                'is_not_found' => false,
                'status' => 'found',
                'message' => 'Halaman ditemukan dan valid',
                'details' => 'Ditemukan indikator: ' . implode(', ', $found_valid_indicators)
            ];
        }
        
        // Cek apakah halaman kosong atau hanya berisi struktur dasar
        $content_length = strlen(strip_tags($content));
        if ($content_length < 500) {
            return [
                'is_not_found' => true,
                'status' => 'empty',
                'message' => 'Halaman kosong atau tidak memiliki konten',
                'details' => 'Konten terlalu pendek: ' . $content_length . ' karakter'
            ];
        }
        
        // Cek apakah ada meta tag robots noindex
        if (preg_match('/<meta[^>]*name=["\']robots["\'][^>]*content=["\'][^"\']*noindex[^"\']*["\'][^>]*>/i', $content)) {
            return [
                'is_not_found' => true,
                'status' => 'noindex',
                'message' => 'Halaman memiliki meta robots noindex',
                'details' => 'Meta tag robots dengan noindex ditemukan'
            ];
        }
        
        // Jika tidak ada indikator yang jelas
        return [
            'is_not_found' => false,
            'status' => 'uncertain',
            'message' => 'Status halaman tidak dapat dipastikan',
            'details' => 'Tidak ditemukan indikator yang jelas. HTTP Code: ' . $http_code . ', Content Length: ' . $content_length
        ];
    }
    
    /**
     * Extract title from content
     */
    private function extract_title($content) {
        if (preg_match('/<title[^>]*>([^<]+)</i', $content, $matches)) {
            return trim($matches[1]);
        }
        return 'No title found';
    }
    
    /**
     * Get random user agent
     */
    private function get_random_user_agent() {
        $user_agents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:121.0) Gecko/20100101 Firefox/121.0',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.1 Safari/605.1.15',
            'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36 Edg/120.0.0.0'
        ];
        
        return $user_agents[array_rand($user_agents)];
    }
    
    /**
     * Create error response
     */
    private function create_error_response($message, $url = '') {
        return [
            'success' => false,
            'url' => $url,
            'message' => $message,
            'checked_at' => date('Y-m-d H:i:s')
        ];
    }
    
    /**
     * Get supported platforms
     */
    public function get_supported_platforms() {
        return $this->platforms;
    }
    
    /**
     * Batch check multiple URLs
     */
    public function batch_check($urls, $delay = 1) {
        $results = [];
        $total = count($urls);
        
        foreach ($urls as $index => $url) {
            $result = $this->check_page_status($url);
            $result['batch_index'] = $index + 1;
            $result['batch_total'] = $total;
            $results[] = $result;
            
            // Delay untuk menghindari rate limiting
            if ($index < $total - 1 && $delay > 0) {
                sleep($delay);
            }
        }
        
        return [
            'batch_results' => $results,
            'summary' => $this->generate_batch_summary($results)
        ];
    }
    
    /**
     * Generate summary for batch results
     */
    private function generate_batch_summary($results) {
        $total = count($results);
        $successful = 0;
        $not_found = 0;
        $errors = 0;
        $platforms = [];
        
        foreach ($results as $result) {
            if ($result['success']) {
                $successful++;
                if ($result['is_not_found']) {
                    $not_found++;
                }
                
                $platform = $result['platform'];
                if (!isset($platforms[$platform])) {
                    $platforms[$platform] = ['total' => 0, 'not_found' => 0];
                }
                $platforms[$platform]['total']++;
                if ($result['is_not_found']) {
                    $platforms[$platform]['not_found']++;
                }
            } else {
                $errors++;
            }
        }
        
        return [
            'total_urls' => $total,
            'successful_checks' => $successful,
            'failed_checks' => $errors,
            'not_found_count' => $not_found,
            'found_count' => $successful - $not_found,
            'platforms' => $platforms,
            'success_rate' => $total > 0 ? round(($successful / $total) * 100, 2) : 0,
            'not_found_rate' => $successful > 0 ? round(($not_found / $successful) * 100, 2) : 0
        ];
    }
    
    /**
     * Debug check page - untuk troubleshooting
     */
    public function debug_check_page($url) {
        $this->set_debug_mode(true);
        
        $result = $this->fetch_page_content($url);
        
        if ($result['success']) {
            $platform = $this->detect_platform($url);
            
            // Ekstrak informasi debugging
            $debug_info = [
                'url' => $url,
                'final_url' => isset($result['final_url']) ? $result['final_url'] : $result['url'],
                'platform' => $platform ? $this->platforms[$platform]['name'] : 'Unknown',
                'http_code' => $result['http_code'],
                'content_length' => strlen($result['content']),
                'content_sample' => substr(strip_tags($result['content']), 0, 1000),
                'title' => $this->extract_title($result['content']),
                'has_html_structure' => stripos($result['content'], '<html') !== false,
                'has_title_tag' => stripos($result['content'], '<title') !== false,
                'has_javascript_config' => stripos($result['content'], 'window.__PAGE_ID__') !== false,
                'found_indicators' => $this->find_indicators($result['content'], $platform)
            ];
            
            return $debug_info;
        }
        
        return $result;
    }
    
    /**
     * Find indicators in content
     */
    private function find_indicators($content, $platform) {
        $not_found_indicators = $this->get_not_found_indicators($platform);
        $valid_indicators = $this->get_valid_indicators($platform);
        
        $found = [
            'not_found' => [],
            'valid' => []
        ];
        
        foreach ($not_found_indicators as $indicator) {
            if (stripos($content, $indicator) !== false) {
                $found['not_found'][] = $indicator;
            }
        }
        
        foreach ($valid_indicators as $indicator) {
            if (stripos($content, $indicator) !== false) {
                $found['valid'][] = $indicator;
            }
        }
        
        return $found;
    }
    
    /**
     * Smart check - menggunakan cache jika tersedia
     */
    public function smart_check($url, $cache_hours = 24, $force_refresh = false) {
        if (!$force_refresh) {
            $cached = $this->check_recent_result($url, $cache_hours);
            if ($cached['found']) {
                $cached_result = $cached['data'];
                return [
                    'success' => true,
                    'url' => $cached_result['url'],
                    'final_url' => isset($cached_result['final_url']) ? $cached_result['final_url'] : $cached_result['url'],
                    'platform' => $cached_result['platform'],
                    'platform_type' => $cached_result['platform_type'],
                    'http_code' => $cached_result['http_code'],
                    'is_not_found' => (bool)$cached_result['is_not_found'],
                    'status' => $cached_result['status'],
                    'message' => $cached_result['message'],
                    'details' => $cached_result['details'],
                    'checked_at' => $cached_result['checked_at'],
                    'cached' => true,
                    'cache_age_hours' => $cached['age_hours']
                ];
            }
        }
        
        // Jika tidak ada cache atau force refresh
        $result = $this->check_page_status($url);
        
        if ($result['success']) {
            $this->save_check_result($result);
            $result['cached'] = false;
        }
        
        return $result;
    }
    
    /**
     * Check if URL exists in database (recent check)
     */
    private function check_recent_result($url, $hours = 24) {
        if (!$this->CI->db) {
            return ['found' => false];
        }
        
        $this->CI->db->select('*');
        $this->CI->db->from('data_laporan_ecommerce_checks');
        $this->CI->db->where('url', $url);
        $this->CI->db->where('created_at >=', date('Y-m-d H:i:s', strtotime("-{$hours} hours")));
        $this->CI->db->order_by('created_at', 'DESC');
        $this->CI->db->limit(1);
        
        $result = $this->CI->db->get()->row_array();
        
        if ($result) {
            return [
                'found' => true,
                'data' => $result,
                'age_hours' => round((time() - strtotime($result['created_at'])) / 3600, 2)
            ];
        }
        
        return ['found' => false];
    }
    
    /**
     * Simpan hasil ke database
     */
    private function save_check_result($result) {
        if (!$result['success'] || !$this->CI->db) {
            return false;
        }
        
        $data = [
            'url' => $result['url'],
            'final_url' => isset($result['final_url']) ? $result['final_url'] : $result['url'],
            'platform' => $result['platform'],
            'platform_type' => $result['platform_type'],
            'http_code' => $result['http_code'],
            'is_not_found' => $result['is_not_found'] ? 1 : 0,
            'status' => $result['status'],
            'message' => $result['message'],
            'details' => $result['details'],
            'checked_at' => $result['checked_at'],
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        return $this->CI->db->insert('data_laporan_ecommerce_checks', $data);
    }
    
    /**
     * Bulk smart check
     */
    public function bulk_smart_check($urls, $cache_hours = 24, $delay = 1, $force_refresh = false) {
        $results = [];
        $total = count($urls);
        $fresh_checks = 0;
        $cached_results = 0;
        
        foreach ($urls as $index => $url) {
            $result = $this->smart_check($url, $cache_hours, $force_refresh);
            $result['batch_index'] = $index + 1;
            $result['batch_total'] = $total;
            
            if (isset($result['cached']) && $result['cached']) {
                $cached_results++;
            } else {
                $fresh_checks++;
                // Delay hanya untuk fresh checks
                if ($index < $total - 1 && $delay > 0) {
                    sleep($delay);
                }
            }
            
            $results[] = $result;
        }
        
        return [
            'batch_results' => $results,
            'summary' => $this->generate_batch_summary($results),
            'cache_info' => [
                'fresh_checks' => $fresh_checks,
                'cached_results' => $cached_results,
                'cache_hit_rate' => $total > 0 ? round(($cached_results / $total) * 100, 2) : 0
            ]
        ];
    }
    
    /**
     * Get statistics
     */
    public function get_statistics($platform = null, $days = 30) {
        if (!$this->CI->db) {
            return [];
        }
        
        $this->CI->db->select('
            platform,
            COUNT(*) as total_checks,
            SUM(CASE WHEN is_not_found = 1 THEN 1 ELSE 0 END) as not_found_count,
            SUM(CASE WHEN is_not_found = 0 THEN 1 ELSE 0 END) as found_count,
            AVG(CASE WHEN is_not_found = 1 THEN 1 ELSE 0 END) * 100 as not_found_percentage
        ');
        $this->CI->db->from('data_laporan_ecommerce_checks');
        $this->CI->db->where('created_at >=', date('Y-m-d H:i:s', strtotime("-{$days} days")));
        
        if ($platform) {
            $this->CI->db->where('platform', $platform);
        }
        
        $this->CI->db->group_by('platform');
        $this->CI->db->order_by('total_checks', 'DESC');
        
        return $this->CI->db->get()->result_array();
    }
    
    /**
     * Get recent checks
     */
    public function get_recent_checks($limit = 20, $platform = null) {
        if (!$this->CI->db) {
            return [];
        }
        
        $this->CI->db->select('*');
        $this->CI->db->from('data_laporan_ecommerce_checks');
        
        if ($platform) {
            $this->CI->db->where('platform', $platform);
        }
        
        $this->CI->db->order_by('created_at', 'DESC');
        $this->CI->db->limit($limit);
        
        return $this->CI->db->get()->result_array();
    }
    
    /**
     * Clean old records
     */
    public function clean_old_records($days = 90) {
        if (!$this->CI->db) {
            return ['deleted' => false, 'affected_rows' => 0];
        }
        
        $this->CI->db->where('created_at <', date('Y-m-d H:i:s', strtotime("-{$days} days")));
        $affected_rows = $this->CI->db->count_all_results('data_laporan_ecommerce_checks');
        
        $this->CI->db->where('created_at <', date('Y-m-d H:i:s', strtotime("-{$days} days")));
        $deleted = $this->CI->db->delete('data_laporan_ecommerce_checks');
        
        return [
            'deleted' => $deleted,
            'affected_rows' => $affected_rows
        ];
    }
}

/*
// Contoh penggunaan di Controller
class Checker extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->library('ecommerce_checker');
    }
    
    public function check_single() {
        $url = $this->input->post('url') ?: 'https://shopee.co.id/some-product';
        $force_refresh = $this->input->post('force_refresh') ?: false;
        $cache_hours = $this->input->post('cache_hours') ?: 24;
        
        // Gunakan smart check untuk memanfaatkan cache
        $result = $this->ecommerce_checker->smart_check($url, $cache_hours, $force_refresh);
        
        $result['csrf_hash'] = $this->security->get_csrf_hash();
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($result, JSON_PRETTY_PRINT));
    }
    
    public function debug_check() {
        $url = $this->input->post('url') ?: $this->input->get('url');
        
        if (!$url) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['error' => 'URL parameter required'], JSON_PRETTY_PRINT));
            return;
        }
        
        $debug_info = $this->ecommerce_checker->debug_check_page($url);
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($debug_info, JSON_PRETTY_PRINT));
    }
    
    public function batch_check() {
        $urls = $this->input->post('urls');
        $delay = $this->input->post('delay') ?: 2;
        $cache_hours = $this->input->post('cache_hours') ?: 24;
        $force_refresh = $this->input->post('force_refresh') ?: false;
        
        if (!is_array($urls)) {
            $urls = [
                'https://shopee.co.id/some-product',
                'https://tokopedia.com/some-product',
                'https://lazada.co.id/some-product'
            ];
        }
        
        $results = $this->ecommerce_checker->bulk_smart_check($urls, $cache_hours, $delay, $force_refresh);
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($results, JSON_PRETTY_PRINT));
    }
    
    public function get_platforms() {
        $platforms = $this->ecommerce_checker->get_supported_platforms();
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($platforms, JSON_PRETTY_PRINT));
    }
    
    public function statistics() {
        $platform = $this->input->get('platform');
        $days = $this->input->get('days') ?: 30;
        
        $stats = $this->ecommerce_checker->get_statistics($platform, $days);
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($stats, JSON_PRETTY_PRINT));
    }
    
    public function recent_checks() {
        $limit = $this->input->get('limit') ?: 20;
        $platform = $this->input->get('platform');
        
        $recent = $this->ecommerce_checker->get_recent_checks($limit, $platform);
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($recent, JSON_PRETTY_PRINT));
    }
    
    public function cleanup() {
        $days = $this->input->post('days') ?: 90;
        
        $result = $this->ecommerce_checker->clean_old_records($days);
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($result, JSON_PRETTY_PRINT));
    }
}
*/

/*
// SQL untuk tabel database yang diperbaiki
CREATE TABLE `data_laporan_ecommerce_checks` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `url` text NOT NULL,
    `final_url` text,
    `platform` varchar(50) NOT NULL,
    `platform_type` enum('ecommerce','food_delivery','application','government','property','testing','social_media') NOT NULL,
    `http_code` int(3) NOT NULL,
    `is_not_found` tinyint(1) NOT NULL DEFAULT 0,
    `status` enum('found','not_found','redirect','uncertain','gone','server_error','empty','client_error','noindex','redirect_to_home') NOT NULL,
    `message` varchar(500) NOT NULL,
    `details` text,
    `checked_at` datetime NOT NULL,
    `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_platform` (`platform`),
    KEY `idx_platform_type` (`platform_type`),
    KEY `idx_status` (`status`),
    KEY `idx_checked_at` (`checked_at`),
    KEY `idx_is_not_found` (`is_not_found`),
    KEY `idx_created_at` (`created_at`),
    KEY `idx_url` (`url`(255)),
    KEY `idx_platform_status` (`platform`, `status`),
    KEY `idx_http_code` (`http_code`),
    FULLTEXT KEY `ft_url` (`url`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Index untuk optimasi query cache
CREATE INDEX idx_url_created_composite ON `data_laporan_ecommerce_checks` (`url`(255), `created_at` DESC);
CREATE INDEX idx_platform_type_status ON `data_laporan_ecommerce_checks` (`platform_type`, `status`);
CREATE INDEX idx_not_found_created ON `data_laporan_ecommerce_checks` (`is_not_found`, `created_at`);
*/