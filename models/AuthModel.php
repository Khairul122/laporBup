<?php

require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/BaseModel.php';

class AuthModel extends BaseModel {
    /**
     * Login user
     * @param string $username
     * @param string $password
     * @return array|false
     */
    public function login($username, $password) {
        try {
            $user = $this->fetchOne(
                "SELECT * FROM users WHERE username = ? LIMIT 1",
                's',
                [$username]
            );

            if ($user) {
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
            $this->query(
                "UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE id_user = ?",
                'i',
                [(int)$userId]
            );
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
        $user = $this->fetchOne(
            "SELECT id_user, username, email, jabatan, role, created_at FROM users WHERE id_user = ? LIMIT 1",
            'i',
            [(int)$userId]
        );

        return $user ?: false;
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