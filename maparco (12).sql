-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 08, 2024 at 04:42 PM
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
-- Database: `maparco`
--

-- --------------------------------------------------------

--
-- Table structure for table `addresses`
--

CREATE TABLE `addresses` (
  `AddressID` int(11) NOT NULL,
  `CustomerID` int(11) DEFAULT NULL,
  `Description` varchar(255) NOT NULL,
  `HouseNo` varchar(255) NOT NULL,
  `Street` varchar(255) NOT NULL,
  `Barangay` varchar(255) NOT NULL,
  `City` varchar(100) NOT NULL,
  `Province` varchar(100) NOT NULL,
  `ZipCode` varchar(20) NOT NULL,
  `FullName` varchar(255) DEFAULT NULL,
  `PhoneNumber` varchar(20) DEFAULT NULL,
  `IsDefault` tinyint(1) DEFAULT 0,
  `AddedAt` datetime DEFAULT NULL,
  `UpdatedAt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `addresses`
--

INSERT INTO `addresses` (`AddressID`, `CustomerID`, `Description`, `HouseNo`, `Street`, `Barangay`, `City`, `Province`, `ZipCode`, `FullName`, `PhoneNumber`, `IsDefault`, `AddedAt`, `UpdatedAt`) VALUES
(27, 19, 'lola mating residence, white house', 'houseNo. N/A', 'Guinto', 'Ma. Parang', 'Naujan', 'Oriental Mindoro', '5204', 'Pi Jee', '0987654321', 1, '2024-04-18 10:40:34', '2024-04-18 10:40:34'),
(28, 19, 'Sto. Ipilan (Murangan) Hatulan Green Bhouse', 'houseNo. N/A', 'Murangan', 'Alcate', 'Victoria', 'Oriental Mindoro', '5205', 'Shi Rena', '0987654321', 0, '2024-04-18 10:41:00', '2024-04-18 10:41:00'),
(30, 19, 'Rabino Family', 'houseNo. N/A', 'Guinto', 'Ma. Parang', 'Naujan', 'Oriental Mindoro', '5204', 'Potpotan', '0987654321', 0, '2024-04-18 11:15:20', '2024-04-18 11:15:20'),
(35, 0, 'Sto. Ipilan (Murangan) Hatulan Green Bhouse', 'houseNo. N/A', 'Guinto', 'Ma. Parang', 'Naujan', 'Oriental Mindoro', '5205', 'Patrick James V. Tapas', '0987654321', 1, '2024-04-21 14:41:57', '2024-04-21 14:41:57'),
(37, 21, 'lola mating residence, white house', 'N/A', 'Ipilan', 'Murangan', 'Victoria', 'Or. Mdo.', '5204', 'Patrick James V. Tapas', '0987654321', 1, '2024-04-21 17:31:41', '2024-04-21 17:31:41');

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `ID` int(11) NOT NULL,
  `Username` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Full_Name` varchar(255) NOT NULL,
  `Is_Admin` tinyint(1) NOT NULL DEFAULT 0,
  `photo` varchar(200) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`ID`, `Username`, `Password`, `Email`, `Full_Name`, `Is_Admin`, `photo`) VALUES
(2, 'admin', '$2y$10$X/Uev8FzP0uqYJx7AnLpredkTaS1u4Q3Sqd9sngp./WgjlxEPeMnW', 'admin@gmail.com', 'Patrick James', 1, 'MAPARCO-logo.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `CartID` int(11) NOT NULL,
  `CustomerID` int(11) DEFAULT NULL,
  `ProductID` int(11) DEFAULT NULL,
  `Quantity` int(11) DEFAULT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `IsDeleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`CartID`, `CustomerID`, `ProductID`, `Quantity`, `CreatedAt`, `IsDeleted`) VALUES
(73, 1, 21, 1, '2024-04-18 08:41:47', 0),
(74, 1, 22, 1, '2024-04-29 15:02:26', 0),
(75, 1, 20, 1, '2024-04-30 09:56:40', 0),
(76, 1, 20, 1, '2024-05-02 08:36:24', 0),
(77, 1, 20, 1, '2024-05-02 09:05:37', 0);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `CategoryID` int(11) NOT NULL,
  `CategoryName` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`CategoryID`, `CategoryName`) VALUES
(2, 'Shirt'),
(3, 'Pants');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `CustomerID` int(11) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Address` varchar(255) NOT NULL,
  `Phone` varchar(20) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `create_on` timestamp NULL DEFAULT NULL,
  `ProfilePicFilename` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`CustomerID`, `Name`, `Email`, `Address`, `Phone`, `Password`, `create_on`, `ProfilePicFilename`) VALUES
