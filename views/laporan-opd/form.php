<?php
$title = isset($laporan) ? 'Edit Laporan OPD - ' : 'Buat Laporan OPD Baru - ';
include 'views/template/header.php';
?>
<?php include 'views/template/navbar.php'; ?>

<div class="fullscreen-container">
    <div class="fullscreen-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="header-content">
            <h1 class="page-title">
                <i class="fas fa-<?php echo isset($laporan) ? 'edit' : 'plus-circle'; ?>"></i>
                <?php echo isset($laporan) ? 'Edit Laporan OPD' : 'Buat Laporan OPD Baru'; ?>
            </h1>
            <div class="page-actions">
                <a href="index.php?controller=laporanOPD&action=index" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Kembali ke Daftar
                </a>
            </div>
        </div>
    </div>

    <!-- Form Container -->
    <div class="form-container">
        <form id="laporanForm" method="POST" enctype="multipart/form-data"
              action="index.php?controller=laporanOPD&action=<?php echo isset($laporan) ? 'update' : 'store'; ?>"
              novalidate>

            <?php if (isset($laporan)): ?>
                <input type="hidden" name="id" value="<?php echo $laporan['id_laporan_opd']; ?>">
            <?php endif; ?>

            <!-- Informasi OPD Section -->
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

            <!-- Kegiatan Section -->
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

            <!-- Tujuan Section -->
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

            <!-- Lampiran Section -->
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
                                <a href="index.php?controller=laporanOPD&action=download&id=<?php echo $laporan['id_laporan_opd']; ?>"
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

            <!-- Form Actions -->
            <div class="form-actions">
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <i class="fas fa-save"></i>
                    <span><?php echo isset($laporan) ? 'Update Laporan' : 'Simpan Laporan'; ?></span>
                </button>

                <a href="index.php?controller=laporanOPD&action=index" class="btn btn-secondary">
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

<!-- Error/Success Messages -->
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

