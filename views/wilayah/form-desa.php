<?php include('views/layouts/admin-header.php'); ?>

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

              <div class="page-header d-flex justify-content-between align-items-center mb-4">
                <div>
                  <h2 class="page-title"><?php echo $desa ? 'Edit Desa' : 'Tambah Desa'; ?></h2>
                </div>
                <a href="<?= route('desa', 'index') ?>" class="btn btn-secondary">
                  <i class="mdi mdi-arrow-left me-2"></i> Kembali
                </a>
              </div>

              <div class="card">
                <div class="card-header">
                  <h5 class="card-title mb-0">
                    <i class="mdi mdi-home-map-marker me-2"></i>
                    Form <?php echo $desa ? 'Edit' : 'Tambah'; ?> Desa
                  </h5>
                </div>
                <div class="card-body">
                  <form id="desaForm" method="POST" action="<?= route('desa', 'save') ?>">
                    <input type="hidden" name="id_desa" value="<?php echo $desa ? $desa['id_desa'] : ''; ?>">

                    <div class="row mb-3">
                      <div class="col-md-6">
                        <label for="id_kecamatan" class="form-label">
                          Kecamatan <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                          <span class="input-group-text">
                            <i class="mdi mdi-map-marker-multiple"></i>
                          </span>
                          <select class="form-select" id="id_kecamatan" name="id_kecamatan" required>
                            <option value="">-- Pilih Kecamatan --</option>
                            <?php foreach ($kecamatanOptions as $kecamatan): ?>
                              <option value="<?php echo $kecamatan['id_kecamatan']; ?>"
                                      <?php echo ($desa && $desa['id_kecamatan'] == $kecamatan['id_kecamatan']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($kecamatan['nama_kecamatan']); ?>
                              </option>
                            <?php endforeach; ?>
                          </select>
                        </div>
                        <div class="invalid-feedback">
                          Kecamatan wajib dipilih
                        </div>
                      </div>
                      <div class="col-md-6">
                        <label for="nama_desa" class="form-label">
                          Nama Desa <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                          <span class="input-group-text">
                            <i class="mdi mdi-home-map-marker"></i>
                          </span>
                          <input type="text" class="form-control" id="nama_desa"
                                 name="nama_desa"
                                 value="<?php echo $desa ? htmlspecialchars($desa['nama_desa']) : ''; ?>"
                                 placeholder="Masukkan nama desa" required>
                        </div>
                        <div class="invalid-feedback">
                          Nama desa wajib diisi
                        </div>
                        <small class="text-muted">Masukkan nama desa dengan huruf kapital di awal kata</small>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                          <i class="mdi mdi-content-save me-2"></i>
                          <?php echo $desa ? 'Update' : 'Simpan'; ?>
                        </button>
                        <a href="<?= route('desa', 'index') ?>"
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
  <?php include 'views/layouts/admin-script.php'; ?>

  <script>
    function showNotification(message, type = 'success') {
      console.log('Showing notification:', message, type);

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

    document.getElementById('desaForm').addEventListener('submit', function(e) {
      e.preventDefault();

      const form = e.target;
      const idKecamatan = form.id_kecamatan.value;
      const namaDesa = form.nama_desa.value.trim();

      form.classList.remove('was-validated');
      form.id_kecamatan.classList.remove('is-invalid');
      form.nama_desa.classList.remove('is-invalid');

      let isValid = true;

      if (!idKecamatan) {
        form.id_kecamatan.classList.add('is-invalid');
        isValid = false;
      }

      if (!namaDesa) {
        form.nama_desa.classList.add('is-invalid');
        isValid = false;
      }

      if (isValid) {
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';

        const formData = new FormData(form);

        fetch(form.action, {
          method: 'POST',
          body: formData,
          headers: {
            'X-Requested-With': 'XMLHttpRequest'
          }
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            showNotification(data.message, 'success');
            setTimeout(() => {
              window.location.href = '<?= route('desa', 'index') ?>';
            }, 1500);
          } else {
            showNotification(data.message, 'error');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
          }
        })
        .catch(error => {
          console.error('Error:', error);
          showNotification('Terjadi kesalahan saat menyimpan data', 'error');
          submitBtn.disabled = false;
          submitBtn.innerHTML = originalText;
        });
      } else {
        form.classList.add('was-validated');
      }
    });

    document.getElementById('nama_desa').addEventListener('input', function(e) {
      let value = e.target.value;
      value = value.replace(/\b\w/g, l => l.toUpperCase());
      e.target.value = value;
    });

    <?php if (isset($_SESSION['error'])): ?>
      showNotification('<?php echo addslashes($_SESSION['error']); ?>', 'error');
      <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
  </script>
</body>
</html>