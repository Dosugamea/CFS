<?php
//lbonus.php 登录奖励module
//lbonus/execute 执行登录奖励
function lbonus_execute() {
	global $uid, $mysql, $perm;
	require 'config/modules_lbonus.php';
	$data = $mysql->query("SELECT day FROM login_bonus WHERE user_id = ".$uid." AND year = ".(int)date('Y')." AND month = ".(int)date('m'))->fetchAll(PDO::FETCH_NUM);
	$login_query = [];
	if(!empty($data)){
		foreach($data as $i){
			$login_query[] = $i[0];
		}
	}
	//本月
	$calendar_info['current_date'] = date('Y-m-d');
	$calendar_info['current_month']['year'] = (int)date('Y');
	$calendar_info['current_month']['month'] = (int)date('m');
	$calendar_info['current_month']['days'] = [];
	foreach($login_bonus_list as $k => $v) {
		$item['day'] = $k + 1;
		$item['day_of_the_week'] = (int)date('w', strtotime(date('Y-m-').(string)$item['day']));
		if ($item['day'] ==1 || $v[0] == 'loveca'){
			$item['special_day'] = true;
			$item['special_image_asset'] = "assets/image/ui/login_bonus/loge_icon_01.png";
		}else{
			$item['special_day'] = false;
			$item['special_image_asset'] = "";
		}
		$item['received'] = in_array($item['day'], $login_query);
		switch($v[0]) {
			case 'ticket': $item['item']['item_id'] = 1;$item['item']['add_type'] = 1000;break;
			case 'social': $item['item']['item_id'] = 2;$item['item']['add_type'] = 3002;break;
			case 'coin': $item['item']['item_id'] = 3;$item['item']['add_type'] = 3000;break;
			case 'loveca': $item['item']['item_id'] = 4;$item['item']['add_type'] = 3001;break;
			case 's_ticket': $item['item']['item_id'] = 5;$item['item']['add_type'] = 1000;break;
			case 'r_sticker': $item['item']['item_id'] = 2;$item['item']['add_type'] = 3006;break;
			default: $item['item']['unit_id'] = $v[0];$item['item']['add_type'] = 1001;$item['item']['is_rank_max'] = false;break;
		}
		$item['item']['amount'] = $v[1];
		$calendar_info['current_month']['days'][] = $item;
		unset($item);
		if($k == (int)date('t',strtotime(date('Y-m-d')))-1){
			break;
		}
	}
	//下个月的
	$calendar_info['next_month']['year'] = (int)date('Y');
	$calendar_info['next_month']['month'] = (int)date('m') + 1;
	if($calendar_info['next_month']['month'] > 12){
		$calendar_info['next_month']['year'] += 1;
		$calendar_info['next_month']['month'] = 1;
	}
	$calendar_info['next_month']['days'] = [];
	foreach($login_bonus_list as $k => $v) {
		$item['day'] = $k + 1;
		$item['day_of_the_week'] = (int)date('w', strtotime($calendar_info['next_month']['year']."-".$calendar_info['next_month']['month']."-".(string)$item['day']));
		if ($item['day'] ==1 || $v[0] == 'loveca'){
			$item['special_day'] = true;
			$item['special_image_asset'] = "assets/image/ui/login_bonus/loge_icon_01.png";
		}else{
			$item['special_day'] = false;
			$item['special_image_asset'] = "";
		}
		$item['received'] = false;
		switch($v[0]) {
			case 'ticket': $item['item']['item_id'] = 1;$item['item']['add_type'] = 1000;break;
			case 'social': $item['item']['item_id'] = 2;$item['item']['add_type'] = 3002;break;
			case 'coin': $item['item']['item_id'] = 3;$item['item']['add_type'] = 3000;break;
			case 'loveca': $item['item']['item_id'] = 4;$item['item']['add_type'] = 3001;break;
			case 's_ticket': $item['item']['item_id'] = 5;$item['item']['add_type'] = 1000;break;
			case 'r_sticker': $item['item']['item_id'] = 2;$item['item']['add_type'] = 3006;break;
			default: $item['item']['unit_id'] = $v[0];$item['item']['add_type'] = 1001;$item['item']['is_rank_max'] = false;break;
		}
		$item['item']['amount'] = $v[1];
		$calendar_info['next_month']['days'][] = $item;
		unset($item);
		if($k == 13){
			break;
		}
	}
	//获取是否领取了当天的登录奖励
	if(!in_array((int)date('d'), $login_query)){
		$mysql->query("INSERT INTO login_bonus (user_id, year, month, day) VALUES(".$uid.", ".(int)date('Y').", ".(int)date('m').", ".(int)date('d').")");
		$calendar_info['get_item'] = $calendar_info['current_month']['days'][(int)date('d')-1]['item'];
		switch($calendar_info['get_item']['add_type']){
			case 1000: $calendar_info['get_item']['item_category_id'] = 1;break;
			case 3002: $calendar_info['get_item']['item_category_id'] = 2;break;
			case 3000: $calendar_info['get_item']['item_category_id'] = 3;break;
			case 3001: $calendar_info['get_item']['item_category_id'] = 4;break;
			case 1000: $calendar_info['get_item']['item_category_id'] = 5;break;
			default: $calendar_info['get_item']['item_category_id'] = 0;break;
		}
		$calendar_info['get_item']['reward_box_flag'] = true;
		$is_card = isset($calendar_info['get_item']['unit_id']);
		$mysql->exec("INSERT INTO incentive_list (user_id, incentive_item_id, amount, is_card, incentive_message) VALUES (".$uid.",".$calendar_info['get_item']['item_category_id'].",".$calendar_info['get_item']['amount'].", ".(int)$is_card.", \"".(int)date('m')."月登録獎励：第".(int)date('d')."天！\")");
	}
	
	$ret['calendar_info'] = $calendar_info;
	$sheets = runAction('nlbonus', 'execute');
	$ret = array_merge($ret, $sheets);
	
	return $ret;
}
?>