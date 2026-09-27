-- Database: `krishi_dibanisi` (or your live database name)
-- Table Prefix: `kd_`

SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `kd_users`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `kd_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','farmer','customer') DEFAULT 'customer',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dumping default admin for `kd_users` (password is 'password' hashed with bcrypt)
INSERT IGNORE INTO `kd_users` (`id`, `name`, `email`, `password`, `role`) VALUES
(1, 'Admin User', 'admin@krishidibanisi.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- --------------------------------------------------------
-- Table structure for table `kd_categories`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `kd_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `icon` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dumping categories for `kd_categories`
INSERT IGNORE INTO `kd_categories` (`id`, `name`, `slug`, `icon`) VALUES
(1, 'চাল ও ডাল', 'rice-lentils', '🌾'),
(2, 'শাকসবজি', 'vegetables', '🥦'),
(3, 'ফলমূল', 'fruits', '🍎'),
(4, 'মধু ও ঘি', 'honey-ghee', '🍯'),
(5, 'দেশি হাঁস-মুরগি', 'deshi-has-murgi', '🐔'),
(6, 'ডিম', 'dim', '🥚'),
(7, 'গরু-ছাগলের মাংস', 'meat', '🥩'),
(8, 'দেশি মাছ', 'deshi-mas', '🐟'),
(9, 'ডেইরি পণ্য', 'dairy-ponno', '🥛');

-- --------------------------------------------------------
-- Table structure for table `kd_products`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `kd_products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `farmer_id` int(11) NOT NULL DEFAULT 1,
  `name` varchar(200) NOT NULL,
  `description` text,
  `price` decimal(10,2) NOT NULL,
  `unit` varchar(50) DEFAULT 'kg',
  `stock` int(11) DEFAULT 0,
  `image` varchar(255) DEFAULT 'default.jpg',
  `is_featured` tinyint(1) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `fk_kd_category` (`category_id`),
  KEY `fk_kd_farmer` (`farmer_id`),
  CONSTRAINT `fk_kd_category` FOREIGN KEY (`category_id`) REFERENCES `kd_categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_kd_farmer` FOREIGN KEY (`farmer_id`) REFERENCES `kd_users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dumping sample products for `kd_products`
INSERT IGNORE INTO `kd_products` (`category_id`, `farmer_id`, `name`, `description`, `price`, `unit`, `stock`, `image`, `is_featured`) VALUES
(1, 1, 'খাঁটি চিনিগুঁড়া চাল', 'দিনাজপুরের সেরা মানের সুগন্ধি চিনিগুঁড়া চাল। পোলাও, বিরিয়ানি বা পায়েস তৈরির জন্য দারুণ।', 140.00, 'kg', 50, 'chinigura.jpg', 1),
(4, 1, 'সুন্দরবনের খাঁটি মধু', 'সরাসরি সুন্দরবনের চাক থেকে কাটা ১০০% বিশুদ্ধ মধু।', 850.00, 'kg', 20, 'honey.jpg', 1),
(2, 1, 'তাজা করলা', 'আমাদের নিজস্ব জমিতে উৎপাদিত সম্পূর্ণ বিষমুক্ত তাজা করলা।', 60.00, 'kg', 30, 'bitter-gourd.jpg', 0),
(4, 1, 'গাওয়া ঘি', 'গ্রামের ঘানি ভাঙা খাঁটি সরিষার তেল ও গরুর দুধ থেকে তৈরি গাওয়া ঘি।', 1200.00, 'kg', 10, 'ghee.jpg', 1);

-- --------------------------------------------------------
-- Table structure for table `kd_orders`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `kd_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
  `shipping_address` text NOT NULL,
  `payment_method` varchar(50) DEFAULT 'Cash on Delivery',
  `payment_number` varchar(20) DEFAULT NULL,
  `trx_id` varchar(100) DEFAULT NULL,
  `coupon_code` varchar(50) DEFAULT NULL,
  `discount_amount` decimal(10,2) DEFAULT '0.00',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_kd_user_order` (`user_id`),
  CONSTRAINT `fk_kd_user_order` FOREIGN KEY (`user_id`) REFERENCES `kd_users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `kd_order_items`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `kd_order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_kd_order` (`order_id`),
  KEY `fk_kd_product` (`product_id`),
  CONSTRAINT `fk_kd_order` FOREIGN KEY (`order_id`) REFERENCES `kd_orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_kd_product` FOREIGN KEY (`product_id`) REFERENCES `kd_products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `kd_blogs`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `kd_blogs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `excerpt` varchar(500) DEFAULT NULL,
  `image` varchar(255) DEFAULT 'default_blog.jpg',
  `type` enum('farming_tips','farmer_story') NOT NULL DEFAULT 'farming_tips',
  `author` varchar(100) DEFAULT 'কৃষি দিবানিশি',
  `tags` varchar(255) DEFAULT NULL,
  `is_published` tinyint(1) DEFAULT 1,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `kd_coupons`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `kd_coupons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `type` enum('fixed', 'percentage') NOT NULL DEFAULT 'fixed',
  `discount_value` decimal(10,2) NOT NULL,
  `min_order_amount` decimal(10,2) DEFAULT '0.00',
  `is_first_purchase` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO `kd_coupons` (`code`, `type`, `discount_value`, `min_order_amount`, `is_first_purchase`, `is_active`) VALUES 
('KRISHI10', 'percentage', 10.00, 500.00, 0, 1),
('DISCOUNT50', 'fixed', 50.00, 200.00, 0, 1);

-- --------------------------------------------------------
-- Table structure for table `kd_settings`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `kd_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO `kd_settings` (`setting_key`, `setting_value`) VALUES
('site_name', 'কৃষি দিবানিশি'),
('contact_email', 'info@krishidibanisi.com'),
('contact_phone', '+880 171XXXXXXX'),
('contact_address', 'ঢাকা, বাংলাদেশ'),
('bkash_number', '+880 171XXXXXXX'),
('delivery_charge', '60'),
('facebook_url', '#'),
('youtube_url', '#');

-- --------------------------------------------------------
-- Table structure for table `kd_messages`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `kd_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
