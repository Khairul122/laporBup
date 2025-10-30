<?php

require_once 'models/WilayahModel.php';

class DesaController {
    private $wilayahModel;

    public function __construct() {
        $this->wilayahModel = new WilayahModel();
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
                header('Location: index.php?controller=dashboard&action=' . $_SESSION['role']);
                exit;
            }
        }
    }

    /**
     * Menampilkan halaman utama desa
     */
    public function index() {
        $this->requireAdmin();

        // Parameters untuk pagination dan search
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $search = $_GET['search'] ?? '';
        $kecamatan_filter = $_GET['kecamatan_filter'] ?? '';

        $result = $this->wilayahModel->getAllDesa($page, $limit, $search, $kecamatan_filter);
        $desaData = $result['data'];
        $totalPages = $result['total_pages'];
        $currentPage = $result['current_page'];
        $kecamatanOptions = $this->wilayahModel->getKecamatanOptions();

        $statistics = $this->wilayahModel->getStatistics();

        include 'views/wilayah/index-desa.php';
    }

    /**
     * Menampilkan form tambah/edit desa
     */
    public function form() {
        $this->requireAdmin();

        $id_desa = $_GET['id'] ?? null;
        $desa = null;
        $kecamatanOptions = $this->wilayahModel->getKecamatanOptions();

        if ($id_desa) {
            $desa = $this->wilayahModel->getDesaById($id_desa);
            if (!$desa) {
                $_SESSION['error'] = 'Desa tidak ditemukan';
                header('Location: ../views/wilayah/index-desa.php');
                exit;
            }
        }

        include 'views/wilayah/form-desa.php';
    }

    /**
     * Menyimpan desa (tambah/edit)
     */
    public function save() {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_desa = $_POST['id_desa'] ?? null;
            $data = [
                'id_kecamatan' => (int)($_POST['id_kecamatan'] ?? 0),
                'nama_desa' => trim($_POST['nama_desa'] ?? '')
            ];

            // Validasi
            if (empty($data['nama_desa'])) {
                $response = [
                    'success' => false,
                    'message' => 'Nama desa wajib diisi'
                ];
            } elseif (empty($data['id_kecamatan'])) {
                $response = [
                    'success' => false,
                    'message' => 'Kecamatan wajib dipilih'
                ];
            } else {
                if ($id_desa) {
                    // Update
                    $result = $this->wilayahModel->updateDesa($id_desa, $data);
                    $message = $result ? 'Desa berhasil diperbarui' : 'Gagal memperbarui desa';
                    $response = [
                        'success' => $result,
                        'message' => $message
                    ];
                } else {
                    // Insert
                    $result = $this->wilayahModel->insertDesa($data);
                    $message = $result ? 'Desa berhasil ditambahkan' : 'Gagal menambahkan desa';
                    $response = [
                        'success' => $result,
                        'message' => $message
                    ];
                }
            }

            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                header('Content-Type: application/json');
                echo json_encode($response);
                exit;
            } else {
                $_SESSION[$response['success'] ? 'success' : 'error'] = $response['message'];
                header('Location: ../views/wilayah/index-desa.php');
                exit;
            }
        }
    }

    /**
     * Menghapus desa
     */
    public function delete() {
        $this->requireAdmin();

        $id_desa = $_POST['id'] ?? null;
        if ($id_desa) {
            $result = $this->wilayahModel->deleteDesa($id_desa);
            $response = [
                'success' => $result['success'],
                'message' => $result['message'] ?? 'Gagal menghapus desa'
            ];
        } else {
            $response = [
                'success' => false,
                'message' => 'ID desa tidak valid'
            ];
        }

        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        } else {
            $_SESSION[$response['success'] ? 'success' : 'error'] = $response['message'];
            header('Location: ../views/wilayah/index-desa.php');
            exit;
        }
    }

    /**
     * Get kecamatan options for AJAX
     */
    public function getKecamatanOptions() {
        $this->requireAdmin();

        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
            $options = $this->wilayahModel->getKecamatanOptions();
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'data' => $options
            ]);
            exit;
        }
    }

    /**
     * Get desa by kecamatan for AJAX
     */
    public function getDesaByKecamatan() {
        $this->requireLogin();
        
        $user_role = $this->getUserRole();
        if ($user_role !== 'admin' && $user_role !== 'camat') {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke endpoint ini'
            ]);
            exit;
        }

        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
            $id_kecamatan = (int)($_GET['id_kecamatan'] ?? 0);
            
            if ($id_kecamatan) {
                $desa_list = $this->wilayahModel->getDesaByKecamatan($id_kecamatan);
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'data' => $desa_list
                ]);
            } else {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'ID kecamatan tidak valid'
                ]);
            }
            exit;
        } else {
            // For non-AJAX request, redirect to index
            header('Location: index.php?controller=desa&action=index');
            exit;
        }
    }
}