<?php
defined('BASEPATH') or exit('No direct script access allowed');

if (!function_exists('is_active_menu')) {
    function is_active_menu($url)
    {
        $CI =& get_instance();
        $current_url = $CI->uri->uri_string();

        return $current_url == $url;
    }
}

if (!function_exists('is_active_menu_parent')) {
    function is_active_menu_parent($urls)
    {
        $CI =& get_instance();
        $current_url = $CI->uri->uri_string();

        foreach ($urls as $url) {
            if (strpos($current_url, $url) === 0) {
                return TRUE;
            }
        }

        return FALSE;
    }
}