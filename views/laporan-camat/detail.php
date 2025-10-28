<?php 
$title = 'Detail Laporan - LaporBup';
include 'views/template/header.php'; 
?>
<?php include 'views/template/navbar.php'; ?>

<div class="main-content">
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h3 class="page-title mb-1">Detail Laporan</h3>
                <nav aria-label="breadcrumb"></nav>
                </nav>
            </div>
            <div>
                <a href="index.php?controller=laporanCamat&action=index" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
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

        <!-- Laporan Detail Card -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-file-alt"></i> Laporan Camat
                    <span class="float-end">
                        <span class="badge bg-light text-dark"><?php echo ucfirst($laporan['status_laporan']); ?></span>
                    </span>
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td width="30%"><strong>ID Laporan</strong></td>
                                <td width="5%">:</td>
                                <td><?php echo $laporan['id_laporan_camat']; ?></td>
                            </tr>
                            <tr>
                                <td><strong>Nama Pelapor</strong></td>
                                <td>:</td>
                                <td><?php echo htmlspecialchars($laporan['nama_pelapor']); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Nama Desa</strong></td>
                                <td>:</td>
                                <td><?php echo htmlspecialchars($laporan['nama_desa']); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Nama Kecamatan</strong></td>
                                <td>:</td>
                                <td><?php echo htmlspecialchars($laporan['nama_kecamatan']); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Waktu Kejadian</strong></td>
                                <td>:</td>
                                <td><?php echo date('d F Y H:i', strtotime($laporan['waktu_kejadian'])); ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td width="30%"><strong>Tujuan</strong></td>
                                <td width="5%">:</td>
                                <td>
                                    <span class="badge bg-<?php echo $laporan['tujuan'] === 'bupati' ? 'primary' : ($laporan['tujuan'] === 'wakil bupati' ? 'info' : ($laporan['tujuan'] === 'sekda' ? 'warning' : 'secondary')); ?>">
                                        <?php echo ucfirst(str_replace('_', ' ', $laporan['tujuan'])); ?>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Status</strong></td>
                                <td>:</td>
                                <td>
                                    <span class="badge bg-<?php echo $laporan['status_laporan'] === 'baru' ? 'warning' : ($laporan['status_laporan'] === 'diproses' ? 'info' : 'success'); ?>">
                                        <?php echo ucfirst($laporan['status_laporan']); ?>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Dibuat Tanggal</strong></td>
                                <td>:</td>
                                <td><?php echo date('d F Y H:i', strtotime($laporan['created_at'])); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Diupdate Tanggal</strong></td>
                                <td>:</td>
                                <td><?php echo date('d F Y H:i', strtotime($laporan['updated_at'])); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <hr>
                
                <div class="mb-3">
                    <h6><strong>Uraian Laporan</strong></h6>
                    <p class="text-muted"><?php echo nl2br(htmlspecialchars($laporan['uraian_laporan'])); ?></p>
                </div>
                
                <?php if ($laporan['upload_file']): ?>
                <div class="mb-3">
                    <h6><strong>File Pendukung</strong></h6>
                    <?php 
                    $file_path = $laporan['upload_file'];
                    $file_ext = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
                    $is_image = in_array($file_ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                    $is_video = in_array($file_ext, ['mp4', 'mov', 'avi', 'mkv', 'webm']);
                    ?>
                    
                    <?php if ($is_image): ?>
                        <div class="mb-3">
                            <img src="<?php echo htmlspecialchars($file_path); ?>" class="img-fluid rounded" style="max-height: 400px; object-fit: contain;" alt="File pendukung">
                        </div>
                    <?php elseif ($is_video): ?>
                        <div class="mb-3">
                            <video controls class="img-fluid rounded" style="max-width: 100%; max-height: 400px;">
                                <source src="<?php echo htmlspecialchars($file_path); ?>" type="video/<?php echo $file_ext; ?>">
                                Browser Anda tidak mendukung pemutar video.
                            </video>
                        </div>
                    <?php else: ?>
                        <a href="<?php echo htmlspecialchars($file_path); ?>" target="_blank" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-file-download"></i> Download File
                        </a>
                    <?php endif; ?>
                    
                    <small class="text-muted ms-2"><?php echo basename($file_path); ?></small>
                </div>
                <?php endif; ?>
                
                <!-- Action Buttons -->
                <div class="d-flex justify-content-between mt-4">
                    <?php if ($_SESSION['role'] === 'camat' && $laporan['status_laporan'] === 'baru' && $laporan['id_user'] == $_SESSION['user_id']): ?>
                    <div>
                        <a href="index.php?controller=laporanCamat&action=edit&id=<?php echo $laporan['id_laporan_camat']; ?>" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($_SESSION['role'] === 'admin'): ?>
                    <div>
                        <form method="POST" action="index.php?controller=laporanCamat&action=updateStatus&id=<?php echo $laporan['id_laporan_camat']; ?>" class="d-inline">
                            <select name="status" class="form-select d-inline w-auto me-2" required>
                                <option value="">Ubah Status</option>
                                <option value="baru" <?php echo $laporan['status_laporan'] === 'baru' ? 'selected' : ''; ?>>Baru</option>
                                <option value="diproses" <?php echo $laporan['status_laporan'] === 'diproses' ? 'selected' : ''; ?>>Diproses</option>
                                <option value="selesai" <?php echo $laporan['status_laporan'] === 'selesai' ? 'selected' : ''; ?>>Selesai</option>
                            </select>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-sync-alt"></i> Update Status
                            </button>
                        </form>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'views/template/footer.php'; ?>