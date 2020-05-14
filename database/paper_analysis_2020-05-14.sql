# ************************************************************
# Sequel Pro SQL dump
# Version 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: 127.0.0.1 (MySQL 5.7.25)
# Database: paper_analysis
# Generation Time: 2020-05-14 02:22:13 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table admin_menu
# ------------------------------------------------------------

DROP TABLE IF EXISTS `admin_menu`;

CREATE TABLE `admin_menu` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `order` int(11) NOT NULL DEFAULT '0',
  `title` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `uri` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `permission` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `admin_menu` WRITE;
/*!40000 ALTER TABLE `admin_menu` DISABLE KEYS */;

INSERT INTO `admin_menu` (`id`, `parent_id`, `order`, `title`, `icon`, `uri`, `permission`, `created_at`, `updated_at`)
VALUES
	(1,0,1,'首页','fa-institution','/','dashboard',NULL,'2020-04-29 11:36:51'),
	(2,0,2,'系统管理','fa-wrench',NULL,'auth.management',NULL,'2020-04-29 11:36:42'),
	(3,2,3,'用户','fa-users','auth/users','auth.management',NULL,'2020-03-25 02:16:13'),
	(4,2,4,'角色','fa-user','auth/roles','auth.management',NULL,'2020-03-25 02:16:18'),
	(5,2,5,'权限','fa-ban','auth/permissions','auth.management',NULL,'2020-03-25 02:16:24'),
	(6,2,6,'菜单','fa-bars','auth/menu','auth.management',NULL,'2020-03-25 02:16:29'),
	(7,2,8,'操作记录','fa-history','auth/logs','auth.management',NULL,'2020-03-25 02:16:42'),
	(13,12,12,'选择题管理','fa-inbox','question/selection','question.management','2020-02-08 01:28:27','2020-04-22 06:31:46'),
	(14,12,13,'判断题管理','fa-inbox','question/judgement','question.management','2020-02-08 08:48:27','2020-04-22 06:31:37'),
	(15,12,14,'主观题管理','fa-inbox','question/subjective','question.management','2020-02-08 08:48:46','2020-04-22 06:31:51'),
	(16,2,7,'反馈','fa-envelope','auth/feedback','auth.management','2020-02-22 07:28:03','2020-02-25 03:36:29'),
	(17,0,18,'我的反馈','fa-envelope-o','feedback','feedback','2020-02-22 07:32:55','2020-04-28 17:56:52'),
	(18,0,17,'我的试卷','fa-tasks','student/paper','my.paper','2020-02-26 03:51:42','2020-04-28 17:56:52'),
	(19,11,17,'主观题答案','fa-list','answer/subjective','answer.management','2020-03-30 02:50:25','2020-04-22 06:32:02'),
	(20,11,16,'学生答案','fa-list','answer/','answer.management','2020-03-30 02:51:09','2020-04-22 06:31:57'),
	(21,0,9,'试卷管理','fa-file-text-o','paper','paper.management','2020-04-28 17:51:52','2020-04-28 17:52:00'),
	(22,0,10,'题目管理','fa-tag','question','question.management','2020-04-28 17:53:08','2020-04-29 11:37:08'),
	(23,0,14,'学生答案管理','fa-inbox','answer','answer.management','2020-04-28 17:53:55','2020-04-29 11:37:16'),
	(24,22,11,'选择题管理','fa-bars','question/selection','question.management','2020-04-28 17:54:45','2020-04-29 11:37:26'),
	(25,22,12,'判断题管理','fa-bars','question/judgement','question.management','2020-04-28 17:55:12','2020-04-29 11:37:32'),
	(26,22,13,'主观题管理','fa-bars','question/subjective','question.management','2020-04-28 17:55:37','2020-04-29 11:37:37'),
	(27,23,15,'学生答案','fa-bars','answer','answer.management','2020-04-28 17:56:23','2020-04-29 11:37:43'),
	(28,23,16,'学生主观题答案','fa-bars','answer/subjective','answer.management','2020-04-28 17:56:47','2020-04-29 11:37:48');

/*!40000 ALTER TABLE `admin_menu` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table admin_permissions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `admin_permissions`;

CREATE TABLE `admin_permissions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `http_method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `http_path` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `admin_permissions_name_unique` (`name`),
  UNIQUE KEY `admin_permissions_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `admin_permissions` WRITE;
/*!40000 ALTER TABLE `admin_permissions` DISABLE KEYS */;

