<?php include('template/header.php'); ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/brands.min.css" integrity="sha512-APz+y2aUHgE2S3i8/CEM5A6z+oGnf5GBlhQYCzBjVjG6HkpKzzAfmzrPwKs6wI9M6PqH+4yKv6QyBvJNvNxg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

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

              <!-- Page Header -->
              <div class="page-header mb-4">
                <div class="d-flex align-items-center justify-content-between">
                  <div>
                    <h3 class="page-title mb-1">Data Pelapor</h3>
                    <nav aria-label="breadcrumb">
                      <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php?controller=dashboard&action=admin">Dashboard</a></li>
                        <li class="breadcrumb-item active">Data Pelapor</li>
                      </ol>
                    </nav>
                  </div>
                  <div class="page-actions">
                    <a href="index.php?controller=dataPelapor&action=form" class="btn btn-primary btn-sm">
                      <i class="fas fa-plus-circle"></i> Tambah Pelapor
                    </a>
                                      </div>
                </div>
              </div>

              
              <!-- Search and Filter Full Width -->
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
                                <i class="fas fa-map-marker-alt"></i> Camat
                              </option>
                              <option value="opd" <?php echo $role === 'opd' ? 'selected' : ''; ?>>
                                <i class="fas fa-building"></i> OPD
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
                        <div class="col-md-2">
                          <label class="form-label small text-muted">&nbsp;</label>
                          <button type="button" class="btn btn-secondary w-100" onclick="resetFilter()">
                            <i class="fas fa-arrow-rotate-left"></i> Reset
                          </button>
                        </div>
                       
                      </form>

                      <!-- Info Bar -->
                      <div class="row mt-3 pt-3 border-top">
                        <div class="col-md-4">
                          <div class="d-flex align-items-center">
                            <i class="fas fa-database text-primary me-2"></i>
                            <div>
                              <small class="text-muted">Total Data</small>
                              <div class="fw-bold"><?php echo number_format($result['total']); ?> pelapor</div>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-4">
                          <div class="d-flex align-items-center justify-content-center">
                            <i class="fas fa-file-alt text-info me-2"></i>
                            <div class="text-center">
                              <small class="text-muted">Halaman</small>
                              <div class="fw-bold"><?php echo $result['page']; ?> dari <?php echo $result['total_pages']; ?></div>
                            </div>
                          </div>
                        </div>
                        </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Data Table -->
              <div class="row mb-4">
                <div class="col-12">
                  <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 pt-4 pb-3">
                      <h5 class="card-title mb-0">
                        <i class="fas fa-users text-primary"></i>
                        Daftar Data Pelapor
                      </h5>
                    </div>
                    <div class="card-body pb-4">
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

                        <!-- Pagination -->
                        <?php if ($result['total_pages'] > 1): ?>
                          <div class="d-flex justify-content-between align-items-center mt-4">
                            <div class="text-muted">
                              Menampilkan <?php echo count($result['data']); ?> dari <?php echo $result['total']; ?> data
                            </div>
                            <nav>
                              <ul class="pagination pagination-sm mb-0">
                                <?php if ($result['page'] > 1): ?>
                                  <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $result['page'] - 1; ?>&search=<?php echo urlencode($search); ?>&role=<?php echo $role; ?>">
                                      <i class="fas fa-chevron-left"></i>
                                    </a>
                                  </li>
                                <?php endif; ?>

                                <?php
                                $startPage = max(1, $result['page'] - 2);
                                $endPage = min($result['total_pages'], $result['page'] + 2);

                                if ($startPage > 1) {
                                  echo '<li class="page-item"><a class="page-link" href="?page=1&search=' . urlencode($search) . '&role=' . $role . '">1</a></li>';
                                  if ($startPage > 2) {
                                    echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                  }
                                }

                                for ($i = $startPage; $i <= $endPage; $i++) {
                                  $activeClass = $i == $result['page'] ? 'active' : '';
                                  echo '<li class="page-item ' . $activeClass . '">
                                          <a class="page-link" href="?page=' . $i . '&search=' . urlencode($search) . '&role=' . $role . '">' . $i . '</a>
                                        </li>';
                                }

                                if ($endPage < $result['total_pages']) {
                                  if ($endPage < $result['total_pages'] - 1) {
                                    echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                  }
                                  echo '<li class="page-item">
                                          <a class="page-link" href="?page=' . $result['total_pages'] . '&search=' . urlencode($search) . '&role=' . $role . '">' . $result['total_pages'] . '</a>
                                        </li>';
                                }
                                ?>

                                <?php if ($result['page'] < $result['total_pages']): ?>
                                  <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $result['page'] + 1; ?>&search=<?php echo urlencode($search); ?>&role=<?php echo $role; ?>">
                                      <i class="fas fa-chevron-right"></i>
                                    </a>
                                  </li>
                                <?php endif; ?>
                              </ul>
                            </nav>
                          </div>
                        <?php endif; ?>
                      <?php else: ?>
                        <div class="text-center py-5">
                          <i class="fas fa-users text-muted" style="font-size: 3rem;"></i>
                          <h5 class="mt-3 text-muted">Belum ada data pelapor</h5>
                          <p class="text-muted">Tambahkan data pelapor untuk memulai</p>
                          <a href="index.php?controller=dataPelapor&action=form" class="btn btn-primary">
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
// Helper function untuk format tanggal Indonesia
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

  <?php include 'template/script.php'; ?>

  <!-- Custom JavaScript -->
  <script>
    
    // Edit pelapor
    function editPelapor(id) {
      window.location.href = 'index.php?controller=dataPelapor&action=form&id=' + id;
    }

    // Function untuk format tanggal Indonesia (JavaScript version)
    function formatDateIndoJS(dateString) {
      const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
      const months = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

      const date = new Date(dateString);
      const dayName = days[date.getDay()];
      const day = date.getDate();
      const month = months[date.getMonth()];
      const year = date.getFullYear();

      return `${dayName}, ${day} ${month} ${year}`;
    }

    // Delete pelapor
    function deletePelapor(id, username) {
      if (confirm(`Apakah Anda yakin ingin menghapus pelapor "${username}"?`)) {
        fetch('index.php?controller=dataPelapor&action=delete', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: 'id=' + id
        })
        .then(response => response.json())
        .then(result => {
          if (result.success) {
            showAlert('success', result.message);
            location.reload();
          } else {
            showAlert('danger', result.message);
          }
        })
        .catch(error => {
          showAlert('danger', 'Error: ' + error.message);
        });
      }
    }

    // Export data
    function exportData() {
      const role = document.getElementById('roleFilter').value;
      const url = 'index.php?controller=dataPelapor&action=export';
      window.open(url + (role ? '?role=' + role : ''), '_blank');
    }

    // Reset filter
    function resetFilter() {
      document.getElementById('searchInput').value = '';
      document.getElementById('roleFilter').value = '';
      window.location.href = 'index.php?controller=dataPelapor';
    }

    // Show alert
    function showAlert(type, message) {
      const alertDiv = document.createElement('div');
      alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
      alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      `;

      document.querySelector('.page-header').after(alertDiv);

      setTimeout(() => {
        alertDiv.remove();
      }, 5000);
    }

    // Search form submission
    document.getElementById('searchForm').addEventListener('submit', function(e) {
      e.preventDefault();

      const search = document.getElementById('searchInput').value;
      const role = document.getElementById('roleFilter').value;

      const params = new URLSearchParams();
      if (search) params.append('search', search);
      if (role) params.append('role', role);

      window.location.href = 'index.php?controller=dataPelapor&' + params.toString();
    });

    // Auto-refresh data (optional - every 30 seconds)
    setInterval(() => {
      // Only refresh if no modal is open
      if (!document.querySelector('.modal.show')) {
        // Optional: Implement auto-refresh logic here
      }
    }, 30000);
  </script>
</body>
</html>