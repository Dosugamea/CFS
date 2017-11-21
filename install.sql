-- --------------------------------------------------------
-- 主机:                           REMOVED
-- 服务器版本:                        10.1.26-MariaDB - Source distribution
-- 服务器操作系统:                      Linux
-- HeidiSQL 版本:                  9.4.0.5125
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- 导出  表 lovelive.album 结构
CREATE TABLE IF NOT EXISTS `album` (
  `user_id` int(11) NOT NULL,
  `unit_id` int(11) NOT NULL,
  `rank_max_flag` tinyint(1) NOT NULL DEFAULT '0',
  `love_max_flag` tinyint(1) NOT NULL DEFAULT '0',
  `rank_level_max_flag` tinyint(1) NOT NULL DEFAULT '0',
  `total_love` int(11) NOT NULL DEFAULT '0',
  UNIQUE KEY `user_id` (`user_id`,`unit_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 数据导出被取消选择。
-- 导出  表 lovelive.auth_log 结构
CREATE TABLE IF NOT EXISTS `auth_log` (
  `user_id` int(11) NOT NULL DEFAULT '0',
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `login_key` text,
  `login_passwd` text,
  `device_data` text,
  `hdr_device` text,
  `ip` text,
  `client_version` text,
  `bundle_version` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 数据导出被取消选择。
-- 导出  表 lovelive.award 结构
CREATE TABLE IF NOT EXISTS `award` (
  `user_id` int(11) NOT NULL,
  `award_id` int(11) NOT NULL,
  `insert_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `Index 1` (`user_id`,`award_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 数据导出被取消选择。
-- 导出  表 lovelive.banned_user 结构
CREATE TABLE IF NOT EXISTS `banned_user` (
  `user` varchar(255) NOT NULL,
  `msg` text NOT NULL,
  PRIMARY KEY (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 数据导出被取消选择。
-- 导出  表 lovelive.effort_box 结构
CREATE TABLE IF NOT EXISTS `effort_box` (
  `user_id` int(11) NOT NULL,
  `box_id` int(11) NOT NULL,
  `point` int(11) NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 数据导出被取消选择。
-- 导出  表 lovelive.error_report 结构
CREATE TABLE IF NOT EXISTS `error_report` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `token` varchar(40) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `text` text NOT NULL,
  `dele` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 数据导出被取消选择。
-- 导出  表 lovelive.event_point 结构
CREATE TABLE IF NOT EXISTS `event_point` (
  `user_id` int(11) NOT NULL,
  `event_id` int(2) NOT NULL,
  `event_point` int(11) NOT NULL DEFAULT '0',
  `score_point` int(11) NOT NULL DEFAULT '0',
  UNIQUE KEY `UNIQUE` (`user_id`,`event_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 数据导出被取消选择。
-- 导出  表 lovelive.exchange_log 结构
CREATE TABLE IF NOT EXISTS `exchange_log` (
  `user_id` int(11) NOT NULL,
  `exchange_item_id` int(11) NOT NULL,
  `got_item_count` int(11) NOT NULL DEFAULT '0',
  UNIQUE KEY `user_id` (`user_id`,`exchange_item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 数据导出被取消选择。
-- 导出  表 lovelive.extend_download 结构
CREATE TABLE IF NOT EXISTS `extend_download` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `url` text NOT NULL,
  `size` int(11) NOT NULL,
  `description` text NOT NULL,
  `version` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- 数据导出被取消选择。
-- 导出  表 lovelive.extend_download_queue 结构
CREATE TABLE IF NOT EXISTS `extend_download_queue` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `download_id` int(11) NOT NULL,
  `downloaded_version` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- 数据导出被取消选择。
-- 导出  表 lovelive.extend_medley 结构
CREATE TABLE IF NOT EXISTS `extend_medley` (
  `medley_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `medley_type` tinyint(1) NOT NULL,
  `song_count` int(11) NOT NULL DEFAULT '3',
  PRIMARY KEY (`medley_id`)
) ENGINE=InnoDB AUTO_INCREMENT=175 DEFAULT CHARSET=utf8;

-- 数据导出被取消选择。
-- 导出  表 lovelive.extend_medley_bind 结构
CREATE TABLE IF NOT EXISTS `extend_medley_bind` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `difficulty` tinyint(1) NOT NULL,
  `count` tinyint(1) NOT NULL,
  `medley` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=146 DEFAULT CHARSET=utf8;

-- 数据导出被取消选择。
-- 导出  表 lovelive.extend_medley_song_30 结构
CREATE TABLE IF NOT EXISTS `extend_medley_song_30` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `medley_id` int(11) NOT NULL,
  `notes_setting_asset` tinytext NOT NULL,
  `random_switch` tinyint(1) NOT NULL,
  `order` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=1244 DEFAULT CHARSET=utf8;

-- 数据导出被取消选择。
-- 导出  表 lovelive.friend 结构
CREATE TABLE IF NOT EXISTS `friend` (
  `applicant` int(11) NOT NULL,
  `applicated` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  `insert_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `agree_date` timestamp NULL DEFAULT NULL,
  `read` tinyint(4) NOT NULL DEFAULT '0',
  UNIQUE KEY `Index 1` (`applicant`,`applicated`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 数据导出被取消选择。
-- 导出  表 lovelive.incentive_list 结构
CREATE TABLE IF NOT EXISTS `incentive_list` (
  `incentive_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `incentive_item_id` int(11) NOT NULL,
  `item_id` int(11) DEFAULT NULL,
  `amount` int(11) NOT NULL,
  `is_card` tinyint(1) NOT NULL,
  `incentive_message` text NOT NULL,
  `extra_info` text,
  `insert_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `opened_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`incentive_id`)
) ENGINE=InnoDB AUTO_INCREMENT=35555 DEFAULT CHARSET=utf8;

-- 数据导出被取消选择。
-- 导出  表 lovelive.live_accomplish 结构
CREATE TABLE IF NOT EXISTS `live_accomplish` (
  `user_id` int(11) NOT NULL,
  `notes_setting_asset` text NOT NULL,
  `insert_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `Unique` (`user_id`,`notes_setting_asset`(100))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 数据导出被取消选择。
-- 导出  表 lovelive.live_goal 结构
CREATE TABLE IF NOT EXISTS `live_goal` (
  `user_id` int(11) NOT NULL,
  `live_goal_reward_id` int(11) NOT NULL,
  UNIQUE KEY `user_id` (`user_id`,`live_goal_reward_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 数据导出被取消选择。
-- 导出  表 lovelive.live_ranking 结构
CREATE TABLE IF NOT EXISTS `live_ranking` (
  `user_id` int(11) NOT NULL,
  `notes_setting_asset` varchar(255) NOT NULL,
  `card_switch` tinyint(1) NOT NULL DEFAULT '0',
  `random_switch` tinyint(2) NOT NULL DEFAULT '0',
  `hi_score` int(11) NOT NULL,
  `hi_combo_count` int(11) NOT NULL,
  `clear_cnt` int(11) NOT NULL,
  `mx_perfect_cnt` int(11) NOT NULL DEFAULT '0',
  `mx_great_cnt` int(11) NOT NULL DEFAULT '0',
  `mx_good_cnt` int(11) NOT NULL DEFAULT '0',
  `mx_bad_cnt` int(11) NOT NULL DEFAULT '0',
  `mx_miss_cnt` int(11) NOT NULL DEFAULT '0',
  `mx_max_combo` int(11) NOT NULL DEFAULT '0',
  `cheat` int(11) NOT NULL DEFAULT '0',
  UNIQUE KEY `user_id` (`user_id`,`notes_setting_asset`,`card_switch`,`random_switch`),
  KEY `user_id_2` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 数据导出被取消选择。
-- 导出  表 lovelive.log 结构
CREATE TABLE IF NOT EXISTS `log` (
  `command_num` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `module` text NOT NULL,
  `action` text NOT NULL,
  `request` blob NOT NULL,
  `response` blob NOT NULL,
  PRIMARY KEY (`command_num`(100))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 数据导出被取消选择。
-- 导出  表 lovelive.login_bonus 结构
CREATE TABLE IF NOT EXISTS `login_bonus` (
  `user_id` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `month` int(11) NOT NULL DEFAULT '0',
  `day` int(11) NOT NULL DEFAULT '0',
  `insert_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 数据导出被取消选择。
-- 导出  表 lovelive.login_bonus_n 结构
CREATE TABLE IF NOT EXISTS `login_bonus_n` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `nlbonus_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `last_seq` int(11) NOT NULL DEFAULT '0',
  `last_login_date` timestamp NOT NULL DEFAULT '2000-01-01 08:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=832 DEFAULT CHARSET=utf8;

-- 数据导出被取消选择。
-- 导出  表 lovelive.mail 结构
CREATE TABLE IF NOT EXISTS `mail` (
  `notice_id` int(11) NOT NULL AUTO_INCREMENT,
  `from_id` int(11) NOT NULL,
  `to_id` int(11) NOT NULL,
  `message` text,
  `read` tinyint(4) NOT NULL DEFAULT '0',
  `replied` tinyint(4) NOT NULL DEFAULT '0',
  `insert_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`notice_id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;

-- 数据导出被取消选择。
-- 导出  表 lovelive.notes_setting 结构
CREATE TABLE IF NOT EXISTS `notes_setting` (
  `notes_setting_asset` varchar(255) NOT NULL,
  `notes_list` mediumtext NOT NULL,
  PRIMARY KEY (`notes_setting_asset`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 数据导出被取消选择。
-- 导出  表 lovelive.notes_setting_gf 结构
CREATE TABLE IF NOT EXISTS `notes_setting_gf` (
  `ID` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `artist` varchar(255) NOT NULL,
  `difficulty` tinyint(4) NOT NULL,
  `stage_level` tinyint(4) NOT NULL,
  `attribute` tinyint(4) NOT NULL,
  `bgm` varchar(255) NOT NULL,
  `json` mediumtext NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 数据导出被取消选择。
-- 导出  表 lovelive.packages 结构
CREATE TABLE IF NOT EXISTS `packages` (
  `update_id` int(11) NOT NULL AUTO_INCREMENT,
  `package_type` int(11) NOT NULL,
  `package_id` int(11) NOT NULL,
  `version` int(11) NOT NULL,
  `url` text NOT NULL,
  `size` int(11) NOT NULL,
  `os` enum('Android','iOS') NOT NULL,
  PRIMARY KEY (`update_id`),
  KEY `version` (`version`)
) ENGINE=InnoDB AUTO_INCREMENT=6394 DEFAULT CHARSET=utf8;

-- 数据导出被取消选择。
-- 导出  表 lovelive.programmed_live 结构
CREATE TABLE IF NOT EXISTS `programmed_live` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `dl` text,
  `live_json` text NOT NULL,
  `notes_setting_asset` varchar(255) NOT NULL,
  `category` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=116 DEFAULT CHARSET=utf8;

-- 数据导出被取消选择。
-- 导出  表 lovelive.removable_skill 结构
CREATE TABLE IF NOT EXISTS `removable_skill` (
  `user_id` int(11) NOT NULL,
  `skill_id` int(11) unsigned NOT NULL,
  `amount` int(11) unsigned NOT NULL,
  `equipped` int(11) unsigned NOT NULL DEFAULT '0',
  `insert_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 数据导出被取消选择。
-- 导出  表 lovelive.secretbox 结构
CREATE TABLE IF NOT EXISTS `secretbox` (
  `user_id` int(11) NOT NULL,
  `last_scout_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `free_gacha_muse` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `free_gacha_aqours` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `gauge` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 数据导出被取消选择。
-- 导出  表 lovelive.secretbox_count 结构
CREATE TABLE IF NOT EXISTS `secretbox_count` (
  `user_id` int(11) NOT NULL,
  `secretbox_id` int(11) NOT NULL,
  `count` int(11) NOT NULL DEFAULT '0',
  `last_scout` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `Index 1` (`user_id`,`secretbox_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 数据导出被取消选择。
-- 导出  表 lovelive.secretbox_stepup 结构
CREATE TABLE IF NOT EXISTS `secretbox_stepup` (
  `user_id` int(11) NOT NULL,
  `secretbox_id` int(11) NOT NULL,
  `step` int(11) NOT NULL DEFAULT '1',
  `last_scout` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `Index 1` (`user_id`,`secretbox_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 数据导出被取消选择。
-- 导出  表 lovelive.tmp_authorize 结构
CREATE TABLE IF NOT EXISTS `tmp_authorize` (
  `token` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL DEFAULT '',
  `password` varchar(255) NOT NULL DEFAULT '',
  `sessionKey` varchar(255) NOT NULL,
  PRIMARY KEY (`token`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8;

-- 数据导出被取消选择。
-- 导出  表 lovelive.tmp_battle_result 结构
CREATE TABLE IF NOT EXISTS `tmp_battle_result` (
  `user_id` int(11) NOT NULL,
  `battle_event_room_id` int(11) NOT NULL,
  `result` text NOT NULL,
  `reward` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 数据导出被取消选择。
-- 导出  表 lovelive.tmp_battle_room 结构
CREATE TABLE IF NOT EXISTS `tmp_battle_room` (
  `battle_event_room_id` int(11) NOT NULL,
  `difficulty` int(11) NOT NULL,
  `live_difficulty_id` int(11) NOT NULL,
  `player_ready_1` tinyint(1) NOT NULL DEFAULT '0',
  `player_ready_2` tinyint(1) NOT NULL DEFAULT '0',
  `player_ready_3` tinyint(1) NOT NULL DEFAULT '0',
  `player_ready_4` tinyint(1) NOT NULL DEFAULT '0',
  `start_flag` tinyint(1) NOT NULL DEFAULT '0',
  `player1` int(11) NOT NULL,
  `player2` int(11) NOT NULL DEFAULT '0',
  `player3` int(11) NOT NULL DEFAULT '0',
  `player4` int(11) NOT NULL DEFAULT '0',
  `event_chat_id_1` varchar(10) NOT NULL DEFAULT '',
  `event_chat_id_2` varchar(10) NOT NULL DEFAULT '',
  `event_chat_id_3` varchar(10) NOT NULL DEFAULT '',
  `event_chat_id_4` varchar(10) NOT NULL DEFAULT '',
  `ended_flag_1` tinyint(1) NOT NULL DEFAULT '0',
  `ended_flag_2` tinyint(1) NOT NULL DEFAULT '0',
  `ended_flag_3` tinyint(1) NOT NULL DEFAULT '0',
  `ended_flag_4` tinyint(1) NOT NULL DEFAULT '0',
  `timestamp` int(11) NOT NULL,
  `card_switch` tinyint(1) NOT NULL,
  `random_switch` int(11) NOT NULL,
  UNIQUE KEY `battle_event_room_id` (`battle_event_room_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 数据导出被取消选择。
-- 导出  表 lovelive.tmp_battle_user_room 结构
CREATE TABLE IF NOT EXISTS `tmp_battle_user_room` (
  `user_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8;

-- 数据导出被取消选择。
-- 导出  表 lovelive.tmp_challenge_live 结构
CREATE TABLE IF NOT EXISTS `tmp_challenge_live` (
  `user_id` int(11) NOT NULL,
  `course_id` tinyint(4) DEFAULT NULL,
  `round` tinyint(4) NOT NULL DEFAULT '1',
  `live_difficulty_id` int(11) DEFAULT NULL,
  `random` tinyint(4) NOT NULL DEFAULT '0',
  `bonus` text,
  `mission` text,
  `use_item` text,
  `is_start` tinyint(4) NOT NULL DEFAULT '0',
  `event_point` int(11) NOT NULL DEFAULT '0',
  `exp` int(11) NOT NULL DEFAULT '0',
  `coin` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 数据导出被取消选择。
-- 导出  表 lovelive.tmp_challenge_reward 结构
CREATE TABLE IF NOT EXISTS `tmp_challenge_reward` (
  `user_id` int(11) NOT NULL,
  `rarity` int(11) NOT NULL,
  `amount` int(11) NOT NULL DEFAULT '0',
  UNIQUE KEY `Index 1` (`user_id`,`rarity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 数据导出被取消选择。
-- 导出  表 lovelive.tmp_duty_result 结构
CREATE TABLE IF NOT EXISTS `tmp_duty_result` (
  `user_id` int(11) NOT NULL,
  `duty_event_room_id` int(11) NOT NULL,
  `result` text NOT NULL,
  `reward` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 数据导出被取消选择。
-- 导出  表 lovelive.tmp_duty_room 结构
CREATE TABLE IF NOT EXISTS `tmp_duty_room` (
  `duty_event_room_id` int(11) NOT NULL,
  `difficulty` int(11) NOT NULL,
  `live_difficulty_id` int(11) NOT NULL,
  `player_ready_1` tinyint(1) NOT NULL DEFAULT '0',
  `player_ready_2` tinyint(1) NOT NULL DEFAULT '0',
  `player_ready_3` tinyint(1) NOT NULL DEFAULT '0',
  `player_ready_4` tinyint(1) NOT NULL DEFAULT '0',
  `start_flag` tinyint(1) NOT NULL DEFAULT '0',
  `player1` int(11) NOT NULL,
  `player2` int(11) NOT NULL DEFAULT '0',
  `player3` int(11) NOT NULL DEFAULT '0',
  `player4` int(11) NOT NULL DEFAULT '0',
  `full_flag` tinyint(1) NOT NULL DEFAULT '0',
  `event_chat_id_1` varchar(10) NOT NULL DEFAULT '',
  `event_chat_id_2` varchar(10) NOT NULL DEFAULT '',
  `event_chat_id_3` varchar(10) NOT NULL DEFAULT '',
  `event_chat_id_4` varchar(10) NOT NULL DEFAULT '',
  `ended_flag_1` tinyint(1) NOT NULL DEFAULT '0',
  `ended_flag_2` tinyint(1) NOT NULL DEFAULT '0',
  `ended_flag_3` tinyint(1) NOT NULL DEFAULT '0',
  `ended_flag_4` tinyint(1) NOT NULL DEFAULT '0',
  `timestamp` int(11) NOT NULL,
  `card_switch` tinyint(1) NOT NULL,
  `random` tinyint(1) NOT NULL DEFAULT '0',
  `mission_id` tinyint(1) NOT NULL DEFAULT '1',
  `bonus_value` float NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 数据导出被取消选择。
-- 导出  表 lovelive.tmp_duty_user_room 结构
CREATE TABLE IF NOT EXISTS `tmp_duty_user_room` (
  `user_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `pos_id` int(11) NOT NULL,
  `deck_id` int(11) 
) ENGINE=MEMORY DEFAULT CHARSET=utf8;

-- 数据导出被取消选择。
-- 导出  表 lovelive.tmp_festival_playing 结构
CREATE TABLE IF NOT EXISTS `tmp_festival_playing` (
  `user_id` int(11) NOT NULL,
  `lives` text NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 数据导出被取消选择。
-- 导出  表 lovelive.tmp_live_playing 结构
CREATE TABLE IF NOT EXISTS `tmp_live_playing` (
  `user_id` int(11) NOT NULL,
  `unit_deck_id` smallint(6) NOT NULL,
  `party_user_id` int(11) NOT NULL DEFAULT '0',
  `play_count` int(11) NOT NULL DEFAULT '0',
  `factor` float NOT NULL DEFAULT '1',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 数据导出被取消选择。
-- 导出  表 lovelive.tos 结构
CREATE TABLE IF NOT EXISTS `tos` (
  `user_id` int(11) NOT NULL,
  `tos_id` int(11) NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 数据导出被取消选择。
-- 导出  表 lovelive.unit_list 结构
CREATE TABLE IF NOT EXISTS `unit_list` (
  `unit_owning_user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `unit_id` int(11) NOT NULL,
  `rank` tinyint(2) NOT NULL DEFAULT '1',
  `exp` int(11) NOT NULL DEFAULT '0',
  `love` int(11) NOT NULL DEFAULT '0',
  `unit_skill_exp` int(11) NOT NULL DEFAULT '0',
  `removable_skill` text,
  `removable_skill_count` tinyint(4) NOT NULL DEFAULT '0',
  `favorite_flag` tinyint(1) NOT NULL DEFAULT '0',
  `display_rank` tinyint(4) NOT NULL DEFAULT '2',
  `insert_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`unit_owning_user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1269050 DEFAULT CHARSET=utf8;

-- 数据导出被取消选择。
-- 导出  表 lovelive.unit_support_list 结构
CREATE TABLE IF NOT EXISTS `unit_support_list` (
  `user_id` int(11) NOT NULL,
  `unit_id` int(11) NOT NULL,
  `amount` int(11) NOT NULL,
  `insert_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 数据导出被取消选择。
-- 导出  表 lovelive.users 结构
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `introduction` text NOT NULL,
  `level` int(11) NOT NULL DEFAULT '1',
  `exp` int(11) NOT NULL DEFAULT '0',
  `award` int(11) NOT NULL DEFAULT '1',
  `background` int(11) NOT NULL DEFAULT '1',
  `insert_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `username` text NOT NULL,
  `password` text NOT NULL,
  `login_password` text NOT NULL,
  `authorize_token` varchar(255) NOT NULL DEFAULT '',
  `sessionKey` varchar(255) DEFAULT '',
  `nonce` int(11) NOT NULL DEFAULT '1',
  `elapsed_time_from_login` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `download_site` int(11) NOT NULL DEFAULT '1',
  `over_max_energy` int(11) NOT NULL DEFAULT '0',
  `energy_full_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `daily_reward` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `mail` text,
  `mail_pending` text,
  `mail_secret_key` text,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 数据导出被取消选择。
-- 导出  表 lovelive.user_deck 结构
CREATE TABLE IF NOT EXISTS `user_deck` (
  `user_id` int(11) NOT NULL,
  `json` text NOT NULL,
  `center_unit` int(11) NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 数据导出被取消选择。
-- 导出  表 lovelive.user_params 结构
CREATE TABLE IF NOT EXISTS `user_params` (
  `user_id` int(11) NOT NULL,
  `param` varchar(255) NOT NULL,
  `value` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`param`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 数据导出被取消选择。
-- 导出  表 lovelive.webview 结构
CREATE TABLE IF NOT EXISTS `webview` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tab` tinyint(1) NOT NULL DEFAULT '1',
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `detail_id` int(11) NOT NULL DEFAULT '0',
  `order` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=193 DEFAULT CHARSET=utf8;

-- 数据导出被取消选择。

CREATE TABLE `user_card_switch` (
	`user_id` INT(11) NULL,
	`user_from` INT(11) NULL,
	`stat` INT(1) NOT NULL DEFAULT '0',
	UNIQUE INDEX `user_id` (`user_id`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
;


/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */

