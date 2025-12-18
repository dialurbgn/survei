<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Convert Food Delivery App Link to Web Link
 * Supports: GrabFood, ShopeeFood, GoFood
 * 
 * @param string $link
 * @return string
 */
if (!function_exists('translate_food_link')) {
    function translate_food_link($link)
    {
        $link = trim($link);

        // 1. GrabFood
        if (strpos($link, 'grab://') === 0) {
            // Format: grab://food?merchantId=XXXXX
            $parts = parse_url($link);
            parse_str($parts['query'] ?? '', $params);
            if (!empty($params['merchantId'])) {
                return 'https://food.grab.com/id/id/restaurant/unknown/' . $params['merchantId'];
            }
        }
        if (strpos($link, 'https://grb.to') === 0) {
            // Short link GrabFood
            return expand_short_link($link);
        }

        // 2. ShopeeFood
        if (strpos($link, 'shopeefood://') === 0) {
            // Format: shopeefood://food/shop/123456
            $parts = explode('/', $link);
            $id = end($parts);
            return 'https://shopee.co.id/universal-link/now-food/shop/' . $id;
        }
        if (strpos($link, 'https://shp.ee') === 0) {
            return expand_short_link($link);
        }

        // 3. GoFood
        if (strpos($link, 'gojek://') === 0) {
            // Format: gojek://gofood/merchant/123456
            $parts = explode('/', $link);
            $id = end($parts);
            return 'https://gofood.co.id/merchant/' . $id;
        }
        if (strpos($link, 'https://gofood.co.id') === 0) {
            return $link; // Sudah web link
        }

        return $link; // Default return original
    }
}

/**
 * Expand short link (GrabFood, ShopeeFood)
 * 
 * @param string $shortUrl
 * @return string
 */
if (!function_exists('expand_short_link')) {
    function expand_short_link($shortUrl)
    {
        $ch = curl_init($shortUrl);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        $finalUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        curl_close($ch);
        return $finalUrl;
    }
}



//$this->load->helper('food_link');

// Contoh GrabFood deep link
//$link_app = 'grab://food?merchantId=ABC123XYZ';
//$link_web = translate_food_link($link_app);

//echo $link_web;
// Output: https://food.grab.com/id/id/restaurant/unknown/ABC123XYZ
