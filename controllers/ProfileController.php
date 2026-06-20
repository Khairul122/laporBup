<?php

require_once 'models/ProfileModel.php';
require_once 'models/AuthModel.php';

class ProfileController extends BaseController {
    private $profileModel;
    private $authModel;

    public function __construct() {
        $this->profileModel = new ProfileModel();
        $this->authModel = new AuthModel();
    }

    
    private function isAdmin() {
        return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }

    
    private function requireAdminLogin() {
        if (!$this->isLoggedIn()) {
            $_SESSION['error'] = 'Silakan login terlebih dahulu';
            $this->redirect('index.php');
        }

        if (!$this->isAdmin()) {
            $_SESSION['error'] = 'Akses ditolak. Halaman ini hanya untuk admin.';
            $this->redirect('index.php');
        }
    }

    
    public function index() {
        $this->requireAdminLogin();

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $role_filter = isset($_GET['role']) ? $_GET['role'] : '';

        
        $profiles_result = $this->profileModel->getAllProfiles($page, $limit, $search, $role_filter);
        $profiles = $profiles_result['data'];
        $total = $profiles_result['total'];
        $total_pages = $profiles_result['total_pages'];

        
        $stats = $this->profileModel->getProfileStats();

        require_once 'views/profile/index.php';
    }

    
    public function create() {
        $this->requireAdminLogin();

        require_once 'views/profile/form.php';
    }

    
    public function store() {
        $this->requireAdminLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(route('profile', 'create'));
        }

        
        $nama_aplikasi = trim($_POST['nama_aplikasi'] ?? '');
        $role = trim($_POST['role'] ?? '');

        
        if (empty($nama_aplikasi) || empty($role)) {
            $_SESSION['error'] = 'Semua field wajib diisi';
            $this->redirect(route('profile', 'create'));
        }

        
        if (!in_array($role, ['camat', 'opd'])) {
            $_SESSION['error'] = 'Role tidak valid. Pilih antara camat atau opd.';
            $this->redirect(route('profile', 'create'));
        }

        
        if ($this->profileModel->checkNamaAplikasiExists($nama_aplikasi)) {
            $_SESSION['error'] = 'Nama aplikasi sudah digunakan. Silakan pilih nama lain.';
            $this->redirect(route('profile', 'create'));
        }

        
        $logo = '';
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $upload_result = $this->handleLogoUpload($_FILES['logo']);
            if (!$upload_result['success']) {
                $_SESSION['error'] = $upload_result['message'];
                $this->redirect(route('profile', 'create'));
            }
            $logo = $upload_result['file_path'];
        }

        
        $data = [
            'nama_aplikasi' => $nama_aplikasi,
            'logo' => $logo,
            'role' => $role
        ];

        $result = $this->profileModel->createProfile($data);

        if ($result['success']) {
            $_SESSION['success'] = $result['message'];
            $this->redirect(route('profile', 'index'));
        } else {
            $_SESSION['error'] = $result['message'];
            $this->redirect(route('profile', 'create'));
        }
        exit;
    }

    
    public function edit($id = null) {
        $this->requireAdminLogin();

        
        $id = $id ?? $_GET['id'] ?? $_POST['id'] ?? 0;

        if (!$id) {
            $_SESSION['error'] = 'ID profile tidak ditemukan';
            $this->redirect(route('profile', 'index'));
        }

        $profile = $this->profileModel->getProfileById($id);

        if (!$profile) {
            $_SESSION['error'] = 'Profile tidak ditemukan';
            $this->redirect(route('profile', 'index'));
        }

        require_once 'views/profile/form.php';
    }

    
    public function update($id = null) {
        $this->requireAdminLogin();

        
        $id = $id ?? $_GET['id'] ?? $_POST['id'] ?? 0;

        if (!$id) {
            $_SESSION['error'] = 'ID profile tidak ditemukan';
            $this->redirect(route('profile', 'index'));
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(route('profile', 'edit', ['id' => $id]));
            exit;
        }

        
        $nama_aplikasi = trim($_POST['nama_aplikasi'] ?? '');
        $role = trim($_POST['role'] ?? '');

        
        if (empty($nama_aplikasi) || empty($role)) {
            $_SESSION['error'] = 'Semua field wajib diisi';
            $this->redirect(route('profile', 'edit', ['id' => $id]));
            exit;
        }

        
        if (!in_array($role, ['camat', 'opd'])) {
            $_SESSION['error'] = 'Role tidak valid. Pilih antara camat atau opd.';
            $this->redirect(route('profile', 'edit', ['id' => $id]));
            exit;
        }

        
        if ($this->profileModel->checkNamaAplikasiExists($nama_aplikasi, $id)) {
            $_SESSION['error'] = 'Nama aplikasi sudah digunakan. Silakan pilih nama lain.';
            $this->redirect(route('profile', 'edit', ['id' => $id]));
            exit;
        }

        
        $current_profile = $this->profileModel->getProfileById($id);
        $logo = $current_profile['logo'] ?? '';

        
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $upload_result = $this->handleLogoUpload($_FILES['logo']);
            if (!$upload_result['success']) {
                $_SESSION['error'] = $upload_result['message'];
                $this->redirect(route('profile', 'edit', ['id' => $id]));
                exit;
            }

            
            if (!empty($logo) && file_exists($logo)) {
                unlink($logo);
            }

            $logo = $upload_result['file_path'];
        }

        
        $data = [
            'nama_aplikasi' => $nama_aplikasi,
            'logo' => $logo,
            'role' => $role
        ];

        $result = $this->profileModel->updateProfile($id, $data);

        if ($result['success']) {
            $_SESSION['success'] = $result['message'];
            $this->redirect(route('profile', 'index'));
        } else {
            $_SESSION['error'] = $result['message'];
            $this->redirect(route('profile', 'edit', ['id' => $id]));
        }
        exit;
    }

    
    public function delete($id = null) {
        $this->requireAdminLogin();

        
        $id = $id ?? $_GET['id'] ?? $_POST['id'] ?? 0;

        if (!$id) {
            $_SESSION['error'] = 'ID profile tidak ditemukan';
            $this->redirect(route('profile', 'index'));
        }

        
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
            $result = $this->profileModel->deleteProfile($id);

            header('Content-Type: application/json');
            echo json_encode($result);
            exit;
        } else {
            
            $result = $this->profileModel->deleteProfile($id);

            if ($result['success']) {
                $_SESSION['success'] = $result['message'];
            } else {
                $_SESSION['error'] = $result['message'];
            }

            $this->redirect(route('profile', 'index'));
        }
    }

    
    private function handleLogoUpload($file) {
        
        if ($file['size'] > 5 * 1024 * 1024) {
            return [
                'success' => false,
                'message' => 'Ukuran file terlalu besar. Maksimal 5MB.'
            ];
        }

        
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

        
        $upload_dir = 'uploads/profile_logos/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        
        $filename = uniqid('profile_logo_') . '.' . $file_extension;
        $upload_path = $upload_dir . $filename;

        
        if (move_uploaded_file($file['tmp_name'], $upload_path)) {
            
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
            
            $this->redirect(route('profile', 'index'));
        }
    }
}