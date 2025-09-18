-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 17, 2025 at 08:07 PM
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
-- Database: `dbgo`
--
CREATE DATABASE IF NOT EXISTS `dbgo` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `dbgo`;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(5) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`) VALUES
(1, 'Peter', 'peter.m@msu.ac.th'),
(2, 'onizuka', 'oni.v@msu.ac.th\r\n'),
(3, 'Tesspitch', 'tesspitch@gmail.com'),
(4, 'spkwrp', 'spkwrp@gmail.com'),
(5, 'amp', 'spkwrp@gmail.com'),
(6, 'aaa', 'spaaap@gmail.com'),
(7, 'ABC', 'ABC@gmail.com');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- Database: `eprocurement`
--
CREATE DATABASE IF NOT EXISTS `eprocurement` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `eprocurement`;

-- --------------------------------------------------------

--
-- Table structure for table `bounce`
--

CREATE TABLE `bounce` (
  `bid` int(11) NOT NULL,
  `bname` varchar(100) NOT NULL,
  `bprice` decimal(10,2) NOT NULL,
  `b_qty` int(11) NOT NULL,
  `sid` int(11) NOT NULL,
  `tid` int(11) NOT NULL,
  `dateTime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bounce`
--

INSERT INTO `bounce` (`bid`, `bname`, `bprice`, `b_qty`, `sid`, `tid`, `dateTime`) VALUES
(1, 'milk', 10.00, 4, 1, 1, '2025-03-30 10:18:50'),
(2, 'PR BigBag', 20.00, 100, 4, 4, '2025-03-30 10:18:50'),
(3, 'Lactasoy', 10.00, 100, 6, 4, '2025-03-30 10:18:50'),
(4, 'asdsadsad', 20.00, 100, 8, 7, '2025-03-30 10:18:50'),
(5, 'PC', 10000.00, 30, 10, 9, '2025-03-31 04:36:47'),
(6, 'GPU', 10000.00, 30, 10, 9, '2025-03-31 04:36:48'),
(7, 'HHD', 10000.00, 30, 10, 9, '2025-03-31 04:37:18');

-- --------------------------------------------------------

--
-- Table structure for table `order_supplier`
--

CREATE TABLE `order_supplier` (
  `osid` int(11) NOT NULL,
  `osname` varchar(100) NOT NULL,
  `osprice` decimal(10,2) NOT NULL,
  `os_qty` int(11) NOT NULL,
  `sid` int(11) NOT NULL,
  `tid` int(11) NOT NULL,
  `dateTime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `pid` int(11) NOT NULL,
  `pname` varchar(100) NOT NULL,
  `pprice` decimal(10,2) NOT NULL,
  `p_qty` int(11) NOT NULL,
  `sid` int(11) NOT NULL,
  `tid` int(11) NOT NULL,
  `dateTime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`pid`, `pname`, `pprice`, `p_qty`, `sid`, `tid`, `dateTime`) VALUES
(1, 'PR BigBag', 20.00, 120, 4, 6, '2025-03-30 09:20:36'),
(4, 'Cuury ', 20.00, 100, 8, 7, '2025-03-30 09:20:36'),
(5, 'Chocolate', 29.00, 22, 4, 6, '2025-03-30 09:20:36'),
(6, 'PC', 39000.00, 16, 9, 8, '2025-03-30 09:20:36'),
(7, 'PC', 39000.00, 10, 8, 9, '2025-03-30 09:20:36'),
(8, 'GPU', 29000.00, 10, 10, 8, '2025-03-30 09:20:36'),
(9, 'TV', 25000.00, 10, 8, 8, '2025-03-30 09:20:36'),
(10, 'Ram', 3990.00, 100, 10, 9, '2025-03-30 10:11:08'),
(11, 'Ram', 10000.00, 30, 10, 9, '2025-03-31 04:37:14');

-- --------------------------------------------------------

--
-- Table structure for table `receive`
--

CREATE TABLE `receive` (
  `rid` int(11) NOT NULL,
  `rname` varchar(100) NOT NULL,
  `rprice` decimal(10,2) NOT NULL,
  `r_qty` int(11) NOT NULL,
  `sid` int(11) NOT NULL,
  `tid` int(11) NOT NULL,
  `dateTime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `receive`
--

INSERT INTO `receive` (`rid`, `rname`, `rprice`, `r_qty`, `sid`, `tid`, `dateTime`) VALUES
(1, 'PR BigBag', 20.00, 120, 4, 4, '2025-03-30 09:20:48'),
(2, 'Vitamilk', 15.00, 200, 6, 5, '2025-03-30 09:20:48'),
(4, 'Cuury ', 20.00, 100, 8, 7, '2025-03-30 09:20:48'),
(5, 'PC', 39000.00, 16, 6, 6, '2025-03-30 09:20:48'),
(6, 'PC', 39000.00, 10, 8, 9, '2025-03-30 09:20:48'),
(7, 'GPU', 29000.00, 10, 10, 8, '2025-03-30 09:20:48'),
(8, 'TV', 25000.00, 10, 8, 8, '2025-03-30 09:20:48'),
(9, 'Ram', 3990.00, 100, 10, 9, '2025-03-30 10:11:08'),
(10, 'Ram', 10000.00, 30, 10, 9, '2025-03-31 04:37:14');

--
-- Triggers `receive`
--
DELIMITER $$
CREATE TRIGGER `after_receive_insert` AFTER INSERT ON `receive` FOR EACH ROW BEGIN
    INSERT INTO product (pname, pprice, p_qty, sid, tid)
    VALUES (NEW.rname, NEW.rprice, NEW.r_qty, NEW.sid, NEW.tid);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `supplier`
--

