# AGENT.md — RESTful Routing Migration Report

Referensi: `prd_restful_routes.html` (PRD-LB-2026-002)

## Status Sebelum Sesi Ini

Migrasi RESTful sudah ~95% diterapkan sebelumnya:
- `core/Router.php` mendukung regex path param (`{id}`) dan HTTP method spoofing (`_method`).
- `core/routes.php` sudah mendaftarkan rute RESTful untuk seluruh resource di tabel PRD Section 4: `opd`, `kecamatan`, `desa`, `data-pelapor`, `profiles`, `wa-messages`, `laporan-opd`, `laporan-camat`, dan varian admin masing-masing.
- `core/helpers.php::route()` sudah menghasilkan URL RESTful dinamis via `build_route_path()`.
- Sebagian besar controller (`OPDController`, `KecamatanController`, `DesaController`, `LaporanOPDController`, `LaporanCamatController`, `LaporanOPDAdminController`, `LaporanCamatAdminController`, `ProfileController`, `WAGatewayController`, `DataPelaporController`) menerima `$id` langsung dari Router, dengan fallback `?? $_GET['id']` yang memang diizinkan PRD Section 5 untuk kelenturan.
- Form edit/update/delete di views sudah memakai hidden input `_method` (PUT/DELETE).

## Gap yang Ditemukan & Diperbaiki Sesi Ini

### 1. `controllers/WilayahController.php` — dihapus
File ini orphan/dead code: tidak terdaftar di `core/routes.php` (sudah digantikan oleh `KecamatanController` dan `DesaController`), satu-satunya referensi tersisa adalah classmap auto-generated Composer. Dihapus total.

### 2. Endpoint tanda-tangan di `LaporanController` — dikonversi ke RESTful
Endpoint ini sebelumnya di luar tabel PRD dan masih memakai query string `?id=&type=`. Atas permintaan eksplisit, dikonversi penuh ke path param:

| Method | Path Lama | Path Baru |
|---|---|---|
| GET | `laporan/tanda-tangan?id=&type=` | `laporan/tanda-tangan/{type}/{id}` |
| GET | `laporan/tanda-tangan/pdf?id=&type=` | `laporan/tanda-tangan/{type}/{id}/pdf` |
| POST | `laporan/tanda-tangan` | tidak berubah (tidak butuh id) |

File yang diubah:
- `core/routes.php` — registrasi 2 rute di atas dengan `{type}/{id}`.
- `controllers/LaporanController.php` — `tandaTangan($type, $id)` dan `generatePDFWithSignature($type, $id)` menerima parameter langsung dari Router, tidak lagi membaca `$_GET`.
- `views/laporan/tanda-tangan.php` — seluruh pembacaan `$_GET['type']`/`$_GET['id']` diganti memakai variabel `$type`/`$id` yang diteruskan controller via `include` (shared scope). Pemanggilan generate PDF di JS dirender langsung sebagai URL RESTful lewat `route()`, bukan template literal query string.
- `views/laporan/index.php` — link tombol "TTD" (baris 243 & 518) memakai `route('laporan', 'tandaTangan', ['type' => ..., 'id' => 0])`.
- `core/helpers.php::build_route_path()` — menambahkan pembangunan path `laporan/tanda-tangan/{type}/{id}` (dan varian `/pdf`) untuk action `tandatangan`/`generatepdfwithsignature`; key `type` diunset dari `$extra` setelah dipakai agar tidak ikut ter-append sebagai query string.

### 3. Bug fatal query SQL `laporan_camat` — diperbaiki
`LaporanModel::getLaporanCamat()` dan `LaporanCamatAdminModel::getAllLaporanCamat()` membangun klausa WHERE pencarian dengan `lc.nama_kegiatan`, kolom yang **tidak ada** di tabel `laporan_camat` (kolom itu hanya milik `laporan_opd`; SELECT bahkan sudah menambal dengan `'' as nama_kegiatan`). Menyebabkan `Unknown column 'lc.nama_kegiatan'` saat fitur cari dipakai. Diperbaiki dengan mengganti ke kolom asli yang tersedia: `lc.nama_kecamatan`, `lc.nama_pelapor`, `lc.uraian_laporan`.

### 4. Link route rusak di halaman "Cetak Laporan" (`views/laporan/index.php`) — diperbaiki
Halaman ini adalah tujuan menu sidebar "Cetak Laporan" (`route('laporan', 'index')`). Tiga link JS memanggil `url()` langsung dengan path gaya RPC lama yang tidak cocok dengan rute RESTful terdaftar di `routes.php`, sehingga 404:

