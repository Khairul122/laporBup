<?php

require_once __DIR__ . '/../config/koneksi.php';

class LaporanOPDAdminModel {
    private $db;

    public function __construct() {
        $this->db = getKoneksi();
    }

    /**
     * Get all laporan OPD with pagination and filtering
     */
    public function getAllLaporanOPD($page = 1, $limit = 10, $search = '', $status = '') {
        $offset = ($page - 1) * $limit;

        // Build WHERE conditions
        $whereClause = "";

        if (!empty($search)) {
            $search = escapeString($search);
            $whereClause .= " WHERE (lo.nama_opd LIKE '%$search%' OR lo.nama_kegiatan LIKE '%$search%' OR lo.uraian_laporan LIKE '%$search%' OR u.username LIKE '%$search%')";
        }

        if (!empty($status)) {
            $status = escapeString($status);
            if (!empty($whereClause)) {
                $whereClause .= " AND lo.status_laporan = '$status'";
            } else {
                $whereClause .= " WHERE lo.status_laporan = '$status'";
            }
        }

        // Get total data
        $countQuery = "SELECT COUNT(*) as total
                      FROM laporan_opd lo
                      LEFT JOIN users u ON lo.id_user = u.id_user" . $whereClause;

        $countResult = $this->db->query($countQuery);
        $totalData = 0;
        if ($countResult) {
            $totalRow = $countResult->fetch_assoc();
            $totalData = $totalRow['total'];
        }

        // Get data with pagination
        $query = "SELECT
                    lo.id_laporan_opd,
                    lo.id_user,
                    lo.nama_opd,
                    lo.nama_kegiatan,
                    lo.uraian_laporan,
                    lo.tujuan,
                    lo.upload_file,
                    lo.status_laporan,
                    lo.created_at,
                    lo.updated_at,
                    u.username,
                    u.jabatan
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

        return [
            'data' => $data,
            'total' => $totalData,
            'per_page' => $limit,
            'current_page' => $page,
            'total_pages' => $totalData > 0 ? ceil($totalData / $limit) : 1
        ];
    }

    /**
     * Get single laporan OPD by ID
     */
    public function getLaporanOPDById($id) {
        $id = (int)$id;
        $query = "SELECT
                    lo.*,
                    u.username,
                    u.jabatan
                  FROM laporan_opd lo
                  LEFT JOIN users u ON lo.id_user = u.id_user
                  WHERE lo.id_laporan_opd = $id";

        $result = $this->db->query($query);
        return $result ? $result->fetch_assoc() : null;
    }

