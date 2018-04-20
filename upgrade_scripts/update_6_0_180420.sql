CREATE TABLE `login_bonus_total` (
	`user_id` INT(11) NOT NULL,
	`count` INT(11) NOT NULL,
	`insert_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	UNIQUE INDEX `Index 1` (`user_id`, `count`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
;