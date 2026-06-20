<?php

require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/BaseModel.php';

class OPDModel extends BaseModel {

    public function getAllOPD($page = 1, $limit = 10, $search = '') {
        $page = max(1, (int)$page);
        $limit = max(1, (int)$limit);

        if (!empty($search)) {
            $like = '%' . $search . '%';
            $result = $this->paginate(
                "SELECT * FROM opd WHERE nama_opd LIKE ? ORDER BY nama_opd ASC",
                "SELECT COUNT(*) FROM opd WHERE nama_opd LIKE ?",
                "s",
                [$like],
                $page,
                $limit
            );
        } else {
            $result = $this->paginate(
                "SELECT * FROM opd ORDER BY nama_opd ASC",
                "SELECT COUNT(*) FROM opd",
                "",
                [],
                $page,
                $limit
            );
        }

        return [
            'data' => $result['data'],
            'total' => $result['total'],
            'per_page' => $result['limit'],
            'current_page' => $result['page'],
            'total_pages' => $result['total_pages']
        ];
    }

    public function getOPDById($id_opd) {
        return $this->fetchOne("SELECT * FROM opd WHERE id_opd = ?", "i", [(int)$id_opd]);
    }

    public function createOPD($data) {
        $stmt = $this->query("INSERT INTO opd (nama_opd) VALUES (?)", "s", [$data['nama_opd']]);
        $success = $stmt->affected_rows > 0;
        $insertId = $this->db->insert_id;
        $stmt->close();
        return $success ? $insertId : false;
    }

    public function updateOPD($id_opd, $data) {
        $stmt = $this->query("UPDATE opd SET nama_opd = ? WHERE id_opd = ?", "si", [$data['nama_opd'], (int)$id_opd]);
        $success = $stmt->affected_rows >= 0;
        $stmt->close();
        return $success;
    }

    public function deleteOPD($id_opd) {
        $stmt = $this->query("DELETE FROM opd WHERE id_opd = ?", "i", [(int)$id_opd]);
        $success = $stmt->affected_rows > 0;
        $stmt->close();
        if ($success) {
            return ['success' => true, 'message' => 'OPD berhasil dihapus'];
        }
        return ['success' => false, 'message' => 'Gagal menghapus OPD'];
    }

    public function checkNamaOPDExists($nama_opd, $exclude_id = null) {
        if ($exclude_id) {
            $row = $this->fetchOne(
                "SELECT COUNT(*) as count FROM opd WHERE nama_opd = ? AND id_opd != ?",
                "si",
                [$nama_opd, (int)$exclude_id]
            );
        } else {
            $row = $this->fetchOne(
                "SELECT COUNT(*) as count FROM opd WHERE nama_opd = ?",
                "s",
                [$nama_opd]
            );
        }
        return $row ? ((int)$row['count'] > 0) : false;
    }
}