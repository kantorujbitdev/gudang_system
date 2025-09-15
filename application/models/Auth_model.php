<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth_model extends CI_Model
{

    public function login($username, $password)
    {
        // Cek apakah tabel user ada
        if (!$this->db->table_exists('user')) {
            return FALSE;
        }

        $this->db->select('u.*, p.nama_perusahaan, r.nama_role');
        $this->db->from('user u');
        $this->db->join('perusahaan p', 'u.id_perusahaan = p.id_perusahaan', 'left');
        $this->db->join('role_user r', 'u.id_role = r.id_role');
        $this->db->where('u.username', $username);
        $this->db->where('u.aktif', 1);

        $query = $this->db->get();

        if ($query->num_rows() == 1) {
            $user = $query->row();

            // Verifikasi password
            if (password_verify($password, $user->password_hash)) {
                return $user;
            }
        }

        return FALSE;
    }

    public function update_last_login($id_user)
    {
        // Cek apakah tabel user ada
        if (!$this->db->table_exists('user')) {
            return FALSE;
        }

        $this->db->where('id_user', $id_user);
        return $this->db->update('user', ['last_login' => date('Y-m-d H:i:s')]);
    }

    public function set_remember_token($id_user, $token)
    {
        // Cek apakah tabel user ada
        if (!$this->db->table_exists('user')) {
            return FALSE;
        }

        $data = [
            'remember_token' => $token,
            'token_expires' => date('Y-m-d H:i:s', strtotime('+30 days'))
        ];

        $this->db->where('id_user', $id_user);
        return $this->db->update('user', $data);
    }

    public function clear_remember_token($token)
    {
        // Cek apakah tabel user ada
        if (!$this->db->table_exists('user')) {
            return FALSE;
        }

        $this->db->where('remember_token', $token);
        return $this->db->update('user', [
            'remember_token' => NULL,
            'token_expires' => NULL
        ]);
    }

    public function get_user_by_token($token)
    {
        // Cek apakah tabel user ada
        if (!$this->db->table_exists('user')) {
            return FALSE;
        }

        $this->db->where('remember_token', $token);
        $this->db->where('token_expires >', date('Y-m-d H:i:s'));
        $query = $this->db->get('user');

        if ($query->num_rows() == 1) {
            return $query->row();
        }

        return FALSE;
    }
}