-- Apartman Yönetim Yazılımı - Database Schema
-- Open Source Version

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------

--
-- Table structure for `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for `apartments`
--

CREATE TABLE `apartments` (
  `id` int(11) NOT NULL,
  `block_id` int(11) NOT NULL,
  `door_number` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for `apartment_payment_ratio`
--

CREATE TABLE `apartment_payment_ratio` (
  `apartment_id` int(11) NOT NULL,
  `tenant_ratio` int(11) DEFAULT 100,
  `owner_ratio` int(11) DEFAULT 0,
  `extra_tenant_ratio` int(11) DEFAULT 0,
  `extra_owner_ratio` int(11) DEFAULT 100
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for `blocks`
--

CREATE TABLE `blocks` (
  `id` int(11) NOT NULL,
  `block_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for `dues`
--

CREATE TABLE `dues` (
  `id` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `month` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for `dues_assignments`
--

CREATE TABLE `dues_assignments` (
  `id` int(11) NOT NULL,
  `due_id` int(11) NOT NULL,
  `apartment_id` int(11) NOT NULL,
  `tenant_amount` decimal(10,2) NOT NULL,
  `owner_amount` decimal(10,2) NOT NULL,
  `paid_tenant` decimal(10,2) DEFAULT 0.00,
  `paid_owner` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for `expenses`
--

CREATE TABLE `expenses` (
  `id` int(11) NOT NULL,
  `category` enum('Peyzaj','Elektrik','Su','Temizlik','Asansör','Tamirat','Diğer') NOT NULL,
  `title` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `date` date NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for `extra_charges`
--

CREATE TABLE `extra_charges` (
  `id` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `month` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for `extra_charge_assignments`
--

CREATE TABLE `extra_charge_assignments` (
  `id` int(11) NOT NULL,
  `extra_charge_id` int(11) NOT NULL,
  `apartment_id` int(11) NOT NULL,
  `tenant_amount` decimal(10,2) NOT NULL,
  `owner_amount` decimal(10,2) NOT NULL,
  `paid_tenant` decimal(10,2) DEFAULT 0.00,
  `paid_owner` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `resident_id` int(11) NOT NULL,
  `assignment_id` int(11) DEFAULT NULL,
  `type` enum('due','extra') NOT NULL,
  `target_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_date` date NOT NULL,
  `payment_type` enum('tenant','owner') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for `residents`
--

CREATE TABLE `residents` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `block_id` int(11) NOT NULL,
  `apartment_id` int(11) NOT NULL,
  `resident_type` enum('tenant','owner') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for `site_settings`
--

CREATE TABLE `site_settings` (
  `id` int(11) NOT NULL,
  `site_name` varchar(255) NOT NULL DEFAULT 'Apartman Yönetim Yazılımı',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `site_settings` (`id`, `site_name`) VALUES
(1, 'Apartman Yönetim Yazılımı');

-- --------------------------------------------------------

--
-- Table structure for `whatsapp_settings`
--

CREATE TABLE `whatsapp_settings` (
  `id` int(11) NOT NULL,
  `meta_access_token` text DEFAULT NULL,
  `meta_phone_number_id` varchar(100) DEFAULT NULL,
  `meta_waba_id` varchar(100) DEFAULT NULL,
  `webhook_verify_token` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 0,
  `send_day` tinyint(4) DEFAULT 1,
  `send_time` time DEFAULT '10:00:00',
  `message_template` text DEFAULT NULL,
  `template_name` varchar(100) DEFAULT NULL,
  `language_code` varchar(10) DEFAULT 'tr',
  `last_run_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `whatsapp_settings` (`id`, `meta_access_token`, `is_active`) VALUES
(1, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for `whatsapp_logs`
--

CREATE TABLE `whatsapp_logs` (
  `id` int(11) NOT NULL,
  `resident_id` int(11) NOT NULL,
  `message_type` varchar(50) DEFAULT 'reminder',
  `phone` varchar(20) DEFAULT NULL,
  `normalized_phone` varchar(20) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `period` varchar(20) DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `http_code` int(11) DEFAULT NULL,
  `error_message` text DEFAULT NULL,
  `raw_response` text DEFAULT NULL,
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

ALTER TABLE `apartments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `block_id` (`block_id`);

ALTER TABLE `apartment_payment_ratio`
  ADD PRIMARY KEY (`apartment_id`);

ALTER TABLE `blocks`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `dues`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `dues_assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `due_id` (`due_id`),
  ADD KEY `apartment_id` (`apartment_id`);

ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `extra_charges`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `extra_charge_assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `extra_charge_id` (`extra_charge_id`),
  ADD KEY `apartment_id` (`apartment_id`);

ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `resident_id` (`resident_id`);

ALTER TABLE `residents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `block_id` (`block_id`),
  ADD KEY `apartment_id` (`apartment_id`);

ALTER TABLE `site_settings`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `whatsapp_settings`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `whatsapp_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `resident_id` (`resident_id`);

--
-- AUTO_INCREMENT for dumped tables
--

ALTER TABLE `admin_users` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `apartments` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `blocks` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `dues` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `dues_assignments` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `expenses` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `extra_charges` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `extra_charge_assignments` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `payments` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `residents` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `site_settings` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `whatsapp_settings` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `whatsapp_logs` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

ALTER TABLE `apartments` ADD CONSTRAINT `apartments_ibfk_1` FOREIGN KEY (`block_id`) REFERENCES `blocks` (`id`) ON DELETE CASCADE;
ALTER TABLE `apartment_payment_ratio` ADD CONSTRAINT `apartment_payment_ratio_ibfk_1` FOREIGN KEY (`apartment_id`) REFERENCES `apartments` (`id`) ON DELETE CASCADE;
ALTER TABLE `dues_assignments` ADD CONSTRAINT `dues_assignments_ibfk_1` FOREIGN KEY (`due_id`) REFERENCES `dues` (`id`) ON DELETE CASCADE, ADD CONSTRAINT `dues_assignments_ibfk_2` FOREIGN KEY (`apartment_id`) REFERENCES `apartments` (`id`) ON DELETE CASCADE;
ALTER TABLE `extra_charge_assignments` ADD CONSTRAINT `extra_charge_assignments_ibfk_1` FOREIGN KEY (`extra_charge_id`) REFERENCES `extra_charges` (`id`) ON DELETE CASCADE, ADD CONSTRAINT `extra_charge_assignments_ibfk_2` FOREIGN KEY (`apartment_id`) REFERENCES `apartments` (`id`) ON DELETE CASCADE;
ALTER TABLE `payments` ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`resident_id`) REFERENCES `residents` (`id`) ON DELETE CASCADE;
ALTER TABLE `residents` ADD CONSTRAINT `residents_ibfk_1` FOREIGN KEY (`block_id`) REFERENCES `blocks` (`id`) ON DELETE CASCADE, ADD CONSTRAINT `residents_ibfk_2` FOREIGN KEY (`apartment_id`) REFERENCES `apartments` (`id`) ON DELETE CASCADE;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
