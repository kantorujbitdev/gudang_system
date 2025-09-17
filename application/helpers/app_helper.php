<?php
defined('BASEPATH') or exit('No direct script access allowed');

if (!function_exists('app_version')) {
    function app_version()
    {
        return '1.2.4';
    }
}

if (!function_exists('back_button')) {
    function back_button($fallback = 'dashboard', $label = 'Kembali')
    {
        $ci = get_instance();
        $url = site_url($fallback);
        return '<a href="#" onclick="if(document.referrer){window.history.back();}else{window.location=\'' . $url . '\';} return false;" class="btn btn-secondary btn-sm btn-responsive">
                    <i class="fas fa-arrow-left"></i> ' . $label . '
                </a>';
    }
}

if (!function_exists('responsive_title')) {
    function responsive_title($text, $icon_class = "fas fa-tags")
    {
        $icon_html = $icon_class ? '<i class="' . $icon_class . ' mr-2"></i>' : '';

        // Pisah teks jadi array kata
        $words = explode(' ', trim($text));

        if (count($words) > 2) {
            // Ambil 3 kata pertama
            $first_line = implode(' ', array_slice($words, 0, 3));
            // Sisanya untuk baris kedua
            $second_line = implode(' ', array_slice($words, 3));
            $text = $first_line . '<br>' . $second_line;
        }

        return '<h5 class="mb-0 title-responsive d-flex align-items-center">'
            . $icon_html . $text .
            '</h5>';
    }
}

if (!function_exists('responsive_title_blue')) {
    function responsive_title_blue($text, $icon_class = "fas fa-tags")
    {
        $icon_html = $icon_class ? '<i class="' . $icon_class . ' mr-2"></i>' : '';

        // Pisah teks jadi array kata
        $words = explode(' ', trim($text));

        if (count($words) > 2) {
            // Ambil 3 kata pertama
            $first_line = implode(' ', array_slice($words, 0, 3));
            // Sisanya untuk baris kedua
            $second_line = implode(' ', array_slice($words, 3));
            $text = $first_line . '<br>' . $second_line;
        }
        return '<h5 class="m-0 font-weight-bold text-primary title-blue-responsive d-flex align-items-center">'
            . $text .
            '</h5>';
    }
}

