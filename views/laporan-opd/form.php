<?php
$title = isset($laporan) ? 'Edit Laporan OPD - ' : 'Buat Laporan OPD Baru - ';
include 'views/layouts/simple-header.php';
?>
<?php include 'views/layouts/simple-navbar.php'; ?>

<div class="fullscreen-container">
    <div class="fullscreen-content">
    
    <div class="page-header">
        <div class="header-content">
            <h1 class="page-title">
                <i class="fas fa-<?php echo isset($laporan) ? 'edit' : 'plus-circle'; ?>"></i>
                <?php echo isset($laporan) ? 'Edit Laporan OPD' : 'Buat Laporan OPD Baru'; ?>
            </h1>
            <div class="page-actions">
                <a href="<?= route('laporanOPD', 'index') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Kembali ke Daftar
                </a>
            </div>
        </div>
    </div>

    
    <div class="form-container">
        <form id="laporanForm" method="POST" enctype="multipart/form-data"
              action="<?php echo isset($laporan) ? route('laporanOPD', 'update') : route('laporanOPD', 'store'); ?>"
              novalidate>

            <?php if (isset($laporan)): ?>
                <input type="hidden" name="id" value="<?php echo $laporan['id_laporan_opd']; ?>">
            <?php endif; ?>

            
            <div class="form-section">
                <h2 class="section-title">
                    <i class="fas fa-building"></i>
                    Informasi OPD
                </h2>
                <div class="form-row">
                    <div class="form-group">
                        <label for="nama_opd" class="form-label">
                            <i class="fas fa-hospital"></i>
                            Nama OPD/Instansi <span class="required">*</span>
                        </label>
                        <select id="nama_opd" name="nama_opd" class="form-control" required>
                            <option value="">Pilih Nama OPD</option>
                            <?php if (isset($opd_list)): ?>
                                <?php foreach ($opd_list as $opd): ?>
                                    <option value="<?php echo htmlspecialchars($opd['nama_opd']); ?>"
                                            <?php
                                            $selected_nama_opd = null;
                                            if (isset($old_input['nama_opd'])) {
                                                $selected_nama_opd = $old_input['nama_opd'];
                                            } else if (isset($laporan)) {
                                                $selected_nama_opd = $laporan['nama_opd'];
                                            }
                                            echo $selected_nama_opd == $opd['nama_opd'] ? 'selected' : '';
                                            ?>>
                                        <?php echo htmlspecialchars($opd['nama_opd']); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <small class="form-text">Pilih nama OPD atau instansi dari daftar.</small>
                    </div>
                </div>
            </div>

            
            <div class="form-section">
                <h2 class="section-title">
                    <i class="fas fa-tasks"></i>
                    Informasi Kegiatan
                </h2>
                <div class="form-row">
                    <div class="form-group">
                        <label for="nama_kegiatan" class="form-label">
                            <i class="fas fa-clipboard-list"></i>
                            Nama Kegiatan <span class="required">*</span>
                        </label>
                        <input type="text"
                               id="nama_kegiatan"
                               name="nama_kegiatan"
                               class="form-control"
                               placeholder="Masukkan nama kegiatan yang dilaporkan"
                               value="<?php echo isset($old_input['nama_kegiatan']) ? htmlspecialchars($old_input['nama_kegiatan']) : (isset($laporan) ? htmlspecialchars($laporan['nama_kegiatan']) : ''); ?>"
                               required>
                        <small class="form-text">Jelaskan nama kegiatan secara singkat dan jelas.</small>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group full-width">
                        <label for="uraian_laporan" class="form-label">
                            <i class="fas fa-file-alt"></i>
                            Uraian Laporan <span class="required">*</span>
                        </label>
                        <textarea id="uraian_laporan"
                                  name="uraian_laporan"
                                  class="form-control"
                                  rows="8"
                                  placeholder="Jelaskan secara detail mengenai kegiatan yang dilaporkan..."
                                  required><?php echo isset($old_input['uraian_laporan']) ? htmlspecialchars($old_input['uraian_laporan']) : (isset($laporan) ? htmlspecialchars($laporan['uraian_laporan']) : ''); ?></textarea>
                        <div class="char-counter">
                            <span id="charCount">0</span> karakter (minimal 10 karakter)
                        </div>
                        <small class="form-text">Deskripsikan kegiatan secara rinci: apa, kapan, di mana, siapa yang terlibat, dan hasil yang dicapai.</small>
                    </div>
                </div>
            </div>

            
            <div class="form-section">
                <h2 class="section-title">
                    <i class="fas fa-paper-plane"></i>
                    Tujuan Laporan
                </h2>
                <div class="form-row">
                    <div class="form-group">
                        <label for="tujuan" class="form-label">
                            <i class="fas fa-bullseye"></i>
                            Tujuan Pengiriman
                        </label>
                        <select id="tujuan" name="tujuan" class="form-control" required>
                            <option value="dinas kominfo"
                                    <?php
                                    $selected_tujuan = null;
                                    if (isset($old_input['tujuan'])) {
                                        $selected_tujuan = $old_input['tujuan'];
                                    } else if (isset($laporan)) {
                                        $selected_tujuan = $laporan['tujuan'];
                                    }
                                    echo $selected_tujuan == 'dinas kominfo' ? 'selected' : '';
                                    ?>>
                                Dinas Komunikasi dan Informasi
                            </option>
                        </select>
                        <small class="form-text">Laporan ini akan dikirim ke Dinas Komunikasi dan Informasi.</small>
                    </div>
                </div>
            </div>

            
            <div class="form-section">
                <h2 class="section-title">
                    <i class="fas fa-paperclip"></i>
                    Lampiran File
                </h2>
                <div class="form-row">
                    <div class="form-group">
                        <label for="upload_file" class="form-label">
                            <i class="fas fa-upload"></i>
                            Upload File (Opsional)
                        </label>
                        <input type="file"
                               id="upload_file"
                               name="upload_file"
                               class="form-control-file"
                               accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.gif,.bmp,.webp,.svg,.mp4,.avi,.mov,.wmv,.flv,.mkv,.webm,.3gp">

                        <div class="file-info">
                            <div class="allowed-types">
                                <strong>Tipe file yang diperbolehkan:</strong>
                                <span>Dokumen (PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX), Gambar (JPG, JPEG, PNG, GIF, BMP, WEBP, SVG), Video (MP4, AVI, MOV, WMV, FLV, MKV, WEBM, 3GP)</span>
                            </div>
                            <div class="max-size">
                                <strong>Ukuran maksimal:</strong> 50MB
                            </div>
                        </div>

                        <?php if (isset($laporan) && !empty($laporan['upload_file'])): ?>
                            <div class="current-file">
                                <strong>File saat ini:</strong>
                                <a href="<?= route('laporanOPD', 'download') ?>?id=<?php echo $laporan['id_laporan_opd']; ?>"
                                   target="_blank"
                                   class="file-link">
                                    <i class="fas fa-download"></i>
                                    <?php echo htmlspecialchars(basename($laporan['upload_file'])); ?>
                                </a>
                                <small class="form-text">Upload file baru untuk mengganti file yang ada.</small>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <i class="fas fa-save"></i>
                    <span><?php echo isset($laporan) ? 'Update Laporan' : 'Simpan Laporan'; ?></span>
                </button>

                <a href="<?= route('laporanOPD', 'index') ?>" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    Batal
                </a>

                <?php if (isset($laporan)): ?>
                    <button type="button" class="btn btn-danger" onclick="confirmDelete(<?php echo $laporan['id_laporan_opd']; ?>)">
                        <i class="fas fa-trash"></i>
                        Hapus Laporan
                    </button>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

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
            <form id="deleteForm" method="POST" action="<?= route('laporanOPD', 'delete') ?>" style="display: inline;">
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

