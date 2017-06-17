ALTER TABLE live_ranking
ADD mx_perfect_cnt INT NOT NULL DEFAULT 0 AFTER clear_cnt,
ADD mx_great_cnt INT NOT NULL DEFAULT 0 AFTER mx_perfect_cnt,
ADD mx_good_cnt INT NOT NULL DEFAULT 0 AFTER mx_great_cnt,
ADD mx_bad_cnt INT NOT NULL DEFAULT 0 AFTER mx_good_cnt,
ADD mx_max_combo INT NOT NULL DEFAULT 0 AFTER mx_bad_cnt,
ADD mt_miss_cnt INT NOT NULL DEFAULT 0 AFTER mx_bad_cnt;