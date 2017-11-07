<?php
include_once("includes/check_admin.php");
include("../../includes/db.php");
$unit = new PDO("sqlite:../../db/unit.db_");
$secretbox = new PDO("sqlite:../../db/secretbox_svonly.db_");
function batchInsert($cards, $box, $rarity){
	global $secretbox;
	$sql = "INSERT INTO secret_box_unit_m (secret_box_id, unit_group_id, unit_id, weight, start_date) VALUES";
	foreach($cards as $i){
		$sql .= "(".$box.", ".$rarity.", ".$i['unit_id'].", 2, '".date("Y-m-d H:i:s", time())."'), ";
	}
	$secretbox->query(substr($sql,0,strlen($sql)-2));
}
$secretbox->query("BEGIN TRANSACTION");
/*优等生招募-μ's*/
$secretbox->query("DELETE FROM secret_box_unit_m WHERE secret_box_id = 2");
$r = $unit->query("SELECT unit_id FROM unit_m WHERE rarity = 2 AND unit_type_id IN (1,2,3,4,5,6,7,8,9) AND smile_max != 1 AND release_tag IS NULL AND normal_icon_asset not like \"%rankup%\" and rank_max_icon_asset not like \"%normal%\"")->fetchAll(PDO::FETCH_ASSOC);
batchInsert($r, 2, 2);

$sr = $unit->query("SELECT unit_id FROM unit_m WHERE rarity = 3 AND unit_type_id IN (1,2,3,4,5,6,7,8,9) AND smile_max != 1 AND release_tag IS NULL AND normal_icon_asset not like \"%rankup%\" and rank_max_icon_asset not like \"%normal%\"")->fetchAll(PDO::FETCH_ASSOC);

$event_db = new PDO("sqlite:../../db/event_common.db_");
$event_point_sr_ = $event_db->query("SELECT item_id FROM event_point_count_reward_m WHERE add_type = 1001")->fetchAll(PDO::FETCH_ASSOC);
$event_rank_sr_ = $event_db->query("SELECT item_id FROM event_point_ranking_reward_m WHERE add_type = 1001")->fetchAll(PDO::FETCH_ASSOC);
$event_point_sr = [];
$event_rank_sr = [];
foreach($event_point_sr_ as $i) $event_point_sr []= (int)$i['item_id'];
foreach($event_rank_sr_ as $i) $event_rank_sr []= (int)$i['item_id'];
$event_card = array_unique(array_merge($event_rank_sr, $event_point_sr));
/*foreach($event_card as $i => $k)
	if($unit->query("SELECT rarity FROM unit_m WHERE unit_id = ".$k)->fetchColumn() != '3') unset($event_card[$i]);
$event_sr = array_values($event_card);*/

foreach($sr as $i){
	$weight = in_array((int)$i['unit_id'], $event_card) ? 1 : 5;
	$secretbox->query("INSERT INTO secret_box_unit_m (secret_box_id, unit_group_id, unit_id, weight, start_date) VALUES(2, 3, ".$i['unit_id'].", ".$weight.", '".date("Y-m-d H:i:s", time())."')");
}

$ur = $unit->query("SELECT unit_id FROM unit_m WHERE rarity = 4 AND unit_type_id IN (1,2,3,4,5,6,7,8,9) AND smile_max != 1 AND release_tag IS NULL AND normal_icon_asset not like \"%rankup%\" and rank_max_icon_asset not like \"%normal%\"")->fetchAll(PDO::FETCH_ASSOC);
batchInsert($ur, 2, 4);

$ssr = $unit->query("SELECT unit_id FROM unit_m WHERE rarity = 5 AND unit_type_id IN (1,2,3,4,5,6,7,8,9) AND smile_max != 1 AND release_tag IS NULL AND normal_icon_asset not like \"%rankup%\" and rank_max_icon_asset not like \"%normal%\"")->fetchAll(PDO::FETCH_ASSOC);
batchInsert($ssr, 2, 5);

