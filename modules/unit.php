<?php
require_once('includes/unit.php');
require_once('includes/exchange.php');
//unit/unitAll 获取卡片列表
function unit_unitAll() {
  global $uid, $mysql, $params;
  if ($params['card_switch'] == 0) {
    $ret = json_decode('[{"unit_owning_user_id":1,"unit_id":40,"rank":2,"exp":0,"love":0,"unit_skill_level":1,"favorite_flag":false,"insert_date":"2014-10-2514:29:35","max_hp":4,"max_level":60,"max_love":200,"max_rank":2,"level":1,"is_level_max":false,"is_love_max":false,"is_rank_max":true,"is_skill_level_max":false,"is_removable_skill_capacity_max":false,"unit_removable_skill_capacity": 1,"unit_skill_exp": 0, "display_rank": 1,"next_exp":14},{"unit_owning_user_id":2,"unit_id":41,"rank":2,"exp":0,"love":0,"unit_skill_level":1,"favorite_flag":false,"insert_date":"2014-10-2514:29:35","max_hp":4,"max_level":60,"max_love":200,"max_rank":2,"level":1,"is_level_max":false,"is_love_max":false,"is_rank_max":true,"is_skill_level_max":false,"is_removable_skill_capacity_max":false,"unit_removable_skill_capacity": 1,"unit_skill_exp": 0, "display_rank": 1,"next_exp":14},{"unit_owning_user_id":3,"unit_id":42,"rank":2,"exp":0,"love":0,"unit_skill_level":1,"favorite_flag":false,"insert_date":"2014-10-2514:29:35","max_hp":4,"max_level":60,"max_love":200,"max_rank":2,"level":1,"is_level_max":false,"is_love_max":false,"is_rank_max":true,"is_skill_level_max":false,"is_removable_skill_capacity_max":false,"unit_removable_skill_capacity": 1,"unit_skill_exp": 0, "display_rank": 1,"next_exp":14},{"unit_owning_user_id":4,"unit_id":43,"rank":2,"exp":0,"love":0,"unit_skill_level":1,"favorite_flag":false,"insert_date":"2014-10-2514:29:35","max_hp":4,"max_level":60,"max_love":200,"max_rank":2,"level":1,"is_level_max":false,"is_love_max":false,"is_rank_max":true,"is_skill_level_max":false,"is_removable_skill_capacity_max":false,"unit_removable_skill_capacity": 1,"unit_skill_exp": 0, "display_rank": 1,"next_exp":14},{"unit_owning_user_id":5,"unit_id":44,"rank":2,"exp":0,"love":0,"unit_skill_level":1,"favorite_flag":false,"insert_date":"2014-10-2514:29:35","max_hp":4,"max_level":60,"max_love":200,"max_rank":2,"level":1,"is_level_max":false,"is_love_max":false,"is_rank_max":true,"is_skill_level_max":false,"is_removable_skill_capacity_max":false,"unit_removable_skill_capacity": 1,"unit_skill_exp": 0, "display_rank": 1,"next_exp":14},{"unit_owning_user_id":6,"unit_id":45,"rank":2,"exp":0,"love":0,"unit_skill_level":1,"favorite_flag":false,"insert_date":"2014-10-2514:29:35","max_hp":4,"max_level":60,"max_love":200,"max_rank":2,"level":1,"is_level_max":false,"is_love_max":false,"is_rank_max":true,"is_skill_level_max":false,"is_removable_skill_capacity_max":false,"unit_removable_skill_capacity": 1,"unit_skill_exp": 0, "display_rank": 1,"next_exp":14},{"unit_owning_user_id":7,"unit_id":46,"rank":2,"exp":0,"love":0,"unit_skill_level":1,"favorite_flag":false,"insert_date":"2014-10-2514:29:35","max_hp":4,"max_level":60,"max_love":200,"max_rank":2,"level":1,"is_level_max":false,"is_love_max":false,"is_rank_max":true,"is_skill_level_max":false,"is_removable_skill_capacity_max":false,"unit_removable_skill_capacity": 1,"unit_skill_exp": 0, "display_rank": 1,"next_exp":14},{"unit_owning_user_id":8,"unit_id":47,"rank":2,"exp":0,"love":0,"unit_skill_level":1,"favorite_flag":false,"insert_date":"2014-10-2514:29:35","max_hp":4,"max_level":60,"max_love":200,"max_rank":2,"level":1,"is_level_max":false,"is_love_max":false,"is_rank_max":true,"is_skill_level_max":false,"is_removable_skill_capacity_max":false,"unit_removable_skill_capacity": 1,"unit_skill_exp": 0, "display_rank": 1,"next_exp":14},{"unit_owning_user_id":9,"unit_id":48,"rank":2,"exp":0,"love":0,"unit_skill_level":1,"favorite_flag":false,"insert_date":"2014-10-2514:29:35","max_hp":4,"max_level":60,"max_love":200,"max_rank":2,"level":1,"is_level_max":false,"is_love_max":false,"is_rank_max":true,"is_skill_level_max":false,"is_removable_skill_capacity_max":false,"unit_removable_skill_capacity": 1,"unit_skill_exp": 0, "display_rank": 1,"next_exp":14},{"unit_owning_user_id":10,"unit_id":49,"rank":1,"exp":0,"love":0,"unit_skill_level":1,"favorite_flag":false,"insert_date":"2014-10-2514:29:35","max_hp":4,"max_level":60,"max_love":200,"max_rank":2,"level":1,"is_level_max":false,"is_love_max":false,"is_rank_max":true,"is_skill_level_max":false,"is_removable_skill_capacity_max":false,"unit_removable_skill_capacity": 1,"unit_skill_exp": 0, "display_rank": 1,"next_exp":14},{"unit_owning_user_id":11,"unit_id":641,"rank":2,"exp":36800,"love":500,"unit_skill_level":1,"favorite_flag":false,"insert_date":"2014-10-2514:29:35","max_hp":4,"max_level":80,"max_love":500,"max_rank":2,"level":80,"is_level_max":true,"is_love_max":true,"is_rank_max":true,"is_skill_level_max":false,"is_removable_skill_capacity_max":false,"unit_removable_skill_capacity": 1,"unit_skill_exp": 0, "display_rank": 1,"next_exp":0},{"unit_owning_user_id":12,"unit_id":642,"rank":2,"exp":36800,"love":500,"unit_skill_level":1,"favorite_flag":false,"insert_date":"2014-10-2514:29:35","max_hp":4,"max_level":80,"max_love":500,"max_rank":2,"level":80,"is_level_max":true,"is_love_max":true,"is_rank_max":true,"is_skill_level_max":false,"is_removable_skill_capacity_max":false,"unit_removable_skill_capacity": 1,"unit_skill_exp": 0, "display_rank": 1,"next_exp":0},{"unit_owning_user_id":13,"unit_id":643,"rank":2,"exp":36800,"love":500,"unit_skill_level":1,"favorite_flag":false,"insert_date":"2014-10-2514:29:35","max_hp":4,"max_level":80,"max_love":500,"max_rank":2,"level":80,"is_level_max":true,"is_love_max":true,"is_rank_max":true,"is_skill_level_max":false,"is_removable_skill_capacity_max":false,"unit_removable_skill_capacity": 1,"unit_skill_exp": 0, "display_rank": 1,"next_exp":0},{"unit_owning_user_id":14,"unit_id":644,"rank":2,"exp":36800,"love":500,"unit_skill_level":1,"favorite_flag":false,"insert_date":"2014-10-2514:29:35","max_hp":4,"max_level":80,"max_love":500,"max_rank":2,"level":80,"is_level_max":true,"is_love_max":true,"is_rank_max":true,"is_skill_level_max":false,"is_removable_skill_capacity_max":false,"unit_removable_skill_capacity": 1,"unit_skill_exp": 0, "display_rank": 1,"next_exp":0},{"unit_owning_user_id":15,"unit_id":645,"rank":2,"exp":79700,"love":1000,"unit_skill_level":1,"favorite_flag":false,"insert_date":"2014-10-2514:29:35","max_hp":6,"max_level":100,"max_love":1000,"max_rank":2,"level":100,"is_level_max":true,"is_love_max":true,"is_rank_max":true,"is_skill_level_max":false,"is_removable_skill_capacity_max":false,"unit_removable_skill_capacity": 1,"unit_skill_exp": 0, "display_rank": 1,"next_exp":0},{"unit_owning_user_id":16,"unit_id":649,"rank":2,"exp":36800,"love":500,"unit_skill_level":1,"favorite_flag":false,"insert_date":"2014-10-2514:29:35","max_hp":4,"max_level":80,"max_love":500,"max_rank":2,"level":80,"is_level_max":true,"is_love_max":true,"is_rank_max":true,"is_skill_level_max":false,"is_removable_skill_capacity_max":false,"unit_removable_skill_capacity": 1,"unit_skill_exp": 0, "display_rank": 1,"next_exp":0},{"unit_owning_user_id":17,"unit_id":650,"rank":2,"exp":36800,"love":500,"unit_skill_level":1,"favorite_flag":false,"insert_date":"2014-10-2514:29:35","max_hp":4,"max_level":80,"max_love":500,"max_rank":2,"level":80,"is_level_max":true,"is_love_max":true,"is_rank_max":true,"is_skill_level_max":false,"is_removable_skill_capacity_max":false,"unit_removable_skill_capacity": 1,"unit_skill_exp": 0, "display_rank": 1,"next_exp":0},{"unit_owning_user_id":18,"unit_id":651,"rank":2,"exp":36800,"love":500,"unit_skill_level":1,"favorite_flag":false,"insert_date":"2014-10-2514:29:35","max_hp":4,"max_level":80,"max_love":500,"max_rank":2,"level":80,"is_level_max":true,"is_love_max":true,"is_rank_max":true,"is_skill_level_max":false,"is_removable_skill_capacity_max":false,"unit_removable_skill_capacity": 1,"unit_skill_exp": 0, "display_rank": 1,"next_exp":0},{"unit_owning_user_id":19,"unit_id":652,"rank":2,"exp":79700,"love":1000,"unit_skill_level":1,"favorite_flag":false,"insert_date":"2014-10-2514:29:35","max_hp":6,"max_level":100,"max_love":1000,"max_rank":2,"level":100,"is_level_max":true,"is_love_max":true,"is_rank_max":true,"is_skill_level_max":false,"is_removable_skill_capacity_max":false,"unit_removable_skill_capacity": 1,"unit_skill_exp": 0, "display_rank": 1,"next_exp":0},{"unit_owning_user_id":21,"unit_id":1,"rank":2,"exp":8000,"love":50,"unit_skill_level":1,"favorite_flag":false,"insert_date":"2014-10-2514:29:35","max_hp":4,"max_level":40,"max_love":50,"max_rank":2,"level":40,"is_level_max":true,"is_love_max":true,"is_rank_max":true,"is_skill_level_max":false,"is_removable_skill_capacity_max":false,"unit_removable_skill_capacity": 1,"unit_skill_exp": 0, "display_rank": 1,"next_exp":0},{"unit_owning_user_id":22,"unit_id":4,"rank":2,"exp":8000,"love":50,"unit_skill_level":1,"favorite_flag":false,"insert_date":"2014-10-2514:29:35","max_hp":4,"max_level":40,"max_love":50,"max_rank":2,"level":40,"is_level_max":true,"is_love_max":true,"is_rank_max":true,"is_skill_level_max":false,"is_removable_skill_capacity_max":false,"unit_removable_skill_capacity": 1,"unit_skill_exp": 0, "display_rank": 1,"next_exp":0},{"unit_owning_user_id":23,"unit_id":7,"rank":2,"exp":8000,"love":50,"unit_skill_level":1,"favorite_flag":false,"insert_date":"2014-10-2514:29:35","max_hp":4,"max_level":40,"max_love":50,"max_rank":2,"level":40,"is_level_max":true,"is_love_max":true,"is_rank_max":true,"is_skill_level_max":false,"is_removable_skill_capacity_max":false,"unit_removable_skill_capacity": 1,"unit_skill_exp": 0, "display_rank": 1,"next_exp":0},{"unit_owning_user_id":24,"unit_id":10,"rank":2,"exp":8000,"love":50,"unit_skill_level":1,"favorite_flag":false,"insert_date":"2014-10-2514:29:35","max_hp":4,"max_level":40,"max_love":50,"max_rank":2,"level":40,"is_level_max":true,"is_love_max":true,"is_rank_max":true,"is_skill_level_max":false,"is_removable_skill_capacity_max":false,"unit_removable_skill_capacity": 1,"unit_skill_exp": 0, "display_rank": 1,"next_exp":0},{"unit_owning_user_id":25,"unit_id":13,"rank":2,"exp":8000,"love":50,"unit_skill_level":1,"favorite_flag":false,"insert_date":"2014-10-2514:29:35","max_hp":4,"max_level":40,"max_love":50,"max_rank":2,"level":40,"is_level_max":true,"is_love_max":true,"is_rank_max":true,"is_skill_level_max":false,"is_removable_skill_capacity_max":false,"unit_removable_skill_capacity": 1,"unit_skill_exp": 0, "display_rank": 1,"next_exp":0},{"unit_owning_user_id":26,"unit_id":16,"rank":2,"exp":8000,"love":50,"unit_skill_level":1,"favorite_flag":false,"insert_date":"2014-10-2514:29:35","max_hp":4,"max_level":40,"max_love":50,"max_rank":2,"level":40,"is_level_max":true,"is_love_max":true,"is_rank_max":true,"is_skill_level_max":false,"is_removable_skill_capacity_max":false,"unit_removable_skill_capacity": 1,"unit_skill_exp": 0, "display_rank": 1,"next_exp":0},{"unit_owning_user_id":27,"unit_id":19,"rank":2,"exp":8000,"love":50,"unit_skill_level":1,"favorite_flag":false,"insert_date":"2014-10-2514:29:35","max_hp":4,"max_level":40,"max_love":50,"max_rank":2,"level":40,"is_level_max":true,"is_love_max":true,"is_rank_max":true,"is_skill_level_max":false,"is_removable_skill_capacity_max":false,"unit_removable_skill_capacity": 1,"unit_skill_exp": 0, "display_rank": 1,"next_exp":0},{"unit_owning_user_id":28,"unit_id":22,"rank":2,"exp":8000,"love":50,"unit_skill_level":1,"favorite_flag":false,"insert_date":"2014-10-2514:29:35","max_hp":4,"max_level":40,"max_love":50,"max_rank":2,"level":40,"is_level_max":true,"is_love_max":true,"is_rank_max":true,"is_skill_level_max":false,"is_removable_skill_capacity_max":false,"unit_removable_skill_capacity": 1,"unit_skill_exp": 0, "display_rank": 1,"next_exp":0},{"unit_owning_user_id":29,"unit_id":25,"rank":2,"exp":8000,"love":50,"unit_skill_level":1,"favorite_flag":false,"insert_date":"2014-10-2514:29:35","max_hp":4,"max_level":40,"max_love":50,"max_rank":2,"level":40,"is_level_max":true,"is_love_max":true,"is_rank_max":true,"is_skill_level_max":false,"is_removable_skill_capacity_max":false,"unit_removable_skill_capacity": 1,"unit_skill_exp": 0, "display_rank": 1,"next_exp":0}]', true);
    if ($params['card_switch'] == 0) {
      if (isset($params['extend_avatar'])) {
      $ret[9]['unit_id'] = $params['extend_avatar'];
      $ret[9]['rank'] = $params['extend_avatar_is_rankup'] + 1;
      $ret[9]['is_rank_max'] = (bool)$params['extend_avatar_is_rankup'];
      }
    } else {
      unset($ret[9]);
      $ret = array_merge($ret, GetUnitDetail(array($mysql->query('SELECT center_unit FROM user_deck WHERE user_id=' . $uid)->fetchColumn())));
      $ret[9]['unit_owning_user_id'] = 10;
    }
    $ret[9]['display_rank'] = $ret[9]['rank'];
    return $ret;
  }
  $res = $mysql->query("SELECT unit_owning_user_id FROM unit_list WHERE user_id={$uid}");
  while ($row = $res->fetchColumn()) {
    $ret[] = $row;
  }
  if (!isset($ret)) {
    return array();
  }
  return GetUnitDetail($ret, false, true);
}

