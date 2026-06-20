<?php

require_once 'models/WilayahModel.php';

class WilayahController extends BaseController {
    private $wilayahModel;

    public function __construct() {
        $this->wilayahModel = new WilayahModel();
    }

    public function index() {
        $this->redirect(route('wilayah', 'indexKecamatan'));
    }

    public function indexKecamatan() {
        $this->requireRole('admin');

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

    public function indexDesa() {
        $this->requireRole('admin');

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

    public function formKecamatan() {
        $this->requireRole('admin');

        $id_kecamatan = $_GET['id'] ?? null;
        $kecamatan = null;

        if ($id_kecamatan) {
            $kecamatan = $this->wilayahModel->getKecamatanById($id_kecamatan);
            if (!$kecamatan) {
                $_SESSION['error'] = 'Kecamatan tidak ditemukan';
                $this->redirect(route('wilayah', 'indexKecamatan'));
            }
        }

        include 'views/wilayah/form-kecamatan.php';
    }

    public function formDesa() {
        $this->requireRole('admin');

        $id_desa = $_GET['id'] ?? null;
        $desa = null;
        $kecamatanOptions = $this->wilayahModel->getKecamatanOptions();

        if ($id_desa) {
            $desa = $this->wilayahModel->getDesaById($id_desa);
            if (!$desa) {
                $_SESSION['error'] = 'Desa tidak ditemukan';
                $this->redirect(route('wilayah', 'indexDesa'));
            }
        }

        include 'views/wilayah/form-desa.php';
    }

    public function saveKecamatan() {
        $this->requireRole('admin');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_kecamatan = $_POST['id_kecamatan'] ?? null;
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

            if ($this->isAjaxRequest()) {
                $this->json($response);
            } else {
                $_SESSION[$response['success'] ? 'success' : 'error'] = $response['message'];
                $this->redirect(route('wilayah', 'indexKecamatan'));
            }
        }
    }

    public function saveDesa() {
        $this->requireRole('admin');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_desa = $_POST['id_desa'] ?? null;
            $data = [
                'id_kecamatan' => (int)($_POST['id_kecamatan'] ?? 0),
                'nama_desa' => trim($_POST['nama_desa'] ?? '')
            ];

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
                    $result = $this->wilayahModel->updateDesa($id_desa, $data);
                    $message = $result ? 'Desa berhasil diperbarui' : 'Gagal memperbarui desa';
                    $response = [
                        'success' => $result,
                        'message' => $message
                    ];
                } else {
                    $result = $this->wilayahModel->insertDesa($data);
                    $message = $result ? 'Desa berhasil ditambahkan' : 'Gagal menambahkan desa';
                    $response = [
                        'success' => $result,
                        'message' => $message
                    ];
                }
            }

            if ($this->isAjaxRequest()) {
                $this->json($response);
            } else {
                $_SESSION[$response['success'] ? 'success' : 'error'] = $response['message'];
                $this->redirect(route('wilayah', 'indexDesa'));
            }
        }
    }

    public function deleteKecamatan() {
        $this->requireRole('admin');

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

        if ($this->isAjaxRequest()) {
            $this->json($response);
        } else {
            $_SESSION[$response['success'] ? 'success' : 'error'] = $response['message'];
            $this->redirect(route('wilayah', 'indexKecamatan'));
        }
    }

    public function deleteDesa() {
        $this->requireRole('admin');

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

        if ($this->isAjaxRequest()) {
            $this->json($response);
        } else {
            $_SESSION[$response['success'] ? 'success' : 'error'] = $response['message'];
            $this->redirect(route('wilayah', 'indexDesa'));
        }
    }

    public function getKecamatanOptions() {
        $this->requireRole('admin');

        $options = $this->wilayahModel->getKecamatanOptions();
        $this->json([
            'success' => true,
            'data' => $options
        ]);
    }

    public function getKecamatanStats() {
        $this->requireRole('admin');

        $id_kecamatan = $_GET['id'] ?? null;

        if (!$id_kecamatan) {
            $this->json([
                'success' => false,
                'message' => 'ID kecamatan tidak valid'
            ]);
        }

        $desa = $this->wilayahModel->getRelatedDesa((int)$id_kecamatan);

        $relatedDesaList = [];
        $relatedDesaCount = count($desa);

        foreach ($desa as $row) {
            $relatedDesaList[] = "- " . htmlspecialchars($row['nama_desa']);
        }

        $this->json([
            'success' => true,
            'relatedDesaCount' => $relatedDesaCount,
            'relatedDesaList' => $relatedDesaList
        ]);
    }
}