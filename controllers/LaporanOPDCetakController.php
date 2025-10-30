<?php

require_once 'models/LaporanModel.php';
require_once __DIR__ . '/../vendor/tecnickcom/tcpdf/tcpdf.php';

class LaporanOPDCetakController {
    private $laporanModel;

    public function __construct() {
        $this->laporanModel = new LaporanModel();
    }

    /**
     * Cek apakah user sudah login
     */
    private function isLoggedIn() {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }

    /**
     * Require role admin untuk mengakses halaman
     */
    private function requireAdmin() {
        $this->requireLogin();

        if ($_SESSION['role'] !== 'admin') {
            $response = [
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke halaman ini'
            ];

            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                header('Content-Type: application/json');
                echo json_encode($response);
                exit;
            } else {
                header('Location: index.php?controller=dashboard&action=' . $_SESSION['role']);
                exit;
            }
        }
    }

    /**
     * Require login untuk mengakses halaman
     */
    private function requireLogin() {
        if (!$this->isLoggedIn()) {
            $response = [
                'success' => false,
                'message' => 'Silakan login terlebih dahulu',
                'redirect' => 'index.php'
            ];

            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                header('Content-Type: application/json');
                echo json_encode($response);
                exit;
            } else {
                header('Location: index.php');
                exit;
            }
        }
    }

    /**
     * Menampilkan halaman cetak laporan OPD
     */
    public function index() {
        $this->requireAdmin();

        // Parameters untuk filtering
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $search = $_GET['search'] ?? '';
        $status = $_GET['status'] ?? '';
        $hari = $_GET['hari'] ?? '';
        $bulan = $_GET['bulan'] ?? '';
        $tahun = $_GET['tahun'] ?? '';

        // Get data OPD
        $result = $this->laporanModel->getLaporanOPD($page, $limit, $search, $status);
        $laporans = $result['data'];
        $totalLaporan = $result['total'];
        $totalPages = $result['total_pages'];

        // Get statistics
        $statistics = $this->laporanModel->getOPDStatistics();

        // Format statistics untuk view
        $stats = [
            'total' => $statistics['total']['total_laporan'] ?? 0,
            'baru' => 0,
            'diproses' => 0,
            'selesai' => 0
        ];

        foreach ($statistics['by_status'] as $stat) {
            $stats[$stat['status_laporan']] = $stat['total'];
        }

        // Include view
        include 'views/laporan-opd-cetak/index.php';
    }

    /**
     * Generate PDF report untuk OPD
     */
    public function generatePDF() {
        $this->requireAdmin();

        $hari = $_GET['hari'] ?? '';
        $bulan = $_GET['bulan'] ?? '';
        $tahun = $_GET['tahun'] ?? '';
        $status = $_GET['status'] ?? '';

        // Get OPD data
        $data = $this->laporanModel->getLaporanOPDForPDF($hari, $bulan, $tahun, $status);
        $title = 'Laporan OPD';

        // Generate PDF
        $this->generatePDFFile($data, $title, 'opd', $hari, $bulan, $tahun, $status);
    }

    /**
     * Generate Excel report untuk OPD
     */
    public function generateExcel() {
        $this->requireAdmin();

        $hari = $_GET['hari'] ?? '';
        $bulan = $_GET['bulan'] ?? '';
        $tahun = $_GET['tahun'] ?? '';
        $status = $_GET['status'] ?? '';

        // Get OPD data
        $data = $this->laporanModel->getLaporanOPDForExcel($hari, $bulan, $tahun, $status);
        $title = 'Laporan OPD';

        // Generate Excel
        $this->generateExcelFile($data, $title, 'opd', $hari, $bulan, $tahun, $status);
    }

    /**
     * Generate PDF file menggunakan TCPDF
     */
    private function generatePDFFile($data, $title, $role, $hari, $bulan, $tahun, $status) {
        // Create new PDF document
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Set document information
        $pdf->SetCreator('SILAP GAWAT');
        $pdf->SetAuthor('Admin');
        $pdf->SetTitle($title);

        // Set header and footer
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // Set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // Add a page
        $pdf->AddPage();

        // Set font
        $pdf->SetFont('helvetica', '', 12);

        // Build title and filter information
        $html = '<h1 align="center">' . $title . '</h1>';

        if ($hari || $bulan || $tahun || $status) {
            $html .= '<h3>Filter:</h3><ul>';
            if ($hari) {
                $days = ['Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu',
                         'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu', 'Sunday' => 'Minggu'];
                $hari_display = $days[$hari] ?? $hari;
                $html .= '<li>Hari: ' . $hari_display . '</li>';
            }
            if ($bulan) {
                $months = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
                          7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
                $bulan_display = $months[(int)$bulan] ?? $bulan;
                $html .= '<li>Bulan: ' . $bulan_display . '</li>';
            }
            if ($tahun) $html .= '<li>Tahun: ' . $tahun . '</li>';
            if ($status) $html .= '<li>Status: ' . ucfirst($status) . '</li>';
            $html .= '</ul>';
        }

        $html .= '<table border="1" cellpadding="5">
            <thead>
                <tr style="background-color:#f0f0f0;">
                    <th width="10%">No</th>
                    <th width="25%">Nama OPD</th>
                    <th width="25%">Nama Kegiatan</th>
                    <th width="30%">Uraian Laporan</th>
                    <th width="15%">Status</th>
                    <th width="15%">Tanggal</th>
                </tr>
            </thead>
            <tbody>';

        // Add data rows
        $no = 1;
        foreach ($data as $row) {
            $html .= '<tr>
                <td>' . $no++ . '</td>
                <td>' . htmlspecialchars($row['nama_opd']) . '</td>
                <td>' . htmlspecialchars($row['nama_kegiatan']) . '</td>
                <td>' . htmlspecialchars(strip_tags($row['uraian_laporan'])) . '</td>
                <td>' . ucfirst($row['status_laporan']) . '</td>
                <td>' . date('d/m/Y', strtotime($row['created_at'])) . '</td>
            </tr>';
        }

        $html .= '</tbody>
        </table>';

        // Output the HTML content
        $pdf->writeHTML($html, true, false, true, false, '');

        // Close and output PDF document
        $filename = $title . '_' . date('Y-m-d_H-i-s') . '.pdf';
        $pdf->Output($filename, 'D');
        exit;
    }

    /**
     * Generate Excel file menggunakan PhpSpreadsheet
     */
    private function generateExcelFile($data, $title, $role, $hari, $bulan, $tahun, $status) {
        // Create new Spreadsheet object
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator('SILAP GAWAT')
            ->setLastModifiedBy('Admin')
            ->setTitle($title)
            ->setSubject($title)
            ->setDescription('Laporan ' . ucfirst($role));

        // Add sheet data
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle($title);

        // Set headers
        $headers = ['No', 'Nama OPD', 'Nama Kegiatan', 'Uraian Laporan', 'Status', 'Tanggal'];
        $sheet->fromArray($headers, NULL, 'A1');

        // Style header row
        $headerStyle = [
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => ['rgb' => 'E0E0E0']
            ],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]
        ];
        $sheet->getStyle('A1:' . $sheet->getHighestColumn() . '1')->applyFromArray($headerStyle);

        // Add data
        $row = 2;
        $no = 1;
        foreach ($data as $item) {
            $rowData = [
                $no++,
                $item['nama_opd'],
                $item['nama_kegiatan'],
                strip_tags($item['uraian_laporan']),
                ucfirst($item['status_laporan']),
                date('d/m/Y', strtotime($item['created_at']))
            ];

            $sheet->fromArray($rowData, NULL, 'A' . $row);
            $row++;
        }

        // Auto-size columns
        foreach (range('A', $sheet->getHighestColumn()) as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Create Excel file
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');

        // Set headers for download
        $filename = $title . '_' . date('Y-m-d_H-i-s') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
}