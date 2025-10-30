<?php
require_once 'models/OPDModel.php';
require_once 'models/AuthModel.php';

class OPDController {
    private $opdModel;
    private $authModel;

    public function __construct() {
        $this->opdModel = new OPDModel();
        $this->authModel = new AuthModel();
    }

    /**
     * Check if user is logged in
     */
    private function isLoggedIn() {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }

    /**
     * Require login for accessing pages
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
     * Require admin role for accessing pages
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
                header('Location: index.php?controller=dashboard&action=' . $_SESSION['role']);
                exit;
            }
        }
    }

    /**
     * Index: Display list of OPD
     */
    public function index() {
        $this->requireAdmin();
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        
        $result = $this->opdModel->getAllOPD($page, $limit, $search);
        $opds = $result['data'];
        $total = $result['total'];
        $total_pages = ceil($total / $limit);
        
        require_once 'views/opd/index.php';
    }

    /**
     * Show form to create new OPD
     */
    public function create() {
        $this->requireAdmin();
        
        require_once 'views/opd/form.php';
    }

    /**
     * Store new OPD
     */
    public function store() {
        $this->requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=opd&action=create');
            exit;
        }
        
        // Validate input
        $nama_opd = trim($_POST['nama_opd'] ?? '');
        
        // Validation
        if (empty($nama_opd)) {
            $_SESSION['error'] = 'Nama OPD wajib diisi';
            header('Location: index.php?controller=opd&action=create');
            exit;
        }
        
        // Check if nama opd already exists
        if ($this->opdModel->checkNamaOPDExists($nama_opd)) {
            $_SESSION['error'] = 'Nama OPD sudah ada';
            header('Location: index.php?controller=opd&action=create');
            exit;
        }
        
        // Prepare data for insertion
        $data = [
            'nama_opd' => $nama_opd
        ];
        
        $result = $this->opdModel->createOPD($data);
        
        if ($result) {
            $_SESSION['success'] = 'OPD berhasil ditambahkan';
            header('Location: index.php?controller=opd&action=index');
        } else {
            $_SESSION['error'] = 'Gagal menambahkan OPD';
            header('Location: index.php?controller=opd&action=create');
        }
        exit;
    }

    /**
     * Show form to edit OPD
     */
    public function edit($id = null) {
        $this->requireAdmin();
        
        // Get ID from parameter or from GET request
        $id = $id ?? $_GET['id'] ?? 0;
        
        if (!$id) {
            $_SESSION['error'] = 'ID OPD tidak ditemukan';
            header('Location: index.php?controller=opd&action=index');
            exit;
        }
        
        $opd = $this->opdModel->getOPDById($id);
        
        if (!$opd) {
            $_SESSION['error'] = 'OPD tidak ditemukan';
            header('Location: index.php?controller=opd&action=index');
            exit;
        }
        
        require_once 'views/opd/form.php';
    }

    /**
     * Update OPD
     */
    public function update($id = null) {
        $this->requireAdmin();
        
        // Get ID from parameter or from GET request
        $id = $id ?? $_GET['id'] ?? 0;
        
        if (!$id) {
            $_SESSION['error'] = 'ID OPD tidak ditemukan';
            header('Location: index.php?controller=opd&action=index');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=opd&action=edit&id=' . $id);
            exit;
        }
        
        $opd = $this->opdModel->getOPDById($id);
        
        if (!$opd) {
            $_SESSION['error'] = 'OPD tidak ditemukan';
            header('Location: index.php?controller=opd&action=index');
            exit;
        }
        
        // Validate input
        $nama_opd = trim($_POST['nama_opd'] ?? '');
        
        // Validation
        if (empty($nama_opd)) {
            $_SESSION['error'] = 'Nama OPD wajib diisi';
            header('Location: index.php?controller=opd&action=edit&id=' . $id);
            exit;
        }
        
        // Check if nama opd already exists (excluding current opd)
        if ($this->opdModel->checkNamaOPDExists($nama_opd, $id)) {
            $_SESSION['error'] = 'Nama OPD sudah ada';
            header('Location: index.php?controller=opd&action=edit&id=' . $id);
            exit;
        }
        
        // Prepare data for update
        $data = [
            'nama_opd' => $nama_opd
        ];
        
        $result = $this->opdModel->updateOPD($id, $data);
        
        if ($result) {
            $_SESSION['success'] = 'OPD berhasil diperbarui';
            header('Location: index.php?controller=opd&action=index');
        } else {
            $_SESSION['error'] = 'Gagal memperbarui OPD';
            header('Location: index.php?controller=opd&action=edit&id=' . $id);
        }
        exit;
    }

    /**
     * Delete OPD
     */
    public function delete($id = null) {
        $this->requireAdmin();
        
        // Get ID from parameter or from GET request
        $id = $id ?? $_GET['id'] ?? 0;
        
        if (!$id) {
            $response = ['success' => false, 'message' => 'ID OPD tidak ditemukan'];
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }
        
        $opd = $this->opdModel->getOPDById($id);
        
        if (!$opd) {
            $response = ['success' => false, 'message' => 'OPD tidak ditemukan'];
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }
        
        $result = $this->opdModel->deleteOPD($id);
        
        if ($result['success']) {
            $response = ['success' => true, 'message' => $result['message']];
        } else {
            $response = ['success' => false, 'message' => $result['message']];
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    /**
     * Get OPD as JSON for AJAX requests (accessible by OPD users)
     */
    public function getOPDList() {
        $this->requireLogin();

        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
            $search = $_GET['search'] ?? '';
            $result = $this->opdModel->getAllOPD(1, 1000, $search);
            $opds = $result['data'];

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'data' => $opds
            ]);
            exit;
        } else {
            // For non-AJAX request, redirect to index
            header('Location: index.php?controller=opd&action=index');
            exit;
        }
    }
}