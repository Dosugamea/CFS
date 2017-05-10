<?php
//download.php 下载module

//download/additional 下载附加内容
function download_additional($post) {
	global $uid, $mysql, $additional_for_android, $additional_for_ios;
	/*
	pl_assert(($_SERVER['HTTP_OS'] == 'Android' && $additional_for_android) || ($_SERVER['HTTP_OS'] == 'iOS' && $additional_for_ios), '暂不提供您区系统的客户端的数据包下载！');
	$ret = $mysql->query(
		"SELECT update_id as download_additional_id, url, size FROM packages
		WHERE package_id=? AND package_type=? AND os=?"
	, [$post['package_id'], $post['package_type'], $post['os']])->fetchAll(PDO::FETCH_ASSOC);
	pl_assert($ret, "找不到下载：ID：{$post['package_id']} 种类：{$post['package_type']} 系统：{$post['os']}");
	return $ret;
	*/
	include 'includes/post.php';
	include 'config/maintenance.php';
	$tokenanduid = login();
	$token = $tokenanduid['authorize_token'];
	$user_id = $tokenanduid['user_id'];
	$sessionKey = $tokenanduid['sessionKey'];
	$os = $post['os'];
	if($post['os'] == 'Android'){
		$PlatformType = 2;
	}else{
		$PlatformType = 1;
	}
	
	$time = time();
	$headers = array(
			'Accept: */*',
			'Accept-Encoding: deflate',
			'API-Model: straightforward',
			'Debug: 1',
			"Bundle-Version: $bundle_ver",
			"Client-Version: $server_ver",
			'OS-Version: Nexus 5 google hammerhead 4.4.4',
			"OS: $os",
			"Platform-Type: $PlatformType",
			'Application-ID: 626776655',
			'Time-Zone: JST',
			'Reigion: 392',
			"Authorize: consumerKey=lovelive_test&timeStamp=$time&version=1.1&token=$token&nonce=3",
			'Expect:'
		);
	include 'config/modules_download.php';
	$post['timeStamp'] = time();
	$post['commandNum'] = $login_key.".".time()."."."3";
	
	$XMC = hash_hmac('sha1', json_encode($post), $sessionKey);
	$headers[] = "X-Message-Code: $XMC";
	$headers[] = "User-ID: $user_id";
	
	$post_server = array(
		"request_data" => json_encode($post)
	);
	$r = curls("prod-jp.lovelive.ge.klabgames.net/main.php/download/additional",$headers,$post_server);
	$ret = $r[1]['response_data'];
	return $ret;
}

function download_batch($post) {
	global $uid, $mysql, $additional_for_android, $additional_for_ios;
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
	include 'includes/post.php';
	include 'config/maintenance.php';
	$tokenanduid = login();
	if($tokenanduid == "Maintenance"){
		return [];
	}
	$token = $tokenanduid['authorize_token'];
	$user_id = $tokenanduid['user_id'];
	$sessionKey = $tokenanduid['sessionKey'];
	$os = $post['os'];
	if($post['os'] == 'Android'){
		$PlatformType = 2;
	}else{
		$PlatformType = 1;
	}
	
	$time = time();
	$headers = array(
			'Accept: */*',
			'Accept-Encoding: deflate',
			'API-Model: straightforward',
			'Debug: 1',
			"Bundle-Version: $bundle_ver",
			"Client-Version: $server_ver",
			'OS-Version: Nexus 5 google hammerhead 4.4.4',
			"OS: $os",
			"Platform-Type: $PlatformType",
			'Application-ID: 626776655',
			'Time-Zone: JST',
			'Reigion: 392',
			"Authorize: consumerKey=lovelive_test&timeStamp=$time&version=1.1&token=$token&nonce=3",
			'Expect:'
		);
	include 'config/modules_download.php';
	$post['timeStamp'] = time();
	$post['commandNum'] = $login_key.".".time()."."."3";
	
	$XMC = hash_hmac('sha1', json_encode($post), $sessionKey);
	$headers[] = "X-Message-Code: $XMC";
	$headers[] = "User-ID: $user_id";
	
	$post_server = array(
		"request_data" => json_encode($post)
	);
	$r = curls("prod-jp.lovelive.ge.klabgames.net/main.php/download/batch",$headers,$post_server);
	if(in_array("Maintenance: 1", $r[0])){
		return [];
	}
	else{
		$ret = $r[1]['response_data'];
		return $ret;
	}
}

