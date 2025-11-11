<?php
$title = 'Manajemen Profile - ';
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
                    <i class="fas fa-users-cog"></i>
                    Manajemen Profile
                  </h3>
                  <p class="text-muted mb-0">Kelola konfigurasi profile aplikasi untuk setiap role</p>
                </div>
                <div>
                  <a href="index.php?controller=profile&action=create" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Profile
                  </a>
                </div>
              </div>

              <!-- Search and Filter Section -->
              <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                  <form method="GET" action="index.php" class="row g-3">
                    <input type="hidden" name="controller" value="profile">
                    <input type="hidden" name="action" value="index">

                    <div class="col-md-5">
                      <label for="search" class="form-label">Cari Profile</label>
                      <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" id="search" name="search"
                               placeholder="Masukkan nama aplikasi..."
                               value="<?php echo htmlspecialchars($search); ?>">
                      </div>
                    </div>

                    <div class="col-md-3">
                      <label for="role" class="form-label">Filter Role</label>
                      <select class="form-select" id="role" name="role">
                        <option value="">Semua Role</option>
                        <option value="camat" <?php echo $role_filter === 'camat' ? 'selected' : ''; ?>>Camat</option>
                        <option value="opd" <?php echo $role_filter === 'opd' ? 'selected' : ''; ?>>OPD</option>
                      </select>
                    </div>

                    <div class="col-md-4 d-flex align-items-end">
                      <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search"></i> Cari
                      </button>
                      <a href="index.php?controller=profile&action=index" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> Reset
                      </a>
                    </div>
                  </form>
                </div>
              </div>

              <!-- Table Section -->
              <div class="card border-0 shadow-sm">
                <div class="card-body">
                  <?php if (empty($profiles)): ?>
                    <div class="text-center py-5">
                      <i class="fas fa-users-cog text-muted" style="font-size: 3rem;"></i>
                      <h5 class="mt-3 text-muted">Belum ada Profile</h5>
                      <p class="text-muted">Tambahkan profile pertama untuk memulai</p>
                      <a href="index.php?controller=profile&action=create" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Tambah Profile
                      </a>
                    </div>
                  <?php else: ?>
                    <div class="table-responsive">
                      <table class="table table-hover">
                        <thead>
                          <tr>
                            <th>No</th>
                            <th>Nama Aplikasi</th>
                            <th>Role</th>
                            <th>Aksi</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                          $no = $offset + 1;
                          foreach ($profiles as $profile):
                          ?>
                          <tr>
                            <td><?php echo $no++; ?></td>
                            <td>
                              <div class="d-flex align-items-center">
                                <div class="me-3">
                                  <?php
                                  $logoPath = !empty($profile['logo']) ? $profile['logo'] : '';
                                  $logoExists = !empty($logoPath) && file_exists($logoPath);
                                  ?>
                                  <?php if ($logoExists): ?>
                                    <?php
                                    // Check if file is readable
                                    $isReadable = is_readable($logoPath);
                                    $fileSize = filesize($logoPath);
                                    ?>
                                    <?php if ($isReadable): ?>
                                      <img src="<?php echo htmlspecialchars($logoPath); ?>"
                                           alt="<?php echo htmlspecialchars($profile['nama_aplikasi']); ?>"
                                           style="width: 40px; height: 40px; object-fit: cover; border-radius: 50%; border: 2px solid #e3e6f0;"
                                           onerror="handleImageError(this, '<?php echo addslashes(strtoupper(substr($profile['nama_aplikasi'], 0, 2))); ?>')">
                                    <?php else: ?>
                                      <div class="bg-gradient-warning rounded-circle d-flex align-items-center justify-content-center text-white"
                                           style="width: 40px; height: 40px; font-size: 14px; font-weight: bold;"
                                           title="File tidak dapat dibaca: <?php echo htmlspecialchars($logoPath); ?>">
                                        ?
                                      </div>
                                    <?php endif; ?>
                                  <?php else: ?>
                                    <div class="bg-gradient-primary rounded-circle d-flex align-items-center justify-content-center text-white"
                                         style="width: 40px; height: 40px; font-size: 14px; font-weight: bold;">
                                      <?php echo strtoupper(substr($profile['nama_aplikasi'], 0, 2)); ?>
                                    </div>
                                  <?php endif; ?>
                                </div>
                                <div class="fw-medium"><?php echo htmlspecialchars($profile['nama_aplikasi']); ?></div>
                              </div>
                            </td>
                            <td>
                              <?php if ($profile['role'] === 'camat'): ?>
                                <span class="badge bg-info">Camat</span>
                              <?php elseif ($profile['role'] === 'opd'): ?>
                                <span class="badge bg-warning">OPD</span>
                              <?php else: ?>
                                <span class="badge bg-secondary"><?php echo htmlspecialchars($profile['role']); ?></span>
                              <?php endif; ?>
                            </td>
                            <td>
                              <div class="btn-group" role="group">
                                <a href="index.php?controller=profile&action=edit&id=<?php echo $profile['id_profile']; ?>"
                                   class="btn btn-sm btn-outline-primary"
                                   title="Edit Profile">
                                  <i class="fas fa-edit"></i>
                                </a>
                                <button type="button"
                                        class="btn btn-sm btn-outline-danger"
                                        onclick="confirmDelete(<?php echo $profile['id_profile']; ?>, '<?php echo addslashes($profile['nama_aplikasi']); ?>')"
                                        title="Hapus Profile">
                                  <i class="fas fa-trash"></i>
                                </button>
                              </div>
                            </td>
                          </tr>
                          <?php endforeach; ?>
                        </tbody>
                      </table>
                    </div>

                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                      <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted">
                          Menampilkan <?php echo $offset + 1; ?> - <?php echo min($offset + $limit, $total); ?> dari <?php echo $total; ?> profile
                        </div>
                        <nav>
                          <ul class="pagination pagination-sm mb-0">
                            <?php if ($page > 1): ?>
                              <li class="page-item">
                                <a class="page-link" href="?controller=profile&action=index&page=<?php echo $page - 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?><?php echo !empty($role_filter) ? '&role=' . urlencode($role_filter) : ''; ?>">
                                  <i class="fas fa-chevron-left"></i>
                                </a>
                              </li>
                            <?php endif; ?>

                            <?php
                            $start_page = max(1, $page - 2);
                            $end_page = min($total_pages, $page + 2);

                            if ($start_page > 1) {
                              echo '<li class="page-item"><a class="page-link" href="?controller=profile&action=index&page=1' . (!empty($search) ? '&search=' . urlencode($search) : '') . (!empty($role_filter) ? '&role=' . urlencode($role_filter) : '') . '">1</a></li>';
                              if ($start_page > 2) {
                                echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                              }
                            }

                            for ($i = $start_page; $i <= $end_page; $i++) {
                              $active = $i == $page ? 'active' : '';
                              echo '<li class="page-item ' . $active . '"><a class="page-link" href="?controller=profile&action=index&page=' . $i . (!empty($search) ? '&search=' . urlencode($search) : '') . (!empty($role_filter) ? '&role=' . urlencode($role_filter) : '') . '">' . $i . '</a></li>';
                            }

                            if ($end_page < $total_pages) {
                              if ($end_page < $total_pages - 1) {
                                echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                              }
                              echo '<li class="page-item"><a class="page-link" href="?controller=profile&action=index&page=' . $total_pages . (!empty($search) ? '&search=' . urlencode($search) : '') . (!empty($role_filter) ? '&role=' . urlencode($role_filter) : '') . '">' . $total_pages . '</a></li>';
                            }
                            ?>

                            <?php if ($page < $total_pages): ?>
                              <li class="page-item">
                                <a class="page-link" href="?controller=profile&action=index&page=<?php echo $page + 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?><?php echo !empty($role_filter) ? '&role=' . urlencode($role_filter) : ''; ?>">
                                  <i class="fas fa-chevron-right"></i>
                                </a>
                              </li>
                            <?php endif; ?>
                          </ul>
                        </nav>
                      </div>
                    <?php endif; ?>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Toast Notifications Container -->
  <div id="toastContainer" style="position: fixed; top: 20px; right: 20px; z-index: 9999;"></div>

  <!-- Delete Confirmation Modal -->
  <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="deleteModalLabel">
            <i class="fas fa-exclamation-triangle text-danger me-2"></i>
            Konfirmasi Hapus
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p>Apakah Anda yakin ingin menghapus profile <strong id="deleteProfileName"></strong>?</p>
          <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Peringatan:</strong> Tindakan ini tidak dapat dibatalkan.
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fas fa-times me-2"></i> Batal
          </button>
          <form id="deleteForm" method="POST" style="display: inline;">
            <input type="hidden" name="id" id="deleteId">
            <button type="submit" class="btn btn-danger">
              <i class="fas fa-trash me-2"></i> Hapus
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <?php include 'template/script.php'; ?>

  <script>
  document.addEventListener('DOMContentLoaded', function() {
    // Function to handle image loading errors
    function handleImageError(img, initials) {
      img.onerror = null;
      img.src = '';
      img.style.display = 'none';

      // Create fallback div with initials
      const fallbackDiv = document.createElement('div');
      fallbackDiv.className = 'bg-gradient-primary rounded-circle d-flex align-items-center justify-content-center text-white';
      fallbackDiv.style.cssText = 'width: 40px; height: 40px; font-size: 14px; font-weight: bold;';
      fallbackDiv.textContent = initials;

      // Replace the image with the fallback
      img.parentElement.appendChild(fallbackDiv);
    }

    // Make function globally available
    window.handleImageError = handleImageError;

    // Show toast notifications
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

    // Show session messages
    <?php if (isset($_SESSION['success'])): ?>
      showToast("<?php echo addslashes($_SESSION['success']); unset($_SESSION['success']); ?>", 'success');
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
      showToast("<?php echo addslashes($_SESSION['error']); unset($_SESSION['error']); ?>", 'error');
    <?php endif; ?>

    // Delete confirmation
    function confirmDelete(id, name) {
      document.getElementById('deleteId').value = id;
      document.getElementById('deleteProfileName').textContent = name;
      document.getElementById('deleteForm').action = 'index.php?controller=profile&action=delete';

      const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
      modal.show();
    }

    // Make function globally available
    window.confirmDelete = confirmDelete;

    // CSS animations
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
  });
  </script>
</body>
</html>