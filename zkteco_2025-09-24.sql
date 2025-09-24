# ************************************************************
# Sequel Ace SQL dump
# Version 20094
#
# https://sequel-ace.com/
# https://github.com/Sequel-Ace/Sequel-Ace
#
# Host: 127.0.0.1 (MySQL 9.3.0)
# Database: zkteco
# Generation Time: 2025-09-24 15:30:45 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
SET NAMES utf8mb4;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE='NO_AUTO_VALUE_ON_ZERO', SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table attendances
# ------------------------------------------------------------

DROP TABLE IF EXISTS `attendances`;

CREATE TABLE `attendances` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `punch_code_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `device_ip` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `device_type` enum('IN','OUT') COLLATE utf8mb4_unicode_ci NOT NULL,
  `punch_time` datetime NOT NULL,
  `punch_type` enum('check_in','check_out','break_out','break_in') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `verify_mode` int DEFAULT NULL,
  `is_processed` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `attendances_employee_id_punch_time_index` (`punch_code_id`,`punch_time`),
  KEY `attendances_device_ip_punch_time_index` (`device_ip`,`punch_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `attendances` WRITE;
/*!40000 ALTER TABLE `attendances` DISABLE KEYS */;

INSERT INTO `attendances` (`id`, `punch_code_id`, `device_ip`, `device_type`, `punch_time`, `punch_type`, `verify_mode`, `is_processed`, `created_at`, `updated_at`)
VALUES
	(1,'144','172.16.10.14','IN','2025-09-24 00:06:48',NULL,0,0,'2025-09-24 10:52:59','2025-09-24 10:52:59'),
	(2,'144','172.16.10.14','IN','2025-09-24 00:16:26',NULL,0,0,'2025-09-24 10:52:59','2025-09-24 10:52:59'),
	(3,'436','172.16.10.14','IN','2025-09-24 00:20:46',NULL,0,0,'2025-09-24 10:52:59','2025-09-24 10:52:59'),
	(4,'144','172.16.10.14','IN','2025-09-24 01:08:38',NULL,0,0,'2025-09-24 10:52:59','2025-09-24 10:52:59'),
	(5,'225','172.16.10.14','IN','2025-09-24 02:09:51',NULL,0,0,'2025-09-24 10:52:59','2025-09-24 10:52:59'),
	(6,'433','172.16.10.14','IN','2025-09-24 02:20:05',NULL,0,0,'2025-09-24 10:52:59','2025-09-24 10:52:59'),
	(7,'436','172.16.10.14','IN','2025-09-24 02:49:35',NULL,0,0,'2025-09-24 10:52:59','2025-09-24 10:52:59'),
	(8,'144','172.16.10.14','IN','2025-09-24 02:52:41',NULL,0,0,'2025-09-24 10:52:59','2025-09-24 10:52:59'),
	(9,'144','172.16.10.14','IN','2025-09-24 04:19:23',NULL,0,0,'2025-09-24 10:52:59','2025-09-24 10:52:59'),
	(10,'144','172.16.10.14','IN','2025-09-24 04:42:31',NULL,0,0,'2025-09-24 10:52:59','2025-09-24 10:52:59'),
	(11,'47','172.16.10.14','IN','2025-09-24 04:42:34',NULL,0,0,'2025-09-24 10:52:59','2025-09-24 10:52:59'),
	(12,'168','172.16.10.14','IN','2025-09-24 08:26:06',NULL,0,0,'2025-09-24 10:52:59','2025-09-24 10:52:59'),
	(13,'3005','172.16.10.14','IN','2025-09-24 11:12:41',NULL,0,0,'2025-09-24 10:52:59','2025-09-24 10:52:59'),
	(14,'3005','172.16.10.14','IN','2025-09-24 11:13:09',NULL,0,0,'2025-09-24 10:52:59','2025-09-24 10:52:59'),
	(15,'3005','172.16.10.14','IN','2025-09-24 11:27:49',NULL,0,0,'2025-09-24 10:52:59','2025-09-24 10:52:59'),
	(16,'438','172.16.10.14','IN','2025-09-24 12:09:21',NULL,0,0,'2025-09-24 10:52:59','2025-09-24 10:52:59'),
	(17,'180','172.16.10.14','IN','2025-09-24 12:16:23',NULL,0,0,'2025-09-24 10:52:59','2025-09-24 10:52:59'),
	(18,'113','172.16.10.14','IN','2025-09-24 12:17:43',NULL,0,0,'2025-09-24 10:52:59','2025-09-24 10:52:59'),
	(19,'168','172.16.10.14','IN','2025-09-24 12:32:43',NULL,0,0,'2025-09-24 10:52:59','2025-09-24 10:52:59'),
	(20,'97','172.16.10.14','IN','2025-09-24 12:57:59',NULL,0,0,'2025-09-24 10:52:59','2025-09-24 10:52:59'),
	(21,'82','172.16.10.14','IN','2025-09-24 13:06:59',NULL,0,0,'2025-09-24 10:52:59','2025-09-24 10:52:59'),
	(22,'224','172.16.10.14','IN','2025-09-24 13:11:33',NULL,0,0,'2025-09-24 10:52:59','2025-09-24 10:52:59'),
	(23,'3005','172.16.10.14','IN','2025-09-24 13:12:05',NULL,0,0,'2025-09-24 10:52:59','2025-09-24 10:52:59'),
	(24,'174','172.16.10.14','IN','2025-09-24 13:21:05',NULL,0,0,'2025-09-24 10:52:59','2025-09-24 10:52:59'),
	(25,'3005','172.16.10.14','IN','2025-09-24 13:21:28',NULL,0,0,'2025-09-24 10:52:59','2025-09-24 10:52:59'),
	(26,'407','172.16.10.14','IN','2025-09-24 13:24:34',NULL,0,0,'2025-09-24 10:52:59','2025-09-24 10:52:59'),
	(27,'419','172.16.10.14','IN','2025-09-24 13:27:35',NULL,0,0,'2025-09-24 10:52:59','2025-09-24 10:52:59'),
	(28,'155','172.16.10.14','IN','2025-09-24 13:33:59',NULL,0,0,'2025-09-24 10:52:59','2025-09-24 10:52:59'),
	(29,'434','172.16.10.14','IN','2025-09-24 13:42:28',NULL,0,0,'2025-09-24 10:52:59','2025-09-24 10:52:59'),
	(30,'3005','172.16.10.14','IN','2025-09-24 13:55:32',NULL,0,0,'2025-09-24 10:52:59','2025-09-24 10:52:59'),
	(31,'430','172.16.10.14','IN','2025-09-24 13:58:08',NULL,0,0,'2025-09-24 10:52:59','2025-09-24 10:52:59'),
	(32,'427','172.16.10.14','IN','2025-09-24 13:58:10',NULL,0,0,'2025-09-24 10:52:59','2025-09-24 10:52:59'),
	(33,'430','172.16.10.14','IN','2025-09-24 14:21:41',NULL,0,0,'2025-09-24 10:52:59','2025-09-24 10:52:59'),
	(34,'7','172.16.10.14','IN','2025-09-24 14:30:43',NULL,0,0,'2025-09-24 10:52:59','2025-09-24 10:52:59'),
	(35,'3005','172.16.10.14','IN','2025-09-24 14:33:09',NULL,0,0,'2025-09-24 10:52:59','2025-09-24 10:52:59'),
	(36,'168','172.16.10.14','IN','2025-09-24 14:37:20',NULL,0,0,'2025-09-24 10:52:59','2025-09-24 10:52:59'),
	(37,'430','172.16.10.14','IN','2025-09-24 14:43:43',NULL,0,0,'2025-09-24 10:52:59','2025-09-24 10:52:59'),
	(38,'53','172.16.10.14','IN','2025-09-24 14:49:18',NULL,0,0,'2025-09-24 10:52:59','2025-09-24 10:52:59'),
	(39,'1','172.16.10.14','IN','2025-09-24 15:04:00',NULL,0,0,'2025-09-24 10:52:59','2025-09-24 10:52:59'),
	(40,'180','172.16.10.14','IN','2025-09-24 15:18:51',NULL,0,0,'2025-09-24 10:52:59','2025-09-24 10:52:59'),
	(41,'430','172.16.10.14','IN','2025-09-24 15:39:36',NULL,0,0,'2025-09-24 10:52:59','2025-09-24 10:52:59'),
	(42,'168','172.16.10.14','IN','2025-09-24 15:42:39',NULL,0,0,'2025-09-24 10:52:59','2025-09-24 10:52:59'),
	(43,'436','172.16.10.15','OUT','2025-09-24 00:15:50',NULL,0,0,'2025-09-24 10:54:04','2025-09-24 10:54:04'),
	(44,'144','172.16.10.15','OUT','2025-09-24 00:16:07',NULL,0,0,'2025-09-24 10:54:04','2025-09-24 10:54:04'),
	(45,'144','172.16.10.15','OUT','2025-09-24 01:08:07',NULL,0,0,'2025-09-24 10:54:04','2025-09-24 10:54:04'),
	(46,'225','172.16.10.15','OUT','2025-09-24 02:04:15',NULL,0,0,'2025-09-24 10:54:04','2025-09-24 10:54:04'),
	(47,'433','172.16.10.15','OUT','2025-09-24 02:13:23',NULL,0,0,'2025-09-24 10:54:04','2025-09-24 10:54:04'),
	(48,'225','172.16.10.15','OUT','2025-09-24 02:24:56',NULL,0,0,'2025-09-24 10:54:04','2025-09-24 10:54:04'),
	(49,'150','172.16.10.15','OUT','2025-09-24 02:26:08',NULL,0,0,'2025-09-24 10:54:04','2025-09-24 10:54:04'),
	(50,'144','172.16.10.15','OUT','2025-09-24 02:37:25',NULL,0,0,'2025-09-24 10:54:04','2025-09-24 10:54:04'),
	(51,'436','172.16.10.15','OUT','2025-09-24 02:39:56',NULL,0,0,'2025-09-24 10:54:04','2025-09-24 10:54:04'),
	(52,'433','172.16.10.15','OUT','2025-09-24 02:39:59',NULL,0,0,'2025-09-24 10:54:04','2025-09-24 10:54:04'),
	(53,'1','172.16.10.15','OUT','2025-09-24 02:40:22',NULL,0,0,'2025-09-24 10:54:04','2025-09-24 10:54:04'),
	(54,'436','172.16.10.15','OUT','2025-09-24 02:54:19',NULL,0,0,'2025-09-24 10:54:04','2025-09-24 10:54:04'),
	(55,'435','172.16.10.15','OUT','2025-09-24 02:54:24',NULL,0,0,'2025-09-24 10:54:04','2025-09-24 10:54:04'),
	(56,'107','172.16.10.15','OUT','2025-09-24 03:03:53',NULL,0,0,'2025-09-24 10:54:04','2025-09-24 10:54:04'),
	(57,'26','172.16.10.15','OUT','2025-09-24 03:12:08',NULL,0,0,'2025-09-24 10:54:04','2025-09-24 10:54:04'),
	(58,'144','172.16.10.15','OUT','2025-09-24 04:18:10',NULL,0,0,'2025-09-24 10:54:04','2025-09-24 10:54:04'),
	(59,'144','172.16.10.15','OUT','2025-09-24 04:41:30',NULL,0,0,'2025-09-24 10:54:04','2025-09-24 10:54:04'),
	(60,'47','172.16.10.15','OUT','2025-09-24 04:48:02',NULL,0,0,'2025-09-24 10:54:04','2025-09-24 10:54:04'),
	(61,'80','172.16.10.15','OUT','2025-09-24 06:30:24',NULL,0,0,'2025-09-24 10:54:04','2025-09-24 10:54:04'),
	(62,'221','172.16.10.15','OUT','2025-09-24 06:30:30',NULL,0,0,'2025-09-24 10:54:04','2025-09-24 10:54:04'),
	(63,'168','172.16.10.15','OUT','2025-09-24 07:15:54',NULL,0,0,'2025-09-24 10:54:04','2025-09-24 10:54:04'),
	(64,'3005','172.16.10.15','OUT','2025-09-24 11:27:13',NULL,0,0,'2025-09-24 10:54:04','2025-09-24 10:54:04'),
	(65,'3005','172.16.10.15','OUT','2025-09-24 12:58:04',NULL,0,0,'2025-09-24 10:54:04','2025-09-24 10:54:04'),
	(66,'168','172.16.10.15','OUT','2025-09-24 13:07:05',NULL,0,0,'2025-09-24 10:54:04','2025-09-24 10:54:04'),
	(67,'3005','172.16.10.15','OUT','2025-09-24 13:11:28',NULL,0,0,'2025-09-24 10:54:04','2025-09-24 10:54:04'),
	(68,'3005','172.16.10.15','OUT','2025-09-24 13:21:11',NULL,0,0,'2025-09-24 10:54:04','2025-09-24 10:54:04'),
	(69,'3005','172.16.10.15','OUT','2025-09-24 13:58:20',NULL,0,0,'2025-09-24 10:54:04','2025-09-24 10:54:04'),
	(70,'430','172.16.10.15','OUT','2025-09-24 14:19:51',NULL,0,0,'2025-09-24 10:54:04','2025-09-24 10:54:04'),
	(71,'3005','172.16.10.15','OUT','2025-09-24 14:30:53',NULL,0,0,'2025-09-24 10:54:04','2025-09-24 10:54:04'),
	(72,'430','172.16.10.15','OUT','2025-09-24 14:41:56',NULL,0,0,'2025-09-24 10:54:04','2025-09-24 10:54:04'),
	(73,'3005','172.16.10.15','OUT','2025-09-24 14:49:21',NULL,0,0,'2025-09-24 10:54:04','2025-09-24 10:54:04'),
	(74,'180','172.16.10.15','OUT','2025-09-24 15:14:12',NULL,0,0,'2025-09-24 10:54:04','2025-09-24 10:54:04'),
	(75,'3005','172.16.10.15','OUT','2025-09-24 15:23:47',NULL,0,0,'2025-09-24 10:54:04','2025-09-24 10:54:04'),
	(76,'430','172.16.10.15','OUT','2025-09-24 15:37:47',NULL,0,0,'2025-09-24 10:54:04','2025-09-24 10:54:04'),
	(77,'168','172.16.10.15','OUT','2025-09-24 15:42:10',NULL,0,0,'2025-09-24 10:54:04','2025-09-24 10:54:04'),
	(78,'168','172.16.10.14','IN','2025-09-24 15:58:09',NULL,0,0,'2025-09-24 12:13:19','2025-09-24 12:13:19'),
	(79,'3005','172.16.10.14','IN','2025-09-24 16:10:47',NULL,0,0,'2025-09-24 12:13:19','2025-09-24 12:13:19'),
	(80,'430','172.16.10.14','IN','2025-09-24 16:18:27',NULL,0,0,'2025-09-24 12:13:19','2025-09-24 12:13:19'),
	(81,'3005','172.16.10.14','IN','2025-09-24 16:53:23',NULL,0,0,'2025-09-24 12:13:19','2025-09-24 12:13:19'),
	(82,'150','172.16.10.14','IN','2025-09-24 16:53:30',NULL,0,0,'2025-09-24 12:13:19','2025-09-24 12:13:19'),
	(83,'430','172.16.10.14','IN','2025-09-24 16:54:05',NULL,0,0,'2025-09-24 12:13:19','2025-09-24 12:13:19'),
	(84,'135','172.16.10.14','IN','2025-09-24 17:00:20',NULL,0,0,'2025-09-24 12:13:19','2025-09-24 12:13:19'),
	(85,'168','172.16.10.15','OUT','2025-09-24 15:58:20',NULL,0,0,'2025-09-24 12:13:19','2025-09-24 12:13:19'),
	(86,'430','172.16.10.15','OUT','2025-09-24 16:16:55',NULL,0,0,'2025-09-24 12:13:19','2025-09-24 12:13:19'),
	(87,'430','172.16.10.15','OUT','2025-09-24 16:48:44',NULL,0,0,'2025-09-24 12:13:19','2025-09-24 12:13:19'),
	(88,'419','172.16.10.15','OUT','2025-09-24 16:50:34',NULL,0,0,'2025-09-24 12:13:19','2025-09-24 12:13:19'),
	(89,'3005','172.16.10.15','OUT','2025-09-24 16:53:40',NULL,0,0,'2025-09-24 12:13:19','2025-09-24 12:13:19'),
	(90,'168','172.16.10.15','OUT','2025-09-24 16:54:15',NULL,0,0,'2025-09-24 12:13:19','2025-09-24 12:13:19'),
	(91,'3005','172.16.10.15','OUT','2025-09-24 17:00:28',NULL,0,0,'2025-09-24 12:13:20','2025-09-24 12:13:20'),
	(92,'174','172.16.10.15','OUT','2025-09-24 17:16:05',NULL,0,0,'2025-09-24 12:20:38','2025-09-24 12:20:38'),
	(93,'225','172.16.10.14','IN','2025-09-24 17:16:48',NULL,0,0,'2025-09-24 12:26:36','2025-09-24 12:26:36'),
	(94,'107','172.16.10.14','IN','2025-09-24 17:17:25',NULL,0,0,'2025-09-24 12:26:36','2025-09-24 12:26:36'),
	(95,'225','172.16.10.14','IN','2025-09-24 17:18:05',NULL,0,0,'2025-09-24 12:26:36','2025-09-24 12:26:36'),
	(96,'174','172.16.10.14','IN','2025-09-24 17:19:44',NULL,0,0,'2025-09-24 12:26:36','2025-09-24 12:26:36'),
	(97,'168','172.16.10.14','IN','2025-09-24 17:22:06',NULL,0,0,'2025-09-24 12:26:36','2025-09-24 12:26:36'),
	(98,'3005','172.16.10.14','IN','2025-09-24 17:36:48',NULL,0,0,'2025-09-24 13:31:50','2025-09-24 13:31:50'),
	(99,'430','172.16.10.14','IN','2025-09-24 17:56:22',NULL,0,0,'2025-09-24 13:31:50','2025-09-24 13:31:50'),
	(100,'433','172.16.10.14','IN','2025-09-24 17:58:28',NULL,0,0,'2025-09-24 13:31:50','2025-09-24 13:31:50'),
	(101,'144','172.16.10.14','IN','2025-09-24 17:59:05',NULL,0,0,'2025-09-24 13:31:50','2025-09-24 13:31:50'),
	(102,'436','172.16.10.14','IN','2025-09-24 18:17:45',NULL,0,0,'2025-09-24 13:31:50','2025-09-24 13:31:50'),
	(103,'435','172.16.10.14','IN','2025-09-24 18:17:50',NULL,0,0,'2025-09-24 13:31:50','2025-09-24 13:31:50'),
	(104,'435','172.16.10.14','IN','2025-09-24 18:17:52',NULL,0,0,'2025-09-24 13:31:50','2025-09-24 13:31:50'),
	(105,'3005','172.16.10.14','IN','2025-09-24 18:18:59',NULL,0,0,'2025-09-24 13:31:50','2025-09-24 13:31:50'),
	(106,'82','172.16.10.14','IN','2025-09-24 18:27:15',NULL,0,0,'2025-09-24 13:31:50','2025-09-24 13:31:50'),
	(107,'144','172.16.10.15','OUT','2025-09-24 17:27:44',NULL,0,0,'2025-09-24 13:31:51','2025-09-24 13:31:51'),
	(108,'3005','172.16.10.15','OUT','2025-09-24 17:36:33',NULL,0,0,'2025-09-24 13:31:51','2025-09-24 13:31:51'),
	(109,'144','172.16.10.15','OUT','2025-09-24 17:50:31',NULL,0,0,'2025-09-24 13:31:51','2025-09-24 13:31:51'),
	(110,'168','172.16.10.15','OUT','2025-09-24 17:53:15',NULL,0,0,'2025-09-24 13:31:51','2025-09-24 13:31:51'),
	(111,'430','172.16.10.15','OUT','2025-09-24 17:54:37',NULL,0,0,'2025-09-24 13:31:51','2025-09-24 13:31:51'),
	(112,'3005','172.16.10.15','OUT','2025-09-24 17:56:43',NULL,0,0,'2025-09-24 13:31:51','2025-09-24 13:31:51'),
	(113,'3005','172.16.10.15','OUT','2025-09-24 17:57:37',NULL,0,0,'2025-09-24 13:31:51','2025-09-24 13:31:51'),
	(114,'82','172.16.10.15','OUT','2025-09-24 18:22:00',NULL,0,0,'2025-09-24 13:31:51','2025-09-24 13:31:51'),
	(115,'47','172.16.10.14','IN','2025-09-24 18:39:45',NULL,0,0,'2025-09-24 14:12:27','2025-09-24 14:12:27'),
	(116,'26','172.16.10.14','IN','2025-09-24 18:43:33',NULL,0,0,'2025-09-24 14:12:27','2025-09-24 14:12:27'),
	(117,'430','172.16.10.14','IN','2025-09-24 18:47:56',NULL,0,0,'2025-09-24 14:12:27','2025-09-24 14:12:27'),
	(118,'82','172.16.10.14','IN','2025-09-24 18:52:38',NULL,0,0,'2025-09-24 14:12:27','2025-09-24 14:12:27'),
	(119,'82','172.16.10.15','OUT','2025-09-24 18:37:14',NULL,0,0,'2025-09-24 14:12:27','2025-09-24 14:12:27'),
	(120,'430','172.16.10.15','OUT','2025-09-24 18:42:32',NULL,0,0,'2025-09-24 14:12:27','2025-09-24 14:12:27'),
	(121,'407','172.16.10.15','OUT','2025-09-24 18:55:42',NULL,0,0,'2025-09-24 14:12:27','2025-09-24 14:12:27'),
	(122,'407','172.16.10.14','IN','2025-09-24 19:12:02',NULL,0,0,'2025-09-24 14:17:06','2025-09-24 14:17:06');

