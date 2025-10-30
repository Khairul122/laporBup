# 🏛️ PERANCANGAN SISTEM INFORMASI **LaporBup (Lapor Bupati)**

## 📘 Deskripsi Umum
**LaporBup (Lapor Bupati)** atau **SILAP GAWAT (Sistem Informasi Laporan Gabungan Wilayah Kecamatan)** adalah sistem informasi pelaporan berbasis web yang berfungsi sebagai pusat layanan aduan dan pelaporan wilayah.
Sistem ini mengadopsi konsep **helpdesk**, di mana **Camat** dan **OPD** berperan sebagai pelapor, sedangkan **Admin (Dinas Kominfo)** bertugas sebagai penerima, pengelola, dan pengarah tindak lanjut laporan.

**Status Implementasi:** ✅ **Sudah Dikembangkan (versi 2.0)**

---

## 🎯 Tujuan Pengembangan
1. Menyediakan sarana pelaporan digital bagi Camat dan OPD.
2. Mempercepat penyampaian laporan dari instansi pelapor ke Dinas Kominfo.
3. Menjadikan proses pelaporan lebih terstruktur, terdokumentasi, dan mudah ditindaklanjuti.
4. Meningkatkan transparansi dan akuntabilitas proses pelaporan pemerintah daerah.

---

## 👥 Aktor dan Peran Sistem

| Aktor | Deskripsi | Hak Akses |
|-------|------------|-----------|
| **Admin (Dinas Kominfo)** | Petugas utama penerima dan pengelola laporan | - Menerima laporan dari Camat dan OPD<br>- Melihat detail laporan beserta statusnya<br>- Mengubah status laporan (*baru*, *diproses*, *selesai*)<br>- Memberikan tanggapan atau catatan pada laporan<br>- Mencetak laporan (PDF/Excel/CSV)<br>- Mengelola akun pengguna<br>- Mengelola data kecamatan dan desa |
| **Camat** | Pelapor tingkat kecamatan | - Membuat laporan sesuai kejadian di wilayahnya<br>- Mengunggah bukti (foto/lampiran)<br>- Melihat riwayat laporan yang pernah dikirim<br>- Memantau status laporan (baru, diproses, selesai) |
| **OPD (Organisasi Perangkat Daerah)** | Pelapor kegiatan instansi atau temuan lapangan | - Membuat laporan terkait kegiatan atau temuan di lapangan<br>- Mengunggah bukti pendukung<br>- Melihat daftar laporan yang dikirim<br>- Memantau tindak lanjut laporan oleh admin |

---

## ⚙️ Alur Kerja Sistem (Helpdesk Flow)

1. **Camat atau OPD** membuat laporan melalui aplikasi (mengisi formulir laporan dan melampirkan bukti).
2. Laporan dikirim ke **Admin (Dinas Kominfo)**.
3. **Admin** memverifikasi laporan dan memperbarui status menjadi *diproses* jika sudah ditindaklanjuti.
4. Setelah masalah selesai atau laporan diselesaikan, status diubah menjadi *selesai*.
5. **Camat/OPD** dapat memantau status dan membaca tanggapan dari admin melalui dashboard masing-masing.

---

## 🧠 Rancangan Basis Data

### Tabel: `users`
| Kolom | Tipe Data | Keterangan |
|-------|------------|------------|
| id_user | INT AUTO_INCREMENT PRIMARY KEY | ID unik pengguna |
| email | VARCHAR(100) NOT NULL UNIQUE | Email pengguna |
| username | VARCHAR(50) NOT NULL UNIQUE | Nama pengguna |
| password | VARCHAR(255) NOT NULL | Password terenkripsi (bcrypt) |
| jabatan | VARCHAR(100) | Jabatan pengguna |
| role | ENUM('admin', 'opd', 'camat') NOT NULL | Peran pengguna |
| created_at | TIMESTAMP DEFAULT CURRENT_TIMESTAMP | Waktu pembuatan akun |
| updated_at | TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP | Waktu pembaruan akun |
| last_login | TIMESTAMP NULL | Waktu terakhir login |

