<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Sales extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('laporan/Sales_model', 'sales');
        $this->load->model('setup/Pelanggan_model', 'pelanggan');
        $this->load->model('setup/Barang_model', 'barang');
        $this->load->helper('form');
        $this->load->library('form_validation');

        // Cek akses menu
        $this->check_menu_access('laporan/sales');
    }

    public function index()
    {
        $this->data['title'] = 'Laporan Sales';
        $this->data['pelanggan'] = $this->pelanggan->get_all();
        $this->data['barang'] = $this->barang->get_all();

        // Set filter default
        $filter = [
            'tanggal_awal' => date('Y-m-01'),
            'tanggal_akhir' => date('Y-m-d'),
            'id_pelanggan' => '',
            'id_barang' => ''
        ];

        $this->data['filter'] = $filter;
        $this->data['sales'] = $this->sales->get_filtered($filter);

        $this->render_view('laporan/sales/index');
    }

    public function filter()
    {
        $this->data['title'] = 'Laporan Sales';
        $this->data['pelanggan'] = $this->pelanggan->get_all();
        $this->data['barang'] = $this->barang->get_all();

        // Get filter dari POST
        $filter = [
            'tanggal_awal' => $this->input->post('tanggal_awal') ?: date('Y-m-01'),
            'tanggal_akhir' => $this->input->post('tanggal_akhir') ?: date('Y-m-d'),
            'id_pelanggan' => $this->input->post('id_pelanggan'),
            'id_barang' => $this->input->post('id_barang')
        ];

        $this->data['filter'] = $filter;
        $this->data['sales'] = $this->sales->get_filtered($filter);

        $this->render_view('laporan/sales/index');
    }

    public function detail($id_penjualan)
    {
        $penjualan = $this->sales->get_penjualan($id_penjualan);

        if (!$penjualan) {
            $this->session->set_flashdata('error', 'Data penjualan tidak ditemukan!');
            return redirect('laporan/sales');
        }

        $this->data['title'] = 'Detail Penjualan';
        $this->data['penjualan'] = $penjualan;
        $this->data['detail'] = $this->sales->get_detail_penjualan($id_penjualan);

        $this->render_view('laporan/sales/detail');
    }

    public function export()
    {
        // Get filter dari POST
        $filter = [
            'tanggal_awal' => $this->input->post('tanggal_awal') ?: date('Y-m-01'),
            'tanggal_akhir' => $this->input->post('tanggal_akhir') ?: date('Y-m-d'),
            'id_pelanggan' => $this->input->post('id_pelanggan'),
            'id_barang' => $this->input->post('id_barang')
        ];

        $sales = $this->sales->get_filtered($filter);

        // Create new PHPExcel object
        $this->load->library('PHPExcel');
        $objPHPExcel = new PHPExcel();

        // Set properties
        $objPHPExcel->getProperties()
            ->setTitle("Laporan Sales")
            ->setSubject("Laporan Sales")
            ->setDescription("Laporan Sales");

        // Add header
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'No')
            ->setCellValue('B1', 'No Invoice')
            ->setCellValue('C1', 'Tanggal')
            ->setCellValue('D1', 'Pelanggan')
            ->setCellValue('E1', 'Barang')
            ->setCellValue('F1', 'Jumlah')
            ->setCellValue('G1', 'Harga')
            ->setCellValue('H1', 'Total')
            ->setCellValue('I1', 'Status');

        // Add data
        $row = 2;
        $no = 1;
        foreach ($sales as $item) {
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . $row, $no++)
                ->setCellValue('B' . $row, $item->no_invoice)
                ->setCellValue('C' . $row, date('d-m-Y', strtotime($item->tanggal_penjualan)))
                ->setCellValue('D' . $row, $item->nama_pelanggan)
                ->setCellValue('E' . $row, $item->nama_barang)
                ->setCellValue('F' . $row, $item->jumlah)
                ->setCellValue('G' . $row, $item->harga_jual)
                ->setCellValue('H' . $row, $item->total)
                ->setCellValue('I' . $row, $item->status);
            $row++;
        }

        // Auto size columns
        foreach (range('A', 'I') as $column) {
            $objPHPExcel->getActiveSheet()->getColumnDimension($column)->setAutoSize(true);
        }

        // Set active sheet index to the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to client browser
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Laporan_Sales_' . date('YmdHis') . '.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }
}
