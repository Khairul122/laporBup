<?php

session_start();

require_once 'config/koneksi.php';
require_once 'core/BaseController.php';
require_once 'core/Router.php';

applySecurityHeaders();

$router = new Router();

require_once 'core/routes.php';

$router->dispatch();