-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 17, 2025 at 10:40 PM
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
-- Database: `procurement_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `head_id` int(11) DEFAULT NULL,
  `base_role` varchar(50) NOT NULL DEFAULT 'Employee',
  `base_role_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `name`, `head_id`, `base_role`, `base_role_id`) VALUES
(1, 'ผู้ดูแลระบบ', 1, 'Admin', 3),
(2, 'พนักงาน', 6, 'Employee', 1),
(3, 'พนักงานจัดซื้อ', 7, 'Purchasing', 2);

-- --------------------------------------------------------

--
-- Table structure for table `department_roles`
--

CREATE TABLE `department_roles` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `head_role_name` enum('DeptHead','PurchasingHead','Admin') NOT NULL DEFAULT 'DeptHead'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `department_roles`
--

INSERT INTO `department_roles` (`id`, `name`, `head_role_name`) VALUES
(1, 'Employee', 'DeptHead'),
(2, 'Purchasing', 'PurchasingHead'),
(3, 'Admin', 'Admin');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `dept_id` int(11) DEFAULT NULL,
  `role_id` int(11) DEFAULT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'Employee',
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `name`, `phone`, `email`, `dept_id`, `role_id`, `role`, `password`) VALUES
(1, 'Admin', '0999999999', 'admin@tsc.com', 1, 1, 'Admin', '$2y$10$CXqxgnRdoy4IAq.fogoj..w2V5omGTrMJgcnN9kFrZVafoxZqA0Qm'),
(2, 'Employee', '0999999998', 'employee@tsc.com', 2, 2, 'Employee', '$2y$10$G6usLNHkixn.FgDkJOalM.XL.P5NY7M6ju0oe9hF2y45OlQ/aNIlq'),
(4, 'Purchasing', '0999999996', 'purchasing@tsc.com', 3, 4, 'Purchasing', '$2y$10$qODTryex3A6SkkMj5yMaOO/vwP6WLXffK5q5Sf.A/DCJd6IHGF9AC'),
(5, 'PurchasingHead', '0999999995', 'purchasinghead@tsc.com', 3, 5, 'PurchasingHead', '$2y$10$MbwXQf5nGqmb1LwUbwdlwe1HCXb0BbBi.GaciowWNrkpVWwQkCEDi'),
(6, 'Supahkit Weeraphan', '0935560964', 'tess@gmail.com', 2, 3, 'DeptHead', '$2y$10$wV5SEYOWltq9S7xZ5a6BTeovTMTAKv4iUVDMaChPLrK9iyBPu2IJa'),
(7, 'dept', '0999999997', 'dept@tsc.com', 3, 5, 'Employee', '$2y$10$x5pYPLqFa9O1refDsa1I/.5YwfeY/t81Qc9AN31Mi3rupSIEswM/.'),
(8, 'Test', '0999999991', 'test@tsc.com', 2, 2, 'Employee', '$2y$10$syFQ7r4MSSjlbxb09jMMYOD7/qeJYike/LlzuCp0caQ2cfrJIj3k.');

-- --------------------------------------------------------

--
-- Table structure for table `payment_types`
--

CREATE TABLE `payment_types` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `payment_types`
--

INSERT INTO `payment_types` (`id`, `name`) VALUES
(1, 'COD'),
(2, 'PromptPay');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `product_type_id` int(11) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `min_stock` int(11) NOT NULL DEFAULT 0,
  `unit_price` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `product_type_id`, `details`, `quantity`, `min_stock`, `unit_price`) VALUES
(1, 'ของฝาก', 3, 'ของฝาก', 1000, 100, 20.00),
(2, 'Notebook Starter', 2, '', 100, 10, 14900.00);

-- --------------------------------------------------------

--
-- Table structure for table `product_types`
--

CREATE TABLE `product_types` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `product_types`
--

INSERT INTO `product_types` (`id`, `name`) VALUES
(1, 'POS'),
(2, 'IT'),
(3, 'Gift');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_orders`
--

CREATE TABLE `purchase_orders` (
  `id` int(11) NOT NULL,
  `quotation_id` int(11) NOT NULL,
  `order_date` datetime NOT NULL,
  `status` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `purchase_orders`
--

INSERT INTO `purchase_orders` (`id`, `quotation_id`, `order_date`, `status`) VALUES
(1, 1, '2025-09-18 01:38:47', 'PendingApproval');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_requests`
--

