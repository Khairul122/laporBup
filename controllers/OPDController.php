<?php

require_once 'models/OPDModel.php';
require_once 'models/AuthModel.php';

class OPDController extends BaseController {
    private $opdModel;
    private $authModel;

    public function __construct() {
        $this->opdModel = new OPDModel();
        $this->authModel = new AuthModel();
    }

    public function index() {
        $this->requireRole('admin');
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        
        $result = $this->opdModel->getAllOPD($page, $limit, '');
        $opds = $result['data'];
        $total = $result['total'];
        $total_pages = ceil($total / $limit);
        
        require_once 'views/opd/index.php';
    }

    public function create() {
        $this->requireRole('admin');
        
        require_once 'views/opd/form.php';
    }

    public function store() {
        $this->requireRole('admin');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(route('opd', 'create'));
        }
        
        $nama_opd = trim($_POST['nama_opd'] ?? '');
        
        if (empty($nama_opd)) {
            $_SESSION['error'] = 'Nama OPD wajib diisi';
            $this->redirect(route('opd', 'create'));
        }
        
        if ($this->opdModel->checkNamaOPDExists($nama_opd)) {
            $_SESSION['error'] = 'Nama OPD sudah ada';
            $this->redirect(route('opd', 'create'));
        }
        
        $data = [
            'nama_opd' => $nama_opd
        ];
        
        $result = $this->opdModel->createOPD($data);
        
        if ($result) {
            $_SESSION['success'] = 'OPD berhasil ditambahkan';
            $this->redirect(route('opd', 'index'));
        } else {
            $_SESSION['error'] = 'Gagal menambahkan OPD';
            $this->redirect(route('opd', 'create'));
        }
    }

    public function edit($id = null) {
        $this->requireRole('admin');
        
        $id = $id ?? $_GET['id'] ?? 0;
        
        if (!$id) {
            $_SESSION['error'] = 'ID OPD tidak ditemukan';
            $this->redirect(route('opd', 'index'));
        }
        
        $opd = $this->opdModel->getOPDById($id);
        
        if (!$opd) {
            $_SESSION['error'] = 'OPD tidak ditemukan';
            $this->redirect(route('opd', 'index'));
        }
        
        require_once 'views/opd/form.php';
    }

    public function update($id = null) {
        $this->requireRole('admin');
        
        $id = $id ?? $_GET['id'] ?? 0;
        
        if (!$id) {
            $_SESSION['error'] = 'ID OPD tidak ditemukan';
            $this->redirect(route('opd', 'index'));
        }
        
        if (!in_array($_SERVER['REQUEST_METHOD'], ['POST', 'PUT'], true)) {
            $this->redirect(route('opd', 'edit', ['id' => $id]));
        }
        
        $opd = $this->opdModel->getOPDById($id);
        
        if (!$opd) {
            $_SESSION['error'] = 'OPD tidak ditemukan';
            $this->redirect(route('opd', 'index'));
        }
        
        $nama_opd = trim($_POST['nama_opd'] ?? '');
        
        if (empty($nama_opd)) {
            $_SESSION['error'] = 'Nama OPD wajib diisi';
            $this->redirect(route('opd', 'edit', ['id' => $id]));
        }
        
        if ($this->opdModel->checkNamaOPDExists($nama_opd, $id)) {
            $_SESSION['error'] = 'Nama OPD sudah ada';
            $this->redirect(route('opd', 'edit', ['id' => $id]));
        }
        
        $data = [
            'nama_opd' => $nama_opd
        ];
        
        $result = $this->opdModel->updateOPD($id, $data);
        
        if ($result) {
            $_SESSION['success'] = 'OPD berhasil diperbarui';
            $this->redirect(route('opd', 'index'));
        } else {
            $_SESSION['error'] = 'Gagal memperbarui OPD';
            $this->redirect(route('opd', 'edit', ['id' => $id]));
        }
    }

    public function delete($id = null) {
        $this->requireRole('admin');
        
        $id = $id ?? $_GET['id'] ?? $_POST['id'] ?? 0;
        
        if (!$id) {
            $this->json(['success' => false, 'message' => 'ID OPD tidak ditemukan']);
        }
        
        $opd = $this->opdModel->getOPDById($id);
        
        if (!$opd) {
            $this->json(['success' => false, 'message' => 'OPD tidak ditemukan']);
        }
        
        $result = $this->opdModel->deleteOPD($id);
        $this->json($result);
    }

    public function getOPDList() {
        $this->requireLogin();

        $search = $_GET['search'] ?? '';
        $result = $this->opdModel->getAllOPD(1, 1000, $search);
        
        $this->json([
            'success' => true,
            'data' => $result['data']
        ]);
    }
}
