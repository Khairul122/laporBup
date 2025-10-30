<?php

require_once 'models/LaporanModel.php';
require_once __DIR__ . '/../vendor/tecnickcom/tcpdf/tcpdf.php';
require_once __DIR__ . '/../vendor/autoload.php';

// Disable error reporting to prevent output before PDF
error_reporting(0);
ini_set('display_errors', 0);

class LaporanController
{
    private $laporanModel;

    public function __construct()
    {
        $this->laporanModel = new LaporanModel();
        // Create TTD table if not exists
        $this->laporanModel->createTTDTable();
    }

    /**
     * Cek apakah user sudah login
     */
    private function isLoggedIn()
    {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }

    /**
     * Mendapatkan role user yang sedang login
     */
    private function getUserRole()
    {
        return $_SESSION['role'] ?? null;
    }

    /**
     * Mendapatkan data user yang sedang login
     */
    private function getCurrentUser()
    {
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
    private function requireLogin()
    {
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
    private function requireAdmin()
    {
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
    public function index()
    {
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
    public function generatePDF()
    {
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
    public function generateExcel()
    {
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
    private function generatePDFFile($data, $title, $role, $hari, $bulan, $tahun, $status, $tujuan)
    {
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

        // Set posisi X dan Y untuk teks kop (tengah kertas, accounting untuk logo di kiri)
        $center_x = 148.5; // Tengah halaman A4 Landscape (297/2)
        $y_text = $y_start + 2; // Sedikit lebih rendah dari logo agar sejajar

        // PEMERINTAH KABUPATEN MANDAILING NATAL
        $pdf->SetY($y_text);
        $pdf->SetFont('times', '', 16);
        $pdf->Cell(0, 6, 'PEMERINTAH KABUPATEN MANDAILING NATAL', 0, 1, 'C', 0, '', 0, false, 'T', 'M');

        // DINAS KOMUNIKASI DAN INFORMATIKA
        $pdf->SetY($y_text + 6);
        $pdf->SetFont('times', 'B', 20);
        $pdf->Cell(0, 6, 'DINAS KOMUNIKASI DAN INFORMATIKA', 0, 1, 'C', 0, '', 0, false, 'T', 'M');

        // KOMPLEK PERKANTORAN PAYALOTING
        $pdf->SetY($y_text + 13);
        $pdf->SetFont('times', '', 10);
        $pdf->Cell(0, 5, 'KOMPLEK PERKANTORAN PAYALOTING, PANYABUNGAN SUMATERA UTARA, KODE POS 22978', 0, 1, 'C', 0, '', 0, false, 'T', 'M');

        // Telp dan Fax
        $pdf->SetY($y_text + 17);
        $pdf->Cell(0, 5, 'Telp. (0636) 326255, 326258 Fax: (0636) 326254', 0, 1, 'C', 0, '', 0, false, 'T', 'M');

        // Email dan Website
        $pdf->SetY($y_text + 22);
        $pdf->Cell(0, 5, 'E-mail : diskominfo@M.madina.go.id    Website : www.diskominfo.madina.go.id', 0, 1, 'C', 0, '', 0, false, 'T', 'M');


        // Tambahkan garis horizontal di bawah kop
        $pdf->SetLineWidth(1);
        $pdf->Line(10, $y_text + 30, 287, $y_text + 30);
        $pdf->SetY($y_text + 35); // Pindah ke baris berikutnya

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
                    <th>Tujuan</th>
                    <th>Uraian Laporan</th>
                    <th>Status</th>
                    <th>Tanggal</th>';
        }

        $html .= '</tr>
        </thead>
        <tbody>';

        function formatTanggalIndonesia($tanggal)
        {
            $hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            $bulan = [
                'Januari',
                'Februari',
                'Maret',
                'April',
                'Mei',
                'Juni',
                'Juli',
                'Agustus',
                'September',
                'Oktober',
                'November',
                'Desember'
            ];

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

        // Add signature section
        $this->addSignatureToPDF($pdf, $role);

        $filename = $title . '_' . date('Y-m-d_H-i-s') . '.pdf';
        $pdf->Output($filename, 'I');
        exit;
    }

    /**
     * Generate Excel file using PhpSpreadsheet
     */
    private function generateExcelFile($data, $title, $role, $hari, $bulan, $tahun, $status, $tujuan)
    {
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
            $headers = array_merge($headers, ['Nama OPD', 'Nama Kegiatan', 'Uraian Laporan', 'Tujuan', 'Status', 'Tanggal']);
        } else {
            $headers = array_merge($headers, ['Nama Pelapor', 'Nama Desa', 'Nama Kecamatan', 'Tujuan', 'Uraian Laporan', 'Status', 'Tanggal']);
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
                    $item['tujuan'],
                    ucfirst($item['status_laporan']),
                    date('d/m/Y', strtotime($item['created_at']))
                ]);
            } else {
                $rowData = array_merge($rowData, [
                    $item['nama_pelapor'],
                    $item['nama_desa'],
                    $item['nama_kecamatan'],
                    $item['tujuan'],
                    strip_tags($item['uraian_laporan']),
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

        // Add signature section
        $this->addSignatureToExcel($sheet, $role, $row + 1);

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

    /**
     * Add signature to Excel
     */
    private function addSignatureToExcel($sheet, $role, $startRow)
    {
        // Get default signature data
        $defaultSignature = $this->laporanModel->getDefaultSignature($role);

        // Format tanggal otomatis
        $hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $bulan = [
            'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember'
        ];

        $nama_hari = $hari[date('w')];
        $tanggal = date('d');
        $nama_bulan = $bulan[date('n') - 1];
        $tahun = date('Y');

        $tempatTanggal = "Panyabungan, $nama_hari $tanggal $nama_bulan $tahun";

        // Add empty row for spacing
        $startRow += 2;

        // Add signature header
        $sheet->setCellValue('A' . $startRow, 'TANDA TANGAN');
        $sheet->mergeCells('A' . $startRow . ':' . $sheet->getHighestColumn() . $startRow);

        // Style signature header
        $headerStyle = [
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]
        ];
        $sheet->getStyle('A' . $startRow . ':' . $sheet->getHighestColumn() . $startRow)->applyFromArray($headerStyle);

        $startRow += 2;

        if ($defaultSignature) {
            // Tempat dan tanggal otomatis
            $sheet->setCellValue($sheet->getHighestColumn() . $startRow, $tempatTanggal);

            // Jabatan
            $startRow += 2;
            $sheet->setCellValue($sheet->getHighestColumn() . $startRow, $defaultSignature['jabatan_penanda_tangan']);

            // Space for signature
            $startRow += 3;
            $sheet->setCellValue($sheet->getHighestColumn() . $startRow, '(Tanda Tangan)');

            // Nama
            $startRow += 1;
            $sheet->setCellValue($sheet->getHighestColumn() . $startRow, $defaultSignature['nama_penanda_tangan']);

            // NIP
            $startRow += 1;
            $sheet->setCellValue($sheet->getHighestColumn() . $startRow, 'NIP. ' . $defaultSignature['nip']);
        } else {
            // Default signature
            $sheet->setCellValue($sheet->getHighestColumn() . $startRow, $tempatTanggal);

            $startRow += 2;
            $sheet->setCellValue($sheet->getHighestColumn() . $startRow, 'Plt. KEPALA DINAS KOMUNIKASI DAN INFORMATIKA');

            $startRow += 3;
            $sheet->setCellValue($sheet->getHighestColumn() . $startRow, '(Tanda Tangan)');

            $startRow += 1;
            $sheet->setCellValue($sheet->getHighestColumn() . $startRow, 'RAHMAD HIDAYAT, S.Pd');

            $startRow += 1;
            $sheet->setCellValue($sheet->getHighestColumn() . $startRow, 'PEMBINA UTAMA MUDA');

            $startRow += 1;
            $sheet->setCellValue($sheet->getHighestColumn() . $startRow, 'NIP. 19730417 199903 1 003');
        }

        // Style signature section
        $lastColumn = $sheet->getHighestColumn();
        $signatureStyle = [
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT]
        ];
        $sheet->getStyle($lastColumn . ($startRow - 7) . ':' . $lastColumn . $startRow)->applyFromArray($signatureStyle);
    }

    /**
     * Custom wordwrap function for PDF
     */
    private function pdfWordWrap($text, $maxWidth)
    {
        $words = explode(' ', $text);
        $lines = [];
        $currentLine = '';

        foreach ($words as $word) {
            if (strlen($currentLine . ' ' . $word) <= $maxWidth) {
                $currentLine .= ($currentLine ? ' ' : '') . $word;
            } else {
                if ($currentLine) {
                    $lines[] = $currentLine;
                }
                $currentLine = $word;
            }
        }

        if ($currentLine) {
            $lines[] = $currentLine;
        }

        return $lines;
    }

    /**
     * Add signature to PDF - Format Formal Indonesia yang Rapi
     */
    private function addSignatureToPDF($pdf, $role)
    {
        $defaultSignature = $this->laporanModel->getDefaultSignature($role);

        // Format tanggal formal Indonesia
        $bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                  'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        $tanggal = date('d');
        $nama_bulan = $bulan[date('n') - 1];
        $tahun = date('Y');
        $tempatTanggal = "Panyabungan, $tanggal $nama_bulan $tahun";

        // Pindah ke bawah untuk tanda tangan
        $pdf->SetY(200);

        if ($defaultSignature) {
            // 1. Tempat dan Tanggal - rata kanan
            $pdf->SetFont('times', '', 11);
            $pdf->Cell(0, 7, $tempatTanggal, 0, 1, 'R');

            // Spasi untuk tanda tangan
            $pdf->Ln(25);

            // 2. Jabatan - uppercase, rata kanan
            $pdf->SetFont('times', 'B', 11);
            $jabatan = strtoupper($defaultSignature['jabatan_penanda_tangan']);
            $pdf->Cell(0, 6, $jabatan, 0, 1, 'R');

            // Spasi untuk nama
            $pdf->Ln(25);

            // 3. Nama - uppercase, rata kanan
            $pdf->SetFont('times', 'B', 11);
            $nama = strtoupper($defaultSignature['nama_penanda_tangan']);
            $pdf->Cell(0, 6, $nama, 0, 1, 'R');
            $pdf->Ln(3);

            // 4. Pangkat - italic, rata kanan
            $pdf->SetFont('times', 'I', 10);
            $pangkat = $defaultSignature['pangkat'] ?? 'PEMBINA UTAMA MUDA';
            $pdf->Cell(0, 5, $pangkat, 0, 1, 'R');
            $pdf->Ln(2);

            // 5. NIP - rata kanan
            $pdf->SetFont('times', '', 10);
            $pdf->Cell(0, 5, 'NIP. ' . $defaultSignature['nip'], 0, 1, 'R');

        } else {
            // Default ketika tidak ada data signature
            // 1. Tempat dan Tanggal
            $pdf->SetFont('times', '', 11);
            $pdf->Cell(0, 7, $tempatTanggal, 0, 1, 'R');
            $pdf->Ln(25);

            // 2. Jabatan - multi-line jika perlu
            $pdf->SetFont('times', 'B', 11);
            $pdf->Cell(0, 6, 'PLT. KEPALA DINAS KOMUNIKASI DAN INFORMATIKA', 0, 1, 'R');
            $pdf->Ln(6);
            $pdf->Cell(0, 6, 'KABUPATEN MANDAILING NATAL', 0, 1, 'R');
            $pdf->Ln(25);

            // 3. Nama
            $pdf->SetFont('times', 'B', 11);
            $pdf->Cell(0, 6, 'RAHMAD HIDAYAT, S.Pd', 0, 1, 'R');
            $pdf->Ln(3);

            // 4. Pangkat
            $pdf->SetFont('times', 'I', 10);
            $pdf->Cell(0, 5, 'PEMBINA UTAMA MUDA', 0, 1, 'R');
            $pdf->Ln(2);

            // 5. NIP
            $pdf->SetFont('times', '', 10);
            $pdf->Cell(0, 5, 'NIP. 19730417 199903 1 003', 0, 1, 'R');
        }
    }


    /**
     * Halaman tanda tangan laporan
     */
    public function tandaTangan()
    {
        $this->requireAdmin();

        $id = $_GET['id'] ?? 0;
        $type = $_GET['type'] ?? 'camat'; // camat atau opd

        // Handle global tanda tangan (when id = 0)
        if ($id == 0) {
            $laporan = null;
        } else {
            // Get laporan data
            if ($type === 'opd') {
                $laporan = $this->laporanModel->getLaporanOPDById($id);
            } else {
                $laporan = $this->laporanModel->getLaporanCamatById($id);
            }

            if (!$laporan) {
                $_SESSION['error'] = 'Laporan tidak ditemukan';
                header('Location: index.php?controller=laporan&action=index');
                exit;
            }
        }

        // Get signature data if exists
        $signature = $this->laporanModel->getSignature($id, $type);

        include 'views/laporan/tanda-tangan.php';
    }

    /**
     * Upload tanda tangan
     */
    public function uploadTandaTangan()
    {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        // Prepare signature data according to table structure
        $data = [
            'jabatan' => trim($_POST['jabatan'] ?? ''),
            'nama_penanda_tangan' => trim($_POST['nama_penanda_tangan'] ?? ''),
            'jabatan_penanda_tangan' => trim($_POST['jabatan_penanda_tangan'] ?? ''),
            'nip' => trim($_POST['nip'] ?? '')
        ];

        // Save signature
        $result = $this->laporanModel->saveSignature($data);

        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    }

    /**
     * Generate PDF with signature
     */
    public function generatePDFWithSignature()
    {
        $this->requireAdmin();

        $id = $_GET['id'] ?? 0;
        $type = $_GET['type'] ?? 'camat';

        // Get laporan data
        if ($type === 'opd') {
            $laporan = $this->laporanModel->getLaporanOPDById($id);
            $title = 'Laporan OPD';
        } else {
            $laporan = $this->laporanModel->getLaporanCamatById($id);
            $title = 'Laporan Camat';
        }

        if (!$laporan) {
            $_SESSION['error'] = 'Laporan tidak ditemukan';
            header('Location: index.php?controller=laporan&action=index');
            exit;
        }

        // Get signature data
        $signature = $this->laporanModel->getSignature($id, $type);

        // Generate PDF with signature
        $this->generatePDFWithSignatureData($laporan, $title, $type, $signature);
    }

    /**
     * Generate PDF file with signature data
     */
    private function generatePDFWithSignatureData($laporan, $title, $type, $signature)
    {
        $pdf = new TCPDF('P', PDF_UNIT, 'A4', true, 'UTF-8', false);
        $pdf->SetCreator('SILAP GAWAT');
        $pdf->SetAuthor('Admin');
        $pdf->SetTitle($title . ' - ' . ($type === 'opd' ? $laporan['nama_kegiatan'] : $laporan['nama_pelapor']));
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(15, 15, 15);
        $pdf->AddPage();
        $pdf->SetFont('times', '', 12);

        // Header/KOP Surat
        $this->addPDFHeader($pdf);

        // Content
        $this->addPDFContent($pdf, $laporan, $type);

        // Signature Section
        $this->addPDFSignature($pdf, $signature);

        // Output PDF
        $filename = $title . '_Tanda_Tangan_' . date('Y-m-d_H-i-s') . '.pdf';
        $pdf->Output($filename, 'I');
        exit;
    }

    /**
     * Add PDF Header
     */
    private function addPDFHeader($pdf)
    {
        // Logo
        $logoPath = __DIR__ . '/../uploads/logo-resmi.png';
        if (file_exists($logoPath)) {
            $pdf->Image($logoPath, 15, 15, 25, 25);
        }

        // Instansi Info
        $pdf->SetFont('times', 'B', 14);
        $pdf->Cell(0, 5, 'PEMERINTAHAN KABUPATEN MANDAILING NATAL', 0, 1, 'C');
        $pdf->SetFont('times', 'B', 16);
        $pdf->Cell(0, 5, 'DINAS KOMUNIKASI DAN INFORMATIKA', 0, 1, 'C');
        $pdf->SetFont('times', 'I', 10);
        $pdf->Cell(0, 5, 'Jl. Lintas Sumatera No. 01 Panyabungan - 22916', 0, 1, 'C');
        $pdf->Cell(0, 5, 'Telp. (0638) 21123 Fax. (0638) 21634', 0, 1, 'C');

        // Line
        $pdf->Line(15, 50, 195, 50);
        $pdf->Line(15, 51, 195, 51);

        $pdf->Ln(10);
    }

    /**
     * Add PDF Content
     */
    private function addPDFContent($pdf, $laporan, $type)
    {
        $pdf->SetFont('times', 'B', 14);
        $pdf->Cell(0, 8, 'LAPORAN ' . strtoupper($type), 0, 1, 'C');
        $pdf->Ln(5);

        if ($type === 'opd') {
            $pdf->SetFont('times', '', 12);
            $pdf->Cell(40, 6, 'Nama OPD', 0, 0);
            $pdf->SetFont('times', 'B', 12);
            $pdf->Cell(0, 6, ': ' . $laporan['nama_opd'], 0, 1);

            $pdf->SetFont('times', '', 12);
            $pdf->Cell(40, 6, 'Nama Kegiatan', 0, 0);
            $pdf->SetFont('times', 'B', 12);
            $pdf->Cell(0, 6, ': ' . $laporan['nama_kegiatan'], 0, 1);

            $pdf->SetFont('times', '', 12);
            $pdf->Cell(40, 6, 'Tujuan', 0, 0);
            $pdf->SetFont('times', 'B', 12);
            $pdf->Cell(0, 6, ': ' . $laporan['tujuan'], 0, 1);
        } else {
            $pdf->SetFont('times', '', 12);
            $pdf->Cell(40, 6, 'Nama Pelapor', 0, 0);
            $pdf->SetFont('times', 'B', 12);
            $pdf->Cell(0, 6, ': ' . $laporan['nama_pelapor'], 0, 1);

            $pdf->SetFont('times', '', 12);
            $pdf->Cell(40, 6, 'Desa', 0, 0);
            $pdf->SetFont('times', 'B', 12);
            $pdf->Cell(0, 6, ': ' . $laporan['nama_desa'], 0, 1);

            $pdf->SetFont('times', '', 12);
            $pdf->Cell(40, 6, 'Kecamatan', 0, 0);
            $pdf->SetFont('times', 'B', 12);
            $pdf->Cell(0, 6, ': ' . $laporan['nama_kecamatan'], 0, 1);

            $pdf->SetFont('times', '', 12);
            $pdf->Cell(40, 6, 'Tujuan', 0, 0);
            $pdf->SetFont('times', 'B', 12);
            $pdf->Cell(0, 6, ': ' . $laporan['tujuan'], 0, 1);
        }

        $pdf->SetFont('times', '', 12);
        $pdf->Cell(40, 6, 'Tanggal Laporan', 0, 0);
        $pdf->SetFont('times', 'B', 12);
        $pdf->Cell(0, 6, ': ' . date('d F Y', strtotime($laporan['created_at'])), 0, 1);

        $pdf->Ln(10);

        $pdf->SetFont('times', '', 12);
        $pdf->Cell(40, 6, 'Uraian Laporan', 0, 1);
        $pdf->MultiCell(0, 6, strip_tags($laporan['uraian_laporan']), 0, 'L');
    }

    /**
     * Add PDF Signature Section
     */
    private function addPDFSignature($pdf, $signature)
    {
        $pdf->Ln(20);

        if ($signature) {
            // Tempat dan tanggal
            $pdf->SetFont('times', '', 12);
            $pdf->Cell(0, 6, $signature['tempat'] . ', ' . $signature['bulan'] . ' ' . $signature['tahun'], 0, 1, 'R');
            $pdf->Ln(15);

            // Jabatan
            $pdf->SetFont('times', 'B', 12);
            $pdf->Cell(0, 6, $signature['jabatan'], 0, 1, 'R');
            $pdf->Ln(25);

            // Signature image if exists
            if (!empty($signature['signature_path']) && file_exists($signature['signature_path'])) {
                $pdf->Image($signature['signature_path'], 150, $pdf->GetY(), 40, 20);
                $pdf->Ln(20);
            }

            // Nama
            $pdf->SetFont('times', 'B', 12);
            $pdf->Cell(0, 6, $signature['nama_penandatangan'], 0, 1, 'R');

            // Pangkat
            $pdf->SetFont('times', 'I', 10);
            $pdf->Cell(0, 4, $signature['pangkat'], 0, 1, 'R');

            // NIP
            $pdf->SetFont('times', '', 10);
            $pdf->Cell(0, 4, 'NIP. ' . $signature['nip'], 0, 1, 'R');
        } else {
            // Default signature when no signature data
            $pdf->SetFont('times', '', 12);
            $pdf->Cell(0, 6, 'Panyabungan, ' . date('F Y'), 0, 1, 'R');
            $pdf->Ln(15);

            $pdf->SetFont('times', 'B', 12);
            $pdf->Cell(0, 6, 'Plt. KEPALA DINAS KOMUNIKASI DAN INFORMATIKA', 0, 1, 'R');
            $pdf->Ln(25);

            $pdf->SetFont('times', 'B', 12);
            $pdf->Cell(0, 6, 'RAHMAD HIDAYAT, S.Pd', 0, 1, 'R');

            $pdf->SetFont('times', 'I', 10);
            $pdf->Cell(0, 4, 'PEMBINA UTAMA MUDA', 0, 1, 'R');

            $pdf->SetFont('times', '', 10);
            $pdf->Cell(0, 4, 'NIP. 19730417 199903 1 003', 0, 1, 'R');
        }
    }

    /**
     * Handle signature upload
     */
    private function handleSignatureUpload($file)
    {
        // Check file size (2MB limit)
        if ($file['size'] > 2 * 1024 * 1024) {
            return null;
        }

        // Check file type
        $allowedTypes = ['image/png', 'image/jpeg', 'image/jpg'];
        if (!in_array($file['type'], $allowedTypes)) {
            return null;
        }

        // Create upload directory if not exists
        $uploadDir = 'uploads/signatures/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'signature_' . date('YmdHis') . '_' . uniqid() . '.' . $extension;
        $uploadPath = $uploadDir . $filename;

        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            return $uploadPath;
        }

        return null;
    }
}
