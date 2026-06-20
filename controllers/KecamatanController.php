<?php

require_once 'models/WilayahModel.php';

class KecamatanController extends BaseController {
    private $wilayahModel;

    public function __construct() {
        $this->wilayahModel = new WilayahModel();
    }

    
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
                $this->redirectToDashboard();
            }
        }
    }

    
    public function index() {
        $this->requireAdmin();

        
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

    
    public function form($id_kecamatan = null) {
        $this->requireAdmin();

        $id_kecamatan = $id_kecamatan ?? $_GET['id'] ?? null;
        $kecamatan = null;

        if ($id_kecamatan) {
            $kecamatan = $this->wilayahModel->getKecamatanById($id_kecamatan);
            if (!$kecamatan) {
                $_SESSION['error'] = 'Kecamatan tidak ditemukan';
                $this->redirect(route('kecamatan', 'index'));
            }
        }

        include 'views/wilayah/form-kecamatan.php';
    }

    
    public function save($id_kecamatan = null) {
        $this->requireAdmin();

        if (in_array($_SERVER['REQUEST_METHOD'], ['POST', 'PUT'], true)) {
            $id_kecamatan = $id_kecamatan ?? $_POST['id_kecamatan'] ?? null;
            $data = [
                'nama_kecamatan' => trim($_POST['nama_kecamatan'] ?? '')
            ];

            
            if (empty($data['nama_kecamatan'])) {
                $response = [
                    'success' => false,
                    'message' => 'Nama kecamatan wajib diisi'
                ];
            } else {
                if ($id_kecamatan) {
                    
                    $result = $this->wilayahModel->updateKecamatan($id_kecamatan, $data);
                    $message = $result ? 'Kecamatan berhasil diperbarui' : 'Gagal memperbarui kecamatan';
                    $response = [
                        'success' => $result,
                        'message' => $message
                    ];
                } else {
                    
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
                $this->redirect(route('kecamatan', 'index'));
            }
        }
    }

    
    public function delete($id_kecamatan = null) {
        $this->requireAdmin();

        $id_kecamatan = $id_kecamatan ?? $_POST['id'] ?? null;
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
            $this->redirect(route('kecamatan', 'index'));
        }
    }

    
    public function getStats($id_kecamatan = null) {
        $this->requireAdmin();

        $id_kecamatan = $id_kecamatan ?? $_GET['id'] ?? null;

        if (!$id_kecamatan) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'ID kecamatan tidak valid'
            ]);
            exit;
        }

        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
            
            $query = "SELECT id_desa, nama_desa FROM desa WHERE id_kecamatan = " . (int)$id_kecamatan . " ORDER BY nama_desa ASC";

            
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
