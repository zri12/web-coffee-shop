-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 16 Feb 2026 pada 01.33
-- Versi server: 10.11.9-MariaDB-cll-lve-log
-- Versi PHP: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `fazriluk_coffeeshop`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'Coffee', 'coffee', 'Coffee Selection', 1, 1, '2026-02-08 09:53:55', '2026-02-09 01:23:18'),
(2, 'Non-Coffee', 'non-coffee', 'Non-Coffee Selection', 1, 2, '2026-02-08 09:53:55', '2026-02-09 01:23:18'),
(3, 'Snack', 'snack', 'Snack Selection', 1, 3, '2026-02-08 09:53:55', '2026-02-09 01:23:18'),
(4, 'Dessert', 'dessert', 'Dessert Selection', 1, 4, '2026-02-08 09:53:55', '2026-02-09 01:23:18'),
(5, 'Food', 'food', 'Food Selection', 1, 5, '2026-02-08 09:53:55', '2026-02-09 01:23:18');

-- --------------------------------------------------------

--
-- Struktur dari tabel `menus`
--

CREATE TABLE `menus` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(12,2) NOT NULL,
  `image_url` varchar(500) DEFAULT NULL,
  `is_available` tinyint(1) NOT NULL DEFAULT 1,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `menus`
--

INSERT INTO `menus` (`id`, `category_id`, `name`, `description`, `price`, `image_url`, `is_available`, `is_featured`, `created_at`, `updated_at`) VALUES
(1, 1, 'Hazelnut Latte', 'Rich espresso with steamed milk and roasted hazelnut flavor.', 45000.00, 'coffee-1.jpg', 1, 1, '2026-02-09 01:23:18', '2026-02-09 01:23:18'),
(2, 1, 'Cold Brew', 'Steeped for 20 hours for super smooth flavor.', 35000.00, 'coffee-2.jpg', 1, 1, '2026-02-09 01:23:18', '2026-02-09 01:23:18'),
(3, 1, 'Cappuccino', 'Dark, rich espresso lying in wait under a smoothed and stretched layer of thick milk foam.', 42000.00, 'coffee-3.jpg', 1, 0, '2026-02-09 01:23:18', '2026-02-09 01:23:18'),
(4, 2, 'Matcha Latte', 'Premium Japanese green tea with steamed milk.', 38000.00, 'tea-1.jpg', 1, 1, '2026-02-09 01:23:18', '2026-02-09 01:23:18'),
(5, 3, 'Butter Croissant', 'Flaky, buttery, and freshly baked every morning.', 30000.00, 'bread-1.jpg', 1, 1, '2026-02-09 01:23:18', '2026-02-09 01:23:18'),
(6, 4, 'Matcha Cake', 'Delicate layers of matcha sponge and cream.', 55000.00, 'cake-1.jpg', 1, 1, '2026-02-09 01:23:18', '2026-02-09 01:23:18'),
(7, 4, 'Tiramisu', 'Classic Italian coffee-flavoured dessert.', 50000.00, 'cake-2.jpg', 1, 0, '2026-02-09 01:23:18', '2026-02-09 01:23:18'),
(8, 3, 'KENTANG', 'ASDSA', 20000.00, 'menu-images/fhEnasIhT91H5BBj06vF3MmB8gbVelar49zWVo5m.jpg', 1, 0, '2026-02-10 04:53:23', '2026-02-10 04:53:23');

-- --------------------------------------------------------

--
-- Struktur dari tabel `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2025_02_10_000001_create_settings_table', 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_number` varchar(50) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'Cashier who created manual order',
  `customer_name` varchar(255) NOT NULL,
  `customer_phone` varchar(20) DEFAULT NULL,
  `table_number` int(11) DEFAULT NULL,
  `table_label` varchar(50) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` enum('draft','waiting_payment','paid','pending','preparing','processing','completed','cancelled') NOT NULL DEFAULT 'pending',
  `total_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `order_type` enum('qr','dine_in','takeaway') NOT NULL DEFAULT 'dine_in',
  `payment_method` enum('cash','qris','card','transfer') DEFAULT NULL,
  `payment_status` enum('unpaid','pending','paid','failed','refunded') NOT NULL DEFAULT 'unpaid',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `orders`
--

