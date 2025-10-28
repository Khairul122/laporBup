<?php 
$title = 'Laporan Camat - LaporBup';
include 'views/template/header.php'; 
?>
<?php include 'views/template/navbar.php'; ?>

<div class="main-content">
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h3 class="page-title mb-1">Laporan Camat</h3>
                <nav aria-label="breadcrumb">
                </nav>
            </div>
            <?php if ($_SESSION['role'] === 'camat'): ?>
            <div>
                <a href="index.php?controller=laporanCamat&action=create" class="btn btn-primary">
                    <i class="fas fa-plus-circle"></i> Tambah Laporan
                </a>
            </div>
            <?php endif; ?>
        </div>

        <!-- Search and Filter -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <form method="GET" action="index.php">
                    <input type="hidden" name="controller" value="laporanCamat">
                    <input type="hidden" name="action" value="index">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" name="search" placeholder="Cari laporan..." value="<?php echo htmlspecialchars($search ?? ''); ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" name="status">
                                <option value="">Semua Status</option>
                                <option value="baru" <?php echo (isset($status) && $status === 'baru') ? 'selected' : ''; ?>>Baru</option>
                                <option value="diproses" <?php echo (isset($status) && $status === 'diproses') ? 'selected' : ''; ?>>Diproses</option>
                                <option value="selesai" <?php echo (isset($status) && $status === 'selesai') ? 'selected' : ''; ?>>Selesai</option>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                </form>
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

        <!-- Laporan List -->
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <?php if (!empty($laporans)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Pelapor</th>
                                    <th>Desa</th>
                                    <th>Kecamatan</th>
                                    <th>Waktu Kejadian</th>
                                    <th>Tujuan</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($laporans as $index => $laporan): ?>
                                <tr>
                                    <td><?php echo $offset + $index + 1; ?></td>
                                    <td><?php echo htmlspecialchars($laporan['nama_pelapor']); ?></td>
                                    <td><?php echo htmlspecialchars($laporan['nama_desa']); ?></td>
                                    <td><?php echo htmlspecialchars($laporan['nama_kecamatan']); ?></td>
                                    <td><?php echo date('d M Y H:i', strtotime($laporan['waktu_kejadian'])); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $laporan['tujuan'] === 'bupati' ? 'primary' : ($laporan['tujuan'] === 'wakil bupati' ? 'info' : ($laporan['tujuan'] === 'sekda' ? 'warning' : 'secondary')); ?>">
                                            <?php echo ucfirst(str_replace('_', ' ', $laporan['tujuan'])); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo $laporan['status_laporan'] === 'baru' ? 'warning' : ($laporan['status_laporan'] === 'diproses' ? 'info' : 'success'); ?>">
                                            <?php echo ucfirst($laporan['status_laporan']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="index.php?controller=laporanCamat&action=detail&id=<?php echo $laporan['id_laporan_camat']; ?>" 
                                               class="btn btn-outline-primary" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <?php if ($_SESSION['role'] === 'camat' && $laporan['status_laporan'] === 'baru' && $laporan['id_user'] == $_SESSION['user_id']): ?>
                                                <a href="index.php?controller=laporanCamat&action=edit&id=<?php echo $laporan['id_laporan_camat']; ?>" 
                                                   class="btn btn-outline-secondary" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button class="btn btn-outline-danger delete-btn" 
                                                        data-id="<?php echo $laporan['id_laporan_camat']; ?>" 
                                                        title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                    <nav aria-label="Laporan pagination" class="mt-4">
                        <ul class="pagination justify-content-center">
                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?controller=laporanCamat&action=index&page=<?php echo $page - 1; ?><?php echo isset($search) && $search ? '&search=' . urlencode($search) : ''; ?><?php echo isset($status) && $status ? '&status=' . $status : ''; ?>">Sebelumnya</a>
                                </li>
                            <?php endif; ?>

                            <?php
                            $start = max(1, $page - 2);
                            $end = min($total_pages, $page + 2);
                            ?>

                            <?php for ($i = $start; $i <= $end; $i++): ?>
                                <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="?controller=laporanCamat&action=index&page=<?php echo $i; ?><?php echo isset($search) && $search ? '&search=' . urlencode($search) : ''; ?><?php echo isset($status) && $status ? '&status=' . $status : ''; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>

                            <?php if ($page < $total_pages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?controller=laporanCamat&action=index&page=<?php echo $page + 1; ?><?php echo isset($search) && $search ? '&search=' . urlencode($search) : ''; ?><?php echo isset($status) && $status ? '&status=' . $status : ''; ?>">Selanjutnya</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                    <?php endif; ?>

                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-file-alt text-muted" style="font-size: 3rem;"></i>
                        <h5 class="mt-3 text-muted">Belum ada laporan</h5>
                        <?php if ($_SESSION['role'] === 'camat'): ?>
                            <p class="text-muted">Buat laporan pertama Anda</p>
                            <a href="index.php?controller=laporanCamat&action=create" class="btn btn-primary">
                                <i class="fas fa-plus-circle"></i> Buat Laporan
                            </a>
                        <?php else: ?>
                            <p class="text-muted">Tidak ada laporan yang ditemukan</p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus laporan ini?</p>
                <p class="text-muted"><small>Laporan yang dihapus tidak dapat dikembalikan.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Hapus</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let deleteId = null;
    
    // Set up delete button event listeners
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function() {
            deleteId = this.getAttribute('data-id');
            const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            modal.show();
        });
    });
    
    // Confirm delete action
    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        if (deleteId) {
            fetch('index.php?controller=laporanCamat&action=delete&id=' + deleteId, {
                method: 'POST',
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Gagal menghapus laporan');
                }
            })
            .catch(error => {
                alert('Terjadi kesalahan saat menghapus laporan');
            });
        }
    });
});
</script>

<?php include 'views/template/footer.php'; ?>