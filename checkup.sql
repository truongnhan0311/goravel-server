-- phpMyAdmin SQL Dump
-- version 4.4.6.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 18, 2017 at 08:16 PM
-- Server version: 5.6.24
-- PHP Version: 5.4.41

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `airbnbsthr_tool`
--

-- --------------------------------------------------------

--
-- Table structure for table `checkup_answers`
--

DROP TABLE IF EXISTS `checkup_answers`;
CREATE TABLE IF NOT EXISTS `checkup_answers` (
  `id` bigint(20) NOT NULL,
  `report_id` varchar(200) NOT NULL,
  `question_id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `property_id` int(11) DEFAULT NULL,
  `description` text NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `checkup_answers`
--

INSERT INTO `checkup_answers` (`id`, `report_id`, `question_id`, `employee_id`, `property_id`, `description`, `created`) VALUES
(1, 'c81e728d9d4c2f636f067f89cc14862c', 1, 7, 2, 'blue', '2017-03-15 13:18:54'),
(2, 'c81e728d9d4c2f636f067f89cc14862c', 3, 7, 2, 'looks beautiful', '2017-03-15 13:19:02');

-- --------------------------------------------------------

--
-- Table structure for table `checkup_answers_attachments`
--

DROP TABLE IF EXISTS `checkup_answers_attachments`;
CREATE TABLE IF NOT EXISTS `checkup_answers_attachments` (
  `id` int(11) NOT NULL,
  `answer_id` int(11) NOT NULL,
  `type` enum('image','video','','') NOT NULL,
  `file` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `checkup_questions`
--

DROP TABLE IF EXISTS `checkup_questions`;
CREATE TABLE IF NOT EXISTS `checkup_questions` (
  `id` int(11) NOT NULL,
  `description` text,
  `status` enum('active','disabled') NOT NULL DEFAULT 'active'
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `checkup_questions`
--

INSERT INTO `checkup_questions` (`id`, `description`, `status`) VALUES
(1, 'What is the color of carpet', 'active'),
(2, 'what is width', 'active'),
(3, 'what is the look of room', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `checkup_questions_attachments`
--

DROP TABLE IF EXISTS `checkup_questions_attachments`;
CREATE TABLE IF NOT EXISTS `checkup_questions_attachments` (
  `id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `type` enum('image','video','','') NOT NULL,
  `file` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `checkup_reports`
--

DROP TABLE IF EXISTS `checkup_reports`;
CREATE TABLE IF NOT EXISTS `checkup_reports` (
  `id` int(11) NOT NULL,
  `title` varchar(500) NOT NULL,
  `property_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `template_id` varchar(200) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `checkup_reports`
--

INSERT INTO `checkup_reports` (`id`, `title`, `property_id`, `employee_id`, `template_id`) VALUES
(1, 'test report', 2, 7, 'c81e728d9d4c2f636f067f89cc14862c');

-- --------------------------------------------------------

--
-- Table structure for table `checkup_templates`
--

DROP TABLE IF EXISTS `checkup_templates`;
CREATE TABLE IF NOT EXISTS `checkup_templates` (
  `id` bigint(20) NOT NULL,
  `questions` text,
  `title` varchar(300) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `checkup_templates`
--

INSERT INTO `checkup_templates` (`id`, `questions`, `title`, `created`) VALUES
(1, 'a:2:{i:0;s:1:"2";i:1;s:1:"3";}', 'This is test report', '2017-03-15 08:17:38'),
(2, 'a:2:{i:0;s:1:"1";i:1;s:1:"3";}', 'this is another report', '2017-03-15 08:17:56');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `checkup_answers`
--
ALTER TABLE `checkup_answers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `checkup_answers_attachments`
--
ALTER TABLE `checkup_answers_attachments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `checkup_questions`
--
ALTER TABLE `checkup_questions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `checkup_questions_attachments`
--
ALTER TABLE `checkup_questions_attachments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `checkup_reports`
--
ALTER TABLE `checkup_reports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `checkup_templates`
--
ALTER TABLE `checkup_templates`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `checkup_answers`
--
ALTER TABLE `checkup_answers`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `checkup_answers_attachments`
--
ALTER TABLE `checkup_answers_attachments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `checkup_questions`
--
ALTER TABLE `checkup_questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `checkup_questions_attachments`
--
ALTER TABLE `checkup_questions_attachments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `checkup_reports`
--
ALTER TABLE `checkup_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `checkup_templates`
--
ALTER TABLE `checkup_templates`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
