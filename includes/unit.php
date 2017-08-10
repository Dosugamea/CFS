<?php
function addUnit($unit_id, $cnt = 1, $detail = false) {
	global $uid, $mysql;
	$unit = getUnitDb();
	$support_list = getSupportUnitList();
	if(in_array($unit_id,$support_list)){
		$amount = $mysql->query("SELECT amount FROM unit_support_list WHERE user_id=".$uid." AND unit_id=".$unit_id)->fetch();
		//var_dump($amount[0] + $cnt);
		//die();
		if($amount == false)
			$mysql->query('INSERT INTO unit_support_list (user_id, unit_id, amount) VALUES (?,?,?)', [$uid, $unit_id, $cnt]);
		else
			$mysql->query('UPDATE unit_support_list SET amount = '.($amount[0] + $cnt).' WHERE user_id = '.$uid.' AND unit_id = '.$unit_id);
		$mysql->query('insert ignore into album (user_id, unit_id, rank_max_flag, love_max_flag, rank_level_max_flag) values (?, ?, 1, 1, 1)', [$uid, $unit_id]);
		$ret = [GetUnitDetail($unit_id, false, false, true)];
	}else{
		$default_rankup = $unit->query('select unit_id from unit_m where unit_id=? and (disable_rank_up=1 or normal_icon_asset like "%rankup%")', [$unit_id])->fetch();
		$default_skill_count = $unit->query("SELECT default_removable_skill_capacity FROM unit_m WHERE unit_id = ".$unit_id)->fetch()[0];
		if ($default_rankup) {
			$mysql->query('insert ignore into album (user_id, unit_id, rank_max_flag) values (?, ?, 1)', [$uid, $unit_id]);
			$sql = $mysql->prepare('insert into unit_list (user_id, unit_id, rank, removable_skill_count) values (?, ?, 2, ?)');
		} else {
			$mysql->query('insert ignore into album (user_id, unit_id) values (?, ?)', [$uid, $unit_id]);
			$sql = $mysql->prepare('insert into unit_list (user_id, unit_id, removable_skill_count) values (?, ?, ?)');
		}
		for ($i = 0; $i < $cnt; $i++) {
			$sql->execute([$uid, $unit_id, $default_skill_count]);
			$ret[] = GetUnitDetail($mysql->lastInsertId(), $detail);
		}
	}
	return $ret;
}

//GetUnitDetail函数：获取指定卡片的详细信息。客户端不会调用此函数。
function GetUnitDetail($unit_owning_user_id, $return_attr_value = false, $preload_all = false, $is_support_unit = false) {
	if (!$unit_owning_user_id) {
		return array();
	}
	$support_list = getSupportUnitList();
	if($is_support_unit || in_array($unit_owning_user_id, $support_list)){
		$unit = getUnitDb();
		$ret['rarity'] = (int)$unit->query("SELECT rarity FROM unit_m WHERE unit_id = ".$unit_owning_user_id)->fetch()['rarity'];
		$ret['unit_id'] = (int)$unit_owning_user_id;
		$ret['unit_owning_user_id'] = 0;
		$ret['is_support_member'] = true;
		$ret['exp'] = 0;
		$ret['next_exp'] = 0;
		$ret['max_hp'] = 0;
		$ret['level'] = 1;
		$ret['skill_level'] = 0;
		$ret['rank'] = 1;
		$ret['love'] = 0;
		$ret['is_rank_max'] = false;
		$ret['is_level_max'] = false;
		$ret['is_love_max'] = false;
		$ret['new_unit_flag'] = true;
		$ret['reward_box_flag'] = false;
		$ret['unit_skill_exp'] = 0;
		$ret['display_rank'] = 1;
		$ret['unit_removable_skill_capacity'] = 0;
		$ret2 = $ret;
	}else{
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
			if($card['default_unit_skill_id'] == null){
				$skill_level = ['skill_level' => 1, 'next_exp' => 0, 'hp_diff' => 0, 'smile_diff' => 0, 'pure_diff' => 0, 'cool_diff' => 0];
			}else{
				$skill_pattern_id = $unit->query("SELECT unit_skill_level_up_pattern_id FROM unit_skill_m WHERE unit_skill_id = ".$card['default_unit_skill_id'])->fetch()[0];
				if($skill_pattern_id == null|| $skill_pattern_id == "")
					trigger_error("skill_pattern_id NOT FOUND! Skill ID: ".$card['default_unit_skill_id']." Card ID: ".$card['unit_id']);
				$skill_level = $unit->query('SELECT * FROM unit_skill_level_up_pattern_m WHERE unit_skill_level_up_pattern_id='.$skill_pattern_id.' AND next_exp>'.$ret['unit_skill_exp'].' LIMIT 1')->fetch();
				if ($skill_level == null) {
					$skill_level = $unit->query('SELECT * FROM unit_skill_level_up_pattern_m WHERE unit_skill_level_up_pattern_id='.$skill_pattern_id.' AND next_exp=0')->fetch();
				}
				if ($skill_level == null) {
					$skill_level = ['skill_level' => 1, 'next_exp' => 0, 'hp_diff' => 0, 'smile_diff' => 0, 'pure_diff' => 0, 'cool_diff' => 0];
				}
			}
			$ret['is_support_member'] = false;
			$ret['favorite_flag'] = (bool) $ret['favorite_flag'];
			$ret['level'] = $level['unit_level'];
			$ret['unit_skill_level'] = (int)$skill_level['skill_level'];
			$ret['skill_level'] = $ret['unit_skill_level'];
			$ret['unit_skill_exp'] = (int)$ret['unit_skill_exp'];
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
				
			} else {
				unset($ret['center_skill']);
			}
			if(empty($ret['removable_skill']))
				$ret['removable_skill']=[];
			else
				$ret['removable_skill'] = json_decode($ret['removable_skill'], true);
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
			$ret['unit_removable_skill_capacity'] = (int)$ret['removable_skill_count'];
			//unset($ret['removable_skill']);
			$ret['is_removable_skill_capacity_max'] = $card['max_removable_skill_capacity'] == $ret['unit_removable_skill_capacity'];
			
			//4.0假数据（目前不支持）：
			
			$ret2[] = $ret;
		}
		if ($noarray) {
			if(count($ret2)==0)
				trigger_error('GetUnitDetail: 部分卡缺失，请重新组卡'.$$unit_owning_user_id[0]);
			return $ret2[0];
		}
		array_multisort($sort, SORT_ASC, $ret2);
	}
	return $ret2;
}

