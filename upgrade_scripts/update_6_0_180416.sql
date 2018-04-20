CREATE TABLE `live_log` (
	`live_log_id` INT(11) NOT NULL AUTO_INCREMENT,
	`user_id` INT(11) NOT NULL,
	`live_difficulty_id` INT(11) NOT NULL,
	`score` INT(11) NOT NULL,
	`perfect_cnt` INT(11) NOT NULL,
	`great_cnt` INT(11) NOT NULL,
	`good_cnt` INT(11) NOT NULL,
	`bad_cnt` INT(11) NOT NULL,
	`miss_cnt` INT(11) NOT NULL,
	`max_combo` INT(11) NOT NULL,
	`precise_score_log` MEDIUMTEXT NOT NULL,
	`timeStamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`live_log_id`),
	INDEX `Index 1` (`user_id`)
)
ENGINE=InnoDB
ROW_FORMAT=COMPRESSED
;
TRUNCATE `log`;
ALTER TABLE `log`
	ROW_FORMAT=COMPRESSED,
	CHANGE COLUMN `request` `request` MEDIUMTEXT NOT NULL AFTER `action`,
	CHANGE COLUMN `response` `response` MEDIUMTEXT NOT NULL AFTER `request`;

CREATE TABLE `live_precise_log` (
	`user_id` INT(11) NOT NULL,
	`live_difficulty_id` INT(11) NOT NULL,
	`skill` TINYINT(4) NOT NULL,
	`perfect_cnt` SMALLINT(6) NOT NULL,
	`great_cnt` SMALLINT(6) NOT NULL,
	`good_cnt` SMALLINT(6) NOT NULL,
	`precise_list` MEDIUMTEXT NOT NULL,
	`max_combo` MEDIUMTEXT NOT NULL,
	`deck_info` MEDIUMTEXT NOT NULL,
	`timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	UNIQUE INDEX `Index 1` (`user_id`, `live_difficulty_id`, `skill`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
ROW_FORMAT=COMPRESSED
;