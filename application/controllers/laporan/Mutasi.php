<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Mutasi extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Laporan/Mutasi_model', 'mutasi');
        $this->load->model('setup/Barang_model', 'barang');
        $this->load->model('setup/Gudang_model', 'gudang');
        $this->load->helper('form');
        $this->load->library('form_validation');

        // Cek akses menu
        $this->check_menu_access('laporan/mutasi');
    }

    public function index()
    {
        $this->data['title'] = 'Laporan Mutasi Barang';
        $this->data['barang'] = $this->barang->get_all();
        $this->data['gudang'] = $this->gudang->get_all();

        // Set filter default
        $filter = [
            'tanggal_awal' => date('Y-m-01'),
            'tanggal_akhir' => date('Y-m-d'),
            'id_barang' => '',
            'id_gudang' => '',
            'jenis' => ''
        ];

        $this->data['filter'] = $filter;
        $this->data['mutasi'] = $this->mutasi->get_filtered($filter);

        $this->render_view('laporan/mutasi/index');
    }

    public function filter()
    {
        $this->data['title'] = 'Laporan Mutasi Barang';
        $this->data['barang'] = $this->barang->get_all();
        $this->data['gudang'] = $this->gudang->get_all();

        // Get filter from POST
        $filter = [
            'tanggal_awal' => $this->input->post('tanggal_awal') ?: date('Y-m-01'),
            'tanggal_akhir' => $this->input->post('tanggal_akhir') ?: date('Y-m-d'),
            'id_barang' => $this->input->post('id_barang'),
            'id_gudang' => $this->input->post('id_gudang'),
            'jenis' => $this->input->post('jenis')
        ];

        $this->data['filter'] = $filter;
        $this->data['mutasi'] = $this->mutasi->get_filtered($filter);

        $this->render_view('laporan/mutasi/index');
    }

    public function export()
    {
        // Get filter from POST
        $filter = [
            'tanggal_awal' => $this->input->post('tanggal_awal') ?: date('Y-m-01'),
            'tanggal_akhir' => $this->input->post('tanggal_akhir') ?: date('Y-m-d'),
            'id_barang' => $this->input->post('id_barang'),
            'id_gudang' => $this->input->post('id_gudang'),
            'jenis' => $this->input->post('jenis')
        ];

        $mutasi = $this->mutasi->get_filtered($filter);

        // Create new PHPExcel object
        $this->load->library('PHPExcel');
        $objPHPExcel = new PHPExcel();

        // Set properties
        $objPHPExcel->getProperties()->setTitle("Laporan Mutasi Barang")
            ->setSubject("Laporan Mutasi Barang")
            ->setDescription("Laporan Mutasi Barang");

        // Add header
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'No')
            ->setCellValue('B1', 'Tanggal')
            ->setCellValue('C1', 'Barang')
            ->setCellValue('D1', 'Gudang')
            ->setCellValue('E1', 'Jenis')
            ->setCellValue('F1', 'Jumlah')
            ->setCellValue('G1', 'Sisa Stok')
            ->setCellValue('H1', 'Keterangan');

        // Add data
        $row = 2;
        $no = 1;
        foreach ($mutasi as $item) {
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . $row, $no++)
                ->setCellValue('B' . $row, date('d-m-Y H:i', strtotime($item->tanggal)))
                ->setCellValue('C' . $row, $item->nama_barang)
                ->setCellValue('D' . $row, $item->nama_gudang)
                ->setCellValue('E' . $row, $item->jenis)
                ->setCellValue('F' . $row, $item->jumlah)
                ->setCellValue('G' . $row, $item->sisa_stok)
                ->setCellValue('H' . $row, $item->keterangan);

            $row++;
        }

        // Auto size columns
        foreach (range('A', 'H') as $column) {
            $objPHPExcel->getActiveSheet()->getColumnDimension($column)->setAutoSize(true);
        }

        // Set active sheet index to the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to client browser
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Laporan_Mutasi_' . date('YmdHis') . '.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }
}