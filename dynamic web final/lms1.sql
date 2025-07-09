-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 28, 2024 at 04:31 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lms1`
--

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `book_id` int(11) NOT NULL,
  `book_title` varchar(30) NOT NULL,
  `author` varchar(30) NOT NULL,
  `publisher` varchar(30) NOT NULL,
  `language` enum('English','French','German','Mandarin','Japanese','Russian','Other') DEFAULT 'English',
  `category` enum('Fiction','Nonfiction','Reference') DEFAULT 'Fiction',
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`book_id`, `book_title`, `author`, `publisher`, `language`, `category`, `image`) VALUES
(1, 'Great Expectations', 'Charles Dickens', 'Macmillan Collectors Library', 'English', 'Fiction', 'book_1.png'),
(2, 'An Inconvenient Truth', 'Al Gore', 'Penguin Books', 'English', 'Nonfiction', 'book_2.png'),
(3, 'Oxford Dictionary', 'Oxford Press', 'Oxford Press', 'English', 'Reference', 'book_3.png'),
(4, 'Anna Karenina', 'Leo Tolstoy', 'Star Publishing', 'Russian', 'Fiction', 'book_4.png'),
(5, 'The Tale of Genji', 'Murasaki Shikibu', 'Kinokuniya', 'Japanese', 'Fiction', 'book_5.png');

-- --------------------------------------------------------

--
-- Table structure for table `book_status`
--

CREATE TABLE `book_status` (
  `status_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `member_id` int(11) DEFAULT NULL,
  `status` enum('Available','Onloan','Deleted') DEFAULT 'Available',
  `applied_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `book_status`
--

INSERT INTO `book_status` (`status_id`, `book_id`, `member_id`, `status`, `applied_date`) VALUES
(24, 1, NULL, 'Available', '2024-11-29 02:29:30'),
(25, 2, NULL, 'Available', '2024-11-28 23:57:17'),
(31, 3, NULL, 'Available', '2024-11-27 22:27:18');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `member_id` int(11) NOT NULL,
  `member_type` enum('Member','Admin') DEFAULT 'Member',
  `first_name` varchar(20) NOT NULL,
  `last_name` varchar(20) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password_md5hash` char(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`member_id`, `member_type`, `first_name`, `last_name`, `email`, `password_md5hash`) VALUES
(33, 'Admin', 'Admin', 'Admin', 'admin@example.com', '21232f297a57a5a743894a0e4a801fc3'),
(41, 'Member', 'Lakshya', 'Goyal', 'Lakshyagoyal5@gmail.com', '03c8864672c69cd89846b9d7a8594a72');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`book_id`);

--
-- Indexes for table `book_status`
--
ALTER TABLE `book_status`
  ADD PRIMARY KEY (`status_id`),
  ADD KEY `book_id` (`book_id`),
  ADD KEY `member_id` (`member_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`member_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `book_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `book_status`
--
ALTER TABLE `book_status`
  MODIFY `status_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `member_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `book_status`
--
ALTER TABLE `book_status`
  ADD CONSTRAINT `book_status_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `book_status_ibfk_2` FOREIGN KEY (`member_id`) REFERENCES `users` (`member_id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