/*一般生招募-μ's*/
$secretbox->query("DELETE FROM secret_box_unit_m WHERE secret_box_id = 1");
$n = $unit->query("SELECT unit_id FROM unit_m WHERE rarity = 1 AND smile_max != 1 AND release_tag IS NULL")->fetchAll(PDO::FETCH_ASSOC);
foreach($n as $i){
	$weight = in_array((int)$i['unit_id'], $event_card) ? 1 : 2;
	$secretbox->query("INSERT INTO secret_box_unit_m (secret_box_id, unit_group_id, unit_id, weight, start_date) VALUES(1, 1, ".$i['unit_id'].", ".$weight.", '".date("Y-m-d H:i:s", time())."')");
}
batchInsert($r, 1, 2);

/*机票*/
$secretbox->query("DELETE FROM secret_box_unit_m WHERE secret_box_id = 22");
batchInsert($ur, 22, 4);
foreach($sr as $i){
	$weight = in_array((int)$i['unit_id'], $event_card) ? 1 : 5;
	$secretbox->query("INSERT INTO secret_box_unit_m (secret_box_id, unit_group_id, unit_id, weight, start_date) VALUES(22, 3, ".$i['unit_id'].", ".$weight.", '".date("Y-m-d H:i:s", time())."')");
}
/*SR以上*/
$secretbox->query("DELETE FROM secret_box_unit_m WHERE secret_box_id = 64");
foreach($sr as $i){
	$weight = in_array((int)$i['unit_id'], $event_card) ? 1 : 5;
	$secretbox->query("INSERT INTO secret_box_unit_m (secret_box_id, unit_group_id, unit_id, weight, start_date) VALUES(64, 3, ".$i['unit_id'].", ".$weight.", '".date("Y-m-d H:i:s", time())."')");
}
batchInsert($ur, 64, 4);
batchInsert($ssr, 64, 5);

/*SSR以上*/
$secretbox->query("DELETE FROM secret_box_unit_m WHERE secret_box_id = 66");
batchInsert($ur, 66, 4);
batchInsert($ssr, 66, 5);

/*一年级*/
$secretbox->query("DELETE FROM secret_box_unit_m WHERE secret_box_id = 4");
$r_grade1 = $unit->query("SELECT unit_id FROM unit_m WHERE rarity = 2 AND unit_type_id IN (5,6,8) AND smile_max != 1 AND release_tag IS NULL AND normal_icon_asset not like \"%rankup%\" and rank_max_icon_asset not like \"%normal%\"")->fetchAll(PDO::FETCH_ASSOC);
$sr_grade1 = $unit->query("SELECT unit_id FROM unit_m WHERE rarity = 3 AND unit_type_id IN (5,6,8) AND smile_max != 1 AND release_tag IS NULL AND normal_icon_asset not like \"%rankup%\" and rank_max_icon_asset not like \"%normal%\"")->fetchAll(PDO::FETCH_ASSOC);
foreach($sr_grade1 as $i){
	$weight = in_array((int)$i['unit_id'], $event_card) ? 1 : 5;
	$secretbox->query("INSERT INTO secret_box_unit_m (secret_box_id, unit_group_id, unit_id, weight, start_date) VALUES(4, 3, ".$i['unit_id'].", ".$weight.", '".date("Y-m-d H:i:s", time())."')");
}
$ssr_grade1 = $unit->query("SELECT unit_id FROM unit_m WHERE rarity = 5 AND unit_type_id IN (5,6,8) AND smile_max != 1 AND release_tag IS NULL AND normal_icon_asset not like \"%rankup%\" and rank_max_icon_asset not like \"%normal%\"")->fetchAll(PDO::FETCH_ASSOC);
$ur_grade1 = $unit->query("SELECT unit_id FROM unit_m WHERE rarity = 4 AND unit_type_id IN (5,6,8) AND smile_max != 1 AND release_tag IS NULL AND normal_icon_asset not like \"%rankup%\" and rank_max_icon_asset not like \"%normal%\"")->fetchAll(PDO::FETCH_ASSOC);
batchInsert($r_grade1, 4, 2);
batchInsert($ssr_grade1, 4, 5);
batchInsert($ur_grade1, 4, 4);

