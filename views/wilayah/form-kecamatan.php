<?php include('template/header.php'); ?>

<style>
  /* Custom toast positioning */
  .toast-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 1055;
  }

  .toast {
    min-width: 250px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  }

  .toast.success {
    background-color: #10b981;
    border: 1px solid #10b981;
  }

  .toast.error {
    background-color: #ef4444;
    border: 1px solid #ef4444;
  }

  /* Ensure toast is above other elements */
  .position-fixed {
    z-index: 1055 !important;
  }
</style>

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

              <!-- Header -->
              <div class="page-header d-flex justify-content-between align-items-center mb-4">
                <div>
                  <h2 class="page-title"><?php echo $kecamatan ? 'Edit Kecamatan' : 'Tambah Kecamatan'; ?></h2>
                  <p class="text-muted mb-0">
                    <?php echo $kecamatan ? 'Ubah data kecamatan yang sudah ada' : 'Tambah data kecamatan baru'; ?>
                  </p>
                </div>
                <a href="../views/wilayah/index-kecamatan.php" class="btn btn-secondary">
                  <i class="mdi mdi-arrow-left me-2"></i> Kembali
                </a>
              </div>

              <!-- Form Card -->
              <div class="card">
                <div class="card-header">
                  <h5 class="card-title mb-0">
                    <i class="mdi mdi-map-marker-multiple me-2"></i>
                    Form <?php echo $kecamatan ? 'Edit' : 'Tambah'; ?> Kecamatan
                  </h5>
                </div>
                <div class="card-body">
                  <form id="kecamatanForm" method="POST" action="index.php?controller=kecamatan&action=save">
                    <input type="hidden" name="id_kecamatan" value="<?php echo $kecamatan ? $kecamatan['id_kecamatan'] : ''; ?>">

                    <div class="row mb-3">
                      <div class="col-md-8">
                        <label for="nama_kecamatan" class="form-label">
                          Nama Kecamatan <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                          <span class="input-group-text">
                            <i class="mdi mdi-map-marker"></i>
                          </span>
                          <input type="text" class="form-control" id="nama_kecamatan"
                                 name="nama_kecamatan"
                                 value="<?php echo $kecamatan ? htmlspecialchars($kecamatan['nama_kecamatan']) : ''; ?>"
                                 placeholder="Masukkan nama kecamatan" required>
                        </div>
                        <div class="invalid-feedback">
                          Nama kecamatan wajib diisi
                        </div>
                        <small class="text-muted">Masukkan nama kecamatan dengan huruf kapital di awal kata</small>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                          <i class="mdi mdi-content-save me-2"></i>
                          <?php echo $kecamatan ? 'Update' : 'Simpan'; ?>
                        </button>
                        <a href="index.php?controller=wilayah&action=index&tab=kecamatan"
                           class="btn btn-secondary">
                          <i class="mdi mdi-close-circle me-2"></i> Batal
                        </a>
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

  
  <!-- Custom Scripts -->
  <script>
    // Simple notification function
    function showNotification(message, type = 'success') {
      console.log('Showing notification:', message, type);

      // Create a simple toast notification
      const toastContainer = document.createElement('div');
      toastContainer.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
        background: ${type === 'success' ? '#10b981' : '#ef4444'};
        color: white;
        padding: 16px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        display: flex;
        align-items: center;
        justify-content: space-between;
        animation: slideIn 0.3s ease-out;
      `;

      toastContainer.innerHTML = `
        <div style="display: flex; align-items: center;">
          <i class="mdi ${type === 'success' ? 'mdi-check-circle' : 'mdi-alert-circle'} me-2"></i>
          <span>${message}</span>
        </div>
        <button style="background: none; border: none; color: white; cursor: pointer; font-size: 20px; margin-left: 10px;" onclick="this.parentElement.remove()">×</button>
      `;

      // Add animation
      const style = document.createElement('style');
      style.textContent = `
        @keyframes slideIn {
          from {
            transform: translateX(100%);
            opacity: 0;
          }
          to {
            transform: translateX(0);
            opacity: 1;
          }
        }
        @keyframes slideOut {
          from {
            transform: translateX(0);
            opacity: 1;
          }
          to {
            transform: translateX(100%);
            opacity: 0;
          }
        }
      `;
      document.head.appendChild(style);

      document.body.appendChild(toastContainer);

      // Auto remove after 5 seconds
      setTimeout(() => {
        toastContainer.style.animation = 'slideOut 0.3s ease-in';
        setTimeout(() => {
          if (toastContainer.parentElement) {
            toastContainer.remove();
          }
        }, 300);
      }, 5000);

      console.log('Custom toast shown');
    }

    // Form validation and submission
    document.getElementById('kecamatanForm').addEventListener('submit', function(e) {
      e.preventDefault();

      const form = e.target;
      const namaKecamatan = form.nama_kecamatan.value.trim();

      // Reset validation states
      form.classList.remove('was-validated');
      form.nama_kecamatan.classList.remove('is-invalid');

      // Validate
      let isValid = true;

      if (!namaKecamatan) {
        form.nama_kecamatan.classList.add('is-invalid');
        isValid = false;
      }

      if (isValid) {
        // Show loading
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';

        // Create form data
        const formData = new FormData(form);

        // Send via AJAX
        fetch(form.action, {
          method: 'POST',
          body: formData,
          headers: {
            'X-Requested-With': 'XMLHttpRequest'
          }
        })
        .then(response => {
          console.log('Response status:', response.status);
          if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
          }
          return response.json();
        })
        .then(data => {
          console.log('Response data:', data); // Debug log
          if (data.success) {
            showNotification(data.message, 'success');
            setTimeout(() => {
              window.location.href = '../views/wilayah/index-kecamatan.php';
            }, 1500);
          } else {
            showNotification(data.message, 'error');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
          }
        })
        .catch(error => {
          console.error('Fetch Error:', error);
          showNotification('Terjadi kesalahan saat menyimpan data: ' + error.message, 'error');
          submitBtn.disabled = false;
          submitBtn.innerHTML = originalText;
        });
      } else {
        form.classList.add('was-validated');
      }
    });

    // Capitalize input
    document.getElementById('nama_kecamatan').addEventListener('input', function(e) {
      let value = e.target.value;
      // Capitalize first letter of each word
      value = value.replace(/\b\w/g, l => l.toUpperCase());
      e.target.value = value;
    });

    // Show toast on page load if there are session messages
    <?php if (isset($_SESSION['error'])): ?>
      showNotification('<?php echo addslashes($_SESSION['error']); ?>', 'error');
      <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

      </script>
</body>
</html>