function getSupportUnitList(){
	$unit = getUnitDb();
	$ret = $unit->query("SELECT unit_id FROM unit_m WHERE disable_rank_up=1 OR disable_rank_up=3")->fetchAll();
	foreach($ret as &$i)
		$i = $i['unit_id'];
	return $ret;
}

function removeFromDeck($unit_owning_user_id){
	global $uid, $mysql;
	$deck_orig = $mysql->query("SELECT json FROM user_deck WHERE user_id = ".$uid)->fetchColumn();
	$deck_orig = json_decode($deck_orig, true);
	$deck = $deck_orig;
	foreach($deck as &$i)
		foreach($i['unit_deck_detail'] as $j => $k)
			if($unit_owning_user_id == $k['unit_owning_user_id'])
				array_slice($i['unit_deck_detail'],$j,1);
	if($deck != $deck_orig)
		$mysql->query("UPDATE user_deck SET json = '".json_encode($deck)."' WHERE user_id = ".$uid);
	return 1;
}

function getCenterSkillInfo($center_skill_id){
	if(!isset($center_skill_id)||empty($center_skill_id))
		return ['source'=>0,'target'=>0,'effect'=>0,'e_attr'=>0,'e_type'=>0,'e_effect'=>0];
	$unitdb = getUnitDb();
	$center_skill=$unitdb->query('SELECT * FROM unit_leader_skill_m WHERE unit_leader_skill_id = '.$center_skill_id)->fetch();
	$leader_skill_type = $center_skill['leader_skill_effect_type'] . $center_skill['leader_skill_effect_type'];
	$ret['source']=(int)substr($leader_skill_type, -2, 1);
	$ret['target']=(int)substr($leader_skill_type, -1, 1);
	$ret['effect']=(int)$center_skill['effect_value'];

	$center_skill_extra=$unitdb->query('SELECT * FROM unit_leader_skill_extra_m WHERE unit_leader_skill_id = '.$center_skill_id)->fetch();
	if($center_skill_extra==null||count($center_skill_extra)==0){
		$ret['e_attr']=0;
		$ret['e_type']=0;
		$ret['e_effect']=0;
	}else{
		$ret['e_attr']=(int)$center_skill_extra['leader_skill_effect_type'];
		$ret['e_type']=(int)$center_skill_extra['member_tag_id'];
		$ret['e_effect']=(int)$center_skill_extra['effect_value'];
	}
	return $ret;
}

