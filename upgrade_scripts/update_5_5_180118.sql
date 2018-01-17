CREATE TABLE `invitation` (
	`user_id` INT(11) NOT NULL,
	`from_user` INT(11) NOT NULL,
	`time` TIMESTAMP NOT NULL DEFAULT '',
	PRIMARY KEY (`user_id`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB