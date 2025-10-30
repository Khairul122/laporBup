<?php
require_once __DIR__ . '/../config/koneksi.php';

class OPDModel {
    public $db;

    public function __construct() {
        $this->db = getKoneksi();
    }

    /**
     * Get all OPD with pagination
     */
    public function getAllOPD($page = 1, $limit = 10, $search = '') {
        $offset = ($page - 1) * $limit;

        // Build WHERE conditions
        $whereClause = "";
        if (!empty($search)) {
            $search = escapeString($search);
            $whereClause = "WHERE nama_opd LIKE '%$search%'";
        }

        // Main query
        $query = "SELECT * FROM opd $whereClause ORDER BY nama_opd ASC LIMIT $limit OFFSET $offset";
        $result = $this->db->query($query);

        $data = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }

        // Get total count for pagination
        $countQuery = "SELECT COUNT(*) as total FROM opd $whereClause";
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
     * Get OPD by ID
     */
    public function getOPDById($id_opd) {
        $id_opd = (int)$id_opd;
        $query = "SELECT * FROM opd WHERE id_opd = $id_opd";
        $result = $this->db->query($query);

        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        return null;
    }

    /**
     * Create new OPD
     */
    public function createOPD($data) {
        $nama_opd = escapeString($data['nama_opd']);

        $query = "INSERT INTO opd (nama_opd) VALUES ('$nama_opd')";

        if ($this->db->query($query)) {
            return $this->db->insert_id;
        }
        return false;
    }

    /**
     * Update OPD
     */
    public function updateOPD($id_opd, $data) {
        $id_opd = (int)$id_opd;
        $nama_opd = escapeString($data['nama_opd']);

        $query = "UPDATE opd SET nama_opd = '$nama_opd' WHERE id_opd = $id_opd";

        return $this->db->query($query);
    }

    /**
     * Delete OPD
     */
    public function deleteOPD($id_opd) {
        $id_opd = (int)$id_opd;

        $query = "DELETE FROM opd WHERE id_opd = $id_opd";
        if ($this->db->query($query)) {
            return ['success' => true, 'message' => 'OPD berhasil dihapus'];
        }
        return ['success' => false, 'message' => 'Gagal menghapus OPD'];
    }

    /**
     * Check if OPD name already exists
     */
    public function checkNamaOPDExists($nama_opd, $exclude_id = null) {
        $nama_opd = escapeString($nama_opd);
        $query = "SELECT COUNT(*) as count FROM opd WHERE nama_opd = '$nama_opd'";
        
        if ($exclude_id) {
            $exclude_id = (int)$exclude_id;
            $query .= " AND id_opd != $exclude_id";
        }
        
        $result = $this->db->query($query);
        if ($result) {
            $row = $result->fetch_assoc();
            return $row['count'] > 0;
        }
        return false;
    }
}