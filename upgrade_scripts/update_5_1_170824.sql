-- --------------------------------------------------------
-- 主机:                           127.0.0.1
-- 服务器版本:                        10.2.6-MariaDB - mariadb.org binary distribution
-- 服务器操作系统:                      Win64
-- HeidiSQL 版本:                  9.4.0.5125
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- 导出 lovelive 的数据库结构
CREATE DATABASE IF NOT EXISTS `lovelive` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `lovelive`;

-- 导出  表 lovelive.tmp_challenge_live 结构
CREATE TABLE IF NOT EXISTS `tmp_challenge_live` (
  `user_id` int(11) NOT NULL,
  `course_id` tinyint(4) DEFAULT NULL,
  `round` tinyint(4) NOT NULL DEFAULT 1,
  `live_difficulty_id` int(11) DEFAULT NULL,
  `random` tinyint(4) NOT NULL DEFAULT 0,
  `bonus` text DEFAULT '[]',
  `mission` text DEFAULT '[]',
  `use_item` text NOT NULL DEFAULT '[]',
  `is_start` tinyint(4) NOT NULL DEFAULT 0,
  `event_point` int(11) NOT NULL DEFAULT 0,
  `exp` int(11) NOT NULL DEFAULT 0,
  `coin` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 数据导出被取消选择。
-- 导出  表 lovelive.tmp_challenge_reward 结构
CREATE TABLE IF NOT EXISTS `tmp_challenge_reward` (
  `user_id` int(11) NOT NULL,
  `rarity` int(11) NOT NULL,
  `amount` int(11) NOT NULL DEFAULT 0,
  UNIQUE KEY `Index 1` (`user_id`,`rarity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 数据导出被取消选择。
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
