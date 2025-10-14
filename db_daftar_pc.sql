-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Oct 13, 2025 at 07:52 AM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_daftar_pc`
--

-- --------------------------------------------------------

--
-- Table structure for table `pc_list`
--

DROP TABLE IF EXISTS `pc_list`;
CREATE TABLE IF NOT EXISTS `pc_list` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama_pc` varchar(100) NOT NULL,
  `nama_user` varchar(100) NOT NULL,
  `nomor_asset` varchar(50) NOT NULL,
  `nomor_ip` varchar(15) NOT NULL,
  `status` varchar(50) NOT NULL,
  `tanggal_produksi` date NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nomor_asset` (`nomor_asset`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pc_list`
--

INSERT INTO `pc_list` (`id`, `nama_pc`, `nama_user`, `nomor_asset`, `nomor_ip`, `status`, `tanggal_produksi`, `created_at`, `updated_at`) VALUES
(9, 'PC-FIN-002', 'Siti Rahayu', 'AST-2023-002', '192.168.1.11', 'Maintenance', '2023-02-20', '2025-10-11 15:16:51', '2025-10-11 15:16:51');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
