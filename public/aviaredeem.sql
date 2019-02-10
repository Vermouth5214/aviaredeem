-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 10, 2019 at 01:04 PM
-- Server version: 10.1.36-MariaDB
-- PHP Version: 7.2.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `aviaredeem`
--

-- --------------------------------------------------------

--
-- Table structure for table `access_control`
--

CREATE TABLE `access_control` (
  `id` int(11) NOT NULL,
  `user_level_id` int(11) DEFAULT NULL,
  `module_id` int(11) DEFAULT NULL,
  `content` text,
  `user_modified` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `access_control`
--

INSERT INTO `access_control` (`id`, `user_level_id`, `module_id`, `content`, `user_modified`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'a', 1, '2018-10-10 02:28:44', '2018-10-10 02:28:44'),
(2, 1, 2, 'a', 1, '2018-10-10 02:28:44', '2018-10-10 02:28:44'),
(3, 1, 3, 'a', 1, '2018-10-10 02:28:44', '2018-10-10 02:28:44'),
(4, 2, 1, 'a', 1, '2018-10-10 02:28:49', '2018-10-10 02:28:49'),
(5, 2, 2, 'a', 1, '2018-10-10 02:28:49', '2018-10-10 02:28:49'),
(6, 2, 3, 'a', 1, '2018-10-10 02:28:49', '2018-10-10 02:28:49'),
(7, 3, 1, 'v', 1, '2018-10-10 02:28:54', '2018-10-10 02:28:54'),
(8, 3, 2, 'v', 1, '2018-10-10 02:28:54', '2018-10-10 02:28:54'),
(9, 3, 3, 'v', 1, '2018-10-10 02:28:54', '2018-10-10 02:28:54');

-- --------------------------------------------------------

--
-- Table structure for table `campaign_d_bagi`
--

CREATE TABLE `campaign_d_bagi` (
  `id` int(11) NOT NULL,
  `id_campaign` int(11) DEFAULT NULL,
  `kode_agen` varchar(30) DEFAULT NULL,
  `id_campaign_d_hadiah` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_modified` varchar(30) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `campaign_d_bagi`
--

INSERT INTO `campaign_d_bagi` (`id`, `id_campaign`, `kode_agen`, `id_campaign_d_hadiah`, `created_at`, `updated_at`, `user_modified`) VALUES
(1, 1, '02A01010001', 10, '2019-02-08 03:09:57', '2019-02-08 03:09:57', 'superadm'),
(2, 1, '02A02010003', 9, '2019-02-08 03:09:57', '2019-02-08 03:09:57', 'superadm'),
(3, 1, '03A01010002', 9, '2019-02-08 03:09:57', '2019-02-08 03:09:57', 'superadm'),
(4, 1, '05A01010002', 9, '2019-02-08 03:09:57', '2019-02-08 03:09:57', 'superadm'),
(5, 1, '06A01010002', 10, '2019-02-08 03:09:57', '2019-02-08 03:09:57', 'superadm'),
(6, 1, '08A01010002', 10, '2019-02-08 03:09:57', '2019-02-08 03:09:57', 'superadm'),
(7, 1, '10A01010001', 10, '2019-02-08 03:09:57', '2019-02-08 03:09:57', 'superadm'),
(8, 1, '10A02010003', 10, '2019-02-08 03:09:57', '2019-02-08 03:09:57', 'superadm'),
(9, 1, '11B02010002', 10, '2019-02-08 03:09:57', '2019-02-08 03:09:57', 'superadm'),
(10, 1, '11B02010006', 10, '2019-02-08 03:09:57', '2019-02-08 03:09:57', 'superadm'),
(11, 1, '13I01010002', 10, '2019-02-08 03:09:57', '2019-02-08 03:09:57', 'superadm'),
(12, 1, '14B02010003', 10, '2019-02-08 03:09:57', '2019-02-08 03:09:57', 'superadm'),
(13, 1, '14F01020002', 10, '2019-02-08 03:09:57', '2019-02-08 03:09:57', 'superadm'),
(14, 1, '15A01010001', 10, '2019-02-08 03:09:57', '2019-02-08 03:09:57', 'superadm'),
(15, 1, '16C02030006', 10, '2019-02-08 03:09:57', '2019-02-08 03:09:57', 'superadm'),
(16, 1, '16C02070012', 10, '2019-02-08 03:09:57', '2019-02-08 03:09:57', 'superadm'),
(17, 1, '16D02040005', 10, '2019-02-08 03:09:57', '2019-02-08 03:09:57', 'superadm'),
(18, 1, '16F01010003', 10, '2019-02-08 03:09:57', '2019-02-08 03:09:57', 'superadm'),
(19, 1, '16G01030001', 10, '2019-02-08 03:09:57', '2019-02-08 03:09:57', 'superadm'),
(20, 1, '16G02010001', 10, '2019-02-08 03:09:57', '2019-02-08 03:09:57', 'superadm'),
(21, 1, '16H01040001', 10, '2019-02-08 03:09:57', '2019-02-08 03:09:57', 'superadm'),
(22, 1, '16H02010001', 10, '2019-02-08 03:09:57', '2019-02-08 03:09:57', 'superadm'),
(23, 1, '17A01030005', 10, '2019-02-08 03:09:57', '2019-02-08 03:09:57', 'superadm'),
(24, 1, '17A01030007', 10, '2019-02-08 03:09:57', '2019-02-08 03:09:57', 'superadm'),
(25, 1, '18A01010001', 10, '2019-02-08 03:09:57', '2019-02-08 03:09:57', 'superadm'),
(26, 1, '18B01010001', 10, '2019-02-08 03:09:57', '2019-02-08 03:09:57', 'superadm'),
(27, 1, '19A14010001', 10, '2019-02-08 03:09:57', '2019-02-08 03:09:57', 'superadm'),
(28, 1, '20A01010001', 10, '2019-02-08 03:09:57', '2019-02-08 03:09:57', 'superadm'),
(29, 1, '22A02010002', 10, '2019-02-08 03:09:57', '2019-02-08 03:09:57', 'superadm'),
(30, 1, '23A01010002', 10, '2019-02-08 03:09:57', '2019-02-08 03:09:57', 'superadm'),
(31, 1, '24A01010002', 10, '2019-02-08 03:09:57', '2019-02-08 03:09:57', 'superadm'),
(32, 1, '24A01010003', 10, '2019-02-08 03:09:57', '2019-02-08 03:09:57', 'superadm'),
(33, 1, '25A01010002', 10, '2019-02-08 03:09:57', '2019-02-08 03:09:57', 'superadm'),
(34, 1, '26A02020008', 10, '2019-02-08 03:09:57', '2019-02-08 03:09:57', 'superadm'),
(35, 1, '26A02020009', 10, '2019-02-08 03:09:57', '2019-02-08 03:09:57', 'superadm'),
(36, 1, '27A01060006', 10, '2019-02-08 03:09:57', '2019-02-08 03:09:57', 'superadm'),
(37, 1, '27A01060009', 10, '2019-02-08 03:09:57', '2019-02-08 03:09:57', 'superadm'),
(38, 1, '27A01060010', 10, '2019-02-08 03:09:57', '2019-02-08 03:09:57', 'superadm'),
(39, 1, '27A01080008', 10, '2019-02-08 03:09:57', '2019-02-08 03:09:57', 'superadm'),
(40, 1, '27A02010011', 10, '2019-02-08 03:09:57', '2019-02-08 03:09:57', 'superadm'),
(41, 1, '29A01020005', 10, '2019-02-08 03:09:57', '2019-02-08 03:09:57', 'superadm'),
(42, 1, '32A04060259', 10, '2019-02-08 03:09:57', '2019-02-08 03:09:57', 'superadm'),
(43, 1, '32A06050262', 10, '2019-02-08 03:09:57', '2019-02-08 03:09:57', 'superadm'),
(44, 1, '32A11040269', 10, '2019-02-08 03:09:57', '2019-02-08 03:09:57', 'superadm');

-- --------------------------------------------------------

--
-- Table structure for table `campaign_d_emas`
--

CREATE TABLE `campaign_d_emas` (
  `id` int(11) NOT NULL,
  `id_campaign` int(11) DEFAULT NULL,
  `kode_catalogue` varchar(30) DEFAULT NULL,
  `kode_hadiah` varchar(30) DEFAULT NULL,
  `nama_hadiah` varchar(100) DEFAULT NULL,
  `jumlah` int(11) DEFAULT NULL,
  `harga` bigint(20) UNSIGNED DEFAULT NULL,
  `satuan` varchar(10) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_modified` varchar(30) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `campaign_d_emas`
--

INSERT INTO `campaign_d_emas` (`id`, `id_campaign`, `kode_catalogue`, `kode_hadiah`, `nama_hadiah`, `jumlah`, `harga`, `satuan`, `created_at`, `updated_at`, `user_modified`) VALUES
(5, 1, 'H12345/006', 'HEMAS5', '5 Gr Emas', 5, 3250000, 'BH', '2019-02-08 03:19:30', '2019-02-08 03:19:30', 'superadm'),
(4, 1, 'H12345/005', 'HEMAS100', '100 Gr Emas', 100, 700000000, 'BH', '2019-02-08 03:19:30', '2019-02-08 03:19:30', 'superadm'),
(6, 1, 'H12345/007', 'HEMAS1', '1 Gr Emas', 1, 650000, 'BH', '2019-02-08 03:19:30', '2019-02-08 03:19:30', 'superadm'),
(14, 2, 'D12345/006', 'HEMAS5', '5 Gr Emas', 5, 3000000, 'BH', '2019-02-09 03:27:08', '2019-02-09 03:27:08', 'superadm'),
(15, 2, 'D12345/007', 'HEMAS1', '1 Gr Emas', 1, 600000, 'BH', '2019-02-09 03:27:08', '2019-02-09 03:27:08', 'superadm'),
(13, 2, 'D12345/005', 'HEMAS100', '100 Gr Emas', 100, 700000000, 'BH', '2019-02-09 03:27:08', '2019-02-09 03:27:08', 'superadm');

-- --------------------------------------------------------

--
-- Table structure for table `campaign_d_hadiah`
--

CREATE TABLE `campaign_d_hadiah` (
  `id` int(11) NOT NULL,
  `id_campaign` int(11) DEFAULT NULL,
  `kode_catalogue` varchar(30) DEFAULT NULL,
  `kode_hadiah` varchar(30) DEFAULT NULL,
  `nama_hadiah` varchar(100) DEFAULT NULL,
  `jumlah` int(11) DEFAULT NULL,
  `harga` bigint(20) UNSIGNED DEFAULT NULL,
  `pilihan` int(1) DEFAULT NULL,
  `emas` int(1) DEFAULT NULL,
  `satuan` varchar(10) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_modified` varchar(30) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `campaign_d_hadiah`
--

INSERT INTO `campaign_d_hadiah` (`id`, `id_campaign`, `kode_catalogue`, `kode_hadiah`, `nama_hadiah`, `jumlah`, `harga`, `pilihan`, `emas`, `satuan`, `created_at`, `updated_at`, `user_modified`) VALUES
(9, 1, 'H12345/002', 'A123456', '2 Voucher Alfa', 2, 500000, 1, 0, 'LBR', '2019-02-08 03:09:33', '2019-02-08 03:09:33', 'superadm'),
(8, 1, 'H12345/001', 'S123456', 'Sarung', 1, 250000, 0, 0, 'BH', '2019-02-08 03:09:33', '2019-02-08 03:09:33', 'superadm'),
(7, 1, '-', '-', '1 Gr Emas', 1, 700000, 0, 1, 'BH', '2019-02-08 03:09:33', '2019-02-08 03:09:33', 'superadm'),
(6, 1, '-', '-', '2 Gr Emas', 2, 1100000, 0, 1, 'BH', '2019-02-08 03:09:33', '2019-02-08 03:09:33', 'superadm'),
(10, 1, 'H12345/003', 'I123456', '2 Voucher Indomaret', 2, 500000, 1, 0, 'LBR', '2019-02-08 03:09:33', '2019-02-08 03:09:33', 'superadm'),
(11, 2, '-', '-', '100 Gr Emas', 100, 7500, 0, 1, 'BH', '2019-02-08 03:26:36', '2019-02-08 03:26:36', 'superadm'),
(12, 2, '-', '-', '5 Gr Emas', 5, 500, 0, 1, 'BH', '2019-02-08 03:26:36', '2019-02-08 03:26:36', 'superadm'),
(13, 2, '-', '-', '1 Gr Emas', 1, 250, 0, 1, 'BH', '2019-02-08 03:26:36', '2019-02-08 03:26:36', 'superadm');

-- --------------------------------------------------------

--
-- Table structure for table `campaign_h`
--

CREATE TABLE `campaign_h` (
  `id` int(11) NOT NULL,
  `kode_campaign` varchar(50) DEFAULT NULL,
  `nama_campaign` varchar(100) DEFAULT NULL,
  `jenis` enum('poin','omzet') DEFAULT NULL,
  `TPP` int(1) DEFAULT NULL,
  `brosur` text,
  `active` int(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_modified` varchar(30) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `campaign_h`
--

INSERT INTO `campaign_h` (`id`, `kode_campaign`, `nama_campaign`, `jenis`, `TPP`, `brosur`, `active`, `created_at`, `updated_at`, `user_modified`) VALUES
(1, 'H12345', 'Nama Campaign 1', 'omzet', 0, 'BROSUR-H12345-1549595262.jpg', 1, '2019-02-08 03:07:43', '2019-02-08 03:19:10', 'superadm'),
(2, 'D12345', 'Nama Campaign Poin', 'poin', 1, 'BROSUR-D12345-1549596396.jpg', 1, '2019-02-08 03:26:36', '2019-02-09 03:32:04', 'superadm');

-- --------------------------------------------------------

--
-- Table structure for table `customer_omzet`
--

CREATE TABLE `customer_omzet` (
  `id` int(11) NOT NULL,
  `kode_campaign` varchar(50) DEFAULT NULL,
  `kode_customer` varchar(50) DEFAULT NULL,
  `periode_awal` date DEFAULT NULL,
  `periode_akhir` date DEFAULT NULL,
  `omzet_tepat_waktu` decimal(13,2) DEFAULT '0.00',
  `disc_pembelian` decimal(10,5) DEFAULT NULL,
  `disc_penjualan` decimal(10,5) DEFAULT NULL,
  `omzet_netto` decimal(13,2) DEFAULT NULL,
  `poin` int(11) DEFAULT '0',
  `active` int(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_modified` varchar(30) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `customer_omzet`
--

INSERT INTO `customer_omzet` (`id`, `kode_campaign`, `kode_customer`, `periode_awal`, `periode_akhir`, `omzet_tepat_waktu`, `disc_pembelian`, `disc_penjualan`, `omzet_netto`, `poin`, `active`, `created_at`, `updated_at`, `user_modified`) VALUES
(1, 'H12345', '02A02010003', '2019-02-06', '2019-02-09', '25000000.00', '10.56700', '10.12200', '30000000.00', 0, 1, '2019-02-06 07:50:34', '2019-02-06 09:03:12', 'superadm'),
(2, 'B12345', '02A02010003', '2019-01-25', '2019-01-30', '45000000.00', '12.12300', '14.56700', '50000000.00', 0, 1, '2019-02-06 07:50:34', '2019-02-06 07:50:34', 'superadm'),
(3, 'D12345', '02A02010003', '2019-01-25', '2019-02-08', '25000000.00', '15.78900', '11.45600', '0.00', 30000, 1, '2019-02-06 07:50:34', '2019-02-06 08:08:10', 'superadm'),
(4, 'E12345', '02A02010003', '2019-01-25', '2019-01-31', '25000000.00', '10.12200', '10.56700', '0.00', 3000000, 1, '2019-02-06 07:50:34', '2019-02-06 07:50:34', 'superadm'),
(5, 'H12345', '27A01060010', '2019-02-06', '2019-02-10', '50000000.00', '5.00000', '7.00000', '60000000.00', 0, 1, '2019-02-10 05:27:55', '2019-02-10 05:28:57', 'superadm');

-- --------------------------------------------------------

--
-- Table structure for table `media_library`
--

CREATE TABLE `media_library` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(200) DEFAULT NULL,
  `type` varchar(10) DEFAULT NULL,
  `url` varchar(300) DEFAULT NULL,
  `size` varchar(10) DEFAULT NULL,
  `user_created` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `media_library`
--

INSERT INTO `media_library` (`id`, `name`, `type`, `url`, `size`, `user_created`, `created_at`, `updated_at`) VALUES
(0, 'noprofileimage', 'png', 'img/noprofileimage.png', '1159', 1, '2017-05-29 19:56:03', '2017-05-29 19:56:03');

-- --------------------------------------------------------

--
-- Table structure for table `modules`
--

CREATE TABLE `modules` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `slug` varchar(20) DEFAULT NULL,
  `active` int(1) DEFAULT NULL,
  `user_modified` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `modules`
--

INSERT INTO `modules` (`id`, `name`, `slug`, `active`, `user_modified`, `created_at`, `updated_at`) VALUES
(1, 'Master User Level', 'users-level', 1, 1, '2017-10-17 07:07:07', '2017-10-17 07:43:43'),
(2, 'Master User', 'users-user', 1, 1, '2017-10-17 07:16:51', '2017-10-17 07:49:30'),
(3, 'Media Library', 'media-library', 1, 1, '2017-10-17 07:19:28', '2018-06-03 05:40:18');

-- --------------------------------------------------------

--
-- Table structure for table `redeem_detail`
--

CREATE TABLE `redeem_detail` (
  `id` int(11) NOT NULL,
  `kode_customer` varchar(30) DEFAULT NULL,
  `id_campaign` int(11) DEFAULT NULL,
  `id_campaign_hadiah` int(11) DEFAULT NULL,
  `jumlah` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `redeem_detail`
--

INSERT INTO `redeem_detail` (`id`, `kode_customer`, `id_campaign`, `id_campaign_hadiah`, `jumlah`, `created_at`, `updated_at`) VALUES
(1, '02A02010003', 1, 6, 20, '2019-02-08 03:52:49', '2019-02-08 03:52:49'),
(2, '02A02010003', 1, 7, 9, '2019-02-08 03:52:49', '2019-02-08 03:52:49'),
(3, '02A02010003', 1, 8, 2, '2019-02-08 03:52:49', '2019-02-08 03:52:49'),
(4, '02A02010003', 1, 9, 2, '2019-02-08 03:52:49', '2019-02-08 03:52:49'),
(5, '27A01060010', 1, 6, 50, '2019-02-10 05:32:57', '2019-02-10 05:32:57'),
(6, '27A01060010', 1, 7, 5, '2019-02-10 05:32:57', '2019-02-10 05:32:57'),
(7, '27A01060010', 1, 8, 0, '2019-02-10 05:32:57', '2019-02-10 05:32:57'),
(8, '27A01060010', 1, 10, 3, '2019-02-10 05:32:57', '2019-02-10 05:32:57');

-- --------------------------------------------------------

--
-- Table structure for table `redeem_emas`
--

CREATE TABLE `redeem_emas` (
  `id` int(11) NOT NULL,
  `kode_customer` varchar(30) DEFAULT NULL,
  `id_campaign` int(11) DEFAULT NULL,
  `id_campaign_emas` int(11) DEFAULT NULL,
  `jumlah` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `redeem_emas`
--

INSERT INTO `redeem_emas` (`id`, `kode_customer`, `id_campaign`, `id_campaign_emas`, `jumlah`, `created_at`, `updated_at`) VALUES
(1, '02A02010003', 1, 4, 0, '2019-02-08 03:56:18', '2019-02-08 03:56:18'),
(2, '02A02010003', 1, 5, 9, '2019-02-08 03:56:18', '2019-02-08 03:56:18'),
(3, '02A02010003', 1, 6, 4, '2019-02-08 03:56:18', '2019-02-08 03:56:18'),
(4, '27A01060010', 1, 4, 1, '2019-02-10 05:33:17', '2019-02-10 05:33:17'),
(5, '27A01060010', 1, 5, 1, '2019-02-10 05:33:17', '2019-02-10 05:33:17'),
(6, '27A01060010', 1, 6, 0, '2019-02-10 05:33:17', '2019-02-10 05:33:17');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `name` varchar(45) DEFAULT NULL,
  `value` text,
  `user_modified` varchar(30) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `name`, `value`, `user_modified`, `created_at`, `updated_at`) VALUES
(1, 'web_title', 'AVIA Redeem', 'superadm', '2017-06-13 00:27:16', '2019-01-17 01:36:21'),
(2, 'logo', 'img/logo.png', '1', '2017-06-13 00:27:16', '2018-06-03 05:58:24'),
(3, 'email_admin', 'admin@admin.com', 'superadm', '2017-06-13 00:27:16', '2019-01-17 01:35:33'),
(4, 'web_description', '', 'superadm', '2017-07-23 23:56:28', '2019-01-17 01:35:33');

-- --------------------------------------------------------

--
-- Table structure for table `tto_last`
--

CREATE TABLE `tto_last` (
  `id` int(11) NOT NULL,
  `no_tto` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tto_last`
--

INSERT INTO `tto_last` (`id`, `no_tto`, `created_at`, `updated_at`) VALUES
(1, 'TTO-AAP-1902-20001', '2019-02-10 07:03:25', '2019-02-10 07:03:25'),
(2, 'TTO-AAP-1902-50001', '2019-02-10 07:03:25', '2019-02-10 07:03:25'),
(3, 'TTO-AAP-1902-20003', '2019-02-10 07:06:24', '2019-02-10 07:06:24'),
(4, 'TTO-AAP-1902-50003', '2019-02-10 07:06:24', '2019-02-10 07:06:24');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_level_id` int(11) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `avatar_id` int(11) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `address` text,
  `phone` text,
  `gender` enum('male','female','other') DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `username` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `active` int(1) NOT NULL,
  `user_modified` int(11) DEFAULT NULL,
  `last_activity` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `user_level_id`, `firstname`, `lastname`, `avatar_id`, `email`, `address`, `phone`, `gender`, `birthdate`, `username`, `password`, `active`, `user_modified`, `last_activity`, `created_at`, `updated_at`) VALUES
(1, 1, 'Super', 'Admin', 0, 'superadmin@admin.com', 'Jl Madura xxxxxxx', '08383xxxxxxx', 'male', '1986-07-25', 'superadmin', '$2y$10$TkX/dDYrtvIEXidPOag5T.V8qbyluUHJg5ssBjKe6WlVqpItuN8uy', 1, 1, '2019-01-03 03:54:50', '2017-03-13 20:51:35', '2019-01-03 03:54:50'),
(2, 2, 'Admin', 'Admin', 0, 'admin@admin.com', NULL, NULL, 'male', NULL, 'admin', '$2y$10$PQaUY4b0YsSo5qAuK8Cc.OB.WeEJHrJJ0FDgk6YE9xhXboVRou3We', 1, 1, '2019-01-02 03:17:02', '2017-04-19 14:29:01', '2019-01-02 03:17:02');

-- --------------------------------------------------------

--
-- Table structure for table `user_levels`
--

CREATE TABLE `user_levels` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(30) NOT NULL,
  `active` int(1) DEFAULT NULL,
  `user_modified` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user_levels`
--

INSERT INTO `user_levels` (`id`, `name`, `active`, `user_modified`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 1, 1, '2017-06-28 06:18:17', '2017-06-28 06:18:17'),
(2, 'Admin', 1, 1, '2018-06-02 15:59:51', '2018-06-02 15:59:51'),
(3, 'User', 1, 1, '2018-06-03 04:19:49', '2018-06-03 04:19:49');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `access_control`
--
ALTER TABLE `access_control`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `campaign_d_bagi`
--
ALTER TABLE `campaign_d_bagi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `campaign_d_emas`
--
ALTER TABLE `campaign_d_emas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `campaign_d_hadiah`
--
ALTER TABLE `campaign_d_hadiah`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `campaign_h`
--
ALTER TABLE `campaign_h`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customer_omzet`
--
ALTER TABLE `customer_omzet`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `media_library`
--
ALTER TABLE `media_library`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `modules`
--
ALTER TABLE `modules`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `redeem_detail`
--
ALTER TABLE `redeem_detail`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `redeem_emas`
--
ALTER TABLE `redeem_emas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tto_last`
--
ALTER TABLE `tto_last`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_levels`
--
ALTER TABLE `user_levels`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `access_control`
--
ALTER TABLE `access_control`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `campaign_d_bagi`
--
ALTER TABLE `campaign_d_bagi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `campaign_d_emas`
--
ALTER TABLE `campaign_d_emas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `campaign_d_hadiah`
--
ALTER TABLE `campaign_d_hadiah`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `campaign_h`
--
ALTER TABLE `campaign_h`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `customer_omzet`
--
ALTER TABLE `customer_omzet`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `media_library`
--
ALTER TABLE `media_library`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `modules`
--
ALTER TABLE `modules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `redeem_detail`
--
ALTER TABLE `redeem_detail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `redeem_emas`
--
ALTER TABLE `redeem_emas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `tto_last`
--
ALTER TABLE `tto_last`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user_levels`
--
ALTER TABLE `user_levels`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