### Tabel: `laporan_camat`
| Kolom | Tipe Data | Keterangan |
|-------|------------|------------|
| id_laporan_camat | INT AUTO_INCREMENT PRIMARY KEY | ID laporan camat |
| nama_pelapor | VARCHAR(100) NOT NULL | Nama pelapor |
| nama_desa | VARCHAR(100) NOT NULL | Nama desa asal |
| nama_kecamatan | VARCHAR(100) NOT NULL | Nama kecamatan |
| waktu_kejadian | DATETIME NOT NULL | Waktu terjadinya peristiwa |
| tujuan | ENUM('bupati', 'wakil bupati', 'sekda', 'opd') NOT NULL | Tujuan laporan |
| uraian_laporan | TEXT NOT NULL | Isi detail laporan |
| upload_file | VARCHAR(255) | Path file lampiran |
| status_laporan | ENUM('baru', 'diproses', 'selesai') DEFAULT 'baru' | Status laporan |
| created_at | TIMESTAMP DEFAULT CURRENT_TIMESTAMP | Waktu pembuatan laporan |
| updated_at | TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP | Waktu pembaruan laporan |

### Tabel: `laporan_opd`
| Kolom | Tipe Data | Keterangan |
|-------|------------|------------|
| id_laporan_opd | INT AUTO_INCREMENT PRIMARY KEY | ID laporan OPD |
| nama_opd | VARCHAR(150) NOT NULL | Nama instansi OPD |
| nama_kegiatan | VARCHAR(150) NOT NULL | Nama kegiatan yang dilaporkan |
| uraian_laporan | TEXT NOT NULL | Isi detail laporan |
| tujuan | ENUM('dinas kominfo') DEFAULT 'dinas kominfo' | Tujuan laporan |
| upload_file | VARCHAR(255) | Path file lampiran |
| status_laporan | ENUM('baru', 'diproses', 'selesai') DEFAULT 'baru' | Status laporan |
| created_at | TIMESTAMP DEFAULT CURRENT_TIMESTAMP | Waktu pembuatan laporan |
| updated_at | TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP | Waktu pembaruan laporan |

### Tabel: `kecamatan`
| Kolom | Tipe Data | Keterangan |
|-------|------------|------------|
| id_kecamatan | INT AUTO_INCREMENT PRIMARY KEY | ID unik kecamatan |
| nama_kecamatan | VARCHAR(100) NOT NULL | Nama kecamatan |
| created_at | TIMESTAMP DEFAULT CURRENT_TIMESTAMP | Waktu pembuatan |
| updated_at | TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP | Waktu pembaruan |

### Tabel: `desa`
| Kolom | Tipe Data | Keterangan |
|-------|------------|------------|
| id_desa | INT AUTO_INCREMENT PRIMARY KEY | ID unik desa |
| id_kecamatan | INT NOT NULL | Foreign key ke tabel kecamatan |
| nama_desa | VARCHAR(100) NOT NULL | Nama desa |
| created_at | TIMESTAMP DEFAULT CURRENT_TIMESTAMP | Waktu pembuatan |
| updated_at | TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP | Waktu pembaruan |

---

## 🖥️ Rancangan Fitur per Aktor

### 🔹 **Admin** (✅ **Sudah Diimplementasikan**)
- **Dashboard**: Menampilkan statistik lengkap laporan dengan chart real-time
- **Manajemen Laporan**: Melihat semua laporan masuk dari Camat dan OPD
- **Status Management**: Mengubah status laporan (*baru → diproses → selesai*)
- **Tanggapan**: Memberikan tanggapan untuk setiap laporan
- **Export**: Mengekspor laporan dalam format CSV
- **Statistik**: Melihat grafik laporan berdasarkan kategori, status, dan pelapor
- **User Management**: Melihat dan mengelola informasi pengguna
- **Manajemen Wilayah**: CRUD data kecamatan dan desa dengan cascade delete

### 🔹 **Controller URL Format**
- **Format Umum**: `index.php?controller={controllerName}&action={actionName}`
- **Contoh Format**:
  - `index.php?controller=dashboard&action=admin`
  - `index.php?controller=wilayah&action=index-kecamatan`
  - `index.php?controller=wilayah&action=index-desa`
- **Catatan**: Nama controller menggunakan format camelCase tanpa underscore

### 🔹 **Camat** (🔄 **Dalam Pengembangan**)
- **Dashboard**: Menampilkan laporan pribadi dengan status monitoring
- **Input Laporan**: Mengisi dan mengirim laporan wilayah
- **File Upload**: Mengunggah bukti pendukung (foto, dokumen)
- **Status Tracking**: Melihat status laporan yang dikirim
- **History**: Melihat riwayat laporan sebelumnya

