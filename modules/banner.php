<?php
//banner.php 首页显示的banner相关module

//banner/bannerList 获取banner列表 不返回的话不影响使用，但教程会卡在第8步
function banner_bannerList() {
	global $params;
	$ret = json_decode('{
		"time_limit": "'.Date('Y-m-d').' 23:59:59",
		"member_category_list": [{
			"member_category": 1,
			"banner_list": []
		}, {
			"member_category": 2,
			"banner_list": []
		}]
	}', true);
	include("config/event.php");
	if(strtotime($marathon['start_date']) < time() && strtotime($marathon['end_date']) > time()){
		$ret['member_category_list'][0]['banner_list'][] = [
			"banner_type"            => 0,
			"target_id"              => $marathon['event_id'],
			"asset_path"             => $marathon['asset_path'],
			"asset_path_se"          => $marathon['asset_path_se'],
			"master_is_active_event" => true
		];
	}
	if(strtotime($battle['start_date']) < time() && strtotime($battle['end_date']) > time()){
		$ret['member_category_list'][0]['banner_list'][] = [
			"banner_type"            => 0,
			"target_id"              => $battle['event_id'],
			"asset_path"             => $battle['asset_path'],
			"asset_path_se"          => $battle['asset_path_se'],
			"master_is_active_event" => true
		];
	}
	if(strtotime($festival['start_date']) < time() && strtotime($festival['end_date']) > time()){
		$ret['member_category_list'][0]['banner_list'][] = [
			"banner_type"            => 0,
			"target_id"              => $festival['event_id'],
			"asset_path"             => $festival['asset_path'],
			"asset_path_se"          => $festival['asset_path_se'],
			"master_is_active_event" => true
		];
	}
	if($params['card_switch']==1 && 
		strtotime($duty['start_date']) < time() && strtotime($duty['end_date']) > time()){
		$ret['member_category_list'][0]['banner_list'][] = [
			"banner_type"            => 0,
			"target_id"              => $duty['event_id'],
			"asset_path"             => $duty['asset_path'],
			"asset_path_se"          => $duty['asset_path_se'],
			"master_is_active_event" => true
		];
	}
	
	$ret['member_category_list'][1]['banner_list'] = $ret['member_category_list'][0]['banner_list'];
	return $ret;
}

?>