<?php
//login.php 登录module

function login_startUp($post) {
  $post['user_id'] = 0;
  return $post;
}

function login_startWithoutInvite() {
  return [];
}

//login/authkey 获取一个认证token
function login_authkey() {
  sscanf($_SERVER['HTTP_AUTHORIZE'], 'consumerKey=lovelive_test&timeStamp=%d&version=1.1&nonce=%d', $timestamp, $nonce);
  if($nonce != 1) {
    throw403('HTTP_AUTHORIZE_INVALID_AUTHKEY');
  }
  $ret['authorize_token'] = sha1(rand(10000000, 99999999));
  global $mysql;
  $mysql->query('insert into tmp_authorize(token) values (?)', [$ret['authorize_token']]);
  return $ret;
}

//login/login 执行登录，返回一个UID
function login_login($post) {
  global $mysql;
  $authorize = explode('&', $_SERVER['HTTP_AUTHORIZE']);
  sscanf($authorize[1], 'timeStamp=%d', $timestamp);
  sscanf($authorize[3], 'token=%s', $token);
  sscanf($authorize[4], 'nonce=%d', $nonce);
  if ($nonce != 2) {
    throw403('HTTP_AUTHORIZE_INVALID_LOGIN');
  }
  if (!$mysql->query('select * from tmp_authorize where token=?', [$token])->fetch()) {
    throw403('AUTHORIZE_TOKEN_NOT_FOUND_LOGIN');
  }
  $mysql->query('delete from tmp_authorize where token=?', [$token]);
  $login_result = login_v2($post);
  $id = $login_result[0];
  $login_result_v1 = false;
  if ($login_result[0] === false) {
    $login_result_v1 = login_v1($post);
    if ($login_result_v1[0] !== false) {
      $mysql->exec("UPDATE users SET password='{$login_result[2]}', username='{$login_result[1]}' WHERE username='{$login_result_v1[1]}'"); 
      $id = $login_result_v1[0];
    }
  }
  if($id === false) {
    //if($enable_web_reg) {
      $id = -1;
    //}else{
    //  $id = $mysql->query('select ifnull(max(user_id), 0) from users')->fetchColumn();
    //  $id++;
    //  $mysql->exec("INSERT INTO `users` (`user_id`, `username`, `password`) VALUES ($id, '$uname', '$pass')");
    //  $mysql->exec("INSERT INTO `user_info` (`user_id`, `name`, `invite_code`) VALUES($id, 'New User', '$id')");
    //  $mysql->exec("INSERT INTO `user_perm` VALUES($id, 1, 1, 1, 0, 0)");
    //}
  }
  $ret['user_id'] = $id;
  $ret['authorize_token'] = sha1(rand(10000000, 99999999));
  if ($id !== -1) {
    $mysql->exec("UPDATE users SET nonce=2, authorize_token='{$ret['authorize_token']}', elapsed_time_from_login=CURRENT_TIMESTAMP WHERE username='{$login_result[1]}'");
  } else {
    $mysql->query('delete from tmp_authorize where username=?', [$login_result[1]]);
    $mysql->query('insert into tmp_authorize (token, username, password) values (?, ?, ?)', [$ret['authorize_token'], $login_result[1], $login_result[2]]);
  }
  $ret['review_version'] = '';
  return $ret;
}

function login_v2($post) {
  global $mysql;
  $pass = hash('sha512', $post['login_passwd']);
  $uname = hash('sha512', str_replace('-', 'MakiMakiMa', $post['login_key'])).$pass;
  $pass .= hash('sha512', str_replace('-', 'NicoNicoNi', $post['login_key']));
  $uname .= $uname; //长度不够
  $pass .= $pass;
  $uname = substr($uname, hexdec(substr($post['login_key'], 0, 2)), 32);
  $pass = substr($pass, hexdec(substr($post['login_passwd'], 0, 2)), 32);
  return [$mysql->query("SELECT user_id FROM users WHERE username='$uname' and password='$pass'")->fetchColumn(), $uname, $pass];
}

function login_v1($post) {
  global $mysql;
  $uname = sha1($post['login_key']);
  $pass = sha1($post['login_passwd']);
  return [$mysql->query("SELECT user_id FROM users WHERE username='$uname' and password='$pass'")->fetchColumn(), $uname, $pass];
}

//login/unitList 返回首次登录的队伍信息
function login_unitList() {
  require 'config/reg.php';
  $i = 1;
  foreach($default_deck as $v) {
    $tmp['unit_initial_set_id'] = $i;
    $i++;
    $tmp['unit_list'] = $v;
    $tmp['center_unit_id'] = $v[4];
    $ret[] = $tmp;
  }
  return ['unit_initial_set' => $ret];
}