### 🔹 **OPD** (🔄 **Dalam Pengembangan**)
- **Dashboard**: Menampilkan laporan instansi dengan monitoring status
- **Input Laporan**: Membuat laporan kegiatan atau kondisi lapangan
- **File Upload**: Melampirkan bukti foto atau file laporan
- **Status Tracking**: Melihat status tindak lanjut laporan
- **History**: Mengakses riwayat laporan yang telah dikirim

---

## 🧾 Output Aplikasi (✅ **Sebagian Sudah Diimplementasikan**)
- **Dashboard Statistik**: ✅ Jumlah laporan per status (baru, diproses, selesai)
- **Export Data**: ✅ Export CSV
- **Real-time Charts**: ✅ Grafik laporan bulanan dan per kategori
- **Auto-refresh**: ✅ Data dashboard update otomatis setiap 30 detik
- **Toast Notifications**: ✅ Sistem notifikasi modern dengan animasi
- **Manajemen Wilayah**: ✅ CRUD kecamatan dan desa dengan cascade delete
- **Responsive Design**: ✅ Mobile-friendly dengan Bootstrap 5

---

## 🛠️ **Struktur Kode (Sudah Diimplementasikan)**

### 📁 **Direktori & File**
```
helpdesk/
├── config/
│   └── koneksi.php              # Konfigurasi database & constants
├── controllers/
│   ├── AuthController.php      # Controller autentikasi (login/logout)
│   ├── DashboardController.php # Controller dashboard & manajemen
│   ├── LaporanCamatController.php # Controller laporan camat
│   ├── LaporanOPDController.php   # Controller laporan OPD
│   ├── WilayahController.php     # Controller manajemen wilayah (legacy)
│   ├── KecamatanController.php   # Controller khusus kecamatan
│   └── DesaController.php        # Controller khusus desa
├── models/
│   ├── AuthModel.php          # Model database users
│   ├── DashboardModel.php     # Model query dashboard data
│   ├── LaporanCamatModel.php  # Model laporan camat
│   ├── LaporanOPDModel.php    # Model laporan OPD
│   └── WilayahModel.php       # Model CRUD wilayah (kecamatan & desa)
├── views/
│   ├── auth/
│   │   └── index.php           # Halaman login modern & responsive
│   ├── dashboard/
│   │   └── admin/
│   │       └── index.php       # Dashboard admin dengan chart & statistik
│   ├── laporanCamat/
│   │   └── admin/
│   │       └── index.php       # Manajemen laporan camat untuk admin
│   ├── laporanOPD/
│   │   └── admin/
│   │       └── index.php       # Manajemen laporan OPD untuk admin
│   ├── wilayah/
│   │   ├── index.php           # Legacy manajemen wilayah dengan tabs
│   │   ├── index-kecamatan.php # Halaman khusus manajemen kecamatan
│   │   ├── index-desa.php      # Halaman khusus manajemen desa
│   │   ├── form-kecamatan.php  # Form tambah/edit kecamatan
│   │   └── form-desa.php       # Form tambah/edit desa
│   └── template/
│       ├── header.php          # Template header dengan Bootstrap 5
│       ├── navbar.php          # Template navbar
│       ├── sidebar.php         # Template sidebar dengan navigasi
│       ├── setting_panel.php   # Template setting panel
│       └── script.php          # Template footer scripts
├── uploads/                    # Direktori upload file laporan
├── index.php                   # Router utama
├── admin_akun.php              # Script untuk membuat akun admin
└── perancangan.md              # Dokumentasi sistem
```

### 🔧 **Fitur Teknis Yang Diimplementasikan**
- **Authentication System**: Login dengan hash password (bcrypt)
- **Role-based Access Control**: Admin, Camat, OPD dengan hak akses berbeda
- **Session Management**: Secure session handling dengan auto-expire
- **MVC Pattern**: Clean separation of concerns
- **Real-time Dashboard**: Chart.js untuk visualisasi data
- **Toast Notifications**: Custom toast system dengan animasi
- **AJAX Operations**: Form submission dan delete operations dengan AJAX
- **Responsive Design**: Mobile-friendly dengan Bootstrap 5
- **Error Handling**: Comprehensive error handling dengan try-catch
- **Security**: SQL injection prevention, input validation
- **URL Routing**: Format camelCase untuk controller
- **Cascade Delete**: Otomatis hapus desa saat kecamatan dihapus
- **File Upload**: Upload laporan dengan validasi file type
- **Pagination**: Pagination untuk data tables
- **Search & Filter**: Pencarian dan filtering data

---

