<?php

require_once 'models/LaporanModel.php';
require_once __DIR__ . '/../vendor/tecnickcom/tcpdf/tcpdf.php';

// Disable error reporting to prevent output before PDF
error_reporting(0);
ini_set('display_errors', 0);

class LaporanController {
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
     * Mendapatkan role user yang sedang login
     */
    private function getUserRole() {
        return $_SESSION['role'] ?? null;
    }

    /**
     * Mendapatkan data user yang sedang login
     */
    private function getCurrentUser() {
        if ($this->isLoggedIn()) {
            return [
                'id_user' => $_SESSION['user_id'],
                'username' => $_SESSION['username'],
                'email' => $_SESSION['email'],
                'jabatan' => $_SESSION['jabatan'],
                'role' => $_SESSION['role']
            ];
        }
        return null;
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
     * Menampilkan halaman laporan untuk admin
     */
    public function index() {
        $this->requireAdmin();

        $user = $this->getCurrentUser();

        // Parameters untuk filtering
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $search = $_GET['search'] ?? '';
        $status = $_GET['status'] ?? '';
        $tujuan = $_GET['tujuan'] ?? '';
        $hari = $_GET['hari'] ?? '';
        $bulan = $_GET['bulan'] ?? '';
        $tahun = $_GET['tahun'] ?? '';

        // Admin can access both OPD and Camat data
        $activeTab = $_GET['tab'] ?? 'camat';

        if ($activeTab === 'opd') {
            $result = $this->laporanModel->getLaporanOPD($page, $limit, $search, $status);
            $statistics = $this->laporanModel->getOPDStatistics();
            $tujuanOptions = [];
        } else {
            // Default to camat
            $result = $this->laporanModel->getLaporanCamat($page, $limit, $search, $status, $tujuan);
            $statistics = $this->laporanModel->getCamatStatistics();
            $tujuanOptions = $this->laporanModel->getCamatTujuanOptions();
        }

        $laporans = $result['data'];
        $totalLaporan = $result['total'];
        $totalPages = $result['total_pages'];

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

        // Include view dengan data yang sesuai
        include 'views/laporan/index.php';
    }

    /**
     * Generate PDF report
     */
    public function generatePDF() {
        $this->requireAdmin();

        $activeTab = $_GET['tab'] ?? 'camat';
        $hari = $_GET['hari'] ?? '';
        $bulan = $_GET['bulan'] ?? '';
        $tahun = $_GET['tahun'] ?? '';
        $status = $_GET['status'] ?? '';
        $tujuan = $_GET['tujuan'] ?? '';

        // Get data based on active tab
        if ($activeTab === 'opd') {
            $data = $this->laporanModel->getLaporanOPDForPDF($hari, $bulan, $tahun, $status);
            $title = 'Laporan OPD';
            $role = 'opd';
        } else {
            // Default to camat
            $data = $this->laporanModel->getLaporanCamatForPDF($hari, $bulan, $tahun, $status, $tujuan);
            $title = 'Laporan Camat';
            $role = 'camat';
        }

        // Generate PDF
        $this->generatePDFFile($data, $title, $role, $hari, $bulan, $tahun, $status, $tujuan);
    }

    /**
     * Generate Excel report
     */
    public function generateExcel() {
        $this->requireAdmin();

        $activeTab = $_GET['tab'] ?? 'camat';
        $hari = $_GET['hari'] ?? '';
        $bulan = $_GET['bulan'] ?? '';
        $tahun = $_GET['tahun'] ?? '';
        $status = $_GET['status'] ?? '';
        $tujuan = $_GET['tujuan'] ?? '';

        // Get data based on active tab
        if ($activeTab === 'opd') {
            $data = $this->laporanModel->getLaporanOPDForExcel($hari, $bulan, $tahun, $status);
            $title = 'Laporan OPD';
            $role = 'opd';
        } else {
            // Default to camat
            $data = $this->laporanModel->getLaporanCamatForExcel($hari, $bulan, $tahun, $status, $tujuan);
            $title = 'Laporan Camat';
            $role = 'camat';
        }

        // Generate Excel
        $this->generateExcelFile($data, $title, $role, $hari, $bulan, $tahun, $status, $tujuan);
    }

    /**
     * Generate PDF file using TCPDF
     */
private function generatePDFFile($data, $title, $role, $hari, $bulan, $tahun, $status, $tujuan) {
    $pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    $pdf->SetCreator('SILAP GAWAT');
    $pdf->SetAuthor('Admin');
    $pdf->SetTitle($title);

    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);

    $pdf->SetMargins(10, 10, 10);

    $pdf->AddPage();

    $pdf->SetFont('helvetica', '', 12);

    $logoPath = __DIR__ . '/../uploads/logo-resmi.png';

    // Set posisi Y awal untuk logo dan teks (misalnya 15mm dari atas)
    $y_start = 15;
    $x_logo = 15;
    $y_logo = $y_start;
    $w_logo = 20; // Lebar logo
    $h_logo = 24; // Tinggi logo

    // Tambahkan Logo menggunakan Image (untuk memastikan rendering)
    if (file_exists($logoPath)) {
        // Posisi logo di kiri, 15mm dari tepi kiri, y_start dari atas
        $pdf->Image($logoPath, $x_logo, $y_logo, $w_logo, $h_logo, '', '', 'T', false, 300, '', false, false, 0, false, false, false);
    }

    // Set posisi X dan Y untuk teks kop (di sebelah kanan logo, dan di tengah halaman)
    // Teks kop dimulai sekitar 45mm dari tepi kiri (setelah logo dan margin)
    $x_text = 45;
    $w_text = 297 - $x_text - 10; // Lebar halaman - posisi X teks - margin kanan (297 adalah lebar A4 Landscape)
    $y_text = $y_start;

    $pdf->SetX($x_text);
    $pdf->SetY($y_text);
    $pdf->SetFont('times', '', 16);
    $pdf->Cell($w_text, 0, 'PEMERINTAH KABUPATEN MANDAILING NATAL', 0, 1, 'C', 0, '', 0, false, 'T', 'M');

    $pdf->SetX($x_text);
    $pdf->SetFont('times', 'B', 20);
    $pdf->Cell($w_text, 0, 'DINAS KOMUNIKASI DAN INFORMATIKA', 0, 1, 'C', 0, '', 0, false, 'T', 'M');

    $pdf->SetX($x_text);
    $pdf->SetFont('times', '', 10);
    $pdf->Cell($w_text, 0, 'KOMPLEK PERKANTORAN PAYALOTING, PANYABUNGAN SUMATERA UTARA, KODE POS 22978', 0, 1, 'C', 0, '', 0, false, 'T', 'M');

    $pdf->SetX($x_text);
    $pdf->Cell($w_text, 0, 'Telp. (0636) 326255, 326258 Fax: (0636) 326254', 0, 1, 'C', 0, '', 0, false, 'T', 'M');

    $pdf->SetX($x_text);
    $pdf->Cell($w_text, 0, 'E-mail : diskominfo@M.madina.go.id Website : www.diskominfo.madina.go.id', 0, 1, 'C', 0, '', 0, false, 'T', 'M');


    // Tambahkan garis horizontal di bawah kop
    $pdf->SetLineWidth(1);
    $pdf->Line(10, $y_start + $h_logo + 5, 287, $y_start + $h_logo + 5);
    $pdf->SetY($y_start + $h_logo + 8); // Pindah ke baris berikutnya

    $html = '<h1 align="center" style="font-size: 18px; font-weight: bold; margin-top: 20px; line-height: 1.0;">' . $title . '</h1>';

    $html .= '<table border="1" cellpadding="5">
        <thead>
            <tr style="background-color:#f0f0f0;">
                <th>No</th>';

    if ($role === 'opd') {
        $html .= '<th>Nama OPD</th>
                    <th>Nama Kegiatan</th>
                    <th>Uraian Laporan</th>
                    <th>Tujuan</th>
                    <th>Status</th>
                    <th>Tanggal</th>';
    } else {
        $html .= '<th>Nama Pelapor</th>
                    <th>Nama Desa</th>
                    <th>Nama Kecamatan</th>
                    <th>Waktu Kejadian</th>
                    <th>Tujuan</th>
                    <th>Uraian Laporan</th>
                    <th>Status</th>
                    <th>Tanggal</th>';
    }

    $html .= '</tr>
        </thead>
        <tbody>';

    function formatTanggalIndonesia($tanggal) {
        $hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        $timestamp = strtotime($tanggal);
        $nama_hari = $hari[date('w', $timestamp)];
        $tanggal_num = date('d', $timestamp);
        $nama_bulan = $bulan[date('n', $timestamp) - 1];
        $tahun = date('Y', $timestamp);

        return "$nama_hari, $tanggal_num $nama_bulan $tahun";
    }

    $no = 1;
    foreach ($data as $row) {
        $html .= '<tr>
                <td>' . $no++ . '</td>';

        if ($role === 'opd') {
            $html .= '<td>' . htmlspecialchars($row['nama_opd'] ?? '') . '</td>
                        <td>' . htmlspecialchars($row['nama_kegiatan'] ?? '') . '</td>
                        <td>' . htmlspecialchars(strip_tags($row['uraian_laporan'] ?? '')) . '</td>
                        <td>' . htmlspecialchars($row['tujuan'] ?? '') . '</td>
                        <td>' . ucfirst($row['status_laporan'] ?? '') . '</td>
                        <td>' . formatTanggalIndonesia($row['created_at']) . '</td>';
        } else {
            $html .= '<td>' . htmlspecialchars($row['nama_pelapor'] ?? '') . '</td>
                        <td>' . htmlspecialchars($row['nama_desa'] ?? '') . '</td>
                        <td>' . htmlspecialchars($row['nama_kecamatan'] ?? '') . '</td>
                        <td>' . htmlspecialchars($row['waktu_kejadian'] ?? '') . '</td>
                        <td>' . htmlspecialchars($row['tujuan'] ?? '') . '</td>
                        <td>' . htmlspecialchars(strip_tags($row['uraian_laporan'] ?? '')) . '</td>
                        <td>' . ucfirst($row['status_laporan'] ?? '') . '</td>
                        <td>' . formatTanggalIndonesia($row['created_at']) . '</td>';
        }

        $html .= '</tr>';
    }

    $html .= '</tbody>
    </table>';

    $pdf->writeHTML($html, true, false, true, false, '');

    $filename = $title . '_' . date('Y-m-d_H-i-s') . '.pdf';
    $pdf->Output($filename, 'I');
    exit;
}