/*二年级*/
$secretbox->query("DELETE FROM secret_box_unit_m WHERE secret_box_id = 6");
$r_grade1 = $unit->query("SELECT unit_id FROM unit_m WHERE rarity = 2 AND unit_type_id IN (1,3,4) AND smile_max != 1 AND release_tag IS NULL AND normal_icon_asset not like \"%rankup%\" and rank_max_icon_asset not like \"%normal%\"")->fetchAll(PDO::FETCH_ASSOC);
$sr_grade1 = $unit->query("SELECT unit_id FROM unit_m WHERE rarity = 3 AND unit_type_id IN (1,3,4) AND smile_max != 1 AND release_tag IS NULL AND normal_icon_asset not like \"%rankup%\" and rank_max_icon_asset not like \"%normal%\"")->fetchAll(PDO::FETCH_ASSOC);
foreach($sr_grade1 as $i){
	$weight = in_array((int)$i['unit_id'], $event_card) ? 1 : 5;
	$secretbox->query("INSERT INTO secret_box_unit_m (secret_box_id, unit_group_id, unit_id, weight, start_date) VALUES(6, 3, ".$i['unit_id'].", ".$weight.", '".date("Y-m-d H:i:s", time())."')");
}
$ssr_grade1 = $unit->query("SELECT unit_id FROM unit_m WHERE rarity = 5 AND unit_type_id IN (1,3,4) AND smile_max != 1 AND release_tag IS NULL AND normal_icon_asset not like \"%rankup%\" and rank_max_icon_asset not like \"%normal%\"")->fetchAll(PDO::FETCH_ASSOC);
$ur_grade1 = $unit->query("SELECT unit_id FROM unit_m WHERE rarity = 4 AND unit_type_id IN (1,3,4) AND smile_max != 1 AND release_tag IS NULL AND normal_icon_asset not like \"%rankup%\" and rank_max_icon_asset not like \"%normal%\"")->fetchAll(PDO::FETCH_ASSOC);
batchInsert($r_grade1, 6, 2);
batchInsert($ssr_grade1, 6, 5);
batchInsert($ur_grade1, 6, 4);

/*三年级*/
$secretbox->query("DELETE FROM secret_box_unit_m WHERE secret_box_id = 8");
$r_grade1 = $unit->query("SELECT unit_id FROM unit_m WHERE rarity = 2 AND unit_type_id IN (2,7,9) AND smile_max != 1 AND release_tag IS NULL AND normal_icon_asset not like \"%rankup%\" and rank_max_icon_asset not like \"%normal%\"")->fetchAll(PDO::FETCH_ASSOC);
$sr_grade1 = $unit->query("SELECT unit_id FROM unit_m WHERE rarity = 3 AND unit_type_id IN (2,7,9) AND smile_max != 1 AND release_tag IS NULL AND normal_icon_asset not like \"%rankup%\" and rank_max_icon_asset not like \"%normal%\"")->fetchAll(PDO::FETCH_ASSOC);
foreach($sr_grade1 as $i){
	$weight = in_array((int)$i['unit_id'], $event_card) ? 1 : 5;
	$secretbox->query("INSERT INTO secret_box_unit_m (secret_box_id, unit_group_id, unit_id, weight, start_date) VALUES(8, 3, ".$i['unit_id'].", ".$weight.", '".date("Y-m-d H:i:s", time())."')");
}
$ssr_grade1 = $unit->query("SELECT unit_id FROM unit_m WHERE rarity = 5 AND unit_type_id IN (2,7,9) AND smile_max != 1 AND release_tag IS NULL AND normal_icon_asset not like \"%rankup%\" and rank_max_icon_asset not like \"%normal%\"")->fetchAll(PDO::FETCH_ASSOC);
$ur_grade1 = $unit->query("SELECT unit_id FROM unit_m WHERE rarity = 4 AND unit_type_id IN (2,7,9) AND smile_max != 1 AND release_tag IS NULL AND normal_icon_asset not like \"%rankup%\" and rank_max_icon_asset not like \"%normal%\"")->fetchAll(PDO::FETCH_ASSOC);
batchInsert($r_grade1, 8, 2);
batchInsert($ssr_grade1, 8, 5);
batchInsert($ur_grade1, 8, 4);

