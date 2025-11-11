<?php

require_once 'models/ProfileModel.php';
require_once 'models/AuthModel.php';

class ProfileController {
    private $profileModel;
    private $authModel;

    public function __construct() {
        $this->profileModel = new ProfileModel();
        $this->authModel = new AuthModel();
    }

    /**
     * Check if user is logged in
     */
    private function isLoggedIn() {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }

    /**
     * Check if user is admin
     */
    private function isAdmin() {
        return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }

    /**
     * Require admin login
     */
    private function requireAdminLogin() {
        if (!$this->isLoggedIn()) {
            $_SESSION['error'] = 'Silakan login terlebih dahulu';
            header('Location: index.php');
            exit;
        }

        if (!$this->isAdmin()) {
            $_SESSION['error'] = 'Akses ditolak. Halaman ini hanya untuk admin.';
            header('Location: index.php');
            exit;
        }
    }

    /**
     * Index: Display list of profiles
     */
    public function index() {
        $this->requireAdminLogin();

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $role_filter = isset($_GET['role']) ? $_GET['role'] : '';

        // Get profile data
        $profiles_result = $this->profileModel->getAllProfiles($page, $limit, $search, $role_filter);
        $profiles = $profiles_result['data'];
        $total = $profiles_result['total'];
        $total_pages = $profiles_result['total_pages'];

        // Get statistics
        $stats = $this->profileModel->getProfileStats();

        require_once 'views/profile/index.php';
    }

    /**
     * Show form to create new profile
     */
    public function create() {
        $this->requireAdminLogin();

        require_once 'views/profile/form.php';
    }

    /**
     * Store new profile
     */
    public function store() {
        $this->requireAdminLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=profile&action=create');
            exit;
        }

        // Validate input
        $nama_aplikasi = trim($_POST['nama_aplikasi'] ?? '');
        $role = trim($_POST['role'] ?? '');

        // Validation
        if (empty($nama_aplikasi) || empty($role)) {
            $_SESSION['error'] = 'Semua field wajib diisi';
            header('Location: index.php?controller=profile&action=create');
            exit;
        }

        // Validate role
        if (!in_array($role, ['camat', 'opd'])) {
            $_SESSION['error'] = 'Role tidak valid. Pilih antara camat atau opd.';
            header('Location: index.php?controller=profile&action=create');
            exit;
        }

        // Check if nama_aplikasi already exists
        if ($this->profileModel->checkNamaAplikasiExists($nama_aplikasi)) {
            $_SESSION['error'] = 'Nama aplikasi sudah digunakan. Silakan pilih nama lain.';
            header('Location: index.php?controller=profile&action=create');
            exit;
        }

