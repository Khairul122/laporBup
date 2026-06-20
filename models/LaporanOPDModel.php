<?php

require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/BaseModel.php';

class LaporanOPDModel extends BaseModel {
    
    public function getAllLaporanByUser($userId) {
        $query = "SELECT lo.*, u.username, u.jabatan
                  FROM laporan_opd lo
                  JOIN users u ON lo.id_user = u.id_user
                  WHERE lo.id_user = ?
                  ORDER BY lo.created_at DESC";

        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        $laporan = [];
        while ($row = $result->fetch_assoc()) {
            $laporan[] = $row;
        }

        return $laporan;
    }

    public function getAllLaporan() {
        $query = "SELECT lo.*, u.username, u.jabatan
                  FROM laporan_opd lo
                  JOIN users u ON lo.id_user = u.id_user
                  ORDER BY lo.created_at DESC";

        return $this->fetchAll($query);
    }

    
    public function getLaporanById($id) {
        $query = "SELECT lo.*, u.username, u.jabatan, u.email
                  FROM laporan_opd lo
                  JOIN users u ON lo.id_user = u.id_user
                  WHERE lo.id_laporan_opd = ?";

        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_assoc();
    }

    
    public function createLaporan($data) {
        $query = "INSERT INTO laporan_opd (id_user, nama_opd, nama_kegiatan, uraian_laporan, tujuan, upload_file, status_laporan)
                  VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->db->prepare($query);
        $stmt->bind_param("issssss",
            $data['id_user'],
            $data['nama_opd'],
            $data['nama_kegiatan'],
            $data['uraian_laporan'],
            $data['tujuan'],
            $data['upload_file'],
            $data['status_laporan']
        );

        return $stmt->execute();
    }

    
    public function updateLaporan($id, $data) {
        $query = "UPDATE laporan_opd
                  SET nama_opd = ?, nama_kegiatan = ?, uraian_laporan = ?, tujuan = ?, upload_file = ?, status_laporan = ?
                  WHERE id_laporan_opd = ? AND id_user = ?";

        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ssssssii",
            $data['nama_opd'],
            $data['nama_kegiatan'],
            $data['uraian_laporan'],
            $data['tujuan'],
            $data['upload_file'],
            $data['status_laporan'],
            $id,
            $data['id_user']
        );

        return $stmt->execute();
    }

    
    public function updateStatus($id, $status) {
        $query = "UPDATE laporan_opd SET status_laporan = ? WHERE id_laporan_opd = ?";

        $stmt = $this->db->prepare($query);
        $stmt->bind_param("si", $status, $id);

        return $stmt->execute();
    }

    
    public function deleteLaporan($id, $userId) {
        $query = "DELETE FROM laporan_opd WHERE id_laporan_opd = ? AND id_user = ?";

        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ii", $id, $userId);

        return $stmt->execute();
    }

    
    public function getLaporanStatsByUser($userId) {
        $query = "SELECT
                    SUM(CASE WHEN status_laporan = 'baru' THEN 1 ELSE 0 END) as total_baru,
                    SUM(CASE WHEN status_laporan = 'diproses' THEN 1 ELSE 0 END) as total_diproses,
                    SUM(CASE WHEN status_laporan = 'selesai' THEN 1 ELSE 0 END) as total_selesai,
                    COUNT(*) as total_semua
                  FROM laporan_opd
                  WHERE id_user = ?";

        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_assoc();
    }

    
    public function searchLaporan($keyword, $userId = null) {
        $query = "SELECT lo.*, u.username, u.jabatan
                  FROM laporan_opd lo
                  JOIN users u ON lo.id_user = u.id_user
                  WHERE (lo.nama_opd LIKE ? OR lo.nama_kegiatan LIKE ? OR lo.uraian_laporan LIKE ?)";

        if ($userId) {
            $query .= " AND lo.id_user = ?";
        }

        $query .= " ORDER BY lo.created_at DESC";

        $searchTerm = "%$keyword%";

        if ($userId) {
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("sssi", $searchTerm, $searchTerm, $searchTerm, $userId);
        } else {
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        $laporan = [];
        while ($row = $result->fetch_assoc()) {
            $laporan[] = $row;
        }

        return $laporan;
    }

    
    public function getLaporanByStatus($status, $userId = null) {
        $query = "SELECT lo.*, u.username, u.jabatan
                  FROM laporan_opd lo
                  JOIN users u ON lo.id_user = u.id_user
                  WHERE lo.status_laporan = ?";

        if ($userId) {
            $query .= " AND lo.id_user = ?";
        }

        $query .= " ORDER BY lo.created_at DESC";

        if ($userId) {
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("si", $status, $userId);
        } else {
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("s", $status);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        $laporan = [];
        while ($row = $result->fetch_assoc()) {
            $laporan[] = $row;
        }

        return $laporan;
    }

    
    public function getRecentLaporan($userId = null, $limit = 5) {
        $query = "SELECT lo.*, u.username, u.jabatan
                  FROM laporan_opd lo
                  JOIN users u ON lo.id_user = u.id_user";

        if ($userId) {
            $query .= " WHERE lo.id_user = ?";
        }

        $query .= " ORDER BY lo.created_at DESC LIMIT ?";

        if ($userId) {
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("ii", $userId, $limit);
        } else {
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $limit);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        $laporan = [];
        while ($row = $result->fetch_assoc()) {
            $laporan[] = $row;
        }

        return $laporan;
    }

    
    public function handleFileUpload($file, $maxSize = 52428800) { 
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        
        if ($file['size'] > $maxSize) {
            return null;
        }

        
        $allowedTypes = [
            
            'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx',
            
            'jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg',
            
            'mp4', 'avi', 'mov', 'wmv', 'flv', 'mkv', 'webm', '3gp'
        ];
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($fileExtension, $allowedTypes)) {
            return null;
        }

        
        $newFileName = 'laporan_opd_' . time() . '_' . uniqid() . '.' . $fileExtension;
        $uploadPath = 'uploads/laporan_opd/';

        
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        
        if (move_uploaded_file($file['tmp_name'], $uploadPath . $newFileName)) {
            return $uploadPath . $newFileName;
        }

        return null;
    }

    
    public function deleteFile($filePath) {
        if (file_exists($filePath)) {
            return unlink($filePath);
        }
        return false;
    }
}