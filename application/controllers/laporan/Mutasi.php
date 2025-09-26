<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Mutasi extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('laporan/Mutasi_model', 'mutasi');
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

        // Ambil filter dari POST
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
        // Ambil filter dari POST
        $filter = [
            'tanggal_awal' => $this->input->post('tanggal_awal') ?: date('Y-m-01'),
            'tanggal_akhir' => $this->input->post('tanggal_akhir') ?: date('Y-m-d'),
            'id_barang' => $this->input->post('id_barang'),
            'id_gudang' => $this->input->post('id_gudang'),
            'jenis' => $this->input->post('jenis')
        ];

        $mutasi = $this->mutasi->get_filtered($filter);

        if (empty($mutasi)) {
            $this->session->set_flashdata('error', 'Data mutasi tidak ditemukan untuk filter yang dipilih!');
            return redirect('laporan/mutasi');
        }

        // Buat objek PHPExcel baru
        $this->load->library('PHPExcel');
        $objPHPExcel = new PHPExcel();

        // Set properties
        $objPHPExcel->getProperties()
            ->setTitle("Laporan Mutasi Barang")
            ->setSubject("Laporan Mutasi Barang")
            ->setDescription("Laporan Mutasi Barang");

        // Tambah header
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'No')
            ->setCellValue('B1', 'Tanggal')
            ->setCellValue('C1', 'Barang')
            ->setCellValue('D1', 'Gudang')
            ->setCellValue('E1', 'Jenis')
            ->setCellValue('F1', 'Jumlah')
            ->setCellValue('G1', 'Sisa Stok')
            ->setCellValue('H1', 'Keterangan');

        // Isi data
        $row = 2;
        $no = 1;
        foreach ($mutasi as $item) {
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . $row, $no++)
                ->setCellValue('B' . $row, date('d-m-Y H:i:s', strtotime($item->tanggal)))
                ->setCellValue('C' . $row, $item->nama_barang)
                ->setCellValue('D' . $row, $item->nama_gudang)
                ->setCellValue('E' . $row, ucfirst($item->jenis))
                ->setCellValue('F' . $row, $item->jumlah)
                ->setCellValue('G' . $row, $item->sisa_stok)
                ->setCellValue('H' . $row, $item->keterangan);

            $row++;
        }

        // Auto size kolom
        foreach (range('A', 'H') as $column) {
            $objPHPExcel->getActiveSheet()->getColumnDimension($column)->setAutoSize(true);
        }

        // Set aktif sheet
        $objPHPExcel->setActiveSheetIndex(0);

        // Output ke browser
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Laporan_Mutasi_' . date('YmdHis') . '.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }
}