| Lokasi | Sebelum | Sesudah |
|---|---|---|
| Tombol PDF | `url('laporan/generatePDF')` | `route('laporan', 'generatePDF')` → `laporan/pdf` |
| Tombol Excel | `url('laporan/generateExcel')` | `route('laporan', 'generateExcel')` → `laporan/excel` |
| Switch tab | `url('laporan/index')` | `route('laporan', 'index')` → `laporan` |

Sekaligus dibersihkan hidden input `controller`/`action` peninggalan routing RPC lama pada form filter (`camatFilterForm`, `opdFilterForm`) beserta `params.delete('controller'/'action')` di JS yang sudah tidak relevan dengan router berbasis path saat ini.

### 5. `controllers/LaporanOPDCetakController.php` — dihapus (dead & broken code)
Terdaftar di `routes.php` (`laporan-opd-cetak`, `/pdf`, `/excel`) tapi **tidak pernah ditautkan dari UI manapun**, dan method `index()`-nya `include 'views/laporan-opd-cetak/index.php'` — file ini tidak ada di filesystem, sehingga kalau rute itu pernah diakses akan fatal error. Controller, 3 baris registrasi rute terkait di `core/routes.php`, dan entry `laporanopdcetak` di `core/helpers.php::route()`/`build_route_path()` dihapus seluruhnya.

## Verifikasi

- `php -l` dijalankan terhadap seluruh file `.php` di `controllers/`, `core/`, dan `views/` — tidak ada syntax error, termasuk setelah penghapusan `LaporanOPDCetakController.php` dan perbaikan link di `views/laporan/index.php`.
- Pencarian ulang seluruh codebase (di luar `vendor/`) untuk caller `route('laporan', 'tandaTangan' ...)`, `route('laporan', 'generatePDFWithSignature' ...)`, `url('laporan/...')` hardcoded, dan referensi ke `WilayahController`/`LaporanOPDCetakController`/`laporan-opd-cetak` — semua bersih, tidak ada sisa.
- Pengecekan komentar (`//`, `#`, `/* */`) pada seluruh file yang diubah sesi ini — nihil, sesuai Zero Comments Policy.

### 6. Logout 404 — diperbaiki
Rute `logout` terdaftar `POST` only (`$router->post('logout', 'AuthController@logout')`), sudah benar secara REST karena logout adalah aksi state-changing (destroy session). Namun trigger-nya di dua tempat masih `<a href="...">` biasa (request GET), sehingga tidak pernah cocok dengan rute dan selalu 404:
- `views/layouts/admin-navbar.php` (dropdown menu admin/camat/opd)
- `views/layouts/simple-navbar.php` (navbar publik)

Diperbaiki dengan mengubah kedua trigger menjadi `<form method="POST" action="...">` berisi `<button type="submit">`, tampilan dipertahankan sama (menambahkan CSS reset kecil `.nav-menu button` di `simple-header.php` agar tombol logout terlihat identik dengan link nav lain). Rute `login`/`logout` di `routes.php` dan `AuthController` sendiri tidak diubah karena sudah benar — bug-nya murni di sisi pemicu (trigger) di view.

## Catatan / Follow-up Manual

- Endpoint `laporan/tanda-tangan/*` dan halaman "Cetak Laporan" butuh sesi login admin (`requireAdmin()`), sehingga pengujian fungsional end-to-end (filter, switch tab, generate PDF/Excel, klik TTD) perlu dilakukan manual via browser dengan akun admin yang valid; tidak diuji otomatis dalam sesi ini.
- Tidak ada perubahan pada logika bisnis inti maupun tampilan UI — perubahan terbatas pada pemetaan route, query SQL yang salah kolom, dan pembersihan dead code.

# Frontend Refactor — PRD-LB Frontend Refactor v1.0

Referensi: `PRD.html` (Frontend Refactor PRD).

Eksekusi end-to-end 9 fase sesuai §13 (Urutan Prioritas) dan §14 (Task Tracker) PRD. Seluruh perubahan bersifat frontend-only (HTML structure/CSS/UI-JS) — tidak ada perubahan pada `route()`, nama variabel PHP dari controller, atribut `name`/hidden `type`/`value`, atau logika bisnis. Verifikasi tiap file dengan `php -l`.

