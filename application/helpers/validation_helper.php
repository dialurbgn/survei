<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Validasi email yang aman untuk PHP 8.1+
 * 
 * @param string|null $email
 * @return bool
 */
if (!function_exists('is_valid_email')) {
    function is_valid_email($email) {
        if (empty($email) || !is_string($email)) {
            return false;
        }
        
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}

/**
 * Validasi input dengan filter yang aman
 * 
 * @param mixed $value
 * @param int $filter
 * @param array|int $options
 * @return mixed
 */
if (!function_exists('safe_filter_var')) {
    function safe_filter_var($value, $filter, $options = []) {
        if ($value === null || $value === '') {
            return false;
        }
        
        if (empty($options)) {
            return filter_var($value, $filter);
        }
        
        return filter_var($value, $filter, $options);
    }
}

/**
 * Sanitasi input string
 * 
 * @param mixed $value
 * @return string
 */
if (!function_exists('safe_sanitize_string')) {
    function safe_sanitize_string($value) {
        if ($value === null || $value === '') {
            return '';
        }
        
        return filter_var($value, FILTER_SANITIZE_STRING);
    }
}

/**
 * Validasi URL yang aman
 * 
 * @param string|null $url
 * @return bool
 */
if (!function_exists('is_valid_url')) {
    function is_valid_url($url) {
        if (empty($url) || !is_string($url)) {
            return false;
        }
        
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }
}

/**
 * Validasi IP address yang aman
 * 
 * @param string|null $ip
 * @return bool
 */
if (!function_exists('is_valid_ip')) {
    function is_valid_ip($ip) {
        if (empty($ip) || !is_string($ip)) {
            return false;
        }
        
        return filter_var($ip, FILTER_VALIDATE_IP) !== false;
    }
}

/**
 * Validasi integer yang aman
 * 
 * @param mixed $value
 * @return bool
 */
if (!function_exists('is_valid_int')) {
    function is_valid_int($value) {
        if ($value === null || $value === '') {
            return false;
        }
        
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }
}