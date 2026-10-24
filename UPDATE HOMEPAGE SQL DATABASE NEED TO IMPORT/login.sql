-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 24, 2026 at 04:39 PM
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

DELIMITER $$
--
-- Functions
--
CREATE DEFINER=`root`@`localhost` FUNCTION `get_inventory_status` (`qty` INT) RETURNS VARCHAR(20) CHARSET utf8mb4 DETERMINISTIC RETURN (
    CASE 
        WHEN qty > 10 THEN 'IN STOCK'
        WHEN qty > 0 THEN 'LOW STOCK'
        ELSE 'OUT OF STOCK'
    END
)$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `analytics`
--

CREATE TABLE `analytics` (
  `AnalyticsID` int NOT NULL,
  `Forecast_ID` int NOT NULL,
  `Report_Date` date NOT NULL,
  `SalesMetrics` int NOT NULL,
  `Account_ID` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `category_markup`
--

CREATE TABLE `category_markup` (
  `CategoryMarkup_ID` int NOT NULL,
  `CategoryName` varchar(100) NOT NULL,
  `MarkupPercent` decimal(7,2) NOT NULL,
  `UpdatedAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ;

--
-- Dumping data for table `category_markup`
--

INSERT INTO `category_markup` (`CategoryMarkup_ID`, `CategoryName`, `MarkupPercent`, `UpdatedAt`) VALUES
(1, 'Noodles and Instant Meals', 35.00, '2026-10-21 13:16:11'),
(2, 'Ice Cream', 30.00, '2026-10-21 10:11:39');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `Customer_ID` int NOT NULL,
  `CustomerName` varchar(100) NOT NULL,
  `Location` varchar(250) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `PhoneNumber` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customersreturns`
--

CREATE TABLE `customersreturns` (
  `CReturn_ID` int NOT NULL,
  `ReferenceNo` varchar(100) NOT NULL,
  `Quantity` int NOT NULL,
  `ReturnedDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ReasonForReturn` enum('Wrong-Item','Damaged','Expired','Other') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `Product_ID` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `customersreturns`
--

INSERT INTO `customersreturns` (`CReturn_ID`, `ReferenceNo`, `Quantity`, `ReturnedDate`, `ReasonForReturn`, `Product_ID`) VALUES
(9, '20250900001', 1, '2026-10-08 16:00:00', 'Wrong-Item', 14),
(10, '20250900001', 1, '2026-10-09 16:02:11', 'Wrong-Item', 13),
(11, '20250900001', 1, '2026-10-09 16:00:00', 'Damaged', 14),
(12, '20250900001', 1, '2026-10-09 16:02:30', 'Other', 10);

-- --------------------------------------------------------

--
-- Table structure for table `daily_sales`
--

CREATE TABLE `daily_sales` (
  `DailySales_ID` int NOT NULL,
  `PeriodStart` date DEFAULT NULL,
  `PeriodEnd` date DEFAULT NULL,
  `Product_ID` int DEFAULT NULL,
  `TotalSales` decimal(10,2) DEFAULT NULL,
  `TotalQuantity` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `daily_sales_summary`
-- (See below for the actual view)
--
CREATE TABLE `daily_sales_summary` (
`Product_ID` int
,`ProductName` varchar(100)
,`SalesDate` timestamp
,`TotalQuantity` decimal(32,0)
,`TotalRevenue` decimal(32,0)
);

-- --------------------------------------------------------

--
-- Table structure for table `daily_total_sales`
--

CREATE TABLE `daily_total_sales` (
  `EntireDailySales_ID` int NOT NULL,
  `PeriodStart` date NOT NULL,
  `PeriodEnd` date NOT NULL,
  `TotalSales` decimal(12,2) NOT NULL DEFAULT '0.00',
  `TotalQuantity` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `expiration`
--

CREATE TABLE `expiration` (
  `Expiration_ID` int NOT NULL,
  `Product_ID` int NOT NULL,
  `BatchNum` varchar(50) NOT NULL,
  `ExpirationDate` date NOT NULL,
  `Quantity` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `expiration`
--

INSERT INTO `expiration` (`Expiration_ID`, `Product_ID`, `BatchNum`, `ExpirationDate`, `Quantity`) VALUES
(1, 1, 'BATCH-1-001', '2026-10-24', 37),
(2, 2, 'BATCH-2-001', '2026-10-24', 0),
(3, 3, 'BATCH-3-001', '2026-10-24', 11),
(4, 4, 'BATCH-4-001', '2026-10-24', 1),
(5, 5, 'BATCH-5-001', '2026-10-24', 1),
(6, 6, 'BATCH-6-001', '2026-10-24', 9),
(7, 7, 'BATCH-7-001', '2026-10-24', 10),
(8, 8, 'BATCH-8-001', '2026-10-24', 8),
(9, 9, 'BATCH-9-001', '2026-10-24', 28),
(10, 10, 'BATCH-10-001', '2026-10-24', 40),
(11, 11, 'BATCH-11-001', '2026-10-24', 40),
(12, 12, 'BATCH-12-001', '2026-10-24', 16),
(13, 13, 'BATCH-13-001', '2026-10-24', 38),
(14, 14, 'BATCH-14-001', '2026-10-24', 22),
(15, 15, 'BATCH-15-001', '2026-10-24', 22),
(16, 16, 'BATCH-16-001', '2026-10-24', 40),
(17, 1, 'BATCH-1-002', '2026-11-24', 40),
(18, 17, 'BATCH-17-001', '2026-10-22', 10),
(19, 17, 'BATCH-17-002', '2027-01-09', 0),
(20, 15, 'BATCH-15-002', '2026-10-12', 24),
(21, 28, 'BATCH-28-001', '2026-11-24', 8);

-- --------------------------------------------------------

--
-- Table structure for table `forecast`
--

CREATE TABLE `forecast` (
  `Forecast_ID` int NOT NULL,
  `ForecastType` text NOT NULL,
  `Product_ID` int DEFAULT NULL,
  `ForecastPeriod` text NOT NULL,
  `ForecastStart` date NOT NULL,
  `ForecastEnd` date NOT NULL,
  `ProjectedSales` int NOT NULL,
  `ConfidenceLevel` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `general_markup`
--

CREATE TABLE `general_markup` (
  `Markup_ID` tinyint UNSIGNED NOT NULL,
  `MarkupPercent` decimal(7,2) NOT NULL DEFAULT '0.00',
  `UpdatedAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ;

--
-- Dumping data for table `general_markup`
--

INSERT INTO `general_markup` (`Markup_ID`, `MarkupPercent`, `UpdatedAt`) VALUES
(1, 25.00, '2026-10-21 13:15:14');

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `Product_ID` int NOT NULL,
  `LocationS` varchar(100) NOT NULL,
  `LocationR` varchar(100) NOT NULL,
  `Price` decimal(10,2) NOT NULL,
  `Inventory` int DEFAULT NULL,
  `UnitIN` datetime DEFAULT NULL,
  `UnitOut` timestamp NULL DEFAULT NULL,
  `Status` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `ExpirationDate` date DEFAULT NULL,
  `Barcode` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`Product_ID`, `LocationS`, `LocationR`, `Price`, `Inventory`, `UnitIN`, `UnitOut`, `Status`, `ExpirationDate`, `Barcode`) VALUES
(1, 'Shelf A', 'Row A', 78.00, 139, '2026-10-22 13:18:11', '2026-07-11 04:00:00', 'IN-STOCK', '2026-10-24', '6976693307846'),
(2, 'Shelf A', 'Row B', 60.00, 72, '2026-10-24 13:58:55', '2026-10-23 10:05:37', 'IN-STOCK', '2026-10-24', '6936520871469'),
(3, 'Shelf A', 'Row B', 280.00, 41, '2026-10-22 13:13:40', '2026-10-20 06:51:13', 'IN-STOCK', '2026-10-24', '2000224312185'),
(4, 'Shelf A', 'Row B', 315.00, 81, '2026-10-22 13:14:16', '2026-10-23 16:13:35', 'IN-STOCK', '2026-10-24', '2000219213022'),
(5, 'Shelf A', 'Row C', 250.00, 13, '2026-10-22 13:15:43', '2026-10-09 13:32:10', 'IN-STOCK', '2026-10-24', '2000213074353'),
(6, 'Shelf A', 'Row C', 135.00, 21, '2026-10-22 13:15:20', '2026-10-09 13:32:10', 'IN-STOCK', '2026-10-24', '2000218723423'),
(7, 'Shelf A', 'Row D', 190.00, 46, '2026-10-22 13:13:58', '2026-10-09 13:32:10', 'IN-STOCK', '2026-10-24', '2000225506484'),
(8, 'Shelf A', 'Row D', 190.00, 20, '2026-10-22 18:55:53', '2026-10-24 08:06:14', 'IN-STOCK', '2026-10-24', '11111111111'),
(9, 'Shelf B', 'Row A', 40.00, 688, '2026-10-22 13:15:30', '2026-10-09 13:32:10', 'IN-STOCK', '2026-10-24', '2000212162167'),
(10, 'Shelf A', 'Row A', 55.00, 70, '2026-10-10 00:27:30', '2026-07-11 04:00:00', 'IN-STOCK', '2026-10-24', '2222222222222'),
(11, 'Shelf B', 'Row B', 55.00, 120, '2026-10-22 13:11:54', '2026-07-11 04:00:00', 'IN-STOCK', '2026-10-24', '2000225506484'),
(12, 'Shelf B', 'Row B', 99.00, 32, '2026-10-22 13:12:53', '2026-07-11 04:00:00', 'IN-STOCK', '2026-10-24', '3333333333333'),
(13, 'Shelf B', 'Row C', 45.00, 328, '2026-10-22 18:59:58', '2026-10-24 07:08:40', 'IN-STOCK', '2026-10-24', '6932808788887'),
(14, 'Shelf B', 'Row C', 75.00, 118, '2026-10-22 13:11:32', '2026-10-23 10:06:48', 'IN-STOCK', '2026-10-24', '4444444444444'),
(15, 'Shelf B', 'Row D', 420.00, 46, '2026-10-10 00:27:08', '2026-10-23 07:36:58', 'IN-STOCK', '2026-10-12', '6976693307846'),
(16, 'Shelf B', 'Row D', 580.00, 400, '2026-10-22 13:16:16', '2026-07-11 04:00:00', 'IN-STOCK', '2026-10-24', '5555555555555'),
(17, 'Shelf A', 'Row A', 190.00, 184, '2026-10-22 23:50:24', '2026-10-23 07:33:00', 'IN-STOCK', '2027-01-09', '7777777777777'),
(27, 'Shelf A', 'Row A', 1.00, 2, '2026-10-23 18:08:28', '2026-10-23 16:14:35', 'LOW-STOCK', NULL, '1212121212121'),
(28, 'Shelf A', 'Row A', 0.00, 8, '2026-10-24 19:59:59', '2026-10-24 12:00:36', 'LOW-STOCK', '2026-11-24', '1313131313131');

-- --------------------------------------------------------

--
-- Table structure for table `inventory_batch`
--

CREATE TABLE `inventory_batch` (
  `Batch_ID` int NOT NULL,
  `Product_ID` int NOT NULL,
  `Restock_ID` int NOT NULL,
  `Supplier_ID` int NOT NULL,
  `BatchNum` varchar(50) NOT NULL,
  `ExpirationDate` date DEFAULT NULL,
  `ReceivedDate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Quantity` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `item_to_order`
--

CREATE TABLE `item_to_order` (
  `ItemToOrder_ID` int NOT NULL,
  `ListToOrder_ID` int NOT NULL,
  `Product_ID` int NOT NULL,
  `Supplier_ID` int DEFAULT NULL,
  `Current_Stock` int NOT NULL DEFAULT '0',
  `Predicted_Demand` int NOT NULL DEFAULT '0',
  `Net_Demand` int NOT NULL DEFAULT '0',
  `Pack_Size` int NOT NULL DEFAULT '1',
  `Recommended_Order_Quantity` int NOT NULL DEFAULT '0',
  `Ordered_Quantity` int NOT NULL DEFAULT '0',
  `Unit_Cost` decimal(12,2) NOT NULL DEFAULT '0.00',
  `Line_Total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `Received_Quantity` int NOT NULL DEFAULT '0',
  `Item_Status` enum('Pending','Ordered','Partially-Received','Received','Cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'Pending',
  `Notes` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `item_to_order`
--

INSERT INTO `item_to_order` (`ItemToOrder_ID`, `ListToOrder_ID`, `Product_ID`, `Supplier_ID`, `Current_Stock`, `Predicted_Demand`, `Net_Demand`, `Pack_Size`, `Recommended_Order_Quantity`, `Ordered_Quantity`, `Unit_Cost`, `Line_Total`, `Received_Quantity`, `Item_Status`, `Notes`) VALUES
(1, 1, 1, 1, 0, 0, 0, 40, 0, 40, 55.00, 2200.00, 40, 'Received', ''),
(2, 1, 2, 1, 0, 0, 0, 40, 0, 40, 42.00, 1680.00, 40, 'Received', ''),
(3, 2, 3, 2, 0, 0, 0, 15, 0, 15, 190.00, 2850.00, 15, 'Received', ''),
(4, 2, 4, 2, 0, 0, 0, 10, 0, 10, 210.00, 2100.00, 10, 'Received', ''),
(5, 3, 5, 3, 0, 0, 0, 12, 0, 12, 175.00, 2100.00, 12, 'Received', ''),
(6, 3, 6, 3, 0, 0, 0, 12, 0, 12, 90.00, 1080.00, 12, 'Received', ''),
(7, 4, 7, 4, 0, 0, 0, 12, 0, 12, 140.00, 1680.00, 12, 'Received', ''),
(8, 4, 8, 4, 0, 0, 0, 12, 0, 12, 140.00, 1680.00, 12, 'Received', ''),
(9, 5, 9, 5, 0, 0, 0, 30, 0, 30, 26.00, 780.00, 30, 'Received', ''),
(10, 5, 10, 5, 0, 0, 0, 40, 0, 40, 35.00, 1400.00, 40, 'Received', ''),
(11, 6, 11, 6, 0, 0, 0, 40, 0, 40, 38.00, 1520.00, 40, 'Received', ''),
(12, 6, 12, 6, 0, 0, 0, 16, 0, 16, 68.00, 1088.00, 16, 'Received', ''),
(13, 7, 13, 7, 0, 0, 0, 40, 0, 40, 25.00, 1000.00, 40, 'Received', ''),
(14, 7, 14, 7, 0, 0, 0, 24, 0, 24, 45.00, 1080.00, 24, 'Received', ''),
(15, 8, 15, 8, 0, 0, 0, 24, 0, 24, 280.00, 6720.00, 24, 'Received', ''),
(16, 8, 16, 8, 0, 0, 0, 40, 0, 40, 390.00, 15600.00, 40, 'Received', ''),
(17, 9, 1, 1, 39, 0, 0, 40, 0, 40, 55.00, 2200.00, 40, 'Received', ''),
(18, 10, 4, 8, 5, 76, 71, 10, 80, 80, 210.00, 16800.00, 0, 'Pending', NULL),
(19, 11, 9, 5, 30, 85, 55, 30, 60, 60, 26.00, 1560.00, 0, 'Pending', NULL),
(20, 11, 10, 5, 40, 53, 13, 40, 40, 40, 35.00, 1400.00, 0, 'Pending', NULL),
(21, 12, 2, 1, 38, 89, 51, 40, 80, 80, 42.00, 3360.00, 0, 'Pending', NULL),
(22, 13, 6, 3, 10, 47, 37, 12, 48, 48, 90.00, 4320.00, 0, 'Pending', NULL),
(23, 13, 5, 3, 5, 33, 28, 12, 36, 36, 175.00, 6300.00, 0, 'Pending', NULL),
(24, 13, 13, 3, 40, 53, 13, 40, 40, 40, 25.00, 1000.00, 0, 'Pending', NULL),
(25, 13, 17, 3, 0, 10, 10, 12, 12, 12, 150.00, 1800.00, 0, 'Pending', NULL),
(26, 14, 7, 4, 12, 32, 20, 12, 24, 24, 140.00, 3360.00, 0, 'Pending', NULL),
(27, 15, 3, 6, 15, 32, 17, 15, 30, 30, 190.00, 5700.00, 0, 'Pending', NULL),
(28, 15, 11, 6, 40, 53, 13, 40, 40, 40, 38.00, 1520.00, 0, 'Pending', NULL),
(29, 16, 12, 2, 16, 32, 16, 16, 16, 16, 68.00, 1088.00, 0, 'Pending', NULL),
(30, 16, 1, 2, 77, 84, 7, 40, 40, 40, 55.00, 2200.00, 0, 'Pending', NULL),
(31, 17, 14, 7, 24, 32, 8, 24, 24, 24, 45.00, 1080.00, 0, 'Pending', NULL),
(32, 18, 4, 2, 5, 76, 71, 10, 80, 80, 210.00, 16800.00, 0, 'Pending', NULL),
(33, 18, 3, 2, 15, 32, 17, 15, 30, 30, 190.00, 5700.00, 0, 'Pending', NULL),
(34, 19, 9, 5, 30, 85, 55, 30, 60, 60, 26.00, 1560.00, 0, 'Pending', NULL),
(35, 19, 10, 5, 40, 53, 13, 40, 40, 40, 35.00, 1400.00, 0, 'Pending', NULL),
(36, 20, 2, 1, 38, 89, 51, 40, 80, 80, 42.00, 3360.00, 0, 'Pending', NULL),
(37, 20, 17, 1, 0, 10, 10, 12, 12, 12, 150.00, 1800.00, 0, 'Pending', NULL),
(38, 20, 1, 1, 77, 84, 7, 40, 40, 40, 55.00, 2200.00, 0, 'Pending', NULL),
(39, 21, 6, 3, 10, 47, 37, 12, 48, 48, 90.00, 4320.00, 0, 'Pending', NULL),
(40, 21, 5, 3, 5, 33, 28, 12, 36, 36, 175.00, 6300.00, 0, 'Pending', NULL),
(41, 22, 7, 4, 12, 32, 20, 12, 24, 24, 140.00, 3360.00, 0, 'Pending', NULL),
(42, 23, 12, 6, 16, 32, 16, 16, 16, 16, 68.00, 1088.00, 0, 'Pending', NULL),
(43, 23, 11, 6, 40, 53, 13, 40, 40, 40, 38.00, 1520.00, 0, 'Pending', NULL),
(44, 24, 13, 7, 40, 53, 13, 40, 40, 40, 25.00, 1000.00, 0, 'Pending', NULL),
(45, 24, 14, 7, 24, 32, 8, 24, 24, 24, 45.00, 1080.00, 0, 'Pending', NULL),
(46, 25, 4, 2, 5, 76, 71, 10, 80, 80, 210.00, 16800.00, 0, 'Pending', NULL),
(47, 25, 3, 2, 15, 32, 17, 15, 30, 30, 190.00, 5700.00, 0, 'Pending', NULL),
(48, 26, 9, 5, 30, 85, 55, 30, 60, 60, 26.00, 1560.00, 0, 'Pending', NULL),
(49, 26, 10, 5, 40, 53, 13, 40, 40, 40, 35.00, 1400.00, 0, 'Pending', NULL),
(50, 27, 2, 1, 38, 89, 51, 40, 80, 80, 42.00, 3360.00, 0, 'Pending', NULL),
(51, 27, 17, 1, 0, 10, 10, 12, 12, 12, 150.00, 1800.00, 0, 'Pending', NULL),
(52, 27, 1, 1, 77, 84, 7, 40, 40, 40, 55.00, 2200.00, 0, 'Pending', NULL),
(53, 28, 6, 3, 10, 47, 37, 12, 48, 48, 90.00, 4320.00, 0, 'Pending', NULL),
(54, 28, 5, 3, 5, 33, 28, 12, 36, 36, 175.00, 6300.00, 0, 'Pending', NULL),
(55, 29, 7, 4, 12, 32, 20, 12, 24, 24, 140.00, 3360.00, 0, 'Pending', NULL),
(56, 30, 12, 6, 16, 32, 16, 16, 16, 16, 68.00, 1088.00, 0, 'Pending', NULL),
(57, 30, 11, 6, 40, 53, 13, 40, 40, 40, 38.00, 1520.00, 0, 'Pending', NULL),
(58, 31, 13, 7, 40, 53, 13, 40, 40, 40, 25.00, 1000.00, 0, 'Pending', NULL),
(59, 31, 14, 7, 24, 32, 8, 24, 24, 24, 45.00, 1080.00, 0, 'Pending', NULL),
(60, 32, 4, 2, 5, 76, 71, 10, 80, 80, 210.00, 16800.00, 0, 'Pending', NULL),
(61, 32, 3, 2, 15, 32, 17, 15, 30, 30, 190.00, 5700.00, 0, 'Pending', NULL),
(62, 33, 9, 5, 30, 85, 55, 30, 60, 60, 26.00, 1560.00, 0, 'Pending', NULL),
(63, 33, 10, 5, 40, 53, 13, 40, 40, 40, 35.00, 1400.00, 0, 'Pending', NULL),
(64, 34, 2, 1, 38, 89, 51, 40, 80, 80, 42.00, 3360.00, 0, 'Pending', NULL),
(65, 34, 1, 1, 77, 84, 7, 40, 40, 40, 55.00, 2200.00, 0, 'Pending', NULL),
(66, 35, 6, 3, 10, 47, 37, 12, 48, 48, 90.00, 4320.00, 0, 'Pending', NULL),
(67, 35, 5, 3, 5, 33, 28, 12, 36, 36, 175.00, 6300.00, 0, 'Pending', NULL),
(68, 36, 7, 4, 12, 32, 20, 12, 24, 24, 140.00, 3360.00, 0, 'Pending', NULL),
(69, 37, 12, 6, 16, 32, 16, 16, 16, 16, 68.00, 1088.00, 0, 'Pending', NULL),
(70, 37, 11, 6, 40, 53, 13, 40, 40, 40, 38.00, 1520.00, 0, 'Pending', NULL),
(71, 38, 13, 7, 40, 53, 13, 40, 40, 40, 25.00, 1000.00, 0, 'Pending', NULL),
(72, 38, 14, 7, 24, 32, 8, 24, 24, 24, 45.00, 1080.00, 0, 'Pending', NULL),
(73, 39, 17, 2, 0, 0, 0, 12, 0, 12, 150.00, 1800.00, 12, 'Received', ''),
(74, 40, 2, 2, 38, 0, 0, 40, 0, 40, 42.00, 1680.00, 40, 'Received', ''),
(75, 40, 2, 2, 38, 0, 0, 40, 0, 40, 42.00, 1680.00, 40, 'Received', ''),
(76, 41, 17, 1, 0, 0, 0, 12, 0, 12, 150.00, 1800.00, 12, 'Received', '');

--
-- Triggers `item_to_order`
--
DELIMITER $$
CREATE TRIGGER `trg_item_to_order_after_delete` AFTER DELETE ON `item_to_order` FOR EACH ROW BEGIN
    UPDATE list_to_order
    SET Total_Amount = (
        SELECT IFNULL(SUM(Line_Total), 0)
        FROM item_to_order
        WHERE ListToOrder_ID = OLD.ListToOrder_ID
    )
    WHERE ListToOrder_ID = OLD.ListToOrder_ID;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_item_to_order_after_insert` AFTER INSERT ON `item_to_order` FOR EACH ROW BEGIN
    UPDATE list_to_order
    SET Total_Amount = (
        SELECT IFNULL(SUM(Line_Total), 0)
        FROM item_to_order
        WHERE ListToOrder_ID = NEW.ListToOrder_ID
    )
    WHERE ListToOrder_ID = NEW.ListToOrder_ID;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_item_to_order_after_update` AFTER UPDATE ON `item_to_order` FOR EACH ROW BEGIN
    UPDATE list_to_order
    SET Total_Amount = (
        SELECT IFNULL(SUM(Line_Total), 0)
        FROM item_to_order
        WHERE ListToOrder_ID = NEW.ListToOrder_ID
    )
    WHERE ListToOrder_ID = NEW.ListToOrder_ID;

    IF OLD.ListToOrder_ID <> NEW.ListToOrder_ID THEN

        UPDATE list_to_order
        SET Total_Amount = (
            SELECT IFNULL(SUM(Line_Total), 0)
            FROM item_to_order
            WHERE ListToOrder_ID = OLD.ListToOrder_ID
        )
        WHERE ListToOrder_ID = OLD.ListToOrder_ID;

    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_item_to_order_before_insert` BEFORE INSERT ON `item_to_order` FOR EACH ROW BEGIN
    SET NEW.Net_Demand =
        GREATEST(
            IFNULL(NEW.Predicted_Demand, 0)
            - IFNULL(NEW.Current_Stock, 0),
            0
        );

    SET NEW.Line_Total =
        IFNULL(NEW.Ordered_Quantity, 0)
        * IFNULL(NEW.Unit_Cost, 0);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_item_to_order_before_update` BEFORE UPDATE ON `item_to_order` FOR EACH ROW BEGIN
    SET NEW.Net_Demand =
        GREATEST(
            IFNULL(NEW.Predicted_Demand, 0)
            - IFNULL(NEW.Current_Stock, 0),
            0
        );

    SET NEW.Line_Total =
        IFNULL(NEW.Ordered_Quantity, 0)
        * IFNULL(NEW.Unit_Cost, 0);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_list_order_status` AFTER UPDATE ON `item_to_order` FOR EACH ROW BEGIN

    DECLARE v_TotalItems INT DEFAULT 0;
    DECLARE v_ReceivedItems INT DEFAULT 0;
    DECLARE v_PartialItems INT DEFAULT 0;
    DECLARE v_OrderedItems INT DEFAULT 0;
    DECLARE v_PendingItems INT DEFAULT 0;
    DECLARE v_CancelledItems INT DEFAULT 0;


    /*
     * Total items inside this list.
     */
    SELECT COUNT(*)
    INTO v_TotalItems
    FROM item_to_order
    WHERE ListToOrder_ID = NEW.ListToOrder_ID;


    /*
     * Completely received items.
     */
    SELECT COUNT(*)
    INTO v_ReceivedItems
    FROM item_to_order
    WHERE ListToOrder_ID = NEW.ListToOrder_ID
      AND Item_Status = 'Received';


    /*
     * Partially received items.
     */
    SELECT COUNT(*)
    INTO v_PartialItems
    FROM item_to_order
    WHERE ListToOrder_ID = NEW.ListToOrder_ID
      AND Item_Status = 'Partially-Received';


    /*
     * Ordered items.
     */
    SELECT COUNT(*)
    INTO v_OrderedItems
    FROM item_to_order
    WHERE ListToOrder_ID = NEW.ListToOrder_ID
      AND Item_Status = 'Ordered';


    /*
     * Pending items.
     */
    SELECT COUNT(*)
    INTO v_PendingItems
    FROM item_to_order
    WHERE ListToOrder_ID = NEW.ListToOrder_ID
      AND Item_Status = 'Pending';


    /*
     * Cancelled items.
     */
    SELECT COUNT(*)
    INTO v_CancelledItems
    FROM item_to_order
    WHERE ListToOrder_ID = NEW.ListToOrder_ID
      AND Item_Status = 'Cancelled';


    /*
     * =========================================================
     * DETERMINE OVERALL LIST STATUS
     * =========================================================
     */

    /*
     * Every item is completely received.
     */
    IF v_TotalItems > 0
       AND v_ReceivedItems = v_TotalItems THEN

        UPDATE list_to_order
        SET Order_Status = 'Received'
        WHERE ListToOrder_ID = NEW.ListToOrder_ID;


    /*
     * At least one item has been partially/completely received.
     */
    ELSEIF v_PartialItems > 0
        OR v_ReceivedItems > 0 THEN

        UPDATE list_to_order
        SET Order_Status = 'Partially-Received'
        WHERE ListToOrder_ID = NEW.ListToOrder_ID;


    /*
     * At least one item is still ordered.
     */
    ELSEIF v_OrderedItems > 0 THEN

        UPDATE list_to_order
        SET Order_Status = 'Ordered'
        WHERE ListToOrder_ID = NEW.ListToOrder_ID;


    /*
     * Every item is pending.
     */
    ELSEIF v_PendingItems = v_TotalItems THEN

        UPDATE list_to_order
        SET Order_Status = 'Confirmed'
        WHERE ListToOrder_ID = NEW.ListToOrder_ID;


    /*
     * Every item is cancelled.
     */
    ELSEIF v_CancelledItems = v_TotalItems THEN

        UPDATE list_to_order
        SET Order_Status = 'Cancelled'
        WHERE ListToOrder_ID = NEW.ListToOrder_ID;

    END IF;

END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `list_to_order`
--

CREATE TABLE `list_to_order` (
  `ListToOrder_ID` int NOT NULL,
  `Supplier_ID` int NOT NULL,
  `Order_Date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Expected_Receive_Date` datetime DEFAULT NULL,
  `Forecast_Start_Date` date DEFAULT NULL,
  `Forecast_End_Date` date DEFAULT NULL,
  `Order_Status` enum('Pending','Confirmed','Ordered','Partially-Received','Received','Cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'Pending',
  `Total_Amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `Created_By` varchar(100) DEFAULT NULL,
  `Notes` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `list_to_order`
--

INSERT INTO `list_to_order` (`ListToOrder_ID`, `Supplier_ID`, `Order_Date`, `Expected_Receive_Date`, `Forecast_Start_Date`, `Forecast_End_Date`, `Order_Status`, `Total_Amount`, `Created_By`, `Notes`) VALUES
(1, 1, '2026-09-24 14:33:06', '2026-09-29 00:00:00', NULL, NULL, 'Received', 3880.00, 'gomezcarlo222@gmail.com', ''),
(2, 2, '2026-09-24 14:39:04', '2026-09-29 00:00:00', NULL, NULL, 'Received', 4950.00, 'gomezcarlo222@gmail.com', ''),
(3, 3, '2026-09-24 14:39:36', '2026-09-29 00:00:00', NULL, NULL, 'Received', 3180.00, 'gomezcarlo222@gmail.com', ''),
(4, 4, '2026-09-24 14:40:04', '2026-09-29 00:00:00', NULL, NULL, 'Received', 3360.00, 'gomezcarlo222@gmail.com', ''),
(5, 5, '2026-09-24 14:41:12', '2026-09-29 00:00:00', NULL, NULL, 'Received', 2180.00, 'gomezcarlo222@gmail.com', ''),
(6, 6, '2026-09-24 14:41:47', '2026-09-29 00:00:00', NULL, NULL, 'Received', 2608.00, 'gomezcarlo222@gmail.com', ''),
(7, 7, '2026-09-24 14:42:21', '2026-09-29 00:00:00', NULL, NULL, 'Received', 2080.00, 'gomezcarlo222@gmail.com', ''),
(8, 8, '2026-09-24 14:43:16', '2026-09-29 00:00:00', NULL, NULL, 'Received', 22320.00, 'gomezcarlo222@gmail.com', ''),
(9, 1, '2026-09-24 15:22:01', '2026-09-22 00:00:00', NULL, NULL, 'Received', 2200.00, 'gomezcarlo222@gmail.com', ''),
(10, 8, '2026-10-08 01:45:20', NULL, '2026-10-07', '2026-10-21', 'Confirmed', 16800.00, 'AI Recommendation', 'Created from AI demand recommendation.'),
(11, 5, '2026-10-08 01:45:20', NULL, '2026-10-07', '2026-10-21', 'Confirmed', 2960.00, 'AI Recommendation', 'Created from AI demand recommendation.'),
(12, 1, '2026-10-08 01:45:20', NULL, '2026-10-07', '2026-10-21', 'Confirmed', 3360.00, 'AI Recommendation', 'Created from AI demand recommendation.'),
(13, 3, '2026-10-08 01:45:20', NULL, '2026-10-07', '2026-10-21', 'Confirmed', 13420.00, 'AI Recommendation', 'Created from AI demand recommendation.'),
(14, 4, '2026-10-08 01:45:20', NULL, '2026-10-07', '2026-10-21', 'Confirmed', 3360.00, 'AI Recommendation', 'Created from AI demand recommendation.'),
(15, 6, '2026-10-08 01:45:20', NULL, '2026-10-07', '2026-10-21', 'Confirmed', 7220.00, 'AI Recommendation', 'Created from AI demand recommendation.'),
(16, 2, '2026-10-08 01:45:20', NULL, '2026-10-07', '2026-10-21', 'Confirmed', 3288.00, 'AI Recommendation', 'Created from AI demand recommendation.'),
(17, 7, '2026-10-08 01:45:20', NULL, '2026-10-07', '2026-10-21', 'Confirmed', 1080.00, 'AI Recommendation', 'Created from AI demand recommendation.'),
(18, 2, '2026-10-08 02:01:46', NULL, '2026-10-07', '2026-10-21', 'Confirmed', 22500.00, 'AI Recommendation', 'Created from AI demand recommendation.'),
(19, 5, '2026-10-08 02:01:46', NULL, '2026-10-07', '2026-10-21', 'Confirmed', 2960.00, 'AI Recommendation', 'Created from AI demand recommendation.'),
(20, 1, '2026-10-08 02:01:46', NULL, '2026-10-07', '2026-10-21', 'Confirmed', 7360.00, 'AI Recommendation', 'Created from AI demand recommendation.'),
(21, 3, '2026-10-08 02:01:46', NULL, '2026-10-07', '2026-10-21', 'Confirmed', 10620.00, 'AI Recommendation', 'Created from AI demand recommendation.'),
(22, 4, '2026-10-08 02:01:46', NULL, '2026-10-07', '2026-10-21', 'Confirmed', 3360.00, 'AI Recommendation', 'Created from AI demand recommendation.'),
(23, 6, '2026-10-08 02:01:46', NULL, '2026-10-07', '2026-10-21', 'Confirmed', 2608.00, 'AI Recommendation', 'Created from AI demand recommendation.'),
(24, 7, '2026-10-08 02:01:46', NULL, '2026-10-07', '2026-10-21', 'Confirmed', 2080.00, 'AI Recommendation', 'Created from AI demand recommendation.'),
(25, 2, '2026-10-08 02:02:14', NULL, '2026-10-07', '2026-10-21', 'Confirmed', 22500.00, 'AI Recommendation', 'Created from AI demand recommendation.'),
(26, 5, '2026-10-08 02:02:14', NULL, '2026-10-07', '2026-10-21', 'Confirmed', 2960.00, 'AI Recommendation', 'Created from AI demand recommendation.'),
(27, 1, '2026-10-08 02:02:14', NULL, '2026-10-07', '2026-10-21', 'Confirmed', 7360.00, 'AI Recommendation', 'Created from AI demand recommendation.'),
(28, 3, '2026-10-08 02:02:14', NULL, '2026-10-07', '2026-10-21', 'Confirmed', 10620.00, 'AI Recommendation', 'Created from AI demand recommendation.'),
(29, 4, '2026-10-08 02:02:14', NULL, '2026-10-07', '2026-10-21', 'Confirmed', 3360.00, 'AI Recommendation', 'Created from AI demand recommendation.'),
(30, 6, '2026-10-08 02:02:14', NULL, '2026-10-07', '2026-10-21', 'Confirmed', 2608.00, 'AI Recommendation', 'Created from AI demand recommendation.'),
(31, 7, '2026-10-08 02:02:14', NULL, '2026-10-07', '2026-10-21', 'Confirmed', 2080.00, 'AI Recommendation', 'Created from AI demand recommendation.'),
(32, 2, '2026-10-08 11:32:55', NULL, '2026-10-08', '2026-10-22', 'Confirmed', 22500.00, 'AI Recommendation', 'Created from AI demand recommendation.'),
(33, 5, '2026-10-08 11:32:55', NULL, '2026-10-08', '2026-10-22', 'Confirmed', 2960.00, 'AI Recommendation', 'Created from AI demand recommendation.'),
(34, 1, '2026-10-08 11:32:55', NULL, '2026-10-08', '2026-10-22', 'Confirmed', 5560.00, 'AI Recommendation', 'Created from AI demand recommendation.'),
(35, 3, '2026-10-08 11:32:55', NULL, '2026-10-08', '2026-10-22', 'Confirmed', 10620.00, 'AI Recommendation', 'Created from AI demand recommendation.'),
(36, 4, '2026-10-08 11:32:55', NULL, '2026-10-08', '2026-10-22', 'Confirmed', 3360.00, 'AI Recommendation', 'Created from AI demand recommendation.'),
(37, 6, '2026-10-08 11:32:55', NULL, '2026-10-08', '2026-10-22', 'Confirmed', 2608.00, 'AI Recommendation', 'Created from AI demand recommendation.'),
(38, 7, '2026-10-08 11:32:55', NULL, '2026-10-08', '2026-10-22', 'Confirmed', 2080.00, 'AI Recommendation', 'Created from AI demand recommendation.'),
(39, 2, '2026-10-08 12:24:32', '2026-10-30 00:00:00', NULL, NULL, 'Received', 1800.00, 'gomezcarlo222@gmail.com', ''),
(40, 2, '2026-10-08 12:37:10', '2026-10-31 00:00:00', NULL, NULL, 'Received', 3360.00, 'gomezcarlo222@gmail.com', ''),
(41, 1, '2026-10-08 12:58:23', '2026-10-31 00:00:00', NULL, NULL, 'Received', 1800.00, 'gomezcarlo222@gmail.com', '');

-- --------------------------------------------------------

--
-- Table structure for table `monthly_sales`
--

CREATE TABLE `monthly_sales` (
  `MonthlySales_ID` int NOT NULL,
  `PeriodStart` date DEFAULT NULL,
  `PeriodEnd` date DEFAULT NULL,
  `Product_ID` int DEFAULT NULL,
  `TotalSales` decimal(10,2) DEFAULT NULL,
  `TotalQuantity` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `monthly_total_sales`
--

CREATE TABLE `monthly_total_sales` (
  `EntireMonthlySales_ID` int NOT NULL,
  `PeriodStart` date NOT NULL,
  `PeriodEnd` date NOT NULL,
  `TotalSales` decimal(12,2) NOT NULL DEFAULT '0.00',
  `TotalQuantity` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `newaddition`
--

CREATE TABLE `newaddition` (
  `Inventory_ID` int NOT NULL,
  `Product_ID` int NOT NULL,
  `Quantity` int NOT NULL,
  `Date_Added` timestamp NOT NULL,
  `Expiration_Date` date DEFAULT NULL,
  `Status` enum('New','Returned') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `Supplier_ID` int NOT NULL,
  `LocationS` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `LocationR` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Triggers `newaddition`
--
DELIMITER $$
CREATE TRIGGER `after_newaddition_insert` AFTER INSERT ON `newaddition` FOR EACH ROW BEGIN
    DECLARE v_Price DECIMAL(10,2);
    DECLARE v_Supplier_ID VARCHAR(50);
    DECLARE v_ExpirationDate DATE;
    DECLARE v_Barcode VARCHAR(100);
    DECLARE v_NewInventory INT;
    DECLARE v_Status VARCHAR(20);

    -- Get values from product table
    SELECT StorePrice, Supplier_ID, ExpirationDate, Barcode
    INTO v_Price, v_Supplier_ID, v_ExpirationDate, v_Barcode
    FROM product
    WHERE Product_ID = NEW.Product_ID;

    -- Check if product already exists in inventory at the same location
    IF EXISTS (
        SELECT 1 FROM inventory 
        WHERE Product_ID = NEW.Product_ID
          AND LocationS = NEW.LocationS
          AND LocationR = NEW.LocationR
    ) THEN
        -- Get current inventory
        SELECT Inventory INTO v_NewInventory
        FROM inventory
        WHERE Product_ID = NEW.Product_ID
          AND LocationS = NEW.LocationS
          AND LocationR = NEW.LocationR;

        -- Add the incoming quantity
        SET v_NewInventory = v_NewInventory + NEW.Quantity;

        -- Set the new status
        SET v_Status = CASE 
            WHEN v_NewInventory > 10 THEN 'IN-STOCK'
            WHEN v_NewInventory > 0 THEN 'LOW-STOCK'
            ELSE 'OUT-OF-STOCK'
        END;

        -- ✅ Update existing inventory EXCLUDING Supplier_ID
        UPDATE inventory
        SET 
            Inventory = v_NewInventory,
            UnitIN = NEW.Date_Added,
            Status = v_Status,
            ExpirationDate = v_ExpirationDate,
            Price = v_Price,
            Barcode = v_Barcode
        WHERE Product_ID = NEW.Product_ID
          AND LocationS = NEW.LocationS
          AND LocationR = NEW.LocationR;

    ELSE
        -- New inventory entry
        SET v_NewInventory = NEW.Quantity;

        -- Set the new status
        SET v_Status = CASE 
            WHEN v_NewInventory > 10 THEN 'IN-STOCK'
            WHEN v_NewInventory > 0 THEN 'LOW-STOCK'
            ELSE 'OUT-OF-STOCK'
        END;

        -- ✅ Insert all values (including Supplier_ID for new records)
        INSERT INTO inventory (
            Product_ID,
            LocationS,
            LocationR,
            Price,
            Inventory,
            UnitIN,
            UnitOut,
            Status,
            Supplier_ID,
            ExpirationDate,
            Barcode
        ) VALUES (
            NEW.Product_ID,
            NEW.LocationS,
            NEW.LocationR,
            v_Price,
            v_NewInventory,
            NEW.Date_Added,
            NULL,
            v_Status,
            v_Supplier_ID,
            v_ExpirationDate,
            v_Barcode
        );
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `pricing_audit_log`
--

CREATE TABLE `pricing_audit_log` (
  `Audit_ID` bigint UNSIGNED NOT NULL,
  `User_ID` int DEFAULT NULL,
  `UserName` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ActionType` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Product_ID` int DEFAULT NULL,
  `Supplier_ID` int DEFAULT NULL,
  `ProductSupplier_ID` int DEFAULT NULL,
  `PreviousValue` text COLLATE utf8mb4_unicode_ci,
  `NewValue` text COLLATE utf8mb4_unicode_ci,
  `Details` text COLLATE utf8mb4_unicode_ci,
  `CreatedAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pricing_audit_log`
--

INSERT INTO `pricing_audit_log` (`Audit_ID`, `User_ID`, `UserName`, `ActionType`, `Product_ID`, `Supplier_ID`, `ProductSupplier_ID`, `PreviousValue`, `NewValue`, `Details`, `CreatedAt`) VALUES
(1, 18, 'JAY', 'add_supplier_offer', 17, 3, 36, NULL, '152.00', 'Added supplier offer with initial purchase price. Offer activated.', '2026-10-21 12:42:56'),
(2, 18, 'JAY', 'update_supplier_price', 17, 1, 17, '150.00', '165.00', 'Updated supplier purchase price. Product StorePrice and POS selling price were not changed.', '2026-10-21 12:43:34'),
(3, 18, 'JAY', 'deactivate_supplier_offer', 17, 1, 17, '1', '0', 'Deactivated supplier offer.', '2026-10-21 12:43:41'),
(4, 18, 'JAY', 'activate_supplier_offer', 17, 1, 17, '0', '1', 'Activated supplier offer.', '2026-10-21 12:43:43'),
(5, 18, 'JAY', 'apply_suggested_price', 17, 5, 32, '214.65', '206.55', 'Applied suggested selling price. Product: Toblerone Ice Cream. Supplier: michael. Supplier purchase cost: 153.00. Effective markup: 35.00%. Markup source: product.', '2026-10-21 13:02:06'),
(6, 18, 'JAY', 'apply_suggested_price', 17, 1, 17, '206.55', '222.75', 'Applied suggested selling price. Product: Toblerone Ice Cream. Supplier: JIN MART KOREAN GROCERY - PASAY 진마트 파사이. Supplier purchase cost: 165.00. Effective markup: 35.00%. Markup source: product.', '2026-10-21 13:02:50'),
(7, 18, 'JAY', 'apply_suggested_price', 8, 4, 8, '190.00', '168.00', 'Applied suggested selling price. Product: Spam Less Sodium Luncheon Meat 340g. Supplier: john. Supplier purchase cost: 140.00. Effective markup: 20.00%. Markup source: general.', '2026-10-21 13:04:11'),
(8, 18, 'JAY', 'save_general_markup', NULL, NULL, NULL, '20.00', '25.00', 'Saved general markup rule.', '2026-10-21 13:15:14'),
(9, 18, 'JAY', 'save_category_markup', NULL, NULL, NULL, '30.00', '30.00', 'Saved category markup for: Noodles and Instant Meals', '2026-10-21 13:15:20'),
(10, 18, 'JAY', 'save_category_markup', NULL, NULL, NULL, '30.00', '30.00', 'Saved category markup for: Noodles and Instant Meals', '2026-10-21 13:15:30'),
(11, 18, 'JAY', 'save_product_markup', 17, NULL, NULL, '35.00', '40.00', 'Saved product-specific markup for Product ID 17: Toblerone Ice Cream', '2026-10-21 13:15:43'),
(12, 18, 'JAY', 'save_category_markup', NULL, NULL, NULL, '30.00', '35.00', 'Saved category markup for: Noodles and Instant Meals', '2026-10-21 13:16:11'),
(13, 18, 'JAY', 'deactivate_supplier_offer', 16, 8, 16, '1', '0', 'Deactivated supplier offer.', '2026-10-22 05:08:56'),
(14, 18, 'JAY', 'activate_supplier_offer', 16, 8, 16, '0', '1', 'Activated supplier offer.', '2026-10-22 05:08:59'),
(15, 18, 'JAY', 'add_supplier_offer', 5, 1, 37, NULL, '180.00', 'Added supplier offer with initial purchase price. Offer activated.', '2026-10-22 05:38:54'),
(16, 18, 'JAY', 'add_supplier_offer', 4, 4, 38, NULL, '220.00', 'Added supplier offer with initial purchase price. Offer activated.', '2026-10-22 05:40:43'),
(17, 18, 'JAY', 'add_supplier_offer', 4, 6, 39, NULL, '215.00', 'Added supplier offer with initial purchase price. Offer activated.', '2026-10-22 05:40:54'),
(18, 18, 'JAY', 'add_supplier_offer', 6, 7, 40, NULL, '95.00', 'Added supplier offer with initial purchase price. Offer activated.', '2026-10-22 05:43:32'),
(19, 18, 'JAY', 'deactivate_supplier_offer', 16, 8, 16, '1', '0', 'Deactivated supplier offer.', '2026-10-22 08:22:50'),
(20, 18, 'JAY', 'update_supplier_price', 17, 1, 17, '165.00', '145.00', 'Updated supplier purchase price. Product StorePrice and POS selling price were not changed.', '2026-10-22 09:21:28'),
(21, 18, 'JAY', 'apply_suggested_price', 17, 1, 17, '222.75', '203.00', 'Applied suggested selling price. Product: Toblerone Ice Cream. Supplier: JIN MART KOREAN GROCERY - PASAY 진마트 파사이. Supplier purchase cost: 145.00. Effective markup: 40.00%. Markup source: product.', '2026-10-22 09:21:39'),
(22, 18, 'JAY', 'add_supplier_offer', 27, 1, 41, NULL, '110.00', 'Added supplier offer with initial purchase price. Offer activated.', '2026-10-23 08:51:36'),
(23, 18, 'JAY', 'add_supplier_offer', 27, 4, 42, NULL, '100.00', 'Added supplier offer with initial purchase price. Offer activated.', '2026-10-23 08:56:58'),
(24, 18, 'JAY', 'deactivate_supplier_offer', 27, 1, 41, '1', '0', 'Deactivated supplier offer.', '2026-10-23 08:57:20'),
(25, 18, 'JAY', 'apply_suggested_price', 27, 4, 42, '1.00', '130.00', 'Applied suggested selling price. Product: Magnun. Supplier: john. Supplier purchase cost: 100.00. Effective markup: 30.00%. Markup source: category.', '2026-10-23 09:18:14'),
(26, 18, 'JAY', 'add_supplier_offer', 16, 1, 43, NULL, '350.00', 'Added supplier offer with initial purchase price. Offer activated.', '2026-10-24 11:00:00'),
(27, 18, 'JAY', 'activate_supplier_offer', 16, 8, 16, '0', '1', 'Activated supplier offer.', '2026-10-24 11:15:15'),
(28, 18, 'JAY', 'add_supplier_offer', 28, 9, 44, NULL, '18.00', 'Added supplier offer with initial purchase price. Offer activated.', '2026-10-24 11:58:30'),
(29, 18, 'JAY', 'apply_suggested_price', 28, 9, 44, '0.00', '23.40', 'Applied suggested selling price. Product: Cornetto Classic. Supplier: IMSIN KOREAN STORE. Supplier purchase cost: 18.00. Effective markup: 30.00%. Markup source: category.', '2026-10-24 11:59:05');

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `Product_ID` int NOT NULL,
  `ProductName` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `Type` enum('Noodles and Instant Meals','Frozen Hotpot Goods','Side Dishes','Canned Goods','Beverages','Sweets and Snacks','Ice Cream','Beauty & Personal Care') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `ReordingPoints` int DEFAULT NULL,
  `UnitsOrdered` int DEFAULT NULL,
  `UnitSold` int DEFAULT NULL,
  `StorePrice` decimal(10,2) NOT NULL,
  `Image` blob,
  `Pack_Size` int NOT NULL DEFAULT '1',
  `Barcode` varchar(100) NOT NULL,
  `LocationS` text NOT NULL,
  `LocationR` text NOT NULL,
  `CurrentStock` int GENERATED ALWAYS AS ((`UnitsOrdered` - `UnitSold`)) VIRTUAL,
  `IsArchived` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`Product_ID`, `ProductName`, `Type`, `ReordingPoints`, `UnitsOrdered`, `UnitSold`, `StorePrice`, `Image`, `Pack_Size`, `Barcode`, `LocationS`, `LocationR`, `IsArchived`) VALUES
(1, 'Samyang Buldak Carbonara Ramen 130g', 'Noodles and Instant Meals', NULL, 142, 3, 77.00, 0x366162346261396562336138315f73616d79616e672e6a666966, 40, '6976693307846', 'Shelf A', 'Row A', 0),
(2, 'Nongshim Shin Ramyun Spicy 120g', 'Noodles and Instant Meals', NULL, 112, 40, 60.00, 0x366162346262396135663739645f6e6f6e677368696d2e6a666966, 40, '6936520871469', 'Shelf A', 'Row B', 0),
(3, 'Premium Sliced Beef Samgyupsal 500g', 'Frozen Hotpot Goods', NULL, 45, 4, 280.00, 0x366162346263303331633137315f426565662d53616d6779757073616c2d353030672d312e706e67, 15, '2000224312185', 'Shelf A', 'Row B', 0),
(4, 'Pork & Cabbage Mandu Dumplings 1kg', 'Frozen Hotpot Goods', NULL, 90, 9, 315.00, 0x366162346263363830313965645f696d61676573202833292e6a666966, 10, '2000219213022', 'Shelf A', 'Row B', 0),
(5, 'Jongga Mat Kimchi 500g', 'Side Dishes', NULL, 24, 11, 210.00, 0x366162346264343564363638385f696d61676573202834292e6a666966, 12, '2000213074353', 'Shelf A', 'Row C', 0),
(6, 'Daesang Season Pickled Radish 400g', 'Side Dishes', NULL, 24, 3, 108.00, 0x366162346265303436363034645f696d61676573202835292e6a666966, 12, '2000218723423', 'Shelf A', 'Row C', 0),
(7, 'Spam Classic Luncheon Meat 340g', 'Canned Goods', NULL, 48, 2, 190.00, 0x366162346266363632623837395f36313130393030623961396164353338393632353839626338383335333263362e6a7067, 12, '2000225506484', 'Shelf A', 'Row D', 0),
(8, 'Spam Less Sodium Luncheon Meat 340g', 'Canned Goods', NULL, 24, 4, 168.00, 0x366162346266653264626131665f3831673839766d304e614c2e5f41435f5546313030302c313030305f514c38305f2e6a7067, 12, '1111111111111', 'Shelf A', 'Row D', 0),
(9, 'Lotte Milkis Carbonated Milk Drink 250ml', 'Beverages', NULL, 690, 2, 40.00, 0x366162346330346561623437395f70682d31313133343230372d38317a74632d6d6c6a6b77646d766469777766362e6a666966, 30, '2000212162167', 'Shelf B', 'Row A', 0),
(10, 'Cantata Sweet Americano Liquid Pouch Drink 230ml', 'Beverages', NULL, 70, 0, 55.00, 0x366162346330393635343236325f363156412d42475245674c2e6a7067, 40, '2222222222222', 'Shelf A', 'Row A', 0),
(11, 'Lotte Pepero Chocolate Covered Biscuit Sticks 47g', 'Sweets and Snacks', NULL, 120, 0, 55.00, 0x366162346330663532326266665f39613830613762353134343566363965653534343136323966333730643632332d66363062396336613334343837382d70657065726f2d63686f636f6c6174652d3437672e6a7067, 40, '2000225506484', 'Shelf B', 'Row B', 0),
(12, 'Orion Turtle Chips Choco Churros Flavor 136g', 'Sweets and Snacks', NULL, 32, 0, 99.00, 0x366162346331326461663164375f36317447494978474a654c2e6a7067, 16, '3333333333333', 'Shelf B', 'Row B', 0),
(13, 'Binggrae Melona Melon Ice Cream Bar 80ml', 'Ice Cream', NULL, 330, 2, 45.00, 0x366162346332383265383230615f34626334316232393837346363633130656238663539396439353833396666342e6a7067, 40, '6932808788887', 'Shelf B', 'Row C', 0),
(14, 'Binggrae Samanco Red Bean Waffle Ice Cream 150ml', 'Ice Cream', NULL, 120, 2, 75.00, 0x366162346332636365356333325f34323139633762336264643062316563396565653061343563323163353032622e6a70675f373230783732307138302e6a7067, 24, '4444444444444', 'Shelf B', 'Row C', 0),
(15, 'COSRX Low pH Good Morning Gel Cleanser 150ml', 'Beauty & Personal Care', NULL, 48, 2, 420.00, 0x366162346333356238333835625f696d61676573202836292e6a666966, 24, '6976693307846', 'Shelf B', 'Row D', 0),
(16, 'Beauty of Joseon Relief Sun: Rice + Probiotics SPF50+ 50ml', 'Beauty & Personal Care', NULL, 400, 0, 580.00, 0x366162346333643836643761365f696d61676573202837292e6a666966, 40, '5555555555555', 'Shelf B', 'Row D', 0),
(17, 'Toblerone Ice Cream', 'Ice Cream', NULL, 198, 14, 203.00, 0x366163363162666165396133335f746f626c65726f6e652d6963652d637265616d312d3634302d312e6a7067, 12, '7777777777777', 'Shelf A', 'Row A', 0),
(27, 'Magnun', 'Ice Cream', NULL, 12, 10, 130.00, 0x366164623166373565353532615f696d616765732e6a666966, 12, '1212121212121', 'Shelf A', 'Row A', 1),
(28, 'Cornetto Classic', 'Ice Cream', NULL, 12, 4, 23.40, 0x366164633963333730623463365f636f726e6574746f2e706e67, 12, '1313131313131', 'Shelf A', 'Row A', 0);

--
-- Triggers `product`
--
DELIMITER $$
CREATE TRIGGER `delete_product` BEFORE DELETE ON `product` FOR EACH ROW BEGIN
    DELETE FROM inventory
    WHERE Product_ID = OLD.Product_ID;
    
    DELETE FROM expiration
    WHERE Product_ID = OLD.Product_ID;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `new_product_to_invetory` AFTER INSERT ON `product` FOR EACH ROW BEGIN
    DECLARE v_Price DECIMAL(10,2);
    DECLARE v_Barcode VARCHAR(100);
    DECLARE v_NewInventory INT;
    DECLARE v_Status VARCHAR(20);
    DECLARE v_NewRestock DATE;

    -- Get product details (supplier fields removed)
    SELECT StorePrice, Barcode
    INTO v_Price, v_Barcode
    FROM product
    WHERE Product_ID = NEW.Product_ID;

    -- Compute new inventory
    SET v_NewInventory = NEW.UnitsOrdered - NEW.UnitSold;

    -- Determine stock status
    SET v_Status = CASE
        WHEN v_NewInventory > 10 THEN 'IN-STOCK'
        WHEN v_NewInventory > 0 THEN 'LOW-STOCK'
        ELSE 'OUT-OF-STOCK'
    END;

    -- Get most recent restock date
    SET v_NewRestock = (
        SELECT Date_Received
        FROM restock
        WHERE Product_ID = NEW.Product_ID
        ORDER BY Date_Received DESC
        LIMIT 1
    );

    -- Check if inventory already exists at this location
    IF EXISTS (
        SELECT 1
        FROM inventory
        WHERE Product_ID = NEW.Product_ID
          AND LocationS = NEW.LocationS
          AND LocationR = NEW.LocationR
    ) THEN

        UPDATE inventory
        SET
            Inventory = v_NewInventory,
            UnitIN = v_NewRestock,
            Status = v_Status,
            Price = v_Price,
            Barcode = v_Barcode
        WHERE Product_ID = NEW.Product_ID
          AND LocationS = NEW.LocationS
          AND LocationR = NEW.LocationR;

    ELSE

        INSERT INTO inventory (
            Product_ID,
            LocationS,
            LocationR,
            Price,
            Inventory,
            UnitIN,
            UnitOut,
            Status,
            Barcode
        )
        VALUES (
            NEW.Product_ID,
            NEW.LocationS,
            NEW.LocationR,
            v_Price,
            v_NewInventory,
            v_NewRestock,
            NULL,
            v_Status,
            v_Barcode
        );

    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `product_markup`
--

CREATE TABLE `product_markup` (
  `ProductMarkup_ID` int NOT NULL,
  `Product_ID` int NOT NULL,
  `MarkupPercent` decimal(7,2) NOT NULL,
  `UpdatedAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ;

--
-- Dumping data for table `product_markup`
--

INSERT INTO `product_markup` (`ProductMarkup_ID`, `Product_ID`, `MarkupPercent`, `UpdatedAt`) VALUES
(1, 1, 40.00, '2026-10-21 06:14:13'),
(2, 17, 40.00, '2026-10-21 13:15:43');

-- --------------------------------------------------------

--
-- Table structure for table `product_supplier`
--

CREATE TABLE `product_supplier` (
  `ProductSupplier_ID` int NOT NULL,
  `Product_ID` int NOT NULL,
  `Supplier_ID` int NOT NULL,
  `SupplierPrice` decimal(10,2) NOT NULL,
  `IsActive` tinyint(1) NOT NULL DEFAULT '1',
  `CreatedAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `UpdatedAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `product_supplier`
--

INSERT INTO `product_supplier` (`ProductSupplier_ID`, `Product_ID`, `Supplier_ID`, `SupplierPrice`, `IsActive`, `CreatedAt`, `UpdatedAt`) VALUES
(1, 1, 1, 55.00, 1, '2026-10-21 04:26:34', '2026-10-21 04:26:34'),
(2, 2, 1, 42.00, 1, '2026-10-21 04:26:34', '2026-10-21 04:26:34'),
(3, 3, 2, 190.00, 1, '2026-10-21 04:26:34', '2026-10-21 04:26:34'),
(4, 4, 2, 210.00, 1, '2026-10-21 04:26:34', '2026-10-21 04:26:34'),
(5, 5, 3, 175.00, 1, '2026-10-21 04:26:34', '2026-10-21 04:26:34'),
(6, 6, 3, 90.00, 1, '2026-10-21 04:26:34', '2026-10-21 04:26:34'),
(7, 7, 4, 140.00, 1, '2026-10-21 04:26:34', '2026-10-21 04:26:34'),
(8, 8, 4, 140.00, 1, '2026-10-21 04:26:34', '2026-10-21 04:26:34'),
(9, 9, 5, 26.00, 1, '2026-10-21 04:26:34', '2026-10-21 04:26:34'),
(10, 10, 5, 35.00, 1, '2026-10-21 04:26:34', '2026-10-21 04:26:34'),
(11, 11, 6, 38.00, 1, '2026-10-21 04:26:34', '2026-10-21 04:26:34'),
(12, 12, 6, 68.00, 1, '2026-10-21 04:26:34', '2026-10-21 04:26:34'),
(13, 13, 7, 25.00, 1, '2026-10-21 04:26:34', '2026-10-21 04:26:34'),
(14, 14, 7, 45.00, 1, '2026-10-21 04:26:34', '2026-10-21 04:26:34'),
(15, 15, 8, 280.00, 1, '2026-10-21 04:26:34', '2026-10-21 04:26:34'),
(16, 16, 8, 390.00, 1, '2026-10-21 04:26:34', '2026-10-24 11:15:15'),
(17, 17, 1, 145.00, 1, '2026-10-21 04:26:34', '2026-10-22 09:21:28'),
(32, 17, 5, 153.00, 1, '2026-10-21 11:19:23', '2026-10-21 11:57:19'),
(33, 17, 4, 152.00, 1, '2026-10-21 11:35:06', '2026-10-21 11:57:24'),
(34, 17, 8, 159.00, 1, '2026-10-21 11:47:39', '2026-10-21 11:47:39'),
(36, 17, 3, 152.00, 1, '2026-10-21 12:42:56', '2026-10-21 12:42:56'),
(37, 5, 1, 180.00, 1, '2026-10-22 05:38:54', '2026-10-22 05:38:54'),
(38, 4, 4, 220.00, 1, '2026-10-22 05:40:43', '2026-10-22 05:40:43'),
(39, 4, 6, 215.00, 1, '2026-10-22 05:40:54', '2026-10-22 05:40:54'),
(40, 6, 7, 95.00, 1, '2026-10-22 05:43:32', '2026-10-22 05:43:32'),
(41, 27, 1, 110.00, 0, '2026-10-23 08:51:36', '2026-10-23 08:57:20'),
(42, 27, 4, 100.00, 1, '2026-10-23 08:56:58', '2026-10-23 08:56:58'),
(43, 16, 1, 350.00, 1, '2026-10-24 11:00:00', '2026-10-24 11:00:00'),
(44, 28, 9, 18.00, 1, '2026-10-24 11:58:30', '2026-10-24 11:58:30');

-- --------------------------------------------------------

--
-- Table structure for table `pulledoutitems`
--

CREATE TABLE `pulledoutitems` (
  `Pulled_ID` int NOT NULL,
  `Product_ID` int NOT NULL,
  `Supplier_ID` int NOT NULL,
  `Quantity` int NOT NULL,
  `Reason` text NOT NULL,
  `PulledDate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `restock`
--

CREATE TABLE `restock` (
  `Orestock_ID` int NOT NULL,
  `ItemToOrder_ID` int DEFAULT NULL,
  `Type` enum('New','Re-Order') NOT NULL,
  `Quantity` int NOT NULL DEFAULT '0',
  `PurchaseUnitCost` decimal(12,2) DEFAULT NULL,
  `OrderDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Expected_Receive_Date` date DEFAULT NULL,
  `Product_ID` int NOT NULL,
  `Supplier_ID` int NOT NULL,
  `Status` enum('Requested','Out for Delivery','Partially-Received','P-R-With-Issue','Received-With-Issue','Received','Backorder-Fulfillment','Cancelled') NOT NULL,
  `Image` blob,
  `DeliveryStatus` enum('','On-Time','Delayed','Early') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `Date_Received` timestamp NULL DEFAULT NULL,
  `TotalReceived` int NOT NULL DEFAULT '0',
  `withIssue` int NOT NULL DEFAULT '0',
  `ExpirationDate` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `restock`
--

INSERT INTO `restock` (`Orestock_ID`, `ItemToOrder_ID`, `Type`, `Quantity`, `PurchaseUnitCost`, `OrderDate`, `Expected_Receive_Date`, `Product_ID`, `Supplier_ID`, `Status`, `Image`, `DeliveryStatus`, `Date_Received`, `TotalReceived`, `withIssue`, `ExpirationDate`) VALUES
(1, 1, 'Re-Order', 40, NULL, '2026-09-24 06:43:37', NULL, 1, 1, 'Received', NULL, NULL, '2026-09-24 06:45:18', 40, 0, '2026-10-24'),
(2, 2, 'Re-Order', 40, NULL, '2026-09-24 06:43:37', NULL, 2, 1, 'Received', NULL, NULL, '2026-09-24 06:45:52', 40, 0, '2026-10-24'),
(4, 3, 'Re-Order', 15, NULL, '2026-09-24 06:43:42', NULL, 3, 2, 'Received', NULL, NULL, '2026-09-24 06:48:31', 15, 0, '2026-10-24'),
(5, 4, 'Re-Order', 10, NULL, '2026-09-24 06:43:42', NULL, 4, 2, 'Received', NULL, NULL, '2026-09-24 06:48:52', 10, 0, '2026-10-24'),
(7, 5, 'Re-Order', 12, NULL, '2026-09-24 06:43:47', NULL, 5, 3, 'Received', NULL, NULL, '2026-09-24 06:49:07', 12, 0, '2026-10-24'),
(8, 6, 'Re-Order', 12, NULL, '2026-09-24 06:43:47', NULL, 6, 3, 'Received', NULL, NULL, '2026-09-24 06:49:27', 12, 0, '2026-10-24'),
(10, 7, 'Re-Order', 12, NULL, '2026-09-24 06:43:52', NULL, 7, 4, 'Received', NULL, NULL, '2026-09-24 06:49:47', 12, 0, '2026-10-24'),
(11, 8, 'Re-Order', 12, NULL, '2026-09-24 06:43:52', NULL, 8, 4, 'Received', NULL, NULL, '2026-09-24 06:51:01', 12, 0, '2026-10-24'),
(13, 9, 'Re-Order', 30, NULL, '2026-09-24 06:43:56', NULL, 9, 5, 'Received', NULL, NULL, '2026-09-24 06:51:56', 30, 0, '2026-10-24'),
(14, 10, 'Re-Order', 40, NULL, '2026-09-24 06:43:56', NULL, 10, 5, 'Received', NULL, NULL, '2026-09-24 06:52:10', 40, 0, '2026-10-24'),
(16, 11, 'Re-Order', 40, NULL, '2026-09-24 06:44:01', NULL, 11, 6, 'Received', NULL, NULL, '2026-09-24 06:52:24', 40, 0, '2026-10-24'),
(17, 12, 'Re-Order', 16, NULL, '2026-09-24 06:44:01', NULL, 12, 6, 'Received', NULL, NULL, '2026-09-24 06:52:38', 16, 0, '2026-10-24'),
(19, 13, 'Re-Order', 40, NULL, '2026-09-24 06:44:08', NULL, 13, 7, 'Received', NULL, NULL, '2026-09-24 06:52:53', 40, 0, '2026-10-24'),
(20, 14, 'Re-Order', 24, NULL, '2026-09-24 06:44:08', NULL, 14, 7, 'Received', NULL, NULL, '2026-09-24 06:53:08', 24, 0, '2026-10-24'),
(22, 15, 'Re-Order', 24, NULL, '2026-09-24 06:44:13', NULL, 15, 8, 'Partially-Received', NULL, 'Early', '2026-09-24 07:22:37', 12, 0, '2026-10-24'),
(23, 16, 'Re-Order', 40, NULL, '2026-09-24 06:44:13', NULL, 16, 8, 'Received', NULL, 'Early', '2026-09-24 07:24:42', 40, 0, '2026-10-24'),
(25, 15, 'Re-Order', 12, NULL, '2026-09-24 07:22:37', NULL, 15, 8, 'Received', NULL, 'Early', '2026-09-24 07:23:31', 12, 0, NULL),
(26, 17, 'Re-Order', 40, NULL, '2026-09-24 07:26:13', NULL, 1, 1, 'Received', NULL, 'Delayed', '2026-09-24 07:26:38', 40, 0, '2026-11-24'),
(27, NULL, 'New', 12, NULL, '2026-10-07 05:50:31', NULL, 1, 1, 'Received', NULL, NULL, '2026-10-22 05:18:11', 12, 0, NULL),
(28, NULL, 'New', 12, NULL, '2026-10-07 05:54:49', NULL, 2, 1, 'Received', NULL, NULL, '2026-10-22 05:18:01', 12, 0, NULL),
(29, NULL, 'New', 12, NULL, '2026-10-07 08:04:33', NULL, 1, 2, 'Received', NULL, NULL, '2026-10-22 05:17:51', 12, 0, NULL),
(30, NULL, 'New', 12, NULL, '2026-10-07 13:28:49', NULL, 17, 1, 'Received', NULL, NULL, '2026-10-22 05:17:42', 12, 0, NULL),
(31, NULL, 'New', 12, NULL, '2026-10-07 13:29:33', NULL, 17, 2, 'Received', NULL, NULL, '2026-10-22 05:17:29', 12, 0, NULL),
(32, NULL, 'New', 12, NULL, '2026-10-07 13:29:33', NULL, 17, 2, 'Received', NULL, NULL, '2026-10-22 05:17:20', 12, 0, NULL),
(33, NULL, 'New', 12, NULL, '2026-10-07 14:00:24', NULL, 17, 8, 'Received', NULL, NULL, '2026-10-22 05:17:11', 12, 0, NULL),
(34, NULL, 'New', 12, NULL, '2026-10-07 14:00:24', NULL, 17, 7, 'Received', NULL, NULL, '2026-10-22 05:16:57', 12, 0, NULL),
(35, NULL, 'New', 12, NULL, '2026-10-07 14:00:24', NULL, 17, 7, 'Received', NULL, NULL, '2026-10-22 05:16:46', 12, 0, NULL),
(36, NULL, 'New', 12, NULL, '2026-10-07 14:01:08', NULL, 17, 1, 'Received', NULL, NULL, '2026-10-22 05:16:38', 12, 0, NULL),
(37, NULL, 'New', 12, NULL, '2026-10-07 14:01:08', NULL, 17, 1, 'Received', NULL, NULL, NULL, 12, 0, NULL),
(38, NULL, 'New', 40, NULL, '2026-10-07 15:01:20', NULL, 16, 1, 'Received', NULL, NULL, '2026-10-22 05:16:16', 40, 0, NULL),
(39, NULL, 'New', 40, NULL, '2026-10-07 15:01:20', NULL, 13, 2, 'Received', NULL, NULL, '2026-10-22 05:15:57', 40, 0, NULL),
(40, NULL, 'New', 12, NULL, '2026-10-07 15:01:20', NULL, 5, 1, 'Received', NULL, NULL, '2026-10-22 05:15:43', 12, 0, NULL),
(41, NULL, 'New', 30, NULL, '2026-10-07 15:01:20', NULL, 9, 3, 'Received', NULL, NULL, '2026-10-22 05:15:30', 30, 0, NULL),
(42, NULL, 'New', 12, NULL, '2026-10-07 15:01:20', NULL, 6, 2, 'Received', NULL, NULL, '2026-10-22 05:15:20', 12, 0, NULL),
(43, NULL, 'New', 40, NULL, '2026-10-07 15:13:21', NULL, 16, 1, 'Received', NULL, NULL, '2026-10-22 05:15:10', 40, 0, NULL),
(44, NULL, 'New', 40, NULL, '2026-10-07 15:13:21', NULL, 13, 2, 'Received', NULL, NULL, NULL, 40, 0, NULL),
(45, NULL, 'New', 40, NULL, '2026-10-07 15:13:21', NULL, 10, 3, 'Received', NULL, NULL, NULL, 40, 0, NULL),
(46, NULL, 'New', 24, NULL, '2026-10-07 15:13:21', NULL, 14, 1, 'Partially-Received', NULL, NULL, '2026-10-08 03:11:51', 24, 0, NULL),
(48, NULL, 'New', 12, NULL, '2026-10-08 03:34:33', NULL, 17, 1, 'Received', NULL, NULL, NULL, 12, 0, NULL),
(49, NULL, 'New', 40, NULL, '2026-10-08 03:36:52', NULL, 1, 1, 'Partially-Received', NULL, NULL, '2026-10-08 03:37:09', 20, 0, NULL),
(50, 73, 'Re-Order', 12, NULL, '2026-10-08 04:25:05', NULL, 17, 2, 'Received', NULL, NULL, NULL, 12, 0, NULL),
(51, 74, 'Re-Order', 40, NULL, '2026-10-08 04:38:00', NULL, 2, 2, 'Received', NULL, NULL, NULL, 40, 0, NULL),
(52, 75, 'Re-Order', 40, NULL, '2026-10-08 04:38:00', NULL, 2, 2, 'Partially-Received', NULL, NULL, NULL, 20, 0, NULL),
(54, 75, 'Re-Order', 20, NULL, '2026-10-08 04:56:08', NULL, 2, 2, 'Received', NULL, 'Early', '2026-10-08 04:57:19', 20, 0, NULL),
(55, 76, 'Re-Order', 12, NULL, '2026-10-08 04:58:48', NULL, 17, 1, 'Received', NULL, 'Early', '2026-10-08 04:59:04', 12, 0, NULL),
(56, NULL, 'New', 12, NULL, '2026-10-08 06:13:05', NULL, 17, 1, 'Received', NULL, NULL, '2026-10-08 07:01:15', 12, 0, NULL),
(57, NULL, 'New', 12, NULL, '2026-10-08 07:28:18', NULL, 17, 1, 'Received-With-Issue', NULL, NULL, '2026-10-08 07:52:33', 10, 2, NULL),
(58, NULL, 'New', 80, NULL, '2026-10-08 11:53:25', '2026-10-11', 4, 2, 'Received', NULL, NULL, '2026-10-22 05:14:16', 80, 0, NULL),
(59, NULL, 'New', 24, NULL, '2026-10-08 11:53:25', '2026-10-12', 7, 4, 'Received', NULL, NULL, '2026-10-22 05:13:58', 24, 0, NULL),
(60, NULL, 'New', 30, NULL, '2026-10-08 11:53:25', '2026-10-11', 3, 2, 'Received', NULL, NULL, '2026-10-22 05:13:40', 30, 0, NULL),
(61, NULL, 'New', 16, NULL, '2026-10-08 11:53:25', '2026-10-13', 12, 6, 'Received', NULL, NULL, '2026-10-22 05:12:53', 16, 0, NULL),
(62, NULL, 'New', 40, NULL, '2026-10-08 11:53:25', '2026-10-13', 11, 6, 'Received', NULL, NULL, '2026-10-08 12:49:39', 40, 0, NULL),
(63, NULL, 'New', 600, NULL, '2026-10-08 12:50:35', NULL, 9, 1, 'Received', NULL, NULL, '2026-10-08 12:51:54', 600, 0, NULL),
(64, NULL, 'New', 30, NULL, '2026-10-08 12:52:37', NULL, 9, 1, 'Received', NULL, NULL, '2026-10-08 12:52:51', 30, 0, NULL),
(65, NULL, 'New', 40, NULL, '2026-10-08 13:07:14', NULL, 1, 1, 'Partially-Received', NULL, NULL, '2026-10-08 13:07:28', 20, 0, NULL),
(66, NULL, 'New', 12, NULL, '2026-10-08 13:14:48', NULL, 7, 1, 'Partially-Received', NULL, NULL, '2026-10-08 13:15:05', 6, 0, NULL),
(67, NULL, 'New', 6, NULL, '2026-10-08 13:15:05', NULL, 7, 1, 'Received', NULL, NULL, '2026-10-08 13:16:22', 6, 0, NULL),
(68, NULL, 'New', 12, NULL, '2026-10-08 16:00:42', '2026-10-15', 17, 1, 'Received', NULL, NULL, '2026-10-08 16:01:04', 12, 0, '2027-01-09'),
(72, NULL, 'New', 40, NULL, '2026-10-09 15:52:05', '2026-10-15', 16, 8, 'Received', NULL, NULL, '2026-10-22 05:13:23', 40, 0, NULL),
(73, NULL, 'New', 40, NULL, '2026-10-09 15:52:34', '2026-10-20', 16, 4, 'Received', NULL, NULL, '2026-10-22 05:13:14', 40, 0, NULL),
(74, NULL, 'New', 40, NULL, '2026-10-09 15:52:34', '2026-10-13', 13, 3, 'Received', NULL, NULL, '2026-10-22 05:13:04', 40, 0, NULL),
(76, NULL, 'New', 40, NULL, '2026-10-09 15:54:28', '2026-10-13', 16, 8, 'Received', NULL, NULL, '2026-10-22 05:12:37', 40, 0, NULL),
(77, NULL, 'New', 40, NULL, '2026-10-09 15:54:28', '2026-10-13', 13, 3, 'Received', NULL, NULL, '2026-10-22 05:12:26', 40, 0, NULL),
(80, NULL, 'New', 40, NULL, '2026-10-09 16:07:36', '2026-10-15', 16, 1, 'Partially-Received', NULL, NULL, '2026-10-09 16:11:55', 20, 10, NULL),
(81, NULL, 'New', 40, NULL, '2026-10-09 16:08:22', '2026-10-13', 16, 1, 'Received-With-Issue', NULL, NULL, '2026-10-09 16:11:31', 20, 20, NULL),
(82, NULL, 'New', 40, NULL, '2026-10-09 16:08:22', '2026-10-22', 13, 4, 'Partially-Received', NULL, NULL, '2026-10-09 16:10:41', 10, 0, NULL),
(83, NULL, 'New', 24, NULL, '2026-10-09 16:08:22', '2026-10-14', 14, 2, 'Received', NULL, NULL, '2026-10-09 16:10:15', 24, 0, NULL),
(84, NULL, 'New', 30, NULL, '2026-10-09 16:10:41', '2026-10-22', 13, 4, 'Received', NULL, NULL, '2026-10-09 16:11:02', 30, 0, NULL),
(85, NULL, 'New', 10, NULL, '2026-10-09 16:11:55', '2026-10-15', 16, 1, 'Received', NULL, NULL, '2026-10-09 16:12:52', 10, 0, NULL),
(86, NULL, 'New', 40, NULL, '2026-10-09 16:16:39', '2026-10-14', 16, 8, 'Received', NULL, NULL, '2026-10-22 05:12:15', 40, 0, NULL),
(87, NULL, 'New', 40, NULL, '2026-10-09 16:16:39', '2026-10-14', 13, 3, 'Received', NULL, NULL, '2026-10-22 05:12:04', 40, 0, NULL),
(88, NULL, 'New', 40, NULL, '2026-10-09 16:16:51', '2026-10-15', 11, 1, 'Received', NULL, NULL, '2026-10-22 05:11:54', 40, 0, NULL),
(89, NULL, 'New', 40, NULL, '2026-10-09 16:18:04', '2026-10-13', 16, 1, 'Received', NULL, NULL, '2026-10-22 05:11:42', 40, 0, NULL),
(90, NULL, 'New', 24, NULL, '2026-10-09 16:18:04', '2026-10-13', 14, 1, 'Received', NULL, NULL, '2026-10-22 05:11:32', 24, 0, NULL),
(91, NULL, 'New', 40, NULL, '2026-10-09 16:18:04', '2026-10-22', 13, 2, 'Received', NULL, NULL, '2026-10-22 05:10:55', 40, 0, NULL),
(92, NULL, 'New', 40, NULL, '2026-10-09 16:26:52', '2026-10-11', 16, 1, 'P-R-With-Issue', NULL, NULL, '2026-10-09 16:44:32', 20, 10, NULL),
(94, NULL, 'New', 24, NULL, '2026-10-09 16:26:52', '2026-10-21', 14, 6, 'Partially-Received', NULL, NULL, '2026-10-09 16:27:46', 12, 0, NULL),
(95, NULL, 'New', 40, NULL, '2026-10-09 16:26:52', '2026-10-21', 10, 8, 'Received-With-Issue', NULL, NULL, '2026-10-09 16:27:30', 30, 10, NULL),
(96, NULL, 'New', 24, NULL, '2026-10-09 16:26:52', '2026-10-12', 15, 2, 'Received', NULL, NULL, '2026-10-09 16:27:08', 24, 0, '2026-10-12'),
(97, NULL, 'New', 12, NULL, '2026-10-09 16:27:46', '2026-10-21', 14, 6, 'Received', NULL, NULL, '2026-10-09 16:28:00', 12, 0, NULL),
(98, NULL, 'New', 10, NULL, '2026-10-09 16:36:47', '2026-10-13', 13, 3, 'Received', NULL, NULL, '2026-10-09 16:41:16', 10, 0, NULL),
(99, NULL, 'New', 10, NULL, '2026-10-09 16:44:32', '2026-10-11', 16, 1, 'Received', NULL, NULL, '2026-10-22 05:11:21', 10, 0, NULL),
(100, NULL, 'New', 12, NULL, '2026-10-09 20:38:52', '2026-10-15', 17, 3, 'Received', NULL, NULL, '2026-10-09 20:39:09', 12, 0, NULL),
(101, NULL, 'New', 12, NULL, '2026-10-09 20:41:17', '2026-10-15', 17, 3, 'Partially-Received', NULL, NULL, '2026-10-10 06:09:22', 6, 0, NULL),
(102, NULL, 'New', 6, NULL, '2026-10-10 06:09:22', '2026-10-15', 17, 3, 'Received', NULL, NULL, '2026-10-22 05:11:08', 6, 0, NULL),
(103, NULL, 'New', 24, NULL, '2026-10-22 09:11:06', '2026-10-29', 14, 7, 'Received-With-Issue', NULL, 'Early', '2026-10-22 11:23:41', 0, 24, NULL),
(104, NULL, 'New', 40, 25.00, '2026-10-22 09:46:01', '2026-10-23', 13, 7, 'Partially-Received', NULL, NULL, '2026-10-22 10:59:58', 20, 0, NULL),
(105, NULL, 'New', 12, 140.00, '2026-10-22 10:52:11', '2026-10-23', 8, 4, 'Received-With-Issue', NULL, NULL, '2026-10-22 10:52:36', 6, 6, NULL),
(106, NULL, 'New', 12, 140.00, '2026-10-22 10:55:43', '2026-10-23', 8, 4, 'Received-With-Issue', NULL, NULL, '2026-10-22 10:55:53', 6, 6, NULL),
(107, NULL, 'New', 20, NULL, '2026-10-22 10:59:58', '2026-10-23', 13, 7, 'Backorder-Fulfillment', NULL, NULL, NULL, 0, 0, NULL),
(108, NULL, 'New', 12, 140.00, '2026-10-22 11:24:08', '2026-10-23', 8, 4, 'Received-With-Issue', NULL, 'Early', '2026-10-22 11:24:17', 0, 12, NULL),
(109, NULL, 'New', 12, 145.00, '2026-10-22 12:05:08', '2026-10-30', 17, 1, 'Partially-Received', NULL, 'Early', '2026-10-22 12:05:21', 10, 0, NULL),
(110, NULL, 'New', 2, 145.00, '2026-10-22 12:05:21', '2026-10-30', 17, 1, 'Backorder-Fulfillment', NULL, NULL, NULL, 0, 0, NULL),
(111, NULL, 'New', 12, 159.00, '2026-10-22 15:30:54', '2026-10-30', 17, 8, 'Received', NULL, 'Early', '2026-10-22 15:50:24', 12, 0, NULL),
(112, NULL, 'New', 12, 140.00, '2026-10-22 15:50:54', '2026-10-29', 8, 4, 'Requested', NULL, NULL, NULL, 0, 0, NULL),
(113, NULL, 'New', 12, 140.00, '2026-10-22 15:52:16', '2026-10-29', 8, 4, 'Requested', NULL, NULL, NULL, 0, 0, NULL),
(114, NULL, 'New', 12, 140.00, '2026-10-22 15:57:23', '2026-10-29', 8, 4, 'Requested', NULL, NULL, NULL, 0, 0, NULL),
(119, NULL, 'New', 40, 25.00, '2026-10-22 16:04:59', '2026-10-27', 13, 7, 'Requested', NULL, NULL, NULL, 0, 0, NULL),
(120, NULL, 'New', 24, 280.00, '2026-10-22 16:04:59', '2026-10-29', 15, 8, 'Requested', NULL, NULL, NULL, 0, 0, NULL),
(121, NULL, 'New', 12, 153.00, '2026-10-23 08:24:03', '2026-10-29', 17, 5, 'Requested', NULL, NULL, NULL, 0, 0, NULL),
(122, NULL, 'New', 12, 100.00, '2026-10-23 10:08:08', '2026-10-29', 27, 4, 'Received', NULL, 'Early', '2026-10-23 10:08:28', 12, 0, NULL),
(123, NULL, 'New', 12, 145.00, '2026-10-23 14:48:05', '2026-10-30', 17, 1, 'Requested', NULL, NULL, NULL, 0, 0, NULL),
(127, NULL, 'New', 12, 145.00, '2026-10-23 15:00:33', '2026-10-22', 17, 1, 'Requested', NULL, NULL, NULL, 0, 0, NULL),
(131, NULL, 'New', 24, 45.00, '2026-10-23 15:48:03', '2026-10-29', 14, 7, 'Requested', NULL, NULL, NULL, 0, 0, NULL),
(132, NULL, 'New', 20, NULL, '2026-10-23 15:48:21', '2026-10-28', 4, 2, 'Requested', NULL, NULL, NULL, 0, 0, NULL),
(133, NULL, 'New', 12, NULL, '2026-10-23 15:48:21', '2026-10-28', 6, 3, 'Requested', NULL, NULL, NULL, 0, 0, NULL),
(134, NULL, 'New', 12, NULL, '2026-10-23 15:48:21', '2026-10-28', 5, 3, 'Requested', NULL, NULL, NULL, 0, 0, NULL),
(135, NULL, 'New', 40, 42.00, '2026-10-24 05:58:42', '2026-10-26', 2, 1, 'Received', NULL, 'Early', '2026-10-24 05:58:55', 40, 0, NULL),
(136, NULL, 'New', 40, 390.00, '2026-10-24 11:15:30', '2026-10-25', 16, 8, 'Requested', NULL, NULL, NULL, 0, 0, NULL),
(137, NULL, 'New', 40, 350.00, '2026-10-24 11:21:34', '2026-10-28', 16, 1, 'Requested', NULL, NULL, NULL, 0, 0, NULL),
(138, NULL, 'New', 12, 145.00, '2026-10-24 11:26:27', '2026-10-28', 17, 1, 'Requested', NULL, NULL, NULL, 0, 0, NULL),
(139, NULL, 'New', 12, 18.00, '2026-10-24 11:59:39', '2026-10-25', 28, 9, 'Received', NULL, 'Early', '2026-10-24 11:59:59', 12, 0, '2026-11-24');

--
-- Triggers `restock`
--
DELIMITER $$
CREATE TRIGGER `restock_status_received` BEFORE UPDATE ON `restock` FOR EACH ROW BEGIN
    DECLARE v_ExpectedReceiveDate DATE;

    /*
      1. Record the first physical arrival.
      Good or defective units both count as an arrival.
      Preserve the original date on subsequent updates.
    */
    IF NEW.Date_Received IS NULL
       AND (
           IFNULL(NEW.TotalReceived, 0) >
               IFNULL(OLD.TotalReceived, 0)
           OR
           IFNULL(NEW.withIssue, 0) >
               IFNULL(OLD.withIssue, 0)
       )
    THEN
        SET NEW.Date_Received = CURRENT_TIMESTAMP;
    END IF;

    /*
      2. Start with the expected date on the direct restock row.
    */
    SET v_ExpectedReceiveDate = NEW.Expected_Receive_Date;

    /*
      3. For legacy orders, use the expected date from
         list_to_order when the linked record exists.
    */
    IF NEW.ItemToOrder_ID IS NOT NULL THEN
        SET v_ExpectedReceiveDate = (
            SELECT l.Expected_Receive_Date
            FROM item_to_order i
            INNER JOIN list_to_order l
                ON l.ListToOrder_ID = i.ListToOrder_ID
            WHERE i.ItemToOrder_ID = NEW.ItemToOrder_ID
            LIMIT 1
        );
    END IF;

    /*
      4. Calculate delivery timing once an arrival is recorded.
    */
    IF NEW.Date_Received IS NOT NULL
       AND v_ExpectedReceiveDate IS NOT NULL
    THEN
        IF DATE(NEW.Date_Received) < v_ExpectedReceiveDate THEN
            SET NEW.DeliveryStatus = 'Early';

        ELSEIF DATE(NEW.Date_Received) = v_ExpectedReceiveDate THEN
            SET NEW.DeliveryStatus = 'On-Time';

        ELSE
            SET NEW.DeliveryStatus = 'Delayed';
        END IF;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `restock_status_received_old` BEFORE UPDATE ON `restock` FOR EACH ROW BEGIN
    -- IF NEW.Status = 'Received' AND OLD.Status <> 'Received' THEN
        -- SET NEW.Date_Received = CURRENT_TIMESTAMP;
        -- SET NEW.DeliveryStatus = 'On-Time';
    -- END IF;
    
    
    
    
    
       -- IF NEW.Status IN (
        -- 'Partially-Received',
        -- 'Backorder-Fulfillment',
        -- 'Received'
    -- )
    -- AND IFNULL(NEW.TotalReceived, 0)
        -- > IFNULL(OLD.TotalReceived, 0)
    -- THEN

        -- SET NEW.Date_Received = CURRENT_TIMESTAMP;

    -- END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `restock_status_received_old2` BEFORE UPDATE ON `restock` FOR EACH ROW BEGIN

    -- IF NEW.Status IN (
        -- 'Partially-Received',
        -- 'Backorder-Fulfillment',
        -- 'Received'
    -- )
    -- AND IFNULL(NEW.TotalReceived, 0)
        -- > IFNULL(OLD.TotalReceived, 0)
    -- THEN

        -- SET NEW.Date_Received = CURRENT_TIMESTAMP;

    -- END IF;

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_product_unitorder` BEFORE UPDATE ON `restock` FOR EACH ROW BEGIN
    DECLARE v_NewInventory INT;
    DECLARE new_status VARCHAR(30);
    DECLARE v_BatchNum VARCHAR(50);
    DECLARE v_BatchCount INT;
    DECLARE v_ReceivedDiff INT DEFAULT 0;
    DECLARE v_NewReceivedTotal INT DEFAULT 0;
    DECLARE v_TotalAccounted INT DEFAULT 0;

    -- 1. Validate received and defective quantities
    IF IFNULL(NEW.TotalReceived, 0) < 0
       OR IFNULL(NEW.withIssue, 0) < 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT =
            'Received quantity and issue quantity cannot be negative.';
    END IF;

    IF (
        IFNULL(NEW.TotalReceived, 0) +
        IFNULL(NEW.withIssue, 0)
    ) > IFNULL(NEW.Quantity, 0) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT =
            'TotalReceived + withIssue cannot exceed Quantity.';
    END IF;

    -- 2. Calculate the change in good units received
    SET v_ReceivedDiff =
        IFNULL(NEW.TotalReceived, 0) -
        IFNULL(OLD.TotalReceived, 0);

    -- 3. Automatically determine restock status
    SET v_TotalAccounted =
        IFNULL(NEW.TotalReceived, 0) +
        IFNULL(NEW.withIssue, 0);

    IF v_TotalAccounted <= 0 THEN
        SET NEW.Status = 'Requested';

    ELSEIF v_TotalAccounted < IFNULL(NEW.Quantity, 0)
       AND IFNULL(NEW.withIssue, 0) > 0 THEN
        SET NEW.Status = 'P-R-With-Issue';

    ELSEIF v_TotalAccounted < IFNULL(NEW.Quantity, 0) THEN
        SET NEW.Status = 'Partially-Received';

    ELSEIF IFNULL(NEW.withIssue, 0) > 0 THEN
        SET NEW.Status = 'Received-With-Issue';

    ELSE
        SET NEW.Status = 'Received';
    END IF;

    -- 4. Update legacy item-to-order tracking when applicable
    IF NEW.ItemToOrder_ID IS NOT NULL
       AND v_ReceivedDiff <> 0 THEN

        SELECT
            GREATEST(
                IFNULL(Received_Quantity, 0) + v_ReceivedDiff,
                0
            )
        INTO v_NewReceivedTotal
        FROM item_to_order
        WHERE ItemToOrder_ID = NEW.ItemToOrder_ID
        LIMIT 1;

        UPDATE item_to_order
        SET
            Received_Quantity = v_NewReceivedTotal,
            Item_Status = CASE
                WHEN v_NewReceivedTotal >=
                     IFNULL(Ordered_Quantity, 0)
                    THEN 'Received'
                ELSE 'Partially-Received'
            END
        WHERE ItemToOrder_ID = NEW.ItemToOrder_ID;
    END IF;

    -- 5. Update product stock using good units only
    IF v_ReceivedDiff <> 0 THEN

        UPDATE product
        SET UnitsOrdered = GREATEST(
            IFNULL(UnitsOrdered, 0) + v_ReceivedDiff,
            0
        )
        WHERE Product_ID = NEW.Product_ID;

        SELECT
            IFNULL(UnitsOrdered, 0) - IFNULL(UnitSold, 0)
        INTO v_NewInventory
        FROM product
        WHERE Product_ID = NEW.Product_ID;

        SET new_status = CASE
            WHEN v_NewInventory > 10 THEN 'IN-STOCK'
            WHEN v_NewInventory > 0 THEN 'LOW-STOCK'
            ELSE 'OUT-OF-STOCK'
        END;

        UPDATE inventory
        SET
            UnitIN = COALESCE(
                NEW.Date_Received,
                CURRENT_TIMESTAMP
            ),
            Inventory = v_NewInventory,
            Status = new_status,
            ExpirationDate = CASE
                WHEN ExpirationDate IS NULL
                    THEN NEW.ExpirationDate
                WHEN NEW.ExpirationDate IS NULL
                    THEN ExpirationDate
                WHEN NEW.ExpirationDate < ExpirationDate
                    THEN NEW.ExpirationDate
                ELSE ExpirationDate
            END
        WHERE Product_ID = NEW.Product_ID;

        -- Removed the legacy product.Supplier_ID update.
        -- Supplier selection is handled by product_supplier.
    END IF;

    -- 6. Create or update expiration batches for good units only
    IF v_ReceivedDiff > 0
       AND NEW.ExpirationDate IS NOT NULL THEN

        SELECT COUNT(*) + 1
        INTO v_BatchCount
        FROM expiration
        WHERE Product_ID = NEW.Product_ID;

        SET v_BatchNum = CONCAT(
            'BATCH-',
            NEW.Product_ID,
            '-',
            LPAD(v_BatchCount, 3, '0')
        );

        IF EXISTS (
            SELECT 1
            FROM expiration
            WHERE Product_ID = NEW.Product_ID
              AND ExpirationDate = NEW.ExpirationDate
        ) THEN

            UPDATE expiration
            SET Quantity = GREATEST(
                IFNULL(Quantity, 0) + v_ReceivedDiff,
                0
            )
            WHERE Product_ID = NEW.Product_ID
              AND ExpirationDate = NEW.ExpirationDate;

        ELSE

            INSERT INTO expiration (
                Product_ID,
                BatchNum,
                ExpirationDate,
                Quantity
            )
            VALUES (
                NEW.Product_ID,
                v_BatchNum,
                NEW.ExpirationDate,
                v_ReceivedDiff
            );

        END IF;
    END IF;

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_restock_request` BEFORE UPDATE ON `restock` FOR EACH ROW BEGIN
	-- UPDATE restock
	-- SET 
    -- Product_ID = New.Product_ID,
	-- Supplier_ID = New.Supplier_ID,
    -- Quantity = New.Quantity;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `Order_ID` int NOT NULL,
  `Transaction_ID` int NOT NULL,
  `Product_ID` int NOT NULL,
  `ProductName` varchar(100) NOT NULL,
  `Unit_Price` int NOT NULL,
  `Quantity` int NOT NULL,
  `TotalPrice` int DEFAULT NULL,
  `Barcode` varchar(100) NOT NULL,
  `SalesDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `id` int DEFAULT NULL,
  `BatchNum` varchar(50) DEFAULT NULL,
  `userName` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Triggers `sales`
--
DELIMITER $$
CREATE TRIGGER `after_insert_sales` AFTER INSERT ON `sales` FOR EACH ROW BEGIN
    -- UPDATE product
    -- SET UnitSold = UnitSold + NEW.Quantity
    -- WHERE Product_ID = NEW.Product_ID;
    
    -- UPDATE expiration
    -- SET Quantity = NEW.Quantity
    -- WHERE Product_ID = NEW.Product_ID;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_sales_insert_aggregration` AFTER INSERT ON `sales` FOR EACH ROW BEGIN
    DECLARE daily_start DATE;
    DECLARE daily_end DATE;
    
    DECLARE weekly_start DATE;
    DECLARE weekly_end DATE;
    
    DECLARE monthly_start DATE;
    DECLARE monthly_end DATE;

    -- Compute period boundaries
    SET daily_start = DATE(NEW.SalesDate);
    SET daily_end = daily_start;

    SET weekly_start = DATE_SUB(NEW.SalesDate, INTERVAL WEEKDAY(NEW.SalesDate) DAY);
    SET weekly_end = DATE_ADD(weekly_start, INTERVAL 6 DAY);

    SET monthly_start = DATE_FORMAT(NEW.SalesDate, '%Y-%m-01');
    SET monthly_end = LAST_DAY(NEW.SalesDate);

    -- DAILY AGGREGATION
    INSERT INTO daily_sales (
        PeriodStart, PeriodEnd, Product_ID, TotalSales, TotalQuantity
    )
    VALUES (
         daily_start, daily_end, NEW.Product_ID, NEW.TotalPrice, NEW.Quantity
    )
    ON DUPLICATE KEY UPDATE
        TotalSales = TotalSales + NEW.TotalPrice,
        TotalQuantity = TotalQuantity + NEW.Quantity;

    -- WEEKLY AGGREGATION (no PeriodType)
    INSERT INTO weekly_sales (
        PeriodStart, PeriodEnd, Product_ID, TotalSales, TotalQuantity
    )
    VALUES (
        weekly_start, weekly_end, NEW.Product_ID, NEW.TotalPrice, NEW.Quantity
    )
    ON DUPLICATE KEY UPDATE
        TotalSales = TotalSales + NEW.TotalPrice,
        TotalQuantity = TotalQuantity + NEW.Quantity;

    -- MONTHLY AGGREGATION
    INSERT INTO monthly_sales (
         PeriodStart, PeriodEnd, Product_ID, TotalSales, TotalQuantity
    )
    VALUES (
         monthly_start, monthly_end, NEW.Product_ID, NEW.TotalPrice, NEW.Quantity
    )
    ON DUPLICATE KEY UPDATE
        TotalSales = TotalSales + NEW.TotalPrice,
        TotalQuantity = TotalQuantity + NEW.Quantity;



    -- DAILY TOTAL AGGREGATION (all products)
    INSERT INTO daily_total_sales (
        PeriodStart, PeriodEnd, TotalSales, TotalQuantity
    )
    VALUES (
        daily_start, daily_end, NEW.TotalPrice, NEW.Quantity
    )
    ON DUPLICATE KEY UPDATE
        TotalSales = TotalSales + NEW.TotalPrice,
        TotalQuantity = TotalQuantity + NEW.Quantity;

    -- WEEKLY TOTAL AGGREGATION (all products)
    INSERT INTO weekly_total_sales (
        PeriodStart, PeriodEnd, TotalSales, TotalQuantity
    )
    VALUES (
        weekly_start, weekly_end, TotalSales, TotalQuantity
    )
    ON DUPLICATE KEY UPDATE
        TotalSales = TotalSales + NEW.TotalPrice,
        TotalQuantity = TotalQuantity + NEW.Quantity;

    -- MONTHLY TOTAL AGGREGATION (all products)
    INSERT INTO monthly_total_sales (
        PeriodStart, PeriodEnd, TotalSales, TotalQuantity
    )
    VALUES (
        monthly_start, monthly_end, TotalSales, TotalQuantity
    )
    ON DUPLICATE KEY UPDATE
        TotalSales = TotalSales + NEW.TotalPrice,
        TotalQuantity = TotalQuantity + NEW.Quantity;








END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_sales_insert_inventory` AFTER INSERT ON `sales` FOR EACH ROW BEGIN
    DECLARE current_inventory INT DEFAULT 0;
    DECLARE new_inventory INT DEFAULT 0;
    DECLARE new_status VARCHAR(20);
    DECLARE TransactionDate DATETIME;

    -- 1️⃣ Get current inventory for this product
    SELECT Inventory INTO current_inventory
    FROM inventory
    WHERE Product_ID = NEW.Product_ID
    LIMIT 1;

    -- 2️⃣ Compute new inventory
    SET new_inventory = current_inventory - NEW.Quantity;
    
    SELECT Transaction_Date INTO TransactionDate
    FROM transactions
    WHERE Transaction_ID = NEW.Transaction_ID;

    -- 3️⃣ Determine stock status
    SET new_status = CASE 
        WHEN new_inventory > 10 THEN 'IN-STOCK'
        WHEN new_inventory > 0 THEN 'LOW-STOCK'
        ELSE 'OUT-OF-STOCK'
    END;

    -- 4️⃣ Update main inventory table
    UPDATE inventory
    SET 
        Inventory = new_inventory,
        -- UnitOut = NEW.SalesDate,
        UnitOut = TransactionDate,
        Status = new_status
    WHERE Product_ID = NEW.Product_ID;

    -- 5️⃣ Update total sold units in product table
    UPDATE product
    SET UnitSold = UnitSold + NEW.Quantity
    WHERE Product_ID = NEW.Product_ID;

    -- 6️⃣ Deduct from the correct expiration batch only
    UPDATE expiration
    SET Quantity = Quantity - NEW.Quantity
    WHERE Product_ID = NEW.Product_ID
      AND BatchNum = NEW.BatchNum; 

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_sales_update_inventory` BEFORE UPDATE ON `sales` FOR EACH ROW BEGIN
    DECLARE new_inventory INT;
    DECLARE new_status VARCHAR(20);
    DECLARE DATErelease DATETIME;

    -- Calculate new inventory from product table
    SELECT UnitsOrdered - UnitSold INTO new_inventory
    FROM product
    WHERE Product_ID = NEW.Product_ID;

    -- Determine the new status
    SET new_status = CASE 
        WHEN new_inventory > 10 THEN 'IN-STOCK'
        WHEN new_inventory > 0 THEN 'LOW-STOCK'
        ELSE 'OUT-OF-STOCK'
    END;

    -- Update the inventory table
    SELECT Transaction_Date INTO DATErelease
    FROM transactions
    WHERE Transaction_ID = NEW.Transaction_ID;
    
    UPDATE inventory
    SET 
        Inventory = new_inventory,
        UnitOut = DATErelease,
        Status = new_status
    WHERE Product_ID = NEW.Product_ID;
    

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `sales_to_transactions` AFTER INSERT ON `sales` FOR EACH ROW BEGIN
    -- Update the inventory table
    
    DECLARE Price INT;
    SET Price = New.TotalPrice;
    
    UPDATE transactions
    SET 
        Total_Price = COALESCE(Total_Price, 0) + Price
        -- UnitOut = NEW.SalesDate,
        -- Status = new_status
    WHERE Transaction_ID = NEW.Transaction_ID;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `sales_to_transactions_UPDATE` AFTER UPDATE ON `sales` FOR EACH ROW BEGIN
    DECLARE price_difference DECIMAL(10,2);

    -- Compute the difference between new and old total prices
    SET price_difference = NEW.TotalPrice - OLD.TotalPrice;

    -- Apply only the difference to the transaction's total
    UPDATE transactions
    SET Total_Price = COALESCE(Total_Price, 0) + price_difference
    WHERE Transaction_ID = NEW.Transaction_ID;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `salesaggregration`
--

CREATE TABLE `salesaggregration` (
  `Aggregation_ID` int NOT NULL,
  `PeriodType` enum('daily','weekly','monthly') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `PeriodStart` date NOT NULL,
  `PeriodEnd` date NOT NULL,
  `Product_ID` int NOT NULL,
  `TotalSales` decimal(10,2) NOT NULL,
  `TotalQuantity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stock_adjustments`
--

CREATE TABLE `stock_adjustments` (
  `Adjustment_ID` int NOT NULL,
  `Product_ID` int NOT NULL,
  `User_ID` int DEFAULT NULL,
  `Adjustment_Type` enum('increase','decrease') NOT NULL,
  `Quantity` int NOT NULL,
  `Previous_Stock` int NOT NULL,
  `New_Stock` int NOT NULL,
  `Reason` varchar(255) NOT NULL,
  `Notes` text,
  `BatchNum` varchar(50) NOT NULL,
  `Date_Created` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `stock_adjustments`
--

INSERT INTO `stock_adjustments` (`Adjustment_ID`, `Product_ID`, `User_ID`, `Adjustment_Type`, `Quantity`, `Previous_Stock`, `New_Stock`, `Reason`, `Notes`, `BatchNum`, `Date_Created`) VALUES
(1, 28, NULL, 'increase', 2, 8, 10, 'Other', 'additional stock', 'BATCH-28-001', '2026-10-24 13:40:20'),
(2, 28, NULL, 'decrease', 2, 10, 8, 'Damaged Items', '', 'BATCH-28-001', '2026-10-24 13:41:14');

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
  `ProductCategory` enum('Noodles and Instant Meals','Frozen Hotpot Goods','Side Dishes','Canned Goods','Beverages','Sweets and Snacks','Ice Cream','Beauty & Personal Care') NOT NULL,
  `IsArchived` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `supplier`
--

INSERT INTO `supplier` (`Supplier_ID`, `CompanyName`, `ContactPerson`, `Address`, `City`, `Email`, `PhoneNumber`, `ProductCategory`, `IsArchived`) VALUES
(1, 'JIN MART KOREAN GROCERY', 'BUGOY', 'HK SUN PLAZA, DIOSDADO MACAPAGAL BLVD, PASAY CITY, 1300', 'METRO MANILA', 'jinmart@gmail.com', '09177111844', 'Noodles and Instant Meals', 1),
(2, 'JOSE', 'dela cruz', 'MAKATI', NULL, 'josedelacruz@gmail.com', '09112233445', 'Frozen Hotpot Goods', 0),
(3, 'James', 'DELAPAZ', 'Pasig', NULL, 'jamesdelapaz@gmail.com', '09112233445', 'Side Dishes', 0),
(4, 'john', 'de;lo santos', 'malabon', NULL, 'johndelosantos@gmail.com', '09112233445', 'Canned Goods', 0),
(5, 'michael', 'JACINTO', 'QUEZON CITY', NULL, 'michaeljacinto@gmail.com', '09112233445', 'Beverages', 0),
(6, 'thomas', 'hilario', 'tondo', NULL, 'thomashilario@gmail.com', '09112233445', 'Sweets and Snacks', 0),
(7, 'joshua', 'santos', 'recto', NULL, 'joshuasantos@gmail.com', '09112233445', 'Ice Cream', 0),
(8, 'alex', 'santos', 'baclaran', NULL, 'alexsantos@gmail.com', '09112233445', 'Beauty & Personal Care', 0),
(9, 'IMSIN KOREAN STORE', 'JOAN CRUZ', '198 INTERIOR 2 PATEROS ST, MAYPAJO,', 'CALOOCAN', 'imsin@gmail.com', '09616030481', 'Ice Cream', 0);

-- --------------------------------------------------------

--
-- Table structure for table `supplierreturns`
--

CREATE TABLE `supplierreturns` (
  `SReturns_ID` int NOT NULL,
  `Supplier_ID` int NOT NULL,
  `Product_ID` int NOT NULL,
  `Quantity` int NOT NULL,
  `ReturnedDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Status` enum('Pending','Out-for-Delivery','Cancelled','Delivered') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `Reason` enum('Wrong-Item','Damaged','Expired','Other') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `supplierreturns`
--

INSERT INTO `supplierreturns` (`SReturns_ID`, `Supplier_ID`, `Product_ID`, `Quantity`, `ReturnedDate`, `Status`, `Reason`) VALUES
(5, 8, 16, 1, '2026-10-09 16:00:00', 'Cancelled', 'Expired'),
(6, 8, 16, 1, '2026-10-09 16:03:30', 'Out-for-Delivery', 'Damaged'),
(7, 1, 13, 1, '2026-10-09 16:00:00', 'Cancelled', 'Expired'),
(8, 2, 15, 1, '2026-10-09 16:04:04', 'Delivered', 'Other'),
(9, 2, 14, 1, '2026-10-09 16:00:00', 'Cancelled', 'Expired');

-- --------------------------------------------------------

--
-- Table structure for table `toorder`
--

CREATE TABLE `toorder` (
  `ToOrder` int NOT NULL,
  `Current_Stock` int NOT NULL,
  `Predicted_Demand` int NOT NULL,
  `Order_Amount` int NOT NULL,
  `Supplier` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `Transaction_ID` int NOT NULL,
  `ReferenceNo` varchar(100) NOT NULL,
  `PaymentMethod` enum('','Cash','eWallet') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `Transaction_Date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Total_Price` int DEFAULT NULL,
  `User_ID` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `userName` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `phone` varchar(100) DEFAULT NULL,
  `image` blob,
  `reset_token_hash` varchar(100) DEFAULT NULL,
  `reset_token_expires_at` datetime DEFAULT NULL,
  `account_activation_hash` varchar(64) DEFAULT NULL,
  `accountType` enum('Manager','Cashier','Super Admin') NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `userName`, `email`, `password`, `phone`, `image`, `reset_token_hash`, `reset_token_expires_at`, `account_activation_hash`, `accountType`, `is_active`) VALUES
(17, 'Carlo', 'gomezcarlo222@gmail.com', 'c9e72a64b39834ba291fc2344d57f6f1', '09167549519', NULL, NULL, NULL, NULL, 'Cashier', 1),
(18, 'JAY', 'gomezcarlo333@gmail.com', 'c9e72a64b39834ba291fc2344d57f6f1', '09657763817', NULL, NULL, NULL, NULL, 'Super Admin', 1),
(20, 'yuma', 'delacruzbryanlenard@gmail.com', 'a6dfdb5e296de21896a5e29be9510ba4', '09167549519', NULL, NULL, NULL, NULL, 'Manager', 1);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_sales_forecast`
-- (See below for the actual view)
--
CREATE TABLE `v_sales_forecast` (
`Product_ID` int
,`ProductName` varchar(100)
,`SalesDate` date
,`DailyQuantity` decimal(32,0)
,`MovingAverage3` decimal(35,2)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_sales_next_forecast`
-- (See below for the actual view)
--
CREATE TABLE `v_sales_next_forecast` (
`Product_ID` int
,`ProductName` varchar(100)
,`ForecastDate` date
,`ForecastValue` decimal(35,2)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_store_sales_forecast`
-- (See below for the actual view)
--
CREATE TABLE `v_store_sales_forecast` (
`SalesDate` date
,`DailyTotalQuantity` decimal(32,0)
,`MovingAverage3` decimal(35,2)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_store_sales_forecast_all`
-- (See below for the actual view)
--
CREATE TABLE `v_store_sales_forecast_all` (
`SalesDate` date
,`DailyTotalQuantity` decimal(32,0)
,`Daily_MA3` decimal(35,2)
,`WeekStart` date
,`WeeklyTotalQuantity` decimal(32,0)
,`Weekly_MA3` decimal(35,2)
,`MonthStart` date
,`MonthlyTotalQuantity` decimal(32,0)
,`Monthly_MA3` decimal(35,2)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_store_sales_forecast_daily`
-- (See below for the actual view)
--
CREATE TABLE `v_store_sales_forecast_daily` (
`Period` date
,`TotalQuantity` decimal(32,0)
,`MovingAverage3` decimal(35,2)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_store_sales_forecast_monthly`
-- (See below for the actual view)
--
CREATE TABLE `v_store_sales_forecast_monthly` (
`Period` varchar(7)
,`TotalQuantity` decimal(32,0)
,`MovingAverage3` decimal(35,2)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_store_sales_forecast_weekly`
-- (See below for the actual view)
--
CREATE TABLE `v_store_sales_forecast_weekly` (
`Period` int
,`TotalQuantity` decimal(32,0)
,`MovingAverage3` decimal(35,2)
);

-- --------------------------------------------------------

--
-- Table structure for table `weekly_sales`
--

CREATE TABLE `weekly_sales` (
  `WeeklySales_ID` int NOT NULL,
  `PeriodStart` date DEFAULT NULL,
  `PeriodEnd` date DEFAULT NULL,
  `Product_ID` int DEFAULT NULL,
  `TotalSales` decimal(10,2) DEFAULT NULL,
  `TotalQuantity` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `weekly_total_sales`
--

CREATE TABLE `weekly_total_sales` (
  `EntireWeeklySales_ID` int NOT NULL,
  `PeriodStart` date NOT NULL,
  `PeriodEnd` date NOT NULL,
  `TotalSales` decimal(12,2) NOT NULL DEFAULT '0.00',
  `TotalQuantity` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure for view `daily_sales_summary`
--
DROP TABLE IF EXISTS `daily_sales_summary`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `daily_sales_summary`  AS SELECT `sales`.`Product_ID` AS `Product_ID`, `sales`.`ProductName` AS `ProductName`, `sales`.`SalesDate` AS `SalesDate`, sum(`sales`.`Quantity`) AS `TotalQuantity`, sum(`sales`.`TotalPrice`) AS `TotalRevenue` FROM `sales` GROUP BY `sales`.`Product_ID`, `sales`.`ProductName`, `sales`.`SalesDate` ORDER BY `sales`.`SalesDate` ASC, `sales`.`Product_ID` ASC ;

-- --------------------------------------------------------

--
-- Structure for view `v_sales_forecast`
--
DROP TABLE IF EXISTS `v_sales_forecast`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_sales_forecast`  AS SELECT `sales`.`Product_ID` AS `Product_ID`, `sales`.`ProductName` AS `ProductName`, cast(`sales`.`SalesDate` as date) AS `SalesDate`, sum(`sales`.`Quantity`) AS `DailyQuantity`, round(avg(sum(`sales`.`Quantity`)) OVER (PARTITION BY `sales`.`Product_ID` ORDER BY cast(`sales`.`SalesDate` as date) ROWS BETWEEN 2 PRECEDING AND CURRENT ROW) ,2) AS `MovingAverage3` FROM `sales` GROUP BY `sales`.`Product_ID`, `sales`.`ProductName`, cast(`sales`.`SalesDate` as date) ORDER BY `sales`.`Product_ID` ASC, cast(`sales`.`SalesDate` as date) ASC ;

-- --------------------------------------------------------

--
-- Structure for view `v_sales_next_forecast`
--
DROP TABLE IF EXISTS `v_sales_next_forecast`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_sales_next_forecast`  AS SELECT `last3`.`Product_ID` AS `Product_ID`, `last3`.`ProductName` AS `ProductName`, (max(`last3`.`SalesDate`) + interval 1 day) AS `ForecastDate`, round(avg(`last3`.`DailyTotal`),2) AS `ForecastValue` FROM (select `sales`.`Product_ID` AS `Product_ID`,`sales`.`ProductName` AS `ProductName`,cast(`sales`.`SalesDate` as date) AS `SalesDate`,sum(`sales`.`TotalPrice`) AS `DailyTotal` from `sales` group by `sales`.`Product_ID`,`sales`.`ProductName`,cast(`sales`.`SalesDate` as date) order by `sales`.`Product_ID`,`SalesDate` desc limit 3) AS `last3` GROUP BY `last3`.`Product_ID`, `last3`.`ProductName` ;

-- --------------------------------------------------------

--
-- Structure for view `v_store_sales_forecast`
--
DROP TABLE IF EXISTS `v_store_sales_forecast`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_store_sales_forecast`  AS SELECT cast(`s`.`SalesDate` as date) AS `SalesDate`, sum(`s`.`Quantity`) AS `DailyTotalQuantity`, round(avg(sum(`s`.`Quantity`)) OVER (ORDER BY cast(`s`.`SalesDate` as date) ROWS BETWEEN 2 PRECEDING AND CURRENT ROW) ,2) AS `MovingAverage3` FROM `sales` AS `s` GROUP BY cast(`s`.`SalesDate` as date) ORDER BY cast(`s`.`SalesDate` as date) ASC ;

-- --------------------------------------------------------

--
-- Structure for view `v_store_sales_forecast_all`
--
DROP TABLE IF EXISTS `v_store_sales_forecast_all`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_store_sales_forecast_all`  AS WITH     `daily` as (select cast(`sales`.`SalesDate` as date) AS `SalesDate`,sum(`sales`.`Quantity`) AS `DailyTotalQuantity`,round(avg(sum(`sales`.`Quantity`)) OVER (ORDER BY cast(`sales`.`SalesDate` as date) ROWS BETWEEN 2 PRECEDING AND CURRENT ROW) ,2) AS `Daily_MA3` from `sales` group by cast(`sales`.`SalesDate` as date)), `weekly` as (select yearweek(`sales`.`SalesDate`,1) AS `WeekNum`,min(cast(`sales`.`SalesDate` as date)) AS `WeekStart`,sum(`sales`.`Quantity`) AS `WeeklyTotalQuantity`,round(avg(sum(`sales`.`Quantity`)) OVER (ORDER BY yearweek(`sales`.`SalesDate`,1) ROWS BETWEEN 2 PRECEDING AND CURRENT ROW) ,2) AS `Weekly_MA3` from `sales` group by yearweek(`sales`.`SalesDate`,1)), `monthly` as (select date_format(`sales`.`SalesDate`,'%Y-%m') AS `Month`,min(cast(`sales`.`SalesDate` as date)) AS `MonthStart`,sum(`sales`.`Quantity`) AS `MonthlyTotalQuantity`,round(avg(sum(`sales`.`Quantity`)) OVER (ORDER BY date_format(`sales`.`SalesDate`,'%Y-%m') ROWS BETWEEN 2 PRECEDING AND CURRENT ROW) ,2) AS `Monthly_MA3` from `sales` group by date_format(`sales`.`SalesDate`,'%Y-%m')) select `d`.`SalesDate` AS `SalesDate`,`d`.`DailyTotalQuantity` AS `DailyTotalQuantity`,`d`.`Daily_MA3` AS `Daily_MA3`,`w`.`WeekStart` AS `WeekStart`,`w`.`WeeklyTotalQuantity` AS `WeeklyTotalQuantity`,`w`.`Weekly_MA3` AS `Weekly_MA3`,`m`.`MonthStart` AS `MonthStart`,`m`.`MonthlyTotalQuantity` AS `MonthlyTotalQuantity`,`m`.`Monthly_MA3` AS `Monthly_MA3` from ((`daily` `d` left join `weekly` `w` on((yearweek(`d`.`SalesDate`,1) = `w`.`WeekNum`))) left join `monthly` `m` on((date_format(`d`.`SalesDate`,'%Y-%m') = `m`.`Month`)))  ;

-- --------------------------------------------------------

--
-- Structure for view `v_store_sales_forecast_daily`
--
DROP TABLE IF EXISTS `v_store_sales_forecast_daily`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_store_sales_forecast_daily`  AS SELECT cast(`s`.`date_created` as date) AS `Period`, sum(`si`.`Quantity`) AS `TotalQuantity`, round(avg(sum(`si`.`Quantity`)) OVER (ORDER BY cast(`s`.`date_created` as date) ROWS BETWEEN 2 PRECEDING AND CURRENT ROW) ,2) AS `MovingAverage3` FROM (`point_of_sale`.`sales_item_test` `si` join `point_of_sale`.`sales_test` `s` on((`s`.`Sales_ID` = `si`.`Sales_ID`))) GROUP BY cast(`s`.`date_created` as date) ORDER BY cast(`s`.`date_created` as date) ASC ;

-- --------------------------------------------------------

--
-- Structure for view `v_store_sales_forecast_monthly`
--
DROP TABLE IF EXISTS `v_store_sales_forecast_monthly`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_store_sales_forecast_monthly`  AS SELECT date_format(`s`.`date_created`,'%Y-%m') AS `Period`, sum(`si`.`Quantity`) AS `TotalQuantity`, round(avg(sum(`si`.`Quantity`)) OVER (ORDER BY date_format(`s`.`date_created`,'%Y-%m') ROWS BETWEEN 2 PRECEDING AND CURRENT ROW) ,2) AS `MovingAverage3` FROM (`point_of_sale`.`sales_item_test` `si` join `point_of_sale`.`sales_test` `s` on((`s`.`Sales_ID` = `si`.`Sales_ID`))) GROUP BY date_format(`s`.`date_created`,'%Y-%m') ORDER BY date_format(`s`.`date_created`,'%Y-%m') ASC ;

-- --------------------------------------------------------

--
-- Structure for view `v_store_sales_forecast_weekly`
--
DROP TABLE IF EXISTS `v_store_sales_forecast_weekly`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_store_sales_forecast_weekly`  AS SELECT yearweek(`s`.`date_created`,1) AS `Period`, sum(`si`.`Quantity`) AS `TotalQuantity`, round(avg(sum(`si`.`Quantity`)) OVER (ORDER BY yearweek(`s`.`date_created`,1) ROWS BETWEEN 2 PRECEDING AND CURRENT ROW) ,2) AS `MovingAverage3` FROM (`point_of_sale`.`sales_item_test` `si` join `point_of_sale`.`sales_test` `s` on((`s`.`Sales_ID` = `si`.`Sales_ID`))) GROUP BY yearweek(`s`.`date_created`,1) ORDER BY yearweek(`s`.`date_created`,1) ASC ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `analytics`
--
ALTER TABLE `analytics`
  ADD PRIMARY KEY (`AnalyticsID`);

--
-- Indexes for table `category_markup`
--
ALTER TABLE `category_markup`
  ADD PRIMARY KEY (`CategoryMarkup_ID`),
  ADD UNIQUE KEY `uq_category_markup` (`CategoryName`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`Customer_ID`);

--
-- Indexes for table `customersreturns`
--
ALTER TABLE `customersreturns`
  ADD PRIMARY KEY (`CReturn_ID`);

--
-- Indexes for table `daily_sales`
--
ALTER TABLE `daily_sales`
  ADD PRIMARY KEY (`DailySales_ID`),
  ADD UNIQUE KEY `uq_daily` (`PeriodStart`,`PeriodEnd`,`Product_ID`),
  ADD UNIQUE KEY `uniq_period_product` (`PeriodStart`,`PeriodEnd`,`Product_ID`);

--
-- Indexes for table `daily_total_sales`
--
ALTER TABLE `daily_total_sales`
  ADD PRIMARY KEY (`EntireDailySales_ID`),
  ADD UNIQUE KEY `unique_period` (`PeriodStart`,`PeriodEnd`);

--
-- Indexes for table `expiration`
--
ALTER TABLE `expiration`
  ADD PRIMARY KEY (`Expiration_ID`),
  ADD KEY `Product_ID` (`Product_ID`);

--
-- Indexes for table `forecast`
--
ALTER TABLE `forecast`
  ADD PRIMARY KEY (`Forecast_ID`);

--
-- Indexes for table `general_markup`
--
ALTER TABLE `general_markup`
  ADD PRIMARY KEY (`Markup_ID`);

--
-- Indexes for table `inventory_batch`
--
ALTER TABLE `inventory_batch`
  ADD PRIMARY KEY (`Batch_ID`),
  ADD UNIQUE KEY `uq_batch_number` (`BatchNum`),
  ADD KEY `fk_batch_product` (`Product_ID`),
  ADD KEY `fk_batch_restock` (`Restock_ID`),
  ADD KEY `fk_batch_supplier` (`Supplier_ID`);

--
-- Indexes for table `item_to_order`
--
ALTER TABLE `item_to_order`
  ADD PRIMARY KEY (`ItemToOrder_ID`),
  ADD KEY `fk_item_order_list` (`ListToOrder_ID`),
  ADD KEY `fk_item_order_product` (`Product_ID`);

--
-- Indexes for table `list_to_order`
--
ALTER TABLE `list_to_order`
  ADD PRIMARY KEY (`ListToOrder_ID`),
  ADD KEY `fk_list_order_supplier` (`Supplier_ID`);

--
-- Indexes for table `monthly_sales`
--
ALTER TABLE `monthly_sales`
  ADD PRIMARY KEY (`MonthlySales_ID`),
  ADD UNIQUE KEY `uq_monthly` (`PeriodStart`,`PeriodEnd`,`Product_ID`),
  ADD UNIQUE KEY `uniq_period_product` (`PeriodStart`,`PeriodEnd`,`Product_ID`);

--
-- Indexes for table `monthly_total_sales`
--
ALTER TABLE `monthly_total_sales`
  ADD PRIMARY KEY (`EntireMonthlySales_ID`),
  ADD UNIQUE KEY `unique_period` (`PeriodStart`,`PeriodEnd`);

--
-- Indexes for table `newaddition`
--
ALTER TABLE `newaddition`
  ADD PRIMARY KEY (`Inventory_ID`);

--
-- Indexes for table `pricing_audit_log`
--
ALTER TABLE `pricing_audit_log`
  ADD PRIMARY KEY (`Audit_ID`),
  ADD KEY `idx_audit_user` (`User_ID`),
  ADD KEY `idx_audit_action` (`ActionType`),
  ADD KEY `idx_audit_product` (`Product_ID`),
  ADD KEY `idx_audit_created` (`CreatedAt`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`Product_ID`);

--
-- Indexes for table `product_markup`
--
ALTER TABLE `product_markup`
  ADD PRIMARY KEY (`ProductMarkup_ID`),
  ADD UNIQUE KEY `uq_product_markup` (`Product_ID`);

--
-- Indexes for table `product_supplier`
--
ALTER TABLE `product_supplier`
  ADD PRIMARY KEY (`ProductSupplier_ID`),
  ADD UNIQUE KEY `uq_product_supplier` (`Product_ID`,`Supplier_ID`),
  ADD KEY `idx_product_supplier_supplier` (`Supplier_ID`);

--
-- Indexes for table `pulledoutitems`
--
ALTER TABLE `pulledoutitems`
  ADD PRIMARY KEY (`Pulled_ID`);

--
-- Indexes for table `restock`
--
ALTER TABLE `restock`
  ADD PRIMARY KEY (`Orestock_ID`),
  ADD KEY `Product_ID` (`Product_ID`),
  ADD KEY `Supplier_ID` (`Supplier_ID`),
  ADD KEY `fk_restock_item_to_order` (`ItemToOrder_ID`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`Order_ID`);

--
-- Indexes for table `salesaggregration`
--
ALTER TABLE `salesaggregration`
  ADD PRIMARY KEY (`Aggregation_ID`),
  ADD UNIQUE KEY `unique_aggregation` (`PeriodType`,`PeriodStart`,`Product_ID`);

--
-- Indexes for table `stock_adjustments`
--
ALTER TABLE `stock_adjustments`
  ADD PRIMARY KEY (`Adjustment_ID`);

--
-- Indexes for table `supplier`
--
ALTER TABLE `supplier`
  ADD PRIMARY KEY (`Supplier_ID`);

--
-- Indexes for table `supplierreturns`
--
ALTER TABLE `supplierreturns`
  ADD PRIMARY KEY (`SReturns_ID`);

--
-- Indexes for table `toorder`
--
ALTER TABLE `toorder`
  ADD PRIMARY KEY (`ToOrder`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`Transaction_ID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `account_activation_hash` (`account_activation_hash`);

--
-- Indexes for table `weekly_sales`
--
ALTER TABLE `weekly_sales`
  ADD PRIMARY KEY (`WeeklySales_ID`),
  ADD UNIQUE KEY `uq_weekly` (`PeriodStart`,`PeriodEnd`,`Product_ID`),
  ADD UNIQUE KEY `uniq_period_product` (`PeriodStart`,`PeriodEnd`,`Product_ID`);

--
-- Indexes for table `weekly_total_sales`
--
ALTER TABLE `weekly_total_sales`
  ADD PRIMARY KEY (`EntireWeeklySales_ID`),
  ADD UNIQUE KEY `unique_period` (`PeriodStart`,`PeriodEnd`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `analytics`
--
ALTER TABLE `analytics`
  MODIFY `AnalyticsID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `category_markup`
--
ALTER TABLE `category_markup`
  MODIFY `CategoryMarkup_ID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `Customer_ID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customersreturns`
--
ALTER TABLE `customersreturns`
  MODIFY `CReturn_ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `daily_sales`
--
ALTER TABLE `daily_sales`
  MODIFY `DailySales_ID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `daily_total_sales`
--
ALTER TABLE `daily_total_sales`
  MODIFY `EntireDailySales_ID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `expiration`
--
ALTER TABLE `expiration`
  MODIFY `Expiration_ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `forecast`
--
ALTER TABLE `forecast`
  MODIFY `Forecast_ID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory_batch`
--
ALTER TABLE `inventory_batch`
  MODIFY `Batch_ID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `item_to_order`
--
ALTER TABLE `item_to_order`
  MODIFY `ItemToOrder_ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT for table `list_to_order`
--
ALTER TABLE `list_to_order`
  MODIFY `ListToOrder_ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `monthly_sales`
--
ALTER TABLE `monthly_sales`
  MODIFY `MonthlySales_ID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `monthly_total_sales`
--
ALTER TABLE `monthly_total_sales`
  MODIFY `EntireMonthlySales_ID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `newaddition`
--
ALTER TABLE `newaddition`
  MODIFY `Inventory_ID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pricing_audit_log`
--
ALTER TABLE `pricing_audit_log`
  MODIFY `Audit_ID` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `Product_ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `product_markup`
--
ALTER TABLE `product_markup`
  MODIFY `ProductMarkup_ID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_supplier`
--
ALTER TABLE `product_supplier`
  MODIFY `ProductSupplier_ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `pulledoutitems`
--
ALTER TABLE `pulledoutitems`
  MODIFY `Pulled_ID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `restock`
--
ALTER TABLE `restock`
  MODIFY `Orestock_ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=140;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `Order_ID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `salesaggregration`
--
ALTER TABLE `salesaggregration`
  MODIFY `Aggregation_ID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock_adjustments`
--
ALTER TABLE `stock_adjustments`
  MODIFY `Adjustment_ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `supplier`
--
ALTER TABLE `supplier`
  MODIFY `Supplier_ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `supplierreturns`
--
ALTER TABLE `supplierreturns`
  MODIFY `SReturns_ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `toorder`
--
ALTER TABLE `toorder`
  MODIFY `ToOrder` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `Transaction_ID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `weekly_sales`
--
ALTER TABLE `weekly_sales`
  MODIFY `WeeklySales_ID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `weekly_total_sales`
--
ALTER TABLE `weekly_total_sales`
  MODIFY `EntireWeeklySales_ID` int NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `inventory_batch`
--
ALTER TABLE `inventory_batch`
  ADD CONSTRAINT `fk_batch_product` FOREIGN KEY (`Product_ID`) REFERENCES `product` (`Product_ID`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_batch_supplier` FOREIGN KEY (`Supplier_ID`) REFERENCES `supplier` (`Supplier_ID`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `item_to_order`
--
ALTER TABLE `item_to_order`
  ADD CONSTRAINT `fk_item_order_list` FOREIGN KEY (`ListToOrder_ID`) REFERENCES `list_to_order` (`ListToOrder_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_item_order_product` FOREIGN KEY (`Product_ID`) REFERENCES `product` (`Product_ID`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `list_to_order`
--
ALTER TABLE `list_to_order`
  ADD CONSTRAINT `fk_list_order_supplier` FOREIGN KEY (`Supplier_ID`) REFERENCES `supplier` (`Supplier_ID`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `product_markup`
--
ALTER TABLE `product_markup`
  ADD CONSTRAINT `fk_product_markup_product` FOREIGN KEY (`Product_ID`) REFERENCES `product` (`Product_ID`) ON DELETE CASCADE;

--
-- Constraints for table `product_supplier`
--
ALTER TABLE `product_supplier`
  ADD CONSTRAINT `fk_ps_product` FOREIGN KEY (`Product_ID`) REFERENCES `product` (`Product_ID`),
  ADD CONSTRAINT `fk_ps_supplier` FOREIGN KEY (`Supplier_ID`) REFERENCES `supplier` (`Supplier_ID`);

--
-- Constraints for table `restock`
--
ALTER TABLE `restock`
  ADD CONSTRAINT `fk_restock_item_to_order` FOREIGN KEY (`ItemToOrder_ID`) REFERENCES `item_to_order` (`ItemToOrder_ID`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