function getDeckAttribute($deck,$post){
	global $mysql;
	$deck_info=[0,0,0,0];
	$member_info=[];
	$school_skill=[];//记录全体百分比加成

	//第一步 获得基础属性、可换技能以及C位技能
	foreach($deck['unit_deck_detail'] as $member){
		$position=$member['position'];
		$card_info=GetUnitDetail($member['unit_owning_user_id'],true);
		$deck_info[0]+=$card_info['hp'];

		$info[0]=0;
		$info[1]=$card_info['smile'];
		$info[2]=$card_info['cute'];
		$info[3]=$card_info['cool'];
		$info[$card_info['attribute']]+=$card_info['love'];
		$info['unit_id']=$card_info['unit_id'];

		$info['skill']=[];
		foreach($card_info['removable_skill'] as $skill){
			if($skill<=24)
				$info['skill'][]=(int)$skill;
			elseif($skill<=30)
				$school_skill[]=(int)$skill;
		}

		$member_info[$position]=$info;

		if($position==5)
			$center_skill_id=$card_info['center_skill'];
	}
	$default_center_skill = [0 => 0, -1 => 1, -2 =>4, -3 => 7];
	if (!isset($post['ScoreMatch'])) {
		if ($post['party_user_id'] <= 0)
			$party_skill_id = $default_center_skill[$post['party_user_id']];
		else {
			$party = $mysql->query('SELECT users.user_id, center_unit FROM users LEFT JOIN user_deck ON users.user_id=user_deck.user_id WHERE users.user_id = '.$post['party_user_id'])->fetch();
			$party_center_unit = GetUnitDetail($party['center_unit'],true);
			$party_skill_id = $party_center_unit['center_skill'];
		}
	}
	$center_skill=getCenterSkillInfo($center_skill_id);
	$party_skill=getCenterSkillInfo($party_skill_id);

	//第二步 计算可换技能加成 + 第三步 计算Center及好友应援加成
	foreach($member_info as $info){
		$bonus=[0,0,0,0];
		$skills=array_merge($info['skill'],$school_skill);
		foreach($skills as $skill){
			$skill2=($skill-1)%3+1;
			if($skill<=3)
				$bonus[$skill]+=200;
			elseif($skill<=6)
				$bonus[$skill2]+=450;
			elseif($skill<=15)
				$bonus[$skill2]+=(int)ceil($info[$skill2] * 0.10);
			elseif($skill<=24)
				$bonus[$skill2]+=(int)ceil($info[$skill2] * 0.16);
			elseif($skill<=27)
				$bonus[$skill2]+=(int)ceil($info[$skill2] * 0.018);
			else
				$bonus[$skill2]+=(int)ceil($info[$skill2] * 0.024);
		}
		for($i=1;$i<=3;$i++)
			$info[$i]+=$bonus[$i];
				
		$bonus=[0,0,0,0];
		$bonus[$center_skill['target']]+=(int)ceil($info[$center_skill['source']] * $center_skill['effect'] /100);
		$bonus[$party_skill['target']]+=(int)ceil($info[$party_skill['source']] * $party_skill['effect'] /100);
				
		$unitdb = getUnitDb();
		$unit_type_id = $unitdb->query('SELECT unit_type_id FROM unit_m WHERE unit_id = '.$info['unit_id'])->fetch()[0];
		if (in_array($unit_type_id,[1,2,3,4,5,6,7,8,9,101,102,103,104,105,106,107,108,109])){//如果是水缪成员
			$member_tag_id = $unitdb->query('SELECT member_tag_id FROM unit_type_member_tag_m WHERE unit_type_id = '.$unit_type_id)->fetchAll(PDO::FETCH_COLUMN);
			if(!empty($center_skill) && in_array($center_skill['e_type'],$member_tag_id))
				$bonus[$center_skill['e_attr']] += (int)ceil($info[$center_skill['e_attr']] * $center_skill['e_effect'] /100);
			if(!empty($party_skill) && in_array($party_skill['e_type'],$member_tag_id))
				$bonus[$party_skill['e_attr']] += (int)ceil($info[$party_skill['e_attr']] * $party_skill['e_effect'] /100);
		}

		for($i=1;$i<=3;$i++)
			$deck_info[$i]+=($info[$i]+$bonus[$i]);
	}

	$deck_ret = [
		'unit_deck_id' => $deck['unit_deck_id'],
		'total_smile' => $deck_info[1],
		'total_cute' => $deck_info[2],
		'total_cool' => $deck_info[3],
		'total_hp' => $deck_info[0]
	];
	return $deck_ret;
}