-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 06, 2025 at 07:07 PM
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
-- Database: `mental_health`
--

-- --------------------------------------------------------

--
-- Table structure for table `ai_analysis`
--

CREATE TABLE `ai_analysis` (
  `Analysis_ID` int(11) NOT NULL,
  `Journal_ID` int(11) NOT NULL,
  `Sentiment_Score` decimal(4,2) DEFAULT NULL,
  `Suggestions` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ai_analysis`
--

INSERT INTO `ai_analysis` (`Analysis_ID`, `Journal_ID`, `Sentiment_Score`, `Suggestions`) VALUES
(1, 3, 7.00, 'You are doing well! Try yoga, talk to friends more often.'),
(7, 9, 5.00, 'You are calm today, try to take less stress, eat proteins.');

-- --------------------------------------------------------

--
-- Table structure for table `health_factor`
--

CREATE TABLE `health_factor` (
  `Factor_ID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `Medication` varchar(150) DEFAULT NULL,
  `Exercise_duration` int(11) DEFAULT NULL,
  `Sleep_Hours` decimal(4,1) DEFAULT NULL,
  `Diet_Type` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `health_factor`
--

INSERT INTO `health_factor` (`Factor_ID`, `UserID`, `Medication`, `Exercise_duration`, `Sleep_Hours`, `Diet_Type`) VALUES
(2, 23101506, 'Pain killer', 1, 8.0, 'keto');

-- --------------------------------------------------------

--
-- Table structure for table `journal`
--

CREATE TABLE `journal` (
  `Journal_ID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `Journal_entry` text DEFAULT NULL,
  `Date` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `journal`
--

INSERT INTO `journal` (`Journal_ID`, `UserID`, `Journal_entry`, `Date`) VALUES
(3, 23101504, 'Today was one of those days where my emotions felt like a rollercoaster. I woke up feeling kind of sluggish, almost like my body was trying to tell me to slow down. The weather was gloomy too, which didn’t help my mood at all. I had my morning coffee, but even that didn’t kick-start my energy the way it usually does.\n\nI tried to focus on work, but my mind kept drifting. Everything felt a little overwhelming — emails piling up, deadlines creeping closer, and that nagging feeling that I’m falling behind on everything. Around lunchtime, I decided to take a short walk outside just to clear my head. Surprisingly, it helped a lot. The fresh air, the sound of birds, even the way the wind brushed against my face made me feel a little lighter.\n\nIn the afternoon, I had a long call with a friend I hadn’t spoken to in months. We talked about life, how fast time seems to pass, and all the small victories we tend to overlook. That conversation made me realize I’ve been too hard on myself lately. I keep focusing on what I haven’t achieved instead of appreciating how far I’ve already come.\n\nBy evening, my mood had shifted. I still feel a bit tired, but also calmer. I made myself a simple dinner, put on some relaxing music, and journaled about the day — which honestly feels therapeutic.\n\nI guess today taught me something important: it’s okay to have slow days. It’s okay to pause, breathe, and not have everything figured out. Sometimes, progress looks like taking one tiny step at a time.', '2025-08-28'),
(9, 23101506, 'Today felt like a reset button in my mind. I woke up later than usual but didn’t rush myself, letting the morning unfold slowly. I brewed coffee, listened to soft music, and noticed how the sunlight touched the edges of my desk. At work, things were steady, not overwhelming, though I caught myself drifting into daydreams. My mood stayed mostly balanced, somewhere between calm and slightly restless. I ended the evening with a short walk, which surprisingly cleared my thoughts. Writing this down helps me realize how even ordinary days carry quiet moments worth remembering. I feel lighter now.', '2025-09-06');

-- --------------------------------------------------------

--
-- Table structure for table `progress_report`
--

CREATE TABLE `progress_report` (
  `Report_ID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `Start_Date` date NOT NULL,
  `End_Date` date DEFAULT NULL,
  `Avg_moodScore` decimal(3,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `progress_report`
--

INSERT INTO `progress_report` (`Report_ID`, `UserID`, `Start_Date`, `End_Date`, `Avg_moodScore`) VALUES
(1, 23101506, '2025-09-06', NULL, 9.10);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `UserID` int(11) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Gender` enum('Male','Female','Other','') NOT NULL,
  `Date_of_birth` date NOT NULL,
  `Email` varchar(150) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Height` decimal(5,2) NOT NULL,
  `Weight` decimal(5,2) NOT NULL,
  `Joining_Date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`UserID`, `Name`, `Gender`, `Date_of_birth`, `Email`, `Password`, `Height`, `Weight`, `Joining_Date`) VALUES
(23101504, 'hello world', 'Male', '2000-01-01', 'helloworld@gmail.com', '@Mpassword06', 172.00, 70.00, '0000-00-00'),
(23101505, 'ai connect', 'Male', '2000-01-01', 'aiconnect@gmail.com', 'helloworld', 150.00, 35.00, '0000-00-00'),
(23101506, 'Maisha Maisara', 'Female', '2002-12-06', 'maisha.maisara@g.bracu.ac.bd', 'a1b2c3', 152.00, 48.00, '0000-00-00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ai_analysis`
--
ALTER TABLE `ai_analysis`
  ADD PRIMARY KEY (`Analysis_ID`),
  ADD KEY `fk_analysis_journalID` (`Journal_ID`);

--
-- Indexes for table `health_factor`
--
ALTER TABLE `health_factor`
  ADD PRIMARY KEY (`Factor_ID`),
  ADD KEY `fk_healthfactor_user` (`UserID`);

--
-- Indexes for table `journal`
--
ALTER TABLE `journal`
  ADD PRIMARY KEY (`Journal_ID`),
  ADD KEY `fk_journal_user` (`UserID`);

--
-- Indexes for table `progress_report`
--
ALTER TABLE `progress_report`
  ADD PRIMARY KEY (`Report_ID`),
  ADD KEY `fk_progRep_user` (`UserID`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`UserID`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ai_analysis`
--
ALTER TABLE `ai_analysis`
  MODIFY `Analysis_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `health_factor`
--
ALTER TABLE `health_factor`
  MODIFY `Factor_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `journal`
--
ALTER TABLE `journal`
  MODIFY `Journal_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `progress_report`
--
ALTER TABLE `progress_report`
  MODIFY `Report_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23101507;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `ai_analysis`
--
ALTER TABLE `ai_analysis`
  ADD CONSTRAINT `fk_analysis_journalID` FOREIGN KEY (`Journal_ID`) REFERENCES `journal` (`Journal_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `health_factor`
--
ALTER TABLE `health_factor`
  ADD CONSTRAINT `fk_healthfactor_user` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `journal`
--
ALTER TABLE `journal`
  ADD CONSTRAINT `fk_journal_user` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `progress_report`
--
ALTER TABLE `progress_report`
  ADD CONSTRAINT `fk_progRep_user` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