## 🧰 Teknologi yang Digunakan (Implementasi Saat Ini)
- **Frontend**: HTML5, CSS3 (Bootstrap 5), JavaScript (ES6+), Chart.js
- **Backend**: PHP 8+ Native dengan pola **MVC (Model-View-Controller)**
- **Database**: MySQL 8+ dengan mysqli
- **Styling**: Bootstrap 5 + Custom CSS dengan CSS variables
- **Icons**: Bootstrap Icons 1.11
- **Export**: PHP CSV export
- **Notifications**: Custom JavaScript toast notifications
- **AJAX**: Fetch API untuk asynchronous operations

---

## 🔐 Keamanan Sistem (✅ **Implementasi Lengkap**)
- **Password Security**: Hash password menggunakan bcrypt (PASSWORD_DEFAULT)
- **Session Security**: Secure session dengan proper timeout
- **Role-based Access**: Pembatasan akses berdasarkan role pengguna (admin, opd, camat)
- **Input Validation**: Validasi dan sanitasi data untuk mencegah SQL Injection
- **XSS Prevention**: Escape output untuk mencegah XSS attacks
- **File Upload Security**: Validasi file type dan size untuk upload
- **Transaction Management**: Database transactions untuk data integrity
- **Error Logging**: Error logging untuk debugging tanpa expose sensitive data

---

## 🎮 **Controller Documentation (Lengkap)**

### 🔹 **AuthController.php**
**Purpose**: Mengelola autentikasi user (login, logout, session management)

#### Methods:
```php
public function index() {
    // Menampilkan halaman login
    // Redirect ke dashboard jika sudah login
}

public function login() {
    // Proses login user
    // Validasi username dan password
    // Set session dan redirect sesuai role
}

public function logout() {
    // Proses logout user
    // Destroy session dan redirect ke login
}
```

#### Features:
- ✅ **Password Verification**: Hash password verification dengan bcrypt
- ✅ **Role-based Redirect**: Redirect ke dashboard sesuai role (admin/camat/opd)
- ✅ **Session Management**: Secure session creation dengan timeout
- ✅ **Remember Me**: Optional remember me functionality
- ✅ **Error Handling**: Proper error messages untuk invalid credentials
- ✅ **Rate Limiting**: Protection dari brute force attacks (planned)

---

### 🔹 **DashboardController.php**
**Purpose**: Menampilkan dashboard admin dengan statistik dan chart real-time

#### Methods:
```php
public function admin() {
    // Menampilkan dashboard admin
    // Load statistik laporan (camat & OPD)
    // Generate charts dengan Chart.js
    // Auto-refresh data setiap 30 detik
}

public function getDashboardStats() {
    // AJAX endpoint untuk real-time stats
    // Return JSON data untuk charts
    // Filter by date range jika ada
}
```

#### Features:
- ✅ **Real-time Charts**: Line chart untuk laporan bulanan, pie chart untuk status
- ✅ **Statistics Cards**: Total laporan, laporan baru, diproses, selesai
- ✅ **Auto-refresh**: Refresh data otomatis setiap 30 detik
- ✅ **Date Filtering**: Filter data berdasarkan rentang tanggal
- ✅ **Export Ready**: Data preparation untuk CSV export
- ✅ **AJAX Updates**: Smooth updates tanpa page reload

---

### 🔹 **LaporanCamatController.php**
**Purpose**: Mengelola CRUD laporan camat untuk admin

#### Methods:
```php
public function index() {
    // Menampilkan semua laporan camat
    // Pagination dengan search dan filter
    // Filter by status, tanggal, kecamatan
}

public function detail() {
    // Menampilkan detail laporan spesifik
    // Show form untuk update status dan tanggapan
    // Download attachment jika ada
}

public function updateStatus() {
    // Update status laporan (baru/diproses/selesai)
    // Send notification ke pelapor (planned)
    // Log perubahan status
}

public function addTanggapan() {
    // Menambahkan tanggapan admin pada laporan
    // Update timestamp untuk tanggapan terakhir
}

public function delete() {
    // Hapus laporan (soft/hard delete)
    // Delete associated files
    // Confirmation dialog dengan info related data
}

public function exportCSV() {
    // Export laporan ke format CSV
    // Filter berdasarkan search parameters
    // Include all relevant data fields
}
```

#### Features:
- ✅ **CRUD Operations**: Complete create, read, update, delete
- ✅ **Advanced Filtering**: Filter by status, date range, kecamatan
- ✅ **File Management**: Upload, download, delete attachment files
- ✅ **Status Tracking**: Update status dengan history log
- ✅ **Admin Response**: Add tanggapan/komentar pada laporan
- ✅ **Bulk Operations**: Multiple selection untuk batch operations (planned)
- ✅ **Export System**: CSV export dengan custom filters

