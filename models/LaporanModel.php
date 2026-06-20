<?php

require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/BaseModel.php';

class LaporanModel extends BaseModel {
    
    public function getLaporanOPD($page = 1, $limit = 10, $search = '', $status = '') {
        $offset = ($page - 1) * $limit;

        
        $whereClause = "";
        $params = [];

        if (!empty($search)) {
            $search = escapeString($search);
            $whereClause .= "WHERE (lo.nama_opd LIKE '%$search%' OR lo.nama_kegiatan LIKE '%$search%')";
        }

        if (!empty($status)) {
            $status = escapeString($status);
            if (!empty($whereClause)) {
                $whereClause .= " AND lo.status_laporan = '$status'";
            } else {
                $whereClause .= "WHERE lo.status_laporan = '$status'";
            }
        }

        
        $query = "SELECT lo.*, u.username, '' as nama_kegiatan
                  FROM laporan_opd lo
                  LEFT JOIN users u ON lo.id_user = u.id_user
                  $whereClause
                  ORDER BY lo.created_at DESC
                  LIMIT $limit OFFSET $offset";

        $result = $this->db->query($query);
        $data = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }

        
        $countQuery = "SELECT COUNT(*) as total FROM laporan_opd lo
                       LEFT JOIN users u ON lo.id_user = u.id_user
                       $whereClause";

        $countResult = $this->db->query($countQuery);
        $total = 0;
        if ($countResult) {
            $totalRow = $countResult->fetch_assoc();
            $total = $totalRow['total'];
        }

