<?php
//live/liveStatus 歌曲信息以及最高分
function live_liveStatus() {
	$getLiveList = function ($table) {
		global $mysql, $envi, $uid, $config;
		$live = getLiveDb();
		$max_live_difficulty_id = $config->basic['max_live_difficulty_id'];
		$exist_live = $mysql->query('SELECT notes_setting_asset FROM notes_setting')->fetchAll(PDO::FETCH_COLUMN);
		
		$exclude_setting = [];
		$extra_limit = '';
		switch ($table) {
			case 'normal_live_m': break;
			case 'special_live_m': $exclude_setting = $live->query('SELECT live_setting_id FROM normal_live_m')->fetchAll(PDO::FETCH_COLUMN); break;
			case 'event_marathon_live_m': 
				$table = 'marathon.' . $table;
				$exclude_setting = $live->query('SELECT live_setting_id FROM normal_live_m UNION SELECT live_setting_id FROM special_live_m')->fetchAll(PDO::FETCH_COLUMN);
				$extra_limit = ' AND random_flag = 0';
				break;
			default: trigger_error('getLiveList:错误的表');
		}
		if (count($exclude_setting)) {
			$extra_limit .= ' AND live_setting_m.live_setting_id NOT IN ('.implode(',', $exclude_setting).')';
		}
		$live_list = $live->query("
			SELECT notes_setting_asset, live_difficulty_id FROM $table LEFT JOIN live_setting_m ON $table.live_setting_id=live_setting_m.live_setting_id
			WHERE live_difficulty_id<=$max_live_difficulty_id AND notes_setting_asset in ('".implode("','", $exist_live)."') $extra_limit"
		)->fetchAll();
		
		$asset_to_id = [];
		foreach ($live_list as $v) {
			$asset_to_id[$v['notes_setting_asset']] = (int)$v['live_difficulty_id'];
		}
		
		$user_goals = $mysql->query('SELECT live_goal_reward_id FROM live_goal WHERE user_id='.$uid)->fetchAll(PDO::FETCH_COLUMN);
		$goals = $live->query('SELECT live_goal_reward_id, live_difficulty_id FROM live_goal_reward_m WHERE live_goal_reward_id IN ('.implode(',', $user_goals).') ORDER BY live_difficulty_id ASC')->fetchAll();
		
		$live_detail_list = $mysql->query('
			SELECT notes_setting_asset, clear_cnt, hi_combo_count, hi_score FROM live_ranking
			WHERE user_id='.$uid.' AND card_switch='.$envi->params['card_switch'].' AND random_switch='.$envi->params['random_switch'].'
			AND notes_setting_asset in ("'.implode('","', array_map(function ($e) {
				return $e['notes_setting_asset'];
			}, $live_list)).'")
		')->fetchAll(PDO::FETCH_ASSOC);
		$generate_live_list = function ($v) use ($envi, $goals, $asset_to_id) {
			$id = isset($v['notes_setting_asset']) ? $asset_to_id[$v['notes_setting_asset']] : $v;
			$ret = [
				'live_difficulty_id'		=> (int)$id,
				'clear_cnt'					=> isset($v['clear_cnt']) ? (int)$v['clear_cnt'] : 0,
				'hi_combo_count'			=> isset($v['hi_combo_count']) ? (int)$v['hi_combo_count'] : 0,
				'hi_score'					=> isset($v['hi_score']) ? (int)$v['hi_score'] : 0,
				'status'					=> isset($v['clear_cnt']) ? 2 : 1,
				'achieved_goal_id_list'		=> array_values(array_map(function ($e) {
					return (int)$e['live_goal_reward_id'];
				}, array_filter($goals, function ($e) use ($id) {
					return $e['live_difficulty_id'] == $id;
				})))
			];
			if ($envi->params['random_switch']){
				$ret['is_random'] = ($envi->params['random_switch'] > 0);
			}
			return $ret;
		};
		$live_detail_list = array_map($generate_live_list, $live_detail_list);
		$live_without_rank = array_map($generate_live_list, array_diff($asset_to_id, array_map(function ($e) {
			return $e['live_difficulty_id'];
		}, $live_detail_list)));
		return array_values(array_merge($live_detail_list, $live_without_rank));
	};
	return [
		'normal_live_status_list'	=> $getLiveList('normal_live_m'),
		'special_live_status_list'	=> $getLiveList('special_live_m'),
		'marathon_live_status_list'	=> $getLiveList('event_marathon_live_m')
	];
}

//live/schedule 获取活动曲列表
function live_schedule() {
	global $mysql, $envi, $config;
	$max_live_difficulty_id = $config->basic['max_live_difficulty_id'];
	$ret['event_list'] = [];

	$genEventInfo = function ($event, $category) use (&$ret){
		if(strtotime($event['start_date']) < time() && strtotime($event['end_date']) > time()){
			$ret['event_list'][] = [
				"event_id"					=> $event['event_id'],
				"event_category_id"			=> $category,
				"name"						=> $event['name'],
				"open_date"					=> $event['start_date'],
				"start_date"				=> $event['start_date'],
				"end_date"					=> $event['end_date'],
				"close_date"				=> $event['end_date'],
				"banner_asset_name"			=> $event['asset_path'],
				"banner_se_asset_name"		=> $event['asset_path_se'],
				"result_banner_asset_name"	=> $event['result_path'],
				"description"				=> $event['description'],
				"member_category"			=> 1
			];
		}
	};

	$genEventInfo($config->event['marathon'], 1);
	$genEventInfo($config->event['battle'], 2);
	$genEventInfo($config->event['festival'], 3);
	$genEventInfo($config->event['marathon'], 4);
	$genEventInfo($config->event['duty'], 6);
	
	$ret['limited_bonus_list']=[];
	
	$live = getLiveDb();
	$exist_live = $mysql->query('SELECT notes_setting_asset FROM notes_setting')->fetchAll(PDO::FETCH_COLUMN);
	$exist_setting = $live->query('SELECT live_setting_id, stage_level FROM live_setting_m WHERE notes_setting_asset IN ("'.implode('","', $exist_live).'")');
	$difficulty = [];
	$setting_ids = [];
	foreach($exist_setting as $v) {
		$difficulty[$v['live_setting_id']] = $v['stage_level'];
		$setting_ids[] = $v['live_setting_id'];
	}
	$live_list = $live->query("
		SELECT * FROM (
			SELECT live_difficulty_id, live_setting_id from special_live_m 
			UNION SELECT live_difficulty_id, live_setting_id from marathon.event_marathon_live_m 
		) WHERE live_difficulty_id <= $max_live_difficulty_id and live_setting_id in (".implode(',',$setting_ids).")"
	)->fetchAll();
	$ret['live_list'] = array_map(function ($v) use ($difficulty, $envi) {
		return [
			'live_difficulty_id' => (int)$v['live_difficulty_id'],
			'start_date' => date('Y').'-01-01 00:00:00',
			'end_date' => (date('Y')+1).'-12-31 23:59:59',
			'is_random' => ($envi->params['random_switch'] > 0)
		];
	}, $live_list);
	$ret['random_live_list'] = []; //TODO
	$ret['free_live_list'] = []; //??
	return $ret;
}

