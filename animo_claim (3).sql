-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Aug 01, 2026 at 07:22 AM
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
-- Database: `animo_claim`
--

-- --------------------------------------------------------

--
-- Table structure for table `crowd_traffic_logs`
--

CREATE TABLE `crowd_traffic_logs` (
  `id` int(11) NOT NULL,
  `campus` enum('manila','laguna') NOT NULL,
  `location_name` varchar(150) NOT NULL,
  `density_percentage` tinyint(3) UNSIGNED NOT NULL,
  `current_headcount` int(11) NOT NULL,
  `recorded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `crowd_traffic_logs`
--

INSERT INTO `crowd_traffic_logs` (`id`, `campus`, `location_name`, `density_percentage`, `current_headcount`, `recorded_at`) VALUES
(1, 'manila', 'Henry Sy Sr. Hall', 75, 750, '2026-07-08 17:00:59'),
(2, 'manila', 'Enrique Razon Sports Center', 40, 400, '2026-07-08 17:00:59'),
(3, 'manila', 'Gokongwei Hall', 60, 600, '2026-07-08 17:00:59'),
(4, 'manila', 'St. La Salle Hall', 35, 350, '2026-07-08 17:00:59'),
(5, 'manila', 'Velasco Hall', 20, 200, '2026-07-08 17:00:59'),
(6, 'laguna', 'Milagros R. Del Rosario Bldg', 45, 450, '2026-07-08 17:00:59'),
(7, 'laguna', 'John L. Gokongwei Jr. Innovation Center', 85, 850, '2026-07-08 17:00:59'),
(8, 'laguna', 'Richard L. Lee Engineering Block', 70, 700, '2026-07-08 17:00:59'),
(9, 'laguna', 'Dr. George S.K. Ty Bldg', 30, 300, '2026-07-08 17:00:59'),
(10, 'laguna', 'St. Matthew Gymnasium', 15, 150, '2026-07-08 17:00:59'),
(11, 'laguna', 'Enrique K. Razon Jr. Hall', 20, 200, '2026-07-08 17:00:59'),
(12, 'manila', 'Henry Sy Sr. Hall', 82, 820, '2026-07-10 01:30:00'),
(13, 'manila', 'Gokongwei Hall', 41, 410, '2026-07-10 01:30:00'),
(14, 'laguna', 'Milagros R. Del Rosario Bldg', 50, 500, '2026-07-10 01:30:00'),
(15, 'manila', 'Henry Sy Sr. Hall', 89, 890, '2026-07-11 04:15:00'),
(16, 'manila', 'Gokongwei Hall', 78, 780, '2026-07-11 04:15:00'),
(17, 'laguna', 'John L. Gokongwei Jr. Innovation Center', 92, 920, '2026-07-11 04:15:00'),
(18, 'manila', 'Henry Sy Sr. Hall', 31, 310, '2026-07-12 08:45:00'),
(19, 'laguna', 'St. Matthew Gymnasium', 40, 400, '2026-07-12 08:45:00'),
(20, 'laguna', 'John L. Gokongwei Jr. Innovation Center', 0, 0, '2026-07-30 23:53:01'),
(21, 'laguna', 'John L. Gokongwei Jr. Innovation Center', 0, 0, '2026-07-30 23:54:02'),
(22, 'laguna', 'John L. Gokongwei Jr. Innovation Center', 0, 0, '2026-07-30 23:55:03'),
(23, 'laguna', 'John L. Gokongwei Jr. Innovation Center', 0, 0, '2026-07-30 23:56:04'),
(24, 'laguna', 'John L. Gokongwei Jr. Innovation Center', 0, 0, '2026-07-30 23:57:05'),
(25, 'laguna', 'John L. Gokongwei Jr. Innovation Center', 0, 0, '2026-07-30 23:58:06'),
(26, 'laguna', 'John L. Gokongwei Jr. Innovation Center', 0, 0, '2026-07-30 23:59:18'),
(27, 'laguna', 'John L. Gokongwei Jr. Innovation Center', 0, 0, '2026-07-31 00:00:19'),
(28, 'laguna', 'John L. Gokongwei Jr. Innovation Center', 3, 1, '2026-07-31 00:01:19'),
(29, 'laguna', 'John L. Gokongwei Jr. Innovation Center', 3, 1, '2026-07-31 00:02:20'),
(30, 'laguna', 'John L. Gokongwei Jr. Innovation Center', 0, 0, '2026-07-31 00:03:20'),
(31, 'laguna', 'John L. Gokongwei Jr. Innovation Center', 0, 0, '2026-07-31 00:04:21'),
(32, 'laguna', 'John L. Gokongwei Jr. Innovation Center', 0, 0, '2026-07-31 00:11:09'),
(33, 'laguna', 'John L. Gokongwei Jr. Innovation Center', 0, 0, '2026-07-31 00:12:09'),
(34, 'laguna', 'John L. Gokongwei Jr. Innovation Center', 0, 0, '2026-07-31 00:13:10'),
(35, 'laguna', 'John L. Gokongwei Jr. Innovation Center', 0, 0, '2026-07-31 00:14:11'),
(36, 'laguna', 'John L. Gokongwei Jr. Innovation Center', 0, 0, '2026-07-31 00:15:12'),
(37, 'laguna', 'John L. Gokongwei Jr. Innovation Center', 0, 0, '2026-07-31 00:16:13'),
(38, 'laguna', 'John L. Gokongwei Jr. Innovation Center', 0, 0, '2026-07-31 00:17:14'),
(39, 'laguna', 'John L. Gokongwei Jr. Innovation Center', 0, 0, '2026-07-31 00:18:15'),
(40, 'laguna', 'John L. Gokongwei Jr. Innovation Center', 0, 0, '2026-07-31 00:19:16'),
(41, 'laguna', 'John L. Gokongwei Jr. Innovation Center', 0, 0, '2026-07-31 00:20:16'),
(42, 'laguna', 'John L. Gokongwei Jr. Innovation Center', 0, 0, '2026-07-31 00:27:57'),
(43, 'laguna', 'John L. Gokongwei Jr. Innovation Center', 0, 0, '2026-07-31 00:28:58'),
(44, 'laguna', 'John L. Gokongwei Jr. Innovation Center', 0, 0, '2026-07-31 00:29:58'),
(45, 'laguna', 'John L. Gokongwei Jr. Innovation Center', 13, 4, '2026-07-31 00:30:58'),
(46, 'laguna', 'John L. Gokongwei Jr. Innovation Center', 0, 0, '2026-07-31 00:31:59'),
(47, 'laguna', 'John L. Gokongwei Jr. Innovation Center', 3, 1, '2026-07-31 00:32:59'),
(48, 'laguna', 'John L. Gokongwei Jr. Innovation Center', 0, 0, '2026-07-31 00:33:59'),
(49, 'laguna', 'John L. Gokongwei Jr. Innovation Center', 0, 0, '2026-07-31 00:35:00'),
(50, 'laguna', 'John L. Gokongwei Jr. Innovation Center', 0, 0, '2026-07-31 00:36:00'),
(51, 'laguna', 'John L. Gokongwei Jr. Innovation Center', 0, 0, '2026-07-31 00:37:01'),
(52, 'laguna', 'John L. Gokongwei Jr. Innovation Center', 0, 0, '2026-07-31 00:38:01'),
(53, 'laguna', 'John L. Gokongwei Jr. Innovation Center', 0, 0, '2026-07-31 00:39:02'),
(54, 'laguna', 'John L. Gokongwei Jr. Innovation Center', 0, 0, '2026-07-31 00:40:02'),
(55, 'laguna', 'John L. Gokongwei Jr. Innovation Center', 0, 0, '2026-07-31 00:41:02'),
(56, 'laguna', 'John L. Gokongwei Jr. Innovation Center', 0, 0, '2026-07-31 00:42:03'),
(57, 'laguna', 'John L. Gokongwei Jr. Innovation Center', 0, 0, '2026-07-31 00:43:03'),
(58, 'laguna', 'John L. Gokongwei Jr. Innovation Center', 0, 0, '2026-07-31 00:44:04'),
(59, 'laguna', 'John L. Gokongwei Jr. Innovation Center', 0, 0, '2026-07-31 00:45:04'),
(60, 'laguna', 'John L. Gokongwei Jr. Innovation Center', 0, 0, '2026-07-31 00:46:04'),
(61, 'laguna', 'John L. Gokongwei Jr. Innovation Center', 0, 0, '2026-07-31 00:47:05'),
(62, 'laguna', 'John L. Gokongwei Jr. Innovation Center', 0, 0, '2026-07-31 00:48:05'),
(63, 'laguna', 'John L. Gokongwei Jr. Innovation Center', 0, 0, '2026-07-31 00:49:05'),
(64, 'laguna', 'John L. Gokongwei Jr. Innovation Center', 0, 0, '2026-07-31 00:50:05'),
(65, 'laguna', 'John L. Gokongwei Jr. Innovation Center', 0, 0, '2026-07-31 00:51:05'),
(66, 'laguna', 'John L. Gokongwei Jr. Innovation Center', 0, 0, '2026-07-31 00:52:06'),
(67, 'laguna', 'John L. Gokongwei Jr. Innovation Center', 0, 0, '2026-07-31 00:53:06'),
(68, 'laguna', 'John L. Gokongwei Jr. Innovation Center', 0, 0, '2026-07-31 00:54:07'),
(69, 'laguna', 'John L. Gokongwei Jr. Innovation Center', 27, 8, '2026-07-31 00:55:07'),
(70, 'laguna', 'John L. Gokongwei Jr. Innovation Center', 13, 4, '2026-07-31 00:56:08'),
(71, 'laguna', 'John L. Gokongwei Jr. Innovation Center', 27, 8, '2026-07-31 00:57:08'),
(72, 'laguna', 'John L. Gokongwei Jr. Innovation Center', 0, 0, '2026-07-31 00:58:08'),
(73, 'laguna', 'John L. Gokongwei Jr. Innovation Center', 0, 0, '2026-07-31 00:59:09'),
(74, 'laguna', 'John L. Gokongwei Jr. Innovation Center', 3, 1, '2026-07-31 01:00:09'),
(75, 'laguna', 'John L. Gokongwei Jr. Innovation Center', 0, 0, '2026-07-31 01:01:10');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `organizer_id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `location` varchar(150) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `category` enum('Giveaway','Event') NOT NULL DEFAULT 'Giveaway',
  `target_sex` enum('All','M','F') NOT NULL DEFAULT 'All',
  `target_college` varchar(150) NOT NULL DEFAULT 'All',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `organizer_id`, `title`, `description`, `location`, `image_url`, `category`, `target_sex`, `target_college`, `is_active`, `created_at`) VALUES
