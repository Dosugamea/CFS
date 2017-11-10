CREATE TABLE `user_card_switch` (
	`user_id` INT(11) NULL,
	`user_from` INT(11) NULL,
	`stat` INT(1) NOT NULL DEFAULT '0'
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
;
