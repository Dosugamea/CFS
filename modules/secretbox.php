<?php
function secretBox_all() {
	global $uid, $mysql, $params;
	$ret = [];
	$ret['use_cache'] = 1;
	$ret['is_unit_max'] = false; //检测社员是否满了，CFS默认为false
	$ret['item_list'] = runAction('user', 'showAllItem')['items']; //物品列表，目前从user/showAllItem读取
	$gauge_point = $mysql->query("SELECT gauge FROM secretbox WHERE user_id = ".$uid)->fetchColumn();
	$ret['gauge_info'] = ['max_gauge_point' => 100, 'gauge_point' => (int)$gauge_point];
	$ret['member_category_list'] = [];
	
	//连接svonly数据库
	$secretboxdb = getSecretBoxDb();
	
	/*缪斯页面*/
	//选出符合时间的tab
	$tab_schedule = $secretboxdb->query("SELECT * FROM secret_box_tab_schedule_m")->fetchAll(PDO::FETCH_ASSOC);
	$tab = [];
	foreach($tab_schedule as $i){
		if(strtotime($i['start_date']) > time() || (!empty($i['end_date']) && strtotime($i['end_date']) < time()))
			continue;
		$tab[] = $secretboxdb->query("SELECT * FROM secret_box_tab_m WHERE secret_box_tab_id = ".$i['secret_box_tab_id'])->fetch(PDO::FETCH_ASSOC);
	}
	//挨个处理tab
	$tab_muse = [];
	foreach($tab as $i){
		//选出符合时间的page
		$page_schedule = $secretboxdb->query("SELECT * FROM secret_box_page_schedule_m WHERE secret_box_tab_id = ".$i['secret_box_tab_id'])->fetchAll(PDO::FETCH_ASSOC);
		$page = [];
		foreach($page_schedule as $j){
			if(strtotime($j['start_date']) > time() || (!empty($j['end_date']) && strtotime($j['end_date']) < time()))
				continue;
			$res = $secretboxdb->query("SELECT * FROM secret_box_page_m WHERE member_category = 1 AND secret_box_page_id = ".$j['secret_box_page_id'])->fetch(PDO::FETCH_ASSOC);
			if(!empty($res))
				$page[] = $res;
		}
		//挨个处理page
		$pages = [];
		foreach($page as $j){
			$secret_box_list = $secretboxdb->query("SELECT * FROM secret_box_m WHERE secret_box_page_id = ".$j['secret_box_page_id'])->fetchAll(PDO::FETCH_ASSOC);
			//挨个处理secretbox
			$secret_box = [];
			$skip_page = false;
			foreach($secret_box_list as $k){
				if(strtotime($k['start_date']) > time() || strtotime($k['end_date']) < time()){
					$skip_page = true;
					continue;
				}
				$secret_box_cost = $secretboxdb->query("SELECT * FROM secret_box_cost_m WHERE secret_box_id = ".$k['secret_box_id'])->fetchAll(PDO::FETCH_ASSOC);
				//挨个处理cost
				$all_cost = [];
				foreach($secret_box_cost as $l){
					$cost_detail = [];
					$cost_detail['priority'] = (int)$l['priority'];
					$l['cost_type'] = (int)$l['cost_type'];
					if($l['cost_type']==4 && !$params['card_switch'])
						continue;
					switch($l['cost_type']){
						case 4: //每日免费
							$cost_detail['type'] = 100;break;
						case 3: //友情
							$cost_detail['type'] = 3002;break;
						case 2: //道具
							$cost_detail['type'] = 1000;break;
						case 1: //心
							$cost_detail['type'] = 3001;break;
						default:
							trigger_error("cost_type: ".$cost_detail['type']." 无法识别的消耗类型");
					}
					$cost_detail['item_id'] = empty($l['item_id'])? Null : (int)$l['item_id'];
					$cost_detail['amount'] = (int)$l['amount'];
					$cost_detail['multi_type'] = (int)$l['multi_type'];
					switch($cost_detail['multi_type']){
						case 0: //只允许单抽
							$cost_detail['is_pay_cost'] = checkScoutAvaliable($cost_detail['type'], $cost_detail['item_id'], $cost_detail['amount'], 0);
							$cost_detail['within_single_limit'] = 1;
							break;
						case 1: //允许单抽和十一连
							$cost_detail['multi_amount'] = 10 * $cost_detail['amount'];
							$cost_detail['multi_count'] = (int)$k['multi_additional'] ? 11 : 10;
							$cost_detail['is_pay_cost'] = checkScoutAvaliable($cost_detail['type'], $cost_detail['item_id'], $cost_detail['amount']);
							$cost_detail['is_pay_multi_cost'] = checkScoutAvaliable($cost_detail['type'], $cost_detail['item_id'], $cost_detail['multi_amount']);
							$cost_detail['within_single_limit'] = 1;
							$cost_detail['within_multi_limit'] = 1;
							break;
						case 2: //只允许十一连
							$cost_detail['multi_amount'] = $cost_detail['amount'];
							$cost_detail['multi_count'] = (int)$k['multi_additional'] ? 11 : 10;
							$cost_detail['is_pay_cost'] = checkScoutAvaliable($cost_detail['type'], $cost_detail['item_id'], $cost_detail['amount']);
							$cost_detail['is_pay_multi_cost'] = checkScoutAvaliable($cost_detail['type'], $cost_detail['item_id'], $cost_detail['multi_amount']);
							$cost_detail['within_single_limit'] = 1;
							$cost_detail['within_multi_limit'] = 1;
							break;
						case 3:
							//Unknown
							break;
						default:
							trigger_error("cost_type: ".$cost_detail['multi_type']." 无法识别的十一连类型");
					}
					$all_cost []= $cost_detail;
				}
				
				$secret_box_detail = [];
				$secret_box_detail['secret_box_id'] = (int)$k['secret_box_id'];
				$secret_box_detail['name'] = $k['name'];
				$secret_box_detail['title_asset'] = $k['title_asset'];
				$secret_box_detail['description'] = $k['description'];
				$secret_box_detail['start_date'] = date("Y-m-d H:i:s", strtotime($k['start_date']));
				$secret_box_detail['end_date'] = date("Y-m-d H:i:s", strtotime($k['end_date']));
				$secret_box_detail['add_gauge'] = (int)$k['add_gauge'];
				$secret_box_detail['pon_count'] = 0; //这是抽卡总数，以单抽记，目前直接0
				$secret_box_detail['pon_upper_limit'] = (int)$k['upper_limit'];
				$secret_box_detail['display_type'] = (int)$k['display_type'];
				$secret_box_detail['all_cost'] = $all_cost;
				$secret_box_detail['step'] = Null;
				$secret_box_detail['end_step'] = Null;
				$secret_box_detail['show_step'] = Null;
				$secret_box_detail['term_count'] = Null;
				$secret_box_detail['step_up_bonus_asset_path'] = Null;
				$secret_box_detail['step_up_bonus_bonus_item_list'] = Null;
				$secret_box_detail['knapsack_select_unit_list'] = Null;
				$secret_box_detail['knapsack_selected_unit_list'] = Null;
				$secret_box_detail['is_knapsack_reset'] = Null;
				$secret_box_detail['is_knapsack_select'] = Null;
				$secret_box_detail['knapsack_rest_count'] = Null;
				
				$secret_box []= $secret_box_detail;
			}
			if($skip_page)
				continue;
			if(empty($secret_box))
				continue;
			$page_detail = [];
			$page_detail['secret_box_page_id'] = (int)$j['secret_box_page_id'];
			$page_detail['page_layout'] = (int)$j['page_layout'];
			$default_img_info = [];
			$default_img_info['banner_img_asset'] = $j['banner_img_asset'];
			$default_img_info['banner_se_img_asset'] = $j['banner_img_se_asset'];
			$default_img_info['img_asset'] = $j['img_asset'];
			$default_img_info['url'] = $j['url'];
			$page_detail['default_img_info'] = $default_img_info;
			$page_detail['limited_img_info'] = []; //TODO:限定banner
			$page_detail['effect_list'] = []; //TODO:卡池左侧限定up人物展示
			$page_detail['secret_box_list'] = $secret_box;
			
			$pages []= $page_detail;
		}
		$tab_detail = [];
		$tab_detail['secret_box_tab_id'] = (int)$i['secret_box_tab_id'];
		$tab_detail['title_img_asset'] = $i['title_img_asset'];
		$tab_detail['title_img_se_asset'] = $i['title_img_se_asset'];
		$tab_detail['page_list'] = $pages;
		if(empty($pages))
			continue;
		$tab_muse []= $tab_detail;
	}
	$ret['member_category_list'] []= ["member_category" => 1, "tab_list" => $tab_muse];
	
	
	/*水团页面（其实就是把缪斯界面拷了一份过来）*/
	$tab_schedule = $secretboxdb->query("SELECT * FROM secret_box_tab_schedule_m")->fetchAll(PDO::FETCH_ASSOC);
	$tab = [];
	foreach($tab_schedule as $i){
		if(strtotime($i['start_date']) > time() || (!empty($i['end_date']) && strtotime($i['end_date']) < time()))
			continue;
		$tab[] = $secretboxdb->query("SELECT * FROM secret_box_tab_m WHERE secret_box_tab_id = ".$i['secret_box_tab_id'])->fetch(PDO::FETCH_ASSOC);
	}
	//挨个处理tab
	$tab_aqours = [];
	foreach($tab as $i){
		//选出符合时间的page
		$page_schedule = $secretboxdb->query("SELECT * FROM secret_box_page_schedule_m WHERE secret_box_tab_id = ".$i['secret_box_tab_id'])->fetchAll(PDO::FETCH_ASSOC);
		$page = [];
		foreach($page_schedule as $j){
			if(strtotime($j['start_date']) > time() || (!empty($j['end_date']) && strtotime($j['end_date']) < time()))
				continue;
			$res = $secretboxdb->query("SELECT * FROM secret_box_page_m WHERE member_category = 2 AND secret_box_page_id = ".$j['secret_box_page_id'])->fetch(PDO::FETCH_ASSOC);
			if(!empty($res))
				$page[] = $res;
		}
		//挨个处理page
		$pages = [];
		foreach($page as $j){
			$secret_box_list = $secretboxdb->query("SELECT * FROM secret_box_m WHERE secret_box_page_id = ".$j['secret_box_page_id'])->fetchAll(PDO::FETCH_ASSOC);
			//挨个处理secretbox
			$secret_box = [];
			$skip_page = false;
			foreach($secret_box_list as $k){
				if(strtotime($k['start_date']) > time() || strtotime($k['end_date']) < time()){
					$skip_page = true;
					continue;
				}
				$secret_box_cost = $secretboxdb->query("SELECT * FROM secret_box_cost_m WHERE secret_box_id = ".$k['secret_box_id'])->fetchAll(PDO::FETCH_ASSOC);
				//挨个处理cost
				$all_cost = [];
				foreach($secret_box_cost as $l){
					$cost_detail = [];
					$cost_detail['priority'] = (int)$l['priority'];
					$cost_detail['type'] = (int)$l['cost_type'];
					if($cost_detail['type']==4 && !$params['card_switch'])
						continue;
					switch($cost_detail['type']){
						case 4: //每日免费
							$cost_detail['type'] = 100;break;
						case 3: //友情
							$cost_detail['type'] = 3002;break;
						case 2: //道具
							$cost_detail['type'] = 1000;break;
						case 1: //心
							$cost_detail['type'] = 3001;break;
						default:
							trigger_error("cost_type: ".$cost_detail['type']." 无法识别的消耗类型");
					}
					$cost_detail['item_id'] = empty($l['item_id'])? Null : (int)$l['item_id'];
					$cost_detail['amount'] = (int)$l['amount'];
					$cost_detail['multi_type'] = (int)$l['multi_type'];
					switch($cost_detail['multi_type']){
						case 0: //只允许单抽
							$cost_detail['is_pay_cost'] = checkScoutAvaliable($cost_detail['type'], $cost_detail['item_id'], $cost_detail['amount'], 1);
							$cost_detail['within_single_limit'] = 1;
							break;
						case 1: //允许单抽和十一连
							$cost_detail['multi_amount'] = 10 * $cost_detail['amount'];
							$cost_detail['multi_count'] = (int)$k['multi_additional'] ? 11 : 10;
							$cost_detail['is_pay_cost'] = checkScoutAvaliable($cost_detail['type'], $cost_detail['item_id'], $cost_detail['amount']);
							$cost_detail['is_pay_multi_cost'] = checkScoutAvaliable($cost_detail['type'], $cost_detail['item_id'], $cost_detail['multi_amount']);
							$cost_detail['within_single_limit'] = 1;
							$cost_detail['within_multi_limit'] = 1;
							break;
						case 2: //只允许十一连
							$cost_detail['multi_amount'] = $cost_detail['amount'];
							$cost_detail['multi_count'] = (int)$k['multi_additional'] ? 11 : 10;
							$cost_detail['is_pay_cost'] = checkScoutAvaliable($cost_detail['type'], $cost_detail['item_id'], $cost_detail['amount']);
							$cost_detail['is_pay_multi_cost'] = checkScoutAvaliable($cost_detail['type'], $cost_detail['item_id'], $cost_detail['multi_amount']);
							$cost_detail['within_single_limit'] = 1;
							$cost_detail['within_multi_limit'] = 1;
							break;
						case 3:
							//Unknown
							break;
						default:
							trigger_error("cost_type: ".$cost_detail['multi_type']." 无法识别的十一连类型");
					}
					$all_cost []= $cost_detail;
				}
				
				$secret_box_detail = [];
				$secret_box_detail['secret_box_id'] = (int)$k['secret_box_id'];
				$secret_box_detail['name'] = $k['name'];
				$secret_box_detail['title_asset'] = $k['title_asset'];
				$secret_box_detail['description'] = $k['description'];
				$secret_box_detail['start_date'] = date("Y-m-d H:i:s", strtotime($k['start_date']));
				$secret_box_detail['end_date'] = date("Y-m-d H:i:s", strtotime($k['end_date']));
				$secret_box_detail['add_gauge'] = (int)$k['add_gauge'];
				$secret_box_detail['pon_count'] = 0; //这是抽卡总数，以单抽记，目前直接0
				$secret_box_detail['pon_upper_limit'] = (int)$k['upper_limit'];
				$secret_box_detail['display_type'] = (int)$k['display_type'];
				$secret_box_detail['all_cost'] = $all_cost;
				$secret_box_detail['step'] = Null;
				$secret_box_detail['end_step'] = Null;
				$secret_box_detail['show_step'] = Null;
				$secret_box_detail['term_count'] = Null;
				$secret_box_detail['step_up_bonus_asset_path'] = Null;
				$secret_box_detail['step_up_bonus_bonus_item_list'] = Null;
				$secret_box_detail['knapsack_select_unit_list'] = Null;
				$secret_box_detail['knapsack_selected_unit_list'] = Null;
				$secret_box_detail['is_knapsack_reset'] = Null;
				$secret_box_detail['is_knapsack_select'] = Null;
				$secret_box_detail['knapsack_rest_count'] = Null;
				
				$secret_box []= $secret_box_detail;
			}
			if($skip_page)
				continue;
			if(empty($secret_box))
				continue;
			$page_detail = [];
			$page_detail['secret_box_page_id'] = (int)$j['secret_box_page_id'];
			$page_detail['page_layout'] = (int)$j['page_layout'];
			$default_img_info = [];
			$default_img_info['banner_img_asset'] = $j['banner_img_asset'];
			$default_img_info['banner_se_img_asset'] = $j['banner_img_se_asset'];
			$default_img_info['img_asset'] = $j['img_asset'];
			$default_img_info['url'] = $j['url'];
			$page_detail['default_img_info'] = $default_img_info;
			$page_detail['limited_img_info'] = []; //TODO:限定banner
			$page_detail['effect_list'] = []; //TODO:卡池左侧限定up人物展示
			$page_detail['secret_box_list'] = $secret_box;
			
			$pages []= $page_detail;
		}
		$tab_detail = [];
		$tab_detail['secret_box_tab_id'] = (int)$i['secret_box_tab_id'];
		$tab_detail['title_img_asset'] = $i['title_img_asset'];
		$tab_detail['title_img_se_asset'] = $i['title_img_se_asset'];
		$tab_detail['page_list'] = $pages;
		if(empty($pages))
			continue;
		$tab_aqours []= $tab_detail;
	}
	$ret['member_category_list'] []= ["member_category" => 2, "tab_list" => $tab_aqours];
	
	if(!$params['card_switch']){
		foreach($ret['member_category_list'] as &$i){
			foreach($i['tab_list'] as &$j){
				foreach($j['page_list'] as &$k){
					foreach($k['secret_box_list'] as &$l){
						foreach($l['all_cost'] as &$m){
							$m['amount'] = 0;
							if(isset($m['multi_amount'])) $m['multi_amount'] = 0;
							if(isset($m['is_pay_cost'])) $m['is_pay_cost'] = true;
							if(isset($m['is_pay_multi_cost'])) $m['is_pay_multi_cost'] = true;
						}
					}
				}
			}
		}
	}
	return $ret;
}