(19, 'Patrick James V. Tapas', 'pijee@gmail.com', '', '', '$2y$10$UN6elCQO1IRAAEdQN2p.VegegCDzGC2Uds/QKUgWDoclxEyZ8gF3W', '2024-04-18 02:39:59', '2x2-3.jpg'),
(20, 'qwerty', 'qwe@gmail.com', '', '', '$2y$10$BUG7xtejI7noT0JjzHueouc9cr0EqYGuinBCtDjnC84p4Muoh36b2', '2024-04-18 03:30:45', NULL),
(21, 'Patrick James', 'tapaspatrickjames@gmail.com', '', '', '$2y$10$YJ6ZO.QZ8oEyxj1eVqgoBuNc4X/2DhSSTPhS3qtJqj325jF3.kmvu', '2024-04-20 08:35:37', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `orderitems`
--

CREATE TABLE `orderitems` (
  `OrderItemID` int(11) NOT NULL,
  `OrderID` int(11) DEFAULT NULL,
  `ProductID` int(11) DEFAULT NULL,
  `Quantity` int(11) DEFAULT NULL,
  `Price` decimal(10,2) DEFAULT NULL,
  `Subtotal` decimal(10,2) DEFAULT NULL,
  `ProductName` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orderitems`
--

INSERT INTO `orderitems` (`OrderItemID`, `OrderID`, `ProductID`, `Quantity`, `Price`, `Subtotal`, `ProductName`) VALUES
(47, 47, 21, 1, 300.00, 300.00, 'Buenas Nata De Coco '),
(48, 48, 21, 1, 300.00, 300.00, 'Buenas Nata De Coco '),
(49, 49, 21, 1, 300.00, 300.00, 'Buenas Nata De Coco '),
(50, 50, 21, 1, 300.00, 300.00, 'Buenas Nata De Coco '),
(51, 51, 20, 1, 250.00, 250.00, 'Buenas Nata De Coco '),
(52, 52, 21, 1, 300.00, 300.00, 'Buenas Nata De Coco '),
(53, 53, 21, 1, 300.00, 300.00, 'Buenas Nata De Coco ');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `OrderID` int(11) NOT NULL,
  `CustomerID` int(11) DEFAULT NULL,
  `OrderDate` date DEFAULT NULL,
  `TotalAmount` decimal(10,2) NOT NULL,
  `OrderStatus` varchar(50) NOT NULL,
  `PaymentStatus` varchar(50) DEFAULT NULL,
  `ShippingAddress` varchar(255) DEFAULT NULL,
  `AddressID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`OrderID`, `CustomerID`, `OrderDate`, `TotalAmount`, `OrderStatus`, `PaymentStatus`, `ShippingAddress`, `AddressID`) VALUES
(47, 19, '2024-04-18', 300.00, 'Delivered', 'Paid', 'lola mating residence, white house, houseNo. N/A, Guinto, Ma. Parang, Naujan, Oriental Mindoro, 5205', 27),
(48, 19, '2024-04-18', 300.00, 'Delivered', 'Paid', 'lola mating residence, white house, houseNo. N/A, Guinto, Ma. Parang, Naujan, Oriental Mindoro, 5205', 27),
(49, 19, '2024-04-18', 300.00, 'Delivered', 'Paid', 'Sto. Ipilan (Murangan) Hatulan Green Bhouse, houseNo. N/A, Murangan, Alcate, Victoria, Oriental Mindoro, 5205', 28),
(50, 19, '2024-04-18', 300.00, 'Delivered', 'Paid', 'Rabino Family, houseNo. N/A, Guinto, Ma. Parang, Naujan, Oriental Mindoro, 5204', 30),
(51, 19, '2024-02-01', 250.00, 'Delivered', 'Paid', 'lola mating residence, white house, houseNo. N/A, Guinto, Ma. Parang, Naujan, Oriental Mindoro, 5204', 27),
(52, 19, '2024-05-03', 300.00, 'Delivered', 'Paid', 'lola mating residence, white house, houseNo. N/A, Guinto, Ma. Parang, Naujan, Oriental Mindoro, 5204', 27),
(53, 19, '2024-05-03', 300.00, 'Delivered', 'Paid', 'lola mating residence, white house, houseNo. N/A, Guinto, Ma. Parang, Naujan, Oriental Mindoro, 5204', 27);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `ProductID` int(11) NOT NULL,
  `ProductName` varchar(255) NOT NULL,
  `Photo` varchar(200) NOT NULL,
  `Description` text DEFAULT NULL,
  `ProductDetails` text DEFAULT NULL,
  `Price` decimal(10,2) NOT NULL,
  `QuantityAvailable` int(11) NOT NULL,
  `CategoryID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`ProductID`, `ProductName`, `Photo`, `Description`, `ProductDetails`, `Price`, `QuantityAvailable`, `CategoryID`) VALUES
(19, 'Natta De Coco 70', 'uploads/8.jpg', 'Coconut Gel in Syrup 70th', NULL, 700.00, 80, NULL),
(20, 'Buenas Nata De Coco ', 'uploads/4.jpg', 'Coconut Gel in Syrup (320 g)', NULL, 250.00, 50, NULL),
(21, 'Buenas Nata De Coco ', 'uploads/3.jpg', 'Net WT. 12 oz (340 g)', NULL, 300.00, 86, NULL),
(22, 'CDO Natta De Coco', 'uploads/2.jpg', 'Coconut Gel in Syrup (200 g)', NULL, 98.00, 23, NULL),
(23, 'Inaco Nata de Coco', 'uploads/1.jpg', 'Salam Syrup (50 g)', NULL, 50.00, 45, NULL),
(24, 'Monika Brand', 'uploads/5.jpg', 'Nata de Coco EXTRA HEAVY SYRUP (340 G)', NULL, 250.00, 82, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `addresses`
--
ALTER TABLE `addresses`
  ADD PRIMARY KEY (`AddressID`);

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`CartID`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`CategoryID`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`CustomerID`);

--
-- Indexes for table `orderitems`
--
ALTER TABLE `orderitems`
  ADD PRIMARY KEY (`OrderItemID`),
  ADD KEY `OrderID` (`OrderID`),
  ADD KEY `fk_orderitems_products` (`ProductID`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`OrderID`),
  ADD KEY `CustomerID` (`CustomerID`),
  ADD KEY `AddressID` (`AddressID`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`ProductID`),
  ADD KEY `fk_CategoryID` (`CategoryID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `addresses`
--
ALTER TABLE `addresses`
  MODIFY `AddressID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `CartID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `CategoryID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `CustomerID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `orderitems`
--
ALTER TABLE `orderitems`
  MODIFY `OrderItemID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `OrderID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `ProductID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orderitems`
--
ALTER TABLE `orderitems`
  ADD CONSTRAINT `fk_orderitems_orders` FOREIGN KEY (`OrderID`) REFERENCES `orders` (`OrderID`),
  ADD CONSTRAINT `fk_orderitems_products` FOREIGN KEY (`ProductID`) REFERENCES `products` (`ProductID`),
  ADD CONSTRAINT `orderitems_ibfk_1` FOREIGN KEY (`OrderID`) REFERENCES `orders` (`OrderID`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_customer_id` FOREIGN KEY (`CustomerID`) REFERENCES `customers` (`CustomerID`),
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`CustomerID`) REFERENCES `customers` (`CustomerID`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`AddressID`) REFERENCES `addresses` (`AddressID`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_CategoryID` FOREIGN KEY (`CategoryID`) REFERENCES `categories` (`CategoryID`),
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`CategoryID`) REFERENCES `categories` (`CategoryID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
