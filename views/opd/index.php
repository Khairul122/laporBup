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
              
              <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                  <h3 class="page-title mb-1">Daftar OPD</h3>
                </div>
                <div>
                  <a href="<?= route('opd', 'create') ?>" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah OPD
                  </a>
                </div>
              </div>

              
              <div id="toastContainer" style="position: fixed; top: 20px; right: 20px; z-index: 9999;"></div>

              
              <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                  <form method="GET" action="<?= route('opd', 'index') ?>">
                    <div class="row g-3">
                      <div class="col-md-9">
                        <div class="input-group">
                          <span class="input-group-text"><i class="fas fa-search"></i></span>
                          <input type="text" class="form-control" name="search" placeholder="Cari nama OPD..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                        </div>
                      </div>
                      <div class="col-md-3">
                        <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search"></i> Cari</button>
                      </div>
                    </div>
                  </form>
                </div>
              </div>

              
              <div class="card border-0 shadow-sm">
                <div class="card-body">
                  <?php if (empty($opds)): ?>
                    <div class="text-center py-5">
                      <i class="fas fa-building text-muted" style="font-size: 3rem;"></i>
                      <h5 class="mt-3 text-muted">Belum ada OPD</h5>
                      <p class="text-muted">Tambahkan OPD pertama Anda</p>
                      <a href="<?= route('opd', 'create') ?>" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Tambah OPD
                      </a>
                    </div>
                  <?php else: ?>
                    <div class="table-responsive">
                      <table class="table table-hover">
                        <thead>
                          <tr>
                            <th>No</th>
                            <th>Nama OPD</th>
                            <th>Aksi</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                          $no = ($page - 1) * $limit + 1;
                          foreach ($opds as $opd):
                          ?>
                          <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo htmlspecialchars($opd['nama_opd']); ?></td>
                            <td>
                              <div class="btn-group btn-group-sm">
                                <a href="<?= route('opd', 'edit', ['id' => $opd['id_opd']]) ?>" 
                                   class="btn btn-outline-primary" title="Edit">
                                  <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-outline-danger delete-btn" 
                                        data-id="<?php echo $opd['id_opd']; ?>" 
                                        title="Hapus">
                                  <i class="fas fa-trash"></i>
                                </button>
                              </div>
                            </td>
                          </tr>
                          <?php endforeach; ?>
                        </tbody>
                      </table>
                    </div>

                    
                    <?php if ($total_pages > 1): ?>
                    <nav aria-label="OPD pagination" class="mt-4">
                      <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                          <li class="page-item">
                            <a class="page-link" href="<?= route('opd', 'index') ?>?page=<?php echo $page - 1; ?><?php echo isset($_GET['search']) && $_GET['search'] ? '&search=' . urlencode($_GET['search']) : ''; ?>">Sebelumnya</a>
                          </li>
                        <?php endif; ?>

                        <?php
                        $start = max(1, $page - 2);
                        $end = min($total_pages, $page + 2);
                        ?>

                        <?php for ($i = $start; $i <= $end; $i++): ?>
                          <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                            <a class="page-link" href="<?= route('opd', 'index') ?>?page=<?php echo $i; ?><?php echo isset($_GET['search']) && $_GET['search'] ? '&search=' . urlencode($_GET['search']) : ''; ?>"><?php echo $i; ?></a>
                          </li>
                        <?php endfor; ?>

                        <?php if ($page < $total_pages): ?>
                          <li class="page-item">
                            <a class="page-link" href="<?= route('opd', 'index') ?>?page=<?php echo $page + 1; ?><?php echo isset($_GET['search']) && $_GET['search'] ? '&search=' . urlencode($_GET['search']) : ''; ?>">Selanjutnya</a>
                          </li>
                        <?php endif; ?>
                      </ul>
                    </nav>
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
  <?php include 'views/layouts/admin-script.php'; ?>

  
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

  
  document.addEventListener('DOMContentLoaded', function() {
    <?php if (isset($_SESSION['success'])): ?>
      showToast("<?php echo addslashes($_SESSION['success']); unset($_SESSION['success']); ?>", 'success');
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
      showToast("<?php echo addslashes($_SESSION['error']); unset($_SESSION['error']); ?>", 'error');
    <?php endif; ?>
  });

  
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
          <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Hapus</button>
        </div>
      </div>
    </div>
  </div>

  <script>
  document.addEventListener('DOMContentLoaded', function() {
    let deleteId = null;

    
    document.querySelectorAll('.delete-btn').forEach(button => {
      button.addEventListener('click', function() {
        deleteId = this.getAttribute('data-id');
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
      });
    });

    
    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
      if (deleteId) {
        
        const modal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
        modal.hide();

        
        fetch('<?= route('opd', 'delete') ?>/' + deleteId, {
          method: 'POST',
          headers: {
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: new URLSearchParams({
            _method: 'DELETE',
            id: deleteId
          })
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            
            showToast(data.message, 'success');

            
            const row = document.querySelector(`[data-id="${deleteId}"]`).closest('tr');
            if (row) {
              row.style.animation = 'fadeOut 0.3s ease-out';
              setTimeout(() => {
                row.remove();

                
                const tbody = document.querySelector('tbody');
                if (tbody && tbody.children.length === 0) {
                  
                  const tableContainer = document.querySelector('.table-responsive');
                  tableContainer.innerHTML = `
                    <div class="text-center py-5">
                      <i class="fas fa-building text-muted" style="font-size: 3rem;"></i>
                      <h5 class="mt-3 text-muted">Belum ada OPD</h5>
                      <p class="text-muted">Tambahkan OPD pertama Anda</p>
                      <a href="<?= route('opd', 'create') ?>" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Tambah OPD
                      </a>
                    </div>
                  `;
                }
              }, 300);
            }
          } else {
            
            showToast(data.message, 'error');
          }
        })
        .catch(error => {
          console.error('Error:', error);
          showToast('Terjadi kesalahan saat menghapus OPD', 'error');
        });
      }
    });
  });

  
  const style = document.createElement('style');
  style.textContent = `
    @keyframes fadeOut {
      from {
        opacity: 1;
        transform: translateX(0);
      }
      to {
        opacity: 0;
        transform: translateX(-20px);
      }
    }
  `;
  document.head.appendChild(style);
  </script>
</body>

</html>
