<?php
require_once 'models/LaporanCamatModel.php';
require_once 'models/AuthModel.php';
require_once 'models/WilayahModel.php';

class LaporanCamatController {
    private $laporanCamatModel;
    private $authModel;
    private $wilayahModel;

    public function __construct() {
        $this->laporanCamatModel = new LaporanCamatModel();
        $this->authModel = new AuthModel();
        $this->wilayahModel = new WilayahModel();
    }

    /**
     * Check if user is logged in
     */
    private function isLoggedIn() {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }

    /**
     * Require login for accessing pages
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
     * Check if user has permission to access this resource
     */
    private function hasPermission($id_user) {
        $current_user_id = $_SESSION['user_id'] ?? 0;
        $current_role = $_SESSION['role'] ?? '';
        
        // Admin can access all
        if ($current_role === 'admin') {
            return true;
        }
        
        // User can only access their own reports
        return $current_user_id == $id_user;
    }

    /**
     * Index: Display list of laporan camat
     */
    public function index() {
        $this->requireLogin();
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $status = isset($_GET['status']) ? $_GET['status'] : '';
        
        $current_user_id = $_SESSION['user_id'];
        $current_role = $_SESSION['role'];
        
        // Get data based on user role
        if ($current_role === 'admin') {
            // Admin sees all reports
            $laporans = $this->laporanCamatModel->getWithPagination($limit, $offset, $search, $status);
            $total = $this->laporanCamatModel->getTotalWithFilters($search, $status);
        } else {
            // Regular user sees only their reports
            $laporans = $this->laporanCamatModel->getAllByUserId($current_user_id, $limit, $offset, $search);
            $total = $this->laporanCamatModel->getTotalByUserId($current_user_id, $search);
        }
        
        $total_pages = ceil($total / $limit);
        
        require_once 'views/laporan-camat/index.php';
    }

    /**
     * Show form to create new laporan
     */
    public function create() {
        $this->requireLogin();
        
        // Only camat can create laporan camat
        if ($_SESSION['role'] !== 'camat') {
            $_SESSION['error'] = 'Hanya camat yang dapat membuat laporan camat';
            header('Location: index.php?controller=laporanCamat&action=index');
            exit;
        }
        
        // Load kecamatan and desa data for the form
        $kecamatan_list = $this->wilayahModel->getKecamatanOptions();
        $desa_list = [];
        $selected_kecamatan_id = null;
        $selected_desa_id = null;
        
        require_once 'views/laporan-camat/form.php';
    }

