<?php

require_once 'models/LaporanCamatModel.php';
require_once 'models/AuthModel.php';
require_once 'models/WilayahModel.php';

class LaporanCamatController extends BaseController {
    private $laporanCamatModel;
    private $authModel;
    private $wilayahModel;

    public function __construct() {
        $this->laporanCamatModel = new LaporanCamatModel();
        $this->authModel = new AuthModel();
        $this->wilayahModel = new WilayahModel();
    }

    private function hasPermission($id_user) {
        $current_user_id = $_SESSION['user_id'] ?? 0;
        $current_role = $_SESSION['role'] ?? '';
        
        if ($current_role === 'admin') {
            return true;
        }
        
        return $current_user_id == $id_user;
    }

    public function index() {
        $this->requireLogin();
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $status = isset($_GET['status']) ? $_GET['status'] : '';
        
        $current_user_id = $_SESSION['user_id'];
        $current_role = $_SESSION['role'];
        
        if ($current_role === 'admin') {
            $laporans = $this->laporanCamatModel->getWithPagination($limit, $offset, $search, $status);
            $total = $this->laporanCamatModel->getTotalWithFilters($search, $status);
        } else {
            $laporans = $this->laporanCamatModel->getAllByUserId($current_user_id, $limit, $offset, $search);
            $total = $this->laporanCamatModel->getTotalByUserId($current_user_id, $search);
        }
        
        $total_pages = ceil($total / $limit);
        
        require_once 'views/laporan-camat/index.php';
    }

    public function create() {
        $this->requireLogin();
        
        if ($_SESSION['role'] !== 'camat') {
            $_SESSION['error'] = 'Hanya camat yang dapat membuat laporan camat';
            $this->redirect(route('laporanCamat', 'index'));
        }
        
        $kecamatan_list = $this->wilayahModel->getKecamatanOptions();
        $desa_list = [];
        $selected_kecamatan_id = null;
        $selected_desa_id = null;
        
        require_once 'views/laporan-camat/form.php';
    }

