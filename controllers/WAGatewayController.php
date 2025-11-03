<?php

require_once 'models/WAGatewayModel.php';

class WAGatewayController {
    private $model;

    public function __construct() {
        $this->model = new WAGatewayModel();
    }

    /**
     * Cek apakah user sudah login
     */
    private function isLoggedIn() {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }

    /**
     * Require login untuk mengakses halaman
     */
    private function requireLogin() {
        if (!$this->isLoggedIn()) {
            $response = [
                'success' => false,
                'message' => 'Silakan login terlebih dahulu',
                'redirect' => 'index.php'
            ];

            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                header('Content-Type: application/json');
                echo json_encode($response);
                exit;
            } else {
                header('Location: index.php');
                exit;
            }
        }
    }

    /**
     * Require role admin untuk mengakses halaman
     */
    private function requireAdmin() {
        $this->requireLogin();

        if ($_SESSION['role'] !== 'admin') {
            $response = [
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke halaman ini'
            ];

            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                header('Content-Type: application/json');
                echo json_encode($response);
                exit;
            } else {
                header('Location: index.php?controller=dashboard&action=admin');
                exit;
            }
        }
    }

    public function index() {
        $this->requireAdmin();

        // Get filter parameters from URL
        $search = $_GET['search'] ?? '';
        $status_filter = $_GET['status'] ?? '';
        $date_filter = $_GET['date'] ?? '';

        $result = $this->model->getAllMessages(1, 10, $search, $status_filter, $date_filter);
        $contacts = $this->model->getContacts();

        require_once 'views/wagateway/index.php';
    }

    public function form() {
        $this->requireAdmin();

        $id = $_GET['id'] ?? null;
        $message = null;

        if ($id) {
            $message = $this->model->getMessageById($id);
            if (!$message) {
                $_SESSION['error'] = 'Pesan tidak ditemukan';
                header('Location: index.php?controller=waGateway');
                exit;
            }
        }

        // Get contacts for autocomplete
        $contacts = $this->model->getContacts();

        require_once 'views/wagateway/form.php';
    }

    public function sendMessage() {
        $this->requireAdmin();

        header('Content-Type: application/json');

        $no_tujuan = $_POST['no_tujuan'] ?? '';
        $pesan = $_POST['pesan'] ?? '';
        $id = $_POST['id'] ?? null;

        if(empty($no_tujuan) || empty($pesan)) {
            echo json_encode(['success' => false, 'message' => 'Nomor tujuan dan pesan tidak boleh kosong']);
            exit;
        }

        // Format nomor telepon
        $no_tujuan = $this->formatPhoneNumber($no_tujuan);

        // Validasi format nomor telepon
        if(!preg_match('/^(62)[0-9]{9,13}$/', $no_tujuan)) {
            echo json_encode(['success' => false, 'message' => 'Format nomor telepon tidak valid. Gunakan format: 08xxxxxxxxxx']);
            exit;
        }

        // Update atau create message
        if ($id) {
            // Update existing message
            $result = $this->model->updateMessage($id, $no_tujuan, $pesan, 'pending');
            $message_id = $id;
        } else {
            // Create new message
            $message_id = $this->model->createMessage($no_tujuan, $pesan, 'pending');
        }

        if(!$message_id) {
            echo json_encode(['success' => false, 'message' => 'Gagal menyimpan pesan ke database']);
            exit;
        }

        // Kirim via WhatsApp API
        $result = $this->model->sendWhatsAppMessage($no_tujuan, $pesan);

        if($result && isset($result['success']) && $result['success']) {
            // Update status jika berhasil
            $this->model->updateStatus($message_id, 'sent');
            echo json_encode(['success' => true, 'message' => 'Pesan berhasil dikirim', 'data' => $result]);
        } else {
            // Update status jika gagal
            $this->model->updateStatus($message_id, 'failed');
            $errorMessage = isset($result['message']) ? $result['message'] : 'Gagal mengirim pesan';
            echo json_encode(['success' => false, 'message' => $errorMessage]);
        }
        exit;
    }

    public function delete() {
        $this->requireAdmin();

        header('Content-Type: application/json');

        $id = $_POST['id'] ?? '';
        if(empty($id)) {
            echo json_encode(['success' => false, 'message' => 'ID pesan tidak valid']);
            exit;
        }

        $result = $this->model->deleteMessage($id);
        if($result) {
            echo json_encode(['success' => true, 'message' => 'Pesan berhasil dihapus']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menghapus pesan']);
        }
        exit;
    }

    public function getMessages() {
        $this->requireAdmin();

        header('Content-Type: application/json');

        try {
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
            $search = $_GET['search'] ?? '';
            $status_filter = $_GET['status'] ?? '';
            $date_filter = $_GET['date'] ?? '';

            $result = $this->model->getAllMessages($page, $limit, $search, $status_filter, $date_filter);

            echo json_encode([
                'success' => true,
                'data' => $result
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
        exit;
    }

    public function searchContacts() {
        $this->requireAdmin();

        header('Content-Type: application/json');

        try {
            $keyword = $_GET['q'] ?? '';

            if (strlen($keyword) < 2) {
                echo json_encode([
                    'success' => true,
                    'data' => []
                ]);
                exit;
            }

            $data = $this->model->searchContacts($keyword);

            echo json_encode([
                'success' => true,
                'data' => $data
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
        exit;
    }

    
    public function export() {
        $this->requireAdmin();

        try {
            $status_filter = $_GET['status'] ?? '';
            $date_filter = $_GET['date'] ?? '';
            $data = $this->model->getMessagesForExport($status_filter, $date_filter);

            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="wa_gateway_messages_' . date('Y-m-d') . '.csv"');

            $output = fopen('php://output', 'w');

            // Header
            fputcsv($output, ['ID', 'Nomor Tujuan', 'Pesan', 'Tanggal Kirim', 'Status', 'Created At']);

            // Data
            foreach ($data as $row) {
                fputcsv($output, [
                    $row['id_wagateway'],
                    $row['no_tujuan'],
                    $row['pesan'],
                    date('d/m/Y H:i', strtotime($row['tanggal_kirim'])),
                    $row['status'],
                    date('d/m/Y H:i', strtotime($row['created_at']))
                ]);
            }

            fclose($output);
            exit;
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error export: ' . $e->getMessage();
            header('Location: index.php?controller=waGateway');
            exit;
        }
    }

    public function bulkSend() {
        $this->requireAdmin();

        header('Content-Type: application/json');

        try {
            $message = trim($_POST['message'] ?? '');
            $contacts = $_POST['contacts'] ?? [];

            if (empty($message)) {
                throw new Exception('Pesan harus diisi');
            }

            if (empty($contacts)) {
                throw new Exception('Pilih minimal satu kontak');
            }

            $successCount = 0;
            $failedCount = 0;
            $results = [];

            foreach ($contacts as $contact) {
                // Parse contact data (format: "name|phone")
                $contactData = explode('|', $contact);
                if (count($contactData) >= 2) {
                    $phone = trim($contactData[1]);
                    $name = trim($contactData[0]);

                    // Format phone number
                    $phone = $this->formatPhoneNumber($phone);

                    if (preg_match('/^(62)[0-9]{9,13}$/', $phone)) {
                        // Save to database
                        $messageId = $this->model->createMessage($phone, $message, 'pending');

                        if ($messageId) {
                            // Send to API
                            $result = $this->model->sendWhatsAppMessage($phone, $message);

                            if ($result && isset($result['success']) && $result['success']) {
                                $this->model->updateStatus($messageId, 'sent');
                                $successCount++;
                            } else {
                                $this->model->updateStatus($messageId, 'failed');
                                $failedCount++;
                            }

                            $results[] = [
                                'name' => $name,
                                'phone' => $phone,
                                'status' => $result && isset($result['success']) && $result['success'] ? 'success' : 'failed',
                                'message' => $result['message'] ?? 'Unknown error'
                            ];
                        } else {
                            $failedCount++;
                        }
                    } else {
                        $failedCount++;
                        $results[] = [
                            'name' => $name,
                            'phone' => $phone,
                            'status' => 'failed',
                            'message' => 'Invalid phone number format'
                        ];
                    }
                }
            }

            $response = [
                'success' => true,
                'message' => "Pengiriman selesai. Sukses: {$successCount}, Gagal: {$failedCount}",
                'results' => $results,
                'success_count' => $successCount,
                'failed_count' => $failedCount
            ];

            echo json_encode($response);

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
        exit;
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
}

// Proses request
if (isset($_GET['action'])) {
    $waGatewayController = new WAGatewayController();

    switch ($_GET['action']) {
        case 'form':
            $waGatewayController->form();
            break;
        case 'send':
            $waGatewayController->sendMessage();
            break;
        case 'delete':
            $waGatewayController->delete();
            break;
        case 'getData':
            $waGatewayController->getMessages();
            break;
        case 'searchContacts':
            $waGatewayController->searchContacts();
            break;
                case 'export':
            $waGatewayController->export();
            break;
        case 'bulkSend':
            $waGatewayController->bulkSend();
            break;
        default:
            $waGatewayController->index();
            break;
    }
} else {
    $waGatewayController = new WAGatewayController();
    $waGatewayController->index();
}