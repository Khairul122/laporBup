# 🏛️ PERANCANGAN SISTEM INFORMASI **LaporBup (Lapor Bupati)**

## 📘 Deskripsi Umum
**LaporBup (Lapor Bupati)** atau **SILAP GAWAT (Sistem Informasi Laporan Gabungan Wilayah Kecamatan)** adalah sistem informasi pelaporan berbasis web yang berfungsi sebagai pusat layanan aduan dan pelaporan wilayah.
Sistem ini mengadopsi konsep **helpdesk**, di mana **Camat** dan **OPD** berperan sebagai pelapor, sedangkan **Admin (Dinas Kominfo)** bertugas sebagai penerima, pengelola, dan pengarah tindak lanjut laporan.

**Status Implementasi:** ✅ **Sudah Dikembangkan (versi 1.0)**

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
| **Admin (Dinas Kominfo)** | Petugas utama penerima dan pengelola laporan | - Menerima laporan dari Camat dan OPD<br>- Melihat detail laporan beserta statusnya<br>- Mengubah status laporan (*baru*, *diproses*, *selesai*)<br>- Memberikan tanggapan atau catatan pada laporan<br>- Mencetak laporan (PDF/Excel/CSV)<br>- Mengelola akun pengguna |
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

---

## 🖥️ Rancangan Fitur per Aktor

### 🔹 **Admin** (✅ **Sudah Diimplementasikan**)
- **Dashboard**: Menampilkan statistik lengkap laporan dengan chart real-time
- **Manajemen Laporan**: Melihat semua laporan masuk dari Camat dan OPD
- **Status Management**: Mengubah status laporan (*baru → diproses → selesai*)
- **Tanggapan**: Memberikan tanggapan untuk setiap laporan
- **Export**: Mengekspor laporan dalam format CSV (PDF/Excel coming soon)
- **Statistik**: Melihat grafik laporan berdasarkan kategori, status, dan pelapor
- **User Management**: Melihat dan mengelola informasi pengguna

### 🔹 **Controller URL Format**
- **Format Umum**: `index.php?controller={controllerName}&action={actionName}`
- **Contoh Format**: `index.php?controller=laporanCamat&action=index`
- **Catatan**: Nama controller menggunakan format camelCase tanpa underscore meskipun nama file controller menggunakan format PascalCase

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
- **Export Data**: ✅ Export CSV (PDF/Excel coming soon)
- **Real-time Charts**: ✅ Grafik laporan bulanan dan per kategori
- **Auto-refresh**: ✅ Data dashboard update otomatis setiap 30 detik
- **Notifikasi Otomatis**: 🔄 Dalam pengembangan (notifikasi status laporan)
- **Riwayat Aktivitas**: 🔄 Dalam pengembangan (audit sistem)

---

## 🛠️ **Struktur Kode (Sudah Diimplementasikan)**

### 📁 **Direktori & File**
```
helpdesk/
├── config/
│   └── koneksi.php              # Konfigurasi database & constants
├── controllers/
│   ├── AuthController.php      # Controller autentikasi (login/logout)
│   └── DashboardController.php # Controller dashboard & manajemen
├── models/
│   ├── AuthModel.php          # Model database users
│   └── DashboardModel.php     # Model query dashboard data
├── views/
│   ├── auth/
│   │   └── index.php           # Halaman login modern & responsive
│   └── dashboard/
│       └── admin/
│           └── index.php       # Dashboard admin dengan chart & statistik
├── index.php                   # Router utama
├── admin_akun.php              # Script untuk membuat akun admin
└── PERANCANGAN.md              # Dokumentasi sistem
```

### 🔧 **Fitur Teknis Yang Diimplementasikan**
- **Authentication System**: Login dengan hash password (bcrypt)
- **Role-based Access Control**: Admin, Camat, OPD dengan hak akses berbeda
- **Session Management**: Secure session handling dengan auto-expire
- **MVC Pattern**: Clean separation of concerns
- **Real-time Dashboard**: Chart.js untuk visualisasi data
- **Responsive Design**: Mobile-friendly dengan Bootstrap 5
- **Error Handling**: Comprehensive error handling dengan try-catch
- **Security**: SQL injection prevention, input validation
- **URL Routing**: Format camelCase untuk controller (misal: laporanCamat, bukan laporan_camat)

