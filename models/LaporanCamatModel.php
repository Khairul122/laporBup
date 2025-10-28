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
        $stmt->execute($params);
        
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
        $stmt->execute($params);
        $result = $stmt->get_result()->fetch_assoc();
        
        return $result['total'];
    }

    /**
     * Get laporan camat by ID
     */
    public function getById($id) {
        $query = "SELECT * FROM laporan_camat WHERE id_laporan_camat = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id]);
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Create new laporan camat
     */
    public function create($data) {
        $query = "INSERT INTO laporan_camat (id_user, nama_pelapor, nama_desa, nama_kecamatan, waktu_kejadian, tujuan, uraian_laporan, upload_file) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($query);
        $result = $stmt->execute([
            $data['id_user'],
            $data['nama_pelapor'],
            $data['nama_desa'],
            $data['nama_kecamatan'],
            $data['waktu_kejadian'],
            $data['tujuan'],
            $data['uraian_laporan'],
            $data['upload_file'] ?? null
        ]);
        
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
        return $stmt->execute([
            $data['nama_pelapor'],
            $data['nama_desa'],
            $data['nama_kecamatan'],
            $data['waktu_kejadian'],
            $data['tujuan'],
            $data['uraian_laporan'],
            $data['upload_file'] ?? null,
            $id
        ]);
    }

    /**
     * Update status laporan
     */
    public function updateStatus($id, $status) {
        $query = "UPDATE laporan_camat SET status_laporan = ? WHERE id_laporan_camat = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$status, $id]);
    }

    /**
     * Delete laporan camat
     */
    public function delete($id) {
        $query = "DELETE FROM laporan_camat WHERE id_laporan_camat = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$id]);
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
        $stmt->execute($params);
        
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
        $stmt->execute($params);
        $result = $stmt->get_result()->fetch_assoc();
        
        return $result['total'];
    }
}