ALTER TABLE `tmp_duty_room`
	ADD COLUMN `bonus_value` FLOAT NOT NULL DEFAULT '1' AFTER `mission_id`;