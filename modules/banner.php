<?php
//banner.php 首页显示的banner相关module

//banner/bannerList 获取banner列表 不返回的话不影响使用，但教程会卡在第8步
function banner_bannerList() {
	global $envi, $config;
	$ret = [
		"time_limit" => Date('Y-m-d')." 23:59:59",
		"member_category_list" => [[
			"member_category" => 1,
			"banner_list" => []
		], [
			"member_category"=> 2,
			"banner_list" => []
		]]
	];

	$genEventDetail = function ($event) use (&$ret){
		if(strtotime($event['start_date']) < time() && strtotime($event['end_date']) > time()){
			$ret['member_category_list'][0]['banner_list'][] = [
				"banner_type"            => 0,
				"target_id"              => $event['event_id'],
				"asset_path"             => $event['asset_path'],
				"asset_path_se"          => $event['asset_path_se'],
				"master_is_active_event" => true
			];
		}
	};

	$genEventDetail($config->event['marathon']);
	$genEventDetail($config->event['battle']);
	$genEventDetail($config->event['festival']);
	$genEventDetail($config->event['challenge']);
	
	if($envi->params['card_switch'] == 1){
		$genEventDetail($config->event['duty']);
	}
	
	$ret['member_category_list'][1]['banner_list'] = $ret['member_category_list'][0]['banner_list'];
	return $ret;
}

?>