---

### 🔹 **LaporanOPDController.php**
**Purpose**: Mengelola CRUD laporan OPD untuk admin

#### Methods:
```php
public function index() {
    // Menampilkan semua laporan OPD
    // Pagination dengan search dan filter
    // Filter by status, tanggal, nama OPD
}

public function detail() {
    // Menampilkan detail laporan spesifik
    // Show form untuk update status dan tanggapan
    // Download attachment jika ada
}

public function updateStatus() {
    // Update status laporan (baru/diproses/selesai)
    // Send notification ke OPD (planned)
}

public function addTanggapan() {
    // Menambahkan tanggapan admin pada laporan OPD
}

public function delete() {
    // Hapus laporan OPD
    // Delete associated files
}

public function exportCSV() {
    // Export laporan OPD ke format CSV
}
```

#### Features:
- ✅ **CRUD Operations**: Complete management untuk laporan OPD
- ✅ **OPD Filtering**: Filter berdasarkan nama OPD, status, tanggal
- ✅ **File Management**: Handle OPD report attachments
- ✅ **Status Workflow**: Complete status management system
- ✅ **Admin Communication**: Tanggapan system untuk OPD reports
- ✅ **Data Export**: CSV export dengan OPD-specific fields

---

### 🔹 **WilayahController.php**
**Purpose**: Controller legacy untuk manajemen wilayah dengan tabs (dihapus sekarang)

#### Methods:
```php
public function index() {
    // Redirect ke index-kecamatan (legacy support)
}

public function indexKecamatan() {
    // Menampilkan halaman kecamatan
    // Load data kecamatan dengan pagination
    // Include statistics cards
}

public function indexDesa() {
    // Menampilkan halaman desa
    // Load data desa dengan kecamatan filter
    // Include search functionality
}

public function formKecamatan() {
    // Form tambah/edit kecamatan
    // Load existing data untuk edit mode
}

public function formDesa() {
    // Form tambah/edit desa
    // Load kecamatan options untuk dropdown
}

public function saveKecamatan() {
    // Save kecamatan data (insert/update)
    // AJAX response dengan success/error
}

public function saveDesa() {
    // Save desa data (insert/update)
    // Validate kecamatan selection
}

public function deleteKecamatan() {
    // Delete kecamatan dengan cascade delete
    // Confirmation dialog dengan related desa info
}

public function deleteDesa() {
    // Delete desa
    // Simple delete tanpa dependencies
}

public function getKecamatanStats() {
    // Get related desa info untuk delete confirmation
    // Return JSON dengan count dan list desa
}
```

#### Features:
- ✅ **Dual Management**: Kecamatan dan desa dalam satu controller
- ✅ **Cascade Delete**: Otomatis hapus desa saat kecamatan dihapus
- ✅ **AJAX Operations**: Smooth CRUD operations tanpa reload
- ✅ **Data Validation**: Proper validation untuk all inputs
- ✅ **Statistics**: Real-time stats untuk kecamatan & desa
- ✅ **Search & Filter**: Advanced search capabilities

---

### 🔹 **KecamatanController.php**
**Purpose**: Controller khusus untuk manajemen kecamatan (terpisah)

#### Methods:
```php
public function index() {
    // Menampilkan halaman utama kecamatan
    // Pagination dengan search
    // Statistics card total kecamatan
}

public function form() {
    // Form tambah/edit kecamatan
    // Load existing data untuk edit mode
}

public function save() {
    // Save kecamatan (insert/update)
    // AJAX response dengan redirect
}

public function delete() {
    // Delete kecamatan dengan cascade delete
    // Get related desa info untuk confirmation
}

public function getStats() {
    // AJAX endpoint untuk delete confirmation
    // Return related desa count dan list
}
```

#### Features:
- ✅ **Focused Management**: Khusus untuk kecamatan operations
- ✅ **Clean Interface**: Dedicated UI untuk kecamatan
- ✅ **Cascade Delete**: Safe deletion dengan related data info
- ✅ **AJAX Workflow**: Modern interaction patterns
- ✅ **Navigation Integration**: Link ke halaman desa

---

### 🔹 **DesaController.php**
**Purpose**: Controller khusus untuk manajemen desa (terpisah)

