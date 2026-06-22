<?php include('views/layouts/admin-header.php'); ?>

<style>
  .toast-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 1055;
  }

  .toast {
    min-width: 250px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  }

  .toast.success {
    background-color: #10b981;
    border: 1px solid #10b981;
  }

  .toast.error {
    background-color: #ef4444;
    border: 1px solid #ef4444;
  }

  .position-fixed {
    z-index: 1055 !important;
  }
</style>

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

              <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                  <li class="breadcrumb-item"><a href="<?= route('Dashboard', 'admin') ?>"><i class="fa-solid fa-house"></i> Dashboard</a></li>
                  <li class="breadcrumb-item"><a href="<?= route('kecamatan', 'index') ?>">Kecamatan</a></li>
                  <li class="breadcrumb-item active" aria-current="page"><?php echo $kecamatan ? 'Edit' : 'Tambah'; ?></li>
                </ol>
              </nav>

              <div class="page-header d-flex justify-content-between align-items-center mb-4">
                <div>
                  <h3 class="page-title mb-0"><?php echo $kecamatan ? 'Edit Kecamatan' : 'Tambah Kecamatan'; ?></h3>
                </div>
                <a href="<?= route('kecamatan', 'index') ?>" class="btn btn-outline-secondary">
                  <i class="mdi mdi-arrow-left me-2"></i> Kembali
                </a>
              </div>

              <div class="card border-0 shadow-sm col-md-8 mx-auto">
                <div class="card-header bg-transparent border-bottom py-3">
                  <h5 class="card-title mb-0 fw-bold">
                    <i class="mdi mdi-map-marker text-primary me-2"></i>
                    Form <?php echo $kecamatan ? 'Edit' : 'Tambah'; ?> Kecamatan
                  </h5>
                </div>
                <div class="card-body p-4">
                  <form id="kecamatanForm" method="POST" action="<?= $kecamatan ? route('kecamatan', 'save', ['id' => $kecamatan['id_kecamatan']]) : route('kecamatan', 'save') ?>">
                    <?php if ($kecamatan): ?>
                      <input type="hidden" name="_method" value="PUT">
                    <?php endif; ?>
                    <input type="hidden" name="id_kecamatan" value="<?php echo $kecamatan ? $kecamatan['id_kecamatan'] : ''; ?>">

                    <div class="mb-4">
                      <label for="nama_kecamatan" class="form-label fw-semibold text-dark mb-2">
                        Nama Kecamatan <span class="text-danger">*</span>
                      </label>
                      <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0 text-muted">
                          <i class="mdi mdi-map-marker"></i>
                        </span>
                        <input type="text" class="form-control border-start-0 ps-0" id="nama_kecamatan"
                          name="nama_kecamatan"
                          value="<?php echo $kecamatan ? htmlspecialchars($kecamatan['nama_kecamatan']) : ''; ?>"
                          placeholder="Masukkan nama kecamatan" required>
                      </div>
                      <div class="invalid-feedback text-danger small mt-1" id="nama_kecamatan-feedback" style="display: none;">
                        Nama kecamatan wajib diisi
                      </div>
                      <div class="form-text text-muted mt-2">Format: Masukkan nama kecamatan dengan huruf kapital di awal kata (contoh: Panyabungan).</div>
                    </div>

                    <div class="form-actions d-flex justify-content-end gap-2 border-top pt-4">
                      <a href="<?= route('kecamatan', 'index') ?>" class="btn btn-outline-secondary">
                        Batal
                      </a>
                      <button type="submit" class="btn btn-primary px-4 shadow-sm" id="btnSubmit">
                        <i class="mdi mdi-content-save me-2"></i>
                        <?php echo $kecamatan ? 'Update' : 'Simpan'; ?>
                      </button>
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
    document.getElementById('kecamatanForm').addEventListener('submit', function(e) {
      e.preventDefault();

      const form = e.target;
      const namaKecamatan = form.nama_kecamatan.value.trim();
      const feedbackEl = document.getElementById('nama_kecamatan-feedback');

      form.classList.remove('was-validated');
      form.nama_kecamatan.classList.remove('is-invalid');
      feedbackEl.style.display = 'none';

      let isValid = true;

      if (!namaKecamatan) {
        form.nama_kecamatan.classList.add('is-invalid');
        feedbackEl.style.display = 'block';
        isValid = false;
      }

      if (isValid) {
        const submitBtn = document.getElementById('btnSubmit');
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
          .then(response => {
            if (!response.ok) {
              throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
          })
          .then(data => {
            if (data.success) {
              showToast(data.message, 'success');
              setTimeout(() => {
                window.location.href = '<?= route('kecamatan', 'index') ?>';
              }, 1200);
            } else {
              showToast(data.message, 'error');
              submitBtn.disabled = false;
              submitBtn.innerHTML = originalText;
            }
          })
          .catch(error => {
            console.error('Fetch Error:', error);
            showToast('Terjadi kesalahan saat menyimpan data: ' + error.message, 'error');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
          });
      } else {
        form.classList.add('was-validated');
      }
    });

    document.getElementById('nama_kecamatan').addEventListener('input', function(e) {
      let value = e.target.value;
      value = value.replace(/\b\w/g, l => l.toUpperCase());
      e.target.value = value;
    });

    <?php if (isset($_SESSION['error'])): ?>
      showToast('<?php echo addslashes($_SESSION['error']); ?>', 'error');
      <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
  </script>
</body>

</html>