## 1. `assets/css/design-tokens.css` — dibuat
File baru, satu `:root` sumber kebenaran berisi token baru (`--clr-*`, `--surface-*`, `--text-*`, `--radius-*`, `--shadow-*-token`, `--z-*`) sekaligus alias nama variabel lama (`--primary-navy`, `--primary-blue`, `--primary-black`, dst.) yang nilainya disatukan supaya markup existing tidak perlu diubah. Dimuat paling awal di `<head>` `views/layouts/simple-header.php` dan `views/layouts/admin-header.php`, sebelum `custom-modern.css`. Blok `:root` duplikat dihapus dari `custom-modern.css` dan `simple-header.php`.

Untuk empat file auth (`views/auth/{index,admin,camat,opd}.php`) yang standalone (tidak include layout manapun), link `design-tokens.css` ditambahkan langsung ke masing-masing, dan blok `:root` lokalnya **dipertahankan** (bukan dihapus) tapi nilainya diselaraskan ke palet token (lihat bagian Warna di bawah) — keputusan ini diambil supaya halaman tidak sempat tampil rusak di antara fase, sekaligus tetap menyisakan sedikit aksen warna per-role yang berguna secara UX.

## 2. Font — unifikasi ke Plus Jakarta Sans
Semua Google Fonts link (Poppins+Inter di `simple-header.php`, Poppins di `landing/index.php` dan `auth/{admin,camat,opd}.php`, tidak ada sama sekali di `auth/index.php` dan `admin-header.php`) diganti/ditambahkan menjadi Plus Jakarta Sans. `body { font-family }` diset sekali di `design-tokens.css` dan `custom-modern.css`; deklarasi `font-family` per-file (termasuk `Segoe UI` di `auth/index.php`) dihapus.

## 3. Warna & gradient — unifikasi
- `auth/index.php`: gradient AI ungu `#667eea→#764ba2` (2 lokasi) diganti `linear-gradient(135deg, var(--surface-dark), var(--surface-dark-2))`; `:root` lokal diselaraskan ke `#1e3a8a`/`#1d4ed8`/`#dc2626`/`#16a34a`/`#ca8a04`.
- `auth/{admin,camat,opd}.php`: gradient `.branding-section` dan `.btn-login` (masing-masing 2x per file) diganti `linear-gradient(135deg, var(--surface-dark), var(--primary-{role}))` — base gelap konsisten, aksen warna per-role dipertahankan tipis di ujung gradient.
- `landing/index.php`: `--primary-black` diganti dari `#000000` (pure black, temuan MED) ke `#0f172a`; background gradient mengikuti.
- `views/dashboard/admin/index.php`: 4 kartu statistik (`bg-primary/warning/info/success`) dan 2 kartu inline (`#6f42c1`, `#e83e8c` dengan `!important`) diganti kelas semantik `.stat-icon-{primary,warning,info,success,purple,pink}` (tinted bg + colored icon, sesuai contoh before/after PRD §12.3) di `custom-modern.css`. Warna line chart Chart.js disamakan ke `#1d4ed8`.

## 4. Hover/active states
`custom-modern.css` ditambah: `.sidebar .nav-item .nav-link` hover/active (border-left accent + tinted bg), `.btn:active{transform:scale(.98)}`. `views/layouts/admin-sidebar.php`: 6 item nav level-atas (Dashboard, Data Pelapor, Laporan OPD, Laporan Camat, Cetak Laporan, Kirim Pesan) sebelumnya tidak pernah mendapat class `active` — ditambahkan `route_starts_with()` per item (pola yang sama yang sudah dipakai submenu Pengaturan), memetakan ke prefix rute asli di `routes.php` (`admin/dashboard`, `data-pelapor`, `admin/laporan-opd`, `admin/laporan-camat`, `laporan`, `wa-messages`).

## 5. Landing & auth layout
- `landing/index.php`: 3 kartu login (`onclick="location.href=..."`) diganti `<a href="...">` asli dengan target route() yang identik; `<button>` dalam kartu diganti `<span>` (tidak ada `type=submit`/form terkait, aman). Grid diubah asimetris (`1.3fr 1fr 1fr`), kartu Admin mendapat padding lebih besar sebagai penekanan visual.
- Favicon (`assets/images/favicon.png`) ditambahkan ke `simple-header.php`, `landing/index.php`, dan keempat file `auth/*.php` (sebelumnya hanya admin-header.php yang punya).

