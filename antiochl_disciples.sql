-- phpMyAdmin SQL Dump
-- version 4.0.10.7
-- http://www.phpmyadmin.net
--
-- Host: localhost:3306
-- Generation Time: Nov 04, 2015 at 08:12 PM
-- Server version: 5.5.46-cll
-- PHP Version: 5.4.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `antiochl_disciples`
--

-- --------------------------------------------------------

--
-- Table structure for table `course`
--

USE DISCIPLES;

CREATE TABLE IF NOT EXISTS `course` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `course_name` varchar(64) CHARACTER SET utf8 NOT NULL,
  `created_on` datetime NOT NULL,
  `last_updated_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `course`
--

INSERT INTO `course` (`id`, `course_name`, `created_on`, `last_updated_on`) VALUES
(1, '새가족반', '2015-10-28 00:00:00', '2015-10-29 00:07:26'),
(2, '일대일(동반자)', '2015-10-28 00:00:00', '2015-10-29 00:07:36'),
(3, '일대일(양육자)', '2015-10-28 00:00:00', '2015-10-29 00:07:36'),
(4, '크로스웨이(성경대학)', '2015-10-28 00:00:00', '2015-10-29 00:07:36'),
(5, '크로스웨이(교리대학)', '2015-10-28 00:00:00', '2015-10-29 00:07:36'),
(6, 'TE(풍성한삶)', '2015-10-28 00:00:00', '2015-10-29 00:07:36'),
(7, 'TE(성숙한삶)', '2015-10-28 00:00:00', '2015-10-29 00:07:36'),
(8, 'TE(목자의삶)', '2015-10-28 00:00:00', '2015-10-29 00:07:36');

-- --------------------------------------------------------

--
-- Table structure for table `duty`
--

CREATE TABLE IF NOT EXISTS `duty` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `duty_name` varchar(45) NOT NULL,
  `display_priority` int(11) NOT NULL,
  `created_on` datetime NOT NULL,
  `last_updated_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17 ;

--
-- Dumping data for table `duty`
--

INSERT INTO `duty` (`id`, `duty_name`, `display_priority`, `created_on`, `last_updated_on`) VALUES
(1, '목사', 1, '2014-03-26 15:06:16', '2014-03-26 20:06:16'),
(2, '부목사', 2, '2014-03-26 15:06:16', '2014-03-26 20:06:16'),
(3, '사모', 3, '2014-03-26 15:06:16', '2014-03-26 20:06:16'),
(4, '전도사', 4, '2014-03-26 15:06:16', '2014-03-26 20:06:16'),
(5, '선교사', 5, '2014-03-26 15:06:16', '2014-03-26 20:06:16'),
(6, '장로', 6, '2014-03-26 15:06:16', '2014-03-26 20:06:16'),
(7, '시무장로', 7, '2014-03-26 15:06:16', '2014-03-26 20:06:16'),
(8, '협동장로', 8, '2014-03-26 15:06:16', '2014-03-26 20:06:16'),
(9, '은퇴장로', 9, '2014-03-26 15:06:16', '2014-03-26 20:06:16'),
(10, '권사', 10, '2014-03-26 15:06:16', '2014-03-26 20:06:16'),
(11, '협동권사', 11, '2014-03-26 15:06:16', '2014-03-26 20:06:16'),
(12, '명예권사', 12, '2014-03-26 15:06:16', '2014-03-26 20:06:16'),
(13, '집사', 13, '2014-03-26 15:06:16', '2014-03-26 20:06:16'),
(14, '안수집사', 14, '2014-03-26 15:06:16', '2014-03-26 20:06:16'),
(15, '서리집사', 15, '2014-03-26 15:06:16', '2014-03-26 20:06:16'),
(16, '협동집사', 16, '2014-03-26 15:06:16', '2014-03-26 20:06:16');

-- --------------------------------------------------------

--
-- Table structure for table `family`
--

