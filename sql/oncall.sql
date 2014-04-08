-- MySQL dump 10.14  Distrib 5.5.32-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: oncall
-- ------------------------------------------------------
-- Server version	5.5.32-MariaDB-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `AccountCounter`
--

DROP TABLE IF EXISTS `AccountCounter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `AccountCounter` (
  `date_in` date NOT NULL,
  `user_id` int(9) unsigned NOT NULL,
  `client_count` int(9) unsigned NOT NULL DEFAULT '0',
  `number_count` int(9) unsigned NOT NULL DEFAULT '0',
  `call_count` int(9) unsigned NOT NULL DEFAULT '0',
  `duration` bigint(15) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`date_in`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `AdGroup`
--

DROP TABLE IF EXISTS `AdGroup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `AdGroup` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `campaign_id` int(11) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `status` smallint(2) unsigned NOT NULL,
  `date_create` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Advert`
--

DROP TABLE IF EXISTS `Advert`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Advert` (
  `id` int(9) unsigned NOT NULL AUTO_INCREMENT,
  `adgroup_id` int(9) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `number_id` bigint(15) unsigned DEFAULT NULL,
  `destination` bigint(15) unsigned DEFAULT NULL,
  `xml_replace` text,
  `xml_override` smallint(1) unsigned NOT NULL DEFAULT '0',
  `status` smallint(2) unsigned NOT NULL,
  `date_create` date NOT NULL,
  `record` smallint(1) NOT NULL DEFAULT '0',
  `speak` smallint(1) NOT NULL DEFAULT '0',
  `speak_message` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `CallLog`
--

DROP TABLE IF EXISTS `CallLog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `CallLog` (
  `id` bigint(14) unsigned NOT NULL AUTO_INCREMENT,
  `date_in` datetime NOT NULL,
  `call_id` varchar(40) NOT NULL,
  `origin_number` bigint(15) NOT NULL,
  `dialled_number` bigint(15) NOT NULL,
  `destination_number` bigint(15) DEFAULT NULL,
  `date_start` datetime NOT NULL,
  `date_end` datetime NOT NULL,
  `duration` int(5) NOT NULL DEFAULT '0',
  `bill_duration` int(5) NOT NULL DEFAULT '0',
  `bill_rate` decimal(8,5) NOT NULL DEFAULT '0.00000',
  `status` varchar(20) NOT NULL DEFAULT 'completed',
  `hangup_cause` varchar(100) DEFAULT NULL,
  `advert_id` int(9) unsigned NOT NULL,
  `adgroup_id` int(9) unsigned NOT NULL,
  `campaign_id` int(9) unsigned NOT NULL,
  `client_id` int(9) unsigned NOT NULL,
  `b_status` varchar(20) DEFAULT NULL,
  `b_hangup_cause` varchar(100) DEFAULT NULL,
  `audio_record` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Campaign`
--

DROP TABLE IF EXISTS `Campaign`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Campaign` (
  `id` int(9) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` int(9) NOT NULL,
  `name` varchar(50) NOT NULL,
  `status` smallint(2) unsigned NOT NULL DEFAULT '1',
  `date_create` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Client`
--

DROP TABLE IF EXISTS `Client`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Client` (
  `id` int(9) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(9) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `timezone` decimal(5,1) NOT NULL DEFAULT '8.0',
  `status` smallint(2) unsigned NOT NULL DEFAULT '1',
  `date_create` date NOT NULL,
  `call_count` int(9) unsigned NOT NULL DEFAULT '0',
  `duration` int(9) unsigned NOT NULL DEFAULT '0',
  `alert_enable` tinyint(1) NOT NULL DEFAULT '0',
  `alert_email` varchar(80) DEFAULT NULL,
  `alert_cid` int(9) unsigned DEFAULT NULL,
  `alert_adgid` int(9) unsigned DEFAULT NULL,
  `alert_adid` int(9) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Counter`
--

DROP TABLE IF EXISTS `Counter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Counter` (
  `date_in` datetime NOT NULL,
  `client_id` int(9) unsigned NOT NULL,
  `campaign_id` int(9) unsigned NOT NULL,
  `adgroup_id` int(9) unsigned NOT NULL,
  `advert_id` int(9) unsigned NOT NULL,
  `number_id` bigint(15) unsigned NOT NULL,
  `caller_id` varchar(20) NOT NULL,
  `count_total` bigint(12) unsigned NOT NULL DEFAULT '0',
  `count_plead` bigint(12) unsigned NOT NULL DEFAULT '0',
  `count_failed` bigint(12) unsigned NOT NULL DEFAULT '0',
  `duration_secs` bigint(15) unsigned NOT NULL,
  PRIMARY KEY (`date_in`,`client_id`,`campaign_id`,`adgroup_id`,`advert_id`,`number_id`,`caller_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8
/*!50100 PARTITION BY RANGE (TO_DAYS(date_in))
(PARTITION p201310 VALUES LESS THAN (735538) ENGINE = InnoDB,
 PARTITION p201311 VALUES LESS THAN (735568) ENGINE = InnoDB,
 PARTITION p201312 VALUES LESS THAN (735599) ENGINE = InnoDB,
 PARTITION p201401 VALUES LESS THAN (735630) ENGINE = InnoDB,
 PARTITION p201402 VALUES LESS THAN (735658) ENGINE = InnoDB,
 PARTITION p201403 VALUES LESS THAN (735689) ENGINE = InnoDB,
 PARTITION p201404 VALUES LESS THAN (735719) ENGINE = InnoDB,
 PARTITION p201405 VALUES LESS THAN (735750) ENGINE = InnoDB,
 PARTITION p201406 VALUES LESS THAN (735780) ENGINE = InnoDB,
 PARTITION p201407 VALUES LESS THAN (735811) ENGINE = InnoDB,
 PARTITION p201408 VALUES LESS THAN (735842) ENGINE = InnoDB,
 PARTITION p201409 VALUES LESS THAN (735872) ENGINE = InnoDB,
 PARTITION p201410 VALUES LESS THAN (735903) ENGINE = InnoDB,
 PARTITION p201411 VALUES LESS THAN (735933) ENGINE = InnoDB,
 PARTITION p201412 VALUES LESS THAN (735964) ENGINE = InnoDB,
 PARTITION p201501 VALUES LESS THAN (735995) ENGINE = InnoDB,
 PARTITION p201502 VALUES LESS THAN (736023) ENGINE = InnoDB,
 PARTITION p201503 VALUES LESS THAN (736054) ENGINE = InnoDB,
 PARTITION p201504 VALUES LESS THAN (736084) ENGINE = InnoDB,
 PARTITION p201505 VALUES LESS THAN (736115) ENGINE = InnoDB,
 PARTITION p201506 VALUES LESS THAN (736145) ENGINE = InnoDB,
 PARTITION p201507 VALUES LESS THAN (736176) ENGINE = InnoDB,
 PARTITION p201508 VALUES LESS THAN (736207) ENGINE = InnoDB,
 PARTITION p201509 VALUES LESS THAN (736237) ENGINE = InnoDB,
 PARTITION p201510 VALUES LESS THAN (736268) ENGINE = InnoDB,
 PARTITION p201511 VALUES LESS THAN (736298) ENGINE = InnoDB,
 PARTITION p201512 VALUES LESS THAN (736329) ENGINE = InnoDB,
 PARTITION p201601 VALUES LESS THAN (736360) ENGINE = InnoDB,
 PARTITION p201602 VALUES LESS THAN (736389) ENGINE = InnoDB,
 PARTITION p201603 VALUES LESS THAN (736420) ENGINE = InnoDB,
 PARTITION p201604 VALUES LESS THAN (736450) ENGINE = InnoDB,
 PARTITION p201605 VALUES LESS THAN (736481) ENGINE = InnoDB,
 PARTITION p201606 VALUES LESS THAN (736511) ENGINE = InnoDB,
 PARTITION p201607 VALUES LESS THAN (736542) ENGINE = InnoDB,
 PARTITION p201608 VALUES LESS THAN (736573) ENGINE = InnoDB,
 PARTITION p201609 VALUES LESS THAN (736603) ENGINE = InnoDB,
 PARTITION p201610 VALUES LESS THAN (736634) ENGINE = InnoDB,
 PARTITION p201611 VALUES LESS THAN (736664) ENGINE = InnoDB,
 PARTITION p201612 VALUES LESS THAN (736695) ENGINE = InnoDB,
 PARTITION p201701 VALUES LESS THAN (736726) ENGINE = InnoDB,
 PARTITION p201702 VALUES LESS THAN (736754) ENGINE = InnoDB,
 PARTITION p201703 VALUES LESS THAN (736785) ENGINE = InnoDB,
 PARTITION p201704 VALUES LESS THAN (736815) ENGINE = InnoDB,
 PARTITION p201705 VALUES LESS THAN (736846) ENGINE = InnoDB,
 PARTITION p201706 VALUES LESS THAN (736876) ENGINE = InnoDB,
 PARTITION p201707 VALUES LESS THAN (736907) ENGINE = InnoDB,
 PARTITION p201708 VALUES LESS THAN (736938) ENGINE = InnoDB,
 PARTITION p201709 VALUES LESS THAN (736968) ENGINE = InnoDB,
 PARTITION p201710 VALUES LESS THAN (736999) ENGINE = InnoDB,
 PARTITION p201711 VALUES LESS THAN (737029) ENGINE = InnoDB,
 PARTITION p201712 VALUES LESS THAN (737060) ENGINE = InnoDB,
 PARTITION p201801 VALUES LESS THAN (737091) ENGINE = InnoDB,
 PARTITION p201802 VALUES LESS THAN (737119) ENGINE = InnoDB,
 PARTITION p201803 VALUES LESS THAN (737150) ENGINE = InnoDB,
 PARTITION p201804 VALUES LESS THAN (737180) ENGINE = InnoDB,
 PARTITION p201805 VALUES LESS THAN (737211) ENGINE = InnoDB,
 PARTITION p201806 VALUES LESS THAN (737241) ENGINE = InnoDB,
 PARTITION p201807 VALUES LESS THAN (737272) ENGINE = InnoDB,
 PARTITION p201808 VALUES LESS THAN (737303) ENGINE = InnoDB,
 PARTITION p201809 VALUES LESS THAN (737333) ENGINE = InnoDB,
 PARTITION p201810 VALUES LESS THAN (737364) ENGINE = InnoDB,
 PARTITION p201811 VALUES LESS THAN (737394) ENGINE = InnoDB,
 PARTITION p201812 VALUES LESS THAN (737425) ENGINE = InnoDB,
 PARTITION pMAX VALUES LESS THAN MAXVALUE ENGINE = InnoDB) */;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Number`
--

DROP TABLE IF EXISTS `Number`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Number` (
  `id` bigint(15) unsigned NOT NULL,
  `type` int(2) NOT NULL,
  `provider` varchar(50) NOT NULL,
  `client_id` int(9) unsigned DEFAULT NULL,
  `price_buy` int(8) unsigned NOT NULL DEFAULT '0',
  `price_resale` int(8) unsigned NOT NULL DEFAULT '0',
  `date_create` date NOT NULL,
  `date_assign` date DEFAULT NULL,
  `date_lastcall` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User`
--

DROP TABLE IF EXISTS `User`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `User` (
  `id` int(9) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `username_canonical` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email_canonical` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `salt` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `last_login` datetime DEFAULT NULL,
  `locked` tinyint(1) NOT NULL,
  `expired` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  `confirmation_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password_requested_at` datetime DEFAULT NULL,
  `roles` longtext COLLATE utf8_unicode_ci NOT NULL COMMENT '(DC2Type:array)',
  `credentials_expired` tinyint(1) NOT NULL,
  `credentials_expire_at` datetime DEFAULT NULL,
  `multi_client` int(1) DEFAULT NULL,
  `name` varchar(80) COLLATE utf8_unicode_ci DEFAULT NULL,
  `business_name` varchar(80) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bill_business_name` varchar(80) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bill_name` varchar(80) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bill_email` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bill_phone` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bill_address` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_create` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_2DA1797792FC23A8` (`username_canonical`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `User` VALUES (1,'admin','admin','','',1,'88mx3wkmehwk08wwoo00kwg04wgk8cc','IcAuzWLxVy9m9Rn1BBL9TGn3t75mHaq2j9s/ASwQdMqJrcsu8koByMYlv9oygfjA6HR1uy0+9ODBfIqRoaCCUA==','2014-04-09 02:41:56',0,0,NULL,NULL,NULL,'a:1:{i:0;s:10:\"ROLE_ADMIN\";}',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);

/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-04-09  2:40:08
