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
        }
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

//generateRandomLive 生成新随机谱面，外部访问无法调用
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
	$decoded = mapSort($note);
	$len = count($decoded);
	$occupied = array(-1,-1,-1,-1,-1,-1,-1,-1,-1,-1);
	$lastisadouble = 0;
	$sides = [0, 1,1,1,1,3,2,2,2,2];

	$min_ival = 0.2;

	for($i=0; $i<$len; $i++) {

		$timing = $decoded[$i]["timing_sec"];

		$double = (($i+1 < $len) && (abs($decoded[$i]["timing_sec"] - $decoded[$i+1]["timing_sec"])) < 0.04);

		//error_log($i);
		//error_log(json_encode($occupied));
		if($decoded[$i]["effect"] >= 10)//跳过滑键
			continue;

		if ($double) {
			//error_log("is double");

			do {
				$pos = rand(1, 4);
			} while($timing - $occupied[$pos] < $min_ival);

			$decoded[$i]["position"] = $pos;
			$occupied[$pos] = $decoded[$i]["timing_sec"];
			if ($decoded[$i]["effect"] == 3) {
				$occupied[$pos] += $decoded[$i]["effect_value"];
			}


			do {
				$pos = rand(6, 9);
			} while($timing - $occupied[$pos] < $min_ival);

			$decoded[$i+1]["position"] = $pos;
			$occupied[$pos] = $decoded[$i+1]["timing_sec"];
			
			if ($decoded[$i+1]["effect"] == 3) {
				$occupied[$pos] += $decoded[$i+1]["effect_value"];
			}
			$lastisadouble = 1;
			$i++;

		} else {
			//error_log("is single");
			$latest = 0;
			$latestpos = 0;
			for($u=1;$u<=9;$u++) {
				if ($occupied[$u] > $latest) {
					$latest = $occupied[$u];
					$latestpos = $u;
				}
			}
			if ($latest >= $timing - 0.2) {
				do {
					if ($latestpos < 5) {
						$pos = rand(6, 9);
					} else {
						$pos = rand(1, 4);
					} 
				} while(
					($timing - $occupied[$pos] < $min_ival)
				);
			} else if ($latest < $timing - $min_ival || $lastisadouble || $latestpos == 5) { // any
				do {
					$pos = rand(1, 9);
				} while(
					($pos == 5 && $decoded[$i]["effect"] == 3)
					|| ($timing - $occupied[$pos] < $min_ival)
				);
			} else {
				do {
					if ($latestpos < 5) {
						$pos = rand(6, 9);
					} else {
						$pos = rand(1, 4);
					} 
				} while(
					($timing - $occupied[$pos] < $min_ival)
				);
			}

			$occupied[$pos] = $decoded[$i]["timing_sec"];

			if ($decoded[$i]["effect"] == 3) {
				$occupied[$pos] += $decoded[$i]["effect_value"];
			}

			$decoded[$i]["position"] = $pos;
			$lastisadouble = 0;
		}

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
		if(!$holding && $v['effect']==3) {
			//长条，什么都不做
			$holdend=$v['timing_sec']+$v['effect_value'];
			$holding=true;
		}
		elseif($holding) {
			//长按中，什么都不做
			if($v['effect']==3) {
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
            if($v['timing_sec']>$holding[$v['position']]+0.1)
                break;
        }
        if($v['effect']==3) //长条
			$holding[$v['position']]=$v['timing_sec']+$v['effect_value'];
    }
	return $note;
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
      if ($next['effect'] == 3) {
        $score *= 1.25;
      }
      return $sum + floor($score / 100);
    }, 0);
  }
  return $total;
}