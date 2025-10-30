<?php
// Load profile based on user role
require_once __DIR__ . '/../../models/ProfileModel.php';
$profileModel = new ProfileModel();
$current_role = $_SESSION['role'] ?? 'user';

// Get profile for the current user's role
$profiles = $profileModel->getProfilesByRole($current_role);
$profile = !empty($profiles) ? $profiles[0] : null;

// Fallback profile data if no profile exists for the role
$profile_nama_aplikasi = $profile ? $profile['nama_aplikasi'] : 'LaporBup';
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
        <a href="index.php?controller=dashboard&action=<?php echo htmlspecialchars($current_role); ?>">Beranda</a>
        <a href="index.php?controller=<?php echo ($current_role === 'opd') ? 'laporanOPD' : 'laporanCamat'; ?>&action=index">Laporan</a>
        <!-- User Info & Logout -->
        <div class="user-section">
            <a href="index.php?controller=auth&action=logout">
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