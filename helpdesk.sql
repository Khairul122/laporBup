-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 28, 2025 at 09:11 PM
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
(1, 2, 'camat1', 'MUARA BARU', 'Penyabungan', '2025-10-28 23:26:00', 'bupati', 'HAHHAHAHAHHHA', 'uploads/laporan_camat/6900eed5848f2.png', 'baru', '2025-10-28 16:27:01', '2025-10-28 20:59:29');

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
(3, 'opd1@gmail.com', 'opd1', '$2y$10$JtkzsYif.dp2Wy7xP6QaHu86TIU0wHH.zHwdThO0sOHe1RVd7eADC', 'OPD 1', 'opd', '2025-10-28 18:01:42', '2025-10-28 18:01:42');

--
-- Indexes for dumped tables
--

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
-- AUTO_INCREMENT for table `laporan_camat`
--
ALTER TABLE `laporan_camat`
  MODIFY `id_laporan_camat` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `laporan_opd`
--
ALTER TABLE `laporan_opd`
  MODIFY `id_laporan_opd` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

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
