-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 09, 2026 at 05:42 PM
-- Server version: 8.0.43
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `login`
--

-- --------------------------------------------------------

--
-- Table structure for table `supplier`
--

CREATE TABLE `supplier` (
  `Supplier_ID` int NOT NULL,
  `CompanyName` varchar(150) NOT NULL,
  `ContactPerson` varchar(100) NOT NULL,
  `Address` varchar(250) NOT NULL,
  `City` varchar(100) DEFAULT NULL,
  `Email` varchar(100) NOT NULL,
  `PhoneNumber` varchar(100) NOT NULL,
  `ProductCategory` enum('Noodles and Instant Meals','Frozen Hotpot Goods','Side Dishes','Canned Goods','Beverages','Sweets and Snacks','Ice Cream','Beauty & Personal Care') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `supplier`
--

INSERT INTO `supplier` (`Supplier_ID`, `CompanyName`, `ContactPerson`, `Address`, `City`, `Email`, `PhoneNumber`, `ProductCategory`) VALUES
(1, 'JIN MART KOREAN GROCERY - PASAY 진마트 파사이', 'BUGOY', 'HK SUN PLAZA, DIOSDADO MACAPAGAL BLVD, PASAY CITY, 1300', 'METRO MANILA', 'jinmart@gmail.com', '09177111844', 'Noodles and Instant Meals'),
(2, 'JOSE', 'dela cruz', 'MAKATI', NULL, 'josedelacruz@gmail.com', '09112233445', 'Frozen Hotpot Goods'),
(3, 'James', 'DELAPAZ', 'Pasig', NULL, 'jamesdelapaz@gmail.com', '09112233445', 'Side Dishes'),
(4, 'john', 'de;lo santos', 'malabon', NULL, 'johndelosantos@gmail.com', '09112233445', 'Canned Goods'),
(5, 'michael', 'JACINTO', 'QUEZON CITY', NULL, 'michaeljacinto@gmail.com', '09112233445', 'Beverages'),
(6, 'thomas', 'hilario', 'tondo', NULL, 'thomashilario@gmail.com', '09112233445', 'Sweets and Snacks'),
(7, 'joshua', 'santos', 'recto', NULL, 'joshuasantos@gmail.com', '09112233445', 'Ice Cream'),
(8, 'alex', 'santos', 'baclaran', NULL, 'alexsantos@gmail.com', '09112233445', 'Beauty & Personal Care');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `supplier`
--
ALTER TABLE `supplier`
  ADD PRIMARY KEY (`Supplier_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `supplier`
--
ALTER TABLE `supplier`
  MODIFY `Supplier_ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
