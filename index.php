<?php

require_once 'config/koneksi.php';

session_set_cookie_params([
    'lifetime' => SESSION_LIFETIME,
    'path' => '/',
    'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_name(SESSION_NAME);
session_start();

require_once 'core/BaseController.php';
require_once 'core/Router.php';

applySecurityHeaders();

$router = new Router();

require_once 'core/routes.php';

$router->dispatch();