<?php

require_once 'models/AuthModel.php';
require_once 'models/DashboardModel.php';

class DashboardController extends BaseController {
    private $authModel;
    private $dashboardModel;

    public function __construct() {
        $this->authModel = new AuthModel();
        $this->dashboardModel = new DashboardModel();
    }

    public function admin() {
        $this->requireRole('admin');

        $data = $this->dashboardModel->getDashboardSummary();
        $chartSumber = $this->dashboardModel->getChartDataSumber();
        $chartCamatTujuan = $this->dashboardModel->getChartDataCamatTujuan();
        $chartCamatKecamatan = $this->dashboardModel->getChartDataCamatKecamatan();
        $chartOPDInstansi = $this->dashboardModel->getChartDataOPDInstansi();
        $chartBulanan = $this->dashboardModel->getChartDataBulanan();

        $user = $this->getCurrentUser();

        require_once 'views/dashboard/admin/index.php';
    }

    public function camat() {
        $this->requireRole('camat');

        $user = $this->getCurrentUser();

        require_once 'views/dashboard/camat/index.php';
    }

    public function opd() {
        $this->requireRole('opd');

        $user = $this->getCurrentUser();

        require_once 'views/dashboard/opd/index.php';
    }

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
                $this->redirect('index.php');
        }
    }

    public function getDashboardData() {
        $this->requireRole('admin');

        try {
            $data = $this->dashboardModel->getDashboardSummary();
            $chartSumber = $this->dashboardModel->getChartDataSumber();
            $chartCamatTujuan = $this->dashboardModel->getChartDataCamatTujuan();
            $chartCamatKecamatan = $this->dashboardModel->getChartDataCamatKecamatan();
            $chartOPDInstansi = $this->dashboardModel->getChartDataOPDInstansi();
            $chartBulanan = $this->dashboardModel->getChartDataBulanan();

            $this->json([
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
            $this->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

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
            $this->redirect(route('dashboard', 'admin'));
        }
    }

    private function exportToPDF() {
        echo "Export PDF - Coming Soon";
    }

    private function exportToExcel() {
        echo "Export Excel - Coming Soon";
    }

    private function exportToCSV() {
        $data = $this->dashboardModel->getTotalLaporanByStatus();

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="laporan_' . date('Y-m-d') . '.csv"');

        $output = fopen('php://output', 'w');

        fputcsv($output, ['Status', 'Total']);

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