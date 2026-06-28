-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jun 27, 2026 at 03:53 PM
-- Server version: 8.4.7
-- PHP Version: 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bookstore`
--
CREATE DATABASE IF NOT EXISTS `bookstore` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `bookstore`;

-- --------------------------------------------------------

--
-- Table structure for table `address`
--

DROP TABLE IF EXISTS `address`;
CREATE TABLE IF NOT EXISTS `address` (
  `AddressID` int NOT NULL AUTO_INCREMENT,
  `CustomerID` int DEFAULT NULL,
  `ReceiverName` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `FullAddress` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `IsDefault` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`AddressID`),
  KEY `fk_address_user` (`CustomerID`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `address`
--

INSERT INTO `address` (`AddressID`, `CustomerID`, `ReceiverName`, `Phone`, `FullAddress`, `IsDefault`) VALUES
(1, 2, 'Trần Khách Hàng 1', '0912345678', '123 Nguyễn Trãi, Quận 1, TP.HCM', 1),
(2, 3, 'Lê Khách Hàng 2', '0923456789', '456 Lê Lợi, Quận 1, TP.HCM', 1),
(3, 6, 'Vũ Khách Hàng 3', '0956789012', '789 Điện Biên Phủ, Bình Thạnh, TP.HCM', 1),
(4, 7, 'Đặng Khách Hàng 4', '0967890123', '12 Cách Mạng Tháng 8, Quận 3, TP.HCM', 1),
(5, 8, 'Bùi Khách Hàng 5', '0978901234', '34 Xuân Thủy, Cầu Giấy, Hà Nội', 1),
(6, 9, 'Đỗ Khách Hàng 6', '0989012345', '56 Nguyễn Chí Thanh, Đống Đa, Hà Nội', 1),
(7, 10, 'Hồ Khách Hàng 7', '0990123456', '78 Bạch Đằng, Hải Châu, Đà Nẵng', 1),
(8, 2, 'Trần Khách Hàng 1', '0911111111', 'Tòa nhà Bitexco, Quận 1, TP.HCM (Công ty)', 0),
(9, 3, 'Người nhận hộ', '0922222222', '11 Nguyễn Đình Chiểu, Quận 3, TP.HCM', 0),
(10, 6, 'Vũ Khách Hàng 3', '0933333333', 'Ký túc xá ĐHQG, Thủ Đức, TP.HCM', 0);

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

DROP TABLE IF EXISTS `cart`;
CREATE TABLE IF NOT EXISTS `cart` (
  `CartID` int NOT NULL AUTO_INCREMENT,
  `CustomerID` int DEFAULT NULL,
  `CreatedDate` datetime DEFAULT CURRENT_TIMESTAMP,
  `Status` enum('Active','Abandoned','Completed') COLLATE utf8mb4_unicode_ci DEFAULT 'Active',
  PRIMARY KEY (`CartID`),
  KEY `fk_cart_user` (`CustomerID`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`CartID`, `CustomerID`, `CreatedDate`, `Status`) VALUES
(1, 2, '2026-04-01 08:40:00', 'Completed'),
(2, 3, '2026-04-02 09:20:00', 'Completed'),
(3, 6, '2026-04-05 10:15:00', 'Active'),
(4, 7, '2026-04-08 09:10:00', 'Completed'),
(5, 8, '2026-04-09 16:30:00', 'Abandoned'),
(6, 9, '2026-04-10 19:45:00', 'Active'),
(7, 10, '2026-04-11 15:00:00', 'Completed'),
(8, 2, '2026-04-15 08:00:00', 'Active'),
(9, 3, '2026-04-16 11:20:00', 'Abandoned'),
(10, 6, '2026-04-17 14:00:00', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `cart_detail`
--

DROP TABLE IF EXISTS `cart_detail`;
CREATE TABLE IF NOT EXISTS `cart_detail` (
  `CartID` int NOT NULL,
  `ProductID` int NOT NULL,
<<<<<<< HEAD
  `Quantity` int NOT NULL DEFAULT '1',
=======
  `SizeID` int DEFAULT NULL,
  `Quantity` int NOT NULL DEFAULT '1',
  `AddedAt` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`CartID`,`ProductID`),
  KEY `fk_cartdetail_product` (`ProductID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cart_detail`
--


INSERT INTO `cart_detail` (`CartID`, `ProductID`, `SizeID`, `Quantity`, `AddedAt`) VALUES
(1, 1, NULL, 1, '2026-06-27 22:14:35'),
(1, 4, NULL, 1, '2026-06-27 22:14:35'),
(2, 2, NULL, 2, '2026-06-27 22:14:35'),
(2, 5, NULL, 1, '2026-06-27 22:14:35'),
(3, 8, NULL, 1, '2026-06-27 22:14:35'),
(4, 3, NULL, 1, '2026-06-27 22:14:35'),
(5, 9, NULL, 1, '2026-06-27 22:14:35'),
(6, 6, NULL, 5, '2026-06-27 22:14:35'),
(7, 7, NULL, 1, '2026-06-27 22:14:35'),
(8, 10, NULL, 1, '2026-06-27 22:14:35');

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

DROP TABLE IF EXISTS `category`;
CREATE TABLE IF NOT EXISTS `category` (
  `CategoryID` int NOT NULL AUTO_INCREMENT,
  `CategoryName` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Description` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`CategoryID`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`CategoryID`, `CategoryName`, `Description`) VALUES
