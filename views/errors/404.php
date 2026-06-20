<?php $title = '404 - Halaman Tidak Ditemukan'; ?>
<?php include 'views/layouts/simple-header.php'; ?>

<div class="fullscreen-container">
    <div class="fullscreen-content d-flex align-items-center justify-content-center" style="min-height: 100%;">
        <div class="text-center">
            <div class="error-404-code">404</div>
            <i class="fas fa-compass error-404-icon"></i>
            <p class="error-404-message"><?= htmlspecialchars($message ?? 'Halaman ini tidak ada.') ?></p>
            <a href="<?= url('index.php') ?>" class="btn-back-home">
                <i class="fas fa-arrow-left me-2"></i>Kembali ke Halaman Utama
            </a>
        </div>
    </div>
</div>

<?php include 'views/layouts/simple-footer.php'; ?>
