<?php

require_once 'models/AuthModel.php';
require_once 'models/LaporanOPDModel.php';
require_once 'models/OPDModel.php';

class LaporanOPDController {
    private $authModel;
    private $laporanOPDModel;
    private $opdModel;

    public function __construct() {
        $this->authModel = new AuthModel();
        $this->laporanOPDModel = new LaporanOPDModel();
        $this->opdModel = new OPDModel();
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
     * Mendapatkan ID user yang sedang login
     */
    private function getCurrentUserId() {
        return $_SESSION['user_id'] ?? null;
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
            header('Location: index.php?controller=auth&action=index');
            exit;
        }
    }

    /**
     * Require role OPD untuk mengakses halaman
     */
    private function requireOPDRole() {
        $this->requireLogin();

        if ($this->getUserRole() !== 'opd') {
            header('Location: index.php?controller=dashboard&action=' . $this->getUserRole());
            exit;
        }
    }

    /**
     * Halaman daftar laporan OPD
     */
    public function index() {
        $this->requireOPDRole();

        $currentUser = $this->getCurrentUser();
        $userId = $currentUser['id_user'];

        // Get parameters
        $status = $_GET['status'] ?? '';
        $search = $_GET['search'] ?? '';

        // Get laporan
        if (!empty($search)) {
            $laporan = $this->laporanOPDModel->searchLaporan($search, $userId);
            $title = "Hasil Pencarian: " . htmlspecialchars($search);
        } elseif (!empty($status)) {
            $laporan = $this->laporanOPDModel->getLaporanByStatus($status, $userId);
            $title = "Laporan Status: " . ucfirst($status);
        } else {
            $laporan = $this->laporanOPDModel->getAllLaporanByUser($userId);
            $title = "Daftar Laporan OPD";
        }

        // Get statistics
        $stats = $this->laporanOPDModel->getLaporanStatsByUser($userId);

        require_once 'views/laporan-opd/index.php';
    }

    /**
     * Halaman form tambah laporan
     */
    public function create() {
        $this->requireOPDRole();
        $currentUser = $this->getCurrentUser();

        // Load OPD list untuk dropdown tujuan
        $opd_result = $this->opdModel->getAllOPD(1, 1000, '');
        $opd_list = $opd_result['data'];

        require_once 'views/laporan-opd/form.php';
    }

    /**
     * Halaman form edit laporan
     */
    public function edit() {
        $this->requireOPDRole();

        $id = $_GET['id'] ?? 0;
        $userId = $this->getCurrentUserId();

        $laporan = $this->laporanOPDModel->getLaporanById($id);

        if (!$laporan || $laporan['id_user'] != $userId) {
            $_SESSION['error'] = 'Laporan tidak ditemukan';
            header('Location: index.php?controller=laporanOPD&action=index');
            exit;
        }

        // Load OPD list untuk dropdown tujuan
        $opd_result = $this->opdModel->getAllOPD(1, 1000, '');
        $opd_list = $opd_result['data'];

        require_once 'views/laporan-opd/form.php';
    }

    /**
     * Halaman detail laporan
     */
    public function detail() {
        $this->requireOPDRole();

        $id = $_GET['id'] ?? 0;
        $userId = $this->getCurrentUserId();

        // Debug: Log untuk troubleshooting
        error_log("DEBUG: Detail laporan - ID: $id, User ID: $userId");

        $laporan = $this->laporanOPDModel->getLaporanById($id);

        error_log("DEBUG: Laporan data: " . ($laporan ? json_encode($laporan) : 'NULL'));

        if (!$laporan) {
            error_log("DEBUG: Laporan tidak ditemukan untuk ID: $id");
            $_SESSION['error'] = 'Laporan tidak ditemukan';
            header('Location: index.php?controller=laporanOPD&action=index');
            exit;
        }

        if ($laporan['id_user'] != $userId) {
            error_log("DEBUG: User tidak memiliki akses. Laporan user ID: {$laporan['id_user']}, Current user ID: $userId");
            $_SESSION['error'] = 'Anda tidak memiliki akses ke laporan ini';
            header('Location: index.php?controller=laporanOPD&action=index');
            exit;
        }

        require_once 'views/laporan-opd/detail.php';
    }

    /**
     * Proses simpan laporan
     */
    public function store() {
        $this->requireOPDRole();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=laporanOPD&action=index');
            exit;
        }

        $currentUser = $this->getCurrentUser();
        $userId = $currentUser['id_user'];

