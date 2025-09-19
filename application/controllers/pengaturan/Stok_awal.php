<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Stok_awal extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Pengaturan/Stok_awal_model', 'stok_awal');
        $this->load->model('setup/Barang_model', 'barang');
        $this->load->model('setup/Gudang_model', 'gudang');
        $this->load->helper('form');
        $this->load->library('form_validation');

        // Cek akses menu
        $this->check_menu_access('pengaturan/stok_awal');
    }

    public function index()
    {
        $this->data['title'] = 'Stok Awal';
        $this->data['stok_awal'] = $this->stok_awal->get_all();
        $this->data['can_create'] = $this->check_permission('pengaturan/stok_awal', 'create');
        $this->data['can_edit'] = $this->check_permission('pengaturan/stok_awal', 'edit');
        $this->data['can_delete'] = $this->check_permission('pengaturan/stok_awal', 'delete');

        $this->render_view('pengaturan/stok_awal/index');
    }

    public function tambah()
    {
        // Check permission
        if (!$this->check_permission('pengaturan/stok_awal', 'create')) {
            $this->data['error'] = 'Anda tidak memiliki izin untuk menambah stok awal!';
            $this->render_view('pengaturan/stok_awal');
        }

        $this->data['title'] = 'Tambah Stok Awal';
        $this->data['barang'] = $this->barang->get_all();
        $this->data['gudang'] = $this->gudang->get_all();

        $this->form_validation->set_rules('id_barang', 'Barang', 'required');
        $this->form_validation->set_rules('id_gudang', 'Gudang', 'required');
        $this->form_validation->set_rules('qty_awal', 'Jumlah Stok', 'required|numeric|greater_than_equal_to[0]');

        if ($this->form_validation->run() == FALSE) {
            $this->render_view('pengaturan/stok_awal/form');
        } else {
            $data_insert = [
                'id_barang' => $this->input->post('id_barang'),
                'id_gudang' => $this->input->post('id_gudang'),
                'id_perusahaan' => $this->session->userdata('id_perusahaan'),
                'qty_awal' => $this->input->post('qty_awal'),
                'keterangan' => $this->input->post('keterangan'),
                'created_by' => $this->session->userdata('id_user'),
                'created_at' => date('Y-m-d H:i:s')
            ];

            // Check if stock already exists
            $existing_stok = $this->stok_awal->get_by_barang_gudang(
                $this->input->post('id_barang'),
                $this->input->post('id_gudang')
            );

            if ($existing_stok) {
                $this->data['error'] = 'Stok awal untuk barang ini di gudang ini sudah ada!';
                $this->render_view('pengaturan/stok_awal/form');
                return;
            }

            if ($this->stok_awal->insert($data_insert)) {
                // Update stok gudang
                $this->update_stok_gudang(
                    $this->input->post('id_barang'),
                    $this->input->post('id_gudang'),
                    $this->input->post('qty_awal')
                );

                $this->data['success'] = 'Stok awal berhasil ditambahkan';
                $this->render_view('pengaturan/stok_awal');
            } else {
                $this->data['error'] = 'Gagal menambahkan stok awal!';
                $this->render_view('pengaturan/stok_awal/form');
            }
        }
    }

    public function edit($id_stok_awal)
    {
        // Check permission
        if (!$this->check_permission('pengaturan/stok_awal', 'edit')) {
            $this->data['error'] = 'Anda tidak memiliki izin untuk mengubah stok awal!';
            $this->render_view('pengaturan/stok_awal');
        }

        $this->data['title'] = 'Edit Stok Awal';
        $this->data['stok_awal'] = $this->stok_awal->get($id_stok_awal);
        $this->data['barang'] = $this->barang->get_all();
        $this->data['gudang'] = $this->gudang->get_all();

        if (!$this->data['stok_awal']) {
            show_404();
        }

        $this->form_validation->set_rules('id_barang', 'Barang', 'required');
        $this->form_validation->set_rules('id_gudang', 'Gudang', 'required');
        $this->form_validation->set_rules('qty_awal', 'Jumlah Stok', 'required|numeric|greater_than_equal_to[0]');

        if ($this->form_validation->run() == FALSE) {
            $this->render_view('pengaturan/stok_awal/form');
        } else {
            $data_update = [
                'id_barang' => $this->input->post('id_barang'),
                'id_gudang' => $this->input->post('id_gudang'),
                'qty_awal' => $this->input->post('qty_awal'),
                'keterangan' => $this->input->post('keterangan')
            ];

            if ($this->stok_awal->update($id_stok_awal, $data_update)) {
                // Update stok gudang
                $this->update_stok_gudang(
                    $this->input->post('id_barang'),
                    $this->input->post('id_gudang'),
                    $this->input->post('qty_awal')
                );

                $this->data['success'] = 'Stok awal berhasil diperbarui';
                $this->render_view('pengaturan/stok_awal');
            } else {
                $this->data['error'] = 'Gagal memperbarui stok awal!';
                $this->render_view('pengaturan/stok_awal/form');
            }
        }
    }

    public function hapus($id_stok_awal)
    {
        // Check permission
        if (!$this->check_permission('pengaturan/stok_awal', 'delete')) {
            $this->data['error'] = 'Anda tidak memiliki izin untuk menghapus stok awal!';
            $this->render_view('pengaturan/stok_awal');
        }

        $stok_awal = $this->stok_awal->get($id_stok_awal);

        if (!$stok_awal) {
            show_404();
        }

        if ($this->stok_awal->delete($id_stok_awal)) {
            // Reset stok gudang
            $this->update_stok_gudang(
                $stok_awal->id_barang,
                $stok_awal->id_gudang,
                0
            );

            $this->data['success'] = 'Stok awal berhasil dihapus';
        } else {
            $this->data['error'] = 'Gagal menghapus stok awal!';
        }
        $this->render_view('pengaturan/stok_awal');
    }

    public function import()
    {
        // Check permission
        if (!$this->check_permission('pengaturan/stok_awal', 'create')) {
            $this->data['error'] = 'Anda tidak memiliki izin untuk mengimpor stok awal!';
            $this->render_view('pengaturan/stok_awal');
        }

        $this->data['title'] = 'Import Stok Awal';

        if ($this->input->post()) {
            $config['upload_path'] = './uploads/temp/';
            $config['allowed_types'] = 'xlsx|xls|csv';
            $config['max_size'] = 2048;
            $config['file_name'] = 'stok_awal_' . time();

            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('file_import')) {
                $this->data['error'] = $this->upload->display_errors();
            } else {
                $file_data = $this->upload->data();
                $file_path = './uploads/temp/' . $file_data['file_name'];

                // Load PHPExcel library
                $this->load->library('PHPExcel');

                try {
                    $inputFileType = PHPExcel_IOFactory::identify($file_path);
                    $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                    $objPHPExcel = $objReader->load($file_path);

                    $all_data_in_sheet = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);

                    $success_count = 0;
                    $error_count = 0;
                    $error_messages = [];

                    // Skip header row
                    array_shift($all_data_in_sheet);

                    foreach ($all_data_in_sheet as $row) {
                        if (!empty($row['A']) && !empty($row['B']) && !empty($row['C'])) {
                            $barang = $this->barang->get_by_sku($row['A']);
                            $gudang = $this->gudang->get_by_nama($row['B']);

                            if ($barang && $gudang) {
                                // Check if stock already exists
                                $existing_stok = $this->stok_awal->get_by_barang_gudang(
                                    $barang->id_barang,
                                    $gudang->id_gudang
                                );

                                if (!$existing_stok) {
                                    $data_insert = [
                                        'id_barang' => $barang->id_barang,
                                        'id_gudang' => $gudang->id_gudang,
                                        'id_perusahaan' => $this->session->userdata('id_perusahaan'),
                                        'qty_awal' => $row['C'],
                                        'keterangan' => $row['D'] ?: 'Import dari Excel',
                                        'created_by' => $this->session->userdata('id_user'),
                                        'created_at' => date('Y-m-d H:i:s')
                                    ];

                                    if ($this->stok_awal->insert($data_insert)) {
                                        // Update stok gudang
                                        $this->update_stok_gudang(
                                            $barang->id_barang,
                                            $gudang->id_gudang,
                                            $row['C']
                                        );

                                        $success_count++;
                                    } else {
                                        $error_count++;
                                        $error_messages[] = "Gagal menyimpan data untuk SKU: " . $row['A'];
                                    }
                                } else {
                                    $error_count++;
                                    $error_messages[] = "Stok awal untuk SKU " . $row['A'] . " di gudang " . $row['B'] . " sudah ada";
                                }
                            } else {
                                $error_count++;
                                $error_messages[] = "SKU " . $row['A'] . " atau Gudang " . $row['B'] . " tidak ditemukan";
                            }
                        }
                    }

                    // Delete temp file
                    unlink($file_path);

                    $this->data['success_message'] = "Import selesai. Berhasil: $success_count, Gagal: $error_count";
                    if (!empty($error_messages)) {
                        $this->data['error_messages'] = $error_messages;
                    }

                } catch (Exception $e) {
                    $this->data['error'] = 'Error processing file: ' . $e->getMessage();
                }
            }
        }

        $this->render_view('pengaturan/stok_awal/import');
    }

    private function update_stok_gudang($id_barang, $id_gudang, $jumlah)
    {
        // Check if stock record exists
        $stok = $this->stok_awal->get_stok_gudang($id_barang, $id_gudang);

        if ($stok) {
            // Update existing record
            $data = [
                'jumlah' => $jumlah,
                'updated_at' => date('Y-m-d H:i:s')
            ];
            $this->stok_awal->update_stok_gudang($id_barang, $id_gudang, $data);
        } else {
            // Insert new record
            $data = [
                'id_perusahaan' => $this->session->userdata('id_perusahaan'),
                'id_barang' => $id_barang,
                'id_gudang' => $id_gudang,
                'jumlah' => $jumlah,
                'reserved' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            $this->stok_awal->insert_stok_gudang($data);
        }

        // Log stock movement
        $log_data = [
            'id_barang' => $id_barang,
            'id_user' => $this->session->userdata('id_user'),
            'id_perusahaan' => $this->session->userdata('id_perusahaan'),
            'id_gudang' => $id_gudang,
            'jenis' => 'penyesuaian',
            'jumlah' => $jumlah,
            'sisa_stok' => $jumlah,
            'keterangan' => 'Penyesuaian stok awal',
            'tanggal' => date('Y-m-d H:i:s')
        ];
        $this->stok_awal->insert_log_stok($log_data);
    }
}