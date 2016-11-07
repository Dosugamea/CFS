<?php
//ranking.php 排名相关module
require_once('includes/live.php');
require_once('includes/unit.php');
//ranking/live 曲目排名
function ranking_live($post) {
  global $mysql, $params;
  $notes_setting = getLiveSettings($post['live_difficulty_id'], 'notes_setting_asset');
  $ret['total_cnt'] = 10;
  $ret['page'] = 0;
  $ret['rank'] = null;
  $ret['items'] = [];
  $rank=$mysql->query('
  SELECT @id:=@id+1 as rank, a.* FROM  (
    SELECT live_ranking.user_id,hi_score,name,level,center_unit,award FROM live_ranking
    LEFT JOIN users ON users.user_id=live_ranking.user_id
    LEFT JOIN user_deck ON user_deck.user_id=live_ranking.user_id
    WHERE notes_setting_asset="'.$notes_setting['notes_setting_asset'].'" AND card_switch='.$params['card_switch'].' AND random_switch='.$params['random_switch'].'
    ORDER BY hi_score DESC LIMIT 0,10
  ) a,(SELECT @id:=0) id');
  while ($item = $rank->fetch()) {
    $ret2['rank'] = (int)$item['rank'];
    $ret2['score'] = (int)$item['hi_score'];
    $ret2['user_data']['user_id'] = (int)$item['user_id'];
    $user_list[] = $item['user_id'];
    $ret2['user_data']['name'] = $item['name'];
    $ret2['user_data']['level'] = (int)$item['level'];
    $center_units[] = $item['center_unit'];
    $ret2['setting_award_id'] = $item['award'];
    $ret['items'][] = $ret2;
  }
  if (empty($ret['items'])) {
    return $ret;
  }
  $avatar = [];
  foreach ($mysql->query('SELECT * from user_params where user_id in ('.implode(',',$user_list).') and param in("enable_card_switch", "extend_avatar", "extend_avatar_is_rankup")') as $v) {
    if (!isset($avatar[$v['user_id']])) {
      $avatar[$v['user_id']] = [];
    }
    $avatar[$v['user_id']][$v['param']] = (int)$v['value'];
  }
  $unit_detail = GetUnitDetail($center_units);
  foreach($ret['items'] as $k => &$v) {
    $v['center_unit_info'] = $unit_detail[$k];
    $avatar_info = $avatar[$v['user_data']['user_id']];
    if (isset($avatar_info['extend_avatar']) && (!$params['card_switch'] || ($params['card_switch'] && !$avatar_info['enable_card_switch']))) {
      $v['center_unit_info']['unit_id'] = $avatar_info['extend_avatar'];
      $v['center_unit_info']['is_rank_max'] = $avatar_info['extend_avatar_is_rankup'];
    }
  }
  return $ret;
}

//ranking/player 玩家排名
function ranking_player($post) {
  global $mysql, $params;
  $ret['rank'] = null;
  $ret['items'] = [];
  $count = $post['limit'];
  $ret['total_cnt'] = $mysql->query('SELECT count(DISTINCT user_id) FROM live_ranking WHERE card_switch=0')->fetchColumn();
  if (isset($post['id'])) {
    $begin = $mysql->query('
      SELECT rank-1 FROM (
        SELECT @id:=@id+1 as rank,a.* from (
          SELECT sum(hi_score) as score, user_id FROM live_ranking
          WHERE card_switch='.$params['card_switch'].'
          GROUP BY user_id ORDER BY score DESC
        )a, (SELECT @id:=0)rank
      ) b WHERE user_id='.$post['id']
    )->fetchColumn();
    $ret['page'] = 0;
    if ($begin === false) {
      return $ret;
    }
  } else {
    $begin = $post['page']*$count;
    $ret['page'] = $post['page'];
  }
  $rank = $mysql->query('
    SELECT @id:=@id+1 as rank,a.* from (
      SELECT b.*,name,level,center_unit,award FROM (
        SELECT sum(hi_score) as score, user_id FROM live_ranking
        WHERE card_switch='.$params['card_switch'].'
        GROUP BY user_id ORDER BY score DESC
      ) b
      LEFT JOIN users ON users.user_id=b.user_id
      LEFT JOIN user_deck ON user_deck.user_id=b.user_id
    )a, (SELECT @id:='.($ret['page']*$count).')rank LIMIT '.($ret['page']*$count).','.$count);
  while($item = $rank->fetch()) {
    $ret2['rank'] = (int)$item['rank'];
    $ret2['score'] = (int)$item['score'];
    $ret2['user_data']['user_id'] = (int)$item['user_id'];
    $ret2['user_data']['name'] = $item['name'];
    $ret2['user_data']['level'] = (int)$item['level'];
    $user_list[] = $item['user_id'];
    $center_units[] = $item['center_unit'];
    $ret2['setting_award_id'] = (($item['award'] == 0) ? 1 : $item['award']);
    $ret['items'][] = $ret2;
  }
  if (empty($ret['items'])) {
    return $ret;
  }
  $avatar = [];
  foreach ($mysql->query('SELECT * from user_params where user_id in ('.implode(',',$user_list).') and param in("enable_card_switch", "extend_avatar", "extend_avatar_is_rankup")') as $v) {
    if (!isset($avatar[$v['user_id']])) {
      $avatar[$v['user_id']] = [];
    }
    $avatar[$v['user_id']][$v['param']] = (int)$v['value'];
  }
  $unit_detail = GetUnitDetail($center_units);
  foreach($ret['items'] as $k => &$v) {
    $v['center_unit_info'] = $unit_detail[$k];
    $avatar_info = $avatar[$v['user_data']['user_id']];
    if (isset($avatar_info['extend_avatar']) && (!$params['card_switch'] || ($params['card_switch'] && !$avatar_info['enable_card_switch']))) {
      $v['center_unit_info']['unit_id'] = $avatar_info['extend_avatar'];
      $v['center_unit_info']['is_rank_max'] = $avatar_info['extend_avatar_is_rankup'];
    }
  }
  return $ret;
}
?>