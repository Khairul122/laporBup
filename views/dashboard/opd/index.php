<?php 
$title = 'Dashboard OPD - Madina Maju Madani';
include 'views/template/header.php'; 
?>
<?php include 'views/template/navbar.php'; ?>

<div class="fullscreen-container">
    <div class="fullscreen-content">
        <div class="content-row">
            <!-- Informasi Pengguna -->
            <div class="info-card">
                <h3 class="card-title">Informasi Pengguna</h3>
                <div class="user-name"><?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?></div>
                <div class="user-position">
                    <i class="fas fa-building"></i>
                    <span><?php echo htmlspecialchars($_SESSION['jabatan'] ?? 'OPD'); ?></span>
                </div>
                <div class="last-login">
                    <i class="fas fa-clock"></i>
                    Terakhir login: <?php echo date('d F Y H.i'); ?>
                </div>
            </div>

            <!-- Menu -->
            <div class="menu-card">
                <h3 class="card-title">Menu Utama</h3>
                <div class="menu-grid">
                    <div class="menu-item" onclick="location.href='index.php?controller=dashboard&action=opd'">
                        <div class="menu-icon">
                            <i class="fas fa-tachometer-alt"></i>
                        </div>
                        <div class="menu-text">Dashboard</div>
                    </div>

                    <div class="menu-item" onclick="location.href='index.php?controller=laporanOPD&action=index'">
                        <div class="menu-icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div class="menu-text">Laporan Saya</div>
                    </div>

                    <div class="menu-item" onclick="location.href='index.php?controller=laporanOPD&action=create'">
                        <div class="menu-icon">
                            <i class="fas fa-plus-circle"></i>
                        </div>
                        <div class="menu-text">Buat Laporan Baru</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Additional spacing for dashboard cards */
.fullscreen-container .content-row {
    gap: clamp(25px, 4vw, 40px);
    margin-bottom: 0;
}

.fullscreen-container .info-card,
.fullscreen-container .menu-card {
    padding: clamp(25px, 4vw, 40px);
    margin-bottom: 0;
}

/* Better menu spacing */
.fullscreen-container .menu-grid {
    gap: clamp(20px, 3vw, 25px);
}

.fullscreen-container .menu-item {
    padding: clamp(25px, 4vw, 30px) clamp(15px, 2.5vw, 20px);
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .fullscreen-container .content-row {
        gap: clamp(20px, 3vw, 30px);
    }

    .fullscreen-container .info-card,
    .fullscreen-container .menu-card {
        padding: clamp(20px, 3vw, 30px);
    }
}

@media (max-width: 480px) {
    .fullscreen-container .content-row {
        gap: clamp(15px, 2.5vw, 20px);
    }

    .fullscreen-container .info-card,
    .fullscreen-container .menu-card {
        padding: clamp(15px, 2.5vw, 20px);
    }
}
</style>

<?php include 'views/template/footer.php'; ?>