    public function store() {
        $this->requireLogin();
        
        if ($_SESSION['role'] !== 'camat') {
            $_SESSION['error'] = 'Hanya camat yang dapat membuat laporan camat';
            $this->redirect(route('laporanCamat', 'index'));
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(route('laporanCamat', 'create'));
        }
        
        $nama_pelapor = trim($_POST['nama_pelapor'] ?? '');
        $id_desa = (int)($_POST['id_desa'] ?? 0);
        $id_kecamatan = (int)($_POST['id_kecamatan'] ?? 0);
        $waktu_kejadian = $_POST['waktu_kejadian'] ?? '';
        $tujuan = $_POST['tujuan'] ?? '';
        $uraian_laporan = trim($_POST['uraian_laporan'] ?? '');
        
        if (empty($nama_pelapor) || empty($id_desa) || empty($id_kecamatan) || 
            empty($waktu_kejadian) || empty($tujuan) || empty($uraian_laporan)) {
            $_SESSION['error'] = 'Semua field wajib diisi';
            $this->redirect(route('laporanCamat', 'create'));
        }
        
        $desa = $this->wilayahModel->getDesaById($id_desa);
        $kecamatan = $this->wilayahModel->getKecamatanById($id_kecamatan);
        
        if (!$desa || !$kecamatan) {
            $_SESSION['error'] = 'Data desa atau kecamatan tidak valid';
            $this->redirect(route('laporanCamat', 'create'));
        }
        
        $nama_desa = $desa['nama_desa'];
        $nama_kecamatan = $kecamatan['nama_kecamatan'];
        
        if (!strtotime($waktu_kejadian)) {
            $_SESSION['error'] = 'Format waktu kejadian tidak valid';
            $this->redirect(route('laporanCamat', 'create'));
        }
        
        $valid_tujuuan = ['bupati', 'wakil bupati', 'sekda', 'opd'];
        if (!in_array($tujuan, $valid_tujuuan)) {
            $_SESSION['error'] = 'Tujuan laporan tidak valid';
            $this->redirect(route('laporanCamat', 'create'));
        }
        
        $upload_file = null;
        if (isset($_FILES['upload_file']) && $_FILES['upload_file']['error'] === 0) {
            $upload_dir = 'uploads/laporan_camat/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $file_name = $_FILES['upload_file']['name'];
            $file_tmp = $_FILES['upload_file']['tmp_name'];
            $file_size = $_FILES['upload_file']['size'];
            $file_type = $_FILES['upload_file']['type'];
            
            $allowed_types = $this->getAllowedFileTypes();
            if (!in_array($file_type, $allowed_types)) {
                $_SESSION['error'] = 'Format file tidak diizinkan';
                $this->redirect(route('laporanCamat', 'create'));
            }
            
            $max_file_size = 10000000;
            if (strpos($file_type, 'video/') === 0) {
                $max_file_size = 100000000;
            }
            
            if ($file_size > $max_file_size) {
                $max_size_mb = $max_file_size / 1000000;
                $_SESSION['error'] = "Ukuran file terlalu besar (maksimal {$max_size_mb}MB)";
                $this->redirect(route('laporanCamat', 'create'));
            }
            
            $extension = $this->getExtensionFromMimeType($file_type);
            $upload_file = $upload_dir . uniqid() . $extension;
            
            if (!move_uploaded_file($file_tmp, $upload_file)) {
                $_SESSION['error'] = 'Gagal mengunggah file';
                $this->redirect(route('laporanCamat', 'create'));
            }
        }
        
        $data = [
            'id_user' => $_SESSION['user_id'],
            'nama_pelapor' => $nama_pelapor,
            'nama_desa' => $nama_desa,
            'nama_kecamatan' => $nama_kecamatan,
            'waktu_kejadian' => $waktu_kejadian,
            'tujuan' => $tujuan,
            'uraian_laporan' => $uraian_laporan,
            'upload_file' => $upload_file
        ];
        
        $result = $this->laporanCamatModel->create($data);
        
        if ($result) {
            $_SESSION['success'] = 'Laporan berhasil dibuat';
            $this->redirect(route('laporanCamat', 'index'));
        } else {
            $_SESSION['error'] = 'Gagal membuat laporan';
            $this->redirect(route('laporanCamat', 'create'));
        }
    }

    public function edit($id = null) {
        $this->requireLogin();
        
        $id = $id ?? $_GET['id'] ?? 0;
        
        if (!$id) {
            $_SESSION['error'] = 'ID laporan tidak ditemukan';
            $this->redirect(route('laporanCamat', 'index'));
        }
        
        $laporan = $this->laporanCamatModel->getById($id);
        
        if (!$laporan) {
            $_SESSION['error'] = 'Laporan tidak ditemukan';
            $this->redirect(route('laporanCamat', 'index'));
        }
        
        if (!$this->hasPermission($laporan['id_user'])) {
            $_SESSION['error'] = 'Anda tidak memiliki akses ke laporan ini';
            $this->redirect(route('laporanCamat', 'index'));
        }
        
        if ($laporan['status_laporan'] !== 'baru') {
            $_SESSION['error'] = 'Laporan yang sudah diproses tidak dapat diedit';
            $this->redirect(route('laporanCamat', 'detail', ['id' => $id]));
        }
        
        $kecamatan_list = $this->wilayahModel->getKecamatanOptions();

        $selected_kecamatan_id = null;
        $selected_desa_id = null;

        foreach($kecamatan_list as $kec) {
            if($kec['nama_kecamatan'] === $laporan['nama_kecamatan']) {
                $selected_kecamatan_id = $kec['id_kecamatan'];
                break;
            }
        }

        $desa_list = [];
        if($selected_kecamatan_id) {
            $desa_list = $this->wilayahModel->getDesaByKecamatan($selected_kecamatan_id);

            foreach($desa_list as $d) {
                if($d['nama_desa'] === $laporan['nama_desa']) {
                    $selected_desa_id = $d['id_desa'];
                    break;
                }
            }
        }
          
        require_once 'views/laporan-camat/form.php';
    }

