<?php

require_once __DIR__ . '/../config/koneksi.php';

class WilayahModel {
    public $db;

    public function __construct() {
        $this->db = getKoneksi();
    }

    /**
     * ==================== KECAMATAN ====================
     */

    /**
     * Get all kecamatan with pagination
     */
    public function getAllKecamatan($page = 1, $limit = 10, $search = '') {
        $offset = ($page - 1) * $limit;

        // Build WHERE conditions
        $whereClause = "";
        if (!empty($search)) {
            $search = escapeString($search);
            $whereClause = "WHERE nama_kecamatan LIKE '%$search%'";
        }

        // Main query
        $query = "SELECT * FROM kecamatan $whereClause ORDER BY nama_kecamatan ASC LIMIT $limit OFFSET $offset";
        $result = $this->db->query($query);

        $data = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }

        // Get total count for pagination
        $countQuery = "SELECT COUNT(*) as total FROM kecamatan $whereClause";
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
     * Get kecamatan by ID
     */
    public function getKecamatanById($id_kecamatan) {
        $id_kecamatan = (int)$id_kecamatan;
        $query = "SELECT * FROM kecamatan WHERE id_kecamatan = $id_kecamatan";
        $result = $this->db->query($query);

        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        return null;
    }

    /**
     * Insert new kecamatan
     */
    public function insertKecamatan($data) {
        $nama_kecamatan = escapeString($data['nama_kecamatan']);

        $query = "INSERT INTO kecamatan (nama_kecamatan, created_at, updated_at)
                  VALUES ('$nama_kecamatan', NOW(), NOW())";

        if ($this->db->query($query)) {
            return $this->db->insert_id;
        }
        return false;
    }

    /**
     * Update kecamatan
     */
    public function updateKecamatan($id_kecamatan, $data) {
        $id_kecamatan = (int)$id_kecamatan;
        $nama_kecamatan = escapeString($data['nama_kecamatan']);

        $query = "UPDATE kecamatan SET
                  nama_kecamatan = '$nama_kecamatan',
                  updated_at = NOW()
                  WHERE id_kecamatan = $id_kecamatan";

        return $this->db->query($query);
    }