#### Methods:
```php
public function index() {
    // Menampilkan halaman utama desa
    // Pagination dengan search dan kecamatan filter
    // Statistics card total desa
}

public function form() {
    // Form tambah/edit desa
    // Load kecamatan options dropdown
}

public function save() {
    // Save desa (insert/update)
    // Validate kecamatan selection
    // AJAX response dengan redirect
}

public function delete() {
    // Delete desa
    // Simple delete operation
}

public function getKecamatanOptions() {
    // AJAX endpoint untuk kecamatan dropdown
    // Return JSON dengan kecamatan list
}
```

#### Features:
- ✅ **Dedicated Management**: Fokus khusus untuk desa operations
- ✅ **Kecamatan Integration**: Dropdown untuk kecamatan selection
- ✅ **Advanced Filtering**: Filter by kecamatan + search
- ✅ **Clean UI**: Modern interface khusus desa
- ✅ **Navigation**: Link ke halaman kecamatan

---

## 🗃️ **Model Documentation (Lengkap)**

### 🔹 **AuthModel.php**
**Purpose**: Handle semua database operations untuk user authentication dan management

#### Methods:
```php
public function login($username, $password) {
    // Verify user credentials
    // Return user data jika valid
    // Update last_login timestamp
}

public function getUserById($id_user) {
    // Get user data by ID
    // Return user information
}

public function getUserByUsername($username) {
    // Get user data by username
    // Used untuk login validation
}

public function createUser($userData) {
    // Create new user account
    // Hash password dengan bcrypt
    // Return new user ID
}

public function updateUser($id_user, $userData) {
    // Update user information
    // Handle password update jika ada
}

public function changePassword($id_user, $newPassword) {
    // Update user password
    // Hash new password sebelum save
}

public function getAllUsers($page = 1, $limit = 10, $search = '') {
    // Get all users dengan pagination
    // Search by username, email, jabatan
}

public function deleteUser($id_user) {
    // Delete user account
    // Soft delete atau hard delete
}
```

#### Features:
- ✅ **Secure Authentication**: Bcrypt password hashing
- ✅ **User Management**: Complete CRUD operations
- ✅ **Session Integration**: Works dengan session management
- ✅ **Search & Pagination**: Advanced user listing
- ✅ **Password Security**: Secure password update/change
- ✅ **Audit Trail**: Last login tracking

---

### 🔹 **DashboardModel.php**
**Purpose**: Menghandle semua data queries untuk dashboard statistics dan charts

#### Methods:
```php
public function getLaporanStats() {
    // Get total laporan statistics
    // Count by status (baru/diproses/selesai)
    // Count by type (camat/opd)
}

public function getMonthlyLaporanData($year = null) {
    // Get monthly laporan data untuk charts
    // Filter by year jika specified
    // Return formatted data untuk Chart.js
}

public function getRecentLaporan($limit = 5) {
    // Get recent laporan entries
    // Both camat and OPD laporan
    // Order by created_at DESC
}

public function getLaporanByKecamatan() {
    // Get laporan distribution by kecamatan
    // For geographic analysis
}

public function getOPDStats() {
    // Get statistics per OPD
    // Count reports by OPD name
}
```

#### Features:
- ✅ **Real-time Statistics**: Up-to-date counts dan metrics
- ✅ **Chart Data Preparation**: Format data untuk Chart.js
- ✅ **Time-based Analytics**: Monthly, yearly statistics
- ✅ **Geographic Data**: Kecamatan-based statistics
- ✅ **Performance Optimized**: Efficient queries untuk dashboard
- ✅ **Date Range Filtering**: Flexible date filtering

---

### 🔹 **LaporanCamatModel.php**
**Purpose**: Handle semua database operations untuk laporan camat

#### Methods:
```php
public function getAllLaporan($page = 1, $limit = 10, $search = '', $filters = []) {
    // Get all laporan camat dengan pagination
    // Advanced filtering (status, date, kecamatan)
    // Search in multiple fields
}

public function getLaporanById($id_laporan) {
    // Get single laporan by ID
    // Include all related data
}

public function createLaporan($data) {
    // Create new laporan camat
    // Handle file upload
    // Validate required fields
}

public function updateLaporan($id_laporan, $data) {
    // Update existing laporan
    // Handle file replacement
    // Update timestamps
}

public function deleteLaporan($id_laporan) {
    // Delete laporan dan files
    // File cleanup dan database deletion
}

public function updateStatus($id_laporan, $status, $tanggapan = '') {
    // Update laporan status
    // Add admin tanggapan
    // Log status changes
}

public function getLaporanStats($filters = []) {
    // Get statistics untuk laporan camat
    // Count by status, date range, etc.
}

public function exportToCSV($filters = []) {
    // Export data ke CSV format
    // Apply filters dan proper formatting
}
```

