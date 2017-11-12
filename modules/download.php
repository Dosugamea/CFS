<?php
//download.php 下载module

//download/additional 下载附加内容
function download_additional($post) {
	global $uid, $mysql, $additional_for_android, $additional_for_ios;
	include '../config/modules_download.php';
	/*
	pl_assert(($_SERVER['HTTP_OS'] == 'Android' && $additional_for_android) || ($_SERVER['HTTP_OS'] == 'iOS' && $additional_for_ios), '暂不提供您区系统的客户端的数据包下载！');
	$ret = $mysql->query(
		"SELECT update_id as download_additional_id, url, size FROM packages
		WHERE package_id=? AND package_type=? AND os=?"
	, [$post['package_id'], $post['package_type'], $post['os']])->fetchAll(PDO::FETCH_ASSOC);
	pl_assert($ret, "找不到下载：ID：{$post['package_id']} 种类：{$post['package_type']} 系统：{$post['os']}");
	return $ret;
	*/
	include '../config/maintenance.php';
	
	$post['timeStamp'] = time();
	$post['commandNum'] = $login_key.".".time()."."."3";
	
	$poster = new poster();
	$r = $poster->post($post, "/download/additional");
	$ret = $r[1]['response_data'];
	$download_site = $mysql->query('SELECT download_site FROM users WHERE user_id='.$uid)->fetch()[0];
	if($download_site == "1"){
		$ret = json_encode($ret);
		$ret = (str_replace('dnw5grz2619mn.cloudfront.net', $reverse_proxy,$ret));
		$ret = json_decode($ret);
	}
	return removeQueryStrings($ret);
}

function download_batch($post) {
	global $uid, $mysql, $additional_for_android, $additional_for_ios;
	include '../config/modules_download.php';
	//虹原的旧代码先注释掉啦=w=
	/*pl_assert(($_SERVER['HTTP_OS'] == 'Android' && $additional_for_android) || ($_SERVER['HTTP_OS'] == 'iOS' && $additional_for_ios), '暂不提供您区系统的客户端的数据包下载！');
	$ret = $mysql->query(
		"SELECT package_id, url, size FROM packages
		WHERE package_type=? AND os=?"
	, [$post['package_type'], $post['os']])->fetchAll(PDO::FETCH_ASSOC);
	$ret = array_merge(array_filter($ret, function ($e) use ($post) {
		return array_search($e['package_id'], $post['excluded_package_ids']) === false;
	}));
	return $ret;*/
	include '../config/maintenance.php';
	
	$post['timeStamp'] = time();
	$post['commandNum'] = $login_key.".".time()."."."3";
	
	$poster = new poster();
	$r = $poster->post($post, "/download/batch");
	if(in_array("Maintenance: 1", $r[0])){
		return [];
	}
	else{
		$ret = $r[1]['response_data'];
		$download_site = $mysql->query('SELECT download_site FROM users WHERE user_id='.$uid)->fetch()[0];
		if($download_site == "1"){
			$ret = json_encode($ret);
			$ret = (str_replace('dnw5grz2619mn.cloudfront.net', $reverse_proxy,$ret));
			$ret = json_decode($ret, true);
		}
		return removeQueryStrings($ret);
	}
}

function download_event($post) {
	global $uid, $mysql, $additional_for_android, $additional_for_ios;
	include '../config/modules_download.php';
	include '../config/maintenance.php';
	
	$post['timeStamp'] = time();
	$post['commandNum'] = $login_key.".".time()."."."4";
	
	$poster = new poster();
	$r = $poster->post($post, "/download/event");
	
	if(in_array("Maintenance: 1", $r[0])){
		return [];
	}
	else{
		$ret = $r[1]['response_data'];
		$download_site = $mysql->query('SELECT download_site FROM users WHERE user_id='.$uid)->fetch()[0];
		if($download_site == "1"){
			$ret = json_encode($ret);
			$ret = (str_replace('dnw5grz2619mn.cloudfront.net', $reverse_proxy,$ret));
			$ret = json_decode($ret);
		}
		return removeQueryStrings($ret);
	}
}

function download_getUrl($post) {
	global $getUrl_address;
	return ['url_list' => array_map(function ($e) use ($getUrl_address) {
		return $getUrl_address . $e;
	}, $post['path_list'])];
}

//download/update 下载更新
function download_update($post) {
	global $uid, $mysql;
	include '../config/modules_download.php';
	
	$post['timeStamp'] = time();
	$post['commandNum'] = $login_key.".".time()."."."3";
	
	$poster = new poster();
	$r = $poster->post($post, "/download/update");
	$ret = $r[1]['response_data'];
	$download_site = $mysql->query('SELECT download_site FROM users WHERE user_id='.$uid)->fetch()[0];
	if($download_site == "1"){
		$ret = json_encode($ret);
		$ret = (str_replace('dnw5grz2619mn.cloudfront.net', $reverse_proxy, $ret));
		$ret = json_decode($ret);
	}
	$extend = $mysql->query('
		SELECT extend_download.* FROM extend_download_queue
		LEFT JOIN extend_download
		ON extend_download.ID=extend_download_queue.download_id
		WHERE downloaded_version < version OR downloaded_version=0
		AND extend_download_queue.user_id='.$uid
	)->fetchAll(PDO::FETCH_ASSOC);
	foreach($extend as $i){
		$i['size'] = (int)$i['size'];
		$ret[] = $i;
	}
	$mysql->query("DELETE FROM extend_download_queue WHERE user_id = ".$uid);
	return removeQueryStrings($ret);
}

function removeQueryStrings($ret){
	global $uid, $mysql;
	include '../config/modules_download.php';
	
	$download_site = (int)$mysql->query('SELECT download_site FROM users WHERE user_id='.$uid)->fetch()[0];
	$package_path = $_SERVER['HTTP_OS'] == 'iOS' ? $check_package_ios : $check_package_an;
	if($reverse_proxy && $download_site == 1){
		foreach($ret as &$i){
			$url = explode("?", $i['url'])[0];
			$fhash = substr($url, -43, 32);
			$fullfname = $package_path.$fhash;
			if(is_file($fullname)){
				$i['url'] = $url;
			}
		}
	}
	
	return $ret;
}
?>