(1, 'Văn học Việt Nam', 'Tiểu thuyết, truyện ngắn trong nước'),
(2, 'Văn học Nước ngoài', 'Tiểu thuyết, truyện dịch'),
(3, 'Kinh tế', 'Sách kinh doanh, quản trị, đầu tư'),
(4, 'Tâm lý - Kỹ năng sống', 'Sách phát triển bản thân'),
(5, 'Khoa học Công nghệ', 'Sách CNTT, mạng máy tính, lập trình'),
(6, 'Thiếu nhi', 'Truyện tranh, sách khám phá cho trẻ em'),
(7, 'Lịch sử', 'Sách về các triều đại, sự kiện lịch sử'),
(8, 'Triết học', 'Sách tư tưởng, triết học Mác-Lênin, phương Tây'),
(9, 'Ngoại ngữ', 'Sách học tiếng Anh, Nhật, Hàn...'),
(10, 'Giáo trình', 'Tài liệu học tập cấp Đại học');

-- --------------------------------------------------------

--
-- Table structure for table `delivery`
--

DROP TABLE IF EXISTS `delivery`;
CREATE TABLE IF NOT EXISTS `delivery` (
  `DeliveryID` int NOT NULL AUTO_INCREMENT,
  `OrderID` int DEFAULT NULL,
  `DeliveryStatus` enum('Preparing','Shipping','Delivered','Failed') COLLATE utf8mb4_unicode_ci DEFAULT 'Preparing',
  `DeliveryDate` datetime DEFAULT NULL,
  `ShippingFee` decimal(10,2) DEFAULT '0.00',
  PRIMARY KEY (`DeliveryID`),
  KEY `fk_delivery_order` (`OrderID`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `delivery`
--

INSERT INTO `delivery` (`DeliveryID`, `OrderID`, `DeliveryStatus`, `DeliveryDate`, `ShippingFee`) VALUES
(1, 1, 'Delivered', '2026-04-03 14:00:00', 30000.00),
(2, 2, 'Delivered', '2026-04-04 10:30:00', 35000.00),
(3, 3, 'Shipping', NULL, 30000.00),
(4, 4, 'Preparing', NULL, 40000.00),
(5, 5, 'Failed', NULL, 30000.00),
(6, 6, 'Preparing', NULL, 30000.00),
(7, 7, 'Shipping', NULL, 35000.00),
(8, 8, 'Preparing', NULL, 40000.00),
(9, 9, 'Preparing', NULL, 30000.00),
(10, 10, 'Preparing', NULL, 45000.00);

-- --------------------------------------------------------

--
-- Table structure for table `image`
--

DROP TABLE IF EXISTS `image`;
CREATE TABLE IF NOT EXISTS `image` (
  `ImageID` int NOT NULL AUTO_INCREMENT,
  `ProductID` int DEFAULT NULL,
  `ImageURL` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `AltText` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `IsThumbnail` tinyint(1) DEFAULT '0',
  `SortOrder` int DEFAULT '0',
  PRIMARY KEY (`ImageID`),
  KEY `fk_image_product` (`ProductID`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `image`
--

INSERT INTO `image` (`ImageID`, `ProductID`, `ImageURL`, `AltText`, `IsThumbnail`, `SortOrder`) VALUES
(1, 1, '/images/matbiec_thumb.jpg', 'Bìa sách Mắt Biếc', 1, 1),
(2, 1, '/images/matbiec_back.jpg', 'Mặt sau sách Mắt Biếc', 0, 2),
(3, 2, '/images/nhagiakim_thumb.jpg', 'Bìa sách Nhà Giả Kim', 1, 1),
(4, 3, '/images/books/richdad_thumb.jpg', 'Bìa sách Cha Giàu Cha Nghèo', 1, 1),
(5, 4, '/images/books/dac-nhan-tam.jpg', 'Bìa sách Đắc Nhân Tâm', 1, 1),
(6, 5, '/images/cleancode_thumb.jpg', 'Bìa sách Clean Code', 1, 1),
(7, 6, '/images/doremon1_thumb.jpg', 'Bìa sách Doraemon Tập 1', 1, 1),
(8, 7, '/images/daiviet_thumb.jpg', 'Bìa sách Đại Việt Sử Ký Toàn Thư', 1, 1),
(9, 8, '/images/triethoc_thumb.jpg', 'Bìa sách Triết học Mác-Lênin', 1, 1),
(10, 9, '/images/hacknao_thumb.jpg', 'Bìa sách Hack Não 1500', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `order`
--

DROP TABLE IF EXISTS `order`;
CREATE TABLE IF NOT EXISTS `order` (
  `OrderID` int NOT NULL AUTO_INCREMENT,
  `CustomerID` int DEFAULT NULL,
  `EmployeeID` int DEFAULT NULL,
  `VoucherID` int DEFAULT NULL,
  `OrderDate` datetime DEFAULT CURRENT_TIMESTAMP,
  `ShippingAddress` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `OrderStatus` enum('Pending','Processing','Shipped','Delivered','Cancelled') COLLATE utf8mb4_unicode_ci DEFAULT 'Pending',
  `TotalAmount` decimal(15,2) NOT NULL,
  PRIMARY KEY (`OrderID`),
  KEY `fk_order_user` (`CustomerID`),
  KEY `fk_order_voucher` (`VoucherID`),
  KEY `fk_order_employee` (`EmployeeID`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order`
--

INSERT INTO `order` (`OrderID`, `CustomerID`, `EmployeeID`, `VoucherID`, `OrderDate`, `ShippingAddress`, `OrderStatus`, `TotalAmount`) VALUES
(1, 2, NULL, 2, '2026-04-01 09:00:00', '123 Nguyễn Trãi, Quận 1, TP.HCM', 'Delivered', 145000.00),
(2, 3, NULL, NULL, '2026-04-02 09:45:00', '456 Lê Lợi, Quận 1, TP.HCM', 'Delivered', 500000.00),
(3, 7, NULL, 5, '2026-04-08 09:30:00', '12 Cách Mạng Tháng 8, Quận 3, TP.HCM', 'Shipped', 70000.00),
(4, 10, NULL, 1, '2026-04-11 15:15:00', '78 Bạch Đằng, Hải Châu, Đà Nẵng', 'Processing', 350000.00),
(5, 2, NULL, NULL, '2026-04-12 10:00:00', 'Tòa nhà Bitexco, Quận 1, TP.HCM', 'Cancelled', 85000.00),
(6, 6, NULL, 8, '2026-04-13 14:20:00', '789 Điện Biên Phủ, Bình Thạnh, TP.HCM', 'Pending', 120000.00),
(7, 8, NULL, NULL, '2026-04-14 16:00:00', '34 Xuân Thủy, Cầu Giấy, Hà Nội', 'Shipped', 90000.00),
(8, 9, NULL, 3, '2026-04-15 19:30:00', '56 Nguyễn Chí Thanh, Đống Đa, Hà Nội', 'Processing', 345000.00),
(9, 3, NULL, NULL, '2026-04-16 11:00:00', '11 Nguyễn Đình Chiểu, Quận 3, TP.HCM', 'Pending', 65000.00),
(10, 10, NULL, 2, '2026-04-18 08:30:00', '78 Bạch Đằng, Hải Châu, Đà Nẵng', 'Pending', 200000.00);

-- --------------------------------------------------------

--
-- Table structure for table `order_detail`
--

DROP TABLE IF EXISTS `order_detail`;
CREATE TABLE IF NOT EXISTS `order_detail` (
  `OrderID` int NOT NULL,
  `ProductID` int NOT NULL,
  `SizeID` int DEFAULT NULL,
  `Quantity` int NOT NULL,
  `Price` int NOT NULL DEFAULT '0',
  `UnitPrice` decimal(15,2) NOT NULL,
  PRIMARY KEY (`OrderID`,`ProductID`),
  KEY `fk_orderdetail_product` (`ProductID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_detail`
--

INSERT INTO `order_detail` (`OrderID`, `ProductID`, `SizeID`, `Quantity`, `Price`, `UnitPrice`) VALUES
(1, 1, NULL, 1, 85000, 0.00),
(1, 4, NULL, 1, 90000, 0.00),
(2, 2, NULL, 2, 75000, 0.00),
(2, 5, NULL, 1, 350000, 0.00),
(3, 3, NULL, 1, 110000, 0.00),
(4, 7, NULL, 1, 450000, 0.00),
(5, 1, NULL, 1, 85000, 0.00),
(6, 1, NULL, 1, 85000, 0.00),
(7, 4, NULL, 1, 90000, 0.00),
(8, 9, NULL, 1, 395000, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

DROP TABLE IF EXISTS `payment`;
CREATE TABLE IF NOT EXISTS `payment` (
  `PaymentID` int NOT NULL AUTO_INCREMENT,
  `OrderID` int DEFAULT NULL,
  `PaymentMethod` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `PaymentStatus` enum('Pending','Completed','Failed','Refunded') COLLATE utf8mb4_unicode_ci DEFAULT 'Pending',
  `PaymentDate` datetime DEFAULT NULL,
  PRIMARY KEY (`PaymentID`),
  KEY `fk_payment_order` (`OrderID`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payment`
--

INSERT INTO `payment` (`PaymentID`, `OrderID`, `PaymentMethod`, `PaymentStatus`, `PaymentDate`) VALUES
(1, 1, 'VNPay', 'Completed', '2026-04-01 09:05:00'),
(2, 2, 'COD', 'Completed', '2026-04-04 10:30:00'),
(3, 3, 'MoMo', 'Completed', '2026-04-08 09:35:00'),
(4, 4, 'Credit Card', 'Completed', '2026-04-11 15:20:00'),
(5, 5, 'VNPay', 'Refunded', '2026-04-12 11:00:00'),
(6, 6, 'COD', 'Pending', NULL),
(7, 7, 'Bank Transfer', 'Completed', '2026-04-14 16:10:00'),
(8, 8, 'ZaloPay', 'Completed', '2026-04-15 19:35:00'),
(9, 9, 'COD', 'Pending', NULL),
(10, 10, 'VNPay', 'Pending', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

DROP TABLE IF EXISTS `product`;
CREATE TABLE IF NOT EXISTS `product` (
  `ProductID` int NOT NULL AUTO_INCREMENT,
  `CategoryID` int DEFAULT NULL,
  `ProductName` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `BrandID` int DEFAULT NULL,
  `ProductName` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Brand` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Price` int NOT NULL DEFAULT '0',
  `Quantity` int DEFAULT '0',
  `Description` text COLLATE utf8mb4_unicode_ci,
  `Publisher` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'AlphaBooks',
  `Status` enum('Còn hàng','Hết hàng') COLLATE utf8mb4_unicode_ci DEFAULT 'Còn hàng',
  PRIMARY KEY (`ProductID`),
  KEY `fk_product_category` (`CategoryID`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`ProductID`, `CategoryID`, `BrandID`, `ProductName`, `Brand`, `Price`, `Quantity`, `Description`, `Publisher`, `Status`) VALUES
(1, 1, NULL, 'Mắt Biếc', NULL, 85000, 150, 'Tiểu thuyết của Nguyễn Nhật Ánh', 'AlphaBooks', 'Còn hàng'),
(2, 2, NULL, 'Nhà Giả Kim', NULL, 75000, 200, 'Tiểu thuyết của Paulo Coelho', 'AlphaBooks', 'Còn hàng'),
(3, 3, NULL, 'Cha Giàu Cha Nghèo', NULL, 110000, 50, 'Sách tài chính cá nhân', 'AlphaBooks', 'Còn hàng'),
(4, 4, NULL, 'Đắc Nhân Tâm', NULL, 90000, 300, 'Sách kỹ năng giao tiếp', 'AlphaBooks', 'Còn hàng'),
(5, 5, NULL, 'Clean Code', NULL, 350000, 20, 'Kỹ thuật viết mã sạch', 'AlphaBooks', 'Còn hàng'),
(6, 6, NULL, 'Doraemon Tập 1', NULL, 20000, 500, 'Truyện tranh thiếu nhi', 'AlphaBooks', 'Còn hàng'),
(7, 7, NULL, 'Đại Việt Sử Ký Toàn Thư', NULL, 450000, 10, 'Lịch sử Việt Nam', 'AlphaBooks', 'Còn hàng'),
(8, 8, NULL, 'Giáo trình Triết học Mác-Lênin', NULL, 65000, 100, 'Giáo trình đại học chuẩn', 'AlphaBooks', 'Còn hàng'),
(9, 9, NULL, 'Hack Não 1500 Từ Tiếng Anh', NULL, 395000, 80, 'Sách học từ vựng hiệu quả', 'AlphaBooks', 'Còn hàng'),
(10, 5, NULL, 'Computer Networking: A Top-Down Approach', NULL, 550000, 0, 'Giáo trình mạng máy tính', 'AlphaBooks', 'Hết hàng');

-- --------------------------------------------------------

--
-- Table structure for table `promotion`
--

DROP TABLE IF EXISTS `promotion`;
CREATE TABLE IF NOT EXISTS `promotion` (
  `PromotionID` int NOT NULL AUTO_INCREMENT,
  `PromotionName` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `DiscountPercent` decimal(5,2) NOT NULL,
  `StartDate` datetime DEFAULT NULL,
  `EndDate` datetime DEFAULT NULL,
  PRIMARY KEY (`PromotionID`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `promotion`
--

INSERT INTO `promotion` (`PromotionID`, `PromotionName`, `DiscountPercent`, `StartDate`, `EndDate`) VALUES
(1, 'Summer Sale', 15.00, '2026-06-01 00:00:00', '2026-06-30 00:00:00'),
(2, 'Back to School', 20.00, '2026-08-15 00:00:00', '2026-09-15 00:00:00'),
(3, 'Black Friday', 50.00, '2026-11-20 00:00:00', '2026-11-30 00:00:00'),
(4, 'Flash Sale Tháng 4', 10.00, '2026-04-01 00:00:00', '2026-04-05 00:00:00'),
(5, 'Mừng tuổi mới', 25.00, '2026-01-01 00:00:00', '2026-01-10 00:00:00'),
(6, 'Giải phóng miền Nam', 30.00, '2026-04-28 00:00:00', '2026-05-02 00:00:00'),
(7, 'Quốc tế Phụ nữ', 15.00, '2026-03-05 00:00:00', '2026-03-10 00:00:00'),
(8, 'Trung Thu Yêu Thương', 10.00, '2026-09-10 00:00:00', '2026-09-20 00:00:00'),
(9, 'Clearance Sale', 40.00, '2026-12-15 00:00:00', '2026-12-31 00:00:00'),
(10, 'Happy Weekend', 5.00, '2026-05-08 00:00:00', '2026-05-10 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `promotion_detail`
--

DROP TABLE IF EXISTS `promotion_detail`;
CREATE TABLE IF NOT EXISTS `promotion_detail` (
  `ProductID` int NOT NULL,
  `PromotionID` int NOT NULL,
  `DiscountRate` decimal(5,2) NOT NULL,
  `StartDate` datetime DEFAULT NULL,
  `EndDate` datetime DEFAULT NULL,
  PRIMARY KEY (`ProductID`,`PromotionID`),
  KEY `fk_promodetail_promo` (`PromotionID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `review`
--

DROP TABLE IF EXISTS `review`;
CREATE TABLE IF NOT EXISTS `review` (
  `ReviewID` int NOT NULL AUTO_INCREMENT,
  `CustomerID` int DEFAULT NULL,
  `ProductID` int DEFAULT NULL,
  `Rating` int DEFAULT NULL,
  `Comment` text COLLATE utf8mb4_unicode_ci,
  `ReviewDate` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ReviewID`),
  KEY `fk_review_user` (`CustomerID`),
  KEY `fk_review_product` (`ProductID`)
) ;

--
-- Dumping data for table `review`
--

INSERT INTO `review` (`ReviewID`, `CustomerID`, `ProductID`, `Rating`, `Comment`, `ReviewDate`) VALUES
(1, 2, 1, 5, 'Sách rất hay và cảm động!', '2026-04-05 10:00:00'),
(2, 3, 2, 5, 'Nội dung truyền cảm hứng tuyệt vời.', '2026-04-06 11:30:00'),
(3, 6, 3, 4, 'Kiến thức tài chính thực tế, dễ hiểu.', '2026-04-07 14:15:00'),
(4, 7, 5, 5, 'Cuốn sách bắt buộc phải có cho lập trình viên.', '2026-04-08 09:20:00'),
(5, 8, 8, 4, 'Giáo trình chuẩn, giao hàng bọc cẩn thận.', '2026-04-09 16:45:00'),
(6, 9, 6, 5, 'Tuổi thơ ùa về, giấy in đẹp.', '2026-04-10 20:00:00'),
(7, 10, 9, 3, 'Học cũng khá ổn nhưng cần kiên trì.', '2026-04-11 15:10:00'),
(8, 2, 4, 5, 'Đọc đi đọc lại vẫn thấy đúng.', '2026-04-12 08:05:00'),
(9, 3, 7, 5, 'Sách bọc bìa cứng rất xịn xò, đáng tiền.', '2026-04-13 19:30:00'),
(10, 6, 1, 4, 'Truyện hơi buồn nhưng văn phong mượt mà.', '2026-04-14 21:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

DROP TABLE IF EXISTS `role`;
CREATE TABLE IF NOT EXISTS `role` (
  `RoleID` int NOT NULL AUTO_INCREMENT,
  `RoleName` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Description` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`RoleID`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role`
--

INSERT INTO `role` (`RoleID`, `RoleName`, `Description`) VALUES
(1, 'Admin', 'Quản trị viên hệ thống'),
(2, 'Customer', 'Khách hàng mua sách'),
(3, 'Manager', 'Quản lý cửa hàng'),
(4, 'Staff', 'Nhân viên bán hàng'),
(5, 'Shipper', 'Nhân viên giao hàng'),
(6, 'Accountant', 'Nhân viên kế toán'),
(7, 'Editor', 'Biên tập viên nội dung'),
(8, 'Marketing', 'Nhân viên Marketing'),
(9, 'Support', 'Nhân viên chăm sóc khách hàng'),
(10, 'Guest', 'Người dùng vãng lai');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `CustomerID` int NOT NULL AUTO_INCREMENT,
  `RoleID` int DEFAULT NULL,
  `LastName` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `FirstName` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `CreatedDate` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`CustomerID`),
  UNIQUE KEY `Email` (`Email`),
  KEY `fk_user_role` (`RoleID`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`CustomerID`, `RoleID`, `LastName`, `FirstName`, `Email`, `Password`, `Phone`, `Address`, `CreatedDate`) VALUES
(1, 1, 'Nguyễn', 'Quản Trị', 'admin@bookstore.vn', 'hashed_pass_1', '0901234567', NULL, '2026-06-27 22:14:34'),
(2, 2, 'Trần', 'Khách Hàng 1', 'khach1@gmail.com', 'hashed_pass_2', '0912345678', NULL, '2026-06-27 22:14:34'),
(3, 2, 'Lê', 'Khách Hàng 2', 'khach2@gmail.com', 'hashed_pass_3', '0923456789', NULL, '2026-06-27 22:14:34'),
(4, 4, 'Phạm', 'Nhân Viên', 'staff1@bookstore.vn', 'hashed_pass_4', '0934567890', NULL, '2026-06-27 22:14:34'),
(5, 5, 'Hoàng', 'Giao Hàng', 'shipper@bookstore.vn', 'hashed_pass_5', '0945678901', NULL, '2026-06-27 22:14:34'),
(6, 2, 'Vũ', 'Khách Hàng 3', 'khach3@gmail.com', 'hashed_pass_6', '0956789012', NULL, '2026-06-27 22:14:34'),
(7, 2, 'Đặng', 'Khách Hàng 4', 'khach4@gmail.com', 'hashed_pass_7', '0967890123', NULL, '2026-06-27 22:14:34'),
(8, 2, 'Bùi', 'Khách Hàng 5', 'khach5@gmail.com', 'hashed_pass_8', '0978901234', NULL, '2026-06-27 22:14:34'),
(9, 2, 'Đỗ', 'Khách Hàng 6', 'khach6@gmail.com', 'hashed_pass_9', '0989012345', NULL, '2026-06-27 22:14:34'),
(10, 2, 'Hồ', 'Khách Hàng 7', 'khach7@gmail.com', 'hashed_pass_10', '0990123456', NULL, '2026-06-27 22:14:34');

-- --------------------------------------------------------

--
-- Table structure for table `user_log`
--

DROP TABLE IF EXISTS `user_log`;
CREATE TABLE IF NOT EXISTS `user_log` (
  `LogID` int NOT NULL AUTO_INCREMENT,
  `CustomerID` int DEFAULT NULL,
  `EmployeeID` int DEFAULT NULL,
  `Action` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `LogDate` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`LogID`),
  KEY `fk_userlog_user` (`CustomerID`),
  KEY `fk_userlog_employee` (`EmployeeID`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_log`
--

INSERT INTO `user_log` (`LogID`, `CustomerID`, `EmployeeID`, `Action`, `LogDate`) VALUES
(1, 2, NULL, 'Đăng nhập hệ thống', '2026-04-01 08:30:00'),
(2, 2, NULL, 'Xem sản phẩm ID 1', '2026-04-01 08:35:00'),
(3, 3, NULL, 'Đăng nhập hệ thống', '2026-04-02 09:15:00'),
(4, 3, NULL, 'Thêm sản phẩm ID 5 vào giỏ', '2026-04-02 09:20:00'),
(5, 6, NULL, 'Cập nhật địa chỉ giao hàng', '2026-04-03 10:00:00'),
(6, 7, NULL, 'Đăng xuất', '2026-04-03 10:45:00'),
(7, 8, NULL, 'Thanh toán đơn hàng', '2026-04-04 14:20:00'),
(8, 1, NULL, 'Cập nhật giá sản phẩm ID 10', '2026-04-04 15:00:00'),
(9, 9, NULL, 'Đăng ký tài khoản mới', '2026-04-05 11:11:00'),
(10, 10, NULL, 'Áp dụng voucher NEWUSER100', '2026-04-05 11:20:00');

-- --------------------------------------------------------

--
-- Table structure for table `user_provider`
--

DROP TABLE IF EXISTS `user_provider`;
CREATE TABLE IF NOT EXISTS `user_provider` (
  `ProviderID` int NOT NULL AUTO_INCREMENT,
  `User_ID` int NOT NULL,
  `ProviderName` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `Provider_userID` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `access_token` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `refresh_token` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `CreatedAt` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ProviderID`),
  KEY `fk_userprovider_user` (`User_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `voucher`
--

DROP TABLE IF EXISTS `voucher`;
CREATE TABLE IF NOT EXISTS `voucher` (
  `VoucherID` int NOT NULL AUTO_INCREMENT,
  `VoucherCode` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `DiscountValue` decimal(10,2) NOT NULL,
  `ExpiredDate` datetime DEFAULT NULL,
  PRIMARY KEY (`VoucherID`),
  UNIQUE KEY `VoucherCode` (`VoucherCode`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `voucher`
--

INSERT INTO `voucher` (`VoucherID`, `VoucherCode`, `DiscountValue`, `ExpiredDate`) VALUES
(1, 'NEWUSER100', 100000.00, '2026-12-31 00:00:00'),
(2, 'FREESHIP', 30000.00, '2026-06-30 00:00:00'),
(3, 'GIAM50K', 50000.00, '2026-05-01 00:00:00'),
(4, 'BOOKLOVE', 20000.00, '2026-08-01 00:00:00'),
(5, 'STUDENT', 40000.00, '2026-10-01 00:00:00'),
(6, 'VIPONLY', 200000.00, '2026-12-31 00:00:00'),
(7, 'SALEALL', 15000.00, '2026-07-01 00:00:00'),
(8, 'WEEKEND', 25000.00, '2026-05-31 00:00:00'),
(9, 'NIGHTOWL', 35000.00, '2026-04-30 00:00:00'),
(10, 'BIRTHDAY', 100000.00, '2026-12-31 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `voucher_detail`
--

DROP TABLE IF EXISTS `voucher_detail`;
CREATE TABLE IF NOT EXISTS `voucher_detail` (
  `CustomerID` int NOT NULL,
  `VoucherID` int NOT NULL,
  `ReceivedDate` datetime DEFAULT CURRENT_TIMESTAMP,
  `UsedStatus` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`CustomerID`,`VoucherID`),
  KEY `fk_voucherdetail_voucher` (`VoucherID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `address`
--
ALTER TABLE `address`
  ADD CONSTRAINT `fk_address_user` FOREIGN KEY (`CustomerID`) REFERENCES `user` (`CustomerID`) ON DELETE CASCADE;

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `fk_cart_user` FOREIGN KEY (`CustomerID`) REFERENCES `user` (`CustomerID`) ON DELETE CASCADE;

--
-- Constraints for table `cart_detail`
--
ALTER TABLE `cart_detail`
  ADD CONSTRAINT `fk_cartdetail_cart` FOREIGN KEY (`CartID`) REFERENCES `cart` (`CartID`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cartdetail_product` FOREIGN KEY (`ProductID`) REFERENCES `product` (`ProductID`) ON DELETE CASCADE;

--
-- Constraints for table `delivery`
--
ALTER TABLE `delivery`
  ADD CONSTRAINT `fk_delivery_order` FOREIGN KEY (`OrderID`) REFERENCES `order` (`OrderID`) ON DELETE CASCADE;

--
-- Constraints for table `image`
--
ALTER TABLE `image`
  ADD CONSTRAINT `fk_image_product` FOREIGN KEY (`ProductID`) REFERENCES `product` (`ProductID`) ON DELETE CASCADE;

--
-- Constraints for table `order`
--
ALTER TABLE `order`
  ADD CONSTRAINT `fk_order_employee` FOREIGN KEY (`EmployeeID`) REFERENCES `user` (`CustomerID`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_order_user` FOREIGN KEY (`CustomerID`) REFERENCES `user` (`CustomerID`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_order_voucher` FOREIGN KEY (`VoucherID`) REFERENCES `voucher` (`VoucherID`) ON DELETE SET NULL;

--
-- Constraints for table `order_detail`
--
ALTER TABLE `order_detail`
  ADD CONSTRAINT `fk_orderdetail_order` FOREIGN KEY (`OrderID`) REFERENCES `order` (`OrderID`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_orderdetail_product` FOREIGN KEY (`ProductID`) REFERENCES `product` (`ProductID`) ON DELETE CASCADE;

--
-- Constraints for table `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `fk_payment_order` FOREIGN KEY (`OrderID`) REFERENCES `order` (`OrderID`) ON DELETE CASCADE;

--
-- Constraints for table `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `fk_product_category` FOREIGN KEY (`CategoryID`) REFERENCES `category` (`CategoryID`) ON DELETE SET NULL;

--
-- Constraints for table `promotion_detail`
--
ALTER TABLE `promotion_detail`
  ADD CONSTRAINT `fk_promodetail_product` FOREIGN KEY (`ProductID`) REFERENCES `product` (`ProductID`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_promodetail_promo` FOREIGN KEY (`PromotionID`) REFERENCES `promotion` (`PromotionID`) ON DELETE CASCADE;

--
-- Constraints for table `review`
--
ALTER TABLE `review`
  ADD CONSTRAINT `fk_review_product` FOREIGN KEY (`ProductID`) REFERENCES `product` (`ProductID`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_review_user` FOREIGN KEY (`CustomerID`) REFERENCES `user` (`CustomerID`) ON DELETE CASCADE;

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `fk_user_role` FOREIGN KEY (`RoleID`) REFERENCES `role` (`RoleID`) ON DELETE SET NULL;

--
-- Constraints for table `user_log`
--
ALTER TABLE `user_log`
  ADD CONSTRAINT `fk_userlog_employee` FOREIGN KEY (`EmployeeID`) REFERENCES `user` (`CustomerID`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_userlog_user` FOREIGN KEY (`CustomerID`) REFERENCES `user` (`CustomerID`) ON DELETE CASCADE;

--
-- Constraints for table `user_provider`
--
ALTER TABLE `user_provider`
  ADD CONSTRAINT `fk_userprovider_user` FOREIGN KEY (`User_ID`) REFERENCES `user` (`CustomerID`) ON DELETE CASCADE;

--
-- Constraints for table `voucher_detail`
--
ALTER TABLE `voucher_detail`
  ADD CONSTRAINT `fk_voucherdetail_user` FOREIGN KEY (`CustomerID`) REFERENCES `user` (`CustomerID`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_voucherdetail_voucher` FOREIGN KEY (`VoucherID`) REFERENCES `voucher` (`VoucherID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