#### Features:
- ✅ **Advanced Filtering**: Multiple filter combinations
- ✅ **File Management**: Upload, update, delete attachments
- ✅ **Status Workflow**: Complete status management
- ✅ **Audit Logging**: Track semua perubahan
- ✅ **Data Export**: CSV export dengan custom filters
- ✅ **Search**: Full-text search dalam relevant fields

---

### 🔹 **LaporanOPDModel.php**
**Purpose**: Handle semua database operations untuk laporan OPD

#### Methods:
```php
public function getAllLaporan($page = 1, $limit = 10, $search = '', $filters = []) {
    // Get all laporan OPD dengan pagination
    // Filter by status, OPD name, date range
}

public function getLaporanById($id_laporan) {
    // Get single OPD laporan by ID
}

public function createLaporan($data) {
    // Create new OPD laporan
    // Handle file upload
    // Validate OPD data
}

public function updateLaporan($id_laporan, $data) {
    // Update existing OPD laporan
    // Handle file updates
}

public function deleteLaporan($id_laporan) {
    // Delete OPD laporan dan files
}

public function updateStatus($id_laporan, $status, $tanggapan = '') {
    // Update OPD laporan status
    // Add admin tanggapan
}

public function getOPDList() {
    // Get unique OPD names untuk dropdown
}

public function getLaporanStats($filters = []) {
    // Get OPD laporan statistics
}

public function exportToCSV($filters = []) {
    // Export OPD data ke CSV
}
```

#### Features:
- ✅ **OPD Management**: Complete CRUD untuk OPD reports
- ✅ **Institution Tracking**: Track laporan by OPD name
- ✅ **File Handling**: OPD attachment management
- ✅ **Statistics**: OPD-specific analytics
- ✅ **Data Export**: OPD report exports
- ✅ **Search Integration**: Search dalam OPD reports

---

### 🔹 **WilayahModel.php**
**Purpose**: Handle semua database operations untuk kecamatan dan desa management

#### Methods:
```php
// ========== KECAMATAN METHODS ==========
public function getAllKecamatan($page = 1, $limit = 10, $search = '') {
    // Get all kecamatan dengan pagination
    // Search by nama_kecamatan
    // Order by nama_kecamatan ASC
}

public function getKecamatanById($id_kecamatan) {
    // Get single kecamatan by ID
}

public function insertKecamatan($data) {
    // Insert new kecamatan
    // Validate nama_kecamatan uniqueness
}

public function updateKecamatan($id_kecamatan, $data) {
    // Update existing kecamatan
    // Handle nama_kecamatan changes
}

public function deleteKecamatan($id_kecamatan) {
    // Delete kecamatan dengan CASCADE DELETE
    // Delete all related desa records
    // Transaction management untuk data integrity
}

// ========== DESA METHODS ==========
public function getAllDesa($page = 1, $limit = 10, $search = '', $kecamatan_filter = '') {
    // Get all desa dengan pagination
    // Search by nama_desa atau nama_kecamatan
    // Filter by kecamatan
    // JOIN dengan kecamatan table
}

public function getDesaById($id_desa) {
    // Get single desa dengan kecamatan info
}

public function insertDesa($data) {
    // Insert new desa
    // Validate kecamatan exists
}

public function updateDesa($id_desa, $data) {
    // Update existing desa
    // Validate kecamatan selection
}

public function deleteDesa($id_desa) {
    // Delete desa record
}

// ========== UTILITY METHODS ==========
public function getKecamatanOptions() {
    // Get kecamatan list untuk dropdown
    // Used dalam desa form
}

public function getStatistics() {
    // Get total counts untuk statistics cards
    // Count kecamatan dan desa
}

public function getKecamatanWithDesaStats($id_kecamatan) {
    // Get kecamatan info dengan desa count
    // List all desa dalam kecamatan
}
```

#### Features:
- ✅ **Dual Management**: Kecamatan dan desa dalam satu model
- ✅ **Cascade Delete**: Safe kecamatan deletion dengan desa cleanup
- ✅ **Transaction Safety**: Database transactions untuk data integrity
- ✅ **Data Relationships**: Proper JOIN operations
- ✅ **Validation**: Input validation untuk data integrity
- ✅ **Statistics**: Real-time counts dan analytics
- ✅ **Search Integration**: Advanced search capabilities
- ✅ **Filtering**: Multiple filter options