    /**
     * Generate Excel file using PhpSpreadsheet
     */
    private function generateExcelFile($data, $title, $role, $hari, $bulan, $tahun, $status, $tujuan) {
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
        $headers = ['No'];
        if ($role === 'opd') {
            $headers = array_merge($headers, ['Nama OPD', 'Nama Kegiatan', 'Uraian Laporan', 'Status', 'Tanggal']);
        } else {
            $headers = array_merge($headers, ['Nama Kecamatan', 'Nama Kegiatan', 'Uraian Laporan', 'Tujuan', 'Status', 'Tanggal']);
        }

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
            $rowData = [$no++];

            if ($role === 'opd') {
                $rowData = array_merge($rowData, [
                    $item['nama_opd'],
                    $item['nama_kegiatan'],
                    strip_tags($item['uraian_laporan']),
                    ucfirst($item['status_laporan']),
                    date('d/m/Y', strtotime($item['created_at']))
                ]);
            } else {
                $rowData = array_merge($rowData, [
                    $item['nama_kecamatan'],
                    $item['nama_kegiatan'],
                    strip_tags($item['uraian_laporan']),
                    $item['tujuan'],
                    ucfirst($item['status_laporan']),
                    date('d/m/Y', strtotime($item['created_at']))
                ]);
            }

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