<?php
//reward.php 礼物
require_once 'includes/unit.php';
//处理客户端发送的category和filter
function getfilter($category, $filter) {
	$ret = '';
	switch($category) {
	case 1: $ret = ' and is_card=1';break;
	case 2: $ret = ' and is_card!=1';
	}
	$add_type_to_item_id = [3000=>3, 3001=>4, 3002=>2];
	if (is_array($filter)) {
		$filter = $filter[0];
	}
	if($filter > 4) {
		$ret .= ' and incentive_item_id='.$add_type_to_item_id[$filter];
	} else if ($filter != 0 && $filter <= 4) {
		$unit = getUnitDb();
		$unit_id_list = $unit->query('SELECT unit_id FROM unit_m WHERE rarity='.$filter)->fetchAll(PDO::FETCH_COLUMN, 0);
		$ret .= ' and incentive_item_id in ('.implode(', ', $unit_id_list).')';
	}
	return $ret;
}

//读取未领取和已领取礼物的共用代码
function getRewardList($post, $history) {
	if ($history) {
		$array_name = 'history';
		$unset = 'remaining_time';
	} else {
		$array_name = 'items';
		$unset = 'opened_date';
	}
	global $uid, $mysql;
	$filter = getfilter($post['category'], $post['filter']);
	$res = $mysql->query('SELECT * FROM incentive_list WHERE user_id='.$uid.' AND opened_date'.($history?'!=':'=').'0'.$filter.' ORDER BY incentive_id DESC')->fetchAll();
	$ret['item_count'] = count($res);
	$ret[$array_name] = [];
	$correct_add_type = [1000, 3002, 3000, 3001, 1000];
	foreach ($res as $r) {
		foreach ($r as &$v) {
			if (is_numeric($v)) $v = (int)$v;
		}
		if ($r['is_card']) {
			$r['add_type'] = 1001;
		}else if($r['incentive_item_id'] == 0){
			$r['add_type'] = 3006;
		} else {
			$r['add_type'] = $correct_add_type[$r['incentive_item_id'] - 1];
		}
		$r['item_category_id'] = $r['incentive_item_id'];
		$r['remaining_time'] = '无限期';
		unset($r[$unset], $r['user_id'], $r['is_card']);
		$ret[$array_name][] = $r;
	}
	return $ret;
}

//reward/rewardList 读礼物列表
function reward_rewardList($post) {
	return getRewardList($post, false);
}

//reward/rewardHistory 读礼物历史
function reward_rewardHistory($post) {
	return getRewardList($post, true);
}

//reward/open //开一个礼物
function reward_open($post) {
	global $params, $mysql, $uid;
	$res = $mysql->query('SELECT incentive_id,incentive_item_id,is_card,amount FROM incentive_list WHERE incentive_id='.$post['incentive_id'].' and opened_date=0')->fetch(PDO::FETCH_ASSOC);
	if (empty($res)) {
		return [];
	}
	$ret['opened_num'] = 1;
	//$ret['order'] = $post['order'];
	$ret['success'] = [];
	$ret['bushimo_reward_info'] = [];
	$ret['unit_support_list'] = [];
	$correct_add_type = [1000, 3002, 3000, 3001, 1000];
	foreach($res as &$rr){
		if(is_numeric($rr)){
			$rr = (int)$rr;
		}
	}
	if(!$res['is_card']) {
		$res['item_id'] = $res['incentive_item_id'];
		$params['item' . $res['item_id']] += $res['amount'];
		$res['add_type'] = $correct_add_type[$res['item_id'] - 1];
		$res['item_category_id'] = 0;
		$res['reward_box_flag'] = false;
		unset($res['is_card']);
		$ret['success'][] = $res;
	} else {
		$mysql->query("INSERT INTO unit_list (user_id, unit_id, rank, exp, love, unit_skill_level, favorite_flag, display_rank) VALUES(".$uid.", ".$res['incentive_item_id'].",1,0,0,1,0,2)");
		$unit_owning_user_id = $mysql->query("SELECT unit_owning_user_id FROM unit_list WHERE user_id = ".$uid." AND unit_id = ".$res['incentive_item_id']." ORDER BY unit_owning_user_id desc")->fetch()['unit_owning_user_id'];
		$unit_detail = GetUnitDetail($unit_owning_user_id);
		if($res['incentive_item_id'] == 382){
			$res['is_support_member'] = true;
		}
		else{
			$res['is_support_member'] = false;
		}
		$res['item_category_id'] = 0;
		$res['reward_box_flag'] = false;
		$res['add_type'] = 1001;
		$res = array_merge($res, $unit_detail);
		unset($res['incentive_item_id'], $res['is_card'], $res['insert_date']);
		$ret['success'][] = $res;
	}
	$ret['opened_num']++;
	$mysql->exec('UPDATE incentive_list SET opened_date=CURRENT_TIMESTAMP WHERE incentive_id='.$res['incentive_id']);
	return $ret;
}

//reward/openAll //开所有礼物
function reward_openAll($post) {
	global $uid, $mysql, $params;
	$filter = getfilter($post['category'], $post['filter']);
	$res = $mysql->query('SELECT incentive_id,incentive_item_id,is_card,amount FROM incentive_list WHERE user_id='.$uid.' AND opened_date=0'.$filter)->fetchAll(PDO::FETCH_ASSOC);
	$ret['reward_num'] = count($res);
	$ret['opened_num'] = 0;
	$ret['total_num'] = $ret['reward_num'];
	$ret['order'] = $post['order'];
	$ret['upper_limit'] = false;
	$ret['reward_item_list'] = [];
	$ret['bushimo_reward_info'] = [];
	$ret['unit_support_list'] = [];
	$correct_add_type = [1000, 3002, 3000, 3001, 1000];
	foreach($res as $r) {
		foreach($r as &$rr){
			if(is_numeric($rr)){
				$rr = (int)$rr;
			}
		}
		if(!$r['is_card']) {
			$r['item_id'] = $r['incentive_item_id'];
			$params['item' . $r['item_id']] += $r['amount'];
			$r['add_type'] = $correct_add_type[$r['item_id'] - 1];
			$r['item_category_id'] = 0;
			$r['reward_box_flag'] = false;
			unset($r['is_card']);
			$ret['reward_item_list'][] = $r;
		} else {
			$mysql->query("INSERT INTO unit_list (user_id, unit_id, rank, exp, love, unit_skill_level, favorite_flag, display_rank) VALUES(".$uid.", ".$r['incentive_item_id'].",1,0,0,1,0,2)");
			$unit_owning_user_id = $mysql->query("SELECT unit_owning_user_id FROM unit_list WHERE user_id = ".$uid." AND unit_id = ".$r['incentive_item_id']." ORDER BY unit_owning_user_id desc")->fetch()['unit_owning_user_id'];
			$unit_detail = GetUnitDetail($unit_owning_user_id);
			if($r['incentive_item_id'] == 382){
				$r['is_support_member'] = true;
			}
			else{
				$r['is_support_member'] = false;
			}
			$r['item_category_id'] = 0;
			$r['reward_box_flag'] = false;
			$r['add_type'] = 1001;
			$r = array_merge($r, $unit_detail);
			unset($r['incentive_item_id'], $r['is_card'], $r['insert_date']);
			$ret['reward_item_list'][] = $r;
		}
		$ret['opened_num']++;
		$mysql->exec('UPDATE incentive_list SET opened_date=CURRENT_TIMESTAMP WHERE incentive_id='.$r['incentive_id']);
	}
	//$ret['total_num'] = $ret['reward_num'] - $ret['opened_num'];
	return $ret;
}

?>
