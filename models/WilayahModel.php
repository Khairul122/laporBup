<?php

require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/BaseModel.php';

class WilayahModel extends BaseModel {

    public function getAllKecamatan($page = 1, $limit = 10, $search = '') {
        $page = max(1, (int)$page);
        $limit = max(1, (int)$limit);

        if (!empty($search)) {
            $like = '%' . $search . '%';
            $result = $this->paginate(
                "SELECT * FROM kecamatan WHERE nama_kecamatan LIKE ? ORDER BY nama_kecamatan ASC",
                "SELECT COUNT(*) FROM kecamatan WHERE nama_kecamatan LIKE ?",
                "s",
                [$like],
                $page,
                $limit
            );
        } else {
            $result = $this->paginate(
                "SELECT * FROM kecamatan ORDER BY nama_kecamatan ASC",
                "SELECT COUNT(*) FROM kecamatan",
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

    public function getKecamatanById($id_kecamatan) {
        return $this->fetchOne("SELECT * FROM kecamatan WHERE id_kecamatan = ?", "i", [(int)$id_kecamatan]);
    }

    public function insertKecamatan($data) {
        $stmt = $this->query(
            "INSERT INTO kecamatan (nama_kecamatan, created_at, updated_at) VALUES (?, NOW(), NOW())",
            "s",
            [$data['nama_kecamatan']]
        );
        $success = $stmt->affected_rows > 0;
        $insertId = $this->db->insert_id;
        $stmt->close();
        return $success ? $insertId : false;
    }

    public function updateKecamatan($id_kecamatan, $data) {
        $stmt = $this->query(
            "UPDATE kecamatan SET nama_kecamatan = ?, updated_at = NOW() WHERE id_kecamatan = ?",
            "si",
            [$data['nama_kecamatan'], (int)$id_kecamatan]
        );
        $success = $stmt->affected_rows >= 0;
        $stmt->close();
        return $success;
    }

    public function deleteKecamatan($id_kecamatan) {
        $id_kecamatan = (int)$id_kecamatan;
        $this->db->begin_transaction();

        try {
            $row = $this->fetchOne("SELECT COUNT(*) as count FROM desa WHERE id_kecamatan = ?", "i", [$id_kecamatan]);
            $relatedDesaCount = $row ? (int)$row['count'] : 0;

            if ($relatedDesaCount > 0) {
                $stmtDesa = $this->query("DELETE FROM desa WHERE id_kecamatan = ?", "i", [$id_kecamatan]);
                $stmtDesa->close();
            }

            $stmtKec = $this->query("DELETE FROM kecamatan WHERE id_kecamatan = ?", "i", [$id_kecamatan]);
            $stmtKec->close();

            $this->db->commit();

            if ($relatedDesaCount > 0) {
                $message = "Kecamatan dan " . $relatedDesaCount . " desa terkait berhasil dihapus";
            } else {
                $message = "Kecamatan berhasil dihapus";
            }

            return ['success' => true, 'message' => $message];

        } catch (Exception $e) {
            $this->db->rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function getAllDesa($page = 1, $limit = 10, $search = '', $kecamatan_filter = '') {
        $page = max(1, (int)$page);
        $limit = max(1, (int)$limit);

        $params = [];
        $types = "";
        
        $baseQuery = "SELECT d.*, k.nama_kecamatan FROM desa d INNER JOIN kecamatan k ON d.id_kecamatan = k.id_kecamatan WHERE 1=1";
        $countQuery = "SELECT COUNT(*) FROM desa d INNER JOIN kecamatan k ON d.id_kecamatan = k.id_kecamatan WHERE 1=1";

        if (!empty($search)) {
            $like = '%' . $search . '%';
            $baseQuery .= " AND (d.nama_desa LIKE ? OR k.nama_kecamatan LIKE ?)";
            $countQuery .= " AND (d.nama_desa LIKE ? OR k.nama_kecamatan LIKE ?)";
            $params[] = $like;
            $params[] = $like;
            $types .= "ss";
        }

        if (!empty($kecamatan_filter)) {
            $baseQuery .= " AND d.id_kecamatan = ?";
            $countQuery .= " AND d.id_kecamatan = ?";
            $params[] = (int)$kecamatan_filter;
            $types .= "i";
        }

        $baseQuery .= " AND (d.nama_desa IS NOT NULL AND d.nama_desa != '')";
        $countQuery .= " AND (d.nama_desa IS NOT NULL AND d.nama_desa != '')";
        $baseQuery .= " ORDER BY k.nama_kecamatan ASC, d.nama_desa ASC";

        $result = $this->paginate($baseQuery, $countQuery, $types, $params, $page, $limit);

        return [
            'data' => $result['data'],
            'total' => $result['total'],
            'per_page' => $result['limit'],
            'current_page' => $result['page'],
            'total_pages' => $result['total_pages']
        ];
    }

    public function getDesaById($id_desa) {
        return $this->fetchOne(
            "SELECT d.*, k.nama_kecamatan FROM desa d LEFT JOIN kecamatan k ON d.id_kecamatan = k.id_kecamatan WHERE d.id_desa = ?",
            "i",
            [(int)$id_desa]
        );
    }

    public function insertDesa($data) {
        $stmt = $this->query(
            "INSERT INTO desa (id_kecamatan, nama_desa, created_at, updated_at) VALUES (?, ?, NOW(), NOW())",
            "is",
            [(int)$data['id_kecamatan'], $data['nama_desa']]
        );
        $success = $stmt->affected_rows > 0;
        $insertId = $this->db->insert_id;
        $stmt->close();
        return $success ? $insertId : false;
    }

    public function updateDesa($id_desa, $data) {
        $stmt = $this->query(
            "UPDATE desa SET id_kecamatan = ?, nama_desa = ?, updated_at = NOW() WHERE id_desa = ?",
            "isi",
            [(int)$data['id_kecamatan'], $data['nama_desa'], (int)$id_desa]
        );
        $success = $stmt->affected_rows >= 0;
        $stmt->close();
        return $success;
    }

    public function deleteDesa($id_desa) {
        $stmt = $this->query("DELETE FROM desa WHERE id_desa = ?", "i", [(int)$id_desa]);
        $success = $stmt->affected_rows > 0;
        $stmt->close();
        if ($success) {
            return ['success' => true, 'message' => 'Desa berhasil dihapus'];
        }
        return ['success' => false, 'message' => 'Gagal menghapus desa'];
    }

    public function getKecamatanOptions() {
        return $this->fetchAll("SELECT id_kecamatan, nama_kecamatan FROM kecamatan ORDER BY nama_kecamatan ASC");
    }

    public function getStatistics() {
        $rowKec = $this->fetchOne("SELECT COUNT(*) as total_kecamatan FROM kecamatan");
        $rowDesa = $this->fetchOne("SELECT COUNT(*) as total_desa FROM desa");
        return [
            'total_kecamatan' => $rowKec ? (int)$rowKec['total_kecamatan'] : 0,
            'total_desa' => $rowDesa ? (int)$rowDesa['total_desa'] : 0
        ];
    }

    public function getDesaByKecamatan($id_kecamatan) {
        return $this->fetchAll(
            "SELECT id_desa, nama_desa FROM desa WHERE id_kecamatan = ? ORDER BY nama_desa ASC",
            "i",
            [(int)$id_kecamatan]
        );
    }

    public function getKecamatanWithDesaStats($id_kecamatan) {
        return $this->fetchOne(
            "SELECT k.nama_kecamatan, COUNT(d.id_desa) as desa_count, GROUP_CONCAT(d.nama_desa ORDER BY d.nama_desa SEPARATOR '\n') as desa_list FROM kecamatan k LEFT JOIN desa d ON k.id_kecamatan = d.id_kecamatan WHERE k.id_kecamatan = ? GROUP BY k.id_kecamatan, k.nama_kecamatan",
            "i",
            [(int)$id_kecamatan]
        );
    }

    public function getRelatedDesa(int $id_kecamatan): array {
        return $this->fetchAll(
            "SELECT id_desa, nama_desa FROM desa WHERE id_kecamatan = ? ORDER BY nama_desa ASC",
            "i",
            [$id_kecamatan]
        );
    }
}