<?php
//download.php 下载module

//download/additional 下载附加内容
function download_additional($post) {
	global $uid, $mysql, $additional_for_android, $additional_for_ios;
	include 'config/modules_download.php';
	/*
	pl_assert(($_SERVER['HTTP_OS'] == 'Android' && $additional_for_android) || ($_SERVER['HTTP_OS'] == 'iOS' && $additional_for_ios), '暂不提供您区系统的客户端的数据包下载！');
	$ret = $mysql->query(
		"SELECT update_id as download_additional_id, url, size FROM packages
		WHERE package_id=? AND package_type=? AND os=?"
	, [$post['package_id'], $post['package_type'], $post['os']])->fetchAll(PDO::FETCH_ASSOC);
	pl_assert($ret, "找不到下载：ID：{$post['package_id']} 种类：{$post['package_type']} 系统：{$post['os']}");
	return $ret;
	*/
	include_once 'includes/post.php';
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
			"Bundle-Version: $official_bundle_ver",
			"Client-Version: $official_client_ver",
			'OS-Version: Nexus 5 google hammerhead 4.4.4',
			"OS: $os",
			"Platform-Type: $PlatformType",
			'Application-ID: 626776655',
			'Time-Zone: JST',
			'Reigion: 392',
			"Authorize: consumerKey=lovelive_test&timeStamp=$time&version=1.1&token=$token&nonce=3",
			'Expect:'
		);
	
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
	$download_site = $mysql->query('SELECT download_site FROM users WHERE user_id='.$uid)->fetch()[0];
	if($download_site == "1"){
		$ret = json_encode($ret);
		$ret = (str_replace('dnw5grz2619mn.cloudfront.net', $reverse_proxy,$ret));
		$ret = json_decode($ret);
	}
	return $ret;
}

function download_batch($post) {
	global $uid, $mysql, $additional_for_android, $additional_for_ios;
	include 'config/modules_download.php';
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
	include_once 'includes/post.php';
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
			"Bundle-Version: $official_bundle_ver",
			"Client-Version: $official_client_ver",
			'OS-Version: Nexus 5 google hammerhead 4.4.4',
			"OS: $os",
			"Platform-Type: $PlatformType",
			'Application-ID: 626776655',
			'Time-Zone: JST',
			'Reigion: 392',
			"Authorize: consumerKey=lovelive_test&timeStamp=$time&version=1.1&token=$token&nonce=3",
			'Expect:'
		);
	
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
		$download_site = $mysql->query('SELECT download_site FROM users WHERE user_id='.$uid)->fetch()[0];
		if($download_site == "1"){
			$ret = json_encode($ret);
			$ret = (str_replace('dnw5grz2619mn.cloudfront.net', $reverse_proxy,$ret));
			$ret = json_decode($ret);
		}
		return $ret;
	}
}

function download_event($post) {
	global $uid, $mysql, $additional_for_android, $additional_for_ios;
	include 'config/modules_download.php';
	include_once 'includes/post.php';
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
			"Bundle-Version: $official_bundle_ver",
			"Client-Version: $official_client_ver",
			'OS-Version: Nexus 5 google hammerhead 4.4.4',
			"OS: $os",
			"Platform-Type: $PlatformType",
			'Application-ID: 626776655',
			'Time-Zone: JST',
			'Reigion: 392',
			"Authorize: consumerKey=lovelive_test&timeStamp=$time&version=1.1&token=$token&nonce=3",
			'Expect:'
		);
	
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
		$download_site = $mysql->query('SELECT download_site FROM users WHERE user_id='.$uid)->fetch()[0];
		if($download_site == "1"){
			$ret = json_encode($ret);
			$ret = (str_replace('dnw5grz2619mn.cloudfront.net', $reverse_proxy,$ret));
			$ret = json_decode($ret);
		}
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
	include 'config/modules_download.php';
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
	$post['install_version'] = $official_client_ver;
	include 'includes/post.php';
	include 'config/maintenance.php';
	$tokenanduid = login();
	$token = $tokenanduid['authorize_token'];
	$user_id = $tokenanduid['user_id'];
	$sessionKey = $tokenanduid['sessionKey'];
	$user_cli_ver = $official_client_ver > $post['external_version']?$official_client_ver:$post['external_version'];
	
	$time = time();
	$headers = array(
			'Accept: */*',
			'Accept-Encoding: deflate',
			'API-Model: straightforward',
			'Debug: 1',
			"Bundle-Version: $official_bundle_ver",
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
	$download_site = $mysql->query('SELECT download_site FROM users WHERE user_id='.$uid)->fetch()[0];
	if($download_site == "1"){
		$ret = json_encode($ret);
		$ret = (str_replace('dnw5grz2619mn.cloudfront.net', $reverse_proxy,$ret));
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
	return $ret;
}

?>
