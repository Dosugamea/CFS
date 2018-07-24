<?php
//challenge.php CF活动

//获得当前CF活动信息
function challenge_challengeInfo(){
    global $envi, $mysql, $uid, $config;
	$event_point = (int)$mysql->query("SELECT event_point FROM event_point WHERE user_id = ? AND event_id = ?", [$uid, $config->event['challenge']['event_id']])->fetchColumn();
	if(!$event_point){
		$mysql->query("INSERT IGNORE INTO event_point VALUES(?,?,?,?)",[$uid, $config->event['challenge']['event_id'], 0, 0]);
		$event_point = 0;
	}
	$mysql->query("INSERT IGNORE INTO tmp_challenge_live (user_id) VALUES (?)", [$uid]);
	$mysql->query("INSERT IGNORE INTO tmp_challenge_reward (user_id, rarity, amount) VALUES (?, 1, 0), (?, 2, 0), (?, 3, 0)", [$uid, $uid, $uid]);
    $ret = json_decode(
        '{
            "base_info":{
                "event_id": 0,
                "asset_bgm_id": 4,
                "event_point": 0,
                "total_event_point": 0,
				"max_round": 5
            },
            "challenge_status": {
                "should_proceed": false,
                "should_retire": false,
                "should_finalize": false
            }
        }', true);
	$ret['base_info']['event_id'] = $config->event['challenge']['event_id'];
	$ret['base_info']['event_point'] = $event_point;
	$ret['base_info']['total_event_point'] = $event_point;
	$challenge_status = $mysql->query("SELECT live_difficulty_id FROM tmp_challenge_live WHERE user_id = ?",[$uid])->fetchColumn();
	if($challenge_status != null)
		$ret['challenge_status']['should_proceed'] = true;
	return $ret;
}

//获得当前分数与排名
function challenge_top() {
	include("../config/event.php");
	include("../config/modules_challenge.php");
	global $uid, $mysql;
	$event_status = getUserEventStatus($uid, $challenge['event_id']);
	$ret = [];
	$ret['event_status']['total_event_point'] = $event_status['event_point'];
	$ret['event_status']['event_rank'] = $event_status['event_point'] ? $event_status['rank'] : false;
    return $ret;
}

//没打完一局的时候读取活动状态
function challenge_status() {
	include("../config/event.php");
	include("../config/modules_challenge.php");
	global $uid, $mysql;
	$ret = [];
	$info = $mysql->query("SELECT * FROM tmp_challenge_live WHERE user_id = ?",[$uid])->fetch(PDO::FETCH_ASSOC);
	$ret['can_proceed'] = 0;
	$ret['challenge_info']['round'] = (int)$info['round'];
	$ret['challenge_info']['live_info']['live_difficulty_id'] = (int)$info['live_difficulty_id'];
	$ret['challenge_info']['live_info']['is_random'] = (bool)$info['random'];
	$ret['challenge_info']['slot_info'] = json_decode($info['bonus']);
	$ret['challenge_info']['mission_info'] = json_decode($info['mission']);
	switch((int)$info['course_id']){
		case 1:
			$ret['challenge_info']['use_lp'] = 5;break;
		case 2:
			$ret['challenge_info']['use_lp'] = 10;break;
		case 3:
			$ret['challenge_info']['use_lp'] = 15;break;
		case 4:
			$ret['challenge_info']['use_lp'] = 25;break;
	}
	$ret['challenge_info']['event_challenge_previous_item_ids'] = json_decode($info['use_item']);
	$ret['challenge_info']['accumulated_reward_info']['player_exp'] = (int)$info['exp'];
	$ret['challenge_info']['accumulated_reward_info']['game_coin'] = (int)$info['coin'];
	$ret['challenge_info']['accumulated_reward_info']['event_point'] = (int)$info['event_point'];
	$ret['challenge_info']['accumulated_reward_info']['reward_rarity_list'] = [];
	$rewards = $mysql->query("SELECT * FROM tmp_challenge_reward WHERE user_id = ?", [$uid])->fetchAll(PDO::FETCH_ASSOC);
	foreach($rewards as $i){
		$ret['challenge_info']['accumulated_reward_info']['reward_rarity_list'][] = ["rarity" => (int)$i['rarity'], "amount" => (int)$i['amount']];
	}
	
	return $ret;
}

