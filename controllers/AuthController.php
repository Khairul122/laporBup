<?php

require_once 'models/AuthModel.php';

class AuthController extends BaseController {
    private $authModel;

    public function __construct() {
        $this->authModel = new AuthModel();
    }

    public function index() {
        if ($this->isLoggedIn()) {
            $this->redirectToDashboard();
        }

        require_once 'views/landing/index.php';
    }

    public function admin() {
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

    public function camat() {
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

    public function opd() {
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

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $username = trim($_POST['username'] ?? '');
                $password = $_POST['password'] ?? '';
                $loginRole = $_POST['login_role'] ?? '';

                $errors = [];

                if (empty($username)) {
                    $errors[] = 'Username harus diisi';
                }
                if (empty($password)) {
                    $errors[] = 'Password harus diisi';
                }
                if (empty($loginRole)) {
                    $errors[] = 'Role login tidak valid';
                }

                if (empty($errors) && $this->isLoginLocked()) {
                    $remaining = $this->getLoginLockRemaining();
                    $errors[] = 'Terlalu banyak percobaan login gagal. Coba lagi dalam ' . ceil($remaining / 60) . ' menit.';
                }

                if (empty($errors)) {
                    $user = $this->authModel->login($username, $password);

                    if ($user) {
                        if ($user['role'] !== $loginRole) {
                            $this->recordLoginAttempt(false);
                            $response = [
                                'success' => false,
                                'message' => 'Username atau password salah',
                                'role_mismatch' => true,
                                'correct_role' => $user['role']
                            ];
                        } else {
                            $this->recordLoginAttempt(true);

                            session_regenerate_id(true);

                            $_SESSION['user_id'] = $user['id_user'];
                            $_SESSION['username'] = $user['username'];
                            $_SESSION['email'] = $user['email'];
                            $_SESSION['jabatan'] = $user['jabatan'];
                            $_SESSION['role'] = $user['role'];
                            $_SESSION['logged_in'] = true;

                            $response = [
                                'success' => true,
                                'message' => 'Login berhasil',
                                'redirect' => $this->getDashboardUrl()
                            ];
                        }
                    } else {
                        $this->recordLoginAttempt(false);
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

            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }
    }

    private function isLoginLocked() {
        return $this->getLoginLockRemaining() > 0;
    }

    private function getLoginLockRemaining() {
        $attempts = $_SESSION['login_attempts'] ?? 0;
        $lastAttempt = $_SESSION['login_last_attempt'] ?? 0;

        if ($attempts < MAX_LOGIN_ATTEMPTS) {
            return 0;
        }

        $elapsed = time() - $lastAttempt;
        $remaining = LOGIN_TIMEOUT - $elapsed;

        if ($remaining <= 0) {
            unset($_SESSION['login_attempts'], $_SESSION['login_last_attempt']);
            return 0;
        }

        return $remaining;
    }

    private function recordLoginAttempt($success) {
        if ($success) {
            unset($_SESSION['login_attempts'], $_SESSION['login_last_attempt']);
            return;
        }

        $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;
        $_SESSION['login_last_attempt'] = time();
    }

    public function logout() {
        $currentRole = $_SESSION['role'] ?? 'user';
        
        session_destroy();

        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }

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