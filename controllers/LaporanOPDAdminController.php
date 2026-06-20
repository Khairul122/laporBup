<?php

require_once 'models/LaporanOPDAdminModel.php';

class LaporanOPDAdminController extends BaseController {
    private $laporanOPDAdminModel;

    public function __construct() {
        $this->laporanOPDAdminModel = new LaporanOPDAdminModel();
    }

    
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
                $this->redirect(route('dashboard', 'admin'));
            }
        }
    }

    
    public function index() {
        $this->requireAdmin();

        $user = $this->getCurrentUser();
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $search = $_GET['search'] ?? '';
        $status = $_GET['status'] ?? '';

        
        $result = $this->laporanOPDAdminModel->getAllLaporanOPD($page, $limit, $search, $status);
        $laporans = $result['data'];
        $totalLaporan = $result['total'];
        $totalPages = $result['total_pages'];

        
        $statistics = $this->laporanOPDAdminModel->getLaporanOPDStatistics();

        
        $stats = [
            'total' => $statistics['total']['total_laporan'] ?? 0,
            'baru' => 0,
            'diproses' => 0,
            'selesai' => 0
        ];

        foreach ($statistics['by_status'] as $stat) {
            $stats[$stat['status_laporan']] = $stat['total'];
        }

        
        include 'views/laporan-admin-opd/index.php';
    }

    
    public function detail($id = null) {
        $this->requireAdmin();

        $id = $id !== null ? (int) $id : (isset($_GET['id']) ? (int) $_GET['id'] : 0);

        if ($id === 0) {
            $_SESSION['error'] = "ID laporan tidak valid.";
            $this->redirect(route('laporanOPDAdmin', 'index'));
        }

        $laporan = $this->laporanOPDAdminModel->getLaporanOPDById($id);

        if (!$laporan) {
            $_SESSION['error'] = "Laporan tidak ditemukan.";
            $this->redirect(route('laporanOPDAdmin', 'index'));
        }

        include 'views/laporan-admin-opd/detail.php';
    }

    
    public function edit($id = null) {
        $this->requireAdmin();

        $id = $id !== null ? (int) $id : (isset($_GET['id']) ? (int) $_GET['id'] : 0);

        if ($id === 0) {
            $_SESSION['error'] = "ID laporan tidak valid.";
            $this->redirect(route('laporanOPDAdmin', 'index'));
        }

        $laporan = $this->laporanOPDAdminModel->getLaporanOPDById($id);

        if (!$laporan) {
            $_SESSION['error'] = "Laporan tidak ditemukan.";
            $this->redirect(route('laporanOPDAdmin', 'index'));
        }

        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nama_opd' => trim($_POST['nama_opd']),
                'uraian_laporan' => trim($_POST['uraian_laporan']),
                'tujuan' => $_POST['tujuan'] ?? 'dinas kominfo',
                'status_laporan' => $_POST['status_laporan']
            ];

            
            if (isset($_FILES['upload_file']) && $_FILES['upload_file']['error'] === UPLOAD_ERR_OK) {
                $uploadResult = $this->handleFileUpload($_FILES['upload_file']);
                if ($uploadResult['success']) {
                    $data['upload_file'] = $uploadResult['filename'];
                } else {
                    $_SESSION['error'] = $uploadResult['message'];
                    include 'views/laporan-admin-opd/edit.php';
                    exit;
                }
            }

            $result = $this->laporanOPDAdminModel->updateLaporanOPD($id, $data);

            if ($result['success']) {
                $_SESSION['success'] = $result['message'];
                $this->redirect(route('laporanOPDAdmin', 'index'));
            } else {
                $_SESSION['error'] = $result['message'];
            }
        }

        include 'views/laporan-admin-opd/edit.php';
    }

    
    public function updateStatus($id = null) {
        $this->requireAdmin();

        if (!in_array($_SERVER['REQUEST_METHOD'], ['POST', 'PATCH'], true)) {
            $this->redirect(route('laporanOPDAdmin', 'index'));
        }

        $id = $id !== null ? (int) $id : (isset($_POST['id']) ? (int) $_POST['id'] : 0);
        $status = $_POST['status'] ?? '';

        if ($id === 0 || !in_array($status, ['baru', 'diproses', 'selesai'])) {
            $response = [
                'success' => false,
                'message' => "Data tidak valid."
            ];

            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                header('Content-Type: application/json');
                echo json_encode($response);
                exit;
            } else {
                $_SESSION['error'] = "Data tidak valid.";
                $this->redirect(route('laporanOPDAdmin', 'index'));
            }
        }

        $result = $this->laporanOPDAdminModel->updateStatus($id, $status);

        if ($result['success']) {
            $_SESSION['success'] = $result['message'];
        } else {
            $_SESSION['error'] = $result['message'];
        }

        $redirectUrl = $_POST['redirect_url'] ?? route('laporanOPDAdmin', 'index');

        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => $result['success'],
                'message' => $result['message'],
                'redirect' => $redirectUrl
            ]);
            exit;
        } else {
            $this->redirect($redirectUrl);
        }
    }

    
    public function delete($id = null) {
        $this->requireAdmin();

        $id = $id !== null ? (int) $id : (isset($_GET['id']) ? (int) $_GET['id'] : 0);

        if ($id === 0) {
            $response = [
                'success' => false,
                'message' => "ID laporan tidak valid."
            ];

            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                header('Content-Type: application/json');
                echo json_encode($response);
                exit;
            } else {
                $_SESSION['error'] = "ID laporan tidak valid.";
                $this->redirect(route('laporanOPDAdmin', 'index'));
            }
        }

        $result = $this->laporanOPDAdminModel->deleteLaporanOPD($id);

        if ($result['success']) {
            $_SESSION['success'] = $result['message'];
        } else {
            $_SESSION['error'] = $result['message'];
        }

        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => $result['success'],
                'message' => $result['message']
            ]);
            exit;
        } else {
            $this->redirect(route('laporanOPDAdmin', 'index'));
        }
    }

    
    public function export() {
        $this->requireAdmin();

        $format = isset($_GET['format']) ? $_GET['format'] : 'csv';

        if ($format === 'csv') {
            $this->exportCSV();
        } else {
            $_SESSION['error'] = "Format export tidak didukung.";
            $this->redirect(route('laporanOPDAdmin', 'index'));
        }
    }

    
    private function exportCSV() {
        $data = $this->laporanOPDAdminModel->exportLaporanOPD(); 
        $laporans = $data;

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="laporan_opd_' . date('Y-m-d') . '.csv"');

        $output = fopen('php://output', 'w');

        
        fputcsv($output, ['ID', 'Nama OPD', 'Nama Kegiatan', 'Uraian', 'Tujuan', 'Status', 'Tanggal', 'User', 'File']);

        
        foreach ($laporans as $laporan) {
            fputcsv($output, [
                $laporan['id_laporan_opd'],
                $laporan['nama_opd'],
                $laporan['nama_kegiatan'],
                strip_tags($laporan['uraian_laporan']),
                ucfirst($laporan['tujuan']),
                ucfirst($laporan['status_laporan']),
                date('d/m/Y H:i', strtotime($laporan['created_at'])),
                $laporan['username'] ?? '-',
                $laporan['upload_file'] ? basename($laporan['upload_file']) : '-'
            ]);
        }

        fclose($output);
        exit;
    }

    
    private function handleFileUpload($file) {
        $allowedTypes = ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx', 'xls', 'xlsx'];
        $maxSize = 5 * 1024 * 1024; 
        $uploadDir = __DIR__ . '/../uploads/laporan_opd/';

        
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileInfo = pathinfo($file['name']);
        $extension = strtolower($fileInfo['extension']);

        if (!in_array($extension, $allowedTypes)) {
            return ['success' => false, 'message' => 'Tipe file tidak diizinkan.'];
        }

        if ($file['size'] > $maxSize) {
            return ['success' => false, 'message' => 'Ukuran file terlalu besar (maksimal 5MB).'];
        }

        $filename = 'laporan_opd_' . time() . '_' . uniqid() . '.' . $extension;
        $uploadPath = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            return ['success' => true, 'filename' => 'uploads/laporan_opd/' . $filename];
        } else {
            return ['success' => false, 'message' => 'Gagal mengupload file.'];
        }
    }
}
