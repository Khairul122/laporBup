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
                  <h2 class="page-title">Manajemen Wilayah</h2>
                </div>
              </div>

              <!-- Statistics Cards -->
              <div class="row mb-4">
                <div class="col-md-4">
                  <div class="card bg-primary text-white">
                    <div class="card-body">
                      <div class="d-flex justify-content-between align-items-center">
                        <div>
                          <h6 class="card-title mb-1">Total Kecamatan</h6>
                          <h2 class="mb-0"><?php echo number_format($statistics['total_kecamatan']); ?></h2>
                        </div>
                        <div class="fs-1 opacity-75">
                          <i class="mdi mdi-map-marker-multiple"></i>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
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

              <!-- Main Card -->
              <div class="card">
                <div class="card-header">
                  <ul class="nav nav-tabs card-header-tabs" id="wilayahTab" role="tablist">
                    <li class="nav-item" role="presentation">
                      <button class="nav-link <?php echo $activeTab === 'kecamatan' ? 'active' : ''; ?>"
                              id="kecamatan-tab" data-bs-toggle="tab" data-bs-target="#kecamatan"
                              type="button" role="tab" aria-controls="kecamatan"
                              aria-selected="<?php echo $activeTab === 'kecamatan' ? 'true' : 'false'; ?>">
                        <i class="mdi mdi-map-marker-multiple me-2"></i> Kecamatan
                      </button>
                    </li>
                    <li class="nav-item" role="presentation">
                      <button class="nav-link <?php echo $activeTab === 'desa' ? 'active' : ''; ?>"
                              id="desa-tab" data-bs-toggle="tab" data-bs-target="#desa"
                              type="button" role="tab" aria-controls="desa"
                              aria-selected="<?php echo $activeTab === 'desa' ? 'true' : 'false'; ?>">
                        <i class="mdi mdi-home-map-marker me-2"></i> Desa
                      </button>
                    </li>
                  </ul>
                </div>
                <div class="card-body">
                  <div class="tab-content" id="wilayahTabContent">
                    <!-- Kecamatan Tab -->
                    <div class="tab-pane fade <?php echo $activeTab === 'kecamatan' ? 'show active' : ''; ?>"
                         id="kecamatan" role="tabpanel" aria-labelledby="kecamatan-tab">

                      <!-- Filter and Actions -->
                      <div class="row mb-3">
                        <div class="col-md-6">
                          <form method="GET" class="d-flex">
                            <input type="hidden" name="controller" value="wilayah">
                            <input type="hidden" name="action" value="index">
                            <input type="hidden" name="tab" value="kecamatan">
                            <input type="text" class="form-control me-2" name="search"
                                   placeholder="Cari kecamatan..." value="<?php echo htmlspecialchars($search); ?>">
                            <button type="submit" class="btn btn-outline-primary">
                              <i class="mdi mdi-magnify"></i>
                            </button>
                          </form>
                        </div>
                        <div class="col-md-6 text-end">
                          <a href="index.php?controller=wilayah&action=formKecamatan"
                             class="btn btn-primary">
                            <i class="mdi mdi-plus-circle me-2"></i> Tambah Kecamatan
                          </a>
                        </div>
                      </div>

                      <!-- Table Kecamatan -->
                      <div class="table-responsive">
                        <table class="table table-striped table-hover">
                          <thead class="table-dark">
                            <tr>
                              <th width="50">No</th>
                              <th>Nama Kecamatan</th>
                              <th width="100" class="text-center">Aksi</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php
                            // Temp debug - tampilkan info variabel
                            echo '<tr><td colspan="3" style="background: #f0f0f0; font-size: 12px;">';
                            echo 'Tab: ' . ($activeTab ?? 'null') . ' | Count: ' . count($wilayahData ?? []) . ' | Page: ' . ($page ?? 1);
                            echo '</td></tr>';

                            if (empty($wilayahData)): ?>
                              <tr>
                                <td colspan="3" class="text-center text-muted">Tidak ada data kecamatan</td>
                              </tr>
                            <?php else: ?>
                              <?php $no = (($result['current_page'] ?? $page ?? 1) - 1) * ($limit ?? 10) + 1; ?>
                              <?php foreach ($wilayahData as $kecamatan): ?>
                                <tr>
                                  <td><?php echo $no++; ?></td>
                                  <td><?php echo htmlspecialchars($kecamatan['nama_kecamatan'] ?? ''); ?></td>
                                  <td class="text-center">
                                    <div class="btn-group" role="group">
                                      <a href="index.php?controller=wilayah&action=formKecamatan&id=<?php echo $kecamatan['id_kecamatan'] ?? ''; ?>"
                                         class="btn btn-sm btn-warning"
                                         title="Edit">
                                        <i class="mdi mdi-pencil"></i>
                                      </a>
                                      <button type="button" class="btn btn-sm btn-danger"
                                              onclick="deleteKecamatan(<?php echo $kecamatan['id_kecamatan'] ?? ''; ?>)"
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
                        <nav aria-label="Page navigation" class="mt-3">
                          <ul class="pagination justify-content-center">
                            <?php if ($result['current_page'] > 1): ?>
                              <li class="page-item">
                                <a class="page-link" href="?controller=wilayah&action=index&tab=kecamatan&page=<?php echo $result['current_page'] - 1; ?>&search=<?php echo urlencode($search); ?>">
                                  <i class="mdi mdi-chevron-left"></i> Previous
                                </a>
                              </li>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                              <li class="page-item <?php echo $i == $result['current_page'] ? 'active' : ''; ?>">
                                <a class="page-link" href="?controller=wilayah&action=index&tab=kecamatan&page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>"><?php echo $i; ?></a>
                              </li>
                            <?php endfor; ?>

                            <?php if ($result['current_page'] < $totalPages): ?>
                              <li class="page-item">
                                <a class="page-link" href="?controller=wilayah&action=index&tab=kecamatan&page=<?php echo $result['current_page'] + 1; ?>&search=<?php echo urlencode($search); ?>">
                                  Next <i class="mdi mdi-chevron-right"></i>
                                </a>
                              </li>
                            <?php endif; ?>
                          </ul>
                        </nav>
                      <?php endif; ?>
                    </div>

                    <!-- Desa Tab -->
                    <div class="tab-pane fade <?php echo $activeTab === 'desa' ? 'show active' : ''; ?>"
                         id="desa" role="tabpanel" aria-labelledby="desa-tab">

                      <!-- Filter and Actions -->
                      <div class="row mb-3">
                        <div class="col-md-8">
                          <form method="GET" class="row g-2">
                            <input type="hidden" name="controller" value="wilayah">
                            <input type="hidden" name="action" value="index">
                            <input type="hidden" name="tab" value="desa">
                            <div class="col-md-5">
                              <input type="text" class="form-control" name="search"
                                     placeholder="Cari desa atau kecamatan..." value="<?php echo htmlspecialchars($search); ?>">
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
                            <div class="col-md-3">
                              <button type="submit" class="btn btn-outline-primary w-100">
                                <i class="mdi mdi-magnify me-2"></i> Cari
                              </button>
                            </div>
                          </form>
                        </div>
                        <div class="col-md-4 text-end">
                          <a href="index.php?controller=wilayah&action=formDesa"
                             class="btn btn-primary">
                            <i class="mdi mdi-plus-circle me-2"></i> Tambah Desa
                          </a>
                        </div>
                      </div>

                      <!-- Table Desa -->
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
                            <?php if (empty($wilayahData)): ?>
                              <tr>
                                <td colspan="4" class="text-center text-muted">Tidak ada data desa</td>
                              </tr>
                            <?php else: ?>
                              <?php $no = ($result['current_page'] - 1) * $limit + 1; ?>
                              <?php foreach ($wilayahData as $desa): ?>
                                <?php // Skip jika nama desa kosong atau null ?>
                                <?php if (empty($desa['nama_desa'])) continue; ?>
                                <tr>
                                  <td><?php echo $no++; ?></td>
                                  <td><?php echo htmlspecialchars($desa['nama_desa']); ?></td>
                                  <td><?php echo htmlspecialchars($desa['nama_kecamatan']); ?></td>
                                  <td class="text-center">
                                    <div class="btn-group" role="group">
                                      <a href="index.php?controller=wilayah&action=formDesa&id=<?php echo $desa['id_desa'] ?? ''; ?>"
                                         class="btn btn-sm btn-warning"
                                         title="Edit">
                                        <i class="mdi mdi-pencil"></i>
                                      </a>
                                      <button type="button" class="btn btn-sm btn-danger"
                                              onclick="deleteDesa(<?php echo $desa['id_desa'] ?? ''; ?>)"
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
                        <nav aria-label="Page navigation" class="mt-3">
                          <ul class="pagination justify-content-center">
                            <?php if ($result['current_page'] > 1): ?>
                              <li class="page-item">
                                <a class="page-link" href="?controller=wilayah&action=index&tab=desa&page=<?php echo $result['current_page'] - 1; ?>&search=<?php echo urlencode($search); ?>&kecamatan_filter=<?php echo urlencode($_GET['kecamatan_filter'] ?? ''); ?>">
                                  <i class="mdi mdi-chevron-left"></i> Previous
                                </a>
                              </li>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                              <li class="page-item <?php echo $i == $result['current_page'] ? 'active' : ''; ?>">
                                <a class="page-link" href="?controller=wilayah&action=index&tab=desa&page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&kecamatan_filter=<?php echo urlencode($_GET['kecamatan_filter'] ?? ''); ?>"><?php echo $i; ?></a>
                              </li>
                            <?php endfor; ?>

                            <?php if ($result['current_page'] < $totalPages): ?>
                              <li class="page-item">
                                <a class="page-link" href="?controller=wilayah&action=index&tab=desa&page=<?php echo $result['current_page'] + 1; ?>&search=<?php echo urlencode($search); ?>&kecamatan_filter=<?php echo urlencode($_GET['kecamatan_filter'] ?? ''); ?>">
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
    </div>
  </div>
  <?php include 'template/script.php'; ?>

  <!-- Custom Scripts -->
  <script>
    // Delete kecamatan
    function deleteKecamatan(id) {
      // First check if there are related desa
      fetch(`index.php?controller=wilayah&action=getKecamatanStats&id=${id}`, {
        method: 'GET',
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(response => response.json())
      .then(data => {
        let confirmMessage = 'Apakah Anda yakin ingin menghapus kecamatan ini?';

        if (data.relatedDesaCount > 0) {
          confirmMessage = `Apakah Anda yakin ingin menghapus kecamatan ini? ${data.relatedDesaCount} desa terkait juga akan ikut dihapus.\n\nDaftar desa yang akan dihapus:\n${data.relatedDesaList.join('\n')}`;
        }

        if (confirm(confirmMessage)) {
          const form = document.createElement('form');
          form.method = 'POST';
          form.action = 'index.php?controller=wilayah&action=deleteKecamatan';

          const input = document.createElement('input');
          input.type = 'hidden';
          input.name = 'id';
          input.value = id;

          form.appendChild(input);
          document.body.appendChild(form);
          form.submit();
        }
      })
      .catch(error => {
        console.error('Error checking kecamatan stats:', error);
        // Fallback to simple confirm if stats check fails
        if (confirm('Apakah Anda yakin ingin menghapus kecamatan ini?')) {
          const form = document.createElement('form');
          form.method = 'POST';
          form.action = 'index.php?controller=wilayah&action=deleteKecamatan';

          const input = document.createElement('input');
          input.type = 'hidden';
          input.name = 'id';
          input.value = id;

          form.appendChild(input);
          document.body.appendChild(form);
          form.submit();
        }
      });
    }

    // Delete desa
    function deleteDesa(id) {
      if (confirm('Apakah Anda yakin ingin menghapus desa ini?')) {
        fetch('index.php?controller=wilayah&action=deleteDesa', {
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
            // Reload halaman dengan parameter tab yang benar
            setTimeout(() => {
              const currentUrl = new URL(window.location);
              currentUrl.searchParams.set('tab', 'desa');
              window.location.href = currentUrl.toString();
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
          from {
            transform: translateX(100%);
            opacity: 0;
          }
          to {
            transform: translateX(0);
            opacity: 1;
          }
        }
        @keyframes slideOut {
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

      console.log('Custom toast shown');
    }

    // Show notifications on page load
    <?php if (isset($_SESSION['success'])): ?>
      showNotification('<?php echo addslashes($_SESSION['success']); ?>', 'success');
      <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
      showNotification('<?php echo addslashes($_SESSION['error']); ?>', 'error');
      <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    // Tab persistence
    document.addEventListener('DOMContentLoaded', function() {
      const tabButtons = document.querySelectorAll('#wilayahTab button[data-bs-toggle="tab"]');
      tabButtons.forEach(button => {
        button.addEventListener('shown.bs.tab', function (e) {
          const target = e.target.getAttribute('data-bs-target');
          const tab = target === '#kecamatan' ? 'kecamatan' : 'desa';

          // Update URL without page reload
          const url = new URL(window.location);
          url.searchParams.set('tab', tab);
          window.history.replaceState({}, '', url);
        });
      });
    });
  </script>
</body>
</html>