/*!40000 ALTER TABLE `attendances` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table cache
# ------------------------------------------------------------

DROP TABLE IF EXISTS `cache`;

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table cache_locks
# ------------------------------------------------------------

DROP TABLE IF EXISTS `cache_locks`;

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table employees
# ------------------------------------------------------------

DROP TABLE IF EXISTS `employees`;

CREATE TABLE `employees` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `punch_code_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `department` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `position` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `employees_employee_id_unique` (`punch_code_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `employees` WRITE;
/*!40000 ALTER TABLE `employees` DISABLE KEYS */;

INSERT INTO `employees` (`id`, `punch_code_id`, `name`, `email`, `department`, `position`, `is_active`, `created_at`, `updated_at`)
VALUES
	(1,'164','Hareem Fatima',NULL,NULL,NULL,1,'2025-09-24 11:04:37','2025-09-24 11:04:37'),
	(2,'174','Samad Khan',NULL,NULL,NULL,1,'2025-09-24 11:04:37','2025-09-24 11:04:37'),
	(3,'180','Asif Ahmed',NULL,NULL,NULL,1,'2025-09-24 11:04:37','2025-09-24 11:04:37'),
	(4,'1','Wahaj',NULL,NULL,NULL,1,'2025-09-24 11:04:37','2025-09-24 11:04:37'),
	(5,'7','Ebad Qureshi',NULL,NULL,NULL,1,'2025-09-24 11:04:37','2025-09-24 11:04:37'),
	(6,'17','Umer Shahjahan',NULL,NULL,NULL,1,'2025-09-24 11:04:37','2025-09-24 11:04:37'),
	(7,'26','Azmeer Sheikh',NULL,NULL,NULL,1,'2025-09-24 11:04:37','2025-09-24 11:04:37'),
	(8,'47','Wasif Azhar',NULL,NULL,NULL,1,'2025-09-24 11:04:37','2025-09-24 11:04:37'),
	(9,'53','Muhammad Mubashir',NULL,NULL,NULL,1,'2025-09-24 11:04:37','2025-09-24 11:04:37'),
	(10,'70','Khawaja Muhammad Ali',NULL,NULL,NULL,1,'2025-09-24 11:04:37','2025-09-24 11:04:37'),
	(11,'78','Farooq Khan %',NULL,NULL,NULL,1,'2025-09-24 11:04:37','2025-09-24 11:04:37'),
	(12,'80','Muhammad Ibtehaj Ali ',NULL,NULL,NULL,1,'2025-09-24 11:04:37','2025-09-24 11:04:37'),
	(13,'82','Fatima Hassan',NULL,NULL,NULL,1,'2025-09-24 11:04:37','2025-09-24 11:04:37'),
	(14,'113','Affan Khan',NULL,NULL,NULL,1,'2025-09-24 11:04:37','2025-09-24 11:04:37'),
	(15,'147','Shahbaz Khan',NULL,NULL,NULL,1,'2025-09-24 11:04:37','2025-09-24 11:04:37'),
	(16,'150','Humayoun Khan',NULL,NULL,NULL,1,'2025-09-24 11:04:37','2025-09-24 11:04:37'),
	(17,'155','Syed Sumair Hussain',NULL,NULL,NULL,1,'2025-09-24 11:04:37','2025-09-24 11:04:37'),
	(18,'999','A',NULL,NULL,NULL,1,'2025-09-24 11:04:38','2025-09-24 11:04:38'),
	(19,'998','B',NULL,NULL,NULL,1,'2025-09-24 11:04:38','2025-09-24 11:04:38'),
	(20,'168','Amanullah',NULL,NULL,NULL,1,'2025-09-24 11:04:38','2025-09-24 11:04:38'),
	(21,'22','Shahmeer Sheikh',NULL,NULL,NULL,1,'2025-09-24 11:04:38','2025-09-24 11:04:38'),
	(22,'97','Wania Qureshi',NULL,NULL,NULL,1,'2025-09-24 11:04:38','2025-09-24 11:04:38'),
	(23,'104','Farooq co',NULL,NULL,NULL,1,'2025-09-24 11:04:38','2025-09-24 11:04:38'),
	(24,'107','Hasnain Ali Farooqi',NULL,NULL,NULL,1,'2025-09-24 11:04:38','2025-09-24 11:04:38'),
	(25,'128','Omer Electric',NULL,NULL,NULL,1,'2025-09-24 11:04:38','2025-09-24 11:04:38'),
	(26,'135','Farhad M. Khan',NULL,NULL,NULL,1,'2025-09-24 11:04:38','2025-09-24 11:04:38'),
	(27,'144','Salman',NULL,NULL,NULL,1,'2025-09-24 11:04:38','2025-09-24 11:04:38'),
	(28,'149','M. Yousuf',NULL,NULL,NULL,1,'2025-09-24 11:04:38','2025-09-24 11:04:38'),
	(29,'221','Abdul Sami Kamali',NULL,NULL,NULL,1,'2025-09-24 11:04:38','2025-09-24 11:04:38'),
	(30,'225','Aun Zaidi',NULL,NULL,NULL,1,'2025-09-24 11:04:38','2025-09-24 11:04:38'),
	(31,'224','Taha Ahmed',NULL,NULL,NULL,1,'2025-09-24 11:04:38','2025-09-24 11:04:38'),
	(32,'3005','Junaid OfficeB',NULL,NULL,NULL,1,'2025-09-24 11:04:38','2025-09-24 11:04:38'),
	(33,'407','Areeb',NULL,NULL,NULL,1,'2025-09-24 11:04:38','2025-09-24 11:04:38'),
	(34,'419','Khushboo',NULL,NULL,NULL,1,'2025-09-24 11:04:38','2025-09-24 11:04:38'),
	(35,'213','Salman',NULL,NULL,NULL,1,'2025-09-24 11:04:38','2025-09-24 11:04:38'),
	(36,'8','Khawar',NULL,NULL,NULL,1,'2025-09-24 11:04:38','2025-09-24 11:04:38'),
	(37,'427','Shaheer',NULL,NULL,NULL,1,'2025-09-24 11:04:38','2025-09-24 11:04:38'),
	(38,'428','Abdulqadeer',NULL,NULL,NULL,1,'2025-09-24 11:04:38','2025-09-24 11:04:38'),
	(39,'429','Muzammil',NULL,NULL,NULL,1,'2025-09-24 11:04:38','2025-09-24 11:04:38'),
	(40,'430','Hammad',NULL,NULL,NULL,1,'2025-09-24 11:04:38','2025-09-24 11:04:38'),
	(41,'431','Yousif',NULL,NULL,NULL,1,'2025-09-24 11:04:38','2025-09-24 11:04:38'),
	(42,'432','Aliafzal',NULL,NULL,NULL,1,'2025-09-24 11:04:38','2025-09-24 11:04:38'),
	(43,'433','Fahad',NULL,NULL,NULL,1,'2025-09-24 11:04:38','2025-09-24 11:04:38'),
	(44,'2','Zara',NULL,NULL,NULL,1,'2025-09-24 11:04:38','2025-09-24 11:04:38'),
	(45,'434','Ahmed',NULL,NULL,NULL,1,'2025-09-24 11:04:38','2025-09-24 11:04:38'),
	(46,'3','Nasir',NULL,NULL,NULL,1,'2025-09-24 11:04:38','2025-09-24 11:04:38'),
	(47,'435','Bilal',NULL,NULL,NULL,1,'2025-09-24 11:04:38','2025-09-24 11:04:38'),
	(48,'2222','Zafar',NULL,NULL,NULL,1,'2025-09-24 11:04:38','2025-09-24 11:04:38'),
	(49,'11111','In',NULL,NULL,NULL,1,'2025-09-24 11:04:38','2025-09-24 11:04:38'),
	(50,'436','Ehteshan',NULL,NULL,NULL,1,'2025-09-24 11:04:38','2025-09-24 11:04:38'),
	(51,'437','Ayesha',NULL,NULL,NULL,1,'2025-09-24 11:04:38','2025-09-24 11:04:38'),
	(52,'438','Bisma',NULL,NULL,NULL,1,'2025-09-24 11:04:38','2025-09-24 11:04:38'),
	(53,'18','Umair Kaim Khani',NULL,NULL,NULL,1,'2025-09-24 11:04:38','2025-09-24 11:04:38'),
	(54,'159','Muhammad Junaid Khan',NULL,NULL,NULL,1,'2025-09-24 11:04:38','2025-09-24 11:04:38'),
	(55,'186','Mubasihr',NULL,NULL,NULL,1,'2025-09-24 11:04:38','2025-09-24 11:04:38'),
	(56,'215','Salman',NULL,NULL,NULL,1,'2025-09-24 11:04:38','2025-09-24 11:04:38'),
	(57,'22222','Zafar',NULL,NULL,NULL,1,'2025-09-24 11:04:38','2025-09-24 11:04:38');

