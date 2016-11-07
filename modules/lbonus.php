<?php
//lbonus.php 登录奖励module
//lbonus/execute 执行登录奖励
function lbonus_execute() {
  global $uid, $mysql, $perm;
  require 'config/modules_lbonus.php';
  $ret['login_count'] = 1;
  $days = $mysql->query('
  SELECT last_login_date, lbonus_point,
  to_days(`last_login_date`) - to_days(insert_date) days_from_first_login,
  to_days(CURRENT_TIMESTAMP) - to_days(last_login_date) has_bonus,
  month(CURRENT_TIMESTAMP) - month(last_login_date) new_month
  FROM `login_bonus`,`users` WHERE login_bonus.user_id='.$uid.' AND users.user_id=login_bonus.user_id')->fetch();
  if (empty($days)) {
    $mysql->query("INSERT INTO login_bonus (user_id) VALUES($uid)");
    $days = ['days_from_first_login'=>0, 'last_login_date'=>'2014-01-01 00:00:00', 'lbonus_point'=>0, 'has_bonus'=>1, 'new_month'=>0];
  }
  $ret['days_from_first_login'] = abs((int)$days['days_from_first_login']);
  $ret['last_login_date'] = $days['last_login_date'];
  $ret['before_lbonus_point'] = (int)$days['lbonus_point'];
  $ret['after_lbonus_point'] = (int)$days['lbonus_point'];
  $ret['items'] = ['point'=>[]];
  if ($days['has_bonus']) {
    if ($days['new_month']) {
      $ret['before_lbonus_point'] = 0;
      $ret['after_lbonus_point'] = 1;
    } else {
      $ret['after_lbonus_point']++;
    }
    $bonus = $login_bonus_list[$ret['before_lbonus_point']];
    switch ($bonus[0]) {
    case 'ticket': $bonus['incentive_item_id'] = 1;$bonus['add_type'] = 1000;break;
    case 'social': $bonus['incentive_item_id'] = 2;$bonus['add_type'] = 3002;break;
    case 'coin': $bonus['incentive_item_id'] = 3;$bonus['add_type'] = 3000;break;
    case 'loveca': $bonus['incentive_item_id'] = 4;$bonus['add_type'] = 3001;break;
    case 's_ticket': $bonus['incentive_item_id'] = 5;$bonus['add_type'] = 1000;break;
    default: $bonus['incentive_item_id'] = $bonus[0];$bonus['add_type'] = 1001;break;
    }
    $is_card = ($bonus['add_type'] == 1001) ? 1 : 0;
    $bonus['amount'] = $bonus[1];
    $bonus['incentive_id'] = [];
    $ret['items']['point'][0] = $bonus;
    $mysql->exec('UPDATE login_bonus SET lbonus_point='.$ret['after_lbonus_point'].',last_login_date=CURRENT_TIMESTAMP WHERE user_id='.$uid);
    $mysql->exec("INSERT INTO incentive_list (user_id, incentive_item_id, amount, is_card, incentive_message) VALUES ($uid,{$bonus['incentive_item_id']},{$bonus['amount']}, $is_card, '".date('m')."月登録獎励：第".$ret['after_lbonus_point']."天！')");
  }
  //如果客户端大于2.0，合并lbonus/getCard和nlbonus/execute的返回值
  //if(version_compare($_SERVER['HTTP_BUNDLE_VERSION'], '2.0', '>=')) {
    $card['card_info'] = lbonus_getCard();
    $sheets = runAction('nlbonus', 'execute');
    $ret = array_merge($ret, $card, $sheets);
  //}
  $ret['bushimo_reward_info'] = [];
  return $ret;
}

//lbonus/getCard 获取登录奖励列表
function lbonus_getCard() {
  require 'config/modules_lbonus.php';
  $incentive_item_ids = ['ticket'=>1, 'coin'=>2, 'social'=>3, 'loveca'=>4, 's_ticket'=>5];
  $ret['lbonus_count'] = (int)date('t');
  $ret['start_date'] = date('Y-m').'-01 00:00:00';
  $ret['end_date'] = date('Y-m-t').' 23:59:59';
  $ret['description'] = '';
  $ret['items'] = [];
  foreach($login_bonus_list as $k => $v) {
    $item['lbonus_point'] = $k + 1;
    switch($v[0]) {
    case 'ticket': $item['incentive_item_id'] = 1;$item['add_type'] = 1000;break;
    case 'social': $item['incentive_item_id'] = 2;$item['add_type'] = 3002;break;
    case 'coin': $item['incentive_item_id'] = 3;$item['add_type'] = 3000;break;
    case 'loveca': $item['incentive_item_id'] = 4;$item['add_type'] = 3001;break;
    case 's_ticket': $item['incentive_item_id'] = 5;$item['add_type'] = 1000;break;
    default: $item['incentive_item_id'] = $item[0];$item['add_type'] = 1001;break;
    }
    $item['amount'] = $v[1];
    $ret['items'][] = $item;
  }
  return $ret;
}

?>