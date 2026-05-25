-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 25, 2026 at 09:02 AM
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
-- Database: `car_rental_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(20) DEFAULT 'admin',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `email`, `password`, `role`, `created_at`) VALUES
(2, 'admin', 'admin@velocityrentals.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'super_admin', '2026-05-19 21:27:20');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `booking_no` varchar(20) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `vehicle_id` int(11) NOT NULL,
  `pickup_date` date NOT NULL,
  `return_date` date NOT NULL,
  `total_days` int(11) DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `customer_name` varchar(100) NOT NULL,
  `customer_email` varchar(100) NOT NULL,
  `customer_phone` varchar(20) NOT NULL,
  `status` enum('pending','confirmed','completed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `booking_no`, `user_id`, `vehicle_id`, `pickup_date`, `return_date`, `total_days`, `total_price`, `customer_name`, `customer_email`, `customer_phone`, `status`, `created_at`) VALUES
(1, 'VEL-6A0CF2B3CA7E4-88', 4, 7, '2026-05-21', '2026-05-26', 5, 2499.95, 'chadrack Bosimwenda', 'chadibosi34@gmail.com', '+254851600109', 'confirmed', '2026-05-19 23:30:59'),
(2, 'VEL-6A0CF2D122774-72', 4, 7, '2026-05-21', '2026-05-26', 5, 2499.95, 'chadrack Bosimwenda', 'chadibosi34@gmail.com', '+254851600109', 'cancelled', '2026-05-19 23:31:29'),
(5, 'VEL-6A0CF418691F0-46', 4, 6, '2026-05-21', '2026-05-24', 3, 689.97, 'chadrack Bosimwenda', 'chadibosi34@gmail.com', '+254851600109', 'pending', '2026-05-19 23:36:56');

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `subject` varchar(200) DEFAULT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contacts`
--

INSERT INTO `contacts` (`id`, `name`, `email`, `subject`, `message`, `is_read`, `created_at`) VALUES
(1, 'Jonathan Bosimwenda', 'Estherlakadia2@dsdggfu', 'Complain', 'how are you doing?', 0, '2026-05-19 23:15:33');

-- --------------------------------------------------------

--
-- Table structure for table `subscribers`
--

