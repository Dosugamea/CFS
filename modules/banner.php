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

	//四种活动
	$genEventDetail($config->event['marathon']);
	$genEventDetail($config->event['battle']);
	$genEventDetail($config->event['festival']);
	$genEventDetail($config->event['challenge']);
	
	//协力在开卡才出现
	if($envi->params['card_switch'] == 1){
		$genEventDetail($config->event['duty']);
	}
	
	//约战
	if(($config->m_duel['muse']['is_open'] || $config->m_duel['aqours']['is_open']) && 
	(strtotime($config->m_duel['start_date']) < time() && strtotime($config->m_duel['end_date']) > time())){
		$ret['member_category_list'][0]['banner_list'][] = [
			//banner type：0=EVENT 1=SECRETBOX 2=WEBVIEW 3=ONLINE 4=AREA 5=STORY 6=SNS 7=DUEL
			"banner_type"            => 7,
			"target_id"              => $config->m_duel['muse']['is_open'] ? 1 : 2,
			"asset_path"             => "",
			"asset_path_se"          => "",
			"master_is_active_event" => true
		];
	}
	

	//水团页面跟缪一样
	$ret['member_category_list'][1]['banner_list'] = $ret['member_category_list'][0]['banner_list'];
	return $ret;
}

?>