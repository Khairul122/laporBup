<?php

require_once __DIR__ . '/../config/koneksi.php';

class WAGatewayModel {
    private $conn;
    private $table = "wagateway";

    // Fonnte API Configuration
    private $fonnte_token = "mebMc5vfWw1ZpMbh1n77";
    private $api_url = "https://api.fonnte.com";

    public function __construct() {
        $this->conn = getKoneksi();
    }

    public function getAllMessages($page = 1, $limit = 10, $search = '', $status_filter = '', $date_filter = '') {
        $offset = ($page - 1) * $limit;

        $whereClause = "";
        $params = [];

        if (!empty($search)) {
            $whereClause .= " WHERE (no_tujuan LIKE ? OR pesan LIKE ?)";
            $searchTerm = '%' . $search . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        if (!empty($status_filter)) {
            $whereClause .= ($whereClause ? " AND" : " WHERE") . " status = ?";
            $params[] = $status_filter;
        }

        if (!empty($date_filter)) {
            $whereClause .= ($whereClause ? " AND" : " WHERE") . " DATE(tanggal_kirim) = ?";
            $params[] = $date_filter;
        }

        // Get total data
        $countQuery = "SELECT COUNT(*) as total FROM " . $this->table . $whereClause;
        $result = query($countQuery, $params);
        $totalData = $result->fetch_assoc()['total'];

        // Get data with pagination
        $query = "SELECT * FROM " . $this->table . $whereClause . " ORDER BY created_at DESC LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
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

    public function getMessageById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id_wagateway = ? LIMIT 1";
        $result = query($query, [$id]);
        return $result->fetch_assoc();
    }

    public function createMessage($no_tujuan, $pesan, $status = 'pending') {
        $currentUserId = $_SESSION['user_id'] ?? null;
        $query = "INSERT INTO " . $this->table . " (no_tujuan, pesan, tanggal_kirim, status, created_by, created_at) VALUES (?, ?, NOW(), ?, ?, NOW())";
        $result = query($query, [$no_tujuan, $pesan, $status, $currentUserId]);

        if ($result) {
            return $this->conn->insert_id;
        }
        return false;
    }

    public function updateMessage($id, $no_tujuan, $pesan, $status) {
        $query = "UPDATE " . $this->table . " SET no_tujuan = ?, pesan = ?, status = ? WHERE id_wagateway = ?";
        $result = query($query, [$no_tujuan, $pesan, $status, $id]);
        return $result;
    }

    public function updateStatus($id, $status) {
        $query = "UPDATE " . $this->table . " SET status = ? WHERE id_wagateway = ?";
        $result = query($query, [$status, $id]);
        return $result;
    }

    public function deleteMessage($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id_wagateway = ?";
        $result = query($query, [$id]);
        return $result;
    }

    public function sendWhatsAppMessage($no_tujuan, $pesan) {
        // Format nomor telepon
        $no_tujuan = $this->formatPhoneNumber($no_tujuan);

        $data = [
            'target' => $no_tujuan,
            'message' => $pesan,
            'countryCode' => '62'
        ];

        $response = $this->makeAPICall('/send', $data);
        return $response;
    }

    public function sendMessageAndSave($no_tujuan, $pesan) {
        // Simpan ke database terlebih dahulu
        $messageId = $this->createMessage($no_tujuan, $pesan, 'pending');

        if($messageId) {
            // Kirim via WhatsApp
            $response = $this->sendWhatsAppMessage($no_tujuan, $pesan);

            if($response && isset($response['success']) && $response['success']) {
                // Update status jika berhasil
                $this->updateStatus($messageId, 'sent');
                return ['success' => true, 'message' => 'Pesan berhasil dikirim', 'data' => $response];
            } else {
                // Update status jika gagal
                $this->updateStatus($messageId, 'failed');
                return ['success' => false, 'message' => 'Gagal mengirim pesan: ' . ($response['message'] ?? 'Unknown error')];
            }
        }

        return ['success' => false, 'message' => 'Gagal menyimpan pesan ke database'];
    }

    private function formatPhoneNumber($phone) {
        // Remove any non-numeric characters
        $phone = preg_replace('/\D/', '', $phone);

        // Add country code if not present
        if(substr($phone, 0, 2) !== '62') {
            if(substr($phone, 0, 1) === '0') {
                $phone = '62' . substr($phone, 1);
            } else {
                $phone = '62' . $phone;
            }
        }

        return $phone;
    }

    private function makeAPICall($endpoint, $data = []) {
        $url = $this->api_url . $endpoint;

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => http_build_query($data),
            CURLOPT_HTTPHEADER => [
                'Authorization: ' . $this->fonnte_token
            ],
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error_msg = curl_errno($curl) ? curl_error($curl) : null;
        curl_close($curl);

        if($error_msg) {
            return ['success' => false, 'message' => 'CURL Error: ' . $error_msg];
        }

        if(!$response) {
            return ['success' => false, 'message' => 'Empty response from API'];
        }

        $decodedResponse = json_decode($response, true);
        if(json_last_error() !== JSON_ERROR_NONE) {
            return ['success' => false, 'message' => 'Invalid JSON response', 'raw_response' => $response];
        }

        // Debug: Log the actual response to understand the format
        error_log("Fonnte API Response: " . $response);

        // Check multiple possible success indicators
        $isSuccess = false;

        // Check for various success indicators from Fonnte API
        if(isset($decodedResponse['status']) && $decodedResponse['status'] === true) {
            $isSuccess = true;
        } elseif(isset($decodedResponse['id']) && !empty($decodedResponse['id'])) {
            // If there's an ID, it usually means message was sent successfully
            $isSuccess = true;
        } elseif(isset($decodedResponse['target']) && isset($decodedResponse['message'])) {
            // If response contains target and message, it's likely successful
            $isSuccess = true;
        }

        if($isSuccess) {
            return ['success' => true, 'data' => $decodedResponse];
        } else {
            $errorMessage = isset($decodedResponse['reason']) ? $decodedResponse['reason'] :
                           (isset($decodedResponse['message']) ? $decodedResponse['message'] :
                           (isset($decodedResponse['error']) ? $decodedResponse['error'] : 'Unknown error'));

            return ['success' => false, 'message' => $errorMessage, 'response' => $decodedResponse, 'raw_response' => $response];
        }
    }

    public function getMessageStatistics() {
        $stats = [];

        // Total messages
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        $result = query($query);
        $stats['total'] = $result->fetch_assoc()['total'];

        // Messages by status
        $statuses = ['sent', 'pending', 'failed'];
        foreach($statuses as $status) {
            $query = "SELECT COUNT(*) as count FROM " . $this->table . " WHERE status = ?";
            $result = query($query, [$status]);
            $stats[$status] = $result->fetch_assoc()['count'];
        }

        // Messages today
        $query = "SELECT COUNT(*) as today FROM " . $this->table . " WHERE DATE(tanggal_kirim) = CURDATE()";
        $result = query($query);
        $stats['today'] = $result->fetch_assoc()['today'];

        return $stats;
    }

    public function getMessagesByStatus($status) {
        $query = "SELECT * FROM " . $this->table . " WHERE status = ? ORDER BY created_at DESC";
        $result = query($query, [$status]);

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }

    public function getContacts() {
        $query = "SELECT
                    u.id_user,
                    u.username,
                    u.no_telp,
                    u.jabatan,
                    u.role,
                    CASE
                        WHEN u.role = 'camat' THEN CONCAT('Camat ', u.jabatan)
                        WHEN u.role = 'opd' THEN u.jabatan
                        ELSE u.username
                    END as display_name
                  FROM users u
                  WHERE u.role IN ('camat', 'opd') AND u.no_telp IS NOT NULL AND u.no_telp != ''
                  ORDER BY u.role, u.username";

        $result = query($query);
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }

    public function searchContacts($keyword) {
        $searchTerm = '%' . $keyword . '%';
        $query = "SELECT
                    u.id_user,
                    u.username,
                    u.no_telp,
                    u.jabatan,
                    u.role,
                    CASE
                        WHEN u.role = 'camat' THEN CONCAT('Camat ', u.jabatan)
                        WHEN u.role = 'opd' THEN u.jabatan
                        ELSE u.username
                    END as display_name
                  FROM users u
                  WHERE u.role IN ('camat', 'opd')
                    AND u.no_telp IS NOT NULL
                    AND u.no_telp != ''
                    AND (u.username LIKE ? OR u.jabatan LIKE ? OR u.no_telp LIKE ?)
                  ORDER BY u.role, u.username
                  LIMIT 10";

        $result = query($query, [$searchTerm, $searchTerm, $searchTerm]);
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }

    public function getMessagesForExport($status_filter = '', $date_filter = '') {
        $whereClause = "";
        $params = [];

        if (!empty($status_filter)) {
            $whereClause = " WHERE status = ?";
            $params[] = $status_filter;
        }

        if (!empty($date_filter)) {
            $whereClause .= ($whereClause ? " AND" : " WHERE") . " DATE(tanggal_kirim) = ?";
            $params[] = $date_filter;
        }

        $query = "SELECT id_wagateway, no_tujuan, pesan, tanggal_kirim, status, created_at FROM " . $this->table . $whereClause . " ORDER BY created_at DESC";
        $result = query($query, $params);

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }

    // Method untuk test kirim pesan tanpa save ke database
    public function testSendMessage($no_tujuan, $pesan) {
        $no_tujuan = $this->formatPhoneNumber($no_tujuan);

        $data = [
            'target' => $no_tujuan,
            'message' => $pesan,
            'countryCode' => '62'
        ];

        // Call API and get detailed response for debugging
        $result = $this->makeAPICallWithDebug('/send', $data);
        return $result;
    }

    // Special method for debugging that returns raw response
    private function makeAPICallWithDebug($endpoint, $data = []) {
        $url = $this->api_url . $endpoint;

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => http_build_query($data),
            CURLOPT_HTTPHEADER => [
                'Authorization: ' . $this->fonnte_token
            ],
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error_msg = curl_errno($curl) ? curl_error($curl) : null;
        curl_close($curl);

        // Prepare debug info
        $debugInfo = [
            'url' => $url,
            'sent_data' => $data,
            'http_code' => $httpCode,
            'curl_error' => $error_msg,
            'raw_response' => $response,
            'token_used' => substr($this->fonnte_token, 0, 10) . '...'
        ];

        if($error_msg) {
            return [
                'success' => false,
                'message' => 'CURL Error: ' . $error_msg,
                'debug' => $debugInfo
            ];
        }

        if(!$response) {
            return [
                'success' => false,
                'message' => 'Empty response from API',
                'debug' => $debugInfo
            ];
        }

        $decodedResponse = json_decode($response, true);
        if(json_last_error() !== JSON_ERROR_NONE) {
            return [
                'success' => false,
                'message' => 'Invalid JSON response: ' . json_last_error_msg(),
                'debug' => $debugInfo
            ];
        }

        $debugInfo['parsed_response'] = $decodedResponse;

        // Check for success - let's be more lenient and see what we actually get
        $isSuccess = false;
        $successReason = '';

        if(isset($decodedResponse['status']) && $decodedResponse['status'] === true) {
            $isSuccess = true;
            $successReason = 'status=true';
        } elseif(isset($decodedResponse['id']) && !empty($decodedResponse['id'])) {
            $isSuccess = true;
            $successReason = 'has ID';
        } elseif($httpCode === 200 && !isset($decodedResponse['status']) && !isset($decodedResponse['error'])) {
            // If HTTP 200 and no explicit error, consider it success
            $isSuccess = true;
            $successReason = 'HTTP 200 with no error';
        }

        if($isSuccess) {
            return [
                'success' => true,
                'data' => $decodedResponse,
                'success_reason' => $successReason,
                'debug' => $debugInfo
            ];
        } else {
            $errorMessage = isset($decodedResponse['reason']) ? $decodedResponse['reason'] :
                           (isset($decodedResponse['message']) ? $decodedResponse['message'] :
                           (isset($decodedResponse['error']) ? $decodedResponse['error'] : 'No explicit error found'));

            return [
                'success' => false,
                'message' => $errorMessage,
                'debug' => $debugInfo
            ];
        }
    }

    public function getMessageStats() {
        return $this->getMessageStatistics();
    }
}