function checkScoutAvaliable($type, $item_id, $amount, $member=0){
	global $uid, $params, $mysql;
	switch($type){
		case 100:
			if($member == 0)
				$free_gacha = $mysql->query("SELECT free_gacha_muse FROM secretbox WHERE user_id = ?", [$uid])->fetchColumn();
			else
				$free_gacha = $mysql->query("SELECT free_gacha_aqours FROM secretbox WHERE user_id = ?", [$uid])->fetchColumn();
			if(!$free_gacha)
				$mysql->query("INSERT INTO secretbox (user_id) VALUES (?)", [$uid]);
			$ret = date("Y-m-d", strtotime($free_gacha)) != date("Y-m-d", time());
			break;
		case 3002:
			$ret = $params['item2'] >= $amount;break;
		case 1000:
			$ret = $params['item'.$item_id] >= $amount;break;
		case 3001:
			$ret = $params['item4'] >= $amount;break;
		default:
			$ret = false;
	}
	return $ret;
}

function secretbox_pon($post) {
	//检查合法性
	if(!isset($post['cost_priority']) || !isset($post['secret_box_id'])){
		$ret = retError(1501); // ERROR_CODE_SECRET_BOX_COST_TYPE_IS_NOT_SPECIFIED
		return $ret;
	}
	if(!is_numeric($post['secret_box_id']) || !is_numeric($post['cost_priority']))
		throw403("INVALID_DATA");
	
	global $uid, $mysql, $params;
	$secretboxdb = getSecretBoxDb();
	$secret_box_info = $secretboxdb->query("SELECT * FROM secret_box_m WHERE secret_box_id = ".$post['secret_box_id'])->fetch(PDO::FETCH_ASSOC);
	
	//检查招募箱是否存在
	if(empty($secret_box_info)){
		$ret = retError(1500); //ERROR_CODE_SECRET_BOX_NOT_EXIST
		return $ret;
	}
	if(strtotime($secret_box_info['start_date']) > time() || strtotime($secret_box_info['end_date']) < time()){
		$ret = retError(1508); //ERROR_CODE_SECRET_BOX_OUT_OF_DATE
		return $ret;
	}
	
	//检查cost是否合法
	$all_cost_ = $secretboxdb->query("SELECT * FROM secret_box_cost_m WHERE secret_box_id = ".$post['secret_box_id'])->fetchAll(PDO::FETCH_ASSOC);
	$all_cost = [];
	foreach($all_cost_ as $i){
		$cost_detail = [];
		$cost_detail['priority'] = (int)$i['priority'];
		$cost_detail['type'] = (int)$i['cost_type'];
		switch($cost_detail['type']){
			case 4: //每日免费
				$cost_detail['type'] = 100;break;
			case 3: //友情
				$cost_detail['type'] = 3002;break;
			case 2: //道具
				$cost_detail['type'] = 1000;break;
			case 1: //心
				$cost_detail['type'] = 3001;break;
			default:
				trigger_error("cost_type: ".$cost_detail['type']." 无法识别的消耗类型");
		}
		$cost_detail['item_id'] = empty($i['item_id'])? Null : (int)$i['item_id'];
		$cost_detail['amount'] = (int)$i['amount'];
		$cost_detail['multi_type'] = (int)$i['multi_type'];
		switch($cost_detail['multi_type']){
			case 0: //只允许单抽
				$cost_detail['is_pay_cost'] = checkScoutAvaliable($cost_detail['type'], $cost_detail['item_id'], 2 * $cost_detail['amount'], 1);
				$cost_detail['within_single_limit'] = 1;
				break;
			case 1: //允许单抽和十一连
				$cost_detail['multi_amount'] = 10 * $cost_detail['amount'];
				$cost_detail['multi_count'] = (int)$secret_box_info['multi_additional'] ? 11 : 10;
				$cost_detail['is_pay_cost'] = checkScoutAvaliable($cost_detail['type'], $cost_detail['item_id'], $cost_detail['amount']);
				$cost_detail['is_pay_multi_cost'] = checkScoutAvaliable($cost_detail['type'], $cost_detail['item_id'], 2 * $cost_detail['multi_amount']);
				$cost_detail['within_single_limit'] = 1;
				$cost_detail['within_multi_limit'] = 1;
				break;
			case 2: //只允许十一连
				$cost_detail['multi_amount'] = $cost_detail['amount'];
				$cost_detail['multi_count'] = (int)$secret_box_info['multi_additional'] ? 11 : 10;
				$cost_detail['is_pay_cost'] = checkScoutAvaliable($cost_detail['type'], $cost_detail['item_id'], $cost_detail['amount']);
				$cost_detail['is_pay_multi_cost'] = checkScoutAvaliable($cost_detail['type'], $cost_detail['item_id'], 2 * $cost_detail['multi_amount']);
				$cost_detail['within_single_limit'] = 1;
				$cost_detail['within_multi_limit'] = 1;
				break;
			case 3:
				//Unknown
				break;
			default:
				trigger_error("cost_type: ".$cost_detail['multi_type']." 无法识别的十一连类型");
		}
		$all_cost []= $cost_detail;
		if((int)$i['priority'] == $post['cost_priority']){
			$now_cost['cost_type'] = (int)$i['cost_type'];
			$now_cost['item_id'] = (int)$i['item_id'];
			$now_cost['amount'] = (int)$i['amount'];
			$now_cost['multi_type'] = (int)$i['multi_type'];
		}
	}
	
	if(!$params['card_switch']){
		foreach($all_cost as &$i){
			$i['amount'] = 0;
			if(isset($i['multi_amount'])) $i['multi_amount'] = 0;
			if(isset($i['is_pay_cost'])) $i['is_pay_cost'] = true;
			if(isset($i['is_pay_multi_cost'])) $i['is_pay_multi_cost'] = true;
		}
	}
	if(!isset($now_cost)){
		$ret = retError(1502); //ERROR_CODE_SECRET_BOX_INVALID_COST_TYPE
		return $ret;
	}
	switch($now_cost['multi_type']){ //检测multi的合法性并给cost乘上正确的数值
		case 0: //只允许单抽
			if($post['action'] == "multi"){
				$ret = retError(1503);
				return $ret;
			}
			break;
		case 1: //允许单抽和十一连
			if($post['action'] == "multi")
				$now_cost['amount'] *= 10;
			break;
		case 2: //只允许十一连
			if($post['action'] == "pon")
				trigger_error("该cost不允许单抽！");
			break;
		case 3:
			//Unknown
			break;
		default:
			trigger_error("cost_type: ".$now_cost['multi_type']." 无法识别的十一连类型");
	}
	
	switch($now_cost['cost_type']){ //将cost_type转换为新版
		case 4:
			$now_cost['cost_type'] = 100;break;
		case 3:
			$now_cost['cost_type'] = 3002;break;
		case 2:
			$now_cost['cost_type'] = 1000;break;
		case 1:
			$now_cost['cost_type'] = 3001;break;
		default:
			trigger_error("未知的cost_type: ".$now_cost['cost_type']);
	}
	
	
	//查询个人信息
	$before_user_info = runAction("user", "userInfo")['user'];
	
	if($params['card_switch']){
		//检测道具数是否够
		$member_category = (int)$secretboxdb->query("SELECT member_category FROM secret_box_page_m WHERE secret_box_page_id = ".$secret_box_info['secret_box_page_id'])->fetchColumn();
		$member_category -= 1;
		if(!checkScoutAvaliable($now_cost['cost_type'], $now_cost['item_id'], $now_cost['amount'], $member_category)){
			$ret = retError(1507);
			return $ret;
		}
		
		//扣道具
		switch($now_cost['cost_type']){
			case 100:
				if($member_category == 0)
					$mysql->query("UPDATE secretbox SET free_gacha_muse = NOW() WHERE user_id = ?", [$uid]);
				else
					$mysql->query("UPDATE secretbox SET free_gacha_aqours = NOW() WHERE user_id = ?", [$uid]);
				break;
			case 3002:
				$params['item2'] -= $now_cost['amount'];break;
			case 1000:
				$params['item'.$now_cost['item_id']] -= $now_cost['amount'];break;
			case 3001:
				$params['item4'] -= $now_cost['amount'];break;
		}
		
		$ret = [];
		$ret['is_unit_max'] = false; //检测社员是否满了，CFS默认为false
		$ret['item_list'] = runAction('user', 'showAllItem')['items']; //物品列表
		
		/*处理优等生招募进度条*/
		$gauge_info = [];
		if($post['action'] == "multi"){
			if((int)$secret_box_info['multi_additional'] == 1)
				$added_gauge_point = (int)$secret_box_info['add_gauge'] * 11;
			else
				$added_gauge_point = (int)$secret_box_info['add_gauge'] * 10;
		}else{
			$added_gauge_point = (int)$secret_box_info['add_gauge'];
		}
		$gauge_info['max_gauge_point'] = 100;
		$gauge_info['gauge_point'] = (int)$mysql->query("SELECT gauge FROM secretbox WHERE user_id = ".$uid)->fetchColumn() + $added_gauge_point;
		$gauge_info['added_gauge_point'] = $added_gauge_point;
		$gauge_reward_info = $secretboxdb->query("SELECT * FROM secret_box_gauge_reward_m")->fetch(PDO::FETCH_ASSOC);
		if(empty($gauge_reward_info)){
			$ret = retError(1505); //ERROR_CODE_SECRET_BOX_GAUGE_INFORMATION_NOT_EXIST
			return $ret;
		}
		$get_items = [];
		while($gauge_info['gauge_point'] >= 100){
			$get_items []= ["owning_item_id" => 0, "item_id" => (int)$gauge_reward_info['item_id'], "add_type" => (int)$gauge_reward_info['add_type'], "amount" => (int)$gauge_reward_info['amount'], "item_category_id" => (int)$gauge_reward_info['item_category_id'], "reward_box_flag" => false];
			$params['item'.$gauge_reward_info['item_id']] += (int)$gauge_reward_info['amount'];
			$gauge_info['gauge_point'] -= 100;
		}
		$mysql->query("UPDATE secretbox SET gauge = ? WHERE user_id = ?", [$gauge_info['gauge_point'], $uid]);
		$ret['gauge_info'] = $gauge_info;
	}else{
		$ret = [];
		$ret['is_unit_max'] = false; //检测社员是否满了，CFS默认为false
		$ret['item_list'] = runAction('user', 'showAllItem')['items']; //物品列表
		$gauge_info['max_gauge_point'] = 100;
		$gauge_info['gauge_point'] = 0;
		$gauge_info['added_gauge_point'] = 0;
		$gauge_info['added_gauge_point'] = 0;
		$ret['gauge_info'] = $gauge_info;
		$get_items = [];
	}
	
	/*处理礼物箱相关信息*/
	$ret['secret_box_page_id'] = (int)$secret_box_info['secret_box_page_id'];
	$ret['secret_box_info']['secret_box_id'] = (int)$secret_box_info['secret_box_id'];
	$ret['secret_box_info']['name'] = $secret_box_info['name'];
	$ret['secret_box_info']['title_asset'] = $secret_box_info['title_asset'];
	$ret['secret_box_info']['description'] = $secret_box_info['description'];
	$ret['secret_box_info']['start_date'] = date("Y-m-d H:i:s", strtotime($secret_box_info['start_date']));
	$ret['secret_box_info']['end_date'] = date("Y-m-d H:i:s", strtotime($secret_box_info['end_date']));
	$ret['secret_box_info']['add_gauge'] = (int)$secret_box_info['add_gauge'];
	$ret['secret_box_info']['pon_count'] = 0;
	$ret['secret_box_info']['pon_upper_limit'] = (int)$secret_box_info['upper_limit'];
	$ret['secret_box_info']['display_type'] = (int)$secret_box_info['display_type'];
	$ret['secret_box_info']['all_cost'] = $all_cost;
	
	/*开始抽牌*/
	$got_units = [];
	$unit_count = $post['action'] == "multi" ? ((int)$secret_box_info['multi_additional'] ? 11 : 10) : 1;
	for($j = 0; $j < $unit_count; $j++){
		if($post['action'] == "multi" && $j == $unit_count - 1){//查看是否需要保底
			$fix_rarity = $secretboxdb->query("SELECT * FROM secret_box_fix_rarity_m WHERE secret_box_id = ".$secret_box_info['secret_box_id'])->fetch(PDO::FETCH_ASSOC);
			if(!empty($fix_rarity) && strtotime($fix_rarity['start_date']) < time() && strtotime($fix_rarity['end_date']) > time()){
				$fix_count = 0;
				foreach($got_units as $k){//查找低于保底下限的卡数目
					if($k['unit_rarity_id'] < (int)$fix_rarity['unit_group_id']){
						$fix_count++;
					}
				}
				if($fix_count == $unit_count - 1){
					$rarity = (int)$fix_rarity['unit_group_id'];
				}
			}else{ //超过保底期限
				//获取卡池中的社员以及权重
				$unit_group_ = $secretboxdb->query("SELECT * FROM secret_box_unit_group_m WHERE secret_box_id = ".$secret_box_info['secret_box_id'])->fetchAll(PDO::FETCH_ASSOC);
				$random_pick = function ($array) {
					$pick = mt_rand(1, array_sum($array));
					foreach ($array as $k => $v) if (($pick -= $v) <= 0) return $k;
				};
				$unit_group = [];
				foreach($unit_group_ as $i){
					$unit_group[$i['unit_group_id']] = (!empty($i['weight_extra']) && $params['card_switch']) ? (int)$i['weight_extra'] : (int)$i['weight'];
				}
				if(empty($unit_group))
					trigger_error("未配置稀有度对应权重！");
				//抽一张看看稀有度
				$rarity = (int)$random_pick($unit_group);
			}
		}else{
			//获取卡池中的社员以及权重
			$unit_group_ = $secretboxdb->query("SELECT * FROM secret_box_unit_group_m WHERE secret_box_id = ".$secret_box_info['secret_box_id'])->fetchAll(PDO::FETCH_ASSOC);
			$random_pick = function ($array) {
				$pick = mt_rand(1, array_sum($array));
				foreach ($array as $k => $v) if (($pick -= $v) <= 0) return $k;
			};
			$unit_group = [];
			foreach($unit_group_ as $i){
				$unit_group[$i['unit_group_id']] = (!empty($i['weight_extra']) && $params['card_switch']) ? (int)$i['weight_extra'] : (int)$i['weight'];
			}
			if(empty($unit_group))
				trigger_error("未配置稀有度对应权重！");
			//抽一张看看稀有度
			$rarity = (int)$random_pick($unit_group);
		}
		//取该稀有度的所有卡
		$unit_all_ = $secretboxdb->query("SELECT unit_id, weight FROM secret_box_unit_m WHERE secret_box_id = ".$secret_box_info['secret_box_id']." AND unit_group_id = ".$rarity)->fetchAll(PDO::FETCH_ASSOC);
		$unit_all = [];
		foreach($unit_all_ as $i){
			$unit_all[$i['unit_id']] = (int)$i['weight'];
		}
		if(empty($unit_all))
			trigger_error("稀有度".$rarity."没有配置社员！");
		$unit_id = (int)$random_pick($unit_all);
		$get_unit_detail = addUnit($unit_id)[0];
		$get_unit_detail['unit_rarity_id'] = $rarity;
		$get_unit_detail['add_type'] = 1001;
		$get_unit_detail['amount'] = 1;
		$get_unit_detail['item_category_id'] = 0;
		$get_unit_detail['new_unit_flag'] = false;
		$get_unit_detail['reward_box_flag'] = false;
		$got_units [] = $get_unit_detail;
	}
	
	if(!$params['card_switch']){
		rollback();
	}
	
	$ret['secret_box_items']['unit'] = $got_units;
	$ret['secret_box_items']['item'] = $get_items;
	$ret['before_user_info'] = $before_user_info;
	$ret['after_user_info'] = runAction('user', 'userInfo')['user'];
	$last_muse_free_gacha = date("Y-m-d", strtotime($mysql->query("SELECT free_gacha_muse FROM secretbox WHERE user_id = ?", [$uid])->fetchColumn()));
	$last_aqours_free_gacha = date("Y-m-d", strtotime($mysql->query("SELECT free_gacha_aqours FROM secretbox WHERE user_id = ?", [$uid])->fetchColumn()));
	$ret['next_free_muse_gacha_timestamp'] = $last_muse_free_gacha == date("Y-m-d", time()) ? (strtotime($last_muse_free_gacha) + 86400) : strtotime(date("Y-m-d", time()));
	$ret['next_free_aqours_gacha_timestamp'] = $last_aqours_free_gacha == date("Y-m-d", time()) ? (strtotime($last_muse_free_gacha) + 86400) : strtotime(date("Y-m-d", time()));
	
	return $ret;
}

function secretbox_multi($post) {
	return secretbox_pon($post);
}
?>
