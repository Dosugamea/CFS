<?php
//battle.php SCORE MATCH相关功能
//battle/battleInfo 登录时请求的SCORE MATCH信息（若返回空，SM活动页面为空白）
function battle_battleInfo() {
	global $config;
	$ret = json_decode('[{
		"event_id": 0,
		"point_name": "PT",
		"event_point": 0,
		"total_event_point": 0,
		"event_battle_difficulty_m": [{
				"difficulty": 1,
				"capital_type": 1,
				"capital_value": 5
		}, {
				"difficulty": 2,
				"capital_type": 1,
				"capital_value": 10
		}, {
				"difficulty": 3,
				"capital_type": 1,
				"capital_value": 15
		}, {
				"difficulty": 4,
				"capital_type": 1,
				"capital_value": 25
		}, {
				"difficulty": 5,
				"capital_type": 1,
				"capital_value": 25
		}, {
				"difficulty": 6,
				"capital_type": 1,
				"capital_value": 25
		}]
	}]', true);
	$ret[0]['event_id'] = $config->event['battle']['event_id'];
	//$ret[0]['event_point'] = getEventPoint(97);
	//$ret[0]['total_event_point'] = getEventPoint(97);
	return $ret;
}
//活动pt
function getEventPoint($event_id){
	/*global $mysql, $uid;
	$event_point = $mysql->query("SELECT event_point FROM event_point WHERE user_id = ".$uid." AND event_id = ".$event_id)->fetchColumn();
	if($event_point)
		return (int)$event_point;
	else{
		$mysql->query("INSERT INTO event_point (user_id, event_point) VALUES(".$uid.", ".$event_id.")");
		return 0;
	}*/
	return 0;
}
//获取某个房间的详细信息，后面的函数均会用到的重复代码
//返回值为3项数组：result=>数据库中查询得到的房间信息 me=>自己在房间中的编号 ret=>后面所有部分均用到的公共返回值
function GetRoomData($id, $ready_only=true){
	global $mysql, $uid, $params;
	if (!is_numeric($id)) {
		die(); //防注入
	}
	$ret['result'] = $mysql->query('SELECT * FROM tmp_battle_room WHERE battle_event_room_id='.$id)->fetch();
	foreach ($ret['result'] as &$v) {
		if (is_numeric($v)) {
			$v = (int)$v;
		}
	}
	$common_ret['event_tmp_battle_room_id'] = (int)$id;
	$common_ret['event_id'] = 97;
	$common_ret['live_difficulty_id'] = $ret['result']['live_difficulty_id'];
	for ($i = 1; $i <= 4; $i++) {
		if($ret['result']['player'.$i] == 0) {
			continue;
		}
		if ($ready_only && $ret['result']['player_ready_'.$i] == 0) {
			if ($ret['result']['player'.$i] == $uid) {
				$next_id = max($ret['result']['player_ready_1'], $ret['result']['player_ready_2'], $ret['result']['player_ready_3'], $ret['result']['player_ready_4']) + 1;
				$mysql->prepare('UPDATE tmp_battle_room SET player_ready_'.$i.'=? WHERE battle_event_room_id=?')->execute([$next_id, $id]);
				$ret['result']['player_ready_'.$i] = $next_id;
			} else {
				continue;
			}
		}
		$sort[$i] = $ret['result']['player_ready_'.$i];
		$userdata['user_id'] = $ret['result']['player'.$i];
		if($userdata['user_id'] == $uid) {
			$ret['me'] = $i;
		}
		$user_info = $mysql->query('SELECT name,level,center_unit, award FROM users,user_deck WHERE user_deck.user_id='.$userdata['user_id'].' AND users.user_id=user_deck.user_id')->fetch();
		$userdata['name'] = $user_info['name'];
		$userdata['level'] = (int)$user_info['level'];
		$eventdata['total_event_point'] = getEventPoint(97);
		$eventdata['event_rank'] = 0;
		$user_list[] = $userdata['user_id'];
		$center_units[] = $user_info['center_unit'];
		$chat = [];
		if ($ret['result']['event_chat_id_'.$i] > 0) {
			$chat['chat_id'] = $ret['result']['event_chat_id_'.$i];
		}
		$common_ret['matching_user'][] = ['user_info'=>$userdata, 'event_status' => $eventdata, 'setting_award_id' => (int)$user_info['award'], 'chat'=>$chat];
	}
	array_multisort($sort, $common_ret['matching_user'], $center_units);
	$unit_detail = GetUnitDetail($center_units);
	loadExtendAvatar($user_list);
	foreach($common_ret['matching_user'] as $k => &$v) {
		$v['center_unit_info'] = $unit_detail[$k];
		$v['event_battle_rating_status'] =['rating' => 0, 'rating_rank' => 0, 'evaluation' => 0];
		setExtendAvatar($v['user_info']['user_id'], $v['center_unit_info']);
	}
	$common_ret['battle_player_num'] = count($common_ret['matching_user']);
	$common_ret['capacity'] = $common_ret['battle_player_num'];
	if ($common_ret['battle_player_num'] == 1)
		$common_ret['capacity'] = 4;
	$ret['ret'] = $common_ret;
	return $ret;
}