---

## 🧰 Teknologi yang Digunakan (Implementasi Saat Ini)
- **Frontend**: HTML5, CSS3 (Bootstrap 5), JavaScript (ES6+), Chart.js
- **Backend**: PHP 8+ Native dengan pola **MVC (Model-View-Controller)**
- **Database**: MySQL 8+ dengan mysqli
- **Styling**: Bootstrap 5 + Custom CSS dengan CSS variables
- **Animations**: AOS (Animate On Scroll)
- **Icons**: Bootstrap Icons 1.11
- **Export**: PHP CSV export (TCPDF/Excel planned)

---

## 🔐 Keamanan Sistem (✅ **Implementasi Lengkap**)
- **Password Security**: Hash password menggunakan bcrypt (PASSWORD_DEFAULT)
- **Session Security**: Secure session dengan proper timeout
- **Role-based Access**: Pembatasan akses berdasarkan role pengguna (admin, opd, camat)
- **Input Validation**: Validasi dan sanitasi data untuk mencegah SQL Injection
- **XSS Prevention**: Escape output untuk mencegah XSS attacks
- **CSRF Protection**: Token-based protection (planned)
- **Error Logging**: Error logging untuk debugging tanpa expose sensitive data  

---

## 📈 **Status Implementasi & Roadmap**

### ✅ **Versi 1.0 - Sudah Diimplementasikan (Selesai)**
- **Authentication System**: Login/logout dengan role-based access
- **Admin Dashboard**: Dashboard lengkap dengan real-time charts
- **Database Structure**: Tabel users dan laporan sudah siap
- **Security**: Password hashing, session management, input validation
- **Responsive UI**: Modern interface dengan mobile support
- **Export System**: CSV export (PDF/Excel dalam pengembangan)

### 🔄 **Versi 1.1 - Dalam Pengembangan (Q1 2025)**
- **Camat Dashboard**: Dashboard untuk pelapor tingkat kecamatan
- **OPD Dashboard**: Dashboard untuk pelapor tingkat OPD
- **Create Laporan**: Form input laporan dengan file upload
- **Status Management**: Update status laporan dengan notifikasi
- **PDF Export**: Export laporan ke format PDF
- **Email Notifications**: Email alerts untuk status changes

### 🚀 **Versi 2.0 - Rencana Pengembangan (Q2 2025)**
- **Mobile App**: Native mobile application
- **Real-time Notifications**: WebSocket untuk live updates
- **Advanced Analytics**: Reporting tools yang lebih komprehensif
- **API Integration**: RESTful API untuk third-party integrations
- **Audit System**: Complete audit trail untuk compliance
- **Multi-language Support**: Bahasa Indonesia & English

## 🚀 **Quick Start Guide**
1. **Setup Database**: Import MySQL schema (tabel users dan laporan)
2. **Create Admin Account**: Jalankan `admin_akun.php`
3. **Login**: Akses `index.php` dengan:
   - Username: `admin`
   - Password: `admin12345`
4. **Dashboard**: Otomatis redirect ke dashboard admin
5. **Manage Laporan**: Mulai kelola laporan dari dashboard

## 📞 **Support & Maintenance**
- **Documentation**: Lihat file PERANCANGAN.md untuk detail teknis
- **Bug Reports**: Log error tersimpan di server logs
- **Updates**: Update rutin untuk security patches
- **Backup**: Daily backup database dan file penting

## 🧩 Penutup
Aplikasi **LaporBup** dengan model **helpdesk pelaporan terpusat** telah berhasil diimplementasikan sesuai perancangan. Sistem ini memudahkan **Camat dan OPD** dalam menyampaikan laporan secara cepat, aman, dan terdokumentasi. **Admin (Dinas Kominfo)** dapat mengelola, menindaklanjuti, dan merekap setiap laporan secara transparan, efisien, dan akuntabel melalui dashboard modern yang user-friendly.

**Sistem siap digunakan untuk meningkatkan transparansi dan akuntabilitas proses pelaporan pemerintah daerah!** 🎉