INSERT INTO `orders` (`id`, `order_number`, `user_id`, `customer_name`, `customer_phone`, `table_number`, `table_label`, `notes`, `status`, `total_amount`, `order_type`, `payment_method`, `payment_status`, `created_at`, `updated_at`) VALUES
(1, 'ORD-SAMPLE001', NULL, 'Budi Santoso', '081234567890', 5, NULL, NULL, 'completed', 85000.00, '', NULL, 'unpaid', '2026-02-08 09:53:55', '2026-02-09 18:51:03'),
(2, 'ORD-SAMPLE002', NULL, 'Siti Rahayu', '081987654321', 3, NULL, NULL, 'completed', 55000.00, '', NULL, 'unpaid', '2026-02-08 09:53:55', '2026-02-09 18:51:03'),
(3, 'ORD-SAMPLE003', NULL, 'Ahmad Pratama', '082112345678', 8, NULL, NULL, 'completed', 120000.00, '', NULL, 'unpaid', '2026-02-08 09:53:55', '2026-02-10 03:54:17'),
(4, 'ORD-C5KU45WA', NULL, 'Fazri', NULL, 6, NULL, NULL, 'completed', 51000.00, 'dine_in', NULL, 'paid', '2026-02-10 03:02:24', '2026-02-11 14:06:50'),
(5, 'ORD-698B0FEA3C14B', NULL, 'ZRI', NULL, 1, NULL, NULL, 'completed', 0.00, 'dine_in', NULL, 'paid', '2026-02-10 04:00:58', '2026-02-14 14:50:40'),
(6, 'ORD-698B0FEC9A49F', NULL, 'ZRI', NULL, 1, NULL, NULL, 'completed', 0.00, 'dine_in', NULL, 'paid', '2026-02-10 04:01:00', '2026-02-14 14:50:41'),
(7, 'ORD-698B0FF2A90D0', NULL, 'ZRI', NULL, 1, NULL, NULL, 'completed', 0.00, 'dine_in', NULL, 'paid', '2026-02-10 04:01:06', '2026-02-14 14:50:41'),
(8, 'ORD-698B0FF425D89', NULL, 'ZRI', NULL, 1, NULL, NULL, 'completed', 0.00, 'dine_in', NULL, 'paid', '2026-02-10 04:01:08', '2026-02-14 14:50:41'),
(9, 'ORD-698B111E72FC3', NULL, 'zry', NULL, 3, NULL, NULL, 'completed', 0.00, 'dine_in', 'cash', 'paid', '2026-02-10 04:06:06', '2026-02-14 14:50:22'),
(10, 'ORD-698B1130785FA', NULL, 'zry', NULL, 3, NULL, NULL, 'completed', 0.00, 'dine_in', 'qris', 'paid', '2026-02-10 04:06:24', '2026-02-14 14:50:43'),
(11, 'ORD-698B118D3D3CD', NULL, 'zry', NULL, 3, NULL, NULL, 'completed', 48300.00, 'dine_in', 'cash', 'paid', '2026-02-10 04:07:57', '2026-02-11 14:06:50'),
(12, 'ORD-698B123D10EAC', NULL, 'azr', NULL, 3, NULL, NULL, 'completed', 56700.00, 'dine_in', 'qris', 'paid', '2026-02-10 04:10:53', '2026-02-14 14:50:15'),
(14, 'ORD-KNSDVWD4', NULL, 'azri', '081222222222', 1, NULL, 'ac', 'pending', 146000.00, 'dine_in', 'qris', 'pending', '2026-02-11 00:48:52', '2026-02-11 00:48:52'),
(15, 'ORD-HWL49VOL', NULL, 'sxacasc', '08199999999', 2, NULL, 'sdc', 'completed', 48000.00, 'dine_in', 'cash', 'paid', '2026-02-11 00:55:57', '2026-02-14 14:50:43'),
(16, 'ORD-CA33YXTY', NULL, 'asascas', '0812222212', 5, NULL, 'sdcsd', 'completed', 20000.00, 'dine_in', 'cash', 'paid', '2026-02-11 01:02:50', '2026-02-14 14:50:21'),
(17, 'ORD-V9M2HWFI', NULL, 'saxasx', '081222221212', 4, NULL, 'ascas', 'pending', 45000.00, 'dine_in', 'qris', 'pending', '2026-02-11 01:12:21', '2026-02-11 01:12:21'),
(18, 'ORD-P7HYHY80', NULL, 'sdcsdc', '08122222121212', 7, NULL, 'sdcsdc', 'pending', 38000.00, 'dine_in', 'qris', 'pending', '2026-02-11 01:25:30', '2026-02-11 01:25:30'),
(19, 'ORD-WYMBWZWB', NULL, 'fwfwe', '081222221211', 8, NULL, 'asdsa', 'pending', 38000.00, 'dine_in', 'qris', 'pending', '2026-02-11 05:04:12', '2026-02-11 05:04:12'),
(20, 'ORD-BQWNRACK', NULL, 'sdcdsc', '081222221211121', 3, NULL, 'sdc', 'pending', 45000.00, 'dine_in', 'qris', 'pending', '2026-02-11 05:13:35', '2026-02-11 05:13:35'),
(21, 'ORD-HBB46S03', NULL, 'ascsa', '08123', 2, NULL, 'dcsd', 'pending', 45000.00, 'dine_in', 'qris', 'pending', '2026-02-11 05:26:16', '2026-02-11 05:26:16'),
(22, 'ORD-698C8A56E7D24', 2, 'wda', NULL, 5, NULL, NULL, 'completed', 17850.00, 'dine_in', 'cash', 'paid', '2026-02-11 06:55:34', '2026-02-14 14:50:35'),
(23, 'ORD-698C8A8A4A405', 2, 'asdasd', NULL, 9, NULL, NULL, 'completed', 55650.00, 'dine_in', 'qris', 'paid', '2026-02-11 06:56:26', '2026-02-14 14:51:08'),
(24, 'ORD-698C8ABE67B29', 2, 'asdas', NULL, 12, NULL, NULL, 'completed', 23100.00, 'dine_in', 'qris', 'paid', '2026-02-11 06:57:18', '2026-02-14 14:50:34'),
(25, 'ORD-698C9147280C1', 2, 'Guestjfv', NULL, 12, NULL, NULL, 'completed', 67200.00, 'dine_in', 'qris', 'paid', '2026-02-11 07:25:11', '2026-02-14 14:50:31'),
(26, 'ORD-698C91624DAFD', 2, 'jhfhj', NULL, 14, NULL, NULL, 'completed', 55650.00, 'dine_in', 'card', 'paid', '2026-02-11 07:25:38', '2026-02-14 14:51:07'),
(27, 'ORD-698CA66CE3588', 2, 'adawd', NULL, 13, NULL, NULL, 'completed', 64050.00, 'dine_in', 'card', 'paid', '2026-02-11 08:55:24', '2026-02-14 14:51:08'),
(28, 'ORD-698CA66D4DF53', 2, 'adawd', NULL, 13, NULL, NULL, 'completed', 64050.00, 'dine_in', 'card', 'paid', '2026-02-11 08:55:25', '2026-02-14 14:51:07'),
(29, 'ORD-698CA8250F806', 2, 'asdsa', NULL, 15, NULL, NULL, 'completed', 39900.00, 'dine_in', 'card', 'paid', '2026-02-11 09:02:45', '2026-02-11 09:03:24'),
(30, 'ORD-HPJYUER7', NULL, 'zxczc', '0819999999912', 14, NULL, 'sdcsd', 'pending', 75000.00, 'dine_in', 'cash', 'unpaid', '2026-02-12 01:29:36', '2026-02-12 01:29:43'),
(31, 'ORD-2DXUT6PY', NULL, 'fbfg', '08122222222212', 8, NULL, 'vdf', 'pending', 60000.00, 'dine_in', 'qris', 'pending', '2026-02-12 01:32:34', '2026-02-12 01:32:41'),
(32, 'ORD-ZSML396I', NULL, 'gsvbss', '0812222222', 8, NULL, 'vahha', 'pending', 48000.00, 'dine_in', 'cash', 'unpaid', '2026-02-12 01:37:59', '2026-02-12 01:38:03'),
(33, 'ORD-DF5W98A9', NULL, 'bzbhs', '08133333', 9, NULL, 'vsggs', 'pending', 50000.00, 'dine_in', 'qris', 'pending', '2026-02-12 01:39:09', '2026-02-12 01:39:13'),
(34, 'ORD-KX519LQ8', NULL, 'dsd', 'sdsdc', 4, NULL, 'cds', 'pending', 37000.00, 'dine_in', 'qris', 'pending', '2026-02-12 01:45:25', '2026-02-12 01:45:28'),
(35, 'ORD-D0URXQTK', NULL, 'dcsdc', '0812222212122', 9, NULL, 'dfvdf', 'waiting_payment', 56000.00, 'dine_in', 'qris', 'pending', '2026-02-12 02:47:30', '2026-02-12 02:47:33'),
(36, 'ORD-8UIPMMGM', NULL, 'asdsa', '0819999999921', 10, NULL, 'sfds', 'pending', 45000.00, 'dine_in', 'cash', 'unpaid', '2026-02-12 06:15:14', '2026-02-12 06:15:19'),
(37, 'ORD-0XCKMPE1', NULL, '43f34f34', '08199999999', 10, NULL, 'dfvdfv', 'pending', 42000.00, 'dine_in', 'qris', 'pending', '2026-02-12 06:16:25', '2026-02-12 06:16:28'),
(38, 'ORD-A67W51QZ', NULL, 'n', '5588', 0, NULL, 'halo', 'pending', 35000.00, 'takeaway', 'qris', 'pending', '2026-02-12 16:04:58', '2026-02-12 16:05:02'),
(39, 'ORD-IVLIG8DA', NULL, 'nemo', '5588', 0, NULL, 'kaka cantik', 'pending', 137000.00, 'takeaway', 'qris', 'pending', '2026-02-12 19:26:22', '2026-02-12 19:26:32'),
(40, 'ORD-TIGYUKIH', NULL, 'bayi besar', '082199380693', 0, NULL, NULL, 'pending', 112000.00, 'takeaway', 'qris', 'pending', '2026-02-13 03:44:26', '2026-02-13 03:44:33'),
(41, 'ORD-BMWO5FKI', NULL, 'bayi besar', '082199380693', 0, NULL, NULL, 'pending', 54000.00, 'takeaway', 'qris', 'pending', '2026-02-13 03:50:34', '2026-02-13 03:50:37'),
(42, 'ORD-F08VJRPI', NULL, 'bayi besar', '082199380693', 0, NULL, NULL, 'pending', 54000.00, 'takeaway', 'cash', 'unpaid', '2026-02-13 03:50:59', '2026-02-13 03:51:02'),
(43, 'ORD-UQAEDWJP', NULL, 'nnn', '5588', 0, NULL, 'p', 'pending', 77000.00, 'takeaway', 'cash', 'unpaid', '2026-02-14 14:38:32', '2026-02-14 14:38:35'),
(44, 'ORD-SD8QX7SW', NULL, 'nnn', NULL, 0, NULL, NULL, 'pending', 35000.00, 'takeaway', 'cash', 'unpaid', '2026-02-14 14:39:57', '2026-02-14 14:40:01'),
(45, 'ORD-VKS1M8L9', NULL, 'nemo', NULL, 0, NULL, NULL, 'pending', 30000.00, 'takeaway', 'qris', 'pending', '2026-02-14 20:04:29', '2026-02-14 20:04:33'),
(46, 'ORD-KINJHPP9', NULL, 'bayi besar', '082199380693', 0, NULL, NULL, 'pending', 58000.00, 'takeaway', 'qris', 'pending', '2026-02-14 21:24:01', '2026-02-14 21:24:04'),
(47, 'ORD-PWHZO4P0', NULL, 'bayi besar', '082199380693', 0, NULL, NULL, 'pending', 53000.00, 'takeaway', 'qris', 'pending', '2026-02-14 21:50:24', '2026-02-14 21:50:28'),
(48, 'ORD-JWHJFFGP', NULL, 'bayi besar', '082199380693', 0, NULL, NULL, 'pending', 53000.00, 'takeaway', 'qris', 'pending', '2026-02-14 21:50:35', '2026-02-14 21:50:38'),
(49, 'ORD-8TJELRLW', NULL, 'bayi besar', '082199380693', 0, NULL, NULL, 'pending', 53000.00, 'takeaway', 'qris', 'pending', '2026-02-14 21:50:42', '2026-02-14 21:50:46'),
(50, 'ORD-SESI8LVG', NULL, 'bayi besar', NULL, 0, NULL, NULL, 'pending', 48000.00, 'takeaway', 'qris', 'pending', '2026-02-14 23:20:27', '2026-02-14 23:20:31'),
(51, 'ORD-NH9ZSHSG', NULL, 'bayi besar', NULL, 0, NULL, NULL, 'pending', 48000.00, 'takeaway', 'qris', 'pending', '2026-02-14 23:20:33', '2026-02-14 23:20:37'),
(52, 'ORD-WXYLFBVI', NULL, 'bayi besar', NULL, 0, NULL, NULL, 'pending', 48000.00, 'takeaway', 'qris', 'pending', '2026-02-14 23:20:43', '2026-02-14 23:20:47'),
(53, 'ORD-ZQFRB4FO', NULL, 'bayi besar', NULL, 0, NULL, NULL, 'pending', 48000.00, 'takeaway', 'qris', 'pending', '2026-02-14 23:20:54', '2026-02-14 23:20:57'),
(54, 'ORD-FMWOTLGZ', NULL, 'bayi besar', NULL, 0, NULL, NULL, 'pending', 48000.00, 'takeaway', 'qris', 'pending', '2026-02-14 23:20:57', '2026-02-14 23:21:00'),
(55, 'ORD-82V8EKXC', NULL, 'bayi besar', '082199380693', 0, NULL, NULL, 'pending', 48000.00, 'takeaway', 'qris', 'pending', '2026-02-14 23:23:07', '2026-02-14 23:23:10'),
(56, 'ORD-YKKYKGQ6', NULL, 'bayi besar', '082199380693', 0, NULL, NULL, 'pending', 91000.00, 'takeaway', 'qris', 'pending', '2026-02-15 00:58:18', '2026-02-15 00:58:25'),
(57, 'ORD-D2NWZ9TP', NULL, 'bayi besar', '082199380693', 0, NULL, NULL, 'pending', 40000.00, 'takeaway', 'qris', 'pending', '2026-02-15 01:02:01', '2026-02-15 01:02:05'),
(58, 'ORD-ZIPA5W4D', NULL, 'bayi besar', '082199380693', 0, NULL, NULL, 'pending', 40000.00, 'takeaway', 'qris', 'pending', '2026-02-15 01:02:09', '2026-02-15 01:02:13'),
(59, 'ORD-RMW2GRAB', NULL, 'bayi besar', '082199380693', 0, NULL, NULL, 'pending', 51000.00, 'takeaway', 'qris', 'pending', '2026-02-15 01:14:58', '2026-02-15 01:15:01'),
(60, 'ORD-ITDDSMTY', NULL, 'bayi besar', '082199380693', 0, NULL, NULL, 'pending', 48000.00, 'takeaway', 'qris', 'pending', '2026-02-15 01:28:09', '2026-02-15 01:28:12'),
(61, 'ORD-NQ4B3PUD', NULL, 'bayi besar', '082199380693', 0, NULL, NULL, 'pending', 48000.00, 'takeaway', 'qris', 'pending', '2026-02-15 01:28:20', '2026-02-15 01:28:24');

