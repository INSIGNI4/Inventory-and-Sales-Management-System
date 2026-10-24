-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 09, 2026 at 06:50 PM
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
-- Table structure for table `restock`
--

CREATE TABLE `restock` (
  `Orestock_ID` int NOT NULL,
  `ItemToOrder_ID` int DEFAULT NULL,
  `Type` enum('New','Re-Order') NOT NULL,
  `Quantity` int NOT NULL DEFAULT '0',
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

INSERT INTO `restock` (`Orestock_ID`, `ItemToOrder_ID`, `Type`, `Quantity`, `OrderDate`, `Expected_Receive_Date`, `Product_ID`, `Supplier_ID`, `Status`, `Image`, `DeliveryStatus`, `Date_Received`, `TotalReceived`, `withIssue`, `ExpirationDate`) VALUES
(1, 1, 'Re-Order', 40, '2026-09-24 06:43:37', NULL, 1, 1, 'Received', NULL, NULL, '2026-09-24 06:45:18', 40, 0, '2026-10-24'),
(2, 2, 'Re-Order', 40, '2026-09-24 06:43:37', NULL, 2, 1, 'Received', NULL, NULL, '2026-09-24 06:45:52', 40, 0, '2026-10-24'),
(4, 3, 'Re-Order', 15, '2026-09-24 06:43:42', NULL, 3, 2, 'Received', NULL, NULL, '2026-09-24 06:48:31', 15, 0, '2026-10-24'),
(5, 4, 'Re-Order', 10, '2026-09-24 06:43:42', NULL, 4, 2, 'Received', NULL, NULL, '2026-09-24 06:48:52', 10, 0, '2026-10-24'),
(7, 5, 'Re-Order', 12, '2026-09-24 06:43:47', NULL, 5, 3, 'Received', NULL, NULL, '2026-09-24 06:49:07', 12, 0, '2026-10-24'),
(8, 6, 'Re-Order', 12, '2026-09-24 06:43:47', NULL, 6, 3, 'Received', NULL, NULL, '2026-09-24 06:49:27', 12, 0, '2026-10-24'),
(10, 7, 'Re-Order', 12, '2026-09-24 06:43:52', NULL, 7, 4, 'Received', NULL, NULL, '2026-09-24 06:49:47', 12, 0, '2026-10-24'),
(11, 8, 'Re-Order', 12, '2026-09-24 06:43:52', NULL, 8, 4, 'Received', NULL, NULL, '2026-09-24 06:51:01', 12, 0, '2026-10-24'),
(13, 9, 'Re-Order', 30, '2026-09-24 06:43:56', NULL, 9, 5, 'Received', NULL, NULL, '2026-09-24 06:51:56', 30, 0, '2026-10-24'),
(14, 10, 'Re-Order', 40, '2026-09-24 06:43:56', NULL, 10, 5, 'Received', NULL, NULL, '2026-09-24 06:52:10', 40, 0, '2026-10-24'),
(16, 11, 'Re-Order', 40, '2026-09-24 06:44:01', NULL, 11, 6, 'Received', NULL, NULL, '2026-09-24 06:52:24', 40, 0, '2026-10-24'),
(17, 12, 'Re-Order', 16, '2026-09-24 06:44:01', NULL, 12, 6, 'Received', NULL, NULL, '2026-09-24 06:52:38', 16, 0, '2026-10-24'),
(19, 13, 'Re-Order', 40, '2026-09-24 06:44:08', NULL, 13, 7, 'Received', NULL, NULL, '2026-09-24 06:52:53', 40, 0, '2026-10-24'),
(20, 14, 'Re-Order', 24, '2026-09-24 06:44:08', NULL, 14, 7, 'Received', NULL, NULL, '2026-09-24 06:53:08', 24, 0, '2026-10-24'),
(22, 15, 'Re-Order', 24, '2026-09-24 06:44:13', NULL, 15, 8, 'Partially-Received', NULL, 'Early', '2026-09-24 07:22:37', 12, 0, '2026-10-24'),
(23, 16, 'Re-Order', 40, '2026-09-24 06:44:13', NULL, 16, 8, 'Received', NULL, 'Early', '2026-09-24 07:24:42', 40, 0, '2026-10-24'),
(25, 15, 'Re-Order', 12, '2026-09-24 07:22:37', NULL, 15, 8, 'Backorder-Fulfillment', NULL, 'Early', '2026-09-24 07:23:31', 12, 0, '2026-10-24'),
(26, 17, 'Re-Order', 40, '2026-09-24 07:26:13', NULL, 1, 1, 'Received', NULL, 'Delayed', '2026-09-24 07:26:38', 40, 0, '2026-11-24'),
(27, NULL, 'New', 12, '2026-10-07 05:50:31', NULL, 1, 1, 'Requested', NULL, NULL, NULL, 2, 0, NULL),
(28, NULL, 'New', 12, '2026-10-07 05:54:49', NULL, 2, 1, 'Requested', NULL, NULL, NULL, 0, 0, NULL),
(29, NULL, 'New', 12, '2026-10-07 08:04:33', NULL, 1, 2, 'Requested', NULL, NULL, NULL, 0, 0, NULL),
(30, NULL, 'New', 12, '2026-10-07 13:28:49', NULL, 17, 1, 'Requested', NULL, NULL, NULL, 0, 0, NULL),
(31, NULL, 'New', 12, '2026-10-07 13:29:33', NULL, 17, 2, 'Requested', NULL, NULL, NULL, 0, 0, NULL),
(32, NULL, 'New', 12, '2026-10-07 13:29:33', NULL, 17, 2, 'Requested', NULL, NULL, NULL, 0, 0, NULL),
(33, NULL, 'New', 12, '2026-10-07 14:00:24', NULL, 17, 8, 'Requested', NULL, NULL, NULL, 0, 0, NULL),
(34, NULL, 'New', 12, '2026-10-07 14:00:24', NULL, 17, 7, 'Requested', NULL, NULL, NULL, 0, 0, NULL),
(35, NULL, 'New', 12, '2026-10-07 14:00:24', NULL, 17, 7, 'Requested', NULL, NULL, NULL, 0, 0, NULL),
(36, NULL, 'New', 12, '2026-10-07 14:01:08', NULL, 17, 1, 'Requested', NULL, NULL, NULL, 0, 0, NULL),
(37, NULL, 'New', 12, '2026-10-07 14:01:08', NULL, 17, 1, 'Requested', NULL, NULL, NULL, 12, 0, '2026-10-22'),
(38, NULL, 'New', 40, '2026-10-07 15:01:20', NULL, 16, 1, 'Requested', NULL, NULL, NULL, 0, 0, NULL),
(39, NULL, 'New', 40, '2026-10-07 15:01:20', NULL, 13, 2, 'Requested', NULL, NULL, NULL, 0, 0, NULL),
(40, NULL, 'New', 12, '2026-10-07 15:01:20', NULL, 5, 1, 'Requested', NULL, NULL, NULL, 0, 0, NULL),
(41, NULL, 'New', 30, '2026-10-07 15:01:20', NULL, 9, 3, 'Requested', NULL, NULL, NULL, 0, 0, NULL),
(42, NULL, 'New', 12, '2026-10-07 15:01:20', NULL, 6, 2, 'Requested', NULL, NULL, NULL, 0, 0, NULL),
(43, NULL, 'New', 40, '2026-10-07 15:13:21', NULL, 16, 1, 'Requested', NULL, NULL, NULL, 0, 0, NULL),
(44, NULL, 'New', 40, '2026-10-07 15:13:21', NULL, 13, 2, 'Requested', NULL, NULL, NULL, 40, 0, NULL),
(45, NULL, 'New', 40, '2026-10-07 15:13:21', NULL, 10, 3, 'Requested', NULL, NULL, NULL, 40, 0, NULL),
(46, NULL, 'New', 24, '2026-10-07 15:13:21', NULL, 14, 1, 'Partially-Received', NULL, NULL, '2026-10-08 03:11:51', 24, 0, NULL),
(48, NULL, 'New', 12, '2026-10-08 03:34:33', NULL, 17, 1, 'Received', NULL, NULL, NULL, 12, 0, NULL),
(49, NULL, 'New', 40, '2026-10-08 03:36:52', NULL, 1, 1, 'Partially-Received', NULL, NULL, '2026-10-08 03:37:09', 20, 0, NULL),
(50, 73, 'Re-Order', 12, '2026-10-08 04:25:05', NULL, 17, 2, 'Requested', NULL, NULL, NULL, 12, 0, NULL),
(51, 74, 'Re-Order', 40, '2026-10-08 04:38:00', NULL, 2, 2, 'Received', NULL, NULL, NULL, 40, 0, NULL),
(52, 75, 'Re-Order', 40, '2026-10-08 04:38:00', NULL, 2, 2, 'Partially-Received', NULL, NULL, NULL, 20, 0, NULL),
(54, 75, 'Re-Order', 20, '2026-10-08 04:56:08', NULL, 2, 2, 'Backorder-Fulfillment', NULL, 'Early', '2026-10-08 04:57:19', 20, 0, NULL),
(55, 76, 'Re-Order', 12, '2026-10-08 04:58:48', NULL, 17, 1, 'Received', NULL, 'Early', '2026-10-08 04:59:04', 12, 0, NULL),
(56, NULL, 'New', 12, '2026-10-08 06:13:05', NULL, 17, 1, 'Received', NULL, NULL, '2026-10-08 07:01:15', 12, 0, NULL),
(57, NULL, 'New', 12, '2026-10-08 07:28:18', NULL, 17, 1, 'Received-With-Issue', NULL, NULL, '2026-10-08 07:52:33', 10, 2, NULL),
(58, NULL, 'New', 80, '2026-10-08 11:53:25', '2026-10-11', 4, 2, 'Requested', NULL, NULL, NULL, 0, 0, NULL),
(59, NULL, 'New', 24, '2026-10-08 11:53:25', '2026-10-12', 7, 4, 'Requested', NULL, NULL, NULL, 0, 0, NULL),
(60, NULL, 'New', 30, '2026-10-08 11:53:25', '2026-10-11', 3, 2, 'Requested', NULL, NULL, NULL, 0, 0, NULL),
(61, NULL, 'New', 16, '2026-10-08 11:53:25', '2026-10-13', 12, 6, 'Requested', NULL, NULL, NULL, 0, 0, NULL),
(62, NULL, 'New', 40, '2026-10-08 11:53:25', '2026-10-13', 11, 6, 'Received', NULL, NULL, '2026-10-08 12:49:39', 40, 0, NULL),
(63, NULL, 'New', 600, '2026-10-08 12:50:35', NULL, 9, 1, 'Received', NULL, NULL, '2026-10-08 12:51:54', 600, 0, NULL),
(64, NULL, 'New', 30, '2026-10-08 12:52:37', NULL, 9, 1, 'Received', NULL, NULL, '2026-10-08 12:52:51', 30, 0, NULL),
(65, NULL, 'New', 40, '2026-10-08 13:07:14', NULL, 1, 1, 'Partially-Received', NULL, NULL, '2026-10-08 13:07:28', 20, 0, NULL),
(66, NULL, 'New', 12, '2026-10-08 13:14:48', NULL, 7, 1, 'Partially-Received', NULL, NULL, '2026-10-08 13:15:05', 6, 0, NULL),
(67, NULL, 'New', 6, '2026-10-08 13:15:05', NULL, 7, 1, 'Received', NULL, NULL, '2026-10-08 13:16:22', 6, 0, NULL),
(68, NULL, 'New', 12, '2026-10-08 16:00:42', '2026-10-15', 17, 1, 'Received', NULL, NULL, '2026-10-08 16:01:04', 12, 0, '2027-01-09'),
(72, NULL, 'New', 40, '2026-10-09 15:52:05', '2026-10-15', 16, 8, 'Requested', NULL, NULL, NULL, 0, 0, NULL),
(73, NULL, 'New', 40, '2026-10-09 15:52:34', '2026-10-20', 16, 4, 'Requested', NULL, NULL, NULL, 0, 0, NULL),
(74, NULL, 'New', 40, '2026-10-09 15:52:34', '2026-10-13', 13, 3, 'Requested', NULL, NULL, NULL, 0, 0, NULL),
(76, NULL, 'New', 40, '2026-10-09 15:54:28', '2026-10-13', 16, 8, 'Requested', NULL, NULL, NULL, 0, 0, NULL),
(77, NULL, 'New', 40, '2026-10-09 15:54:28', '2026-10-13', 13, 3, 'Requested', NULL, NULL, NULL, 0, 0, NULL),
(80, NULL, 'New', 40, '2026-10-09 16:07:36', '2026-10-15', 16, 1, 'Partially-Received', NULL, NULL, '2026-10-09 16:11:55', 20, 10, NULL),
(81, NULL, 'New', 40, '2026-10-09 16:08:22', '2026-10-13', 16, 1, 'Received-With-Issue', NULL, NULL, '2026-10-09 16:11:31', 20, 20, NULL),
(82, NULL, 'New', 40, '2026-10-09 16:08:22', '2026-10-22', 13, 4, 'Partially-Received', NULL, NULL, '2026-10-09 16:10:41', 10, 0, NULL),
(83, NULL, 'New', 24, '2026-10-09 16:08:22', '2026-10-14', 14, 2, 'Received', NULL, NULL, '2026-10-09 16:10:15', 24, 0, NULL),
(84, NULL, 'New', 30, '2026-10-09 16:10:41', '2026-10-22', 13, 4, 'Received', NULL, NULL, '2026-10-09 16:11:02', 30, 0, NULL),
(85, NULL, 'New', 10, '2026-10-09 16:11:55', '2026-10-15', 16, 1, 'Received', NULL, NULL, '2026-10-09 16:12:52', 10, 0, NULL),
(86, NULL, 'New', 40, '2026-10-09 16:16:39', '2026-10-14', 16, 8, 'Requested', NULL, NULL, NULL, 0, 0, NULL),
(87, NULL, 'New', 40, '2026-10-09 16:16:39', '2026-10-14', 13, 3, 'Requested', NULL, NULL, NULL, 0, 0, NULL),
(88, NULL, 'New', 40, '2026-10-09 16:16:51', '2026-10-15', 11, 1, 'Requested', NULL, NULL, NULL, 0, 0, NULL),
(89, NULL, 'New', 40, '2026-10-09 16:18:04', '2026-10-13', 16, 1, 'Requested', NULL, NULL, NULL, 0, 0, NULL),
(90, NULL, 'New', 24, '2026-10-09 16:18:04', '2026-10-13', 14, 1, 'Requested', NULL, NULL, NULL, 0, 0, NULL),
(91, NULL, 'New', 40, '2026-10-09 16:18:04', '2026-10-22', 13, 2, 'Requested', NULL, NULL, NULL, 0, 0, NULL),
(92, NULL, 'New', 40, '2026-10-09 16:26:52', '2026-10-11', 16, 1, 'P-R-With-Issue', NULL, NULL, '2026-10-09 16:44:32', 20, 10, NULL),
(94, NULL, 'New', 24, '2026-10-09 16:26:52', '2026-10-21', 14, 6, 'Partially-Received', NULL, NULL, '2026-10-09 16:27:46', 12, 0, NULL),
(95, NULL, 'New', 40, '2026-10-09 16:26:52', '2026-10-21', 10, 8, 'Received-With-Issue', NULL, NULL, '2026-10-09 16:27:30', 30, 10, NULL),
(96, NULL, 'New', 24, '2026-10-09 16:26:52', '2026-10-12', 15, 2, 'Received', NULL, NULL, '2026-10-09 16:27:08', 24, 0, '2026-10-12'),
(97, NULL, 'New', 12, '2026-10-09 16:27:46', '2026-10-21', 14, 6, 'Received', NULL, NULL, '2026-10-09 16:28:00', 12, 0, NULL),
(98, NULL, 'New', 10, '2026-10-09 16:36:47', '2026-10-13', 13, 3, 'Received', NULL, NULL, '2026-10-09 16:41:16', 10, 0, NULL),
(99, NULL, 'New', 10, '2026-10-09 16:44:32', '2026-10-11', 16, 1, 'Backorder-Fulfillment', NULL, NULL, NULL, 0, 0, NULL);

--
-- Triggers `restock`
--
DELIMITER $$
CREATE TRIGGER `restock_status_received` BEFORE UPDATE ON `restock` FOR EACH ROW BEGIN

    DECLARE v_ExpectedReceiveDate DATE;


    /* =========================================================
       1. SET DATE RECEIVED WHEN ACTUAL RECEIVED QUANTITY
          INCREASES
       ========================================================= */

    IF IFNULL(NEW.TotalReceived, 0) >
       IFNULL(OLD.TotalReceived, 0)
    THEN

        SET NEW.Date_Received = CURRENT_TIMESTAMP;

    END IF;


    /* =========================================================
       2. VERSION 2:
          GET EXPECTED RECEIVE DATE FROM LIST_TO_ORDER
       ========================================================= */

    IF NEW.ItemToOrder_ID IS NOT NULL
       AND NEW.Date_Received IS NOT NULL
    THEN

        SELECT l.Expected_Receive_Date
        INTO v_ExpectedReceiveDate
        FROM item_to_order i
        INNER JOIN list_to_order l
            ON l.ListToOrder_ID = i.ListToOrder_ID
        WHERE i.ItemToOrder_ID = NEW.ItemToOrder_ID
        LIMIT 1;


        /* =====================================================
           3. CALCULATE DELIVERY STATUS
           ===================================================== */

        IF v_ExpectedReceiveDate IS NOT NULL THEN

            IF DATE(NEW.Date_Received) < v_ExpectedReceiveDate THEN

                SET NEW.DeliveryStatus = 'Early';

            ELSEIF DATE(NEW.Date_Received) =
                   v_ExpectedReceiveDate THEN

                SET NEW.DeliveryStatus = 'On-Time';

            ELSE

                SET NEW.DeliveryStatus = 'Delayed';

            END IF;

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

    /* =========================================================
       1. VALIDATE RECEIVED + ISSUE QUANTITY

       TotalReceived = GOOD products
       withIssue     = DEFECTIVE products

       Together they cannot exceed the ordered quantity.
       ========================================================= */

    IF IFNULL(NEW.TotalReceived, 0) < 0
       OR IFNULL(NEW.withIssue, 0) < 0
    THEN

        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT =
            'Received quantity and issue quantity cannot be negative.';

    END IF;


    IF (
        IFNULL(NEW.TotalReceived, 0)
        + IFNULL(NEW.withIssue, 0)
    ) > IFNULL(NEW.Quantity, 0)
    THEN

        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT =
            'TotalReceived + withIssue cannot exceed Quantity.';

    END IF;


    /* =========================================================
       2. CALCULATE CHANGE IN GOOD PRODUCTS

       Only the change in good products affects inventory.
       ========================================================= */

    SET v_ReceivedDiff =
        IFNULL(NEW.TotalReceived, 0)
        - IFNULL(OLD.TotalReceived, 0);


    /* =========================================================
       3. AUTOMATIC RESTOCK STATUS

       P-R-With Issue:
       Partially accounted for, with defective products reported.

       Partially-Received:
       Partially accounted for, with no defective products.

       Received-With-Issue:
       Fully accounted for, with defective products reported.
       ========================================================= */

    SET v_TotalAccounted =
        IFNULL(NEW.TotalReceived, 0)
        + IFNULL(NEW.withIssue, 0);


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


    /* =========================================================
       4. UPDATE ITEM_TO_ORDER

       Only GOOD products count toward Received_Quantity.
       ========================================================= */

    IF NEW.ItemToOrder_ID IS NOT NULL
       AND v_ReceivedDiff <> 0
    THEN

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

            Item_Status =
                CASE
                    WHEN v_NewReceivedTotal >=
                         IFNULL(Ordered_Quantity, 0)
                    THEN 'Received'

                    ELSE 'Partially-Received'
                END

        WHERE ItemToOrder_ID = NEW.ItemToOrder_ID;

    END IF;


    /* =========================================================
       5. UPDATE PRODUCT INVENTORY

       ONLY GOOD PRODUCTS are added to inventory.
       ========================================================= */

    IF v_ReceivedDiff <> 0 THEN

        UPDATE product
        SET
            UnitsOrdered =
                GREATEST(
                    IFNULL(UnitsOrdered, 0) + v_ReceivedDiff,
                    0
                )
        WHERE Product_ID = NEW.Product_ID;


        /* Calculate current inventory */

        SELECT
            IFNULL(UnitsOrdered, 0) - IFNULL(UnitSold, 0)
        INTO v_NewInventory
        FROM product
        WHERE Product_ID = NEW.Product_ID;


        /* Determine inventory status */

        SET new_status =
            CASE
                WHEN v_NewInventory > 10
                    THEN 'IN-STOCK'

                WHEN v_NewInventory > 0
                    THEN 'LOW-STOCK'

                ELSE 'OUT-OF-STOCK'
            END;


        /* Update inventory table */

        UPDATE inventory
        SET
            UnitIN = COALESCE(
                NEW.Date_Received,
                CURRENT_TIMESTAMP
            ),

            Inventory = v_NewInventory,

            Status = new_status,

            ExpirationDate =
                CASE
                    WHEN ExpirationDate IS NULL
                        THEN NEW.ExpirationDate

                    WHEN NEW.ExpirationDate IS NULL
                        THEN ExpirationDate

                    WHEN NEW.ExpirationDate < ExpirationDate
                        THEN NEW.ExpirationDate

                    ELSE ExpirationDate
                END

        WHERE Product_ID = NEW.Product_ID;


        /* Update product supplier only when:
           1. Delivery was On-Time
           2. There were no defective products
        */

        IF NEW.DeliveryStatus = 'On-Time'
           AND IFNULL(NEW.withIssue, 0) = 0
        THEN

            UPDATE product
            SET Supplier_ID = NEW.Supplier_ID
            WHERE Product_ID = NEW.Product_ID;

        END IF;

    END IF;


    /* =========================================================
       6. CREATE / UPDATE EXPIRATION BATCH

       ONLY GOOD PRODUCTS enter expiration inventory.
       Defective products do NOT enter expiration.
       ========================================================= */

    IF v_ReceivedDiff > 0
       AND NEW.ExpirationDate IS NOT NULL
    THEN

        SELECT
            COUNT(*) + 1
        INTO v_BatchCount
        FROM expiration
        WHERE Product_ID = NEW.Product_ID;


        SET v_BatchNum =
            CONCAT(
                'BATCH-',
                NEW.Product_ID,
                '-',
                LPAD(v_BatchCount, 3, '0')
            );


        /* If the expiration date already exists, add quantity. */

        IF EXISTS (
            SELECT 1
            FROM expiration
            WHERE Product_ID = NEW.Product_ID
              AND ExpirationDate = NEW.ExpirationDate
        )
        THEN

            UPDATE expiration
            SET
                Quantity = GREATEST(
                    IFNULL(Quantity, 0) + v_ReceivedDiff,
                    0
                )
            WHERE Product_ID = NEW.Product_ID
              AND ExpirationDate = NEW.ExpirationDate;


        /* Otherwise, create a new expiration batch. */

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

--
-- Indexes for dumped tables
--

--
-- Indexes for table `restock`
--
ALTER TABLE `restock`
  ADD PRIMARY KEY (`Orestock_ID`),
  ADD KEY `Product_ID` (`Product_ID`),
  ADD KEY `Supplier_ID` (`Supplier_ID`),
  ADD KEY `fk_restock_item_to_order` (`ItemToOrder_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `restock`
--
ALTER TABLE `restock`
  MODIFY `Orestock_ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `restock`
--
ALTER TABLE `restock`
  ADD CONSTRAINT `fk_restock_item_to_order` FOREIGN KEY (`ItemToOrder_ID`) REFERENCES `item_to_order` (`ItemToOrder_ID`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
