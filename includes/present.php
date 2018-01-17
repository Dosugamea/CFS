<?php
//present.php，负责往礼物箱插礼物的接口
//add_present：增加礼物
function add_present($present, $amount, $message, $extra = false){
	//$present:礼物内容，int或者string，int时指的是特定卡片，string为下列item
	//$amount:数量
	//$message:礼物信息
	//$extra:额外信息。为item的时候通常指item_id，为卡片的时候指卡片信息，
	//  包括is_rank_max,exp,skill_exp,unit_removable_skill_capacity
	global $uid, $mysql;
	if(is_numeric($present)){
		if(!$extra){
			$extra = null;
		}
		$mysql->query("INSERT INTO incentive_list (user_id, incentive_item_id, amount, is_card, incentive_message, extra_info) VALUES(?,?,?,?,?,?)", [$uid, $present, $amount, 1, $message, $extra]);
		$ret = ["unit_id" => $present, "add_type" => 1001];
		if($extra){
			$ret = array_merge($ret, $extra);
		}else{
			$ret['is_rank_max'] = false;
		}
	}else{
		switch($present){
			case "loveca":
				$mysql->query("INSERT INTO incentive_list (user_id, incentive_item_id, amount, is_card, incentive_message) VALUES(?,?,?,?,?)", [$uid, 4, $amount, 0, $message]);
				$ret = ["item_id" => 4, "add_type" => 3001];break;
			case "coin":
				$mysql->query("INSERT INTO incentive_list (user_id, incentive_item_id, amount, is_card, incentive_message) VALUES(?,?,?,?,?)", [$uid, 3, $amount, 0, $message]);
				$ret = ["item_id" => 3, "add_type" => 3000];break;
			case "social":
				$mysql->query("INSERT INTO incentive_list (user_id, incentive_item_id, amount, is_card, incentive_message) VALUES(?,?,?,?,?)", [$uid, 2, $amount, 0, $message]);
				$ret = ["item_id" => 2, "add_type" => 3002];break;
			case "ticket":
				$mysql->query("INSERT INTO incentive_list (user_id, incentive_item_id, amount, is_card, incentive_message) VALUES(?,?,?,?,?)", [$uid, 1, $amount, 0, $message]);
				$ret = ["item_id" => 1, "add_type" => 1000];break;
			case "s_ticket":
				$mysql->query("INSERT INTO incentive_list (user_id, incentive_item_id, amount, is_card, incentive_message) VALUES(?,?,?,?,?)", [$uid, 5, $amount, 0, $message]);
				$ret = ["item_id" => 5, "add_type" => 1000];break;
			case "sr_ticket_1":
				$mysql->query("INSERT INTO incentive_list (user_id, incentive_item_id, amount, is_card, incentive_message) VALUES(?,?,?,?,?)", [$uid, 9, $amount, 0, $message]);
				$ret = ["item_id" => 9, "add_type" => 1000];break;
			case "sr_ticket_2":
				$mysql->query("INSERT INTO incentive_list (user_id, incentive_item_id, amount, is_card, incentive_message) VALUES(?,?,?,?,?)", [$uid, 10, $amount, 0, $message]);
				$ret = ["item_id" => 10, "add_type" => 1000];break;
			case "ssr_ticket_1":
				$mysql->query("INSERT INTO incentive_list (user_id, incentive_item_id, amount, is_card, incentive_message) VALUES(?,?,?,?,?)", [$uid, 11, $amount, 0, $message]);
				$ret = ["item_id" => 11, "add_type" => 1000];break;
			case "ssr_ticket_2":
				$mysql->query("INSERT INTO incentive_list (user_id, incentive_item_id, amount, is_card, incentive_message) VALUES(?,?,?,?,?)", [$uid, 12, $amount, 0, $message]);
				$ret = ["item_id" => 12, "add_type" => 1000];break;
			case "ur_ticket_1":
				$mysql->query("INSERT INTO incentive_list (user_id, incentive_item_id, amount, is_card, incentive_message) VALUES(?,?,?,?,?)", [$uid, 13, $amount, 0, $message]);
				$ret = ["item_id" => 13, "add_type" => 1000];break;
			case "ur_ticket_2":
				$mysql->query("INSERT INTO incentive_list (user_id, incentive_item_id, amount, is_card, incentive_message) VALUES(?,?,?,?,?)", [$uid, 14, $amount, 0, $message]);
				$ret = ["item_id" => 14, "add_type" => 1000];break;
			case "r_seal":
				$mysql->query("INSERT INTO incentive_list (user_id, incentive_item_id, item_id, amount, is_card, incentive_message) VALUES(?,?,?,?,?,?)", [$uid, 3006, 2, $amount, 0, $message]);
				$ret = ["item_id" => 2, "add_type" => 3006];break;
			case "sr_seal":
				$mysql->query("INSERT INTO incentive_list (user_id, incentive_item_id, item_id, amount, is_card, incentive_message) VALUES(?,?,?,?,?,?)", [$uid, 3006, 3, $amount, 0, $message]);
				$ret = ["item_id" => 3, "add_type" => 3006];break;
			case "ssr_seal":
				$mysql->query("INSERT INTO incentive_list (user_id, incentive_item_id, item_id, amount, is_card, incentive_message) VALUES(?,?,?,?,?,?)", [$uid, 3006, 5, $amount, 0, $message]);
				$ret = ["item_id" => 5, "add_type" => 3006];break;
			case "ur_seal":
				$mysql->query("INSERT INTO incentive_list (user_id, incentive_item_id, item_id, amount, is_card, incentive_message) VALUES(?,?,?,?,?,?)", [$uid, 3006, 4, $amount, 0, $message]);
				$ret = ["item_id" => 4, "add_type" => 3006];break;
			case "award":
				$mysql->query("INSERT INTO incentive_list (user_id, incentive_item_id, item_id, amount, is_card, incentive_message) VALUES(?,?,?,?,?,?)", [$uid, 5100, $extra, $amount, 0, $message]);
				$ret = ["item_id" => $extra, "add_type" => 5100];break;
			case "removable_skill":
				$mysql->query("INSERT INTO incentive_list (user_id, incentive_item_id, item_id, amount, is_card, incentive_message) VALUES(?,?,?,?,?,?)", [$uid, 5500, $extra, $amount, 0, $message]);
				$ret = ["item_id" => $extra, "add_type" => 5500];break;
			default:
				trigger_error("不支持插入物品：".$present);
		}
	}
	return $ret;
}

