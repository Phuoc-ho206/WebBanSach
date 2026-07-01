-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 01, 2026 at 06:37 AM
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
-- Database: `bookstore`
--

-- --------------------------------------------------------

--
-- Table structure for table `address`
--

CREATE TABLE `address` (
  `AddressID` int(11) NOT NULL,
  `CustomerID` int(11) DEFAULT NULL,
  `ReceiverName` varchar(100) NOT NULL,
  `Phone` varchar(20) NOT NULL,
  `FullAddress` text NOT NULL,
  `IsDefault` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `CartID` int(11) NOT NULL,
  `CustomerID` int(11) DEFAULT NULL,
  `CreatedDate` datetime DEFAULT current_timestamp(),
  `Status` enum('Active','Abandoned','Completed') DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

CREATE TABLE `cart_detail` (
  `CartID` int(11) NOT NULL,
  `ProductID` int(11) NOT NULL,
  `SizeID` int(11) DEFAULT NULL,
  `Quantity` int(11) NOT NULL DEFAULT 1,
  `AddedAt` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `CategoryID` int(11) NOT NULL,
  `CategoryName` varchar(100) NOT NULL,
  `Description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

CREATE TABLE `delivery` (
  `DeliveryID` int(11) NOT NULL,
  `OrderID` int(11) DEFAULT NULL,
  `DeliveryStatus` enum('Preparing','Shipping','Delivered','Failed') DEFAULT 'Preparing',
  `DeliveryDate` datetime DEFAULT NULL,
  `ShippingFee` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

CREATE TABLE `image` (
  `ImageID` int(11) NOT NULL,
  `ProductID` int(11) DEFAULT NULL,
  `ImageURL` varchar(255) NOT NULL,
  `AltText` varchar(255) DEFAULT NULL,
  `IsThumbnail` tinyint(1) DEFAULT 0,
  `SortOrder` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

CREATE TABLE `order` (
  `OrderID` int(11) NOT NULL,
  `CustomerID` int(11) DEFAULT NULL,
  `EmployeeID` int(11) DEFAULT NULL,
  `VoucherID` int(11) DEFAULT NULL,
  `OrderDate` datetime DEFAULT current_timestamp(),
  `ShippingAddress` text NOT NULL,
  `OrderStatus` enum('Pending','Processing','Shipped','Delivered','Cancelled') DEFAULT 'Pending',
  `TotalAmount` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

CREATE TABLE `order_detail` (
  `OrderID` int(11) NOT NULL,
  `ProductID` int(11) NOT NULL,
  `SizeID` int(11) DEFAULT NULL,
  `Quantity` int(11) NOT NULL,
  `Price` int(11) NOT NULL DEFAULT 0,
  `UnitPrice` decimal(15,2) NOT NULL
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

CREATE TABLE `payment` (
  `PaymentID` int(11) NOT NULL,
  `OrderID` int(11) DEFAULT NULL,
  `PaymentMethod` varchar(50) NOT NULL,
  `PaymentStatus` enum('Pending','Completed','Failed','Refunded') DEFAULT 'Pending',
  `PaymentDate` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

CREATE TABLE `product` (
  `ProductID` int(11) NOT NULL,
  `CategoryID` int(11) DEFAULT NULL,
  `BrandID` int(11) DEFAULT NULL,
  `ProductName` varchar(255) NOT NULL,
  `Brand` varchar(100) DEFAULT NULL,
  `Price` int(11) NOT NULL DEFAULT 0,
  `Quantity` int(11) DEFAULT 0,
  `Description` text DEFAULT NULL,
  `Publisher` varchar(100) DEFAULT 'AlphaBooks',
  `Status` enum('Còn hàng','Hết hàng') DEFAULT 'Còn hàng'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

CREATE TABLE `promotion` (
  `PromotionID` int(11) NOT NULL,
  `PromotionName` varchar(255) NOT NULL,
  `DiscountPercent` decimal(5,2) NOT NULL,
  `StartDate` datetime DEFAULT NULL,
  `EndDate` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

CREATE TABLE `promotion_detail` (
  `ProductID` int(11) NOT NULL,
  `PromotionID` int(11) NOT NULL,
  `DiscountRate` decimal(5,2) NOT NULL,
  `StartDate` datetime DEFAULT NULL,
  `EndDate` datetime DEFAULT NULL
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

CREATE TABLE `review` (
  `ReviewID` int(11) NOT NULL,
  `CustomerID` int(11) DEFAULT NULL,
  `ProductID` int(11) DEFAULT NULL,
  `Rating` int(11) DEFAULT NULL,
  `Comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ReviewDate` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE `role` (
  `RoleID` int(11) NOT NULL,
  `RoleName` varchar(50) NOT NULL,
  `Description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

CREATE TABLE `user` (
  `CustomerID` int(11) NOT NULL,
  `RoleID` int(11) DEFAULT NULL,
  `LastName` varchar(50) NOT NULL,
  `FirstName` varchar(50) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Phone` varchar(20) DEFAULT NULL,
  `Address` text DEFAULT NULL,
  `CreatedDate` datetime DEFAULT current_timestamp(),
  `username` varchar(100) DEFAULT NULL,
  `ResetToken` varchar(10) DEFAULT NULL,
  `ResetTokenExpires` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

CREATE TABLE `user_log` (
  `LogID` int(11) NOT NULL,
  `CustomerID` int(11) DEFAULT NULL,
  `EmployeeID` int(11) DEFAULT NULL,
  `Action` varchar(255) NOT NULL,
  `LogDate` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(59, 17, NULL, 'Đăng nhập hệ thống', '2026-07-01 11:22:18');

-- --------------------------------------------------------

--
-- Table structure for table `user_provider`
--

CREATE TABLE `user_provider` (
  `ProviderID` int(11) NOT NULL,
  `User_ID` int(11) NOT NULL,
  `ProviderName` varchar(50) NOT NULL,
  `Provider_userID` varchar(255) NOT NULL,
  `access_token` text DEFAULT NULL,
  `refresh_token` text DEFAULT NULL,
  `CreatedAt` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `voucher`
--

CREATE TABLE `voucher` (
  `VoucherID` int(11) NOT NULL,
  `VoucherCode` varchar(50) NOT NULL,
  `DiscountValue` decimal(10,2) NOT NULL,
  `ExpiredDate` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

CREATE TABLE `voucher_detail` (
  `CustomerID` int(11) NOT NULL,
  `VoucherID` int(11) NOT NULL,
  `ReceivedDate` datetime DEFAULT current_timestamp(),
  `UsedStatus` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `voucher_detail`
--

INSERT INTO `voucher_detail` (`CustomerID`, `VoucherID`, `ReceivedDate`, `UsedStatus`) VALUES
(17, 1, '2026-07-01 10:56:00', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `address`
--
ALTER TABLE `address`
  ADD PRIMARY KEY (`AddressID`),
  ADD KEY `fk_address_user` (`CustomerID`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`CartID`),
  ADD KEY `fk_cart_user` (`CustomerID`);

--
-- Indexes for table `cart_detail`
--
ALTER TABLE `cart_detail`
  ADD PRIMARY KEY (`CartID`,`ProductID`),
  ADD KEY `fk_cartdetail_product` (`ProductID`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`CategoryID`);

--
-- Indexes for table `delivery`
--
ALTER TABLE `delivery`
  ADD PRIMARY KEY (`DeliveryID`),
  ADD KEY `fk_delivery_order` (`OrderID`);

--
-- Indexes for table `image`
--
ALTER TABLE `image`
  ADD PRIMARY KEY (`ImageID`),
  ADD KEY `fk_image_product` (`ProductID`);

--
-- Indexes for table `order`
--
ALTER TABLE `order`
  ADD PRIMARY KEY (`OrderID`),
  ADD KEY `fk_order_user` (`CustomerID`),
  ADD KEY `fk_order_voucher` (`VoucherID`),
  ADD KEY `fk_order_employee` (`EmployeeID`);

--
-- Indexes for table `order_detail`
--
ALTER TABLE `order_detail`
  ADD PRIMARY KEY (`OrderID`,`ProductID`),
  ADD KEY `fk_orderdetail_product` (`ProductID`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`PaymentID`),
  ADD KEY `fk_payment_order` (`OrderID`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`ProductID`),
  ADD KEY `fk_product_category` (`CategoryID`);

--
-- Indexes for table `promotion`
--
ALTER TABLE `promotion`
  ADD PRIMARY KEY (`PromotionID`);

--
-- Indexes for table `promotion_detail`
--
ALTER TABLE `promotion_detail`
  ADD PRIMARY KEY (`ProductID`,`PromotionID`),
  ADD KEY `fk_promodetail_promo` (`PromotionID`);

--
-- Indexes for table `review`
--
ALTER TABLE `review`
  ADD PRIMARY KEY (`ReviewID`),
  ADD KEY `fk_review_user` (`CustomerID`),
  ADD KEY `fk_review_product` (`ProductID`);

--
-- Indexes for table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`RoleID`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`CustomerID`),
  ADD UNIQUE KEY `Email` (`Email`),
  ADD KEY `fk_user_role` (`RoleID`);

--
-- Indexes for table `user_log`
--
ALTER TABLE `user_log`
  ADD PRIMARY KEY (`LogID`),
  ADD KEY `fk_userlog_user` (`CustomerID`),
  ADD KEY `fk_userlog_employee` (`EmployeeID`);

--
-- Indexes for table `user_provider`
--
ALTER TABLE `user_provider`
  ADD PRIMARY KEY (`ProviderID`),
  ADD KEY `fk_userprovider_user` (`User_ID`);

--
-- Indexes for table `voucher`
--
ALTER TABLE `voucher`
  ADD PRIMARY KEY (`VoucherID`),
  ADD UNIQUE KEY `VoucherCode` (`VoucherCode`);

--
-- Indexes for table `voucher_detail`
--
ALTER TABLE `voucher_detail`
  ADD PRIMARY KEY (`CustomerID`,`VoucherID`),
  ADD KEY `fk_voucherdetail_voucher` (`VoucherID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `address`
--
ALTER TABLE `address`
  MODIFY `AddressID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `CartID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `CategoryID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `delivery`
--
ALTER TABLE `delivery`
  MODIFY `DeliveryID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `image`
--
ALTER TABLE `image`
  MODIFY `ImageID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `order`
--
ALTER TABLE `order`
  MODIFY `OrderID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `PaymentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `ProductID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `promotion`
--
ALTER TABLE `promotion`
  MODIFY `PromotionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `review`
--
ALTER TABLE `review`
  MODIFY `ReviewID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `role`
--
ALTER TABLE `role`
  MODIFY `RoleID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `CustomerID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `user_log`
--
ALTER TABLE `user_log`
  MODIFY `LogID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `user_provider`
--
ALTER TABLE `user_provider`
  MODIFY `ProviderID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `voucher`
--
ALTER TABLE `voucher`
  MODIFY `VoucherID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

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
