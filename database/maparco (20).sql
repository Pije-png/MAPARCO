-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 14, 2024 at 09:48 AM
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
(45, 29, 'lola mating residence, white house', 'N/A', 'Guinto', 'Ma. Parang', 'Naujan', 'Mindoro Oriental', '5204', 'Patrick James Villanueva Tapas', '09167466766', 1, '2024-09-06 17:33:32', '2024-09-06 17:33:32'),
(46, 30, 'j', 'j', 'j', 'j', 'j', 'j', 'j', 'Coco Martin', 'j', 1, '2024-09-14 08:40:17', '2024-09-14 08:40:17'),
(47, 32, 'p', 'p', 'p', 'p', 'p', 'p', 'p', 'John Doe', 'p', 0, '2024-09-26 06:39:59', '2024-09-26 06:39:59'),
(49, 32, '5', '5', '5', '5', '5', '5', '5', '5', '5', 0, '2024-10-01 10:52:07', '2024-10-01 10:52:07'),
(50, 32, '5', '5', '55', '5', '55', '5', '5', '5', '5', 0, '2024-10-01 11:08:33', '2024-10-01 11:08:33');

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
(2, 'admin', '$2y$10$X/Uev8FzP0uqYJx7AnLpredkTaS1u4Q3Sqd9sngp./WgjlxEPeMnW', 'admin@gmail.com', 'Patrick James', 1, 'management/uploads/PIJEE 1x1.png');

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
(88, 28, 19, 1, '2024-09-06 14:58:25', 0);

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
(28, 'John Doe', 'Johndoe@gmail.com', '', '', '$2y$10$nbbpz.ZjEBpepAkRKD/3n.VlvcBRUmFKsqFvglgkHajpDRSlbHziu', '2024-09-06 08:58:04', 'image-removebg-preview.png'),
(29, 'Patrick James V. Tapas', 'tapaspatrickjames@gmail.com', '', '', '$2y$10$n6x.hBr9yZA6ZZ9rnReUeuOJzVVDoltiU.z/.3UjV.489Y/ABaf9e', '2024-09-06 09:33:04', NULL),
(30, 'AKO SI PIJE', 'pijee@gmail.com', '', '', '$2y$10$mOXXHPpMdrdybtef/t0sF.ziXyieckXeofHBh0lVxe92tO9wxGXYm', '2024-09-14 00:40:01', NULL),
(31, 'Patrick James', 'tapaspatrickjames@gmail.com', '', '', '$2y$10$IHP1uvqD5k8ByMVrkuXmE.vhnKKstY3cX2D3rG31gy.K8hn/iJTD6', '2024-09-25 22:39:10', NULL),
(32, 'Patrick James', 'tapas@gmail.com', '', '', '$2y$10$pAcAC/lWF6NvtkbkGMXaLuEqBQGMq7brH2K3c/QjDmAtsqyeDpFcC', '2024-09-25 22:39:42', 'mr3.png'),
(33, 'Jack Frost', 'jack@gmail.com', '', '', '$2y$10$.FA3wGlRwB6qCwp2SIot8eTJfNt2VKoLEdpEctBJ/7LzQ9m6BUMrO', '2024-10-02 00:35:06', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `message_text` text NOT NULL,
  `status` int(11) DEFAULT 1,
  `timestamp` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(84, 84, 21, 1, 300.00, 300.00, 'Buenas Nata De Coco '),
(85, 85, 21, 1, 300.00, 300.00, 'Buenas Nata De Coco '),
(86, 86, 21, 1, 300.00, 300.00, 'Buenas Nata De Coco '),
(87, 87, 21, 1, 300.00, 300.00, 'Buenas Nata De Coco '),
(88, 88, 21, 1, 300.00, 300.00, 'Buenas Nata De Coco '),
(89, 89, 20, 5, 250.00, 1250.00, 'Buenas Nata De Coco '),
(90, 90, 22, 3, 98.00, 294.00, 'CDO Natta De Coco Green'),
(91, 91, 23, 2, 50.00, 100.00, 'Inaco Nata de Coco'),
(92, 92, 19, 1, 700.00, 700.00, 'Natta De Coco 70'),
(93, 93, 19, 1, 700.00, 700.00, 'Natta De Coco 70'),
(94, 94, 20, 1, 250.00, 250.00, 'Coconut Gel in Syrup'),
(95, 95, 21, 1, 300.00, 300.00, 'Buenas Nata De Coco '),
(96, 96, 23, 1, 50.00, 50.00, 'Inaco Nata de Coco'),
(97, 97, 20, 1, 250.00, 250.00, 'Coconut Gel in Syrup'),
(98, 98, 23, 1, 50.00, 50.00, 'Inaco Nata de Coco'),
(99, 99, 19, 1, 7000.00, 7000.00, 'Natta De Coco 700'),
(100, 100, 24, 1, 250.00, 250.00, 'Monika Brand');

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
(84, 29, '2024-09-06', 300.00, 'Delivered', 'Paid', 'lola mating residence, white house, N/A, Guinto, Ma. Parang, Naujan, Mindoro Oriental, 5204', 45),
(85, 29, '2024-09-06', 300.00, 'Delivered', 'Paid', 'lola mating residence, white house, N/A, Guinto, Ma. Parang, Naujan, Mindoro Oriental, 5204', 45),
(86, 29, '2024-09-06', 300.00, 'Delivered', 'Paid', 'lola mating residence, white house, N/A, Guinto, Ma. Parang, Naujan, Mindoro Oriental, 5204', 45),
(87, 29, '2024-09-06', 300.00, 'Pending', 'Pending', 'lola mating residence, white house, N/A, Guinto, Ma. Parang, Naujan, Mindoro Oriental, 5204', 45),
(88, 29, '2024-09-06', 300.00, 'Pending', 'Pending', 'lola mating residence, white house, N/A, Guinto, Ma. Parang, Naujan, Mindoro Oriental, 5204', 45),
(89, 29, '2024-09-07', 1250.00, 'Delivered', 'Paid', 'lola mating residence, white house, N/A, Guinto, Ma. Parang, Naujan, Mindoro Oriental, 5204', 45),
(90, 29, '2024-09-07', 294.00, 'Delivered', 'Paid', 'lola mating residence, white house, N/A, Guinto, Ma. Parang, Naujan, Mindoro Oriental, 5204', 45),
(91, 29, '2024-09-07', 100.00, 'Delivered', 'Paid', 'lola mating residence, white house, N/A, Guinto, Ma. Parang, Naujan, Mindoro Oriental, 5204', 45),
(92, 29, '2024-09-07', 700.00, 'Delivered', 'Paid', 'lola mating residence, white house, N/A, Guinto, Ma. Parang, Naujan, Mindoro Oriental, 5204', 45),
(93, 30, '2024-09-14', 700.00, 'Delivered', 'Paid', 'j, j, j, j, j, j, j', 46),
(94, 30, '2024-09-14', 250.00, 'Delivered', 'Paid', 'j, j, j, j, j, j, j', 46),
(95, 30, '2024-09-14', 300.00, 'Delivered', 'Paid', 'j, j, j, j, j, j, j', 46),
(96, 30, '2024-09-14', 50.00, 'Delivered', 'Paid', 'j, j, j, j, j, j, j', 46),
(97, 30, '2024-09-14', 250.00, 'Delivered', 'Paid', 'j, j, j, j, j, j, j', 46),
(98, 30, '2024-09-14', 50.00, 'Delivered', 'Paid', 'j, j, j, j, j, j, j', 46),
(99, 32, '2024-09-26', 7000.00, 'Delivered', 'Paid', 'p, p, p, p, p, p, p', 47),
(100, 32, '2024-09-26', 250.00, 'Delivered', 'Paid', 'p, p, p, p, p, p, p', 47);

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
  `CategoryID` int(11) DEFAULT NULL,
  `sms_status` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`ProductID`, `ProductName`, `Photo`, `Description`, `ProductDetails`, `Price`, `QuantityAvailable`, `CategoryID`, `sms_status`) VALUES
