<?php

function asset($path) {
    return BASE_PATH . '/' . ltrim($path, '/');
}

function url($path = '') {
    if ($path === '') {
        return BASE_PATH . '/';
    }
    return BASE_PATH . '/' . ltrim($path, '/');
}

function route($controller, $action = 'index', $extra = []) {
    $path = trim($controller, '/');
    if ($action !== 'index') {
        $path .= '/' . trim($action, '/');
    }

    $url = url($path);
    if (!empty($extra)) {
        $url .= '?' . http_build_query($extra);
    }
    return $url;
}

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

function rateLimit($key, $maxHits = 30, $windowSec = 60) {
    $now = time();
    $bucket = '_rate_' . $key;

    if (!isset($_SESSION[$bucket]) || ($now - $_SESSION[$bucket]['start']) >= $windowSec) {
        $_SESSION[$bucket] = ['start' => $now, 'count' => 0];
    }

    $_SESSION[$bucket]['count']++;

    return $_SESSION[$bucket]['count'] <= $maxHits;
}

function formatTanggalIndonesia($tanggal) {
    $hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    $bulan = [
        'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember'
    ];

    $timestamp = strtotime($tanggal);
    if (!$timestamp) {
        return $tanggal;
    }
    $nama_hari = $hari[date('w', $timestamp)];
    $tanggal_num = date('d', $timestamp);
    $nama_bulan = $bulan[date('n', $timestamp) - 1];
    $tahun = date('Y', $timestamp);

    return "$nama_hari, $tanggal_num $nama_bulan $tahun";
}
