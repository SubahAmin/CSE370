-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 24, 2026 at 06:27 PM
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
-- Database: `secondhandassistivedevicelending`
--

-- --------------------------------------------------------

--
-- Table structure for table `device`
--

CREATE TABLE `device` (
  `DEVICE_ID` varchar(100) NOT NULL,
  `DEVICE_CONDITION` varchar(300) NOT NULL,
  `STATUS` varchar(300) NOT NULL,
  `DONATION_ID` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `device`
--

INSERT INTO `device` (`DEVICE_ID`, `DEVICE_CONDITION`, `STATUS`, `DONATION_ID`) VALUES
('C1', 'Perfect', 'Donated', 'D2'),
('C2', 'Slightly Damaged', 'Maintainence', 'D1'),
('DEV1786985348', 'Perfect', 'Donated', 'D1786985348'),
('DEV1786986378', 'slightly damaged', 'Donated', 'D1786986378'),
('DEV1786986503', 'slightly damaged', 'Storage', 'D1786986503'),
('W1', 'Perfect', 'Donated', 'D3'),
('W2', 'Perfect', 'Storage', 'D4');

-- --------------------------------------------------------

--
-- Table structure for table `donations`
--

CREATE TABLE `donations` (
  `DONATION_ID` varchar(100) NOT NULL,
  `DONATION_DATE` date NOT NULL,
  `DEVICE_TYPE` varchar(200) NOT NULL,
  `DESCRIPTION_TEXT` varchar(300) NOT NULL,
  `DONOR_ID` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `donations`
--

INSERT INTO `donations` (`DONATION_ID`, `DONATION_DATE`, `DEVICE_TYPE`, `DESCRIPTION_TEXT`, `DONOR_ID`) VALUES
('D1', '2026-01-05', 'Crutch', 'Forearm Crutch', 'admin2'),
('D1786985348', '2026-08-17', 'Wheelchair', 'Travel Wheelchair', 'admin1'),
('D1786986378', '2026-08-17', 'Crutch', 'Underarm Crutch', 'U178698632557'),
('D1786986503', '2026-08-17', 'Wheelchair', 'Travel Wheelchair', 'U178698632557'),
('D2', '2025-04-22', 'Crutch', 'Underarm Crutch', 'admin2'),
('D3', '2025-08-01', 'Wheelchair', 'Travel Wheelchair', 'admin1'),
('D4', '2026-07-14', 'Pedriatic Wheelchair', '', 'admin2');

-- --------------------------------------------------------

--
-- Table structure for table `loan`
--

CREATE TABLE `loan` (
  `LOAN_ID` varchar(50) NOT NULL,
  `START_DATE` date NOT NULL,
  `DUE_DATE` date NOT NULL,
  `ACTUAL_RETURN_DATE` date NOT NULL,
  `OVERDUE_STATUS` varchar(100) NOT NULL,
  `COORDINATOR_ID` varchar(100) NOT NULL,
  `REQUEST_ID` varchar(100) NOT NULL,
  `DEVICE_ID` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `loan`
--

INSERT INTO `loan` (`LOAN_ID`, `START_DATE`, `DUE_DATE`, `ACTUAL_RETURN_DATE`, `OVERDUE_STATUS`, `COORDINATOR_ID`, `REQUEST_ID`, `DEVICE_ID`) VALUES
('Loan12', '2026-07-18', '2028-08-01', '0000-00-00', 'No', 'admin2', 'R1', 'W2'),
('LOAN1786985387', '2026-08-17', '2026-08-27', '0000-00-00', 'No', 'admin2', 'REQ1786985387', 'C1'),
('LOAN1786987093', '2026-08-17', '2026-09-16', '0000-00-00', 'No', 'U178698632557', 'REQ1786987093', 'DEV1786986378'),
('LOAN1786987134', '2026-08-17', '2026-11-25', '0000-00-00', 'No', 'U178698632557', 'REQ1786987134', 'DEV1786985348'),
('LOAN3', '2022-08-30', '2026-08-01', '0000-00-00', '', '', 'REQ1786985387', 'DEV1786985348'),
('LOAN4', '2025-09-13', '2026-06-29', '0000-00-00', '', '', 'REQ1787583144', 'C1'),
('LOAN5', '2022-08-30', '2026-08-01', '0000-00-00', '', '', 'R3', 'W2'),
('LOAN6', '2026-07-15', '2026-08-21', '0000-00-00', '', '', 'REQ1786985703', 'DEV1786986503');

-- --------------------------------------------------------

--
-- Table structure for table `maintainence`
--

CREATE TABLE `maintainence` (
  `MAINTAINENCE_ID` varchar(50) NOT NULL,
  `LOG_DATE` date NOT NULL,
  `CLEARED_FOR_LENDING` varchar(10) NOT NULL,
  `NOTES` varchar(300) NOT NULL,
  `COORDINATOR_ID` varchar(100) NOT NULL,
  `DEVICE_ID` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `maintainence`
--

INSERT INTO `maintainence` (`MAINTAINENCE_ID`, `LOG_DATE`, `CLEARED_FOR_LENDING`, `NOTES`, `COORDINATOR_ID`, `DEVICE_ID`) VALUES
('M1', '2026-08-11', 'No', 'Clean, but scratched, condition mostly okay', 'admin2', 'C2');

-- --------------------------------------------------------

--
-- Table structure for table `requests`
--

CREATE TABLE `requests` (
  `REQUEST_ID` varchar(100) NOT NULL,
  `REQUEST_DATE` date NOT NULL,
  `ESTIMATED_DURATION` int(11) NOT NULL,
  `BORROWER_ID` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `requests`
--

INSERT INTO `requests` (`REQUEST_ID`, `REQUEST_DATE`, `ESTIMATED_DURATION`, `BORROWER_ID`) VALUES
('R1', '2026-06-02', 56, 'admin2'),
('R2', '2022-07-19', 67, 'admin1'),
('R3', '2026-04-16', 200, 'maisha.maliha.mim@g.bracu.ac.bd'),
('REQ1786985387', '2026-08-17', 10, 'admin2'),
('REQ1786985703', '2026-07-01', 60, 'saimaalam@gmail.com'),
('REQ1786987093', '2026-08-17', 30, 'U178698632557'),
('REQ1786987134', '2026-08-17', 100, 'U178698632557'),
('REQ1787560992', '2026-08-24', 40, 'subahamin@gmail.com'),
('REQ1787583144', '2026-08-24', 60, 'subahamin@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `USER_ID` varchar(100) NOT NULL,
  `EMAIL` varchar(100) NOT NULL,
  `PASSWORD` varchar(200) NOT NULL,
  `DONOR_FLAG` varchar(100) NOT NULL,
  `BORROWER_FLAG` varchar(100) NOT NULL,
  `COORDINATOR_FLAG` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`USER_ID`, `EMAIL`, `PASSWORD`, `DONOR_FLAG`, `BORROWER_FLAG`, `COORDINATOR_FLAG`) VALUES
('', 'izaanahsan@gmail.com', 'admin1', 'NO', 'NO', 'YES'),
('', 'maisha.maliha.mim@g.bracu.ac.bd', 'admin2', '', 'YES', ''),
('', 'subah.amin1@g.bracu.ac.bd', 'admin1', '', '', ''),
('', 'subahamin@gmail.com', 'admin1', 'YES', 'YES', 'NO'),
('', 'saimaalam@gmail.com', 'b2', 'NO', 'YES', 'NO');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `username` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`username`, `password`) VALUES
('admin', 'admin123'),
('user', 'user123');

-- --------------------------------------------------------

--
-- Table structure for table `waitlist`
--

CREATE TABLE `waitlist` (
  `WAITLIST_ID` varchar(50) NOT NULL,
  `QUEUE_POSITION` int(11) NOT NULL,
  `NOTIFIED_FLAG` varchar(50) NOT NULL,
  `REQUEST_ID` varchar(50) NOT NULL,
  `DEVICE_ID` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `waitlist`
--

INSERT INTO `waitlist` (`WAITLIST_ID`, `QUEUE_POSITION`, `NOTIFIED_FLAG`, `REQUEST_ID`, `DEVICE_ID`) VALUES
('WL1', 1, 'YES', 'R1', 'C2'),
('W2', 2, 'NO', 'R2', 'W1'),
('WL1787560992', 1, 'NO', 'REQ1787560992', 'C1'),
('WL1787583144', 2, 'NO', 'REQ1787583144', 'C1');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `device`
--
ALTER TABLE `device`
  ADD PRIMARY KEY (`DEVICE_ID`),
  ADD KEY `DONATION_ID` (`DONATION_ID`);

--
-- Indexes for table `donations`
--
ALTER TABLE `donations`
  ADD PRIMARY KEY (`DONATION_ID`),
  ADD KEY `DONOR_ID` (`DONOR_ID`);

--
-- Indexes for table `loan`
--
ALTER TABLE `loan`
  ADD PRIMARY KEY (`LOAN_ID`),
  ADD KEY `COORDINATOR_ID` (`COORDINATOR_ID`),
  ADD KEY `REQUEST_ID` (`REQUEST_ID`),
  ADD KEY `DEVICE_ID` (`DEVICE_ID`);

--
-- Indexes for table `maintainence`
--
ALTER TABLE `maintainence`
  ADD PRIMARY KEY (`MAINTAINENCE_ID`),
  ADD KEY `COORDINATOR_ID` (`COORDINATOR_ID`),
  ADD KEY `DEVICE_ID` (`DEVICE_ID`);

--
-- Indexes for table `requests`
--
ALTER TABLE `requests`
  ADD PRIMARY KEY (`REQUEST_ID`);

--
-- Indexes for table `waitlist`
--
ALTER TABLE `waitlist`
  ADD KEY `REQUEST_ID` (`REQUEST_ID`),
  ADD KEY `DEVICE_ID` (`DEVICE_ID`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `device`
--
ALTER TABLE `device`
  ADD CONSTRAINT `device_ibfk_1` FOREIGN KEY (`DONATION_ID`) REFERENCES `donations` (`DONATION_ID`);

--
-- Constraints for table `loan`
--
ALTER TABLE `loan`
  ADD CONSTRAINT `loan_ibfk_2` FOREIGN KEY (`REQUEST_ID`) REFERENCES `requests` (`REQUEST_ID`),
  ADD CONSTRAINT `loan_ibfk_3` FOREIGN KEY (`DEVICE_ID`) REFERENCES `device` (`DEVICE_ID`);

--
-- Constraints for table `maintainence`
--
ALTER TABLE `maintainence`
  ADD CONSTRAINT `maintainence_ibfk_2` FOREIGN KEY (`DEVICE_ID`) REFERENCES `device` (`DEVICE_ID`);

--
-- Constraints for table `waitlist`
--
ALTER TABLE `waitlist`
  ADD CONSTRAINT `waitlist_ibfk_1` FOREIGN KEY (`REQUEST_ID`) REFERENCES `requests` (`REQUEST_ID`),
  ADD CONSTRAINT `waitlist_ibfk_2` FOREIGN KEY (`DEVICE_ID`) REFERENCES `device` (`DEVICE_ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
