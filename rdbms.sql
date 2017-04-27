-- MySQL dump 10.15  Distrib 10.0.30-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: test.poland.com
-- ------------------------------------------------------
-- Server version	10.0.30-MariaDB-0+deb8u1

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
-- Table structure for table `delivery`
--

DROP TABLE IF EXISTS `delivery`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `delivery` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` tinytext NOT NULL,
  `price` decimal(5,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `delivery`
--

LOCK TABLES `delivery` WRITE;
/*!40000 ALTER TABLE `delivery` DISABLE KEYS */;
INSERT INTO `delivery` VALUES (1,'Pick up',0.00),(2,'UPS',5.00);
/*!40000 ALTER TABLE `delivery` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order`
--

DROP TABLE IF EXISTS `order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` tinyint(3) unsigned NOT NULL,
  `delivery_id` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `delivery_id` (`delivery_id`),
  CONSTRAINT `order_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order`
--

LOCK TABLES `order` WRITE;
/*!40000 ALTER TABLE `order` DISABLE KEYS */;
INSERT INTO `order` VALUES (5,57,1),(11,57,2),(12,57,2),(13,57,1),(14,57,1),(15,57,2),(16,57,2),(17,58,2),(18,58,1),(19,58,2),(20,59,2),(21,59,2),(22,60,2),(24,61,2),(25,62,2),(26,62,1),(27,62,1),(28,65,2);
/*!40000 ALTER TABLE `order` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_product`
--

DROP TABLE IF EXISTS `order_product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_product` (
  `order_id` tinyint(3) unsigned NOT NULL,
  `product_id` tinyint(3) unsigned NOT NULL,
  `quantity` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`order_id`,`product_id`),
  KEY `order_id` (`order_id`),
  CONSTRAINT `order_product_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `order` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_product`
--

LOCK TABLES `order_product` WRITE;
/*!40000 ALTER TABLE `order_product` DISABLE KEYS */;
INSERT INTO `order_product` VALUES (5,1,3),(5,2,8),(5,4,1),(11,1,3),(11,2,8),(11,4,1),(12,1,3),(12,2,8),(12,4,1),(13,1,3),(13,2,8),(13,4,1),(14,1,3),(14,2,8),(14,4,1),(15,1,3),(15,2,8),(15,4,1),(16,1,3),(16,2,8),(16,4,1),(17,1,1),(17,2,2),(17,3,1),(18,1,1),(18,2,2),(18,3,1),(19,1,1),(19,2,2),(19,3,1),(20,1,2),(21,2,2),(22,2,2),(24,1,1),(25,1,1),(25,2,1),(26,3,2),(27,2,3),(28,1,9),(28,2,13),(28,3,2);
/*!40000 ALTER TABLE `order_product` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product`
--

DROP TABLE IF EXISTS `product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` tinytext NOT NULL,
  `price` decimal(5,2) NOT NULL DEFAULT '0.00',
  `image` char(32) NOT NULL,
  `rating` tinyint(4) NOT NULL DEFAULT '0',
  `vote_count` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product`
--

LOCK TABLES `product` WRITE;
/*!40000 ALTER TABLE `product` DISABLE KEYS */;
INSERT INTO `product` VALUES (1,'Apple',0.30,'b22bf3780db7ffb854f11b29edd691b3',42,12),(2,'Beer',2.00,'8930bfaa5ecfa109ae1f722b888e9915',10,3),(3,'Water',1.00,'a03e30a79241fec906a56e27a2752b90',12,3),(4,'Cheese',3.74,'fea0f1f6fede90bd0a925b4194deac11',9,3);
/*!40000 ALTER TABLE `product` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_rating`
--

DROP TABLE IF EXISTS `product_rating`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_rating` (
  `product_id` tinyint(3) unsigned NOT NULL,
  `user_id` tinyint(3) unsigned NOT NULL,
  `mark` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`product_id`,`user_id`),
  KEY `product_id` (`product_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `product_rating_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`),
  CONSTRAINT `product_rating_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_rating`
--

LOCK TABLES `product_rating` WRITE;
/*!40000 ALTER TABLE `product_rating` DISABLE KEYS */;
INSERT INTO `product_rating` VALUES (1,68,4),(1,69,4),(1,70,4),(1,71,5),(1,72,4),(1,73,1),(1,74,2),(1,75,5),(1,76,3),(1,79,4),(1,80,5),(1,81,1),(2,79,2),(2,80,3),(2,81,5),(3,79,5),(3,80,5),(3,81,2),(4,79,3),(4,80,3),(4,81,3);
/*!40000 ALTER TABLE `product_rating` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` tinytext NOT NULL,
  `balance` decimal(5,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=82 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'Customer #1493212105',100.00),(2,'Customer #1493215626',100.00),(3,'Customer #1493215714',100.00),(4,'Customer #1493215952',100.00),(5,'Customer #1493215965',100.00),(6,'Customer #1493216094',100.00),(7,'Customer #1493216166',100.00),(8,'Customer #1493216216',100.00),(9,'Customer #1493216264',100.00),(10,'Customer #1493216312',100.00),(11,'Customer #1493216388',100.00),(12,'Customer #1493216393',100.00),(13,'Customer #1493226447',100.00),(14,'Customer #1493226455',100.00),(15,'Customer #1493237207',100.00),(16,'Customer #1493237213',100.00),(17,'Customer #1493237232',100.00),(18,'Customer #1493237289',100.00),(19,'Customer #1493237356',100.00),(20,'Customer #1493237485',100.00),(21,'Customer #1493237491',100.00),(22,'Customer #1493237501',100.00),(23,'Customer #1493237546',100.00),(24,'Customer #1493237570',100.00),(25,'Customer #1493237618',100.00),(26,'Customer #1493237650',100.00),(27,'Customer #1493285385',100.00),(28,'Customer #1493285430',100.00),(29,'Customer #1493285470',100.00),(30,'Customer #1493285472',100.00),(31,'Customer #1493285524',100.00),(32,'Customer #1493286435',100.00),(33,'Customer #1493286457',100.00),(34,'Customer #1493287306',100.00),(35,'Customer #1493291234',100.00),(36,'Customer #1493291258',100.00),(37,'Customer #1493291278',100.00),(38,'Customer #1493291286',100.00),(39,'Customer #1493291343',100.00),(40,'Customer #1493291346',100.00),(41,'Customer #1493291412',100.00),(42,'Customer #1493291463',100.00),(43,'Customer #1493291468',100.00),(44,'Customer #1493291473',100.00),(45,'Customer #1493291543',100.00),(46,'Customer #1493291583',100.00),(47,'Customer #1493291658',100.00),(48,'Customer #1493291826',100.00),(49,'Customer #1493291829',100.00),(50,'Customer #1493291831',100.00),(51,'Customer #1493291834',100.00),(52,'Customer #1493292051',100.00),(53,'Customer #1493292054',100.00),(54,'Customer #1493292935',100.00),(55,'Customer #1493293319',100.00),(56,'Customer #1493295846',100.00),(57,'Dima',952.76),(58,'Customer #1493302289',80.10),(59,'Customer #1493302444',87.70),(60,'Customer #1493302533',93.00),(61,'Customer #1493302590',94.70),(62,'Customer #1493302669',85.70),(63,'Customer #1493304095',100.00),(64,'Customer #1493304113',100.00),(65,'Customer #1493304439',64.30),(66,'Customer #1493310654',100.00),(67,'Customer #1493310746',100.00),(68,'Customer #1493311430',100.00),(69,'Customer #1493311653',100.00),(70,'Customer #1493311689',100.00),(71,'Customer #1493311706',100.00),(72,'Customer #1493311726',100.00),(73,'Customer #1493311831',100.00),(74,'Customer #1493312034',100.00),(75,'Customer #1493312079',100.00),(76,'Customer #1493312087',100.00),(77,'Customer #1493313247',100.00),(78,'Customer #1493313295',100.00),(79,'Customer #1493313373',100.00),(80,'Customer #1493314166',100.00),(81,'Customer #1493314707',10.00);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-04-27 21:32:15