CREATE TABLE `supplier` (
  `sid` int(11) NOT NULL,
  `sname` varchar(255) NOT NULL,
  `contact` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `supplier`
--

INSERT INTO `supplier` (`sid`, `sname`, `contact`) VALUES
(4, 'CP All', '02-232-1233'),
(6, 'ThaiBev', '02-222-2822'),
(8, 'AuProduction', '099-989-9009'),
(9, 'Advice', '02-222-1111'),
(10, 'I Have Cpu', '02-287-9876');

-- --------------------------------------------------------

--
-- Table structure for table `type`
--

CREATE TABLE `type` (
  `tid` int(11) NOT NULL,
  `tname` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `type`
--

INSERT INTO `type` (`tid`, `tname`) VALUES
(4, 'Noodle'),
(5, 'Milk'),
(6, 'Snack'),
(7, 'Food'),
(8, 'Electric'),
(9, 'IT');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bounce`
--
ALTER TABLE `bounce`
  ADD PRIMARY KEY (`bid`),
  ADD KEY `sid` (`sid`),
  ADD KEY `tid` (`tid`);

--
-- Indexes for table `order_supplier`
--
ALTER TABLE `order_supplier`
  ADD PRIMARY KEY (`osid`),
  ADD KEY `fk_sid` (`sid`),
  ADD KEY `fk_tid` (`tid`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`pid`),
  ADD KEY `sid` (`sid`),
  ADD KEY `tid` (`tid`);

--
-- Indexes for table `receive`
--
ALTER TABLE `receive`
  ADD PRIMARY KEY (`rid`),
  ADD KEY `sid` (`sid`),
  ADD KEY `tid` (`tid`);

--
-- Indexes for table `supplier`
--
ALTER TABLE `supplier`
  ADD PRIMARY KEY (`sid`);

--
-- Indexes for table `type`
--
ALTER TABLE `type`
  ADD PRIMARY KEY (`tid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bounce`
--
ALTER TABLE `bounce`
  MODIFY `bid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `order_supplier`
--
ALTER TABLE `order_supplier`
  MODIFY `osid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `pid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `receive`
--
ALTER TABLE `receive`
  MODIFY `rid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `supplier`
--
ALTER TABLE `supplier`
  MODIFY `sid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `type`
--
ALTER TABLE `type`
  MODIFY `tid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `order_supplier`
--
ALTER TABLE `order_supplier`
  ADD CONSTRAINT `fk_sid` FOREIGN KEY (`sid`) REFERENCES `supplier` (`sid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_tid` FOREIGN KEY (`tid`) REFERENCES `type` (`tid`) ON DELETE CASCADE ON UPDATE CASCADE;
--
-- Database: `phpmyadmin`
--
CREATE DATABASE IF NOT EXISTS `phpmyadmin` DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;
USE `phpmyadmin`;

-- --------------------------------------------------------

--
-- Table structure for table `pma__bookmark`
--

CREATE TABLE `pma__bookmark` (
  `id` int(10) UNSIGNED NOT NULL,
  `dbase` varchar(255) NOT NULL DEFAULT '',
  `user` varchar(255) NOT NULL DEFAULT '',
  `label` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `query` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Bookmarks';

-- --------------------------------------------------------

--
-- Table structure for table `pma__central_columns`
--

CREATE TABLE `pma__central_columns` (
  `db_name` varchar(64) NOT NULL,
  `col_name` varchar(64) NOT NULL,
  `col_type` varchar(64) NOT NULL,
  `col_length` text DEFAULT NULL,
  `col_collation` varchar(64) NOT NULL,
  `col_isNull` tinyint(1) NOT NULL,
  `col_extra` varchar(255) DEFAULT '',
  `col_default` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Central list of columns';

-- --------------------------------------------------------

--
-- Table structure for table `pma__column_info`
--

CREATE TABLE `pma__column_info` (
  `id` int(5) UNSIGNED NOT NULL,
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `table_name` varchar(64) NOT NULL DEFAULT '',
  `column_name` varchar(64) NOT NULL DEFAULT '',
  `comment` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `mimetype` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `transformation` varchar(255) NOT NULL DEFAULT '',
  `transformation_options` varchar(255) NOT NULL DEFAULT '',
  `input_transformation` varchar(255) NOT NULL DEFAULT '',
  `input_transformation_options` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Column information for phpMyAdmin';

-- --------------------------------------------------------

--
-- Table structure for table `pma__designer_settings`
--

CREATE TABLE `pma__designer_settings` (
  `username` varchar(64) NOT NULL,
  `settings_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Settings related to Designer';

-- --------------------------------------------------------

--
-- Table structure for table `pma__export_templates`
--

CREATE TABLE `pma__export_templates` (
  `id` int(5) UNSIGNED NOT NULL,
  `username` varchar(64) NOT NULL,
  `export_type` varchar(10) NOT NULL,
  `template_name` varchar(64) NOT NULL,
  `template_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Saved export templates';

-- --------------------------------------------------------

--
-- Table structure for table `pma__favorite`
--

CREATE TABLE `pma__favorite` (
  `username` varchar(64) NOT NULL,
  `tables` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Favorite tables';

-- --------------------------------------------------------

--
-- Table structure for table `pma__history`
--

CREATE TABLE `pma__history` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(64) NOT NULL DEFAULT '',
  `db` varchar(64) NOT NULL DEFAULT '',
  `table` varchar(64) NOT NULL DEFAULT '',
  `timevalue` timestamp NOT NULL DEFAULT current_timestamp(),
  `sqlquery` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='SQL history for phpMyAdmin';

-- --------------------------------------------------------

--
-- Table structure for table `pma__navigationhiding`
--

CREATE TABLE `pma__navigationhiding` (
  `username` varchar(64) NOT NULL,
  `item_name` varchar(64) NOT NULL,
  `item_type` varchar(64) NOT NULL,
  `db_name` varchar(64) NOT NULL,
  `table_name` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Hidden items of navigation tree';

-- --------------------------------------------------------

--
-- Table structure for table `pma__pdf_pages`
--

CREATE TABLE `pma__pdf_pages` (
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `page_nr` int(10) UNSIGNED NOT NULL,
  `page_descr` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='PDF relation pages for phpMyAdmin';

-- --------------------------------------------------------

--
-- Table structure for table `pma__recent`
--

CREATE TABLE `pma__recent` (
  `username` varchar(64) NOT NULL,
  `tables` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Recently accessed tables';

--
-- Dumping data for table `pma__recent`
--

INSERT INTO `pma__recent` (`username`, `tables`) VALUES
('root', '[{\"db\":\"procurement_db\",\"table\":\"employees\"},{\"db\":\"procurement_db\",\"table\":\"departments\"},{\"db\":\"procurement_db\",\"table\":\"roles\"},{\"db\":\"procurement_db\",\"table\":\"purchase_requests\"},{\"db\":\"procurement_db\",\"table\":\"purchase_request_items\"},{\"db\":\"procurement_db\",\"table\":\"products\"},{\"db\":\"procurement_db\",\"table\":\"product_types\"},{\"db\":\"procurement_db\",\"table\":\"payment_types\"},{\"db\":\"procurement_db\",\"table\":\"purchase_orders\"},{\"db\":\"procurement_db\",\"table\":\"purchase_tax_reports\"}]');

-- --------------------------------------------------------

--
-- Table structure for table `pma__relation`
--

CREATE TABLE `pma__relation` (
  `master_db` varchar(64) NOT NULL DEFAULT '',
  `master_table` varchar(64) NOT NULL DEFAULT '',
  `master_field` varchar(64) NOT NULL DEFAULT '',
  `foreign_db` varchar(64) NOT NULL DEFAULT '',
  `foreign_table` varchar(64) NOT NULL DEFAULT '',
  `foreign_field` varchar(64) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Relation table';

-- --------------------------------------------------------

--
-- Table structure for table `pma__savedsearches`
--

CREATE TABLE `pma__savedsearches` (
  `id` int(5) UNSIGNED NOT NULL,
  `username` varchar(64) NOT NULL DEFAULT '',
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `search_name` varchar(64) NOT NULL DEFAULT '',
  `search_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Saved searches';

-- --------------------------------------------------------

--
-- Table structure for table `pma__table_coords`
--

CREATE TABLE `pma__table_coords` (
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `table_name` varchar(64) NOT NULL DEFAULT '',
  `pdf_page_number` int(11) NOT NULL DEFAULT 0,
  `x` float UNSIGNED NOT NULL DEFAULT 0,
  `y` float UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Table coordinates for phpMyAdmin PDF output';

-- --------------------------------------------------------

--
-- Table structure for table `pma__table_info`
--

CREATE TABLE `pma__table_info` (
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `table_name` varchar(64) NOT NULL DEFAULT '',
  `display_field` varchar(64) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Table information for phpMyAdmin';

-- --------------------------------------------------------

--
-- Table structure for table `pma__table_uiprefs`
--

CREATE TABLE `pma__table_uiprefs` (
  `username` varchar(64) NOT NULL,
  `db_name` varchar(64) NOT NULL,
  `table_name` varchar(64) NOT NULL,
  `prefs` text NOT NULL,
  `last_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Tables'' UI preferences';

--
-- Dumping data for table `pma__table_uiprefs`
--

INSERT INTO `pma__table_uiprefs` (`username`, `db_name`, `table_name`, `prefs`, `last_update`) VALUES
('root', 'procurement_db', 'employees', '{\"CREATE_TIME\":\"2025-09-17 21:56:02\",\"col_order\":[0,1,2,3,4,5,6,7],\"col_visib\":[1,1,1,1,1,1,1,1],\"sorted_col\":\"`employees`.`dept_id` ASC\"}', '2025-09-17 18:05:57');

-- --------------------------------------------------------

--
-- Table structure for table `pma__tracking`
--

CREATE TABLE `pma__tracking` (
  `db_name` varchar(64) NOT NULL,
  `table_name` varchar(64) NOT NULL,
  `version` int(10) UNSIGNED NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime NOT NULL,
  `schema_snapshot` text NOT NULL,
  `schema_sql` text DEFAULT NULL,
  `data_sql` longtext DEFAULT NULL,
  `tracking` set('UPDATE','REPLACE','INSERT','DELETE','TRUNCATE','CREATE DATABASE','ALTER DATABASE','DROP DATABASE','CREATE TABLE','ALTER TABLE','RENAME TABLE','DROP TABLE','CREATE INDEX','DROP INDEX','CREATE VIEW','ALTER VIEW','DROP VIEW') DEFAULT NULL,
  `tracking_active` int(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Database changes tracking for phpMyAdmin';

-- --------------------------------------------------------

--
-- Table structure for table `pma__userconfig`
--

CREATE TABLE `pma__userconfig` (
  `username` varchar(64) NOT NULL,
  `timevalue` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `config_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='User preferences storage for phpMyAdmin';

--
-- Dumping data for table `pma__userconfig`
--

INSERT INTO `pma__userconfig` (`username`, `timevalue`, `config_data`) VALUES
('root', '2025-09-17 18:06:44', '{\"Console\\/Mode\":\"collapse\"}');

-- --------------------------------------------------------

--
-- Table structure for table `pma__usergroups`
--

CREATE TABLE `pma__usergroups` (
  `usergroup` varchar(64) NOT NULL,
  `tab` varchar(64) NOT NULL,
  `allowed` enum('Y','N') NOT NULL DEFAULT 'N'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='User groups with configured menu items';

-- --------------------------------------------------------

--
-- Table structure for table `pma__users`
--

CREATE TABLE `pma__users` (
  `username` varchar(64) NOT NULL,
  `usergroup` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Users and their assignments to user groups';

--
-- Indexes for dumped tables
--

--
-- Indexes for table `pma__bookmark`
--
ALTER TABLE `pma__bookmark`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pma__central_columns`
--
ALTER TABLE `pma__central_columns`
  ADD PRIMARY KEY (`db_name`,`col_name`);

--
-- Indexes for table `pma__column_info`
--
ALTER TABLE `pma__column_info`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `db_name` (`db_name`,`table_name`,`column_name`);

--
-- Indexes for table `pma__designer_settings`
--
ALTER TABLE `pma__designer_settings`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `pma__export_templates`
--
ALTER TABLE `pma__export_templates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `u_user_type_template` (`username`,`export_type`,`template_name`);

--
-- Indexes for table `pma__favorite`
--
ALTER TABLE `pma__favorite`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `pma__history`
--
ALTER TABLE `pma__history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `username` (`username`,`db`,`table`,`timevalue`);

--
-- Indexes for table `pma__navigationhiding`
--
ALTER TABLE `pma__navigationhiding`
  ADD PRIMARY KEY (`username`,`item_name`,`item_type`,`db_name`,`table_name`);

--
-- Indexes for table `pma__pdf_pages`
--
ALTER TABLE `pma__pdf_pages`
  ADD PRIMARY KEY (`page_nr`),
  ADD KEY `db_name` (`db_name`);

--
-- Indexes for table `pma__recent`
--
ALTER TABLE `pma__recent`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `pma__relation`
--
ALTER TABLE `pma__relation`
  ADD PRIMARY KEY (`master_db`,`master_table`,`master_field`),
  ADD KEY `foreign_field` (`foreign_db`,`foreign_table`);

--
-- Indexes for table `pma__savedsearches`
--
ALTER TABLE `pma__savedsearches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `u_savedsearches_username_dbname` (`username`,`db_name`,`search_name`);

--
-- Indexes for table `pma__table_coords`
--
ALTER TABLE `pma__table_coords`
  ADD PRIMARY KEY (`db_name`,`table_name`,`pdf_page_number`);

--
-- Indexes for table `pma__table_info`
--
ALTER TABLE `pma__table_info`
  ADD PRIMARY KEY (`db_name`,`table_name`);

--
-- Indexes for table `pma__table_uiprefs`
--
ALTER TABLE `pma__table_uiprefs`
  ADD PRIMARY KEY (`username`,`db_name`,`table_name`);

--
-- Indexes for table `pma__tracking`
--
ALTER TABLE `pma__tracking`
  ADD PRIMARY KEY (`db_name`,`table_name`,`version`);

--
-- Indexes for table `pma__userconfig`
--
ALTER TABLE `pma__userconfig`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `pma__usergroups`
--
ALTER TABLE `pma__usergroups`
  ADD PRIMARY KEY (`usergroup`,`tab`,`allowed`);

--
-- Indexes for table `pma__users`
--
ALTER TABLE `pma__users`
  ADD PRIMARY KEY (`username`,`usergroup`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `pma__bookmark`
--
ALTER TABLE `pma__bookmark`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pma__column_info`
--
ALTER TABLE `pma__column_info`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pma__export_templates`
--
ALTER TABLE `pma__export_templates`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pma__history`
--
ALTER TABLE `pma__history`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pma__pdf_pages`
--
ALTER TABLE `pma__pdf_pages`
  MODIFY `page_nr` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pma__savedsearches`
--
ALTER TABLE `pma__savedsearches`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- Database: `pos_procurement`
--
CREATE DATABASE IF NOT EXISTS `pos_procurement` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `pos_procurement`;

-- --------------------------------------------------------

--
-- Table structure for table `department`
--

CREATE TABLE `department` (
  `dept_code` varchar(20) NOT NULL COMMENT 'รหัสแผนก',
  `dept_name` varchar(150) NOT NULL COMMENT 'ชื่อแผนก',
  `head_emp_code` varchar(20) DEFAULT NULL COMMENT 'รหัสพนักงานที่เป็นหัวหน้า (FK ไป employee)',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='แผนก (รหัสแผนก, ชื่อแผนก, รหัสพนักงานที่เป็นหัวหน้า)';

-- --------------------------------------------------------

--
-- Table structure for table `employee`
--

CREATE TABLE `employee` (
  `emp_code` varchar(20) NOT NULL COMMENT 'รหัสพนักงาน',
  `full_name` varchar(200) NOT NULL COMMENT 'ชื่อพนักงาน',
  `phone_no` varchar(50) DEFAULT NULL COMMENT 'เบอร์โทร',
  `email` varchar(200) DEFAULT NULL COMMENT 'Email',
  `password_hash` varchar(255) DEFAULT NULL COMMENT 'รหัสผ่าน (hash)',
  `emp_status` enum('active','inactive') NOT NULL DEFAULT 'active' COMMENT 'สถานะพนักงาน',
  `dept_code` varchar(20) DEFAULT NULL COMMENT 'รหัสแผนก (FK)',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='พนักงาน (รหัสพนักงาน, ชื่อ, โทร, Email, รหัสผ่าน, สถานะ, รหัสแผนก*)';

-- --------------------------------------------------------

--
-- Table structure for table `goods_receipt`
--

CREATE TABLE `goods_receipt` (
  `gr_no` varchar(30) NOT NULL COMMENT 'เลขที่รับสินค้า',
  `po_no` varchar(30) NOT NULL COMMENT 'อ้างใบสั่งซื้อ (FK)',
  `receipt_date` date NOT NULL COMMENT 'วันที่รับของ',
  `received_by` varchar(20) DEFAULT NULL COMMENT 'รหัสพนักงานผู้รับ (FK)',
  `receipt_status` enum('draft','received','partial','cancelled','closed') NOT NULL DEFAULT 'received' COMMENT 'สถานะการรับของ',
  `note` varchar(500) DEFAULT NULL COMMENT 'หมายเหตุ',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='รายการรับของ (เลขที่รับ, อ้าง PO*, วันที่รับ, ผู้รับ, สถานะ)';

-- --------------------------------------------------------

--
-- Table structure for table `goods_receipt_item`
--

CREATE TABLE `goods_receipt_item` (
  `gr_no` varchar(30) NOT NULL COMMENT 'เลขที่รับ (FK)',
  `product_code` varchar(30) NOT NULL COMMENT 'รหัสสินค้า (FK)',
  `qty_received` decimal(14,3) NOT NULL COMMENT 'จำนวนที่รับ',
  `unit_price` decimal(12,2) NOT NULL COMMENT 'ราคาต่อหน่วย (ตามบิล/PO)',
  `unit` varchar(30) NOT NULL DEFAULT 'unit' COMMENT 'หน่วยนับ'
) ;

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `product_code` varchar(30) NOT NULL COMMENT 'รหัสสินค้า',
  `product_name` varchar(200) NOT NULL COMMENT 'ชื่อสินค้า',
  `product_desc` text DEFAULT NULL COMMENT 'รายละเอียดสินค้า',
  `unit` varchar(30) NOT NULL DEFAULT 'unit' COMMENT 'หน่วยนับ',
  `unit_price` decimal(12,2) NOT NULL DEFAULT 0.00 COMMENT 'ราคาต่อหน่วย (มาตรฐาน)',
  `stock_qty` decimal(14,3) NOT NULL DEFAULT 0.000 COMMENT 'จำนวนคงเหลือ',
  `min_stock_qty` decimal(14,3) NOT NULL DEFAULT 0.000 COMMENT 'จำนวนคงคลังขั้นต่ำ',
  `category_code` varchar(20) DEFAULT NULL COMMENT 'รหัสประเภทสินค้า (FK)',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ;

-- --------------------------------------------------------

--
-- Table structure for table `product_category`
--

CREATE TABLE `product_category` (
  `category_code` varchar(20) NOT NULL COMMENT 'รหัสประเภทสินค้า',
  `category_name` varchar(150) NOT NULL COMMENT 'ชื่อประเภท'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='ประเภทสินค้า (รหัสประเภทสินค้า, ชื่อประเภท)';

-- --------------------------------------------------------

--
-- Table structure for table `purchase_order`
--

CREATE TABLE `purchase_order` (
  `po_no` varchar(30) NOT NULL COMMENT 'เลขที่สั่งซื้อ',
  `quote_no` varchar(30) NOT NULL COMMENT 'อ้างใบเสนอราคา (FK)',
  `order_date` date NOT NULL COMMENT 'วันที่สั่งซื้อ',
  `po_status` enum('draft','issued','approved','rejected','cancelled','closed') NOT NULL DEFAULT 'issued' COMMENT 'สถานะใบสั่งซื้อ',
  `total_amount` decimal(14,2) NOT NULL DEFAULT 0.00 COMMENT 'ยอดรวมสุทธิ',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='สั่งซื้อ (เลขที่สั่งซื้อ, อ้างใบเสนอราคา*, วันที่สั่งซื้อ*, สถานะ*, ยอดสุทธิ)';

-- --------------------------------------------------------

--
-- Table structure for table `purchase_order_item`
--

CREATE TABLE `purchase_order_item` (
  `po_no` varchar(30) NOT NULL COMMENT 'เลขที่สั่งซื้อ (FK)',
  `product_code` varchar(30) NOT NULL COMMENT 'รหัสสินค้า (FK)',
  `qty_ordered` decimal(14,3) NOT NULL COMMENT 'จำนวนที่สั่งซื้อ',
  `unit_price` decimal(12,2) NOT NULL COMMENT 'ราคาต่อหน่วย',
  `unit` varchar(30) NOT NULL DEFAULT 'unit' COMMENT 'หน่วยนับ'
) ;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_request`
--

CREATE TABLE `purchase_request` (
  `pr_no` varchar(30) NOT NULL COMMENT 'เลขที่ข้อมูล/ใบขอซื้อ',
  `request_date` date NOT NULL COMMENT 'วันที่ขอซื้อ',
  `requested_by` varchar(20) NOT NULL COMMENT 'รหัสพนักงานผู้ออกขอซื้อ (FK)',
  `approved_by` varchar(20) DEFAULT NULL COMMENT 'รหัสหัวหน้าผู้อนุมัติ (FK)',
  `pr_status` enum('draft','submitted','approved','rejected','cancelled','closed') NOT NULL DEFAULT 'draft' COMMENT 'สถานะใบขอซื้อ',
  `note` varchar(500) DEFAULT NULL COMMENT 'หมายเหตุ',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='ข้อมูล/ใบขอซื้อ (เลขที่, วันที่, ผู้ออกขอซื้อ*, หัวหน้าอนุมัติ*, สถานะ*)';

-- --------------------------------------------------------

--
-- Table structure for table `purchase_request_item`
--

CREATE TABLE `purchase_request_item` (
  `pr_no` varchar(30) NOT NULL COMMENT 'เลขที่ใบขอซื้อ (FK)',
  `product_code` varchar(30) NOT NULL COMMENT 'รหัสสินค้า (FK)',
  `qty_requested` decimal(14,3) NOT NULL COMMENT 'จำนวนที่ต้องการซื้อ',
  `exp_unit_price` decimal(12,2) DEFAULT NULL COMMENT 'ราคาต่อหน่วยโดยประมาณ/ตามประสบการณ์',
  `unit` varchar(30) NOT NULL DEFAULT 'unit' COMMENT 'หน่วยนับ'
) ;

-- --------------------------------------------------------

--
-- Table structure for table `quotation`
--

CREATE TABLE `quotation` (
  `quote_no` varchar(30) NOT NULL COMMENT 'เลขที่เสนอราคา',
  `pr_no` varchar(30) NOT NULL COMMENT 'อ้างใบขอซื้อ (FK)',
  `vendor_code` varchar(20) NOT NULL COMMENT 'รหัสผู้ขาย (FK)',
  `quote_date` date NOT NULL COMMENT 'วันที่เสนอราคา',
  `quote_status` enum('draft','submitted','accepted','rejected','expired','cancelled') NOT NULL DEFAULT 'submitted' COMMENT 'สถานะใบเสนอราคา',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='เสนอราคา (เลขที่เสนอราคา, อ้างใบขอซื้อ*, วันที่เสนอ, รหัสผู้ขาย*, สถานะ)';

-- --------------------------------------------------------

--
-- Table structure for table `quotation_item`
--

CREATE TABLE `quotation_item` (
  `quote_no` varchar(30) NOT NULL COMMENT 'เลขที่เสนอราคา (FK)',
  `product_code` varchar(30) NOT NULL COMMENT 'รหัสสินค้า (FK)',
  `qty_offered` decimal(14,3) NOT NULL COMMENT 'จำนวนที่เสนอ',
  `unit_price` decimal(12,2) NOT NULL COMMENT 'ราคาต่อหน่วยที่เสนอ',
  `unit` varchar(30) NOT NULL DEFAULT 'unit' COMMENT 'หน่วยนับ'
) ;

-- --------------------------------------------------------

--
-- Table structure for table `vendor`
--

CREATE TABLE `vendor` (
  `vendor_code` varchar(20) NOT NULL COMMENT 'รหัสผู้ขาย',
  `vendor_name` varchar(200) NOT NULL COMMENT 'ชื่อผู้ขาย',
  `contact_name` varchar(150) DEFAULT NULL COMMENT 'ชื่อผู้ติดต่อ',
  `address_text` text DEFAULT NULL COMMENT 'ที่อยู่',
  `phone_no` varchar(50) DEFAULT NULL COMMENT 'เบอร์โทร',
  `email` varchar(200) DEFAULT NULL COMMENT 'Email',
  `password_hash` varchar(255) DEFAULT NULL COMMENT 'รหัสผ่าน (hash)',
  `vendor_status` enum('active','inactive','suspended') NOT NULL DEFAULT 'active' COMMENT 'สถานะผู้ขาย',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='ผู้ขาย (รหัสผู้ขาย, ชื่อผู้ขาย, ผู้ติดต่อ, ที่อยู่, โทร, Email, รหัสผ่าน, สถานะผู้ขาย)';

-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_product_below_min`
-- (See below for the actual view)
--
CREATE TABLE `vw_product_below_min` (
`product_code` varchar(30)
,`product_name` varchar(200)
,`stock_qty` decimal(14,3)
,`min_stock_qty` decimal(14,3)
,`diff_to_min` decimal(15,3)
);

-- --------------------------------------------------------

--
-- Structure for view `vw_product_below_min`
--
DROP TABLE IF EXISTS `vw_product_below_min`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_product_below_min`  AS SELECT `p`.`product_code` AS `product_code`, `p`.`product_name` AS `product_name`, `p`.`stock_qty` AS `stock_qty`, `p`.`min_stock_qty` AS `min_stock_qty`, `p`.`stock_qty`- `p`.`min_stock_qty` AS `diff_to_min` FROM `product` AS `p` WHERE `p`.`stock_qty` < `p`.`min_stock_qty` ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`dept_code`),
  ADD UNIQUE KEY `uk_department_name` (`dept_name`),
  ADD KEY `idx_department_head_emp` (`head_emp_code`);

--
-- Indexes for table `employee`
--
ALTER TABLE `employee`
  ADD PRIMARY KEY (`emp_code`),
  ADD UNIQUE KEY `uk_employee_email` (`email`),
  ADD KEY `idx_employee_dept` (`dept_code`);

--
-- Indexes for table `goods_receipt`
--
ALTER TABLE `goods_receipt`
  ADD PRIMARY KEY (`gr_no`),
  ADD KEY `idx_gr_po` (`po_no`),
  ADD KEY `idx_gr_receiver` (`received_by`),
  ADD KEY `idx_gr_status_date` (`receipt_status`,`receipt_date`);

--
-- Indexes for table `goods_receipt_item`
--
ALTER TABLE `goods_receipt_item`
  ADD PRIMARY KEY (`gr_no`,`product_code`),
  ADD KEY `idx_gri_product` (`product_code`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`product_code`),
  ADD KEY `idx_product_category` (`category_code`);

--
-- Indexes for table `product_category`
--
ALTER TABLE `product_category`
  ADD PRIMARY KEY (`category_code`),
  ADD UNIQUE KEY `uk_category_name` (`category_name`);

--
-- Indexes for table `purchase_order`
--
ALTER TABLE `purchase_order`
  ADD PRIMARY KEY (`po_no`),
  ADD KEY `idx_po_quote` (`quote_no`),
  ADD KEY `idx_po_status_date` (`po_status`,`order_date`);

--
-- Indexes for table `purchase_order_item`
--
ALTER TABLE `purchase_order_item`
  ADD PRIMARY KEY (`po_no`,`product_code`),
  ADD KEY `idx_poi_product` (`product_code`);

--
-- Indexes for table `purchase_request`
--
ALTER TABLE `purchase_request`
  ADD PRIMARY KEY (`pr_no`),
  ADD KEY `idx_pr_requested_by` (`requested_by`),
  ADD KEY `idx_pr_approved_by` (`approved_by`),
  ADD KEY `idx_pr_status_date` (`pr_status`,`request_date`);

--
-- Indexes for table `purchase_request_item`
--
ALTER TABLE `purchase_request_item`
  ADD PRIMARY KEY (`pr_no`,`product_code`),
  ADD KEY `idx_pri_product` (`product_code`);

--
-- Indexes for table `quotation`
--
ALTER TABLE `quotation`
  ADD PRIMARY KEY (`quote_no`),
  ADD KEY `idx_quote_pr` (`pr_no`),
  ADD KEY `idx_quote_vendor` (`vendor_code`),
  ADD KEY `idx_quote_status_date` (`quote_status`,`quote_date`);

--
-- Indexes for table `quotation_item`
--
ALTER TABLE `quotation_item`
  ADD PRIMARY KEY (`quote_no`,`product_code`),
  ADD KEY `idx_qi_product` (`product_code`);

--
-- Indexes for table `vendor`
--
ALTER TABLE `vendor`
  ADD PRIMARY KEY (`vendor_code`),
  ADD UNIQUE KEY `uk_vendor_email` (`email`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `department`
--
ALTER TABLE `department`
  ADD CONSTRAINT `fk_department_head` FOREIGN KEY (`head_emp_code`) REFERENCES `employee` (`emp_code`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `employee`
--
ALTER TABLE `employee`
  ADD CONSTRAINT `fk_employee_dept` FOREIGN KEY (`dept_code`) REFERENCES `department` (`dept_code`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `goods_receipt`
--
ALTER TABLE `goods_receipt`
  ADD CONSTRAINT `fk_gr_po` FOREIGN KEY (`po_no`) REFERENCES `purchase_order` (`po_no`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_gr_received_by` FOREIGN KEY (`received_by`) REFERENCES `employee` (`emp_code`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `goods_receipt_item`
--
ALTER TABLE `goods_receipt_item`
  ADD CONSTRAINT `fk_gri_gr` FOREIGN KEY (`gr_no`) REFERENCES `goods_receipt` (`gr_no`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_gri_product` FOREIGN KEY (`product_code`) REFERENCES `product` (`product_code`) ON UPDATE CASCADE;

--
-- Constraints for table `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `fk_product_category` FOREIGN KEY (`category_code`) REFERENCES `product_category` (`category_code`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `purchase_order`
--
ALTER TABLE `purchase_order`
  ADD CONSTRAINT `fk_po_quote` FOREIGN KEY (`quote_no`) REFERENCES `quotation` (`quote_no`) ON UPDATE CASCADE;

--
-- Constraints for table `purchase_order_item`
--
ALTER TABLE `purchase_order_item`
  ADD CONSTRAINT `fk_poi_po` FOREIGN KEY (`po_no`) REFERENCES `purchase_order` (`po_no`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_poi_product` FOREIGN KEY (`product_code`) REFERENCES `product` (`product_code`) ON UPDATE CASCADE;

--
-- Constraints for table `purchase_request`
--
ALTER TABLE `purchase_request`
  ADD CONSTRAINT `fk_pr_approved_by` FOREIGN KEY (`approved_by`) REFERENCES `employee` (`emp_code`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pr_requested_by` FOREIGN KEY (`requested_by`) REFERENCES `employee` (`emp_code`) ON UPDATE CASCADE;

--
-- Constraints for table `purchase_request_item`
--
ALTER TABLE `purchase_request_item`
  ADD CONSTRAINT `fk_pri_pr` FOREIGN KEY (`pr_no`) REFERENCES `purchase_request` (`pr_no`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pri_product` FOREIGN KEY (`product_code`) REFERENCES `product` (`product_code`) ON UPDATE CASCADE;

--
-- Constraints for table `quotation`
--
ALTER TABLE `quotation`
  ADD CONSTRAINT `fk_quote_pr` FOREIGN KEY (`pr_no`) REFERENCES `purchase_request` (`pr_no`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_quote_vendor` FOREIGN KEY (`vendor_code`) REFERENCES `vendor` (`vendor_code`) ON UPDATE CASCADE;

--
-- Constraints for table `quotation_item`
--
ALTER TABLE `quotation_item`
  ADD CONSTRAINT `fk_qi_product` FOREIGN KEY (`product_code`) REFERENCES `product` (`product_code`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_qi_quote` FOREIGN KEY (`quote_no`) REFERENCES `quotation` (`quote_no`) ON DELETE CASCADE ON UPDATE CASCADE;
--
-- Database: `procurement_db`
--
CREATE DATABASE IF NOT EXISTS `procurement_db` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `procurement_db`;

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `head_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `name`, `head_id`) VALUES
(1, 'ผู้ดูแลระบบ', 6),
(2, 'พนักงาน', 3),
(3, 'พนักงานจัดซื้อ', 5);

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
(3, 'DeptHead', '0999999997', 'depthead@tsc.com', 2, 3, 'DeptHead', '$2y$10$LytJyJ0qL2JtgdGw3o1MFOIJiDHcema2feNwwlBSfkeFNr71o.ZJ.'),
(4, 'Purchasing', '0999999996', 'purchasing@tsc.com', 3, 4, 'Purchasing', '$2y$10$qODTryex3A6SkkMj5yMaOO/vwP6WLXffK5q5Sf.A/DCJd6IHGF9AC'),
(5, 'PurchasingHead', '0999999995', 'purchasinghead@tsc.com', 3, 5, 'PurchasingHead', '$2y$10$MbwXQf5nGqmb1LwUbwdlwe1HCXb0BbBi.GaciowWNrkpVWwQkCEDi'),
(6, 'Supahkit Weeraphan', '0935560964', 'tess@gmail.com', 1, 1, 'Employee', '$2y$10$wV5SEYOWltq9S7xZ5a6BTeovTMTAKv4iUVDMaChPLrK9iyBPu2IJa');

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
(1, 'ของฝาก', 3, 'ของฝาก', 1000, 100, 20.00);

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
  `approved_by_head_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `purchase_requests`
--

INSERT INTO `purchase_requests` (`id`, `employee_id`, `request_date`, `status`, `created_at`, `reason`, `approved_by_head_at`) VALUES
(1, 2, '2025-09-17 22:03:32', 'Pending', '2025-09-17 22:09:34', 'อยากได้อะ', NULL),
(4, 2, '2025-09-17 22:12:15', 'ApprovedByDeptHead', '2025-09-17 22:12:15', 'ของขาด', NULL);

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
(1, 1, 1, 10);

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
(1, 'Supahkit Weeraphan', 'TSC Dev co,ltd', 'Mahasarakham University', '0935560964', 'tesspitch@gmail.com', '$2y$10$EjYb3ZswB3bwFWtEAJv6T.KKL3GHtK1FtVdrfi15vHbqKoFWFPT4W', 'Active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `head_id` (`head_id`);

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
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `payment_types`
--
ALTER TABLE `payment_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `product_types`
--
ALTER TABLE `product_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_requests`
--
ALTER TABLE `purchase_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `purchase_request_items`
--
ALTER TABLE `purchase_request_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `purchase_tax_reports`
--
ALTER TABLE `purchase_tax_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quotations`
--
ALTER TABLE `quotations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quotation_items`
--
ALTER TABLE `quotation_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

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
  ADD CONSTRAINT `departments_ibfk_1` FOREIGN KEY (`head_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL;

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
--
-- Database: `proc_data`
--
CREATE DATABASE IF NOT EXISTS `proc_data` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `proc_data`;
--
-- Database: `supplychain`
--
CREATE DATABASE IF NOT EXISTS `supplychain` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `supplychain`;

-- --------------------------------------------------------

--
-- Table structure for table `orderitem`
--

CREATE TABLE `orderitem` (
  `item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orderitem`
--

INSERT INTO `orderitem` (`item_id`, `order_id`, `product_id`, `quantity`, `unit_price`) VALUES
(1, 1, 2, 3, 40000.00),
(2, 1, 1, 5, 20000.00),
(3, 2, 1, 3, 20000.00),
(4, 3, 2, 1, 40000.00),
(5, 3, 1, 1, 20000.00);

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `product_id` int(11) NOT NULL,
  `product_name` varchar(100) DEFAULT NULL,
  `product_description` text DEFAULT NULL,
  `product_price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`product_id`, `product_name`, `product_description`, `product_price`) VALUES
(1, 'RTX3070', 'Ghapies card', 20000.00),
(2, 'RTX4090', 'Ghapies card', 40000.00),
(3, 'RTX3060', 'Ghapies card', 15000.00);

-- --------------------------------------------------------

--
-- Table structure for table `producttype`
--

CREATE TABLE `producttype` (
  `type_id` int(11) NOT NULL,
  `type_name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `producttype`
--

INSERT INTO `producttype` (`type_id`, `type_name`) VALUES
(1, 'Computer accessories'),
(2, 'Computer equipment');

-- --------------------------------------------------------

--
-- Table structure for table `purchaseorder`
--

CREATE TABLE `purchaseorder` (
  `order_id` int(11) NOT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `order_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchaseorder`
--

INSERT INTO `purchaseorder` (`order_id`, `supplier_id`, `order_date`) VALUES
(1, 3, '2568-03-31'),
(2, 1, '2568-03-31'),
(3, 2, '2568-03-31');

-- --------------------------------------------------------

--
-- Table structure for table `returnorder`
--

CREATE TABLE `returnorder` (
  `return_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `return_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `returnorder`
--

INSERT INTO `returnorder` (`return_id`, `order_id`, `return_date`) VALUES
(1, 1, '2568-03-31');

-- --------------------------------------------------------

--
-- Table structure for table `supplier`
--

CREATE TABLE `supplier` (
  `supplier_id` int(11) NOT NULL,
  `supplier_name` varchar(100) DEFAULT NULL,
  `supplier_contact` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `supplier`
--

INSERT INTO `supplier` (`supplier_id`, `supplier_name`, `supplier_contact`) VALUES
(1, 'Kim', '0840077307'),
(2, 'Boom', '0973124245'),
(3, 'Ko', '0875435643');

-- --------------------------------------------------------

--
-- Table structure for table `supplier_product`
--

CREATE TABLE `supplier_product` (
  `supplier_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `supplier_product`
--

INSERT INTO `supplier_product` (`supplier_id`, `product_id`) VALUES
(1, 2),
(2, 1),
(3, 2);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `orderitem`
--
ALTER TABLE `orderitem`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `producttype`
--
ALTER TABLE `producttype`
  ADD PRIMARY KEY (`type_id`);

--
-- Indexes for table `purchaseorder`
--
ALTER TABLE `purchaseorder`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- Indexes for table `returnorder`
--
ALTER TABLE `returnorder`
  ADD PRIMARY KEY (`return_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `supplier`
--
ALTER TABLE `supplier`
  ADD PRIMARY KEY (`supplier_id`);

--
-- Indexes for table `supplier_product`
--
ALTER TABLE `supplier_product`
  ADD PRIMARY KEY (`supplier_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `orderitem`
--
ALTER TABLE `orderitem`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `producttype`
--
ALTER TABLE `producttype`
  MODIFY `type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `purchaseorder`
--
ALTER TABLE `purchaseorder`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `returnorder`
--
ALTER TABLE `returnorder`
  MODIFY `return_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `supplier`
--
ALTER TABLE `supplier`
  MODIFY `supplier_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orderitem`
--
ALTER TABLE `orderitem`
  ADD CONSTRAINT `orderitem_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `purchaseorder` (`order_id`),
  ADD CONSTRAINT `orderitem_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`);

--
-- Constraints for table `purchaseorder`
--
ALTER TABLE `purchaseorder`
  ADD CONSTRAINT `purchaseorder_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `supplier` (`supplier_id`);

--
-- Constraints for table `returnorder`
--
ALTER TABLE `returnorder`
  ADD CONSTRAINT `returnorder_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `purchaseorder` (`order_id`);

--
-- Constraints for table `supplier_product`
--
ALTER TABLE `supplier_product`
  ADD CONSTRAINT `supplier_product_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `supplier` (`supplier_id`),
  ADD CONSTRAINT `supplier_product_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`);
--
-- Database: `test`
--
CREATE DATABASE IF NOT EXISTS `test` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `test`;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
