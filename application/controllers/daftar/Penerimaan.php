<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Penerimaan extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Daftar/Penerimaan_model', 'penerimaan');
        $this->load->helper('form');
        $this->load->library('form_validation');

        // Cek akses menu
        $this->check_menu_access('daftar/penerimaan');
    }

    public function index()
    {
        $this->data['title'] = 'Daftar Penerimaan Barang';
        $this->data['gudang'] = $this->penerimaan->get_gudang();
        $this->data['supplier'] = $this->penerimaan->get_supplier();

        // Set filter default
        $filter = [
            'tanggal_awal' => date('Y-m-01'),
            'tanggal_akhir' => date('Y-m-d'),
            'status' => '',
            'id_gudang' => '',
            'id_supplier' => ''
        ];

        $this->data['filter'] = $filter;
        $this->data['penerimaan'] = $this->penerimaan->get_filtered($filter);

        $this->render_view('daftar/penerimaan/index');
    }

    public function filter()
    {
        $this->data['title'] = 'Daftar Penerimaan Barang';
        $this->data['gudang'] = $this->penerimaan->get_gudang();
        $this->data['supplier'] = $this->penerimaan->get_supplier();

        // Get filter from POST
        $filter = [
            'tanggal_awal' => $this->input->post('tanggal_awal') ?: date('Y-m-01'),
            'tanggal_akhir' => $this->input->post('tanggal_akhir') ?: date('Y-m-d'),
            'status' => $this->input->post('status'),
            'id_gudang' => $this->input->post('id_gudang'),
            'id_supplier' => $this->input->post('id_supplier')
        ];

        $this->data['filter'] = $filter;
        $this->data['penerimaan'] = $this->penerimaan->get_filtered($filter);

        $this->render_view('daftar/penerimaan/index');
    }

    public function detail($id_penerimaan)
    {
        $this->data['title'] = 'Detail Penerimaan Barang';
        $this->data['penerimaan'] = $this->penerimaan->get($id_penerimaan);
        $this->data['detail'] = $this->penerimaan->get_detail($id_penerimaan);

        if (!$this->data['penerimaan']) {
            show_404();
        }

        $this->render_view('daftar/penerimaan/detail');
    }

    public function cetak($id_penerimaan)
    {
        $this->data['penerimaan'] = $this->penerimaan->get($id_penerimaan);
        $this->data['detail'] = $this->penerimaan->get_detail($id_penerimaan);

        if (!$this->data['penerimaan']) {
            show_404();
        }

        $this->load->view('daftar/penerimaan/cetak', $this->data);
    }

    /* ==============================
     * Tambahan CRUD dengan Flashdata
     * ============================== */

    public function tambah()
    {
        $this->data['title'] = 'Tambah Penerimaan';
        $this->_set_validation_rules();

        if ($this->form_validation->run() === FALSE) {
            // render ulang form jika validasi gagal
            $this->data['gudang'] = $this->penerimaan->get_gudang();
            $this->data['supplier'] = $this->penerimaan->get_supplier();
            $this->render_view('daftar/penerimaan/form');
        } else {
            if ($this->penerimaan->insert($this->input->post())) {
                $this->session->set_flashdata('success', 'Data penerimaan berhasil ditambahkan.');
                redirect('daftar/penerimaan');
            } else {
                $this->session->set_flashdata('error', 'Gagal menambahkan data penerimaan.');
                redirect('daftar/penerimaan/tambah');
            }
        }
    }

    public function edit($id_penerimaan)
    {
        $this->data['title'] = 'Edit Penerimaan';
        $this->data['penerimaan'] = $this->penerimaan->get($id_penerimaan);

        if (!$this->data['penerimaan']) {
            show_404();
        }

        $this->_set_validation_rules();

        if ($this->form_validation->run() === FALSE) {
            $this->data['gudang'] = $this->penerimaan->get_gudang();
            $this->data['supplier'] = $this->penerimaan->get_supplier();
            $this->render_view('daftar/penerimaan/form');
        } else {
            if ($this->penerimaan->update($id_penerimaan, $this->input->post())) {
                $this->session->set_flashdata('success', 'Data penerimaan berhasil diperbarui.');
                redirect('daftar/penerimaan');
            } else {
                $this->session->set_flashdata('error', 'Gagal memperbarui data penerimaan.');
                redirect('daftar/penerimaan/edit/' . $id_penerimaan);
            }
        }
    }

    public function hapus($id_penerimaan)
    {
        if ($this->penerimaan->delete($id_penerimaan)) {
            $this->session->set_flashdata('success', 'Data penerimaan berhasil dihapus.');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus data penerimaan.');
        }
        redirect('daftar/penerimaan');
    }

    public function aktifkan($id_penerimaan)
    {
        if ($this->penerimaan->update($id_penerimaan, ['status' => 'aktif'])) {
            $this->session->set_flashdata('success', 'Data penerimaan berhasil diaktifkan.');
        } else {
            $this->session->set_flashdata('error', 'Gagal mengaktifkan data penerimaan.');
        }
        redirect('daftar/penerimaan');
    }

    public function nonaktifkan($id_penerimaan)
    {
        if ($this->penerimaan->update($id_penerimaan, ['status' => 'nonaktif'])) {
            $this->session->set_flashdata('success', 'Data penerimaan berhasil dinonaktifkan.');
        } else {
            $this->session->set_flashdata('error', 'Gagal menonaktifkan data penerimaan.');
        }
        redirect('daftar/penerimaan');
    }

    /* ==============================
     * Private Method
     * ============================== */
    private function _set_validation_rules()
    {
        $this->form_validation->set_rules('id_gudang', 'Gudang', 'required');
        $this->form_validation->set_rules('id_supplier', 'Supplier', 'required');
        $this->form_validation->set_rules('tanggal', 'Tanggal Penerimaan', 'required');
        $this->form_validation->set_rules('keterangan', 'Keterangan', 'trim');
    }
}
