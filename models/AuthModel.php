<?php

require_once __DIR__ . '/../config/koneksi.php';

class AuthModel {
    private $db;

    public function __construct() { 
        $this->db = getKoneksi();
    }

    /**
     * Login user
     * @param string $username
     * @param string $password
     * @return array|false
     */
    public function login($username, $password) {
        try {
            $username = escapeString($username);

            $query = "SELECT * FROM users WHERE username = '$username' LIMIT 1";
            $result = query($query);

            if ($result && $result->num_rows === 1) {
                $user = $result->fetch_assoc();

                // Verifikasi password
                if (password_verify($password, $user['password'])) {
                    // Update last login
                    $this->updateLastLogin($user['id_user']);
                    return $user;
                }
            }

            return false;
        } catch (Exception $e) {
            // Log error tapi jangan throw exception
            error_log("Login error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update last login time
     * @param int $userId
     */
    private function updateLastLogin($userId) {
        try {
            $userId = (int)$userId;
            $query = "UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE id_user = $userId";
            query($query);
        } catch (Exception $e) {
            error_log("Update last login error: " . $e->getMessage());
        }
    }

    /**
     * Get user by ID
     * @param int $userId
     * @return array|false
     */
    public function getUserById($userId) {
        $userId = (int)$userId;
        $query = "SELECT id_user, username, email, jabatan, role, created_at FROM users WHERE id_user = $userId LIMIT 1";
        $result = query($query);

        if ($result->num_rows === 1) {
            return $result->fetch_assoc();
        }

        return false;
    }

    /**
     * Get all users (for admin)
     * @return array
     */
    public function getAllUsers() {
        $query = "SELECT id_user, username, email, jabatan, role, created_at FROM users ORDER BY created_at DESC";
        $result = query($query);

        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }

        return $users;
    }

    }