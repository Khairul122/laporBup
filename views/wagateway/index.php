<?php include('template/header.php'); ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

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
                    <h3 class="page-title mb-1">
                      <i class="fab fa-whatsapp text-success"></i> WhatsApp Gateway
                    </h3>
                  </div>
                  <div class="page-actions">
                    <button class="btn btn-success btn-sm me-2" onclick="showBulkModal()">
                      <i class="fas fa-paper-plane"></i> Kirim Massal
                    </button>
                    <a href="index.php?controller=waGateway&action=form" class="btn btn-primary btn-sm">
                      <i class="fas fa-plus-circle"></i> Kirim Pesan
                    </a>
                  </div>
                </div>
              </div>

              
              <!-- Search and Filter -->
              <div class="row mb-4">
                <div class="col-12">
                  <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 pt-4 pb-3">
                      <h5 class="card-title mb-0">
                        <i class="fas fa-filter text-primary"></i>
                        Pencarian dan Filter
                      </h5>
                    </div>
                    <div class="card-body pb-4">
                      <form id="searchForm" class="row g-3">
                        <div class="col-md-4">
                          <label class="form-label small text-muted">Cari Pesan</label>
                          <div class="input-group">
                            <span class="input-group-text">
                              <i class="fas fa-search"></i>
                            </span>
                            <input type="text" class="form-control" id="searchInput" placeholder="Cari nomor tujuan atau pesan..." value="<?php echo htmlspecialchars($search); ?>">
                          </div>
                        </div>
                        <div class="col-md-2">
                          <label class="form-label small text-muted">Filter Status</label>
                          <div class="input-group">
                            <span class="input-group-text">
                              <i class="fas fa-flag"></i>
                            </span>
                            <select class="form-select" id="statusFilter">
                              <option value="">Semua Status</option>
                              <option value="sent" <?php echo $status_filter === 'sent' ? 'selected' : ''; ?>>
                                <i class="fas fa-check-circle"></i> Terkirim
                              </option>
                              <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>
                                <i class="fas fa-clock"></i> Pending
                              </option>
                              <option value="failed" <?php echo $status_filter === 'failed' ? 'selected' : ''; ?>>
                                <i class="fas fa-times-circle"></i> Gagal
                              </option>
                            </select>
                          </div>
                        </div>
                        <div class="col-md-2">
                          <label class="form-label small text-muted">Filter Tanggal</label>
                          <div class="input-group">
                            <span class="input-group-text">
                              <i class="fas fa-calendar"></i>
                            </span>
                            <input type="date" class="form-control" id="dateFilter" value="<?php echo htmlspecialchars($date_filter); ?>">
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
                              <div class="fw-bold"><?php echo number_format($result['total']); ?> pesan</div>
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
                        <div class="col-md-4 text-end">
                          <button type="button" class="btn btn-outline-success btn-sm" onclick="exportData()">
                            <i class="fas fa-download"></i> Export CSV
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Messages Table -->
              <div class="row mb-4">
                <div class="col-12">
                  <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 pt-4 pb-3">
                      <h5 class="card-title mb-0">
                        <i class="fab fa-whatsapp text-success"></i>
                        Daftar Pesan WhatsApp
                      </h5>
                    </div>
                    <div class="card-body pb-4">
                      <?php if (!empty($result['data'])): ?>
                        <div class="table-responsive">
                          <table class="table table-hover">
                            <thead>
                              <tr>
                                <th class="border-top-0">No</th>
                                <th class="border-top-0">Nomor Tujuan</th>
                                <th class="border-top-0">Pesan</th>
                                <th class="border-top-0">Tanggal Kirim</th>
                                <th class="border-top-0">Status</th>
                                <th class="border-top-0">Dibuat Oleh</th>
                                <th class="border-top-0 text-center">Aksi</th>
                              </tr>
                            </thead>
                            <tbody id="dataTableBody">
                              <?php
                              $no = 1;
                              foreach ($result['data'] as $message): ?>
                                <tr>
                                  <td>
                                    <span class="fw-bold"><?php echo $no++; ?></span>
                                  </td>
                                  <td>
                                    <div class="d-flex align-items-center">
                                      <div class="avatar-sm bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                        <i class="fab fa-whatsapp"></i>
                                      </div>
                                      <div>
                                        <div class="fw-semibold"><?php echo htmlspecialchars($message['no_tujuan']); ?></div>
                                        <a href="https://wa.me/<?php echo str_replace('+', '', $message['no_tujuan']); ?>" target="_blank" class="text-success text-decoration-none">
                                          <i class="fab fa-whatsapp"></i> Chat
                                        </a>
                                      </div>
                                    </div>
                                  </td>
                                  <td>
                                    <div class="message-preview">
                                      <?php echo htmlspecialchars(substr($message['pesan'], 0, 100)); ?>
                                      <?php if (strlen($message['pesan']) > 100): ?>
                                        <span class="text-muted">...</span>
                                        <button class="btn btn-link btn-sm p-0 ms-1" onclick="toggleMessage(<?php echo $message['id_wagateway']; ?>)">
                                          Selengkapnya
                                        </button>
                                      <?php endif; ?>
                                    </div>
                                    <div id="fullMessage-<?php echo $message['id_wagateway']; ?>" class="d-none">
                                      <?php echo nl2br(htmlspecialchars($message['pesan'])); ?>
                                      <button class="btn btn-link btn-sm p-0 ms-1" onclick="toggleMessage(<?php echo $message['id_wagateway']; ?>)">
                                        Sembunyikan
                                      </button>
                                    </div>
                                  </td>
                                  <td>
                                    <small class="text-muted"><?php echo formatDateIndo($message['tanggal_kirim']); ?></small>
                                  </td>
                                  <td>
                                    <?php
                                    $statusClass = [
                                        'sent' => 'success',
                                        'pending' => 'warning',
                                        'failed' => 'danger'
                                    ];
                                    $statusIcon = [
                                        'sent' => 'check-circle',
                                        'pending' => 'clock',
                                        'failed' => 'times-circle'
                                    ];
                                    $status = $message['status'] ?? 'pending';
                                    ?>
                                    <span class="badge bg-<?php echo $statusClass[$status]; ?>">
                                      <i class="fas fa-<?php echo $statusIcon[$status]; ?>"></i>
                                      <?php echo ucfirst($status); ?>
                                    </span>
                                    <?php if ($status === 'failed' && !empty($message['error_message'])): ?>
                                      <i class="fas fa-exclamation-triangle text-warning ms-1" title="<?php echo htmlspecialchars($message['error_message']); ?>"></i>
                                    <?php endif; ?>
                                  </td>
                                  <td>
                                    <small class="text-muted"><?php echo htmlspecialchars($message['created_by_name'] ?? 'System'); ?></small>
                                  </td>
                                  <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                      <?php if ($status === 'failed'): ?>
                                        <button class="btn btn-outline-warning" onclick="retryMessage(<?php echo $message['id_wagateway']; ?>)" title="Coba Lagi">
                                          <i class="fas fa-redo"></i>
                                        </button>
                                      <?php endif; ?>
                                      <button class="btn btn-outline-danger" onclick="deleteMessage(<?php echo $message['id_wagateway']; ?>, '<?php echo htmlspecialchars($message['no_tujuan']); ?>')" title="Hapus">
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
                                    <a class="page-link" href="?page=<?php echo $result['page'] - 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo $status_filter; ?>&date=<?php echo $date_filter; ?>">
                                      <i class="fas fa-chevron-left"></i>
                                    </a>
                                  </li>
                                <?php endif; ?>

                                <?php
                                $startPage = max(1, $result['page'] - 2);
                                $endPage = min($result['total_pages'], $result['page'] + 2);

                                if ($startPage > 1) {
                                  echo '<li class="page-item"><a class="page-link" href="?page=1&search=' . urlencode($search) . '&status=' . $status_filter . '&date=' . $date_filter . '">1</a></li>';
                                  if ($startPage > 2) {
                                    echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                  }
                                }

                                for ($i = $startPage; $i <= $endPage; $i++) {
                                  $activeClass = $i == $result['page'] ? 'active' : '';
                                  echo '<li class="page-item ' . $activeClass . '">
                                          <a class="page-link" href="?page=' . $i . '&search=' . urlencode($search) . '&status=' . $status_filter . '&date=' . $date_filter . '">' . $i . '</a>
                                        </li>';
                                }

                                if ($endPage < $result['total_pages']) {
                                  if ($endPage < $result['total_pages'] - 1) {
                                    echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                  }
                                  echo '<li class="page-item">
                                          <a class="page-link" href="?page=' . $result['total_pages'] . '&search=' . urlencode($search) . '&status=' . $status_filter . '&date=' . $date_filter . '">' . $result['total_pages'] . '</a>
                                        </li>';
                                }
                                ?>

                                <?php if ($result['page'] < $result['total_pages']): ?>
                                  <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $result['page'] + 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo $status_filter; ?>&date=<?php echo $date_filter; ?>">
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
                          <i class="fab fa-whatsapp text-muted" style="font-size: 3rem;"></i>
                          <h5 class="mt-3 text-muted">Belum ada pesan</h5>
                          <p class="text-muted">Kirim pesan WhatsApp pertama Anda</p>
                          <a href="index.php?controller=waGateway&action=form" class="btn btn-success">
                            <i class="fas fa-plus-circle"></i> Kirim Pesan
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

  <!-- Bulk Send Modal -->
  <div class="modal fade" id="bulkSendModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            <i class="fas fa-paper-plane"></i> Kirim Pesan Massal
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form id="bulkSendForm">
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label fw-semibold">Pesan <span class="text-danger">*</span></label>
              <textarea class="form-control" id="bulkMessage" name="message" rows="4" required
                        placeholder="Masukkan pesan yang akan dikirim ke semua kontak..."></textarea>
              <small class="text-muted">Pesan akan dikirim ke semua kontak yang dipilih</small>
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Pilih Kontak <span class="text-danger">*</span></label>
              <div class="contact-list" style="max-height: 300px; overflow-y: auto;">
                <div class="form-check mb-3 custom-checkbox">
                  <input class="form-check-input custom-checkbox-input" type="checkbox" id="selectAll" onchange="toggleAllContacts()">
                  <label class="form-check-label custom-checkbox-label fw-semibold" for="selectAll">
                    <i class="fas fa-users me-2"></i>Pilih Semua Kontak
                  </label>
                </div>
                <hr>
                <?php foreach ($contacts as $contact): ?>
                  <div class="form-check mb-3 custom-checkbox contact-item">
                    <input class="form-check-input custom-checkbox-input contact-checkbox" type="checkbox"
                           name="contacts[]" value="<?php echo htmlspecialchars($contact['username'] . '|' . $contact['no_telp']); ?>"
                           id="contact_<?php echo $contact['id_user']; ?>">
                    <label class="form-check-label custom-checkbox-label" for="contact_<?php echo $contact['id_user']; ?>">
                      <div class="d-flex align-items-center">
                        <div class="contact-avatar bg-<?php echo $contact['role'] === 'camat' ? 'success' : 'primary'; ?> text-white rounded-circle d-flex align-items-center justify-content-center me-3">
                          <i class="fas fa-user fa-xs"></i>
                        </div>
                        <div class="flex-grow-1">
                          <div class="fw-semibold text-dark"><?php echo htmlspecialchars($contact['username']); ?></div>
                          <small class="text-muted d-block">
                            <i class="fas fa-phone me-1"></i><?php echo htmlspecialchars($contact['no_telp']); ?>
                            <?php if ($contact['role'] === 'camat'): ?>
                              <span class="badge bg-success ms-2">Camat</span>
                            <?php else: ?>
                              <span class="badge bg-primary ms-2">OPD</span>
                            <?php endif; ?>
                          </small>
                        </div>
                      </div>
                    </label>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
              <i class="fas fa-times"></i> Batal
            </button>
            <button type="submit" class="btn btn-success" id="bulkSendBtn">
              <i class="fas fa-paper-plane"></i> Kirim ke Semua
            </button>
          </div>
        </form>
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
      $time = date('H:i', $timestamp);

      return "$dayName, $day $month $year - $time";
  }
  ?>

  <?php include 'template/script.php'; ?>

  <!-- Custom JavaScript -->
  <script>
    // Toggle message preview
    function toggleMessage(id) {
      const preview = document.querySelector(`#dataTableBody tr:nth-child(${id}) .message-preview`);
      const fullMessage = document.getElementById(`fullMessage-${id}`);

      if (fullMessage.classList.contains('d-none')) {
        preview.classList.add('d-none');
        fullMessage.classList.remove('d-none');
      } else {
        preview.classList.remove('d-none');
        fullMessage.classList.add('d-none');
      }
    }

    // Retry failed message
    function retryMessage(id) {
      if (confirm('Apakah Anda yakin ingin mencoba mengirim ulang pesan ini?')) {
        fetch('index.php?controller=waGateway&action=send', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: 'id=' + id + '&retry=true'
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

    // Delete message
    function deleteMessage(id, phoneNumber) {
      if (confirm(`Apakah Anda yakin ingin menghapus pesan ke nomor "${phoneNumber}"?`)) {
        fetch('index.php?controller=waGateway&action=delete', {
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
      const status = document.getElementById('statusFilter').value;
      const date = document.getElementById('dateFilter').value;
      const url = 'index.php?controller=waGateway&action=export';
      const params = new URLSearchParams();
      if (status) params.append('status', status);
      if (date) params.append('date', date);
      window.open(url + (params.toString() ? '?' + params.toString() : ''), '_blank');
    }

    // Reset filter
    function resetFilter() {
      document.getElementById('searchInput').value = '';
      document.getElementById('statusFilter').value = '';
      document.getElementById('dateFilter').value = '';
      window.location.href = 'index.php?controller=waGateway';
    }

    // Show bulk send modal
    function showBulkModal() {
      const modal = new bootstrap.Modal(document.getElementById('bulkSendModal'));
      modal.show();
    }

    // Toggle all contacts
    function toggleAllContacts() {
      const selectAll = document.getElementById('selectAll');
      const checkboxes = document.querySelectorAll('.contact-checkbox');
      checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
      });
    }

    // Update select all checkbox when individual checkboxes change
    document.addEventListener('change', function(e) {
      if (e.target.classList.contains('contact-checkbox')) {
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.contact-checkbox');
        const checkedBoxes = document.querySelectorAll('.contact-checkbox:checked');
        selectAll.checked = checkboxes.length === checkedBoxes.length;
      }
    });

    // Bulk send form submission
    document.getElementById('bulkSendForm').addEventListener('submit', function(e) {
      e.preventDefault();

      const formData = new FormData(this);
      const submitBtn = document.getElementById('bulkSendBtn');

      // Check if any contacts are selected
      const selectedContacts = formData.getAll('contacts[]');
      if (selectedContacts.length === 0) {
        showAlert('warning', 'Pilih minimal satu kontak untuk dikirimkan pesan.');
        return;
      }

      // Show loading state
      submitBtn.disabled = true;
      submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Mengirim...';

      // Convert FormData to URLSearchParams
      const params = new URLSearchParams();
      for (const [key, value] of formData.entries()) {
        params.append(key, value);
      }

      fetch('index.php?controller=waGateway&action=bulkSend', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: params.toString()
      })
      .then(response => response.json())
      .then(result => {
        if (result.success) {
          showAlert('success', result.message);

          // Show detailed results
          if (result.results && result.results.length > 0) {
            const modal = bootstrap.Modal.getInstance(document.getElementById('bulkSendModal'));
            modal.hide();

            setTimeout(() => {
              showBulkResults(result.results, result.success_count, result.failed_count);
            }, 500);
          } else {
            setTimeout(() => location.reload(), 1500);
          }
        } else {
          showAlert('danger', result.message);
        }
      })
      .catch(error => {
        showAlert('danger', 'Error: ' + error.message);
      })
      .finally(() => {
        // Reset button state
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Kirim ke Semua';
      });
    });

    // Show bulk send results
    function showBulkResults(results, successCount, failedCount) {
      let resultHtml = `
        <div class="modal fade" id="resultsModal" tabindex="-1">
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">
                  <i class="fas fa-check-circle"></i> Hasil Pengiriman
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                <div class="alert alert-info">
                  <i class="fas fa-info-circle"></i>
                  <strong>Ringkasan:</strong> Sukses: ${successCount}, Gagal: ${failedCount}
                </div>
                <div class="table-responsive">
                  <table class="table table-sm">
                    <thead>
                      <tr>
                        <th>Nama</th>
                        <th>Nomor</th>
                        <th>Status</th>
                       
                      </tr>
                    </thead>
                    <tbody>
      `;

      results.forEach(result => {
        const statusClass = result.status === 'success' ? 'success' : 'danger';
        const statusIcon = result.status === 'success' ? 'check-circle' : 'times-circle';
        resultHtml += `
          <tr>
            <td>${result.name}</td>
            <td>${result.phone}</td>
            <td>
              <span class="badge bg-${statusClass}">
                <i class="fas fa-${statusIcon}"></i>
                ${result.status === 'success' ? 'Berhasil' : 'Gagal'}
              </span>
            </td>
          </tr>
        `;
      });

      resultHtml += `
                    </tbody>
                  </table>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="location.reload()">
                  <i class="fas fa-sync"></i> Refresh Halaman
                </button>
              </div>
            </div>
          </div>
        </div>
      `;

      // Add modal to body
      const modalContainer = document.createElement('div');
      modalContainer.innerHTML = resultHtml;
      document.body.appendChild(modalContainer);

      // Show modal
      const modal = new bootstrap.Modal(document.getElementById('resultsModal'));
      modal.show();

      // Remove modal from DOM when hidden
      document.getElementById('resultsModal').addEventListener('hidden.bs.modal', function() {
        modalContainer.remove();
      });
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
      const status = document.getElementById('statusFilter').value;
      const date = document.getElementById('dateFilter').value;

      const params = new URLSearchParams();
      if (search) params.append('search', search);
      if (status) params.append('status', status);
      if (date) params.append('date', date);

      window.location.href = 'index.php?controller=waGateway&' + params.toString();
    });

    // Auto-refresh data (optional - every 30 seconds)
    setInterval(() => {
      // Only refresh if no modal is open
      if (!document.querySelector('.modal.show')) {
        // Optional: Implement auto-refresh logic here
      }
    }, 30000);
  </script>

  <style>
        .message-preview {
      max-width: 300px;
      word-wrap: break-word;
    }
    .contact-list {
      border: 1px solid #dee2e6;
      border-radius: 0.375rem;
      padding: 1rem;
      background: #f8f9fa;
    }
    .spinner-border-sm {
      width: 1rem;
      height: 1rem;
    }

    /* Custom Checkbox Styles */
    .custom-checkbox {
      position: relative;
      padding-left: 0;
    }

    .custom-checkbox-input {
      width: 20px;
      height: 20px;
      border: 2px solid #28a745;
      border-radius: 4px;
      background-color: white;
      cursor: pointer;
      position: relative;
      margin-top: 2px;
      margin-right: 12px;
      transition: all 0.3s ease;
    }

    .custom-checkbox-input:checked {
      background-color: #28a745;
      border-color: #28a745;
    }

    .custom-checkbox-input:checked::after {
      content: '✓';
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      color: white;
      font-size: 14px;
      font-weight: bold;
    }

    .custom-checkbox-input:hover {
      border-color: #218838;
      box-shadow: 0 0 0 2px rgba(40, 167, 69, 0.2);
    }

    .custom-checkbox-label {
      cursor: pointer;
      padding-left: 0;
      margin-left: 8px;
      display: flex;
      align-items: center;
    }

    .contact-item {
      padding: 12px;
      background: white;
      border: 1px solid #e9ecef;
      border-radius: 8px;
      transition: all 0.3s ease;
    }

    .contact-item:hover {
      background: #f8f9fa;
      border-color: #28a745;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .contact-avatar {
      width: 36px;
      height: 36px;
      flex-shrink: 0;
    }

    .contact-checkbox:checked + .custom-checkbox-label .contact-item {
      background: #d4edda;
      border-color: #28a745;
    }

    .contact-checkbox:checked + .custom-checkbox-label .contact-avatar {
      background: #28a745 !important;
    }
  </style>
</body>
</html>