<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Approval extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Pengaturan/Approval_model', 'approval');
        $this->load->model('Menu_model');
        $this->load->helper('form');
        $this->load->library('form_validation');

        // Cek akses menu
        $this->check_menu_access('pengaturan/approval');
    }

    public function index()
    {
        $this->data['title'] = 'Approval Flow';
        $this->data['approval_flows'] = $this->approval->get_all_approval_flows();
        $this->data['can_edit'] = $this->check_permission('pengaturan/approval', 'edit');

        $this->render_view('pengaturan/approval/index');
    }

    public function diagram()
    {
        $this->data['title'] = 'Diagram Approval Flow';
        $this->data['approval_flows'] = $this->approval->get_all_approval_flows();

        $this->render_view('pengaturan/approval/diagram');
    }

    public function edit($tipe_transaksi)
    {
        // Check permission
        if (!$this->check_permission('pengaturan/approval', 'edit')) {
            $this->data['error'] = 'Anda tidak memiliki izin untuk mengubah approval flow!';
            redirect('pengaturan/approval');
        }

        $this->data['title'] = 'Edit Approval Flow - ' . ucfirst(str_replace('_', ' ', $tipe_transaksi));
        $this->data['tipe_transaksi'] = $tipe_transaksi;
        $this->data['approval_flows'] = $this->approval->get_approval_flow_by_tipe($tipe_transaksi);
        $this->data['roles'] = $this->approval->get_all_roles();

        $this->render_view('pengaturan/approval/form');
    }

    public function update()
    {
        // Check permission
        if (!$this->check_permission('pengaturan/approval', 'edit')) {
            $this->data['error'] = 'Anda tidak memiliki izin untuk mengubah approval flow!';
            redirect('pengaturan/approval');
        }

        if ($this->input->post()) {
            $tipe_transaksi = $this->input->post('tipe_transaksi');
            $status_daris = $this->input->post('status_dari');
            $status_kes = $this->input->post('status_ke');
            $id_roles = $this->input->post('id_role');
            $urutans = $this->input->post('urutan');

            // Delete existing approval flow for this transaction type
            $this->approval->delete_by_tipe($tipe_transaksi);

            // Insert new approval flow
            foreach ($status_daris as $key => $status_dari) {
                if (!empty($status_dari) && !empty($status_kes[$key]) && !empty($id_roles[$key])) {
                    $data = [
                        'tipe_transaksi' => $tipe_transaksi,
                        'status_dari' => $status_dari,
                        'status_ke' => $status_kes[$key],
                        'id_role' => $id_roles[$key],
                        'urutan' => $urutans[$key] ?: 0,
                        'status_aktif' => 1
                    ];

                    $this->approval->insert($data);
                }
            }

            $this->data['success'] = 'Approval flow berhasil diperbarui';
            redirect('pengaturan/approval');
        }
    }
}