<?php 
function addUnit($unit_id, $cnt = 1, $detail = false) {
	global $uid, $mysql;
	$unit = getUnitDb();
	$default_rankup = $unit->query('select unit_id from unit_m where unit_id=? and (disable_rank_up=1 or normal_icon_asset like "%rankup%")', [$unit_id])->fetch();
	if ($default_rankup) {
		$mysql->query('insert ignore into album (user_id, unit_id, rank_max_flag) values (?, ?, 1)', [$uid, $unit_id]);
		$sql = $mysql->prepare('insert into unit_list (user_id, unit_id, rank) values (?, ?, 2)');
	} else {
		$mysql->query('insert ignore into album (user_id, unit_id) values (?, ?)', [$uid, $unit_id]);
		$sql = $mysql->prepare('insert into unit_list (user_id, unit_id) values (?, ?)');
	}
	for ($i = 0; $i < $cnt; $i++) {
		$sql->execute([$uid, $unit_id]);
		$ret[] = GetUnitDetail($mysql->lastInsertId(), $detail);
	}
	return $ret;
}

//GetUnitDetail函数：获取指定卡片的详细信息。客户端不会调用此函数。
function GetUnitDetail($unit_owning_user_id, $return_attr_value = false, $preload_all = false) {
	if (!$unit_owning_user_id) {
		return array();
	}
	$noarray = false;
	if (!is_array($unit_owning_user_id)) {
		$noarray = true;
		$unit_owning_user_id = [$unit_owning_user_id];
	}
	global $mysql;
	$unit = getUnitDb();
	$ret2 = array();
	$res = $mysql->query('SELECT * FROM unit_list WHERE unit_owning_user_id in(' . implode(', ', $unit_owning_user_id) . ')');
	if ($preload_all) {
		$card_cache = array();
		$level_cache = array();
		foreach ($unit->query('SELECT * FROM unit_m')->fetchAll() as $v) {
			$card_cache[$v['unit_id']] = $v;
		}
		foreach ($unit->query('SELECT * FROM unit_level_up_pattern_m order by unit_level_up_pattern_id, unit_level')->fetchAll() as $v) {
			if (!isset($level_cache[$v['unit_level_up_pattern_id']])) {
				$level_cache[$v['unit_level_up_pattern_id']] = array();
			}
			$level_cache[$v['unit_level_up_pattern_id']][] = $v;
		}
	}
	while ($ret = $res->fetch(PDO::FETCH_ASSOC)) {
		if ($preload_all) {
			$card = $card_cache[$ret['unit_id']];
			if (!isset($level_cache[$card['unit_level_up_pattern_id']])) {
				$level = ['unit_level' => 1, 'next_exp' => 0, 'hp_diff' => 0, 'smile_diff' => 0, 'pure_diff' => 0, 'cool_diff' => 0];
			} else {
				foreach ($level_cache[$card['unit_level_up_pattern_id']] as $v) {
					if ($v['next_exp'] > $ret['exp'] || $v['next_exp'] == 0) {
						$level = $v;
						break;
					}
				}
			}
		} else {
			$card = $unit->query('SELECT * FROM unit_m WHERE unit_id=' . $ret['unit_id'])->fetch();
			$level = $unit->query('SELECT * FROM unit_level_up_pattern_m WHERE unit_level_up_pattern_id=' . $card['unit_level_up_pattern_id'] . ' and next_exp>' . $ret['exp'] . ' limit 1')->fetch();
			if (empty($level)) {
				$level = $unit->query('SELECT * FROM unit_level_up_pattern_m WHERE unit_level_up_pattern_id=' . $card['unit_level_up_pattern_id'] . ' and next_exp=0')->fetch();
			}
			if (empty($level)) {
				$level = ['unit_level' => 1, 'next_exp' => 0, 'hp_diff' => 0, 'smile_diff' => 0, 'pure_diff' => 0, 'cool_diff' => 0];
			}
		}
		$ret['favorite_flag'] = (bool) $ret['favorite_flag'];
		$ret['level'] = $level['unit_level'];
		$ret['max_hp'] = $card['hp_max'];
		$ret['max_level'] = $ret['rank'] == 1 ? $card['before_level_max'] : $card['after_level_max'];
		$ret['max_love'] = $ret['rank'] == 1 ? $card['before_love_max'] : $card['after_love_max'];
		$ret['max_rank'] = $card['disable_rank_up'] ? 1 : 2;
		$ret['is_level_max'] = $ret['level'] == $card['after_level_max'];
		$ret['is_love_max'] = $ret['love'] == $card['after_love_max'];
		$ret['is_rank_max'] = $ret['rank'] == $ret['max_rank'];
		$ret['is_skill_level_max'] = $ret['unit_skill_level'] == 8;
		$ret['next_exp'] = $level['next_exp'];
		$ret['center_skill'] = $card['default_leader_skill_id'];
		$ret['rarity'] = (int) $card['rarity'];
		$ret['display_rank'] = min($ret['rank'], $ret['display_rank']);
		if ($return_attr_value) {
			$ret['hp'] = $card['hp_max'] - $level['hp_diff'];
			$ret['attribute'] = $card['attribute_id'];
			$ret['smile'] = $card['smile_max'] - $level['smile_diff'];
			$ret['cute'] = $card['pure_max'] - $level['pure_diff'];
			$ret['cool'] = $card['cool_max'] - $level['cool_diff'];
			$ret['skill'] = 0;
		} else {
			unset($ret['center_skill']);
		}
		unset($ret['user_id']);
		foreach ($ret as &$v3) {
			if (is_numeric($v3)) {
				$v3 = (int) $v3;
			}
		}
		foreach ($unit_owning_user_id as $k2 => $v2) {
			if ($v2 == $ret['unit_owning_user_id']) {
				$sort[] = $k2;
				break;
			}
		}
		//4.0假数据（目前不支持）：
		$ret['is_removable_skill_capacity_max'] = false;
		$ret['unit_removable_skill_capacity'] = 0;
		$ret['unit_skill_exp'] = 0;
		$ret2[] = $ret;
	}
	if ($noarray) {
		return $ret2[0];
	}
	array_multisort($sort, SORT_ASC, $ret2);
	return $ret2;
}

function CheckWhetherSupportUnit($unit_id){
	$unit = getUnitDb();
}