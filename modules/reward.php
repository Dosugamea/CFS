<?php
//reward.php 礼物
require_once 'includes/unit.php';
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
  $res = $mysql->query('SELECT * FROM incentive_list WHERE user_id='.$uid.' AND opened_date'.($history?'!=':'=').'0'.$filter.' ORDER BY incentive_id DESC')->fetchAll();
  $ret['item_count'] = count($res);
  $ret[$array_name] = [];
  $correct_add_type = [1000, 3002, 3000, 3001, 1000];
  foreach ($res as $r) {
    foreach ($r as &$v) {
      if (is_numeric($v)) $v = (int)$v;
    }
    if ($r['is_card']) {
      $r['add_type'] = 1001;
    } else {
      $r['add_type'] = $correct_add_type[$r['incentive_item_id'] - 1];
    }
    $r['item_category_id'] = $r['incentive_item_id'];
    $r['remaining_time'] = '无限期';
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
  global $params, $mysql;
  $ret['opened_num'] = 1;
  $ret['success'] = [];
  $ret['fail'] = [];
  $ret['bushimo_reward_info'] = [];
  $r = $mysql->query('SELECT incentive_id,incentive_item_id,is_card,amount FROM incentive_list WHERE incentive_id='.$post['incentive_id'].' and opened_date=0')->fetch(PDO::FETCH_ASSOC);
  if (empty($r)) {
    return [];
  }
  foreach($r as &$v) if (is_numeric($v)) $v = (int)$v;
  $r['item_id'] = $r['incentive_item_id'];
  $r['item_category_id'] = $r['incentive_item_id'];
  if(!$r['is_card']) {
    unset($r['is_card']);
    switch($r['incentive_item_id']) {
    case 1:$params['item1'] += $r['amount'];$r['add_type']=1000;break;
    case 2:$params['item2'] += $r['amount'];$r['add_type']=3002;break;
    case 3:$params['item3'] += $r['amount'];$r['add_type']=3000;break;
    case 4:$params['item4'] += $r['amount'];$r['add_type']=3001;break;
    case 5:$params['item5'] += $r['amount'];$r['add_type']=1000;break;
    }
    $ret['success'][] = $r;
  } else {
    $ret['opened_num'] = 0;
    $ret['fail'][] = $r;
    /*
    $result = addUnit($r['item_id'], $r['amount']);
    $r['add_type'] = 1001;
    foreach ($result as $v2) {
      $v2 = array_merge($v2, $r);
      $v2['new_unit_flag'] = true;
      $v2['skill_level'] = 1;
      unset($v2['is_card'], $v2['incentive_item_id'], $v2['item_id'], $v2['amount']);
      $ret['success'][] = $v2;
    }*/
  }
  $mysql->exec('UPDATE incentive_list set opened_date=CURRENT_TIMESTAMP WHERE incentive_id='.$r['incentive_id']);
  return $ret;
}

//reward/openAll //开所有礼物
function reward_openAll($post) {
  global $uid, $mysql, $params;
  $filter = getfilter($post['category'], $post['filter']);
  $res = $mysql->query('SELECT incentive_id,incentive_item_id,is_card,amount FROM incentive_list WHERE user_id='.$uid.' AND opened_date=0'.$filter)->fetchAll(PDO::FETCH_ASSOC);
  $ret['reward_num'] = count($res);
  $ret['opened_num'] = 0;
  $ret['total_num'] = $ret['reward_num'];
  $ret['reward_item_list'] = [];
  $ret['bushimo_reward_info'] = [];
  $correct_add_type = [1000, 3002, 3000, 3001, 1000];
  foreach($res as $r) {
    foreach($r as &$v) if (is_numeric($v)) $v = (int)$v;
    if(!$r['is_card']) {
      $r['item_id'] = $r['incentive_item_id'];
      $params['item' . $r['item_id']] += $r['amount'];
      $r['add_type'] = $correct_add_type[$r['item_id'] - 1];
      $r['item_category_id'] = $r['incentive_item_id'];
      unset($r['incentive_item_id'], $r['is_card']);
      $ret['reward_item_list'][] = $r;
    } else {
      continue; //现在不能领
      $result = addUnit($r['incentive_item_id'], $r['amount']);
      $r['add_type'] = 1001;
      foreach ($result as $v2) {
        $v2 = array_merge($v2, $r);
        $v2['new_unit_flag'] = true;
        $v2['skill_level'] = 1;
        unset($v2['is_card'], $v2['incentive_item_id'], $v2['item_id'], $v2['amount']);
        $ret['reward_item_list'][] = $v2;
      }
    }
    $ret['opened_num']++;
    $mysql->exec('UPDATE incentive_list SET opened_date=CURRENT_TIMESTAMP WHERE incentive_id='.$r['incentive_id']);
  }
  return $ret;
}

?>