/*llw*/
$secretbox->query("DELETE FROM secret_box_unit_m WHERE secret_box_id = 16");
$r_grade1 = $unit->query("SELECT unit_id FROM unit_m WHERE rarity = 2 AND unit_type_id IN (4,5,7) AND smile_max != 1 AND release_tag IS NULL AND normal_icon_asset not like \"%rankup%\" and rank_max_icon_asset not like \"%normal%\"")->fetchAll(PDO::FETCH_ASSOC);
$sr_grade1 = $unit->query("SELECT unit_id FROM unit_m WHERE rarity = 3 AND unit_type_id IN (4,5,7) AND smile_max != 1 AND release_tag IS NULL AND normal_icon_asset not like \"%rankup%\" and rank_max_icon_asset not like \"%normal%\"")->fetchAll(PDO::FETCH_ASSOC);
foreach($sr_grade1 as $i){
	$weight = in_array((int)$i['unit_id'], $event_card) ? 1 : 5;
	$secretbox->query("INSERT INTO secret_box_unit_m (secret_box_id, unit_group_id, unit_id, weight, start_date) VALUES(16, 3, ".$i['unit_id'].", ".$weight.", '".date("Y-m-d H:i:s", time())."')");
}
$ssr_grade1 = $unit->query("SELECT unit_id FROM unit_m WHERE rarity = 5 AND unit_type_id IN (4,5,7) AND smile_max != 1 AND release_tag IS NULL AND normal_icon_asset not like \"%rankup%\" and rank_max_icon_asset not like \"%normal%\"")->fetchAll(PDO::FETCH_ASSOC);
$ur_grade1 = $unit->query("SELECT unit_id FROM unit_m WHERE rarity = 4 AND unit_type_id IN (4,5,7) AND smile_max != 1 AND release_tag IS NULL AND normal_icon_asset not like \"%rankup%\" and rank_max_icon_asset not like \"%normal%\"")->fetchAll(PDO::FETCH_ASSOC);
batchInsert($r_grade1, 16, 2);
batchInsert($ssr_grade1, 16, 5);
batchInsert($ur_grade1, 16, 4);

/*Printemps*/
$secretbox->query("DELETE FROM secret_box_unit_m WHERE secret_box_id = 18");
$r_grade1 = $unit->query("SELECT unit_id FROM unit_m WHERE rarity = 2 AND unit_type_id IN (1,3,8) AND smile_max != 1 AND release_tag IS NULL AND normal_icon_asset not like \"%rankup%\" and rank_max_icon_asset not like \"%normal%\"")->fetchAll(PDO::FETCH_ASSOC);
$sr_grade1 = $unit->query("SELECT unit_id FROM unit_m WHERE rarity = 3 AND unit_type_id IN (1,3,8) AND smile_max != 1 AND release_tag IS NULL AND normal_icon_asset not like \"%rankup%\" and rank_max_icon_asset not like \"%normal%\"")->fetchAll(PDO::FETCH_ASSOC);
foreach($sr_grade1 as $i){
	$weight = in_array((int)$i['unit_id'], $event_card) ? 1 : 5;
	$secretbox->query("INSERT INTO secret_box_unit_m (secret_box_id, unit_group_id, unit_id, weight, start_date) VALUES(18, 3, ".$i['unit_id'].", ".$weight.", '".date("Y-m-d H:i:s", time())."')");
}
$ssr_grade1 = $unit->query("SELECT unit_id FROM unit_m WHERE rarity = 5 AND unit_type_id IN (1,3,8) AND smile_max != 1 AND release_tag IS NULL AND normal_icon_asset not like \"%rankup%\" and rank_max_icon_asset not like \"%normal%\"")->fetchAll(PDO::FETCH_ASSOC);
$ur_grade1 = $unit->query("SELECT unit_id FROM unit_m WHERE rarity = 4 AND unit_type_id IN (1,3,8) AND smile_max != 1 AND release_tag IS NULL AND normal_icon_asset not like \"%rankup%\" and rank_max_icon_asset not like \"%normal%\"")->fetchAll(PDO::FETCH_ASSOC);
batchInsert($r_grade1, 18, 2);
batchInsert($ssr_grade1, 18, 5);
batchInsert($ur_grade1, 18, 4);

