<?php 
$title = (isset($laporan) ? 'Edit Laporan' : 'Tambah Laporan') . ' - LaporBup';
include 'views/template/header.php'; 
?>
<?php include 'views/template/navbar.php'; ?>

<div class="main-content">
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h3 class="page-title mb-1"><?php echo isset($laporan) ? 'Edit Laporan' : 'Tambah Laporan'; ?></h3>
            </div>
        </div>

        <!-- Status Messages -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Form -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form method="POST" action="index.php?controller=laporanCamat&action=<?php echo isset($laporan) ? 'update&id=' . $laporan['id_laporan_camat'] : 'store'; ?>" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nama_pelapor" class="form-label">Nama Pelapor <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nama_pelapor" name="nama_pelapor" 
                                       value="<?php echo isset($laporan) ? htmlspecialchars($laporan['nama_pelapor']) : htmlspecialchars($_SESSION['username'] ?? ''); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nama_desa" class="form-label">Nama Desa <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nama_desa" name="nama_desa" 
                                       value="<?php echo isset($laporan) ? htmlspecialchars($laporan['nama_desa']) : ''; ?>" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nama_kecamatan" class="form-label">Nama Kecamatan <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nama_kecamatan" name="nama_kecamatan" 
                                       value="<?php echo isset($laporan) ? htmlspecialchars($laporan['nama_kecamatan']) : ''; ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="waktu_kejadian" class="form-label">Waktu Kejadian <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control" id="waktu_kejadian" name="waktu_kejadian" 
                                       value="<?php echo isset($laporan) ? date('Y-m-d\\TH:i', strtotime($laporan['waktu_kejadian'])) : ''; ?>" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="tujuan" class="form-label">Tujuan Laporan <span class="text-danger">*</span></label>
                        <select class="form-select" id="tujuan" name="tujuan" required>
                            <option value="">Pilih Tujuan</option>
                            <option value="bupati" <?php echo (isset($laporan) && $laporan['tujuan'] === 'bupati') ? 'selected' : ''; ?>>Bupati</option>
                            <option value="wakil bupati" <?php echo (isset($laporan) && $laporan['tujuan'] === 'wakil bupati') ? 'selected' : ''; ?>>Wakil Bupati</option>
                            <option value="sekda" <?php echo (isset($laporan) && $laporan['tujuan'] === 'sekda') ? 'selected' : ''; ?>>Sekda</option>
                            <option value="opd" <?php echo (isset($laporan) && $laporan['tujuan'] === 'opd') ? 'selected' : ''; ?>>OPD</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="uraian_laporan" class="form-label">Uraian Laporan <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="uraian_laporan" name="uraian_laporan" rows="5" required><?php echo isset($laporan) ? htmlspecialchars($laporan['uraian_laporan']) : ''; ?></textarea>
                        <div class="form-text">Jelaskan secara rinci peristiwa yang terjadi</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="upload_file" class="form-label">File Pendukung (Opsional)</label>
                        <input type="file" class="form-control" id="upload_file" name="upload_file" accept=".jpg,.jpeg,.png,.gif,.webp,.mp4,.mov,.avi,.mkv,.webm,.pdf,.doc,.docx">
                        <div class="form-text">Format yang diperbolehkan: Foto (JPG, JPEG, PNG, GIF, WEBP) dan Video (MP4, MOV, AVI, MKV, WEBM), serta dokumen (PDF, DOC, DOCX). Ukuran maksimal: 10MB untuk foto/dokumen, 100MB untuk video</div>
                        
                        <?php if (isset($laporan) && $laporan['upload_file']): ?>
                        <div class="mt-2">
                            <label class="form-label">File Saat Ini:</label>
                            <a href="<?php echo htmlspecialchars($laporan['upload_file']); ?>" target="_blank" class="text-primary">
                                <i class="fas fa-file"></i> <?php echo basename($laporan['upload_file']); ?>
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="index.php?controller=laporanCamat&action=index" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fas fa-save"></i> <?php echo isset($laporan) ? 'Perbarui' : 'Simpan'; ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'views/template/footer.php'; ?>