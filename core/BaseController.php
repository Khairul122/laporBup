<?php

/**
 * Base controller menyediakan auth guard, rendering view, dan helper
 * redirect/JSON yang sebelumnya diduplikasi di tiap controller.
 */
class BaseController {

    /**
     * Cek apakah user sudah login
     */
    protected function isLoggedIn() {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }

    /**
     * Mendapatkan role user yang sedang login
     */
    protected function getUserRole() {
        return $_SESSION['role'] ?? null;
    }

    /**
     * Mendapatkan ID user yang sedang login
     */
    protected function getCurrentUserId() {
        return $_SESSION['user_id'] ?? null;
    }

    /**
     * Mendapatkan data user yang sedang login
     */
    protected function getCurrentUser() {
        if ($this->isLoggedIn()) {
            return [
                'id_user' => $_SESSION['user_id'],
                'username' => $_SESSION['username'],
                'email' => $_SESSION['email'],
                'jabatan' => $_SESSION['jabatan'],
                'role' => $_SESSION['role']
            ];
        }
        return null;
    }

    /**
     * Require login untuk mengakses halaman.
     * Mengirim JSON jika request AJAX, atau redirect ke halaman login.
     */
    protected function requireLogin() {
        if (!$this->isLoggedIn()) {
            $response = [
                'success' => false,
                'message' => 'Silakan login terlebih dahulu',
                'redirect' => url('index.php')
            ];

            if ($this->isAjaxRequest()) {
                $this->json($response);
            } else {
                $this->redirect('index.php');
            }
        }
    }

    /**
     * Require role tertentu untuk mengakses halaman
     */
    protected function requireRole($requiredRole) {
        $this->requireLogin();

        if ($_SESSION['role'] !== $requiredRole) {
            $response = [
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke halaman ini'
            ];

            if ($this->isAjaxRequest()) {
                $this->json($response);
            } else {
                $this->redirectToDashboard();
            }
        }
    }

    /**
     * Cek apakah request berasal dari AJAX (XMLHttpRequest)
     */
    protected function isAjaxRequest() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH'])
            && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
    }

    /**
     * Redirect ke path internal (relatif terhadap root aplikasi)
     */
    protected function redirect($path) {
        header('Location: ' . url($path));
        exit;
    }

    /**
     * Redirect ke dashboard sesuai role user yang sedang login
     */
    protected function redirectToDashboard() {
        header('Location: ' . $this->getDashboardUrl());
        exit;
    }

    /**
     * Mendapatkan URL dashboard sesuai role
     */
    protected function getDashboardUrl() {
        $role = $_SESSION['role'] ?? '';

        switch ($role) {
            case 'admin':
                return route('dashboard', 'admin');
            case 'camat':
                return route('dashboard', 'camat');
            case 'opd':
                return route('dashboard', 'opd');
            default:
                return url('index.php');
        }
    }

    /**
     * Kirim response JSON dan hentikan eksekusi
     */
    protected function json($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Render view dengan data yang di-extract ke variabel lokal,
     * konsisten dengan konvensi `require_once 'views/...'` yang sudah ada.
     */
    protected function render($view, $data = []) {
        extract($data);
        require __DIR__ . '/../views/' . $view . '.php';
    }
}
