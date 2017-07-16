<?php 
function getLiveSettingsFromCustomLive($id, $rows) {
	global $mysql;
	$result = $mysql->query('select * from programmed_live where ID=?', [$id])->fetch();
	if ($result) {
		$settings = json_decode($result['live_json'], true);
		if (isset($settings['pl_auto_calculate_combo_rank'])) {
			$map = $mysql->query('SELECT notes_list FROM notes_setting WHERE notes_setting_asset=?', [$result['notes_setting_asset']])->fetchColumn();
			if ($map) {
				$map = json_decode($map, true);
				if (is_array($map)) {
					$combo = count($map);
					$settings['c_rank_combo'] = ceil($combo * 0.3);
					$settings['b_rank_combo'] = ceil($combo * 0.5);
					$settings['a_rank_combo'] = ceil($combo * 0.7);
					$settings['s_rank_combo'] = $combo;
					unset($settings['pl_auto_calculate_combo_rank']);
					$mysql->query('update programmed_live set live_json=? where ID=?', [json_encode($settings), $id]);
				}else{
					trigger_error('getLiveSettingsFromCustomLive: 找不到Live谱面'.$id);
				}
			}else{
				trigger_error('getLiveSettingsFromCustomLive: 找不到Live谱面'.$id);
			}
		}
		
		$settings['notes_setting_asset'] = $result['notes_setting_asset'];
		$settings['capital_type'] = 1;
		if (!isset($settings['difficulty'])) {
			if (strtolower(substr($settings['name'], 0, 6)) == '[easy]' || $settings['stage_level'] <= 4) {
				$settings['difficulty'] = 1;
				$settings['capital_value'] = 5;
			} else if (strtolower(substr($settings['name'], 0, 8)) == '[normal]' || $settings['stage_level'] <= 6) {
				$settings['difficulty'] = 2;
				$settings['capital_value'] = 10;
			} else if (strtolower(substr($settings['name'], 0, 6)) == '[hard]' || $settings['stage_level'] <= 8) {
				$settings['difficulty'] = 3;
				$settings['capital_value'] = 15;
			} else {
				$settings['difficulty'] = 4;
				$settings['capital_value'] = 25;
			}
		}
		$ret = [];
		$cnt = 0;
		$settings['member_category'] = 2;
		foreach (explode(',', $rows) as $v) {
			$v = trim($v);
			$ret[$v] = $settings[$v];
			$ret[$cnt++] = $settings[$v];
		}
		return $ret;
	}
	trigger_error('getLiveSettingsFromCustomLive: 找不到live'.$id);
}

//getLiveSettings 根据live_difficulty_id获取live_settings_m，外部访问无法调用
function getLiveSettings($id, $rows) {
	if ($id < 0) {
		return getLiveSettingsFromCustomLive(0 - $id, $rows);
	}
	global $mysql;
	$live = getLiveDb();
	foreach (['normal_live_m', 'special_live_m', 'marathon.event_marathon_live_m', 'battle.event_battle_live_m', 'festival.event_festival_live_m'] as $v) {
		$ret = $live->query ('
			SELECT '.$rows.' FROM live_setting_m
			LEFT JOIN '.$v.' ON '.$v.'.live_setting_id = live_setting_m.live_setting_id
			LEFT JOIN live_track_m ON live_track_m.live_track_id = live_setting_m.live_track_id
			WHERE live_difficulty_id = '.$id
		)->fetch();
		if($ret) {
			return $ret;
		}
	}
	trigger_error('getLiveSettings: 找不到live'.$id);
}

//getRankInfo 获取rank_info，外部访问无法调用
function getRankInfo($id) {
	if ($id < 0) {
		$rank = getLiveSettingsFromCustomLive(0 - $id, 'c_rank_score,b_rank_score,a_rank_score,s_rank_score');
		array_unshift($rank, 0);
		array_push($rank, 1);
	} else {
		$rank = getLiveSettings($id, '0 as a, live_setting_m.c_rank_score, live_setting_m.b_rank_score, live_setting_m.a_rank_score, live_setting_m.s_rank_score');
	}
	$ret = [];
	for ($i = 5; $i > 0; $i--) {
		$r['rank'] = $i;
		$r['rank_min'] = (int)$rank[5 - $i];
		$ret[] = $r;
	}
	return $ret;
}

function detectSameTiming(&$notes) {
	$sameTimingMap = [];
	foreach($notes as $e) {
		if (isset($sameTimingMap[$e['timing_sec']])) {
			++$sameTimingMap[$e['timing_sec']];
		} else {
			$sameTimingMap[$e['timing_sec']] = 1;
		}
	}
	foreach($notes as &$v) {
		if ($sameTimingMap[$v['timing_sec']] > 1) {
			$v['is_same_timing'] = true;
		}
	}
}

