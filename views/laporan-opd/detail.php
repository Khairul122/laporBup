<?php
$title = 'Detail Laporan OPD - LaporBup';
include 'views/template/header.php';
?>
<?php include 'views/template/navbar.php'; ?>

<div class="fullscreen-container">
    <div class="fullscreen-content">
        <!-- Page Header -->
        <div class="page-header">
            <div class="header-content">
                <h1 class="page-title">
                    <i class="fas fa-file-alt"></i>
                    Detail Laporan OPD
                </h1>
            </div>
        </div>

        <!-- Status Card -->
        <div class="status-card">
            <div class="status-content">
                <div class="status-badge <?php echo getStatusClass($laporan['status_laporan']); ?>">
                    <i class="fas fa-<?php echo getStatusIcon($laporan['status_laporan']); ?>"></i>
                    <span><?php echo getStatusText($laporan['status_laporan']); ?></span>
                </div>
                <div class="status-meta">
                    <div class="created-info">
                        <i class="fas fa-calendar-plus"></i>
                        <span>Dibuat: <?php echo date('d F Y H.i', strtotime($laporan['created_at'])); ?></span>
                    </div>
                    <?php if ($laporan['updated_at'] !== $laporan['created_at']): ?>
                        <div class="updated-info">
                            <i class="fas fa-edit"></i>
                            <span>Diupdate: <?php echo date('d F Y H.i', strtotime($laporan['updated_at'])); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Detail Container -->
        <div class="detail-container">
            <div class="detail-grid">
                <!-- Informasi OPD -->
                <div class="detail-section">
                    <h2 class="section-title">
                        <i class="fas fa-building"></i>
                        Informasi OPD
                    </h2>
                    <div class="detail-content">
                        <div class="detail-item">
                            <label>Nama OPD</label>
                            <div class="detail-value">
                                <i class="fas fa-hospital"></i>
                                <?php echo htmlspecialchars($laporan['nama_opd']); ?>
                            </div>
                        </div>
                        <div class="detail-item">
                            <label>Pelapor</label>
                            <div class="detail-value">
                                <i class="fas fa-user"></i>
                                <div class="user-info">
                                    <span class="user-name"><?php echo htmlspecialchars($laporan['username']); ?></span>
                                    <span class="user-role"><?php echo htmlspecialchars($laporan['jabatan']); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informasi Kegiatan -->
                <div class="detail-section">
                    <h2 class="section-title">
                        <i class="fas fa-tasks"></i>
                        Informasi Kegiatan
                    </h2>
                    <div class="detail-content">
                        <div class="detail-item">
                            <label>Nama Kegiatan</label>
                            <div class="detail-value">
                                <i class="fas fa-clipboard-list"></i>
                                <?php echo htmlspecialchars($laporan['nama_kegiatan']); ?>
                            </div>
                        </div>
                        <div class="detail-item">
                            <label>Tujuan Pengiriman</label>
                            <div class="detail-value">
                                <i class="fas fa-paper-plane"></i>
                                <?php echo ucfirst(htmlspecialchars($laporan['tujuan'])); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Uraian Laporan -->
            <div class="detail-section full-width">
                <h2 class="section-title">
                    <i class="fas fa-file-alt"></i>
                    Uraian Laporan
                </h2>
                <?php echo nl2br(htmlspecialchars($laporan['uraian_laporan'])); ?>
            </div>

            <!-- Lampiran File & Aksi -->
            <div class="detail-section full-width">
                <div class="section-header">
                    <h2 class="section-title">
                        <i class="fas fa-paperclip"></i>
                        Lampiran File & Aksi
                    </h2>

                </div>
                <div class="detail-content">
                    <?php if (!empty($laporan['upload_file'])): ?>
                        <div class="file-attachment">
                            <div class="file-info">
                                <div class="file-icon">
                                    <i class="fas <?php echo getFileIcon($laporan['upload_file']); ?>"></i>
                                </div>
                                <div class="file-details">
                                    <div class="file-name"><?php echo htmlspecialchars(basename($laporan['upload_file'])); ?></div>
                                    <div class="file-meta">
                                        <span class="file-size"><?php echo formatFileSize($laporan['upload_file']); ?></span>
                                        <span class="file-type"><?php echo getFileType($laporan['upload_file']); ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="file-actions">
                                <a href="index.php?controller=laporanOPD&action=download&id=<?php echo $laporan['id_laporan_opd']; ?>"
                                    class="btn btn-primary btn-sm"
                                    target="_blank">
                                    <i class="fas fa-download"></i>
                                    Download
                                </a>
                                <button type="button" class="btn btn-secondary btn-sm" onclick="previewFile()">
                                    <i class="fas fa-eye"></i>
                                    Preview
                                </button>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="no-file">
                            <i class="fas fa-info-circle"></i>
                            <span>Tidak ada lampiran file untuk laporan ini</span>
                        </div>
                    <?php endif; ?>

                </div>
                <div style="text-align: right; padding-top: 10px;">
                    <a href="index.php?controller=laporanOPD&action=index"
                        class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        Kembali
                    </a>
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
                <form id="deleteForm" method="POST" action="index.php?controller=laporanOPD&action=delete" style="display: inline;">
                    <input type="hidden" name="id" id="deleteId">
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Batal</button>
            </div>
        </div>
    </div>

    <!-- File Preview Modal -->
    <div id="previewModal" class="modal">
        <div class="modal-content modal-large">
            <div class="modal-header">
                <h3>Preview File</h3>
                <button type="button" class="close-btn" onclick="closePreviewModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div class="preview-container">
                    <iframe id="previewFrame" src="" width="100%" height="600px"></iframe>
                </div>
            </div>
            <div class="modal-footer">
                <a href="" id="downloadBtn" class="btn btn-primary" target="_blank">
                    <i class="fas fa-download"></i>
                    Download File
                </a>
                <button type="button" class="btn btn-secondary" onclick="closePreviewModal()">Tutup</button>
            </div>
        </div>
    </div>

    <?php
    // Helper functions for detail view
    function getStatusClass($status)
    {
        switch ($status) {
            case 'baru':
                return 'status-new';
            case 'diproses':
                return 'status-processing';
            case 'selesai':
                return 'status-completed';
            default:
                return 'status-new';
        }
    }

    function getStatusIcon($status)
    {
        switch ($status) {
            case 'baru':
                return 'clock';
            case 'diproses':
                return 'spinner';
            case 'selesai':
                return 'check-circle';
            default:
                return 'clock';
        }
    }

    function getStatusText($status)
    {
        switch ($status) {
            case 'baru':
                return 'Baru';
            case 'diproses':
                return 'Sedang Diproses';
            case 'selesai':
                return 'Selesai';
            default:
                return 'Menunggu Proses';
        }
    }

    function formatFileSize($filePath)
    {
        if (!file_exists($filePath)) {
            return 'File tidak ditemukan';
        }

        $bytes = filesize($filePath);
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    function getFileType($filePath)
    {
        if (!file_exists($filePath)) {
            return 'Unknown';
        }

        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        $types = [
            // Documents
            'pdf' => 'PDF Document',
            'doc' => 'Word Document',
            'docx' => 'Word Document',
            'xls' => 'Excel Spreadsheet',
            'xlsx' => 'Excel Spreadsheet',
            'ppt' => 'PowerPoint',
            'pptx' => 'PowerPoint',
            // Images
            'jpg' => 'JPEG Image',
            'jpeg' => 'JPEG Image',
            'png' => 'PNG Image',
            'gif' => 'GIF Image',
            'bmp' => 'Bitmap Image',
            'webp' => 'WebP Image',
            'svg' => 'SVG Image',
            // Videos
            'mp4' => 'MP4 Video',
            'avi' => 'AVI Video',
            'mov' => 'QuickTime Video',
            'wmv' => 'WMV Video',
            'flv' => 'Flash Video',
            'mkv' => 'MKV Video',
            'webm' => 'WebM Video',
            '3gp' => '3GP Video'
        ];

        return $types[$extension] ?? 'Unknown File';
    }

    function getFileIcon($filePath)
    {
        if (!file_exists($filePath)) {
            return 'fa-file';
        }

        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        $icons = [
            // Documents
            'pdf' => 'fa-file-pdf',
            'doc' => 'fa-file-word',
            'docx' => 'fa-file-word',
            'xls' => 'fa-file-excel',
            'xlsx' => 'fa-file-excel',
            'ppt' => 'fa-file-powerpoint',
            'pptx' => 'fa-file-powerpoint',
            // Images
            'jpg' => 'fa-file-image',
            'jpeg' => 'fa-file-image',
            'png' => 'fa-file-image',
            'gif' => 'fa-file-image',
            'bmp' => 'fa-file-image',
            'webp' => 'fa-file-image',
            'svg' => 'fa-file-image',
            // Videos
            'mp4' => 'fa-file-video',
            'avi' => 'fa-file-video',
            'mov' => 'fa-file-video',
            'wmv' => 'fa-file-video',
            'flv' => 'fa-file-video',
            'mkv' => 'fa-file-video',
            'webm' => 'fa-file-video',
            '3gp' => 'fa-file-video'
        ];

        return $icons[$extension] ?? 'fa-file';
    }
    ?>

    <style>
        /* Page Header */
        .page-header {
            background: white;
            padding: clamp(20px, 3vw, 30px);
            border-radius: clamp(15px, 2vw, 20px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: clamp(20px, 3vw, 30px);
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

        .page-actions {
            display: flex;
            gap: clamp(8px, 1.2vw, 12px);
            flex-wrap: wrap;
        }

        /* Status Card */
        .status-card {
            background: white;
            padding: clamp(20px, 3vw, 30px);
            border-radius: clamp(12px, 1.5vw, 16px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: clamp(25px, 3vw, 35px);
        }

        .status-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: clamp(15px, 2vw, 20px);
        }

        .status-badge {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 25px;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-new {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        .status-processing {
            background: #cce7ff;
            color: #004085;
            border: 1px solid #b3d7ff;
        }

        .status-completed {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .status-meta {
            display: flex;
            flex-direction: column;
            gap: 6px;
            text-align: right;
        }

        .created-info,
        .updated-info {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            color: #666;
        }

        .created-info i,
        .updated-info i {
            color: var(--primary-blue);
        }

        /* Detail Container */
        .detail-container {
            background: white;
            border-radius: clamp(12px, 1.5vw, 16px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0;
        }

        .detail-section {
            padding: clamp(25px, 3vw, 35px);
            border-bottom: 1px solid #f0f0f0;
        }

        .detail-section:last-child {
            border-bottom: none;
        }

        .detail-section.full-width {
            grid-column: 1 / -1;
        }

        .section-title {
            font-size: clamp(18px, 2.5vw, 20px);
            font-weight: 600;
            color: var(--dark-gray);
            margin: 0 0 clamp(20px, 2.5vw, 25px) 0;
            display: flex;
            align-items: center;
            gap: clamp(10px, 1.5vw, 15px);
            padding-bottom: clamp(10px, 1.5vw, 15px);
            border-bottom: 2px solid var(--primary-blue);
        }

        .section-title i {
            color: var(--primary-blue);
            font-size: clamp(20px, 2.5vw, 24px);
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: clamp(15px, 2vw, 20px);
            margin-bottom: clamp(20px, 2.5vw, 25px);
        }

        .section-header .section-title {
            margin: 0;
            border-bottom: none;
            padding-bottom: 0;
        }

        .no-file {
            display: flex;
            align-items: center;
            gap: clamp(10px, 1.5vw, 15px);
            padding: clamp(20px, 3vw, 30px);
            background: #f8f9fa;
            border-radius: clamp(8px, 1vw, 12px);
            color: #666;
            font-style: italic;
            justify-content: center;
        }

        .no-file i {
            color: var(--primary-blue);
            font-size: clamp(18px, 2.5vw, 24px);
        }

        .detail-content {
            display: flex;
            flex-direction: column;
            gap: clamp(15px, 2vw, 20px);
        }

        .detail-item {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .detail-item label {
            font-size: clamp(12px, 1.5vw, 14px);
            font-weight: 600;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .detail-value {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: clamp(14px, 1.8vw, 16px);
            color: var(--dark-gray);
            line-height: 1.4;
        }

        .detail-value i {
            color: var(--primary-blue);
            font-size: 14px;
            flex-shrink: 0;
        }

        .user-info {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .user-name {
            font-weight: 600;
            color: var(--dark-gray);
        }

        .user-role {
            font-size: 12px;
            color: #666;
        }

        .uraian-content {
            background: #f8f9fa;
            padding: clamp(15px, 2vw, 20px);
            border-radius: 8px;
            border-left: 4px solid var(--primary-blue);
            line-height: 1.6;
            font-size: clamp(14px, 1.8vw, 16px);
            color: #333;
            white-space: pre-wrap;
        }

        /* File Attachment */
        .file-attachment {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #e9ecef;
            flex-wrap: wrap;
            gap: 15px;
        }

        .file-info {
            display: flex;
            align-items: center;
            gap: 12px;
            flex: 1;
            min-width: 0;
        }

        .file-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            flex-shrink: 0;
        }

        .file-details {
            flex: 1;
            min-width: 0;
        }

        .file-name {
            font-weight: 600;
            color: var(--dark-gray);
            margin-bottom: 4px;
            word-break: break-word;
        }

        .file-meta {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .file-size,
        .file-type {
            font-size: 12px;
            color: #666;
            background: white;
            padding: 2px 8px;
            border-radius: 12px;
            border: 1px solid #e9ecef;
        }

        .file-actions {
            display: flex;
            gap: 8px;
            flex-shrink: 0;
        }

        /* Buttons */
        .btn {
            padding: clamp(8px, 1.2vw, 10px) clamp(16px, 2vw, 20px);
            border: none;
            border-radius: clamp(6px, 1vw, 8px);
            font-size: clamp(13px, 1.6vw, 15px);
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
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

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(108, 117, 125, 0.3);
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background: #c82333;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
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
            background: rgba(0, 0, 0, 0.5);
            animation: fadeIn 0.3s ease;
        }

        .modal-content {
            background: white;
            margin: 5% auto;
            padding: 0;
            border-radius: 12px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            animation: slideIn 0.3s ease;
        }

        .modal-large {
            max-width: 90%;
            max-height: 90vh;
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

        .preview-container {
            width: 100%;
            height: 600px;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
        }

        .preview-container iframe {
            border: none;
            width: 100%;
            height: 100%;
        }

        .text-danger {
            color: #dc3545;
            font-weight: 500;
        }

        /* Animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
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
                justify-content: center;
                flex-wrap: wrap;
            }

            .status-content {
                flex-direction: column;
                align-items: stretch;
                text-align: center;
            }

            .status-meta {
                text-align: center;
                align-items: center;
            }

            .detail-grid {
                grid-template-columns: 1fr;
            }

            .file-attachment {
                flex-direction: column;
                align-items: stretch;
                text-align: center;
            }

            .file-actions {
                justify-content: center;
            }

            .modal-large {
                width: 95%;
                margin: 2% auto;
            }

            .preview-container {
                height: 400px;
            }
        }

        @media (max-width: 480px) {
            .detail-section {
                padding: 20px;
            }

            .page-actions {
                flex-direction: column;
                gap: 8px;
            }

            .btn {
                justify-content: center;
                width: 100%;
            }

            .status-badge {
                font-size: 12px;
                padding: 6px 12px;
            }

            .preview-container {
                height: 300px;
            }
        }
    </style>

    <script>
        // Delete confirmation
        function confirmDelete(id) {
            document.getElementById('deleteId').value = id;
            document.getElementById('deleteModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('deleteModal').style.display = 'none';
        }

        // File preview
        function previewFile() {
            const filePath = '<?php echo $laporan['upload_file'] ?? ''; ?>';
            const fileName = '<?php echo basename($laporan['upload_file'] ?? ''); ?>';

            if (!filePath || !fileName) {
                showNotification('File tidak tersedia untuk preview.', 'error');
                return;
            }

            const previewModal = document.getElementById('previewModal');
            const previewFrame = document.getElementById('previewFrame');
            const downloadBtn = document.getElementById('downloadBtn');

            // Set iframe source
            previewFrame.src = filePath;
            downloadBtn.href = 'index.php?controller=laporanOPD&action=download&id=<?php echo $laporan['id_laporan_opd']; ?>';

            // Show modal
            previewModal.style.display = 'block';
        }

        function closePreviewModal() {
            document.getElementById('previewModal').style.display = 'none';
            // Clear iframe source to stop loading
            document.getElementById('previewFrame').src = '';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const deleteModal = document.getElementById('deleteModal');
            const previewModal = document.getElementById('previewModal');

            if (event.target === deleteModal) {
                closeModal();
            }

            if (event.target === previewModal) {
                closePreviewModal();
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
            // Remove existing notifications
            const existingNotifications = document.querySelectorAll('.notification');
            existingNotifications.forEach(n => n.remove());

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

        // Print functionality
        function printLaporan() {
            window.print();
        }
    </script>

    <style>
        /* Notification Styles */
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background: white;
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
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

        /* Print Styles */
        @media print {

            .page-header,
            .page-actions,
            .modal,
            .notification {
                display: none !important;
            }

            .main-content {
                padding: 0;
                background: white;
            }

            .detail-container {
                box-shadow: none;
                border: 1px solid #ddd;
            }

            .btn {
                display: none;
            }
        }

        /* Action Section Styles */
        .action-section {
            background: white;
            border-top: 1px solid #e9ecef;
            padding: clamp(20px, 3vw, 30px) 0;
            margin-top: clamp(20px, 3vw, 30px);
            flex-shrink: 0;
        }

        .action-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 clamp(20px, 3vw, 30px);
        }

        .action-buttons {
            display: flex;
            flex-direction: column;
            gap: clamp(15px, 2vw, 20px);
        }

        .action-group {
            display: flex;
            gap: clamp(12px, 2vw, 15px);
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
        }

        .action-group.primary {
            justify-content: center;
        }

        .action-group.secondary {
            justify-content: center;
        }

        /* Responsive Action Buttons */
        @media (max-width: 768px) {
            .action-section {
                padding: clamp(15px, 3vw, 25px) 0;
                margin-top: clamp(15px, 3vw, 25px);
            }

            .action-container {
                padding: 0 clamp(15px, 3vw, 25px);
            }

            .action-buttons {
                gap: clamp(12px, 2vw, 18px);
            }

            .action-group {
                flex-direction: column;
                width: 100%;
                gap: clamp(10px, 2vw, 15px);
            }

            .action-group .btn {
                width: 100%;
                justify-content: center;
                max-width: 300px;
                margin: 0 auto;
            }
        }

        @media (max-width: 480px) {
            .action-section {
                padding: clamp(12px, 2.5vw, 20px) 0;
                margin-top: clamp(12px, 2.5vw, 20px);
            }

            .action-container {
                padding: 0 clamp(12px, 2.5vw, 20px);
            }

            .action-buttons {
                gap: clamp(10px, 2vw, 15px);
            }

            .action-group {
                gap: clamp(8px, 1.5vw, 12px);
            }
        }

        @media print {
            .action-section {
                display: none;
            }
        }
    </style>

</div>
</div>

<?php include 'views/template/footer.php'; ?>