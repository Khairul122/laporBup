<?php

require_once 'models/AuthModel.php';

class AuthController {
    private $authModel;

    public function __construct() {
        $this->authModel = new AuthModel();
    }

    /**
     * Menampilkan halaman landing (selector login)
     */
    public function index() {
        // Jika sudah login, redirect ke dashboard
        if ($this->isLoggedIn()) {
            $this->redirectToDashboard();
        }

        require_once 'views/landing/index.php';
    }

    /**
     * Menampilkan halaman login admin
     */
    public function admin() {
        // Jika sudah login, redirect ke dashboard
        if ($this->isLoggedIn()) {
            $this->redirectToDashboard();
        }

        $appData = [
            'name' => '',
            'title' => 'Login Administrator - ',
            'role' => 'admin',
            'description' => 'Sistem Informasi Laporan Gabungan Wilayah Kecamatan',
            'icon' => 'fas fa-user-shield'
        ];

        require_once 'views/auth/admin.php';
    }

    /**
     * Menampilkan halaman login camat
     */
    public function camat() {
        // Jika sudah login, redirect ke dashboard
        if ($this->isLoggedIn()) {
            $this->redirectToDashboard();
        }

        $appData = [
            'name' => 'Silap Gawat',
            'title' => 'Login Camat - Silap Gawat',
            'role' => 'camat',
            'description' => 'Sistem Informasi Laporan Gabungan Wilayah Kecamatan',
            'icon' => 'fas fa-landmark'
        ];

        require_once 'views/auth/camat.php';
    }

    /**
     * Menampilkan halaman login OPD
     */
    public function opd() {
        // Jika sudah login, redirect ke dashboard
        if ($this->isLoggedIn()) {
            $this->redirectToDashboard();
        }

        $appData = [
            'name' => 'Madina Maju Madani',
            'title' => 'Login OPD - Madina Maju Madani',
            'role' => 'opd',
            'description' => 'Sistem Pelaporan OPD Terpadu',
            'icon' => 'fas fa-building'
        ];

        require_once 'views/auth/opd.php';
    }

    /**
     * Proses login
     */
    public function login() {
        // Turn off error display for JSON response
        ini_set('display_errors', 0);
        error_reporting(0);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $username = trim($_POST['username'] ?? '');
                $password = $_POST['password'] ?? '';
                $loginRole = $_POST['login_role'] ?? ''; // Role dari halaman login

                $errors = [];

                // Validasi input
                if (empty($username)) {
                    $errors[] = 'Username harus diisi';
                }
                if (empty($password)) {
                    $errors[] = 'Password harus diisi';
                }
                if (empty($loginRole)) {
                    $errors[] = 'Role login tidak valid';
                }

                if (empty($errors)) {
                    $user = $this->authModel->login($username, $password);

                    if ($user) {
                        // Validasi role sesuai dengan halaman login
                        if ($user['role'] !== $loginRole) {
                            $response = [
                                'success' => false,
                                'message' => 'Username atau password salah',
                                'role_mismatch' => true,
                                'correct_role' => $user['role']
                            ];
                        } else {
                            // Set session
                            $_SESSION['user_id'] = $user['id_user'];
                            $_SESSION['username'] = $user['username'];
                            $_SESSION['email'] = $user['email'];
                            $_SESSION['jabatan'] = $user['jabatan'];
                            $_SESSION['role'] = $user['role'];
                            $_SESSION['logged_in'] = true;

                            // Response sukses
                            $response = [
                                'success' => true,
                                'message' => 'Login berhasil',
                                'redirect' => $this->getDashboardUrl()
                            ];
                        }
                    } else {
                        $response = [
                            'success' => false,
                            'message' => 'Username atau password salah'
                        ];
                    }
                } else {
                    $response = [
                        'success' => false,
                        'message' => implode('<br>', $errors)
                    ];
                }

            } catch (Exception $e) {
                $response = [
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage()
                ];
            }

            // Return JSON response
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }
    }

    /**
     * Proses logout
     */
    public function logout() {
        // Simpan role sebelum session dihancurkan
        $currentRole = $_SESSION['role'] ?? 'user';
        
        session_destroy();

        // Hapus cookie session
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }

        // Redirect ke halaman login sesuai role sebelum logout
        switch ($currentRole) {
            case 'admin':
                $redirectUrl = 'index.php?controller=auth&action=admin';
                break;
            case 'opd':
                $redirectUrl = 'index.php?controller=auth&action=opd';
                break;
            case 'camat':
                $redirectUrl = 'index.php?controller=auth&action=camat';
                break;
            default:
                $redirectUrl = 'index.php?controller=auth&action=index';
                break;
        }

        header('Location: ' . $redirectUrl);
        exit;
    }

    /**
     * Cek apakah user sudah login
     */
    public function isLoggedIn() {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }

    /**
     * Mendapatkan role user yang sedang login
     */
    public function getUserRole() {
        return $_SESSION['role'] ?? null;
    }

    /**
     * Mendapatkan data user yang sedang login
     */
    public function getCurrentUser() {
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
    public function requireLogin() {
        if (!$this->isLoggedIn()) {
            $response = [
                'success' => false,
                'message' => 'Silakan login terlebih dahulu',
                'redirect' => 'index.php?auth=login'
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
     * Require role tertentu untuk mengakses halaman
     */
    public function requireRole($requiredRole) {
        $this->requireLogin();

        if ($_SESSION['role'] !== $requiredRole) {
            $response = [
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke halaman ini'
            ];

            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                header('Content-Type: application/json');
                echo json_encode($response);
                exit;
            } else {
                // Redirect ke dashboard user
                $this->redirectToDashboard();
                exit;
            }
        }
    }

    /**
     * Redirect ke dashboard sesuai role
     */
    private function redirectToDashboard() {
        $dashboardUrl = $this->getDashboardUrl();
        header("Location: $dashboardUrl");
        exit;
    }

    /**
     * Mendapatkan URL dashboard sesuai role
     */
    private function getDashboardUrl() {
        $role = $_SESSION['role'] ?? '';

        switch ($role) {
            case 'admin':
                return 'index.php?controller=dashboard&action=admin';
            case 'camat':
                return 'index.php?controller=dashboard&action=camat';
            case 'opd':
                return 'index.php?controller=dashboard&action=opd';
            default:
                return 'index.php';
        }
    }
}

// Proses request
if (isset($_GET['action'])) {
    $authController = new AuthController();

    switch ($_GET['action']) {
        case 'login':
            $authController->login();
            break;
        case 'logout':
            $authController->logout();
            break;
        case 'admin':
            $authController->admin();
            break;
        case 'camat':
            $authController->camat();
            break;
        case 'opd':
            $authController->opd();
            break;
        default:
            $authController->index();
            break;
    }
} else {
    $authController = new AuthController();
    $authController->index();
}