//login/unitSelect 选择首次登录的队伍
function login_unitSelect($post) {
  global $uid, $mysql, $max_unit_id;
  require 'config/reg.php';
  $unit = getUnitDb();
  $selected = login_unitList();
  $selected = $selected['unit_initial_set'][$post['unit_initial_set_id']-1];
  $position = 1;
  foreach($selected['unit_list'] as $v) {
    $mysql->exec("INSERT INTO `unit_list` (`user_id`, `unit_id`) VALUES ('$uid', '$v');");
    $tmp['position'] = $position;
    $tmp['unit_owning_user_id'] = (int)$mysql->lastInsertId();
    if($position == 5) {
      $center = $tmp['unit_owning_user_id'];
    }
    $unit_deck_detail[] = $tmp;
    $position++;
  }
  if($all_card_by_default) {
    $card_list = $unit->query('select unit_id from unit_m where unit_id<='.$max_unit_id)->fetchAll();
    $query = 'INSERT INTO `unit_list` (`user_id`, `unit_id`) VALUES ';
    foreach($card_list as $v) {
      $query .= '('.$uid.', '.$v[0].'), ';
    }
    $query = substr($query, 0,strlen($query)-1);
    $mysql->exec($query);
  }
  $mysql->exec("INSERT IGNORE INTO album (user_id,unit_id) SELECT DISTINCT $uid, unit_id FROM unit_list WHERE user_id = {$uid}");
  //修正特典卡的rank
  $default_rankup = $unit->query('select unit_id from unit_m where unit_m.normal_live_asset like "%rankup%"')->fetchAll(PDO::FETCH_COLUMN);
  $mysql->exec('UPDATE unit_list SET rank=2 WHERE user_id='.$uid.' AND unit_id in('.implode(', ', $default_rankup).')');
  $mysql->exec('UPDATE album SET rank_max_flag=1 WHERE user_id='.$uid.' AND unit_id in('.implode(', ', $default_rankup).')');
  $tmp2['unit_deck_detail'] = $unit_deck_detail;
  $tmp2['unit_deck_id'] = 1;
  $tmp2['main_flag'] = true;
  $tmp2['deck_name'] = '';
  $unit_deck_list[] = $tmp2;
  $json = json_encode($unit_deck_list);
  $mysql->exec("INSERT IGNORE INTO user_deck (user_id,json,center_unit) VALUES ($uid, '$json', $center)");
  //这两个初始成员一旦没有进不去
  $mysql->exec("INSERT INTO `unit_list` (`user_id`, `unit_id`) VALUES ('$uid', '9');");
  $mysql->exec("INSERT INTO `unit_list` (`user_id`, `unit_id`) VALUES ('$uid', '13');");
  
  runAction('tutorial', 'skip');
  throw403('restart client');
  return [];
}

//login/topInfo 返回首页显示的一些信息 这里面绝大多数功能LLSP都没实现所以返回定值
function login_topInfo() {
  global $uid, $mysql, $params;
  $present_count = $mysql->query('SELECT count(*) FROM incentive_list WHERE user_id='.$uid.' and opened_date=0')->fetchColumn();
  $free_gacha = $mysql->query('select to_days(CURRENT_TIMESTAMP)-to_days(last_scout_time) free_gacha, got_free_gacha_list from secretbox where user_id=?', [$uid])->fetch();
  $free_gacha = ($params['card_switch'] && ($free_gacha['free_gacha'] > 0 || $free_gacha['got_free_gacha_list'] == ''));
  return json_decode('{
            "free_gacha_flag": '.($free_gacha ? 'true' : 'false').',
            "next_free_gacha_timestamp": '.strtotime(date('Y-m-d',strtotime('+1 day'))).',
            "friend_action_cnt": 0,
            "friend_greet_cnt": 0,
            "friend_variety_cnt": 0,
            "notice_friend_datetime": "2013-04-15 11:47:00",
            "notice_mail_datetime": "2000-01-01 12:00:00",
            "present_cnt": '.$present_count.',
            "server_datetime": "'.date('Y-m-d H:i:s').'",
            "server_timestamp": '.time().'
        }');
}

//login/topInfoOnce 返回新成就数目
function login_topInfoOnce() {
  return ["new_achievement_cnt"=>0, 'unaccomplished_achievement_cnt'=>0, 'handover_expire_status'=>0,'live_daily_reward_exist'=>false];
}

?>