        return [
            'data' => $data,
            'total' => $total,
            'per_page' => $limit,
            'current_page' => $page,
            'total_pages' => $total > 0 ? ceil($total / $limit) : 1
        ];
    }

    
    public function getLaporanCamat($page = 1, $limit = 10, $search = '', $status = '', $tujuan = '') {
        $offset = ($page - 1) * $limit;

        
        $whereClause = "";
        $params = [];

        if (!empty($search)) {
            $search = escapeString($search);
            $whereClause .= "WHERE (lc.nama_kecamatan LIKE '%$search%' OR lc.nama_pelapor LIKE '%$search%' OR lc.uraian_laporan LIKE '%$search%')";
        }

        if (!empty($status)) {
            $status = escapeString($status);
            if (!empty($whereClause)) {
                $whereClause .= " AND lc.status_laporan = '$status'";
            } else {
                $whereClause .= "WHERE lc.status_laporan = '$status'";
            }
        }

        if (!empty($tujuan)) {
            $tujuan = escapeString($tujuan);
            if (!empty($whereClause)) {
                $whereClause .= " AND lc.tujuan = '$tujuan'";
            } else {
                $whereClause .= "WHERE lc.tujuan = '$tujuan'";
            }
        }

        
        $query = "SELECT lc.*, u.username, '' as nama_kegiatan
                  FROM laporan_camat lc
                  LEFT JOIN users u ON lc.id_user = u.id_user
                  $whereClause
                  ORDER BY lc.created_at DESC
                  LIMIT $limit OFFSET $offset";

        $result = $this->db->query($query);
        $data = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }

        
        $countQuery = "SELECT COUNT(*) as total FROM laporan_camat lc
                       LEFT JOIN users u ON lc.id_user = u.id_user
                       $whereClause";

        $countResult = $this->db->query($countQuery);
        $total = 0;
        if ($countResult) {
            $totalRow = $countResult->fetch_assoc();
            $total = $totalRow['total'];
        }

        return [
            'data' => $data,
            'total' => $total,
            'per_page' => $limit,
            'current_page' => $page,
            'total_pages' => $total > 0 ? ceil($total / $limit) : 1
        ];
    }

    
    public function getOPDStatistics() {
        
        $query = "SELECT COUNT(*) as total_laporan FROM laporan_opd";
        $result = $this->db->query($query);
        $total = $result ? $result->fetch_assoc() : ['total_laporan' => 0];

        
        $query = "SELECT status_laporan, COUNT(*) as total
                  FROM laporan_opd
                  GROUP BY status_laporan";
        $result = $this->db->query($query);
        $byStatus = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $byStatus[] = $row;
            }
        }

        return [
            'total' => $total,
            'by_status' => $byStatus
        ];
    }

    
    public function getCamatStatistics() {
        
        $query = "SELECT COUNT(*) as total_laporan FROM laporan_camat";
        $result = $this->db->query($query);
        $total = $result ? $result->fetch_assoc() : ['total_laporan' => 0];

        
        $query = "SELECT status_laporan, COUNT(*) as total
                  FROM laporan_camat
                  GROUP BY status_laporan";
        $result = $this->db->query($query);
        $byStatus = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $byStatus[] = $row;
            }
        }

        return [
            'total' => $total,
            'by_status' => $byStatus
        ];
    }

    
    public function getCamatTujuanOptions() {
        
        return [
            'Bupati',
            'Wakil Bupati',
            'Sekda',
            'OPD'
        ];
    }

    
    public function getLaporanOPDForPDF($hari = '', $bulan = '', $tahun = '', $status = '') {
        $whereClause = "";

        if (!empty($hari)) {
            $hari = escapeString($hari);
            if (!empty($whereClause)) {
                $whereClause .= " AND DAYNAME(lo.created_at) = '$hari'";
            } else {
                $whereClause .= "WHERE DAYNAME(lo.created_at) = '$hari'";
            }
        }

        if (!empty($bulan)) {
            $bulan = escapeString($bulan);
            if (!empty($whereClause)) {
                $whereClause .= " AND MONTH(lo.created_at) = '$bulan'";
            } else {
                $whereClause .= "WHERE MONTH(lo.created_at) = '$bulan'";
            }
        }

        if (!empty($tahun)) {
            $tahun = escapeString($tahun);
            if (!empty($whereClause)) {
                $whereClause .= " AND YEAR(lo.created_at) = '$tahun'";
            } else {
                $whereClause .= "WHERE YEAR(lo.created_at) = '$tahun'";
            }
        }

        if (!empty($status)) {
            $status = escapeString($status);
            if (!empty($whereClause)) {
                $whereClause .= " AND lo.status_laporan = '$status'";
            } else {
                $whereClause .= "WHERE lo.status_laporan = '$status'";
            }
        }

        $query = "SELECT lo.*, u.username
                  FROM laporan_opd lo
                  LEFT JOIN users u ON lo.id_user = u.id_user
                  $whereClause
                  ORDER BY lo.created_at DESC";

        $result = $this->db->query($query);
        $data = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }

        return $data;
    }

    
    public function getLaporanOPDForExcel($hari = '', $bulan = '', $tahun = '', $status = '') {
        return $this->getLaporanOPDForPDF($hari, $bulan, $tahun, $status);
    }

    
    public function getLaporanCamatForPDF($hari = '', $bulan = '', $tahun = '', $status = '', $tujuan = '') {
        $whereClause = "";

        if (!empty($hari)) {
            $hari = escapeString($hari);
            if (!empty($whereClause)) {
                $whereClause .= " AND DAYNAME(lc.created_at) = '$hari'";
            } else {
                $whereClause .= "WHERE DAYNAME(lc.created_at) = '$hari'";
            }
        }

        if (!empty($bulan)) {
            $bulan = escapeString($bulan);
            if (!empty($whereClause)) {
                $whereClause .= " AND MONTH(lc.created_at) = '$bulan'";
            } else {
                $whereClause .= "WHERE MONTH(lc.created_at) = '$bulan'";
            }
        }

        if (!empty($tahun)) {
            $tahun = escapeString($tahun);
            if (!empty($whereClause)) {
                $whereClause .= " AND YEAR(lc.created_at) = '$tahun'";
            } else {
                $whereClause .= "WHERE YEAR(lc.created_at) = '$tahun'";
            }
        }

        if (!empty($status)) {
            $status = escapeString($status);
            if (!empty($whereClause)) {
                $whereClause .= " AND lc.status_laporan = '$status'";
            } else {
                $whereClause .= "WHERE lc.status_laporan = '$status'";
            }
        }

        if (!empty($tujuan)) {
            $tujuan = escapeString($tujuan);
            if (!empty($whereClause)) {
                $whereClause .= " AND lc.tujuan = '$tujuan'";
            } else {
                $whereClause .= "WHERE lc.tujuan = '$tujuan'";
            }
        }

        $query = "SELECT lc.*, u.username
                  FROM laporan_camat lc
                  LEFT JOIN users u ON lc.id_user = u.id_user
                  $whereClause
                  ORDER BY lc.created_at DESC";

        $result = $this->db->query($query);
        $data = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }

        return $data;
    }

    
    public function getLaporanCamatForExcel($hari = '', $bulan = '', $tahun = '', $status = '', $tujuan = '') {
        return $this->getLaporanCamatForPDF($hari, $bulan, $tahun, $status, $tujuan);
    }

    
    public function getAvailableYears() {
        $query = "SELECT DISTINCT YEAR(created_at) as year
                  FROM (
                      SELECT created_at FROM laporan_camat
                      UNION
                      SELECT created_at FROM laporan_opd
                  ) as combined
                  ORDER BY year DESC";

        $result = $this->db->query($query);
        $years = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $years[] = $row['year'];
            }
        }

        return $years;
    }

    
    public function getAvailableDays() {
        return [
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
            'Sunday' => 'Minggu'
        ];
    }

    
    public function getAvailableMonths() {
        return [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];
    }

    
    public function getLaporanCamatById($id) {
        $id = (int)$id;
        $query = "SELECT lc.*, d.nama_desa, k.nama_kecamatan
                  FROM laporan_camat lc
                  LEFT JOIN desa d ON lc.id_desa = d.id_desa
                  LEFT JOIN kecamatan k ON lc.id_kecamatan = k.id_kecamatan
                  WHERE lc.id_laporan_camat = $id";
        $result = $this->db->query($query);

        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        return null;
    }

    
    public function getLaporanOPDById($id) {
        $id = (int)$id;
        $query = "SELECT lo.*, u.username, u.pangkat
                  FROM laporan_opd lo
                  LEFT JOIN users u ON lo.id_user = u.id_user
                  WHERE lo.id_laporan_opd = $id";
        $result = $this->db->query($query);

        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        return null;
    }

    
    public function saveSignature($data) {
        $jabatan_penanda_tangan = escapeString($data['jabatan_penanda_tangan']);
        $nama_penanda_tangan = escapeString($data['nama_penanda_tangan']);
        $pangkat = escapeString($data['pangkat'] ?? '');
        $nip = escapeString($data['nip'] ?? '');

        
        $checkQuery = "SELECT id_ttd_laporan FROM ttd_laporan LIMIT 1";
        $checkResult = $this->db->query($checkQuery);

        if ($checkResult && $checkResult->num_rows > 0) {
            
            $id_ttd = $checkResult->fetch_assoc()['id_ttd_laporan'];
            $query = "UPDATE ttd_laporan SET
                      jabatan_penanda_tangan = '$jabatan_penanda_tangan',
                      nama_penanda_tangan = '$nama_penanda_tangan',
                      pangkat = '$pangkat',
                      nip = '$nip'
                      WHERE id_ttd_laporan = $id_ttd";
        } else {
            
            $query = "INSERT INTO ttd_laporan
                      (jabatan_penanda_tangan, nama_penanda_tangan, pangkat, nip)
                      VALUES
                      ('$jabatan_penanda_tangan', '$nama_penanda_tangan', '$pangkat', '$nip')";
        }

        $result = $this->db->query($query);

        if ($result) {
            return [
                'success' => true,
                'message' => 'Tanda tangan berhasil disimpan'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Gagal menyimpan tanda tangan: ' . $this->db->error
            ];
        }
    }

    
    public function getSignature($id_laporan = null, $type = null) {
        
        $query = "SELECT * FROM ttd_laporan ORDER BY id_ttd_laporan DESC LIMIT 1";
        $result = $this->db->query($query);

        if ($result && $result->num_rows > 0) {
            $signature = $result->fetch_assoc();

            
            $signature['tempat'] = 'Panyabungan';
            $signature['tanggal_format'] = $this->formatTanggalIndo(date('Y-m-d'));

            
            $signature['nama_penandatangan'] = $signature['nama_penanda_tangan'];
            $signature['jabatan_penanda_tangan'] = $signature['jabatan_penanda_tangan'];

            return $signature;
        }
        return null;
    }

    
    public function createTTDTable() {
        $query = "CREATE TABLE IF NOT EXISTS ttd_laporan (
            id_ttd_laporan INT AUTO_INCREMENT PRIMARY KEY,
            jabatan_penanda_tangan VARCHAR(100) NOT NULL,
            nama_penanda_tangan VARCHAR(100) NOT NULL,
            pangkat VARCHAR(100) DEFAULT NULL,
            nip VARCHAR(50) DEFAULT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        try {
            return $this->db->query($query);
        } catch (\mysqli_sql_exception $e) {
            
            error_log("createTTDTable skipped: " . $e->getMessage());
            return false;
        }
    }

    
    public function getDefaultSignature($type = null) {
        
        $query = "SELECT * FROM ttd_laporan ORDER BY id_ttd_laporan DESC LIMIT 1";
        $result = $this->db->query($query);

        if ($result && $result->num_rows > 0) {
            $signature = $result->fetch_assoc();

            
            $signature['tempat'] = 'Panyabungan';
            $signature['tanggal_format'] = $this->formatTanggalIndo(date('Y-m-d'));

            
            $signature['nama_penandatangan'] = $signature['nama_penanda_tangan'];
            $signature['jabatan_penanda_tangan'] = $signature['jabatan_penanda_tangan'];
            

            return $signature;
        }

        
        return null;
    }

    
    private function formatTanggalIndo($tanggal) {
        $hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                  'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        $timestamp = strtotime($tanggal);
        $nama_hari = $hari[date('w', $timestamp)];
        $tanggal_num = date('d', $timestamp);
        $nama_bulan = $bulan[date('n', $timestamp) - 1];
        $tahun = date('Y', $timestamp);

        return "$nama_hari, $tanggal_num $nama_bulan $tahun";
    }
}