    public function update($id = null) {
        $this->requireLogin();
        
        $id = $id ?? $_POST['id'] ?? $_GET['id'] ?? 0;
        
        if (!$id) {
            $_SESSION['error'] = 'ID laporan tidak ditemukan';
            $this->redirect(route('laporanCamat', 'index'));
        }
        
        if (!in_array($_SERVER['REQUEST_METHOD'], ['POST', 'PUT'], true)) {
            $this->redirect(route('laporanCamat', 'edit', ['id' => $id]));
        }
        
        $laporan = $this->laporanCamatModel->getById($id);
        
        if (!$laporan) {
            $_SESSION['error'] = 'Laporan tidak ditemukan';
            $this->redirect(route('laporanCamat', 'index'));
        }
        
        if (!$this->hasPermission($laporan['id_user'])) {
            $_SESSION['error'] = 'Anda tidak memiliki akses ke laporan ini';
            $this->redirect(route('laporanCamat', 'index'));
        }
        
        if ($laporan['status_laporan'] !== 'baru') {
            $_SESSION['error'] = 'Laporan yang sudah diproses tidak dapat diedit';
            $this->redirect(route('laporanCamat', 'detail', ['id' => $id]));
        }
        
        $nama_pelapor = trim($_POST['nama_pelapor'] ?? '');
        $id_desa = (int)($_POST['id_desa'] ?? 0);
        $id_kecamatan = (int)($_POST['id_kecamatan'] ?? 0);
        $waktu_kejadian = $_POST['waktu_kejadian'] ?? '';
        $tujuan = $_POST['tujuan'] ?? '';
        $uraian_laporan = trim($_POST['uraian_laporan'] ?? '');
        
        if (empty($nama_pelapor) || empty($id_desa) || empty($id_kecamatan) || 
            empty($waktu_kejadian) || empty($tujuan) || empty($uraian_laporan)) {
            $_SESSION['error'] = 'Semua field wajib diisi';
            $this->redirect(route('laporanCamat', 'edit', ['id' => $id]));
        }
        
        $desa = $this->wilayahModel->getDesaById($id_desa);
        $kecamatan = $this->wilayahModel->getKecamatanById($id_kecamatan);
        
        if (!$desa || !$kecamatan) {
            $_SESSION['error'] = 'Data desa atau kecamatan tidak valid';
            $this->redirect(route('laporanCamat', 'edit', ['id' => $id]));
        }
        
        $nama_desa = $desa['nama_desa'];
        $nama_kecamatan = $kecamatan['nama_kecamatan'];
        
        if (!strtotime($waktu_kejadian)) {
            $_SESSION['error'] = 'Format waktu kejadian tidak valid';
            $this->redirect(route('laporanCamat', 'edit', ['id' => $id]));
        }
        
        $valid_tujuuan = ['bupati', 'wakil bupati', 'sekda', 'opd'];
        if (!in_array($tujuan, $valid_tujuuan)) {
            $_SESSION['error'] = 'Tujuan laporan tidak valid';
            $this->redirect(route('laporanCamat', 'edit', ['id' => $id]));
        }
        
        $upload_file = $laporan['upload_file'];
        if (isset($_FILES['upload_file']) && $_FILES['upload_file']['error'] === 0) {
            $upload_dir = 'uploads/laporan_camat/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $file_name = $_FILES['upload_file']['name'];
            $file_tmp = $_FILES['upload_file']['tmp_name'];
            $file_size = $_FILES['upload_file']['size'];
            $file_type = $_FILES['upload_file']['type'];
            
            $allowed_types = $this->getAllowedFileTypes();
            if (!in_array($file_type, $allowed_types)) {
                $_SESSION['error'] = 'Format file tidak diizinkan';
                $this->redirect(route('laporanCamat', 'edit', ['id' => $id]));
            }
            
            $max_file_size = 10000000;
            if (strpos($file_type, 'video/') === 0) {
                $max_file_size = 100000000;
            }
            
            if ($file_size > $max_file_size) {
                $max_size_mb = $max_file_size / 1000000;
                $_SESSION['error'] = "Ukuran file terlalu besar (maksimal {$max_size_mb}MB)";
                $this->redirect(route('laporanCamat', 'edit', ['id' => $id]));
            }
            
            if ($laporan['upload_file'] && file_exists($laporan['upload_file'])) {
                unlink($laporan['upload_file']);
            }
            
            $extension = $this->getExtensionFromMimeType($file_type);
            $upload_file = $upload_dir . uniqid() . $extension;
            
            if (!move_uploaded_file($file_tmp, $upload_file)) {
                $_SESSION['error'] = 'Gagal mengunggah file';
                $this->redirect(route('laporanCamat', 'edit', ['id' => $id]));
            }
        }
        
        $data = [
            'nama_pelapor' => $nama_pelapor,
            'nama_desa' => $nama_desa,
            'nama_kecamatan' => $nama_kecamatan,
            'waktu_kejadian' => $waktu_kejadian,
            'tujuan' => $tujuan,
            'uraian_laporan' => $uraian_laporan,
            'upload_file' => $upload_file
        ];
        
        $result = $this->laporanCamatModel->update($id, $data);
        
        if ($result) {
            $_SESSION['success'] = 'Laporan berhasil diperbarui';
            $this->redirect(route('laporanCamat', 'detail', ['id' => $id]));
        } else {
            $_SESSION['error'] = 'Gagal memperbarui laporan';
            $this->redirect(route('laporanCamat', 'edit', ['id' => $id]));
        }
    }

