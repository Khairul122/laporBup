<?php

require_once __DIR__ . '/env.php';
loadEnv(__DIR__ . '/../.env');

define('APP_ENV', env('APP_ENV', 'local'));

define('DB_HOST', env('DB_HOST', 'localhost'));
define('DB_USER', env('DB_USER', 'silapga_user'));
define('DB_PASS', env('DB_PASS', 'SFVU85Di4zsY'));
define('DB_NAME', env('DB_NAME', 'silapga_web'));

define('APP_NAME', 'Sistem ');
define('APP_VERSION', '1.0.0');

define('BASE_PATH', rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/'));
define('BASE_URL', (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . BASE_PATH);

require_once __DIR__ . '/../core/helpers.php';
require_once __DIR__ . '/../vendor/autoload.php';

define('SESSION_NAME', env('SESSION_NAME', '_session'));
define('SESSION_LIFETIME', (int) env('SESSION_LIFETIME', 7200));

define('HASH_ALGORITHM', PASSWORD_DEFAULT);
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_TIMEOUT', 900);

$isProduction = APP_ENV === 'production';

error_reporting(E_ALL);
ini_set('display_errors', $isProduction ? '0' : '1');
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/../logs/php-error.log');

date_default_timezone_set('Asia/Jakarta');

mysqli_report(MYSQLI_REPORT_OFF);
$koneksi = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($koneksi->connect_error) {
    error_log("Koneksi database gagal: " . $koneksi->connect_error);
    die($isProduction ? "Layanan sedang tidak tersedia. Coba lagi nanti." : "Koneksi database gagal: " . $koneksi->connect_error);
}

$koneksi->set_charset("utf8mb4");

function getKoneksi() {
    global $koneksi;
    return $koneksi;
}

function getConnection() {
    return getKoneksi();
}

function escapeString($string) {
    global $koneksi;
    return $koneksi->real_escape_string($string);
}

function query($sql, $params = []) {
    global $koneksi;

    if (empty($params)) {
        $result = $koneksi->query($sql);
        if ($result === false) {
            throw new mysqli_sql_exception("Query failed: " . $koneksi->error . " SQL: " . $sql);
        }
        return $result;
    }

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

function closeKoneksi() {
    global $koneksi;
    if ($koneksi) {
        $koneksi->close();
    }
}
