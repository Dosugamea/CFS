ALTER TABLE `tmp_live_playing`
	ADD COLUMN `reward_flag` INT NOT NULL DEFAULT '0' AFTER `factor`;