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
              <div class="page-header d-flex justify-content-between align-items-center mb-4">
                <div>
                  <h2 class="page-title">Manajemen Desa</h2>
                  <p class="text-muted mb-0">Kelola data desa</p>
                </div>
                <div>
                  <a href="index.php?controller=desa&action=form" class="btn btn-primary">
                    <i class="mdi mdi-plus-circle me-2"></i> Tambah Desa
                  </a>
                </div>
              </div>

              <!-- Statistics Card -->
              <div class="row mb-4">
                <div class="col-md-4">
                  <div class="card bg-success text-white">
                    <div class="card-body">
                      <div class="d-flex justify-content-between align-items-center">
                        <div>
                          <h6 class="card-title mb-1">Total Desa</h6>
                          <h2 class="mb-0"><?php echo number_format($statistics['total_desa']); ?></h2>
                        </div>
                        <div class="fs-1 opacity-75">
                          <i class="mdi mdi-home-map-marker"></i>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Search and Filter Card -->
              <div class="card mb-4">
                <div class="card-body">
                  <form method="GET" class="row g-3">
                    <input type="hidden" name="controller" value="desa">
                    <input type="hidden" name="action" value="index">
                    <div class="col-md-4">
                      <input type="text" class="form-control" name="search"
                             placeholder="Cari nama desa..." value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <div class="col-md-4">
                      <select class="form-select" name="kecamatan_filter">
                        <option value="">Semua Kecamatan</option>
                        <?php foreach ($kecamatanOptions as $kecamatan): ?>
                          <option value="<?php echo $kecamatan['id_kecamatan']; ?>"
                                  <?php echo (isset($_GET['kecamatan_filter']) && $_GET['kecamatan_filter'] == $kecamatan['id_kecamatan']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($kecamatan['nama_kecamatan']); ?>
                          </option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                    <div class="col-md-4">
                      <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="mdi mdi-magnify me-2"></i> Cari
                      </button>
                    </div>
                  </form>
                </div>
              </div>

              <!-- Main Table Card -->
              <div class="card">
                <div class="card-header">
                  <h5 class="card-title mb-0">
                    <i class="mdi mdi-home-map-marker me-2"></i> Data Desa
                  </h5>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table table-striped table-hover">
                      <thead class="table-dark">
                        <tr>
                          <th width="50">No</th>
                          <th>Nama Desa</th>
                          <th>Nama Kecamatan</th>
                          <th width="100" class="text-center">Aksi</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php if (empty($desaData)): ?>
                          <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                              <i class="mdi mdi-information-outline me-2"></i> Tidak ada data desa
                            </td>
                          </tr>
                        <?php else: ?>
                          <?php $no = ($currentPage - 1) * $limit + 1; ?>
                          <?php foreach ($desaData as $desa): ?>
                            <tr>
                              <td><?php echo $no++; ?></td>
                              <td><?php echo htmlspecialchars($desa['nama_desa']); ?></td>
                              <td><?php echo htmlspecialchars($desa['nama_kecamatan']); ?></td>
                              <td class="text-center">
                                <div class="btn-group" role="group">
                                  <a href="index.php?controller=desa&action=form&id=<?php echo $desa['id_desa']; ?>"
                                     class="btn btn-sm btn-warning"
                                     title="Edit">
                                    <i class="mdi mdi-pencil"></i>
                                  </a>
                                  <button type="button" class="btn btn-sm btn-danger"
                                          onclick="deleteDesa(<?php echo $desa['id_desa']; ?>)"
                                          title="Hapus">
                                    <i class="mdi mdi-delete"></i>
                                  </button>
                                </div>
                              </td>
                            </tr>
                          <?php endforeach; ?>
                        <?php endif; ?>
                      </tbody>
                    </table>
                  </div>

                  <!-- Pagination -->
                  <?php if ($totalPages > 1): ?>
                    <nav aria-label="Page navigation" class="mt-4">
                      <ul class="pagination justify-content-center">
                        <?php if ($currentPage > 1): ?>
                          <li class="page-item">
                            <a class="page-link" href="?controller=desa&action=index&page=<?php echo $currentPage - 1; ?>&search=<?php echo urlencode($search); ?>&kecamatan_filter=<?php echo urlencode($_GET['kecamatan_filter'] ?? ''); ?>">
                              <i class="mdi mdi-chevron-left"></i> Previous
                            </a>
                          </li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                          <li class="page-item <?php echo $i == $currentPage ? 'active' : ''; ?>">
                            <a class="page-link" href="?controller=desa&action=index&page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&kecamatan_filter=<?php echo urlencode($_GET['kecamatan_filter'] ?? ''); ?>"><?php echo $i; ?></a>
                          </li>
                        <?php endfor; ?>

                        <?php if ($currentPage < $totalPages): ?>
                          <li class="page-item">
                            <a class="page-link" href="?controller=desa&action=index&page=<?php echo $currentPage + 1; ?>&search=<?php echo urlencode($search); ?>&kecamatan_filter=<?php echo urlencode($_GET['kecamatan_filter'] ?? ''); ?>">
                              Next <i class="mdi mdi-chevron-right"></i>
                            </a>
                          </li>
                        <?php endif; ?>
                      </ul>
                    </nav>
                  <?php endif; ?>
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
          from { transform: translateX(100%); opacity: 0; }
          to { transform: translateX(0); opacity: 1; }
        }
        @keyframes slideOut {
          from { transform: translateX(0); opacity: 1; }
          to { transform: translateX(100%); opacity: 0; }
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
    }

    // Delete desa
    function deleteDesa(id) {
      if (confirm('Apakah Anda yakin ingin menghapus desa ini?')) {
        fetch('index.php?controller=desa&action=delete', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: 'id=' + encodeURIComponent(id)
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            showNotification(data.message, 'success');
            setTimeout(() => {
              window.location.reload();
            }, 1000);
          } else {
            showNotification(data.message, 'error');
          }
        })
        .catch(error => {
          console.error('Error:', error);
          showNotification('Terjadi kesalahan saat menghapus data', 'error');
        });
      }
    }

    // Show toast on page load if there are session messages
    <?php if (isset($_SESSION['error'])): ?>
      showNotification('<?php echo addslashes($_SESSION['error']); ?>', 'error');
      <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
      showNotification('<?php echo addslashes($_SESSION['success']); ?>', 'success');
      <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
  </script>
</body>
</html>