-- --------------------------------------------------------

--
-- Struktur dari tabel `order_items`
--

CREATE TABLE `order_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `menu_id` bigint(20) UNSIGNED NOT NULL,
  `menu_name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price` decimal(12,2) DEFAULT NULL,
  `unit_price` decimal(12,2) DEFAULT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  `notes` text DEFAULT NULL,
  `options` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Customization options (temperature, ice, sugar, etc.)' CHECK (json_valid(`options`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `menu_id`, `menu_name`, `quantity`, `price`, `unit_price`, `subtotal`, `notes`, `options`, `created_at`, `updated_at`) VALUES
(1, 1, 3, 'Cappuccino', 2, 35000.00, 35000.00, 70000.00, NULL, NULL, '2026-02-08 09:53:55', '2026-02-10 11:05:43'),
(2, 1, 16, '', 1, 28000.00, 28000.00, 28000.00, NULL, NULL, '2026-02-08 09:53:55', '2026-02-10 11:05:43'),
(3, 2, 10, '', 1, 38000.00, 38000.00, 38000.00, NULL, NULL, '2026-02-08 09:53:55', '2026-02-10 11:05:43'),
(4, 2, 13, '', 1, 22000.00, 22000.00, 22000.00, NULL, NULL, '2026-02-08 09:53:55', '2026-02-10 11:05:43'),
(5, 3, 5, 'Butter Croissant', 2, 40000.00, 40000.00, 80000.00, NULL, NULL, '2026-02-08 09:53:55', '2026-02-10 11:05:43'),
(6, 3, 24, '', 1, 45000.00, 45000.00, 45000.00, NULL, NULL, '2026-02-08 09:53:55', '2026-02-10 11:05:43'),
(7, 4, 2, 'Cold Brew', 1, 51000.00, 51000.00, 51000.00, 'Large, Ice, Ice: Normal, Sugar: Normal, + Whipped Cream, + Extra Shot', NULL, '2026-02-10 03:02:24', '2026-02-10 11:05:43'),
(8, 11, 2, 'Cold Brew', 1, 46000.00, 46000.00, 46000.00, '❄️ Ice - normal | Sugar: Less Sugar | Size: Large (+8k) | Add-ons: Whipped Cream +3k', NULL, '2026-02-10 04:07:57', '2026-02-10 04:07:57'),
(9, 12, 4, 'Matcha Latte', 1, 54000.00, 54000.00, 54000.00, '❄️ Ice - less | Size: Large (+8k) | Add-ons: Extra Shot +5k, Whipped Cream +3k', NULL, '2026-02-10 04:10:53', '2026-02-10 04:10:53'),
(13, 14, 1, 'Hazelnut Latte', 1, NULL, 58000.00, 58000.00, 'Large, Ice, Ice: Normal, Sugar: Normal, + Extra Shot', NULL, '2026-02-11 00:48:52', '2026-02-11 00:48:52'),
(14, 14, 2, 'Cold Brew', 1, NULL, 40000.00, 40000.00, 'Regular, Ice, Ice: Normal, Sugar: Normal, + Extra Shot', NULL, '2026-02-11 00:48:52', '2026-02-11 00:48:52'),
(15, 14, 2, 'Cold Brew', 1, NULL, 48000.00, 48000.00, 'Large, Hot, Sugar: Less, + Extra Shot', NULL, '2026-02-11 00:48:52', '2026-02-11 00:48:52'),
(16, 15, 2, 'Cold Brew', 1, NULL, 48000.00, 48000.00, 'Large, Hot, Sugar: Normal, + Extra Shot', NULL, '2026-02-11 00:55:57', '2026-02-11 00:55:57'),
(17, 16, 8, 'KENTANG', 1, NULL, 20000.00, 20000.00, 'Portion: Regular, Chili sauce, Mayonnaise sauce', NULL, '2026-02-11 01:02:50', '2026-02-11 01:02:50'),
(18, 17, 3, 'Cappuccino', 1, NULL, 45000.00, 45000.00, 'Regular, Ice, Ice: Less, Sugar: Less, + Caramel Syrup', NULL, '2026-02-11 01:12:21', '2026-02-11 01:12:21'),
(19, 18, 4, 'Matcha Latte', 1, NULL, 38000.00, 38000.00, 'Regular, Ice, Ice: Normal, Sugar: Normal', NULL, '2026-02-11 01:25:30', '2026-02-11 01:25:30'),
(20, 19, 4, 'Matcha Latte', 1, NULL, 38000.00, 38000.00, 'Regular, Ice, Ice: Normal, Sugar: Normal', NULL, '2026-02-11 05:04:12', '2026-02-11 05:04:12'),
(21, 20, 1, 'Hazelnut Latte', 1, NULL, 45000.00, 45000.00, 'Regular, Ice, Ice: Normal, Sugar: Normal', NULL, '2026-02-11 05:13:35', '2026-02-11 05:13:35'),
(22, 21, 1, 'Hazelnut Latte', 1, NULL, 45000.00, 45000.00, 'Regular, Ice, Ice: Normal, Sugar: Normal', NULL, '2026-02-11 05:26:16', '2026-02-11 05:26:16'),
(23, 22, 8, 'KENTANG', 1, 17000.00, 17000.00, 17000.00, 'Size: Small (-5k) | Sauces: Chili Sauce, BBQ Sauce +2k', NULL, '2026-02-11 06:55:34', '2026-02-11 06:55:34'),
(24, 23, 7, 'Tiramisu', 1, 53000.00, 53000.00, 53000.00, 'Toppings: Whipped Cream +3k', NULL, '2026-02-11 06:56:26', '2026-02-11 06:56:26'),
(25, 24, 8, 'KENTANG', 1, 22000.00, 22000.00, 22000.00, 'Sauces: Mayonnaise, BBQ Sauce +2k', NULL, '2026-02-11 06:57:18', '2026-02-11 06:57:18'),
(26, 25, 6, 'Matcha Cake', 1, 64000.00, 64000.00, 64000.00, 'Portion: Large (+5k) | Toppings: Chocolate Chips +4k', NULL, '2026-02-11 07:25:11', '2026-02-11 07:25:11'),
(27, 26, 3, 'Cappuccino', 1, 53000.00, 53000.00, 53000.00, '🔥 Hot | Size: Large (+8k) | Add-ons: Caramel Syrup +3k', NULL, '2026-02-11 07:25:38', '2026-02-11 07:25:38'),
(28, 27, 7, 'Tiramisu', 1, 61000.00, 61000.00, 61000.00, 'Portion: Large (+5k) | Toppings: Whipped Cream +3k, Caramel Drizzle +3k', NULL, '2026-02-11 08:55:24', '2026-02-11 08:55:24'),
(29, 28, 7, 'Tiramisu', 1, 61000.00, 61000.00, 61000.00, 'Portion: Large (+5k) | Toppings: Whipped Cream +3k, Caramel Drizzle +3k', NULL, '2026-02-11 08:55:25', '2026-02-11 08:55:25'),
(30, 29, 4, 'Matcha Latte', 1, 38000.00, 38000.00, 38000.00, '❄️ Ice - normal', NULL, '2026-02-11 09:02:45', '2026-02-11 09:02:45'),
(31, 30, 4, 'Matcha Latte', 1, NULL, 43000.00, 43000.00, '', NULL, '2026-02-12 01:29:38', '2026-02-12 01:29:38'),
(32, 30, 5, 'Butter Croissant', 1, NULL, 32000.00, 32000.00, 'Portion: Large, Bbq sauce, Ketchup sauce', NULL, '2026-02-12 01:29:41', '2026-02-12 01:29:41'),
(33, 31, 5, 'Butter Croissant', 1, NULL, 30000.00, 30000.00, 'Portion: Regular', NULL, '2026-02-12 01:32:36', '2026-02-12 01:32:36'),
(34, 31, 5, 'Butter Croissant', 1, NULL, 30000.00, 30000.00, 'Portion: Large', NULL, '2026-02-12 01:32:39', '2026-02-12 01:32:39'),
(35, 32, 1, 'Hazelnut Latte', 1, NULL, 48000.00, 48000.00, 'Large, Hot, Sugar: Normal, + Whipped Cream', NULL, '2026-02-12 01:38:01', '2026-02-12 01:38:01'),
(36, 33, 1, 'Hazelnut Latte', 1, NULL, 50000.00, 50000.00, 'Regular, Ice, Ice: Less, Sugar: Less, + Extra Shot', NULL, '2026-02-12 01:39:11', '2026-02-12 01:39:11'),
(37, 34, 5, 'Butter Croissant', 1, NULL, 37000.00, 37000.00, 'Portion: Large, Chili sauce, Bbq sauce', NULL, '2026-02-12 01:45:26', '2026-02-12 01:45:26'),
(38, 35, 1, 'Hazelnut Latte', 1, NULL, 56000.00, 56000.00, 'Large, Hot, Sugar: Less, + Whipped Cream', NULL, '2026-02-12 02:47:31', '2026-02-12 02:47:31'),
(39, 36, 1, 'Hazelnut Latte', 1, NULL, 45000.00, 45000.00, 'Regular, Ice, Ice: Normal, Sugar: Normal, + Whipped Cream', NULL, '2026-02-12 06:15:16', '2026-02-12 06:15:16'),
(40, 37, 3, 'Cappuccino', 1, NULL, 42000.00, 42000.00, 'Large, Hot, Sugar: No sugar', NULL, '2026-02-12 06:16:26', '2026-02-12 06:16:26'),
(41, 38, 2, 'Cold Brew', 1, NULL, 35000.00, 35000.00, 'Regular, Ice, Ice: Normal, Sugar: Normal', NULL, '2026-02-12 16:05:00', '2026-02-12 16:05:00'),
(42, 39, 4, 'Matcha Latte', 1, NULL, 46000.00, 46000.00, 'Large, Ice, Ice: Normal, Sugar: Normal', NULL, '2026-02-12 19:26:23', '2026-02-12 19:26:23'),
(43, 39, 5, 'Butter Croissant', 1, NULL, 25000.00, 25000.00, 'Portion: Small, Mayonnaise sauce', NULL, '2026-02-12 19:26:27', '2026-02-12 19:26:27'),
(44, 39, 6, 'Matcha Cake', 1, NULL, 66000.00, 66000.00, 'Portion: Large, + Chocolate', NULL, '2026-02-12 19:26:30', '2026-02-12 19:26:30'),
(45, 40, 2, 'Cold Brew', 1, NULL, 48000.00, 48000.00, 'Large, Ice, Ice: Less, Sugar: Less, + Extra Shot', NULL, '2026-02-13 03:44:27', '2026-02-13 03:44:27'),
(46, 40, 1, 'Hazelnut Latte', 1, NULL, 64000.00, 64000.00, 'Large, Ice, Ice: Normal, Sugar: Less, + Extra Shot, + Whipped Cream, + Caramel Syrup, Note: halo kk cantik, minta ig dong', NULL, '2026-02-13 03:44:30', '2026-02-13 03:44:30'),
(47, 41, 2, 'Cold Brew', 1, NULL, 54000.00, 54000.00, 'Large, Hot, Sugar: No sugar, + Extra Shot, + Whipped Cream, + Caramel Syrup', NULL, '2026-02-13 03:50:35', '2026-02-13 03:50:35'),
(48, 42, 2, 'Cold Brew', 1, NULL, 54000.00, 54000.00, 'Large, Hot, Sugar: No sugar, + Extra Shot, + Whipped Cream, + Caramel Syrup', NULL, '2026-02-13 03:51:00', '2026-02-13 03:51:00'),
(49, 43, 7, 'Tiramisu', 1, NULL, 77000.00, 77000.00, 'Portion: Large, + Chocolate, + Caramel, + Whipped, + Ice-cream', NULL, '2026-02-14 14:38:33', '2026-02-14 14:38:33'),
(50, 44, 2, 'Cold Brew', 1, NULL, 35000.00, 35000.00, 'Regular, Ice, Ice: Normal, Sugar: Normal', NULL, '2026-02-14 14:39:59', '2026-02-14 14:39:59'),
(51, 45, 5, 'Butter Croissant', 1, NULL, 30000.00, 30000.00, 'Portion: Regular', NULL, '2026-02-14 20:04:31', '2026-02-14 20:04:31'),
(52, 46, 1, 'Hazelnut Latte', 1, NULL, 58000.00, 58000.00, 'Large, Ice, Ice: Normal, Sugar: Normal, + Extra Shot', NULL, '2026-02-14 21:24:02', '2026-02-14 21:24:02'),
(53, 47, 1, 'Hazelnut Latte', 1, NULL, 53000.00, 53000.00, 'Large, Ice, Ice: Normal, Sugar: Normal', NULL, '2026-02-14 21:50:25', '2026-02-14 21:50:25'),
(54, 48, 1, 'Hazelnut Latte', 1, NULL, 53000.00, 53000.00, 'Large, Ice, Ice: Normal, Sugar: Normal', NULL, '2026-02-14 21:50:36', '2026-02-14 21:50:36'),
(55, 49, 1, 'Hazelnut Latte', 1, NULL, 53000.00, 53000.00, 'Large, Ice, Ice: Normal, Sugar: Normal', NULL, '2026-02-14 21:50:44', '2026-02-14 21:50:44'),
(56, 50, 2, 'Cold Brew', 1, NULL, 48000.00, 48000.00, 'Large, Ice, Ice: Normal, Sugar: Normal, + Extra Shot', NULL, '2026-02-14 23:20:29', '2026-02-14 23:20:29'),
(57, 51, 2, 'Cold Brew', 1, NULL, 48000.00, 48000.00, 'Large, Ice, Ice: Normal, Sugar: Normal, + Extra Shot', NULL, '2026-02-14 23:20:35', '2026-02-14 23:20:35'),
(58, 52, 2, 'Cold Brew', 1, NULL, 48000.00, 48000.00, 'Large, Ice, Ice: Normal, Sugar: Normal, + Extra Shot', NULL, '2026-02-14 23:20:45', '2026-02-14 23:20:45'),
(59, 53, 2, 'Cold Brew', 1, NULL, 48000.00, 48000.00, 'Large, Ice, Ice: Normal, Sugar: Normal, + Extra Shot', NULL, '2026-02-14 23:20:55', '2026-02-14 23:20:55'),
(60, 54, 2, 'Cold Brew', 1, NULL, 48000.00, 48000.00, 'Large, Ice, Ice: Normal, Sugar: Normal, + Extra Shot', NULL, '2026-02-14 23:20:58', '2026-02-14 23:20:58'),
(61, 55, 2, 'Cold Brew', 1, NULL, 48000.00, 48000.00, 'Large, Ice, Ice: Less, Sugar: Normal, + Extra Shot', NULL, '2026-02-14 23:23:08', '2026-02-14 23:23:08'),
(62, 56, 2, 'Cold Brew', 1, NULL, 43000.00, 43000.00, 'Large, Ice, Ice: Normal, Sugar: No sugar', NULL, '2026-02-15 00:58:20', '2026-02-15 00:58:20'),
(63, 56, 2, 'Cold Brew', 1, NULL, 48000.00, 48000.00, 'Large, Ice, Ice: Normal, Sugar: Normal, + Extra Shot', NULL, '2026-02-15 00:58:23', '2026-02-15 00:58:23'),
(64, 57, 2, 'Cold Brew', 1, NULL, 40000.00, 40000.00, 'Regular, Ice, Ice: Normal, Sugar: Normal, + Extra Shot', NULL, '2026-02-15 01:02:03', '2026-02-15 01:02:03'),
(65, 58, 2, 'Cold Brew', 1, NULL, 40000.00, 40000.00, 'Regular, Ice, Ice: Normal, Sugar: Normal, + Extra Shot', NULL, '2026-02-15 01:02:11', '2026-02-15 01:02:11'),
(66, 59, 2, 'Cold Brew', 1, NULL, 51000.00, 51000.00, 'Large, Ice, Ice: Normal, Sugar: Normal, + Extra Shot, + Whipped Cream', NULL, '2026-02-15 01:14:59', '2026-02-15 01:14:59'),
(67, 60, 2, 'Cold Brew', 1, NULL, 48000.00, 48000.00, 'Large, Ice, Ice: Normal, Sugar: Normal, + Extra Shot', NULL, '2026-02-15 01:28:10', '2026-02-15 01:28:10'),
(68, 61, 2, 'Cold Brew', 1, NULL, 48000.00, 48000.00, 'Large, Ice, Ice: Normal, Sugar: Normal, + Extra Shot', NULL, '2026-02-15 01:28:22', '2026-02-15 01:28:22');

-- --------------------------------------------------------

--
-- Struktur dari tabel `payments`
--

CREATE TABLE `payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `method` enum('cash','qris','card','transfer') NOT NULL,
  `status` enum('unpaid','pending','paid','failed','refunded') NOT NULL DEFAULT 'pending',
  `amount` decimal(12,2) NOT NULL,
  `midtrans_transaction_id` varchar(255) DEFAULT NULL,
  `midtrans_order_id` varchar(255) DEFAULT NULL,
  `midtrans_response` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`midtrans_response`)),
  `paid_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `payments`
--

INSERT INTO `payments` (`id`, `order_id`, `method`, `status`, `amount`, `midtrans_transaction_id`, `midtrans_order_id`, `midtrans_response`, `paid_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'qris', 'paid', 85000.00, NULL, NULL, NULL, '2026-02-08 09:53:55', '2026-02-08 09:53:55', '2026-02-08 09:53:55'),
(2, 2, 'cash', 'paid', 55000.00, NULL, NULL, NULL, '2026-02-08 09:53:55', '2026-02-08 09:53:55', '2026-02-08 09:53:55'),
(3, 3, 'cash', 'pending', 120000.00, NULL, NULL, NULL, NULL, '2026-02-08 09:53:55', '2026-02-08 09:53:55'),
(4, 4, 'cash', 'paid', 51000.00, NULL, NULL, NULL, '2026-02-11 14:06:50', '2026-02-10 03:02:24', '2026-02-11 14:06:50'),
(5, 11, 'cash', 'paid', 48300.00, NULL, NULL, NULL, '2026-02-11 14:06:50', '2026-02-10 04:07:57', '2026-02-11 14:06:50'),
(6, 12, 'qris', 'paid', 56700.00, NULL, NULL, NULL, '2026-02-11 14:06:50', '2026-02-10 04:10:53', '2026-02-11 14:06:50'),
(7, 14, 'qris', 'pending', 146000.00, NULL, NULL, NULL, NULL, '2026-02-11 00:48:52', '2026-02-11 00:48:52'),
(8, 15, 'cash', 'paid', 48000.00, NULL, NULL, NULL, '2026-02-11 05:59:19', '2026-02-11 00:55:57', '2026-02-11 05:59:19'),
(9, 16, 'cash', 'paid', 20000.00, NULL, NULL, NULL, '2026-02-11 05:53:07', '2026-02-11 01:02:50', '2026-02-11 05:53:07'),
(10, 17, 'qris', 'pending', 45000.00, NULL, NULL, NULL, NULL, '2026-02-11 01:12:21', '2026-02-11 01:12:21'),
(11, 18, 'qris', 'pending', 38000.00, NULL, NULL, NULL, NULL, '2026-02-11 01:25:30', '2026-02-11 01:25:30'),
(12, 19, 'qris', 'pending', 38000.00, NULL, NULL, NULL, NULL, '2026-02-11 05:04:12', '2026-02-11 05:04:12'),
(13, 20, 'qris', 'pending', 45000.00, NULL, NULL, NULL, NULL, '2026-02-11 05:13:35', '2026-02-11 05:13:35'),
(14, 21, 'qris', 'pending', 45000.00, '3f84e282-0640-4dc5-85df-4d0d8808e1c9', NULL, NULL, NULL, '2026-02-11 05:26:16', '2026-02-11 05:26:16'),
(15, 22, 'cash', 'paid', 17850.00, NULL, NULL, NULL, '2026-02-11 06:55:34', '2026-02-11 06:55:34', '2026-02-11 06:55:34'),
(16, 23, 'qris', 'paid', 55650.00, NULL, NULL, NULL, '2026-02-11 14:06:50', '2026-02-11 06:56:26', '2026-02-11 14:06:50'),
(17, 24, 'qris', 'paid', 23100.00, NULL, NULL, NULL, '2026-02-11 14:06:50', '2026-02-11 06:57:18', '2026-02-11 14:06:50'),
(18, 25, 'qris', 'paid', 67200.00, NULL, NULL, NULL, '2026-02-11 07:25:11', '2026-02-11 07:25:11', '2026-02-11 07:25:11'),
(19, 27, 'card', 'paid', 64050.00, NULL, NULL, NULL, '2026-02-11 08:55:24', '2026-02-11 08:55:24', '2026-02-11 08:55:24'),
(20, 28, 'card', 'paid', 64050.00, NULL, NULL, NULL, '2026-02-11 08:55:25', '2026-02-11 08:55:25', '2026-02-11 08:55:25'),
(21, 29, 'card', 'paid', 39900.00, NULL, NULL, NULL, '2026-02-11 09:02:45', '2026-02-11 09:02:45', '2026-02-11 09:02:45'),
(22, 30, 'cash', 'unpaid', 75000.00, NULL, NULL, NULL, NULL, '2026-02-12 01:29:44', '2026-02-12 01:29:44'),
(23, 31, 'qris', 'pending', 60000.00, '162b50f1-860e-458d-b39d-72df4d288364', NULL, NULL, NULL, '2026-02-12 01:32:41', '2026-02-12 01:32:43'),
(24, 32, 'cash', 'unpaid', 48000.00, NULL, NULL, NULL, NULL, '2026-02-12 01:38:04', '2026-02-12 01:38:04'),
(25, 33, 'qris', 'pending', 50000.00, 'e7a34add-5b94-4172-8f9a-242c581dbb3b', NULL, NULL, NULL, '2026-02-12 01:39:13', '2026-02-12 01:39:15'),
(26, 34, 'qris', 'pending', 37000.00, '9be4249a-d115-4ece-9d64-6f4777a2fb66', NULL, NULL, NULL, '2026-02-12 01:45:29', '2026-02-12 01:45:30'),
(27, 35, 'qris', 'pending', 56000.00, 'c7651fbc-0f39-4451-8c8c-7c0efb40922a', NULL, NULL, NULL, '2026-02-12 02:47:33', '2026-02-12 02:47:35'),
(28, 36, 'cash', 'unpaid', 45000.00, NULL, NULL, NULL, NULL, '2026-02-12 06:15:20', '2026-02-12 06:15:20'),
(29, 37, 'qris', 'pending', 42000.00, 'd3c870d1-64ca-4aab-b7b3-5e15dcc77b41', NULL, NULL, NULL, '2026-02-12 06:16:29', '2026-02-12 06:16:30'),
(30, 38, 'qris', 'pending', 35000.00, '4f5926d2-dfd2-482d-951f-ef3c3eb39539', NULL, NULL, NULL, '2026-02-12 16:05:02', '2026-02-12 16:05:04'),
(31, 39, 'qris', 'pending', 137000.00, 'da38212a-b558-4933-af67-10ecbef3a66c', NULL, NULL, NULL, '2026-02-12 19:26:32', '2026-02-12 19:26:34'),
(32, 40, 'qris', 'pending', 112000.00, 'c52df968-8454-43f7-8a0e-918eb8be5707', NULL, NULL, NULL, '2026-02-13 03:44:33', '2026-02-13 03:44:35'),
(33, 41, 'qris', 'pending', 54000.00, 'e9850586-69ff-4f82-b031-9b5ce95e1d5c', NULL, NULL, NULL, '2026-02-13 03:50:38', '2026-02-13 03:50:40'),
(34, 42, 'cash', 'unpaid', 54000.00, NULL, NULL, NULL, NULL, '2026-02-13 03:51:03', '2026-02-13 03:51:03'),
(35, 43, 'cash', 'unpaid', 77000.00, NULL, NULL, NULL, NULL, '2026-02-14 14:38:36', '2026-02-14 14:38:36'),
(36, 44, 'cash', 'unpaid', 35000.00, NULL, NULL, NULL, NULL, '2026-02-14 14:40:01', '2026-02-14 14:40:01'),
(37, 45, 'qris', 'pending', 30000.00, '42a48940-b46f-4dd7-9911-f8b3a407d0e9', NULL, NULL, NULL, '2026-02-14 20:04:34', '2026-02-14 20:04:35'),
(38, 46, 'qris', 'pending', 58000.00, '9ba4bda2-4801-4692-8653-d6cc0b226bc8', NULL, NULL, NULL, '2026-02-14 21:24:05', '2026-02-14 21:24:07'),
(39, 47, 'qris', 'pending', 53000.00, '2e5402f2-60a8-40fd-8e26-62fea98e2fa6', NULL, NULL, NULL, '2026-02-14 21:50:28', '2026-02-14 21:50:30'),
(40, 48, 'qris', 'pending', 53000.00, 'd7106243-3889-4390-9078-39d64e494108', NULL, NULL, NULL, '2026-02-14 21:50:39', '2026-02-14 21:50:40'),
(41, 49, 'qris', 'pending', 53000.00, '1a1ca0e5-eaec-469f-8cc0-07cd1bd24f07', NULL, NULL, NULL, '2026-02-14 21:50:46', '2026-02-14 21:50:48'),
(42, 50, 'qris', 'pending', 48000.00, '460df959-08f8-4336-b238-003b10503d8f', NULL, NULL, NULL, '2026-02-14 23:20:31', '2026-02-14 23:20:33'),
(43, 51, 'qris', 'pending', 48000.00, '07863835-4098-4a9b-9b34-2fb89b909e2f', NULL, NULL, NULL, '2026-02-14 23:20:38', '2026-02-14 23:20:39'),
(44, 52, 'qris', 'pending', 48000.00, 'ac3902d7-f500-405e-9dde-92fa8ffba267', NULL, NULL, NULL, '2026-02-14 23:20:47', '2026-02-14 23:20:48'),
(45, 53, 'qris', 'pending', 48000.00, 'c7461c58-1cef-4a0c-aa36-f853c31a7696', NULL, NULL, NULL, '2026-02-14 23:20:58', '2026-02-14 23:20:59'),
(46, 54, 'qris', 'pending', 48000.00, '9d495390-e6d5-4969-9783-85142eb9795d', NULL, NULL, NULL, '2026-02-14 23:21:01', '2026-02-14 23:21:02'),
(47, 55, 'qris', 'pending', 48000.00, 'f109029a-35b4-4621-9188-cef3d9161797', NULL, NULL, NULL, '2026-02-14 23:23:11', '2026-02-14 23:23:12'),
(48, 56, 'qris', 'pending', 91000.00, 'd4bda5d7-7f09-461a-9ece-85e8dff409c3', NULL, NULL, NULL, '2026-02-15 00:58:25', '2026-02-15 00:58:27'),
(49, 57, 'qris', 'pending', 40000.00, '29d62dcd-abd9-47a8-89fb-178bca0588a6', NULL, NULL, NULL, '2026-02-15 01:02:05', '2026-02-15 01:02:07'),
(50, 58, 'qris', 'pending', 40000.00, '4f5a0cae-1088-4171-91a7-52bd58ee2460', NULL, NULL, NULL, '2026-02-15 01:02:13', '2026-02-15 01:02:15'),
(51, 59, 'qris', 'pending', 51000.00, 'f6ae592f-44f6-4e60-bf04-e794f0e9b90e', NULL, NULL, NULL, '2026-02-15 01:15:02', '2026-02-15 01:15:03'),
(52, 60, 'qris', 'pending', 48000.00, 'd81357a5-6e1e-4125-9490-da3057568688', NULL, NULL, NULL, '2026-02-15 01:28:13', '2026-02-15 01:28:15'),
(53, 61, 'qris', 'pending', 48000.00, '78eaede0-b18a-420c-ad7c-707cd6770e14', NULL, NULL, NULL, '2026-02-15 01:28:24', '2026-02-15 01:28:26');

