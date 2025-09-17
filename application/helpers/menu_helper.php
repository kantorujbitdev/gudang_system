<?php
defined('BASEPATH') or exit('No direct script access allowed');

if (!function_exists('render_menu')) {
    function render_menu($menu_items, $parent_id = NULL)
    {
        $html = '';
        foreach ($menu_items as $menu) {
            if ($menu->id_parent == $parent_id) {
                $has_children = isset($menu->children) && count($menu->children) > 0;
                $url = $menu->url ? site_url($menu->url) : '#';
                $active = ($menu->url == uri_string()) ? 'active' : '';

                if ($has_children) {
                    $html .= '<li class="nav-item">';
                    $html .= '<a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapse' . $menu->id_menu . '" aria-expanded="true" aria-controls="collapse' . $menu->id_menu . '">';
                    $html .= '<i class="' . $menu->icon . '"></i>';
                    $html .= '<span>' . $menu->nama_menu . '</span>';
                    $html .= '</a>';
                    $html .= '<div id="collapse' . $menu->id_menu . '" class="collapse" aria-labelledby="heading' . $menu->id_menu . '" data-parent="#accordionSidebar">';
                    $html .= '<div class="bg-white py-2 collapse-inner rounded">';
                    $html .= render_menu($menu_items, $menu->id_menu);
                    $html .= '</div>';
                    $html .= '</div>';
                    $html .= '</li>';
                } else {
                    $html .= '<li class="nav-item">';
                    $html .= '<a class="nav-link ' . $active . '" href="' . $url . '">';
                    $html .= '<i class="' . $menu->icon . '"></i>';
                    $html .= '<span>' . $menu->nama_menu . '</span></a>';
                    $html .= '</li>';
                }
            }
        }
        return $html;
    }
}

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