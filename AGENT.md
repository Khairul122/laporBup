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

## Catatan / Follow-up Manual

- Endpoint `laporan/tanda-tangan/*` dan halaman "Cetak Laporan" butuh sesi login admin (`requireAdmin()`), sehingga pengujian fungsional end-to-end (filter, switch tab, generate PDF/Excel, klik TTD) perlu dilakukan manual via browser dengan akun admin yang valid; tidak diuji otomatis dalam sesi ini.
- Tidak ada perubahan pada logika bisnis inti maupun tampilan UI — perubahan terbatas pada pemetaan route, query SQL yang salah kolom, dan pembersihan dead code.
