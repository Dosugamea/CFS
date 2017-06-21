ALTER TABLE secretbox
DROP COLUMN got_free_gacha_list,
ADD COLUMN free_gacha_muse TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER last_scout_time,
ADD COLUMN free_gacha_aqours TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER free_gacha_muse;