(1, 1, 'Women\'s Care Kit Giveaway', 'Exclusive wellness and care essentials for women.', 'Henry Sy Sr. Hall', 'care_kit.jpg', 'Giveaway', 'F', 'All', 1, '2026-07-09 05:22:13'),
(2, 1, 'Archer\'s Kitchen', 'Free packed lunch distribution for all Lasallians.', 'SJ Walk', 'archers_kitchen.jpg', 'Giveaway', 'All', 'All', 1, '2026-07-09 05:22:13'),
(3, 1, 'Tomo Coffee Free Upsize', 'Get a free upsize on your Tomo Coffee purchase. Exclusive for CCS students!', 'Gokongwei Hall', 'tomo_coffee.jpg', 'Giveaway', 'All', 'College of Computer Studies', 1, '2026-07-09 05:22:13'),
(4, 1, 'Free School Supplies', 'Notebooks, pens, and essentials for our beloved scholars.', 'EKR TLC', 'school_supplies.jpg', 'Giveaway', 'All', 'All', 1, '2026-07-09 05:22:13'),
(5, 1, 'Animo Lanyard Giveaway', 'Show your school pride with a free 2026 Animo Lanyard.', 'Razon Hall', 'lanyard.jpg', 'Giveaway', 'All', 'All', 1, '2026-07-09 05:22:13'),
(6, 1, 'UAAP Volleyball Match', 'Support the Lady Spikers live!', 'St. Matthew Gymnasium', 'volleyball.jpg', 'Event', 'All', 'All', 1, '2026-07-09 05:22:13'),
(7, 1, 'UAAP Basketball', 'Cheer for the Green Archers in the season opener.', 'Enrique Razon Sports Center', 'basketball.jpg', 'Event', 'All', 'All', 1, '2026-07-09 05:22:13'),
(8, 1, 'UAAP Cheerleading', 'Watch the Animo Squad defend their title.', 'Enrique Razon Sports Center', 'cheerleading.jpg', 'Event', 'All', 'All', 1, '2026-07-09 05:22:13'),
(9, 1, 'CCS Tech Symposium', 'Annual gathering for tech innovations and networking.', 'John L. Gokongwei Jr. Innovation Center', 'ccs_tech.jpg', 'Event', 'All', 'College of Computer Studies', 1, '2026-07-09 05:22:13'),
(10, 1, 'Lasallian Scholar Assembly', 'Mandatory orientation and gathering for all grantees.', 'Milagros R. Del Rosario Bldg', 'scholar_assembly.jpg', 'Event', 'All', 'All', 1, '2026-07-09 05:22:13'),
(11, 1, 'test giveaway', 'just for testing', 'ekr', 'school_supplies.jpg', 'Giveaway', 'All', 'All', 1, '2026-07-31 05:44:51');

