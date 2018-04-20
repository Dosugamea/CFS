<?php
//lbonus.php 登录奖励module
//lbonus/execute 执行登录奖励
function lbonus_execute() {
	global $uid, $mysql, $perm;
	require '../config/modules_lbonus.php';
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
		
		$item['item'] = get_present_info($v[0]);
		$item['item']['amount'] = $v[1];
		$item['item']['brief'] = $v[0];
		
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
		
		$item['item'] = get_present_info($v[0]);
		$item['item']['amount'] = $v[1];
		
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
		$calendar_info['get_item']['reward_box_flag'] = true;
		add_present($calendar_info['get_item']['brief'], $calendar_info['get_item']['amount'], (int)date('m')."月登陆奖励：第".(int)date('d')."天！");
	}
	
	$ret['calendar_info'] = $calendar_info;
	$sheets = nlbonus_execute();
	$ret = array_merge($ret, $sheets);
	
	//通算课题
	$total_login_count = (int)$mysql->query("SELECT COUNT(*) FROM login_bonus WHERE user_id = ?", [$uid])->fetchColumn();
	$to_get_count = 0;
	$delay_flag = false;
	
	foreach($total_login_bonus_list as $k => $v){
		$current_info = $mysql->query("SELECT * FROM login_bonus_total WHERE user_id = ? AND `count` = ?", [$uid, $k])->fetch(PDO::FETCH_ASSOC);
		if(!$current_info && $total_login_count >= $k){
			add_present($v[0], $v[1], "累计登录".$k."天登录奖励！");
			$mysql->query("INSERT INTO login_bonus_total (user_id, `count`) VALUES(?,?)", [$uid, $k]);
		}
		if($total_login_count >= $k){
			$to_get_count = $k;
			$delay_flag = false;
		}elseif(!$delay_flag){
			$to_get_count = $k;
			$delay_flag = true;
		}
	}
	
	$ret['total_login_info'] = [
		"login_count" => $total_login_count,
		"remaining_count" => $to_get_count - $total_login_count,
		"reward" => [get_present_info($total_login_bonus_list[$to_get_count][0])]
	];
	$ret['total_login_info']['reward'][0]['amount'] = $total_login_bonus_list[$to_get_count][1];
	
	$ret['server_timestamp'] = time();
	
	return $ret;
}

// nlbonus/execute 执行特殊登录奖励
function nlbonus_execute () {
	global $uid, $mysql, $param;
	require '../config/modules_nlbonus.php';
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
		foreach($v['items'] as $k2 => $v2) {
			$item['seq'] = $k2+1;
			$item['amount'] = $v2[1];
			$item = array_merge($item, get_present_info($v2[0], is_int($v2[0]), isset($v2[3])?$v2[3]:false));
			$ret['items'][] = $item;
			if($days['last_seq'] == $k2) {
				$ret['stamp_num'] = $k2;
				$ret['get_item'] = add_present($v2[0], $v2[1], $v2[2],  isset($v2[3])?$v2[3]:false);
				$ret['get_item']['amount'] = $item['amount'];
				$mysql->exec('UPDATE login_bonus_n SET last_seq=last_seq+1 WHERE nlbonus_id='.$ret['nlbonus_id'].' AND user_id='.$uid);
			}
		}
		$sheets[] = $ret;
	}
	return ['sheets' => $sheets];
}
?>