/* Form Container */
.form-container {
    background: white;
    border-radius: clamp(15px, 2vw, 20px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    overflow: hidden;
}

/* Form Sections */
.form-section {
    padding: clamp(25px, 3vw, 35px);
    border-bottom: 1px solid #f0f0f0;
}

.form-section:last-child {
    border-bottom: none;
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

/* Form Rows */
.form-row {
    display: flex;
    gap: clamp(20px, 2.5vw, 30px);
    margin-bottom: 0;
}

.form-row:last-child {
    margin-bottom: 0;
}

.form-group {
    flex: 1;
    min-width: 0;
}

.form-group.full-width {
    width: 100%;
}

/* Form Labels */
.form-label {
    display: block;
    font-size: clamp(14px, 1.8vw, 16px);
    font-weight: 600;
    color: var(--dark-gray);
    margin-bottom: clamp(6px, 1vw, 8px);
    align-items: center;
    gap: 6px;
}

.form-label i {
    color: var(--primary-blue);
    font-size: 14px;
}

.required {
    color: #dc3545;
    margin-left: 2px;
}

/* Form Controls */
.form-control {
    width: 100%;
    padding: clamp(10px, 1.5vw, 12px) clamp(15px, 2vw, 18px);
    border: 1px solid #ddd;
    border-radius: clamp(6px, 1vw, 8px);
    font-size: clamp(14px, 1.8vw, 16px);
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
    background: white;
}

.form-control:focus {
    outline: none;
    border-color: var(--primary-blue);
    box-shadow: 0 0 0 3px rgba(47, 88, 205, 0.1);
}

.form-control::placeholder {
    color: #999;
}

textarea.form-control {
    resize: vertical;
    min-height: 120px;
    line-height: 1.5;
}

select.form-control {
    cursor: pointer;
}

/* File Input */
.form-control-file {
    width: 100%;
    padding: clamp(8px, 1.2vw, 10px);
    border: 2px dashed #ddd;
    border-radius: clamp(6px, 1vw, 8px);
    background: #f9f9f9;
    font-size: clamp(14px, 1.8vw, 16px);
    cursor: pointer;
    transition: border-color 0.3s ease, background 0.3s ease;
}

.form-control-file:hover {
    border-color: var(--primary-blue);
    background: #f0f8ff;
}

.file-info {
    margin-top: clamp(10px, 1.5vw, 15px);
    font-size: clamp(12px, 1.5vw, 14px);
    color: #666;
}

/* Style for select dropdown */
select.form-control {
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' stroke='%23333' viewBox='0 0 20 20'%3e%3cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 1rem center;
    background-size: 1em;
    padding-right: 2.5rem;
}

/* Ensure dropdown container has enough space */
.form-group {
    position: relative;
    overflow: visible; /* Allow dropdown to extend beyond container */
}

/* Custom Select Styles */
.custom-select {
    position: relative;
    margin-bottom: 1rem;
}

.custom-select-input {
    position: relative;
    display: block;
    width: 100%;
    padding: 10px 40px 10px 15px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 14px;
    cursor: pointer;
    background: white;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

.custom-select-input:focus {
    outline: none;
    border-color: var(--primary-blue);
    box-shadow: 0 0 0 3px rgba(47, 88, 205, 0.1);
}

.custom-select-arrow {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    pointer-events: none;
    color: #666;
    font-size: 12px;
    user-select: none;
}

.custom-select-options {
    position: absolute;
    top: 100%;
    left: 0;
    width: 100%;
    background: white;
    border: 1px solid #ddd;
    border-top: none;
    border-radius: 0 0 6px 6px;
    max-height: 200px;
    overflow-y: auto;
    z-index: 1000;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    /* Always position below the input */
    transform: none;
}

.custom-select-option {
    padding: 10px 15px;
    cursor: pointer;
    transition: background-color 0.2s ease;
}

.custom-select-option:hover {
    background-color: #f0f0f0;
}

.custom-select-option.selected {
    background-color: #e3f2fd;
}

/* Error state for custom select */
.custom-select-input.error {
    border-color: #dc3545;
    box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1);
}

.allowed-types, .max-size {
    margin-bottom: 6px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.allowed-types span, .max-size span {
    color: var(--primary-blue);
    font-weight: 500;
}

.current-file {
    margin-top: clamp(10px, 1.5vw, 15px);
    padding: clamp(10px, 1.5vw, 15px);
    background: #e8f5e8;
    border-radius: clamp(6px, 1vw, 8px);
    border-left: 3px solid #28a745;
}

.file-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: #28a745;
    text-decoration: none;
    font-weight: 500;
    margin-top: 4px;
    transition: color 0.3s ease;
}

.file-link:hover {
    color: #1e7e34;
    text-decoration: underline;
}

/* Character Counter */
.char-counter {
    text-align: right;
    font-size: clamp(11px, 1.4vw, 12px);
    color: #666;
    margin-top: 4px;
}

/* Form Text */
.form-text {
    font-size: clamp(12px, 1.5vw, 14px);
    color: #666;
    margin-top: 4px;
    display: block;
}

/* Form Actions */
.form-actions {
    padding: clamp(25px, 3vw, 35px);
    background: #f8f9fa;
    display: flex;
    gap: clamp(10px, 1.5vw, 15px);
    align-items: center;
    flex-wrap: wrap;
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

/* Error/Success Messages */
.error-message {
    background: #f8d7da;
    border: 1px solid #f5c6cb;
    color: #721c24;
    padding: 12px 16px;
    border-radius: 6px;
    margin-bottom: 15px;
    font-size: 14px;
}

.error-message ul {
    margin: 0;
    padding-left: 20px;
}

.error-message li {
    margin-bottom: 4px;
}

.error-message li:last-child {
    margin-bottom: 0;
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

    .form-row {
        flex-direction: column;
        gap: 0;
    }

    .form-actions {
        flex-direction: column;
        align-items: stretch;
    }

    .btn {
        justify-content: center;
    }

    .section-title {
        font-size: clamp(16px, 2.2vw, 18px);
    }

    textarea.form-control {
        min-height: 100px;
    }
}

@media (max-width: 480px) {
    .form-section {
        padding: 20px;
    }

    .form-actions {
        padding: 20px;
    }

    .section-title {
        font-size: 16px;
    }

    .form-control {
        padding: 10px 14px;
        font-size: 14px;
    }

    textarea.form-control {
        min-height: 80px;
    }
}
</style>

<?php if (isset($_SESSION['errors'])): ?>
    <div class="error-message">
        <strong>Kesalahan:</strong>
        <ul>
            <?php foreach ($_SESSION['errors'] as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php unset($_SESSION['errors']); ?>
<?php endif; ?>

<script></script>

<style>
/* Notification Styles */
.notification {
    position: fixed;
    top: 20px;
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

    </div>
    </div>
</div>

<?php include 'views/layouts/simple-footer.php'; ?>