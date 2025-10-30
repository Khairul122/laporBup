# LaporBup (Lapor Bupati) - Sistem Informasi Laporan Gabungan Wilayah Kecamatan (SILAP GAWAT)

**LaporBup** atau **SILAP GAWAT (Sistem Informasi Laporan Gabungan Wilayah Kecamatan)** adalah sistem informasi pelaporan berbasis web yang berfungsi sebagai pusat layanan aduan dan pelaporan wilayah untuk Pemerintah Kabupaten Mandailing Natal. Sistem ini mengadopsi konsep helpdesk, di mana Camat dan OPD berperan sebagai pelapor, sedangkan Admin (Dinas Kominfo) bertugas sebagai penerima, pengelola, dan pengarah tindak lanjut laporan.

## 📋 Daftar Isi

- [Tentang Proyek](#tentang-proyek)
- [Fitur Utama](#fitur-utama)
- [Teknologi yang Digunakan](#teknologi-yang-digunakan)
- [Struktur Proyek](#struktur-proyek)
- [Instalasi](#instalasi)
- [Penggunaan](#penggunaan)
- [Kontribusi](#kontribusi)
- [Lisensi](#lisensi)

## 🔍 Tentang Proyek

Sistem ini dirancang untuk:
- Menyediakan sarana pelaporan digital bagi Camat dan OPD
- Mempercepat penyampaian laporan dari instansi pelapor ke Dinas Kominfo
- Menjadikan proses pelaporan lebih terstruktur, terdokumentasi, dan mudah ditindaklanjuti
- Meningkatkan transparansi dan akuntabilitas proses pelaporan pemerintah daerah

## ✨ Fitur Utama

### 🔐 Sistem Otentikasi dan Otorisasi
- Login/logout dengan hash password
- Sistem role-based access control (Admin, Camat, OPD)
- Session management yang aman

### 📊 Dashboard Admin
- Statistik real-time laporan
- Grafik interaktif menggunakan Chart.js
- Monitoring laporan secara keseluruhan

### 📝 Manajemen Laporan
- CRUD laporan Camat dan OPD
- Sistem status laporan (baru, diproses, selesai)
- Fitur pencarian dan filter lanjutan
- Ekspor data ke format CSV dan PDF

### 🌍 Manajemen Wilayah
- CRUD data kecamatan dan desa
- Cascade delete untuk menjaga integritas data
- Sistem hierarki wilayah

### 📱 Responsif
- Desain mobile-friendly
- UI/UX modern menggunakan Bootstrap 5
- Notifikasi toast yang interaktif

## 🛠 Teknologi yang Digunakan

### Backend
- **PHP 8+** - Bahasa pemrograman utama
- **MySQL 8+** - Database untuk menyimpan data
- **MVC Pattern** - Arsitektur aplikasi

### Frontend
- **HTML5, CSS3** - Struktur dan tampilan dasar
- **Bootstrap 5** - Framework CSS untuk responsive design
- **JavaScript (ES6+)** - Interaksi dinamis
- **Chart.js** - Visualisasi data

### Eksternal Libraries
- **TCPDF** - Pembuatan dokumen PDF
- **PHPSpreadsheet** - Penanganan file Excel

## 📁 Struktur Proyek

```
helpdesk/
├── assets/              # File CSS, JS, dan gambar
│   ├── css/
│   ├── js/
│   └── images/
├── config/              # Konfigurasi sistem
├── controllers/         # File-file controller 
├── models/              # File-file model
├── template/            # File-file template (header, sidebar, dll)
├── uploads/             # Folder untuk upload file
├── vendor/              # Dependencies eksternal
└── views/               # File-file view tampilan
    ├── auth/
    ├── dashboard/
    ├── laporan/
    └── wilayah/
├── composer.json        # Dependencies PHP
├── index.php            # Router utama
├── template.php         # Template utama
├── LaporanController.php # Controller untuk laporan
└── README.md            # File dokumentasi ini
```

## ⚙️ Instalasi

### Prasyarat
- PHP 8.0 atau lebih baru
- MySQL 8.0 atau lebih baru
- Web server (Apache/Nginx)
- Composer

### Langkah-langkah Instalasi

1. **Clone repository atau download file**:
   ```bash
   git clone [URL_REPOSITORY] helpdesk
   cd helpdesk
   ```

2. **Install dependencies**:
   ```bash
   composer install
   ```

3. **Konfigurasi database**:
   - Buat database MySQL baru
   - Impor file `helpdesk.sql` (jika tersedia) atau buat tabel sesuai dokumentasi
   - Edit file konfigurasi database di `config/koneksi.php`

4. **Setup file permissions**:
   ```bash
   chmod -R 755 uploads/
   ```

5. **Jalankan aplikasi**:
   - Akses melalui web browser
   - Default login untuk admin bisa dibuat melalui script `admin_akun.php`

## 🚀 Penggunaan

### Login Default
- **Username**: admin
- **Password**: admin12345 (atau sesuai dengan setup akun)

### Alur Kerja Utama
1. **Camat atau OPD** membuat laporan melalui aplikasi
2. Laporan dikirim ke **Admin (Dinas Kominfo)**
3. **Admin** memverifikasi laporan dan memperbarui status
4. Setelah selesai, status diubah menjadi "selesai"
5. **Camat/OPD** dapat memantau status laporan

### Halaman Utama
- `/` - Halaman login
- `/index.php?controller=dashboard&action=admin` - Dashboard admin
- `/index.php?controller=laporan&action=index` - Manajemen laporan
- `/index.php?controller=wilayah&action=index` - Manajemen wilayah

## 📊 Manajemen Laporan

### Jenis Laporan
1. **Laporan Camat**: Laporan dari tingkat kecamatan
2. **Laporan OPD**: Laporan dari Organisasi Perangkat Daerah

### Status Laporan
- **Baru**: Laporan baru diterima
- **Diproses**: Laporan sedang ditangani
- **Selesai**: Laporan telah selesai ditindaklanjuti

## 🌍 Manajemen Wilayah

Sistem manajemen wilayah terdiri dari:
- **Kecamatan**: Tingkat wilayah administratif
- **Desa**: Sub wilayah dari kecamatan

Fitur manajemen wilayah meliputi:
- CRUD data kecamatan dan desa
- Cascade delete (hapus desa saat kecamatan dihapus)
- Filter laporan berdasarkan wilayah

## 🖨️ Ekspor Data

Sistem mendukung ekspor data dalam berbagai format:
- **CSV**: Untuk analisis data
- **PDF**: Untuk laporan resmi
- **Excel**: Untuk dokumentasi

## 🔐 Keamanan

- **Password Security**: Hash password menggunakan bcrypt
- **Session Security**: Session management yang aman
- **Input Validation**: Validasi dan sanitasi input
- **SQL Injection Prevention**: Prepared statements
- **XSS Prevention**: Output escaping

## 📁 File Upload

Sistem mendukung upload file untuk:
- Lampiran laporan
- Bukti pendukung
- Dokumentasi tambahan

## 📈 Fitur Dashboard

### Admin Dashboard
- Statistik laporan secara real-time
- Grafik interaktif
- Monitoring status laporan
- Tabel laporan terbaru
- Filter dan pencarian lanjutan

## ✅ Status Implementasi

### Fitur yang Sudah Tersedia
- ✅ Sistem otentikasi dan otorisasi
- ✅ Dashboard admin lengkap
- ✅ Manajemen laporan (Camat & OPD)
- ✅ Manajemen wilayah (Kecamatan & Desa)
- ✅ Ekspor data (CSV, PDF)
- ✅ UI/UX responsive
- ✅ Sistem notifikasi
- ✅ Cascade delete

### Fitur yang Akan Datang
- 🔄 Dashboard Camat dan OPD
- 🔄 Fitur laporan oleh Camat/OPD
- 🔄 Email notifikasi
- 🔄 Integrasi mobile app

## 🤝 Kontribusi

1. Fork repository
2. Buat feature branch (`git checkout -b feature/fitur-baru`)
3. Commit perubahan (`git commit -m 'Tambah fitur baru'`)
4. Push ke branch (`git push origin feature/fitur-baru`)
5. Buat Pull Request

## 📄 Lisensi

Proyek ini dilisensikan di bawah MIT License - lihat file [LICENSE](LICENSE) untuk detail selengkapnya.

## 📞 Dukungan

Untuk bantuan teknis atau pertanyaan, silakan:
- Hubungi tim pengembang
- Lihat file dokumentasi teknis (`perancangan.md`)
- Cek issue di repository (jika tersedia)

---

**Dikembangkan dengan ❤️ untuk Pemerintah Kabupaten Mandailing Natal**