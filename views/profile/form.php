<?php
$title = isset($profile) ? 'Edit Profile - ' : 'Tambah Profile Baru - ';
include 'views/layouts/admin-header.php';
?>

<body class="with-welcome-text">
  <div class="container-scroller">
    <?php include 'views/layouts/admin-navbar.php'; ?>
    <div class="container-fluid page-body-wrapper">
      <?php include 'views/layouts/admin-setting-panel.php'; ?>
      <?php include 'views/layouts/admin-sidebar.php'; ?>
      <div class="main-panel">
        <div class="content-wrapper">
          <div class="row">
            <div class="col-sm-12">

                            
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
                  <a href="<?= route('profile', 'index') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                  </a>
                </div>
              </div>

              
              <div id="toastContainer" style="position: fixed; top: 20px; right: 20px; z-index: 9999;"></div>

              
              <div class="card border-0 shadow-sm">
                <div class="card-body">
                  <form id="profileForm" method="POST" enctype="multipart/form-data"
                        action="<?php echo isset($profile) ? route('profile', 'update', ['id' => $profile['id_profile']]) : route('profile', 'store'); ?>"
                        novalidate>

                    <?php if (isset($profile)): ?>
                      <input type="hidden" name="id" value="<?php echo $profile['id_profile']; ?>">
                    <?php endif; ?>

                    
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

                    
                    <div class="row">
                      <div class="col-12">
                        <div class="d-flex gap-2 justify-content-end">
                          <a href="<?= route('profile', 'index') ?>" class="btn btn-secondary">
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
  <?php include 'views/layouts/admin-script.php'; ?>

  <script></script>
</body>
</html>