CREATE TABLE `secretbox_stepup` (
	`user_id` INT NOT NULL,
	`secretbox_id` INT NOT NULL,
	`step` INT NOT NULL DEFAULT '1',
	`last_scout` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	UNIQUE INDEX `Index 1` (`user_id`, `secretbox_id`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
;