## 6. Icon library & inline-style cleanup
- `views/layouts/admin-header.php`: 4 dari 5 icon library dihapus (Feather, Themify, Typicons, Simple Line Icons) — disisakan MDI + Font Awesome. Sebelum dihapus, 3 usage nyata ditemukan & diganti ke MDI: `admin-setting-panel.php` (`ti-settings`→`mdi-cog`, `ti-close`→`mdi-close`), `admin-navbar.php` (`icon-menu` milik simple-line-icons → `mdi-menu`). `lang="en"` diperbaiki ke `lang="id"`.
- Blok `<style>` duplikat di `laporan-admin-{opd,camat}/{index,detail,edit}.php` (6 file, identik berpasangan) dipindah ke `custom-modern.css` sekali (`.avatar-sm`, `.avatar-lg`, `.avatar-xs`, `.avatar-title`, `.form-check-card*`, `.btn-group-sm .btn`, `.breadcrumb*`, `.alert`) — aturan yang ternyata sudah redundan dengan rule global existing (`.badge`, `.card`, `.table th`, `.text-success/warning/danger` override) tidak dipindah (dibuang sebagai no-op).
- `views/dashboard/admin/index.php`: `mr-3` (Bootstrap 4) → `me-3` di 6 kartu statistik.

## 7. `assets/js/ui-helpers.js` — dibuat, 26 alert()/confirm() diganti
Fungsi: `showToast(message, type)` (toast top-right, auto-injected container, pola sama dengan `#toastContainer` yang sudah ada di `opd/`, `profile/`), `showConfirm(message, onConfirm, options)` (modal Bootstrap5 dibuat dinamis via JS, callback-based), `showFieldError(fieldId, message)` / `clearFieldError()` (pesan error inline `.invalid-feedback` di bawah field). Dimuat di `views/layouts/admin-script.php` dan `views/layouts/simple-footer.php` (setelah Bootstrap JS bundle).

Diganti di: `laporan-admin-{opd,camat}/edit.php` (5 alert + 2 confirm masing-masing — alert validasi → `showFieldError`/`showToast`, confirm submit/escape → `showConfirm` dengan `editForm.submit()` di callback agar tidak memicu ulang listener), `laporan-admin-{opd,camat}/{index,detail}.php` (`onsubmit="return confirm(...)"` pada form hapus → `onsubmit="showConfirm(...); return false;"`), `wilayah/{index,index-kecamatan,index-desa}.php` (3 fungsi delete dengan confirm dinamis termasuk pesan multi-baris, `\n` diganti `<br>` karena modal merender HTML), `data-pelapor/index.php` (1 confirm + 2 alert pada delete AJAX), `laporan/tanda-tangan.php` (1 confirm reset form). Diverifikasi tidak ada `alert(`/`confirm(` tersisa di `views/` di luar `ui-helpers.js`.

## 8. Breadcrumb & empty state
Breadcrumb Bootstrap5 ditambahkan ke `laporan-admin-{opd,camat}/{index,detail,edit}.php` (6 file), dibangun dari `route()` yang sudah ada (tidak ada data baru dari controller). Empty-state tabel laporan (`laporan-admin-{opd,camat}/index.php`) diredesain: ikon dalam lingkaran tinted (`.empty-state-icon`), heading, deskripsi, dan CTA "Hapus filter" ke `route()` index masing-masing.

## 9. Halaman 404 & meta tags
`views/errors/404.php` dirombak total: dari halaman standalone minimal (Segoe UI, kotak putih polos) menjadi memakai `simple-header.php`/`simple-footer.php` (mewarisi font/warna terpusat), nomor "404" besar bergradient, ikon, CTA kembali. Variabel `$message` dari `Router::dispatch()` (satu-satunya caller) dipertahankan exact sama. Meta `description`/`og:title`/`og:description`/`og:image` ditambahkan ke `simple-header.php` dan `admin-header.php`.

## Verifikasi
- `php -l` terhadap seluruh file `.php` di `views/`, `core/`, `controllers/` — nihil syntax error.
- Grep ulang: tidak ada `alert(`/`confirm(` tersisa di `views/`, tidak ada link Feather/Themify/Typicons/Simple-Line tersisa, tidak ada `mr-3`/`ml-3`/`pr-3`/`pl-3` tersisa.
- Diff per-file disortir untuk memastikan tidak ada `route()` yang diubah targetnya, tidak ada atribut `name=`/hidden `type=`/`value=` yang terhapus atau berubah, tidak ada logika PHP controller yang tersentuh.
- Tidak dijalankan di browser sungguhan dalam sesi ini (tidak ada sesi browser tersedia) — rekomendasi pengecekan visual manual sebelum sign-off: keempat halaman auth (gradient & font baru), landing (kartu asimetris jadi `<a>`), dashboard admin (stat icon semantik), dua form edit laporan (showFieldError/showConfirm flow end-to-end termasuk submit sungguhan), halaman 404 (akses URL tidak valid).
