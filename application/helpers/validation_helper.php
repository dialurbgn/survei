<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Validasi email yang aman untuk PHP 8.1+
 * 
 * @param mixed $email
 * @return bool
 */
if (!function_exists('is_valid_email')) {
    function is_valid_email($email) {
        // Cek tipe data dan empty
        if (empty($email) || !is_string($email)) {
            return false;
        }
        
        // Trim whitespace
        $email = trim($email);
        
        // Basic regex validation
        $pattern = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
        
        if (!preg_match($pattern, $email)) {
            return false;
        }
        
        // Use filter_var safely
        $result = filter_var($email, FILTER_VALIDATE_EMAIL);
        
        return $result !== false;
    }
}

/**
 * Validasi URL yang aman
 * 
 * @param mixed $url
 * @return bool
 */
if (!function_exists('is_valid_url')) {
    function is_valid_url($url) {
        if (empty($url) || !is_string($url)) {
            return false;
        }
        
        $url = trim($url);
        
        // Basic check
        if (!preg_match('/^https?:\/\/.+/', $url)) {
            return false;
        }
        
        $result = filter_var($url, FILTER_VALIDATE_URL);
        return $result !== false;
    }
}

/**
 * Validasi IP address yang aman
 * 
 * @param mixed $ip
 * @return bool
 */
if (!function_exists('is_valid_ip')) {
    function is_valid_ip($ip) {
        if (empty($ip) || !is_string($ip)) {
            return false;
        }
        
        $ip = trim($ip);
        $result = filter_var($ip, FILTER_VALIDATE_IP);
        return $result !== false;
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
        
        // Cek apakah numeric dulu
        if (!is_numeric($value)) {
            return false;
        }
        
        $result = filter_var($value, FILTER_VALIDATE_INT);
        return $result !== false;
    }
}

/**
 * Sanitasi string dengan aman
 * 
 * @param mixed $value
 * @return string
 */
if (!function_exists('safe_sanitize')) {
    function safe_sanitize($value) {
        if ($value === null || $value === '') {
            return '';
        }
        
        if (!is_string($value)) {
            $value = (string)$value;
        }
        
        // Remove HTML tags
        $value = strip_tags($value);
        
        // Remove special chars
        $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        
        return $value;
    }
}

/**
 * Validasi nomor telepon Indonesia
 * 
 * @param mixed $phone
 * @return bool
 */
if (!function_exists('is_valid_phone')) {
    function is_valid_phone($phone) {
        if (empty($phone) || !is_string($phone)) {
            return false;
        }
        
        // Remove non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Check length (10-15 digits)
        $length = strlen($phone);
        if ($length < 10 || $length > 15) {
            return false;
        }
        
        // Check if starts with valid prefix
        $valid_prefixes = ['08', '62', '0'];
        $starts_valid = false;
        
        foreach ($valid_prefixes as $prefix) {
            if (substr($phone, 0, strlen($prefix)) === $prefix) {
                $starts_valid = true;
                break;
            }
        }
        
        return $starts_valid;
    }
}

/**
 * Validasi NIP (minimal 8 digit, hanya angka)
 * 
 * @param mixed $nip
 * @return bool
 */
if (!function_exists('is_valid_nip')) {
    function is_valid_nip($nip) {
        if (empty($nip) || !is_string($nip)) {
            return false;
        }
        
        $nip = trim($nip);
        
        // Hanya boleh angka
        if (!preg_match('/^[0-9]+$/', $nip)) {
            return false;
        }
        
        // Minimal 8 digit
        return strlen($nip) >= 8;
    }
}

/**
 * Validasi nama (hanya huruf dan spasi)
 * 
 * @param mixed $name
 * @return bool
 */
if (!function_exists('is_valid_name')) {
    function is_valid_name($name) {
        if (empty($name) || !is_string($name)) {
            return false;
        }
        
        $name = trim($name);
        
        // Minimal 3 karakter
        if (strlen($name) < 3) {
            return false;
        }
        
        // Hanya huruf dan spasi
        return preg_match('/^[a-zA-Z\s]+$/', $name) === 1;
    }
}

/**
 * Clean input untuk mencegah XSS
 * 
 * @param mixed $data
 * @return mixed
 */
if (!function_exists('clean_input')) {
    function clean_input($data) {
        if ($data === null || $data === '') {
            return '';
        }
        
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = clean_input($value);
            }
            return $data;
        }
        
        if (!is_string($data)) {
            return $data;
        }
        
        // Remove whitespace
        $data = trim($data);
        
        // Remove backslashes
        $data = stripslashes($data);
        
        // Convert special characters
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        
        return $data;
    }
}