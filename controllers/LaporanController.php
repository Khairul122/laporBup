<?php

require_once 'models/LaporanModel.php';
require_once __DIR__ . '/../vendor/tecnickcom/tcpdf/tcpdf.php';
require_once __DIR__ . '/../vendor/autoload.php';

class LaporanController extends BaseController {
    private $laporanModel;

    public function __construct()
    {
        $this->laporanModel = new LaporanModel();
        
        $this->laporanModel->createTTDTable();
    }

    
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
                $this->redirectToDashboard();
            }
        }
    }

    
    public function index()
    {
        $this->requireAdmin();

        $user = $this->getCurrentUser();

        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $search = $_GET['search'] ?? '';
        $status = $_GET['status'] ?? '';
        $tujuan = $_GET['tujuan'] ?? '';
        $hari = $_GET['hari'] ?? '';
        $bulan = $_GET['bulan'] ?? '';
        $tahun = $_GET['tahun'] ?? '';

        
        $activeTab = $_GET['tab'] ?? 'camat';

        if ($activeTab === 'opd') {
            $result = $this->laporanModel->getLaporanOPD($page, $limit, $search, $status);
            $statistics = $this->laporanModel->getOPDStatistics();
            $tujuanOptions = [];
        } else {
            
            $result = $this->laporanModel->getLaporanCamat($page, $limit, $search, $status, $tujuan);
            $statistics = $this->laporanModel->getCamatStatistics();
            $tujuanOptions = $this->laporanModel->getCamatTujuanOptions();
        }

        $laporans = $result['data'];
        $totalLaporan = $result['total'];
        $totalPages = $result['total_pages'];

        
        $stats = [
            'total' => $statistics['total']['total_laporan'] ?? 0,
            'baru' => 0,
            'diproses' => 0,
            'selesai' => 0
        ];

        foreach ($statistics['by_status'] as $stat) {
            $stats[$stat['status_laporan']] = $stat['total'];
        }

        
        include 'views/laporan/index.php';
    }

    
    public function generatePDF()
    {
        $this->requireAdmin();

        $activeTab = $_GET['tab'] ?? 'camat';
        $hari = $_GET['hari'] ?? '';
        $bulan = $_GET['bulan'] ?? '';
        $tahun = $_GET['tahun'] ?? '';
        $status = $_GET['status'] ?? '';
        $tujuan = $_GET['tujuan'] ?? '';

        
        if ($activeTab === 'opd') {
            $data = $this->laporanModel->getLaporanOPDForPDF($hari, $bulan, $tahun, $status);
            $title = 'Laporan OPD';
            $role = 'opd';
        } else {
            
            $data = $this->laporanModel->getLaporanCamatForPDF($hari, $bulan, $tahun, $status, $tujuan);
            $title = 'Laporan Camat';
            $role = 'camat';
        }

        
        $this->generatePDFFile($data, $title, $role, $hari, $bulan, $tahun, $status, $tujuan);
    }

    
    public function generateExcel()
    {
        $this->requireAdmin();

        $activeTab = $_GET['tab'] ?? 'camat';
        $hari = $_GET['hari'] ?? '';
        $bulan = $_GET['bulan'] ?? '';
        $tahun = $_GET['tahun'] ?? '';
        $status = $_GET['status'] ?? '';
        $tujuan = $_GET['tujuan'] ?? '';

        
        if ($activeTab === 'opd') {
            $data = $this->laporanModel->getLaporanOPDForExcel($hari, $bulan, $tahun, $status);
            $title = 'Laporan OPD';
            $role = 'opd';
        } else {
            
            $data = $this->laporanModel->getLaporanCamatForExcel($hari, $bulan, $tahun, $status, $tujuan);
            $title = 'Laporan Camat';
            $role = 'camat';
        }

        
        $this->generateExcelFile($data, $title, $role, $hari, $bulan, $tahun, $status, $tujuan);
    }

    
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

        
        $y_start = 15;
        $x_logo = 15;
        $y_logo = $y_start;
        $w_logo = 20; 
        $h_logo = 24; 

        
        if (file_exists($logoPath)) {
            
            $pdf->Image($logoPath, $x_logo, $y_logo, $w_logo, $h_logo, '', '', 'T', false, 300, '', false, false, 0, false, false, false);
        }

        
        $center_x = 148.5; 
        $y_text = $y_start + 2; 

        
        $pdf->SetY($y_text);
        $pdf->SetFont('times', '', 16);
        $pdf->Cell(0, 6, 'PEMERINTAH KABUPATEN MANDAILING NATAL', 0, 1, 'C', 0, '', 0, false, 'T', 'M');

        
        $pdf->SetY($y_text + 6);
        $pdf->SetFont('times', 'B', 20);
        $pdf->Cell(0, 6, 'DINAS KOMUNIKASI DAN INFORMATIKA', 0, 1, 'C', 0, '', 0, false, 'T', 'M');

        
        $pdf->SetY($y_text + 13);
        $pdf->SetFont('times', '', 10);
        $pdf->Cell(0, 5, 'KOMPLEK PERKANTORAN PAYALOTING, PANYABUNGAN SUMATERA UTARA, KODE POS 22978', 0, 1, 'C', 0, '', 0, false, 'T', 'M');

        
        $pdf->SetY($y_text + 17);
        $pdf->Cell(0, 5, 'Telp. (0636) 326255, 326258 Fax: (0636) 326254', 0, 1, 'C', 0, '', 0, false, 'T', 'M');

        
        $pdf->SetY($y_text + 22);
        $pdf->Cell(0, 5, 'E-mail : diskominfo@M.madina.go.id    Website : www.diskominfo.madina.go.id', 0, 1, 'C', 0, '', 0, false, 'T', 'M');

        
        $pdf->SetLineWidth(1);
        $pdf->Line(10, $y_text + 30, 287, $y_text + 30);
        $pdf->SetY($y_text + 35); 

        $html = '<h1 align="center" style="font-size: 18px; font-weight: bold; margin-top: 20px; line-height: 1.0;">' . $title . '</h1>';

        $html .= '<table border="1" cellpadding="5" cellspacing="0" style="border-collapse:collapse; width:100%; text-align:justify; vertical-align:middle;">
        <thead>
            <tr style="background-color:#f0f0f0;">
                <th style="width:30px">No</th>';

        if ($role === 'opd') {
            $html .= '<th>Nama OPD</th>
                    <th>Nama Kegiatan</th>
                    <th style="width:230px">Uraian Laporan</th>
                    <th>Tujuan</th>
                    <th>Tanggal</th>';
        } else {
            $html .= '<th>Nama Pelapor</th>
                    <th>Nama Desa</th>
                    <th>Nama Kecamatan</th>
                    <th>Tujuan</th>
                    <th style="width:200px">Uraian Laporan</th>
                    <th>Tanggal</th>';
        }

        $html .= '</tr>
        </thead>
        <tbody>';

        $no = 1;
        foreach ($data as $row) {
            $html .= '<tr>
                <td style="width:30px">' . $no++ . '</td>';

            if ($role === 'opd') {
                
                $tujuan = $row['tujuan'] ?? '';
                if ($tujuan === 'dinas kominfo') {
                    $tujuan_display = 'DINAS KOMUNIKASI DAN INFORMATIKA';
                } else {
                    $tujuan_display = $tujuan;
                }
                
                $html .= '<td>' . htmlspecialchars($row['nama_opd'] ?? '') . '</td>
                        <td>' . htmlspecialchars($row['nama_kegiatan'] ?? '') . '</td>
                        <td style="width:230px">' . htmlspecialchars(strip_tags($row['uraian_laporan'] ?? '')) . '</td>
                        <td>' . htmlspecialchars($tujuan_display) . '</td>
                        <td>' . formatTanggalIndonesia($row['created_at']) . '</td>';
            } else {
                
                $tujuan = $row['tujuan'] ?? '';
                if ($tujuan === 'dinas kominfo') {
                    $tujuan_display = 'DINAS KOMUNIKASI DAN INFORMATIKA';
                } else {
                    $tujuan_display = $tujuan;
                }
                
                $html .= '<td>' . htmlspecialchars($row['nama_pelapor'] ?? '') . '</td>
                        <td>' . htmlspecialchars($row['nama_desa'] ?? '') . '</td>
                        <td>' . htmlspecialchars($row['nama_kecamatan'] ?? '') . '</td>
                        <td>' . htmlspecialchars($tujuan_display) . '</td>
                        <td style="width:200px">' . htmlspecialchars(strip_tags($row['uraian_laporan'] ?? '')) . '</td>
                        <td>' . formatTanggalIndonesia($row['created_at']) . '</td>';
            }

            $html .= '</tr>';
        }

        $html .= '</tbody>
    </table>';

        $pdf->writeHTML($html, true, false, true, false, '');

        
        $this->addSignatureToPDF($pdf, $role);

        $filename = $title . '_' . date('Y-m-d_H-i-s') . '.pdf';
        $pdf->Output($filename, 'I');
        exit;
    }

    
    private function generateExcelFile($data, $title, $role, $hari, $bulan, $tahun, $status, $tujuan)
    {
        
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

        
        $spreadsheet->getProperties()
            ->setCreator('SILAP GAWAT')
            ->setLastModifiedBy('Admin')
            ->setTitle($title)
            ->setSubject($title)
            ->setDescription('Laporan ' . ucfirst($role));

        
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle($title);

        
        $headers = ['No'];
        if ($role === 'opd') {
            $headers = array_merge($headers, ['Nama OPD', 'Nama Kegiatan', 'Uraian Laporan', 'Tujuan', 'Status', 'Tanggal']);
        } else {
            $headers = array_merge($headers, ['Nama Pelapor', 'Nama Desa', 'Nama Kecamatan', 'Tujuan', 'Uraian Laporan', 'Status', 'Tanggal']);
        }

        $sheet->fromArray($headers, NULL, 'A1');

        
        $headerStyle = [
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => ['rgb' => 'E0E0E0']
            ],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]
        ];
        $sheet->getStyle('A1:' . $sheet->getHighestColumn() . '1')->applyFromArray($headerStyle);

        
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

        
        foreach (range('A', $sheet->getHighestColumn()) as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        
        $this->addSignatureToExcel($sheet, $role, $row + 1);

        
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');

        
        $filename = $title . '_' . date('Y-m-d_H-i-s') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    
    private function addSignatureToExcel($sheet, $role, $startRow)
    {
        $defaultSignature = $this->laporanModel->getDefaultSignature($role);

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

        $tanggal = date('d');
        $nama_bulan = $bulan[date('n') - 1];
        $tahun = date('Y');
        $tempatTanggal = "Panyabungan, $tanggal $nama_bulan $tahun";

        $startRow += 5;
        $lastColumn = $sheet->getHighestColumn();

        if ($defaultSignature) {
            $sheet->setCellValue($lastColumn . $startRow, $tempatTanggal);
            $sheet->getStyle($lastColumn . $startRow)->getFont()->setSize(11);
            $sheet->getStyle($lastColumn . $startRow)->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT)
                ->setIndent(3);

            $startRow += 1;

            $sheet->setCellValue($lastColumn . $startRow, strtoupper($defaultSignature['jabatan_penanda_tangan']));
            $sheet->getStyle($lastColumn . $startRow)->getFont()->setBold(true);
            $sheet->getStyle($lastColumn . $startRow)->getFont()->setSize(11);
            $sheet->getStyle($lastColumn . $startRow)->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT)
                ->setIndent(3);

            $startRow += 4;

            $sheet->setCellValue($lastColumn . $startRow, strtoupper($defaultSignature['nama_penanda_tangan']));
            $sheet->getStyle($lastColumn . $startRow)->getFont()->setBold(true);
            $sheet->getStyle($lastColumn . $startRow)->getFont()->setSize(11);
            $sheet->getStyle($lastColumn . $startRow)->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT)
                ->setIndent(3);

            $startRow += 1;
            $sheet->setCellValue($lastColumn . $startRow, $defaultSignature['pangkat'] ?? 'PEMBINA UTAMA MUDA');
            $sheet->getStyle($lastColumn . $startRow)->getFont()->setItalic(true);
            $sheet->getStyle($lastColumn . $startRow)->getFont()->setSize(10);
            $sheet->getStyle($lastColumn . $startRow)->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT)
                ->setIndent(3);

            $startRow += 1;
            $sheet->setCellValue($lastColumn . $startRow, 'NIP. ' . $defaultSignature['nip']);
            $sheet->getStyle($lastColumn . $startRow)->getFont()->setSize(10);
            $sheet->getStyle($lastColumn . $startRow)->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT)
                ->setIndent(3);
        } else {
            $sheet->setCellValue($lastColumn . $startRow, $tempatTanggal);
            $sheet->getStyle($lastColumn . $startRow)->getFont()->setSize(11);
            $sheet->getStyle($lastColumn . $startRow)->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT)
                ->setIndent(3);

            $startRow += 3;

            $sheet->setCellValue($lastColumn . $startRow, 'PLT. KEPALA DINAS KOMUNIKASI DAN INFORMATIKA KABUPATEN MANDAILING NATAL');
            $sheet->getStyle($lastColumn . $startRow)->getFont()->setBold(true);
            $sheet->getStyle($lastColumn . $startRow)->getFont()->setSize(11);
            $sheet->getStyle($lastColumn . $startRow)->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT)
                ->setIndent(3);

            $startRow += 4;

            $sheet->setCellValue($lastColumn . $startRow, 'RAHMAD HIDAYAT, S.Pd');
            $sheet->getStyle($lastColumn . $startRow)->getFont()->setBold(true);
            $sheet->getStyle($lastColumn . $startRow)->getFont()->setSize(11);
            $sheet->getStyle($lastColumn . $startRow)->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT)
                ->setIndent(3);

            $startRow += 1;
            $sheet->setCellValue($lastColumn . $startRow, 'PEMBINA UTAMA MUDA');
            $sheet->getStyle($lastColumn . $startRow)->getFont()->setItalic(true);
            $sheet->getStyle($lastColumn . $startRow)->getFont()->setSize(10);
            $sheet->getStyle($lastColumn . $startRow)->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT)
                ->setIndent(3);

            $startRow += 1;
            $sheet->setCellValue($lastColumn . $startRow, 'NIP. 19730417 199903 1 003');
            $sheet->getStyle($lastColumn . $startRow)->getFont()->setSize(10);
            $sheet->getStyle($lastColumn . $startRow)->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT)
                ->setIndent(3);
        }
    }

    
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

    
    private function addSignatureToPDF($pdf, $role)
    {
        
        $pageHeight = $pdf->getPageHeight();
        $currentY = $pdf->GetY();
        $pageMargin = 20; 
        $usablePageHeight = $pageHeight - (2 * $pageMargin);
        $tableThreshold = 0.7 * $usablePageHeight; 

        
        if ($currentY > $tableThreshold) {
            
            $pdf->AddPage();
            $startY = 120; 
        } else {
            
            $startY = $currentY + 20; 
        }

        $defaultSignature = $this->laporanModel->getDefaultSignature($role);

        
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

        $tanggal = date('d');
        $nama_bulan = $bulan[date('n') - 1];
        $tahun = date('Y');
        $tempatTanggal = "Panyabungan, $tanggal $nama_bulan $tahun";

        
        $startX = 200;  

        if ($defaultSignature) {
            
            $pdf->SetFont('times', '', 11);
            $pdf->SetXY($startX, $startY);
            $pdf->Cell(70, 6, $tempatTanggal, 0, 0, 'L');

            
            $pdf->SetFont('times', 'B', 11);
            $pdf->SetXY($startX, $startY + -4 + 10);
            $pdf->MultiCell(70, 5, strtoupper($defaultSignature['jabatan_penanda_tangan']), 0, 'L');

            
            $pdf->SetFont('times', 'B', 11);
            $pdf->SetXY($startX, $startY + 6 + 13 + 13);
            $pdf->Cell(70, 6, strtoupper($defaultSignature['nama_penanda_tangan']), 0, 0, 'L');

            
            $pdf->SetFont('times', '', 10);
            $pdf->SetXY($startX, $startY + 6 + 10 + 15 + 6);
            $pdf->Cell(70, 5, $defaultSignature['pangkat'] ?? 'PEMBINA UTAMA MUDA', 0, 0, 'L');

            
            $pdf->SetFont('times', '', 10);
            $pdf->SetXY($startX, $startY + 6 + 10 + 15 + 6 + 5);
            $pdf->Cell(70, 5, 'NIP. ' . $defaultSignature['nip'], 0, 0, 'L');
        } else {
            
            
            $pdf->SetFont('times', '', 11);
            $pdf->SetXY($startX, $startY);
            $pdf->Cell(70, 6, $tempatTanggal, 0, 0, 'L');

            
            $pdf->SetFont('times', 'B', 11);
            $pdf->SetXY($startX, $startY + 6 + 10);
            $pdf->MultiCell(70, 5, "PLT. KEPALA DINAS KOMUNIKASI DAN INFORMATIKA\nKABUPATEN MANDAILING NATAL", 0, 'L');

            
            $pdf->SetFont('times', 'B', 11);
            $pdf->SetXY($startX, $startY + 6 + 10 + 15);
            $pdf->Cell(70, 6, 'RAHMAD HIDAYAT, S.Pd', 0, 0, 'L');

            
            $pdf->SetFont('times', 'I', 10);
            $pdf->SetXY($startX, $startY + 6 + 10 + 15 + 6);
            $pdf->Cell(70, 5, 'PEMBINA UTAMA MUDA', 0, 0, 'L');

            
            $pdf->SetFont('times', '', 10);
            $pdf->SetXY($startX, $startY + 6 + 10 + 15 + 6 + 5);
            $pdf->Cell(70, 5, 'NIP. 19730417 199903 1 003', 0, 0, 'L');
        }
    }

    
    public function tandaTangan($type = 'camat', $id = 0)
    {
        $this->requireAdmin();

        if ($id == 0) {
            $laporan = null;
        } else {
            
            if ($type === 'opd') {
                $laporan = $this->laporanModel->getLaporanOPDById($id);
            } else {
                $laporan = $this->laporanModel->getLaporanCamatById($id);
            }

            if (!$laporan) {
                $_SESSION['error'] = 'Laporan tidak ditemukan';
                $this->redirect(route('laporan', 'index'));
            }
        }

        
        $signature = $this->laporanModel->getSignature($id, $type);

        include 'views/laporan/tanda-tangan.php';
    }

    
    public function uploadTandaTangan()
    {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        
        $data = [
            'nama_penanda_tangan' => trim($_POST['nama_penanda_tangan'] ?? ''),
            'jabatan_penanda_tangan' => trim($_POST['jabatan_penanda_tangan'] ?? ''),
            'pangkat' => trim($_POST['pangkat'] ?? ''),
            'nip' => trim($_POST['nip'] ?? '')
        ];

        
        $result = $this->laporanModel->saveSignature($data);

        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    }

    
    public function generatePDFWithSignature($type = 'camat', $id = 0)
    {
        $this->requireAdmin();

        
        if ($type === 'opd') {
            $laporan = $this->laporanModel->getLaporanOPDById($id);
            $title = 'Laporan OPD';
        } else {
            $laporan = $this->laporanModel->getLaporanCamatById($id);
            $title = 'Laporan Camat';
        }

        if (!$laporan) {
            $_SESSION['error'] = 'Laporan tidak ditemukan';
            $this->redirect(route('laporan', 'index'));
        }

        
        $signature = $this->laporanModel->getSignature($id, $type);

        
        $this->generatePDFWithSignatureData($laporan, $title, $type, $signature);
    }

    
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

        
        $this->addPDFHeader($pdf);

        
        $this->addPDFContent($pdf, $laporan, $type);

        
        $this->addPDFSignature($pdf, $signature);

        
        $filename = $title . '_Tanda_Tangan_' . date('Y-m-d_H-i-s') . '.pdf';
        $pdf->Output($filename, 'I');
        exit;
    }

    
    private function addPDFHeader($pdf)
    {
        
        $logoPath = __DIR__ . '/../uploads/logo-resmi.png';
        if (file_exists($logoPath)) {
            $pdf->Image($logoPath, 15, 15, 25, 25);
        }

        
        $pdf->SetFont('times', 'B', 14);
        $pdf->Cell(0, 5, 'PEMERINTAHAN KABUPATEN MANDAILING NATAL', 0, 1, 'C');
        $pdf->SetFont('times', 'B', 16);
        $pdf->Cell(0, 5, 'DINAS KOMUNIKASI DAN INFORMATIKA', 0, 1, 'C');
        $pdf->SetFont('times', 'I', 10);
        $pdf->Cell(0, 5, 'Jl. Lintas Sumatera No. 01 Panyabungan - 22916', 0, 1, 'C');
        $pdf->Cell(0, 5, 'Telp. (0638) 21123 Fax. (0638) 21634', 0, 1, 'C');

        
        $pdf->Line(15, 50, 195, 50);
        $pdf->Line(15, 51, 195, 51);

        $pdf->Ln(10);
    }

    
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

    
    private function addPDFSignature($pdf, $signature)
    {
        $pdf->Ln(20);

        if ($signature) {
            
            $pdf->SetFont('times', '', 12);
            $pdf->Cell(0, 6, $signature['tempat'] . ', ' . $signature['bulan'] . ' ' . $signature['tahun'], 0, 1, 'R');
            $pdf->Ln(15);

            
            $pdf->SetFont('times', 'B', 12);
            $pdf->Cell(0, 6, $signature['jabatan'], 0, 1, 'R');
            $pdf->Ln(25);

            
            if (!empty($signature['signature_path']) && file_exists($signature['signature_path'])) {
                $pdf->Image($signature['signature_path'], 150, $pdf->GetY(), 40, 20);
                $pdf->Ln(20);
            }

            
            $pdf->SetFont('times', 'B', 12);
            $pdf->Cell(0, 6, $signature['nama_penandatangan'], 0, 1, 'R');

            
            $pdf->SetFont('times', 'I', 10);
            $pdf->Cell(0, 4, $signature['pangkat'], 0, 1, 'R');

            
            $pdf->SetFont('times', '', 10);
            $pdf->Cell(0, 4, 'NIP. ' . $signature['nip'], 0, 1, 'R');
        } else {
            
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

    
    private function handleSignatureUpload($file)
    {
        
        if ($file['size'] > 2 * 1024 * 1024) {
            return null;
        }

        
        $allowedTypes = ['image/png', 'image/jpeg', 'image/jpg'];
        if (!in_array($file['type'], $allowedTypes)) {
            return null;
        }

        
        $uploadDir = 'uploads/signatures/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'signature_' . date('YmdHis') . '_' . uniqid() . '.' . $extension;
        $uploadPath = $uploadDir . $filename;

        
        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            return $uploadPath;
        }

        return null;
    }
}