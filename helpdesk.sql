-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 30, 2025 at 05:02 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `helpdesk`
--

-- --------------------------------------------------------

--
-- Table structure for table `desa`
--

CREATE TABLE `desa` (
  `id_desa` int NOT NULL,
  `id_kecamatan` int NOT NULL,
  `nama_desa` varchar(100) COLLATE utf8mb3_swedish_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_swedish_ci;

--
-- Dumping data for table `desa`
--

INSERT INTO `desa` (`id_desa`, `id_kecamatan`, `nama_desa`, `created_at`, `updated_at`) VALUES
(9, 20, 'Dalan Lidang', '2025-10-29 23:47:16', '2025-10-29 23:58:41'),
(10, 24, 'Sidojadi', '2025-10-29 23:58:25', '2025-10-29 23:58:25'),
(11, 20, 'Sipolu-Polu', '2025-10-29 23:59:26', '2025-10-29 23:59:26'),
(12, 20, 'Pidoli Lombang', '2025-10-29 23:59:40', '2025-10-29 23:59:40'),
(13, 20, 'Pidoli Dolok', '2025-10-29 23:59:50', '2025-10-29 23:59:50'),
(14, 20, 'Kayu Jati', '2025-10-30 00:00:03', '2025-10-30 00:00:31'),
(15, 20, 'Aek Mata', '2025-10-30 00:02:06', '2025-10-30 00:02:06'),
(16, 20, 'Kota Siantar', '2025-10-30 00:02:35', '2025-10-30 00:02:35'),
(17, 20, 'Ipar Bondar', '2025-10-30 00:02:58', '2025-10-30 00:02:58'),
(18, 20, 'Parbangunan', '2025-10-30 00:03:20', '2025-10-30 00:03:20');

-- --------------------------------------------------------

--
-- Table structure for table `kecamatan`
--

CREATE TABLE `kecamatan` (
  `id_kecamatan` int NOT NULL,
  `nama_kecamatan` varchar(100) COLLATE utf8mb3_swedish_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_swedish_ci;

--
-- Dumping data for table `kecamatan`
--

INSERT INTO `kecamatan` (`id_kecamatan`, `nama_kecamatan`, `created_at`, `updated_at`) VALUES
(20, 'Panyabungan', '2025-10-29 14:27:45', '2025-10-29 23:48:39'),
(21, 'Panyabungan Timur', '2025-10-29 23:46:34', '2025-10-29 23:49:01'),
(22, 'Panyabungan Barat', '2025-10-29 23:46:43', '2025-10-29 23:47:53'),
(23, 'Panyabungan Utara', '2025-10-29 23:49:23', '2025-10-29 23:49:23'),
(24, 'Bukit Malintang', '2025-10-29 23:49:57', '2025-10-29 23:49:57'),
(25, 'Siabu', '2025-10-29 23:50:15', '2025-10-29 23:50:15'),
(26, 'Naga Juang', '2025-10-29 23:50:35', '2025-10-29 23:50:35'),
(27, 'Hutabargot', '2025-10-29 23:51:10', '2025-10-29 23:51:10'),
(28, 'Lembah Sorik Marapi', '2025-10-29 23:51:30', '2025-10-29 23:51:30'),
(29, 'Puncak Sorik Marapi', '2025-10-29 23:51:46', '2025-10-29 23:51:46'),
(30, 'Panyabungan Selatan', '2025-10-29 23:52:02', '2025-10-29 23:52:02'),
(31, 'Kotanopan', '2025-10-29 23:52:14', '2025-10-29 23:52:14'),
(32, 'Natal', '2025-10-29 23:53:03', '2025-10-29 23:53:03'),
(33, 'Sinunukan', '2025-10-29 23:53:15', '2025-10-29 23:53:15'),
(34, 'Muara Batang Gadis', '2025-10-29 23:53:30', '2025-10-29 23:53:30'),
(35, 'Lingga Bayu', '2025-10-29 23:53:50', '2025-10-29 23:53:50'),
(36, 'Ranto Baek', '2025-10-29 23:54:08', '2025-10-29 23:54:08'),
(37, 'Batang Natal', '2025-10-29 23:54:24', '2025-10-29 23:54:24'),
(38, 'Muara Sipongi', '2025-10-29 23:54:55', '2025-10-29 23:54:55'),
(39, 'Pakantan', '2025-10-29 23:55:11', '2025-10-29 23:55:11'),
(40, 'Batahan', '2025-10-29 23:56:12', '2025-10-29 23:56:12'),
(41, 'Tambangan', '2025-10-29 23:56:42', '2025-10-29 23:56:42'),
(42, 'Ulu Pungkut', '2025-10-29 23:56:53', '2025-10-29 23:56:53');

-- --------------------------------------------------------

--
-- Table structure for table `laporan_camat`
--

CREATE TABLE `laporan_camat` (
  `id_laporan_camat` int NOT NULL,
  `id_user` int NOT NULL,
  `nama_pelapor` varchar(100) COLLATE utf8mb3_swedish_ci NOT NULL,
  `nama_desa` varchar(100) COLLATE utf8mb3_swedish_ci NOT NULL,
  `nama_kecamatan` varchar(100) COLLATE utf8mb3_swedish_ci NOT NULL,
  `waktu_kejadian` datetime NOT NULL,
  `tujuan` enum('bupati','wakil bupati','sekda','opd') COLLATE utf8mb3_swedish_ci NOT NULL,
  `uraian_laporan` text COLLATE utf8mb3_swedish_ci NOT NULL,
  `upload_file` varchar(255) COLLATE utf8mb3_swedish_ci DEFAULT NULL,
  `status_laporan` enum('baru','diproses','selesai') COLLATE utf8mb3_swedish_ci DEFAULT 'baru',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_swedish_ci;

--
-- Dumping data for table `laporan_camat`
--

INSERT INTO `laporan_camat` (`id_laporan_camat`, `id_user`, `nama_pelapor`, `nama_desa`, `nama_kecamatan`, `waktu_kejadian`, `tujuan`, `uraian_laporan`, `upload_file`, `status_laporan`, `created_at`, `updated_at`) VALUES
(1, 2, 'camat1', 'MUARA BARU', 'Penyabungan', '2025-10-28 23:26:00', 'bupati', 'HAHHAHAHAHHHA', 'uploads/laporan_camat/6900eed5848f2.png', 'baru', '2025-10-28 16:27:01', '2025-10-28 20:59:29'),
(2, 4, 'camatpakantan', 'Desa Huta Dolok', 'Kecamatan Pakantan', '2025-10-29 11:01:00', 'bupati', 'Kebakarannn tolonggg', 'uploads/laporan_camat/6901920a3415b.png', 'selesai', '2025-10-29 04:03:22', '2025-10-29 08:44:45'),
(3, 4, 'camatpakantan', 'Dalan Lidang', 'Panyabungan', '2025-10-30 04:10:00', 'bupati', 'Terjadi Kebakaran', 'uploads/laporan_camat/6902e57826c50.png', 'baru', '2025-10-30 04:11:36', '2025-10-30 04:11:36');

-- --------------------------------------------------------

--
-- Table structure for table `laporan_opd`
--

CREATE TABLE `laporan_opd` (
  `id_laporan_opd` int NOT NULL,
  `id_user` int NOT NULL,
  `nama_opd` varchar(150) COLLATE utf8mb3_swedish_ci NOT NULL,
  `nama_kegiatan` varchar(150) COLLATE utf8mb3_swedish_ci NOT NULL,
  `uraian_laporan` text COLLATE utf8mb3_swedish_ci NOT NULL,
  `tujuan` enum('dinas kominfo') COLLATE utf8mb3_swedish_ci DEFAULT 'dinas kominfo',
  `upload_file` varchar(255) COLLATE utf8mb3_swedish_ci DEFAULT NULL,
  `status_laporan` enum('baru','diproses','selesai') COLLATE utf8mb3_swedish_ci DEFAULT 'baru',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_swedish_ci;

--
-- Dumping data for table `laporan_opd`
--

INSERT INTO `laporan_opd` (`id_laporan_opd`, `id_user`, `nama_opd`, `nama_kegiatan`, `uraian_laporan`, `tujuan`, `upload_file`, `status_laporan`, `created_at`, `updated_at`) VALUES
(1, 3, 'OPD1', 'HIHIHIIHIHIHIHIH', 'HIHIHIIHIHIHIHIHHIHIHIIHIHIHIHIHHIHIHIIHIHIHIHIH', 'dinas kominfo', 'uploads/laporan_opd/laporan_opd_1761678045_690112dd325e8.mp4', 'baru', '2025-10-28 19:00:45', '2025-10-28 19:00:45');

-- --------------------------------------------------------

--
-- Table structure for table `opd`
--

CREATE TABLE `opd` (
  `id_opd` int NOT NULL,
  `nama_opd` varchar(150) COLLATE utf8mb3_swedish_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_swedish_ci;

--
-- Dumping data for table `opd`
--

INSERT INTO `opd` (`id_opd`, `nama_opd`, `created_at`, `updated_at`) VALUES
(3, 'DINAS KOMUNIKASI DAN INFORMATIKA', '2025-10-30 01:16:57', '2025-10-30 03:43:52'),
(4, 'SEKRETARIAT DAERAH KABUPATEN', '2025-10-30 03:43:11', '2025-10-30 03:43:11'),
(5, 'DINAS PENDIDIKAN DAN KEBUDAYAAN', '2025-10-30 03:46:44', '2025-10-30 03:46:44'),
(6, 'DINAS KESEHATAN', '2025-10-30 03:47:15', '2025-10-30 03:47:15'),
(7, 'DINAS PEKERJAAN UMUM DAN PENATAAN RUANG', '2025-10-30 03:47:31', '2025-10-30 03:47:31'),
(8, 'DINAS PERUMAHAN RAKYAT DAN KAWASAN PERMUKIMAN SERTA PERTANAHAN', '2025-10-30 03:48:26', '2025-10-30 03:48:26'),
(9, 'SATUAN POLISI PAMONG PRAJA DAN PEMADAM KEBAKARAN', '2025-10-30 03:48:49', '2025-10-30 03:48:49'),
(10, 'DINAS SOSIAL, PEMBERDAYAAN PEREMPUAN DAN PERLIDUNGAN ANAK', '2025-10-30 03:49:05', '2025-10-30 03:49:05'),
(11, 'DINAS KOPERASI, USAHA KECIL DAN MENENGAH', '2025-10-30 03:49:18', '2025-10-30 03:49:18'),
(12, 'DINAS TENAGA KERJA', '2025-10-30 03:49:29', '2025-10-30 03:49:29'),
(13, 'DINAS PENGENDALIAN PENDUDUK DAN KELUARGA BERENCANA', '2025-10-30 03:49:40', '2025-10-30 03:49:40'),
(14, 'DINAS KETAHANAN PANGAN', '2025-10-30 03:49:53', '2025-10-30 03:49:53'),
(15, 'DINAS PERTANIAN', '2025-10-30 03:50:04', '2025-10-30 03:50:04'),
(16, 'DINAS LINGKUNGAN HIDUP', '2025-10-30 03:50:15', '2025-10-30 03:50:15'),
(17, 'DINAS KEPENDUDUKAN DAN PENCATATAN SIPIL', '2025-10-30 03:50:24', '2025-10-30 03:50:24'),
(18, 'DINAS PEMBERDAYAAN MASYARAKAT DAN DESA', '2025-10-30 03:50:38', '2025-10-30 03:50:38'),
(19, 'DINAS PERHUBUNGAN', '2025-10-30 03:50:48', '2025-10-30 03:50:48'),
(20, 'DINAS PERPUSTAKAAN DAN KEARSIPAN', '2025-10-30 03:51:00', '2025-10-30 03:51:00'),
(21, 'DINAS PENANAMAN MODAL DAN PELAYANAN TERPADU SATU PINTU', '2025-10-30 03:51:25', '2025-10-30 03:51:25'),
(22, 'DINAS PEMUDA DAN OLAHRAGA', '2025-10-30 03:51:47', '2025-10-30 03:51:47'),
(23, 'DINAS PERIKANAN', '2025-10-30 03:52:12', '2025-10-30 03:52:12'),
(24, 'DINAS PARIWISATA', '2025-10-30 03:52:24', '2025-10-30 03:52:24'),
(25, 'DINAS PERDAGANGAN', '2025-10-30 03:52:34', '2025-10-30 03:52:34'),
(26, 'BADAN PERENCANAAN PEMBANGUNAN, RISET DAN INOVASI DAERAH', '2025-10-30 03:52:47', '2025-10-30 03:52:47'),
(27, 'BADAN PENGELOLAAN KEUANGAN DAN ASET DAERAH', '2025-10-30 03:52:56', '2025-10-30 03:52:56'),
(28, 'BADAN PENDAPATAN DAERAH', '2025-10-30 03:53:12', '2025-10-30 03:53:12'),
(29, 'BADAN KEPEGAWAIAN DAN PENGEMBANGAN SUMBER DAYA MANUSIA', '2025-10-30 03:53:23', '2025-10-30 03:53:23'),
(30, 'BADAN PENANGGULANGAN BENCANA DAERAH', '2025-10-30 03:53:34', '2025-10-30 03:53:34'),
(31, 'BADAN KESATUAN BANGSA DAN POLITIK', '2025-10-30 03:53:45', '2025-10-30 03:53:45'),
(32, 'INSPEKTORAT DAERAH KABUPATEN', '2025-10-30 03:56:14', '2025-10-30 03:56:14');

-- --------------------------------------------------------

--
-- Table structure for table `profile`
--

CREATE TABLE `profile` (
  `id_profile` int NOT NULL,
  `nama_aplikasi` varchar(150) COLLATE utf8mb3_swedish_ci NOT NULL,
  `logo` varchar(255) COLLATE utf8mb3_swedish_ci DEFAULT NULL,
  `role` enum('camat','opd') COLLATE utf8mb3_swedish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_swedish_ci;

--
-- Dumping data for table `profile`
--

INSERT INTO `profile` (`id_profile`, `nama_aplikasi`, `logo`, `role`) VALUES
(1, 'Madina Maju Madani', 'uploads/profile_logos/profile_logo_6902cbd9e0b61.png', 'opd');

-- --------------------------------------------------------

--
-- Table structure for table `ttd_laporan`
--

CREATE TABLE `ttd_laporan` (
  `id_ttd_laporan` int NOT NULL,
  `pangkat` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_swedish_ci NOT NULL,
  `nama_penanda_tangan` varchar(100) COLLATE utf8mb3_swedish_ci NOT NULL,
  `jabatan_penanda_tangan` varchar(100) COLLATE utf8mb3_swedish_ci NOT NULL,
  `nip` varchar(50) COLLATE utf8mb3_swedish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_swedish_ci;

--
-- Dumping data for table `ttd_laporan`
--

INSERT INTO `ttd_laporan` (`id_ttd_laporan`, `pangkat`, `nama_penanda_tangan`, `jabatan_penanda_tangan`, `nip`) VALUES
(1, '', 'RAHMAD HIDAYAT, S.Pd', 'Plt. KEPALA DINAS KOMUNIKASI DAN INFORMATIKA', '19730417 199903 1 003');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int NOT NULL,
  `email` varchar(100) COLLATE utf8mb3_swedish_ci NOT NULL,
  `username` varchar(50) COLLATE utf8mb3_swedish_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb3_swedish_ci NOT NULL,
  `jabatan` varchar(100) COLLATE utf8mb3_swedish_ci DEFAULT NULL,
  `role` enum('admin','opd','camat') COLLATE utf8mb3_swedish_ci NOT NULL DEFAULT 'opd',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `email`, `username`, `password`, `jabatan`, `role`, `created_at`, `updated_at`) VALUES
(1, 'admin@gmail.com', 'admin', '$2y$10$2yPltRwQ5qHGJ7/N70d9U.3qdB94j10RTSwc1mExuC.5poJA2zJj2', 'Administrator', 'admin', '2025-10-28 11:33:09', '2025-10-28 11:33:09'),
(2, 'camat1@gmail.com', 'camat1', '$2y$10$wnu8WcZf818tbfLiG8wv.uh1VExNx34MReu1T.pbsnfOIXKvonbh.', 'Camat', 'camat', '2025-10-28 14:31:58', '2025-10-28 14:31:58'),
(3, 'opd1@gmail.com', 'opd1', '$2y$10$JtkzsYif.dp2Wy7xP6QaHu86TIU0wHH.zHwdThO0sOHe1RVd7eADC', 'OPD 1', 'opd', '2025-10-28 18:01:42', '2025-10-28 18:01:42'),
(4, 'camatpakantan@gmail.com', 'camatpakantan', '$2y$10$Hd6zzRMruI.89mJUE9EV2empl3h2yUXdsWwpwgvw043hmdIIHS8GS', 'Camat Pakantan', 'camat', '2025-10-29 03:59:41', '2025-10-29 03:59:41'),
(5, 'diskominfo@gmail.com', 'diskominfo1', '$2y$10$Dj1so4EHU7Lg3GMWy5SgUeDyslV3rpNfSX43a3v5rIukf0jR/rwS6', 'Dinas Komunikasi Dan Informatika', 'opd', '2025-10-29 08:25:31', '2025-10-29 08:25:31');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `desa`
--
ALTER TABLE `desa`
  ADD PRIMARY KEY (`id_desa`),
  ADD KEY `fk_desa_kecamatan` (`id_kecamatan`);

--
-- Indexes for table `kecamatan`
--
ALTER TABLE `kecamatan`
  ADD PRIMARY KEY (`id_kecamatan`);

--
-- Indexes for table `laporan_camat`
--
ALTER TABLE `laporan_camat`
  ADD PRIMARY KEY (`id_laporan_camat`),
  ADD KEY `fk_laporan_camat_user` (`id_user`);

--
-- Indexes for table `laporan_opd`
--
ALTER TABLE `laporan_opd`
  ADD PRIMARY KEY (`id_laporan_opd`),
  ADD KEY `fk_laporan_opd_user` (`id_user`);

--
-- Indexes for table `opd`
--
ALTER TABLE `opd`
  ADD PRIMARY KEY (`id_opd`);

--
-- Indexes for table `profile`
--
ALTER TABLE `profile`
  ADD PRIMARY KEY (`id_profile`);

--
-- Indexes for table `ttd_laporan`
--
ALTER TABLE `ttd_laporan`
  ADD PRIMARY KEY (`id_ttd_laporan`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `desa`
--
ALTER TABLE `desa`
  MODIFY `id_desa` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `kecamatan`
--
ALTER TABLE `kecamatan`
  MODIFY `id_kecamatan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `laporan_camat`
--
ALTER TABLE `laporan_camat`
  MODIFY `id_laporan_camat` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `laporan_opd`
--
ALTER TABLE `laporan_opd`
  MODIFY `id_laporan_opd` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `opd`
--
ALTER TABLE `opd`
  MODIFY `id_opd` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `profile`
--
ALTER TABLE `profile`
  MODIFY `id_profile` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ttd_laporan`
--
ALTER TABLE `ttd_laporan`
  MODIFY `id_ttd_laporan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `desa`
--
ALTER TABLE `desa`
  ADD CONSTRAINT `fk_desa_kecamatan` FOREIGN KEY (`id_kecamatan`) REFERENCES `kecamatan` (`id_kecamatan`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `laporan_camat`
--
ALTER TABLE `laporan_camat`
  ADD CONSTRAINT `fk_laporan_camat_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `laporan_opd`
--
ALTER TABLE `laporan_opd`
  ADD CONSTRAINT `fk_laporan_opd_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
