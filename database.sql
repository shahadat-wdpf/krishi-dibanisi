-- Database: `krishi_dibanisi`
SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

-- Table structure for table `users`
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','farmer','customer') DEFAULT 'customer',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dumping data for table `users` (admin password is 'password123' hashed with bcrypt)
INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`) VALUES
(1, 'Admin User', 'admin@krishidibanisi.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Table structure for table `categories`
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `icon` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dumping data for table `categories`
INSERT INTO `categories` (`id`, `name`, `slug`, `icon`) VALUES
(1, 'চাল ও ডাল', 'rice-lentils', '🌾'),
(2, 'শাকসবজি', 'vegetables', '🥦'),
(3, 'ফলমূল', 'fruits', '🍎'),
(4, 'মধু ও ঘি', 'honey-ghee', '🍯'),
(5, 'দেশি হাঁস-মুরগি', 'deshi-has-murgi', '🐔'),
(6, 'ডিম', 'dim', '🥚'),
(7, 'গরু-ছাগলের মাংস', 'meat', '🥩'),
(8, 'দেশি মাছ', 'deshi-mas', '🐟'),
(9, 'ডেইরি পণ্য', 'dairy-ponno', '🥛');

-- Table structure for table `products`
CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `farmer_id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` text,
  `price` decimal(10,2) NOT NULL,
  `unit` varchar(50) DEFAULT 'kg', -- e.g., kg, liter, piece
  `stock` int(11) DEFAULT 0,
  `image` varchar(255) DEFAULT 'default.jpg',
  `is_featured` tinyint(1) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `fk_category` (`category_id`),
  KEY `fk_farmer` (`farmer_id`),
  CONSTRAINT `fk_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_farmer` FOREIGN KEY (`farmer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dumping sample data for table `products`
INSERT INTO `products` (`category_id`, `farmer_id`, `name`, `description`, `price`, `unit`, `stock`, `image`, `is_featured`) VALUES
(1, 1, 'খাঁটি চিনিগুঁড়া চাল', 'দিনাজপুরের সেরা মানের সুগন্ধি চিনিগুঁড়া চাল। পোলাও, বিরিয়ানি বা পায়েস তৈরির জন্য দারুণ।', 140.00, 'kg', 50, 'chinigura.jpg', 1),
(4, 1, 'সুন্দরবনের খাঁটি মধু', 'সরাসরি সুন্দরবনের চাক থেকে কাটা ১০০% বিশুদ্ধ মধু।', 850.00, 'kg', 20, 'honey.jpg', 1),
(2, 1, 'তাজা করলা', 'আমাদের নিজস্ব জমিতে উৎপাদিত সম্পূর্ণ বিষমুক্ত তাজা করলা।', 60.00, 'kg', 30, 'bitter-gourd.jpg', 0),
(4, 1, 'গাওয়া ঘি', 'গ্রামের ঘানি ভাঙা খাঁটি সরিষার তেল ও গরুর দুধ থেকে তৈরি গাওয়া ঘি।', 1200.00, 'kg', 10, 'ghee.jpg', 1);

-- Table structure for table `orders`
CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
  `shipping_address` text NOT NULL,
  `payment_method` varchar(50) DEFAULT 'Cash on Delivery',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_user_order` (`user_id`),
  CONSTRAINT `fk_user_order` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for table `order_items`
CREATE TABLE `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_order` (`order_id`),
  KEY `fk_product` (`product_id`),
  CONSTRAINT `fk_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
