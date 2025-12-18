<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('encrypt_short')) {
    function encrypt_short($data, $key = 'rahasia123456789') {
        $cipher = "aes-128-ctr";
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher));
        $enc = openssl_encrypt($data, $cipher, $key, OPENSSL_RAW_DATA, $iv);
        return base64url_encode($iv . $enc);
    }
}

if (!function_exists('decrypt_short')) {
    function decrypt_short($data, $key = 'rahasia123456789') {
        $cipher = "aes-128-ctr";
        $raw = base64url_decode($data);
        $iv_len = openssl_cipher_iv_length($cipher);
        $iv = substr($raw, 0, $iv_len);
        $enc = substr($raw, $iv_len);
        return openssl_decrypt($enc, $cipher, $key, OPENSSL_RAW_DATA, $iv);
    }
}

if (!function_exists('base64url_encode')) {
    function base64url_encode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}

if (!function_exists('base64url_decode')) {
    function base64url_decode($data) {
        return base64_decode(strtr($data, '-_', '+/'));
    }
}