(19, 'Natta De Coco 700', 'uploads/8.jpg', 'Coconut Gel in Syrup 700', NULL, 7000.00, 6999, NULL, 0),
(20, 'Coconut Gel in Syrup', 'uploads/4.jpg', 'Coconut Gel in Syrup (320 g)', NULL, 250.00, 100, NULL, 0),
(21, 'Buenas Nata De Coco ', 'uploads/3.jpg', 'Net WT. 12 oz (340 g)', NULL, 300.00, 100, NULL, 0),
(22, 'CDO Natta De Coco Green', 'uploads/2.jpg', 'Coconut Gel in Syrup (200 g)', NULL, 98.00, 100, NULL, 0),
(23, 'Inaco Nata de Coco', 'uploads/1.jpg', 'Salam Syrup (50 g)', NULL, 50.00, 100, NULL, 0),
(24, 'Monika Brand', 'uploads/5.jpg', 'Nata de Coco EXTRA HEAVY SYRUP (340 G)', NULL, 250.00, 100, NULL, 0),
(43, 'Boots', 'uploads/mr1.jpg', 'Sto. Ipilan (Murangan) Hatulan Green Bhouse', NULL, 100000.00, 100, NULL, 0),
(44, 'Boots', 'uploads/mr1_1727582388.jpg', 'Sto. Ipilan (Murangan) Hatulan Green Bhouse', NULL, 100000.00, 100, NULL, 0),
(45, 'Boots', 'uploads/mr1_1727582445.jpg', 'Sto. Ipilan (Murangan) Hatulan Green Bhouse', NULL, 100000.00, 100, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `ReviewID` int(11) NOT NULL,
  `CustomerID` int(11) NOT NULL,
  `ProductID` int(11) NOT NULL,
  `OrderID` int(11) NOT NULL,
  `Rating` int(1) NOT NULL,
  `ReviewText` text NOT NULL,
  `ReviewDate` datetime NOT NULL DEFAULT current_timestamp(),
  `Status` varchar(50) DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`ReviewID`, `CustomerID`, `ProductID`, `OrderID`, `Rating`, `ReviewText`, `ReviewDate`, `Status`) VALUES