    public function delete($id = null) {
        $this->requireLogin();
        
        $id = $id ?? $_POST['id'] ?? $_GET['id'] ?? 0;
        
        if (!$id) {
            $this->json(['success' => false, 'message' => 'ID laporan tidak ditemukan']);
        }
        
        $laporan = $this->laporanCamatModel->getById($id);
        
        if (!$laporan) {
            $this->json(['success' => false, 'message' => 'Laporan tidak ditemukan']);
        }
        
        if (!$this->hasPermission($laporan['id_user'])) {
            $this->json(['success' => false, 'message' => 'Anda tidak memiliki akses ke laporan ini']);
        }
        
        if ($laporan['status_laporan'] !== 'baru') {
            $this->json(['success' => false, 'message' => 'Hanya laporan dengan status baru yang dapat dihapus']);
        }
        
        if ($laporan['upload_file'] && file_exists($laporan['upload_file'])) {
            unlink($laporan['upload_file']);
        }
        
        $result = $this->laporanCamatModel->delete($id);
        
        if ($result) {
            $this->json(['success' => true, 'message' => 'Laporan berhasil dihapus']);
        } else {
            $this->json(['success' => false, 'message' => 'Gagal menghapus laporan']);
        }
    }

    public function detail($id = null) {
        $this->requireLogin();
        
        $id = $id ?? $_POST['id'] ?? $_GET['id'] ?? 0;
        
        if (!$id) {
            $_SESSION['error'] = 'ID laporan tidak ditemukan';
            $this->redirect(route('laporanCamat', 'index'));
        }
        
        $laporan = $this->laporanCamatModel->getById($id);
        
        if (!$laporan) {
            $_SESSION['error'] = 'Laporan tidak ditemukan';
            $this->redirect(route('laporanCamat', 'index'));
        }
        
        if (!$this->hasPermission($laporan['id_user'])) {
            $_SESSION['error'] = 'Anda tidak memiliki akses ke laporan ini';
            $this->redirect(route('laporanCamat', 'index'));
        }
        
        require_once 'views/laporan-camat/detail.php';
    }

