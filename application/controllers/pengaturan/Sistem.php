<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Sistem extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Pengaturan/Sistem_model', 'sistem');
        $this->load->helper('form');
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
        // Check permission
        if (!$this->check_permission('pengaturan/sistem', 'edit')) {
            $this->data['error'] = 'Anda tidak memiliki izin untuk mengubah pengaturan sistem!';
            $this->render_view('pengaturan/sistem');
        }

        if ($this->input->post()) {
            $keys = $this->input->post('key');
            $values = $this->input->post('value');

            foreach ($keys as $index => $key) {
                if (!empty($key)) {
                    $data = [
                        'value' => $values[$index]
                    ];

                    $this->sistem->update_pengaturan($key, $data);
                }
            }

            $this->data['success'] = 'Pengaturan sistem berhasil diperbarui';
            $this->render_view('pengaturan/sistem');
        }
    }

    public function backup()
    {
        // Check permission
        if (!$this->check_permission('pengaturan/sistem', 'edit')) {
            $this->data['error'] = 'Anda tidak memiliki izin untuk melakukan backup!';
            $this->render_view('pengaturan/sistem');
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

        write_file($save, $backup);

        $this->data['success'] = 'Backup database berhasil dibuat. File disimpan di: ' . $save;
        $this->render_view('pengaturan/sistem');
    }

    public function log()
    {
        // Check permission
        if (!$this->check_permission('pengaturan/sistem', 'edit')) {
            $this->data['error'] = 'Anda tidak memiliki izin untuk melihat log sistem!';
            $this->render_view('pengaturan/sistem');
        }

        $this->data['title'] = 'Log Sistem';

        // Get log files
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

            // Sort by modified date
            usort($log_files, function ($a, $b) {
                return $b['modified'] - $a['modified'];
            });
        }

        $this->data['log_files'] = $log_files;

        $this->render_view('pengaturan/sistem/log');
    }

    public function view_log($filename)
    {
        // Check permission
        if (!$this->check_permission('pengaturan/sistem', 'edit')) {
            $this->data['error'] = 'Anda tidak memiliki izin untuk melihat log sistem!';
            $this->render_view('pengaturan/sistem');
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
        // Check permission
        if (!$this->check_permission('pengaturan/sistem', 'edit')) {
            $this->data['error'] = 'Anda tidak memiliki izin untuk membersihkan log sistem!';
            $this->render_view('pengaturan/sistem/log');
        }

        $log_path = APPPATH . 'logs/';

        if (is_dir($log_path)) {
            $files = glob($log_path . '*.log');

            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
        }

        $this->data['success'] = 'Log sistem berhasil dibersihkan';
        $this->render_view('pengaturan/sistem/log');
    }
}