-- --------------------------------------------------------

--
-- Table structure for table `event_time_slots`
--

CREATE TABLE `event_time_slots` (
  `id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `max_capacity` int(11) NOT NULL,
  `current_reservations` int(11) NOT NULL DEFAULT 0
) ;

--
-- Dumping data for table `event_time_slots`
--

INSERT INTO `event_time_slots` (`id`, `event_id`, `start_time`, `end_time`, `max_capacity`, `current_reservations`) VALUES
(101, 1, '2026-07-14 09:00:00', '2026-07-14 10:00:00', 50, 2),
(102, 1, '2026-07-14 10:00:00', '2026-07-14 11:00:00', 50, 0),
(103, 1, '2026-07-14 11:00:00', '2026-07-14 12:00:00', 50, 0),
(201, 2, '2026-07-15 11:30:00', '2026-07-15 12:30:00', 200, 2),
(202, 2, '2026-07-15 12:30:00', '2026-07-15 13:30:00', 200, 2),
(301, 3, '2026-07-16 08:00:00', '2026-07-16 10:00:00', 100, 1),
(302, 3, '2026-07-16 10:00:00', '2026-07-16 12:00:00', 100, 0),
(501, 5, '2026-07-17 13:00:00', '2026-07-17 15:00:00', 300, 0),
(502, 11, '2026-08-02 09:00:00', '2026-08-02 10:30:00', 50, 0),
(503, 11, '2026-08-02 11:00:00', '2026-08-02 12:30:00', 50, 0),
(504, 11, '2026-08-02 13:30:00', '2026-08-02 15:00:00', 50, 0);

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `item_name` varchar(150) NOT NULL,
  `total_quantity` int(11) NOT NULL,
  `remaining_quantity` int(11) NOT NULL
) ;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`id`, `event_id`, `item_name`, `total_quantity`, `remaining_quantity`) VALUES
(1, 1, 'Wellness Care Package', 150, 149),
(2, 2, 'Chicken Adobo Packed Lunch', 400, 398),
(3, 3, 'Tomo Coffee Upsize Voucher', 200, 199),
(4, 4, 'TLC Supply Bundle', 500, 500),
(5, 5, 'DLSU 2026 Green Lanyard', 300, 300),
(6, 6, 'Volleyball Match General Admission', 1000, 1000),
(7, 7, 'Basketball Match VIP Tickets', 200, 200),
(8, 11, 'test giveaway Package', 150, 150);

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `time_slot_id` int(11) NOT NULL,
  `inventory_id` int(11) NOT NULL,
  `qr_code_hash` varchar(50) NOT NULL,
  `status` enum('reserved','claimed','expired') NOT NULL DEFAULT 'reserved',
  `claimed_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`id`, `user_id`, `time_slot_id`, `inventory_id`, `qr_code_hash`, `status`, `claimed_at`, `created_at`) VALUES
