-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 10, 2025 at 05:22 AM
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
-- Database: `ridjik`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id_admin` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id_admin`, `username`, `password`) VALUES
(1, 'admin', 'admin123');

-- --------------------------------------------------------

--
-- Table structure for table `bahan`
--

CREATE TABLE `bahan` (
  `id_bahan` int(11) NOT NULL,
  `nama_bahan` varchar(50) NOT NULL,
  `nama_suppliyer` varchar(50) NOT NULL,
  `stok_bahan` int(11) NOT NULL,
  `harga` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bahan`
--

INSERT INTO `bahan` (`id_bahan`, `nama_bahan`, `nama_suppliyer`, `stok_bahan`, `harga`) VALUES
(0, 'sabun air', 'sandi', 712, 6000),
(1, 'gliserin', 'toko abc', 5, 50000),
(2, 'alkohol murni', 'toko def', 6, 50000),
(3, 'sabun basah', 'datasari', 12, 9000),
(4, 'sampo', 'daka', 17, 7000),
(5, 'sampo', 'daka', 17, 7000),
(6, 'abc', 'tep', 6, 31000),
(7, 'abc', 'tep', 6, 31000),
(8, 'erq', 'opas', 54, 25000),
(9, 'ryu', 'yui', 32, 15000),
(10, 'ryu', 'yui', 32, 15000);

-- --------------------------------------------------------

--
-- Table structure for table `detail_transaksi`
--

CREATE TABLE `detail_transaksi` (
  `id_detail_transaksi` int(11) NOT NULL,
  `id_transaksi` int(11) NOT NULL,
  `produk` varchar(100) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `harga` bigint(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `detail_transaksi`
--

INSERT INTO `detail_transaksi` (`id_detail_transaksi`, `id_transaksi`, `produk`, `jumlah`, `harga`) VALUES
(1, 1, 'abc', 2, 20000),
(2, 2, 'cba', 2, 40000),
(3, 3, 'dca', 2, 20000),
(4, 4, 'botol parfum', 1, 44000000000),
(5, 5, 'stiker', 1, 2147483647),
(6, 6, 'afsd', 5, 5000),
(7, 7, 'dfg', 10, 15000),
(9, 9, 'sabun bersih ', 4, 13000);

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `id_produk` int(11) NOT NULL,
  `id_resep` int(11) NOT NULL,
  `kode_produk` int(5) NOT NULL,
  `nama_produk` varchar(50) NOT NULL,
  `stok_produk` int(11) NOT NULL,
  `harga` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`id_produk`, `id_resep`, `kode_produk`, `nama_produk`, `stok_produk`, `harga`) VALUES
(1, 6, 12344, 'sabun mandi', 4, 20000),
(2, 13, 12334, 'sabun cuci piring', 6, 20000),
(3, 7, 97421, 'pewangi laundry', 5, 0),
(7, 1, 12341, 'sabun muka', 32, 0),
(10, 9, 15214, 'pewangi ruangan', 80, 6000),
(11, 5, 12366, 'sabun muka pria', 20, 0),
(12, 2, 12348, 'pembersih sepatu', 30, 0),
(13, 12, 12349, 'sabun mandi cair', 6, 40000),
(16, 11, 11111, 'syabun', 123, 512811),
(20, 6, 89076, 'jja', 12, 412),
(23, 3, 33212, 'jjaz', 12, 4120055),
(26, 4, 55667, 'tes2', 99, 99000),
(38, 10, 23443, 'asdew', 32, 10000),
(40, 0, 11224, 'reya', 33, 20000),
(42, 0, 2131, 'sa', 12334, 121000),
(44, 0, 14212, 'dfs', 2312, 314000),
(46, 0, 20004, 'fs', 423, 345000),
(47, 7, 13123, 'kepo', 12, 10000),
(54, 0, 66543, 'sabun bersih', 14, 14000),
(58, 0, 66542, 'sabun cuci muka standart', 20, 21000);

-- --------------------------------------------------------

--
-- Table structure for table `resep`
--

CREATE TABLE `resep` (
  `id_resep` int(11) NOT NULL,
  `id_produk` int(11) NOT NULL,
  `bahan` text NOT NULL,
  `cara` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `resep`
--

INSERT INTO `resep` (`id_resep`, `id_produk`, `bahan`, `cara`, `created_at`, `updated_at`) VALUES
(2, 12, '1. zcz', '1. zcz', '2025-06-05 12:20:03', '2025-06-05 12:20:03'),
(3, 23, '1. sad', '1. asdad', '2025-06-05 12:20:49', '2025-06-05 12:20:49'),
(4, 26, '1. ad', '1. ad', '2025-06-05 12:26:57', '2025-06-05 12:26:57'),
(5, 11, '1. adadada', '1. adadad', '2025-06-05 12:27:20', '2025-06-05 12:27:20'),
(6, 20, '1. ad', '1. dad', '2025-06-05 12:31:02', '2025-06-05 12:31:02'),
(7, 47, '1. ljhnoj\r\n2. ojkhojuh', '1. okjhouj\r\n2. oujh0ouh', '2025-06-05 12:35:04', '2025-06-05 12:35:04'),
(9, 10, '1. asa', '1. sa', '2025-06-06 05:45:05', '2025-06-06 05:45:05'),
(10, 38, '1. asa', '1. as', '2025-06-06 05:50:22', '2025-06-06 05:50:22'),
(11, 16, '1. sas', '1. as', '2025-06-06 05:53:45', '2025-06-06 05:53:45'),
(12, 13, '1. as', '1. sa', '2025-06-06 06:12:42', '2025-06-06 06:12:42'),
(13, 2, '1. sa', '1. sa', '2025-06-06 06:39:35', '2025-06-06 06:39:35');

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `id_transaksi` int(11) NOT NULL,
  `jenis` varchar(20) NOT NULL,
  `tanggal` date NOT NULL,
  `nota` varchar(255) DEFAULT NULL,
  `nama_toko` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaksi`
--

INSERT INTO `transaksi` (`id_transaksi`, `jenis`, `tanggal`, `nota`, `nama_toko`) VALUES
(1, 'pembelian', '2025-05-27', 'nota_68479ef9073aa.jpg', 'a'),
(2, 'penjualan', '2025-05-27', '', 'aa'),
(3, 'penjualan', '2025-05-27', '', 'aaa'),
(4, 'penjualan', '2025-05-27', '', 'aaaa'),
(5, 'pembelian', '2025-05-27', '', 'aaaaa'),
(6, 'pembelian', '2025-05-30', '', 'aaaaaa'),
(7, 'pembelian', '2025-05-30', '', 'abcd'),
(8, 'pembelian', '2025-05-30', '', 'abcd'),
(9, 'penjualan', '2025-05-30', '', 'maju ');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id_user` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id_user`, `email`, `username`, `password`) VALUES
(1, '', 'user', 'user123'),
(2, 'erwinsusilo404@gmail.com', 'erwin666', '$2y$10$bOqirfSXC8d2/fLxCxkYuOSeHPGMtDv6dG5kD4qGO2x8.PjHnIpXm');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`);

--
-- Indexes for table `bahan`
--
ALTER TABLE `bahan`
  ADD PRIMARY KEY (`id_bahan`);

--
-- Indexes for table `detail_transaksi`
--
ALTER TABLE `detail_transaksi`
  ADD PRIMARY KEY (`id_detail_transaksi`),
  ADD KEY `transaksi_id` (`id_transaksi`);

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id_produk`),
  ADD UNIQUE KEY `kode_produk` (`kode_produk`);

--
-- Indexes for table `resep`
--
ALTER TABLE `resep`
  ADD PRIMARY KEY (`id_resep`),
  ADD KEY `id_produk` (`id_produk`);

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id_transaksi`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `detail_transaksi`
--
ALTER TABLE `detail_transaksi`
  MODIFY `id_detail_transaksi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `id_produk` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `resep`
--
ALTER TABLE `resep`
  MODIFY `id_resep` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id_transaksi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `detail_transaksi`
--
ALTER TABLE `detail_transaksi`
  ADD CONSTRAINT `detail_transaksi_ibfk_1` FOREIGN KEY (`id_transaksi`) REFERENCES `transaksi` (`id_transaksi`) ON DELETE CASCADE;

--
-- Constraints for table `resep`
--
ALTER TABLE `resep`
  ADD CONSTRAINT `resep_ibfk_1` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