CREATE TABLE `subscribers` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `subscribed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `testimonials`
--

CREATE TABLE `testimonials` (
  `id` int(11) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `user_email` varchar(100) DEFAULT NULL,
  `user_image` varchar(255) DEFAULT NULL,
  `rating` int(11) DEFAULT 5,
  `comment` text NOT NULL,
  `is_approved` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `testimonials`
--

INSERT INTO `testimonials` (`id`, `user_name`, `user_email`, `user_image`, `rating`, `comment`, `is_approved`, `created_at`) VALUES
(1, 'Michael Chen', 'michael@email.com', NULL, 5, 'Absolutely incredible service! The Tesla Model S was immaculate and the pickup process was seamless. Highly recommend Velocity Rentals!', 1, '2026-05-19 20:03:48'),
(2, 'Sarah Williams', 'sarah@email.com', NULL, 5, 'Best car rental experience I\'ve ever had. The Porsche 911 made my birthday weekend unforgettable. Professional staff and premium vehicles.', 1, '2026-05-19 20:03:48'),
(3, 'David Kim', 'david@email.com', NULL, 4, 'Great selection of luxury cars. The booking process was easy and customer support was responsive. Will definitely use again.', 1, '2026-05-19 20:03:48');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `full_name`, `phone`, `role`, `is_active`, `created_at`) VALUES
(1, 'Jonathan', 'josbosimwenda@gmail.com', '$2y$10$ARJthF6lGCHcDYZxwJfZ5uGbnHRALAYipQPh1pv4odzxofGVRmBhi', 'Jonathan Bosimwenda', '0768062600', 'admin', 1, '2026-05-19 20:22:12'),
(2, 'admin', 'admin@velocityrentals.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', NULL, 'admin', 1, '2026-05-19 21:31:32'),
(4, 'Chadi', 'chadibosi34@gmail.com', '$2y$10$zN73nELcsmVWBLRArOGsveULPx3.iw6du8Ij4Oet1VvSpG/OLQZGC', 'chadrack Bosimwenda', '+254851600109', 'user', 1, '2026-05-19 23:21:37');

-- --------------------------------------------------------

--
-- Table structure for table `vehicles`
--

CREATE TABLE `vehicles` (
  `id` int(11) NOT NULL,
  `brand` varchar(50) NOT NULL,
  `model` varchar(50) NOT NULL,
  `year` int(11) DEFAULT 2023,
  `transmission` varchar(20) DEFAULT 'Automatic',
  `fuel_type` varchar(20) DEFAULT 'Petrol',
  `seats` int(11) DEFAULT 5,
  `rental_price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT 'assets/images/default-car.jpg',
  `availability` tinyint(1) DEFAULT 1,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vehicles`
--

INSERT INTO `vehicles` (`id`, `brand`, `model`, `year`, `transmission`, `fuel_type`, `seats`, `rental_price`, `image`, `availability`, `description`, `created_at`) VALUES
(1, 'Tesla', 'Model S Plaid', 2024, 'Automatic', 'Electric', 5, 149.99, '../uploads/1779230376_50a96367d6aabecdc7c49cbc14c17174.jpg', 1, 'Luxury electric sedan with 1020 hp, 0-60 in 1.99s', '2026-05-19 20:03:48'),
(2, 'BMW', 'X7 M60i', 2024, 'Automatic', 'Petrol', 7, 189.99, '../uploads/1779230315_fb55d2880cfc411c3ec69e7e0ba79d81.jpg', 1, 'Full-size luxury SUV with V8 engine', '2026-05-19 20:03:48'),
(3, 'Mercedes-Benz', 'S-Class', 2024, 'Automatic', 'Petrol', 5, 199.99, '../uploads/1779230247_6af4a817968ab68792f36d220c33a450.jpg', 1, 'The ultimate luxury sedan', '2026-05-19 20:03:48'),
(4, 'Porsche', '911 Turbo S', 2024, 'Automatic', 'Petrol', 4, 299.99, '../uploads/1779230163_36bbf18e6bea53ee9ba551934ae2a204.jpg', 1, 'Iconic sports car with 640 hp', '2026-05-19 20:03:48'),
(5, 'Audi', 'Q8 e-tron', 2024, 'Automatic', 'Electric', 5, 159.99, '../uploads/1779230078_6e8a0b9efda59788ae751160a00288b4.jpg', 1, 'Electric luxury SUV', '2026-05-19 20:03:48'),
(6, 'Range Rover', 'Sport', 2024, 'Automatic', 'Diesel', 5, 229.99, '../uploads/1779230021_2f2b4e360166b532a26dac0e89bb2918.jpg', 1, 'British luxury SUV', '2026-05-19 20:03:48'),
(7, 'Ferrari', 'SF90 Stradale', 2024, 'Automatic', 'Hybrid', 2, 499.99, '../uploads/1779229932_ab99caf93b52c1568af1e3051b44bb72.jpg', 1, '986 hp hybrid supercar', '2026-05-19 20:03:48'),
(8, 'Lamborghini', 'Urus', 2024, 'Automatic', 'Petrol', 5, 399.99, '../uploads/1779229872_fa489d35aa36be80dded689815733654.jpg', 1, 'Super SUV', '2026-05-19 20:03:48');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `booking_no` (`booking_no`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `vehicle_id` (`vehicle_id`);

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subscribers`
--
ALTER TABLE `subscribers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `testimonials`
--
ALTER TABLE `testimonials`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `subscribers`
--
ALTER TABLE `subscribers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `testimonials`
--
ALTER TABLE `testimonials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `vehicles`
--
ALTER TABLE `vehicles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
