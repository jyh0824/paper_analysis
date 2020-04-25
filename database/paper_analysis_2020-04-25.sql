# ************************************************************
# Sequel Pro SQL dump
# Version 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: 127.0.0.1 (MySQL 5.7.25)
# Database: paper_analysis
# Generation Time: 2020-04-25 03:40:26 +0000
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
	(1,0,1,'首页','fa-institution','/',NULL,NULL,'2019-12-19 11:37:41'),
	(2,0,2,'系统管理','fa-wrench',NULL,NULL,NULL,'2020-04-25 03:06:25'),
	(3,2,3,'用户','fa-users','auth/users','auth.management',NULL,'2020-03-25 02:16:13'),
	(4,2,4,'角色','fa-user','auth/roles','auth.management',NULL,'2020-03-25 02:16:18'),
	(5,2,5,'权限','fa-ban','auth/permissions','auth.management',NULL,'2020-03-25 02:16:24'),
	(6,2,6,'菜单','fa-bars','auth/menu','auth.management',NULL,'2020-03-25 02:16:29'),
	(7,2,8,'操作记录','fa-history','auth/logs','auth.management',NULL,'2020-03-25 02:16:42'),
	(8,0,9,'试卷管理','fa-file-text-o',NULL,'paper.management','2019-12-19 11:14:11','2020-03-25 02:15:51'),
	(10,8,10,'套卷管理','fa-edit','paper','paper.management','2019-12-19 11:24:36','2020-03-25 02:15:23'),
	(11,8,15,'学生答案管理','fa-laptop','answer/','paper.management','2019-12-19 11:36:22','2020-03-25 02:15:43'),
	(12,8,11,'题目管理','fa-bookmark','question',NULL,'2020-02-08 01:24:51','2020-02-22 07:28:43'),
	(13,12,12,'选择题管理','fa-inbox','question/selection','question.management','2020-02-08 01:28:27','2020-04-22 06:31:46'),
	(14,12,13,'判断题管理','fa-inbox','question/judgement','question.management','2020-02-08 08:48:27','2020-04-22 06:31:37'),
	(15,12,14,'主观题管理','fa-inbox','question/subjective','question.management','2020-02-08 08:48:46','2020-04-22 06:31:51'),
	(16,2,7,'反馈','fa-envelope','auth/feedback','auth.management','2020-02-22 07:28:03','2020-02-25 03:36:29'),
	(17,0,19,'我的反馈','fa-envelope-o','feedback','feedback','2020-02-22 07:32:55','2020-04-22 06:34:21'),
	(18,0,18,'我的试卷','fa-tasks','student/paper','my.paper','2020-02-26 03:51:42','2020-04-22 06:34:07'),
	(19,11,17,'主观题答案','fa-list','answer/subjective','answer.management','2020-03-30 02:50:25','2020-04-22 06:32:02'),
	(20,11,16,'学生答案','fa-list','answer/','answer.management','2020-03-30 02:51:09','2020-04-22 06:31:57');

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
	(1,2,NULL,NULL),
	(3,8,NULL,NULL),
	(3,10,NULL,NULL),
	(3,11,NULL,NULL),
	(1,10,NULL,NULL),
	(1,11,NULL,NULL),
	(1,8,NULL,NULL),
	(1,12,NULL,NULL),
	(3,12,NULL,NULL),
	(1,14,NULL,NULL),
	(3,14,NULL,NULL),
	(1,15,NULL,NULL),
	(3,15,NULL,NULL),
	(1,16,NULL,NULL),
	(1,13,NULL,NULL),
	(3,13,NULL,NULL),
	(1,3,NULL,NULL),
	(1,5,NULL,NULL),
	(1,4,NULL,NULL),
	(1,6,NULL,NULL),
	(1,7,NULL,NULL),
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
	(1,13,NULL,NULL);

/*!40000 ALTER TABLE `admin_role_permissions` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table admin_role_users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `admin_role_users`;

CREATE TABLE `admin_role_users` (
  `role_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  KEY `admin_role_users_role_id_user_id_index` (`role_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `admin_role_users` WRITE;
/*!40000 ALTER TABLE `admin_role_users` DISABLE KEYS */;

