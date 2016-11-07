<?php

// nlbonus/execute 执行特殊登录奖励
function nlbonus_execute () {
  global $uid, $mysql, $param;
  require 'config/modules_nlbonus.php';
  $sheets = [];
  foreach($nlbonus as $v) {
    if (strtotime(date("Y-m-d H:i:s")) > strtotime($v['end']) || strtotime(date("Y-m-d H:i:s")) < strtotime($v['start'])) {
      continue;
    }
    $days = $mysql->query('
      SELECT last_seq, to_days(CURRENT_TIMESTAMP)-to_days(last_login_date) has_bonus
      FROM `login_bonus_n` LEFT JOIN `users` ON login_bonus_n.user_id=users.user_id
      WHERE login_bonus_n.nlbonus_id='.$v['nlbonus_id'].' AND users.user_id='.$uid
    )->fetch();
    if (empty($days)) {
      $mysql->exec("INSERT INTO login_bonus_n (nlbonus_id,user_id) VALUES({$v['nlbonus_id']}, $uid)");
      $days = ['last_seq'=>0, 'has_bonus'=>1];
    }
    if (!$days['has_bonus'] || $days['last_seq']>=count($v['items'])) {
      continue;
    }
    $ret['nlbonus_id'] = $v['nlbonus_id'];
    $ret['nlbonus_item_num'] = count($v['items']);
    $ret['detail_text'] = $v['detail_text'];
    $ret['bg_asset'] = $v['bg_asset'];
    $ret['items'] = [];
    foreach($v['items'] as $k2=>$v2) {
      $item['nlbonus_item_id'] = $k2+1;
      $item['seq'] = $k2+1;
      $item['amount'] = $v2[1];
      switch($v2[0]) {
      case 'ticket': $item['incentive_item_id'] = 1;$item['add_type'] = 1000;break;
      case 'social': $item['incentive_item_id'] = 2;$item['add_type'] = 3002;break;
      case 'coin': $item['incentive_item_id'] = 3;$item['add_type'] = 3000;break;
      case 'loveca': $item['incentive_item_id'] = 4;$item['add_type'] = 3001;break;
      case 's_ticket': $item['incentive_item_id'] = 5;$item['add_type'] = 1000;break;
      default: $item['incentive_item_id'] = $v2[0];$item['add_type'] = 1001;break;
      }
      $ret['items'][] = $item;
      if($days['last_seq'] == $k2) {
        $ret['stamp_num'] = $k2;
        $ret['get_item'] = ['amount' => $item['amount'], 'add_type' => $item['add_type'], 'incentive_item_id' => $item['incentive_item_id']];
        $mysql->exec('UPDATE login_bonus_n SET last_seq=last_seq+1 WHERE nlbonus_id='.$ret['nlbonus_id'].' AND user_id='.$uid);
        $mysql->exec("INSERT INTO incentive_list (user_id,incentive_item_id,amount,is_card,incentive_message) VALUES($uid,{$item['incentive_item_id']},{$item['amount']},".($item['add_type'] == 1001 ? 1 : 0).", '{$v2[2]}')");
      }
    }
    $sheets[] = $ret;
  }
  return ['sheets' => $sheets];
}