        // Handle logo upload
        $logo = '';
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $upload_result = $this->handleLogoUpload($_FILES['logo']);
            if (!$upload_result['success']) {
                $_SESSION['error'] = $upload_result['message'];
                header('Location: index.php?controller=profile&action=create');
                exit;
            }
            $logo = $upload_result['file_path'];
        }

        // Prepare data
        $data = [
            'nama_aplikasi' => $nama_aplikasi,
            'logo' => $logo,
            'role' => $role
        ];

        $result = $this->profileModel->createProfile($data);

        if ($result['success']) {
            $_SESSION['success'] = $result['message'];
            header('Location: index.php?controller=profile&action=index');
        } else {
            $_SESSION['error'] = $result['message'];
            header('Location: index.php?controller=profile&action=create');
        }
        exit;
    }

    /**
     * Show form to edit profile
     */
    public function edit($id = null) {
        $this->requireAdminLogin();

        // Get ID from parameter or from GET/POST request
        $id = $id ?? $_GET['id'] ?? $_POST['id'] ?? 0;

        if (!$id) {
            $_SESSION['error'] = 'ID profile tidak ditemukan';
            header('Location: index.php?controller=profile&action=index');
            exit;
        }

        $profile = $this->profileModel->getProfileById($id);

        if (!$profile) {
            $_SESSION['error'] = 'Profile tidak ditemukan';
            header('Location: index.php?controller=profile&action=index');
            exit;
        }

        require_once 'views/profile/form.php';
    }

    /**
     * Update profile
     */
    public function update($id = null) {
        $this->requireAdminLogin();

        // Get ID from parameter or from GET/POST request
        $id = $id ?? $_GET['id'] ?? $_POST['id'] ?? 0;

        if (!$id) {
            $_SESSION['error'] = 'ID profile tidak ditemukan';
            header('Location: index.php?controller=profile&action=index');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=profile&action=edit&id=' . $id);
            exit;
        }

        // Validate input
        $nama_aplikasi = trim($_POST['nama_aplikasi'] ?? '');
        $role = trim($_POST['role'] ?? '');

        // Validation
        if (empty($nama_aplikasi) || empty($role)) {
            $_SESSION['error'] = 'Semua field wajib diisi';
            header('Location: index.php?controller=profile&action=edit&id=' . $id);
            exit;
        }

        // Validate role
        if (!in_array($role, ['camat', 'opd'])) {
            $_SESSION['error'] = 'Role tidak valid. Pilih antara camat atau opd.';
            header('Location: index.php?controller=profile&action=edit&id=' . $id);
            exit;
        }

        // Check if nama_aplikasi already exists (excluding current record)
        if ($this->profileModel->checkNamaAplikasiExists($nama_aplikasi, $id)) {
            $_SESSION['error'] = 'Nama aplikasi sudah digunakan. Silakan pilih nama lain.';
            header('Location: index.php?controller=profile&action=edit&id=' . $id);
            exit;
        }

        // Get current profile to check existing logo
        $current_profile = $this->profileModel->getProfileById($id);
        $logo = $current_profile['logo'] ?? '';

        // Handle logo upload
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $upload_result = $this->handleLogoUpload($_FILES['logo']);
            if (!$upload_result['success']) {
                $_SESSION['error'] = $upload_result['message'];
                header('Location: index.php?controller=profile&action=edit&id=' . $id);
                exit;
            }

            // Delete old logo if exists
            if (!empty($logo) && file_exists($logo)) {
                unlink($logo);
            }

            $logo = $upload_result['file_path'];
        }

        // Prepare data
        $data = [
            'nama_aplikasi' => $nama_aplikasi,
            'logo' => $logo,
            'role' => $role
        ];

        $result = $this->profileModel->updateProfile($id, $data);

        if ($result['success']) {
            $_SESSION['success'] = $result['message'];
            header('Location: index.php?controller=profile&action=index');
        } else {
            $_SESSION['error'] = $result['message'];
            header('Location: index.php?controller=profile&action=edit&id=' . $id);
        }
        exit;
    }

    /**
     * Delete profile
     */
    public function delete($id = null) {
        $this->requireAdminLogin();

        // Get ID from parameter or from GET/POST request
        $id = $id ?? $_GET['id'] ?? $_POST['id'] ?? 0;

        if (!$id) {
            $_SESSION['error'] = 'ID profile tidak ditemukan';
            header('Location: index.php?controller=profile&action=index');
            exit;
        }

        // Handle AJAX request
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
            $result = $this->profileModel->deleteProfile($id);

            header('Content-Type: application/json');
            echo json_encode($result);
            exit;
        } else {
            // Handle regular request
            $result = $this->profileModel->deleteProfile($id);

            if ($result['success']) {
                $_SESSION['success'] = $result['message'];
            } else {
                $_SESSION['error'] = $result['message'];
            }

            header('Location: index.php?controller=profile&action=index');
            exit;
        }
    }

    /**
     * Handle logo upload
     */
    private function handleLogoUpload($file) {
        // Check file size (5MB limit)
        if ($file['size'] > 5 * 1024 * 1024) {
            return [
                'success' => false,
                'message' => 'Ukuran file terlalu besar. Maksimal 5MB.'
            ];
        }

        // Check file type (more flexible for JPEG)
        $allowed_types = [
            'image/jpeg', 'image/pjpeg', 'image/png', 'image/gif', 'image/bmp', 'image/webp', 'image/svg+xml'
        ];
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg'];

        if (!in_array($file['type'], $allowed_types)) {
            return [
                'success' => false,
                'message' => 'Tipe file tidak didukung. Hanya gambar yang diperbolehkan. File type: ' . $file['type']
            ];
        }

        $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($file_extension, $allowed_extensions)) {
            return [
                'success' => false,
                'message' => 'Ekstensi file tidak didukung.'
            ];
        }

        // Create upload directory if not exists
        $upload_dir = 'uploads/profile_logos/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // Generate unique filename
        $filename = uniqid('profile_logo_') . '.' . $file_extension;
        $upload_path = $upload_dir . $filename;

        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $upload_path)) {
            // Set proper permissions for uploaded file
            chmod($upload_path, 0644);

            return [
                'success' => true,
                'file_path' => $upload_path,
                'message' => 'Logo berhasil diupload'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Gagal mengupload logo. Error: ' . error_get_last()['message'] ?? 'Unknown error'
            ];
        }
    }

    /**
     * Get profile as JSON for AJAX requests
     */
    public function getProfileList() {
        $this->requireAdminLogin();

        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
            $search = $_GET['search'] ?? '';
            $role_filter = $_GET['role'] ?? '';
            $result = $this->profileModel->getAllProfiles(1, 1000, $search, $role_filter);
            $profiles = $result['data'];

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'data' => $profiles
            ]);
            exit;
        } else {
            // For non-AJAX request, redirect to index
            header('Location: index.php?controller=profile&action=index');
            exit;
        }
    }
}