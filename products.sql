-- phpMyAdmin SQL Dump
-- version 3.3.8
-- http://www.phpmyadmin.net
--
-- Vært: localhost
-- Genereringstid: 28. 07 2015 kl. 20:50:05
-- Serverversion: 5.1.61
-- PHP-version: 5.3.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `dury`
--

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `languages`
--

CREATE TABLE IF NOT EXISTS `languages` (
  `languages_id` int(11) NOT NULL AUTO_INCREMENT,
  `languages_name` varchar(255) NOT NULL,
  PRIMARY KEY (`languages_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Data dump for tabellen `languages`
--

INSERT INTO `languages` (`languages_id`, `languages_name`) VALUES
(1, 'Danish'),
(2, 'English'),
(3, 'Norwegian');

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `products`
--

CREATE TABLE IF NOT EXISTS `products` (
  `products_id` int(11) NOT NULL AUTO_INCREMENT,
  `products_reference` varchar(255) NOT NULL,
  `products_price` float NOT NULL,
  PRIMARY KEY (`products_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Data dump for tabellen `products`
--

INSERT INTO `products` (`products_id`, `products_reference`, `products_price`) VALUES
(1, 'KI-0500', 189),
(2, '600500', 50);

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `products_description`
--

CREATE TABLE IF NOT EXISTS `products_description` (
  `products_description_id` int(11) NOT NULL AUTO_INCREMENT,
  `products_id` int(11) NOT NULL,
  `languages_id` int(11) NOT NULL,
  `products_description_name` varchar(255) NOT NULL,
  `products_description_short_description` text NOT NULL,
  `products_description_description` text NOT NULL,
  PRIMARY KEY (`products_description_id`),
  KEY `products_id` (`products_id`,`languages_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Data dump for tabellen `products_description`
--

INSERT INTO `products_description` (`products_description_id`, `products_id`, `languages_id`, `products_description_name`, `products_description_short_description`, `products_description_description`) VALUES
(1, 1, 1, 'Hæklenålesæt', 'Godt hæklenålesæt', 'Meget mere info om hæklenålene'),
(2, 1, 2, 'Crochet hook set', 'Some text', 'More text'),
(3, 1, 3, 'Heklenåleset', 'Veldig bra heklenåleset', 'Mer på settet'),
(4, 2, 1, 'Diverse produkt', 'Lidt tekst', 'Mere tekst\r\n<p>Hej <u>med</u> dig</p>'),
(5, 2, 2, 'Miscellaneous', 'Text miscellaneous', 'Text miscellaneous more more more'),
(6, 2, 3, 'Norsk diverse', 'Tekst nummer 1', 'Mer tekst på norsk');
