<?php

require_once 'models/AuthModel.php';

class AuthController extends BaseController {
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
                $this->redirect(route('auth', 'admin'));
                break;
            case 'opd':
                $this->redirect(route('auth', 'opd'));
                break;
            case 'camat':
                $this->redirect(route('auth', 'camat'));
                break;
            default:
                $this->redirect(route('auth', 'index'));
                break;
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