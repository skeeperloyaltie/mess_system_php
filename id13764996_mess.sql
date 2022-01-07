-- phpMyAdmin SQL Dump
-- version 4.9.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 23, 2020 at 07:00 AM
-- Server version: 10.3.16-MariaDB
-- PHP Version: 7.3.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: ``
--

-- --------------------------------------------------------

--
-- Table structure for table `adds`
--

CREATE TABLE `adds` (
  `id` int(11) NOT NULL,
  `stat` varchar(5) NOT NULL,
  `day` date NOT NULL DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `adds`
--

INSERT INTO `adds` (`id`, `stat`, `day`) VALUES
(1, 'T', '2020-05-10'),
(1, 'T', '2020-05-11'),
(1, 'F', '2020-05-19'),
(1, 'T', '2020-05-23');

-- --------------------------------------------------------

--
-- Table structure for table `cwwdetails`
--

CREATE TABLE `cwwdetails` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `pno` bigint(20) NOT NULL,
  `department` varchar(100) NOT NULL,
  `hall` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `cwwdetails`
--

INSERT INTO `cwwdetails` (`id`, `name`, `pno`, `department`, `hall`) VALUES
(2, 'Warden', 9090909090, 'Warden Department', 'Warden Hall');

-- --------------------------------------------------------

--
-- Table structure for table `ded`
--

CREATE TABLE `ded` (
  `id` int(11) NOT NULL,
  `reason` varchar(200) NOT NULL,
  `amt` bigint(20) NOT NULL,
  `ded` int(11) NOT NULL,
  `day` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `ded`
--

INSERT INTO `ded` (`id`, `reason`, `amt`, `ded`, `day`) VALUES
(4, 'coupon code', 100, 0, '2020-05-10'),
(4, 'dfdf', 12, 1, '2020-05-10'),
(4, 'dfdffd', 12, 1, '2020-05-10'),
(4, 'donation', 10, 0, '2020-05-10'),
(4, 'donation', 100, 0, '2020-05-10'),
(4, 'finances', 12, 0, '2020-05-10'),
(4, 'finances', 100, 0, '2020-05-10'),
(4, 'For onions', 100, 1, '2020-05-10'),
(4, 'Mehant', 100, 0, '2020-05-10'),
(4, 'mess fees', 100, 0, '2020-05-10');

-- --------------------------------------------------------

--
-- Table structure for table `finance`
--

CREATE TABLE `finance` (
  `id` int(11) NOT NULL,
  `amount` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `finance`
--

INSERT INTO `finance` (`id`, `amount`) VALUES
(0, 0),
(1, 0),
(2, 0),
(4, 710),
(16, 0);

-- --------------------------------------------------------

--
-- Table structure for table `mratings`
--

CREATE TABLE `mratings` (
  `id` int(11) NOT NULL,
  `mid` int(11) DEFAULT NULL,
  `breakfast` int(11) NOT NULL,
  `lunch` int(11) NOT NULL,
  `snacks` int(11) NOT NULL,
  `dinner` int(11) NOT NULL,
  `day` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `mratings`
--

INSERT INTO `mratings` (`id`, `mid`, `breakfast`, `lunch`, `snacks`, `dinner`, `day`) VALUES
(1, 4, 1, 1, 1, 1, '2020-05-09'),
(1, 4, 1, 2, 1, 1, '2020-05-10'),
(1, 16, 1, 4, 3, 1, '2020-05-11');

-- --------------------------------------------------------

--
-- Table structure for table `mrdetails`
--

CREATE TABLE `mrdetails` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `pno` bigint(15) NOT NULL,
  `rating` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `mrdetails`
--

INSERT INTO `mrdetails` (`id`, `name`, `email`, `pno`, `rating`) VALUES
(4, 'Ramesh', 'Ramesh@gmail.com', 9890989098, 3),
(16, 'Star', 'star@google.com', 9090989098, 4);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `status` varchar(5) NOT NULL DEFAULT 'F',
  `path` varchar(100) NOT NULL,
  `roll` bigint(20) NOT NULL,
  `reg` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `status`, `path`, `roll`, `reg`) VALUES
(1, 'T', './uploads/payments/5eb6c316a286c9.64694060.png', 411843, 982158);

-- --------------------------------------------------------

--
-- Table structure for table `ratings`
--

CREATE TABLE `ratings` (
  `id` int(11) NOT NULL,
  `rate` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `ratings`
--

INSERT INTO `ratings` (`id`, `rate`) VALUES
(1, 4);

--
-- Triggers `ratings`
--
DELIMITER $$
CREATE TRIGGER `ratingInsert` AFTER INSERT ON `ratings` FOR EACH ROW UPDATE mrdetails
    SET rating = (SELECT AVG(rate) FROM ratings)
    WHERE id = (SELECT id FROM mrdetails ORDER BY id DESC LIMIT 1)
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `ratingUpdate` AFTER UPDATE ON `ratings` FOR EACH ROW UPDATE mrdetails
    SET rating = (SELECT AVG(rate) FROM ratings)
    WHERE id = (SELECT id FROM mrdetails ORDER BY id DESC LIMIT 1)
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `timetable`
--

CREATE TABLE `timetable` (
  `id` int(11) NOT NULL,
  `path` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `timetable`
--

INSERT INTO `timetable` (`id`, `path`) VALUES
(4, './uploads/mess/5eb6d032782fb2.51467463.jpeg'),
(5, './uploads/mess/5ec804c0c962b9.68538163.jpg'),
(6, './uploads/mess/5ec805123a4642.20109199.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `usertype` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `usertype`) VALUES
(1, '411843@student.nitandhra.ac.in', '1234', 's'),
(2, 'a@gmail.com', 'abcd', 'w'),
(4, 'ma@gmail.com', 'ma', 'ma'),
(5, 'a@g.com', 'a', 'a'),
(16, 'Stars@gmail.com', 'star', 'ma'),
(54, '411801@student.nitandhra.ac.in', '1234', 's'),
(55, '411802@student.nitandhra.ac.in', '1234', 's'),
(56, '411803@student.nitandhra.ac.in', '1234', 's'),
(57, '411804@student.nitandhra.ac.in', '1234', 's'),
(58, '411805@student.nitandhra.ac.in', '1234', 's'),
(59, '411806@student.nitandhra.ac.in', '1234', 's'),
(60, '411807@student.nitandhra.ac.in', '1234', 's'),
(61, '411808@student.nitandhra.ac.in', '1234', 's'),
(62, '411809@student.nitandhra.ac.in', '1234', 's'),
(63, '411810@student.nitandhra.ac.in', '1234', 's'),
(64, '411811@student.nitandhra.ac.in', '1234', 's'),
(65, '411812@student.nitandhra.ac.in', '1234', 's'),
(66, '411813@student.nitandhra.ac.in', '1234', 's'),
(67, '411814@student.nitandhra.ac.in', '1234', 's'),
(68, '411815@student.nitandhra.ac.in', '1234', 's'),
(69, 'cw@nitandhra.ac.in', '1234', 'cw'),
(70, 'w@nitandhra.ac.in', '1234', 'w'),
(71, 'a@nitandhra.ac.in', '1234', 'a');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `adds`
--
ALTER TABLE `adds`
  ADD PRIMARY KEY (`id`,`day`) USING BTREE;

--
-- Indexes for table `cwwdetails`
--
ALTER TABLE `cwwdetails`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ded`
--
ALTER TABLE `ded`
  ADD PRIMARY KEY (`id`,`reason`,`amt`);

--
-- Indexes for table `finance`
--
ALTER TABLE `finance`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mratings`
--
ALTER TABLE `mratings`
  ADD PRIMARY KEY (`day`,`id`) USING BTREE;

--
-- Indexes for table `mrdetails`
--
ALTER TABLE `mrdetails`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `timetable`
--
ALTER TABLE `timetable`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ded`
--
ALTER TABLE `ded`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `mrdetails`
--
ALTER TABLE `mrdetails`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `timetable`
--
ALTER TABLE `timetable`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
