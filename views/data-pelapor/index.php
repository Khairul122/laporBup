<?php include('views/layouts/admin-header.php'); ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

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

              <div class="page-header mb-4">
                <div class="d-flex align-items-center justify-content-between">
                  <div>
                    <h3 class="page-title mb-1">Data Pelapor</h3>
                    <nav aria-label="breadcrumb">
                      <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= route('dashboard', 'admin') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active">Data Pelapor</li>
                      </ol>
                    </nav>
                  </div>
                  <div class="page-actions">
                    <a href="<?= route('dataPelapor', 'form') ?>" class="btn btn-primary btn-sm">
                      <i class="fas fa-plus-circle"></i> Tambah Pelapor
                    </a>
                  </div>
                </div>
              </div>

              <div class="row mb-4">
                <div class="col-12">
                  <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 pt-4 pb-3">
                      <h5 class="card-title mb-0">
                        <i class="fas fa-filter text-primary"></i>
                        Pencarian dan Filter Data
                      </h5>
                    </div>
                    <div class="card-body pb-4">
                      <form id="searchForm" class="row g-3">
                        <div class="col-md-4">
                          <label class="form-label small text-muted">Cari Pelapor</label>
                          <div class="input-group">
                            <span class="input-group-text">
                              <i class="fas fa-search"></i>
                            </span>
                            <input type="text" class="form-control" id="searchInput" placeholder="Cari username, email, atau jabatan..." value="<?php echo htmlspecialchars($search); ?>">
                          </div>
                        </div>
                        <div class="col-md-3">
                          <label class="form-label small text-muted">Filter Role</label>
                          <div class="input-group">
                            <span class="input-group-text">
                              <i class="fas fa-user-tag"></i>
                            </span>
                            <select class="form-select" id="roleFilter">
                              <option value="">Semua Role</option>
                              <option value="camat" <?php echo $role === 'camat' ? 'selected' : ''; ?>>
                                Camat
                              </option>
                              <option value="opd" <?php echo $role === 'opd' ? 'selected' : ''; ?>>
                                OPD
                              </option>
                            </select>
                          </div>
                        </div>
                        <div class="col-md-2">
                          <label class="form-label small text-muted">&nbsp;</label>
                          <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search"></i> Cari
                          </button>
                        </div>
                        <div class="col-md-1">
                          <label class="form-label small text-muted">&nbsp;</label>
                          <button type="button" class="btn btn-secondary w-100" onclick="resetFilter()" title="Reset filter">
                            <i class="fas fa-arrow-rotate-left"></i>
                          </button>
                        </div>
                        <div class="col-md-1">
                          <label class="form-label small text-muted">Tampil</label>
                          <select class="form-select" id="limitSelect" onchange="changeLimit()">
                            <?php foreach ([10, 25, 50, 100] as $opt): ?>
                              <option value="<?php echo $opt; ?>" <?php echo $limit == $opt ? 'selected' : ''; ?>><?php echo $opt; ?></option>
                            <?php endforeach; ?>
                          </select>
                        </div>
                      </form>

                      <div class="row mt-3 pt-3 border-top">
                        <div class="col-md-4">
                          <div class="d-flex align-items-center">
                            <i class="fas fa-database text-primary me-2"></i>
                            <div>
                              <small class="text-muted">Total Data</small>
                              <div class="fw-bold" id="totalDataCount"><?php echo number_format($result['total']); ?> pelapor</div>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-4">
                          <div class="d-flex align-items-center justify-content-center">
                            <i class="fas fa-file-alt text-info me-2"></i>
                            <div class="text-center">
                              <small class="text-muted">Halaman</small>
                              <div class="fw-bold" id="pageInfo"><?php echo $result['page']; ?> dari <?php echo max(1, $result['total_pages']); ?></div>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-4">
                          <div class="d-flex align-items-center justify-content-md-end">
                            <i class="fas fa-list-ol text-success me-2"></i>
                            <div class="text-md-end">
                              <small class="text-muted">Menampilkan</small>
                              <div class="fw-bold" id="rangeInfo">
                                <?php
                                $rangeStart = $result['total'] > 0 ? (($result['page'] - 1) * $limit) + 1 : 0;
                                $rangeEnd = min($result['page'] * $limit, $result['total']);
                                echo $rangeStart . '-' . $rangeEnd . ' dari ' . number_format($result['total']);
                                ?>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="row mb-4">
                <div class="col-12">
                  <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 pt-4 pb-3">
                      <h5 class="card-title mb-0">
                        <i class="fas fa-users text-primary"></i>
                        Daftar Data Pelapor
                      </h5>
                    </div>
                    <div class="card-body pb-4" id="dataContent">
                      <?php if (!empty($result['data'])): ?>
                        <div class="table-responsive">
                          <table class="table table-hover">
                            <thead>
                              <tr>
                                <th class="border-top-0">No</th>
                                <th class="border-top-0">Username</th>
                                <th class="border-top-0">Email</th>
                                <th class="border-top-0">No. Telepon</th>
                                <th class="border-top-0">Jabatan</th>
                                <th class="border-top-0">Role</th>
                                <th class="border-top-0">Total Laporan</th>
                                <th class="border-top-0">Tanggal Dibuat</th>
                                <th class="border-top-0 text-center">Aksi</th>
                              </tr>
                            </thead>
                            <tbody id="dataTableBody">
                              <?php
                              $no = 1;
                              foreach ($result['data'] as $pelapor): ?>
                                <tr>
                                  <td>
                                    <span class="fw-bold"><?php echo $no++; ?></span>
                                  </td>
                                  <td>
                                    <div class="d-flex align-items-center">
                                      <div class="avatar-sm bg-<?php echo $pelapor['role'] === 'camat' ? 'primary' : 'info'; ?> text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                        <?php echo strtoupper(substr($pelapor['username'], 0, 1)); ?>
                                      </div>
                                      <div class="fw-semibold"><?php echo htmlspecialchars($pelapor['username']); ?></div>
                                    </div>
                                  </td>
                                  <td>
                                    <a href="mailto:<?php echo htmlspecialchars($pelapor['email']); ?>" class="text-decoration-none">
                                      <?php echo htmlspecialchars($pelapor['email']); ?>
                                    </a>
                                  </td>
                                  <td>
                                    <?php
                                    $no_telp = $pelapor['no_telp'] ?? '';
                                    if (!empty($no_telp)) {
                                        echo '<a href="https://wa.me/' . preg_replace('/[^0-9]/', '', $no_telp) . '" target="_blank" class="text-decoration-none text-success">';
                                        echo '<i class="fab fa-whatsapp"></i> ' . htmlspecialchars($no_telp);
                                        echo '</a>';
                                    } else {
                                        echo '<span class="text-muted">-</span>';
                                    }
                                    ?>
                                  </td>
                                  <td>
                                    <?php echo htmlspecialchars($pelapor['jabatan']); ?>
                                  </td>
                                  <td>
                                    <span class="badge bg-<?php echo $pelapor['role'] === 'camat' ? 'primary' : 'info'; ?>">
                                      <i class="fas fa-<?php echo $pelapor['role'] === 'camat' ? 'map-marker-alt' : 'building'; ?>"></i>
                                      <?php echo ucfirst($pelapor['role']); ?>
                                    </span>
                                  </td>
                                  <td>
                                    <div class="d-flex flex-column">
                                      <span class="fw-bold"><?php echo number_format($pelapor['total_laporan']); ?></span>
                                      <small class="text-muted">
                                        Camat: <?php echo $pelapor['total_laporan_camat']; ?> | OPD: <?php echo $pelapor['total_laporan_opd']; ?>
                                      </small>
                                    </div>
                                  </td>
                                  <td>
                                    <small class="text-muted"><?php echo formatDateIndo($pelapor['created_at']); ?></small>
                                  </td>
                                  <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                      <button class="btn btn-outline-primary" onclick="editPelapor(<?php echo $pelapor['id_user']; ?>)" title="Edit">
                                        <i class="fas fa-edit"></i>
                                      </button>
                                      <button class="btn btn-outline-danger" onclick="deletePelapor(<?php echo $pelapor['id_user']; ?>, '<?php echo htmlspecialchars($pelapor['username']); ?>')" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                      </button>
                                    </div>
                                  </td>
                                </tr>
                              <?php endforeach; ?>
                            </tbody>
                          </table>
                        </div>

                        <div id="paginationWrapper"></div>
                      <?php else: ?>
                        <div class="text-center py-5">
                          <i class="fas fa-users text-muted" style="font-size: 3rem;"></i>
                          <h5 class="mt-3 text-muted">Belum ada data pelapor</h5>
                          <p class="text-muted">Tambahkan data pelapor untuk memulai</p>
                          <a href="<?= route('dataPelapor', 'form') ?>" class="btn btn-primary">
                            <i class="fas fa-plus-circle"></i> Tambah Pelapor
                          </a>
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
    </div>
  </div>

  <?php
function formatDateIndo($date) {
    $days = array('Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu');
    $months = array('', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');

    $timestamp = strtotime($date);
    $dayName = $days[date('w', $timestamp)];
    $day = date('d', $timestamp);
    $month = $months[date('n', $timestamp)];
    $year = date('Y', $timestamp);

    return "$dayName, $day $month $year";
}
?>

  <?php include 'views/layouts/admin-script.php'; ?>

  <script></script>

  <style>
    .form-control.is-invalid {
      border-color: #dc3545;
    }

    .form-control.is-invalid:focus {
      border-color: #dc3545;
      box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }

    .alert {
      animation: slideInRight 0.3s ease-out;
    }

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

    .spinner-border-sm {
      width: 1rem;
      height: 1rem;
    }

    .input-group-text {
      background-color: #f8f9fa;
      border-right: none;
    }

    .form-control:focus + .input-group-text {
      border-color: #86b7fe;
    }

    .card {
      transition: transform 0.2s ease-in-out;
    }

    .card:hover {
      transform: translateY(-2px);
    }
  </style>
</body>
</html>