<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Packing extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Laporan/Packing_model', 'packing');
        $this->load->model('setup/User_model', 'user');
        $this->load->helper('form');
        $this->load->library('form_validation');

        // Cek akses menu
        $this->check_menu_access('laporan/packing');
    }

    public function index()
    {
        $this->data['title'] = 'Laporan Packing';
        $this->data['user'] = $this->user->get_by_role(4); // Admin Packing

        // Set filter default
        $filter = [
            'tanggal_awal' => date('Y-m-01'),
            'tanggal_akhir' => date('Y-m-d'),
            'id_user' => '',
            'status' => ''
        ];

        $this->data['filter'] = $filter;
        $this->data['packing'] = $this->packing->get_filtered($filter);

        $this->render_view('laporan/packing/index');
    }

    public function filter()
    {
        $this->data['title'] = 'Laporan Packing';
        $this->data['user'] = $this->user->get_by_role(4); // Admin Packing

        // Get filter dari POST
        $filter = [
            'tanggal_awal' => $this->input->post('tanggal_awal') ?: date('Y-m-01'),
            'tanggal_akhir' => $this->input->post('tanggal_akhir') ?: date('Y-m-d'),
            'id_user' => $this->input->post('id_user'),
            'status' => $this->input->post('status')
        ];

        $this->data['filter'] = $filter;
        $this->data['packing'] = $this->packing->get_filtered($filter);

        $this->render_view('laporan/packing/index');
    }

    public function detail($id_packing)
    {
        $packing = $this->packing->get_packing($id_packing);

        if (!$packing) {
            $this->session->set_flashdata('error', 'Data packing tidak ditemukan!');
            return redirect('laporan/packing');
        }

        $this->data['title'] = 'Detail Packing';
        $this->data['packing'] = $packing;
        $this->data['detail'] = $this->packing->get_detail_packing($id_packing);

        $this->render_view('laporan/packing/detail');
    }

    public function export()
    {
        // Get filter dari POST
        $filter = [
            'tanggal_awal' => $this->input->post('tanggal_awal') ?: date('Y-m-01'),
            'tanggal_akhir' => $this->input->post('tanggal_akhir') ?: date('Y-m-d'),
            'id_user' => $this->input->post('id_user'),
            'status' => $this->input->post('status')
        ];

        $packing = $this->packing->get_filtered($filter);

        // Create new PHPExcel object
        $this->load->library('PHPExcel');
        $objPHPExcel = new PHPExcel();

        // Set properties
        $objPHPExcel->getProperties()
            ->setTitle("Laporan Packing")
            ->setSubject("Laporan Packing")
            ->setDescription("Laporan Packing");

        // Add header
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'No')
            ->setCellValue('B1', 'No Packing')
            ->setCellValue('C1', 'Tanggal')
            ->setCellValue('D1', 'User')
            ->setCellValue('E1', 'Referensi')
            ->setCellValue('F1', 'Barang')
            ->setCellValue('G1', 'Jumlah')
            ->setCellValue('H1', 'Status');

        // Add data
        $row = 2;
        $no = 1;
        foreach ($packing as $item) {
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . $row, $no++)
                ->setCellValue('B' . $row, $item->id_packing)
                ->setCellValue('C' . $row, date('d-m-Y H:i', strtotime($item->tanggal_packing)))
                ->setCellValue('D' . $row, $item->user_nama)
                ->setCellValue('E' . $row, $item->tipe_referensi . ' #' . $item->id_referensi)
                ->setCellValue('F' . $row, $item->nama_barang)
                ->setCellValue('G' . $row, $item->jumlah)
                ->setCellValue('H' . $row, $item->status);
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
        header('Content-Disposition: attachment;filename="Laporan_Packing_' . date('YmdHis') . '.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }
}
