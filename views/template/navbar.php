<header class="header">
    <div class="logo">
        <i class="fas fa-landmark"></i>
        <span>
            <?php
            $current_role = $_SESSION['role'] ?? 'user';
            switch ($current_role) {
                case 'camat':
                    echo 'Silap Gawat';
                    break;
                case 'opd':
                    echo 'Madina Maju Madani';
                    break;
                default:
                    echo 'LaporBup';
                    break;
            }
            ?>
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