(1001, 2, 201, 2, 'AC-1001-A1B2C3D4', 'claimed', '2026-07-15 11:45:12', '2026-07-11 00:30:00'),
(1002, 2, 301, 3, 'AC-1002-E5F6G7H8', 'claimed', '2026-07-15 08:16:23', '2026-07-11 01:15:00'),
(1003, 3, 101, 1, 'AC-1003-I9J0K1L2', 'claimed', '2026-07-14 09:12:34', '2026-07-11 02:00:00'),
(1004, 3, 202, 2, 'AC-1004-M3N4O5P6', 'reserved', NULL, '2026-07-11 02:05:00'),
(1005, 5, 201, 2, 'AC-1005-Q7R8S9T0', 'expired', NULL, '2026-07-11 03:20:00'),
(1006, 6, 201, 2, 'AC-1006-U1V2W3X4', 'reserved', NULL, '2026-07-11 04:00:00'),
(1007, 7, 102, 1, 'AC-1007-Y5Z6A7B8', 'expired', NULL, '2026-07-11 05:45:00'),
(1008, 7, 201, 2, 'AC-1008-C9D0E1F2', 'expired', NULL, '2026-07-11 06:10:00'),
(1009, 7, 301, 3, 'AC-1009-G3H4I5J6', 'expired', NULL, '2026-07-11 07:30:00'),
(1010, 2, 101, 1, 'AC-276-7AAAE8', 'reserved', NULL, '2026-07-13 02:28:55'),
(1011, 2, 202, 2, 'AC-886-31BEFE', 'claimed', '2026-07-15 09:17:24', '2026-07-15 01:16:19');

