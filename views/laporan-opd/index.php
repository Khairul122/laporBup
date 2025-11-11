<?php
$title = $title ?? 'Daftar Laporan OPD - ';
include 'views/template/header.php';
?>
<?php include 'views/template/navbar.php'; ?>

<div class="fullscreen-container">
    <div class="fullscreen-content">
    <!-- Header Section -->
    <div class="page-header">
        <div class="header-content">
            <h1 class="page-title">
                <i class="fas fa-building"></i>
                <?php echo htmlspecialchars($title); ?>
            </h1>
            <div class="page-actions">
                <a href="index.php?controller=laporanOPD&action=create" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    Buat Laporan Baru
                </a>
            </div>
        </div>
    </div>

    
    <!-- Search and Filter -->
    <div class="search-filter-container">
        <div class="search-section">
            <form method="GET" action="index.php" class="search-form">
                <input type="hidden" name="controller" value="laporanOPD">
                <input type="hidden" name="action" value="index">

                <div class="search-input-group">
                    <input type="text"
                           name="search"
                           placeholder="Cari nama OPD, kegiatan, atau uraian..."
                           value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>"
                           class="search-input">
                    <button type="submit" class="search-btn">
                        <i class="fas fa-search"></i>
                    </button>
                </div>

                <div class="filter-group">
                    <select name="status" id="statusFilter" class="filter-select" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="baru" <?php echo ($_GET['status'] ?? '') === 'baru' ? 'selected' : ''; ?>>
                            Menunggu Proses
                        </option>
                        <option value="diproses" <?php echo ($_GET['status'] ?? '') === 'diproses' ? 'selected' : ''; ?>>
                            Sedang Diproses
                        </option>
                        <option value="selesai" <?php echo ($_GET['status'] ?? '') === 'selesai' ? 'selected' : ''; ?>>
                            Selesai
                        </option>
                    </select>

                    <button type="button" class="reset-btn" onclick="window.location.href='index.php?controller=laporanOPD&action=index'">
                        <i class="fas fa-times"></i>
                        Reset
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Laporan Table -->
    <div class="table-container">
        <?php if (empty($laporan)): ?>
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-inbox"></i>
                </div>
                <h3>Belum Ada Laporan</h3>
                <p>Anda belum memiliki laporan. Mulai buat laporan pertama Anda.</p>
                <a href="index.php?controller=laporanOPD&action=create" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    Buat Laporan Baru
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Nama OPD</th>
                            <th>Nama Kegiatan</th>
                            <th>Uraian</th>
                            <th>Tujuan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        foreach ($laporan as $item):
                            $statusClass = '';
                            $statusText = '';

                            switch($item['status_laporan']) {
                                case 'baru':
                                    $statusClass = 'status-new';
                                    $statusText = 'Baru';
                                    break;
                                case 'diproses':
                                    $statusClass = 'status-processing';
                                    $statusText = 'Diproses';
                                    break;
                                case 'selesai':
                                    $statusClass = 'status-completed';
                                    $statusText = 'Selesai';
                                    break;
                                default:
                                    $statusClass = 'status-new';
                                    $statusText = ucfirst($item['status_laporan']);
                                    break;
                            }
                        ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td>
                                <div class="date-info">
                                    <?php echo date('d M Y', strtotime($item['created_at'])); ?>
                                    <small><?php echo date('H.i', strtotime($item['created_at'])); ?></small>
                                </div>
                            </td>
                            <td>
                                <div class="opd-name">
                                    <?php echo htmlspecialchars($item['nama_opd']); ?>
                                </div>
                            </td>
                            <td>
                                <div class="activity-name">
                                    <?php echo htmlspecialchars($item['nama_kegiatan']); ?>
                                </div>
                            </td>
                            <td>
                                <div class="description">
                                    <?php echo nl2br(htmlspecialchars($item['uraian_laporan'])); ?>
                                </div>
                            </td>
                            <td>
                                <div class="tujuan-info">
                                    <?php echo ucfirst(htmlspecialchars($item['tujuan'])); ?>
                                </div>
                            </td>
                            <td>
                                <span class="status-badge <?php echo $statusClass; ?>">
                                    <?php echo $statusText; ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="index.php?controller=laporanOPD&action=detail&id=<?php echo $item['id_laporan_opd']; ?>"
                                       class="btn-action btn-view"
                                       title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <?php if ($item['status_laporan'] === 'baru'): ?>
                                        <a href="index.php?controller=laporanOPD&action=edit&id=<?php echo $item['id_laporan_opd']; ?>"
                                           class="btn-action btn-edit"
                                           title="Edit Laporan">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    <?php endif; ?>

                                    
                                    <?php if ($item['status_laporan'] === 'baru'): ?>
                                        <button type="button"
                                                class="btn-action btn-delete"
                                                title="Hapus Laporan"
                                                onclick="confirmDelete(<?php echo $item['id_laporan_opd']; ?>)">
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
        <?php endif; ?>
    </div>
