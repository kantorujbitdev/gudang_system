<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Sistem extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('pengaturan/Sistem_model', 'sistem');
        $this->load->helper(['form', 'file']);
        $this->load->library('form_validation');

        // Cek akses menu
        $this->check_menu_access('pengaturan/sistem');
    }

    public function index()
    {
        $this->data['title'] = 'Pengaturan Sistem';
        $this->data['pengaturan'] = $this->sistem->get_all_pengaturan();
        $this->data['can_edit'] = $this->check_permission('pengaturan/sistem', 'edit');

        $this->render_view('pengaturan/sistem/index');
    }

    public function update()
    {
        if (!$this->check_permission('pengaturan/sistem', 'edit')) {
            $this->session->set_flashdata('error', 'Anda tidak memiliki izin untuk mengubah pengaturan sistem!');
            return redirect('pengaturan/sistem');
        }

        if ($this->input->post()) {
            $keys = $this->input->post('key');
            $values = $this->input->post('value');

            foreach ($keys as $index => $key) {
                if (!empty($key)) {
                    $this->sistem->update_pengaturan($key, [
                        'value' => $values[$index]
                    ]);
                }
            }

            $this->session->set_flashdata('success', 'Pengaturan sistem berhasil diperbarui');
            return redirect('pengaturan/sistem');
        }

        // kalau tidak ada POST, kembali ke index
        redirect('pengaturan/sistem');
    }

    public function backup()
    {
        if (!$this->check_permission('pengaturan/sistem', 'edit')) {
            $this->session->set_flashdata('error', 'Anda tidak memiliki izin untuk melakukan backup!');
            return redirect('pengaturan/sistem');
        }

        $this->load->dbutil();

        $prefs = [
            'format' => 'zip',
            'filename' => 'backup_' . date('Y-m-d_H-i-s'),
            'add_drop' => TRUE,
            'add_insert' => TRUE,
            'newline' => "\n"
        ];

        $backup = $this->dbutil->backup($prefs);
        $db_name = 'backup_on_' . date('Y-m-d_H-i-s') . '.zip';
        $save = './uploads/backup/' . $db_name;

        if (write_file($save, $backup)) {
            $this->session->set_flashdata('success', 'Backup database berhasil dibuat. File disimpan di: ' . $save);
        } else {
            $this->session->set_flashdata('error', 'Gagal membuat backup database!');
        }

        redirect('pengaturan/sistem');
    }

    public function log()
    {
        if (!$this->check_permission('pengaturan/sistem', 'edit')) {
            $this->session->set_flashdata('error', 'Anda tidak memiliki izin untuk melihat log sistem!');
            return redirect('pengaturan/sistem');
        }

        $this->data['title'] = 'Log Sistem';
        $log_files = [];
        $log_path = APPPATH . 'logs/';

        if (is_dir($log_path)) {
            $files = scandir($log_path);

            foreach ($files as $file) {
                if ($file != '.' && $file != '..' && pathinfo($file, PATHINFO_EXTENSION) == 'log') {
                    $log_files[] = [
                        'name' => $file,
                        'path' => $log_path . $file,
                        'size' => filesize($log_path . $file),
                        'modified' => filemtime($log_path . $file)
                    ];
                }
            }

            // sort log dari terbaru
            usort($log_files, function ($a, $b) {
                return $b['modified'] - $a['modified'];
            });
        }

        $this->data['log_files'] = $log_files;
        $this->render_view('pengaturan/sistem/log');
    }

    public function view_log($filename)
    {
        if (!$this->check_permission('pengaturan/sistem', 'edit')) {
            $this->session->set_flashdata('error', 'Anda tidak memiliki izin untuk melihat log sistem!');
            return redirect('pengaturan/sistem/log');
        }

        $log_path = APPPATH . 'logs/' . $filename;
        if (!file_exists($log_path)) {
            show_404();
        }

        $this->data['title'] = 'View Log - ' . $filename;
        $this->data['filename'] = $filename;
        $this->data['log_content'] = file_get_contents($log_path);

        $this->render_view('pengaturan/sistem/view_log');
    }

    public function clear_log()
    {
        if (!$this->check_permission('pengaturan/sistem', 'edit')) {
            $this->session->set_flashdata('error', 'Anda tidak memiliki izin untuk membersihkan log sistem!');
            return redirect('pengaturan/sistem/log');
        }

        $log_path = APPPATH . 'logs/';
        $success = true;

        if (is_dir($log_path)) {
            $files = glob($log_path . '*.log');
            foreach ($files as $file) {
                if (is_file($file) && !unlink($file)) {
                    $success = false;
                }
            }
        }

        if ($success) {
            $this->session->set_flashdata('success', 'Log sistem berhasil dibersihkan');
        } else {
            $this->session->set_flashdata('error', 'Gagal membersihkan sebagian/seluruh log!');
        }

        redirect('pengaturan/sistem/log');
    }
}
