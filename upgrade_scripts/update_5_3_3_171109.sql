CREATE TABLE `personalnotice` (
	`user_id` INT(11) NOT NULL,
	`notice_id` INT(11) NOT NULL,
	`type` INT(11) NOT NULL DEFAULT '1',
	`title` TEXT NOT NULL,
	`content` TEXT NOT NULL,
	`agree` TINYINT(4) NOT NULL DEFAULT '0',
	`insert_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`agree_date` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00'
	UNIQUE INDEX `Index 1` (`user_id`, `notice_id`)
)
COLLATE='utf8mb4_general_ci'
ENGINE=InnoDB
;
CREATE TABLE `personalnotice_global` (
	`user_id` INT(11) NOT NULL,
	`notice_id` INT(11) NOT NULL,
	`insert_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
	UNIQUE INDEX `Index 1` (`user_id`, `notice_id`)
)
ENGINE=InnoDB
;