-- MySQL dump 10.14  Distrib 5.5.32-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: oncall
-- ------------------------------------------------------
-- Server version	5.5.32-MariaDB

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
-- Table structure for table `Client`
--

DROP TABLE IF EXISTS `Client`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Client` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `timezone` decimal(5,1) NOT NULL DEFAULT '8.0',
  `status` smallint(2) unsigned NOT NULL DEFAULT '1',
  `date_create` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Client`
--

LOCK TABLES `Client` WRITE;
/*!40000 ALTER TABLE `Client` DISABLE KEYS */;
INSERT INTO `Client` VALUES (1,7,'Test',8.0,1,'2013-09-27'),(2,7,'Adidas Edit',-5.0,1,'2013-09-27'),(3,7,'Nike',8.0,1,'2013-09-27'),(4,7,'Lego',8.0,1,'2013-09-30'),(5,12,'New Single Client',8.0,1,'2013-09-30');
/*!40000 ALTER TABLE `Client` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Number`
--

DROP TABLE IF EXISTS `Number`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Number` (
  `id` int(15) unsigned NOT NULL,
  `type` int(2) NOT NULL,
  `provider` varchar(50) NOT NULL,
  `client_id` int(11) unsigned DEFAULT NULL,
  `price_buy` int(8) unsigned NOT NULL DEFAULT '0',
  `price_resale` int(8) unsigned NOT NULL DEFAULT '0',
  `date_create` date NOT NULL,
  `date_assign` date DEFAULT NULL,
  `date_lastcall` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Number`
--

LOCK TABLES `Number` WRITE;
/*!40000 ALTER TABLE `Number` DISABLE KEYS */;
INSERT INTO `Number` VALUES (30958340,3,'TestEdit',3,125,185,'2013-09-23',NULL,NULL),(234234234,1,'Test',2,150,320,'2013-09-22',NULL,NULL),(4294967295,2,'CiticTelecom',2,100,210,'2013-09-23',NULL,NULL);
/*!40000 ALTER TABLE `Number` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `User`
--

DROP TABLE IF EXISTS `User`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `User` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
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
  UNIQUE KEY `UNIQ_2DA1797792FC23A8` (`username_canonical`),
  UNIQUE KEY `UNIQ_2DA17977A0D96FBF` (`email_canonical`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `User`
--

LOCK TABLES `User` WRITE;
/*!40000 ALTER TABLE `User` DISABLE KEYS */;
INSERT INTO `User` VALUES (1,'admin','admin','kc@jankstudio.com','kc@jankstudio.com',1,'88mx3wkmehwk08wwoo00kwg04wgk8cc','IcAuzWLxVy9m9Rn1BBL9TGn3t75mHaq2j9s/ASwQdMqJrcsu8koByMYlv9oygfjA6HR1uy0+9ODBfIqRoaCCUA==','2013-09-30 21:44:13',0,0,NULL,NULL,NULL,'a:1:{i:0;s:10:\"ROLE_ADMIN\";}',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(7,'sample','sample','contact.guy@yahoo.com','contact.guy@yahoo.com',1,'4i19bumhhv28swww0gwss8cwkkk848c','3GIRXTHKroyV8grwhpIlC/kVVQkRQ3n8uzpthQrHGWCig0ZEzsOJNJkDs9nAzyReRvSMaXcicGZM4Qxu6XNbgQ==','2013-09-30 21:03:06',0,0,NULL,NULL,NULL,'a:0:{}',0,NULL,1,'Contact Guy','Sample Company','0923840209','Somewhere out there','Sample Company','Contact Guy','contact.guy@sample.company.com','0982349092','Somewhere out there part 2','2013-09-21 02:20:33'),(8,'multi','multi','multi@jankstudio.com','multi@jankstudio.com',1,'gyv7hgc6m6g4008cgsw8gsww8ggc840','tdwcSMvfmLo+AI3YxsK8M/PPw1w+toftMSsy/KRluhJx2eKlbU1PE1rDxDWYHgDtkZUITM+FlzPNSqhSs9xleQ==','2013-09-30 05:53:04',0,0,NULL,NULL,NULL,'a:0:{}',0,NULL,1,'Multi Test','Multi Test','230948','Somewhere','','','','','','2013-09-30 05:52:52'),(12,'new','new','new@jankstudio.com','new@jankstudio.com',1,'66ymmethpxgkokc8k0wg40k8w4s44kw','eeBd48cSiAmTgMNgj/9brWvCASFGTK/QbvaJAs6lPf4vpqpaX2HaYAHgQ64sCxhlMnQlLkE0uPSW+zqEgGPynA==',NULL,0,0,NULL,NULL,NULL,'a:0:{}',0,NULL,0,'New Single Client','New Single Client','29392834','Test','','','','','','2013-09-30 20:28:15');
/*!40000 ALTER TABLE `User` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2013-09-30 21:49:56
