-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jul 02, 2026 at 12:35 AM
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
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`CartID`, `CustomerID`, `CreatedDate`, `Status`) VALUES
(14, 16, '2026-06-30 22:55:17', 'Active'),
(15, 17, '2026-07-01 10:41:44', 'Completed'),
(16, 17, '2026-07-01 11:08:30', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `cart_detail`
--

DROP TABLE IF EXISTS `cart_detail`;
CREATE TABLE IF NOT EXISTS `cart_detail` (
  `CartID` int NOT NULL,
  `ProductID` int NOT NULL,
  `SizeID` int DEFAULT NULL,
  `Quantity` int NOT NULL DEFAULT '1',
  `AddedAt` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`CartID`,`ProductID`),
  KEY `fk_cartdetail_product` (`ProductID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `delivery`
--

INSERT INTO `delivery` (`DeliveryID`, `OrderID`, `DeliveryStatus`, `DeliveryDate`, `ShippingFee`) VALUES
(17, 17, 'Shipping', NULL, 0.00),
(18, 18, 'Delivered', '2026-06-30 12:37:25', 0.00),
(20, 20, 'Delivered', '2026-06-30 19:37:56', 0.00),
(21, 21, 'Preparing', NULL, 0.00),
(22, 22, 'Preparing', NULL, 0.00),
(23, 23, 'Preparing', NULL, 0.00),
(24, 24, 'Preparing', NULL, 0.00),
(26, 26, 'Delivered', '2026-07-01 10:48:50', 0.00),
(27, 27, 'Delivered', '2026-07-01 11:05:00', 0.00);

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
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `image`
--

INSERT INTO `image` (`ImageID`, `ProductID`, `ImageURL`, `AltText`, `IsThumbnail`, `SortOrder`) VALUES
(1, 1, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1782916527/xlruilawulto7o0oz08m.jpg', 'Bìa sách Mắt Biếc', 1, 1),
(2, 1, '/images/matbiec_back.jpg', 'Mặt sau sách Mắt Biếc', 0, 2),
(3, 2, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1782916499/urs15cuf6pd3vfdp22dx.jpg', 'Bìa sách Nhà Giả Kim', 1, 1),
(4, 3, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1782916482/qcxhpysdbhugu2nkscc3.jpg', 'Bìa sách Cha Giàu Cha Nghèo', 1, 1),
(5, 4, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1782916470/oqq6dp5xhtg0m3pz4wpx.webp', 'Bìa sách Đắc Nhân Tâm', 1, 1),
(6, 5, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1782916458/sxrtn4kcwejt5ympd6mr.webp', 'Bìa sách Clean Code', 1, 1),
(7, 6, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1782916448/nhqbp8vahdmhp7kiszho.jpg', 'Bìa sách Doraemon Tập 1', 1, 1),
(8, 7, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1782916437/dmngvwmdhh8ebfjisaj6.jpg', 'Bìa sách Đại Việt Sử Ký Toàn Thư', 1, 1),
(9, 8, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1782916425/iio8zncgh7lhyjiuvwuu.png', 'Bìa sách Triết học Mác-Lênin', 1, 1),
(10, 9, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1782916402/vzeojocu5rxtmh4rk0rz.jpg', 'Bìa sách Hack Não 1500', 1, 1),
(11, 10, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1782922014/tddojwxlatvwrev9oqpg.jpg', 'Bìa sách Computer Networking: A Top-Down Approach', 1, 1);

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
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order`
--

INSERT INTO `order` (`OrderID`, `CustomerID`, `EmployeeID`, `VoucherID`, `OrderDate`, `ShippingAddress`, `OrderStatus`, `TotalAmount`) VALUES
(17, NULL, NULL, NULL, '2026-06-30 12:09:11', 'Người nhận: Phúc Hồ Thiên | SĐT: 0389027572 | Địa chỉ: sdfsdfsdfsdf', 'Shipped', 65000.00),
(18, NULL, NULL, NULL, '2026-06-30 12:37:09', 'Người nhận: Phúc Hồ Thiên | SĐT: 0389027572 | Địa chỉ: sdfsdfsdfsdf', 'Delivered', 72250.00),
(20, NULL, NULL, NULL, '2026-06-30 19:36:50', 'Người nhận: Phúc Hồ Thiên | SĐT: 0389027572 | Địa chỉ: sdfsdfsdfsdf', 'Delivered', 65000.00),
(21, NULL, NULL, NULL, '2026-06-30 20:31:09', 'Người nhận: Phúc Hồ Thiên | SĐT: 0389027572 | Địa chỉ: sdfsdfsdfsdf', 'Pending', 65000.00),
(22, NULL, NULL, NULL, '2026-06-30 20:31:45', 'Người nhận: Phúc Hồ Thiên | SĐT: 0389027572 | Địa chỉ: sdfsdfsdfsdf', 'Cancelled', 99000.00),
(23, NULL, NULL, NULL, '2026-06-30 20:38:38', 'Người nhận: Phúc Hồ Thiên | SĐT: 0389027572 | Địa chỉ: sdfsdfsdfsdf', 'Cancelled', 65000.00),
(24, NULL, NULL, NULL, '2026-06-30 20:38:54', 'Người nhận: Phúc Hồ Thiên | SĐT: 0389027572 | Địa chỉ: sdfsdfsdfsdf', 'Pending', 65000.00),
(26, NULL, NULL, NULL, '2026-07-01 10:45:27', 'Người nhận: Phúc Hồ Thiên | SĐT: 0389027572 | Địa chỉ: sdfsdfsdfsdf', 'Delivered', 65000.00),
(27, 17, NULL, 1, '2026-07-01 10:56:00', '123 Streé', 'Delivered', 0.00);

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
(17, 8, NULL, 1, 65000, 65000.00),
(18, 1, NULL, 1, 72250, 72250.00),
(20, 8, NULL, 1, 65000, 65000.00),
(21, 8, NULL, 1, 65000, 65000.00),
(22, 3, NULL, 1, 99000, 99000.00),
(23, 8, NULL, 1, 65000, 65000.00),
(24, 8, NULL, 1, 65000, 65000.00),
(26, 8, NULL, 1, 65000, 65000.00),
(27, 8, NULL, 1, 65000, 65000.00);

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
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payment`
--

INSERT INTO `payment` (`PaymentID`, `OrderID`, `PaymentMethod`, `PaymentStatus`, `PaymentDate`) VALUES
(17, 17, 'COD', 'Pending', NULL),
(18, 18, 'COD', 'Completed', '2026-06-30 12:37:25'),
(20, 20, 'COD', 'Completed', '2026-06-30 19:37:56'),
(21, 21, 'COD', 'Pending', NULL),
(22, 22, 'VNPAY', 'Failed', NULL),
(23, 23, 'VNPAY', 'Failed', NULL),
(24, 24, 'COD', 'Pending', NULL),
(26, 26, 'VNPAY', 'Completed', '2026-07-01 10:48:50'),
(27, 27, 'COD', 'Completed', '2026-07-01 11:05:00');

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

DROP TABLE IF EXISTS `product`;
CREATE TABLE IF NOT EXISTS `product` (
  `ProductID` int NOT NULL AUTO_INCREMENT,
  `CategoryID` int DEFAULT NULL,
  `BrandID` int DEFAULT NULL,
  `ProductName` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Brand` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Price` int NOT NULL DEFAULT '0',
  `Quantity` int DEFAULT '0',
  `Description` text COLLATE utf8mb4_unicode_ci,
  `Publisher` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'AlphaBooks',
  `Status` enum('Còn hàng','Hết hàng') COLLATE utf8mb4_unicode_ci DEFAULT 'Còn hàng',
  PRIMARY KEY (`ProductID`),
  KEY `fk_product_category` (`CategoryID`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`ProductID`, `CategoryID`, `BrandID`, `ProductName`, `Brand`, `Price`, `Quantity`, `Description`, `Publisher`, `Status`) VALUES
(1, 1, NULL, 'Mắt Biếc', NULL, 85000, 149, 'Tiểu thuyết của Nguyễn Nhật Ánh', 'AlphaBooks', 'Còn hàng'),
(2, 2, NULL, 'Nhà Giả Kim', NULL, 75000, 200, 'Tiểu thuyết của Paulo Coelho', 'AlphaBooks', 'Còn hàng'),
(3, 3, NULL, 'Cha Giàu Cha Nghèo', NULL, 110000, 48, 'Sách tài chính cá nhân', 'AlphaBooks', 'Còn hàng'),
(4, 4, NULL, 'Đắc Nhân Tâm', NULL, 90000, 300, 'Sách kỹ năng giao tiếp', 'AlphaBooks', 'Còn hàng'),
(5, 5, NULL, 'Clean Code', NULL, 350000, 19, 'Kỹ thuật viết mã sạch', 'AlphaBooks', 'Còn hàng'),
(6, 6, NULL, 'Doraemon Tập 1', NULL, 20000, 500, 'Truyện tranh thiếu nhi', 'AlphaBooks', 'Còn hàng'),
(7, 7, NULL, 'Đại Việt Sử Ký Toàn Thư', NULL, 450000, 9, 'Lịch sử Việt Nam', 'AlphaBooks', 'Còn hàng'),
(8, 8, NULL, 'Giáo trình Triết học Mác-Lênin', NULL, 65000, 92, 'Giáo trình đại học chuẩn', 'AlphaBooks', 'Còn hàng'),
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
(1, 'Summer Sale', 15.00, '2026-06-01 00:00:00', '2026-07-30 00:00:00'),
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

--
-- Dumping data for table `promotion_detail`
--

INSERT INTO `promotion_detail` (`ProductID`, `PromotionID`, `DiscountRate`, `StartDate`, `EndDate`) VALUES
(1, 1, 15.00, NULL, NULL),
(2, 1, 20.00, NULL, NULL),
(3, 1, 10.00, NULL, NULL);

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
  `Comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `ReviewDate` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ReviewID`),
  KEY `fk_review_user` (`CustomerID`),
  KEY `fk_review_product` (`ProductID`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(2, 'Customer', 'Khách hàng');

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
  `Address` text COLLATE utf8mb4_unicode_ci,
  `CreatedDate` datetime DEFAULT CURRENT_TIMESTAMP,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ResetToken` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ResetTokenExpires` datetime DEFAULT NULL,
  PRIMARY KEY (`CustomerID`),
  UNIQUE KEY `Email` (`Email`),
  KEY `fk_user_role` (`RoleID`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`CustomerID`, `RoleID`, `LastName`, `FirstName`, `Email`, `Password`, `Phone`, `Address`, `CreatedDate`, `username`, `ResetToken`, `ResetTokenExpires`) VALUES
(16, 1, '', 'Admin_Pro', 'thienphuc12a1ltt@gmail.com', '$2y$10$wKWxDEECqzPLAg1NBg7eVuS/IXcISbfKqjGwgtCZhsRiizP3Y.lsG', '', '', '2026-06-30 22:54:47', 'Admin_Pro', NULL, NULL),
(17, 2, '', 'Phuchoclaptrinh1803', 'hoput4426@gmail.com', '$2y$10$nG2kgmhTiAZHuG1mCvkY2OipfhoESfTpVpzSeAQkcl5ZD5P5QArdm', '', '', '2026-07-01 10:41:21', 'Phuchoclaptrinh1803', NULL, NULL);

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
) ENGINE=InnoDB AUTO_INCREMENT=77 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_log`
--

INSERT INTO `user_log` (`LogID`, `CustomerID`, `EmployeeID`, `Action`, `LogDate`) VALUES
(11, NULL, NULL, 'Đặt hàng thành công đơn hàng #WBS-21 (Khách vãng lai)', '2026-06-30 20:31:09'),
(12, NULL, NULL, 'Đặt hàng thành công đơn hàng #WBS-22 (Khách vãng lai)', '2026-06-30 20:31:45'),
(13, NULL, NULL, 'Đặt hàng thành công đơn hàng #WBS-23 (Khách vãng lai)', '2026-06-30 20:38:38'),
(14, NULL, NULL, 'Thanh toán thất bại đơn hàng #WBS-23 qua VNPAY', '2026-06-30 20:38:44'),
(15, NULL, NULL, 'Đặt hàng thành công đơn hàng #WBS-24 (Khách vãng lai)', '2026-06-30 20:38:54'),
(24, 16, NULL, 'Đăng nhập hệ thống', '2026-06-30 22:55:17'),
(25, 16, NULL, 'Đăng nhập hệ thống', '2026-06-30 22:55:17'),
(26, 16, NULL, 'Đăng nhập hệ thống', '2026-07-01 10:06:07'),
(27, 16, NULL, 'Đăng nhập hệ thống', '2026-07-01 10:06:07'),
(28, 16, NULL, 'Đăng nhập hệ thống', '2026-07-01 10:31:46'),
(29, 16, NULL, 'Đăng nhập hệ thống', '2026-07-01 10:31:47'),
(30, 16, NULL, 'Đăng nhập hệ thống', '2026-07-01 10:32:24'),
(31, 16, NULL, 'Đăng nhập hệ thống', '2026-07-01 10:32:24'),
(32, 16, NULL, 'Đăng nhập hệ thống', '2026-07-01 10:33:14'),
(33, NULL, 16, 'Đăng nhập hệ thống', '2026-07-01 10:33:14'),
(34, 16, NULL, 'Đăng nhập hệ thống', '2026-07-01 10:35:01'),
(35, NULL, 16, 'Đăng nhập hệ thống', '2026-07-01 10:35:01'),
(36, 17, NULL, 'Đăng nhập hệ thống', '2026-07-01 10:41:44'),
(37, 17, NULL, 'Đăng nhập hệ thống', '2026-07-01 10:41:44'),
(38, NULL, NULL, 'Đặt hàng thành công đơn hàng #WBS-26 (Khách vãng lai)', '2026-07-01 10:45:27'),
(39, NULL, NULL, 'Thanh toán thành công đơn hàng #WBS-26 qua VNPAY', '2026-07-01 10:46:08'),
(40, 16, NULL, 'Đăng nhập hệ thống', '2026-07-01 10:48:41'),
(41, NULL, 16, 'Đăng nhập hệ thống', '2026-07-01 10:48:41'),
(42, NULL, 16, 'Cập nhật trạng thái đơn hàng #WBS-26 thành: Delivered', '2026-07-01 10:48:50'),
(43, 16, NULL, 'Đăng nhập hệ thống', '2026-07-01 10:49:55'),
(44, NULL, 16, 'Đăng nhập hệ thống', '2026-07-01 10:49:55'),
(45, 16, NULL, 'Đăng nhập hệ thống', '2026-07-01 10:53:15'),
(46, NULL, 16, 'Đăng nhập hệ thống', '2026-07-01 10:53:15'),
(47, 17, NULL, 'Đăng nhập hệ thống', '2026-07-01 10:53:53'),
(48, 17, NULL, 'Đăng nhập hệ thống', '2026-07-01 10:53:53'),
(49, 17, NULL, 'Đặt hàng thành công đơn hàng #WBS-27', '2026-07-01 10:56:00'),
(50, 16, NULL, 'Đăng nhập hệ thống', '2026-07-01 11:04:47'),
(51, NULL, 16, 'Đăng nhập hệ thống', '2026-07-01 11:04:47'),
(52, NULL, 16, 'Cập nhật trạng thái đơn hàng #WBS-27 thành: Delivered', '2026-07-01 11:05:00'),
(53, NULL, 16, 'Xóa đơn hàng #WBS-11', '2026-07-01 11:05:24'),
(54, NULL, 16, 'Xóa đơn hàng #WBS-15', '2026-07-01 11:05:30'),
(55, NULL, 16, 'Xóa đơn hàng #WBS-16', '2026-07-01 11:05:44'),
(56, 17, NULL, 'Đăng nhập hệ thống', '2026-07-01 11:08:30'),
(57, 17, NULL, 'Đăng nhập hệ thống', '2026-07-01 11:08:30'),
(58, 17, NULL, 'Đăng nhập hệ thống', '2026-07-01 11:22:18'),
(59, 17, NULL, 'Đăng nhập hệ thống', '2026-07-01 11:22:18'),
(60, 16, NULL, 'Đăng nhập hệ thống', '2026-07-01 20:55:45'),
(61, NULL, 16, 'Đăng nhập hệ thống', '2026-07-01 20:55:45'),
(62, 16, NULL, 'Đăng nhập hệ thống', '2026-07-01 21:08:13'),
(63, NULL, 16, 'Đăng nhập hệ thống', '2026-07-01 21:08:13'),
(64, NULL, 16, 'Cập nhật sản phẩm ID 10 - Tên: Computer Networking: A Top-Down Approach', '2026-07-01 21:32:48'),
(65, NULL, 16, 'Cập nhật sản phẩm ID 9 - Tên: Hack Não 1500 Từ Tiếng Anh', '2026-07-01 21:33:24'),
(66, NULL, 16, 'Cập nhật sản phẩm ID 8 - Tên: Giáo trình Triết học Mác-Lênin', '2026-07-01 21:33:46'),
(67, NULL, 16, 'Cập nhật sản phẩm ID 7 - Tên: Đại Việt Sử Ký Toàn Thư', '2026-07-01 21:33:59'),
(68, NULL, 16, 'Cập nhật sản phẩm ID 6 - Tên: Doraemon Tập 1', '2026-07-01 21:34:10'),
(69, NULL, 16, 'Cập nhật sản phẩm ID 5 - Tên: Clean Code', '2026-07-01 21:34:20'),
(70, NULL, 16, 'Cập nhật sản phẩm ID 4 - Tên: Đắc Nhân Tâm', '2026-07-01 21:34:32'),
(71, NULL, 16, 'Cập nhật sản phẩm ID 3 - Tên: Cha Giàu Cha Nghèo', '2026-07-01 21:34:44'),
(72, NULL, 16, 'Cập nhật sản phẩm ID 2 - Tên: Nhà Giả Kim', '2026-07-01 21:35:01'),
(73, NULL, 16, 'Cập nhật sản phẩm ID 1 - Tên: Mắt Biếc', '2026-07-01 21:35:04'),
(74, NULL, 16, 'Cập nhật sản phẩm ID 1 - Tên: Mắt Biếc', '2026-07-01 21:35:29'),
(75, NULL, 16, 'Cập nhật sản phẩm ID 1 - Tên: Mắt Biếc', '2026-07-01 22:01:32'),
(76, NULL, 16, 'Cập nhật sản phẩm ID 10 - Tên: Computer Networking: A Top-Down Approach', '2026-07-01 23:06:56');

-- --------------------------------------------------------

--
-- Table structure for table `user_provider`
--

DROP TABLE IF EXISTS `user_provider`;
CREATE TABLE IF NOT EXISTS `user_provider` (
  `ProviderID` int NOT NULL AUTO_INCREMENT,
  `User_ID` int NOT NULL,
  `ProviderName` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Provider_userID` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `access_token` text COLLATE utf8mb4_unicode_ci,
  `refresh_token` text COLLATE utf8mb4_unicode_ci,
  `CreatedAt` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ProviderID`),
  KEY `fk_userprovider_user` (`User_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
-- Dumping data for table `voucher_detail`
--

INSERT INTO `voucher_detail` (`CustomerID`, `VoucherID`, `ReceivedDate`, `UsedStatus`) VALUES
(17, 1, '2026-07-01 10:56:00', 1);

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
