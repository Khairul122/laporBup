<?php

require_once 'models/AuthModel.php';
require_once 'models/LaporanOPDModel.php';
require_once 'models/OPDModel.php';

class LaporanOPDController extends BaseController {
    private $authModel;
    private $laporanOPDModel;
    private $opdModel;

    public function __construct() {
        $this->authModel = new AuthModel();
        $this->laporanOPDModel = new LaporanOPDModel();
        $this->opdModel = new OPDModel();
    }

    public function index() {
        $this->requireRole('opd');

        $currentUser = $this->getCurrentUser();
        $userId = $currentUser['id_user'];

        $status = $_GET['status'] ?? '';
        $search = $_GET['search'] ?? '';

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

        $stats = $this->laporanOPDModel->getLaporanStatsByUser($userId);

        require_once 'views/laporan-opd/index.php';
    }

    public function create() {
        $this->requireRole('opd');
        $currentUser = $this->getCurrentUser();

        $opd_result = $this->opdModel->getAllOPD(1, 1000, '');
        $opd_list = $opd_result['data'];

        require_once 'views/laporan-opd/form.php';
    }

    public function edit($id = null) {
        $this->requireRole('opd');

        $id = $id ?? $_GET['id'] ?? 0;
        $userId = $this->getCurrentUserId();

        $laporan = $this->laporanOPDModel->getLaporanById($id);

        if (!$laporan || $laporan['id_user'] != $userId) {
            $_SESSION['error'] = 'Laporan tidak ditemukan';
            $this->redirect(route('laporanOPD', 'index'));
        }

        $opd_result = $this->opdModel->getAllOPD(1, 1000, '');
        $opd_list = $opd_result['data'];

        require_once 'views/laporan-opd/form.php';
    }

    public function detail($id = null) {
        $this->requireRole('opd');

        $id = $id ?? $_GET['id'] ?? 0;
        $userId = $this->getCurrentUserId();

        error_log("DEBUG: Detail laporan - ID: $id, User ID: $userId");

        $laporan = $this->laporanOPDModel->getLaporanById($id);

        error_log("DEBUG: Laporan data: " . ($laporan ? json_encode($laporan) : 'NULL'));

        if (!$laporan) {
            error_log("DEBUG: Laporan tidak ditemukan untuk ID: $id");
            $_SESSION['error'] = 'Laporan tidak ditemukan';
            $this->redirect(route('laporanOPD', 'index'));
        }

        if ($laporan['id_user'] != $userId) {
            error_log("DEBUG: User tidak memiliki akses. Laporan user ID: {$laporan['id_user']}, Current user ID: $userId");
            $_SESSION['error'] = 'Anda tidak memiliki akses ke laporan ini';
            $this->redirect(route('laporanOPD', 'index'));
        }

        require_once 'views/laporan-opd/detail.php';
    }

    public function store() {
        $this->requireRole('opd');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(route('laporanOPD', 'index'));
        }

        $currentUser = $this->getCurrentUser();
        $userId = $currentUser['id_user'];

