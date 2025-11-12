<?php
require_once __DIR__ . '/../config/koneksi.php';

class LaporanCamatModel {
    private $db;

    public function __construct() {
        $this->db = getKoneksi();
    }

    /**
     * Get all laporan camat for a specific user
     */
    public function getAllByUserId($id_user, $limit = 10, $offset = 0, $search = '') {
        $query = "SELECT * FROM laporan_camat WHERE id_user = ?";

        if (!empty($search)) {
            $query .= " AND (nama_pelapor LIKE ? OR nama_desa LIKE ? OR nama_kecamatan LIKE ? OR uraian_laporan LIKE ?)";
        }

        $query .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";

        $params = [$id_user];
        if (!empty($search)) {
            $searchParam = '%' . $search . '%';
            $params = array_merge($params, [$searchParam, $searchParam, $searchParam, $searchParam]);
        }
        $params[] = $limit;
        $params[] = $offset;

        $stmt = $this->db->prepare($query);

        // Build parameter type string
        $types = str_repeat('s', count($params) - 2) . 'ii'; // last 2 params are integers (limit, offset)
        $stmt->bind_param($types, ...$params);

        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get total count of laporan camat for a specific user
     */
    public function getTotalByUserId($id_user, $search = '') {
        $query = "SELECT COUNT(*) as total FROM laporan_camat WHERE id_user = ?";

        if (!empty($search)) {
            $query .= " AND (nama_pelapor LIKE ? OR nama_desa LIKE ? OR nama_kecamatan LIKE ? OR uraian_laporan LIKE ?)";
        }

        $params = [$id_user];
        if (!empty($search)) {
            $searchParam = '%' . $search . '%';
            $params = array_merge($params, [$searchParam, $searchParam, $searchParam, $searchParam]);
        }

        $stmt = $this->db->prepare($query);
        $types = str_repeat('s', count($params));
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        return $result['total'];
    }

    /**
     * Get laporan camat by ID
     */
    public function getById($id) {
        $query = "SELECT * FROM laporan_camat WHERE id_laporan_camat = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Create new laporan camat
     */
    public function create($data) {
        $query = "INSERT INTO laporan_camat (id_user, nama_pelapor, nama_desa, nama_kecamatan, waktu_kejadian, tujuan, uraian_laporan, upload_file)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->db->prepare($query);

        $upload_file = $data['upload_file'] ?? null;
        $stmt->bind_param('isssssss',
            $data['id_user'],
            $data['nama_pelapor'],
            $data['nama_desa'],
            $data['nama_kecamatan'],
            $data['waktu_kejadian'],
            $data['tujuan'],
            $data['uraian_laporan'],
            $upload_file
        );

        $result = $stmt->execute();

        if ($result) {
            return $this->db->insert_id;
        }
        return false;
    }

    /**
     * Update laporan camat
     */
    public function update($id, $data) {
        $query = "UPDATE laporan_camat
                 SET nama_pelapor = ?, nama_desa = ?, nama_kecamatan = ?, waktu_kejadian = ?,
                     tujuan = ?, uraian_laporan = ?, upload_file = ?
                 WHERE id_laporan_camat = ?";

        $stmt = $this->db->prepare($query);

        $upload_file = $data['upload_file'] ?? null;
        $stmt->bind_param('sssssssi',
            $data['nama_pelapor'],
            $data['nama_desa'],
            $data['nama_kecamatan'],
            $data['waktu_kejadian'],
            $data['tujuan'],
            $data['uraian_laporan'],
            $upload_file,
            $id
        );

        return $stmt->execute();
    }

    /**
     * Update status laporan
     */
    public function updateStatus($id, $status) {
        $query = "UPDATE laporan_camat SET status_laporan = ? WHERE id_laporan_camat = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('si', $status, $id);
        return $stmt->execute();
    }

    /**
     * Delete laporan camat
     */
    public function delete($id) {
        $query = "DELETE FROM laporan_camat WHERE id_laporan_camat = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }

    /**
     * Get laporan camat with pagination
     */
    public function getWithPagination($limit = 10, $offset = 0, $search = '', $status = null) {
        $query = "SELECT lc.*, u.username FROM laporan_camat lc
                  LEFT JOIN users u ON lc.id_user = u.id_user WHERE 1=1";

        $params = [];

        if (!empty($search)) {
            $query .= " AND (lc.nama_pelapor LIKE ? OR lc.nama_desa LIKE ? OR lc.nama_kecamatan LIKE ? OR lc.uraian_laporan LIKE ? OR u.username LIKE ?)";
            $searchParam = '%' . $search . '%';
            $params = array_merge($params, [$searchParam, $searchParam, $searchParam, $searchParam, $searchParam]);
        }

        if ($status) {
            $query .= " AND lc.status_laporan = ?";
            $params[] = $status;
        }

        $query .= " ORDER BY lc.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;

        $stmt = $this->db->prepare($query);

        // Build parameter type string
        if (!empty($search) && $status) {
            $types = str_repeat('s', count($params) - 2) . 'ii';
        } elseif (!empty($search) || $status) {
            $types = str_repeat('s', count($params) - 2) . 'ii';
        } else {
            $types = 'ii';
        }
        $stmt->bind_param($types, ...$params);

        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get total count with filters
     */
    public function getTotalWithFilters($search = '', $status = null) {
        $query = "SELECT COUNT(*) as total FROM laporan_camat lc
                  LEFT JOIN users u ON lc.id_user = u.id_user WHERE 1=1";

        $params = [];

        if (!empty($search)) {
            $query .= " AND (lc.nama_pelapor LIKE ? OR lc.nama_desa LIKE ? OR lc.nama_kecamatan LIKE ? OR lc.uraian_laporan LIKE ? OR u.username LIKE ?)";
            $searchParam = '%' . $search . '%';
            $params = array_merge($params, [$searchParam, $searchParam, $searchParam, $searchParam, $searchParam]);
        }

        if ($status) {
            $query .= " AND lc.status_laporan = ?";
            $params[] = $status;
        }

        $stmt = $this->db->prepare($query);

        if (!empty($params)) {
            $types = str_repeat('s', count($params));
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        return $result['total'];
    }

    /**
     * Get all reports for export
     */
    public function getAllReportsForExport($id_user, $search = '', $status = '', $tanggal_awal = '', $tanggal_akhir = '') {
        $query = "SELECT * FROM laporan_camat WHERE id_user = ?";
        $params = [$id_user];

        // Add search condition
        if (!empty($search)) {
            $query .= " AND (nama_pelapor LIKE ? OR nama_desa LIKE ? OR nama_kecamatan LIKE ? OR uraian_laporan LIKE ?)";
            $searchParam = '%' . $search . '%';
            $params = array_merge($params, [$searchParam, $searchParam, $searchParam, $searchParam]);
        }

        // Add status filter
        if (!empty($status)) {
            $query .= " AND status_laporan = ?";
            $params[] = $status;
        }

        // Add date range filter
        if (!empty($tanggal_awal) && !empty($tanggal_akhir)) {
            $query .= " AND DATE(created_at) BETWEEN ? AND ?";
            $params[] = $tanggal_awal;
            $params[] = $tanggal_akhir;
        } elseif (!empty($tanggal_awal)) {
            $query .= " AND DATE(created_at) >= ?";
            $params[] = $tanggal_awal;
        } elseif (!empty($tanggal_akhir)) {
            $query .= " AND DATE(created_at) <= ?";
            $params[] = $tanggal_akhir;
        }

        $query .= " ORDER BY created_at DESC";

        try {
            $stmt = $this->db->prepare($query);
            if ($stmt === false) {
                throw new Exception('Query preparation failed: ' . $this->db->error);
            }

            // Build parameter type string
            $types = str_repeat('s', count($params));
            $stmt->bind_param($types, ...$params);

            $execute_result = $stmt->execute();
            if ($execute_result === false) {
                throw new Exception('Query execution failed: ' . $stmt->error);
            }

            $result = $stmt->get_result();
            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }

            $stmt->close();
            return $data;

        } catch (Exception $e) {
            error_log("Error in getAllReportsForExport: " . $e->getMessage());
            return [];
        }
    }
}