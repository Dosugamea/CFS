SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


CREATE TABLE IF NOT EXISTS `album` (
  `user_id` int(11) NOT NULL,
  `unit_id` int(11) NOT NULL,
  `rank_max_flag` tinyint(1) NOT NULL DEFAULT '0',
  `love_max_flag` tinyint(1) NOT NULL DEFAULT '0',
  `rank_level_max_flag` tinyint(1) NOT NULL DEFAULT '0',
  `total_love` int(11) NOT NULL DEFAULT '0',
  UNIQUE KEY `user_id` (`user_id`,`unit_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `banned_user` (
  `user` varchar(255) NOT NULL,
  `msg` text NOT NULL,
  PRIMARY KEY (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `effort_box` (
  `user_id` INT(11) NOT NULL,
  `box_id` INT(11) NOT NULL,
  `point` INT(11) NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `error_report` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `token` varchar(40) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `text` text NOT NULL,
  `dele` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `exchange_log` (
  `user_id` int(11) NOT NULL,
  `exchange_item_id` int(11) NOT NULL,
  `got_item_count` int(11) NOT NULL DEFAULT '0',
  UNIQUE KEY `user_id` (`user_id`,`exchange_item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `extend_download` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `url` text NOT NULL,
  `size` int(11) NOT NULL,
  `description` text NOT NULL,
  `version` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `extend_download_queue` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `download_id` int(11) NOT NULL,
  `downloaded_version` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `extend_medley` (
  `medley_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `medley_type` tinyint(1) NOT NULL,
  `song_count` int(11) NOT NULL DEFAULT '3',
  PRIMARY KEY (`medley_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `extend_medley_bind` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `difficulty` tinyint(1) NOT NULL,
  `count` tinyint(1) NOT NULL,
  `medley` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `extend_medley_song_30` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `medley_id` int(11) NOT NULL,
  `notes_setting_asset` tinytext NOT NULL,
  `random_switch` tinyint(1) NOT NULL,
  `order` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `friend` (
  `applicant` INT(11) NOT NULL,
  `applicated` INT(11) NOT NULL,
  `status` TINYINT(4) NOT NULL DEFAULT '1',
  `insert_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `agree_date` TIMESTAMP NULL DEFAULT NULL,
  `read` TINYINT(4) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `incentive_list` (
  `incentive_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `incentive_item_id` int(11) NOT NULL,
  `amount` int(11) NOT NULL,
  `is_card` tinyint(1) NOT NULL,
  `incentive_message` text NOT NULL,
  `insert_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `opened_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`incentive_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `live_goal` (
  `user_id` int(11) NOT NULL,
  `live_goal_reward_id` int(11) NOT NULL,
  UNIQUE KEY `user_id` (`user_id`,`live_goal_reward_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `live_ranking` (
  `user_id` int(11) NOT NULL,
  `notes_setting_asset` varchar(255) NOT NULL,
  `card_switch` tinyint(1) NOT NULL DEFAULT '0',
  `random_switch` tinyint(2) NOT NULL DEFAULT '0',
  `hi_score` int(11) NOT NULL,
  `hi_combo_count` int(11) NOT NULL,
  `clear_cnt` int(11) NOT NULL,
  UNIQUE KEY `user_id` (`user_id`,`notes_setting_asset`,`card_switch`,`random_switch`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `login_bonus` (
  `user_id` INT(11) NOT NULL,
  `year` INT(11) NOT NULL,
  `month` INT(11) NOT NULL,
  `day` INT(11) NOT NULL,
  `insert_timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `login_bonus_n` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `nlbonus_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `last_seq` int(11) NOT NULL DEFAULT '0',
  `last_login_date` timestamp NOT NULL DEFAULT '2000-01-01 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `mail` (
  `notice_id` INT(11) NOT NULL AUTO_INCREMENT,
  `from_id` INT(11) NOT NULL,
  `to_id` INT(11) NOT NULL,
  `message` TEXT NULL,
  `read` TINYINT(4) NOT NULL DEFAULT '0',
  `replied` TINYINT(4) NOT NULL DEFAULT '0',
  `insert_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`notice_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `notes_setting` (
  `notes_setting_asset` varchar(255) NOT NULL,
  `notes_list` mediumtext NOT NULL,
  PRIMARY KEY (`notes_setting_asset`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `programmed_live` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `dl` text,
  `live_json` text NOT NULL,
  `notes_setting_asset` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `removable_skill` (
	`user_id` INT(11) NOT NULL,
	`skill_id` INT(11) UNSIGNED NOT NULL,
	`amount` INT(11) UNSIGNED NOT NULL,
	`equipped` INT(11) UNSIGNED NOT NULL DEFAULT '0',
	`insert_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `secretbox` (
  `user_id` int(11) NOT NULL,
  `last_scout_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `got_free_gacha_list` text NOT NULL,
  `gauge` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `tos` (
  `user_id` int(11) NOT NULL,
  `tos_id` int(11) NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `tmp_authorize` (
  `token` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL DEFAULT '',
  `password` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`token`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `tmp_battle_result` (
  `user_id` int(11) NOT NULL,
  `battle_event_room_id` int(11) NOT NULL,
  `result` text NOT NULL,
  `reward` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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

CREATE TABLE IF NOT EXISTS `tmp_festival_playing` (
  `user_id` int(11) NOT NULL,
  `lives` text NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `tmp_live_playing` (
  `user_id` int(11) NOT NULL,
  `unit_deck_id` smallint(6) NOT NULL,
  `party_user_id` int(11) NOT NULL DEFAULT '0',
  `play_count` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `unit_list` (
  `unit_owning_user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `unit_id` int(11) NOT NULL,
  `rank` tinyint(2) NOT NULL DEFAULT '1',
  `exp` int(11) NOT NULL DEFAULT '0',
  `love` int(11) NOT NULL DEFAULT '0',
  `unit_skill_exp` int(11) NOT NULL DEFAULT '0',
  `favorite_flag` tinyint(1) NOT NULL DEFAULT '0',
  `insert_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`unit_owning_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `unit_support_list` (
	`user_id` INT(11) NOT NULL,
	`unit_id` INT(11) NOT NULL,
	`amount` INT(11) NOT NULL,
	`insert_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
  `nonce` int(11) NOT NULL DEFAULT '1',
  `elapsed_time_from_login` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `download_site` INT(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `user_deck` (
  `user_id` int(11) NOT NULL,
  `json` text NOT NULL,
  `center_unit` int(11) NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `user_params` (
  `user_id` int(11) NOT NULL,
  `param` varchar(255) NOT NULL,
  `value` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`param`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `webview` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tab` tinyint(1) NOT NULL DEFAULT '1',
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `detail_id` int(11) NOT NULL DEFAULT '0',
  `order` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `unit_list` ADD `display_rank` TINYINT NOT NULL DEFAULT '2' AFTER `favorite_flag`; 
CREATE TABLE `tmp_battle_user_room` ( `user_id` INT NOT NULL , `room_id` INT NOT NULL , PRIMARY KEY (`user_id`)) ENGINE = MEMORY; 

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