--
-- Triggers `reservations`
--
DELIMITER $$
CREATE TRIGGER `trg_reservations_after_insert` AFTER INSERT ON `reservations` FOR EACH ROW BEGIN
    UPDATE event_time_slots
        SET current_reservations = current_reservations + 1
        WHERE id = NEW.time_slot_id;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_reservations_before_insert` BEFORE INSERT ON `reservations` FOR EACH ROW BEGIN
    DECLARE v_status VARCHAR(20);
    DECLARE v_event_id INT;
    DECLARE v_dupe_count INT;
    DECLARE v_capacity INT;
    DECLARE v_current INT;

    SELECT status INTO v_status FROM users WHERE id = NEW.user_id;
    IF v_status = 'suspended' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Suspended users cannot make reservations.';
    END IF;

    SELECT event_id, max_capacity, current_reservations
        INTO v_event_id, v_capacity, v_current
        FROM event_time_slots WHERE id = NEW.time_slot_id;

    IF v_current >= v_capacity THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Time slot is at full capacity.';
    END IF;

    SELECT COUNT(*) INTO v_dupe_count
        FROM reservations r
        JOIN event_time_slots ts ON r.time_slot_id = ts.id
        WHERE r.user_id = NEW.user_id
          AND ts.event_id = v_event_id
          AND r.status IN ('reserved', 'claimed');

    IF v_dupe_count > 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'User already has an active reservation for this event.';
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_reservations_before_update` BEFORE UPDATE ON `reservations` FOR EACH ROW BEGIN
    DECLARE v_remaining INT;

    IF NEW.status = 'claimed' AND OLD.status = 'reserved' THEN
        SELECT remaining_quantity INTO v_remaining FROM inventory WHERE id = NEW.inventory_id;
        IF v_remaining <= 0 THEN
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No remaining inventory for this item.';
        END IF;
        UPDATE inventory SET remaining_quantity = remaining_quantity - 1 WHERE id = NEW.inventory_id;
    END IF;

    IF NEW.status = 'expired' AND OLD.status = 'reserved' THEN
        UPDATE event_time_slots
            SET current_reservations = current_reservations - 1
            WHERE id = NEW.time_slot_id;
        INSERT INTO strike_logs (user_id, reservation_id, status, strike_date)
            VALUES (NEW.user_id, NEW.id, 'Unexcused No-Show', NOW());
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `strike_logs`
--

CREATE TABLE `strike_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `reservation_id` int(11) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Unexcused No-Show',
  `strike_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `strike_logs`
--

INSERT INTO `strike_logs` (`id`, `user_id`, `reservation_id`, `status`, `strike_date`) VALUES
(1, 5, 1005, 'Unexcused No-Show', '2026-07-28 19:54:35'),
(2, 7, 1007, 'Unexcused No-Show', '2026-07-28 19:54:35'),
(3, 7, 1008, 'Unexcused No-Show', '2026-07-28 19:54:35'),
(4, 7, 1009, 'Unexcused No-Show', '2026-07-28 19:54:35');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `dlsu_id` varchar(20) NOT NULL,
  `rfid_uid` varchar(20) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `program` varchar(100) DEFAULT NULL,
  `college` varchar(150) DEFAULT NULL,
  `sex` enum('M','F') NOT NULL,
  `scholarship_type` varchar(150) DEFAULT 'None',
  `status` enum('active','suspended') NOT NULL DEFAULT 'active',
  `role` enum('student','organizer','admin') NOT NULL DEFAULT 'student',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `dlsu_id`, `rfid_uid`, `first_name`, `last_name`, `email`, `password`, `program`, `college`, `sex`, `scholarship_type`, `status`, `role`, `created_at`) VALUES
