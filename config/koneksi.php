<?php

// Konfigurasi Database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'helpdesk');

// Konfigurasi Aplikasi
define('APP_NAME', 'Sistem LaporBup');
define('APP_VERSION', '1.0.0');
define('BASE_URL', 'http://localhost/helpdesk');

// Konfigurasi Session
define('SESSION_NAME', 'laporbup_session');
define('SESSION_LIFETIME', 7200); // 2 jam dalam detik

// Konfigurasi Keamanan
define('HASH_ALGORITHM', PASSWORD_DEFAULT);
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_TIMEOUT', 900); // 15 menit dalam detik

// Error Reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Timezone
date_default_timezone_set('Asia/Jakarta');

// Koneksi Database
$koneksi = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Cek Koneksi
if ($koneksi->connect_error) {
    die("Koneksi database gagal: " . $koneksi->connect_error);
}

// Set charset
$koneksi->set_charset("utf8mb4");

// Fungsi untuk koneksi database
function getKoneksi() {
    global $koneksi;
    return $koneksi;
}

// Alias untuk getKoneksi (digunakan dalam model)
function getConnection() {
    return getKoneksi();
}

// Fungsi untuk escape string
function escapeString($string) {
    global $koneksi;
    return $koneksi->real_escape_string($string);
}

// Fungsi untuk query
function query($sql, $params = []) {
    global $koneksi;

    if (empty($params)) {
        $result = $koneksi->query($sql);
        if ($result === false) {
            throw new mysqli_sql_exception("Query failed: " . $koneksi->error . " SQL: " . $sql);
        }
        return $result;
    }

    // For backward compatibility, build safe query manually
    foreach ($params as $param) {
        $escapedParam = $koneksi->real_escape_string((string)$param);
        $sql = preg_replace('/\?/', "'" . $escapedParam . "'", $sql, 1);
    }

    $result = $koneksi->query($sql);
    if ($result === false) {
        throw new mysqli_sql_exception("Query failed: " . $koneksi->error . " SQL: " . $sql);
    }
    return $result;
}

// Fungsi untuk close koneksi
function closeKoneksi() {
    global $koneksi;
    if ($koneksi) {
        $koneksi->close();
    }
}