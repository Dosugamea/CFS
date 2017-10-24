<?php
//reward.php 礼物
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
	if($history)
		$filter .= " ORDER BY opened_date DESC";
	else if(isset($post['order']) && $post['order'])
		$filter .= " ORDER BY incentive_id ASC";
	else
		$filter .= " ORDER BY incentive_id DESC";
	$res = $mysql->query('SELECT * FROM incentive_list WHERE user_id='.$uid.' AND opened_date'.($history?'!=':'=').'0'.$filter)->fetchAll(PDO::FETCH_ASSOC);
	$ret['item_count'] = count($res);
	$ret[$array_name] = [];
	$correct_add_type = [1000, 3002, 3000, 3001, 1000, 1000, 1000, 1000, 1000, 1000, 1000, 1000, 1000, 1000];
	foreach ($res as $r) {
		foreach ($r as &$v) {
			if (is_numeric($v)) $v = (int)$v;
		}
		if ($r['is_card']) {
			$r['add_type'] = 1001;
			$r['item_category_id'] = $r['incentive_item_id'];
		}else if($r['incentive_item_id'] > 1000){
			$r['add_type'] = $r['incentive_item_id'];
			$r['item_category_id'] = 0;
			$r['incentive_type'] = 0; //物品获得来源，暂时空
			$r['incentive_item_id'] = $r['item_id'];
		}else {
			$r['add_type'] = $correct_add_type[$r['incentive_item_id'] - 1];
			$r['item_category_id'] = $r['incentive_item_id'];
		}
		$r['remaining_time'] = '无限期';
		if($history)
			$r['insert_date'] = $r['opened_date'];
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
	$res = $mysql->query('SELECT incentive_id,incentive_item_id,item_id,is_card,amount FROM incentive_list WHERE incentive_id='.$post['incentive_id'].' and opened_date=0')->fetch(PDO::FETCH_ASSOC);
	if (empty($res)) {
		return [];
	}
	$ret['opened_num'] = 1;
	//$ret['order'] = $post['order'];
	$ret['success'] = [];
	$ret['bushimo_reward_info'] = [];
	$ret['unit_support_list'] = [];
	$correct_add_type = [1000, 3002, 3000, 3001, 1000, 1000, 1000, 1000, 1000, 1000, 1000, 1000, 1000, 1000];
	foreach($res as &$rr){
		if(is_numeric($rr)){
			$rr = (int)$rr;
		}
	}
	if(!$res['is_card']) {
		if($res['incentive_item_id'] < 1000){
			$params['item'.$res['incentive_item_id']] += $res['amount'];
			$res['item_category_id'] = $res['incentive_item_id'];
			switch($res['incentive_item_id']){
				case 2:
					$res['add_type'] = 3002;break;
				case 3:
					$res['add_type'] = 3000;break;
				case 4:
					$res['add_type'] = 3001;break;
				default:
					$res['add_type'] = 1000;
			}
		}else{
			$res['add_type'] = $res['incentive_item_id'];
			switch($res['incentive_item_id']){
				case 3006://贴纸
					switch($res['item_id']){
						case 2:
							$params['seal1'] += $res['amount'];break;
						case 3:
							$params['seal2'] += $res['amount'];break;
						case 4:
							$params['seal4'] += $res['amount'];break;
						case 5:
							$params['seal3'] += $res['amount'];break;
						default:
							trigger_error("没有这样的技能宝石：".$res['item_id']);
					}break;
				case 5100://称号
					$mysql->query("INSERT IGNORE INTO award (user_id, award_id) VALUES(?, ?)", [$uid, $res['item_id']]);break;
				case 5500://技能宝石
					$skill_check = $mysql->query("SELECT * FROM removable_skill WHERE user_id = ? AND skill_id = ?", [$uid, $res['item_id']])->fetch(PDO::FETCH_ASSOC);
					if($skill_check)
						$mysql->query("UPDATE removable_skill SET amount = amount + ? WHERE user_id = ? AND skill_id = ?", [$res['amount'], $uid, $res['item_id']]);
					else
						$mysql->query("INSERT INTO removable_skill (user_id, skill_id, amount) VALUES (?, ?, ?)", [$uid, $res['item_id'], $res['amount']]);
					break;
				default:
					trigger_error("未定义该物品的追加方式！");
			}
			//$res['incentive_item_id'] = $res['item_id'];
			//unset($res['item_id']);
			$res['item_category_id'] = 0;
			$res['reward_box_flag'] = false;
			unset($res['is_card']);
		}
		$ret['success'][] = $res;
	} else {
		for($i = 0;$i < $res['amount']; $i++){
			$support_list = getSupportUnitList();
			if(in_array($res['incentive_item_id'],$support_list)){
				$unit_detail = addUnit($res['incentive_item_id'], 1, true);
				$res['is_support_member'] = true;
			}else{
				$unit_detail = addUnit($res['incentive_item_id'], 1, true);
				$res['is_support_member'] = false;	
			}
			$res['item_category_id'] = 0;
			$res['reward_box_flag'] = false;
			$res['add_type'] = 1001;
			$res_ = array_merge($res, $unit_detail[0]);
			unset($res_['incentive_item_id'], $res_['is_card'], $res_['insert_date'], $res_['item_id']);
			$ret['success'][] = $res_;
		}
	}
	include_once("../modules/unit.php");
	$ret['unit_support_list'] = unit_supporterAll()['unit_support_list'];
	//$ret['opened_num']++;
	$mysql->exec('UPDATE incentive_list SET opened_date=CURRENT_TIMESTAMP WHERE incentive_id='.$res['incentive_id']);
	return $ret;
}