/*bibi*/
$secretbox->query("DELETE FROM secret_box_unit_m WHERE secret_box_id = 20");
$r_grade1 = $unit->query("SELECT unit_id FROM unit_m WHERE rarity = 2 AND unit_type_id IN (2,6,9) AND smile_max != 1 AND release_tag IS NULL AND normal_icon_asset not like \"%rankup%\" and rank_max_icon_asset not like \"%normal%\"")->fetchAll(PDO::FETCH_ASSOC);
$sr_grade1 = $unit->query("SELECT unit_id FROM unit_m WHERE rarity = 3 AND unit_type_id IN (2,6,9) AND smile_max != 1 AND release_tag IS NULL AND normal_icon_asset not like \"%rankup%\" and rank_max_icon_asset not like \"%normal%\"")->fetchAll(PDO::FETCH_ASSOC);
foreach($sr_grade1 as $i){
	$weight = in_array((int)$i['unit_id'], $event_card) ? 1 : 5;
	$secretbox->query("INSERT INTO secret_box_unit_m (secret_box_id, unit_group_id, unit_id, weight, start_date) VALUES(20, 3, ".$i['unit_id'].", ".$weight.", '".date("Y-m-d H:i:s", time())."')");
}
$ssr_grade1 = $unit->query("SELECT unit_id FROM unit_m WHERE rarity = 5 AND unit_type_id IN (2,6,9) AND smile_max != 1 AND release_tag IS NULL AND normal_icon_asset not like \"%rankup%\" and rank_max_icon_asset not like \"%normal%\"")->fetchAll(PDO::FETCH_ASSOC);
$ur_grade1 = $unit->query("SELECT unit_id FROM unit_m WHERE rarity = 4 AND unit_type_id IN (2,6,9) AND smile_max != 1 AND release_tag IS NULL AND normal_icon_asset not like \"%rankup%\" and rank_max_icon_asset not like \"%normal%\"")->fetchAll(PDO::FETCH_ASSOC);
batchInsert($r_grade1, 20, 2);
batchInsert($ssr_grade1, 20, 5);
batchInsert($ur_grade1, 20, 4);

/*SSR·UR*/
$secretbox->query("DELETE FROM secret_box_unit_m WHERE secret_box_id = 69");
batchInsert($ur, 69, 4);
batchInsert($ssr, 69, 5);

/*UR限定*/
$secretbox->query("DELETE FROM secret_box_unit_m WHERE secret_box_id = 70");
batchInsert($ur, 70, 4);

/*------------------------------------Aqours------------------------------*/
/*优等生招募-Aqours*/
$secretbox->query("DELETE FROM secret_box_unit_m WHERE secret_box_id = 62");
$r = $unit->query("SELECT unit_id FROM unit_m WHERE rarity = 2 AND unit_type_id IN (101,102,103,104,105,106,107,108,109) AND smile_max != 1 AND release_tag IS NULL AND normal_icon_asset not like \"%rankup%\" and rank_max_icon_asset not like \"%normal%\"")->fetchAll(PDO::FETCH_ASSOC);
batchInsert($r, 62, 2);

$sr = $unit->query("SELECT unit_id FROM unit_m WHERE rarity = 3 AND unit_type_id IN (101,102,103,104,105,106,107,108,109) AND smile_max != 1 AND release_tag IS NULL AND normal_icon_asset not like \"%rankup%\" and rank_max_icon_asset not like \"%normal%\"")->fetchAll(PDO::FETCH_ASSOC);
foreach($sr as $i){
	$weight = in_array((int)$i['unit_id'], $event_card) ? 1 : 5;
	$secretbox->query("INSERT INTO secret_box_unit_m (secret_box_id, unit_group_id, unit_id, weight, start_date) VALUES(62, 3, ".$i['unit_id'].", ".$weight.", '".date("Y-m-d H:i:s", time())."')");
}

$ur = $unit->query("SELECT unit_id FROM unit_m WHERE rarity = 4 AND unit_type_id IN (101,102,103,104,105,106,107,108,109) AND smile_max != 1 AND release_tag IS NULL AND normal_icon_asset not like \"%rankup%\" and rank_max_icon_asset not like \"%normal%\"")->fetchAll(PDO::FETCH_ASSOC);
batchInsert($ur, 62, 4);

$ssr = $unit->query("SELECT unit_id FROM unit_m WHERE rarity = 5 AND unit_type_id IN (101,102,103,104,105,106,107,108,109) AND smile_max != 1 AND release_tag IS NULL AND normal_icon_asset not like \"%rankup%\" and rank_max_icon_asset not like \"%normal%\"")->fetchAll(PDO::FETCH_ASSOC);
batchInsert($ssr, 62, 5);

