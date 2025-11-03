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
            <div class="col-12">
              <div class="page-header">
                <div class="page-title">
                  <h3 class="mb-1">
                    <i class="fab fa-whatsapp text-success me-2"></i>
                    <?php echo $message ? 'Edit Pesan' : 'Kirim Pesan WhatsApp'; ?>
                  </h3>
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <!-- Form Section -->
            <div class="col-lg-8">
              <div class="card shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                  <div class="d-flex align-items-center justify-content-between">
                    <div>
                      <h5 class="card-title mb-1">
                        <i class="fas fa-edit text-success me-2"></i>
                        Formulir Pesan
                      </h5>
                      <small class="text-muted">Lengkapi form berikut untuk mengirim pesan WhatsApp</small>
                    </div>
                  </div>
                </div>
                <div class="card-body">
                  <form id="messageForm" onsubmit="sendMessage(event)">
                    <input type="hidden" name="id" id="messageId" value="<?php echo $message['id_wagateway'] ?? ''; ?>">

                    <!-- Recipient Section -->
                    <div class="form-section mb-4">
                      <h6 class="section-title mb-3">
                        <i class="fas fa-user-plus text-primary me-2"></i>
                        Penerima Pesan
                      </h6>

                      <div class="row g-3">
                        <div class="col-md-8">
                          <label class="form-label fw-semibold">
                            Nomor WhatsApp <span class="text-danger">*</span>
                          </label>
                          <div class="input-group">
                            <span class="input-group-text bg-success text-white">
                              <i class="fab fa-whatsapp"></i>
                            </span>
                            <input type="tel" class="form-control" id="no_tujuan" name="no_tujuan"
                                   value="<?php echo htmlspecialchars($message['no_tujuan'] ?? ''); ?>"
                                   placeholder="08123456789"
                                   pattern="(^\\+62|62|^08)[0-9]{9,13}"
                                   title="Format: 08xxxxxxxxxx atau +62xxxxxxxxxx"
                                   <?php echo $message ? 'readonly' : ''; ?> required>
                            <?php if (!$message): ?>
                            <button class="btn btn-outline-success" type="button" onclick="showContactModal()">
                              <i class="fas fa-address-book"></i>
                            </button>
                            <?php endif; ?>
                          </div>
                          <div class="form-text">Format nomor Indonesia: 08xxxxxxxxxx atau +62xxxxxxxxxx</div>
                        </div>

                        <div class="col-md-4">
                          <label class="form-label fw-semibold">Opsi</label>
                          <div class="d-flex flex-column gap-2">
                            <div class="form-text">
                              <i class="fas fa-info-circle me-1"></i>
                              Masukkan nomor tujuan yang valid
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                    
                    <!-- Message Section -->
                    <div class="form-section mb-4">
                      <h6 class="section-title mb-3">
                        <i class="fas fa-comment-dots text-primary me-2"></i>
                        Isi Pesan <span class="text-danger">*</span>
                      </h6>

                      <div class="message-editor">
                        <div class="editor-toolbar d-flex justify-content-between align-items-center mb-2">
                          <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-secondary" onclick="addEmoji()" title="Tambah Emoji">
                              <i class="fas fa-smile"></i>
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="addVariable()" title="Tambah Variabel">
                              <i class="fas fa-code"></i>
                            </button>
                          </div>
                          <div class="text-muted small">
                            <span id="charCount">0</span> / 1600 karakter |
                            <span id="smsCount">1</span> SMS
                          </div>
                        </div>

                        <textarea class="form-control" id="pesan" name="pesan" rows="10" required
                                  placeholder="Ketik pesan Anda di sini...&#10;&#10;Tips:&#10;- Gunakan {nama} untuk personalisasi&#10;- Hindari pengulangan yang tidak perlu&#10;- Perhatikan batas karakter"
                                  oninput="updateCharacterCount(); updatePreview()"><?php echo htmlspecialchars($message['pesan'] ?? ''); ?></textarea>

                        <div class="editor-footer text-muted small mt-1">
                          <i class="fas fa-info-circle me-1"></i>
                          Gunakan {nama} untuk menyisipkan nama penerima secara otomatis
                        </div>
                      </div>
                    </div>

                    
                    <!-- Action Buttons -->
                    <div class="form-section">
                      <div class="d-flex justify-content-between align-items-center">
                        <a href="index.php?controller=waGateway" class="btn btn-secondary">
                          <i class="fas fa-arrow-left me-2"></i> Kembali
                        </a>
                        <div class="d-flex gap-2">
                          <button type="reset" class="btn btn-outline-secondary">
                            <i class="fas fa-undo me-2"></i> Reset
                          </button>
                          <button type="submit" class="btn btn-success" id="submitBtn">
                            <i class="fas fa-paper-plane me-2"></i>
                            <span id="submitBtnText"><?php echo $message ? 'Update & Kirim' : 'Kirim Pesan'; ?></span>
                          </button>
                        </div>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>

            <!-- Preview Section -->
            <div class="col-lg-4">
              <div class="card shadow-sm sticky-top" style="top: 20px;">
                <div class="card-header bg-white border-0 py-3">
                  <h5 class="card-title mb-0">
                    <i class="fas fa-eye text-info me-2"></i>
                    Preview Pesan
                  </h5>
                </div>
                <div class="card-body">
                  <div class="whatsapp-preview">
                    <div class="chat-bubble-container">
                      <div class="chat-bubble">
                        <div id="previewContent" class="chat-content">
                          <?php echo nl2br(htmlspecialchars($message['pesan'] ?? 'Ketik pesan untuk melihat preview...')); ?>
                        </div>
                      </div>
                      <div class="chat-time">
                        <i class="fas fa-clock me-1"></i>
                        <span id="previewTime"><?php echo $message ? formatDateIndo($message['tanggal_kirim']) : 'Sekarang'; ?></span>
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

  <!-- Contact Selection Modal -->
  <div class="modal fade" id="contactModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            <i class="fas fa-address-book"></i> Pilih Kontak
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <input type="text" class="form-control" id="contactSearch" placeholder="Cari nama atau nomor telepon..." onkeyup="searchContacts()">
          </div>
          <div class="contact-list" style="max-height: 400px; overflow-y: auto;">
            <?php foreach ($contacts as $contact): ?>
              <div class="contact-item p-2 border rounded mb-2" onclick="selectContact('<?php echo htmlspecialchars($contact['no_telp']); ?>', '<?php echo htmlspecialchars($contact['username']); ?>')" style="cursor: pointer;">
                <div class="d-flex align-items-center">
                  <div class="avatar-sm bg-<?php echo $contact['role'] === 'camat' ? 'primary' : 'info'; ?> text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                    <?php echo strtoupper(substr($contact['username'], 0, 1)); ?>
                  </div>
                  <div class="flex-grow-1">
                    <div class="fw-semibold"><?php echo htmlspecialchars($contact['username']); ?></div>
                    <div class="text-muted small"><?php echo htmlspecialchars($contact['no_telp']); ?></div>
                    <div>
                      <small class="badge bg-<?php echo $contact['role'] === 'camat' ? 'primary' : 'info'; ?>">
                        <?php echo ucfirst($contact['role']); ?>
                      </small>
                    </div>
                  </div>
                  <div>
                    <button class="btn btn-sm btn-success" onclick="event.stopPropagation(); selectContact('<?php echo htmlspecialchars($contact['no_telp']); ?>', '<?php echo htmlspecialchars($contact['username']); ?>')">
                      <i class="fas fa-check"></i> Pilih
                    </button>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fas fa-times"></i> Tutup
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Emoji Picker Modal -->
  <div class="modal fade" id="emojiModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            <i class="fas fa-smile"></i> Pilih Emoji
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="emoji-grid" style="display: grid; grid-template-columns: repeat(8, 1fr); gap: 5px;">
            <?php
            $emojis = ['😀', '😁', '😂', '🤣', '😃', '😄', '😅', '😆', '😉', '😊', '😋', '😎', '😍', '😘', '🥰', '😗', '🙄', '😏', '😒', '🙄', '😬', '😮', '😯', '😲', '😴', '😪', '😵', '🤯', '🤠', '🥳', '😎', '🤓', '🧐', '😕', '😟', '🙁', '😮', '😯', '😲', '😳', '🥺', '😦', '😧', '😨', '😰', '😥', '😢', '😭', '😱', '😖', '😣', '😞', '😓', '😩', '😫', '🥱', '😤', '😡', '😠', '🤬', '😈', '👿', '💀', '☠️', '💩', '🤡', '👹', '👺', '👻', '👽', '👾', '🤖', '❤️', '🧡', '💛', '💚', '💙', '💜', '🖤', '🤍', '🤎', '💔', '❣️', '💕', '💞', '💓', '💗', '💖', '💘', '💝', '👍', '👎', '👌', '✌️', '🤞', '🤟', '🤘', '🤙', '👈', '👉', '👆', '👇', '☝️', '✋', '🤚', '🖐️', '🖖', '👋', '🤙', '💪', '🙏', '✍️', '🎉', '🎊', '🎈', '🎁', '🎀', '🎗️', '🎟️', '🎫', '🎖️', '🏆', '🏅', '🥇', '🥈', '🥉'];
            foreach ($emojis as $emoji) {
              echo '<button type="button" class="btn btn-outline-light btn-sm p-2" onclick="insertEmoji(\'' . $emoji . '\')" style="font-size: 20px;">' . $emoji . '</button>';
            }
            ?>
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
      $time = date('H:i', $timestamp);

      return "$dayName, $day $month $year - $time";
  }
  ?>

  <?php include 'template/script.php'; ?>

  <!-- Custom JavaScript -->
  <script>
    
    
    // Update character and SMS count
    function updateCharacterCount() {
      const pesan = document.getElementById('pesan').value;
      const charCount = document.getElementById('charCount');
      const smsCount = document.getElementById('smsCount');
      const charCountDisplay = charCount.parentElement;

      const length = pesan.length;
      const smsLength = 160; // Standard SMS length
      const maxLength = 1600; // Maximum 10 SMS

      charCount.textContent = length;
      smsCount.textContent = Math.ceil(length / smsLength);

      // Color coding based on character count
      charCountDisplay.classList.remove('char-count-safe', 'char-count-warning', 'char-count-danger');

      if (length <= smsLength) {
        charCountDisplay.classList.add('char-count-safe');
      } else if (length <= maxLength * 0.8) {
        charCountDisplay.classList.add('char-count-warning');
      } else {
        charCountDisplay.classList.add('char-count-danger');
      }

      updatePreview();
    }

    // Update preview
    function updatePreview() {
      const pesan = document.getElementById('pesan');
      const previewContent = document.getElementById('previewContent');
      const previewTime = document.getElementById('previewTime');

      if (!pesan || !previewContent) return;

      const pesanValue = pesan.value;

      if (pesanValue.trim()) {
        previewContent.innerHTML = nl2br(htmlspecialchars(pesanValue));
      } else {
        previewContent.innerHTML = 'Ketik pesan untuk melihat preview...';
      }

      // Always show "Sekarang" since we send immediately
      if (previewTime) {
        previewTime.textContent = 'Sekarang';
      }

      // Animate the preview bubble
      const chatBubble = previewContent.closest('.chat-bubble');
      if (chatBubble) {
        chatBubble.style.transform = 'scale(0.98)';
        setTimeout(() => {
          chatBubble.style.transform = 'scale(1)';
        }, 100);
      }
    }

    // Show contact modal
    function showContactModal() {
      const modal = new bootstrap.Modal(document.getElementById('contactModal'));
      modal.show();
    }

    // Select contact
    function selectContact(phone, name) {
      document.getElementById('no_tujuan').value = phone;

      // Close modal
      const modal = bootstrap.Modal.getInstance(document.getElementById('contactModal'));
      modal.hide();

      // Show notification
      showNotification('success', `Kontak "${name}" dipilih`);
    }

    // Search contacts
    function searchContacts() {
      const searchTerm = document.getElementById('contactSearch').value.toLowerCase();
      const contactItems = document.querySelectorAll('.contact-item');

      contactItems.forEach(item => {
        const text = item.textContent.toLowerCase();
        if (text.includes(searchTerm)) {
          item.style.display = 'block';
        } else {
          item.style.display = 'none';
        }
      });
    }

    // Add emoji
    function addEmoji() {
      const modal = new bootstrap.Modal(document.getElementById('emojiModal'));
      modal.show();
    }

    // Insert emoji
    function insertEmoji(emoji) {
      const pesanField = document.getElementById('pesan');
      const start = pesanField.selectionStart;
      const end = pesanField.selectionEnd;

      pesanField.value = pesanField.value.substring(0, start) + emoji + pesanField.value.substring(end);
      pesanField.selectionStart = pesanField.selectionEnd = start + emoji.length;

      updateCharacterCount();

      // Close modal
      const modal = bootstrap.Modal.getInstance(document.getElementById('emojiModal'));
      modal.hide();
    }

    // Add variable
    function addVariable() {
      const pesanField = document.getElementById('pesan');
      const start = pesanField.selectionStart;

      pesanField.value = pesanField.value.substring(0, start) + '{nama}' + pesanField.value.substring(start);
      pesanField.selectionStart = pesanField.selectionEnd = start + 6; // Length of {nama}

      updateCharacterCount();
    }

    // Send message
    function sendMessage(event) {
      event.preventDefault();

      const form = document.getElementById('messageForm');
      const formData = new FormData(form);
      const submitBtn = document.getElementById('submitBtn');
      const submitBtnText = document.getElementById('submitBtnText');

      // Show loading state
      submitBtn.disabled = true;
      submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Mengirim...';

      // Convert FormData to URLSearchParams
      const params = new URLSearchParams();
      for (const [key, value] of formData.entries()) {
        params.append(key, value);
      }

      fetch('index.php?controller=waGateway&action=send', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: params.toString()
      })
      .then(response => response.json())
      .then(result => {
        if (result.success) {
          showNotification('success', result.message);

          // Redirect after delay
          setTimeout(() => {
            window.location.href = 'index.php?controller=waGateway';
          }, 1500);
        } else {
          showNotification('danger', result.message);
        }
      })
      .catch(error => {
        showNotification('danger', 'Error: ' + error.message);
      })
      .finally(() => {
        // Reset button state
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> <span id="submitBtnText">' + submitBtnText.textContent + '</span>';
      });
    }

    // Show notification
    function showNotification(type, message) {
      // Remove existing alerts
      const existingAlerts = document.querySelectorAll('.alert');
      existingAlerts.forEach(alert => alert.remove());

      // Create new alert
      const alertDiv = document.createElement('div');
      alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
      alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
      alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      `;

      document.body.appendChild(alertDiv);

      // Auto remove after 5 seconds
      setTimeout(() => {
        if (alertDiv.parentNode) {
          alertDiv.remove();
        }
      }, 5000);
    }

    // nl2br function for JavaScript
    function nl2br(str) {
      return str.replace(/\\n/g, '<br>');
    }

    // htmlspecialchars function for JavaScript
    function htmlspecialchars(str) {
      if (typeof str !== 'string') return str;
      return str
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
    }

    // Phone number validation
    document.getElementById('no_tujuan').addEventListener('input', function(e) {
      let value = e.target.value;

      // Allow only numbers, +, and spaces
      value = value.replace(/[^0-9+\s]/g, '');

      // Format: If starts with 0, keep as is. If starts with 62, add + at the beginning
      if (value.startsWith('62') && !value.startsWith('+62')) {
        value = '+' + value;
      }

      e.target.value = value;

      // Validate format
      const phoneRegex = /^(^\+62|62|^08)[0-9]{9,13}$/;
      if (value.length > 0 && !phoneRegex.test(value.replace(/\s/g, ''))) {
        e.target.classList.add('is-invalid');
      } else {
        e.target.classList.remove('is-invalid');
      }
    });

    // Initialize form on load
    document.addEventListener('DOMContentLoaded', function() {
      updateCharacterCount();
      updatePreview(); // Initialize preview on page load

      
      // Message textarea change handler for real-time preview
      const pesanTextarea = document.getElementById('pesan');
      if (pesanTextarea) {
        pesanTextarea.addEventListener('input', function(e) {
          updatePreview();
        });
      }

      // Focus on first empty field
      const firstEmpty = document.querySelector('input:not([readonly]):not([disabled]):not([value])');
      if (firstEmpty) {
        firstEmpty.focus();
      }
    });
  </script>

  <style>
    /* General Form Styling */
    .form-section {
      padding: 1.5rem;
      background: #f8f9fa;
      border-radius: 0.5rem;
      border: 1px solid #e9ecef;
      margin-bottom: 1.5rem;
      transition: all 0.3s ease;
    }

    .form-section:hover {
      box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
      border-color: #dee2e6;
    }

    .section-title {
      color: #495057;
      font-weight: 600;
      font-size: 0.875rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin-bottom: 1rem;
      padding-bottom: 0.5rem;
      border-bottom: 2px solid #e9ecef;
    }

    /* Message Editor */
    .message-editor {
      border: 1px solid #dee2e6;
      border-radius: 0.5rem;
      overflow: hidden;
      transition: all 0.3s ease;
    }

    .message-editor:focus-within {
      border-color: #28a745;
      box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
    }

    .editor-toolbar {
      background: #fff;
      padding: 0.75rem 1rem;
      border-bottom: 1px solid #dee2e6;
    }

    .editor-footer {
      background: #f8f9fa;
      padding: 0.5rem 1rem;
      border-top: 1px solid #dee2e6;
    }

    .message-editor textarea {
      border: none;
      border-radius: 0;
      resize: vertical;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      font-size: 0.95rem;
      line-height: 1.6;
    }

    .message-editor textarea:focus {
      box-shadow: none;
      border-color: transparent;
    }

    /* WhatsApp Preview */
    .whatsapp-preview {
      background: #e5ddd5;
      background-image: url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KICA8ZGVmcz4KICAgIDxwYXR0ZXJuIGlkPSJ3YSIgcGF0dGVyblVuaXRzPSJ1c2VyU3BhY2VPblVzZSIgd2lkdGg9IjEwMCIgaGVpZ2h0PSIxMDAiPgogICAgICA8cmVjdCB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgZmlsbD0iI2U1ZGRkNSIvPgogICAgICA8Y2lyY2xlIGN4PSI1MCIgY3k9IjUwIiByPSI0MCIgZmlsbD0iI2Q4ZjhkYyIgb3BhY2l0eT0iMC4xIi8+CiAgICA8L3BhdHRlcm4+CiAgPC9kZWZzPgogIDxyZWN0IHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIGZpbGw9InVybCgjd2EpIi8+Cjwvc3ZnPg==');
      background-size: 100px 100px;
      padding: 1rem;
      border-radius: 0.5rem;
      border: 1px solid #ddd;
    }

    .chat-bubble-container {
      display: flex;
      justify-content: flex-end;
      margin-bottom: 1rem;
    }

    .chat-bubble {
      background: #dcf8c6;
      color: #303030;
      padding: 0.75rem 1rem;
      border-radius: 0.75rem;
      max-width: 100%;
      position: relative;
      box-shadow: 0 1px 0.5px rgba(0, 0, 0, 0.13);
      transition: transform 0.2s ease-in-out;
    }

    .chat-bubble::after {
      content: '';
      position: absolute;
      top: 0;
      right: -6px;
      width: 12px;
      height: 12px;
      background: #dcf8c6;
      border-top-left-radius: 50%;
      box-shadow: -4px -4px 8px rgba(0, 0, 0, 0.13);
    }

    .chat-content {
      word-wrap: break-word;
      line-height: 1.4;
      font-size: 0.9rem;
    }

    .chat-time {
      text-align: right;
      color: #667781;
      font-size: 0.75rem;
      margin-top: 0.25rem;
    }

    
    /* Custom Toggle Switch */
    .form-check-lg .form-check-input {
      width: 3rem;
      height: 1.5rem;
    }

    .form-check-lg .form-check-label {
      font-size: 0.95rem;
      font-weight: 500;
      cursor: pointer;
      user-select: none;
      transition: color 0.2s ease;
    }

    .form-check-input:checked {
      background-color: #28a745;
      border-color: #28a745;
    }

    .form-check-input:focus {
      box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.25);
    }

    .form-switch .form-check-input {
      background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3circle r='3' fill='%23adb5bd'/%3e%3c/svg%3e");
      background-position: left center;
      border-radius: 2rem;
    }

    .form-switch .form-check-input:checked {
      background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3cpath d='M5 10.5a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-1Z' fill='%23adb5bd'/%3e%3c/svg%3e");
      background-position: right center;
    }

    /* Form Validation */
    .form-control.is-invalid {
      border-color: #dc3545;
      background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
      background-repeat: no-repeat;
      background-position: right calc(0.375em + 0.1875rem) center;
      background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    }

    .form-control.is-invalid:focus {
      border-color: #dc3545;
      box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }

    /* Contact Modal */
    .contact-item {
      padding: 1rem;
      border: 1px solid #e9ecef;
      border-radius: 0.5rem;
      margin-bottom: 0.5rem;
      transition: all 0.3s ease;
      cursor: pointer;
    }

    .contact-item:hover {
      background-color: #f8f9fa;
      border-color: #28a745;
      transform: translateY(-2px);
      box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.075);
    }

    /* Animations */
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

    .card {
      transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }

    .card:hover {
      transform: translateY(-2px);
      box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    /* Emoji Grid */
    .emoji-grid button {
      border: none;
      background: transparent;
      padding: 0.5rem;
      border-radius: 0.25rem;
      transition: all 0.2s ease;
      font-size: 1.25rem;
    }

    .emoji-grid button:hover {
      background-color: #e9ecef;
      transform: scale(1.3);
    }

    /* Sticky */
    .sticky-top {
      position: sticky;
      top: 1rem;
      z-index: 1020;
    }

    /* Loading States */
    .spinner-border-sm {
      width: 1rem;
      height: 1rem;
      border-width: 0.2em;
    }

    /* Custom Scrollbar */
    .contact-list::-webkit-scrollbar {
      width: 6px;
    }

    .contact-list::-webkit-scrollbar-track {
      background: #f1f1f1;
      border-radius: 3px;
    }

    .contact-list::-webkit-scrollbar-thumb {
      background: #c1c1c1;
      border-radius: 3px;
    }

    .contact-list::-webkit-scrollbar-thumb:hover {
      background: #a8a8a8;
    }

    /* Responsive Design */
    @media (max-width: 991.98px) {
      .form-section {
        padding: 1rem;
      }

      .whatsapp-preview {
        margin-top: 2rem;
      }

      .sticky-top {
        position: relative;
        top: 0;
      }
    }

    @media (max-width: 767.98px) {
      .page-title h3 {
        font-size: 1.5rem;
      }

      .section-title {
        font-size: 0.8rem;
      }

      .message-editor textarea {
        font-size: 0.9rem;
      }

      .btn-group .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
      }
    }

    /* Character Count Color Coding */
    .char-count-safe {
      color: #28a745;
    }

    .char-count-warning {
      color: #ffc107;
    }

    .char-count-danger {
      color: #dc3545;
    }
  </style>
</body>
</html>