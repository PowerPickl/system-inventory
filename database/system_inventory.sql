-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 04, 2025 at 04:14 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `system_inventory`
--

-- --------------------------------------------------------

--
-- Table structure for table `barang`
--

CREATE TABLE `barang` (
  `id_barang` bigint(20) UNSIGNED NOT NULL,
  `id_kategori` bigint(20) UNSIGNED DEFAULT NULL,
  `kode_barang_old` varchar(50) DEFAULT NULL,
  `kode_barang` varchar(50) DEFAULT NULL,
  `sequence_number` int(11) NOT NULL DEFAULT 1,
  `nama_barang` varchar(255) NOT NULL,
  `merk` varchar(100) DEFAULT NULL,
  `model_tipe` varchar(100) DEFAULT NULL,
  `satuan` varchar(255) NOT NULL,
  `harga_beli` decimal(10,2) NOT NULL,
  `harga_jual` decimal(10,2) NOT NULL,
  `reorder_point` int(11) NOT NULL,
  `annual_demand` decimal(10,2) DEFAULT NULL,
  `ordering_cost` decimal(10,2) DEFAULT 5000.00,
  `holding_cost` decimal(10,2) DEFAULT NULL,
  `demand_avg_daily` decimal(8,2) DEFAULT NULL,
  `demand_max_daily` decimal(8,2) DEFAULT NULL,
  `eoq_qty` int(11) DEFAULT NULL,
  `eoq_calculated` int(11) DEFAULT NULL,
  `safety_stock` int(11) DEFAULT NULL,
  `rop_calculated` int(11) DEFAULT NULL,
  `last_eoq_calculation` timestamp NULL DEFAULT NULL,
  `eoq_notes` text DEFAULT NULL,
  `lead_time` int(11) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `keterangan_detail` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `barang`
--

INSERT INTO `barang` (`id_barang`, `id_kategori`, `kode_barang_old`, `kode_barang`, `sequence_number`, `nama_barang`, `merk`, `model_tipe`, `satuan`, `harga_beli`, `harga_jual`, `reorder_point`, `annual_demand`, `ordering_cost`, `holding_cost`, `demand_avg_daily`, `demand_max_daily`, `eoq_qty`, `eoq_calculated`, `safety_stock`, `rop_calculated`, `last_eoq_calculation`, `eoq_notes`, `lead_time`, `deskripsi`, `keterangan_detail`, `created_at`, `updated_at`) VALUES
(1, 1, 'RAD001', 'FLD-0003', 3, 'Radiator Coolant Texal', NULL, NULL, 'pcs', 40100.00, 45000.00, 3, 365.00, 5000.00, 10.00, 1.00, 2.00, 30, 30, 1, 3, '2025-07-14 20:32:04', NULL, 2, 'Manual EOQ: 30, ROP: 2, SS: 2', NULL, '2025-07-04 08:44:55', '2025-07-14 20:32:04'),
(2, 1, 'OIL001', 'FLD-0004', 4, 'Minyak Rem Prestone', NULL, NULL, 'pcs', 28000.00, 32000.00, 7, 1095.00, 5000.00, 10.00, 3.00, 4.00, 63, 63, 1, 7, '2025-07-14 20:32:39', NULL, 2, 'Manual EOQ: 63, ROP: 6, SS: 2', NULL, '2025-07-04 08:44:55', '2025-07-14 20:32:39'),
(3, 1, 'OIL002', 'FLD-0002', 2, 'Minyak Rem Jumbo', NULL, NULL, 'pcs', 26000.00, 30000.00, 8, 1095.00, 5000.00, 10.00, 3.00, 4.00, 65, 65, 2, 8, '2025-07-23 21:17:13', NULL, 2, '300 ML', NULL, '2025-07-04 08:44:55', '2025-07-23 21:17:13'),
(4, 6, 'CLN001', 'CSM-0001', 1, 'Carbon Cleaner', NULL, NULL, 'pcs', 32500.00, 37000.00, 15, 1460.00, 5000.00, 10.00, 4.00, 5.00, 67, 67, 3, 15, '2025-07-29 05:54:32', NULL, 3, '500 ML', NULL, '2025-07-04 08:44:55', '2025-07-29 05:54:32'),
(5, 6, 'CLN002', 'CSM-0002', 2, 'Injector Cleaner', NULL, NULL, 'pcs', 28000.00, 32000.00, 15, 1460.00, 5000.00, 10.00, 4.00, 5.00, 72, 72, 3, 15, '2025-07-27 21:11:10', NULL, 3, '300 ML', NULL, '2025-07-04 08:44:55', '2025-07-27 21:11:10'),
(6, 6, 'OIL003', 'CSM-0003', 3, 'WD-40', NULL, NULL, 'pcs', 55000.00, 62000.00, 2, 120.00, 5000.00, 10.00, 0.33, 1.00, 15, 15, 1, 2, '2025-07-14 20:31:56', NULL, 3, 'Manual EOQ: 15, ROP: 1, SS: 2', NULL, '2025-07-04 08:44:55', '2025-07-14 20:31:56'),
(11, 1, 'OIL004', 'FLD-0005', 5, 'Prima XP 30w 50', NULL, NULL, 'pcs', 152520.00, 250000.00, 10, 494.00, 5000.00, 10.00, 3.00, 5.00, NULL, 18, 4, 10, '2025-07-23 21:17:29', NULL, 2, '4 liter', NULL, '2025-07-13 21:39:50', '2025-07-23 21:17:29'),
(12, 1, 'OIL005', 'FLD-0001', 1, 'Fastron eco green 5w 30', NULL, NULL, 'pcs', 175500.00, 250000.00, 7, 144.00, 5000.00, 10.00, 2.00, 4.00, NULL, 9, 3, 7, '2025-07-14 20:29:21', NULL, 2, '3.5 Liter', NULL, '2025-07-14 17:44:57', '2025-07-14 20:29:21'),
(15, 5, NULL, 'BDY-0001', 1, 'Ban', 'Toyota', 'KR0-1', 'pcs', 800000.00, 1000000.00, 1, 40.00, 100000.00, 10.00, 0.11, 0.20, NULL, 10, 0, 1, '2025-07-15 08:54:00', NULL, 7, NULL, NULL, '2025-07-15 08:10:50', '2025-07-15 08:54:00'),
(16, 4, NULL, 'ELC-0001', 1, 'Aki', 'Yuasa', 'CC12', 'pcs', 350000.00, 500000.00, 0, 24.00, 60000.00, 10.00, 0.07, 0.15, NULL, 9, 0, 0, '2025-07-15 08:16:28', NULL, 4, NULL, NULL, '2025-07-15 08:15:25', '2025-07-15 08:16:28'),
(17, 1, NULL, 'FLD-0006', 6, 'Filter Oli Toyota Avanza/Xenia', 'Toyota', NULL, 'pcs', 35000.00, 55000.00, 20, 960.00, 75000.00, 25.00, 2.66, 4.00, NULL, 128, 7, 20, '2025-07-27 21:10:55', NULL, 5, NULL, NULL, '2025-07-21 19:43:18', '2025-07-27 21:10:55'),
(18, 1, NULL, 'FLD-0007', 7, 'Oli Mesin SAE 10W-40', 'SAE', NULL, 'liter', 45000.00, 65000.00, 42, 1440.00, 50000.00, 20.00, 4.00, 6.00, NULL, 126, 14, 42, '2025-07-27 21:10:43', NULL, 7, NULL, '1 Liter', '2025-07-21 19:46:41', '2025-07-27 21:10:43');

-- --------------------------------------------------------

--
-- Table structure for table `barang_backup`
--

CREATE TABLE `barang_backup` (
  `id_barang` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `id_kategori` bigint(20) UNSIGNED DEFAULT NULL,
  `kode_barang` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_internal` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sequence_number` int(11) NOT NULL DEFAULT 1,
  `nama_barang` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `merk` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `model_tipe` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `satuan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `harga_beli` decimal(10,2) NOT NULL,
  `harga_jual` decimal(10,2) NOT NULL,
  `reorder_point` int(11) NOT NULL,
  `annual_demand` decimal(10,2) DEFAULT NULL,
  `ordering_cost` decimal(10,2) NOT NULL DEFAULT 5000.00,
  `holding_cost` decimal(10,2) DEFAULT NULL,
  `demand_avg_daily` decimal(8,2) DEFAULT NULL,
  `demand_max_daily` decimal(8,2) DEFAULT NULL,
  `eoq_qty` int(11) DEFAULT NULL,
  `eoq_calculated` int(11) DEFAULT NULL,
  `safety_stock` int(11) DEFAULT NULL,
  `rop_calculated` int(11) DEFAULT NULL,
  `last_eoq_calculation` timestamp NULL DEFAULT NULL,
  `eoq_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lead_time` int(11) DEFAULT NULL,
  `deskripsi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keterangan_detail` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `barang_backup`
--

INSERT INTO `barang_backup` (`id_barang`, `id_kategori`, `kode_barang`, `kode_internal`, `sequence_number`, `nama_barang`, `merk`, `model_tipe`, `satuan`, `harga_beli`, `harga_jual`, `reorder_point`, `annual_demand`, `ordering_cost`, `holding_cost`, `demand_avg_daily`, `demand_max_daily`, `eoq_qty`, `eoq_calculated`, `safety_stock`, `rop_calculated`, `last_eoq_calculation`, `eoq_notes`, `lead_time`, `deskripsi`, `keterangan_detail`, `created_at`, `updated_at`) VALUES
(1, 1, 'RAD001', 'FLD-0003', 3, 'Radiator Coolant Texal', NULL, NULL, 'pcs', 40100.00, 45000.00, 3, 365.00, 5000.00, 10.00, 1.00, 2.00, 30, 30, 1, 3, '2025-07-14 20:32:04', NULL, 2, 'Manual EOQ: 30, ROP: 2, SS: 2', NULL, '2025-07-04 08:44:55', '2025-07-14 20:32:04'),
(2, 1, 'OIL001', 'FLD-0004', 4, 'Minyak Rem Prestone', NULL, NULL, 'pcs', 28000.00, 32000.00, 7, 1095.00, 5000.00, 10.00, 3.00, 4.00, 63, 63, 1, 7, '2025-07-14 20:32:39', NULL, 2, 'Manual EOQ: 63, ROP: 6, SS: 2', NULL, '2025-07-04 08:44:55', '2025-07-14 20:32:39'),
(3, 1, 'OIL002', 'FLD-0002', 2, 'Minyak Rem Jumbo', NULL, NULL, 'pcs', 26000.00, 30000.00, 7, 1095.00, 5000.00, 10.00, 3.00, 4.00, 65, 65, 1, 7, '2025-07-14 20:31:44', NULL, 2, '300 ML', NULL, '2025-07-04 08:44:55', '2025-07-14 20:31:44'),
(4, 6, 'CLN001', 'CSM-0001', 1, 'Carbon Cleaner', NULL, NULL, 'pcs', 32500.00, 37000.00, 14, 1460.00, 5000.00, 10.00, 4.00, 5.00, 67, 67, 2, 14, '2025-07-14 20:29:32', NULL, 3, '500 ML', NULL, '2025-07-04 08:44:55', '2025-07-14 20:29:32'),
(5, 6, 'CLN002', 'CSM-0002', 2, 'Injector Cleaner', NULL, NULL, 'pcs', 28000.00, 32000.00, 14, 1460.00, 5000.00, 10.00, 4.00, 5.00, 72, 72, 2, 14, '2025-07-14 20:29:40', NULL, 3, '300 ML', NULL, '2025-07-04 08:44:55', '2025-07-14 20:29:40'),
(6, 6, 'OIL003', 'CSM-0003', 3, 'WD-40', NULL, NULL, 'pcs', 55000.00, 62000.00, 2, 120.00, 5000.00, 10.00, 0.33, 1.00, 15, 15, 1, 2, '2025-07-14 20:31:56', NULL, 3, 'Manual EOQ: 15, ROP: 1, SS: 2', NULL, '2025-07-04 08:44:55', '2025-07-14 20:31:56'),
(11, 1, 'OIL004', 'FLD-0005', 5, 'Prima XP 30w 50', NULL, NULL, 'pcs', 152520.00, 250000.00, 9, 494.00, 5000.00, 10.00, 3.00, 5.00, NULL, 18, 3, 9, '2025-07-14 20:33:22', NULL, 2, '4 liter', NULL, '2025-07-13 21:39:50', '2025-07-14 20:33:22'),
(12, 1, 'OIL005', 'FLD-0001', 1, 'Fastron eco green 5w 30', NULL, NULL, 'pcs', 175500.00, 250000.00, 7, 144.00, 5000.00, 10.00, 2.00, 4.00, NULL, 9, 3, 7, '2025-07-14 20:29:21', NULL, 2, '3.5 Liter', NULL, '2025-07-14 17:44:57', '2025-07-14 20:29:21');

-- --------------------------------------------------------

--
-- Table structure for table `barang_masuk`
--

CREATE TABLE `barang_masuk` (
  `id_masuk` bigint(20) UNSIGNED NOT NULL,
  `nomor_masuk` varchar(255) NOT NULL,
  `id_request` bigint(20) UNSIGNED DEFAULT NULL,
  `tanggal_masuk` datetime NOT NULL,
  `id_user_gudang` bigint(20) UNSIGNED NOT NULL,
  `supplier` varchar(255) DEFAULT NULL,
  `nomor_invoice` varchar(255) DEFAULT NULL,
  `total_nilai` decimal(15,2) DEFAULT NULL,
  `jenis_masuk` enum('Restock Request','Pembelian Manual','Return','Adjustment') NOT NULL DEFAULT 'Restock Request',
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `barang_masuk`
--

INSERT INTO `barang_masuk` (`id_masuk`, `nomor_masuk`, `id_request`, `tanggal_masuk`, `id_user_gudang`, `supplier`, `nomor_invoice`, `total_nilai`, `jenis_masuk`, `keterangan`, `created_at`, `updated_at`) VALUES
(1, 'BM20250709001', 13, '2025-07-09 14:33:00', 2, 'CR', NULL, 2071000.00, 'Restock Request', NULL, '2025-07-09 07:33:50', '2025-07-09 07:33:50'),
(2, 'BM20250709002', NULL, '2025-07-09 14:34:00', 2, 'CR', NULL, 55000.00, 'Pembelian Manual', NULL, '2025-07-09 07:35:24', '2025-07-09 07:35:24'),
(3, 'BM20250709003', NULL, '2025-07-09 14:35:00', 2, 'WE', NULL, 55000.00, 'Pembelian Manual', NULL, '2025-07-09 07:36:01', '2025-07-09 07:36:01'),
(4, 'BM20250709004', 12, '2025-07-09 15:11:00', 2, 'Jook', NULL, 2016000.00, 'Restock Request', NULL, '2025-07-09 08:12:45', '2025-07-09 08:12:45'),
(5, 'BM20250715001', 11, '2025-07-15 15:39:00', 2, 'Jookaa', 'ssss2331234', 2837500.00, 'Restock Request', 'aasda', '2025-07-15 08:39:47', '2025-07-15 08:39:47'),
(6, 'BM20250715002', NULL, '2025-07-15 15:41:00', 2, 'Jooks', 'wer112', 800000.00, 'Pembelian Manual', NULL, '2025-07-15 08:41:55', '2025-07-15 08:41:55'),
(7, 'BM20250716001', 15, '2025-07-16 14:41:00', 2, 'PR', 'SKR123', 1525200.00, 'Restock Request', 'ANJAI', '2025-07-16 07:42:13', '2025-07-16 07:42:13'),
(8, 'BM20250719001', 14, '2025-07-19 04:26:00', 2, 'Jongkok', 'SK2000', 1330160.00, 'Restock Request', 'Anjia Berhasil co', '2025-07-18 21:27:01', '2025-07-18 21:27:01'),
(9, 'BM20250724001', 16, '2025-07-24 13:27:00', 2, 'asda', 'asdad', 461040.00, 'Restock Request', 'asdasd', '2025-07-24 06:28:10', '2025-07-24 06:28:10'),
(10, 'BM20250725001', NULL, '2025-07-25 04:19:00', 2, 'SSSS', 'ssssss', 160000.00, 'Pembelian Manual', 'asdasd', '2025-07-24 21:19:58', '2025-07-24 21:19:58'),
(11, 'BM20250728001', 22, '2025-07-28 04:24:00', 2, 'asdas', 'asdasd', 540000.00, 'Restock Request', 'asdasd', '2025-07-27 21:24:49', '2025-07-27 21:24:50'),
(12, 'BM20250802001', 27, '2025-08-02 06:28:00', 2, 'hhh', 'jjj', 45000.00, 'Restock Request', 'kkk', '2025-08-01 23:28:41', '2025-08-01 23:28:41'),
(13, 'BM20250802002', 29, '2025-08-02 06:36:00', 2, 'hh', 'j', 1455000.00, 'Restock Request', 'j', '2025-08-01 23:37:10', '2025-08-01 23:37:10');

-- --------------------------------------------------------

--
-- Table structure for table `barang_masuk_detail`
--

CREATE TABLE `barang_masuk_detail` (
  `id_masuk_detail` bigint(20) UNSIGNED NOT NULL,
  `id_masuk` bigint(20) UNSIGNED NOT NULL,
  `id_barang` bigint(20) UNSIGNED NOT NULL,
  `qty_masuk` int(11) NOT NULL,
  `harga_beli_satuan` decimal(10,2) NOT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  `tanggal_expired` date DEFAULT NULL,
  `batch_number` varchar(255) DEFAULT NULL,
  `keterangan_detail` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `barang_masuk_detail`
--

INSERT INTO `barang_masuk_detail` (`id_masuk_detail`, `id_masuk`, `id_barang`, `qty_masuk`, `harga_beli_satuan`, `subtotal`, `tanggal_expired`, `batch_number`, `keterangan_detail`, `created_at`, `updated_at`) VALUES
(1, 1, 5, 72, 28000.00, 2016000.00, NULL, NULL, NULL, '2025-07-09 07:33:50', '2025-07-09 07:33:50'),
(2, 1, 6, 1, 55000.00, 55000.00, NULL, NULL, NULL, '2025-07-09 07:33:50', '2025-07-09 07:33:50'),
(3, 2, 6, 1, 55000.00, 55000.00, NULL, NULL, NULL, '2025-07-09 07:35:24', '2025-07-09 07:35:24'),
(4, 3, 6, 1, 55000.00, 55000.00, NULL, NULL, NULL, '2025-07-09 07:36:01', '2025-07-09 07:36:01'),
(5, 4, 5, 72, 28000.00, 2016000.00, NULL, NULL, NULL, '2025-07-09 08:12:45', '2025-07-09 08:12:45'),
(6, 5, 4, 67, 32500.00, 2177500.00, NULL, NULL, NULL, '2025-07-15 08:39:47', '2025-07-15 08:39:47'),
(7, 5, 6, 12, 55000.00, 660000.00, NULL, NULL, NULL, '2025-07-15 08:39:47', '2025-07-15 08:39:47'),
(8, 6, 15, 1, 800000.00, 800000.00, NULL, NULL, NULL, '2025-07-15 08:41:55', '2025-07-15 08:41:55'),
(9, 7, 11, 10, 152520.00, 1525200.00, NULL, NULL, NULL, '2025-07-16 07:42:13', '2025-07-16 07:42:13'),
(10, 8, 6, 2, 55000.00, 110000.00, NULL, NULL, NULL, '2025-07-18 21:27:01', '2025-07-18 21:27:01'),
(11, 8, 11, 8, 152520.00, 1220160.00, NULL, NULL, NULL, '2025-07-18 21:27:01', '2025-07-18 21:27:01'),
(12, 9, 3, 6, 26000.00, 156000.00, NULL, NULL, NULL, '2025-07-24 06:28:10', '2025-07-24 06:28:10'),
(13, 9, 11, 2, 152520.00, 305040.00, NULL, NULL, NULL, '2025-07-24 06:28:10', '2025-07-24 06:28:10'),
(14, 10, 17, 2, 35000.00, 70000.00, NULL, NULL, NULL, '2025-07-24 21:19:58', '2025-07-24 21:19:58'),
(15, 10, 18, 2, 45000.00, 90000.00, NULL, NULL, NULL, '2025-07-24 21:19:58', '2025-07-24 21:19:58'),
(16, 11, 18, 12, 45000.00, 540000.00, NULL, NULL, NULL, '2025-07-27 21:24:49', '2025-07-27 21:24:49'),
(17, 12, 18, 1, 45000.00, 45000.00, NULL, NULL, NULL, '2025-08-01 23:28:41', '2025-08-01 23:28:41'),
(18, 13, 6, 1, 55000.00, 55000.00, NULL, NULL, NULL, '2025-08-01 23:37:10', '2025-08-01 23:37:10'),
(19, 13, 17, 40, 35000.00, 1400000.00, NULL, NULL, NULL, '2025-08-01 23:37:10', '2025-08-01 23:37:10');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('laravel_cache_demand_distribution', 'O:29:\"Illuminate\\Support\\Collection\":2:{s:8:\"\0*\0items\";a:12:{i:0;d:2;i:1;d:3.3333333333333335;i:2;d:10;i:3;d:12;i:4;d:30.416666666666668;i:5;d:41.166666666666664;i:6;d:80;i:7;d:91.25;i:8;d:91.25;i:9;d:120;i:10;d:121.66666666666667;i:11;d:121.66666666666667;}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}', 1754234713);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `detail_transaksi`
--

CREATE TABLE `detail_transaksi` (
  `id_detail` bigint(20) UNSIGNED NOT NULL,
  `id_transaksi` bigint(20) UNSIGNED NOT NULL,
  `id_barang` bigint(20) UNSIGNED NOT NULL,
  `qty` int(11) NOT NULL,
  `harga_satuan` decimal(10,2) NOT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  `status_permintaan` enum('Pending','Approved','Rejected','Cancelled') NOT NULL DEFAULT 'Pending',
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `detail_transaksi`
--

INSERT INTO `detail_transaksi` (`id_detail`, `id_transaksi`, `id_barang`, `qty`, `harga_satuan`, `subtotal`, `status_permintaan`, `keterangan`, `created_at`, `updated_at`) VALUES
(1, 1, 3, 1, 30000.00, 30000.00, 'Approved', NULL, '2025-07-11 18:51:20', '2025-07-11 19:16:36'),
(2, 1, 5, 1, 32000.00, 32000.00, 'Approved', NULL, '2025-07-11 18:51:20', '2025-07-11 19:16:36'),
(3, 2, 4, 1, 37000.00, 37000.00, 'Rejected', NULL, '2025-07-11 19:18:02', '2025-07-11 19:19:07'),
(4, 2, 2, 1, 32000.00, 32000.00, 'Rejected', NULL, '2025-07-11 19:18:02', '2025-07-11 19:19:07'),
(5, 2, 6, 1, 62000.00, 62000.00, 'Rejected', NULL, '2025-07-11 19:18:02', '2025-07-11 19:19:07'),
(6, 3, 5, 1, 32000.00, 32000.00, 'Rejected', NULL, '2025-07-11 19:21:39', '2025-07-11 19:27:34'),
(7, 3, 4, 1, 37000.00, 37000.00, 'Approved', NULL, '2025-07-11 19:21:39', '2025-07-11 19:27:27'),
(8, 4, 11, 1, 250000.00, 250000.00, 'Approved', NULL, '2025-07-16 07:37:37', '2025-07-16 07:38:06'),
(9, 5, 12, 1, 250000.00, 250000.00, 'Rejected', NULL, '2025-07-18 21:31:25', '2025-07-18 21:32:13'),
(10, 5, 2, 1, 32000.00, 32000.00, 'Approved', NULL, '2025-07-18 21:31:25', '2025-07-18 21:32:15'),
(11, 6, 16, 1, 500000.00, 500000.00, 'Rejected', NULL, '2025-07-24 21:34:39', '2025-07-24 21:47:54'),
(12, 6, 15, 1, 1000000.00, 1000000.00, 'Approved', NULL, '2025-07-24 21:34:39', '2025-07-24 21:47:52'),
(13, 6, 4, 1, 37000.00, 37000.00, 'Approved', NULL, '2025-07-24 21:34:39', '2025-07-24 21:47:51'),
(14, 7, 16, 1, 500000.00, 500000.00, 'Approved', NULL, '2025-07-27 21:32:12', '2025-07-28 00:07:59'),
(15, 8, 4, 1, 37000.00, 37000.00, 'Approved', NULL, '2025-07-28 00:07:04', '2025-07-28 00:07:55'),
(16, 9, 12, 1, 250000.00, 250000.00, 'Rejected', NULL, '2025-07-28 22:17:24', '2025-07-29 07:58:46'),
(17, 10, 4, 1, 37000.00, 37000.00, 'Rejected', NULL, '2025-07-29 08:01:33', '2025-07-29 08:04:03'),
(18, 11, 5, 1, 32000.00, 32000.00, 'Approved', NULL, '2025-07-29 08:03:44', '2025-07-29 08:04:09'),
(19, 11, 17, 1, 55000.00, 55000.00, 'Approved', NULL, '2025-07-29 08:03:44', '2025-07-29 08:04:09'),
(20, 11, 4, 1, 37000.00, 37000.00, 'Approved', NULL, '2025-07-29 08:03:44', '2025-07-29 08:04:09'),
(21, 12, 4, 1, 37000.00, 37000.00, 'Approved', NULL, '2025-07-29 08:41:45', '2025-07-29 08:42:10'),
(22, 13, 15, 1, 1000000.00, 1000000.00, 'Approved', NULL, '2025-07-29 09:25:57', '2025-07-29 09:31:02'),
(23, 14, 3, 1, 30000.00, 30000.00, 'Approved', NULL, '2025-08-01 23:30:16', '2025-08-01 23:31:35'),
(24, 14, 15, 1, 1000000.00, 1000000.00, 'Rejected', NULL, '2025-08-01 23:30:16', '2025-08-01 23:31:32'),
(25, 14, 16, 1, 500000.00, 500000.00, 'Approved', NULL, '2025-08-01 23:30:16', '2025-08-01 23:31:37'),
(26, 15, 16, 1, 500000.00, 500000.00, 'Approved', NULL, '2025-08-03 07:23:56', '2025-08-03 07:25:45'),
(27, 15, 15, 1, 1000000.00, 1000000.00, 'Rejected', NULL, '2025-08-03 07:23:56', '2025-08-03 07:25:43'),
(28, 15, 3, 1, 30000.00, 30000.00, 'Approved', NULL, '2025-08-03 07:23:56', '2025-08-03 07:25:44');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `failed_jobs`
--

INSERT INTO `failed_jobs` (`id`, `uuid`, `connection`, `queue`, `payload`, `exception`, `failed_at`) VALUES
(1, '0fcfc878-3986-490e-9789-8ae0d08c58a3', 'database', 'default', '{\"uuid\":\"0fcfc878-3986-490e-9789-8ae0d08c58a3\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":1:{s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:0;}\"},\"createdAt\":1751683617,\"delay\":null}', 'Error: Call to a member function diffInHours() on string in C:\\xampp\\htdocs\\sistem-inventory\\app\\Jobs\\UpdateEOQCalculations.php:98\nStack trace:\n#0 C:\\xampp\\htdocs\\sistem-inventory\\app\\Jobs\\UpdateEOQCalculations.php(44): App\\Jobs\\UpdateEOQCalculations->updateAllItems(Object(App\\Services\\EOQCalculationService))\n#1 C:\\xampp\\htdocs\\sistem-inventory\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(36): App\\Jobs\\UpdateEOQCalculations->handle()\n#2 C:\\xampp\\htdocs\\sistem-inventory\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\Util.php(43): Illuminate\\Container\\BoundMethod::Illuminate\\Container\\{closure}()\n#3 C:\\xampp\\htdocs\\sistem-inventory\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(96): Illuminate\\Container\\Util::unwrapIfClosure(Object(Closure))\n#4 C:\\xampp\\htdocs\\sistem-inventory\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod(Object(Illuminate\\Foundation\\Application), Array, Object(Closure))\n#5 C:\\xampp\\htdocs\\sistem-inventory\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\Container.php(754): Illuminate\\Container\\BoundMethod::call(Object(Illuminate\\Foundation\\Application), Array, Array, NULL)\n#6 C:\\xampp\\htdocs\\sistem-inventory\\vendor\\laravel\\framework\\src\\Illuminate\\Bus\\Dispatcher.php(132): Illuminate\\Container\\Container->call(Array)\n#7 C:\\xampp\\htdocs\\sistem-inventory\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(169): Illuminate\\Bus\\Dispatcher->Illuminate\\Bus\\{closure}(Object(App\\Jobs\\UpdateEOQCalculations))\n#8 C:\\xampp\\htdocs\\sistem-inventory\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(126): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(App\\Jobs\\UpdateEOQCalculations))\n#9 C:\\xampp\\htdocs\\sistem-inventory\\vendor\\laravel\\framework\\src\\Illuminate\\Bus\\Dispatcher.php(136): Illuminate\\Pipeline\\Pipeline->then(Object(Closure))\n#10 C:\\xampp\\htdocs\\sistem-inventory\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\CallQueuedHandler.php(125): Illuminate\\Bus\\Dispatcher->dispatchNow(Object(App\\Jobs\\UpdateEOQCalculations), false)\n#11 C:\\xampp\\htdocs\\sistem-inventory\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(169): Illuminate\\Queue\\CallQueuedHandler->Illuminate\\Queue\\{closure}(Object(App\\Jobs\\UpdateEOQCalculations))\n#12 C:\\xampp\\htdocs\\sistem-inventory\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(126): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(App\\Jobs\\UpdateEOQCalculations))\n#13 C:\\xampp\\htdocs\\sistem-inventory\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\CallQueuedHandler.php(120): Illuminate\\Pipeline\\Pipeline->then(Object(Closure))\n#14 C:\\xampp\\htdocs\\sistem-inventory\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\CallQueuedHandler.php(68): Illuminate\\Queue\\CallQueuedHandler->dispatchThroughMiddleware(Object(Illuminate\\Queue\\Jobs\\DatabaseJob), Object(App\\Jobs\\UpdateEOQCalculations))\n#15 C:\\xampp\\htdocs\\sistem-inventory\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Jobs\\Job.php(102): Illuminate\\Queue\\CallQueuedHandler->call(Object(Illuminate\\Queue\\Jobs\\DatabaseJob), Array)\n#16 C:\\xampp\\htdocs\\sistem-inventory\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Worker.php(444): Illuminate\\Queue\\Jobs\\Job->fire()\n#17 C:\\xampp\\htdocs\\sistem-inventory\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Worker.php(394): Illuminate\\Queue\\Worker->process(\'database\', Object(Illuminate\\Queue\\Jobs\\DatabaseJob), Object(Illuminate\\Queue\\WorkerOptions))\n#18 C:\\xampp\\htdocs\\sistem-inventory\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Worker.php(180): Illuminate\\Queue\\Worker->runJob(Object(Illuminate\\Queue\\Jobs\\DatabaseJob), \'database\', Object(Illuminate\\Queue\\WorkerOptions))\n#19 C:\\xampp\\htdocs\\sistem-inventory\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Console\\WorkCommand.php(148): Illuminate\\Queue\\Worker->daemon(\'database\', \'default\', Object(Illuminate\\Queue\\WorkerOptions))\n#20 C:\\xampp\\htdocs\\sistem-inventory\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Console\\WorkCommand.php(131): Illuminate\\Queue\\Console\\WorkCommand->runWorker(\'database\', \'default\')\n#21 C:\\xampp\\htdocs\\sistem-inventory\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(36): Illuminate\\Queue\\Console\\WorkCommand->handle()\n#22 C:\\xampp\\htdocs\\sistem-inventory\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\Util.php(43): Illuminate\\Container\\BoundMethod::Illuminate\\Container\\{closure}()\n#23 C:\\xampp\\htdocs\\sistem-inventory\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(96): Illuminate\\Container\\Util::unwrapIfClosure(Object(Closure))\n#24 C:\\xampp\\htdocs\\sistem-inventory\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod(Object(Illuminate\\Foundation\\Application), Array, Object(Closure))\n#25 C:\\xampp\\htdocs\\sistem-inventory\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\Container.php(754): Illuminate\\Container\\BoundMethod::call(Object(Illuminate\\Foundation\\Application), Array, Array, NULL)\n#26 C:\\xampp\\htdocs\\sistem-inventory\\vendor\\laravel\\framework\\src\\Illuminate\\Console\\Command.php(209): Illuminate\\Container\\Container->call(Array)\n#27 C:\\xampp\\htdocs\\sistem-inventory\\vendor\\symfony\\console\\Command\\Command.php(318): Illuminate\\Console\\Command->execute(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Illuminate\\Console\\OutputStyle))\n#28 C:\\xampp\\htdocs\\sistem-inventory\\vendor\\laravel\\framework\\src\\Illuminate\\Console\\Command.php(178): Symfony\\Component\\Console\\Command\\Command->run(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Illuminate\\Console\\OutputStyle))\n#29 C:\\xampp\\htdocs\\sistem-inventory\\vendor\\symfony\\console\\Application.php(1092): Illuminate\\Console\\Command->run(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#30 C:\\xampp\\htdocs\\sistem-inventory\\vendor\\symfony\\console\\Application.php(341): Symfony\\Component\\Console\\Application->doRunCommand(Object(Illuminate\\Queue\\Console\\WorkCommand), Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#31 C:\\xampp\\htdocs\\sistem-inventory\\vendor\\symfony\\console\\Application.php(192): Symfony\\Component\\Console\\Application->doRun(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#32 C:\\xampp\\htdocs\\sistem-inventory\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Console\\Kernel.php(197): Symfony\\Component\\Console\\Application->run(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#33 C:\\xampp\\htdocs\\sistem-inventory\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Application.php(1234): Illuminate\\Foundation\\Console\\Kernel->handle(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#34 C:\\xampp\\htdocs\\sistem-inventory\\artisan(16): Illuminate\\Foundation\\Application->handleCommand(Object(Symfony\\Component\\Console\\Input\\ArgvInput))\n#35 {main}', '2025-07-04 19:47:23');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`id`, `queue`, `payload`, `attempts`, `reserved_at`, `available_at`, `created_at`) VALUES
(3, 'default', '{\"uuid\":\"402ceaad-430b-4d7f-9f9e-c82fe0d06cbd\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":1:{s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:1;}\"},\"createdAt\":1751684704,\"delay\":null}', 0, NULL, 1751684704, 1751684704),
(4, 'default', '{\"uuid\":\"67b16738-852b-4a5b-92b6-1128e13b2042\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":1:{s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:1;}\"},\"createdAt\":1751684706,\"delay\":null}', 0, NULL, 1751684706, 1751684706),
(5, 'default', '{\"uuid\":\"e1674922-6856-4cc3-9c66-2291c2838b41\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":1:{s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:1;}\"},\"createdAt\":1751684706,\"delay\":null}', 0, NULL, 1751684706, 1751684706),
(6, 'default', '{\"uuid\":\"a7cd8a70-ce3b-4feb-9fcc-6b3051ff4bec\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":1:{s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:1;}\"},\"createdAt\":1751684707,\"delay\":null}', 0, NULL, 1751684707, 1751684707),
(7, 'default', '{\"uuid\":\"fe62bb83-3065-4c1e-8f05-7881fc8c3cde\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":2:{s:9:\\\"\\u0000*\\u0000itemId\\\";s:1:\\\"1\\\";s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:1;}\"},\"createdAt\":1751684709,\"delay\":null}', 0, NULL, 1751684709, 1751684709),
(8, 'default', '{\"uuid\":\"73995527-de4c-4a8a-aa86-c71ec5ca97d2\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":2:{s:9:\\\"\\u0000*\\u0000itemId\\\";s:1:\\\"1\\\";s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:1;}\"},\"createdAt\":1751684710,\"delay\":null}', 0, NULL, 1751684710, 1751684710),
(9, 'default', '{\"uuid\":\"4d9c799d-c72a-4ed9-90ee-2fbe67fbf9b4\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":2:{s:9:\\\"\\u0000*\\u0000itemId\\\";s:1:\\\"1\\\";s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:1;}\"},\"createdAt\":1751684711,\"delay\":null}', 0, NULL, 1751684711, 1751684711),
(10, 'default', '{\"uuid\":\"32c3a50c-3c63-4dbb-aaa1-a43fda69886a\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":2:{s:9:\\\"\\u0000*\\u0000itemId\\\";s:1:\\\"2\\\";s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:1;}\"},\"createdAt\":1751684712,\"delay\":null}', 0, NULL, 1751684712, 1751684712),
(11, 'default', '{\"uuid\":\"0b3125f6-a1c8-4348-a705-b231d7cf325c\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":2:{s:9:\\\"\\u0000*\\u0000itemId\\\";s:1:\\\"4\\\";s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:1;}\"},\"createdAt\":1751684714,\"delay\":null}', 0, NULL, 1751684714, 1751684714),
(12, 'default', '{\"uuid\":\"f3c317c0-7270-4e87-9122-1bb2b304941f\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":2:{s:9:\\\"\\u0000*\\u0000itemId\\\";s:1:\\\"5\\\";s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:1;}\"},\"createdAt\":1751684715,\"delay\":null}', 0, NULL, 1751684715, 1751684715),
(13, 'default', '{\"uuid\":\"db1a0b26-7aa2-4fa2-8299-f4e01d5f7419\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":2:{s:9:\\\"\\u0000*\\u0000itemId\\\";s:1:\\\"6\\\";s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:1;}\"},\"createdAt\":1751684717,\"delay\":null}', 0, NULL, 1751684717, 1751684717),
(14, 'default', '{\"uuid\":\"b4593e76-b2da-456a-adf5-ab9618e1184b\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":1:{s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:1;}\"},\"createdAt\":1751684719,\"delay\":null}', 0, NULL, 1751684719, 1751684719),
(15, 'default', '{\"uuid\":\"c1b127c4-933a-4dc8-b8d6-139d3832b241\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":2:{s:9:\\\"\\u0000*\\u0000itemId\\\";s:1:\\\"1\\\";s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:1;}\"},\"createdAt\":1751684749,\"delay\":null}', 0, NULL, 1751684749, 1751684749),
(16, 'default', '{\"uuid\":\"34b5e1d4-e83f-42ba-99e9-f005eeb5d970\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":2:{s:9:\\\"\\u0000*\\u0000itemId\\\";s:1:\\\"2\\\";s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:1;}\"},\"createdAt\":1751684750,\"delay\":null}', 0, NULL, 1751684750, 1751684750),
(17, 'default', '{\"uuid\":\"c126c632-5d46-40c2-ac24-7d92ab110cbe\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":2:{s:9:\\\"\\u0000*\\u0000itemId\\\";s:1:\\\"3\\\";s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:1;}\"},\"createdAt\":1751684751,\"delay\":null}', 0, NULL, 1751684751, 1751684751),
(18, 'default', '{\"uuid\":\"1e4bb5d0-eca0-42c2-a802-9ddfd1505bc1\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":2:{s:9:\\\"\\u0000*\\u0000itemId\\\";s:1:\\\"4\\\";s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:1;}\"},\"createdAt\":1751684752,\"delay\":null}', 0, NULL, 1751684752, 1751684752),
(19, 'default', '{\"uuid\":\"432ce83f-f7fe-4d84-91a4-f25223cecec2\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":2:{s:9:\\\"\\u0000*\\u0000itemId\\\";s:1:\\\"5\\\";s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:1;}\"},\"createdAt\":1751684753,\"delay\":null}', 0, NULL, 1751684753, 1751684753),
(20, 'default', '{\"uuid\":\"85bc1d2a-3599-41ea-ac05-44498b735e9d\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":2:{s:9:\\\"\\u0000*\\u0000itemId\\\";s:1:\\\"6\\\";s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:1;}\"},\"createdAt\":1751684754,\"delay\":null}', 0, NULL, 1751684754, 1751684754),
(21, 'default', '{\"uuid\":\"4ccdebc2-cd45-4bf8-8c46-d5fa12634211\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":1:{s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:1;}\"},\"createdAt\":1751684894,\"delay\":null}', 0, NULL, 1751684894, 1751684894),
(22, 'default', '{\"uuid\":\"b174a22d-cec3-482e-abfa-fe42dd2066ca\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":2:{s:9:\\\"\\u0000*\\u0000itemId\\\";s:1:\\\"1\\\";s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:1;}\"},\"createdAt\":1751684902,\"delay\":null}', 0, NULL, 1751684902, 1751684902),
(23, 'default', '{\"uuid\":\"1c2ca748-9cd6-4341-8466-93a633ecafcc\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":2:{s:9:\\\"\\u0000*\\u0000itemId\\\";s:1:\\\"2\\\";s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:1;}\"},\"createdAt\":1751684903,\"delay\":null}', 0, NULL, 1751684903, 1751684903),
(24, 'default', '{\"uuid\":\"1c415108-547e-4757-9305-c06a79109bf7\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":2:{s:9:\\\"\\u0000*\\u0000itemId\\\";s:1:\\\"3\\\";s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:1;}\"},\"createdAt\":1751684905,\"delay\":null}', 0, NULL, 1751684905, 1751684905),
(25, 'default', '{\"uuid\":\"90ad373e-aee3-4f8b-be72-811d00686dcd\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":2:{s:9:\\\"\\u0000*\\u0000itemId\\\";s:1:\\\"4\\\";s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:1;}\"},\"createdAt\":1751684906,\"delay\":null}', 0, NULL, 1751684906, 1751684906),
(26, 'default', '{\"uuid\":\"76ab9b24-dcdc-45ad-83be-904a784b0b76\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":2:{s:9:\\\"\\u0000*\\u0000itemId\\\";s:1:\\\"5\\\";s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:1;}\"},\"createdAt\":1751685238,\"delay\":null}', 0, NULL, 1751685238, 1751685238),
(27, 'default', '{\"uuid\":\"7de947fa-6759-4114-94a4-8fcf292fa59b\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":2:{s:9:\\\"\\u0000*\\u0000itemId\\\";s:1:\\\"5\\\";s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:1;}\"},\"createdAt\":1751685240,\"delay\":null}', 0, NULL, 1751685240, 1751685240),
(28, 'default', '{\"uuid\":\"243078d3-635d-488d-a36e-8f8d7920c07f\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":2:{s:9:\\\"\\u0000*\\u0000itemId\\\";s:1:\\\"5\\\";s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:1;}\"},\"createdAt\":1751685254,\"delay\":null}', 0, NULL, 1751685254, 1751685254),
(29, 'default', '{\"uuid\":\"9b4cb77b-841d-40c7-9615-dd65a26670b1\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":1:{s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:1;}\"},\"createdAt\":1751685322,\"delay\":null}', 0, NULL, 1751685322, 1751685322),
(30, 'default', '{\"uuid\":\"a5cebad7-95f0-4c70-90e4-4366dbc3f240\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":1:{s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:1;}\"},\"createdAt\":1751685322,\"delay\":null}', 0, NULL, 1751685322, 1751685322),
(31, 'default', '{\"uuid\":\"e6ebd1be-1cd1-437a-b82d-31b45465814a\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":2:{s:9:\\\"\\u0000*\\u0000itemId\\\";s:1:\\\"2\\\";s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:1;}\"},\"createdAt\":1751686380,\"delay\":null}', 0, NULL, 1751686380, 1751686380),
(32, 'default', '{\"uuid\":\"85f2d634-96d1-487d-a8c1-846916083152\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":2:{s:9:\\\"\\u0000*\\u0000itemId\\\";s:1:\\\"2\\\";s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:1;}\"},\"createdAt\":1751686381,\"delay\":null}', 0, NULL, 1751686381, 1751686381),
(33, 'default', '{\"uuid\":\"bafba8c1-79a8-4a5f-a04b-5390cac4d025\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":2:{s:9:\\\"\\u0000*\\u0000itemId\\\";s:1:\\\"2\\\";s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:1;}\"},\"createdAt\":1751686381,\"delay\":null}', 0, NULL, 1751686381, 1751686381),
(34, 'default', '{\"uuid\":\"5403ae82-b2c0-449b-959f-c9f38a82c4b7\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":2:{s:9:\\\"\\u0000*\\u0000itemId\\\";s:1:\\\"4\\\";s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:1;}\"},\"createdAt\":1751807321,\"delay\":null}', 0, NULL, 1751807321, 1751807321),
(35, 'default', '{\"uuid\":\"98b4004e-e6b4-470f-94b3-343cd6e4324a\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":2:{s:9:\\\"\\u0000*\\u0000itemId\\\";s:1:\\\"4\\\";s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:1;}\"},\"createdAt\":1751807321,\"delay\":null}', 0, NULL, 1751807321, 1751807321),
(36, 'default', '{\"uuid\":\"e49d4afc-535f-4f0e-8793-316af896ab72\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":2:{s:9:\\\"\\u0000*\\u0000itemId\\\";s:1:\\\"4\\\";s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:1;}\"},\"createdAt\":1751807403,\"delay\":null}', 0, NULL, 1751807403, 1751807403),
(37, 'default', '{\"uuid\":\"d4e8be3a-f652-4022-ba79-9ad1af9eb764\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":2:{s:9:\\\"\\u0000*\\u0000itemId\\\";s:1:\\\"1\\\";s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:1;}\"},\"createdAt\":1751810080,\"delay\":null}', 0, NULL, 1751810080, 1751810080),
(38, 'default', '{\"uuid\":\"5fa7868d-1e7c-4c0e-89e5-2359c8e2797d\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":2:{s:9:\\\"\\u0000*\\u0000itemId\\\";s:1:\\\"2\\\";s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:1;}\"},\"createdAt\":1751810080,\"delay\":null}', 0, NULL, 1751810080, 1751810080),
(39, 'default', '{\"uuid\":\"5d37e621-eaf7-4d89-86bd-556634995555\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":2:{s:9:\\\"\\u0000*\\u0000itemId\\\";s:1:\\\"3\\\";s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:1;}\"},\"createdAt\":1751810081,\"delay\":null}', 0, NULL, 1751810081, 1751810081),
(40, 'default', '{\"uuid\":\"d7c7b82d-a4f6-4640-b08a-cc40ee5e2d86\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":2:{s:9:\\\"\\u0000*\\u0000itemId\\\";s:1:\\\"3\\\";s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:1;}\"},\"createdAt\":1751810082,\"delay\":null}', 0, NULL, 1751810082, 1751810082),
(41, 'default', '{\"uuid\":\"c77a44a8-6ca5-4448-afc0-3cf6221730ca\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":1:{s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:1;}\"},\"createdAt\":1751810086,\"delay\":null}', 0, NULL, 1751810086, 1751810086),
(42, 'default', '{\"uuid\":\"a7977fa1-d652-414f-b03d-1ceead0ae86f\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":2:{s:9:\\\"\\u0000*\\u0000itemId\\\";s:1:\\\"1\\\";s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:1;}\"},\"createdAt\":1751810101,\"delay\":null}', 0, NULL, 1751810101, 1751810101),
(43, 'default', '{\"uuid\":\"94d4ba0a-35c2-4b72-8ff8-a1b2a53b5317\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":2:{s:9:\\\"\\u0000*\\u0000itemId\\\";s:1:\\\"4\\\";s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:1;}\"},\"createdAt\":1751813757,\"delay\":null}', 0, NULL, 1751813757, 1751813757),
(44, 'default', '{\"uuid\":\"b5449388-ff8a-4cce-a5bd-1156fc2156c6\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":2:{s:9:\\\"\\u0000*\\u0000itemId\\\";s:1:\\\"1\\\";s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:1;}\"},\"createdAt\":1751813762,\"delay\":null}', 0, NULL, 1751813762, 1751813762),
(45, 'default', '{\"uuid\":\"ff379bdf-05e4-4e81-afd0-f59c4cd9734e\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":2:{s:9:\\\"\\u0000*\\u0000itemId\\\";s:1:\\\"4\\\";s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:1;}\"},\"createdAt\":1751815270,\"delay\":null}', 0, NULL, 1751815270, 1751815270),
(46, 'default', '{\"uuid\":\"4ed599f2-f4f1-4333-a7aa-2f1fd80e2cbd\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":1:{s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:1;}\"},\"createdAt\":1751816794,\"delay\":null}', 0, NULL, 1751816794, 1751816794),
(47, 'default', '{\"uuid\":\"677f1b2c-ee06-4651-a4fd-faa58e3fa1d8\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":2:{s:9:\\\"\\u0000*\\u0000itemId\\\";s:1:\\\"4\\\";s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:1;}\"},\"createdAt\":1751984084,\"delay\":null}', 0, NULL, 1751984084, 1751984084),
(48, 'default', '{\"uuid\":\"da37af98-a33a-429b-8ad7-6da39a6f9049\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":1:{s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:1;}\"},\"createdAt\":1751984109,\"delay\":null}', 0, NULL, 1751984109, 1751984109),
(49, 'default', '{\"uuid\":\"b79bd349-e61c-4402-ade4-fac616f06337\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":2:{s:9:\\\"\\u0000*\\u0000itemId\\\";s:1:\\\"4\\\";s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:1;}\"},\"createdAt\":1752069107,\"delay\":null}', 0, NULL, 1752069107, 1752069107),
(50, 'default', '{\"uuid\":\"2e7b101b-25ad-4134-a2c5-87c87d106d77\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":2:{s:9:\\\"\\u0000*\\u0000itemId\\\";s:1:\\\"5\\\";s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:1;}\"},\"createdAt\":1752069108,\"delay\":null}', 0, NULL, 1752069108, 1752069108),
(51, 'default', '{\"uuid\":\"5b0b1883-e64b-429e-aa50-0d45a3beed46\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":2:{s:9:\\\"\\u0000*\\u0000itemId\\\";i:5;s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:0;}\"},\"createdAt\":1752071631,\"delay\":null}', 0, NULL, 1752071631, 1752071631),
(52, 'default', '{\"uuid\":\"78b13989-a02e-49e6-92a3-607cefbb093c\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":2:{s:9:\\\"\\u0000*\\u0000itemId\\\";i:6;s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:0;}\"},\"createdAt\":1752071631,\"delay\":null}', 0, NULL, 1752071631, 1752071631),
(53, 'default', '{\"uuid\":\"fdeacbdf-cc05-45d5-9af0-2726cdef70f4\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":2:{s:9:\\\"\\u0000*\\u0000itemId\\\";i:6;s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:0;}\"},\"createdAt\":1752071724,\"delay\":null}', 0, NULL, 1752071724, 1752071724),
(54, 'default', '{\"uuid\":\"996febc9-5636-4039-a523-85cef72ee10a\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":2:{s:9:\\\"\\u0000*\\u0000itemId\\\";i:6;s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:0;}\"},\"createdAt\":1752071761,\"delay\":null}', 0, NULL, 1752071761, 1752071761),
(55, 'default', '{\"uuid\":\"2a560384-d166-4f46-ac77-bc75732d0751\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":2:{s:9:\\\"\\u0000*\\u0000itemId\\\";i:5;s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:0;}\"},\"createdAt\":1752073965,\"delay\":null}', 0, NULL, 1752073965, 1752073965),
(56, 'default', '{\"uuid\":\"e848a616-c712-4ea3-b638-33eafafbd221\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":2:{s:9:\\\"\\u0000*\\u0000itemId\\\";s:1:\\\"2\\\";s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:1;}\"},\"createdAt\":1752467605,\"delay\":null}', 0, NULL, 1752467605, 1752467605),
(57, 'default', '{\"uuid\":\"a460948e-d2cc-4b20-ac56-625b42db48c0\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":2:{s:9:\\\"\\u0000*\\u0000itemId\\\";s:1:\\\"2\\\";s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:1;}\"},\"createdAt\":1752467606,\"delay\":null}', 0, NULL, 1752467606, 1752467606),
(58, 'default', '{\"uuid\":\"de554ce3-6b83-4749-b399-2582d5bca916\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":2:{s:9:\\\"\\u0000*\\u0000itemId\\\";s:1:\\\"1\\\";s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:1;}\"},\"createdAt\":1752467607,\"delay\":null}', 0, NULL, 1752467607, 1752467607),
(59, 'default', '{\"uuid\":\"0a07b708-2b41-490a-91b1-2375a7053d0c\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":2:{s:9:\\\"\\u0000*\\u0000itemId\\\";s:1:\\\"2\\\";s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:1;}\"},\"createdAt\":1752467614,\"delay\":null}', 0, NULL, 1752467614, 1752467614),
(60, 'default', '{\"uuid\":\"2ef2e5ae-5363-4d24-bfaf-e34a0a7e9258\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":2:{s:9:\\\"\\u0000*\\u0000itemId\\\";s:1:\\\"4\\\";s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:1;}\"},\"createdAt\":1752467616,\"delay\":null}', 0, NULL, 1752467616, 1752467616),
(61, 'default', '{\"uuid\":\"1bc950cd-524e-495e-aeb4-b2140224b14f\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":2:{s:9:\\\"\\u0000*\\u0000itemId\\\";s:1:\\\"5\\\";s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:1;}\"},\"createdAt\":1752467617,\"delay\":null}', 0, NULL, 1752467617, 1752467617),
(62, 'default', '{\"uuid\":\"6ee4b932-47bb-4eaa-8e85-696b18409faf\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":2:{s:9:\\\"\\u0000*\\u0000itemId\\\";s:1:\\\"6\\\";s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:1;}\"},\"createdAt\":1752467619,\"delay\":null}', 0, NULL, 1752467619, 1752467619),
(63, 'default', '{\"uuid\":\"52307691-afe8-4fb8-ace6-67416e2dc1e4\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":2:{s:9:\\\"\\u0000*\\u0000itemId\\\";s:1:\\\"4\\\";s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:1;}\"},\"createdAt\":1752467622,\"delay\":null}', 0, NULL, 1752467622, 1752467622),
(64, 'default', '{\"uuid\":\"a54191cf-07d5-460d-a9d4-9321bedc2f92\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":2:{s:9:\\\"\\u0000*\\u0000itemId\\\";s:1:\\\"3\\\";s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:1;}\"},\"createdAt\":1752467625,\"delay\":null}', 0, NULL, 1752467625, 1752467625),
(65, 'default', '{\"uuid\":\"413e9eb1-c985-409b-8a1a-7ec184b1181d\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":2:{s:9:\\\"\\u0000*\\u0000itemId\\\";s:1:\\\"5\\\";s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:1;}\"},\"createdAt\":1752467629,\"delay\":null}', 0, NULL, 1752467629, 1752467629),
(66, 'default', '{\"uuid\":\"4971c58e-de10-46f5-8572-46accb9516da\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":2:{s:9:\\\"\\u0000*\\u0000itemId\\\";s:2:\\\"11\\\";s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:1;}\"},\"createdAt\":1752475242,\"delay\":null}', 0, NULL, 1752475242, 1752475242),
(67, 'default', '{\"uuid\":\"90466056-5764-49ba-aa7c-29ef5bf01fc7\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":2:{s:9:\\\"\\u0000*\\u0000itemId\\\";s:1:\\\"2\\\";s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:1;}\"},\"createdAt\":1752592969,\"delay\":null}', 0, NULL, 1752592969, 1752592969),
(68, 'default', '{\"uuid\":\"bc23522c-7650-4612-976b-2b6e51b4ab68\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":2:{s:9:\\\"\\u0000*\\u0000itemId\\\";s:1:\\\"2\\\";s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:1;}\"},\"createdAt\":1752592971,\"delay\":null}', 0, NULL, 1752592971, 1752592971),
(69, 'default', '{\"uuid\":\"8b3450ae-e84a-4fea-a8d2-ac78d2ec34c8\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":2:{s:9:\\\"\\u0000*\\u0000itemId\\\";s:1:\\\"3\\\";s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:1;}\"},\"createdAt\":1752592972,\"delay\":null}', 0, NULL, 1752592972, 1752592972),
(70, 'default', '{\"uuid\":\"aa9f60ba-3cd3-4d5e-8698-f87926e170a6\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":2:{s:9:\\\"\\u0000*\\u0000itemId\\\";i:4;s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:0;}\"},\"createdAt\":1752593987,\"delay\":null}', 0, NULL, 1752593987, 1752593987),
(71, 'default', '{\"uuid\":\"01ef2295-d914-4c02-ba2c-670a2aa023dd\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":2:{s:9:\\\"\\u0000*\\u0000itemId\\\";i:6;s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:0;}\"},\"createdAt\":1752593987,\"delay\":null}', 0, NULL, 1752593987, 1752593987),
(72, 'default', '{\"uuid\":\"b89181a0-a637-41ab-80bc-98098b7a4c44\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":2:{s:9:\\\"\\u0000*\\u0000itemId\\\";i:15;s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:0;}\"},\"createdAt\":1752594115,\"delay\":null}', 0, NULL, 1752594115, 1752594115),
(73, 'default', '{\"uuid\":\"6664049d-a931-4e37-887d-d8a91a6a77fd\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":2:{s:9:\\\"\\u0000*\\u0000itemId\\\";s:1:\\\"1\\\";s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:1;}\"},\"createdAt\":1752594815,\"delay\":null}', 0, NULL, 1752594815, 1752594815),
(74, 'default', '{\"uuid\":\"f5174f4d-b98f-470d-839d-f1153ca1ec5e\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":2:{s:9:\\\"\\u0000*\\u0000itemId\\\";i:11;s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:0;}\"},\"createdAt\":1752676933,\"delay\":null}', 0, NULL, 1752676933, 1752676933),
(75, 'default', '{\"uuid\":\"a5bd57e6-cde1-4fed-ae40-b743aee6afbc\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":2:{s:9:\\\"\\u0000*\\u0000itemId\\\";s:1:\\\"3\\\";s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:1;}\"},\"createdAt\":1752898796,\"delay\":null}', 0, NULL, 1752898796, 1752898796),
(76, 'default', '{\"uuid\":\"8d3a6acb-e8da-4936-ab65-43ebdf815336\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":2:{s:9:\\\"\\u0000*\\u0000itemId\\\";i:6;s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:0;}\"},\"createdAt\":1752899221,\"delay\":null}', 0, NULL, 1752899221, 1752899221),
(77, 'default', '{\"uuid\":\"a1fb2406-3055-40a0-9fcc-70d204b3104d\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":2:{s:9:\\\"\\u0000*\\u0000itemId\\\";i:11;s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:0;}\"},\"createdAt\":1752899221,\"delay\":null}', 0, NULL, 1752899221, 1752899221),
(78, 'default', '{\"uuid\":\"72fa07f2-b7b8-47e7-ba43-b7f51668dca5\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":1:{s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:1;}\"},\"createdAt\":1753154503,\"delay\":null}', 0, NULL, 1753154503, 1753154503),
(79, 'default', '{\"uuid\":\"2012c946-751d-4853-911f-ef930e73572d\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":1:{s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:1;}\"},\"createdAt\":1753325812,\"delay\":null}', 0, NULL, 1753325812, 1753325812),
(80, 'default', '{\"uuid\":\"35c088c8-048f-4f66-ade2-3a156cae1da5\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":2:{s:9:\\\"\\u0000*\\u0000itemId\\\";s:2:\\\"18\\\";s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:1;}\"},\"createdAt\":1753326376,\"delay\":null}', 0, NULL, 1753326376, 1753326376),
(81, 'default', '{\"uuid\":\"2a21c170-7663-4feb-9361-3e9086ddcfad\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":2:{s:9:\\\"\\u0000*\\u0000itemId\\\";s:2:\\\"18\\\";s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:1;}\"},\"createdAt\":1753329862,\"delay\":null}', 0, NULL, 1753329862, 1753329862),
(82, 'default', '{\"uuid\":\"9ae96a7d-4ce1-43af-91ef-091b2c6eac14\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":2:{s:9:\\\"\\u0000*\\u0000itemId\\\";i:3;s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:0;}\"},\"createdAt\":1753363690,\"delay\":null}', 0, NULL, 1753363690, 1753363690),
(83, 'default', '{\"uuid\":\"fe6e3590-a5c8-4000-b8b0-58b5e61d9168\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":2:{s:9:\\\"\\u0000*\\u0000itemId\\\";i:11;s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:0;}\"},\"createdAt\":1753363690,\"delay\":null}', 0, NULL, 1753363690, 1753363690),
(84, 'default', '{\"uuid\":\"1201d175-0459-4a24-9340-7a44d8f2ddb2\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":2:{s:9:\\\"\\u0000*\\u0000itemId\\\";i:17;s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:0;}\"},\"createdAt\":1753417198,\"delay\":null}', 0, NULL, 1753417198, 1753417198),
(85, 'default', '{\"uuid\":\"6eea497f-2a12-4f69-9d50-e08d5a957358\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":2:{s:9:\\\"\\u0000*\\u0000itemId\\\";i:18;s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:0;}\"},\"createdAt\":1753417198,\"delay\":null}', 0, NULL, 1753417198, 1753417198);
INSERT INTO `jobs` (`id`, `queue`, `payload`, `attempts`, `reserved_at`, `available_at`, `created_at`) VALUES
(86, 'default', '{\"uuid\":\"d71ce6f9-0ec1-4724-a5d2-d8c154065fd9\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":2:{s:9:\\\"\\u0000*\\u0000itemId\\\";s:1:\\\"2\\\";s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:1;}\"},\"createdAt\":1753676665,\"delay\":null}', 0, NULL, 1753676665, 1753676665),
(87, 'default', '{\"uuid\":\"fd24ab03-7173-496b-9f45-f49ef4e1b992\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":2:{s:9:\\\"\\u0000*\\u0000itemId\\\";i:18;s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:0;}\"},\"createdAt\":1753676690,\"delay\":null}', 0, NULL, 1753676690, 1753676690),
(88, 'default', '{\"uuid\":\"38be96f5-448c-4015-ab7d-cb55129084ba\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":2:{s:9:\\\"\\u0000*\\u0000itemId\\\";i:18;s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:0;}\"},\"createdAt\":1754116121,\"delay\":null}', 0, NULL, 1754116121, 1754116121),
(89, 'default', '{\"uuid\":\"9cbdd738-ecaa-4a1a-bd73-3ffb19e0a4ce\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":2:{s:9:\\\"\\u0000*\\u0000itemId\\\";i:6;s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:0;}\"},\"createdAt\":1754116630,\"delay\":null}', 0, NULL, 1754116630, 1754116630),
(90, 'default', '{\"uuid\":\"af1300ab-902f-4a68-bada-9b6a480e147c\",\"displayName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\UpdateEOQCalculations\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\UpdateEOQCalculations\\\":2:{s:9:\\\"\\u0000*\\u0000itemId\\\";i:17;s:14:\\\"\\u0000*\\u0000forceUpdate\\\";b:0;}\"},\"createdAt\":1754116630,\"delay\":null}', 0, NULL, 1754116630, 1754116630);

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kategori_barang`
--

CREATE TABLE `kategori_barang` (
  `id_kategori` bigint(20) UNSIGNED NOT NULL,
  `nama_kategori` varchar(100) NOT NULL,
  `kode_kategori` varchar(10) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `icon` varchar(50) NOT NULL DEFAULT '?',
  `warna` varchar(20) NOT NULL DEFAULT '#6B7280',
  `aktif` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kategori_barang`
--

INSERT INTO `kategori_barang` (`id_kategori`, `nama_kategori`, `kode_kategori`, `deskripsi`, `icon`, `warna`, `aktif`, `created_at`, `updated_at`) VALUES
(1, 'Fluids', 'FLD', 'Oli mesin, coolant, brake fluid, power steering', '🛢️', '#DC2626', 1, '2025-07-14 18:18:12', '2025-07-14 18:18:12'),
(2, 'Filters', 'FLT', 'Filter oli, udara, fuel, AC, transmisi', '🔍', '#059669', 1, '2025-07-14 18:18:12', '2025-07-14 18:18:12'),
(3, 'Engine Parts', 'ENG', 'Busi, timing belt, gasket, water pump', '⚙️', '#2563EB', 1, '2025-07-14 18:18:12', '2025-07-14 18:18:12'),
(4, 'Electrical', 'ELC', 'Aki, alternator, starter, lampu, fuse, relay', '🔌', '#7C3AED', 1, '2025-07-14 18:18:12', '2025-07-14 18:18:12'),
(5, 'Body Parts', 'BDY', 'Spion, bumper, kaca, wiper, trim', '🚗', '#EA580C', 1, '2025-07-14 18:18:12', '2025-07-14 18:18:12'),
(6, 'Consumables', 'CSM', 'Lap, pembersih, grease, seal, gasket kecil', '📄', '#65A30D', 1, '2025-07-14 18:18:12', '2025-07-14 18:18:12'),
(7, 'Tools', 'TOL', 'Diagnostic tools, kompressor, peralatan bengkel', '🔧', '#0891B2', 1, '2025-07-14 18:18:12', '2025-07-14 18:18:12');

-- --------------------------------------------------------

--
-- Table structure for table `log_stok`
--

CREATE TABLE `log_stok` (
  `id_log` bigint(20) UNSIGNED NOT NULL,
  `id_barang` bigint(20) UNSIGNED NOT NULL,
  `tanggal_log` datetime NOT NULL,
  `jenis_perubahan` enum('Masuk','Keluar','Adjustment','Koreksi') NOT NULL,
  `qty_sebelum` int(11) NOT NULL,
  `qty_perubahan` int(11) NOT NULL,
  `qty_sesudah` int(11) NOT NULL,
  `id_user` bigint(20) UNSIGNED NOT NULL,
  `referensi_tipe` varchar(255) DEFAULT NULL,
  `referensi_id` bigint(20) UNSIGNED DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `log_stok`
--

INSERT INTO `log_stok` (`id_log`, `id_barang`, `tanggal_log`, `jenis_perubahan`, `qty_sebelum`, `qty_perubahan`, `qty_sesudah`, `id_user`, `referensi_tipe`, `referensi_id`, `keterangan`, `created_at`, `updated_at`) VALUES
(1, 1, '2025-07-05 03:07:50', 'Keluar', 47, -5, 42, 1, 'manual', NULL, 'Test transaction', '2025-07-04 20:07:50', '2025-07-04 20:07:50'),
(2, 5, '2025-07-05 03:11:32', 'Keluar', 11, -5, 6, 1, 'transaksi', 999, 'Service Honda Beat - Filter + Injector', '2025-07-04 20:11:32', '2025-07-04 20:11:32'),
(3, 2, '2025-07-05 03:12:46', 'Keluar', 41, -2, 39, 1, 'transaksi', NULL, 'Service Yamaha Mio - Brake Oil', '2025-07-04 20:12:46', '2025-07-04 20:12:46'),
(4, 3, '2025-07-05 03:12:46', 'Keluar', 12, -1, 11, 1, 'transaksi', NULL, 'Service Honda Vario - Brake Oil', '2025-07-04 20:12:46', '2025-07-04 20:12:46'),
(5, 4, '2025-07-05 03:12:46', 'Keluar', 15, -3, 12, 1, 'transaksi', NULL, 'Service Honda Beat - Carbon Cleaner', '2025-07-04 20:12:46', '2025-07-04 20:12:46'),
(6, 5, '2025-07-09 14:33:50', 'Masuk', 6, 72, 78, 2, 'barang_masuk', 1, 'Barang Masuk: BM20250709001', '2025-07-09 07:33:50', '2025-07-09 07:33:50'),
(7, 6, '2025-07-09 14:33:50', 'Masuk', 47, 1, 48, 2, 'barang_masuk', 1, 'Barang Masuk: BM20250709001', '2025-07-09 07:33:50', '2025-07-09 07:33:50'),
(8, 6, '2025-07-09 14:35:24', 'Masuk', 48, 1, 49, 2, 'barang_masuk', 2, 'Barang Masuk Manual: BM20250709002', '2025-07-09 07:35:24', '2025-07-09 07:35:24'),
(9, 6, '2025-07-09 14:36:01', 'Masuk', 49, 1, 50, 2, 'barang_masuk', 3, 'Barang Masuk Manual: BM20250709003', '2025-07-09 07:36:01', '2025-07-09 07:36:01'),
(10, 5, '2025-07-09 15:12:45', 'Masuk', 78, 72, 150, 2, 'barang_masuk', 4, 'Barang Masuk: BM20250709004', '2025-07-09 08:12:45', '2025-07-09 08:12:45'),
(11, 3, '2025-07-12 02:17:08', 'Keluar', 11, -1, 10, 3, 'transaksi', 1, 'Transaksi Service: TRX20250712001', '2025-07-11 19:17:08', '2025-07-11 19:17:08'),
(12, 5, '2025-07-12 02:17:08', 'Keluar', 150, -1, 149, 3, 'transaksi', 1, 'Transaksi Service: TRX20250712001', '2025-07-11 19:17:08', '2025-07-11 19:17:08'),
(13, 4, '2025-07-12 02:28:19', 'Keluar', 12, -1, 11, 3, 'transaksi', 3, 'Transaksi Service: TRX20250712003', '2025-07-11 19:28:19', '2025-07-11 19:28:19'),
(14, 11, '2025-07-14 04:40:14', 'Masuk', 0, 10, 10, 2, 'manual_adjustment', NULL, 'Coba', '2025-07-13 21:40:14', '2025-07-13 21:40:14'),
(18, 11, '2025-07-14 04:57:43', 'Adjustment', 0, 0, 0, 2, 'barang_update', NULL, 'Data barang diupdate: lead_time:  → 1', '2025-07-13 21:57:43', '2025-07-13 21:57:43'),
(19, 11, '2025-07-14 05:02:29', 'Adjustment', 0, 0, 0, 2, 'barang_update', NULL, 'Data barang diupdate: lead_time: 1 → 2', '2025-07-13 22:02:29', '2025-07-13 22:02:29'),
(20, 11, '2025-07-14 05:14:55', 'Adjustment', 0, 0, 0, 2, 'barang_update', NULL, 'Data barang diupdate: annual_demand:  → 100.00, holding_cost:  → 10.00', '2025-07-13 22:14:55', '2025-07-13 22:14:55'),
(21, 11, '2025-07-14 05:15:42', 'Adjustment', 0, 0, 0, 2, 'barang_update', NULL, 'Data barang diupdate: annual_demand: 100.00 → 1976.00', '2025-07-13 22:15:42', '2025-07-13 22:15:42'),
(22, 11, '2025-07-14 05:21:46', 'Adjustment', 0, 0, 0, 2, 'barang_update', NULL, 'Data barang diupdate: harga_beli: 140000.00 → 152520.00, annual_demand: 0.00 → 513.00, ordering_cost: 25000.00 → 5000.00, holding_cost: 15.00 → 10.00', '2025-07-13 22:21:46', '2025-07-13 22:21:46'),
(23, 11, '2025-07-14 05:24:03', 'Adjustment', 0, 0, 0, 2, 'barang_update', NULL, 'Data barang diupdate: annual_demand: 513.00 → 494.00', '2025-07-13 22:24:03', '2025-07-13 22:24:03'),
(24, 2, '2025-07-14 07:15:24', 'Adjustment', 0, 0, 0, 2, 'barang_update', NULL, 'Data barang diupdate: holding_cost: 2800.00 → 10.00', '2025-07-14 00:15:24', '2025-07-14 00:15:24'),
(25, 1, '2025-07-14 07:15:38', 'Adjustment', 0, 0, 0, 2, 'barang_update', NULL, 'Data barang diupdate: holding_cost: 4010.00 → 10.00', '2025-07-14 00:15:38', '2025-07-14 00:15:38'),
(26, 6, '2025-07-14 07:15:56', 'Adjustment', 0, 0, 0, 2, 'barang_update', NULL, 'Data barang diupdate: holding_cost: 5500.00 → 10.00', '2025-07-14 00:15:56', '2025-07-14 00:15:56'),
(27, 3, '2025-07-14 07:17:08', 'Adjustment', 0, 0, 0, 2, 'barang_update', NULL, 'Data barang diupdate: holding_cost: 2600.00 → 10.00', '2025-07-14 00:17:08', '2025-07-14 00:17:08'),
(28, 5, '2025-07-14 07:17:48', 'Adjustment', 0, 0, 0, 2, 'barang_update', NULL, 'Data barang diupdate: holding_cost: 2800.00 → 10.00', '2025-07-14 00:17:48', '2025-07-14 00:17:48'),
(29, 4, '2025-07-14 07:18:11', 'Adjustment', 0, 0, 0, 2, 'barang_update', NULL, 'Data barang diupdate: holding_cost: 3250.00 → 10.00', '2025-07-14 00:18:11', '2025-07-14 00:18:11'),
(30, 12, '2025-07-15 00:45:16', 'Masuk', 0, 11, 11, 2, 'manual_adjustment', NULL, 'Manual adjustment from barang update', '2025-07-14 17:45:16', '2025-07-14 17:45:16'),
(31, 11, '2025-07-15 03:33:22', 'Masuk', 10, 2, 12, 2, 'manual_adjustment', NULL, 'Manual adjustment from barang update', '2025-07-14 20:33:22', '2025-07-14 20:33:22'),
(32, 15, '2025-07-15 15:11:16', 'Adjustment', 0, 0, 0, 2, 'barang_update', NULL, 'Data barang diupdate: annual_demand:  → 1000.00, ordering_cost:  → 5000.00, holding_cost:  → 10.00, lead_time:  → 2', '2025-07-15 08:11:16', '2025-07-15 08:11:16'),
(33, 15, '2025-07-15 15:14:12', 'Adjustment', 0, 0, 0, 2, 'barang_update', NULL, 'Data barang diupdate: harga_beli: 440000.00 → 800000.00, harga_jual: 500000.00 → 1000000.00, annual_demand: 1000.00 → 40.00, ordering_cost: 5000.00 → 100000.00, lead_time: 2 → 7', '2025-07-15 08:14:12', '2025-07-15 08:14:12'),
(34, 16, '2025-07-15 15:16:23', 'Masuk', 0, 10, 10, 2, 'manual_adjustment', NULL, 'Manual adjustment', '2025-07-15 08:16:23', '2025-07-15 08:16:23'),
(35, 15, '2025-07-15 15:16:46', 'Masuk', 0, 10, 10, 2, 'manual_adjustment', NULL, 'Manual adjustment', '2025-07-15 08:16:46', '2025-07-15 08:16:46'),
(36, 4, '2025-07-15 15:39:47', 'Masuk', 11, 67, 78, 2, 'barang_masuk', 5, 'Barang Masuk: BM20250715001', '2025-07-15 08:39:47', '2025-07-15 08:39:47'),
(37, 6, '2025-07-15 15:39:47', 'Masuk', 50, 12, 62, 2, 'barang_masuk', 5, 'Barang Masuk: BM20250715001', '2025-07-15 08:39:47', '2025-07-15 08:39:47'),
(38, 15, '2025-07-15 15:41:55', 'Masuk', 10, 1, 11, 2, 'barang_masuk', 6, 'Barang Masuk Manual: BM20250715002', '2025-07-15 08:41:55', '2025-07-15 08:41:55'),
(39, 15, '2025-07-15 15:54:00', 'Keluar', 11, -7, 4, 2, 'manual_adjustment', NULL, 'Manual adjustment from barang update', '2025-07-15 08:54:00', '2025-07-15 08:54:00'),
(40, 11, '2025-07-15 15:54:29', 'Keluar', 12, -7, 5, 2, 'manual_adjustment', NULL, 'Manual adjustment from barang update', '2025-07-15 08:54:29', '2025-07-15 08:54:29'),
(41, 11, '2025-07-16 14:38:27', 'Keluar', 5, -1, 4, 3, 'transaksi', 4, 'Transaksi Service: TRX20250716001', '2025-07-16 07:38:27', '2025-07-16 07:38:27'),
(42, 11, '2025-07-16 14:42:13', 'Masuk', 4, 10, 14, 2, 'barang_masuk', 7, 'Barang Masuk: BM20250716001', '2025-07-16 07:42:13', '2025-07-16 07:42:13'),
(43, 6, '2025-07-19 04:27:01', 'Masuk', 62, 2, 64, 2, 'barang_masuk', 8, 'Barang Masuk: BM20250719001', '2025-07-18 21:27:01', '2025-07-18 21:27:01'),
(44, 11, '2025-07-19 04:27:01', 'Masuk', 14, 8, 22, 2, 'barang_masuk', 8, 'Barang Masuk: BM20250719001', '2025-07-18 21:27:01', '2025-07-18 21:27:01'),
(45, 3, '2025-07-19 04:28:12', 'Keluar', 10, -6, 4, 2, 'manual_adjustment', NULL, 'Manual adjustment from barang update', '2025-07-18 21:28:12', '2025-07-18 21:28:12'),
(46, 11, '2025-07-19 04:28:33', 'Keluar', 22, -14, 8, 2, 'manual_adjustment', NULL, 'Manual adjustment from barang update', '2025-07-18 21:28:33', '2025-07-18 21:28:33'),
(47, 2, '2025-07-19 04:33:00', 'Keluar', 39, -1, 38, 3, 'transaksi', 5, 'Transaksi Service: TRX20250719001', '2025-07-18 21:33:00', '2025-07-18 21:33:00'),
(48, 11, '2025-07-24 03:43:19', 'Keluar', 8, -6, 2, 2, 'manual_adjustment', NULL, 'Manual adjustment from barang update', '2025-07-23 20:43:19', '2025-07-23 20:43:19'),
(49, 3, '2025-07-24 13:28:10', 'Masuk', 4, 6, 10, 2, 'barang_masuk', 9, 'Barang Masuk: BM20250724001', '2025-07-24 06:28:10', '2025-07-24 06:28:10'),
(50, 11, '2025-07-24 13:28:10', 'Masuk', 2, 2, 4, 2, 'barang_masuk', 9, 'Barang Masuk: BM20250724001', '2025-07-24 06:28:10', '2025-07-24 06:28:10'),
(51, 17, '2025-07-25 04:19:58', 'Masuk', 0, 2, 2, 2, 'barang_masuk', 10, 'Barang Masuk Manual: BM20250725001', '2025-07-24 21:19:58', '2025-07-24 21:19:58'),
(52, 18, '2025-07-25 04:19:58', 'Masuk', 0, 2, 2, 2, 'barang_masuk', 10, 'Barang Masuk Manual: BM20250725001', '2025-07-24 21:19:58', '2025-07-24 21:19:58'),
(53, 4, '2025-07-25 04:48:19', 'Keluar', 78, -1, 77, 3, 'transaksi', 6, 'Transaksi Service: TRX20250725001', '2025-07-24 21:48:19', '2025-07-24 21:48:19'),
(54, 15, '2025-07-25 04:48:19', 'Keluar', 4, -1, 3, 3, 'transaksi', 6, 'Transaksi Service: TRX20250725001', '2025-07-24 21:48:19', '2025-07-24 21:48:19'),
(55, 17, '2025-07-28 04:22:33', 'Keluar', 2, -1, 1, 2, 'manual_adjustment', NULL, 'Manual adjustment', '2025-07-27 21:22:33', '2025-07-27 21:22:33'),
(56, 18, '2025-07-28 04:24:50', 'Masuk', 2, 12, 14, 2, 'barang_masuk', 11, 'Barang Masuk: BM20250728001', '2025-07-27 21:24:50', '2025-07-27 21:24:50'),
(57, 4, '2025-07-28 07:08:34', 'Keluar', 77, -1, 76, 7, 'transaksi', 8, 'Transaksi Service: TRX20250728002', '2025-07-28 00:08:34', '2025-07-28 00:08:34'),
(58, 4, '2025-07-29 12:52:58', 'Keluar', 76, -52, 24, 2, 'manual_adjustment', NULL, 'Manual adjustment from barang update', '2025-07-29 05:52:58', '2025-07-29 05:52:58'),
(59, 4, '2025-07-29 12:53:15', 'Keluar', 24, -5, 19, 2, 'manual_adjustment', NULL, 'Manual adjustment from barang update', '2025-07-29 05:53:15', '2025-07-29 05:53:15'),
(60, 4, '2025-07-29 12:53:55', 'Keluar', 19, -3, 16, 2, 'manual_adjustment', NULL, 'Manual adjustment from barang update', '2025-07-29 05:53:55', '2025-07-29 05:53:55'),
(61, 4, '2025-07-29 12:54:32', 'Keluar', 16, -1, 15, 2, 'manual_adjustment', NULL, 'Manual adjustment from barang update', '2025-07-29 05:54:32', '2025-07-29 05:54:32'),
(62, 16, '2025-07-29 13:10:59', 'Keluar', 10, -1, 9, 3, 'transaksi', 7, 'Transaksi Service: TRX20250728001', '2025-07-29 06:10:59', '2025-07-29 06:10:59'),
(63, 12, '2025-07-29 14:55:18', 'Keluar', 11, -11, 0, 2, 'manual_adjustment', NULL, 'Manual adjustment', '2025-07-29 07:55:18', '2025-07-29 07:55:18'),
(64, 3, '2025-07-29 15:18:15', 'Masuk', 10, 5, 15, 2, 'manual_adjustment', NULL, 'Manual adjustment', '2025-07-29 08:18:15', '2025-07-29 08:18:15'),
(65, 3, '2025-07-29 15:18:48', 'Keluar', 15, -3, 12, 2, 'manual_adjustment', NULL, 'Manual adjustment', '2025-07-29 08:18:48', '2025-07-29 08:18:48'),
(66, 4, '2025-07-29 15:33:54', 'Keluar', 15, -1, 14, 7, 'transaksi', 11, 'Transaksi Service: TRX20250729003', '2025-07-29 08:33:54', '2025-07-29 08:33:54'),
(67, 5, '2025-07-29 15:33:54', 'Keluar', 149, -1, 148, 7, 'transaksi', 11, 'Transaksi Service: TRX20250729003', '2025-07-29 08:33:54', '2025-07-29 08:33:54'),
(68, 17, '2025-07-29 15:33:54', 'Keluar', 1, -1, 0, 7, 'transaksi', 11, 'Transaksi Service: TRX20250729003', '2025-07-29 08:33:54', '2025-07-29 08:33:54'),
(69, 4, '2025-07-29 15:44:03', 'Keluar', 14, -1, 13, 7, 'transaksi', 12, 'Transaksi Service: TRX20250729004', '2025-07-29 08:44:03', '2025-07-29 08:44:03'),
(70, 15, '2025-07-29 16:31:25', 'Keluar', 3, -1, 2, 7, 'transaksi', 13, 'Transaksi Service: TRX20250729005', '2025-07-29 09:31:25', '2025-07-29 09:31:25'),
(71, 18, '2025-08-02 06:28:41', 'Masuk', 14, 1, 15, 2, 'barang_masuk', 12, 'Barang Masuk: BM20250802001', '2025-08-01 23:28:41', '2025-08-01 23:28:41'),
(72, 6, '2025-08-02 06:37:10', 'Masuk', 64, 1, 65, 2, 'barang_masuk', 13, 'Barang Masuk: BM20250802002', '2025-08-01 23:37:10', '2025-08-01 23:37:10'),
(73, 17, '2025-08-02 06:37:10', 'Masuk', 0, 40, 40, 2, 'barang_masuk', 13, 'Barang Masuk: BM20250802002', '2025-08-01 23:37:10', '2025-08-01 23:37:10'),
(74, 3, '2025-08-03 14:58:10', 'Keluar', 12, -1, 11, 7, 'transaksi', 14, 'Transaksi Service: TRX20250802001', '2025-08-03 07:58:10', '2025-08-03 07:58:10'),
(75, 16, '2025-08-03 14:58:10', 'Keluar', 9, -1, 8, 7, 'transaksi', 14, 'Transaksi Service: TRX20250802001', '2025-08-03 07:58:10', '2025-08-03 07:58:10');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0000_06_30_130627_create_roles_table', 1),
(2, '0001_01_01_000000_create_users_table', 1),
(3, '0001_01_01_000001_create_cache_table', 1),
(4, '0001_01_01_000002_create_jobs_table', 1),
(5, '2025_07_04_134657_create_barang_table', 2),
(6, '2025_07_04_134757_create_stok_table', 2),
(7, '2025_07_04_134952_create_transaksi_table', 2),
(8, '2025_07_04_135009_create_detail_transaksi_table', 2),
(9, '2025_07_04_135040_create_restock_request_table', 2),
(10, '2025_07_04_135057_create_restock_request_detail_table', 2),
(11, '2025_07_04_135127_create_barang_masuk_table', 2),
(12, '2025_07_04_135140_create_barang_masuk_detail_table', 2),
(13, '2025_07_04_135207_create_log_stok_table', 2),
(14, '2025_07_04_135243_update_users_table', 2),
(15, '2025_07_04_153613_add_eoq_fields_to_barang_table', 3),
(16, '2025_07_08_073545_add_termination_fields_to_restock_request', 4),
(17, '2025_07_08_133559_add_ordered_status_to_restock_request', 5),
(18, '2025_07_12_014451_change_jenis_transaksi_to_string_in_transaksi_table', 6),
(19, '2025_07_14_060420_add_eoq_fields_to_barang_table', 7),
(20, '2025_07_15_011625_create_kategori_barang_table', 8),
(21, '2025_07_15_011717_update_barang_table_add_kategori', 8);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `restock_request`
--

CREATE TABLE `restock_request` (
  `id_request` bigint(20) UNSIGNED NOT NULL,
  `nomor_request` varchar(255) NOT NULL,
  `id_user_gudang` bigint(20) UNSIGNED NOT NULL,
  `tanggal_request` datetime NOT NULL,
  `status_request` enum('Pending','Approved','Ordered','Completed','Rejected','Terminated','Cancelled') DEFAULT 'Pending',
  `id_user_approved` bigint(20) UNSIGNED DEFAULT NULL,
  `tanggal_approved` datetime DEFAULT NULL,
  `tanggal_terminated` timestamp NULL DEFAULT NULL,
  `id_user_terminated` bigint(20) UNSIGNED DEFAULT NULL,
  `tanggal_ordered` timestamp NULL DEFAULT NULL,
  `id_user_ordered` bigint(20) UNSIGNED DEFAULT NULL,
  `catatan_request` text DEFAULT NULL,
  `catatan_approval` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `restock_request`
--

INSERT INTO `restock_request` (`id_request`, `nomor_request`, `id_user_gudang`, `tanggal_request`, `status_request`, `id_user_approved`, `tanggal_approved`, `tanggal_terminated`, `id_user_terminated`, `tanggal_ordered`, `id_user_ordered`, `catatan_request`, `catatan_approval`, `created_at`, `updated_at`) VALUES
(1, 'REQ20250707001', 2, '2025-07-07 05:02:52', 'Rejected', 1, '2025-07-07 14:53:55', NULL, NULL, NULL, NULL, NULL, 'dd', '2025-07-06 22:02:52', '2025-07-07 07:53:55'),
(2, 'REQ20250708001', 2, '2025-07-08 04:05:02', 'Terminated', 1, '2025-07-08 05:46:12', '2025-07-08 06:30:53', 1, NULL, NULL, NULL, 'FORCE TERMINATED by Owner User on 2025-07-08 13:30:53 | Reason: asdasdasdasdasasd', '2025-07-07 21:05:02', '2025-07-08 06:30:53'),
(3, 'REQ20250708002', 2, '2025-07-08 05:25:15', 'Terminated', 1, '2025-07-08 05:45:39', '2025-07-08 06:30:27', 1, NULL, NULL, NULL, 'FORCE TERMINATED by Owner User on 2025-07-08 13:30:27 | Reason: asdasdasdasdad', '2025-07-07 22:25:15', '2025-07-08 06:30:27'),
(4, 'REQ20250708003', 2, '2025-07-08 06:03:01', 'Terminated', 1, '2025-07-08 06:03:49', '2025-07-08 06:30:32', 1, NULL, NULL, NULL, 'FORCE TERMINATED by Owner User on 2025-07-08 13:30:32 | Reason: asdasdasdasdasd', '2025-07-07 23:03:01', '2025-07-08 06:30:32'),
(5, 'REQ20250708004', 2, '2025-07-08 06:12:29', 'Terminated', 1, '2025-07-08 06:30:23', '2025-07-08 06:30:45', 1, NULL, NULL, NULL, 'FORCE TERMINATED by Owner User on 2025-07-08 13:30:45 | Reason: asdasdasdasdasdasd', '2025-07-07 23:12:29', '2025-07-08 06:30:45'),
(6, 'REQ20250708005', 2, '2025-07-08 06:31:07', 'Terminated', 1, '2025-07-08 06:39:07', '2025-07-08 06:30:37', 1, NULL, NULL, NULL, 'FORCE TERMINATED by Owner User on 2025-07-08 13:30:37 | Reason: adasdasdasdasdasdad', '2025-07-07 23:31:07', '2025-07-08 06:30:37'),
(7, 'REQ20250708006', 2, '2025-07-08 06:44:25', 'Terminated', 1, '2025-07-08 06:44:55', '2025-07-08 06:30:14', 1, NULL, NULL, NULL, 'FORCE TERMINATED by Owner User on 2025-07-08 13:30:14 | Reason: asdasdasdasda', '2025-07-07 23:44:25', '2025-07-08 06:30:14'),
(8, 'REQ20250708007', 2, '2025-07-08 07:03:03', 'Completed', 1, '2025-07-08 07:03:34', NULL, NULL, '2025-07-08 06:25:03', 1, NULL, 'WALAWEEEEEE', '2025-07-08 00:03:03', '2025-07-08 06:25:03'),
(9, 'REQ20250708008', 2, '2025-07-08 13:26:30', 'Terminated', 1, '2025-07-08 13:27:14', '2025-07-08 06:30:07', 1, NULL, NULL, NULL, 'FORCE TERMINATED by Owner User on 2025-07-08 13:30:07 | Reason: asdasdasdasdasdad', '2025-07-08 06:26:30', '2025-07-08 06:30:07'),
(10, 'REQ20250708009', 2, '2025-07-08 13:44:00', 'Completed', 1, '2025-07-08 13:44:30', NULL, NULL, '2025-07-08 06:52:37', 1, NULL, '', '2025-07-08 06:44:00', '2025-07-08 06:52:37'),
(11, 'REQ20250708010', 2, '2025-07-08 13:50:37', 'Completed', 1, '2025-07-08 13:51:08', NULL, NULL, '2025-07-08 07:04:09', 1, NULL, 'Added 1 strategic items by Owner.', '2025-07-08 06:50:37', '2025-07-15 08:39:47'),
(12, 'REQ20250708011', 2, '2025-07-08 14:49:49', 'Completed', 1, '2025-07-08 14:50:24', NULL, NULL, '2025-07-08 07:51:07', 1, NULL, 'Quick approval - all items approved as requested', '2025-07-08 07:49:49', '2025-07-09 08:12:45'),
(13, 'REQ20250709001', 2, '2025-07-09 14:28:44', 'Completed', 1, '2025-07-09 14:29:19', NULL, NULL, '2025-07-09 07:30:03', 1, NULL, 'Added 1 strategic items by Owner.', '2025-07-09 07:28:44', '2025-07-09 07:33:50'),
(14, 'REQ20250716001', 2, '2025-07-16 02:13:55', 'Completed', 1, '2025-07-19 04:25:28', NULL, NULL, '2025-07-18 21:25:48', 1, NULL, 'Added 1 strategic items by Owner.', '2025-07-15 19:13:55', '2025-07-18 21:27:01'),
(15, 'REQ20250716002', 2, '2025-07-16 14:40:11', 'Completed', 1, '2025-07-16 14:40:56', NULL, NULL, '2025-07-16 07:41:11', 1, NULL, '', '2025-07-16 07:40:11', '2025-07-16 07:42:13'),
(16, 'REQ20250719001', 2, '2025-07-19 04:29:03', 'Completed', 1, '2025-07-19 04:29:57', NULL, NULL, '2025-07-18 21:30:08', 1, NULL, '', '2025-07-18 21:29:03', '2025-07-24 06:28:10'),
(17, 'REQ20250724001', 2, '2025-07-24 03:06:43', 'Cancelled', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Cancelled by warehouse staff', '2025-07-23 20:06:43', '2025-07-24 19:47:57'),
(18, 'REQ20250724002', 2, '2025-07-24 03:11:15', 'Rejected', 1, '2025-07-25 02:35:04', NULL, NULL, NULL, NULL, NULL, 'Cek', '2025-07-23 20:11:15', '2025-07-24 19:35:04'),
(19, 'REQ20250724003', 2, '2025-07-24 03:12:20', 'Rejected', 1, '2025-07-25 02:35:16', NULL, NULL, NULL, NULL, NULL, 'CEK', '2025-07-23 20:12:20', '2025-07-24 19:35:16'),
(20, 'REQ20250724004', 2, '2025-07-24 13:24:19', 'Rejected', 1, '2025-07-25 02:35:12', NULL, NULL, NULL, NULL, NULL, 'CEK', '2025-07-24 06:24:19', '2025-07-24 19:35:12'),
(21, 'REQ20250725001', 2, '2025-07-25 04:14:17', 'Cancelled', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Cancelled by warehouse staff', '2025-07-24 21:14:17', '2025-07-24 21:14:24'),
(22, 'REQ20250728001', 2, '2025-07-28 03:42:41', 'Completed', 1, '2025-07-28 04:05:44', NULL, NULL, '2025-07-27 21:06:03', 1, NULL, 'Quick approval - all items approved as requested', '2025-07-27 20:42:41', '2025-07-27 21:24:50'),
(23, 'REQ20250728002', 2, '2025-07-28 04:25:16', 'Cancelled', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Cancelled by warehouse staff', '2025-07-27 21:25:16', '2025-07-28 05:48:01'),
(24, 'REQ20250729001', 2, '2025-07-29 14:08:39', 'Approved', 1, '2025-07-29 14:20:01', NULL, NULL, NULL, NULL, NULL, 'Added 2 strategic items by Owner.', '2025-07-29 07:08:39', '2025-07-29 07:20:01'),
(25, 'REQ20250729002', 2, '2025-07-29 14:26:33', 'Rejected', 1, '2025-07-29 14:28:28', NULL, NULL, NULL, NULL, NULL, 'item test', '2025-07-29 07:26:33', '2025-07-29 07:28:28'),
(26, 'REQ20250729003', 2, '2025-07-29 14:26:45', 'Ordered', 1, '2025-07-29 14:27:04', NULL, NULL, '2025-07-29 07:32:57', 1, NULL, 'Quick approval - all items approved as requested', '2025-07-29 07:26:45', '2025-07-29 07:32:57'),
(27, 'REQ20250729004', 2, '2025-07-29 14:35:05', 'Completed', 1, '2025-07-30 01:52:25', NULL, NULL, '2025-07-29 19:15:00', 1, NULL, 'Quick approval - all items approved as requested', '2025-07-29 07:35:05', '2025-08-01 23:28:41'),
(28, 'REQ20250730001', 2, '2025-07-30 01:53:09', 'Pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-07-29 18:53:09', '2025-07-29 18:53:09'),
(29, 'REQ20250802001', 2, '2025-08-02 06:26:46', 'Completed', 1, '2025-08-02 06:35:49', NULL, NULL, '2025-08-01 23:36:10', 1, NULL, 'Added 1 strategic items by Owner.', '2025-08-01 23:26:46', '2025-08-01 23:37:10');

-- --------------------------------------------------------

--
-- Table structure for table `restock_request_detail`
--

CREATE TABLE `restock_request_detail` (
  `id_request_detail` bigint(20) UNSIGNED NOT NULL,
  `id_request` bigint(20) UNSIGNED NOT NULL,
  `id_barang` bigint(20) UNSIGNED NOT NULL,
  `qty_request` int(11) NOT NULL,
  `qty_approved` int(11) DEFAULT NULL,
  `estimasi_harga` decimal(12,2) DEFAULT NULL,
  `alasan_request` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `restock_request_detail`
--

INSERT INTO `restock_request_detail` (`id_request_detail`, `id_request`, `id_barang`, `qty_request`, `qty_approved`, `estimasi_harga`, `alasan_request`, `created_at`, `updated_at`) VALUES
(1, 1, 4, 67, NULL, 2177500.00, 'Stock below ROP (14) - EOQ calculation suggests 67 units', '2025-07-06 22:02:52', '2025-07-06 22:02:52'),
(2, 2, 4, 67, 67, 2177500.00, 'Stock below ROP (14) - EOQ calculation suggests 67 units', '2025-07-07 21:05:02', '2025-07-07 22:46:12'),
(3, 3, 5, 72, 72, 2016000.00, 'Stock below ROP (14) - EOQ calculation suggests 72 units', '2025-07-07 22:25:15', '2025-07-07 22:45:39'),
(4, 4, 4, 67, 67, 2177500.00, 'Stock below ROP (14) - EOQ calculation suggests 67 units', '2025-07-07 23:03:01', '2025-07-07 23:03:49'),
(5, 4, 5, 72, 72, 2016000.00, 'Stock below ROP (14) - EOQ calculation suggests 72 units', '2025-07-07 23:03:01', '2025-07-07 23:03:49'),
(6, 5, 4, 67, 67, 2177500.00, 'Stock below ROP (14) - EOQ calculation suggests 67 units', '2025-07-07 23:12:29', '2025-07-07 23:30:22'),
(7, 6, 4, 67, 67, 2177500.00, 'Stock below ROP (14) - EOQ calculation suggests 67 units', '2025-07-07 23:31:07', '2025-07-07 23:39:07'),
(8, 7, 4, 67, 67, 2177500.00, 'Stock below ROP (14) - EOQ calculation suggests 67 units', '2025-07-07 23:44:25', '2025-07-07 23:44:55'),
(9, 8, 4, 67, 67, 2177500.00, 'WHWHWHWH', '2025-07-08 00:03:03', '2025-07-08 00:03:34'),
(10, 9, 4, 67, 67, 2177500.00, 'Stock below ROP (14) - EOQ calculation suggests 67 units', '2025-07-08 06:26:30', '2025-07-08 06:27:14'),
(11, 10, 4, 67, 67, 2177500.00, 'Stock below ROP (14) - EOQ calculation suggests 67 units', '2025-07-08 06:44:00', '2025-07-08 06:44:30'),
(12, 11, 4, 67, 67, 2177500.00, 'Stock below ROP (14) - EOQ calculation suggests 67 units', '2025-07-08 06:50:37', '2025-07-08 06:51:08'),
(13, 11, 6, 12, 12, 660000.00, 'Additional item added by Owner during approval', '2025-07-08 06:51:08', '2025-07-08 06:51:08'),
(14, 12, 5, 72, 72, 2016000.00, 'Stock below ROP (14) - EOQ calculation suggests 72 units', '2025-07-08 07:49:49', '2025-07-08 07:50:24'),
(15, 13, 5, 72, 72, 2016000.00, 'Stock below ROP (14) - EOQ calculation suggests 72 units', '2025-07-09 07:28:44', '2025-07-09 07:29:19'),
(16, 13, 6, 1, 1, 55000.00, 'Additional item added by Owner during approval', '2025-07-09 07:29:19', '2025-07-09 07:29:19'),
(17, 14, 11, 18, 8, 1220160.00, 'Stock below ROP - EOQ calculation', '2025-07-15 19:13:55', '2025-07-18 21:25:28'),
(18, 15, 11, 18, 10, 1525200.00, 'Stock below ROP - EOQ calculation', '2025-07-16 07:40:11', '2025-07-16 07:40:56'),
(19, 14, 6, 3, 3, 165000.00, 'Additional item added by Owner during approval', '2025-07-18 21:25:28', '2025-07-18 21:25:28'),
(20, 16, 3, 65, 6, 156000.00, 'Stock below ROP - EOQ calculation', '2025-07-18 21:29:03', '2025-07-18 21:29:57'),
(21, 16, 11, 18, 2, 305040.00, 'Stock below ROP - EOQ calculation', '2025-07-18 21:29:03', '2025-07-18 21:29:57'),
(22, 17, 18, 126, NULL, 5670000.00, '🚨 CRITICAL: Oli Mesin SAE 10W-40 has critical stock level (0). Immediate restock required to avoid service disruption.', '2025-07-23 20:06:43', '2025-07-23 20:06:43'),
(23, 18, 18, 126, NULL, 5670000.00, '🚨 CRITICAL + HIGH DEMAND: Oli Mesin SAE 10W-40 has critical stock (0) with high demand pattern. IMMEDIATE restock required - this should be Owner\'s TOP PRIORITY to avoid major service disruption.', '2025-07-23 20:11:16', '2025-07-23 20:11:16'),
(24, 19, 3, 65, NULL, 1690000.00, '⚠️ HIGH PRIORITY + MEDIUM DEMAND: Minyak Rem Jumbo below reorder point with moderate demand. Plan restock within 3-5 days.', '2025-07-23 20:12:20', '2025-07-23 20:12:20'),
(25, 20, 18, 126, NULL, 5670000.00, '🚨 CRITICAL + HIGH DEMAND: Oli Mesin SAE 10W-40 has critical stock (0) with high demand pattern. IMMEDIATE restock required - this should be Owner\'s TOP PRIORITY to avoid major service disruption.', '2025-07-24 06:24:19', '2025-07-24 06:24:19'),
(26, 20, 17, 128, NULL, 4480000.00, '⚠️ CRITICAL + MEDIUM DEMAND: Filter Oli Toyota Avanza/Xenia has critical stock (0) with moderate demand. Urgent restock needed within 24 hours.', '2025-07-24 06:24:19', '2025-07-24 06:24:19'),
(27, 20, 11, 18, NULL, 2745360.00, '⚠️ HIGH PRIORITY + MEDIUM DEMAND: Prima XP 30w 50 below reorder point with moderate demand. Plan restock within 3-5 days.', '2025-07-24 06:24:19', '2025-07-24 06:24:19'),
(28, 20, 3, 65, NULL, 1690000.00, '⚠️ HIGH PRIORITY + MEDIUM DEMAND: Minyak Rem Jumbo below reorder point with moderate demand. Plan restock within 3-5 days.', '2025-07-24 06:24:19', '2025-07-24 06:24:19'),
(29, 21, 18, 126, NULL, 5670000.00, '🚨 CRITICAL + HIGH DEMAND: Oli Mesin SAE 10W-40 has critical stock (0) with high demand pattern. IMMEDIATE restock required - this should be Owner\'s TOP PRIORITY to avoid major service disruption.', '2025-07-24 21:14:17', '2025-07-24 21:14:17'),
(30, 22, 18, 126, 126, 5670000.00, '🔥 HIGH PRIORITY + HIGH DEMAND: Oli Mesin SAE 10W-40 is below reorder point with high demand. Restock within 24-48 hours to prevent stockout.', '2025-07-27 20:42:41', '2025-07-27 21:05:44'),
(31, 23, 11, 18, NULL, 2745360.00, '⚠️ HIGH PRIORITY + MEDIUM DEMAND: Prima XP 30w 50 below reorder point with moderate demand. Plan restock within 3-5 days.', '2025-07-27 21:25:16', '2025-07-27 21:25:16'),
(32, 24, 18, 12, 10, 450000.00, '🔥 HIGH PRIORITY + HIGH DEMAND: Oli Mesin SAE 10W-40 is below reorder point with high demand. Restock within 24-48 hours to prevent stockout.', '2025-07-29 07:08:39', '2025-07-29 07:20:01'),
(33, 24, 17, 1, 1, 35000.00, 'Additional item added by Owner during approval', '2025-07-29 07:20:01', '2025-07-29 07:20:01'),
(34, 24, 12, 1, 1, 175500.00, 'Additional item added by Owner during approval', '2025-07-29 07:20:01', '2025-07-29 07:20:01'),
(35, 25, 11, 1, NULL, 152520.00, '⚠️ HIGH PRIORITY + MEDIUM DEMAND: Prima XP 30w 50 below reorder point with moderate demand. Plan restock within 3-5 days.', '2025-07-29 07:26:33', '2025-07-29 07:26:33'),
(36, 26, 17, 1, 1, 35000.00, '⚠️ HIGH PRIORITY + MEDIUM DEMAND: Filter Oli Toyota Avanza/Xenia below reorder point with moderate demand. Plan restock within 3-5 days.', '2025-07-29 07:26:45', '2025-07-29 07:27:04'),
(37, 27, 18, 1, 1, 45000.00, '🔥 HIGH PRIORITY + HIGH DEMAND: Oli Mesin SAE 10W-40 is below reorder point with high demand. Restock within 24-48 hours to prevent stockout.', '2025-07-29 07:35:05', '2025-07-29 18:52:25'),
(38, 28, 18, 12, NULL, 540000.00, '🔥 HIGH PRIORITY + HIGH DEMAND: Oli Mesin SAE 10W-40 is below reorder point with high demand. Restock within 24-48 hours to prevent stockout.', '2025-07-29 18:53:09', '2025-07-29 18:53:09'),
(39, 29, 17, 128, 50, 1750000.00, '⚠️ CRITICAL + MEDIUM DEMAND: Filter Oli Toyota Avanza/Xenia has critical stock (0) with moderate demand. Urgent restock needed within 24 hours.', '2025-08-01 23:26:46', '2025-08-01 23:35:49'),
(40, 29, 6, 1, 1, 55000.00, 'Additional item added by Owner during approval', '2025-08-01 23:35:49', '2025-08-01 23:35:49');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_role` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `nama_role`, `created_at`, `updated_at`) VALUES
(1, 'Owner', '2025-06-30 08:32:00', '2025-06-30 08:32:00'),
(2, 'Gudang', '2025-06-30 08:32:00', '2025-06-30 08:32:00'),
(3, 'Service Advisor', '2025-06-30 08:32:00', '2025-06-30 08:32:00');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('d3h7BKPiIc5YtXQQKXxxOnI3pnGY6RbB0UeEtXFL', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoiaGNvb3lJT1ducHkwaVlrUG5Oejc1dzU2S2kzd3RqTWFLWHQ2MmxZdyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1753670039),
('LIwalcHWsSyL4pcwQDXajQ2iXL8KdQk0105Z7HOR', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiQVpNc1ppaDFhRG9zVkRDeVlta0p6U0hBNW5KSW9CVjNwMVhScWdZUiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJuZXciO2E6MDp7fXM6Mzoib2xkIjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzg6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9kYXNoYm9hcmQtZ3VkYW5nIjt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6Mjt9', 1753517168);

-- --------------------------------------------------------

--
-- Table structure for table `stok`
--

CREATE TABLE `stok` (
  `id_stok` bigint(20) UNSIGNED NOT NULL,
  `id_barang` bigint(20) UNSIGNED NOT NULL,
  `jumlah_stok` int(11) NOT NULL DEFAULT 0,
  `status_stok` enum('Aman','Perlu Restock','Habis') NOT NULL DEFAULT 'Aman',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stok`
--

INSERT INTO `stok` (`id_stok`, `id_barang`, `jumlah_stok`, `status_stok`, `created_at`, `updated_at`) VALUES
(1, 1, 42, 'Aman', '2025-07-04 08:44:55', '2025-07-04 20:07:50'),
(2, 2, 38, 'Aman', '2025-07-04 08:44:55', '2025-07-18 21:33:00'),
(3, 3, 11, 'Aman', '2025-07-04 08:44:55', '2025-08-03 07:58:10'),
(4, 4, 13, 'Perlu Restock', '2025-07-04 08:44:55', '2025-07-29 08:44:03'),
(5, 5, 148, 'Aman', '2025-07-04 08:44:55', '2025-07-29 08:33:54'),
(6, 6, 65, 'Aman', '2025-07-04 08:44:55', '2025-08-01 23:37:10'),
(11, 11, 4, 'Perlu Restock', '2025-07-13 21:39:50', '2025-07-24 06:28:10'),
(12, 12, 0, 'Habis', '2025-07-14 17:44:57', '2025-07-29 07:55:18'),
(15, 15, 2, 'Aman', '2025-07-15 08:10:50', '2025-07-29 09:31:25'),
(16, 16, 8, 'Aman', '2025-07-15 08:15:25', '2025-08-03 07:58:10'),
(17, 17, 40, 'Aman', '2025-07-21 19:43:18', '2025-08-01 23:37:10'),
(18, 18, 15, 'Perlu Restock', '2025-07-21 19:46:41', '2025-08-01 23:28:41');

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `id_transaksi` bigint(20) UNSIGNED NOT NULL,
  `nomor_transaksi` varchar(255) NOT NULL,
  `tanggal_transaksi` datetime NOT NULL,
  `id_user` bigint(20) UNSIGNED NOT NULL,
  `nama_customer` varchar(255) DEFAULT NULL,
  `kendaraan` varchar(255) DEFAULT NULL,
  `total_harga` decimal(12,2) NOT NULL,
  `jenis_transaksi` varchar(100) NOT NULL,
  `status_transaksi` enum('Progress','Selesai','Dibatalkan') NOT NULL DEFAULT 'Progress',
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transaksi`
--

INSERT INTO `transaksi` (`id_transaksi`, `nomor_transaksi`, `tanggal_transaksi`, `id_user`, `nama_customer`, `kendaraan`, `total_harga`, `jenis_transaksi`, `status_transaksi`, `keterangan`, `created_at`, `updated_at`) VALUES
(1, 'TRX20250712001', '2025-07-12 01:51:20', 3, 'Jokoo', 'Porche 911 GT 3', 62000.00, 'Service Berkala', 'Selesai', 'Transaksi selesai', '2025-07-11 18:51:20', '2025-07-11 19:17:08'),
(2, 'TRX20250712002', '2025-07-12 02:18:02', 3, 'Uki', 'Vespa', 0.00, 'Service Berkala', 'Selesai', 'Transaksi selesai', '2025-07-11 19:18:02', '2025-07-11 19:19:52'),
(3, 'TRX20250712003', '2025-07-12 02:21:39', 3, 'Noto', 'Yamaha NMAX', 37000.00, 'Service Berkala', 'Selesai', 'Transaksi selesai', '2025-07-11 19:21:39', '2025-07-11 19:28:19'),
(4, 'TRX20250716001', '2025-07-16 14:37:37', 3, 'Piki piks', 'Proche', 250000.00, 'Ganti Oli', 'Selesai', 'Transaksi selesai', '2025-07-16 07:37:37', '2025-07-16 07:38:27'),
(5, 'TRX20250719001', '2025-07-19 04:31:25', 3, 'Semenep', 'Vespa Bebek', 32000.00, 'Ganti Oli', 'Selesai', 'Transaksi selesai', '2025-07-18 21:31:25', '2025-07-18 21:33:00'),
(6, 'TRX20250725001', '2025-07-25 04:34:39', 3, 'Gab', 'Porche 911 GT 3', 1037000.00, 'Service Berkala', 'Selesai', 'Transaksi selesai', '2025-07-24 21:34:39', '2025-07-24 21:48:19'),
(7, 'TRX20250728001', '2025-07-28 04:32:12', 3, 'Jokoo', 'Vespa', 500000.00, 'Service Berkala', 'Selesai', 'Transaksi selesai', '2025-07-27 21:32:12', '2025-07-29 06:10:59'),
(8, 'TRX20250728002', '2025-07-28 07:07:04', 7, 'Jokoo', 'Porche 911 GT 3', 37000.00, 'Tune Up', 'Selesai', 'Transaksi selesai', '2025-07-28 00:07:04', '2025-07-28 00:08:34'),
(9, 'TRX20250729001', '2025-07-29 05:17:24', 7, 'asdasd', 'asd', 0.00, 'Service Berkala', 'Selesai', 'Transaksi selesai', '2025-07-28 22:17:24', '2025-07-29 08:34:02'),
(10, 'TRX20250729002', '2025-07-29 15:01:33', 7, 'asda', 'asdasdads', 0.00, 'Service Berkala', 'Selesai', 'Transaksi selesai', '2025-07-29 08:01:33', '2025-07-29 08:33:59'),
(11, 'TRX20250729003', '2025-07-29 15:03:44', 7, 'asda', 'asdasd', 124000.00, 'Service Berkala', 'Selesai', 'Transaksi selesai', '2025-07-29 08:03:44', '2025-07-29 08:33:54'),
(12, 'TRX20250729004', '2025-07-29 15:41:45', 7, '123123', '123123', 37000.00, 'Service Berkala', 'Selesai', 'Transaksi selesai', '2025-07-29 08:41:45', '2025-07-29 08:44:03'),
(13, 'TRX20250729005', '2025-07-29 16:25:57', 7, 'Jokoo', 'Porche 911 GT 3', 1000000.00, 'Service Berkala', 'Selesai', 'Transaksi selesai', '2025-07-29 09:25:57', '2025-07-29 09:31:25'),
(14, 'TRX20250802001', '2025-08-02 06:30:16', 7, 'gid', 'Vespa', 530000.00, 'Ganti Oli', 'Selesai', 'Transaksi selesai', '2025-08-01 23:30:16', '2025-08-03 07:58:10'),
(15, 'TRX20250803001', '2025-08-03 14:23:56', 7, 'asdas', 'asdasd', 0.00, 'Service Berkala', 'Dibatalkan', 'Transaksi dibatalkan oleh Service Advisor pada 03/08/2025 14:57', '2025-08-03 07:23:56', '2025-08-03 07:57:58');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `address`, `is_active`, `email_verified_at`, `password`, `role_id`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Owner User', 'owner@mail.com', NULL, NULL, 1, NULL, '$2y$12$PVvinuZKCPprroOK18TvB.1MLWOJv/ZeEoFR9TeBI8wdnTPW9RHb2', 1, 'Mi8aNvck4vt0D9XI076mMimTQyzwXWdoJ7oDeNuDHPKU1KpyhxqGPtvE1wsO', '2025-06-30 08:32:00', '2025-06-30 08:32:00'),
(2, 'Gudang User', 'gudang@mail.com', NULL, NULL, 1, NULL, '$2y$12$2O5sBmC9Y.RkJhSvxRXeT.e.2rSq72LKBuqq3jwgeIP7fyHVJTp96', 2, 'ZjiWN71lM7XTFXsIBL07f0W2e8Ovy8ukLrwTvCyjVAwAB8a6fjLV0kfNpciv', '2025-06-30 08:32:01', '2025-06-30 08:32:01'),
(3, 'Kasir User', 'kasir@mail.com', NULL, NULL, 1, NULL, '$2y$12$iHadwOj9JNnmTT.H4ZLzludGAts8BrHOc3BH5Vm8Y8ez91yPy7oNS', 3, NULL, '2025-06-30 08:32:01', '2025-06-30 08:32:01'),
(4, 'Test User', 'test@example.com', '081234567890', 'Test Address', 1, NULL, '$2y$12$WbIOaxj.dhNGDuraU6/ofejURg38bGk2uNmIIm6j0/9RB6HKs6xR2', 1, NULL, '2025-07-18 23:26:56', '2025-07-18 23:26:56'),
(5, 'Samuel', 'sam@mail.com', '0839434343', 'Alamtt', 0, NULL, '$2y$12$wmtLNyRFALH9N0ZrVvi4BejhGhm98v6of7q8Ch8R8Yh4mOa8EHdwC', 1, NULL, '2025-07-18 23:38:59', '2025-07-18 23:42:38'),
(6, 'Samsul', 'Sul@mail.com', '123123123123', 'Alamatt', 1, NULL, '$2y$12$8wVw85VuhprmI0cYAahY5e44FxNY/cTjh5Iie2Ir8d74R3dxw0BQu', 1, NULL, '2025-07-18 23:39:37', '2025-07-18 23:41:50'),
(7, 'Service Advisor', 'serv@mail.com', '1231231231', 'GHEHEY', 1, NULL, '$2y$12$y5ZIDEs7nbh/1pFxOVJJ9uI1z5jH6egqsJVdEvmhYFhVbOjI30l2e', 3, NULL, '2025-07-24 22:54:37', '2025-07-24 22:54:37');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `barang`
--
ALTER TABLE `barang`
  ADD PRIMARY KEY (`id_barang`),
  ADD UNIQUE KEY `barang_kode_barang_unique` (`kode_barang_old`),
  ADD UNIQUE KEY `barang_kode_internal_unique` (`kode_barang`),
  ADD KEY `idx_eoq_params` (`annual_demand`,`ordering_cost`,`holding_cost`),
  ADD KEY `idx_eoq_results` (`eoq_calculated`,`rop_calculated`),
  ADD KEY `idx_last_calculation` (`last_eoq_calculation`),
  ADD KEY `barang_id_kategori_foreign` (`id_kategori`);

--
-- Indexes for table `barang_masuk`
--
ALTER TABLE `barang_masuk`
  ADD PRIMARY KEY (`id_masuk`),
  ADD UNIQUE KEY `barang_masuk_nomor_masuk_unique` (`nomor_masuk`),
  ADD KEY `barang_masuk_id_request_foreign` (`id_request`),
  ADD KEY `barang_masuk_id_user_gudang_foreign` (`id_user_gudang`),
  ADD KEY `barang_masuk_nomor_masuk_index` (`nomor_masuk`),
  ADD KEY `barang_masuk_tanggal_masuk_index` (`tanggal_masuk`),
  ADD KEY `barang_masuk_jenis_masuk_index` (`jenis_masuk`);

--
-- Indexes for table `barang_masuk_detail`
--
ALTER TABLE `barang_masuk_detail`
  ADD PRIMARY KEY (`id_masuk_detail`),
  ADD KEY `barang_masuk_detail_id_barang_foreign` (`id_barang`),
  ADD KEY `barang_masuk_detail_id_masuk_id_barang_index` (`id_masuk`,`id_barang`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `detail_transaksi`
--
ALTER TABLE `detail_transaksi`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `detail_transaksi_id_barang_foreign` (`id_barang`),
  ADD KEY `detail_transaksi_id_transaksi_id_barang_index` (`id_transaksi`,`id_barang`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kategori_barang`
--
ALTER TABLE `kategori_barang`
  ADD PRIMARY KEY (`id_kategori`),
  ADD UNIQUE KEY `kategori_barang_kode_kategori_unique` (`kode_kategori`);

--
-- Indexes for table `log_stok`
--
ALTER TABLE `log_stok`
  ADD PRIMARY KEY (`id_log`),
  ADD KEY `log_stok_id_user_foreign` (`id_user`),
  ADD KEY `log_stok_id_barang_index` (`id_barang`),
  ADD KEY `log_stok_tanggal_log_index` (`tanggal_log`),
  ADD KEY `log_stok_jenis_perubahan_index` (`jenis_perubahan`),
  ADD KEY `log_stok_referensi_tipe_referensi_id_index` (`referensi_tipe`,`referensi_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `restock_request`
--
ALTER TABLE `restock_request`
  ADD PRIMARY KEY (`id_request`),
  ADD UNIQUE KEY `restock_request_nomor_request_unique` (`nomor_request`),
  ADD KEY `restock_request_id_user_gudang_foreign` (`id_user_gudang`),
  ADD KEY `restock_request_id_user_approved_foreign` (`id_user_approved`),
  ADD KEY `restock_request_nomor_request_index` (`nomor_request`),
  ADD KEY `restock_request_status_request_index` (`status_request`),
  ADD KEY `restock_request_tanggal_request_index` (`tanggal_request`),
  ADD KEY `restock_request_id_user_terminated_foreign` (`id_user_terminated`),
  ADD KEY `restock_request_id_user_ordered_foreign` (`id_user_ordered`);

--
-- Indexes for table `restock_request_detail`
--
ALTER TABLE `restock_request_detail`
  ADD PRIMARY KEY (`id_request_detail`),
  ADD KEY `restock_request_detail_id_barang_foreign` (`id_barang`),
  ADD KEY `restock_request_detail_id_request_id_barang_index` (`id_request`,`id_barang`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `stok`
--
ALTER TABLE `stok`
  ADD PRIMARY KEY (`id_stok`),
  ADD KEY `stok_id_barang_index` (`id_barang`);

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id_transaksi`),
  ADD UNIQUE KEY `transaksi_nomor_transaksi_unique` (`nomor_transaksi`),
  ADD KEY `transaksi_id_user_foreign` (`id_user`),
  ADD KEY `transaksi_nomor_transaksi_index` (`nomor_transaksi`),
  ADD KEY `transaksi_tanggal_transaksi_index` (`tanggal_transaksi`),
  ADD KEY `transaksi_status_transaksi_index` (`status_transaksi`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_role_id_index` (`role_id`),
  ADD KEY `users_is_active_index` (`is_active`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `barang`
--
ALTER TABLE `barang`
  MODIFY `id_barang` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `barang_masuk`
--
ALTER TABLE `barang_masuk`
  MODIFY `id_masuk` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `barang_masuk_detail`
--
ALTER TABLE `barang_masuk_detail`
  MODIFY `id_masuk_detail` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `detail_transaksi`
--
ALTER TABLE `detail_transaksi`
  MODIFY `id_detail` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- AUTO_INCREMENT for table `kategori_barang`
--
ALTER TABLE `kategori_barang`
  MODIFY `id_kategori` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `log_stok`
--
ALTER TABLE `log_stok`
  MODIFY `id_log` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `restock_request`
--
ALTER TABLE `restock_request`
  MODIFY `id_request` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `restock_request_detail`
--
ALTER TABLE `restock_request_detail`
  MODIFY `id_request_detail` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `stok`
--
ALTER TABLE `stok`
  MODIFY `id_stok` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id_transaksi` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `barang`
--
ALTER TABLE `barang`
  ADD CONSTRAINT `barang_id_kategori_foreign` FOREIGN KEY (`id_kategori`) REFERENCES `kategori_barang` (`id_kategori`) ON DELETE SET NULL;

--
-- Constraints for table `barang_masuk`
--
ALTER TABLE `barang_masuk`
  ADD CONSTRAINT `barang_masuk_id_request_foreign` FOREIGN KEY (`id_request`) REFERENCES `restock_request` (`id_request`) ON DELETE SET NULL,
  ADD CONSTRAINT `barang_masuk_id_user_gudang_foreign` FOREIGN KEY (`id_user_gudang`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `barang_masuk_detail`
--
ALTER TABLE `barang_masuk_detail`
  ADD CONSTRAINT `barang_masuk_detail_id_barang_foreign` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id_barang`) ON DELETE CASCADE,
  ADD CONSTRAINT `barang_masuk_detail_id_masuk_foreign` FOREIGN KEY (`id_masuk`) REFERENCES `barang_masuk` (`id_masuk`) ON DELETE CASCADE;

--
-- Constraints for table `detail_transaksi`
--
ALTER TABLE `detail_transaksi`
  ADD CONSTRAINT `detail_transaksi_id_barang_foreign` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id_barang`) ON DELETE CASCADE,
  ADD CONSTRAINT `detail_transaksi_id_transaksi_foreign` FOREIGN KEY (`id_transaksi`) REFERENCES `transaksi` (`id_transaksi`) ON DELETE CASCADE;

--
-- Constraints for table `log_stok`
--
ALTER TABLE `log_stok`
  ADD CONSTRAINT `log_stok_id_barang_foreign` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id_barang`) ON DELETE CASCADE,
  ADD CONSTRAINT `log_stok_id_user_foreign` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `restock_request`
--
ALTER TABLE `restock_request`
  ADD CONSTRAINT `restock_request_id_user_approved_foreign` FOREIGN KEY (`id_user_approved`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `restock_request_id_user_gudang_foreign` FOREIGN KEY (`id_user_gudang`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `restock_request_id_user_ordered_foreign` FOREIGN KEY (`id_user_ordered`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `restock_request_id_user_terminated_foreign` FOREIGN KEY (`id_user_terminated`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `restock_request_detail`
--
ALTER TABLE `restock_request_detail`
  ADD CONSTRAINT `restock_request_detail_id_barang_foreign` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id_barang`) ON DELETE CASCADE,
  ADD CONSTRAINT `restock_request_detail_id_request_foreign` FOREIGN KEY (`id_request`) REFERENCES `restock_request` (`id_request`) ON DELETE CASCADE;

--
-- Constraints for table `stok`
--
ALTER TABLE `stok`
  ADD CONSTRAINT `stok_id_barang_foreign` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id_barang`) ON DELETE CASCADE;

--
-- Constraints for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `transaksi_id_user_foreign` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
