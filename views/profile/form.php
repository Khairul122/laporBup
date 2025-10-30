<?php
$title = isset($profile) ? 'Edit Profile - LaporBup' : 'Tambah Profile Baru - LaporBup';
include 'template/header.php';
?>

<body class="with-welcome-text">
  <div class="container-scroller">
    <?php include 'template/navbar.php'; ?>
    <div class="container-fluid page-body-wrapper">
      <?php include 'template/setting_panel.php'; ?>
      <?php include 'template/sidebar.php'; ?>
      <div class="main-panel">
        <div class="content-wrapper">
          <div class="row">
            <div class="col-sm-12">

                            <!-- Header Section -->
              <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                  <h3 class="page-title mb-1">
                    <i class="fas fa-<?php echo isset($profile) ? 'edit' : 'plus-circle'; ?>"></i>
                    <?php echo isset($profile) ? 'Edit Profile' : 'Tambah Profile Baru'; ?>
                  </h3>
                  <p class="text-muted mb-0">
                    <?php echo isset($profile) ? 'Perbarui informasi profile aplikasi' : 'Tambahkan profile aplikasi baru'; ?>
                  </p>
                </div>
                <div>
                  <a href="index.php?controller=profile&action=index" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                  </a>
                </div>
              </div>

              <!-- Toast Notifications Container -->
              <div id="toastContainer" style="position: fixed; top: 20px; right: 20px; z-index: 9999;"></div>

              <!-- Form Container -->
              <div class="card border-0 shadow-sm">
                <div class="card-body">
                  <form id="profileForm" method="POST" enctype="multipart/form-data"
                        action="index.php?controller=profile&action=<?php echo isset($profile) ? 'update' : 'store'; ?>"
                        novalidate>

                    <?php if (isset($profile)): ?>
                      <input type="hidden" name="id" value="<?php echo $profile['id_profile']; ?>">
                    <?php endif; ?>

                    <!-- Logo Upload Section -->
                    <div class="row mb-4">
                      <div class="col-12">
                        <h5 class="mb-3">
                          <i class="fas fa-image text-success me-2"></i>
                          Logo Aplikasi
                        </h5>
                      </div>

                      <div class="col-md-6">
                        <div class="mb-3">
                          <label for="logo" class="form-label">
                            <i class="fas fa-upload text-muted me-2"></i>
                            Upload Logo (Opsional)
                          </label>
                          <input type="file"
                                 class="form-control"
                                 id="logo"
                                 name="logo"
                                 accept="image/*">
                          <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i>
                            Format: JPG, PNG, GIF, WebP. Maksimal 5MB.
                          </div>
                        </div>

                        <?php if (isset($profile) && !empty($profile['logo'])): ?>
                          <div class="alert alert-info">
                            <strong>Logo Saat Ini:</strong><br>
                            <img src="<?php echo htmlspecialchars($profile['logo']); ?>"
                                 alt="Current Logo"
                                 style="max-width: 100px; max-height: 100px; border-radius: 8px; margin-top: 5px;">
                          </div>
                        <?php endif; ?>
                      </div>

                      <div class="col-md-6">
                        <div class="mb-3">
                          <label class="form-label">
                            <i class="fas fa-eye text-muted me-2"></i>
                            Preview Logo
                          </label>
                          <div class="border rounded p-3 text-center bg-light" style="min-height: 120px;">
                            <div id="logoPreviewContainer">
                              <?php if (isset($profile) && !empty($profile['logo'])): ?>
                                <img id="uploadLogoPreview" src="<?php echo htmlspecialchars($profile['logo']); ?>"
                                     alt="Logo Preview"
                                     style="max-width: 100px; max-height: 100px; border-radius: 8px;">
                              <?php else: ?>
                                <div id="uploadLogoPreview" class="text-muted">
                                  <i class="fas fa-image fa-3x mb-2"></i><br>
                                  <small>Logo akan muncul di sini</small>
                                </div>
                              <?php endif; ?>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Informasi Dasar Section -->
                    <div class="row mb-4">
                      <div class="col-12">
                        <h5 class="mb-3">
                          <i class="fas fa-info-circle text-primary me-2"></i>
                          Informasi Dasar
                        </h5>
                      </div>

                      <div class="col-md-8">
                        <div class="mb-3">
                          <label for="nama_aplikasi" class="form-label">
                            <i class="fas fa-laptop-code text-muted me-2"></i>
                            Nama Aplikasi <span class="text-danger">*</span>
                          </label>
                          <input type="text"
                                 class="form-control"
                                 id="nama_aplikasi"
                                 name="nama_aplikasi"
                                 placeholder="Masukkan nama aplikasi"
                                 value="<?php echo isset($old_input['nama_aplikasi']) ? htmlspecialchars($old_input['nama_aplikasi']) : (isset($profile) ? htmlspecialchars($profile['nama_aplikasi']) : ''); ?>"
                                 required>
                          <div class="form-text">Contoh: Lapor Camat, Sistem Laporan OPD, dll.</div>
                        </div>
                      </div>

                      <div class="col-md-4">
                        <div class="mb-3">
                          <label for="role" class="form-label">
                            <i class="fas fa-user-tag text-muted me-2"></i>
                            Role <span class="text-danger">*</span>
                          </label>
                          <select class="form-select" id="role" name="role" required>
                            <option value="">Pilih Role</option>
                            <option value="camat" <?php echo (isset($old_input['role']) ? $old_input['role'] : (isset($profile) ? $profile['role'] : '')) === 'camat' ? 'selected' : ''; ?>>
                              <i class="fas fa-user-tie"></i> Camat
                            </option>
                            <option value="opd" <?php echo (isset($old_input['role']) ? $old_input['role'] : (isset($profile) ? $profile['role'] : '')) === 'opd' ? 'selected' : ''; ?>>
                              <i class="fas fa-building"></i> OPD
                            </option>
                          </select>
                          <div class="form-text">Pilih role yang sesuai untuk aplikasi ini.</div>
                        </div>
                      </div>
                    </div>

                    <!-- Preview Section -->
                    <div class="row mb-4">
                      <div class="col-12">
                        <h5 class="mb-3">
                          <i class="fas fa-eye text-info me-2"></i>
                          Preview Profile
                        </h5>
                      </div>

                      <div class="col-12">
                        <div class="card bg-light">
                          <div class="card-body text-center py-4">
                            <div class="mb-3">
                              <div id="previewAvatar" class="mx-auto mb-3 position-relative">
                                <div id="previewLogoContainer" class="rounded-circle overflow-hidden bg-white border border-2 border-light shadow-sm"
                                     style="display: none; width: 80px; height: 80px;">
                                  <img id="previewLogo" src="" alt="Logo Preview"
                                       style="width: 100%; height: 100%; object-fit: cover;">
                                </div>
                                <div id="previewDefaultAvatar" class="bg-gradient-primary rounded-circle d-inline-flex align-items-center justify-content-center text-white shadow-sm"
                                     style="width: 80px; height: 80px; font-size: 24px; font-weight: bold;">
                                  <span id="previewInitials">??</span>
                                </div>
                              </div>
                            </div>
                            <h5 id="previewNama" class="mb-2">Nama Aplikasi</h5>
                            <span id="previewRole" class="badge bg-secondary">Role</span>
                          </div>
                        </div>
                        <div class="form-text mt-2">
                          <i class="fas fa-info-circle me-1"></i>
                          Preview akan diperbarui otomatis saat Anda mengetik atau upload logo.
                        </div>
                      </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="row">
                      <div class="col-12">
                        <div class="d-flex gap-2 justify-content-end">
                          <a href="index.php?controller=profile&action=index" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i> Batal
                          </a>
                          <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="fas fa-save me-2"></i>
                            <span><?php echo isset($profile) ? 'Update Profile' : 'Simpan Profile'; ?></span>
                          </button>
                        </div>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php include 'template/script.php'; ?>

  <script>
  // Define global functions
  function showToast(message, type = 'success') {
    const toastContainer = document.getElementById('toastContainer');
    const toast = document.createElement('div');
    const toastId = 'toast-' + Date.now();
    const iconHtml = type === 'success'
      ? '<i class="fas fa-check-circle me-2"></i>'
      : '<i class="fas fa-exclamation-circle me-2"></i>';
    const bgColor = type === 'success' ? '#10b981' : '#ef4444';

    toast.id = toastId;
    toast.className = 'd-flex align-items-center justify-content-between p-3 mb-2 text-white rounded shadow-lg';
    toast.style.cssText = `
      background: ${bgColor};
      min-width: 300px;
      animation: slideInRight 0.3s ease-out;
    `;
    toast.innerHTML = `
      <div class="d-flex align-items-center">
        ${iconHtml}
        <span>${message}</span>
      </div>
      <button type="button" class="btn-close btn-close-white ms-3" onclick="document.getElementById('${toastId}').remove()"></button>
    `;

    toastContainer.appendChild(toast);

    // Auto remove after 5 seconds
    setTimeout(() => {
      const toastElement = document.getElementById(toastId);
      if (toastElement) {
        toastElement.style.animation = 'slideOutRight 0.3s ease-out';
        setTimeout(() => toastElement.remove(), 300);
      }
    }, 5000);
  }

  // Form validation and initialization
  document.addEventListener('DOMContentLoaded', function() {
    const namaAplikasiInput = document.getElementById('nama_aplikasi');
    const roleSelect = document.getElementById('role');
    const logoInput = document.getElementById('logo');
    const form = document.getElementById('profileForm');
    const submitBtn = document.getElementById('submitBtn');

    // Logo preview functionality (defined within DOMContentLoaded)
    function previewLogo(event) {
      const file = event.target.files[0];
      const previewContainer = document.getElementById('logoPreviewContainer');
      const uploadLogoPreview = document.getElementById('uploadLogoPreview');

      if (file) {
        // Validate file size
        if (file.size > 5 * 1024 * 1024) {
          showToast('Ukuran file terlalu besar. Maksimal 5MB.', 'error');
          event.target.value = '';
          return;
        }

        // Validate file type (more flexible for JPEG)
        const allowedTypes = ['image/jpeg', 'image/pjpeg', 'image/png', 'image/gif', 'image/bmp', 'image/webp', 'image/svg+xml'];
        if (!allowedTypes.includes(file.type)) {
          showToast('Tipe file tidak didukung. Hanya gambar yang diperbolehkan. File type: ' + file.type, 'error');
          event.target.value = '';
          return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
          // Update upload preview section
          previewContainer.innerHTML = `<img id="uploadLogoPreview" src="${e.target.result}" alt="Logo Preview" style="max-width: 100px; max-height: 100px; border-radius: 8px;">`;

          // Update main preview section
          const previewLogoContainer = document.getElementById('previewLogoContainer');
          const previewDefaultAvatar = document.getElementById('previewDefaultAvatar');
          const previewLogo = document.getElementById('previewLogo');

          if (previewLogoContainer && previewLogo && previewDefaultAvatar) {
            previewLogo.src = e.target.result;
            previewLogoContainer.style.display = 'flex';
            previewLogoContainer.style.visibility = 'visible';
            previewDefaultAvatar.style.display = 'none';
            previewDefaultAvatar.style.visibility = 'hidden';
          }

          // Show success feedback
          showToast('Logo berhasil dipreview', 'success');
        };

        reader.onerror = function() {
          showToast('Gagal membaca file gambar', 'error');
          event.target.value = '';
        };

        reader.readAsDataURL(file);
      }
    }

    // Reset logo preview when file is cleared
    function resetLogoPreview() {
      const previewContainer = document.getElementById('logoPreviewContainer');
      const previewLogoContainer = document.getElementById('previewLogoContainer');
      const previewDefaultAvatar = document.getElementById('previewDefaultAvatar');

      // Reset upload preview
      previewContainer.innerHTML = `
        <div id="uploadLogoPreview" class="text-muted">
          <i class="fas fa-image fa-3x mb-2"></i><br>
          <small>Logo akan muncul di sini</small>
        </div>`;

      // Reset main preview
      if (previewLogoContainer && previewDefaultAvatar) {
        previewLogoContainer.style.display = 'none';
        previewLogoContainer.style.visibility = 'hidden';
        previewDefaultAvatar.style.display = 'block';
        previewDefaultAvatar.style.visibility = 'visible';
      }
    }

    // Preview functionality
    function updatePreview() {
      const namaAplikasi = namaAplikasiInput.value.trim();
      const role = roleSelect.value;

      // Update nama
      const previewNama = document.getElementById('previewNama');
      previewNama.textContent = namaAplikasi || 'Nama Aplikasi';

      // Update initials (only if no logo)
      const previewInitials = document.getElementById('previewInitials');
      const hasLogo = logoInput.files && logoInput.files[0];

      if (namaAplikasi && !hasLogo) {
        const words = namaAplikasi.split(' ');
        let initials = '';
        if (words.length >= 2) {
          initials = words[0].charAt(0) + words[1].charAt(0);
        } else {
          initials = namaAplikasi.substring(0, 2);
        }
        previewInitials.textContent = initials.toUpperCase();
      } else if (!hasLogo) {
        previewInitials.textContent = '??';
      }

      // Update role badge
      const previewRole = document.getElementById('previewRole');
      if (role === 'camat') {
        previewRole.textContent = 'Camat';
        previewRole.className = 'badge bg-info';
      } else if (role === 'opd') {
        previewRole.textContent = 'OPD';
        previewRole.className = 'badge bg-warning';
      } else {
        previewRole.textContent = 'Role';
        previewRole.className = 'badge bg-secondary';
      }
    }

    // Initialize preview
    updatePreview();

    // Initialize existing logo if in edit mode
    function initializeExistingLogo() {
      const existingLogoImg = document.querySelector('#uploadLogoPreview');
      const previewLogoContainer = document.getElementById('previewLogoContainer');
      const previewDefaultAvatar = document.getElementById('previewDefaultAvatar');
      const previewLogo = document.getElementById('previewLogo');

      if (existingLogoImg && existingLogoImg.src && existingLogoImg.src !== window.location.href &&
          previewLogoContainer && previewLogo && previewDefaultAvatar) {
        // Copy existing logo to main preview
        previewLogo.src = existingLogoImg.src;
        previewLogoContainer.style.display = 'flex';
        previewLogoContainer.style.visibility = 'visible';
        previewDefaultAvatar.style.display = 'none';
        previewDefaultAvatar.style.visibility = 'hidden';
      }
    }

    // Initialize existing logo with slight delay to ensure DOM is ready
    setTimeout(initializeExistingLogo, 100);

    // Update preview on input change
    namaAplikasiInput.addEventListener('input', updatePreview);
    roleSelect.addEventListener('change', updatePreview);

    // Handle logo input change
    logoInput.addEventListener('change', function(e) {
      if (!e.target.files || e.target.files.length === 0) {
        // File was cleared
        resetLogoPreview();
      } else {
        // File was selected
        previewLogo(e);
      }
    });

    // Form validation
    if (form && submitBtn) {
      form.addEventListener('submit', function(e) {
        let isValid = true;
        const errors = [];

        // Validate nama aplikasi
        if (!namaAplikasiInput.value.trim()) {
          errors.push('Nama aplikasi harus diisi');
          namaAplikasiInput.classList.add('is-invalid');
          isValid = false;
        } else {
          namaAplikasiInput.classList.remove('is-invalid');
        }

        // Validate role
        if (!roleSelect.value) {
          errors.push('Role harus dipilih');
          roleSelect.classList.add('is-invalid');
          isValid = false;
        } else {
          roleSelect.classList.remove('is-invalid');
        }

        // Show errors if any
        if (!isValid) {
          e.preventDefault();
          showErrors(errors);
        } else {
          // Show loading state
          submitBtn.disabled = true;
          submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';
        }
      });
    }

    // Show errors function
    function showErrors(errors) {
      const existingError = document.querySelector('.alert-danger');
      if (existingError) {
        existingError.remove();
      }

      const errorDiv = document.createElement('div');
      errorDiv.className = 'alert alert-danger alert-dismissible fade show';
      errorDiv.innerHTML = `
        <strong><i class="fas fa-exclamation-triangle me-2"></i>Kesalahan:</strong>
        <ul class="mb-0">
          ${errors.map(error => `<li>${error}</li>`).join('')}
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      `;

      const card = document.querySelector('.card-body');
      card.insertBefore(errorDiv, card.firstChild);

      // Scroll to top to see errors
      window.scrollTo({ top: 0, behavior: 'smooth' });

      // Auto remove after 5 seconds
      setTimeout(() => {
        if (errorDiv.parentElement) {
          errorDiv.remove();
        }
      }, 5000);
    }
  });

  // Show toast notifications on page load
  document.addEventListener('DOMContentLoaded', function() {
    <?php if (isset($_SESSION['success'])): ?>
      showToast("<?php echo addslashes($_SESSION['success']); unset($_SESSION['success']); ?>", 'success');
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
      showToast("<?php echo addslashes($_SESSION['error']); unset($_SESSION['error']); ?>", 'error');
    <?php endif; ?>
  });

  // Add CSS animations and styles
  const style = document.createElement('style');
  style.textContent = `
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

    @keyframes slideOutRight {
      from {
        transform: translateX(0);
        opacity: 1;
      }
      to {
        transform: translateX(100%);
        opacity: 0;
      }
    }

    .is-invalid {
      border-color: #dc3545 !important;
      box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
    }

    .form-select:invalid {
      border-color: #dc3545 !important;
    }

    /* Preview avatar positioning */
    #previewAvatar {
      position: relative;
      z-index: 10;
      width: 80px;
      height: 80px;
    }

    #previewLogoContainer {
      transition: all 0.3s ease;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
      position: absolute;
      top: 0;
      left: 0;
      width: 80px;
      height: 80px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    #previewDefaultAvatar {
      transition: all 0.3s ease;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
      position: absolute;
      top: 0;
      left: 0;
    }

    #previewLogoContainer:hover,
    #previewDefaultAvatar:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
    }

    /* Ensure logo fills the circle properly */
    #previewLogo {
      display: block;
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    /* Preview card improvements */
    .card.bg-light {
      border: 1px solid #e9ecef;
      transition: all 0.3s ease;
    }

    .card.bg-light:hover {
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
      border-color: #dee2e6;
    }
  `;
  document.head.appendChild(style);
  </script>
</body>
</html>