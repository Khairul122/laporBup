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
                  <h2 class="page-title">Manajemen Laporan</h2>
                  <p class="text-muted mb-0">Admin dapat mengakses semua laporan OPD dan Camat</p>
                </div>
              </div>

              <!-- Tab Navigation -->
              <div class="card mb-4">
                <div class="card-body p-0">
                  <ul class="nav nav-tabs nav-justified" id="reportTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                      <button class="nav-link <?php echo ($activeTab ?? 'camat') === 'camat' ? 'active' : ''; ?>"
                              id="camat-tab"
                              data-bs-toggle="tab"
                              data-bs-target="#camat-panel"
                              type="button"
                              role="tab"
                              aria-controls="camat-panel"
                              aria-selected="<?php echo ($activeTab ?? 'camat') === 'camat' ? 'true' : 'false'; ?>">
                        <i class="mdi mdi-map-marker-multiple me-2"></i>
                        Laporan Camat
                      </button>
                    </li>
                    <li class="nav-item" role="presentation">
                      <button class="nav-link <?php echo ($activeTab ?? 'camat') === 'opd' ? 'active' : ''; ?>"
                              id="opd-tab"
                              data-bs-toggle="tab"
                              data-bs-target="#opd-panel"
                              type="button"
                              role="tab"
                              aria-controls="opd-panel"
                              aria-selected="<?php echo ($activeTab ?? 'camat') === 'opd' ? 'true' : 'false'; ?>">
                        <i class="mdi mdi-domain me-2"></i>
                        Laporan OPD
                      </button>
                    </li>
                  </ul>
                </div>
              </div>

              <!-- Tab Content -->
              <div class="tab-content" id="reportTabContent">
                <!-- Camat Tab Panel -->
                <div class="tab-pane fade <?php echo ($activeTab ?? 'camat') === 'camat' ? 'show active' : ''; ?>"
                     id="camat-panel"
                     role="tabpanel"
                     aria-labelledby="camat-tab">

                  <!-- Statistics Cards for Camat -->
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
                              <h6 class="mb-1">Total Laporan Camat</h6>
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

                  <!-- Camat Filter Section -->
                  <div class="card mb-4">
                    <div class="card-body">
                      <form method="GET" id="camatFilterForm">
                        <input type="hidden" name="controller" value="laporan">
                        <input type="hidden" name="action" value="index">
                        <input type="hidden" name="tab" value="camat">

                        <div class="row g-3">
                          <div class="col-md-3">
                            <label class="form-label">Cari Laporan</label>
                            <div class="input-group">
                              <span class="input-group-text">
                                <i class="mdi mdi-magnify"></i>
                              </span>
                              <input type="text" name="search" class="form-control"
                                     placeholder="Nama kecamatan, kegiatan..."
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
                            <label class="form-label">Tujuan</label>
                            <select name="tujuan" class="form-select">
                              <option value="">Semua</option>
                              <?php foreach ($tujuanOptions as $option): ?>
                                <option value="<?php echo htmlspecialchars($option); ?>"
                                        <?php echo $tujuan === $option ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($option); ?>
                                </option>
                              <?php endforeach; ?>
                            </select>
                          </div>

                          <div class="col-md-2">
                            <label class="form-label">Hari</label>
                            <select name="hari" class="form-select">
                              <option value="">Semua</option>
                              <?php
                              $days = ['Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu',
                                       'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu', 'Sunday' => 'Minggu'];
                              foreach ($days as $dayKey => $dayValue):
                              ?>
                                <option value="<?php echo $dayKey; ?>" <?php echo $hari === $dayKey ? 'selected' : ''; ?>>
                                  <?php echo $dayValue; ?>
                                </option>
                              <?php endforeach; ?>
                            </select>
                          </div>

                          <div class="col-md-1">
                            <label class="form-label">Bulan</label>
                            <select name="bulan" class="form-select">
                              <option value="">Semua</option>
                              <?php
                              $months = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
                                        7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
                              foreach ($months as $monthKey => $monthValue):
                              ?>
                                <option value="<?php echo $monthKey; ?>" <?php echo $bulan == $monthKey ? 'selected' : ''; ?>>
                                  <?php echo $monthValue; ?>
                                </option>
                              <?php endforeach; ?>
                            </select>
                          </div>

                          <div class="col-md-1">
                            <label class="form-label">Tahun</label>
                            <select name="tahun" class="form-select">
                              <option value="">Semua</option>
                              <?php
                              $years = [2023, 2024, 2025]; // You can make this dynamic
                              foreach ($years as $year):
                              ?>
                                <option value="<?php echo $year; ?>" <?php echo $tahun == $year ? 'selected' : ''; ?>>
                                  <?php echo $year; ?>
                                </option>
                              <?php endforeach; ?>
                            </select>
                          </div>

                          <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary w-100">
                              <i class="mdi mdi-magnify me-1"></i>Cari
                            </button>
                          </div>

                          <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <div class="btn-group w-100">
                              <a href="index.php?controller=laporan&action=index&tab=camat" class="btn btn-outline-secondary">
                                <i class="mdi mdi-refresh"></i> Reset
                              </a>
                              <a href="index.php?controller=laporan&action=tandaTangan&id=0&type=camat" class="btn btn-outline-info">
                                <i class="mdi mdi-draw me-1"></i>TTD
                              </a>
                              <button type="button" class="btn btn-outline-danger" onclick="generatePDF('camat')">
                                <i class="mdi mdi-file-pdf me-1"></i>PDF
                              </button>
                              <button type="button" class="btn btn-outline-success" onclick="generateExcel('camat')">
                                <i class="mdi mdi-file-excel me-1"></i>Excel
                              </button>
                            </div>
                          </div>
                        </div>
                      </form>
                    </div>
                  </div>

                  <!-- Camat Data Table -->
                  <div class="card">
                    <div class="card-header">
                      <h5 class="card-title mb-0">
                        Daftar Laporan Camat
                        <span class="badge bg-secondary ms-2"><?php echo number_format($totalLaporan); ?></span>
                      </h5>
                    </div>

                    <div class="card-body p-0">
                      <div class="table-responsive">
                        <table class="table table-hover mb-0">
                          <thead class="table-light">
                            <tr>
                              <th width="60" class="text-center">No</th>
                              <th>Kecamatan / Kegiatan</th>
                              <th>Uraian Laporan</th>
                              <th width="120" class="text-center">Tujuan</th>
                              <th width="120" class="text-center">Status</th>
                              <th width="160" class="text-center">Tanggal</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php if (empty($laporans)): ?>
                              <tr>
                                <td colspan="6" class="text-center py-5">
                                  <div class="text-muted">
                                    <i class="mdi mdi-inbox-outline display-4"></i>
                                    <h5 class="mt-3">Tidak ada data laporan camat</h5>
                                    <p>Belum ada laporan camat yang sesuai dengan filter yang dipilih</p>
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
                                $tanggal_format = "$nama_hari, $tanggal $nama_bulan $tahun - $jam";
                              ?>
                                <tr>
                                  <td class="text-center"><?php echo $no++; ?></td>
                                  <td>
                                    <div>
                                      <h6 class="mb-1"><?php echo htmlspecialchars($laporan['nama_kecamatan']); ?></h6>
                                      <small class="text-muted"><?php echo htmlspecialchars($laporan['nama_kegiatan']); ?></small>
                                    </div>
                                  </td>
                                  <td>
                                    <div class="text-truncate" style="max-width: 300px;">
                                      <?php echo htmlspecialchars(strip_tags($laporan['uraian_laporan'])); ?>
                                    </div>
                                  </td>
                                  <td class="text-center">
                                    <span class="badge bg-info"><?php echo htmlspecialchars($laporan['tujuan']); ?></span>
                                  </td>
                                  <td class="text-center">
                                    <?php
                                    $statusClass = [
                                        'baru' => 'warning',
                                        'diproses' => 'info',
                                        'selesai' => 'success'
                                    ];
                                    $statusClass = $statusClass[$laporan['status_laporan']] ?? 'secondary';
                                    ?>
                                    <span class="badge bg-<?php echo $statusClass; ?>">
                                      <?php echo ucfirst($laporan['status_laporan']); ?>
                                    </span>
                                  </td>
                                  <td class="text-center">
                                    <small class="text-muted"><?php echo $tanggal_format; ?></small>
                                  </td>
                                </tr>
                              <?php endforeach; ?>
                            <?php endif; ?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- OPD Tab Panel -->
                <div class="tab-pane fade <?php echo ($activeTab ?? 'camat') === 'opd' ? 'show active' : ''; ?>"
                     id="opd-panel"
                     role="tabpanel"
                     aria-labelledby="opd-tab">

                  <!-- Statistics Cards for OPD -->
                  <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-3">
                      <div class="card">
                        <div class="card-body">
                          <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                              <div class="avatar-sm bg-primary bg-opacity-10 rounded">
                                <i class="mdi mdi-domain text-primary"></i>
                              </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                              <h6 class="mb-1">Total Laporan OPD</h6>
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

                  <!-- OPD Filter Section -->
                  <div class="card mb-4">
                    <div class="card-body">
                      <form method="GET" id="opdFilterForm">
                        <input type="hidden" name="controller" value="laporan">
                        <input type="hidden" name="action" value="index">
                        <input type="hidden" name="tab" value="opd">

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
                            <label class="form-label">Hari</label>
                            <select name="hari" class="form-select">
                              <option value="">Semua</option>
                              <?php
                              $days = ['Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu',
                                       'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu', 'Sunday' => 'Minggu'];
                              foreach ($days as $dayKey => $dayValue):
                              ?>
                                <option value="<?php echo $dayKey; ?>" <?php echo $hari === $dayKey ? 'selected' : ''; ?>>
                                  <?php echo $dayValue; ?>
                                </option>
                              <?php endforeach; ?>
                            </select>
                          </div>

                          <div class="col-md-2">
                            <label class="form-label">Bulan</label>
                            <select name="bulan" class="form-select">
                              <option value="">Semua</option>
                              <?php
                              $months = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
                                        7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
                              foreach ($months as $monthKey => $monthValue):
                              ?>
                                <option value="<?php echo $monthKey; ?>" <?php echo $bulan == $monthKey ? 'selected' : ''; ?>>
                                  <?php echo $monthValue; ?>
                                </option>
                              <?php endforeach; ?>
                            </select>
                          </div>

                          <div class="col-md-2">
                            <label class="form-label">Tahun</label>
                            <select name="tahun" class="form-select">
                              <option value="">Semua</option>
                              <?php
                              $years = [2023, 2024, 2025]; // You can make this dynamic
                              foreach ($years as $year):
                              ?>
                                <option value="<?php echo $year; ?>" <?php echo $tahun == $year ? 'selected' : ''; ?>>
                                  <?php echo $year; ?>
                                </option>
                              <?php endforeach; ?>
                            </select>
                          </div>

                          <div class="col-md-4">
                            <label class="form-label">&nbsp;</label>
                            <div class="btn-group w-100">
                              <button type="submit" class="btn btn-primary">
                                <i class="mdi mdi-magnify me-1"></i>Cari
                              </button>
                              <a href="index.php?controller=laporan&action=index&tab=opd" class="btn btn-outline-secondary">
                                <i class="mdi mdi-refresh"></i> Reset
                              </a>
                              <a href="index.php?controller=laporan&action=tandaTangan&id=0&type=opd" class="btn btn-outline-info">
                                <i class="mdi mdi-draw me-1"></i>TTD
                              </a>
                              <button type="button" class="btn btn-outline-danger" onclick="generatePDF('opd')">
                                <i class="mdi mdi-file-pdf me-1"></i>PDF
                              </button>
                              <button type="button" class="btn btn-outline-success" onclick="generateExcel('opd')">
                                <i class="mdi mdi-file-excel me-1"></i>Excel
                              </button>
                            </div>
                          </div>
                        </div>
                      </form>
                    </div>
                  </div>

                  <!-- OPD Data Table -->
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
                              <th width="60" class="text-center">No</th>
                              <th>OPD / Kegiatan</th>
                              <th>Uraian Laporan</th>
                              <th width="120" class="text-center">Status</th>
                              <th width="160" class="text-center">Tanggal</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php if (empty($laporans)): ?>
                              <tr>
                                <td colspan="5" class="text-center py-5">
                                  <div class="text-muted">
                                    <i class="mdi mdi-inbox-outline display-4"></i>
                                    <h5 class="mt-3">Tidak ada data laporan OPD</h5>
                                    <p>Belum ada laporan OPD yang sesuai dengan filter yang dipilih</p>
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
                                $tanggal_format = "$nama_hari, $tanggal $nama_bulan $tahun - $jam";
                              ?>
                                <tr>
                                  <td class="text-center"><?php echo $no++; ?></td>
                                  <td>
                                    <div>
                                      <h6 class="mb-1"><?php echo htmlspecialchars($laporan['nama_opd']); ?></h6>
                                      <small class="text-muted"><?php echo htmlspecialchars($laporan['nama_kegiatan']); ?></small>
                                    </div>
                                  </td>
                                  <td>
                                    <div class="text-truncate" style="max-width: 300px;">
                                      <?php echo htmlspecialchars(strip_tags($laporan['uraian_laporan'])); ?>
                                    </div>
                                  </td>
                                  <td class="text-center">
                                    <?php
                                    $statusClass = [
                                        'baru' => 'warning',
                                        'diproses' => 'info',
                                        'selesai' => 'success'
                                    ];
                                    $statusClass = $statusClass[$laporan['status_laporan']] ?? 'secondary';
                                    ?>
                                    <span class="badge bg-<?php echo $statusClass; ?>">
                                      <?php echo ucfirst($laporan['status_laporan']); ?>
                                    </span>
                                  </td>
                                  <td class="text-center">
                                    <small class="text-muted"><?php echo $tanggal_format; ?></small>
                                  </td>
                                </tr>
                              <?php endforeach; ?>
                            <?php endif; ?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Pagination -->
              <?php if ($totalPages > 1): ?>
                <div class="card">
                  <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                      <div class="text-muted">
                        Menampilkan <?php echo count($laporans); ?> dari <?php echo $totalLaporan; ?> data
                      </div>

                      <nav>
                        <ul class="pagination mb-0">
                          <?php if ($page > 1): ?>
                            <li class="page-item">
                              <a class="page-link" href="?controller=laporan&action=index&tab=<?php echo urlencode($activeTab ?? 'camat'); ?>&page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status); ?>&tujuan=<?php echo urlencode($tujuan); ?>&hari=<?php echo urlencode($hari); ?>&bulan=<?php echo urlencode($bulan); ?>&tahun=<?php echo urlencode($tahun); ?>">
                                <i class="mdi mdi-chevron-left"></i>
                              </a>
                            </li>
                          <?php endif; ?>

                          <?php
                          $startPage = max(1, $page - 2);
                          $endPage = min($totalPages, $page + 2);

                          if ($startPage > 1) {
                            echo '<li class="page-item"><a class="page-link" href="?controller=laporan&action=index&tab=' . urlencode($activeTab ?? 'camat') . '&page=1&search=' . urlencode($search) . '&status=' . urlencode($status) . '&tujuan=' . urlencode($tujuan) . '&hari=' . urlencode($hari) . '&bulan=' . urlencode($bulan) . '&tahun=' . urlencode($tahun) . '">1</a></li>';
                            if ($startPage > 2) {
                              echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                            }
                          }

                          for ($i = $startPage; $i <= $endPage; $i++) {
                            $activeClass = $i == $page ? 'active' : '';
                            echo '<li class="page-item ' . $activeClass . '">';
                            echo '<a class="page-link" href="?controller=laporan&action=index&tab=' . urlencode($activeTab ?? 'camat') . '&page=' . $i . '&search=' . urlencode($search) . '&status=' . urlencode($status) . '&tujuan=' . urlencode($tujuan) . '&hari=' . urlencode($hari) . '&bulan=' . urlencode($bulan) . '&tahun=' . urlencode($tahun) . '">' . $i . '</a>';
                            echo '</li>';
                          }

                          if ($endPage < $totalPages) {
                            if ($endPage < $totalPages - 1) {
                              echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                            }
                            echo '<li class="page-item"><a class="page-link" href="?controller=laporan&action=index&tab=' . urlencode($activeTab ?? 'camat') . '&page=' . $totalPages . '&search=' . urlencode($search) . '&status=' . urlencode($status) . '&tujuan=' . urlencode($tujuan) . '&hari=' . urlencode($hari) . '&bulan=' . urlencode($bulan) . '&tahun=' . urlencode($tahun) . '">' . $totalPages . '</a></li>';
                          }
                          ?>

                          <?php if ($page < $totalPages): ?>
                            <li class="page-item">
                              <a class="page-link" href="?controller=laporan&action=index&tab=<?php echo urlencode($activeTab ?? 'camat'); ?>&page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status); ?>&tujuan=<?php echo urlencode($tujuan); ?>&hari=<?php echo urlencode($hari); ?>&bulan=<?php echo urlencode($bulan); ?>&tahun=<?php echo urlencode($tahun); ?>">
                                <i class="mdi mdi-chevron-right"></i>
                              </a>
                            </li>
                          <?php endif; ?>
                        </ul>
                      </nav>
                    </div>
                  </div>
                </div>
              <?php endif; ?>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php include 'template/script.php'; ?>

  <!-- JavaScript for report generation -->
  <script>
    function generatePDF(tab) {
      const formId = tab === 'opd' ? 'opdFilterForm' : 'camatFilterForm';
      const form = document.getElementById(formId);
      const formData = new FormData(form);
      const params = new URLSearchParams();

      for (let [key, value] of formData.entries()) {
        params.append(key, value);
      }

      params.set('controller', 'laporan');
      params.set('action', 'generatePDF');
      params.set('tab', tab);

      window.open('index.php?' + params.toString(), '_blank');
    }

    function generateExcel(tab) {
      const formId = tab === 'opd' ? 'opdFilterForm' : 'camatFilterForm';
      const form = document.getElementById(formId);
      const formData = new FormData(form);
      const params = new URLSearchParams();

      for (let [key, value] of formData.entries()) {
        params.append(key, value);
      }

      params.set('controller', 'laporan');
      params.set('action', 'generateExcel');
      params.set('tab', tab);

      window.open('index.php?' + params.toString(), '_blank');
    }

    // Handle tab switching with form data preservation
    document.querySelectorAll('[data-bs-toggle="tab"]').forEach(tabButton => {
      tabButton.addEventListener('shown.bs.tab', function (e) {
        const targetTab = e.target.getAttribute('aria-controls');
        const currentTab = e.relatedTarget?.getAttribute('aria-controls');

        if (currentTab) {
          // Store current form data
          const currentFormId = currentTab === 'opd-panel' ? 'opdFilterForm' : 'camatFilterForm';
          const currentForm = document.getElementById(currentFormId);

          if (currentForm) {
            const formData = new FormData(currentForm);
            const params = new URLSearchParams();

            for (let [key, value] of formData.entries()) {
              if (key !== 'tab') {
                params.append(key, value);
              }
            }

            // Navigate to new tab with current filters
            const newTab = targetTab === 'opd-panel' ? 'opd' : 'camat';
            params.set('tab', newTab);

            window.location.href = 'index.php?' + params.toString();
          }
        }
      });
    });
  </script>
</body>

</html>