//进入一局CF
function challenge_init($post) {
	include("../config/event.php");
	include("../config/modules_challenge.php");
	global $uid, $mysql;
	$ret = [];
	
	$maps = $challenge_live_difficulty_ids[(int)$post['course_id']][1];
	$live = getLiveDb();
	$initial_stage_level = ["1"=>1, "2"=>4, "3"=>8, "4"=>10];
	$initial_difficulty = ["1"=>1, "2"=>2, "3"=>3, "4"=>4];
	if($maps==null || count($maps)==0){//检测歌单是否为空
		$maps = [];
    	$mapss = $live->query('SELECT notes_setting_asset 
        	FROM live_setting_m 
        	WHERE stage_level = ? AND difficulty = ?',[$initial_stage_level[$post['course_id']], $initial_difficulty[$post['course_id']]])->fetchAll(PDO::FETCH_ASSOC);
    	foreach($mapss as $i){
			$current_map[1] = ($initial_difficulty[$post['course_id']]==5)?1:0;
			$current_map[0] = $i['notes_setting_asset'];
			$maps []= $current_map;
		}
	}
	$map = $maps[rand(0,count($maps)-1)];
	$random = $map[1];
	$map = $map[0];
    $selected_live_setting = $live->query('SELECT live_setting_id 
        FROM live_setting_m 
        WHERE notes_setting_asset = ?',[$map])->fetchColumn();
	foreach(["normal_live_m", "special_live_m"] as $i){
		$selected_live = $live->query('SELECT live_difficulty_id 
			FROM '.$i.' 
			WHERE live_setting_id = ?',[$selected_live_setting])->fetchColumn();
		if($selected_live)
			break;
	}
	if(!$selected_live)
		trigger_error("找不到对应的live_difficulty_id:".$map);
	$selected_live = (int)$selected_live;
	$ret['challenge_info'] = [];
	$ret['challenge_info']['round'] = 1;
	$ret['challenge_info']['live_info']['live_difficulty_id'] = $selected_live;
	$ret['challenge_info']['live_info']['is_random'] = (bool)$random;
	$ret['challenge_info']['slot_info'] = [];
	
	$challengedb = getChallengeDb();
	if(!$challengedb)
		trigger_error("连接CF活动数据库失败！");
	
	$bonus_units = $challengedb->query("SELECT * FROM event_challenge_bonus_unit_m")->fetchAll(PDO::FETCH_ASSOC);
	$bonus_unit_sum = rand(1,3);
	$bonus_unit_sum = 3;
	for($i = 0; $i < $bonus_unit_sum; $i++){
		srand((float) microtime() * 10000000);
		$bonus_unit = $bonus_units[array_rand($bonus_units)];
		$ret['challenge_info']['slot_info'][] = ["unit_id" => (int)$bonus_unit['unit_id'], "bonus_type" => (int)$bonus_unit['bonus_type'], "bonus_param" => (int)$bonus_unit['bonus_param'] >= 100 ? ((int)$bonus_unit['bonus_param'])/100 : (int)$bonus_unit['bonus_param']];
	}
	
	$ret['challenge_info']['mission_info'] = [];
	$unitdb = getUnitDb();
	$member_tags = [];
	$mission = [];
	if($bonus_unit_sum == 3){
		$slot_unit = [];
		foreach($ret['challenge_info']['slot_info'] as $i){
			$unit_type = $unitdb->query("SELECT unit_type_id FROM unit_m WHERE unit_id = ?", [$i['unit_id']])->fetchColumn();
			$member_tag = $unitdb->query("SELECT member_tag_id FROM unit_type_member_tag_m WHERE unit_type_id = ?", [$unit_type])->fetchAll(PDO::FETCH_ASSOC);
			foreach($member_tag as &$j)
				$j = (int)$j['member_tag_id'];
			unset($j);
			$member_tags[] = $member_tag;
			$slot_unit[] = $i['unit_id'];
		}
		foreach([1,2,3,6,7,8] as $i){
			if(in_array($i, $member_tags[0]) && in_array($i, $member_tags[1]) && in_array($i, $member_tags[2])){
				$mission[] = $i;
			}
		}
		foreach($mission as $i){
			switch($i){
				case 1:
					$ret['challenge_info']['mission_info'][] = ["type" => 4000, "param" => "100", "bonus_type" => 3050, "bonus_param" => "20", "asset_name" => "assets/image/event/mission/ms_m_type_01.png"];break;
				case 2:
					$ret['challenge_info']['mission_info'][] = ["type" => 4000, "param" => "100", "bonus_type" => 3010, "bonus_param" => "1.2", "asset_name" => "assets/image/event/mission/ms_m_type_02.png"];break;
				case 3:
					$ret['challenge_info']['mission_info'][] = ["type" => 4000, "param" => "100", "bonus_type" => 3030, "bonus_param" => "1.2", "asset_name" => "assets/image/event/mission/ms_m_type_03.png"];break;
				case 6:
					$ret['challenge_info']['mission_info'][] = ["type" => 4000, "param" => "100", "bonus_type" => 3030, "bonus_param" => "1.3", "asset_name" => "assets/image/event/mission/ms_m_type_04.png"];break;
				case 7:
					$ret['challenge_info']['mission_info'][] = ["type" => 4000, "param" => "100", "bonus_type" => 3050, "bonus_param" => "30", "asset_name" => "assets/image/event/mission/ms_m_type_05.png"];break;
				case 8:
					$ret['challenge_info']['mission_info'][] = ["type" => 4000, "param" => "100", "bonus_type" => 3010, "bonus_param" => "1.3", "asset_name" => "assets/image/event/mission/ms_m_type_06.png"];break;
				default:
					trigger_error("找不到对应的mission：".$i);
			}
		}
		$in_list = 0;
		foreach($slot_unit as $i){
			if(in_array($i, [588, 589, 590, 591, 592, 599, 600, 601, 602])) //电玩
				$in_list ++;
		}
		if($in_list == 3)
			$ret['challenge_info']['mission_info'][] = ["type" => 1000, "param" => "1", "bonus_type" => 3030, "bonus_param" => "1.1", "asset_name" => "assets/image/event/mission/ms_m_group_01.png"];
		
		$in_list = 0;
		foreach($slot_unit as $i){
			if(in_array($i, [374, 375, 376, 377, 378, 394, 395, 396, 397])) //旗袍
				$in_list ++;
		}
		if($in_list == 3)
			$ret['challenge_info']['mission_info'][] = ["type" => 1000, "param" => "1", "bonus_type" => 3050, "bonus_param" => "10", "asset_name" => "assets/image/event/mission/ms_m_group_02.png"];
		
		$in_list = 0;
		foreach($slot_unit as $i){
			if(in_array($i, [278, 279, 280, 281, 282, 295, 296, 297, 298])) //打工
				$in_list ++;
		}
		if($in_list == 3)
			$ret['challenge_info']['mission_info'][] = ["type" => 1000, "param" => "1", "bonus_type" => 3010, "bonus_param" => "1.1", "asset_name" => "assets/image/event/mission/ms_m_group_03.png"];
		
		$in_list = 0;
		foreach($slot_unit as $i){
			if(in_array($i, [161, 162, 163, 164, 165, 169, 170, 171, 172])) //啦啦队
				$in_list ++;
		}
		if($in_list == 3)
			$ret['challenge_info']['mission_info'][] = ["type" => 1000, "param" => "1", "bonus_type" => 3060, "bonus_param" => "3", "asset_name" => "assets/image/event/mission/ms_m_group_04.png"];
		
	}
	$lp_list = [false,5,10,15,25];
	$ret['challenge_info']['use_lp'] = $lp_list[(int)$post['course_id']];
	$use_item = json_decode($mysql->query("SELECT use_item FROM tmp_challenge_live WHERE user_id = ?",[$uid])->fetchColumn(), true);
	$ret['challenge_info']['event_challenge_previous_item_ids'] = $use_item;
	$ret['challenge_info']['accumulated_reward_info'] = [];
	$ret['challenge_info']['accumulated_reward_info']['player_exp'] = 0;
	$ret['challenge_info']['accumulated_reward_info']['game_coin'] = 0;
	$ret['challenge_info']['accumulated_reward_info']['event_point'] = 0;
	$rewards = $mysql->query("SELECT * FROM tmp_challenge_reward WHERE user_id = ?", [$uid])->fetchAll(PDO::FETCH_ASSOC);
	foreach($rewards as $i){
		$ret['challenge_info']['accumulated_reward_info']['reward_rarity_list'][] = ["rarity" => (int)$i['rarity'], "amount" => (int)$i['amount']];
	}
	
	$mysql->query("UPDATE tmp_challenge_live SET course_id = ?, round = 1, live_difficulty_id = ?, bonus = ?, mission = ?, random = ?, event_point = 0, exp = 0, coin = 0 WHERE user_id = ?",[$post['course_id'], $selected_live, json_encode($ret['challenge_info']['slot_info']), json_encode($ret['challenge_info']['mission_info']), $random, $uid]);
	return $ret;
}

//开始live
function challenge_proceed($post) {
	global $mysql, $uid, $envi;
	$info = $mysql->query("SELECT * FROM tmp_challenge_live WHERE user_id = ?",[$uid])->fetch(PDO::FETCH_ASSOC);
	$ret = runAction('live','play',['live_difficulty_id' => $info['live_difficulty_id'],'unit_deck_id' => $post['unit_deck_id'],'random_switch' => $info['random'], 'ScoreMatch' => true]);
	$mysql->query("UPDATE tmp_challenge_live SET is_start = 1, use_item = ? WHERE user_id = ?", [json_encode($post['event_challenge_item_ids']), $uid]);
	$slot = json_decode($info['bonus'], true);
	$bonus = [];
	$before_user_info = runAction('user','userInfo')['user'];
	foreach($slot as $i)
		if($i['bonus_type'] == 2030)
			$bonus[] = ["bonus_type" => $i['bonus_type'], "bonus_param" => $i['bonus_param']];
		
	foreach($post['event_challenge_item_ids'] as $i){
		switch($i){
			case 1:
				$envi->params['item3'] -= 15000;
				break;
			case 2:
				$envi->params['item3'] -= 5000;
				break;
			case 3:
				$envi->params['item3'] -= 12500;
				$bonus[] = ["bonus_type" => 2020, "bonus_param" => "1.1"];
				break;
			case 4:
				$envi->params['item3'] -= 12500;
				$bonus[] = ["bonus_type" => 2010, "bonus_param" => "1.1"];
				break;
			case 5:
				$envi->params['item3'] -= 25000;
				$bonus[] = ["bonus_type" => 2030, "bonus_param" => "5"];
				break;
			case 6:
				$envi->params['item3'] -= 50000;
				break;
			default:
				trigger_error("您使用了未知的物品：".$i);
		}
	}
	
	foreach($ret['live_list'] as &$i){
		$i['bonus_list'] = $bonus;
	}
	$ret['live_se_id'] = 1;
	$ret['before_user_info'] = $before_user_info;
	$ret['after_user_info'] = runAction('user','userInfo')['user'];
	$ret['available_live_resume'] = false;
	return $ret;
}

//结束live，创建记录点
function challenge_checkpoint($post) {
	include("../config/event.php");
	include("../config/modules_challenge.php");
	global $mysql, $uid;
	$info = $mysql->query("SELECT * FROM tmp_challenge_live WHERE user_id = ?",[$uid])->fetch(PDO::FETCH_ASSOC);
	$post['ScoreMatch'] = true;
	$post['no_card'] = true;
	$reward = runAction('live','reward',$post);
	$ret = [];
	$ret['challenge_result'] = [];
	$ret['challenge_result']['live_info'] = $reward['live_info'];
	$ret['challenge_result']['rank'] = $reward['rank'];
	$ret['challenge_result']['combo_rank'] = $reward['combo_rank'];
	$ret['challenge_result']['total_love'] = $reward['total_love'];
	$ret['challenge_result']['mission_result'] = [];
	$ret['challenge_result']['reward_info'] = [];
	$ret['challenge_result']['reward_info']['player_exp'] = $reward['base_reward_info']['player_exp'];
	$ret['challenge_result']['reward_info']['game_coin'] = $reward['base_reward_info']['game_coin'];
	switch((int)$info['course_id']){
		case 4:
			$ret['challenge_result']['reward_info']['base_event_point'] = 301;break;
		case 3:
			$ret['challenge_result']['reward_info']['base_event_point'] = 158;break;
		case 2:
			$ret['challenge_result']['reward_info']['base_event_point'] = 91;break;
		case 1:
			$ret['challenge_result']['reward_info']['base_event_point'] = 39;break;
	}
	$ret['challenge_result']['reward_info']['event_point'] = $ret['challenge_result']['reward_info']['base_event_point'];
	switch($ret['challenge_result']['rank']){
		case 1:
			$ret['challenge_result']['reward_info']['event_point'] *= 1.20;break;
		case 2:
			$ret['challenge_result']['reward_info']['event_point'] *= 1.15;break;
		case 3:
			$ret['challenge_result']['reward_info']['event_point'] *= 1.10;break;
		case 4:
			$ret['challenge_result']['reward_info']['event_point'] *= 1.05;break;
	}
	switch($ret['challenge_result']['combo_rank']){
		case 1:
			$ret['challenge_result']['reward_info']['event_point'] *= 1.08;break;
		case 2:
			$ret['challenge_result']['reward_info']['event_point'] *= 1.06;break;
		case 3:
			$ret['challenge_result']['reward_info']['event_point'] *= 1.04;break;
		case 4:
			$ret['challenge_result']['reward_info']['event_point'] *= 1.02;break;
	}
	$ret['challenge_result']['reward_info']['event_point'] = floor($ret['challenge_result']['reward_info']['event_point']);
	
	$generate_reward = function($rank){
		if($rank == 1)
			return [3];
		else if($rank == 2)
			return [rand(1,3)];
		else if($rank == 3)
			return [rand(1,2)];
		else if($rank == 4)
			return [3];
		else
			return [];
	};
	
	
	$slot = json_decode($info['bonus'], true);
	$bonus = [];
	foreach($slot as $i)
		$bonus[] = ["bonus_type" => $i['bonus_type'], "bonus_param" => $i['bonus_param']];
		
	$reward_up = false;
	foreach(json_decode($info['use_item'], true) as $i){
		switch($i){
			case 1:
				$ret['challenge_result']['reward_info']['player_exp'] = floor($reward['base_reward_info']['player_exp'] * 1.1);
				$bonus[] = ["bonus_type" => 3010, "bonus_param" => "1.1"];
				break;
			case 2:
				$ret['challenge_result']['reward_info']['event_point'] = floor($ret['challenge_result']['reward_info']['event_point'] * 1.1);
				$bonus[] = ["bonus_type" => 3030, "bonus_param" => "1.1"];
				break;
			case 3:
				$bonus[] = ["bonus_type" => 2020, "bonus_param" => "1.1"];
				break;
			case 4:
				$bonus[] = ["bonus_type" => 2010, "bonus_param" => "1.1"];
				break;
			case 5:
				$bonus[] = ["bonus_type" => 2030, "bonus_param" => "5"];
				break;
			case 6:
				$reward_up = true;
				break;
			default:
				trigger_error("您使用了未知的物品：".$i);
		}
	}
	
	$ret['challenge_result']['reward_info']['reward_rarity_list'] = [];
	if($reward_up){
		$ret['challenge_result']['reward_info']['reward_rarity_list']['live_clear'] = [rand(2,3)];
		$ret['challenge_result']['reward_info']['reward_rarity_list']['bonuses'] = [rand(1,3)];
	}else{
		$ret['challenge_result']['reward_info']['reward_rarity_list']['live_clear'] = [rand(1,3)];
		$ret['challenge_result']['reward_info']['reward_rarity_list']['bonuses'] = [];
	}
	$ret['challenge_result']['reward_info']['reward_rarity_list']['live_score'] = $generate_reward($ret['challenge_result']['rank']);
	$ret['challenge_result']['reward_info']['reward_rarity_list']['live_combo'] = $generate_reward($ret['challenge_result']['combo_rank']);
	
	//奖励写入数据库
	foreach($ret['challenge_result']['reward_info']['reward_rarity_list'] as $i)
		foreach($i as $j)
			$mysql->query("UPDATE tmp_challenge_reward SET amount = amount + 1 WHERE user_id = ? AND rarity = ?", [$uid, $j]);
	
	$ret['challenge_result']['bonus_list'] = $bonus;
	$ret['unlocked_subscenario_ids'] = [];
	$ret['effort_point'] = $reward['effort_point'];
	$ret['limited_effort_box'] = [];
	$ret['unit_list'] = $reward['unit_list'];
	$ret['before_user_info'] = $reward['before_user_info'];
	$ret['after_user_info'] = $reward['after_user_info'];
	$ret['can_proceed'] = (int)$info['round'] == 5 ? false : true;
	
	$ret['challenge_info'] = [];
	if($ret['can_proceed']){
		$ret['challenge_info']['round'] = (int)$info['round'] + 1;
		$ret['challenge_info']['live_info'] = [];
		
		//随机抽歌
		$maps = $challenge_live_difficulty_ids[(int)$info['course_id']][1];
		$live = getLiveDb();
		$challenge_stage_level = [null,[null,1,2,3,4,5],[null,4,5,6,7,8],[null,7,8,8,9,9],[null,10,11,11,11,12]];
		$challenge_difficulty = [null,[null,1,1,1,1,1],[null,2,2,2,2,2],[null,3,3,3,3,4],[null,4,4,5,6,6]];
		if($maps==null || count($maps)==0){//检测歌单是否为空
			$maps = [];
			$mapss = $live->query('SELECT notes_setting_asset 
				FROM live_setting_m 
				WHERE stage_level = ? AND difficulty = ?',[$challenge_stage_level[$info['course_id']][$ret['challenge_info']['round']], $challenge_difficulty[$info['course_id']][$ret['challenge_info']['round']]])->fetchAll(PDO::FETCH_ASSOC);
			foreach($mapss as $i){
				$current_map[1] = ($challenge_difficulty[$info['course_id']]==5)?1:0;
				$current_map[0] = $i['notes_setting_asset'];
				$maps []= $current_map;
			}
		}
		$map = $maps[rand(0,count($maps)-1)];
		$random = $map[1];
		$map = $map[0];
		$selected_live_setting = $live->query('SELECT live_setting_id 
			FROM live_setting_m 
			WHERE notes_setting_asset = ?',[$map])->fetchColumn();
		foreach(["normal_live_m", "special_live_m"] as $i){
			$selected_live = $live->query('SELECT live_difficulty_id 
				FROM '.$i.' 
				WHERE live_setting_id = ?',[$selected_live_setting])->fetchColumn();
			if($selected_live)
				break;
		}
		if(!$selected_live)
			trigger_error("找不到对应的live_difficulty_id:".$map);
		$selected_live = (int)$selected_live;
		$ret['challenge_info']['live_info'] = ["live_difficulty_id"=>$selected_live, "is_random"=> (bool)$random];
		$ret['challenge_info']['slot_info'] = [];
		
		$challengedb = getChallengeDb();
		if(!$challengedb)
			trigger_error("连接CF活动数据库失败！");
		
		$bonus_units = $challengedb->query("SELECT * FROM event_challenge_bonus_unit_m")->fetchAll(PDO::FETCH_ASSOC);
		$bonus_unit_sum = rand(1,3);
		$bonus_unit_sum = 3;
		for($i = 0; $i < $bonus_unit_sum; $i++){
			$bonus_unit = $bonus_units[rand(0,count($bonus_units))];
			$ret['challenge_info']['slot_info'][] = ["unit_id" => (int)$bonus_unit['unit_id'], "bonus_type" => (int)$bonus_unit['bonus_type'], "bonus_param" => (int)$bonus_unit['bonus_param'] >= 100 ? ((int)$bonus_unit['bonus_param'])/100 : (int)$bonus_unit['bonus_param']];
		}
		
		$ret['challenge_info']['mission_info'] = [];
		$unitdb = getUnitDb();
		$member_tags = [];
		$mission = [];
		if($bonus_unit_sum == 3){
			$slot_unit = [];
			foreach($ret['challenge_info']['slot_info'] as $i){
				$unit_type = $unitdb->query("SELECT unit_type_id FROM unit_m WHERE unit_id = ?", [$i['unit_id']])->fetchColumn();
				$member_tag = $unitdb->query("SELECT member_tag_id FROM unit_type_member_tag_m WHERE unit_type_id = ?", [$unit_type])->fetchAll(PDO::FETCH_ASSOC);
				foreach($member_tag as &$j)
					$j = (int)$j['member_tag_id'];
				unset($j);
				$member_tags[] = $member_tag;
				$slot_unit[] = $i['unit_id'];
			}
			foreach([1,2,3,6,7,8] as $i){
				if(in_array($i, $member_tags[0]) && in_array($i, $member_tags[1]) && in_array($i, $member_tags[2])){
					$mission[] = $i;
				}
			}
			foreach($mission as $i){
				switch($i){
					case 1:
						$ret['challenge_info']['mission_info'][] = ["type" => 4000, "param" => "100", "bonus_type" => 3050, "bonus_param" => "20", "asset_name" => "assets/image/event/mission/ms_m_type_01.png"];break;
					case 2:
						$ret['challenge_info']['mission_info'][] = ["type" => 4000, "param" => "100", "bonus_type" => 3010, "bonus_param" => "1.2", "asset_name" => "assets/image/event/mission/ms_m_type_02.png"];break;
					case 3:
						$ret['challenge_info']['mission_info'][] = ["type" => 4000, "param" => "100", "bonus_type" => 3030, "bonus_param" => "1.2", "asset_name" => "assets/image/event/mission/ms_m_type_03.png"];break;
					case 6:
						$ret['challenge_info']['mission_info'][] = ["type" => 4000, "param" => "100", "bonus_type" => 3030, "bonus_param" => "1.3", "asset_name" => "assets/image/event/mission/ms_m_type_04.png"];break;
					case 7:
						$ret['challenge_info']['mission_info'][] = ["type" => 4000, "param" => "100", "bonus_type" => 3050, "bonus_param" => "30", "asset_name" => "assets/image/event/mission/ms_m_type_05.png"];break;
					case 8:
						$ret['challenge_info']['mission_info'][] = ["type" => 4000, "param" => "100", "bonus_type" => 3010, "bonus_param" => "1.3", "asset_name" => "assets/image/event/mission/ms_m_type_06.png"];break;
					default:
						trigger_error("找不到对应的mission：".$i);
				}
			}
			$in_list = 0;
			foreach($slot_unit as $i){
				if(in_array($i, [588, 589, 590, 591, 592, 599, 600, 601, 602])) //电玩
					$in_list ++;
			}
			if($in_list == 3)
				$ret['challenge_info']['mission_info'][] = ["type" => 1000, "param" => "1", "bonus_type" => 3030, "bonus_param" => "1.1", "asset_name" => "assets/image/event/mission/ms_m_group_01.png"];
			
			$in_list = 0;
			foreach($slot_unit as $i){
				if(in_array($i, [374, 375, 376, 377, 378, 394, 395, 396, 397])) //旗袍
					$in_list ++;
			}
			if($in_list == 3)
				$ret['challenge_info']['mission_info'][] = ["type" => 1000, "param" => "1", "bonus_type" => 3050, "bonus_param" => "10", "asset_name" => "assets/image/event/mission/ms_m_group_02.png"];
			
			$in_list = 0;
			foreach($slot_unit as $i){
				if(in_array($i, [278, 279, 280, 281, 282, 295, 296, 297, 298])) //打工
					$in_list ++;
			}
			if($in_list == 3)
				$ret['challenge_info']['mission_info'][] = ["type" => 1000, "param" => "1", "bonus_type" => 3010, "bonus_param" => "1.1", "asset_name" => "assets/image/event/mission/ms_m_group_03.png"];
			
			$in_list = 0;
			foreach($slot_unit as $i){
				if(in_array($i, [161, 162, 163, 164, 165, 169, 170, 171, 172])) //啦啦队
					$in_list ++;
			}
			if($in_list == 3)
				$ret['challenge_info']['mission_info'][] = ["type" => 1000, "param" => "1", "bonus_type" => 3060, "bonus_param" => "3", "asset_name" => "assets/image/event/mission/ms_m_group_04.png"];
			
		}
		
		$lp_list = [false,5,10,15,25];
		$ret['challenge_info']['use_lp'] = $lp_list[(int)$info['course_id']];
		$use_item = json_decode($info['use_item'], true);
		$ret['challenge_info']['event_challenge_previous_item_ids'] = $use_item;
		$ret['challenge_info']['accumulated_reward_info'] = [];
		$ret['challenge_info']['accumulated_reward_info']['player_exp'] = (int)$info['event_point'] + $ret['challenge_result']['reward_info']['event_point'];
		$ret['challenge_info']['accumulated_reward_info']['game_coin'] = (int)$info['coin'] + $ret['challenge_result']['reward_info']['game_coin'];
		$ret['challenge_info']['accumulated_reward_info']['event_point'] = (int)$info['coin'] + $ret['challenge_result']['reward_info']['player_exp'];
		$rewards = $mysql->query("SELECT * FROM tmp_challenge_reward WHERE user_id = ?", [$uid])->fetchAll(PDO::FETCH_ASSOC);
		foreach($rewards as $i){
			$ret['challenge_info']['accumulated_reward_info']['reward_rarity_list'][] = ["rarity" => (int)$i['rarity'], "amount" => (int)$i['amount']];
		}
		$mysql->query("UPDATE tmp_challenge_live SET event_point = ?, exp = ?, coin = ?, is_start = 0, bonus = ?, round = ?, live_difficulty_id = ?, random = ? WHERE user_id = ?",[$ret['challenge_info']['accumulated_reward_info']['event_point'], $ret['challenge_info']['accumulated_reward_info']['player_exp'], $ret['challenge_info']['accumulated_reward_info']['game_coin'], json_encode($ret['challenge_info']['slot_info']), $ret['challenge_info']['round'], $selected_live, $random, $uid]);
	}
	$ret['daily_reward_info'] = $reward['daily_reward_info'];
	//更新房间信息
	
		
	return $ret;
}

//完成live，获取奖励
function challenge_finalize($post) {
	global $mysql, $uid, $user, $envi;
	include("../config/event.php");
	$ret = [];
	$info = $mysql->query("SELECT * FROM tmp_challenge_live WHERE user_id = ?",[$uid])->fetch(PDO::FETCH_ASSOC);
	$ret['base_reward_info']['player_exp'] = (int)$info['exp'];
	$before_user_info = runAction('user','userInfo')['user'];
	$ret['base_reward_info']['player_exp_unit_max'] = ["before" => 9000, "after" => 9000];
	$ret['base_reward_info']['player_exp_friend_max'] = ["before" => 999, "after" => 999];
	$ret['base_reward_info']['player_exp_lp_max'] = ["before" => $before_user_info['energy_max'], "after" => 0];
	$ret['base_reward_info']['game_coin'] = (int)$info['coin'];
	$ret['base_reward_info']['game_coin_reward_box_flag'] = false;
	
	$exp = [0,6,12,20,30,43,59,79,103,131,165,204,250,302,362,430,506,591,685,789,904,1029,1166,1315,1477,1651,1839,2042,2259,2491,2738,3002,3283,3581,3891,4218,4563,4925,5304,5700,6113,6544,6992,7457,7940,8440,8957,9491,10042,10611,11196,11799,12419,13057,13711,14383,15072,15779,16502,17243,18001,18776,19569,20379,21206,22050,22911,23789,24685,25598,26528,27475,28440,29422,30421,31437,32470,33521,34589,35674,36776,37896,39033,40187,41358,42547,43753,44976,46216,47473,48748,50040,51349,52675,54018,55379,56757,58152,59565,60995,63889,66818,69781,72779,75811,78877,81978,85113,88283,91487,94726,97999,101306,104647,108023,111433,114879,118359,121873,125421,129004,132622,136274,139961,143682,147438,151228,155053,158911,162804,166732,170694,174690,178721,182786,186886,191020,195189,199392,203629,207901,212207,216548,220923,225332,229777,234256,238770,243318,247900,252516,257167,261853,266573,271327,276116,280939,285797,290689,295616,300577,305573,310603,315667,320766,325900,331068,336270,341507,346778,352084,357424,362799,368208,373652,379130,384642,390189,395770,401386,407036,412721,418440,424194,429982,435804,441661,447552,453478,459438,465432,471461,477524,483622,489754,495921,502122,508358,514628,520933,527272,533645,540053,546495,552972,559483,566029,572609,579223,585873,592557,599275,606027,612814,619635,626491,633381,640306,647265,654259,661288,668350,675447,682579,689745,696946,704181,711451,718755,726094,733467,740874,748315,755791,763302,770847,778427,786041,793689,801372,809089,816841,824627,832447,840302,848192,856116,864075,872068,880096,888158,896255,904386,912551,920751,928985,937253,945556,953893,962264,970670,979110,987585,996095,1004639,1013218,1021831,1030478,1039160,1047876,1056627,1065413,1074233,1083088,1091977,1100900,1109858,1118850,1127876,1136937,1146032,1155161,1164325,1173523,1182756,1192023,1201325,1210661,1220032,1229438,1238878,1248352,1257861,1267404,1276982,1286594,1296240,1305921,1315636,1325386,1335171,1345024,1354877,1364764,1374686,1384642,1394633,1404658,1414718,1424812,1434941,1445105,1455302,1465534,1475801,1486102,1496438,1506808,1517213,1527652,1538126,1548634,1559176,1569752,1580364,1591010,1601690,1612405,1623154,1633938,1644756,1655609,1666496,1677417,1688373,1699363,1710388,1721447,1732541,1743669,1754832,1766029,1777261,1788527,1799827,1811162,1822531,1833935,1845373,1856845,1868352,1879893,1891469,1903079,1914724,1926403,1938117,1949865,1961647,1973464,1985315];
	$newexp = $before_user_info['exp'] + $ret['base_reward_info']['player_exp'];
	$newcoin = $before_user_info['game_coin'] + $ret['base_reward_info']['game_coin'];
	$newlevel = $before_user_info['level'];
	
	$next_level_info = [['level' => $before_user_info['level'], 'from_exp' => $before_user_info['exp']]];
	while($newexp >= $exp[$newlevel]) {
		$newlevel ++;
		$next_level_info[] = ['level'=>$newlevel,' from_exp'=>$exp[$newlevel]];
		energyRecover($newlevel);
	}
	$user['level'] = $newlevel;
	$user['exp'] = $newexp;
	$envi->params['coin'] = $newcoin;
	
	$ret['reward_item_list'] = []; //TODO
	
	$ret['before_user_info'] = $before_user_info;
	$ret['after_user_info'] = runAction('user','userInfo')['user'];
	$ret['next_level_info'] = $next_level_info;
	
	$ret['event_info'] = [];
	$ret['event_info']['event_id'] = $challenge['event_id'];
	$ret['event_info']['event_point_info']['before_event_point'] = (int)$mysql->query("SELECT event_point FROM event_point WHERE user_id = ? AND event_id = ?",[$uid, $challenge['event_id']])->fetchColumn();
	$ret['event_info']['event_point_info']['before_total_event_point'] = $ret['event_info']['event_point_info']['before_event_point'];
	$ret['event_info']['event_point_info']['after_event_point'] = $ret['event_info']['event_point_info']['before_event_point'] + (int)$info['event_point'];
	$ret['event_info']['event_point_info']['after_total_event_point'] = $ret['event_info']['event_point_info']['after_event_point'];
	$ret['event_info']['event_point_info']['added_event_point'] = (int)$info['event_point'];
	
	$ret['event_info']['event_reward_info'] = [];//TODO
	$ret['event_info']['next_event_reward_info'] = ["event_point" => 100000, "rewards" => []];
	
	$mysql->query("UPDATE tmp_challenge_live SET event_point = 0, exp = 0, coin = 0, bonus = '[]', round = 1, live_difficulty_id = NULL WHERE user_id = ?", [$uid]);
	$mysql->query("UPDATE event_point SET event_point = ? WHERE user_id = ? AND event_id = ?", [$ret['event_info']['event_point_info']['after_total_event_point'], $uid, $challenge['event_id']]);
	return $ret;
}

//live失败
function challenge_gameover($post) {
	return [];
}