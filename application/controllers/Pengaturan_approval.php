<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pengaturan_approval extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('auth');
        $this->load->model('approval_model');
        $this->load->model('role_model');
        $this->load->library('form_validation');

        if (!$this->auth->is_logged_in()) {
            redirect('auth');
        }

        if (!$this->auth->has_permission('pengaturan/approval')) {
            show_error('Anda tidak memiliki akses ke halaman ini', 403);
        }
    }

    public function index()
    {
        $data['title'] = 'Approval Flow';
        $data['content'] = 'pengaturan/approval/index';
        $data['approval'] = $this->approval_model->get_all();
        $data['roles'] = $this->role_model->get_all();

        $this->load->view('template/template', $data);
    }

    public function tambah()
    {
        $data['title'] = 'Tambah Approval Flow';
        $data['content'] = 'pengaturan/approval/tambah';
        $data['roles'] = $this->role_model->get_all();

        $this->form_validation->set_rules('tipe_transaksi', 'Tipe Transaksi', 'required');
        $this->form_validation->set_rules('status_dari', 'Status Dari', 'required');
        $this->form_validation->set_rules('status_ke', 'Status Ke', 'required');
        $this->form_validation->set_rules('id_role', 'Role', 'required');
        $this->form_validation->set_rules('urutan', 'Urutan', 'required|numeric');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('template/template', $data);
        } else {
            $data_approval = array(
                'tipe_transaksi' => $this->input->post('tipe_transaksi'),
                'status_dari' => $this->input->post('status_dari'),
                'status_ke' => $this->input->post('status_ke'),
                'id_role' => $this->input->post('id_role'),
                'urutan' => $this->input->post('urutan'),
                'status_aktif' => 1
            );

            $this->approval_model->insert($data_approval);
            $this->session->set_flashdata('success', 'Approval flow berhasil ditambahkan!');
            redirect('pengaturan/approval');
        }
    }

    public function edit($id)
    {
        $data['title'] = 'Edit Approval Flow';
        $data['content'] = 'pengaturan/approval/edit';
        $data['approval'] = $this->approval_model->get_by_id($id);
        $data['roles'] = $this->role_model->get_all();

        if (!$data['approval']) {
            show_404();
        }

        $this->form_validation->set_rules('tipe_transaksi', 'Tipe Transaksi', 'required');
        $this->form_validation->set_rules('status_dari', 'Status Dari', 'required');
        $this->form_validation->set_rules('status_ke', 'Status Ke', 'required');
        $this->form_validation->set_rules('id_role', 'Role', 'required');
        $this->form_validation->set_rules('urutan', 'Urutan', 'required|numeric');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('template/template', $data);
        } else {
            $data_approval = array(
                'tipe_transaksi' => $this->input->post('tipe_transaksi'),
                'status_dari' => $this->input->post('status_dari'),
                'status_ke' => $this->input->post('status_ke'),
                'id_role' => $this->input->post('id_role'),
                'urutan' => $this->input->post('urutan'),
                'status_aktif' => $this->input->post('status_aktif')
            );

            $this->approval_model->update($id, $data_approval);
            $this->session->set_flashdata('success', 'Approval flow berhasil diperbarui!');
            redirect('pengaturan/approval');
        }
    }

    public function hapus($id)
    {
        $approval = $this->approval_model->get_by_id($id);

        if (!$approval) {
            show_404();
        }

        $this->approval_model->delete($id);
        $this->session->set_flashdata('success', 'Approval flow berhasil dihapus!');
        redirect('pengaturan/approval');
    }
}