CREATE TABLE IF NOT EXISTS `family` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ind_id` int(11) NOT NULL,
  `fam_id` int(11) NOT NULL,
  `fam_relation` int(11) NOT NULL,
  `head_of_house` tinyint(1) NOT NULL DEFAULT '0',
  `created_on` datetime NOT NULL,
  `last_updated_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `family` (`ind_id`),
  KEY `relation` (`fam_relation`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `individual`
--

CREATE TABLE IF NOT EXISTS `individual` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `e_first` varchar(45) DEFAULT NULL,
  `e_last` varchar(45) DEFAULT NULL,
  `e_middle` varchar(45) DEFAULT NULL,
  `duty` tinyint(10) DEFAULT NULL,
  `birth_day` int(11) DEFAULT NULL,
  `birth_month` int(11) DEFAULT NULL,
  `birth_year` year(4) DEFAULT NULL,
  `birth_lunar` tinyint(4) NOT NULL DEFAULT '0',
  `gender` char(1) NOT NULL DEFAULT 'M',
  `email` varchar(60) DEFAULT NULL,
  `photo` varchar(128) DEFAULT NULL,
  `home_phone` varchar(10) DEFAULT NULL,
  `mobile_phone` varchar(10) DEFAULT NULL,
  `business_phone` varchar(10) DEFAULT NULL,
  `business_name` varchar(128) DEFAULT NULL,
  `street` varchar(45) DEFAULT NULL,
  `city` varchar(45) DEFAULT NULL,
  `state` varchar(2) DEFAULT NULL,
  `zip` varchar(5) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `marital_status` tinyint(10) NOT NULL DEFAULT '1',
  `baptized` tinyint(1) NOT NULL DEFAULT '0',
  `baptized_on` varchar(16) DEFAULT NULL,
  `registered_on` varchar(16) DEFAULT NULL,
  `cell` int(11) DEFAULT NULL,
  `cell_leader` tinyint(4) NOT NULL DEFAULT '0',
  `cell_co_leader` tinyint(4) NOT NULL DEFAULT '0',
  `created_on` datetime NOT NULL,
  `last_updated_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17 ;

-- --------------------------------------------------------

--
-- Table structure for table `marital`
--

CREATE TABLE IF NOT EXISTS `marital` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status_name` varchar(45) NOT NULL,
  `created_on` datetime NOT NULL,
  `last_updated_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `marital`
--

INSERT INTO `marital` (`id`, `status_name`, `created_on`, `last_updated_on`) VALUES
(1, '미혼', '2014-02-13 12:03:45', '2014-02-14 02:03:45'),
(2, '기혼', '2014-02-13 12:03:45', '2014-02-14 02:03:45'),
(3, '이혼', '2014-02-13 12:03:45', '2014-02-14 02:03:45'),
(4, '별거', '2014-02-13 12:03:45', '2014-02-14 02:03:45'),
(5, '재혼', '2014-02-13 12:03:45', '2014-02-14 02:03:45'),
(6, '사별', '2014-02-13 12:03:45', '2014-02-14 02:03:45');

-- --------------------------------------------------------

--
-- Table structure for table `nurture`
--

CREATE TABLE IF NOT EXISTS `nurture` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ind_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `completed_on` date DEFAULT NULL,
  `last_updated_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `relation`
--

CREATE TABLE IF NOT EXISTS `relation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `relation_name` varchar(45) NOT NULL,
  `created_on` datetime NOT NULL,
  `last_updated_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- Dumping data for table `relation`
--

INSERT INTO `relation` (`id`, `relation_name`, `created_on`, `last_updated_on`) VALUES
(1, '미지정', '2014-02-27 13:20:11', '2014-03-13 23:58:29'),
(2, '남편', '2014-02-27 13:20:13', '2014-02-27 19:20:13'),
(3, '부인', '2014-02-27 13:20:13', '2014-02-27 19:20:13'),
(4, '아버지', '2014-02-27 13:20:13', '2014-02-27 19:20:13'),
(5, '어머니', '2014-02-27 13:20:13', '2014-02-27 19:20:13'),
(6, '아들', '2014-02-27 13:20:13', '2014-02-27 19:20:13'),
(7, '딸', '2014-02-27 13:20:13', '2014-02-27 19:20:13'),
(8, '손자', '2014-02-27 13:20:13', '2014-02-27 19:20:13'),
(9, '손녀', '2014-02-27 13:20:13', '2014-02-27 19:20:13');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(45) NOT NULL,
  `email` varchar(60) NOT NULL,
  `first_name` varchar(45) NOT NULL,
  `last_name` varchar(45) NOT NULL,
  `hash` varchar(255) NOT NULL,
  `level` tinyint(4) NOT NULL,
  `created_on` datetime NOT NULL,
  `last_updated_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email_UNIQUE` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `email`, `first_name`, `last_name`, `hash`, `level`, `created_on`, `last_updated_on`) VALUES
(2, 'shan', 'linkedkorean@gmail.com', 'Sangwoo', 'Han', '$2a$10$m1WLjKYNN2I9ubD4kmUdL.B2JOdCng1HNt9wC1jzoVWEYG9jlB9vO', 1, '2014-02-19 21:01:51', '2014-03-24 21:14:55'),
(3, 'admin', 'myungjinko@hotmail.com', 'Myungjin', 'Ko', '$2a$10$tDuGBpWDijRALiHrILOJveQyQmtnnTJFsegdnGBdNcp/169K9S1Dy', 1, '2014-03-16 17:03:21', '2014-03-24 21:15:03'),
(4, 'guest', 'guest@antiochlife.org', 'guest', 'guest', '$2a$10$qztMH5YfQNymiV7LxkGAFu3vW.dXrOs9vyP90rOKqwXsZPfILjVL6', 0, '2014-03-24 16:19:17', '2014-03-24 21:19:17'),
(5, 'sangpark', 'sangpark003@gmail.com', 'Sang', 'Park', '$2a$10$bhrlt8ySF09W0j1Z6vaz2.6N9c2w9I8yNBEXDqiZwwJHmhMkRvpJe', 0, '2015-01-14 19:15:53', '2015-01-15 01:15:53');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `family`
--
ALTER TABLE `family`
  ADD CONSTRAINT `family` FOREIGN KEY (`ind_id`) REFERENCES `individual` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `relation` FOREIGN KEY (`fam_relation`) REFERENCES `relation` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
