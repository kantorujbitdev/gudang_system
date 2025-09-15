<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MY_Model extends CI_Model
{

    protected $table = '';
    protected $primary_key = 'id';
    protected $fillable = array();
    protected $protected = array();
    protected $timestamps = TRUE;

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // Get all records
    public function get_all($where = array(), $order_by = array())
    {
        $this->db->from($this->table);

        if (!empty($where)) {
            $this->db->where($where);
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
        return $this->db->get_where($this->table, array($this->primary_key => $id))->row();
    }

    // Insert record
    public function insert($data)
    {
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
        if ($this->timestamps) {
            $data['updated_at'] = date('Y-m-d H:i:s');
        }

        $this->db->where($this->primary_key, $id);
        return $this->db->update($this->table, $data);
    }

    // Delete record (soft delete)
    public function delete($id)
    {
        if (in_array('deleted_at', $this->fillable)) {
            $this->db->where($this->primary_key, $id);
            return $this->db->update($this->table, array('deleted_at' => date('Y-m-d H:i:s')));
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

        return $this->db->count_all_results();
    }

    // Get with pagination
    public function get_paginated($limit = 10, $offset = 0, $where = array(), $order_by = array())
    {
        $this->db->from($this->table);

        if (!empty($where)) {
            $this->db->where($where);
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

        if (!empty($order_by)) {
            foreach ($order_by as $field => $direction) {
                $this->db->order_by($field, $direction);
            }
        }

        return $this->db->get()->result();
    }
}