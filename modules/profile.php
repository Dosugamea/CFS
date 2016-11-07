<?php
//profile.php 显示用户信息
require_once 'includes/unit.php';
//profile/liveCnt 返回用户clear各难度谱面的数目
function profile_liveCnt($post) {
  global $mysql;
  $live = getLiveDb();
  $clear_id = $mysql->query('SELECT DISTINCT notes_setting_asset FROM live_ranking WHERE user_id='.$post['user_id'])->fetchAll(PDO::FETCH_COLUMN);
  if (!$clear_id) {
    $clear_id[0] = -1;
  }
  $result = [
    ['difficulty'=>1,'clear_cnt'=>0],
    ['difficulty'=>2,'clear_cnt'=>0],
    ['difficulty'=>3,'clear_cnt'=>0],
    ['difficulty'=>4,'clear_cnt'=>0],
    ['difficulty'=>6,'clear_cnt'=>0]
  ];
  $allcount = $live->query('
    SELECT count(notes_setting_asset) as clear_cnt,difficulty FROM
    (SELECT live_setting_id FROM normal_live_m UNION SELECT live_setting_id FROM special_live_m) all_live_m
    LEFT JOIN live_setting_m ON live_setting_m.live_setting_id=all_live_m.live_setting_id
    WHERE notes_setting_asset in ("'.implode('","',$clear_id).'") GROUP BY difficulty
  ');
  while($cnt = $allcount->fetch()) {
    $result[$cnt['difficulty']-1]['clear_cnt'] = (int)$cnt['clear_cnt'];
  }
  return $result;
}

//profile/cardRanking 返回最大的绊
function profile_cardRanking($post) {
  global $mysql;
  $res = $mysql->query('
    SELECT @id:=@id+1 as rank, unit_id, sum(love) as total_love
    FROM unit_list,(SELECT @id:=0)rank WHERE user_id='.$post['user_id'].'
    GROUP BY unit_id ORDER BY total_love DESC LIMIT 0,10
  ');
  $ret = [];
  while ($t = $res->fetch(PDO::FETCH_ASSOC)) {
    foreach($t as &$v2)
      if (is_numeric($v2)) $v2 = (int)$v2;
    $ret[] = $t;
  }
  return $ret;
}

//profile/profileInfo 返回详细信息
function profile_profileInfo($post) {
  global $mysql, $params;
  $ret2 = $mysql->query('SELECT user_id,name,level,award,background,9999 as unit_max,999 as friend_max,user_id as invite_code,introduction FROM users WHERE user_id='.$post['user_id'])->fetch(PDO::FETCH_ASSOC);
  if (empty($ret2)) {
    return [];
  }
  foreach($ret2 as $k2 => &$v2) {
    if ($k2 != 'invite_code' && is_numeric($v2)) $v2 = (int)$v2;
  }
  $time = $mysql->query('SELECT elapsed_time_from_login FROM users WHERE user_id='.$post['user_id'])->fetchColumn();
  $ret['user_info'] = $ret2;
  $ret['user_info']['elapsed_time_from_login'] = (($time === false) ? 'Unknown' : $time);
  //TODO：有头像的不先读卡片信息
  $center = GetUnitDetail($mysql->query('SELECT center_unit FROM user_deck WHERE user_id='.$post['user_id'])->fetchColumn());
  $ret['center_unit_info'] = $center;
  $avatar_info = [];
  foreach ($mysql->query('SELECT param, value from user_params where user_id='.$post['user_id'].' and param in("enable_card_switch", "extend_avatar", "extend_avatar_is_rankup")') as $v) {
    $avatar_info[$v['param']] = (int)$v['value'];
  }
  if (isset($avatar_info['extend_avatar']) && (!$params['card_switch'] || ($params['card_switch'] && !$avatar_info['enable_card_switch']))) {
    $ret['center_unit_info']['unit_id'] = $avatar_info['extend_avatar'];
    $ret['center_unit_info']['is_rank_max'] = $avatar_info['extend_avatar_is_rankup'];
  }
  $ret['is_alliance'] = false;
  $ret['friend_status'] = 0;
  if (!$ret['user_info']['award']) $ret['user_info']['award'] = 1;
  if (!$ret['user_info']['background']) $ret['user_info']['background'] = 1;
  $ret['setting_award_id'] = $ret['user_info']['award'];
  $ret['setting_background_id'] = $ret['user_info']['background'];
  unset($ret['user_info']['award'], $ret['user_info']['background']);
  return $ret;
}

//profile/profileRegister 更改简介
function profile_profileRegister($post) {
  global $user;
  $user['introduction'] = $post['introduction'];
  return [];
}

?>