INSERT INTO `admin_role_users` (`role_id`, `user_id`, `created_at`, `updated_at`)
VALUES
	(1,1,NULL,NULL),
	(3,2,NULL,NULL),
	(2,3,NULL,NULL),
	(2,4,NULL,NULL),
	(2,5,NULL,NULL),
	(2,6,NULL,NULL),
	(2,7,NULL,NULL),
	(2,8,NULL,NULL),
	(2,9,NULL,NULL),
	(2,10,NULL,NULL),
	(2,11,NULL,NULL),
	(2,12,NULL,NULL),
	(2,13,NULL,NULL),
	(2,14,NULL,NULL),
	(2,15,NULL,NULL),
	(2,16,NULL,NULL),
	(2,17,NULL,NULL),
	(2,18,NULL,NULL),
	(2,19,NULL,NULL),
	(2,20,NULL,NULL),
	(2,21,NULL,NULL),
	(2,22,NULL,NULL),
	(2,23,NULL,NULL),
	(2,24,NULL,NULL),
	(2,25,NULL,NULL),
	(2,26,NULL,NULL),
	(2,27,NULL,NULL),
	(2,28,NULL,NULL),
	(2,29,NULL,NULL),
	(2,30,NULL,NULL),
	(2,31,NULL,NULL),
	(2,32,NULL,NULL),
	(2,33,NULL,NULL),
	(2,34,NULL,NULL),
	(2,35,NULL,NULL),
	(2,36,NULL,NULL),
	(2,37,NULL,NULL),
	(2,38,NULL,NULL),
	(2,39,NULL,NULL),
	(2,40,NULL,NULL),
	(2,41,NULL,NULL),
	(2,42,NULL,NULL),
	(2,43,NULL,NULL),
	(2,44,NULL,NULL),
	(2,45,NULL,NULL),
	(2,46,NULL,NULL),
	(2,47,NULL,NULL),
	(2,48,NULL,NULL),
	(2,49,NULL,NULL),
	(2,50,NULL,NULL),
	(2,51,NULL,NULL),
	(2,52,NULL,NULL),
	(2,53,NULL,NULL),
	(2,54,NULL,NULL),
	(2,55,NULL,NULL),
	(2,56,NULL,NULL),
	(2,57,NULL,NULL),
	(2,58,NULL,NULL),
	(2,59,NULL,NULL),
	(2,60,NULL,NULL),
	(2,61,NULL,NULL),
	(2,62,NULL,NULL),
	(2,63,NULL,NULL),
	(2,64,NULL,NULL),
	(2,65,NULL,NULL),
	(2,66,NULL,NULL),
	(2,67,NULL,NULL),
	(2,68,NULL,NULL),
	(2,69,NULL,NULL),
	(2,70,NULL,NULL),
	(2,71,NULL,NULL),
	(2,72,NULL,NULL),
	(2,73,NULL,NULL),
	(2,74,NULL,NULL),
	(2,75,NULL,NULL),
	(2,76,NULL,NULL),
	(2,77,NULL,NULL),
	(2,78,NULL,NULL),
	(2,79,NULL,NULL),
	(2,80,NULL,NULL),
	(2,81,NULL,NULL),
	(2,82,NULL,NULL),
	(2,83,NULL,NULL),
	(2,84,NULL,NULL),
	(2,85,NULL,NULL),
	(2,86,NULL,NULL),
	(2,87,NULL,NULL),
	(2,88,NULL,NULL),
	(2,89,NULL,NULL),
	(2,90,NULL,NULL),
	(2,91,NULL,NULL),
	(2,92,NULL,NULL),
	(2,93,NULL,NULL),
	(2,94,NULL,NULL),
	(2,95,NULL,NULL),
	(2,96,NULL,NULL),
	(2,97,NULL,NULL),
	(2,98,NULL,NULL),
	(2,99,NULL,NULL),
	(2,100,NULL,NULL),
	(2,101,NULL,NULL),
	(2,102,NULL,NULL),
	(2,103,NULL,NULL),
	(2,104,NULL,NULL),
	(2,105,NULL,NULL),
	(2,106,NULL,NULL),
	(2,107,NULL,NULL),
	(2,108,NULL,NULL),
	(2,109,NULL,NULL),
	(2,110,NULL,NULL),
	(2,111,NULL,NULL),
	(2,112,NULL,NULL),
	(2,113,NULL,NULL),
	(2,114,NULL,NULL),
	(2,115,NULL,NULL),
	(2,116,NULL,NULL),
	(2,117,NULL,NULL),
	(2,118,NULL,NULL),
	(2,119,NULL,NULL),
	(2,120,NULL,NULL),
	(2,121,NULL,NULL),
	(2,122,NULL,NULL),
	(2,123,NULL,NULL),
	(2,124,NULL,NULL),
	(2,125,NULL,NULL),
	(2,126,NULL,NULL),
	(2,127,NULL,NULL),
	(2,128,NULL,NULL),
	(2,129,NULL,NULL),
	(2,130,NULL,NULL),
	(2,131,NULL,NULL),
	(2,132,NULL,NULL),
	(2,133,NULL,NULL),
	(2,134,NULL,NULL),
	(2,135,NULL,NULL),
	(2,136,NULL,NULL),
	(2,137,NULL,NULL),
	(2,138,NULL,NULL),
	(2,139,NULL,NULL),
	(2,140,NULL,NULL),
	(2,141,NULL,NULL),
	(2,142,NULL,NULL),
	(2,143,NULL,NULL),
	(2,144,NULL,NULL),
	(2,145,NULL,NULL),
	(2,146,NULL,NULL),
	(2,147,NULL,NULL),
	(2,148,NULL,NULL),
	(2,149,NULL,NULL),
	(2,150,NULL,NULL),
	(2,151,NULL,NULL),
	(2,152,NULL,NULL);

/*!40000 ALTER TABLE `admin_role_users` ENABLE KEYS */;
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
	(3,'教师','teacher','2019-12-19 10:52:34','2019-12-19 10:52:34');

/*!40000 ALTER TABLE `admin_roles` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table admin_user_permissions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `admin_user_permissions`;

CREATE TABLE `admin_user_permissions` (
  `user_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  KEY `admin_user_permissions_user_id_permission_id_index` (`user_id`,`permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