    /**
     * Store new laporan
     */
    public function store() {
        $this->requireLogin();
        
        if ($_SESSION['role'] !== 'camat') {
            $_SESSION['error'] = 'Hanya camat yang dapat membuat laporan camat';
            header('Location: index.php?controller=laporanCamat&action=index');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=laporanCamat&action=create');
            exit;
        }
        
        // Validate input
        $nama_pelapor = trim($_POST['nama_pelapor'] ?? '');
        $id_desa = (int)($_POST['id_desa'] ?? 0);
        $id_kecamatan = (int)($_POST['id_kecamatan'] ?? 0);
        $waktu_kejadian = $_POST['waktu_kejadian'] ?? '';
        $tujuan = $_POST['tujuan'] ?? '';
        $uraian_laporan = trim($_POST['uraian_laporan'] ?? '');
        
        // Validation
        if (empty($nama_pelapor) || empty($id_desa) || empty($id_kecamatan) || 
            empty($waktu_kejadian) || empty($tujuan) || empty($uraian_laporan)) {
            $_SESSION['error'] = 'Semua field wajib diisi';
            header('Location: index.php?controller=laporanCamat&action=create');
            exit;
        }
        
        // Get nama_desa and nama_kecamatan from ID
        $desa = $this->wilayahModel->getDesaById($id_desa);
        $kecamatan = $this->wilayahModel->getKecamatanById($id_kecamatan);
        
        if (!$desa || !$kecamatan) {
            $_SESSION['error'] = 'Data desa atau kecamatan tidak valid';
            header('Location: index.php?controller=laporanCamat&action=create');
            exit;
        }
        
        $nama_desa = $desa['nama_desa'];
        $nama_kecamatan = $kecamatan['nama_kecamatan'];
        
        // Validate waktu kejadian format
        if (!strtotime($waktu_kejadian)) {
            $_SESSION['error'] = 'Format waktu kejadian tidak valid';
            header('Location: index.php?controller=laporanCamat&action=create');
            exit;
        }
        
        // Validate tujuan
        $valid_tujuuan = ['bupati', 'wakil bupati', 'sekda', 'opd'];
        if (!in_array($tujuan, $valid_tujuuan)) {
            $_SESSION['error'] = 'Tujuan laporan tidak valid';
            header('Location: index.php?controller=laporanCamat&action=create');
            exit;
        }
        
        // Handle file upload if exists
        $upload_file = null;
        if (isset($_FILES['upload_file']) && $_FILES['upload_file']['error'] === 0) {
            $upload_dir = 'uploads/laporan_camat/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $file_name = $_FILES['upload_file']['name'];
            $file_tmp = $_FILES['upload_file']['tmp_name'];
            $file_size = $_FILES['upload_file']['size'];
            $file_type = $_FILES['upload_file']['type'];
            
            // Validate file type
            $allowed_types = $this->getAllowedFileTypes();
            if (!in_array($file_type, $allowed_types)) {
                $_SESSION['error'] = 'Format file tidak diizinkan';
                header('Location: index.php?controller=laporanCamat&action=create');
                exit;
            }
            
            // Validate file size (max 100MB for videos, 10MB for other files)
            $max_file_size = 10000000; // 10MB default
            if (strpos($file_type, 'video/') === 0) {
                $max_file_size = 100000000; // 100MB for videos
            }
            
            if ($file_size > $max_file_size) {
                $max_size_mb = $max_file_size / 1000000;
                $_SESSION['error'] = "Ukuran file terlalu besar (maksimal {$max_size_mb}MB)";
                header('Location: index.php?controller=laporanCamat&action=create');
                exit;
            }
            
            // Generate unique filename with correct extension
            $extension = $this->getExtensionFromMimeType($file_type);
            $upload_file = $upload_dir . uniqid() . $extension;
            
            if (!move_uploaded_file($file_tmp, $upload_file)) {
                $_SESSION['error'] = 'Gagal mengunggah file';
                header('Location: index.php?controller=laporanCamat&action=create');
                exit;
            }
        }
        
        // Prepare data for insertion
        $data = [
            'id_user' => $_SESSION['user_id'],
            'nama_pelapor' => $nama_pelapor,
            'nama_desa' => $nama_desa,
            'nama_kecamatan' => $nama_kecamatan,
            'waktu_kejadian' => $waktu_kejadian,
            'tujuan' => $tujuan,
            'uraian_laporan' => $uraian_laporan,
            'upload_file' => $upload_file
        ];
        
        $result = $this->laporanCamatModel->create($data);
        
        if ($result) {
            $_SESSION['success'] = 'Laporan berhasil dibuat';
            header('Location: index.php?controller=laporanCamat&action=index');
        } else {
            $_SESSION['error'] = 'Gagal membuat laporan';
            header('Location: index.php?controller=laporanCamat&action=create');
        }
        exit;
    }

    /**
     * Show form to edit laporan
     */
    public function edit($id = null) {
        $this->requireLogin();
        
        // Get ID from parameter or from GET request
        $id = $id ?? $_GET['id'] ?? 0;
        
        if (!$id) {
            $_SESSION['error'] = 'ID laporan tidak ditemukan';
            header('Location: index.php?controller=laporanCamat&action=index');
            exit;
        }
        
        $laporan = $this->laporanCamatModel->getById($id);
        
        if (!$laporan) {
            $_SESSION['error'] = 'Laporan tidak ditemukan';
            header('Location: index.php?controller=laporanCamat&action=index');
            exit;
        }
        
        // Check permission
        if (!$this->hasPermission($laporan['id_user'])) {
            $_SESSION['error'] = 'Anda tidak memiliki akses ke laporan ini';
            header('Location: index.php?controller=laporanCamat&action=index');
            exit;
        }
        
        // Only allow edit if status is 'baru'
        if ($laporan['status_laporan'] !== 'baru') {
            $_SESSION['error'] = 'Laporan yang sudah diproses tidak dapat diedit';
            header('Location: index.php?controller=laporanCamat&action=detail&id=' . $id);
            exit;
        }
        
        // Load kecamatan and desa data for the form
        $kecamatan_list = $this->wilayahModel->getKecamatanOptions();

        // For edit mode, we need to find the kecamatan ID first, then get all desa for that kecamatan
        $selected_kecamatan_id = null;
        $selected_desa_id = null;

        // Find kecamatan ID based on laporan data
        foreach($kecamatan_list as $kec) {
            if($kec['nama_kecamatan'] === $laporan['nama_kecamatan']) {
                $selected_kecamatan_id = $kec['id_kecamatan'];
                break;
            }
        }

        // Get ALL desa for the selected kecamatan
        $desa_list = [];
        if($selected_kecamatan_id) {
            $desa_list = $this->wilayahModel->getDesaByKecamatan($selected_kecamatan_id);

            // Find the specific desa ID that matches the laporan
            foreach($desa_list as $d) {
                if($d['nama_desa'] === $laporan['nama_desa']) {
                    $selected_desa_id = $d['id_desa'];
                    break;
                }
            }
        }
          
        require_once 'views/laporan-camat/form.php';
    }

