<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Get client IP address dengan safe handling untuk PHP 8.1+
 * 
 * @return string
 */
if (!function_exists('get_client_ip')) {
    function get_client_ip() {
        $ip = '127.0.0.1'; // Default fallback
        
        // Priority list untuk mendapatkan IP
        $ip_keys = array(
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        );
        
        foreach ($ip_keys as $key) {
            if (isset($_SERVER[$key]) && !empty($_SERVER[$key])) {
                $ip_list = explode(',', $_SERVER[$key]);
                
                foreach ($ip_list as $potential_ip) {
                    $potential_ip = trim($potential_ip);
                    
                    // Validate IP
                    if (validate_ip_format($potential_ip)) {
                        return $potential_ip;
                    }
                }
            }
        }
        
        return $ip;
    }
}

/**
 * Validasi format IP address
 * 
 * @param string $ip
 * @return bool
 */
if (!function_exists('validate_ip_format')) {
    function validate_ip_format($ip) {
        if (empty($ip) || !is_string($ip)) {
            return false;
        }
        
        $ip = trim($ip);
        
        // Check for valid IPv4
        if (preg_match('/^(\d{1,3}\.){3}\d{1,3}$/', $ip)) {
            $parts = explode('.', $ip);
            foreach ($parts as $part) {
                if ((int)$part > 255 || (int)$part < 0) {
                    return false;
                }
            }
            return true;
        }
        
        // Check for valid IPv6
        if (preg_match('/^([0-9a-fA-F]{0,4}:){2,7}[0-9a-fA-F]{0,4}$/', $ip)) {
            return true;
        }
        
        return false;
    }
}

/**
 * Check if IP is private/local
 * 
 * @param string $ip
 * @return bool
 */
if (!function_exists('is_private_ip')) {
    function is_private_ip($ip) {
        if (!validate_ip_format($ip)) {
            return false;
        }
        
        // Private IPv4 ranges
        $private_ranges = array(
            array('10.0.0.0', '10.255.255.255'),
            array('172.16.0.0', '172.31.255.255'),
            array('192.168.0.0', '192.168.255.255'),
            array('127.0.0.0', '127.255.255.255')
        );
        
        $ip_long = ip2long($ip);
        
        if ($ip_long === false) {
            return false;
        }
        
        foreach ($private_ranges as $range) {
            $min = ip2long($range[0]);
            $max = ip2long($range[1]);
            
            if ($ip_long >= $min && $ip_long <= $max) {
                return true;
            }
        }
        
        // Check for localhost IPv6
        if ($ip === '::1' || $ip === 'fe80::1') {
            return true;
        }
        
        return false;
    }
}