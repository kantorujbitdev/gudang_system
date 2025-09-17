<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MY_Model extends CI_Model
{
    protected $table = '';
    protected $primary_key = 'id';
    protected $fillable = array();
    protected $protected = array();
    protected $timestamps = TRUE;
    protected $soft_delete = FALSE;

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // Get all records
    public function get_all_data($where = array(), $order_by = array())
    {
        $this->db->from($this->table);
        if (!empty($where)) {
            $this->db->where($where);
        }
        if ($this->soft_delete) {
            $this->db->where('deleted_at IS NULL');
        }
        if (!empty($order_by)) {
            foreach ($order_by as $field => $direction) {
                $this->db->order_by($field, $direction);
            }
        }
        return $this->db->get()->result();
    }

    // Get single record
    public function get($id)
    {
        $this->db->where($this->primary_key, $id);
        if ($this->soft_delete) {
            $this->db->where('deleted_at IS NULL');
        }
        return $this->db->get($this->table)->row();
    }

    // Insert record
    public function insert($data)
    {
        // Remove protected fields
        foreach ($this->protected as $field) {
            if (isset($data[$field])) {
                unset($data[$field]);
            }
        }

        if ($this->timestamps) {
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['updated_at'] = date('Y-m-d H:i:s');
        }

        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    // Update record
    public function update($id, $data)
    {
        // Remove protected fields
        foreach ($this->protected as $field) {
            if (isset($data[$field])) {
                unset($data[$field]);
            }
        }

        if ($this->timestamps) {
            $data['updated_at'] = date('Y-m-d H:i:s');
        }

        $this->db->where($this->primary_key, $id);
        return $this->db->update($this->table, $data);
    }

    // Delete record (soft delete)
    public function delete($id)
    {
        if ($this->soft_delete) {
            $data = array(
                'deleted_at' => date('Y-m-d H:i:s'),
                'status_aktif' => 0
            );
            return $this->update($id, $data);
        } else {
            $this->db->where($this->primary_key, $id);
            return $this->db->delete($this->table);
        }
    }

    // Hard delete
    public function force_delete($id)
    {
        $this->db->where($this->primary_key, $id);
        return $this->db->delete($this->table);
    }

    // Count records
    public function count($where = array())
    {
        $this->db->from($this->table);
        if (!empty($where)) {
            $this->db->where($where);
        }
        if ($this->soft_delete) {
            $this->db->where('deleted_at IS NULL');
        }
        return $this->db->count_all_results();
    }

    // Get with pagination
    public function get_paginated($limit = 10, $offset = 0, $where = array(), $order_by = array())
    {
        $this->db->from($this->table);
        if (!empty($where)) {
            $this->db->where($where);
        }
        if ($this->soft_delete) {
            $this->db->where('deleted_at IS NULL');
        }
        if (!empty($order_by)) {
            foreach ($order_by as $field => $direction) {
                $this->db->order_by($field, $direction);
            }
        }
        $this->db->limit($limit, $offset);
        return $this->db->get()->result();
    }

    // Get with join
    public function get_with_join($joins = array(), $where = array(), $select = '*', $order_by = array())
    {
        $this->db->select($select);
        $this->db->from($this->table);
        foreach ($joins as $join) {
            $this->db->join($join['table'], $join['condition'], $join['type']);
        }
        if (!empty($where)) {
            $this->db->where($where);
        }
        if ($this->soft_delete) {
            $this->db->where($this->table . '.deleted_at IS NULL');
        }
        if (!empty($order_by)) {
            foreach ($order_by as $field => $direction) {
                $this->db->order_by($field, $direction);
            }
        }
        return $this->db->get()->result();
    }
}