function download_event($post) {
	global $uid, $mysql, $additional_for_android, $additional_for_ios;
	include 'includes/post.php';
	include 'config/maintenance.php';
	$tokenanduid = login();
	if($tokenanduid == "Maintenance"){
		return [];
	}
	$token = $tokenanduid['authorize_token'];
	$user_id = $tokenanduid['user_id'];
	$sessionKey = $tokenanduid['sessionKey'];
	$os = $post['os'];
	if($post['os'] == 'Android'){
		$PlatformType = 2;
	}else{
		$PlatformType = 1;
	}
	
	$time = time();
	$headers = array(
			'Accept: */*',
			'Accept-Encoding: deflate',
			'API-Model: straightforward',
			'Debug: 1',
			"Bundle-Version: $bundle_ver",
			"Client-Version: $server_ver",
			'OS-Version: Nexus 5 google hammerhead 4.4.4',
			"OS: $os",
			"Platform-Type: $PlatformType",
			'Application-ID: 626776655',
			'Time-Zone: JST',
			'Reigion: 392',
			"Authorize: consumerKey=lovelive_test&timeStamp=$time&version=1.1&token=$token&nonce=3",
			'Expect:'
		);
	include 'config/modules_download.php';
	$post['timeStamp'] = time();
	$post['commandNum'] = $login_key.".".time()."."."3";
	
	$XMC = hash_hmac('sha1', json_encode($post), $sessionKey);
	$headers[] = "X-Message-Code: $XMC";
	$headers[] = "User-ID: $user_id";
	
	$post_server = array(
		"request_data" => json_encode($post)
	);
	$r = curls("prod-jp.lovelive.ge.klabgames.net/main.php/download/event",$headers,$post_server);
	if(in_array("Maintenance: 1", $r[0])){
		return [];
	}
	else{
		$ret = $r[1]['response_data'];
		return $ret;
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
	pl_assert($post);
	pl_assert(isset($post['install_version']), '请升级到4.0客户端！');
	if ($post['os'] != 'Android' && $post['os'] != 'iOS') {
	return [];
	}
	$os = $post['os'];
	if($post['os'] == 'Android'){
		$PlatformType = 2;
	}else{
		$PlatformType = 1;
	}
	include 'includes/post.php';
	include 'config/maintenance.php';
	$tokenanduid = login();
	$token = $tokenanduid['authorize_token'];
	$user_id = $tokenanduid['user_id'];
	$sessionKey = $tokenanduid['sessionKey'];
	$user_cli_ver = (((float)$server_ver - (float)$post['external_version']) > 1)?floor($server_ver):$post['external_version'];
	
	$time = time();
	$headers = array(
			'Accept: */*',
			'Accept-Encoding: deflate',
			'API-Model: straightforward',
			'Debug: 1',
			"Bundle-Version: $bundle_ver",
			"Client-Version: $user_cli_ver",
			'OS-Version: Nexus 5 google hammerhead 4.4.4',
			"OS: $os",
			"Platform-Type: $PlatformType",
			'Application-ID: 626776655',
			'Time-Zone: JST',
			'Reigion: 392',
			"Authorize: consumerKey=lovelive_test&timeStamp=$time&version=1.1&token=$token&nonce=3",
			'Expect:'
		);
	include 'config/modules_download.php';
	$post['timeStamp'] = time();
	$post['commandNum'] = $login_key.".".time()."."."3";
	
	require 'config/code.php';
	$XMC = hash_hmac('sha1', json_encode($post), $sessionKey);
	$headers[] = "X-Message-Code: $XMC";
	$headers[] = "User-ID: $user_id";
	
	$post_server = array(
		"request_data" => json_encode($post)
	);
	$r = curls("prod-jp.lovelive.ge.klabgames.net/main.php/download/update",$headers,$post_server);
	$ret = $r[1]['response_data'];
	return $ret;
}

?>