    /**
     * Update laporan
     */
    public function update($id = null) {
        $this->requireLogin();
        
        // Get ID from parameter or from GET request
        $id = $id ?? $_GET['id'] ?? 0;
        
        if (!$id) {
            $_SESSION['error'] = 'ID laporan tidak ditemukan';
            header('Location: index.php?controller=laporanCamat&action=index');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=laporanCamat&action=edit&id=' . $id);
            exit;
        }
        
        $laporan = $this->laporanCamatModel->getById($id);
        
        if (!$laporan) {
            $_SESSION['error'] = 'Laporan tidak ditemukan';
            header('Location: index.php?controller=laporanCamat&action=index');
            exit;
        }
        
        // Check permission
        if (!$this->hasPermission($laporan['id_user'])) {
            $_SESSION['error'] = 'Anda tidak memiliki akses ke laporan ini';
            header('Location: index.php?controller=laporanCamat&action=index');
            exit;
        }
        
        // Only allow edit if status is 'baru'
        if ($laporan['status_laporan'] !== 'baru') {
            $_SESSION['error'] = 'Laporan yang sudah diproses tidak dapat diedit';
            header('Location: index.php?controller=laporanCamat&action=detail&id=' . $id);
            exit;
        }
        
        // Validate input
        $nama_pelapor = trim($_POST['nama_pelapor'] ?? '');
        $id_desa = (int)($_POST['id_desa'] ?? 0);
        $id_kecamatan = (int)($_POST['id_kecamatan'] ?? 0);
        $waktu_kejadian = $_POST['waktu_kejadian'] ?? '';
        $tujuan = $_POST['tujuan'] ?? '';
        $uraian_laporan = trim($_POST['uraian_laporan'] ?? '');
        
        // Validation
        if (empty($nama_pelapor) || empty($id_desa) || empty($id_kecamatan) || 
            empty($waktu_kejadian) || empty($tujuan) || empty($uraian_laporan)) {
            $_SESSION['error'] = 'Semua field wajib diisi';
            header('Location: index.php?controller=laporanCamat&action=edit&id=' . $id);
            exit;
        }
        
        // Get nama_desa and nama_kecamatan from ID
        $desa = $this->wilayahModel->getDesaById($id_desa);
        $kecamatan = $this->wilayahModel->getKecamatanById($id_kecamatan);
        
        if (!$desa || !$kecamatan) {
            $_SESSION['error'] = 'Data desa atau kecamatan tidak valid';
            header('Location: index.php?controller=laporanCamat&action=edit&id=' . $id);
            exit;
        }
        
        $nama_desa = $desa['nama_desa'];
        $nama_kecamatan = $kecamatan['nama_kecamatan'];
        
        // Validate waktu kejadian format
        if (!strtotime($waktu_kejadian)) {
            $_SESSION['error'] = 'Format waktu kejadian tidak valid';
            header('Location: index.php?controller=laporanCamat&action=edit&id=' . $id);
            exit;
        }
        
        // Validate tujuan
        $valid_tujuuan = ['bupati', 'wakil bupati', 'sekda', 'opd'];
        if (!in_array($tujuan, $valid_tujuuan)) {
            $_SESSION['error'] = 'Tujuan laporan tidak valid';
            header('Location: index.php?controller=laporanCamat&action=edit&id=' . $id);
            exit;
        }
        
        // Handle file upload if exists
        $upload_file = $laporan['upload_file']; // Keep existing file
        if (isset($_FILES['upload_file']) && $_FILES['upload_file']['error'] === 0) {
            $upload_dir = 'uploads/laporan_camat/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $file_name = $_FILES['upload_file']['name'];
            $file_tmp = $_FILES['upload_file']['tmp_name'];
            $file_size = $_FILES['upload_file']['size'];
            $file_type = $_FILES['upload_file']['type'];
            
            // Validate file type
            $allowed_types = $this->getAllowedFileTypes();
            if (!in_array($file_type, $allowed_types)) {
                $_SESSION['error'] = 'Format file tidak diizinkan';
                header('Location: index.php?controller=laporanCamat&action=edit&id=' . $id);
                exit;
            }
            
            // Validate file size (max 100MB for videos, 10MB for other files)
            $max_file_size = 10000000; // 10MB default
            if (strpos($file_type, 'video/') === 0) {
                $max_file_size = 100000000; // 100MB for videos
            }
            
            if ($file_size > $max_file_size) {
                $max_size_mb = $max_file_size / 1000000;
                $_SESSION['error'] = "Ukuran file terlalu besar (maksimal {$max_size_mb}MB)";
                header('Location: index.php?controller=laporanCamat&action=edit&id=' . $id);
                exit;
            }
            
            // Delete old file if exists
            if ($laporan['upload_file'] && file_exists($laporan['upload_file'])) {
                unlink($laporan['upload_file']);
            }
            
            // Generate unique filename with correct extension
            $extension = $this->getExtensionFromMimeType($file_type);
            $upload_file = $upload_dir . uniqid() . $extension;
            
            if (!move_uploaded_file($file_tmp, $upload_file)) {
                $_SESSION['error'] = 'Gagal mengunggah file';
                header('Location: index.php?controller=laporanCamat&action=edit&id=' . $id);
                exit;
            }
        }
        
        // Prepare data for update
        $data = [
            'nama_pelapor' => $nama_pelapor,
            'nama_desa' => $nama_desa,
            'nama_kecamatan' => $nama_kecamatan,
            'waktu_kejadian' => $waktu_kejadian,
            'tujuan' => $tujuan,
            'uraian_laporan' => $uraian_laporan,
            'upload_file' => $upload_file
        ];
        
        $result = $this->laporanCamatModel->update($id, $data);
        
        if ($result) {
            $_SESSION['success'] = 'Laporan berhasil diperbarui';
            header('Location: index.php?controller=laporanCamat&action=detail&id=' . $id);
        } else {
            $_SESSION['error'] = 'Gagal memperbarui laporan';
            header('Location: index.php?controller=laporanCamat&action=edit&id=' . $id);
        }
        exit;
    }

