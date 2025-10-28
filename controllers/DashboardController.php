<?php

require_once 'models/AuthModel.php';
require_once 'models/DashboardModel.php';

class DashboardController {
    private $authModel;
    private $dashboardModel;

    public function __construct() {
        $this->authModel = new AuthModel();
        $this->dashboardModel = new DashboardModel();
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
     * Require role tertentu untuk mengakses halaman
     */
    private function requireRole($requiredRole) {
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

    /**
     * Menampilkan dashboard admin
     */
    public function admin() {
        // Require login dan role admin
        $this->requireRole('admin');

        // Ambil data dashboard
        $data = $this->dashboardModel->getDashboardSummary();

        // Ambil data untuk chart
        $chartSumber = $this->dashboardModel->getChartDataSumber();
        $chartCamatTujuan = $this->dashboardModel->getChartDataCamatTujuan();
        $chartCamatKecamatan = $this->dashboardModel->getChartDataCamatKecamatan();
        $chartOPDInstansi = $this->dashboardModel->getChartDataOPDInstansi();
        $chartBulanan = $this->dashboardModel->getChartDataBulanan();

        // Passing data ke view
        $user = $this->getCurrentUser();

        require_once 'views/dashboard/admin/index.php';
    }

    /**
     * Menampilkan dashboard camat
     */
    public function camat() {
        // Require login dan role camat
        $this->requireRole('camat');

        // Ambil data user
        $user = $this->getCurrentUser();

        require_once 'views/dashboard/camat/index.php';
    }

    /**
     * Menampilkan dashboard OPD
     */
    public function opd() {
        // Require login dan role OPD
        $this->requireRole('opd');

        // Ambil data user
        $user = $this->getCurrentUser();

        require_once 'views/dashboard/opd/index.php';
    }

    /**
     * Index method - redirect sesuai role
     */
    public function index() {
        $this->requireLogin();

        $userRole = $this->getUserRole();

        switch ($userRole) {
            case 'admin':
                $this->admin();
                break;
            case 'camat':
                $this->camat();
                break;
            case 'opd':
                $this->opd();
                break;
            default:
                // Redirect ke login jika role tidak valid
                header('Location: index.php');
                exit;
        }
    }

    /**
     * API endpoint untuk dashboard data (JSON)
     */
    public function getDashboardData() {
        $this->requireRole('admin');

        header('Content-Type: application/json');

        try {
            $data = $this->dashboardModel->getDashboardSummary();
            $chartSumber = $this->dashboardModel->getChartDataSumber();
            $chartCamatTujuan = $this->dashboardModel->getChartDataCamatTujuan();
            $chartCamatKecamatan = $this->dashboardModel->getChartDataCamatKecamatan();
            $chartOPDInstansi = $this->dashboardModel->getChartDataOPDInstansi();
            $chartBulanan = $this->dashboardModel->getChartDataBulanan();

            echo json_encode([
                'success' => true,
                'data' => $data,
                'charts' => [
                    'sumber' => $chartSumber,
                    'camat_tujuan' => $chartCamatTujuan,
                    'camat_kecamatan' => $chartCamatKecamatan,
                    'opd_instansi' => $chartOPDInstansi,
                    'bulanan' => $chartBulanan
                ]
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
     * Export laporan data
     */
    public function exportLaporan() {
        $this->requireRole('admin');

        $format = $_GET['format'] ?? 'pdf';

        try {
            switch ($format) {
                case 'pdf':
                    $this->exportToPDF();
                    break;
                case 'excel':
                    $this->exportToExcel();
                    break;
                case 'csv':
                    $this->exportToCSV();
                    break;
                default:
                    throw new Exception('Format tidak valid');
            }
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error export: ' . $e->getMessage();
            header('Location: index.php?page=dashboard&role=admin');
            exit;
        }
    }

    /**
     * Export to PDF (placeholder - akan diimplementasi nanti)
     */
    private function exportToPDF() {
        // Placeholder untuk PDF export
        // Akan menggunakan library seperti TCPDF atau DomPDF
        echo "Export PDF - Coming Soon";
    }

    /**
     * Export to Excel (placeholder - akan diimplementasi nanti)
     */
    private function exportToExcel() {
        // Placeholder untuk Excel export
        // Akan menggunakan library seperti PHPSpreadsheet
        echo "Export Excel - Coming Soon";
    }

    /**
     * Export to CSV
     */
    private function exportToCSV() {
        $data = $this->dashboardModel->getTotalLaporanByStatus();

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="laporan_' . date('Y-m-d') . '.csv"');

        $output = fopen('php://output', 'w');

        // Header
        fputcsv($output, ['Status', 'Total']);

        // Data
        foreach ($data as $row) {
            fputcsv($output, [
                'Baru: ' . $row['total_baru'],
                $row['total_baru']
            ]);
            fputcsv($output, [
                'Diproses: ' . $row['total_diproses'],
                $row['total_diproses']
            ]);
            fputcsv($output, [
                'Selesai: ' . $row['total_selesai'],
                $row['total_selesai']
            ]);
            fputcsv($output, [
                'Total Semua: ' . $row['total_semua'],
                $row['total_semua']
            ]);
        }

        fclose($output);
        exit;
    }
}

// Proses request
if (isset($_GET['action'])) {
    $dashboardController = new DashboardController();

    switch ($_GET['action']) {
        case 'data':
            $dashboardController->getDashboardData();
            break;
        case 'export':
            $dashboardController->exportLaporan();
            break;
        case 'admin':
            $dashboardController->admin();
            break;
        case 'camat':
            $dashboardController->camat();
            break;
        case 'opd':
            $dashboardController->opd();
            break;
        default:
            $dashboardController->index();
            break;
    }
} else {
    $dashboardController = new DashboardController();
    $dashboardController->index();
}