        $errors = $this->validateInput($_POST);

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_input'] = $_POST;
            $this->redirect(route('laporanOPD', 'create'));
        }

        $filePath = null;
        if (isset($_FILES['upload_file']) && $_FILES['upload_file']['error'] === UPLOAD_ERR_OK) {
            $filePath = $this->laporanOPDModel->handleFileUpload($_FILES['upload_file']);
            if (!$filePath) {
                $_SESSION['errors'] = ['File upload gagal. Pastikan file bertipe PDF, DOC, DOCX, XLS, XLSX, JPG, JPEG, PNG, atau GIF dan ukuran maksimal 5MB'];
                $_SESSION['old_input'] = $_POST;
                $this->redirect(route('laporanOPD', 'create'));
            }
        }

        $data = [
            'id_user' => $userId,
            'nama_opd' => trim($_POST['nama_opd']),
            'nama_kegiatan' => trim($_POST['nama_kegiatan']),
            'uraian_laporan' => trim($_POST['uraian_laporan']),
            'tujuan' => $_POST['tujuan'] ?? 'dinas kominfo',
            'upload_file' => $filePath,
            'status_laporan' => 'baru'
        ];

        if ($this->laporanOPDModel->createLaporan($data)) {
            $_SESSION['success'] = 'Laporan berhasil dikirim';
            $this->redirect(route('laporanOPD', 'index'));
        } else {
            $_SESSION['error'] = 'Gagal mengirim laporan';
            $_SESSION['old_input'] = $_POST;
            $this->redirect(route('laporanOPD', 'create'));
        }
    }

    public function update($id = null) {
        $this->requireRole('opd');

        if (!in_array($_SERVER['REQUEST_METHOD'], ['POST', 'PUT'], true)) {
            $this->redirect(route('laporanOPD', 'index'));
        }

        $id = $id ?? $_POST['id'] ?? 0;
        $userId = $this->getCurrentUserId();

        $laporan = $this->laporanOPDModel->getLaporanById($id);
        if (!$laporan || $laporan['id_user'] != $userId) {
            $_SESSION['error'] = 'Laporan tidak ditemukan';
            $this->redirect(route('laporanOPD', 'index'));
        }

        $errors = $this->validateInput($_POST, $id);

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_input'] = $_POST;
            $this->redirect(route('laporanOPD', 'edit', ['id' => $id]));
        }

        $filePath = $laporan['upload_file'];
        if (isset($_FILES['upload_file']) && $_FILES['upload_file']['error'] === UPLOAD_ERR_OK) {
            $newFilePath = $this->laporanOPDModel->handleFileUpload($_FILES['upload_file']);
            if ($newFilePath) {
                if ($filePath && file_exists($filePath)) {
                    $this->laporanOPDModel->deleteFile($filePath);
                }
                $filePath = $newFilePath;
            } else {
                $_SESSION['errors'] = ['File upload gagal. Pastikan file bertipe PDF, DOC, DOCX, XLS, XLSX, JPG, JPEG, PNG, atau GIF dan ukuran maksimal 5MB'];
                $_SESSION['old_input'] = $_POST;
                $this->redirect(route('laporanOPD', 'edit', ['id' => $id]));
            }
        }

        $data = [
            'id_user' => $userId,
            'nama_opd' => trim($_POST['nama_opd']),
            'nama_kegiatan' => trim($_POST['nama_kegiatan']),
            'uraian_laporan' => trim($_POST['uraian_laporan']),
            'tujuan' => $_POST['tujuan'] ?? 'dinas kominfo',
            'upload_file' => $filePath,
            'status_laporan' => $laporan['status_laporan']
        ];

        if ($this->laporanOPDModel->updateLaporan($id, $data)) {
            $_SESSION['success'] = 'Laporan berhasil diperbarui';
            $this->redirect(route('laporanOPD', 'detail', ['id' => $id]));
        } else {
            $_SESSION['error'] = 'Gagal memperbarui laporan';
            $_SESSION['old_input'] = $_POST;
            $this->redirect(route('laporanOPD', 'edit', ['id' => $id]));
        }
    }

    public function delete($id = null) {
        $this->requireRole('opd');

        if (!in_array($_SERVER['REQUEST_METHOD'], ['POST', 'DELETE'], true)) {
            $this->redirect(route('laporanOPD', 'index'));
        }

        $id = $id ?? $_POST['id'] ?? 0;
        $userId = $this->getCurrentUserId();

        $laporan = $this->laporanOPDModel->getLaporanById($id);
        if (!$laporan || $laporan['id_user'] != $userId) {
            $_SESSION['error'] = 'Laporan tidak ditemukan';
            $this->redirect(route('laporanOPD', 'index'));
        }

        if ($laporan['upload_file'] && file_exists($laporan['upload_file'])) {
            $this->laporanOPDModel->deleteFile($laporan['upload_file']);
        }

        if ($this->laporanOPDModel->deleteLaporan($id, $userId)) {
            $_SESSION['success'] = 'Laporan berhasil dihapus';
        } else {
            $_SESSION['error'] = 'Gagal menghapus laporan';
        }

        $this->redirect(route('laporanOPD', 'index'));
    }

    private function validateInput($data, $excludeId = null) {
        $errors = [];

        if (empty(trim($data['nama_opd']))) {
            $errors[] = 'Nama OPD harus dipilih';
        } else {
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

        if (empty(trim($data['nama_kegiatan']))) {
            $errors[] = 'Nama kegiatan harus diisi';
        } elseif (strlen(trim($data['nama_kegiatan'])) < 3) {
            $errors[] = 'Nama kegiatan minimal 3 karakter';
        }

        if (empty(trim($data['uraian_laporan']))) {
            $errors[] = 'Uraian laporan harus diisi';
        } elseif (strlen(trim($data['uraian_laporan'])) < 10) {
            $errors[] = 'Uraian laporan minimal 10 karakter';
        }

        $validTujuan = ['dinas kominfo'];
        if (!empty($data['tujuan']) && !in_array(strtolower($data['tujuan']), $validTujuan)) {
            $errors[] = 'Tujuan tidak valid';
        }

        return $errors;
    }

    public function download($id = null) {
        $this->requireRole('opd');

        $id = $id ?? $_GET['id'] ?? 0;
        $userId = $this->getCurrentUserId();

        $laporan = $this->laporanOPDModel->getLaporanById($id);

        if (!$laporan || $laporan['id_user'] != $userId || empty($laporan['upload_file'])) {
            $_SESSION['error'] = 'File tidak ditemukan';
            $this->redirect(route('laporanOPD', 'index'));
        }

        $filePath = $laporan['upload_file'];
        $fileName = basename($filePath);

        if (!file_exists($filePath)) {
            $_SESSION['error'] = 'File tidak ditemukan di server';
            $this->redirect(route('laporanOPD', 'index'));
        }

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Content-Length: ' . filesize($filePath));
        header('Cache-Control: no-cache, must-revalidate');
        header('Pragma: no-cache');

        readfile($filePath);
        exit;
    }

    public function getStats() {
        $this->requireRole('opd');

        $userId = $this->getCurrentUserId();
        $stats = $this->laporanOPDModel->getLaporanStatsByUser($userId);

        $this->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}