//对谱面按时间排序以免随机谱爆炸
function mapSort($arr)
{	
	$len=count($arr);
	//该层循环控制 需要冒泡的轮数
	for($i=1;$i<$len;$i++)
	{ //该层循环用来控制每轮 冒出一个数 需要比较的次数
		for($k=0;$k<$len-$i;$k++)
		{
			 if($arr[$k]['timing_sec']>$arr[$k+1]['timing_sec'])
			{
				$tmp=$arr[$k+1];
				$arr[$k+1]=$arr[$k];
				$arr[$k]=$tmp;
			}
		}
	}
	return $arr;
}

function generateRandomLive($note) {
	$decoded=mapSort($note);
	$max_combo=count($decoded);
	for($i=1;$i<=9;$i++){
		$latest[$i]=-0.2;
		$toput[$i]=0;
	}
	$change=0;
	//latest用来存储每个键位最后一个note的结束时间
	//toput用来存储这个note是否可以放在这个位置上
	//change用来交换两个note（需要交换的情况在下面会看到）
	for($note=0;$note<$max_combo;$note++){
		$start[$note]=$decoded[$note]["timing_sec"];
		$slide_group[$decoded[$note]["notes_level"]][0]=0;
	}
	for($note=0;$note<$max_combo;$note++){
		if($note<$max_combo-1&&$start[$note+1]==$start[$note]&&($decoded[$note]["effect"]<10||$slide_group[$decoded[$note]["notes_level"]][0]==0)&&$decoded[$note+1]["effect"]>10||$change==1){
			$change++;
			if($change==1)$note++;
		}
		//滑点双押优先处理滑键
		$end[$note]=$decoded[$note]["timing_sec"];
		if($decoded[$note]["effect"]%10==3){
			$end[$note]+=$decoded[$note]["effect_value"];
			$longnote=1;
		}
		else $longnote=0;
		$last=0;
		$singlelast=0;
		if($note<$max_combo-1&&$start[$note+1]==$start[$note])$equalnext=1;
		else $equalnext=0;
		//判断是否为长条，双键的前半
		for($i=1;$i<=9;$i++){
			if($latest[$i]<$start[$note]-0.2)$toput[$i]=1;//当且仅当这个这个键位前面0.2s内没有note存在时这个键位可以放这个note
			else if($latest[$i]>=$start[$note]){
				$singlelast=10;
				$last=$i;
			}
			//判断是否另一手有长条或为双键后一半，锁定singlelast（唯一最后一键）为10
			else if($singlelast<10){
				$singlelast++;
				if($singlelast==1)$last=$i;
				else{
					if($latest[$i]>$latest[$last])$last=$i;//如果没有检测到它是长条或双键后一半，找到前面离它最近的note，并令singlelast不为0
					else if($latest[$i]==$latest[$last])$singlelast=0;//但是如果这样的note至少有两个，那么令singlelast为0
				}
			}
		}
		//此时有：一个note为双键或长条或另一手为长条等价于singlelast=10或longnote=1或equalnext=1
		//一个note不能放5等价于singlelast>=1或longnote=1或equalnext=1
		if($change==1)$equalnext=1;//处理滑单交换的特殊情形
		if($decoded[$note]["effect"]>10){
			$group=$decoded[$note]["notes_level"];
			$slide_group[$group][0]++;
			$num=$slide_group[$group][0];
			$slide_group[$group][$num]=$note;//滑键的分组
			$last1=$slide_group[$group][$num-1];
			if($num>1){
				if($decoded[$last1]["position"]==1)$decoded[$note]["position"]=2;
				else if($decoded[$last1]["position"]==9)$decoded[$note]["position"]=8;
				else if($decoded[$last1]["position"]==4&&($singlelast==10||$equalnext==1||$longnote==1||$num==2))$decoded[$note]["position"]=3;//干掉_45 _65
				else if($decoded[$last1]["position"]==6&&($singlelast==10||$equalnext==1||$longnote==1||$num==2))$decoded[$note]["position"]=7;//双手原则优先
				else if($num==2)$decoded[$note]["position"]=2*(rand(0,1))-1+$decoded[$last1]["position"];//第二个note随机取滑向
				else{
					$last2=$slide_group[$group][$num-2];
					if($num==3)$decoded[$note]["position"]=2*$decoded[$last1]["position"]-$decoded[$last2]["position"];//第三个note尽可能保滑向
					else if($decoded[$last1]["position"]==4&&$decoded[$last2]["position"]==5)$decoded[$note]["position"]=3;
					else if($decoded[$last1]["position"]==6&&$decoded[$last2]["position"]==5)$decoded[$note]["position"]=7;//干掉565 545
					else{
						$last3=$slide_group[$group][$num-3];
						if($decoded[$last1]["position"]==$decoded[$last3]["position"])$decoded[$note]["position"]=2*$decoded[$last1]["position"]-$decoded[$last2]["position"];//连续两个note不同时转滑向
						else{
							$i=rand(0,99);
							if($i<21)$decoded[$note]["position"]=$decoded[$last2]["position"];
							else $decoded[$note]["position"]=2*$decoded[$last1]["position"]-$decoded[$last2]["position"];//以上情况都不满足的话，79%概率保划向
						}
					}
				}
			}
			else{
				if($equalnext==1||$longnote==1)$toput[5]=0;
				if($singlelast>=1){
					$toput[5]=0;
					if($last<=4)$toput[1]=$toput[2]=$toput[3]=$toput[4]=0;
					if($last>=6)$toput[6]=$toput[7]=$toput[8]=$toput[9]=0;
				}
				for($j=0;$j==0;){
					$i=rand(1,9);
					if($toput[$i]==1){
						$j++;
						$decoded[$note]["position"]=$i;
					}
				}
			}
		}
		else{
			if($equalnext==1||$longnote==1)$toput[5]=0;
			for($i=1;$i<=9;$i++){
				$toput1[$i]=$toput[$i];
			}
			if($singlelast>=1){
				$toput[5]=0;
				if($last<=4)$toput[1]=$toput[2]=$toput[3]=$toput[4]=0;
				if($last>=6)$toput[6]=$toput[7]=$toput[8]=$toput[9]=0;
			}
			if($toput[1]+$toput[2]+$toput[3]+$toput[4]+$toput[6]+$toput[7]+$toput[8]+$toput[9]+$toput[5]==0){
				for($i=1;$i<=9;$i++){
					$toput[$i]=$toput1[$i];
				}
			}
			for($j=0;$j==0;){
				$i=rand(1,9);
				if($toput[$i]==1){
					$j++;
					$decoded[$note]["position"]=$i;
				}
			}
		}
		for($i=1;$i<=9;$i++){
			$toput[$i]=0;
		}
		$latest[$decoded[$note]["position"]]=$end[$note];
		if($change==1)$note-=2;
		if($change==2){
			$change=0;
			$note++;
		}//滑单交换
	}
	$live_notes = $decoded;
	return $decoded;
}

