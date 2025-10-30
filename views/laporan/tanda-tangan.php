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
                </div>
              </div>

              <div id="toastContainer" style="position: fixed; top: 20px; right: 20px; z-index: 9999;"></div>

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
                      <div class="col-12">
                        <div class="row">
                          <div class="col-md-12 mb-3">
                            <label for="nama_penanda_tangan" class="form-label">
                              <i class="fas fa-user text-muted me-2"></i>
                              Nama Penandatangan <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="nama_penanda_tangan" name="nama_penanda_tangan" placeholder="Masukkan nama penandatangan" value="<?php echo $signature ? htmlspecialchars($signature['nama_penanda_tangan']) : 'RAHMAD HIDAYAT, S.Pd'; ?>" required>
                          </div>
                          <div class="col-md-12 mb-3">
                            <label for="jabatan_penanda_tangan" class="form-label">
                              <i class="fas fa-briefcase text-muted me-2"></i>
                              Jabatan Penandatangan <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="jabatan_penanda_tangan" name="jabatan_penanda_tangan" placeholder="Masukkan jabatan penandatangan" value="<?php echo $signature ? htmlspecialchars($signature['jabatan_penanda_tangan']) : 'Plt. KEPALA DINAS KOMUNIKASI DAN INFORMATIKA'; ?>" required>
                          </div>
                          <div class="col-md-12 mb-3">
                            <label for="pangkat" class="form-label">
                              <i class="fas fa-building text-muted me-2"></i>
                              Pangkat Instansi <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="pangkat" name="pangkat" placeholder="Masukkan pangkat instansi" value="<?php echo $signature ? htmlspecialchars($signature['pangkat']) : 'PEMBINA UTAMA MUDA '; ?>" required>
                          </div>
                          <div class="col-md-12 mb-3">
                            <label for="nip" class="form-label">
                              <i class="fas fa-id-card text-muted me-2"></i>
                              NIP
                            </label>
                            <input type="text" class="form-control" id="nip" name="nip" placeholder="Masukkan NIP" value="<?php echo $signature ? htmlspecialchars($signature['nip']) : '19730417 199903 1 003'; ?>">
                          </div>
                        </div>
                      </div>
                    </div>

                    <div class="d-flex gap-2 justify-content-end">
                      <button type="button" class="btn btn-secondary" id="resetBtn">
                        <i class="fas fa-times me-2"></i> Reset
                      </button>
                      <button type="submit" class="btn btn-primary" id="saveBtn">
                        <i class="fas fa-save me-2"></i> Simpan Tanda Tangan
                      </button>
                    </div>
                  </form>
                </div>
              </div>

              <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                  <h5 class="mb-0">
                    <i class="fas fa-file-alt text-info me-2"></i>
                    Preview Tanda Tangan
                  </h5>
                </div>
                <div class="card-body">
                  <div class="border rounded p-4 bg-white" style="min-height: 220px; display: flex; justify-content: flex-end; align-items: flex-start;">
                    <div style="width: 320px; text-align: left;">
                      <div class="mb-3">
                        <span id="previewTempatTanggal" style="font-style: italic; font-size: 13px;">
                          Panyabungan,
                          <?php
                          $hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                          $bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                          echo $bulan[date('n') - 1] . ' ' . date('Y');
                          ?>
                        </span>
                      </div>
                      <div class="mb-3">
                        <strong id="previewJabatan" style="text-transform: uppercase; font-size: 13px; line-height: 1.4;">
                          <?php
                          if ($signature) {
                            echo htmlspecialchars($signature['jabatan_penanda_tangan']);
                          } else {
                            echo 'Plt. KEPALA DINAS KOMUNIKASI DAN INFORMATIKA<br>KABUPATEN MANDAILING NATAL';
                          }
                          ?>
                        </strong>
                      </div>
                      <div class="mb-4" style="height: 60px;"></div>
                      <div class="mb-1">
                        <strong id="previewNama" style="font-size: 13px;">
                          <?php
                          if ($signature) {
                            echo htmlspecialchars($signature['nama_penanda_tangan']);
                          } else {
                            echo 'RAHMAD HIDAYAT, S.Pd';
                          }
                          ?>
                        </strong>
                      </div>
                      <div class="mb-1">
                        <em style="font-size: 12px;">PEMBINA UTAMA MUDA</em>
                      </div>
                      <div>
                        <small id="previewNIP" style="font-size: 12px;">
                          NIP.
                          <?php
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

      <?php if (isset($_SESSION['success'])): ?>
        showToast("<?php echo addslashes($_SESSION['success']); unset($_SESSION['success']); ?>", 'success');
      <?php endif; ?>

      <?php if (isset($_SESSION['error'])): ?>
        showToast("<?php echo addslashes($_SESSION['error']); unset($_SESSION['error']); ?>", 'error');
      <?php endif; ?>

      function updatePreview() {
        const nama = document.getElementById('nama_penanda_tangan').value;
        const jabatan = document.getElementById('jabatan_penanda_tangan').value;
        const pangkat = document.getElementById('pangkat').value;
        const nip = document.getElementById('nip').value;
        
        document.getElementById('previewNama').textContent = nama || 'RAHMAD HIDAYAT, S.Pd';
        document.getElementById('previewJabatan').textContent = jabatan || 'Plt. KEPALA DINAS KOMUNIKASI DAN INFORMATIKA<br>KABUPATEN MANDAILING NATAL';
        document.getElementById('previewNIP').textContent = 'NIP. ' + (nip || '19730417 199903 1 003');
        
        // Update pangkat in preview - find the <em> element in the preview area
        const previewPangkatElement = document.querySelector('.border.rounded.p-4.bg-white em');
        if (previewPangkatElement) {
          previewPangkatElement.textContent = pangkat || 'PEMBINA UTAMA MUDA';
        }
      }

      document.getElementById('nama_penanda_tangan').addEventListener('input', updatePreview);
      document.getElementById('jabatan_penanda_tangan').addEventListener('input', updatePreview);
      document.getElementById('pangkat').addEventListener('input', updatePreview);
      document.getElementById('nip').addEventListener('input', updatePreview);

      form.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(form);
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
              setTimeout(() => {
                window.location.reload();
              }, 2000);
            } else {
              showToast(data.message, 'error');
            }
          })
          .catch(() => {
            showToast('Terjadi kesalahan. Silakan coba lagi.', 'error');
          })
          .finally(() => {
            saveBtn.disabled = false;
            saveBtn.innerHTML = '<i class="fas fa-save me-2"></i> Simpan Tanda Tangan';
          });
      });

      resetBtn.addEventListener('click', function() {
        if (confirm('Apakah Anda yakin ingin mereset form?')) {
          form.reset();
          updatePreview();
          showToast('Form telah direset', 'success');
        }
      });

      generatePDFBtn.addEventListener('click', function() {
        const id = <?php echo json_encode($_GET['id']); ?>;
        const type = <?php echo json_encode($_GET['type']); ?>;
        window.open(`index.php?controller=laporan&action=generatePDFWithSignature&id=${id}&type=${type}`, '_blank');
      });

      function showToast(message, type = 'success') {
        const toastContainer = document.getElementById('toastContainer');
        const toast = document.createElement('div');
        const toastId = 'toast-' + Date.now();
        const iconHtml = type === 'success' ? '<i class="fas fa-check-circle me-2"></i>' : '<i class="fas fa-exclamation-circle me-2"></i>';
        const bgColor = type === 'success' ? '#10b981' : '#ef4444';
        toast.id = toastId;
        toast.className = 'd-flex align-items-center justify-content-between p-3 mb-2 text-white rounded shadow-lg';
        toast.style.cssText = `background: ${bgColor}; min-width: 300px; animation: slideInRight 0.3s ease-out;`;
        toast.innerHTML = `<div class="d-flex align-items-center">${iconHtml}<span>${message}</span></div><button type="button" class="btn-close btn-close-white ms-3" onclick="document.getElementById('${toastId}').remove()"></button>`;
        toastContainer.appendChild(toast);
        setTimeout(() => {
          const toastElement = document.getElementById(toastId);
          if (toastElement) {
            toastElement.style.animation = 'slideOutRight 0.3s ease-out';
            setTimeout(() => toastElement.remove(), 300);
          }
        }, 5000);
      }

      const style = document.createElement('style');
      style.textContent = `
      @keyframes slideInRight { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
      @keyframes slideOutRight { from { transform: translateX(0); opacity: 1; } to { transform: translateX(100%); opacity: 0; } }
      `;
      document.head.appendChild(style);
    });
  </script>
</body>
</html>