//live/partyList 获取嘉宾列表
function live_partyList() {
	global $envi, $mysql, $uid;
	if(!$envi->params['card_switch']) {
		return json_decode('{"party_list":[{"user_info":{"user_id":0,"name":"らぶらいぶ","level":1},"center_unit_info":{"unit_id":49,"level":1,"unit_skill_level":1,"max_hp":3,"smile":0,"cute":0,"cool":0,"love":0,"is_love_max":false,"is_level_max":false,"is_rank_max":true},"setting_award_id":1,"friend_status":1,"available_social_point":0}]}');
	}
	$default_party = json_decode('{"party_list":[{"user_info":{"user_id":-1,"name":"Default(smile)","level":1},"center_unit_info":{"unit_id":49,"level":1,"love":0,"unit_skill_level":1,"max_hp":3,"smile":0,"cute":0,"cool":0,"is_love_max":false,"is_level_max":false,"is_rank_max":false},"setting_award_id":1,"friend_status":1,"available_social_point":0},{"user_info":{"user_id":-2,"name":"Default(pure)","level":1},"center_unit_info":{"unit_id":40,"level":1,"love":0,"unit_skill_level":1,"max_hp":3,"smile":0,"cute":0,"cool":0,"is_love_max":false,"is_level_max":false,"is_rank_max":false},"setting_award_id":1,"friend_status":1,"available_social_point":0},{"user_info":{"user_id":-3,"name":"Default(cool)","level":1},"center_unit_info":{"unit_id":31,"level":1,"love":0,"unit_skill_level":1,"max_hp":3,"smile":0,"cute":0,"cool":0,"is_love_max":false,"is_level_max":false,"is_rank_max":false},"setting_award_id":1,"friend_status":1,"available_social_point":0}]}',true);
    
    $friend_list = runAction("friend", "list",["type" => 0])['friend_list'];
    $friend_id_list = [0];
	foreach($friend_list as &$i){
		$i["user_info"] = $i["user_data"];
		$i["user_info"]['user_id'] = (int)$i["user_info"]['user_id'];
		$i["user_info"]['level'] = (int)$i["user_info"]['level'];
		$i["available_social_point"] = 0;
		$i['friend_status'] = 1;
		unset($i["user_data"]);

		$friend_id_list[] = $i["user_info"]['user_id'];
	}
	$friend_ids = implode(',', $friend_id_list);
    
    $self_center = GetUnitDetail($mysql->query('SELECT center_unit FROM user_deck WHERE user_id='.$uid)->fetchColumn());
    $self = $mysql->query('SELECT user_id,name,level,award FROM users WHERE user_id='.$uid)->fetch(PDO::FETCH_ASSOC);
    $self_info['user_info'] = $self;
    $self_info["user_info"]['user_id'] = (int)$self_info["user_info"]['user_id'];
    $self_info["user_info"]['level'] = (int)$self_info["user_info"]['level'];
    $self_info['center_unit_info'] = $self_center;
    $self_info['setting_award_id'] = (int)$self['award'];
    $self_info['friend_status'] = 0;
    $self_info["available_social_point"] = 0;
	$friend_list []= $self_info;
    
    //TODO:拉取好友列表
	$non_friend = $mysql->query('
		SELECT tmp_live_playing.user_id, name, level, center_unit,award FROM tmp_live_playing
		LEFT JOIN users ON tmp_live_playing.user_id=users.user_id
		LEFT JOIN user_deck ON tmp_live_playing.user_id=user_deck.user_id
		WHERE play_count>0 AND tmp_live_playing.user_id!='.$uid.'
		AND tmp_live_playing.user_id NOT IN ('.$friend_ids.')
		ORDER BY rand() LIMIT 3
	')->fetchAll(PDO::FETCH_ASSOC);
	$center_unit = [];
	foreach ($non_friend as &$v) {
		foreach($v as &$v2) if(is_numeric($v2)) $v2=(int)$v2;
		$center_unit[] = $v['center_unit'];
	}
	$center_unit = GetUnitDetail($center_unit,true);
	foreach ($non_friend as $k => $v3) {
		$party = [];
		$party['user_info'] = $v3;
		$party['center_unit_info'] = $center_unit[$k];
		$party['setting_award_id'] = $party['user_info']['award'];
		unset($party['user_info']['center_unit'], $party['user_info']['award']);
		$party['friend_status'] = 0; //TODO
		$party['available_social_point'] = 0;
		$default_party['party_list'][] = $party;
	}
	$default_party['party_list'] = array_merge($default_party['party_list'], $friend_list);
	return $default_party;
}

//live/deckList 获取所有的可用卡组列表（4.0不调用，live_play里调用）
function live_deckList($post) {
global $uid, $mysql, $envi;
	if($envi->params['card_switch'] == 0) {
		if (isset($post['do_not_use_multiply']) && $post['do_not_use_multiply']) { //4.0计分修正
			$deck_ret = json_decode('[{"unit_deck_id": 1,"total_smile": 55000,"total_cute": 50000,"total_cool": 55000,"total_hp": 20,"unit_list":[{"smile":0, "cute":0, "cool":0},{"smile":0, "cute":0, "cool":0},{"smile":0, "cute":0, "cool":0},{"smile":0, "cute":0, "cool":0},{"smile":0, "cute":0, "cool":0},{"smile":0, "cute":0, "cool":0},{"smile":0, "cute":0, "cool":0},{"smile":0, "cute":0, "cool":0},{"smile":0, "cute":0, "cool":0}]},{"unit_deck_id": 2,"total_smile": 55000,"total_cute": 60500,"total_cool": 60500,"total_hp": 30,"unit_list":[{"smile":0, "cute":0, "cool":0},{"smile":0, "cute":0, "cool":0},{"smile":0, "cute":0, "cool":0},{"smile":0, "cute":0, "cool":0},{"smile":0, "cute":0, "cool":0},{"smile":0, "cute":0, "cool":0},{"smile":0, "cute":0, "cool":0},{"smile":0, "cute":0, "cool":0},{"smile":0, "cute":0, "cool":0}]},{"unit_deck_id": 3,"total_smile": 39940,"total_cute": 41072,"total_cool": 40940,"total_hp": 39,"unit_list":[{"smile":0, "cute":0, "cool":0},{"smile":0, "cute":0, "cool":0},{"smile":0, "cute":0, "cool":0},{"smile":0, "cute":0, "cool":0},{"smile":0, "cute":0, "cool":0},{"smile":0, "cute":0, "cool":0},{"smile":0, "cute":0, "cool":0},{"smile":0, "cute":0, "cool":0},{"smile":0, "cute":0, "cool":0}]}]',true);
		} else {
			$deck_ret = json_decode('[{"unit_deck_id": 1,"total_smile": 60500,"total_cute": 55000,"total_cool": 60500,"total_hp": 20,"unit_list":[{"smile":0, "cute":0, "cool":0},{"smile":0, "cute":0, "cool":0},{"smile":0, "cute":0, "cool":0},{"smile":0, "cute":0, "cool":0},{"smile":0, "cute":0, "cool":0},{"smile":0, "cute":0, "cool":0},{"smile":0, "cute":0, "cool":0},{"smile":0, "cute":0, "cool":0},{"smile":0, "cute":0, "cool":0}]},{"unit_deck_id": 2,"total_smile": 55000,"total_cute": 60500,"total_cool": 60500,"total_hp": 30,"unit_list":[{"smile":0, "cute":0, "cool":0},{"smile":0, "cute":0, "cool":0},{"smile":0, "cute":0, "cool":0},{"smile":0, "cute":0, "cool":0},{"smile":0, "cute":0, "cool":0},{"smile":0, "cute":0, "cool":0},{"smile":0, "cute":0, "cool":0},{"smile":0, "cute":0, "cool":0},{"smile":0, "cute":0, "cool":0}]},{"unit_deck_id": 3,"total_smile": 39940,"total_cute": 41072,"total_cool": 40940,"total_hp": 39,"unit_list":[{"smile":0, "cute":0, "cool":0},{"smile":0, "cute":0, "cool":0},{"smile":0, "cute":0, "cool":0},{"smile":0, "cute":0, "cool":0},{"smile":0, "cute":0, "cool":0},{"smile":0, "cute":0, "cool":0},{"smile":0, "cute":0, "cool":0},{"smile":0, "cute":0, "cool":0},{"smile":0, "cute":0, "cool":0}]}]',true);
		}
	} else {
		$deck_list = json_decode($mysql->query('SELECT json FROM user_deck WHERE user_id = ?', [$uid])->fetchColumn(), true);
		$deck_ret = [];
		foreach($deck_list as $deck) {//分别处理每一个小组
			if(count($deck['unit_deck_detail']) < 9)
				continue;

			$deck_ret[] = getDeckAttribute($deck,$post);
		}
	}
	if (isset($envi->params['extend_mods_life'])) {
		if ($envi->params['extend_mods_life'] == 1) {
			foreach($deck_ret as &$v) {
				$v['total_hp'] = 99999;
			}
		} else if ($envi->params['extend_mods_life'] == 2) {
			foreach($deck_ret as &$v) {
				$v['total_hp'] = 1;
			}
		}
	}
	return $deck_ret;
}

//live/play 获取游戏谱面
function live_play($post) {
	global $mysql, $uid, $envi;
	$livedb = getLiveDb();
	if(isset($post['festival'])) { //读取festival曲目列表
		$festival_lives = json_decode($mysql->query('SELECT lives FROM tmp_festival_playing WHERE user_id = ', [$uid])->fetchColumn(), true);
		foreach($festival_lives as $v) {
			$live_id_list[] = $v['live_difficulty_id'];
			$random[] = $v['random_switch'] + $envi->params['extend_mods_key'] * 10;
		}
		$energy_list = [null, 4, 8, 12, 20, 20, 20];
	} else {
		$live_id_list[0] = $post['live_difficulty_id'];
		$random[0] = (isset($post['random_switch']) ? $post['random_switch'] : $envi->params['random_switch']);
		$random[0] += $envi->params['extend_mods_key'] * 10;
		$energy_list = [null, 5, 10, 15, 25, 25, 25];
	}
	$post['do_not_use_multiply'] = false;
	$map_count = 0;
	$energy_use = 0;
	foreach($live_id_list as $k2 => $v2) {
		$live_settings = getLiveSettings($v2, 'notes_speed, difficulty, notes_setting_asset, member_category');
		if (isset($live_settings['member_category']) && $live_settings['member_category'] == 1) {
			$post['do_not_use_multiply'] = true; //4.0计分修正
		}
		$extra_flag = $livedb->query("SELECT ac_flag, swing_flag FROM special_live_m WHERE live_difficulty_id = ?", [$v2])->fetch();
		$live_map = $mysql->query('SELECT notes_list FROM notes_setting WHERE notes_setting_asset = ?', [$live_settings['notes_setting_asset']])->fetch();
		$live_info['live_difficulty_id'] = (int)$v2;
		$live_info['ac_flag'] = $extra_flag ? $extra_flag['ac_flag'] : 0;
		$live_info['swing_flag'] = $extra_flag ? $extra_flag['swing_flag']: 0;
		$live_info['notes_speed'] = floatval($live_settings['notes_speed']);
		$live_info['notes_list'] = json_decode($live_map['notes_list'],true);
		$live_info['dangerous'] = false;
		$live_info['is_random'] = $random[$k2] %10 > 0;
		$live_info['use_quad_point'] = $random[$k2] == 2;
		$live_info['guest_bonus'] = [];
		$live_info['sub_guest_bonus'] = [];
		$live_cost = $livedb->query("SELECT capital_value FROM special_live_m WHERE live_difficulty_id = ?", [$v2])->fetchColumn();
		if($live_cost){
			$energy_use += $live_cost;
		}else{
			$energy_use += $energy_list[(int)$live_settings['difficulty']];
		}
		$part1=[9,5,7];
		$part2=['Live','RandomLive','RandomLiveOld','RandomLiveLimitless','RandomLiveLimited'];
		$p1=$envi->params['extend_mods_key'];
		$p2=$random[$k2]%10;
		if($random[$k2]>0)
			$live_info['notes_list'] = call_user_func('generate'.$part2[$p2], $live_info['notes_list'],$part1[$p1]);

		if (isset($envi->params['extend_mods_vanish']) && $envi->params['extend_mods_vanish']) {
			foreach ($live_info['notes_list'] as &$set) {
				$set['vanish'] = $envi->params['extend_mods_vanish'];
			}
		}
		if (isset($envi->params['extend_mods_mirror']) && $envi->params['extend_mods_mirror']) {
			foreach ($live_info['notes_list'] as &$set) {
				if (isset($set['position']) && $set['position'] != 0) {
					$set['position'] = 10 - $set['position'];
				}
				if (isset($set['first_position'])) {
					$set['first_position'] = 10 - $set['first_position'];
				}
			}
		}
		if (isset($envi->params['extend_mods_speed']) && $envi->params['extend_mods_speed']) {
			foreach ($live_info['notes_list'] as &$set) {
				$set['speed'] = 1/(1+$envi->params['extend_mods_speed']/100);
			}
		}
		if (!isset($post['ScoreMatch']) && isset($envi->params['extend_mods_hantei_count']) && $envi->params['extend_mods_hantei_count']) {
			$live_info['guest_bonus'] = json_decode('{
				"bonus_id": 8,
				"guest_bonus_rarity": 1,
				"bonus_param": '.$envi->params['extend_mods_hantei_count'].',
				"guest_bonus_asset": "assets\/flash\/ui\/event\/img\/ef_812_017.png",
				"description": "PERFECT SUPPORT",
				"user_info": {
					"user_id": -4,
					"name": "",
					"level": 1
				},
				"center_unit_info": {
					"unit_id": 49,
					"is_rank_max": false,
					"is_love_max": false,
					"is_level_max": false
				},
				"setting_award_id": 2,
				"friend_status": 0
			}', true);
		}
		
		$map['live_list'][] = array("live_info" => Null);
		$map['live_list'][$map_count]['live_info'] = $live_info;
		$map_count += 1;
	}
	//无卡模式计算分数线
	if($envi->params['card_switch'] == 0 && $post['unit_deck_id'] < 3) {
		$total = calcScore(60500, $map['live_list']);
		$map['rank_info'] = json_decode('[{"rank":5,"rank_min":0},{"rank":4,"rank_min":'.($total*0.7).'},{"rank":3,"rank_min":'.($total*0.8).'},{"rank":2,"rank_min":'.($total*0.9).'},{"rank":1,"rank_min":'.($total*0.975).'}]');
	} else { //有卡模式读取分数线，所有曲目分数线直接相加
		foreach($live_id_list as $v2) {
			$rankinfo = getRankInfo($v2);
			if (!isset($rank)) {
				$rank = $rankinfo;
			} else {
				foreach($rankinfo as $k3 => $v3) {
					$rank[$k3]['rank_min'] += $v3['rank_min'] - 1;
				}
			}
		}
		$map['rank_info'] = $rank;
	}
	if (!isset($post['ScoreMatch'])) {
		$map['is_marathon_event'] = true;
		$map['marathon_event_id'] = 52;
	}
	if(isset($post['lp_factor']))
		$energy_result = energyDecrease($energy_use * $post['lp_factor']);
	else
		$energy_result = energyDecrease($energy_use);
	
	if($energy_result && isset($post['lp_factor']))
		$lp_factor = $post['lp_factor'];
	else if($energy_result && !isset($post['lp_factor']))
		$lp_factor = 1;
	else
		$lp_factor = 0.5;
	$current_energy = getCurrentEnergy();
	$map = array_merge($map, $current_energy);
	if (!isset($post['party_user_id'])) {
		$post['party_user_id'] = 0;
	}
	
	$deck = live_deckList($post);
	foreach($deck as $v) {
		if ($v['unit_deck_id'] == $post['unit_deck_id']) {
			foreach($map['live_list'] as &$j){
				$j['deck_info'] = $v;
			}
			break;
		}
	}
	if($envi->params['card_switch'] && $post['party_user_id'] > 0) {
		$mysql->query("
			INSERT INTO `tmp_live_playing` VALUES (?,?,?,1,?,0)
			ON DUPLICATE KEY UPDATE unit_deck_id = ?, party_user_id = ?, factor = ?, play_count=IF (play_count+1 < 6, play_count+1, 5)
		", [$uid, $post['unit_deck_id'], $post['party_user_id'], $lp_factor, $post['unit_deck_id'], $post['party_user_id'], $lp_factor]);
		$mysql->query("UPDATE `tmp_live_playing` SET `factor` = ?, `reward_flag` = 0, `play_count` = IF (play_count-1 > 0, play_count-1, 0) WHERE user_id = ?", [$lp_factor, $post['party_user_id']]);
	} else {
		$mysql->query("
			INSERT INTO `tmp_live_playing` VALUES (?,?,?,1,?,0)
			ON DUPLICATE KEY UPDATE unit_deck_id=?, party_user_id=?, factor = ?, play_count=play_count+1
		", [$uid, $post['unit_deck_id'], $post['party_user_id'], $lp_factor, $post['unit_deck_id'], $post['party_user_id'], $lp_factor]);
	}
	$mysql->query("UPDATE `tmp_live_playing` SET `reward_flag` = 0 WHERE user_id = ?", [$uid]);
	if(date("m-d") == '04-01'){
		$map['live_se_id'] = 99;
	}
	return $map;
}

//live/reward 获取奖励
function live_reward($post) {
	global $uid, $mysql, $envi;
	$live = getLiveDb();
	//验证访问合法性，有人反映有问题，不验了
	/*if (isset($post['ScoreMatch']) || isset($post['festival'])) {
		if (!isset($post['seed']) || !isset($post['key'])) {
			throw403('NO_SEED_OR_KEY');
		}
		$calcClearKeys = function ($live_difficulty_id, $score_smile, $score_pure, $score_cool, $max_combo, $love_cnt, $zero, $live_clear_cnt_from_start) use ($post) {
			$key = (int)$post['seed'];
			$str = "*%d*%d*";
			$current_key = $key % 7927;
			$list = array_map(function ($e) use (&$str, &$current_key) {
				$value = floor($e) + $current_key;
				$current_key = $value % 7927;
				$str = $str . "%d" . "*";
				return $value;
			}, [
				$live_difficulty_id,
				$score_smile,
				$score_pure,
				$score_cool,
				$max_combo,
				$love_cnt,
				$zero
			]);
			$hash = sha1(call_user_func_array('sprintf', array_merge([$str, $key, $live_clear_cnt_from_start], $list)));
			if ($hash != $post['key']) {
				//throw403('INVALID_KEY');
			}
		};
		if (isset($post['ScoreMatch'])) {
			$calcClearKeys($post['live_difficulty_id'], $post['score_smile'], $post['score_cute'], $post['score_cool'], $post['max_combo'], $post['love_cnt'], 0, 0);
		}
	}*/
	
	//防止重放攻击
	$rewarded = $mysql->query('SELECT reward_flag FROM tmp_live_playing WHERE user_id = ?', [$uid])->fetchColumn();
	if($rewarded != '0'){
		$ret = retError(3411); //ERROR_CODE_LIVE_PLAY_DATA_NOT_FOUND
		return $ret;
	}
	$mysql->query("UPDATE tmp_live_playing SET reward_flag = 1 WHERE user_id = ?", [$uid]);
	
	//客户端提交的分数
	$score = $post['score_smile'] + $post['score_cute'] + $post['score_cool'];
	
	//验证是不是unranked
	if(!isset($post['ScoreMatch'])) {
		$unranked = isset($envi->params['extend_mods_hantei_count']) && $envi->params['extend_mods_hantei_count'] != 0;
	} else {
		$unranked = false; //live/play已经限制了
	}
	if(isset($envi->params['extend_mods_speed']))
		if(abs($envi->params['extend_mods_speed'])>8)
			$unranked = true;
			
	if (!$envi->params['card_switch']) {
		$test_unranked = $mysql->query('SELECT unit_deck_id FROM tmp_live_playing WHERE user_id='.$uid)->fetchColumn();
		$unranked = ($test_unranked > 2);
	}
	/* 此处单首歌曲和FESTIVAL各使用一套代码来完成相同的功能 */
	
	if(!isset($post['festival'])) { //如果是单首歌曲
		$post['live_difficulty_id'] = (int)$post['live_difficulty_id'];
		$random = $envi->params['random_switch'] + $envi->params['extend_mods_key'] * 10;
		//读取live信息（消耗等）
		if (!isset($post['ScoreMatch'])) {
			$map_info = getLiveSettings($post['live_difficulty_id'], 'capital_type, capital_value, difficulty, c_rank_combo, b_rank_combo, a_rank_combo, s_rank_combo, c_rank_complete, b_rank_complete, a_rank_complete, s_rank_complete, notes_setting_asset');
		} else {
			$map_info = getLiveSettings($post['live_difficulty_id'], 'difficulty, c_rank_combo, b_rank_combo, a_rank_combo, s_rank_combo, notes_setting_asset');
			$map_info['capital_type'] = 1;
			$values = [null, 5, 10, 15, 25, 25, 25];
			$map_info['capital_value'] = $values[$map_info['difficulty']];
		}
		//读取谱面和显示边框
		$livedb = getLiveDb();
		$note_list = $mysql->query('SELECT notes_list FROM notes_setting WHERE notes_setting_asset="'.$map_info['notes_setting_asset'].'"')->fetchColumn();
		$extra_flag = $livedb->query("SELECT ac_flag, swing_flag FROM special_live_m WHERE live_difficulty_id = ?", [$post['live_difficulty_id']])->fetch(PDO::FETCH_ASSOC);
		
		$ret = [];
		$ret['live_info'] = [];
		$ret['live_info'][] = [
			"live_difficulty_id"=>(int)$post['live_difficulty_id'],
			"is_random"=>($random % 10 > 0),
			"ac_flag" => $extra_flag ? (int)$extra_flag['ac_flag'] : 0,
			"swing_flag" => $extra_flag ? (int)$extra_flag['swing_flag'] : 0];
		
		/* 更新最高分、计算评价 */
		
		//读取曲目当前模式下的最高分
		$hiscore=$mysql->query('
			SELECT clear_cnt,hi_score,hi_combo_count FROM live_ranking
			WHERE card_switch='.$envi->params['card_switch'].' AND user_id='.$uid.'
			AND random_switch='.$random.' AND notes_setting_asset="'.$map_info['notes_setting_asset'].'"'
		)->fetch();
		if (!empty($hiscore) && $hiscore['hi_score'] >= $score) {
			$ret['hi_score'] = (int)$hiscore['hi_score'];
			$ret['is_high_score'] = false;
		} else {
			$ret['hi_score'] = $score;
			$ret['is_high_score'] = true;
		}
		//获取最大combo数
		$max_combo = $map_info['s_rank_combo'];
		//如果卡片关闭，计算各评价的分数线
		if ($envi->params['card_switch']==0 && $test_unranked < 3) {
			$map = json_decode($note_list,true);
			$total = calcScore(60500, $map);
			$rank_info = json_decode('[{"rank":5,"rank_min":0},{"rank":4,"rank_min":'.($total*0.7).'},{"rank":3,"rank_min":'.($total*0.8).'},{"rank":2,"rank_min":'.($total*0.9).'},{"rank":1,"rank_min":'.($total*0.975).'}]',true);
		} else { //如果卡片开启，直接读取
			$rank_info = getRankInfo($post['live_difficulty_id']);
		}
		//combo评价的分数线
		$combo_rank_info = [$map_info['c_rank_combo'], $map_info['b_rank_combo'], $map_info['a_rank_combo'], $map_info['s_rank_combo']];
		//计算rank和combo评价
		foreach($rank_info as $v) {
			if ($score >= $v['rank_min']) {
				$ret['rank'] = $v['rank'];
			}
		}
		$ret['combo_rank'] = 5;
		foreach($combo_rank_info as $v) {
			if ($post['max_combo'] >= $v) {
				$ret['combo_rank']--;
			}
		}
		//如果以前插入过最高分，更新成绩数据
		if (!$unranked) {
			foreach(['perfect_cnt', 'great_cnt', 'good_cnt', 'bad_cnt', 'miss_cnt', 'max_combo'] as $idx) $post[$idx] = (int)$post[$idx];
			if (!empty($hiscore)) {
				$clear_cnt = $hiscore['clear_cnt'] + 1;
				$hi_combo_count = max($post['max_combo'], $hiscore['hi_combo_count']);
				if ($ret['is_high_score']) {
					$mysql->exec("UPDATE live_ranking SET clear_cnt=$clear_cnt, hi_combo_count=$hi_combo_count, hi_score={$ret['hi_score']},  ".
					             "mx_perfect_cnt = {$post['perfect_cnt']}, mx_great_cnt = {$post['great_cnt']}, mx_good_cnt = {$post['good_cnt']}, ".
					             "mx_bad_cnt = {$post['bad_cnt']}, mx_miss_cnt = {$post['miss_cnt']}, mx_max_combo = {$post['max_combo']} ".
					             "WHERE card_switch={$envi->params['card_switch']} AND random_switch=$random AND user_id=$uid AND notes_setting_asset='".$map_info['notes_setting_asset']."'");
				} else {
					$mysql->exec("UPDATE live_ranking SET clear_cnt=$clear_cnt, hi_combo_count=$hi_combo_count, hi_score={$ret['hi_score']}  ".
					             "WHERE card_switch={$envi->params['card_switch']} AND random_switch=$random AND user_id=$uid AND notes_setting_asset='".$map_info['notes_setting_asset']."'");
				}

			} else { //否则插入一条新的
				$hi_combo_count = $post['max_combo'];
				if ($ret['hi_score'] != 0) {
					$mysql->exec("INSERT INTO live_ranking VALUES ($uid, '{$map_info['notes_setting_asset']}', {$envi->params['card_switch']},$random, {$ret['hi_score']}, $hi_combo_count, 1,".
					             "{$post['perfect_cnt']}, {$post['great_cnt']}, {$post['good_cnt']}, {$post['bad_cnt']}, {$post['miss_cnt']}, {$post['max_combo']} ,0)");
				}
				$clear_cnt = 1;
			}
		}
		
		/* 计算基本奖励 */
		
		//消耗LP数对应的EXP数
		$exp_list=[
			1=>[
				1=>12,
				2=>26,
				3=>46,
				4=>83,
				5=>83,
				6=>83,
			],
			2=>[
				1=>12,
				2=>26,
				3=>46,
				4=>83
			]
		];
		//各rank对应的金币数
		$coin_list = [1=>[null,2250,1800,1200,600,300],2=>[null,3000,2400,1600,800,400],
						3=>[null,3750,3000,2000,1000,500],4=>[null,4500,3600,2400,1200,600],5=>[null,4500,3600,2400,1200,600],6=>[null,4500,3600,2400,1200,600]];
		//获取本次获得的EXP和金币
		$factor = (float)$mysql->query('SELECT factor FROM tmp_live_playing WHERE user_id='.$uid)->fetchColumn();
		$ret['base_reward_info']['player_exp'] = floor($factor * $exp_list[$map_info['capital_type']][$map_info['difficulty']]);
		$ret['base_reward_info']['game_coin'] = floor($factor * $coin_list[$map_info['difficulty']][$ret['rank']]);
		$ret['base_reward_info']['game_coin_reward_box_flag'] = false;
		$ret['base_reward_info']['social_point'] = 0;
		$party = $mysql->query('SELECT party_user_id FROM tmp_live_playing WHERE user_id='.$uid)->fetchColumn();
		
		if($party > 0) {
			$ret['base_reward_info']['social_point'] = 5; //TODO:好友写好后检测好友
		}
		$ret['base_reward_info'] = array_merge($ret['base_reward_info'], json_decode('
		{"player_exp_unit_max": {
				"before": 999,
				"after": 999
		},
		"player_exp_friend_max": {
				"before": 50,
				"after": 50
		},
		"player_exp_lp_max": {
				"before": 100,
				"after": 100
		}}' ,true));
		$ret['base_reward_info']['player_exp_lp_max']['before'] = getCurrentEnergy()['energy_max'];
		$live_accomplish = $mysql->query("SELECT * FROM live_accomplish WHERE user_id = ".$uid." AND notes_setting_asset = '".$map_info['notes_setting_asset']."'")->fetch(PDO::FETCH_ASSOC);
		if(!$live_accomplish && !isset($post['ScoreMatch']) && $map_info['difficulty'] >= 4){
			$mysql->query("INSERT INTO live_accomplish (user_id, notes_setting_asset) VALUES(".$uid.", '".$map_info['notes_setting_asset']."')");
			$ret['special_reward_info'][] = ["item_id" => 4, "add_type" => 3001, "amount" => 1, "item_category_id" => 0, "reward_box_flag" => false];
			$envi->params['loveca'] += 1;
		}
	} else {//如果是FESTIVAL
		//读取本次的曲目列表
		$lives = json_decode($mysql->query('SELECT lives FROM tmp_festival_playing WHERE user_id='.$uid)->fetchColumn(),true);
		foreach($lives as $v) {
			$live_id_list[]=$v['live_difficulty_id'];
		}
		
		//验证访问合法性，有人反映有问题，不验了 + 1
		//$calcClearKeys($live_id_list[0], $post['score_smile'], $post['score_cute'], $post['score_cool'], $post['max_combo'], $post['love_cnt'], 0, 0);
		
		$live_settings = [];
		foreach($live_id_list as $k => $v) {
			$live_settings[$k] = getLiveSettings($v, 'c_rank_combo, b_rank_combo, a_rank_combo, s_rank_combo, notes_setting_asset, difficulty');
			$live_settings[$k]['capital_type'] = 1;
			$capital = [null, 5, 10, 15, 25, 25, 25];
			$live_settings[$k]['capital_value'] = $capital[$live_settings[$k]['difficulty']];
		}
		//读取所有曲目的谱面并合到一起
		$map = [];
		foreach($live_id_list as $k => $v2) {
			$live_map = $mysql->query('SELECT notes_list FROM notes_setting WHERE notes_setting_asset="'.$live_settings[$k]['notes_setting_asset'].'"')->fetchColumn();
			$map = array_merge($map, json_decode($live_map, true));
		}
		
		/* 计算评价 */
		
		//如果卡片关闭，用合并的谱面计算各评价的分数线
		if ($envi->params['card_switch'] == 0 && $test_unranked < 3) {
			$total = calcScore(60500, $map);
			$rank_info = json_decode('[{"rank":5,"rank_min":0},{"rank":4,"rank_min":'.($total*0.7).'},{"rank":3,"rank_min":'.($total*0.8).'},{"rank":2,"rank_min":'.($total*0.9).'},{"rank":1,"rank_min":'.($total*0.975).'}]',true);
		} else { //有卡模式读取分数线，所有曲目分数线直接相加
			foreach($live_id_list as $v2) {
				$rankinfo = getRankInfo($v2);
				if (!isset($rank)) {
					$rank = $rankinfo;
				} else {
					foreach($rankinfo as $k3 => $v3) {
						$rank[$k3]['rank_min'] += $v3['rank_min'] - 1;
					}
				}
			}
			$rank_info = $rank;
		}
		//获取combo数
		$combo_rank_info= [ 0,0,0,0];
		foreach ($live_id_list as $k => $v2) {
			foreach ($combo_rank_info as $k3 => &$cb) {
				$cb += $live_settings[$k][$k3];
			}
		}
		//计算rank和combo评价
		foreach($rank_info as $v) {
			if ($score >= $v['rank_min']) {
				$ret['rank'] = $v['rank'];
			}
		}
		$ret['combo_rank'] = 5;
		foreach($combo_rank_info as $v) {
			if ($post['max_combo'] >= $v) {
				$ret['combo_rank']--;
			}
		}
		
		/* 计算基本奖励 */
		
		//消耗LP数对应的EXP数
		$exp_list = [
			1=>[
				1=>[4=>9,5=>12,10=>12],
				2=>[6=>14,7=>17,8=>20,9=>23,10=>26,15=>26],
				3=>[9=>25,10=>29,12=>35,14=>42,15=>46,25=>46],
				4=>[25=>83],
				6=>[25=>83]
			],
			2=>[
				1=>[15=>12],
				2=>[30=>26],
				3=>[45=>46],
				4=>[75=>83]
			]
		];
		//各rank对应的金币数
		$coin_list = [1=>[null,2250,1800,1200,600,300],2=>[null,3000,2400,1600,800,400],
						3=>[null,3750,3000,2000,1000,500],4=>[null,4500,3600,2400,1200,600],6=>[null,4500,3600,2400,1200,600]];
		$ret['base_reward_info']['player_exp'] = 0;
		$ret['base_reward_info']['game_coin'] = 0;
		$ret['base_reward_info']['game_coin_reward_box_flag'] = false;
		$ret['base_reward_info']['social_point'] = 0;
		//每首曲子的奖励相加
		$factor = (float)$mysql->query('SELECT factor FROM tmp_live_playing WHERE user_id='.$uid)->fetchColumn();
		foreach($live_id_list as $v2) {
			$map_info = $live_settings[$k];
			$ret['base_reward_info']['player_exp'] += floor($factor * $exp_list[$map_info['capital_type']][$map_info['difficulty']][$map_info['capital_value']]);
			$ret['base_reward_info']['game_coin'] += floor($factor * $coin_list[$map_info['difficulty']][$ret['rank']]);
		}
		$ret['base_reward_info'] = array_merge($ret['base_reward_info'], json_decode('
		{"player_exp_unit_max": {
				"before": 999,
				"after": 999
		},
		"player_exp_friend_max": {
				"before": 50,
				"after": 50
		},
		"player_exp_lp_max": {
				"before": 100,
				"after": 100
		}}' ,true));
		$ret['base_reward_info']['player_exp_lp_max']['before'] = getCurrentEnergy()['energy_max'];
	}
	
	/* 分配基本奖励 */
	
	//客户端提交的绊点数
	$ret['total_love'] = floor($factor * $post['love_cnt']);
	//如果评价是无，经验值和绊点减半
	if ($ret['rank'] == 5) {
		$ret['base_reward_info']['player_exp'] = ceil($ret['base_reward_info']['player_exp'] / 2);
		$ret['total_love'] = ceil($ret['total_love'] / 2);
	}
	//1~360级的玩家经验值
	$exp = [0,6,12,20,30,43,59,79,103,131,165,204,250,302,362,430,506,591,685,789,904,1029,1166,1315,1477,1651,1839,2042,2259,2491,2738,3002,3283,3581,3891,4218,4563,4925,5304,5700,6113,6544,6992,7457,7940,8440,8957,9491,10042,10611,11196,11799,12419,13057,13711,14383,15072,15779,16502,17243,18001,18776,19569,20379,21206,22050,22911,23789,24685,25598,26528,27475,28440,29422,30421,31437,32470,33521,34589,35674,36776,37896,39033,40187,41358,42547,43753,44976,46216,47473,48748,50040,51349,52675,54018,55379,56757,58152,59565,60995,63889,66818,69781,72779,75811,78877,81978,85113,88283,91487,94726,97999,101306,104647,108023,111433,114879,118359,121873,125421,129004,132622,136274,139961,143682,147438,151228,155053,158911,162804,166732,170694,174690,178721,182786,186886,191020,195189,199392,203629,207901,212207,216548,220923,225332,229777,234256,238770,243318,247900,252516,257167,261853,266573,271327,276116,280939,285797,290689,295616,300577,305573,310603,315667,320766,325900,331068,336270,341507,346778,352084,357424,362799,368208,373652,379130,384642,390189,395770,401386,407036,412721,418440,424194,429982,435804,441661,447552,453478,459438,465432,471461,477524,483622,489754,495921,502122,508358,514628,520933,527272,533645,540053,546495,552972,559483,566029,572609,579223,585873,592557,599275,606027,612814,619635,626491,633381,640306,647265,654259,661288,668350,675447,682579,689745,696946,704181,711451,718755,726094,733467,740874,748315,755791,763302,770847,778427,786041,793689,801372,809089,816841,824627,832447,840302,848192,856116,864075,872068,880096,888158,896255,904386,912551,920751,928985,937253,945556,953893,962264,970670,979110,987585,996095,1004639,1013218,1021831,1030478,1039160,1047876,1056627,1065413,1074233,1083088,1091977,1100900,1109858,1118850,1127876,1136937,1146032,1155161,1164325,1173523,1182756,1192023,1201325,1210661,1220032,1229438,1238878,1248352,1257861,1267404,1276982,1286594,1296240,1305921,1315636,1325386,1335171,1345024,1354877,1364764,1374686,1384642,1394633,1404658,1414718,1424812,1434941,1445105,1455302,1465534,1475801,1486102,1496438,1506808,1517213,1527652,1538126,1548634,1559176,1569752,1580364,1591010,1601690,1612405,1623154,1633938,1644756,1655609,1666496,1677417,1688373,1699363,1710388,1721447,1732541,1743669,1754832,1766029,1777261,1788527,1799827,1811162,1822531,1833935,1845373,1856845,1868352,1879893,1891469,1903079,1914724,1926403,1938117,1949865,1961647,1973464,1985315];
	//调用user/userInfo获取用户信息
	$ret['before_user_info'] = runAction('user','userInfo')['user'];
	$newexp = $ret['before_user_info']['exp']+$ret['base_reward_info']['player_exp'];
	$newcoin = $ret['before_user_info']['game_coin']+$ret['base_reward_info']['game_coin'];
	$newlevel = $ret['before_user_info']['level'];
	//分配基本奖励并计算玩家新的等级
	$ret['next_level_info'] = [['level' => $ret['before_user_info']['level'], 'from_exp' => $ret['before_user_info']['exp']]];
	while ($newexp >= $exp[$newlevel]) {
		$newlevel++;
		$ret['next_level_info'][] = ['level'=>$newlevel,' from_exp'=>$exp[$newlevel]];
		energyRecover($newlevel);
	}
	$newsocial = $ret['before_user_info']['social_point'];
	$newloveca = $ret['before_user_info']['sns_coin'];
	
	/* 解锁目标并分配对应奖励 */
	
	if ($unranked) {
		$ret['goal_accomp_info'] = ['achieved_ids'=>[], 'rewards'=>[]];
	}
	
	if (!isset($post['ScoreMatch']) && !isset($post['festival']) && !$unranked) { //只有单首歌曲才有目标
		$ret['goal_accomp_info'] = ['achieved_ids'=>[], 'rewards'=>[]];
		$got_goals = $mysql->query('SELECT live_goal_reward_id FROM live_goal WHERE user_id='.$uid)->fetchAll(PDO::FETCH_COLUMN);
		//获取剩下的目标，这些目标是后面要验证是否解锁的
		if ($got_goals) {
			$goalinfo = $live->query('SELECT * FROM live_goal_reward_m WHERE live_difficulty_id='.$post['live_difficulty_id'].' and live_goal_reward_id not in ('.implode(',',$got_goals).')');
		} else {
			$goalinfo = $live->query('SELECT * FROM live_goal_reward_m WHERE live_difficulty_id='.$post['live_difficulty_id']);
		}
		//遍历所有目标并判断条件是否符合
		$setgoal = [];
		$clear_rank_info = [$map_info['c_rank_complete'], $map_info['b_rank_complete'], $map_info['a_rank_complete'], $map_info['s_rank_complete']];
		while ($goal = $goalinfo->fetch(PDO::FETCH_ASSOC)) {
			switch($goal['live_goal_type']) {
			case 1:if($ret['rank'] <= $goal['rank']) $setgoal[] = $goal;break;
			case 2:if($ret['combo_rank'] <= $goal['rank']) $setgoal[] = $goal;break;
			case 3:if($clear_cnt >= $clear_rank_info[4 - $goal['rank']]) $setgoal[] = $goal;
			}
		}
		//如果有符合的目标
		if (!empty($setgoal)) {
			foreach($setgoal as &$vv) foreach ($vv as &$v3) if(is_numeric($v3)) $v3=(int)$v3;
			//遍历符合的目标，组装插入语句和返回值，并分配奖励
			foreach($setgoal as $v) {
				$unlocked_goals[] = '('.$v['live_goal_reward_id'].','.$uid.')';
				$ret['goal_accomp_info']['achieved_ids'][] = $v['live_goal_reward_id'];
				unset($v['live_goal_type'], $v['item_option'], $v['rank']);
				$v['reward_box_flag'] = false;
				$ret['goal_accomp_info']['rewards'][] = $v;
				switch($v['add_type']) {
				case 3001: $newloveca += $v['amount'];break;
				case 3002: $newsocial += $v['amount'];break;
				case 3000: $newcoin += $v['amount'];
				}
			}
			$mysql->exec('INSERT INTO live_goal (`live_goal_reward_id`,`user_id`) VALUES '.implode(',',$unlocked_goals));
		}
	}
	
	/* 卡片奖励，此部分代码【将被完全重写】 */
	
	$scout = function ($type, $ret_key, $miss_rate) use (&$ret, $uid, $mysql, $post) {
		global $max_unit_id;
		$random = rand(1, 100);
        if ($type == 1) {//Clear-ea/nm/hd Rank-any-ea/nm Combo-c-nm Combo-any-ea
			if($random <= 95) $rarity = 1; else $rarity = 2;//R 5%, N 95%
		} elseif($type == 2) {//Clear-ex/ma Rank-any-hd Combo-b/c-hd Combo-s/a/b-nm
			if($random<=80) $rarity = 2; elseif($random >= 99) $rarity = 4; elseif($random <= 95) $rarity = 3; else $rarity = 5;//UR 1%, SSR 4% SR 15%, R 80%
		} else {//Rank-any-ex/ma Combo-any-ex/ma Combo-s/a-hd
			if($random <= 20) $rarity = 4; else $rarity = 3;//UR 20%, SR 80%
		}
        
        $unit = getUnitDb();
        if(rand(1, 100)>$miss_rate)
            $unit_got = $unit->query("SELECT * FROM unit_m WHERE unit_id<='$max_unit_id' and rarity=$rarity order by random() limit 1")->fetch();
        else
            $unit_got = $unit->query("SELECT * FROM unit_m WHERE unit_id=1170 limit 1")->fetch();
        $unit_id = $unit_got['unit_id'];
		$result = addUnit($unit_id);
		$result[0]['new_unit_flag']=false;
		$result[0]['is_support_member']=$unit_got['disable_rank_up'] != 0;
		$result[0]['skill_level']=1;
		if (isset($post['festival'])) {
			$reward_name = 'reward_item_list';
			$result[0]['add_type'] = 1001;
			$result[0]['amount'] = 1;
			$result[0]['item_category_id'] = 0;
			$result[0]['rarity'] = $rarity - 1;
		} else {
			$reward_name='reward_unit_list';
		}
		$ret[$reward_name][$ret_key] = $result;
	};
	$clear_card = [1=>1,2=>1,3=>1,4=>2,5=>2,6=>2];
	$rank_card = [1=>[1,1,1,1],2=>[1,1,1,1],
					3=>[2,2,2,2],4=>[3,3,3,3],5=>[3,3,3,3],6=>[3,3,3,3]];
	$combo_card = [1=>[1,1,1,1],2=>[2,2,2,1],
					3=>[3,3,2,2],4=>[3,3,3,3],5=>[3,3,3,3],6=>[3,3,3,3]];
	if (isset($post['festival'])) {
		$ret['reward_item_list']['live_clear'] = [];
		$ret['reward_item_list']['live_rank'] = [];
		$ret['reward_item_list']['live_combo'] = [];
		$ret['reward_item_list']['guest_bonus'] = [];
	} else {
		$ret['reward_unit_list']['live_clear'] = [];
		$ret['reward_unit_list']['live_rank'] = [];
		$ret['reward_unit_list']['live_combo'] = [];
	}
	if ($envi->params['card_switch'] && !isset($post['no_card'])) { //post的nocard是为了CF活动准备。
        $miss_rate=floor($post['miss_cnt']*10 / $map_info['s_rank_combo'])*10;
		$scout($clear_card[$map_info['difficulty']],'live_clear',$miss_rate);
		if ($ret['rank'] < 5) {
			$scout($rank_card[$map_info['difficulty']][$ret['rank'] - 1], 'live_rank',$miss_rate);
		}
		if ($ret['combo_rank'] < 5) {
			$scout($combo_card[$map_info['difficulty']][$ret['combo_rank'] - 1],'live_combo',$miss_rate);
		}
	}
	
	/* 分配绊点 */
	
	$max_love = [null,[null,25,100,250,500,375], [null,50,200,500,1000,750]];
	$love_max_num = 0;
	$center_love_max = false;
	$total_love = $ret['total_love'];
	$unitid_list = [];
	$love_list = [];
	$deck_ii = [];//记录卡组完整信息

	//如果卡片被禁用，返回定值
	if($envi->params['card_switch']==0) {
		$deck_ii = [
			'unit_deck_id' => 0,
			'total_smile'  => 60500,
			'total_cute'   => 55000,
			'total_cool'   => 60500,
			'total_hp'     => 20,
			"unit_list"    => []
		];
		$ret['unit_list']=json_decode('[{"position": 1,"unit_owning_user_id": 2,"unit_id": 41,"rank": 2,"exp": 0,"love": 0,"before_love": 0,"unit_skill_level": 1,"favorite_flag": false,"insert_date": "2014-10-25 14:29:35","attribute": 2,"smile": 1290,"cool": 1670,"max_hp": 4,"max_level": 60,"max_love": 200,"max_rank": 2,"level": 1,"is_level_max": false,"is_love_max": false,"is_rank_max": true,"is_skill_level_max": false,"next_exp": 14,"hp": 3,"cute": 2340}, {"position": 2,"unit_owning_user_id": 3,"unit_id": 42,"rank": 2,"exp": 0,"love": 0,"before_love": 0,"unit_skill_level": 1,"favorite_flag": false,"insert_date": "2014-10-25 14:29:35","attribute": 2,"smile": 1230,"cool": 1010,"max_hp": 4,"max_level": 60,"max_love": 200,"max_rank": 2,"level": 1,"is_level_max": false,"is_love_max": false,"is_rank_max": true,"is_skill_level_max": false,"next_exp": 14,"hp": 3,"cute": 3160}, {"position": 3,"unit_owning_user_id": 4,"unit_id": 43,"rank": 2,"exp": 0,"love": 0,"before_love": 0,"unit_skill_level": 1,"favorite_flag": false,"insert_date": "2014-10-25 14:29:35","attribute": 2,"smile": 1470,"cool": 1150,"max_hp": 4,"max_level": 60,"max_love": 200,"max_rank": 2,"level": 1,"is_level_max": false,"is_love_max": false,"is_rank_max": true,"is_skill_level_max": false,"next_exp": 14,"hp": 3,"cute": 2690}, {"position": 4,"unit_owning_user_id": 5,"unit_id": 44,"rank": 2,"exp": 0,"love": 0,"before_love": 0,"unit_skill_level": 1,"favorite_flag": false,"insert_date": "2014-10-25 14:29:35","attribute": 2,"smile": 1180,"cool": 1630,"max_hp": 4,"max_level": 60,"max_love": 200,"max_rank": 2,"level": 1,"is_level_max": false,"is_love_max": false,"is_rank_max": true,"is_skill_level_max": false,"next_exp": 14,"hp": 3,"cute": 2500}, {"position": 5,"unit_owning_user_id": 1,"unit_id": 40,"rank": 2,"exp": 0,"love": 0,"before_love": 0,"unit_skill_level": 1,"favorite_flag": false,"insert_date": "2014-10-25 14:29:35","attribute": 2,"smile": 1110,"cool": 1560,"max_hp": 4,"max_level": 60,"max_love": 200,"max_rank": 2,"level": 1,"is_level_max": false,"is_love_max": false,"is_rank_max": true,"is_skill_level_max": false,"next_exp": 14,"hp": 3,"cute": 2640}, {"position": 6,"unit_owning_user_id": 6,"unit_id": 45,"rank": 2,"exp": 0,"love": 0,"before_love": 0,"unit_skill_level": 1,"favorite_flag": false,"insert_date": "2014-10-25 14:29:35","attribute": 2,"smile": 1260,"cool": 1570,"max_hp": 4,"max_level": 60,"max_love": 200,"max_rank": 2,"level": 1,"is_level_max": false,"is_love_max": false,"is_rank_max": true,"is_skill_level_max": false,"next_exp": 14,"hp": 3,"cute": 2480}, {"position": 7,"unit_owning_user_id": 7,"unit_id": 46,"rank": 2,"exp": 0,"love": 0,"before_love": 0,"unit_skill_level": 1,"favorite_flag": false,"insert_date": "2014-10-25 14:29:35","attribute": 2,"smile": 1360,"cool": 1000,"max_hp": 4,"max_level": 60,"max_love": 200,"max_rank": 2,"level": 1,"is_level_max": false,"is_love_max": false,"is_rank_max": true,"is_skill_level_max": false,"next_exp": 14,"hp": 3,"cute": 3040}, {"position": 8,"unit_owning_user_id": 8,"unit_id": 47,"rank": 2,"exp": 0,"love": 0,"before_love": 0,"unit_skill_level": 1,"favorite_flag": false,"insert_date": "2014-10-25 14:29:35","attribute": 2,"smile": 1020,"cool": 1280,"max_hp": 4,"max_level": 60,"max_love": 200,"max_rank": 2,"level": 1,"is_level_max": false,"is_love_max": false,"is_rank_max": true,"is_skill_level_max": false,"next_exp": 14,"hp": 3,"cute": 3100}, {"position": 9,"unit_owning_user_id": 9,"unit_id": 48,"rank": 2,"exp": 0,"love": 0,"before_love": 0,"unit_skill_level": 1,"favorite_flag": false,"insert_date": "2014-10-25 14:29:35","attribute": 2,"smile": 1200,"cool": 1660,"max_hp": 4,"max_level": 60,"max_love": 200,"max_rank": 2,"level": 1,"is_level_max": false,"is_love_max": false,"is_rank_max": true,"is_skill_level_max": false,"next_exp": 14,"hp": 3,"cute": 2440}]', true);
	} else { //否则执行分配
		//读取卡组
		$res = $mysql->query('SELECT json FROM user_deck WHERE user_id='.$uid)->fetchColumn();
		$deck = json_decode($res,true);
		$deckid = (int)$mysql->query('SELECT unit_deck_id FROM tmp_live_playing WHERE user_id='.$uid)->fetchColumn();
		$total_love+=0;//供调试使用
        $new_love_max=[];
		foreach($deck as $v) {
			if ($v['unit_deck_id'] != $deckid) {
				continue; //定位到当前的卡组
			}
			$deck_ii=getDeckAttribute($v,$post);
			//读取卡组中全部卡片的信息
			foreach($v['unit_deck_detail'] as $v2) {
				$unitlist[] = $v2['unit_owning_user_id'];
			}
			$units = GetUnitDetail($unitlist);
			//组装基本返回值，顺便判断满绊卡片数和center是否满绊
			foreach($v['unit_deck_detail'] as $k2=>$v2) {
				$ret['unit_list'][] = array_merge($units[$k2],$v2);
				if ($units[$k2]['love'] == $max_love[$units[$k2]['rank']][$units[$k2]['rarity']]) {
					if($v2['position'] == 5) {
						$center_love_max=true;
					}
					$love_max_num++;
				}
			}
			//第一轮分配：先为CENTER分配绊点
			foreach($ret['unit_list'] as &$v3) {
				$v3['before_love'] = $v3['love']; //补上返回值中加前的绊点
				if ($v3['position'] == 5 && !$center_love_max) { //如果center没满绊
					//给center分配30%的绊点
					$v3['love'] += ceil($total_love * 0.3);
					//如果超过了绊点上限，取绊点上限
					$v3['love'] = min($v3['love'], $max_love[$v3['rank']][$v3['rarity']]);
					//扣掉分配的绊点
					$total_love -= ($v3['love']-$v3['before_love']);
					//如果分配后绊满了，在下一轮分配中移除这张卡
					if ($v3['love'] == $max_love[$v3['rank']][$v3['rarity']]) {
						$love_max_num++;
						//并记录更新信息
						$unitid_list[] = $v3['unit_owning_user_id'];
						$love_list[] = $v3['love'];
                        if ((int)$v3['rank'] == 2) {
							$v3['is_love_max'] = true;
							$new_love_max []= $v3['unit_id'];
						}
					}
				}
			}
			//第二轮分配，如果有没满绊的卡
			if ($love_max_num != 9) {
				foreach($ret['unit_list'] as &$v3) {
					//遍历每张卡，如果绊没满
					if ($v3['love'] != $max_love[$v3['rank']][$v3['rarity']]) {
						//把剩余的绊点平均分配给所有没满的卡
						$v3['love'] += ceil($total_love / (9 - $love_max_num));
						//如果超过了绊点上限，取绊点上限
						if($v3['love'] >= $max_love[$v3['rank']][$v3['rarity']]) {
							$v3['love'] = $max_love[$v3['rank']][$v3['rarity']];
							//如果绊满了，记录下来
							if ((int)$v3['rank'] == 2) {
								$v3['is_love_max'] = true;
								$new_love_max []= $v3['unit_id'];
							}
						}
						//记录更新信息
						$unitid_list[] = $v3['unit_owning_user_id'];
						$love_list[] = $v3['love'];
					}
				}
			}
			//如果有获得绊点的卡片，组装更新语句
			if (!empty($love_list)) {
				$update_love = 'UPDATE unit_list SET love = CASE unit_owning_user_id';
				foreach($unitid_list as $k4 => $v4) {
					$update_love .= ' WHEN '.$v4.' THEN '.$love_list[$k4];
				}
				$update_love .= ' END WHERE unit_owning_user_id in ('.implode(',',$unitid_list).')';
				$mysql->exec($update_love);
			}
			//如果有新卡满绊，写相册信息，发1个心
			if (isset($new_love_max)) {
				foreach($new_love_max as $v) {
					$mysql->exec("UPDATE album SET love_max_flag=1 WHERE user_id=$uid and unit_id=$v");
					$mysql->exec("INSERT INTO incentive_list (user_id, incentive_item_id, amount, is_card, incentive_message) VALUES ($uid, 4, 1, 0, '與部員的絆達到MAX')");
				}
			}
			break;
		}
	}
	//以下的内容PLS不处理。event_info交由对应模块添加
	$ret = array_merge($ret,json_decode('{
				"unlocked_subscenario_ids": [],
				"event_info": [],
				"accomplished_achievement_list": [],
				"new_achievement_cnt": 0
		}',true));
	if(!isset($ret['special_reward_info']))
		$ret['special_reward_info'] = [];
	$ret['effort_point'] = [];
	$capacity_list = [null,100000,400000,1200000,2000000,4000000];
	$box_now = $mysql->query("SELECT * FROM effort_box WHERE user_id = ".$uid)->fetch();
	if($box_now == false){
		$mysql->query("INSERT INTO effort_box (user_id, box_id, point) VALUES(".$uid.",1,0)");
		$box_now = $mysql->query("SELECT * FROM effort_box WHERE user_id = ".$uid)->fetch();
	}
	
	$score = floor($score * $factor);
	$score_still = $score;
	do{
		$rewards = [];
		$is_full = $score > $capacity_list[(int)$box_now['box_id']] - (int)$box_now['point'];
		if($is_full){
			for($i=0;$i<3;$i++){
				$SIS=getRandomSIS((int)$box_now['box_id']);
				$rewards[] = [
				"rarity"           => 1,
				"item_id"          => $SIS,
				"add_type"         => 5500,
				"amount"           => 1,
				"item_category_id" => 0,
				"reward_box_flag"  => false,
				"insert_date"      => date("Y-m-d H:i:s",time())];
				addSIS($SIS);
			}
		}
		$ret['effort_point'][] = [
		//"limited_effort_event_id"		=> 1,
		"live_effort_point_box_spec_id"	=> (int)$box_now['box_id'],
		"capacity"						=> $capacity_list[(int)$box_now['box_id']],
		"before"						=> (int)$box_now['point'],
		"after"							=> $is_full?$capacity_list[(int)$box_now['box_id']]:(int)$box_now['point'] + $score,
		"rewards"						=> $rewards];
		$score_ = $score;
		$score += ((int)$box_now['point'] - $capacity_list[(int)$box_now['box_id']]);
		if($is_full){
			//根据分数生成箱子
			if($score_still < 10000){
				$box_now['box_id'] = 1;
				$box_now['point'] = 0;
			}else if($score_still < 40000){
				$box_now['box_id'] = 2;
				$box_now['point'] = 0;
			}else if($score_still < 120000){
				$box_now['box_id'] = 3;
				$box_now['point'] = 0;
			}else if($score_still < 200000){
				$box_now['box_id'] = 4;
				$box_now['point'] = 0;
			}else if($score_still < 400000){
				$box_now['box_id'] = 5;
				$box_now['point'] = 0;
			}else{
				$box_now['box_id'] = 5;
				$box_now['point'] = 0;
			}
			
		}
	}while($score > 0);
	$mysql->query("UPDATE effort_box SET box_id = ".(int)$box_now['box_id']." , point = ".((int)$box_now['point'] + $score_)." WHERE user_id = ".$uid);
	$ret['is_effort_point_visible'] = true;
	$ret['limited_effort_box'] = [];
	
	//每日奖励
	$daily_reward = $mysql->query("SELECT daily_reward FROM users WHERE user_id = ".$uid)->fetchColumn();
	if(date("Y-m-d",strtotime($daily_reward)) != date("Y-m-d",time())){
		$newloveca += 5;
		$newcoin += 1500000;
		$ret['daily_reward_info'][] = ["item_id" => 4, "add_type" => 3001, "amount" => 5, "item_category_id" => 0, "reward_box_flag" => false];
		$ret['daily_reward_info'][] = ["item_id" => 3, "add_type" => 3000, "amount" => 1500000, "item_category_id" => 0, "reward_box_flag" => false];
		$mysql->query("UPDATE users SET daily_reward = '".date("Y-m-d H:i:s",time())."' WHERE user_id = ".$uid);
	}else
		$ret['daily_reward_info'] = [];
	
	//写入奖励并返回新的用户信息
	global $user;
	if(!isset($post['no_card'])){
		$user['level'] = $newlevel;
		$user['exp'] = $newexp;
		$envi->params['coin'] = $newcoin;
	}
	$envi->params['social_point'] = $newsocial;
	$envi->params['loveca'] = $newloveca;
	$ret['after_user_info'] = runAction('user','userInfo');
	$ret['after_user_info'] = $ret['after_user_info']['user'];
	$ret['after_user_info']['energy_max'] = 100+(int)floor($newlevel/2);
	$ret['base_reward_info']['player_exp_lp_max']['after'] = getCurrentEnergy($newlevel)['energy_max'];
	$ret['can_send_friend_request'] = false;
	
	//记录live日志
	if(!isset($post['precise_score_log'])){
		$post['precise_score_log'] = [];
	}
	$mysql->query("INSERT INTO live_log 
	(user_id, live_difficulty_id, score, perfect_cnt, great_cnt, good_cnt, bad_cnt, miss_cnt, max_combo, precise_score_log, timeStamp)
	VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP())", 
	[$uid, $post['live_difficulty_id'], $score_still, $post['perfect_cnt'], $post['great_cnt'], $post['good_cnt'], $post['bad_cnt'], $post['miss_cnt'], $post['max_combo'], json_encode($post['precise_score_log'])]);
	
	//记录precise log
	if($post['precise_score_log'] != []){
		$current_deck = $ret['unit_list'];
		$bonus_ii=[0,0,0,0,0,0];//记录具体加成
		foreach($current_deck as $nn => &$i){
			if($deck_ii['unit_list']==[]){
				//关卡，无具体卡组详情
				$i['total_status'] = [
					"hp"	=> 0,
					"smile"	=> 0,
					"cute"	=> 0,
					"cool"	=> 0
				];
				$i['si_bonus'] = [
					"hp"	=> 0,
					"smile"	=> 0,
					"cute"	=> 0,
					"cool"	=> 0
				];
			}else{
				$i['total_status'] = [
					"hp"	=> $deck_ii['unit_list'][$nn]['hp'],
					"smile"	=> $deck_ii['unit_list'][$nn]['smile'] - $deck_ii['unit_list'][$nn]['cen_smile'],
					"cute"	=> $deck_ii['unit_list'][$nn]['cute'] - $deck_ii['unit_list'][$nn]['cen_cute'],
					"cool"	=> $deck_ii['unit_list'][$nn]['cool'] - $deck_ii['unit_list'][$nn]['cen_cool']
				];
				$i['si_bonus'] = [
					"hp"	=> 0,
					"smile"	=> $deck_ii['unit_list'][$nn]['sis_smile'],
					"cute"	=> $deck_ii['unit_list'][$nn]['sis_cute'],
					"cool"	=> $deck_ii['unit_list'][$nn]['sis_cool']
				];

				//记录总和
				$bonus_ii[0]+=$deck_ii['unit_list'][$nn]['sis_smile'];
				$bonus_ii[1]+=$deck_ii['unit_list'][$nn]['sis_cute'];
				$bonus_ii[2]+=$deck_ii['unit_list'][$nn]['sis_cool'];

				$bonus_ii[3]+=$deck_ii['unit_list'][$nn]['cen_smile'];
				$bonus_ii[4]+=$deck_ii['unit_list'][$nn]['cen_cute'];
				$bonus_ii[5]+=$deck_ii['unit_list'][$nn]['cen_cool'];
			}
		}
		$log_deck_info = [
			"live_difficulty_id"	=> $post['live_difficulty_id'],
			"total_status"			=> [
				"hp"	=> $deck_ii['total_hp'],
				"smile"	=> $deck_ii['total_smile'],
				"cute"	=> $deck_ii['total_cute'],
				"cool"	=> $deck_ii['total_cool']
			],
			"center_bonus" 			=> [
				"hp"	=> 0,
				"smile"	=> $bonus_ii[3],
				"cute"	=> $bonus_ii[4],
				"cool"	=> $bonus_ii[5]
			],
			"si_bonus"				=> [
				"hp"	=> 0,
				"smile"	=> $bonus_ii[0],
				"cute"	=> $bonus_ii[1],
				"cool"	=> $bonus_ii[2]
			],
			"unit_list"				=> $current_deck
		];
		$old_log = $mysql->query("SELECT * FROM live_precise_log 
			WHERE user_id = ? AND live_difficulty_id = ? AND skill = ?", 
			[$uid, $post['live_difficulty_id'], (int)$post['precise_score_log']['is_skill_on']])->fetch(PDO::FETCH_ASSOC);
		$write_flag = true;
		if($old_log){
			if((int)$old_log['perfect_cnt'] < $post['perfect_cnt'] || 
			((int)$old_log['perfect_cnt'] === $post['perfect_cnt'] && (int)$old_log['great_cnt'] > $post['great_cnt']) ||
			((int)$old_log['perfect_cnt'] === $post['perfect_cnt'] && (int)$old_log['great_cnt'] === $post['great_cnt'] && (int)$old_log['good_cnt'] > $post['good_cnt'])){
				$mysql->query("DELETE FROM live_precise_log WHERE user_id = ? AND live_difficulty_id = ? AND skill = ?", [$uid, $post['live_difficulty_id'], (int)$post['precise_score_log']['is_skill_on']]);
				$write_flag = true;
			}else{
				$write_flag = false;
			}
		}
		if($write_flag){
			$mysql->query("INSERT INTO live_precise_log 
				(user_id, live_difficulty_id, skill, perfect_cnt, great_cnt, good_cnt, precise_list, max_combo, deck_info, timestamp)
				VALUES(?,?,?,?,?,?,?,?,?,CURRENT_TIMESTAMP())",
				[$uid, $post['live_difficulty_id'], (int)$post['precise_score_log']['is_skill_on'], $post['perfect_cnt'], $post['great_cnt'], $post['good_cnt'], json_encode($post['precise_score_log']['precise_list']), $post['max_combo'], json_encode($log_deck_info)]);
		}
	}
	return $ret;
}

//live/continue 失败后继续游戏（不扣心）
function live_continue() {
	$ret['before_sns_coin'] = runAction('user','userInfo')['user']['sns_coin'];
	$ret['after_sns_coin'] = $ret['before_sns_coin'];
	return $ret;
}
//live/gameover 游戏结束时请求 实际上啥都不做
function live_gameover() {
	return [];
}

//live/preciseScore 精确度分数
function live_preciseScore($post){
	global $uid, $mysql;
	$log = $mysql->query("SELECT * FROM live_precise_log 
		WHERE user_id = ? AND live_difficulty_id = ?", 
		[$uid, $post['live_difficulty_id']])->fetchAll(PDO::FETCH_ASSOC);
	if(!$log){
		return retError(3421);
	}
	
	$result = [
		"on"	=> ["has_record" => false],
		"off"	=> ["has_record" => false]
	];
	
	foreach($log as $i){
		if($i['skill'] == "0"){
			$switch = "off";
		}else{
			$switch = "on";
		}
		
		$result[$switch]['has_record'] = true;
		$result[$switch]['random_seed'] = time();
		$result[$switch]['max_combo'] = (int)$i['max_combo'];
		$result[$switch]['update_date'] = $i['timestamp'];
		$result[$switch]['precise_list'] = json_decode($i['precise_list'], true);
		$result[$switch]['deck_info'] = json_decode($i['deck_info'], true);
	}
	return $result;
}

