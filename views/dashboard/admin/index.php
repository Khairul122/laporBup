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

              <!-- Welcome Section -->
              <div class="welcome-content mb-4">
                <div class="d-flex align-items-center justify-content-between">
                  <div class="welcome-text">
                    <h4 class="mb-1">Selamat datang, <?php echo htmlspecialchars($user['username']); ?></h4>
                  </div>
                  <div class="welcome-action">
                  </div>
                </div>
              </div>

              <!-- Dashboard Cards Status Laporan -->
              <div class="row mb-4">
                <div class="col-md-3 col-sm-6 mb-3">
                  <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-3">
                      <div class="d-flex align-items-center">
                        <div class="icon-md bg-primary text-white rounded-circle mr-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                          <i class="fas fa-file-alt"></i>
                        </div>
                        <div class="flex-grow-1">
                          <h3 class="mb-0 fw-bold"><?php echo number_format($data['laporan_by_status']['total_semua'] ?? 0); ?></h3>
                          <p class="mb-0 text-muted small">Total Laporan</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                  <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-3">
                      <div class="d-flex align-items-center">
                        <div class="icon-md bg-warning text-white rounded-circle mr-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                          <i class="fas fa-clock"></i>
                        </div>
                        <div class="flex-grow-1">
                          <h3 class="mb-0 fw-bold"><?php echo number_format($data['laporan_by_status']['total_baru'] ?? 0); ?></h3>
                          <p class="mb-0 text-muted small">Baru</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                  <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-3">
                      <div class="d-flex align-items-center">
                        <div class="icon-md bg-info text-white rounded-circle mr-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                          <i class="fas fa-sync-alt"></i>
                        </div>
                        <div class="flex-grow-1">
                          <h3 class="mb-0 fw-bold"><?php echo number_format($data['laporan_by_status']['total_diproses'] ?? 0); ?></h3>
                          <p class="mb-0 text-muted small">Diproses</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                  <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-3">
                      <div class="d-flex align-items-center">
                        <div class="icon-md bg-success text-white rounded-circle mr-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                          <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="flex-grow-1">
                          <h3 class="mb-0 fw-bold"><?php echo number_format($data['laporan_by_status']['total_selesai'] ?? 0); ?></h3>
                          <p class="mb-0 text-muted small">Selesai</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Dashboard Cards Laporan Camat & OPD -->
              <div class="row mb-4">
                <div class="col-md-6 col-sm-6 mb-3">
                  <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-3">
                      <div class="d-flex align-items-center">
                        <div class="icon-md bg-purple text-white rounded-circle mr-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: #6f42c1 !important;">
                          <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="flex-grow-1">
                          <h3 class="mb-0 fw-bold"><?php echo number_format($data['laporan_camat_by_status']['total_semua'] ?? 0); ?></h3>
                          <p class="mb-0 text-muted small">Laporan Camat</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-6 col-sm-6 mb-3">
                  <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-3">
                      <div class="d-flex align-items-center">
                        <div class="icon-md bg-pink text-white rounded-circle mr-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: #e83e8c !important;">
                          <i class="fas fa-building"></i>
                        </div>
                        <div class="flex-grow-1">
                          <h3 class="mb-0 fw-bold"><?php echo number_format($data['laporan_opd_by_status']['total_semua'] ?? 0); ?></h3>
                          <p class="mb-0 text-muted small">Laporan OPD</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Simple Chart Section -->
              <div class="row mb-4">
                <div class="col-md-12 mb-3">
                  <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 pt-4 pb-0">
                      <h5 class="card-title mb-1">Statistik Laporan Bulanan</h5>
                      <small class="text-muted">6 bulan terakhir</small>
                    </div>
                    <div class="card-body pb-4">
                      <?php if (!empty($data['statistik_per_bulan'])): ?>
                        <canvas id="monthlyChart" height="250"></canvas>
                      <?php else: ?>
                        <p class="text-muted text-center py-5 mb-0">Belum ada data laporan bulanan</p>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Recent Reports Table -->
              <div class="row mb-4">
                <div class="col-12">
                  <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 pt-4 pb-3">
                      <h5 class="card-title mb-0">
                        <i class="fas fa-history text-primary"></i>
                        Laporan Terbaru
                      </h5>
                    </div>
                    <div class="card-body pb-4">
                      <?php if (!empty($data['laporan_terbaru'])): ?>
                        <div class="table-responsive">
                          <table class="table table-hover">
                            <thead>
                              <tr>
                                <th class="border-top-0">ID</th>
                                <th class="border-top-0">Judul/Informasi</th>
                                <th class="border-top-0">Lokasi/OPD</th>
                                <th class="border-top-0">Status</th>
                                <th class="border-top-0">Tanggal</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php foreach ($data['laporan_terbaru'] as $laporan): ?>
                                <tr>
                                  <td>
                                    <span class="fw-bold">#<?php echo $laporan['id_laporan_camat'] ?? $laporan['id_laporan_opd']; ?></span>
                                    <br>
                                    <small class="badge badge-<?php echo $laporan['sumber'] === 'camat' ? 'primary' : 'info'; ?>">
                                      <?php echo ucfirst($laporan['sumber']); ?>
                                    </small>
                                  </td>
                                  <td>
                                    <?php if ($laporan['sumber'] === 'camat'): ?>
                                      <div class="fw-semibold"><?php echo htmlspecialchars($laporan['nama_pelapor']); ?></div>
                                      <small class="text-muted"><?php echo htmlspecialchars($laporan['nama_desa']); ?></small>
                                    <?php else: ?>
                                      <div class="fw-semibold"><?php echo htmlspecialchars($laporan['nama_opd']); ?></div>
                                      <small class="text-muted"><?php echo htmlspecialchars($laporan['nama_kegiatan']); ?></small>
                                    <?php endif; ?>
                                  </td>
                                  <td>
                                    <?php if ($laporan['sumber'] === 'camat'): ?>
                                      <i class="fas fa-map-marker-alt text-primary"></i>
                                      <?php echo htmlspecialchars($laporan['nama_kecamatan']); ?>
                                    <?php else: ?>
                                      <i class="fas fa-building text-info"></i>
                                      <?php echo htmlspecialchars($laporan['nama_opd']); ?>
                                    <?php endif; ?>
                                  </td>
                                  <td>
                                    <?php
                                    $statusClass = [
                                      'baru' => 'warning',
                                      'diproses' => 'info',
                                      'selesai' => 'success'
                                    ];
                                    ?>
                                    <span class="badge bg-<?php echo $statusClass[$laporan['status_laporan']] ?? 'secondary'; ?>">
                                      <?php echo ucfirst($laporan['status_laporan']); ?>
                                    </span>
                                  </td>
                                  <td>
                                    <small class="text-muted"><?php echo date('d/m/Y H:i', strtotime($laporan['created_at'])); ?></small>
                                  </td>
                                </tr>
                              <?php endforeach; ?>
                            </tbody>
                          </table>
                        </div>
                      <?php else: ?>
                        <p class="text-muted text-center py-5 mb-0">Belum ada laporan masuk</p>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php include 'template/script.php'; ?>

  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <script>
      // Chart Data
      const monthlyData = <?php echo json_encode($data['statistik_per_bulan'] ?? []); ?>;
      const chartData = <?php echo json_encode($charts ?? []); ?>;

      // Initialize charts only if data exists
      document.addEventListener('DOMContentLoaded', function() {
          // Monthly Chart
          <?php if (!empty($data['statistik_per_bulan'])): ?>
          const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
          const monthlyChart = new Chart(monthlyCtx, {
              type: 'line',
              data: {
                  labels: monthlyData.map(item => item.bulan_nama),
                  datasets: [{
                      label: 'Jumlah Laporan',
                      data: monthlyData.map(item => item.total),
                      borderColor: '#3498db',
                      backgroundColor: 'rgba(52, 152, 219, 0.1)',
                      borderWidth: 3,
                      tension: 0.4,
                      fill: true
                  }]
              },
              options: {
                  responsive: true,
                  maintainAspectRatio: false,
                  plugins: {
                      legend: {
                          display: false
                      }
                  },
                  scales: {
                      y: {
                          beginAtZero: true,
                          ticks: {
                              stepSize: 1
                          }
                      }
                  }
              }
          });
          <?php endif; ?>
      });
  </script>
</body>
</html>