CREATE TABLE `purchase_requests` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `request_date` datetime NOT NULL,
  `status` varchar(50) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `reason` text DEFAULT NULL,
  `qty` decimal(12,2) NOT NULL DEFAULT 0.00,
  `approved_by_head_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `purchase_requests`
--

INSERT INTO `purchase_requests` (`id`, `employee_id`, `request_date`, `status`, `created_at`, `reason`, `qty`, `approved_by_head_at`) VALUES
(1, 2, '2025-09-17 22:03:32', 'Ordered', '2025-09-17 22:09:34', 'อยากได้อะ', 0.00, NULL),
(4, 2, '2025-09-17 22:12:15', 'ApprovedByDeptHead', '2025-09-17 22:12:15', 'ของขาด', 0.00, NULL),
(5, 8, '2025-09-18 02:17:47', 'Pending', '2025-09-18 02:17:47', 'สินค้าใกล้หมด stock', 0.00, NULL),
(12, 8, '2025-09-18 02:45:14', 'RejectedByDeptHead', '2025-09-18 02:45:14', 'สินค้าใกล้หมด stock', 0.00, '2025-09-18 02:49:30'),
(15, 8, '0000-00-00 00:00:00', 'Pending', '2025-09-18 03:02:49', 'เบิ่ดแล้ว ของหน่ะ', 0.00, NULL),
(16, 8, '0000-00-00 00:00:00', 'ApprovedByDeptHead', '2025-09-18 03:18:31', 'ของเป็นที่้ต้องการ', 0.00, '2025-09-18 03:18:41');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_request_items`
--