    /**
     * Delete laporan
     */
    public function delete($id = null) {
        $this->requireLogin();
        
        // Get ID from parameter or from GET request
        $id = $id ?? $_GET['id'] ?? 0;
        
        if (!$id) {
            $response = ['success' => false, 'message' => 'ID laporan tidak ditemukan'];
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }
        
        $laporan = $this->laporanCamatModel->getById($id);
        
        if (!$laporan) {
            $response = ['success' => false, 'message' => 'Laporan tidak ditemukan'];
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }
        
        // Check permission
        if (!$this->hasPermission($laporan['id_user'])) {
            $response = ['success' => false, 'message' => 'Anda tidak memiliki akses ke laporan ini'];
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }
        
        // Only allow delete if status is 'baru'
        if ($laporan['status_laporan'] !== 'baru') {
            $response = ['success' => false, 'message' => 'Hanya laporan dengan status baru yang dapat dihapus'];
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }
        
        // Delete file if exists
        if ($laporan['upload_file'] && file_exists($laporan['upload_file'])) {
            unlink($laporan['upload_file']);
        }
        
        $result = $this->laporanCamatModel->delete($id);
        
        if ($result) {
            $response = ['success' => true, 'message' => 'Laporan berhasil dihapus'];
        } else {
            $response = ['success' => false, 'message' => 'Gagal menghapus laporan'];
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    /**
     * Show detail of a laporan
     */
    public function detail($id = null) {
        $this->requireLogin();
        
        // Get ID from parameter or from GET request
        $id = $id ?? $_GET['id'] ?? 0;
        
        if (!$id) {
            $_SESSION['error'] = 'ID laporan tidak ditemukan';
            header('Location: index.php?controller=laporanCamat&action=index');
            exit;
        }
        
        $laporan = $this->laporanCamatModel->getById($id);
        
        if (!$laporan) {
            $_SESSION['error'] = 'Laporan tidak ditemukan';
            header('Location: index.php?controller=laporanCamat&action=index');
            exit;
        }
        
        // Check permission
        if (!$this->hasPermission($laporan['id_user'])) {
            $_SESSION['error'] = 'Anda tidak memiliki akses ke laporan ini';
            header('Location: index.php?controller=laporanCamat&action=index');
            exit;
        }
        
        require_once 'views/laporan-camat/detail.php';
    }

    /**
     * Update status (for admin only)
     */
    public function updateStatus($id = null) {
        $this->requireLogin();
        
        // Get ID from parameter or from GET request
        $id = $id ?? $_GET['id'] ?? 0;
        
        if (!$id) {
            $_SESSION['error'] = 'ID laporan tidak ditemukan';
            header('Location: index.php?controller=laporanCamat&action=index');
            exit;
        }
        
        if ($_SESSION['role'] !== 'admin') {
            $_SESSION['error'] = 'Hanya admin yang dapat mengubah status laporan';
            header('Location: index.php?controller=laporanCamat&action=detail&id=' . $id);
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=laporanCamat&action=detail&id=' . $id);
            exit;
        }
        
        $status = $_POST['status'] ?? '';
        $valid_status = ['baru', 'diproses', 'selesai'];
        
        if (!in_array($status, $valid_status)) {
            $_SESSION['error'] = 'Status tidak valid';
            header('Location: index.php?controller=laporanCamat&action=detail&id=' . $id);
            exit;
        }
        
        $result = $this->laporanCamatModel->updateStatus($id, $status);
        
        if ($result) {
            $_SESSION['success'] = 'Status laporan berhasil diperbarui';
        } else {
            $_SESSION['error'] = 'Gagal memperbarui status laporan';
        }
        
        header('Location: index.php?controller=laporan_camat&action=detail&id=' . $id);
        exit;
    }
    
    /**
     * Get allowed file types for validation
     */
    private function getAllowedFileTypes() {
        return [
            // Images
            'image/jpeg',
            'image/jpg', 
            'image/png',
            'image/gif',
            'image/webp',
            // Videos
            'video/mp4',
            'video/mpeg',
            'video/avi',
            'video/quicktime',
            'video/x-msvideo',
            'video/x-matroska',
            'video/webm',
            // Documents
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ];
    }
    
    /**
     * Get file extension based on MIME type
     */
    private function getExtensionFromMimeType($mimeType) {
        $mimeTypes = [
            'image/jpeg' => '.jpg',
            'image/jpg' => '.jpg',
            'image/png' => '.png',
            'image/gif' => '.gif',
            'image/webp' => '.webp',
            'video/mp4' => '.mp4',
            'video/mpeg' => '.mpeg',
            'video/avi' => '.avi',
            'video/quicktime' => '.mov',
            'video/x-msvideo' => '.avi',
            'video/x-matroska' => '.mkv',
            'video/webm' => '.webm',
            'application/pdf' => '.pdf',
            'application/msword' => '.doc',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => '.docx'
        ];

        return $mimeTypes[$mimeType] ?? '.file';
    }

    /**
     * Download file attachment
     */
    public function download($id = null) {
        $this->requireLogin();

        // Get ID from parameter or from GET request
        $id = $id ?? $_GET['id'] ?? 0;

        if (!$id) {
            $_SESSION['error'] = 'ID laporan tidak ditemukan';
            header('Location: index.php?controller=laporanCamat&action=index');
            exit;
        }

        $laporan = $this->laporanCamatModel->getById($id);

        if (!$laporan) {
            $_SESSION['error'] = 'Laporan tidak ditemukan';
            header('Location: index.php?controller=laporanCamat&action=index');
            exit;
        }

        // Check permission
        if (!$this->hasPermission($laporan['id_user'])) {
            $_SESSION['error'] = 'Anda tidak memiliki akses ke file ini';
            header('Location: index.php?controller=laporanCamat&action=index');
            exit;
        }

        // Check if file exists
        if (empty($laporan['upload_file']) || !file_exists($laporan['upload_file'])) {
            $_SESSION['error'] = 'File tidak ditemukan';
            header('Location: index.php?controller=laporanCamat&action=detail&id=' . $id);
            exit;
        }

        $filePath = $laporan['upload_file'];
        $fileName = basename($filePath);

        // Get file info
        $fileSize = filesize($filePath);
        $fileType = mime_content_type($filePath);

        // Set headers for download
        header('Content-Type: ' . $fileType);
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Content-Length: ' . $fileSize);
        header('Cache-Control: private, no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        // Clear output buffer
        if (ob_get_level()) {
            ob_clean();
        }

        // Read file and output
        readfile($filePath);
        exit;
    }

    /**
     * Export data to Excel
     */
    public function exportToExcel() {
        $this->requireLogin();

        // Only camat can export
        if ($_SESSION['role'] !== 'camat') {
            $_SESSION['error'] = 'Hanya camat yang dapat mengekspor data';
            header('Location: index.php?controller=laporanCamat&action=index');
            exit;
        }

        // Get filter parameters
        $search = $_GET['search'] ?? '';
        $status = $_GET['status'] ?? '';
        $tanggal_awal = $_GET['tanggal_awal'] ?? '';
        $tanggal_akhir = $_GET['tanggal_akhir'] ?? '';

        $current_user_id = $_SESSION['user_id'];

        try {
            // Debug logging
            error_log("Export attempt - User ID: " . $current_user_id . ", Role: " . $_SESSION['role']);
            error_log("Filters - Search: '" . $search . "', Status: '" . $status . "'");

            // Get data based on filters
            $laporans = $this->laporanCamatModel->getAllReportsForExport(
                $current_user_id,
                $search,
                $status,
                $tanggal_awal,
                $tanggal_akhir
            );

            error_log("Found " . count($laporans) . " records to export");

            if (empty($laporans)) {
                $_SESSION['error'] = 'Tidak ada data untuk diekspor';
                header('Location: index.php?controller=laporanCamat&action=index');
                exit;
            }

            // Generate HTML Excel output
            $filename = 'laporan_camat_' . date('Y-m-d_H-i-s') . '.xls';

            // Clear all output buffers first
            while (ob_get_level()) {
                ob_end_clean();
            }

            // Set headers for download
            header('Content-Type: application/vnd.ms-excel; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Cache-Control: private, no-cache, no-store, must-revalidate');
            header('Pragma: no-cache');
            header('Expires: 0');
            header('Content-Transfer-Encoding: binary');

            // Start HTML output
            echo '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Camat</title>
</head>
<body>
    <table border="1">
        <thead>
            <tr>
                <th>ID Laporan</th>
                <th>Tanggal Laporan</th>
                <th>Nama Pelapor</th>
                <th>Nama Desa</th>
                <th>Nama Kecamatan</th>
                <th>Waktu Kejadian</th>
                <th>Tujuan</th>
                <th>Uraian Laporan</th>
                <th>Status</th>
                <th>Lampiran File</th>
            </tr>
        </thead>
        <tbody>';

            // Output data rows
            foreach ($laporans as $laporan) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($laporan['id_laporan_camat']) . '</td>';
                echo '<td>' . (isset($laporan['created_at']) ? date('d/m/Y H:i', strtotime($laporan['created_at'])) : '') . '</td>';
                echo '<td>' . htmlspecialchars($laporan['nama_pelapor'] ?? '') . '</td>';
                echo '<td>' . htmlspecialchars($laporan['nama_desa'] ?? '') . '</td>';
                echo '<td>' . htmlspecialchars($laporan['nama_kecamatan'] ?? '') . '</td>';
                echo '<td>' . (isset($laporan['waktu_kejadian']) ? date('d/m/Y H:i', strtotime($laporan['waktu_kejadian'])) : '') . '</td>';
                echo '<td>' . htmlspecialchars(ucfirst($laporan['tujuan'] ?? '')) . '</td>';
                echo '<td>' . htmlspecialchars(strip_tags($laporan['uraian_laporan'] ?? '')) . '</td>';
                echo '<td>' . htmlspecialchars(ucfirst($laporan['status_laporan'] ?? '')) . '</td>';
                echo '<td>' . htmlspecialchars(!empty($laporan['upload_file']) ? basename($laporan['upload_file']) : 'Tidak ada') . '</td>';
                echo '</tr>';
            }

            echo '
        </tbody>
    </table>
</body>
</html>';

            exit;

        } catch (Exception $e) {
            // Log error for debugging
            error_log("Excel Export Error: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());

            $_SESSION['error'] = 'Terjadi kesalahan saat mengekspor data. Silakan coba lagi atau hubungi administrator.';
            header('Location: index.php?controller=laporanCamat&action=index');
            exit;
        }
    }
}

// Handle request
if (isset($_GET['action'])) {
    $laporanCamatController = new LaporanCamatController();

    switch ($_GET['action']) {
        case 'create':
            $laporanCamatController->create();
            break;
        case 'store':
            $laporanCamatController->store();
            break;
        case 'edit':
            $id = $_GET['id'] ?? 0;
            $laporanCamatController->edit($id);
            break;
        case 'update':
            $id = $_GET['id'] ?? 0;
            $laporanCamatController->update($id);
            break;
        case 'delete':
            $id = $_GET['id'] ?? 0;
            $laporanCamatController->delete($id);
            break;
        case 'detail':
            $id = $_GET['id'] ?? 0;
            $laporanCamatController->detail($id);
            break;
        case 'updateStatus':
            $id = $_GET['id'] ?? 0;
            $laporanCamatController->updateStatus($id);
            break;
        case 'download':
            $id = $_GET['id'] ?? 0;
            $laporanCamatController->download($id);
            break;
        case 'exportToExcel':
            $laporanCamatController->exportToExcel();
            break;
        default:
            $laporanCamatController->index();
            break;
    }
} else {
    $laporanCamatController = new LaporanCamatController();
    $laporanCamatController->index();
}
