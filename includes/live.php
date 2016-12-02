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

function generateRandomLive($note) {
  usort($note, function ($x, $y) {
    if ($x['timing_sec'] == $y['timing_sec']) {
      $x_is_hold = $x['effect'] == 3 ? 1 : 0;
      $y_is_hold = $y['effect'] == 3 ? 1 : 0;
      return $y_is_hold - $x_is_hold;
    }
    return ceil($x['timing_sec'] - $y['timing_sec']);
  });
  detectSameTiming($note);
  $same_timing_map = [];
  $hold_list = [];
  array_reduce($note, function ($last, &$next) use (&$same_timing_map, &$hold_list) {
    $hold_list_for_modify = array_merge(array_filter($hold_list, function ($e) use ($next) {
      if ($e['timing_sec'] + $e['effect_value'] < $next['timing_sec'] - 0.01) {
        return false;
      }
      return true;
    }));
    $hold_list = array_merge(array_filter($hold_list, function ($e) use ($next) {
      if ($e['timing_sec'] + $e['effect_value'] < $next['timing_sec'] - 0.2) {
        return false;
      }
      return true;
    }));
    $lastPosition = $last['position'];
    $lastPositionModified = false;
    if (count($hold_list_for_modify) == 1) {
      $lastPosition = $hold_list_for_modify[0]['position'];
      $lastPositionModified = true;
    } else if (count($hold_list_for_modify) > 1) {
      $lastPosition = 0;
      $lastPositionModified = true;
    }
    $hold_map = [];
    foreach ($hold_list as $v) {
      $hold_map[$v['position']] = true;
    }
    do {
      if (!$lastPositionModified && $next['timing_sec'] - $last['timing_sec'] >= 0.3) {
        $lastPosition = (isset($next['is_same_timing']) || $next['effect'] == 3) ? 5 : 0;
      }
      switch ($lastPosition) {
        case 1: case 2: case 3: case 4:
        $next['position'] = mt_rand(6, 9); break;
        case 6: case 7: case 8: case 9:
        $next['position'] = mt_rand(1, 4); break;
        case 5:
        $next['position'] = mt_rand(1, 8);
        if ($next['position'] > 4) {
          ++$next['position'];
        }
        break;
        default: $next['position'] = mt_rand(1, 9);
      }
    } while (isset($same_timing_map[$next['position']]) || isset($hold_map[$next['position']]));
    if ($next['timing_sec'] != $last['timing_sec']) {
      $same_timing_map = [];
    }
    if (isset($next['is_same_timing'])) {
      $same_timing_map[$next['position']] = true;
    }
    if ($next['effect'] == 3) {
      array_push($hold_list, $next);
    }
    return $next;
  }, ['timing_sec' => 0, 'effect' => 1, 'position' => 0]);
  foreach ($note as &$v) {
    unset($v['is_same_timing']);
  }
  return $note;
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
    else $v['position']=0; //单点
    $lasttime=$v['timing_sec'];
  }
  return $note;
}
//calcScore 计算分数
function calcScore($base, $map) {
  $total = 0;
  $combo = 0;
  $rate = 1;
  if (isset($map[0]['timing_sec'])) {
    $map = [['notes_list'=>$map]];
  }
  foreach($map as $v2) {
    $total += array_reduce($v2['notes_list'], function ($sum, $next) use (&$combo, $base, &$rate) {
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