-- --------------------------------------------------------

--
-- Struktur dari tabel `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `settings`
--

CREATE TABLE `settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`value`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tables`
--

CREATE TABLE `tables` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `table_number` varchar(20) NOT NULL,
  `capacity` int(11) NOT NULL DEFAULT 4,
  `status` enum('available','occupied','reserved') NOT NULL DEFAULT 'available',
  `qr_code_path` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `tables`
--

INSERT INTO `tables` (`id`, `table_number`, `capacity`, `status`, `qr_code_path`, `created_at`, `updated_at`) VALUES
(1, '1', 2, 'occupied', NULL, '2026-02-09 06:03:14', '2026-02-10 04:54:55'),
(2, '2', 2, 'occupied', NULL, '2026-02-09 06:03:14', '2026-02-11 09:28:27'),
(3, '3', 4, 'occupied', NULL, '2026-02-09 06:03:14', '2026-02-10 04:54:55'),
(4, '4', 4, 'occupied', NULL, '2026-02-09 06:03:14', '2026-02-11 09:28:27'),
(5, '5', 4, 'occupied', NULL, '2026-02-09 06:03:14', '2026-02-11 09:28:27'),
(6, '6', 4, 'available', NULL, '2026-02-09 06:03:14', '2026-02-09 06:03:14'),
(7, '7', 6, 'occupied', NULL, '2026-02-09 06:03:14', '2026-02-11 09:28:27'),
(8, '8', 6, 'occupied', NULL, '2026-02-09 06:03:14', '2026-02-11 09:28:27'),
(9, '9', 8, 'occupied', NULL, '2026-02-09 06:03:14', '2026-02-11 09:28:27'),
(10, '10', 8, 'available', NULL, '2026-02-09 06:03:14', '2026-02-09 06:03:14'),
(11, 'VIP-1', 4, 'available', NULL, '2026-02-09 06:03:14', '2026-02-09 06:03:14'),
(12, 'VIP-2', 6, 'available', NULL, '2026-02-09 06:03:14', '2026-02-09 06:03:14'),
(13, 'Outdoor-1', 4, 'available', NULL, '2026-02-09 06:03:14', '2026-02-09 06:03:14'),
(14, 'Outdoor-2', 4, 'available', NULL, '2026-02-09 06:03:14', '2026-02-09 06:03:14'),
(15, 'Bar-1', 2, 'available', NULL, '2026-02-09 06:03:14', '2026-02-09 06:03:14');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','cashier','manager') NOT NULL DEFAULT 'cashier',
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Admin Cafe', 'admin@cafe.com', NULL, '$2y$12$d9mSZL06BPwG/EEllcDE6Oi3jLHilkI6AcWiopKCr2y1XJQz4fmyS', 'admin', NULL, '2026-02-08 09:53:55', '2026-02-08 04:09:28'),
(2, 'Cashier 2', 'cashier@cafe.com', NULL, '$2y$12$Lpf/qSOTOrqT5IH55.8hXucxFKHWxtbbHWRYtcxkLC6AlDkN//GbC', 'cashier', NULL, '2026-02-08 09:53:55', '2026-02-10 04:15:04'),
(3, 'Manager 1', 'manager@cafe.com', NULL, '$2y$12$hBpR9PhFbyCZzOut3WUzpe/rjiCa/XzZ9QbIOvYQYdXK/zuwKptV.', 'manager', NULL, '2026-02-08 09:53:55', '2026-02-10 05:31:17');

-- --------------------------------------------------------

--
-- Stand-in struktur untuk tampilan `v_best_sellers`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `v_best_sellers` (
`id` bigint(20) unsigned
,`name` varchar(255)
,`category_name` varchar(255)
,`total_sold` decimal(32,0)
,`total_revenue` decimal(34,2)
);

-- --------------------------------------------------------

--
-- Stand-in struktur untuk tampilan `v_daily_sales`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `v_daily_sales` (
`sale_date` date
,`total_orders` bigint(21)
,`total_revenue` decimal(34,2)
,`avg_order_value` decimal(16,6)
);

-- --------------------------------------------------------

--
-- Struktur untuk view `v_best_sellers`
--
DROP TABLE IF EXISTS `v_best_sellers`;

CREATE ALGORITHM=UNDEFINED DEFINER=`fazriluk`@`localhost` SQL SECURITY DEFINER VIEW `v_best_sellers`  AS SELECT `m`.`id` AS `id`, `m`.`name` AS `name`, `c`.`name` AS `category_name`, sum(`oi`.`quantity`) AS `total_sold`, sum(`oi`.`subtotal`) AS `total_revenue` FROM (((`order_items` `oi` join `menus` `m` on(`oi`.`menu_id` = `m`.`id`)) join `categories` `c` on(`m`.`category_id` = `c`.`id`)) join `orders` `o` on(`oi`.`order_id` = `o`.`id`)) WHERE `o`.`status` = 'completed' GROUP BY `m`.`id`, `m`.`name`, `c`.`name` ORDER BY sum(`oi`.`quantity`) DESC ;

-- --------------------------------------------------------

--
-- Struktur untuk view `v_daily_sales`
--
DROP TABLE IF EXISTS `v_daily_sales`;

CREATE ALGORITHM=UNDEFINED DEFINER=`fazriluk`@`localhost` SQL SECURITY DEFINER VIEW `v_daily_sales`  AS SELECT cast(`o`.`created_at` as date) AS `sale_date`, count(distinct `o`.`id`) AS `total_orders`, sum(`o`.`total_amount`) AS `total_revenue`, avg(`o`.`total_amount`) AS `avg_order_value` FROM `orders` AS `o` WHERE `o`.`status` = 'completed' GROUP BY cast(`o`.`created_at` as date) ;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indeks untuk tabel `menus`
--
ALTER TABLE `menus`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indeks untuk tabel `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_number` (`order_number`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `orders_status_index` (`status`),
  ADD KEY `orders_created_at_index` (`created_at`),
  ADD KEY `orders_payment_status_index` (`payment_status`);

--
-- Indeks untuk tabel `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `menu_id` (`menu_id`);

--
-- Indeks untuk tabel `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `payments_status_index` (`status`);

--
-- Indeks untuk tabel `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indeks untuk tabel `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `settings_key_unique` (`key`);

--
-- Indeks untuk tabel `tables`
--
ALTER TABLE `tables`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `table_number` (`table_number`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `menus`
--
ALTER TABLE `menus`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT untuk tabel `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT untuk tabel `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT untuk tabel `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `tables`
--
ALTER TABLE `tables`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `menus`
--
ALTER TABLE `menus`
  ADD CONSTRAINT `menus_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`menu_id`) REFERENCES `menus` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
