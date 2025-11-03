<?php

require_once 'models/DataPelaporModel.php';

class DataPelaporController {
    private $dataPelaporModel;

    public function __construct() {
        $this->dataPelaporModel = new DataPelaporModel();
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
     * Menampilkan halaman daftar data pelapor
     */
    public function index() {
        $this->requireAdmin();

        $user = $this->getCurrentUser();
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $search = $_GET['search'] ?? '';
        $role = $_GET['role'] ?? '';

        // Get data pelapor
        $result = $this->dataPelaporModel->getAllDataPelapor($page, $limit, $search, $role);

        // Get statistics
        $statistics = $this->dataPelaporModel->getPelaporStatistics();

        require_once 'views/data-pelapor/index.php';
    }

    /**
     * Menampilkan halaman form tambah/edit data pelapor
     */
    public function form() {
        $this->requireAdmin();

        $user = $this->getCurrentUser();
        $id = $_GET['id'] ?? null;
        $dataPelapor = null;

        if ($id) {
            $dataPelapor = $this->dataPelaporModel->getDataPelaporById($id);
            if (!$dataPelapor) {
                $_SESSION['error'] = 'Data pelapor tidak ditemukan';
                header('Location: index.php?controller=dataPelapor');
                exit;
            }
        }

        require_once 'views/data-pelapor/form.php';
    }

    /**
     * Menyimpan data pelapor (create/update)
     */
    public function save() {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Validasi input
                $errors = [];

                $username = trim($_POST['username'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $jabatan = trim($_POST['jabatan'] ?? '');
                $role = $_POST['role'] ?? '';
                $no_telp = trim($_POST['no_telp'] ?? '');
                $password = $_POST['password'] ?? '';
                $confirmPassword = $_POST['confirm_password'] ?? '';
                $id = $_POST['id'] ?? null;

                // Validasi required fields
                if (empty($username)) {
                    $errors[] = 'Username harus diisi';
                }
                if (empty($email)) {
                    $errors[] = 'Email harus diisi';
                }
                if (empty($jabatan)) {
                    $errors[] = 'Jabatan harus diisi';
                }
                if (empty($role) || !in_array($role, ['camat', 'opd'])) {
                    $errors[] = 'Role harus dipilih (camat atau opd)';
                }

                // Validasi nomor telepon (opsional)
                if (!empty($no_telp)) {
                    // Validasi format nomor telepon Indonesia
                    if (!preg_match('/^(^\\+62|62|^08)[0-9]{8,13}$/', $no_telp)) {
                        $errors[] = 'Format nomor telepon tidak valid. Gunakan format: 08xxxxxxxxxx atau +62xxxxxxxxxx';
                    }
                }

                // Validasi format
                if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = 'Format email tidak valid';
                }

                if (!empty($username) && strlen($username) < 3) {
                    $errors[] = 'Username minimal 3 karakter';
                }

                // Validasi password untuk create baru
                if (!$id) {
                    if (empty($password)) {
                        $errors[] = 'Password harus diisi';
                    } elseif (strlen($password) < 6) {
                        $errors[] = 'Password minimal 6 karakter';
                    } elseif ($password !== $confirmPassword) {
                        $errors[] = 'Konfirmasi password tidak cocok';
                    }
                }
                // Validasi password untuk update (jika diisi)
                elseif (!empty($password)) {
                    if (strlen($password) < 6) {
                        $errors[] = 'Password minimal 6 karakter';
                    } elseif ($password !== $confirmPassword) {
                        $errors[] = 'Konfirmasi password tidak cocok';
                    }
                }

                if (!empty($errors)) {
                    $response = [
                        'success' => false,
                        'message' => implode('<br>', $errors)
                    ];
                } else {
                    $data = [
                        'username' => $username,
                        'email' => $email,
                        'jabatan' => $jabatan,
                        'role' => $role,
                        'no_telp' => $no_telp
                    ];

                    // Tambahkan password hanya jika diisi
                    if (!empty($password)) {
                        $data['password'] = $password;
                    }

                    if ($id) {
                        // Update
                        $result = $this->dataPelaporModel->updateDataPelapor($id, $data);
                        $response = $result;
                    } else {
                        // Create
                        $result = $this->dataPelaporModel->createDataPelapor($data);
                        $response = $result;
                    }
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

    /**
     * Menghapus data pelapor
     */
    public function delete() {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $id = $_POST['id'] ?? null;

                if (!$id) {
                    $response = [
                        'success' => false,
                        'message' => 'ID pelapor tidak valid'
                    ];
                } else {
                    $result = $this->dataPelaporModel->deleteDataPelapor($id);
                    $response = $result;
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

    /**
     * API endpoint untuk mendapatkan data pelapor (JSON)
     */
    public function getDataPelapor() {
        $this->requireAdmin();

        header('Content-Type: application/json');

        try {
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
            $search = $_GET['search'] ?? '';
            $role = $_GET['role'] ?? '';

            $result = $this->dataPelaporModel->getAllDataPelapor($page, $limit, $search, $role);

            echo json_encode([
                'success' => true,
                'data' => $result
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
        exit;
    }

    /**
     * API endpoint untuk search pelapor (autocomplete)
     */
    public function searchPelapor() {
        $this->requireAdmin();

        header('Content-Type: application/json');

        try {
            $keyword = $_GET['q'] ?? '';

            if (strlen($keyword) < 2) {
                echo json_encode([
                    'success' => true,
                    'data' => []
                ]);
                exit;
            }

            $data = $this->dataPelaporModel->searchDataPelapor($keyword);

            echo json_encode([
                'success' => true,
                'data' => $data
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
        exit;
    }

    /**
     * API endpoint untuk statistik pelapor
     */
    public function getStatistics() {
        $this->requireAdmin();

        header('Content-Type: application/json');

        try {
            $statistics = $this->dataPelaporModel->getPelaporStatistics();

            echo json_encode([
                'success' => true,
                'data' => $statistics
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
        exit;
    }

    /**
     * Export data pelapor ke CSV
     */
    public function export() {
        $this->requireAdmin();

        try {
            $role = $_GET['role'] ?? '';
            $data = $this->dataPelaporModel->getDataPelaporByRole($role);

            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="data_pelapor_' . date('Y-m-d') . '.csv"');

            $output = fopen('php://output', 'w');

            // Header
            fputcsv($output, ['ID', 'Username', 'Email', 'No. Telepon', 'Jabatan', 'Role', 'Total Laporan', 'Tanggal Dibuat']);

            // Data
            foreach ($data as $row) {
                fputcsv($output, [
                    $row['id_user'],
                    $row['username'],
                    $row['email'],
                    $row['no_telp'] ?? '-',
                    $row['jabatan'],
                    ucfirst($row['role']),
                    $row['total_laporan'],
                    date('d/m/Y H:i', strtotime($row['created_at']))
                ]);
            }

            fclose($output);
            exit;
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error export: ' . $e->getMessage();
            header('Location: index.php?controller=dataPelapor');
            exit;
        }
    }
}

// Proses request
if (isset($_GET['action'])) {
    $dataPelaporController = new DataPelaporController();

    switch ($_GET['action']) {
        case 'form':
            $dataPelaporController->form();
            break;
        case 'save':
            $dataPelaporController->save();
            break;
        case 'delete':
            $dataPelaporController->delete();
            break;
        case 'getData':
            $dataPelaporController->getDataPelapor();
            break;
        case 'search':
            $dataPelaporController->searchPelapor();
            break;
        case 'statistics':
            $dataPelaporController->getStatistics();
            break;
        case 'export':
            $dataPelaporController->export();
            break;
        default:
            $dataPelaporController->index();
            break;
    }
} else {
    $dataPelaporController = new DataPelaporController();
    $dataPelaporController->index();
}