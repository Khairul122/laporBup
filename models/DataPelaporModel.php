<?php

require_once __DIR__ . '/../config/koneksi.php';

class DataPelaporModel {
    private $db;

    public function __construct() {
        $this->db = getKoneksi();
    }

    /**
     * Get all data pelapor with pagination and filtering
     */
    public function getAllDataPelapor($page = 1, $limit = 10, $search = '', $role = '') {
        $offset = ($page - 1) * $limit;

        $whereClause = "";
        $params = [];

        if (!empty($search)) {
            $whereClause .= " WHERE (u.username LIKE ? OR u.email LIKE ? OR u.jabatan LIKE ?)";
            $searchTerm = '%' . $search . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        if (!empty($role)) {
            $whereClause .= ($whereClause ? " AND" : " WHERE") . " u.role = ?";
            $params[] = $role;
        }

        // Add role filter to exclude admin
        if ($whereClause) {
            $whereClause .= " AND u.role != 'admin'";
        } else {
            $whereClause = " WHERE u.role != 'admin'";
        }

        // Get total data
        $countQuery = "SELECT COUNT(*) as total FROM users u" . $whereClause;
        $countResult = query($countQuery, $params);
        $totalData = $countResult->fetch_assoc()['total'];

        // Get data with pagination - use string concatenation for LIMIT
        $query = "SELECT
                    u.id_user,
                    u.username,
                    u.email,
                    u.jabatan,
                    u.role,
                    u.created_at,
                    0 as total_laporan_camat,
                    0 as total_laporan_opd,
                    0 as total_laporan
                  FROM users u
                  {$whereClause}
                  ORDER BY u.created_at DESC
                  LIMIT " . (int)$limit . " OFFSET " . (int)$offset;

        $result = query($query, $params);
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        return [
            'data' => $data,
            'total' => $totalData,
            'page' => $page,
            'limit' => $limit,
            'total_pages' => ceil($totalData / $limit)
        ];
    }

    /**
     * Get data pelapor by ID
     */
    public function getDataPelaporById($id) {
        $query = "SELECT
                    u.id_user,
                    u.username,
                    u.email,
                    u.jabatan,
                    u.role,
                    u.created_at,
                    0 as total_laporan_camat,
                    0 as total_laporan_opd,
                    0 as total_laporan
                  FROM users u
                  WHERE u.id_user = " . (int)$id;

        $result = query($query);
        return $result->fetch_assoc();
    }

    /**
     * Create new data pelapor
     */
    public function createDataPelapor($data) {
        // Check if username already exists
        $checkQuery = "SELECT id_user FROM users WHERE username = '" . escapeString($data['username']) . "' OR email = '" . escapeString($data['email']) . "'";
        $checkResult = query($checkQuery);

        if ($checkResult->num_rows > 0) {
            return [
                'success' => false,
                'message' => 'Username atau email sudah digunakan'
            ];
        }

        // Hash password
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

        // Insert new user
        $query = "INSERT INTO users (username, email, password, jabatan, role, created_at)
                  VALUES ('" . escapeString($data['username']) . "', '" . escapeString($data['email']) . "', '" . $hashedPassword . "', '" . escapeString($data['jabatan']) . "', '" . escapeString($data['role']) . "', NOW())";

        $result = query($query);

        if ($result) {
            return [
                'success' => true,
                'message' => 'Data pelapor berhasil ditambahkan',
                'id' => $this->db->insert_id
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Gagal menambahkan data pelapor'
            ];
        }
    }

    /**
     * Update data pelapor
     */
    public function updateDataPelapor($id, $data) {
        // Check if username/email already exists (excluding current user)
        $checkQuery = "SELECT id_user FROM users WHERE (username = '" . escapeString($data['username']) . "' OR email = '" . escapeString($data['email']) . "') AND id_user != " . (int)$id;
        $checkResult = query($checkQuery);

        if ($checkResult->num_rows > 0) {
            return [
                'success' => false,
                'message' => 'Username atau email sudah digunakan'
            ];
        }

        // Build query dynamically
        $fields = [];
        $params = [];

        $fields[] = "username = '" . escapeString($data['username']) . "'";
        $fields[] = "email = '" . escapeString($data['email']) . "'";
        $fields[] = "jabatan = '" . escapeString($data['jabatan']) . "'";
        $fields[] = "role = '" . escapeString($data['role']) . "'";

        // Add password if provided
        if (!empty($data['password'])) {
            $fields[] = "password = '" . password_hash($data['password'], PASSWORD_DEFAULT) . "'";
        }

        $query = "UPDATE users SET " . implode(', ', $fields) . " WHERE id_user = " . (int)$id;

        $result = query($query);

        if ($result) {
            return [
                'success' => true,
                'message' => 'Data pelapor berhasil diperbarui'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Gagal memperbarui data pelapor'
            ];
        }
    }

    /**
     * Delete data pelapor
     */
    public function deleteDataPelapor($id) {
        // Delete user
        $query = "DELETE FROM users WHERE id_user = " . (int)$id . " AND role IN ('camat', 'opd')";
        $result = query($query);

        if ($result && $this->db->affected_rows > 0) {
            return [
                'success' => true,
                'message' => 'Data pelapor berhasil dihapus'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Gagal menghapus data pelapor atau data tidak ditemukan'
            ];
        }
    }

    /**
     * Get statistics for pelapor
     */
    public function getPelaporStatistics() {
        $query = "SELECT
                    role,
                    COUNT(*) as total,
                    SUM(CASE WHEN DATE(created_at) = CURDATE() THEN 1 ELSE 0 END) as hari_ini,
                    SUM(CASE WHEN DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) THEN 1 ELSE 0 END) as minggu_ini,
                    SUM(CASE WHEN DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) THEN 1 ELSE 0 END) as bulan_ini
                  FROM users
                  WHERE role IN ('camat', 'opd')
                  GROUP BY role";

        $result = query($query);
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        return $data;
    }

    /**
     * Get data pelapor by role
     */
    public function getDataPelaporByRole($role) {
        $query = "SELECT
                    u.id_user,
                    u.username,
                    u.email,
                    u.jabatan,
                    u.created_at,
                    0 as total_laporan_camat,
                    0 as total_laporan_opd,
                    0 as total_laporan
                  FROM users u
                  WHERE u.role = '" . escapeString($role) . "' AND u.role != 'admin'
                  ORDER BY u.created_at DESC";

        $result = query($query);
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        return $data;
    }

    /**
     * Search data pelapor
     */
    public function searchDataPelapor($keyword) {
        $searchTerm = '%' . $keyword . '%';
        $query = "SELECT
                    u.id_user,
                    u.username,
                    u.email,
                    u.jabatan,
                    u.role,
                    u.created_at,
                    0 as total_laporan_camat,
                    0 as total_laporan_opd,
                    0 as total_laporan
                  FROM users u
                  WHERE u.role IN ('camat', 'opd')
                    AND (u.username LIKE '" . escapeString($searchTerm) . "' OR u.email LIKE '" . escapeString($searchTerm) . "' OR u.jabatan LIKE '" . escapeString($searchTerm) . "')
                  ORDER BY u.created_at DESC
                  LIMIT 10";

        $result = query($query);
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        return $data;
    }
}