    /**
     * Delete kecamatan
     */
    public function deleteKecamatan($id_kecamatan) {
        $id_kecamatan = (int)$id_kecamatan;

        // Start transaction for data integrity
        $this->db->begin_transaction();

        try {
            // First, count related desa for reporting
            $checkQuery = "SELECT COUNT(*) as count FROM desa WHERE id_kecamatan = $id_kecamatan";
            $result = $this->db->query($checkQuery);
            $relatedDesaCount = 0;
            if ($result) {
                $row = $result->fetch_assoc();
                $relatedDesaCount = $row['count'];
            }

            // Delete all related desa first
            if ($relatedDesaCount > 0) {
                $deleteDesaQuery = "DELETE FROM desa WHERE id_kecamatan = $id_kecamatan";
                if (!$this->db->query($deleteDesaQuery)) {
                    throw new Exception('Gagal menghapus desa terkait');
                }
            }

            // Then delete the kecamatan
            $deleteKecamatanQuery = "DELETE FROM kecamatan WHERE id_kecamatan = $id_kecamatan";
            if (!$this->db->query($deleteKecamatanQuery)) {
                throw new Exception('Gagal menghapus kecamatan');
            }

            // Commit the transaction
            $this->db->commit();

            // Create appropriate success message
            if ($relatedDesaCount > 0) {
                $message = "Kecamatan dan {$relatedDesaCount} desa terkait berhasil dihapus";
            } else {
                $message = "Kecamatan berhasil dihapus";
            }

            return ['success' => true, 'message' => $message];

        } catch (Exception $e) {
            // Rollback on error
            $this->db->rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * ==================== DESA ====================
     */

    /**
     * Get all desa with kecamatan name
     */
    public function getAllDesa($page = 1, $limit = 10, $search = '', $kecamatan_filter = '') {
        $offset = ($page - 1) * $limit;

        // Build WHERE conditions
        $whereClause = "WHERE 1=1";
        if (!empty($search)) {
            $search = escapeString($search);
            $whereClause .= " AND (d.nama_desa LIKE '%$search%' OR k.nama_kecamatan LIKE '%$search%')";
        }
        if (!empty($kecamatan_filter)) {
            $kecamatan_filter = (int)$kecamatan_filter;
            $whereClause .= " AND d.id_kecamatan = $kecamatan_filter";
        }

        // Tambahkan filter untuk menghindari data desa kosong
        $whereClause .= " AND (d.nama_desa IS NOT NULL AND d.nama_desa != '')";

        $query = "SELECT d.*, k.nama_kecamatan
                  FROM desa d
                  INNER JOIN kecamatan k ON d.id_kecamatan = k.id_kecamatan
                  $whereClause
                  ORDER BY k.nama_kecamatan ASC, d.nama_desa ASC
                  LIMIT $limit OFFSET $offset";

        $result = $this->db->query($query);
        $data = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }

        // Get total count for pagination
        $countQuery = "SELECT COUNT(*) as total
                       FROM desa d
                       INNER JOIN kecamatan k ON d.id_kecamatan = k.id_kecamatan
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
     * Get desa by ID
     */
    public function getDesaById($id_desa) {
        $id_desa = (int)$id_desa;
        $query = "SELECT d.*, k.nama_kecamatan
                  FROM desa d
                  LEFT JOIN kecamatan k ON d.id_kecamatan = k.id_kecamatan
                  WHERE d.id_desa = $id_desa";
        $result = $this->db->query($query);

        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        return null;
    }

    /**
     * Insert new desa
     */
    public function insertDesa($data) {
        $id_kecamatan = (int)$data['id_kecamatan'];
        $nama_desa = escapeString($data['nama_desa']);

        $query = "INSERT INTO desa (id_kecamatan, nama_desa, created_at, updated_at)
                  VALUES ($id_kecamatan, '$nama_desa', NOW(), NOW())";

        if ($this->db->query($query)) {
            return $this->db->insert_id;
        }
        return false;
    }

    /**
     * Update desa
     */
    public function updateDesa($id_desa, $data) {
        $id_desa = (int)$id_desa;
        $id_kecamatan = (int)$data['id_kecamatan'];
        $nama_desa = escapeString($data['nama_desa']);

        $query = "UPDATE desa SET
                  id_kecamatan = $id_kecamatan,
                  nama_desa = '$nama_desa',
                  updated_at = NOW()
                  WHERE id_desa = $id_desa";

        return $this->db->query($query);
    }

    /**
     * Delete desa
     */
    public function deleteDesa($id_desa) {
        $id_desa = (int)$id_desa;

        $query = "DELETE FROM desa WHERE id_desa = $id_desa";
        if ($this->db->query($query)) {
            return ['success' => true, 'message' => 'Desa berhasil dihapus'];
        }
        return ['success' => false, 'message' => 'Gagal menghapus desa'];
    }

    /**
     * Get all kecamatan for dropdown
     */
    public function getKecamatanOptions() {
        $query = "SELECT id_kecamatan, nama_kecamatan FROM kecamatan ORDER BY nama_kecamatan ASC";
        $result = $this->db->query($query);

        $options = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $options[] = $row;
            }
        }

        return $options;
    }

    /**
     * Get statistics
     */
    public function getStatistics() {
        $kecamatanQuery = "SELECT COUNT(*) as total_kecamatan FROM kecamatan";
        $desaQuery = "SELECT COUNT(*) as total_desa FROM desa";

        $kecamatanResult = $this->db->query($kecamatanQuery);
        $desaResult = $this->db->query($desaQuery);

        $stats = [
            'total_kecamatan' => 0,
            'total_desa' => 0
        ];

        if ($kecamatanResult) {
            $row = $kecamatanResult->fetch_assoc();
            $stats['total_kecamatan'] = $row['total_kecamatan'];
        }

        if ($desaResult) {
            $row = $desaResult->fetch_assoc();
            $stats['total_desa'] = $row['total_desa'];
        }

        return $stats;
    }

    /**
     * Get desa by kecamatan ID
     */
    public function getDesaByKecamatan($id_kecamatan) {
        $id_kecamatan = (int)$id_kecamatan;
        
        $query = "SELECT id_desa, nama_desa
                  FROM desa
                  WHERE id_kecamatan = $id_kecamatan
                  ORDER BY nama_desa ASC";
        $result = $this->db->query($query);

        $desa_list = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $desa_list[] = $row;
            }
        }

        return $desa_list;
    }

    /**
     * Get kecamatan stats with related desa information
     */
    public function getKecamatanWithDesaStats($id_kecamatan) {
        $id_kecamatan = (int)$id_kecamatan;

        $query = "SELECT
                    k.nama_kecamatan,
                    COUNT(d.id_desa) as desa_count,
                    GROUP_CONCAT(d.nama_desa ORDER BY d.nama_desa SEPARATOR '\n') as desa_list
                  FROM kecamatan k
                  LEFT JOIN desa d ON k.id_kecamatan = d.id_kecamatan
                  WHERE k.id_kecamatan = $id_kecamatan
                  GROUP BY k.id_kecamatan, k.nama_kecamatan";

        $result = $this->db->query($query);
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }

        return null;
    }
}