</div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Konfirmasi Hapus</h3>
            <button type="button" class="close-btn" onclick="closeModal()">&times;</button>
        </div>
        <div class="modal-body">
            <p>Apakah Anda yakin ingin menghapus laporan ini?</p>
            <p class="text-danger">Tindakan ini tidak dapat dibatalkan.</p>
        </div>
        <div class="modal-footer">
            <form id="deleteForm" method="POST" style="display: inline;">
                <input type="hidden" name="id" id="deleteId">
                <button type="submit" class="btn btn-danger">Hapus</button>
            </form>
            <button type="button" class="btn btn-secondary" onclick="closeModal()">Batal</button>
        </div>
    </div>
</div>

<style>
/* Page Header */
.page-header {
    background: white;
    padding: clamp(20px, 3vw, 30px);
    border-radius: clamp(15px, 2vw, 20px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    margin-bottom: clamp(20px, 3vw, 30px);
    margin-top: 0;
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: clamp(15px, 2vw, 20px);
}

.page-title {
    font-size: clamp(20px, 3vw, 24px);
    font-weight: 600;
    color: var(--dark-gray);
    margin: 0;
    display: flex;
    align-items: center;
    gap: clamp(10px, 1.5vw, 15px);
}

.page-title i {
    color: var(--primary-blue);
}


/* Search and Filter */
.search-filter-container {
    background: white;
    padding: clamp(20px, 2.5vw, 25px);
    border-radius: clamp(12px, 1.5vw, 16px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    margin-bottom: clamp(20px, 3vw, 30px);
}

.search-form {
    display: flex;
    flex-wrap: wrap;
    gap: clamp(15px, 2vw, 20px);
    align-items: center;
}

.search-input-group {
    display: flex;
    flex: 1;
    min-width: 250px;
    max-width: 400px;
}

.search-input {
    flex: 1;
    padding: clamp(10px, 1.5vw, 12px) clamp(15px, 2vw, 18px);
    border: 1px solid #ddd;
    border-radius: clamp(8px, 1vw, 10px) 0 0 clamp(8px, 1vw, 10px);
    font-size: clamp(14px, 1.8vw, 16px);
    transition: border-color 0.3s ease;
}

.search-input:focus {
    outline: none;
    border-color: var(--primary-blue);
}

.search-btn {
    background: var(--primary-blue);
    color: white;
    border: none;
    padding: clamp(10px, 1.5vw, 12px) clamp(15px, 2vw, 18px);
    border-radius: 0 clamp(8px, 1vw, 10px) clamp(8px, 1vw, 10px) 0;
    cursor: pointer;
    transition: background 0.3s ease;
}

.search-btn:hover {
    background: var(--secondary-blue);
}

.filter-group {
    display: flex;
    gap: clamp(10px, 1.5vw, 15px);
    align-items: center;
}

.filter-select {
    padding: clamp(10px, 1.5vw, 12px) clamp(12px, 1.8vw, 15px);
    border: 1px solid #ddd;
    border-radius: clamp(6px, 1vw, 8px);
    font-size: clamp(14px, 1.8vw, 16px);
    min-width: 150px;
}

.reset-btn {
    background: #6c757d;
    color: white;
    border: none;
    padding: clamp(10px, 1.5vw, 12px) clamp(12px, 1.8vw, 15px);
    border-radius: clamp(6px, 1vw, 8px);
    cursor: pointer;
    font-size: clamp(14px, 1.8vw, 16px);
    transition: background 0.3s ease;
}

.reset-btn:hover {
    background: #5a6268;
}

/* Table */
.table-container {
    background: white;
    border-radius: clamp(12px, 1.5vw, 16px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    overflow: hidden;
}

.table-responsive {
    overflow-x: auto;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
    font-size: clamp(13px, 1.8vw, 15px);
}

.data-table th {
    background: white;
    color: #333;
    padding: clamp(12px, 1.8vw, 15px);
    text-align: left;
    font-weight: 600;
    white-space: nowrap;
    border-bottom: 2px solid var(--primary-blue);
}

.data-table td {
    padding: clamp(12px, 1.8vw, 15px);
    border-bottom: 1px solid #f0f0f0;
    vertical-align: top;
}

.data-table tbody tr:hover {
    background: #f8f9fa;
}

/* Table Content Styling */
.date-info, .opd-name, .activity-name, .tujuan-info {
    font-weight: 500;
}

.date-info small {
    color: #666;
    font-size: 11px;
    margin-left: 4px;
}

.description {
    line-height: 1.5;
    color: #555;
    max-width: 300px;
    word-wrap: break-word;
    white-space: normal;
    overflow-wrap: break-word;
}

/* Status Badge */
.status-badge {
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    white-space: nowrap;
}

.status-new {
    background: #fff3cd;
    color: #856404;
    border: 1px solid #ffeaa7;
}

.status-diproses {
    background: #cce7ff;
    color: #004085;
    border: 1px solid #b3d7ff;
}

.status-selesai {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

/* Action Buttons */
.action-buttons {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
}

.btn-action {
    width: 32px;
    height: 32px;
    border-radius: 6px;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    font-size: 14px;
}

.btn-view {
    background: #e3f2fd;
    color: #1976d2;
}

.btn-view:hover {
    background: #1976d2;
    color: white;
    transform: scale(1.1);
}

.btn-edit {
    background: #fff3e0;
    color: #f57c00;
}

.btn-edit:hover {
    background: #f57c00;
    color: white;
    transform: scale(1.1);
}


.btn-delete {
    background: #ffebee;
    color: #c62828;
}

.btn-delete:hover {
    background: #c62828;
    color: white;
    transform: scale(1.1);
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: clamp(40px, 6vw, 60px);
    color: #666;
}

.empty-icon {
    font-size: clamp(48px, 6vw, 64px);
    color: #ddd;
    margin-bottom: clamp(15px, 2vw, 20px);
}

.empty-state h3 {
    font-size: clamp(18px, 2.5vw, 22px);
    margin-bottom: clamp(10px, 1.5vw, 15px);
    color: #333;
}

.empty-state p {
    font-size: clamp(14px, 1.8vw, 16px);
    margin-bottom: clamp(20px, 3vw, 25px);
    max-width: 400px;
    margin-left: auto;
    margin-right: auto;
}

/* Buttons */
.btn {
    padding: clamp(10px, 1.5vw, 12px) clamp(20px, 2.5vw, 25px);
    border: none;
    border-radius: clamp(6px, 1vw, 8px);
    font-size: clamp(14px, 1.8vw, 16px);
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.btn-primary {
    background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));
    color: white;
}

.btn-primary:hover {
    background: linear-gradient(135deg, var(--secondary-blue), var(--primary-blue));
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(47, 88, 205, 0.3);
}

.btn-danger {
    background: #dc3545;
    color: white;
}

.btn-danger:hover {
    background: #c82333;
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background: #5a6268;
}

/* Modal */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    animation: fadeIn 0.3s ease;
    padding-top: var(--header-height);
}

.modal-content {
    background: white;
    margin: 10% auto;
    padding: 0;
    border-radius: 12px;
    width: 90%;
    max-width: 500px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    animation: slideIn 0.3s ease;
}

.modal-header {
    padding: 20px;
    border-bottom: 1px solid #eee;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h3 {
    margin: 0;
    color: #333;
}

.close-btn {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: #999;
    padding: 0;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: all 0.3s ease;
}

.close-btn:hover {
    background: #f0f0f0;
    color: #333;
}

.modal-body {
    padding: 20px;
    color: #666;
}

.modal-footer {
    padding: 20px;
    border-top: 1px solid #eee;
    display: flex;
    gap: 10px;
    justify-content: flex-end;
}

.text-danger {
    color: #dc3545;
    font-weight: 500;
}

/* Animations */
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideIn {
    from {
        transform: translateY(-30px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

/* Responsive */
@media (max-width: 768px) {
    .header-content {
        flex-direction: column;
        align-items: stretch;
    }

    .page-title {
        justify-content: center;
    }

    .page-actions {
        text-align: center;
    }

    .search-form {
        flex-direction: column;
    }

    .search-input-group {
        max-width: 100%;
    }

    .filter-group {
        width: 100%;
        justify-content: space-between;
    }

    
    .action-buttons {
        justify-content: center;
    }

    .description {
        max-width: 200px;
        font-size: 12px;
        line-height: 1.4;
    }
}

@media (max-width: 480px) {
    
    .data-table {
        font-size: 12px;
    }

    .data-table th,
    .data-table td {
        padding: 8px 6px;
    }

    .btn-action {
        width: 28px;
        height: 28px;
        font-size: 12px;
    }
}
</style>

<script>
// Delete confirmation
function confirmDelete(id) {
    document.getElementById('deleteId').value = id;
    document.getElementById('deleteForm').action = 'index.php?controller=laporanOPD&action=delete';
    document.getElementById('deleteModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('deleteModal').style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('deleteModal');
    if (event.target === modal) {
        closeModal();
    }
}

// Show notifications
document.addEventListener('DOMContentLoaded', function() {
    // Success message
    <?php if (isset($_SESSION['success'])): ?>
        showNotification('<?php echo addslashes($_SESSION['success']); ?>', 'success');
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    // Error message
    <?php if (isset($_SESSION['error'])): ?>
        showNotification('<?php echo addslashes($_SESSION['error']); ?>', 'error');
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
});

function showNotification(message, type) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
        <span>${message}</span>
        <button onclick="this.parentElement.remove()">&times;</button>
    `;

    // Add to page
    document.body.appendChild(notification);

    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 5000);
}
</script>

<style>
/* Notification Styles */
.notification {
    position: fixed;
    top: calc(var(--header-height) + 20px);
    right: 20px;
    background: white;
    padding: 15px 20px;
    border-radius: 8px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    display: flex;
    align-items: center;
    gap: 10px;
    z-index: 9999;
    min-width: 300px;
    animation: slideInRight 0.3s ease;
}

.notification-success {
    border-left: 4px solid #28a745;
    color: #155724;
}

.notification-error {
    border-left: 4px solid #dc3545;
    color: #721c24;
}

.notification i {
    font-size: 18px;
    flex-shrink: 0;
}

.notification-success i {
    color: #28a745;
}

.notification-error i {
    color: #dc3545;
}

.notification button {
    background: none;
    border: none;
    font-size: 18px;
    cursor: pointer;
    color: #999;
    margin-left: auto;
    padding: 0;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
}

.notification button:hover {
    background: #f0f0f0;
}

@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}
</style>

<?php include 'views/template/footer.php'; ?>