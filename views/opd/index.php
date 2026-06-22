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
                  <h3 class="page-title mb-1">Daftar OPD</h3>
                  <p class="text-muted small mb-0">Kelola unit Organisasi Perangkat Daerah di Kabupaten Mandailing Natal</p>
                </div>
                <div>
                  <a href="<?= route('opd', 'create') ?>" class="btn btn-primary shadow-sm">
                    <i class="fas fa-plus me-2"></i> Tambah OPD
                  </a>
                </div>
              </div>

              <div class="row mb-4">
                <div class="col-md-4">
                  <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                      <div class="d-flex align-items-center">
                        <div class="stat-icon stat-icon-info me-3">
                          <i class="fas fa-building"></i>
                        </div>
                        <div>
                          <p class="text-muted mb-1 fs-7 text-uppercase fw-semibold" style="letter-spacing: 0.5px; font-size: 11px;">Total OPD</p>
                          <h2 class="stat-number mb-0 fw-bold"><?php echo number_format($total); ?></h2>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                  <form method="GET" action="<?= route('opd', 'index') ?>">
                    <div class="row g-3">
                      <div class="col-md-9">
                        <div class="input-group">
                          <span class="input-group-text bg-transparent border-end-0"><i class="fas fa-search text-muted"></i></span>
                          <input type="text" class="form-control border-start-0 ps-0" name="search" placeholder="Cari nama OPD..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                        </div>
                      </div>
                      <div class="col-md-3">
                        <button type="submit" class="btn btn-primary w-100">Cari</button>
                      </div>
                    </div>
                  </form>
                </div>
              </div>

              <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-bottom py-3">
                  <h5 class="card-title mb-0 fw-bold">
                    <i class="fas fa-building text-info me-2"></i> Data Organisasi Perangkat Daerah
                  </h5>
                </div>
                <div class="card-body p-0">
                  <?php if (empty($opds)): ?>
                    <div class="text-center py-5">
                      <i class="fas fa-building text-muted mb-3" style="font-size: 3rem; display: block;"></i>
                      <h5 class="mt-3 text-muted">Belum ada OPD</h5>
                      <p class="text-muted small">Silakan tambahkan OPD baru terlebih dahulu</p>
                      <a href="<?= route('opd', 'create') ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i> Tambah OPD
                      </a>
                    </div>
                  <?php else: ?>
                    <div class="table-responsive">
                      <table class="table table-hover mb-0">
                        <thead>
                          <tr>
                            <th width="80" class="text-center">No</th>
                            <th>Nama OPD</th>
                            <th width="150" class="text-center">Aksi</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                          $no = ($page - 1) * $limit + 1;
                          foreach ($opds as $opd):
                          ?>
                          <tr>
                            <td class="text-center text-muted"><?php echo $no++; ?></td>
                            <td class="fw-semibold text-dark"><?php echo htmlspecialchars($opd['nama_opd']); ?></td>
                            <td class="text-center">
                              <div class="btn-group btn-group-sm" role="group">
                                <a href="<?= route('opd', 'edit', ['id' => $opd['id_opd']]) ?>" 
                                   class="btn btn-outline-primary" title="Edit">
                                  <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-outline-danger delete-btn" 
                                        onclick="deleteOPD(<?php echo $opd['id_opd']; ?>)"
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
                    <div class="p-4 border-top">
                      <nav aria-label="OPD pagination">
                        <ul class="pagination justify-content-center mb-0">
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
  <?php include 'views/layouts/admin-script.php'; ?>

  <script>
  function deleteOPD(id) {
    showConfirm('Apakah Anda yakin ingin menghapus OPD ini secara permanen?', function() {
      fetch('<?= route('opd', 'delete') ?>/' + id, {
        method: 'POST',
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: new URLSearchParams({
          _method: 'DELETE',
          id: id
        })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          showToast(data.message, 'success');
          setTimeout(() => {
            window.location.reload();
          }, 1000);
        } else {
          showToast(data.message, 'error');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        showToast('Terjadi kesalahan saat menghapus OPD', 'error');
      });
    });
  }

  document.addEventListener('DOMContentLoaded', function() {
    <?php if (isset($_SESSION['success'])): ?>
      showToast("<?php echo addslashes($_SESSION['success']); unset($_SESSION['success']); ?>", 'success');
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
      showToast("<?php echo addslashes($_SESSION['error']); unset($_SESSION['error']); ?>", 'error');
    <?php endif; ?>
  });
  </script>
</body>
</html>
