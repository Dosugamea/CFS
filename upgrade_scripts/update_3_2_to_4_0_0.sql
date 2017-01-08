CREATE TABLE `user_params` (
 `user_id` int(11) NOT NULL,
 `param` varchar(255) NOT NULL,
 `value` int(11) NOT NULL,
 PRIMARY KEY (`user_id`,`param`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO user_params SELECT user_id, 'enable_card_switch', enable_card_switch FROM user_perm;
INSERT INTO user_params SELECT user_id, 'card_switch', card_switch FROM user_perm;
INSERT INTO user_params SELECT user_id, 'allow_test_func', allow_test_func FROM user_perm;
INSERT INTO user_params SELECT user_id, 'random_switch', random_switch FROM user_perm;
INSERT INTO user_params SELECT user_id, 'item1', ticket FROM user_info;
INSERT INTO user_params SELECT user_id, 'item2', social_point FROM user_info;
INSERT INTO user_params SELECT user_id, 'item3', game_coin FROM user_info;
INSERT INTO user_params SELECT user_id, 'item4', sns_coin FROM user_info;
INSERT INTO user_params SELECT user_id, 'item5', sticket FROM user_info;
INSERT INTO user_params SELECT user_id, 'seal1', seal FROM exchange_point;
INSERT INTO user_params SELECT user_id, 'seal2', super_seal FROM exchange_point;
INSERT INTO user_params SELECT user_id, 'seal4', ultra_seal FROM exchange_point;

drop table exchange_point;
drop table user_perm;

CREATE TABLE `users` (
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
 PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO users SELECT user_info.user_id, name, introduction, level, exp, award, background, insert_date, user_login.username, password, login_password, authorize_token, nonce, elapsed_time_from_login from user_info left join user_login on user_login.user_id=user_info.user_id left join authorize on authorize.user_id=user_info.user_id;

drop table user_login;
drop table user_info;
drop table authorize;

update users set award=1 where award=0;
update users set background=1 where background=0;

INSERT INTO user_params SELECT user_id, 'extend_mods_vanish', speed FROM extend_notes_speed where difficulty=1;
INSERT INTO user_params SELECT user_id, 'extend_mods_mirror', speed FROM extend_notes_speed where difficulty=2;
INSERT INTO user_params SELECT user_id, 'extend_mods_life', speed FROM extend_notes_speed where difficulty=3;
INSERT INTO user_params SELECT user_id, 'extend_mods_hantei_count', speed FROM extend_notes_speed where difficulty=4;
drop table extend_notes_speed;
INSERT INTO user_params SELECT user_id, 'extend_avatar', unit_id FROM extend_avatar;
INSERT INTO user_params SELECT user_id, 'extend_avatar_is_rankup', rankup FROM extend_avatar;
drop table extend_avatar;

ALTER TABLE `incentive_list` DROP `incentive_type`;
drop table if exists `live_goal_reward_m`;

CREATE TABLE `tmp_battle_result` (
 `user_id` int(11) NOT NULL,
 `battle_event_room_id` int(11) NOT NULL,
 `result` text NOT NULL,
 `reward` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;
CREATE TABLE `tmp_battle_room` (
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
CREATE TABLE `tmp_festival_playing` (
 `user_id` int(11) NOT NULL,
 `lives` text NOT NULL,
 PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `tmp_live_playing` (
 `user_id` int(11) NOT NULL,
 `unit_deck_id` smallint(6) NOT NULL,
 `party_user_id` int(11) NOT NULL DEFAULT '0',
 `play_count` int(11) NOT NULL DEFAULT '0',
 PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
drop table live_playing;
drop table festival_playing;
drop table battle_room;
drop table battle_result;

ALTER TABLE `album` ADD `total_love` INT NOT NULL DEFAULT '0' AFTER `rank_level_max_flag`;

CREATE TABLE `tmp_authorize` (
 `token` varchar(255) NOT NULL,
 `username` varchar(255) NOT NULL DEFAULT '',
 `password` varchar(255) NOT NULL DEFAULT '',
 PRIMARY KEY (`token`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8;