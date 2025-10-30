<?php
$title = isset($laporan) ? 'Edit Laporan Camat - LaporBup' : 'Buat Laporan Camat Baru - LaporBup';
include 'views/template/header.php';
?>
<?php include 'views/template/navbar.php'; ?>

<script>
// Load desa berdasarkan kecamatan yang dipilih
function loadDesa() {
    const kecamatanId = document.getElementById('id_kecamatan').value;
    const desaSelect = document.getElementById('id_desa');

    // Update hidden field nama_kecamatan based on selected kecamatan
    if(kecamatanId) {
        const kecamatanText = document.querySelector(`#id_kecamatan option[value="${kecamatanId}"]`).text;
        document.getElementById('hidden_nama_kecamatan').value = kecamatanText;
    } else {
        document.getElementById('hidden_nama_kecamatan').value = '';
    }

    if (!kecamatanId) {
        desaSelect.innerHTML = '<option value="">Pilih Desa</option>';
        document.getElementById('hidden_nama_desa').value = '';
        return;
    }

    // Kosongkan dropdown desa sebelumnya
    desaSelect.innerHTML = '<option value="">Memuat...</option>';

    // Gunakan fetch untuk mendapatkan daftar desa
    fetch(`index.php?controller=desa&action=getDesaByKecamatan&id_kecamatan=${kecamatanId}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(response => response.json())
        .then(data => {
            desaSelect.innerHTML = '<option value="">Pilih Desa</option>';

            if (data.success && data.data && data.data.length > 0) {
                data.data.forEach(desa => {
                    const option = document.createElement('option');
                    option.value = desa.id_desa;
                    option.textContent = desa.nama_desa;

                    // Jika sebelumnya sudah dipilih desa, pilih kembali
                    <?php if (isset($laporan) && isset($selected_desa_id)): ?>
                        if (desa.id_desa == <?php echo $selected_desa_id; ?>) {
                            option.selected = true;
                        }
                    <?php endif; ?>

                    desaSelect.appendChild(option);
                });
            } else {
                desaSelect.innerHTML = '<option value="">Tidak ada desa ditemukan</option>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            desaSelect.innerHTML = '<option value="">Error memuat desa</option>';
        });
}

// Update hidden field nama_desa when desa is selected
document.addEventListener('change', function(e) {
    if(e.target.id === 'id_desa') {
        const desaId = e.target.value;
        if(desaId) {
            const desaText = document.querySelector(`#id_desa option[value="${desaId}"]`).text;
            document.getElementById('hidden_nama_desa').value = desaText;
        } else {
            document.getElementById('hidden_nama_desa').value = '';
        }
    }
});
</script>