---

## 📨 **Template Frontend Structure (Admin)**

### 📋 **Standard Template Structure**
```php
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
              <!-- Page Content Here -->
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php include 'template/script.php'; ?>
</body>
</html>
```

### 🔔 **Toast Notification System**
```javascript
// Custom toast notification function
function showNotification(message, type = 'success') {
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

  // Add content and auto-remove after 5 seconds
  // ... implementation details
}
```

### 🎨 **Features**
- **Responsive Layout**: Mobile-first design dengan Bootstrap 5
- **Modern UI**: Clean design dengan card layouts dan smooth animations
- **Interactive Sidebar**: Collapsible navigation dengan active state indicators
- **Real-time Updates**: Auto-refresh dashboard data
- **AJAX Forms**: Smooth form submissions tanpa page reload
- **Toast Notifications**: User-friendly feedback system
- **Data Tables**: Responsive tables dengan pagination dan search
- **Confirmation Dialogs**: Safety confirmations untuk delete operations

---

## 📈 **Status Implementasi & Roadmap**

### ✅ **Versi 2.0 - Sudah Diimplementasikan (Selesai)**
- **Authentication System**: Login/logout dengan role-based access
- **Admin Dashboard**: Dashboard lengkap dengan real-time charts
- **Laporan Management**: CRUD laporan camat dan OPD untuk admin
- **Database Structure**: Tabel users, laporan, dan wilayah sudah siap
- **Security**: Password hashing, session management, input validation
- **Responsive UI**: Modern interface dengan mobile support
- **Export System**: CSV export untuk laporan
- **Toast Notifications**: Custom notification system
- **Manajemen Wilayah**: CRUD kecamatan dan desa dengan cascade delete
- **File Upload**: Upload laporan dengan validasi

### 🔄 **Versi 2.1 - Dalam Pengembangan (Q1 2025)**
- **Camat Dashboard**: Dashboard untuk pelapor tingkat kecamatan
- **OPD Dashboard**: Dashboard untuk pelapor tingkat OPD
- **Create Laporan**: Form input laporan dengan file upload
- **Status Management**: Update status laporan dengan notifikasi
- **PDF Export**: Export laporan ke format PDF
- **Email Notifications**: Email alerts untuk status changes

### 🚀 **Versi 3.0 - Rencana Pengembangan (Q2 2025)**
- **Mobile App**: Native mobile application
- **Real-time Notifications**: WebSocket untuk live updates
- **Advanced Analytics**: Reporting tools yang lebih komprehensif
- **API Integration**: RESTful API untuk third-party integrations
- **Audit System**: Complete audit trail untuk compliance
- **Multi-language Support**: Bahasa Indonesia & English

## 🚀 **Quick Start Guide**
1. **Setup Database**: Import MySQL schema (tabel users, laporan, kecamatan, desa)
2. **Create Admin Account**: Jalankan `admin_akun.php`
3. **Login**: Akses `index.php` dengan:
   - Username: `admin`
   - Password: `admin12345`
4. **Dashboard**: Otomatis redirect ke dashboard admin
5. **Manage Laporan**: Akses laporan melalui sidebar menu
6. **Manage Wilayah**: Akses manajemen kecamatan dan desa terpisah

## 📞 **Support & Maintenance**
- **Documentation**: Lihat file perancangan.md untuk detail teknis
- **Bug Reports**: Log error tersimpan di server logs
- **Updates**: Update rutin untuk security patches
- **Backup**: Daily backup database dan file penting

## 🧩 Penutup
Aplikasi **LaporBup** dengan model **helpdesk pelaporan terpusat** telah berhasil diimplementasikan sesuai perancangan. Sistem ini memudahkan **Camat dan OPD** dalam menyampaikan laporan secara cepat, aman, dan terdokumentasi. **Admin (Dinas Kominfo)** dapat mengelola, menindaklanjuti, dan merekap setiap laporan secara transparan, efisien, dan akuntabel melalui dashboard modern yang user-friendly.

**Fitur terbaru:** Manajemen wilayah terpisah untuk kecamatan dan desa dengan cascade delete, custom toast notifications, dan responsive design yang optimal untuk semua perangkat!

**Sistem siap digunakan untuk meningkatkan transparansi dan akuntabilitas proses pelaporan pemerintah daerah!** 🎉