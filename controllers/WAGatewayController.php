<?php

require_once 'models/WAGatewayModel.php';

class WAGatewayController extends BaseController {
    private $model;

    public function __construct() {
        $this->model = new WAGatewayModel();
    }

    public function index() {
        $this->requireRole('admin');

        $search = $_GET['search'] ?? '';
        $status_filter = $_GET['status'] ?? '';
        $date_filter = $_GET['date'] ?? '';

        $result = $this->model->getAllMessages(1, 10, $search, $status_filter, $date_filter);
        $contacts = $this->model->getContacts();

        require_once 'views/wagateway/index.php';
    }

    public function form() {
        $this->requireRole('admin');

        $id = $_GET['id'] ?? null;
        $message = null;

        if ($id) {
            $message = $this->model->getMessageById($id);
            if (!$message) {
                $_SESSION['error'] = 'Pesan tidak ditemukan';
                $this->redirect(route('waGateway', 'index'));
            }
        }

        $contacts = $this->model->getContacts();

        require_once 'views/wagateway/form.php';
    }

    public function sendMessage() {
        $this->requireRole('admin');

        $no_tujuan = $_POST['no_tujuan'] ?? '';
        $pesan = $_POST['pesan'] ?? '';
        $id = $_POST['id'] ?? null;

        if (empty($no_tujuan) || empty($pesan)) {
            $this->json(['success' => false, 'message' => 'Nomor tujuan dan pesan tidak boleh kosong']);
        }

        $no_tujuan = $this->formatPhoneNumber($no_tujuan);

        if (!preg_match('/^(62)[0-9]{9,13}$/', $no_tujuan)) {
            $this->json(['success' => false, 'message' => 'Format nomor telepon tidak valid. Gunakan format: 08xxxxxxxxxx']);
        }

        if ($id) {
            $result = $this->model->updateMessage($id, $no_tujuan, $pesan, 'pending');
            $message_id = $id;
        } else {
            $message_id = $this->model->createMessage($no_tujuan, $pesan, 'pending');
        }

        if (!$message_id) {
            $this->json(['success' => false, 'message' => 'Gagal menyimpan pesan ke database']);
        }

        $result = $this->model->sendWhatsAppMessage($no_tujuan, $pesan);

        if ($result && isset($result['success']) && $result['success']) {
            $this->model->updateStatus($message_id, 'sent');
            $this->json(['success' => true, 'message' => 'Pesan berhasil dikirim', 'data' => $result]);
        } else {
            $this->model->updateStatus($message_id, 'failed');
            $errorMessage = isset($result['message']) ? $result['message'] : 'Gagal mengirim pesan';
            $this->json(['success' => false, 'message' => $errorMessage]);
        }
    }

    public function delete() {
        $this->requireRole('admin');

        $id = $_POST['id'] ?? '';
        if (empty($id)) {
            $this->json(['success' => false, 'message' => 'ID pesan tidak valid']);
        }

        $result = $this->model->deleteMessage($id);
        if ($result) {
            $this->json(['success' => true, 'message' => 'Pesan berhasil dihapus']);
        } else {
            $this->json(['success' => false, 'message' => 'Gagal menghapus pesan']);
        }
    }

    public function getMessages() {
        $this->requireRole('admin');

        try {
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
            $search = $_GET['search'] ?? '';
            $status_filter = $_GET['status'] ?? '';
            $date_filter = $_GET['date'] ?? '';

            $result = $this->model->getAllMessages($page, $limit, $search, $status_filter, $date_filter);

            $this->json([
                'success' => true,
                'data' => $result
            ]);
        } catch (Exception $e) {
            $this->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function searchContacts() {
        $this->requireRole('admin');

        try {
            $keyword = $_GET['q'] ?? '';

            if (strlen($keyword) < 2) {
                $this->json([
                    'success' => true,
                    'data' => []
                ]);
            }

            $data = $this->model->searchContacts($keyword);

            $this->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (Exception $e) {
            $this->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function export() {
        $this->requireRole('admin');

        try {
            $status_filter = $_GET['status'] ?? '';
            $date_filter = $_GET['date'] ?? '';
            $data = $this->model->getMessagesForExport($status_filter, $date_filter);

            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="wa_gateway_messages_' . date('Y-m-d') . '.csv"');

            $output = fopen('php://output', 'w');

            fputcsv($output, ['ID', 'Nomor Tujuan', 'Pesan', 'Tanggal Kirim', 'Status', 'Created At']);

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
            $this->redirect(route('waGateway', 'index'));
        }
    }

    public function bulkSend() {
        $this->requireRole('admin');

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
                $contactData = explode('|', $contact);
                if (count($contactData) >= 2) {
                    $phone = trim($contactData[1]);
                    $name = trim($contactData[0]);

                    $phone = $this->formatPhoneNumber($phone);

                    if (preg_match('/^(62)[0-9]{9,13}$/', $phone)) {
                        $messageId = $this->model->createMessage($phone, $message, 'pending');

                        if ($messageId) {
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

            $this->json([
                'success' => true,
                'message' => "Pengiriman selesai. Sukses: {$successCount}, Gagal: {$failedCount}",
                'results' => $results,
                'success_count' => $successCount,
                'failed_count' => $failedCount
            ]);

        } catch (Exception $e) {
            $this->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    private function formatPhoneNumber($phone) {
        $phone = preg_replace('/\D/', '', $phone);

        if (substr($phone, 0, 2) !== '62') {
            if (substr($phone, 0, 1) === '0') {
                $phone = '62' . substr($phone, 1);
            } else {
                $phone = '62' . $phone;
            }
        }

        return $phone;
    }
}