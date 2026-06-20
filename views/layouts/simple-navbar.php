<?php

require_once __DIR__ . '/../../models/ProfileModel.php';
$profileModel = new ProfileModel();
$current_role = $_SESSION['role'] ?? 'user';

$profiles = $profileModel->getProfilesByRole($current_role);
$profile = !empty($profiles) ? $profiles[0] : null;

$profile_nama_aplikasi = $profile ? $profile['nama_aplikasi'] : '';
$profile_logo = $profile ? $profile['logo'] : null;
?>

<header class="header">
    <div class="logo">
        <?php if ($profile_logo && file_exists($profile_logo)): ?>
            <img src="<?php echo $profile_logo; ?>" alt="Logo" class="app-logo">
        <?php else: ?>
            <i class="fas fa-landmark"></i>
        <?php endif; ?>
        <span>
            <?php echo htmlspecialchars($profile_nama_aplikasi); ?>
        </span>
    </div>
    <nav class="nav-menu">
        <?php
        $current_role = $_SESSION['role'] ?? 'user';
        $current_username = $_SESSION['username'] ?? 'User';
        $current_jabatan = $_SESSION['jabatan'] ?? 'User';
        ?>
        <a href="<?= route('dashboard', $current_role) ?>">Beranda</a>
        <a href="<?= route(($current_role === 'opd') ? 'laporanOPD' : 'laporanCamat', 'index') ?>">Laporan</a>
        
        <div class="user-section">
            <a href="<?= route('auth', 'logout') ?>">
                <span>Keluar</span>
            </a>
    </nav>
</header>

<style>
    .app-logo {
        height: 30px;
        width: auto;
        margin-right: 12px;
        vertical-align: middle;
        border-radius: 4px;
    }
    
    .logo {
        display: flex;
        align-items: center;
    }
</style>