CREATE TABLE `purchase_request_items` (
  `id` int(11) NOT NULL,
  `purchase_request_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `purchase_request_items`
--

INSERT INTO `purchase_request_items` (`id`, `purchase_request_id`, `product_id`, `quantity`) VALUES
(1, 1, 1, 10),
(2, 5, 1, 20),
(3, 5, 1, 5),
(6, 15, 1, 12000),
(7, 16, 2, 50);

-- --------------------------------------------------------

--
-- Table structure for table `purchase_tax_reports`
--

CREATE TABLE `purchase_tax_reports` (
  `id` int(11) NOT NULL,
  `purchase_order_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quotations`
--

CREATE TABLE `quotations` (
  `id` int(11) NOT NULL,
  `purchase_request_id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `quote_date` datetime NOT NULL,
  `status` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `quotations`
--

INSERT INTO `quotations` (`id`, `purchase_request_id`, `seller_id`, `quote_date`, `status`) VALUES
(1, 1, 1, '2025-09-18 01:34:49', 'Selected');

-- --------------------------------------------------------

--
-- Table structure for table `quotation_items`
--

CREATE TABLE `quotation_items` (
  `id` int(11) NOT NULL,
  `quotation_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `quotation_items`
--

INSERT INTO `quotation_items` (`id`, `quotation_id`, `product_id`, `quantity`, `price`) VALUES
(1, 1, 1, 10, 10.00);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`) VALUES
(1, 'Admin'),
(3, 'DeptHead'),
(2, 'Employee'),
(4, 'Purchasing'),
(5, 'PurchasingHead');

-- --------------------------------------------------------

--
-- Table structure for table `sellers`
--

CREATE TABLE `sellers` (
  `id` int(11) NOT NULL,
  `contact_name` varchar(100) NOT NULL,
  `company_name` varchar(100) NOT NULL,
  `address` text DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `sellers`
--

INSERT INTO `sellers` (`id`, `contact_name`, `company_name`, `address`, `phone`, `email`, `password`, `status`) VALUES
(1, 'Supahkit Weeraphan', 'TSC Dev co,ltd', 'Mahasarakham University', '0935560964', 'tesspitch@gmail.com', '$2y$10$FpUuCDFKNjiWwLcBWxStmOsQYyp7Ww9/zzZua/5zFAfObkPSOETrq', 'Active');

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_employee_effective_role`
-- (See below for the actual view)
--
CREATE TABLE `v_employee_effective_role` (
`employee_id` int(11)
,`name` varchar(100)
,`email` varchar(100)
,`dept_id` int(11)
,`effective_role` varchar(14)
);

-- --------------------------------------------------------

--
-- Structure for view `v_employee_effective_role`
--
DROP TABLE IF EXISTS `v_employee_effective_role`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_employee_effective_role`  AS SELECT `e`.`id` AS `employee_id`, `e`.`name` AS `name`, `e`.`email` AS `email`, `e`.`dept_id` AS `dept_id`, CASE WHEN `d`.`base_role` = 'Admin' THEN 'Admin' WHEN `e`.`id` = `d`.`head_id` AND `d`.`base_role` = 'Purchasing' THEN 'PurchasingHead' WHEN `d`.`base_role` = 'Purchasing' THEN 'Purchasing' WHEN `e`.`id` = `d`.`head_id` THEN 'DeptHead' ELSE 'Employee' END AS `effective_role` FROM (`employees` `e` left join `departments` `d` on(`d`.`id` = `e`.`dept_id`)) ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `head_id` (`head_id`),
  ADD KEY `fk_departments_base_role` (`base_role_id`);

--
-- Indexes for table `department_roles`
--
ALTER TABLE `department_roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_employee_role` (`role_id`);

--
-- Indexes for table `payment_types`
--
ALTER TABLE `payment_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_type_id` (`product_type_id`);

--
-- Indexes for table `product_types`
--
ALTER TABLE `product_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quotation_id` (`quotation_id`);

--
-- Indexes for table `purchase_requests`
--
ALTER TABLE `purchase_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `purchase_request_items`
--
ALTER TABLE `purchase_request_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_request_id` (`purchase_request_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `purchase_tax_reports`
--
ALTER TABLE `purchase_tax_reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_order_id` (`purchase_order_id`);

--
-- Indexes for table `quotations`
--
ALTER TABLE `quotations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_request_id` (`purchase_request_id`),
  ADD KEY `seller_id` (`seller_id`);

--
-- Indexes for table `quotation_items`
--
ALTER TABLE `quotation_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quotation_id` (`quotation_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `sellers`
--
ALTER TABLE `sellers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `department_roles`
--
ALTER TABLE `department_roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `payment_types`
--
ALTER TABLE `payment_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `product_types`
--
ALTER TABLE `product_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `purchase_requests`
--
ALTER TABLE `purchase_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `purchase_request_items`
--
ALTER TABLE `purchase_request_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `purchase_tax_reports`
--
ALTER TABLE `purchase_tax_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quotations`
--
ALTER TABLE `quotations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `quotation_items`
--
ALTER TABLE `quotation_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `sellers`
--
ALTER TABLE `sellers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `departments`
--
ALTER TABLE `departments`
  ADD CONSTRAINT `departments_ibfk_1` FOREIGN KEY (`head_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_departments_base_role` FOREIGN KEY (`base_role_id`) REFERENCES `department_roles` (`id`);

--
-- Constraints for table `employees`
--
ALTER TABLE `employees`
  ADD CONSTRAINT `fk_employee_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`product_type_id`) REFERENCES `product_types` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD CONSTRAINT `purchase_orders_ibfk_1` FOREIGN KEY (`quotation_id`) REFERENCES `quotations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `purchase_requests`
--
ALTER TABLE `purchase_requests`
  ADD CONSTRAINT `purchase_requests_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `purchase_request_items`
--
ALTER TABLE `purchase_request_items`
  ADD CONSTRAINT `purchase_request_items_ibfk_1` FOREIGN KEY (`purchase_request_id`) REFERENCES `purchase_requests` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchase_request_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `purchase_tax_reports`
--
ALTER TABLE `purchase_tax_reports`
  ADD CONSTRAINT `purchase_tax_reports_ibfk_1` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quotations`
--
ALTER TABLE `quotations`
  ADD CONSTRAINT `quotations_ibfk_1` FOREIGN KEY (`purchase_request_id`) REFERENCES `purchase_requests` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `quotations_ibfk_2` FOREIGN KEY (`seller_id`) REFERENCES `sellers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quotation_items`
--
ALTER TABLE `quotation_items`
  ADD CONSTRAINT `quotation_items_ibfk_1` FOREIGN KEY (`quotation_id`) REFERENCES `quotations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `quotation_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
