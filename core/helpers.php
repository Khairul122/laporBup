<?php

if (!extension_loaded('curl')) {
    $curlConstantDefaults = [
        'CURLOPT_CONNECTTIMEOUT' => 5,
        'CURLOPT_MAXREDIRS' => 5,
        'CURLOPT_SSL_VERIFYHOST' => 2,
        'CURLOPT_SSL_VERIFYPEER' => true,
        'CURLOPT_TIMEOUT' => 30,
        'CURLOPT_USERAGENT' => 78,
        'CURLOPT_FAILONERROR' => 45,
        'CURLOPT_RETURNTRANSFER' => 19913,
        'CURLPROTO_HTTPS' => 2,
        'CURLPROTO_HTTP' => 1,
        'CURLPROTO_FTP' => 4,
        'CURLPROTO_FTPS' => 8,
        'CURLOPT_PROTOCOLS' => 181,
        'CURLOPT_FOLLOWLOCATION' => 52,
        'CURLOPT_URL' => 10002,
        'CURLINFO_HTTP_CODE' => 2097154
    ];

    foreach ($curlConstantDefaults as $name => $value) {
        if (!defined($name)) {
            define($name, $value);
        }
    }
}

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
    $controller = strtolower(trim($controller, '/'));
    $action = strtolower(trim($action, '/'));
    $id = $extra['id'] ?? $extra['id_user'] ?? $extra['id_profile'] ?? $extra['id_opd'] ?? $extra['id_kecamatan'] ?? $extra['id_desa'] ?? $extra['id_laporan_opd'] ?? $extra['id_laporan_camat'] ?? $extra['id_wagateway'] ?? null;

    $resourceMap = [
        'auth' => '',
        'dashboard' => 'dashboard',
        'datapelapor' => 'data-pelapor',
        'opd' => 'opd',
        'kecamatan' => 'kecamatan',
        'desa' => 'desa',
        'profile' => 'profiles',
        'wagateway' => 'wa-messages',
        'laporanopd' => 'laporan-opd',
        'laporancamat' => 'laporan-camat',
        'laporanopdadmin' => 'admin/laporan-opd',
        'laporancamatadmin' => 'admin/laporan-camat',
        'laporan' => 'laporan',
        'wilayah' => 'wilayah'
    ];

    $resource = $resourceMap[$controller] ?? $controller;
    $path = build_route_path($controller, $resource, $action, $id, $extra);
    unset($extra['type']);

    foreach (['id', 'id_user', 'id_profile', 'id_opd', 'id_kecamatan', 'id_desa', 'id_laporan_opd', 'id_laporan_camat', 'id_wagateway'] as $key) {
        unset($extra[$key]);
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

function build_route_path($controller, $resource, $action, $id, array $extra) {
    if ($controller === 'auth') {
        $authMap = [
            'index' => '',
            'admin' => 'login/admin',
            'camat' => 'login/camat',
            'opd' => 'login/opd',
            'login' => 'login',
            'logout' => 'logout'
        ];
        return $authMap[$action] ?? '';
    }

    if ($controller === 'dashboard') {
        $dashboardMap = [
            'admin' => 'admin/dashboard',
            'camat' => 'camat/dashboard',
            'opd' => 'opd/dashboard',
            'data' => 'admin/dashboard/data',
            'export' => 'admin/dashboard/export'
        ];
        return $dashboardMap[$action] ?? 'admin/dashboard';
    }

    if ($controller === 'laporan') {
        $type = $extra['type'] ?? 'camat';
        $laporanMap = [
            'index' => 'laporan',
            'generatepdf' => 'laporan/pdf',
            'generateexcel' => 'laporan/excel',
            'tandatangan' => 'laporan/tanda-tangan/' . rawurlencode($type) . '/' . rawurlencode((string) ($id ?? 0)),
            'uploadtandatangan' => 'laporan/tanda-tangan',
            'generatepdfwithsignature' => 'laporan/tanda-tangan/' . rawurlencode($type) . '/' . rawurlencode((string) ($id ?? 0)) . '/pdf'
        ];
        return $laporanMap[$action] ?? 'laporan';
    }

    if ($controller === 'wilayah') {
        $wilayahMap = [
            'index' => 'kecamatan',
            'indexkecamatan' => 'kecamatan',
            'indexdesa' => 'desa',
            'formkecamatan' => $id !== null ? 'kecamatan/' . rawurlencode((string) $id) . '/edit' : 'kecamatan/create',
            'formdesa' => $id !== null ? 'desa/' . rawurlencode((string) $id) . '/edit' : 'desa/create',
            'savekecamatan' => $id !== null ? 'kecamatan/' . rawurlencode((string) $id) : 'kecamatan',
            'savedesa' => $id !== null ? 'desa/' . rawurlencode((string) $id) : 'desa',
            'deletekecamatan' => $id !== null ? 'kecamatan/' . rawurlencode((string) $id) : 'kecamatan',
            'deletedesa' => $id !== null ? 'desa/' . rawurlencode((string) $id) : 'desa',
            'kecamatanoptions' => 'desa/options/kecamatan',
            'kecamatanstats' => $id !== null ? 'kecamatan/' . rawurlencode((string) $id) . '/stats' : 'kecamatan'
        ];
        return $wilayahMap[$action] ?? 'kecamatan';
    }

    $listActionMap = [
        'datapelapor' => [
            'form' => $id !== null ? $resource . '/' . rawurlencode((string) $id) . '/edit' : $resource . '/create',
            'save' => $id !== null ? $resource . '/' . rawurlencode((string) $id) : $resource,
            'delete' => $id !== null ? $resource . '/' . rawurlencode((string) $id) : $resource,
            'getdata' => $resource . '/list',
            'search' => $resource . '/search',
            'statistics' => $resource . '/statistics',
            'export' => $resource . '/export'
        ],
        'opd' => [
            'store' => $resource,
            'update' => $id !== null ? $resource . '/' . rawurlencode((string) $id) : $resource,
            'delete' => $id !== null ? $resource . '/' . rawurlencode((string) $id) : $resource,
            'list' => $resource . '/list'
        ],
        'kecamatan' => [
            'form' => $id !== null ? $resource . '/' . rawurlencode((string) $id) . '/edit' : $resource . '/create',
            'save' => $id !== null ? $resource . '/' . rawurlencode((string) $id) : $resource,
            'delete' => $id !== null ? $resource . '/' . rawurlencode((string) $id) : $resource,
            'getstats' => $id !== null ? $resource . '/' . rawurlencode((string) $id) . '/stats' : $resource
        ],
        'desa' => [
            'form' => $id !== null ? $resource . '/' . rawurlencode((string) $id) . '/edit' : $resource . '/create',
            'save' => $id !== null ? $resource . '/' . rawurlencode((string) $id) : $resource,
            'delete' => $id !== null ? $resource . '/' . rawurlencode((string) $id) : $resource,
            'getkecamatanoptions' => $resource . '/options/kecamatan',
            'getdesabykecamatan' => $resource . '/by-kecamatan'
        ],
        'profile' => [
            'store' => $resource,
            'update' => $id !== null ? $resource . '/' . rawurlencode((string) $id) : $resource,
            'delete' => $id !== null ? $resource . '/' . rawurlencode((string) $id) : $resource,
            'getprofilelist' => $resource . '/list'
        ],
        'wagateway' => [
            'form' => $id !== null ? $resource . '/' . rawurlencode((string) $id) . '/edit' : $resource . '/create',
            'send' => $id !== null ? $resource . '/' . rawurlencode((string) $id) : $resource,
            'delete' => $id !== null ? $resource . '/' . rawurlencode((string) $id) : $resource,
            'getdata' => $resource . '/list',
            'searchcontacts' => $resource . '/search-contacts',
            'export' => $resource . '/export',
            'bulksend' => $resource . '/bulk-send'
        ],
        'laporanopd' => [
            'store' => $resource,
            'update' => $id !== null ? $resource . '/' . rawurlencode((string) $id) : $resource,
            'delete' => $id !== null ? $resource . '/' . rawurlencode((string) $id) : $resource,
            'detail' => $id !== null ? $resource . '/' . rawurlencode((string) $id) : $resource,
            'download' => $id !== null ? $resource . '/' . rawurlencode((string) $id) . '/download' : $resource,
            'getstats' => $resource . '/stats'
        ],
        'laporancamat' => [
            'store' => $resource,
            'update' => $id !== null ? $resource . '/' . rawurlencode((string) $id) : $resource,
            'delete' => $id !== null ? $resource . '/' . rawurlencode((string) $id) : $resource,
            'detail' => $id !== null ? $resource . '/' . rawurlencode((string) $id) : $resource,
            'download' => $id !== null ? $resource . '/' . rawurlencode((string) $id) . '/download' : $resource,
            'updatestatus' => $id !== null ? $resource . '/' . rawurlencode((string) $id) . '/status' : $resource,
            'exporttoexcel' => $resource . '/export'
        ],
        'laporanopdadmin' => [
            'detail' => $id !== null ? $resource . '/' . rawurlencode((string) $id) : $resource,
            'edit' => $id !== null ? $resource . '/' . rawurlencode((string) $id) . '/edit' : $resource,
            'delete' => $id !== null ? $resource . '/' . rawurlencode((string) $id) : $resource,
            'updatestatus' => $id !== null ? $resource . '/' . rawurlencode((string) $id) . '/status' : $resource,
            'export' => $resource . '/export'
        ],
        'laporancamatadmin' => [
            'detail' => $id !== null ? $resource . '/' . rawurlencode((string) $id) : $resource,
            'edit' => $id !== null ? $resource . '/' . rawurlencode((string) $id) . '/edit' : $resource,
            'delete' => $id !== null ? $resource . '/' . rawurlencode((string) $id) : $resource,
            'updatestatus' => $id !== null ? $resource . '/' . rawurlencode((string) $id) . '/status' : $resource,
            'export' => $resource . '/export'
        ]
    ];

    if (isset($listActionMap[$controller][$action])) {
        return $listActionMap[$controller][$action];
    }

    if ($action === 'index') {
        return $resource;
    }
    if ($action === 'create' || $action === 'form') {
        return $id !== null ? $resource . '/' . rawurlencode((string) $id) . '/edit' : $resource . '/create';
    }
    if ($action === 'edit') {
        return $id !== null ? $resource . '/' . rawurlencode((string) $id) . '/edit' : $resource . '/create';
    }
    if (in_array($action, ['store', 'save'], true)) {
        return $id !== null ? $resource . '/' . rawurlencode((string) $id) : $resource;
    }
    if (in_array($action, ['detail', 'show'], true)) {
        return $id !== null ? $resource . '/' . rawurlencode((string) $id) : $resource;
    }
    if (in_array($action, ['update', 'delete'], true)) {
        return $id !== null ? $resource . '/' . rawurlencode((string) $id) : $resource;
    }

    return $resource . '/' . $action;
}

function current_path() {
    $requestUri = $_SERVER['REQUEST_URI'] ?? '';
    $path = parse_url($requestUri, PHP_URL_PATH) ?? '';
    if (defined('BASE_PATH') && BASE_PATH !== '' && strpos($path, BASE_PATH) === 0) {
        $path = substr($path, strlen(BASE_PATH));
    }
    return trim($path, '/');
}

function route_is($pattern) {
    $path = current_path();
    return $path === trim($pattern, '/');
}

function route_starts_with($prefix) {
    $path = current_path();
    $prefix = trim($prefix, '/');
    return $path === $prefix || strpos($path, $prefix . '/') === 0;
}
