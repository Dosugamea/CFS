<?php
function curls($url, $headers, $data_string) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}

function login(){
	$time = time();
	include 'config/maintenance.php';
	$headers = array(
			'Accept: */*',
			'Accept-Encoding: deflate',
			'API-Model: straightforward',
			'Debug: 1',
			"Bundle-Version: $bundle_ver",
			"Client-Version: $server_ver",
			'OS-Version: Nexus 6P google angler 7.0',
			'OS: Android',
			'Platform-Type: 2',
			'Application-ID: 626776655',
			'Time-Zone: JST',
			'Reigion: 392',
			'Authorize: consumerKey=lovelive_test&timeStamp=$time&version=1.1&nonce=1',
			'Expect:'
		);
	$r = curls("prod-jp.lovelive.ge.klabgames.net/main.php/login/authkey",$headers,"");
	$r = json_decode($r);
	$token = $r->response_data->authorize_token;
	
	$time = time();
	$headers[12] = "Authorize: consumerKey=lovelive_test&timeStamp=$time&version=1.1&token=$token&nonce=2";
	
	include 'config/modules_download.php';
	//这里的body一定要是"request_data" => json的形式！
	$body = array(
		"request_data" => json_encode(array(
			"login_key" => $login_key,
			"login_passwd" => $login_passwd,
			"devtoken" => "APA91bFb7105jsH-vKhCv1XCxJaj3e2g9sEpSCLWtZvWniRhpv-DvHI_pvj7jYGwCGPbVejKfDwXsHXzez3loz95HJoam8xpQnfo-pCHZEAhiBW9IWzfV5-__k3BS5_lRx9zLqJ-LGKq"
	)));
	require 'config/code.php';
	$XMC = hash_hmac('sha1', $body['request_data'], $code);
	$headers[] = "X-Message-Code: $XMC";
	$r = curls("prod-jp.lovelive.ge.klabgames.net/main.php/login/login",$headers,$body);
	//$r = json_decode(gzdecode($r));
	return json_decode($r,true)['response_data'];
}

?>
