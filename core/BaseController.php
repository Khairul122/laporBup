<?php

class BaseController {

    protected function isLoggedIn() {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }

    protected function getUserRole() {
        return $_SESSION['role'] ?? null;
    }

    protected function getCurrentUserId() {
        return $_SESSION['user_id'] ?? null;
    }

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

    protected function isAjaxRequest() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH'])
            && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
    }

    protected function redirect($path) {
        header('Location: ' . url($path));
        exit;
    }

    protected function redirectToDashboard() {
        header('Location: ' . $this->getDashboardUrl());
        exit;
    }

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

    protected function json($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function render($view, $data = []) {
        extract($data);
        require __DIR__ . '/../views/' . $view . '.php';
    }
}