(1, '1201111', 'A1B2C3D4', 'Kyrstie', 'San Jose', 'kyrstie_sanjose@dlsu.edu.ph', '$2y$10$w9.CLLg57uv/EdtQXwjFcOG/SC1tWaXsf69.3B4/NtCVy2Vopck0y', 'BS Information Technology', 'College of Computer Studies', 'F', 'Saint La Salle Financial Assistance Grant', 'active', 'organizer', '2026-07-07 15:41:05'),
(2, '1202222', 'E5F6G7H8', 'Dhens', 'Team Lead', 'dhens_ba@dlsu.edu.ph', '$2y$10$w9.CLLg57uv/EdtQXwjFcOG/SC1tWaXsf69.3B4/NtCVy2Vopck0y', 'BS Information Technology', 'College of Computer Studies', 'M', 'None', 'active', 'student', '2026-07-07 15:41:05'),
(3, '1203333', '19J0K1L2', 'Leila', 'QA', 'leila_qa@dlsu.edu.ph', '$2y$10$w9.CLLg57uv/EdtQXwjFcOG/SC1tWaXsf69.3B4/NtCVy2Vopck0y', 'BS Information Technology', 'College of Computer Studies', 'F', 'None', 'active', 'student', '2026-07-07 15:41:05'),
(4, '1104444', 'M3N405P6', 'Rafael', 'Gonda', 'rafael.gonda@dlsu.edu.ph', '$2y$10$w9.CLLg57uv/EdtQXwjFcOG/SC1tWaXsf69.3B4/NtCVy2Vopck0y', 'Faculty', 'College of Computer Studies', 'M', 'None', 'active', 'admin', '2026-07-07 15:41:05'),
(5, '9999999', 'Z9Y8X7W6', 'Test', 'User', 'test@dlsu.edu.ph', '$2y$10$w9.CLLg57uv/EdtQXwjFcOG/SC1tWaXsf69.3B4/NtCVy2Vopck0y', 'BS Information Technology', 'College of Computer Studies', 'M', 'None', 'active', 'student', '2026-07-07 19:24:14'),
(6, '1215555', 'B1C2D3E4', 'Juan', 'Dela Cruz', 'juan_delacruz@dlsu.edu.ph', '$2y$10$w9.CLLg57uv/EdtQXwjFcOG/SC1tWaXsf69.3B4/NtCVy2Vopck0y', 'BS Accountancy', 'College of Business', 'M', 'Athletic Scholarship', 'active', 'student', '2026-07-10 02:15:00'),
(7, '1216666', 'F5G6H7I8', 'Maria', 'Clara', 'maria_clara@dlsu.edu.ph', '$2y$10$w9.CLLg57uv/EdtQXwjFcOG/SC1tWaXsf69.3B4/NtCVy2Vopck0y', 'AB Psychology', 'College of Liberal Arts', 'F', 'None', 'suspended', 'student', '2026-07-10 03:20:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `crowd_traffic_logs`
--
ALTER TABLE `crowd_traffic_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `organizer_id` (`organizer_id`);

--
-- Indexes for table `event_time_slots`
--
ALTER TABLE `event_time_slots`
  ADD PRIMARY KEY (`id`),
  ADD KEY `event_id` (`event_id`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`id`),
  ADD KEY `event_id` (`event_id`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `qr_code_hash` (`qr_code_hash`),
  ADD UNIQUE KEY `uq_user_slot` (`user_id`,`time_slot_id`),
  ADD KEY `time_slot_id` (`time_slot_id`),
  ADD KEY `inventory_id` (`inventory_id`);

--
-- Indexes for table `strike_logs`
--
ALTER TABLE `strike_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `reservation_id` (`reservation_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `dlsu_id` (`dlsu_id`),
  ADD UNIQUE KEY `rfid_uid` (`rfid_uid`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `crowd_traffic_logs`
--
ALTER TABLE `crowd_traffic_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `event_time_slots`
--
ALTER TABLE `event_time_slots`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1012;

--
-- AUTO_INCREMENT for table `strike_logs`
--
ALTER TABLE `strike_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`organizer_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `event_time_slots`
--
ALTER TABLE `event_time_slots`
  ADD CONSTRAINT `event_time_slots_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `inventory`
--
ALTER TABLE `inventory`
  ADD CONSTRAINT `inventory_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservations_ibfk_2` FOREIGN KEY (`time_slot_id`) REFERENCES `event_time_slots` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservations_ibfk_3` FOREIGN KEY (`inventory_id`) REFERENCES `inventory` (`id`);

--
-- Constraints for table `strike_logs`
--
ALTER TABLE `strike_logs`
  ADD CONSTRAINT `strike_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `strike_logs_ibfk_2` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
