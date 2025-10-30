<?php
require_once __DIR__ . '/../config/koneksi.php';

class ProfileModel {
    public $db;

    public function __construct() {
        $this->db = getKoneksi();
    }

    /**
     * Get all profiles with pagination
     */
    public function getAllProfiles($page = 1, $limit = 10, $search = '', $role_filter = '') {
        $offset = ($page - 1) * $limit;

        // Build WHERE conditions
        $whereClause = "WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $search = escapeString($search);
            $whereClause .= " AND (nama_aplikasi LIKE ?)";
            $params[] = "%$search%";
        }

        if (!empty($role_filter)) {
            $whereClause .= " AND role = ?";
            $params[] = $role_filter;
        }

        // Main query
        $query = "SELECT * FROM profile $whereClause ORDER BY nama_aplikasi ASC LIMIT $limit OFFSET $offset";
        $stmt = $this->db->prepare($query);

        if (!empty($params)) {
            $types = str_repeat('s', count($params));
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        // Get total count for pagination
        $countQuery = "SELECT COUNT(*) as total FROM profile $whereClause";
        $countStmt = $this->db->prepare($countQuery);

        if (!empty($params)) {
            $countStmt->bind_param($types, ...$params);
        }

        $countStmt->execute();
        $countResult = $countStmt->get_result();
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
     * Get profile by ID
     */
    public function getProfileById($id_profile) {
        $id_profile = (int)$id_profile;
        $query = "SELECT * FROM profile WHERE id_profile = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $id_profile);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }

        return null;
    }

    /**
     * Create new profile
     */
    public function createProfile($data) {
        $query = "INSERT INTO profile (nama_aplikasi, logo, role) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('sss', $data['nama_aplikasi'], $data['logo'], $data['role']);

        if ($stmt->execute()) {
            return [
                'success' => true,
                'id_profile' => $this->db->insert_id,
                'message' => 'Profile berhasil ditambahkan'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Gagal menambahkan profile: ' . $this->db->error
            ];
        }
    }

    /**
     * Update profile
     */
    public function updateProfile($id_profile, $data) {
        $id_profile = (int)$id_profile;
        $query = "UPDATE profile SET nama_aplikasi = ?, logo = ?, role = ? WHERE id_profile = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('sssi', $data['nama_aplikasi'], $data['logo'], $data['role'], $id_profile);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                return [
                    'success' => true,
                    'message' => 'Profile berhasil diperbarui'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Tidak ada perubahan data pada profile'
                ];
            }
        } else {
            return [
                'success' => false,
                'message' => 'Gagal memperbarui profile: ' . $this->db->error
            ];
        }
    }

    /**
     * Delete profile
     */
    public function deleteProfile($id_profile) {
        $id_profile = (int)$id_profile;

        // Check if profile exists
        $profile = $this->getProfileById($id_profile);
        if (!$profile) {
            return [
                'success' => false,
                'message' => 'Profile tidak ditemukan'
            ];
        }

        $query = "DELETE FROM profile WHERE id_profile = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $id_profile);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                return [
                    'success' => true,
                    'message' => "Profile '{$profile['nama_aplikasi']}' berhasil dihapus"
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Gagal menghapus profile'
                ];
            }
        } else {
            return [
                'success' => false,
                'message' => 'Gagal menghapus profile: ' . $this->db->error
            ];
        }
    }

    /**
     * Get profile statistics
     */
    public function getProfileStats() {
        $query = "SELECT
                    COUNT(*) as total,
                    SUM(CASE WHEN role = 'camat' THEN 1 ELSE 0 END) as total_camat,
                    SUM(CASE WHEN role = 'opd' THEN 1 ELSE 0 END) as total_opd
                  FROM profile";
        $result = $this->db->query($query);

        if ($result) {
            return $result->fetch_assoc();
        }

        return [
            'total' => 0,
            'total_camat' => 0,
            'total_opd' => 0
        ];
    }

    /**
     * Check if nama_aplikasi already exists
     */
    public function checkNamaAplikasiExists($nama_aplikasi, $exclude_id = null) {
        $query = "SELECT COUNT(*) as count FROM profile WHERE nama_aplikasi = ?";
        $params = [$nama_aplikasi];
        $types = 's';

        if ($exclude_id) {
            $query .= " AND id_profile != ?";
            $params[] = $exclude_id;
            $types .= 'i';
        }

        $stmt = $this->db->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result) {
            $row = $result->fetch_assoc();
            return $row['count'] > 0;
        }

        return false;
    }

    /**
     * Get profiles by role
     */
    public function getProfilesByRole($role) {
        $query = "SELECT * FROM profile WHERE role = ? ORDER BY nama_aplikasi ASC";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('s', $role);
        $stmt->execute();
        $result = $stmt->get_result();

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        return $data;
    }
}