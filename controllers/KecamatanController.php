<?php

require_once 'models/WilayahModel.php';

class KecamatanController {
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
     * Menampilkan halaman utama kecamatan
     */
    public function index() {
        $this->requireAdmin();

        // Parameters untuk pagination dan search
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $search = $_GET['search'] ?? '';

        $result = $this->wilayahModel->getAllKecamatan($page, $limit, $search);
        $kecamatanData = $result['data'];
        $totalPages = $result['total_pages'];
        $currentPage = $result['current_page'];

        $statistics = $this->wilayahModel->getStatistics();

        include 'views/wilayah/index-kecamatan.php';
    }

    /**
     * Menampilkan form tambah/edit kecamatan
     */
    public function form() {
        $this->requireAdmin();

        $id_kecamatan = $_GET['id'] ?? null;
        $kecamatan = null;

        if ($id_kecamatan) {
            $kecamatan = $this->wilayahModel->getKecamatanById($id_kecamatan);
            if (!$kecamatan) {
                $_SESSION['error'] = 'Kecamatan tidak ditemukan';
                header('Location: views/wilayah/index-kecamatan.php');
                exit;
            }
        }

        include 'views/wilayah/form-kecamatan.php';
    }

    /**
     * Menyimpan kecamatan (tambah/edit)
     */
    public function save() {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_kecamatan = $_POST['id_kecamatan'] ?? null;
            $data = [
                'nama_kecamatan' => trim($_POST['nama_kecamatan'] ?? '')
            ];

            // Validasi
            if (empty($data['nama_kecamatan'])) {
                $response = [
                    'success' => false,
                    'message' => 'Nama kecamatan wajib diisi'
                ];
            } else {
                if ($id_kecamatan) {
                    // Update
                    $result = $this->wilayahModel->updateKecamatan($id_kecamatan, $data);
                    $message = $result ? 'Kecamatan berhasil diperbarui' : 'Gagal memperbarui kecamatan';
                    $response = [
                        'success' => $result,
                        'message' => $message
                    ];
                } else {
                    // Insert
                    $result = $this->wilayahModel->insertKecamatan($data);
                    $message = $result ? 'Kecamatan berhasil ditambahkan' : 'Gagal menambahkan kecamatan';
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
                header('Location: ../views/wilayah/index-kecamatan.php');
                exit;
            }
        }
    }

    /**
     * Menghapus kecamatan
     */
    public function delete() {
        $this->requireAdmin();

        $id_kecamatan = $_POST['id'] ?? null;
        if ($id_kecamatan) {
            $result = $this->wilayahModel->deleteKecamatan($id_kecamatan);
            $response = [
                'success' => $result['success'],
                'message' => $result['message'] ?? 'Gagal menghapus kecamatan'
            ];
        } else {
            $response = [
                'success' => false,
                'message' => 'ID kecamatan tidak valid'
            ];
        }

        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        } else {
            $_SESSION[$response['success'] ? 'success' : 'error'] = $response['message'];
            header('Location: ../views/wilayah/index-kecamatan.php');
            exit;
        }
    }

    /**
     * Get kecamatan stats for delete confirmation
     */
    public function getStats() {
        $this->requireAdmin();

        $id_kecamatan = $_GET['id'] ?? null;

        if (!$id_kecamatan) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'ID kecamatan tidak valid'
            ]);
            exit;
        }

        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
            // Get related desa
            $query = "SELECT id_desa, nama_desa FROM desa WHERE id_kecamatan = " . (int)$id_kecamatan . " ORDER BY nama_desa ASC";

            // Get database connection from model
            $db = $this->wilayahModel->db;
            $result = $db->query($query);

            $relatedDesaList = [];
            $relatedDesaCount = 0;

            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $relatedDesaList[] = "- " . htmlspecialchars($row['nama_desa']);
                    $relatedDesaCount++;
                }
            }

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'relatedDesaCount' => $relatedDesaCount,
                'relatedDesaList' => $relatedDesaList
            ]);
            exit;
        }
    }
}