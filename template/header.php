<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta
    name="viewport"
    content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <title>PPID</title>
  <link rel="stylesheet" href="assets/vendors/feather/feather.css" />
  <link
    rel="stylesheet"
    href="assets/vendors/mdi/css/materialdesignicons.min.css" />
  <link
    rel="stylesheet"
    href="assets/vendors/ti-icons/css/themify-icons.css" />
  <link rel="stylesheet" href="assets/vendors/typicons/typicons.css" />
  <link
    rel="stylesheet"
    href="assets/vendors/simple-line-icons/css/simple-line-icons.css" />
  <link rel="stylesheet" href="assets/vendors/css/vendor.bundle.base.css" />
  <link
    rel="stylesheet"
    href="assets/vendors/datatables.net-bs4/dataTables.bootstrap4.css" />
  <link
    rel="stylesheet"
    type="text/css"
    href="assets/js/select.dataTables.min.css" />
  <link rel="stylesheet" href="assets/css/vertical-layout-light/style.css" />
  <link rel="shortcut icon" href="assets/images/favicon.png" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Tambahkan kelas 'collapse-init' ke semua elemen collapse saat halaman dimuat
      const collapseElements = document.querySelectorAll('.sidebar .nav .collapse');
      collapseElements.forEach(function(element) {
        element.classList.add('collapse-init');
      });
      
      // Hapus state 'show' dari semua elemen collapse saat halaman dimuat
      setTimeout(function() {
        const showElements = document.querySelectorAll('.sidebar .nav .show');
        showElements.forEach(function(element) {
          element.classList.remove('show');
        });
      }, 100);
    });
  </script>
  
  <style>
    /* CSS untuk memastikan dropdown tertutup saat halaman pertama kali dimuat */
    .sidebar .nav .collapse.collapse-init:not(.show) {
      display: none;
    }
    
    /* Custom CSS untuk textarea yang lebih besar dan ramah pengguna */
    textarea.form-control {
      min-height: 120px;
      padding: 12px 15px;
      font-size: 14px;
      line-height: 1.5;
      border: 1.5px solid #e2e8f0;
      border-radius: 6px;
      transition: all 0.2s ease;
      background-color: #ffffff;
      color: #1e293b;
    }
    
    textarea.form-control:focus {
      border-color: #2563eb;
      box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
      background-color: #ffffff;
      outline: none;
    }
    
    /* Textarea dengan ukuran khusus berdasarkan jumlah baris */
    textarea.form-control[data-rows="3"] {
      min-height: 90px;
    }
    
    textarea.form-control[data-rows="4"] {
      min-height: 120px;
    }
    
    textarea.form-control[data-rows="5"] {
      min-height: 150px;
    }
    
    textarea.form-control[data-rows="6"] {
      min-height: 180px;
    }
    
    textarea.form-control[data-rows="7"] {
      min-height: 210px;
    }
    
    textarea.form-control[data-rows="8"] {
      min-height: 240px;
    }
    
    /* Placeholder styling */
    textarea.form-control::placeholder {
      color: #94a3b8;
      font-style: italic;
    }
    
    /* Resizable textarea */
    textarea.form-control.resizable {
      resize: vertical;
    }
    
    /* Fixed height textarea (tidak bisa di-resize) */
    textarea.form-control.fixed-height {
      resize: none;
    }
    
    /* Large textarea styling */
    textarea.form-control.textarea-large {
      min-height: 200px;
      font-size: 15px;
    }
    
    /* Medium textarea styling */
    textarea.form-control.textarea-medium {
      min-height: 150px;
    }
    
    /* Small textarea styling */
    textarea.form-control.textarea-small {
      min-height: 100px;
      font-size: 13px;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
      textarea.form-control {
        min-height: 100px;
        font-size: 16px; /* Mencegah zoom pada mobile devices */
      }
      
      textarea.form-control.textarea-large {
        min-height: 160px;
      }
      
      textarea.form-control.textarea-medium {
        min-height: 120px;
      }
      
      textarea.form-control.textarea-small {
        min-height: 80px;
      }
    }
    
    /* Focus effect with glow */
    textarea.form-control.focus-glow:focus {
      box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.2), 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    
    /* Read-only textarea styling */
    textarea.form-control[readonly] {
      background-color: #f8fafc;
      cursor: not-allowed;
      opacity: 0.9;
    }
    
    /* Disabled textarea styling */
    textarea.form-control:disabled {
      background-color: #f1f5f9;
      cursor: not-allowed;
      opacity: 0.7;
    }
    
    /* Error state styling */
    textarea.form-control.is-invalid {
      border-color: #ef4444;
      background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
      background-repeat: no-repeat;
      background-position: right calc(0.375em + 0.1875rem) center;
      background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    }
    
    textarea.form-control.is-invalid:focus {
      border-color: #ef4444;
      box-shadow: 0 0 0 0.25rem rgba(239, 68, 68, 0.25);
    }
    
    /* Success state styling */
    textarea.form-control.is-valid {
      border-color: #10b981;
      background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%23198754' d='M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e");
      background-repeat: no-repeat;
      background-position: right calc(0.375em + 0.1875rem) center;
      background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    }
    
    textarea.form-control.is-valid:focus {
      border-color: #10b981;
      box-shadow: 0 0 0 0.25rem rgba(16, 185, 129, 0.25);
    }
  </style>
 
</head>