    public function updateStatus($id = null) {
        $this->requireLogin();
        
        $id = $id ?? $_GET['id'] ?? 0;
        
        if (!$id) {
            $_SESSION['error'] = 'ID laporan tidak ditemukan';
            $this->redirect(route('laporanCamat', 'index'));
        }
        
        if ($_SESSION['role'] !== 'admin') {
            $_SESSION['error'] = 'Hanya admin yang dapat mengubah status laporan';
            $this->redirect(route('laporanCamat', 'detail', ['id' => $id]));
        }
        
        if (!in_array($_SERVER['REQUEST_METHOD'], ['POST', 'PATCH'], true)) {
            $this->redirect(route('laporanCamat', 'detail', ['id' => $id]));
        }
        
        $status = $_POST['status'] ?? '';
        $valid_status = ['baru', 'diproses', 'selesai'];
        
        if (!in_array($status, $valid_status)) {
            $_SESSION['error'] = 'Status tidak valid';
            $this->redirect(route('laporanCamat', 'detail', ['id' => $id]));
        }
        
        $result = $this->laporanCamatModel->updateStatus($id, $status);
        
        if ($result) {
            $_SESSION['success'] = 'Status laporan berhasil diperbarui';
        } else {
            $_SESSION['error'] = 'Gagal memperbarui status laporan';
        }
        
        $this->redirect(route('laporanCamat', 'detail', ['id' => $id]));
    }
    
    private function getAllowedFileTypes() {
        return [
            'image/jpeg',
            'image/jpg', 
            'image/png',
            'image/gif',
            'image/webp',
            'video/mp4',
            'video/mpeg',
            'video/avi',
            'video/quicktime',
            'video/x-msvideo',
            'video/x-matroska',
            'video/webm',
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ];
    }
    
    private function getExtensionFromMimeType($mimeType) {
        $mimeTypes = [
            'image/jpeg' => '.jpg',
            'image/jpg' => '.jpg',
            'image/png' => '.png',
            'image/gif' => '.gif',
            'image/webp' => '.webp',
            'video/mp4' => '.mp4',
            'video/mpeg' => '.mpeg',
            'video/avi' => '.avi',
            'video/quicktime' => '.mov',
            'video/x-msvideo' => '.avi',
            'video/x-matroska' => '.mkv',
            'video/webm' => '.webm',
            'application/pdf' => '.pdf',
            'application/msword' => '.doc',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => '.docx'
        ];

        return $mimeTypes[$mimeType] ?? '.file';
    }

    public function download($id = null) {
        $this->requireLogin();

        $id = $id ?? $_GET['id'] ?? 0;

        if (!$id) {
            $_SESSION['error'] = 'ID laporan tidak ditemukan';
            $this->redirect(route('laporanCamat', 'index'));
        }

        $laporan = $this->laporanCamatModel->getById($id);

        if (!$laporan) {
            $_SESSION['error'] = 'Laporan tidak ditemukan';
            $this->redirect(route('laporanCamat', 'index'));
        }

        if (!$this->hasPermission($laporan['id_user'])) {
            $_SESSION['error'] = 'Anda tidak memiliki akses ke file ini';
            $this->redirect(route('laporanCamat', 'index'));
        }

        if (empty($laporan['upload_file']) || !file_exists($laporan['upload_file'])) {
            $_SESSION['error'] = 'File tidak ditemukan';
            $this->redirect(route('laporanCamat', 'detail', ['id' => $id]));
        }

        $filePath = $laporan['upload_file'];
        $fileName = basename($filePath);

        $fileSize = filesize($filePath);
        $fileType = mime_content_type($filePath);

        header('Content-Type: ' . $fileType);
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Content-Length: ' . $fileSize);
        header('Cache-Control: private, no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        if (ob_get_level()) {
            ob_clean();
        }

        readfile($filePath);
        exit;
    }

