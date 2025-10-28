<?php

require_once 'models/AuthModel.php';

class AuthController {
    private $authModel;

    public function __construct() {
        $this->authModel = new AuthModel();
    }

    /**
     * Menampilkan halaman login
     */
    public function index() {
        // Jika sudah login, redirect ke dashboard
        if ($this->isLoggedIn()) {
            $this->redirectToDashboard();
        }

        require_once 'views/auth/index.php';
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

                $errors = [];

                // Validasi input
                if (empty($username)) {
                    $errors[] = 'Username harus diisi';
                }
                if (empty($password)) {
                    $errors[] = 'Password harus diisi';
                }

                if (empty($errors)) {
                    $user = $this->authModel->login($username, $password);

                    if ($user) {
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
        session_destroy();

        // Hapus cookie session
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }

        // Redirect langsung ke halaman login
        header('Location: index.php?controller=auth&action=index');
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
        default:
            $authController->index();
            break;
    }
} else {
    $authController = new AuthController();
    $authController->index();
}