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
                  <h3 class="page-title mb-1">Manajemen Kecamatan</h3>
                  <p class="text-muted small mb-0">Kelola unit wilayah kecamatan di Kabupaten Mandailing Natal</p>
                </div>
                <div>
                  <a href="<?= route('kecamatan', 'form') ?>" class="btn btn-primary shadow-sm">
                    <i class="mdi mdi-plus-circle me-2"></i> Tambah Kecamatan
                  </a>
                </div>
              </div>

              <div class="row mb-4">
                <div class="col-md-4">
                  <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                      <div class="d-flex align-items-center">
                        <div class="stat-icon stat-icon-primary me-3">
                          <i class="mdi mdi-map-marker-multiple"></i>
                        </div>
                        <div>
                          <p class="text-muted mb-1 fs-7 text-uppercase fw-semibold" style="letter-spacing: 0.5px; font-size: 11px;">Total Kecamatan</p>
                          <h2 class="stat-number mb-0 fw-bold"><?php echo number_format($statistics['total_kecamatan']); ?></h2>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                  <form method="GET" action="<?= route('kecamatan', 'index') ?>" class="row g-3 align-items-center">
                    <div class="col-md-9">
                      <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0"><i class="mdi mdi-magnify text-muted"></i></span>
                        <input type="text" class="form-control border-start-0 ps-0" name="search"
                               placeholder="Cari nama kecamatan..." value="<?php echo htmlspecialchars($search); ?>">
                      </div>
                    </div>
                    <div class="col-md-3">
                      <button type="submit" class="btn btn-primary w-100">
                        Cari
                      </button>
                    </div>
                  </form>
                </div>
              </div>

              <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-bottom py-3">
                  <h5 class="card-title mb-0 fw-bold">
                    <i class="mdi mdi-map-marker-multiple text-primary me-2"></i> Data Kecamatan
                  </h5>
                </div>
                <div class="card-body p-0">
                  <div class="table-responsive">
                    <table class="table table-hover mb-0">
                      <thead>
                        <tr>
                          <th width="80" class="text-center">No</th>
                          <th>Nama Kecamatan</th>
                          <th width="150" class="text-center">Aksi</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php if (empty($kecamatanData)): ?>
                          <tr>
                            <td colspan="3" class="text-center text-muted py-5">
                              <div class="py-3">
                                <i class="mdi mdi-information-outline text-muted mb-3" style="font-size: 3rem; display: block;"></i>
                                <span class="fw-semibold">Tidak ada data kecamatan ditemukan</span>
                              </div>
                            </td>
                          </tr>
                        <?php else: ?>
                          <?php $no = ($currentPage - 1) * $limit + 1; ?>
                          <?php foreach ($kecamatanData as $kecamatan): ?>
                            <tr>
                              <td class="text-center text-muted"><?php echo $no++; ?></td>
                              <td class="fw-semibold text-dark"><?php echo htmlspecialchars($kecamatan['nama_kecamatan']); ?></td>
                              <td class="text-center">
                                <div class="btn-group btn-group-sm" role="group">
                                  <a href="<?= route('kecamatan', 'form', ['id' => $kecamatan['id_kecamatan']]) ?>"
                                     class="btn btn-outline-primary"
                                     title="Edit">
                                    <i class="mdi mdi-pencil"></i>
                                  </a>
                                  <button type="button" class="btn btn-outline-danger"
                                          onclick="deleteKecamatan(<?php echo $kecamatan['id_kecamatan']; ?>)"
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

                  <?php if ($totalPages > 1): ?>
                    <div class="p-4 border-top">
                      <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center mb-0">
                          <?php if ($currentPage > 1): ?>
                            <li class="page-item">
                              <a class="page-link" href="<?= route('kecamatan', 'index') ?>?page=<?php echo $currentPage - 1; ?>&search=<?php echo urlencode($search); ?>">
                                <i class="mdi mdi-chevron-left"></i> Sebelumnya
                              </a>
                            </li>
                          <?php endif; ?>

                          <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?php echo $i == $currentPage ? 'active' : ''; ?>">
                              <a class="page-link" href="<?= route('kecamatan', 'index') ?>?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>"><?php echo $i; ?></a>
                            </li>
                          <?php endfor; ?>

                          <?php if ($currentPage < $totalPages): ?>
                            <li class="page-item">
                              <a class="page-link" href="<?= route('kecamatan', 'index') ?>?page=<?php echo $currentPage + 1; ?>&search=<?php echo urlencode($search); ?>">
                                Selanjutnya <i class="mdi mdi-chevron-right"></i>
                              </a>
                            </li>
                          <?php endif; ?>
                        </ul>
                      </nav>
                    </div>
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
    function deleteKecamatan(id) {
      fetch(`<?= route('kecamatan', 'getStats') ?>/${id}/stats`, {
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          let confirmMessage = 'Kecamatan ini akan dihapus secara permanen.';
          if (data.relatedDesaCount > 0) {
            confirmMessage += `<br><br>Perhatian: Terdapat ${data.relatedDesaCount} desa terkait yang akan ikut terhapus:<br>${data.relatedDesaList.join('<br>')}`;
          }

          showConfirm(confirmMessage, function() {
            fetch('<?= route('kecamatan', 'delete') ?>/' + id, {
              method: 'POST',
              headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
              },
              body: '_method=DELETE&id=' + encodeURIComponent(id)
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
              showToast('Terjadi kesalahan saat menghapus data', 'error');
            });
          });
        } else {
          showToast(data.message, 'error');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        showToast('Terjadi kesalahan saat mengambil data', 'error');
      });
    }

    <?php if (isset($_SESSION['error'])): ?>
      showToast('<?php echo addslashes($_SESSION['error']); ?>', 'error');
      <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
      showToast('<?php echo addslashes($_SESSION['success']); ?>', 'success');
      <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
  </script>
</body>
</html>
