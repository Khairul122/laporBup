<?php
// Mulai session
session_start();

// Memuat koneksi database
require_once 'config/koneksi.php';

// Menentukan controller dan action default
$controller = isset($_GET['controller']) ? $_GET['controller'] : 'auth';
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

// Handle special controller cases
$controllerMap = [
    'laporanOPDAdmin' => 'LaporanOPDAdminController',
    'laporanCamatAdmin' => 'LaporanCamatAdminController',
    'auth' => 'AuthController',
    'dashboard' => 'DashboardController'
];

// Menentukan file controller dan class
if (isset($controllerMap[$controller])) {
    $controllerClass = $controllerMap[$controller];
    $controllerFile = 'controllers/' . $controllerClass . '.php';
} else {
    $controllerClass = ucfirst($controller) . 'Controller';
    $controllerFile = 'controllers/' . $controllerClass . '.php';
}

// Memeriksa apakah file controller ada
if (file_exists($controllerFile)) {
    require_once $controllerFile;

    // Membuat instance controller
    if (class_exists($controllerClass)) {
        $controllerInstance = new $controllerClass();

        // Memeriksa apakah method (action) ada di dalam controller
        if (method_exists($controllerInstance, $action)) {
            $controllerInstance->$action();
        } else {
            // Jika action tidak ditemukan, gunakan action default
            if (method_exists($controllerInstance, 'index')) {
                $controllerInstance->index();
            } else {
                echo "Action '$action' not found in controller '$controllerClass'";
            }
        }
    } else {
        echo "Controller class '$controllerClass' not found";
    }
} else {
    // Jika controller tidak ditemukan, tampilkan halaman 404
    echo "Controller file '$controllerFile' not found";
}
?>