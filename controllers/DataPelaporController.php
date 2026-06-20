<?php

require_once 'models/DataPelaporModel.php';

class DataPelaporController extends BaseController {
    private $dataPelaporModel;

    public function __construct() {
        $this->dataPelaporModel = new DataPelaporModel();
    }

    public function index() {
        $this->requireRole('admin');

        $user = $this->getCurrentUser();
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        if (!in_array($limit, [10, 25, 50, 100])) {
            $limit = 10;
        }
        $search = $_GET['search'] ?? '';
        $role = $_GET['role'] ?? '';
        if (!in_array($role, ['camat', 'opd'])) {
            $role = '';
        }

        $result = $this->dataPelaporModel->getAllDataPelapor($page, $limit, $search, $role);
        $statistics = $this->dataPelaporModel->getPelaporStatistics();

        require_once 'views/data-pelapor/index.php';
    }

    public function form() {
        $this->requireRole('admin');

        $user = $this->getCurrentUser();
        $id = $_GET['id'] ?? null;
        $dataPelapor = null;

        if ($id) {
            $dataPelapor = $this->dataPelaporModel->getDataPelaporById($id);
            if (!$dataPelapor) {
                $_SESSION['error'] = 'Data pelapor tidak ditemukan';
                $this->redirect(route('dataPelapor', 'index'));
            }
        }

        require_once 'views/data-pelapor/form.php';
    }

    public function save() {
        $this->requireRole('admin');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $id = !empty($_POST['id']) ? (int)$_POST['id'] : null;
                $errors = $this->validatePelaporInput($_POST, $id);

                if (!empty($errors)) {
                    $this->json([
                        'success' => false,
                        'message' => implode('<br>', $errors)
                    ]);
                }

                $data = [
                    'username' => trim($_POST['username'] ?? ''),
                    'email' => trim($_POST['email'] ?? ''),
                    'jabatan' => trim($_POST['jabatan'] ?? ''),
                    'role' => $_POST['role'] ?? '',
                    'no_telp' => trim($_POST['no_telp'] ?? '')
                ];

                if (!empty($_POST['password'])) {
                    $data['password'] = $_POST['password'];
                }

                if ($id) {
                    $response = $this->dataPelaporModel->updateDataPelapor($id, $data);
                } else {
                    $response = $this->dataPelaporModel->createDataPelapor($data);
                }

                $this->json($response);

            } catch (Exception $e) {
                $this->json([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage()
                ]);
            }
        }
    }

    private function validatePelaporInput(array $data, ?int $id): array {
        $errors = [];
        $username = trim($data['username'] ?? '');
        $email = trim($data['email'] ?? '');
        $jabatan = trim($data['jabatan'] ?? '');
        $role = $data['role'] ?? '';
        $no_telp = trim($data['no_telp'] ?? '');
        $password = $data['password'] ?? '';
        $confirmPassword = $data['confirm_password'] ?? '';

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

        if (!empty($no_telp)) {
            if (!preg_match('/^(^\+62|62|^08)[0-9]{8,13}$/', $no_telp)) {
                $errors[] = 'Format nomor telepon tidak valid. Gunakan format: 08xxxxxxxxxx atau +62xxxxxxxxxx';
            }
        }

        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Format email tidak valid';
        }

        if (!empty($username) && strlen($username) < 3) {
            $errors[] = 'Username minimal 3 karakter';
        }

        if (!$id) {
            if (empty($password)) {
                $errors[] = 'Password harus diisi';
            } elseif (strlen($password) < 6) {
                $errors[] = 'Password minimal 6 karakter';
            } elseif ($password !== $confirmPassword) {
                $errors[] = 'Konfirmasi password tidak cocok';
            }
        } elseif (!empty($password)) {
            if (strlen($password) < 6) {
                $errors[] = 'Password minimal 6 karakter';
            } elseif ($password !== $confirmPassword) {
                $errors[] = 'Konfirmasi password tidak cocok';
            }
        }

        return $errors;
    }

    public function delete() {
        $this->requireRole('admin');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $id = $_POST['id'] ?? null;

                if (!$id) {
                    $this->json([
                        'success' => false,
                        'message' => 'ID pelapor tidak valid'
                    ]);
                }

                $response = $this->dataPelaporModel->deleteDataPelapor($id);
                $this->json($response);

            } catch (Exception $e) {
                $this->json([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage()
                ]);
            }
        }
    }

    public function getDataPelapor() {
        $this->requireRole('admin');

        try {
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
            $search = $_GET['search'] ?? '';
            $role = $_GET['role'] ?? '';
            if (!in_array($role, ['camat', 'opd'])) {
                $role = '';
            }

            $result = $this->dataPelaporModel->getAllDataPelapor($page, $limit, $search, $role);
            $this->json([
                'success' => true,
                'data' => $result
            ]);
        } catch (Exception $e) {
            $this->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function searchPelapor() {
        $this->requireRole('admin');

        try {
            $keyword = $_GET['q'] ?? '';

            if (strlen($keyword) < 2) {
                $this->json([
                    'success' => true,
                    'data' => []
                ]);
            }

            $data = $this->dataPelaporModel->searchDataPelapor($keyword);
            $this->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (Exception $e) {
            $this->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function getStatistics() {
        $this->requireRole('admin');

        try {
            $statistics = $this->dataPelaporModel->getPelaporStatistics();
            $this->json([
                'success' => true,
                'data' => $statistics
            ]);
        } catch (Exception $e) {
            $this->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function export() {
        $this->requireRole('admin');

        try {
            $role = $_GET['role'] ?? '';
            $data = $this->dataPelaporModel->getDataPelaporByRole($role);

            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="data_pelapor_' . date('Y-m-d') . '.csv"');

            $output = fopen('php://output', 'w');

            fputcsv($output, ['ID', 'Username', 'Email', 'No. Telepon', 'Jabatan', 'Role', 'Total Laporan', 'Tanggal Dibuat']);

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
            $this->redirect(route('dataPelapor', 'index'));
        }
    }
}