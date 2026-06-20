<?php

require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/BaseModel.php';

class AuthModel extends BaseModel {
    
    public function login($username, $password) {
        try {
            $user = $this->fetchOne(
                "SELECT * FROM users WHERE username = ? LIMIT 1",
                's',
                [$username]
            );

            if ($user) {
                
                if (password_verify($password, $user['password'])) {
                    
                    $this->updateLastLogin($user['id_user']);
                    return $user;
                }
            }

            return false;
        } catch (Exception $e) {
            
            error_log("Login error: " . $e->getMessage());
            return false;
        }
    }

    
    private function updateLastLogin($userId) {
        try {
            $this->query(
                "UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE id_user = ?",
                'i',
                [(int)$userId]
            );
        } catch (Exception $e) {
            if (stripos($e->getMessage(), 'last_login') !== false) {
                try {
                    $this->db->query("ALTER TABLE users ADD COLUMN last_login DATETIME NULL DEFAULT NULL");
                    $this->query(
                        "UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE id_user = ?",
                        'i',
                        [(int)$userId]
                    );
                } catch (Exception $e2) {
                    error_log("Update last login error: " . $e2->getMessage());
                }
            } else {
                error_log("Update last login error: " . $e->getMessage());
            }
        }
    }

    
    public function getUserById($userId) {
        $user = $this->fetchOne(
            "SELECT id_user, username, email, jabatan, role, created_at FROM users WHERE id_user = ? LIMIT 1",
            'i',
            [(int)$userId]
        );

        return $user ?: false;
    }

    public function getAllUsers() {
        $query = "SELECT id_user, username, email, jabatan, role, created_at FROM users ORDER BY created_at DESC";
        return $this->fetchAll($query);
    }
}