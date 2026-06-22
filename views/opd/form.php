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
                  <li class="breadcrumb-item"><a href="<?= route('opd', 'index') ?>">OPD</a></li>
                  <li class="breadcrumb-item active" aria-current="page"><?php echo isset($opd) ? 'Edit' : 'Tambah'; ?></li>
                </ol>
              </nav>
              <div class="page-header d-flex justify-content-between align-items-center mb-4">
                <div>
                  <h3 class="page-title mb-0">
                    <i class="fas fa-<?php echo isset($opd) ? 'edit text-primary' : 'plus-circle text-primary'; ?> me-2"></i>
                    <?php echo isset($opd) ? 'Edit OPD' : 'Tambah OPD Baru'; ?>
                  </h3>
                </div>
                <div>
                  <a href="<?= route('opd', 'index') ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i> Kembali
                  </a>
                </div>
              </div>

              <div class="card border-0 shadow-sm col-md-8 mx-auto">
                <div class="card-header bg-transparent border-bottom py-3">
                  <h5 class="card-title mb-0 fw-bold">
                    <i class="fas fa-building text-primary me-2"></i> Informasi OPD
                  </h5>
                </div>
                <div class="card-body p-4">
                  <form method="POST" 
                        action="<?php echo isset($opd) ? route('opd', 'update', ['id' => $opd['id_opd']]) : route('opd', 'store'); ?>">
                    
                    <?php if (isset($opd)): ?>
                        <input type="hidden" name="_method" value="PUT">
                        <input type="hidden" name="id" value="<?php echo $opd['id_opd']; ?>">
                    <?php endif; ?>

                    <div class="mb-4">
                      <label for="nama_opd" class="form-label fw-semibold text-dark mb-2">
                        Nama OPD / Instansi <span class="text-danger">*</span>
                      </label>
                      <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0 text-muted"><i class="fas fa-building"></i></span>
                        <input type="text"
                               class="form-control border-start-0 ps-0"
                               id="nama_opd"
                               name="nama_opd"
                               placeholder="Masukkan nama OPD atau instansi"
                               value="<?php echo isset($old_input['nama_opd']) ? htmlspecialchars($old_input['nama_opd']) : (isset($opd) ? htmlspecialchars($opd['nama_opd']) : ''); ?>"
                               required
                               maxlength="150">
                      </div>
                      <div class="form-text text-muted mt-2">Contoh: Dinas Pendidikan, Dinas Kesehatan, Badan Perencanaan Pembangunan Daerah.</div>
                    </div>

                    <div class="form-actions d-flex justify-content-end align-items-center gap-2 border-top pt-4">
                      <?php if (isset($opd)): ?>
                        <button type="button" class="btn btn-outline-danger me-auto" onclick="confirmDeleteOPD()">
                          <i class="fas fa-trash me-2"></i> Hapus OPD
                        </button>
                      <?php endif; ?>

                      <a href="<?= route('opd', 'index') ?>" class="btn btn-outline-secondary">
                        Batal
                      </a>

                      <button type="submit" class="btn btn-primary px-4 shadow-sm">
                        <i class="fas fa-save me-2"></i>
                        <?php echo isset($opd) ? 'Update OPD' : 'Simpan OPD'; ?>
                      </button>
                    </div>
                  </form>

                  <?php if (isset($opd)): ?>
                    <form id="deleteForm" method="POST" action="<?= route('opd', 'delete', ['id' => $opd['id_opd']]) ?>" style="display: none;">
                      <input type="hidden" name="_method" value="DELETE">
                      <input type="hidden" name="id" value="<?php echo $opd['id_opd']; ?>">
                    </form>
                  <?php endif; ?>
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
  <?php if (isset($opd)): ?>
  function confirmDeleteOPD() {
    showConfirm('Apakah Anda yakin ingin menghapus OPD ini secara permanen?', function() {
      document.getElementById('deleteForm').submit();
    });
  }
  <?php endif; ?>

  document.addEventListener('DOMContentLoaded', function() {
    <?php if (isset($_SESSION['success'])): ?>
      showToast("<?php echo addslashes($_SESSION['success']); unset($_SESSION['success']); ?>", 'success');
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
      showToast("<?php echo addslashes($_SESSION['error']); unset($_SESSION['error']); ?>", 'error');
    <?php endif; ?>

    <?php if (isset($_SESSION['errors'])): ?>
      <?php
      $errorMessages = array_map('addslashes', $_SESSION['errors']);
      $errorText = 'Kesalahan: ' . implode(', ', $errorMessages);
      unset($_SESSION['errors']);
      ?>
      showToast("<?php echo $errorText; ?>", 'error');
    <?php endif; ?>
  });
  </script>
</body>
</html>
