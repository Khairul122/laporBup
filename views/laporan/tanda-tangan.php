<?php
$title = 'Tanda Tangan Laporan - LaporBup';
include 'template/header.php';
?>

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
              <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                  <h3 class="page-title mb-1">
                    <i class="fas fa-signature"></i>
                    Tanda Tangan Laporan
                  </h3>
                  <p class="text-muted mb-0">
                    <?php
                    if ($laporan) {
                        if ($_GET['type'] === 'opd') {
                            echo 'Laporan OPD: ' . htmlspecialchars($laporan['nama_kegiatan']);
                        } else {
                            echo 'Laporan Camat: ' . htmlspecialchars($laporan['nama_pelapor']);
                        }
                    } else {
                        if ($_GET['type'] === 'opd') {
                            echo 'Pengaturan Tanda Tangan Global untuk Laporan OPD';
                        } else {
                            echo 'Pengaturan Tanda Tangan Global untuk Laporan Camat';
                        }
                    }
                    ?>
                  </p>
                </div>
                <div>
                  <a href="index.php?controller=laporan&action=index&tab=<?php echo htmlspecialchars($_GET['type']); ?>" class="btn btn-secondary me-2">
                    <i class="fas fa-arrow-left"></i> Kembali
                  </a>
                  <button type="button" class="btn btn-success" id="generatePDFBtn">
                    <i class="fas fa-file-pdf"></i> Generate PDF
                  </button>
                </div>
              </div>

              <!-- Toast Notifications Container -->
              <div id="toastContainer" style="position: fixed; top: 20px; right: 20px; z-index: 9999;"></div>

              <!-- Signature Form Section -->
              <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                  <h5 class="mb-0">
                    <i class="fas fa-edit text-primary me-2"></i>
                    Form Tanda Tangan
                    <?php if (!$laporan): ?>
                      <span class="badge bg-info ms-2">Global</span>
                    <?php endif; ?>
                  </h5>
                </div>
                <div class="card-body">
                  <form id="signatureForm" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($_GET['id']); ?>">
                    <input type="hidden" name="type" value="<?php echo htmlspecialchars($_GET['type']); ?>">

                    <div class="row">
                      <!-- Signature Details -->
                      <div class="col-12">
                        <div class="row">
                          <div class="col-md-12">
                            <div class="mb-3">
                              <label for="nama_penanda_tangan" class="form-label">
                                <i class="fas fa-user text-muted me-2"></i>
                                Nama Penandatangan <span class="text-danger">*</span>
                              </label>
                              <input type="text"
                                     class="form-control"
                                     id="nama_penanda_tangan"
                                     name="nama_penanda_tangan"
                                     placeholder="Masukkan nama penandatangan"
                                     value="<?php echo $signature ? htmlspecialchars($signature['nama_penanda_tangan']) : 'RAHMAD HIDAYAT, S.Pd'; ?>"
                                     required>
                            </div>
                          </div>

                          <div class="col-md-12">
                            <div class="mb-3">
                              <label for="jabatan_penanda_tangan" class="form-label">
                                <i class="fas fa-briefcase text-muted me-2"></i>
                                Jabatan Penandatangan <span class="text-danger">*</span>
                              </label>
                              <input type="text"
                                     class="form-control"
                                     id="jabatan_penanda_tangan"
                                     name="jabatan_penanda_tangan"
                                     placeholder="Masukkan jabatan penandatangan"
                                     value="<?php echo $signature ? htmlspecialchars($signature['jabatan_penanda_tangan']) : 'Plt. KEPALA DINAS KOMUNIKASI DAN INFORMATIKA'; ?>"
                                     required>
                            </div>
                          </div>

                          <div class="col-md-12">
                            <div class="mb-3">
                              <label for="jabatan" class="form-label">
                                <i class="fas fa-building text-muted me-2"></i>
                                Jabatan Instansi <span class="text-danger">*</span>
                              </label>
                              <input type="text"
                                     class="form-control"
                                     id="jabatan"
                                     name="jabatan"
                                     placeholder="Masukkan jabatan instansi"
                                     value="<?php echo $signature ? htmlspecialchars($signature['jabatan']) : 'DINAS KOMUNIKASI DAN INFORMATIKA KABUPATEN MANDAILING NATAL'; ?>"
                                     required>
                            </div>
                          </div>

                          <div class="col-md-12">
                            <div class="mb-3">
                              <label for="nip" class="form-label">
                                <i class="fas fa-id-card text-muted me-2"></i>
                                NIP
                              </label>
                              <input type="text"
                                     class="form-control"
                                     id="nip"
                                     name="nip"
                                     placeholder="Masukkan NIP"
                                     value="<?php echo $signature ? htmlspecialchars($signature['nip']) : '19730417 199903 1 003'; ?>">
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Automatic Date Info -->
                    <div class="row">
                      <div class="col-12">
                        <div class="alert alert-info">
                          <i class="fas fa-info-circle me-2"></i>
                          <strong>Informasi:</strong> Tempat dan tanggal akan digenerate otomatis dengan format:
                          <br><strong>"Panyabungan, Hari Tanggal Bulan Tahun"</strong>
                          <br>Contoh: Panyabungan, Selasa 30 Oktober 2025
                        </div>
                      </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="row">
                      <div class="col-12">
                        <div class="d-flex gap-2 justify-content-end">
                          <button type="button" class="btn btn-secondary" id="resetBtn">
                            <i class="fas fa-times me-2"></i> Reset
                          </button>
                          <button type="submit" class="btn btn-primary" id="saveBtn">
                            <i class="fas fa-save me-2"></i> Simpan Tanda Tangan
                          </button>
                        </div>
                      </div>
                    </div>
                  </form>
                </div>
              </div>

              <!-- Preview Section -->
              <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                  <h5 class="mb-0">
                    <i class="fas fa-file-alt text-info me-2"></i>
                    Preview Tanda Tangan
                  </h5>
                </div>
                <div class="card-body">
                  <div class="border rounded p-4 bg-white" style="min-height: 200px;">
                    <div class="text-end">
                      <!-- Tempat dan Tanggal -->
                      <div class="mb-3">
                        <span id="previewTempatTanggal" class="font-italic">
                          Panyabungan, <?php
                          $hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                          $bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                                     'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                          echo $hari[date('w')] . ' ' . date('d') . ' ' . $bulan[date('n') - 1] . ' ' . date('Y');
                          ?>
                        </span>
                      </div>

                      <!-- Jabatan -->
                      <div class="mb-4">
                        <strong id="previewJabatan" style="text-transform: uppercase;">
                          <?php
                          if ($signature) {
                              echo htmlspecialchars($signature['jabatan_penanda_tangan']);
                          } else {
                              echo 'Plt. KEPALA DINAS KOMUNIKASI DAN INFORMATIKA';
                          }
                          ?>
                        </strong>
                      </div>

                      <!-- Space for Signature -->
                      <div class="mb-3" style="height: 60px; border-bottom: 1px solid #ccc; width: 200px; margin-left: auto;">
                      </div>

                      <!-- Nama -->
                      <div class="mb-1">
                        <strong id="previewNama">
                          <?php
                          if ($signature) {
                              echo htmlspecialchars($signature['nama_penanda_tangan']);
                          } else {
                              echo 'RAHMAD HIDAYAT, S.Pd';
                          }
                          ?>
                        </strong>
                      </div>

                      <!-- NIP -->
                      <div>
                        <small id="previewNIP">
                          NIP. <?php
                          if ($signature) {
                              echo htmlspecialchars($signature['nip']);
                          } else {
                              echo '19730417 199903 1 003';
                          }
                          ?>
                        </small>
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
  </div>

  <?php include 'template/script.php'; ?>

  <script>
  document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('signatureForm');
    const saveBtn = document.getElementById('saveBtn');
    const resetBtn = document.getElementById('resetBtn');
    const generatePDFBtn = document.getElementById('generatePDFBtn');

    // Show toast notifications on page load
    <?php if (isset($_SESSION['success'])): ?>
      showToast("<?php echo addslashes($_SESSION['success']); unset($_SESSION['success']); ?>", 'success');
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
      showToast("<?php echo addslashes($_SESSION['error']); unset($_SESSION['error']); ?>", 'error');
    <?php endif; ?>

    // Update preview on input change
    function updatePreview() {
      const nama = document.getElementById('nama_penanda_tangan').value;
      const jabatan = document.getElementById('jabatan_penanda_tangan').value;
      const nip = document.getElementById('nip').value;

      document.getElementById('previewNama').textContent = nama || 'Nama Penandatangan';
      document.getElementById('previewJabatan').textContent = jabatan ? jabatan.toUpperCase() : 'JABATAN';
      document.getElementById('previewNIP').textContent = 'NIP. ' + (nip || 'NIP');
    }

    // Add event listeners for real-time preview
    document.getElementById('nama_penanda_tangan').addEventListener('input', updatePreview);
    document.getElementById('jabatan_penanda_tangan').addEventListener('input', updatePreview);
    document.getElementById('nip').addEventListener('input', updatePreview);

    // Form submission
    form.addEventListener('submit', function(e) {
      e.preventDefault();

      const formData = new FormData(form);

      // Show loading state
      saveBtn.disabled = true;
      saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';

      fetch('index.php?controller=laporan&action=uploadTandaTangan', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          showToast(data.message, 'success');
          // Refresh page after 2 seconds to show updated data
          setTimeout(() => {
            window.location.reload();
          }, 2000);
        } else {
          showToast(data.message, 'error');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        showToast('Terjadi kesalahan. Silakan coba lagi.', 'error');
      })
      .finally(() => {
        // Reset button state
        saveBtn.disabled = false;
        saveBtn.innerHTML = '<i class="fas fa-save me-2"></i> Simpan Tanda Tangan';
      });
    });

    // Reset form
    resetBtn.addEventListener('click', function() {
      if (confirm('Apakah Anda yakin ingin mereset form?')) {
        form.reset();
        updatePreview();
        showToast('Form telah direset', 'success');
      }
    });

    // Generate PDF
    generatePDFBtn.addEventListener('click', function() {
      const id = <?php echo json_encode($_GET['id']); ?>;
      const type = <?php echo json_encode($_GET['type']); ?>;

      window.open(`index.php?controller=laporan&action=generatePDFWithSignature&id=${id}&type=${type}`, '_blank');
    });

    // Toast notification function
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
        <button type="button" class="btn-close btn-close-white ms-3" onclick="document.getElementById('${toastId}').remove()"></button>
      `;

      toastContainer.appendChild(toast);

      // Auto remove after 5 seconds
      setTimeout(() => {
        const toastElement = document.getElementById(toastId);
        if (toastElement) {
          toastElement.style.animation = 'slideOutRight 0.3s ease-out';
          setTimeout(() => toastElement.remove(), 300);
        }
      }, 5000);
    }

    // Add CSS animations
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
  });
  </script>
</body>
</html>