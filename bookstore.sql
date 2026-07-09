-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jul 09, 2026 at 05:20 PM
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
  `ReceiverName` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `Phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `FullAddress` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
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
  `Status` enum('Active','Abandoned','Completed') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Active',
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
  `CategoryName` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `Description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
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
  `DeliveryStatus` enum('Preparing','Shipping','Delivered','Failed') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Preparing',
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
  `ImageURL` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `AltText` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `IsThumbnail` tinyint(1) DEFAULT '0',
  `SortOrder` int DEFAULT '0',
  PRIMARY KEY (`ImageID`),
  KEY `fk_image_product` (`ProductID`)
) ENGINE=InnoDB AUTO_INCREMENT=120 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(11, 10, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1782922014/tddojwxlatvwrev9oqpg.jpg', 'Bìa sách Computer Networking: A Top-Down Approach', 1, 1),
(12, 11, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1782956020/vf7akeetzfruxjjolb91.jpg', 'Bìa sách Lập trình Web với PHP', 1, 1),
(13, 12, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1782956008/uhfmlhgcrvvrtj9jct9f.jpg', 'Bìa sách Cấu trúc dữ liệu C++', 1, 1),
(14, 13, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1782955998/apfb9rd1sqoekd5jpszn.jpg', 'Bìa sách Quản trị mạng Cisco', 1, 1),
(15, 14, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1782955986/btkwvzov1v1gqcw4jblw.jpg', 'Bìa sách Responsive Bootstrap', 1, 1),
(16, 15, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1782955976/wfqsyaf7borjkqezhero.jpg', 'Bìa sách Triết học Kỷ nguyên số', 1, 1),
(17, 16, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1782955966/hgn2rbt65iwvfchmqebi.jpg', 'Bìa sách Hệ thống thu phí ETC', 1, 1),
(18, 17, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1782955955/zmz6qepdpzwx23oqyzz5.jpg', 'Bìa sách Selenium Python', 1, 1),
(19, 18, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1782955945/zkvxvfw1ir4ygakzbbfw.jpg', 'Bìa sách Kiến trúc Smartphone', 1, 1),
(20, 19, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1782955936/b1ypj4tlgx0o1sgevxfp.jpg', 'Bìa sách Phân tích dữ liệu TMĐT', 1, 1),
(21, 20, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1782955913/ylx1gjg32yehhqn9q5d9.jpg', 'Bìa sách Phân tích Thiết kế HT', 1, 1),
(22, 21, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1782955900/xfwxwhs6tcqcnh27bs07.jpg', 'Bìa sách Số Đỏ', 1, 1),
(23, 22, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1782955888/yuz6hsfyeb3n8iaiqtrw.jpg', 'Bìa sách Bố Già', 1, 1),
(24, 23, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1782955875/meksbe78btodwcah44zz.jpg', 'Bìa sách Atomic Habits', 1, 1),
(25, 24, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1782955855/amb14ykvv3j2algvxlcj.jpg', 'Bìa sách Sapiens', 1, 1),
(26, 25, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1782955836/adrhjaax8q7bi6is4zom.jpg', 'Bìa sách English for IT', 1, 1),
(27, 26, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1782955809/yms7yaikos72el4swung.png', 'Bìa sách Bản chất và Hiện tượng', 1, 1),
(28, 27, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1782955794/e5tgcqqjxosjodoxxavf.jpg', 'Bìa sách Kỹ thuật lưu lượng mạng', 1, 1),
(29, 28, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1782955780/zrytwrn2kiz58vuspyto.jpg', 'Bìa sách Lập trình Android', 1, 1),
(30, 29, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1782955753/gaeoqs1kc97cgwam9ngu.jpg', 'Bìa sách Bí mật DotCom', 1, 1),
(31, 30, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1782955730/sbmosb8km0rw7b7rcgbi.jpg', 'Bìa sách Harry Potter', 1, 1),
(32, 31, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1782955696/tkfnk9nf8leepmfxn4kk.jpg', 'Bìa sách Bảo mật Mạng', 1, 1),
(33, 32, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1782955680/nhvpvktgpgpbnteyswg2.jpg', 'Giáo trình Cơ sở dữ liệu', 1, 1),
(34, 33, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1782955639/mqrnvwdoaqrnidp3sxeo.jpg', 'Bìa sách Tư duy nhanh và chậm', 1, 1),
(35, 34, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1782955613/y2fsym3lgwjwt0duvvmf.jpg', 'Bìa sách Dế Mèn Phiêu Lưu Ký', 1, 1),
(36, 35, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1782955626/k7no90qydbfarhcwu5g3.jpg', 'Bìa sách Việt Nam Sử Lược', 1, 1),
(37, 36, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1782955602/iriiur8rck773ax2ewdr.jpg', 'Bìa sách Lập trình C++', 1, 1),
(38, 37, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1782955592/avpob52fkwblcscmpugi.jpg', 'Bìa sách Giai cấp và Dân tộc', 1, 1),
(39, 38, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1782955566/gj3w6skzn3caz3ztnjlb.png', 'Bìa sách Thiết kế LAN WAN', 1, 1),
(40, 39, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1782954933/oclumlr7kaf5p0fw79vt.jpg', 'Bìa sách Ngữ pháp Tiếng Anh', 1, 1),
(41, 40, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1782954922/ie8glejtov6jgmr0saxl.jpg', 'Bìa sách Kiểm thử tự động', 1, 1),
(42, 40, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1783611363/oecqr7hzp8sjegqillef.jpg', 'Ảnh sản phẩm Kiểm thử phần mềm tự động', 0, 2),
(43, 40, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1783611365/envqw98zcgzuckwpr1ad.jpg', 'Ảnh sản phẩm Kiểm thử phần mềm tự động', 0, 3),
(44, 40, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1783611491/dyfa1cokcrzajhs5aucw.png', 'Ảnh sản phẩm Kiểm thử phần mềm tự động', 0, 2),
(45, 40, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1783611493/oqb6ix1ega8cuyqd6fjc.jpg', 'Ảnh sản phẩm Kiểm thử phần mềm tự động', 0, 3),
(46, 39, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1783611601/phvshrjcxxyxsizj65cw.jpg', 'Ảnh sản phẩm Ngữ pháp Tiếng Anh Toàn Diện', 0, 2),
(47, 39, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1783611603/upvvooifjt8ugbxoj4ev.jpg', 'Ảnh sản phẩm Ngữ pháp Tiếng Anh Toàn Diện', 0, 3),
(48, 39, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1783611605/xwfemyxox0yuauzauarq.jpg', 'Ảnh sản phẩm Ngữ pháp Tiếng Anh Toàn Diện', 0, 4),
(49, 39, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1783611608/c3dsqjv7kjiajsmqbts6.webp', 'Ảnh sản phẩm Ngữ pháp Tiếng Anh Toàn Diện', 0, 5),
(50, 38, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1783611669/ulxbjeqryrdwg07jacen.jpg', 'Ảnh sản phẩm Thiết kế Mạng LAN và WAN', 0, 2),
(51, 38, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1783611671/tuanui1gd3ffvzjbvyci.jpg', 'Ảnh sản phẩm Thiết kế Mạng LAN và WAN', 0, 3),
(52, 37, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1783611735/tdrtrlbaz5hadvpmo2kh.jpg', 'Ảnh sản phẩm Giai cấp và Dân tộc', 0, 2),
(53, 37, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1783611737/ojhdn1c4wsgxevjd0zio.jpg', 'Ảnh sản phẩm Giai cấp và Dân tộc', 0, 3),
(54, 37, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1783611739/bdwggcmwmizsfhtyirov.jpg', 'Ảnh sản phẩm Giai cấp và Dân tộc', 0, 4),
(55, 36, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1783611814/yodkrbb4nxzca6ook1mj.webp', 'Ảnh sản phẩm Lập trình C++ Cơ bản đến Nâng cao', 0, 2),
(56, 36, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1783611816/slwzojutzo3xjom99coq.jpg', 'Ảnh sản phẩm Lập trình C++ Cơ bản đến Nâng cao', 0, 3),
(57, 36, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1783611819/jx8xwpzg0sjhe60ao4dy.jpg', 'Ảnh sản phẩm Lập trình C++ Cơ bản đến Nâng cao', 0, 4),
(58, 35, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1783611860/s3tfryyfzo0wnhzzntnb.jpg', 'Ảnh sản phẩm Việt Nam Sử Lược', 0, 2),
(59, 35, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1783611862/mh0tjo6o9vz9jtwjfzop.jpg', 'Ảnh sản phẩm Việt Nam Sử Lược', 0, 3),
(60, 35, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1783611864/kh51uwo22ijnyl09vcw5.jpg', 'Ảnh sản phẩm Việt Nam Sử Lược', 0, 4),
(61, 34, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1783611905/iyfsxk2oszxt9jek2ugt.jpg', 'Ảnh sản phẩm Dế Mèn Phiêu Lưu Ký', 0, 2),
(62, 34, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1783611907/eo9q1x0ulksnxe7eikhl.jpg', 'Ảnh sản phẩm Dế Mèn Phiêu Lưu Ký', 0, 3),
(63, 34, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1783611909/pb2tmaupdqa2ch4yrdcc.jpg', 'Ảnh sản phẩm Dế Mèn Phiêu Lưu Ký', 0, 4),
(64, 33, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1783611940/oy4iqeqldsnavssu2wz2.jpg', 'Ảnh sản phẩm Tư duy nhanh và chậm', 0, 2),
(65, 33, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1783611942/yb3kimw7skbq03ukmcpq.webp', 'Ảnh sản phẩm Tư duy nhanh và chậm', 0, 3),
(66, 32, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1783611997/rwwga4ecwlbbphxa7bte.png', 'Ảnh sản phẩm Giáo trình Cơ sở dữ liệu', 0, 2),
(67, 32, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1783612000/osrnm9efcvi4avzlhs9u.jpg', 'Ảnh sản phẩm Giáo trình Cơ sở dữ liệu', 0, 3),
(68, 32, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1783612002/grplos5b9r2pdevbtaps.jpg', 'Ảnh sản phẩm Giáo trình Cơ sở dữ liệu', 0, 4),
(69, 31, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1783612348/x5gz7fdygr7zrywsv12t.jpg', 'Ảnh sản phẩm Bảo mật Mạng máy tính', 0, 2),
(73, 31, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1783612371/gd4r05um8pb2zaicgtvl.jpg', 'Ảnh sản phẩm Bảo mật Mạng máy tính', 0, 3),
(74, 31, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1783612372/fg7pkf8ttbwdl6mvdoqg.jpg', 'Ảnh sản phẩm Bảo mật Mạng máy tính', 0, 4),
(75, 30, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1783612497/mc9zx9q1fhbj7m3fbgqx.jpg', 'Ảnh sản phẩm Harry Potter và Hòn Đá Phù Thủy', 0, 2),
(76, 30, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1783612498/u8crw32ug5fphjejj6ab.jpg', 'Ảnh sản phẩm Harry Potter và Hòn Đá Phù Thủy', 0, 3),
(77, 30, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1783612499/pams5hdbrkyz2vio2rcn.jpg', 'Ảnh sản phẩm Harry Potter và Hòn Đá Phù Thủy', 0, 4),
(78, 29, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1783612538/jleuotkdlvccwxswgquh.jpg', 'Ảnh sản phẩm Bí mật DotCom', 0, 2),
(79, 29, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1783612540/ze6lctdkr0cxadkzapsv.jpg', 'Ảnh sản phẩm Bí mật DotCom', 0, 3),
(80, 28, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1783612580/yvxhyy0hkgq3ewrfe9h4.jpg', 'Ảnh sản phẩm Lập trình Android với Kotlin', 0, 2),
(81, 28, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1783612583/wwpkcodzzvcot8e5l8mx.jpg', 'Ảnh sản phẩm Lập trình Android với Kotlin', 0, 3),
(82, 28, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1783612585/hkjieq6gm568knjoymsz.jpg', 'Ảnh sản phẩm Lập trình Android với Kotlin', 0, 4),
(83, 27, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1783612641/honc3lf1d8wuyubs6omk.jpg', 'Ảnh sản phẩm Kỹ thuật lưu lượng mạng (Traffic Engineering)', 0, 2),
(84, 27, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1783612643/tzfb9jvtviimzitbkwxc.jpg', 'Ảnh sản phẩm Kỹ thuật lưu lượng mạng (Traffic Engineering)', 0, 3),
(85, 27, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1783612644/ktlyhzpkrs1cgjo18m2z.jpg', 'Ảnh sản phẩm Kỹ thuật lưu lượng mạng (Traffic Engineering)', 0, 4),
(86, 26, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1783613893/eaizrrjeaxwisw2530w1.jpg', 'Ảnh sản phẩm Bản chất và Hiện tượng', 0, 2),
(87, 26, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1783613895/yohzntyoqvfpam4wgyvs.jpg', 'Ảnh sản phẩm Bản chất và Hiện tượng', 0, 3),
(88, 25, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1783613941/skhrh0yvnvn3zxonb3ti.jpg', 'Ảnh sản phẩm English for Information Technology', 0, 2),
(89, 25, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1783613943/ekkpfhnb2lv56zwtimf7.jpg', 'Ảnh sản phẩm English for Information Technology', 0, 3),
(90, 25, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1783613945/fink5jxkn9bfksmvssum.jpg', 'Ảnh sản phẩm English for Information Technology', 0, 4),
(91, 24, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1783613978/ksntbrvjlmysn4zkkkmk.jpg', 'Ảnh sản phẩm Sapiens - Lược sử loài người', 0, 2),
(92, 24, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1783613980/ofywtv0qvaxxifodleyl.jpg', 'Ảnh sản phẩm Sapiens - Lược sử loài người', 0, 3),
(93, 24, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1783613984/xitjkifwge2dyptf5fnj.jpg', 'Ảnh sản phẩm Sapiens - Lược sử loài người', 0, 4),
(94, 23, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1783614010/snk71fbz7sik5q0ba0ko.jpg', 'Ảnh sản phẩm Atomic Habits - Thay Đổi Tý Hon, Hiệu Quả Bất Ngờ', 0, 2),
(95, 23, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1783614012/mny1pmrgxsounuvyowvh.jpg', 'Ảnh sản phẩm Atomic Habits - Thay Đổi Tý Hon, Hiệu Quả Bất Ngờ', 0, 3),
(96, 23, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1783614014/mzyrsqf8asuy9xepjuu1.jpg', 'Ảnh sản phẩm Atomic Habits - Thay Đổi Tý Hon, Hiệu Quả Bất Ngờ', 0, 4),
(97, 22, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1783614049/xhulpkohon9txcf09swq.jpg', 'Ảnh sản phẩm Bố Già (The Godfather)', 0, 2),
(98, 22, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1783614051/fmsp4zi5xu2qo1fhiw5x.jpg', 'Ảnh sản phẩm Bố Già (The Godfather)', 0, 3),
(99, 21, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1783614241/jhcdpmaxdre9utdotszi.jpg', 'Ảnh sản phẩm Số Đỏ', 0, 2),
(100, 21, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1783614243/g5ajfl8vnqleukuiqdww.jpg', 'Ảnh sản phẩm Số Đỏ', 0, 3),
(101, 21, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1783614245/dpdsoosvlfq8pc4bqobx.jpg', 'Ảnh sản phẩm Số Đỏ', 0, 4),
(102, 20, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1783614356/gdnrkhazqvjmseswztxf.jpg', 'Ảnh sản phẩm Giáo trình Phân tích và Thiết kế Hệ thống', 0, 2),
(103, 20, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1783614358/d9ybxxcdo6zbclhq1np1.jpg', 'Ảnh sản phẩm Giáo trình Phân tích và Thiết kế Hệ thống', 0, 3),
(104, 20, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1783614360/xjdkwdlcnvvjgtoqs6pv.jpg', 'Ảnh sản phẩm Giáo trình Phân tích và Thiết kế Hệ thống', 0, 4),
(105, 19, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1783614388/ie8b3jiu1debmqjqvarv.jpg', 'Ảnh sản phẩm Phân tích dữ liệu Thương mại điện tử', 0, 2),
(107, 18, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1783617184/gzbuv6jip1trhfvrdi8q.jpg', 'Ảnh sản phẩm Kiến trúc Smartphone Hiện Đại', 0, 2),
(108, 18, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1783617186/z1bbwv3ndjkilmidddak.jpg', 'Ảnh sản phẩm Kiến trúc Smartphone Hiện Đại', 0, 3),
(109, 17, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1783617229/gwbv7tp6ttx79by5e0at.jpg', 'Ảnh sản phẩm Tự động hóa với Selenium và Python', 0, 2),
(110, 17, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1783617230/yybuazwjx1ub2cblc8gm.jpg', 'Ảnh sản phẩm Tự động hóa với Selenium và Python', 0, 3),
(111, 16, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1783617264/ixysj6ylag3yhrqq2vwf.jpg', 'Ảnh sản phẩm Tổng quan Hệ thống thu phí tự động (ETC)', 0, 2),
(112, 15, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1783617390/vjwmnje8hqb2hq9ettm2.jpg', 'Ảnh sản phẩm Phép biện chứng duy vật trong Kỷ nguyên số', 0, 2),
(113, 15, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1783617392/casbefikxaiif3p0nci5.jpg', 'Ảnh sản phẩm Phép biện chứng duy vật trong Kỷ nguyên số', 0, 3),
(114, 14, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1783617497/dff3yd9tfvhyrmkir4mf.jpg', 'Ảnh sản phẩm Thiết kế Web chuẩn Responsive với Bootstrap', 0, 2),
(115, 14, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1783617499/zcf3blaurj26pypwvspb.jpg', 'Ảnh sản phẩm Thiết kế Web chuẩn Responsive với Bootstrap', 0, 3),
(116, 13, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1783617535/q17qtsfxdfdq7leaxvz3.jpg', 'Ảnh sản phẩm Quản trị mạng Cisco Thực Hành (CCNA)', 0, 2),
(117, 13, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1783617537/athdax3x9xlb85ynaoez.jpg', 'Ảnh sản phẩm Quản trị mạng Cisco Thực Hành (CCNA)', 0, 3),
(118, 12, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1783617573/ovo83xw80jhblyvlbmhi.jpg', 'Ảnh sản phẩm Cấu trúc dữ liệu và Giải thuật với C++', 0, 2),
(119, 12, 'https://res.cloudinary.com/k0v8tr4m/image/upload/v1783617574/vlnaknhzbivvjgj5pbof.jpg', 'Ảnh sản phẩm Cấu trúc dữ liệu và Giải thuật với C++', 0, 3);

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
  `ShippingAddress` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `OrderStatus` enum('Pending','Processing','Shipped','Delivered','Cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Pending',
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
  `PaymentMethod` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `PaymentStatus` enum('Pending','Completed','Failed','Refunded') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Pending',
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
  `ProductName` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `Brand` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Price` int NOT NULL DEFAULT '0',
  `Quantity` int DEFAULT '0',
  `Description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `Publisher` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'AlphaBooks',
  `Status` enum('Còn hàng','Hết hàng') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Còn hàng',
  PRIMARY KEY (`ProductID`),
  KEY `fk_product_category` (`CategoryID`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`ProductID`, `CategoryID`, `BrandID`, `ProductName`, `Brand`, `Price`, `Quantity`, `Description`, `Publisher`, `Status`) VALUES
(1, 1, NULL, 'Mắt Biếc', NULL, 85000, 149, 'Tiểu thuyết nổi tiếng của Nguyễn Nhật Ánh kể về mối tình trong sáng nhưng đầy tiếc nuối giữa Ngạn và Hà Lan. Với lối kể chuyện nhẹ nhàng và giàu cảm xúc, tác phẩm khắc họa vẻ đẹp của tuổi học trò và những rung động đầu đời.', 'Nhà xuất bản Trẻ', 'Còn hàng'),
(2, 2, NULL, 'Nhà Giả Kim', NULL, 75000, 200, 'Câu chuyện kể về hành trình theo đuổi ước mơ của chàng chăn cừu Santiago. Tác phẩm truyền tải thông điệp sâu sắc về lòng dũng cảm, niềm tin và việc lắng nghe tiếng gọi của trái tim để thực hiện vận mệnh của mỗi người.', 'Nhà xuất bản Hội Nhà Văn', 'Còn hàng'),
(3, 3, NULL, 'Cha Giàu Cha Nghèo', NULL, 110000, 48, 'Cuốn sách truyền cảm hứng về tư duy tài chính thông qua những bài học từ hai hình mẫu \"cha giàu\" và \"cha nghèo\". Tác phẩm giúp người đọc hiểu cách quản lý tiền bạc, đầu tư và xây dựng sự tự do tài chính.', 'Nhà xuất bản Trẻ', 'Còn hàng'),
(4, 4, NULL, 'Đắc Nhân Tâm', NULL, 90000, 300, 'Một trong những cuốn sách nổi tiếng nhất về nghệ thuật giao tiếp và ứng xử. Tác phẩm giúp người đọc xây dựng các mối quan hệ tốt đẹp, tạo ảnh hưởng tích cực và phát triển bản thân trong cuộc sống cũng như công việc.', 'Nhà xuất bản Tổng hợp Thành phố Hồ Chí Minh', 'Còn hàng'),
(5, 5, NULL, 'Clean Code', NULL, 350000, 19, 'Tập đầu tiên mở ra thế giới của chú mèo máy Doraemon đến từ tương lai cùng những bảo bối thần kỳ giúp Nobita vượt qua khó khăn. Những câu chuyện hài hước, nhân văn và giàu trí tưởng tượng đã trở thành tuổi thơ của nhiều thế hệ độc giả.', 'Prentice Hall', 'Còn hàng'),
(6, 6, NULL, 'Doraemon Tập 1', NULL, 20000, 500, 'Tập đầu tiên mở ra thế giới của chú mèo máy Doraemon đến từ tương lai cùng những bảo bối thần kỳ giúp Nobita vượt qua khó khăn. Những câu chuyện hài hước, nhân văn và giàu trí tưởng tượng đã trở thành tuổi thơ của nhiều thế hệ độc giả.', 'Nhà xuất bản Kim Đồng', 'Còn hàng'),
(7, 7, NULL, 'Đại Việt Sử Ký Toàn Thư', NULL, 450000, 9, 'Đây là bộ chính sử đầy đủ và quan trọng nhất của Việt Nam thời phong kiến, ghi chép lịch sử dân tộc từ thời Hồng Bàng đến triều Lê. Tác phẩm là nguồn tư liệu quý giá cho việc nghiên cứu lịch sử và văn hóa Việt Nam.', 'NXB Khoa học Xã hội', 'Còn hàng'),
(8, 8, NULL, 'Giáo trình Triết học Mác-Lênin', NULL, 65000, 92, 'Giáo trình cung cấp những kiến thức nền tảng về triết học Mác – Lênin, bao gồm chủ nghĩa duy vật biện chứng và chủ nghĩa duy vật lịch sử. Đây là tài liệu chính thức dành cho sinh viên các trường đại học và cao đẳng tại Việt Nam.', 'Nhà xuất bản Chính trị quốc gia Sự thật', 'Còn hàng'),
(9, 9, NULL, 'Hack Não 1500 Từ Tiếng Anh', NULL, 395000, 80, 'Cuốn sách giúp người học ghi nhớ 1.500 từ vựng tiếng Anh thông dụng bằng phương pháp hình ảnh, âm thanh và liên tưởng. Nội dung được thiết kế khoa học, giúp tăng khả năng ghi nhớ lâu dài và cải thiện vốn từ nhanh chóng.', 'NXB Thế Giới', 'Còn hàng'),
(10, 5, NULL, 'Computer Networking: A Top-Down Approach', NULL, 550000, 230, 'Cuốn sách kinh điển về mạng máy tính, tiếp cận từ góc nhìn ứng dụng trước khi đi sâu vào các tầng giao thức bên dưới. Sách trình bày rõ ràng các khái niệm về Internet, TCP/IP, định tuyến, bảo mật và mạng không dây, phù hợp cho sinh viên và lập trình viên.', 'Pearson', 'Còn hàng'),
(11, 5, NULL, 'Lập trình Web với PHP và MySQL', NULL, 185000, 45, 'Hướng dẫn xây dựng website e-commerce toàn diện', 'NXB Khoa Học Kỹ Thuật', 'Còn hàng'),
(12, 5, NULL, 'Cấu trúc dữ liệu và Giải thuật với C++', NULL, 195000, 30, 'Nền tảng thuật toán, cây nhị phân và tối ưu hóa', 'NXB Bách Khoa', 'Còn hàng'),
(13, 5, NULL, 'Quản trị mạng Cisco Thực Hành (CCNA)', NULL, 220000, 15, 'Cấu hình Router, Switch, VLAN và ACL', 'NXB Thông Tin', 'Còn hàng'),
(14, 5, NULL, 'Thiết kế Web chuẩn Responsive với Bootstrap', NULL, 120000, 50, 'Xây dựng giao diện frontend hiện đại và tối ưu', 'NXB Trẻ', 'Còn hàng'),
(15, 8, NULL, 'Phép biện chứng duy vật trong Kỷ nguyên số', NULL, 95000, 60, 'Ứng dụng triết học vào đời sống và công nghệ', 'Nhà xuất bản Chính trị Quốc gia Sự thật', 'Còn hàng'),
(16, 5, NULL, 'Tổng quan Hệ thống thu phí tự động (ETC)', NULL, 150000, 20, 'Kiến trúc và giải pháp giao thông thông minh (ITS)', 'NXB Giao Thông', 'Còn hàng'),
(17, 5, NULL, 'Tự động hóa với Selenium và Python', NULL, 160000, 40, 'Kỹ thuật scraping dữ liệu và automated testing', 'NXB Bách Khoa', 'Còn hàng'),
(18, 5, NULL, 'Kiến trúc Smartphone Hiện Đại', NULL, 210000, 25, 'Phân tích chipset, hiệu năng và hệ điều hành di động', 'NXB Công Nghệ', 'Còn hàng'),
(19, 3, NULL, 'Phân tích dữ liệu Thương mại điện tử', NULL, 180000, 35, 'Tối ưu hóa doanh thu bán hàng trên các sàn TMĐT', 'AlphaBooks', 'Còn hàng'),
(20, 10, NULL, 'Giáo trình Phân tích và Thiết kế Hệ thống', NULL, 130000, 80, 'Tài liệu chuẩn cho sinh viên ngành CNTT', 'NXB Đại Học Quốc Gia', 'Còn hàng'),
(21, 1, NULL, 'Số Đỏ', NULL, 75000, 120, 'Tác phẩm văn học trào phúng của Vũ Trọng Phụng', 'NXB Văn Học', 'Còn hàng'),
(22, 2, NULL, 'Bố Già (The Godfather)', NULL, 115000, 90, 'Tiểu thuyết kinh điển của Mario Puzo', 'NXB Văn Học', 'Còn hàng'),
(23, 4, NULL, 'Atomic Habits - Thay Đổi Tý Hon, Hiệu Quả Bất Ngờ', NULL, 140000, 200, 'Cách xây dựng thói quen tốt và loại bỏ thói quen xấu', '1980 Books', 'Còn hàng'),
(24, 7, NULL, 'Sapiens - Lược sử loài người', NULL, 250000, 45, 'Hành trình phát triển của nhân loại', 'NXB Tri Thức', 'Còn hàng'),
(25, 9, NULL, 'English for Information Technology', NULL, 165000, 55, 'Tiếng Anh chuyên ngành Công nghệ thông tin', 'Oxford University Press', 'Còn hàng'),
(26, 8, NULL, 'Bản chất và Hiện tượng', NULL, 85000, 40, 'Chuyên đề triết học chuyên sâu', 'Nhà xuất bản Chính trị Quốc gia Sự thật', 'Còn hàng'),
(27, 5, NULL, 'Kỹ thuật lưu lượng mạng (Traffic Engineering)', NULL, 230000, 10, 'Tối ưu hóa băng thông và luồng dữ liệu mạng', 'NXB Khoa Học Kỹ Thuật', 'Còn hàng'),
(28, 5, NULL, 'Lập trình Android với Kotlin', NULL, 175000, 25, 'Phát triển ứng dụng di động hiệu suất cao', 'NXB Thanh Niên', 'Còn hàng'),
(29, 3, NULL, 'Bí mật DotCom', NULL, 150000, 70, 'Chiến lược phát triển công ty online', 'AlphaBooks', 'Còn hàng'),
(30, 6, NULL, 'Harry Potter và Hòn Đá Phù Thủy', NULL, 145000, 150, 'Tập 1 của series truyện ma thuật kinh điển', 'NXB Trẻ', 'Còn hàng'),
(31, 5, NULL, 'Bảo mật Mạng máy tính', NULL, 215000, 18, 'Phòng chống tấn công và quản lý rủi ro', 'NXB Bách Khoa', 'Còn hàng'),
(32, 10, NULL, 'Giáo trình Cơ sở dữ liệu', NULL, 110000, 100, 'Nguyên lý và ứng dụng các hệ quản trị CSDL', 'NXB Đại Học Quốc Gia', 'Còn hàng'),
(33, 4, NULL, 'Tư duy nhanh và chậm', NULL, 190000, 65, 'Khám phá hai hệ thống tư duy của con người', 'AlphaBooks', 'Còn hàng'),
(34, 1, NULL, 'Dế Mèn Phiêu Lưu Ký', NULL, 45000, 250, 'Tác phẩm thiếu nhi kinh điển của Tô Hoài', 'NXB Kim Đồng', 'Còn hàng'),
(35, 7, NULL, 'Việt Nam Sử Lược', NULL, 125000, 30, 'Cuốn sử Việt Nam đầu tiên viết bằng chữ quốc ngữ', 'NXB Văn Học', 'Còn hàng'),
(36, 5, NULL, 'Lập trình C++ Cơ bản đến Nâng cao', NULL, 155000, 55, 'Làm chủ ngôn ngữ lập trình C++', 'NXB Thông Tin', 'Còn hàng'),
(37, 8, NULL, 'Giai cấp và Dân tộc', NULL, 75000, 20, 'Nghiên cứu về mối quan hệ giữa giai cấp và dân tộc', 'Nhà xuất bản Chính trị Quốc gia Sự thật', 'Còn hàng'),
(38, 5, NULL, 'Thiết kế Mạng LAN và WAN', NULL, 195000, 22, 'Quy hoạch IP, Subnetting và cấu hình định tuyến', 'NXB Bách Khoa', 'Còn hàng'),
(39, 9, NULL, 'Ngữ pháp Tiếng Anh Toàn Diện', NULL, 120000, 110, 'Sách tham khảo ngữ pháp từ cơ bản đến nâng cao', 'NXB Tổng Hợp', 'Còn hàng'),
(40, 5, NULL, 'Kiểm thử phần mềm tự động', NULL, 185000, 35, 'Đảm bảo chất lượng phần mềm với Automation Test', 'NXB Khoa Học Kỹ Thuật', 'Còn hàng');

-- --------------------------------------------------------

--
-- Table structure for table `promotion`
--

DROP TABLE IF EXISTS `promotion`;
CREATE TABLE IF NOT EXISTS `promotion` (
  `PromotionID` int NOT NULL AUTO_INCREMENT,
  `PromotionName` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
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
  `RoleName` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `Description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
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
  `LastName` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `FirstName` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `Email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `Password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `Phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `CreatedDate` datetime DEFAULT CURRENT_TIMESTAMP,
  `username` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ResetToken` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
  `Action` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `LogDate` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`LogID`),
  KEY `fk_userlog_user` (`CustomerID`),
  KEY `fk_userlog_employee` (`EmployeeID`)
) ENGINE=InnoDB AUTO_INCREMENT=168 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(76, NULL, 16, 'Cập nhật sản phẩm ID 10 - Tên: Computer Networking: A Top-Down Approach', '2026-07-01 23:06:56'),
(77, 16, NULL, 'Đăng nhập hệ thống', '2026-07-02 07:54:08'),
(78, NULL, 16, 'Đăng nhập hệ thống', '2026-07-02 07:54:08'),
(79, NULL, 16, 'Cập nhật sản phẩm ID 10 - Tên: Computer Networking: A Top-Down Approach', '2026-07-02 07:54:47'),
(80, NULL, 16, 'Cập nhật sản phẩm ID 9 - Tên: Hack Não 1500 Từ Tiếng Anh', '2026-07-02 07:55:49'),
(81, NULL, 16, 'Cập nhật sản phẩm ID 8 - Tên: Giáo trình Triết học Mác-Lênin', '2026-07-02 07:56:24'),
(82, NULL, 16, 'Cập nhật sản phẩm ID 7 - Tên: Đại Việt Sử Ký Toàn Thư', '2026-07-02 07:56:59'),
(83, NULL, 16, 'Cập nhật sản phẩm ID 6 - Tên: Doraemon Tập 1', '2026-07-02 07:57:36'),
(84, NULL, 16, 'Cập nhật sản phẩm ID 5 - Tên: Clean Code', '2026-07-02 07:59:00'),
(85, NULL, 16, 'Cập nhật sản phẩm ID 4 - Tên: Đắc Nhân Tâm', '2026-07-02 07:59:23'),
(86, NULL, 16, 'Cập nhật sản phẩm ID 3 - Tên: Cha Giàu Cha Nghèo', '2026-07-02 07:59:48'),
(87, NULL, 16, 'Cập nhật sản phẩm ID 2 - Tên: Nhà Giả Kim', '2026-07-02 08:00:18'),
(88, NULL, 16, 'Cập nhật sản phẩm ID 1 - Tên: Mắt Biếc', '2026-07-02 08:00:27'),
(89, NULL, 16, 'Cập nhật sản phẩm ID 10 - Tên: Computer Networking: A Top-Down Approach', '2026-07-02 08:01:18'),
(90, NULL, 16, 'Cập nhật sản phẩm ID 9 - Tên: Hack Não 1500 Từ Tiếng Anh', '2026-07-02 08:01:29'),
(91, NULL, 16, 'Cập nhật sản phẩm ID 8 - Tên: Giáo trình Triết học Mác-Lênin', '2026-07-02 08:01:42'),
(92, NULL, 16, 'Cập nhật sản phẩm ID 7 - Tên: Đại Việt Sử Ký Toàn Thư', '2026-07-02 08:01:53'),
(93, NULL, 16, 'Cập nhật sản phẩm ID 6 - Tên: Doraemon Tập 1', '2026-07-02 08:02:03'),
(94, NULL, 16, 'Cập nhật sản phẩm ID 5 - Tên: Clean Code', '2026-07-02 08:02:15'),
(95, NULL, 16, 'Cập nhật sản phẩm ID 4 - Tên: Đắc Nhân Tâm', '2026-07-02 08:02:24'),
(96, NULL, 16, 'Cập nhật sản phẩm ID 3 - Tên: Cha Giàu Cha Nghèo', '2026-07-02 08:02:34'),
(97, NULL, 16, 'Cập nhật sản phẩm ID 2 - Tên: Nhà Giả Kim', '2026-07-02 08:02:46'),
(98, NULL, 16, 'Cập nhật sản phẩm ID 1 - Tên: Mắt Biếc', '2026-07-02 08:02:55'),
(99, NULL, 16, 'Cập nhật sản phẩm ID 40 - Tên: Kiểm thử phần mềm tự động', '2026-07-02 08:13:57'),
(100, NULL, 16, 'Cập nhật sản phẩm ID 40 - Tên: Kiểm thử phần mềm tự động', '2026-07-02 08:15:24'),
(101, NULL, 16, 'Cập nhật sản phẩm ID 39 - Tên: Ngữ pháp Tiếng Anh Toàn Diện', '2026-07-02 08:15:36'),
(102, NULL, 16, 'Cập nhật sản phẩm ID 11 - Tên: Lập trình Web với PHP và MySQL', '2026-07-02 08:25:34'),
(103, NULL, 16, 'Cập nhật sản phẩm ID 38 - Tên: Thiết kế Mạng LAN và WAN', '2026-07-02 08:26:09'),
(104, NULL, 16, 'Cập nhật sản phẩm ID 37 - Tên: Giai cấp và Dân tộc', '2026-07-02 08:26:35'),
(105, NULL, 16, 'Cập nhật sản phẩm ID 36 - Tên: Lập trình C++ Cơ bản đến Nâng cao', '2026-07-02 08:26:45'),
(106, NULL, 16, 'Cập nhật sản phẩm ID 34 - Tên: Dế Mèn Phiêu Lưu Ký', '2026-07-02 08:26:55'),
(107, NULL, 16, 'Cập nhật sản phẩm ID 35 - Tên: Việt Nam Sử Lược', '2026-07-02 08:27:09'),
(108, NULL, 16, 'Cập nhật sản phẩm ID 33 - Tên: Tư duy nhanh và chậm', '2026-07-02 08:27:22'),
(109, NULL, 16, 'Cập nhật sản phẩm ID 32 - Tên: Giáo trình Cơ sở dữ liệu', '2026-07-02 08:28:02'),
(110, NULL, 16, 'Cập nhật sản phẩm ID 31 - Tên: Bảo mật Mạng máy tính', '2026-07-02 08:28:19'),
(111, NULL, 16, 'Cập nhật sản phẩm ID 30 - Tên: Harry Potter và Hòn Đá Phù Thủy', '2026-07-02 08:28:39'),
(112, NULL, 16, 'Cập nhật sản phẩm ID 30 - Tên: Harry Potter và Hòn Đá Phù Thủy', '2026-07-02 08:28:53'),
(113, NULL, 16, 'Cập nhật sản phẩm ID 29 - Tên: Bí mật DotCom', '2026-07-02 08:29:15'),
(114, NULL, 16, 'Cập nhật sản phẩm ID 28 - Tên: Lập trình Android với Kotlin', '2026-07-02 08:29:43'),
(115, NULL, 16, 'Cập nhật sản phẩm ID 27 - Tên: Kỹ thuật lưu lượng mạng (Traffic Engineering)', '2026-07-02 08:29:56'),
(116, NULL, 16, 'Cập nhật sản phẩm ID 26 - Tên: Bản chất và Hiện tượng', '2026-07-02 08:30:11'),
(117, NULL, 16, 'Cập nhật sản phẩm ID 25 - Tên: English for Information Technology', '2026-07-02 08:30:38'),
(118, NULL, 16, 'Cập nhật sản phẩm ID 24 - Tên: Sapiens - Lược sử loài người', '2026-07-02 08:30:58'),
(119, NULL, 16, 'Cập nhật sản phẩm ID 23 - Tên: Atomic Habits - Thay Đổi Tý Hon, Hiệu Quả Bất Ngờ', '2026-07-02 08:31:18'),
(120, NULL, 16, 'Cập nhật sản phẩm ID 22 - Tên: Bố Già (The Godfather)', '2026-07-02 08:31:30'),
(121, NULL, 16, 'Cập nhật sản phẩm ID 21 - Tên: Số Đỏ', '2026-07-02 08:31:43'),
(122, NULL, 16, 'Cập nhật sản phẩm ID 20 - Tên: Giáo trình Phân tích và Thiết kế Hệ thống', '2026-07-02 08:31:55'),
(123, NULL, 16, 'Cập nhật sản phẩm ID 19 - Tên: Phân tích dữ liệu Thương mại điện tử', '2026-07-02 08:32:18'),
(124, NULL, 16, 'Cập nhật sản phẩm ID 18 - Tên: Kiến trúc Smartphone Hiện Đại', '2026-07-02 08:32:28'),
(125, NULL, 16, 'Cập nhật sản phẩm ID 17 - Tên: Tự động hóa với Selenium và Python', '2026-07-02 08:32:37'),
(126, NULL, 16, 'Cập nhật sản phẩm ID 16 - Tên: Tổng quan Hệ thống thu phí tự động (ETC)', '2026-07-02 08:32:48'),
(127, NULL, 16, 'Cập nhật sản phẩm ID 15 - Tên: Phép biện chứng duy vật trong Kỷ nguyên số', '2026-07-02 08:32:59'),
(128, NULL, 16, 'Cập nhật sản phẩm ID 14 - Tên: Thiết kế Web chuẩn Responsive với Bootstrap', '2026-07-02 08:33:09'),
(129, NULL, 16, 'Cập nhật sản phẩm ID 13 - Tên: Quản trị mạng Cisco Thực Hành (CCNA)', '2026-07-02 08:33:20'),
(130, NULL, 16, 'Cập nhật sản phẩm ID 12 - Tên: Cấu trúc dữ liệu và Giải thuật với C++', '2026-07-02 08:33:30'),
(131, NULL, 16, 'Cập nhật sản phẩm ID 11 - Tên: Lập trình Web với PHP và MySQL', '2026-07-02 08:33:42'),
(132, 16, NULL, 'Đăng nhập hệ thống', '2026-07-09 22:31:53'),
(133, NULL, 16, 'Đăng nhập hệ thống', '2026-07-09 22:31:53'),
(134, NULL, 16, 'Cập nhật sản phẩm ID 40 - Tên: Kiểm thử phần mềm tự động', '2026-07-09 22:36:06'),
(135, NULL, 16, 'Cập nhật sản phẩm ID 40 - Tên: Kiểm thử phần mềm tự động', '2026-07-09 22:38:14'),
(136, NULL, 16, 'Cập nhật sản phẩm ID 39 - Tên: Ngữ pháp Tiếng Anh Toàn Diện', '2026-07-09 22:40:09'),
(137, NULL, 16, 'Cập nhật sản phẩm ID 38 - Tên: Thiết kế Mạng LAN và WAN', '2026-07-09 22:41:12'),
(138, NULL, 16, 'Cập nhật sản phẩm ID 37 - Tên: Giai cấp và Dân tộc', '2026-07-09 22:42:20'),
(139, NULL, 16, 'Cập nhật sản phẩm ID 36 - Tên: Lập trình C++ Cơ bản đến Nâng cao', '2026-07-09 22:43:40'),
(140, NULL, 16, 'Cập nhật sản phẩm ID 35 - Tên: Việt Nam Sử Lược', '2026-07-09 22:44:26'),
(141, NULL, 16, 'Cập nhật sản phẩm ID 34 - Tên: Dế Mèn Phiêu Lưu Ký', '2026-07-09 22:45:10'),
(142, NULL, 16, 'Cập nhật sản phẩm ID 33 - Tên: Tư duy nhanh và chậm', '2026-07-09 22:45:43'),
(143, NULL, 16, 'Cập nhật sản phẩm ID 32 - Tên: Giáo trình Cơ sở dữ liệu', '2026-07-09 22:46:43'),
(144, NULL, 16, 'Cập nhật sản phẩm ID 31 - Tên: Bảo mật Mạng máy tính', '2026-07-09 22:52:31'),
(145, NULL, 16, 'Cập nhật sản phẩm ID 31 - Tên: Bảo mật Mạng máy tính', '2026-07-09 22:52:53'),
(146, NULL, 16, 'Cập nhật sản phẩm ID 31 - Tên: Bảo mật Mạng máy tính', '2026-07-09 22:54:08'),
(147, NULL, 16, 'Cập nhật sản phẩm ID 30 - Tên: Harry Potter và Hòn Đá Phù Thủy', '2026-07-09 22:55:00'),
(148, NULL, 16, 'Cập nhật sản phẩm ID 29 - Tên: Bí mật DotCom', '2026-07-09 22:55:41'),
(149, NULL, 16, 'Cập nhật sản phẩm ID 28 - Tên: Lập trình Android với Kotlin', '2026-07-09 22:56:26'),
(150, NULL, 16, 'Cập nhật sản phẩm ID 27 - Tên: Kỹ thuật lưu lượng mạng (Traffic Engineering)', '2026-07-09 22:57:25'),
(151, NULL, 16, 'Cập nhật sản phẩm ID 26 - Tên: Bản chất và Hiện tượng', '2026-07-09 23:18:16'),
(152, NULL, 16, 'Cập nhật sản phẩm ID 25 - Tên: English for Information Technology', '2026-07-09 23:19:06'),
(153, NULL, 16, 'Cập nhật sản phẩm ID 24 - Tên: Sapiens - Lược sử loài người', '2026-07-09 23:19:45'),
(154, NULL, 16, 'Cập nhật sản phẩm ID 23 - Tên: Atomic Habits - Thay Đổi Tý Hon, Hiệu Quả Bất Ngờ', '2026-07-09 23:20:14'),
(155, NULL, 16, 'Cập nhật sản phẩm ID 22 - Tên: Bố Già (The Godfather)', '2026-07-09 23:20:52'),
(156, NULL, 16, 'Cập nhật sản phẩm ID 21 - Tên: Số Đỏ', '2026-07-09 23:24:06'),
(157, NULL, 16, 'Cập nhật sản phẩm ID 20 - Tên: Giáo trình Phân tích và Thiết kế Hệ thống', '2026-07-09 23:26:01'),
(158, NULL, 16, 'Cập nhật sản phẩm ID 19 - Tên: Phân tích dữ liệu Thương mại điện tử', '2026-07-09 23:26:29'),
(159, NULL, 16, 'Cập nhật sản phẩm ID 18 - Tên: Kiến trúc Smartphone Hiện Đại', '2026-07-09 23:27:16'),
(160, NULL, 16, 'Cập nhật sản phẩm ID 18 - Tên: Kiến trúc Smartphone Hiện Đại', '2026-07-10 00:12:32'),
(161, NULL, 16, 'Cập nhật sản phẩm ID 18 - Tên: Kiến trúc Smartphone Hiện Đại', '2026-07-10 00:13:07'),
(162, NULL, 16, 'Cập nhật sản phẩm ID 17 - Tên: Tự động hóa với Selenium và Python', '2026-07-10 00:13:52'),
(163, NULL, 16, 'Cập nhật sản phẩm ID 16 - Tên: Tổng quan Hệ thống thu phí tự động (ETC)', '2026-07-10 00:14:25'),
(164, NULL, 16, 'Cập nhật sản phẩm ID 15 - Tên: Phép biện chứng duy vật trong Kỷ nguyên số', '2026-07-10 00:16:33'),
(165, NULL, 16, 'Cập nhật sản phẩm ID 14 - Tên: Thiết kế Web chuẩn Responsive với Bootstrap', '2026-07-10 00:18:20'),
(166, NULL, 16, 'Cập nhật sản phẩm ID 13 - Tên: Quản trị mạng Cisco Thực Hành (CCNA)', '2026-07-10 00:18:58'),
(167, NULL, 16, 'Cập nhật sản phẩm ID 12 - Tên: Cấu trúc dữ liệu và Giải thuật với C++', '2026-07-10 00:19:35');

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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `voucher`
--

DROP TABLE IF EXISTS `voucher`;
CREATE TABLE IF NOT EXISTS `voucher` (
  `VoucherID` int NOT NULL AUTO_INCREMENT,
  `VoucherCode` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
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