        // Validasi input
        $errors = $this->validateInput($_POST);

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_input'] = $_POST;
            header('Location: index.php?controller=laporanOPD&action=create');
            exit;
        }

        // Handle file upload
        $filePath = null;
        if (isset($_FILES['upload_file']) && $_FILES['upload_file']['error'] === UPLOAD_ERR_OK) {
            $filePath = $this->laporanOPDModel->handleFileUpload($_FILES['upload_file']);
            if (!$filePath) {
                $_SESSION['errors'] = ['File upload gagal. Pastikan file bertipe PDF, DOC, DOCX, XLS, XLSX, JPG, JPEG, PNG, atau GIF dan ukuran maksimal 5MB'];
                $_SESSION['old_input'] = $_POST;
                header('Location: index.php?controller=laporanOPD&action=create');
                exit;
            }
        }

        // Prepare data
        $data = [
            'id_user' => $userId,
            'nama_opd' => trim($_POST['nama_opd']),
            'nama_kegiatan' => trim($_POST['nama_kegiatan']),
            'uraian_laporan' => trim($_POST['uraian_laporan']),
            'tujuan' => $_POST['tujuan'] ?? 'dinas kominfo',
            'upload_file' => $filePath,
            'status_laporan' => 'baru'
        ];

        // Save laporan
        if ($this->laporanOPDModel->createLaporan($data)) {
            $_SESSION['success'] = 'Laporan berhasil dikirim';
            header('Location: index.php?controller=laporanOPD&action=index');
        } else {
            $_SESSION['error'] = 'Gagal mengirim laporan';
            $_SESSION['old_input'] = $_POST;
            header('Location: index.php?controller=laporanOPD&action=create');
        }
        exit;
    }

    /**
     * Proses update laporan
     */
    public function update() {
        $this->requireOPDRole();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=laporanOPD&action=index');
            exit;
        }

        $id = $_POST['id'] ?? 0;
        $userId = $this->getCurrentUserId();

        // Check if laporan exists and belongs to user
        $laporan = $this->laporanOPDModel->getLaporanById($id);
        if (!$laporan || $laporan['id_user'] != $userId) {
            $_SESSION['error'] = 'Laporan tidak ditemukan';
            header('Location: index.php?controller=laporanOPD&action=index');
            exit;
        }

        // Validasi input
        $errors = $this->validateInput($_POST, $id);

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_input'] = $_POST;
            header('Location: index.php?controller=laporanOPD&action=edit&id=' . $id);
            exit;
        }

        // Handle file upload
        $filePath = $laporan['upload_file']; // Keep existing file
        if (isset($_FILES['upload_file']) && $_FILES['upload_file']['error'] === UPLOAD_ERR_OK) {
            $newFilePath = $this->laporanOPDModel->handleFileUpload($_FILES['upload_file']);
            if ($newFilePath) {
                // Delete old file if exists
                if ($filePath && file_exists($filePath)) {
                    $this->laporanOPDModel->deleteFile($filePath);
                }
                $filePath = $newFilePath;
            } else {
                $_SESSION['errors'] = ['File upload gagal. Pastikan file bertipe PDF, DOC, DOCX, XLS, XLSX, JPG, JPEG, PNG, atau GIF dan ukuran maksimal 5MB'];
                $_SESSION['old_input'] = $_POST;
                header('Location: index.php?controller=laporanOPD&action=edit&id=' . $id);
                exit;
            }
        }

        // Prepare data
        $data = [
            'id_user' => $userId,
            'nama_opd' => trim($_POST['nama_opd']),
            'nama_kegiatan' => trim($_POST['nama_kegiatan']),
            'uraian_laporan' => trim($_POST['uraian_laporan']),
            'tujuan' => $_POST['tujuan'] ?? 'dinas kominfo',
            'upload_file' => $filePath,
            'status_laporan' => $laporan['status_laporan'] // Keep existing status
        ];

        // Update laporan
        if ($this->laporanOPDModel->updateLaporan($id, $data)) {
            $_SESSION['success'] = 'Laporan berhasil diperbarui';
            header('Location: index.php?controller=laporanOPD&action=detail&id=' . $id);
        } else {
            $_SESSION['error'] = 'Gagal memperbarui laporan';
            $_SESSION['old_input'] = $_POST;
            header('Location: index.php?controller=laporanOPD&action=edit&id=' . $id);
        }
        exit;
    }

    /**
     * Hapus laporan
     */
    public function delete() {
        $this->requireOPDRole();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=laporanOPD&action=index');
            exit;
        }

        $id = $_POST['id'] ?? 0;
        $userId = $this->getCurrentUserId();

        // Check if laporan exists and belongs to user
        $laporan = $this->laporanOPDModel->getLaporanById($id);
        if (!$laporan || $laporan['id_user'] != $userId) {
            $_SESSION['error'] = 'Laporan tidak ditemukan';
            header('Location: index.php?controller=laporanOPD&action=index');
            exit;
        }

        // Delete file if exists
        if ($laporan['upload_file'] && file_exists($laporan['upload_file'])) {
            $this->laporanOPDModel->deleteFile($laporan['upload_file']);
        }

        // Delete laporan
        if ($this->laporanOPDModel->deleteLaporan($id, $userId)) {
            $_SESSION['success'] = 'Laporan berhasil dihapus';
        } else {
            $_SESSION['error'] = 'Gagal menghapus laporan';
        }

        header('Location: index.php?controller=laporanOPD&action=index');
        exit;
    }

    /**
     * Validasi input form
     */
    private function validateInput($data, $excludeId = null) {
        $errors = [];

        // Validasi nama OPD - check if selected from dropdown
        if (empty(trim($data['nama_opd']))) {
            $errors[] = 'Nama OPD harus dipilih';
        } else {
            // Verify that the selected OPD exists in the database
            $opd_result = $this->opdModel->getAllOPD(1, 1000, '');
            $valid_opds = $opd_result['data'];
            $selected_opd = trim($data['nama_opd']);
            $found = false;
            
            foreach ($valid_opds as $opd) {
                if ($opd['nama_opd'] === $selected_opd) {
                    $found = true;
                    break;
                }
            }
            
            if (!$found) {
                $errors[] = 'Nama OPD yang dipilih tidak valid';
            }
        }

        // Validasi nama kegiatan
        if (empty(trim($data['nama_kegiatan']))) {
            $errors[] = 'Nama kegiatan harus diisi';
        } elseif (strlen(trim($data['nama_kegiatan'])) < 3) {
            $errors[] = 'Nama kegiatan minimal 3 karakter';
        }

        // Validasi uraian laporan
        if (empty(trim($data['uraian_laporan']))) {
            $errors[] = 'Uraian laporan harus diisi';
        } elseif (strlen(trim($data['uraian_laporan'])) < 10) {
            $errors[] = 'Uraian laporan minimal 10 karakter';
        }

        // Validasi tujuan
        $validTujuan = ['dinas kominfo'];
        if (!empty($data['tujuan']) && !in_array(strtolower($data['tujuan']), $validTujuan)) {
            $errors[] = 'Tujuan tidak valid';
        }

        return $errors;
    }

    /**
     * Download file laporan
     */
    public function download() {
        $this->requireOPDRole();

        $id = $_GET['id'] ?? 0;
        $userId = $this->getCurrentUserId();

        $laporan = $this->laporanOPDModel->getLaporanById($id);

        if (!$laporan || $laporan['id_user'] != $userId || empty($laporan['upload_file'])) {
            $_SESSION['error'] = 'File tidak ditemukan';
            header('Location: index.php?controller=laporanOPD&action=index');
            exit;
        }

        $filePath = $laporan['upload_file'];
        $fileName = basename($filePath);

        if (!file_exists($filePath)) {
            $_SESSION['error'] = 'File tidak ditemukan di server';
            header('Location: index.php?controller=laporanOPD&action=index');
            exit;
        }

        // Set headers untuk download
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Content-Length: ' . filesize($filePath));
        header('Cache-Control: no-cache, must-revalidate');
        header('Pragma: no-cache');

        readfile($filePath);
        exit;
    }

    /**
     * AJAX: Get statistics untuk dashboard
     */
    public function getStats() {
        $this->requireOPDRole();

        $userId = $this->getCurrentUserId();
        $stats = $this->laporanOPDModel->getLaporanStatsByUser($userId);

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data' => $stats
        ]);
        exit;
    }
}

// Proses request
if (isset($_GET['action'])) {
    $laporanOPDController = new LaporanOPDController();

    switch ($_GET['action']) {
        case 'index':
        case 'create':
        case 'edit':
        case 'detail':
        case 'store':
        case 'update':
        case 'delete':
        case 'download':
        case 'getStats':
            $action = $_GET['action'];
            $laporanOPDController->$action();
            break;
        default:
            $laporanOPDController->index();
            break;
    }
} else {
    $laporanOPDController = new LaporanOPDController();
    $laporanOPDController->index();
}