/*!40000 ALTER TABLE `employees` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table failed_jobs
# ------------------------------------------------------------

DROP TABLE IF EXISTS `failed_jobs`;

CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table job_batches
# ------------------------------------------------------------

DROP TABLE IF EXISTS `job_batches`;

CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table jobs
# ------------------------------------------------------------

DROP TABLE IF EXISTS `jobs`;

CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table migrations
# ------------------------------------------------------------

DROP TABLE IF EXISTS `migrations`;

CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;

INSERT INTO `migrations` (`id`, `migration`, `batch`)
VALUES
	(1,'0001_01_01_000000_create_users_table',1),
	(2,'0001_01_01_000001_create_cache_table',1),
	(3,'0001_01_01_000002_create_jobs_table',1),
	(4,'2025_09_19_145809_create_employees_table',2),
	(5,'2025_09_19_145821_create_attendances_table',2),
	(6,'2025_09_24_103854_update_employees_table_use_punch_code_id',3);

/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table password_reset_tokens
# ------------------------------------------------------------

DROP TABLE IF EXISTS `password_reset_tokens`;

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table sessions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `sessions`;

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`)
VALUES
	('HHbVS2bU2JJZwCT34BcgNtK2ZKUMSzbSqEQ8V5Sc',NULL,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiQVpOa2I5UDR1a25oeHBvanZNOVBpNzBNWUt6a092N1lTZ1lxUWZBcCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzI6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwNC9hdHRlbmRhbmNlIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1758716798),
	('xSpZMnRIRUhw5CQNvOLV6JVWr2tmbg0RtUqrdwhw',NULL,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoicnV3aVVmbmN6OFFoVGExTDlzSGFXbzNVdElSOEJkanlmbVNxSWpkcCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzI6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwNC9hdHRlbmRhbmNlIjt9fQ==',1758724844);

/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
