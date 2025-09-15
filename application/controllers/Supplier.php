<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Supplier extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('auth');
        $this->load->model('supplier_model');
        $this->load->library('form_validation');
        
        if (!$this->auth->is_logged_in()) {
            redirect('auth');
        }
        
        if (!$this->auth->has_permission('supplier')) {
            show_error('Anda tidak memiliki akses ke halaman ini', 403);
        }
    }

    public function index() {
        $data['title'] = 'Supplier';
        $data['content'] = 'supplier/index';
        $data['supplier'] = $this->supplier_model->get_all();
        
        $this->load->view('template/template', $data);
    }

    public function tambah() {
        $data['title'] = 'Tambah Supplier';
        $data['content'] = 'supplier/tambah';
        
        $this->form_validation->set_rules('nama_supplier', 'Nama Supplier', 'required');
        
        if ($this->form_validation->run() == FALSE) {
            $this->load->view('template/template', $data);
        } else {
            $data_supplier = array(
                'id_perusahaan' => $this->session->userdata('id_perusahaan'),
                'nama_supplier' => $this->input->post('nama_supplier'),
                'alamat' => $this->input->post('alamat'),
                'telepon' => $this->input->post('telepon'),
                'email' => $this->input->post('email'),
                'kontak_person' => $this->input->post('kontak_person'),
                'status_aktif' => 1
            );
            
            $this->supplier_model->insert($data_supplier);
            $this->session->set_flashdata('success', 'Supplier berhasil ditambahkan!');
            redirect('supplier');
        }
    }

    public function edit($id) {
        $data['title'] = 'Edit Supplier';
        $data['content'] = 'supplier/edit';
        $data['supplier'] = $this->supplier_model->get_by_id($id);
        
        if (!$data['supplier']) {
            show_404();
        }
        
        $this->form_validation->set_rules('nama_supplier', 'Nama Supplier', 'required');
        
        if ($this->form_validation->run() == FALSE) {
            $this->load->view('template/template', $data);
        } else {
            $data_supplier = array(
                'nama_supplier' => $this->input->post('nama_supplier'),
                'alamat' => $this->input->post('alamat'),
                'telepon' => $this->input->post('telepon'),
                'email' => $this->input->post('email'),
                'kontak_person' => $this->input->post('kontak_person'),
                'status_aktif' => $this->input->post('status_aktif')
            );
            
            $this->supplier_model->update($id, $data_supplier);
            $this->session->set_flashdata('success', 'Supplier berhasil diperbarui!');
            redirect('supplier');
        }
    }

    public function hapus($id) {
        $supplier = $this->supplier_model->get_by_id($id);
        
        if (!$supplier) {
            show_404();
        }
        
        $this->supplier_model->delete($id);
        $this->session->set_flashdata('success', 'Supplier berhasil dihapus!');
        redirect('supplier');
    }
}