<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Summary extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('laporan/Summary_model', 'summary');
        $this->load->model('setup/Barang_model', 'barang');
        $this->load->model('setup/Gudang_model', 'gudang');
        $this->load->helper('form');
        $this->load->library('form_validation');

        // Cek akses menu
        $this->check_menu_access('laporan/summary');
    }

    public function index()
    {
        $this->data['title'] = 'Ringkasan Stok';
        $this->data['barang'] = $this->barang->get_all();
        $this->data['gudang'] = $this->gudang->get_all();

        // Filter default
        $filter = [
            'id_barang' => '',
            'id_gudang' => ''
        ];

        $this->data['filter'] = $filter;
        $this->data['stok'] = $this->summary->get_filtered($filter);

        $this->render_view('laporan/summary/index');
    }

    public function filter()
    {
        $this->form_validation->set_rules('id_barang', 'Barang', 'trim');
        $this->form_validation->set_rules('id_gudang', 'Gudang', 'trim');

        if ($this->form_validation->run() === FALSE) {
            // Validasi gagal → render ulang
            $this->data['title'] = 'Ringkasan Stok';
            $this->data['barang'] = $this->barang->get_all();
            $this->data['gudang'] = $this->gudang->get_all();
            $this->data['filter'] = $this->input->post();
            $this->data['stok'] = [];

            $this->render_view('laporan/summary/index');
        } else {
            // Validasi sukses → proses filter
            $filter = [
                'id_barang' => $this->input->post('id_barang'),
                'id_gudang' => $this->input->post('id_gudang')
            ];

            $this->data['title'] = 'Ringkasan Stok';
            $this->data['barang'] = $this->barang->get_all();
            $this->data['gudang'] = $this->gudang->get_all();
            $this->data['filter'] = $filter;
            $this->data['stok'] = $this->summary->get_filtered($filter);

            $this->render_view('laporan/summary/index');
        }
    }

    public function export()
    {
        try {
            $filter = [
                'id_barang' => $this->input->post('id_barang'),
                'id_gudang' => $this->input->post('id_gudang')
            ];

            $stok = $this->summary->get_filtered($filter);

            if (!$stok) {
                $this->session->set_flashdata('error', 'Tidak ada data yang bisa diexport.');
                redirect('laporan/summary');
            }

            // Create new PHPExcel object
            $this->load->library('PHPExcel');
            $objPHPExcel = new PHPExcel();

            // Set properties
            $objPHPExcel->getProperties()->setTitle("Ringkasan Stok")
                ->setSubject("Ringkasan Stok")
                ->setDescription("Ringkasan Stok");

            // Add header
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', 'No')
                ->setCellValue('B1', 'Barang')
                ->setCellValue('C1', 'SKU')
                ->setCellValue('D1', 'Gudang')
                ->setCellValue('E1', 'Stok Tersedia')
                ->setCellValue('F1', 'Reserved')
                ->setCellValue('G1', 'Total Stok');

            // Add data
            $row = 2;
            $no = 1;
            foreach ($stok as $item) {
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $row, $no++)
                    ->setCellValue('B' . $row, $item->nama_barang)
                    ->setCellValue('C' . $row, $item->sku)
                    ->setCellValue('D' . $row, $item->nama_gudang)
                    ->setCellValue('E' . $row, $item->stok_tersedia)
                    ->setCellValue('F' . $row, $item->reserved)
                    ->setCellValue('G' . $row, $item->jumlah);
                $row++;
            }

            // Auto size
            foreach (range('A', 'G') as $column) {
                $objPHPExcel->getActiveSheet()->getColumnDimension($column)->setAutoSize(true);
            }

            $objPHPExcel->setActiveSheetIndex(0);

            // Output
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Ringkasan_Stok_' . date('YmdHis') . '.xlsx"');
            header('Cache-Control: max-age=0');

            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $objWriter->save('php://output');
            exit;
        } catch (Exception $e) {
            $this->session->set_flashdata('error', 'Terjadi kesalahan saat export: ' . $e->getMessage());
            redirect('laporan/summary');
        }
    }
}
