<?php

require_once __DIR__ . '/../config/koneksi.php';

class BaseModel {
    protected $db;

    public function __construct() {
        $this->db = getKoneksi();
    }

    
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

    
    protected function fetchAll($sql, $types = '', $params = []) {
        $stmt = $this->query($sql, $types, $params);
        $result = $stmt->get_result();
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $rows;
    }

    
    protected function fetchOne($sql, $types = '', $params = []) {
        $stmt = $this->query($sql, $types, $params);
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row ?: null;
    }

    
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