(16, 29, 21, 84, 5, 'Goods', '2024-09-07 09:59:18', 'pending'),
(17, 29, 20, 89, 4, 'Goods', '2024-09-07 10:01:59', 'pending'),
(18, 29, 22, 90, 5, 'Goods', '2024-09-07 10:05:09', 'pending'),
(19, 29, 23, 91, 4, 'Good', '2024-09-07 11:30:57', 'pending'),
(20, 29, 19, 92, 5, 'Goods', '2024-09-07 11:31:53', 'pending');

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
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `admin_id` (`admin_id`);

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
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`ReviewID`),
  ADD KEY `CustomerID` (`CustomerID`),
  ADD KEY `ProductID` (`ProductID`),
  ADD KEY `OrderID` (`OrderID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `addresses`
--
ALTER TABLE `addresses`
  MODIFY `AddressID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `CartID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `CategoryID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `CustomerID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

--
-- AUTO_INCREMENT for table `orderitems`
--
ALTER TABLE `orderitems`
  MODIFY `OrderItemID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `OrderID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `ProductID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `ReviewID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`CustomerID`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`ID`) ON DELETE CASCADE;

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

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`CustomerID`) REFERENCES `customers` (`CustomerID`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`ProductID`) REFERENCES `orderitems` (`ProductID`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_3` FOREIGN KEY (`OrderID`) REFERENCES `orders` (`OrderID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