    /**
     * Create new laporan OPD
     */
    public function createLaporanOPD($data) {
        $nama_opd = escapeString($data['nama_opd']);
        $nama_kegiatan = escapeString($data['nama_kegiatan']);
        $uraian_laporan = escapeString($data['uraian_laporan']);
        $tujuan = escapeString($data['tujuan']);
        $status_laporan = escapeString($data['status_laporan'] ?? 'baru');
        $id_user = (int)$data['id_user'];
        $upload_file = isset($data['upload_file']) ? escapeString($data['upload_file']) : 'NULL';

        $query = "INSERT INTO laporan_opd (nama_opd, nama_kegiatan, uraian_laporan, tujuan, status_laporan, id_user, upload_file)
                  VALUES ('$nama_opd', '$nama_kegiatan', '$uraian_laporan', '$tujuan', '$status_laporan', $id_user, " .
                  ($upload_file !== 'NULL' ? "'$upload_file'" : 'NULL') . ")";

        $result = $this->db->query($query);

        if ($result) {
            return [
                'success' => true,
                'message' => 'Laporan OPD berhasil ditambahkan',
                'id' => $this->db->insert_id
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Gagal menambahkan laporan OPD'
            ];
        }
    }

    /**
     * Update laporan OPD
     */
    public function updateLaporanOPD($id, $data) {
        $id = (int)$id;
        $setClause = [];

        foreach ($data as $key => $value) {
            if ($key !== 'id_laporan_opd' && $key !== 'created_at') {
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
        $query = "UPDATE laporan_opd SET " . implode(", ", $setClause) . " WHERE id_laporan_opd = $id";

        $result = $this->db->query($query);

        if ($result) {
            return [
                'success' => true,
                'message' => 'Laporan OPD berhasil diperbarui'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Gagal memperbarui laporan OPD'
            ];
        }
    }

    /**
     * Delete laporan OPD
     */
    public function deleteLaporanOPD($id) {
        $id = (int)$id;

        // Get file path before deleting
        $laporan = $this->getLaporanOPDById($id);

        $query = "DELETE FROM laporan_opd WHERE id_laporan_opd = $id";
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
                'message' => 'Laporan OPD berhasil dihapus'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Gagal menghapus laporan OPD'
            ];
        }
    }

    /**
     * Update status laporan
     */
    public function updateStatus($id, $status) {
        $id = (int)$id;
        $status = escapeString($status);

        $query = "UPDATE laporan_opd SET status_laporan = '$status', updated_at = CURRENT_TIMESTAMP WHERE id_laporan_opd = $id";
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
    public function getLaporanOPDStatistics() {
        // Total laporan
        $query = "SELECT COUNT(*) as total_laporan FROM laporan_opd";
        $result = $this->db->query($query);
        $total = $result ? $result->fetch_assoc() : ['total_laporan' => 0];

        // Statistik by status
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

    /**
     * Get recent laporan OPD for dashboard
     */
    public function getRecentLaporanOPD($limit = 5) {
        $limit = (int)$limit;
        $query = "SELECT lo.*, u.username
                  FROM laporan_opd lo
                  LEFT JOIN users u ON lo.id_user = u.id_user
                  ORDER BY lo.created_at DESC
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
     * Search laporan OPD
     */
    public function searchLaporanOPD($keyword, $limit = 50) {
        $keyword = escapeString($keyword);
        $limit = (int)$limit;

        $query = "SELECT lo.*, u.username, u.jabatan
                  FROM laporan_opd lo
                  LEFT JOIN users u ON lo.id_user = u.id_user
                  WHERE lo.nama_opd LIKE '%$keyword%' OR lo.nama_kegiatan LIKE '%$keyword%' OR lo.uraian_laporan LIKE '%$keyword%'
                  ORDER BY lo.created_at DESC
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
     * Get laporan OPD by date range
     */
    public function getLaporanOPDByDateRange($startDate, $endDate, $status = '') {
        $startDate = escapeString($startDate);
        $endDate = escapeString($endDate);

        $whereClause = "WHERE DATE(lo.created_at) BETWEEN '$startDate' AND '$endDate'";

        if (!empty($status)) {
            $status = escapeString($status);
            $whereClause .= " AND lo.status_laporan = '$status'";
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

    /**
     * Export laporan OPD (all data)
     */
    public function exportLaporanOPD() {
        $query = "SELECT lo.*, u.username, u.jabatan
                  FROM laporan_opd lo
                  LEFT JOIN users u ON lo.id_user = u.id_user
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

    /**
     * Get monthly statistics for the current year
     */
    public function getMonthlyStatistics() {
        $query = "SELECT
                    MONTH(created_at) as month,
                    COUNT(*) as total,
                    SUM(CASE WHEN status_laporan = 'selesai' THEN 1 ELSE 0 END) as completed
                  FROM laporan_opd
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
     * Get laporan OPD by user
     */
    public function getLaporanOPDByUser($userId, $limit = 10, $offset = 0) {
        $userId = (int)$userId;
        $limit = (int)$limit;
        $offset = (int)$offset;

        $query = "SELECT lo.*, u.username
                  FROM laporan_opd lo
                  LEFT JOIN users u ON lo.id_user = u.id_user
                  WHERE lo.id_user = $userId
                  ORDER BY lo.created_at DESC
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
     * Get count of laporan OPD by user
     */
    public function getCountLaporanOPDByUser($userId) {
        $userId = (int)$userId;
        $query = "SELECT COUNT(*) as total FROM laporan_opd WHERE id_user = $userId";
        $result = $this->db->query($query);
        return $result ? $result->fetch_assoc()['total'] : 0;
    }

    /**
     * Get laporan OPD statistics by OPD
     */
    public function getStatisticsByOPD() {
        $query = "SELECT
                    nama_opd,
                    COUNT(*) as total,
                    SUM(CASE WHEN status_laporan = 'selesai' THEN 1 ELSE 0 END) as selesai,
                    SUM(CASE WHEN status_laporan = 'diproses' THEN 1 ELSE 0 END) as diproses,
                    SUM(CASE WHEN status_laporan = 'baru' THEN 1 ELSE 0 END) as baru
                  FROM laporan_opd
                  GROUP BY nama_opd
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
     * Get overdue laporan OPD (belum selesai setelah 7 hari)
     */
    public function getOverdueLaporanOPD($days = 7) {
        $days = (int)$days;
        $query = "SELECT lo.*, u.username, u.jabatan
                  FROM laporan_opd lo
                  LEFT JOIN users u ON lo.id_user = u.id_user
                  WHERE lo.status_laporan != 'selesai'
                  AND lo.created_at < DATE_SUB(CURRENT_DATE, INTERVAL $days DAY)
                  ORDER BY lo.created_at ASC";

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

        $query = "UPDATE laporan_opd
                  SET status_laporan = '$status', updated_at = CURRENT_TIMESTAMP
                  WHERE id_laporan_opd IN ($idList)";

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
     * Get laporan OPD untuk notifikasi
     */
    public function getLaporanOPDForNotification() {
        $query = "SELECT lo.*, u.username
                  FROM laporan_opd lo
                  LEFT JOIN users u ON lo.id_user = u.id_user
                  WHERE lo.status_laporan = 'baru'
                  AND lo.created_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR)
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
}