/*一般生招募-Aqours*/
$secretbox->query("DELETE FROM secret_box_unit_m WHERE secret_box_id = 61");
$n = $unit->query("SELECT unit_id FROM unit_m WHERE rarity = 1 AND smile_max != 1 AND release_tag IS NULL")->fetchAll(PDO::FETCH_ASSOC);
foreach($n as $i){
	$weight = in_array((int)$i['unit_id'], $event_card) ? 1 : 2;
	$secretbox->query("INSERT INTO secret_box_unit_m (secret_box_id, unit_group_id, unit_id, weight, start_date) VALUES(61, 1, ".$i['unit_id'].", ".$weight.", '".date("Y-m-d H:i:s", time())."')");
}
batchInsert($r, 61, 2);

/*SR以上*/
$secretbox->query("DELETE FROM secret_box_unit_m WHERE secret_box_id = 63");
foreach($sr as $i){
	$weight = in_array((int)$i['unit_id'], $event_card) ? 1 : 5;
	$secretbox->query("INSERT INTO secret_box_unit_m (secret_box_id, unit_group_id, unit_id, weight, start_date) VALUES(63, 3, ".$i['unit_id'].", ".$weight.", '".date("Y-m-d H:i:s", time())."')");
}
batchInsert($ur, 63, 4);
batchInsert($ssr, 63, 5);

/*SSR以上*/
$secretbox->query("DELETE FROM secret_box_unit_m WHERE secret_box_id = 65");
batchInsert($ur, 65, 4);
batchInsert($ssr, 65, 5);

/*机票*/
$secretbox->query("DELETE FROM secret_box_unit_m WHERE secret_box_id = 71");
batchInsert($ur, 71, 4);
foreach($sr as $i){
	$weight = in_array((int)$i['unit_id'], $event_card) ? 1 : 5;
	$secretbox->query("INSERT INTO secret_box_unit_m (secret_box_id, unit_group_id, unit_id, weight, start_date) VALUES(71, 3, ".$i['unit_id'].", ".$weight.", '".date("Y-m-d H:i:s", time())."')");
}

/*SSR·UR*/
$secretbox->query("DELETE FROM secret_box_unit_m WHERE secret_box_id = 72");
batchInsert($ur, 72, 4);
batchInsert($ssr, 72, 5);

/*UR限定*/
$secretbox->query("DELETE FROM secret_box_unit_m WHERE secret_box_id = 73");
batchInsert($ur, 73, 4);
$secretbox->query("COMMIT");


/*cyaron*/
$secretbox->query("DELETE FROM secret_box_unit_m WHERE secret_box_id = 74");
$r_grade1 = $unit->query("SELECT unit_id FROM unit_m WHERE rarity = 2 AND unit_type_id IN (101,105,109) AND smile_max != 1 AND release_tag IS NULL AND normal_icon_asset not like \"%rankup%\" and rank_max_icon_asset not like \"%normal%\"")->fetchAll(PDO::FETCH_ASSOC);
$sr_grade1 = $unit->query("SELECT unit_id FROM unit_m WHERE rarity = 3 AND unit_type_id IN (101,105,109) AND smile_max != 1 AND release_tag IS NULL AND normal_icon_asset not like \"%rankup%\" and rank_max_icon_asset not like \"%normal%\"")->fetchAll(PDO::FETCH_ASSOC);
foreach($sr_grade1 as $i){
	$weight = in_array((int)$i['unit_id'], $event_card) ? 1 : 5;
	$secretbox->query("INSERT INTO secret_box_unit_m (secret_box_id, unit_group_id, unit_id, weight, start_date) VALUES(74, 3, ".$i['unit_id'].", ".$weight.", '".date("Y-m-d H:i:s", time())."')");
}
$ssr_grade1 = $unit->query("SELECT unit_id FROM unit_m WHERE rarity = 5 AND unit_type_id IN (101,105,109) AND smile_max != 1 AND release_tag IS NULL AND normal_icon_asset not like \"%rankup%\" and rank_max_icon_asset not like \"%normal%\"")->fetchAll(PDO::FETCH_ASSOC);
$ur_grade1 = $unit->query("SELECT unit_id FROM unit_m WHERE rarity = 4 AND unit_type_id IN (101,105,109) AND smile_max != 1 AND release_tag IS NULL AND normal_icon_asset not like \"%rankup%\" and rank_max_icon_asset not like \"%normal%\"")->fetchAll(PDO::FETCH_ASSOC);
batchInsert($r_grade1, 74, 2);
batchInsert($ssr_grade1, 74, 5);
batchInsert($ur_grade1, 74, 4);

