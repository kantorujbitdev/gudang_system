<?php
defined('BASEPATH') or exit('No direct script access allowed');

if (!function_exists('get_company_filter')) {
    function get_company_filter()
    {
        $CI =& get_instance();

        // Super Admin tidak perlu filter
        if ($CI->session->userdata('id_role') == 1) {
            return '';
        }

        // Filter berdasarkan perusahaan user
        $id_perusahaan = $CI->session->userdata('id_perusahaan');

        return "AND id_perusahaan = $id_perusahaan";
    }
}

if (!function_exists('get_company_filter_query')) {
    function get_company_filter_query()
    {
        $CI =& get_instance();

        // Super Admin tidak perlu filter
        if ($CI->session->userdata('id_role') == 1) {
            return array();
        }

        // Filter berdasarkan perusahaan user
        $id_perusahaan = $CI->session->userdata('id_perusahaan');

        return array('id_perusahaan' => $id_perusahaan);
    }
}