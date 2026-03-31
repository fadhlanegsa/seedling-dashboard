-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 02 Feb 2026 pada 19.58
-- Versi server: 10.11.15-MariaDB-cll-lve
-- Versi PHP: 8.4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `wast6986_seedling_dashboard1`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `bpdas`
--

CREATE TABLE `bpdas` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `province_id` int(11) NOT NULL,
  `address` text NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `contact_person` varchar(100) DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `bpdas`
--

INSERT INTO `bpdas` (`id`, `name`, `province_id`, `address`, `phone`, `email`, `contact_person`, `latitude`, `longitude`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Krueng Aceh', 1, 'Ds. Lam Ara Tunong, Kuta Malaka, Aceh Besar, Aceh', '081377280080', 'dit.ppth@kehutanan.go.id', 'Eko Wasiaji', 0.00000000, 0.00000000, 1, '2025-12-18 08:15:35', '2025-12-18 08:15:35'),
(2, 'Asahan Barumun', 2, 'Ds. Motung, Ajibata, Toba, Sumatera Utara', '081361596207', 'dit.ppth@kehutanan.go.id', 'Asri Situmorang', 0.00000000, 0.00000000, 1, '2025-12-18 09:21:10', '2025-12-18 09:21:10'),
(3, 'Batanghari', 5, 'Kel. Sialang Munggu, Tampan, Pekan Baru, Riau', '081366192171', 'dit.ppth@kehutanan.go.id', 'Waskadi', 0.00000000, 0.00000000, 1, '2025-12-18 09:39:05', '2025-12-23 08:18:36'),
(4, 'Citarum Ciliwung', 12, 'DS. Rumpin, Rumpin, Bogor, Jawa Barat', '085780207180', 'dit.ppth@kehutanan.go.id', 'Enjen Jaenal', 0.00000000, 0.00000000, 1, '2025-12-18 09:49:03', '2025-12-18 09:49:03'),
(5, 'Pemali Jratun', 13, 'Ds. Bangsri, Bangsri, Jepara, Jawa Tengah &amp;amp; Ds. Bergas lor, Bergas, Semarang, Jawa Tengah', '085282467706', 'dit.ppth@kehutanan.go.id', 'Yulia', 0.00000000, 0.00000000, 1, '2025-12-18 09:52:55', '2026-01-30 08:05:51'),
(6, 'Brantas Sampean', 15, 'Ds. Kemlagi, Kemlagi, Mojokerto, Jawa Timur', '081357278228', 'dit.ppth@kehutanan.go.id', 'Syafii', 0.00000000, 0.00000000, 1, '2025-12-18 10:00:17', '2025-12-18 10:00:17'),
(7, 'Kapuas', 20, 'Kel. Siantan Hilir, Pontianak Utara, Kota Pontianak, Kalimantan Barat', '081247421632', 'dit.ppth@kehutanan.go.id', 'Yolanda', 0.00000000, 0.00000000, 1, '2025-12-18 10:06:28', '2025-12-18 10:06:28'),
(8, 'Mahakam Berau', 23, 'Ds. Mentawir, Sepaku, Penajam Paser Utara, Kalimantan Timur', '085817269445', 'dit.ppth@kehutanan.go.id', 'Jhen', 0.00000000, 0.00000000, 1, '2025-12-18 10:12:08', '2025-12-18 10:12:08'),
(9, 'Jeneberang Sadang', 27, 'Ds. Marinding, Mengkedek, Tana Toraja, Sulawesi Selatan', '081355527772', 'dit.ppth@kehutanan.go.id', 'Edi Kurniawan', 0.00000000, 0.00000000, 1, '2025-12-18 10:34:55', '2025-12-18 10:34:55'),
(10, 'Membramo', 33, 'Kel. Vim, Jayapura Selatan, Kota Jayapura, Papua', '081248147351', 'dit.ppth@kehutanan.go.id', 'Jumadi', 0.00000000, 0.00000000, 1, '2025-12-18 10:38:46', '2025-12-18 10:38:46'),
(11, 'Wampu Sei Ular', 2, 'Ds. Kebun Lada, Binjai Utara, Binjai, Sumatera Utara', '085275179373', 'dit.ppth@kehutanan.go.id', 'Anggi Siregar', 0.00000000, 0.00000000, 1, '2025-12-18 10:44:25', '2025-12-18 10:44:25'),
(12, 'Agam Kuantan', 3, 'Ds. Pintu Kabun, Mandiangin Koto Selayan, Kota Bukittinggi, Sumatera Barat', '085263904206', 'dit.ppth@kehutanan.go.id', 'Slamet Riadi', 0.00000000, 0.00000000, 1, '2025-12-18 10:48:41', '2025-12-18 10:48:41'),
(13, 'Sei Jang Duriangkang', 10, 'Ds. Senggarang, Tanjung Pinang Timur, Tanjung Pinang, Kep. Riau', '082284099338', 'dit.ppth@kehutanan.go.id', 'Aswan', 0.00000000, 0.00000000, 1, '2025-12-18 10:53:49', '2025-12-18 10:53:49'),
(14, 'Indragiri Rokan', 4, 'Kel. Sialang Munggu, Tampan, Pekan Baru, Riau', '081268371974', 'dit.ppth@kehutanan.go.id', 'Johnson', 0.00000000, 0.00000000, 1, '2025-12-18 10:58:31', '2025-12-18 10:58:31'),
(15, 'Musi', 6, 'JL. Kol. H. Burlian Km 6, 5, Punti Kayu, Palembang, Sukarami, Palembang City, South Sumatra 30961', '-', '', '-', 0.00000000, 0.00000000, 1, '2025-12-18 11:03:50', '2025-12-18 11:03:50'),
(16, 'Ketahun', 7, 'Ds. Padang Petron, Kaur Selatan, Kaur, Bengkulu', '081368237185', 'dit.ppth@kehutanan.go.id', 'Marsudi', 0.00000000, 0.00000000, 1, '2025-12-18 11:06:03', '2025-12-18 11:06:03'),
(17, 'Baturusa Cerucuk', 9, 'Ds. Teru, Simpang Katis, Bangka Tengah, Bangka Belitung', '081217181757', 'dit.ppth@kehutanan.go.id', 'Indra', 0.00000000, 0.00000000, 1, '2025-12-18 14:31:12', '2025-12-18 14:31:12'),
(18, 'Way Seputih Way Sekampung', 8, 'Ds. Kota Agung, Kota Agung Pusat, Tanggamus, Lampung', '082182775048', 'dit.ppth@kehutanan.go.id', 'Momo', 0.00000000, 0.00000000, 1, '2025-12-18 14:36:27', '2025-12-18 14:36:27'),
(19, 'Cimanuk Citanduy', 12, 'Ds. Sawala, Kadipaten, Majalengka, Jawa Barat', '081312499137', 'dit.ppth@kehutanan.go.id', 'Ida', 0.00000000, 0.00000000, 1, '2025-12-18 14:39:51', '2025-12-18 14:39:51'),
(20, 'Solo', 13, 'Ds. Sukosari, Jumantono, Karanganyar, Jawa Tengah', '081248149931', 'dit.ppth@kehutanan.go.id', 'Marjuki', 0.00000000, 0.00000000, 1, '2025-12-18 14:42:52', '2025-12-18 14:42:52'),
(21, 'Serayu Opak Progo', 14, 'Ds. Bunder, Pathuk, Gunung Kidul, D.I. Yogyakarta', '081391979028', 'dit.ppth@kehutanan.go.id', 'Taufik Rahmadi', 0.00000000, 0.00000000, 1, '2025-12-18 15:09:32', '2026-01-30 08:06:53'),
(22, 'Barito', 22, 'Ds. Gambut, Gambut, Banjar, Kalimantan Selatan', '081253482283', 'dit.ppth@kehutanan.go.id', 'Syahid', 0.00000000, 0.00000000, 1, '2025-12-22 07:11:13', '2025-12-22 07:11:13'),
(23, 'Kahayan', 21, 'Ds. Tumbang Nusa, Jabiren Raya, Pulang Pisau, Kalimantan Tengah', '081253155562', 'dit.ppth@kehutanan.go.id', 'Hendra', 0.00000000, 0.00000000, 1, '2025-12-22 07:23:19', '2025-12-22 07:23:19'),
(24, 'Unda Anyar', 17, 'Ds. Suwung, Denpasar Selatan, Kota Denpasar, Bali', '085337274871', 'dit.ppth@kehutanan.go.id', 'Ade', 0.00000000, 0.00000000, 1, '2025-12-22 07:33:37', '2025-12-22 07:33:37'),
(25, 'Dodokan Moyosari', 18, 'Ds. Pujut, Rembitan, Lombok Tengah, NTB', '085337087423', 'dit.ppth@kehutanan.go.id', 'Hafiz', 0.00000000, 0.00000000, 1, '2025-12-22 07:40:34', '2025-12-22 07:40:34'),
(26, 'Benain Noelmina', 19, 'Ds. Nggorang, Komodo, Manggarai Barat, NTT', '081339338574', 'dit.ppth@kehutanan.go.id', 'Piter', 0.00000000, 0.00000000, 1, '2025-12-22 07:50:29', '2025-12-22 07:50:29'),
(27, 'Konaweha', 28, 'Ds. Anduna, Laeya, Konawe Selatan, Sulawesi Tenggara', '081354665748', 'dit.ppth@kehutanan.go.id', 'Johanis', 0.00000000, 0.00000000, 1, '2025-12-22 08:00:38', '2025-12-22 08:00:38'),
(28, 'Karama', 30, 'Ds. Simboro, Simboro dan Kepulauan, Mamuju, Sulawesi Barat', '081342295920', 'dit.ppth@kehutanan.go.id', 'Arif Tangkelangi', 0.00000000, 0.00000000, 1, '2025-12-22 08:12:01', '2025-12-22 08:12:01'),
(29, 'Palu Poso', 26, 'Kel. Tondo, Palu Timur,  Kota Palu, Sulawesi Tengah', '082346307307', 'dit.ppth@kehutanan.go.id', 'Jajang Wahyudin', 0.00000000, 0.00000000, 1, '2025-12-22 08:25:37', '2025-12-22 08:25:37'),
(30, 'Bone Limboto', 29, 'Ds. Toyidito, Pulubala, Gorontalo, Gorontalo', '085256171771', 'dit.ppth@kehutanan.go.id', 'Wajir', 0.00000000, 0.00000000, 1, '2025-12-22 08:35:21', '2025-12-22 08:35:21'),
(31, 'Jeneberang Saddang', 27, 'Ds. Marinding, Mengkedek, Tana Toraja, Sulawesi Selatan', '081355527772', 'dit.ppth@kehutanan.go.id', 'Edi Kurniawan', 0.00000000, 0.00000000, 1, '2025-12-22 08:48:03', '2025-12-22 08:48:03'),
(32, 'Tondano', 25, 'Ds. Batuputih Bawah Likupang, Ranowulu, Bitung, Sulawesi Utara', '08114583473', 'dit.ppth@kehutanan.go.id', 'Arnold', 0.00000000, 0.00000000, 1, '2025-12-22 08:50:48', '2025-12-22 08:50:48'),
(33, 'Waehapu Batumerah', 31, 'Ds.Sanleko/ Marloso, Namlea, Buru, Maluku', '085243754501', 'dit.ppth@kehutanan.go.id', 'Andi Rusdi', 0.00000000, 0.00000000, 1, '2025-12-22 08:54:17', '2025-12-22 08:54:17'),
(34, 'Ake Malamo', 32, 'Ds. Balbar, Oba Utara, Tidore Kepulauan, Maluku Utara', '081224744996', 'dit.ppth@kehutanan.go.id', 'Yusril', 0.00000000, 0.00000000, 1, '2025-12-22 09:09:03', '2025-12-22 09:09:03'),
(35, 'Remu Ransiki', 34, 'Ds. Sowi, Manokwari Selatan, Manokwari, Papua Barat', '081219206907', 'dit.ppth34@kehutanan.go.id', 'Imelda', 0.00000000, 0.00000000, 1, '2025-12-22 09:12:28', '2025-12-22 09:12:28'),
(36, 'BPTH Wilayah I', 6, 'Jl. Kolonel H. Barlian No.km 6 5, Srijaya, Kec. Alang-Alang Lebar, Kota Palembang, Sumatera Selatan 309610813-7941-4225', '081379414225', 'dit.ppth@kehutanan.go.id', 'Lukmedi', 0.00000000, 0.00000000, 1, '2025-12-22 13:30:58', '2025-12-22 13:30:58'),
(37, 'BPTH Wilayah II', 27, 'Jl. Perintis Kemerdekaan No.KM 17, RW.5, Pai, Kec. Biringkanaya, Kota Makassar, Sulawesi Selatan 90242', '085298595018', 'dit.ppth@kehutanan.go.id', 'Rathna', 0.00000000, 0.00000000, 1, '2025-12-22 13:35:29', '2025-12-22 13:36:43'),
(38, 'BPTH Wilayah III', 14, 'Jl. Palagan Tentara Pelajar No.Km.15, Glondong, Purwobinangun, Kec. Pakem, Kabupaten Sleman, Daerah Istimewa Yogyakarta 55582', '087812822925', 'dit.ppth@kehutanan.go.id', 'Hamda', 0.00000000, 0.00000000, 1, '2025-12-22 13:46:56', '2026-01-30 08:04:57');

-- --------------------------------------------------------

--
-- Struktur dari tabel `provinces`
--

CREATE TABLE `provinces` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `code` varchar(10) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `provinces`
--

INSERT INTO `provinces` (`id`, `name`, `code`, `created_at`) VALUES
(1, 'Aceh', 'AC', '2025-12-18 08:09:41'),
(2, 'Sumatera Utara', 'SU', '2025-12-18 08:09:41'),
(3, 'Sumatera Barat', 'SB', '2025-12-18 08:09:41'),
(4, 'Riau', 'RI', '2025-12-18 08:09:41'),
(5, 'Jambi', 'JA', '2025-12-18 08:09:41'),
(6, 'Sumatera Selatan', 'SS', '2025-12-18 08:09:41'),
(7, 'Bengkulu', 'BE', '2025-12-18 08:09:41'),
(8, 'Lampung', 'LA', '2025-12-18 08:09:41'),
(9, 'Kepulauan Bangka Belitung', 'BB', '2025-12-18 08:09:41'),
(10, 'Kepulauan Riau', 'KR', '2025-12-18 08:09:41'),
(11, 'DKI Jakarta', 'JK', '2025-12-18 08:09:41'),
(12, 'Jawa Barat', 'JB', '2025-12-18 08:09:41'),
(13, 'Jawa Tengah', 'JT', '2025-12-18 08:09:41'),
(14, 'DI Yogyakarta', 'YO', '2025-12-18 08:09:41'),
(15, 'Jawa Timur', 'JI', '2025-12-18 08:09:41'),
(16, 'Banten', 'BT', '2025-12-18 08:09:41'),
(17, 'Bali', 'BA', '2025-12-18 08:09:41'),
(18, 'Nusa Tenggara Barat', 'NB', '2025-12-18 08:09:41'),
(19, 'Nusa Tenggara Timur', 'NT', '2025-12-18 08:09:41'),
(20, 'Kalimantan Barat', 'KB', '2025-12-18 08:09:41'),
(21, 'Kalimantan Tengah', 'KT', '2025-12-18 08:09:41'),
(22, 'Kalimantan Selatan', 'KS', '2025-12-18 08:09:41'),
(23, 'Kalimantan Timur', 'KI', '2025-12-18 08:09:41'),
(24, 'Kalimantan Utara', 'KU', '2025-12-18 08:09:41'),
(25, 'Sulawesi Utara', 'SA', '2025-12-18 08:09:41'),
(26, 'Sulawesi Tengah', 'ST', '2025-12-18 08:09:41'),
(27, 'Sulawesi Selatan', 'SN', '2025-12-18 08:09:41'),
(28, 'Sulawesi Tenggara', 'SG', '2025-12-18 08:09:41'),
(29, 'Gorontalo', 'GO', '2025-12-18 08:09:41'),
(30, 'Sulawesi Barat', 'SR', '2025-12-18 08:09:41'),
(31, 'Maluku', 'MA', '2025-12-18 08:09:41'),
(32, 'Maluku Utara', 'MU', '2025-12-18 08:09:41'),
(33, 'Papua', 'PA', '2025-12-18 08:09:41'),
(34, 'Papua Barat', 'PB', '2025-12-18 08:09:41');

-- --------------------------------------------------------

--
-- Struktur dari tabel `requests`
--

CREATE TABLE `requests` (
  `id` int(11) NOT NULL,
  `request_number` varchar(50) NOT NULL,
  `user_id` int(11) NOT NULL,
  `bpdas_id` int(11) NOT NULL,
  `seedling_type_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `purpose` text NOT NULL,
  `land_area` decimal(10,3) NOT NULL DEFAULT 0.000 COMMENT 'Land area in hectares, supports up to 3 decimal places',
  `latitude` decimal(10,8) DEFAULT NULL COMMENT 'Latitude koordinat lokasi tanam',
  `longitude` decimal(11,8) DEFAULT NULL COMMENT 'Longitude koordinat lokasi tanam',
  `status` enum('pending','approved','rejected','completed','delivered') DEFAULT 'pending',
  `approved_by` int(11) DEFAULT NULL,
  `approval_date` timestamp NULL DEFAULT NULL,
  `approval_notes` text DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `approval_letter_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `proposal_file_path` varchar(255) DEFAULT NULL COMMENT 'Path to uploaded proposal PDF for requests >25 seedlings',
  `delivery_photo_path` varchar(255) DEFAULT NULL COMMENT 'Path to delivery proof photo (WebP format)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `requests`
--

INSERT INTO `requests` (`id`, `request_number`, `user_id`, `bpdas_id`, `seedling_type_id`, `quantity`, `purpose`, `land_area`, `latitude`, `longitude`, `status`, `approved_by`, `approval_date`, `approval_notes`, `rejection_reason`, `approval_letter_path`, `created_at`, `updated_at`, `proposal_file_path`, `delivery_photo_path`) VALUES
(1, 'REQ-2025-12-0001', 18, 4, 5, 25, 'untuk penghijauan', 0.000, NULL, NULL, 'approved', 5, '2025-12-18 12:36:58', 'silakan ambil di Persemaian Rumpin', NULL, 'approval_REQ-2025-12-0001_1766061418.html', '2025-12-18 12:32:02', '2025-12-18 12:36:58', NULL, NULL),
(6, 'REQ-2025-12-0002', 18, 38, 25, 35, 'reboisasi', 0.000, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, '2025-12-22 15:42:30', '2025-12-22 15:42:30', NULL, NULL),
(8, 'REQ-2026-01-0001', 39, 37, 65, 8, 'untuk perkarangan', 0.250, NULL, NULL, 'approved', 37, '2026-01-27 09:24:06', 'jangan lupa geotag', NULL, 'approval_REQ-2026-01-0001_1769505846.pdf', '2026-01-27 04:01:31', '2026-01-27 09:24:06', NULL, NULL),
(9, 'REQ-2026-01-0002', 42, 4, 5, 100, 'Penghijauan', 0.010, NULL, NULL, 'rejected', 5, '2026-01-28 04:48:52', NULL, 'kurang lengkap tidak ada surat permohonana', NULL, '2026-01-27 05:37:40', '2026-01-28 04:48:52', NULL, NULL),
(10, 'REQ-2026-01-0003', 42, 4, 5, 100, 'Penghijauan', 0.010, NULL, NULL, 'rejected', 5, '2026-01-28 04:48:39', NULL, 'kurang lengkap tidak ada surat permohonana', NULL, '2026-01-27 05:37:52', '2026-01-28 04:48:39', NULL, NULL),
(11, 'REQ-2026-01-0004', 18, 22, 89, 5, 'penanaman perkarangan', 0.000, -6.20912362, 106.79885191, 'approved', 24, '2026-01-28 02:41:48', '', NULL, 'approval_REQ-2026-01-0004_1769568108.pdf', '2026-01-28 02:40:34', '2026-01-28 02:41:48', NULL, NULL),
(12, 'REQ-2026-01-0005', 43, 22, 139, 2, 'rumah', 0.000, -6.20698411, 106.79687158, 'approved', 24, '2026-01-28 02:45:37', '', NULL, 'approval_REQ-2026-01-0005_1769568337.pdf', '2026-01-28 02:44:59', '2026-01-28 02:45:37', NULL, NULL),
(13, 'REQ-2026-01-0006', 43, 1, 139, 4, 'penghijauan', 0.000, 4.58665361, 96.77367009, 'approved', 2, '2026-01-28 02:50:21', '', NULL, 'approval_REQ-2026-01-0006_1769568621.pdf', '2026-01-28 02:47:45', '2026-01-28 02:50:21', NULL, NULL),
(14, 'REQ-2026-01-0007', 43, 35, 42, 2, 'untuk perkarangan rumah', 0.000, -2.09378385, 133.49899501, 'approved', 35, '2026-01-28 03:35:20', '', NULL, 'approval_REQ-2026-01-0007_1769571320.pdf', '2026-01-28 03:33:37', '2026-01-28 03:35:20', NULL, NULL),
(15, 'REQ-2026-01-0008', 43, 21, 10, 5, 'menanam di rumah', 0.001, -7.49336201, 110.19540037, 'approved', 23, '2026-01-28 03:59:46', '', NULL, 'approval_REQ-2026-01-0008_1769572786.pdf', '2026-01-28 03:55:43', '2026-01-28 03:59:46', NULL, NULL),
(16, 'REQ-2026-01-0009', 43, 22, 44, 69, 'untuk penghijauan', 1.000, -0.83053537, 115.06260150, 'approved', 24, '2026-01-28 04:06:37', '', NULL, 'approval_REQ-2026-01-0009_1769573197.pdf', '2026-01-28 04:02:05', '2026-01-28 04:06:37', 'proposal_69798a3dd4a48_1769572925.pdf', NULL),
(17, 'REQ-2026-01-0010', 43, 33, 93, 4, 'penghijauan', 0.001, 0.20119141, 127.73280038, 'approved', 33, '2026-01-28 04:38:54', '', NULL, 'approval_REQ-2026-01-0010_1769575134.pdf', '2026-01-28 04:37:38', '2026-01-28 04:38:54', NULL, NULL),
(18, 'REQ-2026-01-0011', 43, 4, 5, 70, 'reboisasi', 1.000, -6.42314078, 106.81159767, 'rejected', 5, '2026-01-28 04:51:48', NULL, 'seriys 1000hektar?', NULL, '2026-01-28 04:50:00', '2026-01-28 04:51:48', 'proposal_69799578e646e_1769575800.pdf', NULL),
(19, 'REQ-2026-01-0012', 43, 4, 5, 70, 'reboisasi', 1.000, -6.62248717, 106.83182727, 'approved', 5, '2026-01-28 04:53:43', 'oke', NULL, 'approval_REQ-2026-01-0012_1769576023.pdf', '2026-01-28 04:53:01', '2026-01-28 04:53:43', 'proposal_6979962d76777_1769575981.pdf', NULL),
(20, 'REQ-2026-01-0013', 43, 4, 5, 30, 'rehab', 1.000, -7.09357244, 107.54902572, 'delivered', 5, '2026-01-28 06:48:04', '', NULL, 'approval_REQ-2026-01-0013_1769582884.pdf', '2026-01-28 06:41:56', '2026-01-28 07:23:20', 'proposal_6979afb449af6_1769582516.pdf', 'delivery_20_1769585000.webp'),
(21, 'REQ-2026-01-0014', 44, 4, 5, 3, 'untuk perkarangan rumah', 0.001, -6.34024360, 106.70544106, 'pending', NULL, NULL, NULL, NULL, NULL, '2026-01-29 15:03:32', '2026-01-29 15:03:32', NULL, NULL),
(22, 'REQ-2026-01-0015', 44, 4, 5, 3, 'untuk perkarangan rumah', 0.001, -6.34024360, 106.70544106, 'pending', NULL, NULL, NULL, NULL, NULL, '2026-01-29 15:03:38', '2026-01-29 15:03:38', NULL, NULL),
(23, 'REQ-2026-01-0016', 44, 4, 5, 3, 'untuk perkarangan rumah', 1.000, -6.48042754, 106.71503437, 'pending', NULL, NULL, NULL, NULL, NULL, '2026-01-29 15:04:18', '2026-01-29 15:04:18', NULL, NULL),
(24, 'REQ-2026-01-0017', 46, 4, 5, 25, 'Penanaman', 0.020, -6.34005204, 106.70558275, 'pending', NULL, NULL, NULL, NULL, NULL, '2026-01-29 16:09:38', '2026-01-29 16:09:38', NULL, NULL),
(25, 'REQ-2026-01-0018', 46, 4, 5, 25, 'Penanaman', 0.020, -6.34005204, 106.70558275, 'pending', NULL, NULL, NULL, NULL, NULL, '2026-01-29 16:09:46', '2026-01-29 16:09:46', NULL, NULL),
(26, 'REQ-2026-01-0019', 47, 5, 4, 2, 'Penghijauan', 0.001, -6.56149614, 110.65728417, 'pending', NULL, NULL, NULL, NULL, NULL, '2026-01-30 07:45:43', '2026-01-30 07:45:43', NULL, NULL),
(27, 'REQ-2026-01-0020', 47, 26, 3, 25, 'Penghijauan', 0.001, -4.03482324, 109.77330940, 'pending', NULL, NULL, NULL, NULL, NULL, '2026-01-30 07:47:58', '2026-01-30 07:47:58', NULL, NULL),
(28, 'REQ-2026-01-0021', 48, 19, 139, 25, 'penanaman', 1.000, -6.90583857, 106.98600583, 'pending', NULL, NULL, NULL, NULL, NULL, '2026-01-30 08:06:29', '2026-01-30 08:06:29', NULL, NULL),
(29, 'REQ-2026-01-0022', 47, 2, 24, 20, 'Penanaman', 0.001, 3.45051621, 98.76148226, 'pending', NULL, NULL, NULL, NULL, NULL, '2026-01-30 08:37:46', '2026-01-30 08:37:46', NULL, NULL),
(30, 'REQ-2026-01-0023', 43, 37, 139, 5, 'penghijauan', 0.002, -1.89234915, 120.89588068, 'pending', NULL, NULL, NULL, NULL, NULL, '2026-01-30 08:47:03', '2026-01-30 08:47:03', NULL, NULL),
(31, 'REQ-2026-01-0024', 43, 37, 127, 6, 'tester', 1.000, -2.21346896, 120.81142281, 'pending', NULL, NULL, NULL, NULL, NULL, '2026-01-30 09:09:34', '2026-01-30 09:09:34', NULL, NULL),
(32, 'REQ-2026-01-0025', 43, 37, 127, 6, 'tester', 1.000, -2.21346896, 120.81142281, 'pending', NULL, NULL, NULL, NULL, NULL, '2026-01-30 09:09:41', '2026-01-30 09:09:41', NULL, NULL),
(33, 'REQ-2026-01-0026', 43, 37, 14, 7, 'tes', 0.100, -2.38115008, 120.46157970, 'pending', NULL, NULL, NULL, NULL, NULL, '2026-01-30 09:16:18', '2026-01-30 09:16:18', NULL, NULL),
(34, 'REQ-2026-01-0027', 43, 37, 14, 7, 'yum', 0.001, -2.56280466, 119.40229811, 'pending', NULL, NULL, NULL, NULL, NULL, '2026-01-30 09:32:02', '2026-01-30 09:32:02', NULL, NULL),
(35, 'REQ-2026-01-0028', 43, 37, 14, 7, 'yum', 0.001, -2.56280466, 119.40229811, 'pending', NULL, NULL, NULL, NULL, NULL, '2026-01-30 09:32:54', '2026-01-30 09:32:54', NULL, NULL),
(36, 'REQ-2026-01-0029', 43, 37, 14, 7, 'yum', 0.001, -2.56280466, 119.40229811, 'pending', NULL, NULL, NULL, NULL, NULL, '2026-01-30 09:32:55', '2026-01-30 09:32:55', NULL, NULL),
(37, 'REQ-2026-01-0030', 43, 37, 14, 7, 'yum', 0.001, -2.56280466, 119.40229811, 'pending', NULL, NULL, NULL, NULL, NULL, '2026-01-30 09:32:55', '2026-01-30 09:32:55', NULL, NULL),
(38, 'REQ-2026-01-0031', 43, 37, 14, 7, 'yum', 0.001, -2.56280466, 119.40229811, 'pending', NULL, NULL, NULL, NULL, NULL, '2026-01-30 09:32:55', '2026-01-30 09:32:55', NULL, NULL),
(39, 'REQ-2026-01-0032', 43, 37, 14, 7, 'yum', 0.001, -2.56280466, 119.40229811, 'pending', NULL, NULL, NULL, NULL, NULL, '2026-01-30 09:32:56', '2026-01-30 09:32:56', NULL, NULL),
(40, 'REQ-2026-01-0033', 43, 37, 14, 7, 'yum', 0.001, -2.56280466, 119.40229811, 'pending', NULL, NULL, NULL, NULL, NULL, '2026-01-30 09:32:56', '2026-01-30 09:32:56', NULL, NULL),
(41, 'REQ-2026-01-0034', 37, 37, 1, 10, 'Testing submit request debug', 0.500, -6.20000000, 106.80000000, 'pending', NULL, NULL, NULL, NULL, NULL, '2026-01-30 09:46:08', '2026-01-30 09:46:08', NULL, NULL),
(42, 'REQ-2026-01-0035', 43, 37, 139, 6, 'tester', 0.020, -3.79409915, 120.19388723, 'approved', 37, '2026-01-30 09:49:42', 'ok', NULL, 'approval_REQ-2026-01-0035_1769766582.pdf', '2026-01-30 09:49:22', '2026-01-30 09:49:42', NULL, NULL),
(43, 'REQ-2026-02-0001', 43, 24, NULL, NULL, 'reboisasi', 0.023, -8.64934625, 115.21132557, 'delivered', 26, '2026-02-02 04:14:51', 'oke', NULL, 'approval_REQ-2026-02-0001_1770005691.pdf', '2026-02-02 04:12:41', '2026-02-02 04:16:16', NULL, 'delivery_43_1770005775.webp');

-- --------------------------------------------------------

--
-- Struktur dari tabel `request_history`
--

CREATE TABLE `request_history` (
  `id` int(11) NOT NULL,
  `request_id` int(11) NOT NULL,
  `status` enum('pending','approved','rejected','completed') NOT NULL,
  `changed_by` int(11) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `request_history`
--

INSERT INTO `request_history` (`id`, `request_id`, `status`, `changed_by`, `notes`, `created_at`) VALUES
(1, 1, 'pending', 18, 'Permintaan dibuat', '2025-12-18 12:32:02'),
(2, 1, 'approved', 5, 'silakan ambil di Persemaian Rumpin', '2025-12-18 12:36:58'),
(7, 6, 'pending', 18, 'Permintaan dibuat', '2025-12-22 15:42:30'),
(9, 8, 'pending', 39, 'Permintaan dibuat', '2026-01-27 04:01:31'),
(10, 9, 'pending', 42, 'Permintaan dibuat', '2026-01-27 05:37:40'),
(11, 10, 'pending', 42, 'Permintaan dibuat', '2026-01-27 05:37:52'),
(12, 8, 'approved', 37, 'jangan lupa geotag', '2026-01-27 09:24:06'),
(13, 11, 'pending', 18, 'Permintaan dibuat', '2026-01-28 02:40:34'),
(14, 11, 'approved', 24, '', '2026-01-28 02:41:48'),
(15, 12, 'pending', 43, 'Permintaan dibuat', '2026-01-28 02:44:59'),
(16, 12, 'approved', 24, '', '2026-01-28 02:45:37'),
(17, 13, 'pending', 43, 'Permintaan dibuat', '2026-01-28 02:47:45'),
(18, 13, 'approved', 2, '', '2026-01-28 02:50:21'),
(19, 14, 'pending', 43, 'Permintaan dibuat', '2026-01-28 03:33:37'),
(20, 14, 'approved', 35, '', '2026-01-28 03:35:20'),
(21, 15, 'pending', 43, 'Permintaan dibuat', '2026-01-28 03:55:44'),
(22, 15, 'approved', 23, '', '2026-01-28 03:59:46'),
(23, 16, 'pending', 43, 'Permintaan dibuat', '2026-01-28 04:02:05'),
(24, 16, 'approved', 24, '', '2026-01-28 04:06:37'),
(25, 17, 'pending', 43, 'Permintaan dibuat', '2026-01-28 04:37:38'),
(26, 17, 'approved', 33, '', '2026-01-28 04:38:54'),
(27, 10, 'rejected', 5, 'kurang lengkap tidak ada surat permohonana', '2026-01-28 04:48:39'),
(28, 9, 'rejected', 5, 'kurang lengkap tidak ada surat permohonana', '2026-01-28 04:48:52'),
(29, 18, 'pending', 43, 'Permintaan dibuat', '2026-01-28 04:50:00'),
(30, 18, 'rejected', 5, 'seriys 1000hektar?', '2026-01-28 04:51:48'),
(31, 19, 'pending', 43, 'Permintaan dibuat', '2026-01-28 04:53:01'),
(32, 19, 'approved', 5, 'oke', '2026-01-28 04:53:43'),
(33, 20, 'pending', 43, 'Permintaan dibuat', '2026-01-28 06:41:56'),
(34, 20, 'approved', 5, '', '2026-01-28 06:48:04'),
(35, 21, 'pending', 44, 'Permintaan dibuat', '2026-01-29 15:03:32'),
(36, 22, 'pending', 44, 'Permintaan dibuat', '2026-01-29 15:03:38'),
(37, 23, 'pending', 44, 'Permintaan dibuat', '2026-01-29 15:04:18'),
(38, 24, 'pending', 46, 'Permintaan dibuat', '2026-01-29 16:09:38'),
(39, 25, 'pending', 46, 'Permintaan dibuat', '2026-01-29 16:09:46'),
(40, 26, 'pending', 47, 'Permintaan dibuat', '2026-01-30 07:45:43'),
(41, 27, 'pending', 47, 'Permintaan dibuat', '2026-01-30 07:47:58'),
(42, 28, 'pending', 48, 'Permintaan dibuat', '2026-01-30 08:06:29'),
(43, 29, 'pending', 47, 'Permintaan dibuat', '2026-01-30 08:37:46'),
(51, 30, 'pending', 43, 'Permintaan dibuat', '2026-01-30 08:47:03'),
(55, 31, 'pending', 43, 'Permintaan dibuat', '2026-01-30 09:09:35'),
(56, 32, 'pending', 43, 'Permintaan dibuat', '2026-01-30 09:09:41'),
(57, 33, 'pending', 43, 'Permintaan dibuat', '2026-01-30 09:16:18'),
(58, 34, 'pending', 43, 'Permintaan dibuat', '2026-01-30 09:32:02'),
(59, 35, 'pending', 43, 'Permintaan dibuat', '2026-01-30 09:32:54'),
(60, 36, 'pending', 43, 'Permintaan dibuat', '2026-01-30 09:32:55'),
(61, 37, 'pending', 43, 'Permintaan dibuat', '2026-01-30 09:32:55'),
(62, 38, 'pending', 43, 'Permintaan dibuat', '2026-01-30 09:32:55'),
(63, 39, 'pending', 43, 'Permintaan dibuat', '2026-01-30 09:32:56'),
(64, 40, 'pending', 43, 'Permintaan dibuat', '2026-01-30 09:32:56'),
(65, 41, 'pending', 37, 'Permintaan dibuat (DEBUG TEST)', '2026-01-30 09:46:08'),
(66, 42, 'pending', 43, 'Permintaan dibuat', '2026-01-30 09:49:22'),
(67, 42, 'approved', 37, 'ok', '2026-01-30 09:49:42'),
(68, 43, 'pending', 43, 'Permintaan dibuat', '2026-02-02 04:12:41'),
(69, 43, 'approved', 26, 'oke', '2026-02-02 04:14:51');

-- --------------------------------------------------------

--
-- Struktur dari tabel `request_items`
--

CREATE TABLE `request_items` (
  `id` int(11) NOT NULL,
  `request_id` int(11) NOT NULL,
  `seedling_type_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `request_items`
--

INSERT INTO `request_items` (`id`, `request_id`, `seedling_type_id`, `quantity`, `created_at`) VALUES
(1, 1, 5, 25, '2025-12-18 12:32:02'),
(2, 6, 25, 35, '2025-12-22 15:42:30'),
(3, 8, 65, 8, '2026-01-27 04:01:31'),
(4, 9, 5, 100, '2026-01-27 05:37:40'),
(5, 10, 5, 100, '2026-01-27 05:37:52'),
(6, 11, 89, 5, '2026-01-28 02:40:34'),
(7, 12, 139, 2, '2026-01-28 02:44:59'),
(8, 13, 139, 4, '2026-01-28 02:47:45'),
(9, 14, 42, 2, '2026-01-28 03:33:37'),
(10, 15, 10, 5, '2026-01-28 03:55:43'),
(11, 16, 44, 69, '2026-01-28 04:02:05'),
(12, 17, 93, 4, '2026-01-28 04:37:38'),
(13, 18, 5, 70, '2026-01-28 04:50:00'),
(14, 19, 5, 70, '2026-01-28 04:53:01'),
(15, 20, 5, 30, '2026-01-28 06:41:56'),
(16, 21, 5, 3, '2026-01-29 15:03:32'),
(17, 22, 5, 3, '2026-01-29 15:03:38'),
(18, 23, 5, 3, '2026-01-29 15:04:18'),
(19, 24, 5, 25, '2026-01-29 16:09:38'),
(20, 25, 5, 25, '2026-01-29 16:09:46'),
(21, 26, 4, 2, '2026-01-30 07:45:43'),
(22, 27, 3, 25, '2026-01-30 07:47:58'),
(23, 28, 139, 25, '2026-01-30 08:06:29'),
(24, 29, 24, 20, '2026-01-30 08:37:46'),
(25, 30, 139, 5, '2026-01-30 08:47:03'),
(26, 31, 127, 6, '2026-01-30 09:09:34'),
(27, 32, 127, 6, '2026-01-30 09:09:41'),
(28, 33, 14, 7, '2026-01-30 09:16:18'),
(29, 34, 14, 7, '2026-01-30 09:32:02'),
(30, 35, 14, 7, '2026-01-30 09:32:54'),
(31, 36, 14, 7, '2026-01-30 09:32:55'),
(32, 37, 14, 7, '2026-01-30 09:32:55'),
(33, 38, 14, 7, '2026-01-30 09:32:55'),
(34, 39, 14, 7, '2026-01-30 09:32:56'),
(35, 40, 14, 7, '2026-01-30 09:32:56'),
(36, 41, 1, 10, '2026-01-30 09:46:08'),
(37, 42, 139, 6, '2026-01-30 09:49:22'),
(64, 43, 29, 10, '2026-02-02 04:12:41'),
(65, 43, 98, 5, '2026-02-02 04:12:41'),
(66, 43, 128, 5, '2026-02-02 04:12:41');

-- --------------------------------------------------------

--
-- Struktur dari tabel `seedling_types`
--

CREATE TABLE `seedling_types` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `scientific_name` varchar(200) DEFAULT NULL,
  `category` enum('Pohon Hutan','Pohon Buah','Tanaman Obat','Bambu','Mangrove','Lainnya') DEFAULT 'Pohon Hutan',
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `seedling_types`
--

INSERT INTO `seedling_types` (`id`, `name`, `scientific_name`, `category`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Jati', 'Tectona Grandis', '', 'Pohon Jati', 1, '2025-12-18 08:21:10', '2026-02-02 04:42:59'),
(3, 'Mahoni', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(4, 'Pucuk Merah', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(5, 'Sengon', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(6, 'Trembesi', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(7, 'Jambu Air', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(8, 'Jambu Air vegetatif', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(9, 'Jambu kristal vegetatif', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(10, 'Jambu Biji', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(11, 'Jambu Mente', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(12, 'Jamblang', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(13, 'Jengkol', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(14, 'Jeruk (Nipis/Lemon)', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(15, 'Jeruk', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(16, 'Petai', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(17, 'Pinang', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(18, 'Suren/ Inggul', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(19, 'Pinus', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(20, 'Meranti', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(21, 'Pucuk Merah', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(22, 'Kayu manis', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(23, 'Kemenyan', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(24, 'Kopi', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(25, 'Aren', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(26, 'Durian', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(27, 'Durian vegetatif', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(28, 'Kemiri', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(29, 'Alpukat', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(30, 'Mangga', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(31, 'Alpukat vegetatif', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(32, 'Tabebuya', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(33, 'Karet', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(34, 'Nangka', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(35, 'Rambutan', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(36, 'Cemara', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(37, 'Cemara Laut', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(38, 'Belangiran', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(39, 'Mangrove/ Rhizophora', '', 'Mangrove', '', 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(40, 'Mangrove / Avicennia', '', 'Mangrove', '', 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(41, 'Mangrove / Ceriops', '', 'Mangrove', '', 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(42, 'Matoa', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(43, 'Jambu Bol', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(44, 'Sirsak', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(45, 'Manggis', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(46, 'Cengkeh', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(47, 'Bayur', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(48, 'Kayu Putih', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(49, 'Jabon', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(50, 'Jabon merah', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(51, 'Jeruk Lemon', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(52, 'Jeruk Lemon vegetatif', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(53, 'Glodokan', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(54, 'Kelengkeng', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(55, 'Kelengkeng vegetatif', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(56, 'Indigofera', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(57, 'Jelutung', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(58, 'Bulian/Ulin', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(59, 'Akasia', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(60, 'Balsa', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(61, 'Balau', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(62, 'Kelor vegetatif', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(63, 'Angsana', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(64, 'Angsana vetetatif', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(65, 'Beringin', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(66, 'Gayam', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(67, 'Gaharu', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(68, 'Kaliandra', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(69, 'Saputangan', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(70, 'Sawa', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(71, 'Sawo Kecik', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(72, 'Sawo', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(73, 'Bambang Lanang', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(74, 'kayu Bawang', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(75, 'Pucung/Pangi/Keluwak/Picung', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(76, 'Pala', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(77, 'Kabau', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(78, 'Vetiver', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(79, 'Bogenvil', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(80, 'Bambu', '', 'Bambu', '', 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(81, 'Bambu Petung', '', 'Bambu', '', 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(82, 'Sukun', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(83, 'Gmelina / Jati Putih', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(84, 'Sungkai', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(85, 'Nyatoh', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(86, 'Eboni', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(87, 'Eukaliptus', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(88, 'Eukaliptus deglupta', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(89, 'Langsat', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(90, 'Plajau', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(91, 'Agathis', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(92, 'Medang', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(93, 'Nyamplung', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(94, 'Mata Kucing', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(95, 'Pulai', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(96, 'Spathodea', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(97, 'Tanjung', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(98, 'Ampupu', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(99, 'Flamboyan', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(100, 'Cempedak', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(101, 'Kecapi', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(102, 'Bangeris', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(103, 'Kapur', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(104, 'Keruing', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(105, 'Lamtoro', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(106, 'Melinjo', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(107, 'Gandaria', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(108, 'Kenari', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(109, 'Resak', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(110, 'Salam', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(111, 'Semangkok', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(112, 'Bungur', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(113, 'Asam Kranji', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(114, 'Asam Jawa', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(115, 'Asam Londo', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(116, 'Nyawai', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(117, 'Cendana', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(118, 'Rambai', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(119, 'Klicung', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(120, 'Munting', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(121, 'Palem', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(122, 'Merbau', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(123, 'Linggua', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(124, 'Tengkawang', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(125, 'Armon', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(126, 'Bitti', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(127, 'Kultur Jaringan', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(128, 'Cempaka', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(129, 'Malapari', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(130, 'Kepuh', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(131, 'Timoho', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(132, 'Kayu Kuku', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(133, 'Pohon Roda', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(134, 'Kemuning', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(135, 'Keluwih', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(136, 'Nam-Nam', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(137, 'Kepel', '', '', '', 1, '0000-00-00 00:00:00', '2026-02-02 04:42:59'),
(138, 'Ketapang', '-', '', '', 1, '2025-12-18 09:00:57', '2026-02-02 04:42:59'),
(139, 'Ketapang Kencana', '', '', '', 1, '2025-12-18 09:01:07', '2026-02-02 04:42:59');

-- --------------------------------------------------------

--
-- Struktur dari tabel `stock`
--

CREATE TABLE `stock` (
  `id` int(11) NOT NULL,
  `bpdas_id` int(11) NOT NULL,
  `seedling_type_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `last_update_date` date NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `stock`
--

INSERT INTO `stock` (`id`, `bpdas_id`, `seedling_type_id`, `quantity`, `last_update_date`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 138, 1000, '2025-12-18', 'ketapang', '2025-12-18 09:01:35', '2025-12-18 09:01:35'),
(2, 1, 139, 3460, '2026-01-28', '', '2025-12-18 09:01:59', '2026-01-28 02:50:21'),
(3, 1, 4, 15000, '2025-12-18', '', '2025-12-18 09:06:15', '2025-12-18 09:06:15'),
(4, 1, 5, 3000, '2025-12-18', '', '2025-12-18 09:06:30', '2025-12-18 09:06:30'),
(5, 1, 6, 4631, '2025-12-18', '', '2025-12-18 09:06:49', '2025-12-18 09:06:49'),
(6, 1, 7, 1000, '2025-12-18', '', '2025-12-18 09:07:07', '2025-12-18 09:07:07'),
(7, 1, 10, 9000, '2025-12-18', '', '2025-12-18 09:07:29', '2025-12-18 09:07:29'),
(8, 1, 13, 9892, '2025-12-18', '', '2025-12-18 09:10:26', '2025-12-18 09:10:26'),
(9, 1, 14, 3000, '2025-12-18', '', '2025-12-18 09:10:41', '2025-12-18 09:10:41'),
(10, 1, 16, 13788, '2025-12-18', '', '2025-12-18 09:10:55', '2025-12-18 09:10:55'),
(11, 1, 17, 18000, '2025-12-18', '', '2025-12-18 09:11:10', '2025-12-18 09:11:10'),
(12, 2, 5, 3264, '2025-12-18', '', '2025-12-18 09:33:55', '2025-12-18 09:33:55'),
(13, 2, 19, 12063, '2025-12-18', '', '2025-12-18 09:34:17', '2025-12-18 09:34:17'),
(14, 2, 18, 3659, '2025-12-18', '', '2025-12-18 09:34:33', '2025-12-18 09:34:33'),
(15, 2, 20, 16254, '2025-12-18', '', '2025-12-18 09:34:49', '2025-12-18 09:34:49'),
(16, 2, 4, 6713, '2025-12-18', '', '2025-12-18 09:35:02', '2025-12-18 09:35:02'),
(17, 2, 22, 7342, '2025-12-18', '', '2025-12-18 09:35:18', '2025-12-18 09:35:18'),
(18, 2, 23, 4228, '2025-12-18', '', '2025-12-18 09:35:55', '2025-12-18 09:35:55'),
(19, 2, 24, 5080, '2026-01-30', '', '2025-12-18 09:36:08', '2026-01-30 08:33:07'),
(20, 2, 25, 7561, '2025-12-18', '', '2025-12-18 09:36:24', '2025-12-18 09:36:24'),
(21, 2, 26, 22167, '2025-12-18', '', '2025-12-18 09:36:36', '2025-12-18 09:36:36'),
(22, 2, 28, 4457, '2025-12-18', '', '2025-12-18 09:36:49', '2025-12-18 09:36:49'),
(23, 2, 30, 15517, '2025-12-18', '', '2025-12-18 09:37:00', '2025-12-18 09:37:00'),
(24, 2, 29, 12541, '2025-12-18', '', '2025-12-18 09:37:10', '2025-12-18 09:37:10'),
(25, 3, 139, 3850, '2025-12-18', '', '2025-12-18 09:42:10', '2025-12-18 09:42:10'),
(26, 3, 3, 12186, '2025-12-18', '', '2025-12-18 09:42:27', '2025-12-18 09:42:27'),
(27, 3, 5, 458, '2025-12-18', '', '2025-12-18 09:42:39', '2025-12-18 09:42:39'),
(28, 3, 8, 9365, '2025-12-18', '', '2025-12-18 09:42:56', '2025-12-18 09:42:56'),
(29, 3, 10, 410, '2025-12-18', '', '2025-12-18 09:43:08', '2025-12-18 09:43:08'),
(30, 3, 20, 5829, '2025-12-18', '', '2025-12-18 09:43:53', '2025-12-18 09:43:53'),
(31, 3, 4, 165, '2025-12-18', '', '2025-12-18 09:44:10', '2025-12-18 09:44:10'),
(32, 3, 22, 5081, '2025-12-18', '', '2025-12-18 09:44:29', '2025-12-18 09:44:29'),
(33, 3, 25, 7114, '2025-12-18', '', '2025-12-18 09:44:44', '2025-12-18 09:44:56'),
(34, 3, 29, 2181, '2025-12-18', '', '2025-12-18 09:45:09', '2025-12-18 09:45:09'),
(35, 3, 34, 6312, '2025-12-18', '', '2025-12-18 09:45:59', '2025-12-18 09:45:59'),
(36, 3, 42, 3149, '2025-12-18', '', '2025-12-18 09:46:15', '2025-12-18 09:46:15'),
(37, 3, 44, 3896, '2025-12-18', '', '2025-12-18 09:46:26', '2025-12-18 09:46:26'),
(38, 3, 51, 410, '2025-12-18', '', '2025-12-18 09:46:39', '2025-12-18 09:46:39'),
(39, 3, 55, 854, '2025-12-18', '', '2025-12-18 09:46:49', '2025-12-18 09:46:49'),
(40, 3, 57, 3065, '2025-12-18', '', '2025-12-18 09:47:04', '2025-12-18 09:47:04'),
(41, 3, 58, 1314, '2025-12-18', '', '2025-12-18 09:47:13', '2025-12-18 09:47:13'),
(42, 3, 62, 2216, '2025-12-18', '', '2025-12-18 09:47:23', '2025-12-18 09:47:23'),
(43, 3, 64, 652, '2025-12-18', '', '2025-12-18 09:47:35', '2025-12-18 09:47:35'),
(44, 3, 67, 16495, '2025-12-18', '', '2025-12-18 09:47:47', '2025-12-18 09:47:47'),
(45, 4, 5, 36270, '2026-01-28', '', '2025-12-18 09:50:38', '2026-01-28 06:48:04'),
(46, 4, 39, 3000, '2025-12-18', '', '2025-12-18 09:50:55', '2025-12-18 09:50:55'),
(47, 5, 139, 1500, '2025-12-18', '', '2025-12-18 09:54:56', '2025-12-18 09:54:56'),
(48, 5, 4, 8000, '2025-12-18', '', '2025-12-18 09:55:06', '2025-12-18 09:55:06'),
(49, 5, 6, 1000, '2025-12-18', '', '2025-12-18 09:55:14', '2025-12-18 09:55:14'),
(50, 5, 10, 10000, '2025-12-18', '', '2025-12-18 09:55:59', '2025-12-18 09:55:59'),
(51, 5, 13, 4000, '2025-12-18', '', '2025-12-18 09:56:09', '2025-12-18 09:56:09'),
(52, 5, 16, 10000, '2025-12-18', '', '2025-12-18 09:56:18', '2025-12-18 09:56:18'),
(53, 5, 32, 3000, '2025-12-18', '', '2025-12-18 09:56:27', '2025-12-18 09:56:27'),
(54, 5, 37, 3000, '2025-12-18', '', '2025-12-18 09:56:35', '2025-12-18 09:56:35'),
(55, 5, 44, 4000, '2025-12-18', '', '2025-12-18 09:56:43', '2025-12-18 09:56:43'),
(56, 6, 5, 43062, '2025-12-18', '', '2025-12-18 10:02:29', '2025-12-18 10:02:29'),
(57, 6, 6, 2965, '2025-12-18', '', '2025-12-18 10:02:39', '2025-12-18 10:02:39'),
(58, 6, 16, 7840, '2025-12-18', '', '2025-12-18 10:02:51', '2025-12-18 10:02:51'),
(59, 6, 24, 1075, '2025-12-18', '', '2025-12-18 10:03:08', '2025-12-18 10:03:08'),
(60, 6, 26, 6751, '2025-12-18', '', '2025-12-18 10:03:16', '2025-12-18 10:03:16'),
(61, 6, 29, 15184, '2025-12-18', '', '2025-12-18 10:03:40', '2025-12-18 10:03:40'),
(62, 6, 31, 5000, '2025-12-18', '', '2025-12-18 10:03:51', '2025-12-18 10:03:51'),
(63, 6, 32, 18290, '2025-12-18', '', '2025-12-18 10:04:00', '2025-12-18 10:04:00'),
(64, 6, 34, 10302, '2025-12-18', '', '2025-12-18 10:04:09', '2025-12-18 10:04:09'),
(65, 6, 42, 8571, '2025-12-18', '', '2025-12-18 10:04:24', '2025-12-18 10:04:24'),
(66, 6, 44, 9494, '2025-12-18', '', '2025-12-18 10:04:34', '2025-12-18 10:04:34'),
(67, 6, 59, 13070, '2025-12-18', '', '2025-12-18 10:04:45', '2025-12-18 10:04:45'),
(68, 6, 82, 1725, '2025-12-18', '', '2025-12-18 10:04:59', '2025-12-18 10:04:59'),
(69, 7, 139, 5000, '2025-12-18', '', '2025-12-18 10:07:39', '2025-12-18 10:07:39'),
(70, 7, 4, 2500, '2025-12-18', '', '2025-12-18 10:07:50', '2025-12-18 10:07:50'),
(71, 7, 6, 5000, '2025-12-18', '', '2025-12-18 10:08:00', '2025-12-18 10:08:00'),
(72, 7, 13, 60000, '2025-12-18', '', '2025-12-18 10:08:10', '2025-12-18 10:08:10'),
(73, 7, 16, 88250, '2025-12-18', '', '2025-12-18 10:08:22', '2025-12-18 10:08:22'),
(74, 7, 26, 40000, '2025-12-18', '', '2025-12-18 10:08:39', '2025-12-18 10:08:39'),
(75, 7, 32, 5000, '2025-12-18', '\r\n', '2025-12-18 10:08:49', '2025-12-18 10:08:49'),
(76, 7, 33, 13000, '2025-12-18', '', '2025-12-18 10:08:57', '2025-12-18 10:08:57'),
(77, 7, 35, 14000, '2025-12-18', '', '2025-12-18 10:09:10', '2025-12-18 10:09:10'),
(78, 7, 42, 7500, '2025-12-18', '', '2025-12-18 10:09:22', '2025-12-18 10:09:22'),
(79, 7, 44, 4500, '2025-12-18', '', '2025-12-18 10:09:30', '2025-12-18 10:09:30'),
(80, 7, 68, 11000, '2025-12-18', '', '2025-12-18 10:09:41', '2025-12-18 10:09:41'),
(81, 7, 89, 22000, '2025-12-18', '', '2025-12-18 10:09:51', '2025-12-18 10:09:51'),
(82, 7, 92, 500, '2025-12-18', '', '2025-12-18 10:10:07', '2025-12-18 10:10:07'),
(83, 7, 95, 6200, '2025-12-18', '', '2025-12-18 10:10:21', '2025-12-18 10:10:21'),
(84, 7, 100, 15500, '2025-12-18', '', '2025-12-18 10:10:41', '2025-12-18 10:10:41'),
(85, 7, 124, 50, '2025-12-18', '', '2025-12-18 10:10:53', '2025-12-18 10:10:53'),
(86, 8, 138, 3202, '2025-12-18', '', '2025-12-18 10:13:24', '2025-12-18 10:13:24'),
(87, 8, 3, 108941, '2025-12-18', '', '2025-12-18 10:13:37', '2025-12-18 10:13:37'),
(88, 8, 4, 77972, '2025-12-18', '', '2025-12-18 10:13:56', '2025-12-18 10:13:56'),
(89, 8, 5, 23034, '2025-12-18', '', '2025-12-18 10:14:16', '2025-12-18 10:14:16'),
(90, 8, 6, 4572, '2025-12-18', '', '2025-12-18 10:14:28', '2025-12-18 10:14:28'),
(91, 8, 7, 42332, '2025-12-18', '', '2025-12-18 10:14:46', '2025-12-18 10:14:46'),
(92, 8, 8, 2917, '2025-12-18', '', '2025-12-18 10:14:56', '2025-12-18 10:14:56'),
(93, 8, 9, 5441, '2025-12-18', '', '2025-12-18 10:15:12', '2025-12-18 10:15:12'),
(94, 8, 10, 58709, '2025-12-18', '', '2025-12-18 10:15:26', '2025-12-18 10:15:26'),
(95, 8, 13, 9838, '2025-12-18', '', '2025-12-18 10:15:37', '2025-12-18 10:15:37'),
(96, 8, 16, 14470, '2025-12-18', '', '2025-12-18 10:15:49', '2025-12-18 10:15:49'),
(97, 8, 20, 960065, '2025-12-18', '', '2025-12-18 10:16:08', '2025-12-18 10:16:08'),
(98, 8, 25, 13005, '2025-12-18', '', '2025-12-18 10:16:24', '2025-12-18 10:16:24'),
(99, 8, 26, 5753, '2025-12-18', '', '2025-12-18 10:16:34', '2025-12-18 10:16:34'),
(100, 8, 32, 168837, '2025-12-18', '', '2025-12-18 10:16:47', '2025-12-18 10:16:47'),
(101, 8, 34, 13297, '2025-12-18', '', '2025-12-18 10:17:02', '2025-12-18 10:17:02'),
(102, 8, 35, 39282, '2025-12-18', '', '2025-12-18 10:17:17', '2025-12-18 10:17:17'),
(103, 8, 39, 85133, '2025-12-18', '', '2025-12-18 10:17:29', '2025-12-18 10:17:29'),
(104, 8, 42, 17374, '2025-12-18', '', '2025-12-18 10:17:42', '2025-12-18 10:17:42'),
(105, 8, 44, 11249, '2025-12-18', '', '2025-12-18 10:17:52', '2025-12-18 10:17:52'),
(106, 8, 49, 949, '2025-12-18', '', '2025-12-18 10:18:02', '2025-12-18 10:18:02'),
(107, 8, 53, 2567, '2025-12-18', '', '2025-12-18 10:18:12', '2025-12-18 10:18:12'),
(108, 8, 58, 224, '2025-12-18', '', '2025-12-18 10:18:26', '2025-12-18 10:18:26'),
(109, 8, 60, 24731, '2025-12-18', '\r\n', '2025-12-18 10:18:39', '2025-12-18 10:18:39'),
(110, 8, 67, 124302, '2025-12-18', '', '2025-12-18 10:18:52', '2025-12-18 10:18:52'),
(111, 8, 68, 9282, '2025-12-18', '', '2025-12-18 10:19:01', '2025-12-18 10:19:01'),
(112, 8, 72, 8360, '2025-12-18', '', '2025-12-18 10:19:15', '2025-12-18 10:19:15'),
(113, 8, 80, 1916, '2025-12-18', '', '2025-12-18 10:19:27', '2025-12-18 10:19:27'),
(114, 8, 84, 25579, '2025-12-18', '', '2025-12-18 10:19:43', '2025-12-18 10:19:43'),
(115, 8, 85, 113847, '2025-12-18', '', '2025-12-18 10:19:56', '2025-12-18 10:19:56'),
(116, 8, 89, 13914, '2025-12-18', '', '2025-12-18 10:20:10', '2025-12-18 10:20:10'),
(117, 8, 91, 3000, '2025-12-18', '', '2025-12-18 10:20:17', '2025-12-18 10:20:17'),
(118, 8, 92, 8056, '2025-12-18', '', '2025-12-18 10:20:29', '2025-12-18 10:20:29'),
(119, 8, 93, 29004, '2025-12-18', '', '2025-12-18 10:20:44', '2025-12-18 10:20:44'),
(120, 8, 94, 3662, '2025-12-18', '', '2025-12-18 10:20:56', '2025-12-18 10:20:56'),
(121, 8, 95, 138287, '2025-12-18', '', '2025-12-18 10:21:13', '2025-12-18 10:21:13'),
(122, 8, 96, 82000, '2025-12-18', '', '2025-12-18 10:21:25', '2025-12-18 10:21:25'),
(123, 8, 97, 15904, '2025-12-18', '', '2025-12-18 10:21:34', '2025-12-18 10:21:34'),
(124, 8, 99, 13703, '2025-12-18', '', '2025-12-18 10:21:45', '2025-12-18 10:21:45'),
(125, 8, 100, 42291, '2025-12-18', '', '2025-12-18 10:21:57', '2025-12-18 10:21:57'),
(126, 8, 101, 40921, '2025-12-18', '', '2025-12-18 10:22:08', '2025-12-18 10:22:08'),
(127, 8, 102, 491, '2025-12-18', '', '2025-12-18 10:22:17', '2025-12-18 10:22:17'),
(128, 8, 103, 44980, '2025-12-18', '', '2025-12-18 10:22:28', '2025-12-18 10:22:28'),
(129, 8, 104, 1222, '2025-12-18', '', '2025-12-18 10:22:38', '2025-12-18 10:22:38'),
(130, 8, 105, 14493, '2025-12-18', '', '2025-12-18 10:22:48', '2025-12-18 10:22:48'),
(131, 8, 109, 5636, '2025-12-18', '', '2025-12-18 10:22:58', '2025-12-18 10:22:58'),
(132, 8, 110, 78353, '2025-12-18', '', '2025-12-18 10:23:11', '2025-12-18 10:23:11'),
(133, 8, 111, 9788, '2025-12-18', '', '2025-12-18 10:23:22', '2025-12-18 10:23:22'),
(134, 8, 112, 2521, '2025-12-18', '', '2025-12-18 10:23:32', '2025-12-18 10:23:32'),
(135, 8, 113, 485, '2025-12-18', '', '2025-12-18 10:23:42', '2025-12-18 10:23:42'),
(136, 8, 116, 828, '2025-12-18', '', '2025-12-18 10:23:52', '2025-12-18 10:23:52'),
(137, 8, 118, 156572, '2025-12-18', '', '2025-12-18 10:24:05', '2025-12-18 10:24:05'),
(138, 9, 18, 700, '2025-12-18', '', '2025-12-18 10:37:04', '2025-12-18 10:37:04'),
(139, 9, 25, 700, '2025-12-18', '', '2025-12-18 10:37:12', '2025-12-18 10:37:12'),
(140, 9, 26, 3000, '2025-12-18', '', '2025-12-18 10:37:24', '2025-12-18 10:37:24'),
(141, 9, 87, 3500, '2025-12-18', '', '2025-12-18 10:37:38', '2025-12-18 10:37:38'),
(142, 10, 6, 3000, '2025-12-18', '', '2025-12-18 10:40:41', '2025-12-18 10:40:41'),
(143, 10, 13, 1000, '2025-12-18', '', '2025-12-18 10:40:51', '2025-12-18 10:40:51'),
(144, 10, 16, 500, '2025-12-18', '', '2025-12-18 10:41:01', '2025-12-18 10:41:01'),
(145, 10, 17, 30000, '2025-12-18', '', '2025-12-18 10:41:12', '2025-12-18 10:41:12'),
(146, 10, 26, 1200, '2025-12-18', '', '2025-12-18 10:41:23', '2025-12-18 10:41:23'),
(147, 10, 34, 2000, '2025-12-18', '', '2025-12-18 10:41:35', '2025-12-18 10:41:35'),
(148, 10, 39, 4500, '2025-12-18', '', '2025-12-18 10:41:49', '2025-12-18 10:41:49'),
(149, 10, 44, 2000, '2025-12-18', '', '2025-12-18 10:41:57', '2025-12-18 10:41:57'),
(150, 10, 53, 2000, '2025-12-18', '', '2025-12-18 10:42:08', '2025-12-18 10:42:08'),
(151, 10, 54, 1000, '2025-12-18', '', '2025-12-18 10:42:27', '2025-12-18 10:42:27'),
(152, 10, 58, 3000, '2025-12-18', '', '2025-12-18 10:42:39', '2025-12-18 10:42:39'),
(153, 10, 67, 4200, '2025-12-18', '', '2025-12-18 10:42:48', '2025-12-18 10:42:48'),
(154, 10, 110, 3500, '2025-12-18', '', '2025-12-18 10:43:01', '2025-12-18 10:43:01'),
(155, 10, 114, 4500, '2025-12-18', '', '2025-12-18 10:43:08', '2025-12-18 10:43:08'),
(156, 10, 121, 1000, '2025-12-18', '', '2025-12-18 10:43:15', '2025-12-18 10:43:15'),
(157, 11, 6, 1000, '2025-12-18', '', '2025-12-18 10:45:32', '2025-12-18 10:45:32'),
(158, 11, 13, 3590, '2025-12-18', '', '2025-12-18 10:45:43', '2025-12-18 10:45:43'),
(159, 11, 16, 700, '2025-12-18', '', '2025-12-18 10:45:50', '2025-12-18 10:45:50'),
(160, 11, 17, 500, '2025-12-18', '', '2025-12-18 10:45:57', '2025-12-18 10:45:57'),
(161, 11, 18, 500, '2025-12-18', '', '2025-12-18 10:46:05', '2025-12-18 10:46:05'),
(162, 11, 24, 2270, '2025-12-18', '', '2025-12-18 10:46:15', '2025-12-18 10:46:15'),
(163, 11, 25, 5400, '2025-12-18', '', '2025-12-18 10:46:25', '2025-12-18 10:46:25'),
(164, 11, 26, 1350, '2025-12-18', '', '2025-12-18 10:46:33', '2025-12-18 10:46:33'),
(165, 11, 28, 500, '2025-12-18', '', '2025-12-18 10:46:41', '2025-12-18 10:46:41'),
(166, 11, 30, 1180, '2025-12-18', '', '2025-12-18 10:46:48', '2025-12-18 10:46:48'),
(167, 11, 29, 3022, '2025-12-18', '', '2025-12-18 10:46:55', '2025-12-18 10:46:55'),
(168, 11, 32, 709, '2025-12-18', '', '2025-12-18 10:47:02', '2025-12-18 10:47:02'),
(169, 11, 33, 2400, '2025-12-18', '', '2025-12-18 10:47:08', '2025-12-18 10:47:08'),
(170, 11, 34, 3255, '2025-12-18', '', '2025-12-18 10:47:22', '2025-12-18 10:47:22'),
(171, 11, 35, 500, '2025-12-18', '', '2025-12-18 10:47:32', '2025-12-18 10:47:32'),
(172, 12, 3, 557, '2025-12-18', '', '2025-12-18 10:50:21', '2025-12-18 10:50:21'),
(173, 12, 13, 36447, '2025-12-18', '', '2025-12-18 10:50:34', '2025-12-18 10:50:34'),
(174, 12, 16, 28814, '2025-12-18', '', '2025-12-18 10:50:44', '2025-12-18 10:50:44'),
(175, 12, 18, 5000, '2025-12-18', '', '2025-12-18 10:50:53', '2025-12-18 10:50:53'),
(176, 12, 22, 30073, '2025-12-18', '', '2025-12-18 10:51:05', '2025-12-18 10:51:05'),
(177, 12, 25, 5725, '2025-12-18', '', '2025-12-18 10:51:18', '2025-12-18 10:51:18'),
(178, 12, 26, 3439, '2025-12-18', '', '2025-12-18 10:51:28', '2025-12-18 10:51:28'),
(179, 12, 27, 2335, '2025-12-18', '', '2025-12-18 10:51:38', '2025-12-18 10:51:38'),
(180, 12, 29, 4210, '2025-12-18', '', '2025-12-18 10:51:48', '2025-12-18 10:51:48'),
(181, 12, 31, 2310, '2025-12-18', '', '2025-12-18 10:51:58', '2025-12-18 10:51:58'),
(182, 12, 39, 8000, '2025-12-18', '', '2025-12-18 10:52:10', '2025-12-18 10:52:10'),
(183, 12, 42, 7204, '2025-12-18', '', '2025-12-18 10:52:21', '2025-12-18 10:52:21'),
(184, 12, 45, 875, '2025-12-18', '', '2025-12-18 10:52:32', '2025-12-18 10:52:32'),
(185, 13, 139, 3045, '2025-12-18', '', '2025-12-18 10:54:57', '2025-12-18 10:54:57'),
(186, 13, 3, 1428, '2025-12-18', '', '2025-12-18 10:55:07', '2025-12-18 10:55:07'),
(187, 13, 13, 3365, '2025-12-18', '', '2025-12-18 10:55:19', '2025-12-18 10:55:19'),
(188, 13, 16, 3777, '2025-12-18', '', '2025-12-18 10:55:30', '2025-12-18 10:55:30'),
(189, 13, 25, 2652, '2025-12-18', '', '2025-12-18 10:55:41', '2025-12-18 10:55:41'),
(190, 13, 26, 2100, '2025-12-18', '', '2025-12-18 10:55:49', '2025-12-18 10:55:49'),
(191, 13, 29, 1192, '2025-12-18', '', '2025-12-18 10:55:58', '2025-12-18 10:55:58'),
(192, 13, 34, 2246, '2025-12-18', '', '2025-12-18 10:56:09', '2025-12-18 10:56:09'),
(193, 13, 35, 616, '2025-12-18', '', '2025-12-18 10:56:17', '2025-12-18 10:56:17'),
(194, 13, 39, 6640, '2025-12-18', '', '2025-12-18 10:56:32', '2025-12-18 10:56:32'),
(195, 13, 42, 1350, '2025-12-18', '', '2025-12-18 10:56:41', '2025-12-18 10:56:41'),
(196, 13, 44, 593, '2025-12-18', '', '2025-12-18 10:56:50', '2025-12-18 10:56:50'),
(197, 13, 48, 3250, '2025-12-18', '', '2025-12-18 10:57:00', '2025-12-18 10:57:00'),
(198, 13, 51, 640, '2025-12-18', '', '2025-12-18 10:57:11', '2025-12-18 10:57:11'),
(199, 13, 54, 820, '2025-12-18', '', '2025-12-18 10:57:19', '2025-12-18 10:57:19'),
(200, 14, 3, 3000, '2025-12-18', '', '2025-12-18 11:00:05', '2025-12-18 11:00:05'),
(201, 14, 5, 22559, '2025-12-18', '', '2025-12-18 11:00:31', '2025-12-18 11:00:31'),
(202, 14, 6, 15000, '2025-12-18', '', '2025-12-18 11:00:58', '2025-12-18 11:00:58'),
(203, 14, 13, 8000, '2025-12-18', '', '2025-12-18 11:01:09', '2025-12-18 11:01:09'),
(204, 14, 16, 5000, '2025-12-18', '', '2025-12-18 11:01:27', '2025-12-18 11:01:27'),
(205, 14, 25, 1000, '2025-12-18', '', '2025-12-18 11:01:56', '2025-12-18 11:01:56'),
(206, 14, 26, 1000, '2025-12-18', '', '2025-12-18 11:02:04', '2025-12-18 11:02:04'),
(207, 14, 29, 2000, '2025-12-18', '', '2025-12-18 11:02:14', '2025-12-18 11:02:14'),
(208, 14, 42, 573, '2025-12-18', '', '2025-12-18 11:02:27', '2025-12-18 11:02:27'),
(209, 14, 68, 8110, '2025-12-18', '', '2025-12-18 11:02:38', '2025-12-18 11:02:38'),
(210, 16, 138, 3965, '2025-12-18', '', '2025-12-18 11:10:02', '2025-12-18 11:10:02'),
(211, 16, 139, 4450, '2025-12-18', '', '2025-12-18 11:10:21', '2025-12-18 11:10:21'),
(212, 16, 4, 8062, '2025-12-18', '', '2025-12-18 11:10:36', '2025-12-18 11:10:36'),
(213, 16, 16, 22063, '2025-12-18', '', '2025-12-18 11:10:47', '2025-12-18 11:10:47'),
(214, 16, 17, 19600, '2025-12-18', '', '2025-12-18 11:11:04', '2025-12-18 11:11:04'),
(215, 16, 25, 4326, '2025-12-18', '', '2025-12-18 11:11:16', '2025-12-18 11:11:16'),
(216, 16, 26, 25116, '2025-12-18', '', '2025-12-18 11:11:26', '2025-12-18 11:11:26'),
(217, 16, 29, 26865, '2025-12-18', '', '2025-12-18 11:11:51', '2025-12-18 11:11:51'),
(218, 16, 32, 2500, '2025-12-18', '', '2025-12-18 11:12:01', '2025-12-18 11:12:01'),
(219, 16, 34, 14255, '2025-12-18', '', '2025-12-18 11:12:13', '2025-12-18 11:12:13'),
(220, 16, 35, 2840, '2025-12-18', '', '2025-12-18 11:12:22', '2025-12-18 11:12:22'),
(221, 16, 36, 3525, '2025-12-18', '', '2025-12-18 11:12:31', '2025-12-18 11:12:31'),
(222, 16, 39, 2217, '2025-12-18', '', '2025-12-18 11:12:42', '2025-12-18 11:12:42'),
(223, 16, 42, 2380, '2025-12-18', '', '2025-12-18 11:13:00', '2025-12-18 11:13:00'),
(224, 16, 46, 9255, '2025-12-18', '', '2025-12-18 11:13:10', '2025-12-18 11:13:10'),
(225, 16, 73, 9885, '2025-12-18', '', '2025-12-18 11:13:25', '2025-12-18 11:13:25'),
(226, 16, 74, 26112, '2025-12-18', '', '2025-12-18 11:13:34', '2025-12-18 11:13:34'),
(227, 16, 76, 2694, '2025-12-18', '', '2025-12-18 11:13:44', '2025-12-18 11:13:44'),
(228, 16, 77, 1400, '2025-12-18', '', '2025-12-18 11:13:52', '2025-12-18 11:13:52'),
(229, 16, 79, 5800, '2025-12-18', '', '2025-12-18 11:14:07', '2025-12-18 11:14:07'),
(230, 16, 80, 3000, '2025-12-18', '', '2025-12-18 11:14:17', '2025-12-18 11:14:17'),
(231, 17, 4, 2000, '2025-12-18', '', '2025-12-18 14:32:40', '2025-12-18 14:32:40'),
(232, 17, 36, 950, '2025-12-18', '', '2025-12-18 14:32:56', '2025-12-18 14:32:56'),
(233, 17, 38, 1800, '2025-12-18', '', '2025-12-18 14:34:30', '2025-12-18 14:34:30'),
(234, 17, 39, 7000, '2025-12-18', '', '2025-12-18 14:34:43', '2025-12-18 14:34:43'),
(235, 18, 3, 1090, '2025-12-18', '', '2025-12-18 14:37:57', '2025-12-18 14:37:57'),
(236, 18, 26, 4705, '2025-12-18', '', '2025-12-18 14:38:12', '2025-12-18 14:38:12'),
(237, 18, 29, 2820, '2025-12-18', '', '2025-12-18 14:38:22', '2025-12-18 14:38:22'),
(238, 18, 31, 29, '2025-12-18', '', '2025-12-18 14:38:30', '2025-12-18 14:38:30'),
(239, 19, 139, 1000, '2025-12-18', '', '2025-12-18 14:41:07', '2025-12-18 14:41:07'),
(240, 19, 10, 1035, '2025-12-18', '', '2025-12-18 14:41:27', '2025-12-18 14:41:27'),
(241, 19, 29, 135, '2025-12-18', '', '2025-12-18 14:41:37', '2025-12-18 14:41:37'),
(242, 20, 6, 2500, '2025-12-18', '', '2025-12-18 14:44:19', '2025-12-18 14:44:19'),
(243, 20, 10, 2500, '2025-12-18', '', '2025-12-18 14:44:34', '2025-12-18 14:44:34'),
(244, 20, 14, 1000, '2025-12-18', '', '2025-12-18 14:44:44', '2025-12-18 14:44:44'),
(245, 20, 16, 8000, '2025-12-18', '', '2025-12-18 14:44:53', '2025-12-18 14:44:53'),
(246, 20, 25, 1000, '2025-12-18', '', '2025-12-18 14:45:06', '2025-12-18 14:45:06'),
(247, 20, 32, 1000, '2025-12-18', '', '2025-12-18 14:45:18', '2025-12-18 14:45:18'),
(248, 20, 44, 3000, '2025-12-18', '', '2025-12-18 14:45:28', '2025-12-18 14:45:28'),
(249, 20, 48, 1300, '2025-12-18', '', '2025-12-18 14:45:43', '2025-12-18 14:45:43'),
(250, 20, 49, 8000, '2025-12-18', '', '2025-12-18 14:45:56', '2025-12-18 14:45:56'),
(251, 20, 53, 1000, '2025-12-18', '', '2025-12-18 15:00:22', '2025-12-18 15:00:22'),
(252, 20, 56, 4000, '2025-12-18', '', '2025-12-18 15:00:50', '2025-12-18 15:00:50'),
(253, 21, 138, 609, '2025-12-18', '', '2025-12-18 15:12:43', '2025-12-18 15:12:43'),
(254, 21, 4, 2730, '2025-12-18', '', '2025-12-18 15:12:54', '2025-12-18 15:12:54'),
(255, 21, 5, 43804, '2025-12-18', '', '2025-12-18 15:13:10', '2025-12-18 15:13:10'),
(256, 21, 6, 2075, '2025-12-18', '', '2025-12-18 15:13:22', '2025-12-18 15:13:22'),
(257, 21, 7, 15894, '2025-12-18', '', '2025-12-18 15:13:59', '2025-12-18 15:13:59'),
(258, 21, 10, 1000, '2026-01-28', '', '2025-12-18 15:14:12', '2026-01-28 03:59:46'),
(259, 21, 11, 6493, '2025-12-18', '', '2025-12-18 15:14:26', '2025-12-18 15:14:26'),
(260, 21, 13, 10669, '2025-12-18', '', '2025-12-18 15:14:37', '2025-12-18 15:14:37'),
(261, 21, 16, 3000, '2025-12-18', '', '2025-12-18 15:14:46', '2025-12-18 15:14:46'),
(262, 21, 25, 200, '2025-12-18', '', '2025-12-18 15:14:54', '2025-12-18 15:14:54'),
(263, 21, 30, 300, '2025-12-18', '', '2025-12-18 15:15:01', '2025-12-18 15:15:01'),
(264, 21, 29, 16135, '2025-12-18', '', '2025-12-18 15:15:11', '2025-12-18 15:15:11'),
(265, 21, 32, 27578, '2025-12-18', '', '2025-12-18 15:15:25', '2025-12-18 15:15:25'),
(266, 21, 34, 4103, '2025-12-18', '', '2025-12-18 15:15:35', '2025-12-18 15:15:35'),
(267, 21, 35, 200, '2025-12-18', '', '2025-12-18 15:15:46', '2025-12-18 15:15:46'),
(268, 21, 37, 15894, '2025-12-18', '', '2025-12-18 15:16:00', '2025-12-18 15:16:00'),
(269, 21, 42, 500, '2025-12-18', '', '2025-12-18 15:16:11', '2025-12-18 15:16:11'),
(270, 21, 44, 5000, '2025-12-18', '', '2025-12-18 15:16:19', '2025-12-18 15:16:19'),
(271, 21, 48, 16009, '2025-12-18', '', '2025-12-18 15:16:32', '2025-12-18 15:16:32'),
(272, 21, 56, 40267, '2025-12-18', '', '2025-12-18 15:16:44', '2025-12-18 15:16:44'),
(273, 21, 59, 1101, '2025-12-18', '', '2025-12-18 15:16:54', '2025-12-18 15:16:54'),
(274, 21, 60, 7019, '2025-12-18', '', '2025-12-18 15:17:04', '2025-12-18 15:17:04'),
(275, 21, 65, 1366, '2025-12-18', '', '2025-12-18 15:17:14', '2025-12-18 15:17:14'),
(276, 21, 66, 1317, '2025-12-18', '', '2025-12-18 15:17:25', '2025-12-18 15:17:25'),
(277, 21, 1, 2101, '2025-12-18', '', '2025-12-18 15:17:35', '2025-12-18 15:17:35'),
(278, 21, 68, 5185, '2025-12-18', '', '2025-12-18 15:17:48', '2025-12-18 15:17:48'),
(279, 21, 69, 1006, '2025-12-18', '', '2025-12-18 15:17:57', '2025-12-18 15:17:57'),
(280, 21, 70, 2044, '2025-12-18', '', '2025-12-18 15:18:07', '2025-12-18 15:18:07'),
(281, 21, 71, 1525, '2025-12-18', '', '2025-12-18 15:18:20', '2025-12-18 15:18:20'),
(282, 21, 75, 500, '2025-12-18', '', '2025-12-18 15:21:22', '2025-12-18 15:21:22'),
(283, 21, 78, 10000, '2025-12-18', '', '2025-12-18 15:21:31', '2025-12-18 15:21:31'),
(284, 22, 139, 880, '2026-01-28', '', '2025-12-22 07:14:09', '2026-01-28 02:45:37'),
(285, 22, 6, 2675, '2025-12-22', '', '2025-12-22 07:14:26', '2025-12-22 07:14:26'),
(286, 22, 7, 6764, '2025-12-22', '', '2025-12-22 07:14:50', '2025-12-22 07:14:50'),
(287, 22, 13, 187472, '2025-12-22', '', '2025-12-22 07:15:59', '2025-12-22 07:15:59'),
(288, 22, 17, 14860, '2025-12-22', '', '2025-12-22 07:16:28', '2025-12-22 07:16:28'),
(289, 22, 26, 76658, '2025-12-22', '', '2025-12-22 07:16:59', '2025-12-22 07:16:59'),
(290, 22, 30, 1000, '2025-12-22', '', '2025-12-22 07:18:22', '2025-12-22 07:18:22'),
(291, 22, 29, 16539, '2025-12-22', '', '2025-12-22 07:18:40', '2025-12-22 07:18:40'),
(292, 22, 32, 3589, '2025-12-22', '', '2025-12-22 07:18:59', '2025-12-22 07:18:59'),
(293, 22, 33, 495, '2025-12-22', '', '2025-12-22 07:19:11', '2025-12-22 07:19:11'),
(294, 22, 35, 4828, '2025-12-22', '', '2025-12-22 07:19:57', '2025-12-22 07:19:57'),
(295, 22, 44, 21100, '2026-01-28', '', '2025-12-22 07:20:46', '2026-01-28 04:06:37'),
(296, 22, 84, 328, '2025-12-22', '', '2025-12-22 07:21:05', '2025-12-22 07:21:05'),
(297, 22, 87, 10190, '2025-12-22', '', '2025-12-22 07:21:20', '2025-12-22 07:21:20'),
(298, 22, 89, 990, '2026-01-28', '', '2025-12-22 07:21:36', '2026-01-28 02:41:48'),
(299, 22, 90, 833, '2025-12-22', '', '2025-12-22 07:21:47', '2025-12-22 07:21:47'),
(300, 23, 139, 6000, '2025-12-22', '', '2025-12-22 07:27:35', '2025-12-22 07:27:35'),
(301, 23, 4, 2000, '2025-12-22', '', '2025-12-22 07:27:52', '2025-12-22 07:27:52'),
(302, 23, 6, 6000, '2025-12-22', '', '2025-12-22 07:28:23', '2025-12-22 07:28:23'),
(303, 23, 13, 60000, '2025-12-22', '', '2025-12-22 07:28:50', '2025-12-22 07:28:50'),
(304, 23, 16, 97000, '2025-12-22', '', '2025-12-22 07:29:05', '2025-12-22 07:29:05'),
(305, 23, 24, 3500, '2025-12-22', '', '2025-12-22 07:29:17', '2025-12-22 07:29:17'),
(306, 23, 26, 15000, '2025-12-22', '', '2025-12-22 07:29:31', '2025-12-22 07:29:31'),
(307, 23, 27, 3500, '2025-12-22', '', '2025-12-22 07:29:41', '2025-12-22 07:29:41'),
(308, 23, 33, 6000, '2025-12-22', '', '2025-12-22 07:30:00', '2025-12-22 07:30:00'),
(309, 23, 38, 204000, '2025-12-22', '', '2025-12-22 07:30:15', '2025-12-22 07:30:15'),
(310, 23, 42, 6000, '2025-12-22', '', '2025-12-22 07:30:26', '2025-12-22 07:30:26'),
(311, 23, 44, 3500, '2025-12-22', '', '2025-12-22 07:30:45', '2025-12-22 07:30:45'),
(312, 23, 48, 3781, '2025-12-22', '', '2025-12-22 07:30:59', '2025-12-22 07:30:59'),
(313, 23, 57, 5500, '2025-12-22', '', '2025-12-22 07:31:13', '2025-12-22 07:31:13'),
(314, 23, 58, 409, '2025-12-22', '', '2025-12-22 07:31:27', '2025-12-22 07:31:27'),
(315, 23, 61, 39, '2025-12-22', '', '2025-12-22 07:31:38', '2025-12-22 07:31:38'),
(316, 23, 107, 3500, '2025-12-22', '', '2025-12-22 07:31:54', '2025-12-22 07:31:54'),
(317, 23, 118, 3500, '2025-12-22', '', '2025-12-22 07:32:05', '2025-12-22 07:32:05'),
(318, 24, 5, 2878, '2025-12-22', '', '2025-12-22 07:35:17', '2025-12-22 07:35:17'),
(319, 24, 29, 2990, '2026-02-02', '', '2025-12-22 07:35:35', '2026-02-02 04:14:51'),
(320, 24, 34, 1240, '2025-12-22', '', '2025-12-22 07:35:53', '2025-12-22 07:35:53'),
(321, 24, 39, 272050, '2025-12-22', '', '2025-12-22 07:36:11', '2025-12-22 07:36:11'),
(322, 24, 40, 144650, '2025-12-22', '', '2025-12-22 07:36:23', '2025-12-22 07:36:23'),
(323, 24, 41, 1500, '2025-12-22', '', '2025-12-22 07:36:38', '2025-12-22 07:36:38'),
(324, 24, 78, 1000, '2025-12-22', '', '2025-12-22 07:36:51', '2025-12-22 07:36:51'),
(325, 24, 98, 1895, '2026-02-02', '', '2025-12-22 07:37:05', '2026-02-02 04:14:51'),
(326, 24, 128, 2195, '2026-02-02', '', '2025-12-22 07:37:22', '2026-02-02 04:14:51'),
(327, 26, 3, 433, '2025-12-22', '', '2025-12-22 07:52:19', '2025-12-22 07:52:19'),
(328, 26, 4, 12292, '2025-12-22', '', '2025-12-22 07:52:33', '2025-12-22 07:52:33'),
(329, 26, 6, 133, '2025-12-22', '', '2025-12-22 07:52:42', '2025-12-22 07:52:42'),
(330, 26, 24, 525, '2025-12-22', '', '2025-12-22 07:52:56', '2025-12-22 07:52:56'),
(331, 26, 26, 472, '2025-12-22', '', '2025-12-22 07:53:08', '2025-12-22 07:53:08'),
(332, 26, 28, 250, '2025-12-22', '', '2025-12-22 07:53:17', '2025-12-22 07:53:17'),
(333, 26, 34, 963, '2025-12-22', '', '2025-12-22 07:54:19', '2025-12-22 07:54:19'),
(334, 26, 35, 790, '2025-12-22', '', '2025-12-22 07:54:32', '2025-12-22 07:54:32'),
(335, 26, 44, 316, '2025-12-22', '', '2025-12-22 07:54:45', '2025-12-22 07:54:45'),
(336, 26, 46, 3565, '2025-12-22', '', '2025-12-22 07:55:01', '2025-12-22 07:55:15'),
(337, 26, 54, 482, '2025-12-22', '', '2025-12-22 07:55:30', '2025-12-22 07:55:30'),
(338, 26, 65, 373, '2025-12-22', '', '2025-12-22 07:55:42', '2025-12-22 07:55:42'),
(339, 26, 83, 310, '2025-12-22', '', '2025-12-22 07:55:56', '2025-12-22 07:55:56'),
(340, 26, 99, 5463, '2025-12-22', '', '2025-12-22 07:56:10', '2025-12-22 07:56:10'),
(341, 26, 117, 603, '2025-12-22', '', '2025-12-22 07:56:20', '2025-12-22 07:56:20'),
(342, 26, 119, 1086, '2025-12-22', '', '2025-12-22 07:56:39', '2025-12-22 07:56:39'),
(343, 26, 120, 5025, '2025-12-22', '', '2025-12-22 07:56:51', '2025-12-22 07:57:24'),
(344, 26, 122, 6516, '2025-12-22', '', '2025-12-22 07:57:43', '2025-12-22 07:57:43'),
(345, 27, 139, 1500, '2025-12-22', '', '2025-12-22 08:03:29', '2025-12-22 08:03:29'),
(346, 27, 3, 5000, '2025-12-22', '', '2025-12-22 08:03:36', '2025-12-22 08:03:36'),
(347, 27, 13, 5000, '2025-12-22', '', '2025-12-22 08:03:49', '2025-12-22 08:03:49'),
(348, 27, 16, 4125, '2025-12-22', '', '2025-12-22 08:04:02', '2025-12-22 08:04:02'),
(349, 27, 26, 1200, '2025-12-22', '', '2025-12-22 08:04:12', '2025-12-22 08:04:12'),
(350, 27, 28, 1100, '2025-12-22', '', '2025-12-22 08:04:22', '2025-12-22 08:04:33'),
(351, 27, 39, 5000, '2025-12-22', '', '2025-12-22 08:04:49', '2025-12-22 08:04:49'),
(352, 27, 46, 3000, '2025-12-22', '', '2025-12-22 08:05:00', '2025-12-22 08:05:00'),
(353, 27, 50, 3000, '2025-12-22', '', '2025-12-22 08:05:15', '2025-12-22 08:05:15'),
(354, 27, 1, 1000, '2025-12-22', '', '2025-12-22 08:05:27', '2025-12-22 08:05:27'),
(355, 27, 67, 700, '2025-12-22', '', '2025-12-22 08:05:32', '2025-12-22 08:05:32'),
(356, 27, 76, 15500, '2025-12-22', '', '2025-12-22 08:05:45', '2025-12-22 08:05:45'),
(357, 27, 83, 1000, '2025-12-22', '', '2025-12-22 08:05:59', '2025-12-22 08:05:59'),
(358, 27, 126, 4000, '2025-12-22', '', '2025-12-22 08:06:14', '2025-12-22 08:06:14'),
(359, 29, 3, 6000, '2025-12-22', '', '2025-12-22 08:29:57', '2025-12-22 08:29:57'),
(360, 29, 4, 3000, '2025-12-22', '', '2025-12-22 08:30:08', '2025-12-22 08:30:08'),
(361, 29, 13, 1000, '2025-12-22', '', '2025-12-22 08:30:19', '2025-12-22 08:30:19'),
(362, 29, 26, 3000, '2025-12-22', '', '2025-12-22 08:30:30', '2025-12-22 08:30:30'),
(363, 29, 28, 5000, '2025-12-22', '', '2025-12-22 08:30:49', '2025-12-22 08:30:49'),
(364, 29, 29, 1000, '2025-12-22', '', '2025-12-22 08:31:07', '2025-12-22 08:31:07'),
(365, 29, 32, 1500, '2025-12-22', '', '2025-12-22 08:31:15', '2025-12-22 08:31:15'),
(366, 29, 34, 2000, '2025-12-22', '', '2025-12-22 08:31:26', '2025-12-22 08:31:51'),
(367, 29, 39, 3000, '2025-12-22', '', '2025-12-22 08:32:12', '2025-12-22 08:32:12'),
(368, 29, 76, 2500, '2025-12-22', '', '2025-12-22 08:32:27', '2025-12-22 08:32:27'),
(369, 29, 85, 10000, '2025-12-22', '', '2025-12-22 08:32:44', '2025-12-22 08:32:44'),
(370, 29, 86, 3000, '2025-12-22', '', '2025-12-22 08:33:01', '2025-12-22 08:33:01'),
(371, 30, 139, 2370, '2025-12-22', '', '2025-12-22 08:38:08', '2025-12-22 08:38:08'),
(372, 30, 3, 1956, '2025-12-22', '', '2025-12-22 08:38:23', '2025-12-22 08:38:23'),
(373, 30, 11, 4593, '2025-12-22', '', '2025-12-22 08:38:42', '2025-12-22 08:38:42'),
(374, 30, 15, 1000, '2025-12-22', '', '2025-12-22 08:38:51', '2025-12-22 08:38:51'),
(375, 30, 26, 3394, '2025-12-22', '', '2025-12-22 08:39:21', '2025-12-22 08:39:21'),
(376, 30, 29, 2062, '2025-12-22', '', '2025-12-22 08:39:35', '2025-12-22 08:39:35'),
(377, 30, 32, 685, '2025-12-22', '', '2025-12-22 08:39:48', '2025-12-22 08:39:48'),
(378, 30, 35, 363, '2025-12-22', '', '2025-12-22 08:40:00', '2025-12-22 08:40:00'),
(379, 30, 39, 4400, '2025-12-22', '', '2025-12-22 08:40:11', '2025-12-22 08:40:11'),
(380, 30, 46, 1477, '2025-12-22', '', '2025-12-22 08:40:29', '2025-12-22 08:40:29'),
(381, 30, 49, 6010, '2025-12-22', '', '2025-12-22 08:44:46', '2025-12-22 08:44:46'),
(382, 30, 76, 4215, '2025-12-22', '', '2025-12-22 08:45:15', '2025-12-22 08:45:15'),
(383, 30, 80, 590, '2025-12-22', '', '2025-12-22 08:45:28', '2025-12-22 08:45:28'),
(384, 30, 83, 4325, '2025-12-22', '', '2025-12-22 08:45:43', '2025-12-22 08:45:43'),
(385, 30, 85, 739, '2025-12-22', '', '2025-12-22 08:45:54', '2025-12-22 08:45:54'),
(386, 32, 25, 11000, '2025-12-22', '', '2025-12-22 08:52:00', '2025-12-22 08:52:00'),
(387, 32, 26, 870, '2025-12-22', '', '2025-12-22 08:52:08', '2025-12-22 08:52:08'),
(388, 32, 44, 5673, '2025-12-22', '', '2025-12-22 08:52:21', '2025-12-22 08:52:21'),
(389, 33, 26, 830, '2025-12-22', '', '2025-12-22 09:03:16', '2025-12-22 09:03:16'),
(390, 33, 28, 1105, '2025-12-22', '', '2025-12-22 09:04:11', '2025-12-22 09:04:11'),
(391, 33, 39, 10000, '2025-12-22', '', '2025-12-22 09:04:24', '2025-12-22 09:04:24'),
(392, 33, 42, 1200, '2025-12-22', '', '2025-12-22 09:04:36', '2025-12-22 09:04:36'),
(393, 33, 46, 135728, '2025-12-22', '', '2025-12-22 09:04:51', '2025-12-22 09:04:51'),
(394, 33, 63, 1400, '2025-12-22', '', '2025-12-22 09:05:01', '2025-12-22 09:05:01'),
(395, 33, 66, 965, '2025-12-22', '', '2025-12-22 09:05:11', '2025-12-22 09:05:11'),
(396, 33, 76, 7056, '2025-12-22', '', '2025-12-22 09:05:23', '2025-12-22 09:05:23'),
(397, 33, 89, 1148, '2025-12-22', '', '2025-12-22 09:05:55', '2025-12-22 09:05:55'),
(398, 33, 93, 940, '2026-01-28', '', '2025-12-22 09:06:04', '2026-01-28 04:38:54'),
(399, 33, 106, 2000, '2025-12-22', '', '2025-12-22 09:06:13', '2025-12-22 09:06:13'),
(400, 33, 107, 1095, '2025-12-22', '', '2025-12-22 09:06:23', '2025-12-22 09:06:23'),
(401, 33, 108, 1010, '2025-12-22', '', '2025-12-22 09:06:32', '2025-12-22 09:06:32'),
(402, 34, 39, 1000, '2025-12-22', '', '2025-12-22 09:10:41', '2025-12-22 09:10:41'),
(403, 34, 76, 10275, '2025-12-22', '', '2025-12-22 09:11:04', '2025-12-22 09:11:04'),
(404, 35, 139, 4000, '2025-12-22', '', '2025-12-22 09:14:07', '2025-12-22 09:14:07'),
(405, 35, 3, 5750, '2025-12-22', '', '2025-12-22 09:14:19', '2025-12-22 09:14:19'),
(406, 35, 4, 14000, '2025-12-22', '', '2025-12-22 09:14:28', '2025-12-22 09:14:28'),
(407, 35, 6, 11743, '2025-12-22', '', '2025-12-22 09:14:42', '2025-12-22 09:14:42'),
(408, 35, 17, 45500, '2025-12-22', '', '2025-12-22 09:14:53', '2025-12-22 09:14:53'),
(409, 35, 26, 8032, '2025-12-22', '', '2025-12-22 09:15:06', '2025-12-22 09:15:06'),
(410, 35, 30, 1060, '2025-12-22', '', '2025-12-22 09:15:15', '2025-12-22 09:15:15'),
(411, 35, 32, 2000, '2025-12-22', '', '2025-12-22 09:15:24', '2025-12-22 09:15:24'),
(412, 35, 34, 1000, '2025-12-22', '', '2025-12-22 09:15:31', '2025-12-22 09:15:31'),
(413, 35, 35, 14012, '2025-12-22', '', '2025-12-22 09:15:43', '2025-12-22 09:15:43'),
(414, 35, 36, 762, '2025-12-22', '', '2025-12-22 09:15:50', '2025-12-22 09:15:50'),
(415, 35, 39, 4000, '2025-12-22', '', '2025-12-22 09:16:01', '2025-12-22 09:16:01'),
(416, 35, 42, 17800, '2026-01-28', '', '2025-12-22 09:16:26', '2026-01-28 03:35:20'),
(417, 35, 53, 5000, '2025-12-22', '', '2025-12-22 09:16:38', '2025-12-22 09:16:38'),
(418, 35, 54, 350, '2025-12-22', '', '2025-12-22 09:16:49', '2025-12-22 09:16:49'),
(419, 35, 76, 100, '2025-12-22', '', '2025-12-22 09:17:01', '2025-12-22 09:17:01'),
(420, 35, 82, 3000, '2025-12-22', '', '2025-12-22 09:17:10', '2025-12-22 09:17:10'),
(421, 35, 83, 4682, '2025-12-22', '', '2025-12-22 09:17:23', '2025-12-22 09:17:23'),
(422, 35, 89, 8100, '2025-12-22', '', '2025-12-22 09:17:32', '2025-12-22 09:17:32'),
(423, 35, 91, 12500, '2025-12-22', '', '2025-12-22 09:17:39', '2025-12-22 09:17:39'),
(424, 35, 100, 1500, '2025-12-22', '', '2025-12-22 09:17:52', '2025-12-22 09:17:52'),
(425, 35, 121, 3000, '2025-12-22', '', '2025-12-22 09:18:04', '2025-12-22 09:18:04'),
(426, 35, 122, 18977, '2025-12-22', '', '2025-12-22 09:18:15', '2025-12-22 09:18:15'),
(427, 35, 125, 17487, '2025-12-22', '', '2025-12-22 09:18:35', '2025-12-22 09:18:35'),
(428, 36, 3, 2000, '2025-12-22', '', '2025-12-22 13:32:15', '2025-12-22 13:32:15'),
(429, 36, 5, 1000, '2025-12-22', '', '2025-12-22 13:32:23', '2025-12-22 13:32:23'),
(430, 36, 13, 4000, '2025-12-22', '', '2025-12-22 13:32:32', '2025-12-22 13:32:32'),
(431, 36, 16, 4000, '2025-12-22', '', '2025-12-22 13:32:41', '2025-12-22 13:32:41'),
(432, 36, 17, 11000, '2025-12-22', '', '2025-12-22 13:32:49', '2025-12-22 13:32:49'),
(433, 36, 26, 3000, '2025-12-22', '', '2025-12-22 13:32:58', '2025-12-22 13:32:58'),
(434, 36, 29, 1000, '2025-12-22', '', '2025-12-22 13:33:10', '2025-12-22 13:33:10'),
(435, 36, 32, 3000, '2025-12-22', '', '2025-12-22 13:33:25', '2025-12-22 13:33:25'),
(436, 36, 34, 1000, '2025-12-22', '', '2025-12-22 13:33:35', '2025-12-22 13:33:35'),
(437, 36, 42, 5000, '2025-12-22', '', '2025-12-22 13:33:44', '2025-12-22 13:33:44'),
(438, 36, 44, 1000, '2025-12-22', '', '2025-12-22 13:33:53', '2025-12-22 13:33:53'),
(439, 36, 73, 15000, '2025-12-22', '', '2025-12-22 13:34:07', '2025-12-22 13:34:07'),
(440, 37, 139, 47439, '2026-01-30', '', '2025-12-22 13:38:15', '2026-01-30 09:49:42'),
(441, 37, 3, 95000, '2025-12-22', '', '2025-12-22 13:38:33', '2025-12-22 13:38:33'),
(442, 37, 4, 132199, '2025-12-22', '', '2025-12-22 13:38:44', '2025-12-22 13:38:44'),
(443, 37, 10, 79453, '2025-12-22', '', '2025-12-22 13:38:58', '2025-12-22 13:38:58'),
(444, 37, 14, 38987, '2025-12-22', '', '2025-12-22 13:39:10', '2025-12-22 13:39:10'),
(445, 37, 15, 25000, '2025-12-22', '', '2025-12-22 13:39:23', '2025-12-22 13:39:23'),
(446, 37, 18, 25200, '2025-12-22', '', '2025-12-22 13:39:34', '2025-12-22 13:39:34'),
(447, 37, 25, 31188, '2025-12-22', '', '2025-12-22 13:39:46', '2025-12-22 13:39:46'),
(448, 37, 26, 27000, '2025-12-22', '', '2025-12-22 13:39:55', '2025-12-22 13:39:55'),
(449, 37, 30, 2000, '2025-12-22', '', '2025-12-22 13:40:03', '2025-12-22 13:40:03'),
(450, 37, 32, 48131, '2025-12-22', '', '2025-12-22 13:40:14', '2025-12-22 13:40:14'),
(451, 37, 34, 5000, '2025-12-22', '', '2025-12-22 13:40:23', '2025-12-22 13:40:23'),
(452, 37, 35, 25000, '2025-12-22', '', '2025-12-22 13:40:32', '2025-12-22 13:40:32'),
(453, 37, 42, 2000, '2025-12-22', '', '2025-12-22 13:40:42', '2025-12-22 13:40:42'),
(454, 37, 44, 64945, '2025-12-22', '', '2025-12-22 13:40:52', '2025-12-22 13:40:52'),
(455, 37, 56, 53791, '2025-12-22', '', '2025-12-22 13:41:03', '2025-12-22 13:41:03'),
(456, 37, 59, 1, '2026-01-28', '', '2025-12-22 13:41:14', '2026-01-28 01:18:14'),
(457, 37, 65, 300, '2026-01-27', '', '2025-12-22 13:41:23', '2026-01-27 09:24:06'),
(458, 37, 60, 5250, '2025-12-22', '', '2025-12-22 13:42:29', '2025-12-22 13:42:29'),
(459, 37, 1, 73000, '2025-12-22', '', '2025-12-22 13:42:38', '2025-12-22 13:42:51'),
(460, 37, 68, 10000, '2025-12-22', '', '2025-12-22 13:42:59', '2025-12-22 13:42:59'),
(461, 37, 82, 805, '2025-12-22', '', '2025-12-22 13:43:13', '2025-12-22 13:43:13'),
(462, 37, 83, 99000, '2025-12-22', '', '2025-12-22 13:43:25', '2025-12-22 13:43:25'),
(463, 37, 85, 28000, '2025-12-22', '', '2025-12-22 13:43:36', '2025-12-22 13:43:36'),
(464, 37, 89, 8000, '2025-12-22', '', '2025-12-22 13:43:46', '2025-12-22 13:43:46'),
(465, 37, 110, 59639, '2025-12-22', '', '2025-12-22 13:44:03', '2025-12-22 13:44:03'),
(466, 37, 126, 43313, '2025-12-22', '', '2025-12-22 13:44:16', '2025-12-22 13:44:16'),
(467, 37, 127, 6896, '2025-12-22', '', '2025-12-22 13:44:28', '2025-12-22 13:44:28'),
(468, 38, 4, 1990, '2025-12-22', '', '2025-12-22 13:48:15', '2025-12-22 13:48:15'),
(469, 38, 17, 115, '2025-12-22', '', '2025-12-22 13:48:24', '2025-12-22 13:48:24'),
(470, 38, 25, 2635, '2025-12-22', '', '2025-12-22 13:48:34', '2025-12-22 13:48:34'),
(471, 38, 32, 644, '2025-12-22', '', '2025-12-22 13:48:44', '2025-12-22 13:48:44'),
(472, 38, 34, 910, '2025-12-22', '', '2025-12-22 13:48:53', '2025-12-22 13:48:53'),
(473, 38, 59, 11920, '2025-12-22', '', '2025-12-22 13:49:05', '2025-12-22 13:49:05'),
(474, 38, 65, 308, '2025-12-22', '', '2025-12-22 13:49:15', '2025-12-22 13:49:15'),
(475, 38, 66, 115, '2025-12-22', '', '2025-12-22 13:49:22', '2025-12-22 13:49:22'),
(476, 38, 81, 8890, '2025-12-22', '', '2025-12-22 13:49:37', '2025-12-22 13:49:37'),
(477, 38, 82, 3425, '2025-12-22', '', '2025-12-22 13:49:45', '2025-12-22 13:49:45'),
(478, 38, 87, 105, '2025-12-22', '', '2025-12-22 13:49:56', '2025-12-22 13:49:56'),
(479, 38, 89, 64, '2025-12-22', '', '2025-12-22 13:50:04', '2025-12-22 13:50:04'),
(480, 38, 93, 2640, '2025-12-22', '', '2025-12-22 13:50:14', '2025-12-22 13:50:14'),
(481, 38, 110, 525, '2025-12-22', '', '2025-12-22 13:50:24', '2025-12-22 13:50:24'),
(482, 38, 114, 1500, '2025-12-22', '', '2025-12-22 13:50:35', '2025-12-22 13:50:35'),
(483, 38, 115, 1550, '2025-12-22', '', '2025-12-22 13:50:47', '2025-12-22 13:50:47'),
(484, 38, 121, 290, '2025-12-22', '', '2025-12-22 13:50:57', '2025-12-22 13:50:57'),
(485, 38, 129, 1765, '2025-12-22', '', '2025-12-22 13:51:07', '2025-12-22 13:51:07'),
(486, 38, 130, 45, '2025-12-22', '', '2025-12-22 13:51:15', '2025-12-22 13:51:15'),
(487, 38, 131, 255, '2025-12-22', '', '2025-12-22 13:51:25', '2025-12-22 13:51:25'),
(488, 38, 132, 200, '2025-12-22', '', '2025-12-22 13:51:34', '2025-12-22 13:51:34'),
(489, 38, 133, 36, '2025-12-22', '', '2025-12-22 13:51:44', '2025-12-22 13:51:44'),
(490, 38, 134, 511, '2025-12-22', '', '2025-12-22 13:51:51', '2025-12-22 13:51:51'),
(491, 38, 135, 1210, '2025-12-22', '', '2025-12-22 13:52:04', '2025-12-22 13:52:04'),
(492, 38, 136, 185, '2025-12-22', '', '2025-12-22 13:52:13', '2025-12-22 13:52:13'),
(493, 38, 137, 380, '2025-12-22', '', '2025-12-22 13:52:31', '2025-12-22 13:52:31');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `nik` varchar(16) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `role` enum('admin','bpdas','public') NOT NULL DEFAULT 'public',
  `bpdas_id` int(11) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `full_name`, `phone`, `nik`, `address`, `role`, `bpdas_id`, `is_active`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@seedling-dashboard.id', '$2y$10$H9DKr/4.LwdrKnaECK8QWepmHRo5wGWnAevm1SptiWhX14C2Ort1i', 'System Administrator', '081315168705', NULL, NULL, 'admin', NULL, 1, '2025-12-22 15:26:01', '2025-12-18 08:06:01', '2025-12-23 01:14:03'),
(2, 'EkoWasiaji', 'dit.ppth@kehutanan.go.id', '$2y$10$QOX4mlqCGCK2MzLYqB4WdOUTU.KTc/Wfz.Xbu.97EL5PkKQT3j.56', 'Eko Wasiaji', '081377280080', NULL, NULL, 'bpdas', 1, 1, '2026-01-29 15:23:23', '2025-12-18 08:17:19', '2026-01-29 15:23:23'),
(3, 'AsriSitumorang', 'dit.ppth@kehutanan1.go.id', '$2y$10$dF796oqirbg6IdBQHfZ5FevufmfLy.RwOU/bvSJdTLxdGOrv8ph3q', 'Asri Situmorang', '081361596207', NULL, NULL, 'bpdas', 2, 1, '2026-01-30 08:37:59', '2025-12-18 09:30:06', '2026-01-30 08:37:59'),
(4, 'Waskadi', 'dit.ppth2@kehutanan.go.id', '$2y$10$kopwBIgiS4itBEJeB5ZtYuKkHny1fckFpeV1QmCIWwOHldgJL6Ei2', 'Waskadi', '081366192171', NULL, NULL, 'bpdas', 3, 1, '2025-12-18 09:41:33', '2025-12-18 09:40:54', '2025-12-18 09:41:33'),
(5, 'EnjenJaenal', 'dit.ppth3@kehutanan.go.id', '$2y$10$.6YjKAkbQAW8t.D9qPSnN.Xpe9vt.J.pAKbVjKslsLHqxsWdsJPkK', 'Enjen Jaenal', '085780207180', NULL, NULL, 'bpdas', 4, 1, '2026-01-30 04:17:01', '2025-12-18 09:49:36', '2026-01-30 04:17:01'),
(6, 'Yulia', 'dit.ppth5@kehutanan.go.id', '$2y$10$8b59b2AcMXfxZVqDj4m01ew0Y10u3BZhZLf1YWZEe.OtvOhEhjbnC', 'Yulia', '085282467706', NULL, NULL, 'bpdas', 5, 1, '2025-12-18 09:53:49', '2025-12-18 09:53:39', '2025-12-18 09:53:49'),
(7, 'Syafii', 'dit.ppth6@kehutanan.go.id', '$2y$10$nRKmANmUiV4Hmara4VqMVuFcTzGWR.2UH1IbZzvWnp1PAsCRzZ/mu', 'Syafii', '081357278228', NULL, NULL, 'bpdas', 6, 1, '2025-12-18 10:02:05', '2025-12-18 10:01:49', '2025-12-18 10:02:05'),
(8, 'Yolanda', 'dit.ppth7@kehutanan.go.id', '$2y$10$4T2QY6YR6MdllhBWCNvWBetoaA1ZL1wwCCtMkjbjg8yXhVwCCiIVG', 'Yolanda', '081247421632', NULL, NULL, 'bpdas', 7, 1, '2025-12-18 10:07:16', '2025-12-18 10:07:08', '2025-12-18 10:07:16'),
(9, 'Jhen', 'dit.ppth8@kehutanan.go.id', '$2y$10$oDePE2vRNXJgokmVsGaSbO2Tq5RwqB/XGb9sxrTkdghAF1blSoHdW', 'Jhen', '085817269445', NULL, NULL, 'bpdas', 8, 1, '2025-12-18 10:12:51', '2025-12-18 10:12:40', '2025-12-18 10:12:51'),
(10, 'EdiKurniawan', 'dit.ppth9@kehutanan.go.id', '$2y$10$UzOPHgsc/bn4eJzvSTEl1uhkGl5J6DVN.OrYi5N0LqVQ9sOaKznQe', 'Edi Kurniawan', '081355527772', NULL, NULL, 'bpdas', 9, 1, '2025-12-22 08:49:18', '2025-12-18 10:36:18', '2025-12-22 08:49:18'),
(11, 'Jumadi', 'dit.ppth10@kehutanan.go.id', '$2y$10$3RIdpDFLmY9iMUEMtVvsDeX4FQ9NhNkldx.VY3sN8zXFUwWJEHPP.', 'Jumadi', '081248147351', NULL, NULL, 'bpdas', 10, 1, '2025-12-18 10:40:00', '2025-12-18 10:39:49', '2025-12-18 10:40:00'),
(12, 'AnggiSiregar', 'dit.ppth11@kehutanan.go.id', '$2y$10$biUQTVUhUkiihvZZQeR5o.5u5xx8RA6ATplT6B/j0ycEcZh7DowSK', 'Anggi Siregar', '085275179373', NULL, NULL, 'bpdas', 11, 1, '2025-12-18 10:45:10', '2025-12-18 10:45:00', '2025-12-18 10:45:10'),
(13, 'Slamet Riadi', 'dit.ppth12@kehutanan.go.id', '$2y$10$gGJkR9Nd4c2rwLPt3ifFIuckr6X5Ksqr8ZEOx8eRY9zCRtcb8F9iu', 'Slamet Riadi', '085263904206', NULL, NULL, 'bpdas', 12, 1, '2025-12-18 10:50:00', '2025-12-18 10:49:42', '2025-12-18 10:50:00'),
(14, 'Aswan', 'dit.ppth13@kehutanan.go.id', '$2y$10$TzGi6TUc2JvG95Z0Xtbxpelz1HaytDvMQcBOArU8Gm6KYKyewQeha', 'Aswan', '082284099338', NULL, NULL, 'bpdas', 13, 1, '2025-12-18 10:54:32', '2025-12-18 10:54:22', '2025-12-18 10:54:32'),
(15, 'Johnson', 'dit.ppth14@kehutanan.go.id', '$2y$10$Frm602REEBMt0cLlmWLG4uiWDrg0K9pyvPpS2ZhO1DpGnJcGaloSi', 'Johnson', '081268371974', NULL, NULL, 'bpdas', 14, 1, '2025-12-18 10:59:33', '2025-12-18 10:59:21', '2025-12-18 10:59:33'),
(16, 'BpdasMusi', 'dit.ppth16@kehutanan.go.id', '$2y$10$GMycXgcY903JF.ccFvocquq.0RLgNDFtKCIZIV9qK5QI7PFzDhRia', 'BpdasMusi', '-', NULL, NULL, 'bpdas', 15, 1, NULL, '2025-12-18 11:04:40', '2025-12-18 11:04:40'),
(17, 'Marsudi', 'dit.ppth17@kehutanan.go.id', '$2y$10$raG/qSH6CYtA.Od5pd6y0eO9bhN/uSjg72RkH.f58v05eO3RjrY.O', 'Marsudi', '081368237185', NULL, NULL, 'bpdas', 16, 1, '2025-12-18 11:07:24', '2025-12-18 11:07:10', '2025-12-18 11:07:24'),
(18, 'artaddictz', 'fadhlanegsa@gmail.com', '$2y$10$Q532xGuLWL9PN52p8CZsEOPlkLkLAJmv7I9Lagp1CMf.iKrA4jGqK', 'Fadhlan Egsa', '081315168705', '3674062509920002', NULL, 'public', NULL, 1, '2026-01-28 02:39:06', '2025-12-18 12:21:17', '2026-01-28 02:39:06'),
(19, 'indra', 'dit.ppth18@kehutanan.go.id', '$2y$10$7m7FLthw619v7WHgJ1IMZOoq2QQQzWBmccZ/dsGsUcLcfgxoP7D5i', 'Indra', '081217181757', NULL, NULL, 'bpdas', 17, 1, '2025-12-18 14:32:11', '2025-12-18 14:31:54', '2025-12-18 14:32:11'),
(20, 'Momo', 'dit.ppth19@kehutanan.go.id', '$2y$10$dLUdPwYHXZHxglJhhWdRmOXWsV26gswPQrYCNT1YdLPXy/gb3OVY.', 'Momo', '082182775048', NULL, NULL, 'bpdas', 18, 1, '2025-12-18 14:37:24', '2025-12-18 14:37:15', '2025-12-18 14:37:24'),
(21, 'Ida', 'dit.ppth20@kehutanan.go.id', '$2y$10$Mbq8qJ6hVs05bRquAldHXOHVpNHGi.jl4I.x6244kTIS.yiM6jp1u', 'Ida', '081312499137', NULL, NULL, 'bpdas', 19, 1, '2025-12-22 15:26:43', '2025-12-18 14:40:26', '2025-12-22 15:26:43'),
(22, 'Marjuki', 'dit.ppth21@kehutanan.go.id', '$2y$10$hbAbwubZWGfnHqFRQFKtruGRIJyNTgjQ6ztnfjiwXecF1uKzNz45i', 'Marjuki', '081248149931', NULL, NULL, 'bpdas', 20, 1, '2025-12-18 14:43:56', '2025-12-18 14:43:35', '2025-12-18 14:43:56'),
(23, 'TaufikRahmadi', 'dit.ppth23@kehutanan.go.id', '$2y$10$FZNP4SfZnU49QaYjVpd5Ie.b4q8tLZUJa3C2O0ke5BnIyQPXcrWpi', 'Taufik Rahmadi', '081391979028', NULL, NULL, 'bpdas', 21, 1, '2026-01-28 03:57:06', '2025-12-18 15:12:05', '2026-01-28 03:57:06'),
(24, 'Syahid', 'dit.ppth22@kehutanan.go.id', '$2y$10$4wUUzPZngc.R3B4ByBR.pO7NaAHrFYgTQrr9MNAkyFjhhvAURVEqu', 'Syahid', '081253482283', NULL, NULL, 'bpdas', 22, 1, '2026-01-28 04:03:35', '2025-12-22 07:12:26', '2026-01-28 04:03:35'),
(25, 'Hendra', 'dit.ppth24@kehutanan.go.id', '$2y$10$v3x5yKzdHG2V7sO3v89rLeMA.y/ZEAfzJQU5SO9QDyP4jDc5umLv.', 'Hendra', '081253155562', NULL, NULL, 'bpdas', 23, 1, '2025-12-22 15:28:32', '2025-12-22 07:26:13', '2025-12-22 15:28:32'),
(26, 'Ade', 'dit.ppth25@kehutanan.go.id', '$2y$10$ViFYHcVnPsNurOozMar7MudqSigb/tUi8NB4.jTBaHfJC9jS5xPra', 'Ade', '085337274871', NULL, NULL, 'bpdas', 24, 1, '2026-02-02 04:15:56', '2025-12-22 07:34:29', '2026-02-02 04:15:56'),
(27, 'Hafiz', 'dit.ppth26@kehutanan.go.id', '$2y$10$h3xD79xtHN6YGWADkw9kIummUw4jPtdfWLHLiFUCkGgP4e8R4.q36', 'Hafiz', '085337087423', NULL, NULL, 'bpdas', 25, 1, '2025-12-23 08:24:02', '2025-12-22 07:41:18', '2025-12-23 08:24:02'),
(28, 'Piter', 'dit.ppth27@kehutanan.go.id', '$2y$10$uG7NzTbUB3Mui/fdojDNIOfWqW.owbtBNjhv4zD4.j8eTdpYkwHJa', 'Piter', '081339338574', NULL, NULL, 'bpdas', 26, 1, '2025-12-22 07:51:56', '2025-12-22 07:51:39', '2025-12-22 07:51:56'),
(29, 'Johanis', 'dit.ppth28@kehutanan.go.id', '$2y$10$pnMFvLZefq0mXoXuZvTXt.AC9RBkbo.wE2jGNX3uoeKvSiOJ1hZxa', 'Johanis', '081354665748', NULL, NULL, 'bpdas', 27, 1, '2025-12-22 08:09:19', '2025-12-22 08:01:44', '2025-12-22 08:09:19'),
(30, 'JajangWahyudin', 'dit.ppth29@kehutanan.go.id', '$2y$10$m2DsNJ/t96x3dYsrYh1zeuNzo1jR33uiGQwutUaO2BFiR/NUQZXoG', 'Jajang Wahyudin', '082346307307', NULL, NULL, 'bpdas', 29, 1, '2025-12-22 08:27:23', '2025-12-22 08:26:32', '2025-12-22 08:27:23'),
(31, 'Wajir', 'dit.ppth30@kehutanan.go.id', '$2y$10$7gi0IFXOh4Xx7Ho0H9BbGuZd3fWJpzwWUbnPC3PGy68UIP8loToeO', 'Wajir', '085256171771', NULL, NULL, 'bpdas', 30, 1, '2025-12-22 08:36:26', '2025-12-22 08:36:19', '2025-12-22 08:36:26'),
(32, 'Arnold', 'dit.ppth31@kehutanan.go.id', '$2y$10$4xGwE1lSHKHx/MWAZuPYtOLjfvsIvviQ2A..hRLMWC//xCRKgKg6a', 'Arnold', '08114583473', NULL, NULL, 'bpdas', 32, 1, '2026-01-27 04:06:06', '2025-12-22 08:51:13', '2026-01-27 04:06:06'),
(33, 'AndiRusdi', 'dit.ppth32@kehutanan.go.id', '$2y$10$g7HEpbKXIcplRj8VBYwTDupVS7WYFP98O49zd4dkRpsJsxrcwOgGi', 'Andi Rusdi', '085243754501', NULL, NULL, 'bpdas', 33, 1, '2026-01-28 04:38:29', '2025-12-22 08:55:28', '2026-01-28 04:38:29'),
(34, 'Yusril', 'dit.ppth33@kehutanan.go.id', '$2y$10$tDj1wj3uOJqEfmCIwUmti.8Z/qFzN4stlQAf4MOiV9jhhJaNhpWdO', 'Yusril', '081224744996', NULL, NULL, 'bpdas', 34, 1, '2025-12-22 09:10:13', '2025-12-22 09:10:01', '2025-12-22 09:10:13'),
(35, 'Imelda', 'dit.ppth34@kehutanan.go.id', '$2y$10$/Q/h1D4rQ1mTCqpOP0le3.QVdZVVNKlDA6qP7Mdmceo75r7z9dRL6', 'Imelda', '081219206907', NULL, NULL, 'bpdas', 35, 1, '2026-01-28 03:35:08', '2025-12-22 09:13:31', '2026-01-28 03:35:08'),
(36, 'BPTH1', 'dit.ppth35@kehutanan.go.id', '$2y$10$US4e5s4Tt3H7q4f9iuoquOrIhD0yjBDKjOnTHNlvDwgFRRWxgR2VO', 'Lukmedi', '081379414225', NULL, NULL, 'bpdas', 36, 1, '2025-12-22 13:31:55', '2025-12-22 13:31:36', '2025-12-22 13:31:55'),
(37, 'BPTH2', 'dit.ppth36@kehutanan.go.id', '$2y$10$ig6l0GMN34SzqfXTniU5vOJ/bxFyykd7aLCwfCTEADmqu6aq8hq2K', 'Rathna', '085298595018', NULL, NULL, 'bpdas', 37, 1, '2026-01-30 09:49:33', '2025-12-22 13:36:00', '2026-01-30 09:49:33'),
(38, 'BPTH3', 'dit.ppth38@kehutanan.go.id', '$2y$10$KeX8JpA0JnTaLSAjbYAFMOoQOOtPcB2a6XhAPkgSsxQvX22mm4Uq.', 'Hamda', '087812822925', NULL, NULL, 'bpdas', 38, 1, '2026-02-02 04:57:34', '2025-12-22 13:47:32', '2026-02-02 04:57:34'),
(39, 'tester1', 'tester@gmail.com', '$2y$10$XYI92NJ8ADaoh.UfcGvjZOSV8/OVduuBeEWQr/jE8DRN2acyz1FWG', 'James Bond', '0813234567890', '1234567891011122', NULL, 'public', NULL, 1, '2026-01-27 03:52:34', '2025-12-22 14:45:55', '2026-01-27 03:52:34'),
(41, 'yono1', 'yuno@gmail.com', '$2y$10$5uW1LqudefK0/AUjzoc5Z.4fG8/1Z5a.HbLKx/lO2q0tympTy3wTy', 'yono', '097654345627', '1234567890333333', NULL, 'admin', NULL, 1, '2026-02-02 12:43:44', '2025-12-23 01:24:48', '2026-02-02 12:43:44'),
(42, 'Esnandar', 'esnandarrembang@gmail.com', '$2y$10$UD8QC4zkZctYAOJyNQB39O5gy6pHoVwvw1HFQgksxmdCIWjcH4qAu', 'Esnandar', '082179377515', '3317032908940001', NULL, 'public', NULL, 1, '2026-01-27 05:40:39', '2026-01-27 05:36:06', '2026-01-27 05:40:39'),
(43, 'tester3', 'tester3@gmail.com', '$2y$10$vNf28ek1.z4AdqJ2zKmw.OfFlkozYXK0CXBsttVIV4vlydgf1auki', 'tester3', '08131568722', '5678292819102391', NULL, 'public', NULL, 1, '2026-02-02 04:57:30', '2026-01-28 02:44:17', '2026-02-02 04:57:30'),
(44, 'linda1', 'linda@gmail.com', '$2y$10$QOX4mlqCGCK2MzLYqB4WdOUTU.KTc/Wfz.Xbu.97EL5PkKQT3j.56', 'Linda Aryani Aziz', '081315168729', '6728297292038292', NULL, 'public', NULL, 1, '2026-01-29 15:13:43', '2026-01-29 15:02:01', '2026-01-29 15:13:43'),
(45, 'testerbpdas', 'testerbpdas@gmail.com', '$2y$10$fVa78MFonfltF/n6tYbeg.IqxZGtWkSZmALoWp8FmjtiSOnKKigeS', 'Tester BPDAS', '0877128391028', NULL, NULL, 'bpdas', 4, 1, '2026-01-30 04:55:41', '2026-01-29 15:29:01', '2026-01-30 04:55:41'),
(46, 'Artaadictzzz', 'fadhlan_egsa@yahoo.com', '$2y$10$BOfjvH18LK7hVFexr6Nn9.wSTa98m3GeJXYrv0rcopL1GvILxRoPm', 'Fadhlan Egsa', '081315168704', '3674062509920002', NULL, 'public', NULL, 1, '2026-01-29 16:08:38', '2026-01-29 16:08:00', '2026-01-29 16:08:38'),
(47, 'VINAPPTH', 'trianavina6@gmail.com', '$2y$10$Q2s44Aty0RrFH57jus9ANOO8N1kB68/3jjnrLfW2RLc95.dJscKY2', 'Vina Triana', '08975127320', '3320066802980006', NULL, 'public', NULL, 1, '2026-01-30 08:36:17', '2026-01-30 07:42:38', '2026-01-30 08:36:17'),
(48, 'muna', 'hmunawaroh.pth@gmail.com', '$2y$10$qYn5Drswcv.ggGlmlatQP.tMGMTOBXzZo9MEtsn8gO90fNp5zcrkO', 'MUNA', '0872323', '1213326463856511', NULL, 'public', NULL, 1, '2026-01-30 08:04:17', '2026-01-30 08:04:12', '2026-01-30 08:04:17');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `bpdas`
--
ALTER TABLE `bpdas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_province` (`province_id`),
  ADD KEY `idx_active` (`is_active`);

--
-- Indeks untuk tabel `provinces`
--
ALTER TABLE `provinces`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indeks untuk tabel `requests`
--
ALTER TABLE `requests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `request_number` (`request_number`),
  ADD KEY `seedling_type_id` (`seedling_type_id`),
  ADD KEY `approved_by` (`approved_by`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_bpdas` (`bpdas_id`),
  ADD KEY `idx_request_number` (`request_number`),
  ADD KEY `idx_coordinates` (`latitude`,`longitude`),
  ADD KEY `idx_proposal` (`proposal_file_path`),
  ADD KEY `idx_delivery_photo` (`delivery_photo_path`);

--
-- Indeks untuk tabel `request_history`
--
ALTER TABLE `request_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `changed_by` (`changed_by`),
  ADD KEY `idx_request` (`request_id`);

--
-- Indeks untuk tabel `request_items`
--
ALTER TABLE `request_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_request` (`request_id`),
  ADD KEY `idx_seedling_type` (`seedling_type_id`);

--
-- Indeks untuk tabel `seedling_types`
--
ALTER TABLE `seedling_types`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_name` (`name`);

--
-- Indeks untuk tabel `stock`
--
ALTER TABLE `stock`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_stock` (`bpdas_id`,`seedling_type_id`),
  ADD KEY `idx_bpdas` (`bpdas_id`),
  ADD KEY `idx_seedling` (`seedling_type_id`),
  ADD KEY `idx_quantity` (`quantity`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_role` (`role`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_username` (`username`),
  ADD KEY `fk_users_bpdas` (`bpdas_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `bpdas`
--
ALTER TABLE `bpdas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT untuk tabel `provinces`
--
ALTER TABLE `provinces`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT untuk tabel `requests`
--
ALTER TABLE `requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT untuk tabel `request_history`
--
ALTER TABLE `request_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT untuk tabel `request_items`
--
ALTER TABLE `request_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT untuk tabel `seedling_types`
--
ALTER TABLE `seedling_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=140;

--
-- AUTO_INCREMENT untuk tabel `stock`
--
ALTER TABLE `stock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=494;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `bpdas`
--
ALTER TABLE `bpdas`
  ADD CONSTRAINT `bpdas_ibfk_1` FOREIGN KEY (`province_id`) REFERENCES `provinces` (`id`);

--
-- Ketidakleluasaan untuk tabel `requests`
--
ALTER TABLE `requests`
  ADD CONSTRAINT `requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `requests_ibfk_2` FOREIGN KEY (`bpdas_id`) REFERENCES `bpdas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `requests_ibfk_3` FOREIGN KEY (`seedling_type_id`) REFERENCES `seedling_types` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `requests_ibfk_4` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `request_history`
--
ALTER TABLE `request_history`
  ADD CONSTRAINT `request_history_ibfk_1` FOREIGN KEY (`request_id`) REFERENCES `requests` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `request_history_ibfk_2` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `request_items`
--
ALTER TABLE `request_items`
  ADD CONSTRAINT `request_items_ibfk_1` FOREIGN KEY (`request_id`) REFERENCES `requests` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `request_items_ibfk_2` FOREIGN KEY (`seedling_type_id`) REFERENCES `seedling_types` (`id`);

--
-- Ketidakleluasaan untuk tabel `stock`
--
ALTER TABLE `stock`
  ADD CONSTRAINT `stock_ibfk_1` FOREIGN KEY (`bpdas_id`) REFERENCES `bpdas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_ibfk_2` FOREIGN KEY (`seedling_type_id`) REFERENCES `seedling_types` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_bpdas` FOREIGN KEY (`bpdas_id`) REFERENCES `bpdas` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
