<?php
//duty.php 协力相关
//获得当前协力活动信息
function duty_dutyInfo(){
    global $params, $mysql, $uid;
    if($params['card_switch']==0)
        return [];
    include("config/event.php");
	$event_point = (int)$mysql->query("SELECT event_point FROM event_point WHERE user_id = ? AND event_id = ?", [$uid, $duty['event_id']])->fetchColumn();
	if(!$event_point){
		$mysql->query("INSERT IGNORE INTO event_point VALUES(?,?,?,?)",[$uid, $duty['event_id'], 0, 0]);
		$event_point = 0;
	}
    $ret = json_decode(
        '{
            "base_info":{
                "event_id": 0,
                "asset_bgm_id": 201,
                "event_point": 0,
                "total_event_point": 0
            },
            "difficulty_list": [{
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
        }', true);
	$ret['base_info']['event_id'] = $duty['event_id'];
	$ret['base_info']['event_point'] = $event_point;
	$ret['base_info']['total_event_point'] = $event_point;
	return $ret;
}

//获得全体协作任务
function duty_allUserMission(){
    return json_decode('{
		"has_joined": true,
		"all_user_mission_list": [
			{
				"all_user_mission_id": 1,
				"all_user_mission_type": 1,
				"start_date": "2017-08-20 16:00:00",
				"end_date": "2017-08-21 14:59:59",
				"accomplished_value": 2785039489460,
				"current_flag": true,
				"goal_list": [
					{
						"goal_value": 10000000000000,
						"mission_rank": 7,
						"achieved": true,
						"reward": {
							"item_id": 2,
							"add_type": 3006,
							"amount": 1,
							"item_category_id": 0
						},
						"now_achieved": false,
						"is_added": false
					},{
						"goal_value": 10000000000000,
						"mission_rank": 6,
						"achieved": true,
						"reward": {
							"item_id": 2,
							"add_type": 3006,
							"amount": 1,
							"item_category_id": 0
						},
						"now_achieved": false,
						"is_added": false
					},{
						"goal_value": 1000000000000,
						"mission_rank": 1,
						"achieved": true,
						"reward": {
							"item_id": 2,
							"add_type": 3006,
							"amount": 1,
							"item_category_id": 0
						},
						"now_achieved": false,
						"is_added": false
					},
					{
						"goal_value": 700000000000,
						"mission_rank": 2,
						"achieved": true,
						"reward": {
							"item_id": 89,
							"add_type": 1001,
							"amount": 1,
							"rank_max_flag": false,
							"item_category_id": 0
						},
						"now_achieved": false,
						"is_added": false
					},
					{
						"goal_value": 500000000000,
						"mission_rank": 3,
						"achieved": true,
						"reward": {
							"item_id": 2,
							"add_type": 3002,
							"amount": 500,
							"item_category_id": 2
						},
						"now_achieved": false,
						"is_added": false
					},
					{
						"goal_value": 300000000000,
						"mission_rank": 4,
						"achieved": true,
						"reward": {
							"item_id": 3,
							"add_type": 3000,
							"amount": 5000,
							"item_category_id": 3
						},
						"now_achieved": false,
						"is_added": false
					}
				]
			}
		]
	}',true);
}

//获取活动pt和排名
function getUserEventStatus($uid, $event_id){
	global $mysql;
	$event_point = (int)$mysql->query("SELECT event_point FROM event_point WHERE user_id = ? AND event_id = ?", [$uid, $event_id])->fetchColumn();
	$rank = $mysql->query("SELECT rowNo FROM 
		(Select user_id, event_id, (@rowNum:=@rowNum+1) as rowNo
		From event_point,
		(Select (@rowNum :=0) ) b
		WHERE event_id = ?
		Order by event_point.event_point Desc) as unused
		WHERE user_id = ? AND event_id = ?",[$event_id, $uid, $event_id])->fetchColumn();
	return ["event_point" => $event_point, "rank" => (int)$rank];
}

//获得当前分数与排名
function duty_top() {
	global $uid, $mysql;
	include("config/event.php");
	$event_status = getUserEventStatus($uid, $duty['event_id']);
	$ret = [];
	$ret['event_status']['total_event_point'] = $event_status['event_point'];
	$ret['event_status']['event_rank'] = $event_status['event_point'] ? $event_status['rank'] : false;
	$ret['all_user_mission_rank'] = 7;//全体任务完成进度
	$ret['has_history'] = false;//打嗝历史，以后再加
    return $ret;
}

//进入匹配
function duty_matching($post) {
    //"difficulty":4,"event_id":102
    require_once 'includes/energy.php';
	require_once 'includes/live.php';
    global $uid, $mysql, $params;
    //第一步 - 查询数据库是否有同一难度(且开卡状态相同)的房间，如果有则加入
    $room = $mysql->query('SELECT * FROM tmp_duty_room
        WHERE difficulty=? AND card_switch=? AND full_flag=0',
        [$post['difficulty'],$params['card_switch']]);

    if($room->rowCount()>0){
        $room=$room->fetch(PDO::FETCH_ASSOC);

        for($i=1;$i<=4;$i++){//从1-4中寻找空位，有则记录
            if((int)$room['player'.$i]<=0||(int)$room['player'.$i]==$uid){
                $num=$i;
                break;
            }
        }
        if(!isset($num)||empty($num))//1-4全满
            trigger_error('duty_matching: 正在尝试加入一个已满的房间'.$id);
        $extra_query='';
        if($num==4)//第四个玩家写入full_flag
            $extra_query=',full_flag=1';
        $mysql->query('UPDATE tmp_duty_room 
            SET timestamp=?,player'.$num.'=? '.$extra_query.' 
            WHERE duty_event_room_id=?',
            [time(),$uid,$room['duty_event_room_id']]);

    }else{
    //第二步 - 无符合的房间，随机出目标歌曲，创建房间
		$room = [];
        require_once 'config/modules_duty.php';
        $maps = $duty_lifficulty_ids[$post['difficulty']];
        $map = $maps[rand(0,count($maps)-1)];
        $live = getLiveDb();
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
		
        $room_id = (int)$mysql->query('SELECT MAX(duty_event_room_id) FROM tmp_duty_room')->fetchColumn() + 1;
		
        $mysql->query('INSERT INTO tmp_duty_room 
            (duty_event_room_id, difficulty, live_difficulty_id, player1, timestamp, card_switch) 
            VALUES (?,?,?,?,?,?)',
            [$room_id,$post['difficulty'],$selected_live,$uid,time(),$params['card_switch']]);
        
        $num = 1;
        $room['duty_event_room_id'] = $room_id;
        $room['live_difficulty_id'] = $selected_live;
    }
    //第三步 - 统一返回数据
    $mysql->query('DELETE FROM tmp_duty_user_room 
        WHERE user_id=?',
        [$uid]);
    $mysql->query('INSERT INTO tmp_duty_user_room 
        (user_id,room_id,pos_id) VALUES (?,?,?)',
        [$uid,$room['duty_event_room_id'],$num]);
    $energy=getCurrentEnergy();
        
    $ret['event_id']=(int)$post['event_id'];
    $ret['room_id']=(int)$room['duty_event_room_id'];
    $ret['energy_full_time']=$energy['energy_full_time'];
    $ret['over_max_energy']=$energy['over_max_energy'];

    $ret['live_list'][0]['live_difficulty_id']=(int)$room['live_difficulty_id'];
    $ret['live_list'][0]['is_random']=false;

    $ret['event_team_duty']['mission_id']=1;
    $ret['event_team_duty']['mission_goal']=(int)(getRankInfo((int)$room['live_difficulty_id'])[4]['rank_min']*4*1.2);
    $ret['event_team_duty']['mission_rate']=120;
    $ret['event_team_duty']['mission_type']=1;
    $ret['event_team_duty']['mission_value']=1;

    $target=$params['card_switch']==1?9:3;
    for($i=1;$i<=$target;$i++){
        $temp['deck_id']=$i;
        $temp['event_team_duty_base_point']=0;//写死0话筒
        $ret['deck_bonus_list'][]=$temp;
    }

    return $ret;
    //"event_id":102,"room_id":279599,"energy_full_time":"","over_max_energy":0,
    //"live_list":[{"live_difficulty_id":600064,"is_random":false}],
    //"event_team_duty":{"mission_id":2,"mission_type":2,"mission_value":1,"mission_rate":66,"mission_goal":810},
    //"deck_bonus_list":[{"deck_id":1,"event_team_duty_base_point":5}]
}

function getMyDutyRoom() {
	global $uid, $mysql;
	return $mysql->query('SELECT room_id,pos_id,deck_id FROM tmp_duty_user_room WHERE user_id=?', [$uid])->fetch();
}

//等待期间多次查询，获取他人准备信息
function duty_startWait($post) {
    //event_id":102,"chat_id":"0-0","deck_id":2,room_id":279599
    global $uid, $mysql, $params;
	include("config/event.php");
    if($post['deck_id']<=0)
        return [];

    $mysql->query('UPDATE tmp_duty_user_room 
            SET deck_id=? 
            WHERE user_id=?',
            [$post['deck_id'],$uid]);
    $info=getMyDutyRoom();
    
    $room=$mysql->query('SELECT * FROM tmp_duty_room WHERE duty_event_room_id=?', [$info['room_id']])->fetch();

    //计算玩家数
    $sum0=4;
    for($i=1;$i<=4;$i++){
        if((int)$room['player'.$i]<=0){
            $sum0=$i-1;
            break;
        }
    }

    //计算已准备数量
    $room['player_ready_'.$info['pos_id']]=1;
    $sum=((int)$room['start_flag']==1)?4:0;
    for($i=1;$i<=4;$i++)
        $sum+=(int)$room['player_ready_'.$i];
    $start_flag=$sum>=4?1:0;//4人均准备即可开始

    $mysql->query('UPDATE tmp_duty_room 
        SET player_ready_'.$info['pos_id'].'=1,event_chat_id_'.$info['pos_id'].'=?,start_flag=?
        WHERE duty_event_room_id=?', 
        [$post['chat_id'],$start_flag,$info['room_id']]);

    $ret['event_id']=$post['event_id'];
    $ret['polling_interval']=3;
    $ret['player_num']=$sum0;
    $ret['start_wait_time']=99-(time()-(int)$room['timestamp']);
    $ret['start_flag']=$start_flag==1;
    $ret['capacity']=4;
    $ret['room_id']=$post['room_id'];


    for($i=1;$i<=4;$i++){
        $user_id=$room['player'.$i];
        if($user_id == 0)
            break;
		if($user_id < 0){
			$ret['matching_user'][] = json_decode('{"npc_info":{"npc_id":'.(0 - $i).',"name":"NPC","level":100},"event_status":{"total_event_point":0,"event_rank":0},"center_unit_info":{"unit_owning_user_id": 9819,"unit_id": 49,"rank": 1,"exp": 0,"love": 10,"unit_skill_exp": 0,"removable_skill": [],"removable_skill_count": 1,"favorite_flag": false,"display_rank": 1,"insert_date": "2017-07-21 00:52:41","is_support_member": false,"level": 1,"unit_skill_level": 1,"skill_level": 1,"max_hp": 4,"max_level": 40,"max_love": 100,"max_rank": 2,"is_level_max": false,"is_love_max": false,"is_rank_max": false,"is_skill_level_max": false,"next_exp": 14,"rarity": 2,"unit_removable_skill_capacity": 1,"is_removable_skill_capacity_max": false},"setting_award_id":1,"chat_id":"0-0","room_user_status":{"has_selected_deck":true,"event_team_duty_base_point":0}}', true);
			$mysql->query("UPDATE tmp_duty_room SET ended_flag_".$i." = 1");
			continue;
		}
        $user_info=runAction('profile','profileInfo',['user_id'=>$user_id]);
        $user_event = getUserEventStatus($user_id, $duty['event_id']);
		
        $user_info['event_status']['total_event_point'] = $user_event['event_point'];
        $user_info['event_status']['event_rank'] = $user_event['rank'];
        $user_info['chat_id'] = $room['event_chat_id_'.$i];
        $user_info['room_user_status']['event_team_duty_base_point']=0;
        $user_info['room_user_status']['has_selected_deck']=$room['player_ready_'.$i]==1;

        $ret['matching_user'][]=$user_info;
    }
	
	if((time() - (int)$room['timestamp']) > 60 && $ret['start_flag'] != 1){ //超过60s自动匹配bot
		while($sum0 < 4){
			$sum0 += 1;
			$ret['matching_user'][] = json_decode('{"npc_info":{"npc_id":'.(0 - $sum0).',"name":"NPC","level":100},"event_status":{"total_event_point":0,"event_rank":0},"center_unit_info":{"unit_owning_user_id": 9819,"unit_id": 49,"rank": 1,"exp": 0,"love": 10,"unit_skill_exp": 0,"removable_skill": [],"removable_skill_count": 1,"favorite_flag": false,"display_rank": 1,"insert_date": "2017-07-21 00:52:41","is_support_member": false,"level": 1,"unit_skill_level": 1,"skill_level": 1,"max_hp": 4,"max_level": 40,"max_love": 100,"max_rank": 2,"is_level_max": false,"is_love_max": false,"is_rank_max": false,"is_skill_level_max": false,"next_exp": 14,"rarity": 2,"unit_removable_skill_capacity": 1,"is_removable_skill_capacity_max": false},"setting_award_id":1,"chat_id":"0-0","room_user_status":{"has_selected_deck":true,"event_team_duty_base_point":0}}', true);
			$mysql->query("UPDATE tmp_duty_room 
				SET player_ready_".$sum0." = 1, event_chat_id_".$sum0." = '0-0', player".$sum0." = ?, full_flag = 1
				WHERE duty_event_room_id = ?", 
				[0 - $sum0,$info['room_id']]);
		}
		$ret['start_flag'] = 1;
		$ret['player_num'] = 4;
	}
	

    return $ret;
    //"event_id":102,"polling_interval":2,"player_num":4,"start_wait_time":5,"start_flag":true,"capacity":4,"room_id":279599
    //"matching_user":[{"user_info":{"user_id":0,"name":"","level":0},"event_status":{"total_event_point":0,"event_rank":0},"center_unit_info":{}"setting_award_id":0,"chat_id":"17-0","room_user_status":{"has_selected_deck":true,"event_team_duty_base_point":7}}]
}

//进入歌曲
function duty_liveStart($post) {
    //event_id":102,"room_id":279599
    global $uid, $mysql, $params;
	include("config/event.php");
    $info=getMyDutyRoom();
    $room=$mysql->query('SELECT * FROM tmp_duty_room WHERE duty_event_room_id=?', [$info['room_id']])->fetch();

    $ret=runAction('live','play',['live_difficulty_id'=>$room['live_difficulty_id'],'unit_deck_id'=>$info['deck_id'],'random_switch'=>0, 'ScoreMatch' => true]);

    $ret['event_team_duty']['duty_bonus_type']=2030;
    $ret['event_team_duty']['event_team_duty_bonus_value']=0;

    for($i=1;$i<=4;$i++){
        $user_id=$room['player'.$i];
		if($user_id == 0){
			break;
		}
        if($user_id < 0){
			$ret['matching_user'][] = json_decode('{"npc_info":{"npc_id":'.(0 - $i).',"name":"NPC","level":100},"event_status":{"total_event_point":0,"event_rank":0},"center_unit_info":{"unit_owning_user_id": 9819,"unit_id": 49,"rank": 1,"exp": 0,"love": 10,"unit_skill_exp": 0,"removable_skill": [],"removable_skill_count": 1,"favorite_flag": false,"display_rank": 1,"insert_date": "2017-07-21 00:52:41","is_support_member": false,"level": 1,"unit_skill_level": 1,"skill_level": 1,"max_hp": 4,"max_level": 40,"max_love": 100,"max_rank": 2,"is_level_max": false,"is_love_max": false,"is_rank_max": false,"is_skill_level_max": false,"next_exp": 14,"rarity": 2,"unit_removable_skill_capacity": 1,"is_removable_skill_capacity_max": false},"setting_award_id":1,"chat_id":"0-0","room_user_status":{"has_selected_deck":true,"event_team_duty_base_point":0}}', true);
			continue;
		}
		$user_info=runAction('profile','profileInfo',['user_id'=>$user_id]);
        $user_event = getUserEventStatus($user_id, $duty['event_id']);
		
        $user_info['event_status']['total_event_point'] = $user_event['event_point'];
        $user_info['event_status']['event_rank'] = $user_event['rank'];
        $user_info['chat_id']=$room['event_chat_id_'.$i];
        $user_info['room_user_status']['event_team_duty_base_point']=0;
        $user_info['room_user_status']['has_selected_deck']=true;

        $ret['matching_user'][]=$user_info;
    }
    return $ret;
    //"rank_info":[{"rank":5,"rank_min":0,"rank_max":68726}],
    //"live_list":[{"live_info":{"live_difficulty_id":600064,"is_random":false,"notes_speed":0.8,"notes_list":[],"deck_info":{"total_xxx":0,"prepared_hp_damage":0}}}],
    //"live_se_id":1,"available_live_resume":false,
    //"event_team_duty":{"duty_bonus_type":2030,"event_team_duty_bonus_value":19},
    //"matching_user":[{"user_info":{"user_id":0,"name":"","level":0},"event_status":{"total_event_point":0,"event_rank":0},"center_unit_info":{}"setting_award_id":0,"chat_id":"17-0","room_user_status":{"has_selected_deck":true,"event_team_duty_base_point":7}}]
}

//结束歌曲
function duty_liveEnd($post) {
    //"event_id":102,"good_cnt":0,"love_cnt":77,"room_id":279599
    //TODO TODO TODO
    global $uid, $mysql, $params;
    $info=getMyDutyRoom();
    /*$mysql->query('DELETE FROM tmp_duty_result
        WHERE user_id=?',
        [$uid]);*/

    $post['ScoreMatch'] = true;
	$post['live_difficulty_id'] = (int)$mysql->query("SELECT live_difficulty_id FROM tmp_duty_room WHERE duty_event_room_id = ?", [$post['room_id']])->fetchColumn();
    $reward = runAction('live','reward',$post);//进行歌曲结算

    $result['rank'] = $reward['rank'];
    $result['score']=$post['score_smile']+$post['score_cute']+$post['score_cool'];
	$result['max_combo'] = $post['max_combo'];
	$result['is_full_combo'] = (($reward['combo_rank'] == 1) ? true : false);

    $mysql->query('INSERT INTO tmp_duty_result
        (user_id,duty_event_room_id,result,reward) VALUES (?,?,?,?)',
        [$uid,$info['room_id'],json_encode($result),json_encode($reward)]);
    $mysql->query('UPDATE tmp_duty_room 
        SET ended_flag_'.$info['pos_id'].'=1,timestamp=?
        WHERE duty_event_room_id=?', 
        [time(),$info['room_id']]);
    return [];
}

//歌曲结算
function duty_endRoom($post) {
    //event_id":102,"room_id":279599
    global $uid, $mysql, $params;
	include_once("includes/live.php");
	include_once("includes/unit.php");
    $info=getMyDutyRoom();
    $room=$mysql->query('SELECT * FROM tmp_duty_room WHERE duty_event_room_id=?', [$info['room_id']])->fetch();
    $mysql->query('UPDATE tmp_duty_room 
        SET timestamp=?
        WHERE duty_event_room_id=?', 
        [time(),$info['room_id']]);
    $results = $mysql->query('SELECT user_id, result, reward 
        FROM tmp_duty_result WHERE duty_event_room_id=?', [$info['room_id']]);
    
	//为机器人填补分数
	if($results->rowCount() != 4){
		$result = $results->fetchAll(PDO::FETCH_ASSOC);
		$score_sum = 0;
		foreach($result as $j){
			$result_ = json_decode($j['result'], true);
			$score_sum += $result_['score'];
		}
		$score_avg = $score_sum / count($result);
		for($i = 1;$i <= 4; $i++){
			if($room['player'.$i] < 0){
				$score_bot = $score_avg * rand(70, 130) * 0.01;
				$result_bot = [];
				$result_bot['rank'] = 4;
				$result_bot['score'] = $score_bot;
				$result_bot['max_combo'] = 1;
				$result_bot['is_full_combo'] = (bool)rand(0,1);
				$mysql->query("INSERT INTO tmp_duty_result VALUES(?,?,?,?)", [(0 - $i), $room['duty_event_room_id'], json_encode($result_bot), "{}"]);
			}
		}
		$results = $mysql->query('SELECT user_id, result, reward 
			FROM tmp_duty_result WHERE duty_event_room_id=?', [$info['room_id']]);
	}
	
    $score_sum=0;
	$score_storage = [];

    while($result = $results->fetch(PDO::FETCH_ASSOC)){
        if ($result['user_id'] == $uid) {
			$reward = json_decode($result['reward'], true);
		}
		$result_ = json_decode($result['result'], true);
        $score_sum += (int)$result_['score'];
		if($result['user_id'] < 0){
			$user_info = [];
			$user_info['npc_info']['npc_id'] = (int)$result['user_id'];
			$user_info['npc_info']['name'] = "NPC";
			$user_info['npc_info']['level'] = 100;
			$user_info['center_unit_info'] = json_decode('{"unit_owning_user_id": 9819,"unit_id": 49,"rank": 1,"exp": 0,"love": 10,"unit_skill_exp": 0,"removable_skill": [],"removable_skill_count": 1,"favorite_flag": false,"display_rank": 1,"insert_date": "2017-07-21 00:52:41","is_support_member": false,"level": 1,"unit_skill_level": 1,"skill_level": 1,"max_hp": 4,"max_level": 40,"max_love": 100,"max_rank": 2,"is_level_max": false,"is_love_max": false,"is_rank_max": false,"is_skill_level_max": false,"next_exp": 14,"rarity": 2,"unit_removable_skill_capacity": 1,"is_removable_skill_capacity_max": false}', true);
			$user_info['setting_award_id'] = 1;
		}else{
			$user_info = runAction('profile','profileInfo',['user_id'=>$result['user_id']]);
		}
        $user_info['event_status']['total_event_point']=0;
        $user_info['event_status']['event_rank']=0;
        $user_info['room_user_status']['event_team_duty_base_point']=0;
        $user_info['room_user_status']['has_selected_deck']=true;

        $user_info['result']['rank']=$result_['rank'];
        $user_info['result']['status']=5;
        $user_info['result']['time_up']=false;
        $user_info['result']['score']=(int)$result_['score'];
        $user_info['result']['max_combo']=(int)$result_['max_combo'];
        $user_info['result']['is_full_combo']=(bool)$result_['is_full_combo'];
        $user_info['result']['mission_value']=(int)$result_['score'];
        $user_info['result']['all_user_mission_type']=1;
        $user_info['result']['all_user_mission_value']=(int)$result_['score'];
		
        $ret['matching_user'][] = $user_info;
		$score_storage [] = (int)$result_['score'];
    }
	rsort($score_storage);
	foreach($score_storage as $k => $i)
		foreach($ret['matching_user'] as &$j)
			if($i == $j['result']['score'])
				$j['result']['rank'] = $k + 1;
	foreach($ret['matching_user'] as $j)
		if(isset($j['user_info']) && $uid == $j['user_info']['user_id'])
			$my_rank = $j['result']['rank'];
				
    $ret['live_list'][0]['live_difficulty_id']=(int)$room['live_difficulty_id'];
    $ret['live_list'][0]['is_random']=false;

    $ret['event_team_duty']['mission_id'] = 1;
    $ret['event_team_duty']['mission_type'] = 1;
    $ret['event_team_duty']['mission_value'] = 1;
    $ret['event_team_duty']['mission_rate'] = 120;
    $ret['event_team_duty']['mission_goal'] = (int)(getRankInfo((int)$room['live_difficulty_id'])[4]['rank_min']*4*1.2);
    $ret['event_team_duty']['mission_result_value'] = $score_sum;
    $ret['event_team_duty']['mission_rank'] = getRank($score_sum,$room['live_difficulty_id']);
    $ret['event_team_duty']['mission_reward'] = [];

    $ret['event_id'] = $post['event_id'];
    $ret['room_id'] = $post['room_id'];
	
	//计算获得的活动pt
	$base_reward = [null, 39, 89, 153, 301, 301, 301];
	include("config/event.php");
	$reward['event_info']['event_id'] = $duty['event_id'];
	$reward['event_info']['event_point_info'] = [];
	$before_event = getUserEventStatus($uid, $duty['event_id']);
	$reward['event_info']['event_point_info']['before_event_point'] = $before_event['event_point'];
	$reward['event_info']['event_point_info']['before_total_event_point'] = $before_event['event_point'];
	$reward['event_info']['event_point_info']['after_event_point'] = $before_event['event_point'];
	$reward['event_info']['event_point_info']['after_total_event_point'] = $before_event['event_point'];
	$reward['event_info']['event_point_info']['base_event_point'] = $base_reward[$room['difficulty']];
	$reward['event_info']['event_point_info']['added_event_point'] = $base_reward[$room['difficulty']];
	switch($reward['rank']){
		case 1:
			$reward['event_info']['event_point_info']['score_rank_rate'] = 1.2;break;
		case 2:
			$reward['event_info']['event_point_info']['score_rank_rate'] = 1.15;break;
		case 3:
			$reward['event_info']['event_point_info']['score_rank_rate'] = 1.1;break;
		case 4:
			$reward['event_info']['event_point_info']['score_rank_rate'] = 1.05;break;
		default:
			$reward['event_info']['event_point_info']['score_rank_rate'] = 1;break;
	}
	switch($reward['combo_rank']){
		case 1:
			$reward['event_info']['event_point_info']['combo_rank_rate'] = 1.08;break;
		case 2:
			$reward['event_info']['event_point_info']['combo_rank_rate'] = 1.06;break;
		case 3:
			$reward['event_info']['event_point_info']['combo_rank_rate'] = 1.04;break;
		case 4:
			$reward['event_info']['event_point_info']['combo_rank_rate'] = 1.02;break;
		default:
			$reward['event_info']['event_point_info']['combo_rank_rate'] = 1;break;
	}
	switch($ret['event_team_duty']['mission_rank']){
		case 1:
			$reward['event_info']['event_point_info']['mission_rank_rate'] = 1.25;break;
		case 2:
			$reward['event_info']['event_point_info']['mission_rank_rate'] = 1.15;break;
		case 3:
			$reward['event_info']['event_point_info']['mission_rank_rate'] = 1.1;break;
		case 4:
			$reward['event_info']['event_point_info']['mission_rank_rate'] = 1.05;break;
		case 6:
			$reward['event_info']['event_point_info']['mission_rank_rate'] = 1.35;break;
		case 7:
			$reward['event_info']['event_point_info']['mission_rank_rate'] = 1.45;break;
		default:
			$reward['event_info']['event_point_info']['mission_rank_rate'] = 1;break;
	}
	switch($my_rank){
		case 1:
			$reward['event_info']['event_point_info']['team_rank_rate'] = 1.08;break;
		case 2:
			$reward['event_info']['event_point_info']['team_rank_rate'] = 1.05;break;
		case 3:
			$reward['event_info']['event_point_info']['team_rank_rate'] = 1.02;break;
		default:
			$reward['event_info']['event_point_info']['team_rank_rate'] = 1;break;
	}
	$reward['event_info']['event_point_info']['added_event_point'] = round($reward['event_info']['event_point_info']['added_event_point'] * $reward['event_info']['event_point_info']['score_rank_rate'] * $reward['event_info']['event_point_info']['combo_rank_rate'] * $reward['event_info']['event_point_info']['mission_rank_rate'] * $reward['event_info']['event_point_info']['team_rank_rate']);
	$reward['event_info']['event_point_info']['after_event_point'] += $reward['event_info']['event_point_info']['added_event_point'];
	$reward['event_info']['event_point_info']['after_total_event_point'] = $reward['event_info']['event_point_info']['after_event_point'];
	$mysql->query("UPDATE event_point SET event_point = event_point + ? WHERE user_id = ? AND event_id = ?", [$reward['event_info']['event_point_info']['added_event_point'], $uid, $duty['event_id']]);
	
	$event_reward_info = [];
	$eventdb = getEventDb();
	$point_count = $eventdb->query("SELECT * FROM event_point_count_m WHERE event_id = ? AND point_count >= ? AND point_count <= ?",[$duty['event_id'], $reward['event_info']['event_point_info']['before_event_point'], $reward['event_info']['event_point_info']['after_event_point']])->fetchAll(PDO::FETCH_ASSOC);
	$items_reward = [];
	foreach($point_count as $i){
		$item_reward = $eventdb->query("SELECT item_id, add_type, amount, item_category_id FROM event_point_count_reward_m WHERE event_point_count_reward_id = ?",[$i['event_point_count_id']])->fetch(PDO::FETCH_ASSOC);
		if(!$item_reward)
			trigger_error("找不到对应的奖励物品：".$i['event_point_count_reward_id']);
		$item_reward['reward_box_flag'] = false;
		$item_reward['required_event_point'] = $i['point_count'];
		//处理获得的物品
		switch($item_reward['add_type']){
			case 1001:
				addUnit($item_reward['item_id'], $item_reward['amount']);break;
			case 3000:
				$params['item3'] += $item_reward['amount'];break;
			case 3001:
				$params['item4'] += $item_reward['amount'];break;
			case 3002:
				$params['item2'] += $item_reward['amount'];break;
			case 1000:
				$params['item'.$item_reward['item_id']] += $item_reward['amount'];break;
			case 3006://贴纸
				switch($item_reward['item_id']){
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
				$mysql->query("INSERT IGNORE INTO award (user_id, award_id) VALUES(?, ?)", [$uid, $item_reward['item_id']]);break;
			case 5500://技能宝石
				$skill_check = $mysql->query("SELECT * FROM removable_skill WHERE user_id = ? AND skill_id = ?", [$uid, $res['item_id']])->fetch(PDO::FETCH_ASSOC);
				if($skill_check)
					$mysql->query("UPDATE removable_skill SET amount = amount + ? WHERE user_id = ? AND skill_id = ?", [$res['amount'], $uid, $res['item_id']]);
				else
					$mysql->query("INSERT INTO removable_skill (user_id, skill_id, amount) VALUES (?, ?, ?)", [$uid, $res['item_id'], $res['amount']]);
				break;
			default:
				trigger_error("获得了不支持的物品：".$item_reward['add_type']);
		}
		$items_reward[] = $item_reward;
	}
	$reward['event_info']['event_reward_info'] = $items_reward;
	
	$reward['event_info']['next_event_reward_info'] = [];
	$next_reward = $eventdb->query("SELECT * FROM event_point_count_m WHERE event_id = ? AND point_count > ?",[$duty['event_id'], $reward['event_info']['event_point_info']['after_event_point']])->fetch(PDO::FETCH_ASSOC);
	if($next_reward){
		$next_item = $eventdb->query("SELECT item_id, add_type, amount, item_category_id FROM event_point_count_reward_m WHERE event_point_count_reward_id = ?",[$next_reward['event_point_count_id']])->fetch(PDO::FETCH_ASSOC);
		$reward['event_info']['next_event_reward_info']['event_point'] = $next_reward['point_count'];
		$reward['event_info']['next_event_reward_info']['rewards'] = [$next_item];
	}
    
    return array_merge($ret, $reward);
    //"event_id":102,"room_id":279599,....,
    //"event_team_duty":{},"matching_user":[]
}
function getRank($score,$live_id){
	include_once("includes/live.php");
    $s_score=(float)(getRankInfo((int)$live_id)[4]['rank_min']*4*1.2);
    $rate=(float)$score/$s_score;
    if($rate>=1.5)  return 7;
    if($rate>=1.25) return 6;
    if($rate>=1)    return 1;
    if($rate>=0.7)  return 2;
    if($rate>=0.5)  return 3;
    if($rate>=0.3)  return 4;
                    return 5; 
}

//结束后多次查询他人信息以显示
function duty_endWait($post){
    //event_id":102,"chat_id":"0-0",room_id":279599
    global $uid, $mysql, $params;
	include("config/event.php");
    $info = getMyDutyRoom();
    $room=$mysql->query('SELECT * FROM tmp_duty_room WHERE duty_event_room_id=?', [$info['room_id']])->fetch(PDO::FETCH_ASSOC);

    //计算已结束数量
    $sum=0;
    for($i=1;$i<=4;$i++)
        $sum+=(int)$room['ended_flag_'.$i];

    $mysql->query('UPDATE tmp_duty_room 
        SET event_chat_id_'.$info['pos_id'].'=?
        WHERE duty_event_room_id=?', 
        [$post['chat_id'],$info['room_id']]);

    $ret['event_id']=$post['event_id'];
    $ret['polling_interval']=3;
    $ret['player_num']=4;
    $ret['end_wait_time']=30-(time()-(int)$room['timestamp']);
    $ret['end_flag']=$sum>=4;
    $ret['capacity']=4;
    $ret['room_id']=$post['room_id'];


    for($i=1;$i<=4;$i++){
        $user_id=$room['player'.$i];
        if($user_id == 0)
            break;
		if($user_id < 0){
			$ret['matching_user'][] = json_decode('{"npc_info":{"npc_id":'.(0 - $i).',"name":"NPC","level":100},"event_status":{"total_event_point":0,"event_rank":0},"center_unit_info":{"unit_owning_user_id": 9819,"unit_id": 49,"rank": 1,"exp": 0,"love": 10,"unit_skill_exp": 0,"removable_skill": [],"removable_skill_count": 1,"favorite_flag": false,"display_rank": 1,"insert_date": "2017-07-21 00:52:41","is_support_member": false,"level": 1,"unit_skill_level": 1,"skill_level": 1,"max_hp": 4,"max_level": 40,"max_love": 100,"max_rank": 2,"is_level_max": false,"is_love_max": false,"is_rank_max": false,"is_skill_level_max": false,"next_exp": 14,"rarity": 2,"unit_removable_skill_capacity": 1,"is_removable_skill_capacity_max": false},"setting_award_id":1,"chat_id":"0-0","room_user_status":{"has_selected_deck":true,"event_team_duty_base_point":0}}', true);
			$mysql->query("UPDATE tmp_duty_room SET ended_flag_".$i." = 1");
			continue;
		}
       		$user_info=runAction('profile','profileInfo',['user_id'=>$user_id]);
        $user_event = getUserEventStatus($user_id, $duty['event_id']);
		
        $user_info['event_status']['total_event_point'] = $user_event['event_point'];
        $user_info['event_status']['event_rank'] = $user_event['rank'];
        $user_info['chat_id']=$room['event_chat_id_'.$i];
        $user_info['room_user_status']['event_team_duty_base_point']=0;
        $user_info['room_user_status']['has_selected_deck']=true;

        $ret['matching_user'][]=$user_info;
    }

    return $ret;
}

//死亡
function duty_gameover($post) {
	global $uid, $mysql, $params;
	include("config/event.php");
    $info=getMyDutyRoom();
    $mysql->query('UPDATE tmp_duty_room 
        SET ended_flag_'.$info['pos_id'].'=1,timestamp=?
        WHERE duty_event_room_id=?', 
        [time(),$info['room_id']]);
		
	$base_reward = [null, 39, 89, 153, 301, 301, 301];
	$ret = [];
	$ret['event_info']['event_id'] = $duty['event_id'];
	$ret['event_info']['event_point_info'] = [];
	$before_event = getUserEventStatus($uid, $event_id);
	$ret['event_info']['event_point_info']['before_event_point'] = $before_event['event_point'];
	$ret['event_info']['event_point_info']['before_total_event_point'] = $before_event['event_point'];
	$ret['event_info']['event_point_info']['after_event_point'] = $before_event['event_point'] + ceil($base_reward[$info['difficulty']]/3);
	$ret['event_info']['event_point_info']['after_total_event_point'] = $before_event['event_point'] + ceil($base_reward[$info['difficulty']]/3);
	$ret['event_info']['event_point_info']['base_event_point'] = ceil($base_reward[$info['difficulty']]/3);
	$ret['event_info']['event_point_info']['added_event_point'] = ceil($base_reward[$info['difficulty']]/3);
	$ret['event_info']['event_point_info']['score_rank_rate'] = 1;
	$ret['event_info']['event_point_info']['combo_rank_rate'] = 1;
	$ret['event_info']['event_point_info']['team_rank_rate'] = 1;
	$ret['event_info']['event_point_info']['mission_rank_rate'] = 1;
	
	$event_reward_info = [];
	$eventdb = getEventDb();
	$point_count = $eventdb->query("SELECT * FROM event_point_count_m WHERE event_id = ? AND point_count >= ? AND point_count <= ?",[$duty['event_id'], $ret['event_info']['event_point_info']['before_event_point'], $ret['event_info']['event_point_info']['after_event_point']])->fetchAll(PDO::FETCH_ASSOC);
	$items_reward = [];
	foreach($point_count as $i){
		$item_reward = $eventdb->query("SELECT item_id, add_type, amount, item_category_id FROM event_point_count_reward_m WHERE event_point_count_reward_id = ?",[$i['event_point_count_reward_id']])->fetch(PDO::FETCH_ASSOC);
		if(!$item_reward)
			trigger_error("找不到对应的奖励物品：".$i['event_point_count_reward_id']);
		$item_reward['reward_box_flag'] = false;
		$item_reward['required_event_point'] = $i['point_count'];
		//处理获得的物品
		switch($item_reward['add_type']){
			case 1001:
				addUnit($item_reward['item_id'], $item_reward['amount']);break;
			case 3000:
				$params['item3'] += $item_reward['amount'];break;
			case 3001:
				$params['item4'] += $item_reward['amount'];break;
			case 3002:
				$params['item2'] += $item_reward['amount'];break;
			case 1000:
				$params['item'.$item_reward['item_id']] += $item_reward['amount'];break;
			case 3006://贴纸
				switch($item_reward['item_id']){
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
				$mysql->query("INSERT IGNORE INTO award (user_id, award_id) VALUES(?, ?)", [$uid, $item_reward['item_id']]);break;
			case 5500://技能宝石
				$skill_check = $mysql->query("SELECT * FROM removable_skill WHERE user_id = ? AND skill_id = ?", [$uid, $res['item_id']])->fetch(PDO::FETCH_ASSOC);
				if($skill_check)
					$mysql->query("UPDATE removable_skill SET amount = amount + ? WHERE user_id = ? AND skill_id = ?", [$res['amount'], $uid, $res['item_id']]);
				else
					$mysql->query("INSERT INTO removable_skill (user_id, skill_id, amount) VALUES (?, ?, ?)", [$uid, $res['item_id'], $res['amount']]);
				break;
			default:
				trigger_error("获得了不支持的物品：".$item_reward['add_type']);
		}
		$items_reward[] = $item_reward;
	}
	$ret['event_info']['event_reward_info'] = $items_reward;
	
	$ret['event_info']['next_event_reward_info'] = [];
	$next_reward = $eventdb->query("SELECT * FROM event_point_count_m WHERE event_id = ? AND point_count > ?",[$duty['event_id'], $ret['event_info']['event_point_info']['after_event_point']])->fetch(PDO::FETCH_ASSOC);
	if($next_reward){
		$next_item = $eventdb->query("SELECT item_id, add_type, amount, item_category_id FROM event_point_count_reward_m WHERE event_point_count_reward_id = ?",[$next_reward_id])->fetch(PDO::FETCH_ASSOC);
		$ret['event_info']['next_event_reward_info']['event_point'] = $next_reward['point_count'];
		$ret['event_info']['next_event_reward_info']['rewards'] = [$next_item];
	}
	$ret['extra_reward_info'] = [];
	
	$ret['live_difficulty_id'] = $post['live_difficulty_id'];
	$ret['live_difficulty_id_list'] = [$post['live_difficulty_id']];
	
	return $ret;
}
