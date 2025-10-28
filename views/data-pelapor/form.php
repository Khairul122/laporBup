<?php include('template/header.php'); ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

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

              <!-- Page Header -->
              <div class="page-header mb-4">
                <div class="d-flex align-items-center">
                  <div>
                    <h3 class="page-title mb-1">
                      <i class="fas fa-<?php echo $dataPelapor ? 'edit' : 'plus-circle'; ?> text-primary"></i>
                      <?php echo $dataPelapor ? 'Edit Data Pelapor' : 'Tambah Data Pelapor'; ?>
                    </h3>
                    <nav aria-label="breadcrumb">
                      <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php?controller=dashboard&action=admin">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="index.php?controller=dataPelapor">Data Pelapor</a></li>
                        <li class="breadcrumb-item active"><?php echo $dataPelapor ? 'Edit' : 'Tambah'; ?></li>
                      </ol>
                    </nav>
                  </div>
                </div>
              </div>

              <!-- Form Card Full Screen -->
              <div class="row">
                <div class="col-12">
                  <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 pt-4">
                      <div class="d-flex align-items-center justify-content-between">
                        <div>
                          <h5 class="card-title mb-0">
                            <i class="fas fa-user-plus text-primary"></i>
                            Form Data Pelapor
                          </h5>
                          <small class="text-muted">
                            <?php echo $dataPelapor ? 'Perbarui informasi data pelapor yang sudah ada' : 'Tambahkan data pelapor baru ke sistem'; ?>
                          </small>
                        </div>
                        <div class="text-end">
                          <span class="badge bg-<?php echo $dataPelapor ? 'warning' : 'success'; ?>">
                            <i class="fas fa-<?php echo $dataPelapor ? 'edit' : 'plus-circle'; ?>"></i>
                            <?php echo $dataPelapor ? 'Edit Data' : 'Tambah Data'; ?>
                          </span>
                        </div>
                      </div>
                    </div>
                    <div class="card-body pb-4">
                      <form id="pelaporForm" onsubmit="savePelapor(event)">
                        <input type="hidden" name="id" id="pelaporId" value="<?php echo $dataPelapor['id_user'] ?? ''; ?>">

                        <!-- Username Field -->
                        <div class="form-group mb-4">
                          <label for="username" class="form-label fw-semibold">
                            Username <span class="text-danger">*</span>
                          </label>
                          <div class="input-group">
                            <span class="input-group-text">
                              <i class="fas fa-user"></i>
                            </span>
                            <input type="text" class="form-control" id="username" name="username"
                                   value="<?php echo htmlspecialchars($dataPelapor['username'] ?? ''); ?>"
                                   placeholder="Masukkan username" required>
                          </div>
                          <small class="text-muted">Username minimal 3 karakter, hanya huruf, angka, dan underscore</small>
                        </div>

                        <!-- Email Field -->
                        <div class="form-group mb-4">
                          <label for="email" class="form-label fw-semibold">
                            Email <span class="text-danger">*</span>
                          </label>
                          <div class="input-group">
                            <span class="input-group-text">
                              <i class="fas fa-envelope"></i>
                            </span>
                            <input type="email" class="form-control" id="email" name="email"
                                   value="<?php echo htmlspecialchars($dataPelapor['email'] ?? ''); ?>"
                                   placeholder="nama@contoh.com" required>
                          </div>
                          <small class="text-muted">Email harus valid dan akan digunakan untuk login</small>
                        </div>

                        <!-- Role Field -->
                        <div class="form-group mb-4">
                          <label for="role" class="form-label fw-semibold">
                            Role <span class="text-danger">*</span>
                          </label>
                          <div class="input-group">
                            <span class="input-group-text">
                              <i class="fas fa-user-tag"></i>
                            </span>
                            <select class="form-select" id="role" name="role" required onchange="updateFormFields()">
                              <option value="">Pilih Role</option>
                              <option value="camat" <?php echo ($dataPelapor['role'] ?? '') === 'camat' ? 'selected' : ''; ?>>
                                <i class="fas fa-map-marker-alt"></i> Camat
                              </option>
                              <option value="opd" <?php echo ($dataPelapor['role'] ?? '') === 'opd' ? 'selected' : ''; ?>>
                                <i class="fas fa-building"></i> OPD
                              </option>
                            </select>
                          </div>
                          <small class="text-muted">Pilih role pelapor (Camat atau OPD)</small>
                        </div>

                        <!-- Jabatan Field -->
                        <div class="form-group mb-4">
                          <label for="jabatan" class="form-label fw-semibold">
                            Jabatan <span class="text-danger">*</span>
                          </label>
                          <div class="input-group">
                            <span class="input-group-text">
                              <i class="fas fa-briefcase"></i>
                            </span>
                            <input type="text" class="form-control" id="jabatan" name="jabatan"
                                   value="<?php echo htmlspecialchars($dataPelapor['jabatan'] ?? ''); ?>"
                                   placeholder="Contoh: Camat Kecamatan X, Kepala Dinas Y" required>
                          </div>
                          <small class="text-muted">Jabatan lengkap pelapor</small>
                        </div>

                        <!-- Password Field -->
                        <div class="form-group mb-4">
                          <label for="password" class="form-label fw-semibold">
                            Password <span class="text-danger <?php echo $dataPelapor ? '' : '*'; ?>"></span>
                          </label>
                          <div class="input-group">
                            <span class="input-group-text">
                              <i class="fas fa-lock"></i>
                            </span>
                            <input type="password" class="form-control" id="password" name="password"
                                   placeholder="<?php echo $dataPelapor ? 'Kosongkan jika tidak ingin mengubah password' : 'Masukkan password'; ?>"
                                   <?php echo $dataPelapor ? '' : 'required'; ?> minlength="6">
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">
                              <i class="fas fa-eye" id="password-eye"></i>
                            </button>
                          </div>
                          <small class="text-muted">Password minimal 6 karakter</small>
                        </div>

                        <!-- Confirm Password Field -->
                        <div class="form-group mb-4">
                          <label for="confirm_password" class="form-label fw-semibold">
                            Konfirmasi Password <span class="text-danger <?php echo $dataPelapor ? '' : '*'; ?>"></span>
                          </label>
                          <div class="input-group">
                            <span class="input-group-text">
                              <i class="fas fa-lock"></i>
                            </span>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password"
                                   placeholder="Ulangi password"
                                   <?php echo $dataPelapor ? '' : 'required'; ?> minlength="6">
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('confirm_password')">
                              <i class="fas fa-eye" id="confirm_password-eye"></i>
                            </button>
                          </div>
                          <small class="text-muted">Konfirmasi password harus sama dengan password</small>
                        </div>

                        
                        
                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between">
                          <a href="index.php?controller=dataPelapor" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                          </a>
                          <div>
                            <button type="reset" class="btn btn-outline-secondary me-2">
                              <i class="fas fa-undo"></i> Reset
                            </button>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                              <i class="fas fa-save"></i>
                              <span id="submitBtnText"><?php echo $dataPelapor ? 'Update Data' : 'Simpan Data'; ?></span>
                            </button>
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
    </div>
  </div>

  <?php include 'template/script.php'; ?>

  <!-- Custom JavaScript -->
  <script>
    // Toggle password visibility
    function togglePassword(fieldId) {
      const passwordField = document.getElementById(fieldId);
      const eyeIcon = document.getElementById(fieldId + '-eye');

      if (passwordField.type === 'password') {
        passwordField.type = 'text';
        eyeIcon.className = 'fas fa-eye-slash';
      } else {
        passwordField.type = 'password';
        eyeIcon.className = 'fas fa-eye';
      }
    }

    // Update form fields based on role
    function updateFormFields() {
      const role = document.getElementById('role').value;
      const jabatanField = document.getElementById('jabatan');

      if (role === 'camat') {
        jabatanField.placeholder = 'Contoh: Camat Kecamatan X';
      } else if (role === 'opd') {
        jabatanField.placeholder = 'Contoh: Kepala Dinas Y, Sekretaris OPD Z';
      } else {
        jabatanField.placeholder = 'Contoh: Camat Kecamatan X, Kepala Dinas Y';
      }
    }

    // Save pelapor
    function savePelapor(event) {
      event.preventDefault();

      const form = document.getElementById('pelaporForm');
      const formData = new FormData(form);
      const submitBtn = document.getElementById('submitBtn');
      const submitBtnText = document.getElementById('submitBtnText');

      // Show loading state
      submitBtn.disabled = true;
      submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';

      // Convert FormData to URLSearchParams
      const params = new URLSearchParams();
      for (const [key, value] of formData.entries()) {
        params.append(key, value);
      }

      fetch('index.php?controller=dataPelapor&action=save', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: params.toString()
      })
      .then(response => response.json())
      .then(result => {
        if (result.success) {
          showNotification('success', result.message);

          // Redirect after delay
          setTimeout(() => {
            window.location.href = 'index.php?controller=dataPelapor';
          }, 1500);
        } else {
          showNotification('danger', result.message);
        }
      })
      .catch(error => {
        showNotification('danger', 'Error: ' + error.message);
      })
      .finally(() => {
        // Reset button state
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-save"></i> <span id="submitBtnText">' + submitBtnText.textContent + '</span>';
      });
    }

    // Show notification
    function showNotification(type, message) {
      // Remove existing alerts
      const existingAlerts = document.querySelectorAll('.alert');
      existingAlerts.forEach(alert => alert.remove());

      // Create new alert
      const alertDiv = document.createElement('div');
      alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
      alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
      alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      `;

      document.body.appendChild(alertDiv);

      // Auto remove after 5 seconds
      setTimeout(() => {
        if (alertDiv.parentNode) {
          alertDiv.remove();
        }
      }, 5000);
    }

    // Real-time validation
    document.getElementById('username').addEventListener('input', function(e) {
      const value = e.target.value;
      // Allow only letters, numbers, and underscore
      e.target.value = value.replace(/[^a-zA-Z0-9_]/g, '');
    });

    // Password validation
    document.getElementById('password').addEventListener('input', function(e) {
      const password = e.target.value;
      const confirmField = document.getElementById('confirm_password');

      if (password.length > 0 && password.length < 6) {
        e.target.classList.add('is-invalid');
      } else {
        e.target.classList.remove('is-invalid');
      }

      // Check confirm password match
      if (confirmField.value.length > 0) {
        if (password !== confirmField.value) {
          confirmField.classList.add('is-invalid');
        } else {
          confirmField.classList.remove('is-invalid');
        }
      }
    });

    document.getElementById('confirm_password').addEventListener('input', function(e) {
      const password = document.getElementById('password').value;
      const confirmPassword = e.target.value;

      if (confirmPassword.length > 0 && password !== confirmPassword) {
        e.target.classList.add('is-invalid');
      } else {
        e.target.classList.remove('is-invalid');
      }
    });

    // Email validation
    document.getElementById('email').addEventListener('blur', function(e) {
      const email = e.target.value;
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

      if (email.length > 0 && !emailRegex.test(email)) {
        e.target.classList.add('is-invalid');
      } else {
        e.target.classList.remove('is-invalid');
      }
    });

    // Initialize form on load
    document.addEventListener('DOMContentLoaded', function() {
      updateFormFields();

      // Focus on first empty field
      const firstEmpty = document.querySelector('input:not([readonly]):not([disabled]):not([value])');
      if (firstEmpty) {
        firstEmpty.focus();
      }
    });
  </script>

  <style>
    .form-control.is-invalid {
      border-color: #dc3545;
    }

    .form-control.is-invalid:focus {
      border-color: #dc3545;
      box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }

    .alert {
      animation: slideInRight 0.3s ease-out;
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

    .spinner-border-sm {
      width: 1rem;
      height: 1rem;
    }

    .input-group-text {
      background-color: #f8f9fa;
      border-right: none;
    }

    .form-control:focus + .input-group-text {
      border-color: #86b7fe;
    }

    .card {
      transition: transform 0.2s ease-in-out;
    }

    .card:hover {
      transform: translateY(-2px);
    }
  </style>
</body>
</html>