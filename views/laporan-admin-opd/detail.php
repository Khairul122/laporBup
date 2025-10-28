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

              <?php if ($laporan): ?>
                <!-- Header Section -->
                <div class="card mb-4">
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col-md-8">
                        <div class="d-flex align-items-center">
                          <div class="avatar-lg me-3">
                            <div class="avatar-title bg-primary bg-opacity-10 rounded">
                              <i class="mdi mdi-office-building text-primary"></i>
                            </div>
                          </div>
                          <div>
                            <h2 class="mb-1"><?php echo htmlspecialchars($laporan['nama_opd'] ?? ''); ?></h2>
                            <p class="text-muted mb-0">
                              <?php echo htmlspecialchars($laporan['nama_kegiatan'] ?? ''); ?>
                            </p>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-4 text-md-end">
                        <div class="d-flex justify-content-md-end gap-2">
                          <?php
                          $statusConfig = [
                            'baru' => 'warning',
                            'diproses' => 'info',
                            'selesai' => 'success'
                          ];
                          ?>
                          <span class="badge bg-<?php echo $statusConfig[$laporan['status_laporan'] ?? 'baru']; ?> fs-6">
                            <?php echo ucfirst($laporan['status_laporan'] ?? 'baru'); ?>
                          </span>
                          <span class="badge bg-secondary fs-6">#<?php echo $laporan['id_laporan_opd'] ?? ''; ?></span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <!-- Main Content -->
                  <div class="col-lg-8">
                    <!-- Detail Informasi Laporan -->
                    <div class="card mb-4">
                      <div class="card-header">
                        <h5 class="card-title mb-0">Informasi Laporan</h5>
                      </div>
                      <div class="card-body">
                        <div class="row mb-3">
                          <div class="col-md-4">
                            <label class="text-muted small">Nama OPD</label>
                            <h6 class="mb-0"><?php echo htmlspecialchars($laporan['nama_opd']); ?></h6>
                          </div>
                          <div class="col-md-4">
                            <label class="text-muted small">Tujuan</label>
                            <h6 class="mb-0"><?php echo htmlspecialchars(ucfirst($laporan['tujuan'])); ?></h6>
                          </div>
                          <div class="col-md-4">
                            <label class="text-muted small">Status</label>
                            <h6 class="mb-0">
                              <span class="badge bg-<?php echo $statusConfig[$laporan['status_laporan']]; ?>">
                                <?php echo ucfirst($laporan['status_laporan']); ?>
                              </span>
                            </h6>
                          </div>
                        </div>

                        <div class="mb-3">
                          <label class="text-muted small">Nama Kegiatan</label>
                          <h5 class="text-primary"><?php echo htmlspecialchars($laporan['nama_kegiatan']); ?></h5>
                        </div>

                        <div class="mb-3">
                          <label class="text-muted small">Uraian Laporan</label>
                          <div class="bg-light p-3 rounded">
                            <?php echo nl2br(htmlspecialchars($laporan['uraian_laporan'])); ?>
                          </div>
                        </div>

                        <?php if ($laporan['upload_file']): ?>
                          <div class="mb-0">
                            <label class="text-muted small">File Lampiran</label>
                            <div class="bg-light p-3 rounded">
                              <div class="d-flex align-items-center justify-content-between">
                                <div>
                                  <i class="mdi mdi-paperclip text-muted me-2"></i>
                                  <?php echo htmlspecialchars(basename($laporan['upload_file'])); ?>
                                </div>
                                <a href="<?php echo htmlspecialchars($laporan['upload_file']); ?>" target="_blank"
                                   class="btn btn-sm btn-outline-primary">
                                  <i class="mdi mdi-download me-1"></i>Download
                                </a>
                              </div>
                            </div>
                          </div>
                        <?php endif; ?>
                      </div>
                    </div>
                  </div>

                  <!-- Sidebar -->
                  <div class="col-lg-4">
                    <!-- Informasi Tambahan -->
                    <div class="card mb-4">
                      <div class="card-header">
                        <h5 class="card-title mb-0">Informasi Laporan</h5>
                      </div>
                      <div class="card-body">
                        <table class="table table-sm table-borderless">
                          <tr>
                            <td class="text-muted" width="40%">ID Laporan</td>
                            <td>#<?php echo $laporan['id_laporan_opd']; ?></td>
                          </tr>
                          <tr>
                            <td class="text-muted">Dibuat</td>
                            <td>
                              <?php
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
                              echo "$tanggal_indo<br><small>$jam</small>";
                              ?>
                            </td>
                          </tr>
                          <?php if ($laporan['updated_at'] !== $laporan['created_at']): ?>
                            <tr>
                              <td class="text-muted">Diperbarui</td>
                              <td>
                                <?php
                                $timestamp_update = strtotime($laporan['updated_at']);
                                $nama_hari_update = $hari[date('w', $timestamp_update)];
                                $tanggal_update = date('d', $timestamp_update);
                                $nama_bulan_update = $bulan[date('n', $timestamp_update) - 1];
                                $tahun_update = date('Y', $timestamp_update);
                                $jam_update = date('H:i', $timestamp_update);
                                $tanggal_update_indo = "$nama_hari_update, $tanggal_update $nama_bulan_update $tahun_update";
                                echo "$tanggal_update_indo<br><small>$jam_update</small>";
                                ?>
                              </td>
                            </tr>
                          <?php endif; ?>
                          <?php if ($laporan['username']): ?>
                            <tr>
                              <td class="text-muted">Pelapor</td>
                              <td>
                                <strong><?php echo htmlspecialchars($laporan['username']); ?></strong>
                                <?php if ($laporan['jabatan']): ?>
                                  <br><small class="text-muted"><?php echo htmlspecialchars($laporan['jabatan']); ?></small>
                                <?php endif; ?>
                              </td>
                            </tr>
                          <?php endif; ?>
                        </table>
                      </div>
                    </div>

                    <!-- Aksi -->
                    <div class="card">
                      <div class="card-header">
                        <h5 class="card-title mb-0">Aksi</h5>
                      </div>
                      <div class="card-body">
                        <div class="d-grid gap-2">
                          <a href="index.php?controller=laporanOPDAdmin&action=edit&id=<?php echo $laporan['id_laporan_opd']; ?>"
                             class="btn btn-primary">
                            <i class="mdi mdi-pencil me-2"></i>Edit Laporan
                          </a>

                          <form method="POST" action="index.php?controller=laporanOPDAdmin&action=delete&id=<?php echo $laporan['id_laporan_opd']; ?>"
                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus laporan ini?');">
                            <button type="submit" class="btn btn-outline-danger w-100">
                              <i class="mdi mdi-delete me-2"></i>Hapus Laporan
                            </button>
                          </form>

                          <a href="index.php?controller=laporanOPDAdmin&action=index" class="btn btn-outline-secondary">
                            <i class="mdi mdi-arrow-left me-2"></i>Kembali
                          </a>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              <?php else: ?>
                <!-- Error State -->
                <div class="card">
                  <div class="card-body text-center py-5">
                    <i class="mdi mdi-alert-circle-outline text-muted display-1"></i>
                    <h4 class="mt-3">Data Tidak Ditemukan!</h4>
                    <p class="text-muted">Laporan yang Anda cari tidak ditemukan dalam database.</p>
                    <a href="index.php?controller=laporanOPDAdmin&action=index" class="btn btn-primary">
                      Kembali ke Daftar Laporan
                    </a>
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

  <style>
    .avatar-lg {
      width: 64px;
      height: 64px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.5rem;
    }

    .badge {
      font-weight: 500;
    }

    .card {
      border: 1px solid #e9ecef;
      border-radius: 0.5rem;
    }

    .table-sm td {
      padding: 0.5rem;
      vertical-align: top;
    }

    .breadcrumb {
      background-color: transparent;
      padding: 0;
      margin-bottom: 1rem;
    }

    .breadcrumb-item + .breadcrumb-item::before {
      content: ">";
      color: #6c757d;
    }
  </style>

  <script>
    // Smooth scroll to top when page loads
    document.addEventListener('DOMContentLoaded', function() {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    // Add keyboard shortcuts
    document.addEventListener('keydown', function(e) {
      // E untuk edit
      if (e.key === 'e' && !e.ctrlKey && !e.metaKey && e.target.tagName !== 'INPUT' && e.target.tagName !== 'TEXTAREA') {
        e.preventDefault();
        window.location.href = `index.php?controller=laporanOPDAdmin&action=edit&id=<?php echo $laporan['id_laporan_opd'] ?? 0; ?>`;
      }

      // Escape untuk kembali
      if (e.key === 'Escape') {
        window.location.href = 'index.php?controller=laporanOPDAdmin&action=index';
      }
    });

    // Print functionality
    function printLaporan() {
      window.print();
    }

    // Copy link functionality
    function copyLink() {
      const url = window.location.href;
      navigator.clipboard.writeText(url).then(() => {
        showToast('Link berhasil disalin!', 'success');
      });
    }

    // Toast notification
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
      toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
      toastContainer.innerHTML = toastHtml;
      document.body.appendChild(toastContainer);

      const toast = new bootstrap.Toast(toastContainer.querySelector('.toast'));
      toast.show();

      setTimeout(() => {
        toastContainer.remove();
      }, 5000);
    }

    // Show success/error messages as toast if exist
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