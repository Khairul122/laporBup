<?php

require_once __DIR__ . '/../config/koneksi.php';

class LaporanCamatAdminModel {
    private $db;

    public function __construct() {
        $this->db = getKoneksi();
    }

    /**
     * Get all laporan camat with pagination and filtering
     */
    public function getAllLaporanCamat($page = 1, $limit = 10, $search = '', $status = '', $tujuan = '') {
        $offset = ($page - 1) * $limit;

        // Build WHERE conditions
        $whereClause = "";
        $params = [];

        if (!empty($search)) {
            $search = escapeString($search);
            $whereClause .= "WHERE (lc.nama_kecamatan LIKE '%$search%' OR lc.nama_kegiatan LIKE '%$search%')";
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

        // Main query to get data
        $query = "SELECT lc.*, u.username, u.jabatan, '' as nama_kegiatan
                  FROM laporan_camat lc
                  LEFT JOIN users u ON lc.id_user = u.id_user
                  $whereClause
                  ORDER BY lc.created_at DESC
                  LIMIT $limit OFFSET $offset";

    
        $result = $this->db->query($query);

        // Check for query errors
        if (!$result) {
            error_log("LaporanCamatModel - MySQL error: " . $this->db->errno . " - " . $this->db->error);
            return [
                'data' => [],
                'total' => 0,
                'per_page' => $limit,
                'current_page' => $page,
                'total_pages' => 0,
                'error' => true
            ];
        }

        $data = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }

        
        // Get total count for pagination
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

    /**
     * Get single laporan camat by ID
     */
    public function getLaporanCamatById($id) {
        $id = (int)$id;
        $query = "SELECT lc.*, u.username, u.jabatan, '' as nama_kegiatan
                  FROM laporan_camat lc
                  LEFT JOIN users u ON lc.id_user = u.id_user
                  WHERE lc.id_laporan_camat = $id";

        $result = $this->db->query($query);
        return $result ? $result->fetch_assoc() : null;
    }

    /**
     * Create new laporan camat
     */
    public function createLaporanCamat($data) {
        $nama_kecamatan = escapeString($data['nama_kecamatan']);
        $nama_kegiatan = escapeString($data['nama_kegiatan']);
        $uraian_laporan = escapeString($data['uraian_laporan']);
        $tujuan = escapeString($data['tujuan']);
        $status_laporan = escapeString($data['status_laporan'] ?? 'baru');
        $id_user = (int)$data['id_user'];
        $upload_file = isset($data['upload_file']) ? escapeString($data['upload_file']) : 'NULL';

        $query = "INSERT INTO laporan_camat (nama_kecamatan, nama_kegiatan, uraian_laporan, tujuan, status_laporan, id_user, upload_file)
                  VALUES ('$nama_kecamatan', '$nama_kegiatan', '$uraian_laporan', '$tujuan', '$status_laporan', $id_user, " .
                  ($upload_file !== 'NULL' ? "'$upload_file'" : 'NULL') . ")";

        $result = $this->db->query($query);

        if ($result) {
            return [
                'success' => true,
                'message' => 'Laporan camat berhasil ditambahkan',
                'id' => $this->db->insert_id
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Gagal menambahkan laporan camat'
            ];
        }
    }

    /**
     * Update laporan camat
     */
    public function updateLaporanCamat($id, $data) {
        $id = (int)$id;
        $setClause = [];

        foreach ($data as $key => $value) {
            if ($key !== 'id_laporan_camat' && $key !== 'created_at') {
                $escapedValue = escapeString($value);
                if ($value === null || $value === 'NULL') {
                    $setClause[] = "$key = NULL";
                } else {
                    $setClause[] = "$key = '$escapedValue'";
                }
            }
        }

        if (empty($setClause)) {
            return [
                'success' => false,
                'message' => 'Tidak ada data yang diperbarui'
            ];
        }

        $setClause[] = "updated_at = CURRENT_TIMESTAMP";
        $query = "UPDATE laporan_camat SET " . implode(", ", $setClause) . " WHERE id_laporan_camat = $id";

        $result = $this->db->query($query);

        if ($result) {
            return [
                'success' => true,
                'message' => 'Laporan camat berhasil diperbarui'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Gagal memperbarui laporan camat'
            ];
        }
    }

    /**
     * Delete laporan camat
     */
    public function deleteLaporanCamat($id) {
        $id = (int)$id;

        // Get file path before deleting
        $laporan = $this->getLaporanCamatById($id);

        $query = "DELETE FROM laporan_camat WHERE id_laporan_camat = $id";
        $result = $this->db->query($query);

        if ($result) {
            // Delete file if exists
            if ($laporan && $laporan['upload_file']) {
                $filePath = __DIR__ . '/../' . $laporan['upload_file'];
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            return [
                'success' => true,
                'message' => 'Laporan camat berhasil dihapus'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Gagal menghapus laporan camat'
            ];
        }
    }

    /**
     * Update status laporan
     */
    public function updateStatus($id, $status) {
        $id = (int)$id;
        $status = escapeString($status);

        $query = "UPDATE laporan_camat SET status_laporan = '$status', updated_at = CURRENT_TIMESTAMP WHERE id_laporan_camat = $id";
        $result = $this->db->query($query);

        if ($result) {
            return [
                'success' => true,
                'message' => 'Status laporan berhasil diperbarui'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Gagal memperbarui status laporan'
            ];
        }
    }

    /**
     * Get statistics for dashboard
     */
    public function getLaporanCamatStatistics() {
        // Total laporan
        $query = "SELECT COUNT(*) as total_laporan FROM laporan_camat";
        $result = $this->db->query($query);
        $total = $result ? $result->fetch_assoc() : ['total_laporan' => 0];

        // Statistik by status
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

    /**
     * Get recent laporan camat for dashboard
     */
    public function getRecentLaporanCamat($limit = 5) {
        $limit = (int)$limit;
        $query = "SELECT lc.*, u.username, u.jabatan, '' as nama_kegiatan
                  FROM laporan_camat lc
                  LEFT JOIN users u ON lc.id_user = u.id_user
                  ORDER BY lc.created_at DESC
                  LIMIT $limit";

        $result = $this->db->query($query);
        $data = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }

    /**
     * Search laporan camat
     */
    public function searchLaporanCamat($keyword, $limit = 50) {
        $keyword = escapeString($keyword);
        $limit = (int)$limit;

        $query = "SELECT lc.*, u.username, u.jabatan, '' as nama_kegiatan
                  FROM laporan_camat lc
                  LEFT JOIN users u ON lc.id_user = u.id_user
                  WHERE lc.nama_kecamatan LIKE '%$keyword%' OR lc.uraian_laporan LIKE '%$keyword%'
                  ORDER BY lc.created_at DESC
                  LIMIT $limit";

        $result = $this->db->query($query);
        $data = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }

    /**
     * Get laporan camat by date range
     */
    public function getLaporanCamatByDateRange($startDate, $endDate, $status = '') {
        $startDate = escapeString($startDate);
        $endDate = escapeString($endDate);

        $whereClause = "WHERE DATE(lc.created_at) BETWEEN '$startDate' AND '$endDate'";

        if (!empty($status)) {
            $status = escapeString($status);
            $whereClause .= " AND lc.status_laporan = '$status'";
        }

        $query = "SELECT lc.*, u.username, u.jabatan, '' as nama_kegiatan
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

    /**
     * Export laporan camat (all data)
     */
    public function exportLaporanCamat() {
        $query = "SELECT lc.*, u.username, u.jabatan, '' as nama_kegiatan
                  FROM laporan_camat lc
                  LEFT JOIN users u ON lc.id_user = u.id_user
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

    /**
     * Get monthly statistics for the current year
     */
    public function getMonthlyStatistics() {
        $query = "SELECT
                    MONTH(created_at) as month,
                    COUNT(*) as total,
                    SUM(CASE WHEN status_laporan = 'selesai' THEN 1 ELSE 0 END) as completed
                  FROM laporan_camat
                  WHERE YEAR(created_at) = YEAR(CURRENT_DATE)
                  GROUP BY MONTH(created_at)
                  ORDER BY month";

        $result = $this->db->query($query);
        $data = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }

    /**
     * Get laporan camat by user
     */
    public function getLaporanCamatByUser($userId, $limit = 10, $offset = 0) {
        $userId = (int)$userId;
        $limit = (int)$limit;
        $offset = (int)$offset;

        $query = "SELECT lc.*, u.username, u.jabatan, '' as nama_kegiatan
                  FROM laporan_camat lc
                  LEFT JOIN users u ON lc.id_user = u.id_user
                  WHERE lc.id_user = $userId
                  ORDER BY lc.created_at DESC
                  LIMIT $limit OFFSET $offset";

        $result = $this->db->query($query);
        $data = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }

    /**
     * Get count of laporan camat by user
     */
    public function getCountLaporanCamatByUser($userId) {
        $userId = (int)$userId;
        $query = "SELECT COUNT(*) as total FROM laporan_camat WHERE id_user = $userId";
        $result = $this->db->query($query);
        return $result ? $result->fetch_assoc()['total'] : 0;
    }

    /**
     * Get laporan camat statistics by kecamatan
     */
    public function getStatisticsByKecamatan() {
        $query = "SELECT
                    nama_kecamatan,
                    COUNT(*) as total,
                    SUM(CASE WHEN status_laporan = 'selesai' THEN 1 ELSE 0 END) as selesai,
                    SUM(CASE WHEN status_laporan = 'diproses' THEN 1 ELSE 0 END) as diproses,
                    SUM(CASE WHEN status_laporan = 'baru' THEN 1 ELSE 0 END) as baru
                  FROM laporan_camat
                  GROUP BY nama_kecamatan
                  ORDER BY total DESC";

        $result = $this->db->query($query);
        $data = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }

    /**
     * Get overdue laporan camat (belum selesai setelah 7 hari)
     */
    public function getOverdueLaporanCamat($days = 7) {
        $days = (int)$days;
        $query = "SELECT lc.*, u.username, u.jabatan, '' as nama_kegiatan
                  FROM laporan_camat lc
                  LEFT JOIN users u ON lc.id_user = u.id_user
                  WHERE lc.status_laporan != 'selesai'
                  AND lc.created_at < DATE_SUB(CURRENT_DATE, INTERVAL $days DAY)
                  ORDER BY lc.created_at ASC";

        $result = $this->db->query($query);
        $data = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }

    /**
     * Bulk update status laporan
     */
    public function bulkUpdateStatus($ids, $status) {
        if (empty($ids)) {
            return [
                'success' => false,
                'message' => 'Tidak ada laporan yang dipilih'
            ];
        }

        $ids = array_map('intval', $ids);
        $idList = implode(',', $ids);
        $status = escapeString($status);

        $query = "UPDATE laporan_camat
                  SET status_laporan = '$status', updated_at = CURRENT_TIMESTAMP
                  WHERE id_laporan_camat IN ($idList)";

        $result = $this->db->query($query);

        if ($result) {
            return [
                'success' => true,
                'message' => 'Status laporan berhasil diperbarui',
                'affected_rows' => $this->db->affected_rows
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Gagal memperbarui status laporan'
            ];
        }
    }

    /**
     * Get laporan camat untuk notifikasi
     */
    public function getLaporanCamatForNotification() {
        $query = "SELECT lc.*, u.username
                  FROM laporan_camat lc
                  LEFT JOIN users u ON lc.id_user = u.id_user
                  WHERE lc.status_laporan = 'baru'
                  AND lc.created_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR)
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

    /**
     * Get tujuan options for filter
     */
    public function getTujuanOptions() {
        $query = "SELECT DISTINCT tujuan
                  FROM laporan_camat
                  WHERE tujuan IS NOT NULL
                  AND tujuan != ''
                  ORDER BY tujuan ASC";

        $result = $this->db->query($query);
        $options = [];

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $options[] = $row['tujuan'];
            }
        }

        return $options;
    }
}