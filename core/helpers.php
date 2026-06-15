<?php

/**
 * Build an absolute path (from domain root) for an asset file.
 * Works regardless of the folder name the app is deployed in.
 */
function asset($path) {
    return BASE_PATH . '/' . ltrim($path, '/');
}

/**
 * Build an absolute path (from domain root) for an internal link.
 */
function url($path = '') {
    if ($path === '') {
        return BASE_PATH . '/';
    }
    return BASE_PATH . '/' . ltrim($path, '/');
}

/**
 * Build an index.php?controller=...&action=... URL with optional extra query params.
 */
function route($controller, $action = 'index', $extra = []) {
    $query = array_merge([
        'controller' => $controller,
        'action' => $action,
    ], $extra);

    return url('index.php') . '?' . http_build_query($query);
}

/**
 * Pasang security header dasar (defense-in-depth, melengkapi .htaccess).
 * Aman dipanggil berkali-kali / di environment non-Apache.
 */
function applySecurityHeaders() {
    if (headers_sent()) {
        return;
    }

    header('X-Frame-Options: SAMEORIGIN');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
    header_remove('X-Powered-By');
}

/**
 * Rate limiter sederhana berbasis session, untuk membatasi jumlah
 * request ke endpoint tertentu dalam jeda waktu tertentu.
 *
 * @param string $key       identitas unik endpoint/aksi (misal: 'login', 'kirim-wa')
 * @param int    $maxHits   jumlah maksimum request yang diizinkan
 * @param int    $windowSec lebar jendela waktu dalam detik
 * @return bool true jika request masih diizinkan, false jika sudah melebihi batas
 */
function rateLimit($key, $maxHits = 30, $windowSec = 60) {
    $now = time();
    $bucket = '_rate_' . $key;

    if (!isset($_SESSION[$bucket]) || ($now - $_SESSION[$bucket]['start']) >= $windowSec) {
        $_SESSION[$bucket] = ['start' => $now, 'count' => 0];
    }

    $_SESSION[$bucket]['count']++;

    return $_SESSION[$bucket]['count'] <= $maxHits;
}
