ALTER TABLE incentive_list
ADD item_id INT(11) NULL DEFAULT NULL AFTER incentive_item_id;
UPDATE incentive_list SET item_id = 2 WHERE incentive_item_id = 3006;