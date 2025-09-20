<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Supplier extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('setup/Supplier_model');
        $this->load->model('setup/Perusahaan_model');
        $this->load->helper('form');
        $this->load->library('form_validation');

        // Cek akses menu
        $this->check_menu_access('setup/supplier');
    }

    public function index()
    {
        $this->data['title'] = 'Manajemen Supplier';
        $this->data['supplier'] = $this->Supplier_model->get_with_perusahaan();

        $this->render_view('setup/supplier/index');
    }

    public function tambah()
    {
        $this->data['title'] = 'Tambah Supplier';
        $this->data['perusahaan'] = $this->Perusahaan_model->get_active();

        if ($this->input->post()) {
            $this->form_validation->set_rules('nama_supplier', 'Nama Supplier', 'required|trim|max_length[100]');
            $this->form_validation->set_rules('id_perusahaan', 'Perusahaan', 'required');
            $this->form_validation->set_rules('alamat', 'Alamat', 'trim');
            $this->form_validation->set_rules('telepon', 'Telepon', 'trim|max_length[20]');
            $this->form_validation->set_rules('email', 'Email', 'trim|valid_email|max_length[100]');

            if ($this->form_validation->run() == TRUE) {
                $nama_supplier = $this->input->post('nama_supplier');
                $id_perusahaan = $this->input->post('id_perusahaan');

                // Check unique name per company
                if (!$this->Supplier_model->check_unique_name($nama_supplier, $id_perusahaan)) {
                    $this->data['error'] = 'Nama supplier sudah ada di perusahaan yang sama!';
                    $this->render_view('setup/supplier/form');
                    return;
                }

                $data = array(
                    'nama_supplier' => $nama_supplier,
                    'id_perusahaan' => $id_perusahaan,
                    'alamat' => $this->input->post('alamat'),
                    'telepon' => $this->input->post('telepon'),
                    'email' => $this->input->post('email'),
                    'status_aktif' => 1
                );

                if ($this->Supplier_model->insert($data)) {
                    $this->data['success'] = 'Supplier berhasil ditambahkan!';
                    redirect('setup/supplier');
                } else {
                    $this->data['error'] = 'Gagal menambahkan supplier!';
                }
            }
        }

        $this->render_view('setup/supplier/form');
    }

    public function nonaktif($id)
    {
        if ($this->Supplier_model->update_status($id, 0)) {
            $this->data['success'] = 'Supplier berhasil dinonaktifkan';
        } else {
            $this->data['error'] = 'Gagal menonaktifkan supplier';
        }
        redirect('setup/supplier');
    }

    public function aktif($id)
    {
        if ($this->Supplier_model->update_status($id, 1)) {
            $this->data['success'] = 'Supplier berhasil diaktifkan kembali';
        } else {
            $this->data['error'] = 'Gagal mengaktifkan supplier';
        }
        redirect('setup/supplier');
    }

    public function edit($id_supplier)
    {
        $this->data['title'] = 'Edit Supplier';
        $this->data['supplier'] = $this->Supplier_model->get($id_supplier);

        if (!$this->data['supplier']) {
            show_404();
        }

        // Jika Super Admin, tampilkan pilihan perusahaan
        if ($this->session->userdata('id_role') == 1) {
            $this->load->model('setup/Perusahaan_model');
            $this->data['perusahaan'] = $this->Perusahaan_model->get_all();
        }

        if ($this->input->post()) {
            $this->form_validation->set_rules('nama_supplier', 'Nama Supplier', 'required|trim|max_length[100]');
            $this->form_validation->set_rules('alamat', 'Alamat', 'trim');
            $this->form_validation->set_rules('telepon', 'Telepon', 'trim|max_length[20]');
            $this->form_validation->set_rules('email', 'Email', 'trim|valid_email|max_length[100]');
            $this->form_validation->set_rules('status_aktif', 'Status', 'required|in_list[0,1]');

            // Jika Super Admin, validasi perusahaan
            if ($this->session->userdata('id_role') == 1) {
                $this->form_validation->set_rules('id_perusahaan', 'Perusahaan', 'required');
            }

            if ($this->form_validation->run() == TRUE) {
                $nama_supplier = $this->input->post('nama_supplier');
                $id_perusahaan = $this->session->userdata('id_role') == 1 ?
                    $this->input->post('id_perusahaan') :
                    $this->session->userdata('id_perusahaan');

                // Check unique name (excluding current record)
                if (!$this->Supplier_model->check_unique_name($nama_supplier, $id_perusahaan, $id_supplier)) {
                    $this->data['error'] = 'Nama supplier sudah ada untuk perusahaan ini!';
                    $this->render_view('setup/supplier/form');
                    return;
                }

                $data = array(
                    'nama_supplier' => $nama_supplier,
                    'alamat' => $this->input->post('alamat'),
                    'telepon' => $this->input->post('telepon'),
                    'email' => $this->input->post('email'),
                    'status_aktif' => $this->input->post('status_aktif')
                );

                // Jika Super Admin, update perusahaan
                if ($this->session->userdata('id_role') == 1) {
                    $data['id_perusahaan'] = $id_perusahaan;
                }

                if ($this->Supplier_model->update($id_supplier, $data)) {
                    $this->data['success'] = 'Supplier berhasil diperbarui!';
                    redirect('setup/supplier');
                } else {
                    $this->data['error'] = 'Gagal memperbarui supplier!';
                }
            }
        }

        $this->render_view('setup/supplier/form');
    }

    public function hapus($id_supplier)
    {
        $supplier = $this->Supplier_model->get($id_supplier);

        if (!$supplier) {
            show_404();
        }

        // Check if supplier has related transactions
        $this->load->model('transaksi/Pembelian_model');
        $pembelian_count = $this->Pembelian_model->count_by_supplier($id_supplier);

        if ($pembelian_count > 0) {
            $this->data['error'] = 'Supplier tidak dapat dihapus karena masih memiliki transaksi terkait!';
            redirect('setup/supplier');
        }

        if ($this->Supplier_model->delete($id_supplier)) {
            $this->data['success'] = 'Supplier berhasil dihapus!';
        } else {
            $this->data['error'] = 'Gagal menghapus supplier!';
        }

        redirect('setup/supplier');
    }

    public function detail($id_supplier)
    {
        $this->data['title'] = 'Detail Supplier';
        $this->data['supplier'] = $this->Supplier_model->get($id_supplier);

        if (!$this->data['supplier']) {
            show_404();
        }

        // Get related transactions
        $this->load->model('Pembelian_model');
        $this->data['pembelian'] = $this->Pembelian_model->get_by_supplier($id_supplier, 10);

        $this->render_view('setup/supplier/detail');
    }
}