function battle_matching($post) {
	require '../config/modules_battle.php';
	global $uid, $mysql, $params;
	$live = getLiveDb();
	while (1) {
		$stmt = $mysql->prepare('SELECT ifnull((SELECT max(`battle_event_room_id`) FROM tmp_battle_room WHERE card_switch=? AND difficulty=?) ,0)');
		$stmt->execute([$params['card_switch'],$post['difficulty']]);
		$next_id_to_join=$stmt->fetchColumn();
		if ($next_id_to_join != 0) {
			$players = $mysql->query('SELECT player1,player2,player3,player4 FROM tmp_battle_room WHERE battle_event_room_id='.$next_id_to_join)->fetch(PDO::FETCH_NUM);
			$check_if_joined = $mysql->prepare('UPDATE tmp_battle_room SET timestamp=? WHERE battle_event_room_id=? AND start_flag=0 AND difficulty=?');
			foreach ($players as $v) {
				if ($v == $uid && $check_if_joined->execute([time(), $next_id_to_join, $post['difficulty']]) && $check_if_joined->rowCount()) {
					break 2;
				}
			}
			for ($i = 2; $i <= 4; $i++) {
				$join_room = $mysql->query("UPDATE tmp_battle_room SET player$i=$uid, timestamp=".time()." WHERE battle_event_room_id=$next_id_to_join AND start_flag=0 AND player$i=0 AND difficulty=".$post['difficulty']);
				if ($join_room && $join_room->rowCount()) {
					break 2;
				}
			}
		}
		$max_id_used = $mysql->query('SELECT ifnull((SELECT max(`battle_event_room_id`) as id FROM tmp_battle_room) ,0)')->fetchColumn();
		$next_id_to_join = $max_id_used + 1;
		$exist_live = $mysql->query('select notes_setting_asset from notes_setting')->fetchAll(PDO::FETCH_COLUMN);
		//TODO:允许所有live
		$allowed_live = $live->query('
			SELECT live_difficulty_id, notes_setting_asset FROM battle.event_battle_live_m
			LEFT JOIN live_setting_m ON live_setting_m.live_setting_id=battle.event_battle_live_m.live_setting_id
			WHERE notes_setting_asset in ("'.implode('","',$exist_live).'")
		')->fetchAll();
		$allowed_asset = array_map(function ($e) {
			return $e['notes_setting_asset'];
		}, $allowed_live);
		shuffle($score_match_live_lifficulty_ids[$post['difficulty']]);
		$live_picked = $score_match_live_lifficulty_ids[$post['difficulty']][mt_rand(0,count($score_match_live_lifficulty_ids[$post['difficulty']])-1)];
		pl_assert(array_search($live_picked[0], $exist_live), 'BATTLE 找不到谱面：'.$live_picked[0]);
		pl_assert(array_search($live_picked[0], $allowed_asset), 'BATTLE 官服没用过的谱面：'.$live_picked[0]);
		foreach ($allowed_live as $v) {
			if ($v['notes_setting_asset'] == $live_picked[0]) {
				$picked_live_difficulty_id = $v['live_difficulty_id'];
			}
		}
		$info = ['live_difficulty_id' => $picked_live_difficulty_id, 'random_switch' => $live_picked[1]];
		$stmt = $mysql->prepare('INSERT INTO `tmp_battle_room` (`battle_event_room_id`, `live_difficulty_id`,	`player1`, `difficulty`, `timestamp`,`card_switch`,`random_switch`) VALUES (?,?,?,?,?,?,?)');
		$stmt->execute([$max_id_used + 1, $picked_live_difficulty_id, $uid, $post['difficulty'], time(), $params['card_switch'], $live_picked[1]]);
		if ($stmt->rowCount()) {
			break;
		}
	}
	$mysql->query('replace into tmp_battle_user_room values(?, ?)', [$uid, $next_id_to_join]);
	$data = GetRoomData($next_id_to_join, false);
	$ret = $data['ret'];
	if (!isset($info)) {
		$info = $mysql->query('select live_difficulty_id, random_switch from tmp_battle_room where battle_event_room_id=?', [$next_id_to_join])->fetch();
	}
	$difficulty = $live->query('SELECT ifnull(battle.event_battle_live_m.stage_level, live_setting_m.stage_level) stage_level from live_setting_m left join battle.event_battle_live_m on live_setting_m.live_setting_id = battle.event_battle_live_m.live_setting_id where live_difficulty_id=?', [$info['live_difficulty_id']])->fetchColumn();
	$ret['live_difficulty_id'] = (int)$info['live_difficulty_id'];
	$ret['live_info'][0] = [
		'live_difficulty_id' => (int)$info['live_difficulty_id'],
		'use_quad_point' => $info['random_switch'] == 2,
		'is_random' => $info['random_switch'] > 0,
		'dangerous' => $difficulty >= 11,
	];
	$ret['start_wait_time'] = 30;
	return $ret;
}

function battle_battleDeckList() {
	return runAction('live','deckList',['ScoreMatch'=>true]);
}

function getMyRoom() {
	global $uid, $mysql;
	return $mysql->query('select room_id from tmp_battle_user_room where user_id=?', [$uid])->fetchColumn();
}

function battle_startWait($post) {
	global $mysql, $uid;
	$room_id = getMyRoom();
	$room = GetRoomData($room_id);
	$ret = $room['ret'];
	if ($post['chat_id'] != 0) {
		$column_name = 'event_chat_id_'.$room['me'];
		$column_name2 = 'player'.$room['me'];
		$mysql->exec("UPDATE tmp_battle_room SET $column_name={$post['chat_id']} WHERE battle_event_room_id={$room_id} AND $column_name2=$uid");
		$ret['matching_user'][$room['me']-1]['chat']['event_chat_id'] = (string)$post['chat_id'];
	}
	$ret['polling_interval'] = 2;
	if ($ret['battle_player_num'] == 1) {
		$ret['start_wait_time'] = 60;
		$ret['start_flag'] = false;
	} else {
		$ret['start_flag'] = false;
		$past_time = time() - $room['result']['timestamp'];
		$ret['start_wait_time'] = 60 - $past_time;
		if($past_time > 55 || ($ret['battle_player_num'] == 4 && $past_time > 15)){
			$ret['start_flag'] = true;
			if ($room['result']['start_flag'] == 0)
				$mysql->prepare('UPDATE tmp_battle_room set start_flag=1, event_chat_id_1="", event_chat_id_2="", event_chat_id_3="", event_chat_id_4="" WHERE battle_event_room_id=?')->execute([$room_id]);
		} else {
			$ret['capacity'] = 4;
		}
	}
	return $ret;
}

function battle_liveStart($post) {
	global $mysql;
	$room_id = getMyRoom();
	$stmt = $mysql->prepare('SELECT live_difficulty_id,random_switch FROM tmp_battle_room WHERE battle_event_room_id=?');
	$stmt->execute([$room_id]);
	$id = $stmt->fetch();
	return runAction('live','play',['live_difficulty_id'=>$id[0],'unit_deck_id'=>$post['unit_deck_id'],'random_switch'=>$id[1], 'ScoreMatch' => true]);
}

function battle_liveEnd($post) {
	global $uid, $mysql;
	$room_id = getMyRoom();
	$room = GetRoomData($room_id);
	$post['live_difficulty_id'] = $room['ret']['live_difficulty_id'];
	$post['ScoreMatch'] = true;
	$reward = runAction('live','reward',$post);
	$result['score_smile'] = $post['score_smile'];
	$result['score_cute'] = $post['score_cute'];
	$result['score_cool'] = $post['score_cool'];
	$result['max_combo'] = $post['max_combo'];
	$result['is_full_combo'] = (($reward['combo_rank'] == 1) ? true : false);
	$reward = json_encode($reward);
	$result = json_encode($result);
	$mysql->prepare('INSERT INTO `tmp_battle_result`(`user_id`, `battle_event_room_id`, `result`, `reward`) VALUES (?,?,?,?)')->execute([$uid, $room_id, $result, $reward]);
	$column_name = 'ended_flag_'.$room['me'];
	$mysql->exec("UPDATE `tmp_battle_room` SET $column_name=1,timestamp=".time()." WHERE battle_event_room_id=".$room_id);
	return [];
}

function battle_endWait($post) {
	global $mysql, $uid;
	$room_id = getMyRoom();
	$room = GetRoomData($room_id);
	$ret = $room['ret'];
	if ($post['chat_id'] != 0) {
		$column_name = 'event_chat_id_'.$room['me'];
		$column_name2 = 'player'.$room['me'];
		$mysql->exec("UPDATE tmp_battle_room SET $column_name={$post['chat_id']} WHERE battle_event_room_id={$room_id} AND $column_name2=$uid");
		$ret['matching_user'][$room['me']-1]['chat']['event_chat_id'] = (string)$post['chat_id'];
	}
	$ret['polling_interval'] = 2;
	$ended_flag = true;
	for ($i = 1; $i <= $room['ret']['battle_player_num']; $i++) {
		if ($room['result']['ended_flag_'.$i ]== 0) {
			$ended_flag = false;
		}
	}
	$past_time = time() - $room['result']['timestamp'];
	if ($past_time > 30) { //超时强制结束
		$ended_flag = true;
	}
	$ret['end_wait_time'] = 60 - $past_time;
	$ret['ended_flag'] = $ended_flag;
	return $ret;
}

function battle_endRoom($post) {
	global $mysql, $uid;
	$room_id = getMyRoom();
	$room = GetRoomData($room_id);
	$ret = $room['ret'];
	$stmt = $mysql->prepare('SELECT user_id, result, reward FROM tmp_battle_result WHERE battle_event_room_id=?');
	$stmt->execute([$room_id]);
	while ($result = $stmt->fetch()) {
		if ($result['user_id'] == $uid) {
			$reward = json_decode($result['reward'], true);
		}
		foreach ($ret['matching_user'] as &$v) {
			if ($v['user_info']['user_id'] != $result['user_id']) {
				continue;
			}
			$v['result'] = json_decode($result['result'], true);
			break;
		}
	}
	foreach($ret['matching_user'] as &$v) {
		if (!isset($v['result'])) {
			$v['result'] = ['score_smile'=>0,'score_cute'=>0,'score_cool'=>0,'max_combo'=>0,'is_full_combo'=>false];
		}
		$score[] = $v['result']['score_smile'] + $v['result']['score_cute'] + $v['result']['score_cool'];
	}
	array_multisort($score, SORT_DESC, $ret['matching_user']);
	for ($i = 1; $i <= count($ret['matching_user']); $i++) {
		$ret['matching_user'][$i - 1]['result']['battle_rank'] = $i;
	}
	$ret['polling_interval'] = 2;
	$past_time = time() - $room['result']['timestamp'];
	$ret['end_wait_time'] = 60 - $past_time;
	$reward['event_info'] = json_decode('{
			"event_id": 97,
			"event_point_info": {
					"before_event_point": 0,
					"before_total_event_point": 0,
					"after_event_point": 0,
					"after_total_event_point": 0,
					"added_event_point": 0,
					"clear_event_point": 0,
					"score_rank_rate": 1,
					"battle_rank_rate": 1,
					"combo_rank_rate": 1
			},
			"event_reward_info": [],
			"next_event_reward_info": []
				}');
	$ret['event_id'] = 97;
	return array_merge($ret, $reward);
}

function battle_gameover($post) {
	global $mysql;
	$room_id = getMyRoom();
	$room = GetRoomData($room_id);
	$column_name = 'ended_flag_'.$room['me'];
	$mysql->exec("UPDATE `tmp_battle_room` SET $column_name=1,timestamp=".time()." WHERE battle_event_room_id=".$room_id);
	$ret = json_decode('{
		"event_info": {
		"event_id": 97,
				"event_point_info": {
						"before_event_point": 0,
						"before_total_event_point": 0,
						"after_event_point": 0,
						"after_total_event_point": 0,
			"added_event_point": 0,
			"clear_event_point": 0
				},
				"event_reward_info": [],
				"next_event_reward_info": [],
				"extra_reward_info": []
		},
		"live_difficulty_id": '.$post['live_difficulty_id'].',
		"live_difficulty_id_list": ['.$post['live_difficulty_id'].']
}', true);
	$ret['after_user_info'] = runAction('user','userInfo')['user'];
	return $ret;
}

function battle_top() {
  return json_decode('{"event_status":{"total_event_point":0,"event_rank":0},"has_history":false}',true);
}

?>