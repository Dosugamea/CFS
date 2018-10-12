CREATE TABLE `tmp_duel_room` (
	`room_id` INT(11) NOT NULL AUTO_INCREMENT,
	`users` LONGTEXT NULL DEFAULT NULL COLLATE 'utf8mb4_bin',
    `difficulty` TINYINT(4) NOT NULL DEFAULT '0',
    `card_switch` TINYINT(4) NOT NULL DEFAULT '0',
	PRIMARY KEY (`room_id`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=35;