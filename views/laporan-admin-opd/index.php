<?php include('template/header.php'); ?>

<body class="with-welcome-text">
  <div class="container-scroller">
    <!-- Navbar -->
    <?php include 'template/navbar.php'; ?>

    <div class="container-fluid page-body-wrapper">
      <!-- Settings Panel -->
      <?php include 'template/setting_panel.php'; ?>

      <!-- Sidebar -->
      <?php include 'template/sidebar.php'; ?>

      <!-- Main Panel -->
      <div class="main-panel">
        <div class="content-wrapper">
          <div class="row">
            <div class="col-12">

              <!-- Header Section -->
              <div class="page-header d-flex justify-content-between align-items-center mb-4">
                <div>
                  <h2 class="page-title">Manajemen Laporan OPD</h2>
                </div>
                <div>
                  <a href="index.php?controller=laporanOPDAdmin&action=export&format=csv"
                     class="btn btn-outline-success">
                    <i class="mdi mdi-download me-2"></i>Export CSV
                  </a>
                </div>
              </div>

              <!-- Statistics Cards -->
              <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-3">
                  <div class="card">
                    <div class="card-body">
                      <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                          <div class="avatar-sm bg-primary bg-opacity-10 rounded">
                            <i class="mdi mdi-file-document text-primary"></i>
                          </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                          <h6 class="mb-1">Total Laporan</h6>
                          <h3 class="mb-0"><?php echo number_format($stats['total']); ?></h3>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-3">
                  <div class="card">
                    <div class="card-body">
                      <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                          <div class="avatar-sm bg-warning bg-opacity-10 rounded">
                            <i class="mdi mdi-clock-outline text-warning"></i>
                          </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                          <h6 class="mb-1">Menunggu</h6>
                          <h3 class="mb-0"><?php echo number_format($stats['baru']); ?></h3>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-3">
                  <div class="card">
                    <div class="card-body">
                      <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                          <div class="avatar-sm bg-info bg-opacity-10 rounded">
                            <i class="mdi mdi-timer-sand text-info"></i>
                          </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                          <h6 class="mb-1">Diproses</h6>
                          <h3 class="mb-0"><?php echo number_format($stats['diproses']); ?></h3>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-3">
                  <div class="card">
                    <div class="card-body">
                      <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                          <div class="avatar-sm bg-success bg-opacity-10 rounded">
                            <i class="mdi mdi-check-circle text-success"></i>
                          </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                          <h6 class="mb-1">Selesai</h6>
                          <h3 class="mb-0"><?php echo number_format($stats['selesai']); ?></h3>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Filter & Search Section -->
              <div class="card mb-4">
                <div class="card-body">
                  <form method="GET" id="filterForm">
                    <div class="row g-3">
                      <div class="col-md-4">
                        <label class="form-label">Cari Laporan</label>
                        <div class="input-group">
                          <span class="input-group-text">
                            <i class="mdi mdi-magnify"></i>
                          </span>
                          <input type="text" name="search" class="form-control"
                                 placeholder="Nama OPD, kegiatan..."
                                 value="<?php echo htmlspecialchars($search); ?>">
                        </div>
                      </div>

                      <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                          <option value="">Semua</option>
                          <option value="baru" <?php echo $status === 'baru' ? 'selected' : ''; ?>>Baru</option>
                          <option value="diproses" <?php echo $status === 'diproses' ? 'selected' : ''; ?>>Diproses</option>
                          <option value="selesai" <?php echo $status === 'selesai' ? 'selected' : ''; ?>>Selesai</option>
                        </select>
                      </div>

                      <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">
                          <i class="mdi mdi-magnify me-1"></i>Cari
                        </button>
                      </div>

                      <div class="col-md-4">
                        <label class="form-label">&nbsp;</label>
                        <div class="btn-group w-100">
                          <a href="index.php?controller=laporanOPDAdmin&action=index" class="btn btn-outline-secondary">
                            <i class="mdi mdi-refresh"></i> Reset
                          </a>
                        </div>
                      </div>
                    </div>
                  </form>
                </div>
              </div>

              <!-- Data Table Section -->
              <div class="card">
                <div class="card-header">
                  <h5 class="card-title mb-0">
                    Daftar Laporan OPD
                    <span class="badge bg-secondary ms-2"><?php echo number_format($totalLaporan); ?></span>
                  </h5>
                </div>

                <div class="card-body p-0">
                  <div class="table-responsive">
                    <table class="table table-hover mb-0">
                      <thead class="table-light">
                        <tr>
                          <th width="60" class="text-center">#</th>
                          <th>OPD / Kegiatan</th>
                          <th>Uraian Laporan</th>
                          <th width="120" class="text-center">Status</th>
                          <th width="160" class="text-center">Tanggal</th>
                          <th width="160" class="text-center">Aksi</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php if (empty($laporans)): ?>
                          <tr>
                            <td colspan="6" class="text-center py-5">
                              <div class="text-muted">
                                <i class="mdi mdi-inbox-outline display-4"></i>
                                <h5 class="mt-3">Tidak ada data laporan</h5>
                                <p>Belum ada laporan yang sesuai dengan filter yang dipilih</p>
                              </div>
                            </td>
                          </tr>
                        <?php else: ?>
                          <?php
                          $no = ($page - 1) * $limit + 1;
                          foreach ($laporans as $laporan):
                            // Format tanggal Indonesia
                            $hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                            $bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                                      'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                            $timestamp = strtotime($laporan['created_at']);
                            $nama_hari = $hari[date('w', $timestamp)];
                            $tanggal = date('d', $timestamp);
                            $nama_bulan = $bulan[date('n', $timestamp) - 1];
                            $tahun = date('Y', $timestamp);
                            $jam = date('H:i', $timestamp);
                            $tanggal_indo = "$nama_hari, $tanggal $nama_bulan $tahun";
                          ?>
                            <tr>
                              <td class="text-center">
                                <span class="badge bg-light text-dark"><?php echo $no++; ?></span>
                              </td>
                              <td>
                                <div>
                                  <h6 class="mb-1"><?php echo htmlspecialchars($laporan['nama_opd'] ?? ''); ?></h6>
                                  <small class="text-muted">
                                    <?php echo htmlspecialchars($laporan['nama_kegiatan'] ?? ''); ?>
                                  </small>
                                </div>
                              </td>
                              <td>
                                <div class="text-truncate" style="max-width: 300px;"
                                     title="<?php echo htmlspecialchars(strip_tags($laporan['uraian_laporan'] ?? '')); ?>">
                                  <?php echo htmlspecialchars(substr(strip_tags($laporan['uraian_laporan'] ?? ''), 0, 100)) . '...'; ?>
                                </div>
                                <?php if ($laporan['upload_file'] ?? false): ?>
                                  <small class="text-muted">
                                    <i class="mdi mdi-paperclip"></i> Ada lampiran
                                  </small>
                                <?php endif; ?>
                              </td>
                              <td class="text-center">
                                <?php
                                $statusConfig = [
                                  'baru' => 'warning',
                                  'diproses' => 'info',
                                  'selesai' => 'success'
                                ];
                                ?>
                                <span class="badge bg-<?php echo $statusConfig[$laporan['status_laporan'] ?? 'baru']; ?>">
                                  <?php echo ucfirst($laporan['status_laporan'] ?? 'baru'); ?>
                                </span>
                              </td>
                              <td class="text-center">
                                <div>
                                  <small class="text-muted d-block">
                                    <?php echo $tanggal_indo; ?>
                                  </small>
                                  <small class="text-muted">
                                    <?php echo $jam; ?>
                                  </small>
                                </div>
                              </td>
                              <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                  <a href="index.php?controller=laporanOPDAdmin&action=detail&id=<?php echo $laporan['id_laporan_opd'] ?? ''; ?>"
                                     class="btn btn-outline-info" title="Detail">
                                    <i class="mdi mdi-eye"></i>
                                  </a>

                                  <a href="index.php?controller=laporanOPDAdmin&action=edit&id=<?php echo $laporan['id_laporan_opd'] ?? ''; ?>"
                                     class="btn btn-outline-warning" title="Edit">
                                    <i class="mdi mdi-pencil"></i>
                                  </a>

                                  <button type="button"
                                          class="btn btn-outline-primary"
                                          data-bs-toggle="modal"
                                          data-bs-target="#statusModal"
                                          data-id="<?php echo $laporan['id_laporan_opd'] ?? ''; ?>"
                                          data-status="<?php echo $laporan['status_laporan'] ?? ''; ?>"
                                          title="Ubah Status">
                                    <i class="mdi mdi-flag"></i>
                                  </button>

                                  <form method="POST" action="index.php?controller=laporanOPDAdmin&action=delete&id=<?php echo $laporan['id_laporan_opd'] ?? ''; ?>"
                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus laporan ini?');"
                                        style="display: inline;">
                                    <button type="submit" class="btn btn-outline-danger" title="Hapus">
                                      <i class="mdi mdi-delete"></i>
                                    </button>
                                  </form>
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
                    <div class="card-footer">
                      <nav>
                        <ul class="pagination justify-content-center mb-0">
                          <?php
                          $currentUrl = $_SERVER['REQUEST_URI'];
                          $urlParts = parse_url($currentUrl);
                          parse_str($urlParts['query'] ?? '', $queryParams);
                          unset($queryParams['page']);

                          // Previous button
                          if ($page > 1):
                            $queryParams['page'] = $page - 1;
                            $prevUrl = $urlParts['path'] . '?' . http_build_query($queryParams);
                          ?>
                            <li class="page-item">
                              <a class="page-link" href="<?php echo htmlspecialchars($prevUrl); ?>">
                                <i class="mdi mdi-chevron-left"></i>
                              </a>
                            </li>
                          <?php endif; ?>

                          <?php
                          // Page numbers
                          $startPage = max(1, $page - 2);
                          $endPage = min($totalPages, $page + 2);

                          if ($startPage > 1):
                            $queryParams['page'] = 1;
                            $firstUrl = $urlParts['path'] . '?' . http_build_query($queryParams);
                          ?>
                            <li class="page-item">
                              <a class="page-link" href="<?php echo htmlspecialchars($firstUrl); ?>">1</a>
                            </li>
                            <?php if ($startPage > 2): ?>
                              <li class="page-item disabled">
                                <span class="page-link">...</span>
                              </li>
                            <?php endif; ?>
                          <?php endif; ?>

                          <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                            <?php $queryParams['page'] = $i; ?>
                            <?php $pageUrl = $urlParts['path'] . '?' . http_build_query($queryParams); ?>
                            <li class="page-item <?php echo $page === $i ? 'active' : ''; ?>">
                              <a class="page-link" href="<?php echo htmlspecialchars($pageUrl); ?>"><?php echo $i; ?></a>
                            </li>
                          <?php endfor; ?>

                          <?php
                          if ($endPage < $totalPages):
                            if ($endPage < $totalPages - 1):
                            ?>
                              <li class="page-item disabled">
                                <span class="page-link">...</span>
                              </li>
                            <?php endif; ?>
                            <?php $queryParams['page'] = $totalPages; ?>
                            <?php $lastUrl = $urlParts['path'] . '?' . http_build_query($queryParams); ?>
                            <li class="page-item">
                              <a class="page-link" href="<?php echo htmlspecialchars($lastUrl); ?>"><?php echo $totalPages; ?></a>
                            </li>
                          <?php endif; ?>

                          <!-- Next button -->
                          <?php if ($page < $totalPages): ?>
                            <?php $queryParams['page'] = $page + 1; ?>
                            <?php $nextUrl = $urlParts['path'] . '?' . http_build_query($queryParams); ?>
                            <li class="page-item">
                              <a class="page-link" href="<?php echo htmlspecialchars($nextUrl); ?>">
                                <i class="mdi mdi-chevron-right"></i>
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

  <!-- Status Change Modal -->
  <div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Ubah Status Laporan</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form method="POST" action="index.php?controller=laporanOPDAdmin&action=updateStatus">
          <div class="modal-body">
            <input type="hidden" name="id" id="modalLaporanId">
            <input type="hidden" name="redirect_url" value="<?php echo $_SERVER['REQUEST_URI']; ?>">

            <div class="mb-3">
              <label class="form-label">Pilih Status</label>
              <div class="d-grid gap-2">
                <label class="form-check form-check-card">
                  <input class="form-check-input" type="radio" name="status" value="baru" id="statusBaru">
                  <div class="card">
                    <div class="card-body">
                      <div class="d-flex align-items-center">
                        <i class="mdi mdi-clock-outline text-warning me-3"></i>
                        <div>
                          <h6 class="mb-0">Baru</h6>
                          <small class="text-muted">Laporan baru masuk</small>
                        </div>
                      </div>
                    </div>
                  </div>
                </label>

                <label class="form-check form-check-card">
                  <input class="form-check-input" type="radio" name="status" value="diproses" id="statusDiproses">
                  <div class="card">
                    <div class="card-body">
                      <div class="d-flex align-items-center">
                        <i class="mdi mdi-timer-sand text-info me-3"></i>
                        <div>
                          <h6 class="mb-0">Diproses</h6>
                          <small class="text-muted">Sedang dalam proses</small>
                        </div>
                      </div>
                    </div>
                  </div>
                </label>

                <label class="form-check form-check-card">
                  <input class="form-check-input" type="radio" name="status" value="selesai" id="statusSelesai">
                  <div class="card">
                    <div class="card-body">
                      <div class="d-flex align-items-center">
                        <i class="mdi mdi-check-circle text-success me-3"></i>
                        <div>
                          <h6 class="mb-0">Selesai</h6>
                          <small class="text-muted">Laporan selesai diproses</small>
                        </div>
                      </div>
                    </div>
                  </div>
                </label>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <?php include 'template/script.php'; ?>

  <style>
    .avatar-sm {
      width: 48px;
      height: 48px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.25rem;
    }

    .form-check-card {
      cursor: pointer;
    }

    .form-check-card input[type="radio"] {
      display: none;
    }

    .form-check-card input[type="radio"]:checked + .card {
      border-color: #0d6efd;
      background-color: #f8f9ff;
    }

    .form-check-card input[type="radio"]:checked + .card .card-body {
      font-weight: 500;
    }

    .form-check-card .card {
      transition: all 0.2s ease;
      border: 2px solid #e9ecef;
    }

    .form-check-card .card:hover {
      border-color: #0d6efd;
      background-color: #f8f9ff;
    }

    .btn-group-sm .btn {
      padding: 0.25rem 0.5rem;
    }

    .table th {
      border-top: none;
      font-weight: 600;
      color: #495057;
      font-size: 0.875rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .badge {
      font-weight: 500;
    }

    .card {
      border: 1px solid #e9ecef;
      border-radius: 0.5rem;
    }

    .card:hover {
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
  </style>

  <script>
    // Modal status change
    const statusModal = document.getElementById('statusModal');
    if (statusModal) {
      statusModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const laporanId = button.getAttribute('data-id');
        const currentStatus = button.getAttribute('data-status');

        document.getElementById('modalLaporanId').value = laporanId;

        // Set current status
        document.querySelectorAll('input[name="status"]').forEach(radio => {
          radio.checked = radio.value === currentStatus;
        });
      });
    }

    // Clear filters
    function clearFilters() {
      window.location.href = 'index.php?controller=laporanOPDAdmin&action=index';
    }

    // Refresh data
    function refreshData() {
      location.reload();
    }

    // View detail
    function viewDetail(id) {
      window.location.href = `index.php?controller=laporanOPDAdmin&action=detail&id=${id}`;
    }

    // Edit laporan
    function editLaporan(id) {
      window.location.href = `index.php?controller=laporanOPDAdmin&action=edit&id=${id}`;
    }

    // Auto-refresh data setiap 30 detik
    let refreshInterval;

    function startAutoRefresh() {
      refreshInterval = setInterval(() => {
        location.reload();
      }, 30000);
    }

    function stopAutoRefresh() {
      if (refreshInterval) {
        clearInterval(refreshInterval);
      }
    }

    // Stop auto-refresh saat user sedang berinteraksi
    document.addEventListener('mousemove', stopAutoRefresh);
    document.addEventListener('keypress', stopAutoRefresh);

    // Mulai auto-refresh
    startAutoRefresh();

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
      // Ctrl/Cmd + K for search focus
      if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        document.querySelector('input[name="search"]')?.focus();
      }

      // Escape to clear search
      if (e.key === 'Escape') {
        const searchInput = document.querySelector('input[name="search"]');
        if (searchInput) {
          searchInput.value = '';
          searchInput.blur();
        }
      }
    });

    // Toast notification for success messages
    function showToast(message, type = 'success') {
      const toastHtml = `
        <div class="toast align-items-center text-white bg-${type} border-0" role="alert">
          <div class="d-flex">
            <div class="toast-body">
              ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
          </div>
        </div>
      `;

      const toastContainer = document.createElement('div');
      toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
      toastContainer.innerHTML = toastHtml;
      document.body.appendChild(toastContainer);

      const toast = new bootstrap.Toast(toastContainer.querySelector('.toast'));
      toast.show();

      setTimeout(() => {
        toastContainer.remove();
      }, 5000);
    }

    // Show success messages as toast if exist
    <?php if (isset($_SESSION['success'])): ?>
      showToast('<?php echo $_SESSION['success']; ?>', 'success');
      <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
      showToast('<?php echo $_SESSION['error']; ?>', 'danger');
      <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
  </script>
</body>
</html>