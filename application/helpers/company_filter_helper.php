<?php
defined('BASEPATH') or exit('No direct script access allowed');

if (!function_exists('get_company_filter')) {
    /**
     * Return filter perusahaan
     * @param string $alias alias tabel (default: kosong)
     * @return array
     */
    function get_company_filter($alias = '')
    {
        $CI =& get_instance();

        // Super Admin tidak perlu filter
        if ($CI->session->userdata('id_role') == 1) {
            return [];
        }

        $id_perusahaan = $CI->session->userdata('id_perusahaan');

        if ($alias) {
            return [$alias . '.id_perusahaan' => $id_perusahaan];
        }

        // fallback tanpa alias
        return ['id_perusahaan' => $id_perusahaan];
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