<?php
defined('BASEPATH') or exit('No direct script access allowed');

if (!function_exists('send_notification')) {
    function send_notification($user_id, $message, $type = 'info')
    {
        $CI =& get_instance();

        $data = array(
            'id_user' => $user_id,
            'message' => $message,
            'type' => $type,
            'is_read' => 0,
            'created_at' => date('Y-m-d H:i:s')
        );

        return $CI->db->insert('notifications', $data);
    }
}

if (!function_exists('get_unread_notifications')) {
    function get_unread_notifications($user_id)
    {
        $CI =& get_instance();

        $CI->db->where('id_user', $user_id);
        $CI->db->where('is_read', 0);
        $CI->db->order_by('created_at', 'DESC');

        return $CI->db->get('notifications')->result();
    }
}