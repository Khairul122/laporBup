<?php

require_once 'models/LaporanCamatAdminModel.php';

class LaporanCamatAdminController {
    private $laporanCamatAdminModel;

    public function __construct() {
        $this->laporanCamatAdminModel = new LaporanCamatAdminModel();
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
                header('Location: index.php?controller=dashboard&action=admin');
                exit;
            }
        }
    }

    /**
     * Menampilkan halaman daftar laporan Camat
     */
    public function index() {
        $this->requireAdmin();

        $user = $this->getCurrentUser();
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $search = $_GET['search'] ?? '';
        $status = $_GET['status'] ?? '';

        // Get data laporan Camat
        $result = $this->laporanCamatAdminModel->getAllLaporanCamat($page, $limit, $search, $status);
        $laporans = $result['data'];
        $totalLaporan = $result['total'];
        $totalPages = $result['total_pages'];

        // Get statistics
        $statistics = $this->laporanCamatAdminModel->getLaporanCamatStatistics();

        // Format statistics for view
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
        include 'views/laporan-admin-camat/index.php';
    }

    /**
     * Menampilkan detail laporan Camat
     */
    public function detail() {
        $this->requireAdmin();

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($id === 0) {
            $_SESSION['error'] = "ID laporan tidak valid.";
            header('Location: index.php?controller=laporanCamatAdmin&action=index');
            exit;
        }

        $laporan = $this->laporanCamatAdminModel->getLaporanCamatById($id);

        if (!$laporan) {
            $_SESSION['error'] = "Laporan tidak ditemukan.";
            header('Location: index.php?controller=laporanCamatAdmin&action=index');
            exit;
        }

        include 'views/laporan-admin-camat/detail.php';
    }

    /**
     * Menampilkan halaman edit laporan Camat
     */
    public function edit() {
        $this->requireAdmin();

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($id === 0) {
            $_SESSION['error'] = "ID laporan tidak valid.";
            header('Location: index.php?controller=laporanCamatAdmin&action=index');
            exit;
        }

        $laporan = $this->laporanCamatAdminModel->getLaporanCamatById($id);

        if (!$laporan) {
            $_SESSION['error'] = "Laporan tidak ditemukan.";
            header('Location: index.php?controller=laporanCamatAdmin&action=index');
            exit;
        }

        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nama_kecamatan' => trim($_POST['nama_kecamatan']),
                'nama_kegiatan' => trim($_POST['nama_kegiatan']),
                'uraian_laporan' => trim($_POST['uraian_laporan']),
                'status_laporan' => $_POST['status_laporan']
            ];

            // Handle file upload
            if (isset($_FILES['upload_file']) && $_FILES['upload_file']['error'] === UPLOAD_ERR_OK) {
                $uploadResult = $this->handleFileUpload($_FILES['upload_file']);
                if ($uploadResult['success']) {
                    $data['upload_file'] = $uploadResult['filename'];
                } else {
                    $_SESSION['error'] = $uploadResult['message'];
                    include 'views/laporan-admin-camat/edit.php';
                    exit;
                }
            }

            $result = $this->laporanCamatAdminModel->updateLaporanCamat($id, $data);

            if ($result['success']) {
                $_SESSION['success'] = $result['message'];
                header('Location: index.php?controller=laporanCamatAdmin&action=index');
                exit;
            } else {
                $_SESSION['error'] = $result['message'];
            }
        }

        include 'views/laporan-admin-camat/edit.php';
    }

    /**
     * Update status laporan
     */
    public function updateStatus() {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=laporanCamatAdmin&action=index');
            exit;
        }

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
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
                header('Location: index.php?controller=laporanCamatAdmin&action=index');
                exit;
            }
        }

        $result = $this->laporanCamatAdminModel->updateStatus($id, $status);

        if ($result['success']) {
            $_SESSION['success'] = $result['message'];
        } else {
            $_SESSION['error'] = $result['message'];
        }

        $redirectUrl = $_POST['redirect_url'] ?? 'index.php?controller=laporanCamatAdmin&action=index';

        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => $result['success'],
                'message' => $result['message'],
                'redirect' => $redirectUrl
            ]);
            exit;
        } else {
            header('Location: ' . $redirectUrl);
            exit;
        }
    }

    /**
     * Hapus laporan Camat
     */
    public function delete() {
        $this->requireAdmin();

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

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
                header('Location: index.php?controller=laporanCamatAdmin&action=index');
                exit;
            }
        }

        $result = $this->laporanCamatAdminModel->deleteLaporanCamat($id);

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
            header('Location: index.php?controller=laporanCamatAdmin&action=index');
            exit;
        }
    }

    /**
     * Export data laporan Camat
     */
    public function export() {
        $this->requireAdmin();

        $format = isset($_GET['format']) ? $_GET['format'] : 'csv';

        if ($format === 'csv') {
            $this->exportCSV();
        } else {
            $_SESSION['error'] = "Format export tidak didukung.";
            header('Location: index.php?controller=laporanCamatAdmin&action=index');
        }
    }

    /**
     * Export ke CSV
     */
    private function exportCSV() {
        $data = $this->laporanCamatAdminModel->exportLaporanCamat(); // Get all data without pagination
        $laporans = $data;

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="laporan_camat_' . date('Y-m-d') . '.csv"');

        $output = fopen('php://output', 'w');

        // CSV Header
        fputcsv($output, ['ID', 'Nama Kecamatan', 'Nama Kegiatan', 'Uraian', 'Tujuan', 'Status', 'Tanggal', 'User', 'File']);

        // CSV Data
        foreach ($laporans as $laporan) {
            fputcsv($output, [
                $laporan['id_laporan_camat'],
                $laporan['nama_kecamatan'],
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

    /**
     * Handle file upload
     */
    private function handleFileUpload($file) {
        $allowedTypes = ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx', 'xls', 'xlsx'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        $uploadDir = __DIR__ . '/../uploads/laporan_camat/';

        // Create directory if not exists
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

        $filename = 'laporan_camat_' . time() . '_' . uniqid() . '.' . $extension;
        $uploadPath = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            return ['success' => true, 'filename' => 'uploads/laporan_camat/' . $filename];
        } else {
            return ['success' => false, 'message' => 'Gagal mengupload file.'];
        }
    }
}