/*Printemps*/
$secretbox->query("DELETE FROM secret_box_unit_m WHERE secret_box_id = 75");
$r_grade1 = $unit->query("SELECT unit_id FROM unit_m WHERE rarity = 2 AND unit_type_id IN (103,104,107) AND smile_max != 1 AND release_tag IS NULL AND normal_icon_asset not like \"%rankup%\" and rank_max_icon_asset not like \"%normal%\"")->fetchAll(PDO::FETCH_ASSOC);
$sr_grade1 = $unit->query("SELECT unit_id FROM unit_m WHERE rarity = 3 AND unit_type_id IN (103,104,107) AND smile_max != 1 AND release_tag IS NULL AND normal_icon_asset not like \"%rankup%\" and rank_max_icon_asset not like \"%normal%\"")->fetchAll(PDO::FETCH_ASSOC);
foreach($sr_grade1 as $i){
	$weight = in_array((int)$i['unit_id'], $event_card) ? 1 : 5;
	$secretbox->query("INSERT INTO secret_box_unit_m (secret_box_id, unit_group_id, unit_id, weight, start_date) VALUES(75, 3, ".$i['unit_id'].", ".$weight.", '".date("Y-m-d H:i:s", time())."')");
}
$ssr_grade1 = $unit->query("SELECT unit_id FROM unit_m WHERE rarity = 5 AND unit_type_id IN (103,104,107) AND smile_max != 1 AND release_tag IS NULL AND normal_icon_asset not like \"%rankup%\" and rank_max_icon_asset not like \"%normal%\"")->fetchAll(PDO::FETCH_ASSOC);
$ur_grade1 = $unit->query("SELECT unit_id FROM unit_m WHERE rarity = 4 AND unit_type_id IN (103,104,107) AND smile_max != 1 AND release_tag IS NULL AND normal_icon_asset not like \"%rankup%\" and rank_max_icon_asset not like \"%normal%\"")->fetchAll(PDO::FETCH_ASSOC);
batchInsert($r_grade1, 75, 2);
batchInsert($ssr_grade1, 75, 5);
batchInsert($ur_grade1, 75, 4);

/*bibi*/
$secretbox->query("DELETE FROM secret_box_unit_m WHERE secret_box_id = 76");
$r_grade1 = $unit->query("SELECT unit_id FROM unit_m WHERE rarity = 2 AND unit_type_id IN (102,106,108) AND smile_max != 1 AND release_tag IS NULL AND normal_icon_asset not like \"%rankup%\" and rank_max_icon_asset not like \"%normal%\"")->fetchAll(PDO::FETCH_ASSOC);
$sr_grade1 = $unit->query("SELECT unit_id FROM unit_m WHERE rarity = 3 AND unit_type_id IN (102,106,108) AND smile_max != 1 AND release_tag IS NULL AND normal_icon_asset not like \"%rankup%\" and rank_max_icon_asset not like \"%normal%\"")->fetchAll(PDO::FETCH_ASSOC);
foreach($sr_grade1 as $i){
	$weight = in_array((int)$i['unit_id'], $event_card) ? 1 : 5;
	$secretbox->query("INSERT INTO secret_box_unit_m (secret_box_id, unit_group_id, unit_id, weight, start_date) VALUES(76, 3, ".$i['unit_id'].", ".$weight.", '".date("Y-m-d H:i:s", time())."')");
}
$ssr_grade1 = $unit->query("SELECT unit_id FROM unit_m WHERE rarity = 5 AND unit_type_id IN (102,106,108) AND smile_max != 1 AND release_tag IS NULL AND normal_icon_asset not like \"%rankup%\" and rank_max_icon_asset not like \"%normal%\"")->fetchAll(PDO::FETCH_ASSOC);
$ur_grade1 = $unit->query("SELECT unit_id FROM unit_m WHERE rarity = 4 AND unit_type_id IN (102,106,108) AND smile_max != 1 AND release_tag IS NULL AND normal_icon_asset not like \"%rankup%\" and rank_max_icon_asset not like \"%normal%\"")->fetchAll(PDO::FETCH_ASSOC);
batchInsert($r_grade1, 76, 2);
batchInsert($ssr_grade1, 76, 5);
batchInsert($ur_grade1, 76, 4);

print("Complete");