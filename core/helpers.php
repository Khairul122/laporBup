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
