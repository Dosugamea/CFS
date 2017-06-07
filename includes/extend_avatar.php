<?php
$avatar = [];
function loadExtendAvatar($user_list) {
  global $avatar, $mysql;
  $res = $mysql->query('SELECT * from user_params where user_id in ('.implode(',',$user_list).') and param in("enable_card_switch", "extend_avatar", "extend_avatar_is_rankup")')->fetchAll();
  foreach ($res as $v) {
    if (!isset($avatar[$v['user_id']])) {
      $avatar[$v['user_id']] = [];
    }
    $avatar[$v['user_id']][$v['param']] = (int)$v['value'];
  }
}

function setExtendAvatar($uid, &$unit) {
  global $avatar, $params;
  if (!isset($avatar[$uid])) {
    return;
  }
  $avatar_info = $avatar[$uid];
  if (isset($avatar_info['extend_avatar']) && (!$params['card_switch'] || ($params['card_switch'] && !$avatar_info['enable_card_switch']))) {
    $unit['unit_id'] = $avatar_info['extend_avatar'];
	$unit['level'] = 1;
	$unit['exp'] = 0;
	$unit['unit_skill_level'] = 1;
	$unit['unit_skill_exp'] = 0;
    $unit['is_rank_max'] = (bool)$avatar_info['extend_avatar_is_rankup'];
    $unit['display_rank'] = $avatar_info['extend_avatar_is_rankup'] + 1;
  }
}

function setExtendAvatarForce($uid, &$unit) {
  global $avatar, $params;
  if (!isset($avatar[$uid])) {
    return;
  }
  $avatar_info = $avatar[$uid];
  if (isset($avatar_info['extend_avatar'])) {
    $unit['unit_id'] = $avatar_info['extend_avatar'];
	$unit['level'] = 1;
	$unit['exp'] = 0;
	$unit['unit_skill_level'] = 1;
	$unit['unit_skill_exp'] = 0;
    $unit['is_rank_max'] = (bool)$avatar_info['extend_avatar_is_rankup'];
    $unit['display_rank'] = $avatar_info['extend_avatar_is_rankup'] + 1;
  }
}