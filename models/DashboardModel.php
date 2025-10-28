<?php

require_once __DIR__ . '/../config/koneksi.php';

class DashboardModel {
    private $db;

    public function __construct() {
        $this->db = getKoneksi();
    }

    /**
     * Get total laporan berdasarkan status (semua laporan)
     */
    public function getTotalLaporanByStatus() {
        // Get laporan camat statistics
        $queryCam = "SELECT
                        SUM(CASE WHEN status_laporan = 'baru' THEN 1 ELSE 0 END) as total_baru,
                        SUM(CASE WHEN status_laporan = 'diproses' THEN 1 ELSE 0 END) as total_diproses,
                        SUM(CASE WHEN status_laporan = 'selesai' THEN 1 ELSE 0 END) as total_selesai,
                        COUNT(*) as total_camat
                      FROM laporan_camat";

        $resultCam = query($queryCam);
        $dataCam = $resultCam->fetch_assoc();

        // Get laporan OPD statistics
        $queryOPD = "SELECT
                       SUM(CASE WHEN status_laporan = 'baru' THEN 1 ELSE 0 END) as total_baru,
                       SUM(CASE WHEN status_laporan = 'diproses' THEN 1 ELSE 0 END) as total_diproses,
                       SUM(CASE WHEN status_laporan = 'selesai' THEN 1 ELSE 0 END) as total_selesai,
                       COUNT(*) as total_opd
                     FROM laporan_opd";

        $resultOPD = query($queryOPD);
        $dataOPD = $resultOPD->fetch_assoc();

        // Combine the results
        return [
            'total_baru' => ($dataCam['total_baru'] ?? 0) + ($dataOPD['total_baru'] ?? 0),
            'total_diproses' => ($dataCam['total_diproses'] ?? 0) + ($dataOPD['total_diproses'] ?? 0),
            'total_selesai' => ($dataCam['total_selesai'] ?? 0) + ($dataOPD['total_selesai'] ?? 0),
            'total_semua' => ($dataCam['total_camat'] ?? 0) + ($dataOPD['total_opd'] ?? 0),
            'total_camat' => $dataCam['total_camat'] ?? 0,
            'total_opd' => $dataOPD['total_opd'] ?? 0
        ];
    }

    /**
     * Get total laporan camat berdasarkan status
     */
    public function getTotalLaporanCamatByStatus() {
        $query = "SELECT
                    COUNT(CASE WHEN status_laporan = 'baru' THEN 1 END) as total_baru,
                    COUNT(CASE WHEN status_laporan = 'diproses' THEN 1 END) as total_diproses,
                    COUNT(CASE WHEN status_laporan = 'selesai' THEN 1 END) as total_selesai,
                    COUNT(*) as total_semua
                  FROM laporan_camat";

        $result = query($query);
        return $result->fetch_assoc();
    }

    /**
     * Get total laporan OPD berdasarkan status
     */
    public function getTotalLaporanOPDByStatus() {
        $query = "SELECT
                    COUNT(CASE WHEN status_laporan = 'baru' THEN 1 END) as total_baru,
                    COUNT(CASE WHEN status_laporan = 'diproses' THEN 1 END) as total_diproses,
                    COUNT(CASE WHEN status_laporan = 'selesai' THEN 1 END) as total_selesai,
                    COUNT(*) as total_semua
                  FROM laporan_opd";

        $result = query($query);
        return $result->fetch_assoc();
    }

    /**
     * Get total laporan camat berdasarkan tujuan
     */
    public function getTotalLaporanCamatByTujuan() {
        $query = "SELECT
                    tujuan,
                    COUNT(*) as total
                  FROM laporan_camat
                  GROUP BY tujuan
                  ORDER BY total DESC";

        $result = query($query);
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }

    /**
     * Get total laporan camat berdasarkan kecamatan
     */
    public function getTotalLaporanCamatByKecamatan() {
        $query = "SELECT
                    nama_kecamatan,
                    COUNT(*) as total
                  FROM laporan_camat
                  GROUP BY nama_kecamatan
                  ORDER BY total DESC";

        $result = query($query);
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }

    /**
     * Get total laporan OPD berdasarkan nama OPD
     */
    public function getTotalLaporanOPDByInstansi() {
        $query = "SELECT
                    nama_opd,
                    COUNT(*) as total
                  FROM laporan_opd
                  GROUP BY nama_opd
                  ORDER BY total DESC";

        $result = query($query);
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }

    /**
     * Get total laporan berdasarkan sumber (camat/opd)
     */
    public function getTotalLaporanBySumber() {
        $query = "SELECT
                    'Laporan Camat' as jenis,
                    COUNT(*) as total
                  FROM laporan_camat
                  UNION ALL
                  SELECT
                    'Laporan OPD' as jenis,
                    COUNT(*) as total
                  FROM laporan_opd";

        $result = query($query);
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }

    /**
     * Get laporan camat terbaru
     */
    public function getLaporanCamatTerbaru($limit = 5) {
        $query = "SELECT
                    lc.id_laporan_camat,
                    lc.nama_pelapor,
                    lc.nama_desa,
                    lc.nama_kecamatan,
                    lc.waktu_kejadian,
                    lc.tujuan,
                    lc.status_laporan,
                    lc.created_at,
                    'camat' as sumber
                  FROM laporan_camat lc
                  ORDER BY lc.created_at DESC
                  LIMIT $limit";

        $result = query($query);
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }

    /**
     * Get laporan OPD terbaru
     */
    public function getLaporanOPDTerbaru($limit = 5) {
        $query = "SELECT
                    lo.id_laporan_opd,
                    lo.nama_opd,
                    lo.nama_kegiatan,
                    lo.tujuan,
                    lo.status_laporan,
                    lo.created_at,
                    'opd' as sumber
                  FROM laporan_opd lo
                  ORDER BY lo.created_at DESC
                  LIMIT $limit";

        $result = query($query);
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }

    /**
     * Get semua laporan terbaru (gabungan camat dan OPD)
     */
    public function getLaporanTerbaru($limit = 10) {
        $camatData = $this->getLaporanCamatTerbaru($limit);
        $opdData = $this->getLaporanOPDTerbaru($limit);

        // Gabungkan dan sort berdasarkan created_at
        $allData = array_merge($camatData, $opdData);

        // Sort berdasarkan created_at descending
        usort($allData, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });

        return array_slice($allData, 0, $limit);
    }

    /**
     * Get statistik laporan camat per bulan (6 bulan terakhir)
     */
    public function getStatistikCamatPerBulan() {
        $query = "SELECT
                    DATE_FORMAT(created_at, '%Y-%m') as bulan,
                    DATE_FORMAT(created_at, '%M %Y') as bulan_nama,
                    COUNT(*) as total
                  FROM laporan_camat
                  WHERE created_at >= DATE_SUB(CURRENT_DATE, INTERVAL 6 MONTH)
                  GROUP BY DATE_FORMAT(created_at, '%Y-%m'), DATE_FORMAT(created_at, '%M %Y')
                  ORDER BY bulan ASC";

        $result = query($query);
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }

    /**
     * Get statistik laporan OPD per bulan (6 bulan terakhir)
     */
    public function getStatistikOPDPerBulan() {
        $query = "SELECT
                    DATE_FORMAT(created_at, '%Y-%m') as bulan,
                    DATE_FORMAT(created_at, '%M %Y') as bulan_nama,
                    COUNT(*) as total
                  FROM laporan_opd
                  WHERE created_at >= DATE_SUB(CURRENT_DATE, INTERVAL 6 MONTH)
                  GROUP BY DATE_FORMAT(created_at, '%Y-%m'), DATE_FORMAT(created_at, '%M %Y')
                  ORDER BY bulan ASC";

        $result = query($query);
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }

    /**
     * Get statistik laporan gabungan per bulan
     */
    public function getStatistikPerBulan() {
        $camatData = $this->getStatistikCamatPerBulan();
        $opdData = $this->getStatistikOPDPerBulan();

        // Gabungkan data bulanan
        $monthlyData = [];

        // Process camat data
        foreach ($camatData as $row) {
            $bulan = $row['bulan'];
            if (!isset($monthlyData[$bulan])) {
                $monthlyData[$bulan] = [
                    'bulan' => $bulan,
                    'bulan_nama' => $row['bulan_nama'],
                    'total_camat' => 0,
                    'total_opd' => 0,
                    'total' => 0
                ];
            }
            $monthlyData[$bulan]['total_camat'] = $row['total'];
            $monthlyData[$bulan]['total'] += $row['total'];
        }

        // Process OPD data
        foreach ($opdData as $row) {
            $bulan = $row['bulan'];
            if (!isset($monthlyData[$bulan])) {
                $monthlyData[$bulan] = [
                    'bulan' => $bulan,
                    'bulan_nama' => $row['bulan_nama'],
                    'total_camat' => 0,
                    'total_opd' => 0,
                    'total' => 0
                ];
            }
            $monthlyData[$bulan]['total_opd'] = $row['total'];
            $monthlyData[$bulan]['total'] += $row['total'];
        }

        // Sort by bulan
        ksort($monthlyData);

        return array_values($monthlyData);
    }

    /**
     * Get total users berdasarkan role
     */
    public function getTotalUsersByRole() {
        $query = "SELECT
                    role,
                    COUNT(*) as total
                  FROM users
                  GROUP BY role";

        $result = query($query);
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }

    /**
     * Get laporan camat yang perlu ditindak lanjuti (status baru)
     */
    public function getLaporanCamatPerluTindakan($limit = 5) {
        $query = "SELECT
                    lc.id_laporan_camat,
                    lc.nama_pelapor,
                    lc.nama_desa,
                    lc.nama_kecamatan,
                    lc.waktu_kejadian,
                    lc.tujuan,
                    lc.created_at,
                    'camat' as sumber
                  FROM laporan_camat lc
                  WHERE lc.status_laporan = 'baru'
                  ORDER BY lc.created_at ASC
                  LIMIT $limit";

        $result = query($query);
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }

    /**
     * Get laporan OPD yang perlu ditindak lanjuti (status baru)
     */
    public function getLaporanOPDPerluTindakan($limit = 5) {
        $query = "SELECT
                    lo.id_laporan_opd,
                    lo.nama_opd,
                    lo.nama_kegiatan,
                    lo.created_at,
                    'opd' as sumber
                  FROM laporan_opd lo
                  WHERE lo.status_laporan = 'baru'
                  ORDER BY lo.created_at ASC
                  LIMIT $limit";

        $result = query($query);
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }

    /**
     * Get semua laporan yang perlu ditindak lanjuti (gabungan camat dan OPD)
     */
    public function getLaporanPerluTindakan($limit = 10) {
        $camatData = $this->getLaporanCamatPerluTindakan($limit);
        $opdData = $this->getLaporanOPDPerluTindakan($limit);

        // Gabungkan dan sort berdasarkan created_at ascending
        $allData = array_merge($camatData, $opdData);

        // Sort berdasarkan created_at ascending (yang paling lama duluan)
        usort($allData, function($a, $b) {
            return strtotime($a['created_at']) - strtotime($b['created_at']);
        });

        return array_slice($allData, 0, $limit);
    }

    /**
     * Get summary data untuk dashboard
     */
    public function getDashboardSummary() {
        $summary = [];

        // Total laporan by status (gabungan camat dan OPD)
        $summary['laporan_by_status'] = $this->getTotalLaporanByStatus();

        // Total laporan camat by status
        $summary['laporan_camat_by_status'] = $this->getTotalLaporanCamatByStatus();

        // Total laporan OPD by status
        $summary['laporan_opd_by_status'] = $this->getTotalLaporanOPDByStatus();

        // Total laporan by sumber (camat/opd)
        $summary['laporan_by_sumber'] = $this->getTotalLaporanBySumber();

        // Total laporan camat by tujuan
        $summary['laporan_camat_by_tujuan'] = $this->getTotalLaporanCamatByTujuan();

        // Total laporan camat by kecamatan
        $summary['laporan_camat_by_kecamatan'] = $this->getTotalLaporanCamatByKecamatan();

        // Total laporan OPD by instansi
        $summary['laporan_opd_by_instansi'] = $this->getTotalLaporanOPDByInstansi();

        // Total users by role
        $summary['users_by_role'] = $this->getTotalUsersByRole();

        // Statistik per bulan
        $summary['statistik_per_bulan'] = $this->getStatistikPerBulan();

        // Laporan terbaru
        $summary['laporan_terbaru'] = $this->getLaporanTerbaru();

        // Laporan perlu tindakan
        $summary['laporan_perlu_tindakan'] = $this->getLaporanPerluTindakan();

        return $summary;
    }

    /**
     * Get data untuk chart laporan camat per tujuan
     */
    public function getChartDataCamatTujuan() {
        $query = "SELECT
                    tujuan,
                    COUNT(*) as total
                  FROM laporan_camat
                  GROUP BY tujuan
                  ORDER BY total DESC
                  LIMIT 5";

        $result = query($query);
        $labels = [];
        $data = [];

        while ($row = $result->fetch_assoc()) {
            $labels[] = ucfirst($row['tujuan']);
            $data[] = (int)$row['total'];
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    /**
     * Get data untuk chart laporan per sumber (camat/opd)
     */
    public function getChartDataSumber() {
        $data = $this->getTotalLaporanBySumber();

        $labels = [];
        $chartData = [];

        foreach ($data as $row) {
            $labels[] = $row['jenis'];
            $chartData[] = (int)$row['total'];
        }

        return [
            'labels' => $labels,
            'data' => $chartData
        ];
    }

    /**
     * Get data untuk chart laporan camat per kecamatan
     */
    public function getChartDataCamatKecamatan() {
        $query = "SELECT
                    nama_kecamatan,
                    COUNT(*) as total
                  FROM laporan_camat
                  GROUP BY nama_kecamatan
                  ORDER BY total DESC
                  LIMIT 5";

        $result = query($query);
        $labels = [];
        $data = [];

        while ($row = $result->fetch_assoc()) {
            $labels[] = $row['nama_kecamatan'];
            $data[] = (int)$row['total'];
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    /**
     * Get data untuk chart laporan OPD per instansi
     */
    public function getChartDataOPDInstansi() {
        $query = "SELECT
                    nama_opd,
                    COUNT(*) as total
                  FROM laporan_opd
                  GROUP BY nama_opd
                  ORDER BY total DESC
                  LIMIT 5";

        $result = query($query);
        $labels = [];
        $data = [];

        while ($row = $result->fetch_assoc()) {
            $labels[] = $row['nama_opd'];
            $data[] = (int)$row['total'];
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    /**
     * Get data untuk chart statistik bulanan
     */
    public function getChartDataBulanan() {
        $data = $this->getStatistikPerBulan();

        $labels = [];
        $chartData = [];

        foreach ($data as $row) {
            $labels[] = $row['bulan_nama'];
            $chartData[] = (int)$row['total'];
        }

        return [
            'labels' => $labels,
            'data' => $chartData
        ];
    }
}