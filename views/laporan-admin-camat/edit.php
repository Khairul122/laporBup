<?php include('template/header.php'); ?>

<body class="with-welcome-text">
  <div class="container-scroller">
    <!-- Navbar -->
    <?php include 'template/navbar.php'; ?>

    <div class="container-fluid page-body-wrapper">
      <!-- Settings Panel -->
      <?php include 'template/setting_panel.php'; ?>

      <!-- Sidebar -->
      <?php include 'template/sidebar.php'; ?>

      <!-- Main Panel -->
      <div class="main-panel">
        <div class="content-wrapper">
          <div class="row">
            <div class="col-12">

              <!-- Success/Error Messages -->
              <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                  <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
              <?php endif; ?>

              <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                  <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
              <?php endif; ?>

              <?php if ($laporan): ?>
                <!-- Header Section -->
                <div class="card mb-4">
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col-md-8">
                        <div class="d-flex align-items-center">
                          <div class="avatar-lg me-3">
                            <div class="avatar-title bg-primary bg-opacity-10 rounded">
                              <i class="mdi mdi-pencil text-primary"></i>
                            </div>
                          </div>
                          <div>
                            <h2 class="mb-1">Edit Laporan Camat</h2>
                            <p class="text-muted mb-0"><?php echo htmlspecialchars($laporan['nama_kecamatan']); ?></p>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-4 text-md-end">
                        <span class="badge bg-secondary">ID: #<?php echo $laporan['id_laporan_camat']; ?></span>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Edit Form -->
                <form method="POST" enctype="multipart/form-data" id="editForm">
                  <input type="hidden" name="id_laporan_camat" value="<?php echo $laporan['id_laporan_camat']; ?>">

                  <div class="row">
                    <!-- Main Form Section -->
                    <div class="col-lg-8">
                      <!-- Informasi Laporan Card -->
                      <div class="card mb-4">
                        <div class="card-header">
                          <h5 class="card-title mb-0">Informasi Laporan</h5>
                        </div>
                        <div class="card-body">
                          <!-- Nama Kecamatan -->
                          <div class="mb-3">
                            <label for="nama_kecamatan" class="form-label">Nama Kecamatan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama_kecamatan" name="nama_kecamatan"
                                   value="<?php echo htmlspecialchars($laporan['nama_kecamatan']); ?>"
                                   required maxlength="150"
                                   placeholder="Masukkan nama kecamatan">
                            <small class="text-muted">Masukkan nama lengkap kecamatan yang membuat laporan</small>
                          </div>

                          <!-- Nama Kegiatan -->
                          <div class="mb-3">
                            <label for="nama_kegiatan" class="form-label">Nama Kegiatan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama_kegiatan" name="nama_kegiatan"
                                   value="<?php echo htmlspecialchars($laporan['nama_kegiatan']); ?>"
                                   required maxlength="150"
                                   placeholder="Masukkan nama kegiatan yang dilaporkan">
                            <small class="text-muted">Jelaskan nama kegiatan yang menjadi subjek laporan</small>
                          </div>

                          <!-- Uraian Laporan -->
                          <div class="mb-3">
                            <label for="uraian_laporan" class="form-label">Uraian Laporan <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="uraian_laporan" name="uraian_laporan"
                                      rows="6" required placeholder="Jelaskan detail kegiatan atau temuan yang dilaporkan..."><?php echo htmlspecialchars($laporan['uraian_laporan']); ?></textarea>
                            <div class="d-flex justify-content-between mt-1">
                              <small class="text-muted">Jelaskan dengan detail mengenai kegiatan atau temuan yang dilaporkan</small>
                              <small class="text-muted">
                                <span id="charCount"><?php echo strlen($laporan['uraian_laporan']); ?></span> karakter
                              </small>
                            </div>
                          </div>

                          <!-- File Upload -->
                          <div class="mb-3">
                            <label for="upload_file" class="form-label">File Lampiran</label>
                            <input type="file" class="form-control" id="upload_file" name="upload_file"
                                   accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx">
                            <small class="text-muted">Format: JPG, PNG, PDF, DOC, DOCX, XLS, XLSX (Maksimal 5MB)</small>
                            <?php if ($laporan['upload_file']): ?>
                              <div class="mt-2">
                                <div class="alert alert-info py-2">
                                  <div class="d-flex align-items-center">
                                    <i class="mdi mdi-file-document me-2"></i>
                                    <div class="flex-grow-1">
                                      <small class="fw-semibold">File Saat Ini:</small>
                                      <div><?php echo htmlspecialchars(basename($laporan['upload_file'])); ?></div>
                                    </div>
                                    <a href="<?php echo htmlspecialchars($laporan['upload_file']); ?>" target="_blank"
                                       class="btn btn-sm btn-outline-primary">
                                      <i class="mdi mdi-download me-1"></i>Download
                                    </a>
                                  </div>
                                </div>
                              </div>
                            <?php endif; ?>
                          </div>
                        </div>
                      </div>

                      <!-- Status Card -->
                      <div class="card mb-4">
                        <div class="card-header">
                          <h5 class="card-title mb-0">Status</h5>
                        </div>
                        <div class="card-body">
                          <div class="row">
                            <div class="col-md-6">
                              <div class="mb-3">
                                <label for="status_laporan" class="form-label">Status Laporan <span class="text-danger">*</span></label>
                                <select class="form-select" id="status_laporan" name="status_laporan" required>
                                  <option value="">Pilih Status</option>
                                  <option value="baru" <?php echo $laporan['status_laporan'] === 'baru' ? 'selected' : ''; ?>>Baru</option>
                                  <option value="diproses" <?php echo $laporan['status_laporan'] === 'diproses' ? 'selected' : ''; ?>>Diproses</option>
                                  <option value="selesai" <?php echo $laporan['status_laporan'] === 'selesai' ? 'selected' : ''; ?>>Selesai</option>
                                </select>
                              </div>
                            </div>

                            <div class="col-md-6">
                              <div class="mb-3">
                                <label class="form-label">Tujuan Laporan</label>
                                <div class="bg-light p-2 rounded">
                                  <div class="d-flex align-items-center">
                                    <i class="mdi mdi-map-marker text-primary me-2"></i>
                                    <div>
                                      <div class="fw-semibold"><?php echo htmlspecialchars(ucfirst($laporan['tujuan'])); ?></div>
                                      <small class="text-muted">Tujuan laporan tidak dapat diubah</small>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Sidebar Section -->
                    <div class="col-lg-4">
                      <!-- Info Laporan Card -->
                      <div class="card mb-4">
                        <div class="card-header">
                          <h5 class="card-title mb-0">Informasi Laporan</h5>
                        </div>
                        <div class="card-body">
                          <table class="table table-sm table-borderless">
                            <tr>
                              <td class="text-muted" width="40%">ID Laporan</td>
                              <td>#<?php echo $laporan['id_laporan_camat']; ?></td>
                            </tr>
                            <tr>
                              <td class="text-muted">Tujuan</td>
                              <td><?php echo htmlspecialchars(ucfirst($laporan['tujuan'])); ?></td>
                            </tr>
                            <tr>
                              <td class="text-muted">Dibuat</td>
                              <td>
                                <?php
                                $hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                                $bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                                          'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                                $timestamp = strtotime($laporan['created_at']);
                                $nama_hari = $hari[date('w', $timestamp)];
                                $tanggal = date('d', $timestamp);
                                $nama_bulan = $bulan[date('n', $timestamp) - 1];
                                $tahun = date('Y', $timestamp);
                                $jam = date('H:i', $timestamp);
                                $tanggal_indo = "$nama_hari, $tanggal $nama_bulan $tahun";
                                echo "$tanggal_indo<br><small>$jam</small>";
                                ?>
                              </td>
                            </tr>
                            <?php if ($laporan['updated_at'] !== $laporan['created_at']): ?>
                              <tr>
                                <td class="text-muted">Diperbarui</td>
                                <td>
                                  <?php
                                  $timestamp_update = strtotime($laporan['updated_at']);
                                  $nama_hari_update = $hari[date('w', $timestamp_update)];
                                  $tanggal_update = date('d', $timestamp_update);
                                  $nama_bulan_update = $bulan[date('n', $timestamp_update) - 1];
                                  $tahun_update = date('Y', $timestamp_update);
                                  $jam_update = date('H:i', $timestamp_update);
                                  $tanggal_update_indo = "$nama_hari_update, $tanggal_update $nama_bulan_update $tahun_update";
                                  echo "$tanggal_update_indo<br><small>$jam_update</small>";
                                  ?>
                                </td>
                              </tr>
                            <?php endif; ?>
                            <?php if ($laporan['username']): ?>
                              <tr>
                                <td class="text-muted">User</td>
                                <td>
                                  <div class="d-flex align-items-center">
                                    <div class="avatar-xs me-2">
                                      <div class="avatar-title bg-secondary rounded-circle">
                                        <i class="mdi mdi-account text-white" style="font-size: 0.6rem;"></i>
                                      </div>
                                    </div>
                                    <div>
                                      <div class="fw-semibold"><?php echo htmlspecialchars($laporan['username']); ?></div>
                                      <?php if ($laporan['jabatan']): ?>
                                        <small class="text-muted"><?php echo htmlspecialchars($laporan['jabatan']); ?></small>
                                      <?php endif; ?>
                                    </div>
                                  </div>
                                </td>
                              </tr>
                            <?php endif; ?>
                          </table>
                        </div>
                      </div>

                      <!-- Actions Card -->
                      <div class="card mb-4">
                        <div class="card-header">
                          <h5 class="card-title mb-0">Aksi</h5>
                        </div>
                        <div class="card-body">
                          <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                              <i class="mdi mdi-content-save me-2"></i>Simpan Perubahan
                            </button>

                            <a href="index.php?controller=laporanCamatAdmin&action=detail&id=<?php echo $laporan['id_laporan_camat']; ?>"
                               class="btn btn-outline-secondary">
                              <i class="mdi mdi-eye me-2"></i>Lihat Detail
                            </a>

                            <a href="index.php?controller=laporanCamatAdmin&action=index"
                               class="btn btn-outline-secondary">
                              <i class="mdi mdi-arrow-left me-2"></i>Kembali
                            </a>
                          </div>

                          <hr>

                          <div class="text-center">
                            <small class="text-muted">
                              <i class="mdi mdi-information me-1"></i>
                              Perubahan akan tersimpan secara otomatis
                            </small>
                          </div>
                        </div>
                      </div>

                      <!-- Tips Card -->
                      <div class="card bg-light">
                        <div class="card-body">
                          <h6 class="mb-2">
                            <i class="mdi mdi-lightbulb me-2"></i>
                            Tips Edit
                          </h6>
                          <ul class="small text-muted mb-0">
                            <li>Pastikan semua field wajib diisi</li>
                            <li>Uraian laporan minimal 10 karakter</li>
                            <li>File maksimal 5MB dengan format yang sesuai</li>
                            <li>Gunakan shortcut Ctrl+S untuk menyimpan</li>
                          </ul>
                        </div>
                      </div>
                    </div>
                  </div>
                </form>
              <?php else: ?>
                <!-- Error State -->
                <div class="card">
                  <div class="card-body text-center py-5">
                    <i class="mdi mdi-alert-circle-outline text-muted display-1"></i>
                    <h4 class="mt-3">Data Tidak Ditemukan!</h4>
                    <p class="text-muted">Laporan yang ingin diedit tidak ditemukan dalam database.</p>
                    <a href="index.php?controller=laporanCamatAdmin&action=index" class="btn btn-primary">
                      Kembali ke Daftar Laporan
                    </a>
                  </div>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php include 'template/script.php'; ?>

  <style>
    .avatar-lg {
      width: 64px;
      height: 64px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.5rem;
    }

    .avatar-xs {
      width: 24px;
      height: 24px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .avatar-title {
      width: 100%;
      height: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .breadcrumb {
      background-color: transparent;
      padding: 0;
      margin-bottom: 1rem;
    }

    .breadcrumb-item + .breadcrumb-item::before {
      content: ">";
      color: #6c757d;
    }

    .card {
      border: 1px solid #e9ecef;
      border-radius: 0.5rem;
    }

    .table-sm td {
      padding: 0.5rem;
      vertical-align: top;
    }

    .badge {
      font-weight: 500;
    }

    .alert {
      border-radius: 0.5rem;
    }

    /* Character counter colors */
    .text-success {
      color: #198754 !important;
    }

    .text-warning {
      color: #ffc107 !important;
    }

    .text-danger {
      color: #dc3545 !important;
    }
  </style>

  <script>
    // Character counter
    const textarea = document.getElementById('uraian_laporan');
    const charCount = document.getElementById('charCount');

    if (textarea && charCount) {
      textarea.addEventListener('input', function() {
        const length = this.value.length;
        charCount.textContent = length;

        // Change color based on length
        if (length < 10) {
          charCount.className = 'text-danger';
        } else if (length < 50) {
          charCount.className = 'text-warning';
        } else {
          charCount.className = 'text-success';
        }
      });
    }

    // Form validation
    const editForm = document.getElementById('editForm');
    if (editForm) {
      editForm.addEventListener('submit', function(e) {
        // Additional validation
        const uraian = document.getElementById('uraian_laporan').value.trim();
        const namaKecamatan = document.getElementById('nama_kecamatan').value.trim();
        const namaKegiatan = document.getElementById('nama_kegiatan').value.trim();

        if (uraian.length < 10) {
          e.preventDefault();
          alert('Uraian laporan minimal 10 karakter!');
          return false;
        }

        // File validation
        const fileInput = document.getElementById('upload_file');
        if (fileInput.files[0]) {
          const file = fileInput.files[0];
          const maxSize = 5 * 1024 * 1024; // 5MB
          const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf',
                               'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                               'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];

          if (file.size > maxSize) {
            e.preventDefault();
            alert('Ukuran file terlalu besar! Maksimal 5MB.');
            return false;
          }

          if (!allowedTypes.includes(file.type)) {
            e.preventDefault();
            alert('Tipe file tidak diizinkan! Gunakan format yang sesuai.');
            return false;
          }
        }

        // Confirmation
        if (!confirm('Apakah Anda yakin ingin menyimpan perubahan pada laporan ini?')) {
          e.preventDefault();
          return false;
        }

        // Show loading state
        const submitBtn = editForm.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="mdi mdi-loading mdi-spin me-2"></i>Menyimpan...';
        submitBtn.disabled = true;

        setTimeout(() => {
          submitBtn.innerHTML = originalText;
          submitBtn.disabled = false;
        }, 5000);
      });
    }

    // Auto-save draft
    let autoSaveTimer;
    const draftKey = 'laporan_draft_<?php echo $laporan['id_laporan_camat'] ?? 0; ?>';

    function saveDraft() {
      const formData = new FormData(editForm);
      const data = {};
      for (let [key, value] of formData.entries()) {
        if (key !== 'upload_file') { // Don't save file to draft
          data[key] = value;
        }
      }
      localStorage.setItem(draftKey, JSON.stringify(data));
      alert('Draft berhasil disimpan!');
    }

    function loadDraft() {
      const savedDraft = localStorage.getItem(draftKey);
      if (savedDraft) {
        const draftData = JSON.parse(savedDraft);
        Object.keys(draftData).forEach(function(key) {
          const element = editForm.querySelector('[name="' + key + '"]');
          if (element && element.type !== 'file') {
            element.value = draftData[key];
          }
        });
      }
    }

    function clearDraft() {
      localStorage.removeItem(draftKey);
    }

    // Auto-save every 30 seconds
    function startAutoSave() {
      autoSaveTimer = setInterval(() => {
        const formData = new FormData(editForm);
        const data = {};
        for (let [key, value] of formData.entries()) {
          if (key !== 'upload_file') {
            data[key] = value;
          }
        }
        localStorage.setItem(draftKey, JSON.stringify(data));
        console.log('Draft auto-saved');
      }, 30000);
    }

    // Input change listener for auto-save
    const formElements = editForm.querySelectorAll('input, textarea, select');
    formElements.forEach(function(element) {
      element.addEventListener('input', function() {
        clearTimeout(autoSaveTimer);
        autoSaveTimer = setTimeout(() => {
          const formData = new FormData(editForm);
          const data = {};
          for (let [key, value] of formData.entries()) {
            if (key !== 'upload_file') {
              data[key] = value;
            }
          }
          localStorage.setItem(draftKey, JSON.stringify(data));
        }, 3000);
      });
    });

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
      // Ctrl/Cmd + S untuk save
      if ((e.ctrlKey || e.metaKey) && e.key === 's') {
        e.preventDefault();
        editForm.dispatchEvent(new Event('submit'));
      }

      // Ctrl/Cmd + D untuk save draft
      if ((e.ctrlKey || e.metaKey) && e.key === 'd') {
        e.preventDefault();
        saveDraft();
      }

      // Escape untuk kembali
      if (e.key === 'Escape') {
        if (confirm('Apakah Anda yakin ingin kembali? Perubahan yang belum tersimpan akan hilang.')) {
          window.location.href = 'index.php?controller=laporanCamatAdmin&action=index';
        }
      }
    });

    // File input preview
    const fileInput = document.getElementById('upload_file');
    if (fileInput) {
      fileInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
          const fileSize = (file.size / 1024 / 1024).toFixed(2);
          const fileName = file.name;

          // Update the info alert
          const existingAlert = this.parentNode.querySelector('.alert');
          if (existingAlert) {
            const fileDiv = existingAlert.querySelector('.flex-grow-1 div');
            if (fileDiv) {
              fileDiv.innerHTML = `<small class="fw-semibold">File Baru:</small><div>${fileName} (${fileSize} MB)</div>`;
            }
          }
        }
      });
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
      loadDraft();
      startAutoSave();
    });

    // Clear draft on successful submit
    <?php if (isset($_SESSION['success'])): ?>
      clearDraft();
    <?php endif; ?>
  </script>
</body>
</html>