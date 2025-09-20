<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pelanggan extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('setup/Pelanggan_model');
        $this->load->model('setup/Perusahaan_model');
        $this->load->helper('form');
        $this->load->library('form_validation');

        // Cek akses menu
        $this->check_menu_access('setup/pelanggan');
    }

    public function index()
    {
        $this->data['title'] = 'Manajemen Pelanggan';
        $this->data['pelanggan'] = $this->Pelanggan_model->get_with_perusahaan();

        $this->render_view('setup/pelanggan/index');
    }

    public function tambah()
    {
        $this->data['title'] = 'Tambah Pelanggan';
        $this->data['perusahaan'] = $this->Perusahaan_model->get_active();

        if ($this->input->post()) {
            $this->_set_validation_rules();

            if ($this->form_validation->run() == TRUE) {
                $nama_pelanggan = $this->input->post('nama_pelanggan');
                $id_perusahaan = $this->input->post('id_perusahaan');

                // Cek unique name per perusahaan
                if (!$this->Pelanggan_model->check_unique_name($nama_pelanggan, $id_perusahaan)) {
                    $this->session->set_flashdata('error', 'Nama pelanggan sudah ada di perusahaan yang sama!');
                    redirect('setup/pelanggan/tambah');
                }

                $data = array(
                    'nama_pelanggan' => $nama_pelanggan,
                    'id_perusahaan' => $id_perusahaan,
                    'alamat' => $this->input->post('alamat'),
                    'telepon' => $this->input->post('telepon'),
                    'email' => $this->input->post('email'),
                    'tipe_pelanggan' => $this->input->post('tipe_pelanggan'),
                    'status_aktif' => 1
                );

                if ($this->Pelanggan_model->insert($data)) {
                    $this->session->set_flashdata('success', 'Pelanggan berhasil ditambahkan!');
                } else {
                    $this->session->set_flashdata('error', 'Gagal menambahkan pelanggan!');
                }
                redirect('setup/pelanggan');
            }
        }

        $this->render_view('setup/pelanggan/form');
    }

    public function edit($id_pelanggan)
    {
        $this->data['title'] = 'Edit Pelanggan';
        $this->data['pelanggan'] = $this->Pelanggan_model->get($id_pelanggan);

        if (!$this->data['pelanggan']) {
            show_404();
        }

        // Jika Super Admin → tampilkan semua perusahaan
        if ($this->session->userdata('id_role') == 1) {
            $this->data['perusahaan'] = $this->Perusahaan_model->get_all();
        }

        if ($this->input->post()) {
            $this->_set_validation_rules(true);

            if ($this->form_validation->run() == TRUE) {
                $nama_pelanggan = $this->input->post('nama_pelanggan');
                $id_perusahaan = $this->session->userdata('id_role') == 1
                    ? $this->input->post('id_perusahaan')
                    : $this->session->userdata('id_perusahaan');

                // Cek unique name (exclude record sekarang)
                if (!$this->Pelanggan_model->check_unique_name($nama_pelanggan, $id_perusahaan, $id_pelanggan)) {
                    $this->session->set_flashdata('error', 'Nama pelanggan sudah ada untuk perusahaan ini!');
                    redirect('setup/pelanggan/edit/' . $id_pelanggan);
                }

                $data = array(
                    'nama_pelanggan' => $nama_pelanggan,
                    'alamat' => $this->input->post('alamat'),
                    'telepon' => $this->input->post('telepon'),
                    'email' => $this->input->post('email'),
                    'tipe_pelanggan' => $this->input->post('tipe_pelanggan'),
                    'status_aktif' => $this->input->post('status_aktif')
                );

                if ($this->session->userdata('id_role') == 1) {
                    $data['id_perusahaan'] = $id_perusahaan;
                }

                if ($this->Pelanggan_model->update($id_pelanggan, $data)) {
                    $this->session->set_flashdata('success', 'Pelanggan berhasil diperbarui!');
                } else {
                    $this->session->set_flashdata('error', 'Gagal memperbarui pelanggan!');
                }
                redirect('setup/pelanggan');
            }
        }

        $this->render_view('setup/pelanggan/form');
    }

    public function hapus($id_pelanggan)
    {
        $pelanggan = $this->Pelanggan_model->get($id_pelanggan);
        if (!$pelanggan) {
            show_404();
        }

        // Cek apakah ada transaksi terkait
        $this->load->model('transaksi/Penjualan_model');
        $penjualan_count = $this->Penjualan_model->count_by_pelanggan($id_pelanggan);

        if ($penjualan_count > 0) {
            $this->session->set_flashdata('error', 'Pelanggan tidak dapat dihapus karena masih memiliki transaksi terkait!');
            redirect('setup/pelanggan');
        }

        if ($this->Pelanggan_model->delete($id_pelanggan)) {
            $this->session->set_flashdata('success', 'Pelanggan berhasil dihapus!');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus pelanggan!');
        }

        redirect('setup/pelanggan');
    }

    public function aktif($id)
    {
        if ($this->Pelanggan_model->update_status($id, 1)) {
            $this->session->set_flashdata('success', 'Pelanggan berhasil diaktifkan kembali!');
        } else {
            $this->session->set_flashdata('error', 'Gagal mengaktifkan pelanggan!');
        }
        redirect('setup/pelanggan');
    }

    public function nonaktif($id)
    {
        if ($this->Pelanggan_model->update_status($id, 0)) {
            $this->session->set_flashdata('success', 'Pelanggan berhasil dinonaktifkan!');
        } else {
            $this->session->set_flashdata('error', 'Gagal menonaktifkan pelanggan!');
        }
        redirect('setup/pelanggan');
    }

    public function detail($id_pelanggan)
    {
        $this->data['title'] = 'Detail Pelanggan';
        $this->data['pelanggan'] = $this->Pelanggan_model->get($id_pelanggan);

        if (!$this->data['pelanggan']) {
            show_404();
        }

        $this->load->model('Penjualan_model');
        $this->data['penjualan'] = $this->Penjualan_model->get_by_pelanggan($id_pelanggan, 10);

        $this->render_view('setup/pelanggan/detail');
    }

    // -------------------
    // Private Helper
    // -------------------
    private function _set_validation_rules($is_edit = false)
    {
        $this->form_validation->set_rules('nama_pelanggan', 'Nama Pelanggan', 'required|trim|max_length[100]');
        $this->form_validation->set_rules('alamat', 'Alamat', 'trim');
        $this->form_validation->set_rules('telepon', 'Telepon', 'trim|max_length[20]');
        $this->form_validation->set_rules('email', 'Email', 'trim|valid_email|max_length[100]');
        $this->form_validation->set_rules('tipe_pelanggan', 'Tipe Pelanggan', 'required|in_list[distributor,konsumen]');

        if ($is_edit) {
            $this->form_validation->set_rules('status_aktif', 'Status', 'required|in_list[0,1]');
        } else {
            $this->form_validation->set_rules('id_perusahaan', 'Perusahaan', 'required');
        }
    }
}
