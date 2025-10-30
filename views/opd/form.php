<?php include('template/header.php'); ?>

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
                    <i class="fas fa-<?php echo isset($opd) ? 'edit' : 'plus-circle'; ?>"></i>
                    <?php echo isset($opd) ? 'Edit OPD' : 'Tambah OPD Baru'; ?>
                  </h3>
                </div>
                <div>
                  <a href="index.php?controller=opd&action=index" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                  </a>
                </div>
              </div>

              <!-- Toast Notifications Container -->
              <div id="toastContainer" style="position: fixed; top: 20px; right: 20px; z-index: 9999;"></div>

              <!-- Form Container -->
              <div class="card border-0 shadow-sm">
                <div class="card-body">
                  <form method="POST" 
                        action="index.php?controller=opd&action=<?php echo isset($opd) ? 'update&id=' . $opd['id_opd'] : 'store'; ?>">
                    
                    <?php if (isset($opd)): ?>
                        <input type="hidden" name="id" value="<?php echo $opd['id_opd']; ?>">
                    <?php endif; ?>

                    <!-- Informasi OPD Section -->
                    <div class="mb-4">
                      <h4 class="border-bottom pb-2 mb-3">
                        <i class="fas fa-building"></i> Informasi OPD
                      </h4>
                      <div class="row">
                        <div class="col-md-12">
                          <div class="mb-3">
                            <label for="nama_opd" class="form-label">
                              Nama OPD/Instansi <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   class="form-control"
                                   id="nama_opd"
                                   name="nama_opd"
                                   placeholder="Masukkan nama OPD atau instansi"
                                   value="<?php echo isset($old_input['nama_opd']) ? htmlspecialchars($old_input['nama_opd']) : (isset($opd) ? htmlspecialchars($opd['nama_opd']) : ''); ?>"
                                   required
                                   maxlength="150">
                            <div class="form-text">Contoh: Dinas Pendidikan, Dinas Kesehatan, Badan Perencanaan Pembangunan Daerah, dll.</div>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="d-flex gap-2">
                      <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        <?php echo isset($opd) ? 'Update OPD' : 'Simpan OPD'; ?>
                      </button>
                      
                      <a href="index.php?controller=opd&action=index" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Batal
                      </a>
                      
                      <?php if (isset($opd)): ?>
                        <button type="button" class="btn btn-danger ms-auto" data-bs-toggle="modal" data-bs-target="#deleteModal">
                          <i class="fas fa-trash"></i> Hapus OPD
                        </button>
                      <?php endif; ?>
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

  <!-- Toast Notification Script -->
  <script>
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
      <button type="button" class="btn-close btn-close-white ms-2" onclick="removeToast('${toastId}')"></button>
    `;

    toastContainer.appendChild(toast);

    // Auto remove after 5 seconds
    setTimeout(() => {
      removeToast(toastId);
    }, 5000);
  }

  function removeToast(toastId) {
    const toast = document.getElementById(toastId);
    if (toast) {
      toast.style.animation = 'slideOutRight 0.3s ease-out';
      setTimeout(() => {
        toast.remove();
      }, 300);
    }
  }

  // Show toast notifications on page load
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

  // Add CSS animations
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
  `;
  document.head.appendChild(style);
  </script>

  <!-- Delete Confirmation Modal -->
  <?php if (isset($opd)): ?>
  <div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Konfirmasi Hapus</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <p>Apakah Anda yakin ingin menghapus OPD ini?</p>
          <p class="text-muted"><small>OPD yang dihapus tidak dapat dikembalikan.</small></p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <a href="index.php?controller=opd&action=delete&id=<?php echo $opd['id_opd']; ?>" class="btn btn-danger">
            Hapus
          </a>
        </div>
      </div>
    </div>
  </div>
  <?php endif; ?>
</body>

</html>