-- phpMyAdmin SQL Dump
-- version 2.11.11.3
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 02, 2014 at 10:06 AM
-- Server version: 5.0.95
-- PHP Version: 5.1.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `ebook`
--

-- --------------------------------------------------------

--
-- Table structure for table `backupUpdates`
--

CREATE TABLE IF NOT EXISTS `backupUpdates` (
  `id` int(11) NOT NULL auto_increment,
  `author` text NOT NULL,
  `tiddlerName` text NOT NULL,
  `tiddlerData` text NOT NULL,
  `bookName` text NOT NULL,
  `sendTime` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `systemUpdate` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;

-- --------------------------------------------------------

--
-- Table structure for table `bookAuthor`
--

CREATE TABLE IF NOT EXISTS `bookAuthor` (
  `id` int(11) NOT NULL auto_increment,
  `name` text NOT NULL,
  `userkey` text NOT NULL,
  `role` int(11) NOT NULL default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;

-- --------------------------------------------------------

--
-- Table structure for table `bookUpdates`
--

CREATE TABLE IF NOT EXISTS `bookUpdates` (
  `id` int(11) NOT NULL auto_increment,
  `data` mediumtext NOT NULL,
  `times` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `uppdatedTiddler` text NOT NULL,
  `bookName` text,
  `systemUpdate` tinyint(4) NOT NULL default '0',
  `language` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;

-- --------------------------------------------------------

--
-- Table structure for table `comment`
--

CREATE TABLE IF NOT EXISTS `comment` (
  `id` int(11) NOT NULL auto_increment,
  `tiddlername` text NOT NULL,
  `textIndex` int(11) NOT NULL default '0',
  `kom_id` text NOT NULL,
  `val_text` text NOT NULL,
  `kommentti` text,
  `korjaus` text,
  `time` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `isChecked` tinyint(4) NOT NULL default '0',
  `commentView` text NOT NULL,
  PRIMARY KEY  (`id`),
  FULLTEXT KEY `tiddlername` (`tiddlername`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;

-- --------------------------------------------------------

--
-- Table structure for table `containerLock`
--

CREATE TABLE IF NOT EXISTS `containerLock` (
  `id` int(11) NOT NULL auto_increment,
  `Auothor` text NOT NULL,
  `bookName` text NOT NULL,
  `container` text NOT NULL,
  `lockTime` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;

-- --------------------------------------------------------


--
-- Table structure for table `CourseUpdatesSend`
--

CREATE TABLE IF NOT EXISTS `CourseUpdatesSend` (
  `id` int(11) NOT NULL auto_increment,
  `lastSend` datetime default NULL,
  `bookName` text NOT NULL,
  `type` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;

-- --------------------------------------------------------

--
-- Table structure for table `ebooks`
--

CREATE TABLE IF NOT EXISTS `ebooks` (
  `id` int(11) NOT NULL auto_increment,
  `bookid` text NOT NULL,
  `description` text NOT NULL,
  `type` int(11) NOT NULL,
  `langs` text NOT NULL,
  `creator` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Awailable ebooks' AUTO_INCREMENT=0 ;

-- --------------------------------------------------------

--
-- Table structure for table `ebooktypes`
--

CREATE TABLE IF NOT EXISTS `ebooktypes` (
  `id` int(11) NOT NULL auto_increment,
  `typenro` int(11) NOT NULL,
  `typetext` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE IF NOT EXISTS `groups` (
  `id` int(11) NOT NULL auto_increment,
  `user` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `user` (`user`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;

-- --------------------------------------------------------

--
-- Table structure for table `loginfail`
--

CREATE TABLE IF NOT EXISTS `loginfail` (
  `id` int(11) NOT NULL auto_increment,
  `username` text NOT NULL,
  `userkey` text NOT NULL,
  `info` text NOT NULL,
  `ip` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;

-- --------------------------------------------------------

--
-- Table structure for table `pageComment`
--

CREATE TABLE IF NOT EXISTS `pageComment` (
  `id` int(11) NOT NULL auto_increment,
  `time` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `author` text NOT NULL,
  `tiddlerName` text NOT NULL,
  `tiddlerData` text NOT NULL,
  `bookId` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;

-- --------------------------------------------------------

--
-- Table structure for table `pageCommentCourse`
--

CREATE TABLE IF NOT EXISTS `pageCommentCourse` (
  `id` int(11) NOT NULL auto_increment,
  `time` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `author` text NOT NULL,
  `tiddlerName` text NOT NULL,
  `tiddlerData` text NOT NULL,
  `bookId` text NOT NULL,
  `version` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;

-- --------------------------------------------------------

--
-- Table structure for table `preUpdate`
--

CREATE TABLE IF NOT EXISTS `preUpdate` (
  `id` int(11) NOT NULL auto_increment,
  `author` text NOT NULL,
  `tiddlerName` text NOT NULL,
  `tiddlerData` text NOT NULL,
  `bookName` text NOT NULL,
  `sendTime` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `systemUpdate` tinyint(4) NOT NULL default '0',
  `newTiddler` tinyint(4) NOT NULL default '0',
  `language` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE IF NOT EXISTS `roles` (
  `rolenro` int(11) NOT NULL,
  `rolename` varchar(10) NOT NULL,
  PRIMARY KEY  (`rolenro`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