<script>
// Character counter
document.addEventListener('DOMContentLoaded', function() {
    const textarea = document.getElementById('uraian_laporan');
    const counter = document.getElementById('charCount');

    if (textarea && counter) {
        // Set initial count
        counter.textContent = textarea.value.length;

        // Update count on input
        textarea.addEventListener('input', function() {
            counter.textContent = this.value.length;

            // Update counter color based on length
            if (this.value.length < 10) {
                counter.style.color = '#dc3545';
            } else if (this.value.length > 500) {
                counter.style.color = '#ffc107';
            } else {
                counter.style.color = '#666';
            }
        });
    }

    // Create custom dropdown for nama_opd
    const originalSelect = document.getElementById('nama_opd');
    if (originalSelect) {
        // Create custom dropdown structure
        const originalParent = originalSelect.parentNode;
        const customSelect = document.createElement('div');
        customSelect.className = 'custom-select';
        customSelect.innerHTML = `
            <input type="text" id="custom_nama_opd_input" class="form-control" placeholder="Pilih Nama OPD" readonly>
            <div class="custom-select-arrow">▼</div>
            <div class="custom-select-options" style="display: none;">
                <div class="custom-select-option" data-value="">Pilih Nama OPD</div>
                ${Array.from(originalSelect.options).slice(1).map(option => 
                    `<div class="custom-select-option" data-value="${option.value}">${option.text}</div>`
                ).join('')}
            </div>
            <input type="hidden" id="nama_opd" name="nama_opd" value="${originalSelect.value}">
        `;
        
        // Replace original select with custom dropdown
        originalSelect.parentNode.replaceChild(customSelect, originalSelect);
        
        // Add event listeners for custom dropdown
        const customInput = customSelect.querySelector('#custom_nama_opd_input');
        const optionsContainer = customSelect.querySelector('.custom-select-options');
        const hiddenInput = customSelect.querySelector('#nama_opd');
        const options = customSelect.querySelectorAll('.custom-select-option');
        
        // Set initial value
        if (hiddenInput.value) {
            const selectedOption = Array.from(options).find(opt => opt.dataset.value === hiddenInput.value);
            if (selectedOption) {
                customInput.value = selectedOption.textContent;
            }
        }
        
        // Toggle options
        customInput.addEventListener('click', function() {
            optionsContainer.style.display = optionsContainer.style.display === 'block' ? 'none' : 'block';
        });
        
        customSelect.querySelector('.custom-select-arrow').addEventListener('click', function() {
            optionsContainer.style.display = optionsContainer.style.display === 'block' ? 'none' : 'block';
        });
        
        // Select option
        options.forEach(option => {
            option.addEventListener('click', function() {
                const value = this.dataset.value;
                hiddenInput.value = value;
                customInput.value = this.textContent;
                optionsContainer.style.display = 'none';
                
                // Trigger change event for validation
                hiddenInput.dispatchEvent(new Event('change'));
            });
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!customSelect.contains(e.target)) {
                optionsContainer.style.display = 'none';
            }
        });
    }

    // Form validation
    const form = document.getElementById('laporanForm');
    const submitBtn = document.getElementById('submitBtn');

    if (form && submitBtn) {
        form.addEventListener('submit', function(e) {
            let isValid = true;
            const errors = [];

            // Validate required fields - using hidden input for validation
            const hiddenNamaOPD = document.getElementById('nama_opd');
            const namaKegiatan = document.getElementById('nama_kegiatan');
            const uraianLaporan = document.getElementById('uraian_laporan');

            if (!hiddenNamaOPD.value.trim()) {
                errors.push('Nama OPD harus dipilih');
                // Add error class to custom input
                const customInput = document.querySelector('#custom_nama_opd_input');
                if (customInput) customInput.classList.add('error');
                isValid = false;
            } else {
                // Remove error class from custom input
                const customInput = document.querySelector('#custom_nama_opd_input');
                if (customInput) customInput.classList.remove('error');
            }

            if (!namaKegiatan.value.trim()) {
                errors.push('Nama kegiatan harus diisi');
                namaKegiatan.classList.add('error');
                isValid = false;
            } else {
                namaKegiatan.classList.remove('error');
            }

            if (!uraianLaporan.value.trim()) {
                errors.push('Uraian laporan harus diisi');
                uraianLaporan.classList.add('error');
                isValid = false;
            } else if (uraianLaporan.value.trim().length < 10) {
                errors.push('Uraian laporan minimal 10 karakter');
                uraianLaporan.classList.add('error');
                isValid = false;
            } else {
                uraianLaporan.classList.remove('error');
            }

            // Show errors if any
            if (!isValid) {
                e.preventDefault();
                showErrors(errors);
            }
        });
    }

    // File input preview
    const fileInput = document.getElementById('upload_file');
    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Check file size (50MB limit)
                if (file.size > 50 * 1024 * 1024) {
                    e.target.value = '';
                    showNotification('Ukuran file terlalu besar. Maksimal 50MB.', 'error');
                    return;
                }

                // Check file type
                const allowedTypes = [
                    // Documents
                    'application/pdf', 'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                    // Images
                    'image/jpeg', 'image/png', 'image/gif', 'image/bmp', 'image/webp', 'image/svg+xml',
                    // Videos
                    'video/mp4', 'video/avi', 'video/quicktime', 'video/x-ms-wmv',
                    'video/x-flv', 'video/x-matroska', 'video/webm', 'video/3gpp'
                ];
                const allowedExtensions = /\.(pdf|doc|docx|xls|xlsx|ppt|pptx|jpg|jpeg|png|gif|bmp|webp|svg|mp4|avi|mov|wmv|flv|mkv|webm|3gp)$/i;
                if (!allowedTypes.includes(file.type) && !file.name.match(allowedExtensions)) {
                    e.target.value = '';
                    showNotification('Tipe file tidak didukung. Hanya dokumen, gambar, dan video yang diperbolehkan.', 'error');
                    return;
                }

                showNotification(`File "${file.name}" siap diupload.`, 'success');
            }
        });
    }
});

// Show errors function
function showErrors(errors) {
    const existingError = document.querySelector('.error-message');
    if (existingError) {
        existingError.remove();
    }

    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message';
    errorDiv.innerHTML = `
        <strong>Kesalahan:</strong>
        <ul>
            ${errors.map(error => `<li>${error}</li>`).join('')}
        </ul>
    `;

    const form = document.getElementById('laporanForm');
    form.insertBefore(errorDiv, form.firstChild);

    // Scroll to top to see errors
    window.scrollTo({ top: 0, behavior: 'smooth' });

    // Auto remove after 5 seconds
    setTimeout(() => {
        if (errorDiv.parentElement) {
            errorDiv.remove();
        }
    }, 5000);
}

// Delete confirmation
function confirmDelete(id) {
    document.getElementById('deleteId').value = id;
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

// Add error style
const style = document.createElement('style');
style.textContent = `
    .form-control.error {
        border-color: #dc3545;
        box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1);
    }
`;
document.head.appendChild(style);
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

<?php include 'views/template/footer.php'; ?>