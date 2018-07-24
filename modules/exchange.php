<?php
function exchange_owningPoint() {
	$ret['exchange_point_list'] = addExchangePoint(false);
	return $ret;
}

function exchange_itemInfo() {
	global $uid, $mysql, $envi;
	if (!$envi->params['card_switch']) {
		return ['exchange_item_info' => [], 'exchange_point_list' => []];
	}
	$history = [];
	$log = $mysql->query('select * from exchange_log where user_id=?', [$uid])->fetchAll();
	if ($log) {
		foreach ($log as $v) {
			$history[$v['exchange_item_id']] = $v['got_item_count'];
		}
	}
	$ret['exchange_item_info'] = array_map(function ($e) use ($history) {
		$item = [
			"exchange_item_id"=>$e['exchange_item_id'],
			"amount"=>$e['item'][1],
			"option"=>null,
			"title"=>$e['title'],
			"cost_list"=>$e['cost_list'],
			"is_rank_max"=>$e['rank_max_flag'],
			"got_item_count"=>0,
			"term_count"=>0
		];
		switch($e['item'][0]) {
			case 'ticket':   $item['item_id']=1;$item['item_category_id']=1;$item['add_type']=1000;break;
			case 'social':   $item['item_id']=2;$item['item_category_id']=2;$item['add_type']=3002;break;
			case 'coin':     $item['item_id']=3;$item['item_category_id']=3;$item['add_type']=3000;break;
			case 'loveca':   $item['item_id']=4;$item['item_category_id']=4;$item['add_type']=3001;break;
			case 's_ticket': $item['item_id']=5;$item['item_category_id']=5;$item['add_type']=1000;break;
			case 'item':     $item['item_id']=$e['item'][2];$item['item_category_id']=$e['item'][2];$item['add_type']=1000;break;
			default: $item['item_id']=$e['item'][0];$item['item_category_id']=0;$item['add_type']=1001;break;
		}
		if (isset($history[$e['exchange_item_id']])) {
			$item['got_item_count'] = (int)$history[$e['exchange_item_id']];
		}
		if ($e['end']) {
			$item['term_end_date'] = $e['end'];
		}
		return $item;
	}, $exchange);
	$ret['exchange_point_list'] = addExchangePoint(false);
	return $ret;
}

function exchange_usePoint($post) {
	global $uid, $mysql, $envi;
	if (!$envi->params['card_switch']) {
		return retError(4204);
	}
	$exchangeInfo = false;
	foreach($exchange as $v) {
		if ($v['exchange_item_id'] == $post['exchange_item_id']) {
			$exchangeInfo = $v;
			break;
		}
	}
	if (!$exchangeInfo) {
		return retError(4204);
	}
	if ($exchangeInfo['end'] && strtotime($exchangeInfo['end']) < time()) {
		return retError(4203);
	}
	$cnt = $mysql->query('select got_item_count from exchange_log where user_id=? and exchange_item_id=?', [$uid, $exchangeInfo['exchange_item_id']])->fetchColumn();
	if (!$cnt) {
		$cnt = 0;
	} else {
		$cnt = (int)$cnt;
	}
	if ($v['max_item_count'] > 0 && $cnt >= $v['max_item_count']) {
		return retError(4201);
	}

	foreach($exchangeInfo['cost_list'] as $exInfo){
		if($exInfo['rarity']==$post['rarity']){
			$exchangeInfo['rarity']=$exInfo['rarity'];
			$exchangeInfo['cost_value']=$exInfo['cost_value'];
		}
	}
	if (!isset($exchangeInfo['rarity'])) {
		return retError(4204);
	}
	$amount=$post['amount'];
	

	$new_seal = -1;
	$cost_list = [2=>'seal1', 3=>'seal2', 4=>'seal4', 5=>'seal3'];
	$new_seal = (int)$envi->params[$cost_list[$exchangeInfo['rarity']]] - $exchangeInfo['cost_value'] * $amount;
	if ($new_seal < 0) {
		return retError(4202);
	}
	$envi->params[$cost_list[$exchangeInfo['rarity']]] = $new_seal;
	$mysql->query('insert into exchange_log values(?,?,1) on duplicate key update got_item_count=got_item_count+1',[$uid, $exchangeInfo['exchange_item_id']]);
	$ret['before_user_info'] = runAction('user', 'userInfo')['user'];
	if (is_numeric($exchangeInfo['item'][0])) {
		$mysql->query("INSERT INTO incentive_list (user_id,incentive_item_id,amount,is_card,incentive_message) VALUES(".$uid.",".$exchangeInfo['item'][0].",".$exchangeInfo['item'][1]*$amount.",1,\"貼紙商店兌換\")");
		$ret['exchange_reward'] = [];
		$ret['exchange_reward'][] = [
			'add_type' => 1001,
			'item_category_id' => 0,
			'amount' => 1,
			'reward_box_flag' => true,
			'new_unit_flag' => true
		];
	}else{
		$cnt = $exchangeInfo['item'][1]*$amount;
		switch($exchangeInfo['item'][0]) {
			case 'ticket':   $envi->params['item1'] += $cnt; $ret['exchange_reward'] = ['add_type'=>1000,'item_id'=>1,'item_category_id'=>1];break;
			case 'social':   $envi->params['social_point'] += $cnt; $ret['exchange_reward'] = ['add_type'=>3002,'item_id'=>2,'item_category_id'=>2];break;
			case 'coin':     $envi->params['coin'] += $cnt; $ret['exchange_reward'] = ['add_type'=>3000,'item_id'=>3,'item_category_id'=>3];break;
			case 'loveca':   $envi->params['loveca'] += $cnt; $ret['exchange_reward'] = ['add_type'=>3001,'item_id'=>4,'item_category_id'=>4];break;
			case 's_ticket': $envi->params['item5'] += $cnt; $ret['exchange_reward'] = ['add_type'=>1000,'item_id'=>5,'item_category_id'=>5];break;
			case 'item':     $envi->params['item'.$exchangeInfo['item'][2]] += $cnt; $ret['exchange_reward'] = ['add_type'=>1000,'item_id'=>$exchangeInfo['item'][2],'item_category_id'=>$exchangeInfo['item'][2]];break;
			default: trigger_error('exchange: 无法识别的物品种类：'.$exchangeInfo['item'][0]);break;
		}
		$ret['exchange_reward'] = array_merge($ret['exchange_reward'], ['reward_box_flag'=>false, 'amount'=>$exchangeInfo['item'][1]*$amount]);
	}
	$ret['after_user_info'] = runAction('user', 'userInfo')['user'];
	return $ret;
}

?>
