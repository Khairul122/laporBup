<?php

require_once __DIR__ . '/../config/koneksi.php';

/**
 * Base model: menyediakan koneksi database dan helper query
 * prepared-statement generik agar tidak diduplikasi di setiap model.
 */
class BaseModel {
    protected $db;

    public function __construct() {
        $this->db = getKoneksi();
    }

    /**
     * Jalankan prepared statement dan kembalikan mysqli_stmt yang sudah dieksekusi.
     * @param string $sql
     * @param string $types contoh: "iss" untuk int, string, string
     * @param array $params
     * @return mysqli_stmt
     */
    protected function query($sql, $types = '', $params = []) {
        $stmt = $this->db->prepare($sql);
        if ($stmt === false) {
            throw new mysqli_sql_exception('Prepare failed: ' . $this->db->error . ' SQL: ' . $sql);
        }
        if ($types !== '' && !empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        return $stmt;
    }

    /**
     * Ambil semua baris hasil query sebagai array asosiatif.
     */
    protected function fetchAll($sql, $types = '', $params = []) {
        $stmt = $this->query($sql, $types, $params);
        $result = $stmt->get_result();
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $rows;
    }

    /**
     * Ambil satu baris hasil query sebagai array asosiatif, atau null jika tidak ada.
     */
    protected function fetchOne($sql, $types = '', $params = []) {
        $stmt = $this->query($sql, $types, $params);
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row ?: null;
    }

    /**
     * Helper pagination sederhana.
     * @return array{data: array, total: int, page: int, limit: int, total_pages: int}
     */
    protected function paginate($baseQuery, $countQuery, $types, $params, $page = 1, $limit = 10) {
        $page = max(1, (int) $page);
        $limit = max(1, (int) $limit);
        $offset = ($page - 1) * $limit;

        $total = $this->fetchOne($countQuery, $types, $params);
        $totalRows = $total ? (int) reset($total) : 0;

        $dataQuery = $baseQuery . ' LIMIT ? OFFSET ?';
        $dataTypes = $types . 'ii';
        $dataParams = array_merge($params, [$limit, $offset]);

        return [
            'data' => $this->fetchAll($dataQuery, $dataTypes, $dataParams),
            'total' => $totalRows,
            'page' => $page,
            'limit' => $limit,
            'total_pages' => (int) ceil($totalRows / $limit),
        ];
    }
}
