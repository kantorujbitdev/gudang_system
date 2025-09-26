<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Sales extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('laporan/Sales_model', 'sales');
        $this->load->model('setup/Barang_model', 'barang');
        $this->load->helper('form');
        $this->load->library('form_validation');

        // Cek akses menu
        $this->check_menu_access('laporan/sales');
    }

    public function index()
    {
        $this->data['title'] = 'Laporan Sales';
        $this->data['barang'] = $this->barang->get_all();

        // Get users by company
        $id_perusahaan = $this->session->userdata('id_perusahaan');
        $this->data['users'] = $this->sales->get_users_by_company($id_perusahaan);

        /// Set filter default
        $filter = [
            'tanggal_awal' => date('Y-m-01'),
            'tanggal_akhir' => date('Y-m-d'),
            'id_barang' => '',
            'id_user' => '',
            'status' => '' // Kosong berarti menampilkan semua status (Shipping & Delivered)
        ];

        $this->data['filter'] = $filter;
        $this->data['sales'] = $this->sales->get_filtered($filter);

        $this->render_view('laporan/sales/index');
    }

    public function filter()
    {
        $this->data['title'] = 'Laporan Sales';
        $this->data['barang'] = $this->barang->get_all();

        // Get users by company
        $id_perusahaan = $this->session->userdata('id_perusahaan');
        $this->data['users'] = $this->sales->get_users_by_company($id_perusahaan);

        // Get filter dari POST
        $filter = [
            'tanggal_awal' => $this->input->post('tanggal_awal') ?: date('Y-m-01'),
            'tanggal_akhir' => $this->input->post('tanggal_akhir') ?: date('Y-m-d'),
            'id_barang' => $this->input->post('id_barang'),
            'id_user' => $this->input->post('id_user'),
            'status' => $this->input->post('status')
        ];

        $this->data['filter'] = $filter;
        $this->data['sales'] = $this->sales->get_filtered($filter);

        $this->render_view('laporan/sales/index');
    }

    public function detail($id_pemindahan)
    {
        $pemindahan = $this->sales->get_pemindahan($id_pemindahan);

        if (!$pemindahan) {
            $this->session->set_flashdata('error', 'Data pemindahan barang tidak ditemukan!');
            return redirect('laporan/sales');
        }

        $this->data['title'] = 'Detail Pemindahan Barang';
        $this->data['pemindahan'] = $pemindahan;
        $this->data['detail'] = $this->sales->get_detail_pemindahan($id_pemindahan);

        $this->render_view('laporan/sales/detail');
    }

    public function export()
    {
        // Get filter dari POST
        $filter = [
            'tanggal_awal' => $this->input->post('tanggal_awal') ?: date('Y-m-01'),
            'tanggal_akhir' => $this->input->post('tanggal_akhir') ?: date('Y-m-d'),
            'id_barang' => $this->input->post('id_barang'),
            'id_user' => $this->input->post('id_user')
        ];

        $sales = $this->sales->get_filtered($filter);
        $format = $this->input->post('format');

        if ($format == 'excel') {
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
                ->setCellValue('B1', 'No Transaksi')
                ->setCellValue('C1', 'Tanggal')
                ->setCellValue('D1', 'Tujuan')
                ->setCellValue('E1', 'Barang')
                ->setCellValue('F1', 'Jumlah')
                ->setCellValue('G1', 'Satuan')
                ->setCellValue('H1', 'Status')
                ->setCellValue('I1', 'User');

            // Add data
            $row = 2;
            $no = 1;
            foreach ($sales as $item) {
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $row, $no++)
                    ->setCellValue('B' . $row, $item->no_transaksi)
                    ->setCellValue('C' . $row, date('d-m-Y', strtotime($item->tanggal_pemindahan)))
                    ->setCellValue('D' . $row, $item->nama_tujuan)
                    ->setCellValue('E' . $row, $item->nama_barang)
                    ->setCellValue('F' . $row, $item->jumlah)
                    ->setCellValue('G' . $row, $item->satuan)
                    ->setCellValue('H' . $row, $item->status)
                    ->setCellValue('I' . $row, $item->nama_user);
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
        } elseif ($format == 'csv') {
            // Export to CSV
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment;filename="Laporan_Sales_' . date('YmdHis') . '.csv"');

            $output = fopen('php://output', 'w');

            // Add header
            fputcsv($output, array('No', 'No Transaksi', 'Tanggal', 'Tujuan', 'Barang', 'Jumlah', 'Satuan', 'Status', 'User'));

            // Add data
            $no = 1;
            foreach ($sales as $item) {
                fputcsv($output, array(
                    $no++,
                    $item->no_transaksi,
                    date('d-m-Y', strtotime($item->tanggal_pemindahan)),
                    $item->nama_tujuan,
                    $item->nama_barang,
                    $item->jumlah,
                    $item->satuan,
                    $item->status,
                    $item->nama_user
                ));
            }

            fclose($output);
            exit;
        } elseif ($format == 'pdf') {
            // Export to PDF
            $this->load->library('pdf');

            $data['sales'] = $sales;
            $data['title'] = 'Laporan Sales';
            $data['filter'] = $filter;

            $html = $this->load->view('laporan/sales/pdf', $data, true);

            $this->pdf->create($html, 'Laporan_Sales_' . date('YmdHis') . '.pdf');
        }
    }
}