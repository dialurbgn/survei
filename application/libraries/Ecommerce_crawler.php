<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ecommerce_crawler {
    
    private $CI;
    private $timeout = 30;
    private $max_results = 50;
    private $delay_between_requests = 2;
    private $user_agents;
    
    // Konfigurasi platform untuk crawling
    private $platforms = [
        'tokopedia' => [
            'name' => 'Tokopedia',
            'base_url' => 'https://www.tokopedia.com',
            'search_url' => 'https://www.tokopedia.com/search?q={keyword}&page={page}',
            'type' => 'ecommerce'
        ],
        'shopee' => [
            'name' => 'Shopee',
            'base_url' => 'https://shopee.co.id',
            'search_url' => 'https://shopee.co.id/search?keyword={keyword}&page={page}',
            'type' => 'ecommerce'
        ],
        'blibli' => [
            'name' => 'Blibli',
            'base_url' => 'https://www.blibli.com',
            'search_url' => 'https://www.blibli.com/backend/search/products?searchTerm={keyword}&page={page}',
            'type' => 'ecommerce'
        ],
        'lazada' => [
            'name' => 'Lazada',
            'base_url' => 'https://www.lazada.co.id',
            'search_url' => 'https://www.lazada.co.id/catalog/?q={keyword}&page={page}',
            'type' => 'ecommerce'
        ],
        'bukalapak' => [
            'name' => 'Bukalapak',
            'base_url' => 'https://www.bukalapak.com',
            'search_url' => 'https://www.bukalapak.com/products?search[keywords]={keyword}&page={page}',
            'type' => 'ecommerce'
        ]
    ];
    
    public function __construct() {
        $this->CI =& get_instance();
        
        // Set user agent pool untuk rotasi
        $this->user_agents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:89.0) Gecko/20100101 Firefox/89.0',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.1.1 Safari/605.1.15'
        ];
    }
    
    /**
     * Crawl produk berdasarkan keyword dari semua platform
     * @param string $keyword Keyword pencarian
     * @param array $platforms Platform yang akan di-crawl (optional)
     * @param int $max_pages Maksimal halaman per platform
     * @return array
     */
    public function crawl_by_keyword($keyword, $platforms = null, $max_pages = 3) {
        $results = [];
        $total_found = 0;
        
        // Jika platform tidak dispesifikasi, gunakan semua
        if (!$platforms) {
            $platforms = array_keys($this->platforms);
        }
        
        foreach ($platforms as $platform) {
            if (!isset($this->platforms[$platform])) {
                continue;
            }
            
            $platform_results = $this->crawl_platform($platform, $keyword, $max_pages);
            
            $results[$platform] = [
                'platform' => $this->platforms[$platform]['name'],
                'keyword' => $keyword,
                'total_found' => count($platform_results),
                'products' => $platform_results,
                'crawled_at' => date('Y-m-d H:i:s')
            ];
            
            $total_found += count($platform_results);
            
            // Delay antar platform
            sleep($this->delay_between_requests);
        }
        
        return [
            'keyword' => $keyword,
            'total_platforms' => count($platforms),
            'total_products' => $total_found,
            'results' => $results,
            'crawled_at' => date('Y-m-d H:i:s')
        ];
    }
    
    /**
     * Crawl produk dari platform tertentu
     * @param string $platform
     * @param string $keyword
     * @param int $max_pages
     * @return array
     */
    private function crawl_platform($platform, $keyword, $max_pages = 3) {
        $products = [];
        $page = 1;
        
        while ($page <= $max_pages && count($products) < $this->max_results) {
            $page_products = $this->crawl_page($platform, $keyword, $page);
            
            if (empty($page_products)) {
                break; // Tidak ada produk lagi
            }
            
            $products = array_merge($products, $page_products);
            $page++;
            
            // Delay antar halaman
            sleep(1);
        }
        
        return array_slice($products, 0, $this->max_results);
    }
    
    /**
     * Crawl halaman tertentu dari platform
     * @param string $platform
     * @param string $keyword
     * @param int $page
     * @return array
     */
    private function crawl_page($platform, $keyword, $page) {
        $search_url = $this->build_search_url($platform, $keyword, $page);
        $content = $this->fetch_content($search_url);
        
        if (!$content) {
            return [];
        }
        
        return $this->parse_products($platform, $content);
    }
    
    /**
     * Build URL pencarian untuk platform
     * @param string $platform
     * @param string $keyword
     * @param int $page
     * @return string
     */
    private function build_search_url($platform, $keyword, $page) {
        $config = $this->platforms[$platform];
        $url = $config['search_url'];
        
        $url = str_replace('{keyword}', urlencode($keyword), $url);
        $url = str_replace('{page}', $page, $url);
        
        return $url;
    }
    
    /**
     * Fetch content dari URL
     * @param string $url
     * @return string|false
     */
    private function fetch_content($url) {
        $ch = curl_init();
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_USERAGENT => $this->get_random_user_agent(),
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER => [
                'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                'Accept-Language: id-ID,id;q=0.9,en;q=0.8',
                'Accept-Encoding: gzip, deflate, br',
                'Connection: keep-alive',
                'Upgrade-Insecure-Requests: 1',
                'Cache-Control: no-cache'
            ]
        ]);
        
        $content = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        curl_close($ch);
        
        if ($content === false || $http_code !== 200) {
            return false;
        }
        
        return $content;
    }
    
    /**
     * Parse produk dari konten halaman berdasarkan platform
     * @param string $platform
     * @param string $content
     * @return array
     */
    private function parse_products($platform, $content) {
        switch ($platform) {
            case 'tokopedia':
                return $this->parse_tokopedia_products($content);
            case 'shopee':
                return $this->parse_shopee_products($content);
            case 'blibli':
                return $this->parse_blibli_products($content);
            case 'lazada':
                return $this->parse_lazada_products($content);
            case 'bukalapak':
                return $this->parse_bukalapak_products($content);
            default:
                return [];
        }
    }
    
    /**
     * Parse produk Tokopedia
     * @param string $content
     * @return array
     */
    private function parse_tokopedia_products($content) {
        $products = [];
        
        // Cari JSON data dalam script tag
        if (preg_match('/__NEXT_DATA__.*?=.*?({.*?})<\/script>/s', $content, $matches)) {
            $json_data = json_decode($matches[1], true);
            
            if (isset($json_data['props']['pageProps']['initialData']['products'])) {
                foreach ($json_data['props']['pageProps']['initialData']['products'] as $product) {
                    $products[] = $this->extract_tokopedia_product($product);
                }
            }
        }
        
        // Fallback: parsing HTML
        if (empty($products)) {
            $products = $this->parse_tokopedia_html($content);
        }
        
        return $products;
    }
    
    /**
     * Parse produk Tokopedia dari HTML
     * @param string $content
     * @return array
     */
    private function parse_tokopedia_html($content) {
        $products = [];
        $dom = new DOMDocument();
        @$dom->loadHTML($content);
        $xpath = new DOMXPath($dom);
        
        // Cari element produk
        $product_elements = $xpath->query('//div[contains(@class, "css-1sn1xa2")]');
        
        foreach ($product_elements as $element) {
            $product = $this->extract_tokopedia_html_product($element, $xpath);
            if ($product) {
                $products[] = $product;
            }
        }
        
        return $products;
    }
    
    /**
     * Extract data produk Tokopedia dari JSON
     * @param array $product_data
     * @return array
     */
    private function extract_tokopedia_product($product_data) {
        return [
            'title' => $product_data['name'] ?? '',
            'price' => $this->format_price($product_data['price'] ?? ''),
            'original_price' => $this->format_price($product_data['originalPrice'] ?? ''),
            'discount' => $product_data['discountPercentage'] ?? 0,
            'rating' => $product_data['rating'] ?? 0,
            'sold' => $product_data['sold'] ?? 0,
            'shop_name' => $product_data['shop']['name'] ?? '',
            'shop_location' => $product_data['shop']['location'] ?? '',
            'image_url' => $product_data['imageUrl'] ?? '',
            'product_url' => $this->build_tokopedia_url($product_data['uri'] ?? ''),
            'platform' => 'Tokopedia'
        ];
    }
    
    /**
     * Extract data produk Tokopedia dari HTML element
     * @param DOMElement $element
     * @param DOMXPath $xpath
     * @return array|null
     */
    private function extract_tokopedia_html_product($element, $xpath) {
        $title_node = $xpath->query('.//span[contains(@class, "css-20kt3o")]', $element)->item(0);
        $price_node = $xpath->query('.//span[contains(@class, "css-o5uqvq")]', $element)->item(0);
        $link_node = $xpath->query('.//a', $element)->item(0);
        
        if (!$title_node || !$price_node || !$link_node) {
            return null;
        }
        
        return [
            'title' => trim($title_node->textContent),
            'price' => $this->format_price($price_node->textContent),
            'original_price' => '',
            'discount' => 0,
            'rating' => 0,
            'sold' => 0,
            'shop_name' => '',
            'shop_location' => '',
            'image_url' => '',
            'product_url' => $this->build_full_url('https://www.tokopedia.com', $link_node->getAttribute('href')),
            'platform' => 'Tokopedia'
        ];
    }
    
    /**
     * Parse produk Shopee
     * @param string $content
     * @return array
     */
    private function parse_shopee_products($content) {
        $products = [];
        
        // Cari JSON data dalam script tag
        if (preg_match('/window\.__INITIAL_STATE__\s*=\s*({.*?});/s', $content, $matches)) {
            $json_data = json_decode($matches[1], true);
            
            if (isset($json_data['searchItem']['items'])) {
                foreach ($json_data['searchItem']['items'] as $product) {
                    $products[] = $this->extract_shopee_product($product);
                }
            }
        }
        
        // Fallback: parsing HTML
        if (empty($products)) {
            $products = $this->parse_shopee_html($content);
        }
        
        return $products;
    }
    
    /**
     * Parse produk Shopee dari HTML
     * @param string $content
     * @return array
     */
    private function parse_shopee_html($content) {
        $products = [];
        $dom = new DOMDocument();
        @$dom->loadHTML($content);
        $xpath = new DOMXPath($dom);
        
        // Cari element produk
        $product_elements = $xpath->query('//div[contains(@class, "col-xs-2-4")]');
        
        foreach ($product_elements as $element) {
            $product = $this->extract_shopee_html_product($element, $xpath);
            if ($product) {
                $products[] = $product;
            }
        }
        
        return $products;
    }
    
    /**
     * Extract data produk Shopee dari JSON
     * @param array $product_data
     * @return array
     */
    private function extract_shopee_product($product_data) {
        $item = $product_data['item_basic'] ?? $product_data;
        
        return [
            'title' => $item['name'] ?? '',
            'price' => $this->format_price(($item['price'] ?? 0) / 100000),
            'original_price' => $this->format_price(($item['price_before_discount'] ?? 0) / 100000),
            'discount' => $item['discount'] ?? 0,
            'rating' => $item['item_rating']['rating_star'] ?? 0,
            'sold' => $item['sold'] ?? 0,
            'shop_name' => $item['shop_name'] ?? '',
            'shop_location' => $item['shop_location'] ?? '',
            'image_url' => $this->build_shopee_image_url($item['image'] ?? ''),
            'product_url' => $this->build_shopee_url($item['name'] ?? '', $item['shopid'] ?? 0, $item['itemid'] ?? 0),
            'platform' => 'Shopee'
        ];
    }
    
    /**
     * Extract data produk Shopee dari HTML element
     * @param DOMElement $element
     * @param DOMXPath $xpath
     * @return array|null
     */
    private function extract_shopee_html_product($element, $xpath) {
        $title_node = $xpath->query('.//div[contains(@class, "ie3A+n")]', $element)->item(0);
        $price_node = $xpath->query('.//span[contains(@class, "ZEgDH9")]', $element)->item(0);
        $link_node = $xpath->query('.//a', $element)->item(0);
        
        if (!$title_node || !$price_node || !$link_node) {
            return null;
        }
        
        return [
            'title' => trim($title_node->textContent),
            'price' => $this->format_price($price_node->textContent),
            'original_price' => '',
            'discount' => 0,
            'rating' => 0,
            'sold' => 0,
            'shop_name' => '',
            'shop_location' => '',
            'image_url' => '',
            'product_url' => $this->build_full_url('https://shopee.co.id', $link_node->getAttribute('href')),
            'platform' => 'Shopee'
        ];
    }
    
    /**
     * Parse produk Blibli
     * @param string $content
     * @return array
     */
    private function parse_blibli_products($content) {
        $products = [];
        $data = json_decode($content, true);
        
        if (isset($data['data']['products'])) {
            foreach ($data['data']['products'] as $product) {
                $products[] = $this->extract_blibli_product($product);
            }
        }
        
        return $products;
    }
    
    /**
     * Extract data produk Blibli
     * @param array $product_data
     * @return array
     */
    private function extract_blibli_product($product_data) {
        return [
            'title' => $product_data['name'] ?? '',
            'price' => $this->format_price($product_data['price']['offered'] ?? 0),
            'original_price' => $this->format_price($product_data['price']['listed'] ?? 0),
            'discount' => $product_data['discount']['percentage'] ?? 0,
            'rating' => $product_data['review']['rating'] ?? 0,
            'sold' => $product_data['sold'] ?? 0,
            'shop_name' => $product_data['merchant']['name'] ?? '',
            'shop_location' => '',
            'image_url' => $product_data['image']['small'] ?? '',
            'product_url' => $this->build_blibli_url($product_data['sku'] ?? ''),
            'platform' => 'Blibli'
        ];
    }
    
    /**
     * Parse produk Lazada
     * @param string $content
     * @return array
     */
    private function parse_lazada_products($content) {
        $products = [];
        $dom = new DOMDocument();
        @$dom->loadHTML($content);
        $xpath = new DOMXPath($dom);
        
        // Cari element produk
        $product_elements = $xpath->query('//div[@data-qa-locator="product-item"]');
        
        foreach ($product_elements as $element) {
            $product = $this->extract_lazada_html_product($element, $xpath);
            if ($product) {
                $products[] = $product;
            }
        }
        
        return $products;
    }
    
    /**
     * Extract data produk Lazada dari HTML element
     * @param DOMElement $element
     * @param DOMXPath $xpath
     * @return array|null
     */
    private function extract_lazada_html_product($element, $xpath) {
        $title_node = $xpath->query('.//a[@title]', $element)->item(0);
        $price_node = $xpath->query('.//span[contains(@class, "currency")]', $element)->item(0);
        
        if (!$title_node || !$price_node) {
            return null;
        }
        
        return [
            'title' => trim($title_node->getAttribute('title')),
            'price' => $this->format_price($price_node->textContent),
            'original_price' => '',
            'discount' => 0,
            'rating' => 0,
            'sold' => 0,
            'shop_name' => '',
            'shop_location' => '',
            'image_url' => '',
            'product_url' => $this->build_full_url('https://www.lazada.co.id', $title_node->getAttribute('href')),
            'platform' => 'Lazada'
        ];
    }
    
    /**
     * Parse produk Bukalapak
     * @param string $content
     * @return array
     */
    private function parse_bukalapak_products($content) {
        $products = [];
        $dom = new DOMDocument();
        @$dom->loadHTML($content);
        $xpath = new DOMXPath($dom);
        
        // Cari element produk
        $product_elements = $xpath->query('//div[contains(@class, "product-card")]');
        
        foreach ($product_elements as $element) {
            $product = $this->extract_bukalapak_html_product($element, $xpath);
            if ($product) {
                $products[] = $product;
            }
        }
        
        return $products;
    }
    
    /**
     * Extract data produk Bukalapak dari HTML element
     * @param DOMElement $element
     * @param DOMXPath $xpath
     * @return array|null
     */
    private function extract_bukalapak_html_product($element, $xpath) {
        $title_node = $xpath->query('.//p[contains(@class, "product-title")]', $element)->item(0);
        $price_node = $xpath->query('.//span[contains(@class, "product-price")]', $element)->item(0);
        $link_node = $xpath->query('.//a', $element)->item(0);
        
        if (!$title_node || !$price_node || !$link_node) {
            return null;
        }
        
        return [
            'title' => trim($title_node->textContent),
            'price' => $this->format_price($price_node->textContent),
            'original_price' => '',
            'discount' => 0,
            'rating' => 0,
            'sold' => 0,
            'shop_name' => '',
            'shop_location' => '',
            'image_url' => '',
            'product_url' => $this->build_full_url('https://www.bukalapak.com', $link_node->getAttribute('href')),
            'platform' => 'Bukalapak'
        ];
    }
    
    /**
     * Helper functions
     */
    
    private function get_random_user_agent() {
        return $this->user_agents[array_rand($this->user_agents)];
    }
    
    private function format_price($price) {
        // Remove currency symbols and format
        $price = preg_replace('/[^\d,.]/', '', $price);
        $price = str_replace(',', '', $price);
        return (int) $price;
    }
    
    private function build_tokopedia_url($uri) {
        return 'https://www.tokopedia.com' . $uri;
    }
    
    private function build_shopee_url($name, $shopid, $itemid) {
        $slug = strtolower(str_replace(' ', '-', $name));
        return "https://shopee.co.id/{$slug}-i.{$shopid}.{$itemid}";
    }
    
    private function build_shopee_image_url($image_hash) {
        return "https://cf.shopee.co.id/file/{$image_hash}";
    }
    
    private function build_blibli_url($sku) {
        return "https://www.blibli.com/p/{$sku}";
    }
    
    private function build_full_url($base_url, $path) {
        if (strpos($path, 'http') === 0) {
            return $path;
        }
        return rtrim($base_url, '/') . '/' . ltrim($path, '/');
    }
    
    /**
     * Simpan hasil crawling ke database
     * @param string $keyword
     * @param array $results
     * @return bool
     */
    public function save_crawl_results($keyword, $results) {
        $crawl_id = $this->save_crawl_session($keyword, $results);
        
        if (!$crawl_id) {
            return false;
        }
        
        foreach ($results['results'] as $platform => $platform_data) {
            foreach ($platform_data['products'] as $product) {
                $this->save_product($crawl_id, $product);
            }
        }
        
        return true;
    }
    
    /**
     * Simpan session crawling
     * @param string $keyword
     * @param array $results
     * @return int|false
     */
    private function save_crawl_session($keyword, $results) {
        $data = [
            'keyword' => $keyword,
            'total_platforms' => $results['total_platforms'],
            'total_products' => $results['total_products'],
            'crawled_at' => $results['crawled_at'],
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        if ($this->CI->db->insert('crawl_sessions', $data)) {
            return $this->CI->db->insert_id();
        }
        
        return false;
    }
    
    /**
     * Simpan data produk
     * @param int $crawl_id
     * @param array $product
     * @return bool
     */
    private function save_product($crawl_id, $product) {
        $data = [
            'crawl_id' => $crawl_id,
            'platform' => $product['platform'],
            'title' => $product['title'],
            'price' => $product['price'],
            'original_price' => $product['original_price'],
            'discount' => $product['discount'],
            'rating' => $product['rating'],
            'sold' => $product['sold'],
            'shop_name' => $product['shop_name'],
            'shop_location' => $product['shop_location'],
            'image_url' => $product['image_url'],
            'product_url' => $product['product_url'],
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        return $this->CI->db->insert('crawl_products', $data);
    }
    
    /**
     * Get hasil crawling berdasarkan keyword
     * @param string $keyword
     * @param int $limit
     * @return array
     */
    public function get_crawl_results($keyword, $limit = 50) {
        $this->CI->db->select('cs.*, COUNT(cp.id) as total_products');
        $this->CI->db->from('crawl_sessions cs');
        $this->CI->db->join('crawl_products cp', 'cs.id = cp.crawl_id', 'left');
        $this->CI->db->where('cs.keyword', $keyword);
        $this->CI->db->group_by('cs.id');
        $this->CI->db->order_by('cs.created_at', 'DESC');
        $this->CI->db->limit($limit);
        
        return $this->CI->db->get()->result_array();
    }
    
    /**
     * Get produk berdasarkan crawl session
     * @param int $crawl_id
     * @param string $platform
     * @return array
     */
    public function get_crawl_products($crawl_id, $platform = null) {
        $this->CI->db->from('crawl_products');
        $this->CI->db->where('crawl_id', $crawl_id);
        
        if ($platform) {
            $this->CI->db->where('platform', $platform);
        }
        
        $this->CI->db->order_by('price', 'ASC');
        
        return $this->CI->db->get()->result_array();
    }
    
    /**
     * Get statistik crawling
     * @param string $keyword
     * @return array
     */
    public function get_crawl_statistics($keyword) {
        // Get latest crawl session
        $this->CI->db->select('id, crawled_at');
        $this->CI->db->from('crawl_sessions');
        $this->CI->db->where('keyword', $keyword);
        $this->CI->db->order_by('created_at', 'DESC');
        $this->CI->db->limit(1);
        
        $session = $this->CI->db->get()->row_array();
        
        if (!$session) {
            return null;
        }
        
        // Get statistics by platform
        $this->CI->db->select('platform, COUNT(*) as count, AVG(price) as avg_price, MIN(price) as min_price, MAX(price) as max_price');
        $this->CI->db->from('crawl_products');
        $this->CI->db->where('crawl_id', $session['id']);
        $this->CI->db->group_by('platform');
        
        $platform_stats = $this->CI->db->get()->result_array();
        
        return [
            'session' => $session,
            'platform_stats' => $platform_stats
        ];
    }
}

/*
// Contoh penggunaan di Controller
class Crawler extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->library('ecommerce_crawler');
    }
    
    public function crawl() {
        $keyword = $this->input->post('keyword') ?: 'CAIRAN PERAK TEMBAGA';
        $platforms = $this->input->post('platforms') ?: ['tokopedia', 'shopee', 'blibli'];
        $max_pages = $this->input->post('max_pages') ?: 2;
        
        $results = $this->ecommerce_crawler->crawl_by_keyword($keyword, $platforms, $max_pages);
        
/*
// Contoh penggunaan di Controller
class Crawler extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->library('ecommerce_crawler');
    }
    
    public function crawl() {
        $keyword = $this->input->post('keyword') ?: 'CAIRAN PERAK TEMBAGA';
        $platforms = $this->input->post('platforms') ?: ['tokopedia', 'shopee', 'blibli'];
        $max_pages = $this->input->post('max_pages') ?: 2;
        
        $results = $this->ecommerce_crawler->crawl_by_keyword($keyword, $platforms, $max_pages);
        
        // Simpan ke database
        $this->ecommerce_crawler->save_crawl_results($keyword, $results);
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($results, JSON_PRETTY_PRINT));
    }
    
    public function search_results() {
        $keyword = $this->input->get('keyword') ?: 'CAIRAN PERAK TEMBAGA';
        $results = $this->ecommerce_crawler->get_crawl_results($keyword);
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($results, JSON_PRETTY_PRINT));
    }
    
    public function get_products() {
        $crawl_id = $this->input->get('crawl_id');
        $platform = $this->input->get('platform');
        
        $products = $this->ecommerce_crawler->get_crawl_products($crawl_id, $platform);
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($products, JSON_PRETTY_PRINT));
    }
    
    public function statistics() {
        $keyword = $this->input->get('keyword') ?: 'CAIRAN PERAK TEMBAGA';
        $stats = $this->ecommerce_crawler->get_crawl_statistics($keyword);
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($stats, JSON_PRETTY_PRINT));
    }
}
*/

/*
// SQL untuk tabel database
CREATE TABLE `crawl_sessions` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `keyword` varchar(255) NOT NULL,
    `total_platforms` int(11) NOT NULL DEFAULT 0,
    `total_products` int(11) NOT NULL DEFAULT 0,
    `crawled_at` datetime NOT NULL,
    `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_keyword` (`keyword`),
    KEY `idx_crawled_at` (`crawled_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `crawl_products` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `crawl_id` int(11) NOT NULL,
    `platform` varchar(50) NOT NULL,
    `title` text NOT NULL,
    `price` int(11) NOT NULL DEFAULT 0,
    `original_price` int(11) NOT NULL DEFAULT 0,
    `discount` int(11) NOT NULL DEFAULT 0,
    `rating` decimal(3,2) NOT NULL DEFAULT 0.00,
    `sold` int(11) NOT NULL DEFAULT 0,
    `shop_name` varchar(255) DEFAULT NULL,
    `shop_location` varchar(255) DEFAULT NULL,
    `image_url` text,
    `product_url` text,
    `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_crawl_id` (`crawl_id`),
    KEY `idx_platform` (`platform`),
    KEY `idx_price` (`price`),
    KEY `idx_rating` (`rating`),
    FOREIGN KEY (`crawl_id`) REFERENCES `crawl_sessions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `crawl_logs` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `crawl_id` int(11) NOT NULL,
    `platform` varchar(50) NOT NULL,
    `page` int(11) NOT NULL,
    `url` text NOT NULL,
    `status` enum('success','failed','timeout','error') NOT NULL,
    `response_time` int(11) NOT NULL DEFAULT 0,
    `products_found` int(11) NOT NULL DEFAULT 0,
    `error_message` text,
    `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_crawl_id` (`crawl_id`),
    KEY `idx_platform` (`platform`),
    KEY `idx_status` (`status`),
    FOREIGN KEY (`crawl_id`) REFERENCES `crawl_sessions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
*/