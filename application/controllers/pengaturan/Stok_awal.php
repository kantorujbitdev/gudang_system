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
        $id_perusahaan = $this->session->userdata('id_perusahaan');

        // Jika Super Admin, tampilkan pilihan perusahaan
        if ($this->session->userdata('id_role') == 1) {
            $this->load->model('setup/Perusahaan_model', 'perusahaan');
            $this->data['perusahaan'] = $this->perusahaan->get_all();
            $this->data['selected_perusahaan'] = $this->input->get('id_perusahaan') ?: $this->data['perusahaan'][0]->id_perusahaan;
            $id_perusahaan = $this->data['selected_perusahaan'];
        }

        $this->data['barang'] = $this->stok_awal->get_barang_with_stok($id_perusahaan);
        $this->data['gudang'] = $this->stok_awal->get_gudang_by_perusahaan($id_perusahaan);
        $this->data['can_create'] = $this->check_permission('pengaturan/stok_awal', 'create');
        $this->data['can_edit'] = $this->check_permission('pengaturan/stok_awal', 'edit');
        $this->data['can_delete'] = $this->check_permission('pengaturan/stok_awal', 'delete');

        // Load script terpisah
        $this->data['extra_js'] = 'pengaturan/stok_awal/script';

        $this->render_view('pengaturan/stok_awal/index', $this->data);
    }
    public function ajax_tambah_stok()
    {
        header('Content-Type: application/json');

        if (!$this->check_permission('pengaturan/stok_awal', 'create')) {
            $this->session->set_flashdata('error', 'Anda tidak memiliki izin!');
            echo json_encode(['status' => 'error', 'message' => 'Anda tidak memiliki izin!']);
            return;
        }

        $id_barang = $this->input->post('id_barang');
        $id_gudang = $this->input->post('id_gudang');
        $qty_awal = $this->input->post('qty_awal');
        $id_perusahaan = $this->input->post('id_perusahaan') ?: $this->session->userdata('id_perusahaan');

        // Cek stok sudah ada
        if ($this->stok_awal->get_stok_awal_by_barang_gudang($id_barang, $id_gudang, $id_perusahaan)) {
            $this->session->set_flashdata('error', 'Stok awal sudah ada!');
            echo json_encode(['status' => 'error', 'message' => 'Stok awal sudah ada!']);
            return;
        }

        // Mulai transaksi
        $this->db->trans_start();

        try {
            // Insert ke stok_awal
            $data_insert = [
                'id_barang' => $id_barang,
                'id_gudang' => $id_gudang,
                'id_perusahaan' => $id_perusahaan,
                'qty_awal' => $qty_awal,
                'keterangan' => $this->input->post('keterangan'),
                'created_by' => $this->session->userdata('id_user'),
                'created_at' => date('Y-m-d H:i:s')
            ];

            $this->stok_awal->insert($data_insert);

            // Update stok_gudang
            $stok = $this->stok_awal->get_stok_gudang($id_barang, $id_gudang, $id_perusahaan);

            if ($stok) {
                // Update stok yang ada
                $data_stok = [
                    'jumlah' => $qty_awal,
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                $this->stok_awal->update_stok_gudang($id_barang, $id_gudang, $data_stok);
            } else {
                // Insert stok baru
                $data_stok = [
                    'id_perusahaan' => $id_perusahaan,
                    'id_barang' => $id_barang,
                    'id_gudang' => $id_gudang,
                    'jumlah' => $qty_awal,
                    'reserved' => 0,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                $this->stok_awal->insert_stok_gudang($data_stok);
            }

            // Insert log stok
            $stok_terbaru = $this->stok_awal->get_stok_gudang($id_barang, $id_gudang, $id_perusahaan);

            $log_data = [
                'id_barang' => $id_barang,
                'id_user' => $this->session->userdata('id_user'),
                'id_perusahaan' => $id_perusahaan,
                'id_gudang' => $id_gudang,
                'jenis' => 'penyesuaian',
                'jumlah' => $qty_awal,
                'sisa_stok' => $stok_terbaru ? $stok_terbaru->jumlah : 0,
                'keterangan' => 'Penyesuaian stok awal',
                'tanggal' => date('Y-m-d H:i:s'),
                'id_referensi' => null,
                'tipe_referensi' => 'penyesuaian'
            ];
            $this->stok_awal->insert_log_stok($log_data);

            // Selesaikan transaksi
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('error', 'Transaksi gagal!');
                echo json_encode(['status' => 'error', 'message' => 'Transaksi gagal!']);
            } else {
                $this->session->set_flashdata('success', 'Stok awal berhasil ditambahkan!');
                echo json_encode(['status' => 'success', 'message' => 'Stok awal berhasil ditambahkan!']);
            }
        } catch (Exception $e) {
            // Rollback transaksi jika ada error
            $this->db->trans_rollback();
            $this->session->set_flashdata('error', 'Error: ' . $e->getMessage());
            echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function tambah()
    {
        if (!$this->check_permission('pengaturan/stok_awal', 'create')) {
            $this->session->set_flashdata('error', 'Anda tidak memiliki izin untuk menambah stok awal!');
            return redirect('pengaturan/stok_awal');
        }

        $this->data['title'] = 'Tambah Stok Awal';
        $id_perusahaan = $this->session->userdata('id_perusahaan');
        $this->data['barang'] = $this->stok_awal->get_barang_by_perusahaan($id_perusahaan);
        $this->data['gudang'] = $this->stok_awal->get_gudang_by_perusahaan($id_perusahaan);

        $this->form_validation->set_rules('id_barang', 'Barang', 'required');
        $this->form_validation->set_rules('id_gudang', 'Gudang', 'required');
        $this->form_validation->set_rules('qty_awal', 'Jumlah Stok', 'required|numeric|greater_than_equal_to[0]');

        if ($this->form_validation->run() === FALSE) {
            return $this->render_view('pengaturan/stok_awal/form');
        }

        $id_barang = $this->input->post('id_barang');
        $id_gudang = $this->input->post('id_gudang');
        $qty_awal = $this->input->post('qty_awal');

        // Cek stok sudah ada atau belum
        if ($this->stok_awal->get_by_barang_gudang($id_barang, $id_gudang)) {
            $this->session->set_flashdata('error', 'Stok awal untuk barang & gudang ini sudah ada!');
            return redirect('pengaturan/stok_awal/tambah');
        }

        // Mulai transaksi
        $this->db->trans_start();

        try {
            $data_insert = [
                'id_barang' => $id_barang,
                'id_gudang' => $id_gudang,
                'id_perusahaan' => $id_perusahaan,
                'qty_awal' => $qty_awal,
                'keterangan' => $this->input->post('keterangan'),
                'created_by' => $this->session->userdata('id_user'),
                'created_at' => date('Y-m-d H:i:s')
            ];

            $this->stok_awal->insert($data_insert);
            $this->update_stok_gudang($id_barang, $id_gudang, $qty_awal);

            // Selesaikan transaksi
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('error', 'Transaksi gagal!');
                return redirect('pengaturan/stok_awal/tambah');
            } else {
                $this->session->set_flashdata('success', 'Stok awal berhasil ditambahkan!');
                return redirect('pengaturan/stok_awal');
            }
        } catch (Exception $e) {
            // Rollback transaksi jika ada error
            $this->db->trans_rollback();
            $this->session->set_flashdata('error', 'Error: ' . $e->getMessage());
            return redirect('pengaturan/stok_awal/tambah');
        }
    }

    public function edit($id_stok_awal)
    {
        if (!$this->check_permission('pengaturan/stok_awal', 'edit')) {
            $this->session->set_flashdata('error', 'Anda tidak memiliki izin untuk mengubah stok awal!');
            return redirect('pengaturan/stok_awal');
        }

        $this->data['title'] = 'Edit Stok Awal';
        $this->data['stok_awal'] = $this->stok_awal->get($id_stok_awal);
        $id_perusahaan = $this->session->userdata('id_perusahaan');
        $this->data['barang'] = $this->stok_awal->get_barang_by_perusahaan($id_perusahaan);
        $this->data['gudang'] = $this->stok_awal->get_gudang_by_perusahaan($id_perusahaan);

        if (!$this->data['stok_awal']) {
            show_404();
        }

        $this->form_validation->set_rules('id_barang', 'Barang', 'required');
        $this->form_validation->set_rules('id_gudang', 'Gudang', 'required');
        $this->form_validation->set_rules('qty_awal', 'Jumlah Stok', 'required|numeric|greater_than_equal_to[0]');

        if ($this->form_validation->run() === FALSE) {
            return $this->render_view('pengaturan/stok_awal/form');
        }

        $data_update = [
            'id_barang' => $this->input->post('id_barang'),
            'id_gudang' => $this->input->post('id_gudang'),
            'qty_awal' => $this->input->post('qty_awal'),
            'keterangan' => $this->input->post('keterangan')
        ];

        if ($this->stok_awal->update($id_stok_awal, $data_update)) {
            $this->update_stok_gudang(
                $this->input->post('id_barang'),
                $this->input->post('id_gudang'),
                $this->input->post('qty_awal')
            );
            $this->session->set_flashdata('success', 'Stok awal berhasil diperbarui!');
            return redirect('pengaturan/stok_awal');
        } else {
            $this->session->set_flashdata('error', 'Gagal memperbarui stok awal!');
            return redirect('pengaturan/stok_awal/edit/' . $id_stok_awal);
        }
    }

    public function hapus($id_stok_awal)
    {
        if (!$this->check_permission('pengaturan/stok_awal', 'delete')) {
            $this->session->set_flashdata('error', 'Anda tidak memiliki izin untuk menghapus stok awal!');
            return redirect('pengaturan/stok_awal');
        }

        $stok_awal = $this->stok_awal->get($id_stok_awal);
        if (!$stok_awal) {
            show_404();
        }

        if ($this->stok_awal->delete($id_stok_awal)) {
            $this->update_stok_gudang($stok_awal->id_barang, $stok_awal->id_gudang, 0);
            $this->session->set_flashdata('success', 'Stok awal berhasil dihapus!');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus stok awal!');
        }

        return redirect('pengaturan/stok_awal');
    }

    public function import()
    {
        if (!$this->check_permission('pengaturan/stok_awal', 'create')) {
            $this->session->set_flashdata('error', 'Anda tidak memiliki izin untuk mengimpor stok awal!');
            return redirect('pengaturan/stok_awal');
        }

        $this->data['title'] = 'Import Stok Awal';

        if ($this->input->post()) {
            $config['upload_path'] = './uploads/';
            $config['allowed_types'] = 'xlsx|xls|csv';
            $config['max_size'] = 2048;
            $config['file_name'] = 'stok_awal_' . time();

            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('file_import')) {
                $error = $this->upload->display_errors();
                $this->session->set_flashdata('error', $error);
                return redirect('pengaturan/stok_awal/import');
            } else {
                $file = $this->upload->data();
                $ext = pathinfo($file['file_name'], PATHINFO_EXTENSION);

                if ($ext == 'csv') {
                    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
                } elseif ($ext == 'xls') {
                    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
                } else {
                    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
                }

                $spreadsheet = $reader->load('./uploads/' . $file['file_name']);
                $sheetData = $spreadsheet->getActiveSheet()->toArray();

                $error_messages = [];
                $success_count = 0;
                $skip_count = 0;

                // Lewati header (baris pertama)
                for ($i = 1; $i < count($sheetData); $i++) {
                    $row = $sheetData[$i];

                    $sku = trim($row[0]);
                    $nama_gudang = trim($row[1]);
                    $qty_awal = (int) $row[2];
                    $keterangan = isset($row[3]) ? trim($row[3]) : '';

                    if (empty($sku) || empty($nama_gudang) || $qty_awal < 0) {
                        $error_messages[] = "Baris " . ($i + 1) . ": Data tidak lengkap atau jumlah stok negatif";
                        continue;
                    }

                    // Cari barang berdasarkan SKU
                    $barang = $this->db->get_where('barang', [
                        'sku' => $sku,
                        'id_perusahaan' => $this->session->userdata('id_perusahaan'),
                        'status_aktif' => 1
                    ])->row();

                    if (!$barang) {
                        $error_messages[] = "Baris " . ($i + 1) . ": Barang dengan SKU '$sku' tidak ditemukan";
                        continue;
                    }

                    // Cari gudang berdasarkan nama
                    $gudang = $this->db->get_where('gudang', [
                        'nama_gudang' => $nama_gudang,
                        'id_perusahaan' => $this->session->userdata('id_perusahaan'),
                        'status_aktif' => 1
                    ])->row();

                    if (!$gudang) {
                        $error_messages[] = "Baris " . ($i + 1) . ": Gudang '$nama_gudang' tidak ditemukan";
                        continue;
                    }

                    // Cek apakah stok awal sudah ada
                    $existing_stok = $this->stok_awal->get_by_barang_gudang($barang->id_barang, $gudang->id_gudang);
                    if ($existing_stok) {
                        $skip_count++;
                        continue;
                    }

                    // Simpan stok awal
                    $data_insert = [
                        'id_barang' => $barang->id_barang,
                        'id_gudang' => $gudang->id_gudang,
                        'id_perusahaan' => $this->session->userdata('id_perusahaan'),
                        'qty_awal' => $qty_awal,
                        'keterangan' => $keterangan,
                        'created_by' => $this->session->userdata('id_user'),
                        'created_at' => date('Y-m-d H:i:s')
                    ];

                    if ($this->stok_awal->insert($data_insert)) {
                        $this->update_stok_gudang($barang->id_barang, $gudang->id_gudang, $qty_awal);
                        $success_count++;
                    } else {
                        $error_messages[] = "Baris " . ($i + 1) . ": Gagal menyimpan data";
                    }
                }

                // Hapus file upload
                unlink('./uploads/' . $file['file_name']);

                // Set flashdata
                if ($success_count > 0) {
                    $this->session->set_flashdata('success', "$success_count stok awal berhasil diimpor");
                }

                if ($skip_count > 0) {
                    $this->session->set_flashdata('warning', "$skip_count data dilewati karena sudah ada");
                }

                if (!empty($error_messages)) {
                    $this->session->set_flashdata('error_messages', $error_messages);
                }

                return redirect('pengaturan/stok_awal');
            }
        }

        $this->render_view('pengaturan/stok_awal/import');
    }

    private function update_stok_gudang($id_barang, $id_gudang, $jumlah)
    {
        $id_perusahaan = $this->session->userdata('id_perusahaan');
        $stok = $this->stok_awal->get_stok_gudang($id_barang, $id_gudang);

        if ($stok) {
            // Update stok yang ada
            $data = [
                'jumlah' => $jumlah,
                'updated_at' => date('Y-m-d H:i:s')
            ];
            $this->stok_awal->update_stok_gudang($id_barang, $id_gudang, $data);
        } else {
            // Insert stok baru
            $data = [
                'id_perusahaan' => $id_perusahaan,
                'id_barang' => $id_barang,
                'id_gudang' => $id_gudang,
                'jumlah' => $jumlah,
                'reserved' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            $this->stok_awal->insert_stok_gudang($data);
        }

        // Ambil stok terbaru untuk log
        $stok_terbaru = $this->stok_awal->get_stok_gudang($id_barang, $id_gudang);

        $log_data = [
            'id_barang' => $id_barang,
            'id_user' => $this->session->userdata('id_user'),
            'id_perusahaan' => $id_perusahaan,
            'id_gudang' => $id_gudang,
            'jenis' => 'penyesuaian',
            'jumlah' => $jumlah,
            'sisa_stok' => $stok_terbaru ? $stok_terbaru->jumlah : 0,
            'keterangan' => 'Penyesuaian stok awal',
            'tanggal' => date('Y-m-d H:i:s'),
            'id_referensi' => null,
            'tipe_referensi' => 'penyesuaian'
        ];
        $this->stok_awal->insert_log_stok($log_data);
    }
}