//generateRandomLiveOld 生成旧随机谱面，外部访问无法调用
function generateRandomLiveOld($note) {
	$timing=[];
	foreach($note as $v)
		$timing[]=$v['timing_sec'];
	array_multisort($timing,SORT_ASC,$note);
	$holding=false;
	$holdend=0;
	$lasttime=0;
	foreach($note as $k=>&$v) {
		if($v['timing_sec']>$holdend+0.1)
			$holding=false;
		if(!$holding && $v['effect']%10==3) {
			//长条，什么都不做
			$holdend=$v['timing_sec']+$v['effect_value'];
			$holding=true;
		}
		elseif($holding) {
			//长按中，什么都不做
			if($v['effect']%10==3) {
				$holdend=max($holdend,$v['timing_sec']+$v['effect_value']);
			}
		}
		elseif($v['timing_sec']==$lasttime || (isset($note[$k+1]['timing_sec']) && $v['timing_sec']==$note[$k+1]['timing_sec'])) {
			//双押，什么都不做
		}
		elseif($v['effect'] >= 10) {
			//滑键，什么都不做
		}
		else $v['position']=rand(1,9); //单点
		$lasttime=$v['timing_sec'];
	}
	return $note;
}

function generateRandomLiveLimitless($note) {
	$timing=[];
	foreach($note as $v)
		$timing[]=$v['timing_sec'];
	array_multisort($timing,SORT_ASC,$note);
		
	$holding=[0,-0.1,-0.1,-0.1,-0.1,-0.1,-0.1,-0.1,-0.1,-0.1];
		
	foreach($note as $k=>&$v){
		while(true){
			$v['position']=rand(1,9);
			if($v['timing_sec']>$holding[$v['position']]+0.05)
				break;
		}
		if($v['effect']%10==3) //长条
			$holding[$v['position']]=$v['timing_sec']+$v['effect_value'];
		else
			$holding[$v['position']]=$v['timing_sec'];
	}
	return $note;
}


function beatmap_timing_cmp($u, $v) {
	return $u['timing_sec'] - $v['timing_sec'];
}

//calcScore 计算分数
function calcScore($base, $map) {
	$total = 0;
	$combo = 0;
	$rate = 1;


	if (isset($map[0]['timing_sec'])) {
		$map_ = [['live_info'=>Null]];
	$map_[0]['live_info']['notes_list'] = $map;
	$map = $map_;
	}
	foreach($map as $v2) {

		foreach($v2['live_info']['notes_list'] as $k => &$p) $p['timing_sec'] += ($p['effect'] % 10 == 3) * ($p['effect_value']);
		usort($v2['live_info']['notes_list'], 'beatmap_timing_cmp');


		$total += array_reduce($v2['live_info']['notes_list'], function ($sum, $next) use (&$combo, $base, &$rate) {
			$combo++;
			switch($combo) {
			case 51:$rate = 1.1;break;
			case 101:$rate = 1.15;break;
			case 201:$rate = 1.2;break;
			case 401:$rate = 1.25;break;
			case 601:$rate = 1.3;break;
			case 801:$rate = 1.35;break;
			}
			$score = $base * 1.25 * $rate;
			if ($next['effect'] > 10) $score *= 0.5;
			if ($next['effect'] % 10 == 3) $score *= 1.25;
			return $sum + floor($score / 100);
		}, 0);
	}
	return $total;
}