//reward/openAll //开所有礼物
function reward_openAll($post) {
	global $uid, $mysql, $params;
	$filter = getfilter($post['category'], $post['filter']);
	$res = $mysql->query('SELECT incentive_id,incentive_item_id,item_id,is_card,amount FROM incentive_list WHERE user_id='.$uid.' AND opened_date=0'.$filter)->fetchAll(PDO::FETCH_ASSOC);
	$ret['reward_num'] = count($res);
	$ret['opened_num'] = 0;
	$ret['total_num'] = $ret['reward_num'];
	$ret['order'] = $post['order'];
	$ret['upper_limit'] = false;
	$ret['reward_item_list'] = [];
	$ret['bushimo_reward_info'] = [];
	$ret['unit_support_list'] = [];
	$correct_add_type = [1000, 3002, 3000, 3001, 1000];
	foreach($res as &$r) {
		foreach($r as &$rr){
			if(is_numeric($rr)){
				$rr = (int)$rr;
			}
		}
		if(!$r['is_card']) {
			if($r['incentive_item_id'] < 1000){
				$params['item'.$r['incentive_item_id']] += $r['amount'];
				$r['item_category_id'] = $r['incentive_item_id'];
				switch($r['incentive_item_id']){
					case 2:
						$r['add_type'] = 3002;break;
					case 3:
						$r['add_type'] = 3000;break;
					case 4:
						$r['add_type'] = 3001;break;
					default:
						$r['add_type'] = 1000;break;
				}
			}else{
				$r['add_type'] = $r['incentive_item_id'];
				switch($r['incentive_item_id']){
					case 3006://贴纸
						switch($r['item_id']){
							case 2:
								$params['seal1'] += $r['amount'];break;
							case 3:
								$params['seal2'] += $r['amount'];break;
							case 4:
								$params['seal4'] += $r['amount'];break;
							case 5:
								$params['seal3'] += $r['amount'];break;
							default:
								trigger_error("没有这样的技能宝石：".$r['item_id']);
						}break;
					case 5100://称号
						$mysql->query("INSERT IGNORE INTO award (user_id, award_id) VALUES(?, ?)", [$uid, $r['item_id']]);break;
					case 5500://技能宝石
						$skill_check = $mysql->query("SELECT * FROM removable_skill WHERE user_id = ? AND skill_id = ?", [$uid, $r['item_id']])->fetch(PDO::FETCH_ASSOC);
						if($skill_check)
							$mysql->query("UPDATE removable_skill SET amount = amount + ? WHERE user_id = ? AND skill_id = ?", [$r['amount'], $uid, $r['item_id']]);
						else
							$mysql->query("INSERT INTO removable_skill (user_id, skill_id, amount) VALUES (?, ?, ?)", [$uid, $r['item_id'], $r['amount']]);
						break;
					default:
						trigger_error("未定义该物品的追加方式！");
				}
				$r['incentive_item_id'] = $r['item_id'];
				//unset($r['item_id']);
				$r['item_category_id'] = 0;
				$r['reward_box_flag'] = false;
				unset($r['is_card']);
			}
			$ret['reward_item_list'][] = $r;
		} else {
			for($i = 0;$i < $r['amount']; $i++){
				$support_list = getSupportUnitList();
				if(in_array($r['incentive_item_id'],$support_list)){
					$unit_detail = addUnit($r['incentive_item_id'], 1, true)[0];
					$r['is_support_member'] = true;
				}else{
					$unit_detail = addUnit($r['incentive_item_id'], 1, true)[0];
					$r['is_support_member'] = false;
				}
				$r['item_category_id'] = 0;
				$r['reward_box_flag'] = false;
				$r['add_type'] = 1001;
				$r_ = array_merge($r, $unit_detail);
				$r_['amount'] = 1;
				unset($r_['incentive_item_id'], $r_['is_card'], $r_['insert_date']);
				$ret['reward_item_list'][] = $r_;
			}
		}
		$ret['opened_num']++;
		$mysql->exec('UPDATE incentive_list SET opened_date=CURRENT_TIMESTAMP WHERE incentive_id='.$r['incentive_id']);
	}
	include_once("../modules/unit.php");
	$ret['unit_support_list'] = unit_supporterAll()['unit_support_list'];
	//$ret['total_num'] = $ret['reward_num'] - $ret['opened_num'];
	return $ret;
}

?>
