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

              <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                  <li class="breadcrumb-item"><a href="<?= route('Dashboard', 'admin') ?>"><i class="fa-solid fa-house"></i> Dashboard</a></li>
                  <li class="breadcrumb-item"><a href="<?= route('desa', 'index') ?>">Desa</a></li>
                  <li class="breadcrumb-item active" aria-current="page"><?php echo $desa ? 'Edit' : 'Tambah'; ?></li>
                </ol>
              </nav>

              <div class="page-header d-flex justify-content-between align-items-center mb-4">
                <div>
                  <h3 class="page-title mb-0"><?php echo $desa ? 'Edit Desa / Kelurahan' : 'Tambah Desa / Kelurahan'; ?></h3>
                </div>
                <a href="<?= route('desa', 'index') ?>" class="btn btn-outline-secondary">
                  <i class="mdi mdi-arrow-left me-2"></i> Kembali
                </a>
              </div>

              <div class="card border-0 shadow-sm col-md-10 mx-auto">
                <div class="card-header bg-transparent border-bottom py-3">
                  <h5 class="card-title mb-0 fw-bold">
                    <i class="mdi mdi-home-map-marker text-success me-2"></i>
                    Form <?php echo $desa ? 'Edit' : 'Tambah'; ?> Desa / Kelurahan
                  </h5>
                </div>
                <div class="card-body p-4">
                  <form id="desaForm" method="POST" action="<?= $desa ? route('desa', 'save', ['id' => $desa['id_desa']]) : route('desa', 'save') ?>">
                    <?php if ($desa): ?>
                      <input type="hidden" name="_method" value="PUT">
                    <?php endif; ?>
                    <input type="hidden" name="id_desa" value="<?php echo $desa ? $desa['id_desa'] : ''; ?>">

                    <div class="row mb-4">
                      <div class="col-md-6 mb-3 mb-md-0">
                        <label for="id_kecamatan" class="form-label fw-semibold text-dark mb-2">
                          Kecamatan <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                          <span class="input-group-text bg-transparent border-end-0 text-muted">
                            <i class="mdi mdi-map-marker-multiple"></i>
                          </span>
                          <select class="form-select border-start-0 ps-0" id="id_kecamatan" name="id_kecamatan" required>
                            <option value="">-- Pilih Kecamatan --</option>
                            <?php foreach ($kecamatanOptions as $kecamatan): ?>
                              <option value="<?php echo $kecamatan['id_kecamatan']; ?>"
                                      <?php echo ($desa && $desa['id_kecamatan'] == $kecamatan['id_kecamatan']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($kecamatan['nama_kecamatan']); ?>
                              </option>
                            <?php endforeach; ?>
                          </select>
                        </div>
                        <div class="invalid-feedback text-danger small mt-1" id="id_kecamatan-feedback" style="display: none;">
                          Kecamatan wajib dipilih
                        </div>
                      </div>
                      <div class="col-md-6">
                        <label for="nama_desa" class="form-label fw-semibold text-dark mb-2">
                          Nama Desa / Kelurahan <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                          <span class="input-group-text bg-transparent border-end-0 text-muted">
                            <i class="mdi mdi-home-map-marker"></i>
                          </span>
                          <input type="text" class="form-control border-start-0 ps-0" id="nama_desa"
                                 name="nama_desa"
                                 value="<?php echo $desa ? htmlspecialchars($desa['nama_desa']) : ''; ?>"
                                 placeholder="Masukkan nama desa" required>
                        </div>
                        <div class="invalid-feedback text-danger small mt-1" id="nama_desa-feedback" style="display: none;">
                          Nama desa wajib diisi
                        </div>
                        <div class="form-text text-muted mt-2">Format: Masukkan nama desa dengan huruf kapital di awal kata (contoh: Panyabungan Jae).</div>
                      </div>
                    </div>

                    <div class="form-actions d-flex justify-content-end gap-2 border-top pt-4">
                      <a href="<?= route('desa', 'index') ?>" class="btn btn-outline-secondary">
                        Batal
                      </a>
                      <button type="submit" class="btn btn-primary px-4 shadow-sm" id="btnSubmit">
                        <i class="mdi mdi-content-save me-2"></i>
                        <?php echo $desa ? 'Update' : 'Simpan'; ?>
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
    document.getElementById('desaForm').addEventListener('submit', function(e) {
      e.preventDefault();

      const form = e.target;
      const idKecamatan = form.id_kecamatan.value;
      const namaDesa = form.nama_desa.value.trim();
      const idKecamatanFeedback = document.getElementById('id_kecamatan-feedback');
      const namaDesaFeedback = document.getElementById('nama_desa-feedback');

      form.classList.remove('was-validated');
      form.id_kecamatan.classList.remove('is-invalid');
      form.nama_desa.classList.remove('is-invalid');
      idKecamatanFeedback.style.display = 'none';
      namaDesaFeedback.style.display = 'none';

      let isValid = true;

      if (!idKecamatan) {
        form.id_kecamatan.classList.add('is-invalid');
        idKecamatanFeedback.style.display = 'block';
        isValid = false;
      }

      if (!namaDesa) {
        form.nama_desa.classList.add('is-invalid');
        namaDesaFeedback.style.display = 'block';
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
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            showToast(data.message, 'success');
            setTimeout(() => {
              window.location.href = '<?= route('desa', 'index') ?>';
            }, 1200);
          } else {
            showToast(data.message, 'error');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
          }
        })
        .catch(error => {
          console.error('Error:', error);
          showToast('Terjadi kesalahan saat menyimpan data', 'error');
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
      showToast('<?php echo addslashes($_SESSION['error']); ?>', 'error');
      <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
  </script>
</body>
</html>