function unit_supporterAll() {
  return ['unit_support_list' => []];
}

function unit_removableSkillInfo() {
  return ['owning_info' => [], 'equipment_info' => []];
}

//unit/deckInfo 获取队伍列表
function unit_deckInfo() {
  global $uid, $mysql, $params;
  if ($params['card_switch'] == 0) {
    return json_decode('[{"unit_owning_user_ids":[{"position":5,"unit_owning_user_id":1},{"position":1,"unit_owning_user_id":2},{"position":2,"unit_owning_user_id":3},{"position":3,"unit_owning_user_id":4},{"position":4,"unit_owning_user_id":5},{"position":6,"unit_owning_user_id":6},{"position":7,"unit_owning_user_id":7},{"position":8,"unit_owning_user_id":8},{"position":9,"unit_owning_user_id":9}],"unit_deck_id":1,"main_flag":true,"deck_name":"No card"}, {"unit_owning_user_ids":[{"position":5,"unit_owning_user_id":21},{"position":1,"unit_owning_user_id":28},{"position":2,"unit_owning_user_id":23},{"position":3,"unit_owning_user_id":22},{"position":4,"unit_owning_user_id":25},{"position":6,"unit_owning_user_id":27},{"position":7,"unit_owning_user_id":26},{"position":8,"unit_owning_user_id":29},{"position":9,"unit_owning_user_id":24}],"unit_deck_id":2,"main_flag":false,"deck_name":"No HP recovery"}, {"unit_owning_user_ids":[{"position":5,"unit_owning_user_id":11},{"position":1,"unit_owning_user_id":18},{"position":2,"unit_owning_user_id":13},{"position":3,"unit_owning_user_id":12},{"position":4,"unit_owning_user_id":15},{"position":6,"unit_owning_user_id":17},{"position":7,"unit_owning_user_id":16},{"position":8,"unit_owning_user_id":19},{"position":9,"unit_owning_user_id":14}],"unit_deck_id":3,"main_flag":false,"deck_name":"全国大会予選(Unranked)"}]');
  }
  $res = $mysql->query('SELECT json FROM user_deck WHERE user_id=' . $uid)->fetchColumn();
  $deck = json_decode($res, true);
  if (!$deck) {
    return array();
  }
  $res2 = $mysql->query('SELECT unit_owning_user_id FROM unit_list WHERE user_id=' . $uid);
  while ($t = $res2->fetchColumn()) {
    $exist_id[$t] = true;
  }
  foreach ($deck as &$v) {
    $v['unit_owning_user_ids'] = $v['unit_deck_detail'];
    unset($v['unit_deck_detail']);
    foreach ($v['unit_owning_user_ids'] as $k2 => $v2) {
      if (!isset($exist_id[$v2['unit_owning_user_id']])) {
        unset($v['unit_owning_user_ids'][$k2]);
      }
    }
    $v['unit_owning_user_ids'] = array_merge($v['unit_owning_user_ids']);
  }
  return $deck;
}
//unit/deck 编辑队伍
function unit_deck($post) {
  global $uid, $mysql, $params;
  if ($params['card_switch'] == 0) {
    return array();
  }
  foreach ($post['unit_deck_list'] as &$v3) {
    $v3['main_flag'] = (bool) $v3['main_flag'];
  }
  foreach ($post['unit_deck_list'] as $v) {
    if (!$v['main_flag']) {
      continue;
    }
    foreach ($v['unit_deck_detail'] as $v2) {
      if ($v2['position'] == 5) {
        $center_unit = $v2['unit_owning_user_id'];
        break;
      }
    }
    break;
  }
  $mysql->prepare('UPDATE user_deck set center_unit=?, json=? WHERE user_id=?')->execute(array($center_unit, json_encode($post['unit_deck_list']), $uid));
  return array();
}
//unit/merge 练习
function unit_merge($post) {
  global $params;
  if ($params['card_switch'] == 0) {
    return array();
  }
  $merge_base_id = $post['base_owning_unit_user_id'];
  $merge_id = $post['unit_owning_user_ids'];
  global $uid, $mysql;
  $unit = getUnitDb();
  $total_exp = 0;
  $total_cost = 0;
  $total_skill = 0;
  $base_unit_id = $mysql->query('SELECT unit_id FROM unit_list WHERE unit_owning_user_id=' . $merge_base_id)->fetchColumn();
  $base_unit = $unit->query('SELECT unit_level_up_pattern_id,rarity,attribute_id,default_unit_skill_id FROM unit_m WHERE unit_id=' . $base_unit_id)->fetch();
  $merge_unit_id_all = $mysql->query('SELECT exp,unit_id,rank FROM unit_list WHERE unit_owning_user_id in (' . implode(', ', $merge_id) . ')')->fetchAll();
  pl_assert(count($merge_unit_id_all) == count($merge_id), '练习错误：找不到陪练卡片。如果你是在通信错误后看到这个错误，说明练习已经成功了，否则请报告给作者。');
  $get_seal_list = [];
  foreach ($merge_unit_id_all as $merge_unit_id) {
    $merge_unit = $unit->query('SELECT unit_level_up_pattern_id,default_unit_skill_id,rarity,attribute_id,disable_rank_up,normal_live_asset FROM unit_m WHERE unit_id=' . $merge_unit_id['unit_id'])->fetch();
    $merge_exp_cost = $unit->query('SELECT merge_exp,merge_cost FROM unit_level_up_pattern_m WHERE unit_level_up_pattern_id=' . $merge_unit['unit_level_up_pattern_id'] . ' and next_exp>=' . $merge_unit_id['exp'] . ' limit 1')->fetch();
    if ($merge_unit['attribute_id'] == $base_unit['attribute_id'] || $merge_unit['attribute_id'] == 5) {
      $merge_exp_cost['merge_exp'] = $merge_exp_cost['merge_exp'] / 5 * 6;
    }
    $total_exp += $merge_exp_cost['merge_exp'];
    $total_cost += $merge_exp_cost['merge_cost'];
    if ($merge_unit['default_unit_skill_id'] >= 190 && $merge_unit['default_unit_skill_id'] <= 201) {
      //如果是升级技能卡
      if ($merge_unit['rarity'] == $base_unit['rarity'] && ($merge_unit['attribute_id'] == $base_unit['attribute_id'] || $merge_unit['attribute_id'] == 5)) {
        $total_skill++;
      }
    }
    if ($merge_unit['default_unit_skill_id'] !== null && $merge_unit['default_unit_skill_id'] == $base_unit['default_unit_skill_id']) {
      $total_skill++;
    }
    if ($merge_unit['disable_rank_up'] == 0 && strpos($merge_unit['normal_live_asset'], 'rankup') === false) {
      $get_seal_list[] = (int)$merge_unit['rarity'];
      if ($merge_unit_id['rank'] == 2) {
      $get_seal_list[] = (int)$merge_unit['rarity'];
      }
    }
  }
  $ret['use_game_coin'] = $total_cost;
  $ret['open_subscenario_id'] = null;
  $rand = mt_rand(1, 100);
  if ($rand == 50) {
    $ret['evolution_setting_id'] = 3;
    $ret['bonus_value'] = 2;
  } elseif ($rand % 10 == 0) {
    $ret['evolution_setting_id'] = 2;
    $ret['bonus_value'] = 1.5;
  } else {
    $ret['evolution_setting_id'] = 1;
    $ret['bonus_value'] = 1;
  }
  $total_exp *= $ret['bonus_value'];
  $ret['before'] = GetUnitDetail(array($merge_base_id))[0];
  $ret['before_user_info'] = runAction('user', 'userInfo')['user'];
  $new_exp = $ret['before']['exp'] + $total_exp;
  $new_skill = $ret['before']['unit_skill_level'] + $total_skill;
  if ($new_skill > 8) {
    $new_skill = 8;
  }
  $max_exp = $unit->query('SELECT next_exp FROM unit_level_up_pattern_m WHERE unit_level_up_pattern_id=' . $base_unit['unit_level_up_pattern_id'] . ' and unit_level=' . ($ret['before']['max_level'] - 1))->fetch();
  if ($new_exp > $max_exp['next_exp']) {
    $new_exp = $max_exp['next_exp'];
    if ($base_unit['rarity'] == 2) {
      $new_level_max = true;
    }
  }
  $mysql->exec('UPDATE unit_list SET exp=' . $new_exp . ', unit_skill_level=' . $new_skill . ' WHERE unit_owning_user_id=' . $merge_base_id);
  $params['coin'] -= $total_cost;
  $mysql->exec('DELETE FROM unit_list WHERE unit_owning_user_id in(' . implode(', ', $merge_id) . ')');
  if (isset($new_level_max)) {
    $mysql->exec("UPDATE album SET rank_level_max_flag=1 WHERE user_id={$uid} and unit_id={$merge_base_id}");
  }
  $ret['after'] = GetUnitDetail(array($merge_base_id))[0];
  $ret['after_user_info'] = runAction('user', 'userInfo')['user'];
  $ret['get_exchange_point_list'] = addExchangePoint($get_seal_list);
  return $ret;
}
//unit/rankUp 特别练习
function unit_rankUp($post) {
  global $params;
  if ($params['card_switch'] == 0) {
    return array();
  }
  $evolution_base_id = $post['base_owning_unit_user_id'];
  $evolution_merge_id = $post['unit_owning_user_ids'][0];
  global $uid, $mysql;
  $unit = getUnitDb();
  $base_unit_id = $mysql->query('SELECT unit_id FROM unit_list WHERE unit_owning_user_id=' . $evolution_base_id)->fetchColumn();
  $rank_up_cost = $unit->query('SELECT rank_up_cost FROM unit_m WHERE unit_id=' . $base_unit_id)->fetch();
  $ret['use_game_coin'] = (int) $rank_up_cost['rank_up_cost'];
  $ret['open_subscenario_id'] = null;
  $ret['before'] = GetUnitDetail(array($evolution_base_id))[0];
  $ret['before_user_info'] = runAction('user', 'userInfo')['user'];
  $mysql->exec('UPDATE unit_list SET rank=2 WHERE unit_owning_user_id=' . $evolution_base_id);
  $mysql->exec('DELETE FROM unit_list WHERE unit_owning_user_id=' . $evolution_merge_id);
  $params['coin'] -= $rank_up_cost['rank_up_cost'];
  $mysql->exec("UPDATE album SET rank_max_flag=1 WHERE user_id={$uid} and unit_id=" . $ret['before']['unit_id']);
  $ret['after'] = GetUnitDetail(array($evolution_base_id))[0];
  $ret['after_user_info'] = runAction('user', 'userInfo')['user'];
  $ret['get_exchange_point_list'] = array();
  return $ret;
}
//unit/sale 转部
function unit_sale($post) {
  global $params;
  if ($params['card_switch'] == 0) {
    return array();
  }
  global $uid, $mysql;
  $unit = getUnitDb();
  $total_money = 0;
  $ret['before_user_info'] = runAction('user', 'userInfo')['user'];
  $sell_card_id = $mysql->query('SELECT unit_owning_user_id, exp, unit_id, rank FROM unit_list WHERE unit_list.unit_owning_user_id in(' . implode(', ', $post['unit_owning_user_id']) . ')');
  $get_seal_list = [];
  while ($sell_card = $sell_card_id->fetch()) {
    $unit_detail = $unit->query('SELECT sale_price, rarity, disable_rank_up, normal_live_asset FROM unit_level_up_pattern_m,unit_m WHERE unit_m.unit_id=' . $sell_card['unit_id'] . ' and unit_level_up_pattern_m.unit_level_up_pattern_id=unit_m.unit_level_up_pattern_id and next_exp>=' . $sell_card['exp'] . ' limit 1')->fetch();
    $sale_price = $unit_detail['sale_price'];
    $total_money += $sale_price;
    $ret['detail'][] = array('unit_owning_user_id' => (int) $sell_card['unit_owning_user_id'], 'unit_id' => (int) $sell_card['unit_id'], 'price' => (int) $sale_price);
    if ($unit_detail['disable_rank_up'] == 0 && strpos($unit_detail['normal_live_asset'], 'rankup') === false) {
      $get_seal_list[] = (int)$unit_detail['rarity'];
      if ($sell_card['rank'] == 2) {
        $get_seal_list[] = (int)$merge_unit['rarity'];
      }
    }
  }
  $ret['total'] = $total_money;
  $ret['reward_box_flag'] = false;
  if (count($ret['detail']) == count($post['unit_owning_user_id'])) {
    $params['coin'] += $total_money;
    $mysql->exec('DELETE FROM unit_list WHERE unit_owning_user_id in(' . implode(', ', $post['unit_owning_user_id']) . ')');
    $ret['after_user_info'] = runAction('user', 'userInfo')['user'];
    $ret['get_exchange_point_list'] = addExchangePoint($get_seal_list);
    return $ret;
  } else {
    return array();
  }
}
//unit/favorite 收藏
function unit_favorite($post) {
  global $params;
  if ($params['card_switch'] == 0) {
    return array();
  }
  global $mysql;
  $mysql->exec('UPDATE unit_list set favorite_flag=' . $post['favorite_flag'] . ' WHERE unit_owning_user_id=' . $post['unit_owning_user_id']);
  return array();
}
function unit_setDisplayRank($post) {
  global $params;
  if ($params['card_switch'] == 0) {
    return array();
  }
  global $mysql;
  $mysql->exec('UPDATE unit_list set display_rank=' . $post['display_rank'] . ' WHERE unit_owning_user_id=' . $post['unit_owning_user_id']);
  return array();
}