-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 28, 2024 at 08:13 AM
-- Server version: 8.4.2
-- PHP Version: 8.3.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `boiler_plate`
--

-- --------------------------------------------------------

--
-- Table structure for table `time_zones`
--

CREATE TABLE `time_zones` (
  `id` bigint UNSIGNED NOT NULL,
  `time_zone` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `utc_offset` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `time_zones`
--

INSERT INTO `time_zones` (`id`, `time_zone`, `utc_offset`, `created_at`, `updated_at`) VALUES
(1, '(UTC -12:00) Etc/GMT+12', '-12:00', '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(2, '(UTC -11:00) Pacific/Midway', '-11:00', '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(3, '(UTC -11:00) Pacific/Niue', '-11:00', '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(4, '(UTC -11:00) Pacific/Pago_Pago', '-11:00', '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(5, '(UTC -10:00) Pacific/Honolulu', '-10:00', '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(6, '(UTC -10:00) Pacific/Rarotonga', '-10:00', '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(7, '(UTC -10:00) Pacific/Tahiti', '-10:00', '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(8, '(UTC -09:30) Pacific/Marquesas', '-09:30', '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(9, '(UTC -09:00) America/Anchorage', '-09:00', '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(10, '(UTC -09:00) Pacific/Gambier', '-09:00', '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(11, '(UTC -08:00) America/Los_Angeles', '-08:00', '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(12, '(UTC -08:00) America/Tijuana', '-08:00', '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(13, '(UTC -08:00) Pacific/Pitcairn', '-08:00', '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(14, '(UTC -07:00) America/Denver', '-07:00', '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(15, '(UTC -07:00) America/Phoenix', '-07:00', '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(16, '(UTC -07:00) America/Chihuahua', '-07:00', '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(17, '(UTC -06:00) America/Chicago', '-06:00', '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(18, '(UTC -06:00) America/Guatemala', '-06:00', '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(19, '(UTC -06:00) America/El_Salvador', '-06:00', '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(20, '(UTC -06:00) America/Mexico_City', '-06:00', '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(21, '(UTC -05:00) America/New_York', '-05:00', '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(22, '(UTC -05:00) America/Bogota', '-05:00', '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(23, '(UTC -05:00) America/Havana', '-05:00', '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(24, '(UTC -04:00) Atlantic/Bermuda', '-04:00', '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(25, '(UTC -04:00) America/Caracas', '-04:00', '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(26, '(UTC -04:00) America/Santiago', '-04:00', '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(27, '(UTC -03:30) America/St_Johns', '-03:30', '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(28, '(UTC -03:00) America/Argentina/Buenos_Aires', '-03:00', '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(29, '(UTC -03:00) America/Montevideo', '-03:00', '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(30, '(UTC -02:00) Atlantic/South_Georgia', '-02:00', '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(31, '(UTC -01:00) Atlantic/Azores', '-01:00', '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(32, '(UTC -01:00) Atlantic/Cape_Verde', '-01:00', '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(33, '(UTC +00:00) Europe/London', '+00:00', '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(34, '(UTC +00:00) UTC', '+00:00', '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(35, '(UTC +01:00) Europe/Paris', '+01:00', '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(36, '(UTC +01:00) Europe/Berlin', '+01:00', '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(37, '(UTC +01:00) Africa/Lagos', '+01:00', '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(38, '(UTC +02:00) Europe/Athens', '+02:00', '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(39, '(UTC +02:00) Africa/Cairo', '+02:00', '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(40, '(UTC +02:00) Asia/Jerusalem', '+02:00', '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(41, '(UTC +03:00) Europe/Moscow', '+03:00', '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(42, '(UTC +03:00) Asia/Baghdad', '+03:00', '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(43, '(UTC +03:00) Africa/Nairobi', '+03:00', '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(44, '(UTC +03:30) Asia/Tehran', '+03:30', '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(45, '(UTC +04:00) Asia/Dubai', '+04:00', '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(46, '(UTC +04:00) Europe/Samara', '+04:00', '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(47, '(UTC +04:30) Asia/Kabul', '+04:30', '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(48, '(UTC +05:00) Asia/Karachi', '+05:00', '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(49, '(UTC +05:30) Asia/Kolkata', '+05:30', '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(50, '(UTC +05:45) Asia/Kathmandu', '+05:45', '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(51, '(UTC +06:00) Asia/Dhaka', '+06:00', '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(52, '(UTC +06:30) Asia/Yangon', '+06:30', '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(53, '(UTC +07:00) Asia/Bangkok', '+07:00', '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(54, '(UTC +08:00) Asia/Shanghai', '+08:00', '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(55, '(UTC +09:00) Asia/Tokyo', '+09:00', '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(56, '(UTC +09:30) Australia/Adelaide', '+09:30', '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(57, '(UTC +10:00) Australia/Sydney', '+10:00', '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(58, '(UTC +11:00) Pacific/Noumea', '+11:00', '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(59, '(UTC +12:00) Pacific/Fiji', '+12:00', '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(60, '(UTC +13:00) Pacific/Tongatapu', '+13:00', '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(61, '(UTC +14:00) Pacific/Kiritimati', '+14:00', '2024-11-26 00:13:17', '2024-11-26 00:13:17');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `time_zones`
--
ALTER TABLE `time_zones`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `time_zones`
--
ALTER TABLE `time_zones`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