INSERT INTO `admin_permissions` (`id`, `name`, `slug`, `http_method`, `http_path`, `created_at`, `updated_at`)
VALUES
	(1,'All permission','*','','*\r\n/api/*',NULL,'2020-02-25 03:18:49'),
	(2,'Dashboard','dashboard','GET','*',NULL,'2020-04-23 01:26:05'),
	(3,'Login','auth.login','','/auth/login\r\n/auth/logout',NULL,NULL),
	(4,'User setting','auth.setting','GET,PUT','/auth/setting',NULL,NULL),
	(5,'Auth management','auth.management','','/auth/users*\r\n/auth/roles*\r\n/auth/permissions*\r\n/auth/menu*\r\n/auth/logs*\r\n/auth/feedback*',NULL,'2020-03-25 02:47:38'),
	(8,'Paper management','paper.management','','/paper*','2020-03-25 02:07:21','2020-04-22 06:30:23'),
	(9,'My paper','my.paper','','/student/paper*','2020-03-25 02:08:34','2020-04-22 06:30:32'),
	(10,'Feedback','feedback','','/feedback*','2020-03-25 02:08:59','2020-03-25 02:43:44'),
	(12,'Question Management','question.management','','/question*','2020-04-22 06:29:55','2020-04-22 06:29:55'),
	(13,'Answer Management','answer.management','','/answer*','2020-04-22 06:30:16','2020-04-22 06:30:16');

/*!40000 ALTER TABLE `admin_permissions` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table admin_role_menu
# ------------------------------------------------------------

DROP TABLE IF EXISTS `admin_role_menu`;

CREATE TABLE `admin_role_menu` (
  `role_id` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  KEY `admin_role_menu_role_id_menu_id_index` (`role_id`,`menu_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `admin_role_menu` WRITE;
/*!40000 ALTER TABLE `admin_role_menu` DISABLE KEYS */;

INSERT INTO `admin_role_menu` (`role_id`, `menu_id`, `created_at`, `updated_at`)
VALUES
	(3,10,NULL,NULL),
	(3,11,NULL,NULL),
	(1,10,NULL,NULL),
	(1,11,NULL,NULL),
	(1,12,NULL,NULL),
	(3,12,NULL,NULL),
	(1,14,NULL,NULL),
	(3,14,NULL,NULL),
	(1,15,NULL,NULL),
	(3,15,NULL,NULL),
	(1,13,NULL,NULL),
	(3,13,NULL,NULL),
	(1,19,NULL,NULL),
	(3,19,NULL,NULL),
	(1,20,NULL,NULL),
	(3,20,NULL,NULL);

/*!40000 ALTER TABLE `admin_role_menu` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table admin_role_permissions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `admin_role_permissions`;

CREATE TABLE `admin_role_permissions` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  KEY `admin_role_permissions_role_id_permission_id_index` (`role_id`,`permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `admin_role_permissions` WRITE;
/*!40000 ALTER TABLE `admin_role_permissions` DISABLE KEYS */;

INSERT INTO `admin_role_permissions` (`role_id`, `permission_id`, `created_at`, `updated_at`)
VALUES
	(2,2,NULL,NULL),
	(2,3,NULL,NULL),
	(3,2,NULL,NULL),
	(3,3,NULL,NULL),
	(2,4,NULL,NULL),
	(3,4,NULL,NULL),
	(2,9,NULL,NULL),
	(2,10,NULL,NULL),
	(3,8,NULL,NULL),
	(3,10,NULL,NULL),
	(1,2,NULL,NULL),
	(1,3,NULL,NULL),
	(1,4,NULL,NULL),
	(1,5,NULL,NULL),
	(1,8,NULL,NULL),
	(3,12,NULL,NULL),
	(3,13,NULL,NULL),
	(1,12,NULL,NULL),
	(1,13,NULL,NULL),
	(1,10,NULL,NULL),
	(4,2,NULL,NULL),
	(4,3,NULL,NULL),
	(4,4,NULL,NULL),
	(4,5,NULL,NULL);

/*!40000 ALTER TABLE `admin_role_permissions` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table admin_roles
# ------------------------------------------------------------

DROP TABLE IF EXISTS `admin_roles`;

CREATE TABLE `admin_roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `admin_roles_name_unique` (`name`),
  UNIQUE KEY `admin_roles_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `admin_roles` WRITE;
/*!40000 ALTER TABLE `admin_roles` DISABLE KEYS */;

INSERT INTO `admin_roles` (`id`, `name`, `slug`, `created_at`, `updated_at`)
VALUES
	(1,'Administrator','administrator','2019-12-18 09:18:30','2019-12-18 09:18:30'),
	(2,'学生','student','2019-12-19 09:54:59','2019-12-19 09:54:59'),
	(3,'教师','teacher','2019-12-19 10:52:34','2019-12-19 10:52:34'),
	(4,'管理员','admin','2020-04-29 11:31:54','2020-04-29 11:31:54');

/*!40000 ALTER TABLE `admin_roles` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