    public function exportToExcel() {
        $this->requireLogin();

        if ($_SESSION['role'] !== 'camat') {
            $_SESSION['error'] = 'Hanya camat yang dapat mengekspor data';
            $this->redirect(route('laporanCamat', 'index'));
        }

        $search = $_GET['search'] ?? '';
        $status = $_GET['status'] ?? '';
        $tanggal_awal = $_GET['tanggal_awal'] ?? '';
        $tanggal_akhir = $_GET['tanggal_akhir'] ?? '';

        $current_user_id = $_SESSION['user_id'];

        try {
            error_log("Export attempt - User ID: " . $current_user_id . ", Role: " . $_SESSION['role']);
            error_log("Filters - Search: '" . $search . "', Status: '" . $status . "'");

            $laporans = $this->laporanCamatModel->getAllReportsForExport(
                $current_user_id,
                $search,
                $status,
                $tanggal_awal,
                $tanggal_akhir
            );

            error_log("Found " . count($laporans) . " records to export");

            if (empty($laporans)) {
                $_SESSION['error'] = 'Tidak ada data untuk diekspor';
                $this->redirect(route('laporanCamat', 'index'));
            }

            $filename = 'laporan_camat_' . date('Y-m-d_H-i-s') . '.xls';

            while (ob_get_level()) {
                ob_end_clean();
            }

            header('Content-Type: application/vnd.ms-excel; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Cache-Control: private, no-cache, no-store, must-revalidate');
            header('Pragma: no-cache');
            header('Expires: 0');
            header('Content-Transfer-Encoding: binary');

            echo '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Camat</title>
</head>
<body>
    <table border="1">
        <thead>
            <tr>
                <th>ID Laporan</th>
                <th>Tanggal Laporan</th>
                <th>Nama Pelapor</th>
                <th>Nama Desa</th>
                <th>Nama Kecamatan</th>
                <th>Waktu Kejadian</th>
                <th>Tujuan</th>
                <th>Uraian Laporan</th>
                <th>Status</th>
                <th>Lampiran File</th>
            </tr>
        </thead>
        <tbody>';

            foreach ($laporans as $laporan) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($laporan['id_laporan_camat']) . '</td>';
                echo '<td>' . (isset($laporan['created_at']) ? date('d/m/Y H:i', strtotime($laporan['created_at'])) : '') . '</td>';
                echo '<td>' . htmlspecialchars($laporan['nama_pelapor'] ?? '') . '</td>';
                echo '<td>' . htmlspecialchars($laporan['nama_desa'] ?? '') . '</td>';
                echo '<td>' . htmlspecialchars($laporan['nama_kecamatan'] ?? '') . '</td>';
                echo '<td>' . (isset($laporan['waktu_kejadian']) ? date('d/m/Y H:i', strtotime($laporan['waktu_kejadian'])) : '') . '</td>';
                echo '<td>' . htmlspecialchars(ucfirst($laporan['tujuan'] ?? '')) . '</td>';
                echo '<td>' . htmlspecialchars(strip_tags($laporan['uraian_laporan'] ?? '')) . '</td>';
                echo '<td>' . htmlspecialchars(ucfirst($laporan['status_laporan'] ?? '')) . '</td>';
                echo '<td>' . htmlspecialchars(!empty($laporan['upload_file']) ? basename($laporan['upload_file']) : 'Tidak ada') . '</td>';
                echo '</tr>';
            }

            echo '
        </tbody>
    </table>
</body>
</html>';

            exit;

        } catch (Exception $e) {
            error_log("Excel Export Error: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());

            $_SESSION['error'] = 'Terjadi kesalahan saat mengekspor data. Silakan coba lagi atau hubungi administrator.';
            $this->redirect(route('laporanCamat', 'index'));
        }
    }
}