//get_present_info：获取某样物品的信息
function get_present_info($present, $extra = false){
	//$present:礼物内容，int或者string，int时指的是特定卡片，string为下列item
	//extra:额外信息。为item的时候通常指item_id，为卡片的时候指卡片信息，
	//  包括is_rank_max,exp,skill_exp,unit_removable_skill_capacity
	global $uid, $mysql;
	if(is_numeric($present)){
		if(!$extra){
			$extra = null;
		}
		$ret = ["unit_id" => $present, "add_type" => 1001];
		if($extra){
			$ret = array_merge($ret, $extra);
		}else{
			$ret['is_rank_max'] = false;
		}
	}else{
		switch($present){
			case "loveca":
				$ret = ["item_id" => 4, "add_type" => 3001];break;
			case "coin":
				$ret = ["item_id" => 3, "add_type" => 3000];break;
			case "social":
				$ret = ["item_id" => 2, "add_type" => 3002];break;
			case "ticket":
				$ret = ["item_id" => 1, "add_type" => 1000];break;
			case "s_ticket":
				$ret = ["item_id" => 5, "add_type" => 1000];break;
			case "sr_ticket_1":
				$ret = ["item_id" => 9, "add_type" => 1000];break;
			case "sr_ticket_2":
				$ret = ["item_id" => 10, "add_type" => 1000];break;
			case "ssr_ticket_1":
				$ret = ["item_id" => 11, "add_type" => 1000];break;
			case "ssr_ticket_2":
				$ret = ["item_id" => 12, "add_type" => 1000];break;
			case "ur_ticket_1":
				$ret = ["item_id" => 13, "add_type" => 1000];break;
			case "ur_ticket_2":
				$ret = ["item_id" => 14, "add_type" => 1000];break;
			case "r_seal":
				$ret = ["item_id" => 2, "add_type" => 3006];break;
			case "sr_seal":
				$ret = ["item_id" => 3, "add_type" => 3006];break;
			case "ssr_seal":
				$ret = ["item_id" => 5, "add_type" => 3006];break;
			case "ur_seal":
				$ret = ["item_id" => 4, "add_type" => 3006];break;
			case "award":
				$ret = ["item_id" => $extra, "add_type" => 5100];break;
			case "removable_skill":
				$ret = ["item_id" => $extra, "add_type" => 5500];break;
			default:
				trigger_error("不支持的物品：".$present);
		}
	}
	return $ret;
}