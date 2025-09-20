<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'user';
        $this->primary_key = 'id_user';
        $this->fillable = array('nama', 'username', 'password_hash', 'id_role', 'id_perusahaan', 'email', 'telepon', 'created_by', 'aktif', 'foto_profil');
        $this->timestamps = TRUE;
    }

    public function get_all()
    {
        $user_role = $this->session->userdata('id_role');
        $user_perusahaan = $this->session->userdata('id_perusahaan');

        $this->db->select('u.*, r.nama_role, p.nama_perusahaan');
        $this->db->from('user u');
        $this->db->join('role_user r', 'u.id_role = r.id_role', 'left');
        $this->db->join('perusahaan p', 'u.id_perusahaan = p.id_perusahaan', 'left');

        // Filter berdasarkan role user
        if ($user_role != 1) { // Bukan Super Admin
            $this->db->where('u.id_perusahaan', $user_perusahaan);
        }

        $this->db->order_by('u.created_at', 'DESC');
        return $this->db->get()->result();
    }
    public function get_by_perusahaan($id_perusahaan)
    {
        $this->db->where('id_perusahaan', $id_perusahaan);
        $this->db->where('aktif', 1);
        return $this->db->get('user')->result();
    }
    public function get_by_role($id_role, $id_perusahaan = NULL)
    {
        $this->db->select('u.*, r.nama_role, p.nama_perusahaan');
        $this->db->from('user u');
        $this->db->join('role_user r', 'u.id_role = r.id_role', 'left');
        $this->db->join('perusahaan p', 'u.id_perusahaan = p.id_perusahaan', 'left');
        $this->db->where('u.id_role', $id_role);

        if ($id_perusahaan) {
            $this->db->where('u.id_perusahaan', $id_perusahaan);
        }

        $this->db->where('u.aktif', 1);
        $this->db->order_by('u.nama', 'ASC');
        return $this->db->get()->result();
    }

    public function get_sales($id_perusahaan = NULL)
    {
        return $this->get_by_role(3, $id_perusahaan); // Role ID 3 = Sales
    }

    public function get_packing($id_perusahaan = NULL)
    {
        return $this->get_by_role(4, $id_perusahaan); // Role ID 4 = Admin Packing
    }

    public function get($id_user)
    {
        $user_role = $this->session->userdata('id_role');
        $user_perusahaan = $this->session->userdata('id_perusahaan');

        $this->db->select('u.*, r.nama_role, p.nama_perusahaan');
        $this->db->from('user u');
        $this->db->join('role_user r', 'u.id_role = r.id_role', 'left');
        $this->db->join('perusahaan p', 'u.id_perusahaan = p.id_perusahaan', 'left');
        $this->db->where('u.id_user', $id_user);

        // Filter berdasarkan role user
        if ($user_role != 1) { // Bukan Super Admin
            $this->db->where('u.id_perusahaan', $user_perusahaan);
        }

        return $this->db->get()->row();
    }

    public function check_unique_username($username, $id_user = NULL)
    {
        $this->db->where('username', $username);
        if ($id_user) {
            $this->db->where('id_user !=', $id_user);
        }
        return $this->db->get($this->table)->num_rows() == 0;
    }

    public function check_unique_email($email, $id_user = NULL)
    {
        $this->db->where('email', $email);
        if ($id_user) {
            $this->db->where('id_user !=', $id_user);
        }
        return $this->db->get($this->table)->num_rows() == 0;
    }

    public function update_last_login($id_user)
    {
        $this->db->where('id_user', $id_user);
        return $this->db->update($this->table, array('last_login' => date('Y-m-d H:i:s')));
    }

    public function update_status($id_user, $status)
    {
        $this->db->where('id_user', $id_user);
        return $this->db->update($this->table, array('aktif' => $status));
    }

    public function reset_password($id_user, $new_password)
    {
        $this->db->where('id_user', $id_user);
        return $this->db->update($this->table, array(
            'password_hash' => password_hash($new_password, PASSWORD_DEFAULT)
        ));
    }
}