<div class="fullscreen-container">
    <div class="fullscreen-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="header-content">
            <h1 class="page-title">
                <i class="fas fa-<?php echo isset($laporan) ? 'edit' : 'plus-circle'; ?>"></i>
                <?php echo isset($laporan) ? 'Edit Laporan Camat' : 'Buat Laporan Camat Baru'; ?>
            </h1>
            <div class="page-actions">
                <a href="index.php?controller=laporanCamat&action=index" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Kembali ke Daftar
                </a>
            </div>
        </div>
    </div>

    <!-- Form Container -->
    <div class="form-container">
        <form id="laporanForm" method="POST" enctype="multipart/form-data"
              action="index.php?controller=laporanCamat&action=<?php echo isset($laporan) ? 'update' : 'store'; ?>"
              novalidate>

            <?php if (isset($laporan)): ?>
                <input type="hidden" name="id" value="<?php echo $laporan['id_laporan_camat']; ?>">
            <?php endif; ?>
            
            <!-- Hidden inputs to pass the names after selection -->
            <input type="hidden" name="nama_kecamatan" id="hidden_nama_kecamatan" value="<?php echo isset($old_input['nama_kecamatan']) ? htmlspecialchars($old_input['nama_kecamatan']) : (isset($laporan) ? htmlspecialchars($laporan['nama_kecamatan']) : ''); ?>">
            <input type="hidden" name="nama_desa" id="hidden_nama_desa" value="<?php echo isset($old_input['nama_desa']) ? htmlspecialchars($old_input['nama_desa']) : (isset($laporan) ? htmlspecialchars($laporan['nama_desa']) : ''); ?>">

            <!-- Informasi Pelapor Section -->
            <div class="form-section">
                <h2 class="section-title">
                    <i class="fas fa-user"></i>
                    Informasi Pelapor
                </h2>
                <div class="form-row">
                    <div class="form-group">
                        <label for="nama_pelapor" class="form-label">
                            <i class="fas fa-signature"></i>
                            Nama Pelapor <span class="required">*</span>
                        </label>
                        <input type="text"
                               id="nama_pelapor"
                               name="nama_pelapor"
                               class="form-control"
                               placeholder="Masukkan nama pelapor"
                               value="<?php echo isset($old_input['nama_pelapor']) ? htmlspecialchars($old_input['nama_pelapor']) : (isset($laporan) ? htmlspecialchars($laporan['nama_pelapor']) : htmlspecialchars($_SESSION['username'] ?? '')); ?>"
                               required>
                    </div>
                </div>
            </div>

            <!-- Wilayah Section -->
            <div class="form-section">
                <h2 class="section-title">
                    <i class="fas fa-map-marker-alt"></i>
                    Informasi Wilayah
                </h2>
                <div class="form-row">
                    <div class="form-group">
                        <label for="id_kecamatan" class="form-label">
                            <i class="fas fa-city"></i>
                            Nama Kecamatan <span class="required">*</span>
                        </label>
                        <select id="id_kecamatan" name="id_kecamatan" class="form-control" required onchange="loadDesa()">
                            <option value="">Pilih Kecamatan</option>
                            <?php foreach ($kecamatan_list as $kec): ?>
                                <option value="<?php echo $kec['id_kecamatan']; ?>" 
                                        <?php 
                                        $selected_kec_id = null;
                                        if (isset($old_input['id_kecamatan'])) {
                                            $selected_kec_id = $old_input['id_kecamatan'];
                                        } else if (isset($laporan) && isset($selected_kecamatan_id)) {
                                            $selected_kec_id = $selected_kecamatan_id;
                                        } else if (isset($laporan)) {
                                            // Jika tidak ada selected_kecamatan_id, cari berdasarkan nama
                                            foreach($kecamatan_list as $kec_check) {
                                                if($kec_check['nama_kecamatan'] === $laporan['nama_kecamatan']) {
                                                    $selected_kec_id = $kec_check['id_kecamatan'];
                                                    break;
                                                }
                                            }
                                        }
                                        echo $selected_kec_id == $kec['id_kecamatan'] ? 'selected' : ''; 
                                        ?>>
                                    <?php echo htmlspecialchars($kec['nama_kecamatan']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="id_desa" class="form-label">
                            <i class="fas fa-home"></i>
                            Nama Desa <span class="required">*</span>
                        </label>
                        <select id="id_desa" name="id_desa" class="form-control" required>
                            <option value="">Pilih Desa</option>
                            <?php if (isset($desa_list)): ?>
                                <?php foreach ($desa_list as $desa): ?>
                                    <option value="<?php echo $desa['id_desa']; ?>" 
                                            <?php 
                                            $selected_desa_id_option = null;
                                            if (isset($old_input['id_desa'])) {
                                                $selected_desa_id_option = $old_input['id_desa'];
                                            } else if (isset($laporan) && isset($selected_desa_id)) {
                                                $selected_desa_id_option = $selected_desa_id;
                                            } else if (isset($laporan)) {
                                                // Jika tidak ada selected_desa_id, cari berdasarkan nama dalam desa_list
                                                foreach($desa_list as $desa_check) {
                                                    if($desa_check['nama_desa'] === $laporan['nama_desa']) {
                                                        $selected_desa_id_option = $desa_check['id_desa'];
                                                        break;
                                                    }
                                                }
                                            }
                                            echo $selected_desa_id_option == $desa['id_desa'] ? 'selected' : ''; 
                                            ?>>
                                        <?php echo htmlspecialchars($desa['nama_desa']); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Kejadian Section -->
            <div class="form-section">
                <h2 class="section-title">
                    <i class="fas fa-calendar-alt"></i>
                    Informasi Kejadian
                </h2>
                <div class="form-row">
                    <div class="form-group">
                        <label for="waktu_kejadian" class="form-label">
                            <i class="fas fa-clock"></i>
                            Waktu Kejadian <span class="required">*</span>
                        </label>
                        <input type="datetime-local"
                               id="waktu_kejadian"
                               name="waktu_kejadian"
                               class="form-control"
                               value="<?php echo isset($old_input['waktu_kejadian']) ? htmlspecialchars($old_input['waktu_kejadian']) : (isset($laporan) ? date('Y-m-d\\TH:i', strtotime($laporan['waktu_kejadian'])) : ''); ?>"
                               required>
                        <small class="form-text">Pilih tanggal dan waktu terjadinya peristiwa.</small>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="tujuan" class="form-label">
                            <i class="fas fa-bullseye"></i>
                            Tujuan Laporan <span class="required">*</span>
                        </label>
                        <select id="tujuan" name="tujuan" class="form-control" required>
                            <option value="">Pilih Tujuan</option>
                            <option value="bupati" <?php echo (isset($old_input['tujuan']) ? $old_input['tujuan'] : (isset($laporan) ? $laporan['tujuan'] : '')) === 'bupati' ? 'selected' : ''; ?>>Bupati</option>
                            <option value="wakil bupati" <?php echo (isset($old_input['tujuan']) ? $old_input['tujuan'] : (isset($laporan) ? $laporan['tujuan'] : '')) === 'wakil bupati' ? 'selected' : ''; ?>>Wakil Bupati</option>
                            <option value="sekda" <?php echo (isset($old_input['tujuan']) ? $old_input['tujuan'] : (isset($laporan) ? $laporan['tujuan'] : '')) === 'sekda' ? 'selected' : ''; ?>>Sekda</option>
                            <option value="opd" <?php echo (isset($old_input['tujuan']) ? $old_input['tujuan'] : (isset($laporan) ? $laporan['tujuan'] : '')) === 'opd' ? 'selected' : ''; ?>>OPD</option>
                        </select>
                        <small class="form-text">Pilih tujuan instansi yang akan menerima laporan ini.</small>
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
                                  placeholder="Jelaskan secara detail mengenai kejadian yang dilaporkan..."
                                  required><?php echo isset($old_input['uraian_laporan']) ? htmlspecialchars($old_input['uraian_laporan']) : (isset($laporan) ? htmlspecialchars($laporan['uraian_laporan']) : ''); ?></textarea>
                        <div class="char-counter">
                            <span id="charCount">0</span> karakter (minimal 10 karakter)
                        </div>
                        <small class="form-text">Deskripsikan kejadian secara rinci: apa, kapan, di mana, siapa yang terlibat, dan dampak yang ditimbulkan.</small>
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
                                <a href="index.php?controller=laporanCamat&action=download&id=<?php echo $laporan['id_laporan_camat']; ?>"
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

                <a href="index.php?controller=laporanCamat&action=index" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    Batal
                </a>

                <?php if (isset($laporan)): ?>
                    <button type="button" class="btn btn-danger" onclick="confirmDelete(<?php echo $laporan['id_laporan_camat']; ?>)">
                        <i class="fas fa-trash"></i>
                        Hapus Laporan
                    </button>
                <?php endif; ?>
            </div>
      </form>
    </div>
</div>

    </div>
    </div>
</div>

<?php include 'views/template/footer.php'; ?>

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
            <form id="deleteForm" method="POST" action="index.php?controller=laporanCamat&action=delete" style="display: inline;">
                <input type="hidden" name="id" id="deleteId">
                <button type="submit" class="btn btn-danger">Hapus</button>
            </form>
            <button type="button" class="btn btn-secondary" onclick="closeModal()">Batal</button>
        </div>
    </div>
</div>




<script>
// Auto-select the kecamatan and load desa when page loads (for edit mode)
document.addEventListener('DOMContentLoaded', function() {
    <?php if (isset($laporan)): ?>
        // Simulate loading desa after page load to ensure kecamatan is selected
        setTimeout(function() {
            // First, make sure the kecamatan is selected using the ID
            const kecamatanSelect = document.getElementById('id_kecamatan');
            const desaSelect = document.getElementById('id_desa');
            
            if (kecamatanSelect) {
                // Set the kecamatan based on the stored ID from the controller
                const kecamatanId = <?php echo json_encode($selected_kecamatan_id); ?>;
                
                if(kecamatanId) {
                    kecamatanSelect.value = kecamatanId;
                    
                    // Now load the desa based on selected kecamatan
                    loadDesa();
                    
                    // After desa is loaded, select the correct desa
                    setTimeout(function() {
                        const desaId = <?php echo json_encode($selected_desa_id); ?>;

                        if(desaId && desaSelect) {
                            desaSelect.value = desaId;

                            // Update hidden fields
                            const selectedDesa = desaSelect.options[desaSelect.selectedIndex];
                            if(selectedDesa) {
                                document.getElementById('hidden_nama_desa').value = selectedDesa.text;
                            }
                        }
                    }, 300); // Wait for desa to load
                }
            }
        }, 100); // Wait a bit to ensure DOM is fully loaded
    <?php endif; ?>
});

// Character counter
document.addEventListener('DOMContentLoaded', function() {
    // Load desa jika kecamatan sudah dipilih sebelumnya
    <?php if (isset($laporan) && $laporan['id_kecamatan']): ?>
        window.addEventListener('load', function() {
            // Simulate selecting the kecamatan to load the desa
            setTimeout(function() {
                loadDesa();
            }, 100);
        });
    <?php endif; ?>

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

    // Form validation
    const form = document.getElementById('laporanForm');
    const submitBtn = document.getElementById('submitBtn');

    if (form && submitBtn) {
        form.addEventListener('submit', function(e) {
            let isValid = true;
            const errors = [];

            // Validate required fields
            const namaPelapor = document.getElementById('nama_pelapor');
            const idDesa = document.getElementById('id_desa');
            const idKecamatan = document.getElementById('id_kecamatan');
            const waktuKejadian = document.getElementById('waktu_kejadian');
            const tujuan = document.getElementById('tujuan');
            const uraianLaporan = document.getElementById('uraian_laporan');

            if (!namaPelapor.value.trim()) {
                errors.push('Nama pelapor harus diisi');
                namaPelapor.classList.add('error');
                isValid = false;
            } else {
                namaPelapor.classList.remove('error');
            }

            if (!idDesa.value) {
                errors.push('Nama desa harus dipilih');
                idDesa.classList.add('error');
                isValid = false;
            } else {
                idDesa.classList.remove('error');
            }

            if (!idKecamatan.value) {
                errors.push('Nama kecamatan harus dipilih');
                idKecamatan.classList.add('error');
                isValid = false;
            } else {
                idKecamatan.classList.remove('error');
            }

            if (!waktuKejadian.value) {
                errors.push('Waktu kejadian harus diisi');
                waktuKejadian.classList.add('error');
                isValid = false;
            } else {
                waktuKejadian.classList.remove('error');
            }

            if (!tujuan.value) {
                errors.push('Tujuan laporan harus dipilih');
                tujuan.classList.add('error');
                isValid = false;
            } else {
                tujuan.classList.remove('error');
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

<?php include 'template/script.php'; ?>
</body>
</html>